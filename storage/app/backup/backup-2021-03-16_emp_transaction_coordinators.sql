-- MySQL dump 10.13  Distrib 5.7.33, for Linux (x86_64)
--
-- Host: 127.0.0.1    Database: admin
-- ------------------------------------------------------
-- Server version	5.7.33-0ubuntu0.18.04.1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `emp_transaction_coordinators`
--

DROP TABLE IF EXISTS `emp_transaction_coordinators`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `emp_transaction_coordinators` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `active` varchar(5) COLLATE utf8mb4_unicode_ci DEFAULT 'yes',
  `first_name` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '	',
  `last_name` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `photo_location` text COLLATE utf8mb4_unicode_ci,
  `email` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cell_phone` varchar(15) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address_street` varchar(65) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address_unit` varchar(15) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address_city` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address_state` varchar(5) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address_zip` varchar(15) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `signature` text COLLATE utf8mb4_unicode_ci,
  `emp_type` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `emp_position` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `emp_transaction_coordinators`
--

LOCK TABLES `emp_transaction_coordinators` WRITE;
/*!40000 ALTER TABLE `emp_transaction_coordinators` DISABLE KEYS */;
INSERT INTO `emp_transaction_coordinators` VALUES (1,'yes','Amanda','Watkins',NULL,'amanda@taylorprops.com','555-555-5555','123 West St','','Millersville','MD','21108',NULL,NULL,NULL,'2020-09-02 12:30:30','2020-09-02 12:30:30'),(2,'yes','Bob','Dole',NULL,'bob@yahoo.com','654-987-9685','986 Some St','','Annapolis','MD','21401',NULL,NULL,NULL,'2020-09-02 12:30:30','2020-09-02 12:30:30');
/*!40000 ALTER TABLE `emp_transaction_coordinators` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2021-03-16 11:28:55
