SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL';


-- -----------------------------------------------------
-- Table `application`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `application` ;

CREATE  TABLE IF NOT EXISTS `application` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(128) NOT NULL ,
  `status` ENUM('A','D') NULL DEFAULT NULL ,
  `status_dt` DATETIME NULL DEFAULT NULL ,
  `style_json` TEXT NULL DEFAULT NULL ,
  `fb_settings` TEXT NOT NULL COMMENT 'json fb settings' ,
  `email_settings` TEXT NOT NULL COMMENT 'json email settings' ,
  `settings` TEXT NOT NULL COMMENT 'global application settings' ,
  PRIMARY KEY (`id`) )
ENGINE = MyISAM
AUTO_INCREMENT = 2
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci
COMMENT = 'Open different facebook sms applications.';


-- -----------------------------------------------------
-- Table `auth_resource`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `auth_resource` ;

CREATE  TABLE IF NOT EXISTS `auth_resource` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `code` VARCHAR(20) NOT NULL ,
  `name` VARCHAR(100) NOT NULL ,
  `parent_id` INT(10) UNSIGNED NOT NULL ,
  `module` VARCHAR(20) NOT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = MyISAM
AUTO_INCREMENT = 2
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci
COMMENT = 'resources';

CREATE INDEX `parent_id` ON `auth_resource` (`parent_id` ASC) ;


-- -----------------------------------------------------
-- Table `auth_privilege`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `auth_privilege` ;

CREATE  TABLE IF NOT EXISTS `auth_privilege` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `code` VARCHAR(20) NOT NULL ,
  `name` VARCHAR(100) NOT NULL ,
  `resource_id` INT(10) UNSIGNED NOT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = MyISAM
AUTO_INCREMENT = 2
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;

CREATE INDEX `resource_id` ON `auth_privilege` (`resource_id` ASC) ;


-- -----------------------------------------------------
-- Table `auth_role`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `auth_role` ;

CREATE  TABLE IF NOT EXISTS `auth_role` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(50) NOT NULL ,
  `parent_id` INT(10) UNSIGNED NOT NULL COMMENT 'parent role' ,
  PRIMARY KEY (`id`) )
ENGINE = MyISAM
AUTO_INCREMENT = 4
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;

CREATE INDEX `parent_id` ON `auth_role` (`parent_id` ASC) ;


-- -----------------------------------------------------
-- Table `auth_acl`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `auth_acl` ;

CREATE  TABLE IF NOT EXISTS `auth_acl` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `role_id` INT(10) UNSIGNED NOT NULL ,
  `privilege_id` INT(10) UNSIGNED NOT NULL ,
  `allowed` ENUM('yes','no') NOT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = MyISAM
AUTO_INCREMENT = 2
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci
COMMENT = 'role can or cannot do privilege on resource';

CREATE UNIQUE INDEX `role_id` ON `auth_acl` (`role_id` ASC, `privilege_id` ASC) ;


-- -----------------------------------------------------
-- Table `auth_user`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `auth_user` ;

CREATE  TABLE IF NOT EXISTS `auth_user` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `role_id` INT(10) UNSIGNED NOT NULL COMMENT 'role' ,
  `username` VARCHAR(20) NOT NULL ,
  `password` VARCHAR(32) NOT NULL COMMENT 'md5' ,
  `first_name` VARCHAR(50) NOT NULL ,
  `last_name` VARCHAR(50) NOT NULL ,
  `email` VARCHAR(255) NOT NULL ,
  `status` ENUM('active','blocked','pending') NOT NULL DEFAULT 'pending' ,
  `lang` CHAR(5) NULL DEFAULT NULL ,
  `image_path` VARCHAR(255) NULL DEFAULT NULL ,
  `data` TEXT NULL DEFAULT NULL COMMENT 'custom json data' ,
  `created` DATETIME NOT NULL ,
  `logged` DATETIME NOT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = MyISAM
AUTO_INCREMENT = 2
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci
COMMENT = 'users and admins';

CREATE UNIQUE INDEX `username` ON `auth_user` (`username` ASC) ;

