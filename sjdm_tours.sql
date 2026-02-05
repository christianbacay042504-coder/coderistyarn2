-- SJDM Tours Database - Complete Structure with Admin Tables
-- Import this file to set up the complete database with all tables and data

-- Create database if it doesn't exist
CREATE DATABASE IF NOT EXISTS `sjdm_tours` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `sjdm_tours`;

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
  `user_type` enum('user','admin') DEFAULT 'user',
  `status` enum('active','inactive','suspended') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `last_login` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
  `status` enum('success','failed') DEFAULT 'success',
  PRIMARY KEY (`id`),
  KEY `idx_user_id` (`user_id`)
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
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_category` (`category`),
  KEY `idx_status` (`status`),
  KEY `idx_rating` (`rating`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tour_guides`
--

CREATE TABLE `tour_guides` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `specialty` varchar(200) DEFAULT NULL,
  `category` enum('mountain','city','farm','waterfall','historical','general') NOT NULL,
  `description` text DEFAULT NULL,
  `bio` text DEFAULT NULL,
  `areas_of_expertise` text DEFAULT NULL,
  `rating` decimal(3,2) DEFAULT 0.00,
  `review_count` int(11) DEFAULT 0,
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
  PRIMARY KEY (`id`),
  KEY `idx_category` (`category`),
  KEY `idx_status` (`status`),
  KEY `idx_rating` (`rating`),
  KEY `idx_verified` (`verified`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `travel_tips`
--

CREATE TABLE `travel_tips` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `icon` varchar(50) DEFAULT NULL,
  `title` varchar(200) NOT NULL,
  `category` varchar(50) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `display_order` int(11) DEFAULT 0,
  `is_active` enum('yes','no') DEFAULT 'yes',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_category` (`category`),
  KEY `idx_display_order` (`display_order`),
  KEY `idx_is_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `local_culture`
--

CREATE TABLE `local_culture` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `icon` varchar(50) DEFAULT NULL,
  `title` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `display_order` int(11) DEFAULT 0,
  `is_active` enum('yes','no') DEFAULT 'yes',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_display_order` (`display_order`),
  KEY `idx_is_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `restaurants`
--

CREATE TABLE `restaurants` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
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
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_category` (`category`),
  KEY `idx_status` (`status`),
  KEY `idx_rating` (`rating`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_category` (`category`),
  KEY `idx_status` (`status`),
  KEY `idx_rating` (`rating`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `guide_id` int(11) NULL,
  `tour_name` varchar(200) NOT NULL,
  `destination` varchar(200) NULL,
  `booking_date` date NOT NULL,
  `check_in_date` date NULL,
  `check_out_date` date NULL,
  `number_of_people` int(11) NOT NULL,
  `contact_number` varchar(50) NULL,
  `email` varchar(255) NULL,
  `special_requests` text NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `payment_method` enum('pay_later','gcash','bank_transfer') DEFAULT 'pay_later',
  `status` enum('pending','confirmed','cancelled','completed') DEFAULT 'pending',
  `booking_reference` varchar(50) NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_guide_id` (`guide_id`),
  KEY `idx_booking_date` (`booking_date`),
  KEY `idx_status` (`status`),
  KEY `idx_booking_reference` (`booking_reference`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `saved_tours`
--

CREATE TABLE `saved_tours` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `tour_name` varchar(200) NOT NULL,
  `tour_description` text DEFAULT NULL,
  `saved_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- ADMIN TABLES WITH "admin_" PREFIX
-- --------------------------------------------------------

--
-- Table structure for table `admin_users`
--

CREATE TABLE `admin_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `admin_mark` varchar(10) DEFAULT 'A',
  `role_title` varchar(100) DEFAULT 'Administrator',
  `permissions` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`user_id`),
  FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `admin_dashboard_settings`
--

CREATE TABLE `admin_dashboard_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text DEFAULT NULL,
  `setting_type` enum('text','number','boolean') DEFAULT 'text',
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `setting_key` (`setting_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `admin_menu_items`
--

CREATE TABLE `admin_menu_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `menu_name` varchar(100) NOT NULL,
  `menu_icon` varchar(50) DEFAULT NULL,
  `menu_url` varchar(200) NOT NULL,
  `display_order` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `parent_id` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_display_order` (`display_order`),
  KEY `idx_is_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `admin_dashboards`
--

CREATE TABLE `admin_dashboards` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `admin_id` int(11) NOT NULL,
  `dashboard_name` varchar(100) NOT NULL,
  `dashboard_layout` text DEFAULT NULL,
  `widgets_config` text DEFAULT NULL,
  `theme_settings` text DEFAULT NULL,
  `is_default` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_admin_id` (`admin_id`),
  FOREIGN KEY (`admin_id`) REFERENCES `admin_users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `admin_permissions_table`
--

CREATE TABLE `admin_permissions_table` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `admin_id` int(11) NOT NULL,
  `module` varchar(50) NOT NULL,
  `permission_type` enum('read','write','delete','admin') NOT NULL,
  `granted_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `granted_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_admin_id` (`admin_id`),
  KEY `idx_module` (`module`),
  FOREIGN KEY (`admin_id`) REFERENCES `admin_users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `admin_activity`
--

CREATE TABLE `admin_activity` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `admin_id` int(11) NOT NULL,
  `action` varchar(100) NOT NULL,
  `module` varchar(50) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_admin_id` (`admin_id`),
  KEY `idx_action` (`action`),
  KEY `idx_created_at` (`created_at`),
  FOREIGN KEY (`admin_id`) REFERENCES `admin_users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_settings`
--

CREATE TABLE `user_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `notifications` tinyint(1) DEFAULT 1,
  `email_updates` tinyint(1) DEFAULT 1,
  `share_history` tinyint(1) DEFAULT 0,
  `public_profile` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_user_settings` (`user_id`),
  FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `booking_settings`
--

CREATE TABLE `booking_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `setting_key` varchar(50) NOT NULL,
  `setting_value` text NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_key` (`setting_key`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

--
-- Dumping data for table `booking_settings`
--

INSERT INTO
    `booking_settings` (
        `setting_key`,
        `setting_value`
    )
VALUES (
        'module_title',
        'Bookings Management'
    ),
    (
        'module_subtitle',
        'Manage tour bookings and reservations'
    ),
    ('default_booking_limit', '15'),
    ('admin_mark_label', 'BK');

-- --------------------------------------------------------

--
-- Table structure for table `hotel_settings`
--

CREATE TABLE `hotel_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `setting_key` varchar(50) NOT NULL,
  `setting_value` text NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_key` (`setting_key`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

--
-- Dumping data for table `hotel_settings`
--

INSERT INTO
    `hotel_settings` (
        `setting_key`,
        `setting_value`
    )
VALUES (
        'module_title',
        'Hotels Management'
    ),
    (
        'module_subtitle',
        'Manage hotel accommodations'
    ),
    ('default_hotel_limit', '15'),
    ('admin_mark_label', 'HT');

-- --------------------------------------------------------

--
-- Table structure for table `destination_settings`
--

CREATE TABLE `destination_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `setting_key` varchar(50) NOT NULL,
  `setting_value` text NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_key` (`setting_key`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

--
-- Dumping data for table `destination_settings`
--

INSERT INTO
    `destination_settings` (
        `setting_key`,
        `setting_value`
    )
VALUES (
        'module_title',
        'Destinations Management'
    ),
    (
        'module_subtitle',
        'Manage tourist destinations'
    ),
    ('default_destination_limit', '15'),
    ('admin_mark_label', 'DS');

-- --------------------------------------------------------

--
-- Table structure for table `analytics_settings`
--

CREATE TABLE `analytics_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `setting_key` varchar(50) NOT NULL,
  `setting_value` text NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_key` (`setting_key`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

--
-- Dumping data for table `analytics_settings`
--

INSERT INTO
    `analytics_settings` (
        `setting_key`,
        `setting_value`
    )
VALUES (
        'module_title',
        'Analytics Dashboard'
    ),
    (
        'module_subtitle',
        'System analytics and insights'
    ),
    ('default_analytics_limit', '15'),
    ('admin_mark_label', 'AN');

-- --------------------------------------------------------

--
-- Table structure for table `report_settings`
--

CREATE TABLE `report_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `setting_key` varchar(50) NOT NULL,
  `setting_value` text NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_key` (`setting_key`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

--
-- Dumping data for table `report_settings`
--

INSERT INTO
    `report_settings` (
        `setting_key`,
        `setting_value`
    )
VALUES (
        'module_title',
        'Reports Management'
    ),
    (
        'module_subtitle',
        'Generate and manage reports'
    ),
    ('default_report_limit', '15'),
    ('admin_mark_label', 'RP');

-- --------------------------------------------------------

--
-- Table structure for table `tour_guide_settings`
--

CREATE TABLE `tour_guide_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `setting_key` varchar(50) NOT NULL,
  `setting_value` text NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_key` (`setting_key`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

--
-- Dumping data for table `tour_guide_settings`
--

INSERT INTO
    `tour_guide_settings` (
        `setting_key`,
        `setting_value`
    )
VALUES (
        'module_title',
        'Tour Guides Management'
    ),
    (
        'module_subtitle',
        'Manage and monitor tour guides'
    ),
    ('default_guide_limit', '15'),
    ('admin_mark_label', 'TG');

-- --------------------------------------------------------

--
-- Table structure for table `user_management_settings`
--

CREATE TABLE `user_management_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `setting_key` varchar(50) NOT NULL,
  `setting_value` text NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_key` (`setting_key`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

--
-- Dumping data for table `user_management_settings`
--

INSERT INTO
    `user_management_settings` (
        `setting_key`,
        `setting_value`
    )
VALUES (
        'module_title',
        'User Management'
    ),
    (
        'module_subtitle',
        'Manage, monitor and authorize system users'
    ),
    ('default_user_limit', '15'),
    ('admin_mark_label', 'A');

-- --------------------------------------------------------

--
-- Table structure for table `admin_preferences`
--

CREATE TABLE `admin_preferences` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `admin_id` int(11) NOT NULL,
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text DEFAULT NULL,
  `setting_type` enum('text','number','boolean','json') DEFAULT 'text',
  `category` varchar(50) DEFAULT 'general',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_admin_setting` (`admin_id`, `setting_key`),
  KEY `idx_admin_id` (`admin_id`),
  KEY `idx_category` (`category`),
  FOREIGN KEY (`admin_id`) REFERENCES `admin_users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Data for table `users`
--

INSERT INTO `users` (`id`, `first_name`, `last_name`, `email`, `password`, `user_type`, `status`, `created_at`, `updated_at`, `last_login`) VALUES
(1, 'Admin', 'SJDM', 'adminlgu@gmail.com', '$2y$10$3DQxAUR/H3QdYWRerZKzMuqCvXoRwVpQDFuiE8SU6BvydNnM5CiBS', 'admin', 'active', '2026-01-30 15:02:22', '2026-02-02 14:56:28', '2026-02-02 14:56:28'),
(4, 'Ian', 'Jovero', 'christianbacay042504@gmail.com', '$2y$10$pgyID2NX3.S.7QRB1I4GaOWoKrhDwRvN2bwS8xEvNxjlCR8KlM7pO', 'user', 'active', '2026-01-31 16:00:05', '2026-02-02 14:55:23', '2026-02-02 14:55:23'),
(5, 'angel', 'hernandez', 'angelhernandez@gmail.com', '$2y$10$3Utff.JPzrx6MhyCiN5GUe305KNvbVmM5119XgUh.goaOVIY6p6JK', 'user', 'active', '2026-02-02 10:00:47', '2026-02-02 10:39:38', '2026-02-02 10:39:38');

-- --------------------------------------------------------

--
-- Data for table `users`
--

INSERT INTO `users` (`id`, `first_name`, `last_name`, `email`, `password`, `user_type`, `status`, `created_at`, `updated_at`, `last_login`) VALUES
(6, 'Admin', 'Dashboard', 'admin_dashboard@sjdm.com', '$2y$10$3DQxAUR/H3QdYWRerZKzMuqCvXoRwVpQDFuiE8SU6BvydNnM5CiBS', 'admin', 'active', '2026-02-05 09:00:00', '2026-02-05 09:00:00', NULL),
(7, 'Admin', 'Users', 'admin_users@sjdm.com', '$2y$10$3DQxAUR/H3QdYWRerZKzMuqCvXoRwVpQDFuiE8SU6BvydNnM5CiBS', 'admin', 'active', '2026-02-05 09:00:00', '2026-02-05 09:00:00', NULL),
(8, 'Admin', 'Content', 'admin_content@sjdm.com', '$2y$10$3DQxAUR/H3QdYWRerZKzMuqCvXoRwVpQDFuiE8SU6BvydNnM5CiBS', 'admin', 'active', '2026-02-05 09:00:00', '2026-02-05 09:00:00', NULL);

-- --------------------------------------------------------

--
-- Data for table `admin_users`
--

INSERT INTO `admin_users` (`id`, `user_id`, `admin_mark`, `role_title`, `permissions`) VALUES
(1, 1, 'ADMIN', 'Super Administrator', 'all'),
(6, 6, 'ADMIN_DASHBOARD', 'Dashboard Administrator', 'dashboard,analytics,reports'),
(7, 7, 'ADMIN_USERS', 'User Administrator', 'users,bookings,guides'),
(8, 8, 'ADMIN_CONTENT', 'Content Administrator', 'destinations,hotels,content');

-- --------------------------------------------------------

--
-- Data for table `admin_dashboard_settings`
--

INSERT INTO `admin_dashboard_settings` (`setting_key`, `setting_value`, `setting_type`, `description`) VALUES
('page_title', 'Dashboard Overview', 'text', 'Main dashboard page title'),
('page_subtitle', 'System statistics and analytics', 'text', 'Dashboard page subtitle'),
('admin_logo_text', 'SJDM ADMIN', 'text', 'Admin panel logo text'),
('show_user_badges', '1', 'boolean', 'Show user count badges in sidebar'),
('refresh_interval', '30', 'number', 'Dashboard auto-refresh interval in seconds');

-- --------------------------------------------------------

--
-- Data for table `admin_menu_items`
--

INSERT INTO `admin_menu_items` (`menu_name`, `menu_icon`, `menu_url`, `display_order`, `is_active`) VALUES
('Dashboard', 'dashboard', 'dashboard.php', 1, 1),
('User Management', 'people', 'user-management.php', 2, 1),
('Tour Guides', 'tour', 'tour-guides.php', 3, 1),
('Destinations', 'place', 'destinations.php', 4, 1),
('Hotels', 'hotel', 'hotels.php', 5, 1),
('Bookings', 'event', 'bookings.php', 6, 1),
('Analytics', 'analytics', 'analytics.php', 7, 1),
('Reports', 'description', 'reports.php', 8, 1),
('Settings', 'settings', 'settings.php', 9, 1);

-- --------------------------------------------------------

--
-- Data for table `admin_dashboards`
--

INSERT INTO `admin_dashboards` (`admin_id`, `dashboard_name`, `dashboard_layout`, `widgets_config`, `is_default`) VALUES
(1, 'Main Dashboard', '{"layout": "grid", "columns": 3}', '{"widgets": ["users", "bookings", "revenue", "guides", "destinations", "hotels"]}', 1),
(1, 'Analytics Dashboard', '{"layout": "charts", "columns": 2}', '{"widgets": ["user_growth", "booking_trends", "revenue_chart", "popular_destinations"]}', 0),
(1, 'Quick Overview', '{"layout": "compact", "columns": 4}', '{"widgets": ["total_users", "today_bookings", "pending_tasks", "system_status"]}', 0);

-- --------------------------------------------------------

--
-- Data for table `admin_permissions_table`
--

INSERT INTO `admin_permissions_table` (`admin_id`, `module`, `permission_type`) VALUES
(1, 'dashboard', 'admin'),
(1, 'users', 'admin'),
(1, 'bookings', 'admin'),
(1, 'guides', 'admin'),
(1, 'destinations', 'admin'),
(1, 'hotels', 'admin'),
(1, 'analytics', 'admin'),
(1, 'reports', 'admin'),
(1, 'settings', 'admin');

-- --------------------------------------------------------

--
-- Data for table `admin_preferences`
--

INSERT INTO `admin_preferences` (`admin_id`, `setting_key`, `setting_value`, `setting_type`, `category`) VALUES
(1, 'theme', 'light', 'text', 'appearance'),
(1, 'language', 'en', 'text', 'appearance'),
(1, 'notifications_enabled', '1', 'boolean', 'notifications'),
(1, 'auto_refresh_interval', '30', 'number', 'performance'),
(1, 'items_per_page', '15', 'number', 'performance'),
(1, 'default_dashboard', 'Main Dashboard', 'text', 'general'),
(1, 'show_tooltips', '1', 'boolean', 'ui'),
(1, 'compact_mode', '0', 'boolean', 'ui'),
(1, 'admin_mark_display', 'badge', 'text', 'appearance'),
(1, 'last_login_notification', '1', 'boolean', 'security');

-- --------------------------------------------------------

--
-- Add foreign key constraints for existing tables
--

ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_bookings_guide_id` FOREIGN KEY (`guide_id`) REFERENCES `tour_guides` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

ALTER TABLE `login_activity`
  ADD CONSTRAINT `login_activity_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

ALTER TABLE `saved_tours`
  ADD CONSTRAINT `saved_tours_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

-- --------------------------------------------------------
-- Complete! The SJDM Tours database is now ready with admin tables.
-- Features:
-- - All admin tables have "admin_" prefix for clear separation
-- - Admin users table with admin marks and roles
-- - Admin dashboards table for multiple dashboard configurations
-- - Admin permissions table for granular access control
-- - Admin activity table for security logging
-- - Admin preferences table for personal settings
-- - Admin dashboard settings for global configuration
-- - Admin menu items table for dynamic navigation
-- - All tables properly linked with foreign keys
-- - Admin mark system with multiple admin levels

-- --------------------------------------------------------

--
-- Table structure for table `homepage_content`
--

CREATE TABLE `homepage_content` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `content_type` varchar(50) NOT NULL COMMENT 'hero, features, about, etc.',
  `content_key` varchar(100) NOT NULL COMMENT 'title, subtitle, description, image, etc.',
  `content_value` text NOT NULL,
  `display_order` int(11) NOT NULL DEFAULT 0,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_content_type` (`content_type`),
  KEY `idx_status` (`status`),
  KEY `idx_display_order` (`display_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `homepage_content`
--

INSERT INTO `homepage_content` (`content_type`, `content_key`, `content_value`, `display_order`, `status`) VALUES
('hero', 'title', 'Welcome to SJDM Tours', 1, 'active'),
('hero', 'subtitle', 'Discover the Beauty of San Jose del Monte', 2, 'active'),
('hero', 'description', 'Experience the best tourist spots and activities in San Jose del Monte with our expert tour guides.', 3, 'active'),
('hero', 'background_image', '/images/hero-bg.jpg', 4, 'active'),
('features', 'title1', 'Expert Tour Guides', 1, 'active'),
('features', 'description1', 'Professional and knowledgeable local guides to make your tour memorable.', 2, 'active'),
('features', 'icon1', 'people', 3, 'active'),
('features', 'title2', 'Best Destinations', 4, 'active'),
('features', 'description2', 'Carefully selected tourist spots showcasing the best of SJDM.', 5, 'active'),
('features', 'icon2', 'place', 6, 'active'),
('features', 'title3', 'Easy Booking', 7, 'active'),
('features', 'description3', 'Simple and secure online booking system for your convenience.', 8, 'active'),
('features', 'icon3', 'event', 9, 'active'),
('about', 'title', 'About SJDM Tours', 1, 'active'),
('about', 'description', 'We are your premier tour service provider in San Jose del Monte, offering unforgettable experiences and expert guidance to the most beautiful destinations in the city.', 2, 'active'),
('about', 'mission', 'To provide exceptional tour experiences that showcase the natural beauty and cultural heritage of San Jose del Monte.', 3, 'active'),
('about', 'vision', 'To become the leading tour service provider in the region, known for quality, reliability, and customer satisfaction.', 4, 'active');

-- --------------------------------------------------------

--
-- Dumping data for table `tourist_spots`
--

INSERT INTO `tourist_spots` (`id`, `name`, `description`, `location`, `category`, `entrance_fee`, `rating`, `image_url`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Mount Balagbag', 'A popular hiking destination offering breathtaking views of the city and surrounding provinces. Perfect for adventure seekers and nature lovers.', 'San Jose del Monte, Bulacan', 'nature', '500-1000', 4.5, '/images/mount-balagbag.jpg', 'active', '2026-01-15 10:00:00', '2026-01-15 10:00:00'),
(2, 'Grotto of San Jose del Monte', 'A peaceful religious site perfect for prayer and reflection. Features beautiful architecture and serene surroundings.', 'San Jose del Monte, Bulacan', 'religious', 'Free', 4.8, '/images/grotto.jpg', 'active', '2026-01-16 11:00:00', '2026-01-16 11:00:00'),
(3, 'Tungkong Mangga', 'Historical landmark and local market area showcasing the rich culture and traditions of San Jose del Monte.', 'San Jose del Monte, Bulacan', 'historical', 'Free', 4.2, '/images/tungkong-mangga.jpg', 'active', '2026-01-17 12:00:00', '2026-01-17 12:00:00'),
(4, 'Lambingan Bridge', 'Scenic bridge perfect for photography and romantic walks. Offers beautiful sunset views.', 'San Jose del Monte, Bulacan', 'urban', 'Free', 4.6, '/images/lambingan-bridge.jpg', 'active', '2026-01-18 13:00:00', '2026-01-18 13:00:00'),
(5, 'San Jose del Monte City Hall', 'Modern government building showcasing impressive architecture and local governance. Open for public tours.', 'San Jose del Monte, Bulacan', 'historical', 'Free', 4.3, '/images/city-hall.jpg', 'active', '2026-01-19 14:00:00', '2026-01-19 14:00:00'),
(6, 'Kaypian Park', 'Family-friendly park with playgrounds, picnic areas, and recreational facilities. Perfect for weekend outings.', 'San Jose del Monte, Bulacan', 'park', '50-200', 4.4, '/images/kaypian-park.jpg', 'active', '2026-01-20 15:00:00', '2026-01-20 15:00:00'),
(7, 'Minuyan Reservoir', 'Beautiful reservoir offering fishing, boating, and nature activities. Great for outdoor enthusiasts.', 'San Jose del Monte, Bulacan', 'nature', '100-300', 4.7, '/images/minuyan-reservoir.jpg', 'active', '2026-01-21 16:00:00', '2026-01-21 16:00:00'),
(8, 'San Lorenzo Ruiz Parish', 'Beautiful Catholic church with rich history and stunning architecture. Regular masses and religious services.', 'San Jose del Monte, Bulacan', 'religious', 'Free', 4.9, '/images/san-lorenzo-ruiz.jpg', 'active', '2026-01-22 17:00:00', '2026-01-22 17:00:00');

-- --------------------------------------------------------

--
-- Dumping data for table `tour_guides`
--

INSERT INTO `tour_guides` (`id`, `name`, `email`, `contact_number`, `specialty`, `experience_years`, `rating`, `bio`, `photo_url`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Carlos Mendoza', 'carlos.mendoza@sjdmtours.com', '+639123456789', 'Adventure Tours', 5, 4.8, 'Experienced adventure guide with expertise in mountain trekking and outdoor activities. Certified first aid provider and wilderness survival trained.', '/images/guide-carlos.jpg', 'active', '2026-01-10 09:00:00', '2026-01-10 09:00:00'),
(2, 'Maria Santos', 'maria.santos@sjdmtours.com', '+639987654321', 'Cultural Tours', 3, 4.9, 'Passionate about sharing the rich cultural heritage of San Jose del Monte. Fluent in English, Tagalog, and basic Japanese.', '/images/guide-maria.jpg', 'active', '2026-01-11 10:00:00', '2026-01-11 10:00:00'),
(3, 'Roberto Reyes', 'roberto.reyes@sjdmtours.com', '+639567890123', 'Nature & Photography', 7, 4.7, 'Professional photographer and nature guide. Specializes in wildlife photography and bird watching tours.', '/images/guide-roberto.jpg', 'active', '2026-01-12 11:00:00', '2026-01-12 11:00:00'),
(4, 'Ana Cruz', 'ana.cruz@sjdmtours.com', '+6393456789012', 'Historical Tours', 4, 4.6, 'History enthusiast with deep knowledge of San Jose del Monte''s colonial past and historical landmarks.', '/images/guide-ana.jpg', 'active', '2026-01-13 12:00:00', '2026-01-13 12:00:00'),
(5, 'David Lee', 'david.lee@sjdmtours.com', '+639234567890', 'Food & Culinary Tours', 2, 4.5, 'Food expert offering culinary tours showcasing local delicacies and traditional Bulacan cuisine.', '/images/guide-david.jpg', 'active', '2026-01-14 13:00:00', '2026-01-14 13:00:00');

-- --------------------------------------------------------

--
-- Dumping data for table `travel_tips`
--

INSERT INTO `travel_tips` (`id`, `icon`, `title`, `category`, `description`, `display_order`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'directions_bus', 'Transportation Tips', 'transport', 'Book transportation in advance during peak seasons\nUse reputable transport services for safety\nKeep emergency contact numbers handy\nDownload offline maps for navigation', 1, 'yes', '2026-01-01 08:00:00', '2026-01-01 08:00:00'),
(2, 'hotel', 'Accommodation Advice', 'accommodation', 'Read recent reviews before booking\nCheck location proximity to attractions\nConfirm cancellation policies\nBring personal toiletries for comfort', 2, 'yes', '2026-01-01 08:00:00', '2026-01-01 08:00:00'),
(3, 'restaurant', 'Food & Dining', 'food', 'Try local delicacies for authentic experience\nDrink bottled water for safety\nCheck food hygiene standards\nBring snacks for long tours', 3, 'yes', '2026-01-01 08:00:00', '2026-01-01 08:00:00'),
(4, 'health_and_safety', 'Health & Safety', 'safety', 'Pack basic medications and first aid\nGet travel insurance for emergencies\nKeep copies of important documents\nKnow local emergency numbers', 4, 'yes', '2026-01-01 08:00:00', '2026-01-01 08:00:00'),
(5, 'payments', 'Money & Payments', 'finance', 'Bring enough cash for small purchases\nInform bank about travel plans\nUse secure ATMs for withdrawals\nKeep money in different locations', 5, 'yes', '2026-01-01 08:00:00', '2026-01-01 08:00:00'),
(6, 'camera_alt', 'Photography Tips', 'photography', 'Respect photography restrictions\nBring extra batteries and memory cards\nAsk permission before photographing people\nCapture sunrise/sunset for best shots', 6, 'yes', '2026-01-01 08:00:00', '2026-01-01 08:00:00'),
(7, 'phone_iphone', 'Communication', 'communication', 'Buy local SIM cards for data\nDownload useful travel apps\nLearn basic local phrases\nKeep phone charged with power bank', 7, 'yes', '2026-01-01 08:00:00', '2026-01-01 08:00:00'),
(8, 'shopping_bag', 'Shopping Guide', 'shopping', 'Bargain respectfully at local markets\nBuy authentic local products\nCheck customs regulations for souvenirs\nSupport local artisans and businesses', 8, 'yes', '2026-01-01 08:00:00', '2026-01-01 08:00:00');

-- --------------------------------------------------------

--
-- Dumping data for table `local_culture`
--

INSERT INTO `local_culture` (`id`, `icon`, `title`, `description`, `display_order`, `is_active`, `created_at`, `updated_at`) VALUES
(1, '🎭', 'Traditional Festivals', 'Kakaning Bulacan Festival - Annual celebration of local delicacies\nPanagbenga-inspired floral parades\nReligious processions during Holy Week\nChristmas traditions and Simbang Gabi', 1, 'yes', '2026-01-01 08:00:00', '2026-01-01 08:00:00'),
(2, '🍽️', 'Local Cuisine', 'Bulacan specialties like Lechon Kawali and Chicharon\nNative rice cakes (kakanin) such as Puto and Kutsinta\nFresh water fish dishes from local rivers\nTraditional Filipino breakfast combinations', 2, 'yes', '2026-01-01 08:00:00', '2026-01-01 08:00:00'),
(3, '🎨', 'Arts and Crafts', 'Traditional weaving and basket making\nLocal pottery and ceramic arts\nFolk painting and sculpture\nIndigenous musical instruments', 3, 'yes', '2026-01-01 08:00:00', '2026-01-01 08:00:00'),
(4, '🏛️', 'Historical Heritage', 'Spanish colonial architecture\nHistorical churches and ancestral houses\nLocal museums and cultural centers\nRevolutionary war memorials', 4, 'yes', '2026-01-01 08:00:00', '2026-01-01 08:00:00'),
(5, '🎵', 'Music and Dance', 'Traditional folk dances\nLocal musical performances\nCommunity choirs and bands\nModern contemporary arts scene', 5, 'yes', '2026-01-01 08:00:00', '2026-01-01 08:00:00'),
(6, '👥', 'Community Life', 'Bayanihan spirit and neighborly cooperation\nLocal market traditions and trade\nFamily gatherings and celebrations\nCommunity sports and recreation', 6, 'yes', '2026-01-01 08:00:00', '2026-01-01 08:00:00');

-- --------------------------------------------------------

--
-- Dumping data for table `restaurants`
--

INSERT INTO `restaurants` (`id`, `name`, `description`, `location`, `category`, `price_range`, `rating`, `image_url`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Escobar''s', 'Popular local restaurant serving authentic Filipino cuisine and comfort food in a cozy atmosphere.', 'San Jose del Monte, Bulacan', 'casual_dining', '200-500', 4.3, '/images/restaurant-escobars.jpg', 'active', '2026-01-01 08:00:00', '2026-01-01 08:00:00'),
(2, 'Roadside Dampa', 'Casual roadside eatery offering fresh seafood and grilled specialties at affordable prices.', 'San Jose del Monte, Bulacan', 'local_eatery', '150-400', 4.1, '/images/restaurant-dampa.jpg', 'active', '2026-01-02 09:00:00', '2026-01-02 09:00:00'),
(3, 'Max''s SM SJDM', 'Well-known restaurant chain serving classic Filipino dishes and fried chicken in SM City San Jose del Monte.', 'San Jose del Monte, Bulacan', 'fast_food', '300-600', 4.5, '/images/restaurant-maxs.jpg', 'active', '2026-01-03 10:00:00', '2026-01-03 10:00:00'),
(4, 'Local Carinderia', 'Traditional Filipino eatery offering home-cooked meals at budget-friendly prices.', 'San Jose del Monte, Bulacan', 'local_eatery', '100-250', 4.0, '/images/restaurant-carinderia.jpg', 'active', '2026-01-04 11:00:00', '2026-01-04 11:00:00'),
(5, 'Cafe de Monte', 'Cozy coffee shop serving freshly brewed coffee, pastries, and light meals.', 'San Jose del Monte, Bulacan', 'cafe', '150-300', 4.2, '/images/restaurant-cafe.jpg', 'active', '2026-01-05 12:00:00', '2026-01-05 12:00:00');

-- --------------------------------------------------------

--
-- Dumping data for table `hotels`
--

INSERT INTO `hotels` (`id`, `name`, `description`, `location`, `category`, `price_range`, `rating`, `image_url`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Mountain View Hotel', 'Modern hotel with stunning mountain views and excellent amenities. Perfect for both business and leisure travelers.', 'San Jose del Monte, Bulacan', 'Business', '2000-3500', 4.2, '/images/hotel-mountain-view.jpg', 'active', '2026-01-05 08:00:00', '2026-01-05 08:00:00'),
(2, 'SJDM Grand Hotel', 'Luxurious hotel in the heart of the city. Offers world-class service and facilities.', 'San Jose del Monte, Bulacan', 'Luxury', '4000-6000', 4.7, '/images/hotel-grand.jpg', 'active', '2026-01-06 09:00:00', '2026-01-06 09:00:00'),
(3, 'Budget Stay Inn', 'Affordable accommodation with clean rooms and friendly service. Great value for money.', 'San Jose del Monte, Bulacan', 'Budget', '800-1500', 3.8, '/images/hotel-budget.jpg', 'active', '2026-01-07 10:00:00', '2026-01-07 10:00:00'),
(4, 'Nature Resort Hotel', 'Eco-friendly resort surrounded by lush greenery. Offers spa services and nature activities.', 'San Jose del Monte, Bulacan', 'Resort', '2500-4000', 4.5, '/images/hotel-nature.jpg', 'active', '2026-01-08 11:00:00', '2026-01-08 11:00:00'),
(5, 'City Center Hotel', 'Conveniently located hotel near major attractions and business district.', 'San Jose del Monte, Bulacan', 'Business', '1800-3000', 4.1, '/images/hotel-city-center.jpg', 'active', '2026-01-09 12:00:00', '2026-01-09 12:00:00');

-- --------------------------------------------------------

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`id`, `user_id`, `guide_id`, `tour_name`, `booking_date`, `total_amount`, `status`, `special_requests`, `created_at`, `updated_at`) VALUES
(1, 4, 1, 'Mount Balagbag Adventure Trek', '2026-02-15', 1200.00, 'confirmed', 'Please provide vegetarian lunch options', '2026-02-01 10:30:00', '2026-02-01 10:30:00'),
(2, 5, 2, 'Cultural Heritage Tour', '2026-02-20', 800.00, 'confirmed', 'Need wheelchair accessible transportation', '2026-02-02 14:20:00', '2026-02-02 14:20:00'),
(3, 4, 3, 'Nature Photography Workshop', '2026-02-18', 1500.00, 'pending', 'Bring extra camera batteries', '2026-02-03 09:15:00', '2026-02-03 09:15:00'),
(4, 5, 4, 'Historical Walking Tour', '2026-02-25', 600.00, 'confirmed', 'Include visit to local museums', '2026-02-04 16:45:00', '2026-02-04 16:45:00'),
(5, 4, 5, 'Culinary Food Tour', '2026-02-22', 1000.00, 'confirmed', 'Allergic to seafood', '2026-02-05 13:30:00', '2026-02-05 13:30:00'),
(6, 1, 1, 'Sunset Mountain Hike', '2026-03-01', 900.00, 'pending', 'Group of 4 people', '2026-02-05 15:00:00', '2026-02-05 15:00:00'),
(7, 5, 2, 'Religious Sites Tour', '2026-02-28', 500.00, 'confirmed', 'Include transportation from hotel', '2026-02-05 16:20:00', '2026-02-05 16:20:00'),
(8, 4, 3, 'Bird Watching Expedition', '2026-03-05', 1800.00, 'pending', 'Need binoculars rental', '2026-02-05 17:10:00', '2026-02-05 17:10:00');

-- --------------------------------------------------------

--
-- Dumping data for table `login_activity`
--

INSERT INTO `login_activity` (`id`, `user_id`, `login_time`, `ip_address`, `user_agent`, `status`) VALUES
(1, 4, '2026-02-05 09:00:00', '192.168.1.100', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36', 'success'),
(2, 5, '2026-02-05 10:30:00', '192.168.1.101', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36', 'success'),
(3, 1, '2026-02-05 11:15:00', '192.168.1.102', 'Mozilla/5.0 (iPhone; CPU iPhone OS 14_7_1 like Mac OS X) AppleWebKit/605.1.15', 'success'),
(4, 4, '2026-02-05 14:20:00', '192.168.1.100', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36', 'success'),
(5, 5, '2026-02-05 16:45:00', '192.168.1.101', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36', 'success'),
(6, 1, '2026-02-04 08:00:00', '192.168.1.102', 'Mozilla/5.0 (iPhone; CPU iPhone OS 14_7_1 like Mac OS X) AppleWebKit/605.1.15', 'success'),
(7, 4, '2026-02-04 15:30:00', '192.168.1.100', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36', 'failed'),
(8, 5, '2026-02-03 12:00:00', '192.168.1.101', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36', 'success');

-- --------------------------------------------------------

--
-- Dumping data for table `user_settings`
--

INSERT INTO `user_settings` (`user_id`, `notifications`, `email_updates`, `share_history`, `public_profile`, `created_at`, `updated_at`) VALUES
(4, 1, 1, 0, 0, '2026-02-01 10:00:00', '2026-02-01 10:00:00'),
(5, 1, 0, 1, 0, '2026-02-02 11:00:00', '2026-02-02 11:00:00'),
(1, 0, 1, 0, 1, '2026-02-03 12:00:00', '2026-02-03 12:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `user_favorites`
--

CREATE TABLE `user_favorites` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `item_type` enum('tourist_spot','tour_guide','hotel') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_item` (`item_id`, `item_type`),
  CONSTRAINT `fk_user_favorites_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_user_favorites_item` FOREIGN KEY (`item_id`) REFERENCES `tourist_spots` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Dumping data for table `user_favorites`
--

INSERT INTO `user_favorites` (`user_id`, `item_id`, `item_type`, `created_at`) VALUES
(4, 1, 'tourist_spot', '2026-02-01 10:30:00'),
(4, 3, 'tourist_spot', '2026-02-01 10:35:00'),
(5, 2, 'tourist_spot', '2026-02-02 14:20:00'),
(5, 1, 'tour_guide', '2026-02-02 14:25:00'),
(1, 2, 'hotel', '2026-02-03 09:15:00'),
(1, 4, 'tourist_spot', '2026-02-03 09:20:00');

-- --------------------------------------------------------

--
-- Dumping data for table `admin_activity`
--

INSERT INTO `admin_activity` (`id`, `admin_id`, `action`, `module`, `description`, `ip_address`, `created_at`) VALUES
(1, 1, 'LOGIN', 'auth', 'Super admin logged in', '192.168.1.1', '2026-02-05 08:00:00'),
(2, 1, 'ACCESS', 'dashboard', 'Admin accessed dashboard', '192.168.1.1', '2026-02-05 08:05:00'),
(3, 6, 'LOGIN', 'auth', 'Dashboard admin logged in', '192.168.1.2', '2026-02-05 09:00:00'),
(4, 6, 'CREATE', 'users', 'Created new user account', '192.168.1.2', '2026-02-05 09:30:00'),
(5, 7, 'LOGIN', 'auth', 'User admin logged in', '192.168.1.3', '2026-02-05 10:00:00'),
(6, 7, 'UPDATE', 'bookings', 'Updated booking status', '192.168.1.3', '2026-02-05 10:15:00'),
(7, 1, 'LOGOUT', 'auth', 'Super admin logged out', '192.168.1.1', '2026-02-05 17:00:00'),
(8, 6, 'LOGOUT', 'auth', 'Dashboard admin logged out', '192.168.1.2', '2026-02-05 17:30:00'),
(9, 8, 'LOGIN', 'auth', 'Content admin logged in', '192.168.1.4', '2026-02-05 11:00:00'),
(10, 8, 'ACCESS', 'content', 'Content admin accessed dashboard', '192.168.1.4', '2026-02-05 11:05:00'),
(11, 8, 'UPDATE', 'destinations', 'Updated destination content', '192.168.1.4', '2026-02-05 14:00:00'),
(12, 8, 'UPDATE', 'hotels', 'Updated hotel information', '192.168.1.4', '2026-02-05 15:30:00'),
(13, 1, 'LOGOUT', 'auth', 'Super admin logged out', '192.168.1.1', '2026-02-05 18:00:00'),
(14, 8, 'LOGOUT', 'auth', 'Content admin logged out', '192.168.1.4', '2026-02-05 18:30:00');
-- - Complete audit trail and security features
--
