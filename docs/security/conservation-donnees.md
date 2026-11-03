# Conservation des données

Décision explicite du 3 novembre 2026 : l'utilisateur demande de retirer complètement le principe de purge. Cette décision remplace la proposition de confinement avec une maintenance CLI conservée et les travaux de purge prévus dans le plan initial.

## Règle applicative

Les comptes, listes, idées et notifications supprimés logiquement restent conservés. L'historique de connexion est conservé sans nettoyage fondé sur l'ancienneté ni limite de nombre d'enregistrements. Aucun bouton administrateur, tâche de connexion ou lanceur CLI de purge n'est proposé.

Les opérations métier de retrait d'autorisations et d'invalidation des jetons de récupération suivent leurs parcours de sécurité existants. Leur traitement ne constitue pas une politique de purge des données conservées.

## Mise en œuvre locale

- Retrait des requêtes de purge de comptes, listes, idées, notifications et connexions de `scripts/clean/index.php`.
- Retrait de `cleanup_old_connexions()`, de son appel après authentification, de l'action POST administrateur et du bouton de nettoyage.
- Retrait du mode CLI autorisé et de sa procédure. Un fichier inerte avait d'abord été préparé ; le répertoire `scripts/clean/` a ensuite été supprimé du dépôt au commit `11e3fa1`.
- Les ressources internes et le graphe historique décrivent l'état antérieur et ne sont pas des mécanismes exécutables de maintenance.

## Déploiement et vérification

Faire supprimer les anciennes tâches cron et les lanceurs externes par le responsable de l'hébergement lors d'une intervention autorisée ; ne pas les remplacer par une autre purge. La présence et le contenu de ces tâches ne sont pas connus depuis le dépôt local.

Vérifier l'absence des anciens fichiers, appels et fonctions dans le code déployé. Faire confirmer le retrait de toute ancienne copie de `scripts/clean/` sur l'hébergement ; l'état distant ne peut pas être déduit de la suppression locale. Les tests HTTP 410/CLI de l'historique concernaient le fichier inerte avant sa suppression. Aucun accès aux données réelles n'est nécessaire à cette vérification.

Cette modification du code ne restaure pas les enregistrements supprimés auparavant. Une éventuelle restauration ou une future évolution de la conservation doit faire l'objet d'une décision et d'une procédure distinctes.
