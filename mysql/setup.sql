-- MariaDB dump 10.19  Distrib 10.4.24-MariaDB, for Win64 (AMD64)
--
-- Host: 137.9.40.104    Database: feeddb
-- ------------------------------------------------------
-- Server version	5.5.62-0ubuntu0.12.04.1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `asset`
--

CREATE DATABASE IF NOT EXISTS `feedservice` ;
use `feedservice`;

DROP TABLE IF EXISTS `asset`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `asset` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(255) DEFAULT NULL,
  `category` int(10) unsigned DEFAULT NULL,
  `sub_category` int(10) unsigned DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `price` decimal(65,2) DEFAULT NULL,
  `currency` varchar(10) DEFAULT NULL,
  `purchase_date` date DEFAULT NULL,
  `expire_date` date DEFAULT NULL,
  `status` tinyint(1) DEFAULT NULL,
  `vendor` int(10) unsigned DEFAULT NULL,
  `purchase_order` varchar(255) DEFAULT NULL,
  `invoice_no` varchar(255) DEFAULT NULL,
  `serial_no` varchar(255) DEFAULT NULL,
  `part_no` varchar(255) DEFAULT NULL,
  `detail` text,
  PRIMARY KEY (`id`),
  KEY `vendor_index` (`vendor`) USING BTREE,
  KEY `category_index` (`category`),
  KEY `sub_category_index` (`sub_category`),
  CONSTRAINT `asset_fk1` FOREIGN KEY (`vendor`) REFERENCES `vendor` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `asset_fk2` FOREIGN KEY (`category`) REFERENCES `category` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `asset_fk3` FOREIGN KEY (`sub_category`) REFERENCES `sub_category` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=86 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `branch_filter`
--