CREATE UNIQUE INDEX `email` ON `auth_user` (`email` ASC) ;

CREATE INDEX `role_id` ON `auth_user` (`role_id` ASC) ;


-- -----------------------------------------------------
-- Table `cms_page_type`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `cms_page_type` ;

CREATE  TABLE IF NOT EXISTS `cms_page_type` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(100) NOT NULL ,
  `description` TEXT NOT NULL ,
  `module` VARCHAR(20) NULL DEFAULT NULL ,
  `data` TEXT NULL DEFAULT NULL COMMENT 'json custom data' ,
  PRIMARY KEY (`id`) )
ENGINE = MyISAM
AUTO_INCREMENT = 4
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci
COMMENT = 'page types';


-- -----------------------------------------------------
-- Table `cms_category_set`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `cms_category_set` ;

CREATE  TABLE IF NOT EXISTS `cms_category_set` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(100) NOT NULL ,
  `description` TEXT NOT NULL ,
  `module` VARCHAR(20) NOT NULL ,
  `page_type_id` INT(10) UNSIGNED NOT NULL COMMENT 'page type id' ,
  PRIMARY KEY (`id`) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci
COMMENT = 'set of categories';


-- -----------------------------------------------------
-- Table `cms_category`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `cms_category` ;

CREATE  TABLE IF NOT EXISTS `cms_category` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `set_id` INT(10) UNSIGNED NOT NULL COMMENT 'set id' ,
  `name` VARCHAR(100) NOT NULL ,
  `description` TEXT NOT NULL ,
  `parent_id` INT(10) UNSIGNED NOT NULL ,
  `level` INT(10) UNSIGNED NOT NULL ,
  `data` TEXT NULL DEFAULT NULL COMMENT 'custom json data' ,
  PRIMARY KEY (`id`) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci
COMMENT = 'pages categories';

CREATE INDEX `set_id` ON `cms_category` (`set_id` ASC) ;


-- -----------------------------------------------------
-- Table `cms_page`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `cms_page` ;

CREATE  TABLE IF NOT EXISTS `cms_page` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `code` VARCHAR(20) NOT NULL COMMENT 'url segment' ,
  `url_id` VARCHAR(30) NULL DEFAULT NULL COMMENT 'parmalink id part' ,
  `application_id` INT(10) UNSIGNED NOT NULL ,
  `type_id` INT(10) UNSIGNED NULL DEFAULT NULL COMMENT 'page type' ,
  `user_id` INT(10) UNSIGNED NOT NULL COMMENT 'owner id' ,
  `posted` DATETIME NOT NULL ,
  `format` ENUM('html','php','path') NOT NULL DEFAULT 'html' COMMENT 'format of the content' ,
  `title` VARCHAR(50) NOT NULL ,
  `content` TEXT NOT NULL ,
  `status` ENUM('draft','published') NOT NULL DEFAULT 'draft' ,
  `teaser` TINYTEXT NULL DEFAULT NULL ,
  `data` TEXT NULL DEFAULT NULL COMMENT 'custom json data' ,
  `meta` TEXT NULL DEFAULT NULL COMMENT 'meta json data' ,
  `content_type` ENUM('PUBLIC', 'PRIVATE') NOT NULL DEFAULT 'PUBLIC' COMMENT 'Type of the content: PUBLIC - all could see it; PRIVATE - only allowed user could see it.' ,
  PRIMARY KEY (`id`) )
ENGINE = MyISAM
AUTO_INCREMENT = 6
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci
COMMENT = 'CMS Pages';

CREATE INDEX `type_id` ON `cms_page` (`type_id` ASC) ;

CREATE INDEX `status` ON `cms_page` (`status` ASC) ;

CREATE INDEX `user_id` ON `cms_page` (`user_id` ASC) ;

CREATE INDEX `url_id` ON `cms_page` (`url_id` ASC) ;

CREATE INDEX `application_id` ON `cms_page` (`application_id` ASC, `code` ASC) ;


-- -----------------------------------------------------
-- Table `cms_menu_item`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `cms_menu_item` ;

