--
-- Table structure for table `cms_category`
--

CREATE TABLE IF NOT EXISTS `cms_category` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `url_id` varchar(255) DEFAULT NULL COMMENT 'url slug',
  `set_id` int(10) unsigned NOT NULL COMMENT 'set id',
  `name` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `parent_id` int(10) unsigned NOT NULL,
  `level` int(10) unsigned NOT NULL,
  `data` text COMMENT 'custom json data',
  `meta` text COMMENT 'meta json data',
  PRIMARY KEY (`id`),
  KEY `set_id` (`set_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='pages categories' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `cms_category_page`
--

CREATE TABLE IF NOT EXISTS `cms_category_page` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `category_id` int(10) unsigned NOT NULL,
  `page_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `category_id` (`category_id`,`page_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='page belongs to categories' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `cms_category_page_type`
--

CREATE TABLE IF NOT EXISTS `cms_category_page_type` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `set_id` int(10) NOT NULL COMMENT 'category set id',
  `type_id` int(10) NOT NULL COMMENT 'page type id',
  PRIMARY KEY (`id`),
  UNIQUE KEY `set_category_type` (`set_id`,`type_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='not in use now - page types have categories' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `cms_category_set`
--

CREATE TABLE IF NOT EXISTS `cms_category_set` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `module` varchar(20) NOT NULL,
  `page_type_id` int(10) unsigned NOT NULL COMMENT 'page type id',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='set of categories' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `cms_category_tr`
--

CREATE TABLE IF NOT EXISTS `cms_category_tr` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `url_id` varchar(255) DEFAULT NULL COMMENT 'url slug',
  `language` char(5) NOT NULL,
  `translation_id` int(10) unsigned NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `description` text,
  `meta` text COMMENT 'meta json data',
  PRIMARY KEY (`id`),
  UNIQUE KEY `language` (`language`,`translation_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='category translations' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `cms_menu`
--

CREATE TABLE IF NOT EXISTS `cms_menu` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(30) NOT NULL COMMENT 'menu code',
  `name` varchar(30) NOT NULL COMMENT 'description name',
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`,`name`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='cms menu' AUTO_INCREMENT=3 ;

--
-- Dumping data for table `cms_menu`
--

INSERT INTO `cms_menu` (`id`, `code`, `name`) VALUES
(1, 'main', 'Main'),
(2, 'footer', 'Footer');

-- --------------------------------------------------------

--
-- Table structure for table `cms_menu_item`
--

CREATE TABLE IF NOT EXISTS `cms_menu_item` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `application_id` int(10) unsigned NOT NULL,
  `menu` char(10) NOT NULL DEFAULT 'main',
  `level` int(10) unsigned NOT NULL DEFAULT '0',
  `parent_id` int(10) unsigned NOT NULL DEFAULT '0',
  `page_id` int(10) unsigned DEFAULT NULL COMMENT 'optional cms page id - menu routes to',
  `name` varchar(30) DEFAULT NULL,
  `route` varchar(20) NOT NULL DEFAULT 'default' COMMENT 'route name',
  `path` varchar(255) DEFAULT NULL COMMENT 'module/controller/action',
  `params` varchar(255) DEFAULT NULL COMMENT 'param_name/param_value ...',
  `uri` varchar(255) DEFAULT NULL COMMENT 'if no route specify uri',
  `ord_num` smallint(5) unsigned NOT NULL DEFAULT '1',
  `hidden` enum('yes','no') NOT NULL DEFAULT 'no',
  `meta` text COMMENT 'meta json data',
  `target` varchar(10) DEFAULT NULL COMMENT 'link target',
  PRIMARY KEY (`id`),
  KEY `menu` (`application_id`,`menu`,`level`,`parent_id`,`ord_num`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `cms_menu_item`
--

INSERT INTO `cms_menu_item` (`application_id`, `menu`, `level`, `parent_id`, `page_id`, `name`, `route`, `path`, `params`, `uri`, `ord_num`, `hidden`, `meta`, `target`) VALUES
(1, 'main', 0, 0, 59, 'Home', 'cms', 'cms/page/index', NULL, '', 1, 'no', NULL, NULL),
(1, 'main', 0, 0, 60, 'Intro', 'cms', 'cms/page/index', NULL, '', 2, 'no', NULL, NULL),
(1, 'footer', 0, 0, 60, 'Intro', 'cms', 'cms/page/index', NULL, '', 2, 'no', NULL, NULL),
(1, 'footer', 0, 0, 59, 'Home', 'cms', 'cms/page/index', NULL, '', 1, 'no', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `cms_menu_item_tr`
--

CREATE TABLE IF NOT EXISTS `cms_menu_item_tr` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `language` char(5) NOT NULL,
  `translation_id` int(10) unsigned NOT NULL,
  `name` varchar(50) DEFAULT NULL,
  `uri` varchar(255) DEFAULT NULL,
  `meta` text COMMENT 'meta json data',
  PRIMARY KEY (`id`),
  UNIQUE KEY `language` (`language`,`translation_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='menu translations' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `cms_page`
--

CREATE TABLE IF NOT EXISTS `cms_page` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(20) NOT NULL COMMENT 'url segment',
  `url_id` varchar(30) DEFAULT NULL COMMENT 'parmalink id part',
  `application_id` int(10) unsigned NOT NULL,
  `type_id` int(10) unsigned DEFAULT NULL COMMENT 'page type',
  `user_id` int(10) unsigned NOT NULL COMMENT 'owner id',
  `posted` datetime NOT NULL,
  `format` enum('html','php','path') NOT NULL DEFAULT 'html' COMMENT 'format of the content',
  `title` varchar(50) NOT NULL,
  `content` text NOT NULL,
  `status` enum('draft','published') NOT NULL DEFAULT 'draft',
  `teaser` tinytext,
  `data` text COMMENT 'custom json data',
  `meta` text COMMENT 'meta json data',
  `content_type` enum('PUBLIC','PRIVATE') NOT NULL DEFAULT 'PUBLIC',
  PRIMARY KEY (`id`),
  KEY `type_id` (`type_id`),
  KEY `status` (`status`),
  KEY `user_id` (`user_id`),
  KEY `url_id` (`url_id`),
  KEY `application_id` (`application_id`,`code`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='CMS Pages' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

INSERT INTO `cms_page` (`id`, `code`, `url_id`, `application_id`, `type_id`, `user_id`, `posted`, `format`, `title`, `content`, `status`, `teaser`, `data`, `meta`, `content_type`) VALUES
(58, 'search', 'search', 1, 1, 1, '2014-10-23 16:08:03', 'path', 'Search', 'search.phtml', 'published', '', '{"teaser_image":""}', '{"keywords":"","description":""}', 'PUBLIC'),
(59, 'home', 'home', 1, 1, 1, '2014-09-04 11:38:48', 'path', 'Home', 'en/home.phtml', 'published', '', '{"teaser_image":""}', '{"keywords":"","description":"HORISEN is your home for cross-media marketing solutions including: web services, social media, mobile marketing, telecom, design and graphics and products."}', 'PUBLIC'),
(60, 'intro', 'intro', 1, 1, 1, '2014-09-04 11:38:48', 'path', 'Intro', 'en/intro.phtml', 'published', NULL, '{"teaser_image":""}', '{"keywords":"","description":""}', 'PUBLIC');

--
-- Table structure for table `cms_page_tr`
--

CREATE TABLE IF NOT EXISTS `cms_page_tr` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `language` char(5) NOT NULL,
  `translation_id` int(10) unsigned NOT NULL,
  `url_id` varchar(30) DEFAULT NULL COMMENT 'parmalink id part',
  `title` varchar(50) DEFAULT NULL,
  `content` text,
  `teaser` tinytext,
  `data` text COMMENT 'custom json data',
  `meta` text COMMENT 'meta json data',
  PRIMARY KEY (`id`),
  UNIQUE KEY `language` (`language`,`translation_id`),
  KEY `url_id` (`url_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='pages translations' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

INSERT INTO `cms_page_tr` (`id`, `language`, `translation_id`, `url_id`, `title`, `content`, `teaser`, `data`, `meta`) VALUES
(106, 'en', 58, 'search', 'Search', 'en/search.phtml', '', '{"teaser_image":""}', '{"keywords":"","description":""}'),
(101, 'de', 58, 'search', 'Search', 'de/search.phtml', '', '{"teaser_image":""}', '{"keywords":"","description":""}'),
(102, 'en', 59, 'home', 'Home', 'en/home.phtml', '', '{"teaser_image":""}', '{"keywords":"","description":"HORISEN is your home for cross-media marketing solutions including: web services, social media, mobile marketing, telecom, design and graphics and products."}'),
(103, 'de', 59, 'home', 'Home', 'de/home.phtml', NULL, '{"teaser_image":""}', '{"keywords":"","description":""}'),
(104, 'en', 60, 'intro', '', 'en/intro.phtml', NULL, '{"teaser_image":""}', '{"keywords":"","description":""}'),
(105, 'de', 60, 'intro', '', 'de/intro.phtml', NULL, '{"teaser_image":""}', '{"keywords":"","description":""}');

--
-- Table structure for table `cms_page_type`
--

CREATE TABLE IF NOT EXISTS `cms_page_type` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(20) DEFAULT NULL COMMENT 'page type unique code',
  `name` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `module` varchar(20) DEFAULT NULL,
  `data` text COMMENT 'json custom data',
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='page types' AUTO_INCREMENT=14 ;

--
-- Dumping data for table `cms_page_type`
--

INSERT INTO `cms_page_type` (`id`, `code`, `name`, `description`, `module`, `data`) VALUES
(1, 'page', 'Page', 'Standard Static Page', 'cms', '{\r\n"delete":{\r\n"link":"cms/admin/page-delete" } }');

-- --------------------------------------------------------

--
-- Table structure for table `cms_route`
--

CREATE TABLE IF NOT EXISTS `cms_route` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `application_id` int(10) unsigned NOT NULL,
  `page_id` int(10) unsigned DEFAULT NULL COMMENT 'optional cms page id - record routes to',
  `uri` varchar(255) NOT NULL COMMENT 'uri to match',
  `name` varchar(50) DEFAULT NULL,
  `path` varchar(255) DEFAULT NULL COMMENT 'module/controller/action',
  `params` varchar(255) DEFAULT NULL COMMENT 'param_name/param_value ...',
  `lang` char(5) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `menu` (`application_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `cms_route`
--

INSERT INTO `cms_route` (`id`, `application_id`, `page_id`, `uri`, `name`, `path`, `params`, `lang`) VALUES
(1, 1, 59, 'home', 'Home', 'cms/page/index', NULL, 'de'),
(4, 1, 59, 'home', 'Home', 'cms/page/index', NULL, 'en'),
(2, 1, 60, '', 'Intro', 'cms/page/index', NULL, 'en'),
(3, 1, 60, '', 'Intro', 'cms/page/index', NULL, 'de'),
(5, 1, 58, 'search', 'Search', 'cms/page/index', NULL, 'de'),
(6, 1, 58, 'search', 'Search', 'cms/page/index', NULL, 'en');

INSERT INTO `module` (`id`, `application_id`, `code`, `name`, `description`, `settings`, `data`) VALUES
(10, 1, 'cms', 'CMS', 'Content Management Module', '', '{ "widgets":{ "hDashboard.Widget.Cms":{ "jsFiles":["/modules/cms/js/Widget.js"], "cssFiles":[] }, "hDashboard.Widget.Cms.Statistics":{ "jsFiles":["/modules/cms/js/Widget/Statistics.js","/plugins/highchart/highcharts.js","/js/jquery.peity.min.js"], "cssFiles":[] } }, "menus":{ "page/index":{ "name":"Page view", "dialog_url":"cms\\/admin\\/dialog" }, "sitemap/index":{"name":"Sitemap","dialog_url":null} } }');



--
-- All updates goes below this comment
--

-- 2.0.18
ALTER TABLE `cms_page` CHANGE `title` `title` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ;
ALTER TABLE `cms_page_tr` CHANGE `title` `title` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ;

-- 2.0.23

ALTER TABLE `cms_page` CHANGE `url_id` `url_id` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT 'parmalink id part';
ALTER TABLE `cms_page_tr` CHANGE `url_id` `url_id` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT 'parmalink id part';


-- 2.0.28
ALTER TABLE `application` ADD `theme_settings` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `email_settings` ;

UPDATE  `module` SET  `data` =  '
{
  "widgets": {
    "hDashboard.Widget.Cms": {
      "jsFiles": [
        "/modules/cms/js/Widget.js"
      ],
      "cssFiles": []
    },
    "hDashboard.Widget.Cms.GoogleDashboardCountries": {
      "jsFiles": [
        "/modules/cms/js/Widget/GoogleDashboardCountries.js"
      ],
      "cssFiles": []
    },
    "hDashboard.Widget.Cms.GoogleDashboardSessions": {
      "jsFiles": [
        "/modules/cms/js/Widget/GoogleDashboardSessions.js"
      ],
      "cssFiles": []
    },
    "hDashboard.Widget.Cms.GoogleDashboardBrowsers": {
      "jsFiles": [
        "/modules/cms/js/Widget/GoogleDashboardBrowsers.js"
      ],
      "cssFiles": []
    }
  },
  "menus": {
    "page/index": {
      "name": "Page view",
      "dialog_url": "cms/admin/dialog"
    },
    "sitemap/index": {
      "name": "Sitemap",
      "dialog_url": null
    }
  }
}
' WHERE  `module`.`code` = 'cms';

UPDATE  `auth_user` SET  `data` =  '' WHERE  `auth_user`.`username` = 'admin';

