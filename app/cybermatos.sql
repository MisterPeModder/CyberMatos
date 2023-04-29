-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : sam. 29 avr. 2023 à 20:32
-- Version du serveur : 8.0.27
-- Version de PHP : 7.4.26

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `cybermatos`
--

-- --------------------------------------------------------

--
-- Structure de la table `access_token`
--

DROP TABLE IF EXISTS `access_token`;
CREATE TABLE IF NOT EXISTS `access_token` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id_id` int NOT NULL,
  `value` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `expires_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  PRIMARY KEY (`id`),
  KEY `IDX_B6A2DD689D86650F` (`user_id_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `access_token`
--

INSERT INTO `access_token` (`id`, `user_id_id`, `value`, `created_at`, `expires_at`) VALUES
(1, 1, 'tdnMfJIORaYr3u2Hi9wc9LcaEy-UtQUNHeZMgYzAVFQ', '2023-04-29 20:46:03', '2023-04-30 20:46:03');

-- --------------------------------------------------------

--
-- Structure de la table `cart`
--

DROP TABLE IF EXISTS `cart`;
CREATE TABLE IF NOT EXISTS `cart` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `total_price` double NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_BA388B7A76ED395` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `cart`
--

INSERT INTO `cart` (`id`, `user_id`, `total_price`) VALUES
(1, 1, 0);

-- --------------------------------------------------------

--
-- Structure de la table `cart_product`
--

DROP TABLE IF EXISTS `cart_product`;
CREATE TABLE IF NOT EXISTS `cart_product` (
  `id` int NOT NULL AUTO_INCREMENT,
  `cart_id` int DEFAULT NULL,
  `product_id` int DEFAULT NULL,
  `total_price` double NOT NULL,
  `quantity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_2890CCAA1AD5CDBF` (`cart_id`),
  KEY `IDX_2890CCAA4584665A` (`product_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `cart_product`
--

INSERT INTO `cart_product` (`id`, `cart_id`, `product_id`, `total_price`, `quantity`) VALUES
(1, 1, 4, 4025.6, 2),
(4, 1, 3, 129.8, 2);

-- --------------------------------------------------------

--
-- Structure de la table `doctrine_migration_versions`
--

DROP TABLE IF EXISTS `doctrine_migration_versions`;
CREATE TABLE IF NOT EXISTS `doctrine_migration_versions` (
  `version` varchar(191) COLLATE utf8_unicode_ci NOT NULL,
  `executed_at` datetime DEFAULT NULL,
  `execution_time` int DEFAULT NULL,
  PRIMARY KEY (`version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8_unicode_ci;

--
-- Déchargement des données de la table `doctrine_migration_versions`
--

INSERT INTO `doctrine_migration_versions` (`version`, `executed_at`, `execution_time`) VALUES
('DoctrineMigrations\\Version20230425131444', '2023-04-29 20:19:24', 716);

-- --------------------------------------------------------

--
-- Structure de la table `messenger_messages`
--

DROP TABLE IF EXISTS `messenger_messages`;
CREATE TABLE IF NOT EXISTS `messenger_messages` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `body` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `headers` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue_name` varchar(190) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL,
  `available_at` datetime NOT NULL,
  `delivered_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_75EA56E0FB7336F0` (`queue_name`),
  KEY `IDX_75EA56E0E3BD61CE` (`available_at`),
  KEY `IDX_75EA56E016BA31DB` (`delivered_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `order`
--

DROP TABLE IF EXISTS `order`;
CREATE TABLE IF NOT EXISTS `order` (
  `id` int NOT NULL AUTO_INCREMENT,
  `applicant_id` int DEFAULT NULL,
  `created_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `total_price` double DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_F529939897139001` (`applicant_id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `order`
--

INSERT INTO `order` (`id`, `applicant_id`, `created_at`, `total_price`) VALUES
(1, 1, '2023-04-29 22:10:27', 4025.6);

-- --------------------------------------------------------

--
-- Structure de la table `product`
--

DROP TABLE IF EXISTS `product`;
CREATE TABLE IF NOT EXISTS `product` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `price` double DEFAULT NULL,
  `photo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `product`
--

INSERT INTO `product` (`id`, `name`, `description`, `created_at`, `price`, `photo`) VALUES
(1, 'IBM M1G', 'IBM 6269 modèle M1G, Processeur Intel Pentium III, 668 MHzÉdition 2002, Éditions familiales, Unités Kingston, dimensions 13.5×42×41 cm', '2023-04-29 20:36:06', 1563.9, 'image/IBM.png'),
(2, 'Windows 95', 'Ordinateur PC avec tour, Édition Windows 95 - Années 90. Système d\'exploitation de Microsoft', '2023-04-29 20:48:03', 357.2, 'image/windows95.png'),
(3, 'Souris filaire', 'La souris à deux boutons, utilisé dans les années 90', '2023-04-29 20:48:54', 64.9, 'image/mouse.png'),
(4, 'Commodore 64', 'Première machine vendue à plusieurs millions d\'exemplaires, il reste le modèle d\'ordinateur personnel le plus vendu à ce jour, selon le Livre Guinness des records.', '2023-04-29 20:49:47', 2012.8, 'image/clavier-commodore.png'),
(5, 'Apple Macintosh', 'Ordinateur personnel classique, original avec clavier à l\'écran. On en trouve parfois dans les musées.', '2023-04-29 20:50:32', 897.3, 'image/mac.png'),
(6, 'Intel i486 SX', 'Microprocesseur de la famille des x86 Intel i486 SX (80486SX), architecture CISC - 1989, founit dans un support sur une carte mère.', '2023-04-29 20:51:14', 165.9, 'image/intel-i486.png'),
(7, 'Disquette 8 pouces', 'Nouveau système d’enregistrement qui peut stocker jusqu’à 256 Kio et dispose d’un mode lecture/écriture.', '2023-04-29 20:51:52', 346.8, 'image/disquettes.png'),
(8, 'Atari Inc.', 'Jouer à Pong sur cet ordinateur grand public - 1979 - Atari OS Processeur : MOS Technology 6502B @ 1.8 Mhz - RAM : 8kB base, 48 kB max. Affichage : 320x192 monochrome, 160x96 avec 128 couleurs', '2023-04-29 20:52:31', 456.5, 'image/atari.png'),
(9, 'New product', 'description', '2023-04-29 22:05:30', 456.5, 'image/image.png');

-- --------------------------------------------------------

--
-- Structure de la table `product_order`
--

DROP TABLE IF EXISTS `product_order`;
CREATE TABLE IF NOT EXISTS `product_order` (
  `product_id` int NOT NULL,
  `order_id` int NOT NULL,
  PRIMARY KEY (`product_id`,`order_id`),
  KEY `IDX_5475E8C44584665A` (`product_id`),
  KEY `IDX_5475E8C48D9F6D38` (`order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `product_order`
--

INSERT INTO `product_order` (`product_id`, `order_id`) VALUES
(4, 1);

-- --------------------------------------------------------

--
-- Structure de la table `user`
--

DROP TABLE IF EXISTS `user`;
CREATE TABLE IF NOT EXISTS `user` (
  `id` int NOT NULL AUTO_INCREMENT,
  `login` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(180) COLLATE utf8mb4_unicode_ci NOT NULL,
  `firstname` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `lastname` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `roles` json NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_8D93D649AA08CB10` (`login`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `user`
--

INSERT INTO `user` (`id`, `login`, `email`, `firstname`, `lastname`, `roles`, `password`) VALUES
(1, 'Mike', 'mike@tech.fr', 'Mike', 'Tech', '[]', '$2y$13$LrhuUL9pYKwK2nrpR/25N.ZNmnq6e5ej8dtTlWd19rKj2emMgbGCW');

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `access_token`
--
ALTER TABLE `access_token`
  ADD CONSTRAINT `FK_B6A2DD689D86650F` FOREIGN KEY (`user_id_id`) REFERENCES `user` (`id`);

--
-- Contraintes pour la table `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `FK_BA388B7A76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`);

--
-- Contraintes pour la table `cart_product`
--
ALTER TABLE `cart_product`
  ADD CONSTRAINT `FK_2890CCAA1AD5CDBF` FOREIGN KEY (`cart_id`) REFERENCES `cart` (`id`),
  ADD CONSTRAINT `FK_2890CCAA4584665A` FOREIGN KEY (`product_id`) REFERENCES `product` (`id`);

--
-- Contraintes pour la table `order`
--
ALTER TABLE `order`
  ADD CONSTRAINT `FK_F529939897139001` FOREIGN KEY (`applicant_id`) REFERENCES `user` (`id`);

--
-- Contraintes pour la table `product_order`
--
ALTER TABLE `product_order`
  ADD CONSTRAINT `FK_5475E8C44584665A` FOREIGN KEY (`product_id`) REFERENCES `product` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `FK_5475E8C48D9F6D38` FOREIGN KEY (`order_id`) REFERENCES `order` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