CREATE  TABLE IF NOT EXISTS `cms_menu_item` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `application_id` INT(10) UNSIGNED NOT NULL ,
  `menu` CHAR(10) NOT NULL DEFAULT 'main' ,
  `level` INT(10) UNSIGNED NOT NULL DEFAULT '0' ,
  `parent_id` INT(10) UNSIGNED NOT NULL DEFAULT '0' ,
  `page_id` INT(10) UNSIGNED NULL DEFAULT NULL COMMENT 'optional cms page id - menu routes to' ,
  `name` VARCHAR(30) NULL DEFAULT NULL ,
  `route` VARCHAR(20) NOT NULL DEFAULT 'default' COMMENT 'route name' ,
  `path` VARCHAR(255) NULL DEFAULT NULL COMMENT 'module/controller/action' ,
  `params` VARCHAR(255) NULL DEFAULT NULL COMMENT 'param_name/param_value ...' ,
  `uri` VARCHAR(255) NULL DEFAULT NULL COMMENT 'if no route specify uri' ,
  `ord_num` SMALLINT(5) UNSIGNED NOT NULL DEFAULT '1' ,
  `hidden` ENUM('yes','no') NOT NULL DEFAULT 'no' ,
  `meta` TEXT NULL DEFAULT NULL COMMENT 'meta json data' ,
  `target` VARCHAR(10) NULL DEFAULT NULL COMMENT 'link target' ,
  PRIMARY KEY (`id`) )
ENGINE = MyISAM
AUTO_INCREMENT = 14
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;

CREATE INDEX `menu` ON `cms_menu_item` (`application_id` ASC, `menu` ASC, `level` ASC, `parent_id` ASC, `ord_num` ASC) ;


-- -----------------------------------------------------
-- Table `cms_menu_item_tr`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `cms_menu_item_tr` ;

CREATE  TABLE IF NOT EXISTS `cms_menu_item_tr` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `language` CHAR(5) NOT NULL ,
  `translation_id` INT(10) UNSIGNED NOT NULL ,
  `name` VARCHAR(50) NULL DEFAULT NULL ,
  `meta` TEXT NULL DEFAULT NULL COMMENT 'meta json data' ,
  PRIMARY KEY (`id`) )
ENGINE = MyISAM
AUTO_INCREMENT = 27
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci
COMMENT = 'menu translations';

CREATE UNIQUE INDEX `language` ON `cms_menu_item_tr` (`language` ASC, `translation_id` ASC) ;


-- -----------------------------------------------------
-- Table `cms_page_tr`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `cms_page_tr` ;

CREATE  TABLE IF NOT EXISTS `cms_page_tr` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `language` CHAR(5) NOT NULL ,
  `translation_id` INT(10) UNSIGNED NOT NULL ,
  `url_id` VARCHAR(30) NULL DEFAULT NULL COMMENT 'parmalink id part' ,
  `title` VARCHAR(50) NULL DEFAULT NULL ,
  `content` TEXT NULL DEFAULT NULL ,
  `teaser` TINYTEXT NULL DEFAULT NULL ,
  `data` TEXT NULL DEFAULT NULL COMMENT 'custom json data' ,
  `meta` TEXT NULL DEFAULT NULL COMMENT 'meta json data' ,
  PRIMARY KEY (`id`) )
ENGINE = MyISAM
AUTO_INCREMENT = 11
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci
COMMENT = 'pages translations';

CREATE UNIQUE INDEX `language` ON `cms_page_tr` (`language` ASC, `translation_id` ASC) ;

CREATE INDEX `url_id` ON `cms_page_tr` (`url_id` ASC) ;


-- -----------------------------------------------------
-- Table `cms_route`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `cms_route` ;

CREATE  TABLE IF NOT EXISTS `cms_route` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `application_id` INT(10) UNSIGNED NOT NULL ,
  `page_id` INT(10) UNSIGNED NULL DEFAULT NULL COMMENT 'optional cms page id - record routes to' ,
  `uri` VARCHAR(255) NOT NULL COMMENT 'uri to match' ,
  `name` VARCHAR(50) NULL DEFAULT NULL ,
  `path` VARCHAR(255) NULL DEFAULT NULL COMMENT 'module/controller/action' ,
  `params` VARCHAR(255) NULL DEFAULT NULL COMMENT 'param_name/param_value ...' ,
  `lang` CHAR(5) NULL DEFAULT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = MyISAM
