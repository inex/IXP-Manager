-- MySQL dump 10.11
--
-- Host: localhost    Database: inex
-- ------------------------------------------------------
-- Server version	5.0.67-log

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
-- Table structure for table `bgpsessiondata`
--

DROP TABLE IF EXISTS `bgpsessiondata`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `bgpsessiondata` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `srcipv4addressid` int(10) unsigned default NULL,
  `dstipv4addressid` int(10) unsigned default NULL,
  `packetcount` int(10) unsigned default NULL,
  `timestamp` datetime default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=803044 DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `cabinet`
--

DROP TABLE IF EXISTS `cabinet`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `cabinet` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `locationid` int(10) unsigned NOT NULL default '0',
  `name` varchar(255) NOT NULL default '',
  `cololocation` varchar(255) NOT NULL default '',
  `height` int(11) NOT NULL default '0',
  `type` varchar(255) NOT NULL default '',
  `notes` mediumtext NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `consoleserverconnection`
--

DROP TABLE IF EXISTS `consoleserverconnection`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `consoleserverconnection` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `description` varchar(255) default NULL,
  `custid` int(10) unsigned NOT NULL,
  `port` varchar(255) default NULL,
  `switchid` int(10) unsigned NOT NULL,
  `speed` int(10) unsigned default NULL,
  `parity` int(10) unsigned default NULL,
  `stopbits` int(10) unsigned default NULL,
  `flowcontrol` int(10) unsigned default NULL,
  `autobaud` tinyint(4) default NULL,
  `notes` mediumtext,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=23 DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `consoleserverconnection_seq`
--

