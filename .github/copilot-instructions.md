## Purpose

This file gives concise, repository-specific guidance for automated coding agents (Copilot-style) to be productive in this PHP web app.

### Quick context

- Monolithic PHP app (no framework). Public site hosted under family.matthieudevilliers.fr.
- Entry points are static PHP files in the project root and `pages/` (e.g. `index.php`, `pages/idees/index.php`).
- Database connection and authentication are performed in `scripts/verif/index.php` which defines a PDO instance `$bdd` and enforces HTTPS.

### Big picture & data flows

- Server-rendered, monolithic PHP site using Bootstrap for UI. Pages are plain PHP files under `pages/` that include `widgets/` partials for shared UI.
- Typical request flow: user authenticates in `pages/connexion/index.php` -> sets `$_SESSION['id_compte']` -> pages use `$bdd` (from `scripts/verif/index.php`) to query `lic_*` tables (lic_compte, lic_liste, lic_idee, lic_autorisation).
- Common operations:
  - Login: `pages/connexion/index.php` uses `password_verify()` and records a row in `lic_connexion`.
  - Listing: `pages/listes/index.php` queries `lic_liste` joined to `lic_autorisation` to determine visible lists.
  - Ideas: `pages/idees/index.php` reads and updates `lic_idee` (reservation via `buy_from`, soft-delete via `deleted_to`).
  - Changes by owners/moderators rely on `lic_autorisation.type` (`proprietaire`, `moderateur`, `lecteur`).

### Important conventions and patterns

