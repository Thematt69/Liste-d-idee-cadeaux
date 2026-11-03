# Suivi de remédiation P0

Établi le 3 novembre 2026 à partir du plan P0 fourni par l'utilisateur et du dépôt local au commit `764483d`, puis complété par les changements locaux de l'incrément 8. L'[audit initial](../architecture/audit.md) décrit `614eb9c` ; son registre reste une photographie historique. Voir la [politique des agents](politique-agents.md) pour les limites d'exécution.

## État observé et preuves

**Revue du 4 novembre 2026 :** le [bilan du backlog](verification-backlog-2026-11-04.md) complète cet état avec les preuves, corrections et validations restantes de T0 à T12. Il ne conclut pas à une clôture globale. Les reliquats locaux T1/T4 et les écritures de listes T2/T11 ont été corrigés et font partie d'un test reproductible sans service réel.

L'utilisateur a demandé l'implémentation du plan puis le passage aux incréments suivants. Les commits ci-dessous attestent de correctifs présents dans le dépôt local. Ils ne prouvent ni leur déploiement, ni le passage de tests fonctionnels ou SQL. Les validations syntaxiques rapportées dans les échanges restent distinctes de ces tests.

| Incrément du plan P0 | Référence locale / statut | Validation restant nécessaire |
| --- | --- | --- |
| 0 — Purge et secrets (R7, R8) | Purge abandonnée sur demande explicite : ancien répertoire `scripts/clean/`, mode CLI et nettoyages de connexions retirés du dépôt. Secrets R8 encore dans le code ; état déployé inconnu. | Faire retirer les anciennes tâches/lanceurs externes, vérifier le déploiement du retrait et traiter la rotation des secrets séparément. |
| 1 — Profil (R1) | Correctif présent : `ee92747`. | Deux comptes, deux branches, collision, compte supprimé et formats historiques sur base fictive. |
| 2 — Mutations de listes (R2) | Correctif présent : `4afd4b8`. | Matrice des rôles, IDs croisés et échecs intermédiaires. |
| 3 — Réservations (R3) | Correctif présent : `ffb0f73`. | Deux connexions SQL concurrentes, idées supprimées et réservations existantes. |
| 4 — Droits et homonymes d'idées (R4, R6) | Correctif présent : `77b27bb`. | Rôles, parents supprimés, idées homonymes et interruption d'une opération multiple. |
| 5 — Récupération (R5) | Correctif présent : `6e79c0e` ; migration additive préparée dans `tools/migrations/001_password_reset_tokens.sql`. | Application de la migration non établie ; mail fictif, échec d'envoi, expiration, usage unique et comptes historiques à vérifier. |
| 6 — Création et partage (R9) | Correctif présent : `e9aa23f`. | Concurrence, taille/contraintes réelles des colonnes, transaction et liens historiques. |
| 7 — CSRF, sessions et sorties (R10, R11) | Correctifs présents : `7779142`, `e4be3d8`, `764483d`, `49ca90a` ; compte actif et rôle revalidés avant les actions via le bootstrap, accueil et inscription corrigés localement pour T9. | Tests avec PDO factice réussis ; validation MariaDB isolée, CSRF/AJAX, cookies sur l'hébergement et affichages restante. |
| 8 — Gouvernance, CI, déploiement | CI syntaxique présente : `a3608b2` ; politique, suivi, harmonisation des consignes et filtres de publication préparés localement dans le présent incrément. | Exécution CI hors production, protections GitHub, FTPS, sauvegardes et retour arrière LWS non vérifiés. |

Le serveur déclaré par l'utilisateur est MariaDB `10.11.19` ; PHP déclaré est `8.2.35`, avec `utf8mb4` et `utf8mb4_unicode_ci`. Ces informations ne remplacent pas le contrôle des moteurs de tables, contraintes, index et réglages SQL effectifs. La configuration Docker/base locale a été explicitement mise en pause dans l'échange.

## Changements locaux de l'incrément 8

- `AGENTS.md`, les consignes PHP/sécurité et `.clinerules` utilisent le bootstrap de session partagé, une isolation SQL/mail vérifiée et HTTPS local ; les autorisations et la gestion des incidents sont explicites.
- Le job de syntaxe conserve PHP `8.2.35`, monte le dépôt en lecture seule et dispose de dix minutes. Les deux checkouts ne conservent pas le jeton Git dans leur configuration locale.
- Le job FTP exige `main` même en déclenchement manuel ; un groupe de concurrence évite deux transferts simultanés de ce workflow sans annuler le transfert en cours. La validation syntaxique reste un prérequis.
- Les filtres FTP excluent documentation, graphe, consignes, outils, fichiers d'environnement, exports SQL, caches et sauvegardes usuelles. Les chemins applicatifs et `.htaccess` restent inclus. Il s'agit de filtres, pas encore d'un artefact construit depuis une liste exhaustive de fichiers autorisés.

