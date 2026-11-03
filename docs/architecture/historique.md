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
