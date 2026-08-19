# Sokkies — project setup

## Structure
- `htmlv/` — the original static site (design reference, 1:1 with the XD). Do not edit.
- `sokkies-local/` — the WordPress site. Custom theme: `wp-content/themes/sokkies/`.
- `CLAUDE.md` — full project log & conventions (Dutch).

## Local development
1. Serve `sokkies-local/` with PHP 8.3+ and MySQL (MAMP/LocalWP/Herd). Document root can be the repo root (site then runs at `/sokkies-local/`).
2. Create a database and import the shared dump (ask the team lead — the dump is NOT in git).
3. Copy `sokkies-local/wp-config.example.php` to `wp-config.php`, fill in DB credentials + fresh salts. Keep table prefix `sokkies_`.
4. If your local URL differs from the dump's, run a serialization-safe search-replace on the URLs.
5. Log in at `/wp-admin/`, go to Settings → Permalinks → Save (flushes rewrites).

## Editing rules
- All ACF fields are registered in code (`inc/acf-fields.php`) — never via the ACF admin.
- Pages are built with the "Secties" builder; every field left empty falls back to the original static content.
- CSS: base in `assets/css/style.css`, responsive bands in `assets/css/responsive.css` (range-fenced — edit only the band you mean). The theme's copies are the live ones; `htmlv/` stays untouched.
- Content conventions: `[woord]` in a title field = yellow highlight, `<br>` allowed.

## Plugins (bundled in repo)
ACF Pro (licensed), ACF Extended (free), Classic Editor. Do not update ACF Pro without checking the license.
