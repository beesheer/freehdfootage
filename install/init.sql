-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               5.6.17 - MySQL Community Server (GPL)
-- Server OS:                    Win32
-- HeidiSQL version:             7.0.0.4053
-- Date/time:                    2015-12-21 13:28:00
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET FOREIGN_KEY_CHECKS=0 */;

-- Dumping structure for table freehdfootage.query
DROP TABLE IF EXISTS `query`;
CREATE TABLE IF NOT EXISTS `query` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `q` varchar(50) NOT NULL,
  `count` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `q` (`q`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

-- Dumping data for table freehdfootage.query: ~0 rows (approximately)
/*!40000 ALTER TABLE `query` DISABLE KEYS */;
/*!40000 ALTER TABLE `query` ENABLE KEYS */;


-- Dumping structure for table freehdfootage.tag
DROP TABLE IF EXISTS `tag`;
CREATE TABLE IF NOT EXISTS `tag` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `client_id` int(10) unsigned DEFAULT NULL,
  `parent_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `client_id` (`client_id`),
  KEY `parent_id` (`parent_id`),
  CONSTRAINT `FK_TagParent` FOREIGN KEY (`parent_id`) REFERENCES `tag` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `tag_ibfk_1` FOREIGN KEY (`client_id`) REFERENCES `client` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Dumping data for table freehdfootage.tag: ~0 rows (approximately)
/*!40000 ALTER TABLE `tag` DISABLE KEYS */;
/*!40000 ALTER TABLE `tag` ENABLE KEYS */;


-- Dumping structure for table freehdfootage.tag_entity
DROP TABLE IF EXISTS `tag_entity`;
CREATE TABLE IF NOT EXISTS `tag_entity` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `tag_id` int(10) unsigned NOT NULL,
  `entity_id` int(10) unsigned NOT NULL,
  `entity_type` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `tag_id` (`tag_id`),
  KEY `entity_id` (`entity_id`),
  KEY `entity_type` (`entity_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Dumping data for table freehdfootage.tag_entity: ~0 rows (approximately)
/*!40000 ALTER TABLE `tag_entity` DISABLE KEYS */;
/*!40000 ALTER TABLE `tag_entity` ENABLE KEYS */;


-- Dumping structure for table freehdfootage.user
DROP TABLE IF EXISTS `user`;
CREATE TABLE IF NOT EXISTS `user` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `surname` varchar(50) NOT NULL,
  `firstname` varchar(50) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(60) NOT NULL,
  `created_datetime` datetime NOT NULL,
  `modified_datetime` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- Dumping data for table freehdfootage.user: ~1 rows (approximately)
/*!40000 ALTER TABLE `user` DISABLE KEYS */;
INSERT INTO `user` (`id`, `surname`, `firstname`, `email`, `password`, `created_datetime`, `modified_datetime`) VALUES
	(1, 'Bin', 'Xu', 'beesheer@gmail.com', '81dc9bdb52d04dc20036dbd8313ed055', '2015-12-20 14:47:48', '2015-12-20 14:47:54');
/*!40000 ALTER TABLE `user` ENABLE KEYS */;


-- Dumping structure for table freehdfootage.user_system_session
DROP TABLE IF EXISTS `user_system_session`;
CREATE TABLE IF NOT EXISTS `user_system_session` (
  `id` char(32) NOT NULL DEFAULT '',
  `modified` int(11) DEFAULT NULL,
  `lifetime` int(11) DEFAULT NULL,
  `data` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Dumping data for table freehdfootage.user_system_session: 2 rows
/*!40000 ALTER TABLE `user_system_session` DISABLE KEYS */;
INSERT INTO `user_system_session` (`id`, `modified`, `lifetime`, `data`) VALUES
	('j5qsevli3bmo6t8l32m20u2vp0', 1450645850, 1800, ''),
	('aql8qcu9vkdcurheb7kqf024n6', 1450722433, 1800, 'Zend_Auth|a:1:{s:7:"storage";O:8:"stdClass":6:{s:2:"id";s:1:"1";s:7:"surname";s:3:"Bin";s:9:"firstname";s:2:"Xu";s:5:"email";s:18:"beesheer@gmail.com";s:16:"created_datetime";s:19:"2015-12-20 14:47:48";s:17:"modified_datetime";s:19:"2015-12-20 14:47:54";}}');
/*!40000 ALTER TABLE `user_system_session` ENABLE KEYS */;


-- Dumping structure for table freehdfootage.video
DROP TABLE IF EXISTS `video`;
CREATE TABLE IF NOT EXISTS `video` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `description` text NOT NULL,
  `created_datetime` datetime NOT NULL,
  `modified_datetime` datetime NOT NULL,
  `youtube_id` varchar(50) NOT NULL,
  `filepath` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8;

-- Dumping data for table freehdfootage.video: ~13 rows (approximately)
/*!40000 ALTER TABLE `video` DISABLE KEYS */;
/*!40000 ALTER TABLE `video` ENABLE KEYS */;
/*!40014 SET FOREIGN_KEY_CHECKS=1 */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
