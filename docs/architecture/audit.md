# Audit architectural et sécurité PHP

**Audit initial :** 3 novembre 2026 · **Référence :** branche `develop`, commit `614eb9c` · **Méthode :** analyse statique locale, sans contact avec la production. Documents associés : [architecture cible](architecture-cible.md), [plan](plan-migration.md), [backlog](backlog.md), [historique](historique.md).

## Périmètre et niveau de preuve

Un **constat confirmé** est observé dans le code. Une **hypothèse** dépend de l’exécution, des données ou de protections externes. Une **information manquante** ne peut pas être déduite du dépôt ; une **recommandation** est une proposition non encore validée.

L’audit a couvert les entrées HTTP, les parcours de comptes, listes, idées, réservations, notifications, administration et maintenance, les composants partagés, les ressources frontend et le workflow de déploiement. Les 27 fichiers PHP ont passé `php -l` avec PHP 8.5.11 local. Aucun test fonctionnel, SQL, visuel ou en production n’a été effectué. Le graphe Graphify existant, associé à un commit antérieur, a été consulté pour la navigation après l’échec de sa commande locale ; il n’a pas constitué une preuve indépendante.

**Informations manquantes :** schéma et contraintes SQL réels, données et volumes, protections serveur héritées, version déployée, sauvegardes et restauration, paramètres PHP et extensions effectifs, document root, accès privé, FTPS, SSH, cron et offre LWS exacte. Aucun schéma SQL, gestionnaire Composer ou lanceur de tests n’a été trouvé dans les fichiers suivis examinés. Le README du projet renvoie à un fichier Copilot global absent de cette référence.

## Synthèse

L’application est un monolithe PHP procédural rendu côté serveur. Les pages mélangent souvent requêtes, règles métier et HTML. PDO, des requêtes préparées, les sessions, les widgets, Bootstrap et la compatibilité de connexion avec les anciens formats de mots de passe constituent des points d’appui.

Les principaux risques concernent le ciblage du profil, les droits de listes et d’idées, les réservations, la récupération de mot de passe, les opérations par nom non unique, une purge physique potentiellement accessible et le transport de déploiement. Aucun incident ni exploitation réelle n’a été établi. L’option D est faisable si les corrections de sécurité et d’intégrité précèdent la refonte des couches.

## Cartographie

