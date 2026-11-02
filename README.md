# CoNDiP – Copenhagen Nahuatl Dictionary Project

Interactive dictionary of 16th-century Nahuatl linking a Root Dictionary, a Word Dictionary and a corpus of analysed texts.
Original site by Michael Thomsen and Una Canger (ca. 2007–2008).

## Contents
- `legacy/` – the original PHP site, with database credentials removed (put them in `legacy/config.php`, see `config.example.php`).
- `data/condip.sql` – MySQL export of the dictionary tables (exported from phpMyAdmin 2026-11-02; only the `test17*` tables).
- `data/csv/` – the same tables as UTF-8 CSV.
- `tools/sql_to_sqlite_csv.py` – rebuilds a SQLite file and the CSVs from the SQL export:
  `python3 tools/sql_to_sqlite_csv.py data/condip.sql condip.sqlite data/csv`

## Tables
| Table | Rows | What it holds |
|---|---|---|
| test17b | 1156 | Roots: `tra` analysed form, `ort` normalised spelling, `sea` search key, `eng`/`spa` glosses, `com` commentary |
| test17c | 8029 | Words: same fields plus `gra` grammar class, `plu`, `cla`, `sew` (see-also word id), `per` |
| test17cb | 12055 | Word ↔ root links (`wid`, `rid`) |
| test17d | 7 | Source texts (BANC, CAUB, CF02, CF11, CM8B, LD21, MOLSA) |
| test17e | 4888 | Text sentences: `bid` source, `fol` folio, `sno` sentence no., `tra` analysed text, `pal` palaeographic text, `eng` translation |
| test17cde | 13639 | Word ↔ sentence occurrences (`wid`, `bid`, `tid` sentence id, `wno` word position) – used by the site |
| test17cd | 13491 | Older version of the occurrence table, keyed by book/folio/sentence |

The analysed forms (`tra`) use special characters for Nahuatl phonology (e.g. `ä` long vowel, `´` saltillo); `legacy/condip.php` has the mapping tables (`$traf`/`$palf`) used for display.
