-- MariaDB dump 10.19  Distrib 10.4.24-MariaDB, for Win64 (AMD64)
--
-- Host: 127.0.0.1    Database: prwb_2223_xyy
-- ------------------------------------------------------
-- Server version	10.4.24-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `operations`
--

DROP TABLE IF EXISTS `operations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `operations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(256) COLLATE utf8_unicode_ci NOT NULL,
  `tricount` int(11) NOT NULL,
  `amount` double NOT NULL,
  `operation_date` date NOT NULL,
  `initiator` int(11) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `Initiator` (`initiator`),
  KEY `Tricount` (`tricount`),
  CONSTRAINT `operations_ibfk_1` FOREIGN KEY (`initiator`) REFERENCES `users` (`id`),
  CONSTRAINT `operations_ibfk_2` FOREIGN KEY (`tricount`) REFERENCES `tricounts` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `operations`
--

LOCK TABLES `operations` WRITE;
/*!40000 ALTER TABLE `operations` DISABLE KEYS */;
INSERT INTO `operations` VALUES   (1, 'Achat Narya (Anneau de feu)', 1, 100, '2023-02-01', 5, '2023-02-03 15:01:00'),
                                  (2, 'Achat Vilya (Anneau de l&#039;air)', 1, 85, '2023-02-01', 3, '2023-02-03 15:05:00'),
                                  (3, 'Achat Nenya (Anneau de l&#039;eau)', 1, 75, '2023-02-01', 8, '2023-02-03 15:09:00'),
                                  (4, 'Achat Anneau Unique', 1, 150, '2023-02-01', 6, '2023-02-03 15:10:00'),
                                  (5, 'Achat Dard', 2, 50, '2023-02-01', 3, '2023-02-03 15:18:00'),
                                  (6, 'Achat Armure Mithril', 2, 50, '2023-02-01', 10, '2023-02-03 15:15:00'),
                                  (7, 'Feu d&#039;artifice', 2, 25, '2023-02-01', 5, '2023-02-03 15:12:00'),
                                  (8, 'Achat Anduril', 3, 100, '2023-02-01', 3, '2023-02-03 15:06:00'),
                                  (9, 'Achat Hadhafang', 3, 100, '2023-02-01', 3, '2023-02-03 15:11:00');

/*!40000 ALTER TABLE `operations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `repartition_template_items`
--

DROP TABLE IF EXISTS `repartition_template_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `repartition_template_items` (
  `user` int(11) NOT NULL,
  `repartition_template` int(11) NOT NULL,
  `weight` int(11) NOT NULL,
  PRIMARY KEY (`user`,`repartition_template`),
  KEY `Distribution` (`repartition_template`),
  CONSTRAINT `repartition_template_items_ibfk_1` FOREIGN KEY (`repartition_template`) REFERENCES `repartition_templates` (`id`),
  CONSTRAINT `repartition_template_items_ibfk_2` FOREIGN KEY (`user`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `repartition_template_items`
--

LOCK TABLES `repartition_template_items` WRITE;
/*!40000 ALTER TABLE `repartition_template_items` DISABLE KEYS */;
INSERT INTO `repartition_template_items` VALUES (1,3,1), (5,3,2), (3,1,2), (1,1,1), (4,1,1), (5,1,1), (8,1,1), (9,1,1), 
                                                (4,3,1), (5,5,1), (3,2,1), (1,2,1), (8,2,2), (4,2,1), (5,2,1), (9,2,1),
                                                (7,4,1), (8,3,1), (9,3,1), (3,3,1);
/*!40000 ALTER TABLE `repartition_template_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `repartition_templates`
--

DROP TABLE IF EXISTS `repartition_templates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `repartition_templates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(256) COLLATE utf8_unicode_ci NOT NULL,
  `tricount` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `Title` (`title`,`tricount`),
  KEY `Tricount` (`tricount`),
  CONSTRAINT `repartition_templates_ibfk_1` FOREIGN KEY (`tricount`) REFERENCES `tricounts` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `repartition_templates`
--

LOCK TABLES `repartition_templates` WRITE;
/*!40000 ALTER TABLE `repartition_templates` DISABLE KEYS */;
INSERT INTO `repartition_templates` VALUES  (1, 'Elrond paie double', 1), (2, 'Galadriel paie double', 1), (3, 'Olorin paie double', 1),
                                            (4, 'Gollum paie seul', 1), (5, 'Olorin paie seul', 2);
