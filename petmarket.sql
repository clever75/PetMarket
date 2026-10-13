-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : mar. 13 oct. 2026 à 20:18
-- Version du serveur : 10.4.32-MariaDB
-- Version de PHP : 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `petmarket`
--

-- --------------------------------------------------------

--
-- Structure de la table `animal`
--

CREATE TABLE `animal` (
  `idAnimal` int(3) NOT NULL COMMENT 'Numéro de l''animal',
  `nom` varchar(20) NOT NULL COMMENT 'Nom de l''animal',
  `age` varchar(10) DEFAULT NULL,
  `prix` int(6) NOT NULL COMMENT 'Prix de l''animal',
  `description` text DEFAULT NULL,
  `statut` varchar(10) NOT NULL DEFAULT 'disponible',
  `photo` varchar(255) NOT NULL COMMENT 'Photo de l''animal\r\n',
  `idCategorie` int(2) NOT NULL COMMENT 'Numéro catégorie',
  `idUser` int(3) DEFAULT NULL,
  `date_ajout` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `animal`
--

INSERT INTO `animal` (`idAnimal`, `nom`, `age`, `prix`, `description`, `statut`, `photo`, `idCategorie`, `idUser`, `date_ajout`) VALUES
(43, 'Bobo', NULL, 5000, 'Lapin blanc, très doux, idéal pour enfants', 'reserve', 'seller_6a25b5d56866f.jpg', 3, 47, '2026-06-07 18:17:57'),
(44, 'Luna', NULL, 8000, 'Chatte grise, calme, bien éduquée', 'disponible', 'seller_6a25b62e7224b.jpg', 2, 47, '2026-06-07 18:19:26'),
(45, 'Max', NULL, 20000, 'Chihuahua roux, petit et mignon', 'disponible', 'seller_6a25b67a4d842.jpg', 1, 47, '2026-06-07 18:20:42'),
(46, 'Coco', NULL, 15000, 'Petit oiseau coloré, chante bien', 'disponible', 'seller_6a25b6b151ff5.jpg', 4, 47, '2026-06-07 18:21:37'),
(47, 'Rexi', NULL, 30000, 'Labrador doré, bien vacciné, très affectueux', 'disponible', 'seller_6a25b719cbcaf.jpg', 1, 46, '2026-06-07 18:23:21'),
(48, 'Mimi', NULL, 10000, 'Chatte tigrée, propre, stérilisée', 'reserve', 'seller_6a25b7599dee2.jpg', 2, 46, '2026-06-07 18:24:25'),
(50, 'Lili', NULL, 10000, '', 'reserve', 'seller_6a25b834cdbaf.jpg', 4, 46, '2026-06-07 18:28:04'),
(51, 'Qli', NULL, 5000, 'La poils roux bien dorés', 'disponible', 'seller_6a26b4d0f0fe7.jpg', 2, 46, '2026-06-08 12:25:52');

-- --------------------------------------------------------

--
-- Structure de la table `categorie`
--

CREATE TABLE `categorie` (
  `idCategorie` int(2) NOT NULL COMMENT 'Numéeo de catégorie',
  `libelle` varchar(20) NOT NULL COMMENT 'Libellé de la catégorie'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `categorie`
--

INSERT INTO `categorie` (`idCategorie`, `libelle`) VALUES
(1, 'Chien'),
(2, 'Chat'),
(3, 'Lapin'),
(4, 'Oiseau');

-- --------------------------------------------------------

--
-- Structure de la table `commande`
--

CREATE TABLE `commande` (
  `idCommande` int(11) NOT NULL,
  `idUser` int(11) NOT NULL COMMENT 'Acheteur',
  `idAnimal` int(11) NOT NULL COMMENT 'Animal commandé',
  `prix` int(11) NOT NULL COMMENT 'Prix au moment de la commande',
  `statut` varchar(20) NOT NULL DEFAULT 'en_attente' COMMENT 'en_attente | confirme | annule',
  `date_commande` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `commande`
--

INSERT INTO `commande` (`idCommande`, `idUser`, `idAnimal`, `prix`, `statut`, `date_commande`) VALUES
(5, 44, 50, 10000, 'en_attente', '2026-06-07 18:28:53'),
(6, 45, 43, 5000, 'en_attente', '2026-06-07 18:34:51'),
(7, 44, 50, 10000, 'en_attente', '2026-06-08 11:55:16'),
(8, 44, 48, 10000, 'en_attente', '2026-06-08 12:27:50');

-- --------------------------------------------------------

--
-- Structure de la table `panier`
--

CREATE TABLE `panier` (
  `idUser` int(3) NOT NULL,
  `idAnimal` int(3) NOT NULL,
  `quantite` int(2) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `utilisateur`
--

CREATE TABLE `utilisateur` (
  `idUser` int(3) NOT NULL,
  `nom` varchar(50) DEFAULT NULL,
  `pseudo` varchar(20) DEFAULT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `telephone` varchar(20) DEFAULT NULL,
  `ville` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `role` varchar(15) NOT NULL DEFAULT 'acheteur',
  `date_inscription` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_admin` tinyint(1) NOT NULL DEFAULT 0,
  `seller_request` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `utilisateur`
--

INSERT INTO `utilisateur` (`idUser`, `nom`, `pseudo`, `photo`, `telephone`, `ville`, `email`, `password`, `role`, `date_inscription`, `is_admin`, `seller_request`) VALUES
(31, 'Clever ASSAN', 'Clever01', NULL, NULL, NULL, 'cleverassan696@gmail.com', '$2y$10$1t0VPE5giCH/EDJN1FHvOewogKq.7ElxR3VYLHRSkjfKWiM7fdbNu', 'acheteur', '2026-03-18 07:20:32', 0, 0),
(44, 'Sénam Dossou', 'Sénam Dossou', NULL, '91223344', 'Kpalimé', 'senam@gmail.com', '$2y$10$4PcpBKFgnA7ERkysx6WjJO5cxAU5EjhaH/lVMY8626LvNMqkSEhDm', 'acheteur', '2026-06-07 17:59:15', 0, 0),
(45, 'Eyram Tété', 'Eyram Tété', NULL, '92334455', 'Djable', 'eyram@gmail.com', '$2y$10$sxmOt6SQ7EtnIswhAFPKpOJGDwdCZdBW9Z2BAmyEXw6ByBB2MYbrG', 'acheteur', '2026-06-07 18:03:57', 0, 0),
(46, 'Afi Mensah', 'Afi Mensah', NULL, '93445566', 'Lomé', 'afi@gmail.com', '$2y$10$ZxKVrj1dfbhGXJrg2doWeO7iSscz8oGFRh1Zx.hrWPSyp6d1JI3Je', 'vendeur', '2026-06-07 18:06:40', 0, 0),
(47, 'Yao Kpogo', 'Yao Kpogo', NULL, '94556677', 'Lomé', 'yao@gmail.com', '$2y$10$pZHxTpM8zx7u3ae3Lex7du5MhzeIRm97HcQxw9aG4XShmAp/mZo5i', 'vendeur', '2026-06-07 18:07:46', 0, 0);

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `animal`
--
ALTER TABLE `animal`
  ADD PRIMARY KEY (`idAnimal`),
  ADD KEY `idCategorie` (`idCategorie`),
  ADD KEY `animal_ibfk_2` (`idUser`);

--
-- Index pour la table `categorie`
--
ALTER TABLE `categorie`
  ADD PRIMARY KEY (`idCategorie`);

--
-- Index pour la table `commande`
--
ALTER TABLE `commande`
  ADD PRIMARY KEY (`idCommande`),
  ADD KEY `idUser` (`idUser`),
  ADD KEY `idAnimal` (`idAnimal`);

--
-- Index pour la table `panier`
--
ALTER TABLE `panier`
  ADD PRIMARY KEY (`idUser`,`idAnimal`),
  ADD KEY `idAnimal` (`idAnimal`);

--
-- Index pour la table `utilisateur`
--
ALTER TABLE `utilisateur`
  ADD PRIMARY KEY (`idUser`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `animal`
--
ALTER TABLE `animal`
  MODIFY `idAnimal` int(3) NOT NULL AUTO_INCREMENT COMMENT 'Numéro de l''animal', AUTO_INCREMENT=52;

--
-- AUTO_INCREMENT pour la table `categorie`
--
ALTER TABLE `categorie`
  MODIFY `idCategorie` int(2) NOT NULL AUTO_INCREMENT COMMENT 'Numéeo de catégorie', AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT pour la table `commande`
--
ALTER TABLE `commande`
  MODIFY `idCommande` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT pour la table `utilisateur`
--
ALTER TABLE `utilisateur`
  MODIFY `idUser` int(3) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=48;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `animal`
--
ALTER TABLE `animal`
  ADD CONSTRAINT `animal_ibfk_1` FOREIGN KEY (`idCategorie`) REFERENCES `categorie` (`idCategorie`) ON UPDATE CASCADE,
  ADD CONSTRAINT `animal_ibfk_2` FOREIGN KEY (`idUser`) REFERENCES `utilisateur` (`idUser`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Contraintes pour la table `commande`
--
ALTER TABLE `commande`
  ADD CONSTRAINT `commande_ibfk_1` FOREIGN KEY (`idUser`) REFERENCES `utilisateur` (`idUser`) ON DELETE CASCADE,
  ADD CONSTRAINT `commande_ibfk_2` FOREIGN KEY (`idAnimal`) REFERENCES `animal` (`idAnimal`) ON DELETE CASCADE;

--
-- Contraintes pour la table `panier`
--
ALTER TABLE `panier`
  ADD CONSTRAINT `panier_ibfk_1` FOREIGN KEY (`idUser`) REFERENCES `utilisateur` (`idUser`) ON DELETE CASCADE,
  ADD CONSTRAINT `panier_ibfk_2` FOREIGN KEY (`idAnimal`) REFERENCES `animal` (`idAnimal`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
