-- Safe AUTO_INCREMENT fix for SJDM Tours database
-- This script handles existing data properly to avoid conflicts

USE `sjdm_tours`;

-- Disable foreign key checks temporarily
SET FOREIGN_KEY_CHECKS = 0;

-- Fix users table
ALTER TABLE `users` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

-- Fix login_activity table  
ALTER TABLE `login_activity` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

-- Fix hotels table
ALTER TABLE `hotels` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

-- Fix bookings table
ALTER TABLE `bookings` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

-- Fix saved_tours table
ALTER TABLE `saved_tours` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

-- Set AUTO_INCREMENT values based on existing data
-- Get the maximum ID from each table and set AUTO_INCREMENT accordingly
SET @users_max = (SELECT IFNULL(MAX(id), 0) FROM users);
SET @login_activity_max = (SELECT IFNULL(MAX(id), 0) FROM login_activity);
SET @hotels_max = (SELECT IFNULL(MAX(id), 0) FROM hotels);
SET @bookings_max = (SELECT IFNULL(MAX(id), 0) FROM bookings);
SET @saved_tours_max = (SELECT IFNULL(MAX(id), 0) FROM saved_tours);

-- Set AUTO_INCREMENT to max + 1, or 1 if table is empty
SET @sql = CONCAT('ALTER TABLE users AUTO_INCREMENT = ', @users_max + 1);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @sql = CONCAT('ALTER TABLE login_activity AUTO_INCREMENT = ', @login_activity_max + 1);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @sql = CONCAT('ALTER TABLE hotels AUTO_INCREMENT = ', @hotels_max + 1);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @sql = CONCAT('ALTER TABLE bookings AUTO_INCREMENT = ', @bookings_max + 1);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @sql = CONCAT('ALTER TABLE saved_tours AUTO_INCREMENT = ', @saved_tours_max + 1);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Re-enable foreign key checks
SET FOREIGN_KEY_CHECKS = 1;

-- Display current AUTO_INCREMENT values for verification
SELECT 
    'users' as table_name, AUTO_INCREMENT as current_value 
FROM information_schema.TABLES 
WHERE TABLE_SCHEMA = 'sjdm_tours' AND TABLE_NAME = 'users'
UNION ALL
SELECT 
    'login_activity' as table_name, AUTO_INCREMENT as current_value 
FROM information_schema.TABLES 
WHERE TABLE_SCHEMA = 'sjdm_tours' AND TABLE_NAME = 'login_activity'
UNION ALL
SELECT 
    'hotels' as table_name, AUTO_INCREMENT as current_value 
FROM information_schema.TABLES 
WHERE TABLE_SCHEMA = 'sjdm_tours' AND TABLE_NAME = 'hotels'
UNION ALL
SELECT 
    'bookings' as table_name, AUTO_INCREMENT as current_value 
FROM information_schema.TABLES 
WHERE TABLE_SCHEMA = 'sjdm_tours' AND TABLE_NAME = 'bookings'
UNION ALL
SELECT 
    'saved_tours' as table_name, AUTO_INCREMENT as current_value 
FROM information_schema.TABLES 
WHERE TABLE_SCHEMA = 'sjdm_tours' AND TABLE_NAME = 'saved_tours';

SELECT 'AUTO_INCREMENT fix completed successfully!' as status;
