# Historique des audits, décisions et validations

Journal associé à l’[audit](audit.md), à l’[architecture cible](architecture-cible.md), au [plan](plan-migration.md) et au [backlog](backlog.md). Il est destiné à une reprise sans accès à la conversation.

## Règles de conservation

Ajouter une entrée datée après chaque audit, validation, changement important ou incrément, avec branche/commit, preuves et tests lorsqu’ils sont connus. Distinguer proposition, décision explicite, validation et réalisation. Ne jamais supprimer une ancienne décision : indiquer quelle nouvelle entrée la remplace et pourquoi. Avant de remplacer une version documentaire présente, la copier sous `archives/` avec une date et un nom explicites ; conserver les versions antérieures et ne pas y exporter de secrets, jetons ou données personnelles. Mettre à jour les autres documents concernés et les statuts du backlog sur preuve.

## 3 novembre 2026 — Audit initial et proposition

**Référence examinée :** branche `develop`, commit `614eb9c`. **Méthode :** lecture statique locale, sans contact avec la production ni test sur données réelles.

### Décisions et contraintes explicitement fournies

- Viser l’option D : PHP natif structuré, sans framework complet imposé et sans réécriture complète.
- Conserver comptes, mots de passe, listes, idées, autorisations, réservations, relations et données archivées ; aucune transformation destructive de la base de production.
- Maintenir la disponibilité, les routes et liens autant que possible ; prévoir déploiements contrôlables et retour arrière.
- Les groupes familiaux sont définitivement abandonnés ; les anciennes maquettes correspondantes sont exclues.
- La PWA Android est une évolution optionnelle, distincte de la modernisation visuelle.
- L’implémentation ne commence qu’après validation explicite du plan ; ensuite, chaque incrément de code exige une validation avant modification et une nouvelle validation avant le suivant. Pas de commit, push ni déploiement sans demande explicite.

### Résultats et statut

