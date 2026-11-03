# Documentation d’architecture PHP

Cette documentation permet de reprendre l’audit et la modernisation progressive de l’application de listes d’idées cadeaux sans disposer de la conversation d’origine. Elle décrit une **proposition**, pas une migration déjà réalisée.

## État de référence

L’audit initial du **3 novembre 2026** a examiné le dépôt local sur `develop`, au commit `614eb9c`. Il s’agit d’une application PHP procédurale rendue côté serveur, utilisant PDO et des sessions. L’option recommandée est un PHP natif structuré, sans framework complet imposé ni réécriture totale.

Des risques prioritaires touchent notamment le profil, les permissions de listes, les réservations, la récupération du mot de passe et l’intégrité des données. Ils sont détaillés dans le registre d’audit. Les constats décrivent le code examiné ; ils ne prouvent ni le contenu actuellement déployé ni une exploitation réelle.

**État du plan :** proposé, non validé dans les échanges disponibles. **État de l’implémentation :** aucune modification applicative ni migration réalisée au titre de cet audit n’est enregistrée. La création de cette documentation ne vaut pas validation du plan ou d’un incrément. Aucun commit ni push n’est autorisé sans demande explicite.

## Documents

| Document | Utilité |
|---|---|
| [Audit](audit.md) | Périmètre, inventaire, parcours métier, preuves, risques et dette. |
| [Architecture cible](architecture-cible.md) | Option D, couches, alternatives, frontend et déploiement. |
| [Plan de migration](plan-migration.md) | Phases, dépendances, validation, tests et retour arrière. |
| [Backlog](backlog.md) | Tickets opérationnels avec critères, tests et statuts. |
| [Historique](historique.md) | Chronologie, décisions, limites et reconstitution des documents. |

## Convention de suivi

Les constats **confirmés**, les **hypothèses**, les **informations manquantes**, les **recommandations** et les **décisions explicitement validées** sont identifiés dans les documents. Un ticket proposé ne prouve ni validation, ni réalisation. Les références sources sont relatives à ce dossier ; les numéros de lignes concernent le commit audité et peuvent évoluer.

À chaque nouvel audit ou changement important : enregistrer la date, la branche et le commit, conserver une copie datée de toute version documentaire remplacée dans `archives/`, puis ajouter une entrée à l’[historique](historique.md) avec la raison et les preuves. Pour un simple ajout, conserver les anciennes décisions et indiquer la nouvelle entrée qui les complète ou les remplace. Mettre à jour les statuts du [backlog](backlog.md) seulement sur preuve, sans effacer les décisions antérieures. Ne jamais archiver des secrets ou données personnelles.

Les quatre premiers fichiers avaient été créés lors d’un échange précédent, mais étaient absents du dossier et non suivis par Git lors de la présente vérification. Ils sont ici **reconstitués à partir des résultats conservés dans l’échange**, avec un index et un backlog séparé ; aucune copie antérieure présente sur disque n’a été écrasée. Voir l’[historique](historique.md#3-novembre-2026--reconstitution-et-export-documentaire).