DROP TABLE IF EXISTS `branch_filter`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `branch_filter` (
  `BRANCHID` char(10) NOT NULL,
  `BRANCHNAME` char(30) DEFAULT NULL,
  `SEK` char(3) DEFAULT NULL,
  PRIMARY KEY (`BRANCHID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `branch_option`
--

DROP TABLE IF EXISTS `branch_option`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `branch_option` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `sek` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `branch_trans`
--

DROP TABLE IF EXISTS `branch_trans`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `branch_trans` (
  `IDFEED` bigint(20) NOT NULL AUTO_INCREMENT,
  `DATEID` date DEFAULT NULL,
  `BRANCHID` char(20) DEFAULT NULL,
  `MS` double DEFAULT NULL,
  `WEIGHT` double DEFAULT NULL,
  `MATCHAMT` decimal(20,0) DEFAULT NULL,
  `MATCHCNT` decimal(20,0) DEFAULT NULL,
  `CUSTCNT` decimal(20,0) DEFAULT NULL,
  `LOGINCNT` decimal(20,0) DEFAULT NULL,
  `DATEIMPORT` date DEFAULT NULL,
  `NEWCUST` decimal(20,0) DEFAULT NULL,
  `CLOSECUST` decimal(20,0) DEFAULT NULL,
  `ONLINEAMT` decimal(20,0) DEFAULT NULL,
  `OFFLINEAMT` decimal(20,0) DEFAULT NULL,
  PRIMARY KEY (`IDFEED`)
) ENGINE=InnoDB AUTO_INCREMENT=43287 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `bulk_sms`
--

DROP TABLE IF EXISTS `bulk_sms`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bulk_sms` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `send_date` datetime NOT NULL,
  `username` varchar(255) NOT NULL,
  `phone_no` varchar(255) NOT NULL,
  `message` varchar(255) NOT NULL,
  `response` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2998 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `category`
--

DROP TABLE IF EXISTS `category`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `category` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(255) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `class`
--

DROP TABLE IF EXISTS `class`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `class` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(255) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `client_grade`
--

DROP TABLE IF EXISTS `client_grade`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `client_grade` (
  `date_id` date NOT NULL,
  `acct_no` varchar(8) NOT NULL,
  `acct_name` varchar(40) DEFAULT NULL,
  `commission` decimal(15,2) DEFAULT NULL,
  `interest` decimal(15,2) DEFAULT NULL,
  `stock_value` decimal(15,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `client_name`
--

DROP TABLE IF EXISTS `client_name`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `client_name` (
  `acct_no` varchar(8) NOT NULL,
  `acct_name` varchar(40) DEFAULT NULL,
  PRIMARY KEY (`acct_no`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `client_status`
--

DROP TABLE IF EXISTS `client_status`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `client_status` (
  `acct_no` varchar(8) NOT NULL,
  `online_or_not` varchar(8) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `client_trans`
--

DROP TABLE IF EXISTS `client_trans`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `client_trans` (
  `acct_no` varchar(8) NOT NULL,
  `client_name` varchar(40) DEFAULT NULL,
  `match_amt` double(15,0) DEFAULT NULL,
  `dateid` date NOT NULL,
  PRIMARY KEY (`acct_no`,`dateid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `contact`
--

DROP TABLE IF EXISTS `contact`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `contact` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `position` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `vendor` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `vendor_index` (`vendor`),
  CONSTRAINT `contact_fk1` FOREIGN KEY (`vendor`) REFERENCES `vendor` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `daily_feed`
--

DROP TABLE IF EXISTS `daily_feed`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `daily_feed` (
  `idfeed` bigint(20) NOT NULL AUTO_INCREMENT,
  `datefeed` date DEFAULT NULL,
  `customer` bigint(20) DEFAULT NULL,
  `staff` bigint(20) DEFAULT NULL,
  `trial` bigint(20) DEFAULT NULL,
  `jumlah` bigint(20) DEFAULT NULL,
  `dateimport` date DEFAULT NULL,
  `akademis` bigint(20) DEFAULT NULL,
  `spm` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`idfeed`)
) ENGINE=InnoDB AUTO_INCREMENT=5186 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `daily_trans`
--

DROP TABLE IF EXISTS `daily_trans`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `daily_trans` (
  `IDFEED` bigint(20) NOT NULL AUTO_INCREMENT,
  `DATEID` date DEFAULT NULL,
  `CLOSEIDX` double DEFAULT NULL,
  `VALUEIDX` double DEFAULT NULL,
  `BULAN` int(11) DEFAULT NULL,
  `TAHUN` int(11) DEFAULT NULL,
  `DATEIMPORT` date DEFAULT NULL,
  PRIMARY KEY (`IDFEED`)
) ENGINE=InnoDB AUTO_INCREMENT=18349 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `detail_trans`
--

DROP TABLE IF EXISTS `detail_trans`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `detail_trans` (
  `noid` bigint(20) NOT NULL AUTO_INCREMENT,
  `acct_no` varchar(8) NOT NULL DEFAULT '',
  `stock_code` varchar(40) NOT NULL DEFAULT '',
  `side` varchar(40) NOT NULL,
  `match_price` double(15,0) NOT NULL DEFAULT '0',
  `match_qty` double(15,0) NOT NULL DEFAULT '0',
  `amount` double(15,0) NOT NULL,
  `dateid` date NOT NULL DEFAULT '0000-00-00',
  PRIMARY KEY (`noid`)
) ENGINE=InnoDB AUTO_INCREMENT=3400117 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `dttot`
--

DROP TABLE IF EXISTS `dttot`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dttot` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama` varchar(250) NOT NULL,
  `nama_alias` text NOT NULL,
  `tempat_lahir` varchar(50) DEFAULT NULL,
  `tgl_lahir` date DEFAULT NULL,
  `kewarganegaraan` varchar(50) DEFAULT NULL,
  `alamat` text,
  `keterangan` text,
  `sumber_data` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=85685 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `log_incident`
--

DROP TABLE IF EXISTS `log_incident`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `log_incident` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `open_date` datetime DEFAULT NULL,
  `close_date` datetime DEFAULT NULL,
  `class` int(10) unsigned DEFAULT NULL,
  `sub_class` int(10) DEFAULT NULL,
  `symptom` text,
  `cause` text,
  `reporter` varchar(255) DEFAULT NULL,
  `solution` text,
  `solver` varchar(255) DEFAULT NULL,
  `status` tinyint(1) DEFAULT NULL,
  `detail` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `mobile_trans`
--

DROP TABLE IF EXISTS `mobile_trans`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mobile_trans` (
  `IDFEED` bigint(20) NOT NULL AUTO_INCREMENT,
  `DATEID` date DEFAULT NULL,
  `BRANCH` char(20) DEFAULT NULL,
  `MATCH_AMT` decimal(20,0) DEFAULT NULL,
  `MATCH_CNT` decimal(20,0) DEFAULT NULL,
  `ORDER_CNT` decimal(20,0) DEFAULT NULL,
  `WEIGHT` double DEFAULT NULL,
  `DATEIMPORT` date DEFAULT NULL,
  PRIMARY KEY (`IDFEED`)
) ENGINE=InnoDB AUTO_INCREMENT=18613 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `monthly_feed`
--

DROP TABLE IF EXISTS `monthly_feed`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `monthly_feed` (
  `idfeed` bigint(20) NOT NULL AUTO_INCREMENT,
  `datefeed` date DEFAULT NULL,
  `customer` bigint(20) DEFAULT NULL,
  `staff` bigint(20) DEFAULT NULL,
  `trial` bigint(20) DEFAULT NULL,
  `jumlah` bigint(20) DEFAULT NULL,
  `dateimport` date DEFAULT NULL,
  `bulan` int(11) DEFAULT NULL,
  `tahun` int(11) DEFAULT NULL,
  `akademis` bigint(20) DEFAULT NULL,
  `spm` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`idfeed`)
) ENGINE=InnoDB AUTO_INCREMENT=157 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ng_daily`
--

DROP TABLE IF EXISTS `ng_daily`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ng_daily` (
  `DATEID` date DEFAULT NULL,
  `VOL_Thou` decimal(20,0) DEFAULT NULL,
  `VAL_Mill` decimal(20,0) DEFAULT NULL,
  `FBVAL_Mill` decimal(20,0) DEFAULT NULL,
  `FSVAL_Mill` decimal(20,0) DEFAULT NULL,
  `FRQ` decimal(20,0) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ng_monthly`
--

DROP TABLE IF EXISTS `ng_monthly`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ng_monthly` (
  `DATEID` date DEFAULT NULL,
  `VOL_Mill` decimal(20,0) DEFAULT NULL,
  `VAL_Bill` decimal(20,0) DEFAULT NULL,
  `FBUY_Bill` decimal(20,0) DEFAULT NULL,
  `FSELL_Bill` decimal(20,0) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `param`
--

DROP TABLE IF EXISTS `param`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `param` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `parameter` varchar(40) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=879 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `role_permission`
--

DROP TABLE IF EXISTS `role_permission`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `role_permission` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `group` varchar(255) NOT NULL,
  `role` varchar(255) NOT NULL,
  `permission` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=86 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `saldo_rdn`
--

DROP TABLE IF EXISTS `saldo_rdn`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `saldo_rdn` (
  `no_rdn` decimal(20,0) NOT NULL,
  `saldo` decimal(20,0) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sub_category`
--

DROP TABLE IF EXISTS `sub_category`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sub_category` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(255) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `category` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `category_index` (`category`),
  CONSTRAINT `sub_cat_fk1` FOREIGN KEY (`category`) REFERENCES `category` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sub_class`
--

DROP TABLE IF EXISTS `sub_class`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sub_class` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(255) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `class` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `class_index` (`class`),
  CONSTRAINT `subclass_fk1` FOREIGN KEY (`class`) REFERENCES `class` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `trading_limit`
--

DROP TABLE IF EXISTS `trading_limit`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `trading_limit` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `dt_tradinglimit` datetime DEFAULT NULL,
  `cur_tradinglimit` decimal(15,2) DEFAULT NULL,
  `ch_inputer` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8060 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `department` varchar(255) DEFAULT NULL,
  `status` varchar(10) NOT NULL DEFAULT 'Active',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=49 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_login`
--

DROP TABLE IF EXISTS `user_login`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_login` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) DEFAULT NULL,
  `group` varchar(255) DEFAULT NULL,
  `user` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_index` (`user`),
  CONSTRAINT `user_login_fk1` FOREIGN KEY (`user`) REFERENCES `user` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_transaction`
--

DROP TABLE IF EXISTS `user_transaction`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_transaction` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user` int(10) unsigned NOT NULL,
  `asset` int(10) unsigned NOT NULL,
  `begin_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `detail` text,
  `status` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_index` (`user`),
  KEY `asset_index` (`asset`),
  CONSTRAINT `user_trans_fk1` FOREIGN KEY (`asset`) REFERENCES `asset` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `user_trans_fk2` FOREIGN KEY (`user`) REFERENCES `user` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `vendor`
--

DROP TABLE IF EXISTS `vendor`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vendor` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(255) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `fax` varchar(255) DEFAULT NULL,
  `website` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `vendor_transaction`
--

DROP TABLE IF EXISTS `vendor_transaction`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vendor_transaction` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `vendor` int(10) unsigned NOT NULL,
  `asset` int(10) unsigned NOT NULL,
  `begin_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `detail` text,
  PRIMARY KEY (`id`),
  KEY `vendor_index` (`vendor`),
  KEY `asset_index` (`asset`),
  CONSTRAINT `vendor_trans_fk1` FOREIGN KEY (`asset`) REFERENCES `asset` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `vendor_trans_fk2` FOREIGN KEY (`vendor`) REFERENCES `vendor` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping routines for database 'feeddb'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2023-12-29 19:22:11
