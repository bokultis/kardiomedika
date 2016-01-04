-- phpMyAdmin SQL Dump
-- version 4.0.10deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jan 04, 2016 at 12:25 PM
-- Server version: 5.5.46-0ubuntu0.14.04.2
-- PHP Version: 5.5.9-1ubuntu4.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: `wa_cms_kardiomedika`
--

-- --------------------------------------------------------

--
-- Table structure for table `application`
--

DROP TABLE IF EXISTS `application`;
CREATE TABLE IF NOT EXISTS `application` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(128) NOT NULL,
  `status` enum('A','D') DEFAULT NULL,
  `status_dt` datetime DEFAULT NULL,
  `style_json` text,
  `fb_settings` text NOT NULL COMMENT 'json fb settings',
  `twitter_settings` text NOT NULL,
  `og_settings` text NOT NULL COMMENT 'json open graph settings',
  `email_settings` text NOT NULL COMMENT 'json email settings',
  `theme_settings` text NOT NULL,
  `settings` text NOT NULL COMMENT 'global application settings',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='Open different facebook sms applications.' AUTO_INCREMENT=2 ;

--
-- Dumping data for table `application`
--

INSERT INTO `application` (`id`, `name`, `status`, `status_dt`, `style_json`, `fb_settings`, `twitter_settings`, `og_settings`, `email_settings`, `theme_settings`, `settings`) VALUES
(1, 'Kardiomedika', 'A', '2014-09-19 21:47:09', NULL, '{}', '{}', '{}', '{\r\n  "from_email":"fbapp@horisen.biz",\r\n  "from_name":"HORISEN CMS Skeleton",\r\n  "reply_email":"no-reply@horisen.com",\r\n  "to_emails":[{"name":"CMS","email":"boris@horisen.com"}],\r\n  "transport":"smtp",\r\n  "parameters":{\r\n    "server":"mail.horisen.com",\r\n    "auth":"login",\r\n    "username":"fbapp@horisen.biz",\r\n    "password":"Fbh0r1sen*9",\r\n    "port":"587"\r\n  }\r\n}', '', '{"hosting_quota":524288000,"theme":"genesis","captcha":{"fontName":"font4.ttf","wordLen":"3","timeout":"300","width":"150","height":"40","dotNoiseLevel":"20","lineNoiseLevel":"2"},"ga":{"email":"nis@horisen.com","password":"h0r1sens0lut10ns","account_id":"58280383xxx","tracking_id":"UA-30607857-1xxx"},"default_upload":{"default_extensions":["pjpeg","jpeg","jpg","png","x-png","gif","pdf"],"default_mimetypes":["image\\/pjpeg","image\\/jpeg","image\\/jpg","image\\/png","image\\/x-png","image\\/gif","application\\/pdf"]}}');

-- --------------------------------------------------------

--
-- Table structure for table `auth_acl`
--

