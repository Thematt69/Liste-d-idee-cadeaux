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
(6, 1, 3, 'lecteur', '2020-11-30 21:52:47', NULL, NULL);

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
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `lic_autorisation`
--
ALTER TABLE `lic_autorisation`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `lic_autorisation`
--
ALTER TABLE `lic_autorisation`
  ADD CONSTRAINT `lic_autorisation_ibfk_1` FOREIGN KEY (`id_compte`) REFERENCES `lic_compte` (`id`),
  ADD CONSTRAINT `lic_autorisation_ibfk_2` FOREIGN KEY (`id_liste`) REFERENCES `lic_liste` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
