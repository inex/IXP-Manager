-- MySQL dump 10.13  Distrib 5.5.46, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: ixp3
-- ------------------------------------------------------
-- Server version	5.5.46-0ubuntu0.14.04.2

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
-- Temporary table structure for view `view_authuser`
--

DROP TABLE IF EXISTS `view_authuser`;
/*!50001 DROP VIEW IF EXISTS `view_authuser`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `view_authuser` (
  `username` tinyint NOT NULL,
  `pass_md5` tinyint NOT NULL,
  `disabled` tinyint NOT NULL,
  `privs` tinyint NOT NULL,
  `custid` tinyint NOT NULL
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `view_lonap_activeusers`
--

DROP TABLE IF EXISTS `view_lonap_activeusers`;
/*!50001 DROP VIEW IF EXISTS `view_lonap_activeusers`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `view_lonap_activeusers` (
  `username` tinyint NOT NULL,
  `password` tinyint NOT NULL,
  `dissabled` tinyint NOT NULL
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `view_lonap_cust_contactgroup`
--

DROP TABLE IF EXISTS `view_lonap_cust_contactgroup`;
/*!50001 DROP VIEW IF EXISTS `view_lonap_cust_contactgroup`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `view_lonap_cust_contactgroup` (
  `custid` tinyint NOT NULL,
  `contact_group_id` tinyint NOT NULL,
  `id` tinyint NOT NULL
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `view_lonap_iplookup`
--

DROP TABLE IF EXISTS `view_lonap_iplookup`;
/*!50001 DROP VIEW IF EXISTS `view_lonap_iplookup`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `view_lonap_iplookup` (
  `custid` tinyint NOT NULL,
  `autsys` tinyint NOT NULL,
  `ipv4address` tinyint NOT NULL,
  `ipv6address` tinyint NOT NULL,
  `rsclient` tinyint NOT NULL
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `view_lonap_live_asns`
--

DROP TABLE IF EXISTS `view_lonap_live_asns`;
/*!50001 DROP VIEW IF EXISTS `view_lonap_live_asns`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `view_lonap_live_asns` (
  `id` tinyint NOT NULL,
  `autsys` tinyint NOT NULL,
  `abbreviatedName` tinyint NOT NULL
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `view_lonap_mail`
--

DROP TABLE IF EXISTS `view_lonap_mail`;
/*!50001 DROP VIEW IF EXISTS `view_lonap_mail`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `view_lonap_mail` (
  `name` tinyint NOT NULL,
  `email` tinyint NOT NULL,
  `position` tinyint NOT NULL,
  `status` tinyint NOT NULL,
  `custtype` tinyint NOT NULL,
  `groupid` tinyint NOT NULL,
  `custid` tinyint NOT NULL
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `view_lonap_portvlanip`
--

DROP TABLE IF EXISTS `view_lonap_portvlanip`;
/*!50001 DROP VIEW IF EXISTS `view_lonap_portvlanip`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `view_lonap_portvlanip` (
  `custid` tinyint NOT NULL,
  `switchname` tinyint NOT NULL,
  `ifName` tinyint NOT NULL,
  `speed` tinyint NOT NULL,
  `status` tinyint NOT NULL,
  `ipv4address` tinyint NOT NULL,
  `ipv6address` tinyint NOT NULL,
  `vlanid` tinyint NOT NULL,
  `vlan_number` tinyint NOT NULL,
  `vlan_name` tinyint NOT NULL,
  `ifOperStatus` tinyint NOT NULL,
  `ifAdminStatus` tinyint NOT NULL,
  `rsclient` tinyint NOT NULL,
  `virtid` tinyint NOT NULL,
  `ipv4hostname` tinyint NOT NULL,
  `ipv6hostname` tinyint NOT NULL,
  `as112client` tinyint NOT NULL,
  `irrdbfilter` tinyint NOT NULL,
  `maxbgpprefix` tinyint NOT NULL,
  `ipv4canping` tinyint NOT NULL,
  `ipv6canping` tinyint NOT NULL,
  `ipv4monitorrcbgp` tinyint NOT NULL,
  `ipv6monitorrcbgp` tinyint NOT NULL,
  `busyhost` tinyint NOT NULL,
  `lastPolled` tinyint NOT NULL
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `view_lonap_switchports`
--

DROP TABLE IF EXISTS `view_lonap_switchports`;
/*!50001 DROP VIEW IF EXISTS `view_lonap_switchports`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `view_lonap_switchports` (
  `custid` tinyint NOT NULL,
  `autsys` tinyint NOT NULL,
  `duplex` tinyint NOT NULL,
  `speed` tinyint NOT NULL,
  `status` tinyint NOT NULL,
  `switchportid` tinyint NOT NULL,
  `virtualinterfaceid` tinyint NOT NULL,
  `switchname` tinyint NOT NULL,
  `portname` tinyint NOT NULL,
  `type` tinyint NOT NULL,
  `active` tinyint NOT NULL,
  `ifName` tinyint NOT NULL,
  `ifAlias` tinyint NOT NULL,
  `ifHighSpeed` tinyint NOT NULL,
  `ifAdminStatus` tinyint NOT NULL,
  `ifOperStatus` tinyint NOT NULL,
  `VLAN` tinyint NOT NULL
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `view_lonap_whois`
--

DROP TABLE IF EXISTS `view_lonap_whois`;
/*!50001 DROP VIEW IF EXISTS `view_lonap_whois`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `view_lonap_whois` (
  `custid` tinyint NOT NULL,
  `name` tinyint NOT NULL,
  `tradingname` tinyint NOT NULL,
  `handle` tinyint NOT NULL,
  `autsys` tinyint NOT NULL,
  `asmacrov4` tinyint NOT NULL,
  `asmacrov6` tinyint NOT NULL,
  `type` tinyint NOT NULL,
  `status` tinyint NOT NULL,
  `corpwww` tinyint NOT NULL,
  `peeringemail` tinyint NOT NULL,
  `nocphone` tinyint NOT NULL,
  `nocemail` tinyint NOT NULL,
  `lastupdated` tinyint NOT NULL,
  `ipv4address` tinyint NOT NULL,
  `ipv6address` tinyint NOT NULL,
  `rsclient` tinyint NOT NULL,
  `vlanid` tinyint NOT NULL
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Final view structure for view `view_authuser`
--

/*!50001 DROP TABLE IF EXISTS `view_authuser`*/;
/*!50001 DROP VIEW IF EXISTS `view_authuser`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `view_authuser` AS select `u`.`username` AS `username`,md5(`u`.`password`) AS `pass_md5`,`u`.`disabled` AS `disabled`,`u`.`privs` AS `privs`,`u`.`custid` AS `custid` from (`ixp`.`cust` join `ixp`.`user` `u` on((`u`.`custid` = `ixp`.`cust`.`id`))) where ((`u`.`disabled` = 0) and (`ixp`.`cust`.`status` = 1)) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `view_lonap_activeusers`
--

/*!50001 DROP TABLE IF EXISTS `view_lonap_activeusers`*/;
/*!50001 DROP VIEW IF EXISTS `view_lonap_activeusers`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `view_lonap_activeusers` AS select `ixp`.`user`.`username` AS `username`,`ixp`.`user`.`password` AS `password`,`ixp`.`user`.`disabled` AS `dissabled` from (`ixp`.`cust` join `ixp`.`user` on((`ixp`.`user`.`custid` = `ixp`.`cust`.`id`))) where ((`ixp`.`user`.`disabled` = 0) and (`ixp`.`cust`.`status` = 1)) order by `ixp`.`user`.`username` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `view_lonap_cust_contactgroup`
--

/*!50001 DROP TABLE IF EXISTS `view_lonap_cust_contactgroup`*/;
/*!50001 DROP VIEW IF EXISTS `view_lonap_cust_contactgroup`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `view_lonap_cust_contactgroup` AS select `ixp`.`contact`.`custid` AS `custid`,`ixp`.`contact_to_group`.`contact_group_id` AS `contact_group_id`,`ixp`.`contact`.`id` AS `id` from (`ixp`.`contact` join `ixp`.`contact_to_group` on((`ixp`.`contact_to_group`.`contact_id` = `ixp`.`contact`.`id`))) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `view_lonap_iplookup`
--

/*!50001 DROP TABLE IF EXISTS `view_lonap_iplookup`*/;
/*!50001 DROP VIEW IF EXISTS `view_lonap_iplookup`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `view_lonap_iplookup` AS select `ixp`.`cust`.`id` AS `custid`,`ixp`.`cust`.`autsys` AS `autsys`,`ixp`.`ipv4address`.`address` AS `ipv4address`,`ixp`.`ipv6address`.`address` AS `ipv6address`,`ixp`.`vlaninterface`.`rsclient` AS `rsclient` from ((((`ixp`.`vlaninterface` left join `ixp`.`ipv4address` on((`ixp`.`vlaninterface`.`ipv4addressid` = `ixp`.`ipv4address`.`id`))) left join `ixp`.`ipv6address` on((`ixp`.`vlaninterface`.`ipv6addressid` = `ixp`.`ipv6address`.`id`))) join `ixp`.`virtualinterface` on((`ixp`.`vlaninterface`.`virtualinterfaceid` = `ixp`.`virtualinterface`.`id`))) join `ixp`.`cust` on((`ixp`.`virtualinterface`.`custid` = `ixp`.`cust`.`id`))) where ((`ixp`.`vlaninterface`.`vlanid` = 1) and (`ixp`.`cust`.`type` <> 3)) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `view_lonap_live_asns`
--

/*!50001 DROP TABLE IF EXISTS `view_lonap_live_asns`*/;
/*!50001 DROP VIEW IF EXISTS `view_lonap_live_asns`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `view_lonap_live_asns` AS select `ixp`.`cust`.`id` AS `id`,`ixp`.`cust`.`autsys` AS `autsys`,`ixp`.`cust`.`abbreviatedName` AS `abbreviatedName` from `ixp`.`cust` where ((`ixp`.`cust`.`status` = 1) and (`ixp`.`cust`.`type` <> 3)) order by `ixp`.`cust`.`autsys` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `view_lonap_mail`
--

/*!50001 DROP TABLE IF EXISTS `view_lonap_mail`*/;
/*!50001 DROP VIEW IF EXISTS `view_lonap_mail`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `view_lonap_mail` AS select `ixp`.`contact`.`name` AS `name`,`ixp`.`contact`.`email` AS `email`,`ixp`.`contact`.`position` AS `position`,`ixp`.`cust`.`status` AS `status`,`ixp`.`cust`.`type` AS `custtype`,`ixp`.`contact_group`.`id` AS `groupid`,`ixp`.`cust`.`id` AS `custid` from (((`ixp`.`cust` join `ixp`.`contact` on((`ixp`.`contact`.`custid` = `ixp`.`cust`.`id`))) join `ixp`.`contact_to_group` on((`ixp`.`contact_to_group`.`contact_id` = `ixp`.`contact`.`id`))) join `ixp`.`contact_group` on((`ixp`.`contact_to_group`.`contact_group_id` = `ixp`.`contact_group`.`id`))) where ((not((`ixp`.`contact`.`name` like '%Admin%'))) and (`ixp`.`contact`.`email` is not null) and (not((`ixp`.`contact`.`position` like '%Admin Account%'))) and (`ixp`.`cust`.`status` = 1) and (`ixp`.`cust`.`type` = 1) and ((`ixp`.`contact_group`.`id` = 2) or (`ixp`.`contact_group`.`id` = 3))) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `view_lonap_portvlanip`
--

/*!50001 DROP TABLE IF EXISTS `view_lonap_portvlanip`*/;
/*!50001 DROP VIEW IF EXISTS `view_lonap_portvlanip`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `view_lonap_portvlanip` AS select `ixp`.`virtualinterface`.`custid` AS `custid`,`ixp`.`switch`.`name` AS `switchname`,`ixp`.`switchport`.`ifName` AS `ifName`,`ixp`.`physicalinterface`.`speed` AS `speed`,`ixp`.`physicalinterface`.`status` AS `status`,`ixp`.`ipv4address`.`address` AS `ipv4address`,`ixp`.`ipv6address`.`address` AS `ipv6address`,`ixp`.`vlaninterface`.`vlanid` AS `vlanid`,`ixp`.`vlan`.`number` AS `vlan_number`,`ixp`.`vlan`.`name` AS `vlan_name`,`ixp`.`switchport`.`ifOperStatus` AS `ifOperStatus`,`ixp`.`switchport`.`ifAdminStatus` AS `ifAdminStatus`,`ixp`.`vlaninterface`.`rsclient` AS `rsclient`,`ixp`.`virtualinterface`.`id` AS `virtid`,`ixp`.`vlaninterface`.`ipv4hostname` AS `ipv4hostname`,`ixp`.`vlaninterface`.`ipv6hostname` AS `ipv6hostname`,`ixp`.`vlaninterface`.`as112client` AS `as112client`,`ixp`.`vlaninterface`.`irrdbfilter` AS `irrdbfilter`,`ixp`.`vlaninterface`.`maxbgpprefix` AS `maxbgpprefix`,`ixp`.`vlaninterface`.`ipv4canping` AS `ipv4canping`,`ixp`.`vlaninterface`.`ipv6canping` AS `ipv6canping`,`ixp`.`vlaninterface`.`ipv4monitorrcbgp` AS `ipv4monitorrcbgp`,`ixp`.`vlaninterface`.`ipv6monitorrcbgp` AS `ipv6monitorrcbgp`,`ixp`.`vlaninterface`.`busyhost` AS `busyhost`,`ixp`.`switch`.`lastPolled` AS `lastPolled` from (((((((`ixp`.`physicalinterface` join `ixp`.`switchport` on((`ixp`.`physicalinterface`.`switchportid` = `ixp`.`switchport`.`id`))) left join `ixp`.`switch` on((`ixp`.`switchport`.`switchid` = `ixp`.`switch`.`id`))) join `ixp`.`virtualinterface` on((`ixp`.`physicalinterface`.`virtualinterfaceid` = `ixp`.`virtualinterface`.`id`))) left join `ixp`.`vlaninterface` on((`ixp`.`vlaninterface`.`virtualinterfaceid` = `ixp`.`virtualinterface`.`id`))) left join `ixp`.`ipv4address` on((`ixp`.`vlaninterface`.`ipv4addressid` = `ixp`.`ipv4address`.`id`))) left join `ixp`.`ipv6address` on((`ixp`.`vlaninterface`.`ipv6addressid` = `ixp`.`ipv6address`.`id`))) left join `ixp`.`vlan` on((`ixp`.`vlaninterface`.`vlanid` = `ixp`.`vlan`.`id`))) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `view_lonap_switchports`
--

/*!50001 DROP TABLE IF EXISTS `view_lonap_switchports`*/;
/*!50001 DROP VIEW IF EXISTS `view_lonap_switchports`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `view_lonap_switchports` AS select `ixp`.`cust`.`id` AS `custid`,`ixp`.`cust`.`autsys` AS `autsys`,`ixp`.`physicalinterface`.`duplex` AS `duplex`,`ixp`.`physicalinterface`.`speed` AS `speed`,`ixp`.`physicalinterface`.`status` AS `status`,`ixp`.`physicalinterface`.`switchportid` AS `switchportid`,`ixp`.`physicalinterface`.`virtualinterfaceid` AS `virtualinterfaceid`,`ixp`.`switch`.`name` AS `switchname`,`ixp`.`switchport`.`name` AS `portname`,`ixp`.`switchport`.`type` AS `type`,`ixp`.`switchport`.`active` AS `active`,`ixp`.`switchport`.`ifName` AS `ifName`,`ixp`.`switchport`.`ifAlias` AS `ifAlias`,`ixp`.`switchport`.`ifHighSpeed` AS `ifHighSpeed`,`ixp`.`switchport`.`ifAdminStatus` AS `ifAdminStatus`,`ixp`.`switchport`.`ifOperStatus` AS `ifOperStatus`,`ixp`.`vlan`.`number` AS `VLAN` from ((((((`ixp`.`physicalinterface` join `ixp`.`switchport` on((`ixp`.`physicalinterface`.`switchportid` = `ixp`.`switchport`.`id`))) join `ixp`.`switch` on((`ixp`.`switchport`.`switchid` = `ixp`.`switch`.`id`))) join `ixp`.`virtualinterface` on((`ixp`.`physicalinterface`.`virtualinterfaceid` = `ixp`.`virtualinterface`.`id`))) join `ixp`.`cust` on((`ixp`.`virtualinterface`.`custid` = `ixp`.`cust`.`id`))) join `ixp`.`vlaninterface` on((`ixp`.`vlaninterface`.`virtualinterfaceid` = `ixp`.`virtualinterface`.`id`))) join `ixp`.`vlan` on((`ixp`.`vlaninterface`.`vlanid` = `ixp`.`vlan`.`id`))) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `view_lonap_whois`
--

/*!50001 DROP TABLE IF EXISTS `view_lonap_whois`*/;
/*!50001 DROP VIEW IF EXISTS `view_lonap_whois`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `view_lonap_whois` AS select `ixp`.`cust`.`id` AS `custid`,`ixp`.`cust`.`name` AS `name`,`ixp`.`cust`.`abbreviatedName` AS `tradingname`,`ixp`.`cust`.`shortname` AS `handle`,`ixp`.`cust`.`autsys` AS `autsys`,`ixp`.`cust`.`peeringmacro` AS `asmacrov4`,`ixp`.`cust`.`peeringmacrov6` AS `asmacrov6`,`ixp`.`cust`.`type` AS `type`,`ixp`.`cust`.`status` AS `status`,`ixp`.`cust`.`corpwww` AS `corpwww`,`ixp`.`cust`.`peeringemail` AS `peeringemail`,`ixp`.`cust`.`nocphone` AS `nocphone`,`ixp`.`cust`.`nocemail` AS `nocemail`,`ixp`.`cust`.`lastupdated` AS `lastupdated`,`ixp`.`ipv4address`.`address` AS `ipv4address`,`ixp`.`ipv6address`.`address` AS `ipv6address`,`ixp`.`vlaninterface`.`rsclient` AS `rsclient`,`ixp`.`vlaninterface`.`vlanid` AS `vlanid` from ((((`ixp`.`vlaninterface` left join `ixp`.`ipv4address` on((`ixp`.`vlaninterface`.`ipv4addressid` = `ixp`.`ipv4address`.`id`))) left join `ixp`.`ipv6address` on((`ixp`.`vlaninterface`.`ipv6addressid` = `ixp`.`ipv6address`.`id`))) join `ixp`.`virtualinterface` on((`ixp`.`vlaninterface`.`virtualinterfaceid` = `ixp`.`virtualinterface`.`id`))) join `ixp`.`cust` on((`ixp`.`virtualinterface`.`custid` = `ixp`.`cust`.`id`))) where ((`ixp`.`cust`.`type` <> 3) and (`ixp`.`vlaninterface`.`vlanid` = 1)) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

