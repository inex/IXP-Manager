-- MySQL dump 10.13  Distrib 8.0.21, for osx10.15 (x86_64)
--
-- Host: localhost    Database: myapp_test
-- ------------------------------------------------------
-- Server version	8.0.21

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
  `created` datetime NOT NULL,
  `lastseenAt` datetime DEFAULT NULL,
  `lastseenFrom` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `description` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_9579321F800A1141` (`apiKey`),
  KEY `IDX_9579321FA76ED395` (`user_id`),
  CONSTRAINT `FK_9579321FA76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `api_keys`
--

LOCK TABLES `api_keys` WRITE;
/*!40000 ALTER TABLE `api_keys` DISABLE KEYS */;
INSERT INTO `api_keys` VALUES (1,1,'Syy4R8uXTquJNkSav4mmbk5eZWOgoc6FKUJPqOoGHhBjhsC9',NULL,'','2014-01-06 14:43:19','2017-05-19 09:48:49','127.0.0.1',NULL),(3,2,'Syy4R8uXTquJNkSav4mmbk5eZWOgoc6FKUJPqOoGHhBjhsC8',NULL,'','2014-01-06 14:43:19','2017-05-19 09:48:49','127.0.0.1',NULL),(4,3,'Syy4R8uXTquJNkSav4mmbk5eZWOgoc6FKUJPqOoGHhBjhsC7',NULL,'','2014-01-06 14:43:19','2017-05-19 09:48:49','127.0.0.1',NULL);
/*!40000 ALTER TABLE `api_keys` ENABLE KEYS */;
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
  PRIMARY KEY (`id`),
  UNIQUE KEY `src_protocol_dst` (`srcipaddressid`,`protocol`,`dstipaddressid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bgpsessiondata`
--

LOCK TABLES `bgpsessiondata` WRITE;
/*!40000 ALTER TABLE `bgpsessiondata` DISABLE KEYS */;
/*!40000 ALTER TABLE `bgpsessiondata` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `bgp_sessions_update` AFTER INSERT ON `bgpsessiondata` FOR EACH ROW BEGIN

		IF NOT EXISTS ( SELECT 1 FROM bgp_sessions WHERE srcipaddressid = NEW.srcipaddressid AND protocol = NEW.protocol AND dstipaddressid = NEW.dstipaddressid ) THEN
			INSERT INTO bgp_sessions
				( srcipaddressid, protocol, dstipaddressid, packetcount, last_seen, source )
			VALUES
				( NEW.srcipaddressid, NEW.protocol, NEW.dstipaddressid, NEW.packetcount, NOW(), NEW.source );
		ELSE
			UPDATE bgp_sessions SET
				last_seen   = NOW(),
				packetcount = packetcount + NEW.packetcount
			WHERE
				srcipaddressid = NEW.srcipaddressid AND protocol = NEW.protocol AND dstipaddressid = NEW.dstipaddressid;
		END IF;

		IF NOT EXISTS ( SELECT 1 FROM bgp_sessions WHERE dstipaddressid = NEW.srcipaddressid AND protocol = NEW.protocol AND srcipaddressid = NEW.dstipaddressid ) THEN
			INSERT INTO bgp_sessions
				( srcipaddressid, protocol, dstipaddressid, packetcount, last_seen, source )
			VALUES
				( NEW.dstipaddressid, NEW.protocol, NEW.srcipaddressid, NEW.packetcount, NOW(), NEW.source );
		ELSE
			UPDATE bgp_sessions SET
				last_seen   = NOW(),
				packetcount = packetcount + NEW.packetcount
			WHERE
				dstipaddressid = NEW.srcipaddressid AND protocol = NEW.protocol AND srcipaddressid = NEW.dstipaddressid;
		END IF;

	END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

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
  `cololocation` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `height` int DEFAULT NULL,
  `type` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `notes` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `u_counts_from` smallint DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_4CED05B05E237E06` (`name`),
  KEY `IDX_4CED05B03530CCF` (`locationid`),
  CONSTRAINT `FK_4CED05B03530CCF` FOREIGN KEY (`locationid`) REFERENCES `location` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cabinet`
--

LOCK TABLES `cabinet` WRITE;
/*!40000 ALTER TABLE `cabinet` DISABLE KEYS */;
INSERT INTO `cabinet` VALUES (1,1,'Cabinet 1','c1',0,'','',NULL);
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
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `company_billing_detail`
--

LOCK TABLES `company_billing_detail` WRITE;
/*!40000 ALTER TABLE `company_billing_detail` DISABLE KEYS */;
INSERT INTO `company_billing_detail` VALUES (1,NULL,'c/o The Bill Payers','Money House, Moneybags Street',NULL,'Dublin','D4','IE',NULL,NULL,NULL,NULL,0,'EMAIL',NULL,'NOBILLING'),(2,'','','','','','','','','','','',0,'EMAIL','','NOBILLING'),(3,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,'EMAIL',NULL,'NOBILLING'),(4,'','','','','','','','','','','',0,'EMAIL','','NOBILLING'),(5,'','','','','','','','','','','',0,'EMAIL','','NOBILLING');
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
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `company_registration_detail`
--

LOCK TABLES `company_registration_detail` WRITE;
/*!40000 ALTER TABLE `company_registration_detail` DISABLE KEYS */;
INSERT INTO `company_registration_detail` VALUES (1,'INEX','123456','Ireland','5 Somewhere',NULL,NULL,'Dublin','D4','IE'),(2,'','','','','','','','',''),(3,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(4,'','','','','','','','',''),(5,'','','','','','','','','');
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
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_92A539235E237E06` (`name`),
  KEY `IDX_92A53923F603EE73` (`vendor_id`),
  KEY `IDX_92A53923D351EC` (`cabinet_id`),
  CONSTRAINT `FK_92A53923D351EC` FOREIGN KEY (`cabinet_id`) REFERENCES `cabinet` (`id`),
  CONSTRAINT `FK_92A53923F603EE73` FOREIGN KEY (`vendor_id`) REFERENCES `vendor` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
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
  `switchid` int DEFAULT NULL,
  `description` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `port` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `speed` int DEFAULT NULL,
  `parity` int DEFAULT NULL,
  `stopbits` int DEFAULT NULL,
  `flowcontrol` int DEFAULT NULL,
  `autobaud` tinyint(1) DEFAULT NULL,
  `notes` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `console_server_id` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `console_server_port_uniq` (`console_server_id`,`port`),
  KEY `IDX_530316DCDA0209B9` (`custid`),
  KEY `IDX_530316DCF472E7C6` (`console_server_id`),
  CONSTRAINT `FK_530316DCDA0209B9` FOREIGN KEY (`custid`) REFERENCES `cust` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_530316DCF472E7C6` FOREIGN KEY (`console_server_id`) REFERENCES `console_server` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
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
  `lastupdated` datetime DEFAULT NULL,
  `lastupdatedby` int DEFAULT NULL,
  `creator` varchar(32) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_4C62E638DA0209B9` (`custid`),
  CONSTRAINT `FK_4C62E638DA0209B9` FOREIGN KEY (`custid`) REFERENCES `cust` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
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
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_40EA54CA5E237E06` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `contact_group`
--

LOCK TABLES `contact_group` WRITE;
/*!40000 ALTER TABLE `contact_group` DISABLE KEYS */;
INSERT INTO `contact_group` VALUES (1,'Billing','Contact role for billing matters','ROLE',1,0,'2014-01-06 13:54:22'),(2,'Technical','Contact role for technical matters','ROLE',1,0,'2014-01-06 13:54:22'),(3,'Admin','Contact role for admin matters','ROLE',1,0,'2014-01-06 13:54:22'),(4,'Marketing','Contact role for marketing matters','ROLE',1,0,'2014-01-06 13:54:22');
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
  PRIMARY KEY (`contact_id`,`contact_group_id`),
  KEY `IDX_FCD9E962E7A1254A` (`contact_id`),
  KEY `IDX_FCD9E962647145D0` (`contact_group_id`),
  CONSTRAINT `FK_FCD9E962647145D0` FOREIGN KEY (`contact_group_id`) REFERENCES `contact_group` (`id`),
  CONSTRAINT `FK_FCD9E962E7A1254A` FOREIGN KEY (`contact_id`) REFERENCES `contact` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
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
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `corebundles`
--

LOCK TABLES `corebundles` WRITE;
/*!40000 ALTER TABLE `corebundles` DISABLE KEYS */;
INSERT INTO `corebundles` VALUES (1,'Test Core Bundle',1,'Test Core Bundle',0,NULL,NULL,0,10,10,1);
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
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_E1A404B7FF664B20` (`physical_interface_id`),
  CONSTRAINT `FK_E1A404B7FF664B20` FOREIGN KEY (`physical_interface_id`) REFERENCES `physicalinterface` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `coreinterfaces`
--

LOCK TABLES `coreinterfaces` WRITE;
/*!40000 ALTER TABLE `coreinterfaces` DISABLE KEYS */;
INSERT INTO `coreinterfaces` VALUES (1,9),(2,10),(3,11),(4,12);
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
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_BE421236BEBB85C6` (`core_interface_sidea_id`),
  UNIQUE KEY `UNIQ_BE421236AC0E2A28` (`core_interface_sideb_id`),
  KEY `IDX_BE421236BE9AE9F7` (`core_bundle_id`),
  CONSTRAINT `FK_BE421236AC0E2A28` FOREIGN KEY (`core_interface_sideb_id`) REFERENCES `coreinterfaces` (`id`),
  CONSTRAINT `FK_BE421236BE9AE9F7` FOREIGN KEY (`core_bundle_id`) REFERENCES `corebundles` (`id`),
  CONSTRAINT `FK_BE421236BEBB85C6` FOREIGN KEY (`core_interface_sidea_id`) REFERENCES `coreinterfaces` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `corelinks`
--

LOCK TABLES `corelinks` WRITE;
/*!40000 ALTER TABLE `corelinks` DISABLE KEYS */;
INSERT INTO `corelinks` VALUES (1,1,2,1,1,'10.0.0.0/31',NULL,1),(2,3,4,1,1,'10.0.0.2/31',NULL,1);
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
  `lastupdated` date DEFAULT NULL,
  `lastupdatedby` int DEFAULT NULL,
  `creator` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `created` date DEFAULT NULL,
  `MD5Support` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT 'UNKNOWN',
  `isReseller` tinyint(1) NOT NULL DEFAULT '0',
  `in_manrs` tinyint(1) NOT NULL DEFAULT '0',
  `in_peeringdb` tinyint(1) NOT NULL DEFAULT '0',
  `peeringdb_oauth` tinyint(1) NOT NULL DEFAULT '1',
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
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cust`
--

LOCK TABLES `cust` WRITE;
/*!40000 ALTER TABLE `cust` DISABLE KEYS */;
INSERT INTO `cust` VALUES (1,NULL,1,1,NULL,'INEX',3,'inex','INEX',2128,1000,'peering@siep.net','+353 1 123 4567','+353 1 123 4567','+353 1 123 4568','noc@siep.com','24x7','http://www.siep.com/noc/','AS-INEX','AS-INEX','mandatory','http://www.siep.com/','2014-01-06',NULL,1,1,NULL,NULL,'travis','2014-01-06','YES',0,0,0,1),(2,1,2,2,NULL,'HEAnet',1,'heanet','HEAnet',1213,1000,'peering@example.com','','','','','0','','AS-HEANET',NULL,'open','http://www.example.com/','2014-01-06',NULL,1,1,NULL,NULL,'travis','2014-01-06','UNKNOWN',0,0,0,1),(3,13,3,3,NULL,'PCH DNS',1,'pchdns','PCH DNS',42,2000,'peering@example.com','','','','','0','','AS-PCH',NULL,'open','http://www.example.com/','2014-01-06',NULL,1,1,'2014-01-06',1,'travis','2014-01-06','YES',0,0,0,1),(4,2,4,4,NULL,'AS112',4,'as112','AS112',112,20,'peering@example.com','','','','','0','','',NULL,'open','http://www.example.com/','2014-01-06',NULL,1,1,NULL,NULL,'travis','2014-01-06','NO',0,0,0,1),(5,1,5,5,NULL,'Imagine',1,'imagine','Imagine',25441,1000,'peering@example.com','','','','','0','','AS-IBIS',NULL,'open','http://www.example.com/','2014-01-06',NULL,1,1,NULL,NULL,'travis','2014-01-06','YES',0,0,0,1);
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
  `created` datetime NOT NULL,
  `updated` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_6377D8679395C3F3` (`customer_id`),
  CONSTRAINT `FK_6377D8679395C3F3` FOREIGN KEY (`customer_id`) REFERENCES `cust` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
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
  `created` datetime NOT NULL,
  `updated` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_6B54CFB8389B783` (`tag`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cust_tag`
--

LOCK TABLES `cust_tag` WRITE;
/*!40000 ALTER TABLE `cust_tag` DISABLE KEYS */;
INSERT INTO `cust_tag` VALUES (1,'test-tag1','Test Tag1','Yeah!',0,'2018-06-19 13:38:28','2018-06-19 13:38:28'),(2,'test-tag2','Test Tag2','Yeah!',1,'2018-06-19 13:38:44','2018-06-19 13:38:44');
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
  PRIMARY KEY (`customer_tag_id`,`customer_id`),
  KEY `IDX_A6CFB30CB17BF40` (`customer_tag_id`),
  KEY `IDX_A6CFB30C9395C3F3` (`customer_id`),
  CONSTRAINT `FK_A6CFB30C9395C3F3` FOREIGN KEY (`customer_id`) REFERENCES `cust` (`id`),
  CONSTRAINT `FK_A6CFB30CB17BF40` FOREIGN KEY (`customer_tag_id`) REFERENCES `cust_tag` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cust_to_cust_tag`
--

LOCK TABLES `cust_to_cust_tag` WRITE;
/*!40000 ALTER TABLE `cust_to_cust_tag` DISABLE KEYS */;
INSERT INTO `cust_to_cust_tag` VALUES (1,4),(1,5),(2,1),(2,4);
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
  PRIMARY KEY (`id`),
  KEY `IDX_8127F9AADA0209B9` (`custid`),
  KEY `IDX_8127F9AA2B96718A` (`cabinetid`),
  CONSTRAINT `FK_8127F9AA2B96718A` FOREIGN KEY (`cabinetid`) REFERENCES `cabinet` (`id`),
  CONSTRAINT `FK_8127F9AADA0209B9` FOREIGN KEY (`custid`) REFERENCES `cust` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `custkit`
--

LOCK TABLES `custkit` WRITE;
/*!40000 ALTER TABLE `custkit` DISABLE KEYS */;
/*!40000 ALTER TABLE `custkit` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `customer_to_ixp`
--

DROP TABLE IF EXISTS `customer_to_ixp`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `customer_to_ixp` (
  `customer_id` int NOT NULL,
  `ixp_id` int NOT NULL,
  PRIMARY KEY (`customer_id`,`ixp_id`),
  KEY `IDX_E85DBF209395C3F3` (`customer_id`),
  KEY `IDX_E85DBF20A5A4E881` (`ixp_id`),
  CONSTRAINT `FK_E85DBF209395C3F3` FOREIGN KEY (`customer_id`) REFERENCES `cust` (`id`),
  CONSTRAINT `FK_E85DBF20A5A4E881` FOREIGN KEY (`ixp_id`) REFERENCES `ixp` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `customer_to_ixp`
--

LOCK TABLES `customer_to_ixp` WRITE;
/*!40000 ALTER TABLE `customer_to_ixp` DISABLE KEYS */;
INSERT INTO `customer_to_ixp` VALUES (1,1),(2,1),(3,1),(4,1),(5,1);
/*!40000 ALTER TABLE `customer_to_ixp` ENABLE KEYS */;
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
  `created_at` datetime NOT NULL,
  `extra_attributes` json DEFAULT NULL COMMENT '(DC2Type:json)',
  `last_login_via` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
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
INSERT INTO `customer_to_users` VALUES (1,1,1,3,'2020-01-27 12:04:24','127.0.0.1','2019-05-10 13:40:45','{\"created_by\": {\"type\": \"migration-script\"}}',NULL),(2,5,2,2,'2018-06-20 10:23:22','127.0.0.1','2019-05-10 13:40:45','{\"created_by\": {\"type\": \"migration-script\"}}',NULL),(3,5,3,1,'2018-06-20 10:23:58','127.0.0.1','2019-05-10 13:40:45','{\"created_by\": {\"type\": \"migration-script\"}}',NULL),(4,2,4,1,'1970-01-01 00:00:00','','2019-05-10 13:40:45','{\"created_by\": {\"type\": \"migration-script\"}}',NULL),(5,2,5,2,'2018-06-20 10:24:24','127.0.0.1','2019-05-10 13:40:45','{\"created_by\": {\"type\": \"migration-script\"}}',NULL);
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
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
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
  `ixp_id` int NOT NULL,
  `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `shortname` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `isPrimary` tinyint(1) NOT NULL DEFAULT '0',
  `peeringdb_ix_id` bigint DEFAULT NULL,
  `ixf_ix_id` bigint DEFAULT NULL,
  `country` varchar(2) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `IXPSN` (`shortname`,`ixp_id`),
  KEY `IDX_D129B190A5A4E881` (`ixp_id`),
  CONSTRAINT `FK_D129B190A5A4E881` FOREIGN KEY (`ixp_id`) REFERENCES `ixp` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `infrastructure`
--

LOCK TABLES `infrastructure` WRITE;
/*!40000 ALTER TABLE `infrastructure` DISABLE KEYS */;
INSERT INTO `infrastructure` VALUES (1,1,'Infrastructure #1','#1',1,48,20,NULL),(2,1,'Infrastructure #2','#2',0,387,645,NULL);
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
  PRIMARY KEY (`id`),
  UNIQUE KEY `vlan_address` (`vlanid`,`address`),
  KEY `IDX_A44BCBEEF48D6D0` (`vlanid`),
  CONSTRAINT `FK_A44BCBEEF48D6D0` FOREIGN KEY (`vlanid`) REFERENCES `vlan` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=253 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ipv4address`
--

LOCK TABLES `ipv4address` WRITE;
/*!40000 ALTER TABLE `ipv4address` DISABLE KEYS */;
INSERT INTO `ipv4address` VALUES (1,1,'10.1.0.1'),(10,1,'10.1.0.10'),(100,1,'10.1.0.100'),(101,1,'10.1.0.101'),(102,1,'10.1.0.102'),(103,1,'10.1.0.103'),(104,1,'10.1.0.104'),(105,1,'10.1.0.105'),(106,1,'10.1.0.106'),(107,1,'10.1.0.107'),(108,1,'10.1.0.108'),(109,1,'10.1.0.109'),(11,1,'10.1.0.11'),(110,1,'10.1.0.110'),(111,1,'10.1.0.111'),(112,1,'10.1.0.112'),(113,1,'10.1.0.113'),(114,1,'10.1.0.114'),(115,1,'10.1.0.115'),(116,1,'10.1.0.116'),(117,1,'10.1.0.117'),(118,1,'10.1.0.118'),(119,1,'10.1.0.119'),(12,1,'10.1.0.12'),(120,1,'10.1.0.120'),(121,1,'10.1.0.121'),(122,1,'10.1.0.122'),(123,1,'10.1.0.123'),(124,1,'10.1.0.124'),(125,1,'10.1.0.125'),(126,1,'10.1.0.126'),(13,1,'10.1.0.13'),(14,1,'10.1.0.14'),(15,1,'10.1.0.15'),(16,1,'10.1.0.16'),(17,1,'10.1.0.17'),(18,1,'10.1.0.18'),(19,1,'10.1.0.19'),(2,1,'10.1.0.2'),(20,1,'10.1.0.20'),(21,1,'10.1.0.21'),(22,1,'10.1.0.22'),(23,1,'10.1.0.23'),(24,1,'10.1.0.24'),(25,1,'10.1.0.25'),(26,1,'10.1.0.26'),(27,1,'10.1.0.27'),(28,1,'10.1.0.28'),(29,1,'10.1.0.29'),(3,1,'10.1.0.3'),(30,1,'10.1.0.30'),(31,1,'10.1.0.31'),(32,1,'10.1.0.32'),(33,1,'10.1.0.33'),(34,1,'10.1.0.34'),(35,1,'10.1.0.35'),(36,1,'10.1.0.36'),(37,1,'10.1.0.37'),(38,1,'10.1.0.38'),(39,1,'10.1.0.39'),(4,1,'10.1.0.4'),(40,1,'10.1.0.40'),(41,1,'10.1.0.41'),(42,1,'10.1.0.42'),(43,1,'10.1.0.43'),(44,1,'10.1.0.44'),(45,1,'10.1.0.45'),(46,1,'10.1.0.46'),(47,1,'10.1.0.47'),(48,1,'10.1.0.48'),(49,1,'10.1.0.49'),(5,1,'10.1.0.5'),(50,1,'10.1.0.50'),(51,1,'10.1.0.51'),(52,1,'10.1.0.52'),(53,1,'10.1.0.53'),(54,1,'10.1.0.54'),(55,1,'10.1.0.55'),(56,1,'10.1.0.56'),(57,1,'10.1.0.57'),(58,1,'10.1.0.58'),(59,1,'10.1.0.59'),(6,1,'10.1.0.6'),(60,1,'10.1.0.60'),(61,1,'10.1.0.61'),(62,1,'10.1.0.62'),(63,1,'10.1.0.63'),(64,1,'10.1.0.64'),(65,1,'10.1.0.65'),(66,1,'10.1.0.66'),(67,1,'10.1.0.67'),(68,1,'10.1.0.68'),(69,1,'10.1.0.69'),(7,1,'10.1.0.7'),(70,1,'10.1.0.70'),(71,1,'10.1.0.71'),(72,1,'10.1.0.72'),(73,1,'10.1.0.73'),(74,1,'10.1.0.74'),(75,1,'10.1.0.75'),(76,1,'10.1.0.76'),(77,1,'10.1.0.77'),(78,1,'10.1.0.78'),(79,1,'10.1.0.79'),(8,1,'10.1.0.8'),(80,1,'10.1.0.80'),(81,1,'10.1.0.81'),(82,1,'10.1.0.82'),(83,1,'10.1.0.83'),(84,1,'10.1.0.84'),(85,1,'10.1.0.85'),(86,1,'10.1.0.86'),(87,1,'10.1.0.87'),(88,1,'10.1.0.88'),(89,1,'10.1.0.89'),(9,1,'10.1.0.9'),(90,1,'10.1.0.90'),(91,1,'10.1.0.91'),(92,1,'10.1.0.92'),(93,1,'10.1.0.93'),(94,1,'10.1.0.94'),(95,1,'10.1.0.95'),(96,1,'10.1.0.96'),(97,1,'10.1.0.97'),(98,1,'10.1.0.98'),(99,1,'10.1.0.99'),(127,2,'10.2.0.1'),(136,2,'10.2.0.10'),(226,2,'10.2.0.100'),(227,2,'10.2.0.101'),(228,2,'10.2.0.102'),(229,2,'10.2.0.103'),(230,2,'10.2.0.104'),(231,2,'10.2.0.105'),(232,2,'10.2.0.106'),(233,2,'10.2.0.107'),(234,2,'10.2.0.108'),(235,2,'10.2.0.109'),(137,2,'10.2.0.11'),(236,2,'10.2.0.110'),(237,2,'10.2.0.111'),(238,2,'10.2.0.112'),(239,2,'10.2.0.113'),(240,2,'10.2.0.114'),(241,2,'10.2.0.115'),(242,2,'10.2.0.116'),(243,2,'10.2.0.117'),(244,2,'10.2.0.118'),(245,2,'10.2.0.119'),(138,2,'10.2.0.12'),(246,2,'10.2.0.120'),(247,2,'10.2.0.121'),(248,2,'10.2.0.122'),(249,2,'10.2.0.123'),(250,2,'10.2.0.124'),(251,2,'10.2.0.125'),(252,2,'10.2.0.126'),(139,2,'10.2.0.13'),(140,2,'10.2.0.14'),(141,2,'10.2.0.15'),(142,2,'10.2.0.16'),(143,2,'10.2.0.17'),(144,2,'10.2.0.18'),(145,2,'10.2.0.19'),(128,2,'10.2.0.2'),(146,2,'10.2.0.20'),(147,2,'10.2.0.21'),(148,2,'10.2.0.22'),(149,2,'10.2.0.23'),(150,2,'10.2.0.24'),(151,2,'10.2.0.25'),(152,2,'10.2.0.26'),(153,2,'10.2.0.27'),(154,2,'10.2.0.28'),(155,2,'10.2.0.29'),(129,2,'10.2.0.3'),(156,2,'10.2.0.30'),(157,2,'10.2.0.31'),(158,2,'10.2.0.32'),(159,2,'10.2.0.33'),(160,2,'10.2.0.34'),(161,2,'10.2.0.35'),(162,2,'10.2.0.36'),(163,2,'10.2.0.37'),(164,2,'10.2.0.38'),(165,2,'10.2.0.39'),(130,2,'10.2.0.4'),(166,2,'10.2.0.40'),(167,2,'10.2.0.41'),(168,2,'10.2.0.42'),(169,2,'10.2.0.43'),(170,2,'10.2.0.44'),(171,2,'10.2.0.45'),(172,2,'10.2.0.46'),(173,2,'10.2.0.47'),(174,2,'10.2.0.48'),(175,2,'10.2.0.49'),(131,2,'10.2.0.5'),(176,2,'10.2.0.50'),(177,2,'10.2.0.51'),(178,2,'10.2.0.52'),(179,2,'10.2.0.53'),(180,2,'10.2.0.54'),(181,2,'10.2.0.55'),(182,2,'10.2.0.56'),(183,2,'10.2.0.57'),(184,2,'10.2.0.58'),(185,2,'10.2.0.59'),(132,2,'10.2.0.6'),(186,2,'10.2.0.60'),(187,2,'10.2.0.61'),(188,2,'10.2.0.62'),(189,2,'10.2.0.63'),(190,2,'10.2.0.64'),(191,2,'10.2.0.65'),(192,2,'10.2.0.66'),(193,2,'10.2.0.67'),(194,2,'10.2.0.68'),(195,2,'10.2.0.69'),(133,2,'10.2.0.7'),(196,2,'10.2.0.70'),(197,2,'10.2.0.71'),(198,2,'10.2.0.72'),(199,2,'10.2.0.73'),(200,2,'10.2.0.74'),(201,2,'10.2.0.75'),(202,2,'10.2.0.76'),(203,2,'10.2.0.77'),(204,2,'10.2.0.78'),(205,2,'10.2.0.79'),(134,2,'10.2.0.8'),(206,2,'10.2.0.80'),(207,2,'10.2.0.81'),(208,2,'10.2.0.82'),(209,2,'10.2.0.83'),(210,2,'10.2.0.84'),(211,2,'10.2.0.85'),(212,2,'10.2.0.86'),(213,2,'10.2.0.87'),(214,2,'10.2.0.88'),(215,2,'10.2.0.89'),(135,2,'10.2.0.9'),(216,2,'10.2.0.90'),(217,2,'10.2.0.91'),(218,2,'10.2.0.92'),(219,2,'10.2.0.93'),(220,2,'10.2.0.94'),(221,2,'10.2.0.95'),(222,2,'10.2.0.96'),(223,2,'10.2.0.97'),(224,2,'10.2.0.98'),(225,2,'10.2.0.99');
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
  PRIMARY KEY (`id`),
  UNIQUE KEY `vlan_address` (`vlanid`,`address`),
  KEY `IDX_E66ECC93F48D6D0` (`vlanid`),
  CONSTRAINT `FK_E66ECC93F48D6D0` FOREIGN KEY (`vlanid`) REFERENCES `vlan` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=801 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ipv6address`
--

LOCK TABLES `ipv6address` WRITE;
/*!40000 ALTER TABLE `ipv6address` DISABLE KEYS */;
INSERT INTO `ipv6address` VALUES (1,1,'2001:db8:1::1'),(16,1,'2001:db8:1::10'),(256,1,'2001:db8:1::100'),(257,1,'2001:db8:1::101'),(258,1,'2001:db8:1::102'),(259,1,'2001:db8:1::103'),(260,1,'2001:db8:1::104'),(261,1,'2001:db8:1::105'),(262,1,'2001:db8:1::106'),(263,1,'2001:db8:1::107'),(264,1,'2001:db8:1::108'),(265,1,'2001:db8:1::109'),(266,1,'2001:db8:1::10a'),(267,1,'2001:db8:1::10b'),(268,1,'2001:db8:1::10c'),(269,1,'2001:db8:1::10d'),(270,1,'2001:db8:1::10e'),(271,1,'2001:db8:1::10f'),(17,1,'2001:db8:1::11'),(272,1,'2001:db8:1::110'),(273,1,'2001:db8:1::111'),(274,1,'2001:db8:1::112'),(275,1,'2001:db8:1::113'),(276,1,'2001:db8:1::114'),(277,1,'2001:db8:1::115'),(278,1,'2001:db8:1::116'),(279,1,'2001:db8:1::117'),(280,1,'2001:db8:1::118'),(281,1,'2001:db8:1::119'),(282,1,'2001:db8:1::11a'),(283,1,'2001:db8:1::11b'),(284,1,'2001:db8:1::11c'),(285,1,'2001:db8:1::11d'),(286,1,'2001:db8:1::11e'),(287,1,'2001:db8:1::11f'),(18,1,'2001:db8:1::12'),(288,1,'2001:db8:1::120'),(289,1,'2001:db8:1::121'),(290,1,'2001:db8:1::122'),(291,1,'2001:db8:1::123'),(292,1,'2001:db8:1::124'),(293,1,'2001:db8:1::125'),(294,1,'2001:db8:1::126'),(295,1,'2001:db8:1::127'),(296,1,'2001:db8:1::128'),(297,1,'2001:db8:1::129'),(298,1,'2001:db8:1::12a'),(299,1,'2001:db8:1::12b'),(300,1,'2001:db8:1::12c'),(301,1,'2001:db8:1::12d'),(302,1,'2001:db8:1::12e'),(303,1,'2001:db8:1::12f'),(19,1,'2001:db8:1::13'),(304,1,'2001:db8:1::130'),(305,1,'2001:db8:1::131'),(306,1,'2001:db8:1::132'),(307,1,'2001:db8:1::133'),(308,1,'2001:db8:1::134'),(309,1,'2001:db8:1::135'),(310,1,'2001:db8:1::136'),(311,1,'2001:db8:1::137'),(312,1,'2001:db8:1::138'),(313,1,'2001:db8:1::139'),(314,1,'2001:db8:1::13a'),(315,1,'2001:db8:1::13b'),(316,1,'2001:db8:1::13c'),(317,1,'2001:db8:1::13d'),(318,1,'2001:db8:1::13e'),(319,1,'2001:db8:1::13f'),(20,1,'2001:db8:1::14'),(320,1,'2001:db8:1::140'),(321,1,'2001:db8:1::141'),(322,1,'2001:db8:1::142'),(323,1,'2001:db8:1::143'),(324,1,'2001:db8:1::144'),(325,1,'2001:db8:1::145'),(326,1,'2001:db8:1::146'),(327,1,'2001:db8:1::147'),(328,1,'2001:db8:1::148'),(329,1,'2001:db8:1::149'),(330,1,'2001:db8:1::14a'),(331,1,'2001:db8:1::14b'),(332,1,'2001:db8:1::14c'),(333,1,'2001:db8:1::14d'),(334,1,'2001:db8:1::14e'),(335,1,'2001:db8:1::14f'),(21,1,'2001:db8:1::15'),(336,1,'2001:db8:1::150'),(337,1,'2001:db8:1::151'),(338,1,'2001:db8:1::152'),(339,1,'2001:db8:1::153'),(340,1,'2001:db8:1::154'),(341,1,'2001:db8:1::155'),(342,1,'2001:db8:1::156'),(343,1,'2001:db8:1::157'),(344,1,'2001:db8:1::158'),(345,1,'2001:db8:1::159'),(346,1,'2001:db8:1::15a'),(347,1,'2001:db8:1::15b'),(348,1,'2001:db8:1::15c'),(349,1,'2001:db8:1::15d'),(350,1,'2001:db8:1::15e'),(351,1,'2001:db8:1::15f'),(22,1,'2001:db8:1::16'),(352,1,'2001:db8:1::160'),(353,1,'2001:db8:1::161'),(354,1,'2001:db8:1::162'),(355,1,'2001:db8:1::163'),(356,1,'2001:db8:1::164'),(357,1,'2001:db8:1::165'),(358,1,'2001:db8:1::166'),(359,1,'2001:db8:1::167'),(360,1,'2001:db8:1::168'),(361,1,'2001:db8:1::169'),(362,1,'2001:db8:1::16a'),(363,1,'2001:db8:1::16b'),(364,1,'2001:db8:1::16c'),(365,1,'2001:db8:1::16d'),(366,1,'2001:db8:1::16e'),(367,1,'2001:db8:1::16f'),(23,1,'2001:db8:1::17'),(368,1,'2001:db8:1::170'),(369,1,'2001:db8:1::171'),(370,1,'2001:db8:1::172'),(371,1,'2001:db8:1::173'),(372,1,'2001:db8:1::174'),(373,1,'2001:db8:1::175'),(374,1,'2001:db8:1::176'),(375,1,'2001:db8:1::177'),(376,1,'2001:db8:1::178'),(377,1,'2001:db8:1::179'),(378,1,'2001:db8:1::17a'),(379,1,'2001:db8:1::17b'),(380,1,'2001:db8:1::17c'),(381,1,'2001:db8:1::17d'),(382,1,'2001:db8:1::17e'),(383,1,'2001:db8:1::17f'),(24,1,'2001:db8:1::18'),(384,1,'2001:db8:1::180'),(385,1,'2001:db8:1::181'),(386,1,'2001:db8:1::182'),(387,1,'2001:db8:1::183'),(388,1,'2001:db8:1::184'),(389,1,'2001:db8:1::185'),(390,1,'2001:db8:1::186'),(391,1,'2001:db8:1::187'),(392,1,'2001:db8:1::188'),(393,1,'2001:db8:1::189'),(394,1,'2001:db8:1::18a'),(395,1,'2001:db8:1::18b'),(396,1,'2001:db8:1::18c'),(397,1,'2001:db8:1::18d'),(398,1,'2001:db8:1::18e'),(399,1,'2001:db8:1::18f'),(25,1,'2001:db8:1::19'),(400,1,'2001:db8:1::190'),(26,1,'2001:db8:1::1a'),(27,1,'2001:db8:1::1b'),(28,1,'2001:db8:1::1c'),(29,1,'2001:db8:1::1d'),(30,1,'2001:db8:1::1e'),(31,1,'2001:db8:1::1f'),(2,1,'2001:db8:1::2'),(32,1,'2001:db8:1::20'),(33,1,'2001:db8:1::21'),(34,1,'2001:db8:1::22'),(35,1,'2001:db8:1::23'),(36,1,'2001:db8:1::24'),(37,1,'2001:db8:1::25'),(38,1,'2001:db8:1::26'),(39,1,'2001:db8:1::27'),(40,1,'2001:db8:1::28'),(41,1,'2001:db8:1::29'),(42,1,'2001:db8:1::2a'),(43,1,'2001:db8:1::2b'),(44,1,'2001:db8:1::2c'),(45,1,'2001:db8:1::2d'),(46,1,'2001:db8:1::2e'),(47,1,'2001:db8:1::2f'),(3,1,'2001:db8:1::3'),(48,1,'2001:db8:1::30'),(49,1,'2001:db8:1::31'),(50,1,'2001:db8:1::32'),(51,1,'2001:db8:1::33'),(52,1,'2001:db8:1::34'),(53,1,'2001:db8:1::35'),(54,1,'2001:db8:1::36'),(55,1,'2001:db8:1::37'),(56,1,'2001:db8:1::38'),(57,1,'2001:db8:1::39'),(58,1,'2001:db8:1::3a'),(59,1,'2001:db8:1::3b'),(60,1,'2001:db8:1::3c'),(61,1,'2001:db8:1::3d'),(62,1,'2001:db8:1::3e'),(63,1,'2001:db8:1::3f'),(4,1,'2001:db8:1::4'),(64,1,'2001:db8:1::40'),(65,1,'2001:db8:1::41'),(66,1,'2001:db8:1::42'),(67,1,'2001:db8:1::43'),(68,1,'2001:db8:1::44'),(69,1,'2001:db8:1::45'),(70,1,'2001:db8:1::46'),(71,1,'2001:db8:1::47'),(72,1,'2001:db8:1::48'),(73,1,'2001:db8:1::49'),(74,1,'2001:db8:1::4a'),(75,1,'2001:db8:1::4b'),(76,1,'2001:db8:1::4c'),(77,1,'2001:db8:1::4d'),(78,1,'2001:db8:1::4e'),(79,1,'2001:db8:1::4f'),(5,1,'2001:db8:1::5'),(80,1,'2001:db8:1::50'),(81,1,'2001:db8:1::51'),(82,1,'2001:db8:1::52'),(83,1,'2001:db8:1::53'),(84,1,'2001:db8:1::54'),(85,1,'2001:db8:1::55'),(86,1,'2001:db8:1::56'),(87,1,'2001:db8:1::57'),(88,1,'2001:db8:1::58'),(89,1,'2001:db8:1::59'),(90,1,'2001:db8:1::5a'),(91,1,'2001:db8:1::5b'),(92,1,'2001:db8:1::5c'),(93,1,'2001:db8:1::5d'),(94,1,'2001:db8:1::5e'),(95,1,'2001:db8:1::5f'),(6,1,'2001:db8:1::6'),(96,1,'2001:db8:1::60'),(97,1,'2001:db8:1::61'),(98,1,'2001:db8:1::62'),(99,1,'2001:db8:1::63'),(100,1,'2001:db8:1::64'),(101,1,'2001:db8:1::65'),(102,1,'2001:db8:1::66'),(103,1,'2001:db8:1::67'),(104,1,'2001:db8:1::68'),(105,1,'2001:db8:1::69'),(106,1,'2001:db8:1::6a'),(107,1,'2001:db8:1::6b'),(108,1,'2001:db8:1::6c'),(109,1,'2001:db8:1::6d'),(110,1,'2001:db8:1::6e'),(111,1,'2001:db8:1::6f'),(7,1,'2001:db8:1::7'),(112,1,'2001:db8:1::70'),(113,1,'2001:db8:1::71'),(114,1,'2001:db8:1::72'),(115,1,'2001:db8:1::73'),(116,1,'2001:db8:1::74'),(117,1,'2001:db8:1::75'),(118,1,'2001:db8:1::76'),(119,1,'2001:db8:1::77'),(120,1,'2001:db8:1::78'),(121,1,'2001:db8:1::79'),(122,1,'2001:db8:1::7a'),(123,1,'2001:db8:1::7b'),(124,1,'2001:db8:1::7c'),(125,1,'2001:db8:1::7d'),(126,1,'2001:db8:1::7e'),(127,1,'2001:db8:1::7f'),(8,1,'2001:db8:1::8'),(128,1,'2001:db8:1::80'),(129,1,'2001:db8:1::81'),(130,1,'2001:db8:1::82'),(131,1,'2001:db8:1::83'),(132,1,'2001:db8:1::84'),(133,1,'2001:db8:1::85'),(134,1,'2001:db8:1::86'),(135,1,'2001:db8:1::87'),(136,1,'2001:db8:1::88'),(137,1,'2001:db8:1::89'),(138,1,'2001:db8:1::8a'),(139,1,'2001:db8:1::8b'),(140,1,'2001:db8:1::8c'),(141,1,'2001:db8:1::8d'),(142,1,'2001:db8:1::8e'),(143,1,'2001:db8:1::8f'),(9,1,'2001:db8:1::9'),(144,1,'2001:db8:1::90'),(145,1,'2001:db8:1::91'),(146,1,'2001:db8:1::92'),(147,1,'2001:db8:1::93'),(148,1,'2001:db8:1::94'),(149,1,'2001:db8:1::95'),(150,1,'2001:db8:1::96'),(151,1,'2001:db8:1::97'),(152,1,'2001:db8:1::98'),(153,1,'2001:db8:1::99'),(154,1,'2001:db8:1::9a'),(155,1,'2001:db8:1::9b'),(156,1,'2001:db8:1::9c'),(157,1,'2001:db8:1::9d'),(158,1,'2001:db8:1::9e'),(159,1,'2001:db8:1::9f'),(10,1,'2001:db8:1::a'),(160,1,'2001:db8:1::a0'),(161,1,'2001:db8:1::a1'),(162,1,'2001:db8:1::a2'),(163,1,'2001:db8:1::a3'),(164,1,'2001:db8:1::a4'),(165,1,'2001:db8:1::a5'),(166,1,'2001:db8:1::a6'),(167,1,'2001:db8:1::a7'),(168,1,'2001:db8:1::a8'),(169,1,'2001:db8:1::a9'),(170,1,'2001:db8:1::aa'),(171,1,'2001:db8:1::ab'),(172,1,'2001:db8:1::ac'),(173,1,'2001:db8:1::ad'),(174,1,'2001:db8:1::ae'),(175,1,'2001:db8:1::af'),(11,1,'2001:db8:1::b'),(176,1,'2001:db8:1::b0'),(177,1,'2001:db8:1::b1'),(178,1,'2001:db8:1::b2'),(179,1,'2001:db8:1::b3'),(180,1,'2001:db8:1::b4'),(181,1,'2001:db8:1::b5'),(182,1,'2001:db8:1::b6'),(183,1,'2001:db8:1::b7'),(184,1,'2001:db8:1::b8'),(185,1,'2001:db8:1::b9'),(186,1,'2001:db8:1::ba'),(187,1,'2001:db8:1::bb'),(188,1,'2001:db8:1::bc'),(189,1,'2001:db8:1::bd'),(190,1,'2001:db8:1::be'),(191,1,'2001:db8:1::bf'),(12,1,'2001:db8:1::c'),(192,1,'2001:db8:1::c0'),(193,1,'2001:db8:1::c1'),(194,1,'2001:db8:1::c2'),(195,1,'2001:db8:1::c3'),(196,1,'2001:db8:1::c4'),(197,1,'2001:db8:1::c5'),(198,1,'2001:db8:1::c6'),(199,1,'2001:db8:1::c7'),(200,1,'2001:db8:1::c8'),(201,1,'2001:db8:1::c9'),(202,1,'2001:db8:1::ca'),(203,1,'2001:db8:1::cb'),(204,1,'2001:db8:1::cc'),(205,1,'2001:db8:1::cd'),(206,1,'2001:db8:1::ce'),(207,1,'2001:db8:1::cf'),(13,1,'2001:db8:1::d'),(208,1,'2001:db8:1::d0'),(209,1,'2001:db8:1::d1'),(210,1,'2001:db8:1::d2'),(211,1,'2001:db8:1::d3'),(212,1,'2001:db8:1::d4'),(213,1,'2001:db8:1::d5'),(214,1,'2001:db8:1::d6'),(215,1,'2001:db8:1::d7'),(216,1,'2001:db8:1::d8'),(217,1,'2001:db8:1::d9'),(218,1,'2001:db8:1::da'),(219,1,'2001:db8:1::db'),(220,1,'2001:db8:1::dc'),(221,1,'2001:db8:1::dd'),(222,1,'2001:db8:1::de'),(223,1,'2001:db8:1::df'),(14,1,'2001:db8:1::e'),(224,1,'2001:db8:1::e0'),(225,1,'2001:db8:1::e1'),(226,1,'2001:db8:1::e2'),(227,1,'2001:db8:1::e3'),(228,1,'2001:db8:1::e4'),(229,1,'2001:db8:1::e5'),(230,1,'2001:db8:1::e6'),(231,1,'2001:db8:1::e7'),(232,1,'2001:db8:1::e8'),(233,1,'2001:db8:1::e9'),(234,1,'2001:db8:1::ea'),(235,1,'2001:db8:1::eb'),(236,1,'2001:db8:1::ec'),(237,1,'2001:db8:1::ed'),(238,1,'2001:db8:1::ee'),(239,1,'2001:db8:1::ef'),(15,1,'2001:db8:1::f'),(240,1,'2001:db8:1::f0'),(241,1,'2001:db8:1::f1'),(242,1,'2001:db8:1::f2'),(243,1,'2001:db8:1::f3'),(244,1,'2001:db8:1::f4'),(245,1,'2001:db8:1::f5'),(246,1,'2001:db8:1::f6'),(247,1,'2001:db8:1::f7'),(248,1,'2001:db8:1::f8'),(249,1,'2001:db8:1::f9'),(250,1,'2001:db8:1::fa'),(251,1,'2001:db8:1::fb'),(252,1,'2001:db8:1::fc'),(253,1,'2001:db8:1::fd'),(254,1,'2001:db8:1::fe'),(255,1,'2001:db8:1::ff'),(401,2,'2001:db8:2::1'),(416,2,'2001:db8:2::10'),(656,2,'2001:db8:2::100'),(657,2,'2001:db8:2::101'),(658,2,'2001:db8:2::102'),(659,2,'2001:db8:2::103'),(660,2,'2001:db8:2::104'),(661,2,'2001:db8:2::105'),(662,2,'2001:db8:2::106'),(663,2,'2001:db8:2::107'),(664,2,'2001:db8:2::108'),(665,2,'2001:db8:2::109'),(666,2,'2001:db8:2::10a'),(667,2,'2001:db8:2::10b'),(668,2,'2001:db8:2::10c'),(669,2,'2001:db8:2::10d'),(670,2,'2001:db8:2::10e'),(671,2,'2001:db8:2::10f'),(417,2,'2001:db8:2::11'),(672,2,'2001:db8:2::110'),(673,2,'2001:db8:2::111'),(674,2,'2001:db8:2::112'),(675,2,'2001:db8:2::113'),(676,2,'2001:db8:2::114'),(677,2,'2001:db8:2::115'),(678,2,'2001:db8:2::116'),(679,2,'2001:db8:2::117'),(680,2,'2001:db8:2::118'),(681,2,'2001:db8:2::119'),(682,2,'2001:db8:2::11a'),(683,2,'2001:db8:2::11b'),(684,2,'2001:db8:2::11c'),(685,2,'2001:db8:2::11d'),(686,2,'2001:db8:2::11e'),(687,2,'2001:db8:2::11f'),(418,2,'2001:db8:2::12'),(688,2,'2001:db8:2::120'),(689,2,'2001:db8:2::121'),(690,2,'2001:db8:2::122'),(691,2,'2001:db8:2::123'),(692,2,'2001:db8:2::124'),(693,2,'2001:db8:2::125'),(694,2,'2001:db8:2::126'),(695,2,'2001:db8:2::127'),(696,2,'2001:db8:2::128'),(697,2,'2001:db8:2::129'),(698,2,'2001:db8:2::12a'),(699,2,'2001:db8:2::12b'),(700,2,'2001:db8:2::12c'),(701,2,'2001:db8:2::12d'),(702,2,'2001:db8:2::12e'),(703,2,'2001:db8:2::12f'),(419,2,'2001:db8:2::13'),(704,2,'2001:db8:2::130'),(705,2,'2001:db8:2::131'),(706,2,'2001:db8:2::132'),(707,2,'2001:db8:2::133'),(708,2,'2001:db8:2::134'),(709,2,'2001:db8:2::135'),(710,2,'2001:db8:2::136'),(711,2,'2001:db8:2::137'),(712,2,'2001:db8:2::138'),(713,2,'2001:db8:2::139'),(714,2,'2001:db8:2::13a'),(715,2,'2001:db8:2::13b'),(716,2,'2001:db8:2::13c'),(717,2,'2001:db8:2::13d'),(718,2,'2001:db8:2::13e'),(719,2,'2001:db8:2::13f'),(420,2,'2001:db8:2::14'),(720,2,'2001:db8:2::140'),(721,2,'2001:db8:2::141'),(722,2,'2001:db8:2::142'),(723,2,'2001:db8:2::143'),(724,2,'2001:db8:2::144'),(725,2,'2001:db8:2::145'),(726,2,'2001:db8:2::146'),(727,2,'2001:db8:2::147'),(728,2,'2001:db8:2::148'),(729,2,'2001:db8:2::149'),(730,2,'2001:db8:2::14a'),(731,2,'2001:db8:2::14b'),(732,2,'2001:db8:2::14c'),(733,2,'2001:db8:2::14d'),(734,2,'2001:db8:2::14e'),(735,2,'2001:db8:2::14f'),(421,2,'2001:db8:2::15'),(736,2,'2001:db8:2::150'),(737,2,'2001:db8:2::151'),(738,2,'2001:db8:2::152'),(739,2,'2001:db8:2::153'),(740,2,'2001:db8:2::154'),(741,2,'2001:db8:2::155'),(742,2,'2001:db8:2::156'),(743,2,'2001:db8:2::157'),(744,2,'2001:db8:2::158'),(745,2,'2001:db8:2::159'),(746,2,'2001:db8:2::15a'),(747,2,'2001:db8:2::15b'),(748,2,'2001:db8:2::15c'),(749,2,'2001:db8:2::15d'),(750,2,'2001:db8:2::15e'),(751,2,'2001:db8:2::15f'),(422,2,'2001:db8:2::16'),(752,2,'2001:db8:2::160'),(753,2,'2001:db8:2::161'),(754,2,'2001:db8:2::162'),(755,2,'2001:db8:2::163'),(756,2,'2001:db8:2::164'),(757,2,'2001:db8:2::165'),(758,2,'2001:db8:2::166'),(759,2,'2001:db8:2::167'),(760,2,'2001:db8:2::168'),(761,2,'2001:db8:2::169'),(762,2,'2001:db8:2::16a'),(763,2,'2001:db8:2::16b'),(764,2,'2001:db8:2::16c'),(765,2,'2001:db8:2::16d'),(766,2,'2001:db8:2::16e'),(767,2,'2001:db8:2::16f'),(423,2,'2001:db8:2::17'),(768,2,'2001:db8:2::170'),(769,2,'2001:db8:2::171'),(770,2,'2001:db8:2::172'),(771,2,'2001:db8:2::173'),(772,2,'2001:db8:2::174'),(773,2,'2001:db8:2::175'),(774,2,'2001:db8:2::176'),(775,2,'2001:db8:2::177'),(776,2,'2001:db8:2::178'),(777,2,'2001:db8:2::179'),(778,2,'2001:db8:2::17a'),(779,2,'2001:db8:2::17b'),(780,2,'2001:db8:2::17c'),(781,2,'2001:db8:2::17d'),(782,2,'2001:db8:2::17e'),(783,2,'2001:db8:2::17f'),(424,2,'2001:db8:2::18'),(784,2,'2001:db8:2::180'),(785,2,'2001:db8:2::181'),(786,2,'2001:db8:2::182'),(787,2,'2001:db8:2::183'),(788,2,'2001:db8:2::184'),(789,2,'2001:db8:2::185'),(790,2,'2001:db8:2::186'),(791,2,'2001:db8:2::187'),(792,2,'2001:db8:2::188'),(793,2,'2001:db8:2::189'),(794,2,'2001:db8:2::18a'),(795,2,'2001:db8:2::18b'),(796,2,'2001:db8:2::18c'),(797,2,'2001:db8:2::18d'),(798,2,'2001:db8:2::18e'),(799,2,'2001:db8:2::18f'),(425,2,'2001:db8:2::19'),(800,2,'2001:db8:2::190'),(426,2,'2001:db8:2::1a'),(427,2,'2001:db8:2::1b'),(428,2,'2001:db8:2::1c'),(429,2,'2001:db8:2::1d'),(430,2,'2001:db8:2::1e'),(431,2,'2001:db8:2::1f'),(402,2,'2001:db8:2::2'),(432,2,'2001:db8:2::20'),(433,2,'2001:db8:2::21'),(434,2,'2001:db8:2::22'),(435,2,'2001:db8:2::23'),(436,2,'2001:db8:2::24'),(437,2,'2001:db8:2::25'),(438,2,'2001:db8:2::26'),(439,2,'2001:db8:2::27'),(440,2,'2001:db8:2::28'),(441,2,'2001:db8:2::29'),(442,2,'2001:db8:2::2a'),(443,2,'2001:db8:2::2b'),(444,2,'2001:db8:2::2c'),(445,2,'2001:db8:2::2d'),(446,2,'2001:db8:2::2e'),(447,2,'2001:db8:2::2f'),(403,2,'2001:db8:2::3'),(448,2,'2001:db8:2::30'),(449,2,'2001:db8:2::31'),(450,2,'2001:db8:2::32'),(451,2,'2001:db8:2::33'),(452,2,'2001:db8:2::34'),(453,2,'2001:db8:2::35'),(454,2,'2001:db8:2::36'),(455,2,'2001:db8:2::37'),(456,2,'2001:db8:2::38'),(457,2,'2001:db8:2::39'),(458,2,'2001:db8:2::3a'),(459,2,'2001:db8:2::3b'),(460,2,'2001:db8:2::3c'),(461,2,'2001:db8:2::3d'),(462,2,'2001:db8:2::3e'),(463,2,'2001:db8:2::3f'),(404,2,'2001:db8:2::4'),(464,2,'2001:db8:2::40'),(465,2,'2001:db8:2::41'),(466,2,'2001:db8:2::42'),(467,2,'2001:db8:2::43'),(468,2,'2001:db8:2::44'),(469,2,'2001:db8:2::45'),(470,2,'2001:db8:2::46'),(471,2,'2001:db8:2::47'),(472,2,'2001:db8:2::48'),(473,2,'2001:db8:2::49'),(474,2,'2001:db8:2::4a'),(475,2,'2001:db8:2::4b'),(476,2,'2001:db8:2::4c'),(477,2,'2001:db8:2::4d'),(478,2,'2001:db8:2::4e'),(479,2,'2001:db8:2::4f'),(405,2,'2001:db8:2::5'),(480,2,'2001:db8:2::50'),(481,2,'2001:db8:2::51'),(482,2,'2001:db8:2::52'),(483,2,'2001:db8:2::53'),(484,2,'2001:db8:2::54'),(485,2,'2001:db8:2::55'),(486,2,'2001:db8:2::56'),(487,2,'2001:db8:2::57'),(488,2,'2001:db8:2::58'),(489,2,'2001:db8:2::59'),(490,2,'2001:db8:2::5a'),(491,2,'2001:db8:2::5b'),(492,2,'2001:db8:2::5c'),(493,2,'2001:db8:2::5d'),(494,2,'2001:db8:2::5e'),(495,2,'2001:db8:2::5f'),(406,2,'2001:db8:2::6'),(496,2,'2001:db8:2::60'),(497,2,'2001:db8:2::61'),(498,2,'2001:db8:2::62'),(499,2,'2001:db8:2::63'),(500,2,'2001:db8:2::64'),(501,2,'2001:db8:2::65'),(502,2,'2001:db8:2::66'),(503,2,'2001:db8:2::67'),(504,2,'2001:db8:2::68'),(505,2,'2001:db8:2::69'),(506,2,'2001:db8:2::6a'),(507,2,'2001:db8:2::6b'),(508,2,'2001:db8:2::6c'),(509,2,'2001:db8:2::6d'),(510,2,'2001:db8:2::6e'),(511,2,'2001:db8:2::6f'),(407,2,'2001:db8:2::7'),(512,2,'2001:db8:2::70'),(513,2,'2001:db8:2::71'),(514,2,'2001:db8:2::72'),(515,2,'2001:db8:2::73'),(516,2,'2001:db8:2::74'),(517,2,'2001:db8:2::75'),(518,2,'2001:db8:2::76'),(519,2,'2001:db8:2::77'),(520,2,'2001:db8:2::78'),(521,2,'2001:db8:2::79'),(522,2,'2001:db8:2::7a'),(523,2,'2001:db8:2::7b'),(524,2,'2001:db8:2::7c'),(525,2,'2001:db8:2::7d'),(526,2,'2001:db8:2::7e'),(527,2,'2001:db8:2::7f'),(408,2,'2001:db8:2::8'),(528,2,'2001:db8:2::80'),(529,2,'2001:db8:2::81'),(530,2,'2001:db8:2::82'),(531,2,'2001:db8:2::83'),(532,2,'2001:db8:2::84'),(533,2,'2001:db8:2::85'),(534,2,'2001:db8:2::86'),(535,2,'2001:db8:2::87'),(536,2,'2001:db8:2::88'),(537,2,'2001:db8:2::89'),(538,2,'2001:db8:2::8a'),(539,2,'2001:db8:2::8b'),(540,2,'2001:db8:2::8c'),(541,2,'2001:db8:2::8d'),(542,2,'2001:db8:2::8e'),(543,2,'2001:db8:2::8f'),(409,2,'2001:db8:2::9'),(544,2,'2001:db8:2::90'),(545,2,'2001:db8:2::91'),(546,2,'2001:db8:2::92'),(547,2,'2001:db8:2::93'),(548,2,'2001:db8:2::94'),(549,2,'2001:db8:2::95'),(550,2,'2001:db8:2::96'),(551,2,'2001:db8:2::97'),(552,2,'2001:db8:2::98'),(553,2,'2001:db8:2::99'),(554,2,'2001:db8:2::9a'),(555,2,'2001:db8:2::9b'),(556,2,'2001:db8:2::9c'),(557,2,'2001:db8:2::9d'),(558,2,'2001:db8:2::9e'),(559,2,'2001:db8:2::9f'),(410,2,'2001:db8:2::a'),(560,2,'2001:db8:2::a0'),(561,2,'2001:db8:2::a1'),(562,2,'2001:db8:2::a2'),(563,2,'2001:db8:2::a3'),(564,2,'2001:db8:2::a4'),(565,2,'2001:db8:2::a5'),(566,2,'2001:db8:2::a6'),(567,2,'2001:db8:2::a7'),(568,2,'2001:db8:2::a8'),(569,2,'2001:db8:2::a9'),(570,2,'2001:db8:2::aa'),(571,2,'2001:db8:2::ab'),(572,2,'2001:db8:2::ac'),(573,2,'2001:db8:2::ad'),(574,2,'2001:db8:2::ae'),(575,2,'2001:db8:2::af'),(411,2,'2001:db8:2::b'),(576,2,'2001:db8:2::b0'),(577,2,'2001:db8:2::b1'),(578,2,'2001:db8:2::b2'),(579,2,'2001:db8:2::b3'),(580,2,'2001:db8:2::b4'),(581,2,'2001:db8:2::b5'),(582,2,'2001:db8:2::b6'),(583,2,'2001:db8:2::b7'),(584,2,'2001:db8:2::b8'),(585,2,'2001:db8:2::b9'),(586,2,'2001:db8:2::ba'),(587,2,'2001:db8:2::bb'),(588,2,'2001:db8:2::bc'),(589,2,'2001:db8:2::bd'),(590,2,'2001:db8:2::be'),(591,2,'2001:db8:2::bf'),(412,2,'2001:db8:2::c'),(592,2,'2001:db8:2::c0'),(593,2,'2001:db8:2::c1'),(594,2,'2001:db8:2::c2'),(595,2,'2001:db8:2::c3'),(596,2,'2001:db8:2::c4'),(597,2,'2001:db8:2::c5'),(598,2,'2001:db8:2::c6'),(599,2,'2001:db8:2::c7'),(600,2,'2001:db8:2::c8'),(601,2,'2001:db8:2::c9'),(602,2,'2001:db8:2::ca'),(603,2,'2001:db8:2::cb'),(604,2,'2001:db8:2::cc'),(605,2,'2001:db8:2::cd'),(606,2,'2001:db8:2::ce'),(607,2,'2001:db8:2::cf'),(413,2,'2001:db8:2::d'),(608,2,'2001:db8:2::d0'),(609,2,'2001:db8:2::d1'),(610,2,'2001:db8:2::d2'),(611,2,'2001:db8:2::d3'),(612,2,'2001:db8:2::d4'),(613,2,'2001:db8:2::d5'),(614,2,'2001:db8:2::d6'),(615,2,'2001:db8:2::d7'),(616,2,'2001:db8:2::d8'),(617,2,'2001:db8:2::d9'),(618,2,'2001:db8:2::da'),(619,2,'2001:db8:2::db'),(620,2,'2001:db8:2::dc'),(621,2,'2001:db8:2::dd'),(622,2,'2001:db8:2::de'),(623,2,'2001:db8:2::df'),(414,2,'2001:db8:2::e'),(624,2,'2001:db8:2::e0'),(625,2,'2001:db8:2::e1'),(626,2,'2001:db8:2::e2'),(627,2,'2001:db8:2::e3'),(628,2,'2001:db8:2::e4'),(629,2,'2001:db8:2::e5'),(630,2,'2001:db8:2::e6'),(631,2,'2001:db8:2::e7'),(632,2,'2001:db8:2::e8'),(633,2,'2001:db8:2::e9'),(634,2,'2001:db8:2::ea'),(635,2,'2001:db8:2::eb'),(636,2,'2001:db8:2::ec'),(637,2,'2001:db8:2::ed'),(638,2,'2001:db8:2::ee'),(639,2,'2001:db8:2::ef'),(415,2,'2001:db8:2::f'),(640,2,'2001:db8:2::f0'),(641,2,'2001:db8:2::f1'),(642,2,'2001:db8:2::f2'),(643,2,'2001:db8:2::f3'),(644,2,'2001:db8:2::f4'),(645,2,'2001:db8:2::f5'),(646,2,'2001:db8:2::f6'),(647,2,'2001:db8:2::f7'),(648,2,'2001:db8:2::f8'),(649,2,'2001:db8:2::f9'),(650,2,'2001:db8:2::fa'),(651,2,'2001:db8:2::fb'),(652,2,'2001:db8:2::fc'),(653,2,'2001:db8:2::fd'),(654,2,'2001:db8:2::fe'),(655,2,'2001:db8:2::ff');
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
  PRIMARY KEY (`id`),
  UNIQUE KEY `custasn` (`asn`,`protocol`,`customer_id`),
  KEY `IDX_87BFC5569395C3F3` (`customer_id`),
  CONSTRAINT `FK_87BFC5569395C3F3` FOREIGN KEY (`customer_id`) REFERENCES `cust` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=105 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `irrdb_asn`
--

LOCK TABLES `irrdb_asn` WRITE;
/*!40000 ALTER TABLE `irrdb_asn` DISABLE KEYS */;
INSERT INTO `irrdb_asn` VALUES (1,4,112,4,'2014-01-06 14:42:49',NULL),(2,4,112,6,'2014-01-06 14:42:50',NULL),(3,2,112,4,'2014-01-06 14:42:50',NULL),(4,2,1213,4,'2014-01-06 14:42:50',NULL),(5,2,1921,4,'2014-01-06 14:42:50',NULL),(6,2,2128,4,'2014-01-06 14:42:50',NULL),(7,2,2850,4,'2014-01-06 14:42:50',NULL),(8,2,42310,4,'2014-01-06 14:42:50',NULL),(9,2,112,6,'2014-01-06 14:42:51',NULL),(10,2,1213,6,'2014-01-06 14:42:51',NULL),(11,2,1921,6,'2014-01-06 14:42:51',NULL),(12,2,2128,6,'2014-01-06 14:42:51',NULL),(13,2,2850,6,'2014-01-06 14:42:51',NULL),(14,2,42310,6,'2014-01-06 14:42:51',NULL),(15,5,11521,4,'2014-01-06 14:42:51',NULL),(16,5,25441,4,'2014-01-06 14:42:51',NULL),(17,5,34317,4,'2014-01-06 14:42:51',NULL),(18,5,35272,4,'2014-01-06 14:42:51',NULL),(19,5,39064,4,'2014-01-06 14:42:51',NULL),(20,5,43178,4,'2014-01-06 14:42:51',NULL),(21,5,43610,4,'2014-01-06 14:42:51',NULL),(22,5,47615,4,'2014-01-06 14:42:51',NULL),(23,5,48342,4,'2014-01-06 14:42:51',NULL),(24,5,49573,4,'2014-01-06 14:42:51',NULL),(25,5,197853,4,'2014-01-06 14:42:51',NULL),(26,5,197904,4,'2014-01-06 14:42:51',NULL),(27,5,11521,6,'2014-01-06 14:42:52',NULL),(28,5,25441,6,'2014-01-06 14:42:52',NULL),(29,5,34317,6,'2014-01-06 14:42:52',NULL),(30,5,35272,6,'2014-01-06 14:42:52',NULL),(31,5,39064,6,'2014-01-06 14:42:52',NULL),(32,5,43178,6,'2014-01-06 14:42:52',NULL),(33,5,43610,6,'2014-01-06 14:42:52',NULL),(34,5,47615,6,'2014-01-06 14:42:52',NULL),(35,5,48342,6,'2014-01-06 14:42:52',NULL),(36,5,49573,6,'2014-01-06 14:42:52',NULL),(37,5,197853,6,'2014-01-06 14:42:52',NULL),(38,5,197904,6,'2014-01-06 14:42:52',NULL),(39,3,27,4,'2014-01-06 14:42:52',NULL),(40,3,42,4,'2014-01-06 14:42:52',NULL),(41,3,187,4,'2014-01-06 14:42:52',NULL),(42,3,297,4,'2014-01-06 14:42:52',NULL),(43,3,715,4,'2014-01-06 14:42:52',NULL),(44,3,3856,4,'2014-01-06 14:42:52',NULL),(45,3,7251,4,'2014-01-06 14:42:52',NULL),(46,3,13202,4,'2014-01-06 14:42:52',NULL),(47,3,16327,4,'2014-01-06 14:42:52',NULL),(48,3,16668,4,'2014-01-06 14:42:52',NULL),(49,3,16686,4,'2014-01-06 14:42:52',NULL),(50,3,20144,4,'2014-01-06 14:42:52',NULL),(51,3,20539,4,'2014-01-06 14:42:52',NULL),(52,3,21312,4,'2014-01-06 14:42:52',NULL),(53,3,24999,4,'2014-01-06 14:42:52',NULL),(54,3,27678,4,'2014-01-06 14:42:52',NULL),(55,3,32978,4,'2014-01-06 14:42:52',NULL),(56,3,32979,4,'2014-01-06 14:42:52',NULL),(57,3,35160,4,'2014-01-06 14:42:52',NULL),(58,3,38052,4,'2014-01-06 14:42:52',NULL),(59,3,44876,4,'2014-01-06 14:42:52',NULL),(60,3,45170,4,'2014-01-06 14:42:52',NULL),(61,3,45494,4,'2014-01-06 14:42:52',NULL),(62,3,48582,4,'2014-01-06 14:42:52',NULL),(63,3,48892,4,'2014-01-06 14:42:52',NULL),(64,3,50843,4,'2014-01-06 14:42:52',NULL),(65,3,51874,4,'2014-01-06 14:42:52',NULL),(66,3,52234,4,'2014-01-06 14:42:52',NULL),(67,3,52306,4,'2014-01-06 14:42:52',NULL),(68,3,54145,4,'2014-01-06 14:42:52',NULL),(69,3,59464,4,'2014-01-06 14:42:52',NULL),(70,3,60313,4,'2014-01-06 14:42:52',NULL),(71,3,197058,4,'2014-01-06 14:42:52',NULL),(72,3,27,6,'2014-01-06 14:42:52',NULL),(73,3,42,6,'2014-01-06 14:42:52',NULL),(74,3,187,6,'2014-01-06 14:42:52',NULL),(75,3,297,6,'2014-01-06 14:42:52',NULL),(76,3,715,6,'2014-01-06 14:42:52',NULL),(77,3,3856,6,'2014-01-06 14:42:52',NULL),(78,3,7251,6,'2014-01-06 14:42:52',NULL),(79,3,13202,6,'2014-01-06 14:42:52',NULL),(80,3,16327,6,'2014-01-06 14:42:52',NULL),(81,3,16668,6,'2014-01-06 14:42:52',NULL),(82,3,16686,6,'2014-01-06 14:42:52',NULL),(83,3,20144,6,'2014-01-06 14:42:52',NULL),(84,3,20539,6,'2014-01-06 14:42:52',NULL),(85,3,21312,6,'2014-01-06 14:42:52',NULL),(86,3,24999,6,'2014-01-06 14:42:52',NULL),(87,3,27678,6,'2014-01-06 14:42:52',NULL),(88,3,32978,6,'2014-01-06 14:42:52',NULL),(89,3,32979,6,'2014-01-06 14:42:52',NULL),(90,3,35160,6,'2014-01-06 14:42:52',NULL),(91,3,38052,6,'2014-01-06 14:42:52',NULL),(92,3,44876,6,'2014-01-06 14:42:52',NULL),(93,3,45170,6,'2014-01-06 14:42:52',NULL),(94,3,45494,6,'2014-01-06 14:42:52',NULL),(95,3,48582,6,'2014-01-06 14:42:52',NULL),(96,3,48892,6,'2014-01-06 14:42:52',NULL),(97,3,50843,6,'2014-01-06 14:42:52',NULL),(98,3,51874,6,'2014-01-06 14:42:52',NULL),(99,3,52234,6,'2014-01-06 14:42:52',NULL),(100,3,52306,6,'2014-01-06 14:42:52',NULL),(101,3,54145,6,'2014-01-06 14:42:52',NULL),(102,3,59464,6,'2014-01-06 14:42:52',NULL),(103,3,60313,6,'2014-01-06 14:42:52',NULL),(104,3,197058,6,'2014-01-06 14:42:52',NULL);
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
  PRIMARY KEY (`id`),
  UNIQUE KEY `custprefix` (`prefix`,`protocol`,`customer_id`),
  KEY `IDX_FE73E77C9395C3F3` (`customer_id`),
  CONSTRAINT `FK_FE73E77C9395C3F3` FOREIGN KEY (`customer_id`) REFERENCES `cust` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=649 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `irrdb_prefix`
--

LOCK TABLES `irrdb_prefix` WRITE;
/*!40000 ALTER TABLE `irrdb_prefix` DISABLE KEYS */;
INSERT INTO `irrdb_prefix` VALUES (1,4,'192.175.48.0/24',4,'2014-01-06 14:42:30','2014-01-06 14:42:30'),(2,2,'4.53.84.128/26',4,'2014-01-06 14:42:31','2014-01-06 14:42:31'),(3,2,'4.53.146.192/26',4,'2014-01-06 14:42:31','2014-01-06 14:42:31'),(4,2,'77.72.72.0/21',4,'2014-01-06 14:42:31','2014-01-06 14:42:31'),(5,2,'87.32.0.0/12',4,'2014-01-06 14:42:31','2014-01-06 14:42:31'),(6,2,'91.123.224.0/20',4,'2014-01-06 14:42:31','2014-01-06 14:42:31'),(7,2,'134.226.0.0/16',4,'2014-01-06 14:42:31','2014-01-06 14:42:31'),(8,2,'136.201.0.0/16',4,'2014-01-06 14:42:31','2014-01-06 14:42:31'),(9,2,'136.206.0.0/16',4,'2014-01-06 14:42:31','2014-01-06 14:42:31'),(10,2,'137.43.0.0/16',4,'2014-01-06 14:42:31','2014-01-06 14:42:31'),(11,2,'140.203.0.0/16',4,'2014-01-06 14:42:31','2014-01-06 14:42:31'),(12,2,'143.239.0.0/16',4,'2014-01-06 14:42:31','2014-01-06 14:42:31'),(13,2,'147.252.0.0/16',4,'2014-01-06 14:42:31','2014-01-06 14:42:31'),(14,2,'149.153.0.0/16',4,'2014-01-06 14:42:31','2014-01-06 14:42:31'),(15,2,'149.157.0.0/16',4,'2014-01-06 14:42:31','2014-01-06 14:42:31'),(16,2,'157.190.0.0/16',4,'2014-01-06 14:42:31','2014-01-06 14:42:31'),(17,2,'160.6.0.0/16',4,'2014-01-06 14:42:31','2014-01-06 14:42:31'),(18,2,'176.97.158.0/24',4,'2014-01-06 14:42:31','2014-01-06 14:42:31'),(19,2,'192.174.68.0/24',4,'2014-01-06 14:42:31','2014-01-06 14:42:31'),(20,2,'192.175.48.0/24',4,'2014-01-06 14:42:31','2014-01-06 14:42:31'),(21,2,'193.1.0.0/16',4,'2014-01-06 14:42:31','2014-01-06 14:42:31'),(22,2,'193.242.111.0/24',4,'2014-01-06 14:42:31','2014-01-06 14:42:31'),(23,2,'194.0.24.0/24',4,'2014-01-06 14:42:31','2014-01-06 14:42:31'),(24,2,'194.0.25.0/24',4,'2014-01-06 14:42:31','2014-01-06 14:42:31'),(25,2,'194.0.26.0/24',4,'2014-01-06 14:42:31','2014-01-06 14:42:31'),(26,2,'194.88.240.0/23',4,'2014-01-06 14:42:31','2014-01-06 14:42:31'),(27,2,'212.3.242.128/26',4,'2014-01-06 14:42:31','2014-01-06 14:42:31'),(28,2,'2001:678:20::/48',6,'2014-01-06 14:42:32','2014-01-06 14:42:32'),(29,2,'2001:678:24::/48',6,'2014-01-06 14:42:32','2014-01-06 14:42:32'),(30,2,'2001:67c:1bc::/48',6,'2014-01-06 14:42:32','2014-01-06 14:42:32'),(31,2,'2001:67c:10b8::/48',6,'2014-01-06 14:42:32','2014-01-06 14:42:32'),(32,2,'2001:67c:10e0::/48',6,'2014-01-06 14:42:32','2014-01-06 14:42:32'),(33,2,'2001:770::/32',6,'2014-01-06 14:42:32','2014-01-06 14:42:32'),(34,2,'2001:7f8:18::/48',6,'2014-01-06 14:42:32','2014-01-06 14:42:32'),(35,2,'2001:1900:2205::/48',6,'2014-01-06 14:42:32','2014-01-06 14:42:32'),(36,2,'2001:1900:2206::/48',6,'2014-01-06 14:42:32','2014-01-06 14:42:32'),(37,2,'2620:4f:8000::/48',6,'2014-01-06 14:42:32','2014-01-06 14:42:32'),(38,2,'2a01:4b0::/32',6,'2014-01-06 14:42:32','2014-01-06 14:42:32'),(39,5,'31.169.96.0/21',4,'2014-01-06 14:42:33','2014-01-06 14:42:33'),(40,5,'62.231.32.0/19',4,'2014-01-06 14:42:33','2014-01-06 14:42:33'),(41,5,'78.135.128.0/17',4,'2014-01-06 14:42:33','2014-01-06 14:42:33'),(42,5,'83.141.64.0/18',4,'2014-01-06 14:42:33','2014-01-06 14:42:33'),(43,5,'85.134.128.0/17',4,'2014-01-06 14:42:33','2014-01-06 14:42:33'),(44,5,'87.192.0.0/16',4,'2014-01-06 14:42:33','2014-01-06 14:42:33'),(45,5,'87.232.0.0/16',4,'2014-01-06 14:42:33','2014-01-06 14:42:33'),(46,5,'89.28.176.0/21',4,'2014-01-06 14:42:33','2014-01-06 14:42:33'),(47,5,'89.124.0.0/14',4,'2014-01-06 14:42:33','2014-01-06 14:42:33'),(48,5,'89.124.0.0/15',4,'2014-01-06 14:42:33','2014-01-06 14:42:33'),(49,5,'89.125.0.0/16',4,'2014-01-06 14:42:33','2014-01-06 14:42:33'),(50,5,'89.126.0.0/16',4,'2014-01-06 14:42:33','2014-01-06 14:42:33'),(51,5,'89.126.0.0/19',4,'2014-01-06 14:42:33','2014-01-06 14:42:33'),(52,5,'89.126.0.0/20',4,'2014-01-06 14:42:33','2014-01-06 14:42:33'),(53,5,'89.126.32.0/19',4,'2014-01-06 14:42:33','2014-01-06 14:42:33'),(54,5,'89.126.64.0/19',4,'2014-01-06 14:42:33','2014-01-06 14:42:33'),(55,5,'89.126.96.0/19',4,'2014-01-06 14:42:33','2014-01-06 14:42:33'),(56,5,'91.194.126.0/23',4,'2014-01-06 14:42:33','2014-01-06 14:42:33'),(57,5,'91.194.126.0/24',4,'2014-01-06 14:42:33','2014-01-06 14:42:33'),(58,5,'91.194.127.0/24',4,'2014-01-06 14:42:33','2014-01-06 14:42:33'),(59,5,'91.209.106.0/24',4,'2014-01-06 14:42:33','2014-01-06 14:42:33'),(60,5,'91.209.106.0/25',4,'2014-01-06 14:42:33','2014-01-06 14:42:33'),(61,5,'91.209.106.128/25',4,'2014-01-06 14:42:33','2014-01-06 14:42:33'),(62,5,'91.213.49.0/24',4,'2014-01-06 14:42:33','2014-01-06 14:42:33'),(63,5,'91.220.224.0/24',4,'2014-01-06 14:42:33','2014-01-06 14:42:33'),(64,5,'141.105.112.0/21',4,'2014-01-06 14:42:33','2014-01-06 14:42:33'),(65,5,'176.52.216.0/21',4,'2014-01-06 14:42:33','2014-01-06 14:42:33'),(66,5,'195.5.172.0/24',4,'2014-01-06 14:42:33','2014-01-06 14:42:33'),(67,5,'195.60.166.0/23',4,'2014-01-06 14:42:33','2014-01-06 14:42:33'),(68,5,'216.245.44.0/24',4,'2014-01-06 14:42:33','2014-01-06 14:42:33'),(69,5,'2001:67c:20::/64',6,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(70,5,'2001:67c:338::/48',6,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(71,5,'2001:4d68::/32',6,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(72,5,'2a01:268::/32',6,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(73,5,'2a01:8f80::/32',6,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(74,3,'31.135.128.0/19',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(75,3,'31.135.128.0/21',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(76,3,'31.135.136.0/21',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(77,3,'31.135.144.0/22',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(78,3,'31.135.148.0/22',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(79,3,'31.135.152.0/22',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(80,3,'31.135.152.0/23',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(81,3,'31.135.154.0/23',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(82,3,'36.0.4.0/22',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(83,3,'63.246.32.0/20',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(84,3,'64.68.192.0/20',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(85,3,'64.68.192.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(86,3,'64.68.193.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(87,3,'64.68.194.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(88,3,'64.68.195.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(89,3,'64.68.196.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(90,3,'64.78.200.0/21',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(91,3,'64.185.240.0/20',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(92,3,'65.22.4.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(93,3,'65.22.5.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(94,3,'65.22.19.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(95,3,'65.22.23.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(96,3,'65.22.27.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(97,3,'65.22.31.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(98,3,'65.22.35.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(99,3,'65.22.39.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(100,3,'65.22.47.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(101,3,'65.22.51.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(102,3,'65.22.55.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(103,3,'65.22.59.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(104,3,'65.22.63.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(105,3,'65.22.67.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(106,3,'65.22.71.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(107,3,'65.22.79.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(108,3,'65.22.83.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(109,3,'65.22.87.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(110,3,'65.22.91.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(111,3,'65.22.95.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(112,3,'65.22.99.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(113,3,'65.22.103.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(114,3,'65.22.107.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(115,3,'65.22.111.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(116,3,'65.22.115.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(117,3,'65.22.119.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(118,3,'65.22.123.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(119,3,'65.22.127.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(120,3,'65.22.131.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(121,3,'65.22.135.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(122,3,'65.22.139.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(123,3,'65.22.143.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(124,3,'65.22.147.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(125,3,'65.22.151.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(126,3,'65.22.155.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(127,3,'65.22.159.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(128,3,'65.22.163.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(129,3,'65.22.171.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(130,3,'65.22.175.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(131,3,'65.22.179.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(132,3,'65.22.183.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(133,3,'65.22.187.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(134,3,'65.22.191.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(135,3,'65.22.195.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(136,3,'65.22.199.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(137,3,'65.22.203.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(138,3,'65.22.207.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(139,3,'65.22.211.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(140,3,'65.22.215.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(141,3,'65.22.219.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(142,3,'65.22.223.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(143,3,'65.22.227.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(144,3,'65.22.231.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(145,3,'65.22.235.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(146,3,'65.22.239.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(147,3,'65.22.243.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(148,3,'65.22.247.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(149,3,'66.96.112.0/20',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(150,3,'66.102.32.0/20',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(151,3,'66.175.104.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(152,3,'66.185.112.0/20',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(153,3,'66.225.199.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(154,3,'66.225.200.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(155,3,'66.225.201.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(156,3,'67.21.37.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(157,3,'67.22.112.0/21',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(158,3,'67.158.48.0/20',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(159,3,'68.65.112.0/21',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(160,3,'68.65.126.0/23',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(161,3,'68.65.126.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(162,3,'68.65.127.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(163,3,'69.166.10.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(164,3,'69.166.12.0/22',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(165,3,'70.40.0.0/21',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(166,3,'70.40.8.0/21',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(167,3,'72.0.48.0/20',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(168,3,'72.0.48.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(169,3,'72.0.49.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(170,3,'72.0.50.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(171,3,'72.0.51.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(172,3,'72.0.52.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(173,3,'72.0.53.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(174,3,'72.0.54.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(175,3,'72.0.55.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(176,3,'72.0.56.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(177,3,'72.0.57.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(178,3,'72.0.58.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(179,3,'72.0.59.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(180,3,'72.0.60.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(181,3,'72.0.61.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(182,3,'72.0.62.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(183,3,'72.0.63.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(184,3,'72.42.112.0/20',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(185,3,'72.42.112.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(186,3,'72.42.113.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(187,3,'72.42.114.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(188,3,'72.42.115.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(189,3,'72.42.116.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(190,3,'72.42.117.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(191,3,'72.42.118.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(192,3,'72.42.119.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(193,3,'72.42.120.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(194,3,'72.42.121.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(195,3,'72.42.122.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(196,3,'72.42.123.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(197,3,'72.42.124.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(198,3,'72.42.125.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(199,3,'72.42.126.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(200,3,'72.42.127.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(201,3,'74.63.16.0/20',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(202,3,'74.63.16.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(203,3,'74.63.17.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(204,3,'74.63.18.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(205,3,'74.63.19.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(206,3,'74.63.20.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(207,3,'74.63.21.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(208,3,'74.63.22.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(209,3,'74.63.23.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(210,3,'74.63.24.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(211,3,'74.63.25.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(212,3,'74.63.26.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(213,3,'74.63.27.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(214,3,'74.80.64.0/18',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(215,3,'74.80.64.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(216,3,'74.80.65.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(217,3,'74.80.66.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(218,3,'74.80.67.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(219,3,'74.80.68.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(220,3,'74.80.69.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(221,3,'74.80.70.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(222,3,'74.80.71.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(223,3,'74.80.72.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(224,3,'74.80.73.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(225,3,'74.80.74.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(226,3,'74.80.75.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(227,3,'74.80.76.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(228,3,'74.80.77.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(229,3,'74.80.78.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(230,3,'74.80.79.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(231,3,'74.80.80.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(232,3,'74.80.81.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(233,3,'74.80.82.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(234,3,'74.80.83.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(235,3,'74.80.84.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(236,3,'74.80.85.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(237,3,'74.80.86.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(238,3,'74.80.87.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(239,3,'74.80.88.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(240,3,'74.80.89.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(241,3,'74.80.90.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(242,3,'74.80.91.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(243,3,'74.80.92.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(244,3,'74.80.93.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(245,3,'74.80.94.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(246,3,'74.80.95.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(247,3,'74.80.96.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(248,3,'74.80.97.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(249,3,'74.80.98.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(250,3,'74.80.99.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(251,3,'74.80.100.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(252,3,'74.80.101.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(253,3,'74.80.102.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(254,3,'74.80.103.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(255,3,'74.80.104.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(256,3,'74.80.105.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(257,3,'74.80.106.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(258,3,'74.80.107.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(259,3,'74.80.108.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(260,3,'74.80.109.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(261,3,'74.80.110.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(262,3,'74.80.111.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(263,3,'74.80.112.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(264,3,'74.80.113.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(265,3,'74.80.114.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(266,3,'74.80.115.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(267,3,'74.80.116.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(268,3,'74.80.117.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(269,3,'74.80.118.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(270,3,'74.80.119.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(271,3,'74.80.120.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(272,3,'74.80.121.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(273,3,'74.80.122.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(274,3,'74.80.123.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(275,3,'74.80.124.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(276,3,'74.80.125.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(277,3,'74.80.126.0/23',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(278,3,'74.80.126.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(279,3,'74.80.127.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(280,3,'74.118.212.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(281,3,'74.118.213.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(282,3,'74.118.214.0/23',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(283,3,'75.127.16.0/20',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(284,3,'76.191.16.0/20',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(285,3,'89.19.120.0/21',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(286,3,'89.19.120.0/22',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(287,3,'89.19.124.0/23',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(288,3,'89.19.126.0/23',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(289,3,'91.201.224.0/22',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(290,3,'91.201.224.0/23',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(291,3,'91.201.224.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(292,3,'91.201.225.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(293,3,'91.201.226.0/23',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(294,3,'91.201.226.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(295,3,'91.201.227.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(296,3,'91.209.1.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(297,3,'91.209.193.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(298,3,'91.222.16.0/22',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(299,3,'91.222.40.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(300,3,'91.222.41.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(301,3,'91.222.42.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(302,3,'91.222.43.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(303,3,'91.241.93.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(304,3,'93.95.24.0/21',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(305,3,'93.95.24.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(306,3,'93.95.25.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(307,3,'93.95.26.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(308,3,'93.171.128.0/22',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(309,3,'95.47.163.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(310,3,'101.251.4.0/22',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(311,3,'114.69.222.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(312,3,'128.8.0.0/16',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(313,3,'128.161.0.0/16',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(314,3,'129.2.0.0/16',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(315,3,'130.135.0.0/16',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(316,3,'130.167.0.0/16',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(317,3,'131.161.128.0/18',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(318,3,'131.182.0.0/16',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(319,3,'139.229.0.0/16',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(320,3,'140.169.0.0/16',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(321,3,'146.5.0.0/16',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(322,3,'146.58.0.0/16',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(323,3,'150.144.0.0/16',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(324,3,'156.154.43.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(325,3,'156.154.50.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(326,3,'156.154.59.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(327,3,'156.154.96.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(328,3,'156.154.99.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(329,3,'158.154.0.0/16',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(330,3,'169.222.0.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(331,3,'183.91.132.0/22',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(332,3,'192.5.41.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(333,3,'192.12.123.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(334,3,'192.42.70.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(335,3,'192.58.36.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(336,3,'192.67.83.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(337,3,'192.67.107.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(338,3,'192.67.108.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(339,3,'192.68.52.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(340,3,'192.68.148.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(341,3,'192.68.162.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(342,3,'192.70.244.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(343,3,'192.70.249.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(344,3,'192.77.80.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(345,3,'192.84.8.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(346,3,'192.88.124.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(347,3,'192.92.65.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(348,3,'192.92.90.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(349,3,'192.100.9.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(350,3,'192.100.10.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(351,3,'192.100.15.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(352,3,'192.101.148.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(353,3,'192.102.15.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(354,3,'192.102.219.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(355,3,'192.102.233.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(356,3,'192.102.234.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(357,3,'192.112.18.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(358,3,'192.112.223.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(359,3,'192.112.224.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(360,3,'192.124.20.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(361,3,'192.138.101.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(362,3,'192.138.172.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(363,3,'192.149.89.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(364,3,'192.149.104.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(365,3,'192.149.107.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(366,3,'192.149.133.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(367,3,'192.150.32.0/21',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(368,3,'192.153.157.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(369,3,'192.188.4.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(370,3,'192.203.230.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(371,3,'192.225.64.0/19',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(372,3,'192.243.0.0/20',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(373,3,'192.243.16.0/21',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(374,3,'193.29.206.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(375,3,'193.110.16.0/22',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(376,3,'193.110.16.0/23',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(377,3,'193.110.18.0/23',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(378,3,'193.111.240.0/22',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(379,3,'193.178.228.0/23',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(380,3,'193.178.228.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(381,3,'193.178.229.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(382,3,'194.0.12.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(383,3,'194.0.13.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(384,3,'194.0.14.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(385,3,'194.0.17.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(386,3,'194.0.27.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(387,3,'194.0.36.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(388,3,'194.0.42.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(389,3,'194.0.47.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(390,3,'194.28.144.0/22',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(391,3,'194.117.58.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(392,3,'194.117.60.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(393,3,'194.117.61.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(394,3,'194.117.62.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(395,3,'194.117.63.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(396,3,'194.146.180.0/22',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(397,3,'194.146.180.0/23',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(398,3,'194.146.180.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(399,3,'194.146.181.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(400,3,'194.146.182.0/23',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(401,3,'194.146.182.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(402,3,'194.146.183.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(403,3,'194.146.228.0/22',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(404,3,'194.146.228.0/23',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(405,3,'194.146.228.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(406,3,'194.146.229.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(407,3,'194.146.230.0/23',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(408,3,'194.146.230.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(409,3,'194.146.231.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(410,3,'194.153.148.0/23',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(411,3,'195.64.162.0/23',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(412,3,'195.64.162.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(413,3,'195.64.163.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(414,3,'195.82.138.0/23',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(415,3,'198.9.0.0/16',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(416,3,'198.49.1.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(417,3,'198.116.0.0/14',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(418,3,'198.120.0.0/14',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(419,3,'198.182.28.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(420,3,'198.182.31.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(421,3,'198.182.167.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(422,3,'199.4.137.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(423,3,'199.7.64.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(424,3,'199.7.77.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(425,3,'199.7.83.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(426,3,'199.7.86.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(427,3,'199.7.91.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(428,3,'199.7.94.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(429,3,'199.7.95.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(430,3,'199.43.132.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(431,3,'199.115.156.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(432,3,'199.115.157.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(433,3,'199.120.141.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(434,3,'199.120.142.0/23',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(435,3,'199.120.144.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(436,3,'199.182.32.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(437,3,'199.182.40.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(438,3,'199.184.181.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(439,3,'199.184.182.0/23',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(440,3,'199.184.184.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(441,3,'199.249.112.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(442,3,'199.249.113.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(443,3,'199.249.114.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(444,3,'199.249.115.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(445,3,'199.249.116.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(446,3,'199.249.117.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(447,3,'199.249.118.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(448,3,'199.249.119.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(449,3,'199.249.120.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(450,3,'199.249.121.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(451,3,'199.249.122.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(452,3,'199.249.123.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(453,3,'199.249.124.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(454,3,'199.249.125.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(455,3,'199.249.126.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(456,3,'199.249.127.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(457,3,'199.254.171.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(458,3,'200.1.121.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(459,3,'200.1.131.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(460,3,'200.7.4.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(461,3,'200.16.98.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(462,3,'202.6.102.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(463,3,'202.7.4.0/22',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(464,3,'202.52.0.0/23',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(465,3,'202.53.186.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(466,3,'202.53.191.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(467,3,'203.119.88.0/23',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(468,3,'204.14.112.0/21',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(469,3,'204.19.119.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(470,3,'204.26.57.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(471,3,'204.61.208.0/21',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(472,3,'204.61.208.0/22',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(473,3,'204.61.208.0/23',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(474,3,'204.61.210.0/23',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(475,3,'204.61.210.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(476,3,'204.61.212.0/23',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(477,3,'204.61.216.0/23',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(478,3,'204.194.22.0/23',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(479,3,'204.194.22.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(480,3,'204.194.23.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(481,3,'205.132.46.0/23',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(482,3,'205.207.155.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(483,3,'206.51.254.0/23',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(484,3,'206.108.113.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(485,3,'206.196.160.0/19',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(486,3,'206.220.228.0/22',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(487,3,'206.220.228.0/23',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(488,3,'206.220.230.0/23',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(489,3,'206.223.122.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(490,3,'207.34.5.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(491,3,'207.34.6.0/23',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(492,3,'208.15.19.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(493,3,'208.49.115.64/27',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(494,3,'208.67.88.0/22',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(495,3,'216.21.2.0/23',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(496,3,'2001:500:3::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(497,3,'2001:500:14::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(498,3,'2001:500:15::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(499,3,'2001:500:40::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(500,3,'2001:500:41::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(501,3,'2001:500:42::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(502,3,'2001:500:43::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(503,3,'2001:500:44::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(504,3,'2001:500:45::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(505,3,'2001:500:46::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(506,3,'2001:500:47::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(507,3,'2001:500:48::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(508,3,'2001:500:49::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(509,3,'2001:500:4a::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(510,3,'2001:500:4b::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(511,3,'2001:500:4c::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(512,3,'2001:500:4d::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(513,3,'2001:500:4e::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(514,3,'2001:500:4f::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(515,3,'2001:500:50::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(516,3,'2001:500:51::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(517,3,'2001:500:52::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(518,3,'2001:500:53::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(519,3,'2001:500:54::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(520,3,'2001:500:55::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(521,3,'2001:500:56::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(522,3,'2001:500:7d::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(523,3,'2001:500:83::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(524,3,'2001:500:8c::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(525,3,'2001:500:9c::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(526,3,'2001:500:9d::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(527,3,'2001:500:a4::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(528,3,'2001:500:a5::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(529,3,'2001:500:e0::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(530,3,'2001:500:e1::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(531,3,'2001:678:3::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(532,3,'2001:678:28::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(533,3,'2001:678:4c::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(534,3,'2001:678:60::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(535,3,'2001:678:78::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(536,3,'2001:678:94::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(537,3,'2001:dd8:7::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(538,3,'2001:1398:121::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(539,3,'2404:2c00::/32',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(540,3,'2620:0:870::/45',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(541,3,'2620:0:876::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(542,3,'2620:49::/44',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(543,3,'2620:49::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(544,3,'2620:49:a::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(545,3,'2620:49:b::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(546,3,'2620:95:8000::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(547,3,'2620:171::/40',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(548,3,'2620:171:f0::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(549,3,'2620:171:f1::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(550,3,'2620:171:f2::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(551,3,'2620:171:f3::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(552,3,'2620:171:f4::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(553,3,'2620:171:f5::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(554,3,'2620:171:f6::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(555,3,'2620:171:f7::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(556,3,'2620:171:f8::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(557,3,'2620:171:f9::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(558,3,'2620:171:a00::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(559,3,'2620:171:a01::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(560,3,'2620:171:a02::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(561,3,'2620:171:a03::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(562,3,'2620:171:a04::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(563,3,'2620:171:a05::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(564,3,'2620:171:a06::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(565,3,'2620:171:a07::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(566,3,'2620:171:a08::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(567,3,'2620:171:a09::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(568,3,'2620:171:a0a::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(569,3,'2620:171:a0b::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(570,3,'2620:171:a0c::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(571,3,'2620:171:a0d::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(572,3,'2620:171:a0e::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(573,3,'2620:171:a0f::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(574,3,'2620:171:ad0::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(575,3,'2620:171:d00::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(576,3,'2620:171:d01::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(577,3,'2620:171:d02::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(578,3,'2620:171:d03::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(579,3,'2620:171:d04::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(580,3,'2620:171:d05::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(581,3,'2620:171:d06::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(582,3,'2620:171:d07::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(583,3,'2620:171:d08::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(584,3,'2620:171:d09::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(585,3,'2620:171:d0a::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(586,3,'2620:171:d0b::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(587,3,'2620:171:d0c::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(588,3,'2620:171:d0d::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(589,3,'2620:171:d0e::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(590,3,'2620:171:d0f::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(591,3,'2620:171:dd0::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(592,3,'2a01:8840:4::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(593,3,'2a01:8840:5::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(594,3,'2a01:8840:15::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(595,3,'2a01:8840:19::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(596,3,'2a01:8840:1d::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(597,3,'2a01:8840:21::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(598,3,'2a01:8840:25::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(599,3,'2a01:8840:29::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(600,3,'2a01:8840:2d::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(601,3,'2a01:8840:31::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(602,3,'2a01:8840:35::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(603,3,'2a01:8840:39::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(604,3,'2a01:8840:3d::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(605,3,'2a01:8840:41::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(606,3,'2a01:8840:45::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(607,3,'2a01:8840:4d::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(608,3,'2a01:8840:51::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(609,3,'2a01:8840:55::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(610,3,'2a01:8840:59::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(611,3,'2a01:8840:5d::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(612,3,'2a01:8840:61::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(613,3,'2a01:8840:65::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(614,3,'2a01:8840:69::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(615,3,'2a01:8840:6d::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(616,3,'2a01:8840:71::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(617,3,'2a01:8840:75::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(618,3,'2a01:8840:79::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(619,3,'2a01:8840:7d::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(620,3,'2a01:8840:81::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(621,3,'2a01:8840:85::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(622,3,'2a01:8840:89::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(623,3,'2a01:8840:8d::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(624,3,'2a01:8840:91::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(625,3,'2a01:8840:95::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(626,3,'2a01:8840:99::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(627,3,'2a01:8840:9d::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(628,3,'2a01:8840:a1::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(629,3,'2a01:8840:a5::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(630,3,'2a01:8840:a9::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(631,3,'2a01:8840:ad::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(632,3,'2a01:8840:b1::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(633,3,'2a01:8840:b5::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(634,3,'2a01:8840:b9::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(635,3,'2a01:8840:bd::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(636,3,'2a01:8840:c1::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(637,3,'2a01:8840:c5::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(638,3,'2a01:8840:c9::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(639,3,'2a01:8840:cd::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(640,3,'2a01:8840:d1::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(641,3,'2a01:8840:d5::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(642,3,'2a01:8840:d9::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(643,3,'2a01:8840:dd::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(644,3,'2a01:8840:e1::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(645,3,'2a01:8840:e5::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(646,3,'2a01:8840:e9::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(647,3,'2a01:8840:ed::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(648,3,'2a01:8840:f1::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36');
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
  `protocol` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `source` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `notes` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `irrdbconfig`
--

LOCK TABLES `irrdbconfig` WRITE;
/*!40000 ALTER TABLE `irrdbconfig` DISABLE KEYS */;
INSERT INTO `irrdbconfig` VALUES (1,'whois.ripe.net','ripe','RIPE','RIPE Query from RIPE Database'),(2,'whois.radb.net','irrd','RADB','RADB Query from RADB Database'),(3,'whois.lacnic.net','ripe','LACNIC','LACNIC Query from LACNIC Database'),(4,'whois.afrinic.net','ripe','AFRINIC','AFRINIC Query from AFRINIC Database'),(5,'whois.apnic.net','ripe','APNIC','APNIC Query from APNIC Database'),(6,'rr.level3.net','ripe','LEVEL3','Level3 Query from Level3 Database'),(7,'whois.radb.net','irrd','ARIN','ARIN Query from RADB Database'),(8,'whois.radb.net','irrd','RADB,ARIN','RADB+ARIN Query from RADB Database'),(9,'whois.radb.net','irrd','ALTDB','ALTDB Query from RADB Database'),(10,'whois.radb.net','irrd','RADB,RIPE','RADB+RIPE Query from RADB Database'),(11,'whois.radb.net','irrd','RADB,APNIC,ARIN','RADB+APNIC+ARIN Query from RADB Database'),(12,'whois.radb.net','irrd','RIPE,ARIN','RIPE+ARIN Query from RADB Database'),(13,'whois.radb.net','irrd','RADB,RIPE,APNIC,ARIN','');
/*!40000 ALTER TABLE `irrdbconfig` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ixp`
--

DROP TABLE IF EXISTS `ixp`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ixp` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `shortname` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `address1` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `address2` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `address3` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `address4` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `country` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_FA4AB7F64082763` (`shortname`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ixp`
--

LOCK TABLES `ixp` WRITE;
/*!40000 ALTER TABLE `ixp` DISABLE KEYS */;
INSERT INTO `ixp` VALUES (1,'INEX','INEX','5 Somewhere','Somebourogh','Dublin','D4','IE');
/*!40000 ALTER TABLE `ixp` ENABLE KEYS */;
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
  `created` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mac_vlanint` (`mac`,`vlan_interface_id`),
  KEY `IDX_B9482E1D6AB5F82` (`vlan_interface_id`),
  CONSTRAINT `FK_B9482E1D6AB5F82` FOREIGN KEY (`vlan_interface_id`) REFERENCES `vlaninterface` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
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
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_5E9E89CB64082763` (`shortname`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `location`
--

LOCK TABLES `location` WRITE;
/*!40000 ALTER TABLE `location` DISABLE KEYS */;
INSERT INTO `location` VALUES (1,'Location 1','l1',NULL,'','','','','','','','',NULL,NULL,NULL);
/*!40000 ALTER TABLE `location` ENABLE KEYS */;
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
  `type` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `original_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `stored_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `uploaded_by` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `uploaded_at` datetime NOT NULL,
  `width` int NOT NULL,
  `height` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_9F54004F9395C3F3` (`customer_id`),
  CONSTRAINT `FK_9F54004F9395C3F3` FOREIGN KEY (`customer_id`) REFERENCES `cust` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
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
  PRIMARY KEY (`id`),
  KEY `IDX_42CD65F6BFDF15D5` (`virtualinterfaceid`),
  CONSTRAINT `FK_42CD65F6BFDF15D5` FOREIGN KEY (`virtualinterfaceid`) REFERENCES `virtualinterface` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
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
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'2014_10_12_100000_create_password_resets_table',1),(2,'2018_08_08_100000_create_telescope_entries_table',1),(3,'2019_03_25_211956_create_failed_jobs_table',1),(4,'2020_02_06_204556_create_docstore_directories',2),(5,'2020_02_06_204608_create_docstore_files',2),(6,'2020_02_06_204911_create_docstore_logs',2),(7,'2020_03_09_110945_create_docstore_customer_directories',3),(8,'2020_03_09_111505_create_docstore_customer_files',3);
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
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
  PRIMARY KEY (`id`),
  KEY `IDX_6A0AF167F48D6D0` (`vlanid`),
  CONSTRAINT `FK_6A0AF167F48D6D0` FOREIGN KEY (`vlanid`) REFERENCES `vlan` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
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
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_DAEC0140DAEC0140` (`oui`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
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
  `port_prefix` varchar(10) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `chargeable` int NOT NULL DEFAULT '0',
  `location_notes` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `u_position` int DEFAULT NULL,
  `mounted_at` smallint DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_79A52562D351EC` (`cabinet_id`),
  CONSTRAINT `FK_79A52562D351EC` FOREIGN KEY (`cabinet_id`) REFERENCES `cabinet` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
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
  `chargeable` int NOT NULL DEFAULT '0',
  `duplex_master_id` int DEFAULT NULL,
  `number` smallint NOT NULL,
  `colo_circuit_ref` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `ticket_ref` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `private_notes` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `owned_by` int NOT NULL DEFAULT '0',
  `loa_code` varchar(25) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `description` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `colo_billing_ref` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_4BE40BC2C1DA6A2A` (`switch_port_id`),
  KEY `IDX_4BE40BC2635D5D87` (`patch_panel_id`),
  KEY `IDX_4BE40BC29395C3F3` (`customer_id`),
  KEY `IDX_4BE40BC23838446` (`duplex_master_id`),
  CONSTRAINT `FK_4BE40BC23838446` FOREIGN KEY (`duplex_master_id`) REFERENCES `patch_panel_port` (`id`),
  CONSTRAINT `FK_4BE40BC2635D5D87` FOREIGN KEY (`patch_panel_id`) REFERENCES `patch_panel` (`id`),
  CONSTRAINT `FK_4BE40BC29395C3F3` FOREIGN KEY (`customer_id`) REFERENCES `cust` (`id`),
  CONSTRAINT `FK_4BE40BC2C1DA6A2A` FOREIGN KEY (`switch_port_id`) REFERENCES `switchport` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
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
  PRIMARY KEY (`id`),
  KEY `IDX_28089403B0F978FF` (`patch_panel_port_id`),
  CONSTRAINT `FK_28089403B0F978FF` FOREIGN KEY (`patch_panel_port_id`) REFERENCES `patch_panel_port` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
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
  PRIMARY KEY (`id`),
  KEY `IDX_CB80B54AB0F978FF` (`patch_panel_port_id`),
  KEY `IDX_CB80B54A3838446` (`duplex_master_id`),
  CONSTRAINT `FK_CB80B54A3838446` FOREIGN KEY (`duplex_master_id`) REFERENCES `patch_panel_port_history` (`id`),
  CONSTRAINT `FK_CB80B54AB0F978FF` FOREIGN KEY (`patch_panel_port_id`) REFERENCES `patch_panel_port` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
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
  PRIMARY KEY (`id`),
  KEY `IDX_206EAD4E6F461430` (`patch_panel_port_history_id`),
  CONSTRAINT `FK_206EAD4E6F461430` FOREIGN KEY (`patch_panel_port_history_id`) REFERENCES `patch_panel_port_history` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
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
  `created` datetime DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_35A72597DA0209B9` (`custid`),
  KEY `IDX_35A725974E5F9AFF` (`peerid`),
  CONSTRAINT `FK_35A725974E5F9AFF` FOREIGN KEY (`peerid`) REFERENCES `cust` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_35A72597DA0209B9` FOREIGN KEY (`custid`) REFERENCES `cust` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
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
  `notes` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `autoneg` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_5FFF4D60E5F6FACB` (`switchportid`),
  UNIQUE KEY `UNIQ_5FFF4D602E68AB8C` (`fanout_physical_interface_id`),
  KEY `IDX_5FFF4D60BFDF15D5` (`virtualinterfaceid`),
  CONSTRAINT `FK_5FFF4D602E68AB8C` FOREIGN KEY (`fanout_physical_interface_id`) REFERENCES `physicalinterface` (`id`),
  CONSTRAINT `FK_5FFF4D60BFDF15D5` FOREIGN KEY (`virtualinterfaceid`) REFERENCES `virtualinterface` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_5FFF4D60E5F6FACB` FOREIGN KEY (`switchportid`) REFERENCES `switchport` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `physicalinterface`
--

LOCK TABLES `physicalinterface` WRITE;
/*!40000 ALTER TABLE `physicalinterface` DISABLE KEYS */;
INSERT INTO `physicalinterface` VALUES (1,3,NULL,1,1,1000,'full','',1),(2,4,NULL,1,1,1000,'full','',1),(3,25,NULL,2,1,1000,'full',NULL,1),(4,8,NULL,3,1,100,'full',NULL,1),(5,6,NULL,4,1,10,'full',NULL,1),(6,30,NULL,5,1,10,'full',NULL,1),(7,9,NULL,6,1,1000,'full',NULL,1),(8,32,NULL,7,1,10000,'full',NULL,1),(9,18,NULL,8,1,1000,'full',NULL,1),(10,42,NULL,9,1,1000,'full',NULL,1),(11,19,NULL,8,1,1000,'full',NULL,1),(12,43,NULL,9,1,1000,'full',NULL,1),(13,27,NULL,10,4,1000,'full',NULL,1);
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
  `prefix` varchar(43) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
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
  `last_updated` datetime DEFAULT NULL,
  `rpki` tinyint(1) NOT NULL DEFAULT '0',
  `software_version` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `operating_system` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `operating_system_version` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `rfc1997_passthru` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_504FC9BE918020D9` (`handle`),
  KEY `IDX_504FC9BE8B4937A1` (`vlan_id`),
  CONSTRAINT `FK_504FC9BE8B4937A1` FOREIGN KEY (`vlan_id`) REFERENCES `vlan` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `routers`
--

LOCK TABLES `routers` WRITE;
/*!40000 ALTER TABLE `routers` DISABLE KEYS */;
INSERT INTO `routers` VALUES (1,1,'rc1-lan1-ipv4',4,2,'INEX LAN1 - Route Collector - IPv4','RC1 - LAN1 - IPv4','192.0.2.8','192.0.2.8',65500,'1','203.0.113.8','http://rc1-lan1-ipv4.mgmt.example.com/api',1,0,0,0,'api/v4/router/collector/bird/standard',0,NULL,0,NULL,NULL,NULL,0),(2,1,'rc1-lan1-ipv6',6,2,'INEX LAN1 - Route Collector - IPv6','RC1 - LAN1 - IPv6','192.0.2.8','2001:db8::8',65500,'1','2001:db8:0:0:2::8','http://rc1-lan1-ipv6.mgmt.example.com/api',1,0,0,0,'api/v4/router/collector/bird/standard',0,NULL,0,NULL,NULL,NULL,0),(3,2,'rc1-lan2-ipv4',4,2,'INEX LAN2 - Route Collector - IPv4','RC1 - LAN2 - IPv4','192.0.2.9','192.0.2.9',65500,'1','203.0.113.9','http://rc1-lan2-ipv4.mgmt.example.com/api',1,0,0,0,'api/v4/router/collector/bird/standard',0,NULL,0,NULL,NULL,NULL,0),(4,2,'rc1-lan2-ipv6',6,2,'INEX LAN2 - Route Collector - IPv6','RC1 - LAN2 - IPv6','192.0.2.9','2001:db8::9',65500,'1','2001:db8:0:0:2::9','http://rc1-lan2-ipv6.mgmt.example.com/api',1,0,0,0,'api/v4/router/collector/bird/standard',0,NULL,0,NULL,NULL,NULL,0),(5,1,'rs1-lan1-ipv4',4,1,'INEX LAN1 - Route Server - IPv4','RS1 - LAN1 - IPv4','192.0.2.18','192.0.2.18',65501,'1','203.0.113.18','http://rs1-lan1-ipv4.mgmt.example.com/api',0,0,0,0,'api/v4/router/server/bird/standard',0,NULL,0,NULL,NULL,NULL,0),(6,1,'rs1-lan1-ipv6',6,1,'INEX LAN1 - Route Server - IPv6','RS1 - LAN1 - IPv6','192.0.2.18','2001:db8::18',65501,'1','2001:db8:0:0:2::18','http://rs1-lan1-ipv6.mgmt.example.com/api',1,0,0,0,'api/v4/router/server/bird/standard',0,NULL,0,NULL,NULL,NULL,0),(7,2,'rs1-lan2-ipv4',4,1,'INEX LAN2 - Route Server - IPv4','RS1 - LAN2 - IPv4','192.0.2.19','192.0.2.19',65501,'1','203.0.113.19','http://rs1-lan2-ipv4.mgmt.example.com/api',1,0,0,1,'api/v4/router/server/bird/standard',0,NULL,0,NULL,NULL,NULL,0),(8,2,'rs1-lan2-ipv6',6,1,'INEX LAN2 - Route Server - IPv6','RS1 - LAN2 - IPv6','192.0.2.19','2001:db8::19',65501,'1','2001:db8:0:0:2::19','http://rs1-lan2-ipv6.mgmt.example.com/api',1,0,0,0,'api/v4/router/server/bird/standard',0,NULL,0,NULL,NULL,NULL,0),(9,1,'as112-lan1-ipv4',4,3,'INEX LAN1 - AS112 Service - IPv4','AS112 - LAN1 - IPv4','192.0.2.6','192.0.2.6',112,'1','203.0.113.6','http://as112-lan1-ipv4.mgmt.example.com/api',1,0,0,0,'api/v4/router/as112/bird/standard',1,NULL,0,NULL,NULL,NULL,0),(10,1,'as112-lan1-ipv6',6,3,'INEX LAN1 - AS112 Service - IPv6','AS112 - LAN1 - IPv6','192.0.2.6','2001:db8:0:0:2::6',112,'1','203.0.113.6','http://as112-lan1-ipv6.mgmt.example.com/api',1,0,0,0,'api/v4/router/as112/bird/standard',1,NULL,0,NULL,NULL,NULL,0),(11,2,'as112-lan2-ipv4',4,3,'INEX LAN2 - AS112 Service - IPv4','AS112 - LAN2 - IPv4','192.0.2.16','192.0.2.16',112,'1','203.0.113.16','http://as112-lan2-ipv4.mgmt.example.com/api',1,0,0,0,'api/v4/router/as112/bird/standard',0,NULL,0,NULL,NULL,NULL,0),(12,2,'as112-lan2-ipv6',6,3,'INEX LAN2 - AS112 Service - IPv6','AS112 - LAN2 - IPv6','192.0.2.16','2001:db8:0:0:2::16',112,'1','203.0.113.16','http://as112-lan2-ipv6.mgmt.example.com/api',1,0,0,0,'api/v4/router/as112/bird/standard',0,NULL,0,NULL,NULL,NULL,0),(13,1,'unknown-template',6,2,'INEX LAN2 - Route Collector - IPv6','RC1 - LAN2 - IPv6','192.0.2.9','2001:db8::9',65500,'1','2001:db8:0:0:2::9','http://rc1-lan2-ipv6.mgmt.example.com/api',1,0,0,0,'api/v4/router/does-not-exist',0,NULL,0,NULL,NULL,NULL,0),(29,1,'b2-rs1-lan1-ipv4',4,1,'Bird2 - INEX LAN1 - Route Server - IPv4','B2 RS1 - LAN1 - IPv4','192.0.2.18','192.0.2.18',65501,'6','203.0.113.18',NULL,0,0,0,1,'api/v4/router/server/bird2/standard',0,NULL,1,NULL,NULL,NULL,0),(30,1,'b2-rs1-lan1-ipv6',6,1,'Bird2 - INEX LAN1 - Route Server - IPv6','B2 RS1 - LAN1 - IPv6','192.0.2.18','2001:db8::8',65501,'6','203.0.113.18',NULL,0,0,0,1,'api/v4/router/server/bird2/standard',0,NULL,1,NULL,NULL,NULL,1),(31,1,'b2-rc1-lan1-ipv4',4,2,'Bird2 - INEX LAN1 - Route Collector - IPv4','B2 RC1 - LAN1 - IPv4','192.0.2.8','192.0.2.8',65500,'1','203.0.113.8','http://rc1-lan1-ipv4.mgmt.example.com/api',1,0,0,1,'api/v4/router/collector/bird2/standard',0,NULL,1,NULL,NULL,NULL,0),(32,1,'b2-rc1-lan1-ipv6',6,2,'Bird2 - INEX LAN1 - Route Collector - IPv6','B2 RC1 - LAN1 - IPv6','192.0.2.8','2001:db8::8',65500,'1','2001:db8:0:0:2::8','http://rc1-lan1-ipv6.mgmt.example.com/api',1,0,0,1,'api/v4/router/collector/bird2/standard',0,NULL,1,NULL,NULL,NULL,0);
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
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
  PRIMARY KEY (`id`),
  KEY `IDX_E633EA142C0D6F5F` (`virtual_interface_id`),
  CONSTRAINT `FK_E633EA142C0D6F5F` FOREIGN KEY (`virtual_interface_id`) REFERENCES `virtualinterface` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
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
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_6FE94B185E237E06` (`name`),
  UNIQUE KEY `UNIQ_6FE94B1850C101F8` (`loopback_ip`),
  KEY `IDX_6FE94B18D129B190` (`infrastructure`),
  KEY `IDX_6FE94B182B96718A` (`cabinetid`),
  KEY `IDX_6FE94B18420FB55F` (`vendorid`),
  CONSTRAINT `FK_6FE94B182B96718A` FOREIGN KEY (`cabinetid`) REFERENCES `cabinet` (`id`),
  CONSTRAINT `FK_6FE94B18420FB55F` FOREIGN KEY (`vendorid`) REFERENCES `vendor` (`id`),
  CONSTRAINT `FK_6FE94B18D129B190` FOREIGN KEY (`infrastructure`) REFERENCES `infrastructure` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `switch`
--

LOCK TABLES `switch` WRITE;
/*!40000 ALTER TABLE `switch` DISABLE KEYS */;
INSERT INTO `switch` VALUES (1,1,1,12,'switch1','s1','10.0.0.1','','public','FESX624',1,NULL,NULL,NULL,NULL,'',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1),(2,2,1,12,'switch2','s2','10.0.0.2','','public','FESX624',1,NULL,NULL,NULL,NULL,'',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1);
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
  PRIMARY KEY (`id`),
  KEY `IDX_F84274F1DC2C08F8` (`switchid`),
  CONSTRAINT `FK_F84274F1DC2C08F8` FOREIGN KEY (`switchid`) REFERENCES `switch` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=57 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `switchport`
--

LOCK TABLES `switchport` WRITE;
/*!40000 ALTER TABLE `switchport` DISABLE KEYS */;
INSERT INTO `switchport` VALUES (1,1,1,'GigabitEthernet1',1,1,'GigabitEthernet1','GigabitEthernet1',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(2,1,1,'GigabitEthernet2',1,2,'GigabitEthernet2','GigabitEthernet2',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(3,1,1,'GigabitEthernet3',1,3,'GigabitEthernet3','GigabitEthernet3',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(4,1,1,'GigabitEthernet4',1,4,'GigabitEthernet4','GigabitEthernet4',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(5,1,1,'GigabitEthernet5',1,5,'GigabitEthernet5','GigabitEthernet5',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(6,1,1,'GigabitEthernet6',1,6,'GigabitEthernet6','GigabitEthernet6',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(7,1,1,'GigabitEthernet7',1,7,'GigabitEthernet7','GigabitEthernet7',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(8,1,1,'GigabitEthernet8',1,8,'GigabitEthernet8','GigabitEthernet8',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(9,1,1,'GigabitEthernet9',1,9,'GigabitEthernet9','GigabitEthernet9',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(10,1,1,'GigabitEthernet10',1,10,'GigabitEthernet10','GigabitEthernet10',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(11,1,1,'GigabitEthernet11',1,11,'GigabitEthernet11','GigabitEthernet11',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(12,1,1,'GigabitEthernet12',1,12,'GigabitEthernet12','GigabitEthernet12',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(13,1,1,'GigabitEthernet13',1,13,'GigabitEthernet13','GigabitEthernet13',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(14,1,1,'GigabitEthernet14',1,14,'GigabitEthernet14','GigabitEthernet14',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(15,1,1,'GigabitEthernet15',1,15,'GigabitEthernet15','GigabitEthernet15',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(16,1,1,'GigabitEthernet16',1,16,'GigabitEthernet16','GigabitEthernet16',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(17,1,1,'GigabitEthernet17',1,17,'GigabitEthernet17','GigabitEthernet17',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(18,1,3,'GigabitEthernet18',1,18,'GigabitEthernet18','GigabitEthernet18',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(19,1,3,'GigabitEthernet19',1,19,'GigabitEthernet19','GigabitEthernet19',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(20,1,1,'GigabitEthernet20',1,20,'GigabitEthernet20','GigabitEthernet20',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(21,1,1,'GigabitEthernet21',1,21,'GigabitEthernet21','GigabitEthernet21',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(22,1,1,'GigabitEthernet22',1,22,'GigabitEthernet22','GigabitEthernet22',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(23,1,1,'GigabitEthernet23',1,23,'GigabitEthernet23','GigabitEthernet23',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(24,1,1,'GigabitEthernet24',1,24,'GigabitEthernet24','GigabitEthernet24',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(25,2,1,'GigabitEthernet1',1,25,'GigabitEthernet1','GigabitEthernet1',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(26,2,1,'GigabitEthernet2',1,26,'GigabitEthernet2','GigabitEthernet2',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(27,2,1,'GigabitEthernet3',1,27,'GigabitEthernet3','GigabitEthernet3',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(28,2,1,'GigabitEthernet4',1,28,'GigabitEthernet4','GigabitEthernet4',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(29,2,1,'GigabitEthernet5',1,29,'GigabitEthernet5','GigabitEthernet5',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(30,2,1,'GigabitEthernet6',1,30,'GigabitEthernet6','GigabitEthernet6',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(31,2,1,'GigabitEthernet7',1,31,'GigabitEthernet7','GigabitEthernet7',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(32,2,1,'GigabitEthernet8',1,32,'GigabitEthernet8','GigabitEthernet8',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(33,2,1,'GigabitEthernet9',1,33,'GigabitEthernet9','GigabitEthernet9',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(34,2,1,'GigabitEthernet10',1,34,'GigabitEthernet10','GigabitEthernet10',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(35,2,1,'GigabitEthernet11',1,35,'GigabitEthernet11','GigabitEthernet11',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(36,2,1,'GigabitEthernet12',1,36,'GigabitEthernet12','GigabitEthernet12',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(37,2,1,'GigabitEthernet13',1,37,'GigabitEthernet13','GigabitEthernet13',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(38,2,1,'GigabitEthernet14',1,38,'GigabitEthernet14','GigabitEthernet14',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(39,2,1,'GigabitEthernet15',1,39,'GigabitEthernet15','GigabitEthernet15',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(40,2,1,'GigabitEthernet16',1,40,'GigabitEthernet16','GigabitEthernet16',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(41,2,1,'GigabitEthernet17',1,41,'GigabitEthernet17','GigabitEthernet17',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(42,2,3,'GigabitEthernet18',1,42,'GigabitEthernet18','GigabitEthernet18',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(43,2,3,'GigabitEthernet19',1,43,'GigabitEthernet19','GigabitEthernet19',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(44,2,1,'GigabitEthernet20',1,44,'GigabitEthernet20','GigabitEthernet20',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(45,2,1,'GigabitEthernet21',1,45,'GigabitEthernet21','GigabitEthernet21',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(46,2,1,'GigabitEthernet22',1,46,'GigabitEthernet22','GigabitEthernet22',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(47,2,1,'GigabitEthernet23',1,47,'GigabitEthernet23','GigabitEthernet23',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(48,2,1,'GigabitEthernet24',1,48,'GigabitEthernet24','GigabitEthernet24',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(49,1,3,'GigabitEthernet25',1,49,'GigabitEthernet25','GigabitEthernet25',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(50,1,3,'GigabitEthernet26',1,50,'GigabitEthernet26','GigabitEthernet26',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(51,1,3,'GigabitEthernet27',1,51,'GigabitEthernet27','GigabitEthernet27',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(52,1,3,'GigabitEthernet28',1,52,'GigabitEthernet28','GigabitEthernet28',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(53,2,3,'GigabitEthernet29',1,53,'GigabitEthernet29','GigabitEthernet29',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(54,2,3,'GigabitEthernet30',1,54,'GigabitEthernet30','GigabitEthernet30',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(55,2,3,'GigabitEthernet31',1,55,'GigabitEthernet31','GigabitEthernet31',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(56,2,3,'GigabitEthernet32',1,56,'GigabitEthernet32','GigabitEthernet32',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL);
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
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `telescope_entries`
--

LOCK TABLES `telescope_entries` WRITE;
/*!40000 ALTER TABLE `telescope_entries` DISABLE KEYS */;
INSERT INTO `telescope_entries` VALUES (1,'8ff2744d-3f5e-4d3f-8136-6d1b43cb27ea','8ff2744d-b7d2-45ba-a780-89265a29a336','7fbfaf0b63e202da3dffb66c93082246',1,'exception','{\"class\":\"Illuminate\\\\Database\\\\QueryException\",\"file\":\"\\/Users\\/yannrobin\\/Documents\\/development\\/ixp\\/IXP-Manager\\/vendor\\/laravel\\/framework\\/src\\/Illuminate\\/Database\\/Connection.php\",\"line\":669,\"message\":\"SQLSTATE[42S02]: Base table or view not found: 1146 Table \'ixp_ci.docstore_directories\' doesn\'t exist (SQL: select * from `docstore_directories` where `parent_dir_id` is null order by `name` asc)\",\"trace\":[{\"file\":\"\\/Users\\/yannrobin\\/Documents\\/development\\/ixp\\/IXP-Manager\\/vendor\\/laravel\\/framework\\/src\\/Illuminate\\/Database\\/Connection.php\",\"line\":629},{\"file\":\"\\/Users\\/yannrobin\\/Documents\\/development\\/ixp\\/IXP-Manager\\/vendor\\/laravel\\/framework\\/src\\/Illuminate\\/Database\\/Connection.php\",\"line\":338},{\"file\":\"\\/Users\\/yannrobin\\/Documents\\/development\\/ixp\\/IXP-Manager\\/vendor\\/laravel\\/framework\\/src\\/Illuminate\\/Database\\/Query\\/Builder.php\",\"line\":2132},{\"file\":\"\\/Users\\/yannrobin\\/Documents\\/development\\/ixp\\/IXP-Manager\\/vendor\\/laravel\\/framework\\/src\\/Illuminate\\/Database\\/Query\\/Builder.php\",\"line\":2120},{\"file\":\"\\/Users\\/yannrobin\\/Documents\\/development\\/ixp\\/IXP-Manager\\/vendor\\/laravel\\/framework\\/src\\/Illuminate\\/Database\\/Query\\/Builder.php\",\"line\":2592},{\"file\":\"\\/Users\\/yannrobin\\/Documents\\/development\\/ixp\\/IXP-Manager\\/vendor\\/laravel\\/framework\\/src\\/Illuminate\\/Database\\/Query\\/Builder.php\",\"line\":2121},{\"file\":\"\\/Users\\/yannrobin\\/Documents\\/development\\/ixp\\/IXP-Manager\\/vendor\\/laravel\\/framework\\/src\\/Illuminate\\/Database\\/Eloquent\\/Builder.php\",\"line\":537},{\"file\":\"\\/Users\\/yannrobin\\/Documents\\/development\\/ixp\\/IXP-Manager\\/vendor\\/laravel\\/framework\\/src\\/Illuminate\\/Database\\/Eloquent\\/Builder.php\",\"line\":521},{\"file\":\"\\/Users\\/yannrobin\\/Documents\\/development\\/ixp\\/IXP-Manager\\/app\\/Models\\/DocstoreDirectory.php\",\"line\":129},{\"file\":\"\\/Users\\/yannrobin\\/Documents\\/development\\/ixp\\/IXP-Manager\\/app\\/Http\\/Controllers\\/Docstore\\/DirectoryController.php\",\"line\":72},[],{\"file\":\"\\/Users\\/yannrobin\\/Documents\\/development\\/ixp\\/IXP-Manager\\/vendor\\/laravel\\/framework\\/src\\/Illuminate\\/Routing\\/Controller.php\",\"line\":54},{\"file\":\"\\/Users\\/yannrobin\\/Documents\\/development\\/ixp\\/IXP-Manager\\/vendor\\/laravel\\/framework\\/src\\/Illuminate\\/Routing\\/ControllerDispatcher.php\",\"line\":45},{\"file\":\"\\/Users\\/yannrobin\\/Documents\\/development\\/ixp\\/IXP-Manager\\/vendor\\/laravel\\/framework\\/src\\/Illuminate\\/Routing\\/Route.php\",\"line\":219},{\"file\":\"\\/Users\\/yannrobin\\/Documents\\/development\\/ixp\\/IXP-Manager\\/vendor\\/laravel\\/framework\\/src\\/Illuminate\\/Routing\\/Route.php\",\"line\":176},{\"file\":\"\\/Users\\/yannrobin\\/Documents\\/development\\/ixp\\/IXP-Manager\\/vendor\\/laravel\\/framework\\/src\\/Illuminate\\/Routing\\/Router.php\",\"line\":681},{\"file\":\"\\/Users\\/yannrobin\\/Documents\\/development\\/ixp\\/IXP-Manager\\/vendor\\/laravel\\/framework\\/src\\/Illuminate\\/Pipeline\\/Pipeline.php\",\"line\":130},{\"file\":\"\\/Users\\/yannrobin\\/Documents\\/development\\/ixp\\/IXP-Manager\\/app\\/Http\\/Middleware\\/ControllerEnabled.php\",\"line\":96},{\"file\":\"\\/Users\\/yannrobin\\/Documents\\/development\\/ixp\\/IXP-Manager\\/vendor\\/laravel\\/framework\\/src\\/Illuminate\\/Pipeline\\/Pipeline.php\",\"line\":171},{\"file\":\"\\/Users\\/yannrobin\\/Documents\\/development\\/ixp\\/IXP-Manager\\/vendor\\/laravel\\/framework\\/src\\/Illuminate\\/Routing\\/Middleware\\/SubstituteBindings.php\",\"line\":41},{\"file\":\"\\/Users\\/yannrobin\\/Documents\\/development\\/ixp\\/IXP-Manager\\/vendor\\/laravel\\/framework\\/src\\/Illuminate\\/Pipeline\\/Pipeline.php\",\"line\":171},{\"file\":\"\\/Users\\/yannrobin\\/Documents\\/development\\/ixp\\/IXP-Manager\\/vendor\\/laravel\\/framework\\/src\\/Illuminate\\/Foundation\\/Http\\/Middleware\\/VerifyCsrfToken.php\",\"line\":76},{\"file\":\"\\/Users\\/yannrobin\\/Documents\\/development\\/ixp\\/IXP-Manager\\/vendor\\/laravel\\/framework\\/src\\/Illuminate\\/Pipeline\\/Pipeline.php\",\"line\":171},{\"file\":\"\\/Users\\/yannrobin\\/Documents\\/development\\/ixp\\/IXP-Manager\\/vendor\\/laravel\\/framework\\/src\\/Illuminate\\/View\\/Middleware\\/ShareErrorsFromSession.php\",\"line\":49},{\"file\":\"\\/Users\\/yannrobin\\/Documents\\/development\\/ixp\\/IXP-Manager\\/vendor\\/laravel\\/framework\\/src\\/Illuminate\\/Pipeline\\/Pipeline.php\",\"line\":171},{\"file\":\"\\/Users\\/yannrobin\\/Documents\\/development\\/ixp\\/IXP-Manager\\/vendor\\/laravel\\/framework\\/src\\/Illuminate\\/Session\\/Middleware\\/StartSession.php\",\"line\":56},{\"file\":\"\\/Users\\/yannrobin\\/Documents\\/development\\/ixp\\/IXP-Manager\\/vendor\\/laravel\\/framework\\/src\\/Illuminate\\/Pipeline\\/Pipeline.php\",\"line\":171},{\"file\":\"\\/Users\\/yannrobin\\/Documents\\/development\\/ixp\\/IXP-Manager\\/vendor\\/laravel\\/framework\\/src\\/Illuminate\\/Cookie\\/Middleware\\/AddQueuedCookiesToResponse.php\",\"line\":37},{\"file\":\"\\/Users\\/yannrobin\\/Documents\\/development\\/ixp\\/IXP-Manager\\/vendor\\/laravel\\/framework\\/src\\/Illuminate\\/Pipeline\\/Pipeline.php\",\"line\":171},{\"file\":\"\\/Users\\/yannrobin\\/Documents\\/development\\/ixp\\/IXP-Manager\\/vendor\\/laravel\\/framework\\/src\\/Illuminate\\/Cookie\\/Middleware\\/EncryptCookies.php\",\"line\":66},{\"file\":\"\\/Users\\/yannrobin\\/Documents\\/development\\/ixp\\/IXP-Manager\\/vendor\\/laravel\\/framework\\/src\\/Illuminate\\/Pipeline\\/Pipeline.php\",\"line\":171},{\"file\":\"\\/Users\\/yannrobin\\/Documents\\/development\\/ixp\\/IXP-Manager\\/vendor\\/laravel\\/framework\\/src\\/Illuminate\\/Pipeline\\/Pipeline.php\",\"line\":105},{\"file\":\"\\/Users\\/yannrobin\\/Documents\\/development\\/ixp\\/IXP-Manager\\/vendor\\/laravel\\/framework\\/src\\/Illuminate\\/Routing\\/Router.php\",\"line\":683},{\"file\":\"\\/Users\\/yannrobin\\/Documents\\/development\\/ixp\\/IXP-Manager\\/vendor\\/laravel\\/framework\\/src\\/Illuminate\\/Routing\\/Router.php\",\"line\":658},{\"file\":\"\\/Users\\/yannrobin\\/Documents\\/development\\/ixp\\/IXP-Manager\\/vendor\\/laravel\\/framework\\/src\\/Illuminate\\/Routing\\/Router.php\",\"line\":624},{\"file\":\"\\/Users\\/yannrobin\\/Documents\\/development\\/ixp\\/IXP-Manager\\/vendor\\/laravel\\/framework\\/src\\/Illuminate\\/Routing\\/Router.php\",\"line\":613},{\"file\":\"\\/Users\\/yannrobin\\/Documents\\/development\\/ixp\\/IXP-Manager\\/vendor\\/laravel\\/framework\\/src\\/Illuminate\\/Foundation\\/Http\\/Kernel.php\",\"line\":170},{\"file\":\"\\/Users\\/yannrobin\\/Documents\\/development\\/ixp\\/IXP-Manager\\/vendor\\/laravel\\/framework\\/src\\/Illuminate\\/Pipeline\\/Pipeline.php\",\"line\":130},{\"file\":\"\\/Users\\/yannrobin\\/Documents\\/development\\/ixp\\/IXP-Manager\\/vendor\\/fideloper\\/proxy\\/src\\/TrustProxies.php\",\"line\":57},{\"file\":\"\\/Users\\/yannrobin\\/Documents\\/development\\/ixp\\/IXP-Manager\\/vendor\\/laravel\\/framework\\/src\\/Illuminate\\/Pipeline\\/Pipeline.php\",\"line\":171},{\"file\":\"\\/Users\\/yannrobin\\/Documents\\/development\\/ixp\\/IXP-Manager\\/vendor\\/laravel\\/framework\\/src\\/Illuminate\\/Foundation\\/Http\\/Middleware\\/TransformsRequest.php\",\"line\":21},{\"file\":\"\\/Users\\/yannrobin\\/Documents\\/development\\/ixp\\/IXP-Manager\\/vendor\\/laravel\\/framework\\/src\\/Illuminate\\/Pipeline\\/Pipeline.php\",\"line\":171},{\"file\":\"\\/Users\\/yannrobin\\/Documents\\/development\\/ixp\\/IXP-Manager\\/vendor\\/laravel\\/framework\\/src\\/Illuminate\\/Foundation\\/Http\\/Middleware\\/TransformsRequest.php\",\"line\":21},{\"file\":\"\\/Users\\/yannrobin\\/Documents\\/development\\/ixp\\/IXP-Manager\\/vendor\\/laravel\\/framework\\/src\\/Illuminate\\/Pipeline\\/Pipeline.php\",\"line\":171},{\"file\":\"\\/Users\\/yannrobin\\/Documents\\/development\\/ixp\\/IXP-Manager\\/vendor\\/laravel\\/framework\\/src\\/Illuminate\\/Foundation\\/Http\\/Middleware\\/ValidatePostSize.php\",\"line\":27},{\"file\":\"\\/Users\\/yannrobin\\/Documents\\/development\\/ixp\\/IXP-Manager\\/vendor\\/laravel\\/framework\\/src\\/Illuminate\\/Pipeline\\/Pipeline.php\",\"line\":171},{\"file\":\"\\/Users\\/yannrobin\\/Documents\\/development\\/ixp\\/IXP-Manager\\/vendor\\/laravel\\/framework\\/src\\/Illuminate\\/Foundation\\/Http\\/Middleware\\/CheckForMaintenanceMode.php\",\"line\":63},{\"file\":\"\\/Users\\/yannrobin\\/Documents\\/development\\/ixp\\/IXP-Manager\\/vendor\\/laravel\\/framework\\/src\\/Illuminate\\/Pipeline\\/Pipeline.php\",\"line\":171},{\"file\":\"\\/Users\\/yannrobin\\/Documents\\/development\\/ixp\\/IXP-Manager\\/vendor\\/laravel\\/framework\\/src\\/Illuminate\\/Pipeline\\/Pipeline.php\",\"line\":105},{\"file\":\"\\/Users\\/yannrobin\\/Documents\\/development\\/ixp\\/IXP-Manager\\/vendor\\/laravel\\/framework\\/src\\/Illuminate\\/Foundation\\/Http\\/Kernel.php\",\"line\":145},{\"file\":\"\\/Users\\/yannrobin\\/Documents\\/development\\/ixp\\/IXP-Manager\\/vendor\\/laravel\\/framework\\/src\\/Illuminate\\/Foundation\\/Http\\/Kernel.php\",\"line\":110},{\"file\":\"\\/Users\\/yannrobin\\/Documents\\/development\\/ixp\\/IXP-Manager\\/public\\/index.php\",\"line\":85},{\"file\":\"\\/Users\\/yannrobin\\/Documents\\/development\\/ixp\\/IXP-Manager\\/server.php\",\"line\":21}],\"line_preview\":{\"660\":\"        \\/\\/ took to execute and log the query SQL, bindings and time in our memory.\",\"661\":\"        try {\",\"662\":\"            $result = $callback($query, $bindings);\",\"663\":\"        }\",\"664\":\"\",\"665\":\"        \\/\\/ If an exception occurs when attempting to run a query, we\'ll format the error\",\"666\":\"        \\/\\/ message to include the bindings with SQL, which will make this exception a\",\"667\":\"        \\/\\/ lot more helpful to the developer instead of just the database\'s errors.\",\"668\":\"        catch (Exception $e) {\",\"669\":\"            throw new QueryException(\",\"670\":\"                $query, $this->prepareBindings($bindings), $e\",\"671\":\"            );\",\"672\":\"        }\",\"673\":\"\",\"674\":\"        return $result;\",\"675\":\"    }\",\"676\":\"\",\"677\":\"    \\/**\",\"678\":\"     * Log a query in the connection\'s query log.\",\"679\":\"     *\"},\"hostname\":\"Yanns-MacBook-Pro.local\",\"user\":{\"id\":1,\"name\":null,\"email\":\"joe@siep.com\"},\"occurrences\":1}','2020-02-26 11:02:40'),(2,'9050da12-67a6-4d87-955a-ce770eee65f6','9050da12-6833-468f-be4b-fdffc215e5d8','4acf6fd3bd1ba79c05989b7b18db9175',1,'exception','{\"class\":\"Symfony\\\\Component\\\\Console\\\\Exception\\\\CommandNotFoundException\",\"file\":\"\\/Users\\/barryo\\/dev\\/ixp-inex\\/vendor\\/symfony\\/console\\/Application.php\",\"line\":669,\"message\":\"Command \\\"doctrine:schema:migrate\\\" is not defined.\\n\\nDid you mean one of these?\\n    doctrine:schema:create\\n    doctrine:schema:drop\\n    doctrine:schema:update\\n    doctrine:schema:validate\\n    utils:json-schema-post\",\"trace\":[{\"file\":\"\\/Users\\/barryo\\/dev\\/ixp-inex\\/vendor\\/symfony\\/console\\/Application.php\",\"line\":235},{\"file\":\"\\/Users\\/barryo\\/dev\\/ixp-inex\\/vendor\\/symfony\\/console\\/Application.php\",\"line\":147},{\"file\":\"\\/Users\\/barryo\\/dev\\/ixp-inex\\/vendor\\/laravel\\/framework\\/src\\/Illuminate\\/Console\\/Application.php\",\"line\":93},{\"file\":\"\\/Users\\/barryo\\/dev\\/ixp-inex\\/vendor\\/laravel\\/framework\\/src\\/Illuminate\\/Foundation\\/Console\\/Kernel.php\",\"line\":131},{\"file\":\"\\/Users\\/barryo\\/dev\\/ixp-inex\\/artisan\",\"line\":37}],\"line_preview\":{\"660\":\"\",\"661\":\"                if (1 == \\\\count($alternatives)) {\",\"662\":\"                    $message .= \\\"\\\\n\\\\nDid you mean this?\\\\n    \\\";\",\"663\":\"                } else {\",\"664\":\"                    $message .= \\\"\\\\n\\\\nDid you mean one of these?\\\\n    \\\";\",\"665\":\"                }\",\"666\":\"                $message .= implode(\\\"\\\\n    \\\", $alternatives);\",\"667\":\"            }\",\"668\":\"\",\"669\":\"            throw new CommandNotFoundException($message, array_values($alternatives));\",\"670\":\"        }\",\"671\":\"\",\"672\":\"        \\/\\/ filter out aliases for commands which are already on the list\",\"673\":\"        if (\\\\count($commands) > 1) {\",\"674\":\"            $commandList = $this->commandLoader ? array_merge(array_flip($this->commandLoader->getNames()), $this->commands) : $this->commands;\",\"675\":\"            $commands = array_unique(array_filter($commands, function ($nameOrAlias) use (&$commandList, $commands, &$aliases) {\",\"676\":\"                if (!$commandList[$nameOrAlias] instanceof Command) {\",\"677\":\"                    $commandList[$nameOrAlias] = $this->commandLoader->get($nameOrAlias);\",\"678\":\"                }\",\"679\":\"\"},\"hostname\":\"Barrys-MacBook-Pro.local\",\"occurrences\":1}','2020-04-13 10:15:04');
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
INSERT INTO `telescope_entries_tags` VALUES ('8ff2744d-3f5e-4d3f-8136-6d1b43cb27ea','Auth:1');
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
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
  `ixp_id` int NOT NULL,
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
  PRIMARY KEY (`id`),
  KEY `IDX_1F0F81A7BFF2A482` (`cust_id`),
  KEY `IDX_1F0F81A7A5A4E881` (`ixp_id`),
  CONSTRAINT `FK_1F0F81A7A5A4E881` FOREIGN KEY (`ixp_id`) REFERENCES `ixp` (`id`),
  CONSTRAINT `FK_1F0F81A7BFF2A482` FOREIGN KEY (`cust_id`) REFERENCES `cust` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
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
  `lastupdated` datetime DEFAULT NULL,
  `lastupdatedby` int DEFAULT NULL,
  `creator` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `peeringdb_id` bigint DEFAULT NULL,
  `extra_attributes` json DEFAULT NULL COMMENT '(DC2Type:json)',
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_8D93D649F85E0677` (`username`),
  UNIQUE KEY `UNIQ_8D93D649F2C6186B` (`peeringdb_id`),
  KEY `IDX_8D93D649DA0209B9` (`custid`),
  CONSTRAINT `FK_8D93D649DA0209B9` FOREIGN KEY (`custid`) REFERENCES `cust` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user`
--

LOCK TABLES `user` WRITE;
/*!40000 ALTER TABLE `user` DISABLE KEYS */;
INSERT INTO `user` VALUES (1,1,'travis','$2y$10$FNzPyTKm64oSKeUUCwm1buLQp7h80nBj2suqdjsWH2aajVS1xz/ce','joe@siep.com',NULL,NULL,3,0,'2014-01-06 13:54:22',1,'travis','2014-01-06 13:54:22',NULL,NULL,NULL),(2,5,'imcustadmin','$2y$10$VlJG/42TCK7VQz1Wwy7yreP73Eq/1VKn55B4vJfXy4U7fIGK/9YWC','imagine-custadmin@example.com',NULL,NULL,2,0,'2019-01-16 15:37:24',2,'travis','2018-05-15 15:36:12','Test Test',NULL,NULL),(3,5,'imcustuser','$2y$10$sIUXAklQmQwalBF0nGgCLenCYYUMXWdqSESRjw6faXfiyymfmpk3y','imagine-custuser@example.com',NULL,NULL,1,0,'2019-01-16 15:44:30',3,'travis','2018-05-15 15:36:54','Joe Bloggs',NULL,NULL),(4,2,'hecustuser','$2y$10$sIUXAklQmQwalBF0nGgCLenCYYUMXWdqSESRjw6faXfiyymfmpk3y','heanet-custuser@example.com',NULL,NULL,1,0,'2018-05-15 15:36:54',1,'travis','2018-05-15 15:36:54',NULL,NULL,NULL),(5,2,'hecustadmin','$2y$10$sIUXAklQmQwalBF0nGgCLenCYYUMXWdqSESRjw6faXfiyymfmpk3y','heanet-custadmin@example.com',NULL,NULL,2,0,'2018-05-15 15:36:54',1,'travis','2018-05-15 15:36:54',NULL,NULL,NULL);
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
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
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
  PRIMARY KEY (`id`),
  KEY `user_id_idx` (`user_id`),
  KEY `IDX_6341CC99D43FEAE2` (`customer_to_user_id`),
  KEY `at_idx` (`at`),
  CONSTRAINT `FK_6341CC99D43FEAE2` FOREIGN KEY (`customer_to_user_id`) REFERENCES `customer_to_users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=57 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_logins`
--

LOCK TABLES `user_logins` WRITE;
/*!40000 ALTER TABLE `user_logins` DISABLE KEYS */;
INSERT INTO `user_logins` VALUES (1,1,'10.37.129.2','2014-01-06 13:54:52',1,NULL),(2,1,'10.37.129.2','2014-01-13 10:38:11',1,NULL),(3,1,'::1','2016-11-07 19:30:35',1,NULL),(4,1,'127.0.0.1','2017-10-09 13:19:59',1,NULL),(5,1,'127.0.0.1','2018-05-15 15:34:35',1,NULL),(6,1,'127.0.0.1','2018-06-18 08:30:06',1,NULL),(7,1,'127.0.0.1','2018-06-18 08:30:08',1,NULL),(8,1,'127.0.0.1','2018-06-18 08:31:04',1,NULL),(9,1,'127.0.0.1','2018-06-18 08:31:06',1,NULL),(10,1,'127.0.0.1','2018-06-18 08:36:56',1,NULL),(11,1,'127.0.0.1','2018-06-18 08:36:58',1,NULL),(12,1,'127.0.0.1','2018-06-18 08:43:14',1,NULL),(13,1,'127.0.0.1','2018-06-18 08:43:16',1,NULL),(14,1,'127.0.0.1','2018-06-18 08:43:27',1,NULL),(15,1,'127.0.0.1','2018-06-18 08:43:29',1,NULL),(16,1,'127.0.0.1','2018-06-18 11:29:20',1,NULL),(17,1,'127.0.0.1','2018-06-18 11:29:22',1,NULL),(18,1,'127.0.0.1','2018-06-19 13:15:32',1,NULL),(19,1,'127.0.0.1','2018-06-19 14:16:24',1,NULL),(20,1,'127.0.0.1','2018-06-19 14:16:26',1,NULL),(21,1,'127.0.0.1','2018-06-19 14:17:07',1,NULL),(22,1,'127.0.0.1','2018-06-19 14:17:09',1,NULL),(23,1,'127.0.0.1','2018-06-19 14:19:14',1,NULL),(24,1,'127.0.0.1','2018-06-19 14:19:16',1,NULL),(25,1,'127.0.0.1','2018-06-19 14:22:14',1,NULL),(26,1,'127.0.0.1','2018-06-19 14:22:17',1,NULL),(27,2,'127.0.0.1','2018-06-20 10:23:22',2,NULL),(28,3,'127.0.0.1','2018-06-20 10:23:58',3,NULL),(29,5,'127.0.0.1','2018-06-20 10:24:14',5,NULL),(30,5,'127.0.0.1','2018-06-20 10:24:24',5,NULL),(31,1,'127.0.0.1','2018-06-20 10:25:55',1,NULL),(32,1,'127.0.0.1','2018-06-20 10:25:57',1,NULL),(33,1,'127.0.0.1','2018-06-20 10:26:49',1,NULL),(34,1,'127.0.0.1','2018-06-20 10:26:51',1,NULL),(35,1,'127.0.0.1','2018-06-20 10:27:05',1,NULL),(36,1,'127.0.0.1','2018-06-20 10:27:07',1,NULL),(37,1,'127.0.0.1','2018-06-20 10:27:22',1,NULL),(38,1,'127.0.0.1','2018-06-20 10:27:24',1,NULL),(39,1,'127.0.0.1','2018-06-20 10:28:25',1,NULL),(40,1,'127.0.0.1','2018-06-20 10:28:27',1,NULL),(41,1,'127.0.0.1','2018-06-20 10:28:57',1,NULL),(42,1,'127.0.0.1','2018-06-20 10:28:59',1,NULL),(43,1,'127.0.0.1','2018-06-20 10:32:11',1,NULL),(44,1,'127.0.0.1','2018-06-20 10:32:13',1,NULL),(45,1,'127.0.0.1','2018-06-20 10:36:34',1,NULL),(46,1,'127.0.0.1','2018-06-20 10:36:36',1,NULL),(47,1,'127.0.0.1','2018-06-20 10:37:19',1,NULL),(48,1,'127.0.0.1','2018-06-20 10:37:21',1,NULL),(49,1,'127.0.0.1','2018-06-20 10:37:44',1,NULL),(50,1,'127.0.0.1','2018-06-20 10:37:46',1,NULL),(51,1,'127.0.0.1','2018-06-20 10:38:41',1,NULL),(52,1,'127.0.0.1','2018-06-20 10:38:42',1,NULL),(53,2,'127.0.0.1','2019-01-16 15:37:08',2,NULL),(54,3,'127.0.0.1','2019-01-16 15:38:05',3,NULL),(55,1,'127.0.0.1','2019-03-09 15:38:09',1,NULL),(56,NULL,'127.0.0.1','2020-01-27 12:04:24',1,NULL);
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
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
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
  `created` datetime NOT NULL,
  `expires` datetime NOT NULL,
  `is_2fa_complete` tinyint(1) NOT NULL DEFAULT '0',
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
  `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `shortname` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `nagios_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `bundle_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vendor`
--

LOCK TABLES `vendor` WRITE;
/*!40000 ALTER TABLE `vendor` DISABLE KEYS */;
INSERT INTO `vendor` VALUES (1,'Cisco Systems','Cisco','cisco',NULL),(2,'Foundry Networks','Brocade','brocade',NULL),(3,'Extreme Networks','Extreme','extreme',NULL),(4,'Force10 Networks','Force10','force10',NULL),(5,'Glimmerglass','Glimmerglass','glimmerglass',NULL),(6,'Allied Telesyn','AlliedTel','alliedtel',NULL),(7,'Enterasys','Enterasys','enterasys',NULL),(8,'Dell','Dell','dell',NULL),(9,'Hitachi Cable','Hitachi','hitachi',NULL),(10,'MRV','MRV','mrv',NULL),(11,'Transmode','Transmode','transmode',NULL),(12,'Brocade','Brocade','brocade',NULL),(13,'Juniper Networks','Juniper','juniper',NULL);
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
  PRIMARY KEY (`id`),
  KEY `IDX_11D9014FDA0209B9` (`custid`),
  CONSTRAINT `FK_11D9014FDA0209B9` FOREIGN KEY (`custid`) REFERENCES `cust` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `virtualinterface`
--

LOCK TABLES `virtualinterface` WRITE;
/*!40000 ALTER TABLE `virtualinterface` DISABLE KEYS */;
INSERT INTO `virtualinterface` VALUES (1,2,'Port-Channel','',NULL,0,1,1,1),(2,2,'Port-Channel','',NULL,1,2,1,0),(3,3,'','',NULL,0,NULL,0,0),(4,4,'','',NULL,0,NULL,0,0),(5,4,'','',NULL,0,NULL,0,0),(6,5,'','',NULL,0,NULL,0,0),(7,5,'','',NULL,0,NULL,0,0),(8,1,NULL,NULL,9000,1,NULL,0,0),(9,1,NULL,NULL,9000,1,NULL,0,0),(10,5,'',NULL,NULL,0,NULL,0,0);
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
  PRIMARY KEY (`id`),
  UNIQUE KEY `infra_config_name` (`infrastructureid`,`config_name`),
  KEY `IDX_F83104A1721EBF79` (`infrastructureid`),
  CONSTRAINT `FK_F83104A1721EBF79` FOREIGN KEY (`infrastructureid`) REFERENCES `infrastructure` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vlan`
--

LOCK TABLES `vlan` WRITE;
/*!40000 ALTER TABLE `vlan` DISABLE KEYS */;
INSERT INTO `vlan` VALUES (1,1,'Peering LAN 1',1,0,'',1,1,NULL),(2,2,'Peering LAN 2',2,0,'',1,1,NULL);
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
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_B4B4411A73720641` (`ipv4addressid`),
  UNIQUE KEY `UNIQ_B4B4411A7787D67C` (`ipv6addressid`),
  KEY `IDX_B4B4411ABFDF15D5` (`virtualinterfaceid`),
  KEY `IDX_B4B4411AF48D6D0` (`vlanid`),
  CONSTRAINT `FK_B4B4411A73720641` FOREIGN KEY (`ipv4addressid`) REFERENCES `ipv4address` (`id`),
  CONSTRAINT `FK_B4B4411A7787D67C` FOREIGN KEY (`ipv6addressid`) REFERENCES `ipv6address` (`id`),
  CONSTRAINT `FK_B4B4411ABFDF15D5` FOREIGN KEY (`virtualinterfaceid`) REFERENCES `virtualinterface` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_B4B4411AF48D6D0` FOREIGN KEY (`vlanid`) REFERENCES `vlan` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vlaninterface`
--

LOCK TABLES `vlaninterface` WRITE;
/*!40000 ALTER TABLE `vlaninterface` DISABLE KEYS */;
INSERT INTO `vlaninterface` VALUES (1,10,16,1,1,1,'a.heanet.ie',1,'a.heanet.ie',0,1,NULL,'N7rX2SdfbRsyBLTm','N7rX2SdfbRsyBLTm',1000,1,1,1,1,1,1,0,NULL,0),(2,137,417,2,2,1,'b.heanet.ie',1,'b.heanet.ie',0,1,NULL,'u5zSNJLAVT87RGXQ','u5zSNJLAVT87RGXQ',1000,1,1,1,1,1,0,0,NULL,0),(3,36,NULL,3,1,1,'a.pch.ie',0,'',0,1,NULL,'mcWsqMdzGwTKt67g','mcWsqMdzGwTKt67g',2000,1,1,0,1,0,1,0,NULL,0),(4,6,NULL,4,1,1,'a.as112.net',0,'',0,1,NULL,'w83fmGpRDtaKomQo','w83fmGpRDtaKomQo',20,1,1,0,1,0,0,0,NULL,0),(5,132,NULL,5,2,1,'b.as112.net',0,'',0,1,NULL,'Pz8VYMNwEdCjKz68','Pz8VYMNwEdCjKz68',20,1,1,0,1,0,0,0,NULL,0),(6,NULL,8,6,1,0,'',1,'a.imagine.ie',0,1,NULL,'X8Ks9QnbER9cyzU3','X8Ks9QnbER9cyzU3',1000,1,0,1,0,1,0,0,NULL,1),(7,172,470,7,2,1,'b.imagine.ie',1,'b.imagine.ie',0,1,NULL,'LyJND4eoKuQz5j49','LyJND4eoKuQz5j49',1000,0,1,1,1,1,0,0,'',1),(8,142,422,10,2,1,'v4.example.com',1,'v6.example.com',0,1,NULL,'soopersecret','soopersecret',100,1,1,1,1,1,1,0,NULL,0);
/*!40000 ALTER TABLE `vlaninterface` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Final view structure for view `view_cust_current_active`
--

/*!50001 DROP VIEW IF EXISTS `view_cust_current_active`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `view_cust_current_active` AS select `cu`.`id` AS `id`,`cu`.`irrdb` AS `irrdb`,`cu`.`company_registered_detail_id` AS `company_registered_detail_id`,`cu`.`company_billing_details_id` AS `company_billing_details_id`,`cu`.`reseller` AS `reseller`,`cu`.`name` AS `name`,`cu`.`type` AS `type`,`cu`.`shortname` AS `shortname`,`cu`.`abbreviatedName` AS `abbreviatedName`,`cu`.`autsys` AS `autsys`,`cu`.`maxprefixes` AS `maxprefixes`,`cu`.`peeringemail` AS `peeringemail`,`cu`.`nocphone` AS `nocphone`,`cu`.`noc24hphone` AS `noc24hphone`,`cu`.`nocfax` AS `nocfax`,`cu`.`nocemail` AS `nocemail`,`cu`.`nochours` AS `nochours`,`cu`.`nocwww` AS `nocwww`,`cu`.`peeringmacro` AS `peeringmacro`,`cu`.`peeringmacrov6` AS `peeringmacrov6`,`cu`.`peeringpolicy` AS `peeringpolicy`,`cu`.`corpwww` AS `corpwww`,`cu`.`datejoin` AS `datejoin`,`cu`.`dateleave` AS `dateleave`,`cu`.`status` AS `status`,`cu`.`activepeeringmatrix` AS `activepeeringmatrix`,`cu`.`lastupdated` AS `lastupdated`,`cu`.`lastupdatedby` AS `lastupdatedby`,`cu`.`creator` AS `creator`,`cu`.`created` AS `created`,`cu`.`MD5Support` AS `MD5Support`,`cu`.`isReseller` AS `isReseller`,`cu`.`in_manrs` AS `in_manrs`,`cu`.`in_peeringdb` AS `in_peeringdb`,`cu`.`peeringdb_oauth` AS `peeringdb_oauth` from `cust` `cu` where ((`cu`.`datejoin` <= curdate()) and ((`cu`.`dateleave` is null) or (`cu`.`dateleave` < '1970-01-01') or (`cu`.`dateleave` >= curdate())) and ((`cu`.`status` = 1) or (`cu`.`status` = 2))) */;
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
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `view_switch_details_by_custid` AS select `vi`.`id` AS `id`,`vi`.`custid` AS `custid`,concat(`vi`.`name`,`vi`.`channelgroup`) AS `virtualinterfacename`,`pi`.`virtualinterfaceid` AS `virtualinterfaceid`,`pi`.`status` AS `status`,`pi`.`speed` AS `speed`,`pi`.`duplex` AS `duplex`,`pi`.`notes` AS `notes`,`sp`.`name` AS `switchport`,`sp`.`id` AS `switchportid`,`sp`.`ifName` AS `spifname`,`sw`.`name` AS `switch`,`sw`.`hostname` AS `switchhostname`,`sw`.`id` AS `switchid`,`sw`.`vendorid` AS `vendorid`,`sw`.`snmppasswd` AS `snmppasswd`,`sw`.`infrastructure` AS `infrastructure`,`ca`.`name` AS `cabinet`,`ca`.`cololocation` AS `colocabinet`,`lo`.`name` AS `locationname`,`lo`.`shortname` AS `locationshortname` from (((((`virtualinterface` `vi` join `physicalinterface` `pi`) join `switchport` `sp`) join `switch` `sw`) join `cabinet` `ca`) join `location` `lo`) where ((`pi`.`virtualinterfaceid` = `vi`.`id`) and (`pi`.`switchportid` = `sp`.`id`) and (`sp`.`switchid` = `sw`.`id`) and (`sw`.`cabinetid` = `ca`.`id`) and (`ca`.`locationid` = `lo`.`id`)) */;
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
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
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

-- Dump completed on 2020-08-20 12:33:37
