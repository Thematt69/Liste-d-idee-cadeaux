# AGENTS.md

This repo is a PHP monolith, not a framework app. Most work happens in plain PHP pages under `pages/`, shared UI in `widgets/`, and database/session setup in `scripts/verif/index.php`.

## What to know first

- Start the app from the project root with `php -S localhost:8000`.
- The site enforces HTTPS in `scripts/verif/index.php`; redirects use the production hostname, so local testing usually needs a TLS proxy or a temporary local bypass.
- Authentication uses `$_SESSION['id_compte']` and checks are normally done before page logic.
- All DB access should use the existing `$bdd` PDO connection from `scripts/verif/index.php`; do not create a second database connection in page files.

## Repo conventions

- Follow the established include pattern: `session_start();` and then `include('../../scripts/verif/index.php');` (adjust the relative path for each folder).
- Use prepared statements with positional `?` placeholders and arrays passed to `execute(...)`.
- Close cursors when the pattern already does so (`$response->closeCursor()`).
- Preserve soft-delete semantics: many queries filter `deleted_to IS NULL` and should keep that condition unless the feature intentionally changes deletion behavior.
- Preserved permission checks use `lic_autorisation` with fields like `id_liste`, `id_compte`, and `type` (`proprietaire`, `moderateur`, `lecteur`).
- Preserve absolute URLs for links and redirects unless the repo is intentionally being migrated to a different routing model.

## Security rules

- Escape output with `htmlspecialchars($var, ENT_QUOTES, 'UTF-8')` or the existing `safe_output()` helper when outputting user-controlled values.
- Always call `exit()` immediately after `header('Location: ...')`.
- Validate required GET/POST parameters before use.
- Check `$response->fetch()` results for `null` before array access.
- Do not commit or add new database credentials, secrets, or production tokens.

## Useful references

- `README.md` — project notes and security updates.
- `scripts/verif/index.php` — DB connection, HTTPS enforcement, session setup, and helper functions.
- `pages/idees/index.php` — permission and listing logic example.
- `pages/listes/index.php` — list access and authorization flow.
- `widgets/import/index.php` and `widgets/navbar/index.php` — shared UI and bootstrap imports.

## Editing guidance

- Prefer small, targeted edits in the existing page/widget structure.
- Keep behavior consistent with the site's server-rendered, Bootstrap-based layout.
- If you change routes, links, or auth behavior, update all related pages in the same pass.
- When in doubt, match the surrounding file's established PHP patterns instead of introducing a new abstraction.

## Local testing

From the project root:

```bash
php -S localhost:8000
```

If HTTPS redirects block local testing, either temporarily bypass the redirect in `scripts/verif/index.php` or run behind a TLS-capable proxy.
