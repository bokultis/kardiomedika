--
-- Table structure for table `teaser`
--

CREATE TABLE IF NOT EXISTS `teaser` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `box_code` varchar(100) NOT NULL,
  `name` varchar(100) NOT NULL,
  `all_menu_items` enum('yes','no') NOT NULL DEFAULT 'no' COMMENT 'show on all menu items',
  `content` text COMMENT 'json content',
  PRIMARY KEY (`id`),
  KEY `code` (`box_code`,`all_menu_items`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `teaser_has_items`
--

CREATE TABLE IF NOT EXISTS `teaser_has_items` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `teaser_id` int(10) unsigned NOT NULL,
  `item_id` int(10) unsigned NOT NULL,
  `order_num` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `teaser_id` (`teaser_id`,`item_id`,`order_num`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='which items teaser contains in which order';

-- --------------------------------------------------------

--
-- Table structure for table `teaser_item`
--

CREATE TABLE IF NOT EXISTS `teaser_item` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `box_code` varchar(100) NOT NULL,
  `box_id` int(10) NOT NULL,
  `fallback` enum('yes','no') NOT NULL,
  `start_dt` datetime NOT NULL,
  `end_dt` datetime NOT NULL,
  `title` varchar(255) NOT NULL,
  `content_type` varchar(20) NOT NULL,
  `content` text NOT NULL,
  `item_template` varchar(100) DEFAULT NULL COMMENT 'custom item template',
  `order_num` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'order number',
  PRIMARY KEY (`id`),
  KEY `box_id` (`box_id`,`start_dt`,`end_dt`,`order_num`,`fallback`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;

-- --------------------------------------------------------

--
-- Table structure for table `teaser_item_tr`
--

CREATE TABLE IF NOT EXISTS `teaser_item_tr` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `language` char(5) NOT NULL,
  `translation_id` int(10) unsigned NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `content` text COMMENT 'json data',
  PRIMARY KEY (`id`),
  UNIQUE KEY `language` (`language`,`translation_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='menu translations'  ;

-- --------------------------------------------------------

--
-- Table structure for table `teaser_menu_item`
--

CREATE TABLE IF NOT EXISTS `teaser_menu_item` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `teaser_id` int(10) unsigned NOT NULL,
  `menu_item_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `menu_item_id` (`menu_item_id`,`teaser_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='which menu items slider should be rendered' ;
