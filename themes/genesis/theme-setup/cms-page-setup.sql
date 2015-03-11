TRUNCATE TABLE `cms_page`;
TRUNCATE TABLE `cms_page_tr`;
TRUNCATE TABLE `cms_menu_item`;
TRUNCATE TABLE `cms_menu_item_tr`;
TRUNCATE TABLE `cms_route`;

--
-- Dumping data for table `cms_page`
--

INSERT INTO `cms_page` (`id`, `code`, `url_id`, `application_id`, `type_id`, `user_id`, `posted`, `format`, `title`, `content`, `status`, `teaser`, `data`, `meta`, `content_type`) VALUES
(20, 'box-concept', 'box-concept', 1, 1, 1, '2014-09-04 08:47:55', 'path', 'Box concept', 'en/box-concept.phtml', 'published', '', '{"teaser_image":""}', '{"keywords":"","description":"Consulting and concept includes a great brand strategy with creative approach which HORISEN design experts deliver to meet your business\\u2019 final results."}', 'PUBLIC'),
(21, 'tile-concept', 'tile-concept', 1, 1, 1, '2014-09-04 08:48:55', 'path', 'Tile concept', 'en/tile-concept.phtml', 'published', '', '{"teaser_image":""}', '{"keywords":"","description":"HORISEN works across multiple design areas to assure the right corporate identity and raise your brand awareness."}', 'PUBLIC'),
(23, 'video', 'video', 1, 1, 1, '2014-09-04 08:49:19', 'path', 'Videos', 'en/video.phtml', 'published', '', '{"teaser_image":""}', '{"keywords":"","description":"HORISEN helps you make outstanding videos: from storytelling, scripting, design and final video production until successful market launch."}', 'PUBLIC'),
(24, 'offers', 'offers', 1, 1, 1, '2014-09-04 08:49:34', 'path', 'Design Portfolio', 'en/offers.phtml', 'published', '', '{"teaser_image":""}', '{"keywords":"","description":"Special offers and product \\/ service packages for any business or industry size, anytime in real time."}', 'PUBLIC'),
(72, 'scroll-spy', 'scroll-spy', 1, 1, 1, '2015-02-20 09:52:24', 'path', 'Scroll Spy', 'scroll-spy.phtml', 'published', '', '{"teaser_image":""}', '{"keywords":"","description":""}', 'PUBLIC'),
(32, 'about-us', 'about-us', 1, 1, 1, '2014-09-04 09:07:14', 'path', 'About us', 'en/about-us.phtml', 'published', '', '{"teaser_image":""}', '{"keywords":"voice, web, mobile, social media, channel","description":"We strive to provide a single platform combining voice, web, mobile and social media with offline media channels making a unified cross-media channel."}', 'PUBLIC'),
(42, 'impressum', 'impressum', 1, 21, 1, '2014-09-04 11:37:46', 'path', 'Impressum', 'en/impressum.phtml', 'published', '', '{"teaser_image":""}', '{"keywords":"","description":"Impressum: HORISEN AG Hauptstrasse 65 CH-9400 Rorschach Switzerland Owner and CEO Dipl. Ing. Fabrizio Salanitri "}', 'PUBLIC'),
(43, 'agb', 'agb', 1, 21, 1, '2014-09-04 11:37:56', 'path', 'AGB', 'en/agb.phtml', 'published', '', '{"teaser_image":""}', '{"keywords":"","description":"By accessing and using HORISEN Services, you accept and agree to terms of Services (TOS)."}', 'PUBLIC'),
(58, 'search', 'search', 1, 1, 1, '2014-10-23 16:08:03', 'path', 'Search', 'search.phtml', 'published', '', '{"teaser_image":""}', '{"keywords":"","description":""}', 'PUBLIC'),
(59, 'home', 'home', 1, 1, 1, '2014-09-04 11:38:48', 'path', 'Home', 'en/home.phtml', 'published', '', '{"teaser_image":""}', '{"keywords":"","description":"HORISEN is your home for cross-media marketing solutions including: web services, social media, mobile marketing, telecom, design and graphics and products."}', 'PUBLIC'),
(60, 'intro', 'intro', 1, 1, 1, '2014-09-04 11:38:48', 'path', 'Intro', 'intro.phtml', 'published', NULL, '{"teaser_image":""}', '{"keywords":"","description":""}', 'PUBLIC'),
(64, 'security-privacy', 'security-privacy', 1, 21, 1, '2014-09-04 11:37:56', 'path', 'Security and Privacy', 'en/security-privacy.phtml', 'published', '', '{"teaser_image":""}', '{"keywords":"","description":"HORISEN provides you the opportunities to engage with your customers, advertise your services or interact with your fan base in many ways."}', 'PUBLIC'),
(69, 'downloads', 'downloads', 1, 1, 1, '2014-09-03 15:20:32', 'path', 'Downloads', 'en/downloads.phtml', 'published', '', '{"teaser_image":""}', '{"keywords":"","description":"Section for easy downloads: whitepapers, logos, graphics and other useful HORISEN marketing technology documentation and examples."}', 'PUBLIC'),
(71, 'scroll-spy', 'scroll-spy', 1, 20, 1, '2014-11-19 00:00:00', 'path', 'Scroll Spy', 'scroll-spy.phtml', 'published', '', '{"teaser_image":""}', '{"keywords":"","description":""}', 'PUBLIC');

