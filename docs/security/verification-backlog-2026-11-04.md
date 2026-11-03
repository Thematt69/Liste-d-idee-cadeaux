# Vérification du backlog — 4 novembre 2026

Référence initiale : `db6648d`, arbre de travail propre. Demande : vérifier les tickets précédents et poursuivre le prochain correctif selon le backlog. Cette revue porte sur le code local et les contrôles décrits ci-dessous ; elle ne confirme pas la version déployée.

## Résultat

Toutes les tâches précédentes ne sont pas clôturées. Les corrections locales de ciblage et d'accès sont présentes, mais des reliquats ont été trouvés dans T1, T2/T11 et T4 puis corrigés dans cet incrément. Les validations de restauration, d'hébergement et de SQL réel restent distinctes. La purge est abandonnée, conformément à la décision de l'utilisateur.

| Ticket | État du code et preuve examinée | Contrôles et validations restantes |
| --- | --- | --- |
| T0 — Restauration | Aucun résultat de restauration vérifiée disponible dans le dépôt. | Intervention du responsable, sauvegarde et restauration isolée nécessaires. Non clôturé. |
| T1 — Profil | Les deux UPDATE ciblent l'ID en session et un compte actif ; collision contrôlée. Le succès était annoncé sans contrôler le résultat de l'UPDATE : corrigé par prise en compte de `rowCount()`. | Tests des deux branches, ciblage d'un autre compte, collision, mauvais mot de passe, compte supprimé et écriture sans effet avec PDO factice. Unicité/concurrence réelles des adresses à vérifier sur le schéma réel. |
| T2 — Droits de listes | Propriétaire actif et correspondance GET/POST avant écriture ; IDs de droits bornés à la liste. Suppression et visibilité étaient encore partiellement écrites en cas d'échec : transaction et revalidation verrouillée ajoutées. | Refus lecteur, IDs croisés, perte de droit avant verrouillage et rollback vérifiés par fixtures. Tests SQL de toutes les opérations de partage et de concurrence restants. |
| T3 — Réservations | UPDATE conditionnel unique, idée/listes/comptes actifs, droit lecteur et réservataire contrôlés. | Tests de réservation/annulation, idée d'une autre liste, états supprimé/acheté/réservé et rôle interdit avec PDO factice. Deux connexions MariaDB concurrentes non testées. |
| T4 — Édition d'idées | Édition limitée au propriétaire/modérateur et parent actif dans les requêtes. Création : résultat d'autorisation désormais vérifié et réservation du modérateur initialisée même sans case cochée. | Créations propriétaire/modérateur, sans droit et lecteur vérifiées. Édition/suppression relues statiquement ; tests SQL et suppression concurrente du parent restants. |
| T5 — Ciblage d'idée | Ciblage par ID et réservation annulée sur l'idée ciblée ; verrouillage/transaction dans la branche d'achat propriétaire. Aucun UPDATE par nom retrouvé. | Revue statique. Homonymes et échecs SQL à rejouer sur base fictive. Les notifications et mails ne sont pas une opération atomique avec la modification SQL ; leur délivrance n'est pas garantie par ce correctif. |
| T6 — Récupération | Stockage du hachage du jeton séparé du mot de passe ; expiration UTC, verrouillage, invalidation après succès, réponse générique et suppression du jeton si l'envoi échoue. Le mail indique expiration/usage unique et désactive la copie webmaster via le paramètre prévu. | Revue statique et migration additive présente. Migration appliquée non établie ; parcours mail, expiration/rejeu et échecs SQL à tester en environnement isolé. Non clôturé. |
| T7 — Purge | `scripts/clean/`, helper, appel de connexion et bouton admin absents ; aucune purge réintroduite. | Retrait local confirmé. Suppression des anciennes copies, cron et lanceurs distants à faire confirmer ; aucune maintenance de remplacement prévue. |
| T8 — Déploiement | Lint préalable, branche `main`, sérialisation, exclusions internes et permissions minimales présents. FTPS explicite, artefact minimal et environnement protégé absent du YAML. Secrets applicatifs encore dans le code. | Les exclusions seules ne protègent pas d'anciennes copies publiées. FTPS/certificat, GitHub/LWS, rotation, sauvegardes et retour arrière non validés. Non clôturé. |
| T9 — Sessions | Compte actif et rôle rechargés avant actions, widget sans SQL ; accueil et régénérations corrigés. | Scénarios de compte/rôle, panne, inscription et connexion actuelle/historique rejoués avec PDO factice. Cookies/TLS réels, PHP de production et MariaDB à valider. |
| T10 — CSRF | Inventaire des formulaires POST, helpers partagés, vérification centrale et en-tête AJAX ; déconnexion POST avec validation avant destruction. | Helpers rejoués ; code de déconnexion relu et résultats des fixtures HTTP précédentes consignés. Intégration AJAX, navigateur et cookies de l'hébergement restante. |
| T11 — Transactions de liste | Création avec `lastInsertId()` dans une transaction. Second droit de modérateur possible pour un copropriétaire admin : corrigé ; seuls les administrateurs actifs sans droit reçoivent un droit. | Copropriétaire admin, plusieurs admins, compte supprimé, mauvais ID d'insertion, échecs intermédiaires et neuf transitions de visibilité vérifiés avec PDO factice. Moteurs transactionnels, index et concurrence réels restent à valider. |
| T12 — Échappement | Sorties de formulaires/impression relues ; helpers HTML et URL HTTP(S) présents. | Entités historiques, guillemets/balises, UTF-8 invalide, URL dangereuses et URL héritées testés. Cette vérification ciblée ne constitue pas un audit XSS exhaustif ni un test navigateur. |

