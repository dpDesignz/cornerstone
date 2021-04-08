<?php

/**
 * Core Table Creation
 *
 * @package Cornerstone
 */

// Check for the options table
$checkTables = $cdbh->dbh->select(
  DB_PREFIX . "options",
  "option_id"
);
if ($cdbh->dbh->getNum_Rows() < 1) {

  #############################
  ####    CREATE TABLES    ####
  #############################

  // Create the authorization table
  $cdbh->dbh->query_prepared("CREATE TABLE `" . DB_PREFIX . "authorization` (
  `auth_id` int(11) UNSIGNED NOT NULL,
  `auth_user_id` int(11) UNSIGNED NOT NULL,
  `auth_selector` varchar(75) COLLATE utf8mb4_unicode_ci NOT NULL,
  `auth_token` varchar(75) COLLATE utf8mb4_unicode_ci NOT NULL,
  `auth_remember` tinyint(1) NOT NULL DEFAULT 0,
  `auth_ip_address` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL,
  `auth_user_agent` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `auth_dtm` datetime NOT NULL DEFAULT current_timestamp(),
  `auth_expire` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Table for storing Admin login authorization codes';", array());

  // Create the authorization cookie table
  $cdbh->dbh->query_prepared("CREATE TABLE `" . DB_PREFIX . "auth_cookie` (
  `cookie_id` int(11) UNSIGNED NOT NULL,
  `cookie_user_id` int(11) UNSIGNED NOT NULL,
  `cookie_password_hash` varchar(75) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cookie_key` varchar(75) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cookie_ip_address` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cookie_user_agent` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cookie_friendly_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cookie_set_dtm` datetime NOT NULL DEFAULT current_timestamp(),
  `cookie_expiry_dtm` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Cookie when asked to remember password for all users';", array());

  // Create the content table
  $cdbh->dbh->query_prepared("CREATE TABLE `" . DB_PREFIX . "content` (
  `content_id` int(11) UNSIGNED NOT NULL,
  `content_title` varchar(160) COLLATE utf8mb4_unicode_ci NOT NULL,
  `content_content` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `content_status` tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '0: Draft, 1: Published, 2: Private, 3: Archived',
  `content_type` tinyint(3) UNSIGNED NOT NULL DEFAULT 0 COMMENT '0: Page, 1: FAQ, 2: Block',
  `content_section_id` int(11) UNSIGNED DEFAULT NULL,
  `content_sort_order` int(4) NOT NULL DEFAULT 0,
  `content_show_updated` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0: No, 1: Yes',
  `content_added_id` int(11) UNSIGNED NOT NULL,
  `content_added_dtm` datetime NOT NULL,
  `content_edited_id` int(11) UNSIGNED DEFAULT NULL,
  `content_edited_dtm` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Cornerstone site content data (e.g. Pages, FAQs etc)';", array());

  // Create the FAQ content table
  $cdbh->dbh->query_prepared("CREATE TABLE `" . DB_PREFIX . "content_faq_section` (
  `faqs_id` int(11) UNSIGNED NOT NULL,
  `faqs_content_id` int(11) UNSIGNED NOT NULL,
  `faqs_section_id` int(11) UNSIGNED NOT NULL,
  `faqs_sort_order` int(4) UNSIGNED NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Cornerstone FAQ to Section linking';", array());

  // Create the content menu table
  $cdbh->dbh->query_prepared("CREATE TABLE `" . DB_PREFIX . "content_menu` (
  `menui_id` int(11) UNSIGNED NOT NULL,
  `menui_content_id` int(11) UNSIGNED DEFAULT NULL,
  `menui_menu_id` int(11) UNSIGNED NOT NULL,
  `menui_custom_title` varchar(25) DEFAULT NULL,
  `menui_custom_url` varchar(255) DEFAULT NULL,
  `menui_sort_order` int(4) NOT NULL DEFAULT 0,
  `menui_added_id` int(11) UNSIGNED NOT NULL,
  `menui_added_dtm` datetime DEFAULT NULL,
  `menui_edited_id` int(11) UNSIGNED DEFAULT NULL,
  `menui_edited_dtm` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Cornerstone menu items';", array());

  // Create the content meta table
  $cdbh->dbh->query_prepared("CREATE TABLE `" . DB_PREFIX . "content_meta` (
  `cmeta_id` int(11) UNSIGNED NOT NULL,
  `cmeta_content_id` int(11) UNSIGNED NOT NULL,
  `cmeta_key` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cmeta_value` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cmeta_added_id` int(11) UNSIGNED NOT NULL,
  `cmeta_added_dtm` datetime NOT NULL,
  `cmeta_edited_id` int(11) UNSIGNED DEFAULT NULL,
  `cmeta_edited_dtm` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Cornerstone site content meta data';", array());

  // Create the content section table
  $cdbh->dbh->query_prepared("CREATE TABLE `" . DB_PREFIX . "content_section` (
  `section_id` int(11) UNSIGNED NOT NULL,
  `section_name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `section_type` tinyint(3) UNSIGNED NOT NULL DEFAULT 0 COMMENT '0: Page, 1: FAQ, 5: Menu',
  `section_location_name` varchar(25) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `section_added_id` int(11) UNSIGNED NOT NULL,
  `section_added_dtm` datetime NOT NULL,
  `section_edited_id` int(11) UNSIGNED DEFAULT NULL,
  `section_edited_dtm` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Cornerstone site content sections';", array());

  // Create the edit log table
  $cdbh->dbh->query_prepared("CREATE TABLE `" . DB_PREFIX . "edit_log` (
  `edit_id` int(11) UNSIGNED NOT NULL,
  `edit_user_id` int(11) UNSIGNED NOT NULL,
  `edit_table_key` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `edit_value_id` int(11) UNSIGNED NOT NULL,
  `edit_data` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `edit_dtm` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Edit log for changes in the system';", array());

  // Create the login log table
  $cdbh->dbh->query_prepared("CREATE TABLE `" . DB_PREFIX . "login_log` (
  `login_id` int(11) UNSIGNED NOT NULL,
  `login_user_id` int(11) UNSIGNED NOT NULL,
  `login_dtm` datetime NOT NULL,
  `login_ip_address` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL,
  `login_status` tinyint(4) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Login log for all users';", array());

  // Create the notifications table
  $cdbh->dbh->query_prepared("CREATE TABLE `" . DB_PREFIX . "notification` (
  `noti_id` int(11) UNSIGNED NOT NULL,
  `noti_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `noti_content` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `noti_status` tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '0: Unread, 1: Seen, 2: Read',
  `noti_for_id` int(11) UNSIGNED NOT NULL,
  `noti_for_group` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0: No, 1: Yes',
  `noti_type_id` int(11) UNSIGNED DEFAULT NULL,
  `noti_created_at` datetime NOT NULL,
  `noti_read_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Cornerstone Notification system for the admin dashboard';", array());

  // Create the options table
  $cdbh->dbh->query_prepared("CREATE TABLE `" . DB_PREFIX . "options` (
  `option_id` int(11) UNSIGNED NOT NULL,
  `option_type` varchar(25) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'core',
  `option_name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `option_value` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `autoload` tinyint(1) NOT NULL DEFAULT 0,
  `option_edited_id` int(11) UNSIGNED DEFAULT NULL,
  `option_edited_dtm` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Cornerstone Options';", array());

  // Create the password reset table
  $cdbh->dbh->query_prepared("CREATE TABLE `" . DB_PREFIX . "password_reset` (
  `pwdreset_id` int(11) UNSIGNED NOT NULL,
  `pwdreset_user_id` int(11) UNSIGNED NOT NULL,
  `pwdreset_selector` varchar(75) COLLATE utf8mb4_unicode_ci NOT NULL,
  `pwdreset_token` varchar(75) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Hashed using users key',
  `pwdreset_request_ip` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL,
  `pwdreset_user_agent` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pwdreset_dtm` datetime NOT NULL,
  `pwdreset_status` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0: Incomplete, 1: Success',
  `pwdreset_success_dtm` datetime DEFAULT NULL,
  `pwdreset_expire` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Password Reset Table for all users';", array());

  // Create the roles table
  $cdbh->dbh->query_prepared("CREATE TABLE `" . DB_PREFIX . "roles` (
  `role_id` int(11) UNSIGNED NOT NULL,
  `role_key` varchar(25) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role_name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role_meta` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `role_edited_id` int(11) UNSIGNED DEFAULT NULL,
  `role_edited_dtm` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Cornerstone user roles';", array());

  // Create the role permissions table
  $cdbh->dbh->query_prepared("CREATE TABLE `" . DB_PREFIX . "role_permissions` (
  `rp_id` int(11) UNSIGNED NOT NULL,
  `rp_key` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Cornerstone user role permissions';", array());

  // Create the role permission links table
  $cdbh->dbh->query_prepared("CREATE TABLE `" . DB_PREFIX . "role_perms` (
  `rpl_role_id` int(11) UNSIGNED NOT NULL,
  `rpl_rp_id` int(11) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Cornerstone user role permission links';", array());

  // Create the SEO URL table
  $cdbh->dbh->query_prepared("CREATE TABLE `" . DB_PREFIX . "seo_url` (
  `seo_id` int(11) UNSIGNED NOT NULL,
  `seo_type` int(11) UNSIGNED NOT NULL COMMENT '0: Page, 1: FAQ',
  `seo_type_id` int(11) UNSIGNED NOT NULL,
  `seo_keyword` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `seo_primary` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='SEO friendly URL for pages etc';", array());

  // Create the session table
  $cdbh->dbh->query_prepared("CREATE TABLE `" . DB_PREFIX . "session` (
  `session_id` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL,
  `session_ip_address` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL,
  `session_user_id` int(11) UNSIGNED DEFAULT NULL,
  `session_data` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `session_access_dtm` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Session information for security and to allow multi-domain sharing';", array());

  // Create the users table
  $cdbh->dbh->query_prepared("CREATE TABLE `" . DB_PREFIX . "users` (
  `user_id` int(11) UNSIGNED NOT NULL,
  `user_login` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_display_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_password` varchar(75) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_password_key` varchar(25) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_first_name` varchar(32) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_last_name` varchar(32) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_role_id` int(11) UNSIGNED DEFAULT NULL,
  `user_auth_rqd` tinyint(1) NOT NULL DEFAULT 0,
  `user_status` tinyint(1) NOT NULL DEFAULT 0,
  `user_created_dtm` datetime NOT NULL,
  `user_edited_id` int(11) UNSIGNED DEFAULT NULL,
  `user_edited_dtm` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Site users';", array());

  // Create the user meta table
  $cdbh->dbh->query_prepared("CREATE TABLE `" . DB_PREFIX . "user_meta` (
  `umeta_id` int(11) UNSIGNED NOT NULL,
  `umeta_user_id` int(11) UNSIGNED NOT NULL,
  `umeta_key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `umeta_value` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `umeta_edited_id` int(11) UNSIGNED DEFAULT NULL,
  `umeta_edited_dtm` datetime DEFAULT NULL COMMENT 'YYYY-MM-DD HH:MM:SS'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;", array());

  ###########################
  ####    SET INDEXES    ####
  ###########################

  // Indexes for authorization table
  $cdbh->dbh->query_prepared("ALTER TABLE `" . DB_PREFIX . "authorization`
  ADD PRIMARY KEY (`auth_id`),
  ADD KEY `auth_user_id` (`auth_user_id`);", array());

  // Indexes for authorization cookie table
  $cdbh->dbh->query_prepared("ALTER TABLE `" . DB_PREFIX . "auth_cookie`
  ADD PRIMARY KEY (`cookie_id`),
  ADD KEY `cookie_user_id` (`cookie_user_id`),
  ADD KEY `cookie_key` (`cookie_key`);", array());

  // Indexes for content table
  $cdbh->dbh->query_prepared("ALTER TABLE `" . DB_PREFIX . "content`
  ADD PRIMARY KEY (`content_id`),
  ADD KEY `content_type` (`content_type`),
  ADD KEY `content_id` (`content_id`),
  ADD KEY `content_added_id` (`content_added_id`),
  ADD KEY `content_edited_id` (`content_edited_id`),
  ADD KEY `content_section_id` (`content_section_id`);", array());

  // Indexes for content FAQ table
  $cdbh->dbh->query_prepared("ALTER TABLE `" . DB_PREFIX . "content_faq_section`
  ADD PRIMARY KEY (`faqs_id`),
  ADD KEY `faqs_content_id` (`faqs_content_id`),
  ADD KEY `faqs_section_id` (`faqs_section_id`);", array());

  // Indexes for content menu table
  $cdbh->dbh->query_prepared("ALTER TABLE `" . DB_PREFIX . "content_menu`
  ADD PRIMARY KEY (`menui_id`),
  ADD KEY `menui_content_id` (`menui_content_id`),
  ADD KEY `menui_menu_id` (`menui_menu_id`),
  ADD KEY `menui_added_id` (`menui_added_id`),
  ADD KEY `menui_edited_id` (`menui_edited_id`);", array());

  // Indexes for content meta table
  $cdbh->dbh->query_prepared("ALTER TABLE `" . DB_PREFIX . "content_meta`
  ADD PRIMARY KEY (`cmeta_id`),
  ADD KEY `cmeta_content_id` (`cmeta_content_id`),
  ADD KEY `cmeta_key` (`cmeta_key`),
  ADD KEY `cmeta_added_id` (`cmeta_added_id`),
  ADD KEY `cmeta_edited_id` (`cmeta_edited_id`);", array());

  // Indexes for content section table
  $cdbh->dbh->query_prepared("ALTER TABLE `" . DB_PREFIX . "content_section`
  ADD PRIMARY KEY (`section_id`),
  ADD KEY `section_added_id` (`section_added_id`),
  ADD KEY `section_edited_id` (`section_edited_id`);", array());

  // Indexes for edit log table
  $cdbh->dbh->query_prepared("ALTER TABLE `" . DB_PREFIX . "edit_log`
  ADD PRIMARY KEY (`edit_id`),
  ADD KEY `edit_user_id` (`edit_user_id`);", array());

  // Indexes for login log table
  $cdbh->dbh->query_prepared("ALTER TABLE `" . DB_PREFIX . "login_log`
  ADD PRIMARY KEY (`login_id`),
  ADD KEY `login_user_id` (`login_user_id`);", array());

  // Indexes for notification table
  $cdbh->dbh->query_prepared("ALTER TABLE `" . DB_PREFIX . "notification`
  ADD PRIMARY KEY (`noti_id`);", array());

  // Indexes for options table
  $cdbh->dbh->query_prepared("ALTER TABLE `" . DB_PREFIX . "options`
  ADD PRIMARY KEY (`option_id`),
  ADD UNIQUE KEY `option_name` (`option_name`),
  ADD KEY `option_edited_id` (`option_edited_id`);", array());

  // Indexes for password reset table
  $cdbh->dbh->query_prepared("ALTER TABLE `" . DB_PREFIX . "password_reset`
  ADD PRIMARY KEY (`pwdreset_id`),
  ADD UNIQUE KEY `pwdreset_token` (`pwdreset_token`),
  ADD KEY `pwdreset_user_id` (`pwdreset_user_id`),
  ADD KEY `pwd_reset_selector` (`pwdreset_selector`);", array());

  // Indexes for roles table
  $cdbh->dbh->query_prepared("ALTER TABLE `" . DB_PREFIX . "roles`
  ADD PRIMARY KEY (`role_id`),
  ADD KEY `role_edited_id` (`role_edited_id`);", array());

  // Indexes for role permissions table
  $cdbh->dbh->query_prepared("ALTER TABLE `" . DB_PREFIX . "role_permissions`
  ADD PRIMARY KEY (`rp_id`),
  ADD UNIQUE KEY `rp_key` (`rp_key`);", array());

  // Indexes for role permission links table
  $cdbh->dbh->query_prepared("ALTER TABLE `" . DB_PREFIX . "role_perms`
  ADD PRIMARY KEY (`rpl_role_id`,`rpl_rp_id`),
  ADD KEY `rpl_rp_id` (`rpl_rp_id`),
  ADD KEY `rpl_role_id` (`rpl_role_id`,`rpl_rp_id`) USING BTREE;", array());

  // Indexes for SEO URL table
  $cdbh->dbh->query_prepared("ALTER TABLE `" . DB_PREFIX . "seo_url`
  ADD PRIMARY KEY (`seo_id`),
  ADD UNIQUE KEY `seo_keyword` (`seo_keyword`),
  ADD KEY `seo_type_id` (`seo_type_id`);", array());

  // Indexes for session table
  $cdbh->dbh->query_prepared("ALTER TABLE `" . DB_PREFIX . "session`
  ADD PRIMARY KEY (`session_id`);", array());

  // Indexes for users table
  $cdbh->dbh->query_prepared("ALTER TABLE `" . DB_PREFIX . "users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `user_email` (`user_email`),
  ADD UNIQUE KEY `user_login` (`user_login`) USING BTREE,
  ADD KEY `user_edited_id` (`user_edited_id`),
  ADD KEY `user_role_id` (`user_role_id`) USING BTREE;", array());

  // Indexes for user meta table
  $cdbh->dbh->query_prepared("ALTER TABLE `" . DB_PREFIX . "user_meta`
  ADD PRIMARY KEY (`umeta_id`),
  ADD KEY `umeta_userID` (`umeta_user_id`),
  ADD KEY `umeta_editedID` (`umeta_edited_id`);", array());

  ##############################
  ####    AUTO INCREMENT    ####
  ##############################

  // Indexes for authorization table
  $cdbh->dbh->query_prepared("ALTER TABLE `" . DB_PREFIX . "authorization`
  MODIFY `auth_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;", array());

  // Indexes for authorization cookie table
  $cdbh->dbh->query_prepared("ALTER TABLE `" . DB_PREFIX . "auth_cookie`
  MODIFY `cookie_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;", array());

  // Indexes for content table
  $cdbh->dbh->query_prepared("ALTER TABLE `" . DB_PREFIX . "content`
  MODIFY `content_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;", array());

  // Indexes for content FAQ table
  $cdbh->dbh->query_prepared("ALTER TABLE `" . DB_PREFIX . "content_faq_section`
  MODIFY `faqs_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;", array());

  // Indexes for content menu table
  $cdbh->dbh->query_prepared("ALTER TABLE `" . DB_PREFIX . "content_menu`
  MODIFY `menui_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;", array());

  // Indexes for content meta table
  $cdbh->dbh->query_prepared("ALTER TABLE `" . DB_PREFIX . "content_meta`
  MODIFY `cmeta_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;", array());

  // Indexes for content section table
  $cdbh->dbh->query_prepared("ALTER TABLE `" . DB_PREFIX . "content_section`
  MODIFY `section_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;", array());

  // Indexes for edit log table
  $cdbh->dbh->query_prepared("ALTER TABLE `" . DB_PREFIX . "edit_log`
  MODIFY `edit_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;", array());

  // Indexes for login log table
  $cdbh->dbh->query_prepared("ALTER TABLE `" . DB_PREFIX . "login_log`
  MODIFY `login_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;", array());

  // Indexes for notification table
  $cdbh->dbh->query_prepared("ALTER TABLE `" . DB_PREFIX . "notification`
  MODIFY `noti_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;", array());

  // Indexes for options table
  $cdbh->dbh->query_prepared("ALTER TABLE `" . DB_PREFIX . "options`
  MODIFY `option_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;", array());

  // Indexes for password reset table
  $cdbh->dbh->query_prepared("ALTER TABLE `" . DB_PREFIX . "password_reset`
  MODIFY `pwdreset_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;", array());

  // Indexes for roles table
  $cdbh->dbh->query_prepared("ALTER TABLE `" . DB_PREFIX . "roles`
  MODIFY `role_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;", array());

  // Indexes for role permission links table
  $cdbh->dbh->query_prepared("ALTER TABLE `" . DB_PREFIX . "role_perms`
  MODIFY `rp_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;", array());

  // Indexes for SEO URL table
  $cdbh->dbh->query_prepared("ALTER TABLE `" . DB_PREFIX . "seo_url`
  MODIFY `seo_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;", array());

  // Indexes for users table
  $cdbh->dbh->query_prepared("ALTER TABLE `" . DB_PREFIX . "users`
  MODIFY `user_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;", array());

  // Indexes for user meta table
  $cdbh->dbh->query_prepared("ALTER TABLE `" . DB_PREFIX . "user_meta`
  MODIFY `umeta_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;", array());

  ###########################
  ####    CONSTRAINTS    ####
  ###########################

  // Constraints for authorization table
  $cdbh->dbh->query_prepared("ALTER TABLE `" . DB_PREFIX . "authorization`
  ADD CONSTRAINT `cs_authorization_ibfk_1` FOREIGN KEY (`auth_user_id`) REFERENCES `cs_users` (`user_id`) ON DELETE CASCADE;", array());

  // Constraints for authorization cookie table
  $cdbh->dbh->query_prepared("ALTER TABLE `" . DB_PREFIX . "auth_cookie`
  ADD CONSTRAINT `cs_auth_cookie_ibfk_1` FOREIGN KEY (`cookie_user_id`) REFERENCES `cs_users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;", array());

  // Indexes for content table
  $cdbh->dbh->query_prepared("ALTER TABLE `" . DB_PREFIX . "content`
  ADD CONSTRAINT `cs_content_ibfk_1` FOREIGN KEY (`content_added_id`) REFERENCES `cs_users` (`user_id`),
  ADD CONSTRAINT `cs_content_ibfk_2` FOREIGN KEY (`content_edited_id`) REFERENCES `cs_users` (`user_id`),
  ADD CONSTRAINT `cs_content_ibfk_3` FOREIGN KEY (`content_section_id`) REFERENCES `cs_content_section` (`section_id`);", array());

  // Indexes for content FAQ table
  $cdbh->dbh->query_prepared("ALTER TABLE `" . DB_PREFIX . "content_faq_section`
  ADD CONSTRAINT `cs_content_faq_section_ibfk_1` FOREIGN KEY (`faqs_content_id`) REFERENCES `cs_content` (`content_id`),
  ADD CONSTRAINT `cs_content_faq_section_ibfk_2` FOREIGN KEY (`faqs_section_id`) REFERENCES `cs_content_section` (`section_id`);", array());

  // Indexes for content menu table
  $cdbh->dbh->query_prepared("ALTER TABLE `" . DB_PREFIX . "content_menu`
  ADD CONSTRAINT `cs_content_menu_ibfk_1` FOREIGN KEY (`menui_content_id`) REFERENCES `cs_content` (`content_id`),
  ADD CONSTRAINT `cs_content_menu_ibfk_2` FOREIGN KEY (`menui_added_id`) REFERENCES `cs_users` (`user_id`),
  ADD CONSTRAINT `cs_content_menu_ibfk_3` FOREIGN KEY (`menui_edited_id`) REFERENCES `cs_users` (`user_id`),
  ADD CONSTRAINT `cs_content_menu_ibfk_4` FOREIGN KEY (`menui_menu_id`) REFERENCES `cs_content_section` (`section_id`);", array());

  // Indexes for content meta table
  $cdbh->dbh->query_prepared("ALTER TABLE `" . DB_PREFIX . "content_meta`
  ADD CONSTRAINT `cs_content_meta_ibfk_1` FOREIGN KEY (`cmeta_content_id`) REFERENCES `cs_content` (`content_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `cs_content_meta_ibfk_2` FOREIGN KEY (`cmeta_added_id`) REFERENCES `cs_users` (`user_id`),
  ADD CONSTRAINT `cs_content_meta_ibfk_3` FOREIGN KEY (`cmeta_edited_id`) REFERENCES `cs_users` (`user_id`);", array());

  // Indexes for content section table
  $cdbh->dbh->query_prepared("ALTER TABLE `" . DB_PREFIX . "content_section`
  ADD CONSTRAINT `cs_content_section_ibfk_1` FOREIGN KEY (`section_added_id`) REFERENCES `cs_users` (`user_id`),
  ADD CONSTRAINT `cs_content_section_ibfk_2` FOREIGN KEY (`section_edited_id`) REFERENCES `cs_users` (`user_id`);", array());

  // Indexes for edit log table
  $cdbh->dbh->query_prepared("ALTER TABLE `" . DB_PREFIX . "edit_log`
  ADD CONSTRAINT `cs_edit_log_ibfk_1` FOREIGN KEY (`edit_user_id`) REFERENCES `cs_users` (`user_id`);", array());

  // Indexes for login log table
  $cdbh->dbh->query_prepared("ALTER TABLE `" . DB_PREFIX . "login_log`
  ADD CONSTRAINT `cs_login_log_ibfk_1` FOREIGN KEY (`login_user_id`) REFERENCES `cs_users` (`user_id`);", array());

  // Indexes for notification table
  $cdbh->dbh->query_prepared("ALTER TABLE `" . DB_PREFIX . "notification`
  ADD PRIMARY KEY (`noti_id`);", array());

  // Indexes for options table
  $cdbh->dbh->query_prepared("ALTER TABLE `" . DB_PREFIX . "options`
  ADD CONSTRAINT `cs_options_ibfk_1` FOREIGN KEY (`option_edited_id`) REFERENCES `cs_users` (`user_id`);", array());

  // Indexes for password reset table
  $cdbh->dbh->query_prepared("ALTER TABLE `" . DB_PREFIX . "password_reset`
  ADD CONSTRAINT `cs_password_reset_ibfk_1` FOREIGN KEY (`pwdreset_user_id`) REFERENCES `cs_users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;", array());

  // Indexes for roles table
  $cdbh->dbh->query_prepared("ALTER TABLE `" . DB_PREFIX . "roles`
  ADD CONSTRAINT `cs_roles_ibfk_1` FOREIGN KEY (`role_edited_id`) REFERENCES `cs_users` (`user_id`);", array());

  // Indexes for role permission links table
  $cdbh->dbh->query_prepared("ALTER TABLE `" . DB_PREFIX . "role_permissions`
  ADD CONSTRAINT `cs_role_perms_ibfk_1` FOREIGN KEY (`rpl_role_id`) REFERENCES `cs_roles` (`role_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `cs_role_perms_ibfk_2` FOREIGN KEY (`rpl_rp_id`) REFERENCES `cs_role_permissions` (`rp_id`) ON DELETE CASCADE ON UPDATE CASCADE;", array());

  // Indexes for users table
  $cdbh->dbh->query_prepared("ALTER TABLE `" . DB_PREFIX . "users`
  ADD CONSTRAINT `cs_users_ibfk_1` FOREIGN KEY (`user_role_id`) REFERENCES `cs_roles` (`role_id`),
  ADD CONSTRAINT `cs_users_ibfk_2` FOREIGN KEY (`user_edited_id`) REFERENCES `cs_users` (`user_id`);", array());

  // Indexes for user meta table
  $cdbh->dbh->query_prepared("ALTER TABLE `" . DB_PREFIX . "user_meta`
  ADD CONSTRAINT `cs_user_meta_ibfk_1` FOREIGN KEY (`umeta_user_id`) REFERENCES `cs_users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cs_user_meta_ibfk_2` FOREIGN KEY (`umeta_edited_id`) REFERENCES `cs_users` (`user_id`);", array());

  ##############################
  ####    INSERT CONTENT    ####
  ##############################

  // Add content to the options table
  // CORE
  $cdbh->dbh->insert(
    DB_PREFIX . "options",
    array(
      'option_type' => 'core',
      'option_name' => 'site_url',
      'option_value' => $siteURLBase,
      'autoload' => 1
    )
  );
  // Site HTTPS
  $isHttps =
    $_SERVER['HTTPS']
    ?? $_SERVER['REQUEST_SCHEME']
    ?? $_SERVER['HTTP_X_FORWARDED_PROTO']
    ?? null;
  $isHttps =
    $isHttps && (strcasecmp('on', $isHttps) == 0
      || strcasecmp('https', $isHttps) == 0);
  $cdbh->dbh->insert(
    DB_PREFIX . "options",
    array(
      'option_type' => 'core',
      'option_name' => 'site_https',
      'option_value' => ($isHttps) ? "1" : "0",
      'autoload' => 1
    )
  );
  // Site Name
  $cdbh->dbh->insert(
    DB_PREFIX . "options",
    array(
      'option_type' => 'core',
      'option_name' => 'site_name',
      'option_value' => $siteName,
      'autoload' => 1
    )
  );
  // Test Site
  $cdbh->dbh->insert(
    DB_PREFIX . "options",
    array(
      'option_type' => 'core',
      'option_name' => 'test_site',
      'option_value' => '0',
      'autoload' => 0
    )
  );
  // Site Offline
  $cdbh->dbh->insert(
    DB_PREFIX . "options",
    array(
      'option_type' => 'core',
      'option_name' => 'site_offline',
      'option_value' => '0',
      'autoload' => 0
    )
  );
  // Site Timezone
  $cdbh->dbh->insert(
    DB_PREFIX . "options",
    array(
      'option_type' => 'core',
      'option_name' => 'site_timezone',
      'option_value' => 'Pacific/Auckland',
      'autoload' => 0
    )
  );
  // Phone Locale
  $cdbh->dbh->insert(
    DB_PREFIX . "options",
    array(
      'option_type' => 'core',
      'option_name' => 'phone_locale',
      'option_value' => '+64',
      'autoload' => 0
    )
  );
  // Site version
  $cdbh->dbh->insert(
    DB_PREFIX . "options",
    array(
      'option_type' => 'core',
      'option_name' => 'site_version',
      'option_value' => '0.0.1',
      'autoload' => 0
    )
  );
  // Error Logging
  $cdbh->dbh->insert(
    DB_PREFIX . "options",
    array(
      'option_type' => 'core',
      'option_name' => 'error_log_type',
      'option_value' => '1,2',
      'autoload' => 1
    )
  );
  // MAIL
  // Site From Email
  $cdbh->dbh->insert(
    DB_PREFIX . "options",
    array(
      'option_type' => 'mail',
      'option_name' => 'site_from_email',
      'option_value' => $userEmail,
      'autoload' => 1
    )
  );
  // Errors to Email
  $cdbh->dbh->insert(
    DB_PREFIX . "options",
    array(
      'option_type' => 'mail',
      'option_name' => 'errors_to_email',
      'option_value' => $userEmail,
      'autoload' => 1
    )
  );
  // PHP Mailer
  $cdbh->dbh->insert(
    DB_PREFIX . "options",
    array(
      'option_type' => 'mail',
      'option_name' => 'enable_phpmailer',
      'option_value' => '0',
      'autoload' => 0
    )
  );
  // SMTP: Host
  $cdbh->dbh->insert(
    DB_PREFIX . "options",
    array(
      'option_type' => 'mail',
      'option_name' => 'smtp_host',
      'option_value' => '',
      'autoload' => 0
    )
  );
  // SMTP: Username
  $cdbh->dbh->insert(
    DB_PREFIX . "options",
    array(
      'option_type' => 'mail',
      'option_name' => 'smtp_username',
      'option_value' => '',
      'autoload' => 0
    )
  );
  // SMTP: Password
  $cdbh->dbh->insert(
    DB_PREFIX . "options",
    array(
      'option_type' => 'mail',
      'option_name' => 'smtp_password',
      'option_value' => '',
      'autoload' => 0
    )
  );
  // SMTP: Port
  $cdbh->dbh->insert(
    DB_PREFIX . "options",
    array(
      'option_type' => 'mail',
      'option_name' => 'smtp_port',
      'option_value' => '26',
      'autoload' => 0
    )
  );
  // SMTP: Secure
  $cdbh->dbh->insert(
    DB_PREFIX . "options",
    array(
      'option_type' => 'mail',
      'option_name' => 'smtp_secure',
      'option_value' => 'FALSE',
      'autoload' => 0
    )
  );
  // SMTP: Auth
  $cdbh->dbh->insert(
    DB_PREFIX . "options",
    array(
      'option_type' => 'mail',
      'option_name' => 'smtp_auth',
      'option_value' => 'TRUE',
      'autoload' => 0
    )
  );
  // SECURITY
  // Crypto Hex Length
  $cdbh->dbh->insert(
    DB_PREFIX . "options",
    array(
      'option_type' => 'security',
      'option_name' => 'crypto_hex_length',
      'option_value' => '24',
      'autoload' => 0
    )
  );
  // Registration
  $cdbh->dbh->insert(
    DB_PREFIX . "options",
    array(
      'option_type' => 'security',
      'option_name' => 'registration_active',
      'option_value' => '0',
      'autoload' => 0
    )
  );
  // Max Login Attempts
  $cdbh->dbh->insert(
    DB_PREFIX . "options",
    array(
      'option_type' => 'security',
      'option_name' => 'max_logins',
      'option_value' => '6',
      'autoload' => 0
    )
  );
  // Auth Required
  $cdbh->dbh->insert(
    DB_PREFIX . "options",
    array(
      'option_type' => 'security',
      'option_name' => 'auth_required',
      'option_value' => '0',
      'autoload' => 0
    )
  );
  // Auth Expire Length (seconds)
  $cdbh->dbh->insert(
    DB_PREFIX . "options",
    array(
      'option_type' => 'security',
      'option_name' => 'auth_expire',
      'option_value' => '900',
      'autoload' => 0
    )
  );
  // Password Reset Expire Length (seconds)
  $cdbh->dbh->insert(
    DB_PREFIX . "options",
    array(
      'option_type' => 'security',
      'option_name' => 'password_reset_expire',
      'option_value' => '1800',
      'autoload' => 0
    )
  );
  // Session Expire Length (seconds)
  $cdbh->dbh->insert(
    DB_PREFIX . "options",
    array(
      'option_type' => 'security',
      'option_name' => 'session_expire',
      'option_value' => '1800',
      'autoload' => 0
    )
  );
  // Cookie Expire Length
  $cdbh->dbh->insert(
    DB_PREFIX . "options",
    array(
      'option_type' => 'security',
      'option_name' => 'cookie_expire',
      'option_value' => '0,30',
      'autoload' => 0
    )
  );
  // Browser Tracking
  $cdbh->dbh->insert(
    DB_PREFIX . "options",
    array(
      'option_type' => 'security',
      'option_name' => 'browser_tracking',
      'option_value' => '1',
      'autoload' => 0
    )
  );
  // ADDON
  // TextaHQ Active
  $cdbh->dbh->insert(
    DB_PREFIX . "options",
    array(
      'option_type' => 'addon',
      'option_name' => 'texta_hq_active',
      'option_value' => '0',
      'autoload' => 0
    )
  );
  // TextaHQ Key
  $cdbh->dbh->insert(
    DB_PREFIX . "options",
    array(
      'option_type' => 'addon',
      'option_name' => 'texta_hq_key',
      'option_value' => '0',
      'autoload' => 0
    )
  );
  // ReCAPTCHA Site Key
  $cdbh->dbh->insert(
    DB_PREFIX . "options",
    array(
      'option_type' => 'addon',
      'option_name' => 'recaptcha_site_key',
      'option_value' => '',
      'autoload' => 0
    )
  );
  // ReCAPTCHA Secret Key
  $cdbh->dbh->insert(
    DB_PREFIX . "options",
    array(
      'option_type' => 'addon',
      'option_name' => 'recaptcha_secret_key',
      'option_value' => '',
      'autoload' => 0
    )
  );
  // Facebook Secret Key
  $cdbh->dbh->insert(
    DB_PREFIX . "options",
    array(
      'option_type' => 'addon',
      'option_name' => 'facebook_secret',
      'option_value' => '',
      'autoload' => 0
    )
  );
  // Facebook Login Active
  $cdbh->dbh->insert(
    DB_PREFIX . "options",
    array(
      'option_type' => 'addon',
      'option_name' => 'facebook_login_active',
      'option_value' => '0',
      'autoload' => 0
    )
  );
  // Xero OAuth2
  $cdbh->dbh->insert(
    DB_PREFIX . "options",
    array(
      'option_type' => 'addon',
      'option_name' => 'xero_oauth2',
      'option_value' => '',
      'autoload' => 0
    )
  );
  // Analytics Code
  $cdbh->dbh->insert(
    DB_PREFIX . "options",
    array(
      'option_type' => 'addon',
      'option_name' => 'analytics_code',
      'option_value' => '',
      'autoload' => 0
    )
  );
  // Fontawesome Kit URL
  $cdbh->dbh->insert(
    DB_PREFIX . "options",
    array(
      'option_type' => 'addon',
      'option_name' => 'font_awesome_kit_url',
      'option_value' => $faKitURL,
      'autoload' => 0
    )
  );
  // SITE
  // Tooltip Settings
  $cdbh->dbh->insert(
    DB_PREFIX . "options",
    array(
      'option_type' => 'site',
      'option_name' => 'tooltip_settings',
      'option_value' => '',
      'autoload' => 0
    )
  );
  // Private Docs
  $cdbh->dbh->insert(
    DB_PREFIX . "options",
    array(
      'option_type' => 'site',
      'option_name' => 'docs_private',
      'option_value' => '0',
      'autoload' => 0
    )
  );
  // Site Notice
  $cdbh->dbh->insert(
    DB_PREFIX . "options",
    array(
      'option_type' => 'site',
      'option_name' => 'site_notice',
      'option_value' => '',
      'autoload' => 0
    )
  );

  // Add initial roles
  $cdbh->dbh->insert(
    DB_PREFIX . "roles",
    array(
      'role_key' => 'master',
      'role_name' => 'Master',
      'role_meta' => '{"locked":true,"color":"#FFFFFF"}'
    )
  );
  $cdbh->dbh->insert(
    DB_PREFIX . "roles",
    array(
      'role_key' => 'admin',
      'role_name' => 'Admin',
      'role_meta' => '{"color":"#FFFFFF"}'
    )
  );

  // Add initial role permissions
  $cdbh->dbh->insert(
    DB_PREFIX . "role_permissions",
    array(
      'rp_key' => 'access_admin'
    )
  );
  $cdbh->dbh->insert(
    DB_PREFIX . "role_perms",
    array(
      'rpl_role_id' => '2',
      'rpl_rp_id' => '1'
    )
  );
  // Settings
  $cdbh->dbh->insert(
    DB_PREFIX . "role_permissions",
    array(
      'rp_key' => 'edit_core_settings'
    )
  );
  $cdbh->dbh->insert(
    DB_PREFIX . "role_permissions",
    array(
      'rp_key' => 'edit_mail_settings'
    )
  );
  $cdbh->dbh->insert(
    DB_PREFIX . "role_permissions",
    array(
      'rp_key' => 'edit_security_settings'
    )
  );
  $cdbh->dbh->insert(
    DB_PREFIX . "role_permissions",
    array(
      'rp_key' => 'edit_site_settings'
    )
  );
  $cdbh->dbh->insert(
    DB_PREFIX . "role_permissions",
    array(
      'rp_key' => 'edit_addon_settings'
    )
  );
  $cdbh->dbh->insert(
    DB_PREFIX . "role_permissions",
    array(
      'rp_key' => 'view_log_settings'
    )
  );
  $cdbh->dbh->insert(
    DB_PREFIX . "role_permissions",
    array(
      'rp_key' => 'view_php_info'
    )
  );
  // Users
  $cdbh->dbh->insert(
    DB_PREFIX . "role_permissions",
    array(
      'rp_key' => 'add_user'
    )
  );
  $cdbh->dbh->insert(
    DB_PREFIX . "role_permissions",
    array(
      'rp_key' => 'view_user'
    )
  );
  $cdbh->dbh->insert(
    DB_PREFIX . "role_permissions",
    array(
      'rp_key' => 'edit_user'
    )
  );
  // Roles
  $cdbh->dbh->insert(
    DB_PREFIX . "role_permissions",
    array(
      'rp_key' => 'add_user_role'
    )
  );
  $cdbh->dbh->insert(
    DB_PREFIX . "role_permissions",
    array(
      'rp_key' => 'view_user_role'
    )
  );
  $cdbh->dbh->insert(
    DB_PREFIX . "role_permissions",
    array(
      'rp_key' => 'edit_user_role'
    )
  );
  // Sections
  $cdbh->dbh->insert(
    DB_PREFIX . "role_permissions",
    array(
      'rp_key' => 'add_section'
    )
  );
  $cdbh->dbh->insert(
    DB_PREFIX . "role_permissions",
    array(
      'rp_key' => 'view_section'
    )
  );
  $cdbh->dbh->insert(
    DB_PREFIX . "role_permissions",
    array(
      'rp_key' => 'edit_section'
    )
  );
  // Menu
  $cdbh->dbh->insert(
    DB_PREFIX . "role_permissions",
    array(
      'rp_key' => 'view_menu'
    )
  );
  // FAQ
  $cdbh->dbh->insert(
    DB_PREFIX . "role_permissions",
    array(
      'rp_key' => 'add_faq'
    )
  );
  $cdbh->dbh->insert(
    DB_PREFIX . "role_permissions",
    array(
      'rp_key' => 'view_faq'
    )
  );
  $cdbh->dbh->insert(
    DB_PREFIX . "role_permissions",
    array(
      'rp_key' => 'edit_faq'
    )
  );
  // FAQ Section
  $cdbh->dbh->insert(
    DB_PREFIX . "role_permissions",
    array(
      'rp_key' => 'view_faq_section'
    )
  );
  // Page
  $cdbh->dbh->insert(
    DB_PREFIX . "role_permissions",
    array(
      'rp_key' => 'add_page'
    )
  );
  $cdbh->dbh->insert(
    DB_PREFIX . "role_permissions",
    array(
      'rp_key' => 'view_page'
    )
  );
  $cdbh->dbh->insert(
    DB_PREFIX . "role_permissions",
    array(
      'rp_key' => 'edit_page'
    )
  );

  // Add master admin user
  // Generate new key
  $newKey = get_crypto_key();
  // Hash password with new key
  $password_encrypted = password_hash($userPwd . $newKey, PASSWORD_DEFAULT);
  // Insert to table
  $cdbh->dbh->insert(
    DB_PREFIX . "users",
    array(
      'user_login' => $userLogin,
      'user_display_name' => ucfirst($userLogin),
      'user_password' => $password_encrypted,
      'user_password_key' => $newKey,
      'user_email' => $userEmail,
      'user_first_name' => ucfirst($userLogin),
      'user_role_id' => '1',
      'user_auth_rqd' => '0',
      'user_status' => '1',
      'user_created_dtm' => date('Y-m-d H:i:s')
    )
  );

  // Add dummy content to the content table
  $cdbh->dbh->insert(
    DB_PREFIX . "content",
    array(
      'content_title' => 'Hello World!',
      'content_content' => '<p>Welcome to <strong>Cornerstone</strong>. This is your first page. Edit or delete it, it\'s up to you. Enjoy creating!</p>',
      'content_status' => 1,
      'content_type' => 0,
      'content_sort_order' => 0,
      'content_show_updated' => 0,
      'content_added_id' => 1,
      'content_added_dtm' => date('Y-m-d H:i:s')
    )
  );
  $cdbh->dbh->insert(
    DB_PREFIX . "seo_url",
    array(
      'seo_type' => '0',
      'seo_type_id' => '1',
      'seo_keyword' => 'hello-world',
      'seo_primary' => '0'
    )
  );
  $cdbh->dbh->insert(
    DB_PREFIX . "content_meta",
    array(
      'cmeta_content_id' => '1',
      'cmeta_key' => 'meta_title',
      'cmeta_value' => '',
      'cmeta_added_id' => '1',
      'cmeta_added_dtm' => date('Y-m-d H:i:s')
    )
  );
  $cdbh->dbh->insert(
    DB_PREFIX . "content_meta",
    array(
      'cmeta_content_id' => '1',
      'cmeta_key' => 'meta_description',
      'cmeta_value' => '',
      'cmeta_added_id' => '1',
      'cmeta_added_dtm' => date('Y-m-d H:i:s')
    )
  );
} else {
  // The table already exists
  echo '<p class="error"><span>ERROR:</span> It looks like your database is already setup for using Cornerstone.</p><p><a href="' . $siteURL . '" class="csc-btn">View Site</a></p>';
  exit;
}
