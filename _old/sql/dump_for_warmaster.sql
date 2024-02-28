-- MySQL dump 10.16  Distrib 10.2.0-MariaDB, for Win64 (AMD64)
--
-- Host: 127.0.0.1    Database: warmaster1
-- ------------------------------------------------------
-- Server version	10.2.0-MariaDB

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
-- Table structure for table `equipment`
--

DROP TABLE IF EXISTS `equipment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `equipment` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `i_user` int(11) NOT NULL,
  `i_item` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `equipment`
--

/*!40000 ALTER TABLE `equipment` DISABLE KEYS */;
/*!40000 ALTER TABLE `equipment` ENABLE KEYS */;

--
-- Table structure for table `game_journal`
--

DROP TABLE IF EXISTS `game_journal`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `game_journal` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `i_user` int(11) NOT NULL,
  `message` varchar(555) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=50 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `game_journal`
--

/*!40000 ALTER TABLE `game_journal` DISABLE KEYS */;
INSERT INTO `game_journal` (`id`, `i_user`, `message`) VALUES (49,8,'<ul class=\"Horinis\"><li><span class=\"QuestTitle\">Хоринис</span><br> - Чертов охранник содрал с меня 200 золотых, чтобы я мог попасть в город, нужно искать работу</li></ul>');
/*!40000 ALTER TABLE `game_journal` ENABLE KEYS */;

--
-- Table structure for table `hero_info`
--

DROP TABLE IF EXISTS `hero_info`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `hero_info` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `i_user` int(11) NOT NULL,
  `gold` bigint(20) NOT NULL,
  `stage` int(11) NOT NULL,
  `attack` int(11) NOT NULL,
  `armor` int(11) NOT NULL,
  `health` int(11) NOT NULL,
  `power` int(11) NOT NULL,
  `critical` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `hero_info`
--

/*!40000 ALTER TABLE `hero_info` DISABLE KEYS */;
INSERT INTO `hero_info` (`id`, `i_user`, `gold`, `stage`, `attack`, `armor`, `health`, `power`, `critical`) VALUES (3,8,200,1,10,0,100,0,20);
/*!40000 ALTER TABLE `hero_info` ENABLE KEYS */;

--
-- Table structure for table `inventory`
--

DROP TABLE IF EXISTS `inventory`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `inventory` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `i_user` int(11) NOT NULL,
  `i_item` int(11) NOT NULL,
  `count` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=54 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `inventory`
--

/*!40000 ALTER TABLE `inventory` DISABLE KEYS */;
/*!40000 ALTER TABLE `inventory` ENABLE KEYS */;

--
-- Table structure for table `shop`
--

DROP TABLE IF EXISTS `shop`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shop` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(101) NOT NULL,
  `default_phrase` varchar(111) DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `shop`
--

/*!40000 ALTER TABLE `shop` DISABLE KEYS */;
INSERT INTO `shop` (`id`, `name`, `default_phrase`) VALUES (1,'Сантито (Торговец)','Торговец: Продаю по полной цене, выкупаю за половину :)'),(2,'Харальд (Кузнец)','Харальд: Лучшее оружие и броня!'),(3,'Лесная добыча','Леса Хориниса. Предметы, которые можно добыть с сражений'),(4,'Специфичные предметы','Карты и тдп');
/*!40000 ALTER TABLE `shop` ENABLE KEYS */;

--
-- Table structure for table `shop_item`
--

DROP TABLE IF EXISTS `shop_item`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shop_item` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `i_shop` int(11) NOT NULL,
  `name` varchar(55) NOT NULL,
  `i_item_type` int(11) NOT NULL DEFAULT '0',
  `value` int(11) NOT NULL,
  `cost` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `shop_item`
--

/*!40000 ALTER TABLE `shop_item` DISABLE KEYS */;
INSERT INTO `shop_item` (`id`, `i_shop`, `name`, `i_item_type`, `value`, `cost`) VALUES (1,1,'Дубинка',1,5,130),(2,1,'Полуторный меч',1,10,250),(3,1,'Двуручный меч',1,15,500),(4,1,'Охотничий нож',4,0,120),(5,1,'Кожаная броня',2,5,200),(6,1,'Пластинчатый доспех',2,10,600),(7,1,'Сырая сталь',3,0,110),(8,2,'Фростморн',1,25,800),(9,2,'Доспех Ворона',2,20,1000),(10,3,'Хвост крысы',5,0,50),(11,3,'Волчья шкура',6,0,100),(12,3,'Рог мракосира',7,0,400),(13,4,'Карта топей',8,0,100);
/*!40000 ALTER TABLE `shop_item` ENABLE KEYS */;

--
-- Table structure for table `shop_item_type`
--

DROP TABLE IF EXISTS `shop_item_type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shop_item_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(55) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `shop_item_type`
--

/*!40000 ALTER TABLE `shop_item_type` DISABLE KEYS */;
INSERT INTO `shop_item_type` (`id`, `name`) VALUES (1,'Атака'),(2,'Броня'),(3,'Сырье'),(4,'Охота'),(5,'Хвост крысы'),(6,'Волчья шкура'),(7,'Рог мракориса'),(8,'Карта');
/*!40000 ALTER TABLE `shop_item_type` ENABLE KEYS */;

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(55) NOT NULL,
  `userpassword` varchar(55) NOT NULL,
  `mail` varchar(55) NOT NULL,
  `i_group` int(11) NOT NULL,
  `dt_reg` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `dt_update` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user`
--

/*!40000 ALTER TABLE `user` DISABLE KEYS */;
INSERT INTO `user` (`id`, `username`, `userpassword`, `mail`, `i_group`, `dt_reg`, `dt_update`) VALUES (8,'kopetan','1111','iduso@mail.ru',2,'2019-06-05 17:22:40','2019-06-05 17:22:40');
/*!40000 ALTER TABLE `user` ENABLE KEYS */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2019-07-09 23:05:29
