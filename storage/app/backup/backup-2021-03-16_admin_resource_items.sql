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
-- Table structure for table `admin_resource_items`
--

DROP TABLE IF EXISTS `admin_resource_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `admin_resource_items` (
  `resource_id` int(11) NOT NULL AUTO_INCREMENT,
  `resource_type` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `resource_type_title` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `resource_name` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `resource_order` int(11) NOT NULL,
  `resource_state` varchar(5) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `resource_color` varchar(15) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `resource_active` varchar(5) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'yes',
  PRIMARY KEY (`resource_id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `admin_resource_items`
--

LOCK TABLES `admin_resource_items` WRITE;
/*!40000 ALTER TABLE `admin_resource_items` DISABLE KEYS */;
INSERT INTO `admin_resource_items` VALUES (2,'rejected_reason','Rejected Reason','Missing Agent Signatures or Initials',0,NULL,NULL,'yes'),(4,'rejected_reason','Rejected Reason','Missing Buyer Signatures or Initials',1,NULL,NULL,'yes'),(5,'rejected_reason','Rejected Reason','Missing Seller Signatures or Initials',2,NULL,NULL,'yes'),(7,'rejected_reason','Rejected Reason','Not Legible',4,NULL,NULL,'yes'),(8,'rejected_reason','Rejected Reason','Missing Pages',6,NULL,NULL,'yes'),(9,'rejected_reason','Rejected Reason','Form is Blank',0,NULL,NULL,'yes'),(10,'rejected_reason','Rejected Reason','Wrong Form Added',0,NULL,NULL,'yes');
/*!40000 ALTER TABLE `admin_resource_items` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2021-03-16 11:28:54
