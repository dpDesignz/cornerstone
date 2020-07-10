-- phpMyAdmin SQL Dump
-- version 4.8.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jun 00, 2020 at 00:00 AM
-- Server version: 5.6.41-84.1
-- PHP Version: 7.2.7
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
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
-- Dumping data for table `cs_role_permissions`
--
INSERT INTO `cs_role_permissions` (`rp_key`)
VALUES ('add_faq'),
  ('add_media'),
  ('add_page'),
  ('add_section'),
  ('add_user'),
  ('add_user_role'),
  ('archive_page'),
  ('delete_faq'),
  ('delete_media'),
  ('delete_section'),
  ('delete_user_role'),
  ('edit_addon_settings'),
  ('edit_core_settings'),
  ('edit_faq'),
  ('edit_mail_settings'),
  ('edit_media'),
  ('edit_page'),
  ('edit_section'),
  ('edit_security_settings'),
  ('edit_site_settings'),
  ('edit_user'),
  ('edit_user_role'),
  ('view_faq'),
  ('view_media'),
  ('view_page'),
  ('view_php_info'),
  ('view_section'),
  ('view_settings'),
  ('view_user'),
  ('view_user_role');
COMMIT;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */
;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */
;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */
;