AUTO_INCREMENT = 32
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;

CREATE INDEX `menu` ON `cms_route` (`application_id` ASC) ;


-- -----------------------------------------------------
-- Table `contact`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `contact` ;

CREATE  TABLE IF NOT EXISTS `contact` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `application_id` INT(10) UNSIGNED NOT NULL ,
  `posted` DATETIME NOT NULL ,
  `contact_type` VARCHAR(20) NOT NULL ,
  `first_name` VARCHAR(60) NOT NULL ,
  `last_name` VARCHAR(60) NOT NULL ,
  `email` VARCHAR(255) NOT NULL ,
  `phone` VARCHAR(60) NULL DEFAULT NULL ,
  `company` VARCHAR(60) NULL DEFAULT NULL ,
  `subject` VARCHAR(60) NULL DEFAULT NULL ,
  `description` TEXT NULL DEFAULT NULL ,
  `language` CHAR(5) NOT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci
COMMENT = 'contact form submissions';

CREATE INDEX `posted` ON `contact` (`posted` ASC) ;

CREATE INDEX `contact_type` ON `contact` (`contact_type` ASC) ;

CREATE INDEX `application_id` ON `contact` (`application_id` ASC) ;


-- -----------------------------------------------------
-- Table `country`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `country` ;

CREATE  TABLE IF NOT EXISTS `country` (
  `id` SMALLINT(5) UNSIGNED NOT NULL DEFAULT '0' ,
  `name` VARCHAR(50) NOT NULL DEFAULT '' ,
  `name_de` VARCHAR(50) NULL DEFAULT NULL ,
  `code2` CHAR(2) NULL DEFAULT NULL ,
  `code3` CHAR(3) NULL DEFAULT NULL ,
  `domain` VARCHAR(5) NULL DEFAULT NULL ,
  `dial_code` VARCHAR(15) NULL DEFAULT NULL ,
  `currency` SMALLINT(5) UNSIGNED NULL DEFAULT NULL ,
  `mcc` VARCHAR(4) NULL DEFAULT NULL ,
  `def_lang` CHAR(2) NOT NULL DEFAULT 'EN' ,
  `continent` CHAR(2) NULL DEFAULT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;

CREATE INDEX `idx_country1` ON `country` (`code2` ASC) ;

CREATE INDEX `idx_country2` ON `country` (`code3` ASC) ;

CREATE INDEX `continent` ON `country` (`continent` ASC) ;


-- -----------------------------------------------------
-- Table `country_geoip`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `country_geoip` ;

CREATE  TABLE IF NOT EXISTS `country_geoip` (
  `min_ip` CHAR(15) NOT NULL ,
  `max_ip` CHAR(15) NOT NULL ,
  `min_long` INT(10) UNSIGNED NOT NULL ,
  `max_long` INT(10) UNSIGNED NOT NULL ,
  `country_code2` CHAR(2) NOT NULL ,
  `country_name` VARCHAR(20) NOT NULL )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci
COMMENT = 'country geo ip ranges';

CREATE INDEX `range` ON `country_geoip` (`min_long` ASC, `max_long` ASC) ;


-- -----------------------------------------------------
-- Table `country_translate`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `country_translate` ;

CREATE  TABLE IF NOT EXISTS `country_translate` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `code2` CHAR(2) NOT NULL ,
  `language` VARCHAR(5) NOT NULL ,
  `value` VARCHAR(255) NOT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = MyISAM
AUTO_INCREMENT = 712
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci
COMMENT = 'country translations';

CREATE UNIQUE INDEX `code2` ON `country_translate` (`code2` ASC, `language` ASC) ;


-- -----------------------------------------------------
-- Table `translate_language`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `translate_language` ;

CREATE  TABLE IF NOT EXISTS `translate_language` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `code` VARCHAR(5) NOT NULL ,
  `name` VARCHAR(20) NOT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = MyISAM
AUTO_INCREMENT = 4
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;

CREATE UNIQUE INDEX `code` ON `translate_language` (`code` ASC) ;


-- -----------------------------------------------------
-- Table `translate`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `translate` ;

CREATE  TABLE IF NOT EXISTS `translate` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `language_id` INT(10) UNSIGNED NOT NULL ,
  `section` VARCHAR(20) NULL DEFAULT NULL ,
  `key` TEXT NOT NULL ,
  `value` TEXT NOT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = MyISAM
AUTO_INCREMENT = 269
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;

CREATE UNIQUE INDEX `unique_trans` ON `translate` (`language_id` ASC, `section` ASC, `key`(100) ASC) ;

CREATE INDEX `language_id` ON `translate` (`language_id` ASC, `section` ASC) ;


-- -----------------------------------------------------
-- Table `translate_menu`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `translate_menu` ;

CREATE  TABLE IF NOT EXISTS `translate_menu` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(20) NOT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci
COMMENT = 'defines key cource pages';


-- -----------------------------------------------------
-- Table `translate_type`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `translate_type` ;

CREATE  TABLE IF NOT EXISTS `translate_type` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(20) NOT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci
COMMENT = 'defines key types';


-- -----------------------------------------------------
-- Table `translate_key`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `translate_key` ;

CREATE  TABLE IF NOT EXISTS `translate_key` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `key` TEXT NOT NULL ,
  `menu_id` INT(11) UNSIGNED NULL DEFAULT NULL ,
  `type_id` INT(11) UNSIGNED NULL DEFAULT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci
COMMENT = 'translation keys categorisation';

CREATE UNIQUE INDEX `key_idx` ON `translate_key` (`key`(100) ASC) ;

CREATE INDEX `menu_id` ON `translate_key` (`menu_id` ASC, `type_id` ASC) ;


-- -----------------------------------------------------
-- Table `upgrade_db_log`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `upgrade_db_log` ;

CREATE  TABLE IF NOT EXISTS `upgrade_db_log` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `file` VARCHAR(255) NULL DEFAULT NULL ,
  `branch` VARCHAR(10) NOT NULL DEFAULT 'trunk' COMMENT 'trunk or branch name' ,
  `username` VARCHAR(100) NULL DEFAULT NULL ,
  `revision` INT(11) NULL DEFAULT NULL ,
  `repos_dt` VARCHAR(100) NULL DEFAULT NULL ,
  `insert_dt` DATETIME NULL DEFAULT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = MyISAM
AUTO_INCREMENT = 29
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci
COMMENT = 'Log table. All scripts should be logged here.';


-- -----------------------------------------------------
-- Table `cms_category_page`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `cms_category_page` ;

CREATE  TABLE IF NOT EXISTS `cms_category_page` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `category_id` INT(10) UNSIGNED NOT NULL ,
  `page_id` INT(10) UNSIGNED NOT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci
COMMENT = 'page belongs to categories';

CREATE UNIQUE INDEX `category_id` ON `cms_category_page` (`category_id` ASC, `page_id` ASC) ;


-- -----------------------------------------------------
-- Table `module`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `module` ;

CREATE  TABLE IF NOT EXISTS `module` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `application_id` INT(10) UNSIGNED NOT NULL ,
  `code` VARCHAR(20) NOT NULL COMMENT 'module directory' ,
  `name` VARCHAR(50) NOT NULL ,
  `description` VARCHAR(255) NOT NULL ,
  `settings` TEXT NOT NULL COMMENT 'json encoded settings' ,
  `data` TEXT NULL DEFAULT NULL COMMENT 'json data' ,
  PRIMARY KEY (`id`) )