--
-- Dumping data for table `cms_page_tr`
--

INSERT INTO `cms_page_tr` (`id`, `language`, `translation_id`, `url_id`, `title`, `content`, `teaser`, `data`, `meta`) VALUES
(20, 'de', 20, 'box-concept', 'Box - Konzept', 'de/box-concept.phtml', '', '{"teaser_image":""}', '{"keywords":"","description":""}'),
(21, 'de', 21, 'tile-concept', 'Fliesenkonzept', 'de/tile-concept.phtml', '', '{"teaser_image":""}', '{"keywords":"","description":""}'),
(23, 'de', 23, 'video', 'Video', 'de/video.phtml', NULL, '{"teaser_image":""}', '{"keywords":"","description":""}'),
(24, 'de', 24, 'offers', 'Angebote', 'de/offers.phtml', NULL, '{"teaser_image":""}', '{"keywords":"","description":""}'),
(32, 'de', 32, 'about-us', 'Über uns (+Philosophie)', 'de/about-us.phtml', NULL, '{"teaser_image":""}', '{"keywords":"","description":""}'),
(106, 'en', 58, 'search', 'Search', 'en/search.phtml', '', '{"teaser_image":""}', '{"keywords":"","description":""}'),
(42, 'de', 42, 'impressum', 'Impressum', 'de/impressum.phtml', NULL, '{"teaser_image":""}', '{"keywords":"","description":""}'),
(43, 'de', 43, 'agb', 'AGB', 'de/agb.phtml', NULL, '{"teaser_image":""}', '{"keywords":"","description":""}'),
(55, 'de', 55, 'slider-image', 'Slider Image', '', '', '{"img":"slider_images\\/slideImages.jpg","show":"1","teaser":"","order":"2","target":""}', NULL),
(54, 'de', 54, 'slider-images', 'Slider Images', '', '', '{"img":"slider_images\\/slideImages.jpg","show":"1","teaser":"","order":"1","target":""}', NULL),
(56, 'en', 32, 'about-us', 'About us', 'en/about-us.phtml', '', '{"teaser_image":""}', '{"keywords":"voice, web, mobile, social media, channel","description":"We strive to provide a single platform combining voice, web, mobile and social media with offline media channels making a unified cross-media channel."}'),
(61, 'en', 20, 'box-concept', 'Box concept', 'en/box-concept.phtml', '', '{"teaser_image":""}', '{"keywords":"","description":"Consulting and concept includes a great brand strategy with creative approach which HORISEN design experts deliver to meet your business\\u2019 final results."}'),
(63, 'en', 24, 'offers', 'Design Portfolio', 'en/offers.phtml', '', '{"teaser_image":""}', '{"keywords":"","description":"Special offers and product \\/ service packages for any business or industry size, anytime in real time."}'),
(64, 'en', 21, 'tile-concept', 'Tile concept', 'en/tile-concept.phtml', '', '{"teaser_image":""}', '{"keywords":"","description":"HORISEN works across multiple design areas to assure the right corporate identity and raise your brand awareness."}'),
(65, 'en', 23, 'video', 'Videos', 'en/video.phtml', '', '{"teaser_image":""}', '{"keywords":"","description":"HORISEN helps you make outstanding videos: from storytelling, scripting, design and final video production until successful market launch."}'),
(119, 'en', 72, 'scroll-spy', 'Scroll Spy', 'scroll-spy.phtml', '', '{"teaser_image":""}', '{"keywords":"","description":""}'),
(120, 'de', 72, 'scroll-spy', 'Scroll Spy', 'scroll-spy.phtml', '', '{"teaser_image":""}', '{"keywords":"","description":""}'),
(121, 'en', 71, 'scroll-spy', 'Scroll Spy', 'scroll-spy.phtml', '', '{"teaser_image":""}', '{"keywords":"","description":""}'),
(93, 'en', 42, 'impressum', 'Impressum', 'en/impressum.phtml', '', '{"teaser_image":""}', '{"keywords":"","description":"Impressum: HORISEN AG Hauptstrasse 65 CH-9400 Rorschach Switzerland Owner and CEO Dipl. Ing. Fabrizio Salanitri "}'),
(94, 'en', 43, 'agb', 'AGB', 'en/agb.phtml', '', '{"teaser_image":""}', '{"keywords":"","description":"By accessing and using HORISEN Services, you accept and agree to terms of Services (TOS)."}'),
(101, 'de', 58, 'search', 'Search', 'de/search.phtml', '', '{"teaser_image":""}', '{"keywords":"","description":""}'),
(102, 'en', 59, 'home', 'Home', 'en/home.phtml', '', '{"teaser_image":""}', '{"keywords":"","description":"HORISEN is your home for cross-media marketing solutions including: web services, social media, mobile marketing, telecom, design and graphics and products."}'),
(103, 'de', 59, 'home', 'Home', 'de/home.phtml', NULL, '{"teaser_image":""}', '{"keywords":"","description":""}'),
(104, 'en', 60, 'intro', '', 'en/intro.phtml', NULL, NULL, NULL),
(105, 'de', 60, 'intro', '', 'de/intro.phtml', NULL, '{"teaser_image":""}', '{"keywords":"","description":""}'),
(115, 'en', 64, 'security-privacy', 'Security and Privacy', 'en/security-privacy.phtml', '', '{"teaser_image":""}', '{"keywords":"","description":"HORISEN provides you the opportunities to engage with your customers, advertise your services or interact with your fan base in many ways."}'),
(116, 'en', 69, 'downloads', 'Downloads', 'en/downloads.phtml', '', '{"teaser_image":""}', '{"keywords":"","description":"Section for easy downloads: whitepapers, logos, graphics and other useful HORISEN marketing technology documentation and examples."}'),
(117, 'de', 69, 'downloads', 'Downloads', 'de/downloads.phtml', NULL, '{"teaser_image":""}', '{"keywords":"","description":""}'),
(118, 'de', 64, 'datenschutz', 'Datenschutz', 'de/security-privacy.phtml', '', '{"teaser_image":""}', '{"keywords":"","description":"HORISEN provides you the opportunities to engage with your customers, advertise your services or interact with your fan base in many ways."}');

