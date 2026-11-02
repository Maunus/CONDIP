"""Build the JSON files the static site reads, from the CSV export in data/csv.

Usage: python3 tools/build_site_data.py   (run from the repo root)
"""
import csv, json, os

ROOT = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
CSV = os.path.join(ROOT, 'data', 'csv')
OUT = os.path.join(ROOT, 'site', 'data')

def rows(table):
    with open(os.path.join(CSV, table + '.csv'), encoding='utf-8', newline='') as f:
        return list(csv.DictReader(f))

def clean(s):
    # Normalise line endings and trim trailing whitespace
    return (s or '').replace('\r\n', '\n').replace('\r', '\n').strip()

def num(s):
    return int(s) if s not in (None, '') else 0

SOURCE_NAMES = {
    'BANC': ("Bancroft Dialogues", "Huehuetlatolli - Discursos en Mexicano"),
    'CAUB': ("Codex Aubin", ""),
    'CF02': ("Florentine Codex, Book 2", "Bernardino de Sahagún"),
    'CF11': ("Florentine Codex, Book 11", "Bernardino de Sahagún"),
    'CM8B': ("Códice Matritense de la Real Academia de la Historia", "Bernardino de Sahagún"),
    'LD21': ("Land document #21", "Baltasar Esteban and wife sell or donate land to church, Coyoacan region, 1575"),
    'MOLSA': ("Molina", "Spanish–Nahuatl part of Molina's dictionary"),
}

def main():
    os.makedirs(OUT, exist_ok=True)
    roots = [[num(r['id']), clean(r['tra']), clean(r['nor']), clean(r['ort']), clean(r['sea']),
              clean(r['eng']), clean(r['spa']), clean(r['com'])] for r in rows('test17b')]
    words = [[num(r['id']), clean(r['tra']), clean(r['nor']), clean(r['ort']), clean(r['sea']),
              clean(r['eng']), clean(r['spa']), clean(r['com']), num(r['gra']), num(r['plu']),
              num(r['cla']), num(r['sew']), clean(r['per'])] for r in rows('test17c')]
    links = [[num(r['wid']), num(r['rid'])] for r in rows('test17cb')]
    sources = [[num(r['id']), r['bog'], *SOURCE_NAMES.get(r['bog'], (r['bog'], ''))] for r in rows('test17d')]
    sentences = [[num(r['id']), num(r['bid']), num(r['fol']), num(r['sno']), r['fot'] or '', r['fof'] or '',
                  clean(r['tra']), clean(r['pal']), clean(r['eng'])] for r in rows('test17e')]
    occ = [[num(r['wid']), num(r['tid']), num(r['wno'])] for r in rows('test17cde')]

    dump = lambda name, obj: json.dump(obj, open(os.path.join(OUT, name), 'w', encoding='utf-8'),
                                       ensure_ascii=False, separators=(',', ':'))
    dump('dictionary.json', {
        'rootFields': ['id', 'tra', 'nor', 'ort', 'sea', 'eng', 'spa', 'com'],
        'wordFields': ['id', 'tra', 'nor', 'ort', 'sea', 'eng', 'spa', 'com', 'gra', 'plu', 'cla', 'sew', 'per'],
        'roots': roots, 'words': words, 'wordRoots': links})
    dump('texts.json', {
        'sourceFields': ['id', 'code', 'title', 'note'],
        'sentenceFields': ['id', 'bid', 'fol', 'sno', 'fot', 'fof', 'tra', 'pal', 'eng'],
        'occurrenceFields': ['wid', 'tid', 'wno'],
        'sources': sources, 'sentences': sentences, 'occurrences': occ})
    for n in ('dictionary.json', 'texts.json'):
        print(n, os.path.getsize(os.path.join(OUT, n)) // 1024, 'KB')

if __name__ == '__main__':
    main()
