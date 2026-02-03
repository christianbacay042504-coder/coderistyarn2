-- SJDM Tours Database - Homepage Content
-- Import this file to set up the database structure and data

-- Create database if it doesn't exist
CREATE DATABASE IF NOT EXISTS `sjdm_tours` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `sjdm_tours`;

-- --------------------------------------------------------

--
-- Table structure for table `homepage_content`
--

CREATE TABLE `homepage_content` (
  `id` int(11) NOT NULL,
  `content_type` enum('hero_title','hero_subtitle','hero_button_text','stat_title','stat_value','section_title') NOT NULL,
  `content_key` varchar(100) NOT NULL,
  `content_value` text NOT NULL,
  `display_order` int(11) DEFAULT 0,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `homepage_content`
--

INSERT INTO `homepage_content` (`id`, `content_type`, `content_key`, `conMonte?', 5, 'active', '2026-tent_value`, `display_order`, `status`, `created_at`, `updated_at`) VALUES
(1, 'hero_title', 'main_title', 'Welcome to San Jose del Monte, Bulacan', 1, 'active', '2026-02-03 10:57:00', '2026-02-03 10:57:00'),
(2, 'hero_subtitle', 'main_subtitle', 'The Balcony of Metropolis - Where Nature Meets Progress', 2, 'active', '2026-02-03 10:57:00', '2026-02-03 10:57:00'),
(3, 'hero_button_text', 'main_button', 'Find Your Guide', 3, 'active', '2026-02-03 10:57:00', '2026-02-03 10:57:00'),
(4, 'section_title', 'featured_destinations', 'Featured Destinations', 4, 'active', '2026-02-03 10:57:00', '2026-02-03 10:57:00'),
(5, 'section_title', 'why_visit', 'Why Visit San Jose del 02-03 10:57:00', '2026-02-03 10:57:00'),
(6, 'stat_title', 'natural_attractions', 'Natural Attractions', 1, 'active', '2026-02-03 10:57:00', '2026-02-03 10:57:00'),
(7, 'stat_value', 'natural_attractions', '10+', 1, 'active', '2026-02-03 10:57:00', '2026-02-03 10:57:00'),
(8, 'stat_title', 'travel_time', 'From Metro Manila', 2, 'active', '2026-02-03 10:57:00', '2026-02-03 10:57:00'),
(9, 'stat_value', 'travel_time', '30 min', 2, 'active', '2026-02-03 10:57:00', '2026-02-03 10:57:00'),
(10, 'stat_title', 'climate', 'Perfect Climate', 3, 'active', '2026-02-03 10:57:00', '2026-02-03 10:57:00'),
(11, 'stat_value', 'climate', 'Year-round', 3, 'active', '2026-02-03 10:57:00', '2026-02-03 10:57:00');

-- --------------------------------------------------------

--
-- Indexes for table `homepage_content`
--

ALTER TABLE `homepage_content`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_content_type` (`content_type`),
  ADD KEY `idx_content_key` (`content_key`),
  ADD KEY `idx_status` (`status`);

--
-- AUTO_INCREMENT for table `homepage_content`
--

ALTER TABLE `homepage_content`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

-- --------------------------------------------------------

-- Complete! The homepage_content table is now ready for use.
-- You can now update any homepage content by modifying the records in this table.
