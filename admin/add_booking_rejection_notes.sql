-- Add notes field to bookings table for rejection reasons
ALTER TABLE `bookings` ADD COLUMN `rejection_notes` TEXT NULL AFTER `special_requests`;

-- Add index for better performance if needed
-- ALTER TABLE `bookings` ADD INDEX `idx_rejection_notes` (`rejection_notes`(255));
