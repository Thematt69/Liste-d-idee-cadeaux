# Plan de migration progressive PHP

**Décision du 3 novembre 2026 :** la purge physique est abandonnée sur demande de l'utilisateur. Les anciennes étapes de confinement/maintenance doivent désormais aboutir au retrait des mécanismes et des tâches externes, selon la [règle de conservation](../security/conservation-donnees.md). Les propositions initiales ci-dessous restent historiques.

**Proposition du 3 novembre 2026**, issue de l’[audit](audit.md) de `develop` au commit `614eb9c`. Le plan n’est pas encore validé. L’[architecture cible](architecture-cible.md) décrit les couches ; le [backlog](backlog.md) détaille les tickets ; l’[historique](historique.md) suit les décisions.

## Règles

1. Une action ou route à la fois ; conserver le comportement sauf correction explicitement validée.
2. Conserver comptes, mots de passe, données, relations, liens et routes ; aucune transformation destructive de la base de production.
3. Séparer sécurité, architecture et nouvelles fonctionnalités autant que possible ; migrations SQL additives et compatibles.
4. La phase d’implémentation attend la validation explicite du plan. Ensuite, proposer un incrément, attendre sa validation avant modification et attendre une nouvelle validation avant le suivant.
5. Aucun commit, push ou déploiement sans demande explicite. Une documentation créée ne vaut pas validation du plan.

## Prérequis et informations à obtenir