DROP TABLE IF EXISTS `consoleserverconnection_seq`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `consoleserverconnection_seq` (
  `id` int(10) unsigned NOT NULL auto_increment,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=23 DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `contact`
--

DROP TABLE IF EXISTS `contact`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `contact` (
  `custid` int(10) unsigned NOT NULL default '0',
  `name` varchar(64) NOT NULL default '',
  `email` varchar(64) default NULL,
  `phone` varchar(32) default NULL,
  `mobile` varchar(32) default NULL,
  `facilityaccess` tinyint(3) unsigned NOT NULL default '1',
  `mayauthorize` tinyint(3) unsigned NOT NULL default '0',
  `lastupdated` datetime default NULL,
  `lastupdatedby` int(11) default NULL,
  `creator` varchar(32) default 'Operations',
  `created` datetime default NULL,
  `id` int(10) unsigned NOT NULL auto_increment,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=336 DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `contact_seq`
--

DROP TABLE IF EXISTS `contact_seq`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `contact_seq` (
  `id` int(10) unsigned NOT NULL auto_increment,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `cust`
--

DROP TABLE IF EXISTS `cust`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `cust` (
  `name` varchar(255) NOT NULL default '',
  `type` int(10) unsigned default NULL,
  `shortname` varchar(255) NOT NULL default '',
  `autsys` int(10) unsigned NOT NULL default '0',
  `maxprefixes` int(10) unsigned default NULL,
  `peeringemail` varchar(64) NOT NULL default '',
  `nocphone` varchar(255) default NULL,
  `noc24hphone` varchar(255) default NULL,
  `nocfax` varchar(40) default NULL,
  `nocemail` varchar(40) default NULL,
  `nochours` varchar(40) NOT NULL default '24x7',
  `nocwww` varchar(255) default NULL,
  `peeringmacro` varchar(255) NOT NULL default '',
  `peeringpolicy` varchar(255) default NULL,
  `billingContact` varchar(64) default NULL,
  `billingAddress1` varchar(64) default NULL,
  `billingAddress2` varchar(64) default NULL,
  `billingCity` varchar(64) default NULL,
  `billingCountry` char(2) default NULL,
  `corpwww` varchar(255) default NULL,
  `datejoin` date default NULL,
  `dateleave` date default NULL,
  `status` tinyint(4) default NULL,
  `activepeeringmatrix` tinyint(4) NOT NULL default '1',
  `notes` mediumtext,
  `lastupdated` datetime default NULL,
  `lastupdatedby` int(11) default NULL,
  `creator` varchar(32) default 'Operations',
  `created` datetime default NULL,
  `id` int(10) unsigned NOT NULL auto_increment,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=68 DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `cust_seq`
--

DROP TABLE IF EXISTS `cust_seq`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `cust_seq` (
  `id` int(10) unsigned NOT NULL auto_increment,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=68 DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `custkit`
--

DROP TABLE IF EXISTS `custkit`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `custkit` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(255) NOT NULL default '',
  `custid` int(10) unsigned NOT NULL default '0',
  `cabinetid` int(10) unsigned NOT NULL default '0',
  `descr` mediumtext NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `ipv4address`
--

DROP TABLE IF EXISTS `ipv4address`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `ipv4address` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `address` varchar(16) NOT NULL,
  `vlanid` int(10) unsigned default NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `address` (`address`)
) ENGINE=InnoDB AUTO_INCREMENT=377 DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `ipv6address`
--

DROP TABLE IF EXISTS `ipv6address`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `ipv6address` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `address` varchar(40) NOT NULL,
  `vlanid` int(10) unsigned default NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `address` (`address`)
) ENGINE=InnoDB AUTO_INCREMENT=1309 DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `location`
--

DROP TABLE IF EXISTS `location`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `location` (
  `id` int(10) unsigned NOT NULL default '0',
  `name` varchar(255) NOT NULL default '',
  `shortname` varchar(255) default NULL,
  `address` varchar(255) NOT NULL default '',
  `nocphone` varchar(255) NOT NULL default '',
  `nocfax` varchar(255) NOT NULL default '',
  `nocemail` varchar(255) NOT NULL default '',
  `officephone` varchar(255) NOT NULL default '',
  `officefax` varchar(255) NOT NULL default '',
  `officeemail` varchar(255) NOT NULL default '',
  `notes` blob,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `networkinfo`
--

DROP TABLE IF EXISTS `networkinfo`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `networkinfo` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `vlanid` int(10) unsigned NOT NULL,
  `protocol` tinyint(3) unsigned NOT NULL,
  `network` varchar(255) NOT NULL,
  `masklen` tinyint(4) default NULL,
  `rs1address` varchar(40) default NULL,
  `rs2address` varchar(40) default NULL,
  `dnsfile` varchar(255) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `physicalinterface`
--

DROP TABLE IF EXISTS `physicalinterface`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `physicalinterface` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `switchportid` int(10) unsigned NOT NULL,
  `virtualinterfaceid` int(10) unsigned default NULL,
  `status` int(10) unsigned NOT NULL,
  `speed` int(10) unsigned NOT NULL,
  `duplex` varchar(16) NOT NULL,
  `monitorindex` int(10) unsigned NOT NULL,
  `notes` mediumtext,
  PRIMARY KEY  (`id`),
  KEY `virtualinterfaceid` (`virtualinterfaceid`)
) ENGINE=InnoDB AUTO_INCREMENT=164 DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `physicalinterface_seq`
--

DROP TABLE IF EXISTS `physicalinterface_seq`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `physicalinterface_seq` (
  `id` int(10) unsigned NOT NULL auto_increment,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=32 DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `switch`
--

DROP TABLE IF EXISTS `switch`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `switch` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(255) NOT NULL default '',
  `cabinetid` int(10) unsigned NOT NULL default '0',
  `ipv4addr` varchar(255) NOT NULL default '',
  `ipv6addr` varchar(255) NOT NULL default '',
  `snmppasswd` varchar(255) NOT NULL default '',
  `switchtype` int(10) unsigned default NULL,
  `vendorid` int(10) unsigned NOT NULL default '0',
  `model` varchar(255) NOT NULL default '',
  `notes` mediumtext NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `switch_seq`
--

DROP TABLE IF EXISTS `switch_seq`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `switch_seq` (
  `id` int(10) unsigned NOT NULL auto_increment,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `switchport`
--

DROP TABLE IF EXISTS `switchport`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `switchport` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `switchid` int(10) unsigned NOT NULL default '0',
  `name` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=380 DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `user` (
  `username` varchar(30) NOT NULL default '',
  `password` varchar(30) NOT NULL default '',
  `email` varchar(255) default NULL,
  `uid` int(10) unsigned default NULL,
  `custid` int(10) unsigned NOT NULL default '0',
  `privs` tinyint(4) default '0',
  `disabled` tinyint(4) NOT NULL default '0',
  `lastupdated` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `lastupdatedby` int(11) default NULL,
  `creator` varchar(32) default 'Manager',
  `created` datetime default NULL,
  `id` int(10) unsigned NOT NULL auto_increment,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=64 DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `user_seq`
--

DROP TABLE IF EXISTS `user_seq`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `user_seq` (
  `id` int(10) unsigned NOT NULL auto_increment,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=63 DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `vendor`
--

DROP TABLE IF EXISTS `vendor`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `vendor` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `view_cust_current_active`
--

DROP TABLE IF EXISTS `view_cust_current_active`;
/*!50001 DROP VIEW IF EXISTS `view_cust_current_active`*/;
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
) */;

--
-- Temporary table structure for view `view_switch_details_by_custid`
--

DROP TABLE IF EXISTS `view_switch_details_by_custid`;
/*!50001 DROP VIEW IF EXISTS `view_switch_details_by_custid`*/;
/*!50001 CREATE TABLE `view_switch_details_by_custid` (
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
  `cabinet` varchar(255),
  `colocabinet` varchar(255),
  `locationname` varchar(255),
  `locationshortname` varchar(255)
) */;

--
-- Temporary table structure for view `view_vlaninterface_details_by_custid`
--

DROP TABLE IF EXISTS `view_vlaninterface_details_by_custid`;
/*!50001 DROP VIEW IF EXISTS `view_vlaninterface_details_by_custid`*/;
/*!50001 CREATE TABLE `view_vlaninterface_details_by_custid` (
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
  `bgpmd5secret` varchar(64),
  `rsclient` tinyint(4),
  `notes` text,
  `ipv4address` varchar(16),
  `ipv6address` varchar(40)
) */;

--
-- Table structure for table `virtualinterface`
--

DROP TABLE IF EXISTS `virtualinterface`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `virtualinterface` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `custid` int(10) unsigned default NULL,
  `name` varchar(255) NOT NULL,
  `description` varchar(255) default NULL,
  `mtu` int(10) unsigned default NULL,
  `trunk` tinyint(3) unsigned default NULL,
  `channelgroup` int(10) unsigned default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=168 DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `virtualinterface_seq`
--

DROP TABLE IF EXISTS `virtualinterface_seq`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `virtualinterface_seq` (
  `id` int(10) unsigned NOT NULL auto_increment,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=31 DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `vlan`
--

DROP TABLE IF EXISTS `vlan`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `vlan` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(255) NOT NULL default '',
  `number` int(10) unsigned NOT NULL default '0',
  `ipv6masklen` smallint(5) unsigned default NULL,
  `ipv4masklen` smallint(5) unsigned default NULL,
  `rcvrfname` varchar(255) default NULL,
  `notes` mediumtext NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `vlan_seq`
--

DROP TABLE IF EXISTS `vlan_seq`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `vlan_seq` (
  `id` int(10) unsigned NOT NULL auto_increment,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `vlaninterface`
--

DROP TABLE IF EXISTS `vlaninterface`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `vlaninterface` (
  `virtualinterfaceid` int(10) unsigned NOT NULL,
  `vlanid` int(10) unsigned default NULL,
  `ipv4enabled` tinyint(4) default NULL,
  `ipv4addressid` int(10) unsigned default NULL,
  `ipv4hostname` varchar(64) NOT NULL default '',
  `ipv6enabled` tinyint(4) default NULL,
  `ipv6addressid` int(10) unsigned default NULL,
  `ipv6hostname` varchar(64) NOT NULL default '',
  `mcastenabled` tinyint(4) default NULL,
  `bgpmd5secret` varchar(64) default NULL,
  `maxbgpprefix` int(10) unsigned default NULL,
  `rsclient` tinyint(4) NOT NULL default '0',
  `ipv4canping` tinyint(3) unsigned default NULL,
  `ipv6canping` tinyint(3) unsigned default NULL,
  `ipv4monitorrcbgp` tinyint(3) unsigned default NULL,
  `ipv6monitorrcbgp` tinyint(3) unsigned default NULL,
  `as112client` tinyint(3) unsigned default NULL,
  `notes` text,
  `id` int(10) unsigned NOT NULL auto_increment,
  PRIMARY KEY  (`id`),
  KEY `virtualinterfaceid` (`virtualinterfaceid`)
) ENGINE=InnoDB AUTO_INCREMENT=98 DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `vlaninterface_seq`
--

DROP TABLE IF EXISTS `vlaninterface_seq`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `vlaninterface_seq` (
  `id` int(10) unsigned NOT NULL auto_increment,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=98 DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Final view structure for view `view_cust_current_active`
--

/*!50001 DROP TABLE `view_cust_current_active`*/;
/*!50001 DROP VIEW IF EXISTS `view_cust_current_active`*/;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `view_cust_current_active` AS select `cu`.`name` AS `name`,`cu`.`type` AS `type`,`cu`.`shortname` AS `shortname`,`cu`.`autsys` AS `autsys`,`cu`.`maxprefixes` AS `maxprefixes`,`cu`.`peeringemail` AS `peeringemail`,`cu`.`nocphone` AS `nocphone`,`cu`.`noc24hphone` AS `noc24hphone`,`cu`.`nocfax` AS `nocfax`,`cu`.`nocemail` AS `nocemail`,`cu`.`nochours` AS `nochours`,`cu`.`nocwww` AS `nocwww`,`cu`.`peeringmacro` AS `peeringmacro`,`cu`.`peeringpolicy` AS `peeringpolicy`,`cu`.`billingContact` AS `billingContact`,`cu`.`billingAddress1` AS `billingAddress1`,`cu`.`billingAddress2` AS `billingAddress2`,`cu`.`billingCity` AS `billingCity`,`cu`.`billingCountry` AS `billingCountry`,`cu`.`corpwww` AS `corpwww`,`cu`.`datejoin` AS `datejoin`,`cu`.`dateleave` AS `dateleave`,`cu`.`status` AS `status`,`cu`.`activepeeringmatrix` AS `activepeeringmatrix`,`cu`.`notes` AS `notes`,`cu`.`lastupdated` AS `lastupdated`,`cu`.`lastupdatedby` AS `lastupdatedby`,`cu`.`creator` AS `creator`,`cu`.`created` AS `created`,`cu`.`id` AS `id` from `cust` `cu` where ((isnull(`cu`.`dateleave`) or ((`cu`.`dateleave` < _latin1'1990-01-01') and (`cu`.`dateleave` < now()))) and ((`cu`.`status` = 1) or (`cu`.`status` = 2))) */;

--
-- Final view structure for view `view_switch_details_by_custid`
--

/*!50001 DROP TABLE `view_switch_details_by_custid`*/;
/*!50001 DROP VIEW IF EXISTS `view_switch_details_by_custid`*/;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `view_switch_details_by_custid` AS select `vi`.`custid` AS `custid`,`pi`.`virtualinterfaceid` AS `virtualinterfaceid`,`pi`.`status` AS `status`,`pi`.`speed` AS `speed`,`pi`.`duplex` AS `duplex`,`pi`.`monitorindex` AS `monitorindex`,`pi`.`notes` AS `notes`,`sp`.`name` AS `switchport`,`sp`.`id` AS `switchportid`,`sw`.`name` AS `switch`,`sw`.`id` AS `switchid`,`ca`.`name` AS `cabinet`,`ca`.`cololocation` AS `colocabinet`,`lo`.`name` AS `locationname`,`lo`.`shortname` AS `locationshortname` from (((((`virtualinterface` `vi` join `physicalinterface` `pi`) join `switchport` `sp`) join `switch` `sw`) join `cabinet` `ca`) join `location` `lo`) where ((`pi`.`virtualinterfaceid` = `vi`.`id`) and (`pi`.`switchportid` = `sp`.`id`) and (`sp`.`switchid` = `sw`.`id`) and (`sw`.`cabinetid` = `ca`.`id`) and (`ca`.`locationid` = `lo`.`id`)) */;

--
-- Final view structure for view `view_vlaninterface_details_by_custid`
--

/*!50001 DROP TABLE `view_vlaninterface_details_by_custid`*/;
/*!50001 DROP VIEW IF EXISTS `view_vlaninterface_details_by_custid`*/;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `view_vlaninterface_details_by_custid` AS select `vi`.`custid` AS `custid`,`pi`.`virtualinterfaceid` AS `virtualinterfaceid`,`pi`.`monitorindex` AS `monitorindex`,`vi`.`name` AS `virtualinterfacename`,`vlan`.`number` AS `vlan`,`vlan`.`name` AS `vlanname`,`vlan`.`id` AS `vlanid`,`vlan`.`rcvrfname` AS `rcvrfname`,`vli`.`ipv4enabled` AS `ipv4enabled`,`vli`.`ipv4hostname` AS `ipv4hostname`,`vli`.`ipv4canping` AS `ipv4canping`,`vli`.`ipv4monitorrcbgp` AS `ipv4monitorrcbgp`,`vli`.`ipv6enabled` AS `ipv6enabled`,`vli`.`ipv6hostname` AS `ipv6hostname`,`vli`.`ipv6canping` AS `ipv6canping`,`vli`.`ipv6monitorrcbgp` AS `ipv6monitorrcbgp`,`vli`.`as112client` AS `as112client`,`vli`.`mcastenabled` AS `mcastenabled`,`vli`.`bgpmd5secret` AS `bgpmd5secret`,`vli`.`rsclient` AS `rsclient`,`vli`.`notes` AS `notes`,`v4`.`address` AS `ipv4address`,`v6`.`address` AS `ipv6address` from ((`physicalinterface` `pi` join `virtualinterface` `vi`) join (((`vlaninterface` `vli` left join `ipv4address` `v4` on((`vli`.`ipv4addressid` = `v4`.`id`))) left join `ipv6address` `v6` on((`vli`.`ipv6addressid` = `v6`.`id`))) left join `vlan` on((`vli`.`vlanid` = `vlan`.`id`)))) where ((`pi`.`virtualinterfaceid` = `vi`.`id`) and (`vli`.`virtualinterfaceid` = `vi`.`id`)) */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2008-11-17 15:26:58
