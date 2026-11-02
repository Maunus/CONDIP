/* CoNDiP static site. All data is loaded from data/*.json (built by tools/build_site_data.py). */
'use strict';

// ---------- Labels taken from the original condip.php ----------
const CLASSES = ['noun', 'verb', 'pronoun', 'numeral', 'rel. noun', 'adverb', 'adjective', 'see', 'a', 'all'];
const GRAMMAR = ['', 'toponym', 'proper name', 'ethnonym', 'zool.', 'bot.', 'min.', 'astr.', 'G8', 'obl. poss.',
  'intransitive', 'tr 1', 'tr 2', 'tr 3', 'tr 4', 'imp.agent', 'imp.ag. 1', 'imp.ag. 2', 'imp.ag. 3', 'reflexive'];
const PLURAL = ['', '+me´', '+tin', '+´', '=ke+´', 'R', 'R +me´', 'R +tin', 'R +´', 'R =ke+´', '*', '+me´ *',
  '+tin *', '+´ *', '=ke+´ *', 'R *', 'R +me´ *', 'R +tin *', 'R +´ *'];

// The analysed transcription uses single Latin-1 characters for Nahuatl sounds.
// These are the display forms the original site used.
const TRA_MAP = { '´': 'ˀ', 'ä': 'ā', 'ë': 'ē', 'ï': 'ī', 'ö': 'ō', '£': 'λ', '²': '¢', '¹': 'kʷ', '³': 'č', '¸': '≈' };
const PAL_MAP = { '´': "'", '¸': '≈' };
const mapChars = (s, m) => (s || '').replace(/[´äëïö£²¹³¸]/g, c => m[c] || c);
const tra = s => mapChars(s, TRA_MAP);
// Paleography: the database stores marks that Latin-1 could not hold as codes.
// "~" after a letter and "q#3" are an abbreviation tilde; umlaut vowels are macrons.
// Other codes (#2, #4, q#6, *, ^ …) are shown unchanged until their meaning is confirmed.
const PAL_VOWELS = { 'ä': 'ā', 'ë': 'ē', 'ï': 'ī', 'ö': 'ō', 'Ä': 'Ā', 'Ë': 'Ē', 'Ï': 'Ī', 'Ö': 'Ō' };
const pal = s => mapChars(s, PAL_MAP)
  .replace(/q#3/g, 'q\u0303')
  .replace(/(\S)~/g, '$1\u0303')
  .replace(/[äëïöÄËÏÖ]/g, c => PAL_VOWELS[c]);
// Commentary is prose: convert the vowel and consonant letters, but keep ´ (used there as a quote mark).
const COM_MAP = Object.fromEntries(Object.entries(TRA_MAP).filter(([c]) => c !== '´' && c !== '¸'));
const com = s => mapChars(s, COM_MAP);

const esc = s => String(s ?? '').replace(/[&<>"']/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));

// Search key: lower case, no accents, special letters spelled out, analysis marks removed.
const SEARCH_MAP = { '£': 'tl', '²': 'tz', '¹': 'cu', '³': 'ch', 'λ': 'tl', '¢': 'tz', 'č': 'ch', 'ʷ': '' };
function key(s) {
  return (s || '').toLowerCase()
    .replace(/[£²¹³λ¢čʷ]/g, c => SEARCH_MAP[c])
    .normalize('NFD').replace(/[̀-ͯ]/g, '')
    .replace(/#\d+/g, '')
    .replace(/[^a-z0-9ñ ]/g, '');
}

// ---------- Data ----------
const D = {
  roots: new Map(), words: new Map(), rootWords: new Map(), wordRoots: new Map(),
  sources: new Map(), sourcesByCode: new Map(), sentences: new Map(), folios: new Map(),
  occByWord: new Map(), occBySent: new Map(),
};

function rowsToObjects(fields, rows) {
  return rows.map(r => Object.fromEntries(fields.map((f, i) => [f, r[i]])));
}
function push(map, k, v) { (map.get(k) || map.set(k, []).get(k)).push(v); }

async function loadData() {
  const [dict, texts] = await Promise.all(['data/dictionary.json', 'data/texts.json'].map(u => fetch(u).then(r => {
    if (!r.ok) throw new Error(u + ': ' + r.status);
    return r.json();
  })));
  for (const r of rowsToObjects(dict.rootFields, dict.roots)) { r.k = [key(r.ort), key(r.nor), key(r.sea)]; D.roots.set(r.id, r); }
  for (const w of rowsToObjects(dict.wordFields, dict.words)) { w.k = [key(w.ort), key(w.nor), key(w.sea)]; D.words.set(w.id, w); }
  for (const [wid, rid] of dict.wordRoots) { push(D.wordRoots, wid, rid); push(D.rootWords, rid, wid); }

  for (const s of rowsToObjects(texts.sourceFields, texts.sources)) { D.sources.set(s.id, s); D.sourcesByCode.set(s.code, s); }
  for (const s of rowsToObjects(texts.sentenceFields, texts.sentences)) {
    D.sentences.set(s.id, s);
    const src = D.sources.get(s.bid);
    if (!src) continue;
    if (!src.folios) src.folios = new Map();
    push(src.folios, s.fol, s);
  }
  for (const src of D.sources.values()) {
    src.folioList = [...(src.folios || new Map()).keys()].filter(f => f > 0).sort((a, b) => a - b);
    for (const list of (src.folios || new Map()).values()) list.sort((a, b) => a.id - b.id);
  }
  for (const [wid, tid, wno] of texts.occurrences) {
    push(D.occByWord, wid, { tid, wno });
    if (!D.occBySent.has(tid)) D.occBySent.set(tid, new Map());
    D.occBySent.get(tid).set(wno, wid);
  }
}

// ---------- Sentence tokens (same counting as VisOrd() in the original site) ----------
// Tokens are split on single spaces. "<", ">", "{", "}" are markers and not counted;
// words inside { } are counted but cannot be linked.
function primaryLine(s) { return s.tra !== '' ? s.tra : s.pal; }
function wnoOffset(s) {
  // A sentence marked fof="+" continues a line marked fot="+"; its word numbers follow on from that line.
  if (s.fof !== '+') return 0;
  const src = D.sources.get(s.bid);
  const start = (src.folios.get(s.fol) || []).find(t => t.sno === s.sno && t.fot === '+');
  return start ? primaryLine(start).split(' ').length : 0;
}
function renderTokens(s, opts = {}) {
  const line = primaryLine(s);
  const show = s.tra !== '' ? tra : pal;
  const offset = wnoOffset(s);
  const occ = D.occBySent.get(s.id);
  let i = 0, linkable = true;
  return line.split(' ').map(tok => {
    if (tok === '<' || tok === '>') { linkable = true; return `<span class="muted">${esc(tok)}</span>`; }
    if (tok === '{') { linkable = false; return `<span class="muted">${esc(tok)}</span>`; }
    if (tok === '}') { linkable = true; return `<span class="muted">${esc(tok)}</span>`; }
    i++;
    const wno = i + offset;
    const hl = linkable && opts.highlight === wno ? ' hl' : '';
    const wid = linkable && occ && occ.get(wno);
    if (opts.links !== false && wid && D.words.has(wid)) {
      const w = D.words.get(wid);
      return `<a class="tok${hl}" href="#/word/${wid}" title="${esc(w.ort)}: ${esc(w.eng)}">${esc(show(tok))}</a>`;
    }
    return `<span class="tok${hl}">${esc(show(tok))}</span>`;
  }).join(' ');
}

// ---------- Search ----------
function search(q, scope) {
  const k = key(q).trim();
  const ql = q.trim().toLowerCase();
  if (!k && !ql) return [];
  const glossRe = new RegExp('(^|[^a-záéíóúñü])' + ql.replace(/[.*+?^${}()|[\]\\]/g, '\\$&') + '($|[^a-záéíóúñü])', 'i');
  const out = [];
  const scan = (map, kind) => {
    for (const e of map.values()) {
      let score = -1;
      if (k && e.k.includes(k)) score = 0;
      else if (k && e.k.some(x => x.startsWith(k))) score = 1;
      else if (ql && (glossRe.test(e.eng) || glossRe.test(e.spa))) score = 2;
      else if (k.length > 2 && e.k.some(x => x.includes(k))) score = 3;
      else if (ql.length > 2 && ((e.eng || '').toLowerCase().includes(ql) || (e.spa || '').toLowerCase().includes(ql))) score = 4;
      if (score >= 0) out.push({ e, kind, score });
    }
  };
  if (scope !== 'words') scan(D.roots, 'root');
  if (scope !== 'roots') scan(D.words, 'word');
  out.sort((a, b) => a.score - b.score || (a.kind === b.kind ? 0 : a.kind === 'root' ? -1 : 1) || a.e.ort.localeCompare(b.e.ort));
  return out;
}

// ---------- Views ----------
const app = document.getElementById('app');
const kindLabel = { root: 'Root', word: 'Word', text: 'Text' };

function entryRow(e, kind) {
  const href = `#/${kind}/${e.id}`;
  return `<tr><td><a href="${href}"><b>${esc(e.ort || '—')}</b></a><div class="small muted nah">${esc(tra(e.tra))}</div></td>` +
    `<td class="code"><span class="kind ${kind}">${kindLabel[kind]}</span></td><td>${esc(e.eng)}</td><td>${esc(e.spa)}</td></tr>`;
}
function entryTable(items) {
  return `<div class="table-scroll"><table class="list"><thead><tr><th>Entry</th><th></th><th>English</th><th>Spanish</th></tr></thead><tbody>` +
    items.map(x => entryRow(x.e, x.kind)).join('') + '</tbody></table></div>';
}

function viewHome() {
  const nRoots = D.roots.size, nWords = D.words.size, nSent = [...D.sentences.values()].filter(s => s.sno > 0).length;
  app.innerHTML = `
    <h1>Copenhagen Nahuatl Dictionary Project</h1>
    <p>CoNDiP is a tool for working out the use and meaning of words in 16th-century Nahuatl texts.
    It connects three parts: a <a href="#/roots">Root Dictionary</a> (${nRoots.toLocaleString('en')} roots),
    a <a href="#/words">Word Dictionary</a> (${nWords.toLocaleString('en')} words) and a corpus of
    <a href="#/texts">analysed texts</a> (${nSent.toLocaleString('en')} lines from ${D.sources.size} sources).</p>
    <p>From a root you can go to every word built on it; from a word to its roots and to every sentence where it occurs;
    and in a text you can click any analysed word to open its dictionary entry.</p>
    <div class="card">
      <b>Try:</b> <a href="#/search?q=atl">atl</a> · <a href="#/search?q=water">water</a> · <a href="#/search?q=casa">casa</a> ·
      <a href="#/search?q=tlacatl">tlacatl</a> · <a href="#/text/BANC/5">Bancroft Dialogues, folio 5</a>
    </div>
    <p class="muted small">Search ignores accents and length marks, so <i>atl</i> also finds <i>ātl</i>.
    English and Spanish glosses are searched as well.</p>`;
}

function viewSearch(params) {
  const q = params.get('q') || '';
  const scope = params.get('in') || 'all';
  document.getElementById('q').value = q;
  document.getElementById('scope').value = scope;
  const res = search(q, scope);
  if (res.length === 1) { location.replace(`#/${res[0].kind}/${res[0].e.id}`); return; }
  const shown = res.slice(0, 300);
  app.innerHTML = `<h1>Search: ${esc(q)}</h1>
    <p class="muted">${res.length ? `${res.length.toLocaleString('en')} result${res.length === 1 ? '' : 's'}${res.length > shown.length ? `, showing the first ${shown.length}` : ''}.` : 'No results.'}</p>
    ${shown.length ? entryTable(shown) : ''}`;
}

function viewIndex(kind, params) {
  const map = kind === 'root' ? D.roots : D.words;
  const letters = [...new Set([...map.values()].map(e => (e.k[0] || '?')[0]))].filter(Boolean).sort();
  const letter = params.get('l') || letters[0];
  const items = [...map.values()].filter(e => (e.k[0] || '?')[0] === letter)
    .sort((a, b) => a.k[0].localeCompare(b.k[0]) || a.id - b.id)
    .map(e => ({ e, kind }));
  app.innerHTML = `<h1>${kind === 'root' ? 'Root Dictionary' : 'Word Dictionary'}</h1>
    <p class="muted">${map.size.toLocaleString('en')} ${kind}s, ordered by spelling.</p>
    <nav class="letters">${letters.map(l => `<a href="#/${kind}s?l=${encodeURIComponent(l)}" class="${l === letter ? 'active' : ''}">${esc(l)}</a>`).join('')}</nav>
    ${entryTable(items)}`;
}

function facts(pairs) {
  return '<dl class="facts">' + pairs.filter(([, v]) => v !== '' && v != null)
    .map(([k, v]) => `<dt>${k}</dt><dd>${v}</dd>`).join('') + '</dl>';
}

function viewRoot(id) {
  const r = D.roots.get(id);
  if (!r) return notFound();
  const words = (D.rootWords.get(id) || []).map(w => D.words.get(w)).filter(Boolean)
    .sort((a, b) => a.ort.localeCompare(b.ort)).map(e => ({ e, kind: 'word' }));
  app.innerHTML = `<div class="kind root">Root</div><h1 class="nah">${esc(tra(r.tra))}</h1>
    <div class="card">${facts([
      ['Normalized', esc(tra(r.nor))], ['Orthography', esc(r.ort)],
      ['English', esc(r.eng)], ['Spanish', esc(r.spa)]])}
      ${r.com ? `<div class="comment">${esc(com(r.com))}</div>` : ''}</div>
    <h2>Words containing this root (${words.length})</h2>
    ${words.length ? entryTable(words) : '<p class="muted">None recorded.</p>'}`;
}

function wordFacts(w) {
  const rows = [['Normalized', esc(tra(w.nor))], ['Orthography', esc(w.ort)],
    ['English', esc(w.eng)], ['Spanish', esc(w.spa)],
    ['Class', esc(CLASSES[w.cla] ?? '')], ['Grammar', esc(GRAMMAR[w.gra] ?? '')]];
  if (w.cla === 0) rows.push(['Plural', esc(PLURAL[w.plu] ?? '')], ['Possessed', esc(tra(w.per))]);
  else if (w.cla === 1) rows.push(['Perfect', esc(tra(w.per))]);
  return facts(rows);
}

function viewWord(id) {
  const w = D.words.get(id);
  if (!w) return notFound();
  const roots = (D.wordRoots.get(id) || []).map(r => D.roots.get(r)).filter(Boolean)
    .sort((a, b) => a.ort.localeCompare(b.ort)).map(e => ({ e, kind: 'root' }));
  const see = w.cla === 7 && D.words.get(w.sew);
  const occ = (D.occByWord.get(id) || []).map(o => ({ ...o, s: D.sentences.get(o.tid) })).filter(o => o.s)
    .sort((a, b) => a.s.id - b.s.id);
  app.innerHTML = `<div class="kind word">Word</div><h1 class="nah">${esc(tra(w.tra))}</h1>
    <div class="card">${wordFacts(w)}
      ${see ? `<p>See: <a href="#/word/${see.id}"><b>${esc(see.ort)}</b></a> <span class="nah">${esc(tra(see.tra))}</span> – ${esc(see.eng)}</p>` : ''}
      ${w.com ? `<div class="comment">${esc(com(w.com))}</div>` : ''}</div>
    <h2>Roots (${roots.length})</h2>
    ${roots.length ? entryTable(roots) : '<p class="muted">None recorded.</p>'}
    <h2>Occurrences in texts (${occ.length})</h2>
    ${occ.length ? `<div class="table-scroll"><table class="list kwic"><thead><tr><th>Source</th><th>Line</th></tr></thead><tbody>${occ.map(o => {
      const src = D.sources.get(o.s.bid);
      const href = `#/text/${src.code}/${o.s.fol}?s=${o.s.id}&w=${o.wno}`;
      return `<tr><td class="code"><a href="${href}">${esc(src.code)} ${o.s.fol}.${o.s.sno}</a></td>
        <td class="line nah">${renderTokens(o.s, { highlight: o.wno, links: false })}</td></tr>`;
    }).join('')}</tbody></table></div>` : '<p class="muted">No occurrences recorded.</p>'}`;
}

function viewTexts() {
  app.innerHTML = `<h1>Texts</h1>
    <p>The corpus consists of analysed passages from these sources. Each line is given in analysed transcription,
    in the original spelling (paleography) and, where available, in English translation.</p>
    ${[...D.sources.values()].map(s => `<div class="card"><div class="kind text">${esc(s.code)}</div>
      <b>${esc(s.title)}</b>${s.note ? ` <span class="muted">– ${esc(s.note)}</span>` : ''}
      <div class="small" style="margin-top:6px">Folio${s.folioList.length === 1 ? '' : 's'}:
      ${s.folioList.map(f => `<a href="#/text/${s.code}/${f}">${f}</a>`).join(', ')}</div></div>`).join('')}`;
}

let textLayers = { pal: true, eng: true };
function viewText(code, fol, params) {
  const src = D.sourcesByCode.get(code);
  if (!src) return notFound();
  fol = fol || src.folioList[0];
  const lines = (src.folios.get(fol) || []).filter(s => s.sno > 0);
  if (!lines.length) return notFound();
  const cur = Number(params.get('s')) || 0;
  const wno = Number(params.get('w')) || 0;
  const i = src.folioList.indexOf(fol);
  const prev = src.folioList[i - 1], next = src.folioList[i + 1];
  const hasEng = lines.some(s => s.eng);
  const hasTra = lines.some(s => s.tra);
  app.innerHTML = `<div class="kind text">${esc(src.code)}</div><h1>${esc(src.title)}, folio ${fol}</h1>
    <nav class="folio-nav">
      <span>${prev != null ? `<a href="#/text/${code}/${prev}">← folio ${prev}</a>` : ''}</span>
      <a href="#/texts">All texts</a>
      <span>${next != null ? `<a href="#/text/${code}/${next}">folio ${next} →</a>` : ''}</span>
    </nav>
    <div class="tabs">
      ${hasTra ? `<button type="button" data-layer="pal" aria-pressed="${textLayers.pal}">Paleography</button>` : ''}
      ${hasEng ? `<button type="button" data-layer="eng" aria-pressed="${textLayers.eng}">English</button>` : ''}
    </div>
    <p class="small muted">Click an underlined word to open its entry in the Word Dictionary.</p>
    <div class="card" style="padding:0">${lines.map(s => `
      <div class="sentence${s.id === cur ? ' current' : ''}" id="s${s.id}">
        <div class="no">${s.sno}</div>
        <div>
          <div class="line nah">${renderTokens(s, { highlight: s.id === cur ? wno : 0 })}</div>
          ${s.tra && textLayers.pal && s.pal ? `<div class="line pal">${esc(pal(s.pal))}</div>` : ''}
          ${textLayers.eng && s.eng ? `<div class="line eng">${esc(s.eng)}</div>` : ''}
        </div>
      </div>`).join('')}</div>`;
  app.querySelectorAll('[data-layer]').forEach(b => b.addEventListener('click', () => {
    textLayers[b.dataset.layer] = !textLayers[b.dataset.layer];
    viewText(code, fol, params);
  }));
  if (cur) document.getElementById('s' + cur)?.scrollIntoView({ block: 'center' });
}

function viewAbout() {
  const abbr = [['rel. noun', 'relational noun'], ['zool.', 'zoological'], ['bot.', 'botanic'], ['min.', 'mineral'],
    ['astr.', 'astronomical'], ['obl. poss.', 'obligatorily possessed'], ['tr 1', 'transitive with one object'],
    ['tr 2', 'transitive with two objects'], ['tr 3', 'transitive with three objects'], ['tr 4', 'transitive with four objects'],
    ['imp.agent', 'impersonal agent (impersonal verb)'], ['imp.ag. 1', 'impersonal agent + 1 object (passive verb)'],
    ['imp.ag. 2', 'impersonal agent + 2 objects (passive verb with 1 object)'],
    ['imp.ag. 3', 'impersonal agent + 2 objects (passive verb with 2 objects)'], ['R', 'reduplication']];
  const letters = [['ā ē ī ō', 'long vowels'], ['ˀ', 'saltillo (glottal stop)'], ['λ', 'tl'], ['¢', 'tz'],
    ['č', 'ch'], ['kʷ', 'cu / uc'], ['-  +  =', 'morpheme boundaries in the analysed form'], ['#2', 'homonym number']];
  const textList = [...D.sources.values()].map(s => [s.code, s.folioList.length ? `fol. ${s.folioList.join(', ')}` : '', s.title + (s.note ? ': ' + s.note : '')]);
  app.innerHTML = `<h1>About CoNDiP</h1>
    <p>This dictionary describes Nahuatl as it is used in Mesoamerican sources and codices from the 16th century.
    Nahuatl is still spoken today by about 1.5 million people, mostly in central Mexico.</p>
    <h2>Project description</h2>
    <p class="small muted">From the 1992 description and manual by Una Canger.</p>
    <p>CoNDiP is a tool for cracking the use and meaning of words in Nahuatl texts. It provides immediate connections between
    texts and words, and between words and roots. It organizes the material, and facilitates studies of how words are used in texts,
    what words are formed with what roots, etc. It does not necessarily provide elegant text translations nor even adequate
    selections of glosses for roots and words. CoNDiP organizes and suggests, but interpretation is restricted to an absolute minimum.</p>
    <p>It is made up of three interconnected components: Root Dictionary, Word Dictionary and Text Component. From a root entry you may
    select a word containing the root and go to its entry in the Word Dictionary. From a word entry you may either select one of the roots
    contained in the word, or select a sentence containing the word and see the text containing that sentence. In the Text Component
    you may select any word in any text and go to the Word Dictionary to see that word's entry.</p>
    <p>CoNDiP can exist only on computers: the entries and texts could be printed in three or more books, but it is the connections
    between roots and words and between words and word forms in the texts that make CoNDiP the tool it is.</p>
    <h2>Notation in analysed forms</h2>
    <table class="list abbr-table"><tbody>${letters.map(([a, b]) => `<tr><td class="nah">${esc(a)}</td><td>${esc(b)}</td></tr>`).join('')}</tbody></table>
    <h2>Dictionary abbreviations</h2>
    <table class="list abbr-table"><tbody>${abbr.map(([a, b]) => `<tr><td>${esc(a)}</td><td>${esc(b)}</td></tr>`).join('')}</tbody></table>
    <h2>Texts</h2>
    <div class="table-scroll"><table class="list abbr-table"><tbody>${textList.map(([a, b, c]) => `<tr><td><a href="#/text/${a}">${esc(a)}</a></td><td class="small">${esc(b)}</td><td>${esc(c)}</td></tr>`).join('')}</tbody></table></div>
    <h2>Credits</h2>
    <p>CoNDiP was designed by <a href="https://en.wikipedia.org/wiki/Una_Canger">Una Canger</a> (University of Copenhagen).
    The original web version (2007–2008) was programmed by Michael Thomsen. This version presents the same data as a static website.</p>`;
}

function notFound() {
  app.innerHTML = `<h1>Not found</h1><p>That entry does not exist. <a href="#/">Go to the start page</a>.</p>`;
}

// ---------- Router ----------
function route() {
  const [path, qs] = location.hash.replace(/^#/, '').split('?');
  const params = new URLSearchParams(qs || '');
  const parts = (path || '/').split('/').filter(Boolean);
  document.querySelectorAll('[data-nav]').forEach(a => a.classList.toggle('active',
    a.dataset.nav === parts[0] || (a.dataset.nav === 'roots' && parts[0] === 'root') ||
    (a.dataset.nav === 'words' && parts[0] === 'word') || (a.dataset.nav === 'texts' && parts[0] === 'text')));
  const scrollTop = !(parts[0] === 'text' && params.get('s'));
  switch (parts[0]) {
    case undefined: viewHome(); break;
    case 'search': viewSearch(params); break;
    case 'roots': viewIndex('root', params); break;
    case 'words': viewIndex('word', params); break;
    case 'root': viewRoot(Number(parts[1])); break;
    case 'word': viewWord(Number(parts[1])); break;
    case 'texts': viewTexts(); break;
    case 'text': viewText(decodeURIComponent(parts[1] || ''), parts[2] ? Number(parts[2]) : 0, params); break;
    case 'about': viewAbout(); break;
    default: notFound();
  }
  if (scrollTop) window.scrollTo(0, 0);
  const h1 = app.querySelector('h1');
  document.title = (h1 && parts[0] ? h1.textContent + ' – ' : '') + 'CoNDiP';
}

document.getElementById('search-form').addEventListener('submit', e => {
  e.preventDefault();
  const q = document.getElementById('q').value.trim();
  const scope = document.getElementById('scope').value;
  if (q) location.hash = `#/search?q=${encodeURIComponent(q)}${scope !== 'all' ? '&in=' + scope : ''}`;
});

loadData().then(() => {
  window.addEventListener('hashchange', route);
  route();
}).catch(err => {
  app.innerHTML = `<h1>Could not load the dictionary</h1><p class="muted">${esc(err.message)}</p>
    <p>If you opened this file directly from disk, serve the folder with a local web server instead,
    for example <code>python3 -m http.server</code> inside the <code>site</code> folder.</p>`;
});
