-- phpMyAdmin SQL Dump
-- version 3.4.10.1
-- http://www.phpmyadmin.net
--
-- Host: rdbms.strato.de
-- Generation Time: Aug 09, 2012 at 02:44 AM
-- Server version: 5.0.91
-- PHP Version: 5.2.17

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `DB997247`
--

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE IF NOT EXISTS `cache` (
  `type` varchar(48) collate utf8_unicode_ci NOT NULL,
  `namespace` varchar(48) collate utf8_unicode_ci NOT NULL,
  `name` varchar(48) collate utf8_unicode_ci NOT NULL,
  `data` longtext collate utf8_unicode_ci NOT NULL,
  `creation_date` datetime NOT NULL,
  `best_before` datetime NOT NULL,
  `encoded` tinyint(4) default '0',
  PRIMARY KEY  (`type`,`name`,`namespace`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `contents`
--

CREATE TABLE IF NOT EXISTS `contents` (
  `id` varchar(512) collate utf8_unicode_ci NOT NULL,
  `content` mediumtext collate utf8_unicode_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `directrules`
--

CREATE TABLE IF NOT EXISTS `directrules` (
  `id` int(11) NOT NULL auto_increment,
  `origin_id` int(11) NOT NULL,
  `destination_id` int(11) NOT NULL,
  `element_id` int(11) NOT NULL COMMENT 'only to elements with type show',
  `name_id` int(11) default NULL COMMENT 'rev to a specific name if needed',
  `origin_season` int(11) default NULL,
  `origin_episode` int(11) default NULL,
  `destination_season` int(11) default NULL,
  `destination_episode` int(11) default NULL,
  PRIMARY KEY  (`id`),
  KEY `fk_maps_locations1` (`origin_id`),
  KEY `fk_maps_locations2` (`destination_id`),
  KEY `fk_directrules_names1` (`name_id`),
  KEY `fk_directrules_elements1` (`element_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1503 ;

-- --------------------------------------------------------

--
-- Table structure for table `elements`
--

CREATE TABLE IF NOT EXISTS `elements` (
  `id` int(11) NOT NULL auto_increment,
  `type` enum('show','movie','episode') collate utf8_unicode_ci NOT NULL default 'show',
  `main_name` varchar(45) collate utf8_unicode_ci NOT NULL,
  `entity_order` varchar(90) collate utf8_unicode_ci NOT NULL,
  `status` int(11) NOT NULL default '1',
  `parent` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `main_name_UNIQUE` (`main_name`,`status`,`parent`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=196 ;

-- --------------------------------------------------------

--
-- Table structure for table `history`
--

CREATE TABLE IF NOT EXISTS `history` (
  `id` int(11) NOT NULL auto_increment,
  `user_id` int(11) NOT NULL,
  `user_lvl` int(11) default NULL COMMENT 'user lvl at time',
  `obj_id` varchar(512) collate utf8_unicode_ci NOT NULL,
  `element_id` int(11) default NULL,
  `obj_type` varchar(12) collate utf8_unicode_ci NOT NULL COMMENT 'object type',
  `action` varchar(24) collate utf8_unicode_ci default NULL,
  `time` datetime default NULL COMMENT 'time of the action',
  `revision` int(11) default NULL,
  `old_data` text collate utf8_unicode_ci,
  `new_data` text collate utf8_unicode_ci,
  `silent` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `fk_history_users1` (`user_id`),
  KEY `fk_history_elements1` (`element_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=6896 ;

-- --------------------------------------------------------

--
-- Table structure for table `languages`
--

CREATE TABLE IF NOT EXISTS `languages` (
  `id` varchar(2) collate utf8_unicode_ci NOT NULL COMMENT 'the iso ISO3166-1 alpha-2 name',
  `name` varchar(48) collate utf8_unicode_ci NOT NULL COMMENT 'human name',
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `locations`
--

CREATE TABLE IF NOT EXISTS `locations` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(16) collate utf8_unicode_ci default NULL COMMENT 'the short name',
  `description` varchar(256) collate utf8_unicode_ci default NULL,
  `url` varchar(90) collate utf8_unicode_ci default NULL,
  `show_url` varchar(128) collate utf8_unicode_ci default NULL,
  `movie_url` varchar(128) collate utf8_unicode_ci default NULL,
  `status` tinyint(3) NOT NULL default '1' COMMENT '1=active; 0=deleted',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `name_UNIQUE` (`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=7 ;

-- --------------------------------------------------------

--
-- Table structure for table `names`
--

CREATE TABLE IF NOT EXISTS `names` (
  `id` int(11) NOT NULL auto_increment,
  `element_id` int(11) NOT NULL,
  `season` int(11) NOT NULL default '-1' COMMENT '-1 = for all seasons',
  `name` varchar(128) collate utf8_unicode_ci NOT NULL,
  `language` varchar(2) collate utf8_unicode_ci NOT NULL default 'en',
  PRIMARY KEY  (`id`),
  KEY `fk_names_elements1` (`element_id`),
  KEY `language` (`language`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=356 ;

-- --------------------------------------------------------

--
-- Table structure for table `passthrus`
--

CREATE TABLE IF NOT EXISTS `passthrus` (
  `id` int(11) NOT NULL auto_increment,
  `origin_id` int(11) NOT NULL,
  `destination_id` int(11) NOT NULL,
  `element_id` int(11) NOT NULL,
  `type` enum('absolute','sxxexx','full') collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `fk_passthru_elements1` (`element_id`),
  KEY `fk_passthru_locations1` (`origin_id`),
  KEY `fk_passthru_locations2` (`destination_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=601 ;

-- --------------------------------------------------------

--
-- Table structure for table `seasons`
--

CREATE TABLE IF NOT EXISTS `seasons` (
  `id` int(11) NOT NULL auto_increment,
  `location_id` int(11) NOT NULL,
  `element_id` int(11) NOT NULL,
  `identifier` varchar(90) collate utf8_unicode_ci default NULL COMMENT 'the tvdbid or anidb id.\n\nif this is not set use the identifier of the previous season is used\n\nif set this season has a separate id. absolute start should be 1',
  `season` int(11) default '-1' COMMENT '-1 = global\nx>=0 = season number x',
  `season_size` int(11) default '-1' COMMENT '-1 = has no size\n0 = size zero\nx>0 = size x',
  `absolute_start` int(11) default '0' COMMENT '-1 = dont calulate absolute numbers\n0 = use previous season \nx>0 = start at x',
  `episode_start` int(11) NOT NULL default '1',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `helper` (`location_id`,`element_id`,`season`),
  KEY `location` (`location_id`),
  KEY `fk_elementLocations_elements1` (`element_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=816 ;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `user_id` int(11) NOT NULL auto_increment,
  `user_email` varchar(255) NOT NULL,
  `user_nick` varchar(45) default NULL,
  `user_pass` varchar(60) NOT NULL,
  `user_lvl` int(11) default '0',
  `user_date` datetime NOT NULL,
  `user_modified` datetime NOT NULL,
  `user_last_login` datetime default NULL,
  `user_activationcode` varchar(32) NOT NULL,
  `config_email_new_account` tinyint(1) NOT NULL default '0',
  `config_email_new_show` tinyint(1) NOT NULL default '0',
  `config_email_public_request` tinyint(1) NOT NULL default '1',
  PRIMARY KEY  (`user_id`),
  UNIQUE KEY `user_email` (`user_email`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=78 ;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `directrules`
--
ALTER TABLE `directrules`
  ADD CONSTRAINT `fk_directrules_elements1` FOREIGN KEY (`element_id`) REFERENCES `elements` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_directrules_names1` FOREIGN KEY (`name_id`) REFERENCES `names` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_maps_locations1` FOREIGN KEY (`origin_id`) REFERENCES `locations` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_maps_locations2` FOREIGN KEY (`destination_id`) REFERENCES `locations` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `history`
--
ALTER TABLE `history`
  ADD CONSTRAINT `fk_history_elements1` FOREIGN KEY (`element_id`) REFERENCES `elements` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_history_users1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE NO ACTION ON UPDATE CASCADE;

--
-- Constraints for table `names`
--
ALTER TABLE `names`
  ADD CONSTRAINT `fk_names_elements1` FOREIGN KEY (`element_id`) REFERENCES `elements` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `names_ibfk_1` FOREIGN KEY (`language`) REFERENCES `languages` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `passthrus`
--
ALTER TABLE `passthrus`
  ADD CONSTRAINT `fk_passthru_elements1` FOREIGN KEY (`element_id`) REFERENCES `elements` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_passthru_locations1` FOREIGN KEY (`origin_id`) REFERENCES `locations` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_passthru_locations2` FOREIGN KEY (`destination_id`) REFERENCES `locations` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `seasons`
--
ALTER TABLE `seasons`
  ADD CONSTRAINT `fk_elementLocations_elements1` FOREIGN KEY (`element_id`) REFERENCES `elements` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `location` FOREIGN KEY (`location_id`) REFERENCES `locations` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