--
-- Dumping data for table `cms_menu_item`
--

INSERT INTO `cms_menu_item` (`id`, `application_id`, `menu`, `level`, `parent_id`, `page_id`, `name`, `route`, `path`, `params`, `uri`, `ord_num`, `hidden`, `meta`, `target`) VALUES
(5, 1, 'main', 0, 0, NULL, 'Pages', '', NULL, NULL, '', 3, 'no', NULL, NULL),
(129, 1, 'main', 0, 0, 59, 'Home', 'cms', 'cms/page/index', NULL, '', 1, 'no', NULL, NULL),
(130, 1, 'main', 0, 0, 60, 'Intro', 'cms', 'cms/page/index', NULL, '', 2, 'no', NULL, NULL),
(58, 1, 'main', 1, 5, 20, 'Box concept', 'cms', 'cms/page/index', NULL, '', 1, 'no', NULL, NULL),
(26, 1, 'main', 1, 5, 21, 'Tile concept', 'cms', 'cms/page/index', NULL, '', 2, 'no', NULL, NULL),
(27, 1, 'main', 1, 5, 23, 'Video', 'cms', 'cms/page/index', NULL, '', 4, 'no', NULL, NULL),
(28, 1, 'main', 1, 5, 24, 'Portfolio', 'cms', 'cms/page/index', NULL, '', 3, 'no', NULL, NULL),
(36, 1, 'main', 0, 0, NULL, 'Contact', 'cms', 'contact/generic/index', 'form_id/contact', '', 5, 'no', NULL, NULL),
(38, 1, 'main', 0, 0, 32, 'About us', 'cms', 'cms/page/index', NULL, '', 4, 'no', NULL, NULL),
(133, 1, 'footer', 1, 56, 60, 'Intro', 'cms', 'cms/page/index', NULL, '', 1, 'no', NULL, NULL),
(134, 1, 'footer', 1, 94, 24, 'Portfolio', 'cms', 'cms/page/index', NULL, '', 3, 'no', NULL, NULL),
(131, 1, 'main', 1, 5, 69, 'Download', 'cms', 'cms/page/index', NULL, '', 5, 'no', NULL, NULL),
(132, 1, 'main', 0, 0, 71, 'Scroll Spy', 'cms', 'cms/page/index', NULL, '', 3, 'no', NULL, NULL),
(56, 1, 'footer', 0, 0, NULL, 'Features', '', NULL, NULL, '', 1, 'no', NULL, NULL),
(66, 1, 'footer', 1, 94, 21, 'Tile concept', 'cms', 'cms/page/index', NULL, '', 2, 'no', NULL, NULL),
(68, 1, 'footer', 0, 0, NULL, 'We for you', '', NULL, NULL, '', 7, 'no', NULL, NULL),
(93, 1, 'footer', 1, 94, 20, 'Box concept', 'cms', 'cms/page/index', NULL, '', 1, 'no', NULL, NULL),
(94, 1, 'footer', 0, 0, NULL, 'Pages', '', NULL, NULL, '', 1, 'no', NULL, NULL),
(95, 1, 'footer', 1, 66, 23, 'Video', 'cms', 'cms/page/index', NULL, '', 4, 'no', NULL, NULL),
(96, 1, 'footer', 1, 66, 24, 'Offers', 'cms', 'cms/page/index', NULL, '', 3, 'no', NULL, NULL),
(105, 1, 'footer', 1, 68, 32, 'About us', 'cms', 'cms/page/index', NULL, '', 3, 'no', NULL, NULL),
(135, 1, 'footer', 1, 94, 23, 'Videos', 'cms', 'cms/page/index', NULL, '', 4, 'no', NULL, NULL),
(136, 1, 'footer', 1, 94, 69, 'Download', 'cms', 'cms/page/index', NULL, '', 5, 'no', NULL, NULL),
(112, 1, 'footer', 1, 68, NULL, 'Contact', 'cms', 'contact/generic/index', 'form_id/contact', '', 1, 'no', NULL, NULL),
(126, 1, 'footer', 1, 56, 71, 'Scroll Spy', 'cms', 'cms/page/index', NULL, '', 1, 'no', NULL, NULL);