| Fichier / module | Responsabilité, dépendances | Problème observé | Destination proposée |
|---|---|---|---|
| [Accueil](../../index.php) | Redirection selon session. | Inclusion relative à vérifier ; redirections sans sortie immédiate. | Adaptateur de route conservé. |
| [Bootstrap](../../scripts/verif/index.php#L1) | HTTPS, PDO global, échappement et nettoyage des connexions. | Secrets dans le code, configuration de session souvent trop tardive, responsabilités mélangées. | Bootstrap minimal, sécurité, configuration privée et maintenance. |
| [Connexion](../../pages/connexion/index.php#L32), [inscription](../../pages/inscription/index.php#L32), [réinitialisation](../../pages/reset-password/index.php#L18) | Authentification, comptes, récupération, SQL et vues. | Récupération incohérente ; validation/session à renforcer. | Contrôleurs, services de compte, repository et templates. |
| [Profil](../../pages/compte/index.php#L15) | Modification du compte et historique. | Ciblage par adresse soumise. | Service et repository de compte. |
| [Listes](../../pages/listes/index.php#L43), [détail](../../pages/idees/index.php#L18) | Affichage, accès et réservation. | Listes publiques énumérées ; réservation avant contrôle ; SQL dans le rendu. | Contrôleurs, lecture centralisée et service de réservation. |
| [Modification des listes](../../pages/modif-listes/index.php#L15) | Création, partage, permissions et suppression. | Écritures avant contrôle propriétaire ; absence de transaction ; ID obtenu par date. | Services de liste et de permissions. |
| [Modification des idées](../../pages/modif-idees/index.php#L13) | Création, édition, achat, suppression et notifications. | Lecteur accepté dans une branche d’écriture ; ciblage par nom ; effets multiples. | Services d’idées et réservations. |
| [Impression](../../pages/print-liste/index.php#L28) | Lecture autorisée et rendu imprimable. | Requêtes dupliquées et sorties non systématiquement échappées. | Lecture commune et template distinct. |
| [Administration](../../pages/admin/index.php#L8) | Notifications, mails, nettoyage des connexions. | Rôle mis en session par la navigation ; effets partiels possibles. | Contrôleur admin et services. |
| [Notifications](../../scripts/mark-notif-read/index.php#L17), [widget](../../widgets/notif-toast/index.php#L12), [JS](../../js/notifications.js#L9) | Affichage et acquittement. | L’acquittement est lié au compte ; pas de jeton CSRF visible. | Endpoint conservé, service et repository. |
| [Mail](../../scripts/mail/index.php#L2), [nettoyage](../../scripts/clean/index.php#L13) | Courriel via `mail()`, purge physique. | Copie cachée systématique ; purge HTTP sans contrôle applicatif visible. | Adaptateur de mail ; maintenance hors accès public ou strictement protégée. |
| [Navigation](../../widgets/navbar/index.php#L26), [imports](../../widgets/import/index.php#L27), [pied](../../widgets/footer/index.php) | Composants partagés ; la navigation charge aussi le rôle. | Présentation couplée à SQL et session. | Templates et données préparées en amont. |
| Pages légales, accessibilité, plan du site et erreurs | Contenu et erreurs HTTP. | Initialisation commune parfois chargée pour du contenu simple. | Routes maintenues, templates au besoin. |
| [Styles](../../css/style.css#L1), Bootstrap, jQuery, images | Interface responsive et ressources. | Inventaire et versions à fiabiliser. | Ressources publiques et composants progressifs. |
| [Apache](../../.htaccess), [déploiement](../../.github/workflows/deploy.yml#L1), outils et graphe | En-têtes/cache et synchronisation FTP. | Fichiers privés non séparés de façon démontrée ; pas de CI de tests visible. | Artefact minimal et protections serveur vérifiées. |

## Parcours et règles observés

| Parcours | Chemin, règles, effets et limites | Responsabilité future / tests |
|---|---|---|
| Inscription | [Inscription](../../pages/inscription/index.php#L32) cherche une adresse active, insère le compte, relit son ID et ouvre une session ; inscrit une connexion. Régénération non visible sur ce chemin ; unicité SQL inconnue. | Service de compte ; tester doublon, concurrence et session. |
| Connexion | [Connexion](../../pages/connexion/index.php#L32) vérifie le mot de passe brut, puis le format historique encodé ; rehash de ce dernier, régénération de session et historique. | Conserver ce fallback ; tester les deux formats et caractères spéciaux. |
| Mot de passe oublié | [Demande](../../pages/reset-password/index.php#L73) remplace le hachage par celui d’un jeton ; les étapes suivantes comparent le jeton brut à la colonne contenant le hachage. | Jeton séparé et usage unique ; tester échec de mail, expiration, rejeu et conservation de l’ancien mot de passe. |
| Profil | [Compte](../../pages/compte/index.php#L20) vérifie le mot de passe du compte en session mais cible l’UPDATE par adresse fournie. | Écrire par ID de session ; tester deux comptes et la collision d’adresse. |
| Consultation / création de listes | [Mes listes](../../pages/listes/index.php#L43) combine listes autorisées et toutes les listes publiques. [Création](../../pages/modif-listes/index.php#L125) ajoute propriétaire, éventuel copropriétaire et administrateurs. | Décider la règle publique ; utiliser l’ID de l’insertion et tester la concurrence. |
| Visibilité et permissions | [Partage](../../pages/modif-listes/index.php#L63) gère privé, limité et public ; [détail](../../pages/idees/index.php#L58) peut insérer un droit lecteur pour une liste publique. Les `switch` externes peuvent s’enchaîner ; les POST de droits ne contrôlent pas systématiquement le propriétaire. | Service de permissions, transitions explicites et transactionnelles ; tester les anciens liens et plusieurs propriétaires. |
| Idées | [Création](../../pages/modif-idees/index.php#L235) admet propriétaire ou modérateur. [Édition](../../pages/modif-idees/index.php#L191) accepte aussi un lecteur dans une branche ; suppression réservée au propriétaire. | Autorisation explicite par action et parent actif ; tester les POST directs et listes supprimées. |
| Réservation / annulation | [Détail](../../pages/idees/index.php#L18) lit puis modifie `buy_from` avant le contrôle de liste. L’interface masque l’identité du réservataire aux propriétaires dans les vues normale et imprimée. | Écritures atomiques autorisées ; tester concurrence, achat, réservation d’autrui et confidentialité. |
| Notifications / administration | [Administration](../../pages/admin/index.php#L29) crée des notifications et envoie des mails. [Acquittement](../../scripts/mark-notif-read/index.php#L35) est limité au compte courant. | Tester échecs partiels et propriété ; ne pas confondre notifications internes et Web Push. |
| Suppression / archivage | La suppression d’une liste efface ses autorisations puis marque la liste supprimée ; [clean](../../scripts/clean/index.php#L24) purge physiquement les éléments anciens. | Conservation, restauration et différence archivage/suppression à décider avant migration ou purge. |

**Cas limites non démontrés par une exécution :** compte désactivé après ouverture de session, rôle admin devenu périmé, dernier propriétaire retiré, liste ou idée supprimée, ancien lien, réservations simultanées et erreur SQL intermédiaire. Ils doivent être testés sur une base isolée.

## Registre des risques

P0 : risque critique de sécurité, perte de données ou défaut bloquant ; P1 : fiabilité ou maintenance importante ; P2 : amélioration. Les impacts ci-dessous sont déduits du code et des conditions indiquées.

| Priorité | Preuve et problème | Impact / condition | Correction proposée et tests |
|---|---|---|---|
| P0 | [Compte](../../pages/compte/index.php#L31) vérifie le mot de passe courant puis utilise l’adresse soumise dans l’UPDATE. | Un compte connecté connaissant son mot de passe peut cibler un autre compte dont il connaît l’adresse ; contraintes SQL inconnues. | ID de session et compte actif ; tests à deux comptes, collision et formats historiques. |
| P0 | [Listes](../../pages/modif-listes/index.php#L18) : écritures de liste et de droits avant contrôle propriétaire. | Un POST fabriqué peut viser des identifiants d’autres ressources. | Contrôle de rôle et ressource avant chaque écriture ; matrice de rôles et IDs croisés. |
| P0 | [Réservation](../../pages/idees/index.php#L18) modifiée avant vérification de liste, sans condition atomique. | Idée d’une autre liste et réservation d’autrui potentiellement modifiables ; dernière écriture gagnante. | Contrôle et UPDATE conditionnel ; accès refusés et deux connexions concurrentes. |
| P0 | [Édition d’idée](../../pages/modif-idees/index.php#L191) accepte un droit non propriétaire, dont lecteur. | Un lecteur peut soumettre directement une modification. | Limiter explicitement aux rôles autorisés ; tester aussi le parent supprimé. |
| P0 | [Réinitialisation](../../pages/reset-password/index.php#L73) écrase le mot de passe puis compare jeton brut et hachage comme valeurs SQL. | Parcours normalement non fonctionnel ; verrouillage possible et succès non vérifié. | Jeton distinct, expirant et à usage unique ; tests de bout en bout et échec d’envoi. |
| P0 | [Idée achetée](../../pages/modif-idees/index.php#L177) : réservation effacée par nom. | Plusieurs idées homonymes peuvent être affectées, y compris dans d’autres listes. | Utiliser l’ID et une transaction ; tester deux listes et deux réservataires. |
| P0 conditionnel | [Purge HTTP](../../scripts/clean/index.php#L1) sans contrôle applicatif visible. | Si les protections externes ne bloquent pas la route, une requête peut déclencher les suppressions. L’accès réel n’a pas été vérifié. | Vérifier les protections puis retirer l’accès public ou le contrôler ; tester restauration et conservation. |
| P0/P1 selon exposition | Secrets dans [verif](../../scripts/verif/index.php#L16) et [reset](../../pages/reset-password/index.php#L43) ; exception affichée. | Exposition dépendant des accès ; compromission non établie. | Configuration privée, erreurs génériques, rotation coordonnée ; vérifier artefact et réponses. |
| P0 | [Déploiement](../../.github/workflows/deploy.yml#L16) sans protocole FTPS explicite. | L’action documente FTP non chiffré par défaut ; connexion réelle non observée. | Vérifier FTPS/certificat, rotation et retour arrière ; [documentation de l’action](https://github.com/SamKirkland/FTP-Deploy-Action). |
| P1 | [Mail](../../scripts/mail/index.php#L7) met systématiquement le webmaster en copie cachée. | Les messages, y compris liens de récupération, sont également copiés. | Décider la politique et retirer les liens sensibles des copies ; tester les destinataires. |
| P1 | [Session](../../scripts/verif/index.php#L3) configurée après `session_start()` dans les pages. | Le bloc est sauté si la session est active ; paramètres réels serveur inconnus. | Configurer avant démarrage ; tester cookies et sessions réellement émis. |
| P1 | Jeton CSRF non visible dans les formulaires et l’endpoint de notifications. | Risque dépendant notamment des cookies et du navigateur. | Jeton par session sur les écritures, déconnexion en POST ; tester absence et invalidité. |
| P1 | [Attribut de nom de liste](../../pages/modif-listes/index.php#L317), [impression](../../pages/print-liste/index.php#L83) : sorties non systématiquement échappées. | Injection HTML stockée plausible pour le nom ; exploitabilité des autres sorties dépend de leur stockage. | Échappement contextuel et validation d’URL ; tests d’entités, balises et guillemets. |
| P1 | [Création de liste](../../pages/modif-listes/index.php#L127) et autres écritures multiples sans transaction. | Mauvais propriétaire en concurrence ou état partiel sur erreur. | `lastInsertId()`, transaction et erreurs injectées. |
| P1 | [Listes publiques](../../pages/listes/index.php#L43) énumérées pour tous les comptes. | Différence confirmée avec la présentation d’un accès par lien ; intention inconnue. | Décision produit et tests des anciens liens. |
| P1 | [Rôle admin](../../widgets/navbar/index.php#L26) alimenté par un widget et stocké en session. | Accès direct ou révocation potentiellement incohérents. | Résoudre compte actif et rôle avant contrôleur ; tester changement de rôle. |
| P2 | README et fichier jQuery divergent sur leur version. | Inventaire peu fiable ; inutilité ou obsolescence non démontrées. | Vérifier versions, usages et licences avant suppression. |

## Dette et points à préserver

Prioriser autorisations dispersées, identifiants métier non uniques, opérations non atomiques, SQL et règles dans les vues. Traiter ensuite données historiquement encodées, URL absolues, requêtes répétées dans les boucles et documentation divergente ; mesurer avant d’optimiser. Conserver initialement PDO, tables, routes, fallback des mots de passe historiques, suppressions logiques jusqu’à décision, widgets utiles, Bootstrap et éléments d’accessibilité existants.
