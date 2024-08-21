-- MySQL dump 10.13  Distrib 8.0.39, for Linux (aarch64)
--
-- Host: localhost    Database: ixp
-- ------------------------------------------------------
-- Server version	8.0.39-0ubuntu0.24.04.1

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
  `apiKey` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `expires` datetime DEFAULT NULL,
  `allowedIPs` mediumtext CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `lastseenAt` datetime DEFAULT NULL,
  `lastseenFrom` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `description` longtext CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `created_at` timestamp NOT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_9579321F800A1141` (`apiKey`),
  KEY `IDX_9579321FA76ED395` (`user_id`),
  CONSTRAINT `FK_9579321FA76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `api_keys`
--

LOCK TABLES `api_keys` WRITE;
/*!40000 ALTER TABLE `api_keys` DISABLE KEYS */;
INSERT INTO `api_keys` VALUES (1,1,'r8sFfkGamCjrbbLC12yIoCJooIRXzY9CYPaLVz92GFQyGqLq',NULL,NULL,NULL,NULL,'Vagrant Dev API Key','2024-08-21 18:56:07','2024-08-21 18:56:07');
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
  `source` varchar(40) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `src_protocol_dst` (`srcipaddressid`,`protocol`,`dstipaddressid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
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
  `source` varchar(40) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_timestamp` (`timestamp`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
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
  `name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `colocation` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `height` int DEFAULT NULL,
  `type` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `notes` longtext CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `u_counts_from` smallint DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_4CED05B05E237E06` (`name`),
  KEY `IDX_4CED05B03530CCF` (`locationid`),
  CONSTRAINT `FK_4CED05B03530CCF` FOREIGN KEY (`locationid`) REFERENCES `location` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cabinet`
--

LOCK TABLES `cabinet` WRITE;
/*!40000 ALTER TABLE `cabinet` DISABLE KEYS */;
INSERT INTO `cabinet` VALUES (1,1,'Rack F1-1','FAC1-R1',NULL,NULL,NULL,2,'2024-08-21 18:58:17','2024-08-21 18:58:17'),(2,2,'Rack F2-1','FAC2-R1',NULL,NULL,NULL,2,'2024-08-21 18:58:37','2024-08-21 18:58:37'),(3,2,'Rack F2-2','FAC2-R2',NULL,NULL,NULL,2,'2024-08-21 18:58:53','2024-08-21 18:58:53'),(4,1,'Rack F1-2','FAC1-R2',NULL,NULL,NULL,2,'2024-08-21 19:13:14','2024-08-21 19:13:14');
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
  `billingContactName` varchar(255) DEFAULT NULL,
  `billingAddress1` varchar(255) DEFAULT NULL,
  `billingAddress2` varchar(255) DEFAULT NULL,
  `billingAddress3` varchar(255) DEFAULT NULL,
  `billingTownCity` varchar(255) DEFAULT NULL,
  `billingPostcode` varchar(255) DEFAULT NULL,
  `billingCountry` varchar(255) DEFAULT NULL,
  `billingEmail` varchar(255) DEFAULT NULL,
  `billingTelephone` varchar(255) DEFAULT NULL,
  `vatNumber` varchar(255) DEFAULT NULL,
  `vatRate` varchar(255) DEFAULT NULL,
  `purchaseOrderRequired` tinyint(1) NOT NULL DEFAULT '0',
  `purchaseOrderNumber` varchar(50) DEFAULT NULL,
  `invoiceMethod` varchar(255) DEFAULT NULL,
  `invoiceEmail` varchar(255) DEFAULT NULL,
  `billingFrequency` varchar(255) DEFAULT NULL,
  `notes` longtext,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `company_billing_detail`
--

LOCK TABLES `company_billing_detail` WRITE;
/*!40000 ALTER TABLE `company_billing_detail` DISABLE KEYS */;
INSERT INTO `company_billing_detail` VALUES (1,'Vagrant Superadmin',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,'EMAIL',NULL,'NOBILLING',NULL,'2024-08-21 13:46:48','2024-08-21 13:46:48'),(2,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,'2024-08-21 19:39:41','2024-08-21 19:39:41'),(3,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,'2024-08-21 20:08:58','2024-08-21 20:08:58'),(4,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,'2024-08-21 20:27:04','2024-08-21 20:27:04'),(5,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,'2024-08-21 20:33:17','2024-08-21 20:33:17'),(6,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,'2024-08-21 20:48:58','2024-08-21 20:48:58'),(7,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,'2024-08-21 21:04:53','2024-08-21 21:04:53'),(8,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,'2024-08-21 21:10:32','2024-08-21 21:10:32');
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
  `registeredName` varchar(255) DEFAULT NULL,
  `companyNumber` varchar(255) DEFAULT NULL,
  `jurisdiction` varchar(255) DEFAULT NULL,
  `address1` varchar(255) DEFAULT NULL,
  `address2` varchar(255) DEFAULT NULL,
  `address3` varchar(255) DEFAULT NULL,
  `townCity` varchar(255) DEFAULT NULL,
  `postcode` varchar(255) DEFAULT NULL,
  `country` varchar(255) DEFAULT NULL,
  `notes` longtext,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `company_registration_detail`
--

LOCK TABLES `company_registration_detail` WRITE;
/*!40000 ALTER TABLE `company_registration_detail` DISABLE KEYS */;
INSERT INTO `company_registration_detail` VALUES (1,'VAGRANTIX',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2024-08-21 13:46:40','2024-08-21 13:46:40'),(2,'AS112',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2024-08-21 19:39:41','2024-08-21 19:39:41'),(3,'NREN',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2024-08-21 20:08:58','2024-08-21 20:08:58'),(4,'Eyeball ISP',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2024-08-21 20:27:04','2024-08-21 20:27:04'),(5,'CDN',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2024-08-21 20:33:17','2024-08-21 20:33:17'),(6,'Regional WISP',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2024-08-21 20:48:58','2024-08-21 20:48:58'),(7,'VAGRANTIX Route Servers',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2024-08-21 21:04:53','2024-08-21 21:04:53'),(8,'Associate Member',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2024-08-21 21:10:32','2024-08-21 21:10:32');
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
  `name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `hostname` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `model` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `serialNumber` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `active` tinyint(1) DEFAULT '1',
  `notes` longtext CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_92A539235E237E06` (`name`),
  KEY `IDX_92A53923F603EE73` (`vendor_id`),
  KEY `IDX_92A53923D351EC` (`cabinet_id`),
  CONSTRAINT `FK_92A53923D351EC` FOREIGN KEY (`cabinet_id`) REFERENCES `cabinet` (`id`),
  CONSTRAINT `FK_92A53923F603EE73` FOREIGN KEY (`vendor_id`) REFERENCES `vendor` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
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
  `description` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `port` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `speed` int DEFAULT NULL,
  `parity` int DEFAULT NULL,
  `stopbits` int DEFAULT NULL,
  `flowcontrol` int DEFAULT NULL,
  `autobaud` tinyint(1) DEFAULT NULL,
  `notes` longtext CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `console_server_id` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `console_server_port_uniq` (`console_server_id`,`port`),
  KEY `IDX_530316DCDA0209B9` (`custid`),
  KEY `IDX_530316DCF472E7C6` (`console_server_id`),
  CONSTRAINT `FK_530316DCDA0209B9` FOREIGN KEY (`custid`) REFERENCES `cust` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_530316DCF472E7C6` FOREIGN KEY (`console_server_id`) REFERENCES `console_server` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
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
  `name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `email` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `phone` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `mobile` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `facilityaccess` tinyint(1) NOT NULL DEFAULT '0',
  `mayauthorize` tinyint(1) NOT NULL DEFAULT '0',
  `updated_at` datetime DEFAULT NULL,
  `lastupdatedby` int DEFAULT NULL,
  `creator` varchar(32) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `position` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `notes` longtext CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  PRIMARY KEY (`id`),
  KEY `IDX_4C62E638DA0209B9` (`custid`),
  CONSTRAINT `FK_4C62E638DA0209B9` FOREIGN KEY (`custid`) REFERENCES `cust` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `contact`
--

LOCK TABLES `contact` WRITE;
/*!40000 ALTER TABLE `contact` DISABLE KEYS */;
INSERT INTO `contact` VALUES (1,1,'Vagrant Superuser','vagrant@example.net',NULL,NULL,0,0,'2024-08-21 08:53:33',NULL,NULL,'2024-08-21 08:53:33',NULL,NULL);
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
  `name` varchar(20) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `description` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `type` varchar(20) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `limited_to` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_40EA54CA5E237E06` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `contact_group`
--

LOCK TABLES `contact_group` WRITE;
/*!40000 ALTER TABLE `contact_group` DISABLE KEYS */;
INSERT INTO `contact_group` VALUES (1,'Billing','Contact role for billing matters','ROLE',1,0,'2024-08-21 18:53:50','2024-08-21 18:53:50'),(2,'Technical','Contact role for technical matters','ROLE',1,0,'2024-08-21 18:53:50','2024-08-21 18:53:50'),(3,'Admin','Contact role for admin matters','ROLE',1,0,'2024-08-21 18:53:50','2024-08-21 18:53:50'),(4,'Marketing','Contact role for marketing matters','ROLE',1,0,'2024-08-21 18:53:50','2024-08-21 18:53:50');
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
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
  `description` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `type` int NOT NULL,
  `graph_title` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `bfd` tinyint(1) NOT NULL DEFAULT '0',
  `ipv4_subnet` varchar(18) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `ipv6_subnet` varchar(43) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `stp` tinyint(1) NOT NULL DEFAULT '0',
  `cost` int unsigned DEFAULT NULL,
  `preference` int unsigned DEFAULT NULL,
  `enabled` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `corebundles`
--

LOCK TABLES `corebundles` WRITE;
/*!40000 ALTER TABLE `corebundles` DISABLE KEYS */;
INSERT INTO `corebundles` VALUES (1,'Core: VIX1 - FAC1 - FAC2',2,'Core: VIX1 - FAC1 - FAC2',0,NULL,NULL,0,NULL,NULL,1,'2024-08-21 21:29:17','2024-08-21 21:29:17');
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
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `coreinterfaces`
--

LOCK TABLES `coreinterfaces` WRITE;
/*!40000 ALTER TABLE `coreinterfaces` DISABLE KEYS */;
INSERT INTO `coreinterfaces` VALUES (1,18,'2024-08-21 21:29:17','2024-08-21 21:29:17'),(2,19,'2024-08-21 21:29:17','2024-08-21 21:29:17'),(3,20,'2024-08-21 21:29:17','2024-08-21 21:29:17'),(4,21,'2024-08-21 21:29:17','2024-08-21 21:29:17');
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
  `ipv4_subnet` varchar(18) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `ipv6_subnet` varchar(43) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
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
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `corelinks`
--

LOCK TABLES `corelinks` WRITE;
/*!40000 ALTER TABLE `corelinks` DISABLE KEYS */;
INSERT INTO `corelinks` VALUES (1,1,2,1,0,NULL,NULL,1,'2024-08-21 21:29:17','2024-08-21 21:29:17'),(2,3,4,1,0,NULL,NULL,1,'2024-08-21 21:29:17','2024-08-21 21:29:17');
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
  `name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `type` int DEFAULT NULL,
  `shortname` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `autsys` int DEFAULT NULL,
  `maxprefixes` int DEFAULT NULL,
  `peeringemail` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `nocphone` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `noc24hphone` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `nocfax` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `nocemail` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `nochours` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `nocwww` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `irrdb` int DEFAULT NULL,
  `peeringmacro` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `peeringpolicy` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `corpwww` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `datejoin` date DEFAULT NULL,
  `dateleave` date DEFAULT NULL,
  `status` smallint DEFAULT NULL,
  `activepeeringmatrix` tinyint(1) DEFAULT NULL,
  `lastupdatedby` int DEFAULT NULL,
  `creator` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `company_registered_detail_id` int DEFAULT NULL,
  `company_billing_details_id` int DEFAULT NULL,
  `peeringmacrov6` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `abbreviatedName` varchar(30) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `MD5Support` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT 'UNKNOWN',
  `reseller` int DEFAULT NULL,
  `isReseller` tinyint(1) NOT NULL DEFAULT '0',
  `in_manrs` tinyint(1) NOT NULL DEFAULT '0',
  `in_peeringdb` tinyint(1) NOT NULL DEFAULT '0',
  `peeringdb_oauth` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL,
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
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cust`
--

LOCK TABLES `cust` WRITE;
/*!40000 ALTER TABLE `cust` DISABLE KEYS */;
INSERT INTO `cust` VALUES (1,'VAGRANTIX',3,'VAGRANTIX',65500,100,'peering@example.net','12345678','12345678',NULL,'noc@example.net','24x7','',NULL,NULL,'mandatory','http://127.0.0.1:8088','2024-08-21',NULL,1,1,NULL,NULL,1,1,NULL,'VAGRANTIX','UNKNOWN',NULL,0,0,0,1,'2024-08-21 13:51:52','2024-08-21 13:51:52'),(2,'AS112',4,'dnsoarc112',112,20,'peering@example.com',NULL,NULL,NULL,'noc@example.com',NULL,NULL,3,'AS112','open','https://www.as112.net/','2024-01-01',NULL,1,1,1,'1',2,2,NULL,'AS112','NO',NULL,0,0,0,1,'2024-08-21 19:39:41','2024-08-21 19:50:10'),(3,'NREN',1,'nren',1213,100,'peering@example.com',NULL,NULL,NULL,'noc@example.com',NULL,NULL,1,'AS-HEANET','selective','https://nren.example.com/','2024-08-01',NULL,1,1,1,'1',3,3,NULL,'NREN','YES',NULL,0,0,0,1,'2024-08-21 20:08:58','2024-08-21 21:03:32'),(4,'Eyeball ISP',1,'eyeballisp',25441,50,'peering@example.com',NULL,NULL,NULL,'noc@example.com',NULL,NULL,1,'AS-IBIS','open','https://eyeballisp.example.com/','2024-01-01',NULL,1,1,NULL,'1',4,4,NULL,'Eyeball ISP','YES',NULL,0,0,0,1,'2024-08-21 20:27:04','2024-08-21 20:27:04'),(5,'CDN',1,'cdn',2906,500,'peering@example.com',NULL,NULL,NULL,'noc@example.com',NULL,NULL,3,'AS-NFLX','open','https://cdn.example.com/','2024-02-01',NULL,1,1,NULL,'1',5,5,NULL,'CDN','YES',NULL,0,0,0,1,'2024-08-21 20:33:17','2024-08-21 20:33:17'),(6,'Regional WISP',1,'regionalwisp',39093,10,'peering@example.com',NULL,NULL,NULL,'noc@example.com',NULL,NULL,1,'AS-WESTNET','open','http://regionalwisp.example.com/','2024-03-01',NULL,1,1,1,'1',6,6,NULL,'R-WISP','YES',NULL,0,0,0,1,'2024-08-21 20:48:58','2024-08-21 20:52:16'),(7,'VAGRANTIX Route Servers',3,'routeservers',65501,100000,'peering@example.com',NULL,NULL,NULL,'noc@example.com',NULL,NULL,1,NULL,'open','https://vagrantix.example.com/','2024-01-01',NULL,1,1,NULL,'1',7,7,NULL,'VAGRANTIX RS','YES',NULL,0,0,0,1,'2024-08-21 21:04:53','2024-08-21 21:04:53'),(8,'Associate Member',2,'associate',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'https://associate.example.com/','2024-04-01',NULL,2,1,NULL,'1',8,8,NULL,'Associate Member','UNKNOWN',NULL,0,0,0,1,'2024-08-21 21:10:32','2024-08-21 21:10:32');
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
  `title` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `note` longtext CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_6377D8679395C3F3` (`customer_id`),
  CONSTRAINT `FK_6377D8679395C3F3` FOREIGN KEY (`customer_id`) REFERENCES `cust` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
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
  `tag` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `display_as` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `description` longtext CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `internal_only` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_6B54CFB8389B783` (`tag`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cust_tag`
--

LOCK TABLES `cust_tag` WRITE;
/*!40000 ALTER TABLE `cust_tag` DISABLE KEYS */;
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cust_to_cust_tag`
--

LOCK TABLES `cust_to_cust_tag` WRITE;
/*!40000 ALTER TABLE `cust_to_cust_tag` DISABLE KEYS */;
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
  `name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `descr` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_8127F9AADA0209B9` (`custid`),
  KEY `IDX_8127F9AA2B96718A` (`cabinetid`),
  CONSTRAINT `FK_8127F9AA2B96718A` FOREIGN KEY (`cabinetid`) REFERENCES `cabinet` (`id`),
  CONSTRAINT `FK_8127F9AADA0209B9` FOREIGN KEY (`custid`) REFERENCES `cust` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
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
  `extra_attributes` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT '(DC2Type:json)',
  `last_login_date` datetime DEFAULT NULL,
  `last_login_from` tinytext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `last_login_via` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `customer_user` (`customer_id`,`user_id`),
  KEY `IDX_337AD7F69395C3F3` (`customer_id`),
  KEY `IDX_337AD7F6A76ED395` (`user_id`),
  CONSTRAINT `FK_337AD7F69395C3F3` FOREIGN KEY (`customer_id`) REFERENCES `cust` (`id`),
  CONSTRAINT `FK_337AD7F6A76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `customer_to_users`
--

LOCK TABLES `customer_to_users` WRITE;
/*!40000 ALTER TABLE `customer_to_users` DISABLE KEYS */;
INSERT INTO `customer_to_users` VALUES (1,1,1,3,NULL,'2024-08-21 15:06:56','10.211.55.1','Login','2024-08-21 13:53:06','2024-08-21 20:06:56'),(2,2,2,2,'{\"created_by\":{\"type\":\"user\",\"user_id\":2}}','2024-08-21 15:05:59','10.211.55.1','Login','2024-08-21 19:51:20','2024-08-21 20:05:59'),(3,2,3,1,'{\"created_by\":{\"type\":\"user\",\"user_id\":3}}','2024-08-21 15:06:34','10.211.55.1','Login','2024-08-21 20:04:17','2024-08-21 20:06:34');
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `docstore_customer_directories`
--

LOCK TABLES `docstore_customer_directories` WRITE;
/*!40000 ALTER TABLE `docstore_customer_directories` DISABLE KEYS */;
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `docstore_customer_files`
--

LOCK TABLES `docstore_customer_files` WRITE;
/*!40000 ALTER TABLE `docstore_customer_files` DISABLE KEYS */;
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `docstore_directories`
--

LOCK TABLES `docstore_directories` WRITE;
/*!40000 ALTER TABLE `docstore_directories` DISABLE KEYS */;
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `docstore_files`
--

LOCK TABLES `docstore_files` WRITE;
/*!40000 ALTER TABLE `docstore_files` DISABLE KEYS */;
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
  KEY `docstore_logs_created_at_index` (`created_at`),
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
  `name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `shortname` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `isPrimary` tinyint(1) NOT NULL DEFAULT '0',
  `peeringdb_ix_id` bigint DEFAULT NULL,
  `ixf_ix_id` bigint DEFAULT NULL,
  `country` varchar(2) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `notes` longtext COLLATE utf8mb3_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `IXPSN` (`shortname`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `infrastructure`
--

LOCK TABLES `infrastructure` WRITE;
/*!40000 ALTER TABLE `infrastructure` DISABLE KEYS */;
INSERT INTO `infrastructure` VALUES (1,'VAGRANT IX1','VIX1',1,NULL,NULL,NULL,NULL,'2024-08-21 13:46:32','2024-08-21 13:46:32'),(2,'VAGRANT IX2','VIX2',0,NULL,NULL,'IE',NULL,'2024-08-21 19:17:13','2024-08-21 19:17:13');
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
  `address` varchar(16) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `vlan_address` (`vlanid`,`address`),
  KEY `IDX_A44BCBEEF48D6D0` (`vlanid`),
  CONSTRAINT `FK_A44BCBEEF48D6D0` FOREIGN KEY (`vlanid`) REFERENCES `vlan` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=133 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ipv4address`
--

LOCK TABLES `ipv4address` WRITE;
/*!40000 ALTER TABLE `ipv4address` DISABLE KEYS */;
INSERT INTO `ipv4address` VALUES (1,1,'192.0.2.0','2024-08-21 19:36:15','2024-08-21 19:36:15'),(2,1,'192.0.2.1','2024-08-21 19:36:15','2024-08-21 19:36:15'),(3,1,'192.0.2.2','2024-08-21 19:36:15','2024-08-21 19:36:15'),(4,1,'192.0.2.3','2024-08-21 19:36:15','2024-08-21 19:36:15'),(5,1,'192.0.2.4','2024-08-21 19:36:15','2024-08-21 19:36:15'),(6,1,'192.0.2.5','2024-08-21 19:36:15','2024-08-21 19:36:15'),(7,1,'192.0.2.6','2024-08-21 19:36:15','2024-08-21 19:36:15'),(8,1,'192.0.2.7','2024-08-21 19:36:15','2024-08-21 19:36:15'),(9,1,'192.0.2.8','2024-08-21 19:36:15','2024-08-21 19:36:15'),(10,1,'192.0.2.9','2024-08-21 19:36:15','2024-08-21 19:36:15'),(11,1,'192.0.2.10','2024-08-21 19:36:15','2024-08-21 19:36:15'),(12,1,'192.0.2.11','2024-08-21 19:36:15','2024-08-21 19:36:15'),(13,1,'192.0.2.12','2024-08-21 19:36:15','2024-08-21 19:36:15'),(14,1,'192.0.2.13','2024-08-21 19:36:15','2024-08-21 19:36:15'),(15,1,'192.0.2.14','2024-08-21 19:36:15','2024-08-21 19:36:15'),(16,1,'192.0.2.15','2024-08-21 19:36:15','2024-08-21 19:36:15'),(17,1,'192.0.2.16','2024-08-21 19:36:15','2024-08-21 19:36:15'),(18,1,'192.0.2.17','2024-08-21 19:36:15','2024-08-21 19:36:15'),(19,1,'192.0.2.18','2024-08-21 19:36:15','2024-08-21 19:36:15'),(20,1,'192.0.2.19','2024-08-21 19:36:15','2024-08-21 19:36:15'),(21,1,'192.0.2.20','2024-08-21 19:36:15','2024-08-21 19:36:15'),(22,1,'192.0.2.21','2024-08-21 19:36:15','2024-08-21 19:36:15'),(23,1,'192.0.2.22','2024-08-21 19:36:15','2024-08-21 19:36:15'),(24,1,'192.0.2.23','2024-08-21 19:36:15','2024-08-21 19:36:15'),(25,1,'192.0.2.24','2024-08-21 19:36:15','2024-08-21 19:36:15'),(26,1,'192.0.2.25','2024-08-21 19:36:15','2024-08-21 19:36:15'),(27,1,'192.0.2.26','2024-08-21 19:36:15','2024-08-21 19:36:15'),(28,1,'192.0.2.27','2024-08-21 19:36:15','2024-08-21 19:36:15'),(29,1,'192.0.2.28','2024-08-21 19:36:15','2024-08-21 19:36:15'),(30,1,'192.0.2.29','2024-08-21 19:36:15','2024-08-21 19:36:15'),(31,1,'192.0.2.30','2024-08-21 19:36:15','2024-08-21 19:36:15'),(32,1,'192.0.2.31','2024-08-21 19:36:15','2024-08-21 19:36:15'),(33,3,'192.0.2.0','2024-08-21 19:36:28','2024-08-21 19:36:28'),(34,3,'192.0.2.1','2024-08-21 19:36:28','2024-08-21 19:36:28'),(35,3,'192.0.2.2','2024-08-21 19:36:28','2024-08-21 19:36:28'),(36,3,'192.0.2.3','2024-08-21 19:36:28','2024-08-21 19:36:28'),(37,3,'192.0.2.4','2024-08-21 19:36:28','2024-08-21 19:36:28'),(38,3,'192.0.2.5','2024-08-21 19:36:28','2024-08-21 19:36:28'),(39,3,'192.0.2.6','2024-08-21 19:36:28','2024-08-21 19:36:28'),(40,3,'192.0.2.7','2024-08-21 19:36:28','2024-08-21 19:36:28'),(41,3,'192.0.2.8','2024-08-21 19:36:28','2024-08-21 19:36:28'),(42,3,'192.0.2.9','2024-08-21 19:36:28','2024-08-21 19:36:28'),(43,3,'192.0.2.10','2024-08-21 19:36:28','2024-08-21 19:36:28'),(44,3,'192.0.2.11','2024-08-21 19:36:28','2024-08-21 19:36:28'),(45,3,'192.0.2.12','2024-08-21 19:36:28','2024-08-21 19:36:28'),(46,3,'192.0.2.13','2024-08-21 19:36:28','2024-08-21 19:36:28'),(47,3,'192.0.2.14','2024-08-21 19:36:28','2024-08-21 19:36:28'),(48,3,'192.0.2.15','2024-08-21 19:36:28','2024-08-21 19:36:28'),(49,3,'192.0.2.16','2024-08-21 19:36:28','2024-08-21 19:36:28'),(50,3,'192.0.2.17','2024-08-21 19:36:28','2024-08-21 19:36:28'),(51,3,'192.0.2.18','2024-08-21 19:36:28','2024-08-21 19:36:28'),(52,3,'192.0.2.19','2024-08-21 19:36:28','2024-08-21 19:36:28'),(53,3,'192.0.2.20','2024-08-21 19:36:28','2024-08-21 19:36:28'),(54,3,'192.0.2.21','2024-08-21 19:36:28','2024-08-21 19:36:28'),(55,3,'192.0.2.22','2024-08-21 19:36:28','2024-08-21 19:36:28'),(56,3,'192.0.2.23','2024-08-21 19:36:28','2024-08-21 19:36:28'),(57,3,'192.0.2.24','2024-08-21 19:36:28','2024-08-21 19:36:28'),(58,3,'192.0.2.25','2024-08-21 19:36:28','2024-08-21 19:36:28'),(59,3,'192.0.2.26','2024-08-21 19:36:28','2024-08-21 19:36:28'),(60,3,'192.0.2.27','2024-08-21 19:36:28','2024-08-21 19:36:28'),(61,3,'192.0.2.28','2024-08-21 19:36:28','2024-08-21 19:36:28'),(62,3,'192.0.2.29','2024-08-21 19:36:28','2024-08-21 19:36:28'),(63,3,'192.0.2.30','2024-08-21 19:36:28','2024-08-21 19:36:28'),(64,3,'192.0.2.31','2024-08-21 19:36:28','2024-08-21 19:36:28'),(65,2,'198.51.100.0','2024-08-21 19:36:42','2024-08-21 19:36:42'),(66,2,'198.51.100.1','2024-08-21 19:36:42','2024-08-21 19:36:42'),(67,2,'198.51.100.2','2024-08-21 19:36:42','2024-08-21 19:36:42'),(68,2,'198.51.100.3','2024-08-21 19:36:42','2024-08-21 19:36:42'),(69,2,'198.51.100.4','2024-08-21 19:36:42','2024-08-21 19:36:42'),(70,2,'198.51.100.5','2024-08-21 19:36:42','2024-08-21 19:36:42'),(71,2,'198.51.100.6','2024-08-21 19:36:42','2024-08-21 19:36:42'),(72,2,'198.51.100.7','2024-08-21 19:36:42','2024-08-21 19:36:42'),(73,2,'198.51.100.8','2024-08-21 19:36:42','2024-08-21 19:36:42'),(74,2,'198.51.100.9','2024-08-21 19:36:42','2024-08-21 19:36:42'),(75,2,'198.51.100.10','2024-08-21 19:36:42','2024-08-21 19:36:42'),(76,2,'198.51.100.11','2024-08-21 19:36:42','2024-08-21 19:36:42'),(77,2,'198.51.100.12','2024-08-21 19:36:42','2024-08-21 19:36:42'),(78,2,'198.51.100.13','2024-08-21 19:36:42','2024-08-21 19:36:42'),(79,2,'198.51.100.14','2024-08-21 19:36:42','2024-08-21 19:36:42'),(80,2,'198.51.100.15','2024-08-21 19:36:42','2024-08-21 19:36:42'),(81,2,'198.51.100.16','2024-08-21 19:36:42','2024-08-21 19:36:42'),(82,2,'198.51.100.17','2024-08-21 19:36:42','2024-08-21 19:36:42'),(83,2,'198.51.100.18','2024-08-21 19:36:42','2024-08-21 19:36:42'),(84,2,'198.51.100.19','2024-08-21 19:36:42','2024-08-21 19:36:42'),(85,2,'198.51.100.20','2024-08-21 19:36:42','2024-08-21 19:36:42'),(86,2,'198.51.100.21','2024-08-21 19:36:42','2024-08-21 19:36:42'),(87,2,'198.51.100.22','2024-08-21 19:36:42','2024-08-21 19:36:42'),(88,2,'198.51.100.23','2024-08-21 19:36:42','2024-08-21 19:36:42'),(89,2,'198.51.100.24','2024-08-21 19:36:42','2024-08-21 19:36:42'),(90,2,'198.51.100.25','2024-08-21 19:36:42','2024-08-21 19:36:42'),(91,2,'198.51.100.26','2024-08-21 19:36:42','2024-08-21 19:36:42'),(92,2,'198.51.100.27','2024-08-21 19:36:42','2024-08-21 19:36:42'),(93,2,'198.51.100.28','2024-08-21 19:36:42','2024-08-21 19:36:42'),(94,2,'198.51.100.29','2024-08-21 19:36:42','2024-08-21 19:36:42'),(95,2,'198.51.100.30','2024-08-21 19:36:42','2024-08-21 19:36:42'),(96,2,'198.51.100.31','2024-08-21 19:36:42','2024-08-21 19:36:42'),(97,4,'198.51.100.0','2024-08-21 19:36:47','2024-08-21 19:36:47'),(98,4,'198.51.100.1','2024-08-21 19:36:47','2024-08-21 19:36:47'),(99,4,'198.51.100.2','2024-08-21 19:36:47','2024-08-21 19:36:47'),(100,4,'198.51.100.3','2024-08-21 19:36:47','2024-08-21 19:36:47'),(101,4,'198.51.100.4','2024-08-21 19:36:47','2024-08-21 19:36:47'),(102,4,'198.51.100.5','2024-08-21 19:36:47','2024-08-21 19:36:47'),(103,4,'198.51.100.6','2024-08-21 19:36:47','2024-08-21 19:36:47'),(104,4,'198.51.100.7','2024-08-21 19:36:47','2024-08-21 19:36:47'),(105,4,'198.51.100.8','2024-08-21 19:36:47','2024-08-21 19:36:47'),(106,4,'198.51.100.9','2024-08-21 19:36:47','2024-08-21 19:36:47'),(107,4,'198.51.100.10','2024-08-21 19:36:47','2024-08-21 19:36:47'),(108,4,'198.51.100.11','2024-08-21 19:36:47','2024-08-21 19:36:47'),(109,4,'198.51.100.12','2024-08-21 19:36:47','2024-08-21 19:36:47'),(110,4,'198.51.100.13','2024-08-21 19:36:47','2024-08-21 19:36:47'),(111,4,'198.51.100.14','2024-08-21 19:36:47','2024-08-21 19:36:47'),(112,4,'198.51.100.15','2024-08-21 19:36:47','2024-08-21 19:36:47'),(113,4,'198.51.100.16','2024-08-21 19:36:47','2024-08-21 19:36:47'),(114,4,'198.51.100.17','2024-08-21 19:36:47','2024-08-21 19:36:47'),(115,4,'198.51.100.18','2024-08-21 19:36:47','2024-08-21 19:36:47'),(116,4,'198.51.100.19','2024-08-21 19:36:47','2024-08-21 19:36:47'),(117,4,'198.51.100.20','2024-08-21 19:36:47','2024-08-21 19:36:47'),(118,4,'198.51.100.21','2024-08-21 19:36:47','2024-08-21 19:36:47'),(119,4,'198.51.100.22','2024-08-21 19:36:47','2024-08-21 19:36:47'),(120,4,'198.51.100.23','2024-08-21 19:36:47','2024-08-21 19:36:47'),(121,4,'198.51.100.24','2024-08-21 19:36:47','2024-08-21 19:36:47'),(122,4,'198.51.100.25','2024-08-21 19:36:47','2024-08-21 19:36:47'),(123,4,'198.51.100.26','2024-08-21 19:36:47','2024-08-21 19:36:47'),(124,4,'198.51.100.27','2024-08-21 19:36:47','2024-08-21 19:36:47'),(125,4,'198.51.100.28','2024-08-21 19:36:47','2024-08-21 19:36:47'),(126,4,'198.51.100.29','2024-08-21 19:36:47','2024-08-21 19:36:47'),(127,4,'198.51.100.30','2024-08-21 19:36:47','2024-08-21 19:36:47'),(128,4,'198.51.100.31','2024-08-21 19:36:47','2024-08-21 19:36:47'),(129,1,'192.0.2.126','2024-08-21 20:54:04','2024-08-21 20:54:04'),(130,3,'192.0.2.126','2024-08-21 20:54:10','2024-08-21 20:54:10'),(131,2,'198.51.100.126','2024-08-21 20:55:01','2024-08-21 20:55:01'),(132,4,'198.51.100.126','2024-08-21 20:55:06','2024-08-21 20:55:06');
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
  `address` varchar(40) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `vlan_address` (`vlanid`,`address`),
  KEY `IDX_E66ECC93F48D6D0` (`vlanid`),
  CONSTRAINT `FK_E66ECC93F48D6D0` FOREIGN KEY (`vlanid`) REFERENCES `vlan` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=133 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ipv6address`
--

LOCK TABLES `ipv6address` WRITE;
/*!40000 ALTER TABLE `ipv6address` DISABLE KEYS */;
INSERT INTO `ipv6address` VALUES (1,1,'2001:db8:0:10::','2024-08-21 19:37:24','2024-08-21 19:37:24'),(2,1,'2001:db8:0:10::1','2024-08-21 19:37:24','2024-08-21 19:37:24'),(3,1,'2001:db8:0:10::2','2024-08-21 19:37:24','2024-08-21 19:37:24'),(4,1,'2001:db8:0:10::3','2024-08-21 19:37:24','2024-08-21 19:37:24'),(5,1,'2001:db8:0:10::4','2024-08-21 19:37:24','2024-08-21 19:37:24'),(6,1,'2001:db8:0:10::5','2024-08-21 19:37:24','2024-08-21 19:37:24'),(7,1,'2001:db8:0:10::6','2024-08-21 19:37:24','2024-08-21 19:37:24'),(8,1,'2001:db8:0:10::7','2024-08-21 19:37:24','2024-08-21 19:37:24'),(9,1,'2001:db8:0:10::8','2024-08-21 19:37:24','2024-08-21 19:37:24'),(10,1,'2001:db8:0:10::9','2024-08-21 19:37:24','2024-08-21 19:37:24'),(11,1,'2001:db8:0:10::10','2024-08-21 19:37:24','2024-08-21 19:37:24'),(12,1,'2001:db8:0:10::11','2024-08-21 19:37:24','2024-08-21 19:37:24'),(13,1,'2001:db8:0:10::12','2024-08-21 19:37:24','2024-08-21 19:37:24'),(14,1,'2001:db8:0:10::13','2024-08-21 19:37:24','2024-08-21 19:37:24'),(15,1,'2001:db8:0:10::14','2024-08-21 19:37:24','2024-08-21 19:37:24'),(16,1,'2001:db8:0:10::15','2024-08-21 19:37:24','2024-08-21 19:37:24'),(17,1,'2001:db8:0:10::16','2024-08-21 19:37:24','2024-08-21 19:37:24'),(18,1,'2001:db8:0:10::17','2024-08-21 19:37:24','2024-08-21 19:37:24'),(19,1,'2001:db8:0:10::18','2024-08-21 19:37:24','2024-08-21 19:37:24'),(20,1,'2001:db8:0:10::19','2024-08-21 19:37:24','2024-08-21 19:37:24'),(21,1,'2001:db8:0:10::20','2024-08-21 19:37:24','2024-08-21 19:37:24'),(22,1,'2001:db8:0:10::21','2024-08-21 19:37:24','2024-08-21 19:37:24'),(23,1,'2001:db8:0:10::22','2024-08-21 19:37:24','2024-08-21 19:37:24'),(24,1,'2001:db8:0:10::23','2024-08-21 19:37:24','2024-08-21 19:37:24'),(25,1,'2001:db8:0:10::24','2024-08-21 19:37:24','2024-08-21 19:37:24'),(26,1,'2001:db8:0:10::25','2024-08-21 19:37:24','2024-08-21 19:37:24'),(27,1,'2001:db8:0:10::26','2024-08-21 19:37:24','2024-08-21 19:37:24'),(28,1,'2001:db8:0:10::27','2024-08-21 19:37:24','2024-08-21 19:37:24'),(29,1,'2001:db8:0:10::28','2024-08-21 19:37:24','2024-08-21 19:37:24'),(30,1,'2001:db8:0:10::29','2024-08-21 19:37:24','2024-08-21 19:37:24'),(31,1,'2001:db8:0:10::30','2024-08-21 19:37:24','2024-08-21 19:37:24'),(32,1,'2001:db8:0:10::31','2024-08-21 19:37:24','2024-08-21 19:37:24'),(33,3,'2001:db8:0:10::','2024-08-21 19:37:39','2024-08-21 19:37:39'),(34,3,'2001:db8:0:10::1','2024-08-21 19:37:39','2024-08-21 19:37:39'),(35,3,'2001:db8:0:10::2','2024-08-21 19:37:39','2024-08-21 19:37:39'),(36,3,'2001:db8:0:10::3','2024-08-21 19:37:39','2024-08-21 19:37:39'),(37,3,'2001:db8:0:10::4','2024-08-21 19:37:39','2024-08-21 19:37:39'),(38,3,'2001:db8:0:10::5','2024-08-21 19:37:39','2024-08-21 19:37:39'),(39,3,'2001:db8:0:10::6','2024-08-21 19:37:39','2024-08-21 19:37:39'),(40,3,'2001:db8:0:10::7','2024-08-21 19:37:39','2024-08-21 19:37:39'),(41,3,'2001:db8:0:10::8','2024-08-21 19:37:39','2024-08-21 19:37:39'),(42,3,'2001:db8:0:10::9','2024-08-21 19:37:39','2024-08-21 19:37:39'),(43,3,'2001:db8:0:10::10','2024-08-21 19:37:39','2024-08-21 19:37:39'),(44,3,'2001:db8:0:10::11','2024-08-21 19:37:39','2024-08-21 19:37:39'),(45,3,'2001:db8:0:10::12','2024-08-21 19:37:39','2024-08-21 19:37:39'),(46,3,'2001:db8:0:10::13','2024-08-21 19:37:39','2024-08-21 19:37:39'),(47,3,'2001:db8:0:10::14','2024-08-21 19:37:39','2024-08-21 19:37:39'),(48,3,'2001:db8:0:10::15','2024-08-21 19:37:39','2024-08-21 19:37:39'),(49,3,'2001:db8:0:10::16','2024-08-21 19:37:39','2024-08-21 19:37:39'),(50,3,'2001:db8:0:10::17','2024-08-21 19:37:39','2024-08-21 19:37:39'),(51,3,'2001:db8:0:10::18','2024-08-21 19:37:39','2024-08-21 19:37:39'),(52,3,'2001:db8:0:10::19','2024-08-21 19:37:39','2024-08-21 19:37:39'),(53,3,'2001:db8:0:10::20','2024-08-21 19:37:39','2024-08-21 19:37:39'),(54,3,'2001:db8:0:10::21','2024-08-21 19:37:39','2024-08-21 19:37:39'),(55,3,'2001:db8:0:10::22','2024-08-21 19:37:39','2024-08-21 19:37:39'),(56,3,'2001:db8:0:10::23','2024-08-21 19:37:39','2024-08-21 19:37:39'),(57,3,'2001:db8:0:10::24','2024-08-21 19:37:39','2024-08-21 19:37:39'),(58,3,'2001:db8:0:10::25','2024-08-21 19:37:39','2024-08-21 19:37:39'),(59,3,'2001:db8:0:10::26','2024-08-21 19:37:39','2024-08-21 19:37:39'),(60,3,'2001:db8:0:10::27','2024-08-21 19:37:39','2024-08-21 19:37:39'),(61,3,'2001:db8:0:10::28','2024-08-21 19:37:39','2024-08-21 19:37:39'),(62,3,'2001:db8:0:10::29','2024-08-21 19:37:39','2024-08-21 19:37:39'),(63,3,'2001:db8:0:10::30','2024-08-21 19:37:39','2024-08-21 19:37:39'),(64,3,'2001:db8:0:10::31','2024-08-21 19:37:39','2024-08-21 19:37:39'),(65,2,'2001:db8:0:20::','2024-08-21 19:37:50','2024-08-21 19:37:50'),(66,2,'2001:db8:0:20::1','2024-08-21 19:37:50','2024-08-21 19:37:50'),(67,2,'2001:db8:0:20::2','2024-08-21 19:37:50','2024-08-21 19:37:50'),(68,2,'2001:db8:0:20::3','2024-08-21 19:37:50','2024-08-21 19:37:50'),(69,2,'2001:db8:0:20::4','2024-08-21 19:37:50','2024-08-21 19:37:50'),(70,2,'2001:db8:0:20::5','2024-08-21 19:37:50','2024-08-21 19:37:50'),(71,2,'2001:db8:0:20::6','2024-08-21 19:37:50','2024-08-21 19:37:50'),(72,2,'2001:db8:0:20::7','2024-08-21 19:37:50','2024-08-21 19:37:50'),(73,2,'2001:db8:0:20::8','2024-08-21 19:37:50','2024-08-21 19:37:50'),(74,2,'2001:db8:0:20::9','2024-08-21 19:37:50','2024-08-21 19:37:50'),(75,2,'2001:db8:0:20::10','2024-08-21 19:37:50','2024-08-21 19:37:50'),(76,2,'2001:db8:0:20::11','2024-08-21 19:37:50','2024-08-21 19:37:50'),(77,2,'2001:db8:0:20::12','2024-08-21 19:37:50','2024-08-21 19:37:50'),(78,2,'2001:db8:0:20::13','2024-08-21 19:37:50','2024-08-21 19:37:50'),(79,2,'2001:db8:0:20::14','2024-08-21 19:37:50','2024-08-21 19:37:50'),(80,2,'2001:db8:0:20::15','2024-08-21 19:37:50','2024-08-21 19:37:50'),(81,2,'2001:db8:0:20::16','2024-08-21 19:37:50','2024-08-21 19:37:50'),(82,2,'2001:db8:0:20::17','2024-08-21 19:37:50','2024-08-21 19:37:50'),(83,2,'2001:db8:0:20::18','2024-08-21 19:37:50','2024-08-21 19:37:50'),(84,2,'2001:db8:0:20::19','2024-08-21 19:37:50','2024-08-21 19:37:50'),(85,2,'2001:db8:0:20::20','2024-08-21 19:37:50','2024-08-21 19:37:50'),(86,2,'2001:db8:0:20::21','2024-08-21 19:37:50','2024-08-21 19:37:50'),(87,2,'2001:db8:0:20::22','2024-08-21 19:37:50','2024-08-21 19:37:50'),(88,2,'2001:db8:0:20::23','2024-08-21 19:37:50','2024-08-21 19:37:50'),(89,2,'2001:db8:0:20::24','2024-08-21 19:37:50','2024-08-21 19:37:50'),(90,2,'2001:db8:0:20::25','2024-08-21 19:37:50','2024-08-21 19:37:50'),(91,2,'2001:db8:0:20::26','2024-08-21 19:37:50','2024-08-21 19:37:50'),(92,2,'2001:db8:0:20::27','2024-08-21 19:37:50','2024-08-21 19:37:50'),(93,2,'2001:db8:0:20::28','2024-08-21 19:37:50','2024-08-21 19:37:50'),(94,2,'2001:db8:0:20::29','2024-08-21 19:37:50','2024-08-21 19:37:50'),(95,2,'2001:db8:0:20::30','2024-08-21 19:37:50','2024-08-21 19:37:50'),(96,2,'2001:db8:0:20::31','2024-08-21 19:37:50','2024-08-21 19:37:50'),(97,4,'2001:db8:0:20::','2024-08-21 19:38:00','2024-08-21 19:38:00'),(98,4,'2001:db8:0:20::1','2024-08-21 19:38:00','2024-08-21 19:38:00'),(99,4,'2001:db8:0:20::2','2024-08-21 19:38:00','2024-08-21 19:38:00'),(100,4,'2001:db8:0:20::3','2024-08-21 19:38:00','2024-08-21 19:38:00'),(101,4,'2001:db8:0:20::4','2024-08-21 19:38:00','2024-08-21 19:38:00'),(102,4,'2001:db8:0:20::5','2024-08-21 19:38:00','2024-08-21 19:38:00'),(103,4,'2001:db8:0:20::6','2024-08-21 19:38:00','2024-08-21 19:38:00'),(104,4,'2001:db8:0:20::7','2024-08-21 19:38:00','2024-08-21 19:38:00'),(105,4,'2001:db8:0:20::8','2024-08-21 19:38:00','2024-08-21 19:38:00'),(106,4,'2001:db8:0:20::9','2024-08-21 19:38:00','2024-08-21 19:38:00'),(107,4,'2001:db8:0:20::10','2024-08-21 19:38:00','2024-08-21 19:38:00'),(108,4,'2001:db8:0:20::11','2024-08-21 19:38:00','2024-08-21 19:38:00'),(109,4,'2001:db8:0:20::12','2024-08-21 19:38:00','2024-08-21 19:38:00'),(110,4,'2001:db8:0:20::13','2024-08-21 19:38:00','2024-08-21 19:38:00'),(111,4,'2001:db8:0:20::14','2024-08-21 19:38:00','2024-08-21 19:38:00'),(112,4,'2001:db8:0:20::15','2024-08-21 19:38:00','2024-08-21 19:38:00'),(113,4,'2001:db8:0:20::16','2024-08-21 19:38:00','2024-08-21 19:38:00'),(114,4,'2001:db8:0:20::17','2024-08-21 19:38:00','2024-08-21 19:38:00'),(115,4,'2001:db8:0:20::18','2024-08-21 19:38:00','2024-08-21 19:38:00'),(116,4,'2001:db8:0:20::19','2024-08-21 19:38:00','2024-08-21 19:38:00'),(117,4,'2001:db8:0:20::20','2024-08-21 19:38:00','2024-08-21 19:38:00'),(118,4,'2001:db8:0:20::21','2024-08-21 19:38:00','2024-08-21 19:38:00'),(119,4,'2001:db8:0:20::22','2024-08-21 19:38:00','2024-08-21 19:38:00'),(120,4,'2001:db8:0:20::23','2024-08-21 19:38:00','2024-08-21 19:38:00'),(121,4,'2001:db8:0:20::24','2024-08-21 19:38:00','2024-08-21 19:38:00'),(122,4,'2001:db8:0:20::25','2024-08-21 19:38:00','2024-08-21 19:38:00'),(123,4,'2001:db8:0:20::26','2024-08-21 19:38:00','2024-08-21 19:38:00'),(124,4,'2001:db8:0:20::27','2024-08-21 19:38:00','2024-08-21 19:38:00'),(125,4,'2001:db8:0:20::28','2024-08-21 19:38:00','2024-08-21 19:38:00'),(126,4,'2001:db8:0:20::29','2024-08-21 19:38:00','2024-08-21 19:38:00'),(127,4,'2001:db8:0:20::30','2024-08-21 19:38:00','2024-08-21 19:38:00'),(128,4,'2001:db8:0:20::31','2024-08-21 19:38:00','2024-08-21 19:38:00'),(129,1,'2001:db8:0:10::126','2024-08-21 20:54:28','2024-08-21 20:54:28'),(130,3,'2001:db8:0:10::126','2024-08-21 20:54:34','2024-08-21 20:54:34'),(131,2,'2001:db8:0:20::126','2024-08-21 20:54:44','2024-08-21 20:54:44'),(132,4,'2001:db8:0:20::126','2024-08-21 20:54:49','2024-08-21 20:54:49');
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
) ENGINE=InnoDB AUTO_INCREMENT=50 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `irrdb_asn`
--

LOCK TABLES `irrdb_asn` WRITE;
/*!40000 ALTER TABLE `irrdb_asn` DISABLE KEYS */;
INSERT INTO `irrdb_asn` VALUES (1,2,112,4,'2024-08-21 14:44:07','2024-08-21 16:54:03','2024-08-21 19:44:07','2024-08-21 21:54:03'),(2,2,112,6,'2024-08-21 14:44:07','2024-08-21 16:54:04','2024-08-21 19:44:07','2024-08-21 21:54:04'),(3,3,112,4,'2024-08-21 15:19:35','2024-08-21 16:54:07','2024-08-21 20:19:35','2024-08-21 21:54:07'),(4,3,1213,4,'2024-08-21 15:19:35','2024-08-21 16:54:07','2024-08-21 20:19:35','2024-08-21 21:54:07'),(5,3,1921,4,'2024-08-21 15:19:35','2024-08-21 16:54:07','2024-08-21 20:19:35','2024-08-21 21:54:07'),(6,3,2128,4,'2024-08-21 15:19:35','2024-08-21 16:54:07','2024-08-21 20:19:35','2024-08-21 21:54:07'),(7,3,2850,4,'2024-08-21 15:19:35','2024-08-21 16:54:07','2024-08-21 20:19:35','2024-08-21 21:54:07'),(8,3,42310,4,'2024-08-21 15:19:35','2024-08-21 16:54:07','2024-08-21 20:19:35','2024-08-21 21:54:07'),(9,3,112,6,'2024-08-21 15:19:36','2024-08-21 16:54:07','2024-08-21 20:19:36','2024-08-21 21:54:07'),(10,3,1213,6,'2024-08-21 15:19:36','2024-08-21 16:54:07','2024-08-21 20:19:36','2024-08-21 21:54:07'),(11,3,1921,6,'2024-08-21 15:19:36','2024-08-21 16:54:07','2024-08-21 20:19:36','2024-08-21 21:54:07'),(12,3,2128,6,'2024-08-21 15:19:36','2024-08-21 16:54:07','2024-08-21 20:19:36','2024-08-21 21:54:07'),(13,3,2850,6,'2024-08-21 15:19:36','2024-08-21 16:54:07','2024-08-21 20:19:36','2024-08-21 21:54:07'),(14,3,42310,6,'2024-08-21 15:19:36','2024-08-21 16:54:07','2024-08-21 20:19:36','2024-08-21 21:54:07'),(15,4,8918,4,'2024-08-21 15:31:47','2024-08-21 16:54:05','2024-08-21 20:31:47','2024-08-21 21:54:05'),(16,4,11521,4,'2024-08-21 15:31:47','2024-08-21 16:54:05','2024-08-21 20:31:47','2024-08-21 21:54:05'),(17,4,25441,4,'2024-08-21 15:31:47','2024-08-21 16:54:05','2024-08-21 20:31:47','2024-08-21 21:54:05'),(18,4,34317,4,'2024-08-21 15:31:47','2024-08-21 16:54:05','2024-08-21 20:31:47','2024-08-21 21:54:05'),(19,4,35272,4,'2024-08-21 15:31:47','2024-08-21 16:54:05','2024-08-21 20:31:47','2024-08-21 21:54:05'),(20,4,39064,4,'2024-08-21 15:31:47','2024-08-21 16:54:05','2024-08-21 20:31:47','2024-08-21 21:54:05'),(21,4,43178,4,'2024-08-21 15:31:47','2024-08-21 16:54:05','2024-08-21 20:31:47','2024-08-21 21:54:05'),(22,4,43610,4,'2024-08-21 15:31:47','2024-08-21 16:54:05','2024-08-21 20:31:47','2024-08-21 21:54:05'),(23,4,47615,4,'2024-08-21 15:31:47','2024-08-21 16:54:05','2024-08-21 20:31:47','2024-08-21 21:54:05'),(24,4,48342,4,'2024-08-21 15:31:47','2024-08-21 16:54:05','2024-08-21 20:31:47','2024-08-21 21:54:05'),(25,4,49573,4,'2024-08-21 15:31:47','2024-08-21 16:54:05','2024-08-21 20:31:47','2024-08-21 21:54:05'),(26,4,197853,4,'2024-08-21 15:31:47','2024-08-21 16:54:05','2024-08-21 20:31:47','2024-08-21 21:54:05'),(27,4,197904,4,'2024-08-21 15:31:47','2024-08-21 16:54:05','2024-08-21 20:31:47','2024-08-21 21:54:05'),(28,4,200174,4,'2024-08-21 15:31:47','2024-08-21 16:54:05','2024-08-21 20:31:47','2024-08-21 21:54:05'),(29,4,8918,6,'2024-08-21 15:31:48','2024-08-21 16:54:06','2024-08-21 20:31:48','2024-08-21 21:54:06'),(30,4,11521,6,'2024-08-21 15:31:48','2024-08-21 16:54:06','2024-08-21 20:31:48','2024-08-21 21:54:06'),(31,4,25441,6,'2024-08-21 15:31:48','2024-08-21 16:54:06','2024-08-21 20:31:48','2024-08-21 21:54:06'),(32,4,34317,6,'2024-08-21 15:31:48','2024-08-21 16:54:06','2024-08-21 20:31:48','2024-08-21 21:54:06'),(33,4,35272,6,'2024-08-21 15:31:48','2024-08-21 16:54:06','2024-08-21 20:31:48','2024-08-21 21:54:06'),(34,4,39064,6,'2024-08-21 15:31:48','2024-08-21 16:54:06','2024-08-21 20:31:48','2024-08-21 21:54:06'),(35,4,43178,6,'2024-08-21 15:31:48','2024-08-21 16:54:06','2024-08-21 20:31:48','2024-08-21 21:54:06'),(36,4,43610,6,'2024-08-21 15:31:48','2024-08-21 16:54:06','2024-08-21 20:31:48','2024-08-21 21:54:06'),(37,4,47615,6,'2024-08-21 15:31:48','2024-08-21 16:54:06','2024-08-21 20:31:48','2024-08-21 21:54:06'),(38,4,48342,6,'2024-08-21 15:31:48','2024-08-21 16:54:06','2024-08-21 20:31:48','2024-08-21 21:54:06'),(39,4,49573,6,'2024-08-21 15:31:48','2024-08-21 16:54:06','2024-08-21 20:31:48','2024-08-21 21:54:06'),(40,4,197853,6,'2024-08-21 15:31:48','2024-08-21 16:54:06','2024-08-21 20:31:48','2024-08-21 21:54:06'),(41,4,197904,6,'2024-08-21 15:31:48','2024-08-21 16:54:06','2024-08-21 20:31:48','2024-08-21 21:54:06'),(42,4,200174,6,'2024-08-21 15:31:48','2024-08-21 16:54:06','2024-08-21 20:31:48','2024-08-21 21:54:06'),(43,5,2906,4,'2024-08-21 15:47:44','2024-08-21 16:54:04','2024-08-21 20:47:44','2024-08-21 21:54:04'),(44,5,40027,4,'2024-08-21 15:47:44','2024-08-21 16:54:04','2024-08-21 20:47:44','2024-08-21 21:54:04'),(45,5,55095,4,'2024-08-21 15:47:44','2024-08-21 16:54:04','2024-08-21 20:47:44','2024-08-21 21:54:04'),(46,5,2906,6,'2024-08-21 15:47:45','2024-08-21 16:54:05','2024-08-21 20:47:45','2024-08-21 21:54:05'),(47,5,40027,6,'2024-08-21 15:47:45','2024-08-21 16:54:05','2024-08-21 20:47:45','2024-08-21 21:54:05'),(48,5,55095,6,'2024-08-21 15:47:45','2024-08-21 16:54:05','2024-08-21 20:47:45','2024-08-21 21:54:05'),(49,6,39093,4,'2024-08-21 15:52:54','2024-08-21 16:54:08','2024-08-21 20:52:54','2024-08-21 21:54:08');
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
  `prefix` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `protocol` int NOT NULL,
  `first_seen` datetime DEFAULT NULL,
  `last_seen` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `custprefix` (`prefix`,`protocol`,`customer_id`),
  KEY `IDX_FE73E77C9395C3F3` (`customer_id`),
  CONSTRAINT `FK_FE73E77C9395C3F3` FOREIGN KEY (`customer_id`) REFERENCES `cust` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=312 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `irrdb_prefix`
--

LOCK TABLES `irrdb_prefix` WRITE;
/*!40000 ALTER TABLE `irrdb_prefix` DISABLE KEYS */;
INSERT INTO `irrdb_prefix` VALUES (1,2,'192.31.196.0/24',4,'2024-08-21 14:50:16','2024-08-21 16:53:42','2024-08-21 19:50:16','2024-08-21 21:53:42'),(2,2,'192.175.48.0/24',4,'2024-08-21 14:50:16','2024-08-21 16:53:42','2024-08-21 19:50:16','2024-08-21 21:53:42'),(3,2,'2001:4:112::/48',6,'2024-08-21 14:50:17','2024-08-21 16:53:42','2024-08-21 19:50:17','2024-08-21 21:53:42'),(4,2,'2620:4f:8000::/48',6,'2024-08-21 14:50:17','2024-08-21 16:53:42','2024-08-21 19:50:17','2024-08-21 21:53:42'),(5,3,'45.144.8.0/22',4,'2024-08-21 15:19:24','2024-08-21 16:53:47','2024-08-21 20:19:24','2024-08-21 21:53:47'),(6,3,'77.72.72.0/21',4,'2024-08-21 15:19:24','2024-08-21 16:53:47','2024-08-21 20:19:24','2024-08-21 21:53:47'),(7,3,'77.72.72.0/22',4,'2024-08-21 15:19:24','2024-08-21 16:53:47','2024-08-21 20:19:24','2024-08-21 21:53:47'),(8,3,'77.72.72.0/23',4,'2024-08-21 15:19:24','2024-08-21 16:53:47','2024-08-21 20:19:24','2024-08-21 21:53:47'),(9,3,'77.72.74.0/23',4,'2024-08-21 15:19:24','2024-08-21 16:53:47','2024-08-21 20:19:24','2024-08-21 21:53:47'),(10,3,'77.72.76.0/23',4,'2024-08-21 15:19:24','2024-08-21 16:53:47','2024-08-21 20:19:24','2024-08-21 21:53:47'),(11,3,'77.72.78.0/23',4,'2024-08-21 15:19:24','2024-08-21 16:53:47','2024-08-21 20:19:24','2024-08-21 21:53:47'),(12,3,'77.72.78.0/24',4,'2024-08-21 15:19:24','2024-08-21 16:53:47','2024-08-21 20:19:24','2024-08-21 21:53:47'),(13,3,'77.72.79.0/24',4,'2024-08-21 15:19:24','2024-08-21 16:53:47','2024-08-21 20:19:24','2024-08-21 21:53:47'),(14,3,'77.87.24.0/21',4,'2024-08-21 15:19:24','2024-08-21 16:53:47','2024-08-21 20:19:24','2024-08-21 21:53:47'),(15,3,'87.32.0.0/12',4,'2024-08-21 15:19:24','2024-08-21 16:53:47','2024-08-21 20:19:24','2024-08-21 21:53:47'),(16,3,'91.123.224.0/20',4,'2024-08-21 15:19:24','2024-08-21 16:53:47','2024-08-21 20:19:24','2024-08-21 21:53:47'),(17,3,'91.193.188.0/22',4,'2024-08-21 15:19:24','2024-08-21 16:53:47','2024-08-21 20:19:24','2024-08-21 21:53:47'),(18,3,'91.237.67.0/24',4,'2024-08-21 15:19:24','2024-08-21 16:53:47','2024-08-21 20:19:24','2024-08-21 21:53:47'),(19,3,'134.226.0.0/16',4,'2024-08-21 15:19:24','2024-08-21 16:53:47','2024-08-21 20:19:24','2024-08-21 21:53:47'),(20,3,'136.201.0.0/16',4,'2024-08-21 15:19:24','2024-08-21 16:53:47','2024-08-21 20:19:24','2024-08-21 21:53:47'),(21,3,'136.206.0.0/16',4,'2024-08-21 15:19:24','2024-08-21 16:53:47','2024-08-21 20:19:24','2024-08-21 21:53:47'),(22,3,'137.43.0.0/16',4,'2024-08-21 15:19:24','2024-08-21 16:53:47','2024-08-21 20:19:24','2024-08-21 21:53:47'),(23,3,'140.203.0.0/16',4,'2024-08-21 15:19:24','2024-08-21 16:53:47','2024-08-21 20:19:24','2024-08-21 21:53:47'),(24,3,'143.239.0.0/16',4,'2024-08-21 15:19:24','2024-08-21 16:53:47','2024-08-21 20:19:24','2024-08-21 21:53:47'),(25,3,'147.252.0.0/16',4,'2024-08-21 15:19:24','2024-08-21 16:53:47','2024-08-21 20:19:24','2024-08-21 21:53:47'),(26,3,'149.153.0.0/16',4,'2024-08-21 15:19:24','2024-08-21 16:53:47','2024-08-21 20:19:24','2024-08-21 21:53:47'),(27,3,'149.157.0.0/16',4,'2024-08-21 15:19:24','2024-08-21 16:53:47','2024-08-21 20:19:24','2024-08-21 21:53:47'),(28,3,'157.190.0.0/16',4,'2024-08-21 15:19:24','2024-08-21 16:53:47','2024-08-21 20:19:24','2024-08-21 21:53:47'),(29,3,'160.6.0.0/16',4,'2024-08-21 15:19:24','2024-08-21 16:53:47','2024-08-21 20:19:24','2024-08-21 21:53:47'),(30,3,'176.97.158.0/24',4,'2024-08-21 15:19:24','2024-08-21 16:53:47','2024-08-21 20:19:24','2024-08-21 21:53:47'),(31,3,'185.1.69.0/24',4,'2024-08-21 15:19:24','2024-08-21 16:53:47','2024-08-21 20:19:24','2024-08-21 21:53:47'),(32,3,'185.6.36.0/22',4,'2024-08-21 15:19:24','2024-08-21 16:53:47','2024-08-21 20:19:24','2024-08-21 21:53:47'),(33,3,'185.6.39.0/24',4,'2024-08-21 15:19:24','2024-08-21 16:53:47','2024-08-21 20:19:24','2024-08-21 21:53:47'),(34,3,'185.80.188.0/22',4,'2024-08-21 15:19:24','2024-08-21 16:53:47','2024-08-21 20:19:24','2024-08-21 21:53:47'),(35,3,'185.80.188.0/24',4,'2024-08-21 15:19:24','2024-08-21 16:53:47','2024-08-21 20:19:24','2024-08-21 21:53:47'),(36,3,'185.80.189.0/24',4,'2024-08-21 15:19:24','2024-08-21 16:53:47','2024-08-21 20:19:24','2024-08-21 21:53:47'),(37,3,'185.80.190.0/24',4,'2024-08-21 15:19:24','2024-08-21 16:53:47','2024-08-21 20:19:24','2024-08-21 21:53:47'),(38,3,'185.80.191.0/24',4,'2024-08-21 15:19:24','2024-08-21 16:53:47','2024-08-21 20:19:24','2024-08-21 21:53:47'),(39,3,'185.102.12.0/24',4,'2024-08-21 15:19:24','2024-08-21 16:53:47','2024-08-21 20:19:24','2024-08-21 21:53:47'),(40,3,'185.167.176.0/24',4,'2024-08-21 15:19:24','2024-08-21 16:53:47','2024-08-21 20:19:24','2024-08-21 21:53:47'),(41,3,'192.174.68.0/24',4,'2024-08-21 15:19:24','2024-08-21 16:53:47','2024-08-21 20:19:24','2024-08-21 21:53:47'),(42,3,'193.1.0.0/16',4,'2024-08-21 15:19:24','2024-08-21 16:53:47','2024-08-21 20:19:24','2024-08-21 21:53:47'),(43,3,'193.46.128.0/24',4,'2024-08-21 15:19:24','2024-08-21 16:53:47','2024-08-21 20:19:24','2024-08-21 21:53:47'),(44,3,'193.46.129.0/24',4,'2024-08-21 15:19:24','2024-08-21 16:53:47','2024-08-21 20:19:24','2024-08-21 21:53:47'),(45,3,'193.46.130.0/24',4,'2024-08-21 15:19:24','2024-08-21 16:53:47','2024-08-21 20:19:24','2024-08-21 21:53:47'),(46,3,'193.46.131.0/24',4,'2024-08-21 15:19:24','2024-08-21 16:53:47','2024-08-21 20:19:24','2024-08-21 21:53:47'),(47,3,'193.46.132.0/24',4,'2024-08-21 15:19:24','2024-08-21 16:53:47','2024-08-21 20:19:24','2024-08-21 21:53:47'),(48,3,'193.46.133.0/24',4,'2024-08-21 15:19:24','2024-08-21 16:53:47','2024-08-21 20:19:24','2024-08-21 21:53:47'),(49,3,'193.46.134.0/24',4,'2024-08-21 15:19:24','2024-08-21 16:53:47','2024-08-21 20:19:24','2024-08-21 21:53:47'),(50,3,'193.46.135.0/24',4,'2024-08-21 15:19:24','2024-08-21 16:53:47','2024-08-21 20:19:24','2024-08-21 21:53:47'),(51,3,'193.227.117.0/24',4,'2024-08-21 15:19:24','2024-08-21 16:53:47','2024-08-21 20:19:24','2024-08-21 21:53:47'),(52,3,'193.242.111.0/24',4,'2024-08-21 15:19:24','2024-08-21 16:53:47','2024-08-21 20:19:24','2024-08-21 21:53:47'),(53,3,'194.0.24.0/24',4,'2024-08-21 15:19:24','2024-08-21 16:53:47','2024-08-21 20:19:24','2024-08-21 21:53:47'),(54,3,'194.0.25.0/24',4,'2024-08-21 15:19:24','2024-08-21 16:53:47','2024-08-21 20:19:24','2024-08-21 21:53:47'),(55,3,'194.0.26.0/24',4,'2024-08-21 15:19:24','2024-08-21 16:53:47','2024-08-21 20:19:24','2024-08-21 21:53:47'),(56,3,'194.0.182.0/24',4,'2024-08-21 15:19:24','2024-08-21 16:53:47','2024-08-21 20:19:24','2024-08-21 21:53:47'),(57,3,'194.26.0.0/24',4,'2024-08-21 15:19:24','2024-08-21 16:53:47','2024-08-21 20:19:24','2024-08-21 21:53:47'),(58,3,'194.50.187.0/24',4,'2024-08-21 15:19:24','2024-08-21 16:53:47','2024-08-21 20:19:24','2024-08-21 21:53:47'),(59,3,'194.88.240.0/23',4,'2024-08-21 15:19:24','2024-08-21 16:53:47','2024-08-21 20:19:24','2024-08-21 21:53:47'),(60,3,'2001:678:20::/48',6,'2024-08-21 15:19:25','2024-08-21 16:53:48','2024-08-21 20:19:25','2024-08-21 21:53:48'),(61,3,'2001:678:24::/48',6,'2024-08-21 15:19:25','2024-08-21 16:53:48','2024-08-21 20:19:25','2024-08-21 21:53:48'),(62,3,'2001:67c:1bc::/48',6,'2024-08-21 15:19:25','2024-08-21 16:53:48','2024-08-21 20:19:25','2024-08-21 21:53:48'),(63,3,'2001:67c:10b8::/48',6,'2024-08-21 15:19:25','2024-08-21 16:53:48','2024-08-21 20:19:25','2024-08-21 21:53:48'),(64,3,'2001:67c:10e0::/48',6,'2024-08-21 15:19:25','2024-08-21 16:53:48','2024-08-21 20:19:25','2024-08-21 21:53:48'),(65,3,'2001:770::/32',6,'2024-08-21 15:19:25','2024-08-21 16:53:48','2024-08-21 20:19:25','2024-08-21 21:53:48'),(66,3,'2001:7f8:18::/48',6,'2024-08-21 15:19:25','2024-08-21 16:53:48','2024-08-21 20:19:25','2024-08-21 21:53:48'),(67,3,'2a01:4b0::/32',6,'2024-08-21 15:19:25','2024-08-21 16:53:48','2024-08-21 20:19:25','2024-08-21 21:53:48'),(68,3,'2a01:4b0::/48',6,'2024-08-21 15:19:25','2024-08-21 16:53:48','2024-08-21 20:19:25','2024-08-21 21:53:48'),(69,3,'2a01:4b0:1::/48',6,'2024-08-21 15:19:25','2024-08-21 16:53:48','2024-08-21 20:19:25','2024-08-21 21:53:48'),(70,3,'2a01:4b0:2::/48',6,'2024-08-21 15:19:25','2024-08-21 16:53:48','2024-08-21 20:19:25','2024-08-21 21:53:48'),(71,3,'2a02:850:ffe0::/48',6,'2024-08-21 15:19:25','2024-08-21 16:53:48','2024-08-21 20:19:25','2024-08-21 21:53:48'),(72,3,'2a02:850:ffe1::/48',6,'2024-08-21 15:19:25','2024-08-21 16:53:48','2024-08-21 20:19:25','2024-08-21 21:53:48'),(73,3,'2a02:850:ffe2::/48',6,'2024-08-21 15:19:25','2024-08-21 16:53:48','2024-08-21 20:19:25','2024-08-21 21:53:48'),(74,3,'2a02:850:ffe3::/48',6,'2024-08-21 15:19:25','2024-08-21 16:53:48','2024-08-21 20:19:25','2024-08-21 21:53:48'),(75,3,'2a02:850:ffe4::/48',6,'2024-08-21 15:19:25','2024-08-21 16:53:48','2024-08-21 20:19:25','2024-08-21 21:53:48'),(76,3,'2a02:850:ffe5::/48',6,'2024-08-21 15:19:25','2024-08-21 16:53:48','2024-08-21 20:19:25','2024-08-21 21:53:48'),(77,3,'2a02:850:ffe6::/48',6,'2024-08-21 15:19:25','2024-08-21 16:53:48','2024-08-21 20:19:25','2024-08-21 21:53:48'),(78,3,'2a02:850:ffe7::/48',6,'2024-08-21 15:19:25','2024-08-21 16:53:48','2024-08-21 20:19:25','2024-08-21 21:53:48'),(79,3,'2a02:850:ffff::/48',6,'2024-08-21 15:19:25','2024-08-21 16:53:48','2024-08-21 20:19:25','2024-08-21 21:53:48'),(80,3,'2a03:ac0::/29',6,'2024-08-21 15:19:25','2024-08-21 16:53:48','2024-08-21 20:19:25','2024-08-21 21:53:48'),(81,3,'2a03:ac0::/32',6,'2024-08-21 15:19:25','2024-08-21 16:53:48','2024-08-21 20:19:25','2024-08-21 21:53:48'),(82,3,'2a03:ac1::/32',6,'2024-08-21 15:19:25','2024-08-21 16:53:48','2024-08-21 20:19:25','2024-08-21 21:53:48'),(83,3,'2a04:2b00:14cc::/48',6,'2024-08-21 15:19:25','2024-08-21 16:53:48','2024-08-21 20:19:25','2024-08-21 21:53:48'),(84,3,'2a04:2b00:14dd::/48',6,'2024-08-21 15:19:25','2024-08-21 16:53:48','2024-08-21 20:19:25','2024-08-21 21:53:48'),(85,3,'2a04:2b00:14ee::/48',6,'2024-08-21 15:19:25','2024-08-21 16:53:48','2024-08-21 20:19:25','2024-08-21 21:53:48'),(86,3,'2a05:7f00::/29',6,'2024-08-21 15:19:25','2024-08-21 16:53:48','2024-08-21 20:19:25','2024-08-21 21:53:48'),(87,3,'2a05:7f00:188::/48',6,'2024-08-21 15:19:25','2024-08-21 16:53:48','2024-08-21 20:19:25','2024-08-21 21:53:48'),(88,3,'2a05:7f00:189::/48',6,'2024-08-21 15:19:25','2024-08-21 16:53:48','2024-08-21 20:19:25','2024-08-21 21:53:48'),(89,3,'2a05:7f00:190::/48',6,'2024-08-21 15:19:25','2024-08-21 16:53:48','2024-08-21 20:19:25','2024-08-21 21:53:48'),(90,3,'2a05:7f00:191::/48',6,'2024-08-21 15:19:25','2024-08-21 16:53:48','2024-08-21 20:19:25','2024-08-21 21:53:48'),(91,3,'2a0b:8e00::/48',6,'2024-08-21 15:19:25','2024-08-21 16:53:48','2024-08-21 20:19:25','2024-08-21 21:53:48'),(92,3,'2a0b:8e00:1::/48',6,'2024-08-21 15:19:25','2024-08-21 16:53:48','2024-08-21 20:19:25','2024-08-21 21:53:48'),(93,4,'45.12.32.0/24',4,'2024-08-21 15:31:53','2024-08-21 16:53:45','2024-08-21 20:31:53','2024-08-21 21:53:45'),(94,4,'45.12.33.0/24',4,'2024-08-21 15:31:53','2024-08-21 16:53:45','2024-08-21 20:31:53','2024-08-21 21:53:45'),(95,4,'45.142.3.0/24',4,'2024-08-21 15:31:53','2024-08-21 16:53:45','2024-08-21 20:31:53','2024-08-21 21:53:45'),(96,4,'62.222.0.0/15',4,'2024-08-21 15:31:53','2024-08-21 16:53:45','2024-08-21 20:31:53','2024-08-21 21:53:45'),(97,4,'62.222.0.0/24',4,'2024-08-21 15:31:53','2024-08-21 16:53:45','2024-08-21 20:31:53','2024-08-21 21:53:45'),(98,4,'62.231.32.0/19',4,'2024-08-21 15:31:53','2024-08-21 16:53:45','2024-08-21 20:31:53','2024-08-21 21:53:45'),(99,4,'78.135.128.0/17',4,'2024-08-21 15:31:53','2024-08-21 16:53:45','2024-08-21 20:31:53','2024-08-21 21:53:45'),(100,4,'78.135.208.0/21',4,'2024-08-21 15:31:53','2024-08-21 16:53:45','2024-08-21 20:31:53','2024-08-21 21:53:45'),(101,4,'78.135.216.0/21',4,'2024-08-21 15:31:53','2024-08-21 16:53:45','2024-08-21 20:31:53','2024-08-21 21:53:45'),(102,4,'78.135.224.0/21',4,'2024-08-21 15:31:53','2024-08-21 16:53:45','2024-08-21 20:31:53','2024-08-21 21:53:45'),(103,4,'78.135.232.0/21',4,'2024-08-21 15:31:53','2024-08-21 16:53:45','2024-08-21 20:31:53','2024-08-21 21:53:45'),(104,4,'78.135.240.0/21',4,'2024-08-21 15:31:53','2024-08-21 16:53:45','2024-08-21 20:31:53','2024-08-21 21:53:45'),(105,4,'78.135.248.0/21',4,'2024-08-21 15:31:53','2024-08-21 16:53:45','2024-08-21 20:31:53','2024-08-21 21:53:45'),(106,4,'83.141.64.0/18',4,'2024-08-21 15:31:53','2024-08-21 16:53:45','2024-08-21 20:31:53','2024-08-21 21:53:45'),(107,4,'85.134.128.0/17',4,'2024-08-21 15:31:53','2024-08-21 16:53:45','2024-08-21 20:31:53','2024-08-21 21:53:45'),(108,4,'85.134.128.0/21',4,'2024-08-21 15:31:53','2024-08-21 16:53:45','2024-08-21 20:31:53','2024-08-21 21:53:45'),(109,4,'85.134.136.0/21',4,'2024-08-21 15:31:53','2024-08-21 16:53:45','2024-08-21 20:31:53','2024-08-21 21:53:45'),(110,4,'85.134.144.0/21',4,'2024-08-21 15:31:53','2024-08-21 16:53:45','2024-08-21 20:31:53','2024-08-21 21:53:45'),(111,4,'85.134.152.0/21',4,'2024-08-21 15:31:53','2024-08-21 16:53:45','2024-08-21 20:31:53','2024-08-21 21:53:45'),(112,4,'85.134.160.0/21',4,'2024-08-21 15:31:53','2024-08-21 16:53:45','2024-08-21 20:31:53','2024-08-21 21:53:45'),(113,4,'85.134.168.0/21',4,'2024-08-21 15:31:53','2024-08-21 16:53:45','2024-08-21 20:31:53','2024-08-21 21:53:45'),(114,4,'85.134.176.0/21',4,'2024-08-21 15:31:53','2024-08-21 16:53:45','2024-08-21 20:31:53','2024-08-21 21:53:45'),(115,4,'85.134.184.0/21',4,'2024-08-21 15:31:53','2024-08-21 16:53:45','2024-08-21 20:31:53','2024-08-21 21:53:45'),(116,4,'85.134.192.0/21',4,'2024-08-21 15:31:53','2024-08-21 16:53:45','2024-08-21 20:31:53','2024-08-21 21:53:45'),(117,4,'85.134.200.0/21',4,'2024-08-21 15:31:53','2024-08-21 16:53:45','2024-08-21 20:31:53','2024-08-21 21:53:45'),(118,4,'85.134.208.0/21',4,'2024-08-21 15:31:53','2024-08-21 16:53:45','2024-08-21 20:31:53','2024-08-21 21:53:45'),(119,4,'85.134.216.0/21',4,'2024-08-21 15:31:53','2024-08-21 16:53:45','2024-08-21 20:31:53','2024-08-21 21:53:45'),(120,4,'85.134.224.0/21',4,'2024-08-21 15:31:53','2024-08-21 16:53:45','2024-08-21 20:31:53','2024-08-21 21:53:45'),(121,4,'85.134.232.0/21',4,'2024-08-21 15:31:53','2024-08-21 16:53:45','2024-08-21 20:31:53','2024-08-21 21:53:45'),(122,4,'85.134.240.0/21',4,'2024-08-21 15:31:53','2024-08-21 16:53:45','2024-08-21 20:31:53','2024-08-21 21:53:45'),(123,4,'85.134.248.0/21',4,'2024-08-21 15:31:53','2024-08-21 16:53:45','2024-08-21 20:31:53','2024-08-21 21:53:45'),(124,4,'87.192.0.0/18',4,'2024-08-21 15:31:53','2024-08-21 16:53:45','2024-08-21 20:31:53','2024-08-21 21:53:45'),(125,4,'87.192.64.0/20',4,'2024-08-21 15:31:53','2024-08-21 16:53:45','2024-08-21 20:31:53','2024-08-21 21:53:45'),(126,4,'87.192.81.0/24',4,'2024-08-21 15:31:53','2024-08-21 16:53:45','2024-08-21 20:31:53','2024-08-21 21:53:45'),(127,4,'87.192.82.0/23',4,'2024-08-21 15:31:53','2024-08-21 16:53:45','2024-08-21 20:31:53','2024-08-21 21:53:45'),(128,4,'87.192.84.0/22',4,'2024-08-21 15:31:53','2024-08-21 16:53:45','2024-08-21 20:31:53','2024-08-21 21:53:45'),(129,4,'87.192.88.0/21',4,'2024-08-21 15:31:53','2024-08-21 16:53:45','2024-08-21 20:31:53','2024-08-21 21:53:45'),(130,4,'87.192.96.0/19',4,'2024-08-21 15:31:53','2024-08-21 16:53:45','2024-08-21 20:31:53','2024-08-21 21:53:45'),(131,4,'87.192.128.0/18',4,'2024-08-21 15:31:53','2024-08-21 16:53:45','2024-08-21 20:31:53','2024-08-21 21:53:45'),(132,4,'87.192.192.0/20',4,'2024-08-21 15:31:53','2024-08-21 16:53:45','2024-08-21 20:31:53','2024-08-21 21:53:45'),(133,4,'87.192.208.0/21',4,'2024-08-21 15:31:53','2024-08-21 16:53:45','2024-08-21 20:31:53','2024-08-21 21:53:45'),(134,4,'87.192.216.0/22',4,'2024-08-21 15:31:53','2024-08-21 16:53:45','2024-08-21 20:31:53','2024-08-21 21:53:45'),(135,4,'87.192.222.0/23',4,'2024-08-21 15:31:53','2024-08-21 16:53:45','2024-08-21 20:31:53','2024-08-21 21:53:45'),(136,4,'87.192.224.0/19',4,'2024-08-21 15:31:53','2024-08-21 16:53:45','2024-08-21 20:31:53','2024-08-21 21:53:45'),(137,4,'87.232.0.0/19',4,'2024-08-21 15:31:53','2024-08-21 16:53:45','2024-08-21 20:31:53','2024-08-21 21:53:45'),(138,4,'87.232.136.0/21',4,'2024-08-21 15:31:53','2024-08-21 16:53:45','2024-08-21 20:31:53','2024-08-21 21:53:45'),(139,4,'87.232.144.0/20',4,'2024-08-21 15:31:53','2024-08-21 16:53:45','2024-08-21 20:31:53','2024-08-21 21:53:45'),(140,4,'87.232.160.0/19',4,'2024-08-21 15:31:53','2024-08-21 16:53:45','2024-08-21 20:31:53','2024-08-21 21:53:45'),(141,4,'87.232.192.0/24',4,'2024-08-21 15:31:53','2024-08-21 16:53:45','2024-08-21 20:31:53','2024-08-21 21:53:45'),(142,4,'87.232.194.0/23',4,'2024-08-21 15:31:53','2024-08-21 16:53:45','2024-08-21 20:31:53','2024-08-21 21:53:45'),(143,4,'87.232.196.0/22',4,'2024-08-21 15:31:53','2024-08-21 16:53:45','2024-08-21 20:31:53','2024-08-21 21:53:45'),(144,4,'87.232.200.0/21',4,'2024-08-21 15:31:53','2024-08-21 16:53:45','2024-08-21 20:31:53','2024-08-21 21:53:45'),(145,4,'87.232.208.0/20',4,'2024-08-21 15:31:53','2024-08-21 16:53:45','2024-08-21 20:31:53','2024-08-21 21:53:45'),(146,4,'87.232.225.0/24',4,'2024-08-21 15:31:53','2024-08-21 16:53:45','2024-08-21 20:31:53','2024-08-21 21:53:45'),(147,4,'87.232.226.0/23',4,'2024-08-21 15:31:53','2024-08-21 16:53:45','2024-08-21 20:31:53','2024-08-21 21:53:45'),(148,4,'87.232.228.0/22',4,'2024-08-21 15:31:53','2024-08-21 16:53:45','2024-08-21 20:31:53','2024-08-21 21:53:45'),(149,4,'87.232.232.0/21',4,'2024-08-21 15:31:53','2024-08-21 16:53:45','2024-08-21 20:31:53','2024-08-21 21:53:45'),(150,4,'87.232.240.0/21',4,'2024-08-21 15:31:53','2024-08-21 16:53:45','2024-08-21 20:31:53','2024-08-21 21:53:45'),(151,4,'87.232.248.0/23',4,'2024-08-21 15:31:53','2024-08-21 16:53:45','2024-08-21 20:31:53','2024-08-21 21:53:45'),(152,4,'89.124.0.0/17',4,'2024-08-21 15:31:53','2024-08-21 16:53:45','2024-08-21 20:31:53','2024-08-21 21:53:45'),(153,4,'89.124.128.0/18',4,'2024-08-21 15:31:53','2024-08-21 16:53:45','2024-08-21 20:31:53','2024-08-21 21:53:45'),(154,4,'89.124.192.0/19',4,'2024-08-21 15:31:53','2024-08-21 16:53:45','2024-08-21 20:31:54','2024-08-21 21:53:45'),(155,4,'89.124.224.0/20',4,'2024-08-21 15:31:53','2024-08-21 16:53:45','2024-08-21 20:31:54','2024-08-21 21:53:45'),(156,4,'89.124.240.0/23',4,'2024-08-21 15:31:53','2024-08-21 16:53:45','2024-08-21 20:31:54','2024-08-21 21:53:45'),(157,4,'89.124.245.0/24',4,'2024-08-21 15:31:53','2024-08-21 16:53:45','2024-08-21 20:31:54','2024-08-21 21:53:45'),(158,4,'89.124.246.0/23',4,'2024-08-21 15:31:53','2024-08-21 16:53:45','2024-08-21 20:31:54','2024-08-21 21:53:45'),(159,4,'89.124.248.0/21',4,'2024-08-21 15:31:53','2024-08-21 16:53:45','2024-08-21 20:31:54','2024-08-21 21:53:45'),(160,4,'89.125.0.0/16',4,'2024-08-21 15:31:53','2024-08-21 16:53:45','2024-08-21 20:31:54','2024-08-21 21:53:45'),(161,4,'89.126.0.0/16',4,'2024-08-21 15:31:53','2024-08-21 16:53:45','2024-08-21 20:31:54','2024-08-21 21:53:45'),(162,4,'89.126.0.0/22',4,'2024-08-21 15:31:53','2024-08-21 16:53:45','2024-08-21 20:31:54','2024-08-21 21:53:45'),(163,4,'89.126.4.0/22',4,'2024-08-21 15:31:53','2024-08-21 16:53:45','2024-08-21 20:31:54','2024-08-21 21:53:45'),(164,4,'89.126.8.0/22',4,'2024-08-21 15:31:53','2024-08-21 16:53:45','2024-08-21 20:31:54','2024-08-21 21:53:45'),(165,4,'89.126.12.0/22',4,'2024-08-21 15:31:53','2024-08-21 16:53:45','2024-08-21 20:31:54','2024-08-21 21:53:45'),(166,4,'89.126.16.0/22',4,'2024-08-21 15:31:53','2024-08-21 16:53:45','2024-08-21 20:31:54','2024-08-21 21:53:45'),(167,4,'89.126.20.0/22',4,'2024-08-21 15:31:53','2024-08-21 16:53:45','2024-08-21 20:31:54','2024-08-21 21:53:45'),(168,4,'89.126.24.0/22',4,'2024-08-21 15:31:53','2024-08-21 16:53:45','2024-08-21 20:31:54','2024-08-21 21:53:45'),(169,4,'89.126.28.0/22',4,'2024-08-21 15:31:53','2024-08-21 16:53:45','2024-08-21 20:31:54','2024-08-21 21:53:45'),(170,4,'89.127.0.0/17',4,'2024-08-21 15:31:53','2024-08-21 16:53:45','2024-08-21 20:31:54','2024-08-21 21:53:45'),(171,4,'89.127.128.0/18',4,'2024-08-21 15:31:53','2024-08-21 16:53:45','2024-08-21 20:31:54','2024-08-21 21:53:45'),(172,4,'89.127.192.0/19',4,'2024-08-21 15:31:53','2024-08-21 16:53:45','2024-08-21 20:31:54','2024-08-21 21:53:45'),(173,4,'89.127.224.0/20',4,'2024-08-21 15:31:53','2024-08-21 16:53:45','2024-08-21 20:31:54','2024-08-21 21:53:45'),(174,4,'89.127.240.0/21',4,'2024-08-21 15:31:53','2024-08-21 16:53:45','2024-08-21 20:31:54','2024-08-21 21:53:45'),(175,4,'89.127.248.0/22',4,'2024-08-21 15:31:53','2024-08-21 16:53:45','2024-08-21 20:31:54','2024-08-21 21:53:45'),(176,4,'89.127.254.0/23',4,'2024-08-21 15:31:53','2024-08-21 16:53:45','2024-08-21 20:31:54','2024-08-21 21:53:45'),(177,4,'91.194.126.0/23',4,'2024-08-21 15:31:53','2024-08-21 16:53:45','2024-08-21 20:31:54','2024-08-21 21:53:45'),(178,4,'91.194.127.0/24',4,'2024-08-21 15:31:53','2024-08-21 16:53:45','2024-08-21 20:31:54','2024-08-21 21:53:45'),(179,4,'91.209.106.0/24',4,'2024-08-21 15:31:53','2024-08-21 16:53:45','2024-08-21 20:31:54','2024-08-21 21:53:45'),(180,4,'185.211.188.0/22',4,'2024-08-21 15:31:53','2024-08-21 16:53:45','2024-08-21 20:31:54','2024-08-21 21:53:45'),(181,4,'185.211.188.0/24',4,'2024-08-21 15:31:53','2024-08-21 16:53:45','2024-08-21 20:31:54','2024-08-21 21:53:45'),(182,4,'185.211.189.0/24',4,'2024-08-21 15:31:53','2024-08-21 16:53:45','2024-08-21 20:31:54','2024-08-21 21:53:45'),(183,4,'185.211.190.0/24',4,'2024-08-21 15:31:53','2024-08-21 16:53:45','2024-08-21 20:31:54','2024-08-21 21:53:45'),(184,4,'185.211.191.0/24',4,'2024-08-21 15:31:53','2024-08-21 16:53:45','2024-08-21 20:31:54','2024-08-21 21:53:45'),(185,4,'185.247.52.0/22',4,'2024-08-21 15:31:53','2024-08-21 16:53:45','2024-08-21 20:31:54','2024-08-21 21:53:45'),(186,4,'194.40.242.0/24',4,'2024-08-21 15:31:53','2024-08-21 16:53:45','2024-08-21 20:31:54','2024-08-21 21:53:45'),(187,4,'212.4.192.0/19',4,'2024-08-21 15:31:53','2024-08-21 16:53:45','2024-08-21 20:31:54','2024-08-21 21:53:45'),(188,4,'213.239.0.0/18',4,'2024-08-21 15:31:53','2024-08-21 16:53:45','2024-08-21 20:31:54','2024-08-21 21:53:45'),(189,4,'2001:4d68::/32',6,'2024-08-21 15:31:54','2024-08-21 16:53:46','2024-08-21 20:31:54','2024-08-21 21:53:46'),(190,4,'2a0b:6940::/29',6,'2024-08-21 15:31:54','2024-08-21 16:53:46','2024-08-21 20:31:54','2024-08-21 21:53:46'),(191,5,'23.246.0.0/18',4,'2024-08-21 15:47:37','2024-08-21 16:53:43','2024-08-21 20:47:37','2024-08-21 21:53:43'),(192,5,'23.246.20.0/24',4,'2024-08-21 15:47:37','2024-08-21 16:53:43','2024-08-21 20:47:37','2024-08-21 21:53:43'),(193,5,'23.246.30.0/24',4,'2024-08-21 15:47:37','2024-08-21 16:53:43','2024-08-21 20:47:37','2024-08-21 21:53:43'),(194,5,'23.246.31.0/24',4,'2024-08-21 15:47:37','2024-08-21 16:53:43','2024-08-21 20:47:37','2024-08-21 21:53:43'),(195,5,'23.246.50.0/24',4,'2024-08-21 15:47:37','2024-08-21 16:53:43','2024-08-21 20:47:37','2024-08-21 21:53:43'),(196,5,'23.246.51.0/24',4,'2024-08-21 15:47:37','2024-08-21 16:53:43','2024-08-21 20:47:37','2024-08-21 21:53:43'),(197,5,'23.246.55.0/24',4,'2024-08-21 15:47:37','2024-08-21 16:53:43','2024-08-21 20:47:37','2024-08-21 21:53:43'),(198,5,'37.77.184.0/21',4,'2024-08-21 15:47:37','2024-08-21 16:53:43','2024-08-21 20:47:37','2024-08-21 21:53:43'),(199,5,'37.77.186.0/24',4,'2024-08-21 15:47:37','2024-08-21 16:53:43','2024-08-21 20:47:37','2024-08-21 21:53:43'),(200,5,'37.77.187.0/24',4,'2024-08-21 15:47:37','2024-08-21 16:53:43','2024-08-21 20:47:37','2024-08-21 21:53:43'),(201,5,'38.72.126.0/24',4,'2024-08-21 15:47:37','2024-08-21 16:53:43','2024-08-21 20:47:37','2024-08-21 21:53:43'),(202,5,'45.57.0.0/17',4,'2024-08-21 15:47:37','2024-08-21 16:53:43','2024-08-21 20:47:37','2024-08-21 21:53:43'),(203,5,'45.57.8.0/23',4,'2024-08-21 15:47:37','2024-08-21 16:53:43','2024-08-21 20:47:37','2024-08-21 21:53:43'),(204,5,'45.57.8.0/24',4,'2024-08-21 15:47:37','2024-08-21 16:53:43','2024-08-21 20:47:37','2024-08-21 21:53:43'),(205,5,'45.57.9.0/24',4,'2024-08-21 15:47:37','2024-08-21 16:53:43','2024-08-21 20:47:37','2024-08-21 21:53:43'),(206,5,'45.57.16.0/24',4,'2024-08-21 15:47:37','2024-08-21 16:53:43','2024-08-21 20:47:37','2024-08-21 21:53:43'),(207,5,'45.57.17.0/24',4,'2024-08-21 15:47:37','2024-08-21 16:53:43','2024-08-21 20:47:37','2024-08-21 21:53:43'),(208,5,'45.57.40.0/23',4,'2024-08-21 15:47:37','2024-08-21 16:53:43','2024-08-21 20:47:37','2024-08-21 21:53:43'),(209,5,'45.57.40.0/24',4,'2024-08-21 15:47:37','2024-08-21 16:53:43','2024-08-21 20:47:37','2024-08-21 21:53:43'),(210,5,'45.57.41.0/24',4,'2024-08-21 15:47:37','2024-08-21 16:53:43','2024-08-21 20:47:37','2024-08-21 21:53:43'),(211,5,'45.57.50.0/24',4,'2024-08-21 15:47:37','2024-08-21 16:53:43','2024-08-21 20:47:37','2024-08-21 21:53:43'),(212,5,'45.57.51.0/24',4,'2024-08-21 15:47:37','2024-08-21 16:53:43','2024-08-21 20:47:37','2024-08-21 21:53:43'),(213,5,'45.57.60.0/24',4,'2024-08-21 15:47:37','2024-08-21 16:53:43','2024-08-21 20:47:37','2024-08-21 21:53:43'),(214,5,'45.57.72.0/24',4,'2024-08-21 15:47:37','2024-08-21 16:53:43','2024-08-21 20:47:37','2024-08-21 21:53:43'),(215,5,'45.57.73.0/24',4,'2024-08-21 15:47:37','2024-08-21 16:53:43','2024-08-21 20:47:37','2024-08-21 21:53:43'),(216,5,'45.57.74.0/24',4,'2024-08-21 15:47:37','2024-08-21 16:53:43','2024-08-21 20:47:37','2024-08-21 21:53:43'),(217,5,'45.57.75.0/24',4,'2024-08-21 15:47:37','2024-08-21 16:53:43','2024-08-21 20:47:37','2024-08-21 21:53:43'),(218,5,'45.57.76.0/23',4,'2024-08-21 15:47:37','2024-08-21 16:53:43','2024-08-21 20:47:37','2024-08-21 21:53:43'),(219,5,'45.57.76.0/24',4,'2024-08-21 15:47:37','2024-08-21 16:53:43','2024-08-21 20:47:37','2024-08-21 21:53:43'),(220,5,'45.57.77.0/24',4,'2024-08-21 15:47:37','2024-08-21 16:53:43','2024-08-21 20:47:37','2024-08-21 21:53:43'),(221,5,'45.57.78.0/24',4,'2024-08-21 15:47:37','2024-08-21 16:53:43','2024-08-21 20:47:37','2024-08-21 21:53:43'),(222,5,'45.57.79.0/24',4,'2024-08-21 15:47:37','2024-08-21 16:53:43','2024-08-21 20:47:37','2024-08-21 21:53:43'),(223,5,'45.57.86.0/23',4,'2024-08-21 15:47:37','2024-08-21 16:53:43','2024-08-21 20:47:37','2024-08-21 21:53:43'),(224,5,'45.57.86.0/24',4,'2024-08-21 15:47:37','2024-08-21 16:53:43','2024-08-21 20:47:37','2024-08-21 21:53:43'),(225,5,'45.57.87.0/24',4,'2024-08-21 15:47:37','2024-08-21 16:53:43','2024-08-21 20:47:37','2024-08-21 21:53:43'),(226,5,'45.57.90.0/23',4,'2024-08-21 15:47:37','2024-08-21 16:53:43','2024-08-21 20:47:37','2024-08-21 21:53:43'),(227,5,'45.57.90.0/24',4,'2024-08-21 15:47:37','2024-08-21 16:53:43','2024-08-21 20:47:37','2024-08-21 21:53:43'),(228,5,'45.57.91.0/24',4,'2024-08-21 15:47:37','2024-08-21 16:53:43','2024-08-21 20:47:37','2024-08-21 21:53:43'),(229,5,'64.120.128.0/17',4,'2024-08-21 15:47:37','2024-08-21 16:53:43','2024-08-21 20:47:37','2024-08-21 21:53:43'),(230,5,'66.197.128.0/17',4,'2024-08-21 15:47:37','2024-08-21 16:53:43','2024-08-21 20:47:37','2024-08-21 21:53:43'),(231,5,'69.53.224.0/19',4,'2024-08-21 15:47:37','2024-08-21 16:53:43','2024-08-21 20:47:37','2024-08-21 21:53:43'),(232,5,'69.53.242.0/24',4,'2024-08-21 15:47:37','2024-08-21 16:53:43','2024-08-21 20:47:37','2024-08-21 21:53:43'),(233,5,'108.175.32.0/20',4,'2024-08-21 15:47:37','2024-08-21 16:53:43','2024-08-21 20:47:37','2024-08-21 21:53:43'),(234,5,'185.2.220.0/22',4,'2024-08-21 15:47:37','2024-08-21 16:53:43','2024-08-21 20:47:37','2024-08-21 21:53:43'),(235,5,'185.2.220.0/24',4,'2024-08-21 15:47:37','2024-08-21 16:53:43','2024-08-21 20:47:37','2024-08-21 21:53:43'),(236,5,'185.2.221.0/24',4,'2024-08-21 15:47:37','2024-08-21 16:53:43','2024-08-21 20:47:37','2024-08-21 21:53:43'),(237,5,'185.9.188.0/22',4,'2024-08-21 15:47:37','2024-08-21 16:53:43','2024-08-21 20:47:37','2024-08-21 21:53:43'),(238,5,'192.173.64.0/18',4,'2024-08-21 15:47:37','2024-08-21 16:53:43','2024-08-21 20:47:37','2024-08-21 21:53:43'),(239,5,'192.173.98.0/24',4,'2024-08-21 15:47:37','2024-08-21 16:53:43','2024-08-21 20:47:37','2024-08-21 21:53:43'),(240,5,'192.173.99.0/24',4,'2024-08-21 15:47:37','2024-08-21 16:53:43','2024-08-21 20:47:37','2024-08-21 21:53:43'),(241,5,'198.38.96.0/19',4,'2024-08-21 15:47:37','2024-08-21 16:53:43','2024-08-21 20:47:37','2024-08-21 21:53:43'),(242,5,'198.38.116.0/24',4,'2024-08-21 15:47:37','2024-08-21 16:53:43','2024-08-21 20:47:37','2024-08-21 21:53:43'),(243,5,'198.38.117.0/24',4,'2024-08-21 15:47:37','2024-08-21 16:53:43','2024-08-21 20:47:37','2024-08-21 21:53:43'),(244,5,'198.38.118.0/24',4,'2024-08-21 15:47:37','2024-08-21 16:53:43','2024-08-21 20:47:37','2024-08-21 21:53:43'),(245,5,'198.38.119.0/24',4,'2024-08-21 15:47:37','2024-08-21 16:53:43','2024-08-21 20:47:37','2024-08-21 21:53:43'),(246,5,'198.38.120.0/24',4,'2024-08-21 15:47:37','2024-08-21 16:53:43','2024-08-21 20:47:37','2024-08-21 21:53:43'),(247,5,'198.38.121.0/24',4,'2024-08-21 15:47:37','2024-08-21 16:53:43','2024-08-21 20:47:37','2024-08-21 21:53:43'),(248,5,'198.45.48.0/20',4,'2024-08-21 15:47:37','2024-08-21 16:53:43','2024-08-21 20:47:37','2024-08-21 21:53:43'),(249,5,'207.45.72.0/22',4,'2024-08-21 15:47:37','2024-08-21 16:53:43','2024-08-21 20:47:37','2024-08-21 21:53:43'),(250,5,'207.45.72.0/23',4,'2024-08-21 15:47:37','2024-08-21 16:53:43','2024-08-21 20:47:37','2024-08-21 21:53:43'),(251,5,'207.45.73.0/24',4,'2024-08-21 15:47:37','2024-08-21 16:53:43','2024-08-21 20:47:37','2024-08-21 21:53:43'),(252,5,'208.75.76.0/22',4,'2024-08-21 15:47:37','2024-08-21 16:53:43','2024-08-21 20:47:37','2024-08-21 21:53:43'),(253,5,'208.75.76.0/24',4,'2024-08-21 15:47:37','2024-08-21 16:53:43','2024-08-21 20:47:37','2024-08-21 21:53:43'),(254,5,'208.75.77.0/24',4,'2024-08-21 15:47:37','2024-08-21 16:53:43','2024-08-21 20:47:37','2024-08-21 21:53:43'),(255,5,'208.75.78.0/24',4,'2024-08-21 15:47:37','2024-08-21 16:53:43','2024-08-21 20:47:37','2024-08-21 21:53:43'),(256,5,'208.75.79.0/24',4,'2024-08-21 15:47:37','2024-08-21 16:53:43','2024-08-21 20:47:37','2024-08-21 21:53:43'),(257,5,'2607:fb10::/32',6,'2024-08-21 15:47:37','2024-08-21 16:53:44','2024-08-21 20:47:37','2024-08-21 21:53:44'),(258,5,'2607:fb10:2033::/48',6,'2024-08-21 15:47:37','2024-08-21 16:53:44','2024-08-21 20:47:37','2024-08-21 21:53:44'),(259,5,'2607:fb10:2034::/48',6,'2024-08-21 15:47:37','2024-08-21 16:53:44','2024-08-21 20:47:37','2024-08-21 21:53:44'),(260,5,'2607:fb10:2042::/48',6,'2024-08-21 15:47:37','2024-08-21 16:53:44','2024-08-21 20:47:37','2024-08-21 21:53:44'),(261,5,'2620:10c:7000::/44',6,'2024-08-21 15:47:37','2024-08-21 16:53:44','2024-08-21 20:47:37','2024-08-21 21:53:44'),(262,5,'2a00:86c0::/32',6,'2024-08-21 15:47:37','2024-08-21 16:53:44','2024-08-21 20:47:37','2024-08-21 21:53:44'),(263,5,'2a00:86c0:4::/48',6,'2024-08-21 15:47:37','2024-08-21 16:53:44','2024-08-21 20:47:37','2024-08-21 21:53:44'),(264,5,'2a00:86c0:5::/48',6,'2024-08-21 15:47:37','2024-08-21 16:53:44','2024-08-21 20:47:37','2024-08-21 21:53:44'),(265,5,'2a00:86c0:116::/48',6,'2024-08-21 15:47:37','2024-08-21 16:53:44','2024-08-21 20:47:37','2024-08-21 21:53:44'),(266,5,'2a00:86c0:117::/48',6,'2024-08-21 15:47:37','2024-08-21 16:53:44','2024-08-21 20:47:37','2024-08-21 21:53:44'),(267,5,'2a00:86c0:118::/48',6,'2024-08-21 15:47:37','2024-08-21 16:53:44','2024-08-21 20:47:37','2024-08-21 21:53:44'),(268,5,'2a00:86c0:119::/48',6,'2024-08-21 15:47:37','2024-08-21 16:53:44','2024-08-21 20:47:37','2024-08-21 21:53:44'),(269,5,'2a00:86c0:120::/48',6,'2024-08-21 15:47:37','2024-08-21 16:53:44','2024-08-21 20:47:37','2024-08-21 21:53:44'),(270,5,'2a00:86c0:121::/48',6,'2024-08-21 15:47:37','2024-08-21 16:53:44','2024-08-21 20:47:37','2024-08-21 21:53:44'),(271,5,'2a00:86c0:126::/48',6,'2024-08-21 15:47:37','2024-08-21 16:53:44','2024-08-21 20:47:37','2024-08-21 21:53:44'),(272,5,'2a00:86c0:127::/48',6,'2024-08-21 15:47:37','2024-08-21 16:53:44','2024-08-21 20:47:37','2024-08-21 21:53:44'),(273,5,'2a00:86c0:1018::/48',6,'2024-08-21 15:47:37','2024-08-21 16:53:44','2024-08-21 20:47:37','2024-08-21 21:53:44'),(274,5,'2a00:86c0:1026::/48',6,'2024-08-21 15:47:37','2024-08-21 16:53:44','2024-08-21 20:47:37','2024-08-21 21:53:44'),(275,5,'2a00:86c0:1027::/48',6,'2024-08-21 15:47:37','2024-08-21 16:53:44','2024-08-21 20:47:37','2024-08-21 21:53:44'),(276,5,'2a00:86c0:1050::/48',6,'2024-08-21 15:47:37','2024-08-21 16:53:44','2024-08-21 20:47:37','2024-08-21 21:53:44'),(277,5,'2a00:86c0:1051::/48',6,'2024-08-21 15:47:37','2024-08-21 16:53:44','2024-08-21 20:47:37','2024-08-21 21:53:44'),(278,5,'2a00:86c0:2008::/47',6,'2024-08-21 15:47:37','2024-08-21 16:53:44','2024-08-21 20:47:37','2024-08-21 21:53:44'),(279,5,'2a00:86c0:2008::/48',6,'2024-08-21 15:47:37','2024-08-21 16:53:44','2024-08-21 20:47:37','2024-08-21 21:53:44'),(280,5,'2a00:86c0:2009::/48',6,'2024-08-21 15:47:37','2024-08-21 16:53:44','2024-08-21 20:47:37','2024-08-21 21:53:44'),(281,5,'2a00:86c0:2016::/48',6,'2024-08-21 15:47:37','2024-08-21 16:53:44','2024-08-21 20:47:37','2024-08-21 21:53:44'),(282,5,'2a00:86c0:2017::/48',6,'2024-08-21 15:47:37','2024-08-21 16:53:44','2024-08-21 20:47:37','2024-08-21 21:53:44'),(283,5,'2a00:86c0:2040::/47',6,'2024-08-21 15:47:37','2024-08-21 16:53:44','2024-08-21 20:47:37','2024-08-21 21:53:44'),(284,5,'2a00:86c0:2040::/48',6,'2024-08-21 15:47:37','2024-08-21 16:53:44','2024-08-21 20:47:37','2024-08-21 21:53:44'),(285,5,'2a00:86c0:2041::/48',6,'2024-08-21 15:47:37','2024-08-21 16:53:44','2024-08-21 20:47:37','2024-08-21 21:53:44'),(286,5,'2a00:86c0:2051::/48',6,'2024-08-21 15:47:37','2024-08-21 16:53:44','2024-08-21 20:47:37','2024-08-21 21:53:44'),(287,5,'2a00:86c0:2060::/48',6,'2024-08-21 15:47:37','2024-08-21 16:53:44','2024-08-21 20:47:37','2024-08-21 21:53:44'),(288,5,'2a00:86c0:2072::/48',6,'2024-08-21 15:47:37','2024-08-21 16:53:44','2024-08-21 20:47:37','2024-08-21 21:53:44'),(289,5,'2a00:86c0:2073::/48',6,'2024-08-21 15:47:37','2024-08-21 16:53:44','2024-08-21 20:47:37','2024-08-21 21:53:44'),(290,5,'2a00:86c0:2074::/48',6,'2024-08-21 15:47:37','2024-08-21 16:53:44','2024-08-21 20:47:37','2024-08-21 21:53:44'),(291,5,'2a00:86c0:2075::/48',6,'2024-08-21 15:47:37','2024-08-21 16:53:44','2024-08-21 20:47:37','2024-08-21 21:53:44'),(292,5,'2a00:86c0:2076::/47',6,'2024-08-21 15:47:37','2024-08-21 16:53:44','2024-08-21 20:47:37','2024-08-21 21:53:44'),(293,5,'2a00:86c0:2076::/48',6,'2024-08-21 15:47:37','2024-08-21 16:53:44','2024-08-21 20:47:37','2024-08-21 21:53:44'),(294,5,'2a00:86c0:2077::/48',6,'2024-08-21 15:47:37','2024-08-21 16:53:44','2024-08-21 20:47:37','2024-08-21 21:53:44'),(295,5,'2a00:86c0:2078::/48',6,'2024-08-21 15:47:37','2024-08-21 16:53:44','2024-08-21 20:47:37','2024-08-21 21:53:44'),(296,5,'2a00:86c0:2079::/48',6,'2024-08-21 15:47:37','2024-08-21 16:53:44','2024-08-21 20:47:37','2024-08-21 21:53:44'),(297,5,'2a00:86c0:2086::/47',6,'2024-08-21 15:47:37','2024-08-21 16:53:44','2024-08-21 20:47:37','2024-08-21 21:53:44'),(298,5,'2a00:86c0:2086::/48',6,'2024-08-21 15:47:37','2024-08-21 16:53:44','2024-08-21 20:47:37','2024-08-21 21:53:44'),(299,5,'2a00:86c0:2087::/48',6,'2024-08-21 15:47:37','2024-08-21 16:53:44','2024-08-21 20:47:37','2024-08-21 21:53:44'),(300,5,'2a00:86c0:2090::/47',6,'2024-08-21 15:47:37','2024-08-21 16:53:44','2024-08-21 20:47:37','2024-08-21 21:53:44'),(301,5,'2a00:86c0:2090::/48',6,'2024-08-21 15:47:37','2024-08-21 16:53:44','2024-08-21 20:47:37','2024-08-21 21:53:44'),(302,5,'2a00:86c0:2091::/48',6,'2024-08-21 15:47:37','2024-08-21 16:53:44','2024-08-21 20:47:37','2024-08-21 21:53:44'),(303,5,'2a00:86c0:a2a6::/48',6,'2024-08-21 15:47:37','2024-08-21 16:53:44','2024-08-21 20:47:37','2024-08-21 21:53:44'),(304,5,'2a00:86c0:a2a7::/48',6,'2024-08-21 15:47:37','2024-08-21 16:53:44','2024-08-21 20:47:37','2024-08-21 21:53:44'),(305,5,'2a00:86c0:d0b0::/48',6,'2024-08-21 15:47:37','2024-08-21 16:53:44','2024-08-21 20:47:37','2024-08-21 21:53:44'),(306,5,'2a00:86c0:d0b1::/48',6,'2024-08-21 15:47:37','2024-08-21 16:53:44','2024-08-21 20:47:37','2024-08-21 21:53:44'),(307,5,'2a03:5640::/32',6,'2024-08-21 15:47:37','2024-08-21 16:53:44','2024-08-21 20:47:37','2024-08-21 21:53:44'),(308,6,'45.158.144.0/22',4,'2024-08-21 15:53:04','2024-08-21 16:53:49','2024-08-21 20:53:04','2024-08-21 21:53:49'),(309,6,'88.81.96.0/19',4,'2024-08-21 15:53:04','2024-08-21 16:53:49','2024-08-21 20:53:04','2024-08-21 21:53:49'),(310,6,'185.46.252.0/22',4,'2024-08-21 15:53:04','2024-08-21 16:53:49','2024-08-21 20:53:04','2024-08-21 21:53:49'),(311,6,'185.212.184.0/24',4,'2024-08-21 15:53:04','2024-08-21 16:53:49','2024-08-21 20:53:04','2024-08-21 21:53:49');
/*!40000 ALTER TABLE `irrdb_prefix` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `irrdb_update_logs`
--

DROP TABLE IF EXISTS `irrdb_update_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `irrdb_update_logs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `cust_id` int NOT NULL,
  `prefix_v4` datetime DEFAULT NULL,
  `prefix_v6` datetime DEFAULT NULL,
  `asn_v4` datetime DEFAULT NULL,
  `asn_v6` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `irrdb_update_logs_cust_id_unique` (`cust_id`),
  CONSTRAINT `irrdb_update_logs_cust_id_foreign` FOREIGN KEY (`cust_id`) REFERENCES `cust` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `irrdb_update_logs`
--

LOCK TABLES `irrdb_update_logs` WRITE;
/*!40000 ALTER TABLE `irrdb_update_logs` DISABLE KEYS */;
INSERT INTO `irrdb_update_logs` VALUES (1,2,'2024-08-21 16:53:42','2024-08-21 16:53:42','2024-08-21 16:54:03','2024-08-21 16:54:04','2024-08-21 19:44:07','2024-08-21 21:54:04'),(2,3,'2024-08-21 16:53:47','2024-08-21 16:53:48','2024-08-21 16:54:07','2024-08-21 16:54:07','2024-08-21 20:19:24','2024-08-21 21:54:07'),(3,4,'2024-08-21 16:53:45','2024-08-21 16:53:46','2024-08-21 16:54:05','2024-08-21 16:54:06','2024-08-21 20:31:47','2024-08-21 21:54:06'),(4,5,'2024-08-21 16:53:43','2024-08-21 16:53:44','2024-08-21 16:54:04','2024-08-21 16:54:05','2024-08-21 20:47:37','2024-08-21 21:54:05'),(5,6,'2024-08-21 16:53:49',NULL,'2024-08-21 16:54:08',NULL,'2024-08-21 20:52:54','2024-08-21 21:54:08');
/*!40000 ALTER TABLE `irrdb_update_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `irrdbconfig`
--

DROP TABLE IF EXISTS `irrdbconfig`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `irrdbconfig` (
  `id` int NOT NULL AUTO_INCREMENT,
  `host` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `source` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `notes` longtext CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `irrdbconfig`
--

LOCK TABLES `irrdbconfig` WRITE;
/*!40000 ALTER TABLE `irrdbconfig` DISABLE KEYS */;
INSERT INTO `irrdbconfig` VALUES (1,'whois.radb.net','RIPE','RIPE Query from RIPE Database','2024-08-21 18:53:44','2024-08-21 18:53:44'),(2,'whois.radb.net','RIPE,RIPE-NONAUTH','RIPE+RIPE-NONAUTH Query from RIPE Database','2024-08-21 18:53:44','2024-08-21 18:53:44'),(3,'whois.radb.net','RADB','RADB Query from RADB Database','2024-08-21 18:53:44','2024-08-21 18:53:44'),(4,'whois.radb.net','LACNIC','LACNIC Query from LACNIC Database','2024-08-21 18:53:44','2024-08-21 18:53:44'),(5,'whois.radb.net','AFRINIC','AFRINIC Query from AFRINIC Database','2024-08-21 18:53:44','2024-08-21 18:53:44'),(6,'whois.radb.net','APNIC','APNIC Query from APNIC Database','2024-08-21 18:53:44','2024-08-21 18:53:44'),(7,'whois.radb.net','LEVEL3','Level3 Query from Level3 Database','2024-08-21 18:53:44','2024-08-21 18:53:44'),(8,'whois.radb.net','ARIN','ARIN Query from RADB Database','2024-08-21 18:53:44','2024-08-21 18:53:44'),(9,'whois.radb.net','RADB,ARIN','RADB+ARIN Query from RADB Database','2024-08-21 18:53:44','2024-08-21 18:53:44'),(10,'whois.radb.net','ALTDB','ALTDB Query from RADB Database','2024-08-21 18:53:44','2024-08-21 18:53:44'),(11,'whois.radb.net','RADB,RIPE','RADB+RIPE Query from RADB Database','2024-08-21 18:53:44','2024-08-21 18:53:44'),(12,'whois.radb.net','RADB,APNIC,ARIN','RADB+APNIC+ARIN Query from RADB Database','2024-08-21 18:53:44','2024-08-21 18:53:44'),(13,'whois.radb.net','RIPE,ARIN','RIPE+ARIN Query from RADB Database','2024-08-21 18:53:44','2024-08-21 18:53:44');
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
  `mac` varchar(12) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `firstseen` datetime DEFAULT NULL,
  `lastseen` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mac_vlanint` (`mac`,`vlan_interface_id`),
  KEY `IDX_B9482E1D6AB5F82` (`vlan_interface_id`),
  CONSTRAINT `FK_B9482E1D6AB5F82` FOREIGN KEY (`vlan_interface_id`) REFERENCES `vlaninterface` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `l2address`
--

LOCK TABLES `l2address` WRITE;
/*!40000 ALTER TABLE `l2address` DISABLE KEYS */;
INSERT INTO `l2address` VALUES (1,1,'000000010000',NULL,NULL,'2024-08-21 19:41:37','2024-08-21 19:41:37'),(2,2,'000000010001',NULL,NULL,'2024-08-21 19:43:05','2024-08-21 19:43:05'),(3,3,'000002000000',NULL,NULL,'2024-08-21 20:10:30','2024-08-21 20:10:30'),(4,4,'000002000001',NULL,NULL,'2024-08-21 20:18:43','2024-08-21 20:18:43'),(5,5,'000003000000',NULL,NULL,'2024-08-21 20:28:29','2024-08-21 20:28:29'),(6,6,'000003000001',NULL,NULL,'2024-08-21 20:29:37','2024-08-21 20:29:37'),(7,7,'000003000002',NULL,NULL,'2024-08-21 20:30:29','2024-08-21 20:30:29'),(8,8,'000004000000',NULL,NULL,'2024-08-21 20:39:29','2024-08-21 20:39:29'),(9,9,'000004000001',NULL,NULL,'2024-08-21 20:40:33','2024-08-21 20:40:33'),(10,10,'000005000001',NULL,NULL,'2024-08-21 20:52:05','2024-08-21 20:52:05'),(11,11,'100000000000',NULL,NULL,'2024-08-21 20:56:50','2024-08-21 20:56:50'),(12,12,'100000000000',NULL,NULL,'2024-08-21 20:57:32','2024-08-21 20:57:32'),(13,13,'100000110000',NULL,NULL,'2024-08-21 21:00:59','2024-08-21 21:00:59'),(14,14,'100000110000',NULL,NULL,'2024-08-21 21:01:28','2024-08-21 21:01:28'),(15,15,'222222000000',NULL,NULL,'2024-08-21 21:06:09','2024-08-21 21:06:09'),(16,16,'222222010101',NULL,NULL,'2024-08-21 21:07:06','2024-08-21 21:07:06'),(17,17,'222222010203',NULL,NULL,'2024-08-21 21:08:04','2024-08-21 21:08:04');
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
  `name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `shortname` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `tag` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `address` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `nocphone` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `nocfax` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `nocemail` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `officephone` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `officefax` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `officeemail` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `notes` longtext CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `pdb_facility_id` bigint DEFAULT NULL,
  `city` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `country` varchar(2) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_5E9E89CB64082763` (`shortname`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `location`
--

LOCK TABLES `location` WRITE;
/*!40000 ALTER TABLE `location` DISABLE KEYS */;
INSERT INTO `location` VALUES (1,'Facility 1','FAC1','fac1',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Dublin','IE','2024-08-21 18:57:21','2024-08-21 18:57:21'),(2,'Facility 2','FAC2','fac2',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Dublin','IE','2024-08-21 18:57:38','2024-08-21 18:57:38');
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
  `model` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint unsigned DEFAULT NULL,
  `action` varchar(7) COLLATE utf8mb4_unicode_ci NOT NULL,
  `message` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `models` json NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `log_user_id_foreign` (`user_id`),
  KEY `log_action_index` (`action`),
  KEY `log_model_model_id_index` (`model`,`model_id`),
  CONSTRAINT `log_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=324 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `log`
--

LOCK TABLES `log` WRITE;
/*!40000 ALTER TABLE `log` DISABLE KEYS */;
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
  `type` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `original_name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `stored_name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `uploaded_by` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `width` int NOT NULL,
  `height` int NOT NULL,
  `created_at` timestamp NOT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_9F54004F9395C3F3` (`customer_id`),
  CONSTRAINT `FK_9F54004F9395C3F3` FOREIGN KEY (`customer_id`) REFERENCES `cust` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
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
  `mac` varchar(12) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_42CD65F6BFDF15D5` (`virtualinterfaceid`),
  CONSTRAINT `FK_42CD65F6BFDF15D5` FOREIGN KEY (`virtualinterfaceid`) REFERENCES `virtualinterface` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
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
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'2020_06_01_143931_database_schema_at_end_v5',1),(2,'2020_07_21_094354_create_route_server_filters',1),(3,'2020_09_03_153723_add_timestamps',1),(4,'2020_09_18_095136_delete_ixp_table',1),(5,'2020_11_16_102415_database_fixes',1),(6,'2021_03_12_150418_create_log_table',1),(7,'2021_03_30_124916_create_atlas_probes',1),(8,'2021_03_30_125238_create_atlas_runs',1),(9,'2021_03_30_125422_create_atlas_measurements',1),(10,'2021_03_30_125723_create_atlas_results',1),(11,'2021_04_14_101948_update_timestamps',1),(12,'2021_04_14_125742_user_pref',1),(13,'2021_05_18_085721_add_note_infrastructure',1),(14,'2021_05_18_114206_update_pp_prefix_size',1),(15,'2021_06_11_141137_update_db_doctrine2eloquent',1),(16,'2021_07_20_134716_fix_last_updated_and_timestamps',1),(17,'2021_09_16_195333_add_rate_limit_col_to_physint',1),(18,'2021_09_17_144421_modernise_irrdb_conf_table',1),(19,'2021_09_21_100354_create_route_server_filters_prod',1),(20,'2021_09_21_162700_rs_pairing',1),(21,'2022_02_12_183121_add_colo_pp_type_patch_panel',1),(22,'2023_09_26_191150_add_registration_details',1),(23,'2024_03_18_191322_add_export_to_ixf_vlan',1),(24,'2024_08_10_125003_create_irrdb_update_logs',1);
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
  `property` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `ix` int NOT NULL DEFAULT '0',
  `value` longtext CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_F843DE6B8B4937A1` (`vlan_id`),
  KEY `VlanProtoProp` (`protocol`,`property`,`vlan_id`),
  CONSTRAINT `FK_F843DE6B8B4937A1` FOREIGN KEY (`vlan_id`) REFERENCES `vlan` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
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
  `network` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `masklen` int DEFAULT NULL,
  `rs1address` varchar(40) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `rs2address` varchar(40) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `dnsfile` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_6A0AF167F48D6D0` (`vlanid`),
  CONSTRAINT `FK_6A0AF167F48D6D0` FOREIGN KEY (`vlanid`) REFERENCES `vlan` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `networkinfo`
--

LOCK TABLES `networkinfo` WRITE;
/*!40000 ALTER TABLE `networkinfo` DISABLE KEYS */;
INSERT INTO `networkinfo` VALUES (1,1,4,'190.0.2.0',24,NULL,NULL,NULL,'2024-08-21 19:33:42','2024-08-21 19:33:42'),(2,3,4,'190.0.2.0',24,NULL,NULL,NULL,'2024-08-21 19:33:52','2024-08-21 19:33:52'),(3,2,4,'198.51.100.0',24,NULL,NULL,NULL,'2024-08-21 19:34:26','2024-08-21 19:34:26'),(4,4,4,'198.51.100.0',24,NULL,NULL,NULL,'2024-08-21 19:34:34','2024-08-21 19:34:34'),(5,1,6,'2001:db8:0:10::',64,NULL,NULL,NULL,'2024-08-21 19:35:28','2024-08-21 19:35:28'),(6,3,6,'2001:db8:0:10::',64,NULL,NULL,NULL,'2024-08-21 19:35:37','2024-08-21 19:35:37'),(7,2,6,'2001:db8:0:20::',64,NULL,NULL,NULL,'2024-08-21 19:35:47','2024-08-21 19:35:47'),(8,4,6,'2001:db8:0:20::',64,NULL,NULL,NULL,'2024-08-21 19:35:54','2024-08-21 19:35:54');
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
  `oui` varchar(6) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `organisation` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_DAEC0140DAEC0140` (`oui`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
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
  KEY `password_resets_email_index` (`email`(191))
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
  `name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `colo_reference` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `cable_type` int NOT NULL,
  `connector_type` int NOT NULL,
  `installation_date` date DEFAULT NULL,
  `port_prefix` varchar(20) COLLATE utf8mb3_unicode_ci NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `colo_pp_type` tinyint NOT NULL DEFAULT '1',
  `chargeable` int NOT NULL DEFAULT '0',
  `location_notes` longtext CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `u_position` int DEFAULT NULL,
  `mounted_at` smallint DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_79A52562D351EC` (`cabinet_id`),
  CONSTRAINT `FK_79A52562D351EC` FOREIGN KEY (`cabinet_id`) REFERENCES `cabinet` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `patch_panel`
--

LOCK TABLES `patch_panel` WRITE;
/*!40000 ALTER TABLE `patch_panel` DISABLE KEYS */;
INSERT INTO `patch_panel` VALUES (1,1,'Patch Panel F1-R1-A','F1-R1-A',2,3,'2024-01-01','',1,1,2,'',48,1,'2024-08-21 21:35:25','2024-08-21 21:35:25'),(2,4,'Patch Panel F1-R2-A','F1-R2-A',2,3,'2024-01-01','',1,1,2,'',48,1,'2024-08-21 21:36:09','2024-08-21 21:36:09'),(3,2,'Patch Panel F2-R1-A','F2-R1-A',2,3,'2024-01-01','',1,1,2,'',48,1,'2024-08-21 21:36:43','2024-08-21 21:36:43');
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
  `notes` longtext CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `assigned_at` date DEFAULT NULL,
  `connected_at` date DEFAULT NULL,
  `cease_requested_at` date DEFAULT NULL,
  `ceased_at` date DEFAULT NULL,
  `last_state_change` date DEFAULT NULL,
  `internal_use` tinyint(1) NOT NULL DEFAULT '0',
  `chargeable` int NOT NULL DEFAULT '2',
  `duplex_master_id` int DEFAULT NULL,
  `number` smallint NOT NULL,
  `colo_circuit_ref` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `ticket_ref` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `private_notes` longtext CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `owned_by` int NOT NULL DEFAULT '0',
  `loa_code` varchar(25) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `description` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `colo_billing_ref` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
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
) ENGINE=InnoDB AUTO_INCREMENT=85 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `patch_panel_port`
--

LOCK TABLES `patch_panel_port` WRITE;
/*!40000 ALTER TABLE `patch_panel_port` DISABLE KEYS */;
INSERT INTO `patch_panel_port` VALUES (1,49,1,5,3,'','2024-08-21','2024-08-21',NULL,NULL,'2024-08-21',0,2,NULL,1,NULL,NULL,'',1,'Y8kuw1hU2QCn8k7JH4Tsh6rWc','',NULL,'2024-08-21 21:35:25','2024-08-21 21:37:55'),(2,NULL,1,NULL,1,NULL,NULL,NULL,NULL,NULL,'2024-08-21',0,2,1,2,NULL,NULL,NULL,0,NULL,NULL,NULL,'2024-08-21 21:35:25','2024-08-21 21:37:55'),(3,6,1,3,3,'','2024-08-21','2024-08-21',NULL,NULL,'2024-08-21',0,2,NULL,3,NULL,NULL,'',1,'0BapGBFJ2BnpbaJ9pc7ssTZe1','',NULL,'2024-08-21 21:35:25','2024-08-21 21:38:24'),(4,NULL,1,NULL,1,NULL,NULL,NULL,NULL,NULL,'2024-08-21',0,2,3,4,NULL,NULL,NULL,0,NULL,NULL,NULL,'2024-08-21 21:35:26','2024-08-21 21:38:24'),(5,7,1,3,3,'','2024-08-21','2024-08-21',NULL,NULL,'2024-08-21',0,2,NULL,5,NULL,NULL,'',1,'vyGTLi17ZmnyZmDxXWl7XEarE','',NULL,'2024-08-21 21:35:26','2024-08-21 21:38:40'),(6,NULL,1,NULL,1,NULL,NULL,NULL,NULL,NULL,'2024-08-21',0,2,5,6,NULL,NULL,NULL,0,NULL,NULL,NULL,'2024-08-21 21:35:26','2024-08-21 21:38:40'),(7,8,1,4,3,'','2024-08-21','2024-08-21',NULL,NULL,'2024-08-21',0,2,NULL,7,NULL,NULL,'',1,'ENU3rBsxGRlNx4QgsgsecbTdf','',NULL,'2024-08-21 21:35:26','2024-08-21 21:39:18'),(8,NULL,1,NULL,1,NULL,NULL,NULL,NULL,NULL,'2024-08-21',0,2,7,8,NULL,NULL,NULL,0,NULL,NULL,NULL,'2024-08-21 21:35:26','2024-08-21 21:39:18'),(9,NULL,1,NULL,1,NULL,NULL,NULL,NULL,NULL,'2024-08-21',0,2,NULL,9,NULL,NULL,NULL,0,NULL,NULL,NULL,'2024-08-21 21:35:26','2024-08-21 21:35:26'),(10,NULL,1,NULL,1,NULL,NULL,NULL,NULL,NULL,'2024-08-21',0,2,NULL,10,NULL,NULL,NULL,0,NULL,NULL,NULL,'2024-08-21 21:35:26','2024-08-21 21:35:26'),(11,NULL,1,NULL,1,NULL,NULL,NULL,NULL,NULL,'2024-08-21',0,2,NULL,11,NULL,NULL,NULL,0,NULL,NULL,NULL,'2024-08-21 21:35:26','2024-08-21 21:35:26'),(12,NULL,1,NULL,1,NULL,NULL,NULL,NULL,NULL,'2024-08-21',0,2,NULL,12,NULL,NULL,NULL,0,NULL,NULL,NULL,'2024-08-21 21:35:26','2024-08-21 21:35:26'),(13,NULL,1,NULL,1,NULL,NULL,NULL,NULL,NULL,'2024-08-21',0,2,NULL,13,NULL,NULL,NULL,0,NULL,NULL,NULL,'2024-08-21 21:35:26','2024-08-21 21:35:26'),(14,NULL,1,NULL,1,NULL,NULL,NULL,NULL,NULL,'2024-08-21',0,2,NULL,14,NULL,NULL,NULL,0,NULL,NULL,NULL,'2024-08-21 21:35:26','2024-08-21 21:35:26'),(15,NULL,1,NULL,1,NULL,NULL,NULL,NULL,NULL,'2024-08-21',0,2,NULL,15,NULL,NULL,NULL,0,NULL,NULL,NULL,'2024-08-21 21:35:26','2024-08-21 21:35:26'),(16,NULL,1,NULL,1,NULL,NULL,NULL,NULL,NULL,'2024-08-21',0,2,NULL,16,NULL,NULL,NULL,0,NULL,NULL,NULL,'2024-08-21 21:35:26','2024-08-21 21:35:26'),(17,NULL,1,NULL,1,NULL,NULL,NULL,NULL,NULL,'2024-08-21',0,2,NULL,17,NULL,NULL,NULL,0,NULL,NULL,NULL,'2024-08-21 21:35:26','2024-08-21 21:35:26'),(18,NULL,1,NULL,1,NULL,NULL,NULL,NULL,NULL,'2024-08-21',0,2,NULL,18,NULL,NULL,NULL,0,NULL,NULL,NULL,'2024-08-21 21:35:26','2024-08-21 21:35:26'),(19,NULL,1,NULL,1,NULL,NULL,NULL,NULL,NULL,'2024-08-21',0,2,NULL,19,NULL,NULL,NULL,0,NULL,NULL,NULL,'2024-08-21 21:35:26','2024-08-21 21:35:26'),(20,NULL,1,NULL,1,NULL,NULL,NULL,NULL,NULL,'2024-08-21',0,2,NULL,20,NULL,NULL,NULL,0,NULL,NULL,NULL,'2024-08-21 21:35:26','2024-08-21 21:35:26'),(21,NULL,1,NULL,1,NULL,NULL,NULL,NULL,NULL,'2024-08-21',0,2,NULL,21,NULL,NULL,NULL,0,NULL,NULL,NULL,'2024-08-21 21:35:26','2024-08-21 21:35:26'),(22,NULL,1,NULL,1,NULL,NULL,NULL,NULL,NULL,'2024-08-21',0,2,NULL,22,NULL,NULL,NULL,0,NULL,NULL,NULL,'2024-08-21 21:35:26','2024-08-21 21:35:26'),(23,NULL,1,NULL,1,NULL,NULL,NULL,NULL,NULL,'2024-08-21',0,2,NULL,23,NULL,NULL,NULL,0,NULL,NULL,NULL,'2024-08-21 21:35:26','2024-08-21 21:35:26'),(24,NULL,1,NULL,1,NULL,NULL,NULL,NULL,NULL,'2024-08-21',0,2,NULL,24,NULL,NULL,NULL,0,NULL,NULL,NULL,'2024-08-21 21:35:26','2024-08-21 21:35:26'),(25,NULL,1,NULL,1,NULL,NULL,NULL,NULL,NULL,'2024-08-21',0,2,NULL,25,NULL,NULL,NULL,0,NULL,NULL,NULL,'2024-08-21 21:35:26','2024-08-21 21:35:26'),(26,NULL,1,NULL,1,NULL,NULL,NULL,NULL,NULL,'2024-08-21',0,2,NULL,26,NULL,NULL,NULL,0,NULL,NULL,NULL,'2024-08-21 21:35:26','2024-08-21 21:35:26'),(27,NULL,1,NULL,1,NULL,NULL,NULL,NULL,NULL,'2024-08-21',0,2,NULL,27,NULL,NULL,NULL,0,NULL,NULL,NULL,'2024-08-21 21:35:26','2024-08-21 21:35:26'),(28,NULL,1,NULL,1,NULL,NULL,NULL,NULL,NULL,'2024-08-21',0,2,NULL,28,NULL,NULL,NULL,0,NULL,NULL,NULL,'2024-08-21 21:35:26','2024-08-21 21:35:26'),(29,NULL,1,NULL,1,NULL,NULL,NULL,NULL,NULL,'2024-08-21',0,2,NULL,29,NULL,NULL,NULL,0,NULL,NULL,NULL,'2024-08-21 21:35:26','2024-08-21 21:35:26'),(30,NULL,1,NULL,1,NULL,NULL,NULL,NULL,NULL,'2024-08-21',0,2,NULL,30,NULL,NULL,NULL,0,NULL,NULL,NULL,'2024-08-21 21:35:26','2024-08-21 21:35:26'),(31,NULL,1,NULL,1,NULL,NULL,NULL,NULL,NULL,'2024-08-21',0,2,NULL,31,NULL,NULL,NULL,0,NULL,NULL,NULL,'2024-08-21 21:35:26','2024-08-21 21:35:26'),(32,NULL,1,NULL,1,NULL,NULL,NULL,NULL,NULL,'2024-08-21',0,2,NULL,32,NULL,NULL,NULL,0,NULL,NULL,NULL,'2024-08-21 21:35:26','2024-08-21 21:35:26'),(33,NULL,1,NULL,1,NULL,NULL,NULL,NULL,NULL,'2024-08-21',0,2,NULL,33,NULL,NULL,NULL,0,NULL,NULL,NULL,'2024-08-21 21:35:26','2024-08-21 21:35:26'),(34,NULL,1,NULL,1,NULL,NULL,NULL,NULL,NULL,'2024-08-21',0,2,NULL,34,NULL,NULL,NULL,0,NULL,NULL,NULL,'2024-08-21 21:35:26','2024-08-21 21:35:26'),(35,NULL,1,NULL,1,NULL,NULL,NULL,NULL,NULL,'2024-08-21',0,2,NULL,35,NULL,NULL,NULL,0,NULL,NULL,NULL,'2024-08-21 21:35:26','2024-08-21 21:35:26'),(36,NULL,1,NULL,1,NULL,NULL,NULL,NULL,NULL,'2024-08-21',0,2,NULL,36,NULL,NULL,NULL,0,NULL,NULL,NULL,'2024-08-21 21:35:26','2024-08-21 21:35:26'),(37,NULL,1,NULL,1,NULL,NULL,NULL,NULL,NULL,'2024-08-21',0,2,NULL,37,NULL,NULL,NULL,0,NULL,NULL,NULL,'2024-08-21 21:35:26','2024-08-21 21:35:26'),(38,NULL,1,NULL,1,NULL,NULL,NULL,NULL,NULL,'2024-08-21',0,2,NULL,38,NULL,NULL,NULL,0,NULL,NULL,NULL,'2024-08-21 21:35:26','2024-08-21 21:35:26'),(39,NULL,1,NULL,1,NULL,NULL,NULL,NULL,NULL,'2024-08-21',0,2,NULL,39,NULL,NULL,NULL,0,NULL,NULL,NULL,'2024-08-21 21:35:26','2024-08-21 21:35:26'),(40,NULL,1,NULL,1,NULL,NULL,NULL,NULL,NULL,'2024-08-21',0,2,NULL,40,NULL,NULL,NULL,0,NULL,NULL,NULL,'2024-08-21 21:35:26','2024-08-21 21:35:26'),(41,NULL,1,NULL,1,NULL,NULL,NULL,NULL,NULL,'2024-08-21',0,2,NULL,41,NULL,NULL,NULL,0,NULL,NULL,NULL,'2024-08-21 21:35:26','2024-08-21 21:35:26'),(42,NULL,1,NULL,1,NULL,NULL,NULL,NULL,NULL,'2024-08-21',0,2,NULL,42,NULL,NULL,NULL,0,NULL,NULL,NULL,'2024-08-21 21:35:26','2024-08-21 21:35:26'),(43,NULL,1,NULL,1,NULL,NULL,NULL,NULL,NULL,'2024-08-21',0,2,NULL,43,NULL,NULL,NULL,0,NULL,NULL,NULL,'2024-08-21 21:35:26','2024-08-21 21:35:26'),(44,NULL,1,NULL,1,NULL,NULL,NULL,NULL,NULL,'2024-08-21',0,2,NULL,44,NULL,NULL,NULL,0,NULL,NULL,NULL,'2024-08-21 21:35:26','2024-08-21 21:35:26'),(45,53,1,1,3,'','2024-08-21','2024-08-21',NULL,NULL,'2024-08-21',1,2,NULL,45,NULL,NULL,'',2,'8faei1IQTIqQxxXltjFaQGIFM','Core: VIX1 - FAC1 - FAC2',NULL,'2024-08-21 21:35:26','2024-08-21 21:40:26'),(46,NULL,1,NULL,1,NULL,NULL,NULL,NULL,NULL,'2024-08-21',0,2,45,46,NULL,NULL,NULL,0,NULL,NULL,NULL,'2024-08-21 21:35:26','2024-08-21 21:39:48'),(47,54,1,1,3,'','2024-08-21','2024-08-21',NULL,NULL,'2024-08-21',1,2,NULL,47,NULL,NULL,'',2,'IjqvHWeEpYo4uUVtEknsX4R1v','Core: VIX1 - FAC1 - FAC2',NULL,'2024-08-21 21:35:26','2024-08-21 21:40:15'),(48,NULL,1,NULL,1,NULL,NULL,NULL,NULL,NULL,'2024-08-21',0,2,47,48,NULL,NULL,NULL,0,NULL,NULL,NULL,'2024-08-21 21:35:26','2024-08-21 21:40:15'),(49,118,2,4,3,'','2024-08-21','2024-08-21',NULL,NULL,'2024-08-21',0,2,NULL,1,NULL,NULL,'',1,'hFxCIm8ZhvUXXGgzj4cAhf9ho','',NULL,'2024-08-21 21:36:09','2024-08-21 21:40:57'),(50,NULL,2,NULL,1,NULL,NULL,NULL,NULL,NULL,'2024-08-21',0,2,49,2,NULL,NULL,NULL,0,NULL,NULL,NULL,'2024-08-21 21:36:09','2024-08-21 21:40:57'),(51,116,2,3,3,'','2024-08-21','2024-08-21',NULL,NULL,'2024-08-21',0,2,NULL,3,NULL,NULL,'',1,'dCR7kLoU5HRL4HpVN0HFFWwuU','',NULL,'2024-08-21 21:36:09','2024-08-21 21:41:42'),(52,NULL,2,NULL,1,NULL,NULL,NULL,NULL,NULL,'2024-08-21',0,2,51,4,NULL,NULL,NULL,0,NULL,NULL,NULL,'2024-08-21 21:36:09','2024-08-21 21:41:42'),(53,117,2,3,3,'','2024-08-21','2024-08-21',NULL,NULL,'2024-08-21',0,2,NULL,5,NULL,NULL,'',1,'Tj3MOGlxnzzhNlV39N2TaRwJx','',NULL,'2024-08-21 21:36:09','2024-08-21 21:41:54'),(54,NULL,2,NULL,1,NULL,NULL,NULL,NULL,NULL,'2024-08-21',0,2,NULL,6,NULL,NULL,NULL,0,NULL,NULL,NULL,'2024-08-21 21:36:09','2024-08-21 21:36:09'),(55,NULL,2,NULL,1,NULL,NULL,NULL,NULL,NULL,'2024-08-21',0,2,NULL,7,NULL,NULL,NULL,0,NULL,NULL,NULL,'2024-08-21 21:36:09','2024-08-21 21:36:09'),(56,NULL,2,NULL,1,NULL,NULL,NULL,NULL,NULL,'2024-08-21',0,2,NULL,8,NULL,NULL,NULL,0,NULL,NULL,NULL,'2024-08-21 21:36:09','2024-08-21 21:36:09'),(57,NULL,2,NULL,1,NULL,NULL,NULL,NULL,NULL,'2024-08-21',0,2,NULL,9,NULL,NULL,NULL,0,NULL,NULL,NULL,'2024-08-21 21:36:09','2024-08-21 21:36:09'),(58,NULL,2,NULL,1,NULL,NULL,NULL,NULL,NULL,'2024-08-21',0,2,NULL,10,NULL,NULL,NULL,0,NULL,NULL,NULL,'2024-08-21 21:36:09','2024-08-21 21:36:09'),(59,NULL,2,NULL,1,NULL,NULL,NULL,NULL,NULL,'2024-08-21',0,2,NULL,11,NULL,NULL,NULL,0,NULL,NULL,NULL,'2024-08-21 21:36:09','2024-08-21 21:36:09'),(60,NULL,2,NULL,1,NULL,NULL,NULL,NULL,NULL,'2024-08-21',0,2,NULL,12,NULL,NULL,NULL,0,NULL,NULL,NULL,'2024-08-21 21:36:09','2024-08-21 21:36:09'),(61,63,3,4,3,'','2024-08-21','2024-08-21',NULL,NULL,'2024-08-21',0,2,NULL,1,NULL,NULL,'',1,'fO6xT6AucuFyhmrhWNN32QYlA','',NULL,'2024-08-21 21:36:43','2024-08-21 21:42:20'),(62,NULL,3,NULL,1,NULL,NULL,NULL,NULL,NULL,'2024-08-21',0,2,61,2,NULL,NULL,NULL,0,NULL,NULL,NULL,'2024-08-21 21:36:43','2024-08-21 21:42:20'),(63,68,3,6,3,'','2024-08-21','2024-08-21',NULL,NULL,'2024-08-21',0,2,NULL,3,NULL,NULL,'',1,'TbJmTBv6L8dmoHDSJ0goicgE0','',NULL,'2024-08-21 21:36:43','2024-08-21 21:42:35'),(64,NULL,3,NULL,1,NULL,NULL,NULL,NULL,NULL,'2024-08-21',0,2,63,4,NULL,NULL,NULL,0,NULL,NULL,NULL,'2024-08-21 21:36:43','2024-08-21 21:42:35'),(65,NULL,3,NULL,1,NULL,NULL,NULL,NULL,NULL,'2024-08-21',0,2,NULL,5,NULL,NULL,NULL,0,NULL,NULL,NULL,'2024-08-21 21:36:43','2024-08-21 21:36:43'),(66,NULL,3,NULL,1,NULL,NULL,NULL,NULL,NULL,'2024-08-21',0,2,NULL,6,NULL,NULL,NULL,0,NULL,NULL,NULL,'2024-08-21 21:36:43','2024-08-21 21:36:43'),(67,NULL,3,NULL,1,NULL,NULL,NULL,NULL,NULL,'2024-08-21',0,2,NULL,7,NULL,NULL,NULL,0,NULL,NULL,NULL,'2024-08-21 21:36:43','2024-08-21 21:36:43'),(68,NULL,3,NULL,1,NULL,NULL,NULL,NULL,NULL,'2024-08-21',0,2,NULL,8,NULL,NULL,NULL,0,NULL,NULL,NULL,'2024-08-21 21:36:43','2024-08-21 21:36:43'),(69,NULL,3,NULL,1,NULL,NULL,NULL,NULL,NULL,'2024-08-21',0,2,NULL,9,NULL,NULL,NULL,0,NULL,NULL,NULL,'2024-08-21 21:36:43','2024-08-21 21:36:43'),(70,NULL,3,NULL,1,NULL,NULL,NULL,NULL,NULL,'2024-08-21',0,2,NULL,10,NULL,NULL,NULL,0,NULL,NULL,NULL,'2024-08-21 21:36:43','2024-08-21 21:36:43'),(71,NULL,3,NULL,1,NULL,NULL,NULL,NULL,NULL,'2024-08-21',0,2,NULL,11,NULL,NULL,NULL,0,NULL,NULL,NULL,'2024-08-21 21:36:43','2024-08-21 21:36:43'),(72,NULL,3,NULL,1,NULL,NULL,NULL,NULL,NULL,'2024-08-21',0,2,NULL,12,NULL,NULL,NULL,0,NULL,NULL,NULL,'2024-08-21 21:36:43','2024-08-21 21:36:43'),(73,NULL,3,NULL,1,NULL,NULL,NULL,NULL,NULL,'2024-08-21',0,2,NULL,13,NULL,NULL,NULL,0,NULL,NULL,NULL,'2024-08-21 21:36:43','2024-08-21 21:36:43'),(74,NULL,3,NULL,1,NULL,NULL,NULL,NULL,NULL,'2024-08-21',0,2,NULL,14,NULL,NULL,NULL,0,NULL,NULL,NULL,'2024-08-21 21:36:43','2024-08-21 21:36:43'),(75,NULL,3,NULL,1,NULL,NULL,NULL,NULL,NULL,'2024-08-21',0,2,NULL,15,NULL,NULL,NULL,0,NULL,NULL,NULL,'2024-08-21 21:36:43','2024-08-21 21:36:43'),(76,NULL,3,NULL,1,NULL,NULL,NULL,NULL,NULL,'2024-08-21',0,2,NULL,16,NULL,NULL,NULL,0,NULL,NULL,NULL,'2024-08-21 21:36:43','2024-08-21 21:36:43'),(77,NULL,3,NULL,1,NULL,NULL,NULL,NULL,NULL,'2024-08-21',0,2,NULL,17,NULL,NULL,NULL,0,NULL,NULL,NULL,'2024-08-21 21:36:43','2024-08-21 21:36:43'),(78,NULL,3,NULL,1,NULL,NULL,NULL,NULL,NULL,'2024-08-21',0,2,NULL,18,NULL,NULL,NULL,0,NULL,NULL,NULL,'2024-08-21 21:36:43','2024-08-21 21:36:43'),(79,NULL,3,NULL,1,NULL,NULL,NULL,NULL,NULL,'2024-08-21',0,2,NULL,19,NULL,NULL,NULL,0,NULL,NULL,NULL,'2024-08-21 21:36:43','2024-08-21 21:36:43'),(80,NULL,3,NULL,1,NULL,NULL,NULL,NULL,NULL,'2024-08-21',0,2,NULL,20,NULL,NULL,NULL,0,NULL,NULL,NULL,'2024-08-21 21:36:43','2024-08-21 21:36:43'),(81,NULL,3,NULL,1,NULL,NULL,NULL,NULL,NULL,'2024-08-21',0,2,NULL,21,NULL,NULL,NULL,0,NULL,NULL,NULL,'2024-08-21 21:36:43','2024-08-21 21:36:43'),(82,NULL,3,NULL,1,NULL,NULL,NULL,NULL,NULL,'2024-08-21',0,2,NULL,22,NULL,NULL,NULL,0,NULL,NULL,NULL,'2024-08-21 21:36:43','2024-08-21 21:36:43'),(83,NULL,3,NULL,1,NULL,NULL,NULL,NULL,NULL,'2024-08-21',0,2,NULL,23,NULL,NULL,NULL,0,NULL,NULL,NULL,'2024-08-21 21:36:43','2024-08-21 21:36:43'),(84,NULL,3,NULL,1,NULL,NULL,NULL,NULL,NULL,'2024-08-21',0,2,NULL,24,NULL,NULL,NULL,0,NULL,NULL,NULL,'2024-08-21 21:36:43','2024-08-21 21:36:43');
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
  `name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `type` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `uploaded_at` datetime NOT NULL,
  `uploaded_by` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `size` int NOT NULL,
  `is_private` tinyint(1) NOT NULL DEFAULT '0',
  `storage_location` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_28089403B0F978FF` (`patch_panel_port_id`),
  CONSTRAINT `FK_28089403B0F978FF` FOREIGN KEY (`patch_panel_port_id`) REFERENCES `patch_panel_port` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
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
  `notes` longtext CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `assigned_at` date DEFAULT NULL,
  `connected_at` date DEFAULT NULL,
  `cease_requested_at` date DEFAULT NULL,
  `ceased_at` date DEFAULT NULL,
  `internal_use` tinyint(1) NOT NULL DEFAULT '0',
  `chargeable` int NOT NULL DEFAULT '0',
  `customer` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `switchport` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `duplex_master_id` int DEFAULT NULL,
  `number` smallint NOT NULL,
  `colo_circuit_ref` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `ticket_ref` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `private_notes` longtext CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `owned_by` int NOT NULL DEFAULT '0',
  `description` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `colo_billing_ref` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `cust_id` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_CB80B54AB0F978FF` (`patch_panel_port_id`),
  KEY `IDX_CB80B54A3838446` (`duplex_master_id`),
  CONSTRAINT `FK_CB80B54A3838446` FOREIGN KEY (`duplex_master_id`) REFERENCES `patch_panel_port_history` (`id`),
  CONSTRAINT `FK_CB80B54AB0F978FF` FOREIGN KEY (`patch_panel_port_id`) REFERENCES `patch_panel_port` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
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
  `name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `type` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `uploaded_at` datetime NOT NULL,
  `uploaded_by` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `size` int NOT NULL,
  `is_private` tinyint(1) NOT NULL DEFAULT '0',
  `storage_location` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_206EAD4E6F461430` (`patch_panel_port_history_id`),
  CONSTRAINT `FK_206EAD4E6F461430` FOREIGN KEY (`patch_panel_port_history_id`) REFERENCES `patch_panel_port_history` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
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
  `notes` longtext CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `created_at` timestamp NOT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_35A72597DA0209B9` (`custid`),
  KEY `IDX_35A725974E5F9AFF` (`peerid`),
  CONSTRAINT `FK_35A725974E5F9AFF` FOREIGN KEY (`peerid`) REFERENCES `cust` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_35A72597DA0209B9` FOREIGN KEY (`custid`) REFERENCES `cust` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
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
  `peering_status` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_C1A6F6F9A4CA6408` (`x_custid`),
  KEY `IDX_C1A6F6F968606496` (`y_custid`),
  CONSTRAINT `FK_C1A6F6F968606496` FOREIGN KEY (`y_custid`) REFERENCES `cust` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_C1A6F6F9A4CA6408` FOREIGN KEY (`x_custid`) REFERENCES `cust` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
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
  `virtualinterfaceid` int DEFAULT NULL,
  `status` int DEFAULT NULL,
  `speed` int DEFAULT NULL,
  `duplex` varchar(16) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `rate_limit` int unsigned DEFAULT NULL,
  `notes` longtext CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `fanout_physical_interface_id` int DEFAULT NULL,
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
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `physicalinterface`
--

LOCK TABLES `physicalinterface` WRITE;
/*!40000 ALTER TABLE `physicalinterface` DISABLE KEYS */;
INSERT INTO `physicalinterface` VALUES (1,2,1,1,1000,'full',NULL,NULL,NULL,1,'2024-08-21 19:41:21','2024-08-21 19:41:21'),(2,112,2,1,1000,'full',NULL,NULL,NULL,1,'2024-08-21 19:42:49','2024-08-21 19:42:49'),(3,6,3,1,10000,'full',NULL,NULL,NULL,1,'2024-08-21 20:10:03','2024-08-21 20:10:03'),(4,7,3,1,10000,'full',NULL,NULL,NULL,1,'2024-08-21 20:10:18','2024-08-21 20:10:21'),(5,116,4,1,10000,'full',NULL,NULL,NULL,1,'2024-08-21 20:17:05','2024-08-21 20:17:05'),(6,117,4,1,10000,'full',NULL,NULL,NULL,1,'2024-08-21 20:19:04','2024-08-21 20:19:04'),(7,8,5,1,10000,'full',NULL,NULL,NULL,1,'2024-08-21 20:28:11','2024-08-21 20:28:11'),(8,63,6,1,10000,'full',NULL,NULL,NULL,1,'2024-08-21 20:29:29','2024-08-21 20:29:29'),(9,118,7,1,10000,'full',NULL,NULL,NULL,1,'2024-08-21 20:30:18','2024-08-21 20:30:18'),(10,49,8,1,100000,'full',NULL,NULL,NULL,1,'2024-08-21 20:39:20','2024-08-21 20:39:20'),(11,159,9,1,100000,'full',NULL,NULL,NULL,1,'2024-08-21 20:40:24','2024-08-21 20:40:24'),(12,68,10,1,1000,'full',NULL,NULL,NULL,1,'2024-08-21 20:51:52','2024-08-21 20:51:52'),(13,48,11,1,1000,'full',NULL,NULL,NULL,1,'2024-08-21 20:56:19','2024-08-21 20:56:19'),(14,158,12,1,1000,'full',NULL,NULL,NULL,1,'2024-08-21 21:00:47','2024-08-21 21:00:47'),(15,47,13,1,1000,'full',NULL,NULL,NULL,1,'2024-08-21 21:05:55','2024-08-21 21:05:55'),(16,102,14,1,1000,'full',NULL,NULL,NULL,1,'2024-08-21 21:07:00','2024-08-21 21:07:00'),(17,157,15,1,1000,'full',NULL,NULL,NULL,1,'2024-08-21 21:07:57','2024-08-21 21:07:57'),(18,53,16,1,100000,'full',NULL,NULL,NULL,1,'2024-08-21 21:29:17','2024-08-21 21:29:17'),(19,108,17,1,100000,'full',NULL,NULL,NULL,1,'2024-08-21 21:29:17','2024-08-21 21:29:17'),(20,54,16,1,100000,'full',NULL,NULL,NULL,1,'2024-08-21 21:29:17','2024-08-21 21:29:17'),(21,109,17,1,100000,'full',NULL,NULL,NULL,1,'2024-08-21 21:29:17','2024-08-21 21:29:17');
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
  `received_prefix` varchar(43) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `advertised_prefix` varchar(43) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `protocol` smallint DEFAULT NULL,
  `action_advertise` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `action_receive` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `enabled` tinyint(1) NOT NULL DEFAULT '1',
  `order_by` int NOT NULL,
  `live` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
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
-- Table structure for table `route_server_filters_prod`
--

DROP TABLE IF EXISTS `route_server_filters_prod`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `route_server_filters_prod` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `customer_id` int DEFAULT NULL,
  `peer_id` int DEFAULT NULL,
  `vlan_id` int DEFAULT NULL,
  `received_prefix` varchar(43) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `advertised_prefix` varchar(43) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `protocol` smallint DEFAULT NULL,
  `action_advertise` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `action_receive` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `enabled` tinyint(1) NOT NULL DEFAULT '1',
  `order_by` int NOT NULL,
  `live` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `route_server_filters_prod_customer_id_order_by_unique` (`customer_id`,`order_by`),
  KEY `route_server_filters_prod_peer_id_foreign` (`peer_id`),
  KEY `route_server_filters_prod_vlan_id_foreign` (`vlan_id`),
  CONSTRAINT `route_server_filters_prod_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `cust` (`id`),
  CONSTRAINT `route_server_filters_prod_peer_id_foreign` FOREIGN KEY (`peer_id`) REFERENCES `cust` (`id`),
  CONSTRAINT `route_server_filters_prod_vlan_id_foreign` FOREIGN KEY (`vlan_id`) REFERENCES `vlan` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `route_server_filters_prod`
--

LOCK TABLES `route_server_filters_prod` WRITE;
/*!40000 ALTER TABLE `route_server_filters_prod` DISABLE KEYS */;
/*!40000 ALTER TABLE `route_server_filters_prod` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `routers`
--

DROP TABLE IF EXISTS `routers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `routers` (
  `id` int NOT NULL AUTO_INCREMENT,
  `pair_id` int DEFAULT NULL,
  `vlan_id` int NOT NULL,
  `handle` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `protocol` smallint unsigned NOT NULL,
  `type` smallint unsigned NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `shortname` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `router_id` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `peering_ip` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `asn` int unsigned NOT NULL,
  `software` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `mgmt_host` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `api` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `api_type` smallint unsigned NOT NULL,
  `lg_access` smallint unsigned DEFAULT NULL,
  `quarantine` tinyint(1) NOT NULL,
  `bgp_lc` tinyint(1) NOT NULL,
  `template` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `skip_md5` tinyint(1) NOT NULL,
  `last_update_started` datetime DEFAULT NULL,
  `last_updated` datetime DEFAULT NULL,
  `pause_updates` tinyint(1) NOT NULL DEFAULT '0',
  `rpki` tinyint(1) NOT NULL DEFAULT '0',
  `software_version` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `operating_system` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `operating_system_version` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `rfc1997_passthru` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_504FC9BE918020D9` (`handle`),
  KEY `IDX_504FC9BE8B4937A1` (`vlan_id`),
  KEY `routers_pair_id_foreign` (`pair_id`),
  CONSTRAINT `FK_504FC9BE8B4937A1` FOREIGN KEY (`vlan_id`) REFERENCES `vlan` (`id`),
  CONSTRAINT `routers_pair_id_foreign` FOREIGN KEY (`pair_id`) REFERENCES `routers` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `routers`
--

LOCK TABLES `routers` WRITE;
/*!40000 ALTER TABLE `routers` DISABLE KEYS */;
INSERT INTO `routers` VALUES (1,2,1,'rs1-vix1-ipv4',4,1,'Route Server #1 - VIX1 - IPv4','RS1 - VIX1 - IPv4','192.0.2.8','192.0.2.8',65501,'6','192.0.2.8',NULL,0,0,0,1,'api/v4/router/server/bird2/standard',0,NULL,NULL,0,0,NULL,NULL,NULL,1,'2024-08-21 21:12:45','2024-08-21 21:15:11'),(2,1,1,'rs2-vix1-ipv4',4,1,'Route Server #2 - VIX1 - IPv4','RS2 - VIX1 - IPv4','192.0.2.9','192.0.2.9',65501,'6','192.0.2.9',NULL,0,NULL,0,1,'api/v4/router/server/bird2/standard',0,NULL,NULL,0,0,NULL,NULL,NULL,1,'2024-08-21 21:13:52','2024-08-21 21:13:52'),(3,4,1,'rs1-vix1-ipv6',6,1,'Route Server #1 - VIX1 - IPv6','RS1 - VIX1 - IPv6','192.0.2.8','2001:db8:0:10::8',65501,'6','192.0.2.8',NULL,0,0,0,1,'api/v4/router/server/bird2/standard',0,NULL,NULL,0,0,NULL,NULL,NULL,1,'2024-08-21 21:14:45','2024-08-21 21:16:13'),(4,3,1,'rs2-vix1-ipv6',6,1,'Route Server #2 - VIX1 - IPv6','RS2 - VIX1 - IPv6','192.0.2.9','2001:db8:0:10::9',65501,'6','192.0.2.9',NULL,0,NULL,0,1,'api/v4/router/server/bird2/standard',0,NULL,NULL,0,0,NULL,NULL,NULL,1,'2024-08-21 21:16:04','2024-08-21 21:16:04'),(5,6,2,'rs1-vix2-ipv4',4,1,'Route Server #1 - VIX2 - IPv4','RS1 - VIX2 - IPv4','198.51.100.8','198.51.100.8',65501,'6','198.51.100.8',NULL,0,0,0,1,'api/v4/router/server/bird2/standard',0,NULL,NULL,0,0,NULL,NULL,NULL,1,'2024-08-21 21:17:26','2024-08-21 21:18:23'),(6,5,2,'rs1-vix2-ipv6',6,1,'Route Server #1 - VIX2 - IPv6','RS2 - VIX1 - IPv6','198.51.100.8','2001:db8:0:20::8',65501,'6','198.51.100.8',NULL,0,NULL,0,1,'api/v4/router/server/bird2/standard',0,NULL,NULL,0,0,NULL,NULL,NULL,1,'2024-08-21 21:18:12','2024-08-21 21:18:12'),(7,8,1,'rc1-vix1-ipv4',4,2,'Route Collector #1 - VIX1 - IPv4','RC1 - VIX1 - IPv4','192.0.2.126','192.0.2.126',65500,'6','192.0.2.126',NULL,0,NULL,0,1,'api/v4/router/collector/bird2/standard',0,NULL,NULL,0,0,NULL,NULL,NULL,1,'2024-08-21 21:19:34','2024-08-21 21:19:34'),(8,7,1,'rc1-vix1-ipv6',6,2,'Route Collector #1 - VIX1 - IPv6','RC1 - VIX1 - IPv6','192.0.2.126','2001:db8:0:10::126',65500,'6','192.0.2.126',NULL,0,NULL,0,1,'api/v4/router/collector/bird2/standard',0,NULL,NULL,0,0,NULL,NULL,NULL,1,'2024-08-21 21:20:34','2024-08-21 21:20:34'),(9,10,2,'rc1-vix2-ipv4',4,2,'Route Collector #1 - VIX2 - IPv4','RC1 - VIX2 - IPv4','192.0.2.126','198.51.100.126',65500,'6','198.51.100.126',NULL,0,NULL,0,1,'api/v4/router/collector/bird2/standard',0,NULL,NULL,0,0,NULL,NULL,NULL,1,'2024-08-21 21:19:34','2024-08-21 21:19:34'),(10,9,2,'rc1-vix2-ipv6',6,2,'Route Collector #1 - VIX2 - IPv6','RC1 - VIX2 - IPv6','192.0.2.126','2001:db8:0:20::126',65500,'6','198.51.100.126',NULL,0,NULL,0,1,'api/v4/router/collector/bird2/standard',0,NULL,NULL,0,0,NULL,NULL,NULL,1,'2024-08-21 21:20:34','2024-08-21 21:20:34'),(11,12,3,'rc1q-vix1-ipv4',4,2,'Quarantine Route Collector #1 - VIX1 - IPv4','RC1Q - VIX1 - IPv4','192.0.2.126','192.0.2.126',65500,'6','192.0.2.126',NULL,0,NULL,1,1,'api/v4/router/collector/bird2/standard',0,NULL,NULL,0,0,NULL,NULL,NULL,1,'2024-08-21 21:19:34','2024-08-21 21:19:34'),(12,11,3,'rc1q-vix1-ipv6',6,2,'Quarantine Route Collector #1 - VIX1 - IPv6','RC1Q - VIX1 - IPv6','192.0.2.126','2001:db8:0:10::126',65500,'6','192.0.2.126',NULL,0,NULL,1,1,'api/v4/router/collector/bird2/standard',0,NULL,NULL,0,0,NULL,NULL,NULL,1,'2024-08-21 21:20:34','2024-08-21 21:20:34'),(13,14,4,'rc1q-vix2-ipv4',4,2,'Quarantine Route Collector #1 - VIX2 - IPv4','RC1Q - VIX2 - IPv4','198.51.100.126','198.51.100.126',65500,'6','198.51.100.126',NULL,0,NULL,1,1,'api/v4/router/collector/bird2/standard',0,NULL,NULL,0,0,NULL,NULL,NULL,1,'2024-08-21 21:19:34','2024-08-21 21:19:34'),(14,13,4,'rc1q-vix2-ipv6',6,2,'Quarantine Route Collector #1 - VIX2 - IPv6','RC1Q - VIX2 - IPv6','198.51.100.126','2001:db8:0:20::126',65500,'6','198.51.100.126',NULL,0,NULL,1,1,'api/v4/router/collector/bird2/standard',0,NULL,NULL,0,0,NULL,NULL,NULL,1,'2024-08-21 21:20:34','2024-08-21 21:20:34'),(15,16,1,'as112-vix1-ipv4',4,3,'AS112 - VIX1 - IPv4','AS112 - VIX1 - IPv4','192.0.2.6','192.0.2.6',112,'1','192.0.2.6',NULL,0,NULL,0,1,'api/v4/router/as112/bird/standard',1,NULL,NULL,0,0,NULL,NULL,NULL,1,'2024-08-21 21:24:52','2024-08-21 21:24:52'),(16,15,1,'as112-vix1-ipv6',6,3,'AS112 - VIX1 - IPv6','AS112 - VIX1 - IPv6','192.0.2.6','2001:db8:0:10::6',112,'1','192.0.2.6',NULL,0,NULL,0,1,'api/v4/router/as112/bird/standard',1,NULL,NULL,0,0,NULL,NULL,NULL,1,'2024-08-21 21:24:52','2024-08-21 21:24:52'),(17,18,2,'as112-vix2-ipv4',4,3,'AS112 - VIX2 - IPv4','AS112 - VIX2 - IPv4','198.51.100.6','198.51.100.6',112,'1','198.51.100.6',NULL,0,NULL,0,1,'api/v4/router/as112/bird/standard',1,NULL,NULL,0,0,NULL,NULL,NULL,1,'2024-08-21 21:24:52','2024-08-21 21:24:52'),(18,17,2,'as112-vix2-ipv6',6,3,'AS112 - VIX2 - IPv6','AS112 - VIX2 - IPv6','198.51.100.6','2001:db8:0:20::6',112,'1','198.51.100.6',NULL,0,NULL,0,1,'api/v4/router/as112/bird/standard',1,NULL,NULL,0,0,NULL,NULL,NULL,1,'2024-08-21 21:24:52','2024-08-21 21:24:52');
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
  `prefix` varchar(64) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `protocol` int DEFAULT NULL,
  `irrdb` int DEFAULT NULL,
  `rs_origin` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_29FA9871DA0209B9` (`custid`),
  CONSTRAINT `FK_29FA9871DA0209B9` FOREIGN KEY (`custid`) REFERENCES `cust` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
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
  `user_agent` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
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
  `dst_ip` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `dst_port` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_E633EA142C0D6F5F` (`virtual_interface_id`),
  CONSTRAINT `FK_E633EA142C0D6F5F` FOREIGN KEY (`virtual_interface_id`) REFERENCES `virtualinterface` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
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
  `cabinetid` int DEFAULT NULL,
  `vendorid` int DEFAULT NULL,
  `name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `ipv4addr` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `ipv6addr` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `snmppasswd` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `infrastructure` int DEFAULT NULL,
  `model` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `active` tinyint(1) DEFAULT '1',
  `notes` longtext CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `hostname` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `os` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `osDate` datetime DEFAULT NULL,
  `osVersion` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `serialNumber` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `lastPolled` datetime DEFAULT NULL,
  `mauSupported` tinyint(1) DEFAULT NULL,
  `asn` int unsigned DEFAULT NULL,
  `loopback_ip` varchar(39) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `loopback_name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `mgmt_mac_address` varchar(12) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `snmp_engine_time` bigint DEFAULT NULL,
  `snmp_system_uptime` bigint DEFAULT NULL,
  `snmp_engine_boots` bigint DEFAULT NULL,
  `poll` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_6FE94B185E237E06` (`name`),
  UNIQUE KEY `UNIQ_6FE94B1850C101F8` (`loopback_ip`),
  KEY `IDX_6FE94B182B96718A` (`cabinetid`),
  KEY `IDX_6FE94B18420FB55F` (`vendorid`),
  KEY `IDX_6FE94B18D129B190` (`infrastructure`),
  CONSTRAINT `FK_6FE94B182B96718A` FOREIGN KEY (`cabinetid`) REFERENCES `cabinet` (`id`),
  CONSTRAINT `FK_6FE94B18420FB55F` FOREIGN KEY (`vendorid`) REFERENCES `vendor` (`id`),
  CONSTRAINT `FK_6FE94B18D129B190` FOREIGN KEY (`infrastructure`) REFERENCES `infrastructure` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `switch`
--

LOCK TABLES `switch` WRITE;
/*!40000 ALTER TABLE `switch` DISABLE KEYS */;
INSERT INTO `switch` VALUES (1,1,3,'swi1-fac1-1','127.0.0.1',NULL,'swi1-fac1-1',1,'DCS-7280SR-48C6',1,NULL,'swi1-fac1-1','EOS',NULL,'4.18.0F',NULL,'2024-08-21 16:53:34',1,NULL,NULL,NULL,'',28562428,2856245011,3,1,'2024-08-21 19:16:28','2024-08-21 21:53:34'),(2,2,3,'swi1-fac2-1','127.0.0.1',NULL,'swi1-fac2-1',1,'DCS-7280SR-48C6',1,NULL,'swi1-fac2-1','EOS',NULL,'4.18.0F',NULL,'2024-08-21 16:53:34',1,NULL,NULL,NULL,'',28562428,2856245011,3,1,'2024-08-21 19:18:37','2024-08-21 21:53:34'),(3,4,3,'swi2-fac1-1','127.0.0.1',NULL,'swi2-fac1-1',2,'DCS-7280SR-48C6',1,NULL,'swi2-fac1-1','EOS',NULL,'4.18.0F',NULL,'2024-08-21 16:53:35',1,NULL,NULL,NULL,'',28562428,2856245011,3,1,'2024-08-21 19:18:54','2024-08-21 21:53:35');
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
  `name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `ifName` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `ifAlias` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `ifHighSpeed` int DEFAULT NULL,
  `ifMtu` int DEFAULT NULL,
  `ifPhysAddress` varchar(17) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `ifAdminStatus` int DEFAULT NULL,
  `ifOperStatus` int DEFAULT NULL,
  `ifLastChange` int DEFAULT NULL,
  `lastSnmpPoll` datetime DEFAULT NULL,
  `ifIndex` int DEFAULT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `lagIfIndex` int DEFAULT NULL,
  `mauType` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `mauState` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `mauAvailability` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `mauJacktype` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `mauAutoNegSupported` tinyint(1) DEFAULT NULL,
  `mauAutoNegAdminState` tinyint(1) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_F84274F1DC2C08F8` (`switchid`),
  CONSTRAINT `FK_F84274F1DC2C08F8` FOREIGN KEY (`switchid`) REFERENCES `switch` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=166 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `switchport`
--

LOCK TABLES `switchport` WRITE;
/*!40000 ALTER TABLE `switchport` DISABLE KEYS */;
INSERT INTO `switchport` VALUES (1,1,0,'Ethernet1','Ethernet1',NULL,10000,9214,'444CA8B9427E',1,6,1695696920,'2024-08-21 16:53:34',1,1,NULL,'(empty)','shutdown','available',NULL,0,0,'2024-08-21 19:18:08','2024-08-21 21:53:34'),(2,1,1,'Ethernet2','Ethernet2','Cust: AS112',1000,9214,'444CA8B9427F',1,1,1696047261,'2024-08-21 16:53:34',2,1,NULL,'10GigBaseLR','operational','available',NULL,0,0,'2024-08-21 19:18:08','2024-08-21 21:53:34'),(3,1,0,'Ethernet3','Ethernet3',NULL,10000,9214,'444CA8B94280',1,6,1695696920,'2024-08-21 16:53:34',3,1,NULL,'(empty)','shutdown','available',NULL,0,0,'2024-08-21 19:18:08','2024-08-21 21:53:34'),(4,1,0,'Ethernet4','Ethernet4',NULL,10000,9214,'444CA8B94281',1,6,1695696920,'2024-08-21 16:53:34',4,1,NULL,'(empty)','shutdown','available',NULL,0,0,'2024-08-21 19:18:08','2024-08-21 21:53:34'),(5,1,0,'Ethernet5','Ethernet5',NULL,10000,9214,'444CA8B94282',1,6,1695696920,'2024-08-21 16:53:34',5,1,NULL,'(empty)','shutdown','available',NULL,0,0,'2024-08-21 19:18:08','2024-08-21 21:53:34'),(6,1,1,'Ethernet6','Ethernet6','Cust: NREN',10000,9214,'444CA8B94283',1,1,1710084965,'2024-08-21 16:53:34',6,1,NULL,'10GigBaseLR','operational','available',NULL,0,0,'2024-08-21 19:18:08','2024-08-21 21:53:34'),(7,1,1,'Ethernet7','Ethernet7','Cust: NREN',10000,9214,'444CA8B94284',1,1,1696047920,'2024-08-21 16:53:34',7,1,NULL,'10GigBaseLR','operational','available',NULL,0,0,'2024-08-21 19:18:08','2024-08-21 21:53:34'),(8,1,1,'Ethernet8','Ethernet8','Cust: Eyeball ISP',1000,9214,'444CA8B94285',1,1,1706930099,'2024-08-21 16:53:34',8,1,NULL,'10GigBaseLR','operational','available',NULL,0,0,'2024-08-21 19:18:08','2024-08-21 21:53:34'),(9,1,0,'Ethernet9','Ethernet9',NULL,10000,9214,'444CA8B94286',1,6,1695696920,'2024-08-21 16:53:34',9,1,NULL,'(empty)','shutdown','available',NULL,0,0,'2024-08-21 19:18:08','2024-08-21 21:53:34'),(10,1,0,'Ethernet10','Ethernet10',NULL,10000,9214,'444CA8B94287',1,6,1695696920,'2024-08-21 16:53:34',10,1,NULL,'(empty)','shutdown','available',NULL,0,0,'2024-08-21 19:18:08','2024-08-21 21:53:34'),(11,1,0,'Ethernet11','Ethernet11',NULL,10000,9214,'444CA8B94288',1,6,1695696920,'2024-08-21 16:53:34',11,1,NULL,'(empty)','shutdown','available',NULL,0,0,'2024-08-21 19:18:08','2024-08-21 21:53:34'),(12,1,0,'Ethernet12','Ethernet12',NULL,10000,9214,'444CA8B94289',1,6,1695696920,'2024-08-21 16:53:34',12,1,NULL,'(empty)','shutdown','available',NULL,0,0,'2024-08-21 19:18:08','2024-08-21 21:53:34'),(13,1,0,'Ethernet13','Ethernet13',NULL,1000,9214,'444CA8B9428A',1,1,1719026014,'2024-08-21 16:53:34',13,1,NULL,'1000BaseTFD','operational','available',NULL,1,1,'2024-08-21 19:18:08','2024-08-21 21:53:34'),(14,1,0,'Ethernet14','Ethernet14',NULL,1000,9214,'444CA8B9428B',1,1,1696045507,'2024-08-21 16:53:34',14,1,NULL,'1000BaseTFD','operational','available',NULL,1,1,'2024-08-21 19:18:08','2024-08-21 21:53:34'),(15,1,0,'Ethernet15','Ethernet15',NULL,NULL,9214,'444CA8B9428C',1,2,1705548426,'2024-08-21 16:53:34',15,1,NULL,'1000BaseTFD','shutdown','available',NULL,1,1,'2024-08-21 19:18:08','2024-08-21 21:53:34'),(16,1,0,'Ethernet16','Ethernet16',NULL,NULL,9214,'444CA8B9428D',1,2,1696044777,'2024-08-21 16:53:34',16,1,NULL,'1000BaseTFD','shutdown','available',NULL,1,1,'2024-08-21 19:18:08','2024-08-21 21:53:34'),(17,1,0,'Ethernet17','Ethernet17',NULL,1000,9214,'444CA8B9428E',1,1,1696045697,'2024-08-21 16:53:34',17,1,NULL,'1000BaseTFD','operational','available',NULL,1,1,'2024-08-21 19:18:08','2024-08-21 21:53:34'),(18,1,0,'Ethernet18','Ethernet18',NULL,1000,9214,'444CA8B9428F',1,1,1720889761,'2024-08-21 16:53:34',18,1,NULL,'1000BaseTFD','operational','available',NULL,1,1,'2024-08-21 19:18:08','2024-08-21 21:53:34'),(19,1,0,'Ethernet19','Ethernet19',NULL,1000,9214,'444CA8B94290',1,1,1696046106,'2024-08-21 16:53:34',19,1,NULL,'1000BaseTFD','operational','available',NULL,1,1,'2024-08-21 19:18:08','2024-08-21 21:53:34'),(20,1,0,'Ethernet20','Ethernet20',NULL,1000,9214,'444CA8B94291',1,1,1720877231,'2024-08-21 16:53:34',20,1,NULL,'1000BaseTFD','operational','available',NULL,1,1,'2024-08-21 19:18:08','2024-08-21 21:53:34'),(21,1,0,'Ethernet21','Ethernet21',NULL,1000,9214,'444CA8B94292',1,1,1700300638,'2024-08-21 16:53:34',21,1,NULL,'1000BaseTFD','operational','available',NULL,1,1,'2024-08-21 19:18:08','2024-08-21 21:53:34'),(22,1,0,'Ethernet22','Ethernet22',NULL,1000,9214,'444CA8B94293',1,1,1696046392,'2024-08-21 16:53:34',22,1,NULL,'1000BaseTFD','operational','available',NULL,1,1,'2024-08-21 19:18:08','2024-08-21 21:53:34'),(23,1,0,'Ethernet23','Ethernet23',NULL,NULL,9214,'444CA8B94294',1,2,1696044772,'2024-08-21 16:53:34',23,1,NULL,'1000BaseTFD','shutdown','available',NULL,1,1,'2024-08-21 19:18:08','2024-08-21 21:53:34'),(24,1,0,'Ethernet24','Ethernet24',NULL,NULL,9214,'444CA8B94295',1,2,1696044819,'2024-08-21 16:53:34',24,1,NULL,'1000BaseTFD','shutdown','available',NULL,1,1,'2024-08-21 19:18:08','2024-08-21 21:53:34'),(25,1,0,'Ethernet25','Ethernet25',NULL,10000,9214,'444CA8B94296',1,1,1719686754,'2024-08-21 16:53:34',25,1,NULL,'10GigBaseLR','operational','available',NULL,0,0,'2024-08-21 19:18:08','2024-08-21 21:53:34'),(26,1,0,'Ethernet26','Ethernet26',NULL,10000,9214,'444CA8B94297',1,2,1712946633,'2024-08-21 16:53:34',26,1,NULL,'10GigBaseLR','shutdown','available',NULL,0,0,'2024-08-21 19:18:08','2024-08-21 21:53:34'),(27,1,0,'Ethernet27','Ethernet27',NULL,10000,9214,'444CA8B94298',1,2,1712946608,'2024-08-21 16:53:34',27,1,NULL,'10GigBaseLR','shutdown','available',NULL,0,0,'2024-08-21 19:18:08','2024-08-21 21:53:34'),(28,1,0,'Ethernet28','Ethernet28',NULL,10000,9214,'444CA8B94299',1,2,1712946625,'2024-08-21 16:53:34',28,1,NULL,'10GigBaseLR','shutdown','available',NULL,0,0,'2024-08-21 19:18:08','2024-08-21 21:53:34'),(29,1,0,'Ethernet29','Ethernet29',NULL,10000,9214,'444CA8B9429A',1,2,1712946611,'2024-08-21 16:53:34',29,1,NULL,'10GigBaseLR','shutdown','available',NULL,0,0,'2024-08-21 19:18:08','2024-08-21 21:53:34'),(30,1,0,'Ethernet30','Ethernet30',NULL,10000,9214,'444CA8B9429B',1,2,1712946629,'2024-08-21 16:53:34',30,1,NULL,'10GigBaseLR','shutdown','available',NULL,0,0,'2024-08-21 19:18:08','2024-08-21 21:53:34'),(31,1,0,'Ethernet31','Ethernet31',NULL,10000,9214,'444CA8B9429C',1,2,1723991541,'2024-08-21 16:53:34',31,1,NULL,'10GigBaseLR','shutdown','available',NULL,0,0,'2024-08-21 19:18:08','2024-08-21 21:53:34'),(32,1,0,'Ethernet32','Ethernet32',NULL,1000,9214,'444CA8B9429D',1,1,1715983039,'2024-08-21 16:53:34',32,1,NULL,'1000BaseTFD','operational','available',NULL,1,1,'2024-08-21 19:18:08','2024-08-21 21:53:34'),(33,1,0,'Ethernet33','Ethernet33',NULL,10000,9214,'444CA8B9429E',1,6,1720967479,'2024-08-21 16:53:34',33,1,NULL,'(empty)','shutdown','available',NULL,0,0,'2024-08-21 19:18:08','2024-08-21 21:53:34'),(34,1,0,'Ethernet34','Ethernet34',NULL,10000,9214,'444CA8B9429F',1,6,1720967485,'2024-08-21 16:53:34',34,1,NULL,'(empty)','shutdown','available',NULL,0,0,'2024-08-21 19:18:08','2024-08-21 21:53:34'),(35,1,0,'Ethernet35','Ethernet35',NULL,10000,9214,'444CA8B942A0',1,6,1720967479,'2024-08-21 16:53:34',35,1,NULL,'(empty)','shutdown','available',NULL,0,0,'2024-08-21 19:18:08','2024-08-21 21:53:34'),(36,1,0,'Ethernet36','Ethernet36',NULL,10000,9214,'444CA8B942A1',1,6,1695696920,'2024-08-21 16:53:34',36,1,NULL,'(empty)','shutdown','available',NULL,0,0,'2024-08-21 19:18:08','2024-08-21 21:53:34'),(37,1,0,'Ethernet37','Ethernet37',NULL,10000,9214,'444CA8B942A2',1,6,1695696920,'2024-08-21 16:53:34',37,1,NULL,'(empty)','shutdown','available',NULL,0,0,'2024-08-21 19:18:08','2024-08-21 21:53:34'),(38,1,0,'Ethernet38','Ethernet38',NULL,10000,9214,'444CA8B942A3',1,6,1695696920,'2024-08-21 16:53:34',38,1,NULL,'(empty)','shutdown','available',NULL,0,0,'2024-08-21 19:18:08','2024-08-21 21:53:34'),(39,1,0,'Ethernet39','Ethernet39',NULL,10000,9214,'444CA8B942A4',1,6,1695696920,'2024-08-21 16:53:34',39,1,NULL,'(empty)','shutdown','available',NULL,0,0,'2024-08-21 19:18:08','2024-08-21 21:53:34'),(40,1,0,'Ethernet40','Ethernet40',NULL,10000,9214,'444CA8B942A5',1,6,1695696920,'2024-08-21 16:53:34',40,1,NULL,'(empty)','shutdown','available',NULL,0,0,'2024-08-21 19:18:08','2024-08-21 21:53:34'),(41,1,0,'Ethernet41','Ethernet41',NULL,NULL,9214,'444CA8B942A6',2,2,1720968271,'2024-08-21 16:53:34',41,1,NULL,'1000BaseTFD','shutdown','available',NULL,1,1,'2024-08-21 19:18:08','2024-08-21 21:53:34'),(42,1,0,'Ethernet42','Ethernet42',NULL,NULL,9214,'444CA8B942A7',2,2,1720968278,'2024-08-21 16:53:34',42,1,NULL,'1000BaseTFD','shutdown','available',NULL,1,1,'2024-08-21 19:18:08','2024-08-21 21:53:34'),(43,1,0,'Ethernet43','Ethernet43',NULL,NULL,9214,'444CA8B942A8',2,2,1720968284,'2024-08-21 16:53:34',43,1,NULL,'1000BaseTFD','shutdown','available',NULL,1,1,'2024-08-21 19:18:08','2024-08-21 21:53:34'),(44,1,0,'Ethernet44','Ethernet44',NULL,NULL,9214,'444CA8B942A9',2,2,1720968301,'2024-08-21 16:53:34',44,1,NULL,'1000BaseTFD','shutdown','available',NULL,1,1,'2024-08-21 19:18:08','2024-08-21 21:53:34'),(45,1,0,'Ethernet45','Ethernet45',NULL,1000,9214,'444CA8B942AA',1,1,1720969331,'2024-08-21 16:53:34',45,1,NULL,'1000BaseTFD','operational','available',NULL,1,1,'2024-08-21 19:18:08','2024-08-21 21:53:34'),(46,1,0,'Ethernet46','Ethernet46',NULL,1000,9214,'444CA8B942AB',1,1,1720969332,'2024-08-21 16:53:34',46,1,NULL,'1000BaseTFD','operational','available',NULL,1,1,'2024-08-21 19:18:08','2024-08-21 21:53:34'),(47,1,1,'Ethernet47','Ethernet47','Internal: rs1',1000,9214,'444CA8B942AC',1,1,1696048541,'2024-08-21 16:53:34',47,1,NULL,'10GigBaseLR','operational','available',NULL,0,0,'2024-08-21 19:18:08','2024-08-21 21:53:34'),(48,1,1,'Ethernet48','Ethernet48','Internal: rc1',1000,9214,'444CA8B942AD',1,1,1696048596,'2024-08-21 16:53:34',48,1,NULL,'10GigBaseLR','operational','available',NULL,0,0,'2024-08-21 19:18:08','2024-08-21 21:53:34'),(49,1,1,'Ethernet49/1','Ethernet49/1','Cust: CDN',100000,9214,'444CA8B942AE',1,1,1719687435,'2024-08-21 16:53:34',49001,1,NULL,'100GbaseLR4','operational','available',NULL,0,0,'2024-08-21 19:18:08','2024-08-21 21:53:34'),(50,1,0,'Ethernet50/1','Ethernet50/1',NULL,100000,9214,'444CA8B942B2',1,1,1721959149,'2024-08-21 16:53:34',50001,1,NULL,'100GbaseLR4','operational','available',NULL,0,0,'2024-08-21 19:18:08','2024-08-21 21:53:34'),(51,1,0,'Ethernet51/1','Ethernet51/1',NULL,100000,9214,'444CA8B9427D',1,1,1715396166,'2024-08-21 16:53:34',51001,1,NULL,'100GbaseAR4','operational','available',NULL,0,0,'2024-08-21 19:18:08','2024-08-21 21:53:34'),(52,1,0,'Ethernet52/1','Ethernet52/1',NULL,100000,9214,'444CA8B942BA',1,1,1719613423,'2024-08-21 16:53:34',52001,1,NULL,'100GbaseLR4','operational','available',NULL,0,0,'2024-08-21 19:18:08','2024-08-21 21:53:34'),(53,1,3,'Ethernet53/1','Ethernet53/1','Core: VIX1 - FAC1 - FAC2',100000,9214,'444CA8B9427D',1,1,1715395117,'2024-08-21 16:53:34',53001,1,NULL,'100GbaseAR4','operational','available',NULL,0,0,'2024-08-21 19:18:08','2024-08-21 21:53:34'),(54,1,3,'Ethernet54/1','Ethernet54/1','Core: VIX1 - FAC1 - FAC2',100000,9214,'444CA8B942C2',1,1,1703850298,'2024-08-21 16:53:34',54001,1,NULL,'(empty)','shutdown','available',NULL,0,0,'2024-08-21 19:18:08','2024-08-21 21:53:34'),(55,1,0,'Management1','Management1',NULL,1000,1500,'444CA8B9427C',1,1,1695696818,'2024-08-21 16:53:34',999001,1,NULL,'(empty)',NULL,NULL,NULL,0,0,'2024-08-21 19:18:08','2024-08-21 21:53:34'),(56,2,0,'Ethernet1','Ethernet1',NULL,10000,9214,'444CA8B9427E',1,6,1695696920,'2024-08-21 16:53:34',1,1,NULL,'(empty)','shutdown','available',NULL,0,0,'2024-08-21 19:19:18','2024-08-21 21:53:34'),(57,2,0,'Ethernet2','Ethernet2',NULL,1000,9214,'444CA8B9427F',1,1,1696047261,'2024-08-21 16:53:34',2,1,NULL,'10GigBaseLR','operational','available',NULL,0,0,'2024-08-21 19:19:18','2024-08-21 21:53:34'),(58,2,0,'Ethernet3','Ethernet3',NULL,10000,9214,'444CA8B94280',1,6,1695696920,'2024-08-21 16:53:35',3,1,NULL,'(empty)','shutdown','available',NULL,0,0,'2024-08-21 19:19:18','2024-08-21 21:53:35'),(59,2,0,'Ethernet4','Ethernet4',NULL,10000,9214,'444CA8B94281',1,6,1695696921,'2024-08-21 16:53:35',4,1,NULL,'(empty)','shutdown','available',NULL,0,0,'2024-08-21 19:19:18','2024-08-21 21:53:35'),(60,2,0,'Ethernet5','Ethernet5',NULL,10000,9214,'444CA8B94282',1,6,1695696921,'2024-08-21 16:53:35',5,1,NULL,'(empty)','shutdown','available',NULL,0,0,'2024-08-21 19:19:18','2024-08-21 21:53:35'),(61,2,0,'Ethernet6','Ethernet6','Cust: XXX',10000,9214,'444CA8B94283',1,1,1710084966,'2024-08-21 16:53:35',6,1,NULL,'10GigBaseLR','operational','available',NULL,0,0,'2024-08-21 19:19:18','2024-08-21 21:53:35'),(62,2,0,'Ethernet7','Ethernet7','Cust: XXX',10000,9214,'444CA8B94284',1,1,1696047921,'2024-08-21 16:53:35',7,1,NULL,'10GigBaseLR','operational','available',NULL,0,0,'2024-08-21 19:19:18','2024-08-21 21:53:35'),(63,2,1,'Ethernet8','Ethernet8','Cust: Eyeball ISP',1000,9214,'444CA8B94285',1,1,1706930100,'2024-08-21 16:53:35',8,1,NULL,'10GigBaseLR','operational','available',NULL,0,0,'2024-08-21 19:19:18','2024-08-21 21:53:35'),(64,2,0,'Ethernet9','Ethernet9',NULL,10000,9214,'444CA8B94286',1,6,1695696921,'2024-08-21 16:53:35',9,1,NULL,'(empty)','shutdown','available',NULL,0,0,'2024-08-21 19:19:18','2024-08-21 21:53:35'),(65,2,0,'Ethernet10','Ethernet10',NULL,10000,9214,'444CA8B94287',1,6,1695696921,'2024-08-21 16:53:35',10,1,NULL,'(empty)','shutdown','available',NULL,0,0,'2024-08-21 19:19:18','2024-08-21 21:53:35'),(66,2,0,'Ethernet11','Ethernet11',NULL,10000,9214,'444CA8B94288',1,6,1695696921,'2024-08-21 16:53:35',11,1,NULL,'(empty)','shutdown','available',NULL,0,0,'2024-08-21 19:19:18','2024-08-21 21:53:35'),(67,2,0,'Ethernet12','Ethernet12',NULL,10000,9214,'444CA8B94289',1,6,1695696921,'2024-08-21 16:53:35',12,1,NULL,'(empty)','shutdown','available',NULL,0,0,'2024-08-21 19:19:18','2024-08-21 21:53:35'),(68,2,1,'Ethernet13','Ethernet13','Cust: Regional WISP',1000,9214,'444CA8B9428A',1,1,1719026015,'2024-08-21 16:53:35',13,1,NULL,'1000BaseTFD','operational','available',NULL,1,1,'2024-08-21 19:19:18','2024-08-21 21:53:35'),(69,2,0,'Ethernet14','Ethernet14',NULL,1000,9214,'444CA8B9428B',1,1,1696045508,'2024-08-21 16:53:35',14,1,NULL,'1000BaseTFD','operational','available',NULL,1,1,'2024-08-21 19:19:18','2024-08-21 21:53:35'),(70,2,0,'Ethernet15','Ethernet15',NULL,NULL,9214,'444CA8B9428C',1,2,1705548427,'2024-08-21 16:53:35',15,1,NULL,'1000BaseTFD','shutdown','available',NULL,1,1,'2024-08-21 19:19:18','2024-08-21 21:53:35'),(71,2,0,'Ethernet16','Ethernet16',NULL,NULL,9214,'444CA8B9428D',1,2,1696044778,'2024-08-21 16:53:35',16,1,NULL,'1000BaseTFD','shutdown','available',NULL,1,1,'2024-08-21 19:19:18','2024-08-21 21:53:35'),(72,2,0,'Ethernet17','Ethernet17',NULL,1000,9214,'444CA8B9428E',1,1,1696045698,'2024-08-21 16:53:35',17,1,NULL,'1000BaseTFD','operational','available',NULL,1,1,'2024-08-21 19:19:18','2024-08-21 21:53:35'),(73,2,0,'Ethernet18','Ethernet18',NULL,1000,9214,'444CA8B9428F',1,1,1720889762,'2024-08-21 16:53:35',18,1,NULL,'1000BaseTFD','operational','available',NULL,1,1,'2024-08-21 19:19:18','2024-08-21 21:53:35'),(74,2,0,'Ethernet19','Ethernet19',NULL,1000,9214,'444CA8B94290',1,1,1696046107,'2024-08-21 16:53:35',19,1,NULL,'1000BaseTFD','operational','available',NULL,1,1,'2024-08-21 19:19:18','2024-08-21 21:53:35'),(75,2,0,'Ethernet20','Ethernet20',NULL,1000,9214,'444CA8B94291',1,1,1720877232,'2024-08-21 16:53:35',20,1,NULL,'1000BaseTFD','operational','available',NULL,1,1,'2024-08-21 19:19:18','2024-08-21 21:53:35'),(76,2,0,'Ethernet21','Ethernet21',NULL,1000,9214,'444CA8B94292',1,1,1700300639,'2024-08-21 16:53:35',21,1,NULL,'1000BaseTFD','operational','available',NULL,1,1,'2024-08-21 19:19:18','2024-08-21 21:53:35'),(77,2,0,'Ethernet22','Ethernet22',NULL,1000,9214,'444CA8B94293',1,1,1696046393,'2024-08-21 16:53:35',22,1,NULL,'1000BaseTFD','operational','available',NULL,1,1,'2024-08-21 19:19:18','2024-08-21 21:53:35'),(78,2,0,'Ethernet23','Ethernet23',NULL,NULL,9214,'444CA8B94294',1,2,1696044773,'2024-08-21 16:53:35',23,1,NULL,'1000BaseTFD','shutdown','available',NULL,1,1,'2024-08-21 19:19:18','2024-08-21 21:53:35'),(79,2,0,'Ethernet24','Ethernet24',NULL,NULL,9214,'444CA8B94295',1,2,1696044820,'2024-08-21 16:53:35',24,1,NULL,'1000BaseTFD','shutdown','available',NULL,1,1,'2024-08-21 19:19:18','2024-08-21 21:53:35'),(80,2,0,'Ethernet25','Ethernet25',NULL,10000,9214,'444CA8B94296',1,1,1719686755,'2024-08-21 16:53:35',25,1,NULL,'10GigBaseLR','operational','available',NULL,0,0,'2024-08-21 19:19:18','2024-08-21 21:53:35'),(81,2,0,'Ethernet26','Ethernet26',NULL,10000,9214,'444CA8B94297',1,2,1712946634,'2024-08-21 16:53:35',26,1,NULL,'10GigBaseLR','shutdown','available',NULL,0,0,'2024-08-21 19:19:18','2024-08-21 21:53:35'),(82,2,0,'Ethernet27','Ethernet27',NULL,10000,9214,'444CA8B94298',1,2,1712946609,'2024-08-21 16:53:35',27,1,NULL,'10GigBaseLR','shutdown','available',NULL,0,0,'2024-08-21 19:19:18','2024-08-21 21:53:35'),(83,2,0,'Ethernet28','Ethernet28',NULL,10000,9214,'444CA8B94299',1,2,1712946626,'2024-08-21 16:53:35',28,1,NULL,'10GigBaseLR','shutdown','available',NULL,0,0,'2024-08-21 19:19:18','2024-08-21 21:53:35'),(84,2,0,'Ethernet29','Ethernet29',NULL,10000,9214,'444CA8B9429A',1,2,1712946612,'2024-08-21 16:53:35',29,1,NULL,'10GigBaseLR','shutdown','available',NULL,0,0,'2024-08-21 19:19:18','2024-08-21 21:53:35'),(85,2,0,'Ethernet30','Ethernet30',NULL,10000,9214,'444CA8B9429B',1,2,1712946630,'2024-08-21 16:53:35',30,1,NULL,'10GigBaseLR','shutdown','available',NULL,0,0,'2024-08-21 19:19:18','2024-08-21 21:53:35'),(86,2,0,'Ethernet31','Ethernet31',NULL,10000,9214,'444CA8B9429C',1,2,1723991542,'2024-08-21 16:53:35',31,1,NULL,'10GigBaseLR','shutdown','available',NULL,0,0,'2024-08-21 19:19:18','2024-08-21 21:53:35'),(87,2,0,'Ethernet32','Ethernet32',NULL,1000,9214,'444CA8B9429D',1,1,1715983040,'2024-08-21 16:53:35',32,1,NULL,'1000BaseTFD','operational','available',NULL,1,1,'2024-08-21 19:19:18','2024-08-21 21:53:35'),(88,2,0,'Ethernet33','Ethernet33',NULL,10000,9214,'444CA8B9429E',1,6,1720967480,'2024-08-21 16:53:35',33,1,NULL,'(empty)','shutdown','available',NULL,0,0,'2024-08-21 19:19:18','2024-08-21 21:53:35'),(89,2,0,'Ethernet34','Ethernet34',NULL,10000,9214,'444CA8B9429F',1,6,1720967486,'2024-08-21 16:53:35',34,1,NULL,'(empty)','shutdown','available',NULL,0,0,'2024-08-21 19:19:18','2024-08-21 21:53:35'),(90,2,0,'Ethernet35','Ethernet35',NULL,10000,9214,'444CA8B942A0',1,6,1720967480,'2024-08-21 16:53:35',35,1,NULL,'(empty)','shutdown','available',NULL,0,0,'2024-08-21 19:19:18','2024-08-21 21:53:35'),(91,2,0,'Ethernet36','Ethernet36',NULL,10000,9214,'444CA8B942A1',1,6,1695696921,'2024-08-21 16:53:35',36,1,NULL,'(empty)','shutdown','available',NULL,0,0,'2024-08-21 19:19:18','2024-08-21 21:53:35'),(92,2,0,'Ethernet37','Ethernet37',NULL,10000,9214,'444CA8B942A2',1,6,1695696921,'2024-08-21 16:53:35',37,1,NULL,'(empty)','shutdown','available',NULL,0,0,'2024-08-21 19:19:18','2024-08-21 21:53:35'),(93,2,0,'Ethernet38','Ethernet38',NULL,10000,9214,'444CA8B942A3',1,6,1695696921,'2024-08-21 16:53:35',38,1,NULL,'(empty)','shutdown','available',NULL,0,0,'2024-08-21 19:19:18','2024-08-21 21:53:35'),(94,2,0,'Ethernet39','Ethernet39',NULL,10000,9214,'444CA8B942A4',1,6,1695696921,'2024-08-21 16:53:35',39,1,NULL,'(empty)','shutdown','available',NULL,0,0,'2024-08-21 19:19:18','2024-08-21 21:53:35'),(95,2,0,'Ethernet40','Ethernet40',NULL,10000,9214,'444CA8B942A5',1,6,1695696921,'2024-08-21 16:53:35',40,1,NULL,'(empty)','shutdown','available',NULL,0,0,'2024-08-21 19:19:18','2024-08-21 21:53:35'),(96,2,0,'Ethernet41','Ethernet41',NULL,NULL,9214,'444CA8B942A6',2,2,1720968272,'2024-08-21 16:53:35',41,1,NULL,'1000BaseTFD','shutdown','available',NULL,1,1,'2024-08-21 19:19:18','2024-08-21 21:53:35'),(97,2,0,'Ethernet42','Ethernet42',NULL,NULL,9214,'444CA8B942A7',2,2,1720968279,'2024-08-21 16:53:35',42,1,NULL,'1000BaseTFD','shutdown','available',NULL,1,1,'2024-08-21 19:19:18','2024-08-21 21:53:35'),(98,2,0,'Ethernet43','Ethernet43',NULL,NULL,9214,'444CA8B942A8',2,2,1720968285,'2024-08-21 16:53:35',43,1,NULL,'1000BaseTFD','shutdown','available',NULL,1,1,'2024-08-21 19:19:18','2024-08-21 21:53:35'),(99,2,0,'Ethernet44','Ethernet44',NULL,NULL,9214,'444CA8B942A9',2,2,1720968302,'2024-08-21 16:53:35',44,1,NULL,'1000BaseTFD','shutdown','available',NULL,1,1,'2024-08-21 19:19:18','2024-08-21 21:53:35'),(100,2,0,'Ethernet45','Ethernet45',NULL,1000,9214,'444CA8B942AA',1,1,1720969332,'2024-08-21 16:53:35',45,1,NULL,'1000BaseTFD','operational','available',NULL,1,1,'2024-08-21 19:19:18','2024-08-21 21:53:35'),(101,2,0,'Ethernet46','Ethernet46',NULL,1000,9214,'444CA8B942AB',1,1,1720969333,'2024-08-21 16:53:35',46,1,NULL,'1000BaseTFD','operational','available',NULL,1,1,'2024-08-21 19:19:18','2024-08-21 21:53:35'),(102,2,1,'Ethernet47','Ethernet47','Internal: rs2',1000,9214,'444CA8B942AC',1,1,1696048542,'2024-08-21 16:53:35',47,1,NULL,'10GigBaseLR','operational','available',NULL,0,0,'2024-08-21 19:19:18','2024-08-21 21:53:35'),(103,2,0,'Ethernet48','Ethernet48',NULL,1000,9214,'444CA8B942AD',1,1,1696048597,'2024-08-21 16:53:35',48,1,NULL,'10GigBaseLR','operational','available',NULL,0,0,'2024-08-21 19:19:18','2024-08-21 21:53:35'),(104,2,0,'Ethernet49/1','Ethernet49/1',NULL,100000,9214,'444CA8B942AE',1,1,1719687436,'2024-08-21 16:53:35',49001,1,NULL,'100GbaseLR4','operational','available',NULL,0,0,'2024-08-21 19:19:18','2024-08-21 21:53:35'),(105,2,0,'Ethernet50/1','Ethernet50/1',NULL,100000,9214,'444CA8B942B2',1,1,1721959150,'2024-08-21 16:53:35',50001,1,NULL,'100GbaseLR4','operational','available',NULL,0,0,'2024-08-21 19:19:18','2024-08-21 21:53:35'),(106,2,0,'Ethernet51/1','Ethernet51/1',NULL,100000,9214,'444CA8B9427D',1,1,1715396167,'2024-08-21 16:53:35',51001,1,NULL,'100GbaseAR4','operational','available',NULL,0,0,'2024-08-21 19:19:18','2024-08-21 21:53:35'),(107,2,0,'Ethernet52/1','Ethernet52/1',NULL,100000,9214,'444CA8B942BA',1,1,1719613424,'2024-08-21 16:53:35',52001,1,NULL,'100GbaseLR4','operational','available',NULL,0,0,'2024-08-21 19:19:18','2024-08-21 21:53:35'),(108,2,3,'Ethernet53/1','Ethernet53/1','Core: VIX1 - FAC1 - FAC2',100000,9214,'444CA8B9427D',1,1,1715395118,'2024-08-21 16:53:35',53001,1,NULL,'100GbaseAR4','operational','available',NULL,0,0,'2024-08-21 19:19:18','2024-08-21 21:53:35'),(109,2,3,'Ethernet54/1','Ethernet54/1','Core: VIX1 - FAC1 - FAC2',100000,9214,'444CA8B942C2',1,1,1703850299,'2024-08-21 16:53:35',54001,1,NULL,'(empty)','shutdown','available',NULL,0,0,'2024-08-21 19:19:18','2024-08-21 21:53:35'),(110,2,0,'Management1','Management1',NULL,1000,1500,'444CA8B9427C',1,1,1695696819,'2024-08-21 16:53:35',999001,1,NULL,'(empty)',NULL,NULL,NULL,0,0,'2024-08-21 19:19:18','2024-08-21 21:53:35'),(111,3,0,'Ethernet1','Ethernet1',NULL,10000,9214,'444CA8B9427E',1,6,1695696921,'2024-08-21 16:53:35',1,1,NULL,'(empty)','shutdown','available',NULL,0,0,'2024-08-21 19:19:18','2024-08-21 21:53:35'),(112,3,1,'Ethernet2','Ethernet2','Cust: AS112',1000,9214,'444CA8B9427F',1,1,1696047262,'2024-08-21 16:53:35',2,1,NULL,'10GigBaseLR','operational','available',NULL,0,0,'2024-08-21 19:19:18','2024-08-21 21:53:35'),(113,3,0,'Ethernet3','Ethernet3',NULL,10000,9214,'444CA8B94280',1,6,1695696921,'2024-08-21 16:53:35',3,1,NULL,'(empty)','shutdown','available',NULL,0,0,'2024-08-21 19:19:18','2024-08-21 21:53:35'),(114,3,0,'Ethernet4','Ethernet4',NULL,10000,9214,'444CA8B94281',1,6,1695696921,'2024-08-21 16:53:35',4,1,NULL,'(empty)','shutdown','available',NULL,0,0,'2024-08-21 19:19:18','2024-08-21 21:53:35'),(115,3,0,'Ethernet5','Ethernet5',NULL,10000,9214,'444CA8B94282',1,6,1695696921,'2024-08-21 16:53:35',5,1,NULL,'(empty)','shutdown','available',NULL,0,0,'2024-08-21 19:19:18','2024-08-21 21:53:35'),(116,3,1,'Ethernet6','Ethernet6','Cust: NREN',10000,9214,'444CA8B94283',1,1,1710084966,'2024-08-21 16:53:35',6,1,NULL,'10GigBaseLR','operational','available',NULL,0,0,'2024-08-21 19:19:18','2024-08-21 21:53:35'),(117,3,1,'Ethernet7','Ethernet7','Cust: NREN',10000,9214,'444CA8B94284',1,1,1696047921,'2024-08-21 16:53:35',7,1,NULL,'10GigBaseLR','operational','available',NULL,0,0,'2024-08-21 19:19:18','2024-08-21 21:53:35'),(118,3,1,'Ethernet8','Ethernet8','Cust: Eyeball ISP',1000,9214,'444CA8B94285',1,1,1706930100,'2024-08-21 16:53:35',8,1,NULL,'10GigBaseLR','operational','available',NULL,0,0,'2024-08-21 19:19:18','2024-08-21 21:53:35'),(119,3,0,'Ethernet9','Ethernet9',NULL,10000,9214,'444CA8B94286',1,6,1695696921,'2024-08-21 16:53:35',9,1,NULL,'(empty)','shutdown','available',NULL,0,0,'2024-08-21 19:19:18','2024-08-21 21:53:35'),(120,3,0,'Ethernet10','Ethernet10',NULL,10000,9214,'444CA8B94287',1,6,1695696921,'2024-08-21 16:53:35',10,1,NULL,'(empty)','shutdown','available',NULL,0,0,'2024-08-21 19:19:18','2024-08-21 21:53:35'),(121,3,0,'Ethernet11','Ethernet11',NULL,10000,9214,'444CA8B94288',1,6,1695696921,'2024-08-21 16:53:35',11,1,NULL,'(empty)','shutdown','available',NULL,0,0,'2024-08-21 19:19:18','2024-08-21 21:53:35'),(122,3,0,'Ethernet12','Ethernet12',NULL,10000,9214,'444CA8B94289',1,6,1695696921,'2024-08-21 16:53:35',12,1,NULL,'(empty)','shutdown','available',NULL,0,0,'2024-08-21 19:19:18','2024-08-21 21:53:35'),(123,3,0,'Ethernet13','Ethernet13',NULL,1000,9214,'444CA8B9428A',1,1,1719026015,'2024-08-21 16:53:35',13,1,NULL,'1000BaseTFD','operational','available',NULL,1,1,'2024-08-21 19:19:18','2024-08-21 21:53:35'),(124,3,0,'Ethernet14','Ethernet14',NULL,1000,9214,'444CA8B9428B',1,1,1696045508,'2024-08-21 16:53:35',14,1,NULL,'1000BaseTFD','operational','available',NULL,1,1,'2024-08-21 19:19:18','2024-08-21 21:53:35'),(125,3,0,'Ethernet15','Ethernet15',NULL,NULL,9214,'444CA8B9428C',1,2,1705548427,'2024-08-21 16:53:35',15,1,NULL,'1000BaseTFD','shutdown','available',NULL,1,1,'2024-08-21 19:19:18','2024-08-21 21:53:35'),(126,3,0,'Ethernet16','Ethernet16',NULL,NULL,9214,'444CA8B9428D',1,2,1696044778,'2024-08-21 16:53:35',16,1,NULL,'1000BaseTFD','shutdown','available',NULL,1,1,'2024-08-21 19:19:18','2024-08-21 21:53:35'),(127,3,0,'Ethernet17','Ethernet17',NULL,1000,9214,'444CA8B9428E',1,1,1696045698,'2024-08-21 16:53:35',17,1,NULL,'1000BaseTFD','operational','available',NULL,1,1,'2024-08-21 19:19:18','2024-08-21 21:53:35'),(128,3,0,'Ethernet18','Ethernet18',NULL,1000,9214,'444CA8B9428F',1,1,1720889762,'2024-08-21 16:53:35',18,1,NULL,'1000BaseTFD','operational','available',NULL,1,1,'2024-08-21 19:19:18','2024-08-21 21:53:35'),(129,3,0,'Ethernet19','Ethernet19',NULL,1000,9214,'444CA8B94290',1,1,1696046107,'2024-08-21 16:53:35',19,1,NULL,'1000BaseTFD','operational','available',NULL,1,1,'2024-08-21 19:19:18','2024-08-21 21:53:35'),(130,3,0,'Ethernet20','Ethernet20',NULL,1000,9214,'444CA8B94291',1,1,1720877232,'2024-08-21 16:53:35',20,1,NULL,'1000BaseTFD','operational','available',NULL,1,1,'2024-08-21 19:19:18','2024-08-21 21:53:35'),(131,3,0,'Ethernet21','Ethernet21',NULL,1000,9214,'444CA8B94292',1,1,1700300639,'2024-08-21 16:53:35',21,1,NULL,'1000BaseTFD','operational','available',NULL,1,1,'2024-08-21 19:19:18','2024-08-21 21:53:35'),(132,3,0,'Ethernet22','Ethernet22',NULL,1000,9214,'444CA8B94293',1,1,1696046393,'2024-08-21 16:53:35',22,1,NULL,'1000BaseTFD','operational','available',NULL,1,1,'2024-08-21 19:19:18','2024-08-21 21:53:35'),(133,3,0,'Ethernet23','Ethernet23',NULL,NULL,9214,'444CA8B94294',1,2,1696044773,'2024-08-21 16:53:35',23,1,NULL,'1000BaseTFD','shutdown','available',NULL,1,1,'2024-08-21 19:19:18','2024-08-21 21:53:35'),(134,3,0,'Ethernet24','Ethernet24',NULL,NULL,9214,'444CA8B94295',1,2,1696044820,'2024-08-21 16:53:35',24,1,NULL,'1000BaseTFD','shutdown','available',NULL,1,1,'2024-08-21 19:19:18','2024-08-21 21:53:35'),(135,3,0,'Ethernet25','Ethernet25',NULL,10000,9214,'444CA8B94296',1,1,1719686755,'2024-08-21 16:53:35',25,1,NULL,'10GigBaseLR','operational','available',NULL,0,0,'2024-08-21 19:19:18','2024-08-21 21:53:35'),(136,3,0,'Ethernet26','Ethernet26',NULL,10000,9214,'444CA8B94297',1,2,1712946634,'2024-08-21 16:53:35',26,1,NULL,'10GigBaseLR','shutdown','available',NULL,0,0,'2024-08-21 19:19:18','2024-08-21 21:53:35'),(137,3,0,'Ethernet27','Ethernet27',NULL,10000,9214,'444CA8B94298',1,2,1712946609,'2024-08-21 16:53:35',27,1,NULL,'10GigBaseLR','shutdown','available',NULL,0,0,'2024-08-21 19:19:18','2024-08-21 21:53:35'),(138,3,0,'Ethernet28','Ethernet28',NULL,10000,9214,'444CA8B94299',1,2,1712946626,'2024-08-21 16:53:35',28,1,NULL,'10GigBaseLR','shutdown','available',NULL,0,0,'2024-08-21 19:19:18','2024-08-21 21:53:35'),(139,3,0,'Ethernet29','Ethernet29',NULL,10000,9214,'444CA8B9429A',1,2,1712946612,'2024-08-21 16:53:35',29,1,NULL,'10GigBaseLR','shutdown','available',NULL,0,0,'2024-08-21 19:19:18','2024-08-21 21:53:35'),(140,3,0,'Ethernet30','Ethernet30',NULL,10000,9214,'444CA8B9429B',1,2,1712946630,'2024-08-21 16:53:35',30,1,NULL,'10GigBaseLR','shutdown','available',NULL,0,0,'2024-08-21 19:19:18','2024-08-21 21:53:35'),(141,3,0,'Ethernet31','Ethernet31',NULL,10000,9214,'444CA8B9429C',1,2,1723991542,'2024-08-21 16:53:35',31,1,NULL,'10GigBaseLR','shutdown','available',NULL,0,0,'2024-08-21 19:19:18','2024-08-21 21:53:35'),(142,3,0,'Ethernet32','Ethernet32',NULL,1000,9214,'444CA8B9429D',1,1,1715983040,'2024-08-21 16:53:35',32,1,NULL,'1000BaseTFD','operational','available',NULL,1,1,'2024-08-21 19:19:18','2024-08-21 21:53:35'),(143,3,0,'Ethernet33','Ethernet33',NULL,10000,9214,'444CA8B9429E',1,6,1720967480,'2024-08-21 16:53:35',33,1,NULL,'(empty)','shutdown','available',NULL,0,0,'2024-08-21 19:19:18','2024-08-21 21:53:35'),(144,3,0,'Ethernet34','Ethernet34',NULL,10000,9214,'444CA8B9429F',1,6,1720967486,'2024-08-21 16:53:35',34,1,NULL,'(empty)','shutdown','available',NULL,0,0,'2024-08-21 19:19:18','2024-08-21 21:53:35'),(145,3,0,'Ethernet35','Ethernet35',NULL,10000,9214,'444CA8B942A0',1,6,1720967480,'2024-08-21 16:53:35',35,1,NULL,'(empty)','shutdown','available',NULL,0,0,'2024-08-21 19:19:18','2024-08-21 21:53:35'),(146,3,0,'Ethernet36','Ethernet36',NULL,10000,9214,'444CA8B942A1',1,6,1695696921,'2024-08-21 16:53:35',36,1,NULL,'(empty)','shutdown','available',NULL,0,0,'2024-08-21 19:19:18','2024-08-21 21:53:35'),(147,3,0,'Ethernet37','Ethernet37',NULL,10000,9214,'444CA8B942A2',1,6,1695696921,'2024-08-21 16:53:35',37,1,NULL,'(empty)','shutdown','available',NULL,0,0,'2024-08-21 19:19:18','2024-08-21 21:53:35'),(148,3,0,'Ethernet38','Ethernet38',NULL,10000,9214,'444CA8B942A3',1,6,1695696921,'2024-08-21 16:53:35',38,1,NULL,'(empty)','shutdown','available',NULL,0,0,'2024-08-21 19:19:18','2024-08-21 21:53:35'),(149,3,0,'Ethernet39','Ethernet39',NULL,10000,9214,'444CA8B942A4',1,6,1695696921,'2024-08-21 16:53:35',39,1,NULL,'(empty)','shutdown','available',NULL,0,0,'2024-08-21 19:19:18','2024-08-21 21:53:35'),(150,3,0,'Ethernet40','Ethernet40',NULL,10000,9214,'444CA8B942A5',1,6,1695696921,'2024-08-21 16:53:35',40,1,NULL,'(empty)','shutdown','available',NULL,0,0,'2024-08-21 19:19:18','2024-08-21 21:53:35'),(151,3,0,'Ethernet41','Ethernet41',NULL,NULL,9214,'444CA8B942A6',2,2,1720968272,'2024-08-21 16:53:35',41,1,NULL,'1000BaseTFD','shutdown','available',NULL,1,1,'2024-08-21 19:19:18','2024-08-21 21:53:35'),(152,3,0,'Ethernet42','Ethernet42',NULL,NULL,9214,'444CA8B942A7',2,2,1720968279,'2024-08-21 16:53:35',42,1,NULL,'1000BaseTFD','shutdown','available',NULL,1,1,'2024-08-21 19:19:18','2024-08-21 21:53:35'),(153,3,0,'Ethernet43','Ethernet43',NULL,NULL,9214,'444CA8B942A8',2,2,1720968285,'2024-08-21 16:53:35',43,1,NULL,'1000BaseTFD','shutdown','available',NULL,1,1,'2024-08-21 19:19:18','2024-08-21 21:53:35'),(154,3,0,'Ethernet44','Ethernet44',NULL,NULL,9214,'444CA8B942A9',2,2,1720968302,'2024-08-21 16:53:35',44,1,NULL,'1000BaseTFD','shutdown','available',NULL,1,1,'2024-08-21 19:19:18','2024-08-21 21:53:35'),(155,3,0,'Ethernet45','Ethernet45',NULL,1000,9214,'444CA8B942AA',1,1,1720969332,'2024-08-21 16:53:35',45,1,NULL,'1000BaseTFD','operational','available',NULL,1,1,'2024-08-21 19:19:18','2024-08-21 21:53:35'),(156,3,0,'Ethernet46','Ethernet46',NULL,1000,9214,'444CA8B942AB',1,1,1720969333,'2024-08-21 16:53:35',46,1,NULL,'1000BaseTFD','operational','available',NULL,1,1,'2024-08-21 19:19:18','2024-08-21 21:53:35'),(157,3,1,'Ethernet47','Ethernet47','Internal: rs1',1000,9214,'444CA8B942AC',2,2,1696048542,'2024-08-21 16:53:35',47,1,NULL,'10GigBaseLR','operational','available',NULL,0,0,'2024-08-21 19:19:18','2024-08-21 21:53:35'),(158,3,1,'Ethernet48','Ethernet48','Internal: rc1',1000,9214,'444CA8B942AD',2,2,1696048597,'2024-08-21 16:53:35',48,1,NULL,'10GigBaseLR','operational','available',NULL,0,0,'2024-08-21 19:19:18','2024-08-21 21:53:35'),(159,3,1,'Ethernet49/1','Ethernet49/1','Cust: CDN',100000,9214,'444CA8B942AE',1,1,1719687436,'2024-08-21 16:53:35',49001,1,NULL,'100GbaseLR4','operational','available',NULL,0,0,'2024-08-21 19:19:18','2024-08-21 21:53:35'),(160,3,0,'Ethernet50/1','Ethernet50/1',NULL,100000,9214,'444CA8B942B2',1,1,1721959150,'2024-08-21 16:53:35',50001,1,NULL,'100GbaseLR4','operational','available',NULL,0,0,'2024-08-21 19:19:18','2024-08-21 21:53:35'),(161,3,0,'Ethernet51/1','Ethernet51/1',NULL,100000,9214,'444CA8B9427D',1,1,1715396167,'2024-08-21 16:53:35',51001,1,NULL,'100GbaseAR4','operational','available',NULL,0,0,'2024-08-21 19:19:18','2024-08-21 21:53:35'),(162,3,0,'Ethernet52/1','Ethernet52/1',NULL,100000,9214,'444CA8B942BA',1,1,1719613424,'2024-08-21 16:53:35',52001,1,NULL,'100GbaseLR4','operational','available',NULL,0,0,'2024-08-21 19:19:18','2024-08-21 21:53:35'),(163,3,0,'Ethernet53/1','Ethernet53/1',NULL,100000,9214,'444CA8B9427D',1,1,1715395118,'2024-08-21 16:53:35',53001,1,NULL,'100GbaseAR4','operational','available',NULL,0,0,'2024-08-21 19:19:18','2024-08-21 21:53:35'),(164,3,0,'Ethernet54/1','Ethernet54/1',NULL,100000,9214,'444CA8B942C2',1,6,1703850299,'2024-08-21 16:53:35',54001,1,NULL,'(empty)','shutdown','available',NULL,0,0,'2024-08-21 19:19:18','2024-08-21 21:53:35'),(165,3,0,'Management1','Management1',NULL,1000,1500,'444CA8B9427C',1,1,1695696819,'2024-08-21 16:53:35',999001,1,NULL,'(empty)',NULL,NULL,NULL,0,0,'2024-08-21 19:19:18','2024-08-21 21:53:35');
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
  KEY `telescope_entries_family_hash_index` (`family_hash`(191))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `telescope_entries`
--

LOCK TABLES `telescope_entries` WRITE;
/*!40000 ALTER TABLE `telescope_entries` DISABLE KEYS */;
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
  KEY `telescope_entries_tags_entry_uuid_tag_index` (`entry_uuid`,`tag`(191)),
  KEY `telescope_entries_tags_tag_index` (`tag`(191)),
  CONSTRAINT `telescope_entries_tags_entry_uuid_foreign` FOREIGN KEY (`entry_uuid`) REFERENCES `telescope_entries` (`uuid`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `telescope_entries_tags`
--

LOCK TABLES `telescope_entries_tags` WRITE;
/*!40000 ALTER TABLE `telescope_entries_tags` DISABLE KEYS */;
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
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
  `category` varchar(10) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
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
  `username` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `password` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `email` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `authorisedMobile` varchar(30) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `uid` int DEFAULT NULL,
  `privs` int DEFAULT NULL,
  `disabled` tinyint(1) DEFAULT NULL,
  `lastupdatedby` int DEFAULT NULL,
  `creator` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `peeringdb_id` bigint DEFAULT NULL,
  `extra_attributes` json DEFAULT NULL COMMENT '(DC2Type:json)',
  `prefs` json DEFAULT NULL,
  `created_at` timestamp NOT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_8D93D649F85E0677` (`username`),
  UNIQUE KEY `UNIQ_8D93D649F2C6186B` (`peeringdb_id`),
  KEY `IDX_8D93D649DA0209B9` (`custid`),
  CONSTRAINT `FK_8D93D649DA0209B9` FOREIGN KEY (`custid`) REFERENCES `cust` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user`
--

LOCK TABLES `user` WRITE;
/*!40000 ALTER TABLE `user` DISABLE KEYS */;
INSERT INTO `user` VALUES (1,1,'vagrant','$2y$10$8aLxdFa6PSZGwgEQPkPQN.pfnyn8W83LYMncA7FV7vy7Y36EYEKca','vagrant@example.net',NULL,NULL,3,0,1,NULL,'Vagrant Superadmin',NULL,NULL,NULL,'2024-08-21 13:52:49','2024-08-21 20:05:36'),(2,2,'as112','$2y$10$78ssuNqLQbRK9O8TEV771O47ag/LuO1HcgEZsicGEje7prBoS7HnG','as112@example.com',NULL,NULL,2,0,2,'vagrant','AS112 CustAdmin',NULL,'[]','{}','2024-08-21 19:51:20','2024-08-21 20:06:14'),(3,2,'as112user','$2y$10$wznr.oBCz.0UG/WjloHctuAuQGyQ8ry7IQx4gzqTcioRbRpHOFYtC','as112user@example.com',NULL,NULL,1,0,3,'vagrant','AS112 user',NULL,'[]','{}','2024-08-21 20:04:17','2024-08-21 20:06:41');
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
  `created_at` timestamp NOT NULL,
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
  `ip` varchar(39) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `at` datetime NOT NULL,
  `customer_to_user_id` int DEFAULT NULL,
  `via` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_6341CC99D43FEAE2` (`customer_to_user_id`),
  KEY `at_idx` (`at`),
  KEY `user_id_idx` (`user_id`),
  CONSTRAINT `FK_6341CC99D43FEAE2` FOREIGN KEY (`customer_to_user_id`) REFERENCES `customer_to_users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_logins`
--

LOCK TABLES `user_logins` WRITE;
/*!40000 ALTER TABLE `user_logins` DISABLE KEYS */;
INSERT INTO `user_logins` VALUES (1,NULL,'10.211.55.1','2024-08-21 13:54:00',1,'Login','2024-08-21 18:54:00','2024-08-21 18:54:00'),(2,NULL,'10.211.55.1','2024-08-21 14:53:05',2,'Login','2024-08-21 19:53:05','2024-08-21 19:53:05'),(3,NULL,'10.211.55.1','2024-08-21 14:53:58',2,'Login','2024-08-21 19:53:58','2024-08-21 19:53:58'),(4,NULL,'10.211.55.1','2024-08-21 15:03:15',1,'Login','2024-08-21 20:03:15','2024-08-21 20:03:15'),(5,NULL,'10.211.55.1','2024-08-21 15:05:59',2,'Login','2024-08-21 20:05:59','2024-08-21 20:05:59'),(6,NULL,'10.211.55.1','2024-08-21 15:06:34',3,'Login','2024-08-21 20:06:34','2024-08-21 20:06:34'),(7,NULL,'10.211.55.1','2024-08-21 15:06:56',1,'Login','2024-08-21 20:06:56','2024-08-21 20:06:56');
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
  `attribute` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `ix` int NOT NULL DEFAULT '0',
  `op` varchar(2) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `value` longtext CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `expire` bigint NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `IX_UserPreference_1` (`user_id`,`attribute`,`op`,`ix`),
  KEY `IDX_DBD4D4F8A76ED395` (`user_id`),
  CONSTRAINT `FK_DBD4D4F8A76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_pref`
--

LOCK TABLES `user_pref` WRITE;
/*!40000 ALTER TABLE `user_pref` DISABLE KEYS */;
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
  `created_at` timestamp NOT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_token` (`user_id`,`token`),
  KEY `IDX_E253302EA76ED395` (`user_id`),
  CONSTRAINT `FK_E253302EA76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
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
  `name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `shortname` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `nagios_name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `bundle_name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vendor`
--

LOCK TABLES `vendor` WRITE;
/*!40000 ALTER TABLE `vendor` DISABLE KEYS */;
INSERT INTO `vendor` VALUES (1,'Allied Telesyn','AlliedTel','alliedtel',NULL,'2024-08-21 18:53:47','2024-08-21 18:53:47'),(2,'Allied Telesis','AlliedTelesis','alliedtelesis',NULL,'2024-08-21 18:53:47','2024-08-21 18:53:47'),(3,'Arista','Arista','arista','Port-channel','2024-08-21 18:53:47','2024-08-21 18:53:47'),(4,'Brocade','Brocade','brocade',NULL,'2024-08-21 18:53:47','2024-08-21 18:53:47'),(5,'Cisco Systems','Cisco','cisco','Port-channel','2024-08-21 18:53:47','2024-08-21 18:53:47'),(6,'Cumulus Networks','Cumulus','cumulus','bond','2024-08-21 18:53:47','2024-08-21 18:53:47'),(7,'Dell','Dell','dell',NULL,'2024-08-21 18:53:47','2024-08-21 18:53:47'),(8,'Enterasys','Enterasys','enterasys',NULL,'2024-08-21 18:53:47','2024-08-21 18:53:47'),(9,'Extreme Networks','Extreme','extreme',NULL,'2024-08-21 18:53:47','2024-08-21 18:53:47'),(10,'Force10 Networks','Force10','force10',NULL,'2024-08-21 18:53:47','2024-08-21 18:53:47'),(11,'Foundry Networks','Brocade','brocade',NULL,'2024-08-21 18:53:47','2024-08-21 18:53:47'),(12,'Glimmerglass','Glimmerglass','glimmerglass',NULL,'2024-08-21 18:53:47','2024-08-21 18:53:47'),(13,'Hewlett-Packard','HP','hp',NULL,'2024-08-21 18:53:47','2024-08-21 18:53:47'),(14,'Hitachi Cable','Hitachi','hitachi',NULL,'2024-08-21 18:53:47','2024-08-21 18:53:47'),(15,'Juniper Networks','Juniper','juniper',NULL,'2024-08-21 18:53:47','2024-08-21 18:53:47'),(16,'Linux','Linux','linux',NULL,'2024-08-21 18:53:47','2024-08-21 18:53:47'),(17,'MRV','MRV','mrv',NULL,'2024-08-21 18:53:47','2024-08-21 18:53:47'),(18,'Transmode','Transmode','transmode',NULL,'2024-08-21 18:53:47','2024-08-21 18:53:47');
/*!40000 ALTER TABLE `vendor` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `virtualinterface`
--

DROP TABLE IF EXISTS `virtualinterface`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `virtualinterface` (
  `id` int NOT NULL AUTO_INCREMENT,
  `custid` int DEFAULT NULL,
  `name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `description` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
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
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `virtualinterface`
--

LOCK TABLES `virtualinterface` WRITE;
/*!40000 ALTER TABLE `virtualinterface` DISABLE KEYS */;
INSERT INTO `virtualinterface` VALUES (1,2,NULL,NULL,NULL,0,NULL,0,0,'2024-08-21 19:41:21','2024-08-21 19:41:21'),(2,2,NULL,NULL,NULL,0,NULL,0,0,'2024-08-21 19:42:49','2024-08-21 19:42:49'),(3,3,'Port-channel',NULL,NULL,0,1,1,1,'2024-08-21 20:10:03','2024-08-21 20:10:40'),(4,3,'Port-channel',NULL,NULL,0,1,1,1,'2024-08-21 20:17:05','2024-08-21 20:19:08'),(5,4,NULL,NULL,NULL,0,NULL,0,0,'2024-08-21 20:28:11','2024-08-21 20:28:11'),(6,4,NULL,NULL,NULL,0,NULL,0,0,'2024-08-21 20:29:29','2024-08-21 20:29:29'),(7,4,NULL,NULL,NULL,0,NULL,0,0,'2024-08-21 20:30:18','2024-08-21 20:30:18'),(8,5,'Port-channel',NULL,NULL,0,2,1,1,'2024-08-21 20:39:20','2024-08-21 20:39:36'),(9,5,'Port-channel',NULL,NULL,0,2,1,1,'2024-08-21 20:40:24','2024-08-21 20:40:39'),(10,6,NULL,NULL,NULL,0,NULL,0,0,'2024-08-21 20:51:52','2024-08-21 20:51:52'),(11,1,NULL,NULL,NULL,1,NULL,0,0,'2024-08-21 20:56:19','2024-08-21 20:56:19'),(12,1,NULL,NULL,NULL,1,NULL,0,0,'2024-08-21 21:00:47','2024-08-21 21:00:47'),(13,7,NULL,NULL,NULL,0,NULL,0,0,'2024-08-21 21:05:55','2024-08-21 21:05:55'),(14,7,'',NULL,NULL,0,NULL,0,0,'2024-08-21 21:07:00','2024-08-21 21:07:10'),(15,7,NULL,NULL,NULL,0,NULL,0,0,'2024-08-21 21:07:57','2024-08-21 21:07:57'),(16,1,'Port-Channel',NULL,9000,1,1000,1,1,'2024-08-21 21:29:17','2024-08-21 21:29:17'),(17,1,'Port-Channel',NULL,9000,1,1000,1,1,'2024-08-21 21:29:17','2024-08-21 21:29:17');
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
  `name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `number` int DEFAULT NULL,
  `notes` longtext CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `private` tinyint(1) NOT NULL DEFAULT '0',
  `infrastructureid` int NOT NULL,
  `peering_matrix` tinyint(1) NOT NULL DEFAULT '0',
  `peering_manager` tinyint(1) NOT NULL DEFAULT '0',
  `export_to_ixf` tinyint NOT NULL DEFAULT '1',
  `config_name` varchar(32) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `infra_config_name` (`infrastructureid`,`config_name`),
  KEY `IDX_F83104A1721EBF79` (`infrastructureid`),
  CONSTRAINT `FK_F83104A1721EBF79` FOREIGN KEY (`infrastructureid`) REFERENCES `infrastructure` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vlan`
--

LOCK TABLES `vlan` WRITE;
/*!40000 ALTER TABLE `vlan` DISABLE KEYS */;
INSERT INTO `vlan` VALUES (1,'VAGRANT IX1',10,NULL,0,1,1,1,1,'vagrant_ix1','2024-08-21 19:32:25','2024-08-21 19:32:25'),(2,'VAGRANT IX2',20,NULL,0,2,1,1,1,'vagrant_ix2','2024-08-21 19:32:42','2024-08-21 19:32:42'),(3,'QUARANTINE IX1',11,NULL,0,1,0,0,0,'quarantine_ix1','2024-08-21 19:33:09','2024-08-21 19:33:09'),(4,'QUARANTINE IX2',21,NULL,0,2,0,0,0,'quarantine_ix2','2024-08-21 19:33:23','2024-08-21 19:33:23');
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
  `ipv4hostname` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `ipv6enabled` tinyint(1) DEFAULT '0',
  `ipv6hostname` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `mcastenabled` tinyint(1) DEFAULT '0',
  `irrdbfilter` tinyint(1) DEFAULT '1',
  `bgpmd5secret` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `ipv4bgpmd5secret` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `ipv6bgpmd5secret` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `maxbgpprefix` int DEFAULT NULL,
  `rsclient` tinyint(1) DEFAULT NULL,
  `ipv4canping` tinyint(1) DEFAULT NULL,
  `ipv6canping` tinyint(1) DEFAULT NULL,
  `ipv4monitorrcbgp` tinyint(1) DEFAULT NULL,
  `ipv6monitorrcbgp` tinyint(1) DEFAULT NULL,
  `as112client` tinyint(1) DEFAULT NULL,
  `busyhost` tinyint(1) DEFAULT NULL,
  `notes` longtext CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
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
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vlaninterface`
--

LOCK TABLES `vlaninterface` WRITE;
/*!40000 ALTER TABLE `vlaninterface` DISABLE KEYS */;
INSERT INTO `vlaninterface` VALUES (1,7,7,1,1,1,'vagrantix1.as112.example.net',1,'vagrantix1.as112.example.net',0,1,NULL,NULL,NULL,NULL,1,1,1,1,1,0,0,NULL,0,'2024-08-21 19:41:21','2024-08-21 19:41:21'),(2,71,71,2,2,1,'vagrantix2.as112.example.net',1,'vagrantix2.as112.example.net',0,1,NULL,NULL,NULL,NULL,1,1,1,1,1,0,0,NULL,0,'2024-08-21 19:42:49','2024-08-21 19:42:49'),(3,11,11,3,1,1,'vagrantix1.nren.example.net',1,'vagrantix1.nren.example.net',0,1,NULL,'iKHFHvNSzohx','iKHFHvNSzohx',NULL,1,1,1,1,1,1,0,NULL,0,'2024-08-21 20:10:03','2024-08-21 20:10:03'),(4,75,75,4,2,1,'vagrantix2.nren.example.net',1,'vagrantix2.nren.example.net',0,1,NULL,'iKHFHvNSzohx','iKHFHvNSzohx',NULL,1,1,1,1,1,1,0,NULL,0,'2024-08-21 20:17:05','2024-08-21 20:17:05'),(5,12,12,5,1,1,'vagrantix1.eyeballisp.example.net',1,'vagrantix1.eyeballisp.example.net',0,1,NULL,'lR8Z0s2PaGY4','lR8Z0s2PaGY4',NULL,1,1,1,1,1,1,0,NULL,0,'2024-08-21 20:28:11','2024-08-21 20:28:11'),(6,13,13,6,1,1,'vagrantix1.eyeballisp2.example.net',1,'vagrantix1.eyeballisp2.example.net',0,1,NULL,'lR8Z0s2PaGY4','lR8Z0s2PaGY4',NULL,1,1,1,1,1,1,0,NULL,0,'2024-08-21 20:29:29','2024-08-21 20:29:29'),(7,76,76,7,2,1,'vagrantix2.eyeballisp.example.net',1,'vagrantix2.eyeballisp.example.net',0,1,NULL,'lR8Z0s2PaGY4','lR8Z0s2PaGY4',NULL,1,1,1,1,1,1,0,NULL,0,'2024-08-21 20:30:18','2024-08-21 20:30:18'),(8,14,14,8,1,1,'vagrantix1.cdn.example.net',1,'vagrantix1.cdn.example.net',0,1,NULL,'wpwf4xw2FFTa','wpwf4xw2FFTa',NULL,1,1,1,1,1,0,0,NULL,0,'2024-08-21 20:39:20','2024-08-21 20:39:20'),(9,78,78,9,2,1,'vagrantix2.cdn.example.net',1,'vagrantix2.cdn.example.net',0,1,NULL,'wpwf4xw2FFTa','wpwf4xw2FFTa',NULL,1,1,1,1,1,0,0,NULL,0,'2024-08-21 20:40:24','2024-08-21 20:40:24'),(10,15,NULL,10,1,1,'vagrantix1.regionalwisp.example.net',0,NULL,0,1,NULL,'32o5OVIKDmKN',NULL,NULL,1,1,0,1,0,1,0,NULL,0,'2024-08-21 20:51:52','2024-08-21 20:51:52'),(11,129,129,11,1,1,'rc1.vagrantix1.example.net',1,'rc1.vagrantix1.example.net',0,0,NULL,NULL,NULL,NULL,1,1,1,1,1,1,0,NULL,0,'2024-08-21 20:56:19','2024-08-21 20:56:32'),(12,130,130,11,3,1,'rc1.vagrantix1.example.net',1,'rc1.vagrantix1.example.net',0,0,NULL,NULL,NULL,100,1,1,1,1,1,1,0,NULL,0,'2024-08-21 20:57:25','2024-08-21 20:57:25'),(13,131,131,12,2,1,'rc1.vagrantix2.example.net',1,'rc1.vagrantix2.example.net',0,0,NULL,NULL,NULL,NULL,1,1,1,1,1,1,0,NULL,0,'2024-08-21 21:00:47','2024-08-21 21:00:47'),(14,132,132,12,4,1,'rc1.vagrantix2.example.net',1,'rc1.vagrantix2.example.net',0,0,NULL,NULL,NULL,100,1,1,1,1,1,1,0,NULL,0,'2024-08-21 21:01:21','2024-08-21 21:01:21'),(15,9,9,13,1,1,'rs1.vagrantix1.example.net',1,'rs1.vagrantix1.example.net',0,0,NULL,NULL,NULL,NULL,0,1,1,1,1,1,0,NULL,0,'2024-08-21 21:05:55','2024-08-21 21:05:55'),(16,10,10,14,1,1,'rs2.vagrantix1.example.net',1,'rs2.vagrantix1.example.net',0,0,NULL,NULL,NULL,NULL,0,1,1,1,1,1,0,NULL,0,'2024-08-21 21:07:00','2024-08-21 21:07:00'),(17,73,73,15,2,1,'rs1.vagrantix2.example.net',1,'rs1.vagrantix2.example.net',0,0,NULL,NULL,NULL,NULL,0,1,1,1,1,1,0,NULL,0,'2024-08-21 21:07:57','2024-08-21 21:07:57');
/*!40000 ALTER TABLE `vlaninterface` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2024-08-21 11:55:22
