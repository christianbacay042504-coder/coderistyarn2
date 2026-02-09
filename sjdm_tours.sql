-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 06, 2026 at 01:06 PM
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
(24, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '::1', NULL, '2026-02-06 12:05:59');

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

INSERT INTO `bookings` (`id`, `user_id`, `guide_id`, `tour_name`, `destination`, `booking_date`, `check_in_date`, `check_out_date`, `number_of_people`, `contact_number`, `email`, `special_requests`, `total_amount`, `payment_method`, `status`, `booking_reference`, `created_at`, `updated_at`) VALUES
(1, 4, 1, 'Mount Balagbag Adventure Trek', NULL, '2026-02-15', NULL, NULL, 0, NULL, NULL, 'Please provide vegetarian lunch options', 1200.00, 'pay_later', 'confirmed', NULL, '2026-02-01 02:30:00', '2026-02-01 02:30:00'),
(2, 5, 2, 'Cultural Heritage Tour', NULL, '2026-02-20', NULL, NULL, 0, NULL, NULL, 'Need wheelchair accessible transportation', 800.00, 'pay_later', 'confirmed', NULL, '2026-02-02 06:20:00', '2026-02-02 06:20:00'),
(3, 4, 3, 'Nature Photography Workshop', NULL, '2026-02-18', NULL, NULL, 0, NULL, NULL, 'Bring extra camera batteries', 1500.00, 'pay_later', 'pending', NULL, '2026-02-03 01:15:00', '2026-02-03 01:15:00'),
(4, 5, 4, 'Historical Walking Tour', NULL, '2026-02-25', NULL, NULL, 0, NULL, NULL, 'Include visit to local museums', 600.00, 'pay_later', 'confirmed', NULL, '2026-02-04 08:45:00', '2026-02-04 08:45:00'),
(5, 4, 5, 'Culinary Food Tour', NULL, '2026-02-22', NULL, NULL, 0, NULL, NULL, 'Allergic to seafood', 1000.00, 'pay_later', 'confirmed', NULL, '2026-02-05 05:30:00', '2026-02-05 05:30:00'),
(6, 1, 1, 'Sunset Mountain Hike', NULL, '2026-03-01', NULL, NULL, 0, NULL, NULL, 'Group of 4 people', 900.00, 'pay_later', 'pending', NULL, '2026-02-05 07:00:00', '2026-02-05 07:00:00'),
(7, 5, 2, 'Religious Sites Tour', NULL, '2026-02-28', NULL, NULL, 0, NULL, NULL, 'Include transportation from hotel', 500.00, 'pay_later', 'confirmed', NULL, '2026-02-05 08:20:00', '2026-02-05 08:20:00'),
(8, 4, 3, 'Bird Watching Expedition', NULL, '2026-03-05', NULL, NULL, 0, NULL, NULL, 'Need binoculars rental', 1800.00, 'pay_later', 'pending', NULL, '2026-02-05 09:10:00', '2026-02-05 09:10:00');

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
(8, 5, '2026-02-03 04:00:00', '192.168.1.101', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36', 'success');

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
(1, 'Mount Balagbag', 'A popular hiking destination offering breathtaking views of the city and surrounding provinces. Perfect for adventure seekers and nature lovers.', 'nature', 'San Jose del Monte, Bulacan', NULL, NULL, '500-1000', 'moderate', NULL, NULL, NULL, NULL, NULL, NULL, '/images/mount-balagbag.jpg', NULL, NULL, 4.50, 0, 'active', '2026-01-15 02:00:00', '2026-01-15 02:00:00'),
(2, 'Grotto of San Jose del Monte', 'A peaceful religious site perfect for prayer and reflection. Features beautiful architecture and serene surroundings.', 'religious', 'San Jose del Monte, Bulacan', NULL, NULL, 'Free', 'moderate', NULL, NULL, NULL, NULL, NULL, NULL, '/images/grotto.jpg', NULL, NULL, 4.80, 0, 'active', '2026-01-16 03:00:00', '2026-01-16 03:00:00'),
(3, 'Tungkong Mangga', 'Historical landmark and local market area showcasing the rich culture and traditions of San Jose del Monte.', 'historical', 'San Jose del Monte, Bulacan', NULL, NULL, 'Free', 'moderate', NULL, NULL, NULL, NULL, NULL, NULL, '/images/tungkong-mangga.jpg', NULL, NULL, 4.20, 0, 'active', '2026-01-17 04:00:00', '2026-01-17 04:00:00'),
(4, 'Lambingan Bridge', 'Scenic bridge perfect for photography and romantic walks. Offers beautiful sunset views.', 'urban', 'San Jose del Monte, Bulacan', NULL, NULL, 'Free', 'moderate', NULL, NULL, NULL, NULL, NULL, NULL, '/images/lambingan-bridge.jpg', NULL, NULL, 4.60, 0, 'active', '2026-01-18 05:00:00', '2026-01-18 05:00:00'),
(5, 'San Jose del Monte City Hall', 'Modern government building showcasing impressive architecture and local governance. Open for public tours.', 'historical', 'San Jose del Monte, Bulacan', NULL, NULL, 'Free', 'moderate', NULL, NULL, NULL, NULL, NULL, NULL, '/images/city-hall.jpg', NULL, NULL, 4.30, 0, 'active', '2026-01-19 06:00:00', '2026-01-19 06:00:00'),
(6, 'Kaypian Park', 'Family-friendly park with playgrounds, picnic areas, and recreational facilities. Perfect for weekend outings.', 'park', 'San Jose del Monte, Bulacan', NULL, NULL, '50-200', 'moderate', NULL, NULL, NULL, NULL, NULL, NULL, '/images/kaypian-park.jpg', NULL, NULL, 4.40, 0, 'active', '2026-01-20 07:00:00', '2026-01-20 07:00:00'),
(7, 'Minuyan Reservoir', 'Beautiful reservoir offering fishing, boating, and nature activities. Great for outdoor enthusiasts.', 'nature', 'San Jose del Monte, Bulacan', NULL, NULL, '100-300', 'moderate', NULL, NULL, NULL, NULL, NULL, NULL, '/images/minuyan-reservoir.jpg', NULL, NULL, 4.70, 0, 'active', '2026-01-21 08:00:00', '2026-01-21 08:00:00'),
(8, 'San Lorenzo Ruiz Parish', 'Beautiful Catholic church with rich history and stunning architecture. Regular masses and religious services.', 'religious', 'San Jose del Monte, Bulacan', NULL, NULL, 'Free', 'moderate', NULL, NULL, NULL, NULL, NULL, NULL, '/images/san-lorenzo-ruiz.jpg', NULL, NULL, 4.90, 0, 'active', '2026-01-22 09:00:00', '2026-01-22 09:00:00'),
(9, 'wdwdwdwd', 'dwdwdwdw', '', 'dwdwdwdwd', 'dwwdwdwdw', 'dwdwdwdw', 'dwdwdwd', 'easy', 'dwdwdwd', 'dwdwdwdwd', 'dwdwdwdw', 'dwdwdwddwd', 'dwdwddwd', 'https://instasize.com/p/ec7df4bac8564acb65519c48bdd4308fa971f82e04c7a000c70e58df08b6f61f', 'https://instasize.com/p/ec7df4bac8564acb65519c48bdd4308fa971f82e04c7a000c70e58df08b6f61f', NULL, NULL, 5.00, 100, 'active', '2026-02-06 08:49:15', '2026-02-06 09:33:53'),
(11, 'dwdwd', 'wdwdwdwd', 'historical', 'wdwdwd', 'dwdwdwd', 'dwdwdwd', 'wdwdwdw', 'difficult', 'dwdwdwd', 'wdwdwdw', 'dwdwdwdwd', 'dwdwwdd', 'dwdwdwd', 'https://instasize.com/p/ec7df4bac8564acb65519c48bdd4308fa971f82e04c7a000c70e58df08b6f61f', 'https://instasize.com/p/ec7df4bac8564acb65519c48bdd4308fa971f82e04c7a000c70e58df08b6f61f', NULL, NULL, 5.00, 5, 'active', '2026-02-06 09:34:40', '2026-02-06 09:34:40'),
(12, 'Abes Farm', 'Abes Farm is a premier agri-tourism destination offering visitors an authentic \r\n                        Filipino farming experience. Established over two decades ago, this family-owned \r\n                        farm provides educational and recreational activities that showcase traditional \r\n                        agricultural practices in a modern, sustainable setting.', 'farm', 'San Jose del Monte, Bulacan', 'San Jose del Monte, Bulacan', '8:00 AM - 5:00 PM', 'Free', 'easy', '', 'All year round', '', 'Shed', '', '', '', NULL, NULL, 0.00, 0, 'active', '2026-02-06 09:40:50', '2026-02-06 09:40:50'),
(13, 'Burong Falls', 'Burong Falls is one of San Jose del Monte\'s most spectacular natural attractions, featuring \r\n                        impressive multi-tiered cascades surrounded by pristine rainforest. This destination is part \r\n                        of a trio falls tour (Burong, Otso-Otso, and Kaytitinga Falls) with a mandatory tour guide \r\n                        fee of PHP 350 per group (max 5 pax), arranged through the tourism office.', 'nature', 'San Jose del Monte, Bulacan', 'San Jose del Monte, Bulacan', '6:00 AM - 6:00 PM', '₱350', 'difficult', '', 'Dry season', 'Hiking, Trekking', '', '', '', '', NULL, NULL, 0.00, 0, 'active', '2026-02-06 09:40:50', '2026-02-06 09:40:50'),
(14, 'City Oval & People\'s Park', 'Spanning over 10 hectares in the heart of San Jose del Monte, the City Oval and People\'s Park \r\n                        complex serves as the city\'s premier sports, recreation, and community event facility. This \r\n                        multi-purpose complex combines world-class athletic facilities with beautiful green spaces.', 'park', 'San Jose del Monte, Bulacan', 'San Jose del Monte, Bulacan', '5:00 AM - 10:00 PM', 'Free', 'moderate', '', 'All year round', '', 'Parking, Shed', '', '', '', NULL, NULL, 0.00, 0, 'active', '2026-02-06 09:40:50', '2026-02-06 09:40:50'),
(15, 'Kaytitinga Falls', 'Kaytitinga Falls is one of the most picturesque waterfalls in San Jose del Monte, \r\n                        featuring three stunning tiers of cascading water surrounded by lush tropical forest. \r\n                        This destination is part of a trio falls tour (Burong, Otso-Otso, and Kaytitinga Falls) with a mandatory tour guide \r\n                        fee of PHP 350 per group (max 5 pax), arranged through the tourism office.', 'nature', 'San Jose del Monte, Bulacan', 'San Jose del Monte, Bulacan', '7:00 AM - 10:00 AM', '₱350', 'difficult', '', 'Morning', 'Hiking, Swimming', '', '', '', '', NULL, NULL, 0.00, 0, 'active', '2026-02-06 09:40:50', '2026-02-06 09:40:50'),
(16, 'Mt. Balagbag', 'Mt. Balagbag, standing at 777 meters above sea level, is one of the most popular hiking \r\n                        destinations near Metro Manila. Known as the \"Mt. Pulag of Bulacan,\" it offers stunning \r\n                        360-degree panoramic views of Metro Manila, Laguna de Bay, and the surrounding mountain ranges.', 'nature', 'San Jose del Monte, Bulacan', 'San Jose del Monte, Bulacan', '6:00 AM - 6:00 PM', '₱25', 'easy', '', 'Morning', 'Hiking, Camping', 'Shed', '', '', '', NULL, NULL, 0.00, 0, 'active', '2026-02-06 09:40:50', '2026-02-06 09:40:50'),
(17, 'Otso-Otso Falls', 'Named after the Filipino word \"otso\" meaning eight, Otso-Otso Falls is a magnificent \r\n                        series of eight interconnected waterfalls located in the pristine wilderness of San Jose del Monte. \r\n                        This destination is part of a trio falls tour (Burong, Otso-Otso, and Kaytitinga Falls) with a mandatory tour guide \r\n                        fee of PHP 350 per group (max 5 pax), arranged through the tourism office.', 'nature', 'San Jose del Monte, Bulacan', 'San Jose del Monte, Bulacan', '7:00 AM - 4:00 PM', '₱350', 'easy', '', 'Dry season', 'Hiking, Swimming', '', '', '', '', NULL, NULL, 0.00, 0, 'active', '2026-02-06 09:40:50', '2026-02-06 09:40:50'),
(18, 'Our Lady of Lourdes Grotto', 'The Our Lady of Lourdes Grotto is a renowned spiritual sanctuary in San Jose del Monte, \r\n                        established in 1958 as a replica of the famous Lourdes Grotto in France. This peaceful \r\n                        religious site attracts thousands of pilgrims and visitors seeking spiritual renewal, \r\n                        healing, and divine intervention.', 'nature', 'San Jose del Monte, Bulacan', 'San Jose del Monte, Bulacan', '5:00 AM - 8:00 PM', 'Free', 'moderate', '', 'All year round', 'Photography', 'Shed', '', '', '', NULL, NULL, 0.00, 0, 'active', '2026-02-06 09:40:50', '2026-02-06 09:40:50'),
(19, 'Padre Pio Shrine', 'The Padre Pio Shrine in San Jose del Monte is a major Catholic pilgrimage site dedicated to \r\n                        Saint Padre Pio of Pietrelcina, known for his stigmata and miraculous healings. Established in 1998, \r\n                        this spiritual complex has become one of the most visited religious sites in Bulacan, attracting \r\n                        devotees seeking spiritual guidance, healing, and miracles.', 'nature', 'San Jose del Monte, Bulacan', 'San Jose del Monte, Bulacan', '8:00 AM - 11:00 AM', 'Free', 'moderate', '', 'All year round', 'Photography', 'Shed, Store', '', '', '', NULL, NULL, 0.00, 0, 'active', '2026-02-06 09:40:50', '2026-02-06 09:40:50'),
(20, 'Paradise Hill Farm', 'Paradise Hill Farm is a 25-hectare integrated sustainable farm that combines modern agricultural \r\n                        practices with eco-tourism experiences. Established in 2010, this farm utilizes organic farming \r\n                        methods, renewable energy, and water conservation techniques to create a model for sustainable \r\n                        agriculture in San Jose del Monte.', 'farm', 'San Jose del Monte, Bulacan', 'San Jose del Monte, Bulacan', '8:00 AM - 5:00 PM', '₱200', 'moderate', '', 'All year round', 'Photography', 'Shed, Restaurant', '', '', '', NULL, NULL, 0.00, 0, 'active', '2026-02-06 09:40:50', '2026-02-06 09:40:50'),
(21, 'The Rising Heart Monument', 'Standing majestically at the entrance of San Jose del Monte City, The Rising Heart Monument \r\n                        is a 15-meter tall steel sculpture that has become the city\'s most recognizable landmark. \r\n                        Completed in 2018 to celebrate the city\'s conversion from municipality to component city, \r\n                        this monument symbolizes the city\'s rising prosperity, love for community, and resilient spirit.', 'nature', 'San Jose del Monte, Bulacan', 'San Jose del Monte, Bulacan', '5:30 AM - 6:30 AM', 'Free', 'moderate', '', 'All year round', 'Photography', 'Parking, Restroom, Shed', '', '', '', NULL, NULL, 0.00, 0, 'active', '2026-02-06 09:40:50', '2026-02-06 09:40:50'),
(22, 'Tungtong Falls', 'Tungtong Falls is a spectacular 25-meter waterfall located within a dramatic canyon formation \r\n                        in San Jose del Monte. Named after the local term \"tungtong\" meaning \"to climb or ascend,\" \r\n                        this waterfall features a unique rock amphitheater that creates perfect acoustics and \r\n                        breathtaking visual effects when sunlight hits the cascading water.', 'nature', 'San Jose del Monte, Bulacan', 'San Jose del Monte, Bulacan', '7:00 AM - 5:00 PM', '₱1', 'difficult', '', 'All year round', 'Hiking, Swimming', 'Parking', '', '', '', NULL, NULL, 0.00, 0, 'active', '2026-02-06 09:40:50', '2026-02-06 09:40:50');

-- --------------------------------------------------------

--
-- Table structure for table `tour_guides`
--

CREATE TABLE `tour_guides` (
  `id` int(11) NOT NULL,
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
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tour_guides`
--

INSERT INTO `tour_guides` (`id`, `name`, `specialty`, `category`, `description`, `bio`, `areas_of_expertise`, `rating`, `review_count`, `price_range`, `price_min`, `price_max`, `languages`, `contact_number`, `email`, `schedules`, `experience_years`, `group_size`, `verified`, `total_tours`, `photo_url`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Carlos Mendoza', 'Adventure Tours', 'general', NULL, 'Experienced adventure guide with expertise in mountain trekking and outdoor activities. Certified first aid provider and wilderness survival trained.', NULL, 4.80, 0, NULL, NULL, NULL, NULL, '+639123456789', 'carlos.mendoza@sjdmtours.com', NULL, 5, NULL, 0, 0, '/images/guide-carlos.jpg', 'active', '2026-01-10 01:00:00', '2026-01-10 01:00:00'),
(2, 'Maria Santos', 'Cultural Tours', 'general', NULL, 'Passionate about sharing the rich cultural heritage of San Jose del Monte. Fluent in English, Tagalog, and basic Japanese.', NULL, 4.90, 0, NULL, NULL, NULL, NULL, '+639987654321', 'maria.santos@sjdmtours.com', NULL, 3, NULL, 0, 0, '/images/guide-maria.jpg', 'active', '2026-01-11 02:00:00', '2026-01-11 02:00:00'),
(3, 'Roberto Reyes', 'Nature & Photography', 'general', NULL, 'Professional photographer and nature guide. Specializes in wildlife photography and bird watching tours.', NULL, 4.70, 0, NULL, NULL, NULL, NULL, '+639567890123', 'roberto.reyes@sjdmtours.com', NULL, 7, NULL, 0, 0, '/images/guide-roberto.jpg', 'active', '2026-01-12 03:00:00', '2026-01-12 03:00:00'),
(4, 'Ana Cruz', 'Historical Tours', 'general', NULL, 'History enthusiast with deep knowledge of San Jose del Monte\'s colonial past and historical landmarks.', NULL, 4.60, 0, NULL, NULL, NULL, NULL, '+6393456789012', 'ana.cruz@sjdmtours.com', NULL, 4, NULL, 0, 0, '/images/guide-ana.jpg', 'active', '2026-01-13 04:00:00', '2026-01-13 04:00:00'),
(5, 'David Lee', 'Food & Culinary Tours', 'general', NULL, 'Food expert offering culinary tours showcasing local delicacies and traditional Bulacan cuisine.', NULL, 4.50, 0, NULL, NULL, NULL, NULL, '+639234567890', 'david.lee@sjdmtours.com', NULL, 2, NULL, 0, 0, '/images/guide-david.jpg', 'active', '2026-01-14 05:00:00', '2026-01-14 05:00:00'),
(6, 'dwdwdwd', 'dadwadwadwa', '', 'dwdwdwdw', 'dwdwdwdwd', 'wdwdwdw', 5.00, 900, 'Budget', 0.00, 0.00, 'English', '656565656', 'wdwdwdwd@g', 'dwdwdwdwdwd', 7, '10', 1, 4, 'https://instasize.com/p/ec7df4bac8564acb65519c48bdd4308fa971f82e04c7a000c70e58df08b6f61f', 'active', '2026-02-06 08:07:55', '2026-02-06 08:07:55'),
(7, 'cwwwd', 'dwdwdwd', '', 'dwdwdwdwd', 'dwdwdwdwd', 'wdwdwdwd', 1.60, 8, 'Budget', 0.00, 0.00, 'dwdwd', '4343434', 'wdwdwd@g', 'dwdwdwd', 8, '7', 1, 6, 'https://instasize.com/p/ec7df4bac8564acb65519c48bdd4308fa971f82e04c7a000c70e58df08b6f61f', 'active', '2026-02-06 08:23:15', '2026-02-06 08:23:15'),
(8, 'Ricardo Fernandez', 'Waterfall Tours', 'waterfall', 'Specialized guide for waterfall destinations and nature treks', 'Expert in leading tours to Burong Falls, Kaytitinga Falls, Otso-Otso Falls, and Tungtong Falls. Certified in wilderness safety and rescue operations.', 'Waterfall trekking, Nature photography, Swimming safety', 4.70, 25, '800-1500', 800.00, 1500.00, 'English, Tagalog', '+639123456788', 'ricardo.fernandez@sjdmtours.com', 'Available daily 6AM-6PM', 6, '1-8', 1, 45, '/images/guide-ricardo.jpg', 'active', '2026-02-07 00:00:00', '2026-02-07 00:00:00'),
(9, 'Sofia Martinez', 'Religious Sites', 'historical', 'Expert guide for religious and historical destinations', 'Deep knowledge of Catholic heritage sites and religious landmarks. Provides spiritual and historical context for pilgrimages and cultural tours.', 'Religious history, Cultural heritage, Pilgrimage guidance', 4.80, 30, '500-1200', 500.00, 1200.00, 'English, Tagalog, Spanish', '+639987654322', 'sofia.martinez@sjdmtours.com', 'Available Tue-Sun 5AM-8PM', 4, '1-12', 1, 38, '/images/guide-sofia.jpg', 'active', '2026-02-07 00:00:00', '2026-02-07 00:00:00'),
(10, 'Marco Alvarez', 'Mountain Trekking', 'mountain', 'Professional mountain guide and outdoor enthusiast', 'Specializes in Mt. Balagbag and other mountain destinations. Expert in trail navigation, weather assessment, and mountain safety protocols.', 'Mountain trekking, Rock climbing, Outdoor survival', 4.60, 18, '600-1000', 600.00, 1000.00, 'English, Tagalog', '+639567890124', 'marco.alvarez@sjdmtours.com', 'Available daily 5AM-5PM', 5, '1-6', 1, 28, '/images/guide-marco.jpg', 'active', '2026-02-07 00:00:00', '2026-02-07 00:00:00'),
(11, 'Elena Rodriguez', 'City Landmarks', 'city', 'Urban tourism specialist and cultural guide', 'Expert in city tours focusing on monuments, landmarks, and urban cultural sites. Provides comprehensive historical and architectural insights.', 'Urban exploration, Cultural tours, Photography guidance', 4.50, 22, '400-800', 400.00, 800.00, 'English, Tagalog', '+6393456789013', 'elena.rodriguez@sjdmtours.com', 'Available Mon-Sat 8AM-6PM', 3, '1-10', 1, 32, '/images/guide-elena.jpg', 'active', '2026-02-07 00:00:00', '2026-02-07 00:00:00');

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

--
-- Dumping data for table `guide_destinations`
--

INSERT INTO `guide_destinations` (`id`, `guide_id`, `destination_id`, `created_at`) VALUES
(1, 1, 13, '2026-02-06 15:00:00'),
(2, 1, 16, '2026-02-06 15:00:00'),
(3, 1, 22, '2026-02-06 15:00:00'),
(4, 2, 18, '2026-02-06 15:00:00'),
(5, 2, 21, '2026-02-06 15:00:00'),
(6, 3, 13, '2026-02-06 15:00:00'),
(7, 3, 15, '2026-02-06 15:00:00'),
(8, 3, 17, '2026-02-06 15:00:00'),
(9, 4, 18, '2026-02-06 15:00:00'),
(10, 4, 21, '2026-02-06 15:00:00'),
(11, 5, 13, '2026-02-06 15:00:00'),
(12, 5, 15, '2026-02-06 15:00:00'),
(13, 5, 16, '2026-02-06 15:00:00'),
(14, 8, 13, '2026-02-07 00:00:00'),
(15, 8, 15, '2026-02-07 00:00:00'),
(16, 8, 17, '2026-02-07 00:00:00'),
(17, 8, 22, '2026-02-07 00:00:00'),
(18, 9, 2, '2026-02-07 00:00:00'),
(19, 9, 18, '2026-02-07 00:00:00'),
(20, 10, 16, '2026-02-07 00:00:00'),
(21, 11, 21, '2026-02-07 00:00:00');

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
(1, 'Admin', 'SJDM', 'adminlgu@gmail.com', '$2y$10$3DQxAUR/H3QdYWRerZKzMuqCvXoRwVpQDFuiE8SU6BvydNnM5CiBS', 'admin', 'active', '2026-01-30 07:02:22', '2026-02-06 08:24:48', '2026-02-06 08:24:48', 0),
(4, 'Ian', 'Jovero', 'christianbacay042504@gmail.com', '$2y$10$pgyID2NX3.S.7QRB1I4GaOWoKrhDwRvN2bwS8xEvNxjlCR8KlM7pO', 'user', 'active', '2026-01-31 08:00:05', '2026-02-06 08:23:56', '2026-02-06 08:23:56', 1),
(5, 'angel', 'hernandez', 'angelhernandez@gmail.com', '$2y$10$3Utff.JPzrx6MhyCiN5GUe305KNvbVmM5119XgUh.goaOVIY6p6JK', 'user', 'active', '2026-02-02 02:00:47', '2026-02-02 02:39:38', '2026-02-02 02:39:38', 0),
(6, 'Admin', 'Dashboard', 'admin_dashboard@sjdm.com', '$2y$10$3DQxAUR/H3QdYWRerZKzMuqCvXoRwVpQDFuiE8SU6BvydNnM5CiBS', 'admin', 'active', '2026-02-05 01:00:00', '2026-02-05 01:00:00', NULL, 0),
(7, 'Admin', 'Users', 'admin_users@sjdm.com', '$2y$10$3DQxAUR/H3QdYWRerZKzMuqCvXoRwVpQDFuiE8SU6BvydNnM5CiBS', 'admin', 'active', '2026-02-05 01:00:00', '2026-02-05 01:00:00', NULL, 0),
(8, 'Admin', 'Content', 'admin_content@sjdm.com', '$2y$10$3DQxAUR/H3QdYWRerZKzMuqCvXoRwVpQDFuiE8SU6BvydNnM5CiBS', 'admin', 'active', '2026-02-05 01:00:00', '2026-02-05 01:00:00', NULL, 0);

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

--
-- Dumping data for table `user_favorites`
--

INSERT INTO `user_favorites` (`id`, `user_id`, `item_id`, `item_type`, `created_at`) VALUES
(1, 4, 1, 'tourist_spot', '2026-02-01 02:30:00'),
(2, 4, 3, 'tourist_spot', '2026-02-01 02:35:00'),
(3, 5, 2, 'tourist_spot', '2026-02-02 06:20:00'),
(4, 5, 1, 'tour_guide', '2026-02-02 06:25:00'),
(5, 1, 2, 'hotel', '2026-02-03 01:15:00'),
(6, 1, 4, 'tourist_spot', '2026-02-03 01:20:00');

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
-- Indexes for table `tourist_spots`
--
ALTER TABLE `tourist_spots`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_category` (`category`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_rating` (`rating`);

--
-- Indexes for table `guide_destinations`
--
ALTER TABLE `guide_destinations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_guide_id` (`guide_id`),
  ADD KEY `idx_destination_id` (`destination_id`),
  ADD UNIQUE KEY `unique_guide_destination` (`guide_id`,`destination_id`);

--
-- Indexes for table `tour_guides`
--
ALTER TABLE `tour_guides`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_category` (`category`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_rating` (`rating`),
  ADD KEY `idx_verified` (`verified`);

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

-- --------------------------------------------------------

--
-- Table structure for tour guide user management
--

CREATE TABLE `tour_guide_profiles` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
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
    `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
    PRIMARY KEY (`id`),
    KEY `idx_tour_guide_profiles_user_id` (`user_id`),
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Table structure for tour guide availability
--

CREATE TABLE `tour_guide_availability` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `tour_guide_id` int(11) NOT NULL,
    `available_date` date NOT NULL,
    `start_time` time NOT NULL,
    `end_time` time NOT NULL,
    `status` enum('available','booked','unavailable') DEFAULT 'available',
    `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
    PRIMARY KEY (`id`),
    KEY `idx_tour_guide_availability_guide_id` (`tour_guide_id`),
    KEY `idx_tour_guide_availability_date` (`available_date`),
    FOREIGN KEY (`tour_guide_id`) REFERENCES `tour_guide_profiles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Table structure for tour guide reviews
--

CREATE TABLE `tour_guide_reviews` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `tour_guide_id` int(11) NOT NULL,
    `user_id` int(11) NOT NULL,
    `booking_id` int(11) DEFAULT NULL,
    `rating` int(11) NOT NULL CHECK (`rating` >= 1 AND `rating` <= 5),
    `review` text DEFAULT NULL,
    `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
    PRIMARY KEY (`id`),
    KEY `idx_tour_guide_reviews_guide_id` (`tour_guide_id`),
    FOREIGN KEY (`tour_guide_id`) REFERENCES `tour_guide_profiles` (`id`) ON DELETE CASCADE,
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Sample tour guide user accounts
--

INSERT INTO `users` (`id`, `first_name`, `last_name`, `email`, `password`, `user_type`, `status`, `created_at`, `updated_at`, `preferences_set`) VALUES
(11, 'Juan', 'Santos', 'juan.santos@tourguide.com', '$2y$10$3DQxAUR/H3QdYWRerZKzMuqCvXoRwVpQDFuiE8SU6BvydNnM5CiBS', 'tour_guide', 'active', '2026-02-09 00:00:00', '2026-02-09 00:00:00', 0),
(12, 'Maria', 'Reyes', 'maria.reyes@tourguide.com', '$2y$10$3DQxAUR/H3QdYWRerZKzMuqCvXoRwVpQDFuiE8SU6BvydNnM5CiBS', 'tour_guide', 'active', '2026-02-09 00:00:00', '2026-02-09 00:00:00', 0);

--
-- Sample tour guide profiles
--

INSERT INTO `tour_guide_profiles` (`user_id`, `license_number`, `specialization`, `experience_years`, `languages`, `hourly_rate`, `contact_number`, `bio`) VALUES
(11, 'TG-001-2026', 'Historical Tours, Nature Walks', 5, 'English, Filipino, Basic Japanese', 1500.00, '09123456789', 'Experienced tour guide specializing in rich history and natural beauty of San Jose del Monte.'),
(12, 'TG-002-2026', 'Adventure Tours, Mountain Hiking', 3, 'English, Filipino', 1200.00, '09987654321', 'Adventure enthusiast with extensive knowledge of mountain trails and outdoor activities.');

-- --------------------------------------------------------

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

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
-- AUTO_INCREMENT for table `tourist_spots`
--
ALTER TABLE `tourist_spots`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `guide_destinations`
--
ALTER TABLE `guide_destinations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `tour_guides`
--
ALTER TABLE `tour_guides`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

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
  ADD CONSTRAINT `fk_guide_destinations_guide` FOREIGN KEY (`guide_id`) REFERENCES `tour_guides` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_guide_destinations_destination` FOREIGN KEY (`destination_id`) REFERENCES `tourist_spots` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `login_activity`
--
ALTER TABLE `login_activity`
  ADD CONSTRAINT `login_activity_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `saved_tours`
--
ALTER TABLE `saved_tours`
  ADD CONSTRAINT `saved_tours_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_favorites`
--
ALTER TABLE `user_favorites`
  ADD CONSTRAINT `fk_user_favorites_item` FOREIGN KEY (`item_id`) REFERENCES `tourist_spots` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_user_favorites_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_settings`
--
ALTER TABLE `user_settings`
  ADD CONSTRAINT `user_settings_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
