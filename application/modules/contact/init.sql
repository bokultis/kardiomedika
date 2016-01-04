--
-- Table structure for table `contact`
--

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
  `zip` int(10) NOT NULL,
  `city` varchar(100) NOT NULL,
  `street` varchar(120) NOT NULL,
  `description` text,
  `message` text,
  `language` char(5) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `posted` (`posted`),
  KEY `application_id` (`application_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='contact form submissions' AUTO_INCREMENT=1 ;

INSERT INTO `module` ( `application_id`, `code`, `name`, `description`, `settings`, `data`) VALUES
(1, 'contact', 'Contact Module', 'Contact Module', '{\r\n  "email": {\r\n    "from_email": "fbapp@horisen.biz",\r\n    "from_name": "HORISEN CMS",\r\n    "reply_email": "boris@horisen.com",\r\n    "subject_contact": "subjectContactTranslationkey",\r\n    "mailtext_respond_contact": "mailtextRespondContactTranslationkey",\r\n    "landing_page_text": "landingPageText",\r\n    "transport": "smtp",\r\n    "confirmation_email": "yes",\r\n    "default_country": "no",\r\n    "ip_country_detection": "yes",\r\n    "default_country_on_top": "no",\r\n    "selected_countries": [\r\n      "CH"\r\n    ],\r\n    "show_gender_form_field_required": "no",\r\n    "show_gender_form_field": "no",\r\n    "first_name_form_field_count": "50",\r\n    "show_first_name_form_field_count": "yes",\r\n    "show_first_name_form_field_required": "yes",\r\n    "show_first_name_form_field": "yes",\r\n    "last_name_form_field_count": "50",\r\n    "show_last_name_form_field_count": "yes",\r\n    "show_last_name_form_field_required": "yes",\r\n    "show_last_name_form_field": "yes",\r\n    "email_form_field_count": "255",\r\n    "show_email_form_field_count": "yes",\r\n    "show_email_form_field_required": "yes",\r\n    "show_email_form_field": "yes",\r\n    "street_form_field_count": "50",\r\n    "show_street_form_field_count": "yes",\r\n    "show_street_form_field_required": "yes",\r\n    "show_street_form_field": "yes",\r\n    "zip_form_field_count": "50",\r\n    "show_zip_form_field_count": "yes",\r\n    "show_zip_form_field_required": "yes",\r\n    "show_zip_form_field": "yes",\r\n    "city_form_field_count": "50",\r\n    "show_city_form_field_count": "yes",\r\n    "show_city_form_field_required": "yes",\r\n    "show_city_form_field": "yes",\r\n    "country_form_field_count": "50",\r\n    "show_country_form_field_count": "no",\r\n    "show_country_form_field_required": "yes",\r\n    "show_country_form_field": "yes",\r\n    "phone_form_field_count": "50",\r\n    "show_phone_form_field_count": "yes",\r\n    "show_phone_form_field_required": "no",\r\n    "show_phone_form_field": "yes",\r\n    "mobile_form_field_count": "50",\r\n    "show_mobile_form_field_count": "yes",\r\n    "show_mobile_form_field_required": "no",\r\n    "show_mobile_form_field": "no",\r\n    "fax_form_field_count": "50",\r\n    "show_fax_form_field_count": "yes",\r\n    "show_fax_form_field_required": "no",\r\n    "show_fax_form_field": "no",\r\n    "description_form_field_count": "100",\r\n    "show_description_form_field_count": "yes",\r\n    "show_description_form_field_required": "yes",\r\n    "show_description_form_field": "yes",\r\n    "interest_form_field_count": "",\r\n    "show_interest_form_field_count": "no",\r\n    "show_interest_form_field_required": "yes",\r\n    "show_interest_form_field": "yes",\r\n    "parameters": {\r\n      "server": "mail.horisen.com",\r\n      "auth": "login",\r\n      "username": "fbapp@horisen.biz",\r\n      "password": "Fbh0r1sen*9",\r\n      "port": "587"\r\n    },\r\n    "to_emails": [\r\n      {\r\n        "name": "Milan",\r\n        "email": "boris@horisen.com"\r\n      }\r\n    ]\r\n  },\r\n  "captcha": {\r\n    "fontName": "font4.ttf",\r\n    "wordLen": "3",\r\n    "timeout": "300",\r\n    "width": "150",\r\n    "height": "40",\r\n    "dotNoiseLevel": "20",\r\n    "lineNoiseLevel": "2"\r\n  }\r\n}', '{"menus":{"index\\/index":{"name":"Contact","dialog_url":null}}}');

--
-- All updates goes below this comment !!
--

-- 2.0.15

ALTER TABLE `contact` ADD `fileupload` TEXT CHARACTER SET utf8 COLLATE utf8_estonian_ci NOT NULL AFTER `message` ;
ALTER TABLE `contact` ADD `form_id` VARCHAR( 50 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `fileupload` ;

-- 2.0.16

ALTER TABLE `contact` CHANGE `zip` `zip` VARCHAR( 15 ) NOT NULL ;

-- 2.0.17

ALTER TABLE `contact` CHANGE `fileupload` `fileupload` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ;

-- 2.0.19

UPDATE `module` SET `data` = '{"menus":{"generic\\/index":{"name":"Contact","dialog_url":null}}}' WHERE `module`.`code` = 'contact';



