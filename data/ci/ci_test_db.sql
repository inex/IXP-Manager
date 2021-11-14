-- MySQL dump 10.13  Distrib 8.0.27, for macos11.6 (x86_64)
--
-- Host: localhost    Database: myapp_test
-- ------------------------------------------------------
-- Server version	8.0.27

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `api_keys`
--

DROP TABLE IF EXISTS `api_keys`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `api_keys` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `apiKey` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `expires` datetime DEFAULT NULL,
  `allowedIPs` mediumtext CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `lastseenAt` datetime DEFAULT NULL,
  `lastseenFrom` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `description` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_9579321F800A1141` (`apiKey`),
  KEY `IDX_9579321FA76ED395` (`user_id`),
  CONSTRAINT `FK_9579321FA76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb3 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `api_keys`
--

LOCK TABLES `api_keys` WRITE;
/*!40000 ALTER TABLE `api_keys` DISABLE KEYS */;
INSERT INTO `api_keys` VALUES (1,1,'Syy4R8uXTquJNkSav4mmbk5eZWOgoc6FKUJPqOoGHhBjhsC9',NULL,'','2017-05-19 09:48:49','127.0.0.1',NULL,'2014-01-06 13:43:19',NULL),(3,2,'Syy4R8uXTquJNkSav4mmbk5eZWOgoc6FKUJPqOoGHhBjhsC8',NULL,'','2017-05-19 09:48:49','127.0.0.1',NULL,'2014-01-06 13:43:19',NULL),(4,3,'Syy4R8uXTquJNkSav4mmbk5eZWOgoc6FKUJPqOoGHhBjhsC7',NULL,'','2017-05-19 09:48:49','127.0.0.1',NULL,'2014-01-06 13:43:19',NULL);
/*!40000 ALTER TABLE `api_keys` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `atlas_measurements`
--

DROP TABLE IF EXISTS `atlas_measurements`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `atlas_measurements` (
  `id` int NOT NULL AUTO_INCREMENT,
  `run_id` int NOT NULL,
  `cust_source` int DEFAULT NULL,
  `cust_dest` int DEFAULT NULL,
  `atlas_id` int DEFAULT NULL,
  `atlas_create` datetime DEFAULT NULL,
  `atlas_start` datetime DEFAULT NULL,
  `atlas_stop` datetime DEFAULT NULL,
  `atlas_data` json DEFAULT NULL,
  `atlas_request` json DEFAULT NULL,
  `atlas_state` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `atlas_measurements_cust_source_foreign` (`cust_source`),
  KEY `atlas_measurements_cust_dest_foreign` (`cust_dest`),
  KEY `atlas_measurements_run_id_foreign` (`run_id`),
  CONSTRAINT `atlas_measurements_cust_dest_foreign` FOREIGN KEY (`cust_dest`) REFERENCES `cust` (`id`),
  CONSTRAINT `atlas_measurements_cust_source_foreign` FOREIGN KEY (`cust_source`) REFERENCES `cust` (`id`),
  CONSTRAINT `atlas_measurements_run_id_foreign` FOREIGN KEY (`run_id`) REFERENCES `atlas_runs` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `atlas_measurements`
--

LOCK TABLES `atlas_measurements` WRITE;
/*!40000 ALTER TABLE `atlas_measurements` DISABLE KEYS */;
/*!40000 ALTER TABLE `atlas_measurements` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `atlas_probes`
--

DROP TABLE IF EXISTS `atlas_probes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `atlas_probes` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `cust_id` int NOT NULL,
  `address_v4` varchar(15) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address_v6` varchar(39) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `v4_enabled` tinyint DEFAULT NULL,
  `v6_enabled` tinyint DEFAULT NULL,
  `asn` int DEFAULT NULL,
  `atlas_id` int NOT NULL,
  `is_anchor` tinyint NOT NULL,
  `is_public` tinyint NOT NULL,
  `last_connected` datetime DEFAULT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `api_data` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `atlas_probes_cust_id_foreign` (`cust_id`),
  CONSTRAINT `atlas_probes_cust_id_foreign` FOREIGN KEY (`cust_id`) REFERENCES `cust` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `atlas_probes`
--

LOCK TABLES `atlas_probes` WRITE;
/*!40000 ALTER TABLE `atlas_probes` DISABLE KEYS */;
/*!40000 ALTER TABLE `atlas_probes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `atlas_results`
--

DROP TABLE IF EXISTS `atlas_results`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `atlas_results` (
  `id` int NOT NULL AUTO_INCREMENT,
  `measurement_id` int DEFAULT NULL,
  `routing` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `path` longtext COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `atlas_results_measurement_id_unique` (`measurement_id`),
  CONSTRAINT `atlas_results_measurement_id_foreign` FOREIGN KEY (`measurement_id`) REFERENCES `atlas_measurements` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `atlas_results`
--

LOCK TABLES `atlas_results` WRITE;
/*!40000 ALTER TABLE `atlas_results` DISABLE KEYS */;
/*!40000 ALTER TABLE `atlas_results` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `atlas_runs`
--

DROP TABLE IF EXISTS `atlas_runs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `atlas_runs` (
  `id` int NOT NULL AUTO_INCREMENT,
  `vlan_id` int DEFAULT NULL,
  `protocol` int DEFAULT NULL,
  `scheduled_at` datetime DEFAULT NULL,
  `started_at` datetime DEFAULT NULL,
  `completed_at` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `atlas_runs_vlan_id_foreign` (`vlan_id`),
  CONSTRAINT `atlas_runs_vlan_id_foreign` FOREIGN KEY (`vlan_id`) REFERENCES `vlan` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `atlas_runs`
--

LOCK TABLES `atlas_runs` WRITE;
/*!40000 ALTER TABLE `atlas_runs` DISABLE KEYS */;
/*!40000 ALTER TABLE `atlas_runs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bgp_sessions`
--

DROP TABLE IF EXISTS `bgp_sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `bgp_sessions` (
  `id` int NOT NULL AUTO_INCREMENT,
  `srcipaddressid` int NOT NULL,
  `protocol` int NOT NULL,
  `dstipaddressid` int NOT NULL,
  `packetcount` int NOT NULL DEFAULT '0',
  `last_seen` datetime NOT NULL,
  `source` varchar(40) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `src_protocol_dst` (`srcipaddressid`,`protocol`,`dstipaddressid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bgp_sessions`
--

LOCK TABLES `bgp_sessions` WRITE;
/*!40000 ALTER TABLE `bgp_sessions` DISABLE KEYS */;
/*!40000 ALTER TABLE `bgp_sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bgpsessiondata`
--

DROP TABLE IF EXISTS `bgpsessiondata`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `bgpsessiondata` (
  `id` int NOT NULL AUTO_INCREMENT,
  `srcipaddressid` int DEFAULT NULL,
  `dstipaddressid` int DEFAULT NULL,
  `protocol` int DEFAULT NULL,
  `vlan` int DEFAULT NULL,
  `packetcount` int DEFAULT '0',
  `timestamp` datetime DEFAULT NULL,
  `source` varchar(40) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_timestamp` (`timestamp`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bgpsessiondata`
--

LOCK TABLES `bgpsessiondata` WRITE;
/*!40000 ALTER TABLE `bgpsessiondata` DISABLE KEYS */;
/*!40000 ALTER TABLE `bgpsessiondata` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cabinet`
--

DROP TABLE IF EXISTS `cabinet`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cabinet` (
  `id` int NOT NULL AUTO_INCREMENT,
  `locationid` int DEFAULT NULL,
  `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `colocation` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `height` int DEFAULT NULL,
  `type` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `notes` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `u_counts_from` smallint DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_4CED05B05E237E06` (`name`),
  KEY `IDX_4CED05B03530CCF` (`locationid`),
  CONSTRAINT `FK_4CED05B03530CCF` FOREIGN KEY (`locationid`) REFERENCES `location` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb3 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cabinet`
--

LOCK TABLES `cabinet` WRITE;
/*!40000 ALTER TABLE `cabinet` DISABLE KEYS */;
INSERT INTO `cabinet` VALUES (1,1,'Cabinet 1','c1',0,'','',NULL,NULL,NULL);
/*!40000 ALTER TABLE `cabinet` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `company_billing_detail`
--

DROP TABLE IF EXISTS `company_billing_detail`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `company_billing_detail` (
  `id` int NOT NULL AUTO_INCREMENT,
  `billingContactName` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `billingAddress1` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `billingAddress2` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `billingAddress3` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `billingTownCity` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `billingPostcode` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `billingCountry` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `billingEmail` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `billingTelephone` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `vatNumber` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `vatRate` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `purchaseOrderRequired` tinyint(1) NOT NULL DEFAULT '0',
  `invoiceMethod` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `invoiceEmail` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `billingFrequency` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb3 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `company_billing_detail`
--

LOCK TABLES `company_billing_detail` WRITE;
/*!40000 ALTER TABLE `company_billing_detail` DISABLE KEYS */;
INSERT INTO `company_billing_detail` VALUES (1,NULL,'c/o The Bill Payers','Money House, Moneybags Street',NULL,'Dublin','D4','IE',NULL,NULL,NULL,NULL,0,'EMAIL',NULL,'NOBILLING',NULL,NULL),(2,'','','','','','','','','','','',0,'EMAIL','','NOBILLING',NULL,NULL),(3,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,'EMAIL',NULL,'NOBILLING',NULL,NULL),(4,'','','','','','','','','','','',0,'EMAIL','','NOBILLING',NULL,NULL),(5,'','','','','','','','','','','',0,'EMAIL','','NOBILLING',NULL,NULL);
/*!40000 ALTER TABLE `company_billing_detail` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `company_registration_detail`
--

DROP TABLE IF EXISTS `company_registration_detail`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `company_registration_detail` (
  `id` int NOT NULL AUTO_INCREMENT,
  `registeredName` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `companyNumber` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `jurisdiction` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `address1` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `address2` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `address3` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `townCity` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `postcode` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `country` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb3 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `company_registration_detail`
--

LOCK TABLES `company_registration_detail` WRITE;
/*!40000 ALTER TABLE `company_registration_detail` DISABLE KEYS */;
INSERT INTO `company_registration_detail` VALUES (1,'INEX','123456','Ireland','5 Somewhere',NULL,NULL,'Dublin','D4','IE',NULL,NULL),(2,'','','','','','','','','',NULL,NULL),(3,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(4,'','','','','','','','','',NULL,NULL),(5,'','','','','','','','','',NULL,NULL);
/*!40000 ALTER TABLE `company_registration_detail` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `console_server`
--

DROP TABLE IF EXISTS `console_server`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `console_server` (
  `id` int NOT NULL AUTO_INCREMENT,
  `vendor_id` int DEFAULT NULL,
  `cabinet_id` int DEFAULT NULL,
  `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `hostname` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `model` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `serialNumber` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `active` tinyint(1) DEFAULT '1',
  `notes` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_92A539235E237E06` (`name`),
  KEY `IDX_92A53923F603EE73` (`vendor_id`),
  KEY `IDX_92A53923D351EC` (`cabinet_id`),
  CONSTRAINT `FK_92A53923D351EC` FOREIGN KEY (`cabinet_id`) REFERENCES `cabinet` (`id`),
  CONSTRAINT `FK_92A53923F603EE73` FOREIGN KEY (`vendor_id`) REFERENCES `vendor` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `console_server`
--

LOCK TABLES `console_server` WRITE;
/*!40000 ALTER TABLE `console_server` DISABLE KEYS */;
/*!40000 ALTER TABLE `console_server` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `consoleserverconnection`
--

DROP TABLE IF EXISTS `consoleserverconnection`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `consoleserverconnection` (
  `id` int NOT NULL AUTO_INCREMENT,
  `custid` int DEFAULT NULL,
  `description` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `port` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `speed` int DEFAULT NULL,
  `parity` int DEFAULT NULL,
  `stopbits` int DEFAULT NULL,
  `flowcontrol` int DEFAULT NULL,
  `autobaud` tinyint(1) DEFAULT NULL,
  `notes` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `console_server_id` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `console_server_port_uniq` (`console_server_id`,`port`),
  KEY `IDX_530316DCDA0209B9` (`custid`),
  KEY `IDX_530316DCF472E7C6` (`console_server_id`),
  CONSTRAINT `FK_530316DCDA0209B9` FOREIGN KEY (`custid`) REFERENCES `cust` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_530316DCF472E7C6` FOREIGN KEY (`console_server_id`) REFERENCES `console_server` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `consoleserverconnection`
--

LOCK TABLES `consoleserverconnection` WRITE;
/*!40000 ALTER TABLE `consoleserverconnection` DISABLE KEYS */;
/*!40000 ALTER TABLE `consoleserverconnection` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `contact`
--

DROP TABLE IF EXISTS `contact`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `contact` (
  `id` int NOT NULL AUTO_INCREMENT,
  `custid` int DEFAULT NULL,
  `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `position` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `email` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `phone` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `mobile` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `facilityaccess` tinyint(1) NOT NULL DEFAULT '0',
  `mayauthorize` tinyint(1) NOT NULL DEFAULT '0',
  `notes` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `updated_at` datetime DEFAULT NULL,
  `lastupdatedby` int DEFAULT NULL,
  `creator` varchar(32) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_4C62E638DA0209B9` (`custid`),
  CONSTRAINT `FK_4C62E638DA0209B9` FOREIGN KEY (`custid`) REFERENCES `cust` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb3 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `contact`
--

LOCK TABLES `contact` WRITE;
/*!40000 ALTER TABLE `contact` DISABLE KEYS */;
INSERT INTO `contact` VALUES (1,1,'Travis CI','Master of the Universe','joe@siep.com','+353 86 123 4567','+353 1 123 4567',1,1,'','2014-01-06 13:54:22',1,'1','2014-01-06 13:54:22'),(2,5,'Imagine CustAdmin','Imagine CustAdmin','imagine-custadmin@example.com','','',1,1,'','2018-05-15 15:36:12',1,'travis','2018-05-15 15:36:12'),(3,5,'Imagine CustUser','Imagine CustUser','imagine-custuser@example.com','','',1,1,'','2018-05-15 15:36:54',1,'travis','2018-05-15 15:36:54'),(4,2,'HEAnet CustUser','HEANet CustUser','heanet-custuser@example.com','','',1,1,'','2018-05-22 12:00:00',1,'travis',NULL),(5,2,'HEAnet CustAdmin','HEANet CustAdmin','heanet-custadmin@example.com','','',1,1,'','2018-05-22 12:00:00',1,'travis',NULL);
/*!40000 ALTER TABLE `contact` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `contact_group`
--

DROP TABLE IF EXISTS `contact_group`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `contact_group` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `name` varchar(20) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `description` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `type` varchar(20) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `limited_to` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_40EA54CA5E237E06` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb3 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `contact_group`
--

LOCK TABLES `contact_group` WRITE;
/*!40000 ALTER TABLE `contact_group` DISABLE KEYS */;
INSERT INTO `contact_group` VALUES (1,'Billing','Contact role for billing matters','ROLE',1,0,'2014-01-06 12:54:22',NULL),(2,'Technical','Contact role for technical matters','ROLE',1,0,'2014-01-06 12:54:22',NULL),(3,'Admin','Contact role for admin matters','ROLE',1,0,'2014-01-06 12:54:22',NULL),(4,'Marketing','Contact role for marketing matters','ROLE',1,0,'2014-01-06 12:54:22',NULL);
/*!40000 ALTER TABLE `contact_group` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `contact_to_group`
--

DROP TABLE IF EXISTS `contact_to_group`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `contact_to_group` (
  `contact_id` int NOT NULL,
  `contact_group_id` bigint NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`contact_id`,`contact_group_id`),
  KEY `IDX_FCD9E962E7A1254A` (`contact_id`),
  KEY `IDX_FCD9E962647145D0` (`contact_group_id`),
  CONSTRAINT `FK_FCD9E962647145D0` FOREIGN KEY (`contact_group_id`) REFERENCES `contact_group` (`id`),
  CONSTRAINT `FK_FCD9E962E7A1254A` FOREIGN KEY (`contact_id`) REFERENCES `contact` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `contact_to_group`
--

LOCK TABLES `contact_to_group` WRITE;
/*!40000 ALTER TABLE `contact_to_group` DISABLE KEYS */;
/*!40000 ALTER TABLE `contact_to_group` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `corebundles`
--

DROP TABLE IF EXISTS `corebundles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `corebundles` (
  `id` int NOT NULL AUTO_INCREMENT,
  `description` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `type` int NOT NULL,
  `graph_title` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `bfd` tinyint(1) NOT NULL DEFAULT '0',
  `ipv4_subnet` varchar(18) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `ipv6_subnet` varchar(43) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `stp` tinyint(1) NOT NULL DEFAULT '0',
  `cost` int unsigned DEFAULT NULL,
  `preference` int unsigned DEFAULT NULL,
  `enabled` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb3 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `corebundles`
--

LOCK TABLES `corebundles` WRITE;
/*!40000 ALTER TABLE `corebundles` DISABLE KEYS */;
INSERT INTO `corebundles` VALUES (1,'Test Core Bundle',1,'Test Core Bundle',0,NULL,NULL,0,10,10,1,NULL,NULL);
/*!40000 ALTER TABLE `corebundles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `coreinterfaces`
--

DROP TABLE IF EXISTS `coreinterfaces`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `coreinterfaces` (
  `id` int NOT NULL AUTO_INCREMENT,
  `physical_interface_id` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_E1A404B7FF664B20` (`physical_interface_id`),
  CONSTRAINT `FK_E1A404B7FF664B20` FOREIGN KEY (`physical_interface_id`) REFERENCES `physicalinterface` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb3 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `coreinterfaces`
--

LOCK TABLES `coreinterfaces` WRITE;
/*!40000 ALTER TABLE `coreinterfaces` DISABLE KEYS */;
INSERT INTO `coreinterfaces` VALUES (1,9,NULL,NULL),(2,10,NULL,NULL),(3,11,NULL,NULL),(4,12,NULL,NULL);
/*!40000 ALTER TABLE `coreinterfaces` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `corelinks`
--

DROP TABLE IF EXISTS `corelinks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `corelinks` (
  `id` int NOT NULL AUTO_INCREMENT,
  `core_interface_sidea_id` int NOT NULL,
  `core_interface_sideb_id` int NOT NULL,
  `core_bundle_id` int NOT NULL,
  `bfd` tinyint(1) NOT NULL DEFAULT '0',
  `ipv4_subnet` varchar(18) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `ipv6_subnet` varchar(43) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `enabled` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_BE421236BEBB85C6` (`core_interface_sidea_id`),
  UNIQUE KEY `UNIQ_BE421236AC0E2A28` (`core_interface_sideb_id`),
  KEY `IDX_BE421236BE9AE9F7` (`core_bundle_id`),
  CONSTRAINT `FK_BE421236AC0E2A28` FOREIGN KEY (`core_interface_sideb_id`) REFERENCES `coreinterfaces` (`id`),
  CONSTRAINT `FK_BE421236BE9AE9F7` FOREIGN KEY (`core_bundle_id`) REFERENCES `corebundles` (`id`),
  CONSTRAINT `FK_BE421236BEBB85C6` FOREIGN KEY (`core_interface_sidea_id`) REFERENCES `coreinterfaces` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb3 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `corelinks`
--

LOCK TABLES `corelinks` WRITE;
/*!40000 ALTER TABLE `corelinks` DISABLE KEYS */;
INSERT INTO `corelinks` VALUES (1,1,2,1,1,'10.0.0.0/31',NULL,1,NULL,NULL),(2,3,4,1,1,'10.0.0.2/31',NULL,1,NULL,NULL);
/*!40000 ALTER TABLE `corelinks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cust`
--

DROP TABLE IF EXISTS `cust`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cust` (
  `id` int NOT NULL AUTO_INCREMENT,
  `irrdb` int DEFAULT NULL,
  `company_registered_detail_id` int DEFAULT NULL,
  `company_billing_details_id` int DEFAULT NULL,
  `reseller` int DEFAULT NULL,
  `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `type` int DEFAULT NULL,
  `shortname` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `abbreviatedName` varchar(30) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `autsys` int DEFAULT NULL,
  `maxprefixes` int DEFAULT NULL,
  `peeringemail` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `nocphone` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `noc24hphone` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `nocfax` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `nocemail` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `nochours` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `nocwww` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `peeringmacro` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `peeringmacrov6` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `peeringpolicy` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `corpwww` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `datejoin` date DEFAULT NULL,
  `dateleave` date DEFAULT NULL,
  `status` smallint DEFAULT NULL,
  `activepeeringmatrix` tinyint(1) DEFAULT NULL,
  `lastupdatedby` int DEFAULT NULL,
  `creator` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `MD5Support` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT 'UNKNOWN',
  `isReseller` tinyint(1) NOT NULL DEFAULT '0',
  `in_manrs` tinyint(1) NOT NULL DEFAULT '0',
  `in_peeringdb` tinyint(1) NOT NULL DEFAULT '0',
  `peeringdb_oauth` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_997B25A64082763` (`shortname`),
  UNIQUE KEY `UNIQ_997B25A98386213` (`company_registered_detail_id`),
  UNIQUE KEY `UNIQ_997B25A84478F0C` (`company_billing_details_id`),
  KEY `IDX_997B25A666E98DF` (`irrdb`),
  KEY `IDX_997B25A18015899` (`reseller`),
  CONSTRAINT `FK_997B25A18015899` FOREIGN KEY (`reseller`) REFERENCES `cust` (`id`),
  CONSTRAINT `FK_997B25A666E98DF` FOREIGN KEY (`irrdb`) REFERENCES `irrdbconfig` (`id`),
  CONSTRAINT `FK_997B25A84478F0C` FOREIGN KEY (`company_billing_details_id`) REFERENCES `company_billing_detail` (`id`),
  CONSTRAINT `FK_997B25A98386213` FOREIGN KEY (`company_registered_detail_id`) REFERENCES `company_registration_detail` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb3 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cust`
--

LOCK TABLES `cust` WRITE;
/*!40000 ALTER TABLE `cust` DISABLE KEYS */;
INSERT INTO `cust` VALUES (1,NULL,1,1,NULL,'INEX',3,'inex','INEX',2128,1000,'peering@siep.net','+353 1 123 4567','+353 1 123 4567','+353 1 123 4568','noc@siep.com','24x7','http://www.siep.com/noc/','AS-INEX','AS-INEX','mandatory','http://www.siep.com/','2014-01-06',NULL,1,1,NULL,'travis','YES',0,0,0,1,'2014-01-05 23:00:00',NULL),(2,1,2,2,NULL,'HEAnet',1,'heanet','HEAnet',1213,1000,'peering@example.com','','','','','0','','AS-HEANET',NULL,'open','http://www.example.com/','2014-01-06',NULL,1,1,NULL,'travis','UNKNOWN',0,0,0,1,'2014-01-05 23:00:00',NULL),(3,13,3,3,NULL,'PCH DNS',1,'pchdns','PCH DNS',42,2000,'peering@example.com','','','','','0','','AS-PCH',NULL,'open','http://www.example.com/','2014-01-06',NULL,1,1,1,'travis','YES',0,0,0,1,'2014-01-05 23:00:00','2014-01-05 23:00:00'),(4,2,4,4,NULL,'AS112',4,'as112','AS112',112,20,'peering@example.com','','','','','0','','',NULL,'open','http://www.example.com/','2014-01-06',NULL,1,1,NULL,'travis','NO',0,0,0,1,'2014-01-05 23:00:00',NULL),(5,1,5,5,NULL,'Imagine',1,'imagine','Imagine',25441,1000,'peering@example.com','','','','','0','','AS-IBIS',NULL,'open','http://www.example.com/','2014-01-06',NULL,1,1,NULL,'travis','YES',0,0,0,1,'2014-01-05 23:00:00',NULL);
/*!40000 ALTER TABLE `cust` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cust_notes`
--

DROP TABLE IF EXISTS `cust_notes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cust_notes` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `customer_id` int NOT NULL,
  `private` tinyint(1) NOT NULL DEFAULT '1',
  `title` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `note` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_6377D8679395C3F3` (`customer_id`),
  CONSTRAINT `FK_6377D8679395C3F3` FOREIGN KEY (`customer_id`) REFERENCES `cust` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cust_notes`
--

LOCK TABLES `cust_notes` WRITE;
/*!40000 ALTER TABLE `cust_notes` DISABLE KEYS */;
/*!40000 ALTER TABLE `cust_notes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cust_tag`
--

DROP TABLE IF EXISTS `cust_tag`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cust_tag` (
  `id` int NOT NULL AUTO_INCREMENT,
  `tag` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `display_as` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `description` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `internal_only` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_6B54CFB8389B783` (`tag`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb3 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cust_tag`
--

LOCK TABLES `cust_tag` WRITE;
/*!40000 ALTER TABLE `cust_tag` DISABLE KEYS */;
INSERT INTO `cust_tag` VALUES (1,'test-tag1','Test Tag1','Yeah!',0,'2018-06-19 11:38:28','2018-06-19 11:38:28'),(2,'test-tag2','Test Tag2','Yeah!',1,'2018-06-19 11:38:44','2018-06-19 11:38:44');
/*!40000 ALTER TABLE `cust_tag` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cust_to_cust_tag`
--

DROP TABLE IF EXISTS `cust_to_cust_tag`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cust_to_cust_tag` (
  `customer_tag_id` int NOT NULL,
  `customer_id` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`customer_tag_id`,`customer_id`),
  KEY `IDX_A6CFB30CB17BF40` (`customer_tag_id`),
  KEY `IDX_A6CFB30C9395C3F3` (`customer_id`),
  CONSTRAINT `FK_A6CFB30C9395C3F3` FOREIGN KEY (`customer_id`) REFERENCES `cust` (`id`),
  CONSTRAINT `FK_A6CFB30CB17BF40` FOREIGN KEY (`customer_tag_id`) REFERENCES `cust_tag` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cust_to_cust_tag`
--

LOCK TABLES `cust_to_cust_tag` WRITE;
/*!40000 ALTER TABLE `cust_to_cust_tag` DISABLE KEYS */;
INSERT INTO `cust_to_cust_tag` VALUES (1,4,NULL,NULL),(1,5,NULL,NULL),(2,1,NULL,NULL),(2,4,NULL,NULL);
/*!40000 ALTER TABLE `cust_to_cust_tag` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `custkit`
--

DROP TABLE IF EXISTS `custkit`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `custkit` (
  `id` int NOT NULL AUTO_INCREMENT,
  `custid` int DEFAULT NULL,
  `cabinetid` int DEFAULT NULL,
  `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `descr` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_8127F9AADA0209B9` (`custid`),
  KEY `IDX_8127F9AA2B96718A` (`cabinetid`),
  CONSTRAINT `FK_8127F9AA2B96718A` FOREIGN KEY (`cabinetid`) REFERENCES `cabinet` (`id`),
  CONSTRAINT `FK_8127F9AADA0209B9` FOREIGN KEY (`custid`) REFERENCES `cust` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `custkit`
--

LOCK TABLES `custkit` WRITE;
/*!40000 ALTER TABLE `custkit` DISABLE KEYS */;
/*!40000 ALTER TABLE `custkit` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `customer_to_users`
--

DROP TABLE IF EXISTS `customer_to_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `customer_to_users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `customer_id` int NOT NULL,
  `user_id` int NOT NULL,
  `privs` int NOT NULL,
  `last_login_date` datetime DEFAULT NULL,
  `last_login_from` tinytext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `extra_attributes` json DEFAULT NULL COMMENT '(DC2Type:json)',
  `last_login_via` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `customer_user` (`customer_id`,`user_id`),
  KEY `IDX_337AD7F69395C3F3` (`customer_id`),
  KEY `IDX_337AD7F6A76ED395` (`user_id`),
  CONSTRAINT `FK_337AD7F69395C3F3` FOREIGN KEY (`customer_id`) REFERENCES `cust` (`id`),
  CONSTRAINT `FK_337AD7F6A76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `customer_to_users`
--

LOCK TABLES `customer_to_users` WRITE;
/*!40000 ALTER TABLE `customer_to_users` DISABLE KEYS */;
INSERT INTO `customer_to_users` VALUES (1,1,1,3,'2020-07-10 07:54:18','127.0.0.1','{\"created_by\": {\"type\": \"migration-script\"}}','Login','2019-05-10 11:40:45',NULL),(2,5,2,2,'2018-06-20 10:23:22','127.0.0.1','{\"created_by\": {\"type\": \"migration-script\"}}',NULL,'2019-05-10 11:40:45',NULL),(3,5,3,1,'2018-06-20 10:23:58','127.0.0.1','{\"created_by\": {\"type\": \"migration-script\"}}',NULL,'2019-05-10 11:40:45',NULL),(4,2,4,1,'1970-01-01 00:00:00','','{\"created_by\": {\"type\": \"migration-script\"}}',NULL,'2019-05-10 11:40:45',NULL),(5,2,5,2,'2018-06-20 10:24:24','127.0.0.1','{\"created_by\": {\"type\": \"migration-script\"}}',NULL,'2019-05-10 11:40:45',NULL);
/*!40000 ALTER TABLE `customer_to_users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `docstore_customer_directories`
--

DROP TABLE IF EXISTS `docstore_customer_directories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `docstore_customer_directories` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `cust_id` int NOT NULL,
  `parent_dir_id` bigint unsigned DEFAULT NULL,
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `docstore_customer_directories_cust_id_foreign` (`cust_id`),
  KEY `docstore_customer_directories_parent_dir_id_index` (`parent_dir_id`),
  CONSTRAINT `docstore_customer_directories_cust_id_foreign` FOREIGN KEY (`cust_id`) REFERENCES `cust` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `docstore_customer_directories`
--

LOCK TABLES `docstore_customer_directories` WRITE;
/*!40000 ALTER TABLE `docstore_customer_directories` DISABLE KEYS */;
INSERT INTO `docstore_customer_directories` VALUES (1,5,NULL,'Folder 1','This is the folder 1','2020-04-28 08:00:00','2020-04-28 08:00:00'),(2,5,1,'Sub Folder 1','This is sub folder 1','2020-04-28 08:00:00','2020-04-28 08:00:00'),(3,5,NULL,'Folder 2','This is folder 2','2020-04-28 08:00:00','2020-04-28 08:00:00');
/*!40000 ALTER TABLE `docstore_customer_directories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `docstore_customer_files`
--

DROP TABLE IF EXISTS `docstore_customer_files`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `docstore_customer_files` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `cust_id` int NOT NULL,
  `docstore_customer_directory_id` bigint unsigned DEFAULT NULL,
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `disk` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'docstore_customers',
  `path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `sha256` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `min_privs` smallint NOT NULL,
  `file_last_updated` datetime NOT NULL,
  `created_by` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `docstore_customer_files_cust_id_foreign` (`cust_id`),
  KEY `docstore_customer_files_docstore_customer_directory_id_foreign` (`docstore_customer_directory_id`),
  CONSTRAINT `docstore_customer_files_cust_id_foreign` FOREIGN KEY (`cust_id`) REFERENCES `cust` (`id`),
  CONSTRAINT `docstore_customer_files_docstore_customer_directory_id_foreign` FOREIGN KEY (`docstore_customer_directory_id`) REFERENCES `docstore_customer_directories` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `docstore_customer_files`
--

LOCK TABLES `docstore_customer_files` WRITE;
/*!40000 ALTER TABLE `docstore_customer_files` DISABLE KEYS */;
INSERT INTO `docstore_customer_files` VALUES (1,5,1,'File.pdf','docstore_customers','5/7s5yYBsebKN64SHtFkM16pY2OBvkdURPXzW7abmb.pdf','76ca2a6f2acda3c8ff39df2695885a2dbf05565dedaed6912a2b4cf439a19228',NULL,3,'2020-04-28 09:04:46',1,'2020-04-28 08:04:46','2020-04-28 08:04:46');
/*!40000 ALTER TABLE `docstore_customer_files` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `docstore_directories`
--

DROP TABLE IF EXISTS `docstore_directories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `docstore_directories` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `parent_dir_id` bigint unsigned DEFAULT NULL,
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `docstore_directories_parent_dir_id_index` (`parent_dir_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `docstore_directories`
--

LOCK TABLES `docstore_directories` WRITE;
/*!40000 ALTER TABLE `docstore_directories` DISABLE KEYS */;
INSERT INTO `docstore_directories` VALUES (1,NULL,'Folder 1','I am the folder 1','2020-02-27 10:35:18','2020-02-27 10:35:18'),(2,1,'Sub Folder 1','I am the sub folder 1','2020-02-27 10:35:48','2020-02-27 10:35:48'),(3,NULL,'Folder 2','I am the folder 2','2020-02-27 10:36:11','2020-02-27 10:36:11');
/*!40000 ALTER TABLE `docstore_directories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `docstore_files`
--

DROP TABLE IF EXISTS `docstore_files`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `docstore_files` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `docstore_directory_id` bigint unsigned DEFAULT NULL,
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `disk` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'docstore',
  `path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `sha256` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `min_privs` smallint NOT NULL,
  `file_last_updated` datetime NOT NULL,
  `created_by` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `docstore_files_docstore_directory_id_foreign` (`docstore_directory_id`),
  CONSTRAINT `docstore_files_docstore_directory_id_foreign` FOREIGN KEY (`docstore_directory_id`) REFERENCES `docstore_directories` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `docstore_files`
--

LOCK TABLES `docstore_files` WRITE;
/*!40000 ALTER TABLE `docstore_files` DISABLE KEYS */;
INSERT INTO `docstore_files` VALUES (1,1,'test.txt','docstore','BaLvISQV7Cn48p8LPaOoPSyJ5hzaKC7rHlqMu5Hd.txt','64cdd02f0ef14bf6b8e0a51915396a002afed410459935b1209ba2d654842f10',NULL,0,'2021-05-28 14:03:38',1,'2021-05-28 12:03:38','2021-05-28 12:03:38');
/*!40000 ALTER TABLE `docstore_files` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `docstore_logs`
--

DROP TABLE IF EXISTS `docstore_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `docstore_logs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `docstore_file_id` bigint unsigned NOT NULL,
  `downloaded_by` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `docstore_logs_docstore_file_id_foreign` (`docstore_file_id`),
  CONSTRAINT `docstore_logs_docstore_file_id_foreign` FOREIGN KEY (`docstore_file_id`) REFERENCES `docstore_files` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `docstore_logs`
--

LOCK TABLES `docstore_logs` WRITE;
/*!40000 ALTER TABLE `docstore_logs` DISABLE KEYS */;
/*!40000 ALTER TABLE `docstore_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `connection` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `failed_jobs`
--

LOCK TABLES `failed_jobs` WRITE;
/*!40000 ALTER TABLE `failed_jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `failed_jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `infrastructure`
--

DROP TABLE IF EXISTS `infrastructure`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `infrastructure` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `shortname` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `isPrimary` tinyint(1) NOT NULL DEFAULT '0',
  `peeringdb_ix_id` bigint DEFAULT NULL,
  `ixf_ix_id` bigint DEFAULT NULL,
  `country` varchar(2) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `notes` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_shortname` (`shortname`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb3 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `infrastructure`
--

LOCK TABLES `infrastructure` WRITE;
/*!40000 ALTER TABLE `infrastructure` DISABLE KEYS */;
INSERT INTO `infrastructure` VALUES (1,'Infrastructure #1','#1',1,48,20,NULL,NULL,NULL,NULL),(2,'Infrastructure #2','#2',0,387,645,NULL,NULL,NULL,NULL);
/*!40000 ALTER TABLE `infrastructure` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ipv4address`
--

DROP TABLE IF EXISTS `ipv4address`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ipv4address` (
  `id` int NOT NULL AUTO_INCREMENT,
  `vlanid` int DEFAULT NULL,
  `address` varchar(16) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `vlan_address` (`vlanid`,`address`),
  KEY `IDX_A44BCBEEF48D6D0` (`vlanid`),
  CONSTRAINT `FK_A44BCBEEF48D6D0` FOREIGN KEY (`vlanid`) REFERENCES `vlan` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=253 DEFAULT CHARSET=utf8mb3 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ipv4address`
--

LOCK TABLES `ipv4address` WRITE;
/*!40000 ALTER TABLE `ipv4address` DISABLE KEYS */;
INSERT INTO `ipv4address` VALUES (1,1,'10.1.0.1',NULL,NULL),(2,1,'10.1.0.2',NULL,NULL),(3,1,'10.1.0.3',NULL,NULL),(4,1,'10.1.0.4',NULL,NULL),(5,1,'10.1.0.5',NULL,NULL),(6,1,'10.1.0.6',NULL,NULL),(7,1,'10.1.0.7',NULL,NULL),(8,1,'10.1.0.8',NULL,NULL),(9,1,'10.1.0.9',NULL,NULL),(10,1,'10.1.0.10',NULL,NULL),(11,1,'10.1.0.11',NULL,NULL),(12,1,'10.1.0.12',NULL,NULL),(13,1,'10.1.0.13',NULL,NULL),(14,1,'10.1.0.14',NULL,NULL),(15,1,'10.1.0.15',NULL,NULL),(16,1,'10.1.0.16',NULL,NULL),(17,1,'10.1.0.17',NULL,NULL),(18,1,'10.1.0.18',NULL,NULL),(19,1,'10.1.0.19',NULL,NULL),(20,1,'10.1.0.20',NULL,NULL),(21,1,'10.1.0.21',NULL,NULL),(22,1,'10.1.0.22',NULL,NULL),(23,1,'10.1.0.23',NULL,NULL),(24,1,'10.1.0.24',NULL,NULL),(25,1,'10.1.0.25',NULL,NULL),(26,1,'10.1.0.26',NULL,NULL),(27,1,'10.1.0.27',NULL,NULL),(28,1,'10.1.0.28',NULL,NULL),(29,1,'10.1.0.29',NULL,NULL),(30,1,'10.1.0.30',NULL,NULL),(31,1,'10.1.0.31',NULL,NULL),(32,1,'10.1.0.32',NULL,NULL),(33,1,'10.1.0.33',NULL,NULL),(34,1,'10.1.0.34',NULL,NULL),(35,1,'10.1.0.35',NULL,NULL),(36,1,'10.1.0.36',NULL,NULL),(37,1,'10.1.0.37',NULL,NULL),(38,1,'10.1.0.38',NULL,NULL),(39,1,'10.1.0.39',NULL,NULL),(40,1,'10.1.0.40',NULL,NULL),(41,1,'10.1.0.41',NULL,NULL),(42,1,'10.1.0.42',NULL,NULL),(43,1,'10.1.0.43',NULL,NULL),(44,1,'10.1.0.44',NULL,NULL),(45,1,'10.1.0.45',NULL,NULL),(46,1,'10.1.0.46',NULL,NULL),(47,1,'10.1.0.47',NULL,NULL),(48,1,'10.1.0.48',NULL,NULL),(49,1,'10.1.0.49',NULL,NULL),(50,1,'10.1.0.50',NULL,NULL),(51,1,'10.1.0.51',NULL,NULL),(52,1,'10.1.0.52',NULL,NULL),(53,1,'10.1.0.53',NULL,NULL),(54,1,'10.1.0.54',NULL,NULL),(55,1,'10.1.0.55',NULL,NULL),(56,1,'10.1.0.56',NULL,NULL),(57,1,'10.1.0.57',NULL,NULL),(58,1,'10.1.0.58',NULL,NULL),(59,1,'10.1.0.59',NULL,NULL),(60,1,'10.1.0.60',NULL,NULL),(61,1,'10.1.0.61',NULL,NULL),(62,1,'10.1.0.62',NULL,NULL),(63,1,'10.1.0.63',NULL,NULL),(64,1,'10.1.0.64',NULL,NULL),(65,1,'10.1.0.65',NULL,NULL),(66,1,'10.1.0.66',NULL,NULL),(67,1,'10.1.0.67',NULL,NULL),(68,1,'10.1.0.68',NULL,NULL),(69,1,'10.1.0.69',NULL,NULL),(70,1,'10.1.0.70',NULL,NULL),(71,1,'10.1.0.71',NULL,NULL),(72,1,'10.1.0.72',NULL,NULL),(73,1,'10.1.0.73',NULL,NULL),(74,1,'10.1.0.74',NULL,NULL),(75,1,'10.1.0.75',NULL,NULL),(76,1,'10.1.0.76',NULL,NULL),(77,1,'10.1.0.77',NULL,NULL),(78,1,'10.1.0.78',NULL,NULL),(79,1,'10.1.0.79',NULL,NULL),(80,1,'10.1.0.80',NULL,NULL),(81,1,'10.1.0.81',NULL,NULL),(82,1,'10.1.0.82',NULL,NULL),(83,1,'10.1.0.83',NULL,NULL),(84,1,'10.1.0.84',NULL,NULL),(85,1,'10.1.0.85',NULL,NULL),(86,1,'10.1.0.86',NULL,NULL),(87,1,'10.1.0.87',NULL,NULL),(88,1,'10.1.0.88',NULL,NULL),(89,1,'10.1.0.89',NULL,NULL),(90,1,'10.1.0.90',NULL,NULL),(91,1,'10.1.0.91',NULL,NULL),(92,1,'10.1.0.92',NULL,NULL),(93,1,'10.1.0.93',NULL,NULL),(94,1,'10.1.0.94',NULL,NULL),(95,1,'10.1.0.95',NULL,NULL),(96,1,'10.1.0.96',NULL,NULL),(97,1,'10.1.0.97',NULL,NULL),(98,1,'10.1.0.98',NULL,NULL),(99,1,'10.1.0.99',NULL,NULL),(100,1,'10.1.0.100',NULL,NULL),(101,1,'10.1.0.101',NULL,NULL),(102,1,'10.1.0.102',NULL,NULL),(103,1,'10.1.0.103',NULL,NULL),(104,1,'10.1.0.104',NULL,NULL),(105,1,'10.1.0.105',NULL,NULL),(106,1,'10.1.0.106',NULL,NULL),(107,1,'10.1.0.107',NULL,NULL),(108,1,'10.1.0.108',NULL,NULL),(109,1,'10.1.0.109',NULL,NULL),(110,1,'10.1.0.110',NULL,NULL),(111,1,'10.1.0.111',NULL,NULL),(112,1,'10.1.0.112',NULL,NULL),(113,1,'10.1.0.113',NULL,NULL),(114,1,'10.1.0.114',NULL,NULL),(115,1,'10.1.0.115',NULL,NULL),(116,1,'10.1.0.116',NULL,NULL),(117,1,'10.1.0.117',NULL,NULL),(118,1,'10.1.0.118',NULL,NULL),(119,1,'10.1.0.119',NULL,NULL),(120,1,'10.1.0.120',NULL,NULL),(121,1,'10.1.0.121',NULL,NULL),(122,1,'10.1.0.122',NULL,NULL),(123,1,'10.1.0.123',NULL,NULL),(124,1,'10.1.0.124',NULL,NULL),(125,1,'10.1.0.125',NULL,NULL),(126,1,'10.1.0.126',NULL,NULL),(127,2,'10.2.0.1',NULL,NULL),(128,2,'10.2.0.2',NULL,NULL),(129,2,'10.2.0.3',NULL,NULL),(130,2,'10.2.0.4',NULL,NULL),(131,2,'10.2.0.5',NULL,NULL),(132,2,'10.2.0.6',NULL,NULL),(133,2,'10.2.0.7',NULL,NULL),(134,2,'10.2.0.8',NULL,NULL),(135,2,'10.2.0.9',NULL,NULL),(136,2,'10.2.0.10',NULL,NULL),(137,2,'10.2.0.11',NULL,NULL),(138,2,'10.2.0.12',NULL,NULL),(139,2,'10.2.0.13',NULL,NULL),(140,2,'10.2.0.14',NULL,NULL),(141,2,'10.2.0.15',NULL,NULL),(142,2,'10.2.0.16',NULL,NULL),(143,2,'10.2.0.17',NULL,NULL),(144,2,'10.2.0.18',NULL,NULL),(145,2,'10.2.0.19',NULL,NULL),(146,2,'10.2.0.20',NULL,NULL),(147,2,'10.2.0.21',NULL,NULL),(148,2,'10.2.0.22',NULL,NULL),(149,2,'10.2.0.23',NULL,NULL),(150,2,'10.2.0.24',NULL,NULL),(151,2,'10.2.0.25',NULL,NULL),(152,2,'10.2.0.26',NULL,NULL),(153,2,'10.2.0.27',NULL,NULL),(154,2,'10.2.0.28',NULL,NULL),(155,2,'10.2.0.29',NULL,NULL),(156,2,'10.2.0.30',NULL,NULL),(157,2,'10.2.0.31',NULL,NULL),(158,2,'10.2.0.32',NULL,NULL),(159,2,'10.2.0.33',NULL,NULL),(160,2,'10.2.0.34',NULL,NULL),(161,2,'10.2.0.35',NULL,NULL),(162,2,'10.2.0.36',NULL,NULL),(163,2,'10.2.0.37',NULL,NULL),(164,2,'10.2.0.38',NULL,NULL),(165,2,'10.2.0.39',NULL,NULL),(166,2,'10.2.0.40',NULL,NULL),(167,2,'10.2.0.41',NULL,NULL),(168,2,'10.2.0.42',NULL,NULL),(169,2,'10.2.0.43',NULL,NULL),(170,2,'10.2.0.44',NULL,NULL),(171,2,'10.2.0.45',NULL,NULL),(172,2,'10.2.0.46',NULL,NULL),(173,2,'10.2.0.47',NULL,NULL),(174,2,'10.2.0.48',NULL,NULL),(175,2,'10.2.0.49',NULL,NULL),(176,2,'10.2.0.50',NULL,NULL),(177,2,'10.2.0.51',NULL,NULL),(178,2,'10.2.0.52',NULL,NULL),(179,2,'10.2.0.53',NULL,NULL),(180,2,'10.2.0.54',NULL,NULL),(181,2,'10.2.0.55',NULL,NULL),(182,2,'10.2.0.56',NULL,NULL),(183,2,'10.2.0.57',NULL,NULL),(184,2,'10.2.0.58',NULL,NULL),(185,2,'10.2.0.59',NULL,NULL),(186,2,'10.2.0.60',NULL,NULL),(187,2,'10.2.0.61',NULL,NULL),(188,2,'10.2.0.62',NULL,NULL),(189,2,'10.2.0.63',NULL,NULL),(190,2,'10.2.0.64',NULL,NULL),(191,2,'10.2.0.65',NULL,NULL),(192,2,'10.2.0.66',NULL,NULL),(193,2,'10.2.0.67',NULL,NULL),(194,2,'10.2.0.68',NULL,NULL),(195,2,'10.2.0.69',NULL,NULL),(196,2,'10.2.0.70',NULL,NULL),(197,2,'10.2.0.71',NULL,NULL),(198,2,'10.2.0.72',NULL,NULL),(199,2,'10.2.0.73',NULL,NULL),(200,2,'10.2.0.74',NULL,NULL),(201,2,'10.2.0.75',NULL,NULL),(202,2,'10.2.0.76',NULL,NULL),(203,2,'10.2.0.77',NULL,NULL),(204,2,'10.2.0.78',NULL,NULL),(205,2,'10.2.0.79',NULL,NULL),(206,2,'10.2.0.80',NULL,NULL),(207,2,'10.2.0.81',NULL,NULL),(208,2,'10.2.0.82',NULL,NULL),(209,2,'10.2.0.83',NULL,NULL),(210,2,'10.2.0.84',NULL,NULL),(211,2,'10.2.0.85',NULL,NULL),(212,2,'10.2.0.86',NULL,NULL),(213,2,'10.2.0.87',NULL,NULL),(214,2,'10.2.0.88',NULL,NULL),(215,2,'10.2.0.89',NULL,NULL),(216,2,'10.2.0.90',NULL,NULL),(217,2,'10.2.0.91',NULL,NULL),(218,2,'10.2.0.92',NULL,NULL),(219,2,'10.2.0.93',NULL,NULL),(220,2,'10.2.0.94',NULL,NULL),(221,2,'10.2.0.95',NULL,NULL),(222,2,'10.2.0.96',NULL,NULL),(223,2,'10.2.0.97',NULL,NULL),(224,2,'10.2.0.98',NULL,NULL),(225,2,'10.2.0.99',NULL,NULL),(226,2,'10.2.0.100',NULL,NULL),(227,2,'10.2.0.101',NULL,NULL),(228,2,'10.2.0.102',NULL,NULL),(229,2,'10.2.0.103',NULL,NULL),(230,2,'10.2.0.104',NULL,NULL),(231,2,'10.2.0.105',NULL,NULL),(232,2,'10.2.0.106',NULL,NULL),(233,2,'10.2.0.107',NULL,NULL),(234,2,'10.2.0.108',NULL,NULL),(235,2,'10.2.0.109',NULL,NULL),(236,2,'10.2.0.110',NULL,NULL),(237,2,'10.2.0.111',NULL,NULL),(238,2,'10.2.0.112',NULL,NULL),(239,2,'10.2.0.113',NULL,NULL),(240,2,'10.2.0.114',NULL,NULL),(241,2,'10.2.0.115',NULL,NULL),(242,2,'10.2.0.116',NULL,NULL),(243,2,'10.2.0.117',NULL,NULL),(244,2,'10.2.0.118',NULL,NULL),(245,2,'10.2.0.119',NULL,NULL),(246,2,'10.2.0.120',NULL,NULL),(247,2,'10.2.0.121',NULL,NULL),(248,2,'10.2.0.122',NULL,NULL),(249,2,'10.2.0.123',NULL,NULL),(250,2,'10.2.0.124',NULL,NULL),(251,2,'10.2.0.125',NULL,NULL),(252,2,'10.2.0.126',NULL,NULL);
/*!40000 ALTER TABLE `ipv4address` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ipv6address`
--

DROP TABLE IF EXISTS `ipv6address`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ipv6address` (
  `id` int NOT NULL AUTO_INCREMENT,
  `vlanid` int DEFAULT NULL,
  `address` varchar(40) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `vlan_address` (`vlanid`,`address`),
  KEY `IDX_E66ECC93F48D6D0` (`vlanid`),
  CONSTRAINT `FK_E66ECC93F48D6D0` FOREIGN KEY (`vlanid`) REFERENCES `vlan` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=801 DEFAULT CHARSET=utf8mb3 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ipv6address`
--

LOCK TABLES `ipv6address` WRITE;
/*!40000 ALTER TABLE `ipv6address` DISABLE KEYS */;
INSERT INTO `ipv6address` VALUES (1,1,'2001:db8:1::1',NULL,NULL),(2,1,'2001:db8:1::2',NULL,NULL),(3,1,'2001:db8:1::3',NULL,NULL),(4,1,'2001:db8:1::4',NULL,NULL),(5,1,'2001:db8:1::5',NULL,NULL),(6,1,'2001:db8:1::6',NULL,NULL),(7,1,'2001:db8:1::7',NULL,NULL),(8,1,'2001:db8:1::8',NULL,NULL),(9,1,'2001:db8:1::9',NULL,NULL),(10,1,'2001:db8:1::a',NULL,NULL),(11,1,'2001:db8:1::b',NULL,NULL),(12,1,'2001:db8:1::c',NULL,NULL),(13,1,'2001:db8:1::d',NULL,NULL),(14,1,'2001:db8:1::e',NULL,NULL),(15,1,'2001:db8:1::f',NULL,NULL),(16,1,'2001:db8:1::10',NULL,NULL),(17,1,'2001:db8:1::11',NULL,NULL),(18,1,'2001:db8:1::12',NULL,NULL),(19,1,'2001:db8:1::13',NULL,NULL),(20,1,'2001:db8:1::14',NULL,NULL),(21,1,'2001:db8:1::15',NULL,NULL),(22,1,'2001:db8:1::16',NULL,NULL),(23,1,'2001:db8:1::17',NULL,NULL),(24,1,'2001:db8:1::18',NULL,NULL),(25,1,'2001:db8:1::19',NULL,NULL),(26,1,'2001:db8:1::1a',NULL,NULL),(27,1,'2001:db8:1::1b',NULL,NULL),(28,1,'2001:db8:1::1c',NULL,NULL),(29,1,'2001:db8:1::1d',NULL,NULL),(30,1,'2001:db8:1::1e',NULL,NULL),(31,1,'2001:db8:1::1f',NULL,NULL),(32,1,'2001:db8:1::20',NULL,NULL),(33,1,'2001:db8:1::21',NULL,NULL),(34,1,'2001:db8:1::22',NULL,NULL),(35,1,'2001:db8:1::23',NULL,NULL),(36,1,'2001:db8:1::24',NULL,NULL),(37,1,'2001:db8:1::25',NULL,NULL),(38,1,'2001:db8:1::26',NULL,NULL),(39,1,'2001:db8:1::27',NULL,NULL),(40,1,'2001:db8:1::28',NULL,NULL),(41,1,'2001:db8:1::29',NULL,NULL),(42,1,'2001:db8:1::2a',NULL,NULL),(43,1,'2001:db8:1::2b',NULL,NULL),(44,1,'2001:db8:1::2c',NULL,NULL),(45,1,'2001:db8:1::2d',NULL,NULL),(46,1,'2001:db8:1::2e',NULL,NULL),(47,1,'2001:db8:1::2f',NULL,NULL),(48,1,'2001:db8:1::30',NULL,NULL),(49,1,'2001:db8:1::31',NULL,NULL),(50,1,'2001:db8:1::32',NULL,NULL),(51,1,'2001:db8:1::33',NULL,NULL),(52,1,'2001:db8:1::34',NULL,NULL),(53,1,'2001:db8:1::35',NULL,NULL),(54,1,'2001:db8:1::36',NULL,NULL),(55,1,'2001:db8:1::37',NULL,NULL),(56,1,'2001:db8:1::38',NULL,NULL),(57,1,'2001:db8:1::39',NULL,NULL),(58,1,'2001:db8:1::3a',NULL,NULL),(59,1,'2001:db8:1::3b',NULL,NULL),(60,1,'2001:db8:1::3c',NULL,NULL),(61,1,'2001:db8:1::3d',NULL,NULL),(62,1,'2001:db8:1::3e',NULL,NULL),(63,1,'2001:db8:1::3f',NULL,NULL),(64,1,'2001:db8:1::40',NULL,NULL),(65,1,'2001:db8:1::41',NULL,NULL),(66,1,'2001:db8:1::42',NULL,NULL),(67,1,'2001:db8:1::43',NULL,NULL),(68,1,'2001:db8:1::44',NULL,NULL),(69,1,'2001:db8:1::45',NULL,NULL),(70,1,'2001:db8:1::46',NULL,NULL),(71,1,'2001:db8:1::47',NULL,NULL),(72,1,'2001:db8:1::48',NULL,NULL),(73,1,'2001:db8:1::49',NULL,NULL),(74,1,'2001:db8:1::4a',NULL,NULL),(75,1,'2001:db8:1::4b',NULL,NULL),(76,1,'2001:db8:1::4c',NULL,NULL),(77,1,'2001:db8:1::4d',NULL,NULL),(78,1,'2001:db8:1::4e',NULL,NULL),(79,1,'2001:db8:1::4f',NULL,NULL),(80,1,'2001:db8:1::50',NULL,NULL),(81,1,'2001:db8:1::51',NULL,NULL),(82,1,'2001:db8:1::52',NULL,NULL),(83,1,'2001:db8:1::53',NULL,NULL),(84,1,'2001:db8:1::54',NULL,NULL),(85,1,'2001:db8:1::55',NULL,NULL),(86,1,'2001:db8:1::56',NULL,NULL),(87,1,'2001:db8:1::57',NULL,NULL),(88,1,'2001:db8:1::58',NULL,NULL),(89,1,'2001:db8:1::59',NULL,NULL),(90,1,'2001:db8:1::5a',NULL,NULL),(91,1,'2001:db8:1::5b',NULL,NULL),(92,1,'2001:db8:1::5c',NULL,NULL),(93,1,'2001:db8:1::5d',NULL,NULL),(94,1,'2001:db8:1::5e',NULL,NULL),(95,1,'2001:db8:1::5f',NULL,NULL),(96,1,'2001:db8:1::60',NULL,NULL),(97,1,'2001:db8:1::61',NULL,NULL),(98,1,'2001:db8:1::62',NULL,NULL),(99,1,'2001:db8:1::63',NULL,NULL),(100,1,'2001:db8:1::64',NULL,NULL),(101,1,'2001:db8:1::65',NULL,NULL),(102,1,'2001:db8:1::66',NULL,NULL),(103,1,'2001:db8:1::67',NULL,NULL),(104,1,'2001:db8:1::68',NULL,NULL),(105,1,'2001:db8:1::69',NULL,NULL),(106,1,'2001:db8:1::6a',NULL,NULL),(107,1,'2001:db8:1::6b',NULL,NULL),(108,1,'2001:db8:1::6c',NULL,NULL),(109,1,'2001:db8:1::6d',NULL,NULL),(110,1,'2001:db8:1::6e',NULL,NULL),(111,1,'2001:db8:1::6f',NULL,NULL),(112,1,'2001:db8:1::70',NULL,NULL),(113,1,'2001:db8:1::71',NULL,NULL),(114,1,'2001:db8:1::72',NULL,NULL),(115,1,'2001:db8:1::73',NULL,NULL),(116,1,'2001:db8:1::74',NULL,NULL),(117,1,'2001:db8:1::75',NULL,NULL),(118,1,'2001:db8:1::76',NULL,NULL),(119,1,'2001:db8:1::77',NULL,NULL),(120,1,'2001:db8:1::78',NULL,NULL),(121,1,'2001:db8:1::79',NULL,NULL),(122,1,'2001:db8:1::7a',NULL,NULL),(123,1,'2001:db8:1::7b',NULL,NULL),(124,1,'2001:db8:1::7c',NULL,NULL),(125,1,'2001:db8:1::7d',NULL,NULL),(126,1,'2001:db8:1::7e',NULL,NULL),(127,1,'2001:db8:1::7f',NULL,NULL),(128,1,'2001:db8:1::80',NULL,NULL),(129,1,'2001:db8:1::81',NULL,NULL),(130,1,'2001:db8:1::82',NULL,NULL),(131,1,'2001:db8:1::83',NULL,NULL),(132,1,'2001:db8:1::84',NULL,NULL),(133,1,'2001:db8:1::85',NULL,NULL),(134,1,'2001:db8:1::86',NULL,NULL),(135,1,'2001:db8:1::87',NULL,NULL),(136,1,'2001:db8:1::88',NULL,NULL),(137,1,'2001:db8:1::89',NULL,NULL),(138,1,'2001:db8:1::8a',NULL,NULL),(139,1,'2001:db8:1::8b',NULL,NULL),(140,1,'2001:db8:1::8c',NULL,NULL),(141,1,'2001:db8:1::8d',NULL,NULL),(142,1,'2001:db8:1::8e',NULL,NULL),(143,1,'2001:db8:1::8f',NULL,NULL),(144,1,'2001:db8:1::90',NULL,NULL),(145,1,'2001:db8:1::91',NULL,NULL),(146,1,'2001:db8:1::92',NULL,NULL),(147,1,'2001:db8:1::93',NULL,NULL),(148,1,'2001:db8:1::94',NULL,NULL),(149,1,'2001:db8:1::95',NULL,NULL),(150,1,'2001:db8:1::96',NULL,NULL),(151,1,'2001:db8:1::97',NULL,NULL),(152,1,'2001:db8:1::98',NULL,NULL),(153,1,'2001:db8:1::99',NULL,NULL),(154,1,'2001:db8:1::9a',NULL,NULL),(155,1,'2001:db8:1::9b',NULL,NULL),(156,1,'2001:db8:1::9c',NULL,NULL),(157,1,'2001:db8:1::9d',NULL,NULL),(158,1,'2001:db8:1::9e',NULL,NULL),(159,1,'2001:db8:1::9f',NULL,NULL),(160,1,'2001:db8:1::a0',NULL,NULL),(161,1,'2001:db8:1::a1',NULL,NULL),(162,1,'2001:db8:1::a2',NULL,NULL),(163,1,'2001:db8:1::a3',NULL,NULL),(164,1,'2001:db8:1::a4',NULL,NULL),(165,1,'2001:db8:1::a5',NULL,NULL),(166,1,'2001:db8:1::a6',NULL,NULL),(167,1,'2001:db8:1::a7',NULL,NULL),(168,1,'2001:db8:1::a8',NULL,NULL),(169,1,'2001:db8:1::a9',NULL,NULL),(170,1,'2001:db8:1::aa',NULL,NULL),(171,1,'2001:db8:1::ab',NULL,NULL),(172,1,'2001:db8:1::ac',NULL,NULL),(173,1,'2001:db8:1::ad',NULL,NULL),(174,1,'2001:db8:1::ae',NULL,NULL),(175,1,'2001:db8:1::af',NULL,NULL),(176,1,'2001:db8:1::b0',NULL,NULL),(177,1,'2001:db8:1::b1',NULL,NULL),(178,1,'2001:db8:1::b2',NULL,NULL),(179,1,'2001:db8:1::b3',NULL,NULL),(180,1,'2001:db8:1::b4',NULL,NULL),(181,1,'2001:db8:1::b5',NULL,NULL),(182,1,'2001:db8:1::b6',NULL,NULL),(183,1,'2001:db8:1::b7',NULL,NULL),(184,1,'2001:db8:1::b8',NULL,NULL),(185,1,'2001:db8:1::b9',NULL,NULL),(186,1,'2001:db8:1::ba',NULL,NULL),(187,1,'2001:db8:1::bb',NULL,NULL),(188,1,'2001:db8:1::bc',NULL,NULL),(189,1,'2001:db8:1::bd',NULL,NULL),(190,1,'2001:db8:1::be',NULL,NULL),(191,1,'2001:db8:1::bf',NULL,NULL),(192,1,'2001:db8:1::c0',NULL,NULL),(193,1,'2001:db8:1::c1',NULL,NULL),(194,1,'2001:db8:1::c2',NULL,NULL),(195,1,'2001:db8:1::c3',NULL,NULL),(196,1,'2001:db8:1::c4',NULL,NULL),(197,1,'2001:db8:1::c5',NULL,NULL),(198,1,'2001:db8:1::c6',NULL,NULL),(199,1,'2001:db8:1::c7',NULL,NULL),(200,1,'2001:db8:1::c8',NULL,NULL),(201,1,'2001:db8:1::c9',NULL,NULL),(202,1,'2001:db8:1::ca',NULL,NULL),(203,1,'2001:db8:1::cb',NULL,NULL),(204,1,'2001:db8:1::cc',NULL,NULL),(205,1,'2001:db8:1::cd',NULL,NULL),(206,1,'2001:db8:1::ce',NULL,NULL),(207,1,'2001:db8:1::cf',NULL,NULL),(208,1,'2001:db8:1::d0',NULL,NULL),(209,1,'2001:db8:1::d1',NULL,NULL),(210,1,'2001:db8:1::d2',NULL,NULL),(211,1,'2001:db8:1::d3',NULL,NULL),(212,1,'2001:db8:1::d4',NULL,NULL),(213,1,'2001:db8:1::d5',NULL,NULL),(214,1,'2001:db8:1::d6',NULL,NULL),(215,1,'2001:db8:1::d7',NULL,NULL),(216,1,'2001:db8:1::d8',NULL,NULL),(217,1,'2001:db8:1::d9',NULL,NULL),(218,1,'2001:db8:1::da',NULL,NULL),(219,1,'2001:db8:1::db',NULL,NULL),(220,1,'2001:db8:1::dc',NULL,NULL),(221,1,'2001:db8:1::dd',NULL,NULL),(222,1,'2001:db8:1::de',NULL,NULL),(223,1,'2001:db8:1::df',NULL,NULL),(224,1,'2001:db8:1::e0',NULL,NULL),(225,1,'2001:db8:1::e1',NULL,NULL),(226,1,'2001:db8:1::e2',NULL,NULL),(227,1,'2001:db8:1::e3',NULL,NULL),(228,1,'2001:db8:1::e4',NULL,NULL),(229,1,'2001:db8:1::e5',NULL,NULL),(230,1,'2001:db8:1::e6',NULL,NULL),(231,1,'2001:db8:1::e7',NULL,NULL),(232,1,'2001:db8:1::e8',NULL,NULL),(233,1,'2001:db8:1::e9',NULL,NULL),(234,1,'2001:db8:1::ea',NULL,NULL),(235,1,'2001:db8:1::eb',NULL,NULL),(236,1,'2001:db8:1::ec',NULL,NULL),(237,1,'2001:db8:1::ed',NULL,NULL),(238,1,'2001:db8:1::ee',NULL,NULL),(239,1,'2001:db8:1::ef',NULL,NULL),(240,1,'2001:db8:1::f0',NULL,NULL),(241,1,'2001:db8:1::f1',NULL,NULL),(242,1,'2001:db8:1::f2',NULL,NULL),(243,1,'2001:db8:1::f3',NULL,NULL),(244,1,'2001:db8:1::f4',NULL,NULL),(245,1,'2001:db8:1::f5',NULL,NULL),(246,1,'2001:db8:1::f6',NULL,NULL),(247,1,'2001:db8:1::f7',NULL,NULL),(248,1,'2001:db8:1::f8',NULL,NULL),(249,1,'2001:db8:1::f9',NULL,NULL),(250,1,'2001:db8:1::fa',NULL,NULL),(251,1,'2001:db8:1::fb',NULL,NULL),(252,1,'2001:db8:1::fc',NULL,NULL),(253,1,'2001:db8:1::fd',NULL,NULL),(254,1,'2001:db8:1::fe',NULL,NULL),(255,1,'2001:db8:1::ff',NULL,NULL),(256,1,'2001:db8:1::100',NULL,NULL),(257,1,'2001:db8:1::101',NULL,NULL),(258,1,'2001:db8:1::102',NULL,NULL),(259,1,'2001:db8:1::103',NULL,NULL),(260,1,'2001:db8:1::104',NULL,NULL),(261,1,'2001:db8:1::105',NULL,NULL),(262,1,'2001:db8:1::106',NULL,NULL),(263,1,'2001:db8:1::107',NULL,NULL),(264,1,'2001:db8:1::108',NULL,NULL),(265,1,'2001:db8:1::109',NULL,NULL),(266,1,'2001:db8:1::10a',NULL,NULL),(267,1,'2001:db8:1::10b',NULL,NULL),(268,1,'2001:db8:1::10c',NULL,NULL),(269,1,'2001:db8:1::10d',NULL,NULL),(270,1,'2001:db8:1::10e',NULL,NULL),(271,1,'2001:db8:1::10f',NULL,NULL),(272,1,'2001:db8:1::110',NULL,NULL),(273,1,'2001:db8:1::111',NULL,NULL),(274,1,'2001:db8:1::112',NULL,NULL),(275,1,'2001:db8:1::113',NULL,NULL),(276,1,'2001:db8:1::114',NULL,NULL),(277,1,'2001:db8:1::115',NULL,NULL),(278,1,'2001:db8:1::116',NULL,NULL),(279,1,'2001:db8:1::117',NULL,NULL),(280,1,'2001:db8:1::118',NULL,NULL),(281,1,'2001:db8:1::119',NULL,NULL),(282,1,'2001:db8:1::11a',NULL,NULL),(283,1,'2001:db8:1::11b',NULL,NULL),(284,1,'2001:db8:1::11c',NULL,NULL),(285,1,'2001:db8:1::11d',NULL,NULL),(286,1,'2001:db8:1::11e',NULL,NULL),(287,1,'2001:db8:1::11f',NULL,NULL),(288,1,'2001:db8:1::120',NULL,NULL),(289,1,'2001:db8:1::121',NULL,NULL),(290,1,'2001:db8:1::122',NULL,NULL),(291,1,'2001:db8:1::123',NULL,NULL),(292,1,'2001:db8:1::124',NULL,NULL),(293,1,'2001:db8:1::125',NULL,NULL),(294,1,'2001:db8:1::126',NULL,NULL),(295,1,'2001:db8:1::127',NULL,NULL),(296,1,'2001:db8:1::128',NULL,NULL),(297,1,'2001:db8:1::129',NULL,NULL),(298,1,'2001:db8:1::12a',NULL,NULL),(299,1,'2001:db8:1::12b',NULL,NULL),(300,1,'2001:db8:1::12c',NULL,NULL),(301,1,'2001:db8:1::12d',NULL,NULL),(302,1,'2001:db8:1::12e',NULL,NULL),(303,1,'2001:db8:1::12f',NULL,NULL),(304,1,'2001:db8:1::130',NULL,NULL),(305,1,'2001:db8:1::131',NULL,NULL),(306,1,'2001:db8:1::132',NULL,NULL),(307,1,'2001:db8:1::133',NULL,NULL),(308,1,'2001:db8:1::134',NULL,NULL),(309,1,'2001:db8:1::135',NULL,NULL),(310,1,'2001:db8:1::136',NULL,NULL),(311,1,'2001:db8:1::137',NULL,NULL),(312,1,'2001:db8:1::138',NULL,NULL),(313,1,'2001:db8:1::139',NULL,NULL),(314,1,'2001:db8:1::13a',NULL,NULL),(315,1,'2001:db8:1::13b',NULL,NULL),(316,1,'2001:db8:1::13c',NULL,NULL),(317,1,'2001:db8:1::13d',NULL,NULL),(318,1,'2001:db8:1::13e',NULL,NULL),(319,1,'2001:db8:1::13f',NULL,NULL),(320,1,'2001:db8:1::140',NULL,NULL),(321,1,'2001:db8:1::141',NULL,NULL),(322,1,'2001:db8:1::142',NULL,NULL),(323,1,'2001:db8:1::143',NULL,NULL),(324,1,'2001:db8:1::144',NULL,NULL),(325,1,'2001:db8:1::145',NULL,NULL),(326,1,'2001:db8:1::146',NULL,NULL),(327,1,'2001:db8:1::147',NULL,NULL),(328,1,'2001:db8:1::148',NULL,NULL),(329,1,'2001:db8:1::149',NULL,NULL),(330,1,'2001:db8:1::14a',NULL,NULL),(331,1,'2001:db8:1::14b',NULL,NULL),(332,1,'2001:db8:1::14c',NULL,NULL),(333,1,'2001:db8:1::14d',NULL,NULL),(334,1,'2001:db8:1::14e',NULL,NULL),(335,1,'2001:db8:1::14f',NULL,NULL),(336,1,'2001:db8:1::150',NULL,NULL),(337,1,'2001:db8:1::151',NULL,NULL),(338,1,'2001:db8:1::152',NULL,NULL),(339,1,'2001:db8:1::153',NULL,NULL),(340,1,'2001:db8:1::154',NULL,NULL),(341,1,'2001:db8:1::155',NULL,NULL),(342,1,'2001:db8:1::156',NULL,NULL),(343,1,'2001:db8:1::157',NULL,NULL),(344,1,'2001:db8:1::158',NULL,NULL),(345,1,'2001:db8:1::159',NULL,NULL),(346,1,'2001:db8:1::15a',NULL,NULL),(347,1,'2001:db8:1::15b',NULL,NULL),(348,1,'2001:db8:1::15c',NULL,NULL),(349,1,'2001:db8:1::15d',NULL,NULL),(350,1,'2001:db8:1::15e',NULL,NULL),(351,1,'2001:db8:1::15f',NULL,NULL),(352,1,'2001:db8:1::160',NULL,NULL),(353,1,'2001:db8:1::161',NULL,NULL),(354,1,'2001:db8:1::162',NULL,NULL),(355,1,'2001:db8:1::163',NULL,NULL),(356,1,'2001:db8:1::164',NULL,NULL),(357,1,'2001:db8:1::165',NULL,NULL),(358,1,'2001:db8:1::166',NULL,NULL),(359,1,'2001:db8:1::167',NULL,NULL),(360,1,'2001:db8:1::168',NULL,NULL),(361,1,'2001:db8:1::169',NULL,NULL),(362,1,'2001:db8:1::16a',NULL,NULL),(363,1,'2001:db8:1::16b',NULL,NULL),(364,1,'2001:db8:1::16c',NULL,NULL),(365,1,'2001:db8:1::16d',NULL,NULL),(366,1,'2001:db8:1::16e',NULL,NULL),(367,1,'2001:db8:1::16f',NULL,NULL),(368,1,'2001:db8:1::170',NULL,NULL),(369,1,'2001:db8:1::171',NULL,NULL),(370,1,'2001:db8:1::172',NULL,NULL),(371,1,'2001:db8:1::173',NULL,NULL),(372,1,'2001:db8:1::174',NULL,NULL),(373,1,'2001:db8:1::175',NULL,NULL),(374,1,'2001:db8:1::176',NULL,NULL),(375,1,'2001:db8:1::177',NULL,NULL),(376,1,'2001:db8:1::178',NULL,NULL),(377,1,'2001:db8:1::179',NULL,NULL),(378,1,'2001:db8:1::17a',NULL,NULL),(379,1,'2001:db8:1::17b',NULL,NULL),(380,1,'2001:db8:1::17c',NULL,NULL),(381,1,'2001:db8:1::17d',NULL,NULL),(382,1,'2001:db8:1::17e',NULL,NULL),(383,1,'2001:db8:1::17f',NULL,NULL),(384,1,'2001:db8:1::180',NULL,NULL),(385,1,'2001:db8:1::181',NULL,NULL),(386,1,'2001:db8:1::182',NULL,NULL),(387,1,'2001:db8:1::183',NULL,NULL),(388,1,'2001:db8:1::184',NULL,NULL),(389,1,'2001:db8:1::185',NULL,NULL),(390,1,'2001:db8:1::186',NULL,NULL),(391,1,'2001:db8:1::187',NULL,NULL),(392,1,'2001:db8:1::188',NULL,NULL),(393,1,'2001:db8:1::189',NULL,NULL),(394,1,'2001:db8:1::18a',NULL,NULL),(395,1,'2001:db8:1::18b',NULL,NULL),(396,1,'2001:db8:1::18c',NULL,NULL),(397,1,'2001:db8:1::18d',NULL,NULL),(398,1,'2001:db8:1::18e',NULL,NULL),(399,1,'2001:db8:1::18f',NULL,NULL),(400,1,'2001:db8:1::190',NULL,NULL),(401,2,'2001:db8:2::1',NULL,NULL),(402,2,'2001:db8:2::2',NULL,NULL),(403,2,'2001:db8:2::3',NULL,NULL),(404,2,'2001:db8:2::4',NULL,NULL),(405,2,'2001:db8:2::5',NULL,NULL),(406,2,'2001:db8:2::6',NULL,NULL),(407,2,'2001:db8:2::7',NULL,NULL),(408,2,'2001:db8:2::8',NULL,NULL),(409,2,'2001:db8:2::9',NULL,NULL),(410,2,'2001:db8:2::a',NULL,NULL),(411,2,'2001:db8:2::b',NULL,NULL),(412,2,'2001:db8:2::c',NULL,NULL),(413,2,'2001:db8:2::d',NULL,NULL),(414,2,'2001:db8:2::e',NULL,NULL),(415,2,'2001:db8:2::f',NULL,NULL),(416,2,'2001:db8:2::10',NULL,NULL),(417,2,'2001:db8:2::11',NULL,NULL),(418,2,'2001:db8:2::12',NULL,NULL),(419,2,'2001:db8:2::13',NULL,NULL),(420,2,'2001:db8:2::14',NULL,NULL),(421,2,'2001:db8:2::15',NULL,NULL),(422,2,'2001:db8:2::16',NULL,NULL),(423,2,'2001:db8:2::17',NULL,NULL),(424,2,'2001:db8:2::18',NULL,NULL),(425,2,'2001:db8:2::19',NULL,NULL),(426,2,'2001:db8:2::1a',NULL,NULL),(427,2,'2001:db8:2::1b',NULL,NULL),(428,2,'2001:db8:2::1c',NULL,NULL),(429,2,'2001:db8:2::1d',NULL,NULL),(430,2,'2001:db8:2::1e',NULL,NULL),(431,2,'2001:db8:2::1f',NULL,NULL),(432,2,'2001:db8:2::20',NULL,NULL),(433,2,'2001:db8:2::21',NULL,NULL),(434,2,'2001:db8:2::22',NULL,NULL),(435,2,'2001:db8:2::23',NULL,NULL),(436,2,'2001:db8:2::24',NULL,NULL),(437,2,'2001:db8:2::25',NULL,NULL),(438,2,'2001:db8:2::26',NULL,NULL),(439,2,'2001:db8:2::27',NULL,NULL),(440,2,'2001:db8:2::28',NULL,NULL),(441,2,'2001:db8:2::29',NULL,NULL),(442,2,'2001:db8:2::2a',NULL,NULL),(443,2,'2001:db8:2::2b',NULL,NULL),(444,2,'2001:db8:2::2c',NULL,NULL),(445,2,'2001:db8:2::2d',NULL,NULL),(446,2,'2001:db8:2::2e',NULL,NULL),(447,2,'2001:db8:2::2f',NULL,NULL),(448,2,'2001:db8:2::30',NULL,NULL),(449,2,'2001:db8:2::31',NULL,NULL),(450,2,'2001:db8:2::32',NULL,NULL),(451,2,'2001:db8:2::33',NULL,NULL),(452,2,'2001:db8:2::34',NULL,NULL),(453,2,'2001:db8:2::35',NULL,NULL),(454,2,'2001:db8:2::36',NULL,NULL),(455,2,'2001:db8:2::37',NULL,NULL),(456,2,'2001:db8:2::38',NULL,NULL),(457,2,'2001:db8:2::39',NULL,NULL),(458,2,'2001:db8:2::3a',NULL,NULL),(459,2,'2001:db8:2::3b',NULL,NULL),(460,2,'2001:db8:2::3c',NULL,NULL),(461,2,'2001:db8:2::3d',NULL,NULL),(462,2,'2001:db8:2::3e',NULL,NULL),(463,2,'2001:db8:2::3f',NULL,NULL),(464,2,'2001:db8:2::40',NULL,NULL),(465,2,'2001:db8:2::41',NULL,NULL),(466,2,'2001:db8:2::42',NULL,NULL),(467,2,'2001:db8:2::43',NULL,NULL),(468,2,'2001:db8:2::44',NULL,NULL),(469,2,'2001:db8:2::45',NULL,NULL),(470,2,'2001:db8:2::46',NULL,NULL),(471,2,'2001:db8:2::47',NULL,NULL),(472,2,'2001:db8:2::48',NULL,NULL),(473,2,'2001:db8:2::49',NULL,NULL),(474,2,'2001:db8:2::4a',NULL,NULL),(475,2,'2001:db8:2::4b',NULL,NULL),(476,2,'2001:db8:2::4c',NULL,NULL),(477,2,'2001:db8:2::4d',NULL,NULL),(478,2,'2001:db8:2::4e',NULL,NULL),(479,2,'2001:db8:2::4f',NULL,NULL),(480,2,'2001:db8:2::50',NULL,NULL),(481,2,'2001:db8:2::51',NULL,NULL),(482,2,'2001:db8:2::52',NULL,NULL),(483,2,'2001:db8:2::53',NULL,NULL),(484,2,'2001:db8:2::54',NULL,NULL),(485,2,'2001:db8:2::55',NULL,NULL),(486,2,'2001:db8:2::56',NULL,NULL),(487,2,'2001:db8:2::57',NULL,NULL),(488,2,'2001:db8:2::58',NULL,NULL),(489,2,'2001:db8:2::59',NULL,NULL),(490,2,'2001:db8:2::5a',NULL,NULL),(491,2,'2001:db8:2::5b',NULL,NULL),(492,2,'2001:db8:2::5c',NULL,NULL),(493,2,'2001:db8:2::5d',NULL,NULL),(494,2,'2001:db8:2::5e',NULL,NULL),(495,2,'2001:db8:2::5f',NULL,NULL),(496,2,'2001:db8:2::60',NULL,NULL),(497,2,'2001:db8:2::61',NULL,NULL),(498,2,'2001:db8:2::62',NULL,NULL),(499,2,'2001:db8:2::63',NULL,NULL),(500,2,'2001:db8:2::64',NULL,NULL),(501,2,'2001:db8:2::65',NULL,NULL),(502,2,'2001:db8:2::66',NULL,NULL),(503,2,'2001:db8:2::67',NULL,NULL),(504,2,'2001:db8:2::68',NULL,NULL),(505,2,'2001:db8:2::69',NULL,NULL),(506,2,'2001:db8:2::6a',NULL,NULL),(507,2,'2001:db8:2::6b',NULL,NULL),(508,2,'2001:db8:2::6c',NULL,NULL),(509,2,'2001:db8:2::6d',NULL,NULL),(510,2,'2001:db8:2::6e',NULL,NULL),(511,2,'2001:db8:2::6f',NULL,NULL),(512,2,'2001:db8:2::70',NULL,NULL),(513,2,'2001:db8:2::71',NULL,NULL),(514,2,'2001:db8:2::72',NULL,NULL),(515,2,'2001:db8:2::73',NULL,NULL),(516,2,'2001:db8:2::74',NULL,NULL),(517,2,'2001:db8:2::75',NULL,NULL),(518,2,'2001:db8:2::76',NULL,NULL),(519,2,'2001:db8:2::77',NULL,NULL),(520,2,'2001:db8:2::78',NULL,NULL),(521,2,'2001:db8:2::79',NULL,NULL),(522,2,'2001:db8:2::7a',NULL,NULL),(523,2,'2001:db8:2::7b',NULL,NULL),(524,2,'2001:db8:2::7c',NULL,NULL),(525,2,'2001:db8:2::7d',NULL,NULL),(526,2,'2001:db8:2::7e',NULL,NULL),(527,2,'2001:db8:2::7f',NULL,NULL),(528,2,'2001:db8:2::80',NULL,NULL),(529,2,'2001:db8:2::81',NULL,NULL),(530,2,'2001:db8:2::82',NULL,NULL),(531,2,'2001:db8:2::83',NULL,NULL),(532,2,'2001:db8:2::84',NULL,NULL),(533,2,'2001:db8:2::85',NULL,NULL),(534,2,'2001:db8:2::86',NULL,NULL),(535,2,'2001:db8:2::87',NULL,NULL),(536,2,'2001:db8:2::88',NULL,NULL),(537,2,'2001:db8:2::89',NULL,NULL),(538,2,'2001:db8:2::8a',NULL,NULL),(539,2,'2001:db8:2::8b',NULL,NULL),(540,2,'2001:db8:2::8c',NULL,NULL),(541,2,'2001:db8:2::8d',NULL,NULL),(542,2,'2001:db8:2::8e',NULL,NULL),(543,2,'2001:db8:2::8f',NULL,NULL),(544,2,'2001:db8:2::90',NULL,NULL),(545,2,'2001:db8:2::91',NULL,NULL),(546,2,'2001:db8:2::92',NULL,NULL),(547,2,'2001:db8:2::93',NULL,NULL),(548,2,'2001:db8:2::94',NULL,NULL),(549,2,'2001:db8:2::95',NULL,NULL),(550,2,'2001:db8:2::96',NULL,NULL),(551,2,'2001:db8:2::97',NULL,NULL),(552,2,'2001:db8:2::98',NULL,NULL),(553,2,'2001:db8:2::99',NULL,NULL),(554,2,'2001:db8:2::9a',NULL,NULL),(555,2,'2001:db8:2::9b',NULL,NULL),(556,2,'2001:db8:2::9c',NULL,NULL),(557,2,'2001:db8:2::9d',NULL,NULL),(558,2,'2001:db8:2::9e',NULL,NULL),(559,2,'2001:db8:2::9f',NULL,NULL),(560,2,'2001:db8:2::a0',NULL,NULL),(561,2,'2001:db8:2::a1',NULL,NULL),(562,2,'2001:db8:2::a2',NULL,NULL),(563,2,'2001:db8:2::a3',NULL,NULL),(564,2,'2001:db8:2::a4',NULL,NULL),(565,2,'2001:db8:2::a5',NULL,NULL),(566,2,'2001:db8:2::a6',NULL,NULL),(567,2,'2001:db8:2::a7',NULL,NULL),(568,2,'2001:db8:2::a8',NULL,NULL),(569,2,'2001:db8:2::a9',NULL,NULL),(570,2,'2001:db8:2::aa',NULL,NULL),(571,2,'2001:db8:2::ab',NULL,NULL),(572,2,'2001:db8:2::ac',NULL,NULL),(573,2,'2001:db8:2::ad',NULL,NULL),(574,2,'2001:db8:2::ae',NULL,NULL),(575,2,'2001:db8:2::af',NULL,NULL),(576,2,'2001:db8:2::b0',NULL,NULL),(577,2,'2001:db8:2::b1',NULL,NULL),(578,2,'2001:db8:2::b2',NULL,NULL),(579,2,'2001:db8:2::b3',NULL,NULL),(580,2,'2001:db8:2::b4',NULL,NULL),(581,2,'2001:db8:2::b5',NULL,NULL),(582,2,'2001:db8:2::b6',NULL,NULL),(583,2,'2001:db8:2::b7',NULL,NULL),(584,2,'2001:db8:2::b8',NULL,NULL),(585,2,'2001:db8:2::b9',NULL,NULL),(586,2,'2001:db8:2::ba',NULL,NULL),(587,2,'2001:db8:2::bb',NULL,NULL),(588,2,'2001:db8:2::bc',NULL,NULL),(589,2,'2001:db8:2::bd',NULL,NULL),(590,2,'2001:db8:2::be',NULL,NULL),(591,2,'2001:db8:2::bf',NULL,NULL),(592,2,'2001:db8:2::c0',NULL,NULL),(593,2,'2001:db8:2::c1',NULL,NULL),(594,2,'2001:db8:2::c2',NULL,NULL),(595,2,'2001:db8:2::c3',NULL,NULL),(596,2,'2001:db8:2::c4',NULL,NULL),(597,2,'2001:db8:2::c5',NULL,NULL),(598,2,'2001:db8:2::c6',NULL,NULL),(599,2,'2001:db8:2::c7',NULL,NULL),(600,2,'2001:db8:2::c8',NULL,NULL),(601,2,'2001:db8:2::c9',NULL,NULL),(602,2,'2001:db8:2::ca',NULL,NULL),(603,2,'2001:db8:2::cb',NULL,NULL),(604,2,'2001:db8:2::cc',NULL,NULL),(605,2,'2001:db8:2::cd',NULL,NULL),(606,2,'2001:db8:2::ce',NULL,NULL),(607,2,'2001:db8:2::cf',NULL,NULL),(608,2,'2001:db8:2::d0',NULL,NULL),(609,2,'2001:db8:2::d1',NULL,NULL),(610,2,'2001:db8:2::d2',NULL,NULL),(611,2,'2001:db8:2::d3',NULL,NULL),(612,2,'2001:db8:2::d4',NULL,NULL),(613,2,'2001:db8:2::d5',NULL,NULL),(614,2,'2001:db8:2::d6',NULL,NULL),(615,2,'2001:db8:2::d7',NULL,NULL),(616,2,'2001:db8:2::d8',NULL,NULL),(617,2,'2001:db8:2::d9',NULL,NULL),(618,2,'2001:db8:2::da',NULL,NULL),(619,2,'2001:db8:2::db',NULL,NULL),(620,2,'2001:db8:2::dc',NULL,NULL),(621,2,'2001:db8:2::dd',NULL,NULL),(622,2,'2001:db8:2::de',NULL,NULL),(623,2,'2001:db8:2::df',NULL,NULL),(624,2,'2001:db8:2::e0',NULL,NULL),(625,2,'2001:db8:2::e1',NULL,NULL),(626,2,'2001:db8:2::e2',NULL,NULL),(627,2,'2001:db8:2::e3',NULL,NULL),(628,2,'2001:db8:2::e4',NULL,NULL),(629,2,'2001:db8:2::e5',NULL,NULL),(630,2,'2001:db8:2::e6',NULL,NULL),(631,2,'2001:db8:2::e7',NULL,NULL),(632,2,'2001:db8:2::e8',NULL,NULL),(633,2,'2001:db8:2::e9',NULL,NULL),(634,2,'2001:db8:2::ea',NULL,NULL),(635,2,'2001:db8:2::eb',NULL,NULL),(636,2,'2001:db8:2::ec',NULL,NULL),(637,2,'2001:db8:2::ed',NULL,NULL),(638,2,'2001:db8:2::ee',NULL,NULL),(639,2,'2001:db8:2::ef',NULL,NULL),(640,2,'2001:db8:2::f0',NULL,NULL),(641,2,'2001:db8:2::f1',NULL,NULL),(642,2,'2001:db8:2::f2',NULL,NULL),(643,2,'2001:db8:2::f3',NULL,NULL),(644,2,'2001:db8:2::f4',NULL,NULL),(645,2,'2001:db8:2::f5',NULL,NULL),(646,2,'2001:db8:2::f6',NULL,NULL),(647,2,'2001:db8:2::f7',NULL,NULL),(648,2,'2001:db8:2::f8',NULL,NULL),(649,2,'2001:db8:2::f9',NULL,NULL),(650,2,'2001:db8:2::fa',NULL,NULL),(651,2,'2001:db8:2::fb',NULL,NULL),(652,2,'2001:db8:2::fc',NULL,NULL),(653,2,'2001:db8:2::fd',NULL,NULL),(654,2,'2001:db8:2::fe',NULL,NULL),(655,2,'2001:db8:2::ff',NULL,NULL),(656,2,'2001:db8:2::100',NULL,NULL),(657,2,'2001:db8:2::101',NULL,NULL),(658,2,'2001:db8:2::102',NULL,NULL),(659,2,'2001:db8:2::103',NULL,NULL),(660,2,'2001:db8:2::104',NULL,NULL),(661,2,'2001:db8:2::105',NULL,NULL),(662,2,'2001:db8:2::106',NULL,NULL),(663,2,'2001:db8:2::107',NULL,NULL),(664,2,'2001:db8:2::108',NULL,NULL),(665,2,'2001:db8:2::109',NULL,NULL),(666,2,'2001:db8:2::10a',NULL,NULL),(667,2,'2001:db8:2::10b',NULL,NULL),(668,2,'2001:db8:2::10c',NULL,NULL),(669,2,'2001:db8:2::10d',NULL,NULL),(670,2,'2001:db8:2::10e',NULL,NULL),(671,2,'2001:db8:2::10f',NULL,NULL),(672,2,'2001:db8:2::110',NULL,NULL),(673,2,'2001:db8:2::111',NULL,NULL),(674,2,'2001:db8:2::112',NULL,NULL),(675,2,'2001:db8:2::113',NULL,NULL),(676,2,'2001:db8:2::114',NULL,NULL),(677,2,'2001:db8:2::115',NULL,NULL),(678,2,'2001:db8:2::116',NULL,NULL),(679,2,'2001:db8:2::117',NULL,NULL),(680,2,'2001:db8:2::118',NULL,NULL),(681,2,'2001:db8:2::119',NULL,NULL),(682,2,'2001:db8:2::11a',NULL,NULL),(683,2,'2001:db8:2::11b',NULL,NULL),(684,2,'2001:db8:2::11c',NULL,NULL),(685,2,'2001:db8:2::11d',NULL,NULL),(686,2,'2001:db8:2::11e',NULL,NULL),(687,2,'2001:db8:2::11f',NULL,NULL),(688,2,'2001:db8:2::120',NULL,NULL),(689,2,'2001:db8:2::121',NULL,NULL),(690,2,'2001:db8:2::122',NULL,NULL),(691,2,'2001:db8:2::123',NULL,NULL),(692,2,'2001:db8:2::124',NULL,NULL),(693,2,'2001:db8:2::125',NULL,NULL),(694,2,'2001:db8:2::126',NULL,NULL),(695,2,'2001:db8:2::127',NULL,NULL),(696,2,'2001:db8:2::128',NULL,NULL),(697,2,'2001:db8:2::129',NULL,NULL),(698,2,'2001:db8:2::12a',NULL,NULL),(699,2,'2001:db8:2::12b',NULL,NULL),(700,2,'2001:db8:2::12c',NULL,NULL),(701,2,'2001:db8:2::12d',NULL,NULL),(702,2,'2001:db8:2::12e',NULL,NULL),(703,2,'2001:db8:2::12f',NULL,NULL),(704,2,'2001:db8:2::130',NULL,NULL),(705,2,'2001:db8:2::131',NULL,NULL),(706,2,'2001:db8:2::132',NULL,NULL),(707,2,'2001:db8:2::133',NULL,NULL),(708,2,'2001:db8:2::134',NULL,NULL),(709,2,'2001:db8:2::135',NULL,NULL),(710,2,'2001:db8:2::136',NULL,NULL),(711,2,'2001:db8:2::137',NULL,NULL),(712,2,'2001:db8:2::138',NULL,NULL),(713,2,'2001:db8:2::139',NULL,NULL),(714,2,'2001:db8:2::13a',NULL,NULL),(715,2,'2001:db8:2::13b',NULL,NULL),(716,2,'2001:db8:2::13c',NULL,NULL),(717,2,'2001:db8:2::13d',NULL,NULL),(718,2,'2001:db8:2::13e',NULL,NULL),(719,2,'2001:db8:2::13f',NULL,NULL),(720,2,'2001:db8:2::140',NULL,NULL),(721,2,'2001:db8:2::141',NULL,NULL),(722,2,'2001:db8:2::142',NULL,NULL),(723,2,'2001:db8:2::143',NULL,NULL),(724,2,'2001:db8:2::144',NULL,NULL),(725,2,'2001:db8:2::145',NULL,NULL),(726,2,'2001:db8:2::146',NULL,NULL),(727,2,'2001:db8:2::147',NULL,NULL),(728,2,'2001:db8:2::148',NULL,NULL),(729,2,'2001:db8:2::149',NULL,NULL),(730,2,'2001:db8:2::14a',NULL,NULL),(731,2,'2001:db8:2::14b',NULL,NULL),(732,2,'2001:db8:2::14c',NULL,NULL),(733,2,'2001:db8:2::14d',NULL,NULL),(734,2,'2001:db8:2::14e',NULL,NULL),(735,2,'2001:db8:2::14f',NULL,NULL),(736,2,'2001:db8:2::150',NULL,NULL),(737,2,'2001:db8:2::151',NULL,NULL),(738,2,'2001:db8:2::152',NULL,NULL),(739,2,'2001:db8:2::153',NULL,NULL),(740,2,'2001:db8:2::154',NULL,NULL),(741,2,'2001:db8:2::155',NULL,NULL),(742,2,'2001:db8:2::156',NULL,NULL),(743,2,'2001:db8:2::157',NULL,NULL),(744,2,'2001:db8:2::158',NULL,NULL),(745,2,'2001:db8:2::159',NULL,NULL),(746,2,'2001:db8:2::15a',NULL,NULL),(747,2,'2001:db8:2::15b',NULL,NULL),(748,2,'2001:db8:2::15c',NULL,NULL),(749,2,'2001:db8:2::15d',NULL,NULL),(750,2,'2001:db8:2::15e',NULL,NULL),(751,2,'2001:db8:2::15f',NULL,NULL),(752,2,'2001:db8:2::160',NULL,NULL),(753,2,'2001:db8:2::161',NULL,NULL),(754,2,'2001:db8:2::162',NULL,NULL),(755,2,'2001:db8:2::163',NULL,NULL),(756,2,'2001:db8:2::164',NULL,NULL),(757,2,'2001:db8:2::165',NULL,NULL),(758,2,'2001:db8:2::166',NULL,NULL),(759,2,'2001:db8:2::167',NULL,NULL),(760,2,'2001:db8:2::168',NULL,NULL),(761,2,'2001:db8:2::169',NULL,NULL),(762,2,'2001:db8:2::16a',NULL,NULL),(763,2,'2001:db8:2::16b',NULL,NULL),(764,2,'2001:db8:2::16c',NULL,NULL),(765,2,'2001:db8:2::16d',NULL,NULL),(766,2,'2001:db8:2::16e',NULL,NULL),(767,2,'2001:db8:2::16f',NULL,NULL),(768,2,'2001:db8:2::170',NULL,NULL),(769,2,'2001:db8:2::171',NULL,NULL),(770,2,'2001:db8:2::172',NULL,NULL),(771,2,'2001:db8:2::173',NULL,NULL),(772,2,'2001:db8:2::174',NULL,NULL),(773,2,'2001:db8:2::175',NULL,NULL),(774,2,'2001:db8:2::176',NULL,NULL),(775,2,'2001:db8:2::177',NULL,NULL),(776,2,'2001:db8:2::178',NULL,NULL),(777,2,'2001:db8:2::179',NULL,NULL),(778,2,'2001:db8:2::17a',NULL,NULL),(779,2,'2001:db8:2::17b',NULL,NULL),(780,2,'2001:db8:2::17c',NULL,NULL),(781,2,'2001:db8:2::17d',NULL,NULL),(782,2,'2001:db8:2::17e',NULL,NULL),(783,2,'2001:db8:2::17f',NULL,NULL),(784,2,'2001:db8:2::180',NULL,NULL),(785,2,'2001:db8:2::181',NULL,NULL),(786,2,'2001:db8:2::182',NULL,NULL),(787,2,'2001:db8:2::183',NULL,NULL),(788,2,'2001:db8:2::184',NULL,NULL),(789,2,'2001:db8:2::185',NULL,NULL),(790,2,'2001:db8:2::186',NULL,NULL),(791,2,'2001:db8:2::187',NULL,NULL),(792,2,'2001:db8:2::188',NULL,NULL),(793,2,'2001:db8:2::189',NULL,NULL),(794,2,'2001:db8:2::18a',NULL,NULL),(795,2,'2001:db8:2::18b',NULL,NULL),(796,2,'2001:db8:2::18c',NULL,NULL),(797,2,'2001:db8:2::18d',NULL,NULL),(798,2,'2001:db8:2::18e',NULL,NULL),(799,2,'2001:db8:2::18f',NULL,NULL),(800,2,'2001:db8:2::190',NULL,NULL);
/*!40000 ALTER TABLE `ipv6address` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `irrdb_asn`
--

DROP TABLE IF EXISTS `irrdb_asn`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `irrdb_asn` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `customer_id` int NOT NULL,
  `asn` int unsigned NOT NULL,
  `protocol` int NOT NULL,
  `first_seen` datetime DEFAULT NULL,
  `last_seen` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `custasn` (`asn`,`protocol`,`customer_id`),
  KEY `IDX_87BFC5569395C3F3` (`customer_id`),
  CONSTRAINT `FK_87BFC5569395C3F3` FOREIGN KEY (`customer_id`) REFERENCES `cust` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=105 DEFAULT CHARSET=utf8mb3 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `irrdb_asn`
--

LOCK TABLES `irrdb_asn` WRITE;
/*!40000 ALTER TABLE `irrdb_asn` DISABLE KEYS */;
INSERT INTO `irrdb_asn` VALUES (1,4,112,4,'2014-01-06 14:42:49',NULL,NULL,NULL),(2,4,112,6,'2014-01-06 14:42:50',NULL,NULL,NULL),(3,2,112,4,'2014-01-06 14:42:50',NULL,NULL,NULL),(4,2,1213,4,'2014-01-06 14:42:50',NULL,NULL,NULL),(5,2,1921,4,'2014-01-06 14:42:50',NULL,NULL,NULL),(6,2,2128,4,'2014-01-06 14:42:50',NULL,NULL,NULL),(7,2,2850,4,'2014-01-06 14:42:50',NULL,NULL,NULL),(8,2,42310,4,'2014-01-06 14:42:50',NULL,NULL,NULL),(9,2,112,6,'2014-01-06 14:42:51',NULL,NULL,NULL),(10,2,1213,6,'2014-01-06 14:42:51',NULL,NULL,NULL),(11,2,1921,6,'2014-01-06 14:42:51',NULL,NULL,NULL),(12,2,2128,6,'2014-01-06 14:42:51',NULL,NULL,NULL),(13,2,2850,6,'2014-01-06 14:42:51',NULL,NULL,NULL),(14,2,42310,6,'2014-01-06 14:42:51',NULL,NULL,NULL),(15,5,11521,4,'2014-01-06 14:42:51',NULL,NULL,NULL),(16,5,25441,4,'2014-01-06 14:42:51',NULL,NULL,NULL),(17,5,34317,4,'2014-01-06 14:42:51',NULL,NULL,NULL),(18,5,35272,4,'2014-01-06 14:42:51',NULL,NULL,NULL),(19,5,39064,4,'2014-01-06 14:42:51',NULL,NULL,NULL),(20,5,43178,4,'2014-01-06 14:42:51',NULL,NULL,NULL),(21,5,43610,4,'2014-01-06 14:42:51',NULL,NULL,NULL),(22,5,47615,4,'2014-01-06 14:42:51',NULL,NULL,NULL),(23,5,48342,4,'2014-01-06 14:42:51',NULL,NULL,NULL),(24,5,49573,4,'2014-01-06 14:42:51',NULL,NULL,NULL),(25,5,197853,4,'2014-01-06 14:42:51',NULL,NULL,NULL),(26,5,197904,4,'2014-01-06 14:42:51',NULL,NULL,NULL),(27,5,11521,6,'2014-01-06 14:42:52',NULL,NULL,NULL),(28,5,25441,6,'2014-01-06 14:42:52',NULL,NULL,NULL),(29,5,34317,6,'2014-01-06 14:42:52',NULL,NULL,NULL),(30,5,35272,6,'2014-01-06 14:42:52',NULL,NULL,NULL),(31,5,39064,6,'2014-01-06 14:42:52',NULL,NULL,NULL),(32,5,43178,6,'2014-01-06 14:42:52',NULL,NULL,NULL),(33,5,43610,6,'2014-01-06 14:42:52',NULL,NULL,NULL),(34,5,47615,6,'2014-01-06 14:42:52',NULL,NULL,NULL),(35,5,48342,6,'2014-01-06 14:42:52',NULL,NULL,NULL),(36,5,49573,6,'2014-01-06 14:42:52',NULL,NULL,NULL),(37,5,197853,6,'2014-01-06 14:42:52',NULL,NULL,NULL),(38,5,197904,6,'2014-01-06 14:42:52',NULL,NULL,NULL),(39,3,27,4,'2014-01-06 14:42:52',NULL,NULL,NULL),(40,3,42,4,'2014-01-06 14:42:52',NULL,NULL,NULL),(41,3,187,4,'2014-01-06 14:42:52',NULL,NULL,NULL),(42,3,297,4,'2014-01-06 14:42:52',NULL,NULL,NULL),(43,3,715,4,'2014-01-06 14:42:52',NULL,NULL,NULL),(44,3,3856,4,'2014-01-06 14:42:52',NULL,NULL,NULL),(45,3,7251,4,'2014-01-06 14:42:52',NULL,NULL,NULL),(46,3,13202,4,'2014-01-06 14:42:52',NULL,NULL,NULL),(47,3,16327,4,'2014-01-06 14:42:52',NULL,NULL,NULL),(48,3,16668,4,'2014-01-06 14:42:52',NULL,NULL,NULL),(49,3,16686,4,'2014-01-06 14:42:52',NULL,NULL,NULL),(50,3,20144,4,'2014-01-06 14:42:52',NULL,NULL,NULL),(51,3,20539,4,'2014-01-06 14:42:52',NULL,NULL,NULL),(52,3,21312,4,'2014-01-06 14:42:52',NULL,NULL,NULL),(53,3,24999,4,'2014-01-06 14:42:52',NULL,NULL,NULL),(54,3,27678,4,'2014-01-06 14:42:52',NULL,NULL,NULL),(55,3,32978,4,'2014-01-06 14:42:52',NULL,NULL,NULL),(56,3,32979,4,'2014-01-06 14:42:52',NULL,NULL,NULL),(57,3,35160,4,'2014-01-06 14:42:52',NULL,NULL,NULL),(58,3,38052,4,'2014-01-06 14:42:52',NULL,NULL,NULL),(59,3,44876,4,'2014-01-06 14:42:52',NULL,NULL,NULL),(60,3,45170,4,'2014-01-06 14:42:52',NULL,NULL,NULL),(61,3,45494,4,'2014-01-06 14:42:52',NULL,NULL,NULL),(62,3,48582,4,'2014-01-06 14:42:52',NULL,NULL,NULL),(63,3,48892,4,'2014-01-06 14:42:52',NULL,NULL,NULL),(64,3,50843,4,'2014-01-06 14:42:52',NULL,NULL,NULL),(65,3,51874,4,'2014-01-06 14:42:52',NULL,NULL,NULL),(66,3,52234,4,'2014-01-06 14:42:52',NULL,NULL,NULL),(67,3,52306,4,'2014-01-06 14:42:52',NULL,NULL,NULL),(68,3,54145,4,'2014-01-06 14:42:52',NULL,NULL,NULL),(69,3,59464,4,'2014-01-06 14:42:52',NULL,NULL,NULL),(70,3,60313,4,'2014-01-06 14:42:52',NULL,NULL,NULL),(71,3,197058,4,'2014-01-06 14:42:52',NULL,NULL,NULL),(72,3,27,6,'2014-01-06 14:42:52',NULL,NULL,NULL),(73,3,42,6,'2014-01-06 14:42:52',NULL,NULL,NULL),(74,3,187,6,'2014-01-06 14:42:52',NULL,NULL,NULL),(75,3,297,6,'2014-01-06 14:42:52',NULL,NULL,NULL),(76,3,715,6,'2014-01-06 14:42:52',NULL,NULL,NULL),(77,3,3856,6,'2014-01-06 14:42:52',NULL,NULL,NULL),(78,3,7251,6,'2014-01-06 14:42:52',NULL,NULL,NULL),(79,3,13202,6,'2014-01-06 14:42:52',NULL,NULL,NULL),(80,3,16327,6,'2014-01-06 14:42:52',NULL,NULL,NULL),(81,3,16668,6,'2014-01-06 14:42:52',NULL,NULL,NULL),(82,3,16686,6,'2014-01-06 14:42:52',NULL,NULL,NULL),(83,3,20144,6,'2014-01-06 14:42:52',NULL,NULL,NULL),(84,3,20539,6,'2014-01-06 14:42:52',NULL,NULL,NULL),(85,3,21312,6,'2014-01-06 14:42:52',NULL,NULL,NULL),(86,3,24999,6,'2014-01-06 14:42:52',NULL,NULL,NULL),(87,3,27678,6,'2014-01-06 14:42:52',NULL,NULL,NULL),(88,3,32978,6,'2014-01-06 14:42:52',NULL,NULL,NULL),(89,3,32979,6,'2014-01-06 14:42:52',NULL,NULL,NULL),(90,3,35160,6,'2014-01-06 14:42:52',NULL,NULL,NULL),(91,3,38052,6,'2014-01-06 14:42:52',NULL,NULL,NULL),(92,3,44876,6,'2014-01-06 14:42:52',NULL,NULL,NULL),(93,3,45170,6,'2014-01-06 14:42:52',NULL,NULL,NULL),(94,3,45494,6,'2014-01-06 14:42:52',NULL,NULL,NULL),(95,3,48582,6,'2014-01-06 14:42:52',NULL,NULL,NULL),(96,3,48892,6,'2014-01-06 14:42:52',NULL,NULL,NULL),(97,3,50843,6,'2014-01-06 14:42:52',NULL,NULL,NULL),(98,3,51874,6,'2014-01-06 14:42:52',NULL,NULL,NULL),(99,3,52234,6,'2014-01-06 14:42:52',NULL,NULL,NULL),(100,3,52306,6,'2014-01-06 14:42:52',NULL,NULL,NULL),(101,3,54145,6,'2014-01-06 14:42:52',NULL,NULL,NULL),(102,3,59464,6,'2014-01-06 14:42:52',NULL,NULL,NULL),(103,3,60313,6,'2014-01-06 14:42:52',NULL,NULL,NULL),(104,3,197058,6,'2014-01-06 14:42:52',NULL,NULL,NULL);
/*!40000 ALTER TABLE `irrdb_asn` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `irrdb_prefix`
--

DROP TABLE IF EXISTS `irrdb_prefix`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `irrdb_prefix` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `customer_id` int NOT NULL,
  `prefix` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `protocol` int NOT NULL,
  `first_seen` datetime DEFAULT NULL,
  `last_seen` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `custprefix` (`prefix`,`protocol`,`customer_id`),
  KEY `IDX_FE73E77C9395C3F3` (`customer_id`),
  CONSTRAINT `FK_FE73E77C9395C3F3` FOREIGN KEY (`customer_id`) REFERENCES `cust` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=649 DEFAULT CHARSET=utf8mb3 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `irrdb_prefix`
--

LOCK TABLES `irrdb_prefix` WRITE;
/*!40000 ALTER TABLE `irrdb_prefix` DISABLE KEYS */;
INSERT INTO `irrdb_prefix` VALUES (1,4,'192.175.48.0/24',4,'2014-01-06 14:42:30','2014-01-06 14:42:30',NULL,NULL),(2,2,'4.53.84.128/26',4,'2014-01-06 14:42:31','2014-01-06 14:42:31',NULL,NULL),(3,2,'4.53.146.192/26',4,'2014-01-06 14:42:31','2014-01-06 14:42:31',NULL,NULL),(4,2,'77.72.72.0/21',4,'2014-01-06 14:42:31','2014-01-06 14:42:31',NULL,NULL),(5,2,'87.32.0.0/12',4,'2014-01-06 14:42:31','2014-01-06 14:42:31',NULL,NULL),(6,2,'91.123.224.0/20',4,'2014-01-06 14:42:31','2014-01-06 14:42:31',NULL,NULL),(7,2,'134.226.0.0/16',4,'2014-01-06 14:42:31','2014-01-06 14:42:31',NULL,NULL),(8,2,'136.201.0.0/16',4,'2014-01-06 14:42:31','2014-01-06 14:42:31',NULL,NULL),(9,2,'136.206.0.0/16',4,'2014-01-06 14:42:31','2014-01-06 14:42:31',NULL,NULL),(10,2,'137.43.0.0/16',4,'2014-01-06 14:42:31','2014-01-06 14:42:31',NULL,NULL),(11,2,'140.203.0.0/16',4,'2014-01-06 14:42:31','2014-01-06 14:42:31',NULL,NULL),(12,2,'143.239.0.0/16',4,'2014-01-06 14:42:31','2014-01-06 14:42:31',NULL,NULL),(13,2,'147.252.0.0/16',4,'2014-01-06 14:42:31','2014-01-06 14:42:31',NULL,NULL),(14,2,'149.153.0.0/16',4,'2014-01-06 14:42:31','2014-01-06 14:42:31',NULL,NULL),(15,2,'149.157.0.0/16',4,'2014-01-06 14:42:31','2014-01-06 14:42:31',NULL,NULL),(16,2,'157.190.0.0/16',4,'2014-01-06 14:42:31','2014-01-06 14:42:31',NULL,NULL),(17,2,'160.6.0.0/16',4,'2014-01-06 14:42:31','2014-01-06 14:42:31',NULL,NULL),(18,2,'176.97.158.0/24',4,'2014-01-06 14:42:31','2014-01-06 14:42:31',NULL,NULL),(19,2,'192.174.68.0/24',4,'2014-01-06 14:42:31','2014-01-06 14:42:31',NULL,NULL),(20,2,'192.175.48.0/24',4,'2014-01-06 14:42:31','2014-01-06 14:42:31',NULL,NULL),(21,2,'193.1.0.0/16',4,'2014-01-06 14:42:31','2014-01-06 14:42:31',NULL,NULL),(22,2,'193.242.111.0/24',4,'2014-01-06 14:42:31','2014-01-06 14:42:31',NULL,NULL),(23,2,'194.0.24.0/24',4,'2014-01-06 14:42:31','2014-01-06 14:42:31',NULL,NULL),(24,2,'194.0.25.0/24',4,'2014-01-06 14:42:31','2014-01-06 14:42:31',NULL,NULL),(25,2,'194.0.26.0/24',4,'2014-01-06 14:42:31','2014-01-06 14:42:31',NULL,NULL),(26,2,'194.88.240.0/23',4,'2014-01-06 14:42:31','2014-01-06 14:42:31',NULL,NULL),(27,2,'212.3.242.128/26',4,'2014-01-06 14:42:31','2014-01-06 14:42:31',NULL,NULL),(28,2,'2001:678:20::/48',6,'2014-01-06 14:42:32','2014-01-06 14:42:32',NULL,NULL),(29,2,'2001:678:24::/48',6,'2014-01-06 14:42:32','2014-01-06 14:42:32',NULL,NULL),(30,2,'2001:67c:1bc::/48',6,'2014-01-06 14:42:32','2014-01-06 14:42:32',NULL,NULL),(31,2,'2001:67c:10b8::/48',6,'2014-01-06 14:42:32','2014-01-06 14:42:32',NULL,NULL),(32,2,'2001:67c:10e0::/48',6,'2014-01-06 14:42:32','2014-01-06 14:42:32',NULL,NULL),(33,2,'2001:770::/32',6,'2014-01-06 14:42:32','2014-01-06 14:42:32',NULL,NULL),(34,2,'2001:7f8:18::/48',6,'2014-01-06 14:42:32','2014-01-06 14:42:32',NULL,NULL),(35,2,'2001:1900:2205::/48',6,'2014-01-06 14:42:32','2014-01-06 14:42:32',NULL,NULL),(36,2,'2001:1900:2206::/48',6,'2014-01-06 14:42:32','2014-01-06 14:42:32',NULL,NULL),(37,2,'2620:4f:8000::/48',6,'2014-01-06 14:42:32','2014-01-06 14:42:32',NULL,NULL),(38,2,'2a01:4b0::/32',6,'2014-01-06 14:42:32','2014-01-06 14:42:32',NULL,NULL),(39,5,'31.169.96.0/21',4,'2014-01-06 14:42:33','2014-01-06 14:42:33',NULL,NULL),(40,5,'62.231.32.0/19',4,'2014-01-06 14:42:33','2014-01-06 14:42:33',NULL,NULL),(41,5,'78.135.128.0/17',4,'2014-01-06 14:42:33','2014-01-06 14:42:33',NULL,NULL),(42,5,'83.141.64.0/18',4,'2014-01-06 14:42:33','2014-01-06 14:42:33',NULL,NULL),(43,5,'85.134.128.0/17',4,'2014-01-06 14:42:33','2014-01-06 14:42:33',NULL,NULL),(44,5,'87.192.0.0/16',4,'2014-01-06 14:42:33','2014-01-06 14:42:33',NULL,NULL),(45,5,'87.232.0.0/16',4,'2014-01-06 14:42:33','2014-01-06 14:42:33',NULL,NULL),(46,5,'89.28.176.0/21',4,'2014-01-06 14:42:33','2014-01-06 14:42:33',NULL,NULL),(47,5,'89.124.0.0/14',4,'2014-01-06 14:42:33','2014-01-06 14:42:33',NULL,NULL),(48,5,'89.124.0.0/15',4,'2014-01-06 14:42:33','2014-01-06 14:42:33',NULL,NULL),(49,5,'89.125.0.0/16',4,'2014-01-06 14:42:33','2014-01-06 14:42:33',NULL,NULL),(50,5,'89.126.0.0/16',4,'2014-01-06 14:42:33','2014-01-06 14:42:33',NULL,NULL),(51,5,'89.126.0.0/19',4,'2014-01-06 14:42:33','2014-01-06 14:42:33',NULL,NULL),(52,5,'89.126.0.0/20',4,'2014-01-06 14:42:33','2014-01-06 14:42:33',NULL,NULL),(53,5,'89.126.32.0/19',4,'2014-01-06 14:42:33','2014-01-06 14:42:33',NULL,NULL),(54,5,'89.126.64.0/19',4,'2014-01-06 14:42:33','2014-01-06 14:42:33',NULL,NULL),(55,5,'89.126.96.0/19',4,'2014-01-06 14:42:33','2014-01-06 14:42:33',NULL,NULL),(56,5,'91.194.126.0/23',4,'2014-01-06 14:42:33','2014-01-06 14:42:33',NULL,NULL),(57,5,'91.194.126.0/24',4,'2014-01-06 14:42:33','2014-01-06 14:42:33',NULL,NULL),(58,5,'91.194.127.0/24',4,'2014-01-06 14:42:33','2014-01-06 14:42:33',NULL,NULL),(59,5,'91.209.106.0/24',4,'2014-01-06 14:42:33','2014-01-06 14:42:33',NULL,NULL),(60,5,'91.209.106.0/25',4,'2014-01-06 14:42:33','2014-01-06 14:42:33',NULL,NULL),(61,5,'91.209.106.128/25',4,'2014-01-06 14:42:33','2014-01-06 14:42:33',NULL,NULL),(62,5,'91.213.49.0/24',4,'2014-01-06 14:42:33','2014-01-06 14:42:33',NULL,NULL),(63,5,'91.220.224.0/24',4,'2014-01-06 14:42:33','2014-01-06 14:42:33',NULL,NULL),(64,5,'141.105.112.0/21',4,'2014-01-06 14:42:33','2014-01-06 14:42:33',NULL,NULL),(65,5,'176.52.216.0/21',4,'2014-01-06 14:42:33','2014-01-06 14:42:33',NULL,NULL),(66,5,'195.5.172.0/24',4,'2014-01-06 14:42:33','2014-01-06 14:42:33',NULL,NULL),(67,5,'195.60.166.0/23',4,'2014-01-06 14:42:33','2014-01-06 14:42:33',NULL,NULL),(68,5,'216.245.44.0/24',4,'2014-01-06 14:42:33','2014-01-06 14:42:33',NULL,NULL),(69,5,'2001:67c:20::/64',6,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(70,5,'2001:67c:338::/48',6,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(71,5,'2001:4d68::/32',6,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(72,5,'2a01:268::/32',6,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(73,5,'2a01:8f80::/32',6,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(74,3,'31.135.128.0/19',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(75,3,'31.135.128.0/21',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(76,3,'31.135.136.0/21',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(77,3,'31.135.144.0/22',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(78,3,'31.135.148.0/22',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(79,3,'31.135.152.0/22',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(80,3,'31.135.152.0/23',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(81,3,'31.135.154.0/23',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(82,3,'36.0.4.0/22',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(83,3,'63.246.32.0/20',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(84,3,'64.68.192.0/20',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(85,3,'64.68.192.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(86,3,'64.68.193.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(87,3,'64.68.194.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(88,3,'64.68.195.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(89,3,'64.68.196.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(90,3,'64.78.200.0/21',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(91,3,'64.185.240.0/20',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(92,3,'65.22.4.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(93,3,'65.22.5.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(94,3,'65.22.19.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(95,3,'65.22.23.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(96,3,'65.22.27.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(97,3,'65.22.31.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(98,3,'65.22.35.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(99,3,'65.22.39.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(100,3,'65.22.47.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(101,3,'65.22.51.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(102,3,'65.22.55.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(103,3,'65.22.59.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(104,3,'65.22.63.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(105,3,'65.22.67.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(106,3,'65.22.71.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(107,3,'65.22.79.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(108,3,'65.22.83.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(109,3,'65.22.87.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(110,3,'65.22.91.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(111,3,'65.22.95.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(112,3,'65.22.99.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(113,3,'65.22.103.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(114,3,'65.22.107.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(115,3,'65.22.111.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(116,3,'65.22.115.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(117,3,'65.22.119.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(118,3,'65.22.123.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(119,3,'65.22.127.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(120,3,'65.22.131.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(121,3,'65.22.135.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(122,3,'65.22.139.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(123,3,'65.22.143.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(124,3,'65.22.147.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(125,3,'65.22.151.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(126,3,'65.22.155.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(127,3,'65.22.159.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(128,3,'65.22.163.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(129,3,'65.22.171.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(130,3,'65.22.175.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(131,3,'65.22.179.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(132,3,'65.22.183.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(133,3,'65.22.187.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(134,3,'65.22.191.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(135,3,'65.22.195.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(136,3,'65.22.199.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(137,3,'65.22.203.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(138,3,'65.22.207.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(139,3,'65.22.211.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(140,3,'65.22.215.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(141,3,'65.22.219.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(142,3,'65.22.223.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(143,3,'65.22.227.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(144,3,'65.22.231.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(145,3,'65.22.235.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(146,3,'65.22.239.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(147,3,'65.22.243.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(148,3,'65.22.247.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(149,3,'66.96.112.0/20',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(150,3,'66.102.32.0/20',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(151,3,'66.175.104.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(152,3,'66.185.112.0/20',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(153,3,'66.225.199.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(154,3,'66.225.200.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(155,3,'66.225.201.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(156,3,'67.21.37.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(157,3,'67.22.112.0/21',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(158,3,'67.158.48.0/20',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(159,3,'68.65.112.0/21',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(160,3,'68.65.126.0/23',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(161,3,'68.65.126.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(162,3,'68.65.127.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(163,3,'69.166.10.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(164,3,'69.166.12.0/22',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(165,3,'70.40.0.0/21',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(166,3,'70.40.8.0/21',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(167,3,'72.0.48.0/20',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(168,3,'72.0.48.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(169,3,'72.0.49.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(170,3,'72.0.50.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(171,3,'72.0.51.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(172,3,'72.0.52.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(173,3,'72.0.53.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(174,3,'72.0.54.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(175,3,'72.0.55.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(176,3,'72.0.56.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(177,3,'72.0.57.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(178,3,'72.0.58.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(179,3,'72.0.59.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(180,3,'72.0.60.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(181,3,'72.0.61.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(182,3,'72.0.62.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(183,3,'72.0.63.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(184,3,'72.42.112.0/20',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(185,3,'72.42.112.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(186,3,'72.42.113.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(187,3,'72.42.114.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(188,3,'72.42.115.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(189,3,'72.42.116.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(190,3,'72.42.117.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(191,3,'72.42.118.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(192,3,'72.42.119.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(193,3,'72.42.120.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(194,3,'72.42.121.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(195,3,'72.42.122.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(196,3,'72.42.123.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(197,3,'72.42.124.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(198,3,'72.42.125.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(199,3,'72.42.126.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(200,3,'72.42.127.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(201,3,'74.63.16.0/20',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(202,3,'74.63.16.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(203,3,'74.63.17.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(204,3,'74.63.18.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(205,3,'74.63.19.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(206,3,'74.63.20.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(207,3,'74.63.21.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(208,3,'74.63.22.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(209,3,'74.63.23.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(210,3,'74.63.24.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(211,3,'74.63.25.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(212,3,'74.63.26.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(213,3,'74.63.27.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(214,3,'74.80.64.0/18',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(215,3,'74.80.64.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(216,3,'74.80.65.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(217,3,'74.80.66.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(218,3,'74.80.67.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(219,3,'74.80.68.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(220,3,'74.80.69.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(221,3,'74.80.70.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(222,3,'74.80.71.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(223,3,'74.80.72.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(224,3,'74.80.73.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(225,3,'74.80.74.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(226,3,'74.80.75.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(227,3,'74.80.76.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(228,3,'74.80.77.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(229,3,'74.80.78.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(230,3,'74.80.79.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(231,3,'74.80.80.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(232,3,'74.80.81.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(233,3,'74.80.82.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(234,3,'74.80.83.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(235,3,'74.80.84.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(236,3,'74.80.85.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(237,3,'74.80.86.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(238,3,'74.80.87.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(239,3,'74.80.88.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(240,3,'74.80.89.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(241,3,'74.80.90.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(242,3,'74.80.91.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(243,3,'74.80.92.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(244,3,'74.80.93.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(245,3,'74.80.94.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(246,3,'74.80.95.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(247,3,'74.80.96.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(248,3,'74.80.97.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(249,3,'74.80.98.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(250,3,'74.80.99.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(251,3,'74.80.100.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(252,3,'74.80.101.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(253,3,'74.80.102.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(254,3,'74.80.103.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(255,3,'74.80.104.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(256,3,'74.80.105.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(257,3,'74.80.106.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(258,3,'74.80.107.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(259,3,'74.80.108.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(260,3,'74.80.109.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(261,3,'74.80.110.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(262,3,'74.80.111.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(263,3,'74.80.112.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(264,3,'74.80.113.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(265,3,'74.80.114.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(266,3,'74.80.115.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(267,3,'74.80.116.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(268,3,'74.80.117.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(269,3,'74.80.118.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(270,3,'74.80.119.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(271,3,'74.80.120.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(272,3,'74.80.121.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(273,3,'74.80.122.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(274,3,'74.80.123.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(275,3,'74.80.124.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(276,3,'74.80.125.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(277,3,'74.80.126.0/23',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(278,3,'74.80.126.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(279,3,'74.80.127.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(280,3,'74.118.212.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(281,3,'74.118.213.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(282,3,'74.118.214.0/23',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(283,3,'75.127.16.0/20',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(284,3,'76.191.16.0/20',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(285,3,'89.19.120.0/21',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(286,3,'89.19.120.0/22',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(287,3,'89.19.124.0/23',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(288,3,'89.19.126.0/23',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(289,3,'91.201.224.0/22',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(290,3,'91.201.224.0/23',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(291,3,'91.201.224.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(292,3,'91.201.225.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(293,3,'91.201.226.0/23',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(294,3,'91.201.226.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(295,3,'91.201.227.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(296,3,'91.209.1.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(297,3,'91.209.193.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(298,3,'91.222.16.0/22',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(299,3,'91.222.40.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(300,3,'91.222.41.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(301,3,'91.222.42.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(302,3,'91.222.43.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(303,3,'91.241.93.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(304,3,'93.95.24.0/21',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(305,3,'93.95.24.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(306,3,'93.95.25.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(307,3,'93.95.26.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(308,3,'93.171.128.0/22',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(309,3,'95.47.163.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(310,3,'101.251.4.0/22',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(311,3,'114.69.222.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(312,3,'128.8.0.0/16',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(313,3,'128.161.0.0/16',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(314,3,'129.2.0.0/16',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(315,3,'130.135.0.0/16',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(316,3,'130.167.0.0/16',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(317,3,'131.161.128.0/18',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(318,3,'131.182.0.0/16',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(319,3,'139.229.0.0/16',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(320,3,'140.169.0.0/16',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(321,3,'146.5.0.0/16',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(322,3,'146.58.0.0/16',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(323,3,'150.144.0.0/16',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(324,3,'156.154.43.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(325,3,'156.154.50.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(326,3,'156.154.59.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(327,3,'156.154.96.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(328,3,'156.154.99.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(329,3,'158.154.0.0/16',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(330,3,'169.222.0.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(331,3,'183.91.132.0/22',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(332,3,'192.5.41.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(333,3,'192.12.123.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(334,3,'192.42.70.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(335,3,'192.58.36.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(336,3,'192.67.83.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(337,3,'192.67.107.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(338,3,'192.67.108.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(339,3,'192.68.52.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(340,3,'192.68.148.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(341,3,'192.68.162.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(342,3,'192.70.244.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(343,3,'192.70.249.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(344,3,'192.77.80.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(345,3,'192.84.8.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(346,3,'192.88.124.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(347,3,'192.92.65.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(348,3,'192.92.90.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(349,3,'192.100.9.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(350,3,'192.100.10.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(351,3,'192.100.15.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(352,3,'192.101.148.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(353,3,'192.102.15.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(354,3,'192.102.219.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(355,3,'192.102.233.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(356,3,'192.102.234.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(357,3,'192.112.18.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(358,3,'192.112.223.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(359,3,'192.112.224.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(360,3,'192.124.20.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(361,3,'192.138.101.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(362,3,'192.138.172.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(363,3,'192.149.89.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(364,3,'192.149.104.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(365,3,'192.149.107.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(366,3,'192.149.133.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(367,3,'192.150.32.0/21',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(368,3,'192.153.157.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(369,3,'192.188.4.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(370,3,'192.203.230.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(371,3,'192.225.64.0/19',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(372,3,'192.243.0.0/20',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(373,3,'192.243.16.0/21',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(374,3,'193.29.206.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(375,3,'193.110.16.0/22',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(376,3,'193.110.16.0/23',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(377,3,'193.110.18.0/23',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(378,3,'193.111.240.0/22',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(379,3,'193.178.228.0/23',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(380,3,'193.178.228.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(381,3,'193.178.229.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(382,3,'194.0.12.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(383,3,'194.0.13.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(384,3,'194.0.14.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(385,3,'194.0.17.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(386,3,'194.0.27.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(387,3,'194.0.36.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(388,3,'194.0.42.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(389,3,'194.0.47.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(390,3,'194.28.144.0/22',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(391,3,'194.117.58.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(392,3,'194.117.60.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(393,3,'194.117.61.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(394,3,'194.117.62.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(395,3,'194.117.63.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(396,3,'194.146.180.0/22',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(397,3,'194.146.180.0/23',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(398,3,'194.146.180.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(399,3,'194.146.181.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(400,3,'194.146.182.0/23',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(401,3,'194.146.182.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(402,3,'194.146.183.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(403,3,'194.146.228.0/22',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(404,3,'194.146.228.0/23',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(405,3,'194.146.228.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(406,3,'194.146.229.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(407,3,'194.146.230.0/23',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(408,3,'194.146.230.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(409,3,'194.146.231.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(410,3,'194.153.148.0/23',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(411,3,'195.64.162.0/23',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(412,3,'195.64.162.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(413,3,'195.64.163.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(414,3,'195.82.138.0/23',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(415,3,'198.9.0.0/16',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(416,3,'198.49.1.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(417,3,'198.116.0.0/14',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(418,3,'198.120.0.0/14',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(419,3,'198.182.28.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(420,3,'198.182.31.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(421,3,'198.182.167.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(422,3,'199.4.137.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(423,3,'199.7.64.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(424,3,'199.7.77.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(425,3,'199.7.83.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(426,3,'199.7.86.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(427,3,'199.7.91.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(428,3,'199.7.94.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(429,3,'199.7.95.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(430,3,'199.43.132.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(431,3,'199.115.156.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(432,3,'199.115.157.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(433,3,'199.120.141.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(434,3,'199.120.142.0/23',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(435,3,'199.120.144.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(436,3,'199.182.32.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(437,3,'199.182.40.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(438,3,'199.184.181.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(439,3,'199.184.182.0/23',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(440,3,'199.184.184.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(441,3,'199.249.112.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(442,3,'199.249.113.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(443,3,'199.249.114.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(444,3,'199.249.115.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(445,3,'199.249.116.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(446,3,'199.249.117.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(447,3,'199.249.118.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(448,3,'199.249.119.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(449,3,'199.249.120.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(450,3,'199.249.121.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(451,3,'199.249.122.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(452,3,'199.249.123.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(453,3,'199.249.124.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(454,3,'199.249.125.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(455,3,'199.249.126.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(456,3,'199.249.127.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(457,3,'199.254.171.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(458,3,'200.1.121.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(459,3,'200.1.131.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(460,3,'200.7.4.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(461,3,'200.16.98.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(462,3,'202.6.102.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(463,3,'202.7.4.0/22',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(464,3,'202.52.0.0/23',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(465,3,'202.53.186.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(466,3,'202.53.191.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(467,3,'203.119.88.0/23',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(468,3,'204.14.112.0/21',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(469,3,'204.19.119.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(470,3,'204.26.57.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(471,3,'204.61.208.0/21',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(472,3,'204.61.208.0/22',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(473,3,'204.61.208.0/23',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(474,3,'204.61.210.0/23',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(475,3,'204.61.210.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(476,3,'204.61.212.0/23',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(477,3,'204.61.216.0/23',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(478,3,'204.194.22.0/23',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(479,3,'204.194.22.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(480,3,'204.194.23.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(481,3,'205.132.46.0/23',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(482,3,'205.207.155.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(483,3,'206.51.254.0/23',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(484,3,'206.108.113.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(485,3,'206.196.160.0/19',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(486,3,'206.220.228.0/22',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(487,3,'206.220.228.0/23',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(488,3,'206.220.230.0/23',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(489,3,'206.223.122.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(490,3,'207.34.5.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(491,3,'207.34.6.0/23',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(492,3,'208.15.19.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(493,3,'208.49.115.64/27',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(494,3,'208.67.88.0/22',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(495,3,'216.21.2.0/23',4,'2014-01-06 14:42:34','2014-01-06 14:42:34',NULL,NULL),(496,3,'2001:500:3::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36',NULL,NULL),(497,3,'2001:500:14::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36',NULL,NULL),(498,3,'2001:500:15::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36',NULL,NULL),(499,3,'2001:500:40::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36',NULL,NULL),(500,3,'2001:500:41::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36',NULL,NULL),(501,3,'2001:500:42::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36',NULL,NULL),(502,3,'2001:500:43::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36',NULL,NULL),(503,3,'2001:500:44::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36',NULL,NULL),(504,3,'2001:500:45::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36',NULL,NULL),(505,3,'2001:500:46::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36',NULL,NULL),(506,3,'2001:500:47::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36',NULL,NULL),(507,3,'2001:500:48::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36',NULL,NULL),(508,3,'2001:500:49::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36',NULL,NULL),(509,3,'2001:500:4a::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36',NULL,NULL),(510,3,'2001:500:4b::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36',NULL,NULL),(511,3,'2001:500:4c::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36',NULL,NULL),(512,3,'2001:500:4d::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36',NULL,NULL),(513,3,'2001:500:4e::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36',NULL,NULL),(514,3,'2001:500:4f::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36',NULL,NULL),(515,3,'2001:500:50::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36',NULL,NULL),(516,3,'2001:500:51::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36',NULL,NULL),(517,3,'2001:500:52::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36',NULL,NULL),(518,3,'2001:500:53::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36',NULL,NULL),(519,3,'2001:500:54::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36',NULL,NULL),(520,3,'2001:500:55::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36',NULL,NULL),(521,3,'2001:500:56::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36',NULL,NULL),(522,3,'2001:500:7d::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36',NULL,NULL),(523,3,'2001:500:83::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36',NULL,NULL),(524,3,'2001:500:8c::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36',NULL,NULL),(525,3,'2001:500:9c::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36',NULL,NULL),(526,3,'2001:500:9d::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36',NULL,NULL),(527,3,'2001:500:a4::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36',NULL,NULL),(528,3,'2001:500:a5::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36',NULL,NULL),(529,3,'2001:500:e0::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36',NULL,NULL),(530,3,'2001:500:e1::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36',NULL,NULL),(531,3,'2001:678:3::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36',NULL,NULL),(532,3,'2001:678:28::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36',NULL,NULL),(533,3,'2001:678:4c::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36',NULL,NULL),(534,3,'2001:678:60::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36',NULL,NULL),(535,3,'2001:678:78::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36',NULL,NULL),(536,3,'2001:678:94::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36',NULL,NULL),(537,3,'2001:dd8:7::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36',NULL,NULL),(538,3,'2001:1398:121::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36',NULL,NULL),(539,3,'2404:2c00::/32',6,'2014-01-06 14:42:36','2014-01-06 14:42:36',NULL,NULL),(540,3,'2620:0:870::/45',6,'2014-01-06 14:42:36','2014-01-06 14:42:36',NULL,NULL),(541,3,'2620:0:876::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36',NULL,NULL),(542,3,'2620:49::/44',6,'2014-01-06 14:42:36','2014-01-06 14:42:36',NULL,NULL),(543,3,'2620:49::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36',NULL,NULL),(544,3,'2620:49:a::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36',NULL,NULL),(545,3,'2620:49:b::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36',NULL,NULL),(546,3,'2620:95:8000::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36',NULL,NULL),(547,3,'2620:171::/40',6,'2014-01-06 14:42:36','2014-01-06 14:42:36',NULL,NULL),(548,3,'2620:171:f0::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36',NULL,NULL),(549,3,'2620:171:f1::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36',NULL,NULL),(550,3,'2620:171:f2::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36',NULL,NULL),(551,3,'2620:171:f3::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36',NULL,NULL),(552,3,'2620:171:f4::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36',NULL,NULL),(553,3,'2620:171:f5::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36',NULL,NULL),(554,3,'2620:171:f6::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36',NULL,NULL),(555,3,'2620:171:f7::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36',NULL,NULL),(556,3,'2620:171:f8::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36',NULL,NULL),(557,3,'2620:171:f9::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36',NULL,NULL),(558,3,'2620:171:a00::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36',NULL,NULL),(559,3,'2620:171:a01::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36',NULL,NULL),(560,3,'2620:171:a02::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36',NULL,NULL),(561,3,'2620:171:a03::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36',NULL,NULL),(562,3,'2620:171:a04::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36',NULL,NULL),(563,3,'2620:171:a05::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36',NULL,NULL),(564,3,'2620:171:a06::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36',NULL,NULL),(565,3,'2620:171:a07::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36',NULL,NULL),(566,3,'2620:171:a08::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36',NULL,NULL),(567,3,'2620:171:a09::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36',NULL,NULL),(568,3,'2620:171:a0a::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36',NULL,NULL),(569,3,'2620:171:a0b::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36',NULL,NULL),(570,3,'2620:171:a0c::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36',NULL,NULL),(571,3,'2620:171:a0d::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36',NULL,NULL),(572,3,'2620:171:a0e::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36',NULL,NULL),(573,3,'2620:171:a0f::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36',NULL,NULL),(574,3,'2620:171:ad0::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36',NULL,NULL),(575,3,'2620:171:d00::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36',NULL,NULL),(576,3,'2620:171:d01::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36',NULL,NULL),(577,3,'2620:171:d02::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36',NULL,NULL),(578,3,'2620:171:d03::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36',NULL,NULL),(579,3,'2620:171:d04::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36',NULL,NULL),(580,3,'2620:171:d05::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36',NULL,NULL),(581,3,'2620:171:d06::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36',NULL,NULL),(582,3,'2620:171:d07::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36',NULL,NULL),(583,3,'2620:171:d08::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36',NULL,NULL),(584,3,'2620:171:d09::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36',NULL,NULL),(585,3,'2620:171:d0a::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36',NULL,NULL),(586,3,'2620:171:d0b::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36',NULL,NULL),(587,3,'2620:171:d0c::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36',NULL,NULL),(588,3,'2620:171:d0d::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36',NULL,NULL),(589,3,'2620:171:d0e::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36',NULL,NULL),(590,3,'2620:171:d0f::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36',NULL,NULL),(591,3,'2620:171:dd0::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36',NULL,NULL),(592,3,'2a01:8840:4::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36',NULL,NULL),(593,3,'2a01:8840:5::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36',NULL,NULL),(594,3,'2a01:8840:15::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36',NULL,NULL),(595,3,'2a01:8840:19::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36',NULL,NULL),(596,3,'2a01:8840:1d::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36',NULL,NULL),(597,3,'2a01:8840:21::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36',NULL,NULL),(598,3,'2a01:8840:25::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36',NULL,NULL),(599,3,'2a01:8840:29::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36',NULL,NULL),(600,3,'2a01:8840:2d::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36',NULL,NULL),(601,3,'2a01:8840:31::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36',NULL,NULL),(602,3,'2a01:8840:35::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36',NULL,NULL),(603,3,'2a01:8840:39::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36',NULL,NULL),(604,3,'2a01:8840:3d::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36',NULL,NULL),(605,3,'2a01:8840:41::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36',NULL,NULL),(606,3,'2a01:8840:45::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36',NULL,NULL),(607,3,'2a01:8840:4d::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36',NULL,NULL),(608,3,'2a01:8840:51::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36',NULL,NULL),(609,3,'2a01:8840:55::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36',NULL,NULL),(610,3,'2a01:8840:59::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36',NULL,NULL),(611,3,'2a01:8840:5d::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36',NULL,NULL),(612,3,'2a01:8840:61::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36',NULL,NULL),(613,3,'2a01:8840:65::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36',NULL,NULL),(614,3,'2a01:8840:69::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36',NULL,NULL),(615,3,'2a01:8840:6d::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36',NULL,NULL),(616,3,'2a01:8840:71::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36',NULL,NULL),(617,3,'2a01:8840:75::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36',NULL,NULL),(618,3,'2a01:8840:79::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36',NULL,NULL),(619,3,'2a01:8840:7d::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36',NULL,NULL),(620,3,'2a01:8840:81::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36',NULL,NULL),(621,3,'2a01:8840:85::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36',NULL,NULL),(622,3,'2a01:8840:89::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36',NULL,NULL),(623,3,'2a01:8840:8d::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36',NULL,NULL),(624,3,'2a01:8840:91::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36',NULL,NULL),(625,3,'2a01:8840:95::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36',NULL,NULL),(626,3,'2a01:8840:99::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36',NULL,NULL),(627,3,'2a01:8840:9d::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36',NULL,NULL),(628,3,'2a01:8840:a1::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36',NULL,NULL),(629,3,'2a01:8840:a5::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36',NULL,NULL),(630,3,'2a01:8840:a9::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36',NULL,NULL),(631,3,'2a01:8840:ad::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36',NULL,NULL),(632,3,'2a01:8840:b1::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36',NULL,NULL),(633,3,'2a01:8840:b5::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36',NULL,NULL),(634,3,'2a01:8840:b9::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36',NULL,NULL),(635,3,'2a01:8840:bd::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36',NULL,NULL),(636,3,'2a01:8840:c1::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36',NULL,NULL),(637,3,'2a01:8840:c5::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36',NULL,NULL),(638,3,'2a01:8840:c9::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36',NULL,NULL),(639,3,'2a01:8840:cd::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36',NULL,NULL),(640,3,'2a01:8840:d1::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36',NULL,NULL),(641,3,'2a01:8840:d5::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36',NULL,NULL),(642,3,'2a01:8840:d9::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36',NULL,NULL),(643,3,'2a01:8840:dd::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36',NULL,NULL),(644,3,'2a01:8840:e1::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36',NULL,NULL),(645,3,'2a01:8840:e5::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36',NULL,NULL),(646,3,'2a01:8840:e9::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36',NULL,NULL),(647,3,'2a01:8840:ed::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36',NULL,NULL),(648,3,'2a01:8840:f1::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36',NULL,NULL);
/*!40000 ALTER TABLE `irrdb_prefix` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `irrdbconfig`
--

DROP TABLE IF EXISTS `irrdbconfig`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `irrdbconfig` (
  `id` int NOT NULL AUTO_INCREMENT,
  `host` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `source` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `notes` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb3 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `irrdbconfig`
--

LOCK TABLES `irrdbconfig` WRITE;
/*!40000 ALTER TABLE `irrdbconfig` DISABLE KEYS */;
INSERT INTO `irrdbconfig` VALUES (1,'whois.radb.net','RIPE','RIPE Query from RIPE Database',NULL,NULL),(2,'whois.radb.net','RADB','RADB Query from RADB Database',NULL,NULL),(3,'whois.radb.net','LACNIC','LACNIC Query from LACNIC Database',NULL,NULL),(4,'whois.radb.net','AFRINIC','AFRINIC Query from AFRINIC Database',NULL,NULL),(5,'whois.radb.net','APNIC','APNIC Query from APNIC Database',NULL,NULL),(6,'whois.radb.net','LEVEL3','Level3 Query from Level3 Database',NULL,NULL),(7,'whois.radb.net','ARIN','ARIN Query from RADB Database',NULL,NULL),(8,'whois.radb.net','RADB,ARIN','RADB+ARIN Query from RADB Database',NULL,NULL),(9,'whois.radb.net','ALTDB','ALTDB Query from RADB Database',NULL,NULL),(10,'whois.radb.net','RADB,RIPE','RADB+RIPE Query from RADB Database',NULL,NULL),(11,'whois.radb.net','RADB,APNIC,ARIN','RADB+APNIC+ARIN Query from RADB Database',NULL,NULL),(12,'whois.radb.net','RIPE,ARIN','RIPE+ARIN Query from RADB Database',NULL,NULL),(13,'whois.radb.net','RADB,RIPE,APNIC,ARIN','',NULL,NULL);
/*!40000 ALTER TABLE `irrdbconfig` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `l2address`
--

DROP TABLE IF EXISTS `l2address`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `l2address` (
  `id` int NOT NULL AUTO_INCREMENT,
  `vlan_interface_id` int NOT NULL,
  `mac` varchar(12) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `firstseen` datetime DEFAULT NULL,
  `lastseen` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mac_vlanint` (`mac`,`vlan_interface_id`),
  KEY `IDX_B9482E1D6AB5F82` (`vlan_interface_id`),
  CONSTRAINT `FK_B9482E1D6AB5F82` FOREIGN KEY (`vlan_interface_id`) REFERENCES `vlaninterface` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `l2address`
--

LOCK TABLES `l2address` WRITE;
/*!40000 ALTER TABLE `l2address` DISABLE KEYS */;
/*!40000 ALTER TABLE `l2address` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `location`
--

DROP TABLE IF EXISTS `location`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `location` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `shortname` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `tag` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `address` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `nocphone` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `nocfax` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `nocemail` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `officephone` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `officefax` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `officeemail` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `notes` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `pdb_facility_id` bigint DEFAULT NULL,
  `city` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `country` varchar(2) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_5E9E89CB64082763` (`shortname`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb3 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `location`
--

LOCK TABLES `location` WRITE;
/*!40000 ALTER TABLE `location` DISABLE KEYS */;
INSERT INTO `location` VALUES (1,'Location 1','l1',NULL,'','','','','','','','',NULL,NULL,NULL,NULL,NULL);
/*!40000 ALTER TABLE `location` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `log`
--

DROP TABLE IF EXISTS `log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `log` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `model` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint unsigned DEFAULT NULL,
  `action` varchar(7) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `message` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `models` json NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `log_user_id_foreign` (`user_id`),
  KEY `log_action_index` (`action`),
  KEY `log_model_model_id_index` (`model`,`model_id`),
  CONSTRAINT `log_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `log`
--

LOCK TABLES `log` WRITE;
/*!40000 ALTER TABLE `log` DISABLE KEYS */;
INSERT INTO `log` VALUES (1,1,'DocstoreFile',1,'CREATED','Docstore File [id:1] \'test.txt\'','{\"new\": {\"id\": 1, \"name\": \"test.txt\", \"path\": \"BaLvISQV7Cn48p8LPaOoPSyJ5hzaKC7rHlqMu5Hd.txt\", \"sha256\": \"64cdd02f0ef14bf6b8e0a51915396a002afed410459935b1209ba2d654842f10\", \"min_privs\": \"0\", \"created_at\": \"2021-05-28 14:03:38\", \"created_by\": 1, \"updated_at\": \"2021-05-28 14:03:38\", \"description\": null, \"file_last_updated\": \"2021-05-28 14:03:38\", \"docstore_directory_id\": \"1\"}, \"old\": null, \"changed\": null}','2021-05-28 12:03:38','2021-05-28 12:03:38');
/*!40000 ALTER TABLE `log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `logos`
--

DROP TABLE IF EXISTS `logos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `logos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `customer_id` int DEFAULT NULL,
  `original_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `stored_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `uploaded_by` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `width` int NOT NULL,
  `height` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_9F54004F9395C3F3` (`customer_id`),
  CONSTRAINT `FK_9F54004F9395C3F3` FOREIGN KEY (`customer_id`) REFERENCES `cust` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `logos`
--

LOCK TABLES `logos` WRITE;
/*!40000 ALTER TABLE `logos` DISABLE KEYS */;
/*!40000 ALTER TABLE `logos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `macaddress`
--

DROP TABLE IF EXISTS `macaddress`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `macaddress` (
  `id` int NOT NULL AUTO_INCREMENT,
  `virtualinterfaceid` int DEFAULT NULL,
  `firstseen` datetime DEFAULT NULL,
  `lastseen` datetime DEFAULT NULL,
  `mac` varchar(12) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_42CD65F6BFDF15D5` (`virtualinterfaceid`),
  CONSTRAINT `FK_42CD65F6BFDF15D5` FOREIGN KEY (`virtualinterfaceid`) REFERENCES `virtualinterface` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `macaddress`
--

LOCK TABLES `macaddress` WRITE;
/*!40000 ALTER TABLE `macaddress` DISABLE KEYS */;
/*!40000 ALTER TABLE `macaddress` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'2014_10_12_100000_create_password_resets_table',1),(2,'2018_08_08_100000_create_telescope_entries_table',1),(3,'2019_03_25_211956_create_failed_jobs_table',1),(4,'2020_02_06_204556_create_docstore_directories',2),(5,'2020_02_06_204608_create_docstore_files',2),(6,'2020_02_06_204911_create_docstore_logs',2),(7,'2020_03_09_110945_create_docstore_customer_directories',3),(8,'2020_03_09_111505_create_docstore_customer_files',3),(9,'2020_07_21_094354_create_route_server_filters',4),(12,'2020_09_03_153723_add_timestamps',5),(13,'2020_09_18_095136_delete_ixp_table',6),(14,'2020_11_16_102415_database_fixes',7),(15,'2021_03_12_150418_create_log_table',8),(16,'2021_04_14_125742_user_pref',9),(17,'2021_04_14_101948_update_timestamps',10),(18,'2021_05_18_085721_add_note_infrastructure',11),(19,'2021_05_18_114206_update_pp_prefix_size',12),(20,'2020_06_01_143931_database_schema_at_end_v5',13),(21,'2021_03_30_124916_create_atlas_probes',13),(22,'2021_03_30_125238_create_atlas_runs',13),(23,'2021_03_30_125422_create_atlas_measurements',13),(24,'2021_03_30_125723_create_atlas_results',13),(25,'2021_06_11_141137_update_db_doctrine2eloquent',13),(26,'2021_07_20_134716_fix_last_updated_and_timestamps',13),(27,'2021_09_16_195333_add_rate_limit_col_to_physint',13),(28,'2021_09_17_144421_modernise_irrdb_conf_table',13);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `netinfo`
--

DROP TABLE IF EXISTS `netinfo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `netinfo` (
  `id` int NOT NULL AUTO_INCREMENT,
  `vlan_id` int NOT NULL,
  `protocol` int NOT NULL,
  `property` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `ix` int NOT NULL DEFAULT '0',
  `value` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_F843DE6B8B4937A1` (`vlan_id`),
  KEY `VlanProtoProp` (`protocol`,`property`,`vlan_id`),
  CONSTRAINT `FK_F843DE6B8B4937A1` FOREIGN KEY (`vlan_id`) REFERENCES `vlan` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `netinfo`
--

LOCK TABLES `netinfo` WRITE;
/*!40000 ALTER TABLE `netinfo` DISABLE KEYS */;
/*!40000 ALTER TABLE `netinfo` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `networkinfo`
--

DROP TABLE IF EXISTS `networkinfo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `networkinfo` (
  `id` int NOT NULL AUTO_INCREMENT,
  `vlanid` int DEFAULT NULL,
  `protocol` int DEFAULT NULL,
  `network` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `masklen` int DEFAULT NULL,
  `rs1address` varchar(40) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `rs2address` varchar(40) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `dnsfile` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_6A0AF167F48D6D0` (`vlanid`),
  CONSTRAINT `FK_6A0AF167F48D6D0` FOREIGN KEY (`vlanid`) REFERENCES `vlan` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `networkinfo`
--

LOCK TABLES `networkinfo` WRITE;
/*!40000 ALTER TABLE `networkinfo` DISABLE KEYS */;
/*!40000 ALTER TABLE `networkinfo` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `oui`
--

DROP TABLE IF EXISTS `oui`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `oui` (
  `id` int NOT NULL AUTO_INCREMENT,
  `oui` varchar(6) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `organisation` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_DAEC0140DAEC0140` (`oui`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `oui`
--

LOCK TABLES `oui` WRITE;
/*!40000 ALTER TABLE `oui` DISABLE KEYS */;
/*!40000 ALTER TABLE `oui` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_resets`
--

DROP TABLE IF EXISTS `password_resets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_resets` (
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  KEY `password_resets_email_index` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_resets`
--

LOCK TABLES `password_resets` WRITE;
/*!40000 ALTER TABLE `password_resets` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_resets` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `patch_panel`
--

DROP TABLE IF EXISTS `patch_panel`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `patch_panel` (
  `id` int NOT NULL AUTO_INCREMENT,
  `cabinet_id` int DEFAULT NULL,
  `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `colo_reference` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `cable_type` int NOT NULL,
  `connector_type` int NOT NULL,
  `installation_date` datetime DEFAULT NULL,
  `port_prefix` varchar(20) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `chargeable` int NOT NULL DEFAULT '0',
  `location_notes` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `u_position` int DEFAULT NULL,
  `mounted_at` smallint DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_79A52562D351EC` (`cabinet_id`),
  CONSTRAINT `FK_79A52562D351EC` FOREIGN KEY (`cabinet_id`) REFERENCES `cabinet` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `patch_panel`
--

LOCK TABLES `patch_panel` WRITE;
/*!40000 ALTER TABLE `patch_panel` DISABLE KEYS */;
/*!40000 ALTER TABLE `patch_panel` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `patch_panel_port`
--

DROP TABLE IF EXISTS `patch_panel_port`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `patch_panel_port` (
  `id` int NOT NULL AUTO_INCREMENT,
  `switch_port_id` int DEFAULT NULL,
  `patch_panel_id` int DEFAULT NULL,
  `customer_id` int DEFAULT NULL,
  `state` int NOT NULL,
  `notes` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `assigned_at` date DEFAULT NULL,
  `connected_at` date DEFAULT NULL,
  `cease_requested_at` date DEFAULT NULL,
  `ceased_at` date DEFAULT NULL,
  `last_state_change` date DEFAULT NULL,
  `internal_use` tinyint(1) NOT NULL DEFAULT '0',
  `chargeable` int NOT NULL DEFAULT '2',
  `duplex_master_id` int DEFAULT NULL,
  `number` smallint NOT NULL,
  `colo_circuit_ref` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `ticket_ref` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `private_notes` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `owned_by` int NOT NULL DEFAULT '0',
  `loa_code` varchar(25) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `description` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `colo_billing_ref` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_4BE40BC2C1DA6A2A` (`switch_port_id`),
  KEY `IDX_4BE40BC2635D5D87` (`patch_panel_id`),
  KEY `IDX_4BE40BC29395C3F3` (`customer_id`),
  KEY `IDX_4BE40BC23838446` (`duplex_master_id`),
  CONSTRAINT `FK_4BE40BC23838446` FOREIGN KEY (`duplex_master_id`) REFERENCES `patch_panel_port` (`id`),
  CONSTRAINT `FK_4BE40BC2635D5D87` FOREIGN KEY (`patch_panel_id`) REFERENCES `patch_panel` (`id`),
  CONSTRAINT `FK_4BE40BC29395C3F3` FOREIGN KEY (`customer_id`) REFERENCES `cust` (`id`),
  CONSTRAINT `FK_4BE40BC2C1DA6A2A` FOREIGN KEY (`switch_port_id`) REFERENCES `switchport` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `patch_panel_port`
--

LOCK TABLES `patch_panel_port` WRITE;
/*!40000 ALTER TABLE `patch_panel_port` DISABLE KEYS */;
/*!40000 ALTER TABLE `patch_panel_port` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `patch_panel_port_file`
--

DROP TABLE IF EXISTS `patch_panel_port_file`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `patch_panel_port_file` (
  `id` int NOT NULL AUTO_INCREMENT,
  `patch_panel_port_id` int DEFAULT NULL,
  `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `type` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `uploaded_at` datetime NOT NULL,
  `uploaded_by` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `size` int NOT NULL,
  `is_private` tinyint(1) NOT NULL DEFAULT '0',
  `storage_location` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_28089403B0F978FF` (`patch_panel_port_id`),
  CONSTRAINT `FK_28089403B0F978FF` FOREIGN KEY (`patch_panel_port_id`) REFERENCES `patch_panel_port` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `patch_panel_port_file`
--

LOCK TABLES `patch_panel_port_file` WRITE;
/*!40000 ALTER TABLE `patch_panel_port_file` DISABLE KEYS */;
/*!40000 ALTER TABLE `patch_panel_port_file` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `patch_panel_port_history`
--

DROP TABLE IF EXISTS `patch_panel_port_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `patch_panel_port_history` (
  `id` int NOT NULL AUTO_INCREMENT,
  `patch_panel_port_id` int DEFAULT NULL,
  `state` int NOT NULL,
  `notes` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `assigned_at` date DEFAULT NULL,
  `connected_at` date DEFAULT NULL,
  `cease_requested_at` date DEFAULT NULL,
  `ceased_at` date DEFAULT NULL,
  `internal_use` tinyint(1) NOT NULL DEFAULT '0',
  `chargeable` int NOT NULL DEFAULT '0',
  `customer` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `switchport` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `duplex_master_id` int DEFAULT NULL,
  `number` smallint NOT NULL,
  `colo_circuit_ref` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `ticket_ref` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `private_notes` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `owned_by` int NOT NULL DEFAULT '0',
  `description` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `colo_billing_ref` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `cust_id` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_CB80B54AB0F978FF` (`patch_panel_port_id`),
  KEY `IDX_CB80B54A3838446` (`duplex_master_id`),
  CONSTRAINT `FK_CB80B54A3838446` FOREIGN KEY (`duplex_master_id`) REFERENCES `patch_panel_port_history` (`id`),
  CONSTRAINT `FK_CB80B54AB0F978FF` FOREIGN KEY (`patch_panel_port_id`) REFERENCES `patch_panel_port` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `patch_panel_port_history`
--

LOCK TABLES `patch_panel_port_history` WRITE;
/*!40000 ALTER TABLE `patch_panel_port_history` DISABLE KEYS */;
/*!40000 ALTER TABLE `patch_panel_port_history` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `patch_panel_port_history_file`
--

DROP TABLE IF EXISTS `patch_panel_port_history_file`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `patch_panel_port_history_file` (
  `id` int NOT NULL AUTO_INCREMENT,
  `patch_panel_port_history_id` int DEFAULT NULL,
  `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `type` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `uploaded_at` datetime NOT NULL,
  `uploaded_by` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `size` int NOT NULL,
  `is_private` tinyint(1) NOT NULL DEFAULT '0',
  `storage_location` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_206EAD4E6F461430` (`patch_panel_port_history_id`),
  CONSTRAINT `FK_206EAD4E6F461430` FOREIGN KEY (`patch_panel_port_history_id`) REFERENCES `patch_panel_port_history` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `patch_panel_port_history_file`
--

LOCK TABLES `patch_panel_port_history_file` WRITE;
/*!40000 ALTER TABLE `patch_panel_port_history_file` DISABLE KEYS */;
/*!40000 ALTER TABLE `patch_panel_port_history_file` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `peering_manager`
--

DROP TABLE IF EXISTS `peering_manager`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `peering_manager` (
  `id` int NOT NULL AUTO_INCREMENT,
  `custid` int DEFAULT NULL,
  `peerid` int DEFAULT NULL,
  `email_last_sent` datetime DEFAULT NULL,
  `emails_sent` int DEFAULT NULL,
  `peered` tinyint(1) DEFAULT NULL,
  `rejected` tinyint(1) DEFAULT NULL,
  `notes` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_35A72597DA0209B9` (`custid`),
  KEY `IDX_35A725974E5F9AFF` (`peerid`),
  CONSTRAINT `FK_35A725974E5F9AFF` FOREIGN KEY (`peerid`) REFERENCES `cust` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_35A72597DA0209B9` FOREIGN KEY (`custid`) REFERENCES `cust` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `peering_manager`
--

LOCK TABLES `peering_manager` WRITE;
/*!40000 ALTER TABLE `peering_manager` DISABLE KEYS */;
/*!40000 ALTER TABLE `peering_manager` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `peering_matrix`
--

DROP TABLE IF EXISTS `peering_matrix`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `peering_matrix` (
  `id` int NOT NULL AUTO_INCREMENT,
  `x_custid` int DEFAULT NULL,
  `y_custid` int DEFAULT NULL,
  `vlan` int DEFAULT NULL,
  `x_as` int DEFAULT NULL,
  `y_as` int DEFAULT NULL,
  `peering_status` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_C1A6F6F9A4CA6408` (`x_custid`),
  KEY `IDX_C1A6F6F968606496` (`y_custid`),
  CONSTRAINT `FK_C1A6F6F968606496` FOREIGN KEY (`y_custid`) REFERENCES `cust` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_C1A6F6F9A4CA6408` FOREIGN KEY (`x_custid`) REFERENCES `cust` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `peering_matrix`
--

LOCK TABLES `peering_matrix` WRITE;
/*!40000 ALTER TABLE `peering_matrix` DISABLE KEYS */;
/*!40000 ALTER TABLE `peering_matrix` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `physicalinterface`
--

DROP TABLE IF EXISTS `physicalinterface`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `physicalinterface` (
  `id` int NOT NULL AUTO_INCREMENT,
  `switchportid` int DEFAULT NULL,
  `fanout_physical_interface_id` int DEFAULT NULL,
  `virtualinterfaceid` int DEFAULT NULL,
  `status` int DEFAULT NULL,
  `speed` int DEFAULT NULL,
  `duplex` varchar(16) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `rate_limit` int unsigned DEFAULT NULL,
  `notes` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `autoneg` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_5FFF4D60E5F6FACB` (`switchportid`),
  UNIQUE KEY `UNIQ_5FFF4D602E68AB8C` (`fanout_physical_interface_id`),
  KEY `IDX_5FFF4D60BFDF15D5` (`virtualinterfaceid`),
  CONSTRAINT `FK_5FFF4D602E68AB8C` FOREIGN KEY (`fanout_physical_interface_id`) REFERENCES `physicalinterface` (`id`),
  CONSTRAINT `FK_5FFF4D60BFDF15D5` FOREIGN KEY (`virtualinterfaceid`) REFERENCES `virtualinterface` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_5FFF4D60E5F6FACB` FOREIGN KEY (`switchportid`) REFERENCES `switchport` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb3 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `physicalinterface`
--

LOCK TABLES `physicalinterface` WRITE;
/*!40000 ALTER TABLE `physicalinterface` DISABLE KEYS */;
INSERT INTO `physicalinterface` VALUES (1,3,NULL,1,1,1000,'full',NULL,'',1,NULL,NULL),(2,4,NULL,1,1,1000,'full',NULL,'',1,NULL,NULL),(3,25,NULL,2,1,1000,'full',NULL,NULL,1,NULL,NULL),(4,8,NULL,3,1,100,'full',NULL,NULL,1,NULL,NULL),(5,6,NULL,4,1,10,'full',NULL,NULL,1,NULL,NULL),(6,30,NULL,5,1,10,'full',NULL,NULL,1,NULL,NULL),(7,9,NULL,6,1,1000,'full',NULL,NULL,1,NULL,NULL),(8,32,NULL,7,1,10000,'full',NULL,NULL,1,NULL,NULL),(9,18,NULL,8,1,1000,'full',NULL,NULL,1,NULL,NULL),(10,42,NULL,9,1,1000,'full',NULL,NULL,1,NULL,NULL),(11,19,NULL,8,1,1000,'full',NULL,NULL,1,NULL,NULL),(12,43,NULL,9,1,1000,'full',NULL,NULL,1,NULL,NULL),(13,27,NULL,10,4,1000,'full',NULL,NULL,1,NULL,NULL);
/*!40000 ALTER TABLE `physicalinterface` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `route_server_filters`
--

DROP TABLE IF EXISTS `route_server_filters`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `route_server_filters` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `customer_id` int DEFAULT NULL,
  `peer_id` int DEFAULT NULL,
  `vlan_id` int DEFAULT NULL,
  `received_prefix` varchar(43) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `advertised_prefix` varchar(43) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `protocol` smallint DEFAULT NULL,
  `action_advertise` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `action_receive` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `enabled` tinyint(1) NOT NULL DEFAULT '1',
  `order_by` int NOT NULL,
  `live` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `route_server_filters_customer_id_order_by_unique` (`customer_id`,`order_by`),
  KEY `route_server_filters_peer_id_foreign` (`peer_id`),
  KEY `route_server_filters_vlan_id_foreign` (`vlan_id`),
  CONSTRAINT `route_server_filters_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `cust` (`id`),
  CONSTRAINT `route_server_filters_peer_id_foreign` FOREIGN KEY (`peer_id`) REFERENCES `cust` (`id`),
  CONSTRAINT `route_server_filters_vlan_id_foreign` FOREIGN KEY (`vlan_id`) REFERENCES `vlan` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `route_server_filters`
--

LOCK TABLES `route_server_filters` WRITE;
/*!40000 ALTER TABLE `route_server_filters` DISABLE KEYS */;
/*!40000 ALTER TABLE `route_server_filters` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `routers`
--

DROP TABLE IF EXISTS `routers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `routers` (
  `id` int NOT NULL AUTO_INCREMENT,
  `vlan_id` int NOT NULL,
  `handle` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `protocol` smallint unsigned NOT NULL,
  `type` smallint unsigned NOT NULL,
  `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `shortname` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `router_id` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `peering_ip` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `asn` int unsigned NOT NULL,
  `software` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `mgmt_host` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `api` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `api_type` smallint unsigned NOT NULL,
  `lg_access` smallint unsigned DEFAULT NULL,
  `quarantine` tinyint(1) NOT NULL,
  `bgp_lc` tinyint(1) NOT NULL,
  `template` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `skip_md5` tinyint(1) NOT NULL,
  `rpki` tinyint(1) NOT NULL DEFAULT '0',
  `software_version` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `operating_system` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `operating_system_version` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `rfc1997_passthru` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `last_updated` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_504FC9BE918020D9` (`handle`),
  KEY `IDX_504FC9BE8B4937A1` (`vlan_id`),
  CONSTRAINT `FK_504FC9BE8B4937A1` FOREIGN KEY (`vlan_id`) REFERENCES `vlan` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=utf8mb3 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `routers`
--

LOCK TABLES `routers` WRITE;
/*!40000 ALTER TABLE `routers` DISABLE KEYS */;
INSERT INTO `routers` VALUES (1,1,'rc1-lan1-ipv4',4,2,'INEX LAN1 - Route Collector - IPv4','RC1 - LAN1 - IPv4','192.0.2.8','192.0.2.8',65500,'1','203.0.113.8','http://rc1-lan1-ipv4.mgmt.example.com/api',1,0,0,0,'api/v4/router/collector/bird/standard',0,0,NULL,NULL,NULL,0,NULL,NULL,NULL),(2,1,'rc1-lan1-ipv6',6,2,'INEX LAN1 - Route Collector - IPv6','RC1 - LAN1 - IPv6','192.0.2.8','2001:db8::8',65500,'1','2001:db8:0:0:2::8','http://rc1-lan1-ipv6.mgmt.example.com/api',1,0,0,0,'api/v4/router/collector/bird/standard',0,0,NULL,NULL,NULL,0,NULL,NULL,NULL),(3,2,'rc1-lan2-ipv4',4,2,'INEX LAN2 - Route Collector - IPv4','RC1 - LAN2 - IPv4','192.0.2.9','192.0.2.9',65500,'1','203.0.113.9','http://rc1-lan2-ipv4.mgmt.example.com/api',1,0,0,0,'api/v4/router/collector/bird/standard',0,0,NULL,NULL,NULL,0,NULL,NULL,NULL),(4,2,'rc1-lan2-ipv6',6,2,'INEX LAN2 - Route Collector - IPv6','RC1 - LAN2 - IPv6','192.0.2.9','2001:db8::9',65500,'1','2001:db8:0:0:2::9','http://rc1-lan2-ipv6.mgmt.example.com/api',1,0,0,0,'api/v4/router/collector/bird/standard',0,0,NULL,NULL,NULL,0,NULL,NULL,NULL),(5,1,'rs1-lan1-ipv4',4,1,'INEX LAN1 - Route Server - IPv4','RS1 - LAN1 - IPv4','192.0.2.18','192.0.2.18',65501,'1','203.0.113.18','http://rs1-lan1-ipv4.mgmt.example.com/api',0,0,0,0,'api/v4/router/server/bird/standard',0,0,NULL,NULL,NULL,0,NULL,NULL,NULL),(6,1,'rs1-lan1-ipv6',6,1,'INEX LAN1 - Route Server - IPv6','RS1 - LAN1 - IPv6','192.0.2.18','2001:db8::18',65501,'1','2001:db8:0:0:2::18','http://rs1-lan1-ipv6.mgmt.example.com/api',1,0,0,0,'api/v4/router/server/bird/standard',0,0,NULL,NULL,NULL,0,NULL,NULL,NULL),(7,2,'rs1-lan2-ipv4',4,1,'INEX LAN2 - Route Server - IPv4','RS1 - LAN2 - IPv4','192.0.2.19','192.0.2.19',65501,'1','203.0.113.19','http://rs1-lan2-ipv4.mgmt.example.com/api',1,0,0,1,'api/v4/router/server/bird/standard',0,0,NULL,NULL,NULL,0,NULL,NULL,NULL),(8,2,'rs1-lan2-ipv6',6,1,'INEX LAN2 - Route Server - IPv6','RS1 - LAN2 - IPv6','192.0.2.19','2001:db8::19',65501,'1','2001:db8:0:0:2::19','http://rs1-lan2-ipv6.mgmt.example.com/api',1,0,0,0,'api/v4/router/server/bird/standard',0,0,NULL,NULL,NULL,0,NULL,NULL,NULL),(9,1,'as112-lan1-ipv4',4,3,'INEX LAN1 - AS112 Service - IPv4','AS112 - LAN1 - IPv4','192.0.2.6','192.0.2.6',112,'1','203.0.113.6','http://as112-lan1-ipv4.mgmt.example.com/api',1,0,0,0,'api/v4/router/as112/bird/standard',1,0,NULL,NULL,NULL,0,NULL,NULL,NULL),(10,1,'as112-lan1-ipv6',6,3,'INEX LAN1 - AS112 Service - IPv6','AS112 - LAN1 - IPv6','192.0.2.6','2001:db8:0:0:2::6',112,'1','203.0.113.6','http://as112-lan1-ipv6.mgmt.example.com/api',1,0,0,0,'api/v4/router/as112/bird/standard',1,0,NULL,NULL,NULL,0,NULL,NULL,NULL),(11,2,'as112-lan2-ipv4',4,3,'INEX LAN2 - AS112 Service - IPv4','AS112 - LAN2 - IPv4','192.0.2.16','192.0.2.16',112,'1','203.0.113.16','http://as112-lan2-ipv4.mgmt.example.com/api',1,0,0,0,'api/v4/router/as112/bird/standard',0,0,NULL,NULL,NULL,0,NULL,NULL,NULL),(12,2,'as112-lan2-ipv6',6,3,'INEX LAN2 - AS112 Service - IPv6','AS112 - LAN2 - IPv6','192.0.2.16','2001:db8:0:0:2::16',112,'1','203.0.113.16','http://as112-lan2-ipv6.mgmt.example.com/api',1,0,0,0,'api/v4/router/as112/bird/standard',0,0,NULL,NULL,NULL,0,NULL,NULL,NULL),(13,1,'unknown-template',6,2,'INEX LAN2 - Route Collector - IPv6','RC1 - LAN2 - IPv6','192.0.2.9','2001:db8::9',65500,'1','2001:db8:0:0:2::9','http://rc1-lan2-ipv6.mgmt.example.com/api',1,0,0,0,'api/v4/router/does-not-exist',0,0,NULL,NULL,NULL,0,NULL,NULL,NULL),(29,1,'b2-rs1-lan1-ipv4',4,1,'Bird2 - INEX LAN1 - Route Server - IPv4','B2 RS1 - LAN1 - IPv4','192.0.2.18','192.0.2.18',65501,'6','203.0.113.18',NULL,0,0,0,1,'api/v4/router/server/bird2/standard',0,1,NULL,NULL,NULL,0,NULL,NULL,NULL),(30,1,'b2-rs1-lan1-ipv6',6,1,'Bird2 - INEX LAN1 - Route Server - IPv6','B2 RS1 - LAN1 - IPv6','192.0.2.18','2001:db8::8',65501,'6','203.0.113.18',NULL,0,0,0,1,'api/v4/router/server/bird2/standard',0,1,NULL,NULL,NULL,1,NULL,NULL,NULL),(31,1,'b2-rc1-lan1-ipv4',4,2,'Bird2 - INEX LAN1 - Route Collector - IPv4','B2 RC1 - LAN1 - IPv4','192.0.2.8','192.0.2.8',65500,'1','203.0.113.8','http://rc1-lan1-ipv4.mgmt.example.com/api',1,0,0,1,'api/v4/router/collector/bird2/standard',0,1,NULL,NULL,NULL,0,NULL,NULL,NULL),(32,1,'b2-rc1-lan1-ipv6',6,2,'Bird2 - INEX LAN1 - Route Collector - IPv6','B2 RC1 - LAN1 - IPv6','192.0.2.8','2001:db8::8',65500,'1','2001:db8:0:0:2::8','http://rc1-lan1-ipv6.mgmt.example.com/api',1,0,0,1,'api/v4/router/collector/bird2/standard',0,1,NULL,NULL,NULL,0,NULL,NULL,NULL);
/*!40000 ALTER TABLE `routers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rs_prefixes`
--

DROP TABLE IF EXISTS `rs_prefixes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `rs_prefixes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `custid` int DEFAULT NULL,
  `timestamp` datetime DEFAULT NULL,
  `prefix` varchar(64) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `protocol` int DEFAULT NULL,
  `irrdb` int DEFAULT NULL,
  `rs_origin` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_29FA9871DA0209B9` (`custid`),
  CONSTRAINT `FK_29FA9871DA0209B9` FOREIGN KEY (`custid`) REFERENCES `cust` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rs_prefixes`
--

LOCK TABLES `rs_prefixes` WRITE;
/*!40000 ALTER TABLE `rs_prefixes` DISABLE KEYS */;
/*!40000 ALTER TABLE `rs_prefixes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sessions` (
  `id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint DEFAULT NULL,
  `ip_address` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `payload` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `sessions_id_unique` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sessions`
--

LOCK TABLES `sessions` WRITE;
/*!40000 ALTER TABLE `sessions` DISABLE KEYS */;
INSERT INTO `sessions` VALUES ('j3MLjT008zdFSJhApwe7dwiY8BXvpdR7KFRGSzv7',1,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_2) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/79.0.3945.130 Safari/537.36','ZXlKcGRpSTZJakZMVGlzelpGVXdkamhCYVhJNVkyaG9WM1oxVUhjOVBTSXNJblpoYkhWbElqb2lWVmhpZDBwSUswSjJSREZ6Y3paaFIwaHdSRFJUTVdseFkyNHJNSHBtUW1WTlpFUmhkVzUyU3poNlZWRmNMMnh6ZWtKQ05EZzBSMEpSVVhaQ1NtUk1aWGxVTWtOU1FuQkRVbGMzVmtsSVdrazJibXhPYW1wb09XZzNRM1ZaZUd0elJGUXlkbTUwVjFsbVdVSjVUekEyVVd0cWVGWnRkMWw2Ym1aSlJEWm5UblpOYlRVd1ZrZFFkVGhVZHpOTFRYRkNORmxpUjNSSmQyYzBiV2RDUkZoeFpWVkphVkJJUlRGcmJ6VlhZVnd2ZVV4UWFYQmxkelJzV2xGeVUzaEJhM05TYTJZck1rcENVR041VWx3dlhDOWhiM3BUUzBKV2JDdHFVVkI1ZFdGY0wyOVZVVE5yT1Zsd2JWZ3JSbTh6YjFwc1JVaHBRaXR5UVdvNFJuazBZV2xvWVZkSlJsZHhjWFJ6TlVoelMwMVVUVWs1Y1dGeFJsaHNiMnhwUjNsbVRXWlNLMlo1VkVGY0wxTm1kRXRHTUdGQ2NrdzNkMGxHTm5oQmVrbEpPRUY2ZFVWdE1WQk5WbTFMTVVaUFpqWjFTbTFLU0RSNVRra3hXVWxhVFZsWlNubFVlVmxqU0dSVFEzbzVORTk2WVZBd1R6VnpQU0lzSW0xaFl5STZJbUl5TXpNeE1URTFZemd5TlRJMk1HVTFPRFkxT0RaaE1EVTNORFU0WTJJNE5URmxOakkxTnpnMk1tUTFPVE00TlRobVpHVmhOVE5oWmpabU9HRmhOaklpZlE9PQ==',1580126664);
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sflow_receiver`
--

DROP TABLE IF EXISTS `sflow_receiver`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sflow_receiver` (
  `id` int NOT NULL AUTO_INCREMENT,
  `virtual_interface_id` int DEFAULT NULL,
  `dst_ip` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `dst_port` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_E633EA142C0D6F5F` (`virtual_interface_id`),
  CONSTRAINT `FK_E633EA142C0D6F5F` FOREIGN KEY (`virtual_interface_id`) REFERENCES `virtualinterface` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sflow_receiver`
--

LOCK TABLES `sflow_receiver` WRITE;
/*!40000 ALTER TABLE `sflow_receiver` DISABLE KEYS */;
/*!40000 ALTER TABLE `sflow_receiver` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `switch`
--

DROP TABLE IF EXISTS `switch`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `switch` (
  `id` int NOT NULL AUTO_INCREMENT,
  `infrastructure` int DEFAULT NULL,
  `cabinetid` int DEFAULT NULL,
  `vendorid` int DEFAULT NULL,
  `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `hostname` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `ipv4addr` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `ipv6addr` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `snmppasswd` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `model` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `active` tinyint(1) DEFAULT '1',
  `os` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `osDate` datetime DEFAULT NULL,
  `osVersion` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `lastPolled` datetime DEFAULT NULL,
  `notes` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `serialNumber` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `mauSupported` tinyint(1) DEFAULT NULL,
  `asn` int unsigned DEFAULT NULL,
  `loopback_ip` varchar(39) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `loopback_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `mgmt_mac_address` varchar(12) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `snmp_engine_time` bigint DEFAULT NULL,
  `snmp_system_uptime` bigint DEFAULT NULL,
  `snmp_engine_boots` bigint DEFAULT NULL,
  `poll` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_6FE94B185E237E06` (`name`),
  UNIQUE KEY `UNIQ_6FE94B1850C101F8` (`loopback_ip`),
  KEY `IDX_6FE94B18D129B190` (`infrastructure`),
  KEY `IDX_6FE94B182B96718A` (`cabinetid`),
  KEY `IDX_6FE94B18420FB55F` (`vendorid`),
  CONSTRAINT `FK_6FE94B182B96718A` FOREIGN KEY (`cabinetid`) REFERENCES `cabinet` (`id`),
  CONSTRAINT `FK_6FE94B18420FB55F` FOREIGN KEY (`vendorid`) REFERENCES `vendor` (`id`),
  CONSTRAINT `FK_6FE94B18D129B190` FOREIGN KEY (`infrastructure`) REFERENCES `infrastructure` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb3 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `switch`
--

LOCK TABLES `switch` WRITE;
/*!40000 ALTER TABLE `switch` DISABLE KEYS */;
INSERT INTO `switch` VALUES (1,1,1,12,'switch1','s1','10.0.0.1','','public','FESX624',1,NULL,NULL,NULL,NULL,'',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL),(2,2,1,12,'switch2','s2','10.0.0.2','','public','FESX624',1,NULL,NULL,NULL,NULL,'',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL);
/*!40000 ALTER TABLE `switch` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `switchport`
--

DROP TABLE IF EXISTS `switchport`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `switchport` (
  `id` int NOT NULL AUTO_INCREMENT,
  `switchid` int DEFAULT NULL,
  `type` int DEFAULT NULL,
  `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `ifIndex` int DEFAULT NULL,
  `ifName` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `ifAlias` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `ifHighSpeed` int DEFAULT NULL,
  `ifMtu` int DEFAULT NULL,
  `ifPhysAddress` varchar(17) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `ifAdminStatus` int DEFAULT NULL,
  `ifOperStatus` int DEFAULT NULL,
  `ifLastChange` int DEFAULT NULL,
  `lastSnmpPoll` datetime DEFAULT NULL,
  `lagIfIndex` int DEFAULT NULL,
  `mauType` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `mauState` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `mauAvailability` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `mauJacktype` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `mauAutoNegSupported` tinyint(1) DEFAULT NULL,
  `mauAutoNegAdminState` tinyint(1) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_F84274F1DC2C08F8` (`switchid`),
  CONSTRAINT `FK_F84274F1DC2C08F8` FOREIGN KEY (`switchid`) REFERENCES `switch` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=57 DEFAULT CHARSET=utf8mb3 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `switchport`
--

LOCK TABLES `switchport` WRITE;
/*!40000 ALTER TABLE `switchport` DISABLE KEYS */;
INSERT INTO `switchport` VALUES (1,1,1,'GigabitEthernet1',1,1,'GigabitEthernet1','GigabitEthernet1',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(2,1,1,'GigabitEthernet2',1,2,'GigabitEthernet2','GigabitEthernet2',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(3,1,1,'GigabitEthernet3',1,3,'GigabitEthernet3','GigabitEthernet3',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(4,1,1,'GigabitEthernet4',1,4,'GigabitEthernet4','GigabitEthernet4',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(5,1,1,'GigabitEthernet5',1,5,'GigabitEthernet5','GigabitEthernet5',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(6,1,1,'GigabitEthernet6',1,6,'GigabitEthernet6','GigabitEthernet6',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(7,1,1,'GigabitEthernet7',1,7,'GigabitEthernet7','GigabitEthernet7',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(8,1,1,'GigabitEthernet8',1,8,'GigabitEthernet8','GigabitEthernet8',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(9,1,1,'GigabitEthernet9',1,9,'GigabitEthernet9','GigabitEthernet9',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(10,1,1,'GigabitEthernet10',1,10,'GigabitEthernet10','GigabitEthernet10',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(11,1,1,'GigabitEthernet11',1,11,'GigabitEthernet11','GigabitEthernet11',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(12,1,1,'GigabitEthernet12',1,12,'GigabitEthernet12','GigabitEthernet12',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(13,1,1,'GigabitEthernet13',1,13,'GigabitEthernet13','GigabitEthernet13',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(14,1,1,'GigabitEthernet14',1,14,'GigabitEthernet14','GigabitEthernet14',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(15,1,1,'GigabitEthernet15',1,15,'GigabitEthernet15','GigabitEthernet15',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(16,1,1,'GigabitEthernet16',1,16,'GigabitEthernet16','GigabitEthernet16',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(17,1,1,'GigabitEthernet17',1,17,'GigabitEthernet17','GigabitEthernet17',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(18,1,3,'GigabitEthernet18',1,18,'GigabitEthernet18','GigabitEthernet18',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(19,1,3,'GigabitEthernet19',1,19,'GigabitEthernet19','GigabitEthernet19',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(20,1,1,'GigabitEthernet20',1,20,'GigabitEthernet20','GigabitEthernet20',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(21,1,1,'GigabitEthernet21',1,21,'GigabitEthernet21','GigabitEthernet21',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(22,1,1,'GigabitEthernet22',1,22,'GigabitEthernet22','GigabitEthernet22',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(23,1,1,'GigabitEthernet23',1,23,'GigabitEthernet23','GigabitEthernet23',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(24,1,1,'GigabitEthernet24',1,24,'GigabitEthernet24','GigabitEthernet24',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(25,2,1,'GigabitEthernet1',1,25,'GigabitEthernet1','GigabitEthernet1',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(26,2,1,'GigabitEthernet2',1,26,'GigabitEthernet2','GigabitEthernet2',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(27,2,1,'GigabitEthernet3',1,27,'GigabitEthernet3','GigabitEthernet3',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(28,2,1,'GigabitEthernet4',1,28,'GigabitEthernet4','GigabitEthernet4',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(29,2,1,'GigabitEthernet5',1,29,'GigabitEthernet5','GigabitEthernet5',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(30,2,1,'GigabitEthernet6',1,30,'GigabitEthernet6','GigabitEthernet6',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(31,2,1,'GigabitEthernet7',1,31,'GigabitEthernet7','GigabitEthernet7',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(32,2,1,'GigabitEthernet8',1,32,'GigabitEthernet8','GigabitEthernet8',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(33,2,1,'GigabitEthernet9',1,33,'GigabitEthernet9','GigabitEthernet9',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(34,2,1,'GigabitEthernet10',1,34,'GigabitEthernet10','GigabitEthernet10',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(35,2,1,'GigabitEthernet11',1,35,'GigabitEthernet11','GigabitEthernet11',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(36,2,1,'GigabitEthernet12',1,36,'GigabitEthernet12','GigabitEthernet12',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(37,2,1,'GigabitEthernet13',1,37,'GigabitEthernet13','GigabitEthernet13',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(38,2,1,'GigabitEthernet14',1,38,'GigabitEthernet14','GigabitEthernet14',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(39,2,1,'GigabitEthernet15',1,39,'GigabitEthernet15','GigabitEthernet15',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(40,2,1,'GigabitEthernet16',1,40,'GigabitEthernet16','GigabitEthernet16',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(41,2,1,'GigabitEthernet17',1,41,'GigabitEthernet17','GigabitEthernet17',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(42,2,3,'GigabitEthernet18',1,42,'GigabitEthernet18','GigabitEthernet18',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(43,2,3,'GigabitEthernet19',1,43,'GigabitEthernet19','GigabitEthernet19',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(44,2,1,'GigabitEthernet20',1,44,'GigabitEthernet20','GigabitEthernet20',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(45,2,1,'GigabitEthernet21',1,45,'GigabitEthernet21','GigabitEthernet21',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(46,2,1,'GigabitEthernet22',1,46,'GigabitEthernet22','GigabitEthernet22',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(47,2,1,'GigabitEthernet23',1,47,'GigabitEthernet23','GigabitEthernet23',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(48,2,1,'GigabitEthernet24',1,48,'GigabitEthernet24','GigabitEthernet24',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(49,1,3,'GigabitEthernet25',1,49,'GigabitEthernet25','GigabitEthernet25',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(50,1,3,'GigabitEthernet26',1,50,'GigabitEthernet26','GigabitEthernet26',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(51,1,3,'GigabitEthernet27',1,51,'GigabitEthernet27','GigabitEthernet27',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(52,1,3,'GigabitEthernet28',1,52,'GigabitEthernet28','GigabitEthernet28',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(53,2,3,'GigabitEthernet29',1,53,'GigabitEthernet29','GigabitEthernet29',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(54,2,3,'GigabitEthernet30',1,54,'GigabitEthernet30','GigabitEthernet30',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(55,2,3,'GigabitEthernet31',1,55,'GigabitEthernet31','GigabitEthernet31',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(56,2,3,'GigabitEthernet32',1,56,'GigabitEthernet32','GigabitEthernet32',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL);
/*!40000 ALTER TABLE `switchport` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `telescope_entries`
--

DROP TABLE IF EXISTS `telescope_entries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `telescope_entries` (
  `sequence` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch_id` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `family_hash` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `should_display_on_index` tinyint(1) NOT NULL DEFAULT '1',
  `type` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `content` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`sequence`),
  UNIQUE KEY `telescope_entries_uuid_unique` (`uuid`),
  KEY `telescope_entries_batch_id_index` (`batch_id`),
  KEY `telescope_entries_type_should_display_on_index_index` (`type`,`should_display_on_index`),
  KEY `telescope_entries_family_hash_index` (`family_hash`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `telescope_entries`
--

LOCK TABLES `telescope_entries` WRITE;
/*!40000 ALTER TABLE `telescope_entries` DISABLE KEYS */;
INSERT INTO `telescope_entries` VALUES (1,'8ff2744d-3f5e-4d3f-8136-6d1b43cb27ea','8ff2744d-b7d2-45ba-a780-89265a29a336','7fbfaf0b63e202da3dffb66c93082246',1,'exception','{\"class\":\"Illuminate\\\\Database\\\\QueryException\",\"file\":\"\\/Users\\/yannrobin\\/Documents\\/development\\/ixp\\/IXP-Manager\\/vendor\\/laravel\\/framework\\/src\\/Illuminate\\/Database\\/Connection.php\",\"line\":669,\"message\":\"SQLSTATE[42S02]: Base table or view not found: 1146 Table \'ixp_ci.docstore_directories\' doesn\'t exist (SQL: select * from `docstore_directories` where `parent_dir_id` is null order by `name` asc)\",\"trace\":[{\"file\":\"\\/Users\\/yannrobin\\/Documents\\/development\\/ixp\\/IXP-Manager\\/vendor\\/laravel\\/framework\\/src\\/Illuminate\\/Database\\/Connection.php\",\"line\":629},{\"file\":\"\\/Users\\/yannrobin\\/Documents\\/development\\/ixp\\/IXP-Manager\\/vendor\\/laravel\\/framework\\/src\\/Illuminate\\/Database\\/Connection.php\",\"line\":338},{\"file\":\"\\/Users\\/yannrobin\\/Documents\\/development\\/ixp\\/IXP-Manager\\/vendor\\/laravel\\/framework\\/src\\/Illuminate\\/Database\\/Query\\/Builder.php\",\"line\":2132},{\"file\":\"\\/Users\\/yannrobin\\/Documents\\/development\\/ixp\\/IXP-Manager\\/vendor\\/laravel\\/framework\\/src\\/Illuminate\\/Database\\/Query\\/Builder.php\",\"line\":2120},{\"file\":\"\\/Users\\/yannrobin\\/Documents\\/development\\/ixp\\/IXP-Manager\\/vendor\\/laravel\\/framework\\/src\\/Illuminate\\/Database\\/Query\\/Builder.php\",\"line\":2592},{\"file\":\"\\/Users\\/yannrobin\\/Documents\\/development\\/ixp\\/IXP-Manager\\/vendor\\/laravel\\/framework\\/src\\/Illuminate\\/Database\\/Query\\/Builder.php\",\"line\":2121},{\"file\":\"\\/Users\\/yannrobin\\/Documents\\/development\\/ixp\\/IXP-Manager\\/vendor\\/laravel\\/framework\\/src\\/Illuminate\\/Database\\/Eloquent\\/Builder.php\",\"line\":537},{\"file\":\"\\/Users\\/yannrobin\\/Documents\\/development\\/ixp\\/IXP-Manager\\/vendor\\/laravel\\/framework\\/src\\/Illuminate\\/Database\\/Eloquent\\/Builder.php\",\"line\":521},{\"file\":\"\\/Users\\/yannrobin\\/Documents\\/development\\/ixp\\/IXP-Manager\\/app\\/Models\\/DocstoreDirectory.php\",\"line\":129},{\"file\":\"\\/Users\\/yannrobin\\/Documents\\/development\\/ixp\\/IXP-Manager\\/app\\/Http\\/Controllers\\/Docstore\\/DirectoryController.php\",\"line\":72},[],{\"file\":\"\\/Users\\/yannrobin\\/Documents\\/development\\/ixp\\/IXP-Manager\\/vendor\\/laravel\\/framework\\/src\\/Illuminate\\/Routing\\/Controller.php\",\"line\":54},{\"file\":\"\\/Users\\/yannrobin\\/Documents\\/development\\/ixp\\/IXP-Manager\\/vendor\\/laravel\\/framework\\/src\\/Illuminate\\/Routing\\/ControllerDispatcher.php\",\"line\":45},{\"file\":\"\\/Users\\/yannrobin\\/Documents\\/development\\/ixp\\/IXP-Manager\\/vendor\\/laravel\\/framework\\/src\\/Illuminate\\/Routing\\/Route.php\",\"line\":219},{\"file\":\"\\/Users\\/yannrobin\\/Documents\\/development\\/ixp\\/IXP-Manager\\/vendor\\/laravel\\/framework\\/src\\/Illuminate\\/Routing\\/Route.php\",\"line\":176},{\"file\":\"\\/Users\\/yannrobin\\/Documents\\/development\\/ixp\\/IXP-Manager\\/vendor\\/laravel\\/framework\\/src\\/Illuminate\\/Routing\\/Router.php\",\"line\":681},{\"file\":\"\\/Users\\/yannrobin\\/Documents\\/development\\/ixp\\/IXP-Manager\\/vendor\\/laravel\\/framework\\/src\\/Illuminate\\/Pipeline\\/Pipeline.php\",\"line\":130},{\"file\":\"\\/Users\\/yannrobin\\/Documents\\/development\\/ixp\\/IXP-Manager\\/app\\/Http\\/Middleware\\/ControllerEnabled.php\",\"line\":96},{\"file\":\"\\/Users\\/yannrobin\\/Documents\\/development\\/ixp\\/IXP-Manager\\/vendor\\/laravel\\/framework\\/src\\/Illuminate\\/Pipeline\\/Pipeline.php\",\"line\":171},{\"file\":\"\\/Users\\/yannrobin\\/Documents\\/development\\/ixp\\/IXP-Manager\\/vendor\\/laravel\\/framework\\/src\\/Illuminate\\/Routing\\/Middleware\\/SubstituteBindings.php\",\"line\":41},{\"file\":\"\\/Users\\/yannrobin\\/Documents\\/development\\/ixp\\/IXP-Manager\\/vendor\\/laravel\\/framework\\/src\\/Illuminate\\/Pipeline\\/Pipeline.php\",\"line\":171},{\"file\":\"\\/Users\\/yannrobin\\/Documents\\/development\\/ixp\\/IXP-Manager\\/vendor\\/laravel\\/framework\\/src\\/Illuminate\\/Foundation\\/Http\\/Middleware\\/VerifyCsrfToken.php\",\"line\":76},{\"file\":\"\\/Users\\/yannrobin\\/Documents\\/development\\/ixp\\/IXP-Manager\\/vendor\\/laravel\\/framework\\/src\\/Illuminate\\/Pipeline\\/Pipeline.php\",\"line\":171},{\"file\":\"\\/Users\\/yannrobin\\/Documents\\/development\\/ixp\\/IXP-Manager\\/vendor\\/laravel\\/framework\\/src\\/Illuminate\\/View\\/Middleware\\/ShareErrorsFromSession.php\",\"line\":49},{\"file\":\"\\/Users\\/yannrobin\\/Documents\\/development\\/ixp\\/IXP-Manager\\/vendor\\/laravel\\/framework\\/src\\/Illuminate\\/Pipeline\\/Pipeline.php\",\"line\":171},{\"file\":\"\\/Users\\/yannrobin\\/Documents\\/development\\/ixp\\/IXP-Manager\\/vendor\\/laravel\\/framework\\/src\\/Illuminate\\/Session\\/Middleware\\/StartSession.php\",\"line\":56},{\"file\":\"\\/Users\\/yannrobin\\/Documents\\/development\\/ixp\\/IXP-Manager\\/vendor\\/laravel\\/framework\\/src\\/Illuminate\\/Pipeline\\/Pipeline.php\",\"line\":171},{\"file\":\"\\/Users\\/yannrobin\\/Documents\\/development\\/ixp\\/IXP-Manager\\/vendor\\/laravel\\/framework\\/src\\/Illuminate\\/Cookie\\/Middleware\\/AddQueuedCookiesToResponse.php\",\"line\":37},{\"file\":\"\\/Users\\/yannrobin\\/Documents\\/development\\/ixp\\/IXP-Manager\\/vendor\\/laravel\\/framework\\/src\\/Illuminate\\/Pipeline\\/Pipeline.php\",\"line\":171},{\"file\":\"\\/Users\\/yannrobin\\/Documents\\/development\\/ixp\\/IXP-Manager\\/vendor\\/laravel\\/framework\\/src\\/Illuminate\\/Cookie\\/Middleware\\/EncryptCookies.php\",\"line\":66},{\"file\":\"\\/Users\\/yannrobin\\/Documents\\/development\\/ixp\\/IXP-Manager\\/vendor\\/laravel\\/framework\\/src\\/Illuminate\\/Pipeline\\/Pipeline.php\",\"line\":171},{\"file\":\"\\/Users\\/yannrobin\\/Documents\\/development\\/ixp\\/IXP-Manager\\/vendor\\/laravel\\/framework\\/src\\/Illuminate\\/Pipeline\\/Pipeline.php\",\"line\":105},{\"file\":\"\\/Users\\/yannrobin\\/Documents\\/development\\/ixp\\/IXP-Manager\\/vendor\\/laravel\\/framework\\/src\\/Illuminate\\/Routing\\/Router.php\",\"line\":683},{\"file\":\"\\/Users\\/yannrobin\\/Documents\\/development\\/ixp\\/IXP-Manager\\/vendor\\/laravel\\/framework\\/src\\/Illuminate\\/Routing\\/Router.php\",\"line\":658},{\"file\":\"\\/Users\\/yannrobin\\/Documents\\/development\\/ixp\\/IXP-Manager\\/vendor\\/laravel\\/framework\\/src\\/Illuminate\\/Routing\\/Router.php\",\"line\":624},{\"file\":\"\\/Users\\/yannrobin\\/Documents\\/development\\/ixp\\/IXP-Manager\\/vendor\\/laravel\\/framework\\/src\\/Illuminate\\/Routing\\/Router.php\",\"line\":613},{\"file\":\"\\/Users\\/yannrobin\\/Documents\\/development\\/ixp\\/IXP-Manager\\/vendor\\/laravel\\/framework\\/src\\/Illuminate\\/Foundation\\/Http\\/Kernel.php\",\"line\":170},{\"file\":\"\\/Users\\/yannrobin\\/Documents\\/development\\/ixp\\/IXP-Manager\\/vendor\\/laravel\\/framework\\/src\\/Illuminate\\/Pipeline\\/Pipeline.php\",\"line\":130},{\"file\":\"\\/Users\\/yannrobin\\/Documents\\/development\\/ixp\\/IXP-Manager\\/vendor\\/fideloper\\/proxy\\/src\\/TrustProxies.php\",\"line\":57},{\"file\":\"\\/Users\\/yannrobin\\/Documents\\/development\\/ixp\\/IXP-Manager\\/vendor\\/laravel\\/framework\\/src\\/Illuminate\\/Pipeline\\/Pipeline.php\",\"line\":171},{\"file\":\"\\/Users\\/yannrobin\\/Documents\\/development\\/ixp\\/IXP-Manager\\/vendor\\/laravel\\/framework\\/src\\/Illuminate\\/Foundation\\/Http\\/Middleware\\/TransformsRequest.php\",\"line\":21},{\"file\":\"\\/Users\\/yannrobin\\/Documents\\/development\\/ixp\\/IXP-Manager\\/vendor\\/laravel\\/framework\\/src\\/Illuminate\\/Pipeline\\/Pipeline.php\",\"line\":171},{\"file\":\"\\/Users\\/yannrobin\\/Documents\\/development\\/ixp\\/IXP-Manager\\/vendor\\/laravel\\/framework\\/src\\/Illuminate\\/Foundation\\/Http\\/Middleware\\/TransformsRequest.php\",\"line\":21},{\"file\":\"\\/Users\\/yannrobin\\/Documents\\/development\\/ixp\\/IXP-Manager\\/vendor\\/laravel\\/framework\\/src\\/Illuminate\\/Pipeline\\/Pipeline.php\",\"line\":171},{\"file\":\"\\/Users\\/yannrobin\\/Documents\\/development\\/ixp\\/IXP-Manager\\/vendor\\/laravel\\/framework\\/src\\/Illuminate\\/Foundation\\/Http\\/Middleware\\/ValidatePostSize.php\",\"line\":27},{\"file\":\"\\/Users\\/yannrobin\\/Documents\\/development\\/ixp\\/IXP-Manager\\/vendor\\/laravel\\/framework\\/src\\/Illuminate\\/Pipeline\\/Pipeline.php\",\"line\":171},{\"file\":\"\\/Users\\/yannrobin\\/Documents\\/development\\/ixp\\/IXP-Manager\\/vendor\\/laravel\\/framework\\/src\\/Illuminate\\/Foundation\\/Http\\/Middleware\\/CheckForMaintenanceMode.php\",\"line\":63},{\"file\":\"\\/Users\\/yannrobin\\/Documents\\/development\\/ixp\\/IXP-Manager\\/vendor\\/laravel\\/framework\\/src\\/Illuminate\\/Pipeline\\/Pipeline.php\",\"line\":171},{\"file\":\"\\/Users\\/yannrobin\\/Documents\\/development\\/ixp\\/IXP-Manager\\/vendor\\/laravel\\/framework\\/src\\/Illuminate\\/Pipeline\\/Pipeline.php\",\"line\":105},{\"file\":\"\\/Users\\/yannrobin\\/Documents\\/development\\/ixp\\/IXP-Manager\\/vendor\\/laravel\\/framework\\/src\\/Illuminate\\/Foundation\\/Http\\/Kernel.php\",\"line\":145},{\"file\":\"\\/Users\\/yannrobin\\/Documents\\/development\\/ixp\\/IXP-Manager\\/vendor\\/laravel\\/framework\\/src\\/Illuminate\\/Foundation\\/Http\\/Kernel.php\",\"line\":110},{\"file\":\"\\/Users\\/yannrobin\\/Documents\\/development\\/ixp\\/IXP-Manager\\/public\\/index.php\",\"line\":85},{\"file\":\"\\/Users\\/yannrobin\\/Documents\\/development\\/ixp\\/IXP-Manager\\/server.php\",\"line\":21}],\"line_preview\":{\"660\":\"        \\/\\/ took to execute and log the query SQL, bindings and time in our memory.\",\"661\":\"        try {\",\"662\":\"            $result = $callback($query, $bindings);\",\"663\":\"        }\",\"664\":\"\",\"665\":\"        \\/\\/ If an exception occurs when attempting to run a query, we\'ll format the error\",\"666\":\"        \\/\\/ message to include the bindings with SQL, which will make this exception a\",\"667\":\"        \\/\\/ lot more helpful to the developer instead of just the database\'s errors.\",\"668\":\"        catch (Exception $e) {\",\"669\":\"            throw new QueryException(\",\"670\":\"                $query, $this->prepareBindings($bindings), $e\",\"671\":\"            );\",\"672\":\"        }\",\"673\":\"\",\"674\":\"        return $result;\",\"675\":\"    }\",\"676\":\"\",\"677\":\"    \\/**\",\"678\":\"     * Log a query in the connection\'s query log.\",\"679\":\"     *\"},\"hostname\":\"Yanns-MacBook-Pro.local\",\"user\":{\"id\":1,\"name\":null,\"email\":\"joe@siep.com\"},\"occurrences\":1}','2020-02-26 11:02:40'),(2,'9050da12-67a6-4d87-955a-ce770eee65f6','9050da12-6833-468f-be4b-fdffc215e5d8','4acf6fd3bd1ba79c05989b7b18db9175',1,'exception','{\"class\":\"Symfony\\\\Component\\\\Console\\\\Exception\\\\CommandNotFoundException\",\"file\":\"\\/Users\\/barryo\\/dev\\/ixp-inex\\/vendor\\/symfony\\/console\\/Application.php\",\"line\":669,\"message\":\"Command \\\"doctrine:schema:migrate\\\" is not defined.\\n\\nDid you mean one of these?\\n    doctrine:schema:create\\n    doctrine:schema:drop\\n    doctrine:schema:update\\n    doctrine:schema:validate\\n    utils:json-schema-post\",\"trace\":[{\"file\":\"\\/Users\\/barryo\\/dev\\/ixp-inex\\/vendor\\/symfony\\/console\\/Application.php\",\"line\":235},{\"file\":\"\\/Users\\/barryo\\/dev\\/ixp-inex\\/vendor\\/symfony\\/console\\/Application.php\",\"line\":147},{\"file\":\"\\/Users\\/barryo\\/dev\\/ixp-inex\\/vendor\\/laravel\\/framework\\/src\\/Illuminate\\/Console\\/Application.php\",\"line\":93},{\"file\":\"\\/Users\\/barryo\\/dev\\/ixp-inex\\/vendor\\/laravel\\/framework\\/src\\/Illuminate\\/Foundation\\/Console\\/Kernel.php\",\"line\":131},{\"file\":\"\\/Users\\/barryo\\/dev\\/ixp-inex\\/artisan\",\"line\":37}],\"line_preview\":{\"660\":\"\",\"661\":\"                if (1 == \\\\count($alternatives)) {\",\"662\":\"                    $message .= \\\"\\\\n\\\\nDid you mean this?\\\\n    \\\";\",\"663\":\"                } else {\",\"664\":\"                    $message .= \\\"\\\\n\\\\nDid you mean one of these?\\\\n    \\\";\",\"665\":\"                }\",\"666\":\"                $message .= implode(\\\"\\\\n    \\\", $alternatives);\",\"667\":\"            }\",\"668\":\"\",\"669\":\"            throw new CommandNotFoundException($message, array_values($alternatives));\",\"670\":\"        }\",\"671\":\"\",\"672\":\"        \\/\\/ filter out aliases for commands which are already on the list\",\"673\":\"        if (\\\\count($commands) > 1) {\",\"674\":\"            $commandList = $this->commandLoader ? array_merge(array_flip($this->commandLoader->getNames()), $this->commands) : $this->commands;\",\"675\":\"            $commands = array_unique(array_filter($commands, function ($nameOrAlias) use (&$commandList, $commands, &$aliases) {\",\"676\":\"                if (!$commandList[$nameOrAlias] instanceof Command) {\",\"677\":\"                    $commandList[$nameOrAlias] = $this->commandLoader->get($nameOrAlias);\",\"678\":\"                }\",\"679\":\"\"},\"hostname\":\"Barrys-MacBook-Pro.local\",\"occurrences\":1}','2020-04-13 10:15:04'),(3,'9101c3ca-7580-4574-b3d5-7f2008dd4637','9101c3cb-1198-4a7f-816d-9cc395e8df2b','04edfba26839d239e6f420a64cbad297',1,'exception','{\"class\":\"Doctrine\\\\DBAL\\\\Exception\\\\InvalidFieldNameException\",\"file\":\"\\/Users\\/yannrobin\\/Documents\\/development\\/ixp\\/IXP-Manager\\/vendor\\/doctrine\\/dbal\\/lib\\/Doctrine\\/DBAL\\/Driver\\/AbstractMySQLDriver.php\",\"line\":60,\"message\":\"An exception occurred while executing \'SELECT t0.name AS name_1, t0.colocation AS colocation_2, t0.height AS height_3, t0.u_counts_from AS u_counts_from_4, t0.type AS type_5, t0.notes AS notes_6, t0.id AS id_7, t0.locationid AS locationid_8 FROM cabinet t0 WHERE t0.id = ?\' with params [1]:\\n\\nSQLSTATE[42S22]: Column not found: 1054 Unknown column \'t0.colocation\' in \'field list\'\",\"trace\":[{\"file\":\"\\/Users\\/yannrobin\\/Documents\\/development\\/ixp\\/IXP-Manager\\/vendor\\/doctrine\\/dbal\\/lib\\/Doctrine\\/DBAL\\/DBALException.php\",\"line\":169},{\"file\":\"\\/Users\\/yannrobin\\/Documents\\/development\\/ixp\\/IXP-Manager\\/vendor\\/doctrine\\/dbal\\/lib\\/Doctrine\\/DBAL\\/DBALException.php\",\"line\":149},{\"file\":\"\\/Users\\/yannrobin\\/Documents\\/development\\/ixp\\/IXP-Manager\\/vendor\\/doctrine\\/dbal\\/lib\\/Doctrine\\/DBAL\\/Connection.php\",\"line\":914},{\"file\":\"\\/Users\\/yannrobin\\/Documents\\/development\\/ixp\\/IXP-Manager\\/vendor\\/doctrine\\/orm\\/lib\\/Doctrine\\/ORM\\/Persisters\\/Entity\\/BasicEntityPersister.php\",\"line\":718},{\"file\":\"\\/Users\\/yannrobin\\/Documents\\/development\\/ixp\\/IXP-Manager\\/vendor\\/doctrine\\/orm\\/lib\\/Doctrine\\/ORM\\/Persisters\\/Entity\\/BasicEntityPersister.php\",\"line\":736},{\"file\":\"\\/Users\\/yannrobin\\/Documents\\/development\\/ixp\\/IXP-Manager\\/vendor\\/doctrine\\/orm\\/lib\\/Doctrine\\/ORM\\/Proxy\\/ProxyFactory.php\",\"line\":159},{\"file\":\"\\/Users\\/yannrobin\\/Documents\\/development\\/ixp\\/IXP-Manager\\/database\\/Proxies\\/__CG__EntitiesCabinet.php\",\"line\":453},{\"file\":\"\\/Users\\/yannrobin\\/Documents\\/development\\/ixp\\/IXP-Manager\\/database\\/Proxies\\/__CG__EntitiesCabinet.php\",\"line\":453},{\"file\":\"\\/Users\\/yannrobin\\/Documents\\/development\\/ixp\\/IXP-Manager\\/resources\\/views\\/customer\\/overview-tabs\\/ports\\/port.foil.php\",\"line\":134},{\"file\":\"\\/Users\\/yannrobin\\/Documents\\/development\\/ixp\\/IXP-Manager\\/vendor\\/foil\\/foil\\/src\\/Template\\/Template.php\",\"line\":287},{\"file\":\"\\/Users\\/yannrobin\\/Documents\\/development\\/ixp\\/IXP-Manager\\/vendor\\/foil\\/foil\\/src\\/Template\\/Template.php\",\"line\":231},{\"file\":\"\\/Users\\/yannrobin\\/Documents\\/development\\/ixp\\/IXP-Manager\\/vendor\\/foil\\/foil\\/src\\/Engine.php\",\"line\":307},{\"file\":\"\\/Users\\/yannrobin\\/Documents\\/development\\/ixp\\/IXP-Manager\\/vendor\\/foil\\/foil\\/src\\/Engine.php\",\"line\":211},{\"file\":\"\\/Users\\/yannrobin\\/Documents\\/development\\/ixp\\/IXP-Manager\\/vendor\\/foil\\/foil\\/src\\/Template\\/Template.php\",\"line\":188},{\"file\":\"\\/Users\\/yannrobin\\/Documents\\/development\\/ixp\\/IXP-Manager\\/resources\\/views\\/customer\\/overview-tabs\\/ports.foil.php\",\"line\":16},{\"file\":\"\\/Users\\/yannrobin\\/Documents\\/development\\/ixp\\/IXP-Manager\\/vendor\\/foil\\/foil\\/src\\/Template\\/Template.php\",\"line\":287},{\"file\":\"\\/Users\\/yannrobin\\/Documents\\/development\\/ixp\\/IXP-Manager\\/vendor\\/foil\\/foil\\/src\\/Template\\/Template.php\",\"line\":231},{\"file\":\"\\/Users\\/yannrobin\\/Documents\\/development\\/ixp\\/IXP-Manager\\/vendor\\/foil\\/foil\\/src\\/Engine.php\",\"line\":307},{\"file\":\"\\/Users\\/yannrobin\\/Documents\\/development\\/ixp\\/IXP-Manager\\/vendor\\/foil\\/foil\\/src\\/Engine.php\",\"line\":211},{\"file\":\"\\/Users\\/yannrobin\\/Documents\\/development\\/ixp\\/IXP-Manager\\/vendor\\/foil\\/foil\\/src\\/Template\\/Template.php\",\"line\":188},{\"file\":\"\\/Users\\/yannrobin\\/Documents\\/development\\/ixp\\/IXP-Manager\\/resources\\/views\\/customer\\/overview.foil.php\",\"line\":375},{\"file\":\"\\/Users\\/yannrobin\\/Documents\\/development\\/ixp\\/IXP-Manager\\/vendor\\/foil\\/foil\\/src\\/Template\\/Template.php\",\"line\":287},{\"file\":\"\\/Users\\/yannrobin\\/Documents\\/development\\/ixp\\/IXP-Manager\\/vendor\\/foil\\/foil\\/src\\/Template\\/Template.php\",\"line\":231},{\"file\":\"\\/Users\\/yannrobin\\/Documents\\/development\\/ixp\\/IXP-Manager\\/vendor\\/foil\\/foil\\/src\\/Engine.php\",\"line\":307},{\"file\":\"\\/Users\\/yannrobin\\/Documents\\/development\\/ixp\\/IXP-Manager\\/vendor\\/foil\\/foil\\/src\\/Engine.php\",\"line\":231},{\"file\":\"\\/Users\\/yannrobin\\/Documents\\/development\\/ixp\\/IXP-Manager\\/vendor\\/foil\\/foil\\/src\\/Engine.php\",\"line\":204},{\"file\":\"\\/Users\\/yannrobin\\/Documents\\/development\\/ixp\\/IXP-Manager\\/app\\/Services\\/FoilEngine.php\",\"line\":51},{\"file\":\"\\/Users\\/yannrobin\\/Documents\\/development\\/ixp\\/IXP-Manager\\/vendor\\/laravel\\/framework\\/src\\/Illuminate\\/View\\/View.php\",\"line\":143},{\"file\":\"\\/Users\\/yannrobin\\/Documents\\/development\\/ixp\\/IXP-Manager\\/vendor\\/laravel\\/framework\\/src\\/Illuminate\\/View\\/View.php\",\"line\":126},{\"file\":\"\\/Users\\/yannrobin\\/Documents\\/development\\/ixp\\/IXP-Manager\\/vendor\\/laravel\\/framework\\/src\\/Illuminate\\/View\\/View.php\",\"line\":91},{\"file\":\"\\/Users\\/yannrobin\\/Documents\\/development\\/ixp\\/IXP-Manager\\/vendor\\/laravel\\/framework\\/src\\/Illuminate\\/Http\\/Response.php\",\"line\":42},{\"file\":\"\\/Users\\/yannrobin\\/Documents\\/development\\/ixp\\/IXP-Manager\\/vendor\\/symfony\\/http-foundation\\/Response.php\",\"line\":205},{\"file\":\"\\/Users\\/yannrobin\\/Documents\\/development\\/ixp\\/IXP-Manager\\/vendor\\/laravel\\/framework\\/src\\/Illuminate\\/Routing\\/Router.php\",\"line\":749},{\"file\":\"\\/Users\\/yannrobin\\/Documents\\/development\\/ixp\\/IXP-Manager\\/vendor\\/laravel\\/framework\\/src\\/Illuminate\\/Routing\\/Router.php\",\"line\":721},{\"file\":\"\\/Users\\/yannrobin\\/Documents\\/development\\/ixp\\/IXP-Manager\\/vendor\\/laravel\\/framework\\/src\\/Illuminate\\/Routing\\/Router.php\",\"line\":681},{\"file\":\"\\/Users\\/yannrobin\\/Documents\\/development\\/ixp\\/IXP-Manager\\/vendor\\/laravel\\/framework\\/src\\/Illuminate\\/Pipeline\\/Pipeline.php\",\"line\":130},{\"file\":\"\\/Users\\/yannrobin\\/Documents\\/development\\/ixp\\/IXP-Manager\\/app\\/Http\\/Middleware\\/AssertUserPrivilege.php\",\"line\":58},{\"file\":\"\\/Users\\/yannrobin\\/Documents\\/development\\/ixp\\/IXP-Manager\\/vendor\\/laravel\\/framework\\/src\\/Illuminate\\/Pipeline\\/Pipeline.php\",\"line\":171},{\"file\":\"\\/Users\\/yannrobin\\/Documents\\/development\\/ixp\\/IXP-Manager\\/app\\/Http\\/Middleware\\/Google2FA.php\",\"line\":74},{\"file\":\"\\/Users\\/yannrobin\\/Documents\\/development\\/ixp\\/IXP-Manager\\/vendor\\/laravel\\/framework\\/src\\/Illuminate\\/Pipeline\\/Pipeline.php\",\"line\":171},{\"file\":\"\\/Users\\/yannrobin\\/Documents\\/development\\/ixp\\/IXP-Manager\\/app\\/Http\\/Middleware\\/ControllerEnabled.php\",\"line\":96},{\"file\":\"\\/Users\\/yannrobin\\/Documents\\/development\\/ixp\\/IXP-Manager\\/vendor\\/laravel\\/framework\\/src\\/Illuminate\\/Pipeline\\/Pipeline.php\",\"line\":171},{\"file\":\"\\/Users\\/yannrobin\\/Documents\\/development\\/ixp\\/IXP-Manager\\/vendor\\/laravel\\/framework\\/src\\/Illuminate\\/Routing\\/Middleware\\/SubstituteBindings.php\",\"line\":41},{\"file\":\"\\/Users\\/yannrobin\\/Documents\\/development\\/ixp\\/IXP-Manager\\/vendor\\/laravel\\/framework\\/src\\/Illuminate\\/Pipeline\\/Pipeline.php\",\"line\":171},{\"file\":\"\\/Users\\/yannrobin\\/Documents\\/development\\/ixp\\/IXP-Manager\\/app\\/Http\\/Middleware\\/Authenticate.php\",\"line\":80},{\"file\":\"\\/Users\\/yannrobin\\/Documents\\/development\\/ixp\\/IXP-Manager\\/vendor\\/laravel\\/framework\\/src\\/Illuminate\\/Pipeline\\/Pipeline.php\",\"line\":171},{\"file\":\"\\/Users\\/yannrobin\\/Documents\\/development\\/ixp\\/IXP-Manager\\/vendor\\/laravel\\/framework\\/src\\/Illuminate\\/Foundation\\/Http\\/Middleware\\/VerifyCsrfToken.php\",\"line\":76},{\"file\":\"\\/Users\\/yannrobin\\/Documents\\/development\\/ixp\\/IXP-Manager\\/vendor\\/laravel\\/framework\\/src\\/Illuminate\\/Pipeline\\/Pipeline.php\",\"line\":171},{\"file\":\"\\/Users\\/yannrobin\\/Documents\\/development\\/ixp\\/IXP-Manager\\/vendor\\/laravel\\/framework\\/src\\/Illuminate\\/View\\/Middleware\\/ShareErrorsFromSession.php\",\"line\":49},{\"file\":\"\\/Users\\/yannrobin\\/Documents\\/development\\/ixp\\/IXP-Manager\\/vendor\\/laravel\\/framework\\/src\\/Illuminate\\/Pipeline\\/Pipeline.php\",\"line\":171},{\"file\":\"\\/Users\\/yannrobin\\/Documents\\/development\\/ixp\\/IXP-Manager\\/vendor\\/laravel\\/framework\\/src\\/Illuminate\\/Session\\/Middleware\\/StartSession.php\",\"line\":56},{\"file\":\"\\/Users\\/yannrobin\\/Documents\\/development\\/ixp\\/IXP-Manager\\/vendor\\/laravel\\/framework\\/src\\/Illuminate\\/Pipeline\\/Pipeline.php\",\"line\":171},{\"file\":\"\\/Users\\/yannrobin\\/Documents\\/development\\/ixp\\/IXP-Manager\\/vendor\\/laravel\\/framework\\/src\\/Illuminate\\/Cookie\\/Middleware\\/AddQueuedCookiesToResponse.php\",\"line\":37},{\"file\":\"\\/Users\\/yannrobin\\/Documents\\/development\\/ixp\\/IXP-Manager\\/vendor\\/laravel\\/framework\\/src\\/Illuminate\\/Pipeline\\/Pipeline.php\",\"line\":171},{\"file\":\"\\/Users\\/yannrobin\\/Documents\\/development\\/ixp\\/IXP-Manager\\/vendor\\/laravel\\/framework\\/src\\/Illuminate\\/Cookie\\/Middleware\\/EncryptCookies.php\",\"line\":66},{\"file\":\"\\/Users\\/yannrobin\\/Documents\\/development\\/ixp\\/IXP-Manager\\/vendor\\/laravel\\/framework\\/src\\/Illuminate\\/Pipeline\\/Pipeline.php\",\"line\":171},{\"file\":\"\\/Users\\/yannrobin\\/Documents\\/development\\/ixp\\/IXP-Manager\\/vendor\\/laravel\\/framework\\/src\\/Illuminate\\/Pipeline\\/Pipeline.php\",\"line\":105},{\"file\":\"\\/Users\\/yannrobin\\/Documents\\/development\\/ixp\\/IXP-Manager\\/vendor\\/laravel\\/framework\\/src\\/Illuminate\\/Routing\\/Router.php\",\"line\":683},{\"file\":\"\\/Users\\/yannrobin\\/Documents\\/development\\/ixp\\/IXP-Manager\\/vendor\\/laravel\\/framework\\/src\\/Illuminate\\/Routing\\/Router.php\",\"line\":658},{\"file\":\"\\/Users\\/yannrobin\\/Documents\\/development\\/ixp\\/IXP-Manager\\/vendor\\/laravel\\/framework\\/src\\/Illuminate\\/Routing\\/Router.php\",\"line\":624},{\"file\":\"\\/Users\\/yannrobin\\/Documents\\/development\\/ixp\\/IXP-Manager\\/vendor\\/laravel\\/framework\\/src\\/Illuminate\\/Routing\\/Router.php\",\"line\":613},{\"file\":\"\\/Users\\/yannrobin\\/Documents\\/development\\/ixp\\/IXP-Manager\\/vendor\\/laravel\\/framework\\/src\\/Illuminate\\/Foundation\\/Http\\/Kernel.php\",\"line\":170},{\"file\":\"\\/Users\\/yannrobin\\/Documents\\/development\\/ixp\\/IXP-Manager\\/vendor\\/laravel\\/framework\\/src\\/Illuminate\\/Pipeline\\/Pipeline.php\",\"line\":130},{\"file\":\"\\/Users\\/yannrobin\\/Documents\\/development\\/ixp\\/IXP-Manager\\/vendor\\/fideloper\\/proxy\\/src\\/TrustProxies.php\",\"line\":57},{\"file\":\"\\/Users\\/yannrobin\\/Documents\\/development\\/ixp\\/IXP-Manager\\/vendor\\/laravel\\/framework\\/src\\/Illuminate\\/Pipeline\\/Pipeline.php\",\"line\":171},{\"file\":\"\\/Users\\/yannrobin\\/Documents\\/development\\/ixp\\/IXP-Manager\\/vendor\\/laravel\\/framework\\/src\\/Illuminate\\/Foundation\\/Http\\/Middleware\\/TransformsRequest.php\",\"line\":21},{\"file\":\"\\/Users\\/yannrobin\\/Documents\\/development\\/ixp\\/IXP-Manager\\/vendor\\/laravel\\/framework\\/src\\/Illuminate\\/Pipeline\\/Pipeline.php\",\"line\":171},{\"file\":\"\\/Users\\/yannrobin\\/Documents\\/development\\/ixp\\/IXP-Manager\\/vendor\\/laravel\\/framework\\/src\\/Illuminate\\/Foundation\\/Http\\/Middleware\\/TransformsRequest.php\",\"line\":21},{\"file\":\"\\/Users\\/yannrobin\\/Documents\\/development\\/ixp\\/IXP-Manager\\/vendor\\/laravel\\/framework\\/src\\/Illuminate\\/Pipeline\\/Pipeline.php\",\"line\":171},{\"file\":\"\\/Users\\/yannrobin\\/Documents\\/development\\/ixp\\/IXP-Manager\\/vendor\\/laravel\\/framework\\/src\\/Illuminate\\/Foundation\\/Http\\/Middleware\\/ValidatePostSize.php\",\"line\":27},{\"file\":\"\\/Users\\/yannrobin\\/Documents\\/development\\/ixp\\/IXP-Manager\\/vendor\\/laravel\\/framework\\/src\\/Illuminate\\/Pipeline\\/Pipeline.php\",\"line\":171},{\"file\":\"\\/Users\\/yannrobin\\/Documents\\/development\\/ixp\\/IXP-Manager\\/vendor\\/laravel\\/framework\\/src\\/Illuminate\\/Foundation\\/Http\\/Middleware\\/CheckForMaintenanceMode.php\",\"line\":63},{\"file\":\"\\/Users\\/yannrobin\\/Documents\\/development\\/ixp\\/IXP-Manager\\/vendor\\/laravel\\/framework\\/src\\/Illuminate\\/Pipeline\\/Pipeline.php\",\"line\":171},{\"file\":\"\\/Users\\/yannrobin\\/Documents\\/development\\/ixp\\/IXP-Manager\\/vendor\\/laravel\\/framework\\/src\\/Illuminate\\/Pipeline\\/Pipeline.php\",\"line\":105},{\"file\":\"\\/Users\\/yannrobin\\/Documents\\/development\\/ixp\\/IXP-Manager\\/vendor\\/laravel\\/framework\\/src\\/Illuminate\\/Foundation\\/Http\\/Kernel.php\",\"line\":145},{\"file\":\"\\/Users\\/yannrobin\\/Documents\\/development\\/ixp\\/IXP-Manager\\/vendor\\/laravel\\/framework\\/src\\/Illuminate\\/Foundation\\/Http\\/Kernel.php\",\"line\":110},{\"file\":\"\\/Users\\/yannrobin\\/Documents\\/development\\/ixp\\/IXP-Manager\\/public\\/index.php\",\"line\":85},{\"file\":\"\\/Users\\/yannrobin\\/.composer\\/vendor\\/laravel\\/valet\\/server.php\",\"line\":158}],\"line_preview\":{\"51\":\"            case \'1062\':\",\"52\":\"            case \'1557\':\",\"53\":\"            case \'1569\':\",\"54\":\"            case \'1586\':\",\"55\":\"                return new Exception\\\\UniqueConstraintViolationException($message, $exception);\",\"56\":\"\",\"57\":\"            case \'1054\':\",\"58\":\"            case \'1166\':\",\"59\":\"            case \'1611\':\",\"60\":\"                return new Exception\\\\InvalidFieldNameException($message, $exception);\",\"61\":\"\",\"62\":\"            case \'1052\':\",\"63\":\"            case \'1060\':\",\"64\":\"            case \'1110\':\",\"65\":\"                return new Exception\\\\NonUniqueFieldNameException($message, $exception);\",\"66\":\"\",\"67\":\"            case \'1064\':\",\"68\":\"            case \'1149\':\",\"69\":\"            case \'1287\':\",\"70\":\"            case \'1341\':\"},\"hostname\":\"Yanns-MacBook-Pro.local\",\"user\":{\"id\":1,\"name\":null,\"email\":\"joe@siep.com\"},\"occurrences\":1}','2020-07-10 07:54:18'),(4,'9170f381-84a3-4846-8fae-95d3b6c2df10','9170f381-919e-4d66-8a2d-17f571676d7b','fe14f6e8415a436cf310f61ef353127e',0,'exception','{\"class\":\"Illuminate\\\\Database\\\\QueryException\",\"file\":\"\\/Users\\/barryo\\/dev\\/ixp-ibn\\/vendor\\/laravel\\/framework\\/src\\/Illuminate\\/Database\\/Connection.php\",\"line\":669,\"message\":\"SQLSTATE[42000]: Syntax error or access violation: 1227 Access denied; you need (at least one of) the SYSTEM_USER privilege(s) for this operation (SQL: -- Views and triggers used on the IXP Manager database\\n\\n-- view: view_cust_current_active\\n--\\n-- This is used to pick up all currently active members.  This can further \\n-- be refined by checking for customer type.\\n\\nDROP VIEW IF EXISTS view_cust_current_active;\\nCREATE VIEW view_cust_current_active AS\\n\\tSELECT * FROM cust cu\\n\\tWHERE\\n\\t\\tcu.datejoin  <= CURDATE()\\n\\tAND\\t(\\n\\t\\t\\t( cu.dateleave IS NULL )\\n\\t\\tOR\\t( cu.dateleave < \'1970-01-01\' )\\n\\t\\tOR\\t( cu.dateleave >= CURDATE() )\\n\\t\\t)\\n\\tAND\\t(cu.status = 1 OR cu.status = 2);\\n\\n-- view: view_vlaninterface_details_by_custid\\n--\\n-- This is used to pick up all interesting details from virtualinterfaces.\\n\\nDROP VIEW IF EXISTS view_vlaninterface_details_by_custid;\\nCREATE VIEW view_vlaninterface_details_by_custid AS\\n\\tSELECT\\n        \\t`pi`.`id` AS `id`,\\n\\t\\tvi.custid,\\n\\t\\tpi.virtualinterfaceid,\\n\\t\\tpi.status,\\n\\t\\tCONCAT(vi.name,vi.channelgroup) AS virtualinterfacename,\\n\\t\\tvlan.number AS vlan,\\n\\t\\tvlan.name AS vlanname,\\n\\t\\tvlan.id AS vlanid,\\n\\t\\tvli.id AS vlaninterfaceid,\\n\\t\\tvli.ipv4enabled,\\n\\t\\tvli.ipv4hostname,\\n\\t\\tvli.ipv4canping,\\n\\t\\tvli.ipv4monitorrcbgp,\\n\\t\\tvli.ipv6enabled,\\n\\t\\tvli.ipv6hostname,\\n\\t\\tvli.ipv6canping,\\n\\t\\tvli.ipv6monitorrcbgp,\\n\\t\\tvli.as112client,\\n\\t\\tvli.mcastenabled,\\n\\t\\tvli.ipv4bgpmd5secret,\\n\\t\\tvli.ipv6bgpmd5secret,\\n\\t\\tvli.rsclient,\\n\\t\\tvli.irrdbfilter,\\n\\t\\tvli.busyhost,\\n\\t\\tvli.notes,\\n\\t\\tv4.address AS ipv4address,\\n\\t\\tv6.address AS ipv6address\\n\\tFROM\\n\\t\\tphysicalinterface pi,\\n\\t\\tvirtualinterface vi,\\n\\t\\tvlaninterface vli\\n\\tLEFT JOIN (ipv4address v4) ON vli.ipv4addressid = v4.id\\n\\tLEFT JOIN (ipv6address v6) ON vli.ipv6addressid = v6.id\\n\\tLEFT JOIN vlan ON vli.vlanid = vlan.id\\n\\tWHERE\\n\\t\\tpi.virtualinterfaceid = vi.id\\n\\tAND\\tvli.virtualinterfaceid = vi.id;\\n\\n-- view: view_switch_details_by_custid\\n--\\n-- This is used to pick up all interesting details from switches.\\n\\nDROP VIEW IF EXISTS view_switch_details_by_custid;\\nCREATE VIEW view_switch_details_by_custid AS\\n\\tSELECT\\n\\t\\tvi.id AS id,\\n\\t\\tvi.custid,\\n\\t\\tCONCAT(vi.name,vi.channelgroup) AS virtualinterfacename,\\n\\t\\tpi.virtualinterfaceid,\\n\\t\\tpi.status,\\n\\t\\tpi.speed,\\n\\t\\tpi.duplex,\\n\\t\\tpi.notes,\\n\\t\\tsp.name AS switchport,\\n\\t\\tsp.id AS switchportid,\\n\\t\\tsp.ifName AS spifname,\\n\\t\\tsw.name AS switch,\\n\\t\\tsw.hostname AS switchhostname,\\n\\t\\tsw.id AS switchid,\\n\\t\\tsw.vendorid,\\n\\t\\tsw.snmppasswd,\\n\\t\\tsw.infrastructure,\\n\\t\\tca.name AS cabinet,\\n\\t\\tca.cololocation AS colocabinet,\\n\\t\\tlo.name AS locationname,\\n\\t\\tlo.shortname AS locationshortname\\n\\tFROM\\n\\t\\tvirtualinterface vi,\\n\\t\\tphysicalinterface pi,\\n\\t\\tswitchport sp,\\n\\t\\tswitch sw,\\n\\t\\tcabinet ca,\\n\\t\\tlocation lo\\n\\tWHERE\\n\\t\\tpi.virtualinterfaceid = vi.id\\n\\tAND\\tpi.switchportid = sp.id\\n\\tAND\\tsp.switchid = sw.id\\n\\tAND\\tsw.cabinetid = ca.id\\n\\tAND\\tca.locationid = lo.id;\\n\\n\\n\\n-- trigger: bgp_sessions_update\\n--\\n-- This is used to update a n^2 table showing who peers with whom\\n\\n\\nDROP TRIGGER IF EXISTS `bgp_sessions_update`;\\n\\nDELIMITER ;;\\n\\nCREATE TRIGGER bgp_sessions_update AFTER INSERT ON `bgpsessiondata` FOR EACH ROW\\n\\n\\tBEGIN\\n\\n\\t\\tIF NOT EXISTS ( SELECT 1 FROM bgp_sessions WHERE srcipaddressid = NEW.srcipaddressid AND protocol = NEW.protocol AND dstipaddressid = NEW.dstipaddressid ) THEN\\n\\t\\t\\tINSERT INTO bgp_sessions\\n\\t\\t\\t\\t( srcipaddressid, protocol, dstipaddressid, packetcount, last_seen, source )\\n\\t\\t\\tVALUES\\n\\t\\t\\t\\t( NEW.srcipaddressid, NEW.protocol, NEW.dstipaddressid, NEW.packetcount, NOW(), NEW.source );\\n\\t\\tELSE\\n\\t\\t\\tUPDATE bgp_sessions SET\\n\\t\\t\\t\\tlast_seen   = NOW(),\\n\\t\\t\\t\\tpacketcount = packetcount + NEW.packetcount\\n\\t\\t\\tWHERE\\n\\t\\t\\t\\tsrcipaddressid = NEW.srcipaddressid AND protocol = NEW.protocol AND dstipaddressid = NEW.dstipaddressid;\\n\\t\\tEND IF;\\n\\n\\t\\tIF NOT EXISTS ( SELECT 1 FROM bgp_sessions WHERE dstipaddressid = NEW.srcipaddressid AND protocol = NEW.protocol AND srcipaddressid = NEW.dstipaddressid ) THEN\\n\\t\\t\\tINSERT INTO bgp_sessions\\n\\t\\t\\t\\t( srcipaddressid, protocol, dstipaddressid, packetcount, last_seen, source )\\n\\t\\t\\tVALUES\\n\\t\\t\\t\\t( NEW.dstipaddressid, NEW.protocol, NEW.srcipaddressid, NEW.packetcount, NOW(), NEW.source );\\n\\t\\tELSE\\n\\t\\t\\tUPDATE bgp_sessions SET\\n\\t\\t\\t\\tlast_seen   = NOW(),\\n\\t\\t\\t\\tpacketcount = packetcount + NEW.packetcount\\n\\t\\t\\tWHERE\\n\\t\\t\\t\\tdstipaddressid = NEW.srcipaddressid AND protocol = NEW.protocol AND srcipaddressid = NEW.dstipaddressid;\\n\\t\\tEND IF;\\n\\n\\tEND ;;\\n\\nDELIMITER ;\\n)\",\"trace\":[{\"file\":\"\\/Users\\/barryo\\/dev\\/ixp-ibn\\/vendor\\/laravel\\/framework\\/src\\/Illuminate\\/Database\\/Connection.php\",\"line\":629},{\"file\":\"\\/Users\\/barryo\\/dev\\/ixp-ibn\\/vendor\\/laravel\\/framework\\/src\\/Illuminate\\/Database\\/Connection.php\",\"line\":516},{\"file\":\"\\/Users\\/barryo\\/dev\\/ixp-ibn\\/vendor\\/laravel\\/framework\\/src\\/Illuminate\\/Database\\/DatabaseManager.php\",\"line\":349},{\"file\":\"\\/Users\\/barryo\\/dev\\/ixp-ibn\\/vendor\\/laravel\\/framework\\/src\\/Illuminate\\/Support\\/Facades\\/Facade.php\",\"line\":261},{\"file\":\"\\/Users\\/barryo\\/dev\\/ixp-ibn\\/app\\/Console\\/Commands\\/Upgrade\\/ResetMysqlViews.php\",\"line\":67},[],{\"file\":\"\\/Users\\/barryo\\/dev\\/ixp-ibn\\/vendor\\/laravel\\/framework\\/src\\/Illuminate\\/Container\\/BoundMethod.php\",\"line\":32},{\"file\":\"\\/Users\\/barryo\\/dev\\/ixp-ibn\\/vendor\\/laravel\\/framework\\/src\\/Illuminate\\/Container\\/Util.php\",\"line\":36},{\"file\":\"\\/Users\\/barryo\\/dev\\/ixp-ibn\\/vendor\\/laravel\\/framework\\/src\\/Illuminate\\/Container\\/BoundMethod.php\",\"line\":90},{\"file\":\"\\/Users\\/barryo\\/dev\\/ixp-ibn\\/vendor\\/laravel\\/framework\\/src\\/Illuminate\\/Container\\/BoundMethod.php\",\"line\":34},{\"file\":\"\\/Users\\/barryo\\/dev\\/ixp-ibn\\/vendor\\/laravel\\/framework\\/src\\/Illuminate\\/Container\\/Container.php\",\"line\":590},{\"file\":\"\\/Users\\/barryo\\/dev\\/ixp-ibn\\/vendor\\/laravel\\/framework\\/src\\/Illuminate\\/Console\\/Command.php\",\"line\":134},{\"file\":\"\\/Users\\/barryo\\/dev\\/ixp-ibn\\/vendor\\/symfony\\/console\\/Command\\/Command.php\",\"line\":255},{\"file\":\"\\/Users\\/barryo\\/dev\\/ixp-ibn\\/vendor\\/laravel\\/framework\\/src\\/Illuminate\\/Console\\/Command.php\",\"line\":121},{\"file\":\"\\/Users\\/barryo\\/dev\\/ixp-ibn\\/vendor\\/symfony\\/console\\/Application.php\",\"line\":1001},{\"file\":\"\\/Users\\/barryo\\/dev\\/ixp-ibn\\/vendor\\/symfony\\/console\\/Application.php\",\"line\":271},{\"file\":\"\\/Users\\/barryo\\/dev\\/ixp-ibn\\/vendor\\/symfony\\/console\\/Application.php\",\"line\":147},{\"file\":\"\\/Users\\/barryo\\/dev\\/ixp-ibn\\/vendor\\/laravel\\/framework\\/src\\/Illuminate\\/Console\\/Application.php\",\"line\":93},{\"file\":\"\\/Users\\/barryo\\/dev\\/ixp-ibn\\/vendor\\/laravel\\/framework\\/src\\/Illuminate\\/Foundation\\/Console\\/Kernel.php\",\"line\":131},{\"file\":\"\\/Users\\/barryo\\/dev\\/ixp-ibn\\/artisan\",\"line\":37}],\"line_preview\":{\"660\":\"        \\/\\/ took to execute and log the query SQL, bindings and time in our memory.\",\"661\":\"        try {\",\"662\":\"            $result = $callback($query, $bindings);\",\"663\":\"        }\",\"664\":\"\",\"665\":\"        \\/\\/ If an exception occurs when attempting to run a query, we\'ll format the error\",\"666\":\"        \\/\\/ message to include the bindings with SQL, which will make this exception a\",\"667\":\"        \\/\\/ lot more helpful to the developer instead of just the database\'s errors.\",\"668\":\"        catch (Exception $e) {\",\"669\":\"            throw new QueryException(\",\"670\":\"                $query, $this->prepareBindings($bindings), $e\",\"671\":\"            );\",\"672\":\"        }\",\"673\":\"\",\"674\":\"        return $result;\",\"675\":\"    }\",\"676\":\"\",\"677\":\"    \\/**\",\"678\":\"     * Log a query in the connection\'s query log.\",\"679\":\"     *\"},\"hostname\":\"Barrys-MacBook-Pro.local\",\"occurrences\":1}','2020-09-03 15:24:37'),(5,'9170f401-79d3-4043-bb31-c7d41f88875f','9170f401-85e8-440a-9603-a86b7e6509dd','fe14f6e8415a436cf310f61ef353127e',0,'exception','{\"class\":\"Illuminate\\\\Database\\\\QueryException\",\"file\":\"\\/Users\\/barryo\\/dev\\/ixp-ibn\\/vendor\\/laravel\\/framework\\/src\\/Illuminate\\/Database\\/Connection.php\",\"line\":669,\"message\":\"SQLSTATE[42000]: Syntax error or access violation: 1227 Access denied; you need (at least one of) the SYSTEM_USER privilege(s) for this operation (SQL: -- Views and triggers used on the IXP Manager database\\n\\n-- view: view_cust_current_active\\n--\\n-- This is used to pick up all currently active members.  This can further \\n-- be refined by checking for customer type.\\n\\nDROP VIEW IF EXISTS view_cust_current_active;\\nCREATE VIEW view_cust_current_active AS\\n\\tSELECT * FROM cust cu\\n\\tWHERE\\n\\t\\tcu.datejoin  <= CURDATE()\\n\\tAND\\t(\\n\\t\\t\\t( cu.dateleave IS NULL )\\n\\t\\tOR\\t( cu.dateleave < \'1970-01-01\' )\\n\\t\\tOR\\t( cu.dateleave >= CURDATE() )\\n\\t\\t)\\n\\tAND\\t(cu.status = 1 OR cu.status = 2);\\n\\n-- view: view_vlaninterface_details_by_custid\\n--\\n-- This is used to pick up all interesting details from virtualinterfaces.\\n\\nDROP VIEW IF EXISTS view_vlaninterface_details_by_custid;\\nCREATE VIEW view_vlaninterface_details_by_custid AS\\n\\tSELECT\\n        \\t`pi`.`id` AS `id`,\\n\\t\\tvi.custid,\\n\\t\\tpi.virtualinterfaceid,\\n\\t\\tpi.status,\\n\\t\\tCONCAT(vi.name,vi.channelgroup) AS virtualinterfacename,\\n\\t\\tvlan.number AS vlan,\\n\\t\\tvlan.name AS vlanname,\\n\\t\\tvlan.id AS vlanid,\\n\\t\\tvli.id AS vlaninterfaceid,\\n\\t\\tvli.ipv4enabled,\\n\\t\\tvli.ipv4hostname,\\n\\t\\tvli.ipv4canping,\\n\\t\\tvli.ipv4monitorrcbgp,\\n\\t\\tvli.ipv6enabled,\\n\\t\\tvli.ipv6hostname,\\n\\t\\tvli.ipv6canping,\\n\\t\\tvli.ipv6monitorrcbgp,\\n\\t\\tvli.as112client,\\n\\t\\tvli.mcastenabled,\\n\\t\\tvli.ipv4bgpmd5secret,\\n\\t\\tvli.ipv6bgpmd5secret,\\n\\t\\tvli.rsclient,\\n\\t\\tvli.irrdbfilter,\\n\\t\\tvli.busyhost,\\n\\t\\tvli.notes,\\n\\t\\tv4.address AS ipv4address,\\n\\t\\tv6.address AS ipv6address\\n\\tFROM\\n\\t\\tphysicalinterface pi,\\n\\t\\tvirtualinterface vi,\\n\\t\\tvlaninterface vli\\n\\tLEFT JOIN (ipv4address v4) ON vli.ipv4addressid = v4.id\\n\\tLEFT JOIN (ipv6address v6) ON vli.ipv6addressid = v6.id\\n\\tLEFT JOIN vlan ON vli.vlanid = vlan.id\\n\\tWHERE\\n\\t\\tpi.virtualinterfaceid = vi.id\\n\\tAND\\tvli.virtualinterfaceid = vi.id;\\n\\n-- view: view_switch_details_by_custid\\n--\\n-- This is used to pick up all interesting details from switches.\\n\\nDROP VIEW IF EXISTS view_switch_details_by_custid;\\nCREATE VIEW view_switch_details_by_custid AS\\n\\tSELECT\\n\\t\\tvi.id AS id,\\n\\t\\tvi.custid,\\n\\t\\tCONCAT(vi.name,vi.channelgroup) AS virtualinterfacename,\\n\\t\\tpi.virtualinterfaceid,\\n\\t\\tpi.status,\\n\\t\\tpi.speed,\\n\\t\\tpi.duplex,\\n\\t\\tpi.notes,\\n\\t\\tsp.name AS switchport,\\n\\t\\tsp.id AS switchportid,\\n\\t\\tsp.ifName AS spifname,\\n\\t\\tsw.name AS switch,\\n\\t\\tsw.hostname AS switchhostname,\\n\\t\\tsw.id AS switchid,\\n\\t\\tsw.vendorid,\\n\\t\\tsw.snmppasswd,\\n\\t\\tsw.infrastructure,\\n\\t\\tca.name AS cabinet,\\n\\t\\tca.cololocation AS colocabinet,\\n\\t\\tlo.name AS locationname,\\n\\t\\tlo.shortname AS locationshortname\\n\\tFROM\\n\\t\\tvirtualinterface vi,\\n\\t\\tphysicalinterface pi,\\n\\t\\tswitchport sp,\\n\\t\\tswitch sw,\\n\\t\\tcabinet ca,\\n\\t\\tlocation lo\\n\\tWHERE\\n\\t\\tpi.virtualinterfaceid = vi.id\\n\\tAND\\tpi.switchportid = sp.id\\n\\tAND\\tsp.switchid = sw.id\\n\\tAND\\tsw.cabinetid = ca.id\\n\\tAND\\tca.locationid = lo.id;\\n\\n\\n\\n-- trigger: bgp_sessions_update\\n--\\n-- This is used to update a n^2 table showing who peers with whom\\n\\n\\nDROP TRIGGER IF EXISTS `bgp_sessions_update`;\\n\\nDELIMITER ;;\\n\\nCREATE TRIGGER bgp_sessions_update AFTER INSERT ON `bgpsessiondata` FOR EACH ROW\\n\\n\\tBEGIN\\n\\n\\t\\tIF NOT EXISTS ( SELECT 1 FROM bgp_sessions WHERE srcipaddressid = NEW.srcipaddressid AND protocol = NEW.protocol AND dstipaddressid = NEW.dstipaddressid ) THEN\\n\\t\\t\\tINSERT INTO bgp_sessions\\n\\t\\t\\t\\t( srcipaddressid, protocol, dstipaddressid, packetcount, last_seen, source )\\n\\t\\t\\tVALUES\\n\\t\\t\\t\\t( NEW.srcipaddressid, NEW.protocol, NEW.dstipaddressid, NEW.packetcount, NOW(), NEW.source );\\n\\t\\tELSE\\n\\t\\t\\tUPDATE bgp_sessions SET\\n\\t\\t\\t\\tlast_seen   = NOW(),\\n\\t\\t\\t\\tpacketcount = packetcount + NEW.packetcount\\n\\t\\t\\tWHERE\\n\\t\\t\\t\\tsrcipaddressid = NEW.srcipaddressid AND protocol = NEW.protocol AND dstipaddressid = NEW.dstipaddressid;\\n\\t\\tEND IF;\\n\\n\\t\\tIF NOT EXISTS ( SELECT 1 FROM bgp_sessions WHERE dstipaddressid = NEW.srcipaddressid AND protocol = NEW.protocol AND srcipaddressid = NEW.dstipaddressid ) THEN\\n\\t\\t\\tINSERT INTO bgp_sessions\\n\\t\\t\\t\\t( srcipaddressid, protocol, dstipaddressid, packetcount, last_seen, source )\\n\\t\\t\\tVALUES\\n\\t\\t\\t\\t( NEW.dstipaddressid, NEW.protocol, NEW.srcipaddressid, NEW.packetcount, NOW(), NEW.source );\\n\\t\\tELSE\\n\\t\\t\\tUPDATE bgp_sessions SET\\n\\t\\t\\t\\tlast_seen   = NOW(),\\n\\t\\t\\t\\tpacketcount = packetcount + NEW.packetcount\\n\\t\\t\\tWHERE\\n\\t\\t\\t\\tdstipaddressid = NEW.srcipaddressid AND protocol = NEW.protocol AND srcipaddressid = NEW.dstipaddressid;\\n\\t\\tEND IF;\\n\\n\\tEND ;;\\n\\nDELIMITER ;\\n)\",\"trace\":[{\"file\":\"\\/Users\\/barryo\\/dev\\/ixp-ibn\\/vendor\\/laravel\\/framework\\/src\\/Illuminate\\/Database\\/Connection.php\",\"line\":629},{\"file\":\"\\/Users\\/barryo\\/dev\\/ixp-ibn\\/vendor\\/laravel\\/framework\\/src\\/Illuminate\\/Database\\/Connection.php\",\"line\":516},{\"file\":\"\\/Users\\/barryo\\/dev\\/ixp-ibn\\/vendor\\/laravel\\/framework\\/src\\/Illuminate\\/Database\\/DatabaseManager.php\",\"line\":349},{\"file\":\"\\/Users\\/barryo\\/dev\\/ixp-ibn\\/vendor\\/laravel\\/framework\\/src\\/Illuminate\\/Support\\/Facades\\/Facade.php\",\"line\":261},{\"file\":\"\\/Users\\/barryo\\/dev\\/ixp-ibn\\/app\\/Console\\/Commands\\/Upgrade\\/ResetMysqlViews.php\",\"line\":67},[],{\"file\":\"\\/Users\\/barryo\\/dev\\/ixp-ibn\\/vendor\\/laravel\\/framework\\/src\\/Illuminate\\/Container\\/BoundMethod.php\",\"line\":32},{\"file\":\"\\/Users\\/barryo\\/dev\\/ixp-ibn\\/vendor\\/laravel\\/framework\\/src\\/Illuminate\\/Container\\/Util.php\",\"line\":36},{\"file\":\"\\/Users\\/barryo\\/dev\\/ixp-ibn\\/vendor\\/laravel\\/framework\\/src\\/Illuminate\\/Container\\/BoundMethod.php\",\"line\":90},{\"file\":\"\\/Users\\/barryo\\/dev\\/ixp-ibn\\/vendor\\/laravel\\/framework\\/src\\/Illuminate\\/Container\\/BoundMethod.php\",\"line\":34},{\"file\":\"\\/Users\\/barryo\\/dev\\/ixp-ibn\\/vendor\\/laravel\\/framework\\/src\\/Illuminate\\/Container\\/Container.php\",\"line\":590},{\"file\":\"\\/Users\\/barryo\\/dev\\/ixp-ibn\\/vendor\\/laravel\\/framework\\/src\\/Illuminate\\/Console\\/Command.php\",\"line\":134},{\"file\":\"\\/Users\\/barryo\\/dev\\/ixp-ibn\\/vendor\\/symfony\\/console\\/Command\\/Command.php\",\"line\":255},{\"file\":\"\\/Users\\/barryo\\/dev\\/ixp-ibn\\/vendor\\/laravel\\/framework\\/src\\/Illuminate\\/Console\\/Command.php\",\"line\":121},{\"file\":\"\\/Users\\/barryo\\/dev\\/ixp-ibn\\/vendor\\/symfony\\/console\\/Application.php\",\"line\":1001},{\"file\":\"\\/Users\\/barryo\\/dev\\/ixp-ibn\\/vendor\\/symfony\\/console\\/Application.php\",\"line\":271},{\"file\":\"\\/Users\\/barryo\\/dev\\/ixp-ibn\\/vendor\\/symfony\\/console\\/Application.php\",\"line\":147},{\"file\":\"\\/Users\\/barryo\\/dev\\/ixp-ibn\\/vendor\\/laravel\\/framework\\/src\\/Illuminate\\/Console\\/Application.php\",\"line\":93},{\"file\":\"\\/Users\\/barryo\\/dev\\/ixp-ibn\\/vendor\\/laravel\\/framework\\/src\\/Illuminate\\/Foundation\\/Console\\/Kernel.php\",\"line\":131},{\"file\":\"\\/Users\\/barryo\\/dev\\/ixp-ibn\\/artisan\",\"line\":37}],\"line_preview\":{\"660\":\"        \\/\\/ took to execute and log the query SQL, bindings and time in our memory.\",\"661\":\"        try {\",\"662\":\"            $result = $callback($query, $bindings);\",\"663\":\"        }\",\"664\":\"\",\"665\":\"        \\/\\/ If an exception occurs when attempting to run a query, we\'ll format the error\",\"666\":\"        \\/\\/ message to include the bindings with SQL, which will make this exception a\",\"667\":\"        \\/\\/ lot more helpful to the developer instead of just the database\'s errors.\",\"668\":\"        catch (Exception $e) {\",\"669\":\"            throw new QueryException(\",\"670\":\"                $query, $this->prepareBindings($bindings), $e\",\"671\":\"            );\",\"672\":\"        }\",\"673\":\"\",\"674\":\"        return $result;\",\"675\":\"    }\",\"676\":\"\",\"677\":\"    \\/**\",\"678\":\"     * Log a query in the connection\'s query log.\",\"679\":\"     *\"},\"hostname\":\"Barrys-MacBook-Pro.local\",\"occurrences\":2}','2020-09-03 15:26:01'),(6,'9170f424-a432-4324-8b5e-375dc233029b','9170f424-ae1f-4af9-bb87-27aa90175327','fe14f6e8415a436cf310f61ef353127e',0,'exception','{\"class\":\"Illuminate\\\\Database\\\\QueryException\",\"file\":\"\\/Users\\/barryo\\/dev\\/ixp-ibn\\/vendor\\/laravel\\/framework\\/src\\/Illuminate\\/Database\\/Connection.php\",\"line\":669,\"message\":\"SQLSTATE[42000]: Syntax error or access violation: 1227 Access denied; you need (at least one of) the SYSTEM_USER privilege(s) for this operation (SQL: -- Views and triggers used on the IXP Manager database\\n\\n-- view: view_cust_current_active\\n--\\n-- This is used to pick up all currently active members.  This can further \\n-- be refined by checking for customer type.\\n\\nDROP VIEW IF EXISTS view_cust_current_active;\\nCREATE VIEW view_cust_current_active AS\\n\\tSELECT * FROM cust cu\\n\\tWHERE\\n\\t\\tcu.datejoin  <= CURDATE()\\n\\tAND\\t(\\n\\t\\t\\t( cu.dateleave IS NULL )\\n\\t\\tOR\\t( cu.dateleave < \'1970-01-01\' )\\n\\t\\tOR\\t( cu.dateleave >= CURDATE() )\\n\\t\\t)\\n\\tAND\\t(cu.status = 1 OR cu.status = 2);\\n\\n-- view: view_vlaninterface_details_by_custid\\n--\\n-- This is used to pick up all interesting details from virtualinterfaces.\\n\\nDROP VIEW IF EXISTS view_vlaninterface_details_by_custid;\\nCREATE VIEW view_vlaninterface_details_by_custid AS\\n\\tSELECT\\n        \\t`pi`.`id` AS `id`,\\n\\t\\tvi.custid,\\n\\t\\tpi.virtualinterfaceid,\\n\\t\\tpi.status,\\n\\t\\tCONCAT(vi.name,vi.channelgroup) AS virtualinterfacename,\\n\\t\\tvlan.number AS vlan,\\n\\t\\tvlan.name AS vlanname,\\n\\t\\tvlan.id AS vlanid,\\n\\t\\tvli.id AS vlaninterfaceid,\\n\\t\\tvli.ipv4enabled,\\n\\t\\tvli.ipv4hostname,\\n\\t\\tvli.ipv4canping,\\n\\t\\tvli.ipv4monitorrcbgp,\\n\\t\\tvli.ipv6enabled,\\n\\t\\tvli.ipv6hostname,\\n\\t\\tvli.ipv6canping,\\n\\t\\tvli.ipv6monitorrcbgp,\\n\\t\\tvli.as112client,\\n\\t\\tvli.mcastenabled,\\n\\t\\tvli.ipv4bgpmd5secret,\\n\\t\\tvli.ipv6bgpmd5secret,\\n\\t\\tvli.rsclient,\\n\\t\\tvli.irrdbfilter,\\n\\t\\tvli.busyhost,\\n\\t\\tvli.notes,\\n\\t\\tv4.address AS ipv4address,\\n\\t\\tv6.address AS ipv6address\\n\\tFROM\\n\\t\\tphysicalinterface pi,\\n\\t\\tvirtualinterface vi,\\n\\t\\tvlaninterface vli\\n\\tLEFT JOIN (ipv4address v4) ON vli.ipv4addressid = v4.id\\n\\tLEFT JOIN (ipv6address v6) ON vli.ipv6addressid = v6.id\\n\\tLEFT JOIN vlan ON vli.vlanid = vlan.id\\n\\tWHERE\\n\\t\\tpi.virtualinterfaceid = vi.id\\n\\tAND\\tvli.virtualinterfaceid = vi.id;\\n\\n-- view: view_switch_details_by_custid\\n--\\n-- This is used to pick up all interesting details from switches.\\n\\nDROP VIEW IF EXISTS view_switch_details_by_custid;\\nCREATE VIEW view_switch_details_by_custid AS\\n\\tSELECT\\n\\t\\tvi.id AS id,\\n\\t\\tvi.custid,\\n\\t\\tCONCAT(vi.name,vi.channelgroup) AS virtualinterfacename,\\n\\t\\tpi.virtualinterfaceid,\\n\\t\\tpi.status,\\n\\t\\tpi.speed,\\n\\t\\tpi.duplex,\\n\\t\\tpi.notes,\\n\\t\\tsp.name AS switchport,\\n\\t\\tsp.id AS switchportid,\\n\\t\\tsp.ifName AS spifname,\\n\\t\\tsw.name AS switch,\\n\\t\\tsw.hostname AS switchhostname,\\n\\t\\tsw.id AS switchid,\\n\\t\\tsw.vendorid,\\n\\t\\tsw.snmppasswd,\\n\\t\\tsw.infrastructure,\\n\\t\\tca.name AS cabinet,\\n\\t\\tca.cololocation AS colocabinet,\\n\\t\\tlo.name AS locationname,\\n\\t\\tlo.shortname AS locationshortname\\n\\tFROM\\n\\t\\tvirtualinterface vi,\\n\\t\\tphysicalinterface pi,\\n\\t\\tswitchport sp,\\n\\t\\tswitch sw,\\n\\t\\tcabinet ca,\\n\\t\\tlocation lo\\n\\tWHERE\\n\\t\\tpi.virtualinterfaceid = vi.id\\n\\tAND\\tpi.switchportid = sp.id\\n\\tAND\\tsp.switchid = sw.id\\n\\tAND\\tsw.cabinetid = ca.id\\n\\tAND\\tca.locationid = lo.id;\\n\\n\\n\\n-- trigger: bgp_sessions_update\\n--\\n-- This is used to update a n^2 table showing who peers with whom\\n\\n\\nDROP TRIGGER IF EXISTS `bgp_sessions_update`;\\n\\nDELIMITER ;;\\n\\nCREATE TRIGGER bgp_sessions_update AFTER INSERT ON `bgpsessiondata` FOR EACH ROW\\n\\n\\tBEGIN\\n\\n\\t\\tIF NOT EXISTS ( SELECT 1 FROM bgp_sessions WHERE srcipaddressid = NEW.srcipaddressid AND protocol = NEW.protocol AND dstipaddressid = NEW.dstipaddressid ) THEN\\n\\t\\t\\tINSERT INTO bgp_sessions\\n\\t\\t\\t\\t( srcipaddressid, protocol, dstipaddressid, packetcount, last_seen, source )\\n\\t\\t\\tVALUES\\n\\t\\t\\t\\t( NEW.srcipaddressid, NEW.protocol, NEW.dstipaddressid, NEW.packetcount, NOW(), NEW.source );\\n\\t\\tELSE\\n\\t\\t\\tUPDATE bgp_sessions SET\\n\\t\\t\\t\\tlast_seen   = NOW(),\\n\\t\\t\\t\\tpacketcount = packetcount + NEW.packetcount\\n\\t\\t\\tWHERE\\n\\t\\t\\t\\tsrcipaddressid = NEW.srcipaddressid AND protocol = NEW.protocol AND dstipaddressid = NEW.dstipaddressid;\\n\\t\\tEND IF;\\n\\n\\t\\tIF NOT EXISTS ( SELECT 1 FROM bgp_sessions WHERE dstipaddressid = NEW.srcipaddressid AND protocol = NEW.protocol AND srcipaddressid = NEW.dstipaddressid ) THEN\\n\\t\\t\\tINSERT INTO bgp_sessions\\n\\t\\t\\t\\t( srcipaddressid, protocol, dstipaddressid, packetcount, last_seen, source )\\n\\t\\t\\tVALUES\\n\\t\\t\\t\\t( NEW.dstipaddressid, NEW.protocol, NEW.srcipaddressid, NEW.packetcount, NOW(), NEW.source );\\n\\t\\tELSE\\n\\t\\t\\tUPDATE bgp_sessions SET\\n\\t\\t\\t\\tlast_seen   = NOW(),\\n\\t\\t\\t\\tpacketcount = packetcount + NEW.packetcount\\n\\t\\t\\tWHERE\\n\\t\\t\\t\\tdstipaddressid = NEW.srcipaddressid AND protocol = NEW.protocol AND srcipaddressid = NEW.dstipaddressid;\\n\\t\\tEND IF;\\n\\n\\tEND ;;\\n\\nDELIMITER ;\\n)\",\"trace\":[{\"file\":\"\\/Users\\/barryo\\/dev\\/ixp-ibn\\/vendor\\/laravel\\/framework\\/src\\/Illuminate\\/Database\\/Connection.php\",\"line\":629},{\"file\":\"\\/Users\\/barryo\\/dev\\/ixp-ibn\\/vendor\\/laravel\\/framework\\/src\\/Illuminate\\/Database\\/Connection.php\",\"line\":516},{\"file\":\"\\/Users\\/barryo\\/dev\\/ixp-ibn\\/vendor\\/laravel\\/framework\\/src\\/Illuminate\\/Database\\/DatabaseManager.php\",\"line\":349},{\"file\":\"\\/Users\\/barryo\\/dev\\/ixp-ibn\\/vendor\\/laravel\\/framework\\/src\\/Illuminate\\/Support\\/Facades\\/Facade.php\",\"line\":261},{\"file\":\"\\/Users\\/barryo\\/dev\\/ixp-ibn\\/app\\/Console\\/Commands\\/Upgrade\\/ResetMysqlViews.php\",\"line\":67},[],{\"file\":\"\\/Users\\/barryo\\/dev\\/ixp-ibn\\/vendor\\/laravel\\/framework\\/src\\/Illuminate\\/Container\\/BoundMethod.php\",\"line\":32},{\"file\":\"\\/Users\\/barryo\\/dev\\/ixp-ibn\\/vendor\\/laravel\\/framework\\/src\\/Illuminate\\/Container\\/Util.php\",\"line\":36},{\"file\":\"\\/Users\\/barryo\\/dev\\/ixp-ibn\\/vendor\\/laravel\\/framework\\/src\\/Illuminate\\/Container\\/BoundMethod.php\",\"line\":90},{\"file\":\"\\/Users\\/barryo\\/dev\\/ixp-ibn\\/vendor\\/laravel\\/framework\\/src\\/Illuminate\\/Container\\/BoundMethod.php\",\"line\":34},{\"file\":\"\\/Users\\/barryo\\/dev\\/ixp-ibn\\/vendor\\/laravel\\/framework\\/src\\/Illuminate\\/Container\\/Container.php\",\"line\":590},{\"file\":\"\\/Users\\/barryo\\/dev\\/ixp-ibn\\/vendor\\/laravel\\/framework\\/src\\/Illuminate\\/Console\\/Command.php\",\"line\":134},{\"file\":\"\\/Users\\/barryo\\/dev\\/ixp-ibn\\/vendor\\/symfony\\/console\\/Command\\/Command.php\",\"line\":255},{\"file\":\"\\/Users\\/barryo\\/dev\\/ixp-ibn\\/vendor\\/laravel\\/framework\\/src\\/Illuminate\\/Console\\/Command.php\",\"line\":121},{\"file\":\"\\/Users\\/barryo\\/dev\\/ixp-ibn\\/vendor\\/symfony\\/console\\/Application.php\",\"line\":1001},{\"file\":\"\\/Users\\/barryo\\/dev\\/ixp-ibn\\/vendor\\/symfony\\/console\\/Application.php\",\"line\":271},{\"file\":\"\\/Users\\/barryo\\/dev\\/ixp-ibn\\/vendor\\/symfony\\/console\\/Application.php\",\"line\":147},{\"file\":\"\\/Users\\/barryo\\/dev\\/ixp-ibn\\/vendor\\/laravel\\/framework\\/src\\/Illuminate\\/Console\\/Application.php\",\"line\":93},{\"file\":\"\\/Users\\/barryo\\/dev\\/ixp-ibn\\/vendor\\/laravel\\/framework\\/src\\/Illuminate\\/Foundation\\/Console\\/Kernel.php\",\"line\":131},{\"file\":\"\\/Users\\/barryo\\/dev\\/ixp-ibn\\/artisan\",\"line\":37}],\"line_preview\":{\"660\":\"        \\/\\/ took to execute and log the query SQL, bindings and time in our memory.\",\"661\":\"        try {\",\"662\":\"            $result = $callback($query, $bindings);\",\"663\":\"        }\",\"664\":\"\",\"665\":\"        \\/\\/ If an exception occurs when attempting to run a query, we\'ll format the error\",\"666\":\"        \\/\\/ message to include the bindings with SQL, which will make this exception a\",\"667\":\"        \\/\\/ lot more helpful to the developer instead of just the database\'s errors.\",\"668\":\"        catch (Exception $e) {\",\"669\":\"            throw new QueryException(\",\"670\":\"                $query, $this->prepareBindings($bindings), $e\",\"671\":\"            );\",\"672\":\"        }\",\"673\":\"\",\"674\":\"        return $result;\",\"675\":\"    }\",\"676\":\"\",\"677\":\"    \\/**\",\"678\":\"     * Log a query in the connection\'s query log.\",\"679\":\"     *\"},\"hostname\":\"Barrys-MacBook-Pro.local\",\"occurrences\":3}','2020-09-03 15:26:24'),(7,'9170f484-e2f4-4b89-9eb8-61091d0df78a','9170f484-ec7a-43cb-b5a3-1adda015d112','fe14f6e8415a436cf310f61ef353127e',0,'exception','{\"class\":\"Illuminate\\\\Database\\\\QueryException\",\"file\":\"\\/Users\\/barryo\\/dev\\/ixp-ibn\\/vendor\\/laravel\\/framework\\/src\\/Illuminate\\/Database\\/Connection.php\",\"line\":669,\"message\":\"SQLSTATE[42000]: Syntax error or access violation: 1227 Access denied; you need (at least one of) the SYSTEM_USER privilege(s) for this operation (SQL: -- Views and triggers used on the IXP Manager database\\n\\n-- view: view_cust_current_active\\n--\\n-- This is used to pick up all currently active members.  This can further \\n-- be refined by checking for customer type.\\n\\nDROP VIEW IF EXISTS view_cust_current_active;\\nCREATE VIEW view_cust_current_active AS\\n\\tSELECT * FROM cust cu\\n\\tWHERE\\n\\t\\tcu.datejoin  <= CURDATE()\\n\\tAND\\t(\\n\\t\\t\\t( cu.dateleave IS NULL )\\n\\t\\tOR\\t( cu.dateleave < \'1970-01-01\' )\\n\\t\\tOR\\t( cu.dateleave >= CURDATE() )\\n\\t\\t)\\n\\tAND\\t(cu.status = 1 OR cu.status = 2);\\n\\n-- view: view_vlaninterface_details_by_custid\\n--\\n-- This is used to pick up all interesting details from virtualinterfaces.\\n\\nDROP VIEW IF EXISTS view_vlaninterface_details_by_custid;\\nCREATE VIEW view_vlaninterface_details_by_custid AS\\n\\tSELECT\\n        \\t`pi`.`id` AS `id`,\\n\\t\\tvi.custid,\\n\\t\\tpi.virtualinterfaceid,\\n\\t\\tpi.status,\\n\\t\\tCONCAT(vi.name,vi.channelgroup) AS virtualinterfacename,\\n\\t\\tvlan.number AS vlan,\\n\\t\\tvlan.name AS vlanname,\\n\\t\\tvlan.id AS vlanid,\\n\\t\\tvli.id AS vlaninterfaceid,\\n\\t\\tvli.ipv4enabled,\\n\\t\\tvli.ipv4hostname,\\n\\t\\tvli.ipv4canping,\\n\\t\\tvli.ipv4monitorrcbgp,\\n\\t\\tvli.ipv6enabled,\\n\\t\\tvli.ipv6hostname,\\n\\t\\tvli.ipv6canping,\\n\\t\\tvli.ipv6monitorrcbgp,\\n\\t\\tvli.as112client,\\n\\t\\tvli.mcastenabled,\\n\\t\\tvli.ipv4bgpmd5secret,\\n\\t\\tvli.ipv6bgpmd5secret,\\n\\t\\tvli.rsclient,\\n\\t\\tvli.irrdbfilter,\\n\\t\\tvli.busyhost,\\n\\t\\tvli.notes,\\n\\t\\tv4.address AS ipv4address,\\n\\t\\tv6.address AS ipv6address\\n\\tFROM\\n\\t\\tphysicalinterface pi,\\n\\t\\tvirtualinterface vi,\\n\\t\\tvlaninterface vli\\n\\tLEFT JOIN (ipv4address v4) ON vli.ipv4addressid = v4.id\\n\\tLEFT JOIN (ipv6address v6) ON vli.ipv6addressid = v6.id\\n\\tLEFT JOIN vlan ON vli.vlanid = vlan.id\\n\\tWHERE\\n\\t\\tpi.virtualinterfaceid = vi.id\\n\\tAND\\tvli.virtualinterfaceid = vi.id;\\n\\n-- view: view_switch_details_by_custid\\n--\\n-- This is used to pick up all interesting details from switches.\\n\\nDROP VIEW IF EXISTS view_switch_details_by_custid;\\nCREATE VIEW view_switch_details_by_custid AS\\n\\tSELECT\\n\\t\\tvi.id AS id,\\n\\t\\tvi.custid,\\n\\t\\tCONCAT(vi.name,vi.channelgroup) AS virtualinterfacename,\\n\\t\\tpi.virtualinterfaceid,\\n\\t\\tpi.status,\\n\\t\\tpi.speed,\\n\\t\\tpi.duplex,\\n\\t\\tpi.notes,\\n\\t\\tsp.name AS switchport,\\n\\t\\tsp.id AS switchportid,\\n\\t\\tsp.ifName AS spifname,\\n\\t\\tsw.name AS switch,\\n\\t\\tsw.hostname AS switchhostname,\\n\\t\\tsw.id AS switchid,\\n\\t\\tsw.vendorid,\\n\\t\\tsw.snmppasswd,\\n\\t\\tsw.infrastructure,\\n\\t\\tca.name AS cabinet,\\n\\t\\tca.colocation AS colocabinet,\\n\\t\\tlo.name AS locationname,\\n\\t\\tlo.shortname AS locationshortname\\n\\tFROM\\n\\t\\tvirtualinterface vi,\\n\\t\\tphysicalinterface pi,\\n\\t\\tswitchport sp,\\n\\t\\tswitch sw,\\n\\t\\tcabinet ca,\\n\\t\\tlocation lo\\n\\tWHERE\\n\\t\\tpi.virtualinterfaceid = vi.id\\n\\tAND\\tpi.switchportid = sp.id\\n\\tAND\\tsp.switchid = sw.id\\n\\tAND\\tsw.cabinetid = ca.id\\n\\tAND\\tca.locationid = lo.id;\\n\\n\\n\\n-- trigger: bgp_sessions_update\\n--\\n-- This is used to update a n^2 table showing who peers with whom\\n\\n\\nDROP TRIGGER IF EXISTS `bgp_sessions_update`;\\n\\nDELIMITER ;;\\n\\nCREATE TRIGGER bgp_sessions_update AFTER INSERT ON `bgpsessiondata` FOR EACH ROW\\n\\n\\tBEGIN\\n\\n\\t\\tIF NOT EXISTS ( SELECT 1 FROM bgp_sessions WHERE srcipaddressid = NEW.srcipaddressid AND protocol = NEW.protocol AND dstipaddressid = NEW.dstipaddressid ) THEN\\n\\t\\t\\tINSERT INTO bgp_sessions\\n\\t\\t\\t\\t( srcipaddressid, protocol, dstipaddressid, packetcount, last_seen, source )\\n\\t\\t\\tVALUES\\n\\t\\t\\t\\t( NEW.srcipaddressid, NEW.protocol, NEW.dstipaddressid, NEW.packetcount, NOW(), NEW.source );\\n\\t\\tELSE\\n\\t\\t\\tUPDATE bgp_sessions SET\\n\\t\\t\\t\\tlast_seen   = NOW(),\\n\\t\\t\\t\\tpacketcount = packetcount + NEW.packetcount\\n\\t\\t\\tWHERE\\n\\t\\t\\t\\tsrcipaddressid = NEW.srcipaddressid AND protocol = NEW.protocol AND dstipaddressid = NEW.dstipaddressid;\\n\\t\\tEND IF;\\n\\n\\t\\tIF NOT EXISTS ( SELECT 1 FROM bgp_sessions WHERE dstipaddressid = NEW.srcipaddressid AND protocol = NEW.protocol AND srcipaddressid = NEW.dstipaddressid ) THEN\\n\\t\\t\\tINSERT INTO bgp_sessions\\n\\t\\t\\t\\t( srcipaddressid, protocol, dstipaddressid, packetcount, last_seen, source )\\n\\t\\t\\tVALUES\\n\\t\\t\\t\\t( NEW.dstipaddressid, NEW.protocol, NEW.srcipaddressid, NEW.packetcount, NOW(), NEW.source );\\n\\t\\tELSE\\n\\t\\t\\tUPDATE bgp_sessions SET\\n\\t\\t\\t\\tlast_seen   = NOW(),\\n\\t\\t\\t\\tpacketcount = packetcount + NEW.packetcount\\n\\t\\t\\tWHERE\\n\\t\\t\\t\\tdstipaddressid = NEW.srcipaddressid AND protocol = NEW.protocol AND srcipaddressid = NEW.dstipaddressid;\\n\\t\\tEND IF;\\n\\n\\tEND ;;\\n\\nDELIMITER ;\\n)\",\"trace\":[{\"file\":\"\\/Users\\/barryo\\/dev\\/ixp-ibn\\/vendor\\/laravel\\/framework\\/src\\/Illuminate\\/Database\\/Connection.php\",\"line\":629},{\"file\":\"\\/Users\\/barryo\\/dev\\/ixp-ibn\\/vendor\\/laravel\\/framework\\/src\\/Illuminate\\/Database\\/Connection.php\",\"line\":516},{\"file\":\"\\/Users\\/barryo\\/dev\\/ixp-ibn\\/vendor\\/laravel\\/framework\\/src\\/Illuminate\\/Database\\/DatabaseManager.php\",\"line\":349},{\"file\":\"\\/Users\\/barryo\\/dev\\/ixp-ibn\\/vendor\\/laravel\\/framework\\/src\\/Illuminate\\/Support\\/Facades\\/Facade.php\",\"line\":261},{\"file\":\"\\/Users\\/barryo\\/dev\\/ixp-ibn\\/app\\/Console\\/Commands\\/Upgrade\\/ResetMysqlViews.php\",\"line\":67},[],{\"file\":\"\\/Users\\/barryo\\/dev\\/ixp-ibn\\/vendor\\/laravel\\/framework\\/src\\/Illuminate\\/Container\\/BoundMethod.php\",\"line\":32},{\"file\":\"\\/Users\\/barryo\\/dev\\/ixp-ibn\\/vendor\\/laravel\\/framework\\/src\\/Illuminate\\/Container\\/Util.php\",\"line\":36},{\"file\":\"\\/Users\\/barryo\\/dev\\/ixp-ibn\\/vendor\\/laravel\\/framework\\/src\\/Illuminate\\/Container\\/BoundMethod.php\",\"line\":90},{\"file\":\"\\/Users\\/barryo\\/dev\\/ixp-ibn\\/vendor\\/laravel\\/framework\\/src\\/Illuminate\\/Container\\/BoundMethod.php\",\"line\":34},{\"file\":\"\\/Users\\/barryo\\/dev\\/ixp-ibn\\/vendor\\/laravel\\/framework\\/src\\/Illuminate\\/Container\\/Container.php\",\"line\":590},{\"file\":\"\\/Users\\/barryo\\/dev\\/ixp-ibn\\/vendor\\/laravel\\/framework\\/src\\/Illuminate\\/Console\\/Command.php\",\"line\":134},{\"file\":\"\\/Users\\/barryo\\/dev\\/ixp-ibn\\/vendor\\/symfony\\/console\\/Command\\/Command.php\",\"line\":255},{\"file\":\"\\/Users\\/barryo\\/dev\\/ixp-ibn\\/vendor\\/laravel\\/framework\\/src\\/Illuminate\\/Console\\/Command.php\",\"line\":121},{\"file\":\"\\/Users\\/barryo\\/dev\\/ixp-ibn\\/vendor\\/symfony\\/console\\/Application.php\",\"line\":1001},{\"file\":\"\\/Users\\/barryo\\/dev\\/ixp-ibn\\/vendor\\/symfony\\/console\\/Application.php\",\"line\":271},{\"file\":\"\\/Users\\/barryo\\/dev\\/ixp-ibn\\/vendor\\/symfony\\/console\\/Application.php\",\"line\":147},{\"file\":\"\\/Users\\/barryo\\/dev\\/ixp-ibn\\/vendor\\/laravel\\/framework\\/src\\/Illuminate\\/Console\\/Application.php\",\"line\":93},{\"file\":\"\\/Users\\/barryo\\/dev\\/ixp-ibn\\/vendor\\/laravel\\/framework\\/src\\/Illuminate\\/Foundation\\/Console\\/Kernel.php\",\"line\":131},{\"file\":\"\\/Users\\/barryo\\/dev\\/ixp-ibn\\/artisan\",\"line\":37}],\"line_preview\":{\"660\":\"        \\/\\/ took to execute and log the query SQL, bindings and time in our memory.\",\"661\":\"        try {\",\"662\":\"            $result = $callback($query, $bindings);\",\"663\":\"        }\",\"664\":\"\",\"665\":\"        \\/\\/ If an exception occurs when attempting to run a query, we\'ll format the error\",\"666\":\"        \\/\\/ message to include the bindings with SQL, which will make this exception a\",\"667\":\"        \\/\\/ lot more helpful to the developer instead of just the database\'s errors.\",\"668\":\"        catch (Exception $e) {\",\"669\":\"            throw new QueryException(\",\"670\":\"                $query, $this->prepareBindings($bindings), $e\",\"671\":\"            );\",\"672\":\"        }\",\"673\":\"\",\"674\":\"        return $result;\",\"675\":\"    }\",\"676\":\"\",\"677\":\"    \\/**\",\"678\":\"     * Log a query in the connection\'s query log.\",\"679\":\"     *\"},\"hostname\":\"Barrys-MacBook-Pro.local\",\"occurrences\":4}','2020-09-03 15:27:27'),(8,'9170f4d3-2f56-4797-94d6-eb02dd5a4b01','9170f4d3-3b1d-486c-8e0c-2ef663e9357f','fe14f6e8415a436cf310f61ef353127e',1,'exception','{\"class\":\"Illuminate\\\\Database\\\\QueryException\",\"file\":\"\\/Users\\/barryo\\/dev\\/ixp-ibn\\/vendor\\/laravel\\/framework\\/src\\/Illuminate\\/Database\\/Connection.php\",\"line\":669,\"message\":\"SQLSTATE[42000]: Syntax error or access violation: 1227 Access denied; you need (at least one of) the SYSTEM_USER privilege(s) for this operation (SQL: -- Views and triggers used on the IXP Manager database\\n\\n-- view: view_cust_current_active\\n--\\n-- This is used to pick up all currently active members.  This can further \\n-- be refined by checking for customer type.\\n\\nDROP VIEW IF EXISTS view_cust_current_active;\\nCREATE VIEW view_cust_current_active AS\\n\\tSELECT * FROM cust cu\\n\\tWHERE\\n\\t\\tcu.datejoin  <= CURDATE()\\n\\tAND\\t(\\n\\t\\t\\t( cu.dateleave IS NULL )\\n\\t\\tOR\\t( cu.dateleave < \'1970-01-01\' )\\n\\t\\tOR\\t( cu.dateleave >= CURDATE() )\\n\\t\\t)\\n\\tAND\\t(cu.status = 1 OR cu.status = 2);\\n\\n-- view: view_vlaninterface_details_by_custid\\n--\\n-- This is used to pick up all interesting details from virtualinterfaces.\\n\\nDROP VIEW IF EXISTS view_vlaninterface_details_by_custid;\\nCREATE VIEW view_vlaninterface_details_by_custid AS\\n\\tSELECT\\n        \\t`pi`.`id` AS `id`,\\n\\t\\tvi.custid,\\n\\t\\tpi.virtualinterfaceid,\\n\\t\\tpi.status,\\n\\t\\tCONCAT(vi.name,vi.channelgroup) AS virtualinterfacename,\\n\\t\\tvlan.number AS vlan,\\n\\t\\tvlan.name AS vlanname,\\n\\t\\tvlan.id AS vlanid,\\n\\t\\tvli.id AS vlaninterfaceid,\\n\\t\\tvli.ipv4enabled,\\n\\t\\tvli.ipv4hostname,\\n\\t\\tvli.ipv4canping,\\n\\t\\tvli.ipv4monitorrcbgp,\\n\\t\\tvli.ipv6enabled,\\n\\t\\tvli.ipv6hostname,\\n\\t\\tvli.ipv6canping,\\n\\t\\tvli.ipv6monitorrcbgp,\\n\\t\\tvli.as112client,\\n\\t\\tvli.mcastenabled,\\n\\t\\tvli.ipv4bgpmd5secret,\\n\\t\\tvli.ipv6bgpmd5secret,\\n\\t\\tvli.rsclient,\\n\\t\\tvli.irrdbfilter,\\n\\t\\tvli.busyhost,\\n\\t\\tvli.notes,\\n\\t\\tv4.address AS ipv4address,\\n\\t\\tv6.address AS ipv6address\\n\\tFROM\\n\\t\\tphysicalinterface pi,\\n\\t\\tvirtualinterface vi,\\n\\t\\tvlaninterface vli\\n\\tLEFT JOIN (ipv4address v4) ON vli.ipv4addressid = v4.id\\n\\tLEFT JOIN (ipv6address v6) ON vli.ipv6addressid = v6.id\\n\\tLEFT JOIN vlan ON vli.vlanid = vlan.id\\n\\tWHERE\\n\\t\\tpi.virtualinterfaceid = vi.id\\n\\tAND\\tvli.virtualinterfaceid = vi.id;\\n\\n-- view: view_switch_details_by_custid\\n--\\n-- This is used to pick up all interesting details from switches.\\n\\nDROP VIEW IF EXISTS view_switch_details_by_custid;\\nCREATE VIEW view_switch_details_by_custid AS\\n\\tSELECT\\n\\t\\tvi.id AS id,\\n\\t\\tvi.custid,\\n\\t\\tCONCAT(vi.name,vi.channelgroup) AS virtualinterfacename,\\n\\t\\tpi.virtualinterfaceid,\\n\\t\\tpi.status,\\n\\t\\tpi.speed,\\n\\t\\tpi.duplex,\\n\\t\\tpi.notes,\\n\\t\\tsp.name AS switchport,\\n\\t\\tsp.id AS switchportid,\\n\\t\\tsp.ifName AS spifname,\\n\\t\\tsw.name AS switch,\\n\\t\\tsw.hostname AS switchhostname,\\n\\t\\tsw.id AS switchid,\\n\\t\\tsw.vendorid,\\n\\t\\tsw.snmppasswd,\\n\\t\\tsw.infrastructure,\\n\\t\\tca.name AS cabinet,\\n\\t\\tca.colocation AS colocabinet,\\n\\t\\tlo.name AS locationname,\\n\\t\\tlo.shortname AS locationshortname\\n\\tFROM\\n\\t\\tvirtualinterface vi,\\n\\t\\tphysicalinterface pi,\\n\\t\\tswitchport sp,\\n\\t\\tswitch sw,\\n\\t\\tcabinet ca,\\n\\t\\tlocation lo\\n\\tWHERE\\n\\t\\tpi.virtualinterfaceid = vi.id\\n\\tAND\\tpi.switchportid = sp.id\\n\\tAND\\tsp.switchid = sw.id\\n\\tAND\\tsw.cabinetid = ca.id\\n\\tAND\\tca.locationid = lo.id;\\n\\n\\n\\n-- trigger: bgp_sessions_update\\n--\\n-- This is used to update a n^2 table showing who peers with whom\\n\\n\\nDROP TRIGGER IF EXISTS `bgp_sessions_update`;\\n\\nDELIMITER ;;\\n\\nCREATE TRIGGER bgp_sessions_update AFTER INSERT ON `bgpsessiondata` FOR EACH ROW\\n\\n\\tBEGIN\\n\\n\\t\\tIF NOT EXISTS ( SELECT 1 FROM bgp_sessions WHERE srcipaddressid = NEW.srcipaddressid AND protocol = NEW.protocol AND dstipaddressid = NEW.dstipaddressid ) THEN\\n\\t\\t\\tINSERT INTO bgp_sessions\\n\\t\\t\\t\\t( srcipaddressid, protocol, dstipaddressid, packetcount, last_seen, source )\\n\\t\\t\\tVALUES\\n\\t\\t\\t\\t( NEW.srcipaddressid, NEW.protocol, NEW.dstipaddressid, NEW.packetcount, NOW(), NEW.source );\\n\\t\\tELSE\\n\\t\\t\\tUPDATE bgp_sessions SET\\n\\t\\t\\t\\tlast_seen   = NOW(),\\n\\t\\t\\t\\tpacketcount = packetcount + NEW.packetcount\\n\\t\\t\\tWHERE\\n\\t\\t\\t\\tsrcipaddressid = NEW.srcipaddressid AND protocol = NEW.protocol AND dstipaddressid = NEW.dstipaddressid;\\n\\t\\tEND IF;\\n\\n\\t\\tIF NOT EXISTS ( SELECT 1 FROM bgp_sessions WHERE dstipaddressid = NEW.srcipaddressid AND protocol = NEW.protocol AND srcipaddressid = NEW.dstipaddressid ) THEN\\n\\t\\t\\tINSERT INTO bgp_sessions\\n\\t\\t\\t\\t( srcipaddressid, protocol, dstipaddressid, packetcount, last_seen, source )\\n\\t\\t\\tVALUES\\n\\t\\t\\t\\t( NEW.dstipaddressid, NEW.protocol, NEW.srcipaddressid, NEW.packetcount, NOW(), NEW.source );\\n\\t\\tELSE\\n\\t\\t\\tUPDATE bgp_sessions SET\\n\\t\\t\\t\\tlast_seen   = NOW(),\\n\\t\\t\\t\\tpacketcount = packetcount + NEW.packetcount\\n\\t\\t\\tWHERE\\n\\t\\t\\t\\tdstipaddressid = NEW.srcipaddressid AND protocol = NEW.protocol AND srcipaddressid = NEW.dstipaddressid;\\n\\t\\tEND IF;\\n\\n\\tEND ;;\\n\\nDELIMITER ;\\n)\",\"trace\":[{\"file\":\"\\/Users\\/barryo\\/dev\\/ixp-ibn\\/vendor\\/laravel\\/framework\\/src\\/Illuminate\\/Database\\/Connection.php\",\"line\":629},{\"file\":\"\\/Users\\/barryo\\/dev\\/ixp-ibn\\/vendor\\/laravel\\/framework\\/src\\/Illuminate\\/Database\\/Connection.php\",\"line\":516},{\"file\":\"\\/Users\\/barryo\\/dev\\/ixp-ibn\\/vendor\\/laravel\\/framework\\/src\\/Illuminate\\/Database\\/DatabaseManager.php\",\"line\":349},{\"file\":\"\\/Users\\/barryo\\/dev\\/ixp-ibn\\/vendor\\/laravel\\/framework\\/src\\/Illuminate\\/Support\\/Facades\\/Facade.php\",\"line\":261},{\"file\":\"\\/Users\\/barryo\\/dev\\/ixp-ibn\\/app\\/Console\\/Commands\\/Upgrade\\/ResetMysqlViews.php\",\"line\":67},[],{\"file\":\"\\/Users\\/barryo\\/dev\\/ixp-ibn\\/vendor\\/laravel\\/framework\\/src\\/Illuminate\\/Container\\/BoundMethod.php\",\"line\":32},{\"file\":\"\\/Users\\/barryo\\/dev\\/ixp-ibn\\/vendor\\/laravel\\/framework\\/src\\/Illuminate\\/Container\\/Util.php\",\"line\":36},{\"file\":\"\\/Users\\/barryo\\/dev\\/ixp-ibn\\/vendor\\/laravel\\/framework\\/src\\/Illuminate\\/Container\\/BoundMethod.php\",\"line\":90},{\"file\":\"\\/Users\\/barryo\\/dev\\/ixp-ibn\\/vendor\\/laravel\\/framework\\/src\\/Illuminate\\/Container\\/BoundMethod.php\",\"line\":34},{\"file\":\"\\/Users\\/barryo\\/dev\\/ixp-ibn\\/vendor\\/laravel\\/framework\\/src\\/Illuminate\\/Container\\/Container.php\",\"line\":590},{\"file\":\"\\/Users\\/barryo\\/dev\\/ixp-ibn\\/vendor\\/laravel\\/framework\\/src\\/Illuminate\\/Console\\/Command.php\",\"line\":134},{\"file\":\"\\/Users\\/barryo\\/dev\\/ixp-ibn\\/vendor\\/symfony\\/console\\/Command\\/Command.php\",\"line\":255},{\"file\":\"\\/Users\\/barryo\\/dev\\/ixp-ibn\\/vendor\\/laravel\\/framework\\/src\\/Illuminate\\/Console\\/Command.php\",\"line\":121},{\"file\":\"\\/Users\\/barryo\\/dev\\/ixp-ibn\\/vendor\\/symfony\\/console\\/Application.php\",\"line\":1001},{\"file\":\"\\/Users\\/barryo\\/dev\\/ixp-ibn\\/vendor\\/symfony\\/console\\/Application.php\",\"line\":271},{\"file\":\"\\/Users\\/barryo\\/dev\\/ixp-ibn\\/vendor\\/symfony\\/console\\/Application.php\",\"line\":147},{\"file\":\"\\/Users\\/barryo\\/dev\\/ixp-ibn\\/vendor\\/laravel\\/framework\\/src\\/Illuminate\\/Console\\/Application.php\",\"line\":93},{\"file\":\"\\/Users\\/barryo\\/dev\\/ixp-ibn\\/vendor\\/laravel\\/framework\\/src\\/Illuminate\\/Foundation\\/Console\\/Kernel.php\",\"line\":131},{\"file\":\"\\/Users\\/barryo\\/dev\\/ixp-ibn\\/artisan\",\"line\":37}],\"line_preview\":{\"660\":\"        \\/\\/ took to execute and log the query SQL, bindings and time in our memory.\",\"661\":\"        try {\",\"662\":\"            $result = $callback($query, $bindings);\",\"663\":\"        }\",\"664\":\"\",\"665\":\"        \\/\\/ If an exception occurs when attempting to run a query, we\'ll format the error\",\"666\":\"        \\/\\/ message to include the bindings with SQL, which will make this exception a\",\"667\":\"        \\/\\/ lot more helpful to the developer instead of just the database\'s errors.\",\"668\":\"        catch (Exception $e) {\",\"669\":\"            throw new QueryException(\",\"670\":\"                $query, $this->prepareBindings($bindings), $e\",\"671\":\"            );\",\"672\":\"        }\",\"673\":\"\",\"674\":\"        return $result;\",\"675\":\"    }\",\"676\":\"\",\"677\":\"    \\/**\",\"678\":\"     * Log a query in the connection\'s query log.\",\"679\":\"     *\"},\"hostname\":\"Barrys-MacBook-Pro.local\",\"occurrences\":5}','2020-09-03 15:28:18');
/*!40000 ALTER TABLE `telescope_entries` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `telescope_entries_tags`
--

DROP TABLE IF EXISTS `telescope_entries_tags`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `telescope_entries_tags` (
  `entry_uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `tag` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  KEY `telescope_entries_tags_entry_uuid_tag_index` (`entry_uuid`,`tag`),
  KEY `telescope_entries_tags_tag_index` (`tag`),
  CONSTRAINT `telescope_entries_tags_entry_uuid_foreign` FOREIGN KEY (`entry_uuid`) REFERENCES `telescope_entries` (`uuid`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `telescope_entries_tags`
--

LOCK TABLES `telescope_entries_tags` WRITE;
/*!40000 ALTER TABLE `telescope_entries_tags` DISABLE KEYS */;
INSERT INTO `telescope_entries_tags` VALUES ('8ff2744d-3f5e-4d3f-8136-6d1b43cb27ea','Auth:1'),('9101c3ca-7580-4574-b3d5-7f2008dd4637','Auth:1');
/*!40000 ALTER TABLE `telescope_entries_tags` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `telescope_monitoring`
--

DROP TABLE IF EXISTS `telescope_monitoring`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `telescope_monitoring` (
  `tag` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `telescope_monitoring`
--

LOCK TABLES `telescope_monitoring` WRITE;
/*!40000 ALTER TABLE `telescope_monitoring` DISABLE KEYS */;
/*!40000 ALTER TABLE `telescope_monitoring` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `traffic_95th`
--

DROP TABLE IF EXISTS `traffic_95th`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `traffic_95th` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `cust_id` int DEFAULT NULL,
  `datetime` datetime DEFAULT NULL,
  `average` bigint DEFAULT NULL,
  `max` bigint DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_70BB409ABFF2A482` (`cust_id`),
  CONSTRAINT `FK_70BB409ABFF2A482` FOREIGN KEY (`cust_id`) REFERENCES `cust` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `traffic_95th`
--

LOCK TABLES `traffic_95th` WRITE;
/*!40000 ALTER TABLE `traffic_95th` DISABLE KEYS */;
/*!40000 ALTER TABLE `traffic_95th` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `traffic_95th_monthly`
--

DROP TABLE IF EXISTS `traffic_95th_monthly`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `traffic_95th_monthly` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `cust_id` int DEFAULT NULL,
  `month` date DEFAULT NULL,
  `max_95th` bigint DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_ED79F9DCBFF2A482` (`cust_id`),
  CONSTRAINT `FK_ED79F9DCBFF2A482` FOREIGN KEY (`cust_id`) REFERENCES `cust` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `traffic_95th_monthly`
--

LOCK TABLES `traffic_95th_monthly` WRITE;
/*!40000 ALTER TABLE `traffic_95th_monthly` DISABLE KEYS */;
/*!40000 ALTER TABLE `traffic_95th_monthly` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `traffic_daily`
--

DROP TABLE IF EXISTS `traffic_daily`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `traffic_daily` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `cust_id` int NOT NULL,
  `day` date DEFAULT NULL,
  `category` varchar(10) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `day_avg_in` bigint DEFAULT NULL,
  `day_avg_out` bigint DEFAULT NULL,
  `day_max_in` bigint DEFAULT NULL,
  `day_max_out` bigint DEFAULT NULL,
  `day_tot_in` bigint DEFAULT NULL,
  `day_tot_out` bigint DEFAULT NULL,
  `week_avg_in` bigint DEFAULT NULL,
  `week_avg_out` bigint DEFAULT NULL,
  `week_max_in` bigint DEFAULT NULL,
  `week_max_out` bigint DEFAULT NULL,
  `week_tot_in` bigint DEFAULT NULL,
  `week_tot_out` bigint DEFAULT NULL,
  `month_avg_in` bigint DEFAULT NULL,
  `month_avg_out` bigint DEFAULT NULL,
  `month_max_in` bigint DEFAULT NULL,
  `month_max_out` bigint DEFAULT NULL,
  `month_tot_in` bigint DEFAULT NULL,
  `month_tot_out` bigint DEFAULT NULL,
  `year_avg_in` bigint DEFAULT NULL,
  `year_avg_out` bigint DEFAULT NULL,
  `year_max_in` bigint DEFAULT NULL,
  `year_max_out` bigint DEFAULT NULL,
  `year_tot_in` bigint DEFAULT NULL,
  `year_tot_out` bigint DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_1F0F81A7BFF2A482` (`cust_id`),
  CONSTRAINT `FK_1F0F81A7BFF2A482` FOREIGN KEY (`cust_id`) REFERENCES `cust` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `traffic_daily`
--

LOCK TABLES `traffic_daily` WRITE;
/*!40000 ALTER TABLE `traffic_daily` DISABLE KEYS */;
/*!40000 ALTER TABLE `traffic_daily` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `traffic_daily_phys_ints`
--

DROP TABLE IF EXISTS `traffic_daily_phys_ints`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `traffic_daily_phys_ints` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `physicalinterface_id` int NOT NULL,
  `day` date DEFAULT NULL,
  `category` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `day_avg_in` bigint DEFAULT NULL,
  `day_avg_out` bigint DEFAULT NULL,
  `day_max_in` bigint DEFAULT NULL,
  `day_max_out` bigint DEFAULT NULL,
  `day_max_in_at` datetime DEFAULT NULL,
  `day_max_out_at` datetime DEFAULT NULL,
  `day_tot_in` bigint DEFAULT NULL,
  `day_tot_out` bigint DEFAULT NULL,
  `week_avg_in` bigint DEFAULT NULL,
  `week_avg_out` bigint DEFAULT NULL,
  `week_max_in` bigint DEFAULT NULL,
  `week_max_out` bigint DEFAULT NULL,
  `week_max_in_at` datetime DEFAULT NULL,
  `week_max_out_at` datetime DEFAULT NULL,
  `week_tot_in` bigint DEFAULT NULL,
  `week_tot_out` bigint DEFAULT NULL,
  `month_avg_in` bigint DEFAULT NULL,
  `month_avg_out` bigint DEFAULT NULL,
  `month_max_in` bigint DEFAULT NULL,
  `month_max_out` bigint DEFAULT NULL,
  `month_max_in_at` datetime DEFAULT NULL,
  `month_max_out_at` datetime DEFAULT NULL,
  `month_tot_in` bigint DEFAULT NULL,
  `month_tot_out` bigint DEFAULT NULL,
  `year_avg_in` bigint DEFAULT NULL,
  `year_avg_out` bigint DEFAULT NULL,
  `year_max_in` bigint DEFAULT NULL,
  `year_max_out` bigint DEFAULT NULL,
  `year_max_in_at` datetime DEFAULT NULL,
  `year_max_out_at` datetime DEFAULT NULL,
  `year_tot_in` bigint DEFAULT NULL,
  `year_tot_out` bigint DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_E219461D4643D08A` (`physicalinterface_id`),
  CONSTRAINT `FK_E219461D4643D08A` FOREIGN KEY (`physicalinterface_id`) REFERENCES `physicalinterface` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `traffic_daily_phys_ints`
--

LOCK TABLES `traffic_daily_phys_ints` WRITE;
/*!40000 ALTER TABLE `traffic_daily_phys_ints` DISABLE KEYS */;
/*!40000 ALTER TABLE `traffic_daily_phys_ints` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user` (
  `id` int NOT NULL AUTO_INCREMENT,
  `custid` int DEFAULT NULL,
  `username` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `password` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `email` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `authorisedMobile` varchar(30) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `uid` int DEFAULT NULL,
  `privs` int DEFAULT NULL,
  `disabled` tinyint(1) DEFAULT NULL,
  `lastupdatedby` int DEFAULT NULL,
  `creator` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `peeringdb_id` bigint DEFAULT NULL,
  `extra_attributes` json DEFAULT NULL COMMENT '(DC2Type:json)',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `prefs` json DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_8D93D649F85E0677` (`username`),
  UNIQUE KEY `UNIQ_8D93D649F2C6186B` (`peeringdb_id`),
  KEY `IDX_8D93D649DA0209B9` (`custid`),
  CONSTRAINT `FK_8D93D649DA0209B9` FOREIGN KEY (`custid`) REFERENCES `cust` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb3 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user`
--

LOCK TABLES `user` WRITE;
/*!40000 ALTER TABLE `user` DISABLE KEYS */;
INSERT INTO `user` VALUES (1,1,'travis','$2y$10$FNzPyTKm64oSKeUUCwm1buLQp7h80nBj2suqdjsWH2aajVS1xz/ce','joe@siep.com',NULL,NULL,3,0,1,'travis',NULL,NULL,NULL,'2014-01-06 12:54:22','2014-01-06 12:54:22',NULL),(2,5,'imcustadmin','$2y$10$VlJG/42TCK7VQz1Wwy7yreP73Eq/1VKn55B4vJfXy4U7fIGK/9YWC','imagine-custadmin@example.com',NULL,NULL,2,0,2,'travis','Test Test',NULL,NULL,'2018-05-15 13:36:12','2019-01-16 14:37:24',NULL),(3,5,'imcustuser','$2y$10$sIUXAklQmQwalBF0nGgCLenCYYUMXWdqSESRjw6faXfiyymfmpk3y','imagine-custuser@example.com',NULL,NULL,1,0,3,'travis','Joe Bloggs',NULL,NULL,'2018-05-15 13:36:54','2019-01-16 14:44:30',NULL),(4,2,'hecustuser','$2y$10$sIUXAklQmQwalBF0nGgCLenCYYUMXWdqSESRjw6faXfiyymfmpk3y','heanet-custuser@example.com',NULL,NULL,1,0,1,'travis',NULL,NULL,NULL,'2018-05-15 13:36:54','2018-05-15 13:36:54',NULL),(5,2,'hecustadmin','$2y$10$sIUXAklQmQwalBF0nGgCLenCYYUMXWdqSESRjw6faXfiyymfmpk3y','heanet-custadmin@example.com',NULL,NULL,2,0,1,'travis',NULL,NULL,NULL,'2018-05-15 13:36:54','2018-05-15 13:36:54',NULL);
/*!40000 ALTER TABLE `user` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_2fa`
--

DROP TABLE IF EXISTS `user_2fa`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_2fa` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `enabled` tinyint(1) NOT NULL DEFAULT '0',
  `secret` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_3AAA1488A76ED395` (`user_id`),
  CONSTRAINT `FK_3AAA1488A76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_2fa`
--

LOCK TABLES `user_2fa` WRITE;
/*!40000 ALTER TABLE `user_2fa` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_2fa` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_logins`
--

DROP TABLE IF EXISTS `user_logins`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_logins` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `ip` varchar(39) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `at` datetime NOT NULL,
  `customer_to_user_id` int DEFAULT NULL,
  `via` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id_idx` (`user_id`),
  KEY `IDX_6341CC99D43FEAE2` (`customer_to_user_id`),
  KEY `at_idx` (`at`),
  CONSTRAINT `FK_6341CC99D43FEAE2` FOREIGN KEY (`customer_to_user_id`) REFERENCES `customer_to_users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=58 DEFAULT CHARSET=utf8mb3 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_logins`
--

LOCK TABLES `user_logins` WRITE;
/*!40000 ALTER TABLE `user_logins` DISABLE KEYS */;
INSERT INTO `user_logins` VALUES (1,1,'10.37.129.2','2014-01-06 13:54:52',1,NULL,NULL,NULL),(2,1,'10.37.129.2','2014-01-13 10:38:11',1,NULL,NULL,NULL),(3,1,'::1','2016-11-07 19:30:35',1,NULL,NULL,NULL),(4,1,'127.0.0.1','2017-10-09 13:19:59',1,NULL,NULL,NULL),(5,1,'127.0.0.1','2018-05-15 15:34:35',1,NULL,NULL,NULL),(6,1,'127.0.0.1','2018-06-18 08:30:06',1,NULL,NULL,NULL),(7,1,'127.0.0.1','2018-06-18 08:30:08',1,NULL,NULL,NULL),(8,1,'127.0.0.1','2018-06-18 08:31:04',1,NULL,NULL,NULL),(9,1,'127.0.0.1','2018-06-18 08:31:06',1,NULL,NULL,NULL),(10,1,'127.0.0.1','2018-06-18 08:36:56',1,NULL,NULL,NULL),(11,1,'127.0.0.1','2018-06-18 08:36:58',1,NULL,NULL,NULL),(12,1,'127.0.0.1','2018-06-18 08:43:14',1,NULL,NULL,NULL),(13,1,'127.0.0.1','2018-06-18 08:43:16',1,NULL,NULL,NULL),(14,1,'127.0.0.1','2018-06-18 08:43:27',1,NULL,NULL,NULL),(15,1,'127.0.0.1','2018-06-18 08:43:29',1,NULL,NULL,NULL),(16,1,'127.0.0.1','2018-06-18 11:29:20',1,NULL,NULL,NULL),(17,1,'127.0.0.1','2018-06-18 11:29:22',1,NULL,NULL,NULL),(18,1,'127.0.0.1','2018-06-19 13:15:32',1,NULL,NULL,NULL),(19,1,'127.0.0.1','2018-06-19 14:16:24',1,NULL,NULL,NULL),(20,1,'127.0.0.1','2018-06-19 14:16:26',1,NULL,NULL,NULL),(21,1,'127.0.0.1','2018-06-19 14:17:07',1,NULL,NULL,NULL),(22,1,'127.0.0.1','2018-06-19 14:17:09',1,NULL,NULL,NULL),(23,1,'127.0.0.1','2018-06-19 14:19:14',1,NULL,NULL,NULL),(24,1,'127.0.0.1','2018-06-19 14:19:16',1,NULL,NULL,NULL),(25,1,'127.0.0.1','2018-06-19 14:22:14',1,NULL,NULL,NULL),(26,1,'127.0.0.1','2018-06-19 14:22:17',1,NULL,NULL,NULL),(27,2,'127.0.0.1','2018-06-20 10:23:22',2,NULL,NULL,NULL),(28,3,'127.0.0.1','2018-06-20 10:23:58',3,NULL,NULL,NULL),(29,5,'127.0.0.1','2018-06-20 10:24:14',5,NULL,NULL,NULL),(30,5,'127.0.0.1','2018-06-20 10:24:24',5,NULL,NULL,NULL),(31,1,'127.0.0.1','2018-06-20 10:25:55',1,NULL,NULL,NULL),(32,1,'127.0.0.1','2018-06-20 10:25:57',1,NULL,NULL,NULL),(33,1,'127.0.0.1','2018-06-20 10:26:49',1,NULL,NULL,NULL),(34,1,'127.0.0.1','2018-06-20 10:26:51',1,NULL,NULL,NULL),(35,1,'127.0.0.1','2018-06-20 10:27:05',1,NULL,NULL,NULL),(36,1,'127.0.0.1','2018-06-20 10:27:07',1,NULL,NULL,NULL),(37,1,'127.0.0.1','2018-06-20 10:27:22',1,NULL,NULL,NULL),(38,1,'127.0.0.1','2018-06-20 10:27:24',1,NULL,NULL,NULL),(39,1,'127.0.0.1','2018-06-20 10:28:25',1,NULL,NULL,NULL),(40,1,'127.0.0.1','2018-06-20 10:28:27',1,NULL,NULL,NULL),(41,1,'127.0.0.1','2018-06-20 10:28:57',1,NULL,NULL,NULL),(42,1,'127.0.0.1','2018-06-20 10:28:59',1,NULL,NULL,NULL),(43,1,'127.0.0.1','2018-06-20 10:32:11',1,NULL,NULL,NULL),(44,1,'127.0.0.1','2018-06-20 10:32:13',1,NULL,NULL,NULL),(45,1,'127.0.0.1','2018-06-20 10:36:34',1,NULL,NULL,NULL),(46,1,'127.0.0.1','2018-06-20 10:36:36',1,NULL,NULL,NULL),(47,1,'127.0.0.1','2018-06-20 10:37:19',1,NULL,NULL,NULL),(48,1,'127.0.0.1','2018-06-20 10:37:21',1,NULL,NULL,NULL),(49,1,'127.0.0.1','2018-06-20 10:37:44',1,NULL,NULL,NULL),(50,1,'127.0.0.1','2018-06-20 10:37:46',1,NULL,NULL,NULL),(51,1,'127.0.0.1','2018-06-20 10:38:41',1,NULL,NULL,NULL),(52,1,'127.0.0.1','2018-06-20 10:38:42',1,NULL,NULL,NULL),(53,2,'127.0.0.1','2019-01-16 15:37:08',2,NULL,NULL,NULL),(54,3,'127.0.0.1','2019-01-16 15:38:05',3,NULL,NULL,NULL),(55,1,'127.0.0.1','2019-03-09 15:38:09',1,NULL,NULL,NULL),(56,NULL,'127.0.0.1','2020-01-27 12:04:24',1,NULL,NULL,NULL),(57,NULL,'127.0.0.1','2020-07-10 07:54:18',1,'Login',NULL,NULL);
/*!40000 ALTER TABLE `user_logins` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_pref`
--

DROP TABLE IF EXISTS `user_pref`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_pref` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `attribute` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `ix` int NOT NULL DEFAULT '0',
  `op` varchar(2) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `value` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `expire` bigint NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `IX_UserPreference_1` (`user_id`,`attribute`,`op`,`ix`),
  KEY `IDX_DBD4D4F8A76ED395` (`user_id`),
  CONSTRAINT `FK_DBD4D4F8A76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb3 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_pref`
--

LOCK TABLES `user_pref` WRITE;
/*!40000 ALTER TABLE `user_pref` DISABLE KEYS */;
INSERT INTO `user_pref` VALUES (1,1,'auth.last_login_from',0,'=','127.0.0.1',0),(2,1,'auth.last_login_at',0,'=','1529491122',0),(3,2,'auth.last_login_from',0,'=','127.0.0.1',0),(4,2,'auth.last_login_at',0,'=','1529490202',0),(5,3,'auth.last_login_from',0,'=','127.0.0.1',0),(6,3,'auth.last_login_at',0,'=','1529490238',0),(7,5,'auth.last_login_from',0,'=','127.0.0.1',0),(8,5,'auth.last_login_at',0,'=','1529490264',0);
/*!40000 ALTER TABLE `user_pref` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_remember_tokens`
--

DROP TABLE IF EXISTS `user_remember_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_remember_tokens` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `token` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `device` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `ip` varchar(39) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `expires` datetime NOT NULL,
  `is_2fa_complete` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_token` (`user_id`,`token`),
  KEY `IDX_E253302EA76ED395` (`user_id`),
  CONSTRAINT `FK_E253302EA76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_remember_tokens`
--

LOCK TABLES `user_remember_tokens` WRITE;
/*!40000 ALTER TABLE `user_remember_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_remember_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vendor`
--

DROP TABLE IF EXISTS `vendor`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `vendor` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `shortname` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `nagios_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `bundle_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb3 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vendor`
--

LOCK TABLES `vendor` WRITE;
/*!40000 ALTER TABLE `vendor` DISABLE KEYS */;
INSERT INTO `vendor` VALUES (1,'Cisco Systems','Cisco','cisco',NULL,NULL,NULL),(2,'Foundry Networks','Brocade','brocade',NULL,NULL,NULL),(3,'Extreme Networks','Extreme','extreme',NULL,NULL,NULL),(4,'Force10 Networks','Force10','force10',NULL,NULL,NULL),(5,'Glimmerglass','Glimmerglass','glimmerglass',NULL,NULL,NULL),(6,'Allied Telesyn','AlliedTel','alliedtel',NULL,NULL,NULL),(7,'Enterasys','Enterasys','enterasys',NULL,NULL,NULL),(8,'Dell','Dell','dell',NULL,NULL,NULL),(9,'Hitachi Cable','Hitachi','hitachi',NULL,NULL,NULL),(10,'MRV','MRV','mrv',NULL,NULL,NULL),(11,'Transmode','Transmode','transmode',NULL,NULL,NULL),(12,'Brocade','Brocade','brocade',NULL,NULL,NULL),(13,'Juniper Networks','Juniper','juniper',NULL,NULL,NULL);
/*!40000 ALTER TABLE `vendor` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Temporary view structure for view `view_cust_current_active`
--

DROP TABLE IF EXISTS `view_cust_current_active`;
/*!50001 DROP VIEW IF EXISTS `view_cust_current_active`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `view_cust_current_active` AS SELECT 
 1 AS `id`,
 1 AS `irrdb`,
 1 AS `company_registered_detail_id`,
 1 AS `company_billing_details_id`,
 1 AS `reseller`,
 1 AS `name`,
 1 AS `type`,
 1 AS `shortname`,
 1 AS `abbreviatedName`,
 1 AS `autsys`,
 1 AS `maxprefixes`,
 1 AS `peeringemail`,
 1 AS `nocphone`,
 1 AS `noc24hphone`,
 1 AS `nocfax`,
 1 AS `nocemail`,
 1 AS `nochours`,
 1 AS `nocwww`,
 1 AS `peeringmacro`,
 1 AS `peeringmacrov6`,
 1 AS `peeringpolicy`,
 1 AS `corpwww`,
 1 AS `datejoin`,
 1 AS `dateleave`,
 1 AS `status`,
 1 AS `activepeeringmatrix`,
 1 AS `lastupdated`,
 1 AS `lastupdatedby`,
 1 AS `creator`,
 1 AS `created`,
 1 AS `MD5Support`,
 1 AS `isReseller`,
 1 AS `in_manrs`,
 1 AS `in_peeringdb`,
 1 AS `peeringdb_oauth`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `view_switch_details_by_custid`
--

DROP TABLE IF EXISTS `view_switch_details_by_custid`;
/*!50001 DROP VIEW IF EXISTS `view_switch_details_by_custid`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `view_switch_details_by_custid` AS SELECT 
 1 AS `id`,
 1 AS `custid`,
 1 AS `virtualinterfacename`,
 1 AS `virtualinterfaceid`,
 1 AS `status`,
 1 AS `speed`,
 1 AS `duplex`,
 1 AS `notes`,
 1 AS `switchport`,
 1 AS `switchportid`,
 1 AS `spifname`,
 1 AS `switch`,
 1 AS `switchhostname`,
 1 AS `switchid`,
 1 AS `vendorid`,
 1 AS `snmppasswd`,
 1 AS `infrastructure`,
 1 AS `cabinet`,
 1 AS `colocabinet`,
 1 AS `locationname`,
 1 AS `locationshortname`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `view_vlaninterface_details_by_custid`
--

DROP TABLE IF EXISTS `view_vlaninterface_details_by_custid`;
/*!50001 DROP VIEW IF EXISTS `view_vlaninterface_details_by_custid`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `view_vlaninterface_details_by_custid` AS SELECT 
 1 AS `id`,
 1 AS `custid`,
 1 AS `virtualinterfaceid`,
 1 AS `status`,
 1 AS `virtualinterfacename`,
 1 AS `vlan`,
 1 AS `vlanname`,
 1 AS `vlanid`,
 1 AS `vlaninterfaceid`,
 1 AS `ipv4enabled`,
 1 AS `ipv4hostname`,
 1 AS `ipv4canping`,
 1 AS `ipv4monitorrcbgp`,
 1 AS `ipv6enabled`,
 1 AS `ipv6hostname`,
 1 AS `ipv6canping`,
 1 AS `ipv6monitorrcbgp`,
 1 AS `as112client`,
 1 AS `mcastenabled`,
 1 AS `ipv4bgpmd5secret`,
 1 AS `ipv6bgpmd5secret`,
 1 AS `rsclient`,
 1 AS `irrdbfilter`,
 1 AS `busyhost`,
 1 AS `notes`,
 1 AS `ipv4address`,
 1 AS `ipv6address`*/;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `virtualinterface`
--

DROP TABLE IF EXISTS `virtualinterface`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `virtualinterface` (
  `id` int NOT NULL AUTO_INCREMENT,
  `custid` int DEFAULT NULL,
  `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `description` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `mtu` int DEFAULT NULL,
  `trunk` tinyint(1) DEFAULT NULL,
  `channelgroup` int DEFAULT NULL,
  `lag_framing` tinyint(1) NOT NULL DEFAULT '0',
  `fastlacp` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_11D9014FDA0209B9` (`custid`),
  CONSTRAINT `FK_11D9014FDA0209B9` FOREIGN KEY (`custid`) REFERENCES `cust` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb3 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `virtualinterface`
--

LOCK TABLES `virtualinterface` WRITE;
/*!40000 ALTER TABLE `virtualinterface` DISABLE KEYS */;
INSERT INTO `virtualinterface` VALUES (1,2,'Port-Channel','',NULL,0,1,1,1,NULL,NULL),(2,2,'Port-Channel','',NULL,1,2,1,0,NULL,NULL),(3,3,'','',NULL,0,NULL,0,0,NULL,NULL),(4,4,'','',NULL,0,NULL,0,0,NULL,NULL),(5,4,'','',NULL,0,NULL,0,0,NULL,NULL),(6,5,'','',NULL,0,NULL,0,0,NULL,NULL),(7,5,'','',NULL,0,NULL,0,0,NULL,NULL),(8,1,NULL,NULL,9000,1,NULL,0,0,NULL,NULL),(9,1,NULL,NULL,9000,1,NULL,0,0,NULL,NULL),(10,5,'',NULL,NULL,0,NULL,0,0,NULL,NULL);
/*!40000 ALTER TABLE `virtualinterface` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vlan`
--

DROP TABLE IF EXISTS `vlan`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `vlan` (
  `id` int NOT NULL AUTO_INCREMENT,
  `infrastructureid` int NOT NULL,
  `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `number` int DEFAULT NULL,
  `private` tinyint(1) NOT NULL DEFAULT '0',
  `notes` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `peering_matrix` tinyint(1) NOT NULL DEFAULT '0',
  `peering_manager` tinyint(1) NOT NULL DEFAULT '0',
  `config_name` varchar(32) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `infra_config_name` (`infrastructureid`,`config_name`),
  KEY `IDX_F83104A1721EBF79` (`infrastructureid`),
  CONSTRAINT `FK_F83104A1721EBF79` FOREIGN KEY (`infrastructureid`) REFERENCES `infrastructure` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb3 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vlan`
--

LOCK TABLES `vlan` WRITE;
/*!40000 ALTER TABLE `vlan` DISABLE KEYS */;
INSERT INTO `vlan` VALUES (1,1,'Peering LAN 1',1,0,'',1,1,NULL,NULL,NULL),(2,2,'Peering LAN 2',2,0,'',1,1,NULL,NULL,NULL);
/*!40000 ALTER TABLE `vlan` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vlaninterface`
--

DROP TABLE IF EXISTS `vlaninterface`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `vlaninterface` (
  `id` int NOT NULL AUTO_INCREMENT,
  `ipv4addressid` int DEFAULT NULL,
  `ipv6addressid` int DEFAULT NULL,
  `virtualinterfaceid` int DEFAULT NULL,
  `vlanid` int DEFAULT NULL,
  `ipv4enabled` tinyint(1) DEFAULT '0',
  `ipv4hostname` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `ipv6enabled` tinyint(1) DEFAULT '0',
  `ipv6hostname` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `mcastenabled` tinyint(1) DEFAULT '0',
  `irrdbfilter` tinyint(1) DEFAULT '1',
  `bgpmd5secret` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `ipv4bgpmd5secret` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `ipv6bgpmd5secret` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `maxbgpprefix` int DEFAULT NULL,
  `rsclient` tinyint(1) DEFAULT NULL,
  `ipv4canping` tinyint(1) DEFAULT NULL,
  `ipv6canping` tinyint(1) DEFAULT NULL,
  `ipv4monitorrcbgp` tinyint(1) DEFAULT NULL,
  `ipv6monitorrcbgp` tinyint(1) DEFAULT NULL,
  `as112client` tinyint(1) DEFAULT NULL,
  `busyhost` tinyint(1) DEFAULT NULL,
  `notes` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `rsmorespecifics` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_B4B4411A73720641` (`ipv4addressid`),
  UNIQUE KEY `UNIQ_B4B4411A7787D67C` (`ipv6addressid`),
  KEY `IDX_B4B4411ABFDF15D5` (`virtualinterfaceid`),
  KEY `IDX_B4B4411AF48D6D0` (`vlanid`),
  CONSTRAINT `FK_B4B4411A73720641` FOREIGN KEY (`ipv4addressid`) REFERENCES `ipv4address` (`id`),
  CONSTRAINT `FK_B4B4411A7787D67C` FOREIGN KEY (`ipv6addressid`) REFERENCES `ipv6address` (`id`),
  CONSTRAINT `FK_B4B4411ABFDF15D5` FOREIGN KEY (`virtualinterfaceid`) REFERENCES `virtualinterface` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_B4B4411AF48D6D0` FOREIGN KEY (`vlanid`) REFERENCES `vlan` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb3 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vlaninterface`
--

LOCK TABLES `vlaninterface` WRITE;
/*!40000 ALTER TABLE `vlaninterface` DISABLE KEYS */;
INSERT INTO `vlaninterface` VALUES (1,10,16,1,1,1,'a.heanet.ie',1,'a.heanet.ie',0,1,NULL,'N7rX2SdfbRsyBLTm','N7rX2SdfbRsyBLTm',1000,1,1,1,1,1,1,0,NULL,0,NULL,NULL),(2,137,417,2,2,1,'b.heanet.ie',1,'b.heanet.ie',0,1,NULL,'u5zSNJLAVT87RGXQ','u5zSNJLAVT87RGXQ',1000,1,1,1,1,1,0,0,NULL,0,NULL,NULL),(3,36,NULL,3,1,1,'a.pch.ie',0,'',0,1,NULL,'mcWsqMdzGwTKt67g','mcWsqMdzGwTKt67g',2000,1,1,0,1,0,1,0,NULL,0,NULL,NULL),(4,6,NULL,4,1,1,'a.as112.net',0,'',0,1,NULL,'w83fmGpRDtaKomQo','w83fmGpRDtaKomQo',20,1,1,0,1,0,0,0,NULL,0,NULL,NULL),(5,132,NULL,5,2,1,'b.as112.net',0,'',0,1,NULL,'Pz8VYMNwEdCjKz68','Pz8VYMNwEdCjKz68',20,1,1,0,1,0,0,0,NULL,0,NULL,NULL),(6,NULL,8,6,1,0,'',1,'a.imagine.ie',0,1,NULL,'X8Ks9QnbER9cyzU3','X8Ks9QnbER9cyzU3',1000,1,0,1,0,1,0,0,NULL,1,NULL,NULL),(7,172,470,7,2,1,'b.imagine.ie',1,'b.imagine.ie',0,1,NULL,'LyJND4eoKuQz5j49','LyJND4eoKuQz5j49',1000,0,1,1,1,1,0,0,'',1,NULL,NULL),(8,142,422,10,2,1,'v4.example.com',1,'v6.example.com',0,1,NULL,'soopersecret','soopersecret',100,1,1,1,1,1,1,0,NULL,0,NULL,NULL);
/*!40000 ALTER TABLE `vlaninterface` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Final view structure for view `view_cust_current_active`
--

/*!50001 DROP VIEW IF EXISTS `view_cust_current_active`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `view_cust_current_active` AS select `cu`.`id` AS `id`,`cu`.`irrdb` AS `irrdb`,`cu`.`company_registered_detail_id` AS `company_registered_detail_id`,`cu`.`company_billing_details_id` AS `company_billing_details_id`,`cu`.`reseller` AS `reseller`,`cu`.`name` AS `name`,`cu`.`type` AS `type`,`cu`.`shortname` AS `shortname`,`cu`.`abbreviatedName` AS `abbreviatedName`,`cu`.`autsys` AS `autsys`,`cu`.`maxprefixes` AS `maxprefixes`,`cu`.`peeringemail` AS `peeringemail`,`cu`.`nocphone` AS `nocphone`,`cu`.`noc24hphone` AS `noc24hphone`,`cu`.`nocfax` AS `nocfax`,`cu`.`nocemail` AS `nocemail`,`cu`.`nochours` AS `nochours`,`cu`.`nocwww` AS `nocwww`,`cu`.`peeringmacro` AS `peeringmacro`,`cu`.`peeringmacrov6` AS `peeringmacrov6`,`cu`.`peeringpolicy` AS `peeringpolicy`,`cu`.`corpwww` AS `corpwww`,`cu`.`datejoin` AS `datejoin`,`cu`.`dateleave` AS `dateleave`,`cu`.`status` AS `status`,`cu`.`activepeeringmatrix` AS `activepeeringmatrix`,`cu`.`updated_at` AS `lastupdated`,`cu`.`lastupdatedby` AS `lastupdatedby`,`cu`.`creator` AS `creator`,`cu`.`created_at` AS `created`,`cu`.`MD5Support` AS `MD5Support`,`cu`.`isReseller` AS `isReseller`,`cu`.`in_manrs` AS `in_manrs`,`cu`.`in_peeringdb` AS `in_peeringdb`,`cu`.`peeringdb_oauth` AS `peeringdb_oauth` from `cust` `cu` where ((`cu`.`datejoin` <= curdate()) and ((`cu`.`dateleave` is null) or (`cu`.`dateleave` < '1970-01-01') or (`cu`.`dateleave` >= curdate())) and ((`cu`.`status` = 1) or (`cu`.`status` = 2))) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `view_switch_details_by_custid`
--

/*!50001 DROP VIEW IF EXISTS `view_switch_details_by_custid`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_unicode_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `view_switch_details_by_custid` AS select `vi`.`id` AS `id`,`vi`.`custid` AS `custid`,concat(`vi`.`name`,`vi`.`channelgroup`) AS `virtualinterfacename`,`pi`.`virtualinterfaceid` AS `virtualinterfaceid`,`pi`.`status` AS `status`,`pi`.`speed` AS `speed`,`pi`.`duplex` AS `duplex`,`pi`.`notes` AS `notes`,`sp`.`name` AS `switchport`,`sp`.`id` AS `switchportid`,`sp`.`ifName` AS `spifname`,`sw`.`name` AS `switch`,`sw`.`hostname` AS `switchhostname`,`sw`.`id` AS `switchid`,`sw`.`vendorid` AS `vendorid`,`sw`.`snmppasswd` AS `snmppasswd`,`sw`.`infrastructure` AS `infrastructure`,`ca`.`name` AS `cabinet`,`ca`.`colocation` AS `colocabinet`,`lo`.`name` AS `locationname`,`lo`.`shortname` AS `locationshortname` from (((((`virtualinterface` `vi` join `physicalinterface` `pi`) join `switchport` `sp`) join `switch` `sw`) join `cabinet` `ca`) join `location` `lo`) where ((`pi`.`virtualinterfaceid` = `vi`.`id`) and (`pi`.`switchportid` = `sp`.`id`) and (`sp`.`switchid` = `sw`.`id`) and (`sw`.`cabinetid` = `ca`.`id`) and (`ca`.`locationid` = `lo`.`id`)) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `view_vlaninterface_details_by_custid`
--

/*!50001 DROP VIEW IF EXISTS `view_vlaninterface_details_by_custid`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_unicode_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `view_vlaninterface_details_by_custid` AS select `pi`.`id` AS `id`,`vi`.`custid` AS `custid`,`pi`.`virtualinterfaceid` AS `virtualinterfaceid`,`pi`.`status` AS `status`,concat(`vi`.`name`,`vi`.`channelgroup`) AS `virtualinterfacename`,`vlan`.`number` AS `vlan`,`vlan`.`name` AS `vlanname`,`vlan`.`id` AS `vlanid`,`vli`.`id` AS `vlaninterfaceid`,`vli`.`ipv4enabled` AS `ipv4enabled`,`vli`.`ipv4hostname` AS `ipv4hostname`,`vli`.`ipv4canping` AS `ipv4canping`,`vli`.`ipv4monitorrcbgp` AS `ipv4monitorrcbgp`,`vli`.`ipv6enabled` AS `ipv6enabled`,`vli`.`ipv6hostname` AS `ipv6hostname`,`vli`.`ipv6canping` AS `ipv6canping`,`vli`.`ipv6monitorrcbgp` AS `ipv6monitorrcbgp`,`vli`.`as112client` AS `as112client`,`vli`.`mcastenabled` AS `mcastenabled`,`vli`.`ipv4bgpmd5secret` AS `ipv4bgpmd5secret`,`vli`.`ipv6bgpmd5secret` AS `ipv6bgpmd5secret`,`vli`.`rsclient` AS `rsclient`,`vli`.`irrdbfilter` AS `irrdbfilter`,`vli`.`busyhost` AS `busyhost`,`vli`.`notes` AS `notes`,`v4`.`address` AS `ipv4address`,`v6`.`address` AS `ipv6address` from ((`physicalinterface` `pi` join `virtualinterface` `vi`) join (((`vlaninterface` `vli` left join `ipv4address` `v4` on((`vli`.`ipv4addressid` = `v4`.`id`))) left join `ipv6address` `v6` on((`vli`.`ipv6addressid` = `v6`.`id`))) left join `vlan` on((`vli`.`vlanid` = `vlan`.`id`)))) where ((`pi`.`virtualinterfaceid` = `vi`.`id`) and (`vli`.`virtualinterfaceid` = `vi`.`id`)) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2021-11-14 11:19:18
