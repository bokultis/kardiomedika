INSERT INTO `module` (`id`, `application_id`, `code`, `name`, `description`, `settings`, `data`) VALUES
(20, 1, 'admin', 'Admin Module', 'Admin Management Module', '', '{ "widgets":{ "hDashboard.Widget.Admin":{ "jsFiles":["/modules/admin/js/Widget.js","/plugins/highchart/highcharts.js"], "cssFiles":[] } } }');

--
-- All updates goes below this comment
--

-- 2.0.13
ALTER TABLE `application` DROP `gsc_settings` ;