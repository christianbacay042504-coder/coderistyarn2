-- Fix AUTO_INCREMENT issues for tables missing it
-- This will resolve the "Duplicate entry '0' for key 'PRIMARY'" error

USE `sjdm_tours`;

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

-- Set the AUTO_INCREMENT starting values based on existing data
-- This prevents conflicts with existing records
SELECT MAX(id) + 1 AS next_id FROM users;
SELECT MAX(id) + 1 AS next_id FROM login_activity;  
SELECT MAX(id) + 1 AS next_id FROM hotels;
SELECT MAX(id) + 1 AS next_id FROM bookings;
SELECT MAX(id) + 1 AS next_id FROM saved_tours;

-- Set AUTO_INCREMENT values (uncomment and adjust based on the results above)
-- ALTER TABLE users AUTO_INCREMENT = [next_users_id];
-- ALTER TABLE login_activity AUTO_INCREMENT = [next_login_activity_id];
-- ALTER TABLE hotels AUTO_INCREMENT = [next_hotels_id];
-- ALTER TABLE bookings AUTO_INCREMENT = [next_bookings_id];
-- ALTER TABLE saved_tours AUTO_INCREMENT = [next_saved_tours_id];

-- If tables are empty, start from 1
ALTER TABLE users AUTO_INCREMENT = 1;
ALTER TABLE login_activity AUTO_INCREMENT = 1;  
ALTER TABLE hotels AUTO_INCREMENT = 1;
ALTER TABLE bookings AUTO_INCREMENT = 1;
ALTER TABLE saved_tours AUTO_INCREMENT = 1;
