-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 01, 2020 at 00:00 AM
-- Server version: 10.4.11-MariaDB
-- PHP Version: 7.4.4
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";
/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */
;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */
;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */
;
/*!40101 SET NAMES utf8mb4 */
;
--
-- Database: `cornerstone`
--
-- --------------------------------------------------------
--
-- Table structure for table `cs_authorization`
--
CREATE TABLE `cs_authorization` (
  `auth_id` int(11) UNSIGNED NOT NULL,
  `auth_user_id` int(11) UNSIGNED NOT NULL,
  `auth_selector` varchar(75) COLLATE utf8mb4_unicode_ci NOT NULL,
  `auth_token` varchar(75) COLLATE utf8mb4_unicode_ci NOT NULL,
  `auth_remember` tinyint(1) NOT NULL DEFAULT 0,
  `auth_ip_address` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL,
  `auth_user_agent` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `auth_dtm` datetime NOT NULL DEFAULT current_timestamp(),
  `auth_expire` datetime NOT NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = 'Table for storing Admin login authorization codes';
-- --------------------------------------------------------
--
-- Table structure for table `cs_auth_cookie`
--
CREATE TABLE `cs_auth_cookie` (
  `cookie_id` int(11) UNSIGNED NOT NULL,
  `cookie_user_id` int(11) UNSIGNED NOT NULL,
  `cookie_user_type` tinyint(2) NOT NULL DEFAULT 1 COMMENT '1: Admin',
  `cookie_password_hash` varchar(75) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cookie_key` varchar(75) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cookie_ip_address` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cookie_user_agent` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cookie_friendly_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cookie_set_dtm` datetime NOT NULL DEFAULT current_timestamp(),
  `cookie_expiry_dtm` datetime NOT NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = 'Cookie when asked to remember password for all users';
-- --------------------------------------------------------
--
-- Table structure for table `cs_content`
--
CREATE TABLE `cs_content` (
  `content_id` int(11) UNSIGNED NOT NULL,
  `content_title` varchar(160) COLLATE utf8mb4_unicode_ci NOT NULL,
  `content_content` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `content_status` tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '0: Draft, 1: Published, 2: Private, 3: Archived',
  `content_type` tinyint(3) UNSIGNED NOT NULL DEFAULT 0 COMMENT '0: Page, 1: FAQ, 2: Block',
  `content_section_id` int(11) UNSIGNED NOT NULL DEFAULT 0,
  `content_show_updated` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0: No, 1: Yes',
  `content_added_id` int(11) UNSIGNED NOT NULL,
  `content_added_dtm` datetime NOT NULL,
  `content_edited_id` int(11) UNSIGNED DEFAULT NULL,
  `content_edited_dtm` datetime DEFAULT NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = 'Cornerstone site content data (e.g. Pages, FAQs etc)';
-- --------------------------------------------------------
--
-- Table structure for table `cs_content_meta`
--
CREATE TABLE `cs_content_meta` (
  `cmeta_id` int(11) UNSIGNED NOT NULL,
  `cmeta_content_id` int(11) UNSIGNED NOT NULL,
  `cmeta_key` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cmeta_value` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cmeta_added_id` int(11) UNSIGNED NOT NULL,
  `cmeta_added_dtm` datetime NOT NULL,
  `cmeta_edited_id` int(11) UNSIGNED DEFAULT NULL,
  `cmeta_edited_dtm` datetime DEFAULT NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = 'Cornerstone site content meta data';
-- --------------------------------------------------------
--
-- Table structure for table `cs_content_section`
--
CREATE TABLE `cs_content_section` (
  `section_id` int(11) UNSIGNED NOT NULL,
  `section_name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `section_type` tinyint(3) UNSIGNED NOT NULL DEFAULT 0 COMMENT '0: Page, 1: FAQ, 5: Menu',
  `section_location_name` varchar(25) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `section_added_id` int(11) UNSIGNED NOT NULL,
  `section_added_dtm` datetime NOT NULL,
  `section_edited_id` int(11) UNSIGNED DEFAULT NULL,
  `section_edited_dtm` datetime DEFAULT NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = 'Cornerstone site content sections';
-- --------------------------------------------------------
--
-- Table structure for table `cs_edit_log`
--
CREATE TABLE `cs_edit_log` (
  `edit_id` int(11) UNSIGNED NOT NULL,
  `edit_user_id` int(11) UNSIGNED NOT NULL,
  `edit_table_key` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `edit_value_id` int(11) UNSIGNED NOT NULL,
  `edit_data` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `edit_dtm` datetime NOT NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = 'Edit log for changes in the system';
-- --------------------------------------------------------
--
-- Table structure for table `cs_login_log`
--
CREATE TABLE `cs_login_log` (
  `login_id` int(11) UNSIGNED NOT NULL,
  `login_user_id` int(11) UNSIGNED NOT NULL,
  `login_user_type` tinyint(2) NOT NULL DEFAULT 1 COMMENT '1: Admin',
  `login_dtm` datetime NOT NULL,
  `login_ip_address` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL,
  `login_status` tinyint(4) NOT NULL DEFAULT 0
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = 'Login log for all users';
-- --------------------------------------------------------
--
-- Table structure for table `cs_notification`
--
CREATE TABLE `cs_notification` (
  `noti_id` int(11) UNSIGNED NOT NULL,
  `noti_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `noti_content` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `noti_status` tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '0: Unread, 1: Seen, 2: Read',
  `noti_for_id` int(11) UNSIGNED NOT NULL,
  `noti_for_group` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0: No, 1: Yes',
  `noti_type_id` int(11) UNSIGNED DEFAULT NULL,
  `noti_created_at` datetime NOT NULL,
  `noti_read_at` datetime DEFAULT NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = 'Cornerstone Notification system for the admin dashboard';
-- --------------------------------------------------------
--
-- Table structure for table `cs_options`
--
CREATE TABLE `cs_options` (
  `option_id` int(11) UNSIGNED NOT NULL,
  `option_type` varchar(25) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'core',
  `option_name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `option_value` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `autoload` tinyint(1) NOT NULL DEFAULT 0,
  `option_edited_id` int(11) UNSIGNED DEFAULT NULL,
  `option_edited_dtm` datetime DEFAULT NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = 'Cornerstone Options';
--
-- Dumping data for table `cs_options`
--
INSERT INTO `cs_options` (
    `option_id`,
    `option_type`,
    `option_name`,
    `option_value`,
    `autoload`,
    `option_edited_id`,
    `option_edited_dtm`
  )
VALUES (
    1,
    'core',
    'site_url',
    'www.cornerstone.local',
    1,
    NULL,
    NULL
  ),
  (2, 'core', 'site_https', '0', 1, NULL, NULL),
  (
    3,
    'core',
    'site_name',
    'Cornerstone PHP Framework',
    1,
    NULL,
    NULL
  ),
  (4, 'core', 'test_site', '1', 0, NULL, NULL),
  (5, 'core', 'site_offline', '0', 0, NULL, NULL),
  (
    6,
    'core',
    'site_timezone',
    'Pacific/Auckland',
    0,
    NULL,
    NULL
  ),
  (7, 'core', 'phone_locale', '+64', 0, NULL, NULL),
  (
    8,
    'core',
    'site_version',
    '0.2.1',
    0,
    NULL,
    NULL
  ),
  (
    10,
    'core',
    'error_log_type',
    '1,2',
    1,
    NULL,
    NULL
  ),
  (
    11,
    'mail',
    'site_from_email',
    '',
    1,
    NULL,
    NULL
  ),
  (
    12,
    'mail',
    'errors_to_email',
    '',
    1,
    NULL,
    NULL
  ),
  (
    13,
    'mail',
    'enable_phpmailer',
    '1',
    0,
    NULL,
    NULL
  ),
  (
    14,
    'mail',
    'smtp_host',
    '',
    0,
    NULL,
    NULL
  ),
  (
    15,
    'mail',
    'smtp_username',
    '',
    0,
    NULL,
    NULL
  ),
  (
    16,
    'mail',
    'smtp_password',
    '',
    0,
    NULL,
    NULL
  ),
  (17, 'mail', 'smtp_port', '', 0, NULL, NULL),
  (
    18,
    'mail',
    'smtp_secure',
    'FALSE',
    0,
    NULL,
    NULL
  ),
  (19, 'mail', 'smtp_auth', 'TRUE', 0, NULL, NULL),
  (
    20,
    'security',
    'crypto_hex_length',
    '24',
    0,
    NULL,
    NULL
  ),
  (
    21,
    'security',
    'registration_active',
    '0',
    0,
    NULL,
    NULL
  ),
  (22, 'security', 'max_logins', '6', 0, NULL, NULL),
  (
    23,
    'security',
    'auth_required',
    '0',
    0,
    NULL,
    NULL
  ),
  (
    24,
    'security',
    'auth_expire',
    '900',
    0,
    NULL,
    NULL
  ),
  (
    25,
    'security',
    'password_reset_expire',
    '1800',
    0,
    NULL,
    NULL
  ),
  (
    26,
    'security',
    'session_expire',
    '1800',
    0,
    NULL,
    NULL
  ),
  (
    27,
    'security',
    'cookie_expire',
    '0,30',
    0,
    NULL,
    NULL
  ),
  (
    28,
    'security',
    'browser_tracking',
    '1',
    0,
    NULL,
    NULL
  ),
  (
    29,
    'addon',
    'texta_hq_active',
    '0',
    0,
    NULL,
    NULL
  ),
  (30, 'addon', 'texta_hq_key', '', 0, NULL, NULL),
  (
    31,
    'addon',
    'recaptcha_site_key',
    '',
    0,
    NULL,
    NULL
  ),
  (
    32,
    'addon',
    'recaptcha_secret_key',
    '',
    0,
    NULL,
    NULL
  ),
  (
    33,
    'addon',
    'facebook_secret',
    '',
    0,
    NULL,
    NULL
  ),
  (
    34,
    'addon',
    'facebook_login_active',
    '0',
    0,
    NULL,
    NULL
  ),
  (35, 'addon', 'xero_oauth2', '', 0, NULL, NULL),
  (36, 'addon', 'analytics_code', '', 0, NULL, NULL),
  (
    37,
    'addon',
    'font_awesome_kit_url',
    '',
    0,
    NULL,
    NULL
  ),
  (
    38,
    'site',
    'tooltip_settings',
    '',
    0,
    NULL,
    NULL
  ),
  (39, 'site', 'docs_private', '0', 0, NULL, NULL),
  (40, 'site', 'site_notice', ',', 0, NULL, NULL);
-- --------------------------------------------------------
--
-- Table structure for table `cs_password_reset`
--
CREATE TABLE `cs_password_reset` (
  `pwdreset_id` int(11) UNSIGNED NOT NULL,
  `pwdreset_user_id` int(11) UNSIGNED NOT NULL,
  `pwdreset_user_type` tinyint(2) NOT NULL DEFAULT 1 COMMENT '1: Admin',
  `pwdreset_selector` varchar(75) COLLATE utf8mb4_unicode_ci NOT NULL,
  `pwdreset_token` varchar(75) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Hashed using users key',
  `pwdreset_request_ip` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL,
  `pwdreset_user_agent` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pwdreset_dtm` datetime NOT NULL,
  `pwdreset_status` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0: Incomplete, 1: Success',
  `pwdreset_success_dtm` datetime DEFAULT NULL,
  `pwdreset_expire` datetime NOT NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = 'Password Reset Table for all users';
-- --------------------------------------------------------
--
-- Table structure for table `cs_roles`
--
CREATE TABLE `cs_roles` (
  `role_id` int(11) UNSIGNED NOT NULL,
  `role_key` varchar(25) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role_name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role_meta` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `role_edited_id` int(11) UNSIGNED DEFAULT NULL,
  `role_edited_dtm` datetime DEFAULT NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = 'Cornerstone user roles';
--
-- Dumping data for table `cs_roles`
--
INSERT INTO `cs_roles` (
    `role_id`,
    `role_key`,
    `role_name`,
    `role_meta`,
    `role_edited_id`,
    `role_edited_dtm`
  )
VALUES (
    1,
    'master',
    'Master',
    '{\"locked\":true,\"color\":\"#FFFFFF\"}',
    NULL,
    NULL
  ),
  (
    2,
    'admin',
    'Admin',
    '{\"color\":\"#FFFFFF\"}',
    NULL,
    NULL
  );
-- --------------------------------------------------------
--
-- Table structure for table `cs_role_permissions`
--
CREATE TABLE `cs_role_permissions` (
  `rp_id` int(11) UNSIGNED NOT NULL,
  `rp_key` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = 'Cornerstone user role permissions';
-- --------------------------------------------------------
--
-- Table structure for table `cs_role_perms`
--
CREATE TABLE `cs_role_perms` (
  `rpl_role_id` int(11) UNSIGNED NOT NULL,
  `rpl_rp_id` int(11) UNSIGNED NOT NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = 'Cornerstone user role permission links';
-- --------------------------------------------------------
--
-- Table structure for table `cs_seo_url`
--
CREATE TABLE `cs_seo_url` (
  `seo_id` int(11) UNSIGNED NOT NULL,
  `seo_type` int(11) UNSIGNED NOT NULL COMMENT '0: Page',
  `seo_type_id` int(11) UNSIGNED NOT NULL,
  `seo_keyword` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `seo_primary` tinyint(1) NOT NULL DEFAULT 0
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = 'SEO friendly URL for pages etc';
-- --------------------------------------------------------
--
-- Table structure for table `cs_session`
--
CREATE TABLE `cs_session` (
  `session_id` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL,
  `session_ip_address` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL,
  `session_user_id` int(11) UNSIGNED DEFAULT NULL,
  `session_data` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `session_access_dtm` datetime NOT NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = 'Session information for security and to allow multi-domain sharing';
-- --------------------------------------------------------
--
-- Table structure for table `cs_users`
--
CREATE TABLE `cs_users` (
  `user_id` int(11) UNSIGNED NOT NULL,
  `user_login` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_display_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_password` varchar(75) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_password_key` varchar(25) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_first_name` varchar(32) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_last_name` varchar(32) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_role_id` int(11) UNSIGNED NOT NULL DEFAULT 0,
  `user_auth_rqd` tinyint(1) NOT NULL DEFAULT 0,
  `user_status` tinyint(1) NOT NULL DEFAULT 0,
  `user_created_dtm` datetime NOT NULL,
  `user_edited_id` int(11) UNSIGNED DEFAULT NULL,
  `user_edited_dtm` datetime DEFAULT NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = 'Site users';
--
-- Dumping data for table `cs_users`
--
INSERT INTO `cs_users` (
    `user_id`,
    `user_login`,
    `user_display_name`,
    `user_password`,
    `user_password_key`,
    `user_email`,
    `user_first_name`,
    `user_last_name`,
    `user_role_id`,
    `user_auth_rqd`,
    `user_status`,
    `user_created_dtm`,
    `user_edited_id`,
    `user_edited_dtm`
  )
VALUES (
    1,
    'cornerstone',
    'Cornerstone',
    '$2y$10$ide/acUhcH.THVNNv0nbhuDES9U9KMnN9lKGSsiq8VXKqurRBGPaS',
    'cc398ea77fb3039df2c7e70d',
    'webmaster@dpdesignz.co.nz',
    'Cornerstone',
    'System',
    1,
    0,
    1,
    '2019-08-07 12:32:51',
    NULL,
    NULL
  );
-- --------------------------------------------------------
--
-- Table structure for table `cs_user_meta`
--
CREATE TABLE `cs_user_meta` (
  `umeta_id` int(11) UNSIGNED NOT NULL,
  `umeta_user_id` int(11) UNSIGNED NOT NULL,
  `umeta_key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `umeta_value` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `umeta_edited_id` int(11) UNSIGNED DEFAULT NULL,
  `umeta_edited_dtm` datetime DEFAULT NULL COMMENT 'YYYY-MM-DD HH:MM:SS'
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;
--
-- Indexes for dumped tables
--
--
-- Indexes for table `cs_authorization`
--
ALTER TABLE `cs_authorization`
ADD PRIMARY KEY (`auth_id`),
ADD KEY `cs_authorization_ibfk_1` (`auth_user_id`);
--
-- Indexes for table `cs_auth_cookie`
--
ALTER TABLE `cs_auth_cookie`
ADD PRIMARY KEY (`cookie_id`),
ADD KEY `cookie_user_id` (`cookie_user_id`),
ADD KEY `cookie_key` (`cookie_key`);
--
-- Indexes for table `cs_content`
--
ALTER TABLE `cs_content`
ADD PRIMARY KEY (`content_id`),
ADD KEY `content_type` (`content_type`),
ADD KEY `content__id` (`content_id`);
--
-- Indexes for table `cs_content_meta`
--
ALTER TABLE `cs_content_meta`
ADD PRIMARY KEY (`cmeta_id`),
ADD KEY `cmeta_content_id` (`cmeta_content_id`),
ADD KEY `cmeta_key` (`cmeta_key`);
--
-- Indexes for table `cs_content_section`
--
ALTER TABLE `cs_content_section`
ADD PRIMARY KEY (`section_id`);
--
-- Indexes for table `cs_edit_log`
--
ALTER TABLE `cs_edit_log`
ADD PRIMARY KEY (`edit_id`),
ADD KEY `edit_user_id` (`edit_user_id`);
--
-- Indexes for table `cs_login_log`
--
ALTER TABLE `cs_login_log`
ADD PRIMARY KEY (`login_id`),
ADD KEY `login_user_id` (`login_user_id`);
--
-- Indexes for table `cs_notification`
--
ALTER TABLE `cs_notification`
ADD PRIMARY KEY (`noti_id`);
--
-- Indexes for table `cs_options`
--
ALTER TABLE `cs_options`
ADD PRIMARY KEY (`option_id`),
ADD UNIQUE KEY `option_name` (`option_name`),
ADD KEY `option_edited_id` (`option_edited_id`);
--
-- Indexes for table `cs_password_reset`
--
ALTER TABLE `cs_password_reset`
ADD PRIMARY KEY (`pwdreset_id`),
ADD UNIQUE KEY `pwdreset_token` (`pwdreset_token`),
ADD KEY `pwdreset_user_id` (`pwdreset_user_id`),
ADD KEY `pwd_reset_selector` (`pwdreset_selector`);
--
-- Indexes for table `cs_roles`
--
ALTER TABLE `cs_roles`
ADD PRIMARY KEY (`role_id`),
ADD KEY `role_edited_id` (`role_edited_id`);
--
-- Indexes for table `cs_role_permissions`
--
ALTER TABLE `cs_role_permissions`
ADD PRIMARY KEY (`rp_id`),
ADD UNIQUE KEY `rp_key` (`rp_key`);
--
-- Indexes for table `cs_role_perms`
--
ALTER TABLE `cs_role_perms`
ADD PRIMARY KEY (`rpl_role_id`, `rpl_rp_id`),
ADD KEY `cs_role_perms_ibfk_2` (`rpl_rp_id`),
ADD KEY `rpl_role_id` (`rpl_role_id`, `rpl_rp_id`) USING BTREE;
--
-- Indexes for table `cs_seo_url`
--
ALTER TABLE `cs_seo_url`
ADD PRIMARY KEY (`seo_id`),
ADD UNIQUE KEY `seo_keyword` (`seo_keyword`),
ADD KEY `seo_type_id` (`seo_type_id`);
--
-- Indexes for table `cs_session`
--
ALTER TABLE `cs_session`
ADD PRIMARY KEY (`session_id`);
--
-- Indexes for table `cs_users`
--
ALTER TABLE `cs_users`
ADD PRIMARY KEY (`user_id`),
ADD UNIQUE KEY `user_username` (`user_login`),
ADD UNIQUE KEY `user_email` (`user_email`),
ADD KEY `user_edited_id` (`user_edited_id`),
ADD KEY `user_role_id` (`user_role_id`) USING BTREE;
--
-- Indexes for table `cs_user_meta`
--
ALTER TABLE `cs_user_meta`
ADD PRIMARY KEY (`umeta_id`),
ADD KEY `umeta_userID` (`umeta_user_id`),
ADD KEY `umeta_editedID` (`umeta_edited_id`);
--
-- AUTO_INCREMENT for dumped tables
--
--
-- AUTO_INCREMENT for table `cs_authorization`
--
ALTER TABLE `cs_authorization`
MODIFY `auth_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `cs_auth_cookie`
--
ALTER TABLE `cs_auth_cookie`
MODIFY `cookie_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `cs_content`
--
ALTER TABLE `cs_content`
MODIFY `content_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `cs_content_meta`
--
ALTER TABLE `cs_content_meta`
MODIFY `cmeta_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `cs_content_section`
--
ALTER TABLE `cs_content_section`
MODIFY `section_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `cs_edit_log`
--
ALTER TABLE `cs_edit_log`
MODIFY `edit_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `cs_login_log`
--
ALTER TABLE `cs_login_log`
MODIFY `login_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `cs_notification`
--
ALTER TABLE `cs_notification`
MODIFY `noti_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `cs_options`
--
ALTER TABLE `cs_options`
MODIFY `option_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  AUTO_INCREMENT = 41;
--
-- AUTO_INCREMENT for table `cs_password_reset`
--
ALTER TABLE `cs_password_reset`
MODIFY `pwdreset_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `cs_roles`
--
ALTER TABLE `cs_roles`
MODIFY `role_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  AUTO_INCREMENT = 3;
--
-- AUTO_INCREMENT for table `cs_role_permissions`
--
ALTER TABLE `cs_role_permissions`
MODIFY `rp_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `cs_seo_url`
--
ALTER TABLE `cs_seo_url`
MODIFY `seo_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `cs_users`
--
ALTER TABLE `cs_users`
MODIFY `user_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  AUTO_INCREMENT = 2;
--
-- AUTO_INCREMENT for table `cs_user_meta`
--
ALTER TABLE `cs_user_meta`
MODIFY `umeta_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- Constraints for dumped tables
--
--
-- Constraints for table `cs_authorization`
--
ALTER TABLE `cs_authorization`
ADD CONSTRAINT `cs_authorization_ibfk_1` FOREIGN KEY (`auth_user_id`) REFERENCES `cs_users` (`user_id`) ON DELETE CASCADE;
--
-- Constraints for table `cs_edit_log`
--
ALTER TABLE `cs_edit_log`
ADD CONSTRAINT `cs_edit_log_ibfk_1` FOREIGN KEY (`edit_user_id`) REFERENCES `cs_users` (`user_id`);
--
-- Constraints for table `cs_options`
--
ALTER TABLE `cs_options`
ADD CONSTRAINT `cs_options_ibfk_1` FOREIGN KEY (`option_edited_id`) REFERENCES `cs_users` (`user_id`);
--
-- Constraints for table `cs_roles`
--
ALTER TABLE `cs_roles`
ADD CONSTRAINT `cs_roles_ibfk_1` FOREIGN KEY (`role_edited_id`) REFERENCES `cs_users` (`user_id`);
--
-- Constraints for table `cs_role_perms`
--
ALTER TABLE `cs_role_perms`
ADD CONSTRAINT `cs_role_perms_ibfk_1` FOREIGN KEY (`rpl_role_id`) REFERENCES `cs_roles` (`role_id`) ON DELETE CASCADE ON UPDATE CASCADE,
ADD CONSTRAINT `cs_role_perms_ibfk_2` FOREIGN KEY (`rpl_rp_id`) REFERENCES `cs_role_permissions` (`rp_id`) ON DELETE CASCADE ON UPDATE CASCADE;
--
-- Constraints for table `cs_users`
--
ALTER TABLE `cs_users`
ADD CONSTRAINT `cs_users_ibfk_1` FOREIGN KEY (`user_role_id`) REFERENCES `cs_roles` (`role_id`),
ADD CONSTRAINT `cs_users_ibfk_2` FOREIGN KEY (`user_edited_id`) REFERENCES `cs_users` (`user_id`);
--
-- Constraints for table `cs_user_meta`
--
ALTER TABLE `cs_user_meta`
ADD CONSTRAINT `cs_user_meta_ibfk_1` FOREIGN KEY (`umeta_user_id`) REFERENCES `cs_users` (`user_id`) ON DELETE CASCADE,
ADD CONSTRAINT `cs_user_meta_ibfk_2` FOREIGN KEY (`umeta_edited_id`) REFERENCES `cs_users` (`user_id`);
COMMIT;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */
;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */
;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */
;