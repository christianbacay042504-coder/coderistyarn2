-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 25, 2026 at 02:33 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `sjdm_tours`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin_activity`
--

CREATE TABLE `admin_activity` (
  `id` int(11) NOT NULL,
  `admin_id` int(11) NOT NULL,
  `action` varchar(100) NOT NULL,
  `module` varchar(50) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin_activity`
--

INSERT INTO `admin_activity` (`id`, `admin_id`, `action`, `module`, `description`, `ip_address`, `user_agent`, `created_at`) VALUES
(1, 1, 'LOGIN', 'auth', 'Super admin logged in', '192.168.1.1', NULL, '2026-02-05 00:00:00'),
(2, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '192.168.1.1', NULL, '2026-02-05 00:05:00'),
(3, 6, 'LOGIN', 'auth', 'Dashboard admin logged in', '192.168.1.2', NULL, '2026-02-05 01:00:00'),
(4, 6, 'CREATE', 'users', 'Created new user account', '192.168.1.2', NULL, '2026-02-05 01:30:00'),
(5, 7, 'LOGIN', 'auth', 'User admin logged in', '192.168.1.3', NULL, '2026-02-05 02:00:00'),
(6, 7, 'UPDATE', 'bookings', 'Updated booking status', '192.168.1.3', NULL, '2026-02-05 02:15:00'),
(7, 1, 'LOGOUT', 'auth', 'Super admin logged out', '192.168.1.1', NULL, '2026-02-05 09:00:00'),
(8, 6, 'LOGOUT', 'auth', 'Dashboard admin logged out', '192.168.1.2', NULL, '2026-02-05 09:30:00'),
(9, 8, 'LOGIN', 'auth', 'Content admin logged in', '192.168.1.4', NULL, '2026-02-05 03:00:00'),
(10, 8, 'ACCESS', 'content', 'Content admin accessed dashboard', '192.168.1.4', NULL, '2026-02-05 03:05:00'),
(11, 8, 'UPDATE', 'destinations', 'Updated destination content', '192.168.1.4', NULL, '2026-02-05 06:00:00'),
(12, 8, 'UPDATE', 'hotels', 'Updated hotel information', '192.168.1.4', NULL, '2026-02-05 07:30:00'),
(13, 1, 'LOGOUT', 'auth', 'Super admin logged out', '192.168.1.1', NULL, '2026-02-05 10:00:00'),
(14, 8, 'LOGOUT', 'auth', 'Content admin logged out', '192.168.1.4', NULL, '2026-02-05 10:30:00'),
(15, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-06 08:24:49'),
(16, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-06 09:38:48'),
(17, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-06 12:00:24'),
(18, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-06 12:00:42'),
(19, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-06 12:00:47'),
(20, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-06 12:04:31'),
(21, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-06 12:05:49'),
(22, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-06 12:05:58'),
(23, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-06 12:05:58'),
(24, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-06 12:05:59'),
(25, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-08 21:28:28'),
(26, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-08 21:36:11'),
(27, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-08 22:39:58'),
(28, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-08 22:48:46'),
(29, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-08 22:49:17'),
(30, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-08 22:50:01'),
(31, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-08 22:50:07'),
(32, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-08 22:51:02'),
(33, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-10 07:49:39'),
(34, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-10 07:52:10'),
(35, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-10 08:10:02'),
(36, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-10 08:12:19'),
(37, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-10 08:21:11'),
(38, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-10 12:47:41'),
(39, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-10 22:24:20'),
(40, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-10 22:25:59'),
(41, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-10 22:41:32'),
(42, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-10 22:43:47'),
(43, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-11 00:40:19'),
(44, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-11 00:48:37'),
(45, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-11 00:49:44'),
(46, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-11 00:55:57'),
(47, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-11 01:03:37'),
(48, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-11 01:04:52'),
(49, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-11 01:17:13'),
(50, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-11 01:21:49'),
(51, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-11 01:29:44'),
(52, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-11 01:40:04'),
(53, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-11 01:45:11'),
(54, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-11 01:45:14'),
(55, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-11 01:45:33'),
(56, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-11 02:01:23'),
(57, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-11 02:19:59'),
(58, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-11 02:39:32'),
(59, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-11 02:54:12'),
(60, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-11 02:55:21'),
(61, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-11 03:05:28'),
(62, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-11 03:06:03'),
(63, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-11 03:21:12'),
(64, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-15 14:28:05'),
(65, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-15 15:49:53'),
(66, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-15 15:50:05'),
(67, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-15 15:55:33'),
(68, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-15 18:54:19'),
(69, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-15 23:44:24'),
(70, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-15 23:44:28'),
(71, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-15 23:44:34'),
(72, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-15 23:45:41'),
(73, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-16 04:56:31'),
(74, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-16 05:08:34'),
(75, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-16 05:10:02'),
(76, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-16 05:10:02'),
(77, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-16 05:10:02'),
(78, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-16 05:10:02'),
(79, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-16 05:10:03'),
(80, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-16 05:10:03'),
(81, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-16 05:13:52'),
(82, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-16 07:03:59'),
(83, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-16 07:05:22'),
(84, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-16 07:44:50'),
(85, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-16 08:12:35'),
(86, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-16 08:12:52'),
(87, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-16 08:19:54'),
(88, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-16 08:21:54'),
(89, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-16 08:24:56'),
(90, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-19 08:58:15'),
(91, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-19 09:24:29'),
(92, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-19 09:31:26'),
(93, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-19 09:42:38'),
(94, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-19 09:46:12'),
(95, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-19 09:57:34'),
(96, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-19 09:57:47'),
(97, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-19 10:03:38'),
(98, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-19 10:05:10'),
(99, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-19 10:08:07'),
(100, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-19 11:54:58'),
(101, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-19 11:55:20'),
(102, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-19 12:33:43'),
(103, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-19 13:10:43'),
(104, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-19 13:14:25'),
(105, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-19 13:18:20'),
(106, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-19 13:30:10'),
(107, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-23 19:37:02'),
(108, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-23 19:37:14'),
(109, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-23 19:37:22'),
(110, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-23 20:17:08'),
(111, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-23 20:18:36'),
(112, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-23 20:21:28'),
(113, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-23 20:25:08'),
(114, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-23 20:26:04'),
(115, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-23 20:30:35'),
(116, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-23 20:30:36'),
(117, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-23 20:30:36'),
(118, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-23 20:30:36'),
(119, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-23 20:30:36'),
(120, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-23 20:30:37'),
(121, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-23 20:31:05'),
(122, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-23 20:39:49'),
(123, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-23 20:39:59'),
(124, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-23 20:40:12'),
(125, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-23 20:40:36'),
(126, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-23 20:40:50'),
(127, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-23 20:45:37'),
(128, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-23 20:45:53'),
(129, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-23 20:46:05'),
(130, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-23 20:52:30'),
(131, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-23 20:52:46'),
(132, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-23 20:53:01'),
(133, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-23 20:59:37'),
(134, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-23 20:59:51'),
(135, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-23 21:00:13'),
(136, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-23 21:01:13'),
(137, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-23 21:01:24'),
(138, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-23 21:02:48'),
(139, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-23 21:03:12'),
(140, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-23 21:04:35'),
(141, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-23 21:04:46'),
(142, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-23 21:08:21'),
(143, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-23 21:08:30'),
(144, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-23 21:13:11'),
(145, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-23 21:13:28'),
(146, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-23 21:18:42'),
(147, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-23 21:22:01'),
(148, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-23 21:22:02'),
(149, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-23 21:22:02'),
(150, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-23 21:22:02'),
(151, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-23 21:22:13'),
(152, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-23 21:32:43'),
(153, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-24 01:36:57'),
(154, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-24 02:41:45'),
(155, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-24 04:13:47');

-- --------------------------------------------------------

--
-- Table structure for table `admin_dashboards`
--

CREATE TABLE `admin_dashboards` (
  `id` int(11) NOT NULL,
  `admin_id` int(11) NOT NULL,
  `dashboard_name` varchar(100) NOT NULL,
  `dashboard_layout` text DEFAULT NULL,
  `widgets_config` text DEFAULT NULL,
  `theme_settings` text DEFAULT NULL,
  `is_default` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin_dashboards`
--

INSERT INTO `admin_dashboards` (`id`, `admin_id`, `dashboard_name`, `dashboard_layout`, `widgets_config`, `theme_settings`, `is_default`, `created_at`, `updated_at`) VALUES
(1, 1, 'Main Dashboard', '{\"layout\": \"grid\", \"columns\": 3}', '{\"widgets\": [\"users\", \"bookings\", \"revenue\", \"guides\", \"destinations\", \"hotels\"]}', NULL, 1, '2026-02-06 08:07:12', '2026-02-06 08:07:12'),
(2, 1, 'Analytics Dashboard', '{\"layout\": \"charts\", \"columns\": 2}', '{\"widgets\": [\"user_growth\", \"booking_trends\", \"revenue_chart\", \"popular_destinations\"]}', NULL, 0, '2026-02-06 08:07:12', '2026-02-06 08:07:12'),
(3, 1, 'Quick Overview', '{\"layout\": \"compact\", \"columns\": 4}', '{\"widgets\": [\"total_users\", \"today_bookings\", \"pending_tasks\", \"system_status\"]}', NULL, 0, '2026-02-06 08:07:12', '2026-02-06 08:07:12');

-- --------------------------------------------------------

--
-- Table structure for table `admin_dashboard_settings`
--

CREATE TABLE `admin_dashboard_settings` (
  `id` int(11) NOT NULL,
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text DEFAULT NULL,
  `setting_type` enum('text','number','boolean') DEFAULT 'text',
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin_dashboard_settings`
--

INSERT INTO `admin_dashboard_settings` (`id`, `setting_key`, `setting_value`, `setting_type`, `description`, `created_at`, `updated_at`) VALUES
(1, 'page_title', 'Dashboard Overview', 'text', 'Main dashboard page title', '2026-02-06 08:07:12', '2026-02-06 08:07:12'),
(2, 'page_subtitle', 'System statistics and analytics', 'text', 'Dashboard page subtitle', '2026-02-06 08:07:12', '2026-02-06 08:07:12'),
(3, 'admin_logo_text', 'SJDM ADMIN', 'text', 'Admin panel logo text', '2026-02-06 08:07:12', '2026-02-06 08:07:12'),
(4, 'show_user_badges', '1', 'boolean', 'Show user count badges in sidebar', '2026-02-06 08:07:12', '2026-02-06 08:07:12'),
(5, 'refresh_interval', '30', 'number', 'Dashboard auto-refresh interval in seconds', '2026-02-06 08:07:12', '2026-02-06 08:07:12');

-- --------------------------------------------------------

--
-- Table structure for table `admin_menu_items`
--

CREATE TABLE `admin_menu_items` (
  `id` int(11) NOT NULL,
  `menu_name` varchar(100) NOT NULL,
  `menu_icon` varchar(50) DEFAULT NULL,
  `menu_url` varchar(200) NOT NULL,
  `display_order` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `parent_id` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin_menu_items`
--

INSERT INTO `admin_menu_items` (`id`, `menu_name`, `menu_icon`, `menu_url`, `display_order`, `is_active`, `parent_id`, `created_at`, `updated_at`) VALUES
(1, 'Dashboard', 'dashboard', 'dashboard.php', 1, 1, 0, '2026-02-06 08:07:12', '2026-02-06 08:07:12'),
(2, 'User Management', 'people', 'user-management.php', 2, 1, 0, '2026-02-06 08:07:12', '2026-02-06 08:07:12'),
(3, 'Tour Guides', 'tour', 'tour-guides.php', 3, 1, 0, '2026-02-06 08:07:12', '2026-02-06 08:07:12'),
(4, 'Destinations', 'place', 'destinations.php', 4, 1, 0, '2026-02-06 08:07:12', '2026-02-06 08:07:12'),
(5, 'Hotels', 'hotel', 'hotels.php', 5, 1, 0, '2026-02-06 08:07:12', '2026-02-06 08:07:12'),
(6, 'Bookings', 'event', 'bookings.php', 6, 1, 0, '2026-02-06 08:07:12', '2026-02-06 08:07:12'),
(7, 'Analytics', 'analytics', 'analytics.php', 7, 1, 0, '2026-02-06 08:07:12', '2026-02-06 08:07:12'),
(8, 'Reports', 'description', 'reports.php', 8, 1, 0, '2026-02-06 08:07:12', '2026-02-06 08:07:12'),
(9, 'Settings', 'settings', 'settings.php', 9, 1, 0, '2026-02-06 08:07:12', '2026-02-06 08:07:12');

-- --------------------------------------------------------

--
-- Table structure for table `admin_permissions_table`
--

CREATE TABLE `admin_permissions_table` (
  `id` int(11) NOT NULL,
  `admin_id` int(11) NOT NULL,
  `module` varchar(50) NOT NULL,
  `permission_type` enum('read','write','delete','admin') NOT NULL,
  `granted_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `granted_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin_permissions_table`
--

INSERT INTO `admin_permissions_table` (`id`, `admin_id`, `module`, `permission_type`, `granted_at`, `granted_by`) VALUES
(1, 1, 'dashboard', 'admin', '2026-02-06 08:07:12', NULL),
(2, 1, 'users', 'admin', '2026-02-06 08:07:12', NULL),
(3, 1, 'bookings', 'admin', '2026-02-06 08:07:12', NULL),
(4, 1, 'guides', 'admin', '2026-02-06 08:07:12', NULL),
(5, 1, 'destinations', 'admin', '2026-02-06 08:07:12', NULL),
(6, 1, 'hotels', 'admin', '2026-02-06 08:07:12', NULL),
(7, 1, 'analytics', 'admin', '2026-02-06 08:07:12', NULL),
(8, 1, 'reports', 'admin', '2026-02-06 08:07:12', NULL),
(9, 1, 'settings', 'admin', '2026-02-06 08:07:12', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `admin_preferences`
--

CREATE TABLE `admin_preferences` (
  `id` int(11) NOT NULL,
  `admin_id` int(11) NOT NULL,
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text DEFAULT NULL,
  `setting_type` enum('text','number','boolean','json') DEFAULT 'text',
  `category` varchar(50) DEFAULT 'general',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin_preferences`
--

INSERT INTO `admin_preferences` (`id`, `admin_id`, `setting_key`, `setting_value`, `setting_type`, `category`, `created_at`, `updated_at`) VALUES
(1, 1, 'theme', 'light', 'text', 'appearance', '2026-02-06 08:07:12', '2026-02-06 08:07:12'),
(2, 1, 'language', 'en', 'text', 'appearance', '2026-02-06 08:07:12', '2026-02-06 08:07:12'),
(3, 1, 'notifications_enabled', '1', 'boolean', 'notifications', '2026-02-06 08:07:12', '2026-02-06 08:07:12'),
(4, 1, 'auto_refresh_interval', '30', 'number', 'performance', '2026-02-06 08:07:12', '2026-02-06 08:07:12'),
(5, 1, 'items_per_page', '15', 'number', 'performance', '2026-02-06 08:07:12', '2026-02-06 08:07:12'),
(6, 1, 'default_dashboard', 'Main Dashboard', 'text', 'general', '2026-02-06 08:07:12', '2026-02-06 08:07:12'),
(7, 1, 'show_tooltips', '1', 'boolean', 'ui', '2026-02-06 08:07:12', '2026-02-06 08:07:12'),
(8, 1, 'compact_mode', '0', 'boolean', 'ui', '2026-02-06 08:07:12', '2026-02-06 08:07:12'),
(9, 1, 'admin_mark_display', 'badge', 'text', 'appearance', '2026-02-06 08:07:12', '2026-02-06 08:07:12'),
(10, 1, 'last_login_notification', '1', 'boolean', 'security', '2026-02-06 08:07:12', '2026-02-06 08:07:12');

-- --------------------------------------------------------

--
-- Table structure for table `admin_users`
--

CREATE TABLE `admin_users` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `admin_mark` varchar(10) DEFAULT 'A',
  `role_title` varchar(100) DEFAULT 'Administrator',
  `permissions` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin_users`
--

INSERT INTO `admin_users` (`id`, `user_id`, `admin_mark`, `role_title`, `permissions`, `created_at`, `updated_at`) VALUES
(1, 1, 'ADMIN', 'Super Administrator', 'all', '2026-02-06 08:07:12', '2026-02-06 08:07:12'),
(6, 6, 'ADMIN_DASH', 'Dashboard Administrator', 'dashboard,analytics,reports', '2026-02-06 08:07:12', '2026-02-06 08:07:12'),
(7, 7, 'ADMIN_USER', 'User Administrator', 'users,bookings,guides', '2026-02-06 08:07:12', '2026-02-06 08:07:12'),
(8, 8, 'ADMIN_CONT', 'Content Administrator', 'destinations,hotels,content', '2026-02-06 08:07:12', '2026-02-06 08:07:12');

-- --------------------------------------------------------

--
-- Table structure for table `analytics_settings`
--

CREATE TABLE `analytics_settings` (
  `id` int(11) NOT NULL,
  `setting_key` varchar(50) NOT NULL,
  `setting_value` text NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `analytics_settings`
--

INSERT INTO `analytics_settings` (`id`, `setting_key`, `setting_value`, `updated_at`) VALUES
(1, 'module_title', 'Analytics Dashboard', '2026-02-06 08:07:12'),
(2, 'module_subtitle', 'System analytics and insights', '2026-02-06 08:07:12'),
(3, 'default_analytics_limit', '15', '2026-02-06 08:07:12'),
(4, 'admin_mark_label', 'AN', '2026-02-06 08:07:12');

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `guide_id` int(11) DEFAULT NULL,
  `tour_name` varchar(200) NOT NULL,
  `destination` varchar(200) DEFAULT NULL,
  `booking_date` date NOT NULL,
  `check_in_date` date DEFAULT NULL,
  `check_out_date` date DEFAULT NULL,
  `number_of_people` int(11) NOT NULL,
  `contact_number` varchar(50) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `special_requests` text DEFAULT NULL,
  `rejection_notes` text DEFAULT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `payment_method` enum('pay_later','gcash','bank_transfer') DEFAULT 'pay_later',
  `status` enum('pending','confirmed','cancelled','completed') DEFAULT 'pending',
  `booking_reference` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`id`, `user_id`, `guide_id`, `tour_name`, `destination`, `booking_date`, `check_in_date`, `check_out_date`, `number_of_people`, `contact_number`, `email`, `special_requests`, `rejection_notes`, `total_amount`, `payment_method`, `status`, `booking_reference`, `created_at`, `updated_at`) VALUES
(47, 40, 17, 'Tungtong Falls', 'Tungtong Falls', '2026-02-27', NULL, NULL, 13, '09705667137', 'jeanmarcaguilar829@gmail.com', 'Special', NULL, 4100.00, 'pay_later', 'pending', 'SJDM-71953583', '2026-02-24 17:19:43', '2026-02-24 17:19:43');

-- --------------------------------------------------------

--
-- Table structure for table `booking_settings`
--

CREATE TABLE `booking_settings` (
  `id` int(11) NOT NULL,
  `setting_key` varchar(50) NOT NULL,
  `setting_value` text NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `booking_settings`
--

INSERT INTO `booking_settings` (`id`, `setting_key`, `setting_value`, `updated_at`) VALUES
(1, 'module_title', 'Bookings Management', '2026-02-06 08:07:12'),
(2, 'module_subtitle', 'Manage tour bookings and reservations', '2026-02-06 08:07:12'),
(3, 'default_booking_limit', '15', '2026-02-06 08:07:12'),
(4, 'admin_mark_label', 'BK', '2026-02-06 08:07:12');

-- --------------------------------------------------------

--
-- Table structure for table `destination_settings`
--

CREATE TABLE `destination_settings` (
  `id` int(11) NOT NULL,
  `setting_key` varchar(50) NOT NULL,
  `setting_value` text NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `destination_settings`
--

INSERT INTO `destination_settings` (`id`, `setting_key`, `setting_value`, `updated_at`) VALUES
(1, 'module_title', 'Destinations Management', '2026-02-06 08:07:12'),
(2, 'module_subtitle', 'Manage tourist destinations', '2026-02-06 08:07:12'),
(3, 'default_destination_limit', '15', '2026-02-06 08:07:12'),
(4, 'admin_mark_label', 'DS', '2026-02-06 08:07:12');

-- --------------------------------------------------------

--
-- Table structure for table `guide_destinations`
--

CREATE TABLE `guide_destinations` (
  `id` int(11) NOT NULL,
  `guide_id` int(11) NOT NULL,
  `destination_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `homepage_content`
--

CREATE TABLE `homepage_content` (
  `id` int(11) NOT NULL,
  `content_type` varchar(50) NOT NULL COMMENT 'hero, features, about, etc.',
  `content_key` varchar(100) NOT NULL COMMENT 'title, subtitle, description, image, etc.',
  `content_value` text NOT NULL,
  `display_order` int(11) NOT NULL DEFAULT 0,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `homepage_content`
--

INSERT INTO `homepage_content` (`id`, `content_type`, `content_key`, `content_value`, `display_order`, `status`, `created_at`, `updated_at`) VALUES
(1, 'hero', 'title', 'Welcome to SJDM Tours', 1, 'active', '2026-02-06 08:07:13', '2026-02-06 08:07:13'),
(2, 'hero', 'subtitle', 'Discover the Beauty of San Jose del Monte', 2, 'active', '2026-02-06 08:07:13', '2026-02-06 08:07:13'),
(3, 'hero', 'description', 'Experience the best tourist spots and activities in San Jose del Monte with our expert tour guides.', 3, 'active', '2026-02-06 08:07:13', '2026-02-06 08:07:13'),
(4, 'hero', 'background_image', '/images/hero-bg.jpg', 4, 'active', '2026-02-06 08:07:13', '2026-02-06 08:07:13'),
(5, 'features', 'title1', 'Expert Tour Guides', 1, 'active', '2026-02-06 08:07:13', '2026-02-06 08:07:13'),
(6, 'features', 'description1', 'Professional and knowledgeable local guides to make your tour memorable.', 2, 'active', '2026-02-06 08:07:13', '2026-02-06 08:07:13'),
(7, 'features', 'icon1', 'people', 3, 'active', '2026-02-06 08:07:13', '2026-02-06 08:07:13'),
(8, 'features', 'title2', 'Best Destinations', 4, 'active', '2026-02-06 08:07:13', '2026-02-06 08:07:13'),
(9, 'features', 'description2', 'Carefully selected tourist spots showcasing the best of SJDM.', 5, 'active', '2026-02-06 08:07:13', '2026-02-06 08:07:13'),
(10, 'features', 'icon2', 'place', 6, 'active', '2026-02-06 08:07:13', '2026-02-06 08:07:13'),
(11, 'features', 'title3', 'Easy Booking', 7, 'active', '2026-02-06 08:07:13', '2026-02-06 08:07:13'),
(12, 'features', 'description3', 'Simple and secure online booking system for your convenience.', 8, 'active', '2026-02-06 08:07:13', '2026-02-06 08:07:13'),
(13, 'features', 'icon3', 'event', 9, 'active', '2026-02-06 08:07:13', '2026-02-06 08:07:13'),
(14, 'about', 'title', 'About SJDM Tours', 1, 'active', '2026-02-06 08:07:13', '2026-02-06 08:07:13'),
(15, 'about', 'description', 'We are your premier tour service provider in San Jose del Monte, offering unforgettable experiences and expert guidance to the most beautiful destinations in the city.', 2, 'active', '2026-02-06 08:07:13', '2026-02-06 08:07:13'),
(16, 'about', 'mission', 'To provide exceptional tour experiences that showcase the natural beauty and cultural heritage of San Jose del Monte.', 3, 'active', '2026-02-06 08:07:13', '2026-02-06 08:07:13'),
(17, 'about', 'vision', 'To become the leading tour service provider in the region, known for quality, reliability, and customer satisfaction.', 4, 'active', '2026-02-06 08:07:13', '2026-02-06 08:07:13');

-- --------------------------------------------------------

--
-- Table structure for table `hotels`
--

CREATE TABLE `hotels` (
  `id` int(11) NOT NULL,
  `name` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `category` enum('luxury','mid-range','budget','event') NOT NULL,
  `location` varchar(200) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `contact_info` varchar(200) DEFAULT NULL,
  `website` varchar(200) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `price_range` varchar(100) DEFAULT NULL,
  `rating` decimal(3,2) DEFAULT 0.00,
  `review_count` int(11) DEFAULT 0,
  `amenities` text DEFAULT NULL,
  `services` text DEFAULT NULL,
  `image_url` varchar(500) DEFAULT NULL,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `hotels`
--

INSERT INTO `hotels` (`id`, `name`, `description`, `category`, `location`, `address`, `contact_info`, `website`, `email`, `phone`, `price_range`, `rating`, `review_count`, `amenities`, `services`, `image_url`, `latitude`, `longitude`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Mountain View Hotel', 'Modern hotel with stunning mountain views and excellent amenities. Perfect for both business and leisure travelers.', '', 'San Jose del Monte, Bulacan', NULL, NULL, NULL, NULL, NULL, '2000-3500', 4.20, 0, NULL, NULL, '/images/hotel-mountain-view.jpg', NULL, NULL, 'active', '2026-01-05 00:00:00', '2026-01-05 00:00:00'),
(2, 'SJDM Grand Hotel', 'Luxurious hotel in the heart of the city. Offers world-class service and facilities.', 'luxury', 'San Jose del Monte, Bulacan', NULL, NULL, NULL, NULL, NULL, '4000-6000', 4.70, 0, NULL, NULL, '/images/hotel-grand.jpg', NULL, NULL, 'active', '2026-01-06 01:00:00', '2026-01-06 01:00:00'),
(3, 'Budget Stay Inn', 'Affordable accommodation with clean rooms and friendly service. Great value for money.', 'budget', 'San Jose del Monte, Bulacan', NULL, NULL, NULL, NULL, NULL, '800-1500', 3.80, 0, NULL, NULL, '/images/hotel-budget.jpg', NULL, NULL, 'active', '2026-01-07 02:00:00', '2026-01-07 02:00:00'),
(4, 'Nature Resort Hotel', 'Eco-friendly resort surrounded by lush greenery. Offers spa services and nature activities.', '', 'San Jose del Monte, Bulacan', NULL, NULL, NULL, NULL, NULL, '2500-4000', 4.50, 0, NULL, NULL, '/images/hotel-nature.jpg', NULL, NULL, 'active', '2026-01-08 03:00:00', '2026-01-08 03:00:00'),
(5, 'City Center Hotel', 'Conveniently located hotel near major attractions and business district.', '', 'San Jose del Monte, Bulacan', NULL, NULL, NULL, NULL, NULL, '1800-3000', 4.10, 0, NULL, NULL, '/images/hotel-city-center.jpg', NULL, NULL, 'active', '2026-01-09 04:00:00', '2026-01-09 04:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `hotel_settings`
--

CREATE TABLE `hotel_settings` (
  `id` int(11) NOT NULL,
  `setting_key` varchar(50) NOT NULL,
  `setting_value` text NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `hotel_settings`
--

INSERT INTO `hotel_settings` (`id`, `setting_key`, `setting_value`, `updated_at`) VALUES
(1, 'module_title', 'Hotels Management', '2026-02-06 08:07:12'),
(2, 'module_subtitle', 'Manage hotel accommodations', '2026-02-06 08:07:12'),
(3, 'default_hotel_limit', '15', '2026-02-06 08:07:12'),
(4, 'admin_mark_label', 'HT', '2026-02-06 08:07:12');

-- --------------------------------------------------------

--
-- Table structure for table `local_culture`
--

CREATE TABLE `local_culture` (
  `id` int(11) NOT NULL,
  `icon` varchar(50) DEFAULT NULL,
  `title` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `display_order` int(11) DEFAULT 0,
  `is_active` enum('yes','no') DEFAULT 'yes',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `local_culture`
--

INSERT INTO `local_culture` (`id`, `icon`, `title`, `description`, `display_order`, `is_active`, `created_at`, `updated_at`) VALUES
(1, '🎭', 'Traditional Festivals', 'Kakaning Bulacan Festival - Annual celebration of local delicacies\nPanagbenga-inspired floral parades\nReligious processions during Holy Week\nChristmas traditions and Simbang Gabi', 1, 'yes', '2026-01-01 00:00:00', '2026-01-01 00:00:00'),
(2, '🍽️', 'Local Cuisine', 'Bulacan specialties like Lechon Kawali and Chicharon\nNative rice cakes (kakanin) such as Puto and Kutsinta\nFresh water fish dishes from local rivers\nTraditional Filipino breakfast combinations', 2, 'yes', '2026-01-01 00:00:00', '2026-01-01 00:00:00'),
(3, '🎨', 'Arts and Crafts', 'Traditional weaving and basket making\nLocal pottery and ceramic arts\nFolk painting and sculpture\nIndigenous musical instruments', 3, 'yes', '2026-01-01 00:00:00', '2026-01-01 00:00:00'),
(4, '🏛️', 'Historical Heritage', 'Spanish colonial architecture\nHistorical churches and ancestral houses\nLocal museums and cultural centers\nRevolutionary war memorials', 4, 'yes', '2026-01-01 00:00:00', '2026-01-01 00:00:00'),
(5, '🎵', 'Music and Dance', 'Traditional folk dances\nLocal musical performances\nCommunity choirs and bands\nModern contemporary arts scene', 5, 'yes', '2026-01-01 00:00:00', '2026-01-01 00:00:00'),
(6, '👥', 'Community Life', 'Bayanihan spirit and neighborly cooperation\nLocal market traditions and trade\nFamily gatherings and celebrations\nCommunity sports and recreation', 6, 'yes', '2026-01-01 00:00:00', '2026-01-01 00:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `login_activity`
--

CREATE TABLE `login_activity` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `login_time` timestamp NOT NULL DEFAULT current_timestamp(),
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` varchar(255) DEFAULT NULL,
  `status` enum('success','failed') DEFAULT 'success'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `login_activity`
--

INSERT INTO `login_activity` (`id`, `user_id`, `login_time`, `ip_address`, `user_agent`, `status`) VALUES
(0, 4, '2026-02-06 08:23:56', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 'success'),
(1, 4, '2026-02-05 01:00:00', '192.168.1.100', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36', 'success'),
(2, 5, '2026-02-05 02:30:00', '192.168.1.101', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36', 'success'),
(3, 1, '2026-02-05 03:15:00', '192.168.1.102', 'Mozilla/5.0 (iPhone; CPU iPhone OS 14_7_1 like Mac OS X) AppleWebKit/605.1.15', 'success'),
(4, 4, '2026-02-05 06:20:00', '192.168.1.100', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36', 'success'),
(5, 5, '2026-02-05 08:45:00', '192.168.1.101', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36', 'success'),
(6, 1, '2026-02-04 00:00:00', '192.168.1.102', 'Mozilla/5.0 (iPhone; CPU iPhone OS 14_7_1 like Mac OS X) AppleWebKit/605.1.15', 'success'),
(7, 4, '2026-02-04 07:30:00', '192.168.1.100', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36', 'failed'),
(8, 5, '2026-02-03 04:00:00', '192.168.1.101', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36', 'success'),
(9, 11, '2026-02-08 19:39:07', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 'failed'),
(10, 11, '2026-02-08 19:39:20', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 'failed'),
(11, 12, '2026-02-08 19:40:20', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 'failed'),
(12, 11, '2026-02-08 19:40:47', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 'failed'),
(13, 11, '2026-02-08 19:41:14', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 'failed'),
(14, 11, '2026-02-08 19:43:15', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 'failed'),
(15, 11, '2026-02-08 19:43:33', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 'failed'),
(16, 11, '2026-02-08 19:57:18', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 'failed'),
(17, 11, '2026-02-08 20:12:09', 'Unknown', 'Unknown', 'failed'),
(18, 11, '2026-02-08 20:13:13', 'Unknown', 'Unknown', 'failed'),
(19, 11, '2026-02-08 20:15:54', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 'success'),
(20, 11, '2026-02-08 20:17:09', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', ''),
(21, 11, '2026-02-08 20:24:06', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 'success'),
(22, 11, '2026-02-08 20:24:40', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', ''),
(23, 11, '2026-02-08 21:03:16', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 'success'),
(24, 11, '2026-02-08 21:20:58', 'Unknown', 'Unknown', 'success'),
(25, 11, '2026-02-08 21:22:45', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', ''),
(26, 11, '2026-02-08 21:23:19', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 'success'),
(27, 11, '2026-02-08 21:23:41', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', ''),
(28, 13, '2026-02-08 21:25:25', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 'success'),
(29, 1, '2026-02-08 21:28:27', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 'success'),
(30, 4, '2026-02-08 21:35:16', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 'success'),
(31, 1, '2026-02-08 21:36:09', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 'success'),
(32, 13, '2026-02-08 21:43:24', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 'success'),
(33, 1, '2026-02-08 22:39:57', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 'success'),
(34, 13, '2026-02-08 22:42:04', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 'success'),
(35, 1, '2026-02-08 22:48:45', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 'success'),
(36, 13, '2026-02-08 22:49:33', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 'success'),
(37, 1, '2026-02-08 22:50:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 'success'),
(38, 13, '2026-02-08 22:50:22', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 'success'),
(39, 1, '2026-02-08 22:51:01', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 'success'),
(40, 13, '2026-02-08 22:51:12', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 'success'),
(41, 11, '2026-02-08 22:52:10', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 'success'),
(42, 11, '2026-02-08 22:53:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', ''),
(43, 13, '2026-02-08 22:53:12', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 'success'),
(44, 13, '2026-02-09 01:16:10', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 'success'),
(45, 11, '2026-02-09 02:00:37', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 'success'),
(46, 11, '2026-02-09 02:03:01', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 'success'),
(47, 11, '2026-02-09 02:03:54', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', ''),
(48, 11, '2026-02-09 02:04:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 'success'),
(49, 11, '2026-02-09 02:04:21', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', ''),
(50, 11, '2026-02-09 02:04:30', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 'success'),
(51, 11, '2026-02-09 02:05:14', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', ''),
(52, 11, '2026-02-09 02:05:25', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 'success'),
(53, 11, '2026-02-09 02:08:40', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', ''),
(54, 11, '2026-02-09 02:09:01', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 'success'),
(55, 11, '2026-02-09 02:21:12', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', ''),
(56, 11, '2026-02-09 02:22:19', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 'success'),
(57, 11, '2026-02-09 02:23:24', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', ''),
(58, 11, '2026-02-09 02:23:31', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 'failed'),
(59, 11, '2026-02-09 02:23:35', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 'success'),
(60, 11, '2026-02-09 02:29:53', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', ''),
(61, 11, '2026-02-09 02:29:59', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 'success'),
(62, 11, '2026-02-09 02:31:08', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', ''),
(63, 11, '2026-02-09 02:31:16', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 'success'),
(64, 11, '2026-02-09 02:31:45', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', ''),
(65, 11, '2026-02-09 02:31:52', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 'success'),
(66, 11, '2026-02-09 02:32:49', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', ''),
(67, 11, '2026-02-09 02:33:03', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 'success'),
(68, 11, '2026-02-09 02:33:53', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', ''),
(69, 11, '2026-02-09 02:34:05', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 'success'),
(70, 11, '2026-02-09 02:35:07', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', ''),
(71, 11, '2026-02-09 02:35:33', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 'success'),
(72, 11, '2026-02-09 02:37:56', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', ''),
(73, 11, '2026-02-09 02:39:01', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 'success'),
(74, 11, '2026-02-09 02:41:51', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', ''),
(75, 11, '2026-02-09 02:42:47', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 'success'),
(76, 11, '2026-02-09 02:43:32', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', ''),
(77, 11, '2026-02-09 02:43:43', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 'success'),
(78, 11, '2026-02-09 02:44:04', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', ''),
(79, 11, '2026-02-09 02:44:10', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 'success'),
(80, 11, '2026-02-09 02:44:25', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', ''),
(81, 11, '2026-02-09 02:44:32', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 'success'),
(82, 11, '2026-02-09 02:46:57', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', ''),
(83, 11, '2026-02-09 02:46:57', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', ''),
(84, 11, '2026-02-09 02:47:31', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 'success'),
(85, 11, '2026-02-09 02:47:37', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', ''),
(86, 11, '2026-02-09 02:48:05', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 'success'),
(87, 13, '2026-02-09 05:50:10', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 'success'),
(88, 13, '2026-02-10 06:07:32', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 'success'),
(89, 12, '2026-02-10 06:11:01', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 'success'),
(90, 12, '2026-02-10 06:15:34', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', ''),
(91, 13, '2026-02-10 06:15:46', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 'success'),
(92, 14, '2026-02-10 06:19:09', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 'failed'),
(93, 14, '2026-02-10 06:19:19', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 'success'),
(94, 15, '2026-02-10 06:22:55', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 'success'),
(95, 12, '2026-02-10 06:37:59', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 'success'),
(96, 12, '2026-02-10 06:38:18', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', ''),
(97, 11, '2026-02-10 06:38:35', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 'success'),
(98, 11, '2026-02-10 07:39:11', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 'success'),
(99, 1, '2026-02-10 07:49:38', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 'success'),
(100, 13, '2026-02-10 08:13:03', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 'success'),
(101, 1, '2026-02-10 08:21:10', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 'success'),
(102, 1, '2026-02-10 12:47:35', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 'failed'),
(103, 1, '2026-02-10 12:47:40', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 'success'),
(104, 13, '2026-02-10 13:12:47', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36 Edg/144.0.0.0', 'success'),
(105, 13, '2026-02-10 15:43:54', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 'success'),
(106, 13, '2026-02-10 18:37:29', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 'success'),
(107, 4, '2026-02-10 18:38:24', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 'success'),
(108, 13, '2026-02-10 21:36:48', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 'success'),
(109, 1, '2026-02-10 22:24:19', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 'success'),
(110, 1, '2026-02-10 22:41:31', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 'success'),
(111, 13, '2026-02-10 22:56:43', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 'success'),
(112, 13, '2026-02-10 23:13:41', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 'success'),
(113, 13, '2026-02-10 23:14:52', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 'success'),
(114, 1, '2026-02-11 00:40:18', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 'success'),
(115, 1, '2026-02-11 00:49:43', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 'success'),
(116, 1, '2026-02-11 00:55:56', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 'success'),
(117, 13, '2026-02-11 01:03:01', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 'success'),
(118, 1, '2026-02-11 01:03:36', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 'success'),
(119, 1, '2026-02-11 01:04:50', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 'success'),
(120, 1, '2026-02-11 01:17:12', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 'success'),
(121, 1, '2026-02-11 01:21:45', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 'failed'),
(122, 1, '2026-02-11 01:21:48', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 'success'),
(123, 1, '2026-02-11 01:29:43', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 'success'),
(124, 1, '2026-02-11 01:40:02', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 'success'),
(125, 1, '2026-02-11 01:45:10', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 'success'),
(126, 1, '2026-02-11 01:45:32', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 'success'),
(127, 13, '2026-02-11 01:45:56', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 'success'),
(128, 13, '2026-02-11 01:53:14', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 'success'),
(129, 13, '2026-02-11 01:55:03', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 'success'),
(130, 13, '2026-02-11 01:55:33', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 'success'),
(131, 13, '2026-02-11 01:55:54', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 'success'),
(132, 13, '2026-02-11 01:56:44', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 'success'),
(133, 1, '2026-02-11 02:01:22', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 'success'),
(134, 1, '2026-02-11 02:19:58', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 'success'),
(135, 13, '2026-02-11 02:30:48', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 'failed'),
(136, 13, '2026-02-11 02:30:52', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 'success'),
(137, 1, '2026-02-11 02:39:31', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 'success'),
(138, 13, '2026-02-11 02:40:03', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 'success'),
(139, 1, '2026-02-11 02:54:11', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 'success'),
(140, 13, '2026-02-11 02:54:42', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 'success'),
(141, 1, '2026-02-11 02:55:20', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 'success'),
(142, 13, '2026-02-11 03:03:42', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 'success'),
(143, 1, '2026-02-11 03:05:27', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 'success'),
(144, 1, '2026-02-11 03:21:11', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 'success'),
(145, 13, '2026-02-11 03:21:34', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 'success'),
(146, 4, '2026-02-15 02:02:35', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'success'),
(147, 4, '2026-02-15 02:10:58', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'success'),
(148, 4, '2026-02-15 12:31:11', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 'success'),
(156, 4, '2026-02-15 12:51:10', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 'success'),
(161, 1, '2026-02-15 14:27:45', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36 Edg/144.0.0.0', 'success'),
(165, 1, '2026-02-15 15:55:20', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36 Edg/144.0.0.0', 'success'),
(167, 1, '2026-02-15 18:54:01', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36 Edg/144.0.0.0', 'success'),
(168, 4, '2026-02-15 19:36:55', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 'success'),
(173, 33, '2026-02-15 20:06:50', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', ''),
(174, 4, '2026-02-15 20:08:48', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 'success'),
(175, 1, '2026-02-15 23:44:07', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 'success'),
(176, 1, '2026-02-16 05:13:39', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'success'),
(177, 1, '2026-02-19 08:45:56', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'success'),
(178, 1, '2026-02-19 08:57:48', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'success'),
(179, 1, '2026-02-19 09:14:14', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'failed'),
(180, 1, '2026-02-19 09:14:19', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'success'),
(181, 1, '2026-02-19 09:24:15', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'success'),
(184, 1, '2026-02-19 09:28:37', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'success'),
(185, 1, '2026-02-19 09:30:59', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'success'),
(186, 1, '2026-02-19 09:42:20', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'success'),
(189, 1, '2026-02-19 09:45:57', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'success'),
(190, 1, '2026-02-19 13:17:35', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'success'),
(191, 40, '2026-02-19 13:24:56', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'success'),
(192, 40, '2026-02-19 13:25:33', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'success'),
(193, 1, '2026-02-19 13:29:54', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'success'),
(195, 40, '2026-02-23 17:35:02', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'success'),
(196, 40, '2026-02-23 18:44:38', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'success'),
(197, 1, '2026-02-23 19:36:57', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'success'),
(198, 1, '2026-02-23 19:37:09', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'success'),
(199, 1, '2026-02-23 19:37:18', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'success'),
(200, 1, '2026-02-23 20:17:04', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'success'),
(201, 1, '2026-02-23 20:18:32', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'success'),
(202, 8, '2026-02-23 20:20:01', 'Unknown', 'Unknown', 'failed'),
(203, 1, '2026-02-23 20:21:24', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'success'),
(204, 8, '2026-02-23 20:23:30', 'Unknown', 'Unknown', 'failed'),
(205, 1, '2026-02-23 20:25:04', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'success'),
(206, 40, '2026-02-23 20:25:32', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'success'),
(207, 1, '2026-02-23 20:26:00', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'success'),
(208, 1, '2026-02-23 20:27:28', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'success'),
(209, 1, '2026-02-23 20:30:42', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'success'),
(210, 1, '2026-02-23 20:31:10', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'success'),
(211, 1, '2026-02-23 20:39:45', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'success'),
(212, 1, '2026-02-23 20:39:55', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'success'),
(213, 1, '2026-02-23 20:40:08', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'success'),
(214, 1, '2026-02-23 20:40:32', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'success'),
(215, 1, '2026-02-23 20:40:46', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'success'),
(216, 1, '2026-02-23 20:45:33', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'success'),
(217, 1, '2026-02-23 20:45:49', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'success'),
(218, 1, '2026-02-23 20:46:01', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'success'),
(219, 1, '2026-02-23 20:52:41', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'success'),
(220, 1, '2026-02-23 20:52:56', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'success'),
(221, 4, '2026-02-23 20:58:29', 'Unknown', 'Unknown', 'failed'),
(222, 1, '2026-02-23 20:59:48', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'success'),
(223, 1, '2026-02-23 21:00:09', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'success'),
(224, 1, '2026-02-23 21:01:20', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'success'),
(225, 1, '2026-02-23 21:03:08', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'success'),
(226, 1, '2026-02-23 21:04:41', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'success'),
(227, 1, '2026-02-23 21:08:26', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'success'),
(228, 1, '2026-02-23 21:13:08', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'success'),
(229, 1, '2026-02-23 21:13:24', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'success'),
(230, 1, '2026-02-23 21:18:32', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'failed'),
(231, 1, '2026-02-23 21:18:37', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'success'),
(232, 1, '2026-02-23 21:22:08', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'success'),
(233, 1, '2026-02-23 21:32:16', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'success'),
(234, 40, '2026-02-23 21:40:54', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'success'),
(235, 1, '2026-02-24 01:35:55', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'success'),
(236, 1, '2026-02-24 02:41:18', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'success'),
(237, 43, '2026-02-24 02:43:55', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'success'),
(238, 43, '2026-02-24 02:45:02', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', ''),
(239, 43, '2026-02-24 02:47:06', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'failed'),
(240, 43, '2026-02-24 02:47:12', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'failed'),
(241, 43, '2026-02-24 02:47:23', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'success'),
(242, 43, '2026-02-24 02:48:19', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', ''),
(243, 40, '2026-02-24 02:48:40', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'success'),
(244, 1, '2026-02-24 04:13:26', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'success'),
(245, 40, '2026-02-24 12:28:34', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'success'),
(246, 40, '2026-02-24 19:14:41', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'success');

-- --------------------------------------------------------

--
-- Table structure for table `otp_codes`
--

CREATE TABLE `otp_codes` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `otp_code` varchar(6) NOT NULL,
  `expires_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `is_used` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `used_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `otp_codes`
--

INSERT INTO `otp_codes` (`id`, `user_id`, `email`, `otp_code`, `expires_at`, `is_used`, `created_at`, `used_at`) VALUES
(4, 13, 'amielchiang@gmail.com', '870094', '2026-02-10 19:00:55', 0, '2026-02-11 01:55:55', NULL),
(6, 4, 'christianbacay042504@gmail.com', '363816', '2026-02-15 02:20:58', 0, '2026-02-15 02:10:58', NULL),
(7, 4, 'christianbacay042504@gmail.com', '568135', '2026-02-15 12:41:11', 0, '2026-02-15 12:31:11', NULL),
(14, 4, 'christianbacay042504@gmail.com', '489548', '2026-02-15 12:51:44', 1, '2026-02-15 12:51:10', '2026-02-15 12:51:44'),
(19, 1, 'Jeanmarcaguilar829@gmail.com', '153762', '2026-02-15 14:28:04', 1, '2026-02-15 14:27:45', '2026-02-15 14:28:04'),
(22, 1, 'jeanmarcaguilar829@gmail.com', '292283', '2026-02-15 15:55:32', 1, '2026-02-15 15:55:20', '2026-02-15 15:55:32'),
(24, 1, 'jeanmarcaguilar829@gmail.com', '860688', '2026-02-15 18:54:18', 1, '2026-02-15 18:54:01', '2026-02-15 18:54:18'),
(25, 4, 'christianbacay042504@gmail.com', '864876', '2026-02-15 19:37:13', 1, '2026-02-15 19:36:55', '2026-02-15 19:37:13'),
(30, 4, 'christianbacay042504@gmail.com', '623664', '2026-02-15 20:09:13', 1, '2026-02-15 20:08:48', '2026-02-15 20:09:13'),
(31, 1, 'jeanmarcaguilar829@gmail.com', '375726', '2026-02-15 23:44:22', 1, '2026-02-15 23:44:07', '2026-02-15 23:44:22'),
(32, 1, 'jeanmarcaguilar829@gmail.com', '949908', '2026-02-16 05:13:51', 1, '2026-02-16 05:13:39', '2026-02-16 05:13:51'),
(33, 1, 'jeanmarcaguilar829@gmail.com', '833601', '2026-02-19 08:55:56', 0, '2026-02-19 08:45:56', NULL),
(34, 1, 'christianbacay042504@gmail.com', '224839', '2026-02-19 08:58:13', 1, '2026-02-19 08:57:48', '2026-02-19 08:58:13'),
(36, 1, 'christianbacay042504@gmail.com', '738633', '2026-02-19 09:24:28', 1, '2026-02-19 09:24:15', '2026-02-19 09:24:28'),
(39, 1, 'christianbacay042504@gmail.com', '188381', '2026-02-19 09:31:24', 1, '2026-02-19 09:30:59', '2026-02-19 09:31:24'),
(40, 1, 'christianbacay042504@gmail.com', '259786', '2026-02-19 09:42:37', 1, '2026-02-19 09:42:20', '2026-02-19 09:42:37'),
(42, 1, 'christianbacay042504@gmail.com', '783608', '2026-02-19 09:46:11', 1, '2026-02-19 09:45:57', '2026-02-19 09:46:11'),
(43, 1, 'christianbacay042504@gmail.com', '979097', '2026-02-19 13:18:19', 1, '2026-02-19 13:17:35', '2026-02-19 13:18:19'),
(44, 40, 'jeanmarcaguilar829@gmail.com', '004834', '2026-02-19 13:25:10', 1, '2026-02-19 13:24:56', '2026-02-19 13:25:10'),
(45, 40, 'jeanmarcaguilar829@gmail.com', '266418', '2026-02-19 13:25:56', 1, '2026-02-19 13:25:33', '2026-02-19 13:25:56'),
(46, 1, 'christianbacay042504@gmail.com', '438837', '2026-02-19 13:30:09', 1, '2026-02-19 13:29:54', '2026-02-19 13:30:09'),
(49, 40, 'jeanmarcaguilar829@gmail.com', '874474', '2026-02-23 17:46:38', 0, '2026-02-23 17:36:38', NULL),
(50, 40, 'jeanmarcaguilar829@gmail.com', '849374', '2026-02-23 18:45:10', 1, '2026-02-23 18:44:38', '2026-02-23 18:45:10'),
(53, 1, 'senchi2528@gmail.com', '599796', '2026-02-23 19:47:18', 0, '2026-02-23 19:37:18', NULL),
(57, 8, 'admin_content@sjdm.com', '673104', '2026-02-23 20:30:22', 0, '2026-02-23 20:20:22', NULL),
(60, 40, 'jeanmarcaguilar829@gmail.com', '693336', '2026-02-23 20:35:32', 0, '2026-02-23 20:25:32', NULL),
(85, 1, 'senchi2528@gmail.com', '739183', '2026-02-23 21:32:08', 0, '2026-02-23 21:22:08', NULL),
(86, 1, 'senchi2528@gmail.com', '266722', '2026-02-23 21:32:42', 1, '2026-02-23 21:32:16', '2026-02-23 21:32:42'),
(87, 40, 'jeanmarcaguilar829@gmail.com', '368445', '2026-02-23 21:41:14', 1, '2026-02-23 21:40:54', '2026-02-23 21:41:14'),
(88, 1, 'senchi2528@gmail.com', '507978', '2026-02-24 01:36:55', 1, '2026-02-24 01:35:55', '2026-02-24 01:36:55'),
(89, 1, 'senchi2528@gmail.com', '288055', '2026-02-24 02:41:44', 1, '2026-02-24 02:41:18', '2026-02-24 02:41:44'),
(90, 43, 'ianjovero2528@gmail.com', '189405', '2026-02-24 02:44:29', 1, '2026-02-24 02:43:55', '2026-02-24 02:44:29'),
(91, 43, 'ianjovero2528@gmail.com', '271877', '2026-02-24 02:47:54', 1, '2026-02-24 02:47:23', '2026-02-24 02:47:54'),
(92, 40, 'jeanmarcaguilar829@gmail.com', '373989', '2026-02-24 02:48:55', 1, '2026-02-24 02:48:40', '2026-02-24 02:48:55'),
(93, 1, 'senchi2528@gmail.com', '846920', '2026-02-24 04:13:46', 1, '2026-02-24 04:13:26', '2026-02-24 04:13:46'),
(94, 40, 'jeanmarcaguilar829@gmail.com', '952396', '2026-02-24 12:28:52', 1, '2026-02-24 12:28:34', '2026-02-24 12:28:52'),
(95, 40, 'jeanmarcaguilar829@gmail.com', '184215', '2026-02-24 19:15:00', 1, '2026-02-24 19:14:41', '2026-02-24 19:15:00');

-- --------------------------------------------------------

--
-- Table structure for table `registration_tour_guide`
--

CREATE TABLE `registration_tour_guide` (
  `id` int(11) NOT NULL,
  `application_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `last_name` varchar(100) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `middle_initial` varchar(5) DEFAULT NULL,
  `preferred_name` varchar(100) DEFAULT NULL,
  `date_of_birth` date NOT NULL,
  `gender` enum('male','female') NOT NULL,
  `home_address` text NOT NULL,
  `primary_phone` varchar(20) NOT NULL,
  `secondary_phone` varchar(20) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `emergency_contact_name` varchar(100) NOT NULL,
  `emergency_contact_relationship` varchar(50) NOT NULL,
  `emergency_contact_phone` varchar(20) NOT NULL,
  `dot_accreditation` varchar(100) NOT NULL,
  `accreditation_expiry` date NOT NULL,
  `specialization` enum('mountain','waterfall','cultural','adventure','photography') NOT NULL,
  `years_experience` int(3) NOT NULL DEFAULT 0,
  `first_aid_certified` enum('yes','no') NOT NULL,
  `first_aid_expiry` date DEFAULT NULL,
  `base_location` varchar(255) NOT NULL,
  `employment_type` enum('full-time','part-time','weekends') NOT NULL,
  `has_vehicle` enum('yes','no') NOT NULL,
  `resume_file` varchar(255) DEFAULT NULL,
  `dot_id_file` varchar(255) DEFAULT NULL,
  `government_id_file` varchar(255) DEFAULT NULL,
  `nbi_clearance_file` varchar(255) DEFAULT NULL,
  `first_aid_certificate_file` varchar(255) DEFAULT NULL,
  `id_photo_file` varchar(255) DEFAULT NULL,
  `status` enum('pending','under_review','approved','rejected') NOT NULL DEFAULT 'pending',
  `admin_notes` text DEFAULT NULL,
  `review_date` datetime DEFAULT NULL,
  `reviewed_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `registration_tour_guide`
--

INSERT INTO `registration_tour_guide` (`id`, `application_date`, `last_name`, `first_name`, `middle_initial`, `preferred_name`, `date_of_birth`, `gender`, `home_address`, `primary_phone`, `secondary_phone`, `email`, `emergency_contact_name`, `emergency_contact_relationship`, `emergency_contact_phone`, `dot_accreditation`, `accreditation_expiry`, `specialization`, `years_experience`, `first_aid_certified`, `first_aid_expiry`, `base_location`, `employment_type`, `has_vehicle`, `resume_file`, `dot_id_file`, `government_id_file`, `nbi_clearance_file`, `first_aid_certificate_file`, `id_photo_file`, `status`, `admin_notes`, `review_date`, `reviewed_by`) VALUES
(2, '2026-02-16 04:24:36', 'Chiang', 'Luke', 'H', 'Lukas', '1990-01-01', 'male', 'tsora', '09123238686', '09232334343', 'amieljake929@gmail.com', 'Maria Labo', 'Friend', '09898974545', '22342424234', '2026-02-16', 'waterfall', 6, 'yes', '2026-02-16', 'TSOra', 'full-time', 'no', 'tour_guide_documents/69929c049b5de_resume.docx', 'tour_guide_documents/69929c049becb_dotId.jpg', 'tour_guide_documents/69929c049c7b2_governmentId.jpg', 'tour_guide_documents/69929c049cdff_nbiClearance.jpg', 'tour_guide_documents/69929c049d53c_firstAidCertificate.jpg', 'tour_guide_documents/69929c049dd11_idPhoto.jpg', 'approved', NULL, '2026-02-19 17:13:12', NULL),
(7, '2026-02-19 13:29:48', 'Kurisuchan', 'Ian Jovero', 'C', 'Kurisuchan', '1990-01-01', 'male', 'Sauyo', '09648816402', '09705667137', 'senchi2528@gmail.com', '09636219345', 'Asawa', '098816402', '12312313213', '2026-02-25', 'cultural', 5, 'yes', '2026-02-28', 'Sasuyo', 'part-time', 'yes', 'tour_guide_documents/6997104c87dd1_resume.docx', 'tour_guide_documents/6997104c88ad8_dotId.jpg', 'tour_guide_documents/6997104c898d4_governmentId.jpg', 'tour_guide_documents/6997104c8a94b_nbiClearance.png', 'tour_guide_documents/6997104c8b935_firstAidCertificate.png', 'tour_guide_documents/6997104c8c64c_idPhoto.png', '', NULL, '2026-02-19 21:30:45', NULL),
(8, '2026-02-24 02:41:10', 'Diaz', 'Robert', 'C.', 'Robert', '1998-08-13', 'male', 'Gaya Gaya Bulacan', '090993666143', '090993666143', 'ianjovero2528@gmail.com', 'Susan', 'Wife', '090993666143', '123123123', '2026-02-14', 'cultural', 3, 'no', NULL, 'Gaya Gaya', 'full-time', 'no', 'tour_guide_documents/699d0fc648bbd_resume.docx', 'tour_guide_documents/699d0fc6499cd_dotId.jpg', 'tour_guide_documents/699d0fc64a15a_governmentId.png', 'tour_guide_documents/699d0fc64a672_nbiClearance.png', 'tour_guide_documents/699d0fc64ae52_firstAidCertificate.jpg', 'tour_guide_documents/699d0fc64b93d_idPhoto.png', 'approved', NULL, '2026-02-24 10:42:42', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `report_settings`
--

CREATE TABLE `report_settings` (
  `id` int(11) NOT NULL,
  `setting_key` varchar(50) NOT NULL,
  `setting_value` text NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `report_settings`
--

INSERT INTO `report_settings` (`id`, `setting_key`, `setting_value`, `updated_at`) VALUES
(1, 'module_title', 'Reports Management', '2026-02-06 08:07:12'),
(2, 'module_subtitle', 'Generate and manage reports', '2026-02-06 08:07:12'),
(3, 'default_report_limit', '15', '2026-02-06 08:07:12'),
(4, 'admin_mark_label', 'RP', '2026-02-06 08:07:12');

-- --------------------------------------------------------

--
-- Table structure for table `restaurants`
--

CREATE TABLE `restaurants` (
  `id` int(11) NOT NULL,
  `name` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `category` enum('casual_dining','local_eatery','fast_food','fine_dining','cafe','bar') NOT NULL,
  `location` varchar(200) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `contact_info` varchar(200) DEFAULT NULL,
  `website` varchar(200) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `price_range` varchar(100) DEFAULT NULL,
  `rating` decimal(3,2) DEFAULT 0.00,
  `review_count` int(11) DEFAULT 0,
  `cuisine_type` varchar(100) DEFAULT NULL,
  `opening_hours` varchar(200) DEFAULT NULL,
  `features` text DEFAULT NULL,
  `services` text DEFAULT NULL,
  `image_url` varchar(500) DEFAULT NULL,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `restaurants`
--

INSERT INTO `restaurants` (`id`, `name`, `description`, `category`, `location`, `address`, `contact_info`, `website`, `email`, `phone`, `price_range`, `rating`, `review_count`, `cuisine_type`, `opening_hours`, `features`, `services`, `image_url`, `latitude`, `longitude`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Escobar\'s', 'Popular local restaurant serving authentic Filipino cuisine and comfort food in a cozy atmosphere.', 'casual_dining', 'San Jose del Monte, Bulacan', NULL, NULL, NULL, NULL, NULL, '200-500', 4.30, 0, NULL, NULL, NULL, NULL, '/images/restaurant-escobars.jpg', NULL, NULL, 'active', '2026-01-01 00:00:00', '2026-01-01 00:00:00'),
(2, 'Roadside Dampa', 'Casual roadside eatery offering fresh seafood and grilled specialties at affordable prices.', 'local_eatery', 'San Jose del Monte, Bulacan', NULL, NULL, NULL, NULL, NULL, '150-400', 4.10, 0, NULL, NULL, NULL, NULL, '/images/restaurant-dampa.jpg', NULL, NULL, 'active', '2026-01-02 01:00:00', '2026-01-02 01:00:00'),
(3, 'Max\'s SM SJDM', 'Well-known restaurant chain serving classic Filipino dishes and fried chicken in SM City San Jose del Monte.', 'fast_food', 'San Jose del Monte, Bulacan', NULL, NULL, NULL, NULL, NULL, '300-600', 4.50, 0, NULL, NULL, NULL, NULL, '/images/restaurant-maxs.jpg', NULL, NULL, 'active', '2026-01-03 02:00:00', '2026-01-03 02:00:00'),
(4, 'Local Carinderia', 'Traditional Filipino eatery offering home-cooked meals at budget-friendly prices.', 'local_eatery', 'San Jose del Monte, Bulacan', NULL, NULL, NULL, NULL, NULL, '100-250', 4.00, 0, NULL, NULL, NULL, NULL, '/images/restaurant-carinderia.jpg', NULL, NULL, 'active', '2026-01-04 03:00:00', '2026-01-04 03:00:00'),
(5, 'Cafe de Monte', 'Cozy coffee shop serving freshly brewed coffee, pastries, and light meals.', 'cafe', 'San Jose del Monte, Bulacan', NULL, NULL, NULL, NULL, NULL, '150-300', 4.20, 0, NULL, NULL, NULL, NULL, '/images/restaurant-cafe.jpg', NULL, NULL, 'active', '2026-01-05 04:00:00', '2026-01-05 04:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `saved_tours`
--

CREATE TABLE `saved_tours` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `tour_name` varchar(200) NOT NULL,
  `tour_description` text DEFAULT NULL,
  `saved_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `status_logs`
--

CREATE TABLE `status_logs` (
  `id` int(11) NOT NULL,
  `table_name` varchar(50) NOT NULL,
  `record_id` int(11) NOT NULL,
  `old_status` varchar(50) DEFAULT NULL,
  `new_status` varchar(50) NOT NULL,
  `changed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Logs status changes for audit trail purposes';

-- --------------------------------------------------------

--
-- Table structure for table `tourist_spots`
--

CREATE TABLE `tourist_spots` (
  `id` int(11) NOT NULL,
  `name` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `category` enum('nature','historical','religious','farm','park','urban') NOT NULL,
  `location` varchar(200) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `operating_hours` varchar(100) DEFAULT NULL,
  `entrance_fee` varchar(100) DEFAULT NULL,
  `difficulty_level` enum('easy','moderate','difficult') DEFAULT 'moderate',
  `duration` varchar(100) DEFAULT NULL,
  `best_time_to_visit` varchar(100) DEFAULT NULL,
  `activities` text DEFAULT NULL,
  `amenities` text DEFAULT NULL,
  `contact_info` varchar(200) DEFAULT NULL,
  `website` varchar(200) DEFAULT NULL,
  `image_url` varchar(500) DEFAULT NULL,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `rating` decimal(3,2) DEFAULT 0.00,
  `review_count` int(11) DEFAULT 0,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tourist_spots`
--

INSERT INTO `tourist_spots` (`id`, `name`, `description`, `category`, `location`, `address`, `operating_hours`, `entrance_fee`, `difficulty_level`, `duration`, `best_time_to_visit`, `activities`, `amenities`, `contact_info`, `website`, `image_url`, `latitude`, `longitude`, `rating`, `review_count`, `status`, `created_at`, `updated_at`) VALUES
(12, 'Abes Farm', 'Abes Farm is a premier agri-tourism destination offering visitors an authentic \n                        Filipino farming experience. Established over two decades ago, this family-owned \n                        farm provides educational and recreational activities that showcase traditional \n                        agricultural practices in a modern, sustainable setting.', 'farm', 'San Jose del Monte, Bulacan', 'San Jose del Monte, Bulacan', '8:00 AM - 5:00 PM', 'Free', 'difficult', '', 'All year round', '', 'Shed', '', '', 'https://tse2.mm.bing.net/th/id/OIP.6PNZ3NMvsNBmKlxJeHTXEQHaDj?pid=Api&P=0&h=220', NULL, NULL, 0.00, 0, 'active', '2026-02-06 09:40:50', '2026-02-10 08:03:23'),
(13, 'Burong Falls', 'Burong Falls is one of San Jose del Monte\'s most spectacular natural attractions, featuring \n                        impressive multi-tiered cascades surrounded by pristine rainforest. This destination is part \n                        of a trio falls tour (Burong, Otso-Otso, and Kaytitinga Falls) with a mandatory tour guide \n                        fee of PHP 350 per group (max 5 pax), arranged through the tourism office.', 'nature', 'San Jose del Monte, Bulacan', 'San Jose del Monte, Bulacan', '6:00 AM - 6:00 PM', '₱350', 'difficult', '', 'Dry season', 'Hiking, Trekking', '', '', '', 'https://tse3.mm.bing.net/th/id/OIP.cLp6dchz3-ZHjFh6duCy9wHaEc?pid=Api&P=0&h=220', NULL, NULL, 0.00, 0, 'active', '2026-02-06 09:40:50', '2026-02-08 21:37:25'),
(14, 'City Oval & People\'s Park', 'Spanning over 10 hectares in the heart of San Jose del Monte, the City Oval and People\'s Park \n                        complex serves as the city\'s premier sports, recreation, and community event facility. This \n                        multi-purpose complex combines world-class athletic facilities with beautiful green spaces.', 'park', 'San Jose del Monte, Bulacan', 'San Jose del Monte, Bulacan', '5:00 AM - 10:00 PM', 'Free', 'moderate', '', 'All year round', '', 'Parking, Shed', '', '', 'https://tse1.mm.bing.net/th/id/OIP.1dnzg82f-NJm7SRdZ6HNBAHaFJ?pid=Api&P=0&h=220', NULL, NULL, 0.00, 0, 'active', '2026-02-06 09:40:50', '2026-02-08 21:37:55'),
(15, 'Kaytitinga Falls', 'Kaytitinga Falls is one of the most picturesque waterfalls in San Jose del Monte, \n                        featuring three stunning tiers of cascading water surrounded by lush tropical forest. \n                        This destination is part of a trio falls tour (Burong, Otso-Otso, and Kaytitinga Falls) with a mandatory tour guide \n                        fee of PHP 350 per group (max 5 pax), arranged through the tourism office.', 'nature', 'San Jose del Monte, Bulacan', 'San Jose del Monte, Bulacan', '7:00 AM - 10:00 AM', '₱350', 'difficult', '', 'Morning', 'Hiking, Swimming', '', '', '', 'https://tse4.mm.bing.net/th/id/OIP.EWW5-Bjp7Hci8KrQ7pZbrQHaEo?pid=Api&P=0&h=220', NULL, NULL, 0.00, 0, 'active', '2026-02-06 09:40:50', '2026-02-08 21:38:16'),
(16, 'Mt. Balagbag', 'Mt. Balagbag, standing at 777 meters above sea level, is one of the most popular hiking \n                        destinations near Metro Manila. Known as the \"Mt. Pulag of Bulacan,\" it offers stunning \n                        360-degree panoramic views of Metro Manila, Laguna de Bay, and the surrounding mountain ranges.', 'nature', 'San Jose del Monte, Bulacan', 'San Jose del Monte, Bulacan', '6:00 AM - 6:00 PM', '₱25', 'easy', '', 'Morning', 'Hiking, Camping', 'Shed', '', '', 'https://tse3.mm.bing.net/th/id/OIP.S_I-lXG6p3QDfiO8yUoxTQHaFj?pid=Api&P=0&h=220', NULL, NULL, 0.00, 0, 'active', '2026-02-06 09:40:50', '2026-02-08 21:38:52'),
(17, 'Otso-Otso Falls', 'Named after the Filipino word \"otso\" meaning eight, Otso-Otso Falls is a magnificent \n                        series of eight interconnected waterfalls located in the pristine wilderness of San Jose del Monte. \n                        This destination is part of a trio falls tour (Burong, Otso-Otso, and Kaytitinga Falls) with a mandatory tour guide \n                        fee of PHP 350 per group (max 5 pax), arranged through the tourism office.', 'nature', 'San Jose del Monte, Bulacan', 'San Jose del Monte, Bulacan', '7:00 AM - 4:00 PM', '₱350', 'easy', '', 'Dry season', 'Hiking, Swimming', '', '', '', 'https://tse4.mm.bing.net/th/id/OIP.SsASPok_BbP2wo1flyJvzgHaDj?pid=Api&P=0&h=220', NULL, NULL, 0.00, 0, 'active', '2026-02-06 09:40:50', '2026-02-08 21:39:13'),
(18, 'Our Lady of Lourdes Grotto', 'The Our Lady of Lourdes Grotto is a renowned spiritual sanctuary in San Jose del Monte, \n                        established in 1958 as a replica of the famous Lourdes Grotto in France. This peaceful \n                        religious site attracts thousands of pilgrims and visitors seeking spiritual renewal, \n                        healing, and divine intervention.', 'religious', 'San Jose del Monte, Bulacan', 'San Jose del Monte, Bulacan', '5:00 AM - 8:00 PM', 'Free', 'moderate', '', 'All year round', 'Photography', 'Shed', '', '', 'https://tse2.mm.bing.net/th/id/OIP.3k2Pj5tL7rhVT-sDN2XSzwHaEK?pid=Api&P=0&h=220', NULL, NULL, 0.00, 0, 'active', '2026-02-06 09:40:50', '2026-02-08 22:41:27'),
(20, 'Paradise Hill Farm', 'Paradise Hill Farm is a 25-hectare integrated sustainable farm that combines modern agricultural \n                        practices with eco-tourism experiences. Established in 2010, this farm utilizes organic farming \n                        methods, renewable energy, and water conservation techniques to create a model for sustainable \n                        agriculture in San Jose del Monte.', 'farm', 'San Jose del Monte, Bulacan', 'San Jose del Monte, Bulacan', '8:00 AM - 5:00 PM', '₱200', 'moderate', '', 'All year round', 'Photography', 'Shed, Restaurant', '', '', 'https://tse3.mm.bing.net/th/id/OIP.K8xFcpCM-Frmj4yyT7jkmQHaE8?pid=Api&P=0&h=220', NULL, NULL, 0.00, 0, 'active', '2026-02-06 09:40:50', '2026-02-08 21:40:02'),
(21, 'The Rising Heart Monument', 'Standing majestically at the entrance of San Jose del Monte City, The Rising Heart Monument \n                        is a 15-meter tall steel sculpture that has become the city\'s most recognizable landmark. \n                        Completed in 2018 to celebrate the city\'s conversion from municipality to component city, \n                        this monument symbolizes the city\'s rising prosperity, love for community, and resilient spirit.', 'park', 'San Jose del Monte, Bulacan', 'San Jose del Monte, Bulacan', '5:30 AM - 6:30 AM', 'Free', 'moderate', '', 'All year round', 'Photography', 'Parking, Restroom, Shed', '', '', 'https://tse2.mm.bing.net/th/id/OIP.NoRJkeG3JRq1XuSygZRRNwHaEc?pid=Api&P=0&h=220', NULL, NULL, 0.00, 0, 'active', '2026-02-06 09:40:50', '2026-02-08 22:49:11'),
(22, 'Tungtong Falls', 'Tungtong Falls is a spectacular 25-meter waterfall located within a dramatic canyon formation \n                        in San Jose del Monte. Named after the local term \"tungtong\" meaning \"to climb or ascend,\" \n                        this waterfall features a unique rock amphitheater that creates perfect acoustics and \n                        breathtaking visual effects when sunlight hits the cascading water.', 'nature', 'San Jose del Monte, Bulacan', 'San Jose del Monte, Bulacan', '7:00 AM - 5:00 PM', '₱1', 'difficult', '', 'All year round', 'Hiking, Swimming', 'Parking', '', '', 'https://tse3.mm.bing.net/th/id/OIP.PGdvW97mFRf7-GQD84CukQHaEc?pid=Api&P=0&h=220', NULL, NULL, 0.00, 0, 'active', '2026-02-06 09:40:50', '2026-02-08 21:40:45');

-- --------------------------------------------------------

--
-- Table structure for table `tour_guides`
--

CREATE TABLE `tour_guides` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `name` varchar(100) NOT NULL,
  `specialty` varchar(200) DEFAULT NULL,
  `category` enum('mountain','city','farm','waterfall','historical','general') NOT NULL DEFAULT 'general',
  `description` text DEFAULT NULL,
  `bio` text DEFAULT NULL,
  `areas_of_expertise` text DEFAULT NULL,
  `rating` decimal(3,2) DEFAULT 0.00,
  `review_count` int(11) DEFAULT 0,
  `price_range` varchar(100) DEFAULT NULL,
  `price_min` decimal(10,2) DEFAULT NULL,
  `price_max` decimal(10,2) DEFAULT NULL,
  `languages` varchar(200) DEFAULT NULL,
  `contact_number` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `schedules` text DEFAULT NULL,
  `experience_years` int(11) DEFAULT NULL,
  `group_size` varchar(50) DEFAULT NULL,
  `verified` tinyint(1) DEFAULT 0,
  `total_tours` int(11) DEFAULT 0,
  `photo_url` varchar(500) DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `resume` varchar(500) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tour_guides`
--

INSERT INTO `tour_guides` (`id`, `user_id`, `name`, `specialty`, `category`, `description`, `bio`, `areas_of_expertise`, `rating`, `review_count`, `price_range`, `price_min`, `price_max`, `languages`, `contact_number`, `email`, `schedules`, `experience_years`, `group_size`, `verified`, `total_tours`, `photo_url`, `status`, `created_at`, `updated_at`, `resume`) VALUES
(14, NULL, 'Ian Jaime', 'mountain', '', 'Professional tour guide specializing in Mountain tours.', NULL, NULL, 0.00, 0, NULL, NULL, NULL, NULL, '09099366143', 'ianjaime@gmail.com', NULL, NULL, NULL, 0, 0, NULL, 'active', '2026-02-11 00:39:57', '2026-02-24 03:12:04', NULL),
(15, NULL, 'jean marcc', 'mountain', '', 'Professional tour guide specializing in Mountain tours.', NULL, NULL, 0.00, 0, NULL, NULL, NULL, NULL, '092991156561', 'jeanmarcc@gmail.com', NULL, NULL, NULL, 0, 0, NULL, 'active', '2026-02-11 00:49:33', '2026-02-24 03:11:56', NULL),
(16, NULL, 'amiel jake', 'waterfall', 'waterfall', 'Experienced tour guide specializing in Waterfall tours with 5+ years of experience showing visitors the beautiful waterfalls of San Jose del Monte.', '', '', 0.00, 0, '', NULL, NULL, 'English, Tagalog', '6514623333', 'amieljake@gmail.com', '', 5, '15 guests', 1, 0, '', 'active', '2026-02-11 00:55:44', '2026-02-15 13:25:17', NULL),
(17, NULL, 'Jean Marc  Aguilar', 'mountain', '', 'Professional tour guide specializing in Mountain tours.', NULL, NULL, 0.00, 0, NULL, NULL, NULL, NULL, '09705667137', 'jeanmarcaguilar829@gmail.com', NULL, NULL, NULL, 1, 0, NULL, 'active', '2026-02-11 01:29:34', '2026-02-15 13:35:01', 'C:\\xampp\\htdocs\\coderistyarn2\\log-in/../uploads/resumes/698bdb7ebb537_resume.pdf'),
(18, 33, 'Luke Chiang', 'waterfall', 'waterfall', '', '', 'waterfall', 0.00, 0, '0', 0.00, 0.00, 'English, Filipino', '09123238585', NULL, '0', 0, '0', 0, 0, '', '', '2026-02-15 20:05:22', '2026-02-15 20:05:22', '699227024192b_resume.docx'),
(20, 35, 'Luke Chiang', NULL, 'waterfall', NULL, NULL, NULL, 0.00, 0, NULL, NULL, NULL, NULL, '09123238686', 'amieljake929@gmail.com', NULL, 6, NULL, 0, 0, NULL, 'active', '2026-02-19 09:13:12', '2026-02-19 09:13:12', NULL),
(21, NULL, 'Ian Kurisuchan', 'cultural', '', 'Professional tour guide specializing in cultural tours.', 'Experienced tour guide with 5 years of experience. DOT accredited with license #12312313213. Based in Sasuyo.', 'cultural', 0.00, 0, NULL, NULL, NULL, 'filipino (fluent), english (fluent)', '09648816402', 'senchi2528@gmail.com', 'Available part-time', 5, NULL, 1, 0, NULL, 'active', '2026-02-19 09:24:45', '2026-02-24 03:07:13', NULL),
(22, NULL, 'Jean Marc Aguilar', NULL, '', NULL, NULL, NULL, 0.00, 0, NULL, NULL, NULL, NULL, '09705667137', 'jeanmarcaguilar829@gmail.com', NULL, 6, NULL, 0, 0, NULL, 'active', '2026-02-19 09:42:59', '2026-02-19 09:42:59', NULL),
(25, NULL, 'Ian Jovero Kurisuchan', NULL, '', NULL, NULL, NULL, 0.00, 0, NULL, NULL, NULL, NULL, '09648816402', 'senchi2528@gmail.com', NULL, 5, NULL, 0, 0, NULL, 'active', '2026-02-19 13:30:45', '2026-02-19 13:30:45', NULL),
(26, 43, 'Robert Diaz', NULL, '', NULL, NULL, NULL, 0.00, 0, NULL, NULL, NULL, NULL, '090993666143', 'ianjovero2528@gmail.com', NULL, 3, NULL, 1, 0, NULL, 'active', '2026-02-24 02:42:42', '2026-02-24 03:12:15', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `tour_guide_availability`
--

CREATE TABLE `tour_guide_availability` (
  `id` int(11) NOT NULL,
  `tour_guide_id` int(11) NOT NULL,
  `available_date` date NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `status` enum('available','booked','unavailable') DEFAULT 'available',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tour_guide_languages`
--

CREATE TABLE `tour_guide_languages` (
  `id` int(11) NOT NULL,
  `registration_id` int(11) NOT NULL,
  `language` varchar(50) NOT NULL,
  `proficiency` enum('native','fluent','conversational') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tour_guide_languages`
--

INSERT INTO `tour_guide_languages` (`id`, `registration_id`, `language`, `proficiency`) VALUES
(3, 2, 'filipino', 'fluent'),
(4, 2, 'english', 'fluent'),
(11, 7, 'filipino', 'fluent'),
(12, 7, 'english', 'fluent'),
(13, 8, 'filipino', 'fluent');

-- --------------------------------------------------------

--
-- Table structure for table `tour_guide_profiles`
--

CREATE TABLE `tour_guide_profiles` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `license_number` varchar(50) DEFAULT NULL,
  `specialization` text DEFAULT NULL,
  `experience_years` int(11) DEFAULT 0,
  `languages` text DEFAULT NULL,
  `hourly_rate` decimal(10,2) DEFAULT 0.00,
  `availability_status` enum('available','busy','offline') DEFAULT 'available',
  `rating` decimal(3,2) DEFAULT 0.00,
  `total_tours` int(11) DEFAULT 0,
  `bio` text DEFAULT NULL,
  `contact_number` varchar(20) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tour_guide_profiles`
--

INSERT INTO `tour_guide_profiles` (`id`, `user_id`, `license_number`, `specialization`, `experience_years`, `languages`, `hourly_rate`, `availability_status`, `rating`, `total_tours`, `bio`, `contact_number`, `created_at`, `updated_at`) VALUES
(1, 11, 'TG-001-2026', 'Historical Tours, Nature Walks', 5, 'English, Filipino, Basic Japanese', 1500.00, 'available', 0.00, 0, 'Experienced tour guide specializing in rich history and natural beauty of San Jose del Monte.', '09123456789', '2026-02-08 19:38:48', '2026-02-08 20:24:20'),
(2, 12, 'TG-002-2026', 'Adventure Tours, Mountain Hiking', 3, 'English, Filipino', 1200.00, 'available', 0.00, 0, 'Adventure enthusiast with extensive knowledge of mountain trails and outdoor activities.', '09987654321', '2026-02-08 19:38:48', '2026-02-08 19:38:48');

-- --------------------------------------------------------

--
-- Table structure for table `tour_guide_reviews`
--

CREATE TABLE `tour_guide_reviews` (
  `id` int(11) NOT NULL,
  `tour_guide_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `booking_id` int(11) DEFAULT NULL,
  `rating` int(11) NOT NULL CHECK (`rating` >= 1 and `rating` <= 5),
  `review` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tour_guide_settings`
--

CREATE TABLE `tour_guide_settings` (
  `id` int(11) NOT NULL,
  `setting_key` varchar(50) NOT NULL,
  `setting_value` text NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tour_guide_settings`
--

INSERT INTO `tour_guide_settings` (`id`, `setting_key`, `setting_value`, `updated_at`) VALUES
(1, 'module_title', 'Tour Guides Management', '2026-02-06 08:07:12'),
(2, 'module_subtitle', 'Manage and monitor tour guides', '2026-02-06 08:07:12'),
(3, 'default_guide_limit', '15', '2026-02-06 08:07:12'),
(4, 'admin_mark_label', 'TG', '2026-02-06 08:07:12');

-- --------------------------------------------------------

--
-- Table structure for table `travel_tips`
--

CREATE TABLE `travel_tips` (
  `id` int(11) NOT NULL,
  `icon` varchar(50) DEFAULT NULL,
  `title` varchar(200) NOT NULL,
  `category` varchar(50) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `display_order` int(11) DEFAULT 0,
  `is_active` enum('yes','no') DEFAULT 'yes',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `travel_tips`
--

INSERT INTO `travel_tips` (`id`, `icon`, `title`, `category`, `description`, `display_order`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'directions_bus', 'Transportation Tips', 'transport', 'Book transportation in advance during peak seasons\nUse reputable transport services for safety\nKeep emergency contact numbers handy\nDownload offline maps for navigation', 1, 'yes', '2026-01-01 00:00:00', '2026-01-01 00:00:00'),
(2, 'hotel', 'Accommodation Advice', 'accommodation', 'Read recent reviews before booking\nCheck location proximity to attractions\nConfirm cancellation policies\nBring personal toiletries for comfort', 2, 'yes', '2026-01-01 00:00:00', '2026-01-01 00:00:00'),
(3, 'restaurant', 'Food & Dining', 'food', 'Try local delicacies for authentic experience\nDrink bottled water for safety\nCheck food hygiene standards\nBring snacks for long tours', 3, 'yes', '2026-01-01 00:00:00', '2026-01-01 00:00:00'),
(4, 'health_and_safety', 'Health & Safety', 'safety', 'Pack basic medications and first aid\nGet travel insurance for emergencies\nKeep copies of important documents\nKnow local emergency numbers', 4, 'yes', '2026-01-01 00:00:00', '2026-01-01 00:00:00'),
(5, 'payments', 'Money & Payments', 'finance', 'Bring enough cash for small purchases\nInform bank about travel plans\nUse secure ATMs for withdrawals\nKeep money in different locations', 5, 'yes', '2026-01-01 00:00:00', '2026-01-01 00:00:00'),
(6, 'camera_alt', 'Photography Tips', 'photography', 'Respect photography restrictions\nBring extra batteries and memory cards\nAsk permission before photographing people\nCapture sunrise/sunset for best shots', 6, 'yes', '2026-01-01 00:00:00', '2026-01-01 00:00:00'),
(7, 'phone_iphone', 'Communication', 'communication', 'Buy local SIM cards for data\nDownload useful travel apps\nLearn basic local phrases\nKeep phone charged with power bank', 7, 'yes', '2026-01-01 00:00:00', '2026-01-01 00:00:00'),
(8, 'shopping_bag', 'Shopping Guide', 'shopping', 'Bargain respectfully at local markets\nBuy authentic local products\nCheck customs regulations for souvenirs\nSupport local artisans and businesses', 8, 'yes', '2026-01-01 00:00:00', '2026-01-01 00:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `user_type` enum('user','admin','tour_guide') DEFAULT 'user',
  `status` enum('active','inactive','suspended') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `last_login` timestamp NULL DEFAULT NULL,
  `preferences_set` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `first_name`, `last_name`, `email`, `password`, `user_type`, `status`, `created_at`, `updated_at`, `last_login`, `preferences_set`) VALUES
(1, 'Admin', 'SJDM', 'senchi2528@gmail.com', '$2y$10$6qLFC1Jeajfeb2yMht1BWOodAuVhSWGNBsh7lF6DVMn0Md.deNNkq', 'admin', 'active', '2026-01-30 07:02:22', '2026-02-24 04:13:25', '2026-02-24 04:13:25', 0),
(4, 'Ian', 'Jovero', 'christianbacay0425047@gmail.com', '$2y$10$g6BWxl4c3rJjU33kcsd/V.uhRTCV9Lsb96NRBRKkGQN5smel3TSBq', 'user', 'active', '2026-01-31 08:00:05', '2026-02-19 08:57:30', '2026-02-15 20:08:48', 1),
(5, 'angel', 'hernandez', 'angelhernandez@gmail.com', '$2y$10$xeVhlC34UmXg8OwWTBkLzOdqtEkNImiY30qlNqan/1I1XkFToQ1F.', 'user', 'active', '2026-02-02 02:00:47', '2026-02-02 02:39:38', '2026-02-02 02:39:38', 0),
(6, 'Admin', 'Dashboard', 'admin_dashboard@sjdm.com', '$2y$10$3DQxAUR/H3QdYWRerZKzMuqCvXoRwVpQDFuiE8SU6BvydNnM5CiBS', 'admin', 'active', '2026-02-05 01:00:00', '2026-02-05 01:00:00', NULL, 0),
(7, 'Admin', 'Users', 'admin_users@sjdm.com', '$2y$10$3DQxAUR/H3QdYWRerZKzMuqCvXoRwVpQDFuiE8SU6BvydNnM5CiBS', 'admin', 'active', '2026-02-05 01:00:00', '2026-02-05 01:00:00', NULL, 0),
(8, 'Admin', 'Content', 'admin_content@sjdm.com', '$2y$10$3DQxAUR/H3QdYWRerZKzMuqCvXoRwVpQDFuiE8SU6BvydNnM5CiBS', 'admin', 'active', '2026-02-05 01:00:00', '2026-02-05 01:00:00', NULL, 0),
(11, 'Juan', 'Santos', 'juan.santos@tourguide.com', '$2y$10$th0oYWlqMx4jvSWwaRjxz.hAbX5WNxWGnkOadofI86xME.tybAs3i', 'tour_guide', 'active', '2026-02-09 00:00:00', '2026-02-10 07:39:11', '2026-02-10 07:39:11', 0),
(12, 'Maria', 'Reyes', 'maria.reyes@tourguide.com', '$2y$10$th0oYWlqMx4jvSWwaRjxz.hAbX5WNxWGnkOadofI86xME.tybAs3i', 'tour_guide', 'active', '2026-02-09 00:00:00', '2026-02-10 06:37:59', '2026-02-10 06:37:59', 0),
(13, 'Amiel', 'Chiang', 'amielchiang@gmail.com', '$2y$10$dgdmgYtRSk2u4Srn2AwtXuDqxM4aW24bcvhEOzIrWLy8nQhXkKHAe', 'user', 'active', '2026-02-08 21:25:16', '2026-02-11 03:21:34', '2026-02-11 03:21:34', 0),
(14, 'angel', 'jaime', 'angeljaime@gmail.com', '$2y$10$q5qrN1zxR.hbYLZSMGBjBug2MCDuH0z1gCeIkF9JynGVcwSzD3tPy', 'user', 'active', '2026-02-10 06:16:48', '2026-02-10 06:19:19', '2026-02-10 06:19:19', 0),
(15, 'emy', 'yes', 'amyyes@gmail.com', '$2y$10$erUHNgHJIhgUKNfPYuzsUuXBla/bhbLtsiyxKISjaeXRCpBUHvMAO', 'user', 'active', '2026-02-10 06:21:24', '2026-02-10 06:22:55', '2026-02-10 06:22:55', 0),
(16, 'marc', 'aguilar', 'marcaguilar@gmail.com', '$2y$10$MDx6ItGazK7WDwn11I/T8eVelSoC2bybWCW8JNQkqwJfglJYX0MIG', 'user', 'active', '2026-02-10 06:31:48', '2026-02-10 06:31:58', NULL, 1),
(17, 'elvie', 'jovero', 'elviejoveroo@gmail.com', '$2y$10$/X2rO64286/198CpuLJGBOaaBTwbhq6LfnXfYARr.wp0u2XqT.l3G', 'user', 'active', '2026-02-10 06:36:37', '2026-02-10 06:36:42', NULL, 1),
(18, 'user', 'example', 'suer@gmail.com', '$2y$10$7HUQgKFOF4paxvtDzGAJ.ez8LSHiFCSBW0.WS3CbOPKtl7ETN8P36', 'user', 'active', '2026-02-10 07:36:37', '2026-02-10 07:36:49', NULL, 1),
(19, 'ian', 'jovero', 'iankageyama25@gmail.com', '$2y$10$L/Trri3uiDIKHKmmDClSBOSV.7wpxTdPpH5kdoMmTQv1vlecsOSU.', 'user', 'active', '2026-02-10 08:26:02', '2026-02-10 08:26:24', NULL, 1),
(21, 'ian ', 'jovero', 'ianjovero28', '$2y$10$CBO/1ILZgtGvgU/79kfS6elv86a89Q8QKdPaaMU3Ruo4FimGf86uW', '', 'active', '2026-02-10 22:41:10', '2026-02-10 22:41:10', NULL, 0),
(22, 'ian', 'perdon', 'ianperdon@gmail.com', '$2y$10$nvUi8Z.9g5YQkstENwEvj.p4JHPBaFf1U72EJPkWqQ//WVo6319.W', 'user', 'active', '2026-02-10 22:44:26', '2026-02-10 22:44:31', NULL, 1),
(25, 'Ian', 'Jaime', 'ianjaime@gmail.com', '$2y$10$V0AWwP9vNsr.XnL1r/xrKuH9ZrLiNvIXCktdnM8lpsaHBX/UG9DBS', '', 'active', '2026-02-11 00:39:57', '2026-02-11 00:39:57', NULL, 0),
(26, 'jean', 'marcc', 'jeanmarcc@gmail.com', '$2y$10$kPH/pqQkouADBYR1xR3HkOybWXbtt0CguGBTGlfzUIlJvkB5T9etC', '', 'active', '2026-02-11 00:49:33', '2026-02-11 00:49:33', NULL, 0),
(27, 'amiel', 'jake', 'amieljake@gmail.com', '$2y$10$FgrZJU5WOFa0ViJhNOBZsOC7kT48bqgM6RxoNJoCL7.8GWG247Wl2', '', 'active', '2026-02-11 00:55:44', '2026-02-11 00:55:44', NULL, 0),
(33, 'Luke', 'Chiang', 'amieljake09@gmail.com', '$2y$10$59bWrLvvSh0eOfNQu6Fq..QqtnRgb.4FB2xjtJs.b0wMfC8/E4N0m', 'tour_guide', 'active', '2026-02-15 20:05:22', '2026-02-15 20:05:22', NULL, 0),
(35, 'Luke', 'Chiang', 'amieljake929@gmail.com', '$2y$10$1yNIiyqDDBe8kZ1Y.PlnVemBEsJoxf3CxHVc9WaRj5JHvfWQWj/ru', 'tour_guide', 'active', '2026-02-19 09:13:12', '2026-02-19 09:13:12', NULL, 0),
(40, 'Jean Marc', 'Aguilar', 'jeanmarcaguilar829@gmail.com', '$2y$10$HW.T2T7lI879YrHtzXZU2uPgvNMrrapQOTEliUZn.LC9Pjl/1MHpK', 'user', 'active', '2026-02-19 13:22:43', '2026-02-24 19:14:41', '2026-02-24 19:14:41', 1),
(42, 'test@example.com', 'test123', 'Test', '$2y$10$.hzozDusL7tFiKuGzZatS.F/8N36BPTE20fb8e8luQtfnt.Vp3D5m', 'user', 'active', '2026-02-23 20:58:38', '2026-02-23 20:58:38', NULL, 0),
(43, 'Robert', 'Diaz', 'ianjovero2528@gmail.com', '$2y$10$LjL0YcIA0mHqGhnEwY9R8.5Ezb4Qql5eHJLiVhGyVMaYEh1gsoF4K', 'tour_guide', 'active', '2026-02-24 02:42:42', '2026-02-24 02:47:23', '2026-02-24 02:47:23', 0);

-- --------------------------------------------------------

--
-- Table structure for table `user_favorites`
--

CREATE TABLE `user_favorites` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `item_type` enum('tourist_spot','tour_guide','hotel') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_management_settings`
--

CREATE TABLE `user_management_settings` (
  `id` int(11) NOT NULL,
  `setting_key` varchar(50) NOT NULL,
  `setting_value` text NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_management_settings`
--

INSERT INTO `user_management_settings` (`id`, `setting_key`, `setting_value`, `updated_at`) VALUES
(1, 'module_title', 'User Management', '2026-02-06 08:07:12'),
(2, 'module_subtitle', 'Manage, monitor and authorize system users', '2026-02-06 08:07:12'),
(3, 'default_user_limit', '15', '2026-02-06 08:07:12'),
(4, 'admin_mark_label', 'A', '2026-02-06 08:07:12');

-- --------------------------------------------------------

--
-- Table structure for table `user_preferences`
--

CREATE TABLE `user_preferences` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `category` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_preferences`
--

INSERT INTO `user_preferences` (`id`, `user_id`, `category`, `created_at`) VALUES
(1, 16, 'nature', '2026-02-10 06:31:58'),
(2, 17, 'nature', '2026-02-10 06:36:42'),
(3, 18, 'nature', '2026-02-10 07:36:49'),
(4, 18, 'religious', '2026-02-10 07:36:49'),
(5, 19, 'nature', '2026-02-10 08:26:24'),
(6, 19, 'religious', '2026-02-10 08:26:24'),
(9, 22, 'nature', '2026-02-10 22:44:31'),
(10, 22, 'religious', '2026-02-10 22:44:31'),
(22, 40, 'nature', '2026-02-19 13:22:50'),
(23, 40, 'farm', '2026-02-19 13:22:50'),
(24, 40, 'park', '2026-02-19 13:22:50'),
(25, 40, 'adventure', '2026-02-19 13:22:50'),
(26, 40, 'cultural', '2026-02-19 13:22:50'),
(27, 40, 'religious', '2026-02-19 13:22:50'),
(28, 40, 'entertainment', '2026-02-19 13:22:50'),
(29, 40, 'food', '2026-02-19 13:22:50');

-- --------------------------------------------------------

--
-- Table structure for table `user_settings`
--

CREATE TABLE `user_settings` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `notifications` tinyint(1) DEFAULT 1,
  `email_updates` tinyint(1) DEFAULT 1,
  `share_history` tinyint(1) DEFAULT 0,
  `public_profile` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_settings`
--

INSERT INTO `user_settings` (`id`, `user_id`, `notifications`, `email_updates`, `share_history`, `public_profile`, `created_at`, `updated_at`) VALUES
(1, 4, 1, 1, 0, 0, '2026-02-01 02:00:00', '2026-02-01 02:00:00'),
(2, 5, 1, 0, 1, 0, '2026-02-02 03:00:00', '2026-02-02 03:00:00'),
(3, 1, 0, 1, 0, 1, '2026-02-03 04:00:00', '2026-02-03 04:00:00');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin_activity`
--
ALTER TABLE `admin_activity`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_admin_id` (`admin_id`),
  ADD KEY `idx_action` (`action`),
  ADD KEY `idx_created_at` (`created_at`);

--
-- Indexes for table `admin_dashboards`
--
ALTER TABLE `admin_dashboards`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_admin_id` (`admin_id`);

--
-- Indexes for table `admin_dashboard_settings`
--
ALTER TABLE `admin_dashboard_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `setting_key` (`setting_key`);

--
-- Indexes for table `admin_menu_items`
--
ALTER TABLE `admin_menu_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_display_order` (`display_order`),
  ADD KEY `idx_is_active` (`is_active`);

--
-- Indexes for table `admin_permissions_table`
--
ALTER TABLE `admin_permissions_table`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_admin_id` (`admin_id`),
  ADD KEY `idx_module` (`module`);

--
-- Indexes for table `admin_preferences`
--
ALTER TABLE `admin_preferences`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_admin_setting` (`admin_id`,`setting_key`),
  ADD KEY `idx_admin_id` (`admin_id`),
  ADD KEY `idx_category` (`category`);

--
-- Indexes for table `admin_users`
--
ALTER TABLE `admin_users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`);

--
-- Indexes for table `analytics_settings`
--
ALTER TABLE `analytics_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `idx_key` (`setting_key`);

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_guide_id` (`guide_id`),
  ADD KEY `idx_booking_date` (`booking_date`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_booking_reference` (`booking_reference`);

--
-- Indexes for table `booking_settings`
--
ALTER TABLE `booking_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `idx_key` (`setting_key`);

--
-- Indexes for table `destination_settings`
--
ALTER TABLE `destination_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `idx_key` (`setting_key`);

--
-- Indexes for table `guide_destinations`
--
ALTER TABLE `guide_destinations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_guide_destination` (`guide_id`,`destination_id`),
  ADD KEY `idx_guide_id` (`guide_id`),
  ADD KEY `idx_destination_id` (`destination_id`);

--
-- Indexes for table `homepage_content`
--
ALTER TABLE `homepage_content`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_content_type` (`content_type`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_display_order` (`display_order`);

--
-- Indexes for table `hotels`
--
ALTER TABLE `hotels`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_category` (`category`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_rating` (`rating`);

--
-- Indexes for table `hotel_settings`
--
ALTER TABLE `hotel_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `idx_key` (`setting_key`);

--
-- Indexes for table `local_culture`
--
ALTER TABLE `local_culture`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_display_order` (`display_order`),
  ADD KEY `idx_is_active` (`is_active`);

--
-- Indexes for table `login_activity`
--
ALTER TABLE `login_activity`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_id` (`user_id`);

--
-- Indexes for table `otp_codes`
--
ALTER TABLE `otp_codes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_email` (`user_id`,`email`),
  ADD KEY `idx_otp_code` (`otp_code`),
  ADD KEY `idx_expires_at` (`expires_at`);

--
-- Indexes for table `registration_tour_guide`
--
ALTER TABLE `registration_tour_guide`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `dot_accreditation` (`dot_accreditation`),
  ADD KEY `status` (`status`),
  ADD KEY `application_date` (`application_date`),
  ADD KEY `specialization` (`specialization`);

--
-- Indexes for table `report_settings`
--
ALTER TABLE `report_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `idx_key` (`setting_key`);

--
-- Indexes for table `restaurants`
--
ALTER TABLE `restaurants`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_category` (`category`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_rating` (`rating`);

--
-- Indexes for table `saved_tours`
--
ALTER TABLE `saved_tours`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_id` (`user_id`);

--
-- Indexes for table `status_logs`
--
ALTER TABLE `status_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `table_record` (`table_name`,`record_id`),
  ADD KEY `changed_at` (`changed_at`);

--
-- Indexes for table `tourist_spots`
--
ALTER TABLE `tourist_spots`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_category` (`category`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_rating` (`rating`);

--
-- Indexes for table `tour_guides`
--
ALTER TABLE `tour_guides`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_category` (`category`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_rating` (`rating`),
  ADD KEY `idx_verified` (`verified`),
  ADD KEY `fk_tour_guides_user_id` (`user_id`);

--
-- Indexes for table `tour_guide_availability`
--
ALTER TABLE `tour_guide_availability`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_tour_guide_availability_guide_id` (`tour_guide_id`),
  ADD KEY `idx_tour_guide_availability_date` (`available_date`);

--
-- Indexes for table `tour_guide_languages`
--
ALTER TABLE `tour_guide_languages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `registration_id` (`registration_id`);

--
-- Indexes for table `tour_guide_profiles`
--
ALTER TABLE `tour_guide_profiles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_tour_guide_profiles_user_id` (`user_id`);

--
-- Indexes for table `tour_guide_reviews`
--
ALTER TABLE `tour_guide_reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_tour_guide_reviews_guide_id` (`tour_guide_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `tour_guide_settings`
--
ALTER TABLE `tour_guide_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `idx_key` (`setting_key`);

--
-- Indexes for table `travel_tips`
--
ALTER TABLE `travel_tips`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_category` (`category`),
  ADD KEY `idx_display_order` (`display_order`),
  ADD KEY `idx_is_active` (`is_active`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `user_favorites`
--
ALTER TABLE `user_favorites`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_item` (`item_id`,`item_type`);

--
-- Indexes for table `user_management_settings`
--
ALTER TABLE `user_management_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `idx_key` (`setting_key`);

--
-- Indexes for table `user_preferences`
--
ALTER TABLE `user_preferences`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_user_category` (`user_id`,`category`);

--
-- Indexes for table `user_settings`
--
ALTER TABLE `user_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_user_settings` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin_activity`
--
ALTER TABLE `admin_activity`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=156;

--
-- AUTO_INCREMENT for table `admin_dashboards`
--
ALTER TABLE `admin_dashboards`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `admin_dashboard_settings`
--
ALTER TABLE `admin_dashboard_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `admin_menu_items`
--
ALTER TABLE `admin_menu_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `admin_permissions_table`
--
ALTER TABLE `admin_permissions_table`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `admin_preferences`
--
ALTER TABLE `admin_preferences`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `admin_users`
--
ALTER TABLE `admin_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `analytics_settings`
--
ALTER TABLE `analytics_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=48;

--
-- AUTO_INCREMENT for table `booking_settings`
--
ALTER TABLE `booking_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `destination_settings`
--
ALTER TABLE `destination_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `guide_destinations`
--
ALTER TABLE `guide_destinations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `homepage_content`
--
ALTER TABLE `homepage_content`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `hotels`
--
ALTER TABLE `hotels`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `hotel_settings`
--
ALTER TABLE `hotel_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `local_culture`
--
ALTER TABLE `local_culture`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `login_activity`
--
ALTER TABLE `login_activity`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=247;

--
-- AUTO_INCREMENT for table `otp_codes`
--
ALTER TABLE `otp_codes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=96;

--
-- AUTO_INCREMENT for table `registration_tour_guide`
--
ALTER TABLE `registration_tour_guide`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `report_settings`
--
ALTER TABLE `report_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `restaurants`
--
ALTER TABLE `restaurants`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `saved_tours`
--
ALTER TABLE `saved_tours`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `status_logs`
--
ALTER TABLE `status_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tourist_spots`
--
ALTER TABLE `tourist_spots`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `tour_guides`
--
ALTER TABLE `tour_guides`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `tour_guide_availability`
--
ALTER TABLE `tour_guide_availability`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tour_guide_languages`
--
ALTER TABLE `tour_guide_languages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `tour_guide_profiles`
--
ALTER TABLE `tour_guide_profiles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `tour_guide_reviews`
--
ALTER TABLE `tour_guide_reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tour_guide_settings`
--
ALTER TABLE `tour_guide_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `travel_tips`
--
ALTER TABLE `travel_tips`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT for table `user_favorites`
--
ALTER TABLE `user_favorites`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `user_management_settings`
--
ALTER TABLE `user_management_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `user_preferences`
--
ALTER TABLE `user_preferences`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `user_settings`
--
ALTER TABLE `user_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `admin_activity`
--
ALTER TABLE `admin_activity`
  ADD CONSTRAINT `admin_activity_ibfk_1` FOREIGN KEY (`admin_id`) REFERENCES `admin_users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `admin_dashboards`
--
ALTER TABLE `admin_dashboards`
  ADD CONSTRAINT `admin_dashboards_ibfk_1` FOREIGN KEY (`admin_id`) REFERENCES `admin_users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `admin_permissions_table`
--
ALTER TABLE `admin_permissions_table`
  ADD CONSTRAINT `admin_permissions_table_ibfk_1` FOREIGN KEY (`admin_id`) REFERENCES `admin_users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `admin_preferences`
--
ALTER TABLE `admin_preferences`
  ADD CONSTRAINT `admin_preferences_ibfk_1` FOREIGN KEY (`admin_id`) REFERENCES `admin_users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `admin_users`
--
ALTER TABLE `admin_users`
  ADD CONSTRAINT `admin_users_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_bookings_guide_id` FOREIGN KEY (`guide_id`) REFERENCES `tour_guides` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `guide_destinations`
--
ALTER TABLE `guide_destinations`
  ADD CONSTRAINT `fk_guide_destinations_destination` FOREIGN KEY (`destination_id`) REFERENCES `tourist_spots` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_guide_destinations_guide` FOREIGN KEY (`guide_id`) REFERENCES `tour_guides` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `login_activity`
--
ALTER TABLE `login_activity`
  ADD CONSTRAINT `login_activity_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `otp_codes`
--
ALTER TABLE `otp_codes`
  ADD CONSTRAINT `otp_codes_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `saved_tours`
--
ALTER TABLE `saved_tours`
  ADD CONSTRAINT `saved_tours_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `tour_guides`
--
ALTER TABLE `tour_guides`
  ADD CONSTRAINT `fk_tour_guides_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `tour_guide_availability`
--
ALTER TABLE `tour_guide_availability`
  ADD CONSTRAINT `tour_guide_availability_ibfk_1` FOREIGN KEY (`tour_guide_id`) REFERENCES `tour_guide_profiles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `tour_guide_languages`
--
ALTER TABLE `tour_guide_languages`
  ADD CONSTRAINT `tour_guide_languages_ibfk_1` FOREIGN KEY (`registration_id`) REFERENCES `registration_tour_guide` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `tour_guide_profiles`
--
ALTER TABLE `tour_guide_profiles`
  ADD CONSTRAINT `tour_guide_profiles_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `tour_guide_reviews`
--
ALTER TABLE `tour_guide_reviews`
  ADD CONSTRAINT `tour_guide_reviews_ibfk_1` FOREIGN KEY (`tour_guide_id`) REFERENCES `tour_guide_profiles` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `tour_guide_reviews_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_favorites`
--
ALTER TABLE `user_favorites`
  ADD CONSTRAINT `fk_user_favorites_item` FOREIGN KEY (`item_id`) REFERENCES `tourist_spots` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_user_favorites_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_preferences`
--
ALTER TABLE `user_preferences`
  ADD CONSTRAINT `user_preferences_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_settings`
--
ALTER TABLE `user_settings`
  ADD CONSTRAINT `user_settings_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
