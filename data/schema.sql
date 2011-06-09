/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bgpsessiondata` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `srcipv4addressid` int(10) unsigned DEFAULT NULL,
  `dstipv4addressid` int(10) unsigned DEFAULT NULL,
  `packetcount` int(10) unsigned DEFAULT NULL,
  `timestamp` datetime DEFAULT NULL,
  `source` varchar(40) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5606491 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cabinet` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `locationid` int(10) unsigned NOT NULL DEFAULT '0',
  `name` varchar(255) NOT NULL DEFAULT '',
  `cololocation` varchar(255) NOT NULL DEFAULT '',
  `height` int(11) NOT NULL DEFAULT '0',
  `type` varchar(255) NOT NULL DEFAULT '',
  `notes` mediumtext NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `change_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `details` mediumblob NOT NULL,
  `visibility` int(11) NOT NULL,
  `livedate` date NOT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `consoleserverconnection` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `description` varchar(255) DEFAULT NULL,
  `custid` int(10) unsigned NOT NULL,
  `port` varchar(255) DEFAULT NULL,
  `switchid` int(10) unsigned NOT NULL,
  `speed` int(10) unsigned DEFAULT NULL,
  `parity` int(10) unsigned DEFAULT NULL,
  `stopbits` int(10) unsigned DEFAULT NULL,
  `flowcontrol` int(10) unsigned DEFAULT NULL,
  `autobaud` tinyint(4) DEFAULT NULL,
  `notes` mediumtext,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=39 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `consoleserverconnection_seq` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `contact` (
  `custid` int(10) unsigned NOT NULL DEFAULT '0',
  `name` varchar(64) NOT NULL DEFAULT '',
  `email` varchar(64) DEFAULT NULL,
  `phone` varchar(32) DEFAULT NULL,
  `mobile` varchar(32) DEFAULT NULL,
  `facilityaccess` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `mayauthorize` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `lastupdated` datetime DEFAULT NULL,
  `lastupdatedby` int(11) DEFAULT NULL,
  `creator` varchar(32) DEFAULT 'Operations',
  `created` datetime DEFAULT NULL,
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=421 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `contact_seq` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cust` (
  `name` varchar(255) NOT NULL DEFAULT '',
  `type` int(10) unsigned DEFAULT NULL,
  `shortname` varchar(255) NOT NULL DEFAULT '',
  `autsys` int(10) unsigned NOT NULL DEFAULT '0',
  `maxprefixes` int(10) unsigned DEFAULT NULL,
  `peeringemail` varchar(64) NOT NULL DEFAULT '',
  `nocphone` varchar(255) DEFAULT NULL,
  `noc24hphone` varchar(255) DEFAULT NULL,
  `nocfax` varchar(40) DEFAULT NULL,
  `nocemail` varchar(40) DEFAULT NULL,
  `nochours` varchar(40) NOT NULL DEFAULT '24x7',
  `nocwww` varchar(255) DEFAULT NULL,
  `irrdb` int(10) unsigned DEFAULT NULL,
  `peeringmacro` varchar(255) NOT NULL DEFAULT '',
  `peeringpolicy` varchar(255) DEFAULT NULL,
  `billingContact` varchar(64) DEFAULT NULL,
  `billingAddress1` varchar(64) DEFAULT NULL,
  `billingAddress2` varchar(64) DEFAULT NULL,
  `billingCity` varchar(64) DEFAULT NULL,
  `billingCountry` char(2) DEFAULT NULL,
  `corpwww` varchar(255) DEFAULT NULL,
  `datejoin` date DEFAULT NULL,
  `dateleave` date DEFAULT NULL,
  `status` tinyint(4) DEFAULT NULL,
  `activepeeringmatrix` tinyint(4) NOT NULL DEFAULT '1',
  `notes` mediumtext,
  `lastupdated` datetime DEFAULT NULL,
  `lastupdatedby` int(11) DEFAULT NULL,
  `creator` varchar(32) DEFAULT 'Operations',
  `created` datetime DEFAULT NULL,
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=93 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cust_seq` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=75 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `custkit` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL DEFAULT '',
  `custid` int(10) unsigned NOT NULL DEFAULT '0',
  `cabinetid` int(10) unsigned NOT NULL DEFAULT '0',
  `descr` mediumtext NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `history` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `custid` int(10) unsigned NOT NULL,
  `tablename` varchar(255) NOT NULL,
  `tableid` int(10) unsigned NOT NULL,
  `tablefield` varchar(255) DEFAULT NULL,
  `oldval` text,
  `newval` text,
  `uuid` varchar(36) DEFAULT NULL,
  `username` varchar(255) DEFAULT NULL,
  `eventtime` datetime DEFAULT NULL,
  `eventtype` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=475 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ipv4address` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `address` varchar(16) NOT NULL,
  `vlanid` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `address` (`address`)
) ENGINE=InnoDB AUTO_INCREMENT=377 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ipv6address` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `address` varchar(40) NOT NULL,
  `vlanid` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `address` (`address`)
) ENGINE=InnoDB AUTO_INCREMENT=1309 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `irrdbconfig` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `host` varchar(255) DEFAULT NULL,
  `protocol` varchar(255) DEFAULT NULL,
  `source` varchar(255) DEFAULT NULL,
  `notes` mediumtext,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `location` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL DEFAULT '',
  `shortname` varchar(255) DEFAULT NULL,
  `tag` varchar(255) DEFAULT NULL,
  `address` varchar(255) NOT NULL DEFAULT '',
  `nocphone` varchar(255) NOT NULL DEFAULT '',
  `nocfax` varchar(255) NOT NULL DEFAULT '',
  `nocemail` varchar(255) NOT NULL DEFAULT '',
  `officephone` varchar(255) NOT NULL DEFAULT '',
  `officefax` varchar(255) NOT NULL DEFAULT '',
  `officeemail` varchar(255) NOT NULL DEFAULT '',
  `notes` blob,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `meeting` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `before_text` mediumtext NOT NULL,
  `after_text` mediumtext NOT NULL,
  `date` date NOT NULL,
  `time` time NOT NULL,
  `venue` varchar(255) NOT NULL,
  `venue_url` varchar(255) NOT NULL DEFAULT '',
  `created_at` datetime NOT NULL,
  `created_by` int(10) unsigned NOT NULL,
  `updated_at` datetime NOT NULL,
  `updated_by` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `created_by` (`created_by`),
  KEY `updated_by` (`updated_by`),
  CONSTRAINT `meeting_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`),
  CONSTRAINT `meeting_ibfk_2` FOREIGN KEY (`updated_by`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `meeting_item` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `meeting_id` bigint(20) unsigned NOT NULL,
  `title` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `role` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `company` varchar(255) NOT NULL,
  `company_url` varchar(255) NOT NULL,
  `summary` mediumtext NOT NULL,
  `presentation` varchar(255) NOT NULL DEFAULT '',
  `filename` varchar(255) NOT NULL,
  `video_url` varchar(255) NOT NULL,
  `other_content` tinyint(1) NOT NULL DEFAULT '0',
  `created_by` int(10) unsigned NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_by` int(10) unsigned NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `meeting_id` (`meeting_id`)
) ENGINE=InnoDB AUTO_INCREMENT=36 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `my_peering_matrix` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `custid` int(10) unsigned NOT NULL,
  `peerid` int(10) unsigned NOT NULL,
  `vlan` int(10) unsigned DEFAULT NULL,
  `peered` enum('YES','NO','WAITING','NEVER') NOT NULL,
  `ipv6` tinyint(1) DEFAULT '0',
  `notes_id` int(10) unsigned DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `updated` datetime NOT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2135 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `my_peering_matrix_notes` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `custid` int(10) unsigned NOT NULL,
  `peerid` int(10) unsigned NOT NULL,
  `notes` blob NOT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `custid` (`custid`,`peerid`)
) ENGINE=InnoDB AUTO_INCREMENT=73 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `networkinfo` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `vlanid` int(10) unsigned NOT NULL,
  `protocol` tinyint(3) unsigned NOT NULL,
  `network` varchar(255) NOT NULL,
  `masklen` tinyint(4) DEFAULT NULL,
  `rs1address` varchar(40) DEFAULT NULL,
  `rs2address` varchar(40) DEFAULT NULL,
  `dnsfile` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `patch_panel` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `cabinetid` int(10) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `colo_ref` varchar(255) NOT NULL,
  `cable_type` int(11) NOT NULL,
  `interface_type` int(11) NOT NULL,
  `allow_duplex` tinyint(1) NOT NULL,
  `notes` mediumtext NOT NULL,
  PRIMARY KEY (`id`),
  KEY `cabinetid` (`cabinetid`),
  CONSTRAINT `patch_panel_ibfk_1` FOREIGN KEY (`cabinetid`) REFERENCES `cabinet` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `patch_panel_port` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `patch_panel_id` int(10) unsigned NOT NULL,
  `port` int(10) unsigned NOT NULL,
  `side` smallint(5) unsigned NOT NULL DEFAULT '0',
  `type` smallint(5) unsigned NOT NULL DEFAULT '0',
  `colo_ref` varchar(255) NOT NULL,
  `cable_type` int(10) unsigned NOT NULL,
  `duplex` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `patch_panel_id` (`patch_panel_id`),
  CONSTRAINT `patch_panel_port_ibfk_1` FOREIGN KEY (`patch_panel_id`) REFERENCES `patch_panel` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1105 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `peering_matrix` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `vlan` int(11) NOT NULL,
  `x_as` int(10) unsigned NOT NULL,
  `x_custid` int(10) unsigned NOT NULL,
  `y_as` int(10) unsigned NOT NULL,
  `y_custid` int(10) unsigned NOT NULL,
  `peering_status` enum('YES','NO','INCONSISTENT_X','INCONSISTENT_Y') NOT NULL,
  `updated` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5991 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `physicalinterface` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `switchportid` int(10) unsigned NOT NULL,
  `virtualinterfaceid` int(10) unsigned DEFAULT NULL,
  `status` int(10) unsigned NOT NULL,
  `speed` int(10) unsigned NOT NULL,
  `duplex` varchar(16) NOT NULL,
  `monitorindex` int(10) unsigned NOT NULL,
  `notes` mediumtext,
  PRIMARY KEY (`id`),
  KEY `virtualinterfaceid` (`virtualinterfaceid`),
  KEY `virtualinterfaceid_2` (`virtualinterfaceid`)
) ENGINE=InnoDB AUTO_INCREMENT=191 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `physicalinterface_seq` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=46 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sec_event` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `custid` int(10) unsigned NOT NULL,
  `switchid` int(10) unsigned NOT NULL,
  `switchportid` int(10) unsigned NOT NULL,
  `type` enum('SECURITY_VIOLATION','PORT_UPDOWN','LINEPROTO_UPDOWN','BGP_AUTH') NOT NULL,
  `message` mediumtext NOT NULL,
  `recorded_date` varchar(255) NOT NULL DEFAULT '',
  `timestamp` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3325 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `switch` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL DEFAULT '',
  `cabinetid` int(10) unsigned NOT NULL DEFAULT '0',
  `ipv4addr` varchar(255) NOT NULL DEFAULT '',
  `ipv6addr` varchar(255) NOT NULL DEFAULT '',
  `snmppasswd` varchar(255) NOT NULL DEFAULT '',
  `infrastructure` tinyint(3) unsigned DEFAULT NULL,
  `switchtype` int(10) unsigned DEFAULT NULL,
  `vendorid` int(10) unsigned NOT NULL DEFAULT '0',
  `model` varchar(255) NOT NULL DEFAULT '',
  `active` tinyint(3) unsigned DEFAULT NULL,
  `notes` mediumtext NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `switch_seq` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `switchport` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `switchid` int(10) unsigned NOT NULL DEFAULT '0',
  `type` int(10) unsigned NOT NULL DEFAULT '0',
  `name` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=844 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `traffic_95th` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `cust_id` int(10) unsigned NOT NULL,
  `datetime` datetime NOT NULL,
  `average` bigint(20) unsigned NOT NULL,
  `max` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `cust_id` (`cust_id`,`datetime`)
) ENGINE=InnoDB AUTO_INCREMENT=6029603 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `traffic_95th_monthly` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `cust_id` int(10) unsigned NOT NULL,
  `month` date NOT NULL,
  `max_95th` bigint(20) unsigned NOT NULL COMMENT 'Bits',
  PRIMARY KEY (`id`),
  UNIQUE KEY `cust_id` (`cust_id`,`month`)
) ENGINE=InnoDB AUTO_INCREMENT=821 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `traffic_daily` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `cust_id` int(10) unsigned NOT NULL,
  `day` date NOT NULL,
  `category` enum('bits','pkts','errs','discs') NOT NULL,
  `day_avg_in` bigint(20) unsigned NOT NULL,
  `day_avg_out` bigint(20) unsigned NOT NULL,
  `day_max_in` bigint(20) unsigned NOT NULL,
  `day_max_out` bigint(20) unsigned NOT NULL,
  `day_tot_in` bigint(20) unsigned NOT NULL,
  `day_tot_out` bigint(20) unsigned NOT NULL,
  `week_avg_in` bigint(20) unsigned NOT NULL,
  `week_avg_out` bigint(20) unsigned NOT NULL,
  `week_max_in` bigint(20) unsigned NOT NULL,
  `week_max_out` bigint(20) unsigned NOT NULL,
  `week_tot_in` bigint(20) unsigned NOT NULL,
  `week_tot_out` bigint(20) unsigned NOT NULL,
  `month_avg_in` bigint(20) unsigned NOT NULL,
  `month_avg_out` bigint(20) unsigned NOT NULL,
  `month_max_in` bigint(20) unsigned NOT NULL,
  `month_max_out` bigint(20) unsigned NOT NULL,
  `month_tot_in` bigint(20) unsigned NOT NULL,
  `month_tot_out` bigint(20) unsigned NOT NULL,
  `year_avg_in` bigint(20) unsigned NOT NULL,
  `year_avg_out` bigint(20) unsigned NOT NULL,
  `year_max_in` bigint(20) unsigned NOT NULL,
  `year_max_out` bigint(20) unsigned NOT NULL,
  `year_tot_in` bigint(20) unsigned NOT NULL,
  `year_tot_out` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `cust_id` (`cust_id`,`day`,`category`)
) ENGINE=InnoDB AUTO_INCREMENT=91679 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user` (
  `username` varchar(30) NOT NULL DEFAULT '',
  `password` varchar(30) NOT NULL DEFAULT '',
  `email` varchar(255) DEFAULT NULL,
  `authorisedMobile` varchar(30) NOT NULL DEFAULT '',
  `uid` int(10) unsigned DEFAULT NULL,
  `custid` int(10) unsigned NOT NULL DEFAULT '0',
  `privs` tinyint(4) DEFAULT '0',
  `disabled` tinyint(4) NOT NULL DEFAULT '0',
  `lastupdated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `lastupdatedby` int(11) DEFAULT NULL,
  `creator` varchar(32) DEFAULT 'Manager',
  `created` datetime DEFAULT NULL,
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=169 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_pref` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `attribute` varchar(255) NOT NULL,
  `op` char(2) NOT NULL,
  `value` text NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`user_id`,`attribute`,`op`)
) ENGINE=InnoDB AUTO_INCREMENT=569 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_seq` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=68 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vendor` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `view_cust_current_active` (
  `name` varchar(255),
  `type` int(10) unsigned,
  `shortname` varchar(255),
  `autsys` int(10) unsigned,
  `maxprefixes` int(10) unsigned,
  `peeringemail` varchar(64),
  `nocphone` varchar(255),
  `noc24hphone` varchar(255),
  `nocfax` varchar(40),
  `nocemail` varchar(40),
  `nochours` varchar(40),
  `nocwww` varchar(255),
  `irrdb` int(10) unsigned,
  `peeringmacro` varchar(255),
  `peeringpolicy` varchar(255),
  `billingContact` varchar(64),
  `billingAddress1` varchar(64),
  `billingAddress2` varchar(64),
  `billingCity` varchar(64),
  `billingCountry` char(2),
  `corpwww` varchar(255),
  `datejoin` date,
  `dateleave` date,
  `status` tinyint(4),
  `activepeeringmatrix` tinyint(4),
  `notes` mediumtext,
  `lastupdated` datetime,
  `lastupdatedby` int(11),
  `creator` varchar(32),
  `created` datetime,
  `id` int(10) unsigned
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `view_switch_details_by_custid` (
  `id` int(10) unsigned,
  `custid` int(10) unsigned,
  `virtualinterfaceid` int(10) unsigned,
  `status` int(10) unsigned,
  `speed` int(10) unsigned,
  `duplex` varchar(16),
  `monitorindex` int(10) unsigned,
  `notes` mediumtext,
  `switchport` varchar(255),
  `switchportid` int(10) unsigned,
  `switch` varchar(255),
  `switchid` int(10) unsigned,
  `vendorid` int(10) unsigned,
  `snmppasswd` varchar(255),
  `infrastructure` tinyint(3) unsigned,
  `cabinet` varchar(255),
  `colocabinet` varchar(255),
  `locationname` varchar(255),
  `locationshortname` varchar(255)
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `view_vlaninterface_details_by_custid` (
  `id` int(10) unsigned,
  `custid` int(10) unsigned,
  `virtualinterfaceid` int(10) unsigned,
  `monitorindex` int(10) unsigned,
  `virtualinterfacename` varchar(255),
  `vlan` int(10) unsigned,
  `vlanname` varchar(255),
  `vlanid` int(10) unsigned,
  `rcvrfname` varchar(255),
  `ipv4enabled` tinyint(4),
  `ipv4hostname` varchar(64),
  `ipv4canping` tinyint(3) unsigned,
  `ipv4monitorrcbgp` tinyint(3) unsigned,
  `ipv6enabled` tinyint(4),
  `ipv6hostname` varchar(64),
  `ipv6canping` tinyint(3) unsigned,
  `ipv6monitorrcbgp` tinyint(3) unsigned,
  `as112client` tinyint(3) unsigned,
  `mcastenabled` tinyint(4),
  `ipv4bgpmd5secret` varchar(64),
  `ipv6bgpmd5secret` varchar(64),
  `rsclient` tinyint(4),
  `busyhost` tinyint(1),
  `notes` text,
  `ipv4address` varchar(16),
  `ipv6address` varchar(40)
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `virtualinterface` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `custid` int(10) unsigned DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `mtu` int(10) unsigned DEFAULT NULL,
  `trunk` tinyint(3) unsigned DEFAULT NULL,
  `channelgroup` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=194 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `virtualinterface_seq` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=44 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vlan` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL DEFAULT '',
  `number` int(10) unsigned NOT NULL DEFAULT '0',
  `rcvrfname` varchar(255) DEFAULT NULL,
  `notes` mediumtext NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vlan_seq` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vlaninterface` (
  `virtualinterfaceid` int(10) unsigned NOT NULL,
  `vlanid` int(10) unsigned DEFAULT NULL,
  `ipv4enabled` tinyint(4) DEFAULT NULL,
  `ipv4addressid` int(10) unsigned DEFAULT NULL,
  `ipv4hostname` varchar(64) NOT NULL DEFAULT '',
  `ipv6enabled` tinyint(4) DEFAULT NULL,
  `ipv6addressid` int(10) unsigned DEFAULT NULL,
  `ipv6hostname` varchar(64) NOT NULL DEFAULT '',
  `mcastenabled` tinyint(4) DEFAULT NULL,
  `bgpmd5secret` varchar(64) DEFAULT NULL,
  `ipv4bgpmd5secret` varchar(64) DEFAULT NULL,
  `ipv6bgpmd5secret` varchar(64) DEFAULT NULL,
  `maxbgpprefix` int(10) unsigned DEFAULT NULL,
  `rsclient` tinyint(4) NOT NULL DEFAULT '0',
  `ipv4canping` tinyint(3) unsigned DEFAULT NULL,
  `ipv6canping` tinyint(3) unsigned DEFAULT NULL,
  `ipv4monitorrcbgp` tinyint(3) unsigned DEFAULT NULL,
  `ipv6monitorrcbgp` tinyint(3) unsigned DEFAULT NULL,
  `as112client` tinyint(3) unsigned DEFAULT NULL,
  `busyhost` tinyint(1) DEFAULT '0',
  `notes` text,
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`),
  KEY `virtualinterfaceid` (`virtualinterfaceid`)
) ENGINE=InnoDB AUTO_INCREMENT=139 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vlaninterface_seq` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=114 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!50001 DROP TABLE IF EXISTS `view_cust_current_active`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `view_cust_current_active` AS select `cu`.`name` AS `name`,`cu`.`type` AS `type`,`cu`.`shortname` AS `shortname`,`cu`.`autsys` AS `autsys`,`cu`.`maxprefixes` AS `maxprefixes`,`cu`.`peeringemail` AS `peeringemail`,`cu`.`nocphone` AS `nocphone`,`cu`.`noc24hphone` AS `noc24hphone`,`cu`.`nocfax` AS `nocfax`,`cu`.`nocemail` AS `nocemail`,`cu`.`nochours` AS `nochours`,`cu`.`nocwww` AS `nocwww`,`cu`.`irrdb` AS `irrdb`,`cu`.`peeringmacro` AS `peeringmacro`,`cu`.`peeringpolicy` AS `peeringpolicy`,`cu`.`billingContact` AS `billingContact`,`cu`.`billingAddress1` AS `billingAddress1`,`cu`.`billingAddress2` AS `billingAddress2`,`cu`.`billingCity` AS `billingCity`,`cu`.`billingCountry` AS `billingCountry`,`cu`.`corpwww` AS `corpwww`,`cu`.`datejoin` AS `datejoin`,`cu`.`dateleave` AS `dateleave`,`cu`.`status` AS `status`,`cu`.`activepeeringmatrix` AS `activepeeringmatrix`,`cu`.`notes` AS `notes`,`cu`.`lastupdated` AS `lastupdated`,`cu`.`lastupdatedby` AS `lastupdatedby`,`cu`.`creator` AS `creator`,`cu`.`created` AS `created`,`cu`.`id` AS `id` from `cust` `cu` where ((`cu`.`datejoin` <= curdate()) and (isnull(`cu`.`dateleave`) or (`cu`.`dateleave` < '1990-01-01') or (`cu`.`dateleave` >= curdate())) and ((`cu`.`status` = 1) or (`cu`.`status` = 2))) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!50001 DROP TABLE IF EXISTS `view_switch_details_by_custid`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `view_switch_details_by_custid` AS select `vi`.`id` AS `id`,`vi`.`custid` AS `custid`,`pi`.`virtualinterfaceid` AS `virtualinterfaceid`,`pi`.`status` AS `status`,`pi`.`speed` AS `speed`,`pi`.`duplex` AS `duplex`,`pi`.`monitorindex` AS `monitorindex`,`pi`.`notes` AS `notes`,`sp`.`name` AS `switchport`,`sp`.`id` AS `switchportid`,`sw`.`name` AS `switch`,`sw`.`id` AS `switchid`,`sw`.`vendorid` AS `vendorid`,`sw`.`snmppasswd` AS `snmppasswd`,`sw`.`infrastructure` AS `infrastructure`,`ca`.`name` AS `cabinet`,`ca`.`cololocation` AS `colocabinet`,`lo`.`name` AS `locationname`,`lo`.`shortname` AS `locationshortname` from (((((`virtualinterface` `vi` join `physicalinterface` `pi`) join `switchport` `sp`) join `switch` `sw`) join `cabinet` `ca`) join `location` `lo`) where ((`pi`.`virtualinterfaceid` = `vi`.`id`) and (`pi`.`switchportid` = `sp`.`id`) and (`sp`.`switchid` = `sw`.`id`) and (`sw`.`cabinetid` = `ca`.`id`) and (`ca`.`locationid` = `lo`.`id`)) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!50001 DROP TABLE IF EXISTS `view_vlaninterface_details_by_custid`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `view_vlaninterface_details_by_custid` AS select `pi`.`id` AS `id`,`vi`.`custid` AS `custid`,`pi`.`virtualinterfaceid` AS `virtualinterfaceid`,`pi`.`monitorindex` AS `monitorindex`,`vi`.`name` AS `virtualinterfacename`,`vlan`.`number` AS `vlan`,`vlan`.`name` AS `vlanname`,`vlan`.`id` AS `vlanid`,`vlan`.`rcvrfname` AS `rcvrfname`,`vli`.`ipv4enabled` AS `ipv4enabled`,`vli`.`ipv4hostname` AS `ipv4hostname`,`vli`.`ipv4canping` AS `ipv4canping`,`vli`.`ipv4monitorrcbgp` AS `ipv4monitorrcbgp`,`vli`.`ipv6enabled` AS `ipv6enabled`,`vli`.`ipv6hostname` AS `ipv6hostname`,`vli`.`ipv6canping` AS `ipv6canping`,`vli`.`ipv6monitorrcbgp` AS `ipv6monitorrcbgp`,`vli`.`as112client` AS `as112client`,`vli`.`mcastenabled` AS `mcastenabled`,`vli`.`ipv4bgpmd5secret` AS `ipv4bgpmd5secret`,`vli`.`ipv6bgpmd5secret` AS `ipv6bgpmd5secret`,`vli`.`rsclient` AS `rsclient`,`vli`.`busyhost` AS `busyhost`,`vli`.`notes` AS `notes`,`v4`.`address` AS `ipv4address`,`v6`.`address` AS `ipv6address` from ((`physicalinterface` `pi` join `virtualinterface` `vi`) join (((`vlaninterface` `vli` left join `ipv4address` `v4` on((`vli`.`ipv4addressid` = `v4`.`id`))) left join `ipv6address` `v6` on((`vli`.`ipv6addressid` = `v6`.`id`))) left join `vlan` on((`vli`.`vlanid` = `vlan`.`id`)))) where ((`pi`.`virtualinterfaceid` = `vi`.`id`) and (`vli`.`virtualinterfaceid` = `vi`.`id`)) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

