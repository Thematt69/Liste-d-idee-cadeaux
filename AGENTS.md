# Instructions pour les agents

Ces règles s'appliquent à tout le dépôt. Garder `AGENTS.md` comme socle commun; les fichiers propres à un outil peuvent ajouter des règles ciblées sans dupliquer ces conventions générales.

## Projet

- Application PHP procédurale, sans framework. Les pages sont dans `pages/`; logique partagée dans `scripts/`; éléments communs dans `widgets/`; ressources statiques dans `css/` et `js/`.
- `scripts/verif/index.php` configure la session, impose HTTPS, initialise le PDO global `$bdd` et définit des helpers comme `safe_output()`.
- L'identité de session repose surtout sur `$_SESSION['id_compte']`. Les droits sur les listes sont gérés par `lic_autorisation`.
- Les suppressions sont généralement logiques via `deleted_to`.

## Conventions de modification

- Lire la page visée et un exemple voisin avant de modifier. Les pages mêlent souvent PHP, SQL et HTML; limiter les changements au besoin demandé.
- Réutiliser `$bdd`; ne pas créer de nouvelle connexion PDO dans une page. Utiliser des requêtes préparées, des paramètres liés et fermer les curseurs selon les usages du fichier.
- Respecter les contrôles d'accès existants et vérifier côté serveur les données GET/POST. Vérifier le résultat de `fetch()` avant d'accéder à ses champs.
- Pour les valeurs non fiables rendues en HTML, utiliser `safe_output()` ou l'échappement adapté au contexte. Ne pas confondre échappement HTML et protection SQL.
- Toute redirection `header('Location: ...')` doit être suivie immédiatement de `exit()`.
- Préserver le démarrage de session et les inclusions déjà utilisés dans la page. Pour les lectures, respecter les filtres `deleted_to IS NULL` quand ils s'appliquent.
- Les routes utilisent parfois l'hôte de production, tandis que les ressources partagées utilisent des chemins comme `/css/` et `/js/`. Suivre le contexte local; ne pas normaliser les URL à l'échelle du site sans demande explicite.

## Vérification et développement local

- Aucun gestionnaire de dépendances ni lanceur de tests automatisés n'est présent actuellement.
- Pour un fichier PHP modifié, lancer `php -l chemin/du/fichier.php`; contrôler aussi `git diff --check`.
- Le serveur intégré peut être lancé depuis la racine avec `php -S localhost:8000`. L'inclusion de vérification redirige le HTTP vers le site de production; utiliser un environnement HTTPS local ou un proxy TLS pour tester. Ne jamais désactiver cette redirection dans un changement destiné à la production.
- `.github/workflows/deploy.yml` déploie par FTP sur la production après un push sur `main`. Traiter toute modification du déploiement comme une modification de production.

## Sécurité et intégrations

- `scripts/verif/index.php` contient des identifiants sensibles en dur. Ne jamais les recopier dans les instructions, les sorties ou d'autres fichiers; ne pas modifier ce mécanisme sans besoin explicite.
- `scripts/mail/index.php` envoie les courriels via `mail()` et ajoute actuellement le webmaster en copie cachée. En tenir compte lors de toute modification des courriels.
- Consulter [README.md](README.md) pour les informations de projet déjà documentées; éviter de les recopier ici.

## graphify

For any question about this repo's architecture, structure, components, or how to add/modify/find
code, your first action should be `graphify query "<question>"` when `graphify-out/graph.json`
exists. Use `graphify path "<A>" "<B>"` for relationship questions and `graphify explain "<concept>"`
for focused-concept questions. These return a scoped subgraph, usually much smaller than the full
report or raw grep output.

Triggers: "how do I…", "where is…", "what does … do", "add/modify a <component>",
"explain the architecture", or anything that depends on how files or classes relate.

If `graphify-out/wiki/index.md` exists, use it for broad navigation. Read `graphify-out/GRAPH_REPORT.md`
only for broad architecture review or when query/path/explain do not surface enough context. Only read
source files when (a) modifying/debugging specific code, (b) the graph lacks the needed detail, or
(c) the graph is missing or stale.

Type `/graphify` in Copilot Chat to build or update the graph.
