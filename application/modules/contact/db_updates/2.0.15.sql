ALTER TABLE `contact` ADD `fileupload` TEXT CHARACTER SET utf8 COLLATE utf8_estonian_ci NOT NULL AFTER `message` ;
ALTER TABLE `contact` ADD `form_id` VARCHAR( 50 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `fileupload` ;
