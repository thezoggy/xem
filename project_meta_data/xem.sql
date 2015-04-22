-- phpMyAdmin SQL Dump
-- version 4.2.11
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Apr 22, 2015 at 03:27 PM
-- Server version: 5.6.21
-- PHP Version: 5.6.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `db997247`
--

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
CREATE TABLE IF NOT EXISTS `cache` (
  `type` varchar(48) COLLATE utf8_unicode_ci NOT NULL,
  `namespace` varchar(48) COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(48) COLLATE utf8_unicode_ci NOT NULL,
  `data` longtext COLLATE utf8_unicode_ci NOT NULL,
  `creation_date` datetime NOT NULL,
  `best_before` datetime NOT NULL,
  `encoded` tinyint(4) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `contents`
--

DROP TABLE IF EXISTS `contents`;
CREATE TABLE IF NOT EXISTS `contents` (
  `id` varchar(512) COLLATE utf8_unicode_ci NOT NULL,
  `content` mediumtext COLLATE utf8_unicode_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `directrules`
--

DROP TABLE IF EXISTS `directrules`;
CREATE TABLE IF NOT EXISTS `directrules` (
`id` int(11) NOT NULL,
  `origin_id` int(11) NOT NULL,
  `destination_id` int(11) NOT NULL,
  `element_id` int(11) NOT NULL COMMENT 'only to elements with type show',
  `name_id` int(11) DEFAULT NULL COMMENT 'rev to a specific name if needed',
  `origin_season` int(11) DEFAULT NULL,
  `origin_episode` int(11) DEFAULT NULL,
  `destination_season` int(11) DEFAULT NULL,
  `destination_episode` int(11) DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=73815 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `elements`
--

DROP TABLE IF EXISTS `elements`;
CREATE TABLE IF NOT EXISTS `elements` (
`id` int(11) NOT NULL,
  `type` enum('show','movie','episode') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'show',
  `main_name` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `entity_order` varchar(90) COLLATE utf8_unicode_ci NOT NULL,
  `status` int(11) NOT NULL DEFAULT '1',
  `parent` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB AUTO_INCREMENT=2246 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `history`
--

DROP TABLE IF EXISTS `history`;
CREATE TABLE IF NOT EXISTS `history` (
`id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `user_lvl` int(11) DEFAULT NULL COMMENT 'user lvl at time',
  `obj_id` varchar(512) COLLATE utf8_unicode_ci NOT NULL,
  `element_id` int(11) DEFAULT NULL,
  `obj_type` varchar(12) COLLATE utf8_unicode_ci NOT NULL COMMENT 'object type',
  `action` varchar(24) COLLATE utf8_unicode_ci DEFAULT NULL,
  `time` datetime DEFAULT NULL COMMENT 'time of the action',
  `revision` int(11) DEFAULT NULL,
  `old_data` text COLLATE utf8_unicode_ci,
  `new_data` text COLLATE utf8_unicode_ci,
  `silent` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB AUTO_INCREMENT=231256 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `languages`
--

DROP TABLE IF EXISTS `languages`;
CREATE TABLE IF NOT EXISTS `languages` (
  `id` varchar(2) COLLATE utf8_unicode_ci NOT NULL COMMENT 'the iso ISO3166-1 alpha-2 name',
  `name` varchar(48) COLLATE utf8_unicode_ci NOT NULL COMMENT 'human name'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `languages`
--

INSERT INTO `languages` (`id`, `name`) VALUES
('de', 'Deutsch'),
('fr', 'Français'),
('jp', 'Japanese'),
('us', 'English');

-- --------------------------------------------------------

--
-- Table structure for table `locations`
--

DROP TABLE IF EXISTS `locations`;
CREATE TABLE IF NOT EXISTS `locations` (
`id` int(11) NOT NULL,
  `name` varchar(16) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'the short name',
  `description` varchar(256) COLLATE utf8_unicode_ci DEFAULT NULL,
  `url` varchar(90) COLLATE utf8_unicode_ci DEFAULT NULL,
  `show_url` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL,
  `movie_url` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL,
  `status` tinyint(3) NOT NULL DEFAULT '1' COMMENT '1=active; 0=deleted'
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `locations`
--

INSERT INTO `locations` (`id`, `name`, `description`, `url`, `show_url`, `movie_url`, `status`) VALUES
(1, 'scene', 'the SCENE', NULL, NULL, NULL, 1),
(2, 'tvdb', 'thetvdb.com is a open database that can be modified by anybody', 'http://www.thetvdb.com', 'http://thetvdb.com/?tab=series&id={tvdb}', NULL, 1),
(3, 'anidb', 'anidb.net', 'http://www.anidb.net', 'http://anidb.net/perl-bin/animedb.pl?show=anime&aid={anidb}', NULL, 1),
(4, 'rage', 'tv rage', 'http://www.tvrage.com', 'http://www.tvrage.com/shows/id-{rage}', NULL, 1),
(5, 'trakt', 'trakt is actively keeping a record of what TV shows and movies you are watching.', 'http://trakt.tv/', 'http://trakt.tv/search?q=tvdb:{tvdb}', 'http://trakt.tv/search?q=imdb:{imdb}', 0),
(6, 'master', 'virtual master of the xem', 'http://thexem.de/', '', '', 1);

-- --------------------------------------------------------

--
-- Table structure for table `names`
--

DROP TABLE IF EXISTS `names`;
CREATE TABLE IF NOT EXISTS `names` (
`id` int(11) NOT NULL,
  `element_id` int(11) NOT NULL,
  `season` int(11) NOT NULL DEFAULT '-1' COMMENT '-1 = for all seasons',
  `name` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `language` varchar(2) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'en'
) ENGINE=InnoDB AUTO_INCREMENT=3041 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `passthrus`
--

DROP TABLE IF EXISTS `passthrus`;
CREATE TABLE IF NOT EXISTS `passthrus` (
`id` int(11) NOT NULL,
  `origin_id` int(11) NOT NULL,
  `destination_id` int(11) NOT NULL,
  `element_id` int(11) NOT NULL,
  `type` enum('absolute','sxxexx','full') COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=7154 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `seasons`
--

DROP TABLE IF EXISTS `seasons`;
CREATE TABLE IF NOT EXISTS `seasons` (
`id` int(11) NOT NULL,
  `location_id` int(11) NOT NULL,
  `element_id` int(11) NOT NULL,
  `identifier` varchar(90) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'the tvdbid or anidb id.\n\nif this is not set use the identifier of the previous season is used\n\nif set this season has a separate id. absolute start should be 1',
  `season` int(11) DEFAULT '-1' COMMENT '-1 = global\nx>=0 = season number x',
  `season_size` int(11) DEFAULT '-1' COMMENT '-1 = has no size\n0 = size zero\nx>0 = size x',
  `absolute_start` int(11) DEFAULT '0' COMMENT '-1 = dont calulate absolute numbers\n0 = use previous season \nx>0 = start at x',
  `episode_start` int(11) NOT NULL DEFAULT '1'
) ENGINE=InnoDB AUTO_INCREMENT=12800 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
`user_id` int(11) NOT NULL,
  `user_email` varchar(255) NOT NULL,
  `user_nick` varchar(45) DEFAULT NULL,
  `user_pass` varchar(60) NOT NULL,
  `user_lvl` int(11) DEFAULT '0',
  `user_date` datetime NOT NULL,
  `user_modified` datetime NOT NULL,
  `user_last_login` datetime DEFAULT NULL,
  `user_activationcode` varchar(32) NOT NULL,
  `config_email_new_account` tinyint(1) NOT NULL DEFAULT '0',
  `config_email_new_show` tinyint(1) NOT NULL DEFAULT '0',
  `config_email_public_request` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB AUTO_INCREMENT=2880 DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
 ADD PRIMARY KEY (`type`,`name`,`namespace`);

--
-- Indexes for table `directrules`
--
ALTER TABLE `directrules`
 ADD PRIMARY KEY (`id`), ADD KEY `fk_maps_locations1` (`origin_id`), ADD KEY `fk_maps_locations2` (`destination_id`), ADD KEY `fk_directrules_names1` (`name_id`), ADD KEY `fk_directrules_elements1` (`element_id`);

--
-- Indexes for table `elements`
--
ALTER TABLE `elements`
 ADD PRIMARY KEY (`id`), ADD UNIQUE KEY `main_name_UNIQUE` (`main_name`,`status`,`parent`,`id`), ADD KEY `type` (`type`), ADD KEY `status` (`status`), ADD KEY `parent` (`parent`);

--
-- Indexes for table `history`
--
ALTER TABLE `history`
 ADD PRIMARY KEY (`id`), ADD KEY `fk_history_users1` (`user_id`), ADD KEY `fk_history_elements1` (`element_id`);

--
-- Indexes for table `languages`
--
ALTER TABLE `languages`
 ADD PRIMARY KEY (`id`);

--
-- Indexes for table `locations`
--
ALTER TABLE `locations`
 ADD PRIMARY KEY (`id`), ADD UNIQUE KEY `name_UNIQUE` (`name`);

--
-- Indexes for table `names`
--
ALTER TABLE `names`
 ADD PRIMARY KEY (`id`), ADD KEY `fk_names_elements1` (`element_id`), ADD KEY `language` (`language`);

--
-- Indexes for table `passthrus`
--
ALTER TABLE `passthrus`
 ADD PRIMARY KEY (`id`), ADD KEY `fk_passthru_elements1` (`element_id`), ADD KEY `fk_passthru_locations1` (`origin_id`), ADD KEY `fk_passthru_locations2` (`destination_id`);

--
-- Indexes for table `seasons`
--
ALTER TABLE `seasons`
 ADD PRIMARY KEY (`id`), ADD UNIQUE KEY `helper` (`location_id`,`element_id`,`season`), ADD KEY `location` (`location_id`), ADD KEY `fk_elementLocations_elements1` (`element_id`), ADD KEY `identifier` (`identifier`(24));

--
-- Indexes for table `users`
--
ALTER TABLE `users`
 ADD PRIMARY KEY (`user_id`), ADD UNIQUE KEY `user_email` (`user_email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `directrules`
--
ALTER TABLE `directrules`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=73815;
--
-- AUTO_INCREMENT for table `elements`
--
ALTER TABLE `elements`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2246;
--
-- AUTO_INCREMENT for table `history`
--
ALTER TABLE `history`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=231256;
--
-- AUTO_INCREMENT for table `locations`
--
ALTER TABLE `locations`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=7;
--
-- AUTO_INCREMENT for table `names`
--
ALTER TABLE `names`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=3041;
--
-- AUTO_INCREMENT for table `passthrus`
--
ALTER TABLE `passthrus`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=7154;
--
-- AUTO_INCREMENT for table `seasons`
--
ALTER TABLE `seasons`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=12800;
--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2880;
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
