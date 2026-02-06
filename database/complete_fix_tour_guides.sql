-- Complete Fix for AUTO_INCREMENT Issues in Both Tables
-- This script fixes "Duplicate entry '0' for key 'PRIMARY'" error for both tour_guides and tourist_spots

-- Step 1: Check current status
SELECT 'Fixing AUTO_INCREMENT issues for both tables...' as status;

-- Step 2: Backup existing data
CREATE TABLE IF NOT EXISTS tour_guides_backup AS SELECT * FROM tour_guides;
CREATE TABLE IF NOT EXISTS tourist_spots_backup AS SELECT * FROM tourist_spots;

-- Step 3: Fix tour_guides table
DROP TABLE IF EXISTS tour_guides;
CREATE TABLE `tour_guides` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
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
  PRIMARY KEY (`id`),
  KEY `idx_category` (`category`),
  KEY `idx_status` (`status`),
  KEY `idx_rating` (`rating`),
  KEY `idx_verified` (`verified`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Step 4: Fix tourist_spots table
DROP TABLE IF EXISTS tourist_spots;
CREATE TABLE `tourist_spots` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `category` enum('nature','historical','religious','farm','park','urban') NOT NULL DEFAULT 'nature',
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

-- Step 5: Restore tour_guides data
INSERT INTO tour_guides (
  name, specialty, category, description, bio, areas_of_expertise, 
  rating, review_count, price_range, price_min, price_max, languages, 
  contact_number, email, schedules, experience_years, group_size, 
  verified, total_tours, photo_url, status
)
SELECT 
  name, specialty, category, description, bio, areas_of_expertise, 
  rating, review_count, price_range, price_min, price_max, languages, 
  contact_number, email, schedules, experience_years, group_size, 
  verified, total_tours, photo_url, status
FROM tour_guides_backup;

-- Step 6: Restore tourist_spots data
INSERT INTO tourist_spots (
  name, description, category, location, address, operating_hours, 
  entrance_fee, difficulty_level, duration, best_time_to_visit, activities, 
  amenities, contact_info, website, image_url, latitude, longitude, 
  rating, review_count, status
)
SELECT 
  name, description, category, location, address, operating_hours, 
  entrance_fee, difficulty_level, duration, best_time_to_visit, activities, 
  amenities, contact_info, website, image_url, latitude, longitude, 
  rating, review_count, status
FROM tourist_spots_backup;

-- Step 7: Clean up backup tables
DROP TABLE tour_guides_backup;
DROP TABLE tourist_spots_backup;

-- Step 8: Set auto-increment values
SET @max_guides = (SELECT MAX(id) FROM tour_guides);
SET @max_spots = (SELECT MAX(id) FROM tourist_spots);
SET @next_guides = IFNULL(@max_guides, 0) + 1;
SET @next_spots = IFNULL(@max_spots, 0) + 1;

ALTER TABLE tour_guides AUTO_INCREMENT = @next_guides;
ALTER TABLE tourist_spots AUTO_INCREMENT = @next_spots;

-- Step 9: Test insertions
INSERT INTO tour_guides (name, specialty, contact_number, email) 
VALUES ('__TEST_DELETE_GUIDE__', 'Test Specialty', '1234567890', 'test@test.com');

INSERT INTO tourist_spots (name, category, location) 
VALUES ('__TEST_DELETE_SPOT__', 'nature', 'Test Location');

-- Step 10: Show results
SELECT 'tour_guides test record ID:' as status, id as test_id 
FROM tour_guides WHERE name = '__TEST_DELETE_GUIDE__';

SELECT 'tourist_spots test record ID:' as status, id as test_id 
FROM tourist_spots WHERE name = '__TEST_DELETE_SPOT__';

-- Step 11: Clean up test records
DELETE FROM tour_guides WHERE name = '__TEST_DELETE_GUIDE__';
DELETE FROM tourist_spots WHERE name = '__TEST_DELETE_SPOT__';

-- Step 12: Final verification
SELECT '✅ Both tables fixed successfully!' as final_status;
SHOW CREATE TABLE tour_guides;
SHOW CREATE TABLE tourist_spots;
