-- phpMyAdmin SQL Dump
-- version 4.2.11
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Mar 09, 2016 at 06:22 PM
-- Server version: 5.6.21
-- PHP Version: 5.5.19

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

CREATE TABLE IF NOT EXISTS `cache` (
  `type` varchar(48) COLLATE utf8_unicode_ci NOT NULL,
  `namespace` int(11) NOT NULL,
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

CREATE TABLE IF NOT EXISTS `contents` (
  `id` varchar(512) COLLATE utf8_unicode_ci NOT NULL,
  `content` mediumtext COLLATE utf8_unicode_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `contents`
--

INSERT INTO `contents` (`id`, `content`) VALUES
('index/doc', '\n<h2>Mapping Information</h2>\n<h3>Single</h3>\n<p>\nYou will need the id / identifier of the show e.g. tvdb-id for <i>American Dad!</i> is <strong>73141</strong><br>\nthe origin is the name of the site/entity the episode, season (and/or absolute) numbers are based on</p>\n<p>\n<strong>http://thexem.de/map/single?id=<identifier>&origin=<base_origin>&episode=<episodenumber>&season=<seasonnumber>&absolute=<absolutenumber></absolutenumber></seasonnumber></episodenumber></base_origin></identifier></strong>\n</p>\n<p>\n<strong>episode</strong>, <strong>season</strong> and <strong>absolute</strong> are all optional but it wont work if you don''t provide either <strong>episode</strong> and <strong>season</strong> OR <strong>absolute</strong>\nin addition you can provide <strong>destination</strong> as the name of the wished destination, if not provided it will output all available\n</p>\n<p>\nWhen a destination has two or more addresses another entry will be added as <entityname>_<number> ... for now the second address gets the index "2" (the first index is omitted) and so on\n</number></entityname></p>\n<tt>http://thexem.de/map/single?id=7529&origin=anidb&season=1&episode=2&destination=trakt</tt>\n<pre>{\n"result":"success",\n "data":{\n        "trakt":  {"season":1,"episode":3,"absolute":3},\n        "trakt_2":{"season":1,"episode":4,"absolute":4}\n        },\n "message":"single mapping for 7529 on anidb."\n}\n</pre>\n<h3>All</h3>\n<p>\nBasically same as "single" just a little easier<br>\nThe origin address is added into the output too!!\n</p>\n<tt>http://thexem.de/map/all?id=7529&origin=anidb</tt>\n<pre>{"result":"success","data":[{"scene":{"season":1,"episode":1,"absolute":1},"tvdb":{"season":1,"episode":1,"absolute":1},"tvdb_2":{"season":1,"episode":2,"absolute":2},"rage":{"season":1,"episode":1,"absolute":1},"trakt":{"season":1,"episode":1,"absolute":1},"trakt_2":{"season":1,"episode":2,"absolute":2},"anidb":{"season":1,"episode":1,"absolute":1}},{"scene":{"season":1,"episode":2,"absolute":2},"tvdb":{"season":1,"episode":3,"absolute":3},"tvdb_2":{"season":1,"episode":4,"absolute":4},"rage":{"season":1,"episode":2,"absolute":2},"trakt":{"season":1,"episode":3,"absolute":3},"trakt_2":{"season":1,"episode":4,"absolute":4},"anidb":{"season":1,"episode":2,"absolute":2}},{"scene":{"season":1,"episode":3,"absolute":3},"tvdb":{"season":1,"episode":5,"absolute":5},"tvdb_2":{"season":1,"episode":6,"absolute":6},"rage":{"season":1,"episode":3,"absolute":3},"trakt":{"season":1,"episode":5,"absolute":5},"trakt_2":{"season":1,"episode":6,"absolute":6},"anidb":{"season":1,"episode":3,"absolute":3}},{"scene":{"season":1,"episode":4,"absolute":4},"tvdb":{"season":1,"episode":7,"absolute":7},"tvdb_2":{"season":1,"episode":8,"absolute":8},"rage":{"season":1,"episode":4,"absolute":4},"trakt":{"season":1,"episode":7,"absolute":7},"trakt_2":{"season":1,"episode":8,"absolute":8},"anidb":{"season":1,"episode":4,"absolute":4}},{"scene":{"season":1,"episode":5,"absolute":5},"tvdb":{"season":1,"episode":9,"absolute":9},"tvdb_2":{"season":1,"episode":10,"absolute":10},"rage":{"season":1,"episode":5,"absolute":5},"trakt":{"season":1,"episode":9,"absolute":9},"trakt_2":{"season":1,"episode":10,"absolute":10},"anidb":{"season":1,"episode":5,"absolute":5}},{"scene":{"season":1,"episode":6,"absolute":6},"tvdb":{"season":1,"episode":11,"absolute":11},"tvdb_2":{"season":1,"episode":12,"absolute":12},"rage":{"season":1,"episode":6,"absolute":6},"trakt":{"season":1,"episode":11,"absolute":11},"trakt_2":{"season":1,"episode":12,"absolute":12},"anidb":{"season":1,"episode":6,"absolute":6}},{"scene":{"season":1,"episode":7,"absolute":7},"tvdb":{"season":1,"episode":13,"absolute":13},"tvdb_2":{"season":1,"episode":14,"absolute":14},"rage":{"season":1,"episode":7,"absolute":7},"trakt":{"season":1,"episode":13,"absolute":13},"trakt_2":{"season":1,"episode":14,"absolute":14},"anidb":{"season":1,"episode":7,"absolute":7}},{"scene":{"season":1,"episode":8,"absolute":8},"tvdb":{"season":1,"episode":15,"absolute":15},"tvdb_2":{"season":1,"episode":16,"absolute":16},"rage":{"season":1,"episode":8,"absolute":8},"trakt":{"season":1,"episode":15,"absolute":15},"trakt_2":{"season":1,"episode":16,"absolute":16},"anidb":{"season":1,"episode":8,"absolute":8}},{"scene":{"season":1,"episode":9,"absolute":9},"tvdb":{"season":1,"episode":17,"absolute":17},"tvdb_2":{"season":1,"episode":18,"absolute":18},"rage":{"season":1,"episode":9,"absolute":9},"trakt":{"season":1,"episode":17,"absolute":17},"trakt_2":{"season":1,"episode":18,"absolute":18},"anidb":{"season":1,"episode":9,"absolute":9}},{"scene":{"season":1,"episode":10,"absolute":10},"tvdb":{"season":1,"episode":19,"absolute":19},"tvdb_2":{"season":1,"episode":20,"absolute":20},"rage":{"season":1,"episode":10,"absolute":10},"trakt":{"season":1,"episode":19,"absolute":19},"trakt_2":{"season":1,"episode":20,"absolute":20},"anidb":{"season":1,"episode":10,"absolute":10}},{"scene":{"season":1,"episode":11,"absolute":11},"tvdb":{"season":1,"episode":21,"absolute":21},"tvdb_2":{"season":1,"episode":22,"absolute":22},"rage":{"season":1,"episode":11,"absolute":11},"trakt":{"season":1,"episode":21,"absolute":21},"trakt_2":{"season":1,"episode":22,"absolute":22},"anidb":{"season":1,"episode":11,"absolute":11}},{"scene":{"season":1,"episode":12,"absolute":12},"tvdb":{"season":1,"episode":23,"absolute":23},"tvdb_2":{"season":1,"episode":24,"absolute":24},"rage":{"season":1,"episode":12,"absolute":12},"trakt":{"season":1,"episode":23,"absolute":23},"trakt_2":{"season":1,"episode":24,"absolute":24},"anidb":{"season":1,"episode":12,"absolute":12}},{"scene":{"season":1,"episode":13,"absolute":13},"tvdb":{"season":1,"episode":25,"absolute":25},"tvdb_2":{"season":1,"episode":26,"absolute":26},"rage":{"season":1,"episode":13,"absolute":13},"trakt":{"season":1,"episode":25,"absolute":25},"trakt_2":{"season":1,"episode":26,"absolute":26},"anidb":{"season":1,"episode":13,"absolute":13}}],"message":"full mapping for 7529 on anidb. this was a cached version"}\n</pre>\n\n<h3>All Names</h3>\n<p>\nGet all names xem has to offer<br>\nnon optional params: origin(an entity string like ''tvdb'')<br>\noptional params: season, language<br>\n- season: a season number or a list like: 1,3,5 or a compare operator like ne,gt,ge,lt,le,eq and a season number. default would return all<br>\n- language: a language string like ''us'' or ''jp'' default is all<br>\n- defaultNames: 1(yes) or 0(no) should the default names be added to the list ? default is 0(no)\n</p>\n<tt>http://thexem.de/map/allNames?origin=tvdb&season=le1</tt>\n<pre>{\n"result": "success",\n"data": {\n        "79604": ["Black-Lagoon", "ブラック・ラグーン", "Burakku Ragūn"],\n        "248812": ["Dont Trust the Bitch in Apartment 23", "Don''t Trust the Bitch in Apartment 23"],\n        "257571": ["Nazo no Kanojo X"],\n        "257875": ["Lupin III - Mine Fujiko to Iu Onna", "Lupin III Fujiko to Iu Onna", "Lupin the Third - Mine Fujiko to Iu Onna"]\n        },\n"message": ""\n}\n</pre>\n\n\n<strong>No proxy for you!</strong>\n<p>\nThe proxy has been disabled because it was causing to many confusions and was not reliable, sorry for any inconveniences. \n</p>\n\n<h2 style="text-decoration: line-through;">Proxy Service</h2>\n<p style="text-decoration: line-through;">\nThe proxy service allows you to use your preferred info provider interface and data structure but with the mapping provided by xem.<br>\nOnly by changing the base URL to <strong>http://thexem.de/proxy/<infoprovider>/<xemmapping></xemmapping></infoprovider></strong>\n</p>\n<p style="text-decoration: line-through;">\ne.g. you want to use the information from <span class="tvdb shadow">tvdb</span> but with the numbers of the <span class="scene shadow">scene</span><br>\nyou simply change the basic API URL in your client from <strong>http://www.thetvdb.com</strong> to <strong>http://thexem.de/proxy/tvdb/scene</strong>\n</p>\n\n    <ul>\n        <li style="text-decoration:line-through;">If the show is simply not in the xem database a redirect to the original info provider URL is done.</li>\n        <li style="text-decoration:line-through;">Heavy caching will be used.</li>\n        <li style="text-decoration:line-through;">The episode tags might not appear in original order (although you shouldn''t rely on that anyway)</li>\n        <li style="text-decoration:line-through;">ONLY implemented for <span class="tvdb shadow">tvdb</span> !!</li>\n    </ul>\n<p style="text-decoration:line-through;">\ne.g. <a href="http://thexem.de/proxy/tvdb/scene/api/4CE423C53F065488/series/73141/all/en.xml">http://thexem.de/proxy/tvdb/scene/api/4CE423C53F065488/series/73141/all/en.xml</a>\n</p>');

-- --------------------------------------------------------

--
-- Table structure for table `directrules`
--

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- RELATIONS FOR TABLE `directrules`:
--   `element_id`
--       `elements` -> `id`
--   `name_id`
--       `names` -> `id`
--   `origin_id`
--       `locations` -> `id`
--   `destination_id`
--       `locations` -> `id`
--

-- --------------------------------------------------------

--
-- Table structure for table `elements`
--

CREATE TABLE IF NOT EXISTS `elements` (
`id` int(11) NOT NULL,
  `type` enum('show','movie','episode') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'show',
  `main_name` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `entity_order` varchar(24) COLLATE utf8_unicode_ci NOT NULL,
  `status` int(11) NOT NULL DEFAULT '1',
  `parent` int(11) NOT NULL DEFAULT '0',
  `last_modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `history`
--

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- RELATIONS FOR TABLE `history`:
--   `element_id`
--       `elements` -> `id`
--   `user_id`
--       `users` -> `user_id`
--

-- --------------------------------------------------------

--
-- Table structure for table `languages`
--

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
(1, 'scene', 'the SCENE', '', NULL, NULL, 1),
(2, 'tvdb', 'thetvdb.com is a open database that can be modified by anybody', 'http://www.thetvdb.com', 'http://thetvdb.com/?tab=series&id={tvdb}', NULL, 1),
(3, 'anidb', 'anidb.net', 'http://www.anidb.net', 'http://anidb.net/perl-bin/animedb.pl?show=anime&aid={anidb}', NULL, 1),
(4, 'rage', 'tv rage', 'http://www.tvrage.com', 'http://www.tvrage.com/shows/id-{rage}', NULL, 1),
(5, 'trakt', 'trakt is actively keeping a record of what TV shows and movies you are watching.', 'http://trakt.tv/', 'http://trakt.tv/search?q=tvdb:{tvdb}', 'http://trakt.tv/search?q=imdb:{imdb}', 0),
(6, 'master', 'virtual master of the xem', 'http://thexem.de/', '', '', 1);

-- --------------------------------------------------------

--
-- Table structure for table `names`
--

CREATE TABLE IF NOT EXISTS `names` (
`id` int(11) NOT NULL,
  `element_id` int(11) NOT NULL,
  `season` int(11) NOT NULL DEFAULT '-1' COMMENT '-1 = for all seasons',
  `name` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `language` varchar(2) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'en'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- RELATIONS FOR TABLE `names`:
--   `element_id`
--       `elements` -> `id`
--   `language`
--       `languages` -> `id`
--

-- --------------------------------------------------------

--
-- Table structure for table `passthrus`
--

CREATE TABLE IF NOT EXISTS `passthrus` (
`id` int(11) NOT NULL,
  `origin_id` int(11) NOT NULL,
  `destination_id` int(11) NOT NULL,
  `element_id` int(11) NOT NULL,
  `type` enum('absolute','sxxexx','full') COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- RELATIONS FOR TABLE `passthrus`:
--   `element_id`
--       `elements` -> `id`
--   `origin_id`
--       `locations` -> `id`
--   `destination_id`
--       `locations` -> `id`
--

-- --------------------------------------------------------

--
-- Table structure for table `seasons`
--

CREATE TABLE IF NOT EXISTS `seasons` (
`id` int(11) NOT NULL,
  `location_id` int(11) NOT NULL,
  `element_id` int(11) NOT NULL,
  `identifier` varchar(90) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'the tvdbid or anidb id.\n\nif this is not set use the identifier of the previous season is used\n\nif set this season has a separate id. absolute start should be 1',
  `season` int(11) DEFAULT '-1' COMMENT '-1 = global\nx>=0 = season number x',
  `season_size` int(11) DEFAULT '-1' COMMENT '-1 = has no size\n0 = size zero\nx>0 = size x',
  `absolute_start` int(11) DEFAULT '0' COMMENT '-1 = dont calculate absolute numbers\n0 = use previous season \nx>0 = start at x',
  `episode_start` int(11) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- RELATIONS FOR TABLE `seasons`:
--   `element_id`
--       `elements` -> `id`
--   `location_id`
--       `locations` -> `id`
--

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
`user_id` int(11) NOT NULL,
  `user_email` varchar(255) NOT NULL,
  `user_nick` varchar(20) NOT NULL COMMENT 'XEM',
  `user_pass` varchar(60) NOT NULL,
  `user_lvl` int(11) NOT NULL DEFAULT '0' COMMENT 'XEM',
  `user_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `user_modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `user_last_login` datetime DEFAULT NULL,
  `user_activationcode` varchar(32) NOT NULL COMMENT 'XEM',
  `config_email_new_account` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'XEM',
  `config_email_new_show` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'XEM',
  `config_email_public_request` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'XEM'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

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
 ADD PRIMARY KEY (`id`), ADD KEY `fk_history_users1` (`user_id`), ADD KEY `fk_history_elements1` (`element_id`), ADD KEY `history_obj_type` (`obj_type`,`obj_id`(255));

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
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `elements`
--
ALTER TABLE `elements`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `history`
--
ALTER TABLE `history`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `locations`
--
ALTER TABLE `locations`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=7;
--
-- AUTO_INCREMENT for table `names`
--
ALTER TABLE `names`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `passthrus`
--
ALTER TABLE `passthrus`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `seasons`
--
ALTER TABLE `seasons`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT;
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
