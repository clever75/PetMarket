-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : lun. 16 mars 2026 à 03:08
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
  `idAnimal` int(11) NOT NULL COMMENT 'Numéro de l''animal',
  `nom` varchar(20) NOT NULL COMMENT 'Nom de l''animal',
  `age` varchar(20) DEFAULT NULL,
  `prix` int(11) NOT NULL COMMENT 'Prix de l''animal',
  `description` text DEFAULT NULL,
  `statut` varchar(20) NOT NULL DEFAULT 'disponible',
  `photo` varchar(255) NOT NULL COMMENT 'Photo de l''animal\r\n',
  `idCategorie` int(11) NOT NULL COMMENT 'Numéro catégorie',
  `idUser` int(11) DEFAULT NULL,
  `date_ajout` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `animal`
--

INSERT INTO `animal` (`idAnimal`, `nom`, `age`, `prix`, `description`, `statut`, `photo`, `idCategorie`, `idUser`, `date_ajout`) VALUES
(1, 'Rex', '2 ans', 35000, 'Rex est un berger allemand énergique et fidèle. Il adore jouer et se promener. Idéal pour une famille active.', 'disponible', 'dog0.jpg', 1, NULL, '2026-03-16 01:58:16'),
(2, 'Boby', '2 ans', 40000, 'Boby est un chien très affectueux et sociable. Il s\'entend bien avec les enfants et les autres animaux.', 'disponible', 'dog1.jpg', 1, NULL, '2026-03-16 01:58:16'),
(3, 'Rocky', '2 ans', 35000, 'Rocky est un chien courageux et intelligent. Facile à dresser, il apprend vite les commandes de base.', 'disponible', 'dog2.jpg', 1, NULL, '2026-03-16 01:58:16'),
(4, 'Bunny', '5 mois', 8000, 'Bunny est un petit lapin doux et calme. Parfait pour un appartement. Il aime les carottes et les câlins.', 'disponible', 'rabbit0.jpg', 3, NULL, '2026-03-16 01:58:16'),
(5, 'Carotte', '8 mois', 15000, 'Carotte est une lapine curieuse et joueuse. Elle est propre et facile à entretenir.', 'disponible', 'rabbit2.jpg', 3, NULL, '2026-03-16 01:58:16'),
(6, 'Nougat', '1 an', 12000, 'Nougat est un lapin nain très doux. Il est habitué à vivre en intérieur et adore être caressé.', 'disponible', 'rabbit3.jpg', 3, NULL, '2026-03-16 01:58:16'),
(7, 'Coco', '1 an', 35000, 'Coco est un perroquet coloré qui sait déjà dire quelques mots. Il aime la compagnie et la musique.', 'disponible', 'bird0.jpg', 4, NULL, '2026-03-16 01:58:16'),
(8, 'Kiwi', '9 mois', 8000, 'Kiwi est un petit oiseau chanteur très vif. Son chant est mélodieux et agréable à écouter.', 'disponible', 'bird1.jpg', 4, NULL, '2026-03-16 01:58:16'),
(9, 'Pico', '3 ans', 40000, 'Pico est un perroquet mature et calme. Il est bien apprivoisé et s\'adapte facilement à son environnement.', 'disponible', 'bird2.jpg', 4, NULL, '2026-03-16 01:58:16'),
(10, 'Mimi', '8 mois', 15000, 'Mimi est une chatte douce et câline. Elle est propre, vaccinée et habituée à vivre en appartement.', 'disponible', 'cat0.jpg', 2, NULL, '2026-03-16 01:58:16'),
(11, 'Nala', '1 an', 20000, 'Nala est une chatte élégante et indépendante. Elle est très propre et s\'entend bien avec les humains.', 'disponible', 'cat1.jpg', 2, NULL, '2026-03-16 01:58:16'),
(12, 'Choco', '2 ans', 45000, 'Choco est un chat majestueux au pelage brun. Il est calme, affectueux et adore dormir au soleil.', 'disponible', 'cat2.jpg', 2, NULL, '2026-03-16 01:58:16'),
(25, 'Maya', '2 ans', 25000, 'Maya est une chatte jeune et espiègle. Elle adore jouer avec des jouets et grimper partout.', 'disponible', 'cat3.jpg', 2, NULL, '2026-03-16 01:58:16'),
(26, 'Luna', '2 ans', 12000, 'Luna est une petite chatte timide mais très douce. Elle s\'attache vite à son maître.', 'disponible', 'cat4.jpg', 2, NULL, '2026-03-16 01:58:16'),
(27, 'Kitty', '3 ans', 30000, 'Kitty est une chatte sociable et affectueuse. Elle ronronne dès qu\'on la prend dans les bras.', 'disponible', 'cat5.jpg', 2, NULL, '2026-03-16 01:58:16'),
(28, 'Bella', '2 ans', 15000, 'Bella est une chatte joueuse et curieuse. Elle est en bonne santé et mange bien.', 'disponible', 'cat5.jpg', 2, NULL, '2026-03-16 01:58:16'),
(29, 'Tigrou', '3 ans', 20000, 'Tigrou est un chat rayé plein de caractère. Il est actif et aime explorer son territoire.', 'disponible', 'cat6.jpg', 2, NULL, '2026-03-16 01:58:16'),
(30, 'Zara', '3 ans', 25000, 'Zara est une chatte calme et élégante. Elle est habituée aux enfants et très patiente.', 'disponible', 'cat7.jpg', 2, NULL, '2026-03-16 01:58:16'),
(31, 'Oscar', '5 ans', 25000, 'Oscar est un chien mature et bien éduqué. Il connaît les commandes de base et est très obéissant.', 'disponible', 'dog7.jpg', 1, NULL, '2026-03-16 01:58:16'),
(32, 'Thor', '6 ans', 55000, 'Thor est un grand chien puissant et protecteur. Il est loyal et très attaché à sa famille.', 'disponible', 'dog3.jpg', 1, NULL, '2026-03-16 01:58:16'),
(33, 'Pixel', '3 ans', 30000, 'Pixel est un chat moderne et élégant. Il est propre, vacciné et cherche une famille aimante.', 'disponible', 'cat9.jpg', 2, NULL, '2026-03-16 01:58:16');

