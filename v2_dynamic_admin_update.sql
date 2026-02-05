-- SJDM Tours V2 Dynamic Admin Migration Script
-- Run this script to add missing tables for the dynamic admin dashboard

-- 1. Admins Table
CREATE TABLE IF NOT EXISTS `admins` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `user_id` int(11) NOT NULL,
    `admin_mark` char(1) NOT NULL DEFAULT 'A',
    `role_title` varchar(100) DEFAULT 'Administrator',
    `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
    PRIMARY KEY (`id`),
    KEY `idx_user_id` (`user_id`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

-- Link default admin (id 1) to admins table
INSERT IGNORE INTO
    `admins` (
        `id`,
        `user_id`,
        `admin_mark`,
        `role_title`
    )
VALUES (
        1,
        1,
        'A',
        'System Administrator'
    );

-- 2. Dashboard Settings
CREATE TABLE IF NOT EXISTS `dashboard_settings` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `setting_key` varchar(50) NOT NULL,
    `setting_value` text NOT NULL,
    `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
    PRIMARY KEY (`id`),
    UNIQUE KEY `idx_key` (`setting_key`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

INSERT IGNORE INTO
    `dashboard_settings` (
        `setting_key`,
        `setting_value`
    )
VALUES (
        'page_title',
        'Dashboard Overview'
    ),
    (
        'page_subtitle',
        'System statistics and analytics'
    ),
    (
        'admin_logo_text',
        'SJDM ADMIN'
    ),
    ('admin_mark_label', 'A');

-- 3. Dashboard Widgets
CREATE TABLE IF NOT EXISTS `dashboard_widgets` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `widget_type` varchar(50) NOT NULL DEFAULT 'stat',
    `title` varchar(100) NOT NULL,
    `subtitle` varchar(255) DEFAULT NULL,
    `icon` varchar(50) DEFAULT NULL,
    `color_class` varchar(20) DEFAULT NULL,
    `query_key` varchar(50) NOT NULL,
    `display_order` int(11) DEFAULT 0,
    `is_active` tinyint(1) DEFAULT 1,
    PRIMARY KEY (`id`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

INSERT IGNORE INTO
    `dashboard_widgets` (
        `title`,
        `subtitle`,
        `icon`,
        `color_class`,
        `query_key`,
        `display_order`
    )
VALUES (
        'Total Users',
        'Registered users in system',
        'people',
        'blue',
        'totalUsers',
        1
    ),
    (
        'Active Users',
        'Currently active accounts',
        'check_circle',
        'green',
        'activeUsers',
        2
    ),
    (
        'Total Bookings',
        'All-time bookings',
        'event',
        'orange',
        'totalBookings',
        3
    ),
    (
        'Today\'s Logins',
        'Successful login attempts',
        'login',
        'purple',
        'todayLogins',
        4
    ),
    (
        'Tour Guides',
        'Available tour guides',
        'tour',
        'teal',
        'totalGuides',
        5
    ),
    (
        'Destinations',
        'Tourist spots available',
        'landscape',
        'pink',
        'totalDestinations',
        6
    ),
    (
        'Hotels',
        'Available accommodations',
        'hotel',
        'yellow',
        'totalHotels',
        7
    ),
    (
        'Pending Bookings',
        'Awaiting confirmation',
        'pending_actions',
        'red',
        'pendingBookings',
        8
    );

-- 4. Admin Menu
CREATE TABLE IF NOT EXISTS `admin_menu` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `title` varchar(50) NOT NULL,
    `icon` varchar(50) NOT NULL,
    `link` varchar(255) NOT NULL,
    `badge_query` varchar(50) DEFAULT NULL,
    `display_order` int(11) DEFAULT 0,
    `is_active` tinyint(1) DEFAULT 1,
    PRIMARY KEY (`id`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

INSERT IGNORE INTO
    `admin_menu` (
        `title`,
        `icon`,
        `link`,
        `badge_query`,
        `display_order`
    )
VALUES (
        'Dashboard',
        'dashboard',
        'dashboard.php',
        NULL,
        1
    ),
    (
        'User Management',
        'people',
        'user-management.php',
        'totalUsers',
        2
    ),
    (
        'Tour Guides',
        'tour',
        'tour-guides.php',
        'totalGuides',
        3
    ),
    (
        'Destinations',
        'place',
        'destinations.php',
        NULL,
        4
    ),
    (
        'Hotels',
        'hotel',
        'hotels.php',
        NULL,
        5
    ),
    (
        'Bookings',
        'event',
        'totalBookings',
        6,
        6
    ),
    (
        'Analytics',
        'analytics',
        'analytics.php',
        NULL,
        7
    ),
    (
        'Reports',
        'description',
        'reports.php',
        NULL,
        8
    ),
    (
        'Settings',
        'settings',
        'settings.php',
        NULL,
        9
    );

-- 5. User Management Settings
CREATE TABLE IF NOT EXISTS `user_management_settings` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `setting_key` varchar(50) NOT NULL,
    `setting_value` text NOT NULL,
    `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
    PRIMARY KEY (`id`),
    UNIQUE KEY `idx_key` (`setting_key`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

INSERT IGNORE INTO
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
    ('default_user_limit', '15');

-- 6. Tour Guide Settings
CREATE TABLE IF NOT EXISTS `tour_guide_settings` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `setting_key` varchar(50) NOT NULL,
    `setting_value` text NOT NULL,
    `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
    PRIMARY KEY (`id`),
    UNIQUE KEY `idx_key` (`setting_key`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

INSERT IGNORE INTO
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
        'Manage tour guides and their professional profiles'
    ),
    ('default_guide_limit', '15');

-- 7. Destination Settings
CREATE TABLE IF NOT EXISTS `destination_settings` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `setting_key` varchar(50) NOT NULL,
    `setting_value` text NOT NULL,
    `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
    PRIMARY KEY (`id`),
    UNIQUE KEY `idx_key` (`setting_key`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

INSERT IGNORE INTO
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
        'Manage tourist spots, landmarks and local destinations'
    ),
    (
        'default_destination_limit',
        '15'
    );

-- 8. Hotel Settings
CREATE TABLE IF NOT EXISTS `hotel_settings` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `setting_key` varchar(50) NOT NULL,
    `setting_value` text NOT NULL,
    `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
    PRIMARY KEY (`id`),
    UNIQUE KEY `idx_key` (`setting_key`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

INSERT IGNORE INTO
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
        'Manage hotels, resorts, and accommodations'
    ),
    ('default_hotel_limit', '15');

-- 9. Booking Settings
CREATE TABLE IF NOT EXISTS `booking_settings` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `setting_key` varchar(50) NOT NULL,
    `setting_value` text NOT NULL,
    `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
    PRIMARY KEY (`id`),
    UNIQUE KEY `idx_key` (`setting_key`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

INSERT IGNORE INTO
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
        'Manage, monitor and process system bookings'
    ),
    ('default_booking_limit', '15');

-- 10. Analytics Settings
CREATE TABLE IF NOT EXISTS `analytics_settings` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `setting_key` varchar(50) NOT NULL,
    `setting_value` text NOT NULL,
    `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
    PRIMARY KEY (`id`),
    UNIQUE KEY `idx_key` (`setting_key`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

INSERT IGNORE INTO
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
        'System performance, trends and actionable insights'
    );

-- 11. Report Settings
CREATE TABLE IF NOT EXISTS `report_settings` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `setting_key` varchar(50) NOT NULL,
    `setting_value` text NOT NULL,
    `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
    PRIMARY KEY (`id`),
    UNIQUE KEY `idx_key` (`setting_key`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

INSERT IGNORE INTO
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
        'Generate, schedule and download system reports'
    );

-- 12. Add Foreign Key for Admins
ALTER TABLE `admins`
ADD CONSTRAINT `fk_admins_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;