--
-- Dumping data for table `cms_menu_item_tr`
--

INSERT INTO `cms_menu_item_tr` (`id`, `language`, `translation_id`, `name`, `uri`, `meta`) VALUES
(31, 'de', 5, 'Pages', NULL, NULL),
(223, 'de', 129, 'Home', 'home', NULL),
(224, 'en', 130, 'Intro', NULL, NULL),
(97, 'en', 28, 'Portfolio', NULL, NULL),
(52, 'de', 26, 'Fliesenkonzept', NULL, NULL),
(53, 'de', 27, 'Videos', NULL, NULL),
(54, 'de', 28, 'Portfolio', NULL, NULL),
(61, 'de', 35, 'Intranet Apps', NULL, NULL),
(62, 'de', 36, 'Kontakt', NULL, NULL),
(64, 'de', 38, 'Über uns', NULL, NULL),
(228, 'en', 133, 'Intro', NULL, NULL),
(229, 'en', 134, 'Portfolio', NULL, NULL),
(226, 'en', 132, 'Scroll Spy', NULL, NULL),
(232, 'de', 132, 'Scroll Spy', NULL, NULL),
(82, 'de', 56, 'Eigenschaften', NULL, NULL),
(222, 'en', 129, 'Home', 'home', NULL),
(227, 'en', 126, 'Scroll Spy', NULL, NULL),
(88, 'en', 36, 'Contact', NULL, NULL),
(89, 'en', 38, 'About us', NULL, NULL),
(96, 'de', 58, 'Box Konzept', NULL, NULL),
(98, 'en', 26, 'Tile concept', NULL, NULL),
(99, 'en', 27, 'Videos', NULL, NULL),
(105, 'en', 35, 'Intranet Apps', NULL, NULL),
(225, 'en', 131, 'Download', NULL, NULL),
(132, 'en', 56, 'Features', NULL, NULL),
(133, 'en', 58, 'Box concept', NULL, NULL),
(157, 'en', 93, 'Box concept', NULL, NULL),
(158, 'en', 94, 'Pages', NULL, NULL),
(159, 'en', 95, 'Videos', NULL, NULL),
(160, 'en', 96, 'Design Portfolio', NULL, NULL),
(219, 'de', 66, 'Fliesenkonzept', NULL, NULL),
(167, 'en', 103, 'Intranet Apps', NULL, NULL),
(169, 'en', 105, 'About us', NULL, NULL),
(174, 'en', 68, 'We for you', NULL, NULL),
(176, 'de', 68, 'Wir für Dich', NULL, NULL),
(231, 'en', 136, 'Download', NULL, NULL),
(230, 'en', 135, 'Videos', NULL, NULL),
(181, 'de', 105, 'Über uns', NULL, NULL),
(192, 'en', 5, 'Pages', NULL, NULL),
(196, 'de', 93, 'Box Konzept', NULL, NULL),
(202, 'de', 96, 'Portfolio', NULL, NULL),
(203, 'de', 95, 'Videos', NULL, NULL),
(218, 'en', 66, 'Tile concept', NULL, NULL),
(217, 'de', 112, 'Kontakt', NULL, NULL);