ENGINE = MyISAM
AUTO_INCREMENT = 81
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci
COMMENT = 'cms modules';

CREATE INDEX `application_id` ON `module` (`application_id` ASC) ;


-- -----------------------------------------------------
-- Table `cms_menu`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `cms_menu` ;

CREATE  TABLE IF NOT EXISTS `cms_menu` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `code` VARCHAR(30) NOT NULL COMMENT 'menu code' ,
  `name` VARCHAR(30) NOT NULL COMMENT 'description name' ,
  PRIMARY KEY (`id`) )
ENGINE = MyISAM
AUTO_INCREMENT = 3
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci
COMMENT = 'cms menu';

CREATE UNIQUE INDEX `code` ON `cms_menu` (`code` ASC, `name` ASC) ;


-- -----------------------------------------------------
-- Table `cms_category_tr`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `cms_category_tr` ;

CREATE  TABLE IF NOT EXISTS `cms_category_tr` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `language` CHAR(5) NOT NULL ,
  `translation_id` INT(10) UNSIGNED NOT NULL ,
  `name` VARCHAR(100) NULL DEFAULT NULL ,
  `description` TEXT NULL DEFAULT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci
COMMENT = 'category translations';

CREATE UNIQUE INDEX `language` ON `cms_category_tr` (`language` ASC, `translation_id` ASC) ;


