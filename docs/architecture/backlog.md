# Backlog de modernisation PHP

**Établi le 3 novembre 2026** à partir de l’[audit](audit.md) de `develop` au commit `614eb9c`. Ce backlog est une proposition : aucun ticket ne constitue une autorisation de modification du code. Les dépendances sont des conditions techniques ou de décision ; les éléments P0 peuvent être préparés en parallèle, mais chaque implémentation reste un incrément séparé à valider.

## Lecture et suivi

**Suivi du 3 novembre 2026 :** les statuts initiaux ci-dessous sont conservés comme historique de la proposition. Le [suivi P0](../security/plan-remediation.md#état-observé-et-preuves) consigne les correctifs locaux T1 à T6, T9 à T12 et la CI syntaxique déjà présents, ainsi que la préparation de gouvernance/déploiement. Il détaille les validations restantes ; T7 (purge), T0 (restauration), le durcissement distant T8 et la suite de T13 ne sont pas déclarés terminés.

- **Priorités :** P0 critique ; P1 important ; P2 amélioration. **Tailles :** S, M et L sont relatives et ne sont pas des durées.
- **Statut initial de tous les tickets :** proposé ; validation et réalisation non enregistrées. Ne pas déduire l’état de production de ce statut.
- Après chaque décision ou travail, compléter l’[historique](historique.md) avec date, référence, validation, preuves et résultat des tests, puis mettre à jour le ticket concerné sans effacer son historique.
- Les tests SQL et de sécurité s’exécutent sur un environnement isolé avec données synthétiques. Voir les limites et critères transverses dans le [plan de migration](plan-migration.md#qualité-et-tests-prioritaires).

## Ordre proposé

| Ordre | Ticket | Priorité | Taille | Dépendances principales |
|---|---|---|---|---|
| 0 | [T0 — Restauration](#t0--sauvegarde-et-restauration-isolée) | P0 | M | Accès et capacités d’hébergement |
| 1 | [T1 — Profil](#t1--cibler-le-profil-par-id-de-session) | P0 | S | Deux comptes de test ; T0 avant déploiement |
| 2 | [T2 — Permissions de listes](#t2--protéger-les-écritures-de-liste) | P0 | M | Matrice de droits ; T0 avant déploiement |
| 3 | [T3 — Réservations](#t3--sécuriser-les-réservations) | P0 | M | Rôles autorisés et base isolée |
| 4 | [T4 — Édition des idées](#t4--restreindre-lédition-des-idées) | P0 | S | Matrice de droits |
| 5 | [T5 — ID d’idée](#t5--remplacer-le-ciblage-par-nom) | P0 | S | Jeux de noms identiques |
| 6 | [T6 — Récupération du mot de passe](#t6--réparer-la-récupération-du-mot-de-passe) | P0 | L | T0, schéma additif, courrier de test |
| 7 | [T7 — Retrait de la purge](#t7--contrôler-la-purge-physique) | P0 | S/M | Purge abandonnée ; vérifier retrait et anciens cron |
| 8 | [T8 — Déploiement](#t8--sécuriser-le-déploiement) | P0 | M/L | T0 et capacités LWS |
| 9 | [T9 — Session](#t9--initialiser-correctement-la-session) | P1 | M | Tests auth et administration |
| 10 | [T10 — CSRF](#t10--ajouter-une-protection-csrf) | P1 | M | T9 |
| 11 | [T11 — Transactions de liste](#t11--rendre-la-création-de-liste-atomique) | P1 | M | T0, T2 et schéma réel |
| 12 | [T12 — Échappement](#t12--fiabiliser-léchappement) | P1 | M | Jeux de données synthétiques |
| 13 | [T13 — CI et autoload](#t13--ajouter-ci-et-autoload) | P1 | M | T8 pour déployer l’artefact |
| 14 | [T14 — Lecture des listes](#t14--extraire-la-lecture-des-listes) | P1 | M | T13 et tests de caractérisation |
| 15 | [T15 — Interface](#t15--créer-tokens-et-composants-ui) | P2 | M | Règles stabilisées, maquettes retenues |
| 16 | [T16 — Fonctions futures](#t16--spécifier-les-fonctions-futures) | P2 | M | Décisions produit et capacités LWS |

## Tickets

### T0 — Sauvegarde et restauration isolée

- **Description :** inventorier sauvegardes fichiers et base, procédure et fenêtre de perte admissible ; restaurer une copie isolée avant déploiement ou migration. **P0 · M.**
- **Périmètre :** infrastructure et données de test ; ne pas présumer que l’offre LWS fournit une capacité particulière.
- **Dépendances :** intervention du propriétaire de l’hébergement.
- **Acceptation / tests :** fichiers et base restaurés et vérifiés hors production ; isolation et procédure de reprise documentées ; aucune donnée réelle inutile dans les tests ou journaux.

### T1 — Cibler le profil par ID de session

- **Description :** corriger les deux UPDATE du [profil](../../pages/compte/index.php#L37) pour cibler l’ID de session actif ; traiter la collision de nouvelle adresse et le résultat de l’écriture. **P0 · S.**
- **Dépendances :** deux comptes synthétiques ; T0 avant déploiement. Première modification proposée, pas encore validée.
- **Acceptation :** A ne peut jamais modifier B en soumettant son adresse ; A peut modifier son propre compte avec le mot de passe requis ; erreurs et collisions ne produisent pas de faux succès.
- **Tests :** modification légitime, adresse de B, collision, mauvais mot de passe, changement de mot de passe puis connexion, formats historiques et caractères spéciaux, compte supprimé, erreur SQL, `php -l` et `git diff --check`.
- **Retour arrière :** ancien artefact compatible avec le schéma, mais réintroduisant le défaut ; privilégier un correctif ciblé si possible. Routes et hachages existants restent conservés.

### T2 — Protéger les écritures de liste

- **Description / fichiers :** exiger propriétaire et liste active avant toute suppression, modification, attribution, retrait ou changement de rôle dans [modif-listes](../../pages/modif-listes/index.php#L15). **P0 · M.**
- **Dépendances :** matrice de droits ; T0 avant déploiement. Décider la protection du dernier propriétaire.
- **Acceptation / tests :** propriétaire autorisé ; modérateur, lecteur et sans droit refusés pour les actions interdites ; IDs d’une autre liste et rôles forgés refusés ; liste supprimée et plusieurs propriétaires testés.

### T3 — Sécuriser les réservations

- **Description / fichiers :** dans [idees](../../pages/idees/index.php#L18), vérifier identité, autorisation, lien idée/liste et état actif avant toute écriture ; réserver et annuler par opérations conditionnelles atomiques. **P0 · M.**
- **Dépendances :** décision sur les rôles et les règles de confidentialité ; base SQL isolée.
- **Acceptation / tests :** seul un utilisateur autorisé réserve une idée disponible ; seul le réservataire annule ; refus d’une idée achetée, supprimée, d’une autre liste ou déjà réservée ; deux connexions concurrentes donnent une seule réussite ; confidentialité des propriétaires vérifiée.

### T4 — Restreindre l’édition des idées

- **Description / fichiers :** remplacer le test « non propriétaire » par des rôles explicitement autorisés dans [modif-idees](../../pages/modif-idees/index.php#L191), en vérifiant aussi la liste parente active. **P0 · S.**
- **Dépendances :** matrice des droits.
- **Acceptation / tests :** POST lecteur et utilisateur sans droit refusés ; propriétaire et modérateur traités selon règle validée ; rôle inconnu et parent supprimé refusés.

### T5 — Remplacer le ciblage par nom

- **Description / fichiers :** employer l’ID d’idée autorisée pour trouver la liste, annuler la réservation et notifier dans [modif-idees](../../pages/modif-idees/index.php#L122). **P0 · S.**
- **Dépendances :** deux listes avec idées homonymes et réservataires distincts.
- **Acceptation / tests :** seule l’idée ciblée change ; destinataire et lien correspondent à cette idée ; une erreur intermédiaire ne produit pas d’effet incohérent selon la transaction retenue.

### T6 — Réparer la récupération du mot de passe

- **Description / fichiers :** dans [reset-password](../../pages/reset-password/index.php#L18), stocker séparément un jeton haché, expirant et à usage unique ; préserver le mot de passe jusqu’à validation et retourner une réponse générique. **P0 · L.**
- **Dépendances :** T0, schéma réel, migration additive, transport mail de test et décision sur les demandes déjà en cours.
- **Acceptation / tests :** ancien mot de passe valide après demande et échec d’envoi ; jeton valide change le bon compte une seule fois ; expiration, altération, rejeu, compte supprimé et erreur SQL refusés ; aucun faux succès ni fuite d’existence de compte.

### T7 — Contrôler la purge physique

**Décision du 3 novembre 2026 :** purge complètement abandonnée sur demande de l'utilisateur. Après une première préparation de confinement, les requêtes de purge et le mode CLI ont été retirés, ainsi que le nettoyage automatique et administrateur de l'historique de connexion. Voir la [règle de conservation](../security/conservation-donnees.md) et le [suivi](../security/plan-remediation.md#décision-du-3-novembre-2026--abandon-complet-de-la-purge). La proposition initiale de maintenance ci-dessous est remplacée par cette décision.

- **Description / fichiers actualisés :** retirer tout traitement de purge de [clean](../../scripts/clean/index.php), du bootstrap, de la connexion et de l'administration. **P0 · S/M.**
- **Dépendances :** faire supprimer les anciens cron/lanceurs lors d'une intervention d'hébergement autorisée ; aucun remplacement CLI.
- **Acceptation / tests :** ancienne route sans SQL ni inclusion, appels de nettoyage absents et bouton retiré ; aucun contexte HTTP ou CLI ne permet une purge. Vérifier syntaxe et déploiement sans toucher aux données.

### T8 — Sécuriser le déploiement

- **Description / fichiers :** construire un artefact minimal, vérifier FTPS et certificat, protéger les fichiers privés et documenter le retour arrière ; [workflow actuel](../../.github/workflows/deploy.yml#L1). **P0 · M/L.**
- **Dépendances :** T0 ; capacités et document root LWS vérifiés ; toute modification du workflow est une modification de production.
- **Acceptation / tests :** transfert chiffré vérifié, certificat invalide refusé, fichiers privés inaccessibles, anciennes routes fonctionnelles, déploiement et retour arrière exercés en préproduction ; rotation FTP coordonnée après sécurisation.

### T9 — Initialiser correctement la session

- **Description / fichiers :** configurer la session avant démarrage ; résoudre le compte actif et le rôle sans dépendre de [navbar](../../widgets/navbar/index.php#L26). **P1 · M.**
- **Dépendances :** tests d’authentification et administration.
- **Acceptation / tests :** attributs de cookie observés corrects, session régénérée aux transitions appropriées, accès admin direct fonctionnel selon rôle, révocation et compte supprimé pris en compte.

### T10 — Ajouter une protection CSRF

- **Description / fichiers :** protéger les formulaires et [endpoint de notification](../../scripts/mark-notif-read/index.php) ; migrer la déconnexion vers POST. **P1 · M.**
- **Dépendances :** T9 et inventaire des actions modifiant l’état.
- **Acceptation / tests :** jetons absents ou invalides refusés, jeton valide accepté ; session expirée, AJAX et déconnexion testés.

### T11 — Rendre la création de liste atomique

- **Description / fichiers :** employer `lastInsertId()` sur la même connexion et une transaction pour la [liste et ses autorisations](../../pages/modif-listes/index.php#L125). **P1 · M.**
- **Dépendances :** T0, T2, schéma et moteur réels.
- **Acceptation / tests :** créations simultanées associées aux bons propriétaires ; copropriétaires et administrateurs corrects ; échec à chaque étape sans état partiel.

### T12 — Fiabiliser l’échappement

- **Description / fichiers :** échapper les sorties de formulaires et d’[impression](../../pages/print-liste/index.php#L83), valider les URL et préserver la lecture des entités historiques. **P1 · M.**
- **Dépendances :** jeux synthétiques représentatifs ; comportement historique caractérisé.
- **Acceptation / tests :** accents, entités, guillemets et balises affichés correctement ; schémas d’URL interdits refusés ; pas d’injection dans les cas couverts.

### T13 — Ajouter CI et autoload

- **Description / fichiers :** introduire Composer, PSR-4, PHPUnit, PHPStan et contrôle de style progressif sans reformater tout le dépôt. **P1 · M.**
- **Dépendances :** T8 avant publication du nouvel artefact.
- **Acceptation / tests :** syntaxe et tests en CI, dépendances verrouillées, autoload d’une route pilote, artefact de production compatible avec PHP/extensions effectifs.

### T14 — Extraire la lecture des listes

- **Description / fichiers :** extraire repository et template de [listes](../../pages/listes/index.php#L43), en conservant la route. **P1 · M.**
- **Dépendances :** T13 ; tests de caractérisation et règle de visibilité documentée.
- **Acceptation / tests :** mêmes résultats et liens pour les listes accessibles, plusieurs propriétaires et le comportement public validé ; aucun changement de règles implicite.

### T15 — Créer tokens et composants UI

- **Description / fichiers :** étendre [style.css](../../css/style.css), créer des composants PHP et moderniser une page pilote. **P2 · M.**
- **Dépendances :** règles stabilisées et maquettes retenues.
- **Acceptation / tests :** affichage responsive et thèmes ; clavier, contraste, zoom, erreurs et fonctionnalités de la page vérifiés ; pas de déclaration de conformité RGAA sans audit dédié.

### T16 — Spécifier les fonctions futures

- **Description :** définir séparément archivage métier, images, signalement, PWA et Web Push, sans groupes familiaux. **P2 · M.**
- **Dépendances :** décisions produit, stockage, conservation et capacités LWS.
- **Acceptation / tests :** critères fonctionnels et sécurité propres, stratégie de données, règles de confidentialité, conditions de déploiement et retour arrière documentés avant développement.