- Global session use: files call `session_start()` and include `scripts/verif/index.php` to get `$bdd` and session checks. Look for `$_SESSION['id_compte']` as the logged-in user id.
- Redirects use absolute hostnames (https://family.matthieudevilliers.fr). When changing routes, preserve this pattern or update all references.
- SQL is used directly via prepared statements (`$bdd->prepare(...); $response->execute(array(...));`). Keep using prepared statements and close cursors with `$response->closeCursor()` where present.
- Soft deletes: many queries filter `deleted_to IS NULL` — prefer preserving this logic when adding queries or migrations.
- Authorization model stored in `lic_autorisation` and referenced by queries (fields: `type`, `id_liste`, `id_compte`). Use the same fields when adding permission checks.

### Common PHP / SQL idioms in this repo

- DB: `$bdd` is a PDO instance configured with `PDO::ERRMODE_EXCEPTION`. Use prepared statements with positional `?` placeholders and pass parameters as arrays (e.g. `$response = $bdd->prepare($sql); $response->execute(array($p1, $p2));`).
- Always call `$response->closeCursor()` after fetching where existing code does so. Many pages rely on explicit cursor closing.
- Input sanitisation: user inputs are wrapped with `htmlentities()` before INSERT/UPDATE in many places. Follow the same pattern when adding similar code (note: this is primarily output-encoding; for SQL safety rely on prepared statements).
- Passwords: `password_hash()` is used for storage and `password_verify()` for checks (see `pages/compte/index.php` and `pages/connexion/index.php`).
- Soft deletes: deletion is implemented by updating `deleted_to` with a timestamp instead of removing rows. Add `AND deleted_to IS NULL` to SELECTs to respect this.

### Developer workflows & local testing

- No build tool. To run locally from project root:

```bash
php -S localhost:8000
```

- The app forces HTTPS and redirects to the production hostname inside `scripts/verif/index.php`. For local testing you can:
  - Temporarily comment out the redirect lines in `scripts/verif/index.php`, or
  - Run behind a TLS proxy and use the production hostname in your hosts file (recommended if you want to mimic production).
- Debugging: PDO throws exceptions; for quick debugging you can var_dump fetched rows or enable display_errors in local PHP config.

### Integration points & external dependencies

- External/static assets are loaded via absolute URLs in `widgets/import/index.php` (Bootstrap CSS/JS, jQuery, FontAwesome). If you need offline dev, mirror these files locally and update imports.
- Email sending uses PHP `mail()` through `scripts/mail/index.php` with HTML headers and a Bcc to the developer. Consider this when modifying email flows.

### Edge cases & gotchas

- Many pages assume queries return rows before accessing array keys (e.g., `$donnee['id']`) — check for null returns from `$response->fetch()` and handle missing records to avoid notices/errors.
- Routes and links use absolute URLs. If you change to relative or environment-driven URLs, update all templates and pages consistently.
- Session variables: `$_SESSION['fonction']` is lazily populated by the navbar; avoid assumptions that it always exists elsewhere.
- Some pages mix HTML output and business logic heavily; prefer small, targeted edits to avoid regressions.

### Where to find UI components

### Notable widgets and patterns

- `widgets/import/index.php` centralises all head imports (Bootstrap CSS/JS, jQuery, FontAwesome script, custom CSS). It uses full absolute URLs to `https://family.matthieudevilliers.fr/css/...` and `.../js/...`. Editing site-wide assets should be done here.
- `widgets/navbar/index.php` checks `$_SESSION['id_compte']` and may include `widgets/notif/index.php`. It lazily populates `$_SESSION['fonction']` from `lic_compte.fonction` to show admin links; when modifying navbar behavior preserve this pattern and cursor handling (note existing `$req2->closeCursor()` usage).
- `widgets/footer/index.php` contains legal links and contact mailto; changes here affect every page.

### Local dev & testing notes (discoverable from repo)

### Mail helper

- `scripts/mail/index.php` exposes `envoiMail($to, $subject, $message)` which wraps PHP `mail()` and sets HTML headers. It currently BCCs the developer (`webmaster@matthieudevilliers.fr`) — keep or remove that header deliberately and document the decision.
- There is no build system; PHP files are served by a PHP-capable web server. To test locally, run a PHP built-in server from the project root:

  php -S localhost:8000

- The app expects HTTPS and will redirect to the production hostname. For local testing, either comment out the HTTPS redirect in `scripts/verif/index.php` or run behind a TLS-capable proxy that uses the same host.

### Coding agent guidelines (specific)

- When editing PHP pages, preserve existing session and include patterns: start files with `<?php session_start(); include('../../scripts/verif/index.php');` (adjust relative path when editing different directories).
- Use `$bdd` PDO instance provided by `scripts/verif/index.php` for all DB access. Do not create a new connection in pages; instead, centralize changes in `scripts/verif/index.php` if needed.
- Follow existing SQL patterns: prepared statements with positional placeholders (`?`) and arrays passed to `execute(...)`. Close cursors after fetching when the existing code does.
- Preserve soft-delete checks: add `AND deleted_to IS NULL` to selects unless intentionally changing deletion semantics.
- When adding routes or links, prefer full absolute URLs consistent with existing pages (e.g. `https://family.matthieudevilliers.fr/pages/...`). If converting to relative/ENV-based routing, update all occurrences and document the migration.

### Files to reference for behavior examples

- `scripts/verif/index.php` — DB connection, HTTPS enforcement, `$bdd` PDO var.
- `pages/idees/index.php` — example of session checks, permissions (`lic_autorisation`), listing and reservation logic.
- `pages/listes/index.php` and `pages/modif-listes/index.php` — owner/moderator patterns and link conventions.
- `widgets/import/index.php` — common <head> imports (CSS/JS) pattern used across pages.

### Safety & secrets

- `scripts/verif/index.php` currently contains production DB credentials. Do not commit changes that leak new secrets. If rotating credentials, update only `scripts/verif/index.php` and record the change in project notes.

### Security improvements implemented (October 2025)

- **Session security:** Configured httponly, secure, and SameSite=Strict cookies in `scripts/verif/index.php`
- **HTTP security headers:** Added X-Frame-Options, X-Content-Type-Options, Referrer-Policy, and Permissions-Policy in `.htaccess`
- **XSS protection:** Consistent use of `htmlspecialchars($var, ENT_QUOTES, 'UTF-8')` for all user outputs
- **IP spoofing prevention:** `getIp()` function now prioritizes `REMOTE_ADDR` over proxy headers
- **Secure password reset:** Using `random_bytes(32)` instead of `md5(rand())` for reset tokens
- **Session regeneration:** ID regenerated on password changes
- **Redirect safety:** All `header('Location: ...')` calls followed by `exit()` to prevent code execution
- **Input validation:** GET/POST parameters validated before use
- **Null checks:** Database `fetch()` results checked before array access
- **Component updates:** Bootstrap upgraded from v5.0.2 to v5.3.8 and jQuery from v3.5.1 to v3.7.1

When making changes, maintain these security patterns:
- Always escape output with `htmlspecialchars($var, ENT_QUOTES, 'UTF-8')`
- Always call `exit()` immediately after `header('Location: ...')`
- Always check `fetch()` results for null before accessing array keys
- Always validate required GET/POST parameters exist before use

### When to open a PR vs direct edits

- Small presentational fixes (typos, minor CSS) can be edited directly in feature branches. Database or auth changes require a PR and a clear migration plan because of production credentials and soft-delete semantics.

If anything above is unclear or you'd like additional examples (common SQL queries, widget usage, or a suggested local-dev script to avoid HTTPS redirect), tell me which section to expand.