Ces exclusions portent sur les transferts futurs et n'effacent pas les anciennes copies distantes. La [documentation de FTP-Deploy-Action v4.3.6](https://github.com/SamKirkland/FTP-Deploy-Action/tree/v4.3.6#settings) indique qu'un fichier exclu est aussi exclu du traitement des suppressions. Inventorier les fichiers déjà publiés et planifier leur protection ou retrait avec une personne autorisée. Ne pas activer `dangerous-clean-slate`.

## Prérequis GitHub et LWS avant déploiement

| Cible | Validation à fournir et action suivante |
| --- | --- |
| Branche `main` | Vérifier une règle imposant PR, revue humaine et le contrôle `Validate PHP syntax`, ainsi que les droits de contournement. Le YAML seul ne configure pas ces règles. |
| Environnement protégé | Configurer et vérifier un environnement `production` avec restriction à `main` et approbateurs, selon les possibilités du dépôt, puis le référencer dans le job de déploiement. Ajouter son nom au YAML sans règles effectives ne garantit pas la revue. |
| Secrets GitHub | Vérifier accès minimaux, emplacement des secrets FTP et contrôle des secrets du dépôt ; préparer la rotation séparément. |
| Références d'actions et image | Vérifier les SHA des versions d'actions auprès des dépôts officiels puis les épingler selon les [recommandations GitHub](https://docs.github.com/en/actions/reference/security/secure-use) ; envisager le digest de l'image PHP avec une procédure de mise à jour. Les tags actuels ne sont pas des références immuables. |
| Transport LWS | Confirmer FTPS, port et chaîne de certificat avant de modifier `protocol`/`security`. Le workflow ne spécifie actuellement pas FTPS ; son transport reste à sécuriser. |
| Fichiers web et maintenance | Inventorier document root, fichiers internes déjà présents, règles héritées et cron ; supprimer les anciens appels de purge. Une exclusion FTP n'est pas un contrôle HTTP. |
| Préservation | Confirmer sauvegardes, intégrité, restauration isolée et procédure tenant compte des écritures intervenues après sauvegarde. |

Les [protections d'environnements GitHub](https://docs.github.com/en/actions/how-tos/deploy/configure-and-manage-deployments/control-deployments) peuvent imposer une approbation et limiter les branches ; leur configuration distante n'a pas été consultée ni modifiée ici. Le déploiement automatique sur push vers `main` reste présent : ne pas considérer la revue comme techniquement imposée avant vérification de ces règles.

## Validation et retour arrière de cet incrément

Contrôler localement le YAML, les liens de documentation, les changements de filtres et `git diff --check`. Vérifier en CI, sur une branche hors production, que le lint s'exécute et que le job FTP est ignoré. Aucune commande FTP, application de migration, envoi de mail ou exécution de purge n'est nécessaire à cette préparation.

Résultats locaux du 3 novembre 2026 : YAML et frontmatter valides ; 13 cas de fichiers internes exclus et 51 fichiers applicatifs suivis conservés dans le contrôle des globs ; 114 liens locaux/ancres valides ; `git diff --check` sans erreur. Les parseurs déjà installés ont été utilisés sans ajout de dépendance. La CI GitHub et le transfert réel n'ont pas été exécutés.

Avant tout déploiement autorisé, conserver le code précédent et le journal des changements serveur. Le retour arrière des modifications locales se fait par un patch ciblé rétablissant les versions précédentes des consignes et du workflow ; réexaminer alors le risque de publication des fichiers internes. Les exclusions ajoutées ne suppriment pas de données distantes. Toute restauration de données ou réouverture d'un accès doit suivre une procédure distincte validée.

L'incrément 8 est préparé localement et reste à valider en CI et sur l'infrastructure. Les priorités de confinement de l'incrément 0 et les tests isolés ne sont pas clôturés par cette préparation.

## Complément du 3 novembre 2026 — Confinement local de la purge (R7)

**Étape historique, remplacée par la décision d'abandon ci-dessous.** Un refus HTTP et une règle Apache avaient été préparés. Un mode CLI conditionnel avait été conservé ; il est désormais retiré avec tout le SQL de purge. Voir la [décision de conservation](conservation-donnees.md).

Vérifications locales : `php -l` réussi ; sept cas HTTP refusés avec statut 403 et `Cache-Control: no-store` ; six contextes CLI non autorisés refusés avec code 1 ; entrée CLI autorisée vérifiée avec un PDO factice interrompant avant la première requête. Le serveur PHP temporaire ne contenait que la copie du script et les sondes sans connexion. Le contrôle Apache réel reste à faire. Aucune purge, connexion SQL ou opération distante n'a été exécutée.

Cette étape conservait les critères et instructions SQL historiques. La nouvelle décision supprime ces opérations et demande le retrait des anciens cron sans remplacement. Le traitement des secrets R8 reste ouvert. Aucun état de déploiement n'est établi.

## Décision du 3 novembre 2026 — Abandon complet de la purge

L'utilisateur a demandé de retirer complètement le principe de purge. Les requêtes physiques de l'ancien script et son mode CLI ont été supprimés, ainsi que les nettoyages de l'historique de connexion à l'authentification et dans l'administration. Un fichier inerte avait été préparé, puis le répertoire `scripts/clean/` a été retiré du dépôt au commit `11e3fa1` ; aucun lanceur de remplacement n'est prévu. La [règle de conservation](conservation-donnees.md) remplace les procédures précédentes.

Les tâches cron et lanceurs externes doivent être retirés lors d'une intervention d'hébergement autorisée. L'état distant reste inconnu ; aucune donnée existante n'est supprimée ou restaurée par ce correctif.

## Complément du 3 novembre 2026 — Déconnexion en POST avec CSRF

La navigation soumet un formulaire POST contenant le jeton CSRF. Le script de déconnexion impose HTTPS, refuse les autres méthodes avec HTTP 405 et les jetons invalides avec HTTP 403. Après validation, il vide les données de session, expire le cookie avec ses attributs existants, détruit la session serveur et redirige vers la connexion avec HTTP 303 suivi de `exit()`.

Les trois helpers CSRF existants ont été déplacés sans changement de comportement dans `scripts/csrf/index.php`, inclus par le bootstrap Web et la déconnexion. Ce parcours fonctionne sans bootstrap SQL et ne requiert aucune connexion à la base.

Validation locale : syntaxe des quatre fichiers PHP concernés, `git diff --check`, rendu du formulaire et tests sur fixtures temporaires avec sessions fictives et HTTPS simulé. Les méthodes interdites, jetons absents/invalides/tableaux/d'une autre session, requêtes sans HTTPS, succès par formulaire ou en-tête, destruction serveur, expiration du cookie et rejeu ont été contrôlés. Aucun accès SQL, mail, navigateur de production ou déploiement n'a été utilisé. Les cookies émis par l'hébergement et l'intégration navigateur réelle restent à vérifier.

## Complément du 3 novembre 2026 — Compte actif et rôle courant (T9)

Le bootstrap appelle `refresh_session_account($bdd)` depuis `scripts/auth/index.php` après ouverture du PDO existant et avant les contrôles des pages. Le rôle en cache est supprimé ; pour une session identifiée, une lecture préparée de `lic_compte` vérifie l'ID et `deleted_to IS NULL`, puis fournit le rôle courant. Une session anonyme ne déclenche pas cette lecture et conserve son jeton CSRF.

Un compte absent/supprimé ou un ID de session invalide vide les données de session et régénère son identifiant. Une erreur lors de cette vérification interrompt la requête en HTTP 503, sans utiliser l'ancien rôle ni exposer l'exception. La navigation consomme le rôle vérifié sans requête SQL. L'accueil charge correctement session et bootstrap, avec sortie après redirection. L'inscription contrôle `fetch()` et régénère l'identifiant avant d'ouvrir la session authentifiée.

Validation locale sous PHP CLI 8.5.11 : syntaxe des cinq fichiers PHP concernés et scénarios sur fixtures temporaires, sessions fictives, HTTPS simulé et PDO sans connexion. Accès admin direct, rôle en cache obsolète, promotion/révocation, compte supprimé/inexistant, ID malformé, erreur de lecture sans action protégée, inscription réussie/échec de lecture et connexion avec hachages actuels/historiques ont été vérifiés. Les fixtures n'incorporent pas la configuration PDO réelle et interdisent les écritures administrateur et les mails inattendus.

La vérification s'applique à chaque requête passant par le bootstrap ; elle ne garantit pas qu'un rôle ne puisse changer pendant une requête déjà en cours. Aucun accès à MariaDB, mail réel ou déploiement n'a été effectué. Les tests sur le moteur isolé et l'observation des cookies réellement émis restent nécessaires avant validation d'hébergement.

## Complément du 4 novembre 2026 — Vérification précédente et T11

La demande de poursuivre le backlog avec vérification des tâches précédentes a mis en évidence des reliquats : résultat d'UPDATE de profil non contrôlé, création d'idée modérateur avec réservation non initialisée, droit de modérateur ajouté à un copropriétaire administrateur, transitions de visibilité et suppression de liste sans transaction. Ces points sont corrigés localement. Les propriétaires sont revalidés sous verrou pour les écritures multiples ; les droits existants sont conservés lors de l'attribution aux administrateurs, et les écritures sont annulées en cas d'exception.

Le test `tools/tests/security_regressions.py` passe 53 scénarios de contrôleurs et les contrôles CSRF/HTML/URL avec PDO factice, sans connexion SQL ou mail. Les scénarios de session et d'authentification de T9 ont été rejoués ; les 29 fichiers PHP passent le lint sous PHP 8.5.11. Le pilote MySQL est disponible, mais Docker n'est pas accessible depuis ce terminal et le précédent export SQL n'est plus disponible au chemin fourni.

Le [bilan](verification-backlog-2026-11-04.md) sépare code vérifié, contrôles simulés et validations distantes/SQL restantes. T0, T6 et T8 ne sont pas déclarés terminés ; les moteurs transactionnels, contraintes et deux connexions MariaDB concurrentes restent à valider avant déploiement. Aucun mécanisme de purge n'est réintroduit.
