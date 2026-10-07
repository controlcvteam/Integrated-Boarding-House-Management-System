
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

CREATE DATABASE /*!32312 IF NOT EXISTS*/ `boarding_house` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci */;

USE `boarding_house`;
DROP TABLE IF EXISTS `app_notifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `app_notifications` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `type` varchar(50) NOT NULL DEFAULT 'info',
  `link` varchar(255) DEFAULT NULL,
  `action_url` varchar(255) DEFAULT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `app_notifications_user_id_foreign` (`user_id`),
  CONSTRAINT `app_notifications_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=310 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40000 ALTER TABLE `app_notifications` DISABLE KEYS */;
INSERT INTO `app_notifications` VALUES (1,1,'New GCash Payment for Verification','Tenant Juan Dela Cruz submitted ₱3,500.00 GCash payment for October 2026 (Ref: 1003882741923).','payment',NULL,'http://127.0.0.1:8000/admin/payments',1,'2026-10-04 06:03:07','2026-10-04 07:07:22'),(2,1,'New Pending Registration','Carlo Gomez registered for an account and requested Room 203.','tenant',NULL,'http://127.0.0.1:8000/admin/pending-tenants',1,'2026-10-04 06:03:07','2026-10-04 07:07:22'),(3,1,'New Maintenance Request','Screen window latch loose reported for Room 201.','maintenance',NULL,'http://127.0.0.1:8000/admin/maintenance',1,'2026-10-04 06:03:07','2026-10-04 07:07:22'),(5,1,'New Tenant Registration Awaiting Approval','Test Applicant has registered as a new tenant and is awaiting your review.','registration','http://127.0.0.1:8000/admin/pending-tenants/7',NULL,1,'2026-10-04 06:07:37','2026-10-04 07:07:22'),(8,1,'New Maintenance Request','Tenant Juan Dela Cruz reported [Plumbing]: \'Faucet handle loose\' for Room 101.','maintenance','http://127.0.0.1:8000/admin/maintenance/4','http://127.0.0.1:8000/admin/maintenance/4',1,'2026-10-04 06:07:38','2026-10-04 07:07:22'),(9,1,'New Tenant Registration Awaiting Approval','Test Applicant has registered as a new tenant and is awaiting your review.','registration','http://127.0.0.1:8000/admin/pending-tenants/8',NULL,1,'2026-10-04 06:08:03','2026-10-04 07:07:22'),(12,1,'New Maintenance Request','Tenant Juan Dela Cruz reported [Plumbing]: \'Faucet handle loose\' for Room 101.','maintenance','http://127.0.0.1:8000/admin/maintenance/5','http://127.0.0.1:8000/admin/maintenance/5',1,'2026-10-04 06:08:04','2026-10-04 07:07:22'),(14,1,'New Tenant Registration Awaiting Approval','Jan Marinelle has registered as a new tenant and is awaiting your review.','registration','http://127.0.0.1:8000/admin/pending-tenants/9',NULL,1,'2026-10-04 06:10:12','2026-10-04 07:07:22'),(18,1,'New Tenant Registration Awaiting Approval','Test Applicant has registered as a new tenant and is awaiting your review.','registration','http://127.0.0.1:8000/admin/pending-tenants/10',NULL,1,'2026-10-04 06:22:46','2026-10-04 07:07:22'),(21,1,'New Maintenance Request','Tenant Juan Dela Cruz reported [Plumbing]: \'Faucet handle loose\' for Room 101.','maintenance','http://127.0.0.1:8000/admin/maintenance/6','http://127.0.0.1:8000/admin/maintenance/6',1,'2026-10-04 06:22:46','2026-10-04 07:07:22'),(23,1,'New Room Request','Tenant Jan Marinelle has requested Room Room 001.','room','http://127.0.0.1:8000/admin/room-requests','http://127.0.0.1:8000/admin/room-requests',1,'2026-10-04 06:47:41','2026-10-04 07:07:22'),(25,1,'New Maintenance Request','Tenant Jan Marinelle reported [Furniture]: \'Ga leak siya\' for Room 103.','maintenance','http://127.0.0.1:8000/admin/maintenance/7','http://127.0.0.1:8000/admin/maintenance/7',1,'2026-10-04 06:58:09','2026-10-04 07:07:22'),(28,1,'New Room Request','Tenant Jan Marinelle has requested Room 002.','room','http://127.0.0.1:8000/admin/room-requests','http://127.0.0.1:8000/admin/room-requests',1,'2026-10-04 07:22:15','2026-10-04 08:35:50'),(29,1,'New Tenant Registration Awaiting Approval','Test Applicant has registered as a new tenant and is awaiting your review.','registration','http://127.0.0.1:8000/admin/pending-tenants/11',NULL,1,'2026-10-04 07:40:14','2026-10-04 08:35:50'),(32,1,'New Maintenance Request','Tenant Jan Marinelle reported [Plumbing]: \'Faucet handle loose\' for Room 103.','maintenance','http://127.0.0.1:8000/admin/maintenance/8','http://127.0.0.1:8000/admin/maintenance/8',1,'2026-10-04 07:40:14','2026-10-04 08:35:50'),(33,1,'New Tenant Registration Awaiting Approval','Test Applicant has registered as a new tenant and is awaiting your review.','registration','http://127.0.0.1:8000/admin/pending-tenants/12',NULL,1,'2026-10-04 07:40:39','2026-10-04 08:35:50'),(36,1,'New Maintenance Request','Tenant Jan Marinelle reported [Plumbing]: \'Faucet handle loose\' for Room 103.','maintenance','http://127.0.0.1:8000/admin/maintenance/9','http://127.0.0.1:8000/admin/maintenance/9',1,'2026-10-04 07:40:39','2026-10-04 08:35:50'),(37,1,'New Tenant Registration Awaiting Approval','Test Applicant has registered as a new tenant and is awaiting your review.','registration','http://127.0.0.1:8000/admin/pending-tenants/13',NULL,1,'2026-10-04 07:41:08','2026-10-04 08:35:50'),(40,1,'New Maintenance Request','Tenant Jan Marinelle reported [Plumbing]: \'Faucet handle loose\' for Room 103.','maintenance','http://127.0.0.1:8000/admin/maintenance/10','http://127.0.0.1:8000/admin/maintenance/10',1,'2026-10-04 07:41:08','2026-10-04 08:35:50'),(46,1,'New Tenant Registration Awaiting Approval','Test Applicant has registered as a new tenant and is awaiting your review.','registration','http://127.0.0.1:8000/admin/pending-tenants/14',NULL,1,'2026-10-04 08:35:48','2026-10-04 08:35:50'),(49,1,'New Maintenance Request','Tenant Jan Marinelle reported [Plumbing]: \'Faucet handle loose\' for Room 103.','maintenance','http://127.0.0.1:8000/admin/maintenance/11','http://127.0.0.1:8000/admin/maintenance/11',1,'2026-10-04 08:35:48','2026-10-04 08:35:50'),(51,1,'New Tenant Registration Awaiting Approval','Test Applicant has registered as a new tenant and is awaiting your review.','registration','http://127.0.0.1:8000/admin/pending-tenants/15',NULL,1,'2026-10-04 08:36:34','2026-10-04 08:36:35'),(54,1,'New Maintenance Request','Tenant Jan Marinelle reported [Plumbing]: \'Faucet handle loose\' for Room 103.','maintenance','http://127.0.0.1:8000/admin/maintenance/12','http://127.0.0.1:8000/admin/maintenance/12',1,'2026-10-04 08:36:34','2026-10-04 08:36:35'),(60,1,'New Tenant Registration Awaiting Approval','Test Applicant has registered as a new tenant and is awaiting your review.','registration','http://127.0.0.1:8000/admin/pending-tenants/17',NULL,1,'2026-10-04 08:38:27','2026-10-04 08:38:28'),(63,1,'New Maintenance Request','Tenant Jan Marinelle reported [Plumbing]: \'Faucet handle loose\' for Room 103.','maintenance','http://127.0.0.1:8000/admin/maintenance/14','http://127.0.0.1:8000/admin/maintenance/14',1,'2026-10-04 08:38:27','2026-10-04 08:38:28'),(70,1,'New Maintenance Request','Tenant Jan Marinelle reported [Plumbing]: \'Faucet handle loose\' for Room 103.','maintenance','http://127.0.0.1:8000/admin/maintenance/16','http://127.0.0.1:8000/admin/maintenance/16',1,'2026-10-04 08:41:26','2026-10-04 08:41:26'),(75,1,'New Tenant Registration Awaiting Approval','Test Applicant has registered as a new tenant and is awaiting your review.','registration','http://127.0.0.1:8000/admin/pending-tenants/20',NULL,1,'2026-10-04 08:42:52','2026-10-04 08:42:53'),(78,1,'New Maintenance Request','Tenant Jan Marinelle reported [Plumbing]: \'Faucet handle loose\' for Room 103.','maintenance','http://127.0.0.1:8000/admin/maintenance/18','http://127.0.0.1:8000/admin/maintenance/18',1,'2026-10-04 08:42:53','2026-10-04 08:42:53'),(87,1,'New Tenant Registration Awaiting Approval','Test Applicant has registered as a new tenant and is awaiting your review.','registration','http://127.0.0.1:8000/admin/pending-tenants/22',NULL,1,'2026-10-04 09:04:57','2026-10-04 09:08:45'),(90,1,'New Maintenance Request','Tenant Jan Marinelle reported [Plumbing]: \'Faucet handle loose\' for Room 002.','maintenance','http://127.0.0.1:8000/admin/maintenance/20','http://127.0.0.1:8000/admin/maintenance/20',1,'2026-10-04 09:04:57','2026-10-04 09:04:58'),(95,1,'New Tenant Registration Awaiting Approval','Test Applicant has registered as a new tenant and is awaiting your review.','registration','http://127.0.0.1:8000/admin/pending-tenants/24',NULL,1,'2026-10-04 09:05:48','2026-10-04 09:07:18'),(98,1,'New Maintenance Request','Tenant Jan Marinelle reported [Plumbing]: \'Faucet handle loose\' for Room 002.','maintenance','http://127.0.0.1:8000/admin/maintenance/22','http://127.0.0.1:8000/admin/maintenance/22',1,'2026-10-04 09:05:49','2026-10-04 09:08:45'),(104,1,'New Tenant Registration Awaiting Approval','Test Applicant has registered as a new tenant and is awaiting your review.','registration','http://127.0.0.1:8000/admin/pending-tenants/26',NULL,1,'2026-10-04 09:07:17','2026-10-04 09:08:45'),(107,1,'New Maintenance Request','Tenant Jan Marinelle reported [Plumbing]: \'Faucet handle loose\' for Room 002.','maintenance','http://127.0.0.1:8000/admin/maintenance/24','http://127.0.0.1:8000/admin/maintenance/24',1,'2026-10-04 09:07:18','2026-10-04 09:07:18'),(113,1,'New Tenant Registration Awaiting Approval','Test Applicant has registered as a new tenant and is awaiting your review.','registration','http://127.0.0.1:8000/admin/pending-tenants/28',NULL,1,'2026-10-04 09:08:44','2026-10-04 09:08:45'),(116,1,'New Maintenance Request','Tenant Jan Marinelle reported [Plumbing]: \'Faucet handle loose\' for Room 002.','maintenance','http://127.0.0.1:8000/admin/maintenance/26','http://127.0.0.1:8000/admin/maintenance/26',1,'2026-10-04 09:08:44','2026-10-04 09:08:45'),(121,1,'New Tenant Registration Awaiting Approval','Test Applicant has registered as a new tenant and is awaiting your review.','registration','http://127.0.0.1:8000/admin/pending-tenants/30',NULL,1,'2026-10-04 09:09:03','2026-10-04 09:34:11'),(124,1,'New Maintenance Request','Tenant Jan Marinelle reported [Plumbing]: \'Faucet handle loose\' for Room 002.','maintenance','http://127.0.0.1:8000/admin/maintenance/28','http://127.0.0.1:8000/admin/maintenance/28',1,'2026-10-04 09:09:04','2026-10-04 09:34:11'),(129,1,'New Tenant Registration Awaiting Approval','Test Applicant has registered as a new tenant and is awaiting your review.','registration','http://127.0.0.1:8000/admin/pending-tenants/32',NULL,1,'2026-10-04 09:09:19','2026-10-04 09:09:20'),(132,1,'New Maintenance Request','Tenant Jan Marinelle reported [Plumbing]: \'Faucet handle loose\' for Room 002.','maintenance','http://127.0.0.1:8000/admin/maintenance/31','http://127.0.0.1:8000/admin/maintenance/31',1,'2026-10-04 09:09:19','2026-10-04 09:34:11'),(139,1,'New Tenant Registration Awaiting Approval','Kaye Castro has registered as a new tenant and is awaiting your review.','registration','http://127.0.0.1:8000/admin/pending-tenants/35',NULL,1,'2026-10-04 09:15:21','2026-10-04 09:34:11'),(141,1,'New GCash Payment Submitted','Tenant Kaye Castro submitted a GCash payment of ₱1,500.00 for October 2026 (Ref: 111111000000).','payment','http://127.0.0.1:8000/admin/payments/43','http://127.0.0.1:8000/admin/payments/43',1,'2026-10-04 09:30:37','2026-10-04 09:34:11'),(143,1,'New Tenant Registration Awaiting Approval','Test Applicant has registered as a new tenant and is awaiting your review.','registration','http://127.0.0.1:8000/admin/pending-tenants/36',NULL,1,'2026-10-04 09:50:30','2026-10-04 10:11:09'),(146,1,'New Maintenance Request','Tenant Jan Marinelle reported [Plumbing]: \'Faucet handle loose\' for Room 002.','maintenance','http://127.0.0.1:8000/admin/maintenance/34','http://127.0.0.1:8000/admin/maintenance/34',1,'2026-10-04 09:50:30','2026-10-04 09:50:31'),(152,1,'New Tenant Registration Awaiting Approval','Test Applicant has registered as a new tenant and is awaiting your review.','registration','http://127.0.0.1:8000/admin/pending-tenants/39',NULL,1,'2026-10-04 09:55:09','2026-10-04 10:11:09'),(155,1,'New Maintenance Request','Tenant Jan Marinelle reported [Plumbing]: \'Faucet handle loose\' for Room 002.','maintenance','http://127.0.0.1:8000/admin/maintenance/37','http://127.0.0.1:8000/admin/maintenance/37',1,'2026-10-04 09:55:09','2026-10-04 10:11:09'),(162,1,'New Tenant Registration Awaiting Approval','Britney Jae Lumod has registered as a new tenant and is awaiting your review.','registration','http://127.0.0.1:8000/admin/pending-tenants/43',NULL,1,'2026-10-04 10:37:30','2026-10-04 12:04:01'),(163,1,'New Tenant Registration Awaiting Approval','Test Applicant has registered as a new tenant and is awaiting your review.','registration','http://127.0.0.1:8000/admin/pending-tenants/44',NULL,1,'2026-10-04 11:31:13','2026-10-04 11:31:58'),(166,1,'New Maintenance Request','Tenant Jan Marinelle reported [Plumbing]: \'Faucet handle loose\' for Room 002.','maintenance','http://127.0.0.1:8000/admin/maintenance/40','http://127.0.0.1:8000/admin/maintenance/40',1,'2026-10-04 11:31:14','2026-10-04 12:04:01'),(171,1,'New Tenant Registration Awaiting Approval','Test Applicant has registered as a new tenant and is awaiting your review.','registration','http://127.0.0.1:8000/admin/pending-tenants/47',NULL,1,'2026-10-04 11:31:57','2026-10-04 11:32:59'),(174,1,'New Maintenance Request','Tenant Jan Marinelle reported [Plumbing]: \'Faucet handle loose\' for Room 002.','maintenance','http://127.0.0.1:8000/admin/maintenance/43','http://127.0.0.1:8000/admin/maintenance/43',1,'2026-10-04 11:31:57','2026-10-04 12:04:01'),(179,1,'New Tenant Registration Awaiting Approval','Test Applicant has registered as a new tenant and is awaiting your review.','registration','http://127.0.0.1:8000/admin/pending-tenants/50',NULL,1,'2026-10-04 11:32:58','2026-10-04 11:33:18'),(182,1,'New Maintenance Request','Tenant Jan Marinelle reported [Plumbing]: \'Faucet handle loose\' for Room 002.','maintenance','http://127.0.0.1:8000/admin/maintenance/46','http://127.0.0.1:8000/admin/maintenance/46',1,'2026-10-04 11:32:58','2026-10-04 12:04:01'),(187,1,'New Tenant Registration Awaiting Approval','Test Applicant has registered as a new tenant and is awaiting your review.','registration','http://127.0.0.1:8000/admin/pending-tenants/53',NULL,1,'2026-10-04 11:33:17','2026-10-04 11:33:54'),(189,1,'New Maintenance Request','Tenant Jan Marinelle reported [Plumbing]: \'Faucet handle loose\' for Room 002.','maintenance','http://127.0.0.1:8000/admin/maintenance/49','http://127.0.0.1:8000/admin/maintenance/49',1,'2026-10-04 11:33:18','2026-10-04 12:04:01'),(194,1,'New Tenant Registration Awaiting Approval','Test Applicant has registered as a new tenant and is awaiting your review.','registration','http://127.0.0.1:8000/admin/pending-tenants/56',NULL,1,'2026-10-04 11:33:53','2026-10-04 11:34:22'),(197,1,'New Maintenance Request','Tenant Jan Marinelle reported [Plumbing]: \'Faucet handle loose\' for Room 002.','maintenance','http://127.0.0.1:8000/admin/maintenance/52','http://127.0.0.1:8000/admin/maintenance/52',1,'2026-10-04 11:33:53','2026-10-04 12:04:01'),(202,1,'New Tenant Registration Awaiting Approval','Test Applicant has registered as a new tenant and is awaiting your review.','registration','http://127.0.0.1:8000/admin/pending-tenants/59',NULL,1,'2026-10-04 11:34:18','2026-10-04 11:48:52'),(205,1,'New Maintenance Request','Tenant Jan Marinelle reported [Plumbing]: \'Faucet handle loose\' for Room 002.','maintenance','http://127.0.0.1:8000/admin/maintenance/55','http://127.0.0.1:8000/admin/maintenance/55',1,'2026-10-04 11:34:19','2026-10-04 12:04:01'),(210,1,'New Tenant Registration Awaiting Approval','Test Applicant has registered as a new tenant and is awaiting your review.','registration','http://127.0.0.1:8000/admin/pending-tenants/62',NULL,1,'2026-10-04 11:48:51','2026-10-04 11:49:58'),(213,1,'New Maintenance Request','Tenant Jan Marinelle reported [Plumbing]: \'Faucet handle loose\' for Room 002.','maintenance','http://127.0.0.1:8000/admin/maintenance/58','http://127.0.0.1:8000/admin/maintenance/58',1,'2026-10-04 11:48:51','2026-10-04 12:04:01'),(218,1,'New Tenant Registration Awaiting Approval','Test Applicant has registered as a new tenant and is awaiting your review.','registration','http://127.0.0.1:8000/admin/pending-tenants/65',NULL,1,'2026-10-04 11:49:56','2026-10-04 11:50:20'),(221,1,'New Maintenance Request','Tenant Jan Marinelle reported [Plumbing]: \'Faucet handle loose\' for Room 002.','maintenance','http://127.0.0.1:8000/admin/maintenance/61','http://127.0.0.1:8000/admin/maintenance/61',1,'2026-10-04 11:49:57','2026-10-04 12:04:01'),(226,1,'New Tenant Registration Awaiting Approval','Test Applicant has registered as a new tenant and is awaiting your review.','registration','http://127.0.0.1:8000/admin/pending-tenants/68',NULL,1,'2026-10-04 11:50:18','2026-10-04 11:52:48'),(229,1,'New Maintenance Request','Tenant Jan Marinelle reported [Plumbing]: \'Faucet handle loose\' for Room 002.','maintenance','http://127.0.0.1:8000/admin/maintenance/64','http://127.0.0.1:8000/admin/maintenance/64',1,'2026-10-04 11:50:19','2026-10-04 12:04:01'),(234,1,'New Tenant Registration Awaiting Approval','Test Applicant has registered as a new tenant and is awaiting your review.','registration','http://127.0.0.1:8000/admin/pending-tenants/71',NULL,1,'2026-10-04 11:52:47','2026-10-04 11:55:29'),(237,1,'New Maintenance Request','Tenant Jan Marinelle reported [Plumbing]: \'Faucet handle loose\' for Room 002.','maintenance','http://127.0.0.1:8000/admin/maintenance/67','http://127.0.0.1:8000/admin/maintenance/67',1,'2026-10-04 11:52:47','2026-10-04 12:04:01'),(242,1,'New Tenant Registration Awaiting Approval','Test Applicant has registered as a new tenant and is awaiting your review.','registration','http://127.0.0.1:8000/admin/pending-tenants/74',NULL,1,'2026-10-04 11:55:27','2026-10-04 11:56:07'),(245,1,'New Maintenance Request','Tenant Jan Marinelle reported [Plumbing]: \'Faucet handle loose\' for Room 002.','maintenance','http://127.0.0.1:8000/admin/maintenance/70','http://127.0.0.1:8000/admin/maintenance/70',1,'2026-10-04 11:55:28','2026-10-04 12:04:01'),(250,1,'New Tenant Registration Awaiting Approval','Test Applicant has registered as a new tenant and is awaiting your review.','registration','http://127.0.0.1:8000/admin/pending-tenants/77',NULL,1,'2026-10-04 11:56:06','2026-10-04 12:04:01'),(253,1,'New Maintenance Request','Tenant Jan Marinelle reported [Plumbing]: \'Faucet handle loose\' for Room 002.','maintenance','http://127.0.0.1:8000/admin/maintenance/73','http://127.0.0.1:8000/admin/maintenance/73',1,'2026-10-04 11:56:07','2026-10-04 12:04:01'),(258,1,'New Tenant Registration Awaiting Approval','Test Applicant has registered as a new tenant and is awaiting your review.','registration','http://127.0.0.1:8000/admin/pending-tenants/80',NULL,1,'2026-10-04 12:19:47','2026-10-04 12:21:17'),(261,1,'New Maintenance Request','Tenant Jan Marinelle reported [Plumbing]: \'Faucet handle loose\' for Room 002.','maintenance','http://127.0.0.1:8000/admin/maintenance/76','http://127.0.0.1:8000/admin/maintenance/76',1,'2026-10-04 12:19:48','2026-10-04 12:19:48'),(267,1,'New Tenant Registration Awaiting Approval','Test Applicant has registered as a new tenant and is awaiting your review.','registration','http://127.0.0.1:8000/admin/pending-tenants/83',NULL,1,'2026-10-04 12:21:16','2026-10-04 12:21:17'),(270,1,'New Maintenance Request','Tenant Jan Marinelle reported [Plumbing]: \'Faucet handle loose\' for Room 002.','maintenance','http://127.0.0.1:8000/admin/maintenance/79','http://127.0.0.1:8000/admin/maintenance/79',1,'2026-10-04 12:21:16','2026-10-04 12:21:17'),(275,1,'New Tenant Registration Awaiting Approval','Test Applicant has registered as a new tenant and is awaiting your review.','registration','http://127.0.0.1:8000/admin/pending-tenants/86',NULL,1,'2026-10-04 12:26:38','2026-10-04 12:35:30'),(281,1,'New Tenant Registration Awaiting Approval','Test Applicant has registered as a new tenant and is awaiting your review.','registration','http://127.0.0.1:8000/admin/pending-tenants/89',NULL,1,'2026-10-04 12:34:48','2026-10-04 12:35:30'),(283,1,'New Tenant Registration Awaiting Approval','Jan Marinelle has registered as a new tenant and is awaiting your review.','registration','http://127.0.0.1:8000/admin/pending-tenants/92',NULL,1,'2026-10-04 12:38:56','2026-10-04 14:28:45'),(285,1,'New GCash Payment Submitted','Tenant Jan Marinelle submitted a GCash payment of ₱5,000.00 for October 2026 (Ref: 111111000000).','payment','http://127.0.0.1:8000/admin/payments/128','http://127.0.0.1:8000/admin/payments/128',1,'2026-10-04 12:41:56','2026-10-04 14:28:45'),(286,1,'New Room Request','Tenant Jan Marinelle has requested Room TR-90247.','room','http://127.0.0.1:8000/admin/room-requests','http://127.0.0.1:8000/admin/room-requests',1,'2026-10-04 12:48:07','2026-10-04 14:28:45'),(289,122,'Room Request Rejected','Your request for Room 002 was not approved. Remarks: Selected room is no longer available or request could not be accommodated.','danger','http://127.0.0.1:8000/tenant/rooms',NULL,0,'2026-10-04 13:21:57','2026-10-04 13:21:57'),(290,1,'New Tenant Registration Awaiting Approval','Test Applicant has registered as a new tenant and is awaiting your review.','registration','http://127.0.0.1:8000/admin/pending-tenants/95',NULL,1,'2026-10-04 14:15:06','2026-10-04 14:28:45'),(291,123,'Payment Recorded','Payment of ₱3,500.00 for November 2026 (CASH) has been officially recorded.','payment','http://127.0.0.1:8000/tenant/payments',NULL,1,'2026-10-04 14:15:08','2026-10-04 14:25:57'),(292,123,'GCash Payment Verified!','Your GCash payment of ₱3,500.00 for December 2026 (Ref: 1009988776655) was verified and approved.','success','http://127.0.0.1:8000/tenant/payments','http://127.0.0.1:8000/tenant/payments',1,'2026-10-04 14:15:08','2026-10-04 14:25:57'),(294,123,'Maintenance Request Resolved (#MNT-TEST-4503)','Your maintenance request \"Leaking Faucet in Bathroom\" has been resolved. Remarks: Replaced faucet washer and valve.','success','http://127.0.0.1:8000/tenant/maintenance/86',NULL,1,'2026-10-04 14:15:12','2026-10-04 14:25:57'),(295,123,'Maintenance Request Rejected (#MNT-TEST-4503)','Your maintenance request \"Leaking Faucet in Bathroom\" was rejected. Reason: Item is tenant-provided appliance not covered by boarding house.','danger','http://127.0.0.1:8000/tenant/maintenance/86',NULL,1,'2026-10-04 14:15:12','2026-10-04 14:25:57'),(296,1,'New Tenant Registration Awaiting Approval','Test Applicant has registered as a new tenant and is awaiting your review.','registration','http://127.0.0.1:8000/admin/pending-tenants/99',NULL,1,'2026-10-04 14:15:40','2026-10-04 14:28:45'),(297,123,'Payment Recorded','Payment of ₱3,500.00 for November 2026 (CASH) has been officially recorded.','payment','http://127.0.0.1:8000/tenant/payments',NULL,1,'2026-10-04 14:15:40','2026-10-04 14:25:57'),(298,123,'GCash Payment Verified!','Your GCash payment of ₱3,500.00 for December 2026 (Ref: 1009988776655) was verified and approved.','success','http://127.0.0.1:8000/tenant/payments','http://127.0.0.1:8000/tenant/payments',1,'2026-10-04 14:15:40','2026-10-04 14:25:57'),(299,123,'Maintenance In Progress (#MNT-TEST-4503)','Your maintenance request \"Leaking Faucet in Bathroom\" is now in progress. Assigned technician: Kuya Bert (Electrician). Note: Scheduled inspection today.','info','http://127.0.0.1:8000/tenant/maintenance/86',NULL,1,'2026-10-04 14:15:42','2026-10-04 14:25:57'),(301,123,'Maintenance Request Resolved (#MNT-TEST-2946)','Your maintenance request \"Leaking Faucet in Bathroom\" has been resolved. Remarks: Replaced faucet washer and valve.','success','http://127.0.0.1:8000/tenant/maintenance/88',NULL,1,'2026-10-04 14:15:43','2026-10-04 14:25:57'),(302,123,'Maintenance Request Rejected (#MNT-TEST-2946)','Your maintenance request \"Leaking Faucet in Bathroom\" was rejected. Reason: Item is tenant-provided appliance not covered by boarding house.','danger','http://127.0.0.1:8000/tenant/maintenance/88',NULL,1,'2026-10-04 14:15:43','2026-10-04 14:25:57'),(304,132,'Room Request Rejected','Your request for Room 002 was not approved. Remarks: Selected room is no longer available or request could not be accommodated.','danger','http://127.0.0.1:8000/tenant/rooms',NULL,0,'2026-10-04 14:26:43','2026-10-04 14:26:43'),(305,1,'New Room Request','Tenant Jan Marinelle has requested Room TR-99759.','room','http://127.0.0.1:8000/admin/room-requests','http://127.0.0.1:8000/admin/room-requests',0,'2026-10-07 05:01:42','2026-10-07 05:03:05'),(306,123,'Room Request Approved!','Your request for Room TR-99759 has been approved. Move-in date: Oct 07, 2026.','success','http://127.0.0.1:8000/tenant/my-room',NULL,0,'2026-10-07 05:02:06','2026-10-07 05:02:06'),(307,1,'New Tenant Registration Awaiting Approval','Kaye Castro has registered as a new tenant and is awaiting your review.','registration','http://127.0.0.1:8000/admin/pending-tenants/102',NULL,0,'2026-10-07 05:13:42','2026-10-07 05:13:42'),(308,138,'Your Account Has Been Approved!','Welcome to the boarding house! Your account has been approved and assigned to Room TR-90247. Move-in date: Oct 07, 2026.','success','http://127.0.0.1:8000/tenant/dashboard',NULL,0,'2026-10-07 05:15:31','2026-10-07 05:15:31'),(309,1,'New Cash Payment Submitted','Tenant Kaye Castro recorded a Cash payment of ₱3,500.00 for October 2026 awaiting landlord verification.','payment','http://127.0.0.1:8000/admin/payments/145','http://127.0.0.1:8000/admin/payments/145',0,'2026-10-07 05:27:08','2026-10-07 05:27:08');
/*!40000 ALTER TABLE `app_notifications` ENABLE KEYS */;
DROP TABLE IF EXISTS `maintenance_requests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `maintenance_requests` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `request_code` varchar(50) DEFAULT NULL,
  `tenant_id` bigint(20) unsigned NOT NULL,
  `room_id` bigint(20) unsigned NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `category` varchar(100) NOT NULL DEFAULT 'General',
  `priority` varchar(20) NOT NULL DEFAULT 'medium',
  `status` varchar(30) NOT NULL DEFAULT 'pending',
  `preferred_date` date DEFAULT NULL,
  `attachment_path` varchar(255) DEFAULT NULL,
  `assigned_to` varchar(255) DEFAULT NULL,
  `target_date` date DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `admin_remarks` text DEFAULT NULL,
  `resolved_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `maintenance_requests_request_code_unique` (`request_code`),
  KEY `maintenance_requests_tenant_id_foreign` (`tenant_id`),
  KEY `maintenance_requests_room_id_foreign` (`room_id`),
  CONSTRAINT `maintenance_requests_room_id_foreign` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`) ON DELETE CASCADE,
  CONSTRAINT `maintenance_requests_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=90 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40000 ALTER TABLE `maintenance_requests` DISABLE KEYS */;
