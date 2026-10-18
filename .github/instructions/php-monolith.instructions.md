---
applyTo: "**/*.php"
description: "Use when editing PHP pages, widgets, scripts, or server-side logic in this monolithic gift-list web app."
---

# PHP project conventions

## Architecture

This project is a server-rendered PHP monolith. Most work occurs in page files under `pages/`, shared UI in `widgets/`, and central bootstrapping in `scripts/verif/index.php`.

## Required patterns

- Start files with `session_start();` when the page uses the session.
- Include the verification bootstrap from the correct relative path: `include('../../scripts/verif/index.php');` or equivalent.
- Use the existing `$bdd` PDO instance from `scripts/verif/index.php`.
- Prefer prepared statements and positional placeholders, for example: `$bdd->prepare($sql); $response->execute(array($p1, $p2));`.
- Follow existing cursor-closing patterns when the code already uses `$response->closeCursor()`.

## App-specific logic

- Treat `$_SESSION['id_compte']` as the authenticated user id.
- Preserve soft-delete checks with `AND deleted_to IS NULL` unless a feature explicitly changes deletion semantics.
- Respect the authorization rules stored in `lic_autorisation` (`id_compte`, `id_liste`, `type`).
- Preserve app-specific behavior such as public/limited/private list access and owner/moderator roles.
- Use absolute URLs for redirects and links unless the project deliberately migrates to a new routing model.

## Editing guidance

- Keep changes scoped to the current page or widget; do not introduce framework patterns or new service layers.
- Match the surrounding code style instead of creating new abstractions.
- For list and idea pages, verify both the DB query and the permission logic before changing behavior.
- If a route, link, or authorization rule changes, update the related screens in the same patch.
