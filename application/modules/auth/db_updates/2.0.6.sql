ALTER TABLE `auth_user` ADD `changed_password_dt` DATETIME NOT NULL AFTER `created` ;


CREATE TABLE IF NOT EXISTS `auth_user_history_password` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `password` varchar(32) NOT NULL,
  `last_changed_date_password` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

ALTER TABLE `auth_user` ADD `deleted` ENUM( 'yes', 'no' ) NOT NULL DEFAULT 'no' AFTER `password` ;
