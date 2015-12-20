-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               5.6.17 - MySQL Community Server (GPL)
-- Server OS:                    Win32
-- HeidiSQL version:             7.0.0.4053
-- Date/time:                    2015-12-17 16:10:44
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET FOREIGN_KEY_CHECKS=0 */;

-- Dumping structure for table stratus.app
DROP TABLE IF EXISTS `app`;
CREATE TABLE IF NOT EXISTS `app` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `client_id` int(10) unsigned NOT NULL,
  `name` varchar(50) NOT NULL,
  `app_key` varchar(50) NOT NULL,
  `description` text,
  `created_datetime` datetime NOT NULL,
  `modified_datetime` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `client_id` (`client_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table stratus.app_package
DROP TABLE IF EXISTS `app_package`;
CREATE TABLE IF NOT EXISTS `app_package` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `app_id` int(10) unsigned NOT NULL,
  `package_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_app_package_app` (`app_id`),
  KEY `FK_app_package_package` (`package_id`),
  CONSTRAINT `FK_app_package_app` FOREIGN KEY (`app_id`) REFERENCES `app` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_app_package_package` FOREIGN KEY (`package_id`) REFERENCES `package` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table stratus.app_user
DROP TABLE IF EXISTS `app_user`;
CREATE TABLE IF NOT EXISTS `app_user` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `app_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_app_user_app` (`app_id`),
  KEY `FK_app_user_user` (`user_id`),
  CONSTRAINT `FK_app_user_app` FOREIGN KEY (`app_id`) REFERENCES `app` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_app_user_user` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table stratus.article_body
DROP TABLE IF EXISTS `article_body`;
CREATE TABLE IF NOT EXISTS `article_body` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `body` mediumtext NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table stratus.article_category
DROP TABLE IF EXISTS `article_category`;
CREATE TABLE IF NOT EXISTS `article_category` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table stratus.article_instance
DROP TABLE IF EXISTS `article_instance`;
CREATE TABLE IF NOT EXISTS `article_instance` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `client_id` int(10) unsigned NOT NULL,
  `article_meta_data_id` int(10) unsigned NOT NULL,
  `summary` text,
  `article_body_id` varchar(255) DEFAULT NULL,
  `published_datetime` datetime DEFAULT NULL,
  `is_active` tinyint(1) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `client_id` (`client_id`),
  KEY `article_meta_data_id` (`article_meta_data_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table stratus.article_language
DROP TABLE IF EXISTS `article_language`;
CREATE TABLE IF NOT EXISTS `article_language` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `language_code` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table stratus.article_meta_data
DROP TABLE IF EXISTS `article_meta_data`;
CREATE TABLE IF NOT EXISTS `article_meta_data` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `featured_image_media_asset_id` int(10) unsigned DEFAULT NULL,
  `created_datetime` datetime DEFAULT NULL,
  `modified_datetime` datetime DEFAULT NULL,
  `published_datetime` datetime DEFAULT NULL,
  `category_id` int(10) unsigned DEFAULT NULL,
  `language_id` int(10) unsigned DEFAULT NULL,
  `region_id` int(10) unsigned DEFAULT NULL,
  `type_id` int(10) unsigned DEFAULT NULL,
  `state_id` int(10) unsigned DEFAULT NULL,
  `author` varchar(255) DEFAULT NULL,
  `promotion` text,
  `foot_notes` text,
  `summary` text,
  `article_body_id` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `article_category_id` (`category_id`),
  KEY `author` (`author`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table stratus.article_region
DROP TABLE IF EXISTS `article_region`;
CREATE TABLE IF NOT EXISTS `article_region` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table stratus.article_state
DROP TABLE IF EXISTS `article_state`;
CREATE TABLE IF NOT EXISTS `article_state` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table stratus.article_type
DROP TABLE IF EXISTS `article_type`;
CREATE TABLE IF NOT EXISTS `article_type` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table stratus.calendar
DROP TABLE IF EXISTS `calendar`;
CREATE TABLE IF NOT EXISTS `calendar` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `client_id` int(10) unsigned NOT NULL,
  `name` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `client_id` (`client_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table stratus.calendar_event
DROP TABLE IF EXISTS `calendar_event`;
CREATE TABLE IF NOT EXISTS `calendar_event` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `calendar_id` int(10) unsigned NOT NULL,
  `event_id` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_calendar_event_calendar` (`calendar_id`),
  KEY `FK_calendar_event_event` (`event_id`),
  CONSTRAINT `FK_calendar_event_calendar` FOREIGN KEY (`calendar_id`) REFERENCES `calendar` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_calendar_event_event` FOREIGN KEY (`event_id`) REFERENCES `event` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table stratus.client
DROP TABLE IF EXISTS `client`;
CREATE TABLE IF NOT EXISTS `client` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `parent_id` int(10) unsigned DEFAULT NULL,
  `type_id` tinyint(3) unsigned DEFAULT '1',
  `meta` text,
  `created_datetime` datetime DEFAULT NULL,
  `modified_datetime` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`),
  KEY `parent_id` (`parent_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table stratus.client_type
DROP TABLE IF EXISTS `client_type`;
CREATE TABLE IF NOT EXISTS `client_type` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table stratus.contact
DROP TABLE IF EXISTS `contact`;
CREATE TABLE IF NOT EXISTS `contact` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `owner_user_id` int(10) unsigned NOT NULL,
  `ref_user_id` int(10) unsigned DEFAULT NULL,
  `firstname` varchar(50) DEFAULT NULL,
  `surname` varchar(50) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `company` varchar(50) DEFAULT NULL,
  `title` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_contact_user` (`owner_user_id`),
  KEY `email` (`email`),
  CONSTRAINT `FK_contact_user` FOREIGN KEY (`owner_user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table stratus.data_tag
DROP TABLE IF EXISTS `data_tag`;
CREATE TABLE IF NOT EXISTS `data_tag` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) DEFAULT '0',
  `parent_id` int(10) unsigned DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Data exporting was unselected.


-- Dumping structure for table stratus.document
DROP TABLE IF EXISTS `document`;
CREATE TABLE IF NOT EXISTS `document` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `client_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `name` varchar(50) NOT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `created_datetime` datetime NOT NULL,
  `modified_datetime` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `client_id` (`client_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table stratus.event
DROP TABLE IF EXISTS `event`;
CREATE TABLE IF NOT EXISTS `event` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `client_id` int(10) unsigned NOT NULL,
  `description` varchar(255) NOT NULL,
  `location` varchar(255) DEFAULT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `start_time` time DEFAULT NULL,
  `end_time` time DEFAULT NULL,
  `is_all_day` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `is_repeat` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `repeat_unit_type_id` tinyint(3) unsigned DEFAULT NULL,
  `repeat_unit_number` int(5) unsigned DEFAULT NULL,
  `users` text,
  `series_id` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `client_id` (`client_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table stratus.event_exclusion
DROP TABLE IF EXISTS `event_exclusion`;
CREATE TABLE IF NOT EXISTS `event_exclusion` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `event_id` int(11) unsigned NOT NULL,
  `date` date NOT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_event_exclusion_event` (`event_id`),
  CONSTRAINT `FK_event_exclusion_event` FOREIGN KEY (`event_id`) REFERENCES `event` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table stratus.event_repeat_unit_type
DROP TABLE IF EXISTS `event_repeat_unit_type`;
CREATE TABLE IF NOT EXISTS `event_repeat_unit_type` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table stratus.form
DROP TABLE IF EXISTS `form`;
CREATE TABLE IF NOT EXISTS `form` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `page_id` int(10) unsigned DEFAULT NULL,
  `client_id` int(10) unsigned DEFAULT NULL,
  `name` varchar(50) NOT NULL,
  `response_id` int(10) unsigned DEFAULT NULL,
  `text` varchar(10000) DEFAULT NULL,
  `status` tinyint(1) unsigned NOT NULL,
  `data_tag_id` int(10) unsigned DEFAULT NULL,
  `created_datetime` datetime NOT NULL,
  `modified_datetime` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `response_id` (`response_id`),
  KEY `client_id` (`client_id`),
  KEY `page_id` (`page_id`),
  CONSTRAINT `client_id` FOREIGN KEY (`client_id`) REFERENCES `client` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `page_id` FOREIGN KEY (`page_id`) REFERENCES `page` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `response_id` FOREIGN KEY (`response_id`) REFERENCES `question_response` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table stratus.form_item
DROP TABLE IF EXISTS `form_item`;
CREATE TABLE IF NOT EXISTS `form_item` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `form_id` int(10) unsigned NOT NULL,
  `control_type` varchar(50) NOT NULL,
  `order` int(10) unsigned DEFAULT NULL,
  `data` text,
  `key` varchar(50) NOT NULL,
  `min_value` int(10) DEFAULT NULL,
  `max_value` int(10) DEFAULT NULL,
  `text` varchar(10000) NOT NULL,
  `media_item_id` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `form_id` (`form_id`),
  CONSTRAINT `form_id` FOREIGN KEY (`form_id`) REFERENCES `form` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table stratus.google_font
DROP TABLE IF EXISTS `google_font`;
CREATE TABLE IF NOT EXISTS `google_font` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(150) NOT NULL,
  `image_link` varchar(250) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table stratus.key_data
DROP TABLE IF EXISTS `key_data`;
CREATE TABLE IF NOT EXISTS `key_data` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `key` varchar(50) NOT NULL,
  `value` varchar(50) NOT NULL,
  `data_type` varchar(50) DEFAULT NULL,
  `date_time` datetime DEFAULT NULL,
  `data_tag_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `user_id` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table stratus.library
DROP TABLE IF EXISTS `library`;
CREATE TABLE IF NOT EXISTS `library` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `client_id` int(10) unsigned NOT NULL,
  `name` varchar(50) NOT NULL,
  `status` tinyint(1) unsigned NOT NULL,
  `type` varchar(50) DEFAULT NULL,
  `description` text,
  `created_datetime` datetime NOT NULL,
  `modified_datetime` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `client_id` (`client_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table stratus.library_page
DROP TABLE IF EXISTS `library_page`;
CREATE TABLE IF NOT EXISTS `library_page` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `library_id` int(10) unsigned NOT NULL,
  `page_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `library_id` (`library_id`),
  KEY `FK_library_page_page` (`page_id`),
  CONSTRAINT `FK_library_page_library` FOREIGN KEY (`library_id`) REFERENCES `library` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_library_page_page` FOREIGN KEY (`page_id`) REFERENCES `page` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table stratus.media_asset
DROP TABLE IF EXISTS `media_asset`;
CREATE TABLE IF NOT EXISTS `media_asset` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `client_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `usage_type` enum('page_authoring','document') NOT NULL DEFAULT 'page_authoring',
  `name` varchar(50) NOT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `thumb_path` varchar(255) DEFAULT NULL,
  `cloud_file_container` varchar(50) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `created_datetime` datetime NOT NULL,
  `modified_datetime` datetime DEFAULT NULL,
  `external_link` varchar(32) DEFAULT NULL,
  `client_visibility` tinyint(1) NOT NULL DEFAULT '1',
  `caption` varchar(255) DEFAULT NULL,
  `alternative_text` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `client_id` (`client_id`),
  KEY `user_id` (`user_id`),
  KEY `usage_type` (`usage_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table stratus.media_asset_content
DROP TABLE IF EXISTS `media_asset_content`;
CREATE TABLE IF NOT EXISTS `media_asset_content` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `media_asset_id` int(10) unsigned NOT NULL,
  `version` varchar(50) DEFAULT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `cloud_file_container` varchar(50) DEFAULT NULL,
  `thumb_path` varchar(255) DEFAULT NULL,
  `created_datetime` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `media_asset_id` (`media_asset_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table stratus.package
DROP TABLE IF EXISTS `package`;
CREATE TABLE IF NOT EXISTS `package` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `client_id` int(10) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `nav_type` varchar(50) DEFAULT NULL,
  `nav_data` text,
  `status` tinyint(1) unsigned NOT NULL,
  `version` int(10) unsigned NOT NULL DEFAULT '1',
  `play_audio` tinyint(1) unsigned DEFAULT '0',
  `description` text,
  `media_asset_id` int(11) DEFAULT NULL,
  `created_datetime` datetime NOT NULL,
  `modified_datetime` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `client_id` (`client_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table stratus.package_library
DROP TABLE IF EXISTS `package_library`;
CREATE TABLE IF NOT EXISTS `package_library` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `package_id` int(10) unsigned NOT NULL,
  `library_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_package_library_package` (`package_id`),
  KEY `FK_package_library_library` (`library_id`),
  CONSTRAINT `FK_package_library_library` FOREIGN KEY (`library_id`) REFERENCES `library` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_package_library_package` FOREIGN KEY (`package_id`) REFERENCES `package` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table stratus.package_manifest
DROP TABLE IF EXISTS `package_manifest`;
CREATE TABLE IF NOT EXISTS `package_manifest` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `package_id` int(10) unsigned NOT NULL,
  `version` int(10) unsigned NOT NULL,
  `created_datetime` datetime NOT NULL,
  `data` mediumtext NOT NULL,
  PRIMARY KEY (`id`),
  KEY `package_id` (`package_id`),
  CONSTRAINT `FK_package_manifest_package` FOREIGN KEY (`package_id`) REFERENCES `package` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table stratus.package_title
DROP TABLE IF EXISTS `package_title`;
CREATE TABLE IF NOT EXISTS `package_title` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `package_id` int(10) unsigned NOT NULL,
  `title_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_package_title_package` (`package_id`),
  KEY `FK_package_title_title` (`title_id`),
  CONSTRAINT `FK_package_title_package` FOREIGN KEY (`package_id`) REFERENCES `package` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_package_title_title` FOREIGN KEY (`title_id`) REFERENCES `title` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table stratus.page
DROP TABLE IF EXISTS `page`;
CREATE TABLE IF NOT EXISTS `page` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `client_id` int(10) unsigned NOT NULL,
  `name` varchar(50) NOT NULL,
  `page_id` varchar(50) NOT NULL,
  `page_language` varchar(50) NOT NULL,
  `type` enum('static','template','question','survey','cefeedback') NOT NULL DEFAULT 'static',
  `status` tinyint(3) unsigned NOT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `cloud_file_container` varchar(255) DEFAULT NULL,
  `template_id` int(10) unsigned DEFAULT NULL,
  `version` int(10) unsigned DEFAULT NULL,
  `description` text,
  `internal_desc` text,
  `preview_image` varchar(255) DEFAULT NULL,
  `icon_image` varchar(255) DEFAULT NULL,
  `audio_url` varchar(255) DEFAULT NULL,
  `transcript` text,
  `navigation` text,
  `created_datetime` datetime NOT NULL,
  `modified_datetime` datetime DEFAULT NULL,
  `width` varchar(50) DEFAULT NULL,
  `height` varchar(50) DEFAULT NULL,
  `screenshot_type` tinyint(1) unsigned DEFAULT NULL,
  `screenshot_type_view` tinyint(1) unsigned DEFAULT NULL,
  `content_type` varchar(50) DEFAULT NULL,
  `editor_behaviors` text,
  `legal_page_id` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `client_id` (`client_id`),
  KEY `status` (`status`),
  KEY `type` (`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table stratus.page_group
DROP TABLE IF EXISTS `page_group`;
CREATE TABLE IF NOT EXISTS `page_group` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `page_group_id` int(10) unsigned NOT NULL,
  `page_id` int(10) unsigned NOT NULL,
  `order` tinyint(3) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `page_id` (`page_id`),
  KEY `page_group_id` (`page_group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table stratus.page_pdf_template
DROP TABLE IF EXISTS `page_pdf_template`;
CREATE TABLE IF NOT EXISTS `page_pdf_template` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `page_id` int(10) unsigned NOT NULL,
  `pdf_template_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_page_pdf_template_page` (`page_id`),
  KEY `FK_page_pdf_template_template` (`pdf_template_id`),
  CONSTRAINT `FK_page_pdf_template_page` FOREIGN KEY (`page_id`) REFERENCES `page` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_page_pdf_template_template` FOREIGN KEY (`pdf_template_id`) REFERENCES `pdf_template` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table stratus.page_question
DROP TABLE IF EXISTS `page_question`;
CREATE TABLE IF NOT EXISTS `page_question` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `question_id` int(10) unsigned NOT NULL,
  `page_id` int(10) unsigned NOT NULL,
  `survey_id` int(10) unsigned DEFAULT NULL,
  `order` tinyint(3) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_pagequestion_survey_id` (`survey_id`),
  KEY `fk_pagequestion_page_id` (`page_id`),
  KEY `fk_pagequestion_question_id` (`question_id`),
  CONSTRAINT `fk_pagequestion_page_id` FOREIGN KEY (`page_id`) REFERENCES `page` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_pagequestion_question_id` FOREIGN KEY (`question_id`) REFERENCES `question` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_pagequestion_survey_id` FOREIGN KEY (`survey_id`) REFERENCES `survey` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table stratus.page_status
DROP TABLE IF EXISTS `page_status`;
CREATE TABLE IF NOT EXISTS `page_status` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table stratus.page_template
DROP TABLE IF EXISTS `page_template`;
CREATE TABLE IF NOT EXISTS `page_template` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `client_id` int(10) unsigned NOT NULL,
  `name` varchar(50) NOT NULL,
  `template` text,
  `created_by_user_id` int(10) unsigned NOT NULL,
  `created_datetime` datetime NOT NULL,
  `modified_by_user_id` int(10) DEFAULT NULL,
  `modified_datetime` datetime DEFAULT NULL,
  `media_asset_id` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_page_template_client` (`client_id`),
  CONSTRAINT `FK_page_template_client` FOREIGN KEY (`client_id`) REFERENCES `client` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table stratus.page_template_block
DROP TABLE IF EXISTS `page_template_block`;
CREATE TABLE IF NOT EXISTS `page_template_block` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `page_id` int(10) unsigned NOT NULL,
  `element_id` varchar(50) NOT NULL,
  `element_html` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_page_template_block_page` (`page_id`),
  CONSTRAINT `FK_page_template_block_page` FOREIGN KEY (`page_id`) REFERENCES `page` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table stratus.page_version
DROP TABLE IF EXISTS `page_version`;
CREATE TABLE IF NOT EXISTS `page_version` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `page_id` int(10) unsigned NOT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `cloud_file_container` varchar(255) DEFAULT NULL,
  `version` int(10) unsigned DEFAULT NULL,
  `created_datetime` datetime NOT NULL,
  `is_active` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `page_id` (`page_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table stratus.pdf_template
DROP TABLE IF EXISTS `pdf_template`;
CREATE TABLE IF NOT EXISTS `pdf_template` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `client_id` int(10) unsigned NOT NULL,
  `name` varchar(50) NOT NULL,
  `template` text,
  `created_by_user_id` int(10) unsigned NOT NULL,
  `created_datetime` datetime NOT NULL,
  `modified_by_user_id` int(10) DEFAULT NULL,
  `modified_datetime` datetime DEFAULT NULL,
  `preview_file` varchar(255) DEFAULT NULL,
  `static_pdf` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_pdf_template_client` (`client_id`),
  CONSTRAINT `FK_pdf_template_client` FOREIGN KEY (`client_id`) REFERENCES `client` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table stratus.permission
DROP TABLE IF EXISTS `permission`;
CREATE TABLE IF NOT EXISTS `permission` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `label` varchar(50) NOT NULL,
  `description` text,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table stratus.promote_session
DROP TABLE IF EXISTS `promote_session`;
CREATE TABLE IF NOT EXISTS `promote_session` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `ukey` char(32) NOT NULL,
  `user_id` int(10) NOT NULL,
  `package_id` int(10) NOT NULL,
  `created_datetime` datetime NOT NULL,
  `ended_datetime` datetime DEFAULT NULL,
  `subject` varchar(50) NOT NULL,
  `invite_message` text,
  `start_datetime` bigint(13) unsigned DEFAULT NULL,
  `starting_title` int(10) unsigned DEFAULT NULL,
  `update_sequence` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `ukey` (`ukey`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table stratus.promote_session_contact
DROP TABLE IF EXISTS `promote_session_contact`;
CREATE TABLE IF NOT EXISTS `promote_session_contact` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `promote_session_id` int(10) unsigned NOT NULL,
  `contact_id` int(10) unsigned NOT NULL,
  `invite_key` char(32) NOT NULL,
  `status` tinyint(1) DEFAULT NULL,
  `is_online` tinyint(1) DEFAULT NULL,
  `status_update_datetime` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_promote_session_contact_session` (`promote_session_id`),
  KEY `FK_promote_session_contact_contact` (`contact_id`),
  CONSTRAINT `FK_promote_session_contact_contact` FOREIGN KEY (`contact_id`) REFERENCES `contact` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_promote_session_contact_session` FOREIGN KEY (`promote_session_id`) REFERENCES `promote_session` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table stratus.promote_session_event
DROP TABLE IF EXISTS `promote_session_event`;
CREATE TABLE IF NOT EXISTS `promote_session_event` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `time` int(11) NOT NULL DEFAULT '0',
  `message` varchar(255) NOT NULL,
  `type` enum('action','chat','connect','disconnect') NOT NULL,
  `user_type` enum('presenter','viewer') NOT NULL,
  `user_email` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='This table will store the messages sent to the nodejs server. Including all the events from all types of users.';

-- Data exporting was unselected.


-- Dumping structure for table stratus.promote_session_invite
DROP TABLE IF EXISTS `promote_session_invite`;
CREATE TABLE IF NOT EXISTS `promote_session_invite` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `session_id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `invite_key` char(32) NOT NULL,
  `accept_datetime` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `session_id` (`session_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table stratus.public_authentication_token
DROP TABLE IF EXISTS `public_authentication_token`;
CREATE TABLE IF NOT EXISTS `public_authentication_token` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `token` varchar(96) NOT NULL,
  `expires_on` datetime NOT NULL,
  `client_id` int(10) NOT NULL,
  `description` text,
  PRIMARY KEY (`id`),
  UNIQUE KEY `token` (`token`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table stratus.question
DROP TABLE IF EXISTS `question`;
CREATE TABLE IF NOT EXISTS `question` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `client_id` int(10) unsigned DEFAULT NULL,
  `survey_id` int(10) unsigned DEFAULT NULL,
  `type_id` int(10) unsigned NOT NULL DEFAULT '0',
  `question_id` varchar(50) NOT NULL,
  `question` varchar(2500) NOT NULL,
  `question_fr` varchar(3500) DEFAULT '',
  `value` tinytext NOT NULL COMMENT 'the correct answer expressed as 01 number',
  `enabled` tinyint(1) DEFAULT '1',
  `order` tinyint(2) DEFAULT '0' COMMENT 'order within quiz',
  `image` varchar(1000) DEFAULT '',
  `video` varchar(1000) DEFAULT '',
  `israndom` tinyint(1) DEFAULT '0',
  `response_scope_id` int(10) NOT NULL,
  `created_datetime` datetime NOT NULL,
  `modified_datetime` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_question_clientid` (`client_id`),
  KEY `fk_question_surveyid` (`survey_id`),
  CONSTRAINT `fk_question_clientid` FOREIGN KEY (`client_id`) REFERENCES `client` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_question_surveyid` FOREIGN KEY (`survey_id`) REFERENCES `survey` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table stratus.question_option
DROP TABLE IF EXISTS `question_option`;
CREATE TABLE IF NOT EXISTS `question_option` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `question_id` int(10) unsigned DEFAULT '0',
  `type_id` int(10) unsigned NOT NULL DEFAULT '0',
  `option` varchar(2500) NOT NULL,
  `option_fr` varchar(3000) NOT NULL DEFAULT '',
  `order` tinyint(2) NOT NULL DEFAULT '0',
  `image` varchar(500) NOT NULL DEFAULT '',
  `video` varchar(500) NOT NULL DEFAULT '',
  `nextquestion_id` int(10) NOT NULL DEFAULT '0',
  `prefix` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_opt_question_id` (`question_id`),
  CONSTRAINT `fk_opt_question_id` FOREIGN KEY (`question_id`) REFERENCES `question` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table stratus.question_option_tag
DROP TABLE IF EXISTS `question_option_tag`;
CREATE TABLE IF NOT EXISTS `question_option_tag` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `tag_id` int(11) NOT NULL,
  `question_option_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `question_option_id_2` (`question_option_id`,`tag_id`),
  KEY `question_option_id` (`question_option_id`),
  KEY `tag_id` (`tag_id`),
  CONSTRAINT `question_option_tag_ibfk_1` FOREIGN KEY (`question_option_id`) REFERENCES `question_option` (`id`),
  CONSTRAINT `question_option_tag_ibfk_2` FOREIGN KEY (`tag_id`) REFERENCES `tag` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Data exporting was unselected.


-- Dumping structure for table stratus.question_option_type
DROP TABLE IF EXISTS `question_option_type`;
CREATE TABLE IF NOT EXISTS `question_option_type` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `description` varchar(100) NOT NULL,
  `details` varchar(1000) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `description` (`description`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table stratus.question_response
DROP TABLE IF EXISTS `question_response`;
CREATE TABLE IF NOT EXISTS `question_response` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `question_id` int(10) unsigned DEFAULT '0',
  `order` tinyint(2) NOT NULL DEFAULT '0',
  `response` varchar(10000) NOT NULL,
  `response_fr` varchar(10000) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `fk_resp_question_id` (`question_id`),
  CONSTRAINT `fk_resp_question_id` FOREIGN KEY (`question_id`) REFERENCES `question` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table stratus.question_response_scope
DROP TABLE IF EXISTS `question_response_scope`;
CREATE TABLE IF NOT EXISTS `question_response_scope` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `description` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `description` (`description`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table stratus.question_tag
DROP TABLE IF EXISTS `question_tag`;
CREATE TABLE IF NOT EXISTS `question_tag` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `question_id` int(10) unsigned DEFAULT NULL,
  `tag_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `question_id` (`question_id`),
  KEY `tag_id` (`tag_id`),
  CONSTRAINT `question_tag_ibfk_1` FOREIGN KEY (`question_id`) REFERENCES `question` (`id`),
  CONSTRAINT `question_tag_ibfk_2` FOREIGN KEY (`tag_id`) REFERENCES `tag_child` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Data exporting was unselected.


-- Dumping structure for table stratus.question_type
DROP TABLE IF EXISTS `question_type`;
CREATE TABLE IF NOT EXISTS `question_type` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(25) NOT NULL,
  `description` varchar(500) NOT NULL,
  `data` varchar(1000) DEFAULT NULL,
  `group_id` int(10) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table stratus.question_type_group
DROP TABLE IF EXISTS `question_type_group`;
CREATE TABLE IF NOT EXISTS `question_type_group` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `description` varchar(500) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `description` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table stratus.resource_access
DROP TABLE IF EXISTS `resource_access`;
CREATE TABLE IF NOT EXISTS `resource_access` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `resource_type` varchar(50) NOT NULL,
  `resource_id` varchar(50) NOT NULL,
  `client_id` int(10) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `resource_type_resource_id` (`resource_type`,`resource_id`),
  KEY `client_id` (`client_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table stratus.review_portal
DROP TABLE IF EXISTS `review_portal`;
CREATE TABLE IF NOT EXISTS `review_portal` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `client_id` int(10) unsigned NOT NULL,
  `name` varchar(50) NOT NULL,
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `description` text,
  `created_datetime` datetime NOT NULL,
  `modified_datetime` datetime DEFAULT NULL,
  `pdf_path` varchar(255) DEFAULT NULL,
  `pdf_admin_user_id` int(11) unsigned DEFAULT NULL,
  `pdf_datetime` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_review_portal_client` (`client_id`),
  CONSTRAINT `FK_review_portal_client` FOREIGN KEY (`client_id`) REFERENCES `client` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table stratus.review_portal_document
DROP TABLE IF EXISTS `review_portal_document`;
CREATE TABLE IF NOT EXISTS `review_portal_document` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `portal_id` int(10) unsigned NOT NULL,
  `document_id` int(10) unsigned NOT NULL,
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `sort_order` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_review_portal_document_document` (`document_id`),
  KEY `FK_review_portal_document_portal` (`portal_id`),
  CONSTRAINT `FK_review_portal_document_portal` FOREIGN KEY (`portal_id`) REFERENCES `review_portal` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table stratus.review_portal_page
DROP TABLE IF EXISTS `review_portal_page`;
CREATE TABLE IF NOT EXISTS `review_portal_page` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `portal_id` int(10) unsigned NOT NULL,
  `page_id` int(10) unsigned NOT NULL,
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `sort_order` int(10) unsigned NOT NULL,
  `note` text,
  PRIMARY KEY (`id`),
  KEY `FK_review_portal_page_page` (`page_id`),
  KEY `FK_review_portal_page_portal` (`portal_id`),
  CONSTRAINT `FK_review_portal_page_page` FOREIGN KEY (`page_id`) REFERENCES `page` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_review_portal_page_portal` FOREIGN KEY (`portal_id`) REFERENCES `review_portal` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table stratus.review_portal_page_status_history
DROP TABLE IF EXISTS `review_portal_page_status_history`;
CREATE TABLE IF NOT EXISTS `review_portal_page_status_history` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `portal_page_id` int(10) unsigned NOT NULL,
  `status` tinyint(1) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `status_datetime` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_review_portal_page_status_history_portal` (`portal_page_id`),
  KEY `FK_review_portal_page_status_history_user` (`user_id`),
  CONSTRAINT `FK_review_portal_page_status_history_portal` FOREIGN KEY (`portal_page_id`) REFERENCES `review_portal_page` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_review_portal_page_status_history_user` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table stratus.review_portal_thread
DROP TABLE IF EXISTS `review_portal_thread`;
CREATE TABLE IF NOT EXISTS `review_portal_thread` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` int(10) unsigned DEFAULT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `portal_page_id` int(10) unsigned NOT NULL,
  `subject` varchar(50) NOT NULL,
  `body` text,
  `post_datetime` datetime NOT NULL,
  `is_approved` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `last_modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `parent_id` (`parent_id`),
  KEY `FK_review_portal_thread_user` (`user_id`),
  KEY `FK_review_portal_thread_portal_page` (`portal_page_id`),
  CONSTRAINT `FK_review_portal_thread_portal_page` FOREIGN KEY (`portal_page_id`) REFERENCES `review_portal_page` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_review_portal_thread_user` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table stratus.review_portal_user
DROP TABLE IF EXISTS `review_portal_user`;
CREATE TABLE IF NOT EXISTS `review_portal_user` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `portal_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_review_portal_user_portal` (`portal_id`),
  KEY `FK_review_portal_user_user` (`user_id`),
  CONSTRAINT `FK_review_portal_user_portal` FOREIGN KEY (`portal_id`) REFERENCES `review_portal` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_review_portal_user_user` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table stratus.role
DROP TABLE IF EXISTS `role`;
CREATE TABLE IF NOT EXISTS `role` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `label` varchar(50) NOT NULL,
  `description` text,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table stratus.role_permission
DROP TABLE IF EXISTS `role_permission`;
CREATE TABLE IF NOT EXISTS `role_permission` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `role_id` int(10) unsigned NOT NULL,
  `permission_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_role_permission_role` (`role_id`),
  KEY `FK_role_permission_permission` (`permission_id`),
  CONSTRAINT `FK_role_permission_permission` FOREIGN KEY (`permission_id`) REFERENCES `permission` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_role_permission_role` FOREIGN KEY (`role_id`) REFERENCES `role` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table stratus.student
DROP TABLE IF EXISTS `student`;
CREATE TABLE IF NOT EXISTS `student` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `user_record_client_id` int(10) unsigned DEFAULT NULL,
  `registration_client_id` int(10) unsigned DEFAULT NULL,
  `password` char(32) DEFAULT NULL,
  `password_reset_key` char(32) DEFAULT NULL,
  `app_id` int(10) unsigned DEFAULT NULL,
  `title_id` int(10) unsigned DEFAULT NULL,
  `phone` varchar(25) DEFAULT NULL,
  `clinic` varchar(500) DEFAULT NULL,
  `licensenumber` varchar(500) DEFAULT NULL,
  `associationnumber` varchar(500) DEFAULT NULL,
  `stateoflicensure` varchar(100) DEFAULT NULL,
  `workposition` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `address1` varchar(500) DEFAULT NULL,
  `address2` varchar(100) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `provstate` varchar(100) DEFAULT NULL,
  `country` varchar(100) DEFAULT NULL,
  `postalcode` varchar(15) DEFAULT NULL,
  `is_student` tinyint(1) NOT NULL DEFAULT '1',
  `registered_datetime` datetime NOT NULL,
  `role_id` int(10) unsigned NOT NULL DEFAULT '4',
  `language` varchar(3) NOT NULL DEFAULT 'ENG',
  PRIMARY KEY (`id`),
  KEY `FK_student_app_id` (`app_id`),
  KEY `FK_student_user_id` (`user_id`),
  KEY `FK_student_title_id` (`title_id`),
  CONSTRAINT `FK_student_app_id` FOREIGN KEY (`app_id`) REFERENCES `app` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_student_title_id` FOREIGN KEY (`title_id`) REFERENCES `title` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `FK_student_user_id` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table stratus.student_workposition
DROP TABLE IF EXISTS `student_workposition`;
CREATE TABLE IF NOT EXISTS `student_workposition` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(45) NOT NULL,
  `tally` mediumint(6) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table stratus.survey
DROP TABLE IF EXISTS `survey`;
CREATE TABLE IF NOT EXISTS `survey` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `client_id` int(10) unsigned DEFAULT NULL,
  `type_id` int(10) unsigned DEFAULT '0',
  `response_typeid` int(10) DEFAULT '0',
  `survey_completion_typeid` int(10) unsigned DEFAULT '0',
  `ce_points` tinyint(3) NOT NULL DEFAULT '0',
  `name` varchar(100) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `status` tinyint(3) NOT NULL,
  `passscore` tinyint(3) DEFAULT NULL,
  `maxtime` smallint(4) DEFAULT '0',
  `maxquestions` smallint(4) DEFAULT '0',
  `is_random` tinyint(1) DEFAULT '0',
  `is_surveyfeedback` tinyint(1) DEFAULT '0',
  `created_datetime` datetime NOT NULL,
  `modified_datetime` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`),
  KEY `fk_survey_clientid` (`client_id`),
  CONSTRAINT `fk_survey_clientid` FOREIGN KEY (`client_id`) REFERENCES `client` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table stratus.survey_completion_type
DROP TABLE IF EXISTS `survey_completion_type`;
CREATE TABLE IF NOT EXISTS `survey_completion_type` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `description` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `description` (`description`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table stratus.survey_feedback_question
DROP TABLE IF EXISTS `survey_feedback_question`;
CREATE TABLE IF NOT EXISTS `survey_feedback_question` (
  `id` mediumint(10) NOT NULL AUTO_INCREMENT,
  `text` varchar(2000) NOT NULL,
  `text_fr` varchar(3000) NOT NULL DEFAULT '',
  `type_id` int(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table stratus.survey_question_response
DROP TABLE IF EXISTS `survey_question_response`;
CREATE TABLE IF NOT EXISTS `survey_question_response` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `data` blob,
  `quiz_id` int(10) unsigned DEFAULT NULL,
  `survey_id` int(10) unsigned DEFAULT NULL,
  `quiz_event` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `client_id` int(10) unsigned DEFAULT NULL,
  `question_id` int(10) unsigned DEFAULT NULL,
  `mark` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Data exporting was unselected.


-- Dumping structure for table stratus.survey_response_scope
DROP TABLE IF EXISTS `survey_response_scope`;
CREATE TABLE IF NOT EXISTS `survey_response_scope` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `description` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `description` (`description`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table stratus.survey_response_type
DROP TABLE IF EXISTS `survey_response_type`;
CREATE TABLE IF NOT EXISTS `survey_response_type` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `description` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `description` (`description`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table stratus.survey_result
DROP TABLE IF EXISTS `survey_result`;
CREATE TABLE IF NOT EXISTS `survey_result` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `data` blob,
  `quiz_id` int(10) unsigned DEFAULT NULL,
  `survey_settings` blob,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Data exporting was unselected.


-- Dumping structure for table stratus.survey_tag
DROP TABLE IF EXISTS `survey_tag`;
CREATE TABLE IF NOT EXISTS `survey_tag` (
  `survey_id` int(10) unsigned DEFAULT NULL,
  `tag_id` int(11) DEFAULT NULL,
  `id` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`),
  UNIQUE KEY `tag_id_2` (`tag_id`),
  KEY `survey_id` (`survey_id`),
  KEY `tag_id` (`tag_id`),
  CONSTRAINT `survey_tag_ibfk_1` FOREIGN KEY (`survey_id`) REFERENCES `survey` (`id`),
  CONSTRAINT `survey_tag_ibfk_2` FOREIGN KEY (`tag_id`) REFERENCES `tag` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Data exporting was unselected.


-- Dumping structure for table stratus.survey_type
DROP TABLE IF EXISTS `survey_type`;
CREATE TABLE IF NOT EXISTS `survey_type` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `description` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `description` (`description`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table stratus.tag
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

-- Data exporting was unselected.


-- Dumping structure for table stratus.tag_entity
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

-- Data exporting was unselected.


-- Dumping structure for table stratus.team
DROP TABLE IF EXISTS `team`;
CREATE TABLE IF NOT EXISTS `team` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `client_id` int(10) unsigned NOT NULL,
  `name` varchar(50) NOT NULL,
  `status` tinyint(3) unsigned NOT NULL,
  `description` text,
  `created_datetime` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_team_client` (`client_id`),
  CONSTRAINT `FK_team_client` FOREIGN KEY (`client_id`) REFERENCES `client` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table stratus.team_package
DROP TABLE IF EXISTS `team_package`;
CREATE TABLE IF NOT EXISTS `team_package` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `team_id` int(10) unsigned NOT NULL,
  `package_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_team_package_team` (`team_id`),
  KEY `FK_team_package_package` (`package_id`),
  CONSTRAINT `FK_team_package_package` FOREIGN KEY (`package_id`) REFERENCES `package` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_team_package_team` FOREIGN KEY (`team_id`) REFERENCES `team` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table stratus.team_team
DROP TABLE IF EXISTS `team_team`;
CREATE TABLE IF NOT EXISTS `team_team` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `parent_team_id` int(10) unsigned NOT NULL,
  `child_team_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_team_team_parent` (`parent_team_id`),
  KEY `FK_team_team_child` (`child_team_id`),
  CONSTRAINT `FK_team_team_child` FOREIGN KEY (`child_team_id`) REFERENCES `team` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_team_team_parent` FOREIGN KEY (`parent_team_id`) REFERENCES `team` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table stratus.team_user
DROP TABLE IF EXISTS `team_user`;
CREATE TABLE IF NOT EXISTS `team_user` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `team_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_team_user_team` (`team_id`),
  KEY `FK_team_user_user` (`user_id`),
  CONSTRAINT `FK_team_user_team` FOREIGN KEY (`team_id`) REFERENCES `team` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_team_user_user` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table stratus.title
DROP TABLE IF EXISTS `title`;
CREATE TABLE IF NOT EXISTS `title` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `client_id` int(10) unsigned NOT NULL,
  `name` varchar(100) NOT NULL,
  `status` tinyint(1) unsigned NOT NULL,
  `version` int(10) unsigned NOT NULL DEFAULT '1',
  `nav_type` varchar(50) NOT NULL,
  `type` varchar(50) NOT NULL,
  `description` text,
  `media_asset_id` int(11) DEFAULT NULL,
  `is_editable` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `created_datetime` datetime NOT NULL,
  `modified_datetime` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `client_id` (`client_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table stratus.title_menu
DROP TABLE IF EXISTS `title_menu`;
CREATE TABLE IF NOT EXISTS `title_menu` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title_id` int(10) unsigned NOT NULL,
  `page_id` int(10) unsigned,
  `parent_id` int(10) unsigned DEFAULT NULL,
  `node_name` varchar(50) NOT NULL,
  `order` tinyint(3) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_menu_title` (`title_id`),
  KEY `FK_menu_page` (`page_id`),
  KEY `parent_id` (`parent_id`),
  CONSTRAINT `FK_menu_title` FOREIGN KEY (`title_id`) REFERENCES `title` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table stratus.title_page
DROP TABLE IF EXISTS `title_page`;
CREATE TABLE IF NOT EXISTS `title_page` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title_id` int(10) unsigned NOT NULL,
  `is_root` tinyint(1) unsigned DEFAULT '1',
  `parent_node_id` int(10) unsigned DEFAULT NULL,
  `page_id` int(10) unsigned NOT NULL,
  `node_name` varchar(50) DEFAULT NULL,
  `order` tinyint(3) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `title_id` (`title_id`),
  KEY `FK_title_page_page` (`page_id`),
  CONSTRAINT `FK_title_page_page` FOREIGN KEY (`page_id`) REFERENCES `page` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_title_page_title` FOREIGN KEY (`title_id`) REFERENCES `title` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table stratus.user
DROP TABLE IF EXISTS `user`;
CREATE TABLE IF NOT EXISTS `user` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `client_id` int(10) unsigned NOT NULL,
  `surname` varchar(50) NOT NULL,
  `firstname` varchar(50) NOT NULL,
  `UDID` varchar(50) DEFAULT NULL,
  `user_type` varchar(50) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(60) NOT NULL,
  `password_converted` tinyint(1) DEFAULT NULL,
  `password_reset_key` char(32) DEFAULT NULL,
  `role_id` varchar(255) NOT NULL,
  `created_datetime` datetime NOT NULL,
  `modified_datetime` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  KEY `client_id` (`client_id`),
  KEY `password_reset_key` (`password_reset_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table stratus.user_api_session_keys
DROP TABLE IF EXISTS `user_api_session_keys`;
CREATE TABLE IF NOT EXISTS `user_api_session_keys` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `session_key` char(32) NOT NULL,
  `modified` int(11) unsigned NOT NULL,
  `lifetime` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table stratus.user_data
DROP TABLE IF EXISTS `user_data`;
CREATE TABLE IF NOT EXISTS `user_data` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `user_data_key_id` int(10) unsigned NOT NULL,
  `value` varchar(255) NOT NULL,
  `created_datetime` datetime NOT NULL,
  `modified_datetime` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table stratus.user_data_key
DROP TABLE IF EXISTS `user_data_key`;
CREATE TABLE IF NOT EXISTS `user_data_key` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table stratus.user_device
DROP TABLE IF EXISTS `user_device`;
CREATE TABLE IF NOT EXISTS `user_device` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `device_id` varchar(255) NOT NULL,
  `first_used` datetime NOT NULL,
  `last_used` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_user_device_user` (`user_id`),
  KEY `device_id` (`device_id`),
  CONSTRAINT `FK_user_device_user` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table stratus.user_email_signature
DROP TABLE IF EXISTS `user_email_signature`;
CREATE TABLE IF NOT EXISTS `user_email_signature` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `email` varchar(250) NOT NULL,
  `position` varchar(250) NOT NULL,
  `phone_nr` varchar(50) NOT NULL,
  `created_datetime` date NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table stratus.user_open_id
DROP TABLE IF EXISTS `user_open_id`;
CREATE TABLE IF NOT EXISTS `user_open_id` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `expiration_time` timestamp NULL DEFAULT NULL,
  `authentification_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `sub` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table stratus.user_package
DROP TABLE IF EXISTS `user_package`;
CREATE TABLE IF NOT EXISTS `user_package` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `package_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_user_package_user` (`user_id`),
  KEY `FK_user_package_package` (`package_id`),
  CONSTRAINT `FK_user_package_package` FOREIGN KEY (`package_id`) REFERENCES `package` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_user_package_user` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table stratus.user_setting
DROP TABLE IF EXISTS `user_setting`;
CREATE TABLE IF NOT EXISTS `user_setting` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `setting` varchar(50) NOT NULL,
  `value` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table stratus.user_survey
DROP TABLE IF EXISTS `user_survey`;
CREATE TABLE IF NOT EXISTS `user_survey` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `client_id` int(10) unsigned NOT NULL,
  `page_id` int(10) unsigned NOT NULL,
  `survey_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `lastquestion_id` int(10) unsigned NOT NULL,
  `order` tinyint(4) DEFAULT '0' COMMENT 'the unique order list for this quiz if randomized',
  `is_complete` tinyint(1) DEFAULT '0',
  `is_passed` tinyint(1) DEFAULT '0',
  `elapsedtime` mediumint(6) DEFAULT '0' COMMENT 'time in seconds',
  `score` int(5) DEFAULT NULL,
  `started_datetime` datetime DEFAULT '0000-00-00 00:00:00',
  `modified_datetime` datetime DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `FK_usersurvey_user_id` (`user_id`),
  CONSTRAINT `FK_usersurvey_user_id` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table stratus.user_survey_option
DROP TABLE IF EXISTS `user_survey_option`;
CREATE TABLE IF NOT EXISTS `user_survey_option` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `option_id` int(10) unsigned NOT NULL,
  `user_question_id` int(10) unsigned NOT NULL,
  `value` tinyint(1) DEFAULT '0' COMMENT 'if right or wrong choice',
  `data` text,
  `order` tinyint(4) NOT NULL COMMENT 'user choice',
  PRIMARY KEY (`id`),
  KEY `FK_SurveyOption_SurveyQuestion` (`user_question_id`),
  CONSTRAINT `FK_SurveyOption_SurveyQuestion` FOREIGN KEY (`user_question_id`) REFERENCES `user_survey_question` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table stratus.user_survey_question
DROP TABLE IF EXISTS `user_survey_question`;
CREATE TABLE IF NOT EXISTS `user_survey_question` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_survey_id` int(10) unsigned NOT NULL,
  `survey_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `question_id` int(10) unsigned NOT NULL,
  `useranswer` varchar(25) NOT NULL COMMENT 'user choice',
  `score` tinyint(1) NOT NULL COMMENT 'right or wrong',
  `data` text,
  `elapsedtime` smallint(4) DEFAULT '0' COMMENT 'time in seconds',
  `completed_datetime` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_survey_id` (`survey_id`),
  KEY `FK_SurveyQuestion_Survey` (`user_survey_id`),
  CONSTRAINT `FK_SurveyQuestion_Survey` FOREIGN KEY (`user_survey_id`) REFERENCES `user_survey` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table stratus.user_system_session
DROP TABLE IF EXISTS `user_system_session`;
CREATE TABLE IF NOT EXISTS `user_system_session` (
  `id` char(32) NOT NULL DEFAULT '',
  `modified` int(11) DEFAULT NULL,
  `lifetime` int(11) DEFAULT NULL,
  `data` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table stratus.user_title_status
DROP TABLE IF EXISTS `user_title_status`;
CREATE TABLE IF NOT EXISTS `user_title_status` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `title_id` int(10) unsigned NOT NULL DEFAULT '0',
  `status_type_id` int(10) unsigned NOT NULL DEFAULT '0',
  `datetime` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `title_id` (`title_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table stratus.user_title_status_type
DROP TABLE IF EXISTS `user_title_status_type`;
CREATE TABLE IF NOT EXISTS `user_title_status_type` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `description` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table stratus.website
DROP TABLE IF EXISTS `website`;
CREATE TABLE IF NOT EXISTS `website` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `client_id` int(10) unsigned NOT NULL,
  `description` text,
  `website_state_id` int(10) unsigned DEFAULT NULL,
  `website_layout_id` int(10) unsigned DEFAULT NULL,
  `branding_id` int(10) unsigned DEFAULT NULL,
  `website_style_id` int(10) unsigned DEFAULT NULL,
  `allow_search_engine` tinyint(1) unsigned DEFAULT NULL,
  `timezone_id` int(10) unsigned DEFAULT NULL,
  `language_id` int(10) unsigned DEFAULT NULL,
  `date_format` varchar(50) DEFAULT NULL,
  `time_format` varchar(50) DEFAULT NULL,
  `allow_google_analytics` tinyint(1) unsigned DEFAULT NULL,
  `google_analytics_code` text,
  `website_navigation_node_root_id` int(10) unsigned DEFAULT NULL,
  `is_deleted` tinyint(1) unsigned NOT NULL,
  `footer` text,
  `header` text,
  `created_datetime` datetime NOT NULL,
  `modified_datetime` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `client_id` (`client_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table stratus.website_blog_category
DROP TABLE IF EXISTS `website_blog_category`;
CREATE TABLE IF NOT EXISTS `website_blog_category` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `website_id` int(10) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `website_id` (`website_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table stratus.website_blog_post
DROP TABLE IF EXISTS `website_blog_post`;
CREATE TABLE IF NOT EXISTS `website_blog_post` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `website_blog_category_id` int(10) unsigned NOT NULL,
  `website_id` int(10) unsigned NOT NULL,
  `title` varchar(50) NOT NULL,
  `post` mediumtext NOT NULL,
  `is_active` tinyint(1) unsigned NOT NULL,
  `publish_datetime` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `website_blog_category_id` (`website_blog_category_id`),
  KEY `website_id` (`website_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table stratus.website_brand
DROP TABLE IF EXISTS `website_brand`;
CREATE TABLE IF NOT EXISTS `website_brand` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `client_id` int(10) unsigned NOT NULL,
  `name` varchar(50) NOT NULL,
  `color_1` varchar(50) NOT NULL,
  `color_2` varchar(50) NOT NULL,
  `color_3` varchar(50) NOT NULL,
  `logo_media_asset_id` int(10) unsigned NOT NULL,
  `font_name` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `client_id` (`client_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table stratus.website_domain
DROP TABLE IF EXISTS `website_domain`;
CREATE TABLE IF NOT EXISTS `website_domain` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `client_id` int(10) unsigned NOT NULL,
  `website_id` int(10) unsigned DEFAULT NULL,
  `ip` varchar(50) DEFAULT NULL,
  `contact_email` varchar(255) DEFAULT NULL,
  `contact_name` varchar(255) DEFAULT NULL,
  `managed_by_lifelearn` tinyint(1) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `name` (`name`),
  KEY `client_id` (`client_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table stratus.website_font
DROP TABLE IF EXISTS `website_font`;
CREATE TABLE IF NOT EXISTS `website_font` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `source` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table stratus.website_language
DROP TABLE IF EXISTS `website_language`;
CREATE TABLE IF NOT EXISTS `website_language` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `language_code` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table stratus.website_layout
DROP TABLE IF EXISTS `website_layout`;
CREATE TABLE IF NOT EXISTS `website_layout` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `client_id` int(10) unsigned NOT NULL,
  `name` varchar(50) NOT NULL,
  `data` text NOT NULL,
  `thumbnail_media_asset_id` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `client_id` (`client_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table stratus.website_navigation_node
DROP TABLE IF EXISTS `website_navigation_node`;
CREATE TABLE IF NOT EXISTS `website_navigation_node` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `website_id` int(10) NOT NULL,
  `website_page_id` int(10) DEFAULT NULL,
  `name` varchar(50) NOT NULL,
  `order` tinyint(3) unsigned NOT NULL,
  `parent_id` int(10) unsigned DEFAULT NULL,
  `not_linked` tinyint(1) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `parent_id` (`parent_id`),
  KEY `website_id` (`website_id`),
  KEY `website_page_id` (`website_page_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table stratus.website_page
DROP TABLE IF EXISTS `website_page`;
CREATE TABLE IF NOT EXISTS `website_page` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `content_id` int(11) NOT NULL,
  `website_id` int(10) unsigned NOT NULL,
  `name` varchar(50) NOT NULL,
  `uri` varchar(255) NOT NULL,
  `data` text,
  `html` text,
  PRIMARY KEY (`id`),
  KEY `website_id` (`website_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table stratus.website_page_template
DROP TABLE IF EXISTS `website_page_template`;
CREATE TABLE IF NOT EXISTS `website_page_template` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `client_id` int(10) unsigned NOT NULL,
  `name` varchar(50) NOT NULL,
  `data` text NOT NULL,
  `thumbnail_media_asset_id` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `client_id` (`client_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table stratus.website_role
DROP TABLE IF EXISTS `website_role`;
CREATE TABLE IF NOT EXISTS `website_role` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table stratus.website_state
DROP TABLE IF EXISTS `website_state`;
CREATE TABLE IF NOT EXISTS `website_state` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table stratus.website_style
DROP TABLE IF EXISTS `website_style`;
CREATE TABLE IF NOT EXISTS `website_style` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `client_id` int(10) unsigned NOT NULL,
  `name` varchar(50) NOT NULL,
  `data` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `client_id` (`client_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table stratus.website_timezone
DROP TABLE IF EXISTS `website_timezone`;
CREATE TABLE IF NOT EXISTS `website_timezone` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `code` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table stratus.website_user
DROP TABLE IF EXISTS `website_user`;
CREATE TABLE IF NOT EXISTS `website_user` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `website_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `website_role_id` int(10) unsigned NOT NULL,
  `phone` varchar(50) NOT NULL,
  `address` varchar(255) NOT NULL,
  `country` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `website_id` (`website_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Data exporting was unselected.
/*!40014 SET FOREIGN_KEY_CHECKS=1 */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
