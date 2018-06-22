-- MySQL dump 10.13  Distrib 5.5.57, for Linux (x86_64)
--
-- Host: localhost    Database: cp6686me
-- ------------------------------------------------------
-- Server version	5.5.57-log

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
-- Table structure for table `ubs_abilities`
--

DROP TABLE IF EXISTS `ubs_abilities`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ubs_abilities` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `entity_id` int(10) unsigned DEFAULT NULL,
  `entity_type` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `only_owned` tinyint(4) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=39 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ubs_abilities`
--

LOCK TABLES `ubs_abilities` WRITE;
/*!40000 ALTER TABLE `ubs_abilities` DISABLE KEYS */;
INSERT INTO `ubs_abilities` VALUES (28,'312123','阿德阿斯顿',NULL,NULL,0,'2017-09-21 20:28:28','2017-09-21 20:28:28'),(29,'312123','阿德阿斯顿个',NULL,NULL,0,'2017-09-21 20:28:31','2017-09-21 20:28:31'),(31,'312123','阿德阿斯顿个 我去饿',NULL,NULL,0,'2017-09-21 20:28:36','2017-09-21 20:28:36'),(32,'312123','阿德阿斯顿个 我去饿',NULL,NULL,0,'2017-09-21 20:28:36','2017-09-21 20:28:36'),(33,'323','啊',NULL,NULL,0,'2017-09-21 20:28:47','2017-09-21 20:28:47'),(38,'阿德阿斯顿个 我去饿',NULL,NULL,NULL,0,'2017-09-22 17:32:11','2017-09-22 17:32:11');
/*!40000 ALTER TABLE `ubs_abilities` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ubs_assigned_roles`
--

DROP TABLE IF EXISTS `ubs_assigned_roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ubs_assigned_roles` (
  `role_id` int(10) unsigned NOT NULL,
  `entity_id` int(10) unsigned NOT NULL,
  `entity_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  KEY `assigned_roles_entity_id_entity_type_index` (`entity_id`,`entity_type`) USING BTREE,
  KEY `assigned_roles_role_id_index` (`role_id`) USING BTREE,
  CONSTRAINT `ubs_assigned_roles_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `ubs_roles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ubs_assigned_roles`
--

LOCK TABLES `ubs_assigned_roles` WRITE;
/*!40000 ALTER TABLE `ubs_assigned_roles` DISABLE KEYS */;
/*!40000 ALTER TABLE `ubs_assigned_roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ubs_banks`
--

DROP TABLE IF EXISTS `ubs_banks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ubs_banks` (
  `bank_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `bank_name` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL,
  `bank_status` tinyint(3) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`bank_id`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ubs_banks`
--

LOCK TABLES `ubs_banks` WRITE;
/*!40000 ALTER TABLE `ubs_banks` DISABLE KEYS */;
INSERT INTO `ubs_banks` VALUES (1,'中国银行',1),(2,'工商银行',1),(3,'人民银行',1),(4,'建设银行',1),(5,'交通银行',1),(6,'招商银行',1),(7,'农业银行',1),(8,'民生银行',1),(9,'光大银行',1),(10,'中信银行',1),(11,'兴业银行',1),(12,'上海浦东发展银行',1),(13,'华夏银行',1),(14,'深圳发展银行',1),(15,'广东发展银行',1),(16,'邮政储蓄银行',1),(17,'农业发展银行',1);
/*!40000 ALTER TABLE `ubs_banks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ubs_cashiers_2017`
--

DROP TABLE IF EXISTS `ubs_cashiers_2017`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ubs_cashiers_2017` (
  `cashier_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `cashier_type` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `cashier_payment_id` int(10) unsigned NOT NULL COMMENT 'å…¥æ¬¾\n	\nå‡ºæ¬¾\n	ç®¡ç†å‘˜ID',
  `cashier_relate_uid` int(10) unsigned NOT NULL COMMENT 'å…¥æ¬¾\n	ç”¨æˆ·ID\nå‡ºæ¬¾\n	ç”¨æˆ·ID',
  `cashier_relate_amount` decimal(20,2) NOT NULL COMMENT 'å…¥æ¬¾\n	é‡‘é¢\nå‡ºæ¬¾\n	é‡‘é¢',
  `cashier_relate_status` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT 'å…¥æ¬¾\n	0	ç­‰å¾…\n	1	å·²å¤„ç†\n	2	å¼‚å¸¸ ?? æœªçŸ¥\nå‡ºæ¬¾\n	100	ç­‰å¾…\n	101	å·²å¤„ç†\n	102	å¼‚å¸¸ ?? æœªçŸ¥',
  `cashier_ext_info` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cashier_created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `cashier_updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`cashier_id`)
) ENGINE=InnoDB AUTO_INCREMENT=71 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ubs_cashiers_2017`
--

LOCK TABLES `ubs_cashiers_2017` WRITE;
/*!40000 ALTER TABLE `ubs_cashiers_2017` DISABLE KEYS */;
/*!40000 ALTER TABLE `ubs_cashiers_2017` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ubs_failed_jobs`
--

DROP TABLE IF EXISTS `ubs_failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ubs_failed_jobs` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ubs_failed_jobs`
--

LOCK TABLES `ubs_failed_jobs` WRITE;
/*!40000 ALTER TABLE `ubs_failed_jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `ubs_failed_jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ubs_jobs`
--

DROP TABLE IF EXISTS `ubs_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ubs_jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint(3) unsigned NOT NULL,
  `reserved_at` int(10) unsigned DEFAULT NULL,
  `available_at` int(10) unsigned NOT NULL,
  `created_at` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_reserved_at_index` (`queue`,`reserved_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ubs_jobs`
--

LOCK TABLES `ubs_jobs` WRITE;
/*!40000 ALTER TABLE `ubs_jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `ubs_jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ubs_lottery_2017`
--

DROP TABLE IF EXISTS `ubs_lottery_2017`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ubs_lottery_2017` (
  `lottery_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `lottery_class` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT 'å½©ç§-åˆ†ç±»',
  `lottery_round` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'å½©ç§-æœŸæ•°',
  `lottery_round_extra` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT 'å½©ç§-æœŸæ•°-æ‰©å±•',
  `lottery_time` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'å½©ç§-å¼€ç›˜, å°ç›˜-æ—¶é—´',
  `lottery_numbers` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'å½©ç§-å¼€å¥–å·ç ',
  `lottery_is_cancel` tinyint(4) NOT NULL DEFAULT '0' COMMENT 'å½©ç§-æ˜¯å¦å–æ¶ˆ',
  `lottery_is_open` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `lottery_created_at` date NOT NULL COMMENT 'å½©ç§-åˆ›å»ºæ—¶é—´-åˆ†åŒºå­—æ®µ',
  `lottery_updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'å½©ç§-æ›´æ–°æ—¶é—´',
  PRIMARY KEY (`lottery_id`),
  KEY `idx_lottery_class` (`lottery_class`)
) ENGINE=InnoDB AUTO_INCREMENT=5950 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ubs_lottery_2017`
--

LOCK TABLES `ubs_lottery_2017` WRITE;
/*!40000 ALTER TABLE `ubs_lottery_2017` DISABLE KEYS */;
/*!40000 ALTER TABLE `ubs_lottery_2017` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ubs_lottery_classify`
--

DROP TABLE IF EXISTS `ubs_lottery_classify`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ubs_lottery_classify` (
  `classify_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `classify_pid` int(10) unsigned NOT NULL DEFAULT '0',
  `classify_name` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `classify_code` smallint(5) unsigned NOT NULL DEFAULT '0',
  `classify_sort` tinyint(3) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`classify_id`)
) ENGINE=InnoDB AUTO_INCREMENT=72 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ubs_lottery_classify`
--

LOCK TABLES `ubs_lottery_classify` WRITE;
/*!40000 ALTER TABLE `ubs_lottery_classify` DISABLE KEYS */;
/*!40000 ALTER TABLE `ubs_lottery_classify` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ubs_lottery_numbers`
--

DROP TABLE IF EXISTS `ubs_lottery_numbers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ubs_lottery_numbers` (
  `number_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'çƒå·-ID',
  `number_class` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT 'çƒå·-åˆ†ç±»-å½©ç§',
  `number_code` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT 'çƒå·-åˆ†ç±»-çŽ©æ³•',
  `number_info` varchar(255) NOT NULL COMMENT 'çƒå·-è¯¦æƒ…',
  `number_created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'çƒå·-åˆ›å»ºæ—¶é—´',
  `number_updated_at` datetime NOT NULL DEFAULT '1970-01-01 00:00:00' COMMENT 'çƒå·-æ›´æ–°æ—¶é—´',
  `number_sort` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT 'çƒå·-æŽ’åº',
  PRIMARY KEY (`number_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4442 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ubs_lottery_numbers`
--

LOCK TABLES `ubs_lottery_numbers` WRITE;
/*!40000 ALTER TABLE `ubs_lottery_numbers` DISABLE KEYS */;
/*!40000 ALTER TABLE `ubs_lottery_numbers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ubs_lottery_numbers_limited`
--

DROP TABLE IF EXISTS `ubs_lottery_numbers_limited`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ubs_lottery_numbers_limited` (
  `limit_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `limit_uid` int(10) unsigned NOT NULL DEFAULT '0',
  `limit_number_id` int(10) unsigned NOT NULL,
  `limit_allow_amount_min` decimal(12,2) NOT NULL DEFAULT '0.00',
  `limit_allow_amount_max` decimal(12,2) NOT NULL DEFAULT '0.00',
  `limit_allow_amount_total` decimal(12,2) NOT NULL DEFAULT '0.00',
  `limit_created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `limit_updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`limit_id`),
  KEY `idx_limit_uid` (`limit_uid`)
) ENGINE=InnoDB AUTO_INCREMENT=6355 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ubs_lottery_numbers_limited`
--

LOCK TABLES `ubs_lottery_numbers_limited` WRITE;
/*!40000 ALTER TABLE `ubs_lottery_numbers_limited` DISABLE KEYS */;
/*!40000 ALTER TABLE `ubs_lottery_numbers_limited` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ubs_lottery_numbers_odds`
--

DROP TABLE IF EXISTS `ubs_lottery_numbers_odds`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ubs_lottery_numbers_odds` (
  `odds_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'çƒå·-èµ”çŽ‡ID',
  `odds_uid` int(10) unsigned NOT NULL COMMENT 'çƒå·-ç”¨æˆ·-èµ”çŽ‡',
  `odds_number_id` int(10) unsigned NOT NULL COMMENT 'çƒå·-èµ”çŽ‡å…³ç³»å­—æ®µ',
  `odds_number_odds` decimal(6,4) NOT NULL COMMENT 'çƒå·-èµ”çŽ‡',
  `odds_created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `odds_updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`odds_id`),
  KEY `idx_odds_uid` (`odds_uid`)
) ENGINE=InnoDB AUTO_INCREMENT=36028 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ubs_lottery_numbers_odds`
--

LOCK TABLES `ubs_lottery_numbers_odds` WRITE;
/*!40000 ALTER TABLE `ubs_lottery_numbers_odds` DISABLE KEYS */;
/*!40000 ALTER TABLE `ubs_lottery_numbers_odds` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ubs_lottery_numbers_rebate`
--

DROP TABLE IF EXISTS `ubs_lottery_numbers_rebate`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ubs_lottery_numbers_rebate` (
  `rebate_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `rebate_uid` int(10) unsigned NOT NULL,
  `rebate_number_id` int(10) unsigned NOT NULL,
  `rebate_percnet` decimal(6,2) NOT NULL DEFAULT '0.00',
  `rebate_created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `rebate_updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`rebate_id`),
  KEY `idx_rabate_uid` (`rebate_uid`)
) ENGINE=InnoDB AUTO_INCREMENT=9532 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ubs_lottery_numbers_rebate`
--

LOCK TABLES `ubs_lottery_numbers_rebate` WRITE;
/*!40000 ALTER TABLE `ubs_lottery_numbers_rebate` DISABLE KEYS */;
/*!40000 ALTER TABLE `ubs_lottery_numbers_rebate` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ubs_maintenances`
--

DROP TABLE IF EXISTS `ubs_maintenances`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ubs_maintenances` (
  `maintenance_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `maintenance_comment` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL,
  `maintenance_content` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`maintenance_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ubs_maintenances`
--

LOCK TABLES `ubs_maintenances` WRITE;
/*!40000 ALTER TABLE `ubs_maintenances` DISABLE KEYS */;
/*!40000 ALTER TABLE `ubs_maintenances` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ubs_marquees`
--

DROP TABLE IF EXISTS `ubs_marquees`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ubs_marquees` (
  `marquee_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `marquee_title` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `marquee_content` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `marquee_status` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `marquee_created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `marquee_updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`marquee_id`)
) ENGINE=InnoDB AUTO_INCREMENT=54 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ubs_marquees`
--

LOCK TABLES `ubs_marquees` WRITE;
/*!40000 ALTER TABLE `ubs_marquees` DISABLE KEYS */;
/*!40000 ALTER TABLE `ubs_marquees` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ubs_migrations`
--

DROP TABLE IF EXISTS `ubs_migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ubs_migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=68 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ubs_migrations`
--

LOCK TABLES `ubs_migrations` WRITE;
/*!40000 ALTER TABLE `ubs_migrations` DISABLE KEYS */;
/*!40000 ALTER TABLE `ubs_migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ubs_notifications`
--

DROP TABLE IF EXISTS `ubs_notifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ubs_notifications` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `notifiable_id` int(10) unsigned NOT NULL,
  `notifiable_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `data` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `notifications_notifiable_id_notifiable_type_index` (`notifiable_id`,`notifiable_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ubs_notifications`
--

LOCK TABLES `ubs_notifications` WRITE;
/*!40000 ALTER TABLE `ubs_notifications` DISABLE KEYS */;
/*!40000 ALTER TABLE `ubs_notifications` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ubs_payments`
--

DROP TABLE IF EXISTS `ubs_payments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ubs_payments` (
  `payment_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `payment_morph_id` int(10) unsigned NOT NULL,
  `payment_morph_type` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `payment_class` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT 'å…¥æ¬¾\n	0	çº¿ä¸‹é“¶è¡Œ\n	1	åœ¨çº¿é“¶è¡Œ\n	2	ç¬¬ä¸‰æ–¹-æ”¯ä»˜å®\n	3	ç¬¬ä¸‰æ–¹-å¾®ä¿¡\n	4 	ç¬¬ä¸‰æ–¹-è´¢ä»˜é€š\n\nå‡ºæ¬¾\n	100',
  PRIMARY KEY (`payment_id`)
) ENGINE=InnoDB AUTO_INCREMENT=177 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ubs_payments`
--

LOCK TABLES `ubs_payments` WRITE;
/*!40000 ALTER TABLE `ubs_payments` DISABLE KEYS */;
/*!40000 ALTER TABLE `ubs_payments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ubs_payments_deposit_ext`
--

DROP TABLE IF EXISTS `ubs_payments_deposit_ext`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ubs_payments_deposit_ext` (
  `deposit_ext_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `deposit_ext_deposit_n` int(10) unsigned NOT NULL DEFAULT '0',
  `deposit_ext_deposit_total` decimal(24,2) NOT NULL DEFAULT '0.00',
  `deposit_ext_deposit_min` decimal(6,2) NOT NULL,
  `deposit_ext_deposit_max` decimal(12,2) NOT NULL,
  `deposit_ext_deposit_rate` decimal(12,2) NOT NULL DEFAULT '0.00',
  `deposit_ext_user_paid` tinyint(3) unsigned NOT NULL,
  `deposit_ext_status` tinyint(3) unsigned NOT NULL,
  `deposit_ext_created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `deposit_ext_updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`deposit_ext_id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ubs_payments_deposit_ext`
--

LOCK TABLES `ubs_payments_deposit_ext` WRITE;
/*!40000 ALTER TABLE `ubs_payments_deposit_ext` DISABLE KEYS */;
/*!40000 ALTER TABLE `ubs_payments_deposit_ext` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ubs_payments_deposit_offline`
--

DROP TABLE IF EXISTS `ubs_payments_deposit_offline`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ubs_payments_deposit_offline` (
  `offline_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `offline_bank_id` int(10) unsigned NOT NULL DEFAULT '0',
  `offline_account` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT 'çº¿ä¸‹æ‰‹åŠ¨è½¬è´¦-é“¶è¡Œè´¦æˆ·',
  `offline_acc_open_address` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT 'çº¿ä¸‹æ‰‹åŠ¨è½¬è´¦-å¼€æˆ·åœ°å€',
  `offline_qr_code` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `offline_holder` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT 'çº¿ä¸‹æ‰‹åŠ¨è½¬è´¦-æŒå¡äººå§“å',
  `offline_ext_id` int(10) unsigned NOT NULL,
  `offline_created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `offline_updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`offline_id`)
) ENGINE=InnoDB AUTO_INCREMENT=174 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ubs_payments_deposit_offline`
--

LOCK TABLES `ubs_payments_deposit_offline` WRITE;
/*!40000 ALTER TABLE `ubs_payments_deposit_offline` DISABLE KEYS */;
/*!40000 ALTER TABLE `ubs_payments_deposit_offline` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ubs_payments_deposit_online`
--

DROP TABLE IF EXISTS `ubs_payments_deposit_online`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ubs_payments_deposit_online` (
  `online_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `online_notify_url` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT 'ç¬¬ä¸‰æ–¹æ”¯ä»˜è¿”å›žåœ°å€',
  `online_business_id` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT 'ç¬¬ä¸‰æ–¹æ”¯ä»˜å•†æˆ·å·',
  `online_terminal_id` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT 'ç¬¬ä¸‰æ–¹æ”¯ä»˜ç»ˆç«¯å·',
  `online_interface_name` char(15) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT 'æŽ¥å£å',
  `online_interface_domain` varchar(45) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT 'æ”¯ä»˜åŸŸå',
  `online_ext_id` int(10) unsigned NOT NULL,
  `online_created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `online_updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`online_id`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ubs_payments_deposit_online`
--

LOCK TABLES `ubs_payments_deposit_online` WRITE;
/*!40000 ALTER TABLE `ubs_payments_deposit_online` DISABLE KEYS */;
/*!40000 ALTER TABLE `ubs_payments_deposit_online` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ubs_payments_level`
--

DROP TABLE IF EXISTS `ubs_payments_level`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ubs_payments_level` (
  `level_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `level_type` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `level_payment_ids` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `level_comment` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `level_created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `level_updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`level_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ubs_payments_level`
--

LOCK TABLES `ubs_payments_level` WRITE;
/*!40000 ALTER TABLE `ubs_payments_level` DISABLE KEYS */;
/*!40000 ALTER TABLE `ubs_payments_level` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ubs_payments_withdraw_ext`
--

DROP TABLE IF EXISTS `ubs_payments_withdraw_ext`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ubs_payments_withdraw_ext` (
  `idubs_payments_withdraw_ext` int(10) unsigned NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`idubs_payments_withdraw_ext`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ubs_payments_withdraw_ext`
--

LOCK TABLES `ubs_payments_withdraw_ext` WRITE;
/*!40000 ALTER TABLE `ubs_payments_withdraw_ext` DISABLE KEYS */;
/*!40000 ALTER TABLE `ubs_payments_withdraw_ext` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ubs_payments_withdraw_offline`
--

DROP TABLE IF EXISTS `ubs_payments_withdraw_offline`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ubs_payments_withdraw_offline` (
  `idubs_payments_offline_withdraw` int(10) unsigned NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`idubs_payments_offline_withdraw`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ubs_payments_withdraw_offline`
--

LOCK TABLES `ubs_payments_withdraw_offline` WRITE;
/*!40000 ALTER TABLE `ubs_payments_withdraw_offline` DISABLE KEYS */;
/*!40000 ALTER TABLE `ubs_payments_withdraw_offline` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ubs_payments_withdraw_online`
--

DROP TABLE IF EXISTS `ubs_payments_withdraw_online`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ubs_payments_withdraw_online` (
  `idubs_payments_online_withdraw` int(10) unsigned NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`idubs_payments_online_withdraw`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ubs_payments_withdraw_online`
--

LOCK TABLES `ubs_payments_withdraw_online` WRITE;
/*!40000 ALTER TABLE `ubs_payments_withdraw_online` DISABLE KEYS */;
/*!40000 ALTER TABLE `ubs_payments_withdraw_online` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ubs_permissions`
--

DROP TABLE IF EXISTS `ubs_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ubs_permissions` (
  `ability_id` int(10) unsigned NOT NULL,
  `entity_id` int(10) unsigned NOT NULL,
  `entity_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `forbidden` tinyint(4) NOT NULL DEFAULT '0',
  KEY `permissions_ability_id_index` (`ability_id`),
  KEY `permissions_entity_id_entity_type_index` (`entity_id`,`entity_type`),
  CONSTRAINT `permissions_ability_id_index` FOREIGN KEY (`ability_id`) REFERENCES `ubs_abilities` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ubs_permissions`
--

LOCK TABLES `ubs_permissions` WRITE;
/*!40000 ALTER TABLE `ubs_permissions` DISABLE KEYS */;
/*!40000 ALTER TABLE `ubs_permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ubs_report_2017`
--

DROP TABLE IF EXISTS `ubs_report_2017`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ubs_report_2017` (
  `report_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `report_uid` int(10) unsigned NOT NULL DEFAULT '0',
  `report_count_betslip` int(10) unsigned NOT NULL DEFAULT '0',
  `report_count_betslip_amt` decimal(30,2) unsigned NOT NULL DEFAULT '0.00',
  `report_count_member_results` decimal(30,2) unsigned NOT NULL DEFAULT '0.00',
  `report_count_member_deposits` decimal(30,2) unsigned NOT NULL DEFAULT '0.00',
  `report_count_member_withdraw` decimal(30,2) unsigned NOT NULL DEFAULT '0.00',
  `report_date` date NOT NULL DEFAULT '1970-01-01',
  `report_created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`report_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ubs_report_2017`
--

LOCK TABLES `ubs_report_2017` WRITE;
/*!40000 ALTER TABLE `ubs_report_2017` DISABLE KEYS */;
/*!40000 ALTER TABLE `ubs_report_2017` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ubs_roles`
--

DROP TABLE IF EXISTS `ubs_roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ubs_roles` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `level` int(10) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_name_unique` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ubs_roles`
--

LOCK TABLES `ubs_roles` WRITE;
/*!40000 ALTER TABLE `ubs_roles` DISABLE KEYS */;
/*!40000 ALTER TABLE `ubs_roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ubs_users`
--

DROP TABLE IF EXISTS `ubs_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ubs_users` (
  `user_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ç”¨æˆ·-ID',
  `user_pid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'ç”¨æˆ·-å…³ç³»å­—æ®µ',
  `user_role` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT 'ç”¨æˆ·-è§’è‰²\n254: æ€»ç›‘\n253: åˆ†å…¬å¸\n252: è‚¡ä¸œ\n251: æ€»ä»£ç†\n250: ä»£ç†å•†\n249: ä¼šå‘˜\n248: å­è´¦å·\n247: ç®¡ç†å‘˜\n',
  `user_name` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'ç”¨æˆ·-å',
  `user_passwd` char(60) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'ç”¨æˆ·-å¯†ç ',
  `user_level_deposit` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `user_level_withdraw` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `user_memo` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `user_status` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT 'ç”¨æˆ·-çŠ¶æ€\n0åœç”¨\n1å¯ç”¨',
  `user_is_tester` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT 'ç”¨æˆ·-æ˜¯å¦æµ‹è¯•ç”¨æˆ·',
  `user_last_login_ip` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'ç”¨æˆ·-ç™»å½•ip',
  `user_session_id` char(40) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT 'ç”¨æˆ·-SESSID',
  `user_api_token` char(80) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `user_remember_token` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT 'ç”¨æˆ·-è®°ä½ç™»å½•TOKEN',
  `user_reg_source` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `user_created_at` datetime NOT NULL COMMENT 'ç”¨æˆ·-åˆ›å»ºæ—¶é—´',
  `user_updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'ç”¨æˆ·-æ›´æ–°æ—¶é—´',
  PRIMARY KEY (`user_id`),
  KEY `idx_user_role` (`user_role`),
  KEY `idx_user_pid` (`user_pid`)
) ENGINE=InnoDB AUTO_INCREMENT=120 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ubs_users`
--

LOCK TABLES `ubs_users` WRITE;
/*!40000 ALTER TABLE `ubs_users` DISABLE KEYS */;
/*!40000 ALTER TABLE `ubs_users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ubs_users_agent`
--

DROP TABLE IF EXISTS `ubs_users_agent`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ubs_users_agent` (
  `agent_id` int(11) unsigned NOT NULL DEFAULT '0',
  `agent_uid` int(10) unsigned NOT NULL DEFAULT '0',
  `agent_domain` varchar(191) NOT NULL DEFAULT '',
  `agent_created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `agent_updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`agent_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ubs_users_agent`
--

LOCK TABLES `ubs_users_agent` WRITE;
/*!40000 ALTER TABLE `ubs_users_agent` DISABLE KEYS */;
/*!40000 ALTER TABLE `ubs_users_agent` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ubs_users_betting_limited`
--

DROP TABLE IF EXISTS `ubs_users_betting_limited`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ubs_users_betting_limited` (
  `betting_limit_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `betting_limit_number_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`betting_limit_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ubs_users_betting_limited`
--

LOCK TABLES `ubs_users_betting_limited` WRITE;
/*!40000 ALTER TABLE `ubs_users_betting_limited` DISABLE KEYS */;
/*!40000 ALTER TABLE `ubs_users_betting_limited` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ubs_users_card`
--

DROP TABLE IF EXISTS `ubs_users_card`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ubs_users_card` (
  `card_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `card_uid` int(10) unsigned NOT NULL DEFAULT '0',
  `card_bank_id` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `card_holder` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `card_no` int(10) unsigned NOT NULL,
  `card_open_address` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `card_status` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `card_created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `card_updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`card_id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ubs_users_card`
--

LOCK TABLES `ubs_users_card` WRITE;
/*!40000 ALTER TABLE `ubs_users_card` DISABLE KEYS */;
/*!40000 ALTER TABLE `ubs_users_card` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ubs_users_login_log`
--

DROP TABLE IF EXISTS `ubs_users_login_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ubs_users_login_log` (
  `login_log_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `login_log_uid` int(10) unsigned NOT NULL DEFAULT '0',
  `login_log_ip` int(10) unsigned NOT NULL DEFAULT '0',
  `login_log_domain` varchar(191) NOT NULL DEFAULT '',
  `login_log_user_device` varchar(80) NOT NULL DEFAULT '',
  `login_log_user_platform` varchar(40) NOT NULL DEFAULT '',
  `login_log_user_platform_version` varchar(40) NOT NULL DEFAULT '',
  `login_log_user_browser` varchar(60) NOT NULL DEFAULT '',
  `login_log_user_browser_version` varchar(40) NOT NULL DEFAULT '',
  `login_log_created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`login_log_id`)
) ENGINE=InnoDB AUTO_INCREMENT=176 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ubs_users_login_log`
--

LOCK TABLES `ubs_users_login_log` WRITE;
/*!40000 ALTER TABLE `ubs_users_login_log` DISABLE KEYS */;
/*!40000 ALTER TABLE `ubs_users_login_log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ubs_users_setting`
--

DROP TABLE IF EXISTS `ubs_users_setting`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ubs_users_setting` (
  `setting_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `setting_uid` int(10) unsigned NOT NULL,
  `setting_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'allow_bet => å…è®¸ä¸‹æ³¨',
  `setting_value` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `setting_created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `setting_updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`setting_id`),
  KEY `idx_setting_uid` (`setting_uid`)
) ENGINE=InnoDB AUTO_INCREMENT=80 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ubs_users_setting`
--

LOCK TABLES `ubs_users_setting` WRITE;
/*!40000 ALTER TABLE `ubs_users_setting` DISABLE KEYS */;
/*!40000 ALTER TABLE `ubs_users_setting` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ubs_users_wallet`
--

DROP TABLE IF EXISTS `ubs_users_wallet`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ubs_users_wallet` (
  `wallet_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'é’±åŒ…-ID',
  `wallet_uid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'é’±åŒ…-ç”¨æˆ·ID',
  `wallet_cash` decimal(12,2) NOT NULL DEFAULT '0.00' COMMENT 'é’±åŒ…-ä½™é¢',
  `wallet_cash_before` decimal(12,2) NOT NULL DEFAULT '0.00' COMMENT 'é’±åŒ…-ä½™é¢-ä½¿ç”¨å‰',
  `wallet_cash_bonus` decimal(12,2) NOT NULL DEFAULT '0.00' COMMENT 'é’±åŒ…-ç”¨æˆ·-çº¢åŒ…ï¼Ÿ',
  `wallet_cash_history_withdraw` decimal(12,2) NOT NULL DEFAULT '0.00',
  `wallet_cash_history_withdraw_n` int(10) unsigned NOT NULL DEFAULT '0',
  `wallet_cash_history_deposit` decimal(12,2) NOT NULL DEFAULT '0.00',
  `wallet_cash_history_deposit_n` int(10) unsigned NOT NULL DEFAULT '0',
  `wallet_created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'é’±åŒ…-åˆ›å»ºæ—¶é—´',
  `wallet_updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'é’±åŒ…-æ›´æ–°æ—¶é—´',
  `wallet_passwd` char(60) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`wallet_id`),
  UNIQUE KEY `unique_wallet_uid` (`wallet_uid`)
) ENGINE=InnoDB AUTO_INCREMENT=80 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ubs_users_wallet`
--

LOCK TABLES `ubs_users_wallet` WRITE;
/*!40000 ALTER TABLE `ubs_users_wallet` DISABLE KEYS */;
/*!40000 ALTER TABLE `ubs_users_wallet` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ubs_users_wallet_log_2017`
--

DROP TABLE IF EXISTS `ubs_users_wallet_log_2017`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ubs_users_wallet_log_2017` (
  `wallet_log_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `wallet_log_uid` int(10) unsigned NOT NULL DEFAULT '0',
  `wallet_log_code` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '0	æœªçŸ¥\n1	å……å€¼',
  `wallet_log_detail` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `wallet_log_created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `wallet_log_updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`wallet_log_id`)
) ENGINE=InnoDB AUTO_INCREMENT=37 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ubs_users_wallet_log_2017`
--

LOCK TABLES `ubs_users_wallet_log_2017` WRITE;
/*!40000 ALTER TABLE `ubs_users_wallet_log_2017` DISABLE KEYS */;
/*!40000 ALTER TABLE `ubs_users_wallet_log_2017` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ubs_whitelists`
--

DROP TABLE IF EXISTS `ubs_whitelists`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ubs_whitelists` (
  `white_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `white_ip` int(10) unsigned NOT NULL DEFAULT '0',
  `white_uid` int(10) unsigned NOT NULL DEFAULT '0',
  `white_memo` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `white_created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `white_updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`white_id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ubs_whitelists`
--

LOCK TABLES `ubs_whitelists` WRITE;
/*!40000 ALTER TABLE `ubs_whitelists` DISABLE KEYS */;
/*!40000 ALTER TABLE `ubs_whitelists` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2018-01-01  8:51:37