Version réellement déployée ; sauvegardes et restauration isolée testée ; schéma, moteur, index, contraintes et données historiques ; offre LWS, document root et espace privé, FTPS, version PHP, extensions, SSH et cron éventuels ; préproduction et transport mail de test. Les règles métier restant à décider sont dans l’[historique](historique.md#décisions-et-informations-en-attente).

## Phases et dépendances

| Phase | Objectif et actions concrètes | Prérequis et fichiers | Priorité | Résultat attendu |
|---|---|---|---|---|
| 0. Préparation | Vérifier sauvegardes, restauration, protections de purge, secrets, version déployée et capacités LWS ; isoler les tests. | Propriétaire de l’hébergement ; [clean](../../scripts/clean/index.php), [workflow](../../.github/workflows/deploy.yml). | P0 | Tests et reprise possibles sans supposer l’état de production. |
| 1. Correctifs critiques | Corriger séparément profil, droits de listes et d’idées, réservation, homonymes et récupération. | Préparation opérationnelle avant déploiement ; [tickets T1–T7](backlog.md). | P0 | Écritures critiques contrôlées côté serveur. |
| 2. Protections transverses | Initialiser la session avant démarrage ; compte actif, CSRF et échappement prioritaire. | Tests d’authentification ; [verif](../../scripts/verif/index.php), pages et widgets. | P1 | Protections et identité cohérentes. |
| 3. Intégrité | `lastInsertId()`, transactions, écritures conditionnelles ; examiner les données avant ajout de contraintes. | Schéma réel, restauration et tests SQL concurrents. | P1 | Pas d’état partiel pour les actions couvertes. |
| 4. Fondation | Composer, PSR-4, CI, configuration privée et bootstrap transitoire. | Artefact et emplacement privé testés ; pas de Composer serveur supposé. | P1 | Nouvelles classes appelables depuis les routes existantes. |
| 5. Extraction | Extraire lecture des listes, permissions, idées/réservations, comptes puis notifications, une route à la fois. | Tests de caractérisation et phase 4 ; correctifs concernés. | P1 | Règles testables et pages progressivement simplifiées. |
| 6. Interface | Templates PHP, tokens et composants ; adapter progressivement les pages. | Contrats stabilisés, maquettes pertinentes et tests de rendu. | P2 | UI responsive moderne sans changement métier implicite. |
| 7. Fonctions futures | Images, archivage métier, signalement et PWA comme tickets séparés. | Décisions produit, stockage et exploitation validés. | P2 | Évolutions indépendantes de la refactorisation. |

## Validation et retour arrière par phase

| Phase | Risques et tests | Critère d’acceptation | Retour arrière |
|---|---|---|---|
| 0 | Sauvegarde inutilisable ou environnement mal isolé ; restaurer une copie. | Restauration fichiers/base réellement vérifiée hors production. | Aucun changement applicatif ; suspendre le déploiement si la restauration est incertaine. |
| 1 | Blocage d’un usage légitime ; tester rôles, anciens liens et POST directs. | Cas autorisés réussissent, accès interdits échouent, autres données inchangées. | Artefact précédent si compatible, sachant qu’il réintroduit le risque ; ne pas restaurer automatiquement la base. |
| 2 | Session ou AJAX cassé ; tester cookies, expiration, onglets et notifications. | Parcours fonctionnels ; requêtes sans CSRF refusées. | Revenir au code et à la configuration testés, en considérant les sessions actives. |
| 3 | Verrous et contraintes inattendues ; tester concurrence et erreurs injectées. | Aucune action partielle sur les cas couverts. | Ancien code compatible ; conserver les ajouts de schéma jusqu’à analyse. |
| 4 | Exposition de fichiers privés ou autoload absent ; tester artefact et accès directs en préproduction. | Routes historiques actives, fichiers privés refusés, artefact reproductible. | Redéployer l’artefact précédent sans effacer la configuration privée. |
| 5 | Différence cachée entre ancien et nouveau traitement ; comparer sur jeux synthétiques. | Une route migrée passe ses tests et conserve ses URL. | Rebrancher l’ancien traitement s’il reste compatible avec le schéma. |
| 6 | Régression mobile, clavier, confidentialité ou impression. | Page et rôles validés sur les formats retenus. | Restaurer templates et CSS précédents. |
| 7 | Nouveaux flux de données et coûts d’exploitation. | Critères et tests propres à la fonctionnalité. | Désactiver la fonction et conserver ses données selon procédure documentée. |

## Qualité et tests prioritaires

Contrôles obligatoires proposés : `php -l` sur fichiers concernés puis tous les PHP en CI ; tests ciblés des réussites et refus ; tests SQL isolés pour écritures critiques ; `git diff --check` ; analyse statique et style progressivement sur le nouveau code ; contrôle des anciennes routes, de l’artefact et du rollback. PHPUnit, PHPStan et PHP_CodeSniffer constituent une combinaison légère ; Pest ou PHP-CS-Fixer sont des alternatives, pas des outils à cumuler par principe.

**Permissions :** propriétaire A, lecteur B, utilisateur C sans droit et autre liste de D ; tester POST directs, IDs croisés, rôle forgé, compte ou liste supprimés, copropriétaires et dernier propriétaire selon les règles validées.

**Réservations :** seule une personne autorisée réserve une idée active non achetée ; seul le réservataire annule ; deux connexions concurrentes aboutissent à une seule réussite ; idées homonymes indépendantes ; confidentialité du propriétaire dans affichage et impression selon la règle validée.

## Déploiement et retour arrière

Le workflow actuel synchronise vers le serveur après push sur `main` ou déclenchement manuel. Il ne montre pas de tests ni de construction Composer préalable. Une synchronisation FTP fichier par fichier n’est pas supposée atomique.

1. Construire en CI, à partir du commit et du futur `composer.lock`, un artefact ne contenant que les fichiers nécessaires ; utiliser `composer install --no-dev --optimize-autoloader` lorsque Composer sera introduit. Voir la [documentation Composer](https://getcomposer.org/doc/03-cli.md).
2. Vérifier version et extensions effectives, FTPS et certificat, refus d’accès aux chemins privés, compatibilité des routes et emplacement des secrets.
3. Conserver l’artefact précédent et exercer le retour arrière en préproduction ; en l’absence de basculement atomique, concevoir les versions pour coexister pendant le transfert.
4. Avant migration SQL, sauvegarder et restaurer une copie isolée ; privilégier ajouts compatibles, contrôler comptages et relations, conserver anciennes structures jusqu’à décision explicite.
5. Ne pas restaurer automatiquement une ancienne base pour annuler un déploiement : cela peut effacer les données créées depuis. La restauration complète relève d’une reprise d’incident et d’une décision sur la perte de données.

## Premier incrément recommandé

**T1 — Cibler le profil par ID de session.** Défaut localisé et risque élevé ; périmètre limité à [compte](../../pages/compte/index.php#L15) et aux tests nécessaires. Vérifier compte actif, collision d’adresse et résultat ; conserver routes, base et hachages. Les anciens formats et caractères spéciaux doivent être caractérisés avant changement de comportement. Les critères précis et le rollback figurent dans [T1](backlog.md#t1--cibler-le-profil-par-id-de-session).
