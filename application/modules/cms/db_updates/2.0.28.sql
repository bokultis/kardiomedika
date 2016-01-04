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

