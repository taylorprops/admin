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
-- Table structure for table `config`
--

DROP TABLE IF EXISTS `config`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `config` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `config_key` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `config_value` text COLLATE utf8mb4_unicode_ci,
  `config_role` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `config_type` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'emails, text',
  `category` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `order` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `config`
--

LOCK TABLES `config` WRITE;
/*!40000 ALTER TABLE `config` DISABLE KEYS */;
INSERT INTO `config` VALUES (2,'in_house_notification_emails_using_heritage_title_contract','info@taylorprops.com','notification_documents','emails','In House Notifications','Agent Using Heritage Title - Rep Buyer','<span data-mce-style=\"font-size: 14.4px;\" style=\"font-size: 14.4px;\">Sent when an agent creates a new Contract</span><span style=\"font-size: 14.4px;\"> and they select that they will be using Heritage Title</span><br style=\"font-size: 14.4px;\"><span data-mce-style=\"color: #e03e2d;\" style=\"font-size: 14.4px; color: rgb(224, 62, 45);\">* No contract has beeen submitted yet</span>',3,NULL,NULL,'2021-03-11 18:55:07'),(3,'in_house_notification_emails_holding_earnest','info@taylorprops.com','notification_documents','emails','In House Notifications','We Are Holding The Earnest Deposit','<span style=\"font-size: 14.4px;\" data-mce-style=\"font-size: 14.4px;\">Sent when an agent creates a new Contract</span>&nbsp;and they select that we will be holding the earnest deposit<br><span style=\"color: rgb(224, 62, 45);\" data-mce-style=\"color: #e03e2d;\">* No contract has beeen submitted yet</span><br>',1,NULL,NULL,'2021-03-11 18:34:44'),(4,'in_house_notification_emails_new_contract','info@taylorprops.com','notification_documents','emails','In House Notifications','New Contract Created - Rep Buyer','<span data-mce-style=\"font-size: 14.4px;\" style=\"font-size: 14.4px;\">Sent when an agent creates a new Contract</span><br style=\"font-size: 14.4px;\"><span data-mce-style=\"color: #e03e2d;\" style=\"font-size: 14.4px; color: rgb(224, 62, 45);\">* No contract has beeen submitted yet</span>',0,NULL,NULL,'2021-03-11 18:53:28'),(5,'agent_notification_emails_listing_expired','on','notification_documents','on_off','Agent Notifications','Expired Listing','Sent to agents when their listing has expired',0,NULL,NULL,'2021-03-11 18:11:52'),(7,'agent_notification_emails_missing_documents_frequencey','3','notification_documents','number','Agent Notifications','Missing Documents Frequency','Number of days between notifications to agents who are missing documents',1,NULL,NULL,'2021-03-11 18:11:52'),(8,'in_house_notification_emails_using_heritage_title_listing','info@taylorprops.com','notification_documents','emails','In House Notifications','Agent Using Heritage Title - Rep Seller','<span data-mce-style=\"font-size: 14.4px;\" style=\"font-size: 14.4px;\">Sent when an agent accepts a Contract for their listing</span><span style=\"font-size: 14.4px;\"> and the Buyers will be using Heritage Title</span><br style=\"font-size: 14.4px;\"><span data-mce-style=\"color: #e03e2d;\" style=\"font-size: 14.4px; color: rgb(224, 62, 45);\">* No contract has beeen submitted yet</span>',2,NULL,NULL,'2021-03-11 18:55:07'),(9,'in_house_notification_emails_release_submitted','info@taylorprops.com','notification_documents','emails','In House Notifications','Release Submitted for Review','An agent has submitted a release to the checklist and is ready for Delia\'s review',3,NULL,NULL,'2021-03-13 18:54:20'),(10,'permission_edit_employees','info@taylorprops.com','permissions','','Permissions','View/Edit Employees','Access to view and edit all employee details',0,NULL,NULL,'2021-03-16 14:59:56'),(11,'permission_edit_permissions','info@taylorprops.com','permissions','','Permissions','View/Edit Permissions','Access to view and edit site permissions',0,NULL,NULL,NULL),(12,'permission_edit_notifications','info@taylorprops.com','permissions','','Permissions','View/Edit Notifications','Access to view and edit site notifications',0,NULL,NULL,NULL);
/*!40000 ALTER TABLE `config` ENABLE KEYS */;
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
