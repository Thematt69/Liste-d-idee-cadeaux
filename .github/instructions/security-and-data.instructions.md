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
- Follow `AGENTS.md` and `docs/security/politique-agents.md` for authorization boundaries, incident handling and untrusted content. Never print lines containing existing credentials or tokens.

## Local testing

- Run the application only after verifying isolated database and mail destinations, using synthetic data and local HTTPS or a TLS proxy.
- Do not bypass HTTPS enforcement. Inspect scripts and their network effects before execution; a local command can still contact production.

## General security expectations

- Keep application behavior aligned with `scripts/session/index.php` for sessions and `scripts/verif/index.php` for HTTPS and shared validation.
- Prefer minimal, explicit changes over broad refactors when improving security or data handling.
- When touching auth or DB logic, review permission checks, output escaping, and query filtering together.
