-- MySQL dump 10.13  Distrib 8.0.16, for Linux (x86_64)
--
-- Host: localhost    Database: fondomerende
-- ------------------------------------------------------
-- Server version	8.0.16

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
 SET NAMES utf8mb4 ;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `actions`
--

DROP TABLE IF EXISTS `actions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
 SET character_set_client = utf8mb4 ;
CREATE TABLE `actions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(2) NOT NULL,
  `command_id` int(2) NOT NULL,
  `snack_id` int(2) DEFAULT NULL,
  `snack_quantity` int(2) DEFAULT NULL,
  `funds_amount` decimal(5,2) DEFAULT NULL,
  `inflow_id` int(11) DEFAULT NULL,
  `outflow_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `command_id` (`command_id`),
  KEY `snack_id` (`snack_id`),
  KEY `user_id` (`user_id`),
  KEY `actions_ibfk_4_idx` (`inflow_id`),
  KEY `actions_ibfk_5_idx` (`outflow_id`),
  CONSTRAINT `actions_ibfk_1` FOREIGN KEY (`command_id`) REFERENCES `commands` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `actions_ibfk_2` FOREIGN KEY (`snack_id`) REFERENCES `snacks` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `actions_ibfk_3` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `actions_ibfk_4` FOREIGN KEY (`inflow_id`) REFERENCES `inflows` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `actions_ibfk_5` FOREIGN KEY (`outflow_id`) REFERENCES `outflows` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `actions`
--

LOCK TABLES `actions` WRITE;
/*!40000 ALTER TABLE `actions` DISABLE KEYS */;
/*!40000 ALTER TABLE `actions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `commands`
--

DROP TABLE IF EXISTS `commands`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
 SET character_set_client = utf8mb4 ;
