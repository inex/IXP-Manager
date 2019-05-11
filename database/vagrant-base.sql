-- MySQL dump 10.13  Distrib 5.7.25, for osx10.14 (x86_64)
--
-- Host: localhost    Database: ixp
-- ------------------------------------------------------
-- Server version	5.7.25

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
-- Table structure for table `api_keys`
--

DROP TABLE IF EXISTS `api_keys`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `api_keys` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `apiKey` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `expires` datetime DEFAULT NULL,
  `allowedIPs` mediumtext COLLATE utf8_unicode_ci,
  `created` datetime NOT NULL,
  `lastseenAt` datetime DEFAULT NULL,
  `lastseenFrom` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_9579321F800A1141` (`apiKey`),
  KEY `IDX_9579321FA76ED395` (`user_id`),
  CONSTRAINT `FK_9579321FA76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `api_keys`
--

LOCK TABLES `api_keys` WRITE;
/*!40000 ALTER TABLE `api_keys` DISABLE KEYS */;
INSERT INTO `api_keys` VALUES (1,1,'Syy4R8uXTquJNkSav4mmbk5eZWOgoc6FKUJPqOoGHhBjhsC9',NULL,'','2014-01-06 14:43:19','2014-12-08 21:02:12','127.0.0.1');
/*!40000 ALTER TABLE `api_keys` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bgp_sessions`
--

DROP TABLE IF EXISTS `bgp_sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bgp_sessions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `srcipaddressid` int(11) NOT NULL,
  `protocol` int(11) NOT NULL,
  `dstipaddressid` int(11) NOT NULL,
  `packetcount` int(11) NOT NULL DEFAULT '0',
  `last_seen` datetime NOT NULL,
  `source` varchar(40) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `src_protocol_dst` (`srcipaddressid`,`protocol`,`dstipaddressid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
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
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bgpsessiondata` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `srcipaddressid` int(11) DEFAULT NULL,
  `dstipaddressid` int(11) DEFAULT NULL,
  `protocol` int(11) DEFAULT NULL,
  `vlan` int(11) DEFAULT NULL,
  `packetcount` int(11) DEFAULT '0',
  `timestamp` datetime DEFAULT NULL,
  `source` varchar(40) COLLATE utf8_unicode_ci DEFAULT NULL,
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

--
-- Table structure for table `cabinet`
--

DROP TABLE IF EXISTS `cabinet`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cabinet` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `locationid` int(11) DEFAULT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `cololocation` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `height` int(11) DEFAULT NULL,
  `type` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `notes` longtext COLLATE utf8_unicode_ci,
  `u_counts_from` smallint(6) DEFAULT NULL,
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
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `company_billing_detail` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `billingContactName` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `billingAddress1` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `billingAddress2` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `billingAddress3` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `billingTownCity` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `billingPostcode` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `billingCountry` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `billingEmail` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `billingTelephone` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `vatNumber` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `vatRate` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `purchaseOrderRequired` tinyint(1) NOT NULL DEFAULT '0',
  `invoiceMethod` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `invoiceEmail` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `billingFrequency` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `company_billing_detail`
--

LOCK TABLES `company_billing_detail` WRITE;
/*!40000 ALTER TABLE `company_billing_detail` DISABLE KEYS */;
INSERT INTO `company_billing_detail` VALUES (1,NULL,'c/o The Bill Payers','Money House, Moneybags Street',NULL,'Dublin','D4','IE',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL),(2,'','','','','','','','','','','',0,'','',''),(3,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL),(4,'','','','','','','','','','','',0,'','',''),(5,'','','','','','','','','','','',0,'','','');
/*!40000 ALTER TABLE `company_billing_detail` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `company_registration_detail`
--

DROP TABLE IF EXISTS `company_registration_detail`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `company_registration_detail` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `registeredName` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `companyNumber` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `jurisdiction` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `address1` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `address2` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `address3` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `townCity` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `postcode` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `country` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
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
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `console_server` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `vendor_id` int(11) DEFAULT NULL,
  `cabinet_id` int(11) DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `hostname` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `model` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `serialNumber` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `active` tinyint(1) DEFAULT '1',
  `notes` longtext COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_92A539235E237E06` (`name`),
  KEY `IDX_92A53923F603EE73` (`vendor_id`),
  KEY `IDX_92A53923D351EC` (`cabinet_id`),
  CONSTRAINT `FK_92A53923D351EC` FOREIGN KEY (`cabinet_id`) REFERENCES `cabinet` (`id`),
  CONSTRAINT `FK_92A53923F603EE73` FOREIGN KEY (`vendor_id`) REFERENCES `vendor` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
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
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `consoleserverconnection` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `custid` int(11) DEFAULT NULL,
  `switchid` int(11) DEFAULT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `port` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `speed` int(11) DEFAULT NULL,
  `parity` int(11) DEFAULT NULL,
  `stopbits` int(11) DEFAULT NULL,
  `flowcontrol` int(11) DEFAULT NULL,
  `autobaud` tinyint(1) DEFAULT NULL,
  `notes` longtext COLLATE utf8_unicode_ci,
  `console_server_id` int(11) DEFAULT NULL,
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
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `contact` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `custid` int(11) DEFAULT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `position` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `phone` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `mobile` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `facilityaccess` tinyint(1) NOT NULL DEFAULT '0',
  `mayauthorize` tinyint(1) NOT NULL DEFAULT '0',
  `notes` longtext COLLATE utf8_unicode_ci,
  `lastupdated` datetime DEFAULT NULL,
  `lastupdatedby` int(11) DEFAULT NULL,
  `creator` varchar(32) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_4C62E638A76ED395` (`user_id`),
  KEY `IDX_4C62E638DA0209B9` (`custid`),
  CONSTRAINT `FK_4C62E638A76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE SET NULL,
  CONSTRAINT `FK_4C62E638DA0209B9` FOREIGN KEY (`custid`) REFERENCES `cust` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `contact`
--

LOCK TABLES `contact` WRITE;
/*!40000 ALTER TABLE `contact` DISABLE KEYS */;
INSERT INTO `contact` VALUES (1,NULL,1,'Vagrant','Master of the Universe','joe@example.com','+353 86 123 4567','+353 1 123 4567',0,0,'','2015-08-20 15:19:12',1,'1','2014-01-06 13:54:22'),(2,NULL,4,'Customer AS112','','none@example.com','','',0,0,'','2015-08-20 15:24:41',1,'vagrant','2015-08-20 15:24:41'),(3,NULL,4,'AS112 User','','none@example.com','','',0,0,'','2015-08-20 15:25:30',1,'vagrant','2015-08-20 15:25:20');
/*!40000 ALTER TABLE `contact` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `contact_group`
--

DROP TABLE IF EXISTS `contact_group`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `contact_group` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `type` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `limited_to` int(11) NOT NULL DEFAULT '0',
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
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `contact_to_group` (
  `contact_id` int(11) NOT NULL,
  `contact_group_id` bigint(20) NOT NULL,
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
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `corebundles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `description` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `type` int(11) NOT NULL,
  `graph_title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `bfd` tinyint(1) NOT NULL DEFAULT '0',
  `ipv4_subnet` varchar(18) COLLATE utf8_unicode_ci DEFAULT NULL,
  `ipv6_subnet` varchar(43) COLLATE utf8_unicode_ci DEFAULT NULL,
  `stp` tinyint(1) NOT NULL DEFAULT '0',
  `cost` int(10) unsigned DEFAULT NULL,
  `preference` int(10) unsigned DEFAULT NULL,
  `enabled` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `corebundles`
--

LOCK TABLES `corebundles` WRITE;
/*!40000 ALTER TABLE `corebundles` DISABLE KEYS */;
/*!40000 ALTER TABLE `corebundles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `coreinterfaces`
--

DROP TABLE IF EXISTS `coreinterfaces`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `coreinterfaces` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `physical_interface_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_E1A404B7FF664B20` (`physical_interface_id`),
  CONSTRAINT `FK_E1A404B7FF664B20` FOREIGN KEY (`physical_interface_id`) REFERENCES `physicalinterface` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `coreinterfaces`
--

LOCK TABLES `coreinterfaces` WRITE;
/*!40000 ALTER TABLE `coreinterfaces` DISABLE KEYS */;
/*!40000 ALTER TABLE `coreinterfaces` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `corelinks`
--

DROP TABLE IF EXISTS `corelinks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `corelinks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `core_interface_sidea_id` int(11) NOT NULL,
  `core_interface_sideb_id` int(11) NOT NULL,
  `core_bundle_id` int(11) NOT NULL,
  `bfd` tinyint(1) NOT NULL DEFAULT '0',
  `ipv4_subnet` varchar(18) COLLATE utf8_unicode_ci DEFAULT NULL,
  `ipv6_subnet` varchar(43) COLLATE utf8_unicode_ci DEFAULT NULL,
  `enabled` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_BE421236BEBB85C6` (`core_interface_sidea_id`),
  UNIQUE KEY `UNIQ_BE421236AC0E2A28` (`core_interface_sideb_id`),
  KEY `IDX_BE421236BE9AE9F7` (`core_bundle_id`),
  CONSTRAINT `FK_BE421236AC0E2A28` FOREIGN KEY (`core_interface_sideb_id`) REFERENCES `coreinterfaces` (`id`),
  CONSTRAINT `FK_BE421236BE9AE9F7` FOREIGN KEY (`core_bundle_id`) REFERENCES `corebundles` (`id`),
  CONSTRAINT `FK_BE421236BEBB85C6` FOREIGN KEY (`core_interface_sidea_id`) REFERENCES `coreinterfaces` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `corelinks`
--

LOCK TABLES `corelinks` WRITE;
/*!40000 ALTER TABLE `corelinks` DISABLE KEYS */;
/*!40000 ALTER TABLE `corelinks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cust`
--

DROP TABLE IF EXISTS `cust`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cust` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `irrdb` int(11) DEFAULT NULL,
  `company_registered_detail_id` int(11) DEFAULT NULL,
  `company_billing_details_id` int(11) DEFAULT NULL,
  `reseller` int(11) DEFAULT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `type` int(11) DEFAULT NULL,
  `shortname` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `abbreviatedName` varchar(30) COLLATE utf8_unicode_ci DEFAULT NULL,
  `autsys` int(11) DEFAULT NULL,
  `maxprefixes` int(11) DEFAULT NULL,
  `peeringemail` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `nocphone` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `noc24hphone` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `nocfax` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `nocemail` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `nochours` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `nocwww` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `peeringmacro` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `peeringmacrov6` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `peeringpolicy` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `corpwww` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `datejoin` date DEFAULT NULL,
  `dateleave` date DEFAULT NULL,
  `status` smallint(6) DEFAULT NULL,
  `activepeeringmatrix` tinyint(1) DEFAULT NULL,
  `lastupdated` date DEFAULT NULL,
  `lastupdatedby` int(11) DEFAULT NULL,
  `creator` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created` date DEFAULT NULL,
  `MD5Support` varchar(255) COLLATE utf8_unicode_ci DEFAULT 'UNKNOWN',
  `isReseller` tinyint(1) NOT NULL DEFAULT '0',
  `in_manrs` tinyint(1) NOT NULL DEFAULT '0',
  `in_peeringdb` tinyint(1) NOT NULL DEFAULT '0',
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
INSERT INTO `cust` VALUES (1,NULL,1,1,NULL,'VAGRANT IXP',3,'vagrant','VAGRANT IXP',2128,1000,'peering@example.com','+353 1 123 4567','+353 1 123 4567','+353 1 123 4568','noc@siep.com','24x7','http://www.example.com/noc/','AS-INEX','AS-INEX','mandatory','http://www.example.com/','2014-01-06',NULL,1,1,'2015-08-20',1,'travis','2014-01-06','YES',0,0,0),(2,1,2,2,NULL,'HEAnet',1,'heanet','HEAnet',1213,1000,'peering@example.com','','','','','0','','AS-HEANET',NULL,'open','http://www.example.com/','2014-01-06',NULL,1,1,NULL,NULL,'travis','2014-01-06','UNKNOWN',0,0,0),(3,13,3,3,NULL,'PCH DNS',1,'pchdns','PCH DNS',42,2000,'peering@example.com','','','','','0','','AS-PCH',NULL,'open','http://www.example.com/','2014-01-06',NULL,1,1,'2014-01-06',1,'travis','2014-01-06','YES',0,0,0),(4,2,4,4,NULL,'AS112',4,'as112','AS112',112,20,'peering@example.com','','','','','0','','',NULL,'open','http://www.example.com/','2014-01-06',NULL,1,1,NULL,NULL,'travis','2014-01-06','NO',0,0,0),(5,1,5,5,NULL,'Imagine',1,'imagine','Imagine',25441,1000,'peering@example.com','','','','','0','','AS-IBIS',NULL,'open','http://www.example.com/','2014-01-06',NULL,1,1,NULL,NULL,'travis','2014-01-06','YES',0,0,0);
/*!40000 ALTER TABLE `cust` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cust_notes`
--

DROP TABLE IF EXISTS `cust_notes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cust_notes` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `customer_id` int(11) NOT NULL,
  `private` tinyint(1) NOT NULL DEFAULT '1',
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `note` longtext COLLATE utf8_unicode_ci NOT NULL,
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
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cust_tag` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tag` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `display_as` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` longtext COLLATE utf8mb4_unicode_ci,
  `internal_only` tinyint(1) NOT NULL DEFAULT '0',
  `created` datetime NOT NULL,
  `updated` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_6B54CFB8389B783` (`tag`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
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
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cust_to_cust_tag` (
  `customer_tag_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  PRIMARY KEY (`customer_tag_id`,`customer_id`),
  KEY `IDX_A6CFB30CB17BF40` (`customer_tag_id`),
  KEY `IDX_A6CFB30C9395C3F3` (`customer_id`),
  CONSTRAINT `FK_A6CFB30C9395C3F3` FOREIGN KEY (`customer_id`) REFERENCES `cust` (`id`),
  CONSTRAINT `FK_A6CFB30CB17BF40` FOREIGN KEY (`customer_tag_id`) REFERENCES `cust_tag` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
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
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `custkit` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `custid` int(11) DEFAULT NULL,
  `cabinetid` int(11) DEFAULT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `descr` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
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
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `customer_to_ixp` (
  `customer_id` int(11) NOT NULL,
  `ixp_id` int(11) NOT NULL,
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
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `customer_to_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `customer_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `privs` int(11) NOT NULL,
  `last_login_date` datetime DEFAULT NULL,
  `last_login_from` tinytext COLLATE utf8mb4_unicode_ci,
  `created_at` datetime NOT NULL,
  `extra_attributes` json DEFAULT NULL COMMENT '(DC2Type:json)',
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
INSERT INTO `customer_to_users` VALUES (1,1,1,3,'2019-05-11 14:37:47','127.0.0.1','2019-05-11 14:37:17','{\"created_by\": {\"type\": \"migration-script\"}}'),(2,4,2,2,'1970-01-01 00:00:00','','2019-05-11 14:37:17','{\"created_by\": {\"type\": \"migration-script\"}}'),(3,4,3,1,'1970-01-01 00:00:00','','2019-05-11 14:37:17','{\"created_by\": {\"type\": \"migration-script\"}}');
/*!40000 ALTER TABLE `customer_to_users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `failed_jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
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
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `infrastructure` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ixp_id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `shortname` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `isPrimary` tinyint(1) NOT NULL DEFAULT '0',
  `peeringdb_ix_id` bigint(20) DEFAULT NULL,
  `ixf_ix_id` bigint(20) DEFAULT NULL,
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
INSERT INTO `infrastructure` VALUES (1,1,'Infrastructure #1','#1',1,NULL,NULL),(2,1,'Infrastructure #2','#2',0,NULL,NULL);
/*!40000 ALTER TABLE `infrastructure` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ipv4address`
--

DROP TABLE IF EXISTS `ipv4address`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ipv4address` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `vlanid` int(11) DEFAULT NULL,
  `address` varchar(16) COLLATE utf8_unicode_ci DEFAULT NULL,
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
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ipv6address` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `vlanid` int(11) DEFAULT NULL,
  `address` varchar(40) COLLATE utf8_unicode_ci DEFAULT NULL,
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
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `irrdb_asn` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `customer_id` int(11) NOT NULL,
  `asn` int(11) NOT NULL,
  `protocol` int(11) NOT NULL,
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
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `irrdb_prefix` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `customer_id` int(11) NOT NULL,
  `prefix` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `protocol` int(11) NOT NULL,
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
INSERT INTO `irrdb_prefix` VALUES (1,4,'192.175.48.0/24',4,'2014-01-06 14:42:30','2014-01-06 14:42:30'),(2,2,'4.53.84.128/26',4,'2014-01-06 14:42:31','2014-01-06 14:42:31'),(3,2,'4.53.146.192/26',4,'2014-01-06 14:42:31','2014-01-06 14:42:31'),(4,2,'77.72.72.0/21',4,'2014-01-06 14:42:31','2014-01-06 14:42:31'),(5,2,'87.32.0.0/12',4,'2014-01-06 14:42:31','2014-01-06 14:42:31'),(6,2,'91.123.224.0/20',4,'2014-01-06 14:42:31','2014-01-06 14:42:31'),(7,2,'134.226.0.0/16',4,'2014-01-06 14:42:31','2014-01-06 14:42:31'),(8,2,'136.201.0.0/16',4,'2014-01-06 14:42:31','2014-01-06 14:42:31'),(9,2,'136.206.0.0/16',4,'2014-01-06 14:42:31','2014-01-06 14:42:31'),(10,2,'137.43.0.0/16',4,'2014-01-06 14:42:31','2014-01-06 14:42:31'),(11,2,'140.203.0.0/16',4,'2014-01-06 14:42:31','2014-01-06 14:42:31'),(12,2,'143.239.0.0/16',4,'2014-01-06 14:42:31','2014-01-06 14:42:31'),(13,2,'147.252.0.0/16',4,'2014-01-06 14:42:31','2014-01-06 14:42:31'),(14,2,'149.153.0.0/16',4,'2014-01-06 14:42:31','2014-01-06 14:42:31'),(15,2,'149.157.0.0/16',4,'2014-01-06 14:42:31','2014-01-06 14:42:31'),(16,2,'157.190.0.0/16',4,'2014-01-06 14:42:31','2014-01-06 14:42:31'),(17,2,'160.6.0.0/16',4,'2014-01-06 14:42:31','2014-01-06 14:42:31'),(18,2,'176.97.158.0/24',4,'2014-01-06 14:42:31','2014-01-06 14:42:31'),(19,2,'192.174.68.0/24',4,'2014-01-06 14:42:31','2014-01-06 14:42:31'),(20,2,'192.175.48.0/24',4,'2014-01-06 14:42:31','2014-01-06 14:42:31'),(21,2,'193.1.0.0/16',4,'2014-01-06 14:42:31','2014-01-06 14:42:31'),(22,2,'193.242.111.0/24',4,'2014-01-06 14:42:31','2014-01-06 14:42:31'),(23,2,'194.0.24.0/24',4,'2014-01-06 14:42:31','2014-01-06 14:42:31'),(24,2,'194.0.25.0/24',4,'2014-01-06 14:42:31','2014-01-06 14:42:31'),(25,2,'194.0.26.0/24',4,'2014-01-06 14:42:31','2014-01-06 14:42:31'),(26,2,'194.88.240.0/23',4,'2014-01-06 14:42:31','2014-01-06 14:42:31'),(27,2,'212.3.242.128/26',4,'2014-01-06 14:42:31','2014-01-06 14:42:31'),(28,2,'2001:678:20::/48',6,'2014-01-06 14:42:32','2014-01-06 14:42:32'),(29,2,'2001:678:24::/48',6,'2014-01-06 14:42:32','2014-01-06 14:42:32'),(30,2,'2001:67c:1bc::/48',6,'2014-01-06 14:42:32','2014-01-06 14:42:32'),(31,2,'2001:67c:10b8::/48',6,'2014-01-06 14:42:32','2014-01-06 14:42:32'),(32,2,'2001:67c:10e0::/48',6,'2014-01-06 14:42:32','2014-01-06 14:42:32'),(33,2,'2001:770::/32',6,'2014-01-06 14:42:32','2014-01-06 14:42:32'),(34,2,'2001:7f8:18::/48',6,'2014-01-06 14:42:32','2014-01-06 14:42:32'),(35,2,'2001:1900:2205::/48',6,'2014-01-06 14:42:32','2014-01-06 14:42:32'),(36,2,'2001:1900:2206::/48',6,'2014-01-06 14:42:32','2014-01-06 14:42:32'),(37,2,'2620:4f:8000::/48',6,'2014-01-06 14:42:32','2014-01-06 14:42:32'),(38,2,'2a01:4b0::/32',6,'2014-01-06 14:42:32','2014-01-06 14:42:32'),(39,5,'31.169.96.0/21',4,'2014-01-06 14:42:33','2014-01-06 14:42:33'),(40,5,'62.231.32.0/19',4,'2014-01-06 14:42:33','2014-01-06 14:42:33'),(41,5,'78.135.128.0/17',4,'2014-01-06 14:42:33','2014-01-06 14:42:33'),(42,5,'83.141.64.0/18',4,'2014-01-06 14:42:33','2014-01-06 14:42:33'),(43,5,'85.134.128.0/17',4,'2014-01-06 14:42:33','2014-01-06 14:42:33'),(44,5,'87.192.0.0/16',4,'2014-01-06 14:42:33','2014-01-06 14:42:33'),(45,5,'87.232.0.0/16',4,'2014-01-06 14:42:33','2014-01-06 14:42:33'),(46,5,'89.28.176.0/21',4,'2014-01-06 14:42:33','2014-01-06 14:42:33'),(47,5,'89.124.0.0/14',4,'2014-01-06 14:42:33','2014-01-06 14:42:33'),(48,5,'89.124.0.0/15',4,'2014-01-06 14:42:33','2014-01-06 14:42:33'),(49,5,'89.125.0.0/16',4,'2014-01-06 14:42:33','2014-01-06 14:42:33'),(50,5,'89.126.0.0/16',4,'2014-01-06 14:42:33','2014-01-06 14:42:33'),(51,5,'89.126.0.0/19',4,'2014-01-06 14:42:33','2014-01-06 14:42:33'),(52,5,'89.126.0.0/20',4,'2014-01-06 14:42:33','2014-01-06 14:42:33'),(53,5,'89.126.32.0/19',4,'2014-01-06 14:42:33','2014-01-06 14:42:33'),(54,5,'89.126.64.0/19',4,'2014-01-06 14:42:33','2014-01-06 14:42:33'),(55,5,'89.126.96.0/19',4,'2014-01-06 14:42:33','2014-01-06 14:42:33'),(56,5,'91.194.126.0/23',4,'2014-01-06 14:42:33','2014-01-06 14:42:33'),(57,5,'91.194.126.0/24',4,'2014-01-06 14:42:33','2014-01-06 14:42:33'),(58,5,'91.194.127.0/24',4,'2014-01-06 14:42:33','2014-01-06 14:42:33'),(59,5,'91.209.106.0/24',4,'2014-01-06 14:42:33','2014-01-06 14:42:33'),(60,5,'91.209.106.0/25',4,'2014-01-06 14:42:33','2014-01-06 14:42:33'),(61,5,'91.209.106.128/25',4,'2014-01-06 14:42:33','2014-01-06 14:42:33'),(62,5,'91.213.49.0/24',4,'2014-01-06 14:42:33','2014-01-06 14:42:33'),(63,5,'91.220.224.0/24',4,'2014-01-06 14:42:33','2014-01-06 14:42:33'),(64,5,'141.105.112.0/21',4,'2014-01-06 14:42:33','2014-01-06 14:42:33'),(65,5,'176.52.216.0/21',4,'2014-01-06 14:42:33','2014-01-06 14:42:33'),(66,5,'195.5.172.0/24',4,'2014-01-06 14:42:33','2014-01-06 14:42:33'),(67,5,'195.60.166.0/23',4,'2014-01-06 14:42:33','2014-01-06 14:42:33'),(68,5,'216.245.44.0/24',4,'2014-01-06 14:42:33','2014-01-06 14:42:33'),(69,5,'2001:67c:20::/48',6,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(70,5,'2001:67c:338::/48',6,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(71,5,'2001:4d68::/32',6,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(72,5,'2a01:268::/32',6,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(73,5,'2a01:8f80::/32',6,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(74,3,'31.135.128.0/19',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(75,3,'31.135.128.0/21',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(76,3,'31.135.136.0/21',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(77,3,'31.135.144.0/22',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(78,3,'31.135.148.0/22',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(79,3,'31.135.152.0/22',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(80,3,'31.135.152.0/23',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(81,3,'31.135.154.0/23',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(82,3,'36.0.4.0/22',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(83,3,'63.246.32.0/20',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(84,3,'64.68.192.0/20',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(85,3,'64.68.192.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(86,3,'64.68.193.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(87,3,'64.68.194.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(88,3,'64.68.195.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(89,3,'64.68.196.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(90,3,'64.78.200.0/21',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(91,3,'64.185.240.0/20',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(92,3,'65.22.4.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(93,3,'65.22.5.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(94,3,'65.22.19.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(95,3,'65.22.23.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(96,3,'65.22.27.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(97,3,'65.22.31.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(98,3,'65.22.35.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(99,3,'65.22.39.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(100,3,'65.22.47.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(101,3,'65.22.51.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(102,3,'65.22.55.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(103,3,'65.22.59.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(104,3,'65.22.63.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(105,3,'65.22.67.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(106,3,'65.22.71.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(107,3,'65.22.79.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(108,3,'65.22.83.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(109,3,'65.22.87.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(110,3,'65.22.91.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(111,3,'65.22.95.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(112,3,'65.22.99.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(113,3,'65.22.103.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(114,3,'65.22.107.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(115,3,'65.22.111.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(116,3,'65.22.115.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(117,3,'65.22.119.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(118,3,'65.22.123.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(119,3,'65.22.127.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(120,3,'65.22.131.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(121,3,'65.22.135.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(122,3,'65.22.139.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(123,3,'65.22.143.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(124,3,'65.22.147.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(125,3,'65.22.151.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(126,3,'65.22.155.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(127,3,'65.22.159.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(128,3,'65.22.163.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(129,3,'65.22.171.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(130,3,'65.22.175.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(131,3,'65.22.179.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(132,3,'65.22.183.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(133,3,'65.22.187.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(134,3,'65.22.191.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(135,3,'65.22.195.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(136,3,'65.22.199.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(137,3,'65.22.203.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(138,3,'65.22.207.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(139,3,'65.22.211.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(140,3,'65.22.215.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(141,3,'65.22.219.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(142,3,'65.22.223.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(143,3,'65.22.227.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(144,3,'65.22.231.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(145,3,'65.22.235.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(146,3,'65.22.239.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(147,3,'65.22.243.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(148,3,'65.22.247.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(149,3,'66.96.112.0/20',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(150,3,'66.102.32.0/20',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(151,3,'66.175.104.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(152,3,'66.185.112.0/20',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(153,3,'66.225.199.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(154,3,'66.225.200.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(155,3,'66.225.201.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(156,3,'67.21.37.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(157,3,'67.22.112.0/21',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(158,3,'67.158.48.0/20',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(159,3,'68.65.112.0/21',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(160,3,'68.65.126.0/23',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(161,3,'68.65.126.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(162,3,'68.65.127.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(163,3,'69.166.10.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(164,3,'69.166.12.0/22',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(165,3,'70.40.0.0/21',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(166,3,'70.40.8.0/21',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(167,3,'72.0.48.0/20',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(168,3,'72.0.48.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(169,3,'72.0.49.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(170,3,'72.0.50.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(171,3,'72.0.51.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(172,3,'72.0.52.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(173,3,'72.0.53.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(174,3,'72.0.54.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(175,3,'72.0.55.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(176,3,'72.0.56.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(177,3,'72.0.57.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(178,3,'72.0.58.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(179,3,'72.0.59.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(180,3,'72.0.60.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(181,3,'72.0.61.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(182,3,'72.0.62.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(183,3,'72.0.63.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(184,3,'72.42.112.0/20',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(185,3,'72.42.112.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(186,3,'72.42.113.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(187,3,'72.42.114.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(188,3,'72.42.115.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(189,3,'72.42.116.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(190,3,'72.42.117.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(191,3,'72.42.118.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(192,3,'72.42.119.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(193,3,'72.42.120.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(194,3,'72.42.121.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(195,3,'72.42.122.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(196,3,'72.42.123.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(197,3,'72.42.124.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(198,3,'72.42.125.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(199,3,'72.42.126.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(200,3,'72.42.127.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(201,3,'74.63.16.0/20',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(202,3,'74.63.16.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(203,3,'74.63.17.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(204,3,'74.63.18.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(205,3,'74.63.19.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(206,3,'74.63.20.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(207,3,'74.63.21.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(208,3,'74.63.22.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(209,3,'74.63.23.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(210,3,'74.63.24.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(211,3,'74.63.25.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(212,3,'74.63.26.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(213,3,'74.63.27.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(214,3,'74.80.64.0/18',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(215,3,'74.80.64.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(216,3,'74.80.65.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(217,3,'74.80.66.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(218,3,'74.80.67.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(219,3,'74.80.68.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(220,3,'74.80.69.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(221,3,'74.80.70.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(222,3,'74.80.71.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(223,3,'74.80.72.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(224,3,'74.80.73.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(225,3,'74.80.74.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(226,3,'74.80.75.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(227,3,'74.80.76.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(228,3,'74.80.77.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(229,3,'74.80.78.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(230,3,'74.80.79.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(231,3,'74.80.80.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(232,3,'74.80.81.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(233,3,'74.80.82.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(234,3,'74.80.83.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(235,3,'74.80.84.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(236,3,'74.80.85.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(237,3,'74.80.86.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(238,3,'74.80.87.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(239,3,'74.80.88.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(240,3,'74.80.89.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(241,3,'74.80.90.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(242,3,'74.80.91.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(243,3,'74.80.92.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(244,3,'74.80.93.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(245,3,'74.80.94.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(246,3,'74.80.95.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(247,3,'74.80.96.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(248,3,'74.80.97.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(249,3,'74.80.98.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(250,3,'74.80.99.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(251,3,'74.80.100.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(252,3,'74.80.101.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(253,3,'74.80.102.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(254,3,'74.80.103.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(255,3,'74.80.104.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(256,3,'74.80.105.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(257,3,'74.80.106.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(258,3,'74.80.107.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(259,3,'74.80.108.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(260,3,'74.80.109.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(261,3,'74.80.110.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(262,3,'74.80.111.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(263,3,'74.80.112.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(264,3,'74.80.113.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(265,3,'74.80.114.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(266,3,'74.80.115.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(267,3,'74.80.116.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(268,3,'74.80.117.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(269,3,'74.80.118.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(270,3,'74.80.119.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(271,3,'74.80.120.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(272,3,'74.80.121.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(273,3,'74.80.122.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(274,3,'74.80.123.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(275,3,'74.80.124.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(276,3,'74.80.125.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(277,3,'74.80.126.0/23',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(278,3,'74.80.126.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(279,3,'74.80.127.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(280,3,'74.118.212.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(281,3,'74.118.213.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(282,3,'74.118.214.0/23',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(283,3,'75.127.16.0/20',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(284,3,'76.191.16.0/20',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(285,3,'89.19.120.0/21',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(286,3,'89.19.120.0/22',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(287,3,'89.19.124.0/23',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(288,3,'89.19.126.0/23',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(289,3,'91.201.224.0/22',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(290,3,'91.201.224.0/23',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(291,3,'91.201.224.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(292,3,'91.201.225.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(293,3,'91.201.226.0/23',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(294,3,'91.201.226.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(295,3,'91.201.227.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(296,3,'91.209.1.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(297,3,'91.209.193.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(298,3,'91.222.16.0/22',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(299,3,'91.222.40.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(300,3,'91.222.41.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(301,3,'91.222.42.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(302,3,'91.222.43.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(303,3,'91.241.93.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(304,3,'93.95.24.0/21',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(305,3,'93.95.24.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(306,3,'93.95.25.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(307,3,'93.95.26.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(308,3,'93.171.128.0/22',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(309,3,'95.47.163.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(310,3,'101.251.4.0/22',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(311,3,'114.69.222.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(312,3,'128.8.0.0/16',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(313,3,'128.161.0.0/16',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(314,3,'129.2.0.0/16',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(315,3,'130.135.0.0/16',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(316,3,'130.167.0.0/16',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(317,3,'131.161.128.0/18',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(318,3,'131.182.0.0/16',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(319,3,'139.229.0.0/16',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(320,3,'140.169.0.0/16',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(321,3,'146.5.0.0/16',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(322,3,'146.58.0.0/16',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(323,3,'150.144.0.0/16',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(324,3,'156.154.43.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(325,3,'156.154.50.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(326,3,'156.154.59.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(327,3,'156.154.96.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(328,3,'156.154.99.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(329,3,'158.154.0.0/16',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(330,3,'169.222.0.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(331,3,'183.91.132.0/22',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(332,3,'192.5.41.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(333,3,'192.12.123.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(334,3,'192.42.70.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(335,3,'192.58.36.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(336,3,'192.67.83.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(337,3,'192.67.107.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(338,3,'192.67.108.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(339,3,'192.68.52.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(340,3,'192.68.148.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(341,3,'192.68.162.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(342,3,'192.70.244.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(343,3,'192.70.249.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(344,3,'192.77.80.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(345,3,'192.84.8.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(346,3,'192.88.124.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(347,3,'192.92.65.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(348,3,'192.92.90.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(349,3,'192.100.9.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(350,3,'192.100.10.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(351,3,'192.100.15.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(352,3,'192.101.148.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(353,3,'192.102.15.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(354,3,'192.102.219.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(355,3,'192.102.233.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(356,3,'192.102.234.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(357,3,'192.112.18.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(358,3,'192.112.223.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(359,3,'192.112.224.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(360,3,'192.124.20.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(361,3,'192.138.101.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(362,3,'192.138.172.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(363,3,'192.149.89.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(364,3,'192.149.104.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(365,3,'192.149.107.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(366,3,'192.149.133.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(367,3,'192.150.32.0/21',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(368,3,'192.153.157.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(369,3,'192.188.4.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(370,3,'192.203.230.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(371,3,'192.225.64.0/19',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(372,3,'192.243.0.0/20',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(373,3,'192.243.16.0/21',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(374,3,'193.29.206.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(375,3,'193.110.16.0/22',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(376,3,'193.110.16.0/23',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(377,3,'193.110.18.0/23',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(378,3,'193.111.240.0/22',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(379,3,'193.178.228.0/23',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(380,3,'193.178.228.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(381,3,'193.178.229.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(382,3,'194.0.12.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(383,3,'194.0.13.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(384,3,'194.0.14.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(385,3,'194.0.17.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(386,3,'194.0.27.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(387,3,'194.0.36.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(388,3,'194.0.42.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(389,3,'194.0.47.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(390,3,'194.28.144.0/22',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(391,3,'194.117.58.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(392,3,'194.117.60.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(393,3,'194.117.61.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(394,3,'194.117.62.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(395,3,'194.117.63.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(396,3,'194.146.180.0/22',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(397,3,'194.146.180.0/23',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(398,3,'194.146.180.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(399,3,'194.146.181.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(400,3,'194.146.182.0/23',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(401,3,'194.146.182.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(402,3,'194.146.183.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(403,3,'194.146.228.0/22',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(404,3,'194.146.228.0/23',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(405,3,'194.146.228.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(406,3,'194.146.229.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(407,3,'194.146.230.0/23',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(408,3,'194.146.230.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(409,3,'194.146.231.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(410,3,'194.153.148.0/23',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(411,3,'195.64.162.0/23',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(412,3,'195.64.162.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(413,3,'195.64.163.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(414,3,'195.82.138.0/23',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(415,3,'198.9.0.0/16',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(416,3,'198.49.1.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(417,3,'198.116.0.0/14',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(418,3,'198.120.0.0/14',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(419,3,'198.182.28.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(420,3,'198.182.31.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(421,3,'198.182.167.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(422,3,'199.4.137.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(423,3,'199.7.64.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(424,3,'199.7.77.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(425,3,'199.7.83.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(426,3,'199.7.86.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(427,3,'199.7.91.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(428,3,'199.7.94.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(429,3,'199.7.95.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(430,3,'199.43.132.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(431,3,'199.115.156.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(432,3,'199.115.157.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(433,3,'199.120.141.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(434,3,'199.120.142.0/23',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(435,3,'199.120.144.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(436,3,'199.182.32.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(437,3,'199.182.40.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(438,3,'199.184.181.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(439,3,'199.184.182.0/23',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(440,3,'199.184.184.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(441,3,'199.249.112.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(442,3,'199.249.113.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(443,3,'199.249.114.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(444,3,'199.249.115.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(445,3,'199.249.116.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(446,3,'199.249.117.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(447,3,'199.249.118.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(448,3,'199.249.119.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(449,3,'199.249.120.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(450,3,'199.249.121.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(451,3,'199.249.122.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(452,3,'199.249.123.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(453,3,'199.249.124.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(454,3,'199.249.125.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(455,3,'199.249.126.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(456,3,'199.249.127.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(457,3,'199.254.171.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(458,3,'200.1.121.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(459,3,'200.1.131.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(460,3,'200.7.4.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(461,3,'200.16.98.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(462,3,'202.6.102.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(463,3,'202.7.4.0/22',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(464,3,'202.52.0.0/23',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(465,3,'202.53.186.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(466,3,'202.53.191.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(467,3,'203.119.88.0/23',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(468,3,'204.14.112.0/21',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(469,3,'204.19.119.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(470,3,'204.26.57.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(471,3,'204.61.208.0/21',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(472,3,'204.61.208.0/22',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(473,3,'204.61.208.0/23',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(474,3,'204.61.210.0/23',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(475,3,'204.61.210.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(476,3,'204.61.212.0/23',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(477,3,'204.61.216.0/23',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(478,3,'204.194.22.0/23',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(479,3,'204.194.22.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(480,3,'204.194.23.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(481,3,'205.132.46.0/23',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(482,3,'205.207.155.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(483,3,'206.51.254.0/23',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(484,3,'206.108.113.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(485,3,'206.196.160.0/19',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(486,3,'206.220.228.0/22',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(487,3,'206.220.228.0/23',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(488,3,'206.220.230.0/23',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(489,3,'206.223.122.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(490,3,'207.34.5.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(491,3,'207.34.6.0/23',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(492,3,'208.15.19.0/24',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(493,3,'208.49.115.64/27',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(494,3,'208.67.88.0/22',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(495,3,'216.21.2.0/23',4,'2014-01-06 14:42:34','2014-01-06 14:42:34'),(496,3,'2001:500:3::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(497,3,'2001:500:14::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(498,3,'2001:500:15::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(499,3,'2001:500:40::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(500,3,'2001:500:41::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(501,3,'2001:500:42::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(502,3,'2001:500:43::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(503,3,'2001:500:44::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(504,3,'2001:500:45::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(505,3,'2001:500:46::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(506,3,'2001:500:47::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(507,3,'2001:500:48::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(508,3,'2001:500:49::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(509,3,'2001:500:4a::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(510,3,'2001:500:4b::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(511,3,'2001:500:4c::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(512,3,'2001:500:4d::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(513,3,'2001:500:4e::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(514,3,'2001:500:4f::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(515,3,'2001:500:50::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(516,3,'2001:500:51::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(517,3,'2001:500:52::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(518,3,'2001:500:53::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(519,3,'2001:500:54::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(520,3,'2001:500:55::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(521,3,'2001:500:56::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(522,3,'2001:500:7d::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(523,3,'2001:500:83::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(524,3,'2001:500:8c::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(525,3,'2001:500:9c::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(526,3,'2001:500:9d::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(527,3,'2001:500:a4::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(528,3,'2001:500:a5::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(529,3,'2001:500:e0::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(530,3,'2001:500:e1::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(531,3,'2001:678:3::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(532,3,'2001:678:28::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(533,3,'2001:678:4c::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(534,3,'2001:678:60::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(535,3,'2001:678:78::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(536,3,'2001:678:94::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(537,3,'2001:dd8:7::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(538,3,'2001:1398:121::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(539,3,'2404:2c00::/32',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(540,3,'2620:0:870::/45',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(541,3,'2620:0:876::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(542,3,'2620:49::/44',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(543,3,'2620:49::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(544,3,'2620:49:a::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(545,3,'2620:49:b::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(546,3,'2620:95:8000::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(547,3,'2620:171::/40',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(548,3,'2620:171:f0::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(549,3,'2620:171:f1::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(550,3,'2620:171:f2::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(551,3,'2620:171:f3::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(552,3,'2620:171:f4::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(553,3,'2620:171:f5::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(554,3,'2620:171:f6::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(555,3,'2620:171:f7::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(556,3,'2620:171:f8::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(557,3,'2620:171:f9::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(558,3,'2620:171:a00::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(559,3,'2620:171:a01::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(560,3,'2620:171:a02::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(561,3,'2620:171:a03::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(562,3,'2620:171:a04::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(563,3,'2620:171:a05::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(564,3,'2620:171:a06::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(565,3,'2620:171:a07::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(566,3,'2620:171:a08::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(567,3,'2620:171:a09::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(568,3,'2620:171:a0a::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(569,3,'2620:171:a0b::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(570,3,'2620:171:a0c::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(571,3,'2620:171:a0d::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(572,3,'2620:171:a0e::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(573,3,'2620:171:a0f::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(574,3,'2620:171:ad0::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(575,3,'2620:171:d00::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(576,3,'2620:171:d01::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(577,3,'2620:171:d02::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(578,3,'2620:171:d03::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(579,3,'2620:171:d04::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(580,3,'2620:171:d05::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(581,3,'2620:171:d06::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(582,3,'2620:171:d07::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(583,3,'2620:171:d08::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(584,3,'2620:171:d09::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(585,3,'2620:171:d0a::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(586,3,'2620:171:d0b::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(587,3,'2620:171:d0c::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(588,3,'2620:171:d0d::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(589,3,'2620:171:d0e::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(590,3,'2620:171:d0f::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(591,3,'2620:171:dd0::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(592,3,'2a01:8840:4::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(593,3,'2a01:8840:5::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(594,3,'2a01:8840:15::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(595,3,'2a01:8840:19::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(596,3,'2a01:8840:1d::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(597,3,'2a01:8840:21::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(598,3,'2a01:8840:25::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(599,3,'2a01:8840:29::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(600,3,'2a01:8840:2d::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(601,3,'2a01:8840:31::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(602,3,'2a01:8840:35::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(603,3,'2a01:8840:39::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(604,3,'2a01:8840:3d::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(605,3,'2a01:8840:41::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(606,3,'2a01:8840:45::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(607,3,'2a01:8840:4d::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(608,3,'2a01:8840:51::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(609,3,'2a01:8840:55::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(610,3,'2a01:8840:59::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(611,3,'2a01:8840:5d::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(612,3,'2a01:8840:61::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(613,3,'2a01:8840:65::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(614,3,'2a01:8840:69::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(615,3,'2a01:8840:6d::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(616,3,'2a01:8840:71::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(617,3,'2a01:8840:75::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(618,3,'2a01:8840:79::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(619,3,'2a01:8840:7d::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(620,3,'2a01:8840:81::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(621,3,'2a01:8840:85::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(622,3,'2a01:8840:89::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(623,3,'2a01:8840:8d::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(624,3,'2a01:8840:91::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(625,3,'2a01:8840:95::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(626,3,'2a01:8840:99::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(627,3,'2a01:8840:9d::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(628,3,'2a01:8840:a1::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(629,3,'2a01:8840:a5::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(630,3,'2a01:8840:a9::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(631,3,'2a01:8840:ad::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(632,3,'2a01:8840:b1::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(633,3,'2a01:8840:b5::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(634,3,'2a01:8840:b9::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(635,3,'2a01:8840:bd::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(636,3,'2a01:8840:c1::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(637,3,'2a01:8840:c5::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(638,3,'2a01:8840:c9::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(639,3,'2a01:8840:cd::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(640,3,'2a01:8840:d1::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(641,3,'2a01:8840:d5::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(642,3,'2a01:8840:d9::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(643,3,'2a01:8840:dd::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(644,3,'2a01:8840:e1::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(645,3,'2a01:8840:e5::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(646,3,'2a01:8840:e9::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(647,3,'2a01:8840:ed::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36'),(648,3,'2a01:8840:f1::/48',6,'2014-01-06 14:42:36','2014-01-06 14:42:36');
/*!40000 ALTER TABLE `irrdb_prefix` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `irrdbconfig`
--

DROP TABLE IF EXISTS `irrdbconfig`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `irrdbconfig` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `host` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `protocol` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `source` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `notes` longtext COLLATE utf8_unicode_ci,
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
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ixp` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `shortname` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `address1` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `address2` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `address3` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `address4` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `country` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_FA4AB7F64082763` (`shortname`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ixp`
--

LOCK TABLES `ixp` WRITE;
/*!40000 ALTER TABLE `ixp` DISABLE KEYS */;
INSERT INTO `ixp` VALUES (1,'VAGRANT IXP','vagrant','5 Somewhere','Somebourogh','Dublin','D4','IE');
/*!40000 ALTER TABLE `ixp` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `l2address`
--

DROP TABLE IF EXISTS `l2address`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `l2address` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `vlan_interface_id` int(11) NOT NULL,
  `mac` varchar(12) COLLATE utf8_unicode_ci DEFAULT NULL,
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
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `location` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `shortname` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `tag` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `address` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `nocphone` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `nocfax` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `nocemail` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `officephone` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `officefax` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `officeemail` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `notes` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `pdb_facility_id` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_5E9E89CB64082763` (`shortname`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `location`
--

LOCK TABLES `location` WRITE;
/*!40000 ALTER TABLE `location` DISABLE KEYS */;
INSERT INTO `location` VALUES (1,'Location 1','l1',NULL,'','','','','','','','',NULL);
/*!40000 ALTER TABLE `location` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `logos`
--

DROP TABLE IF EXISTS `logos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `logos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `customer_id` int(11) DEFAULT NULL,
  `type` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `original_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `stored_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `uploaded_by` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `uploaded_at` datetime NOT NULL,
  `width` int(11) NOT NULL,
  `height` int(11) NOT NULL,
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
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `macaddress` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `virtualinterfaceid` int(11) DEFAULT NULL,
  `firstseen` datetime DEFAULT NULL,
  `lastseen` datetime DEFAULT NULL,
  `mac` varchar(12) COLLATE utf8_unicode_ci DEFAULT NULL,
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
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'2014_10_12_100000_create_password_resets_table',1),(2,'2018_08_08_100000_create_telescope_entries_table',1),(3,'2019_03_25_211956_create_failed_jobs_table',1);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `netinfo`
--

DROP TABLE IF EXISTS `netinfo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `netinfo` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `vlan_id` int(11) NOT NULL,
  `protocol` int(11) NOT NULL,
  `property` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `ix` int(11) NOT NULL DEFAULT '0',
  `value` longtext COLLATE utf8_unicode_ci NOT NULL,
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
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `networkinfo` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `vlanid` int(11) DEFAULT NULL,
  `protocol` int(11) DEFAULT NULL,
  `network` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `masklen` int(11) DEFAULT NULL,
  `rs1address` varchar(40) COLLATE utf8_unicode_ci DEFAULT NULL,
  `rs2address` varchar(40) COLLATE utf8_unicode_ci DEFAULT NULL,
  `dnsfile` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
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
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `oui` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `oui` varchar(6) COLLATE utf8_unicode_ci NOT NULL,
  `organisation` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
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
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `password_resets` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
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
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `patch_panel` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cabinet_id` int(11) DEFAULT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `colo_reference` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `cable_type` int(11) NOT NULL,
  `connector_type` int(11) NOT NULL,
  `installation_date` datetime DEFAULT NULL,
  `port_prefix` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `chargeable` int(11) NOT NULL DEFAULT '0',
  `location_notes` longtext COLLATE utf8_unicode_ci NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `u_position` int(11) DEFAULT NULL,
  `mounted_at` smallint(6) DEFAULT NULL,
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
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `patch_panel_port` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `switch_port_id` int(11) DEFAULT NULL,
  `patch_panel_id` int(11) DEFAULT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `duplex_master_id` int(11) DEFAULT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `number` smallint(6) NOT NULL,
  `state` int(11) NOT NULL,
  `colo_circuit_ref` varchar(30) COLLATE utf8_unicode_ci DEFAULT NULL,
  `ticket_ref` varchar(30) COLLATE utf8_unicode_ci DEFAULT NULL,
  `notes` longtext COLLATE utf8_unicode_ci,
  `private_notes` longtext COLLATE utf8_unicode_ci,
  `assigned_at` date DEFAULT NULL,
  `connected_at` date DEFAULT NULL,
  `cease_requested_at` date DEFAULT NULL,
  `ceased_at` date DEFAULT NULL,
  `last_state_change` date DEFAULT NULL,
  `internal_use` tinyint(1) NOT NULL DEFAULT '0',
  `chargeable` int(11) NOT NULL DEFAULT '0',
  `owned_by` int(11) NOT NULL DEFAULT '0',
  `loa_code` varchar(25) COLLATE utf8_unicode_ci DEFAULT NULL,
  `colo_billing_ref` varchar(30) COLLATE utf8_unicode_ci DEFAULT NULL,
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
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `patch_panel_port_file` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `patch_panel_port_id` int(11) DEFAULT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `type` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `uploaded_at` datetime NOT NULL,
  `uploaded_by` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `size` int(11) NOT NULL,
  `is_private` tinyint(1) NOT NULL DEFAULT '0',
  `storage_location` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
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
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `patch_panel_port_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `patch_panel_port_id` int(11) DEFAULT NULL,
  `duplex_master_id` int(11) DEFAULT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `number` smallint(6) NOT NULL,
  `state` int(11) NOT NULL,
  `colo_circuit_ref` varchar(30) COLLATE utf8_unicode_ci DEFAULT NULL,
  `ticket_ref` varchar(30) COLLATE utf8_unicode_ci DEFAULT NULL,
  `notes` longtext COLLATE utf8_unicode_ci,
  `private_notes` longtext COLLATE utf8_unicode_ci,
  `assigned_at` date DEFAULT NULL,
  `connected_at` date DEFAULT NULL,
  `cease_requested_at` date DEFAULT NULL,
  `ceased_at` date DEFAULT NULL,
  `internal_use` tinyint(1) NOT NULL DEFAULT '0',
  `chargeable` int(11) NOT NULL DEFAULT '0',
  `owned_by` int(11) NOT NULL DEFAULT '0',
  `customer` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `switchport` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `colo_billing_ref` varchar(30) COLLATE utf8_unicode_ci DEFAULT NULL,
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
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `patch_panel_port_history_file` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `patch_panel_port_history_id` int(11) DEFAULT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `type` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `uploaded_at` datetime NOT NULL,
  `uploaded_by` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `size` int(11) NOT NULL,
  `is_private` tinyint(1) NOT NULL DEFAULT '0',
  `storage_location` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
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
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `peering_manager` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `custid` int(11) DEFAULT NULL,
  `peerid` int(11) DEFAULT NULL,
  `email_last_sent` datetime DEFAULT NULL,
  `emails_sent` int(11) DEFAULT NULL,
  `peered` tinyint(1) DEFAULT NULL,
  `rejected` tinyint(1) DEFAULT NULL,
  `notes` longtext COLLATE utf8_unicode_ci,
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
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `peering_matrix` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `x_custid` int(11) DEFAULT NULL,
  `y_custid` int(11) DEFAULT NULL,
  `vlan` int(11) DEFAULT NULL,
  `x_as` int(11) DEFAULT NULL,
  `y_as` int(11) DEFAULT NULL,
  `peering_status` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
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
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `physicalinterface` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `switchportid` int(11) DEFAULT NULL,
  `fanout_physical_interface_id` int(11) DEFAULT NULL,
  `virtualinterfaceid` int(11) DEFAULT NULL,
  `status` int(11) DEFAULT NULL,
  `speed` int(11) DEFAULT NULL,
  `duplex` varchar(16) COLLATE utf8_unicode_ci DEFAULT NULL,
  `notes` longtext COLLATE utf8_unicode_ci,
  `autoneg` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_5FFF4D60E5F6FACB` (`switchportid`),
  UNIQUE KEY `UNIQ_5FFF4D602E68AB8C` (`fanout_physical_interface_id`),
  KEY `IDX_5FFF4D60BFDF15D5` (`virtualinterfaceid`),
  CONSTRAINT `FK_5FFF4D602E68AB8C` FOREIGN KEY (`fanout_physical_interface_id`) REFERENCES `physicalinterface` (`id`),
  CONSTRAINT `FK_5FFF4D60BFDF15D5` FOREIGN KEY (`virtualinterfaceid`) REFERENCES `virtualinterface` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_5FFF4D60E5F6FACB` FOREIGN KEY (`switchportid`) REFERENCES `switchport` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `physicalinterface`
--

LOCK TABLES `physicalinterface` WRITE;
/*!40000 ALTER TABLE `physicalinterface` DISABLE KEYS */;
INSERT INTO `physicalinterface` VALUES (1,3,NULL,1,1,1000,'full','',1),(2,4,NULL,1,1,1000,'full','',1),(3,25,NULL,2,1,1000,'full',NULL,1),(4,8,NULL,3,1,100,'full',NULL,1),(5,6,NULL,4,1,10,'full',NULL,1),(6,30,NULL,5,1,10,'full',NULL,1),(7,9,NULL,6,1,1000,'full',NULL,1),(8,32,NULL,7,1,10000,'full',NULL,1);
/*!40000 ALTER TABLE `physicalinterface` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `routers`
--

DROP TABLE IF EXISTS `routers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `routers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `vlan_id` int(11) NOT NULL,
  `handle` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `protocol` smallint(5) unsigned NOT NULL,
  `type` smallint(5) unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `shortname` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `router_id` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `peering_ip` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `asn` int(10) unsigned NOT NULL,
  `software` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `mgmt_host` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `api` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `api_type` smallint(5) unsigned NOT NULL,
  `lg_access` smallint(5) unsigned DEFAULT NULL,
  `quarantine` tinyint(1) NOT NULL,
  `bgp_lc` tinyint(1) NOT NULL,
  `template` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `skip_md5` tinyint(1) NOT NULL,
  `last_updated` datetime DEFAULT NULL,
  `rpki` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_504FC9BE918020D9` (`handle`),
  KEY `IDX_504FC9BE8B4937A1` (`vlan_id`),
  CONSTRAINT `FK_504FC9BE8B4937A1` FOREIGN KEY (`vlan_id`) REFERENCES `vlan` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `routers`
--

LOCK TABLES `routers` WRITE;
/*!40000 ALTER TABLE `routers` DISABLE KEYS */;
/*!40000 ALTER TABLE `routers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rs_prefixes`
--

DROP TABLE IF EXISTS `rs_prefixes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rs_prefixes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `custid` int(11) DEFAULT NULL,
  `timestamp` datetime DEFAULT NULL,
  `prefix` varchar(64) COLLATE utf8_unicode_ci DEFAULT NULL,
  `protocol` int(11) DEFAULT NULL,
  `irrdb` int(11) DEFAULT NULL,
  `rs_origin` int(11) DEFAULT NULL,
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
-- Table structure for table `sflow_receiver`
--

DROP TABLE IF EXISTS `sflow_receiver`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sflow_receiver` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `virtual_interface_id` int(11) DEFAULT NULL,
  `dst_ip` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `dst_port` int(11) NOT NULL,
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
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `switch` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `infrastructure` int(11) DEFAULT NULL,
  `cabinetid` int(11) DEFAULT NULL,
  `vendorid` int(11) DEFAULT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `hostname` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `ipv4addr` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `ipv6addr` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `snmppasswd` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `model` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `active` tinyint(1) DEFAULT '1',
  `os` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `osDate` datetime DEFAULT NULL,
  `osVersion` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `lastPolled` datetime DEFAULT NULL,
  `notes` longtext COLLATE utf8_unicode_ci,
  `serialNumber` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `mauSupported` tinyint(1) DEFAULT NULL,
  `asn` int(10) unsigned DEFAULT NULL,
  `loopback_ip` varchar(39) COLLATE utf8_unicode_ci DEFAULT NULL,
  `loopback_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `mgmt_mac_address` varchar(12) COLLATE utf8_unicode_ci DEFAULT NULL,
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
INSERT INTO `switch` VALUES (1,1,1,12,'Switch 1','s1','10.0.0.1','','public','FESX624',1,NULL,NULL,NULL,NULL,'',NULL,NULL,NULL,NULL,NULL,NULL),(2,2,1,12,'Switch 2','s2','10.0.0.2','','public','FESX624',1,NULL,NULL,NULL,NULL,'',NULL,NULL,NULL,NULL,NULL,NULL);
/*!40000 ALTER TABLE `switch` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `switchport`
--

DROP TABLE IF EXISTS `switchport`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `switchport` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `switchid` int(11) DEFAULT NULL,
  `type` int(11) DEFAULT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `ifIndex` int(11) DEFAULT NULL,
  `ifName` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `ifAlias` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `ifHighSpeed` int(11) DEFAULT NULL,
  `ifMtu` int(11) DEFAULT NULL,
  `ifPhysAddress` varchar(17) COLLATE utf8_unicode_ci DEFAULT NULL,
  `ifAdminStatus` int(11) DEFAULT NULL,
  `ifOperStatus` int(11) DEFAULT NULL,
  `ifLastChange` int(11) DEFAULT NULL,
  `lastSnmpPoll` datetime DEFAULT NULL,
  `lagIfIndex` int(11) DEFAULT NULL,
  `mauType` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `mauState` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `mauAvailability` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `mauJacktype` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `mauAutoNegSupported` tinyint(1) DEFAULT NULL,
  `mauAutoNegAdminState` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_F84274F1DC2C08F8` (`switchid`),
  CONSTRAINT `FK_F84274F1DC2C08F8` FOREIGN KEY (`switchid`) REFERENCES `switch` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=49 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `switchport`
--

LOCK TABLES `switchport` WRITE;
/*!40000 ALTER TABLE `switchport` DISABLE KEYS */;
INSERT INTO `switchport` VALUES (1,1,1,'GigabitEthernet1',1,1,'GigabitEthernet1','GigabitEthernet1',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(2,1,1,'GigabitEthernet2',1,2,'GigabitEthernet2','GigabitEthernet2',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(3,1,1,'GigabitEthernet3',1,3,'GigabitEthernet3','GigabitEthernet3',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(4,1,1,'GigabitEthernet4',1,4,'GigabitEthernet4','GigabitEthernet4',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(5,1,1,'GigabitEthernet5',1,5,'GigabitEthernet5','GigabitEthernet5',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(6,1,1,'GigabitEthernet6',1,6,'GigabitEthernet6','GigabitEthernet6',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(7,1,1,'GigabitEthernet7',1,7,'GigabitEthernet7','GigabitEthernet7',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(8,1,1,'GigabitEthernet8',1,8,'GigabitEthernet8','GigabitEthernet8',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(9,1,1,'GigabitEthernet9',1,9,'GigabitEthernet9','GigabitEthernet9',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(10,1,1,'GigabitEthernet10',1,10,'GigabitEthernet10','GigabitEthernet10',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(11,1,1,'GigabitEthernet11',1,11,'GigabitEthernet11','GigabitEthernet11',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(12,1,1,'GigabitEthernet12',1,12,'GigabitEthernet12','GigabitEthernet12',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(13,1,1,'GigabitEthernet13',1,13,'GigabitEthernet13','GigabitEthernet13',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(14,1,1,'GigabitEthernet14',1,14,'GigabitEthernet14','GigabitEthernet14',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(15,1,1,'GigabitEthernet15',1,15,'GigabitEthernet15','GigabitEthernet15',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(16,1,1,'GigabitEthernet16',1,16,'GigabitEthernet16','GigabitEthernet16',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(17,1,1,'GigabitEthernet17',1,17,'GigabitEthernet17','GigabitEthernet17',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(18,1,1,'GigabitEthernet18',1,18,'GigabitEthernet18','GigabitEthernet18',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(19,1,1,'GigabitEthernet19',1,19,'GigabitEthernet19','GigabitEthernet19',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(20,1,1,'GigabitEthernet20',1,20,'GigabitEthernet20','GigabitEthernet20',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(21,1,1,'GigabitEthernet21',1,21,'GigabitEthernet21','GigabitEthernet21',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(22,1,1,'GigabitEthernet22',1,22,'GigabitEthernet22','GigabitEthernet22',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(23,1,1,'GigabitEthernet23',1,23,'GigabitEthernet23','GigabitEthernet23',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(24,1,1,'GigabitEthernet24',1,24,'GigabitEthernet24','GigabitEthernet24',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(25,2,1,'GigabitEthernet1',1,25,'GigabitEthernet1','GigabitEthernet1',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(26,2,1,'GigabitEthernet2',1,26,'GigabitEthernet2','GigabitEthernet2',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(27,2,1,'GigabitEthernet3',1,27,'GigabitEthernet3','GigabitEthernet3',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(28,2,1,'GigabitEthernet4',1,28,'GigabitEthernet4','GigabitEthernet4',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(29,2,1,'GigabitEthernet5',1,29,'GigabitEthernet5','GigabitEthernet5',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(30,2,1,'GigabitEthernet6',1,30,'GigabitEthernet6','GigabitEthernet6',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(31,2,1,'GigabitEthernet7',1,31,'GigabitEthernet7','GigabitEthernet7',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(32,2,1,'GigabitEthernet8',1,32,'GigabitEthernet8','GigabitEthernet8',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(33,2,1,'GigabitEthernet9',1,33,'GigabitEthernet9','GigabitEthernet9',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(34,2,1,'GigabitEthernet10',1,34,'GigabitEthernet10','GigabitEthernet10',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(35,2,1,'GigabitEthernet11',1,35,'GigabitEthernet11','GigabitEthernet11',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(36,2,1,'GigabitEthernet12',1,36,'GigabitEthernet12','GigabitEthernet12',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(37,2,1,'GigabitEthernet13',1,37,'GigabitEthernet13','GigabitEthernet13',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(38,2,1,'GigabitEthernet14',1,38,'GigabitEthernet14','GigabitEthernet14',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(39,2,1,'GigabitEthernet15',1,39,'GigabitEthernet15','GigabitEthernet15',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(40,2,1,'GigabitEthernet16',1,40,'GigabitEthernet16','GigabitEthernet16',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(41,2,1,'GigabitEthernet17',1,41,'GigabitEthernet17','GigabitEthernet17',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(42,2,1,'GigabitEthernet18',1,42,'GigabitEthernet18','GigabitEthernet18',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(43,2,1,'GigabitEthernet19',1,43,'GigabitEthernet19','GigabitEthernet19',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(44,2,1,'GigabitEthernet20',1,44,'GigabitEthernet20','GigabitEthernet20',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(45,2,1,'GigabitEthernet21',1,45,'GigabitEthernet21','GigabitEthernet21',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(46,2,1,'GigabitEthernet22',1,46,'GigabitEthernet22','GigabitEthernet22',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(47,2,1,'GigabitEthernet23',1,47,'GigabitEthernet23','GigabitEthernet23',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(48,2,1,'GigabitEthernet24',1,48,'GigabitEthernet24','GigabitEthernet24',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL);
/*!40000 ALTER TABLE `switchport` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `telescope_entries`
--

DROP TABLE IF EXISTS `telescope_entries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `telescope_entries` (
  `sequence` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `family_hash` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `should_display_on_index` tinyint(1) NOT NULL DEFAULT '1',
  `type` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `content` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`sequence`),
  UNIQUE KEY `telescope_entries_uuid_unique` (`uuid`),
  KEY `telescope_entries_batch_id_index` (`batch_id`),
  KEY `telescope_entries_type_should_display_on_index_index` (`type`,`should_display_on_index`),
  KEY `telescope_entries_family_hash_index` (`family_hash`)
) ENGINE=InnoDB AUTO_INCREMENT=63 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `telescope_entries`
--

LOCK TABLES `telescope_entries` WRITE;
/*!40000 ALTER TABLE `telescope_entries` DISABLE KEYS */;
INSERT INTO `telescope_entries` VALUES (1,'8da95c94-85e6-40d2-95e2-b2d12a7637c3','8da95c94-f112-426f-9707-56c5859260d0',NULL,1,'query','{\"connection\":\"mysql\",\"bindings\":[\"ixp\",\"migrations\"],\"sql\":\"select * from information_schema.tables where table_schema = ? and table_name = ?\",\"time\":\"2.38\",\"slow\":false,\"file\":\"\\/Users\\/barryo\\/dev\\/ixp-ibn\\/artisan\",\"line\":37,\"hostname\":\"barryo-mac3.local\"}','2019-05-11 14:37:02'),(2,'8da95c94-9a7a-4338-800d-48214ec105d1','8da95c94-f112-426f-9707-56c5859260d0',NULL,1,'query','{\"connection\":\"mysql\",\"bindings\":[],\"sql\":\"create table `migrations` (`id` int unsigned not null auto_increment primary key, `migration` varchar(255) not null, `batch` int not null) default character set utf8mb4 collate \'utf8mb4_unicode_ci\'\",\"time\":\"17.53\",\"slow\":false,\"file\":\"\\/Users\\/barryo\\/dev\\/ixp-ibn\\/artisan\",\"line\":37,\"hostname\":\"barryo-mac3.local\"}','2019-05-11 14:37:02'),(3,'8da95c94-9ba8-4e78-896d-002ac01f7f6c','8da95c94-f112-426f-9707-56c5859260d0',NULL,1,'query','{\"connection\":\"mysql\",\"bindings\":[],\"sql\":\"select `migration` from `migrations` order by `batch` asc, `migration` asc\",\"time\":\"0.74\",\"slow\":false,\"file\":\"\\/Users\\/barryo\\/dev\\/ixp-ibn\\/artisan\",\"line\":37,\"hostname\":\"barryo-mac3.local\"}','2019-05-11 14:37:02'),(4,'8da95c94-9d6b-48e5-abb7-2981d857f30d','8da95c94-f112-426f-9707-56c5859260d0',NULL,1,'query','{\"connection\":\"mysql\",\"bindings\":[],\"sql\":\"select max(`batch`) as aggregate from `migrations`\",\"time\":\"0.56\",\"slow\":false,\"file\":\"\\/Users\\/barryo\\/dev\\/ixp-ibn\\/artisan\",\"line\":37,\"hostname\":\"barryo-mac3.local\"}','2019-05-11 14:37:02'),(5,'8da95c94-a37b-455b-a47f-c15517b065d0','8da95c94-f112-426f-9707-56c5859260d0',NULL,1,'query','{\"connection\":\"mysql\",\"bindings\":[],\"sql\":\"create table `password_resets` (`email` varchar(255) not null, `token` varchar(255) not null, `created_at` timestamp null) default character set utf8mb4 collate \'utf8mb4_unicode_ci\'\",\"time\":\"12.33\",\"slow\":false,\"file\":\"\\/Users\\/barryo\\/dev\\/ixp-ibn\\/database\\/migrations\\/2014_10_12_100000_create_password_resets_table.php\",\"line\":40,\"hostname\":\"barryo-mac3.local\"}','2019-05-11 14:37:02'),(6,'8da95c94-a8a9-4b07-a1a6-d75c2144d2b1','8da95c94-f112-426f-9707-56c5859260d0',NULL,1,'query','{\"connection\":\"mysql\",\"bindings\":[],\"sql\":\"alter table `password_resets` add index `password_resets_email_index`(`email`)\",\"time\":\"12.14\",\"slow\":false,\"file\":\"\\/Users\\/barryo\\/dev\\/ixp-ibn\\/database\\/migrations\\/2014_10_12_100000_create_password_resets_table.php\",\"line\":40,\"hostname\":\"barryo-mac3.local\"}','2019-05-11 14:37:02'),(7,'8da95c94-a972-46d1-8e7e-a497b7779152','8da95c94-f112-426f-9707-56c5859260d0',NULL,1,'query','{\"connection\":\"mysql\",\"bindings\":[\"2014_10_12_100000_create_password_resets_table\",1],\"sql\":\"insert into `migrations` (`migration`, `batch`) values (?, ?)\",\"time\":\"0.58\",\"slow\":false,\"file\":\"\\/Users\\/barryo\\/dev\\/ixp-ibn\\/artisan\",\"line\":37,\"hostname\":\"barryo-mac3.local\"}','2019-05-11 14:37:02'),(8,'8da95c94-af51-4a1b-9600-00517b316bf8','8da95c94-f112-426f-9707-56c5859260d0',NULL,1,'query','{\"connection\":\"mysql\",\"bindings\":[],\"sql\":\"create table `telescope_entries` (`sequence` bigint unsigned not null auto_increment primary key, `uuid` char(36) not null, `batch_id` char(36) not null, `family_hash` varchar(255) null, `should_display_on_index` tinyint(1) not null default \'1\', `type` varchar(20) not null, `content` longtext not null, `created_at` datetime null) default character set utf8mb4 collate \'utf8mb4_unicode_ci\'\",\"time\":\"11.06\",\"slow\":false,\"file\":\"\\/Users\\/barryo\\/dev\\/ixp-ibn\\/artisan\",\"line\":37,\"hostname\":\"barryo-mac3.local\"}','2019-05-11 14:37:02'),(9,'8da95c94-b815-44a3-8482-1e69661555ec','8da95c94-f112-426f-9707-56c5859260d0',NULL,1,'query','{\"connection\":\"mysql\",\"bindings\":[],\"sql\":\"alter table `telescope_entries` add unique `telescope_entries_uuid_unique`(`uuid`)\",\"time\":\"21.25\",\"slow\":false,\"file\":\"\\/Users\\/barryo\\/dev\\/ixp-ibn\\/artisan\",\"line\":37,\"hostname\":\"barryo-mac3.local\"}','2019-05-11 14:37:02'),(10,'8da95c94-c20c-4086-b4e6-d313279c0c4b','8da95c94-f112-426f-9707-56c5859260d0',NULL,1,'query','{\"connection\":\"mysql\",\"bindings\":[],\"sql\":\"alter table `telescope_entries` add index `telescope_entries_batch_id_index`(`batch_id`)\",\"time\":\"24.40\",\"slow\":false,\"file\":\"\\/Users\\/barryo\\/dev\\/ixp-ibn\\/artisan\",\"line\":37,\"hostname\":\"barryo-mac3.local\"}','2019-05-11 14:37:02'),(11,'8da95c94-c6e5-46e7-a1af-1bf933ea50c4','8da95c94-f112-426f-9707-56c5859260d0',NULL,1,'query','{\"connection\":\"mysql\",\"bindings\":[],\"sql\":\"alter table `telescope_entries` add index `telescope_entries_type_should_display_on_index_index`(`type`, `should_display_on_index`)\",\"time\":\"11.14\",\"slow\":false,\"file\":\"\\/Users\\/barryo\\/dev\\/ixp-ibn\\/artisan\",\"line\":37,\"hostname\":\"barryo-mac3.local\"}','2019-05-11 14:37:02'),(12,'8da95c94-cc4f-4259-9af3-6cc6b4784723','8da95c94-f112-426f-9707-56c5859260d0',NULL,1,'query','{\"connection\":\"mysql\",\"bindings\":[],\"sql\":\"alter table `telescope_entries` add index `telescope_entries_family_hash_index`(`family_hash`)\",\"time\":\"12.57\",\"slow\":false,\"file\":\"\\/Users\\/barryo\\/dev\\/ixp-ibn\\/artisan\",\"line\":37,\"hostname\":\"barryo-mac3.local\"}','2019-05-11 14:37:02'),(13,'8da95c94-d4af-4cf8-b596-c19296c1f3fc','8da95c94-f112-426f-9707-56c5859260d0',NULL,1,'query','{\"connection\":\"mysql\",\"bindings\":[],\"sql\":\"create table `telescope_entries_tags` (`entry_uuid` char(36) not null, `tag` varchar(255) not null) default character set utf8mb4 collate \'utf8mb4_unicode_ci\'\",\"time\":\"18.83\",\"slow\":false,\"file\":\"\\/Users\\/barryo\\/dev\\/ixp-ibn\\/artisan\",\"line\":37,\"hostname\":\"barryo-mac3.local\"}','2019-05-11 14:37:02'),(14,'8da95c94-d980-4011-8695-f628a9c98fbf','8da95c94-f112-426f-9707-56c5859260d0',NULL,1,'query','{\"connection\":\"mysql\",\"bindings\":[],\"sql\":\"alter table `telescope_entries_tags` add index `telescope_entries_tags_entry_uuid_tag_index`(`entry_uuid`, `tag`)\",\"time\":\"11.20\",\"slow\":false,\"file\":\"\\/Users\\/barryo\\/dev\\/ixp-ibn\\/artisan\",\"line\":37,\"hostname\":\"barryo-mac3.local\"}','2019-05-11 14:37:02'),(15,'8da95c94-de02-4d1c-8dea-29c53ebdb526','8da95c94-f112-426f-9707-56c5859260d0',NULL,1,'query','{\"connection\":\"mysql\",\"bindings\":[],\"sql\":\"alter table `telescope_entries_tags` add index `telescope_entries_tags_tag_index`(`tag`)\",\"time\":\"10.38\",\"slow\":false,\"file\":\"\\/Users\\/barryo\\/dev\\/ixp-ibn\\/artisan\",\"line\":37,\"hostname\":\"barryo-mac3.local\"}','2019-05-11 14:37:02'),(16,'8da95c94-e52d-4853-b938-63d237ba3551','8da95c94-f112-426f-9707-56c5859260d0',NULL,1,'query','{\"connection\":\"mysql\",\"bindings\":[],\"sql\":\"alter table `telescope_entries_tags` add constraint `telescope_entries_tags_entry_uuid_foreign` foreign key (`entry_uuid`) references `telescope_entries` (`uuid`) on delete cascade\",\"time\":\"17.24\",\"slow\":false,\"file\":\"\\/Users\\/barryo\\/dev\\/ixp-ibn\\/artisan\",\"line\":37,\"hostname\":\"barryo-mac3.local\"}','2019-05-11 14:37:02'),(17,'8da95c94-e9da-4281-a400-52a53df36cb4','8da95c94-f112-426f-9707-56c5859260d0',NULL,1,'query','{\"connection\":\"mysql\",\"bindings\":[],\"sql\":\"create table `telescope_monitoring` (`tag` varchar(255) not null) default character set utf8mb4 collate \'utf8mb4_unicode_ci\'\",\"time\":\"10.50\",\"slow\":false,\"file\":\"\\/Users\\/barryo\\/dev\\/ixp-ibn\\/artisan\",\"line\":37,\"hostname\":\"barryo-mac3.local\"}','2019-05-11 14:37:02'),(18,'8da95c94-ea8f-4f69-9f61-bb752895e2f6','8da95c94-f112-426f-9707-56c5859260d0',NULL,1,'query','{\"connection\":\"mysql\",\"bindings\":[\"2018_08_08_100000_create_telescope_entries_table\",1],\"sql\":\"insert into `migrations` (`migration`, `batch`) values (?, ?)\",\"time\":\"0.55\",\"slow\":false,\"file\":\"\\/Users\\/barryo\\/dev\\/ixp-ibn\\/artisan\",\"line\":37,\"hostname\":\"barryo-mac3.local\"}','2019-05-11 14:37:02'),(19,'8da95c94-efac-481f-9579-19fe72388a50','8da95c94-f112-426f-9707-56c5859260d0',NULL,1,'query','{\"connection\":\"mysql\",\"bindings\":[],\"sql\":\"create table `failed_jobs` (`id` bigint unsigned not null auto_increment primary key, `connection` text not null, `queue` text not null, `payload` longtext not null, `exception` longtext not null, `failed_at` timestamp default CURRENT_TIMESTAMP not null) default character set utf8mb4 collate \'utf8mb4_unicode_ci\'\",\"time\":\"10.67\",\"slow\":false,\"file\":\"\\/Users\\/barryo\\/dev\\/ixp-ibn\\/database\\/migrations\\/2019_03_25_211956_create_failed_jobs_table.php\",\"line\":23,\"hostname\":\"barryo-mac3.local\"}','2019-05-11 14:37:02'),(20,'8da95c94-f05a-4ec9-9b74-37b97fc80ec3','8da95c94-f112-426f-9707-56c5859260d0',NULL,1,'query','{\"connection\":\"mysql\",\"bindings\":[\"2019_03_25_211956_create_failed_jobs_table\",1],\"sql\":\"insert into `migrations` (`migration`, `batch`) values (?, ?)\",\"time\":\"0.55\",\"slow\":false,\"file\":\"\\/Users\\/barryo\\/dev\\/ixp-ibn\\/artisan\",\"line\":37,\"hostname\":\"barryo-mac3.local\"}','2019-05-11 14:37:02'),(21,'8da95c94-f0c6-4ad5-a016-723a20882c90','8da95c94-f112-426f-9707-56c5859260d0',NULL,1,'command','{\"command\":\"migrate\",\"exit_code\":0,\"arguments\":{\"command\":\"migrate\"},\"options\":{\"database\":null,\"force\":false,\"path\":null,\"realpath\":false,\"pretend\":false,\"seed\":false,\"step\":false,\"help\":false,\"quiet\":false,\"verbose\":false,\"version\":false,\"ansi\":false,\"no-ansi\":false,\"no-interaction\":false,\"env\":null},\"hostname\":\"barryo-mac3.local\"}','2019-05-11 14:37:02'),(22,'8da95c9c-6567-4f6e-abe5-d7bc3a1bf65e','8da95c9c-6f55-4bf6-a430-aa4bf8e82dec',NULL,1,'command','{\"command\":\"list\",\"exit_code\":0,\"arguments\":{\"command\":\"list\",\"namespace\":null},\"options\":{\"raw\":false,\"format\":\"txt\",\"help\":false,\"quiet\":false,\"verbose\":false,\"version\":false,\"ansi\":false,\"no-ansi\":false,\"no-interaction\":false,\"env\":null},\"hostname\":\"barryo-mac3.local\"}','2019-05-11 14:37:07'),(23,'8da95cab-8fd2-44d2-b53b-cf778555f5b6','8da95cab-c4d7-4ee6-bfad-0aef24f90e13',NULL,1,'query','{\"connection\":\"mysql\",\"bindings\":[],\"sql\":\"delete from `customer_to_users`\",\"time\":\"6.50\",\"slow\":false,\"file\":\"\\/Users\\/barryo\\/dev\\/ixp-ibn\\/app\\/Console\\/Commands\\/Upgrade\\/Customer2Users.php\",\"line\":87,\"hostname\":\"barryo-mac3.local\"}','2019-05-11 14:37:17'),(24,'8da95cab-c06c-4157-9837-9306a89d4ffe','8da95cab-c4d7-4ee6-bfad-0aef24f90e13',NULL,1,'query','{\"connection\":\"mysql\",\"bindings\":[1,1],\"sql\":\"update `user_logins` set `customer_to_user_id` = ? where `user_id` = ?\",\"time\":\"0.76\",\"slow\":false,\"file\":\"\\/Users\\/barryo\\/dev\\/ixp-ibn\\/app\\/Console\\/Commands\\/Upgrade\\/Customer2Users.php\",\"line\":114,\"hostname\":\"barryo-mac3.local\"}','2019-05-11 14:37:17'),(25,'8da95cab-c245-4a95-b997-af0077fed669','8da95cab-c4d7-4ee6-bfad-0aef24f90e13',NULL,1,'query','{\"connection\":\"mysql\",\"bindings\":[2,2],\"sql\":\"update `user_logins` set `customer_to_user_id` = ? where `user_id` = ?\",\"time\":\"0.29\",\"slow\":false,\"file\":\"\\/Users\\/barryo\\/dev\\/ixp-ibn\\/app\\/Console\\/Commands\\/Upgrade\\/Customer2Users.php\",\"line\":114,\"hostname\":\"barryo-mac3.local\"}','2019-05-11 14:37:17'),(26,'8da95cab-c430-45bc-b1e2-85088f948c40','8da95cab-c4d7-4ee6-bfad-0aef24f90e13',NULL,1,'query','{\"connection\":\"mysql\",\"bindings\":[3,3],\"sql\":\"update `user_logins` set `customer_to_user_id` = ? where `user_id` = ?\",\"time\":\"0.26\",\"slow\":false,\"file\":\"\\/Users\\/barryo\\/dev\\/ixp-ibn\\/app\\/Console\\/Commands\\/Upgrade\\/Customer2Users.php\",\"line\":114,\"hostname\":\"barryo-mac3.local\"}','2019-05-11 14:37:17'),(27,'8da95cab-c4a3-4d59-a708-7e367c24c07f','8da95cab-c4d7-4ee6-bfad-0aef24f90e13',NULL,1,'command','{\"command\":\"update:customer2users\",\"exit_code\":0,\"arguments\":{\"command\":\"update:customer2users\"},\"options\":{\"help\":false,\"quiet\":false,\"verbose\":false,\"version\":false,\"ansi\":false,\"no-ansi\":false,\"no-interaction\":false,\"env\":null},\"hostname\":\"barryo-mac3.local\"}','2019-05-11 14:37:17'),(28,'8da95ccb-f01e-49fe-942e-bdf31b61c5de','8da95ccb-f13f-4788-9f98-7110591c52d9',NULL,1,'request','{\"uri\":\"\\/\",\"method\":\"GET\",\"controller_action\":\"Closure\",\"middleware\":[\"web\"],\"headers\":{\"upgrade-insecure-requests\":\"1\",\"connection\":\"keep-alive\",\"dnt\":\"1\",\"accept-encoding\":\"gzip, deflate\",\"accept-language\":\"en-IE,en-GB;q=0.7,en;q=0.3\",\"accept\":\"text\\/html,application\\/xhtml+xml,application\\/xml;q=0.9,*\\/*;q=0.8\",\"user-agent\":\"Mozilla\\/5.0 (Macintosh; Intel Mac OS X 10.14; rv:66.0) Gecko\\/20100101 Firefox\\/66.0\",\"host\":\"ixp-ibn.ldev\",\"content-length\":\"\",\"content-type\":\"\"},\"payload\":[],\"session\":{\"_token\":\"FT7s9S0hPibpHMiCebIC9W261NvsXxKEBnGWYUcx\",\"_previous\":{\"url\":\"http:\\/\\/ixp-ibn.ldev\"},\"_flash\":{\"old\":[],\"new\":[]}},\"response_status\":302,\"response\":\"Redirected to http:\\/\\/ixp-ibn.ldev\\/login\",\"duration\":254,\"hostname\":\"barryo-mac3.local\"}','2019-05-11 14:37:38'),(29,'8da95ccc-49b0-4828-bfe3-1f6aad405b18','8da95ccc-4c15-47c1-86f3-ad44c098268d',NULL,1,'request','{\"uri\":\"\\/login\",\"method\":\"GET\",\"controller_action\":\"IXP\\\\Http\\\\Controllers\\\\Auth\\\\LoginController@showLoginForm\",\"middleware\":[\"web\",\"guest\"],\"headers\":{\"upgrade-insecure-requests\":\"1\",\"cookie\":\"XSRF-TOKEN=eyJpdiI6IlErXC9SaXVqZXhubFwvWjVraGp2NmJTUT09IiwidmFsdWUiOiI5K0pqV2EzVzVZbUg2dWZHK2hnemxXN0hlOWNxNVwvd3dWTldmYitDXC9QSzhQR2Y0WWtDYlJPWjN2YUo2bVBtNzUiLCJtYWMiOiIzMjIwOWRjMjZiODE4MTVlNzJhYTY3NjU2MzQ4ZGI2MGUwNjk4MGU1NTViZDQ4NGE4MTk4Mzk5MjVkNTNlNTVlIn0%3D; IXP_Manager=eyJpdiI6InROeFBCRDZoa2JkWGl3MG9XSjlsSXc9PSIsInZhbHVlIjoiNFIyWEhsWmtoSitFU3FPNXAzaE83SEo0czA4NGhycnJvbU1sY1FvNHo3YWpHdDNQR0x5Rjd5U1pvVnFUTXJTNiIsIm1hYyI6ImRiNjFiMTc3NDlmZjk4NmE0MzJlZTdlOGY1ODVmOTEwYjE1MDc5ZGY3NzhiMzFlNjZkMmE0YjVjZDA2YjgxMWIifQ%3D%3D\",\"connection\":\"keep-alive\",\"dnt\":\"1\",\"accept-encoding\":\"gzip, deflate\",\"accept-language\":\"en-IE,en-GB;q=0.7,en;q=0.3\",\"accept\":\"text\\/html,application\\/xhtml+xml,application\\/xml;q=0.9,*\\/*;q=0.8\",\"user-agent\":\"Mozilla\\/5.0 (Macintosh; Intel Mac OS X 10.14; rv:66.0) Gecko\\/20100101 Firefox\\/66.0\",\"host\":\"ixp-ibn.ldev\",\"content-length\":\"\",\"content-type\":\"\"},\"payload\":[],\"session\":{\"_token\":\"FT7s9S0hPibpHMiCebIC9W261NvsXxKEBnGWYUcx\",\"_previous\":{\"url\":\"http:\\/\\/ixp-ibn.ldev\\/login\"},\"_flash\":{\"old\":[],\"new\":[]},\"url\":{\"intended\":\"http:\\/\\/ixp-ibn.ldev\\/login\"}},\"response_status\":200,\"response\":{\"view\":\"\\/Users\\/barryo\\/dev\\/ixp-ibn\\/resources\\/views\\/auth\\/login.foil.php\",\"data\":[]},\"duration\":179,\"hostname\":\"barryo-mac3.local\"}','2019-05-11 14:37:38'),(30,'8da95cda-1832-4535-b4fe-a2df241f2f6d','8da95cda-4b45-4e96-bb41-14253ff97d19',NULL,1,'cache','{\"type\":\"missed\",\"key\":\"vagrant|127.0.0.1\",\"hostname\":\"barryo-mac3.local\"}','2019-05-11 14:37:47'),(31,'8da95cda-4a7d-4c5e-8062-e8e8f6ef2fc6','8da95cda-4b45-4e96-bb41-14253ff97d19',NULL,1,'request','{\"uri\":\"\\/login\",\"method\":\"POST\",\"controller_action\":\"IXP\\\\Http\\\\Controllers\\\\Auth\\\\LoginController@login\",\"middleware\":[\"web\",\"guest\"],\"headers\":{\"upgrade-insecure-requests\":\"1\",\"cookie\":\"XSRF-TOKEN=eyJpdiI6IlhhWWlhd2N0b2pSSmM4Q1BFa2F6dlE9PSIsInZhbHVlIjoiTkRINldoaGNqXC95TCthZHdBa1VzVVwvWDRZMDFYaFlCeVUzSWJYVExRNm5PWk0ydGlNb3RvemVcL3hwYyt1V1FkNiIsIm1hYyI6IjQxNWVlMzhkODllYTU2YzFhZDQwY2I5YTdiZDEzNzc5ZWZlZWY1NjAzM2JhMWFhYTI1YWI2ODhmNTMxNzljNTMifQ%3D%3D; IXP_Manager=eyJpdiI6Im9rbVp3T1ZsZERcL0MrV3F1N0U5NHFBPT0iLCJ2YWx1ZSI6IjlQbnltaE9sVlk2MEFLOVkyU3FQZFwvYktqdXZGQVBWZDI2dnpWV2RseGE0eFVxeU8rdnY2RXVhV0VZcFBYUWl1IiwibWFjIjoiMThlOGU2OTFiZjA0ZjU4ZGU5MTg2NjUyNjg0YzJmYWI2ZTdmZGY5YjZjMTRlMjY1MzFlMjBjNGFlNmQ4NWM5NSJ9\",\"connection\":\"keep-alive\",\"dnt\":\"1\",\"content-length\":\"82\",\"content-type\":\"application\\/x-www-form-urlencoded\",\"referer\":\"http:\\/\\/ixp-ibn.ldev\\/login\",\"accept-encoding\":\"gzip, deflate\",\"accept-language\":\"en-IE,en-GB;q=0.7,en;q=0.3\",\"accept\":\"text\\/html,application\\/xhtml+xml,application\\/xml;q=0.9,*\\/*;q=0.8\",\"user-agent\":\"Mozilla\\/5.0 (Macintosh; Intel Mac OS X 10.14; rv:66.0) Gecko\\/20100101 Firefox\\/66.0\",\"host\":\"ixp-ibn.ldev\"},\"payload\":{\"username\":\"vagrant\",\"password\":\"********\",\"_token\":\"FT7s9S0hPibpHMiCebIC9W261NvsXxKEBnGWYUcx\"},\"session\":{\"_token\":\"DKeD4q0Ssmn2xBbh6e7kEq9wzMUPAECXT2sp8KoJ\",\"_previous\":{\"url\":\"http:\\/\\/ixp-ibn.ldev\\/login\"},\"_flash\":{\"old\":[],\"new\":[]},\"url\":[],\"login_web_59ba36addc2b2f9401580f014c7f58ea4e30989d\":1},\"response_status\":302,\"response\":\"Redirected to http:\\/\\/ixp-ibn.ldev\\/login\",\"duration\":321,\"hostname\":\"barryo-mac3.local\",\"user\":{\"id\":1,\"name\":null,\"email\":\"joe@example.com\"}}','2019-05-11 14:37:47'),(32,'8da95cda-9a9e-4e33-ac8c-93293a1408d1','8da95cda-9bb6-4ab8-bb02-6e9aad6f2983',NULL,1,'request','{\"uri\":\"\\/login\",\"method\":\"GET\",\"controller_action\":\"IXP\\\\Http\\\\Controllers\\\\Auth\\\\LoginController@showLoginForm\",\"middleware\":[\"web\",\"guest\"],\"headers\":{\"upgrade-insecure-requests\":\"1\",\"cookie\":\"XSRF-TOKEN=eyJpdiI6Iklobm5tMVRXUGhXQnlKXC9zUXJQQWtRPT0iLCJ2YWx1ZSI6IkpaY091ZG5RajBsTmdENUJ6NTBOWWg3SGtmaTNEaU93UWVFOU1HWjBmdHpPUEZMUGFlNHd0ak9PRG5yUGMyS2ciLCJtYWMiOiJhNzRmNjgxMDVlYzM0MWUzNDc2MzJmOTJiMWM3NjI1YWExNWYzNDdjOGNmODEzMTI1NTcxOTlhMjgyMzRiNWJlIn0%3D; IXP_Manager=eyJpdiI6IlpQSnFyMFFmSlhxMGxQdWVoSldHVlE9PSIsInZhbHVlIjoib3lBVk1nK2RBTTB5RWd2empYNXMwenhySDFmUlR5ZGgwTFZkbXFEQk90MmFwdmpuQ3V1ZEdvMWwwZ2d6eXdZdCIsIm1hYyI6ImJiODJjNjc2NThjOTg2OGE0MzA0MTEzYjI2Y2U4NzA3MGJmYmIyMjg2N2ExYTY4MWExNTYzZmY0NGQ0YzJlNDEifQ%3D%3D\",\"connection\":\"keep-alive\",\"dnt\":\"1\",\"referer\":\"http:\\/\\/ixp-ibn.ldev\\/login\",\"accept-encoding\":\"gzip, deflate\",\"accept-language\":\"en-IE,en-GB;q=0.7,en;q=0.3\",\"accept\":\"text\\/html,application\\/xhtml+xml,application\\/xml;q=0.9,*\\/*;q=0.8\",\"user-agent\":\"Mozilla\\/5.0 (Macintosh; Intel Mac OS X 10.14; rv:66.0) Gecko\\/20100101 Firefox\\/66.0\",\"host\":\"ixp-ibn.ldev\",\"content-length\":\"\",\"content-type\":\"\"},\"payload\":[],\"session\":{\"_token\":\"DKeD4q0Ssmn2xBbh6e7kEq9wzMUPAECXT2sp8KoJ\",\"_previous\":{\"url\":\"http:\\/\\/ixp-ibn.ldev\\/login\"},\"_flash\":{\"old\":[],\"new\":[]},\"url\":[],\"login_web_59ba36addc2b2f9401580f014c7f58ea4e30989d\":1},\"response_status\":302,\"response\":\"Redirected to http:\\/\\/ixp-ibn.ldev\",\"duration\":153,\"hostname\":\"barryo-mac3.local\",\"user\":{\"id\":1,\"name\":null,\"email\":\"joe@example.com\"}}','2019-05-11 14:37:48'),(33,'8da95cda-eb0a-4e26-a3ae-f0e9faed8ea0','8da95cda-ebe7-4d01-afa2-3b58be3710c3',NULL,1,'request','{\"uri\":\"\\/\",\"method\":\"GET\",\"controller_action\":\"Closure\",\"middleware\":[\"web\"],\"headers\":{\"upgrade-insecure-requests\":\"1\",\"cookie\":\"XSRF-TOKEN=eyJpdiI6InBOUjB6RXdoNXdkQlwvSVVBMWxQaUhnPT0iLCJ2YWx1ZSI6Im9qNnlOdjhlblJpamMxRXNPSTNcL1hrem9xSDEzQkp3M0kzcGVNSXJ4bksxd0hUWmNmYmhKZDh6QVQyYThKeHdkIiwibWFjIjoiZDEwMmFkODBmNzc1NDU3ZTRhN2Q5MzQ2OWZmNjhlYzNhOTU3N2E5NjU4MDc2NTk1OWExYTUwMTgxNWJlMWQyZCJ9; IXP_Manager=eyJpdiI6ImorNnhpS2lmZzZaQm8yaVpuamdmK1E9PSIsInZhbHVlIjoiczFJeWlESTdNNW4xUHJnTm1Gd3IzNjhMeEdhcEd5WHBXd1wvV2NGU0hvU1ZpVTFZRFZHZHk3cmdKTUNVcFcyWXUiLCJtYWMiOiIyMTk5ZmE2MWI2NzNiNWQyZjVlYTZjNDI5MjdlYWUwY2NmNGI0YzJiMzQ0OTdmNTkyNjQ4YmNhMjllNzFjMTJlIn0%3D\",\"connection\":\"keep-alive\",\"dnt\":\"1\",\"referer\":\"http:\\/\\/ixp-ibn.ldev\\/login\",\"accept-encoding\":\"gzip, deflate\",\"accept-language\":\"en-IE,en-GB;q=0.7,en;q=0.3\",\"accept\":\"text\\/html,application\\/xhtml+xml,application\\/xml;q=0.9,*\\/*;q=0.8\",\"user-agent\":\"Mozilla\\/5.0 (Macintosh; Intel Mac OS X 10.14; rv:66.0) Gecko\\/20100101 Firefox\\/66.0\",\"host\":\"ixp-ibn.ldev\",\"content-length\":\"\",\"content-type\":\"\"},\"payload\":[],\"session\":{\"_token\":\"DKeD4q0Ssmn2xBbh6e7kEq9wzMUPAECXT2sp8KoJ\",\"_previous\":{\"url\":\"http:\\/\\/ixp-ibn.ldev\"},\"_flash\":{\"old\":[],\"new\":[]},\"url\":[],\"login_web_59ba36addc2b2f9401580f014c7f58ea4e30989d\":1},\"response_status\":302,\"response\":\"Redirected to http:\\/\\/ixp-ibn.ldev\\/admin\",\"duration\":158,\"hostname\":\"barryo-mac3.local\",\"user\":{\"id\":1,\"name\":null,\"email\":\"joe@example.com\"}}','2019-05-11 14:37:48'),(34,'8da95cdb-324e-4d33-8829-71e48083ee84','8da95cdb-8432-48ac-9613-0089efec57f7',NULL,1,'cache','{\"type\":\"missed\",\"key\":\"admin_ctypes\",\"hostname\":\"barryo-mac3.local\",\"user\":{\"id\":1,\"name\":null,\"email\":\"joe@example.com\"}}','2019-05-11 14:37:48'),(35,'8da95cdb-3f68-4114-aa58-5fdccf8e544c','8da95cdb-8432-48ac-9613-0089efec57f7',NULL,1,'query','{\"connection\":\"mysql\",\"bindings\":[],\"sql\":\"SELECT v.name AS vlanname, COUNT(vli.id) AS overall_count, SUM(vli.rsclient = 1) AS rsclient_count\\n            FROM `vlaninterface` AS vli\\n            LEFT JOIN virtualinterface AS vi ON vli.virtualinterfaceid = vi.id\\n            LEFT JOIN cust AS c ON vi.custid = c.id\\n            LEFT JOIN vlan AS v ON vli.vlanid = v.id\\n            WHERE v.`private` = 0 AND c.type IN (1,4)\\n            GROUP BY vlanname\",\"time\":\"0.96\",\"slow\":false,\"file\":\"\\/Users\\/barryo\\/dev\\/ixp-ibn\\/database\\/Repositories\\/VlanInterface.php\",\"line\":752,\"hostname\":\"barryo-mac3.local\",\"user\":{\"id\":1,\"name\":null,\"email\":\"joe@example.com\"}}','2019-05-11 14:37:48'),(36,'8da95cdb-3ff4-4678-b1fc-56c67c36dcea','8da95cdb-8432-48ac-9613-0089efec57f7',NULL,1,'query','{\"connection\":\"mysql\",\"bindings\":[],\"sql\":\"SELECT v.name AS vlanname, COUNT(vli.id) AS overall_count, SUM(vli.ipv6enabled = 1) AS ipv6_count\\n            FROM `vlaninterface` AS vli\\n            LEFT JOIN virtualinterface AS vi ON vli.virtualinterfaceid = vi.id\\n            LEFT JOIN cust AS c ON vi.custid = c.id\\n            LEFT JOIN vlan AS v ON vli.vlanid = v.id\\n            WHERE v.`private` = 0 AND c.type IN (1,4)\\n            GROUP BY vlanname\",\"time\":\"0.57\",\"slow\":false,\"file\":\"\\/Users\\/barryo\\/dev\\/ixp-ibn\\/database\\/Repositories\\/VlanInterface.php\",\"line\":779,\"hostname\":\"barryo-mac3.local\",\"user\":{\"id\":1,\"name\":null,\"email\":\"joe@example.com\"}}','2019-05-11 14:37:48'),(37,'8da95cdb-406b-4616-8d2f-6829ebf8347f','8da95cdb-8432-48ac-9613-0089efec57f7',NULL,1,'cache','{\"type\":\"set\",\"key\":\"admin_ctypes\",\"value\":{\"types\":{\"1\":\"3\",\"3\":\"1\",\"4\":\"1\"},\"speeds\":{\"10\":2,\"100\":1,\"1000\":4,\"10000\":1},\"custsByLocation\":{\"Location 1\":8},\"byLocation\":{\"Location 1\":{\"1000\":4,\"100\":1,\"10\":2,\"10000\":1}},\"byLan\":{\"Infrastructure #1\":{\"1000\":3,\"100\":1,\"10\":1},\"Infrastructure #2\":{\"1000\":1,\"10\":1,\"10000\":1}},\"byIxp\":{\"VAGRANT IXP\":{\"1000\":1,\"100\":1,\"10\":1,\"10000\":1}},\"rsUsage\":[{\"vlanname\":\"Peering LAN 1\",\"overall_count\":4,\"rsclient_count\":\"4\"},{\"vlanname\":\"Peering LAN 2\",\"overall_count\":3,\"rsclient_count\":\"3\"}],\"ipv6Usage\":[{\"vlanname\":\"Peering LAN 1\",\"overall_count\":4,\"ipv6_count\":\"2\"},{\"vlanname\":\"Peering LAN 2\",\"overall_count\":3,\"ipv6_count\":\"2\"}],\"cached_at\":\"2019-05-11T14:37:48.621150Z\"},\"expiration\":300,\"hostname\":\"barryo-mac3.local\",\"user\":{\"id\":1,\"name\":null,\"email\":\"joe@example.com\"}}','2019-05-11 14:37:48'),(38,'8da95cdb-412f-433a-9660-7912dace02c4','8da95cdb-8432-48ac-9613-0089efec57f7',NULL,1,'cache','{\"type\":\"missed\",\"key\":\"admin_stats_week\",\"hostname\":\"barryo-mac3.local\",\"user\":{\"id\":1,\"name\":null,\"email\":\"joe@example.com\"}}','2019-05-11 14:37:48'),(39,'8da95cdb-5461-4dfa-85d2-12c698a2b6de','8da95cdb-8432-48ac-9613-0089efec57f7',NULL,1,'cache','{\"type\":\"set\",\"key\":\"admin_stats_week\",\"value\":{\"ixp\":{},\"1\":{},\"2\":{}},\"expiration\":300,\"hostname\":\"barryo-mac3.local\",\"user\":{\"id\":1,\"name\":null,\"email\":\"joe@example.com\"}}','2019-05-11 14:37:48'),(40,'8da95cdb-55db-4f8d-8e59-5aba480ec518','8da95cdb-8432-48ac-9613-0089efec57f7',NULL,1,'cache','{\"type\":\"missed\",\"key\":\"admin_home_customers\",\"hostname\":\"barryo-mac3.local\",\"user\":{\"id\":1,\"name\":null,\"email\":\"joe@example.com\"}}','2019-05-11 14:37:48'),(41,'8da95cdb-5764-4b10-80df-c87b152f8919','8da95cdb-8432-48ac-9613-0089efec57f7',NULL,1,'cache','{\"type\":\"set\",\"key\":\"admin_home_customers\",\"value\":{\"4\":\"AS112\",\"2\":\"HEAnet\",\"5\":\"Imagine\",\"3\":\"PCH DNS\",\"1\":\"VAGRANT IXP\"},\"expiration\":3600,\"hostname\":\"barryo-mac3.local\",\"user\":{\"id\":1,\"name\":null,\"email\":\"joe@example.com\"}}','2019-05-11 14:37:48'),(42,'8da95cdb-5a60-4e71-bf02-109c50c0397c','8da95cdb-8432-48ac-9613-0089efec57f7',NULL,1,'cache','{\"type\":\"hit\",\"key\":\"admin_home_customers\",\"value\":{\"4\":\"AS112\",\"2\":\"HEAnet\",\"5\":\"Imagine\",\"3\":\"PCH DNS\",\"1\":\"VAGRANT IXP\"},\"hostname\":\"barryo-mac3.local\",\"user\":{\"id\":1,\"name\":null,\"email\":\"joe@example.com\"}}','2019-05-11 14:37:48'),(43,'8da95cdb-5b5b-4a7c-9781-5619c9e9d1dc','8da95cdb-8432-48ac-9613-0089efec57f7',NULL,1,'cache','{\"type\":\"missed\",\"key\":\"grapher::ixp001-protoall-bits-week-png.png\",\"hostname\":\"barryo-mac3.local\",\"user\":{\"id\":1,\"name\":null,\"email\":\"joe@example.com\"}}','2019-05-11 14:37:48'),(44,'8da95cdb-5cb6-45ad-a7aa-0d7ef2c8cb3f','8da95cdb-8432-48ac-9613-0089efec57f7',NULL,1,'cache','0','2019-05-11 14:37:48'),(45,'8da95cdb-5e34-4529-8aee-e560c34a77a4','8da95cdb-8432-48ac-9613-0089efec57f7',NULL,1,'cache','{\"type\":\"missed\",\"key\":\"grapher::ixp001-protoall-bits-week-png.data\",\"hostname\":\"barryo-mac3.local\",\"user\":{\"id\":1,\"name\":null,\"email\":\"joe@example.com\"}}','2019-05-11 14:37:48'),(46,'8da95cdb-6460-4ee4-9f20-81005e52b3d3','8da95cdb-8432-48ac-9613-0089efec57f7',NULL,1,'cache','{\"type\":\"set\",\"key\":\"grapher::ixp001-protoall-bits-week-png.data\",\"value\":[[1454455800,81294362816,81285576440,88069944432,88106694760],[1454457600,70861891680,70879651504,80870320288,80939228632],[1454459400,61820900952,61836187968,66968352168,67014678488],[1454461200,53303777096,53305052864,62845305440,62931096184],[1454463000,46965082136,46977456320,49870513416,49862850104],[1454464800,40384565960,40392848672,45678738008,45636379632],[1454466600,38216781752,38224697040,40799594304,40807874304],[1454468400,35944029240,35956335128,39960841976,39987092904],[1454470200,32967694816,32978399560,35519703248,35509715920],[1454472000,30070160152,30082385616,33153776584,33172195320],[1454473800,24414319016,24419567880,26577381368,26585448936],[1454475600,23904858944,23912816624,28590035120,28612257208],[1454477400,30063386920,30074640448,31599770560,31591656360],[1454479200,27785035432,27794444608,32122766120,32086708720],[1454481000,29279816552,29284912832,30922314872,30918322360],[1454482800,30666057464,30679904160,35515780640,35503495920],[1454484600,37357081544,37371358112,41662438288,41650997536],[1454486400,43327042600,43340908536,45779987440,45788160344],[1454488200,50489368880,50505400192,54572934248,54572019256],[1454490000,57200462984,57214479368,61853898576,61778503184],[1454491800,63373343256,63393476680,68516850000,68544855408],[1454493600,69064495536,69074671520,74284354992,74181294872],[1454495400,70013051296,70043538464,73622118040,73634910728],[1454497200,73012892712,73015317200,78255883456,78166086880],[1454499000,73352036232,73383848632,76636250720,76666954016],[1454500800,75620013040,75637879848,80283491816,80223183568],[1454502600,76228595344,76243703864,81054704368,81053125928],[1454504400,75999819824,76021488840,87437191816,87489136552],[1454506200,78292126288,78308144544,83569412240,83555038472],[1454508000,78085704544,78111659880,83185345296,83187726680],[1454509800,78345803896,78368818680,81801939440,81804611224],[1454511600,78013894008,78033177688,85710113408,85601906328],[1454513400,79577108928,79609217552,83162806944,83214050000],[1454515200,81293692760,81305309584,88821967104,88636556016],[1454517000,84816123360,84846219120,88918795392,88935110568],[1454518800,87081432392,87091507432,94021487096,93905920584],[1454520600,85651853808,85688961648,90248297272,90225347048],[1454522400,83505885640,83518601184,90176417144,89989158328],[1454524200,83793986488,83805506328,86665926344,86676329168],[1454526000,85511366136,85497869576,96762520456,96761447928],[1454527800,88476854712,88476454824,92160504896,92161351168],[1454529600,90648352872,90620921072,97641977904,97464214088],[1454531400,94026246008,93904977944,102154981240,102208628152],[1454533200,94198091872,94169919552,105749290568,105506634816],[1454535000,97110596728,97097309832,101634669488,101587174880],[1454536800,95556494568,95519992032,106372441136,106175058520],[1454538600,91696338768,91653353688,97470650608,97394349312],[1454540400,86827737208,86786107000,97104688184,96882082448],[1454542200,79701142008,79671642200,86761395400,86724876184],[1454544000,70123725592,70109586560,79172492272,79062640088],[1454545800,60995768104,61013157120,65948718584,65968776328],[1454547600,51537418600,51541293520,60431968360,60464608536],[1454549400,46418530088,46429869168,49621685936,49624261992],[1454551200,40376559632,40385829512,46871778392,46816799256],[1454553000,36917014256,36924532872,40277913368,40279221896],[1454554800,35410467488,35421283280,38138767224,38152503432],[1454556600,33269377160,33277090248,36432315808,36427636560],[1454558400,30547892968,30557396104,34193108352,34194383304],[1454560200,31117152736,31126264400,33853356856,33868214264],[1454562000,27903115128,27911278000,30223005072,30198625336],[1454563800,29934257672,29944865160,31506004712,31492730776],[1454565600,29441624672,29452300504,31908599968,31920348632],[1454567400,30348890248,30362244816,31980203864,32017690152],[1454569200,32179799496,32194323696,38244244752,38223516480],[1454571000,39366868856,39381515616,44505251488,44497151872],[1454572800,45996071472,46009294272,50062178904,50060593008],[1454574600,54807359568,54826227872,58070999064,58095039016],[1454576400,58276891720,58291351432,62305218472,62272611040],[1454578200,65252116728,65276435440,68656748056,68691325488],[1454580000,70086442808,70100528008,75931323424,75882340384],[1454581800,71930873456,71954324240,76252227928,76232277096],[1454583600,73408477528,73422544360,80465551304,80381818024],[1454585400,73455023456,73476718576,76442831336,76449727248],[1454587200,73239310816,73256046544,78661128224,78660634608],[1454589000,74237058536,74259646000,77340780208,77300023376],[1454590800,74964145272,74979110536,82920864392,82946083856],[1454592600,76809569096,76824240448,80064745264,80073796136],[1454594400,78100494176,78123839856,84511234640,84459278928],[1454596200,80466829544,80497494000,85060051544,85097480376],[1454598000,79293975272,79302495264,86915567736,86794316736],[1454599800,80392108824,80429461136,84012543440,83991247112],[1454601600,81984696224,82000466944,90237052568,90099949968],[1454603400,83126675616,83167204552,87233539496,87259043504],[1454605200,86443378304,86456597808,93991535760,93760894992],[1454607000,84513029168,84541310664,87859023048,87861768752],[1454608800,82589188984,82590017384,89199089152,88960602080],[1454610600,81692031704,81710528048,87764791192,87737411496],[1454612400,81436198776,81425187960,90264573040,90276420992],[1454614200,84071655264,84065463216,88066380704,88019011000],[1454616000,86614534032,86575647704,92804890792,92501827776],[1454617800,89907786856,89891653920,94556947296,94526956864],[1454619600,90940322576,90902602992,97276698920,97074876920],[1454621400,91819805536,91790983824,96481755968,96440473776],[1454623200,92839727080,92804172520,100117498896,99991715848],[1454625000,92579642360,92534196984,97562440600,97511791880],[1454626800,87659012520,87616060016,96628441184,96517502616],[1454628600,80090564440,80075514848,86851725672,86812310264],[1454630400,71545922224,71554291416,80467583936,80437053704],[1454632200,62981815624,62994380352,68179041600,68182648104],[1454634000,53779200528,53789997832,60927596696,60876776144],[1454635800,47277584224,47286596712,50927426888,50909429640],[1454637600,41026057808,41039247616,46214662192,46217699320],[1454639400,39383384936,39393919384,41802158416,41810225840],[1454641200,38794575608,38806824920,42883686072,42879516648],[1454643000,35771532760,35782286024,39361694576,39420423120],[1454644800,32261048376,32276826344,34701189880,34725474368],[1454646600,29589233832,29600958336,31761446336,31781482656],[1454648400,29002513576,29014087848,32288004048,32278281648],[1454650200,29895545880,29909371168,31954177568,31968767016],[1454652000,27768558936,27777968400,29728539720,29759542872],[1454653800,29464792272,29472097472,31411361648,31421028200],[1454655600,30423344512,30440973616,35401609136,35419534864],[1454657400,36985723216,36999985512,41006096752,41038368760],[1454659200,44306848280,44322889208,48474948920,48501553440],[1454661000,52106017048,52127083488,56038247120,56071574888],[1454662800,58431305184,58442361528,63086069168,63100869216],[1454664600,65132835200,65154774240,70261450840,70274799136],[1454666400,69027263272,69045404928,73692640008,73615980072],[1454668200,74354528840,74374535976,78701134464,78744029528],[1454670000,74035681200,74059215200,80040231704,80019278488],[1454671800,76155425048,76172657296,79529553000,79586814032],[1454673600,77201223000,77208283480,84144927920,84061288616],[1454675400,76618118088,76645229032,80326782152,80357024160],[1454677200,79549706568,79571666872,89202983328,89164870240],[1454679000,77138576104,77160551048,81251421304,81218265112],[1454680800,77066053152,77082186408,84314591488,84319207408],[1454682600,78949139680,78976938696,83071722112,83113883416],[1454684400,79057866040,79081264416,86101271528,85996828208],[1454686200,79144830352,79178862640,84364029176,84368287792],[1454688000,83198448968,83213494168,90495799128,90247050520],[1454689800,84953897344,84987291232,88368824480,88403817168],[1454691600,85091261696,85110719544,92081300744,91926442608],[1454693400,83391813912,83418296216,86807594640,86801574296],[1454695200,81663347184,81682287888,88283661112,88179522120],[1454697000,81375857456,81407486760,84702708408,84711773792],[1454698800,81688933936,81707529960,88450534256,88293906176],[1454700600,82318779800,82342788136,85426675872,85443623304],[1454702400,84787308568,84809255376,91408951816,91308697504],[1454704200,88754271928,88768685400,92482828864,92560383840],[1454706000,90803521808,90832576440,97675678448,97722071904],[1454707800,91612321776,91643840680,95574520072,95619477240],[1454709600,89608557896,89611301280,96616261504,96506777952],[1454711400,89500657448,89537073720,93721649416,93832095160],[1454713200,86665768776,86681079080,95677260024,95611031704],[1454715000,81781610280,81795772496,87560692520,87544151232],[1454716800,76014803152,76035591240,84960518456,85013712240],[1454718600,70021784376,70030907080,75041417936,75036067680],[1454720400,61331252176,61346404144,69398993992,69565920552],[1454722200,53780100328,53796932144,57557002832,57597809896],[1454724000,46353875272,46365433512,51565310088,51524669976],[1454725800,41243110496,41253014344,43854038928,43880993672],[1454727600,36769041240,36781714640,41075537720,41118023992],[1454729400,33497848976,33508769408,36320000176,36326492688],[1454731200,31045760368,31055440832,34052138040,34054560520],[1454733000,29848131368,29861875104,31416929104,31444169152],[1454734800,27358375856,27369089312,29617380512,29618447760],[1454736600,29378137528,29388613800,31919808512,31939410176],[1454738400,27611983816,27622568328,33403838184,33425163720],[1454740200,27089762760,27099282832,28703305824,28713841776],[1454742000,28737630024,28751976696,32431573736,32458045776],[1454743800,30756620824,30770650680,33567867896,33595693952],[1454745600,35672584032,35679646240,39091242744,39096327632],[1454747400,43631690192,43655557736,47215009296,47232414912],[1454749200,50212727384,50220092648,53546642672,53565627840],[1454751000,56257103664,56281898760,59389403912,59409253128],[1454752800,60004430168,60017699272,63455192864,63471363856],[1454754600,62144861376,62168576608,64350585784,64353294504],[1454756400,63882762576,63894235760,68615160216,68473431120],[1454758200,65726957088,65751822008,68794827056,68795088240],[1454760000,67498977400,67514699648,72489149360,72382598112],[1454761800,69373478600,69398247056,72178800752,72203651264],[1454763600,68556996040,68574036776,77100602488,77117762960],[1454765400,68938490880,68965160696,72240949736,72275569424],[1454767200,70057915504,70074033808,75362620256,75252930240],[1454769000,71577845848,71599414120,74736074312,74750242520],[1454770800,73959031800,73972174216,79940258664,79881389584],[1454772600,77195529776,77223553840,80466923752,80459406832],[1454774400,78513721400,78528805352,84462663952,84454338640],[1454776200,78969925656,79007048192,82615809008,82613047968],[1454778000,78719323720,78736046632,84185175488,84099313736],[1454779800,80959915944,80984503856,84152050328,84139376864],[1454781600,81118758912,81139350208,88388113096,88325246408],[1454783400,82487747352,82507356800,86789030376,86799310160],[1454785200,81723737320,81751659136,88420317368,88423779616],[1454787000,81514561576,81556262880,84660799584,84715692344],[1454788800,82846652816,82862919008,89335489944,89178351248],[1454790600,84816813464,84848066832,89194987320,89200051072],[1454792400,85311473904,85321879280,92615473608,92494265448],[1454794200,87908701736,87941461984,92803561328,92879680872],[1454796000,88546960872,88564808736,96976257264,96808990608],[1454797800,86625768440,86649160472,92563798240,92602694544],[1454799600,83144457928,83161564240,92505249520,92422916056],[1454801400,77998634264,78026186888,82895489248,82973338632],[1454803200,72780651152,72784481088,80267213200,80111372928],[1454805000,68267004384,68287852368,73934205232,73996117104],[1454806800,60477147464,60481616192,67816176208,67737847912],[1454808600,54907568168,54926146008,61041737280,61115098312],[1454810400,47030399904,47031295600,53222990008,53229097016],[1454812200,44998669392,45016382048,47951635440,48021841232],[1454814000,40884028328,40896067808,44915492496,44950109872],[1454815800,35649389592,35661029072,40349572016,40388050600],[1454817600,32954768824,32967110376,37344349656,37381316752],[1454819400,30661975616,30672840912,33084184992,33114596904],[1454821200,28931749064,28940710024,31144626528,31169671712],[1454823000,29538408176,29550405688,32015687992,32025484304],[1454824800,27059677136,27065867048,30969181024,30971519128],[1454826600,27703711848,27718478784,29947035176,29982042216],[1454828400,28359654344,28371151528,33329150880,33348586192],[1454830200,28960660032,28975635304,30932819896,30923375600],[1454832000,31942829064,31950467664,35128894512,35143944800],[1454833800,40435342240,40458916200,46566526872,46602123648],[1454835600,47722869232,47736410976,51900868192,51949814040],[1454837400,55738901680,55760465424,60708801920,60730316856],[1454839200,61086942328,61096311736,65518079544,65369489728],[1454841000,62854760040,62876951528,67179225960,67188450584],[1454842800,65180849360,65190971552,69487103248,69495334440],[1454844600,68553563664,68580929128,71955718784,72003619200],[1454846400,70157175488,70166860976,75890306800,75721886168],[1454848200,68443916048,68461305728,72001921280,72062211600],[1454850000,69710763688,69725118880,75714028376,75615794392],[1454851800,71449959712,71476562496,75005339288,75011123528],[1454853600,72142666040,72157543400,78179738488,78095240208],[1454855400,73705399160,73727919000,77932969768,77956928000],[1454857200,76342537920,76351363864,81806770088,81712828104],[1454859000,81822434792,81830423496,86922176896,86935487032],[1454860800,83265260200,83294779912,90762971896,90889921160],[1454862600,86888626304,86897184032,91536147744,91555520368],[1454864400,86125819144,86166091184,95301490704,95464561840],[1454866200,81847453152,81879707776,85640523488,85704200272],[1454868000,82974618880,82997094144,91062651744,90990369592],[1454869800,83241200320,83269713016,86637064912,86668095288],[1454871600,85909254528,85921878056,98680720360,98691446280],[1454873400,88315410392,88344324992,92066529048,92111042544],[1454875200,89900599928,89915385160,98334423920,98177988568],[1454877000,90367178048,90388415552,94892186864,94925800224],[1454878800,90345120128,90338759160,99092140584,98857492400],[1454880600,93315708416,93335305136,97619499600,97647244152],[1454882400,93470750928,93451614312,103445624824,103253903136],[1454884200,91365124328,91353232704,96256066760,96269899144],[1454886000,84740946320,84727951480,94223274368,94089304408],[1454887800,77301934320,77305954168,83973991760,83957138472],[1454889600,67836065952,67843247336,76283912256,76279249720],[1454891400,60934372800,60948731792,66883576352,66917665880],[1454893200,50892264016,50898265872,59038233416,59064762560],[1454895000,45922650856,45937474288,49353227456,49364606376],[1454896800,38668500408,38675954576,43880770640,43842198568],[1454898600,36530061880,36535207000,39964861504,39955435128],[1454900400,32756280120,32773812648,37160851880,37251710728],[1454902200,31586843792,31601126800,33820380192,33846207712],[1454904000,30634813184,30646727960,32867181960,32849335512],[1454905800,30817581032,30831990736,33498671352,33509499248],[1454907600,28508081712,28519002072,31850589200,31899893384],[1454909400,28514713576,28526004896,30218647944,30208335400],[1454911200,27370284368,27377324168,30896659152,30919054968],[1454913000,26343020464,26353420256,27614120144,27632757200],[1454914800,28320753128,28330517200,32463105424,32485427296],[1454916600,36234435696,36250292520,41030791240,41052216360],[1454918400,43479495656,43494124368,47772260344,47782113784],[1454920200,52311196464,52336421944,56167824344,56210392200],[1454922000,58119150632,58124864776,62430923456,62387087576],[1454923800,66945033720,66969680008,71456705080,71495807984],[1454925600,72319675408,72334758816,78233082616,78207399488],[1454927400,74350189800,74384333984,77351525072,77402259248],[1454929200,75018344400,75026661704,80539634832,80449555392],[1454931000,76143645984,76170238048,80474782448,80425504456],[1454932800,77568531568,77584673824,83851924176,83762926232],[1454934600,78703458696,78723356896,82353935056,82345517744],[1454936400,80538368480,80558048424,87906104496,87855474432],[1454938200,79329207168,79353015192,84701163736,84686792720],[1454940000,78059302936,78075453048,84953279704,84842232016],[1454941800,79648084088,79678243608,83154753848,83192930704],[1454943600,79861005040,79879672944,87632547432,87518351384],[1454945400,80216070024,80245069336,83536516120,83516951152],[1454947200,81035854216,81051648528,89207838304,89036841512],[1454949000,83888855984,83930306664,88203533032,88244742448],[1454950800,86290247128,85940388464,95481893728,92886730448],[1454952600,84341445440,84368681480,87781001968,87784991792],[1454954400,82526607968,82537352144,91065452488,90880267512],[1454956200,83205070744,83223681720,86916244952,86911430432],[1454958000,85974861696,85955119024,94870145824,94884743488],[1454959800,87590515256,87580183840,91873256184,91847332312],[1454961600,90259693744,90226306368,97502486504,97286684704],[1454963400,92742870824,92719081048,97924667472,97894228304],[1454965200,93098099000,93061335312,101107937352,100965391688],[1454967000,95236527320,95191469936,99685203416,99595539920],[1454968800,95407964936,95359657200,103500551976,103368779344],[1454970600,93488040720,93432792688,97732670264,97671919936],[1454972400,87095861600,87063618224,97213971000,97203384152],[1454974200,73853506040,73873037816,80612326656,80619506224],[1454976000,63716405648,63721648896,72607502488,72412285632],[1454977800,55054658984,55067690976,60311020072,60322670728],[1454979600,48498144600,48501415480,56643262584,56658586056],[1454981400,44272526792,44284996752,47014808008,47033847048],[1454983200,38750026464,38763359064,44346460304,44351784144],[1454985000,34199270672,34212675024,37762416896,37789498768],[1454986800,32231608760,32244283600,35247101672,35284426032],[1454988600,31305485912,31317945960,33665030320,33646376688],[1454990400,29637662360,29653914640,33404294208,33487706688],[1454992200,27895272080,27902689280,29698939560,29707282832],[1454994000,26896945440,26911209528,30098653608,30184091408],[1454994300,26997528560,27016147096,29243767472,29273266224],[1454994600,28555356880,28572670008,29243767472,29273266224],[1454994900,27632946808,27626263200,29281823888,29283592392],[1454995200,28731024264,28736591960,29281823888,29283592392],[1454995500,28239276896,28264665096,30242587176,30297833464],[1454995800,29533703312,29563942856,30242587176,30297833464],[1454996100,28845010360,28834004648,32149475432,32193755264],[1454996400,30900827840,30941132192,32149475432,32193755264],[1454996700,28546890864,28571254344,29422495944,29427928056],[1454997000,28831344952,28837382528,29422495944,29427928056],[1454997300,27780462168,27792883232,28399202632,28424414000],[1454997600,27875247016,27897542896,28399202632,28424414000],[1454997900,27125257656,27139617184,28120455504,28130514152],[1454998200,27559443008,27567579760,28120455504,28130514152],[1454998500,26993432256,26999999304,28678699064,28692005056],[1454998800,27947540904,27963277816,28678699064,28692005056],[1454999100,27381309880,27404034840,29982390400,30009595776],[1454999400,29305953504,29324062248,29982390400,30009595776],[1454999700,28561779064,28565849416,31098570112,31122463784],[1455000000,29725569936,29737944008,31098570112,31122463784],[1455000300,28398652928,28396237848,36961402232,37011148112],[1455000600,34289813784,34327101120,36961402232,37011148112],[1455000900,29176750640,29182875872,30881229456,30876170320],[1455001200,30625323256,30632641824,30881229456,30876170320],[1455001500,31077556528,31099358712,33703653712,33693491072],[1455001800,33127250424,33129963312,33703653712,33693491072],[1455002100,33256848936,33278918304,36908125928,36907790360],[1455002400,36710826736,36717719952,36908125928,36907790360],[1455002700,37089449752,37102290152,39245528432,39232063640],[1455003000,38450960760,38460627608,39245528432,39232063640],[1455003300,38228692536,38274212720,43842677688,43835296864],[1455003600,42834358024,42835448160,43842677688,43835296864],[1455003900,41548202080,41564877552,43874263672,43883895480],[1455004200,43079733976,43094350272,43874263672,43883895480],[1455004500,42230153856,42251023712,44747117736,44754653136],[1455004800,44701558904,44713942656,44747117736,44754653136],[1455005100,45672205592,45700062048,48526541432,48566391112],[1455005400,47718891360,47749777232,48526541432,48566391112],[1455005700,47123721624,47142526784,50174802048,50212914120],[1455006000,49678311112,49707115736,50174802048,50212914120],[1455006300,49624524048,49632136032,52373118320,52379485736],[1455006600,51113208984,51152829800,52373118320,52379485736],[1455006900,50206778984,50270220832,56759019024,56649152680],[1455007200,54998899416,54923776480,56759019024,56649152680],[1455007500,52410440416,52421813464,55470254240,55510774624],[1455007800,54903742760,54937434824,55470254240,55510774624],[1455008100,55151866848,55170196080,58933399960,58950114240],[1455008400,58432274040,58443166808,58933399960,58950114240],[1455008700,59654960648,59654018912,65678837720,65679798584],[1455009000,64955535872,64957204376,65678837720,65679798584],[1455009300,64503760904,64525826616,67370427576,67440163656],[1455009600,65973905624,66028283096,67370427576,67440163656],[1455009900,64637866160,64653405624,68880974400,68882106936],[1455010200,66932647592,66939890728,68880974400,68882106936],[1455010500,64672315208,64696440552,71937087448,71973833704],[1455010800,69721421584,69757115608,71937087448,71973833704],[1455011100,66038508624,66054881008,69249529656,69222240296],[1455011400,67849782152,67849005720,69249529656,69222240296],[1455011700,66222551440,66265585960,70049701960,70053646392],[1455012000,69242723760,69258310048,70049701960,70053646392],[1455012300,68932630536,68957902528,72691549048,72675776704],[1455012600,71182860808,71184290240,72691549048,72675776704],[1455012900,69068109560,69115498784,72318656504,72386103784],[1455013200,70374610608,70416969352,72318656504,72386103784],[1455013500,67595984768,67600373168,71712579944,71764481352],[1455013800,69746853992,69805480536,71712579944,71764481352],[1455014100,67785133832,67831034600,77050264040,76989853144],[1455014400,74095400816,74063279696,77050264040,76989853144],[1455014700,69105139064,69138139416,72728835216,72772489840],[1455015000,70908607104,70940345456,72728835216,72772489840],[1455015300,68213344392,68222541808,71818885632,71838670352],[1455015600,70676665504,70692772656,71818885632,71838670352],[1455015900,69905450360,69915335024,74802386552,74817927352],[1455016200,73441907160,73460362144,74802386552,74817927352],[1455016500,71484836072,71513912792,74332375240,74372445864],[1455016800,73075314264,73108008144,74332375240,74372445864],[1455017100,71589785384,71604165560,75196148816,75206179992],[1455017400,72904394000,72944930688,75196148816,75206179992],[1455017700,70228145520,70293697896,80365006672,80254065720],[1455018000,77526182768,77461481752,80365006672,80254065720],[1455018300,73187682328,73223005104,77783809336,77819756640],[1455018600,75942398672,75977052760,77783809336,77819756640],[1455018900,73412768448,73444890664,77187889088,77220687800],[1455019200,75721614248,75741161040,77187889088,77220687800],[1455019500,74435880264,74434527648,79346702040,79365472488],[1455019800,78171472200,78201439088,79346702040,79365472488],[1455020100,76821345608,76865794920,80130484664,80147642440],[1455020400,77874256264,77899380648,80130484664,80147642440],[1455020700,74501618800,74529626776,78813618648,78802805768],[1455021000,75950236496,75968816048,78813618648,78802805768],[1455021300,72414236768,72463991800,82769117576,82692514960],[1455021600,78001062704,77966150128,82769117576,82692514960],[1455021900,71146516504,71200188488,90311723920,90337707184],[1455022200,85057768560,85068444408,90311723920,90337707184],[1455022500,75520180104,75528993584,80305264600,80396943128],[1455022800,78231837360,78301885704,80305264600,80396943128],[1455023100,75113336952,75135626304,79091962720,79115277288],[1455023400,77093653296,77110413104,79091962720,79115277288],[1455023700,74308783296,74326282264,78729876472,78788127552],[1455024000,76709864016,76754922440,78729876472,78788127552],[1455024300,73956795784,73977676520,78590860400,78625529936],[1455024600,75868316320,75913141000,78590860400,78625529936],[1455024900,72697970752,72734189296,83882294008,83795068496],[1455025200,80927458312,80873705480,83882294008,83795068496],[1455025500,75688031048,75721058776,78974151696,79042871720],[1455025800,77546967600,77594913040,78974151696,79042871720],[1455026100,75590605648,75593481704,79103746016,79113650320],[1455026400,77427030504,77441388264,79103746016,79113650320],[1455026700,75349445784,75375200184,79747886808,79777586824],[1455027000,78378562768,78412239040,79747886808,79777586824],[1455027300,77086570592,77134779552,81882227280,81945172232],[1455027600,79981209312,80028380584,81882227280,81945172232],[1455027900,76986501440,76992791272,80451228080,80443642392],[1455028200,77950953096,77971532360,80451228080,80443642392],[1455028500,74881495088,74943282960,84798178136,84773715272],[1455028800,81627885152,81614522824,84798178136,84773715272],[1455029100,76319217272,76326214024,80325849480,80323448008],[1455029400,78452546352,78468950568,80325849480,80323448008],[1455029700,75747611416,75800724744,79408869240,79450208744],[1455030000,78061584744,78086828200,79408869240,79450208744],[1455030300,76551873808,76558786248,80502420152,80555772656],[1455030600,78976651984,79024117856,80502420152,80555772656],[1455030900,77205500816,77239874992,81383849728,81418251656],[1455031200,79134726360,79157940232,81383849728,81418251656],[1455031500,75944794416,75976223992,80519253120,80639668144],[1455031800,78039367064,78155024224,80519253120,80639668144],[1455032100,75048053736,75108285088,87094254400,86956265664],[1455032400,83612504280,83505420952,87094254400,86956265664],[1455032700,77611007488,77605728992,82337636080,82421621344],[1455033000,80424282240,80480684312,82337636080,82421621344],[1455033300,77834526680,77841962456,82271623568,82312101120],[1455033600,80380078016,80413621624,82271623568,82312101120],[1455033900,77725726312,77741505808,82038367296,82048597424],[1455034200,80579684480,80592640384,82038367296,82048597424],[1455034500,78941799968,78963732584,83354989504,83384403496],[1455034800,81659112920,81690068544,83354989504,83384403496],[1455035100,79614978816,79654002384,84061012728,84112503400],[1455035400,81319109240,81422628544,84061012728,84112503400],[1455035700,78132175536,78269462024,90043346272,89845526432],[1455036000,86860219952,86731025488,90043346272,89845526432],[1455036300,81881949680,81893353976,87192422240,87177791384],[1455036600,85202551352,85216681392,87192422240,87177791384],[1455036900,82300036032,82351064120,86358430712,86337364272],[1455037200,84734183696,84728167448,86358430712,86337364272],[1455037500,82530119184,82557994200,86567521000,86595024088],[1455037800,84405025136,84425585512,86567521000,86595024088],[1455038100,81281008192,81289297240,85697560280,85714168048],[1455038400,83701465648,83717783432,85697560280,85714168048],[1455038700,80898070096,80907738008,84765412624,84759589920],[1455039000,82350802760,82411275120,84765412624,84759589920],[1455039300,79177439264,79292610784,87776229768,87533323152],[1455039600,84438792200,84274906568,87776229768,87533323152],[1455039900,78549724672,78553172544,81925422608,81920679312],[1455040200,79949766496,79932208328,81925422608,81920679312],[1455040500,77267822144,77239250936,81905981952,81924753616],[1455040800,80224017288,80228703432,81905981952,81924753616],[1455041100,78482163384,78467573848,83715978272,83732960848],[1455041400,82047186680,82049504712,83715978272,83732960848],[1455041700,80258438480,80240253000,85292992400,85306538432],[1455042000,83413810296,83418343696,85292992400,85306538432],[1455042300,81428595544,81409632992,87294325824,87265064904],[1455042600,84542686976,84556110696,87294325824,87265064904],[1455042900,81260824200,81302015424,92631835024,92404065344],[1455043200,88579304048,88415578104,92631835024,92404065344],[1455043500,82300169080,82273109584,90980048672,90934361200],[1455043800,88223074736,88169093824,90980048672,90934361200],[1455044100,83648466568,83607357136,87484584416,87524644624],[1455044400,85515004648,85535962184,87484584416,87524644624],[1455044700,82887883952,82867679384,87480918088,87464260680],[1455045000,85389782872,85367840408,87480918088,87464260680],[1455045300,82695215336,82659009032,87819277400,87776534136],[1455045600,85799276880,85750662064,87819277400,87776534136],[1455045900,82956304232,82915274168,87351284008,87365162536],[1455046200,85339618168,85366708448,87351284008,87365162536],[1455046500,83677873288,83685005160,95223350016,95031168120],[1455046800,92075294768,91930208000,95223350016,95031168120],[1455047100,86459459104,86414906136,90157818536,90101858144],[1455047400,88625247912,88569182448,90157818536,90101858144],[1455047700,86873025712,86817662784,91426354688,91373491024],[1455048000,90540015112,90493074088,91426354688,91373491024],[1455048300,90511439544,90483851776,95758874616,95746321784],[1455048600,94183383680,94156801912,95758874616,95746321784],[1455048900,92446469312,92387272872,97533544232,97473281496],[1455049200,95441351840,95396379664,97533544232,97473281496],[1455049500,92452981056,92416703416,97242940184,97135505064],[1455049800,94629575704,94602596768,97242940184,97135505064],[1455050100,91431778584,91512032936,103031991896,102790254984],[1455050400,99203271872,99028649952,103031991896,102790254984],[1455050700,92828672840,92791878568,98537589544,98470069704],[1455051000,96364872712,96310789304,98537589544,98470069704],[1455051300,93138510808,93098594384,97417917872,97336156336],[1455051600,95829803544,95766382712,97417917872,97336156336],[1455051900,94672032352,94632776088,101139582280,101054329696],[1455052200,99189459104,99114044000,101139582280,101054329696],[1455052500,96208952040,96153068368,100006583968,99943496128],[1455052800,97300332784,97231034448,100006583968,99943496128],[1455053100,93268881848,93204377016,98565059976,98550048768],[1455053400,95434123176,95429843680,98565059976,98550048768],[1455053700,91390611952,91364997136,102852221704,102649425488],[1455054000,99090777472,98946787968,102852221704,102649425488],[1455054300,92918739992,92884130368,99118705984,99021422888],[1455054600,97517969592,97415310280,99118705984,99021422888],[1455054900,95169439536,95097934760,98397964584,98441098416],[1455055200,96166075432,96178905312,98397964584,98441098416],[1455055500,93086773744,93009284448,98024050744,97886772896],[1455055800,96083081008,95993608880,98024050744,97886772896],[1455056100,93717384928,93704018128,99185850912,99086027360],[1455056400,96827253120,96743157488,99185850912,99086027360],[1455056700,92825047272,92769778664,96953317024,96876401888],[1455057000,94029893568,93975406752,96953317024,96876401888],[1455057300,89703711904,89660065080,99281828936,99078903440],[1455057600,95350204928,95194407384,99281828936,99078903440],[1455057900,87845615464,87797144928,91215632384,91173729952],[1455058200,88773297944,88719101224,91215632384,91173729952],[1455058500,84557073768,84483290360,87746554312,87693106256],[1455058800,85838841760,85783272552,87746554312,87693106256],[1455059100,83060305312,83009165848,86962139632,86935328496],[1455059400,84661293280,84627166768,86962139632,86935328496],[1455059700,81060978744,81002702368,85037370688,84958525720],[1455060000,82597658992,82530203064,85037370688,84958525720],[1455060300,78267503432,78237497456,80924960344,80927611536],[1455060600,78077890864,78067570208,80924960344,80927611536],[1455060900,73798424544,73745453232,81538766648,81433964448],[1455061200,78183656544,78101321192,81538766648,81433964448],[1455061500,71305012504,71265692824,73216050512,73155698864],[1455061800,71453325936,71394328576,73216050512,73155698864],[1455062100,68033672472,67989155808,69940441440,69927056720],[1455062400,68980620904,68947727984,69940441440,69927056720],[1455062700,67390119592,67350429608,69306464512,69377142240],[1455063000,66891074680,66935143792,69306464512,69377142240],[1455063300,61831356248,61820699872,63443232768,63453169424],[1455063600,61856827176,61870061576,63443232768,63453169424],[1455063900,58624875704,58649997128,60333124656,60369663856],[1455064200,58015989928,58043615896,60333124656,60369663856],[1455064500,53880685040,53886141944,59094139848,59095613336],[1455064800,55281784232,55282544824,59094139848,59095613336],[1455065100,48905361704,48912135752,58300635888,58341265472],[1455065400,54657716608,54690801176,58300635888,58341265472],[1455065700,47611427064,47620508784,49777461232,49766015064],[1455066000,49110558680,49083554248,49777461232,49766015064],[1455066300,48339673152,48318968808,50298733184,50388292440],[1455066600,48843940512,48908541744,50298733184,50388292440],[1455066900,46415288440,46426136792,48496692320,48512166296],[1455067200,46649537744,46669275808,48496692320,48512166296],[1455067500,42836521544,42862085072,43818396840,43833645560],[1455067800,42291356536,42298133288,43818396840,43833645560],[1455068100,39642080320,39645777408,43066089832,43138314456],[1455068400,41292661360,41348172256,43066089832,43138314456],[1455068700,37498099336,37515383728,39029098088,39053916952],[1455069000,37872115464,37894065344,39029098088,39053916952],[1455069300,35222042232,35232543256,35612543264,35610130736],[1455069600,34857925880,34847281576,35612543264,35610130736],[1455069900,33739848936,33726763856,35649503672,35685016640],[1455070200,34812435920,34836475512,35649503672,35685016640],[1455070500,33059329448,33069502600,33888125064,33938088152],[1455070800,33187793592,33231962088,33888125064,33938088152],[1455071100,31832850232,31853085776,32613801024,32604908632],[1455071400,31820316752,31810533432,32613801024,32604908632],[1455071700,30723298960,30719529208,33850150424,33877704112],[1455072000,33174779680,33185087744,33850150424,33877704112],[1455072300,31531621488,31528068592,31694711904,31667226344],[1455072600,30360366464,30402474472,31105228480,31164246528],[1455072900,29135284200,29142180560,30266916720,30280157928],[1455073200,29959632928,29969604096,30266916720,30280157928],[1455073500,30202861200,30209903288,32667809816,32686414944],[1455073800,31675619296,31684634024,32667809816,32686414944],[1455074100,29861355920,29859811264,30909246488,30936379464],[1455074400,30530590168,30547225032,30909246488,30936379464],[1455074700,30262904480,30262266528,31853782264,31870615792],[1455075000,30998244400,31005229984,31853782264,31870615792],[1455075300,29780734808,29781267256,32204869784,32261188592],[1455075600,31143441160,31195027896,32204869784,32261188592],[1455075900,29227019528,29256414024,30204904168,30202975040],[1455076200,29106788920,29108831400,30204904168,30202975040],[1455076500,27010476912,27021255944,27919994488,27930502960],[1455076800,27484464720,27483379464,27919994488,27930502960],[1455077100,27310705960,27300962936,29347327424,29381078784],[1455077400,28404316784,28425114304,29347327424,29381078784],[1455077700,26690527424,26693030432,27714515408,27745034728],[1455078000,27379646480,27409549784,27714515408,27745034728],[1455078300,27081276576,27100498304,28249896144,28244776056],[1455078600,27562810480,27559919728,28249896144,28244776056],[1455078900,26656852024,26667098296,29001877616,29042877368],[1455079200,27707917464,27734171648,29001877616,29042877368],[1455079500,25213273872,25217971728,25991300440,26022830616],[1455079800,26063091912,26085549352,26222885848,26225149144],[1455080100,26575177672,26583716520,27481070960,27505746936],[1455080400,26766586712,26788543192,27481070960,27505746936],[1455080700,25833749192,25855062360,27461327448,27496311456],[1455081000,26896122592,26919900776,27461327448,27496311456],[1455081300,26449499192,26453044760,28404808280,28418340944],[1455081600,27562206984,27568212592,28404808280,28418340944],[1455081900,26322588768,26323718200,27786173880,27816053704],[1455082200,27033630392,27050041224,27786173880,27816053704],[1455082500,26042264704,26042598536,28284920504,28331301488],[1455082800,27031148888,27068048728,28284920504,28331301488],[1455083100,24828721936,24844807432,26101218952,26116406448],[1455083400,25643173128,25655252912,26101218952,26116406448],[1455083700,25721702136,25726966312,28420441992,28425429248],[1455084000,28293065784,28291453696,28420441992,28425429248],[1455084300,28548749072,28544571136,29890722760,29916075312],[1455084600,29075301648,29100240576,29890722760,29916075312],[1455084900,28185745496,28209928464,30386426720,30410955240],[1455085200,29499436408,29514404064,30386426720,30410955240],[1455085500,28262904512,28261060688,30081670584,30090475232],[1455085800,28891631816,28905143696,30081670584,30090475232],[1455086100,27328712744,27351533392,31033848248,31053098328],[1455086400,29801009768,29819850344,31033848248,31053098328],[1455086700,28117716472,28133353168,31101656656,31110207288],[1455087000,30440405280,30450270392,31101656656,31110207288],[1455087300,30081772128,30090568560,32944246336,32942771344],[1455087600,33846614104,33845827704,35855110128,35855856392],[1455087900,36641122752,36652470344,38730766072,38770297688],[1455088200,37739576224,37775933000,38730766072,38770297688],[1455088500,36572185248,36593896536,39426283744,39428142144],[1455088800,38691676672,38696633152,39426283744,39428142144],[1455089100,37712378992,37729327128,39594295368,39624207272],[1455089400,39153293072,39182580808,39594295368,39624207272],[1455089700,39721246664,39744386888,45668084568,45673491200],[1455090000,44121416896,44130374520,45668084568,45673491200],[1455090300,42004268904,42017729576,45142435232,45147771176],[1455090600,45036310216,45042742352,45142435232,45147771176],[1455090900,46138112432,46157964864,49695276832,49744320968],[1455091200,48704861744,48745032208,49695276832,49744320968],[1455091500,48320473128,48345623600,53159232144,53196960384],[1455091800,52710264952,52726269864,53159232144,53196960384],[1455092100,53150962544,53146168648,56979286240,57047743280],[1455092400,55731221072,55772544928,56979286240,57047743280],[1455092700,54426649488,54429985104,58166762000,58226240064],[1455093000,56983345856,57038807040,58166762000,58226240064],[1455093300,56367565200,56399948976,63664404272,63645678720],[1455093600,62334278008,62315130936,63664404272,63645678720],[1455093900,60675006752,60676125264,63789009880,63844578816],[1455094200,62792702408,62834612000,63789009880,63844578816],[1455094500,62634565840,62636187904,67929524440,67904078400],[1455094800,67390912600,67391237312,67929524440,67904078400],[1455095100,67616795608,67674495400,71280383688,71338120488],[1455095400,69667481984,69711188312,71280383688,71338120488],[1455095700,68152401296,68169141144,73577208688,73605093008],[1455096000,73637539000,73646417440,73771822616,73738397632],[1455096300,76144234568,76141724952,82346805584,82425122912],[1455096600,78755313152,78859023456,82346805584,82425122912],[1455096900,72377971344,72495890392,78583726208,78539225160],[1455097200,76220505064,76206568656,78583726208,78539225160],[1455097500,72892382512,72929459568,77651714752,77647729648],[1455097800,75920424816,75925632312,77651714752,77647729648],[1455098100,73166972576,73177371168,76043043696,76013519864],[1455098400,74727278608,74735070696,76043043696,76013519864],[1455098700,74679439840,74725785480,82093117824,82026456424],[1455099000,79377992504,79358617984,82093117824,82026456424],[1455099300,75278515280,75356620704,80446354720,80503799336],[1455099600,78189871888,78231038880,80446354720,80503799336],[1455099900,75053022576,75057491952,80066078072,80069306472],[1455100200,78330502504,78319023624,80066078072,80069306472],[1455100500,76431231008,76411266624,83969626808,84042751400],[1455100800,80860915456,80920334192,83969626808,84042751400],[1455101100,75600768920,75638655720,79728309992,79788805752],[1455101400,77875616696,77926698776,79728309992,79788805752],[1455101700,74667556776,74709395728,77451116024,77525860472],[1455102000,76152664216,76195403472,77451116024,77525860472],[1455102300,75640083456,75622772888,82081848960,82097264312],[1455102600,80143787024,80178201664,82081848960,82097264312],[1455102900,76937368032,76996489824,79881250040,79893630512],[1455103200,78071203584,77697806688,79881250040,79893630512],[1455103500,75326487392,74212257776,78740305888,77939339784],[1455103800,76572577008,76002536088,78740305888,77939339784],[1455104100,73976297056,73959697128,83310422928,83384229064],[1455104400,80723821584,80800311840,83310422928,83384229064],[1455104700,76058777496,76117968824,78682295072,78680880232],[1455105000,77604061312,77595811656,78682295072,78680880232],[1455105300,76745626144,76737563376,80944281448,80977796936],[1455105600,79540016752,79579763080,80944281448,80977796936],[1455105900,77121693368,77168260168,78873955408,78902352864],[1455106200,77182871744,77209300000,78873955408,78902352864],[1455106500,75152113784,75175080096,79683667208,79709041680],[1455106800,77767379064,77785911160,79683667208,79709041680],[1455107100,74645194152,74659967360,77862801352,77908708256],[1455107400,75568464040,75601448472,77862801352,77908708256],[1455107700,72868083160,72886997512,82223250040,82299142008],[1455108000,78407664872,78459271736,82223250040,82299142008],[1455108300,72483440456,72488282592,82356528936,82392707704],[1455108600,79125142720,79156133872,82356528936,82392707704],[1455108900,73234816432,73264208720,76639138544,76694538832],[1455109200,75317974680,75349751528,76639138544,76694538832],[1455109500,73422129104,73408859784,76199793280,76206556176],[1455109800,74833009720,74841116576,76199793280,76206556176],[1455110100,73507246784,73536051536,78197852824,78273605720],[1455110400,76514553880,76574953312,78197852824,78273605720],[1455110700,74501727144,74533423688,78960252296,79006017072],[1455111000,76451744120,76499064080,78960252296,79006017072],[1455111300,73728061712,73757663712,84375439136,84323944144],[1455111600,81281609600,81244905736,84375439136,84323944144],[1455111900,75572990240,75588578184,78756997088,78824954760],[1455112200,77446698688,77494767024,78756997088,78824954760],[1455112500,75641053920,75644346552,78841192576,78846889072],[1455112800,77528346544,77544030848,78841192576,78846889072],[1455113100,76389627600,76425186784,81335931024,81364173336],[1455113400,79805622896,79838400960,81335931024,81364173336],[1455113700,77968781504,77998552368,82356853856,82350768424],[1455114000,80789290688,80780431872,82356853856,82350768424],[1455114300,78891791152,78908765656,83495463048,83599504776],[1455114600,80750403200,80847851176,83495463048,83599504776],[1455114900,77133582648,77182791104,87073543992,86994800920],[1455115200,83812114536,83765932608,87073543992,86994800920],[1455115500,78130028008,78175675200,82025585416,82125657448],[1455115800,80582008432,80640741760,82025585416,82125657448],[1455116100,78723038456,78712904960,82323103696,82374502408],[1455116400,80915247808,80954611280,82323103696,82374502408],[1455116700,79081355400,79099183528,82536714696,82568507496],[1455117000,80890245464,80920423824,82536714696,82568507496],[1455117300,78229801792,78264272512,81053190240,81108776096],[1455117600,79262752328,79314149032,81053190240,81108776096],[1455117900,77170627552,77200738928,82119909864,82118749480],[1455118200,79620934360,79659513384,82119909864,82118749480],[1455118500,76333104472,76412302576,84889202888,84788457224],[1455118800,83180456120,83113847592,84889202888,84788457224],[1455119100,79886158344,79913412384,82519833344,82571608192],[1455119400,81013695584,81056940096,82519833344,82571608192],[1455119700,78549004376,78566810808,81969607424,81974675640],[1455120000,80803765232,80819016888,81969607424,81974675640],[1455120300,79665470000,79707782872,84419470744,84468979752],[1455120600,82942155536,82981647736,84419470744,84468979752],[1455120900,81034541680,81053144352,85654204776,85681876448],[1455121200,83680484504,83714219712,85654204776,85681876448],[1455121500,80689081008,80717627544,85410832464,85384649288],[1455121800,82842068856,82876593504,85410832464,85384649288],[1455122100,79178299704,79289577640,87943585232,87810721880],[1455122400,85278283208,85186032192,87943585232,87810721880],[1455122700,80447695280,80463628800,84201653048,84253876816],[1455123000,82760741400,82802889160,84201653048,84253876816],[1455123300,81115544040,81138350624,85837101016,85871373744],[1455123600,84116769824,84139634192,85837101016,85871373744],[1455123900,81770614672,81768628864,86109883816,86111551920],[1455124200,84273554880,84279688464,86109883816,86111551920],[1455124500,81632513912,81668345928,85716888856,85805720400],[1455124800,83926923008,83990908712,85716888856,85805720400],[1455125100,80809080448,80810788432,83449480224,83437326056],[1455125400,81238539888,81282194096,83449480224,83437326056],[1455125700,78550423048,78644821552,87540461712,87343572168],[1455126000,84560414464,84430959304,87540461712,87343572168],[1455126300,78759452976,78778840680,81533164912,81536511280],[1455126600,79582344792,79587321776,81533164912,81536511280],[1455126900,76790440888,76810327456,80911821568,80961696784],[1455127200,80194675288,80234253032,80911821568,80961696784],[1455127500,80618772120,80636489328,86056731656,86078225856],[1455127800,84259428168,84258630536,86056731656,86078225856],[1455128100,81980377832,81942900072,86556780992,86553697232],[1455128400,84331212352,84332111048,86556780992,86553697232],[1455128700,80977995704,80980135824,85029853632,85012091744],[1455129000,82748418832,82757649576,85029853632,85012091744],[1455129300,80124464176,80143058200,89544955680,89368861528],[1455129600,85426733872,85290920992,89544955680,89368861528],[1455129900,79196813376,79159346000,90119798112,90108123240],[1455130200,86717149680,86694229208,90119798112,90108123240],[1455130500,80875158512,80848832032,85478801824,85509964936],[1455130800,84185812848,84205608616,85478801824,85509964936],[1455131100,82951653264,82939516328,87442240168,87413533528],[1455131400,85785736096,85762501224,87442240168,87413533528],[1455131700,83653186032,83650246400,87940314440,87958441760],[1455132000,85965467704,85960110008,87940314440,87958441760],[1455132300,83315561208,83274799400,88306713224,88314605664],[1455132600,86005467664,86026848784,88306713224,88314605664],[1455132900,83117605208,82563186208,92004039896,89122225576],[1455133200,88870003264,86830663040,92004039896,89122225576],[1455133500,82708332144,82669665392,86137873944,86108257144],[1455133800,85083989512,85051626160,86137873944,86108257144],[1455134100,84210923120,84175111864,88680961792,88653575352],[1455134400,87397411160,87364186696,88680961792,88653575352],[1455134700,86305907384,86251328992,91540388568,91464547512],[1455135000,89677040128,89611538504,91540388568,91464547512],[1455135300,86763043848,86722354736,91042535184,91002699760],[1455135600,89593090336,89545662512,91042535184,91002699760],[1455135900,87755264152,87695615968,92304835688,92261637136],[1455136200,89855199096,89846169736,92304835688,92261637136],[1455136500,86514226264,86538488152,96922140704,96749073328],[1455136800,94623288880,94479862440,96922140704,96749073328],[1455137100,90716468696,90655239616,94889004616,94860017272],[1455137400,92831623472,92793175016,94889004616,94860017272],[1455137700,89872594776,89807575424,94647782624,94569673152],[1455138000,93473063432,93393668728,94647782624,94569673152],[1455138300,93054581672,92972099504,99103862144,99020888624],[1455138600,96929200968,96859353256,99103862144,99020888624],[1455138900,93464953936,93416433912,97391754336,97320663640],[1455139200,95502482912,95455103008,97391754336,97320663640],[1455139500,92940134000,92931522808,97826188192,97774435192],[1455139800,95081210896,95052904160,97826188192,97774435192],[1455140100,91654960992,91649110104,103089314424,102959657112],[1455140400,99785748424,99668938896,103089314424,102959657112],[1455140700,93849620504,93755274552,97924635744,97812787104],[1455141000,95736573656,95660115232,97924635744,97812787104],[1455141300,92525968664,92502126856,96938069880,96844693664],[1455141600,95094881616,95009749040,96938069880,96844693664],[1455141900,92499318736,92431743464,96677595448,96607147624],[1455142200,94702145424,94632608352,96677595448,96607147624],[1455142500,91839418200,91775341312,95918257184,95863307072],[1455142800,93865936640,93806444472,95918257184,95863307072],[1455143100,90638504752,90575499240,94393946008,94348900640],[1455143400,91304926992,91246608088,94393946008,94348900640],[1455143700,86652705984,86593916832,95187452904,95240270760],[1455144000,91546337064,91570422112,95187452904,95240270760],[1455144300,84150840304,84102287264,86412735112,86343425824],[1455144600,84515482416,84447580856,86412735112,86343425824],[1455144900,81058226192,81003118544,83724809688,83695852376],[1455145200,82321854224,82286225448,83724809688,83695852376],[1455145500,80354470112,80308018248,84155456888,84122974224],[1455145800,82107256280,82063751096,84155456888,84122974224],[1455146100,78339283192,78282702840,81489877304,81471111008],[1455146400,79236105952,79204999632,81489877304,81471111008],[1455146700,74753427016,74698713088,77448339480,77411045816],[1455147000,74849079056,74820744656,77448339480,77411045816],[1455147300,70490494520,70480457120,77557477800,77535907592],[1455147600,74201101248,74176081336,77557477800,77535907592],[1455147900,67537766792,67519014224,69720494120,69739446704],[1455148200,67619241712,67629905800,69720494120,69739446704],[1455148500,63798111216,63785411752,66609071280,66584266200],[1455148800,65247584096,65229675432,66609071280,66584266200],[1455149100,62914094456,62923836368,65415886656,65457241728],[1455149400,63805546296,63825678296,65415886656,65457241728],[1455149700,60760006688,60756186440,62684830760,62750139448],[1455150000,60614298568,60652411296,62684830760,62750139448],[1455150300,56332716352,56330274544,58087309720,58151786952],[1455150600,55748041904,55799578776,58087309720,58151786952],[1455150900,51428904016,51451077032,55801784264,55827144624],[1455151200,52872380768,52891488480,55801784264,55827144624],[1455151500,47870480240,47878298096,53508032368,53525749472],[1455151800,50993427800,51003866320,53508032368,53525749472],[1455152100,45731937120,45728995688,46877502464,46882877584],[1455152400,45849838616,45852561120,46877502464,46882877584],[1455152700,44300335984,44303425704,46633380120,46654500576],[1455153000,45392295584,45414052200,46633380120,46654500576],[1455153300,42823630200,42838909416,43585365080,43579181024],[1455153600,42717099936,42719834816,43585365080,43579181024],[1455153900,40786328664,40804836392,40957678936,40963448704],[1455154200,39659204088,39669306248,40957678936,40963448704],[1455154500,37643151456,37654335264,41534535936,41511066728],[1455154800,39630263824,39626042712,41534535936,41511066728],[1455155100,36017012536,36047354576,37624900728,37633952952],[1455155400,36754564752,36761805120,37624900728,37633952952],[1455155700,34777674056,34784116168,34876471344,34879801904],[1455156000,34348069112,34352208056,34523623912,34538067144],[1455156300,35260930528,35248338120,38543064592,38545270232],[1455156600,37760872688,37774587872,38543064592,38545270232],[1455156900,36166013432,36206344032,36467787336,36511639848],[1455157200,36034558256,36062698032,36467787336,36511639848],[1455157500,35300957632,35298608200,35894143688,35903325256],[1455157800,34439960328,34447261816,35894143688,35903325256],[1455158100,32465703480,32473644472,36937057248,36962549496],[1455158400,35783091224,35811727264,36937057248,36962549496],[1455158700,34313109952,34344588736,36797489368,36819260736],[1455159000,35580302312,35599101576,36797489368,36819260736],[1455159300,33648403696,33652304568,35405492264,35387931280],[1455159600,34344286432,34339385880,35405492264,35387931280],[1455159900,32495243768,32507659776,33629058136,33615751296],[1455160200,32867967208,32868841392,33629058136,33615751296],[1455160500,31908539016,31934678192,33760867920,33771810400],[1455160800,33764023896,33756368368,33770940184,33771810400],[1455161100,33562147096,33552727160,33770940184,33722527320],[1455161400,30971268888,31031653504,33007063048,33101307240],[1455161700,27281360128,27276981176,29926433400,29953285104],[1455162000,28890771112,28914255288,29926433400,29953285104],[1455162300,27054924120,27068429448,28098101200,28104515896],[1455162600,27236081304,27247524760,28098101200,28104515896],[1455162900,26179205440,26196305256,28314734272,28318022560],[1455163200,28259386984,28262494064,28314734272,28318022560],[1455163500,28764503960,28768203576,30407192144,30413495448],[1455163800,29880725520,29887937168,30407192144,30413495448],[1455164100,28929099736,28944192120,29504769416,29535179736],[1455164400,28763184848,28786172000,29504769416,29535179736],[1455164700,27202253088,27215945088,27440702392,27473608664],[1455165000,26419923024,26429540248,27440702392,27473608664],[1455165300,24817213544,24794207080,27203456096,27249721440],[1455165600,25869957992,25899505032,27203456096,27249721440],[1455165900,23448120568,23453207160,24778743336,24816204312],[1455166200,24250755232,24280922184,24778743336,24816204312],[1455166500,23918224064,23936303688,26073973056,26102242376],[1455166800,26187961760,26205879264,26433937400,26429516776],[1455167100,27405165400,27400626728,29861800952,29856963680],[1455167400,28735907384,28747902464,29861800952,29856963680],[1455167700,26676924680,26712083960,27629837216,27631161016],[1455168000,27131378968,27132697672,27629837216,27631161016],[1455168300,26427913944,26433549144,27384884944,27401648008],[1455168600,26634085552,26645949696,27384884944,27401648008],[1455168900,25970599640,25960492936,29429293952,29377973744],[1455169200,28652634096,28634204200,29429293952,29377973744],[1455169500,26745307416,26792179440,26976683904,27029227824],[1455169800,25697180488,25723844520,26160061024,26192586480],[1455170100,25385998048,25396102064,27125386072,27125595800],[1455170400,26849776304,26850292400,27125386072,27125595800],[1455170700,27011521496,27016456792,28924976112,28939417136],[1455171000,28324805080,28341727280,28924976112,28939417136],[1455171300,27293817920,27310859112,28064978384,28068351544],[1455171600,27429487992,27434657712,28064978384,28068351544],[1455171900,26626837728,26641167008,28113604352,28141745184],[1455172200,27449148832,27459952928,28113604352,28141745184],[1455172500,26916950448,26900785736,30176677648,30198264928],[1455172800,29250446208,29273954672,30176677648,30198264928],[1455173100,28720085952,28738987096,33533863344,33523672424],[1455173400,32326064920,32328712792,33533863344,33523672424],[1455173700,30588736592,30610808776,32927577048,32927247584],[1455173905,32927577048,32927247584,32927577048,32927247584],[1455174216,33318989448,33335985072,33318989448,33335985072]],\"expiration\":5,\"hostname\":\"barryo-mac3.local\",\"user\":{\"id\":1,\"name\":null,\"email\":\"joe@example.com\"}}','2019-05-11 14:37:48'),(47,'8da95cdb-65b4-44ff-ab5c-1260b67be953','8da95cdb-8432-48ac-9613-0089efec57f7',NULL,1,'cache','{\"type\":\"hit\",\"key\":\"admin_home_customers\",\"value\":{\"4\":\"AS112\",\"2\":\"HEAnet\",\"5\":\"Imagine\",\"3\":\"PCH DNS\",\"1\":\"VAGRANT IXP\"},\"hostname\":\"barryo-mac3.local\",\"user\":{\"id\":1,\"name\":null,\"email\":\"joe@example.com\"}}','2019-05-11 14:37:48'),(48,'8da95cdb-668a-407a-9c87-c18979b81fd2','8da95cdb-8432-48ac-9613-0089efec57f7',NULL,1,'cache','{\"type\":\"missed\",\"key\":\"grapher::infrastructure001-protoall-bits-week-png.png\",\"hostname\":\"barryo-mac3.local\",\"user\":{\"id\":1,\"name\":null,\"email\":\"joe@example.com\"}}','2019-05-11 14:37:48'),(49,'8da95cdb-672c-4dbf-afe5-0f2d036a2c62','8da95cdb-8432-48ac-9613-0089efec57f7',NULL,1,'cache','0','2019-05-11 14:37:48'),(50,'8da95cdb-6868-4c22-bdf8-58f0e59e3d7c','8da95cdb-8432-48ac-9613-0089efec57f7',NULL,1,'cache','{\"type\":\"missed\",\"key\":\"grapher::infrastructure001-protoall-bits-week-png.data\",\"hostname\":\"barryo-mac3.local\",\"user\":{\"id\":1,\"name\":null,\"email\":\"joe@example.com\"}}','2019-05-11 14:37:48'),(51,'8da95cdb-6d83-416d-a917-88ecabe0c5a0','8da95cdb-8432-48ac-9613-0089efec57f7',NULL,1,'cache','{\"type\":\"set\",\"key\":\"grapher::infrastructure001-protoall-bits-week-png.data\",\"value\":[[1454455800,81294362816,81285576440,88069944432,88106694760],[1454457600,70861891680,70879651504,80870320288,80939228632],[1454459400,61820900952,61836187968,66968352168,67014678488],[1454461200,53303777096,53305052864,62845305440,62931096184],[1454463000,46965082136,46977456320,49870513416,49862850104],[1454464800,40384565960,40392848672,45678738008,45636379632],[1454466600,38216781752,38224697040,40799594304,40807874304],[1454468400,35944029240,35956335128,39960841976,39987092904],[1454470200,32967694816,32978399560,35519703248,35509715920],[1454472000,30070160152,30082385616,33153776584,33172195320],[1454473800,24414319016,24419567880,26577381368,26585448936],[1454475600,23904858944,23912816624,28590035120,28612257208],[1454477400,30063386920,30074640448,31599770560,31591656360],[1454479200,27785035432,27794444608,32122766120,32086708720],[1454481000,29279816552,29284912832,30922314872,30918322360],[1454482800,30666057464,30679904160,35515780640,35503495920],[1454484600,37357081544,37371358112,41662438288,41650997536],[1454486400,43327042600,43340908536,45779987440,45788160344],[1454488200,50489368880,50505400192,54572934248,54572019256],[1454490000,57200462984,57214479368,61853898576,61778503184],[1454491800,63373343256,63393476680,68516850000,68544855408],[1454493600,69064495536,69074671520,74284354992,74181294872],[1454495400,70013051296,70043538464,73622118040,73634910728],[1454497200,73012892712,73015317200,78255883456,78166086880],[1454499000,73352036232,73383848632,76636250720,76666954016],[1454500800,75620013040,75637879848,80283491816,80223183568],[1454502600,76228595344,76243703864,81054704368,81053125928],[1454504400,75999819824,76021488840,87437191816,87489136552],[1454506200,78292126288,78308144544,83569412240,83555038472],[1454508000,78085704544,78111659880,83185345296,83187726680],[1454509800,78345803896,78368818680,81801939440,81804611224],[1454511600,78013894008,78033177688,85710113408,85601906328],[1454513400,79577108928,79609217552,83162806944,83214050000],[1454515200,81293692760,81305309584,88821967104,88636556016],[1454517000,84816123360,84846219120,88918795392,88935110568],[1454518800,87081432392,87091507432,94021487096,93905920584],[1454520600,85651853808,85688961648,90248297272,90225347048],[1454522400,83505885640,83518601184,90176417144,89989158328],[1454524200,83793986488,83805506328,86665926344,86676329168],[1454526000,85511366136,85497869576,96762520456,96761447928],[1454527800,88476854712,88476454824,92160504896,92161351168],[1454529600,90648352872,90620921072,97641977904,97464214088],[1454531400,94026246008,93904977944,102154981240,102208628152],[1454533200,94198091872,94169919552,105749290568,105506634816],[1454535000,97110596728,97097309832,101634669488,101587174880],[1454536800,95556494568,95519992032,106372441136,106175058520],[1454538600,91696338768,91653353688,97470650608,97394349312],[1454540400,86827737208,86786107000,97104688184,96882082448],[1454542200,79701142008,79671642200,86761395400,86724876184],[1454544000,70123725592,70109586560,79172492272,79062640088],[1454545800,60995768104,61013157120,65948718584,65968776328],[1454547600,51537418600,51541293520,60431968360,60464608536],[1454549400,46418530088,46429869168,49621685936,49624261992],[1454551200,40376559632,40385829512,46871778392,46816799256],[1454553000,36917014256,36924532872,40277913368,40279221896],[1454554800,35410467488,35421283280,38138767224,38152503432],[1454556600,33269377160,33277090248,36432315808,36427636560],[1454558400,30547892968,30557396104,34193108352,34194383304],[1454560200,31117152736,31126264400,33853356856,33868214264],[1454562000,27903115128,27911278000,30223005072,30198625336],[1454563800,29934257672,29944865160,31506004712,31492730776],[1454565600,29441624672,29452300504,31908599968,31920348632],[1454567400,30348890248,30362244816,31980203864,32017690152],[1454569200,32179799496,32194323696,38244244752,38223516480],[1454571000,39366868856,39381515616,44505251488,44497151872],[1454572800,45996071472,46009294272,50062178904,50060593008],[1454574600,54807359568,54826227872,58070999064,58095039016],[1454576400,58276891720,58291351432,62305218472,62272611040],[1454578200,65252116728,65276435440,68656748056,68691325488],[1454580000,70086442808,70100528008,75931323424,75882340384],[1454581800,71930873456,71954324240,76252227928,76232277096],[1454583600,73408477528,73422544360,80465551304,80381818024],[1454585400,73455023456,73476718576,76442831336,76449727248],[1454587200,73239310816,73256046544,78661128224,78660634608],[1454589000,74237058536,74259646000,77340780208,77300023376],[1454590800,74964145272,74979110536,82920864392,82946083856],[1454592600,76809569096,76824240448,80064745264,80073796136],[1454594400,78100494176,78123839856,84511234640,84459278928],[1454596200,80466829544,80497494000,85060051544,85097480376],[1454598000,79293975272,79302495264,86915567736,86794316736],[1454599800,80392108824,80429461136,84012543440,83991247112],[1454601600,81984696224,82000466944,90237052568,90099949968],[1454603400,83126675616,83167204552,87233539496,87259043504],[1454605200,86443378304,86456597808,93991535760,93760894992],[1454607000,84513029168,84541310664,87859023048,87861768752],[1454608800,82589188984,82590017384,89199089152,88960602080],[1454610600,81692031704,81710528048,87764791192,87737411496],[1454612400,81436198776,81425187960,90264573040,90276420992],[1454614200,84071655264,84065463216,88066380704,88019011000],[1454616000,86614534032,86575647704,92804890792,92501827776],[1454617800,89907786856,89891653920,94556947296,94526956864],[1454619600,90940322576,90902602992,97276698920,97074876920],[1454621400,91819805536,91790983824,96481755968,96440473776],[1454623200,92839727080,92804172520,100117498896,99991715848],[1454625000,92579642360,92534196984,97562440600,97511791880],[1454626800,87659012520,87616060016,96628441184,96517502616],[1454628600,80090564440,80075514848,86851725672,86812310264],[1454630400,71545922224,71554291416,80467583936,80437053704],[1454632200,62981815624,62994380352,68179041600,68182648104],[1454634000,53779200528,53789997832,60927596696,60876776144],[1454635800,47277584224,47286596712,50927426888,50909429640],[1454637600,41026057808,41039247616,46214662192,46217699320],[1454639400,39383384936,39393919384,41802158416,41810225840],[1454641200,38794575608,38806824920,42883686072,42879516648],[1454643000,35771532760,35782286024,39361694576,39420423120],[1454644800,32261048376,32276826344,34701189880,34725474368],[1454646600,29589233832,29600958336,31761446336,31781482656],[1454648400,29002513576,29014087848,32288004048,32278281648],[1454650200,29895545880,29909371168,31954177568,31968767016],[1454652000,27768558936,27777968400,29728539720,29759542872],[1454653800,29464792272,29472097472,31411361648,31421028200],[1454655600,30423344512,30440973616,35401609136,35419534864],[1454657400,36985723216,36999985512,41006096752,41038368760],[1454659200,44306848280,44322889208,48474948920,48501553440],[1454661000,52106017048,52127083488,56038247120,56071574888],[1454662800,58431305184,58442361528,63086069168,63100869216],[1454664600,65132835200,65154774240,70261450840,70274799136],[1454666400,69027263272,69045404928,73692640008,73615980072],[1454668200,74354528840,74374535976,78701134464,78744029528],[1454670000,74035681200,74059215200,80040231704,80019278488],[1454671800,76155425048,76172657296,79529553000,79586814032],[1454673600,77201223000,77208283480,84144927920,84061288616],[1454675400,76618118088,76645229032,80326782152,80357024160],[1454677200,79549706568,79571666872,89202983328,89164870240],[1454679000,77138576104,77160551048,81251421304,81218265112],[1454680800,77066053152,77082186408,84314591488,84319207408],[1454682600,78949139680,78976938696,83071722112,83113883416],[1454684400,79057866040,79081264416,86101271528,85996828208],[1454686200,79144830352,79178862640,84364029176,84368287792],[1454688000,83198448968,83213494168,90495799128,90247050520],[1454689800,84953897344,84987291232,88368824480,88403817168],[1454691600,85091261696,85110719544,92081300744,91926442608],[1454693400,83391813912,83418296216,86807594640,86801574296],[1454695200,81663347184,81682287888,88283661112,88179522120],[1454697000,81375857456,81407486760,84702708408,84711773792],[1454698800,81688933936,81707529960,88450534256,88293906176],[1454700600,82318779800,82342788136,85426675872,85443623304],[1454702400,84787308568,84809255376,91408951816,91308697504],[1454704200,88754271928,88768685400,92482828864,92560383840],[1454706000,90803521808,90832576440,97675678448,97722071904],[1454707800,91612321776,91643840680,95574520072,95619477240],[1454709600,89608557896,89611301280,96616261504,96506777952],[1454711400,89500657448,89537073720,93721649416,93832095160],[1454713200,86665768776,86681079080,95677260024,95611031704],[1454715000,81781610280,81795772496,87560692520,87544151232],[1454716800,76014803152,76035591240,84960518456,85013712240],[1454718600,70021784376,70030907080,75041417936,75036067680],[1454720400,61331252176,61346404144,69398993992,69565920552],[1454722200,53780100328,53796932144,57557002832,57597809896],[1454724000,46353875272,46365433512,51565310088,51524669976],[1454725800,41243110496,41253014344,43854038928,43880993672],[1454727600,36769041240,36781714640,41075537720,41118023992],[1454729400,33497848976,33508769408,36320000176,36326492688],[1454731200,31045760368,31055440832,34052138040,34054560520],[1454733000,29848131368,29861875104,31416929104,31444169152],[1454734800,27358375856,27369089312,29617380512,29618447760],[1454736600,29378137528,29388613800,31919808512,31939410176],[1454738400,27611983816,27622568328,33403838184,33425163720],[1454740200,27089762760,27099282832,28703305824,28713841776],[1454742000,28737630024,28751976696,32431573736,32458045776],[1454743800,30756620824,30770650680,33567867896,33595693952],[1454745600,35672584032,35679646240,39091242744,39096327632],[1454747400,43631690192,43655557736,47215009296,47232414912],[1454749200,50212727384,50220092648,53546642672,53565627840],[1454751000,56257103664,56281898760,59389403912,59409253128],[1454752800,60004430168,60017699272,63455192864,63471363856],[1454754600,62144861376,62168576608,64350585784,64353294504],[1454756400,63882762576,63894235760,68615160216,68473431120],[1454758200,65726957088,65751822008,68794827056,68795088240],[1454760000,67498977400,67514699648,72489149360,72382598112],[1454761800,69373478600,69398247056,72178800752,72203651264],[1454763600,68556996040,68574036776,77100602488,77117762960],[1454765400,68938490880,68965160696,72240949736,72275569424],[1454767200,70057915504,70074033808,75362620256,75252930240],[1454769000,71577845848,71599414120,74736074312,74750242520],[1454770800,73959031800,73972174216,79940258664,79881389584],[1454772600,77195529776,77223553840,80466923752,80459406832],[1454774400,78513721400,78528805352,84462663952,84454338640],[1454776200,78969925656,79007048192,82615809008,82613047968],[1454778000,78719323720,78736046632,84185175488,84099313736],[1454779800,80959915944,80984503856,84152050328,84139376864],[1454781600,81118758912,81139350208,88388113096,88325246408],[1454783400,82487747352,82507356800,86789030376,86799310160],[1454785200,81723737320,81751659136,88420317368,88423779616],[1454787000,81514561576,81556262880,84660799584,84715692344],[1454788800,82846652816,82862919008,89335489944,89178351248],[1454790600,84816813464,84848066832,89194987320,89200051072],[1454792400,85311473904,85321879280,92615473608,92494265448],[1454794200,87908701736,87941461984,92803561328,92879680872],[1454796000,88546960872,88564808736,96976257264,96808990608],[1454797800,86625768440,86649160472,92563798240,92602694544],[1454799600,83144457928,83161564240,92505249520,92422916056],[1454801400,77998634264,78026186888,82895489248,82973338632],[1454803200,72780651152,72784481088,80267213200,80111372928],[1454805000,68267004384,68287852368,73934205232,73996117104],[1454806800,60477147464,60481616192,67816176208,67737847912],[1454808600,54907568168,54926146008,61041737280,61115098312],[1454810400,47030399904,47031295600,53222990008,53229097016],[1454812200,44998669392,45016382048,47951635440,48021841232],[1454814000,40884028328,40896067808,44915492496,44950109872],[1454815800,35649389592,35661029072,40349572016,40388050600],[1454817600,32954768824,32967110376,37344349656,37381316752],[1454819400,30661975616,30672840912,33084184992,33114596904],[1454821200,28931749064,28940710024,31144626528,31169671712],[1454823000,29538408176,29550405688,32015687992,32025484304],[1454824800,27059677136,27065867048,30969181024,30971519128],[1454826600,27703711848,27718478784,29947035176,29982042216],[1454828400,28359654344,28371151528,33329150880,33348586192],[1454830200,28960660032,28975635304,30932819896,30923375600],[1454832000,31942829064,31950467664,35128894512,35143944800],[1454833800,40435342240,40458916200,46566526872,46602123648],[1454835600,47722869232,47736410976,51900868192,51949814040],[1454837400,55738901680,55760465424,60708801920,60730316856],[1454839200,61086942328,61096311736,65518079544,65369489728],[1454841000,62854760040,62876951528,67179225960,67188450584],[1454842800,65180849360,65190971552,69487103248,69495334440],[1454844600,68553563664,68580929128,71955718784,72003619200],[1454846400,70157175488,70166860976,75890306800,75721886168],[1454848200,68443916048,68461305728,72001921280,72062211600],[1454850000,69710763688,69725118880,75714028376,75615794392],[1454851800,71449959712,71476562496,75005339288,75011123528],[1454853600,72142666040,72157543400,78179738488,78095240208],[1454855400,73705399160,73727919000,77932969768,77956928000],[1454857200,76342537920,76351363864,81806770088,81712828104],[1454859000,81822434792,81830423496,86922176896,86935487032],[1454860800,83265260200,83294779912,90762971896,90889921160],[1454862600,86888626304,86897184032,91536147744,91555520368],[1454864400,86125819144,86166091184,95301490704,95464561840],[1454866200,81847453152,81879707776,85640523488,85704200272],[1454868000,82974618880,82997094144,91062651744,90990369592],[1454869800,83241200320,83269713016,86637064912,86668095288],[1454871600,85909254528,85921878056,98680720360,98691446280],[1454873400,88315410392,88344324992,92066529048,92111042544],[1454875200,89900599928,89915385160,98334423920,98177988568],[1454877000,90367178048,90388415552,94892186864,94925800224],[1454878800,90345120128,90338759160,99092140584,98857492400],[1454880600,93315708416,93335305136,97619499600,97647244152],[1454882400,93470750928,93451614312,103445624824,103253903136],[1454884200,91365124328,91353232704,96256066760,96269899144],[1454886000,84740946320,84727951480,94223274368,94089304408],[1454887800,77301934320,77305954168,83973991760,83957138472],[1454889600,67836065952,67843247336,76283912256,76279249720],[1454891400,60934372800,60948731792,66883576352,66917665880],[1454893200,50892264016,50898265872,59038233416,59064762560],[1454895000,45922650856,45937474288,49353227456,49364606376],[1454896800,38668500408,38675954576,43880770640,43842198568],[1454898600,36530061880,36535207000,39964861504,39955435128],[1454900400,32756280120,32773812648,37160851880,37251710728],[1454902200,31586843792,31601126800,33820380192,33846207712],[1454904000,30634813184,30646727960,32867181960,32849335512],[1454905800,30817581032,30831990736,33498671352,33509499248],[1454907600,28508081712,28519002072,31850589200,31899893384],[1454909400,28514713576,28526004896,30218647944,30208335400],[1454911200,27370284368,27377324168,30896659152,30919054968],[1454913000,26343020464,26353420256,27614120144,27632757200],[1454914800,28320753128,28330517200,32463105424,32485427296],[1454916600,36234435696,36250292520,41030791240,41052216360],[1454918400,43479495656,43494124368,47772260344,47782113784],[1454920200,52311196464,52336421944,56167824344,56210392200],[1454922000,58119150632,58124864776,62430923456,62387087576],[1454923800,66945033720,66969680008,71456705080,71495807984],[1454925600,72319675408,72334758816,78233082616,78207399488],[1454927400,74350189800,74384333984,77351525072,77402259248],[1454929200,75018344400,75026661704,80539634832,80449555392],[1454931000,76143645984,76170238048,80474782448,80425504456],[1454932800,77568531568,77584673824,83851924176,83762926232],[1454934600,78703458696,78723356896,82353935056,82345517744],[1454936400,80538368480,80558048424,87906104496,87855474432],[1454938200,79329207168,79353015192,84701163736,84686792720],[1454940000,78059302936,78075453048,84953279704,84842232016],[1454941800,79648084088,79678243608,83154753848,83192930704],[1454943600,79861005040,79879672944,87632547432,87518351384],[1454945400,80216070024,80245069336,83536516120,83516951152],[1454947200,81035854216,81051648528,89207838304,89036841512],[1454949000,83888855984,83930306664,88203533032,88244742448],[1454950800,86290247128,85940388464,95481893728,92886730448],[1454952600,84341445440,84368681480,87781001968,87784991792],[1454954400,82526607968,82537352144,91065452488,90880267512],[1454956200,83205070744,83223681720,86916244952,86911430432],[1454958000,85974861696,85955119024,94870145824,94884743488],[1454959800,87590515256,87580183840,91873256184,91847332312],[1454961600,90259693744,90226306368,97502486504,97286684704],[1454963400,92742870824,92719081048,97924667472,97894228304],[1454965200,93098099000,93061335312,101107937352,100965391688],[1454967000,95236527320,95191469936,99685203416,99595539920],[1454968800,95407964936,95359657200,103500551976,103368779344],[1454970600,93488040720,93432792688,97732670264,97671919936],[1454972400,87095861600,87063618224,97213971000,97203384152],[1454974200,73853506040,73873037816,80612326656,80619506224],[1454976000,63716405648,63721648896,72607502488,72412285632],[1454977800,55054658984,55067690976,60311020072,60322670728],[1454979600,48498144600,48501415480,56643262584,56658586056],[1454981400,44272526792,44284996752,47014808008,47033847048],[1454983200,38750026464,38763359064,44346460304,44351784144],[1454985000,34199270672,34212675024,37762416896,37789498768],[1454986800,32231608760,32244283600,35247101672,35284426032],[1454988600,31305485912,31317945960,33665030320,33646376688],[1454990400,29637662360,29653914640,33404294208,33487706688],[1454992200,27895272080,27902689280,29698939560,29707282832],[1454994000,26896945440,26911209528,30098653608,30184091408],[1454994300,26997528560,27016147096,29243767472,29273266224],[1454994600,28555356880,28572670008,29243767472,29273266224],[1454994900,27632946808,27626263200,29281823888,29283592392],[1454995200,28731024264,28736591960,29281823888,29283592392],[1454995500,28239276896,28264665096,30242587176,30297833464],[1454995800,29533703312,29563942856,30242587176,30297833464],[1454996100,28845010360,28834004648,32149475432,32193755264],[1454996400,30900827840,30941132192,32149475432,32193755264],[1454996700,28546890864,28571254344,29422495944,29427928056],[1454997000,28831344952,28837382528,29422495944,29427928056],[1454997300,27780462168,27792883232,28399202632,28424414000],[1454997600,27875247016,27897542896,28399202632,28424414000],[1454997900,27125257656,27139617184,28120455504,28130514152],[1454998200,27559443008,27567579760,28120455504,28130514152],[1454998500,26993432256,26999999304,28678699064,28692005056],[1454998800,27947540904,27963277816,28678699064,28692005056],[1454999100,27381309880,27404034840,29982390400,30009595776],[1454999400,29305953504,29324062248,29982390400,30009595776],[1454999700,28561779064,28565849416,31098570112,31122463784],[1455000000,29725569936,29737944008,31098570112,31122463784],[1455000300,28398652928,28396237848,36961402232,37011148112],[1455000600,34289813784,34327101120,36961402232,37011148112],[1455000900,29176750640,29182875872,30881229456,30876170320],[1455001200,30625323256,30632641824,30881229456,30876170320],[1455001500,31077556528,31099358712,33703653712,33693491072],[1455001800,33127250424,33129963312,33703653712,33693491072],[1455002100,33256848936,33278918304,36908125928,36907790360],[1455002400,36710826736,36717719952,36908125928,36907790360],[1455002700,37089449752,37102290152,39245528432,39232063640],[1455003000,38450960760,38460627608,39245528432,39232063640],[1455003300,38228692536,38274212720,43842677688,43835296864],[1455003600,42834358024,42835448160,43842677688,43835296864],[1455003900,41548202080,41564877552,43874263672,43883895480],[1455004200,43079733976,43094350272,43874263672,43883895480],[1455004500,42230153856,42251023712,44747117736,44754653136],[1455004800,44701558904,44713942656,44747117736,44754653136],[1455005100,45672205592,45700062048,48526541432,48566391112],[1455005400,47718891360,47749777232,48526541432,48566391112],[1455005700,47123721624,47142526784,50174802048,50212914120],[1455006000,49678311112,49707115736,50174802048,50212914120],[1455006300,49624524048,49632136032,52373118320,52379485736],[1455006600,51113208984,51152829800,52373118320,52379485736],[1455006900,50206778984,50270220832,56759019024,56649152680],[1455007200,54998899416,54923776480,56759019024,56649152680],[1455007500,52410440416,52421813464,55470254240,55510774624],[1455007800,54903742760,54937434824,55470254240,55510774624],[1455008100,55151866848,55170196080,58933399960,58950114240],[1455008400,58432274040,58443166808,58933399960,58950114240],[1455008700,59654960648,59654018912,65678837720,65679798584],[1455009000,64955535872,64957204376,65678837720,65679798584],[1455009300,64503760904,64525826616,67370427576,67440163656],[1455009600,65973905624,66028283096,67370427576,67440163656],[1455009900,64637866160,64653405624,68880974400,68882106936],[1455010200,66932647592,66939890728,68880974400,68882106936],[1455010500,64672315208,64696440552,71937087448,71973833704],[1455010800,69721421584,69757115608,71937087448,71973833704],[1455011100,66038508624,66054881008,69249529656,69222240296],[1455011400,67849782152,67849005720,69249529656,69222240296],[1455011700,66222551440,66265585960,70049701960,70053646392],[1455012000,69242723760,69258310048,70049701960,70053646392],[1455012300,68932630536,68957902528,72691549048,72675776704],[1455012600,71182860808,71184290240,72691549048,72675776704],[1455012900,69068109560,69115498784,72318656504,72386103784],[1455013200,70374610608,70416969352,72318656504,72386103784],[1455013500,67595984768,67600373168,71712579944,71764481352],[1455013800,69746853992,69805480536,71712579944,71764481352],[1455014100,67785133832,67831034600,77050264040,76989853144],[1455014400,74095400816,74063279696,77050264040,76989853144],[1455014700,69105139064,69138139416,72728835216,72772489840],[1455015000,70908607104,70940345456,72728835216,72772489840],[1455015300,68213344392,68222541808,71818885632,71838670352],[1455015600,70676665504,70692772656,71818885632,71838670352],[1455015900,69905450360,69915335024,74802386552,74817927352],[1455016200,73441907160,73460362144,74802386552,74817927352],[1455016500,71484836072,71513912792,74332375240,74372445864],[1455016800,73075314264,73108008144,74332375240,74372445864],[1455017100,71589785384,71604165560,75196148816,75206179992],[1455017400,72904394000,72944930688,75196148816,75206179992],[1455017700,70228145520,70293697896,80365006672,80254065720],[1455018000,77526182768,77461481752,80365006672,80254065720],[1455018300,73187682328,73223005104,77783809336,77819756640],[1455018600,75942398672,75977052760,77783809336,77819756640],[1455018900,73412768448,73444890664,77187889088,77220687800],[1455019200,75721614248,75741161040,77187889088,77220687800],[1455019500,74435880264,74434527648,79346702040,79365472488],[1455019800,78171472200,78201439088,79346702040,79365472488],[1455020100,76821345608,76865794920,80130484664,80147642440],[1455020400,77874256264,77899380648,80130484664,80147642440],[1455020700,74501618800,74529626776,78813618648,78802805768],[1455021000,75950236496,75968816048,78813618648,78802805768],[1455021300,72414236768,72463991800,82769117576,82692514960],[1455021600,78001062704,77966150128,82769117576,82692514960],[1455021900,71146516504,71200188488,90311723920,90337707184],[1455022200,85057768560,85068444408,90311723920,90337707184],[1455022500,75520180104,75528993584,80305264600,80396943128],[1455022800,78231837360,78301885704,80305264600,80396943128],[1455023100,75113336952,75135626304,79091962720,79115277288],[1455023400,77093653296,77110413104,79091962720,79115277288],[1455023700,74308783296,74326282264,78729876472,78788127552],[1455024000,76709864016,76754922440,78729876472,78788127552],[1455024300,73956795784,73977676520,78590860400,78625529936],[1455024600,75868316320,75913141000,78590860400,78625529936],[1455024900,72697970752,72734189296,83882294008,83795068496],[1455025200,80927458312,80873705480,83882294008,83795068496],[1455025500,75688031048,75721058776,78974151696,79042871720],[1455025800,77546967600,77594913040,78974151696,79042871720],[1455026100,75590605648,75593481704,79103746016,79113650320],[1455026400,77427030504,77441388264,79103746016,79113650320],[1455026700,75349445784,75375200184,79747886808,79777586824],[1455027000,78378562768,78412239040,79747886808,79777586824],[1455027300,77086570592,77134779552,81882227280,81945172232],[1455027600,79981209312,80028380584,81882227280,81945172232],[1455027900,76986501440,76992791272,80451228080,80443642392],[1455028200,77950953096,77971532360,80451228080,80443642392],[1455028500,74881495088,74943282960,84798178136,84773715272],[1455028800,81627885152,81614522824,84798178136,84773715272],[1455029100,76319217272,76326214024,80325849480,80323448008],[1455029400,78452546352,78468950568,80325849480,80323448008],[1455029700,75747611416,75800724744,79408869240,79450208744],[1455030000,78061584744,78086828200,79408869240,79450208744],[1455030300,76551873808,76558786248,80502420152,80555772656],[1455030600,78976651984,79024117856,80502420152,80555772656],[1455030900,77205500816,77239874992,81383849728,81418251656],[1455031200,79134726360,79157940232,81383849728,81418251656],[1455031500,75944794416,75976223992,80519253120,80639668144],[1455031800,78039367064,78155024224,80519253120,80639668144],[1455032100,75048053736,75108285088,87094254400,86956265664],[1455032400,83612504280,83505420952,87094254400,86956265664],[1455032700,77611007488,77605728992,82337636080,82421621344],[1455033000,80424282240,80480684312,82337636080,82421621344],[1455033300,77834526680,77841962456,82271623568,82312101120],[1455033600,80380078016,80413621624,82271623568,82312101120],[1455033900,77725726312,77741505808,82038367296,82048597424],[1455034200,80579684480,80592640384,82038367296,82048597424],[1455034500,78941799968,78963732584,83354989504,83384403496],[1455034800,81659112920,81690068544,83354989504,83384403496],[1455035100,79614978816,79654002384,84061012728,84112503400],[1455035400,81319109240,81422628544,84061012728,84112503400],[1455035700,78132175536,78269462024,90043346272,89845526432],[1455036000,86860219952,86731025488,90043346272,89845526432],[1455036300,81881949680,81893353976,87192422240,87177791384],[1455036600,85202551352,85216681392,87192422240,87177791384],[1455036900,82300036032,82351064120,86358430712,86337364272],[1455037200,84734183696,84728167448,86358430712,86337364272],[1455037500,82530119184,82557994200,86567521000,86595024088],[1455037800,84405025136,84425585512,86567521000,86595024088],[1455038100,81281008192,81289297240,85697560280,85714168048],[1455038400,83701465648,83717783432,85697560280,85714168048],[1455038700,80898070096,80907738008,84765412624,84759589920],[1455039000,82350802760,82411275120,84765412624,84759589920],[1455039300,79177439264,79292610784,87776229768,87533323152],[1455039600,84438792200,84274906568,87776229768,87533323152],[1455039900,78549724672,78553172544,81925422608,81920679312],[1455040200,79949766496,79932208328,81925422608,81920679312],[1455040500,77267822144,77239250936,81905981952,81924753616],[1455040800,80224017288,80228703432,81905981952,81924753616],[1455041100,78482163384,78467573848,83715978272,83732960848],[1455041400,82047186680,82049504712,83715978272,83732960848],[1455041700,80258438480,80240253000,85292992400,85306538432],[1455042000,83413810296,83418343696,85292992400,85306538432],[1455042300,81428595544,81409632992,87294325824,87265064904],[1455042600,84542686976,84556110696,87294325824,87265064904],[1455042900,81260824200,81302015424,92631835024,92404065344],[1455043200,88579304048,88415578104,92631835024,92404065344],[1455043500,82300169080,82273109584,90980048672,90934361200],[1455043800,88223074736,88169093824,90980048672,90934361200],[1455044100,83648466568,83607357136,87484584416,87524644624],[1455044400,85515004648,85535962184,87484584416,87524644624],[1455044700,82887883952,82867679384,87480918088,87464260680],[1455045000,85389782872,85367840408,87480918088,87464260680],[1455045300,82695215336,82659009032,87819277400,87776534136],[1455045600,85799276880,85750662064,87819277400,87776534136],[1455045900,82956304232,82915274168,87351284008,87365162536],[1455046200,85339618168,85366708448,87351284008,87365162536],[1455046500,83677873288,83685005160,95223350016,95031168120],[1455046800,92075294768,91930208000,95223350016,95031168120],[1455047100,86459459104,86414906136,90157818536,90101858144],[1455047400,88625247912,88569182448,90157818536,90101858144],[1455047700,86873025712,86817662784,91426354688,91373491024],[1455048000,90540015112,90493074088,91426354688,91373491024],[1455048300,90511439544,90483851776,95758874616,95746321784],[1455048600,94183383680,94156801912,95758874616,95746321784],[1455048900,92446469312,92387272872,97533544232,97473281496],[1455049200,95441351840,95396379664,97533544232,97473281496],[1455049500,92452981056,92416703416,97242940184,97135505064],[1455049800,94629575704,94602596768,97242940184,97135505064],[1455050100,91431778584,91512032936,103031991896,102790254984],[1455050400,99203271872,99028649952,103031991896,102790254984],[1455050700,92828672840,92791878568,98537589544,98470069704],[1455051000,96364872712,96310789304,98537589544,98470069704],[1455051300,93138510808,93098594384,97417917872,97336156336],[1455051600,95829803544,95766382712,97417917872,97336156336],[1455051900,94672032352,94632776088,101139582280,101054329696],[1455052200,99189459104,99114044000,101139582280,101054329696],[1455052500,96208952040,96153068368,100006583968,99943496128],[1455052800,97300332784,97231034448,100006583968,99943496128],[1455053100,93268881848,93204377016,98565059976,98550048768],[1455053400,95434123176,95429843680,98565059976,98550048768],[1455053700,91390611952,91364997136,102852221704,102649425488],[1455054000,99090777472,98946787968,102852221704,102649425488],[1455054300,92918739992,92884130368,99118705984,99021422888],[1455054600,97517969592,97415310280,99118705984,99021422888],[1455054900,95169439536,95097934760,98397964584,98441098416],[1455055200,96166075432,96178905312,98397964584,98441098416],[1455055500,93086773744,93009284448,98024050744,97886772896],[1455055800,96083081008,95993608880,98024050744,97886772896],[1455056100,93717384928,93704018128,99185850912,99086027360],[1455056400,96827253120,96743157488,99185850912,99086027360],[1455056700,92825047272,92769778664,96953317024,96876401888],[1455057000,94029893568,93975406752,96953317024,96876401888],[1455057300,89703711904,89660065080,99281828936,99078903440],[1455057600,95350204928,95194407384,99281828936,99078903440],[1455057900,87845615464,87797144928,91215632384,91173729952],[1455058200,88773297944,88719101224,91215632384,91173729952],[1455058500,84557073768,84483290360,87746554312,87693106256],[1455058800,85838841760,85783272552,87746554312,87693106256],[1455059100,83060305312,83009165848,86962139632,86935328496],[1455059400,84661293280,84627166768,86962139632,86935328496],[1455059700,81060978744,81002702368,85037370688,84958525720],[1455060000,82597658992,82530203064,85037370688,84958525720],[1455060300,78267503432,78237497456,80924960344,80927611536],[1455060600,78077890864,78067570208,80924960344,80927611536],[1455060900,73798424544,73745453232,81538766648,81433964448],[1455061200,78183656544,78101321192,81538766648,81433964448],[1455061500,71305012504,71265692824,73216050512,73155698864],[1455061800,71453325936,71394328576,73216050512,73155698864],[1455062100,68033672472,67989155808,69940441440,69927056720],[1455062400,68980620904,68947727984,69940441440,69927056720],[1455062700,67390119592,67350429608,69306464512,69377142240],[1455063000,66891074680,66935143792,69306464512,69377142240],[1455063300,61831356248,61820699872,63443232768,63453169424],[1455063600,61856827176,61870061576,63443232768,63453169424],[1455063900,58624875704,58649997128,60333124656,60369663856],[1455064200,58015989928,58043615896,60333124656,60369663856],[1455064500,53880685040,53886141944,59094139848,59095613336],[1455064800,55281784232,55282544824,59094139848,59095613336],[1455065100,48905361704,48912135752,58300635888,58341265472],[1455065400,54657716608,54690801176,58300635888,58341265472],[1455065700,47611427064,47620508784,49777461232,49766015064],[1455066000,49110558680,49083554248,49777461232,49766015064],[1455066300,48339673152,48318968808,50298733184,50388292440],[1455066600,48843940512,48908541744,50298733184,50388292440],[1455066900,46415288440,46426136792,48496692320,48512166296],[1455067200,46649537744,46669275808,48496692320,48512166296],[1455067500,42836521544,42862085072,43818396840,43833645560],[1455067800,42291356536,42298133288,43818396840,43833645560],[1455068100,39642080320,39645777408,43066089832,43138314456],[1455068400,41292661360,41348172256,43066089832,43138314456],[1455068700,37498099336,37515383728,39029098088,39053916952],[1455069000,37872115464,37894065344,39029098088,39053916952],[1455069300,35222042232,35232543256,35612543264,35610130736],[1455069600,34857925880,34847281576,35612543264,35610130736],[1455069900,33739848936,33726763856,35649503672,35685016640],[1455070200,34812435920,34836475512,35649503672,35685016640],[1455070500,33059329448,33069502600,33888125064,33938088152],[1455070800,33187793592,33231962088,33888125064,33938088152],[1455071100,31832850232,31853085776,32613801024,32604908632],[1455071400,31820316752,31810533432,32613801024,32604908632],[1455071700,30723298960,30719529208,33850150424,33877704112],[1455072000,33174779680,33185087744,33850150424,33877704112],[1455072300,31531621488,31528068592,31694711904,31667226344],[1455072600,30360366464,30402474472,31105228480,31164246528],[1455072900,29135284200,29142180560,30266916720,30280157928],[1455073200,29959632928,29969604096,30266916720,30280157928],[1455073500,30202861200,30209903288,32667809816,32686414944],[1455073800,31675619296,31684634024,32667809816,32686414944],[1455074100,29861355920,29859811264,30909246488,30936379464],[1455074400,30530590168,30547225032,30909246488,30936379464],[1455074700,30262904480,30262266528,31853782264,31870615792],[1455075000,30998244400,31005229984,31853782264,31870615792],[1455075300,29780734808,29781267256,32204869784,32261188592],[1455075600,31143441160,31195027896,32204869784,32261188592],[1455075900,29227019528,29256414024,30204904168,30202975040],[1455076200,29106788920,29108831400,30204904168,30202975040],[1455076500,27010476912,27021255944,27919994488,27930502960],[1455076800,27484464720,27483379464,27919994488,27930502960],[1455077100,27310705960,27300962936,29347327424,29381078784],[1455077400,28404316784,28425114304,29347327424,29381078784],[1455077700,26690527424,26693030432,27714515408,27745034728],[1455078000,27379646480,27409549784,27714515408,27745034728],[1455078300,27081276576,27100498304,28249896144,28244776056],[1455078600,27562810480,27559919728,28249896144,28244776056],[1455078900,26656852024,26667098296,29001877616,29042877368],[1455079200,27707917464,27734171648,29001877616,29042877368],[1455079500,25213273872,25217971728,25991300440,26022830616],[1455079800,26063091912,26085549352,26222885848,26225149144],[1455080100,26575177672,26583716520,27481070960,27505746936],[1455080400,26766586712,26788543192,27481070960,27505746936],[1455080700,25833749192,25855062360,27461327448,27496311456],[1455081000,26896122592,26919900776,27461327448,27496311456],[1455081300,26449499192,26453044760,28404808280,28418340944],[1455081600,27562206984,27568212592,28404808280,28418340944],[1455081900,26322588768,26323718200,27786173880,27816053704],[1455082200,27033630392,27050041224,27786173880,27816053704],[1455082500,26042264704,26042598536,28284920504,28331301488],[1455082800,27031148888,27068048728,28284920504,28331301488],[1455083100,24828721936,24844807432,26101218952,26116406448],[1455083400,25643173128,25655252912,26101218952,26116406448],[1455083700,25721702136,25726966312,28420441992,28425429248],[1455084000,28293065784,28291453696,28420441992,28425429248],[1455084300,28548749072,28544571136,29890722760,29916075312],[1455084600,29075301648,29100240576,29890722760,29916075312],[1455084900,28185745496,28209928464,30386426720,30410955240],[1455085200,29499436408,29514404064,30386426720,30410955240],[1455085500,28262904512,28261060688,30081670584,30090475232],[1455085800,28891631816,28905143696,30081670584,30090475232],[1455086100,27328712744,27351533392,31033848248,31053098328],[1455086400,29801009768,29819850344,31033848248,31053098328],[1455086700,28117716472,28133353168,31101656656,31110207288],[1455087000,30440405280,30450270392,31101656656,31110207288],[1455087300,30081772128,30090568560,32944246336,32942771344],[1455087600,33846614104,33845827704,35855110128,35855856392],[1455087900,36641122752,36652470344,38730766072,38770297688],[1455088200,37739576224,37775933000,38730766072,38770297688],[1455088500,36572185248,36593896536,39426283744,39428142144],[1455088800,38691676672,38696633152,39426283744,39428142144],[1455089100,37712378992,37729327128,39594295368,39624207272],[1455089400,39153293072,39182580808,39594295368,39624207272],[1455089700,39721246664,39744386888,45668084568,45673491200],[1455090000,44121416896,44130374520,45668084568,45673491200],[1455090300,42004268904,42017729576,45142435232,45147771176],[1455090600,45036310216,45042742352,45142435232,45147771176],[1455090900,46138112432,46157964864,49695276832,49744320968],[1455091200,48704861744,48745032208,49695276832,49744320968],[1455091500,48320473128,48345623600,53159232144,53196960384],[1455091800,52710264952,52726269864,53159232144,53196960384],[1455092100,53150962544,53146168648,56979286240,57047743280],[1455092400,55731221072,55772544928,56979286240,57047743280],[1455092700,54426649488,54429985104,58166762000,58226240064],[1455093000,56983345856,57038807040,58166762000,58226240064],[1455093300,56367565200,56399948976,63664404272,63645678720],[1455093600,62334278008,62315130936,63664404272,63645678720],[1455093900,60675006752,60676125264,63789009880,63844578816],[1455094200,62792702408,62834612000,63789009880,63844578816],[1455094500,62634565840,62636187904,67929524440,67904078400],[1455094800,67390912600,67391237312,67929524440,67904078400],[1455095100,67616795608,67674495400,71280383688,71338120488],[1455095400,69667481984,69711188312,71280383688,71338120488],[1455095700,68152401296,68169141144,73577208688,73605093008],[1455096000,73637539000,73646417440,73771822616,73738397632],[1455096300,76144234568,76141724952,82346805584,82425122912],[1455096600,78755313152,78859023456,82346805584,82425122912],[1455096900,72377971344,72495890392,78583726208,78539225160],[1455097200,76220505064,76206568656,78583726208,78539225160],[1455097500,72892382512,72929459568,77651714752,77647729648],[1455097800,75920424816,75925632312,77651714752,77647729648],[1455098100,73166972576,73177371168,76043043696,76013519864],[1455098400,74727278608,74735070696,76043043696,76013519864],[1455098700,74679439840,74725785480,82093117824,82026456424],[1455099000,79377992504,79358617984,82093117824,82026456424],[1455099300,75278515280,75356620704,80446354720,80503799336],[1455099600,78189871888,78231038880,80446354720,80503799336],[1455099900,75053022576,75057491952,80066078072,80069306472],[1455100200,78330502504,78319023624,80066078072,80069306472],[1455100500,76431231008,76411266624,83969626808,84042751400],[1455100800,80860915456,80920334192,83969626808,84042751400],[1455101100,75600768920,75638655720,79728309992,79788805752],[1455101400,77875616696,77926698776,79728309992,79788805752],[1455101700,74667556776,74709395728,77451116024,77525860472],[1455102000,76152664216,76195403472,77451116024,77525860472],[1455102300,75640083456,75622772888,82081848960,82097264312],[1455102600,80143787024,80178201664,82081848960,82097264312],[1455102900,76937368032,76996489824,79881250040,79893630512],[1455103200,78071203584,77697806688,79881250040,79893630512],[1455103500,75326487392,74212257776,78740305888,77939339784],[1455103800,76572577008,76002536088,78740305888,77939339784],[1455104100,73976297056,73959697128,83310422928,83384229064],[1455104400,80723821584,80800311840,83310422928,83384229064],[1455104700,76058777496,76117968824,78682295072,78680880232],[1455105000,77604061312,77595811656,78682295072,78680880232],[1455105300,76745626144,76737563376,80944281448,80977796936],[1455105600,79540016752,79579763080,80944281448,80977796936],[1455105900,77121693368,77168260168,78873955408,78902352864],[1455106200,77182871744,77209300000,78873955408,78902352864],[1455106500,75152113784,75175080096,79683667208,79709041680],[1455106800,77767379064,77785911160,79683667208,79709041680],[1455107100,74645194152,74659967360,77862801352,77908708256],[1455107400,75568464040,75601448472,77862801352,77908708256],[1455107700,72868083160,72886997512,82223250040,82299142008],[1455108000,78407664872,78459271736,82223250040,82299142008],[1455108300,72483440456,72488282592,82356528936,82392707704],[1455108600,79125142720,79156133872,82356528936,82392707704],[1455108900,73234816432,73264208720,76639138544,76694538832],[1455109200,75317974680,75349751528,76639138544,76694538832],[1455109500,73422129104,73408859784,76199793280,76206556176],[1455109800,74833009720,74841116576,76199793280,76206556176],[1455110100,73507246784,73536051536,78197852824,78273605720],[1455110400,76514553880,76574953312,78197852824,78273605720],[1455110700,74501727144,74533423688,78960252296,79006017072],[1455111000,76451744120,76499064080,78960252296,79006017072],[1455111300,73728061712,73757663712,84375439136,84323944144],[1455111600,81281609600,81244905736,84375439136,84323944144],[1455111900,75572990240,75588578184,78756997088,78824954760],[1455112200,77446698688,77494767024,78756997088,78824954760],[1455112500,75641053920,75644346552,78841192576,78846889072],[1455112800,77528346544,77544030848,78841192576,78846889072],[1455113100,76389627600,76425186784,81335931024,81364173336],[1455113400,79805622896,79838400960,81335931024,81364173336],[1455113700,77968781504,77998552368,82356853856,82350768424],[1455114000,80789290688,80780431872,82356853856,82350768424],[1455114300,78891791152,78908765656,83495463048,83599504776],[1455114600,80750403200,80847851176,83495463048,83599504776],[1455114900,77133582648,77182791104,87073543992,86994800920],[1455115200,83812114536,83765932608,87073543992,86994800920],[1455115500,78130028008,78175675200,82025585416,82125657448],[1455115800,80582008432,80640741760,82025585416,82125657448],[1455116100,78723038456,78712904960,82323103696,82374502408],[1455116400,80915247808,80954611280,82323103696,82374502408],[1455116700,79081355400,79099183528,82536714696,82568507496],[1455117000,80890245464,80920423824,82536714696,82568507496],[1455117300,78229801792,78264272512,81053190240,81108776096],[1455117600,79262752328,79314149032,81053190240,81108776096],[1455117900,77170627552,77200738928,82119909864,82118749480],[1455118200,79620934360,79659513384,82119909864,82118749480],[1455118500,76333104472,76412302576,84889202888,84788457224],[1455118800,83180456120,83113847592,84889202888,84788457224],[1455119100,79886158344,79913412384,82519833344,82571608192],[1455119400,81013695584,81056940096,82519833344,82571608192],[1455119700,78549004376,78566810808,81969607424,81974675640],[1455120000,80803765232,80819016888,81969607424,81974675640],[1455120300,79665470000,79707782872,84419470744,84468979752],[1455120600,82942155536,82981647736,84419470744,84468979752],[1455120900,81034541680,81053144352,85654204776,85681876448],[1455121200,83680484504,83714219712,85654204776,85681876448],[1455121500,80689081008,80717627544,85410832464,85384649288],[1455121800,82842068856,82876593504,85410832464,85384649288],[1455122100,79178299704,79289577640,87943585232,87810721880],[1455122400,85278283208,85186032192,87943585232,87810721880],[1455122700,80447695280,80463628800,84201653048,84253876816],[1455123000,82760741400,82802889160,84201653048,84253876816],[1455123300,81115544040,81138350624,85837101016,85871373744],[1455123600,84116769824,84139634192,85837101016,85871373744],[1455123900,81770614672,81768628864,86109883816,86111551920],[1455124200,84273554880,84279688464,86109883816,86111551920],[1455124500,81632513912,81668345928,85716888856,85805720400],[1455124800,83926923008,83990908712,85716888856,85805720400],[1455125100,80809080448,80810788432,83449480224,83437326056],[1455125400,81238539888,81282194096,83449480224,83437326056],[1455125700,78550423048,78644821552,87540461712,87343572168],[1455126000,84560414464,84430959304,87540461712,87343572168],[1455126300,78759452976,78778840680,81533164912,81536511280],[1455126600,79582344792,79587321776,81533164912,81536511280],[1455126900,76790440888,76810327456,80911821568,80961696784],[1455127200,80194675288,80234253032,80911821568,80961696784],[1455127500,80618772120,80636489328,86056731656,86078225856],[1455127800,84259428168,84258630536,86056731656,86078225856],[1455128100,81980377832,81942900072,86556780992,86553697232],[1455128400,84331212352,84332111048,86556780992,86553697232],[1455128700,80977995704,80980135824,85029853632,85012091744],[1455129000,82748418832,82757649576,85029853632,85012091744],[1455129300,80124464176,80143058200,89544955680,89368861528],[1455129600,85426733872,85290920992,89544955680,89368861528],[1455129900,79196813376,79159346000,90119798112,90108123240],[1455130200,86717149680,86694229208,90119798112,90108123240],[1455130500,80875158512,80848832032,85478801824,85509964936],[1455130800,84185812848,84205608616,85478801824,85509964936],[1455131100,82951653264,82939516328,87442240168,87413533528],[1455131400,85785736096,85762501224,87442240168,87413533528],[1455131700,83653186032,83650246400,87940314440,87958441760],[1455132000,85965467704,85960110008,87940314440,87958441760],[1455132300,83315561208,83274799400,88306713224,88314605664],[1455132600,86005467664,86026848784,88306713224,88314605664],[1455132900,83117605208,82563186208,92004039896,89122225576],[1455133200,88870003264,86830663040,92004039896,89122225576],[1455133500,82708332144,82669665392,86137873944,86108257144],[1455133800,85083989512,85051626160,86137873944,86108257144],[1455134100,84210923120,84175111864,88680961792,88653575352],[1455134400,87397411160,87364186696,88680961792,88653575352],[1455134700,86305907384,86251328992,91540388568,91464547512],[1455135000,89677040128,89611538504,91540388568,91464547512],[1455135300,86763043848,86722354736,91042535184,91002699760],[1455135600,89593090336,89545662512,91042535184,91002699760],[1455135900,87755264152,87695615968,92304835688,92261637136],[1455136200,89855199096,89846169736,92304835688,92261637136],[1455136500,86514226264,86538488152,96922140704,96749073328],[1455136800,94623288880,94479862440,96922140704,96749073328],[1455137100,90716468696,90655239616,94889004616,94860017272],[1455137400,92831623472,92793175016,94889004616,94860017272],[1455137700,89872594776,89807575424,94647782624,94569673152],[1455138000,93473063432,93393668728,94647782624,94569673152],[1455138300,93054581672,92972099504,99103862144,99020888624],[1455138600,96929200968,96859353256,99103862144,99020888624],[1455138900,93464953936,93416433912,97391754336,97320663640],[1455139200,95502482912,95455103008,97391754336,97320663640],[1455139500,92940134000,92931522808,97826188192,97774435192],[1455139800,95081210896,95052904160,97826188192,97774435192],[1455140100,91654960992,91649110104,103089314424,102959657112],[1455140400,99785748424,99668938896,103089314424,102959657112],[1455140700,93849620504,93755274552,97924635744,97812787104],[1455141000,95736573656,95660115232,97924635744,97812787104],[1455141300,92525968664,92502126856,96938069880,96844693664],[1455141600,95094881616,95009749040,96938069880,96844693664],[1455141900,92499318736,92431743464,96677595448,96607147624],[1455142200,94702145424,94632608352,96677595448,96607147624],[1455142500,91839418200,91775341312,95918257184,95863307072],[1455142800,93865936640,93806444472,95918257184,95863307072],[1455143100,90638504752,90575499240,94393946008,94348900640],[1455143400,91304926992,91246608088,94393946008,94348900640],[1455143700,86652705984,86593916832,95187452904,95240270760],[1455144000,91546337064,91570422112,95187452904,95240270760],[1455144300,84150840304,84102287264,86412735112,86343425824],[1455144600,84515482416,84447580856,86412735112,86343425824],[1455144900,81058226192,81003118544,83724809688,83695852376],[1455145200,82321854224,82286225448,83724809688,83695852376],[1455145500,80354470112,80308018248,84155456888,84122974224],[1455145800,82107256280,82063751096,84155456888,84122974224],[1455146100,78339283192,78282702840,81489877304,81471111008],[1455146400,79236105952,79204999632,81489877304,81471111008],[1455146700,74753427016,74698713088,77448339480,77411045816],[1455147000,74849079056,74820744656,77448339480,77411045816],[1455147300,70490494520,70480457120,77557477800,77535907592],[1455147600,74201101248,74176081336,77557477800,77535907592],[1455147900,67537766792,67519014224,69720494120,69739446704],[1455148200,67619241712,67629905800,69720494120,69739446704],[1455148500,63798111216,63785411752,66609071280,66584266200],[1455148800,65247584096,65229675432,66609071280,66584266200],[1455149100,62914094456,62923836368,65415886656,65457241728],[1455149400,63805546296,63825678296,65415886656,65457241728],[1455149700,60760006688,60756186440,62684830760,62750139448],[1455150000,60614298568,60652411296,62684830760,62750139448],[1455150300,56332716352,56330274544,58087309720,58151786952],[1455150600,55748041904,55799578776,58087309720,58151786952],[1455150900,51428904016,51451077032,55801784264,55827144624],[1455151200,52872380768,52891488480,55801784264,55827144624],[1455151500,47870480240,47878298096,53508032368,53525749472],[1455151800,50993427800,51003866320,53508032368,53525749472],[1455152100,45731937120,45728995688,46877502464,46882877584],[1455152400,45849838616,45852561120,46877502464,46882877584],[1455152700,44300335984,44303425704,46633380120,46654500576],[1455153000,45392295584,45414052200,46633380120,46654500576],[1455153300,42823630200,42838909416,43585365080,43579181024],[1455153600,42717099936,42719834816,43585365080,43579181024],[1455153900,40786328664,40804836392,40957678936,40963448704],[1455154200,39659204088,39669306248,40957678936,40963448704],[1455154500,37643151456,37654335264,41534535936,41511066728],[1455154800,39630263824,39626042712,41534535936,41511066728],[1455155100,36017012536,36047354576,37624900728,37633952952],[1455155400,36754564752,36761805120,37624900728,37633952952],[1455155700,34777674056,34784116168,34876471344,34879801904],[1455156000,34348069112,34352208056,34523623912,34538067144],[1455156300,35260930528,35248338120,38543064592,38545270232],[1455156600,37760872688,37774587872,38543064592,38545270232],[1455156900,36166013432,36206344032,36467787336,36511639848],[1455157200,36034558256,36062698032,36467787336,36511639848],[1455157500,35300957632,35298608200,35894143688,35903325256],[1455157800,34439960328,34447261816,35894143688,35903325256],[1455158100,32465703480,32473644472,36937057248,36962549496],[1455158400,35783091224,35811727264,36937057248,36962549496],[1455158700,34313109952,34344588736,36797489368,36819260736],[1455159000,35580302312,35599101576,36797489368,36819260736],[1455159300,33648403696,33652304568,35405492264,35387931280],[1455159600,34344286432,34339385880,35405492264,35387931280],[1455159900,32495243768,32507659776,33629058136,33615751296],[1455160200,32867967208,32868841392,33629058136,33615751296],[1455160500,31908539016,31934678192,33760867920,33771810400],[1455160800,33764023896,33756368368,33770940184,33771810400],[1455161100,33562147096,33552727160,33770940184,33722527320],[1455161400,30971268888,31031653504,33007063048,33101307240],[1455161700,27281360128,27276981176,29926433400,29953285104],[1455162000,28890771112,28914255288,29926433400,29953285104],[1455162300,27054924120,27068429448,28098101200,28104515896],[1455162600,27236081304,27247524760,28098101200,28104515896],[1455162900,26179205440,26196305256,28314734272,28318022560],[1455163200,28259386984,28262494064,28314734272,28318022560],[1455163500,28764503960,28768203576,30407192144,30413495448],[1455163800,29880725520,29887937168,30407192144,30413495448],[1455164100,28929099736,28944192120,29504769416,29535179736],[1455164400,28763184848,28786172000,29504769416,29535179736],[1455164700,27202253088,27215945088,27440702392,27473608664],[1455165000,26419923024,26429540248,27440702392,27473608664],[1455165300,24817213544,24794207080,27203456096,27249721440],[1455165600,25869957992,25899505032,27203456096,27249721440],[1455165900,23448120568,23453207160,24778743336,24816204312],[1455166200,24250755232,24280922184,24778743336,24816204312],[1455166500,23918224064,23936303688,26073973056,26102242376],[1455166800,26187961760,26205879264,26433937400,26429516776],[1455167100,27405165400,27400626728,29861800952,29856963680],[1455167400,28735907384,28747902464,29861800952,29856963680],[1455167700,26676924680,26712083960,27629837216,27631161016],[1455168000,27131378968,27132697672,27629837216,27631161016],[1455168300,26427913944,26433549144,27384884944,27401648008],[1455168600,26634085552,26645949696,27384884944,27401648008],[1455168900,25970599640,25960492936,29429293952,29377973744],[1455169200,28652634096,28634204200,29429293952,29377973744],[1455169500,26745307416,26792179440,26976683904,27029227824],[1455169800,25697180488,25723844520,26160061024,26192586480],[1455170100,25385998048,25396102064,27125386072,27125595800],[1455170400,26849776304,26850292400,27125386072,27125595800],[1455170700,27011521496,27016456792,28924976112,28939417136],[1455171000,28324805080,28341727280,28924976112,28939417136],[1455171300,27293817920,27310859112,28064978384,28068351544],[1455171600,27429487992,27434657712,28064978384,28068351544],[1455171900,26626837728,26641167008,28113604352,28141745184],[1455172200,27449148832,27459952928,28113604352,28141745184],[1455172500,26916950448,26900785736,30176677648,30198264928],[1455172800,29250446208,29273954672,30176677648,30198264928],[1455173100,28720085952,28738987096,33533863344,33523672424],[1455173400,32326064920,32328712792,33533863344,33523672424],[1455173700,30588736592,30610808776,32927577048,32927247584],[1455173905,32927577048,32927247584,32927577048,32927247584],[1455174216,33318989448,33335985072,33318989448,33335985072]],\"expiration\":5,\"hostname\":\"barryo-mac3.local\",\"user\":{\"id\":1,\"name\":null,\"email\":\"joe@example.com\"}}','2019-05-11 14:37:48'),(52,'8da95cdb-6ed0-4870-8820-f72eda355784','8da95cdb-8432-48ac-9613-0089efec57f7',NULL,1,'cache','{\"type\":\"hit\",\"key\":\"admin_home_customers\",\"value\":{\"4\":\"AS112\",\"2\":\"HEAnet\",\"5\":\"Imagine\",\"3\":\"PCH DNS\",\"1\":\"VAGRANT IXP\"},\"hostname\":\"barryo-mac3.local\",\"user\":{\"id\":1,\"name\":null,\"email\":\"joe@example.com\"}}','2019-05-11 14:37:48'),(53,'8da95cdb-6fa5-46f6-afba-b14a5d76c611','8da95cdb-8432-48ac-9613-0089efec57f7',NULL,1,'cache','{\"type\":\"missed\",\"key\":\"grapher::infrastructure002-protoall-bits-week-png.png\",\"hostname\":\"barryo-mac3.local\",\"user\":{\"id\":1,\"name\":null,\"email\":\"joe@example.com\"}}','2019-05-11 14:37:48'),(54,'8da95cdb-7049-4f54-8246-230162ea098f','8da95cdb-8432-48ac-9613-0089efec57f7',NULL,1,'cache','0','2019-05-11 14:37:48'),(55,'8da95cdb-7192-4c34-9f70-a0a3441e2d00','8da95cdb-8432-48ac-9613-0089efec57f7',NULL,1,'cache','{\"type\":\"missed\",\"key\":\"grapher::infrastructure002-protoall-bits-week-png.data\",\"hostname\":\"barryo-mac3.local\",\"user\":{\"id\":1,\"name\":null,\"email\":\"joe@example.com\"}}','2019-05-11 14:37:48'),(56,'8da95cdb-766a-4fb4-94d4-0bc0f3d4f424','8da95cdb-8432-48ac-9613-0089efec57f7',NULL,1,'cache','{\"type\":\"set\",\"key\":\"grapher::infrastructure002-protoall-bits-week-png.data\",\"value\":[[1454455800,81294362816,81285576440,88069944432,88106694760],[1454457600,70861891680,70879651504,80870320288,80939228632],[1454459400,61820900952,61836187968,66968352168,67014678488],[1454461200,53303777096,53305052864,62845305440,62931096184],[1454463000,46965082136,46977456320,49870513416,49862850104],[1454464800,40384565960,40392848672,45678738008,45636379632],[1454466600,38216781752,38224697040,40799594304,40807874304],[1454468400,35944029240,35956335128,39960841976,39987092904],[1454470200,32967694816,32978399560,35519703248,35509715920],[1454472000,30070160152,30082385616,33153776584,33172195320],[1454473800,24414319016,24419567880,26577381368,26585448936],[1454475600,23904858944,23912816624,28590035120,28612257208],[1454477400,30063386920,30074640448,31599770560,31591656360],[1454479200,27785035432,27794444608,32122766120,32086708720],[1454481000,29279816552,29284912832,30922314872,30918322360],[1454482800,30666057464,30679904160,35515780640,35503495920],[1454484600,37357081544,37371358112,41662438288,41650997536],[1454486400,43327042600,43340908536,45779987440,45788160344],[1454488200,50489368880,50505400192,54572934248,54572019256],[1454490000,57200462984,57214479368,61853898576,61778503184],[1454491800,63373343256,63393476680,68516850000,68544855408],[1454493600,69064495536,69074671520,74284354992,74181294872],[1454495400,70013051296,70043538464,73622118040,73634910728],[1454497200,73012892712,73015317200,78255883456,78166086880],[1454499000,73352036232,73383848632,76636250720,76666954016],[1454500800,75620013040,75637879848,80283491816,80223183568],[1454502600,76228595344,76243703864,81054704368,81053125928],[1454504400,75999819824,76021488840,87437191816,87489136552],[1454506200,78292126288,78308144544,83569412240,83555038472],[1454508000,78085704544,78111659880,83185345296,83187726680],[1454509800,78345803896,78368818680,81801939440,81804611224],[1454511600,78013894008,78033177688,85710113408,85601906328],[1454513400,79577108928,79609217552,83162806944,83214050000],[1454515200,81293692760,81305309584,88821967104,88636556016],[1454517000,84816123360,84846219120,88918795392,88935110568],[1454518800,87081432392,87091507432,94021487096,93905920584],[1454520600,85651853808,85688961648,90248297272,90225347048],[1454522400,83505885640,83518601184,90176417144,89989158328],[1454524200,83793986488,83805506328,86665926344,86676329168],[1454526000,85511366136,85497869576,96762520456,96761447928],[1454527800,88476854712,88476454824,92160504896,92161351168],[1454529600,90648352872,90620921072,97641977904,97464214088],[1454531400,94026246008,93904977944,102154981240,102208628152],[1454533200,94198091872,94169919552,105749290568,105506634816],[1454535000,97110596728,97097309832,101634669488,101587174880],[1454536800,95556494568,95519992032,106372441136,106175058520],[1454538600,91696338768,91653353688,97470650608,97394349312],[1454540400,86827737208,86786107000,97104688184,96882082448],[1454542200,79701142008,79671642200,86761395400,86724876184],[1454544000,70123725592,70109586560,79172492272,79062640088],[1454545800,60995768104,61013157120,65948718584,65968776328],[1454547600,51537418600,51541293520,60431968360,60464608536],[1454549400,46418530088,46429869168,49621685936,49624261992],[1454551200,40376559632,40385829512,46871778392,46816799256],[1454553000,36917014256,36924532872,40277913368,40279221896],[1454554800,35410467488,35421283280,38138767224,38152503432],[1454556600,33269377160,33277090248,36432315808,36427636560],[1454558400,30547892968,30557396104,34193108352,34194383304],[1454560200,31117152736,31126264400,33853356856,33868214264],[1454562000,27903115128,27911278000,30223005072,30198625336],[1454563800,29934257672,29944865160,31506004712,31492730776],[1454565600,29441624672,29452300504,31908599968,31920348632],[1454567400,30348890248,30362244816,31980203864,32017690152],[1454569200,32179799496,32194323696,38244244752,38223516480],[1454571000,39366868856,39381515616,44505251488,44497151872],[1454572800,45996071472,46009294272,50062178904,50060593008],[1454574600,54807359568,54826227872,58070999064,58095039016],[1454576400,58276891720,58291351432,62305218472,62272611040],[1454578200,65252116728,65276435440,68656748056,68691325488],[1454580000,70086442808,70100528008,75931323424,75882340384],[1454581800,71930873456,71954324240,76252227928,76232277096],[1454583600,73408477528,73422544360,80465551304,80381818024],[1454585400,73455023456,73476718576,76442831336,76449727248],[1454587200,73239310816,73256046544,78661128224,78660634608],[1454589000,74237058536,74259646000,77340780208,77300023376],[1454590800,74964145272,74979110536,82920864392,82946083856],[1454592600,76809569096,76824240448,80064745264,80073796136],[1454594400,78100494176,78123839856,84511234640,84459278928],[1454596200,80466829544,80497494000,85060051544,85097480376],[1454598000,79293975272,79302495264,86915567736,86794316736],[1454599800,80392108824,80429461136,84012543440,83991247112],[1454601600,81984696224,82000466944,90237052568,90099949968],[1454603400,83126675616,83167204552,87233539496,87259043504],[1454605200,86443378304,86456597808,93991535760,93760894992],[1454607000,84513029168,84541310664,87859023048,87861768752],[1454608800,82589188984,82590017384,89199089152,88960602080],[1454610600,81692031704,81710528048,87764791192,87737411496],[1454612400,81436198776,81425187960,90264573040,90276420992],[1454614200,84071655264,84065463216,88066380704,88019011000],[1454616000,86614534032,86575647704,92804890792,92501827776],[1454617800,89907786856,89891653920,94556947296,94526956864],[1454619600,90940322576,90902602992,97276698920,97074876920],[1454621400,91819805536,91790983824,96481755968,96440473776],[1454623200,92839727080,92804172520,100117498896,99991715848],[1454625000,92579642360,92534196984,97562440600,97511791880],[1454626800,87659012520,87616060016,96628441184,96517502616],[1454628600,80090564440,80075514848,86851725672,86812310264],[1454630400,71545922224,71554291416,80467583936,80437053704],[1454632200,62981815624,62994380352,68179041600,68182648104],[1454634000,53779200528,53789997832,60927596696,60876776144],[1454635800,47277584224,47286596712,50927426888,50909429640],[1454637600,41026057808,41039247616,46214662192,46217699320],[1454639400,39383384936,39393919384,41802158416,41810225840],[1454641200,38794575608,38806824920,42883686072,42879516648],[1454643000,35771532760,35782286024,39361694576,39420423120],[1454644800,32261048376,32276826344,34701189880,34725474368],[1454646600,29589233832,29600958336,31761446336,31781482656],[1454648400,29002513576,29014087848,32288004048,32278281648],[1454650200,29895545880,29909371168,31954177568,31968767016],[1454652000,27768558936,27777968400,29728539720,29759542872],[1454653800,29464792272,29472097472,31411361648,31421028200],[1454655600,30423344512,30440973616,35401609136,35419534864],[1454657400,36985723216,36999985512,41006096752,41038368760],[1454659200,44306848280,44322889208,48474948920,48501553440],[1454661000,52106017048,52127083488,56038247120,56071574888],[1454662800,58431305184,58442361528,63086069168,63100869216],[1454664600,65132835200,65154774240,70261450840,70274799136],[1454666400,69027263272,69045404928,73692640008,73615980072],[1454668200,74354528840,74374535976,78701134464,78744029528],[1454670000,74035681200,74059215200,80040231704,80019278488],[1454671800,76155425048,76172657296,79529553000,79586814032],[1454673600,77201223000,77208283480,84144927920,84061288616],[1454675400,76618118088,76645229032,80326782152,80357024160],[1454677200,79549706568,79571666872,89202983328,89164870240],[1454679000,77138576104,77160551048,81251421304,81218265112],[1454680800,77066053152,77082186408,84314591488,84319207408],[1454682600,78949139680,78976938696,83071722112,83113883416],[1454684400,79057866040,79081264416,86101271528,85996828208],[1454686200,79144830352,79178862640,84364029176,84368287792],[1454688000,83198448968,83213494168,90495799128,90247050520],[1454689800,84953897344,84987291232,88368824480,88403817168],[1454691600,85091261696,85110719544,92081300744,91926442608],[1454693400,83391813912,83418296216,86807594640,86801574296],[1454695200,81663347184,81682287888,88283661112,88179522120],[1454697000,81375857456,81407486760,84702708408,84711773792],[1454698800,81688933936,81707529960,88450534256,88293906176],[1454700600,82318779800,82342788136,85426675872,85443623304],[1454702400,84787308568,84809255376,91408951816,91308697504],[1454704200,88754271928,88768685400,92482828864,92560383840],[1454706000,90803521808,90832576440,97675678448,97722071904],[1454707800,91612321776,91643840680,95574520072,95619477240],[1454709600,89608557896,89611301280,96616261504,96506777952],[1454711400,89500657448,89537073720,93721649416,93832095160],[1454713200,86665768776,86681079080,95677260024,95611031704],[1454715000,81781610280,81795772496,87560692520,87544151232],[1454716800,76014803152,76035591240,84960518456,85013712240],[1454718600,70021784376,70030907080,75041417936,75036067680],[1454720400,61331252176,61346404144,69398993992,69565920552],[1454722200,53780100328,53796932144,57557002832,57597809896],[1454724000,46353875272,46365433512,51565310088,51524669976],[1454725800,41243110496,41253014344,43854038928,43880993672],[1454727600,36769041240,36781714640,41075537720,41118023992],[1454729400,33497848976,33508769408,36320000176,36326492688],[1454731200,31045760368,31055440832,34052138040,34054560520],[1454733000,29848131368,29861875104,31416929104,31444169152],[1454734800,27358375856,27369089312,29617380512,29618447760],[1454736600,29378137528,29388613800,31919808512,31939410176],[1454738400,27611983816,27622568328,33403838184,33425163720],[1454740200,27089762760,27099282832,28703305824,28713841776],[1454742000,28737630024,28751976696,32431573736,32458045776],[1454743800,30756620824,30770650680,33567867896,33595693952],[1454745600,35672584032,35679646240,39091242744,39096327632],[1454747400,43631690192,43655557736,47215009296,47232414912],[1454749200,50212727384,50220092648,53546642672,53565627840],[1454751000,56257103664,56281898760,59389403912,59409253128],[1454752800,60004430168,60017699272,63455192864,63471363856],[1454754600,62144861376,62168576608,64350585784,64353294504],[1454756400,63882762576,63894235760,68615160216,68473431120],[1454758200,65726957088,65751822008,68794827056,68795088240],[1454760000,67498977400,67514699648,72489149360,72382598112],[1454761800,69373478600,69398247056,72178800752,72203651264],[1454763600,68556996040,68574036776,77100602488,77117762960],[1454765400,68938490880,68965160696,72240949736,72275569424],[1454767200,70057915504,70074033808,75362620256,75252930240],[1454769000,71577845848,71599414120,74736074312,74750242520],[1454770800,73959031800,73972174216,79940258664,79881389584],[1454772600,77195529776,77223553840,80466923752,80459406832],[1454774400,78513721400,78528805352,84462663952,84454338640],[1454776200,78969925656,79007048192,82615809008,82613047968],[1454778000,78719323720,78736046632,84185175488,84099313736],[1454779800,80959915944,80984503856,84152050328,84139376864],[1454781600,81118758912,81139350208,88388113096,88325246408],[1454783400,82487747352,82507356800,86789030376,86799310160],[1454785200,81723737320,81751659136,88420317368,88423779616],[1454787000,81514561576,81556262880,84660799584,84715692344],[1454788800,82846652816,82862919008,89335489944,89178351248],[1454790600,84816813464,84848066832,89194987320,89200051072],[1454792400,85311473904,85321879280,92615473608,92494265448],[1454794200,87908701736,87941461984,92803561328,92879680872],[1454796000,88546960872,88564808736,96976257264,96808990608],[1454797800,86625768440,86649160472,92563798240,92602694544],[1454799600,83144457928,83161564240,92505249520,92422916056],[1454801400,77998634264,78026186888,82895489248,82973338632],[1454803200,72780651152,72784481088,80267213200,80111372928],[1454805000,68267004384,68287852368,73934205232,73996117104],[1454806800,60477147464,60481616192,67816176208,67737847912],[1454808600,54907568168,54926146008,61041737280,61115098312],[1454810400,47030399904,47031295600,53222990008,53229097016],[1454812200,44998669392,45016382048,47951635440,48021841232],[1454814000,40884028328,40896067808,44915492496,44950109872],[1454815800,35649389592,35661029072,40349572016,40388050600],[1454817600,32954768824,32967110376,37344349656,37381316752],[1454819400,30661975616,30672840912,33084184992,33114596904],[1454821200,28931749064,28940710024,31144626528,31169671712],[1454823000,29538408176,29550405688,32015687992,32025484304],[1454824800,27059677136,27065867048,30969181024,30971519128],[1454826600,27703711848,27718478784,29947035176,29982042216],[1454828400,28359654344,28371151528,33329150880,33348586192],[1454830200,28960660032,28975635304,30932819896,30923375600],[1454832000,31942829064,31950467664,35128894512,35143944800],[1454833800,40435342240,40458916200,46566526872,46602123648],[1454835600,47722869232,47736410976,51900868192,51949814040],[1454837400,55738901680,55760465424,60708801920,60730316856],[1454839200,61086942328,61096311736,65518079544,65369489728],[1454841000,62854760040,62876951528,67179225960,67188450584],[1454842800,65180849360,65190971552,69487103248,69495334440],[1454844600,68553563664,68580929128,71955718784,72003619200],[1454846400,70157175488,70166860976,75890306800,75721886168],[1454848200,68443916048,68461305728,72001921280,72062211600],[1454850000,69710763688,69725118880,75714028376,75615794392],[1454851800,71449959712,71476562496,75005339288,75011123528],[1454853600,72142666040,72157543400,78179738488,78095240208],[1454855400,73705399160,73727919000,77932969768,77956928000],[1454857200,76342537920,76351363864,81806770088,81712828104],[1454859000,81822434792,81830423496,86922176896,86935487032],[1454860800,83265260200,83294779912,90762971896,90889921160],[1454862600,86888626304,86897184032,91536147744,91555520368],[1454864400,86125819144,86166091184,95301490704,95464561840],[1454866200,81847453152,81879707776,85640523488,85704200272],[1454868000,82974618880,82997094144,91062651744,90990369592],[1454869800,83241200320,83269713016,86637064912,86668095288],[1454871600,85909254528,85921878056,98680720360,98691446280],[1454873400,88315410392,88344324992,92066529048,92111042544],[1454875200,89900599928,89915385160,98334423920,98177988568],[1454877000,90367178048,90388415552,94892186864,94925800224],[1454878800,90345120128,90338759160,99092140584,98857492400],[1454880600,93315708416,93335305136,97619499600,97647244152],[1454882400,93470750928,93451614312,103445624824,103253903136],[1454884200,91365124328,91353232704,96256066760,96269899144],[1454886000,84740946320,84727951480,94223274368,94089304408],[1454887800,77301934320,77305954168,83973991760,83957138472],[1454889600,67836065952,67843247336,76283912256,76279249720],[1454891400,60934372800,60948731792,66883576352,66917665880],[1454893200,50892264016,50898265872,59038233416,59064762560],[1454895000,45922650856,45937474288,49353227456,49364606376],[1454896800,38668500408,38675954576,43880770640,43842198568],[1454898600,36530061880,36535207000,39964861504,39955435128],[1454900400,32756280120,32773812648,37160851880,37251710728],[1454902200,31586843792,31601126800,33820380192,33846207712],[1454904000,30634813184,30646727960,32867181960,32849335512],[1454905800,30817581032,30831990736,33498671352,33509499248],[1454907600,28508081712,28519002072,31850589200,31899893384],[1454909400,28514713576,28526004896,30218647944,30208335400],[1454911200,27370284368,27377324168,30896659152,30919054968],[1454913000,26343020464,26353420256,27614120144,27632757200],[1454914800,28320753128,28330517200,32463105424,32485427296],[1454916600,36234435696,36250292520,41030791240,41052216360],[1454918400,43479495656,43494124368,47772260344,47782113784],[1454920200,52311196464,52336421944,56167824344,56210392200],[1454922000,58119150632,58124864776,62430923456,62387087576],[1454923800,66945033720,66969680008,71456705080,71495807984],[1454925600,72319675408,72334758816,78233082616,78207399488],[1454927400,74350189800,74384333984,77351525072,77402259248],[1454929200,75018344400,75026661704,80539634832,80449555392],[1454931000,76143645984,76170238048,80474782448,80425504456],[1454932800,77568531568,77584673824,83851924176,83762926232],[1454934600,78703458696,78723356896,82353935056,82345517744],[1454936400,80538368480,80558048424,87906104496,87855474432],[1454938200,79329207168,79353015192,84701163736,84686792720],[1454940000,78059302936,78075453048,84953279704,84842232016],[1454941800,79648084088,79678243608,83154753848,83192930704],[1454943600,79861005040,79879672944,87632547432,87518351384],[1454945400,80216070024,80245069336,83536516120,83516951152],[1454947200,81035854216,81051648528,89207838304,89036841512],[1454949000,83888855984,83930306664,88203533032,88244742448],[1454950800,86290247128,85940388464,95481893728,92886730448],[1454952600,84341445440,84368681480,87781001968,87784991792],[1454954400,82526607968,82537352144,91065452488,90880267512],[1454956200,83205070744,83223681720,86916244952,86911430432],[1454958000,85974861696,85955119024,94870145824,94884743488],[1454959800,87590515256,87580183840,91873256184,91847332312],[1454961600,90259693744,90226306368,97502486504,97286684704],[1454963400,92742870824,92719081048,97924667472,97894228304],[1454965200,93098099000,93061335312,101107937352,100965391688],[1454967000,95236527320,95191469936,99685203416,99595539920],[1454968800,95407964936,95359657200,103500551976,103368779344],[1454970600,93488040720,93432792688,97732670264,97671919936],[1454972400,87095861600,87063618224,97213971000,97203384152],[1454974200,73853506040,73873037816,80612326656,80619506224],[1454976000,63716405648,63721648896,72607502488,72412285632],[1454977800,55054658984,55067690976,60311020072,60322670728],[1454979600,48498144600,48501415480,56643262584,56658586056],[1454981400,44272526792,44284996752,47014808008,47033847048],[1454983200,38750026464,38763359064,44346460304,44351784144],[1454985000,34199270672,34212675024,37762416896,37789498768],[1454986800,32231608760,32244283600,35247101672,35284426032],[1454988600,31305485912,31317945960,33665030320,33646376688],[1454990400,29637662360,29653914640,33404294208,33487706688],[1454992200,27895272080,27902689280,29698939560,29707282832],[1454994000,26896945440,26911209528,30098653608,30184091408],[1454994300,26997528560,27016147096,29243767472,29273266224],[1454994600,28555356880,28572670008,29243767472,29273266224],[1454994900,27632946808,27626263200,29281823888,29283592392],[1454995200,28731024264,28736591960,29281823888,29283592392],[1454995500,28239276896,28264665096,30242587176,30297833464],[1454995800,29533703312,29563942856,30242587176,30297833464],[1454996100,28845010360,28834004648,32149475432,32193755264],[1454996400,30900827840,30941132192,32149475432,32193755264],[1454996700,28546890864,28571254344,29422495944,29427928056],[1454997000,28831344952,28837382528,29422495944,29427928056],[1454997300,27780462168,27792883232,28399202632,28424414000],[1454997600,27875247016,27897542896,28399202632,28424414000],[1454997900,27125257656,27139617184,28120455504,28130514152],[1454998200,27559443008,27567579760,28120455504,28130514152],[1454998500,26993432256,26999999304,28678699064,28692005056],[1454998800,27947540904,27963277816,28678699064,28692005056],[1454999100,27381309880,27404034840,29982390400,30009595776],[1454999400,29305953504,29324062248,29982390400,30009595776],[1454999700,28561779064,28565849416,31098570112,31122463784],[1455000000,29725569936,29737944008,31098570112,31122463784],[1455000300,28398652928,28396237848,36961402232,37011148112],[1455000600,34289813784,34327101120,36961402232,37011148112],[1455000900,29176750640,29182875872,30881229456,30876170320],[1455001200,30625323256,30632641824,30881229456,30876170320],[1455001500,31077556528,31099358712,33703653712,33693491072],[1455001800,33127250424,33129963312,33703653712,33693491072],[1455002100,33256848936,33278918304,36908125928,36907790360],[1455002400,36710826736,36717719952,36908125928,36907790360],[1455002700,37089449752,37102290152,39245528432,39232063640],[1455003000,38450960760,38460627608,39245528432,39232063640],[1455003300,38228692536,38274212720,43842677688,43835296864],[1455003600,42834358024,42835448160,43842677688,43835296864],[1455003900,41548202080,41564877552,43874263672,43883895480],[1455004200,43079733976,43094350272,43874263672,43883895480],[1455004500,42230153856,42251023712,44747117736,44754653136],[1455004800,44701558904,44713942656,44747117736,44754653136],[1455005100,45672205592,45700062048,48526541432,48566391112],[1455005400,47718891360,47749777232,48526541432,48566391112],[1455005700,47123721624,47142526784,50174802048,50212914120],[1455006000,49678311112,49707115736,50174802048,50212914120],[1455006300,49624524048,49632136032,52373118320,52379485736],[1455006600,51113208984,51152829800,52373118320,52379485736],[1455006900,50206778984,50270220832,56759019024,56649152680],[1455007200,54998899416,54923776480,56759019024,56649152680],[1455007500,52410440416,52421813464,55470254240,55510774624],[1455007800,54903742760,54937434824,55470254240,55510774624],[1455008100,55151866848,55170196080,58933399960,58950114240],[1455008400,58432274040,58443166808,58933399960,58950114240],[1455008700,59654960648,59654018912,65678837720,65679798584],[1455009000,64955535872,64957204376,65678837720,65679798584],[1455009300,64503760904,64525826616,67370427576,67440163656],[1455009600,65973905624,66028283096,67370427576,67440163656],[1455009900,64637866160,64653405624,68880974400,68882106936],[1455010200,66932647592,66939890728,68880974400,68882106936],[1455010500,64672315208,64696440552,71937087448,71973833704],[1455010800,69721421584,69757115608,71937087448,71973833704],[1455011100,66038508624,66054881008,69249529656,69222240296],[1455011400,67849782152,67849005720,69249529656,69222240296],[1455011700,66222551440,66265585960,70049701960,70053646392],[1455012000,69242723760,69258310048,70049701960,70053646392],[1455012300,68932630536,68957902528,72691549048,72675776704],[1455012600,71182860808,71184290240,72691549048,72675776704],[1455012900,69068109560,69115498784,72318656504,72386103784],[1455013200,70374610608,70416969352,72318656504,72386103784],[1455013500,67595984768,67600373168,71712579944,71764481352],[1455013800,69746853992,69805480536,71712579944,71764481352],[1455014100,67785133832,67831034600,77050264040,76989853144],[1455014400,74095400816,74063279696,77050264040,76989853144],[1455014700,69105139064,69138139416,72728835216,72772489840],[1455015000,70908607104,70940345456,72728835216,72772489840],[1455015300,68213344392,68222541808,71818885632,71838670352],[1455015600,70676665504,70692772656,71818885632,71838670352],[1455015900,69905450360,69915335024,74802386552,74817927352],[1455016200,73441907160,73460362144,74802386552,74817927352],[1455016500,71484836072,71513912792,74332375240,74372445864],[1455016800,73075314264,73108008144,74332375240,74372445864],[1455017100,71589785384,71604165560,75196148816,75206179992],[1455017400,72904394000,72944930688,75196148816,75206179992],[1455017700,70228145520,70293697896,80365006672,80254065720],[1455018000,77526182768,77461481752,80365006672,80254065720],[1455018300,73187682328,73223005104,77783809336,77819756640],[1455018600,75942398672,75977052760,77783809336,77819756640],[1455018900,73412768448,73444890664,77187889088,77220687800],[1455019200,75721614248,75741161040,77187889088,77220687800],[1455019500,74435880264,74434527648,79346702040,79365472488],[1455019800,78171472200,78201439088,79346702040,79365472488],[1455020100,76821345608,76865794920,80130484664,80147642440],[1455020400,77874256264,77899380648,80130484664,80147642440],[1455020700,74501618800,74529626776,78813618648,78802805768],[1455021000,75950236496,75968816048,78813618648,78802805768],[1455021300,72414236768,72463991800,82769117576,82692514960],[1455021600,78001062704,77966150128,82769117576,82692514960],[1455021900,71146516504,71200188488,90311723920,90337707184],[1455022200,85057768560,85068444408,90311723920,90337707184],[1455022500,75520180104,75528993584,80305264600,80396943128],[1455022800,78231837360,78301885704,80305264600,80396943128],[1455023100,75113336952,75135626304,79091962720,79115277288],[1455023400,77093653296,77110413104,79091962720,79115277288],[1455023700,74308783296,74326282264,78729876472,78788127552],[1455024000,76709864016,76754922440,78729876472,78788127552],[1455024300,73956795784,73977676520,78590860400,78625529936],[1455024600,75868316320,75913141000,78590860400,78625529936],[1455024900,72697970752,72734189296,83882294008,83795068496],[1455025200,80927458312,80873705480,83882294008,83795068496],[1455025500,75688031048,75721058776,78974151696,79042871720],[1455025800,77546967600,77594913040,78974151696,79042871720],[1455026100,75590605648,75593481704,79103746016,79113650320],[1455026400,77427030504,77441388264,79103746016,79113650320],[1455026700,75349445784,75375200184,79747886808,79777586824],[1455027000,78378562768,78412239040,79747886808,79777586824],[1455027300,77086570592,77134779552,81882227280,81945172232],[1455027600,79981209312,80028380584,81882227280,81945172232],[1455027900,76986501440,76992791272,80451228080,80443642392],[1455028200,77950953096,77971532360,80451228080,80443642392],[1455028500,74881495088,74943282960,84798178136,84773715272],[1455028800,81627885152,81614522824,84798178136,84773715272],[1455029100,76319217272,76326214024,80325849480,80323448008],[1455029400,78452546352,78468950568,80325849480,80323448008],[1455029700,75747611416,75800724744,79408869240,79450208744],[1455030000,78061584744,78086828200,79408869240,79450208744],[1455030300,76551873808,76558786248,80502420152,80555772656],[1455030600,78976651984,79024117856,80502420152,80555772656],[1455030900,77205500816,77239874992,81383849728,81418251656],[1455031200,79134726360,79157940232,81383849728,81418251656],[1455031500,75944794416,75976223992,80519253120,80639668144],[1455031800,78039367064,78155024224,80519253120,80639668144],[1455032100,75048053736,75108285088,87094254400,86956265664],[1455032400,83612504280,83505420952,87094254400,86956265664],[1455032700,77611007488,77605728992,82337636080,82421621344],[1455033000,80424282240,80480684312,82337636080,82421621344],[1455033300,77834526680,77841962456,82271623568,82312101120],[1455033600,80380078016,80413621624,82271623568,82312101120],[1455033900,77725726312,77741505808,82038367296,82048597424],[1455034200,80579684480,80592640384,82038367296,82048597424],[1455034500,78941799968,78963732584,83354989504,83384403496],[1455034800,81659112920,81690068544,83354989504,83384403496],[1455035100,79614978816,79654002384,84061012728,84112503400],[1455035400,81319109240,81422628544,84061012728,84112503400],[1455035700,78132175536,78269462024,90043346272,89845526432],[1455036000,86860219952,86731025488,90043346272,89845526432],[1455036300,81881949680,81893353976,87192422240,87177791384],[1455036600,85202551352,85216681392,87192422240,87177791384],[1455036900,82300036032,82351064120,86358430712,86337364272],[1455037200,84734183696,84728167448,86358430712,86337364272],[1455037500,82530119184,82557994200,86567521000,86595024088],[1455037800,84405025136,84425585512,86567521000,86595024088],[1455038100,81281008192,81289297240,85697560280,85714168048],[1455038400,83701465648,83717783432,85697560280,85714168048],[1455038700,80898070096,80907738008,84765412624,84759589920],[1455039000,82350802760,82411275120,84765412624,84759589920],[1455039300,79177439264,79292610784,87776229768,87533323152],[1455039600,84438792200,84274906568,87776229768,87533323152],[1455039900,78549724672,78553172544,81925422608,81920679312],[1455040200,79949766496,79932208328,81925422608,81920679312],[1455040500,77267822144,77239250936,81905981952,81924753616],[1455040800,80224017288,80228703432,81905981952,81924753616],[1455041100,78482163384,78467573848,83715978272,83732960848],[1455041400,82047186680,82049504712,83715978272,83732960848],[1455041700,80258438480,80240253000,85292992400,85306538432],[1455042000,83413810296,83418343696,85292992400,85306538432],[1455042300,81428595544,81409632992,87294325824,87265064904],[1455042600,84542686976,84556110696,87294325824,87265064904],[1455042900,81260824200,81302015424,92631835024,92404065344],[1455043200,88579304048,88415578104,92631835024,92404065344],[1455043500,82300169080,82273109584,90980048672,90934361200],[1455043800,88223074736,88169093824,90980048672,90934361200],[1455044100,83648466568,83607357136,87484584416,87524644624],[1455044400,85515004648,85535962184,87484584416,87524644624],[1455044700,82887883952,82867679384,87480918088,87464260680],[1455045000,85389782872,85367840408,87480918088,87464260680],[1455045300,82695215336,82659009032,87819277400,87776534136],[1455045600,85799276880,85750662064,87819277400,87776534136],[1455045900,82956304232,82915274168,87351284008,87365162536],[1455046200,85339618168,85366708448,87351284008,87365162536],[1455046500,83677873288,83685005160,95223350016,95031168120],[1455046800,92075294768,91930208000,95223350016,95031168120],[1455047100,86459459104,86414906136,90157818536,90101858144],[1455047400,88625247912,88569182448,90157818536,90101858144],[1455047700,86873025712,86817662784,91426354688,91373491024],[1455048000,90540015112,90493074088,91426354688,91373491024],[1455048300,90511439544,90483851776,95758874616,95746321784],[1455048600,94183383680,94156801912,95758874616,95746321784],[1455048900,92446469312,92387272872,97533544232,97473281496],[1455049200,95441351840,95396379664,97533544232,97473281496],[1455049500,92452981056,92416703416,97242940184,97135505064],[1455049800,94629575704,94602596768,97242940184,97135505064],[1455050100,91431778584,91512032936,103031991896,102790254984],[1455050400,99203271872,99028649952,103031991896,102790254984],[1455050700,92828672840,92791878568,98537589544,98470069704],[1455051000,96364872712,96310789304,98537589544,98470069704],[1455051300,93138510808,93098594384,97417917872,97336156336],[1455051600,95829803544,95766382712,97417917872,97336156336],[1455051900,94672032352,94632776088,101139582280,101054329696],[1455052200,99189459104,99114044000,101139582280,101054329696],[1455052500,96208952040,96153068368,100006583968,99943496128],[1455052800,97300332784,97231034448,100006583968,99943496128],[1455053100,93268881848,93204377016,98565059976,98550048768],[1455053400,95434123176,95429843680,98565059976,98550048768],[1455053700,91390611952,91364997136,102852221704,102649425488],[1455054000,99090777472,98946787968,102852221704,102649425488],[1455054300,92918739992,92884130368,99118705984,99021422888],[1455054600,97517969592,97415310280,99118705984,99021422888],[1455054900,95169439536,95097934760,98397964584,98441098416],[1455055200,96166075432,96178905312,98397964584,98441098416],[1455055500,93086773744,93009284448,98024050744,97886772896],[1455055800,96083081008,95993608880,98024050744,97886772896],[1455056100,93717384928,93704018128,99185850912,99086027360],[1455056400,96827253120,96743157488,99185850912,99086027360],[1455056700,92825047272,92769778664,96953317024,96876401888],[1455057000,94029893568,93975406752,96953317024,96876401888],[1455057300,89703711904,89660065080,99281828936,99078903440],[1455057600,95350204928,95194407384,99281828936,99078903440],[1455057900,87845615464,87797144928,91215632384,91173729952],[1455058200,88773297944,88719101224,91215632384,91173729952],[1455058500,84557073768,84483290360,87746554312,87693106256],[1455058800,85838841760,85783272552,87746554312,87693106256],[1455059100,83060305312,83009165848,86962139632,86935328496],[1455059400,84661293280,84627166768,86962139632,86935328496],[1455059700,81060978744,81002702368,85037370688,84958525720],[1455060000,82597658992,82530203064,85037370688,84958525720],[1455060300,78267503432,78237497456,80924960344,80927611536],[1455060600,78077890864,78067570208,80924960344,80927611536],[1455060900,73798424544,73745453232,81538766648,81433964448],[1455061200,78183656544,78101321192,81538766648,81433964448],[1455061500,71305012504,71265692824,73216050512,73155698864],[1455061800,71453325936,71394328576,73216050512,73155698864],[1455062100,68033672472,67989155808,69940441440,69927056720],[1455062400,68980620904,68947727984,69940441440,69927056720],[1455062700,67390119592,67350429608,69306464512,69377142240],[1455063000,66891074680,66935143792,69306464512,69377142240],[1455063300,61831356248,61820699872,63443232768,63453169424],[1455063600,61856827176,61870061576,63443232768,63453169424],[1455063900,58624875704,58649997128,60333124656,60369663856],[1455064200,58015989928,58043615896,60333124656,60369663856],[1455064500,53880685040,53886141944,59094139848,59095613336],[1455064800,55281784232,55282544824,59094139848,59095613336],[1455065100,48905361704,48912135752,58300635888,58341265472],[1455065400,54657716608,54690801176,58300635888,58341265472],[1455065700,47611427064,47620508784,49777461232,49766015064],[1455066000,49110558680,49083554248,49777461232,49766015064],[1455066300,48339673152,48318968808,50298733184,50388292440],[1455066600,48843940512,48908541744,50298733184,50388292440],[1455066900,46415288440,46426136792,48496692320,48512166296],[1455067200,46649537744,46669275808,48496692320,48512166296],[1455067500,42836521544,42862085072,43818396840,43833645560],[1455067800,42291356536,42298133288,43818396840,43833645560],[1455068100,39642080320,39645777408,43066089832,43138314456],[1455068400,41292661360,41348172256,43066089832,43138314456],[1455068700,37498099336,37515383728,39029098088,39053916952],[1455069000,37872115464,37894065344,39029098088,39053916952],[1455069300,35222042232,35232543256,35612543264,35610130736],[1455069600,34857925880,34847281576,35612543264,35610130736],[1455069900,33739848936,33726763856,35649503672,35685016640],[1455070200,34812435920,34836475512,35649503672,35685016640],[1455070500,33059329448,33069502600,33888125064,33938088152],[1455070800,33187793592,33231962088,33888125064,33938088152],[1455071100,31832850232,31853085776,32613801024,32604908632],[1455071400,31820316752,31810533432,32613801024,32604908632],[1455071700,30723298960,30719529208,33850150424,33877704112],[1455072000,33174779680,33185087744,33850150424,33877704112],[1455072300,31531621488,31528068592,31694711904,31667226344],[1455072600,30360366464,30402474472,31105228480,31164246528],[1455072900,29135284200,29142180560,30266916720,30280157928],[1455073200,29959632928,29969604096,30266916720,30280157928],[1455073500,30202861200,30209903288,32667809816,32686414944],[1455073800,31675619296,31684634024,32667809816,32686414944],[1455074100,29861355920,29859811264,30909246488,30936379464],[1455074400,30530590168,30547225032,30909246488,30936379464],[1455074700,30262904480,30262266528,31853782264,31870615792],[1455075000,30998244400,31005229984,31853782264,31870615792],[1455075300,29780734808,29781267256,32204869784,32261188592],[1455075600,31143441160,31195027896,32204869784,32261188592],[1455075900,29227019528,29256414024,30204904168,30202975040],[1455076200,29106788920,29108831400,30204904168,30202975040],[1455076500,27010476912,27021255944,27919994488,27930502960],[1455076800,27484464720,27483379464,27919994488,27930502960],[1455077100,27310705960,27300962936,29347327424,29381078784],[1455077400,28404316784,28425114304,29347327424,29381078784],[1455077700,26690527424,26693030432,27714515408,27745034728],[1455078000,27379646480,27409549784,27714515408,27745034728],[1455078300,27081276576,27100498304,28249896144,28244776056],[1455078600,27562810480,27559919728,28249896144,28244776056],[1455078900,26656852024,26667098296,29001877616,29042877368],[1455079200,27707917464,27734171648,29001877616,29042877368],[1455079500,25213273872,25217971728,25991300440,26022830616],[1455079800,26063091912,26085549352,26222885848,26225149144],[1455080100,26575177672,26583716520,27481070960,27505746936],[1455080400,26766586712,26788543192,27481070960,27505746936],[1455080700,25833749192,25855062360,27461327448,27496311456],[1455081000,26896122592,26919900776,27461327448,27496311456],[1455081300,26449499192,26453044760,28404808280,28418340944],[1455081600,27562206984,27568212592,28404808280,28418340944],[1455081900,26322588768,26323718200,27786173880,27816053704],[1455082200,27033630392,27050041224,27786173880,27816053704],[1455082500,26042264704,26042598536,28284920504,28331301488],[1455082800,27031148888,27068048728,28284920504,28331301488],[1455083100,24828721936,24844807432,26101218952,26116406448],[1455083400,25643173128,25655252912,26101218952,26116406448],[1455083700,25721702136,25726966312,28420441992,28425429248],[1455084000,28293065784,28291453696,28420441992,28425429248],[1455084300,28548749072,28544571136,29890722760,29916075312],[1455084600,29075301648,29100240576,29890722760,29916075312],[1455084900,28185745496,28209928464,30386426720,30410955240],[1455085200,29499436408,29514404064,30386426720,30410955240],[1455085500,28262904512,28261060688,30081670584,30090475232],[1455085800,28891631816,28905143696,30081670584,30090475232],[1455086100,27328712744,27351533392,31033848248,31053098328],[1455086400,29801009768,29819850344,31033848248,31053098328],[1455086700,28117716472,28133353168,31101656656,31110207288],[1455087000,30440405280,30450270392,31101656656,31110207288],[1455087300,30081772128,30090568560,32944246336,32942771344],[1455087600,33846614104,33845827704,35855110128,35855856392],[1455087900,36641122752,36652470344,38730766072,38770297688],[1455088200,37739576224,37775933000,38730766072,38770297688],[1455088500,36572185248,36593896536,39426283744,39428142144],[1455088800,38691676672,38696633152,39426283744,39428142144],[1455089100,37712378992,37729327128,39594295368,39624207272],[1455089400,39153293072,39182580808,39594295368,39624207272],[1455089700,39721246664,39744386888,45668084568,45673491200],[1455090000,44121416896,44130374520,45668084568,45673491200],[1455090300,42004268904,42017729576,45142435232,45147771176],[1455090600,45036310216,45042742352,45142435232,45147771176],[1455090900,46138112432,46157964864,49695276832,49744320968],[1455091200,48704861744,48745032208,49695276832,49744320968],[1455091500,48320473128,48345623600,53159232144,53196960384],[1455091800,52710264952,52726269864,53159232144,53196960384],[1455092100,53150962544,53146168648,56979286240,57047743280],[1455092400,55731221072,55772544928,56979286240,57047743280],[1455092700,54426649488,54429985104,58166762000,58226240064],[1455093000,56983345856,57038807040,58166762000,58226240064],[1455093300,56367565200,56399948976,63664404272,63645678720],[1455093600,62334278008,62315130936,63664404272,63645678720],[1455093900,60675006752,60676125264,63789009880,63844578816],[1455094200,62792702408,62834612000,63789009880,63844578816],[1455094500,62634565840,62636187904,67929524440,67904078400],[1455094800,67390912600,67391237312,67929524440,67904078400],[1455095100,67616795608,67674495400,71280383688,71338120488],[1455095400,69667481984,69711188312,71280383688,71338120488],[1455095700,68152401296,68169141144,73577208688,73605093008],[1455096000,73637539000,73646417440,73771822616,73738397632],[1455096300,76144234568,76141724952,82346805584,82425122912],[1455096600,78755313152,78859023456,82346805584,82425122912],[1455096900,72377971344,72495890392,78583726208,78539225160],[1455097200,76220505064,76206568656,78583726208,78539225160],[1455097500,72892382512,72929459568,77651714752,77647729648],[1455097800,75920424816,75925632312,77651714752,77647729648],[1455098100,73166972576,73177371168,76043043696,76013519864],[1455098400,74727278608,74735070696,76043043696,76013519864],[1455098700,74679439840,74725785480,82093117824,82026456424],[1455099000,79377992504,79358617984,82093117824,82026456424],[1455099300,75278515280,75356620704,80446354720,80503799336],[1455099600,78189871888,78231038880,80446354720,80503799336],[1455099900,75053022576,75057491952,80066078072,80069306472],[1455100200,78330502504,78319023624,80066078072,80069306472],[1455100500,76431231008,76411266624,83969626808,84042751400],[1455100800,80860915456,80920334192,83969626808,84042751400],[1455101100,75600768920,75638655720,79728309992,79788805752],[1455101400,77875616696,77926698776,79728309992,79788805752],[1455101700,74667556776,74709395728,77451116024,77525860472],[1455102000,76152664216,76195403472,77451116024,77525860472],[1455102300,75640083456,75622772888,82081848960,82097264312],[1455102600,80143787024,80178201664,82081848960,82097264312],[1455102900,76937368032,76996489824,79881250040,79893630512],[1455103200,78071203584,77697806688,79881250040,79893630512],[1455103500,75326487392,74212257776,78740305888,77939339784],[1455103800,76572577008,76002536088,78740305888,77939339784],[1455104100,73976297056,73959697128,83310422928,83384229064],[1455104400,80723821584,80800311840,83310422928,83384229064],[1455104700,76058777496,76117968824,78682295072,78680880232],[1455105000,77604061312,77595811656,78682295072,78680880232],[1455105300,76745626144,76737563376,80944281448,80977796936],[1455105600,79540016752,79579763080,80944281448,80977796936],[1455105900,77121693368,77168260168,78873955408,78902352864],[1455106200,77182871744,77209300000,78873955408,78902352864],[1455106500,75152113784,75175080096,79683667208,79709041680],[1455106800,77767379064,77785911160,79683667208,79709041680],[1455107100,74645194152,74659967360,77862801352,77908708256],[1455107400,75568464040,75601448472,77862801352,77908708256],[1455107700,72868083160,72886997512,82223250040,82299142008],[1455108000,78407664872,78459271736,82223250040,82299142008],[1455108300,72483440456,72488282592,82356528936,82392707704],[1455108600,79125142720,79156133872,82356528936,82392707704],[1455108900,73234816432,73264208720,76639138544,76694538832],[1455109200,75317974680,75349751528,76639138544,76694538832],[1455109500,73422129104,73408859784,76199793280,76206556176],[1455109800,74833009720,74841116576,76199793280,76206556176],[1455110100,73507246784,73536051536,78197852824,78273605720],[1455110400,76514553880,76574953312,78197852824,78273605720],[1455110700,74501727144,74533423688,78960252296,79006017072],[1455111000,76451744120,76499064080,78960252296,79006017072],[1455111300,73728061712,73757663712,84375439136,84323944144],[1455111600,81281609600,81244905736,84375439136,84323944144],[1455111900,75572990240,75588578184,78756997088,78824954760],[1455112200,77446698688,77494767024,78756997088,78824954760],[1455112500,75641053920,75644346552,78841192576,78846889072],[1455112800,77528346544,77544030848,78841192576,78846889072],[1455113100,76389627600,76425186784,81335931024,81364173336],[1455113400,79805622896,79838400960,81335931024,81364173336],[1455113700,77968781504,77998552368,82356853856,82350768424],[1455114000,80789290688,80780431872,82356853856,82350768424],[1455114300,78891791152,78908765656,83495463048,83599504776],[1455114600,80750403200,80847851176,83495463048,83599504776],[1455114900,77133582648,77182791104,87073543992,86994800920],[1455115200,83812114536,83765932608,87073543992,86994800920],[1455115500,78130028008,78175675200,82025585416,82125657448],[1455115800,80582008432,80640741760,82025585416,82125657448],[1455116100,78723038456,78712904960,82323103696,82374502408],[1455116400,80915247808,80954611280,82323103696,82374502408],[1455116700,79081355400,79099183528,82536714696,82568507496],[1455117000,80890245464,80920423824,82536714696,82568507496],[1455117300,78229801792,78264272512,81053190240,81108776096],[1455117600,79262752328,79314149032,81053190240,81108776096],[1455117900,77170627552,77200738928,82119909864,82118749480],[1455118200,79620934360,79659513384,82119909864,82118749480],[1455118500,76333104472,76412302576,84889202888,84788457224],[1455118800,83180456120,83113847592,84889202888,84788457224],[1455119100,79886158344,79913412384,82519833344,82571608192],[1455119400,81013695584,81056940096,82519833344,82571608192],[1455119700,78549004376,78566810808,81969607424,81974675640],[1455120000,80803765232,80819016888,81969607424,81974675640],[1455120300,79665470000,79707782872,84419470744,84468979752],[1455120600,82942155536,82981647736,84419470744,84468979752],[1455120900,81034541680,81053144352,85654204776,85681876448],[1455121200,83680484504,83714219712,85654204776,85681876448],[1455121500,80689081008,80717627544,85410832464,85384649288],[1455121800,82842068856,82876593504,85410832464,85384649288],[1455122100,79178299704,79289577640,87943585232,87810721880],[1455122400,85278283208,85186032192,87943585232,87810721880],[1455122700,80447695280,80463628800,84201653048,84253876816],[1455123000,82760741400,82802889160,84201653048,84253876816],[1455123300,81115544040,81138350624,85837101016,85871373744],[1455123600,84116769824,84139634192,85837101016,85871373744],[1455123900,81770614672,81768628864,86109883816,86111551920],[1455124200,84273554880,84279688464,86109883816,86111551920],[1455124500,81632513912,81668345928,85716888856,85805720400],[1455124800,83926923008,83990908712,85716888856,85805720400],[1455125100,80809080448,80810788432,83449480224,83437326056],[1455125400,81238539888,81282194096,83449480224,83437326056],[1455125700,78550423048,78644821552,87540461712,87343572168],[1455126000,84560414464,84430959304,87540461712,87343572168],[1455126300,78759452976,78778840680,81533164912,81536511280],[1455126600,79582344792,79587321776,81533164912,81536511280],[1455126900,76790440888,76810327456,80911821568,80961696784],[1455127200,80194675288,80234253032,80911821568,80961696784],[1455127500,80618772120,80636489328,86056731656,86078225856],[1455127800,84259428168,84258630536,86056731656,86078225856],[1455128100,81980377832,81942900072,86556780992,86553697232],[1455128400,84331212352,84332111048,86556780992,86553697232],[1455128700,80977995704,80980135824,85029853632,85012091744],[1455129000,82748418832,82757649576,85029853632,85012091744],[1455129300,80124464176,80143058200,89544955680,89368861528],[1455129600,85426733872,85290920992,89544955680,89368861528],[1455129900,79196813376,79159346000,90119798112,90108123240],[1455130200,86717149680,86694229208,90119798112,90108123240],[1455130500,80875158512,80848832032,85478801824,85509964936],[1455130800,84185812848,84205608616,85478801824,85509964936],[1455131100,82951653264,82939516328,87442240168,87413533528],[1455131400,85785736096,85762501224,87442240168,87413533528],[1455131700,83653186032,83650246400,87940314440,87958441760],[1455132000,85965467704,85960110008,87940314440,87958441760],[1455132300,83315561208,83274799400,88306713224,88314605664],[1455132600,86005467664,86026848784,88306713224,88314605664],[1455132900,83117605208,82563186208,92004039896,89122225576],[1455133200,88870003264,86830663040,92004039896,89122225576],[1455133500,82708332144,82669665392,86137873944,86108257144],[1455133800,85083989512,85051626160,86137873944,86108257144],[1455134100,84210923120,84175111864,88680961792,88653575352],[1455134400,87397411160,87364186696,88680961792,88653575352],[1455134700,86305907384,86251328992,91540388568,91464547512],[1455135000,89677040128,89611538504,91540388568,91464547512],[1455135300,86763043848,86722354736,91042535184,91002699760],[1455135600,89593090336,89545662512,91042535184,91002699760],[1455135900,87755264152,87695615968,92304835688,92261637136],[1455136200,89855199096,89846169736,92304835688,92261637136],[1455136500,86514226264,86538488152,96922140704,96749073328],[1455136800,94623288880,94479862440,96922140704,96749073328],[1455137100,90716468696,90655239616,94889004616,94860017272],[1455137400,92831623472,92793175016,94889004616,94860017272],[1455137700,89872594776,89807575424,94647782624,94569673152],[1455138000,93473063432,93393668728,94647782624,94569673152],[1455138300,93054581672,92972099504,99103862144,99020888624],[1455138600,96929200968,96859353256,99103862144,99020888624],[1455138900,93464953936,93416433912,97391754336,97320663640],[1455139200,95502482912,95455103008,97391754336,97320663640],[1455139500,92940134000,92931522808,97826188192,97774435192],[1455139800,95081210896,95052904160,97826188192,97774435192],[1455140100,91654960992,91649110104,103089314424,102959657112],[1455140400,99785748424,99668938896,103089314424,102959657112],[1455140700,93849620504,93755274552,97924635744,97812787104],[1455141000,95736573656,95660115232,97924635744,97812787104],[1455141300,92525968664,92502126856,96938069880,96844693664],[1455141600,95094881616,95009749040,96938069880,96844693664],[1455141900,92499318736,92431743464,96677595448,96607147624],[1455142200,94702145424,94632608352,96677595448,96607147624],[1455142500,91839418200,91775341312,95918257184,95863307072],[1455142800,93865936640,93806444472,95918257184,95863307072],[1455143100,90638504752,90575499240,94393946008,94348900640],[1455143400,91304926992,91246608088,94393946008,94348900640],[1455143700,86652705984,86593916832,95187452904,95240270760],[1455144000,91546337064,91570422112,95187452904,95240270760],[1455144300,84150840304,84102287264,86412735112,86343425824],[1455144600,84515482416,84447580856,86412735112,86343425824],[1455144900,81058226192,81003118544,83724809688,83695852376],[1455145200,82321854224,82286225448,83724809688,83695852376],[1455145500,80354470112,80308018248,84155456888,84122974224],[1455145800,82107256280,82063751096,84155456888,84122974224],[1455146100,78339283192,78282702840,81489877304,81471111008],[1455146400,79236105952,79204999632,81489877304,81471111008],[1455146700,74753427016,74698713088,77448339480,77411045816],[1455147000,74849079056,74820744656,77448339480,77411045816],[1455147300,70490494520,70480457120,77557477800,77535907592],[1455147600,74201101248,74176081336,77557477800,77535907592],[1455147900,67537766792,67519014224,69720494120,69739446704],[1455148200,67619241712,67629905800,69720494120,69739446704],[1455148500,63798111216,63785411752,66609071280,66584266200],[1455148800,65247584096,65229675432,66609071280,66584266200],[1455149100,62914094456,62923836368,65415886656,65457241728],[1455149400,63805546296,63825678296,65415886656,65457241728],[1455149700,60760006688,60756186440,62684830760,62750139448],[1455150000,60614298568,60652411296,62684830760,62750139448],[1455150300,56332716352,56330274544,58087309720,58151786952],[1455150600,55748041904,55799578776,58087309720,58151786952],[1455150900,51428904016,51451077032,55801784264,55827144624],[1455151200,52872380768,52891488480,55801784264,55827144624],[1455151500,47870480240,47878298096,53508032368,53525749472],[1455151800,50993427800,51003866320,53508032368,53525749472],[1455152100,45731937120,45728995688,46877502464,46882877584],[1455152400,45849838616,45852561120,46877502464,46882877584],[1455152700,44300335984,44303425704,46633380120,46654500576],[1455153000,45392295584,45414052200,46633380120,46654500576],[1455153300,42823630200,42838909416,43585365080,43579181024],[1455153600,42717099936,42719834816,43585365080,43579181024],[1455153900,40786328664,40804836392,40957678936,40963448704],[1455154200,39659204088,39669306248,40957678936,40963448704],[1455154500,37643151456,37654335264,41534535936,41511066728],[1455154800,39630263824,39626042712,41534535936,41511066728],[1455155100,36017012536,36047354576,37624900728,37633952952],[1455155400,36754564752,36761805120,37624900728,37633952952],[1455155700,34777674056,34784116168,34876471344,34879801904],[1455156000,34348069112,34352208056,34523623912,34538067144],[1455156300,35260930528,35248338120,38543064592,38545270232],[1455156600,37760872688,37774587872,38543064592,38545270232],[1455156900,36166013432,36206344032,36467787336,36511639848],[1455157200,36034558256,36062698032,36467787336,36511639848],[1455157500,35300957632,35298608200,35894143688,35903325256],[1455157800,34439960328,34447261816,35894143688,35903325256],[1455158100,32465703480,32473644472,36937057248,36962549496],[1455158400,35783091224,35811727264,36937057248,36962549496],[1455158700,34313109952,34344588736,36797489368,36819260736],[1455159000,35580302312,35599101576,36797489368,36819260736],[1455159300,33648403696,33652304568,35405492264,35387931280],[1455159600,34344286432,34339385880,35405492264,35387931280],[1455159900,32495243768,32507659776,33629058136,33615751296],[1455160200,32867967208,32868841392,33629058136,33615751296],[1455160500,31908539016,31934678192,33760867920,33771810400],[1455160800,33764023896,33756368368,33770940184,33771810400],[1455161100,33562147096,33552727160,33770940184,33722527320],[1455161400,30971268888,31031653504,33007063048,33101307240],[1455161700,27281360128,27276981176,29926433400,29953285104],[1455162000,28890771112,28914255288,29926433400,29953285104],[1455162300,27054924120,27068429448,28098101200,28104515896],[1455162600,27236081304,27247524760,28098101200,28104515896],[1455162900,26179205440,26196305256,28314734272,28318022560],[1455163200,28259386984,28262494064,28314734272,28318022560],[1455163500,28764503960,28768203576,30407192144,30413495448],[1455163800,29880725520,29887937168,30407192144,30413495448],[1455164100,28929099736,28944192120,29504769416,29535179736],[1455164400,28763184848,28786172000,29504769416,29535179736],[1455164700,27202253088,27215945088,27440702392,27473608664],[1455165000,26419923024,26429540248,27440702392,27473608664],[1455165300,24817213544,24794207080,27203456096,27249721440],[1455165600,25869957992,25899505032,27203456096,27249721440],[1455165900,23448120568,23453207160,24778743336,24816204312],[1455166200,24250755232,24280922184,24778743336,24816204312],[1455166500,23918224064,23936303688,26073973056,26102242376],[1455166800,26187961760,26205879264,26433937400,26429516776],[1455167100,27405165400,27400626728,29861800952,29856963680],[1455167400,28735907384,28747902464,29861800952,29856963680],[1455167700,26676924680,26712083960,27629837216,27631161016],[1455168000,27131378968,27132697672,27629837216,27631161016],[1455168300,26427913944,26433549144,27384884944,27401648008],[1455168600,26634085552,26645949696,27384884944,27401648008],[1455168900,25970599640,25960492936,29429293952,29377973744],[1455169200,28652634096,28634204200,29429293952,29377973744],[1455169500,26745307416,26792179440,26976683904,27029227824],[1455169800,25697180488,25723844520,26160061024,26192586480],[1455170100,25385998048,25396102064,27125386072,27125595800],[1455170400,26849776304,26850292400,27125386072,27125595800],[1455170700,27011521496,27016456792,28924976112,28939417136],[1455171000,28324805080,28341727280,28924976112,28939417136],[1455171300,27293817920,27310859112,28064978384,28068351544],[1455171600,27429487992,27434657712,28064978384,28068351544],[1455171900,26626837728,26641167008,28113604352,28141745184],[1455172200,27449148832,27459952928,28113604352,28141745184],[1455172500,26916950448,26900785736,30176677648,30198264928],[1455172800,29250446208,29273954672,30176677648,30198264928],[1455173100,28720085952,28738987096,33533863344,33523672424],[1455173400,32326064920,32328712792,33533863344,33523672424],[1455173700,30588736592,30610808776,32927577048,32927247584],[1455173905,32927577048,32927247584,32927577048,32927247584],[1455174216,33318989448,33335985072,33318989448,33335985072]],\"expiration\":5,\"hostname\":\"barryo-mac3.local\",\"user\":{\"id\":1,\"name\":null,\"email\":\"joe@example.com\"}}','2019-05-11 14:37:48'),(57,'8da95cdb-7e2d-4d89-b647-95c8c6bd004e','8da95cdb-8432-48ac-9613-0089efec57f7',NULL,1,'gate','{\"ability\":\"viewHorizon\",\"result\":\"allowed\",\"arguments\":[],\"file\":\"\\/Users\\/barryo\\/dev\\/ixp-ibn\\/resources\\/views\\/layouts\\/menu.foil.php\",\"line\":342,\"hostname\":\"barryo-mac3.local\",\"user\":{\"id\":1,\"name\":null,\"email\":\"joe@example.com\"}}','2019-05-11 14:37:48'),(58,'8da95cdb-7eb5-411b-94c8-d0b557596c6b','8da95cdb-8432-48ac-9613-0089efec57f7',NULL,1,'gate','{\"ability\":\"viewTelescope\",\"result\":\"allowed\",\"arguments\":[],\"file\":\"\\/Users\\/barryo\\/dev\\/ixp-ibn\\/resources\\/views\\/layouts\\/menu.foil.php\",\"line\":355,\"hostname\":\"barryo-mac3.local\",\"user\":{\"id\":1,\"name\":null,\"email\":\"joe@example.com\"}}','2019-05-11 14:37:48'),(59,'8da95cdb-8193-42a1-8ce9-e778da6f2111','8da95cdb-8432-48ac-9613-0089efec57f7',NULL,1,'request','{\"uri\":\"\\/admin\",\"method\":\"GET\",\"controller_action\":\"IXP\\\\Http\\\\Controllers\\\\AdminController@dashboard\",\"middleware\":[\"web\",\"auth\",\"assert.privilege:3\"],\"headers\":{\"upgrade-insecure-requests\":\"1\",\"cookie\":\"XSRF-TOKEN=eyJpdiI6IjY0SkJ1b3RHRFgyN2NMcGhlSHRkMkE9PSIsInZhbHVlIjoia3dHU3RcL3dib00wQ2dMaThQUVlqK01jMUh1SlpJMUhJN21sXC9IRUxpMUxUXC9hVjdxUTR6THlwKzJuUjQ0bzJiZSIsIm1hYyI6IjViZTM5ZmNiMWUxZDI5Yjk0NjI2YTA4YmEwNjU3NGU1MTgxNjdkNTQ3YTIyNzUwNWQ0YmVhNjI0NmZiZTBhMWQifQ%3D%3D; IXP_Manager=eyJpdiI6IndKb1UyOFwvOWpRMmdkTUUrZk5TQUtBPT0iLCJ2YWx1ZSI6ImtCNFppcW81d3F3V0VFSFlKa0lsQ29QcUY1bzk1YVlRa09yTXBQeDZVTkQ4M3pxaGVlQnlpRmZndG9iXC9WdGpZIiwibWFjIjoiZGNhMWY1MzE0MzJmZjM2MTZhZWU3YWVmZWYyZWVlZWMxZDdhMTYyYzE3MDIyN2I2YjRmMTAyYzdlODNkN2UyMCJ9\",\"connection\":\"keep-alive\",\"dnt\":\"1\",\"referer\":\"http:\\/\\/ixp-ibn.ldev\\/login\",\"accept-encoding\":\"gzip, deflate\",\"accept-language\":\"en-IE,en-GB;q=0.7,en;q=0.3\",\"accept\":\"text\\/html,application\\/xhtml+xml,application\\/xml;q=0.9,*\\/*;q=0.8\",\"user-agent\":\"Mozilla\\/5.0 (Macintosh; Intel Mac OS X 10.14; rv:66.0) Gecko\\/20100101 Firefox\\/66.0\",\"host\":\"ixp-ibn.ldev\",\"content-length\":\"\",\"content-type\":\"\"},\"payload\":[],\"session\":{\"_token\":\"DKeD4q0Ssmn2xBbh6e7kEq9wzMUPAECXT2sp8KoJ\",\"_previous\":{\"url\":\"http:\\/\\/ixp-ibn.ldev\\/admin\"},\"_flash\":{\"old\":[],\"new\":[]},\"url\":[],\"login_web_59ba36addc2b2f9401580f014c7f58ea4e30989d\":1},\"response_status\":200,\"response\":{\"view\":\"\\/Users\\/barryo\\/dev\\/ixp-ibn\\/resources\\/views\\/admin\\/dashboard.foil.php\",\"data\":{\"stats\":{\"types\":{\"1\":\"3\",\"3\":\"1\",\"4\":\"1\"},\"speeds\":{\"10\":2,\"100\":1,\"1000\":4,\"10000\":1},\"custsByLocation\":{\"Location 1\":8},\"byLocation\":{\"Location 1\":{\"1000\":4,\"100\":1,\"10\":2,\"10000\":1}},\"byLan\":{\"Infrastructure #1\":{\"1000\":3,\"100\":1,\"10\":1},\"Infrastructure #2\":{\"1000\":1,\"10\":1,\"10000\":1}},\"byIxp\":{\"VAGRANT IXP\":{\"1000\":1,\"100\":1,\"10\":1,\"10000\":1}},\"rsUsage\":[{\"vlanname\":\"Peering LAN 1\",\"overall_count\":4,\"rsclient_count\":\"4\"},{\"vlanname\":\"Peering LAN 2\",\"overall_count\":3,\"rsclient_count\":\"3\"}],\"ipv6Usage\":[{\"vlanname\":\"Peering LAN 1\",\"overall_count\":4,\"ipv6_count\":\"2\"},{\"vlanname\":\"Peering LAN 2\",\"overall_count\":3,\"ipv6_count\":\"2\"}],\"cached_at\":\"2019-05-11T14:37:48.621150Z\"},\"graphs\":{\"ixp\":[],\"1\":[],\"2\":[]},\"graph_period\":\"week\",\"graph_periods\":{\"day\":\"Day\",\"week\":\"Week\",\"month\":\"Month\",\"year\":\"Year\"},\"customers\":{\"4\":\"AS112\",\"2\":\"HEAnet\",\"5\":\"Imagine\",\"3\":\"PCH DNS\",\"1\":\"VAGRANT IXP\"}}},\"duration\":376,\"hostname\":\"barryo-mac3.local\",\"user\":{\"id\":1,\"name\":null,\"email\":\"joe@example.com\"}}','2019-05-11 14:37:48'),(60,'8da95ce1-2eec-4324-946e-735163bd51b8','8da95ce1-2fd3-4b02-a7bc-e0a63705b5a4',NULL,1,'request','{\"uri\":\"\\/logout\",\"method\":\"GET\",\"controller_action\":\"IXP\\\\Http\\\\Controllers\\\\Auth\\\\LoginController@logout\",\"middleware\":[\"web\"],\"headers\":{\"upgrade-insecure-requests\":\"1\",\"cookie\":\"XSRF-TOKEN=eyJpdiI6IkhXT1hGajFhYkQrRDYrSTE0UXNrMmc9PSIsInZhbHVlIjoiN25NRUV2VzM0OEpDMWgwSmlWR0VsNjRKMkVzbFduQlVsWVlaV25vcGcxMjdvdkdrMGxIZEtYVko0djVHODZPaiIsIm1hYyI6ImE3MzcwODZkODVkZGZkYmIzYzhhZTViNzFiNWQxMWVmMTg5MjZmYjRmZTA4ZWViZGY1NTk4ZWU5ZGRlNTY5OGYifQ%3D%3D; IXP_Manager=eyJpdiI6IjNPMHFZb1R1Z1FYQWpHekZEMDlWNUE9PSIsInZhbHVlIjoiNzkxZlA4Zmh0SmplVU00UjI0WUtTQm8zSGF0SXJKK0JhM3VvXC9UTUFlUVJVaElsNFZIMGxWOGdwRGhZSE1EZHkiLCJtYWMiOiIxNGNkNmI5OWY1OWM0YjE0YTFiZGYzZDgzN2RlYmRkZjc1OTFjNDY3NWU4MGMyMjU0YTkyNmU5YWRjYjczOWU5In0%3D\",\"connection\":\"keep-alive\",\"dnt\":\"1\",\"referer\":\"http:\\/\\/ixp-ibn.ldev\\/admin\",\"accept-encoding\":\"gzip, deflate\",\"accept-language\":\"en-IE,en-GB;q=0.7,en;q=0.3\",\"accept\":\"text\\/html,application\\/xhtml+xml,application\\/xml;q=0.9,*\\/*;q=0.8\",\"user-agent\":\"Mozilla\\/5.0 (Macintosh; Intel Mac OS X 10.14; rv:66.0) Gecko\\/20100101 Firefox\\/66.0\",\"host\":\"ixp-ibn.ldev\",\"content-length\":\"\",\"content-type\":\"\"},\"payload\":[],\"session\":{\"ixp\":{\"utils\":{\"view\":{\"alerts\":[{}]}}},\"_flash\":{\"old\":[],\"new\":[]}},\"response_status\":302,\"response\":\"Redirected to http:\\/\\/ixp-ibn.ldev\",\"duration\":216,\"hostname\":\"barryo-mac3.local\"}','2019-05-11 14:37:52'),(61,'8da95ce1-9db3-41a8-be38-d497fecf6087','8da95ce1-9ed4-468f-bce1-66137866646a',NULL,1,'request','{\"uri\":\"\\/\",\"method\":\"GET\",\"controller_action\":\"Closure\",\"middleware\":[\"web\"],\"headers\":{\"upgrade-insecure-requests\":\"1\",\"cookie\":\"XSRF-TOKEN=eyJpdiI6InFseGtVNENFcTliK0JxYzUweUpcL0x3PT0iLCJ2YWx1ZSI6InRlM3JaTzJvXC9tZ1hmTUtGaHNaTmF3PT0iLCJtYWMiOiJkN2JiNGMxYmI4NDBhMDQ5NWE4Yzg3ZjQwOWIxMDQyYmE0YjBjMWVmYzliMDEwODA1MTk2NjBmMzg3ZWQ5OTIxIn0%3D; IXP_Manager=eyJpdiI6IkRwYlRqaHRPaHA5VDBhdDdJZHFPV1E9PSIsInZhbHVlIjoic3ppVVFHSUdLZmw2R3FOOU12WlNyVCtUbjFpUExpWjdZZVBrZm5xMWVJbVJwbHQ2RWRKdWpNRkFnTzRScUNFNCIsIm1hYyI6ImM2MTVkODE5OWJhNTAwYTEwODgxMDEwNWY5NDIyMGQ3OTMxYzk0MGQwZGQ0MDNlNTAxMjA3MDY5ZWY5YzkwMGIifQ%3D%3D\",\"connection\":\"keep-alive\",\"dnt\":\"1\",\"referer\":\"http:\\/\\/ixp-ibn.ldev\\/admin\",\"accept-encoding\":\"gzip, deflate\",\"accept-language\":\"en-IE,en-GB;q=0.7,en;q=0.3\",\"accept\":\"text\\/html,application\\/xhtml+xml,application\\/xml;q=0.9,*\\/*;q=0.8\",\"user-agent\":\"Mozilla\\/5.0 (Macintosh; Intel Mac OS X 10.14; rv:66.0) Gecko\\/20100101 Firefox\\/66.0\",\"host\":\"ixp-ibn.ldev\",\"content-length\":\"\",\"content-type\":\"\"},\"payload\":[],\"session\":{\"ixp\":{\"utils\":{\"view\":{\"alerts\":[{}]}}},\"_flash\":{\"old\":[],\"new\":[]},\"_token\":\"WKKKCk5iiWTjLMNvET7WiIWC4tcoazit6yV9zPkQ\",\"_previous\":{\"url\":\"http:\\/\\/ixp-ibn.ldev\"}},\"response_status\":302,\"response\":\"Redirected to http:\\/\\/ixp-ibn.ldev\\/login\",\"duration\":233,\"hostname\":\"barryo-mac3.local\"}','2019-05-11 14:37:52'),(62,'8da95ce1-e637-4b7b-8213-ee96f1a0f04f','8da95ce1-e764-4e0b-8bdc-97466f8917de',NULL,1,'request','{\"uri\":\"\\/login\",\"method\":\"GET\",\"controller_action\":\"IXP\\\\Http\\\\Controllers\\\\Auth\\\\LoginController@showLoginForm\",\"middleware\":[\"web\",\"guest\"],\"headers\":{\"upgrade-insecure-requests\":\"1\",\"cookie\":\"XSRF-TOKEN=eyJpdiI6IktKYnVHelpoNlhxZ0E5YW85ZENVQVE9PSIsInZhbHVlIjoibFVTanlOYWRWdzdHYU1KNWN4aEZ4VWc4d3dka2NqSjNTMGRDQU01MlB1a3hNUHZBMkJSR0dzN0VVR2MzdlBVWCIsIm1hYyI6IjdjNzQyZGQyY2YzYTJhY2M4MjhjOWI5ZmRkNGJkZWVkYzNiYzFkYTAxODUwYzlmM2VjN2M5MDhhNzBiNDU1NDIifQ%3D%3D; IXP_Manager=eyJpdiI6IndrYXJTZjVyN0ZPcWdNNk1hbnBad0E9PSIsInZhbHVlIjoiQ2VXMkpVMndKeWR6TStZS0huaVdod21mOCtPQ0pYdGF4Qnl1N1pXZXM1Y0c1TDEwXC9NbDFXWDE3RTdzdms1M2EiLCJtYWMiOiI2ZWY4NjA0NjgwNjRmZWEwZDYzNjA3OGNkZjkxOTMwMjgzYmIwNTQ2ZGU3MWRhZTUxY2JiY2E2YTg2ZDM4ZjkyIn0%3D\",\"connection\":\"keep-alive\",\"dnt\":\"1\",\"referer\":\"http:\\/\\/ixp-ibn.ldev\\/admin\",\"accept-encoding\":\"gzip, deflate\",\"accept-language\":\"en-IE,en-GB;q=0.7,en;q=0.3\",\"accept\":\"text\\/html,application\\/xhtml+xml,application\\/xml;q=0.9,*\\/*;q=0.8\",\"user-agent\":\"Mozilla\\/5.0 (Macintosh; Intel Mac OS X 10.14; rv:66.0) Gecko\\/20100101 Firefox\\/66.0\",\"host\":\"ixp-ibn.ldev\",\"content-length\":\"\",\"content-type\":\"\"},\"payload\":[],\"session\":{\"ixp\":{\"utils\":{\"view\":{\"alerts\":[]}}},\"_flash\":{\"old\":[],\"new\":[]},\"_token\":\"WKKKCk5iiWTjLMNvET7WiIWC4tcoazit6yV9zPkQ\",\"_previous\":{\"url\":\"http:\\/\\/ixp-ibn.ldev\\/login\"},\"url\":{\"intended\":\"http:\\/\\/ixp-ibn.ldev\\/admin\"}},\"response_status\":200,\"response\":{\"view\":\"\\/Users\\/barryo\\/dev\\/ixp-ibn\\/resources\\/views\\/auth\\/login.foil.php\",\"data\":[]},\"duration\":176,\"hostname\":\"barryo-mac3.local\"}','2019-05-11 14:37:52');
/*!40000 ALTER TABLE `telescope_entries` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `telescope_entries_tags`
--

DROP TABLE IF EXISTS `telescope_entries_tags`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `telescope_entries_tags` (
  `entry_uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tag` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
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
INSERT INTO `telescope_entries_tags` VALUES ('8da95cda-4a7d-4c5e-8062-e8e8f6ef2fc6','Auth:1'),('8da95cda-9a9e-4e33-ac8c-93293a1408d1','Auth:1'),('8da95cda-eb0a-4e26-a3ae-f0e9faed8ea0','Auth:1'),('8da95cdb-324e-4d33-8829-71e48083ee84','Auth:1'),('8da95cdb-3f68-4114-aa58-5fdccf8e544c','Auth:1'),('8da95cdb-3ff4-4678-b1fc-56c67c36dcea','Auth:1'),('8da95cdb-406b-4616-8d2f-6829ebf8347f','Auth:1'),('8da95cdb-412f-433a-9660-7912dace02c4','Auth:1'),('8da95cdb-5461-4dfa-85d2-12c698a2b6de','Auth:1'),('8da95cdb-55db-4f8d-8e59-5aba480ec518','Auth:1'),('8da95cdb-5764-4b10-80df-c87b152f8919','Auth:1'),('8da95cdb-5a60-4e71-bf02-109c50c0397c','Auth:1'),('8da95cdb-5b5b-4a7c-9781-5619c9e9d1dc','Auth:1'),('8da95cdb-5cb6-45ad-a7aa-0d7ef2c8cb3f','Auth:1'),('8da95cdb-5e34-4529-8aee-e560c34a77a4','Auth:1'),('8da95cdb-6460-4ee4-9f20-81005e52b3d3','Auth:1'),('8da95cdb-65b4-44ff-ab5c-1260b67be953','Auth:1'),('8da95cdb-668a-407a-9c87-c18979b81fd2','Auth:1'),('8da95cdb-672c-4dbf-afe5-0f2d036a2c62','Auth:1'),('8da95cdb-6868-4c22-bdf8-58f0e59e3d7c','Auth:1'),('8da95cdb-6d83-416d-a917-88ecabe0c5a0','Auth:1'),('8da95cdb-6ed0-4870-8820-f72eda355784','Auth:1'),('8da95cdb-6fa5-46f6-afba-b14a5d76c611','Auth:1'),('8da95cdb-7049-4f54-8246-230162ea098f','Auth:1'),('8da95cdb-7192-4c34-9f70-a0a3441e2d00','Auth:1'),('8da95cdb-766a-4fb4-94d4-0bc0f3d4f424','Auth:1'),('8da95cdb-7e2d-4d89-b647-95c8c6bd004e','Auth:1'),('8da95cdb-7eb5-411b-94c8-d0b557596c6b','Auth:1'),('8da95cdb-8193-42a1-8ce9-e778da6f2111','Auth:1');
/*!40000 ALTER TABLE `telescope_entries_tags` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `telescope_monitoring`
--

DROP TABLE IF EXISTS `telescope_monitoring`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `telescope_monitoring` (
  `tag` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL
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
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `traffic_95th` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `cust_id` int(11) DEFAULT NULL,
  `datetime` datetime DEFAULT NULL,
  `average` bigint(20) DEFAULT NULL,
  `max` bigint(20) DEFAULT NULL,
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
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `traffic_95th_monthly` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `cust_id` int(11) DEFAULT NULL,
  `month` date DEFAULT NULL,
  `max_95th` bigint(20) DEFAULT NULL,
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
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `traffic_daily` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `cust_id` int(11) NOT NULL,
  `ixp_id` int(11) NOT NULL,
  `day` date DEFAULT NULL,
  `category` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `day_avg_in` bigint(20) DEFAULT NULL,
  `day_avg_out` bigint(20) DEFAULT NULL,
  `day_max_in` bigint(20) DEFAULT NULL,
  `day_max_out` bigint(20) DEFAULT NULL,
  `day_tot_in` bigint(20) DEFAULT NULL,
  `day_tot_out` bigint(20) DEFAULT NULL,
  `week_avg_in` bigint(20) DEFAULT NULL,
  `week_avg_out` bigint(20) DEFAULT NULL,
  `week_max_in` bigint(20) DEFAULT NULL,
  `week_max_out` bigint(20) DEFAULT NULL,
  `week_tot_in` bigint(20) DEFAULT NULL,
  `week_tot_out` bigint(20) DEFAULT NULL,
  `month_avg_in` bigint(20) DEFAULT NULL,
  `month_avg_out` bigint(20) DEFAULT NULL,
  `month_max_in` bigint(20) DEFAULT NULL,
  `month_max_out` bigint(20) DEFAULT NULL,
  `month_tot_in` bigint(20) DEFAULT NULL,
  `month_tot_out` bigint(20) DEFAULT NULL,
  `year_avg_in` bigint(20) DEFAULT NULL,
  `year_avg_out` bigint(20) DEFAULT NULL,
  `year_max_in` bigint(20) DEFAULT NULL,
  `year_max_out` bigint(20) DEFAULT NULL,
  `year_tot_in` bigint(20) DEFAULT NULL,
  `year_tot_out` bigint(20) DEFAULT NULL,
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
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `custid` int(11) DEFAULT NULL,
  `username` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `password` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `authorisedMobile` varchar(30) COLLATE utf8_unicode_ci DEFAULT NULL,
  `uid` int(11) DEFAULT NULL,
  `privs` int(11) DEFAULT NULL,
  `disabled` tinyint(1) DEFAULT NULL,
  `lastupdated` datetime DEFAULT NULL,
  `lastupdatedby` int(11) DEFAULT NULL,
  `creator` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `remember_token` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_8D93D649F85E0677` (`username`),
  KEY `IDX_8D93D649DA0209B9` (`custid`),
  CONSTRAINT `FK_8D93D649DA0209B9` FOREIGN KEY (`custid`) REFERENCES `cust` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user`
--

LOCK TABLES `user` WRITE;
/*!40000 ALTER TABLE `user` DISABLE KEYS */;
INSERT INTO `user` VALUES (1,1,'vagrant','$2a$09$kI2ORSzVnuekb9XERfL1we2tENnDJXsR.oxlWM5ELHX9G3aoCdvne','joe@example.com',NULL,NULL,3,0,'2015-08-20 15:19:12',1,'travis','2014-01-06 13:54:22','Vagrant',NULL),(2,4,'as112','$2a$09$bYMQzLJs6VdISr3OlwqGAe7LVe0K6xALQUkThuhQ27hwB4EJ.g/1a','none@example.com',NULL,NULL,2,0,'2015-08-20 15:24:41',1,'vagrant','2015-08-20 15:24:41','Customer AS112',NULL),(3,4,'as112user','$2a$09$O1rXly8ResuQdbkZGQx6perb2FH72PvFsoVvjVvY5bd6DlyVNKwna','none@example.com',NULL,NULL,1,0,'2015-08-20 15:25:30',1,'vagrant','2015-08-20 15:25:20','AS112 User',NULL);
/*!40000 ALTER TABLE `user` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_logins`
--

DROP TABLE IF EXISTS `user_logins`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_logins` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `ip` varchar(39) COLLATE utf8_unicode_ci NOT NULL,
  `at` datetime NOT NULL,
  `customer_to_user_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_6341CC99A76ED395` (`user_id`),
  KEY `at_idx` (`at`,`user_id`),
  KEY `IDX_6341CC99D43FEAE2` (`customer_to_user_id`),
  CONSTRAINT `FK_6341CC99A76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_6341CC99D43FEAE2` FOREIGN KEY (`customer_to_user_id`) REFERENCES `customer_to_users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_logins`
--

LOCK TABLES `user_logins` WRITE;
/*!40000 ALTER TABLE `user_logins` DISABLE KEYS */;
INSERT INTO `user_logins` VALUES (1,1,'10.37.129.2','2014-01-06 13:54:52',1),(2,1,'10.37.129.2','2014-01-13 10:38:11',1),(3,1,'10.0.2.2','2015-08-20 14:44:45',1),(4,1,'10.0.2.2','2017-11-09 12:14:12',1),(5,1,'10.0.2.2','2019-01-21 16:10:47',1),(6,1,'127.0.0.1','2019-05-11 14:37:47',1);
/*!40000 ALTER TABLE `user_logins` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_pref`
--

DROP TABLE IF EXISTS `user_pref`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_pref` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `attribute` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `ix` int(11) NOT NULL DEFAULT '0',
  `op` varchar(2) COLLATE utf8_unicode_ci DEFAULT NULL,
  `value` longtext COLLATE utf8_unicode_ci,
  `expire` bigint(20) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `IX_UserPreference_1` (`user_id`,`attribute`,`op`,`ix`),
  KEY `IDX_DBD4D4F8A76ED395` (`user_id`),
  CONSTRAINT `FK_DBD4D4F8A76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_pref`
--

LOCK TABLES `user_pref` WRITE;
/*!40000 ALTER TABLE `user_pref` DISABLE KEYS */;
INSERT INTO `user_pref` VALUES (1,1,'auth.last_login_from',0,'=','10.0.2.2',0),(2,1,'auth.last_login_at',0,'=','1510229652',0),(4,3,'customer-notes.read_upto',0,'=','1440084320',0);
/*!40000 ALTER TABLE `user_pref` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vendor`
--

DROP TABLE IF EXISTS `vendor`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vendor` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `shortname` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `nagios_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `bundle_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
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
-- Table structure for table `virtualinterface`
--

DROP TABLE IF EXISTS `virtualinterface`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `virtualinterface` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `custid` int(11) DEFAULT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `mtu` int(11) DEFAULT NULL,
  `trunk` tinyint(1) DEFAULT NULL,
  `channelgroup` int(11) DEFAULT NULL,
  `lag_framing` tinyint(1) NOT NULL DEFAULT '0',
  `fastlacp` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `IDX_11D9014FDA0209B9` (`custid`),
  CONSTRAINT `FK_11D9014FDA0209B9` FOREIGN KEY (`custid`) REFERENCES `cust` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `virtualinterface`
--

LOCK TABLES `virtualinterface` WRITE;
/*!40000 ALTER TABLE `virtualinterface` DISABLE KEYS */;
INSERT INTO `virtualinterface` VALUES (1,2,'','',0,0,0,0,0),(2,2,'','',0,0,0,0,0),(3,3,'','',0,0,0,0,0),(4,4,'','',0,0,0,0,0),(5,4,'','',0,0,0,0,0),(6,5,'','',0,0,0,0,0),(7,5,'','',0,0,0,0,0);
/*!40000 ALTER TABLE `virtualinterface` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vlan`
--

DROP TABLE IF EXISTS `vlan`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vlan` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `infrastructureid` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `number` int(11) DEFAULT NULL,
  `private` tinyint(1) NOT NULL DEFAULT '0',
  `notes` longtext COLLATE utf8_unicode_ci,
  `peering_matrix` tinyint(1) NOT NULL DEFAULT '0',
  `peering_manager` tinyint(1) NOT NULL DEFAULT '0',
  `config_name` varchar(32) COLLATE utf8_unicode_ci DEFAULT NULL,
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
INSERT INTO `vlan` VALUES (1,1,'Peering LAN 1',1,0,'',0,0,NULL),(2,2,'Peering LAN 2',2,0,'',0,0,NULL);
/*!40000 ALTER TABLE `vlan` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vlaninterface`
--

DROP TABLE IF EXISTS `vlaninterface`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vlaninterface` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ipv4addressid` int(11) DEFAULT NULL,
  `ipv6addressid` int(11) DEFAULT NULL,
  `virtualinterfaceid` int(11) DEFAULT NULL,
  `vlanid` int(11) DEFAULT NULL,
  `ipv4enabled` tinyint(1) DEFAULT '0',
  `ipv4hostname` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `ipv6enabled` tinyint(1) DEFAULT '0',
  `ipv6hostname` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `mcastenabled` tinyint(1) DEFAULT '0',
  `irrdbfilter` tinyint(1) DEFAULT '1',
  `bgpmd5secret` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `ipv4bgpmd5secret` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `ipv6bgpmd5secret` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `maxbgpprefix` int(11) DEFAULT NULL,
  `rsclient` tinyint(1) DEFAULT NULL,
  `ipv4canping` tinyint(1) DEFAULT NULL,
  `ipv6canping` tinyint(1) DEFAULT NULL,
  `ipv4monitorrcbgp` tinyint(1) DEFAULT NULL,
  `ipv6monitorrcbgp` tinyint(1) DEFAULT NULL,
  `as112client` tinyint(1) DEFAULT NULL,
  `busyhost` tinyint(1) DEFAULT NULL,
  `notes` longtext COLLATE utf8_unicode_ci,
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
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vlaninterface`
--

LOCK TABLES `vlaninterface` WRITE;
/*!40000 ALTER TABLE `vlaninterface` DISABLE KEYS */;
INSERT INTO `vlaninterface` VALUES (1,10,16,1,1,1,'a.heanet.ie',1,'a.heanet.ie',0,1,NULL,'N7rX2SdfbRsyBLTm','N7rX2SdfbRsyBLTm',1000,1,1,1,1,1,1,0,NULL,0),(2,137,417,2,2,1,'b.heanet.ie',1,'b.heanet.ie',0,1,NULL,'u5zSNJLAVT87RGXQ','u5zSNJLAVT87RGXQ',1000,1,1,1,1,1,0,0,NULL,0),(3,36,NULL,3,1,1,'a.pch.ie',0,'',0,1,NULL,'mcWsqMdzGwTKt67g','mcWsqMdzGwTKt67g',2000,1,1,0,1,0,1,0,NULL,0),(4,6,NULL,4,1,1,'a.as112.net',0,'',0,1,NULL,'w83fmGpRDtaKomQo','w83fmGpRDtaKomQo',20,1,1,0,1,0,0,0,NULL,0),(5,132,NULL,5,2,1,'b.as112.net',0,'',0,1,NULL,'Pz8VYMNwEdCjKz68','Pz8VYMNwEdCjKz68',20,1,1,0,1,0,0,0,NULL,0),(6,NULL,8,6,1,0,'',1,'a.imagine.ie',0,1,NULL,'X8Ks9QnbER9cyzU3','X8Ks9QnbER9cyzU3',1000,1,0,1,0,1,0,0,NULL,0),(7,172,470,7,2,1,'b.imagine.ie',1,'b.imagine.ie',0,1,NULL,'LyJND4eoKuQz5j49','LyJND4eoKuQz5j49',1000,1,1,1,1,1,0,0,NULL,0);
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

-- Dump completed on 2019-05-11 15:38:04