-- --------------------------------------------------------

--
-- Structure de la table `categorie`
--

CREATE TABLE `categorie` (
  `idCategorie` int(11) NOT NULL COMMENT 'Numéeo de catégorie',
  `libelle` varchar(50) NOT NULL COMMENT 'Libellé de la catégorie'
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
-- Structure de la table `panier`
--

CREATE TABLE `panier` (
  `idUser` int(11) NOT NULL,
  `idAnimal` int(11) NOT NULL,
  `quantite` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `panier`
--

INSERT INTO `panier` (`idUser`, `idAnimal`, `quantite`) VALUES
(26, 31, 1),
(26, 32, 1);

-- --------------------------------------------------------

--
-- Structure de la table `utilisateur`
--

CREATE TABLE `utilisateur` (
  `idUser` int(11) NOT NULL,
  `nom` varchar(100) DEFAULT NULL,
  `pseudo` varchar(50) DEFAULT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `telephone` varchar(20) DEFAULT NULL,
  `ville` varchar(100) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `role` varchar(20) NOT NULL DEFAULT 'acheteur',
  `date_inscription` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_admin` tinyint(1) NOT NULL DEFAULT 0,
  `seller_request` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `utilisateur`
--

INSERT INTO `utilisateur` (`idUser`, `nom`, `pseudo`, `photo`, `telephone`, `ville`, `email`, `password`, `role`, `date_inscription`, `is_admin`, `seller_request`) VALUES
(25, 'Clever', 'Clever01', NULL, NULL, NULL, 'cleverassan696@gmail.com', '$2y$10$ioWmMFUEkuyb6N24ywy8..cnM47215jkauOtXBNS3m5.Z8Sc.ad6a', 'acheteur', '2026-03-15 16:12:08', 0, 0),
(26, 'Chabane', 'Escanor', NULL, NULL, NULL, 'chabane@gmail.com', '$2y$10$3E84sBSPqdcSd3Uqa5Olt.rvBbV9RmadrO18iIVA0QEH95FYCRIg6', 'vendeur', '2026-03-15 16:18:02', 0, 0);

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
  MODIFY `idAnimal` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Numéro de l''animal', AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT pour la table `categorie`
--
ALTER TABLE `categorie`
  MODIFY `idCategorie` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Numéeo de catégorie', AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT pour la table `utilisateur`
--
ALTER TABLE `utilisateur`
  MODIFY `idUser` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

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
-- Contraintes pour la table `panier`
--
ALTER TABLE `panier`
  ADD CONSTRAINT `panier_ibfk_1` FOREIGN KEY (`idUser`) REFERENCES `utilisateur` (`idUser`) ON DELETE CASCADE,
  ADD CONSTRAINT `panier_ibfk_2` FOREIGN KEY (`idAnimal`) REFERENCES `animal` (`idAnimal`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