T13 n'est pas terminé : lint CI et tests ciblés existent, Composer/PSR-4/PHPUnit/PHPStan restent à introduire dans un incrément dédié. T14 à T16 ne sont pas déclarés implémentés. Les limites T0/T8 restent des prérequis à toute publication, pas une autorisation de modifier l'hébergement.

## Correctif de cet incrément

Dans [modif-listes](../../pages/modif-listes/index.php), l'ajout des administrateurs conserve les droits existants, dont ceux des propriétaires/copropriétaires. Les transitions depuis le privé traitent plusieurs administrateurs sans sous-requête scalaire. Suppression et visibilité revalident le propriétaire sous verrou puis réalisent leurs écritures dans une transaction avec rollback sur exception ; l'ID verrouillé doit correspondre à celui autorisé initialement. La création conserve `lastInsertId()` et refuse aussi une valeur invalide.

Dans [compte](../../pages/compte/index.php), une écriture sans effet n'annonce plus une modification réussie ; la régénération de session après nouveau mot de passe suit une écriture effective. Dans [modif-idees](../../pages/modif-idees/index.php), une création sans rôle autorisé est refusée et une création modérateur sans réservation fournit explicitement `NULL`.

## Contrôles reproductibles

Commande locale : `python tools/tests/security_regressions.py`. Le script utilise uniquement la bibliothèque standard Python, PHP CLI et des fichiers temporaires. Il n'inclut jamais la configuration PDO applicative, ne crée aucune connexion SQL, ne charge pas le transport mail et n'exécute pas Graphify.

Résultats : 53 scénarios de contrôleurs et contrôles CSRF/HTML/URL réussis. Les fixtures vérifient les paramètres, gardes et transactions demandés par le code en simulant un état en mémoire ; elles ne prouvent pas l'exécution du SQL, les verrous du serveur ou le rollback d'un moteur non transactionnel. Les scénarios HTTP de T9 ont aussi été rejoués avec sessions fictives, HTTPS simulé et PDO sans connexion.

Les 29 fichiers PHP passent `php -l` sous PHP CLI 8.5.11 ; le pilote `pdo_mysql` est disponible. La commande Docker n'est pas disponible dans ce terminal et l'export SQL précédemment fourni n'est plus accessible au chemin indiqué. La mise en place Docker/base isolée reste en pause ; aucun service réel n'a été contacté. La CI cible PHP 8.2.35 mais aucune nouvelle exécution distante n'est confirmée ici.

Contrôle final : diff relu, 61 liens locaux/ancres valides, inventaire des champs CSRF des formulaires POST, retrait de la purge confirmé et `git diff --check` sans erreur. Les changements restent locaux, sans migration, commit, push ou déploiement. Le retour arrière du code ne rétablit pas des données ; aucune donnée réelle n'a été modifiée pendant ces vérifications.