/*!40000 ALTER TABLE `maintenance_requests` ENABLE KEYS */;
DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'2026_01_01_000001_create_users_table',1),(2,'2026_01_01_000002_create_rooms_table',1),(3,'2026_01_01_000003_create_room_images_table',1),(4,'2026_01_01_000004_create_tenants_table',1),(5,'2026_01_01_000005_create_room_requests_table',1),(6,'2026_01_01_000006_create_payments_table',1),(7,'2026_01_01_000007_create_maintenance_requests_table',1),(8,'2026_01_01_000008_create_app_notifications_table',1),(9,'2026_01_01_000009_add_profile_picture_to_users_and_tenants_table',2),(10,'2026_01_01_000010_create_settings_table',2),(11,'2026_01_01_000011_create_payment_edit_histories_table',3);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40000 ALTER TABLE `password_reset_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_reset_tokens` ENABLE KEYS */;
DROP TABLE IF EXISTS `payment_edit_histories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `payment_edit_histories` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `payment_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `editor_name` varchar(255) DEFAULT NULL,
  `reason` text NOT NULL,
  `changed_fields` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`changed_fields`)),
  `old_values` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`old_values`)),
  `new_values` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`new_values`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `payment_edit_histories_payment_id_foreign` (`payment_id`),
  KEY `payment_edit_histories_user_id_foreign` (`user_id`),
  CONSTRAINT `payment_edit_histories_payment_id_foreign` FOREIGN KEY (`payment_id`) REFERENCES `payments` (`id`) ON DELETE CASCADE,
  CONSTRAINT `payment_edit_histories_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40000 ALTER TABLE `payment_edit_histories` DISABLE KEYS */;
INSERT INTO `payment_edit_histories` VALUES (1,132,1,'Copy Paste','Incorrect amount entered','{\"amount\":{\"field_label\":\"Amount\",\"old\":\"\\u20b13,500.00\",\"new\":\"\\u20b13,400.00\"}}','{\"amount\":3500,\"billing_month\":12,\"billing_year\":2026,\"rental_period\":\"December 2026\",\"payment_method\":\"gcash\",\"payment_date\":\"2026-12-01\",\"status\":\"verified\",\"gcash_reference\":\"1009988776655\",\"remarks\":null}','{\"amount\":3400,\"billing_month\":12,\"billing_year\":2026,\"rental_period\":\"December 2026\",\"payment_method\":\"gcash\",\"payment_date\":\"2026-12-01\",\"status\":\"verified\",\"gcash_reference\":\"1009988776655\",\"remarks\":null}','2026-10-07 04:41:48','2026-10-07 04:41:48');
/*!40000 ALTER TABLE `payment_edit_histories` ENABLE KEYS */;
DROP TABLE IF EXISTS `payments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `payments` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `payment_code` varchar(50) DEFAULT NULL,
  `tenant_id` bigint(20) unsigned NOT NULL,
  `room_id` bigint(20) unsigned NOT NULL,
  `billing_month` tinyint(3) unsigned NOT NULL,
  `billing_year` smallint(5) unsigned NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_method` enum('cash','gcash') NOT NULL,
  `payment_date` date NOT NULL,
  `gcash_reference` varchar(100) DEFAULT NULL,
  `receipt_path` varchar(255) DEFAULT NULL,
  `status` enum('pending','paid','verified','rejected','partial') NOT NULL DEFAULT 'pending',
  `is_edited` tinyint(1) NOT NULL DEFAULT 0,
  `notes` text DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `rejection_reason` text DEFAULT NULL,
  `received_by` bigint(20) unsigned DEFAULT NULL,
  `verified_by` bigint(20) unsigned DEFAULT NULL,
  `verified_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `payments_payment_code_unique` (`payment_code`),
  KEY `payments_tenant_id_foreign` (`tenant_id`),
  KEY `payments_room_id_foreign` (`room_id`),
  KEY `payments_received_by_foreign` (`received_by`),
  KEY `payments_verified_by_foreign` (`verified_by`),
  CONSTRAINT `payments_received_by_foreign` FOREIGN KEY (`received_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `payments_room_id_foreign` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`) ON DELETE CASCADE,
  CONSTRAINT `payments_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE,
  CONSTRAINT `payments_verified_by_foreign` FOREIGN KEY (`verified_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=146 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40000 ALTER TABLE `payments` DISABLE KEYS */;
