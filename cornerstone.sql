-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 14, 2020 at 12:00 PM
-- Server version: 10.4.6-MariaDB
-- PHP Version: 7.3.9

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Table for storing Admin login authorization codes';

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Cookie when asked to remember password for all users';

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Edit log for changes in the system';

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Login log for all users';

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Cornerstone Notification system for the admin dashboard';

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Cornerstone Options';

--
-- Dumping data for table `cs_options`
--

INSERT INTO `cs_options` (`option_id`, `option_type`, `option_name`, `option_value`, `autoload`, `option_edited_id`, `option_edited_dtm`) VALUES
(1, 'core', 'site_url', '', 1, NULL, NULL),
(2, 'core', 'site_https', '0', 1, NULL, NULL),
(3, 'core', 'site_name', '', 1, NULL, NULL),
(4, 'core', 'test_site', '1', 0, NULL, NULL),
(5, 'core', 'site_offline', '0', 0, NULL, NULL),
(6, 'core', 'site_timezone', 'Pacific/Auckland', 0, NULL, NULL),
(7, 'core', 'phone_locale', '', 0, NULL, NULL),
(8, 'core', 'site_version', '0.0.1', 0, NULL, NULL),
(10, 'core', 'error_log_type', '1,2', 1, NULL, NULL),
(11, 'core', 'site_from_email', '', 1, NULL, NULL),
(12, 'core', 'errors_to_email', '', 1, NULL, NULL),
(13, 'mail', 'enable_phpmailer', '1', 0, NULL, NULL),
(14, 'mail', 'smtp_host', '', 0, NULL, NULL),
(15, 'mail', 'smtp_username', '', 0, NULL, NULL),
(16, 'mail', 'smtp_password', '', 0, NULL, NULL),
(17, 'mail', 'smtp_port', '', 0, NULL, NULL),
(18, 'mail', 'smtp_secure', '', 0, NULL, NULL),
(19, 'mail', 'smtp_auth', '', 0, NULL, NULL),
(20, 'security', 'crypto_hex_length', '24', 0, NULL, NULL),
(21, 'security', 'registration_active', '0', 0, NULL, NULL),
(22, 'security', 'max_logins', '6', 0, NULL, NULL),
(23, 'security', 'auth_required', '0', 0, NULL, NULL),
(24, 'security', 'auth_expire', '900', 0, NULL, NULL),
(25, 'security', 'password_reset_expire', '1800', 0, NULL, NULL),
(26, 'security', 'session_expire', '1800', 0, NULL, NULL),
(27, 'security', 'cookie_expire', '0,30', 0, NULL, NULL),
(28, 'addon', 'texta_hq_active', '0', 0, NULL, NULL),
(29, 'addon', 'texta_hq_key', '', 0, NULL, NULL),
(30, 'addon', 'recaptcha_site_key', '', 0, NULL, NULL),
(31, 'addon', 'recaptcha_secret_key', '', 0, NULL, NULL),
(32, 'addon', 'facebook_secret', '', 0, NULL, NULL),
(33, 'addon', 'facebook_login_active', '0', 0, NULL, NULL),
(34, 'addon', 'analytics_code', '', 0, NULL, NULL),
(35, 'site', 'tooltip_settings', '', 0, NULL, NULL),
(36, 'addon', 'font_awesome_kit_url', '', 0, NULL, NULL),
(37, 'site', 'docs_private', '0', 0, NULL, NULL),
(38, 'site', 'site_notice', ',', 0, NULL, NULL),
(39, 'security', 'browser_tracking', '1', 0, NULL, NULL);

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Password Reset Table for all users';

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Session information for security and to allow multi-domain sharing';

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
  `user_group_id` int(11) UNSIGNED NOT NULL DEFAULT 0,
  `user_auth_rqd` tinyint(1) NOT NULL DEFAULT 0,
  `user_status` tinyint(1) NOT NULL DEFAULT 0,
  `user_created_dtm` datetime NOT NULL,
  `user_edited_id` int(11) UNSIGNED DEFAULT NULL,
  `user_edited_dtm` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Site users';

-- --------------------------------------------------------

--
-- Table structure for table `cs_user_groups`
--

CREATE TABLE `cs_user_groups` (
  `ugroup_id` int(11) UNSIGNED NOT NULL,
  `ugroup_key` varchar(25) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ugroup_title` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ugroup_display` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ugroup_colour` varchar(6) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'FFFFFF',
  `ugroup_locked` tinyint(1) NOT NULL DEFAULT 0,
  `ugroup_priority` tinyint(3) NOT NULL,
  `ugroup_edited_id` int(11) UNSIGNED DEFAULT NULL,
  `ugroup_edited_dtm` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='User Groups (Levels)';

--
-- Dumping data for table `cs_user_groups`
--

INSERT INTO `cs_user_groups` (`ugroup_id`, `ugroup_key`, `ugroup_title`, `ugroup_display`, `ugroup_colour`, `ugroup_locked`, `ugroup_priority`, `ugroup_edited_id`, `ugroup_edited_dtm`) VALUES
(1, 'master', 'Master', 'Master', 'FFFFFF', 1, 0, NULL, NULL),
(2, 'admin', 'Admin', 'Admin', 'FFFFFF', 1, 1, NULL, NULL);

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
  ADD KEY `user_group_id` (`user_group_id`),
  ADD KEY `user_edited_id` (`user_edited_id`);

--
-- Indexes for table `cs_user_groups`
--
ALTER TABLE `cs_user_groups`
  ADD PRIMARY KEY (`ugroup_id`),
  ADD UNIQUE KEY `ugroup_key` (`ugroup_key`),
  ADD UNIQUE KEY `ugroup_sort_order` (`ugroup_priority`);

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
-- AUTO_INCREMENT for table `cs_edit_log`
--
ALTER TABLE `cs_edit_log`
  MODIFY `edit_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cs_login_log`
--
ALTER TABLE `cs_login_log`
  MODIFY `login_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=84;

--
-- AUTO_INCREMENT for table `cs_notification`
--
ALTER TABLE `cs_notification`
  MODIFY `noti_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `cs_options`
--
ALTER TABLE `cs_options`
  MODIFY `option_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT for table `cs_password_reset`
--
ALTER TABLE `cs_password_reset`
  MODIFY `pwdreset_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `cs_users`
--
ALTER TABLE `cs_users`
  MODIFY `user_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `cs_user_groups`
--
ALTER TABLE `cs_user_groups`
  MODIFY `ugroup_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `cs_user_meta`
--
ALTER TABLE `cs_user_meta`
  MODIFY `umeta_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

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
-- Constraints for table `cs_users`
--
ALTER TABLE `cs_users`
  ADD CONSTRAINT `cs_users_ibfk_1` FOREIGN KEY (`user_group_id`) REFERENCES `cs_user_groups` (`ugroup_id`),
  ADD CONSTRAINT `cs_users_ibfk_2` FOREIGN KEY (`user_edited_id`) REFERENCES `cs_users` (`user_id`);

--
-- Constraints for table `cs_user_meta`
--
ALTER TABLE `cs_user_meta`
  ADD CONSTRAINT `cs_user_meta_ibfk_1` FOREIGN KEY (`umeta_user_id`) REFERENCES `cs_users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cs_user_meta_ibfk_2` FOREIGN KEY (`umeta_edited_id`) REFERENCES `cs_users` (`user_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