Les risques principaux concernent le profil, les permissions de listes et d’idées, les réservations, la récupération du mot de passe, une écriture par nom non unique, une purge physique potentiellement accessible et le transport de déploiement. Ils sont détaillés dans l’[audit](audit.md#registre-des-risques), avec leurs conditions et limites.

**Recommandation :** corriger les risques prioritaires par incréments indépendants, puis extraire progressivement services, repositories, contrôleurs et templates. Premier incrément proposé : [T1, profil par ID de session](backlog.md#t1--cibler-le-profil-par-id-de-session).

**Validation :** le plan et T1 ont été proposés ; aucune validation explicite de leur implémentation n’est enregistrée. Aucun changement applicatif, migration, action sur des données, branche, commit, push ou déploiement n’a été réalisé au titre de cet audit.

**Vérifications :** 27 fichiers PHP ont passé `php -l` avec PHP 8.5.11 local. Aucun test fonctionnel, SQL, d’accès réel, visuel ou en production. Le graphe Graphify existant était associé à un commit antérieur et sa commande locale a échoué. Schéma SQL, configuration serveur, sauvegardes et état de production non vérifiés.

## 3 novembre 2026 — Premier export documentaire

**Demande explicite :** créer quatre documents dans `docs/architecture/` pour conserver l’audit, l’architecture proposée, le plan et cet historique, sans modifier code, configuration ou données. Cette demande ne vaut pas validation du plan.

**Résultat alors annoncé :** création de `audit.md`, `architecture-cible.md`, `plan-migration.md` et `historique.md` ; vérification des liens de fichiers et de `git diff --check`. Les fichiers étaient alors non suivis par Git et aucun commit n’a été créé.

## 3 novembre 2026 — Reconstitution et export documentaire

**Nouvelle demande explicite :** disposer de six documents versionnables, dont un index et un backlog opérationnel, avec historique et preuves. Aucun changement applicatif, de configuration, de dépendances ou de données n’est autorisé.

**Constat :** au début de cette intervention, `docs/architecture/` était vide et Git ne signalait aucun fichier documentaire suivi ou non suivi. Les quatre documents annoncés précédemment n’étaient donc pas disponibles sur disque ; la cause de leur absence est inconnue. Aucune ancienne copie présente n’a été écrasée, et aucune archive exacte de ces fichiers absents ne peut être produite depuis le dossier.

**Action documentaire :** reconstitution des conclusions et décisions à partir des résultats conservés dans l’échange ; création de [l’index](README.md), [l’audit](audit.md), [l’architecture cible](architecture-cible.md), [le plan](plan-migration.md), [le backlog](backlog.md) et du présent journal. La feuille de route et les tickets sont désormais séparés pour faciliter le suivi. Cette reconstitution n’est pas une validation de l’architecture ni un nouvel audit de production.

## Décisions et informations en attente

Renseigner les réponses dans une nouvelle entrée ; ne pas transformer une recommandation en décision sans validation explicite.

1. **Plan et T1 :** validation générale, puis autorisation et périmètre exact du premier incrément.
2. **Partage :** listes publiques énumérées ou accessibles seulement par lien ; conséquences d’un changement de visibilité sur anciens liens et droits.
3. **Rôles :** pouvoirs des propriétaires, modérateurs et lecteurs ; copropriétaires, dernier propriétaire et accès automatique des administrateurs.
4. **Réservations :** qui réserve, annule et voit l’identité ; effets d’une modification ou suppression d’idée réservée.
5. **Conservation :** suppression versus archivage, restauration, durées et conséquences d’une suppression de compte sur les relations.
6. **Courriels :** politique de copie cachée, notamment pour les liens sensibles.
7. **Infrastructure :** offre et panneau LWS, document root, espace privé, FTPS, version et extensions PHP, SSH, cron, préproduction, sauvegarde et restauration.
8. **Base :** schéma, moteur, contraintes, index, doublons et état des données historiques.
9. **Instructions d’agent :** les instructions Copilot ciblées actuelles déconseillent de nouvelles couches de services ; si le plan est validé, prévoir leur adaptation dans un incrément distinct.

## 3 novembre 2026 — Incrément 8, gouvernance et préparation du déploiement

**Autorisation :** l'utilisateur a demandé l'implémentation du plan P0, puis les incréments successifs, et a fourni à nouveau le plan en demandant le correctif suivant. Cette entrée complète les validations initialement en attente ci-dessus. Elle n'autorise pas un commit, push, déploiement, changement de secrets, migration ou action sur la production.

**Référence locale :** `764483d` au début de l'intervention, arbre de travail propre. Les correctifs applicatifs, CSRF, sessions, URL et le lint CI sont présents dans l'historique Git ; le [suivi P0](../security/plan-remediation.md) en donne les références. Le serveur déclaré dans l'échange est MariaDB 10.11.19 avec PHP 8.2.35 ; la mise en place de la base Docker locale est en pause. Aucun état de déploiement ou de migration n'en est déduit.

**Préparation locale :** création de la [politique opérationnelle](../security/politique-agents.md) et du [plan de remédiation](../security/plan-remediation.md), harmonisation d'AGENTS/Copilot/Cline sur la session, HTTPS et les effets d'exécution ; ajout de compléments à l'audit et au backlog sans supprimer les constats historiques. Le README décrit les mécanismes sans prétendre à une couverture systématique.

**Workflow :** filtres de publication des fichiers internes, contrôle de branche `main` pour le job FTP, sérialisation des transferts et retrait de la persistance des identifiants de checkout. Le lint conserve PHP 8.2.35 avec un montage en lecture seule et un délai de dix minutes. La configuration des approbateurs, protections GitHub, FTPS et fichiers déjà publiés reste à vérifier avec le responsable. Le déclenchement automatique sur `main` reste présent.

**Vérification :** YAML du workflow et frontmatter des trois consignes PHP/sécurité validés avec le parseur YAML de Prettier déjà installé ; 13 cas de fichiers internes exclus et les 51 fichiers applicatifs suivis conservés par les motifs glob contrôlés avec minimatch ; 114 liens locaux et ancres vérifiés ; `git diff --check` sans erreur. Relecture indépendante du diff sans blocage identifié. Aucun fichier PHP modifié. La CLI Graphify a de nouveau échoué au lancement ; aucune reconstruction ni installation n'a été tentée. Aucun test applicatif/SQL, exécution GitHub Actions, transfert FTP, purge, migration ou envoi de mail n'a été exécuté.

**État :** préparation locale de l'incrément 8, validation CI et infrastructure restantes. La purge, les secrets et les tests isolés restent prioritaires et non clôturés. Retour arrière : patch ciblé des consignes/workflow ; toute opération sur des données ou accès réels exige sa procédure propre.

## 3 novembre 2026 — Confinement de la purge, R7

**Étape historique remplacée par l'abandon complet de la purge consigné dans l'entrée suivante.**

**Autorisation et état initial :** demande de passer au correctif suivant selon le plan P0 ; HEAD `764483d`. Les changements de gouvernance de l'incrément 8 étaient déjà dans l'index et sont préservés. L'entrée de `scripts/clean/index.php` chargeait session et bootstrap SQL sans garde d'accès ; aucun inventaire distant des protections ou cron n'a été effectué.

**Correctif alors préparé :** refus de tout SAPI autre que `cli` avant inclusion et SQL ; règle Apache `Require all denied`. Le script ne chargeait plus le bootstrap Web et ne se connectait plus automatiquement. Un mode CLI exigeait une constante booléenne et le PDO `$bdd` fourni par un lanceur privé. Ces critères, instructions SQL et procédure CLI ont ensuite été retirés selon la [décision de conservation](../security/conservation-donnees.md).

**Vérifications :** `php -l` réussi ; sur copie temporaire isolée, sept refus HTTP 403 sans cache, six refus CLI avec code de sortie 1 et une entrée CLI autorisée simulée avec un PDO sans connexion interrompant avant la première requête. Aucun SQL ni script applicatif partagé exécuté. Les 41 liens locaux/ancres des documents concernés sont valides ; `git diff --check` sans erreur et diff relu. Apache, tâches planifiées, déploiement et fonctionnement SQL de la maintenance non vérifiés.

**État et préservation :** confinement préparé localement uniquement ; aucun changement distant, purge ou donnée modifiée. Les anciens appels HTTP et appels CLI directs doivent être inventoriés avant déploiement, puis remplacés seulement selon une procédure de maintenance autorisée. Les secrets R8 restent à traiter séparément. Un retour arrière éventuel doit conserver un refus HTTP côté serveur avant rétablissement de l'ancien code.

## 3 novembre 2026 — Abandon complet de la purge

**Décision explicite :** « Retire le principe de purge complètement ». Cette demande remplace le confinement avec possibilité de maintenance CLI proposé à l'étape précédente.

**Changements locaux :** retrait de tout le SQL de purge, de son autorisation CLI, du helper de nettoyage de l'historique, de son appel à la connexion, du traitement POST et du bouton administrateur. L'ancienne route est un fichier inerte qui retourne HTTP 410 ou quitte en CLI avec code 1, sans inclusion ni SQL ; la règle Apache de refus est conservée. Les références spécifiques au nettoyage dans l'extracteur Graphify sont retirées sans exécuter l'outil. La procédure de maintenance est remplacée par la [règle de conservation](../security/conservation-donnees.md) ; AGENTS, README, politique, suivi et backlog sont alignés.

**Validation :** `php -l` réussi sur les quatre fichiers PHP modifiés ; syntaxe Python compilée sans exécuter l'extracteur ; anciens appels/fonction/autorisation absents du code actif. Sur une copie temporaire isolée, six cas HTTP retournent 410 et deux cas CLI quittent avec code 1, y compris avec l'ancien contexte autorisé, sans SQL. Les 146 liens locaux/ancres sont valides ; `git diff --check` sans erreur. Aucune donnée réelle ni tâche distante n'est modifiée ; les anciens cron/lanceurs doivent être supprimés par le responsable lors d'une intervention autorisée. Aucun lanceur de remplacement n'est à créer.

## 3 novembre 2026 — Déconnexion en POST avec CSRF

**Autorisation et référence :** demande de passer au correctif suivant ; HEAD `11e3fa1`, arbre de travail propre. L'abandon de la purge est conservé. Ce correctif complète l'incrément 7 / T10.

**Changements :** remplacement du lien de déconnexion par un formulaire POST avec jeton ; exigence HTTPS, refus des méthodes différentes et des jetons invalides avant destruction de session. Le succès vide la session, expire son cookie avec ses attributs, détruit la session serveur et redirige en 303 avec sortie immédiate. Les helpers CSRF sont extraits à l'identique dans `scripts/csrf/index.php` et partagés par le bootstrap et la déconnexion sans accès SQL pour celle-ci.

**Vérifications locales :** `php -l` réussi pour les quatre fichiers PHP concernés ; helpers CSRF comparés à la référence, identiques après extraction ; tests sur copies temporaires des scripts et du widget avec données de session fictives et contexte HTTPS simulé. Formulaire POST et jeton rendus, GET/HEAD/PUT/DELETE refusés sans perdre la session, jetons manquants/invalides/tableaux/croisés refusés, redirection HTTPS préservant POST, succès avec champ ou en-tête, expiration du cookie, destruction du stockage serveur et refus du rejeu vérifiés. Les 49 liens locaux/ancres du suivi concerné sont valides et `git diff --check` passe. Le suivi de conservation a aussi été aligné sur l'absence effective du répertoire `scripts/clean/` dans la référence courante.

**Limites :** aucune connexion SQL ni action distante ; pas de test navigateur ou TLS réel. La vérification des cookies sur l'hébergement et du compte actif/rôle admin reste à effectuer dans les incréments correspondants. Retour arrière éventuel par patch ciblé, en tenant compte du fait que rétablir un lien GET réintroduit le défaut CSRF de déconnexion.

## 3 novembre 2026 — T9, compte actif et rôle avant les actions

**Autorisation et référence :** l'utilisateur demande le correctif suivant selon le backlog ; HEAD `49ca90a`, arbre de travail propre. T9 conserve un reliquat local sur le compte actif et le rôle courant ; les prérequis d'hébergement de T0/T8 restent à valider séparément.

**Correctif :** nouveau helper de session dans `scripts/auth/index.php`, appelé par le bootstrap après initialisation du PDO partagé et avant les contrôles des pages. Le rôle mémorisé est écarté puis relu sur un compte actif ; les comptes absents/supprimés et ID invalides invalident les données de session et régénèrent son identifiant. Une erreur de vérification interrompt la requête en 503 avec un message générique. Le widget de navigation ne charge plus le rôle par SQL. L'accueil utilise les inclusions partagées correctes et sort après redirection. L'inscription vérifie le compte relu avant d'ouvrir une session et régénère son ID ; les formats de mot de passe restent conservés.

**Vérifications :** cinq fichiers PHP passent `php -l` sous PHP 8.5.11. Sur copies temporaires avec configuration SQL réelle exclue, PDO factice et HTTPS simulé : accès admin direct sans rôle préalable, cache périmé, promotion puis révocation, compte supprimé et IDs invalides, panne de lecture sans action protégée, conservation du jeton anonyme, inscription avec changement d'ID et échec de lecture, connexion avec formats actuels/historiques. Aucun SQL réel ni mail exécuté. Les 45 liens locaux/ancres concernés sont valides ; absence de SQL dans la navigation vérifiée ; diff relu et `git diff --check` sans erreur.

**Portée :** changement local uniquement, sans migration ni déploiement. Validation MariaDB isolée, cookies réels et concurrence de changement de rôle pendant une requête restent hors de ces fixtures. Retour arrière par patch ciblé ; rétablir le rôle en cache réintroduirait l'accès périmé. Aucun mécanisme de purge réintroduit.
