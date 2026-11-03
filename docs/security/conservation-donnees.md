# Conservation des données

Décision explicite du 3 novembre 2026 : l'utilisateur demande de retirer complètement le principe de purge. Cette décision remplace la proposition de confinement avec une maintenance CLI conservée et les travaux de purge prévus dans le plan initial.

## Règle applicative

Les comptes, listes, idées et notifications supprimés logiquement restent conservés. L'historique de connexion est conservé sans nettoyage fondé sur l'ancienneté ni limite de nombre d'enregistrements. Aucun bouton administrateur, tâche de connexion ou lanceur CLI de purge n'est proposé.

Les opérations métier de retrait d'autorisations et d'invalidation des jetons de récupération suivent leurs parcours de sécurité existants. Leur traitement ne constitue pas une politique de purge des données conservées.

## Mise en œuvre locale

- Retrait des requêtes de purge de comptes, listes, idées, notifications et connexions de `scripts/clean/index.php`.
- Retrait de `cleanup_old_connexions()`, de son appel après authentification, de l'action POST administrateur et du bouton de nettoyage.
- Retrait du mode CLI autorisé et de sa procédure. Le fichier de l'ancienne route ne contient plus de SQL ni d'inclusion : HTTP 410, CLI en échec avec code 1. La règle Apache du répertoire peut refuser l'URL en 403 avant PHP.
- Conservation de ce fichier inerte pour écraser également une ancienne copie lors d'un futur déploiement. Les ressources internes et le graphe historique ne sont pas des mécanismes exécutables de maintenance.

## Déploiement et vérification

Faire supprimer les anciennes tâches cron et les lanceurs externes par le responsable de l'hébergement lors d'une intervention autorisée ; ne pas les remplacer par une autre purge. La présence et le contenu de ces tâches ne sont pas connus depuis le dépôt local.

Vérifier syntaxe PHP, absence des anciens appels/fonctions et de SQL dans l'ancienne route. Tester cette route sur une copie temporaire isolée, y compris avec l'ancien contexte CLI fourni, pour confirmer qu'aucune requête n'est exécutée. Aucun accès aux données réelles n'est nécessaire.

Cette modification du code ne restaure pas les enregistrements supprimés auparavant. Une éventuelle restauration ou une future évolution de la conservation doit faire l'objet d'une décision et d'une procédure distinctes.