/*!40000 ALTER TABLE `repartition_templates` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `repartitions`
--

DROP TABLE IF EXISTS `repartitions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `repartitions` (
  `operation` int(11) NOT NULL,
  `user` int(11) NOT NULL,
  `weight` int(11) NOT NULL,
  PRIMARY KEY (`operation`,`user`),
  KEY `User` (`user`),
  CONSTRAINT `repartitions_ibfk_1` FOREIGN KEY (`operation`) REFERENCES `operations` (`id`),
  CONSTRAINT `repartitions_ibfk_2` FOREIGN KEY (`user`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `repartitions`
--

LOCK TABLES `repartitions` WRITE;
/*!40000 ALTER TABLE `repartitions` DISABLE KEYS */;
INSERT INTO `repartitions` VALUES (1,1,1), (1,3,1), (1,4,1), (1,5,2), (1,8,1), (1,9,1),
                                  (2,1,1), (2,3,2), (2,4,1), (2,5,1), (2,8,1), (2,9,1),
                                  (3,1,1), (3,3,1), (3,4,1), (3,5,1), (3,8,2), (3,9,1),
                                  (4,7,1),
                                  (5,3,1), (5,4,1), (5,5,1), (5,8,1), (5,10,1),
                                  (6,3,1),
                                  (7,5,1),
                                  (8,3,1), (8,10,1), (8,5,1),
                                  (9,3,1), (9,10,1), (9,5,1);
/*!40000 ALTER TABLE `repartitions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `subscriptions`
--

DROP TABLE IF EXISTS `subscriptions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `subscriptions` (
  `tricount` int(11) NOT NULL,
  `user` int(11) NOT NULL,
  PRIMARY KEY (`tricount`,`user`),
  KEY `User` (`user`),
  CONSTRAINT `subscriptions_ibfk_1` FOREIGN KEY (`tricount`) REFERENCES `tricounts` (`id`),
  CONSTRAINT `subscriptions_ibfk_2` FOREIGN KEY (`user`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `subscriptions`
--

LOCK TABLES `subscriptions` WRITE;
/*!40000 ALTER TABLE `subscriptions` DISABLE KEYS */;
INSERT INTO `subscriptions` VALUES  (1,1), (1,3), (1,4), (1,5), (1,6), (1,7), (1,8), (1,9),
                                    (2,3), (2,4), (2,5), (2,8), (2,10),
                                    (3,1), (3,3), (3,4), (3,5), (3,8), (3,10),
                                    (4,3), (4,4), (4,5), (4,8), (4,10);
/*!40000 ALTER TABLE `subscriptions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tricounts`
--

DROP TABLE IF EXISTS `tricounts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tricounts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(256) COLLATE utf8_unicode_ci NOT NULL,
  `description` varchar(1024) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `creator` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `Title` (`title`,`creator`),
  KEY `Creator` (`creator`),
  CONSTRAINT `tricounts_ibfk_1` FOREIGN KEY (`creator`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tricounts`
--

LOCK TABLES `tricounts` WRITE;
/*!40000 ALTER TABLE `tricounts` DISABLE KEYS */;
INSERT INTO `tricounts` VALUES  (1, 'Voyage Mordor 2023', 'Détruire anneau unique', '2023-02-02 16:15:34', 5),
                                (2, 'Anniversaire 100 ans Bilbo', NULL, '2023-02-02 17:11:00 ', 5),
                                (3, 'Mariage Elessar', NULL, '2023-02-03 14:12:00', 3),
                                (4, 'Naissance Eldarion', NULL, '2023-02-03 14:15:00', 3 );
/*!40000 ALTER TABLE `tricounts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mail` varchar(256) COLLATE utf8_unicode_ci NOT NULL,
  `hashed_password` varchar(512) COLLATE utf8_unicode_ci NOT NULL,
  `full_name` varchar(256) COLLATE utf8_unicode_ci NOT NULL,
  `role` enum('user','admin') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'user',
  `iban` varchar(256) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `Mail` (`mail`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES  (1, 'manwe@test.com', '56ce92d1de4f05017cf03d6cd514d6d1', 'Manwe', 'user', 'BE78 8956 4512 4578'),
                            (2, 'morgoth@test.com', '56ce92d1de4f05017cf03d6cd514d6d1', 'Morgoth', 'user', 'BE89 5623 4512 7845'),
                            (3, 'elrond@test.com', '56ce92d1de4f05017cf03d6cd514d6d1', 'Elrond', 'user', 'BE23 8945 7856 1245'),
                            (4, 'aragorn@test.com', '56ce92d1de4f05017cf03d6cd514d6d1', 'Elessar', 'user', 'BE78 2356 4578 1245'),
                            (5, 'gandalf@test.com', '56ce92d1de4f05017cf03d6cd514d6d1', 'Olorin', 'user', 'BE23 5645 7889 5645'),
                            (6, 'sauron@test.com', '56ce92d1de4f05017cf03d6cd514d6d1', 'Mairon', 'user', 'BE56 4512 4578 4512'),
                            (7, 'smeagol@test.com', '56ce92d1de4f05017cf03d6cd514d6d1', 'Gollum', 'user', 'BE56 2356 4578 4512'),
                            (8, 'galadriel@test.com', '56ce92d1de4f05017cf03d6cd514d6d1', 'Galadriel', 'user', 'BE89 7856 4868 2645'),
                            (9, 'frodon@test.com', '56ce92d1de4f05017cf03d6cd514d6d1', 'Frodon', 'user', 'BE98 9515 7565 3595'), 
                            (10, 'durin@test.com', '56ce92d1de4f05017cf03d6cd514d6d1', 'Durin', 'user', 'BE56 9562 8491 6275');
                            
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2022-12-01 18:43:19
