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
(1, 'Liste Noël 2020', 'prive', NULL, '2020-11-30 21:40:19', NULL, NULL),
(2, 'Liste Anniversaire 2019', 'lien', 'CcrkZR1D0P', '2020-11-30 21:40:19', NULL, NULL),
(3, 'Liste Anniversaire 2020', 'secure', 'H7MMihSko1', '2020-11-30 21:40:19', NULL, NULL),
(4, 'Liste Noël 2020', 'prive', NULL, '2020-11-30 21:40:19', NULL, NULL);

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `lic_liste`
--
ALTER TABLE `lic_liste`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `lic_liste`
--
ALTER TABLE `lic_liste`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