--
-- Dumping data for table `cms_route`
--

INSERT INTO `cms_route` (`id`, `application_id`, `page_id`, `uri`, `name`, `path`, `params`, `lang`) VALUES
(1, 1, 59, 'home', 'Home', 'cms/page/index', NULL, 'de'),
(4, 1, 59, 'home', 'Home', 'cms/page/index', NULL, 'en'),
(178, 1, 60, '/intro', 'Intro', 'cms/page/index', NULL, 'en'),
(8, 1, 1, 'design-graphics', 'Design/Graphics', 'cms/page/index', NULL, 'de'),
(182, 1, 60, '/intro', 'Intro', 'cms/page/index', NULL, 'en'),
(66, 1, 24, 'design/design-portfolio', 'Offers', 'cms/page/index', NULL, 'en'),
(29, 1, 21, 'design/designs', 'Designs', 'cms/page/index', NULL, 'de'),
(30, 1, 23, 'design/video', 'Video', 'cms/page/index', NULL, 'de'),
(31, 1, 24, 'design/angebote', 'Angebote', 'cms/page/index', NULL, 'de'),
(41, 1, 32, 'wir-fuer-sie/ueber-uns', 'Über uns', 'cms/page/index', NULL, 'de'),
(56, 1, NULL, 'hilfe/sitemap', 'Sitemap', 'cms/sitemap/index', NULL, 'de'),
(57, 1, NULL, 'contact', 'Contact', 'contact/generic/index', 'form_id/contact', 'en'),
(58, 1, 32, 'we-for-you/about-us', 'About us', 'cms/page/index', NULL, 'en'),
(67, 1, 21, 'design/designs', 'Designs', 'cms/page/index', NULL, 'en'),
(68, 1, 23, 'design/videos', 'Video', 'cms/page/index', NULL, 'en'),
(179, 1, 69, '/downloads', 'Download', 'cms/page/index', NULL, 'en'),
(103, 1, NULL, 'help/sitemap', 'Sitemap', 'cms/sitemap/index', NULL, 'en'),
(113, 1, 58, 'search', 'Search', 'cms/page/index', NULL, 'de'),
(115, 1, 60, '', 'Intro', 'cms/page/index', NULL, NULL),
(140, 1, 21, 'pages/tile-concept', 'Designs', 'cms/page/index', NULL, 'en'),
(141, 1, 23, 'design/videos', 'Video', 'cms/page/index', NULL, 'en'),
(142, 1, 24, 'design/portfolio', 'Offers', 'cms/page/index', NULL, 'en'),
(151, 1, 32, 'we-for-you/about-us', 'About us', 'cms/page/index', NULL, 'en'),
(175, 1, 20, 'box-concept', 'Box concept', 'cms/page/index', NULL, 'en'),
(176, 1, 20, '', 'Box Konzept', 'cms/page/index', NULL, 'de'),
(165, 1, 58, 'search', 'Search', 'cms/page/index', NULL, 'en'),
(166, 1, 69, 'help/downloads', 'Downloads', 'cms/page/index', NULL, 'en'),
(167, 1, 69, 'hilfe/downloads', 'Downloads', 'cms/page/index', NULL, 'de'),
(183, 1, 24, 'design/portfolio', 'Portfolio', 'cms/page/index', NULL, 'en'),
(184, 1, 23, 'design/videos', 'Videos', 'cms/page/index', NULL, 'en'),
(185, 1, 69, 'help/downloads', 'Download', 'cms/page/index', NULL, 'en'),
(186, 1, 21, 'tile-concept', 'Tile concept', 'cms/page/index', NULL, 'en');
