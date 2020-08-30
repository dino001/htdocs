/*
SQLyog Ultimate v10.00 Beta1
MySQL - 5.1.41 : Database - quickdict_db
*********************************************************************
*/


/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/`quickdict_db` /*!40100 DEFAULT CHARACTER SET utf8 COLLATE utf8_bin */;

USE `quickdict_db`;

/*Table structure for table `fullword` */

DROP TABLE IF EXISTS `fullword`;

CREATE TABLE `fullword` (
  `fullword_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `word` varchar(100) COLLATE utf8_bin NOT NULL,
  `word_notone` varchar(100) COLLATE utf8_bin NOT NULL,
  `word_lowercase` varchar(100) COLLATE utf8_bin NOT NULL,
  `word_type` int(11) DEFAULT NULL,
  `count_word` int(11) DEFAULT NULL,
  `meaning` varchar(5000) COLLATE utf8_bin DEFAULT NULL,
  `is_propernoun` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`fullword_id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

/*Table structure for table `fullword_oneword` */

DROP TABLE IF EXISTS `fullword_oneword`;

CREATE TABLE `fullword_oneword` (
  `fullword_id` int(11) NOT NULL,
  `oneword_id` int(11) NOT NULL,
  `is_lastword` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`fullword_id`,`oneword_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

/*Table structure for table `oneword` */

DROP TABLE IF EXISTS `oneword`;

-- Break down each full word into multiple onewords with pronunciations
CREATE TABLE `oneword` (
  `oneword_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `word` varchar(10) COLLATE utf8_bin NOT NULL,
  `word_notone` varchar(10) COLLATE utf8_bin DEFAULT NULL,
  `first_consonant` varchar(10) COLLATE utf8_bin DEFAULT NULL,
  `syllable` varchar(10) COLLATE utf8_bin DEFAULT NULL,
  `tone` int(11) DEFAULT NULL,
  PRIMARY KEY (`oneword_id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
