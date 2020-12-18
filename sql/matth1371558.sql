-- phpMyAdmin SQL Dump
-- version 5.0.4
-- https://www.phpmyadmin.net/
--
-- Hôte : localhost
-- Généré le : ven. 18 déc. 2020 à 21:29
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
  `modified_to` datetime DEFAULT NULL,
  `deleted_to` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `lic_autorisation`
--

INSERT INTO `lic_autorisation` (`id`, `id_compte`, `id_liste`, `type`, `created_to`, `modified_to`, `deleted_to`) VALUES
(1, 2, 1, 'proprietaire', '2020-11-30 21:45:09', NULL, NULL),
(2, 1, 4, 'proprietaire', '2020-11-30 21:45:09', NULL, NULL),
(3, 1, 2, 'proprietaire', '2020-11-30 21:45:09', NULL, NULL),
(4, 2, 3, 'proprietaire', '2020-11-30 21:45:09', NULL, NULL),
(5, 2, 2, 'lecteur', '2020-11-30 21:52:47', NULL, NULL),
(6, 1, 3, 'lecteur', '2020-11-30 21:52:47', NULL, NULL),
(7, 1, 5, 'proprietaire', '2020-12-15 23:26:29', NULL, NULL),
(8, 2, 5, 'moderateur', '2020-12-15 23:26:42', NULL, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `lic_compte`
--

CREATE TABLE `lic_compte` (
  `id` int(11) NOT NULL,
  `prenom` varchar(255) NOT NULL,
  `nom` varchar(255) NOT NULL,
  `motdepasse` varchar(255) NOT NULL,
  `date_naissance` date NOT NULL,
  `mail` varchar(255) NOT NULL,
  `created_to` datetime NOT NULL DEFAULT current_timestamp(),
  `modified_to` datetime DEFAULT NULL,
  `deleted_to` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `lic_compte`
--

INSERT INTO `lic_compte` (`id`, `prenom`, `nom`, `motdepasse`, `date_naissance`, `mail`, `created_to`, `modified_to`, `deleted_to`) VALUES
(1, 'Matthieu', 'Devilliers', '$2y$10$MKfuPVysZyCp8sk4gyXBUu8hoj3CGEEr0NVuJQUdBNVCaBVLPYBGa', '2000-09-29', 'devilliers.matthieu@gmail.com', '2020-11-30 21:34:10', NULL, NULL),
(2, 'Mathilde', 'Sanlaville', '$2y$10$.YlsMoaWJ/xde5Tvjt1Hh.R2kVRPrI45CmsYPbrHA0UmZf6R/zuz6', '1968-12-09', 'mathilde.sanlaville@gmail.com', '2020-11-30 21:34:10', NULL, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `lic_idee`
--

CREATE TABLE `lic_idee` (
  `id` int(11) NOT NULL,
  `id_liste` int(11) NOT NULL,
  `nom` varchar(255) NOT NULL,
  `lien` varchar(255) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `is_buy` tinyint(1) NOT NULL DEFAULT 0,
  `created_to` datetime NOT NULL DEFAULT current_timestamp(),
  `modified_to` datetime DEFAULT NULL,
  `deleted_to` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `lic_idee`
--

INSERT INTO `lic_idee` (`id`, `id_liste`, `nom`, `lien`, `image`, `is_buy`, `created_to`, `modified_to`, `deleted_to`) VALUES
(1, 2, 'Casque audio', 'https://www.amazon.fr/Sony-MDR-ZX310B-Casque-Pliable-Noir/dp/B00I3LUWQA/ref=sr_1_9?__mk_fr_FR=%C3%85M%C3%85%C5%BD%C3%95%C3%91&dchild=1&keywords=casque+audio&qid=1606770172&sr=8-9', NULL, 0, '2020-11-30 22:31:25', NULL, NULL),
(2, 2, 'Mug 3D Star Wars', 'https://www.google.com/shopping/product/15500616722017454828?q=mug&sxsrf=ALeKk00UST30V2tm5T4-aJ35LQegLc0fgQ:1606770508353&biw=1920&bih=941&prds=epd:15456449037140900517,prmr:3&sa=X&ved=0ahUKEwiZ5KnymKvtAhVMJhoKHX-KBqcQ8gII3BA', 'shopping.jpg', 1, '2020-11-30 22:31:25', NULL, NULL),
(3, 2, 'Tire-bouchons', 'https://www.amazon.fr/Tire-bouchons-pour-serveurs-tout-en-ouvre-bouteille/dp/B01IZ6HK5S', NULL, 0, '2020-11-30 22:31:25', NULL, NULL),
(4, 3, 'Sweat Hoodie Wankil', 'https://shop.wankil.fr/collections/collection-classique-wankil/products/hoodie-wankil-nouvelle-edition?variant=29365895888989', 'wankilnoirmockup_9a3ba248-dc58-4c11-8d41-a99442732fcd_720x.jpg', 0, '2020-11-30 22:31:25', NULL, NULL),
(5, 3, 'Verre Trempé pour Switch', 'https://www.amazon.fr/gp/product/B0798MRTW8/ref=ppx_yo_dt_b_asin_title_o09_s00?ie=UTF8&psc=1', NULL, 1, '2020-11-30 22:31:25', NULL, NULL),
(6, 3, 'Chaise de bureau', '', NULL, 0, '2020-11-30 22:31:25', NULL, NULL),
(7, 3, 'Argent de poche', '', NULL, 0, '2020-11-30 22:31:25', NULL, NULL),
(8, 1, 'Les montagnes russes', 'https://www.lego.com/fr-fr/product/roller-coaster-10261', '10261.webp', 0, '2020-12-15 23:21:02', NULL, NULL),
(9, 1, 'Pyjama court', NULL, NULL, 0, '2020-12-15 23:21:02', NULL, NULL),
(10, 4, 'Adaptateur USB/Jack pour écouter autre chose que un cd ou de la radio', NULL, NULL, 0, '2020-12-15 23:24:10', NULL, NULL),
(11, 4, 'Stickers Minecraft', 'https://vendugeek.com/home/868-creeper-inside-wall-cling.html?search_query=minecraft&results=22', NULL, 0, '2020-12-15 23:24:10', NULL, NULL),
(12, 5, 'Gourde en bambou', 'https://boutiquepopcorn.fr/products/gourde-en-bambou-popcorn', 'Gourde_1024x1024.png', 0, '2020-12-15 23:31:01', NULL, NULL),
(13, 5, 'Hub HDMI et USB', NULL, NULL, 1, '2020-12-15 23:31:24', NULL, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `lic_liste`
--

CREATE TABLE `lic_liste` (
  `id` int(11) NOT NULL,
  `nom` varchar(255) NOT NULL,
  `partage` enum('prive','lien','secure') NOT NULL DEFAULT 'prive' COMMENT 'prive - lien - secure',
  `lien_partage` varchar(255) DEFAULT NULL COMMENT 'sur la page principal via URL GET',
  `created_to` datetime NOT NULL DEFAULT current_timestamp(),
  `modified_to` datetime DEFAULT NULL,
  `deleted_to` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `lic_liste`
--

INSERT INTO `lic_liste` (`id`, `nom`, `partage`, `lien_partage`, `created_to`, `modified_to`, `deleted_to`) VALUES
(1, 'Liste Noël 2020', 'prive', 'jyutuyinuyi', '2020-11-24 21:40:19', NULL, NULL),
(2, 'Liste Anniversaire 2019', 'lien', 'CcrkZR1D0P', '2019-05-30 21:28:13', NULL, NULL),
(3, 'Liste Anniversaire 2020', 'secure', 'H7MMihSko1', '2020-08-30 21:40:19', NULL, NULL),
(4, 'Liste Noël 2020', 'prive', 'ui89uiuFTyh', '2020-11-30 21:40:19', NULL, NULL),
(5, 'Liste d\'idée en vrac', 'lien', '489e1rg16R', '2020-12-15 23:26:17', NULL, NULL);

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
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `lic_autorisation`
--
ALTER TABLE `lic_autorisation`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT pour la table `lic_compte`
--
ALTER TABLE `lic_compte`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT pour la table `lic_idee`
--
ALTER TABLE `lic_idee`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT pour la table `lic_liste`
--
ALTER TABLE `lic_liste`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

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
-- Contraintes pour la table `lic_idee`
--
ALTER TABLE `lic_idee`
  ADD CONSTRAINT `lic_idee_ibfk_1` FOREIGN KEY (`id_liste`) REFERENCES `lic_liste` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
