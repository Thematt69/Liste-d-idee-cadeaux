# Graph Report - Liste-d-idee-cadeaux  (2026-10-24)

## Corpus Check
- Corpus is ~12,084 words - fits in a single context window. You may not need a graph.

## Summary
- 51 nodes · 193 edges · 7 communities (6 shown, 1 thin omitted)
- Extraction: 100% EXTRACTED · 0% INFERRED · 0% AMBIGUOUS
- Token cost: 0 input · 0 output

## Community Hubs (Navigation)
- Authentification et comptes
- Listes et idées
- Notifications et courriels
- Administration et maintenance
- Pages publiques et widgets
- Infrastructure partagée
- Documentation

## God Nodes (most connected - your core abstractions)
1. `Widget navbar` - 21 edges
2. `Widget import` - 18 edges
3. `Widget footer` - 18 edges
4. `Infrastructure partagée` - 18 edges
5. `Page modif-idees` - 17 edges
6. `Page listes` - 16 edges
7. `id_compte` - 16 edges
8. `Page plan-site` - 15 edges
9. `Page connexion` - 14 edges
10. `Page idees` - 14 edges

## Surprising Connections (you probably didn't know these)
- `Widget import` --references--> `Logo`  [EXTRACTED]
  widgets/import/index.php → images/logo.webp
- `Widget navbar` --references--> `Logo`  [EXTRACTED]
  widgets/navbar/index.php → images/logo.webp
- `Script deconnexion` --redirects_to--> `/pages/connexion/`  [EXTRACTED]
  scripts/deconnexion/index.php → index.php
- `Widget navbar` --links_to--> `Script deconnexion`  [EXTRACTED]
  widgets/navbar/index.php → scripts/deconnexion/index.php
- `Page admin` --calls_function--> `envoiMail()`  [EXTRACTED]
  pages/admin/index.php → scripts/mail/index.php

## Import Cycles
- None detected.

## Hyperedges (group relationships)
- **Flux d'authentification** — pages_connexion_index, pages_inscription_index, pages_reset_password_index, scripts_deconnexion_index, custom_table_lic_compte, custom_session_id_compte [EXTRACTED 1.00]
- **Cycle de vie d'une liste** — pages_listes_index, pages_modif_listes_index, pages_idees_index, pages_modif_idees_index, pages_print_liste_index, custom_table_lic_liste, custom_table_lic_idee, custom_table_lic_autorisation [EXTRACTED 1.00]
- **Notifications** — js_notifications, widgets_notif_toast_index, scripts_mark_notif_read_index, scripts_mail_index, custom_table_lic_notif [EXTRACTED 1.00]
- **Infrastructure partagée (session, PDO, helpers)** — custom_infra_shared, index, pages_accessibilite_index, pages_admin_index, pages_cgu_index, pages_compte_index, pages_connexion_index, pages_idees_index, pages_inscription_index, pages_listes_index, pages_mentions_legales_index, pages_modif_idees_index, pages_modif_listes_index, pages_plan_site_index, pages_print_liste_index, pages_reset_password_index, scripts_clean_index, scripts_mark_notif_read_index, scripts_verif_index [EXTRACTED 1.00]

## Communities (7 total, 1 thin omitted)

### Community 0 - "Authentification et comptes"
Cohesion: 0.33
Nodes (10): fonction, id_compte, lic_compte, lic_connexion, Page compte, Page connexion, getIp(), Page inscription (+2 more)

### Community 1 - "Listes et idées"
Cohesion: 0.40
Nodes (11): /pages/erreur/403.php, /pages/idees/, /pages/modif-listes/, lic_autorisation, lic_idee, lic_liste, Page idees, Page listes (+3 more)

### Community 2 - "Notifications et courriels"
Cohesion: 0.21
Nodes (7): /scripts/mark-notif-read/, lic_notif, JS notifications, Script mail, envoiMail(), Script mark-notif-read, Widget notif-toast

### Community 3 - "Administration et maintenance"
Cohesion: 0.33
Nodes (3): Page admin, Script clean, cleanup_old_connexions()

### Community 4 - "Pages publiques et widgets"
Cohesion: 0.31
Nodes (14): /pages/connexion/, /pages/listes/, Logo, Page accueil, Page accessibilite, Page cgu, Page erreur 401, Page erreur 403 (+6 more)

### Community 5 - "Infrastructure partagée"
Cohesion: 0.67
Nodes (3): Infrastructure partagée, Script verif, safe_output()

## Knowledge Gaps
- **3 isolated node(s):** `Instructions pour les agents`, `README`, `/pages/modif-listes/`
  These have ≤1 connection - possible missing edges or undocumented components. (Counts symbols only; 4 node(s) total have ≤1 connection when file, concept and rationale nodes are included.)
- **1 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `Widget navbar` connect `Pages publiques et widgets` to `Authentification et comptes`, `Listes et idées`, `Administration et maintenance`?**
  _High betweenness centrality (0.128) - this node is a cross-community bridge._
- **What connects `Instructions pour les agents`, `README`, `/pages/modif-listes/` to the rest of the system?**
  _3 weakly-connected nodes found - possible documentation gaps or missing edges._
- **Why does `Infrastructure partagée` connect `Infrastructure partagée` to `Authentification et comptes`, `Listes et idées`, `Notifications et courriels`, `Administration et maintenance`, `Pages publiques et widgets`?**
  _High betweenness centrality (0.124) - this node is a cross-community bridge._
- **Why does `Script mark-notif-read` connect `Notifications et courriels` to `Authentification et comptes`, `Infrastructure partagée`?**
  _High betweenness centrality (0.111) - this node is a cross-community bridge._