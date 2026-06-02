-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : mar. 08 juil. 2025 à 01:02
-- Version du serveur : 9.1.0
-- Version de PHP : 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `mylast_biblio`
--
CREATE DATABASE IF NOT EXISTS `mylast_biblio` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci;
USE `mylast_biblio`;

-- --------------------------------------------------------

--
-- Structure de la table `amende`
--

DROP TABLE IF EXISTS `amende`;
CREATE TABLE IF NOT EXISTS `amende` (
  `id_user` int NOT NULL,
  `id_ex` int NOT NULL,
  `montant` decimal(8,2) NOT NULL,
  `raison` enum('usagé','Endommage') NOT NULL,
  PRIMARY KEY (`id_user`,`id_ex`),
  KEY `id_ex` (`id_ex`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `amende`
--

INSERT INTO `amende` (`id_user`, `id_ex`, `montant`, `raison`) VALUES
(14, 4, 11.00, 'Endommage');

-- --------------------------------------------------------

--
-- Structure de la table `biblio`
--

DROP TABLE IF EXISTS `biblio`;
CREATE TABLE IF NOT EXISTS `biblio` (
  `id_bib` int NOT NULL AUTO_INCREMENT,
  `nom` varchar(50) DEFAULT NULL,
  `prenom` varchar(50) DEFAULT NULL,
  `poste` enum('Stagiaire','Principal') NOT NULL,
  `embauche` date NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `mot_de_passe` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id_bib`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `biblio`
--

INSERT INTO `biblio` (`id_bib`, `nom`, `prenom`, `poste`, `embauche`, `email`, `mot_de_passe`) VALUES
(3, 'ahmed', 'Ali', 'Stagiaire', '2024-06-15', 'ahmedAli@gmail.com', '1234'),
(4, 'Malak', 'El Khalfi', 'Stagiaire', '2024-09-01', 'malakELkhalfi@gmail.com', '123456'),
(5, 'Youssef', 'Benhaddou', 'Principal', '2022-03-22', 'youssefBenhaddou@gmail.com', 'tata'),
(6, 'Amina', 'Mansouri', 'Stagiaire', '2024-07-15', 'aminamansouri@gmail.com', 'toto');

-- --------------------------------------------------------

--
-- Structure de la table `doc`
--

DROP TABLE IF EXISTS `doc`;
CREATE TABLE IF NOT EXISTS `doc` (
  `id_doc` int NOT NULL AUTO_INCREMENT,
  `ref` varchar(100) NOT NULL,
  `editeur` varchar(255) NOT NULL,
  `annee` int NOT NULL,
  `titre` varchar(255) NOT NULL,
  `cat` enum('livre','periodique','autre') NOT NULL,
  PRIMARY KEY (`id_doc`),
  UNIQUE KEY `ref` (`ref`)
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `doc`
--

INSERT INTO `doc` (`id_doc`, `ref`, `editeur`, `annee`, `titre`, `cat`) VALUES
(1, 'REF001', 'Editions Dupont', 2020, 'La programmation SQL pour les débutants', 'livre'),
(2, 'REF002', 'Presses Universitaires', 2019, 'Les bases de données relationnelles', 'livre'),
(3, 'REF003', 'Science Pub', 2021, 'Revue des Sciences Informatique', 'periodique'),
(4, 'REF004', 'Editions de la Technologie', 2022, 'Les tendances du Big Data', 'livre'),
(5, 'REF005', 'Global Press', 2021, 'Journal of Computing', 'periodique'),
(6, 'REF006', 'Penguin Books', 2018, 'Learning Python', 'livre'),
(7, 'REF007', 'HarperCollins', 2020, 'Data Science for Dummies', 'livre'),
(8, 'REF008', 'O\'Reilly Media', 2021, 'Deep Learning with Python', 'livre'),
(9, 'REF009', 'Packt Publishing', 2022, 'Mastering JavaScript', 'livre'),
(10, 'REF010', 'Wiley', 2019, 'Introduction to Algorithms', 'livre'),
(11, 'REF011', 'Springer', 2021, 'Journal of Machine Learning', 'periodique'),
(12, 'REF012', 'Elsevier', 2020, 'Computer Science Review', 'periodique'),
(13, 'REF013', 'IEEE', 2022, 'Journal of Computing and Information Technology', 'periodique'),
(14, 'REF014', 'Nature Publishing', 2021, 'Nature Electronics', 'periodique'),
(15, 'REF015', 'Oxford University Press', 2020, 'Oxford Journal of Computer Science', 'periodique'),
(16, 'REF021', 'McGraw Hill', 2022, 'Artificial Intelligence', 'livre'),
(17, 'REF022', 'Springer', 2020, 'Data Mining Techniques', 'livre'),
(18, 'REF023', 'Pearson', 2021, 'Advanced SQL Techniques', 'livre'),
(19, 'REF024', 'ACM', 2021, 'ACM Computing Surveys', 'periodique'),
(20, 'REF025', 'Wiley', 2020, 'International Journal of Computer Science', 'periodique');

-- --------------------------------------------------------

--
-- Structure de la table `emprunter`
--

DROP TABLE IF EXISTS `emprunter`;
CREATE TABLE IF NOT EXISTS `emprunter` (
  `id_user` int NOT NULL,
  `id_ex` int NOT NULL,
  `date_emprunt` date NOT NULL,
  `date_retour` date DEFAULT NULL,
  PRIMARY KEY (`id_user`,`id_ex`),
  KEY `id_ex` (`id_ex`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `emprunter`
--

INSERT INTO `emprunter` (`id_user`, `id_ex`, `date_emprunt`, `date_retour`) VALUES
(1, 20, '2024-12-26', '2024-12-25'),
(1, 22, '2024-12-26', '2025-01-25'),
(1, 58, '2024-12-27', '2025-01-26'),
(2, 23, '2024-12-26', '2025-01-25'),
(2, 42, '2024-12-26', '2025-01-27'),
(2, 71, '2024-12-26', '2025-01-25'),
(3, 16, '2024-12-26', '2025-01-25'),
(4, 10, '2024-12-28', '2025-01-29'),
(4, 11, '2024-12-26', '2025-01-25'),
(4, 26, '2024-12-28', '2025-01-27'),
(4, 29, '2024-12-28', '2025-01-27'),
(5, 24, '2024-12-26', '2025-01-25'),
(6, 18, '2024-12-26', '2025-01-25'),
(7, 25, '2024-12-26', '2025-01-25'),
(8, 21, '2024-12-26', '2025-01-25'),
(8, 33, '2024-12-26', '2025-01-25'),
(9, 30, '2024-12-26', '2025-01-25'),
(10, 65, '2024-12-26', '2025-01-25'),
(11, 17, '2024-12-26', '2025-01-25'),
(12, 14, '2024-12-26', '2025-01-25'),
(12, 27, '2024-12-26', '2025-01-25'),
(12, 38, '2024-12-26', '2025-01-25'),
(12, 39, '2024-12-26', '2025-01-25'),
(12, 74, '2024-12-26', '2025-01-25'),
(12, 79, '2024-12-26', '2025-01-25'),
(13, 15, '2024-12-27', '2025-01-26'),
(13, 19, '2024-12-26', '2025-01-25'),
(13, 75, '2024-12-26', '2025-01-25'),
(13, 80, '2024-12-26', '2025-01-25'),
(14, 76, '2024-09-01', '2024-10-03'),
(15, 34, '2024-12-26', '2025-01-25'),
(15, 66, '2024-12-26', '2025-01-25'),
(16, 37, '2024-12-26', '2025-01-25'),
(16, 48, '2024-12-26', '2025-01-25'),
(16, 77, '2024-10-12', '2024-12-01'),
(16, 78, '2024-12-26', '2025-01-25'),
(19, 6, '2025-01-03', '2025-02-02'),
(19, 9, '2025-01-03', '2025-02-02'),
(19, 102, '2025-01-03', '2025-02-02');

-- --------------------------------------------------------

--
-- Structure de la table `exemp`
--

DROP TABLE IF EXISTS `exemp`;
CREATE TABLE IF NOT EXISTS `exemp` (
  `id_ex` int NOT NULL AUTO_INCREMENT,
  `id_doc` int NOT NULL,
  `achat` date NOT NULL,
  `etat` enum('bon','neuf','très bon','endommagé','usagé') NOT NULL,
  `statut` enum('en prêt','en retard','en rayon','en réserve','en travaux') NOT NULL,
  `prix` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id_ex`),
  KEY `id_doc` (`id_doc`)
) ENGINE=InnoDB AUTO_INCREMENT=105 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `exemp`
--

INSERT INTO `exemp` (`id_ex`, `id_doc`, `achat`, `etat`, `statut`, `prix`) VALUES
(1, 1, '2023-01-10', 'usagé', 'en travaux', 25.00),
(2, 2, '2023-02-15', 'usagé', 'en travaux', 30.00),
(3, 3, '2023-05-20', 'bon', 'en réserve', 12.00),
(4, 4, '2023-03-18', 'endommagé', 'en travaux', 18.00),
(5, 5, '2023-06-05', 'usagé', 'en réserve', 10.00),
(6, 6, '2023-01-12', 'neuf', 'en prêt', 40.00),
(7, 7, '2023-02-08', 'très bon', 'en prêt', 50.00),
(8, 8, '2023-03-05', 'neuf', 'en rayon', 45.00),
(9, 9, '2023-04-15', 'neuf', 'en prêt', 29.99),
(10, 10, '2023-05-20', 'neuf', 'en prêt', 55.00),
(11, 11, '2023-06-15', 'bon', 'en prêt', 10.00),
(12, 12, '2023-07-10', 'neuf', 'en prêt', 12.50),
(13, 13, '2023-08-03', 'très bon', 'en rayon', 8.00),
(14, 14, '2023-09-25', 'usagé', 'en prêt', 18.00),
(15, 15, '2023-10-12', 'bon', 'en prêt', 22.50),
(16, 16, '2023-01-10', 'neuf', 'en prêt', 60.00),
(17, 17, '2023-02-15', 'très bon', 'en prêt', 70.00),
(18, 18, '2023-03-20', 'neuf', 'en prêt', 40.00),
(19, 19, '2023-04-10', 'bon', 'en prêt', 16.00),
(20, 20, '2023-05-05', 'neuf', 'en retard', 14.00),
(21, 1, '2023-06-01', 'très bon', 'en prêt', 25.00),
(22, 1, '2023-06-02', 'très bon', 'en prêt', 25.00),
(23, 1, '2023-06-03', 'bon', 'en prêt', 25.00),
(24, 2, '2023-06-01', 'neuf', 'en prêt', 15.00),
(25, 2, '2023-06-02', 'très bon', 'en prêt', 15.00),
(26, 2, '2023-06-03', 'bon', 'en prêt', 15.00),
(27, 3, '2023-06-01', 'neuf', 'en prêt', 35.00),
(29, 3, '2023-06-03', 'bon', 'en prêt', 35.00),
(30, 4, '2023-06-01', 'neuf', 'en prêt', 28.00),
(31, 4, '2023-06-02', 'très bon', 'en rayon', 28.00),
(32, 4, '2023-06-03', 'bon', 'en rayon', 28.00),
(33, 5, '2023-06-01', 'neuf', 'en prêt', 18.00),
(34, 5, '2023-06-02', 'très bon', 'en prêt', 18.00),
(35, 5, '2023-06-03', 'bon', 'en rayon', 18.00),
(36, 6, '2023-06-01', 'neuf', 'en rayon', 23.00),
(37, 6, '2023-06-02', 'très bon', 'en prêt', 23.00),
(38, 6, '2023-06-03', 'bon', 'en prêt', 23.00),
(39, 7, '2023-06-01', 'neuf', 'en prêt', 45.00),
(40, 7, '2023-06-02', 'très bon', 'en rayon', 45.00),
(41, 7, '2023-06-03', 'bon', 'en rayon', 45.00),
(42, 8, '2023-06-01', 'neuf', 'en prêt', 30.00),
(43, 8, '2023-06-02', 'très bon', 'en rayon', 30.00),
(44, 8, '2023-06-03', 'bon', 'en rayon', 30.00),
(45, 9, '2023-06-01', 'neuf', 'en rayon', 50.00),
(46, 9, '2023-06-02', 'très bon', 'en rayon', 50.00),
(47, 9, '2023-06-03', 'bon', 'en rayon', 50.00),
(48, 10, '2023-06-01', 'neuf', 'en prêt', 32.00),
(49, 10, '2023-06-02', 'très bon', 'en rayon', 32.00),
(50, 10, '2023-06-03', 'bon', 'en rayon', 32.00),
(51, 11, '2023-06-01', 'neuf', 'en rayon', 40.00),
(52, 11, '2023-06-02', 'très bon', 'en rayon', 40.00),
(53, 11, '2023-06-03', 'bon', 'en rayon', 40.00),
(54, 12, '2023-06-01', 'très bon', 'en réserve', 55.00),
(55, 12, '2023-06-02', 'très bon', 'en rayon', 55.00),
(56, 12, '2023-06-03', 'bon', 'en rayon', 55.00),
(57, 13, '2023-06-01', 'neuf', 'en rayon', 26.00),
(58, 13, '2023-06-02', 'très bon', 'en prêt', 26.00),
(59, 13, '2023-06-03', 'bon', 'en rayon', 26.00),
(60, 14, '2023-06-01', 'neuf', 'en rayon', 33.00),
(61, 14, '2023-06-02', 'très bon', 'en rayon', 33.00),
(62, 14, '2023-06-03', 'bon', 'en rayon', 33.00),
(63, 15, '2023-06-01', 'neuf', 'en rayon', 22.00),
(64, 15, '2023-06-02', 'très bon', 'en rayon', 22.00),
(65, 15, '2023-06-03', 'bon', 'en prêt', 22.00),
(66, 16, '2023-06-01', 'neuf', 'en prêt', 60.00),
(67, 16, '2023-06-02', 'très bon', 'en rayon', 60.00),
(68, 16, '2023-06-03', 'bon', 'en rayon', 60.00),
(69, 17, '2023-06-01', 'neuf', 'en rayon', 25.00),
(70, 17, '2023-06-02', 'très bon', 'en rayon', 25.00),
(71, 17, '2023-06-03', 'bon', 'en prêt', 25.00),
(72, 18, '2023-06-01', 'endommagé', 'en travaux', 19.00),
(74, 18, '2023-06-03', 'bon', 'en prêt', 19.00),
(75, 19, '2023-06-01', 'neuf', 'en prêt', 20.00),
(76, 19, '2023-06-02', 'très bon', 'en retard', 20.00),
(77, 19, '2023-06-03', 'bon', 'en retard', 20.00),
(78, 20, '2023-06-01', 'neuf', 'en prêt', 30.00),
(79, 20, '2023-06-02', 'très bon', 'en prêt', 30.00),
(80, 20, '2023-06-03', 'bon', 'en prêt', 30.00),
(81, 1, '2023-11-23', 'neuf', 'en réserve', 19.99),
(82, 2, '2023-12-01', 'bon', 'en réserve', 14.95),
(83, 3, '2023-12-05', 'très bon', 'en réserve', 25.99),
(84, 4, '2023-12-10', 'neuf', 'en réserve', 12.99),
(85, 5, '2023-12-15', 'usagé', 'en réserve', 8.99),
(86, 6, '2023-12-20', 'bon', 'en réserve', 16.99),
(87, 7, '2023-12-25', 'neuf', 'en réserve', 21.99),
(88, 8, '2024-01-01', 'très bon', 'en réserve', 23.99),
(89, 9, '2024-01-05', 'bon', 'en réserve', 15.99),
(90, 10, '2024-01-10', 'neuf', 'en réserve', 18.99),
(91, 11, '2024-01-15', 'usagé', 'en réserve', 9.99),
(92, 12, '2024-01-20', 'bon', 'en réserve', 17.99),
(93, 13, '2024-01-25', 'neuf', 'en réserve', 22.99),
(94, 14, '2024-02-01', 'très bon', 'en réserve', 24.99),
(95, 15, '2024-02-05', 'bon', 'en réserve', 16.99),
(96, 16, '2024-02-10', 'neuf', 'en réserve', 19.99),
(97, 17, '2024-02-15', 'usagé', 'en réserve', 10.99),
(98, 18, '2024-02-20', 'bon', 'en réserve', 18.99),
(99, 19, '2024-02-25', 'neuf', 'en réserve', 23.99),
(100, 20, '2024-01-15', 'neuf', 'en réserve', 12.99),
(101, 2, '2024-12-28', 'neuf', 'en rayon', 32.00),
(102, 2, '2024-12-28', 'neuf', 'en prêt', 32.00),
(103, 4, '2024-12-28', 'neuf', 'en rayon', 32.00);

-- --------------------------------------------------------

--
-- Structure de la table `livre`
--

DROP TABLE IF EXISTS `livre`;
CREATE TABLE IF NOT EXISTS `livre` (
  `isbn` varchar(13) NOT NULL,
  `id_doc` int NOT NULL,
  `auteurs` text NOT NULL,
  `prixl` decimal(10,2) NOT NULL,
  PRIMARY KEY (`isbn`),
  KEY `id_doc` (`id_doc`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `livre`
--

INSERT INTO `livre` (`isbn`, `id_doc`, `auteurs`, `prixl`) VALUES
('6530128', 30, 'Markus Winand', 23.00),
('978-013409341', 6, 'Mark Lutz', 41.00),
('978-026203384', 10, 'Thomas H. Cormen', 55.00),
('978-098754321', 17, 'Jane Smith', 39.99),
('978-111974276', 7, 'Lillian Pierson', 50.00),
('978-112233445', 4, 'Michael Johnson', 22.80),
('978-12334455', 18, 'Michael Johnson', 50.00),
('978-123456789', 1, 'John Doe', 29.99),
('978-123456890', 16, 'John Doe', 45.99),
('978-161729526', 8, 'Francois Chollet', 45.00),
('978-183921938', 9, 'John Resig', 29.99),
('978-987654321', 2, 'Jane Smith', 35.50);

-- --------------------------------------------------------

--
-- Structure de la table `perio`
--

DROP TABLE IF EXISTS `perio`;
CREATE TABLE IF NOT EXISTS `perio` (
  `issn` varchar(8) NOT NULL,
  `id_doc` int NOT NULL,
  `num` int NOT NULL,
  `vol` int NOT NULL,
  `prixp` decimal(10,2) NOT NULL,
  PRIMARY KEY (`issn`),
  KEY `id_doc` (`id_doc`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `perio`
--

INSERT INTO `perio` (`issn`, `id_doc`, `num`, `vol`, `prixp`) VALUES
('1234-567', 3, 10, 2, 15.99),
('129-5678', 19, 5, 12, 25.50),
('1567-890', 14, 5, 6, 18.00),
('2345-678', 11, 15, 5, 10.00),
('2345-987', 15, 3, 2, 22.50),
('5432-876', 13, 10, 4, 18.00),
('885-4321', 20, 3, 8, 20.75),
('9876-123', 12, 8, 3, 12.50),
('9876-543', 5, 20, 3, 12.50);

-- --------------------------------------------------------

--
-- Structure de la table `user`
--

DROP TABLE IF EXISTS `user`;
CREATE TABLE IF NOT EXISTS `user` (
  `id_user` int NOT NULL AUTO_INCREMENT,
  `nom` varchar(50) DEFAULT NULL,
  `prenom` varchar(50) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `tel` varchar(15) DEFAULT NULL,
  `type` enum('Occasionnel','Abonne','Priviliegie') DEFAULT NULL,
  `inscr` date DEFAULT NULL,
  `interdit` tinyint(1) DEFAULT '0',
  `dateNaissance` date DEFAULT NULL,
  `ville` varchar(100) DEFAULT NULL,
  `codePostal` varchar(10) DEFAULT NULL,
  `id_bib` int DEFAULT '5',
  PRIMARY KEY (`id_user`),
  UNIQUE KEY `email` (`email`),
  KEY `idx_email` (`email`),
  KEY `id_bib` (`id_bib`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `user`
--

INSERT INTO `user` (`id_user`, `nom`, `prenom`, `email`, `tel`, `type`, `inscr`, `interdit`, `dateNaissance`, `ville`, `codePostal`, `id_bib`) VALUES
(1, 'El Habib', 'Ahmed', 'ahmed.elhabib@gmail.com', '0612345678', 'Abonne', '2022-01-01', 0, '1999-05-15', 'Casablanca', '20000', 5),
(2, 'Ben Ali', 'Mohamed', 'mohamed.benali@gmail.com', '0623456789', 'Priviliegie', '2022-02-10', 0, '1985-11-22', 'Rabat', '10000', 5),
(3, 'Jabri', 'Khadija', 'khadija.jabri@hotmail.com', '0634567890', 'Abonne', '2022-03-05', 0, '1992-07-30', 'Marrakech', '20050', 5),
(4, 'Toumi', 'Sami', 'sami.toumi@gmail.com', '0656789012', 'Priviliegie', '2022-04-12', 0, '1988-03-10', 'Fès', '40000', 5),
(5, 'Boukhriss', 'Nawal', 'nawal.boukhriss@yahoo.com', '0678901234', 'Occasionnel', '2022-05-19', 0, '1995-08-12', 'Agadir', '90000', 5),
(6, 'Oujdi', 'Yassine', 'yassine.oujdi@gmail.com', '0689012345', 'Abonne', '2022-06-25', 0, '1980-02-01', 'Oujda', '50000', 5),
(7, 'Fayad', 'Salima', 'salima.fayad@outlook.com', '0690123456', 'Occasionnel', '2022-07-15', 0, '1991-06-05', 'Tanger', '60000', 5),
(8, 'wakili', 'Sarah', 'sarah.wakili@gmail.com', '0601234567', 'Abonne', '2022-08-21', 0, '1987-04-18', 'Meknès', '30000', 5),
(9, 'berrada', 'Ryad', 'ryad.berrada@hotmail.com', '0612345678', 'Abonne', '2022-09-30', 0, '1993-01-10', 'Ouarzazate', '70000', 5),
(10, 'Bensaid', 'Layla', 'layla.bensaid@gmail.com', '0623456789', 'Occasionnel', '2022-10-12', 0, '1990-09-27', 'Kénitra', '11000', 5),
(11, 'Najib', 'Khalid', 'khalid.najib@yahoo.com', '0634567890', 'Abonne', '2022-11-22', 0, '1986-12-02', 'Tétouan', '80000', 5),
(12, 'El Azizi', 'Sofia', 'sofia.elazizi@gmail.com', '0656789012', 'Priviliegie', '2022-12-18', 0, '1994-03-14', 'Nador', '12000', 5),
(13, 'Benkirane', 'Mohamed', 'mohamed.benkirane@example.com', '0612345678', 'Abonne', '2023-01-15', 0, '1990-06-15', 'Casablanca', '20000', 5),
(14, 'Elkadi', 'Rachid', 'rachid.elkadi@example.com', '0701234567', 'Abonne', '2023-02-20', 1, '1985-11-20', 'Rabat', '40000', 5),
(15, 'azzouzi', 'Sami', 'sami.azzouzi@example.com', '0623456789', 'Abonne', '2023-03-10', 0, '1992-04-10', 'Fès', '30000', 5),
(16, 'Oued', 'Fatima', 'fatima.oued@example.com', '0661234567', 'Priviliegie', '2023-04-05', 1, '1980-07-25', 'Marrakech', '70000', 5),
(19, 'Jelaidi', 'Kaoutar', 'kaoutarjelaidi@gmail.com', '0621940345', 'Priviliegie', '2024-12-28', 0, '2003-10-18', 'Beni-Mellal', '89863', 5);

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `user`
--
ALTER TABLE `user`
  ADD CONSTRAINT `id_bib` FOREIGN KEY (`id_bib`) REFERENCES `biblio` (`id_bib`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
