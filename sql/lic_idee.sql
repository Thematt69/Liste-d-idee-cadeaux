-- phpMyAdmin SQL Dump
-- version 5.0.4
-- https://www.phpmyadmin.net/
--
-- Hôte : localhost
-- Généré le : lun. 30 nov. 2020 à 22:37
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
(2, 2, 'Mug 3D Star Wars', 'https://www.google.com/shopping/product/15500616722017454828?q=mug&sxsrf=ALeKk00UST30V2tm5T4-aJ35LQegLc0fgQ:1606770508353&biw=1920&bih=941&prds=epd:15456449037140900517,prmr:3&sa=X&ved=0ahUKEwiZ5KnymKvtAhVMJhoKHX-KBqcQ8gII3BA', NULL, 1, '2020-11-30 22:31:25', NULL, NULL),
(3, 2, 'Tire-bouchons', 'https://www.amazon.fr/Tire-bouchons-pour-serveurs-tout-en-ouvre-bouteille/dp/B01IZ6HK5S', NULL, 0, '2020-11-30 22:31:25', NULL, NULL),
(4, 3, 'Sweat Hoodie Wankil', 'https://shop.wankil.fr/collections/collection-classique-wankil/products/hoodie-wankil-nouvelle-edition?variant=29365895888989', NULL, 0, '2020-11-30 22:31:25', NULL, NULL),
(5, 3, 'Verre Trempé pour Switch', 'https://www.amazon.fr/gp/product/B0798MRTW8/ref=ppx_yo_dt_b_asin_title_o09_s00?ie=UTF8&psc=1', NULL, 1, '2020-11-30 22:31:25', NULL, NULL),
(6, 3, 'Chaise de bureau', '', NULL, 0, '2020-11-30 22:31:25', NULL, NULL),
(7, 3, 'Argent de poche', '', NULL, 0, '2020-11-30 22:31:25', NULL, NULL);

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `lic_idee`
--
ALTER TABLE `lic_idee`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_liste` (`id_liste`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `lic_idee`
--
ALTER TABLE `lic_idee`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `lic_idee`
--
ALTER TABLE `lic_idee`
  ADD CONSTRAINT `lic_idee_ibfk_1` FOREIGN KEY (`id_liste`) REFERENCES `lic_liste` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