DROP TABLE IF EXISTS `auth_acl`;
CREATE TABLE IF NOT EXISTS `auth_acl` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `role_id` int(10) unsigned NOT NULL,
  `privilege_id` int(10) unsigned NOT NULL,
  `allowed` enum('yes','no') NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `role_id` (`role_id`,`privilege_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='role can or cannot do privilege on resource' AUTO_INCREMENT=4 ;

--
-- Dumping data for table `auth_acl`
--

INSERT INTO `auth_acl` (`id`, `role_id`, `privilege_id`, `allowed`) VALUES
(1, 1, 1, 'yes'),
(3, 2, 3, 'yes');

-- --------------------------------------------------------

--
-- Table structure for table `auth_privilege`
--

DROP TABLE IF EXISTS `auth_privilege`;
CREATE TABLE IF NOT EXISTS `auth_privilege` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(20) NOT NULL,
  `name` varchar(100) NOT NULL,
  `resource_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `resource_id` (`resource_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `auth_privilege`
--

INSERT INTO `auth_privilege` (`id`, `code`, `name`, `resource_id`) VALUES
(1, 'access', 'Access Admin Panel', 1),
(3, 'master', 'Master Operations', 1);

-- --------------------------------------------------------

--
-- Table structure for table `auth_resource`
--

DROP TABLE IF EXISTS `auth_resource`;
CREATE TABLE IF NOT EXISTS `auth_resource` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(20) NOT NULL,
  `name` varchar(100) NOT NULL,
  `parent_id` int(10) unsigned NOT NULL,
  `module` varchar(20) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `parent_id` (`parent_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='resources' AUTO_INCREMENT=3 ;

--
-- Dumping data for table `auth_resource`
--

INSERT INTO `auth_resource` (`id`, `code`, `name`, `parent_id`, `module`) VALUES
(1, 'admin', 'Admin Module', 0, 'admin');

-- --------------------------------------------------------

--
-- Table structure for table `auth_role`
--

DROP TABLE IF EXISTS `auth_role`;
CREATE TABLE IF NOT EXISTS `auth_role` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `parent_id` int(10) unsigned NOT NULL COMMENT 'parent role',
  PRIMARY KEY (`id`),
  KEY `parent_id` (`parent_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `auth_role`
--

INSERT INTO `auth_role` (`id`, `name`, `parent_id`) VALUES
(1, 'Admin', 0),
(2, 'Master Admin', 1);

-- --------------------------------------------------------

--
-- Table structure for table `auth_user`
--

DROP TABLE IF EXISTS `auth_user`;
CREATE TABLE IF NOT EXISTS `auth_user` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `role_id` int(10) unsigned NOT NULL COMMENT 'role',
  `username` varchar(20) NOT NULL,
  `password` varchar(32) NOT NULL COMMENT 'md5',
  `password_reset` varchar(10) NOT NULL,
  `deleted` enum('yes','no') NOT NULL DEFAULT 'no',
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `email` varchar(255) NOT NULL,
  `status` enum('active','blocked','pending') NOT NULL DEFAULT 'pending',
  `lang` char(5) DEFAULT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `data` text COMMENT 'custom json data',
  `created` datetime NOT NULL,
  `changed_password_dt` datetime NOT NULL,
  `logged` datetime NOT NULL,
  `attempt_login` int(11) NOT NULL DEFAULT '0',
  `attempt_login_dt` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`),
  KEY `role_id` (`role_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='users and admins' AUTO_INCREMENT=2 ;

--
-- Dumping data for table `auth_user`
--

INSERT INTO `auth_user` (`id`, `role_id`, `username`, `password`, `password_reset`, `deleted`, `first_name`, `last_name`, `email`, `status`, `lang`, `image_path`, `data`, `created`, `changed_password_dt`, `logged`, `attempt_login`, `attempt_login_dt`) VALUES
(1, 2, 'admin', '21232f297a57a5a743894a0e4a801fc3', '', 'no', 'Nikola', 'Djordjevic', 'info@google.com', 'active', 'en', NULL, '', '2012-03-28 10:58:53', '2015-10-08 18:49:24', '2014-08-28 11:47:53', 1, '2015-12-26 14:08:32');

-- --------------------------------------------------------

--
-- Table structure for table `auth_user_history_password`
--

DROP TABLE IF EXISTS `auth_user_history_password`;
CREATE TABLE IF NOT EXISTS `auth_user_history_password` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `password` varchar(32) NOT NULL,
  `last_changed_date_password` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `auth_user_history_password`
--

INSERT INTO `auth_user_history_password` (`id`, `user_id`, `password`, `last_changed_date_password`) VALUES
(1, 1, 'aa01f6f431087f5bb9fc9f698c1ef14b', '2015-10-08 18:49:24');

-- --------------------------------------------------------

--
-- Table structure for table `cms_category`
--

DROP TABLE IF EXISTS `cms_category`;
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

DROP TABLE IF EXISTS `cms_category_page`;
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

DROP TABLE IF EXISTS `cms_category_page_type`;
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

DROP TABLE IF EXISTS `cms_category_set`;
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

DROP TABLE IF EXISTS `cms_category_tr`;
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

DROP TABLE IF EXISTS `cms_menu`;
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

DROP TABLE IF EXISTS `cms_menu_item`;
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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `cms_menu_item`
--

INSERT INTO `cms_menu_item` (`id`, `application_id`, `menu`, `level`, `parent_id`, `page_id`, `name`, `route`, `path`, `params`, `uri`, `ord_num`, `hidden`, `meta`, `target`) VALUES
(1, 1, 'main', 0, 0, 59, 'Home', 'cms', 'cms/page/index', NULL, '', 1, 'no', NULL, NULL),
(2, 1, 'main', 0, 0, 60, 'Intro', 'cms', 'cms/page/index', NULL, '', 2, 'no', NULL, NULL),
(3, 1, 'footer', 0, 0, 60, 'Intro', 'cms', 'cms/page/index', NULL, '', 2, 'no', NULL, NULL),
(4, 1, 'footer', 0, 0, 59, 'Home', 'cms', 'cms/page/index', NULL, '', 1, 'no', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `cms_menu_item_tr`
--

DROP TABLE IF EXISTS `cms_menu_item_tr`;
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

DROP TABLE IF EXISTS `cms_page`;
CREATE TABLE IF NOT EXISTS `cms_page` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(20) NOT NULL COMMENT 'url segment',
  `url_id` varchar(255) DEFAULT NULL COMMENT 'parmalink id part',
  `application_id` int(10) unsigned NOT NULL,
  `type_id` int(10) unsigned DEFAULT NULL COMMENT 'page type',
  `user_id` int(10) unsigned NOT NULL COMMENT 'owner id',
  `posted` datetime NOT NULL,
  `format` enum('html','php','path') NOT NULL DEFAULT 'html' COMMENT 'format of the content',
  `title` varchar(255) NOT NULL,
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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='CMS Pages' AUTO_INCREMENT=61 ;

--
-- Dumping data for table `cms_page`
--

INSERT INTO `cms_page` (`id`, `code`, `url_id`, `application_id`, `type_id`, `user_id`, `posted`, `format`, `title`, `content`, `status`, `teaser`, `data`, `meta`, `content_type`) VALUES
(58, 'search', 'search', 1, 1, 1, '2014-10-23 16:08:03', 'path', 'Search', 'search.phtml', 'published', '', '{"teaser_image":""}', '{"keywords":"","description":""}', 'PUBLIC'),
(59, 'home', 'home', 1, 1, 1, '2014-09-04 11:38:48', 'path', 'Home', 'en/home.phtml', 'published', '', '{"teaser_image":""}', '{"keywords":"","description":"HORISEN is your home for cross-media marketing solutions including: web services, social media, mobile marketing, telecom, design and graphics and products."}', 'PUBLIC'),
(60, 'intro', 'intro', 1, 1, 1, '2014-09-04 11:38:48', 'path', 'Intro', 'en/intro.phtml', 'published', NULL, '{"teaser_image":""}', '{"keywords":"","description":""}', 'PUBLIC');

-- --------------------------------------------------------

--
-- Table structure for table `cms_page_tr`
--

DROP TABLE IF EXISTS `cms_page_tr`;
CREATE TABLE IF NOT EXISTS `cms_page_tr` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `language` char(5) NOT NULL,
  `translation_id` int(10) unsigned NOT NULL,
  `url_id` varchar(255) DEFAULT NULL COMMENT 'parmalink id part',
  `title` varchar(255) DEFAULT NULL,
  `content` text,
  `teaser` tinytext,
  `data` text COMMENT 'custom json data',
  `meta` text COMMENT 'meta json data',
  PRIMARY KEY (`id`),
  UNIQUE KEY `language` (`language`,`translation_id`),
  KEY `url_id` (`url_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='pages translations' AUTO_INCREMENT=107 ;

--
-- Dumping data for table `cms_page_tr`
--

INSERT INTO `cms_page_tr` (`id`, `language`, `translation_id`, `url_id`, `title`, `content`, `teaser`, `data`, `meta`) VALUES
(106, 'en', 58, 'search', 'Search', 'en/search.phtml', '', '{"teaser_image":""}', '{"keywords":"","description":""}'),
(101, 'de', 58, 'search', 'Search', 'de/search.phtml', '', '{"teaser_image":""}', '{"keywords":"","description":""}'),
(102, 'en', 59, 'home', 'Home', 'en/home.phtml', '', '{"teaser_image":""}', '{"keywords":"","description":"HORISEN is your home for cross-media marketing solutions including: web services, social media, mobile marketing, telecom, design and graphics and products."}'),
(103, 'de', 59, 'home', 'Home', 'de/home.phtml', NULL, '{"teaser_image":""}', '{"keywords":"","description":""}'),
(104, 'en', 60, 'intro', '', 'en/intro.phtml', NULL, '{"teaser_image":""}', '{"keywords":"","description":""}'),
(105, 'de', 60, 'intro', '', 'de/intro.phtml', NULL, '{"teaser_image":""}', '{"keywords":"","description":""}');

-- --------------------------------------------------------

--
-- Table structure for table `cms_page_type`
--

DROP TABLE IF EXISTS `cms_page_type`;
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

DROP TABLE IF EXISTS `cms_route`;
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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7 ;

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

-- --------------------------------------------------------

--
-- Table structure for table `contact`
--

DROP TABLE IF EXISTS `contact`;
CREATE TABLE IF NOT EXISTS `contact` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `application_id` int(10) unsigned NOT NULL,
  `posted` datetime NOT NULL,
  `first_name` varchar(60) NOT NULL,
  `last_name` varchar(60) NOT NULL,
  `company` varchar(100) DEFAULT NULL,
  `gender` varchar(10) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(60) DEFAULT NULL,
  `fax` varchar(60) DEFAULT NULL,
  `mobile` varchar(60) DEFAULT NULL,
  `country` varchar(100) NOT NULL,
  `zip` varchar(15) NOT NULL,
  `city` varchar(100) NOT NULL,
  `street` varchar(120) NOT NULL,
  `description` text,
  `message` text,
  `fileupload` text NOT NULL,
  `form_id` varchar(50) NOT NULL,
  `language` char(5) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `posted` (`posted`),
  KEY `application_id` (`application_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='contact form submissions' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `country`
--

DROP TABLE IF EXISTS `country`;
CREATE TABLE IF NOT EXISTS `country` (
  `id` smallint(5) unsigned NOT NULL DEFAULT '0',
  `name` varchar(50) NOT NULL DEFAULT '',
  `name_de` varchar(50) DEFAULT NULL,
  `code2` char(2) DEFAULT NULL,
  `code3` char(3) DEFAULT NULL,
  `domain` varchar(5) DEFAULT NULL,
  `dial_code` varchar(15) DEFAULT NULL,
  `currency` smallint(5) unsigned DEFAULT NULL,
  `mcc` varchar(4) DEFAULT NULL,
  `def_lang` char(2) NOT NULL DEFAULT 'EN',
  `continent` char(2) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_country1` (`code2`),
  KEY `idx_country2` (`code3`),
  KEY `continent` (`continent`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `country`
--

INSERT INTO `country` (`id`, `name`, `name_de`, `code2`, `code3`, `domain`, `dial_code`, `currency`, `mcc`, `def_lang`, `continent`) VALUES
(4, 'Afghanistan', NULL, 'AF', 'AFG', '.af', '93', NULL, NULL, 'EN', 'AS'),
(248, 'Aland Islands', NULL, 'AX', 'ALA', '.ax', NULL, NULL, NULL, 'EN', 'EU'),
(8, 'Albania', NULL, 'AL', 'ALB', '.al', '355', NULL, NULL, 'EN', 'EU'),
(12, 'Algeria', NULL, 'DZ', 'DZA', '.dz', '213', NULL, NULL, 'EN', 'AF'),
(16, 'American Samoa', NULL, 'AS', 'ASM', '.as', '684', NULL, NULL, 'EN', 'OC'),
(20, 'Andorra', NULL, 'AD', 'AND', '.ad', '376', 978, NULL, 'EN', 'EU'),
(24, 'Angola', NULL, 'AO', 'AGO', '.ao', '244', NULL, NULL, 'EN', 'AF'),
(660, 'Anguilla', NULL, 'AI', 'AIA', '.ai', '264', NULL, NULL, 'EN', 'NA'),
(10, 'Antarctica', NULL, 'AQ', 'ATA', '.aq', '672', NULL, NULL, 'EN', 'AN'),
(28, 'Antigua and Barbuda', NULL, 'AG', 'ATG', '.ag', '268', NULL, NULL, 'EN', 'NA'),
(32, 'Argentina', NULL, 'AR', 'ARG', '.ar', '54', NULL, NULL, 'EN', 'SA'),
(51, 'Armenia', NULL, 'AM', 'ARM', '.am', '374', NULL, NULL, 'EN', 'EU'),
(533, 'Aruba', NULL, 'AW', 'ABW', '.aw', '297', NULL, NULL, 'EN', 'NA'),
(36, 'Australia', NULL, 'AU', 'AUS', '.au', '61', NULL, NULL, 'EN', 'OC'),
(40, 'Austria', NULL, 'AT', 'AUT', '.at', '43', 978, NULL, 'DE', 'EU'),
(31, 'Azerbaijan', NULL, 'AZ', 'AZE', '.az', '994', NULL, NULL, 'EN', 'EU'),
(44, 'Bahamas', NULL, 'BS', 'BHS', '.bs', '242', NULL, NULL, 'EN', 'NA'),
(48, 'Bahrain', NULL, 'BH', 'BHR', '.bh', '973', NULL, NULL, 'EN', 'AS'),
(50, 'Bangladesh', NULL, 'BD', 'BGD', '.bd', '880', NULL, NULL, 'EN', 'AS'),
(52, 'Barbados', NULL, 'BB', 'BRB', '.bb', '246', NULL, NULL, 'EN', 'NA'),
(112, 'Belarus', NULL, 'BY', 'BLR', '.by', '375', NULL, NULL, 'EN', 'EU'),
(56, 'Belgium', NULL, 'BE', 'BEL', '.be', '32', 978, NULL, 'EN', 'EU'),
(84, 'Belize', NULL, 'BZ', 'BLZ', '.bz', '501', NULL, NULL, 'EN', 'NA'),
(204, 'Benin', NULL, 'BJ', 'BEN', '.bj', '229', NULL, NULL, 'EN', 'AF'),
(60, 'Bermuda', NULL, 'BM', 'BMU', '.bm', '441', NULL, NULL, 'EN', 'NA'),
(64, 'Bhutan', NULL, 'BT', 'BTN', '.bt', '975', NULL, NULL, 'EN', 'AS'),
(68, 'Bolivia', NULL, 'BO', 'BOL', '.bo', '591', NULL, NULL, 'EN', 'SA'),
(70, 'Bosnia and Herzegovina', NULL, 'BA', 'BIH', '.ba', '387', NULL, NULL, 'EN', 'EU'),
(72, 'Botswana', NULL, 'BW', 'BWA', '.bw', '267', NULL, NULL, 'EN', 'AF'),
(76, 'Brazil', NULL, 'BR', 'BRA', '.br', '55', NULL, NULL, 'EN', 'SA'),
(86, 'British Indian Ocean Territory', NULL, 'IO', 'IOT', '.io', NULL, NULL, NULL, 'EN', 'AS'),
(96, 'Brunei Darussalam', NULL, 'BN', 'BRN', '.bn', NULL, NULL, NULL, 'EN', 'AS'),
(100, 'Bulgaria', NULL, 'BG', 'BGR', '.bg', '359', NULL, NULL, 'EN', 'EU'),
(854, 'Burkina Faso', NULL, 'BF', 'BFA', '.bf', '226', NULL, NULL, 'EN', 'AF'),
(108, 'Burundi', NULL, 'BI', 'BDI', '.bi', '257', NULL, NULL, 'EN', 'AF'),
(116, 'Cambodia', NULL, 'KH', 'KHM', '.kh', '855', NULL, NULL, 'EN', 'AS'),
(120, 'Cameroon', NULL, 'CM', 'CMR', '.cm', '237', NULL, NULL, 'EN', 'AF'),
(124, 'Canada', NULL, 'CA', 'CAN', '.ca', '1', NULL, NULL, 'EN', 'NA'),
(132, 'Cape Verde', NULL, 'CV', 'CPV', '.cv', NULL, NULL, NULL, 'EN', 'AF'),
(136, 'Cayman Islands', NULL, 'KY', 'CYM', '.ky', '345', NULL, NULL, 'EN', 'NA'),
(140, 'Central African Republic', NULL, 'CF', 'CAF', '.cf', '236', NULL, NULL, 'EN', 'AF'),
(148, 'Chad', NULL, 'TD', 'TCD', '.td', '235', NULL, NULL, 'EN', 'AF'),
(152, 'Chile', NULL, 'CL', 'CHL', '.cl', '56', NULL, NULL, 'EN', 'SA'),
(156, 'China', NULL, 'CN', 'CHN', '.cn', '86', NULL, NULL, 'EN', 'AS'),
(162, 'Christmas Island', NULL, 'CX', 'CXR', '.cx', '61', NULL, NULL, 'EN', 'AS'),
(166, 'Cocos Islands', NULL, 'CC', 'CCK', '.cc', '61', NULL, NULL, 'EN', 'AS'),
(170, 'Colombia', NULL, 'CO', 'COL', '.co', '57', NULL, NULL, 'EN', 'SA'),
(174, 'Comoros', NULL, 'KM', 'COM', '.km', '269', NULL, NULL, 'EN', 'AF'),
(178, 'Congo, Republic of', NULL, 'CG', 'COG', '.cg', '', NULL, NULL, 'EN', 'AF'),
(180, 'Congo, Democratic Republic of', NULL, 'CD', 'COD', '.cd', NULL, NULL, NULL, 'EN', 'AF'),
(184, 'Cook Islands', NULL, 'CK', 'COK', '.ck', '682', NULL, NULL, 'EN', 'OC'),
(188, 'Costa Rica', NULL, 'CR', 'CRI', '.cr', '506', NULL, NULL, 'EN', 'NA'),
(384, 'Ivory Coast', NULL, 'CI', 'CIV', '.ci', NULL, NULL, NULL, 'EN', 'AF'),
(191, 'Croatia', NULL, 'HR', 'HRV', '.hr', '385', NULL, NULL, 'EN', 'EU'),
(192, 'Cuba', NULL, 'CU', 'CUB', '.cu', '53', NULL, NULL, 'EN', 'NA'),
(196, 'Cyprus', NULL, 'CY', 'CYP', '.cy', '357', NULL, NULL, 'EN', 'EU'),
(203, 'Czech Republic', NULL, 'CZ', 'CZE', '.cz', '420', NULL, NULL, 'EN', 'EU'),
(208, 'Denmark', NULL, 'DK', 'DNK', '.dk', '45', NULL, NULL, 'EN', 'EU'),
(262, 'Djibouti', NULL, 'DJ', 'DJI', '.dj', '253', NULL, NULL, 'EN', 'AF'),
(212, 'Dominica', NULL, 'DM', 'DMA', '.dm', '767', NULL, NULL, 'EN', 'NA'),
(214, 'Dominican Republic', NULL, 'DO', 'DOM', '.do', '809', NULL, NULL, 'EN', 'NA'),
(218, 'Ecuador', NULL, 'EC', 'ECU', '.ec', '593', NULL, NULL, 'EN', 'SA'),
(818, 'Egypt', NULL, 'EG', 'EGY', '.eg', '20', NULL, NULL, 'EN', 'AF'),
(222, 'El Salvador', NULL, 'SV', 'SLV', '.sv', '503', NULL, NULL, 'EN', 'NA'),
(226, 'Equatorial Guinea', NULL, 'GQ', 'GNQ', '.gq', '240', NULL, NULL, 'EN', 'AF'),
(232, 'Eritrea', NULL, 'ER', 'ERI', '.er', '291', NULL, NULL, 'EN', 'AF'),
(233, 'Estonia', NULL, 'EE', 'EST', '.ee', '372', NULL, NULL, 'EN', 'EU'),
(231, 'Ethiopia', NULL, 'ET', 'ETH', '.et', '251', NULL, NULL, 'EN', 'AF'),
(234, 'Faeroe Islands', NULL, 'FO', 'FRO', '.fo', '298', NULL, NULL, 'EN', 'EU'),
(238, 'Falkland Islands', NULL, 'FK', 'FLK', '.fk', '500', NULL, NULL, 'EN', 'SA'),
(242, 'Fiji', NULL, 'FJ', 'FJI', '.fj', '679', NULL, NULL, 'EN', 'OC'),
(246, 'Finland', NULL, 'FI', 'FIN', '.fi', '358', 978, NULL, 'EN', 'EU'),
(250, 'France', NULL, 'FR', 'FRA', '.fr', '33', 978, NULL, 'FR', 'EU'),
(254, 'French Guiana', NULL, 'GF', 'GUF', '.gf', '594', NULL, NULL, 'EN', 'SA'),
(258, 'French Polynesia', NULL, 'PF', 'PYF', '.pf', '689', NULL, NULL, 'EN', 'OC'),
(260, 'French Southern Territories', NULL, 'TF', 'ATF', '.tf', NULL, NULL, NULL, 'EN', 'AN'),
(266, 'Gabon', NULL, 'GA', 'GAB', '.ga', '241', NULL, NULL, 'EN', 'AF'),
(270, 'Gambia, the', NULL, 'GM', 'GMB', '.gm', '220', NULL, NULL, 'EN', 'AF'),
(268, 'Georgia', NULL, 'GE', 'GEO', '.ge', '995', NULL, NULL, 'EN', 'EU'),
(276, 'Germany', NULL, 'DE', 'DEU', '.de', '49', 978, NULL, 'DE', 'EU'),
(288, 'Ghana', NULL, 'GH', 'GHA', '.gh', '233', NULL, NULL, 'EN', 'AF'),
(292, 'Gibraltar', NULL, 'GI', 'GIB', '.gi', '350', NULL, NULL, 'EN', 'EU'),
(826, 'United Kingdom', NULL, 'GB', 'GBR', '.uk', '44', 826, NULL, 'EN', 'EU'),
(300, 'Greece', NULL, 'GR', 'GRC', '.gr', '30', 978, NULL, 'EN', 'EU'),
(304, 'Greenland', NULL, 'GL', 'GRL', '.gl', '299', NULL, NULL, 'EN', 'NA'),
(308, 'Grenada', NULL, 'GD', 'GRD', '.gd', NULL, NULL, NULL, 'EN', 'NA'),
(312, 'Guadeloupe', NULL, 'GP', 'GLP', '.gp', '590', NULL, NULL, 'EN', 'NA'),
(316, 'Guam', NULL, 'GU', 'GUM', '.gu', '671', NULL, NULL, 'EN', 'OC'),
(320, 'Guatemala', NULL, 'GT', 'GTM', '.gt', '502', NULL, NULL, 'EN', 'NA'),
(324, 'Guinea', NULL, 'GN', 'GIN', '.gn', '224', NULL, NULL, 'EN', 'AF'),
(624, 'Guinea-Bissau', NULL, 'GW', 'GNB', '.gw', '245', NULL, NULL, 'EN', 'AF'),
(328, 'Guyana', NULL, 'GY', 'GUY', '.gy', '592', NULL, NULL, 'EN', 'SA'),
(332, 'Haiti', NULL, 'HT', 'HTI', '.ht', '509', NULL, NULL, 'EN', 'NA'),
(334, 'Heard Island and Mcdonald Islands', NULL, 'HM', 'HMD', '.hm', '', NULL, NULL, 'EN', 'AN'),
(340, 'Honduras', NULL, 'HN', 'HND', '.hn', '504', NULL, NULL, 'EN', 'NA'),
(344, 'Hong Kong', NULL, 'HK', 'HKG', '.hk', '852', NULL, NULL, 'EN', 'AS'),
(348, 'Hungary', NULL, 'HU', 'HUN', '.hu', '36', NULL, NULL, 'EN', 'EU'),
(352, 'Iceland', NULL, 'IS', 'ISL', '.is', '354', NULL, NULL, 'EN', 'EU'),
(356, 'India', NULL, 'IN', 'IND', '.in', '91', NULL, NULL, 'EN', 'AS'),
(360, 'Indonesia', NULL, 'ID', 'IDN', '.id', '62', NULL, NULL, 'EN', 'AS'),
(364, 'Iran', NULL, 'IR', 'IRN', '.ir', '98', NULL, NULL, 'EN', 'AS'),
(368, 'Iraq', NULL, 'IQ', 'IRQ', '.iq', '964', NULL, NULL, 'EN', 'AS'),
(372, 'Ireland', NULL, 'IE', 'IRL', '.ie', '353', 978, NULL, 'EN', 'EU'),
(833, 'Isle of Man', NULL, 'IM', 'IMN', '.im', NULL, NULL, NULL, 'EN', 'EU'),
(376, 'Israel', NULL, 'IL', 'ISR', '.il', '972', NULL, NULL, 'EN', 'AS'),
(380, 'Italy', NULL, 'IT', 'ITA', '.it', '39', 978, NULL, 'EN', 'EU'),
(388, 'Jamaica', NULL, 'JM', 'JAM', '.jm', '876', NULL, NULL, 'EN', 'NA'),
(392, 'Japan', NULL, 'JP', 'JPN', '.jp', '81', NULL, NULL, 'EN', 'AS'),
(400, 'Jordan', NULL, 'JO', 'JOR', '.jo', '962', NULL, NULL, 'EN', 'AS'),
(398, 'Kazakhstan', NULL, 'KZ', 'KAZ', '.kz', '7', NULL, NULL, 'EN', 'EU'),
(404, 'Kenya', NULL, 'KE', 'KEN', '.ke', '254', NULL, NULL, 'EN', 'AF'),
(296, 'Kiribati', NULL, 'KI', 'KIR', '.ki', '686', NULL, NULL, 'EN', 'OC'),
(408, 'Korea, Dem. Peoples Republic of', NULL, 'KP', 'PRK', '.kp', NULL, NULL, NULL, 'EN', 'AS'),
(410, 'Korea, Republic of', NULL, 'KR', 'KOR', '.kr', NULL, NULL, NULL, 'EN', 'AS'),
(414, 'Kuwait', NULL, 'KW', 'KWT', '.kw', '965', NULL, NULL, 'EN', 'AS'),
(417, 'Kyrgyzstan', NULL, 'KG', 'KGZ', '.kg', '996', NULL, NULL, 'EN', 'AS'),
(418, 'Lao Peoples Dem. Republic', NULL, 'LA', 'LAO', '.la', '856', NULL, NULL, 'EN', 'AS'),
(428, 'Latvia', NULL, 'LV', 'LVA', '.lv', '371', NULL, NULL, 'EN', 'EU'),
(422, 'Lebanon', NULL, 'LB', 'LBN', '.lb', '961', NULL, NULL, 'EN', 'AS'),
(426, 'Lesotho', NULL, 'LS', 'LSO', '.ls', '266', NULL, NULL, 'EN', 'AF'),
(430, 'Liberia', NULL, 'LR', 'LBR', '.lr', '231', NULL, NULL, 'EN', 'AF'),
(434, 'Libya', NULL, 'LY', 'LBY', '.ly', '218', NULL, NULL, 'EN', 'AF'),
(438, 'Liechtenstein', NULL, 'LI', 'LIE', '.li', '423', NULL, NULL, 'EN', 'EU'),
(440, 'Lithuania', NULL, 'LT', 'LTU', '.lt', '370', NULL, NULL, 'EN', 'EU'),
(442, 'Luxembourg', NULL, 'LU', 'LUX', '.lu', '352', 978, NULL, 'EN', 'EU'),
(446, 'Macao', NULL, 'MO', 'MAC', '.mo', '853', NULL, NULL, 'EN', 'AS'),
(807, 'Macedonia, Former Yug. Republic of', NULL, 'MK', 'MKD', '.mk', '389', NULL, NULL, 'EN', 'EU'),
(450, 'Madagascar', NULL, 'MG', 'MDG', '.mg', '261', NULL, NULL, 'EN', 'AF'),
(454, 'Malawi', NULL, 'MW', 'MWI', '.mw', '265', NULL, NULL, 'EN', 'AF'),
(458, 'Malaysia', NULL, 'MY', 'MYS', '.my', '60', NULL, NULL, 'EN', 'AS'),
(462, 'Maldives', NULL, 'MV', 'MDV', '.mv', '960', NULL, NULL, 'EN', 'AS'),
(466, 'Mali', NULL, 'ML', 'MLI', '.ml', '223', NULL, NULL, 'EN', 'AF'),
(470, 'Malta', NULL, 'MT', 'MLT', '.mt', '356', NULL, NULL, 'EN', 'EU'),
(584, 'Marshall Islands', NULL, 'MH', 'MHL', '.mh', '', NULL, NULL, 'EN', 'OC'),
(474, 'Martinique', NULL, 'MQ', 'MTQ', '.mq', '596', NULL, NULL, 'EN', 'NA'),
(478, 'Mauritania', NULL, 'MR', 'MRT', '.mr', '222', NULL, NULL, 'EN', 'AF'),
(480, 'Mauritius', NULL, 'MU', 'MUS', '.mu', '230', NULL, NULL, 'EN', 'AF'),
(175, 'Mayotte', NULL, 'YT', 'MYT', '.yt', '269', NULL, NULL, 'EN', 'AF'),
(484, 'Mexico', NULL, 'MX', 'MEX', '.mx', '52', NULL, NULL, 'EN', 'NA'),
(583, 'Micronesia', NULL, 'FM', 'FSM', '.fm', '691', NULL, NULL, 'EN', 'OC'),
(498, 'Moldova', NULL, 'MD', 'MDA', '.md', '373', NULL, NULL, 'EN', 'EU'),
(492, 'Monaco', NULL, 'MC', 'MCO', '.mc', '377', 978, NULL, 'EN', 'EU'),
(496, 'Mongolia', NULL, 'MN', 'MNG', '.mn', '377', NULL, NULL, 'EN', 'AS'),
(500, 'Montserrat', NULL, 'MS', 'MSR', '.ms', '664', NULL, NULL, 'EN', 'NA'),
(504, 'Morocco', NULL, 'MA', 'MAR', '.ma', '212', NULL, NULL, 'EN', 'AF'),
(508, 'Mozambique', NULL, 'MZ', 'MOZ', '.mz', '258', NULL, NULL, 'EN', 'AF'),
(104, 'Myanmar', NULL, 'MM', 'MMR', '.mm', '95', NULL, NULL, 'EN', 'AS'),
(516, 'Namibia', NULL, 'NA', 'NAM', '.na', '264', NULL, NULL, 'EN', 'AF'),
(520, 'Nauru', NULL, 'NR', 'NRU', '.nr', '674', NULL, NULL, 'EN', 'OC'),
(524, 'Nepal', NULL, 'NP', 'NPL', '.np', '977', NULL, NULL, 'EN', 'AS'),
(528, 'Netherlands', NULL, 'NL', 'NLD', '.nl', '31', 978, NULL, 'EN', 'EU'),
(530, 'Netherlands Antilles', NULL, 'AN', 'ANT', '.an', '599', NULL, NULL, 'EN', 'NA'),
(540, 'New Caledonia', NULL, 'NC', 'NCL', '.nc', '687', NULL, NULL, 'EN', 'OC'),
(554, 'New Zealand', NULL, 'NZ', 'NZL', '.nz', '64', NULL, NULL, 'EN', 'OC'),
(558, 'Nicaragua', NULL, 'NI', 'NIC', '.ni', '505', NULL, NULL, 'EN', 'NA'),
(562, 'Niger', NULL, 'NE', 'NER', '.ne', '', NULL, NULL, 'EN', 'AF'),
(566, 'Nigeria', NULL, 'NG', 'NGA', '.ng', '234', NULL, NULL, 'EN', 'AF'),
(570, 'Niue', NULL, 'NU', 'NIU', '.nu', '683', NULL, NULL, 'EN', 'OC'),
(574, 'Norfolk Island', NULL, 'NF', 'NFK', '.nf', '672', NULL, NULL, 'EN', 'OC'),
(580, 'Northern Mariana Islands', NULL, 'MP', 'MNP', '.mp', '', NULL, NULL, 'EN', 'OC'),
(578, 'Norway', NULL, 'NO', 'NOR', '.no', '47', NULL, NULL, 'EN', 'EU'),
(512, 'Oman', NULL, 'OM', 'OMN', '.om', '968', NULL, NULL, 'EN', 'AS'),
(586, 'Pakistan', NULL, 'PK', 'PAK', '.pk', '92', NULL, NULL, 'EN', 'AS'),
(585, 'Palau', NULL, 'PW', 'PLW', '.pw', '680', NULL, NULL, 'EN', 'OC'),
(275, 'Palestinian Territories', NULL, 'PS', 'PSE', '.ps', NULL, NULL, NULL, 'EN', 'AS'),
(591, 'Panama', NULL, 'PA', 'PAN', '.pa', '507', NULL, NULL, 'EN', 'NA'),
(598, 'Papua New Guinea', NULL, 'PG', 'PNG', '.pg', '675', NULL, NULL, 'EN', 'OC'),
(600, 'Paraguay', NULL, 'PY', 'PRY', '.py', '595', NULL, NULL, 'EN', 'SA'),
(604, 'Peru', NULL, 'PE', 'PER', '.pe', '51', NULL, NULL, 'EN', 'SA'),
(608, 'Philippines', NULL, 'PH', 'PHL', '.ph', '63', NULL, NULL, 'EN', 'AS'),
(612, 'Pitcairn', NULL, 'PN', 'PCN', '.pn', NULL, NULL, NULL, 'EN', 'OC'),
(616, 'Poland', NULL, 'PL', 'POL', '.pl', '48', NULL, NULL, 'EN', 'EU'),
(620, 'Portugal', NULL, 'PT', 'PRT', '.pt', '351', 978, NULL, 'EN', 'EU'),
(630, 'Puerto Rico', NULL, 'PR', 'PRI', '.pr', '787', NULL, NULL, 'EN', 'NA'),
(634, 'Qatar', NULL, 'QA', 'QAT', '.qa', '974', NULL, NULL, 'EN', 'AS'),
(638, 'Réunion', NULL, 'RE', 'REU', '.re', '262', NULL, NULL, 'EN', 'AF'),
(642, 'Romania', NULL, 'RO', 'ROU', '.ro', '40', NULL, NULL, 'EN', 'EU'),
(643, 'Russian Federation', NULL, 'RU', 'RUS', '.ru', '7', NULL, NULL, 'EN', 'EU'),
(646, 'Rwanda', NULL, 'RW', 'RWA', '.rw', '250', NULL, NULL, 'EN', 'AF'),
(654, 'Saint Helena', NULL, 'SH', 'SHN', '.sh', NULL, NULL, NULL, 'EN', 'AF'),
(659, 'Saint Kitts and Nevis', NULL, 'KN', 'KNA', '.kn', '', NULL, NULL, 'EN', 'NA'),
(662, 'Saint Lucia', NULL, 'LC', 'LCA', '.lc', NULL, NULL, NULL, 'EN', 'NA'),
(666, 'Saint Pierre and Miquelon', NULL, 'PM', 'SPM', '.pm', NULL, NULL, NULL, 'EN', 'NA'),
(670, 'Saint Vincent and the Grenadines', NULL, 'VC', 'VCT', '.vc', NULL, NULL, NULL, 'EN', 'NA'),
(882, 'Samoa', NULL, 'WS', 'WSM', '.ws', NULL, NULL, NULL, 'EN', 'OC'),
(674, 'San marino', NULL, 'SM', 'SMR', '.sm', '378', 978, NULL, 'EN', 'EU'),
(678, 'Sao Tome and Principe', NULL, 'ST', 'STP', '.st', '239', NULL, NULL, 'EN', 'AF'),
(682, 'Saudi Arabia', NULL, 'SA', 'SAU', '.sa', '966', NULL, NULL, 'EN', 'AS'),
(686, 'Senegal', NULL, 'SN', 'SEN', '.sn', '221', NULL, NULL, 'EN', 'AF'),
(688, 'Serbia', NULL, 'RS', 'SRB', '.rs', '381', 941, NULL, 'EN', 'EU'),
(690, 'Seychelles', NULL, 'SC', 'SYC', '.sc', '248', NULL, NULL, 'EN', 'AF'),
(694, 'Sierra Leone', NULL, 'SL', 'SLE', '.sl', '232', NULL, NULL, 'EN', 'AF'),
(702, 'Singapore', NULL, 'SG', 'SGP', '.sg', '65', NULL, NULL, 'EN', 'AS'),
(703, 'Slovakia', NULL, 'SK', 'SVK', '.sk', '421', NULL, NULL, 'EN', 'EU'),
(705, 'Slovenia', NULL, 'SI', 'SVN', '.si', '386', 978, NULL, 'EN', 'EU'),
(90, 'Solomon Islands', NULL, 'SB', 'SLB', '.sb', '677', NULL, NULL, 'EN', 'OC'),
(706, 'Somalia', NULL, 'SO', 'SOM', '.so', '252', NULL, NULL, 'EN', 'AF'),
(710, 'South Africa', NULL, 'ZA', 'ZAF', '.za', '27', NULL, NULL, 'EN', 'AF'),
(239, 'South Georgia', NULL, 'GS', 'SGS', '.gs', NULL, NULL, NULL, 'EN', 'AN'),
(724, 'Spain', NULL, 'ES', 'ESP', '.es', '34', 978, NULL, 'EN', 'EU'),
(144, 'Sri Lanka', NULL, 'LK', 'LKA', '.lk', '94', NULL, NULL, 'EN', 'AS'),
(736, 'Sudan', NULL, 'SD', 'SDN', '.sd', '249', NULL, NULL, 'EN', 'AF'),
(740, 'Suriname', NULL, 'SR', 'SUR', '.sr', '597', NULL, NULL, 'EN', 'SA'),
(744, 'Svalbard and Jan Mayen', NULL, 'SJ', 'SJM', '.sj', NULL, NULL, NULL, 'EN', 'EU'),
(748, 'Swaziland', NULL, 'SZ', 'SWZ', '.sz', NULL, NULL, NULL, 'EN', 'AF'),
(752, 'Sweden', NULL, 'SE', 'SWE', '.se', '46', NULL, NULL, 'EN', 'EU'),
(756, 'Switzerland', NULL, 'CH', 'CHE', '.ch', '41', 756, NULL, 'DE', 'EU'),
(760, 'Syrian Arab Republic', NULL, 'SY', 'SYR', '.sy', NULL, NULL, NULL, 'EN', 'AS'),
(158, 'Taiwan', NULL, 'TW', 'TWN', '.tw', '886', NULL, NULL, 'EN', 'AS'),
(762, 'Tajikistan', NULL, 'TJ', 'TJK', '.tj', '992', NULL, NULL, 'EN', 'AS'),
(834, 'Tanzania', NULL, 'TZ', 'TZA', '.tz', '255', NULL, NULL, 'EN', 'AF'),
(764, 'Thailand', NULL, 'TH', 'THA', '.th', '66', NULL, NULL, 'EN', 'AS'),
(626, 'Timor-Leste', NULL, 'TL', 'TLS', '.tp', NULL, NULL, NULL, 'EN', 'AS'),
(768, 'Togo', NULL, 'TG', 'TGO', '.tg', '228', NULL, NULL, 'EN', 'AF'),
(772, 'Tokelau', NULL, 'TK', 'TKL', '.tk', '690', NULL, NULL, 'EN', 'OC'),
(776, 'Tonga', NULL, 'TO', 'TON', '.to', '676', NULL, NULL, 'EN', 'OC'),
(780, 'Trinidad and Tobago', NULL, 'TT', 'TTO', '.tt', '868', NULL, NULL, 'EN', 'NA'),
(788, 'Tunisia', NULL, 'TN', 'TUN', '.tn', '216', NULL, NULL, 'EN', 'AF'),
(792, 'Turkey', NULL, 'TR', 'TUR', '.tr', '90', NULL, NULL, 'EN', 'EU'),
(795, 'Turkmenistan', NULL, 'TM', 'TKM', '.tm', '993', NULL, NULL, 'EN', 'AS'),
(796, 'Turks and Caicos Islands', NULL, 'TC', 'TCA', '.tc', '649', NULL, NULL, 'EN', 'NA'),
(798, 'Tuvalu', NULL, 'TV', 'TUV', '.tv', '688', NULL, NULL, 'EN', 'OC'),
(800, 'Uganda', NULL, 'UG', 'UGA', '.ug', '256', NULL, NULL, 'EN', 'AF'),
(804, 'Ukraine', NULL, 'UA', 'UKR', '.ua', '380', NULL, NULL, 'EN', 'EU'),
(784, 'United Arab Emirates', NULL, 'AE', 'ARE', '.ae', '380', NULL, NULL, 'EN', 'AS'),
(840, 'United States', NULL, 'US', 'USA', '.us', '1', 840, NULL, 'EN', 'NA'),
(581, 'United States Minor Outlying Islands', NULL, 'UM', 'UMI', '.um', NULL, NULL, NULL, 'EN', 'OC'),
(858, 'Uruguay', NULL, 'UY', 'URY', '.uy', '598', NULL, NULL, 'EN', 'SA'),
(860, 'Uzbekistan', NULL, 'UZ', 'UZB', '.uz', '998', NULL, NULL, 'EN', 'AS'),
(548, 'Vanuatu', NULL, 'VU', 'VUT', '.vu', '678', NULL, NULL, 'EN', 'OC'),
(336, 'Vatican City', NULL, 'VA', 'VAT', '.va', '39', 978, NULL, 'EN', 'EU'),
(862, 'Venezuela', NULL, 'VE', 'VEN', '.ve', '58', NULL, NULL, 'EN', 'SA'),
(704, 'Viet Nam', NULL, 'VN', 'VNM', '.vn', '84', NULL, NULL, 'EN', 'AS'),
(92, 'Virgin Islands, British', NULL, 'VG', 'VGB', '.vg', NULL, NULL, NULL, 'EN', 'NA'),
(850, 'Virgin Islands, US', NULL, 'VI', 'VIR', '.vi', NULL, NULL, NULL, 'EN', 'NA'),
(876, 'Wallis and Futuna', NULL, 'WF', 'WLF', '.wf', '681', NULL, NULL, 'EN', 'OC'),
(732, 'Western Sahara', NULL, 'EH', 'ESH', '.eh', '685', NULL, NULL, 'EN', 'AF'),
(887, 'Yemen', NULL, 'YE', 'YEM', '.ye', '967', NULL, NULL, 'EN', 'AS'),
(894, 'Zambia', NULL, 'ZM', 'ZMB', '.zm', '260', NULL, NULL, 'EN', 'AF'),
(716, 'Zimbabwe', NULL, 'ZW', 'ZWE', '.zw', '263', NULL, NULL, 'EN', 'AF'),
(1000, 'Other Country', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'EN', NULL),
(499, 'Montenegro', NULL, 'ME', 'MNE', '.me', '382', 978, NULL, 'EN', 'EU');

-- --------------------------------------------------------

--
-- Table structure for table `country_geoip`
--

DROP TABLE IF EXISTS `country_geoip`;
CREATE TABLE IF NOT EXISTS `country_geoip` (
  `min_ip` char(15) NOT NULL,
  `max_ip` char(15) NOT NULL,
  `min_long` int(10) unsigned NOT NULL,
  `max_long` int(10) unsigned NOT NULL,
  `country_code2` char(2) NOT NULL,
  `country_name` varchar(20) NOT NULL,
  KEY `range` (`min_long`,`max_long`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='country geo ip ranges';

-- --------------------------------------------------------

--
-- Table structure for table `country_translate`
--

DROP TABLE IF EXISTS `country_translate`;
CREATE TABLE IF NOT EXISTS `country_translate` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `code2` char(2) NOT NULL,
  `language` varchar(5) NOT NULL,
  `value` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code2` (`code2`,`language`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='country translations' AUTO_INCREMENT=949 ;

--
-- Dumping data for table `country_translate`
--

INSERT INTO `country_translate` (`id`, `code2`, `language`, `value`) VALUES
(1, 'AD', 'en', 'Andorra'),
(2, 'AD', 'de', 'Andorra'),
(3, 'AD', 'it', 'Andorra'),
(4, 'AE', 'en', 'United Arab Emirates'),
(5, 'AE', 'de', 'Vereinigte Arabische Emirate'),
(6, 'AE', 'it', 'Emirati arabi uniti'),
(7, 'AF', 'en', 'Afghanistan'),
(8, 'AF', 'de', 'Afghanistan'),
(9, 'AF', 'it', 'Afghanistan'),
(10, 'AG', 'en', 'Antigua and Barbuda'),
(11, 'AG', 'de', 'Antigua und Barbuda'),
(12, 'AG', 'it', 'Antigua e Barbuda'),
(13, 'AI', 'en', 'Anguilla'),
(14, 'AI', 'de', 'Anguilla'),
(15, 'AI', 'it', 'Anguilla'),
(16, 'AL', 'en', 'Albania'),
(17, 'AL', 'de', 'Albanien'),
(18, 'AL', 'it', 'Albania'),
(19, 'AM', 'en', 'Armenia'),
(20, 'AM', 'de', 'Armenien'),
(21, 'AM', 'it', 'Armenia'),
(22, 'AN', 'en', 'Netherlands Antilles'),
(23, 'AN', 'de', 'Niederländische Antillen'),
(24, 'AN', 'it', 'Antille olandesi'),
(25, 'AO', 'en', 'Angola'),
(26, 'AO', 'de', 'Angola'),
(27, 'AO', 'it', 'Angola'),
(28, 'AQ', 'en', 'Antarctica'),
(29, 'AQ', 'de', 'Antarktis'),
(30, 'AQ', 'it', 'Antartide'),
(31, 'AR', 'en', 'Argentina'),
(32, 'AR', 'de', 'Argentinien'),
(33, 'AR', 'it', 'Argentina'),
(34, 'AS', 'en', 'American Samoa'),
(35, 'AS', 'de', 'Amerikanisch-Samoa'),
(36, 'AS', 'it', 'Samoa americane'),
(37, 'AT', 'en', 'Austria'),
(38, 'AT', 'de', 'Österreich'),
(39, 'AT', 'it', 'Austria'),
(40, 'AU', 'en', 'Australia'),
(41, 'AU', 'de', 'Australien'),
(42, 'AU', 'it', 'Australia'),
(43, 'AW', 'en', 'Aruba'),
(44, 'AW', 'de', 'Aruba'),
(45, 'AW', 'it', 'Aruba'),
(46, 'AZ', 'en', 'Azerbaijan'),
(47, 'AZ', 'de', 'Aserbaidschan'),
(48, 'AZ', 'it', 'Azerbaigian'),
(49, 'BA', 'en', 'Bosnia and Herzegovina'),
(50, 'BA', 'de', 'Bosnien und Herzegowina'),
(51, 'BA', 'it', 'Bosnia-Erzegovina'),
(52, 'BB', 'en', 'Barbados'),
(53, 'BB', 'de', 'Barbados'),
(54, 'BB', 'it', 'Barbados'),
(55, 'BD', 'en', 'Bangladesh'),
(56, 'BD', 'de', 'Bangladesch'),
(57, 'BD', 'it', 'Bangladesh'),
(58, 'BE', 'en', 'Belgium'),
(59, 'BE', 'de', 'Belgien'),
(60, 'BE', 'it', 'Belgio'),
(61, 'BF', 'en', 'Burkina Faso'),
(62, 'BF', 'de', 'Burkina Faso'),
(63, 'BF', 'it', 'Burkina-Faso'),
(64, 'BG', 'en', 'Bulgaria'),
(65, 'BG', 'de', 'Bulgarien'),
(66, 'BG', 'it', 'Bulgaria'),
(67, 'BH', 'en', 'Bahrain'),
(68, 'BH', 'de', 'Bahrain'),
(69, 'BH', 'it', 'Bahrein'),
(70, 'BI', 'en', 'Burundi'),
(71, 'BI', 'de', 'Burundi'),
(72, 'BI', 'it', 'Burundi'),
(73, 'BJ', 'en', 'Benin'),
(74, 'BJ', 'de', 'Benin'),
(75, 'BJ', 'it', 'Benin'),
(76, 'BM', 'en', 'Bermuda'),
(77, 'BM', 'de', 'Bermuda'),
(78, 'BM', 'it', 'Bermuda'),
(79, 'BN', 'en', 'Brunei Darussalam'),
(80, 'BN', 'de', 'Brunei Darussalam'),
(81, 'BN', 'it', 'Brunei Darussalam'),
(82, 'BO', 'en', 'Bolivia'),
(83, 'BO', 'de', 'Bolivien'),
(84, 'BO', 'it', 'Bolivia'),
(85, 'BR', 'en', 'Brazil'),
(86, 'BR', 'de', 'Brasilien'),
(87, 'BR', 'it', 'Brasile'),
(88, 'BS', 'en', 'Bahamas'),
(89, 'BS', 'de', 'Bahamas'),
(90, 'BS', 'it', 'Bahamas'),
(91, 'BT', 'en', 'Bhutan'),
(92, 'BT', 'de', 'Bhutan'),
(93, 'BT', 'it', 'Bhutan'),
(94, 'BW', 'en', 'Botswana'),
(95, 'BW', 'de', 'Botsuana'),
(96, 'BW', 'it', 'Botswana'),
(97, 'BY', 'en', 'Belarus'),
(98, 'BY', 'de', 'Weißrussland'),
(99, 'BY', 'it', 'Bielorussia'),
(100, 'BZ', 'en', 'Belize'),
(101, 'BZ', 'de', 'Belize'),
(102, 'BZ', 'it', 'Belize'),
(103, 'CA', 'en', 'Canada'),
(104, 'CA', 'de', 'Kanada'),
(105, 'CA', 'it', 'Canada'),
(106, 'CC', 'en', 'Cocos (Keeling) Islands'),
(107, 'CC', 'de', 'Kokosinseln (Keelinginseln)'),
(108, 'CC', 'it', 'Isole Cocos (Keeling)'),
(109, 'CD', 'en', 'Congo, Democratic Republic of the'),
(110, 'CD', 'de', 'Demokratische Republik Kongo'),
(111, 'CD', 'it', 'Repubblica democratica del Congo'),
(112, 'CF', 'en', 'Central African Republic'),
(113, 'CF', 'de', 'Zentralafrikanische Republik'),
(114, 'CF', 'it', 'Repubblica centrafricana'),
(115, 'CG', 'en', 'Congo, Republic of the'),
(116, 'CG', 'de', 'Kongo (Republik Kongo)'),
(117, 'CG', 'it', 'Repubblica del Congo'),
(118, 'CH', 'en', 'Switzerland'),
(119, 'CH', 'de', 'Schweiz'),
(120, 'CH', 'it', 'Svizzera'),
(121, 'CI', 'en', 'Côte d''Ivoire'),
(122, 'CI', 'de', 'Côte d''Ivoire'),
(123, 'CI', 'it', 'Costa d''Avorio'),
(124, 'CK', 'en', 'Cook Islands'),
(125, 'CK', 'de', 'Cookinseln'),
(126, 'CK', 'it', 'Isole Cook'),
(127, 'CL', 'en', 'Chile'),
(128, 'CL', 'de', 'Chile'),
(129, 'CL', 'it', 'Cile'),
(130, 'CM', 'en', 'Cameroon'),
(131, 'CM', 'de', 'Kamerun'),
(132, 'CM', 'it', 'Camerun'),
(133, 'CN', 'en', 'China'),
(134, 'CN', 'de', 'China'),
(135, 'CN', 'it', 'Cina'),
(136, 'CO', 'en', 'Colombia'),
(137, 'CO', 'de', 'Kolumbien'),
(138, 'CO', 'it', 'Colombia'),
(139, 'CR', 'en', 'Costa Rica'),
(140, 'CR', 'de', 'Costa Rica'),
(141, 'CR', 'it', 'Costa Rica'),
(142, 'CU', 'en', 'Cuba'),
(143, 'CU', 'de', 'Kuba'),
(144, 'CU', 'it', 'Cuba'),
(145, 'CV', 'en', 'Cape Verde'),
(146, 'CV', 'de', 'Kap Verde'),
(147, 'CV', 'it', 'Capo Verde'),
(148, 'CX', 'en', 'Christmas Island'),
(149, 'CX', 'de', 'Weihnachtsinsel'),
(150, 'CX', 'it', 'Isole Christmas'),
(151, 'CY', 'en', 'Cyprus'),
(152, 'CY', 'de', 'Zypern'),
(153, 'CY', 'it', 'Cipro'),
(154, 'CZ', 'en', 'Czech Republic'),
(155, 'CZ', 'de', 'Tschechische Republik'),
(156, 'CZ', 'it', 'Repubblica ceca'),
(157, 'DE', 'en', 'Germany'),
(158, 'DE', 'de', 'Deutschland'),
(159, 'DE', 'it', 'Germania'),
(160, 'DJ', 'en', 'Djibouti'),
(161, 'DJ', 'de', 'Dschibuti'),
(162, 'DJ', 'it', 'Gibuti'),
(163, 'DK', 'en', 'Denmark'),
(164, 'DK', 'de', 'Dänemark'),
(165, 'DK', 'it', 'Danimarca'),
(166, 'DM', 'en', 'Dominica'),
(167, 'DM', 'de', 'Dominica'),
(168, 'DM', 'it', 'Dominica'),
(169, 'DO', 'en', 'Dominican Republic'),
(170, 'DO', 'de', 'Dominikanische Republik'),
(171, 'DO', 'it', 'Repubblica dominicana'),
(172, 'DZ', 'en', 'Algeria'),
(173, 'DZ', 'de', 'Algerien'),
(174, 'DZ', 'it', 'Algeria'),
(175, 'EC', 'en', 'Ecuador'),
(176, 'EC', 'de', 'Ecuador'),
(177, 'EC', 'it', 'Ecuador'),
(178, 'EE', 'en', 'Estonia'),
(179, 'EE', 'de', 'Estland'),
(180, 'EE', 'it', 'Estonia'),
(181, 'EG', 'en', 'Egypt'),
(182, 'EG', 'de', 'Ägypten'),
(183, 'EG', 'it', 'Egitto'),
(184, 'EH', 'en', 'Western Sahara'),
(185, 'EH', 'de', 'Westsahara'),
(186, 'EH', 'it', 'Sahara occidentale'),
(187, 'ER', 'en', 'Eritrea'),
(188, 'ER', 'de', 'Eritrea'),
(189, 'ER', 'it', 'Eritrea'),
(190, 'ES', 'en', 'Spain'),
(191, 'ES', 'de', 'Spanien'),
(192, 'ES', 'it', 'Spagna'),
(193, 'ET', 'en', 'Ethiopia'),
(194, 'ET', 'de', 'Äthiopien'),
(195, 'ET', 'it', 'Etiopia'),
(196, 'FI', 'en', 'Finland'),
(197, 'FI', 'de', 'Finnland'),
(198, 'FI', 'it', 'Finlandia'),
(199, 'FJ', 'en', 'Fiji'),
(200, 'FJ', 'de', 'Fidschi'),
(201, 'FJ', 'it', 'Figi'),
(202, 'FK', 'en', 'Falkland Islands (Malvinas)'),
(203, 'FK', 'de', 'Falklandinseln (Malwinen)'),
(204, 'FK', 'it', 'Isole Falkland (Malvinas)'),
(205, 'FM', 'en', 'Micronesia, Federated States of'),
(206, 'FM', 'de', 'Föderierte Staaten von Mikronesien'),
(207, 'FM', 'it', 'Stati federati di Micronesia'),
(208, 'FO', 'en', 'Faroe Islands'),
(209, 'FO', 'de', 'Färöer'),
(210, 'FO', 'it', 'Isole Faroe'),
(211, 'FR', 'en', 'France'),
(212, 'FR', 'de', 'Frankreich'),
(213, 'FR', 'it', 'Francia'),
(214, 'GA', 'en', 'Gabon'),
(215, 'GA', 'de', 'Gabun'),
(216, 'GA', 'it', 'Gabon'),
(217, 'GD', 'en', 'Grenada'),
(218, 'GD', 'de', 'Grenada'),
(219, 'GD', 'it', 'Grenada'),
(220, 'GE', 'en', 'Georgia'),
(221, 'GE', 'de', 'Georgien'),
(222, 'GE', 'it', 'Georgia'),
(223, 'GF', 'en', 'French Guiana'),
(224, 'GF', 'de', 'Französisch-Guayana'),
(225, 'GF', 'it', 'Guyana francese'),
(226, 'GH', 'en', 'Ghana'),
(227, 'GH', 'de', 'Ghana'),
(228, 'GH', 'it', 'Ghana'),
(229, 'GI', 'en', 'Gibraltar'),
(230, 'GI', 'de', 'Gibraltar'),
(231, 'GI', 'it', 'Gibilterra'),
(232, 'GL', 'en', 'Greenland'),
(233, 'GL', 'de', 'Grönland'),
(234, 'GL', 'it', 'Groenlandia'),
(235, 'GM', 'en', 'Gambia'),
(236, 'GM', 'de', 'Gambia'),
(237, 'GM', 'it', 'Gambia'),
(238, 'GN', 'en', 'Guinea'),
(239, 'GN', 'de', 'Guinea'),
(240, 'GN', 'it', 'Guinea'),
(241, 'GP', 'en', 'Guadeloupe'),
(242, 'GP', 'de', 'Guadeloupe'),
(243, 'GP', 'it', 'Guadalupa'),
(244, 'GQ', 'en', 'Equatorial Guinea'),
(245, 'GQ', 'de', 'Äquatorialguinea'),
(246, 'GQ', 'it', 'Guinea equatoriale'),
(247, 'GR', 'en', 'Greece'),
(248, 'GR', 'de', 'Griechenland'),
(249, 'GR', 'it', 'Grecia'),
(250, 'GS', 'en', 'South Georgia and The South Sandwich Islands'),
(251, 'GS', 'de', 'Südgeorgien und Südliche Sandwichinseln'),
(252, 'GS', 'it', 'Isole Georgia del Sud e Sandwich del Sud'),
(253, 'GT', 'en', 'Guatemala'),
(254, 'GT', 'de', 'Guatemala'),
(255, 'GT', 'it', 'Guatemala'),
(256, 'GU', 'en', 'Guam'),
(257, 'GU', 'de', 'Guam'),
(258, 'GU', 'it', 'Guam'),
(259, 'GW', 'en', 'Guinea-Bissau'),
(260, 'GW', 'de', 'Guinea-Bissau'),
(261, 'GW', 'it', 'Guinea Bissau'),
(262, 'GY', 'en', 'Guyana'),
(263, 'GY', 'de', 'Guyana'),
(264, 'GY', 'it', 'Guyana'),
(265, 'HK', 'en', 'Hong Kong'),
(266, 'HK', 'de', 'Hongkong'),
(267, 'HK', 'it', 'Hong Kong'),
(268, 'HM', 'en', 'Heard Island and McDonald Islands'),
(269, 'HM', 'de', 'Heard und McDonaldinseln'),
(270, 'HM', 'it', 'Isole Heard e McDonald'),
(271, 'HN', 'en', 'Honduras'),
(272, 'HN', 'de', 'Honduras'),
(273, 'HN', 'it', 'Honduras'),
(274, 'HR', 'en', 'Croatia'),
(275, 'HR', 'de', 'Kroatien'),
(276, 'HR', 'it', 'Croazia'),
(277, 'HT', 'en', 'Haiti'),
(278, 'HT', 'de', 'Haiti'),
(279, 'HT', 'it', 'Haiti'),
(280, 'HU', 'en', 'Hungary'),
(281, 'HU', 'de', 'Ungarn'),
(282, 'HU', 'it', 'Ungheria'),
(283, 'ID', 'en', 'Indonesia'),
(284, 'ID', 'de', 'Indonesien'),
(285, 'ID', 'it', 'Indonesia'),
(286, 'IE', 'en', 'Ireland'),
(287, 'IE', 'de', 'Irland'),
(288, 'IE', 'it', 'Irlanda'),
(289, 'IL', 'en', 'Israel'),
(290, 'IL', 'de', 'Israel'),
(291, 'IL', 'it', 'Israele'),
(292, 'IN', 'en', 'India'),
(293, 'IN', 'de', 'Indien'),
(294, 'IN', 'it', 'India'),
(295, 'IO', 'en', 'British Indian Ocean Territory'),
(296, 'IO', 'de', 'Britisches Territorium im Indischen Ozean'),
(297, 'IO', 'it', 'Territorio britannico dell''Oceano Indiano'),
(298, 'IQ', 'en', 'Iraq'),
(299, 'IQ', 'de', 'Irak'),
(300, 'IQ', 'it', 'Iraq'),
(301, 'IR', 'en', 'Iran, Islamic Republic of'),
(302, 'IR', 'de', 'Islamische Republik Iran'),
(303, 'IR', 'it', 'Repubblica islamica dell''Iran'),
(304, 'IS', 'en', 'Iceland'),
(305, 'IS', 'de', 'Island'),
(306, 'IS', 'it', 'Islanda'),
(307, 'IT', 'en', 'Italy'),
(308, 'IT', 'de', 'Italien'),
(309, 'IT', 'it', 'Italia'),
(310, 'JM', 'en', 'Jamaica'),
(311, 'JM', 'de', 'Jamaika'),
(312, 'JM', 'it', 'Giamaica'),
(313, 'JO', 'en', 'Jordan'),
(314, 'JO', 'de', 'Jordanien'),
(315, 'JO', 'it', 'Giordania'),
(316, 'JP', 'en', 'Japan'),
(317, 'JP', 'de', 'Japan'),
(318, 'JP', 'it', 'Giappone'),
(319, 'KE', 'en', 'Kenya'),
(320, 'KE', 'de', 'Kenia'),
(321, 'KE', 'it', 'Kenya'),
(322, 'KG', 'en', 'Kyrgyzstan'),
(323, 'KG', 'de', 'Kirgisistan'),
(324, 'KG', 'it', 'Kirghizistan'),
(325, 'KH', 'en', 'Cambodia'),
(326, 'KH', 'de', 'Kambodscha'),
(327, 'KH', 'it', 'Cambogia'),
(328, 'KI', 'en', 'Kiribati'),
(329, 'KI', 'de', 'Kiribati'),
(330, 'KI', 'it', 'Kiribati'),
(331, 'KM', 'en', 'Comoros'),
(332, 'KM', 'de', 'Komoren'),
(333, 'KM', 'it', 'Comore'),
(334, 'KN', 'en', 'Saint Kitts and Nevis'),
(335, 'KN', 'de', 'St. Kitts und Nevis'),
(336, 'KN', 'it', 'Saint Kitts e Nevis'),
(337, 'KP', 'en', 'Korea, Republic of'),
(338, 'KP', 'de', 'Republik Korea'),
(339, 'KP', 'it', 'Repubblica di Corea'),
(340, 'KR', 'en', 'Korea, Democratic People''s Republic of'),
(341, 'KR', 'de', 'Demokratische Volksrepublik Korea'),
(342, 'KR', 'it', 'Repubblica popolare democratica di Corea'),
(343, 'KW', 'en', 'Kuwait'),
(344, 'KW', 'de', 'Kuwait'),
(345, 'KW', 'it', 'Kuwait'),
(346, 'KY', 'en', 'Cayman Islands'),
(347, 'KY', 'de', 'Kaiman-Inseln'),
(348, 'KY', 'it', 'Isole Cayman'),
(349, 'KZ', 'en', 'Kazakhstan'),
(350, 'KZ', 'de', 'Kasachstan'),
(351, 'KZ', 'it', 'Kazakstan'),
(352, 'LA', 'en', 'Lao People''s Democratic Republic'),
(353, 'LA', 'de', 'Demokratische Volksrepublik Laos'),
(354, 'LA', 'it', 'Repubblica democratica popolare del Laos'),
(355, 'LB', 'en', 'Lebanon'),
(356, 'LB', 'de', 'Libanon'),
(357, 'LB', 'it', 'Libano'),
(358, 'LI', 'en', 'Liechtenstein'),
(359, 'LI', 'de', 'Liechtenstein'),
(360, 'LI', 'it', 'Liechtenstein'),
(361, 'LK', 'en', 'Sri Lanka'),
(362, 'LK', 'de', 'Sri Lanka'),
(363, 'LK', 'it', 'Sri Lanka'),
(364, 'LR', 'en', 'Liberia'),
(365, 'LR', 'de', 'Liberia'),
(366, 'LR', 'it', 'Liberia'),
(367, 'LS', 'en', 'Lesotho'),
(368, 'LS', 'de', 'Lesotho'),
(369, 'LS', 'it', 'Lesotho'),
(370, 'LT', 'en', 'Lithuania'),
(371, 'LT', 'de', 'Litauen'),
(372, 'LT', 'it', 'Lituania'),
(373, 'LU', 'en', 'Luxembourg'),
(374, 'LU', 'de', 'Luxemburg'),
(375, 'LU', 'it', 'Lussemburgo'),
(376, 'LV', 'en', 'Latvia'),
(377, 'LV', 'de', 'Lettland'),
(378, 'LV', 'it', 'Lettonia'),
(379, 'LY', 'en', 'Libyan Arab Jamahiriya'),
(380, 'LY', 'de', 'Sozialistische Libysch-Arabische Volks-Dschamahirija'),
(381, 'LY', 'it', 'Giamahiria araba libica'),
(382, 'MA', 'en', 'Morocco'),
(383, 'MA', 'de', 'Marokko'),
(384, 'MA', 'it', 'Marocco'),
(385, 'MC', 'en', 'Monaco'),
(386, 'MC', 'de', 'Monaco'),
(387, 'MC', 'it', 'Monaco'),
(388, 'MD', 'en', 'Moldova, Republic of'),
(389, 'MD', 'de', 'Republik Moldau'),
(390, 'MD', 'it', 'Repubblica moldova'),
(391, 'ME', 'en', 'Montenegro'),
(392, 'ME', 'de', 'Montenegro'),
(393, 'ME', 'it', 'Montenegro'),
(394, 'MG', 'en', 'Madagascar'),
(395, 'MG', 'de', 'Madagaskar'),
(396, 'MG', 'it', 'Madagascar'),
(397, 'MH', 'en', 'Marshall Islands'),
(398, 'MH', 'de', 'Marshallinseln'),
(399, 'MH', 'it', 'Isole Marshall'),
(400, 'MK', 'en', 'Macedonia, the former Yugoslav Republic of'),
(401, 'MK', 'de', 'Ehemalige jugoslawische Republik Mazedonien'),
(402, 'MK', 'it', 'Ex Repubblica iugoslava di Macedonia'),
(403, 'ML', 'en', 'Mali'),
(404, 'ML', 'de', 'Mali'),
(405, 'ML', 'it', 'Mali'),
(406, 'MM', 'en', 'Myanmar'),
(407, 'MM', 'de', 'Myanmar'),
(408, 'MM', 'it', 'Myanmar'),
(409, 'MN', 'en', 'Mongolia'),
(410, 'MN', 'de', 'Mongolei'),
(411, 'MN', 'it', 'Mongolia'),
(412, 'MO', 'en', 'Macau'),
(413, 'MO', 'de', 'Macau'),
(414, 'MO', 'it', 'Macao'),
(415, 'MP', 'en', 'Northern Mariana Islands'),
(416, 'MP', 'de', 'Nördliche Marianen'),
(417, 'MP', 'it', 'Isole Marianne settentrionali'),
(418, 'MQ', 'en', 'Martinique'),
(419, 'MQ', 'de', 'Martinique'),
(420, 'MQ', 'it', 'Martinica'),
(421, 'MR', 'en', 'Mauritania'),
(422, 'MR', 'de', 'Mauretanien'),
(423, 'MR', 'it', 'Mauritania'),
(424, 'MS', 'en', 'Montserrat'),
(425, 'MS', 'de', 'Montserrat'),
(426, 'MS', 'it', 'Montserrat'),
(427, 'MT', 'en', 'Malta'),
(428, 'MT', 'de', 'Malta'),
(429, 'MT', 'it', 'Malta'),
(430, 'MU', 'en', 'Mauritius'),
(431, 'MU', 'de', 'Mauritius'),
(432, 'MU', 'it', 'Maurizio'),
(433, 'MV', 'en', 'Maldives'),
(434, 'MV', 'de', 'Malediven'),
(435, 'MV', 'it', 'Maldive'),
(436, 'MW', 'en', 'Malawi'),
(437, 'MW', 'de', 'Malawi'),
(438, 'MW', 'it', 'Malawi'),
(439, 'MX', 'en', 'Mexico'),
(440, 'MX', 'de', 'Mexiko'),
(441, 'MX', 'it', 'Messico'),
(442, 'MY', 'en', 'Malaysia'),
(443, 'MY', 'de', 'Malaysia'),
(444, 'MY', 'it', 'Malaysia'),
(445, 'MZ', 'en', 'Mozambique'),
(446, 'MZ', 'de', 'Mosambik'),
(447, 'MZ', 'it', 'Mozambico'),
(448, 'NA', 'en', 'Namibia'),
(449, 'NA', 'de', 'Namibia'),
(450, 'NA', 'it', 'Namibia'),
(451, 'NC', 'en', 'New Caledonia'),
(452, 'NC', 'de', 'Neukaledonien'),
(453, 'NC', 'it', 'Nuova Caledonia'),
(454, 'NE', 'en', 'Niger'),
(455, 'NE', 'de', 'Niger'),
(456, 'NE', 'it', 'Niger'),
(457, 'NF', 'en', 'Norfolk Island'),
(458, 'NF', 'de', 'Norfolkinsel'),
(459, 'NF', 'it', 'Isole Norfolk'),
(460, 'NG', 'en', 'Nigeria'),
(461, 'NG', 'de', 'Nigeria'),
(462, 'NG', 'it', 'Nigeria'),
(463, 'NI', 'en', 'Nicaragua'),
(464, 'NI', 'de', 'Nicaragua'),
(465, 'NI', 'it', 'Nicaragua'),
(466, 'NL', 'en', 'Netherlands'),
(467, 'NL', 'de', 'Niederlande'),
(468, 'NL', 'it', 'Paesi Bassi'),
(469, 'NO', 'en', 'Norway'),
(470, 'NO', 'de', 'Norwegen'),
(471, 'NO', 'it', 'Norvegia'),
(472, 'NP', 'en', 'Nepal'),
(473, 'NP', 'de', 'Nepal'),
(474, 'NP', 'it', 'Nepal'),
(475, 'NR', 'en', 'Nauru'),
(476, 'NR', 'de', 'Nauru'),
(477, 'NR', 'it', 'Nauru'),
(478, 'NU', 'en', 'Niue'),
(479, 'NU', 'de', 'Niue'),
(480, 'NU', 'it', 'Niue'),
(481, 'NZ', 'en', 'New Zealand'),
(482, 'NZ', 'de', 'Neuseeland'),
(483, 'NZ', 'it', 'Nuova Zelanda'),
(484, 'OM', 'en', 'Oman'),
(485, 'OM', 'de', 'Oman'),
(486, 'OM', 'it', 'Oman'),
(487, 'PA', 'en', 'Panama'),
(488, 'PA', 'de', 'Panama'),
(489, 'PA', 'it', 'Panama'),
(490, 'PE', 'en', 'Peru'),
(491, 'PE', 'de', 'Peru'),
(492, 'PE', 'it', 'Perù'),
(493, 'PF', 'en', 'French Polynesia'),
(494, 'PF', 'de', 'Französisch-Polynesien'),
(495, 'PF', 'it', 'Polinesia francese'),
(496, 'PG', 'en', 'Papua New Guinea'),
(497, 'PG', 'de', 'Papua-Neuguinea'),
(498, 'PG', 'it', 'Papua Nuova Guinea'),
(499, 'PH', 'en', 'Philippines'),
(500, 'PH', 'de', 'Philippinen'),
(501, 'PH', 'it', 'Filippine'),
(502, 'PK', 'en', 'Pakistan'),
(503, 'PK', 'de', 'Pakistan'),
(504, 'PK', 'it', 'Pakistan'),
(505, 'PL', 'en', 'Poland'),
(506, 'PL', 'de', 'Polen'),
(507, 'PL', 'it', 'Polonia'),
(508, 'PN', 'en', 'Pitcairn'),
(509, 'PN', 'de', 'Pitcairn'),
(510, 'PN', 'it', 'Pitcairn'),
(511, 'PR', 'en', 'Puerto Rico'),
(512, 'PR', 'de', 'Puerto Rico'),
(513, 'PR', 'it', 'Portorico'),
(514, 'PS', 'en', 'Palestinian Territory, Occupied'),
(515, 'PS', 'de', 'Besetzte Palästinensische Gebiete'),
(516, 'PS', 'it', 'Territori palestinesi occupati'),
(517, 'PT', 'en', 'Portugal'),
(518, 'PT', 'de', 'Portugal'),
(519, 'PT', 'it', 'Portogallo'),
(520, 'PW', 'en', 'Palau'),
(521, 'PW', 'de', 'Palau'),
(522, 'PW', 'it', 'Palau'),
(523, 'PY', 'en', 'Paraguay'),
(524, 'PY', 'de', 'Paraguay'),
(525, 'PY', 'it', 'Paraguay'),
(526, 'QA', 'en', 'Qatar'),
(527, 'QA', 'de', 'Katar'),
(528, 'QA', 'it', 'Qatar'),
(529, 'RE', 'en', 'Réunion'),
(530, 'RE', 'de', 'Réunion'),
(531, 'RE', 'it', 'Riunione'),
(532, 'RO', 'en', 'Romania'),
(533, 'RO', 'de', 'Rumänien'),
(534, 'RO', 'it', 'Romania'),
(535, 'RS', 'en', 'Serbia'),
(536, 'RS', 'de', 'Serbien'),
(537, 'RS', 'it', 'Serbia'),
(538, 'RU', 'en', 'Russian Federation'),
(539, 'RU', 'de', 'Russische Föderation'),
(540, 'RU', 'it', 'Federazione russa'),
(541, 'RW', 'en', 'Rwanda'),
(542, 'RW', 'de', 'Ruanda'),
(543, 'RW', 'it', 'Ruanda'),
(544, 'SA', 'en', 'Saudi Arabia'),
(545, 'SA', 'de', 'Saudi-Arabien'),
(546, 'SA', 'it', 'Arabia Saudita'),
(547, 'SB', 'en', 'Solomon Islands'),
(548, 'SB', 'de', 'Salomonen'),
(549, 'SB', 'it', 'Isole Salomone'),
(550, 'SC', 'en', 'Seychelles'),
(551, 'SC', 'de', 'Seychellen'),
(552, 'SC', 'it', 'Seicelle'),
(553, 'SD', 'en', 'Sudan'),
(554, 'SD', 'de', 'Sudan'),
(555, 'SD', 'it', 'Sudan'),
(556, 'SE', 'en', 'Sweden'),
(557, 'SE', 'de', 'Schweden'),
(558, 'SE', 'it', 'Svezia'),
(559, 'SG', 'en', 'Singapore'),
(560, 'SG', 'de', 'Singapur'),
(561, 'SG', 'it', 'Singapore'),
(562, 'SI', 'en', 'Slovenia'),
(563, 'SI', 'de', 'Slowenien'),
(564, 'SI', 'it', 'Slovenia'),
(565, 'SJ', 'en', 'Svalbard and Jan Mayen Islands'),
(566, 'SJ', 'de', 'Svalbard und Jan Mayen'),
(567, 'SJ', 'it', 'Isole Svalbard e Jan Mayen'),
(568, 'SK', 'en', 'Slovakia'),
(569, 'SK', 'de', 'Slowakei'),
(570, 'SK', 'it', 'Slovacchia'),
(571, 'SL', 'en', 'Sierra Leone'),
(572, 'SL', 'de', 'Sierra Leone'),
(573, 'SL', 'it', 'Sierra Leone'),
(574, 'SM', 'en', 'San Marino'),
(575, 'SM', 'de', 'San Marino'),
(576, 'SM', 'it', 'San Marino'),
(577, 'SN', 'en', 'Senegal'),
(578, 'SN', 'de', 'Senegal'),
(579, 'SN', 'it', 'Senegal'),
(580, 'SO', 'en', 'Somalia'),
(581, 'SO', 'de', 'Somalia'),
(582, 'SO', 'it', 'Somalia'),
(583, 'SR', 'en', 'Suriname'),
(584, 'SR', 'de', 'Suriname'),
(585, 'SR', 'it', 'Suriname'),
(586, 'ST', 'en', 'São Tomé and Príncipe'),
(587, 'ST', 'de', 'São Tomé und Príncipe'),
(588, 'ST', 'it', 'São Tomé e Príncipe'),
(589, 'SV', 'en', 'El Salvador'),
(590, 'SV', 'de', 'El Salvador'),
(591, 'SV', 'it', 'El Salvador'),
(592, 'SY', 'en', 'Syrian Arab Republic'),
(593, 'SY', 'de', 'Arabische Republik Syrien'),
(594, 'SY', 'it', 'Repubblica araba siriana'),
(595, 'SZ', 'en', 'Swaziland'),
(596, 'SZ', 'de', 'Swasiland'),
(597, 'SZ', 'it', 'Swaziland'),
(598, 'TC', 'en', 'Turks and Caicos Islands'),
(599, 'TC', 'de', 'Turks- und Caicosinseln'),
(600, 'TC', 'it', 'Isole Turks e Caicos'),
(601, 'TD', 'en', 'Chad'),
(602, 'TD', 'de', 'Tschad'),
(603, 'TD', 'it', 'Ciad'),
(604, 'TF', 'en', 'French Southern Territories'),
(605, 'TF', 'de', 'Französische Gebiete im südlichen Indischen Ozean'),
(606, 'TF', 'it', 'Territori australi francesi'),
(607, 'TG', 'en', 'Togo'),
(608, 'TG', 'de', 'Togo'),
(609, 'TG', 'it', 'Togo'),
(610, 'TH', 'en', 'Thailand'),
(611, 'TH', 'de', 'Thailand'),
(612, 'TH', 'it', 'Thailandia'),
(613, 'TJ', 'en', 'Tajikistan'),
(614, 'TJ', 'de', 'Tadschikistan'),
(615, 'TJ', 'it', 'Tagikistan'),
(616, 'TK', 'en', 'Tokelau'),
(617, 'TK', 'de', 'Tokelau'),
(618, 'TK', 'it', 'Tokelau'),
(619, 'TL', 'en', 'Timor-Leste'),
(620, 'TL', 'de', 'Timor-Leste'),
(621, 'TL', 'it', 'Timor orientale'),
(622, 'TM', 'en', 'Turkmenistan'),
(623, 'TM', 'de', 'Turkmenistan'),
(624, 'TM', 'it', 'Turkmenistan'),
(625, 'TN', 'en', 'Tunisia'),
(626, 'TN', 'de', 'Tunesien'),
(627, 'TN', 'it', 'Tunisia'),
(628, 'TO', 'en', 'Tonga'),
(629, 'TO', 'de', 'Tonga'),
(630, 'TO', 'it', 'Tonga'),
(631, 'TR', 'en', 'Turkey'),
(632, 'TR', 'de', 'Türkei'),
(633, 'TR', 'it', 'Turchia'),
(634, 'TT', 'en', 'Trinidad and Tobago'),
(635, 'TT', 'de', 'Trinidad und Tobago'),
(636, 'TT', 'it', 'Trinidad e Tobago'),
(637, 'TV', 'en', 'Tuvalu'),
(638, 'TV', 'de', 'Tuvalu'),
(639, 'TV', 'it', 'Tuvalu'),
(640, 'TW', 'en', 'Taiwan, Province of China'),
(641, 'TW', 'de', 'Taiwan'),
(642, 'TW', 'it', 'Taiwan'),
(643, 'TZ', 'en', 'Tanzania, United Republic Of'),
(644, 'TZ', 'de', 'Vereinigte Republik Tansania'),
(645, 'TZ', 'it', 'Repubblica unita di Tanzania'),
(646, 'UA', 'en', 'Ukraine'),
(647, 'UA', 'de', 'Ukraine'),
(648, 'UA', 'it', 'Ucraina'),
(649, 'UG', 'en', 'Uganda'),
(650, 'UG', 'de', 'Uganda'),
(651, 'UG', 'it', 'Uganda'),
(652, 'GB', 'en', 'United Kingdom'),
(653, 'GB', 'de', 'Vereinigtes Königreich'),
(654, 'GB', 'it', 'Regno Unito'),
(655, 'UM', 'en', 'United States Minor Outlying Islands'),
(656, 'UM', 'de', 'Kleinere Amerikanische Überseeinseln'),
(657, 'UM', 'it', 'Isole minori lontane dagli Stati Uniti'),
(658, 'US', 'en', 'United States'),
(659, 'US', 'de', 'Vereinigte Staaten'),
(660, 'US', 'it', 'Stati Uniti'),
(661, 'UY', 'en', 'Uruguay'),
(662, 'UY', 'de', 'Uruguay'),
(663, 'UY', 'it', 'Uruguay'),
(664, 'UZ', 'en', 'Uzbekistan'),
(665, 'UZ', 'de', 'Usbekistan'),
(666, 'UZ', 'it', 'Uzbekistan'),
(667, 'VA', 'en', 'Vatican City State (Holy See)'),
(668, 'VA', 'de', 'Heiliger Stuhl (Staat Vatikanstadt)'),
(669, 'VA', 'it', 'Stato della Città del Vaticano (Santa Sede)'),
(670, 'VC', 'en', 'Saint Vincent and the Grenadines'),
(671, 'VC', 'de', 'St. Vincent und die Grenadinen'),
(672, 'VC', 'it', 'Saint Vincent e Grenadines'),
(673, 'VE', 'en', 'Venezuela'),
(674, 'VE', 'de', 'Venezuela'),
(675, 'VE', 'it', 'Venezuela'),
(676, 'VG', 'en', 'Virgin Islands, British'),
(677, 'VG', 'de', 'Britische Jungferninseln'),
(678, 'VG', 'it', 'Isole Vergini (britanniche)'),
(679, 'VI', 'en', 'Virgin Islands, U.S.'),
(680, 'VI', 'de', 'Amerikanische Jungferninseln'),
(681, 'VI', 'it', 'Isole Vergini (americane)'),
(682, 'VN', 'en', 'Viet Nam'),
(683, 'VN', 'de', 'Vietnam'),
(684, 'VN', 'it', 'Vietnam'),
(685, 'VU', 'en', 'Vanuatu'),
(686, 'VU', 'de', 'Vanuatu'),
(687, 'VU', 'it', 'Vanuatu'),
(688, 'WF', 'en', 'Wallis and Futuna Islands'),
(689, 'WF', 'de', 'Wallis und Futuna'),
(690, 'WF', 'it', 'Isole Wallis e Futuna'),
(691, 'WL', 'en', 'Saint Lucia'),
(692, 'WL', 'de', 'St. Lucia'),
(693, 'WL', 'it', 'Saint Lucia'),
(694, 'WS', 'en', 'Samoa'),
(695, 'WS', 'de', 'Samoa'),
(696, 'WS', 'it', 'Samoa'),
(697, 'YE', 'en', 'Yemen'),
(698, 'YE', 'de', 'Jemen'),
(699, 'YE', 'it', 'Yemen'),
(700, 'YT', 'en', 'Mayotte'),
(701, 'YT', 'de', 'Mayotte'),
(702, 'YT', 'it', 'Mayotte'),
(703, 'ZA', 'en', 'South Africa'),
(704, 'ZA', 'de', 'Südafrika'),
(705, 'ZA', 'it', 'Sud Africa'),
(706, 'ZM', 'en', 'Zambia'),
(707, 'ZM', 'de', 'Sambia'),
(708, 'ZM', 'it', 'Zambia'),
(709, 'ZW', 'en', 'Zimbabwe'),
(710, 'ZW', 'de', 'Simbabwe'),
(711, 'ZW', 'it', 'Zimbabwe'),
(712, 'AD', 'fr', 'Andorre'),
(713, 'AE', 'fr', 'Émirats Arabes Unis'),
(714, 'AF', 'fr', 'Afghanistan'),
(715, 'AG', 'fr', 'Antigua and Barbuda'),
(716, 'AI', 'fr', 'Anguilla'),
(717, 'AL', 'fr', 'Albanie'),
(718, 'AM', 'fr', 'Arménie'),
(719, 'AN', 'fr', 'Antilles Néerlandaises'),
(720, 'AO', 'fr', 'Angola'),
(721, 'AQ', 'fr', 'Antarctique'),
(722, 'AR', 'fr', 'Argentine'),
(723, 'AS', 'fr', 'Samora Américaines'),
(724, 'AT', 'fr', 'Autriche'),
(725, 'AU', 'fr', 'Australie'),
(726, 'AW', 'fr', 'Aruba'),
(727, 'AZ', 'fr', 'Azerbaïdjan'),
(728, 'BA', 'fr', 'Bosnie-Herzégovine'),
(729, 'BB', 'fr', 'Barbade'),
(730, 'BD', 'fr', 'Bangladesh'),
(731, 'BE', 'fr', 'Belgique'),
(732, 'BF', 'fr', 'Burkina Faso'),
(733, 'BG', 'fr', 'Bulgarie'),
(734, 'BH', 'fr', 'Bahreïn'),
(735, 'BI', 'fr', 'Burundi'),
(736, 'BJ', 'fr', 'Bénin'),
(737, 'BM', 'fr', 'Bermudes'),
(738, 'BN', 'fr', 'Brunéi Darussalam'),
(739, 'BO', 'fr', 'Bolivie'),
(740, 'BR', 'fr', 'Brésil'),
(741, 'BS', 'fr', 'Bahamas'),
(742, 'BT', 'fr', 'Bhoutan'),
(743, 'BW', 'fr', 'Botswana'),
(744, 'BY', 'fr', 'Bélarus'),
(745, 'BZ', 'fr', 'Belize'),
(746, 'CA', 'fr', 'Canada'),
(747, 'CC', 'fr', 'Îles Cocos'),
(748, 'CD', 'fr', 'Congo - Rep. Dém.'),
(749, 'CF', 'fr', 'République Centrafricaine'),
(750, 'CG', 'fr', 'Congo'),
(751, 'CH', 'fr', 'Suisse'),
(752, 'CI', 'fr', 'Côte d''Ivoire'),
(753, 'CK', 'fr', 'Îles Cook'),
(754, 'CL', 'fr', 'Chili'),
(755, 'CM', 'fr', 'Cameroun'),
(756, 'CN', 'fr', 'Chine'),
(757, 'CO', 'fr', 'Colombie'),
(758, 'CR', 'fr', 'Costa Rica'),
(759, 'CU', 'fr', 'Cuba'),
(760, 'CV', 'fr', 'Cap-Vert'),
(761, 'CX', 'fr', 'Île Christmas'),
(762, 'CY', 'fr', 'Chypre'),
(763, 'CZ', 'fr', 'République Tchèque'),
(764, 'DE', 'fr', 'Allemagne'),
(765, 'DJ', 'fr', 'Djibouti'),
(766, 'DK', 'fr', 'Danemark'),
(767, 'DM', 'fr', 'Dominique'),
(768, 'DO', 'fr', 'République Dominicaine'),
(769, 'DZ', 'fr', 'Algérie'),
(770, 'EC', 'fr', 'Équateur'),
(771, 'EE', 'fr', 'Estonie'),
(772, 'EG', 'fr', 'Égypte'),
(773, 'EH', 'fr', 'Sahara Occidental'),
(774, 'ER', 'fr', 'Érythrée'),
(775, 'ES', 'fr', 'Espagne'),
(776, 'ET', 'fr', 'Éthiopie'),
(777, 'FI', 'fr', 'Finlande'),
(778, 'FJ', 'fr', 'Fidji'),
(779, 'FK', 'fr', 'Îles Falkland'),
(780, 'FM', 'fr', 'Micronesia, Federated States of'),
(781, 'FO', 'fr', 'Îles Féroé'),
(782, 'FR', 'fr', 'France'),
(783, 'GA', 'fr', 'Gabon'),
(784, 'GD', 'fr', 'Grenade'),
(785, 'GE', 'fr', 'Géorgie'),
(786, 'GF', 'fr', 'Guyane Française'),
(787, 'GH', 'fr', 'Ghana'),
(788, 'GI', 'fr', 'Gibraltar'),
(789, 'GL', 'fr', 'Groenland'),
(790, 'GM', 'fr', 'Gambie'),
(791, 'GN', 'fr', 'Guinéee'),
(792, 'GP', 'fr', 'Guadeloupe'),
(793, 'GQ', 'fr', 'Guinée Équatoriale'),
(794, 'GR', 'fr', 'Grèce'),
(795, 'GS', 'fr', 'South Georgia and The South Sandwich Islands'),
(796, 'GT', 'fr', 'Guatemala'),
(797, 'GU', 'fr', 'Guam'),
(798, 'GW', 'fr', 'Guinée-Bissau'),
(799, 'GY', 'fr', 'Guyana'),
(800, 'HK', 'fr', 'Hong-Kong'),
(801, 'HM', 'fr', 'Heard Island and McDonald Islands'),
(802, 'HN', 'fr', 'Honduras'),
(803, 'HR', 'fr', 'Croatie'),
(804, 'HT', 'fr', 'Haïti'),
(805, 'HU', 'fr', 'Hongrie'),
(806, 'ID', 'fr', 'Indonésie'),
(807, 'IE', 'fr', 'Irlande'),
(808, 'IL', 'fr', 'Israël'),
(809, 'IN', 'fr', 'Inde'),
(810, 'IO', 'fr', 'British Indian Ocean Territory'),
(811, 'IQ', 'fr', 'Iraq'),
(812, 'IR', 'fr', 'Iran'),
(813, 'IS', 'fr', 'Islande'),
(814, 'IT', 'fr', 'Italie'),
(815, 'JM', 'fr', 'Jamaïque'),
(816, 'JO', 'fr', 'Jordanie'),
(817, 'JP', 'fr', 'Japon'),
(818, 'KE', 'fr', 'Kenya'),
(819, 'KG', 'fr', 'Kirghizistan'),
(820, 'KH', 'fr', 'Comores'),
(821, 'KI', 'fr', 'Kiribati'),
(822, 'KM', 'fr', 'Comores'),
(823, 'KN', 'fr', 'Saint-Kitts-et-Nevis'),
(824, 'KP', 'fr', 'Corée'),
(825, 'KR', 'fr', 'Corée - Rép. Dém.'),
(826, 'KW', 'fr', 'Koweït'),
(827, 'KY', 'fr', 'Caïmanes, Îles'),
(828, 'KZ', 'fr', 'Kazakstan'),
(829, 'LA', 'fr', 'Lao People''s Democratic Republic'),
(830, 'LB', 'fr', 'Liban'),
(831, 'LI', 'fr', 'Liechtenstein'),
(832, 'LK', 'fr', 'Sri Lanka'),
(833, 'LR', 'fr', 'Libéria'),
(834, 'LS', 'fr', 'Lesotho'),
(835, 'LT', 'fr', 'Lituanie'),
(836, 'LU', 'fr', 'Luxembourg'),
(837, 'LV', 'fr', 'Lettonie'),
(838, 'LY', 'fr', 'Libye'),
(839, 'MA', 'fr', 'Maroc'),
(840, 'MC', 'fr', 'Monaco'),
(841, 'MD', 'fr', 'Moldova'),
(842, 'ME', 'fr', 'Monténégro'),
(843, 'MG', 'fr', 'Madagascar'),
(844, 'MH', 'fr', 'Îles Marshall'),
(845, 'MK', 'fr', 'Macédoine'),
(846, 'ML', 'fr', 'Mali'),
(847, 'MM', 'fr', 'Myanmar'),
(848, 'MN', 'fr', 'Mongolie'),
(849, 'MO', 'fr', 'Macao'),
(850, 'MP', 'fr', 'Northern Mariana Islands'),
(851, 'MQ', 'fr', 'Martinique'),
(852, 'MR', 'fr', 'Mauritanie'),
(853, 'MS', 'fr', 'Montserrat'),
(854, 'MT', 'fr', 'Malte'),
(855, 'MU', 'fr', 'Île Maurice'),
(856, 'MV', 'fr', 'Maldives'),
(857, 'MW', 'fr', 'Malawi'),
(858, 'MX', 'fr', 'Mexique'),
(859, 'MY', 'fr', 'Malaisie'),
(860, 'MZ', 'fr', 'Mozambique'),
(861, 'NA', 'fr', 'Namibie'),
(862, 'NC', 'fr', 'Nouvelle-Calédonie'),
(863, 'NE', 'fr', 'Niger'),
(864, 'NF', 'fr', 'Norfolk Island'),
(865, 'NG', 'fr', 'Nigéria'),
(866, 'NI', 'fr', 'Nicaragua'),
(867, 'NL', 'fr', 'Pays-Bas'),
(868, 'NO', 'fr', 'Norvège'),
(869, 'NP', 'fr', 'Népal'),
(870, 'NR', 'fr', 'Nauru'),
(871, 'NU', 'fr', 'Nioué'),
(872, 'NZ', 'fr', 'Nouvelle-Zélande'),
(873, 'OM', 'fr', 'Oman'),
(874, 'PA', 'fr', 'Panama'),
(875, 'PE', 'fr', 'Pérou'),
(876, 'PF', 'fr', 'Polynésie Française'),
(877, 'PG', 'fr', 'Papouasie-Nouvelle-Guinée'),
(878, 'PH', 'fr', 'Philippines'),
(879, 'PK', 'fr', 'Pakistan'),
(880, 'PL', 'fr', 'Pologne'),
(881, 'PN', 'fr', 'Pitcairn'),
(882, 'PR', 'fr', 'Porto Rico'),
(883, 'PS', 'fr', 'Palestine'),
(884, 'PT', 'fr', 'Portugal'),
(885, 'PW', 'fr', 'Palaos'),
(886, 'PY', 'fr', 'Paraguay'),
(887, 'QA', 'fr', 'Qatar'),
(888, 'RE', 'fr', 'Ile de la Réunion'),
(889, 'RO', 'fr', 'Roumanie'),
(890, 'RS', 'fr', 'Serbie'),
(891, 'RU', 'fr', 'Russie'),
(892, 'RW', 'fr', 'Rwanda'),
(893, 'SA', 'fr', 'Arabie Saoudite'),
(894, 'SB', 'fr', 'Îles Salomon'),
(895, 'SC', 'fr', 'Seychelles'),
(896, 'SD', 'fr', 'Soudan'),
(897, 'SE', 'fr', 'Suède'),
(898, 'SG', 'fr', 'Singapour'),
(899, 'SI', 'fr', 'Slovénie'),
(900, 'SJ', 'fr', 'Svalbard and Jan Mayen Islands'),
(901, 'SK', 'fr', 'Slovaquie'),
(902, 'SL', 'fr', 'Sierra Leone'),
(903, 'SM', 'fr', 'Saint-Marin'),
(904, 'SN', 'fr', 'Sénégal'),
(905, 'SO', 'fr', 'Somalie'),
(906, 'SR', 'fr', 'Suriname'),
(907, 'ST', 'fr', 'Sao Tomé-et-Principe'),
(908, 'SV', 'fr', 'El Salvador'),
(909, 'SY', 'fr', 'Syrienne'),
(910, 'SZ', 'fr', 'Swaziland'),
(911, 'TC', 'fr', 'Îles Turks et Caïques'),
(912, 'TD', 'fr', 'Tchad'),
(913, 'TF', 'fr', 'French Southern Territories'),
(914, 'TG', 'fr', 'Togo'),
(915, 'TH', 'fr', 'Thaïlande'),
(916, 'TJ', 'fr', 'Tadjikistan'),
(917, 'TK', 'fr', 'Tokelau'),
(918, 'TL', 'fr', 'Timor-Leste'),
(919, 'TM', 'fr', 'Turkménistan'),
(920, 'TN', 'fr', 'Tunisie'),
(921, 'TO', 'fr', 'Tonga'),
(922, 'TR', 'fr', 'Turquie'),
(923, 'TT', 'fr', 'Trinité-et-Tobago'),
(924, 'TV', 'fr', 'Tuvalu'),
(925, 'TW', 'fr', 'Taïwan'),
(926, 'TZ', 'fr', 'Tanzanie'),
(927, 'UA', 'fr', 'Ukraine'),
(928, 'UG', 'fr', 'Ouganda'),
(929, 'GB', 'fr', 'Royaume-Uni'),
(930, 'UM', 'fr', 'États-Unis Minor Outlying Islands'),
(931, 'US', 'fr', 'États-Unis'),
(932, 'UY', 'fr', 'Uruguay'),
(933, 'UZ', 'fr', 'Ouzbékistan'),
(934, 'VA', 'fr', 'Vatican City State (Holy See)'),
(935, 'VC', 'fr', 'Saint-Vincent-et-les Grenadines'),
(936, 'VE', 'fr', 'Venezuela'),
(937, 'VG', 'fr', 'Îles Vierges Britanniques'),
(938, 'VI', 'fr', 'Îles Vierges des États-Unis'),
(939, 'VN', 'fr', 'Viêt Nam'),
(940, 'VU', 'fr', 'Vanuatu'),
(941, 'WF', 'fr', 'Wallis et Futuna'),
(942, 'WL', 'fr', 'Sainte-Lucie'),
(943, 'WS', 'fr', 'Samoa'),
(944, 'YE', 'fr', 'Yémen'),
(945, 'YT', 'fr', 'Mayotte'),
(946, 'ZA', 'fr', 'Afrique du Sud'),
(947, 'ZM', 'fr', 'Zambie'),
(948, 'ZW', 'fr', 'Zimbabwe');

-- --------------------------------------------------------

--
-- Table structure for table `module`
--

DROP TABLE IF EXISTS `module`;
CREATE TABLE IF NOT EXISTS `module` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `application_id` int(10) unsigned NOT NULL,
  `code` varchar(20) NOT NULL COMMENT 'module directory',
  `name` varchar(50) NOT NULL,
  `description` varchar(255) NOT NULL,
  `settings` text NOT NULL COMMENT 'json encoded settings',
  `data` text COMMENT 'json data',
  PRIMARY KEY (`id`),
  KEY `application_id` (`application_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='cms modules' AUTO_INCREMENT=109 ;

--
-- Dumping data for table `module`
--

INSERT INTO `module` (`id`, `application_id`, `code`, `name`, `description`, `settings`, `data`) VALUES
(30, 1, 'auth', 'Auth', 'Users and permissions management', '', '{\r\n    "widgets":{\r\n        "hDashboard.Widget.Auth":{\r\n            "jsFiles":["/modules/auth/js/Widget.js"],\r\n            "cssFiles":[]\r\n        }\r\n    }\r\n}'),
(20, 1, 'admin', 'Admin Module', 'Admin Management Module', '', '{ "widgets":{ "hDashboard.Widget.Admin":{ "jsFiles":["/modules/admin/js/Widget.js","/plugins/highchart/highcharts.js"], "cssFiles":[] } } }'),
(10, 1, 'cms', 'CMS', 'Content Management Module', '', '\n{\n  "widgets": {\n    "hDashboard.Widget.Cms": {\n      "jsFiles": [\n        "/modules/cms/js/Widget.js"\n      ],\n      "cssFiles": []\n    },\n    "hDashboard.Widget.Cms.GoogleDashboardCountries": {\n      "jsFiles": [\n        "/modules/cms/js/Widget/GoogleDashboardCountries.js"\n      ],\n      "cssFiles": []\n    },\n    "hDashboard.Widget.Cms.GoogleDashboardSessions": {\n      "jsFiles": [\n        "/modules/cms/js/Widget/GoogleDashboardSessions.js"\n      ],\n      "cssFiles": []\n    },\n    "hDashboard.Widget.Cms.GoogleDashboardBrowsers": {\n      "jsFiles": [\n        "/modules/cms/js/Widget/GoogleDashboardBrowsers.js"\n      ],\n      "cssFiles": []\n    }\n  },\n  "menus": {\n    "page/index": {\n      "name": "Page view",\n      "dialog_url": "cms/admin/dialog"\n    },\n    "sitemap/index": {\n      "name": "Sitemap",\n      "dialog_url": null\n    }\n  }\n}\n'),
(108, 1, 'contact', 'Contact Module', 'Contact Module', '{\r\n  "email": {\r\n    "from_email": "fbapp@horisen.biz",\r\n    "from_name": "HORISEN CMS",\r\n    "reply_email": "boris@horisen.com",\r\n    "subject_contact": "subjectContactTranslationkey",\r\n    "mailtext_respond_contact": "mailtextRespondContactTranslationkey",\r\n    "landing_page_text": "landingPageText",\r\n    "transport": "smtp",\r\n    "confirmation_email": "yes",\r\n    "default_country": "no",\r\n    "ip_country_detection": "yes",\r\n    "default_country_on_top": "no",\r\n    "selected_countries": [\r\n      "CH"\r\n    ],\r\n    "show_gender_form_field_required": "no",\r\n    "show_gender_form_field": "no",\r\n    "first_name_form_field_count": "50",\r\n    "show_first_name_form_field_count": "yes",\r\n    "show_first_name_form_field_required": "yes",\r\n    "show_first_name_form_field": "yes",\r\n    "last_name_form_field_count": "50",\r\n    "show_last_name_form_field_count": "yes",\r\n    "show_last_name_form_field_required": "yes",\r\n    "show_last_name_form_field": "yes",\r\n    "email_form_field_count": "255",\r\n    "show_email_form_field_count": "yes",\r\n    "show_email_form_field_required": "yes",\r\n    "show_email_form_field": "yes",\r\n    "street_form_field_count": "50",\r\n    "show_street_form_field_count": "yes",\r\n    "show_street_form_field_required": "yes",\r\n    "show_street_form_field": "yes",\r\n    "zip_form_field_count": "50",\r\n    "show_zip_form_field_count": "yes",\r\n    "show_zip_form_field_required": "yes",\r\n    "show_zip_form_field": "yes",\r\n    "city_form_field_count": "50",\r\n    "show_city_form_field_count": "yes",\r\n    "show_city_form_field_required": "yes",\r\n    "show_city_form_field": "yes",\r\n    "country_form_field_count": "50",\r\n    "show_country_form_field_count": "no",\r\n    "show_country_form_field_required": "yes",\r\n    "show_country_form_field": "yes",\r\n    "phone_form_field_count": "50",\r\n    "show_phone_form_field_count": "yes",\r\n    "show_phone_form_field_required": "no",\r\n    "show_phone_form_field": "yes",\r\n    "mobile_form_field_count": "50",\r\n    "show_mobile_form_field_count": "yes",\r\n    "show_mobile_form_field_required": "no",\r\n    "show_mobile_form_field": "no",\r\n    "fax_form_field_count": "50",\r\n    "show_fax_form_field_count": "yes",\r\n    "show_fax_form_field_required": "no",\r\n    "show_fax_form_field": "no",\r\n    "description_form_field_count": "100",\r\n    "show_description_form_field_count": "yes",\r\n    "show_description_form_field_required": "yes",\r\n    "show_description_form_field": "yes",\r\n    "interest_form_field_count": "",\r\n    "show_interest_form_field_count": "no",\r\n    "show_interest_form_field_required": "yes",\r\n    "show_interest_form_field": "yes",\r\n    "parameters": {\r\n      "server": "mail.horisen.com",\r\n      "auth": "login",\r\n      "username": "fbapp@horisen.biz",\r\n      "password": "Fbh0r1sen*9",\r\n      "port": "587"\r\n    },\r\n    "to_emails": [\r\n      {\r\n        "name": "Milan",\r\n        "email": "boris@horisen.com"\r\n      }\r\n    ]\r\n  },\r\n  "captcha": {\r\n    "fontName": "font4.ttf",\r\n    "wordLen": "3",\r\n    "timeout": "300",\r\n    "width": "150",\r\n    "height": "40",\r\n    "dotNoiseLevel": "20",\r\n    "lineNoiseLevel": "2"\r\n  }\r\n}', '{"menus":{"generic\\/index":{"name":"Contact","dialog_url":null}}}');

-- --------------------------------------------------------

--
-- Table structure for table `teaser`
--

DROP TABLE IF EXISTS `teaser`;
CREATE TABLE IF NOT EXISTS `teaser` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `box_code` varchar(100) NOT NULL,
  `name` varchar(100) NOT NULL,
  `all_menu_items` enum('yes','no') NOT NULL DEFAULT 'no' COMMENT 'show on all menu items',
  `content` text COMMENT 'json content',
  PRIMARY KEY (`id`),
  KEY `code` (`box_code`,`all_menu_items`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `teaser_has_items`
--

DROP TABLE IF EXISTS `teaser_has_items`;
CREATE TABLE IF NOT EXISTS `teaser_has_items` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `teaser_id` int(10) unsigned NOT NULL,
  `item_id` int(10) unsigned NOT NULL,
  `order_num` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `teaser_id` (`teaser_id`,`item_id`,`order_num`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='which items teaser contains in which order' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `teaser_item`
--

DROP TABLE IF EXISTS `teaser_item`;
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
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `teaser_item_tr`
--

DROP TABLE IF EXISTS `teaser_item_tr`;
CREATE TABLE IF NOT EXISTS `teaser_item_tr` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `language` char(5) NOT NULL,
  `translation_id` int(10) unsigned NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `content` text COMMENT 'json data',
  PRIMARY KEY (`id`),
  UNIQUE KEY `language` (`language`,`translation_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='menu translations' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `teaser_menu_item`
--

DROP TABLE IF EXISTS `teaser_menu_item`;
CREATE TABLE IF NOT EXISTS `teaser_menu_item` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `teaser_id` int(10) unsigned NOT NULL,
  `menu_item_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `menu_item_id` (`menu_item_id`,`teaser_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='which menu items slider should be rendered' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `translate`
--

DROP TABLE IF EXISTS `translate`;
CREATE TABLE IF NOT EXISTS `translate` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `language_id` int(10) unsigned NOT NULL,
  `section` varchar(20) DEFAULT NULL,
  `key` text CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `value` text NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_trans` (`language_id`,`section`,`key`(100)),
  KEY `language_id` (`language_id`,`section`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1501 ;

--
-- Dumping data for table `translate`
--

INSERT INTO `translate` (`id`, `language_id`, `section`, `key`, `value`) VALUES
(945, 2, 'global', 'Username', ''),
(946, 2, 'global', 'Password', ''),
(947, 2, 'global', 'Login', ''),
(948, 2, 'global', 'Administration', ''),
(949, 2, 'global', 'Are you sure you want to delete this content item?\\nThis operation has no undo. We recommend unpublishing this content instead of deleting.', ''),
(950, 2, 'global', 'Select Category', ''),
(951, 2, 'global', 'Page', ''),
(952, 2, 'global', 'List ', ''),
(953, 2, 'global', 'Add new', ''),
(954, 2, 'global', 'Search', ''),
(955, 2, 'global', 'Select Status', ''),
(956, 2, 'global', 'Published', ''),
(957, 2, 'global', 'Draft', ''),
(958, 2, 'global', 'Select menu item', ''),
(959, 2, 'global', 'ID', ''),
(960, 2, 'global', 'Title', ''),
(961, 2, 'global', 'Code', ''),
(962, 2, 'global', 'URL ID', ''),
(963, 2, 'global', 'Type', ''),
(964, 2, 'global', 'User', ''),
(965, 2, 'global', 'Status', ''),
(966, 2, 'global', 'Posted', ''),
(967, 2, 'global', 'Edit', ''),
(968, 2, 'global', 'View Page', ''),
(969, 2, 'global', 'Delete', ''),
(970, 2, 'global', 'Choose Content Type', ''),
(971, 2, 'global', 'Settings', ''),
(972, 2, 'global', 'Logout', ''),
(973, 2, 'global', 'Dashboard', ''),
(974, 2, 'global', 'dashboard', ''),
(975, 2, 'global', 'Configuration', ''),
(976, 2, 'global', 'CMS', ''),
(977, 2, 'global', 'Content', ''),
(978, 2, 'global', 'Pages', ''),
(979, 2, 'global', 'Content blocks', ''),
(980, 2, 'global', 'Users', ''),
(981, 2, 'global', 'Roles', ''),
(982, 2, 'global', 'Contact', ''),
(983, 2, 'global', 'File Manager', ''),
(984, 2, 'global', 'Currently using the default file extensions and mime types! Here you can add more file extensions and mime types.', ''),
(985, 2, 'global', 'General information', ''),
(986, 2, 'global', 'Email information', ''),
(987, 2, 'global', 'Upload information', ''),
(988, 2, 'global', 'Google Analytics', ''),
(989, 2, 'global', 'Fb Settings', ''),
(990, 2, 'global', 'Twitter Settings', ''),
(991, 2, 'global', 'Fb Open-Graph Settings', ''),
(992, 2, 'global', 'Notes', ''),
(993, 2, 'global', 'Style information', ''),
(994, 2, 'global', 'Edit Configuration', ''),
(995, 2, 'global', 'Name', ''),
(996, 2, 'global', 'Active', ''),
(997, 2, 'global', 'Inactive', ''),
(998, 2, 'global', 'Emails', ''),
(999, 2, 'global', 'From Email', ''),
(1000, 2, 'global', 'From Name', ''),
(1001, 2, 'global', 'To Emails', ''),
(1002, 2, 'global', 'Email', ''),
(1003, 2, 'global', 'Add New Email', ''),
(1004, 2, 'global', 'Remove Last', ''),
(1005, 2, 'global', 'Email setings', ''),
(1006, 2, 'global', 'Email Port', ''),
(1007, 2, 'global', 'Email Auth', ''),
(1008, 2, 'global', 'Email Username', ''),
(1009, 2, 'global', 'Email Password', ''),
(1010, 2, 'global', 'Email Server', ''),
(1011, 2, 'global', 'Email transport type', ''),
(1012, 2, 'global', 'Select transport type', ''),
(1013, 2, 'global', 'Default file extensions and mime types:', ''),
(1014, 2, 'global', 'Extensions', ''),
(1015, 2, 'global', 'Mimetypes', ''),
(1016, 2, 'global', 'Custom file extensions and mime types:', ''),
(1017, 2, 'global', 'Add Extensions', ''),
(1018, 2, 'global', 'Add Mimetypes', ''),
(1019, 2, 'global', 'NOTICE', ''),
(1020, 2, 'global', 'Account Id', ''),
(1021, 2, 'global', 'Tracking Id', ''),
(1022, 2, 'global', 'Api Id', ''),
(1023, 2, 'global', 'Api Key', ''),
(1024, 2, 'global', 'Api Secret', ''),
(1025, 2, 'global', 'Canvas Page', ''),
(1026, 2, 'global', 'Domain', ''),
(1027, 2, 'global', 'Base Url', ''),
(1028, 2, 'global', 'Scope', ''),
(1029, 2, 'global', 'Redirect Uri', ''),
(1030, 2, 'global', 'Page Url', ''),
(1031, 2, 'global', 'User Id', ''),
(1032, 2, 'global', 'User Screenname', ''),
(1033, 2, 'global', 'Count of tweets to retrieve', ''),
(1034, 2, 'global', 'Logo Image', ''),
(1035, 2, 'global', 'Description', ''),
(1036, 2, 'global', 'Style JSON', ''),
(1037, 2, 'global', 'Save', ''),
(1038, 2, 'global', 'Are you sure you want to delete this User', ''),
(1039, 2, 'global', 'Choose Status', ''),
(1040, 2, 'global', 'Blocked', ''),
(1041, 2, 'global', 'Pending', ''),
(1042, 2, 'global', 'Choose Role', ''),
(1043, 2, 'global', 'Image', ''),
(1044, 2, 'global', 'First Name', 'First Name'),
(1045, 2, 'global', 'Last Name', 'Last Name'),
(1046, 2, 'global', 'Add new Page', ''),
(1047, 2, 'global', 'General', ''),
(1048, 2, 'global', 'Test', ''),
(1049, 2, 'global', 'Format', ''),
(1050, 2, 'global', 'HTML', ''),
(1051, 2, 'global', 'PHP', ''),
(1052, 2, 'global', 'Template Path', ''),
(1053, 2, 'global', 'Advanced', ''),
(1054, 2, 'global', 'Teaser', ''),
(1055, 2, 'global', 'Teaser Image', ''),
(1056, 2, 'global', 'Meta', ''),
(1057, 2, 'global', 'Keywords', ''),
(1058, 2, 'global', 'Categories', ''),
(1059, 2, 'global', 'Cancel', ''),
(1060, 2, 'global', 'Are you sure you want to delete this contact?\\nThis operation has no undo.', ''),
(1061, 2, 'global', 'Contacts', ''),
(1062, 2, 'global', 'Export', ''),
(1063, 2, 'global', 'From date', ''),
(1064, 2, 'global', 'To date', ''),
(1065, 2, 'global', 'Gender', 'Gender'),
(1066, 2, 'global', 'Street/Nr', ''),
(1067, 2, 'global', 'ZIP', 'ZIP'),
(1068, 2, 'global', 'City', 'City'),
(1069, 2, 'global', 'Country', 'Country'),
(1070, 2, 'global', 'Phone', 'Phone'),
(1071, 2, 'global', 'Mobile', ''),
(1072, 2, 'global', 'Fax', ''),
(1073, 2, 'global', 'Language', ''),
(1074, 2, 'global', 'published', ''),
(1075, 2, 'global', 'Edit Page', ''),
(1076, 2, 'global', 'You must give a non-empty value for field ''code''', ''),
(1077, 2, 'global', 'You must give a non-empty value for field ''teaser''', ''),
(1078, 2, 'global', 'You must give a non-empty value for field ''meta''', ''),
(1079, 2, 'global', 'You must give a non-empty value for field ''data''', ''),
(1080, 2, 'global', 'Page saved.', ''),
(1081, 1, 'global', 'German', 'German'),
(1082, 1, 'global', 'English', 'English'),
(1083, 1, 'global', 'French', 'French'),
(1084, 1, 'global', 'Italian', 'Italian'),
(1085, 1, 'global', 'Blues Nights Gossau. All rights reserved', 'Blues Nights Gossau. All rights reserved'),
(1086, 1, 'global', 'Contact', 'Contact'),
(1087, 1, 'global', 'Gender', 'Anrede'),
(1088, 1, 'global', 'Herr', 'Herr'),
(1089, 1, 'global', 'Frau', 'Frau'),
(1090, 1, 'global', 'Email', 'Email'),
(1091, 1, 'global', 'First Name', 'Vorname'),
(1092, 1, 'global', 'Last Name', 'Nachname'),
(1093, 1, 'global', 'Street', 'Street'),
(1094, 1, 'global', 'ZIP', 'PLZ'),
(1095, 1, 'global', 'City', 'Ort'),
(1096, 1, 'global', 'Country', 'Land'),
(1097, 1, 'global', 'Phone', 'Telefon'),
(1098, 1, 'global', 'Message', 'Mitteilung'),
(1099, 1, 'global', 'Load new image', 'Load new image'),
(1100, 1, 'global', 'Send', 'Send'),
(1101, 1, 'global', 'You must give a non-empty value for field ''first_name''', 'You must give a non-empty value for field ''first_name'''),
(1102, 1, 'global', 'You must give a non-empty value for field ''last_name''', 'You must give a non-empty value for field ''last_name'''),
(1103, 1, 'global', 'You must give a non-empty value for field ''email''', 'You must give a non-empty value for field ''email'''),
(1104, 1, 'global', 'You must give a non-empty value for field ''street''', 'You must give a non-empty value for field ''street'''),
(1105, 1, 'global', 'You must give a non-empty value for field ''zip''', 'You must give a non-empty value for field ''zip'''),
(1106, 1, 'global', 'You must give a non-empty value for field ''city''', 'You must give a non-empty value for field ''city'''),
(1107, 1, 'global', 'You must give a non-empty value for field ''country''', 'You must give a non-empty value for field ''country'''),
(1108, 1, 'global', 'You must give a non-empty value for field ''phone''', 'You must give a non-empty value for field ''phone'''),
(1109, 1, 'global', 'You must give a non-empty value for field ''description''', 'You must give a non-empty value for field ''description'''),
(1110, 1, 'global', 'Contact form', 'Contact form'),
(1111, 1, 'global', 'Contact Form Intro text', 'Contact Form Intro text'),
(1112, 1, 'global', 'Gender:', 'Anrede:'),
(1113, 1, 'global', 'Zip', 'PLZ'),
(1114, 1, 'global', 'zip', 'PLZ'),
(1115, 1, 'global', 'Select Country', 'Select Country'),
(1116, 1, 'global', 'Country:', 'Land:'),
(1117, 1, 'global', 'Telephone', 'Telephone'),
(1118, 1, 'global', 'Mobile', 'Mobile'),
(1119, 1, 'global', 'Fax', 'Fax'),
(1120, 1, 'global', 'Picture code', 'Picture code'),
(1121, 1, 'global', 'Security check', 'Security check'),
(1122, 1, 'global', 'You must give a non-empty value for field ''mobile''', 'You must give a non-empty value for field ''mobile'''),
(1123, 1, 'global', 'You must give a non-empty value for field ''fax''', 'You must give a non-empty value for field ''fax'''),
(1124, 2, 'global', 'Site Map Configuration', ''),
(1125, 2, 'global', 'Site Map', ''),
(1126, 2, 'global', 'Choose the menu for sitemap', ''),
(1127, 2, 'global', 'All menus', ''),
(1128, 2, 'global', 'Free', ''),
(1129, 2, 'global', 'In Use', ''),
(1130, 2, 'global', 'Misc', ''),
(1131, 2, 'global', 'Statistics', ''),
(1132, 2, 'global', 'Cms', ''),
(1133, 2, 'global', 'Permissions', ''),
(1134, 2, 'global', 'Manage Pages', ''),
(1135, 2, 'global', 'Manage Menus', ''),
(1136, 2, 'global', '<strong>%s</strong> in use out of total <strong>%s</strong>, free <strong>%s</strong>', ''),
(1137, 2, 'global', 'Manage Files and Directories', ''),
(1138, 2, 'global', 'Visit Front Site', ''),
(1139, 2, 'global', 'Manage Your Account', ''),
(1140, 2, 'global', 'Manage Users', ''),
(1141, 2, 'global', 'Manage Roles', ''),
(1142, 2, 'global', 'Visits', ''),
(1143, 2, 'global', 'Visitors', ''),
(1144, 2, 'global', 'Pageviews', ''),
(1145, 2, 'global', 'Avg. time on site', ''),
(1146, 2, 'global', 'Avg. bounce rate', ''),
(1147, 2, 'global', 'Percent new visits', ''),
(1148, 2, 'global', 'Details on Google Analytics', ''),
(1149, 1, 'global', '''%value%'' does not match the expected structure for a DNS hostname', '''%value%'' does not match the expected structure for a DNS hostname'),
(1150, 1, 'global', 'Thank you', 'Thank you'),
(1151, 1, 'global', 'KONTAKT', 'KONTAKT'),
(1152, 1, 'global', 'Your question is submitted and we will answer as soon as possible.', 'Your question is submitted and we will answer as soon as possible.'),
(1153, 1, 'global', 'Please specify First Name.', 'Please specify First Name.'),
(1154, 1, 'global', 'Please specify Last Name.', 'Please specify Last Name.'),
(1155, 1, 'global', 'Please specify Email.', 'Please specify Email.'),
(1156, 2, 'global', 'Contact deleted.', ''),
(1157, 1, 'global', 'Do you need some more information?', 'Do you need some more information?'),
(1158, 1, 'global', 'First name', 'Vorname'),
(1159, 1, 'global', 'Your first name', 'Ihr Vorname'),
(1160, 1, 'global', 'Last name', 'Nachname'),
(1161, 1, 'global', 'Your last name', 'Ihr Nachname'),
(1162, 1, 'global', 'Company', 'Company'),
(1163, 1, 'global', 'Your company name', 'Your company name'),
(1164, 1, 'global', 'e.g. your.name@example.com', 'z.B. ihr.name@beispiel.com'),
(1165, 1, 'global', 'e.g. +41 21 1234567', 'z.B. +41 71 1234567'),
(1166, 1, 'global', 'e.g. +41 78 1234567', 'z.B. +41 78 1234567'),
(1167, 1, 'global', 'e.g. 9400', 'z.B. 9400'),
(1168, 1, 'global', 'e.g. Rorschach', 'z.B. Rorschach'),
(1169, 1, 'global', 'Choose your country', 'Wählen Sie ihr Land'),
(1170, 1, 'global', 'Your message', 'Ihre Mitteilung'),
(1171, 1, 'global', 'Send feedback', 'Senden'),
(1172, 1, 'global', 'We will advise you personally and without obligation', 'We will advise you personally and without obligation'),
(1173, 1, 'global', 'Thank you! <br>We''ll get in touch with you as soon as possible.', 'Thank you! <br>We''ll get in touch with you as soon as possible.'),
(1174, 1, 'global', 'CONTACT US', 'CONTACT US'),
(1175, 1, 'global', 'HORISEN AG', 'HORISEN AG'),
(1176, 1, 'global', 'Wrong username or password', 'Wrong username or password'),
(1177, 1, 'global', 'Free', 'Free'),
(1178, 1, 'global', 'In Use', 'In Use'),
(1179, 1, 'global', 'Misc', 'Misc'),
(1180, 1, 'global', 'Statistics', 'Statistics'),
(1181, 1, 'global', 'Cms', 'Cms'),
(1182, 1, 'global', 'Permissions', 'Permissions'),
(1183, 1, 'global', 'Administration', 'Administration'),
(1184, 1, 'global', 'Settings', 'Settings'),
(1185, 1, 'global', 'Logout', 'Logout'),
(1186, 1, 'global', 'Dashboard', 'Dashboard'),
(1187, 1, 'global', 'dashboard', 'dashboard'),
(1188, 1, 'global', 'Configuration', 'Configuration'),
(1189, 1, 'global', 'CMS', 'CMS'),
(1190, 1, 'global', 'Content', 'Content'),
(1191, 1, 'global', 'Pages', 'Pages'),
(1192, 1, 'global', 'Content blocks', 'Content blocks'),
(1193, 1, 'global', 'Teaser', 'Teaser'),
(1194, 1, 'global', 'Users', 'Users'),
(1195, 1, 'global', 'Roles', 'Roles'),
(1196, 1, 'global', 'File Manager', 'File Manager'),
(1197, 1, 'global', 'Manage Pages', 'Manage Pages'),
(1198, 1, 'global', 'Manage Menus', 'Manage Menus'),
(1199, 1, 'global', '<strong>%s</strong> in use out of total <strong>%s</strong>, free <strong>%s</strong>', '<strong>%s</strong> in use out of total <strong>%s</strong>, free <strong>%s</strong>'),
(1200, 1, 'global', 'Manage Files and Directories', 'Manage Files and Directories'),
(1201, 1, 'global', 'Visit Front Site', 'Visit Front Site'),
(1202, 1, 'global', 'Visits', 'Visits'),
(1203, 1, 'global', 'Visitors', 'Visitors'),
(1204, 1, 'global', 'Pageviews', 'Pageviews'),
(1205, 1, 'global', 'Avg. time on site', 'Avg. time on site'),
(1206, 1, 'global', 'Avg. bounce rate', 'Avg. bounce rate'),
(1207, 1, 'global', 'Percent new visits', 'Percent new visits'),
(1208, 1, 'global', 'Details on Google Analytics', 'Details on Google Analytics'),
(1209, 1, 'global', 'Manage Your Account', 'Manage Your Account'),
(1210, 1, 'global', 'Manage Users', 'Manage Users'),
(1211, 1, 'global', 'Manage Roles', 'Manage Roles'),
(1212, 1, 'global', 'Are you sure you want to delete this content item?\\nThis operation has no undo. We recommend unpublishing this content instead of deleting.', 'Are you sure you want to delete this content item?\\nThis operation has no undo. We recommend unpublishing this content instead of deleting.'),
(1213, 1, 'global', 'Select Category', 'Select Category'),
(1214, 1, 'global', 'Page', 'Page'),
(1215, 1, 'global', 'List', 'List'),
(1216, 1, 'global', 'Add new', 'Add new'),
(1217, 1, 'global', 'Search', 'Search'),
(1218, 1, 'global', 'Select Status', 'Select Status'),
(1219, 1, 'global', 'Published', 'Published'),
(1220, 1, 'global', 'Draft', 'Draft'),
(1221, 1, 'global', 'Select menu item', 'Select menu item'),
(1222, 1, 'global', 'ID', 'ID'),
(1223, 1, 'global', 'Title', 'Title'),
(1224, 1, 'global', 'Code', 'Code'),
(1225, 1, 'global', 'URL ID', 'URL ID'),
(1226, 1, 'global', 'Type', 'Type'),
(1227, 1, 'global', 'User', 'User'),
(1228, 1, 'global', 'Status', 'Status'),
(1229, 1, 'global', 'Posted', 'Posted'),
(1230, 1, 'global', 'Edit', 'Edit'),
(1231, 1, 'global', 'View Page', 'View Page'),
(1232, 1, 'global', 'Delete', 'Delete'),
(1233, 1, 'global', 'Choose Content Type', 'Choose Content Type'),
(1234, 1, 'global', 'published', 'published'),
(1235, 1, 'global', 'Traveling by car', 'Traveling by car'),
(1236, 1, 'global', 'Route planner', 'Route planner'),
(1237, 1, 'global', 'Directions', 'Directions'),
(1238, 1, 'global', 'Travelling by public transport', 'Travelling by public transport'),
(1239, 1, 'global', 'Schedule', 'Schedule'),
(1240, 1, 'global', 'Username', 'Username'),
(1241, 1, 'global', 'Password', 'Password'),
(1242, 1, 'global', 'Login', 'Login'),
(1243, 1, 'global', 'Please specify First name.', 'Please specify First name.'),
(1244, 1, 'global', 'Please specify Last name.', 'Please specify Last name.'),
(1245, 1, 'global', 'Please specify Message.', 'Please specify Message.'),
(1246, 1, 'global', 'You must give a non-empty value for field ''honepot''', 'You must give a non-empty value for field ''honepot'''),
(1247, 1, 'global', 'Mr.', 'Herr'),
(1248, 1, 'global', 'Mrs.', 'Frau'),
(1249, 1, 'global', 'Please specify Gender.', 'DEPlease specify Gender.'),
(1250, 1, 'global', 'New Contact Form Submission', 'New Contact Form Submission'),
(1251, 1, 'global', 'Thank you for your time', 'Thank you for your time'),
(1252, 1, 'global', 'Please specify Captcha.', 'Please specify Captcha.'),
(1253, 1, 'global', 'Currently using the default file extensions and mime types! Here you can add more file extensions and mime types.', 'Currently using the default file extensions and mime types! Here you can add more file extensions and mime types.'),
(1254, 1, 'global', 'General information', 'General information'),
(1255, 1, 'global', 'Email information', 'Email information'),
(1256, 1, 'global', 'Upload information', 'Upload information'),
(1257, 1, 'global', 'Google Analytics', 'Google Analytics'),
(1258, 1, 'global', 'Fb Settings', 'Fb Settings'),
(1259, 1, 'global', 'Twitter Settings', 'Twitter Settings'),
(1260, 1, 'global', 'Fb Open-Graph Settings', 'Fb Open-Graph Settings'),
(1261, 1, 'global', 'Notes', 'Notes'),
(1262, 1, 'global', 'Style information', 'Style information'),
(1263, 1, 'global', 'Edit Configuration', 'Edit Configuration'),
(1264, 1, 'global', 'Name', 'Name'),
(1265, 1, 'global', 'Active', 'Active'),
(1266, 1, 'global', 'Inactive', 'Inactive'),
(1267, 1, 'global', 'Emails', 'Emails'),
(1268, 1, 'global', 'From Email', 'From Email'),
(1269, 1, 'global', 'From Name', 'From Name'),
(1270, 1, 'global', 'To Emails', 'To Emails'),
(1271, 1, 'global', 'Add New Email', 'Add New Email'),
(1272, 1, 'global', 'Remove Last', 'Remove Last'),
(1273, 1, 'global', 'Email setings', 'Email setings'),
(1274, 1, 'global', 'Email Port', 'Email Port'),
(1275, 1, 'global', 'Email Auth', 'Email Auth'),
(1276, 1, 'global', 'Email Username', 'Email Username'),
(1277, 1, 'global', 'Email Password', 'Email Password'),
(1278, 1, 'global', 'Email Server', 'Email Server'),
(1279, 1, 'global', 'Email transport type', 'Email transport type'),
(1280, 1, 'global', 'Select transport type', 'Select transport type'),
(1281, 1, 'global', 'Default file extensions and mime types:', 'Default file extensions and mime types:'),
(1282, 1, 'global', 'Extensions', 'Extensions'),
(1283, 1, 'global', 'Mimetypes', 'Mimetypes'),
(1284, 1, 'global', 'Custom file extensions and mime types:', 'Custom file extensions and mime types:'),
(1285, 1, 'global', 'Add Extensions', 'Add Extensions'),
(1286, 1, 'global', 'Add Mimetypes', 'Add Mimetypes'),
(1287, 1, 'global', 'NOTICE', 'NOTICE'),
(1288, 1, 'global', 'Account Id', 'Account Id'),
(1289, 1, 'global', 'Tracking Id', 'Tracking Id'),
(1290, 1, 'global', 'Api Id', 'Api Id'),
(1291, 1, 'global', 'Api Key', 'Api Key'),
(1292, 1, 'global', 'Api Secret', 'Api Secret'),
(1293, 1, 'global', 'Canvas Page', 'Canvas Page'),
(1294, 1, 'global', 'Domain', 'Domain'),
(1295, 1, 'global', 'Base Url', 'Base Url'),
(1296, 1, 'global', 'Scope', 'Scope'),
(1297, 1, 'global', 'Redirect Uri', 'Redirect Uri'),
(1298, 1, 'global', 'Page Url', 'Page Url'),
(1299, 1, 'global', 'User Id', 'User Id'),
(1300, 1, 'global', 'User Screenname', 'User Screenname'),
(1301, 1, 'global', 'Count of tweets to retrieve', 'Count of tweets to retrieve'),
(1302, 1, 'global', 'Logo Image', 'Logo Image'),
(1303, 1, 'global', 'Browse', 'Browse'),
(1304, 1, 'global', 'Clear', 'Clear'),
(1305, 1, 'global', 'Description', 'Description'),
(1306, 1, 'global', 'Style JSON', 'Style JSON'),
(1307, 1, 'global', 'Save', 'Save'),
(1308, 1, 'global', 'Select Content Type', 'Select Content Type'),
(1309, 1, 'global', 'Are you sure you want to delete this User', 'Are you sure you want to delete this User'),
(1310, 1, 'global', 'Choose Status', 'Choose Status'),
(1311, 1, 'global', 'Blocked', 'Blocked'),
(1312, 1, 'global', 'Pending', 'Pending'),
(1313, 1, 'global', 'Choose Role', 'Choose Role'),
(1314, 1, 'global', 'Image', 'Image'),
(1315, 1, 'global', 'Are you sure you want to delete this contact?\\nThis operation has no undo.', 'Are you sure you want to delete this contact?\\nThis operation has no undo.'),
(1316, 1, 'global', 'Contacts', 'Contacts'),
(1317, 1, 'global', 'Export', 'Export'),
(1318, 1, 'global', 'From date', 'From date'),
(1319, 1, 'global', 'To date', 'To date'),
(1320, 1, 'global', 'Street/Nr', 'Street/Nr'),
(1321, 1, 'global', 'Language', 'Language'),
(1322, 1, 'global', 'male', 'male'),
(1323, 1, 'global', '', ''),
(1324, 1, 'global', 'Edit Page', 'Edit Page'),
(1325, 1, 'global', 'General', 'General'),
(1326, 1, 'global', 'Format', 'Format'),
(1327, 1, 'global', 'HTML', 'HTML'),
(1328, 1, 'global', 'PHP', 'PHP'),
(1329, 1, 'global', 'Template Path', 'Template Path'),
(1330, 1, 'global', 'Advanced', 'Advanced'),
(1331, 1, 'global', 'Teaser Image', 'Teaser Image'),
(1332, 1, 'global', 'Meta', 'Meta'),
(1333, 1, 'global', 'Keywords', 'Keywords'),
(1334, 1, 'global', 'Categories', 'Categories'),
(1335, 1, 'global', 'Cancel', 'Cancel'),
(1336, 1, 'global', 'Import from excel', 'Import from excel'),
(1337, 1, 'global', 'XLS file template', 'XLS file template'),
(1338, 1, 'global', 'Import', 'Import'),
(1339, 1, 'global', 'Translation', 'Translation'),
(1340, 1, 'global', 'Data Saved.', 'Data Saved.'),
(1360, 1, 'global', 'Your name', 'Your name'),
(1341, 2, 'global', 'Gender:', 'Gender'),
(1342, 2, 'global', 'Mr.', 'Mr.'),
(1343, 2, 'global', 'Mrs.', 'Mrs.'),
(1344, 2, 'global', 'e.g. your.name@example.com', 'e.g. your.name@example.com'),
(1345, 2, 'global', 'e.g. +41 21 1234567', 'e.g. +41 21 1234567'),
(1346, 2, 'global', 'e.g. +41 78 1234567', 'e.g. +41 78 1234567'),
(1347, 2, 'global', 'e.g. 9400', 'e.g. 9400'),
(1348, 2, 'global', 'e.g. Rorschach', 'e.g. Rorschach'),
(1349, 2, 'global', 'zip', 'zip'),
(1350, 2, 'global', 'Zip', 'Zip'),
(1351, 2, 'global', 'Your first name', 'Your first name'),
(1352, 2, 'global', 'Your last name', 'Your last name'),
(1353, 2, 'global', 'Your message', 'Your Message'),
(1354, 2, 'global', 'Send feedback', 'Send feedback'),
(1355, 2, 'global', 'Choose your country', 'Choose your country'),
(1356, 2, 'global', 'First name', 'First name'),
(1357, 2, 'global', 'Last name', 'Last name'),
(1358, 2, 'global', 'Country:', 'Country:'),
(1359, 2, 'global', 'Message', 'Message'),
(1361, 1, 'global', 'Address', 'Address'),
(1362, 1, 'global', 'Zip, City', 'Zip, City'),
(1363, 1, 'global', '9200, e.g. Rorschach', '9200, e.g. Rorschach'),
(1364, 1, 'global', 'Maker', 'Hersteller'),
(1365, 1, 'global', 'Car maker', 'Car maker'),
(1366, 1, 'global', 'Model', 'Modell'),
(1367, 1, 'global', 'Car model', 'Car model'),
(1368, 1, 'global', 'Horsepower', ' Leistung in PS'),
(1369, 1, 'global', 'Horsepower in PS', 'Horsepower in PS'),
(1370, 1, 'global', 'Year', 'Jahrgang'),
(1371, 1, 'global', 'Year of car', 'Year of car'),
(1372, 1, 'global', 'Price', 'Preisvorstellung in CHF'),
(1373, 1, 'global', 'Asking Price in CHF', 'Asking Price in CHF'),
(1374, 1, 'global', 'Condition', ' Zustand Allgemein'),
(1375, 1, 'global', 'General condition', 'General condition'),
(1376, 1, 'global', 'Thank you!', 'Thank you!'),
(1377, 1, 'global', 'Ankauf', 'Ankauf'),
(1378, 1, 'global', '250', '250'),
(1379, 1, 'global', '2014', '2014'),
(1380, 1, 'global', '1000', '1000'),
(1381, 1, 'global', 'Please specify Name.', 'Please specify Name.'),
(1382, 1, 'global', 'Please specify Address.', 'Please specify Address.'),
(1383, 1, 'global', 'Please specify Zip, City.', 'Please specify Zip, City.'),
(1384, 1, 'global', 'Please specify Phone.', 'Please specify Phone.'),
(1385, 1, 'global', 'Please specify Maker.', 'Please specify Maker.'),
(1386, 1, 'global', 'Please specify Model.', 'Please specify Model.'),
(1387, 1, 'global', 'You must give a non-empty value for field ''horsepower''', 'You must give a non-empty value for field ''horsepower'''),
(1388, 1, 'global', 'You must give a non-empty value for field ''year''', 'You must give a non-empty value for field ''year'''),
(1389, 1, 'global', 'Please specify Price.', 'Please specify Price.'),
(1390, 1, 'global', 'You must give a non-empty value for field ''message''', 'You must give a non-empty value for field ''message'''),
(1391, 1, 'global', 'e.g. Meine Strasse', 'e.g. Meine Strasse'),
(1392, 1, 'global', 'e.g.9400', 'e.g.9400'),
(1393, 1, 'global', 'Your password expired. Please update.', 'Your password expired. Please update.'),
(1394, 1, 'global', 'Backgrounds', 'Backgrounds'),
(1395, 1, 'global', 'Menus', 'Menus'),
(1396, 1, 'global', 'Redirection', 'Redirection'),
(1397, 1, 'global', 'Route', 'Route'),
(1398, 1, 'global', 'Menu', 'Menu'),
(1399, 1, 'global', 'Menus Items', 'Menus Items'),
(1400, 1, 'global', 'Add new Item', 'Add new Item'),
(1401, 1, 'global', 'Add new Menu', 'Add new Menu'),
(1402, 1, 'global', 'New Menu', 'New Menu'),
(1403, 1, 'global', 'Hidden', 'Hidden'),
(1404, 1, 'global', 'Add SubMenu Item', 'Add SubMenu Item'),
(1405, 1, 'global', 'View', 'View'),
(1406, 1, 'global', 'File upload', 'File upload'),
(1407, 1, 'global', 'Upload file', 'Upload file'),
(1408, 1, 'global', 'clear', 'clear'),
(1409, 1, 'global', 'Value is required and can''t be empty', 'Value is required and can''t be empty'),
(1410, 1, 'global', 'You must give a non-empty value for field ''fileupload2''', 'You must give a non-empty value for field ''fileupload2'''),
(1411, 1, 'global', 'You must give a non-empty value for field ''fileupload3''', 'You must give a non-empty value for field ''fileupload3'''),
(1412, 1, 'global', 'You must give a non-empty value for field ''fileupload4''', 'You must give a non-empty value for field ''fileupload4'''),
(1413, 1, 'global', 'You must give a non-empty value for field ''fileupload5''', 'You must give a non-empty value for field ''fileupload5'''),
(1414, 1, 'global', 'You must give a non-empty value for field ''fileupload6''', 'You must give a non-empty value for field ''fileupload6'''),
(1415, 1, 'global', 'e.g.9200', 'e.g.9200'),
(1416, 1, 'global', 'e.g.Rorschach', 'e.g.Rorschach'),
(1417, 1, 'global', 'Bild 1', 'Bild 1'),
(1418, 1, 'global', 'Wählen Sie ein Bild aus', 'Wählen Sie ein Bild aus'),
(1419, 1, 'global', 'Bild 2', 'Bild 2'),
(1420, 1, 'global', 'Bild 3', 'Bild 3'),
(1421, 1, 'global', 'Bild 4', 'Bild 4'),
(1422, 1, 'global', 'Dokument 1', 'Dokument 1'),
(1423, 1, 'global', 'Wählen Sie eine Datei aus', 'Wählen Sie eine Datei aus'),
(1424, 1, 'global', 'Dokument 2', 'Dokument 2'),
(1425, 1, 'global', 'Too few files, minimum ''%min%'' are expected but ''%count%'' are given', 'Too few files, minimum ''%min%'' are expected but ''%count%'' are given'),
(1426, 1, 'global', 'File ''%value%'' exceeds the defined ini size', 'File ''%value%'' exceeds the defined ini size'),
(1427, 1, 'global', 'File ''%value%'' has a false extension', 'File ''%value%'' has a false extension'),
(1428, 1, 'global', 'File ''%value%'' has a false mimetype of ''%type%''', 'File ''%value%'' has a false mimetype of ''%type%'''),
(1429, 1, 'global', 'Please specify Zip.', 'Please specify Zip.'),
(1430, 1, 'global', 'Please specify City.', 'Please specify City.'),
(1431, 1, 'global', 'Please specify Street.', 'Please specify Street.'),
(1432, 1, 'global', 'Page not found', 'Page not found'),
(1433, 1, 'global', 'DEPlease specify Gender.', 'DEPlease specify Gender.'),
(1434, 1, 'global', 'Email or username', 'Email or username'),
(1435, 1, 'global', 'Google', 'Google'),
(1436, 1, 'global', 'Please visit <a href=''/'' target=''_blank''>public</a> part of the website to start inline editing.', 'Please visit <a href=''/'' target=''_blank''>public</a> part of the website to start inline editing.'),
(1437, 1, 'global', 'Enable inline editing', 'Enable inline editing'),
(1438, 1, 'global', 'Disable inline editing', 'Disable inline editing'),
(1439, 1, 'global', 'Your website is not visible to search engines! </br> Please click <a href=''/de/admin/google-service/edit'' target=''_blank''>here</a> to make it visible.', 'Your website is not visible to search engines! </br> Please click <a href=''/de/admin/google-service/edit'' target=''_blank''>here</a> to make it visible.'),
(1440, 1, 'global', 'Home', 'Home'),
(1441, 1, 'global', 'Google Services', 'Google Services'),
(1442, 1, 'global', 'Category', 'Category'),
(1443, 1, 'global', 'API Pages', 'API Pages'),
(1444, 1, 'global', 'Products', 'Products'),
(1445, 1, 'global', 'Sliders', 'Sliders'),
(1446, 1, 'global', 'Background Changer', 'Background Changer'),
(1447, 1, 'global', 'Themes Manager', 'Themes Manager'),
(1448, 1, 'global', 'Please setup analytics.', 'Please setup analytics.'),
(1449, 1, 'global', 'Please login with your Google account.', 'Please login with your Google account.'),
(1450, 1, 'global', 'Edit User', 'Edit User'),
(1451, 1, 'global', 'Role', 'Role'),
(1452, 1, 'global', 'Select Role', 'Select Role'),
(1453, 1, 'global', 'Select Language', 'Select Language'),
(1454, 1, 'global', 'New Password', 'New Password'),
(1455, 1, 'global', 'New Password confirmation', 'New Password confirmation'),
(1456, 1, 'global', 'Password must contain at least 1 special character.', 'Password must contain at least 1 special character.'),
(1457, 1, 'global', 'You must give a non-empty value for field ''image_path''', 'You must give a non-empty value for field ''image_path'''),
(1458, 1, 'global', 'User saved.', 'User saved.'),
(1459, 1, 'global', 'your account is temporarily blocked due to too many invalid login attempts', 'your account is temporarily blocked due to too many invalid login attempts'),
(1460, 1, 'global', 'HORISEN CMS Skeleton - Your account is temporarily blocked', 'HORISEN CMS Skeleton - Your account is temporarily blocked'),
(1461, 1, 'global', 'Custom search', 'Custom search'),
(1462, 1, 'global', 'Webmaster Tools', 'Webmaster Tools'),
(1463, 1, 'global', 'Google Tags Manager', 'Google Tags Manager'),
(1464, 1, 'global', 'Web Robots', 'Web Robots'),
(1465, 1, 'global', 'Google Custom Search ', 'Google Custom Search '),
(1466, 1, 'global', 'Google Custom Search lets user to include a search engine on his website to help his visitors find the information they''re looking for. ', 'Google Custom Search lets user to include a search engine on his website to help his visitors find the information they''re looking for. '),
(1467, 1, 'global', 'Edit GSC Configuration', 'Edit GSC Configuration'),
(1468, 1, 'global', 'Toggles ON and OFF Google''s search engine on front end of users web site.', 'Toggles ON and OFF Google''s search engine on front end of users web site.'),
(1469, 1, 'global', 'Here you can name your search engine.', 'Here you can name your search engine.'),
(1470, 1, 'global', 'Cx - Search Engine ID', 'Cx - Search Engine ID'),
(1471, 1, 'global', 'You can find Custom Search Engine ID on the ''Basics'' tab, of the Custom Search control panel, of your account. ', 'You can find Custom Search Engine ID on the ''Basics'' tab, of the Custom Search control panel, of your account. '),
(1472, 1, 'global', 'Results Custom Styles', 'Results Custom Styles'),
(1473, 1, 'global', 'Element', 'Element'),
(1474, 1, 'global', 'Font color', 'Font color'),
(1475, 1, 'global', 'Font size', 'Font size'),
(1476, 1, 'global', 'Snippet', 'Snippet'),
(1477, 1, 'global', 'Bottom url', 'Bottom url'),
(1478, 1, 'global', 'Preview', 'Preview'),
(1479, 1, 'global', 'Webmaster Tools is a free service offered by Google that helps to monitor and maintain site''s presence in Google Search results. It help understand how Google views site and optimize its performance in search results. ', 'Webmaster Tools is a free service offered by Google that helps to monitor and maintain site''s presence in Google Search results. It help understand how Google views site and optimize its performance in search results. '),
(1480, 1, 'global', 'Toggles activities of Webmaster Tools ON and OFF. ', 'Toggles activities of Webmaster Tools ON and OFF. '),
(1481, 1, 'global', 'Web Master Meta tag', 'Web Master Meta tag'),
(1482, 1, 'global', 'Allows Google to verify user''s ownership over his web site. User can obtain his Meta Tag in Google''s Webmaster Tools.', 'Allows Google to verify user''s ownership over his web site. User can obtain his Meta Tag in Google''s Webmaster Tools.'),
(1483, 1, 'global', 'Google Tag Manager ', 'Google Tag Manager '),
(1484, 1, 'global', 'Google Manager is powerful free tool that allows user it self to update all the tags from site without editing site code. This reduces error, puts user in charge, and drastically reduces the time of dealing with tags. ', 'Google Manager is powerful free tool that allows user it self to update all the tags from site without editing site code. This reduces error, puts user in charge, and drastically reduces the time of dealing with tags. '),
(1485, 1, 'global', 'Toggles ON and OFF tag manager function on web site. ', 'Toggles ON and OFF tag manager function on web site. '),
(1486, 1, 'global', 'Google Tag Manager Id', 'Google Tag Manager Id'),
(1487, 1, 'global', 'Google Tag Manager ID is the piece of code, that user gets when create Tag Manager account. It can be found in the list of containers in user''s Google Tag Manager account, under ''ID'' section. ', 'Google Tag Manager ID is the piece of code, that user gets when create Tag Manager account. It can be found in the list of containers in user''s Google Tag Manager account, under ''ID'' section. '),
(1488, 1, 'global', 'This operation makes your website to be visible or not, for Google''s search. You can make content from your web site to be included in global Google''s search for user from all around the world. ', 'This operation makes your website to be visible or not, for Google''s search. You can make content from your web site to be included in global Google''s search for user from all around the world. '),
(1489, 1, 'global', 'Hidden for search engines', 'Hidden for search engines'),
(1490, 1, 'global', 'Visible for search engines', 'Visible for search engines'),
(1491, 1, 'global', 'Set ''Allow'' to toggle ON, or ''Disallow'' to toggle OFF this function. ', 'Set ''Allow'' to toggle ON, or ''Disallow'' to toggle OFF this function. '),
(1492, 1, 'global', 'Theme Management', 'Theme Management'),
(1493, 1, 'global', 'Activate', 'Activate'),
(1494, 1, 'global', 'Export to excel', 'Export to excel'),
(1495, 1, 'global', 'Key', 'Key'),
(1496, 1, 'global', 'Section', 'Section'),
(1497, 1, 'global', 'Close', 'Close'),
(1498, 1, 'global', 'Configuration saved.', 'Configuration saved.'),
(1499, 1, 'global', 'Your password expires in {days} days. Please update.', 'Your password expires in {days} days. Please update.'),
(1500, 1, 'global', 'We are glad to help you in every way', 'We are glad to help you in every way');

-- --------------------------------------------------------

--
-- Table structure for table `translate_key`
--

DROP TABLE IF EXISTS `translate_key`;
CREATE TABLE IF NOT EXISTS `translate_key` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `key` text NOT NULL,
  `menu_id` int(11) unsigned DEFAULT NULL,
  `type_id` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `key_idx` (`key`(100)),
  KEY `menu_id` (`menu_id`,`type_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='translation keys categorisation' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `translate_language`
--

DROP TABLE IF EXISTS `translate_language`;
CREATE TABLE IF NOT EXISTS `translate_language` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(5) NOT NULL,
  `name` varchar(20) NOT NULL,
  `default` enum('yes','no') NOT NULL DEFAULT 'no' COMMENT 'default language',
  `front_enabled` enum('yes','no') NOT NULL DEFAULT 'yes' COMMENT 'is lang available on frontend',
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

--
-- Dumping data for table `translate_language`
--

INSERT INTO `translate_language` (`id`, `code`, `name`, `default`, `front_enabled`) VALUES
(1, 'de', 'German', 'yes', 'yes'),
(2, 'en', 'English', 'no', 'yes'),
(4, 'fr', 'French', 'no', 'yes'),
(5, 'it', 'Italian', 'no', 'yes');

-- --------------------------------------------------------

--
-- Table structure for table `translate_menu`
--

DROP TABLE IF EXISTS `translate_menu`;
CREATE TABLE IF NOT EXISTS `translate_menu` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='defines key cource pages' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `translate_type`
--

DROP TABLE IF EXISTS `translate_type`;
CREATE TABLE IF NOT EXISTS `translate_type` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='defines key types' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `upgrade_db_log`
--

DROP TABLE IF EXISTS `upgrade_db_log`;
CREATE TABLE IF NOT EXISTS `upgrade_db_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `file` varchar(255) DEFAULT NULL,
  `branch` varchar(10) NOT NULL DEFAULT 'trunk' COMMENT 'trunk or branch name',
  `username` varchar(100) DEFAULT NULL,
  `revision` int(11) DEFAULT NULL,
  `repos_dt` varchar(100) DEFAULT NULL,
  `insert_dt` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Log table. All scripts should be logged here.' AUTO_INCREMENT=1 ;

