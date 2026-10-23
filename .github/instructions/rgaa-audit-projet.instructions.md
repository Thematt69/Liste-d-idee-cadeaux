---
description: "Use when conducting an RGAA accessibility audit of this project, choosing its sample pages, checking authenticated access, or reviewing shared components."
---

# Audit RGAA de ce projet

Utiliser cette instruction avec le skill global `rgaa-ara-audit`. Le skill global reste la référence pour les tests officiels, l’évaluation des preuves, les statuts et la saisie dans ARA. Ce fichier fournit seulement le contexte propre au site.

## Site et échantillon

Site audité : <https://family.matthieudevilliers.fr/>

Échantillon de référence fourni par l’utilisateur :

- Connexion : <https://family.matthieudevilliers.fr/pages/connexion/>
- Inscription : <https://family.matthieudevilliers.fr/pages/inscription/>
- Déclaration d’accessibilité : <https://family.matthieudevilliers.fr/pages/accessibilite/>
- Mes listes : <https://family.matthieudevilliers.fr/pages/listes/>
- Détail d’une liste, propriétaire : <https://family.matthieudevilliers.fr/pages/idees/?liste=156aeeb8ed16c7dcfad6c9c3f42c5c4b>
- Détail d’une liste, lecteur : <https://family.matthieudevilliers.fr/pages/idees/?liste=58b5985aa5ccbbc9a6089f1bd89f3be3>
- Création ou modification d’une liste : <https://family.matthieudevilliers.fr/pages/modif-listes/?type=solo>
- Création ou modification d’une idée : <https://family.matthieudevilliers.fr/pages/modif-idees/?idee=32>
- Mon compte : <https://family.matthieudevilliers.fr/pages/compte/>

Les deux paramètres `liste` peuvent donner accès aux listes. Leur conservation dans ce fichier est demandée par l’utilisateur. Toute personne ayant accès au dépôt ou à son historique peut les consulter; ne pas les recopier dans les sorties d’audit ni les journaux sans nécessité.

L’utilisateur peut fournir un périmètre différent pour un audit donné. Confirmer les URL, paramètres, états et pages réellement inclus avant de conclure; ne pas supposer que cet échantillon est celui de chaque audit.

## Accès de test

- Identifiant de test fourni : `test@test.test`.
- Aucun mot de passe n’est fourni ni mémorisé. Ne jamais le demander dans le chat, l’écrire dans un fichier, ni l’inclure dans des journaux ou captures.
- Avant tout contrôle nécessitant une authentification, vérifier d’abord si un outil navigateur est disponible et si sa session est déjà connectée. Vérifier l’état au moyen de l’interface du site; ne pas lire ni exposer les cookies, jetons ou autres secrets de session.
- Si aucune session active n’est constatée, ouvrir la page de connexion ci-dessus. Demander ensuite à l’utilisateur, avec l’outil de questions disponible (`vscode_askQuestions`), de se connecter manuellement dans le navigateur et de confirmer quand c’est fait. Ne pas poursuivre les contrôles protégés avant cette confirmation.
- Après confirmation, vérifier dans le navigateur que la connexion a réussi, par exemple au moyen d’un état de page ou d’une navigation visible réservée au compte connecté. Ne pas traiter la seule confirmation comme preuve de connexion réussie.
- Si les outils ne donnent pas accès à une session navigateur exploitable, l’expliquer et demander des preuves préparées par l’utilisateur. Ne pas prétendre avoir testé les pages protégées.
- Le compte peut ne pas avoir les droits nécessaires pour les deux rôles de liste. Vérifier séparément l’accès propriétaire et lecteur; signaler tout blocage sans contourner les contrôles d’accès.

## Éléments transverses candidats

Vérifier leur présence et leur comportement dans les pages de l’échantillon. Cette liste ne préjuge pas de leur conformité ni de leur présence sur chaque page.

- Navigation, lien d’évitement et menu selon session ou rôle : `widgets/navbar/index.php`.
- Pied de page et liens communs : `widgets/footer/index.php`.
- Imports, thème et styles partagés : `widgets/import/index.php` et `css/style.css`.
- Notifications conditionnelles et leur fermeture : `widgets/notif-toast/index.php` et `js/notifications.js`.

Pour chaque non-conformité transverse, suivre le skill global : documenter aussi les pages et occurrences concernées, et ne pas remplacer les évaluations page par page par un seul résultat transverse.