INSERT INTO `payments` VALUES (132,NULL,92,85,12,2026,3400.00,'gcash','2026-12-01','1009988776655',NULL,'verified',1,NULL,NULL,NULL,NULL,1,'2026-10-04 14:15:08','2026-10-04 14:15:08','2026-10-07 04:41:48'),(145,'PAY-2026-00133',102,85,10,2026,3500.00,'cash','2026-10-07',NULL,NULL,'pending',0,NULL,NULL,NULL,NULL,NULL,NULL,'2026-10-07 05:27:08','2026-10-07 05:27:08');
/*!40000 ALTER TABLE `payments` ENABLE KEYS */;
DROP TABLE IF EXISTS `room_images`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `room_images` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `room_id` bigint(20) unsigned NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `is_primary` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `room_images_room_id_foreign` (`room_id`),
  CONSTRAINT `room_images_room_id_foreign` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40000 ALTER TABLE `room_images` DISABLE KEYS */;
INSERT INTO `room_images` VALUES (21,10,'rooms/5wKtpar9VvXYi8CtPGCjOT5mKb0dFId3uHDmTLh0.jpg',1,'2026-10-04 07:20:35','2026-10-04 07:20:35');
/*!40000 ALTER TABLE `room_images` ENABLE KEYS */;
DROP TABLE IF EXISTS `room_requests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `room_requests` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `tenant_id` bigint(20) unsigned DEFAULT NULL,
  `room_id` bigint(20) unsigned NOT NULL,
  `preferred_move_in_date` date DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `status` enum('pending','approved','rejected','cancelled') NOT NULL DEFAULT 'pending',
  `admin_remarks` text DEFAULT NULL,
  `approved_by` bigint(20) unsigned DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `room_requests_user_id_foreign` (`user_id`),
  KEY `room_requests_tenant_id_foreign` (`tenant_id`),
  KEY `room_requests_room_id_foreign` (`room_id`),
  KEY `room_requests_approved_by_foreign` (`approved_by`),
  CONSTRAINT `room_requests_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `room_requests_room_id_foreign` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`) ON DELETE CASCADE,
  CONSTRAINT `room_requests_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE,
  CONSTRAINT `room_requests_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=64 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40000 ALTER TABLE `room_requests` DISABLE KEYS */;
INSERT INTO `room_requests` VALUES (56,123,NULL,85,'2026-10-04',NULL,'approved','Room request approved and assigned to Room TR-90247',1,'2026-10-04 12:49:01','2026-10-04 12:48:07','2026-10-04 12:49:01'),(62,123,NULL,84,'2026-10-07',NULL,'approved','Room request approved and assigned to Room TR-99759',1,'2026-10-07 05:02:06','2026-10-07 05:01:42','2026-10-07 05:02:06'),(63,NULL,102,85,NULL,NULL,'approved','Approved and assigned to Room TR-90247',1,'2026-10-07 05:15:31','2026-10-07 05:13:42','2026-10-07 05:15:31');
/*!40000 ALTER TABLE `room_requests` ENABLE KEYS */;
DROP TABLE IF EXISTS `rooms`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rooms` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `room_number` varchar(50) NOT NULL,
  `room_name` varchar(100) DEFAULT NULL,
  `room_type` varchar(100) NOT NULL,
  `capacity` int(10) unsigned NOT NULL DEFAULT 1,
  `monthly_rent` decimal(10,2) NOT NULL,
  `floor` varchar(50) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `amenities` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`amenities`)),
  `manual_available` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `rooms_room_number_unique` (`room_number`)
) ENGINE=InnoDB AUTO_INCREMENT=92 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40000 ALTER TABLE `rooms` DISABLE KEYS */;
INSERT INTO `rooms` VALUES (10,'002','Deluxe Suite','Double Room',5,5000.00,'2nd',NULL,'[\"Air Conditioner\",\"Private Bathroom\",\"Bed Frame\",\"Closet\",\"Wi-Fi Access\",\"Study Desk\",\"Water Heater\"]',1,'2026-10-04 07:20:35','2026-10-04 07:20:35'),(84,'TR-99759',NULL,'Single Room',1,3000.00,'2',NULL,'[]',1,'2026-10-04 12:45:54','2026-10-07 04:47:40'),(85,'TR-90247',NULL,'Single',1,3500.00,'2',NULL,NULL,1,'2026-10-04 12:45:54','2026-10-04 12:45:54');
/*!40000 ALTER TABLE `rooms` ENABLE KEYS */;
DROP TABLE IF EXISTS `settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `settings` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `key` varchar(255) NOT NULL,
  `value` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `settings_key_unique` (`key`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40000 ALTER TABLE `settings` DISABLE KEYS */;
INSERT INTO `settings` VALUES (1,'gcash_name','IBHMS','2026-10-04 08:13:11','2026-10-07 04:41:16'),(2,'gcash_number','09928021194','2026-10-04 08:13:11','2026-10-07 04:41:16'),(3,'gcash_qr_path','settings/mIWFw8rgvFtg2yxQCQPzJ60xbIKLZ8exunIejjza.png','2026-10-04 08:13:11','2026-10-07 04:37:25');
/*!40000 ALTER TABLE `settings` ENABLE KEYS */;
DROP TABLE IF EXISTS `tenants`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tenants` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `room_id` bigint(20) unsigned DEFAULT NULL,
  `tenant_code` varchar(50) DEFAULT NULL,
  `full_name` varchar(255) DEFAULT NULL,
  `profile_picture` varchar(255) DEFAULT NULL,
  `contact_number` varchar(50) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `gender` varchar(20) DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `nationality` varchar(50) NOT NULL DEFAULT 'Filipino',
  `emergency_contact` varchar(255) DEFAULT NULL,
  `emergency_contact_name` varchar(255) DEFAULT NULL,
  `emergency_contact_number` varchar(255) DEFAULT NULL,
  `move_in_date` date DEFAULT NULL,
  `move_out_date` date DEFAULT NULL,
  `status` enum('pending','active','inactive','moved_out') NOT NULL DEFAULT 'pending',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `tenants_tenant_code_unique` (`tenant_code`),
  KEY `tenants_user_id_foreign` (`user_id`),
  KEY `tenants_room_id_foreign` (`room_id`),
  CONSTRAINT `tenants_room_id_foreign` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`) ON DELETE SET NULL,
  CONSTRAINT `tenants_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=103 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40000 ALTER TABLE `tenants` DISABLE KEYS */;
