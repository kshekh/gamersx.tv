-- -------------------------------------------------------------
-- TablePlus 6.1.2(568)
--
-- https://tableplus.com/
--
-- Database: gamersx
-- Generation Time: 2024-08-12 18:20:23.4410
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
  CONSTRAINT `FK_7C6B6293C72EFA38` FOREIGN KEY (`home_row_item_id`) REFERENCES `home_row_items` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `home_row_items`;
CREATE TABLE `home_row_items` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `master_theme`;
CREATE TABLE `master_theme` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` smallint DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `partner`;
CREATE TABLE `partner` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `fos_user__user` (`id`, `username`, `username_canonical`, `email`, `email_canonical`, `enabled`, `salt`, `password`, `last_login`, `confirmation_token`, `password_requested_at`, `roles`, `twitch_user_id`, `twitch_access_token`, `twitch_refresh_token`, `created_at`, `updated_at`, `gender`, `firstname`, `lastname`, `website`, `biography`, `locale`, `timezone`, `phone`, `facebook_uid`, `facebook_name`, `facebook_data`, `twitter_uid`, `twitter_name`, `twitter_data`, `gplus_uid`, `gplus_name`, `token`, `two_step_code`, `date_of_birth`) VALUES
(1, 'admin', 'admin', 'admin@example.com', 'admin@example.com', 1, NULL, '$2y$12$L27/F0kV4YacWPR/o2RlC.DcCb/NBbDsxJgege8orptLege.ty0Gi', '2024-08-12 06:06:34', NULL, NULL, 'a:1:{i:0;s:16:\"ROLE_SUPER_ADMIN\";}', NULL, NULL, NULL, '2024-08-12 06:06:30', '2024-08-12 06:06:30', 'unknown', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);

INSERT INTO `home_rows` (`id`, `partner_id`, `title`, `sort_index`, `layout`, `options`, `is_published`, `is_glow_styling`, `is_corner_cut`, `timezone`, `is_published_start`, `is_published_end`, `on_gamers_xtv`, `row_padding_top`, `row_padding_bottom`) VALUES
(1, 2, 'FullWidthDescriptive', 4, 'FullWidthDescriptive', '{\"maxLive\": 2.0, \"itemSortType\": \"desc\", \"maxContainers\": 6.0}', 1, 'enabled_if_live', 'enabled_if_live', 'Asia/Kolkata', '00:00:00', '23:59:00', 1, 1, 2),
(2, NULL, 'NumberedRow', 10, 'NumberedRow', '{\"maxLive\": 10, \"itemSortType\": \"desc\", \"maxContainers\": 15}', 1, 'enabled_if_live', 'enabled_if_live', 'Asia/Kolkata', '00:00:00', '23:59:00', 1, 0, 0),
(3, NULL, 'FullWidthImagery', 70, 'FullWidthImagery', '{\"maxLive\": 5, \"itemSortType\": \"desc\", \"maxContainers\": 5}', 1, 'enabled_if_live', 'enabled_if_live', 'AsiaKolkata', '00:00:00', '23:59:00', 0, 0, 0),
(4, NULL, 'ClassicLg', 30, 'ClassicLg', '{\"maxLive\": 10, \"itemSortType\": \"desc\", \"maxContainers\": 10}', 1, 'enabled_if_live', 'enabled_if_live', 'Asia/Kolkata', '00:00:00', '23:59:00', 0, 0, 0),
(5, NULL, 'ClassicMd', 40, 'ClassicMd', '{\"maxLive\": 8, \"itemSortType\": \"desc\", \"maxContainers\": 12}', 1, 'enabled_if_live', 'enabled_if_live', 'Asia/Kolkata', '00:00:00', '23:59:00', 0, 0, 0),
(6, NULL, 'ClassicVertical', 60, 'ClassicVertical', '{\"maxLive\": 4, \"itemSortType\": \"fixed\", \"maxContainers\": 6}', 1, 'enabled_if_live', 'enabled_if_live', 'Asia/Kolkata', '00:00:00', '23:59:00', 0, 0, 0),
(7, NULL, 'Parallax', 20, 'Parallax', '{\"maxLive\": 15, \"itemSortType\": \"desc\", \"maxContainers\": 15}', 1, 'enabled_if_live', 'enabled_if_live', 'Asia/Kolkata', '00:00:00', '23:59:00', 0, 0, 0),
(8, NULL, 'ClassicSm', 50, 'ClassicSm', '{\"itemSortType\": \"desc\"}', 1, 'enabled_if_live', 'enabled_if_live', 'Asia/Kolkata', '00:00:00', '23:59:00', 0, 0, 0),
(40, NULL, 'YT Playlist Demo', 1000, 'ClassicMd', '{\"maxLive\": 10, \"itemSortType\": \"desc\", \"maxContainers\": 10}', 1, 'enabled_if_live', 'enabled_if_live', 'Asia/Kolkata', '00:00:00', '23:59:00', 0, 0, 0);

INSERT INTO `site_settings` (`id`, `disable_home_access`) VALUES
(1, 1);



/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;