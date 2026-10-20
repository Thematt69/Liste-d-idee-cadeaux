---
applyTo: "**/*.{php,md,js,css}"
description: "Use when handling security, output escaping, session behavior, SQL safety, secrets, or local development setup for the project."
---

# Security and data handling rules

## Output and validation

- Escape user-controlled output with `htmlspecialchars($var, ENT_QUOTES, 'UTF-8')` or the repo helper `safe_output()`.
- Validate required GET and POST parameters before they are used.
- Check `fetch()` results for `null` before reading keys like `$donnee['id']`.
- Use the project’s existing patterns for redirect safety: `header('Location: ...'); exit();`.

## Database and auth

- Keep all SQL access parameterized and use the existing PDO connection.
- Preserve soft-delete conditions and permission checks when writing new queries.
- Do not create a second database connection in page or widget files.
- Treat session state as sensitive; do not expose or log user ids or tokens unnecessarily.

## Secrets and environment

- Never add production credentials, database passwords, or API keys to source code.
- Keep local development safe and avoid committing environment-specific secrets.
- If a local setup requires bypassing HTTPS redirects, do it temporarily and do not leave the bypass in committed code.

## Local testing

- Use `php -S localhost:8000` from the project root for a quick local run.
- If HTTPS enforcement blocks your local server, use a temporary local bypass or a TLS-enabled proxy while testing.

## General security expectations

- Keep application behavior aligned with the secure session and redirect patterns already implemented in `scripts/verif/index.php`.
- Prefer minimal, explicit changes over broad refactors when improving security or data handling.
- When touching auth or DB logic, review permission checks, output escaping, and query filtering together.
