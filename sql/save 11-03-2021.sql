-- phpMyAdmin SQL Dump
-- version 5.0.4
-- https://www.phpmyadmin.net/
--
-- Hôte : localhost
-- Généré le : jeu. 11 mars 2021 à 23:30
-- Version du serveur :  10.3.25-MariaDB-0+deb10u1
-- Version de PHP : 7.3.19-1~deb10u1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `matth1371558`
--

-- --------------------------------------------------------

--
-- Structure de la table `lic_autorisation`
--

CREATE TABLE `lic_autorisation` (
  `id` int(11) NOT NULL,
  `id_compte` int(11) NOT NULL,
  `id_liste` int(11) NOT NULL,
  `type` enum('proprietaire','moderateur','lecteur') NOT NULL DEFAULT 'lecteur' COMMENT 'proprietaire - moderateur - lecteur',
  `created_to` datetime NOT NULL DEFAULT current_timestamp(),
  `deleted_to` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `lic_autorisation`
--

INSERT INTO `lic_autorisation` (`id`, `id_compte`, `id_liste`, `type`, `created_to`, `deleted_to`) VALUES
(2, 1, 4, 'proprietaire', '2020-11-30 21:45:09', NULL),
(3, 1, 2, 'proprietaire', '2020-11-30 21:45:09', NULL),
(7, 1, 5, 'proprietaire', '2020-12-15 23:26:29', NULL),
(10, 1, 17, 'proprietaire', '2020-12-18 23:05:29', '2020-12-18 23:12:06'),
(11, 1, 18, 'proprietaire', '2020-12-20 00:39:32', '2020-12-20 00:12:39'),
(12, 1, 19, 'proprietaire', '2020-12-20 00:40:02', '2020-12-20 00:12:40'),
(13, 5, 20, 'proprietaire', '2021-03-03 21:13:02', NULL),
(14, 6, 21, 'proprietaire', '2021-03-04 08:13:32', NULL),
(15, 6, 22, 'proprietaire', '2021-03-04 08:23:35', NULL),
(16, 6, 23, 'proprietaire', '2021-03-04 08:23:58', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `lic_compte`
--

CREATE TABLE `lic_compte` (
  `id` int(11) NOT NULL,
  `prenom` varchar(255) NOT NULL,
  `nom` varchar(255) NOT NULL,
  `motdepasse` varchar(255) NOT NULL,
  `mail` varchar(255) NOT NULL,
  `fonction` enum('client','admin') NOT NULL DEFAULT 'client',
  `created_to` datetime NOT NULL DEFAULT current_timestamp(),
  `deleted_to` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `lic_compte`
--

INSERT INTO `lic_compte` (`id`, `prenom`, `nom`, `motdepasse`, `mail`, `fonction`, `created_to`, `deleted_to`) VALUES
(1, 'Matthieu', 'Devilliers', '$2y$10$Vs0fz7cVJqUpoUvN6LZCVeU7Xmo1K7OOiLUK.HHizJiLMxzYDvC2y', 'devilliers.matthieu@gmail.com', 'client', '2020-11-30 21:34:10', NULL),
(5, 'Support', 'WebMaster', '$2y$10$88IJdf.zTS28LtZNyca6percE5aEFvOPT9h2VPqA6AXwVh2rLVV4K', 'webmaster@matthieudevilliers.fr', 'admin', '2021-03-03 20:56:35', NULL),
(6, 'Maxx', 'Dev ', '$2y$10$oM.g91PLOo0d1I.CWD.fF.sDgkHrpeRz.iTznzdsVoVmeFBoPESZa', 'maxou336@hotmail.fr', 'client', '2021-03-04 08:05:50', NULL),
(7, 'L&eacute;a', 'MOREAU', '$2y$10$/uHZc50a.kCcg6pS.Z7N5ORzYm2b.kwoG51XVABKSbQU4LlcpFu1m', 'lea.moreau49@gmail.com', 'client', '2021-03-09 15:44:28', NULL),
(9, 'Mathilde', 'SANLAVILLE', '$2y$10$FB6HYAgO8D9MKbu7SJS9vum4InIipmr1VNX34oqn9jmY8hz.oXVY.', 'mathilde.sanlaville@gmail.com', 'client', '2021-03-11 15:41:27', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `lic_connexion`
--

CREATE TABLE `lic_connexion` (
  `id` int(11) NOT NULL,
  `id_compte` int(11) NOT NULL,
  `connected_to` datetime NOT NULL DEFAULT current_timestamp(),
  `adresse_ip_v4` varchar(15) NOT NULL COMMENT 'Adresse IP V4'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `lic_connexion`
--

INSERT INTO `lic_connexion` (`id`, `id_compte`, `connected_to`, `adresse_ip_v4`) VALUES
(1, 1, '2021-03-09 17:28:55', '86.202.94.255'),
(2, 1, '2021-03-10 18:00:31', '86.202.94.255'),
(3, 1, '2021-03-10 18:25:09', '86.202.94.255'),
(4, 1, '2021-03-10 20:04:46', '81.185.164.41'),
(5, 1, '2021-03-10 20:06:03', '81.185.164.41'),
(6, 1, '2021-03-10 20:18:13', '86.202.94.255'),
(7, 5, '2021-03-10 20:52:04', '86.202.94.255'),
(8, 5, '2021-03-10 22:07:44', '86.202.94.255'),
(9, 5, '2021-03-10 22:07:58', '86.202.94.255'),
(10, 1, '2021-03-10 22:46:08', '86.202.94.255'),
(11, 1, '2021-03-10 23:18:12', '86.202.94.255'),
(12, 5, '2021-03-10 23:33:50', '86.202.94.255'),
(13, 1, '2021-03-11 08:36:49', '86.202.94.255'),
(14, 5, '2021-03-11 10:03:20', '86.202.94.255'),
(15, 1, '2021-03-11 10:29:24', '86.202.94.255'),
(16, 5, '2021-03-11 15:04:32', '86.202.94.255'),
(17, 1, '2021-03-11 16:24:33', '86.202.94.255'),
(18, 6, '2021-03-11 16:44:29', '77.205.18.128');

-- --------------------------------------------------------

--
-- Structure de la table `lic_idee`
--

CREATE TABLE `lic_idee` (
  `id` int(11) NOT NULL,
  `id_liste` int(11) NOT NULL,
  `nom` varchar(255) NOT NULL,
  `commentaire` varchar(255) NOT NULL,
  `lien` varchar(255) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `is_buy` tinyint(1) NOT NULL DEFAULT 0,
  `created_to` datetime NOT NULL DEFAULT current_timestamp(),
  `deleted_to` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `lic_idee`
--

INSERT INTO `lic_idee` (`id`, `id_liste`, `nom`, `commentaire`, `lien`, `image`, `is_buy`, `created_to`, `deleted_to`) VALUES
(1, 2, 'Casque audio', '', 'https://www.amazon.fr/Sony-MDR-ZX310B-Casque-Pliable-Noir/dp/B00I3LUWQA/ref=sr_1_9?__mk_fr_FR=%C3%85M%C3%85%C5%BD%C3%95%C3%91&dchild=1&keywords=casque+audio&qid=1606770172&sr=8-9', '', 0, '2020-11-30 22:31:25', NULL),
(2, 2, 'Mug 3D Star Wars', '', 'https://www.google.com/shopping/product/15500616722017454828?q=mug&sxsrf=ALeKk00UST30V2tm5T4-aJ35LQegLc0fgQ:1606770508353&biw=1920&bih=941&prds=epd:15456449037140900517,prmr:3&sa=X&ved=0ahUKEwiZ5KnymKvtAhVMJhoKHX-KBqcQ8gII3BA', 'shopping.jpg', 1, '2020-11-30 22:31:25', NULL),
(3, 2, 'Tire-bouchons', '', 'https://www.amazon.fr/Tire-bouchons-pour-serveurs-tout-en-ouvre-bouteille/dp/B01IZ6HK5S', '', 0, '2020-11-30 22:31:25', NULL),
(10, 4, 'Adaptateur USB/Jack pour écouter autre chose que un cd ou de la radio', '', '', '', 0, '2020-12-15 23:24:10', NULL),
(11, 4, 'Stickers Minecraft', '', 'https://vendugeek.com/home/868-creeper-inside-wall-cling.html?search_query=minecraft&results=22', '', 0, '2020-12-15 23:24:10', NULL),
(12, 5, 'Gourde en bambou', 'Voir avec moi', 'https://boutiquepopcorn.fr/products/gourde-en-bambou-popcorn', '', 0, '2020-12-15 23:31:01', NULL),
(13, 5, 'Hub HDMI et USB', '', '', '', 1, '2020-12-15 23:31:24', NULL),
(15, 5, 'Test d\'id&eacute;e', '', 'https://www.test.com/', '', 0, '2021-01-15 22:00:48', '2021-01-15 22:01:02'),
(16, 20, 'Bouteille', '', '', '', 1, '2021-03-03 21:18:55', NULL),
(17, 21, 'Ehjns', '', 'http://www.minecraft.com', '', 0, '2021-03-04 08:14:16', NULL),
(18, 21, 'Jsk&rsquo;snwwn&rsquo;ddkdkdkdk&rsquo;&acute;ddndbbddndndndnjdjd', '', '', '', 0, '2021-03-04 08:14:30', NULL),
(19, 21, 'Gyjb', '', 'http://www.lego.fr', '', 0, '2021-03-04 08:21:32', NULL),
(20, 23, 'Vjjv', '', '', '', 1, '2021-03-04 08:24:07', '2021-03-04 08:03:24'),
(21, 23, 'Tubj', '', '', '', 0, '2021-03-04 08:25:14', '2021-03-04 08:03:25'),
(22, 23, 'Hjj', 'Ryjcd', '', '', 0, '2021-03-04 08:25:28', NULL),
(23, 23, 'Ghh', '', '', '', 0, '2021-03-04 08:25:32', '2021-03-04 08:03:25');

-- --------------------------------------------------------

--
-- Structure de la table `lic_liste`
--

CREATE TABLE `lic_liste` (
  `id` int(11) NOT NULL,
  `nom` varchar(255) NOT NULL,
  `partage` enum('prive','limite','public') NOT NULL DEFAULT 'prive' COMMENT 'prive - limite - public',
  `lien_partage` varchar(255) DEFAULT NULL COMMENT 'sur la page principal via URL GET',
  `created_to` datetime NOT NULL DEFAULT current_timestamp(),
  `deleted_to` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `lic_liste`
--

INSERT INTO `lic_liste` (`id`, `nom`, `partage`, `lien_partage`, `created_to`, `deleted_to`) VALUES
(2, 'Liste Anniversaire 2019', 'prive', 'CcrkZR1D0P', '2019-05-30 21:28:13', NULL),
(4, 'Liste No&euml;l 2020', 'prive', 'ui89uiuFTyh', '2020-11-30 21:40:19', NULL),
(5, 'Liste d\'id&eacute;e en vrac', 'prive', '489e1rg16R', '2020-12-15 23:26:17', NULL),
(17, 'test1', 'prive', '2e1bfec23244a427c69e07dbee840252', '2020-12-18 23:05:29', '2020-12-18 23:12:06'),
(18, 'test 1', 'prive', 'ca49edf4de7792604129a4109d59acf3', '2020-12-20 00:39:32', '2020-12-20 00:12:39'),
(19, 'test 1', 'prive', '1a2cc39bdd73b717c1cfb34adf03c964', '2020-12-20 00:40:02', '2020-12-20 00:12:40'),
(20, 'Test de liste', 'prive', '8e6f88013da525313380a3f293e04bfe', '2021-03-03 21:13:02', NULL),
(21, 'Chknf', 'prive', 'f4a746f9183916971a9c07fdd70b7729', '2021-03-04 08:13:32', NULL),
(22, 'Vjjvcf', 'prive', '4b718a5094fb942d3019e29d685a1390', '2021-03-04 08:23:35', NULL),
(23, 'Chjvcf', 'prive', '1875b0c85f1d9b9afa40a22ea5dc30a7', '2021-03-04 08:23:58', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `lic_notif`
--

CREATE TABLE `lic_notif` (
  `id` int(11) NOT NULL,
  `id_compte` int(11) NOT NULL,
  `titre` varchar(255) NOT NULL,
  `message` varchar(255) NOT NULL,
  `etat` enum('non-lu','lu') NOT NULL DEFAULT 'non-lu',
  `created_to` datetime NOT NULL DEFAULT current_timestamp(),
  `deleted_to` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `lic_notif`
--

INSERT INTO `lic_notif` (`id`, `id_compte`, `titre`, `message`, `etat`, `created_to`, `deleted_to`) VALUES
(1, 1, 'Nouveauté', 'Les alertes sont maintenant disponibles, vous trouverez bientôt plein d\'informations utiles ici.', 'lu', '2021-03-10 19:57:18', NULL),
(2, 1, 'Nouveauté', 'Vous pouvez retrouver l\'historique de vos connexions directement dans l\'onglet \"Mon compte\" !', 'lu', '2021-03-10 18:19:42', NULL),
(3, 7, 'Nouveauté', 'Les alertes sont maintenant disponibles, vous trouverez bientôt plein d\'informations utiles ici.', 'non-lu', '2021-03-10 19:57:18', NULL),
(4, 7, 'Nouveauté', 'Vous pouvez retrouver l\'historique de vos connexions directement dans l\'onglet \"Mon compte\" !', 'non-lu', '2021-03-10 18:19:42', NULL),
(5, 6, 'Nouveauté', 'Les alertes sont maintenant disponibles, vous trouverez bientôt plein d\'informations utiles ici.', 'lu', '2021-03-10 19:57:18', NULL),
(6, 6, 'Nouveauté', 'Vous pouvez retrouver l\'historique de vos connexions directement dans l\'onglet \"Mon compte\" !', 'lu', '2021-03-10 18:19:42', NULL),
(7, 5, 'Nouveauté', 'Les alertes sont maintenant disponibles, vous trouverez bientôt plein d\'informations utiles ici.', 'non-lu', '2021-03-10 19:57:18', NULL),
(8, 5, 'Nouveauté', 'Vous pouvez retrouver l\'historique de vos connexions directement dans l\'onglet \"Mon compte\" !', 'non-lu', '2021-03-10 18:19:42', NULL),
(9, 7, 'Données personnelles', 'Votre date de naissance n\'était plus utile, nous avons donc procédé à la suppression définitive de celle-ci.', 'non-lu', '2021-03-10 23:21:45', NULL),
(10, 1, 'Données personnelles', 'Votre date de naissance n\'était plus utile, nous avons donc procédé à la suppression définitive de celle-ci.', 'lu', '2021-03-10 23:21:45', NULL),
(11, 6, 'Données personnelles', 'Votre date de naissance n\'était plus utile, nous avons donc procédé à la suppression définitive de celle-ci.', 'lu', '2021-03-10 23:21:45', NULL),
(12, 5, 'Données personnelles', 'Votre date de naissance n\'était plus utile, nous avons donc procédé à la suppression définitive de celle-ci.', 'non-lu', '2021-03-10 23:21:45', NULL),
(16, 1, 'Mot de passe modifié', 'Votre mot de passe vient d\'être modifié, si vous n\'êtes pas à l\'origine. Changez-le directement dans l\'onglet \\\"Mon compte\\\" ou en contactant le support.', 'lu', '2021-03-11 10:29:12', NULL),
(17, 7, 'Nouveauté', 'Les commentaires arrivent dans vos idées ! Besoin d\'ajouter une information, dire votre couleur favorite, bref tout est possible.', 'non-lu', '2021-03-11 13:26:46', NULL),
(18, 1, 'Nouveauté', 'Les commentaires arrivent dans vos idées ! Besoin d\'ajouter une information, dire votre couleur favorite, bref tout est possible.', 'lu', '2021-03-11 13:26:46', NULL),
(19, 6, 'Nouveauté', 'Les commentaires arrivent dans vos idées ! Besoin d\'ajouter une information, dire votre couleur favorite, bref tout est possible.', 'lu', '2021-03-11 13:26:46', NULL),
(20, 5, 'Nouveauté', 'Les commentaires arrivent dans vos idées ! Besoin d\'ajouter une information, dire votre couleur favorite, bref tout est possible.', 'non-lu', '2021-03-11 13:26:46', NULL),
(28, 6, 'Mot de passe modifié', 'Votre mot de passe vient d\'être modifié, si vous n\'êtes pas à l\'origine. Changez-le directement dans l\'onglet \"Mon compte\" ou en contactant le support.', 'lu', '2021-03-11 16:42:42', NULL);

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `lic_autorisation`
--
ALTER TABLE `lic_autorisation`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_compte` (`id_compte`),
  ADD KEY `id_liste` (`id_liste`);

--
-- Index pour la table `lic_compte`
--
ALTER TABLE `lic_compte`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `mail` (`mail`);

--
-- Index pour la table `lic_connexion`
--
ALTER TABLE `lic_connexion`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_compte` (`id_compte`);

--
-- Index pour la table `lic_idee`
--
ALTER TABLE `lic_idee`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_liste` (`id_liste`);

--
-- Index pour la table `lic_liste`
--
ALTER TABLE `lic_liste`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `lic_notif`
--
ALTER TABLE `lic_notif`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_compte` (`id_compte`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `lic_autorisation`
--
ALTER TABLE `lic_autorisation`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT pour la table `lic_compte`
--
ALTER TABLE `lic_compte`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT pour la table `lic_connexion`
--
ALTER TABLE `lic_connexion`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT pour la table `lic_idee`
--
ALTER TABLE `lic_idee`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT pour la table `lic_liste`
--
ALTER TABLE `lic_liste`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT pour la table `lic_notif`
--
ALTER TABLE `lic_notif`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `lic_autorisation`
--
ALTER TABLE `lic_autorisation`
  ADD CONSTRAINT `lic_autorisation_ibfk_1` FOREIGN KEY (`id_compte`) REFERENCES `lic_compte` (`id`),
  ADD CONSTRAINT `lic_autorisation_ibfk_2` FOREIGN KEY (`id_liste`) REFERENCES `lic_liste` (`id`);

--
-- Contraintes pour la table `lic_connexion`
--
ALTER TABLE `lic_connexion`
  ADD CONSTRAINT `lic_connexion_ibfk_1` FOREIGN KEY (`id_compte`) REFERENCES `lic_compte` (`id`);

--
-- Contraintes pour la table `lic_idee`
--
ALTER TABLE `lic_idee`
  ADD CONSTRAINT `lic_idee_ibfk_1` FOREIGN KEY (`id_liste`) REFERENCES `lic_liste` (`id`);

--
-- Contraintes pour la table `lic_notif`
--
ALTER TABLE `lic_notif`
  ADD CONSTRAINT `lic_notif_ibfk_1` FOREIGN KEY (`id_compte`) REFERENCES `lic_compte` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