INSERT INTO `tenants` VALUES (92,123,84,'TEN-2026-0091','Jan Marinelle','avatars/2cpz1SH3ZyyCxyyFXB0djzAPcX1SlrDdnNzW8WRJ.png','09358343056','P5 NORTH POBLACION MARAMAG BUKIDNON','Female','2004-01-08','Filipino','Joseph Auditor (09358343056)','Joseph Auditor','09358343056','2026-10-07',NULL,'active',NULL,'2026-10-04 12:38:56','2026-10-07 05:02:06'),(102,138,85,'TEN-2026-0093','Kaye Castro',NULL,'09358343056','P5 NORTH POBLACION MARAMAG BUKIDNON','Female','2005-01-08','Filipino',NULL,NULL,NULL,'2026-10-07',NULL,'active',NULL,'2026-10-07 05:13:42','2026-10-07 05:15:31');
/*!40000 ALTER TABLE `tenants` ENABLE KEYS */;
DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','tenant') NOT NULL DEFAULT 'tenant',
  `account_status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `rejection_reason` text DEFAULT NULL,
  `contact_number` varchar(50) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `gender` varchar(20) DEFAULT NULL,
  `profile_picture` varchar(255) DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=139 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Copy Paste','admin@gmail.com',NULL,'$2y$12$jbMzlRnyCSWYMhLhba7rc.5vKa0Ls4NGBsL9ME9p0gkyc5F/XhRS2','admin','approved',NULL,'09358343056','P-2B Sayre Highway, Panadtalan, Maramag, Bukidnon','female','avatars/1Xy6lACgvdmuWSDIPrO2a4Gyp6v7EtLDNhJRUiJQ.webp','1982-05-15','kNcQhptvielEjedNt9n9MfbYWoix6iu3QxKQdb3mxmxLWvFrfuUXg4E8lJB7','2026-10-04 06:03:06','2026-10-04 14:27:56'),(122,'Room Req Applicant','roomreq_6ac247e99fdc3@example.com',NULL,'$2y$04$AmZHSKqKOT4ETkOUYT/eNOX0naWylEE303FH5cwXQMYpWLGLd4bfq','tenant','approved',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-10-04 12:34:49','2026-10-04 12:34:49'),(123,'Jan Marinelle','janmarinellea@gmail.com',NULL,'$2y$12$2EMNJc1I2HNiFJYC1kXIEu3buPi7iwbMZwh5rwebzbFGP5Jet58v.','tenant','approved',NULL,'09358343056','P5 NORTH POBLACION MARAMAG BUKIDNON',NULL,'avatars/2cpz1SH3ZyyCxyyFXB0djzAPcX1SlrDdnNzW8WRJ.png',NULL,NULL,'2026-10-04 12:38:56','2026-10-07 04:49:02'),(132,'Room Req Applicant','roomreq_6ac25f713353b@example.com',NULL,'$2y$04$c7S1wTqhxotmYt6wnDtIn.zu/1Qjgv8JMbmsdcdhqTP5djYXMW/uq','tenant','approved',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-10-04 14:15:13','2026-10-04 14:15:13'),(138,'Kaye Castro','digitalmarketing@gmail.com',NULL,'$2y$12$vL2wRR4n3JXmohYAdKQ2tun.ehNi57GLp8qUdkkYFcTYGiof0zNea','tenant','approved',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-10-07 05:13:42','2026-10-07 05:15:31');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

