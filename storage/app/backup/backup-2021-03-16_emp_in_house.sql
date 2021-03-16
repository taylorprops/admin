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
-- Table structure for table `emp_in_house`
--

DROP TABLE IF EXISTS `emp_in_house`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `emp_in_house` (
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
  `emp_type` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `emp_position` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `emp_in_house`
--

LOCK TABLES `emp_in_house` WRITE;
/*!40000 ALTER TABLE `emp_in_house` DISABLE KEYS */;
INSERT INTO `emp_in_house` VALUES (1,'yes','Mike','Taylor','','info@taylorprops.com','234-345-0987','123 Some St',NULL,'Annapolis','MD','21401','Thank you,<br>Mike Taylor<br>Taylor Properties<br>800-590-0925','admin','',NULL,NULL),(2,'yes','Amanda','Watkins','','amanda@taylorprops.com','234-345-0987','854 Some St',NULL,'Annapolis','MD','21401',NULL,'admin','',NULL,NULL),(3,'yes','Tanya','Jones',NULL,'tanya@hf.com','234-345-0987','258 Some St',NULL,'Annapolis','MD','21401',NULL,'mortgage','processor',NULL,NULL),(4,'yes','Melissa','Claure',NULL,'melissa@hf.com','234-345-0987','849 Some St',NULL,'Annapolis','MD','21401',NULL,'mortgage','manager',NULL,NULL),(5,'yes','Bob','Jones',NULL,'bob@tp.com','234-345-0987','123 Some St',NULL,'Annapolis','MD','21401',NULL,'title','processor',NULL,NULL),(6,'yes','Tina','Taylor',NULL,'tina@tp.com','234-345-0987','854 Some St',NULL,'Annapolis','MD','21401',NULL,'title','processor',NULL,NULL),(7,'yes','Tanya','Jones',NULL,'tanya@hf.com','234-345-0987','258 Some St',NULL,'Annapolis','MD','21401',NULL,'mortgage','processor',NULL,NULL),(8,'yes','Melissa','Claure',NULL,'melissa@hf.com','234-345-0987','849 Some St',NULL,'Annapolis','MD','21401',NULL,'mortgage','manager',NULL,NULL);
/*!40000 ALTER TABLE `emp_in_house` ENABLE KEYS */;
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
