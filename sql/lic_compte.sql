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
(1, 'Matthieu', 'Devilliers', '123456', '2000-09-29', 'devilliers.matthieu@gmail.com', '2020-11-30 21:34:10', NULL, NULL),
(2, 'Mathilde', 'Sanlaville', '123456', '1968-12-09', 'mathilde.sanlaville@gmail.com', '2020-11-30 21:34:10', NULL, NULL);

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `lic_compte`
--
ALTER TABLE `lic_compte`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `lic_compte`
--
ALTER TABLE `lic_compte`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