-- -----------------------------------------------------
-- Table `cms_category_page_type`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `cms_category_page_type` ;

CREATE  TABLE IF NOT EXISTS `cms_category_page_type` (
  `id` INT(10) NOT NULL AUTO_INCREMENT ,
  `set_id` INT(10) NOT NULL COMMENT 'category set id' ,
  `type_id` INT(10) NOT NULL COMMENT 'page type id' ,
  PRIMARY KEY (`id`) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci
COMMENT = 'not in use now - page types have categories';

CREATE UNIQUE INDEX `set_category_type` ON `cms_category_page_type` (`set_id` ASC, `type_id` ASC) ;


-- -----------------------------------------------------
-- Table `teaser_box`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `teaser_box` ;

CREATE  TABLE IF NOT EXISTS `teaser_box` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `code` VARCHAR(20) NOT NULL COMMENT 'unique name' ,
  `name` VARCHAR(100) NOT NULL ,
  `width` INT(4) UNSIGNED NOT NULL ,
  `height` INT(4) UNSIGNED NOT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci
COMMENT = 'defined boxes - regions';

CREATE UNIQUE INDEX `code` ON `teaser_box` (`code` ASC) ;


-- -----------------------------------------------------
-- Table `teaser_item`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `teaser_item` ;

CREATE  TABLE IF NOT EXISTS `teaser_item` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `box_id` INT(10) UNSIGNED NOT NULL ,
  `fallback` ENUM('yes','no') NOT NULL DEFAULT 'no' COMMENT 'if should show up if no other defined' ,
  `start_dt` DATETIME NOT NULL ,
  `end_dt` DATETIME NOT NULL ,
  `title` VARCHAR(100) NULL DEFAULT NULL ,
  `content_type` VARCHAR(20) NOT NULL COMMENT 'class to render content' ,
  `content` TEXT NULL DEFAULT NULL COMMENT 'json content' ,
  PRIMARY KEY (`id`) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci
COMMENT = 'scheduled boxes items';

CREATE INDEX `box_id` ON `teaser_item` (`box_id` ASC, `fallback` ASC, `start_dt` ASC, `end_dt` ASC) ;


-- -----------------------------------------------------
-- Table `auth_user_copy1`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `auth_user_copy1` ;

CREATE  TABLE IF NOT EXISTS `auth_user_copy1` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
  PRIMARY KEY (`id`) )
ENGINE = MyISAM
AUTO_INCREMENT = 2
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci
COMMENT = 'users and admins';


-- -----------------------------------------------------
-- Table `cms_content_user`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `cms_content_user` ;

CREATE  TABLE IF NOT EXISTS `cms_content_user` (
  `id` INT NOT NULL ,
  `page_id` INT(10) UNSIGNED NOT NULL COMMENT 'Content allowed to certain user.' ,
  `user_id` INT(10) UNSIGNED NOT NULL COMMENT 'A user who is allowed to see certain content.' ,
  PRIMARY KEY (`id`) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;

CREATE INDEX `cms_content_user_idx1` ON `cms_content_user` (`page_id` ASC, `user_id` ASC) ;

CREATE INDEX `cms_content_user_idx2` ON `cms_content_user` (`user_id` ASC, `page_id` ASC) ;



SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
