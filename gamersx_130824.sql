-- -------------------------------------------------------------
-- TablePlus 6.1.2(568)
--
-- https://tableplus.com/
--
-- Database: gamersx
-- Generation Time: 2024-08-13 07:25:56.9860
-- -------------------------------------------------------------


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


DROP TABLE IF EXISTS `error_log`;
CREATE TABLE `error_log` (
  `id` int NOT NULL AUTO_INCREMENT,
  `error_message` longtext COLLATE utf8mb4_unicode_ci,
  `error_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status_code` int NOT NULL,
  `container_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `fos_user__group`;
CREATE TABLE `fos_user__group` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `roles` json NOT NULL COMMENT '(DC2Type:json)',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `fos_user__user`;
CREATE TABLE `fos_user__user` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(180) COLLATE utf8mb4_unicode_ci NOT NULL,
  `username_canonical` varchar(180) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(180) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_canonical` varchar(180) COLLATE utf8mb4_unicode_ci NOT NULL,
  `enabled` tinyint(1) NOT NULL,
  `salt` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_login` datetime DEFAULT NULL,
  `confirmation_token` varchar(180) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `password_requested_at` datetime DEFAULT NULL,
  `roles` longtext COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '(DC2Type:array)',
  `twitch_user_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `twitch_access_token` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `twitch_refresh_token` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `gender` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `firstname` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `lastname` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `website` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `biography` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `locale` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `timezone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `facebook_uid` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `facebook_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `facebook_data` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `twitter_uid` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `twitter_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `twitter_data` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gplus_uid` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gplus_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `two_step_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date_of_birth` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_E54BFDA992FC23A8` (`username_canonical`),
  UNIQUE KEY `UNIQ_E54BFDA9A0D96FBF` (`email_canonical`),
  UNIQUE KEY `UNIQ_E54BFDA9C05FB297` (`confirmation_token`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `fos_user_user__group`;
CREATE TABLE `fos_user_user__group` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(180) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `roles` json NOT NULL COMMENT '(DC2Type:json)',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `fos_user_user_group`;
CREATE TABLE `fos_user_user_group` (
  `user_id` int NOT NULL,
  `group_id` int NOT NULL,
  PRIMARY KEY (`user_id`,`group_id`),
  KEY `IDX_B3C77447A76ED395` (`user_id`),
  KEY `IDX_B3C77447FE54D947` (`group_id`),
  CONSTRAINT `FK_B3C77447A76ED395` FOREIGN KEY (`user_id`) REFERENCES `fos_user__user` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_B3C77447FE54D947` FOREIGN KEY (`group_id`) REFERENCES `fos_user__group` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `home_row_item`;
CREATE TABLE `home_row_item` (
  `id` int NOT NULL AUTO_INCREMENT,
  `home_row_id` int DEFAULT NULL,
  `partner_id` int DEFAULT NULL,
  `label` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sort_index` smallint NOT NULL,
  `item_type` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL,
  `video_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `playlist_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `topic` json DEFAULT NULL COMMENT '(DC2Type:json)',
  `sort_and_trim_options` json DEFAULT NULL COMMENT '(DC2Type:json)',
  `show_art` tinyint(1) NOT NULL,
  `custom_art` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `overlay_art` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `offline_display_type` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL,
  `link_type` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL,
  `custom_link` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` longtext COLLATE utf8mb4_unicode_ci,
  `is_published` tinyint(1) NOT NULL,
  `timezone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_published_start` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_published_end` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `updated_at` datetime NOT NULL,
  `is_partner` tinyint(1) NOT NULL DEFAULT '0',
  `is_unique_container` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0 = unique, 1 = allow repeat',
  PRIMARY KEY (`id`),
  KEY `IDX_BC687E7973A6004F` (`home_row_id`),
  KEY `IDX_BC687E799393F8FE` (`partner_id`),
  CONSTRAINT `FK_BC687E7973A6004F` FOREIGN KEY (`home_row_id`) REFERENCES `home_rows` (`id`),
  CONSTRAINT `FK_BC687E799393F8FE` FOREIGN KEY (`partner_id`) REFERENCES `partner` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=808 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `home_row_item_operation`;
CREATE TABLE `home_row_item_operation` (
  `id` int NOT NULL AUTO_INCREMENT,
  `home_row_item_id` int DEFAULT NULL,
  `item_type` varchar(25) COLLATE utf8mb4_unicode_ci NOT NULL,
  `streamer_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'itemType=streamer => livestreaming_id, itemType=offline_streamer => userid',
  `game_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `priority` int DEFAULT NULL,
  `is_whitelisted` smallint DEFAULT NULL,
  `is_blacklisted` smallint DEFAULT NULL,
  `is_full_site_blacklisted` smallint DEFAULT NULL,
  `streamer_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `game_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `viewer` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_7C6B6293C72EFA38` (`home_row_item_id`),
  CONSTRAINT `FK_7C6B6293C72EFA38` FOREIGN KEY (`home_row_item_id`) REFERENCES `home_row_item` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `home_rows`;
CREATE TABLE `home_rows` (
  `id` int NOT NULL AUTO_INCREMENT,
  `partner_id` int DEFAULT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sort_index` smallint DEFAULT NULL,
  `layout` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` json DEFAULT NULL COMMENT '(DC2Type:json)',
  `is_published` tinyint(1) NOT NULL,
  `is_glow_styling` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_corner_cut` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `timezone` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_published_start` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_published_end` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `on_gamers_xtv` tinyint(1) NOT NULL DEFAULT '0',
  `row_padding_top` int DEFAULT '0',
  `row_padding_bottom` int DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `IDX_2BAD456E9393F8FE` (`partner_id`),
  CONSTRAINT `FK_2BAD456E9393F8FE` FOREIGN KEY (`partner_id`) REFERENCES `partner` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=41 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `master_setting`;
CREATE TABLE `master_setting` (
  `id` int NOT NULL AUTO_INCREMENT,
  `master_theme_id` int DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` longtext COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`),
  KEY `IDX_435355F01AB7BA00` (`master_theme_id`),
  CONSTRAINT `FK_435355F01AB7BA00` FOREIGN KEY (`master_theme_id`) REFERENCES `master_theme` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=103 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `master_theme`;
CREATE TABLE `master_theme` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` smallint DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `partner`;
CREATE TABLE `partner` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `partner_role`;
CREATE TABLE `partner_role` (
  `id` int NOT NULL AUTO_INCREMENT,
  `partner_id` int NOT NULL,
  `user_id` int NOT NULL,
  `role` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_75DB9729393F8FE` (`partner_id`),
  KEY `IDX_75DB972A76ED395` (`user_id`),
  CONSTRAINT `FK_75DB9729393F8FE` FOREIGN KEY (`partner_id`) REFERENCES `partner` (`id`),
  CONSTRAINT `FK_75DB972A76ED395` FOREIGN KEY (`user_id`) REFERENCES `fos_user__user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `site_settings`;
CREATE TABLE `site_settings` (
  `id` int NOT NULL AUTO_INCREMENT,
  `disable_home_access` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `theme`;
CREATE TABLE `theme` (
  `id` int NOT NULL AUTO_INCREMENT,
  `topic_id` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL,
  `label` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `item_type` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL,
  `banner_image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `embed_background` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `custom_art` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `art_background` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `error_log` (`id`, `error_message`, `error_type`, `status_code`, `container_id`, `created_at`) VALUES
(1, 'Notice: date_default_timezone_set(): Timezone ID \'AsiaKolkata\' is invalid /Users/ahmed/Herd/gmx_ms2_update/src/Command/CacheHomePageContainers.php 86', 'home_cache_clear', 500, NULL, '2024-08-12 18:51:55'),
(2, 'An exception occurred while executing a query: SQLSTATE[42S02]: Base table or view not found: 1146 Table \'gamersx.home_row_item\' doesn\'t exist /Users/ahmed/Herd/gmx_ms2_update/vendor/doctrine/dbal/src/Driver/API/MySQL/ExceptionConverter.php 49', 'twitch_game_containerizer', 1146, '802', '2024-08-13 05:57:19'),
(3, 'An exception occurred while executing a query: SQLSTATE[42S02]: Base table or view not found: 1146 Table \'gamersx.home_row_item\' doesn\'t exist /Users/ahmed/Herd/gmx_ms2_update/vendor/doctrine/dbal/src/Driver/API/MySQL/ExceptionConverter.php 49', 'twitch_game_containerizer', 1146, '803', '2024-08-13 05:57:19'),
(4, 'Failed to save cache /Users/ahmed/Herd/gmx_ms2_update/src/Command/CacheHomePageContainers.php 114', 'home_cache_clear', 500, NULL, '2024-08-13 06:55:27');

INSERT INTO `fos_user__user` (`id`, `username`, `username_canonical`, `email`, `email_canonical`, `enabled`, `salt`, `password`, `last_login`, `confirmation_token`, `password_requested_at`, `roles`, `twitch_user_id`, `twitch_access_token`, `twitch_refresh_token`, `created_at`, `updated_at`, `gender`, `firstname`, `lastname`, `website`, `biography`, `locale`, `timezone`, `phone`, `facebook_uid`, `facebook_name`, `facebook_data`, `twitter_uid`, `twitter_name`, `twitter_data`, `gplus_uid`, `gplus_name`, `token`, `two_step_code`, `date_of_birth`) VALUES
(1, 'admin', 'admin', 'admin@example.com', 'admin@example.com', 1, NULL, '$2y$12$L27/F0kV4YacWPR/o2RlC.DcCb/NBbDsxJgege8orptLege.ty0Gi', '2024-08-12 06:06:34', NULL, NULL, 'a:1:{i:0;s:16:\"ROLE_SUPER_ADMIN\";}', NULL, NULL, NULL, '2024-08-12 06:06:30', '2024-08-12 06:06:30', 'unknown', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);

INSERT INTO `home_row_item` (`id`, `home_row_id`, `partner_id`, `label`, `sort_index`, `item_type`, `video_id`, `playlist_id`, `topic`, `sort_and_trim_options`, `show_art`, `custom_art`, `overlay_art`, `offline_display_type`, `link_type`, `custom_link`, `description`, `is_published`, `timezone`, `is_published_start`, `is_published_end`, `updated_at`, `is_partner`, `is_unique_container`) VALUES
(802, 1, 2, 'ROBLOX', 0, 'game', NULL, NULL, '{\"label\": \"ROBLOX\", \"topicId\": \"23020\"}', '{\"maxLive\": 1.0, \"itemSortType\": \"asc\", \"maxContainers\": 1.0}', 0, 'DOUjAgTySF.jpg', 'fI3SXrdH7C.jpg', 'art', 'gamersx', NULL, NULL, 1, 'Asia/Kolkata', '00:01:00', '23:59:00', '2024-08-13 06:55:22', 0, 1),
(803, 1, NULL, 'League of Legends', 0, 'game', NULL, NULL, '{\"label\": \"League of Legends\", \"topicId\": \"21779\"}', '{\"maxLive\": 1.0, \"itemSortType\": \"asc\", \"maxContainers\": 1.0}', 0, 'Wm8-hA9SAQ.img', 'k52ucW4WTt.img', 'art', 'gamersx', NULL, NULL, 1, 'Asia/Kolkata', '00:00:00', '23:59:00', '2024-08-13 06:55:22', 0, 1),
(804, 1, NULL, 'BALLERLEAGUE', 0, 'streamer', NULL, NULL, '{\"label\": \"BALLERLEAGUE\", \"topicId\": \"527117425\"}', '{\"maxLive\": 1.0, \"itemSortType\": \"desc\", \"maxContainers\": 1.0}', 0, 'U5wyt75r8z.img', NULL, 'none', 'external', NULL, NULL, 1, 'America/Los_Angeles', '00:00:00', '23:59:00', '2024-08-13 06:55:22', 0, 1),
(805, 2, 1, 'Valkyrae', 4, 'channel', NULL, NULL, '{\"label\": \"Valkyrae\", \"topicId\": \"UCWxlUwW9BgGISaakjGM37aw\"}', '{\"maxLive\": 1.0, \"itemSortType\": \"asc\", \"maxContainers\": 1.0}', 0, NULL, NULL, 'overlay', 'external', NULL, NULL, 1, 'Asia/Kolkata', '00:01:00', '23:59:00', '2024-07-12 07:18:09', 0, 1),
(806, 4, 1, NULL, 1, 'twitch_video', 'https://www.twitch.tv/videos/2171815993', NULL, '{\"label\": null, \"topicId\": null}', '{\"maxLive\": 1.0, \"itemSortType\": \"desc\", \"maxContainers\": 2.0}', 0, 'zcUMItmU2_.jpg', '7Ay3WG-unG.jpg', 'art', 'gamersx', NULL, NULL, 1, 'Asia/Kolkata', '00:01:00', '23:59:00', '2024-08-13 06:55:26', 0, 1),
(807, 4, 1, 'Ninja Gaiden Sigma 2', 2, 'game', NULL, NULL, '{\"label\": \"Ninja Gaiden Sigma 2\", \"topicId\": \"23134\"}', '{\"maxLive\": 1.0, \"itemSortType\": \"asc\", \"maxContainers\": 1.0}', 0, 'AE_g0T1xdf.jpg', '00K-a4hJRx.jpg', 'art', 'gamersx', NULL, NULL, 1, 'Asia/Kolkata', '00:01:00', '23:59:00', '2024-08-13 06:55:26', 0, 1);

INSERT INTO `home_rows` (`id`, `partner_id`, `title`, `sort_index`, `layout`, `options`, `is_published`, `is_glow_styling`, `is_corner_cut`, `timezone`, `is_published_start`, `is_published_end`, `on_gamers_xtv`, `row_padding_top`, `row_padding_bottom`) VALUES
(1, 2, 'FullWidthDescriptive', 4, 'FullWidthDescriptive', '{\"maxLive\": 2.0, \"itemSortType\": \"desc\", \"maxContainers\": 6.0}', 1, 'enabled_if_live', 'enabled_if_live', 'Asia/Kolkata', '00:00:00', '23:59:00', 1, 1, 2),
(2, NULL, 'NumberedRow', 10, 'NumberedRow', '{\"maxLive\": 10, \"itemSortType\": \"desc\", \"maxContainers\": 15}', 1, 'enabled_if_live', 'enabled_if_live', 'Asia/Kolkata', '00:00:00', '23:59:00', 1, 0, 0),
(3, NULL, 'FullWidthImagery', 70, 'FullWidthImagery', '{\"maxLive\": 5, \"itemSortType\": \"desc\", \"maxContainers\": 5}', 1, 'enabled_if_live', 'enabled_if_live', 'Asia/Kolkata', '00:00:00', '23:59:00', 0, 0, 0),
(4, NULL, 'ClassicLg', 30, 'ClassicLg', '{\"maxLive\": 10, \"itemSortType\": \"desc\", \"maxContainers\": 10}', 1, 'enabled_if_live', 'enabled_if_live', 'Asia/Kolkata', '00:00:00', '23:59:00', 0, 0, 0),
(5, NULL, 'ClassicMd', 40, 'ClassicMd', '{\"maxLive\": 8, \"itemSortType\": \"desc\", \"maxContainers\": 12}', 1, 'enabled_if_live', 'enabled_if_live', 'Asia/Kolkata', '00:00:00', '23:59:00', 0, 0, 0),
(6, NULL, 'ClassicVertical', 60, 'ClassicVertical', '{\"maxLive\": 4, \"itemSortType\": \"fixed\", \"maxContainers\": 6}', 1, 'enabled_if_live', 'enabled_if_live', 'Asia/Kolkata', '00:00:00', '23:59:00', 0, 0, 0),
(7, NULL, 'Parallax', 20, 'Parallax', '{\"maxLive\": 15, \"itemSortType\": \"desc\", \"maxContainers\": 15}', 1, 'enabled_if_live', 'enabled_if_live', 'Asia/Kolkata', '00:00:00', '23:59:00', 0, 0, 0),
(8, NULL, 'ClassicSm', 50, 'ClassicSm', '{\"itemSortType\": \"desc\"}', 1, 'enabled_if_live', 'enabled_if_live', 'Asia/Kolkata', '00:00:00', '23:59:00', 0, 0, 0),
(40, NULL, 'YT Playlist Demo', 1000, 'ClassicMd', '{\"maxLive\": 10, \"itemSortType\": \"desc\", \"maxContainers\": 10}', 1, 'enabled_if_live', 'enabled_if_live', 'Asia/Kolkata', '00:00:00', '23:59:00', 0, 0, 0);

INSERT INTO `master_setting` (`id`, `master_theme_id`, `name`, `value`) VALUES
(1, 23, 'header_background_type', 'image'),
(2, 23, 'header_color', '#611919'),
(3, 23, 'body_background_type', 'color'),
(4, 23, 'body_color', '#40ce77'),
(5, 23, 'footer_background_type', 'color'),
(6, 23, 'footer_color', '#cd1313'),
(7, 23, 'font_type', 'local'),
(8, 23, 'remote_font_url', ''),
(9, 23, 'color', '#000000'),
(10, 23, 'numbered_row_color', '#000000'),
(11, 23, 'numbered_row_outline_color', '#000000'),
(12, 23, 'twitch_glow_color', '#000000'),
(13, 23, 'youtube_glow_color', '#000000'),
(14, 23, 'border', '1'),
(15, 23, 'border_color', '#000000'),
(16, 23, 'border_radius_top_left', '1'),
(17, 23, 'border_radius_top_right', '1'),
(18, 23, 'border_radius_bottom_right', '1'),
(19, 23, 'border_radius_bottom_left', '2'),
(20, 23, 'privacy_background_color', '#610f0f'),
(21, 23, 'privacy_label_font_color', '#ffffff'),
(22, 23, 'privacy_regular_font_color', '#ffffff'),
(23, 23, 'arrow_color', '#ff0a0a'),
(24, 23, 'font_family', 'Calibri,sans-serif,serif'),
(25, 19, 'header_background_type', 'color'),
(26, 19, 'header_color', '#611919'),
(27, 19, 'body_background_type', 'color'),
(28, 19, 'body_color', '#2c0707'),
(29, 19, 'footer_background_type', 'color'),
(30, 19, 'footer_color', '#cd1313'),
(31, 19, 'font_type', 'local'),
(32, 19, 'remote_font_url', ''),
(33, 19, 'color', '#000000'),
(34, 19, 'numbered_row_color', '#000000'),
(35, 19, 'numbered_row_outline_color', '#000000'),
(36, 19, 'twitch_glow_color', '#000000'),
(37, 19, 'youtube_glow_color', '#000000'),
(38, 19, 'border', '0'),
(39, 19, 'border_color', '#000000'),
(40, 19, 'border_radius_top_left', '0'),
(41, 19, 'border_radius_top_right', '0'),
(42, 19, 'border_radius_bottom_right', '0'),
(43, 19, 'border_radius_bottom_left', '0'),
(44, 19, 'privacy_background_color', '#000000'),
(45, 19, 'privacy_label_font_color', '#000000'),
(46, 19, 'privacy_regular_font_color', '#000000'),
(47, 19, 'arrow_color', '#000000'),
(48, 19, 'font_family', NULL),
(49, 18, 'header_logo', 'logo_300-64f733d9d45d0-64feb5efb2e6f.png'),
(50, 18, 'header_background', 'vc_cropped-669fb84dd2910.png'),
(51, 25, 'header_background_type', 'color'),
(52, 25, 'header_color', '#611919'),
(53, 25, 'body_background_type', 'color'),
(54, 25, 'body_color', '#d62424'),
(55, 25, 'footer_background_type', 'color'),
(56, 25, 'footer_color', '#31a300'),
(57, 25, 'font_type', 'local'),
(58, 25, 'remote_font_url', ''),
(59, 25, 'color', '#000000'),
(60, 25, 'numbered_row_color', '#000000'),
(61, 25, 'numbered_row_outline_color', '#000000'),
(62, 25, 'twitch_glow_color', '#000000'),
(63, 25, 'youtube_glow_color', '#000000'),
(64, 25, 'border', '0'),
(65, 25, 'border_color', '#1b4e0d'),
(66, 25, 'border_radius_top_left', '0'),
(67, 25, 'border_radius_top_right', '0'),
(68, 25, 'border_radius_bottom_right', '0'),
(69, 25, 'border_radius_bottom_left', '0'),
(70, 25, 'privacy_background_color', '#000000'),
(71, 25, 'privacy_label_font_color', '#000000'),
(72, 25, 'privacy_regular_font_color', '#000000'),
(73, 25, 'arrow_color', '#000000'),
(74, 25, 'font_family', NULL),
(75, 18, 'header_background_type', ''),
(76, 18, 'header_color', '#982a2a'),
(77, 18, 'body_background_type', 'color'),
(78, 18, 'body_color', '#d93a3a'),
(79, 18, 'footer_background_type', 'color'),
(80, 18, 'footer_color', '#31a300'),
(81, 18, 'font_type', 'local'),
(82, 18, 'remote_font_url', ''),
(83, 18, 'color', '#ffffff'),
(84, 18, 'numbered_row_color', '#000000'),
(85, 18, 'numbered_row_outline_color', '#000000'),
(86, 18, 'twitch_glow_color', '#000000'),
(87, 18, 'youtube_glow_color', '#000000'),
(88, 18, 'border', '2'),
(89, 18, 'border_color', '#e1ff00'),
(90, 18, 'border_radius_top_left', '2'),
(91, 18, 'border_radius_top_right', '2'),
(92, 18, 'border_radius_bottom_right', '2'),
(93, 18, 'border_radius_bottom_left', '2'),
(94, 18, 'privacy_background_color', '#000000'),
(95, 18, 'privacy_label_font_color', '#fbff00'),
(96, 18, 'privacy_regular_font_color', '#c061cb'),
(97, 18, 'arrow_color', '#000000'),
(98, 18, 'font_family', 'sans-serif'),
(99, 24, 'header_logo', 'logo-64f06b63d34a9-64f1c6e26deb4.png'),
(100, 18, 'footer_background', 'vc_cropped-669fb895c074e.png'),
(101, 18, 'body_background', 'street fighter-669fb964e8630.jpg'),
(102, 18, 'mobile_embed_height', '0');

INSERT INTO `master_theme` (`id`, `name`, `status`) VALUES
(18, 'Default', 1),
(19, 'Theme 1', 0),
(20, 'ABC', 0),
(21, 'Default XYZ', 0),
(22, 'XYZ', 0),
(23, 'dg fgfgfgfgfg', 0),
(24, '32132454', 0),
(25, 'Theme', 0);

INSERT INTO `partner` (`id`, `name`) VALUES
(1, 'Demo Partner 1'),
(2, 'Partner 2');

INSERT INTO `site_settings` (`id`, `disable_home_access`) VALUES
(1, 0);

INSERT INTO `theme` (`id`, `topic_id`, `label`, `item_type`, `banner_image`, `embed_background`, `custom_art`, `art_background`, `updated_at`) VALUES
(1, '732', 'Demon\'s Crest', 'game', 'fDZAUy_U6L.jpg', 'hero-bg-1-668283b074351356601846.png', 'cool-geometric-triangular-figure-neon-laser-light-great-backgrounds-wallpapers-668283b6d8b6d502862861.jpg', NULL, '2024-07-01 03:23:54'),
(2, '22123', 'Free Realms', 'game', 'BAVLqlWrcS.jpg', 'street-fighter-66a0cc7fce236559666312.jpg', 'spider-man-2-scaled-66a0cc80a1b28341749418.jpg', 'game-66a0cc813a5aa798235610.jpg', '2024-07-24 02:42:30');



/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;