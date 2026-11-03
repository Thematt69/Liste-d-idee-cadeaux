---
applyTo: "**/*.php"
description: "Use when editing PHP pages, SQL queries, or transactional database logic in this monolithic gift-list application."
---

# PHP and SQL conventions

## Required project pattern

- Keep the app structure as a PHP monolith: page files in `pages/`, shared UI in `widgets/`, and bootstrap logic in `scripts/verif/index.php`.
- Use the existing `$bdd` PDO connection instead of creating a new DB connection inside page logic.
- Include `scripts/session/index.php` before the verifier bootstrap from the correct relative path; do not bypass the shared session configuration with a direct `session_start()`.
- Validate required POST/GET values before using them.

## SQL rules

- Use prepared statements with positional `?` placeholders and arrays passed to `execute(...)`.
- Avoid string concatenation to build SQL queries.
- Keep the project’s current transactional patterns when they already exist.
- For write operations involving several statements, prefer a transaction with `beginTransaction()`, `commit()`, and rollback on exception.
- Preserve `deleted_to IS NULL` filters unless the change is an explicit business decision.
- Preserve authorization checks against `lic_autorisation` and the expected roles (`proprietaire`, `moderateur`, `lecteur`).

## Transaction guidance

- A transaction must cover the full logical action, especially for related edits across several tables.
- Always roll back on failure and log or surface the error clearly.
- Do not leave partially updated state behind if a DB write fails.
- Keep the transaction boundaries small and aligned with the page’s existing behavior.

## Safety and auditability

- Check `$response->fetch()` results before reading array keys.
- Continue to use `closeCursor()` where the repo already does so.
- Keep redirects safe: `header('Location: ...'); exit();`.
- Escape output with `htmlspecialchars($var, ENT_QUOTES, 'UTF-8')` or `safe_output()`.
- Do not add production credentials or secrets to the repository.

## Local autonomy

- Before running the application, verify isolated SQL and mail destinations under the operational policy in `docs/security/politique-agents.md`.
- Use local HTTPS or a TLS proxy; do not bypass HTTPS enforcement. Syntax validation with `php -l` does not execute the application.