CREATE TABLE `commands` (
  `id` int(2) NOT NULL,
  `name` varchar(15) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `commands`
--

LOCK TABLES `commands` WRITE;
/*!40000 ALTER TABLE `commands` DISABLE KEYS */;
INSERT INTO `commands` VALUES (4,'add-snack'),(1,'add-user'),(6,'buy'),(3,'deposit'),(7,'eat'),(5,'edit-snack'),(2,'edit-user');
/*!40000 ALTER TABLE `commands` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `crates`
--

DROP TABLE IF EXISTS `crates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
 SET character_set_client = utf8mb4 ;
CREATE TABLE `crates` (
  `outflow_id` int(11) NOT NULL,
  `snack_id` int(2) NOT NULL,
  `snack_quantity` int(3) NOT NULL,
  `price_per_snack` decimal(5,2) NOT NULL,
  `expiration` date NOT NULL,
  PRIMARY KEY (`outflow_id`),
  KEY `snack_id` (`snack_id`),
  CONSTRAINT `crates_ibfk_1` FOREIGN KEY (`outflow_id`) REFERENCES `outflows` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `crates_ibfk_2` FOREIGN KEY (`snack_id`) REFERENCES `snacks` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `crates`
--

LOCK TABLES `crates` WRITE;
/*!40000 ALTER TABLE `crates` DISABLE KEYS */;
/*!40000 ALTER TABLE `crates` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `eaten`
--

DROP TABLE IF EXISTS `eaten`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
 SET character_set_client = utf8mb4 ;
CREATE TABLE `eaten` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `snack_id` int(2) NOT NULL,
  `user_id` int(2) NOT NULL,
  `quantity` int(11) DEFAULT '0',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `eaten_ibfk_1` (`snack_id`),
  KEY `eaten_ibfk_2` (`user_id`),
  CONSTRAINT `eaten_ibfk_1` FOREIGN KEY (`snack_id`) REFERENCES `snacks` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `eaten_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `eaten`
--

LOCK TABLES `eaten` WRITE;
/*!40000 ALTER TABLE `eaten` DISABLE KEYS */;
/*!40000 ALTER TABLE `eaten` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `edits`
--

DROP TABLE IF EXISTS `edits`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
 SET character_set_client = utf8mb4 ;
CREATE TABLE `edits` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `action_id` int(11) NOT NULL,
  `column_name` varchar(30) NOT NULL,
  `old_s_value` varchar(60) DEFAULT NULL,
  `new_s_value` varchar(60) DEFAULT NULL,
  `old_d_value` decimal(4,2) DEFAULT NULL,
  `new_d_value` decimal(4,2) DEFAULT NULL,
  `old_i_value` int(4) DEFAULT NULL,
  `new_i_value` int(4) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `action_id` (`action_id`),
  CONSTRAINT `edits_ibfk_1` FOREIGN KEY (`action_id`) REFERENCES `actions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `edits`
--

LOCK TABLES `edits` WRITE;
/*!40000 ALTER TABLE `edits` DISABLE KEYS */;
/*!40000 ALTER TABLE `edits` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `fund_funds`
--

DROP TABLE IF EXISTS `fund_funds`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
 SET character_set_client = utf8mb4 ;
CREATE TABLE `fund_funds` (
  `amount` decimal(5,2) NOT NULL DEFAULT '0.00',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY `total` (`amount`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `fund_funds`
--

LOCK TABLES `fund_funds` WRITE;
/*!40000 ALTER TABLE `fund_funds` DISABLE KEYS */;
INSERT INTO `fund_funds` VALUES (0.00,'2019-01-25 14:07:41');
/*!40000 ALTER TABLE `fund_funds` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `inflows`
--

DROP TABLE IF EXISTS `inflows`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
 SET character_set_client = utf8mb4 ;
CREATE TABLE `inflows` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(2) NOT NULL,
  `amount` decimal(4,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `inflows_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `inflows`
--

LOCK TABLES `inflows` WRITE;
/*!40000 ALTER TABLE `inflows` DISABLE KEYS */;
/*!40000 ALTER TABLE `inflows` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `outflows`
--

DROP TABLE IF EXISTS `outflows`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
 SET character_set_client = utf8mb4 ;
CREATE TABLE `outflows` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `amount` decimal(5,2) NOT NULL,
  `snack_id` int(2) NOT NULL,
  `quantity` int(3) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `snack_id` (`snack_id`),
  CONSTRAINT `outflows_ibfk_1` FOREIGN KEY (`snack_id`) REFERENCES `snacks` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `outflows`
--

LOCK TABLES `outflows` WRITE;
/*!40000 ALTER TABLE `outflows` DISABLE KEYS */;
/*!40000 ALTER TABLE `outflows` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `snacks`
--

DROP TABLE IF EXISTS `snacks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
 SET character_set_client = utf8mb4 ;
CREATE TABLE `snacks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(60) NOT NULL,
  `friendly_name` varchar(60) NOT NULL,
  `price` decimal(4,2) NOT NULL,
  `snacks_per_box` int(3) NOT NULL,
  `expiration_in_days` int(4) NOT NULL,
  `countable` tinyint(1) NOT NULL DEFAULT '1',
  `visible` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `snacks`
--

LOCK TABLES `snacks` WRITE;
/*!40000 ALTER TABLE `snacks` DISABLE KEYS */;
/*!40000 ALTER TABLE `snacks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `snacks_stock`
--

DROP TABLE IF EXISTS `snacks_stock`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
 SET character_set_client = utf8mb4 ;
CREATE TABLE `snacks_stock` (
  `snack_id` int(2) NOT NULL,
  `quantity` int(3) DEFAULT '0',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`snack_id`),
  UNIQUE KEY `snack_id` (`snack_id`),
  CONSTRAINT `snacks_stock_ibfk_1` FOREIGN KEY (`snack_id`) REFERENCES `snacks` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `snacks_stock`
--

LOCK TABLES `snacks_stock` WRITE;
/*!40000 ALTER TABLE `snacks_stock` DISABLE KEYS */;
/*!40000 ALTER TABLE `snacks_stock` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
 SET character_set_client = utf8mb4 ;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(30) NOT NULL,
  `password` varchar(255) NOT NULL,
  `friendly_name` varchar(60) NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '0',
  `admin` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users_funds`
--

DROP TABLE IF EXISTS `users_funds`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
 SET character_set_client = utf8mb4 ;
CREATE TABLE `users_funds` (
  `user_id` int(2) NOT NULL,
  `amount` decimal(4,2) NOT NULL DEFAULT '0.00',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `user_id` (`user_id`),
  CONSTRAINT `users_funds_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users_funds`
--

LOCK TABLES `users_funds` WRITE;
/*!40000 ALTER TABLE `users_funds` DISABLE KEYS */;
/*!40000 ALTER TABLE `users_funds` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2019-07-18 17:30:08
