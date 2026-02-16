-- Create table for tour guide registrations
CREATE TABLE IF NOT EXISTS `tour_guide_registrations` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `guide_id` INT NOT NULL,
    `full_name` VARCHAR(255) NOT NULL,
    `email` VARCHAR(255) NOT NULL,
    `phone` VARCHAR(20) DEFAULT NULL,
    `specialty` VARCHAR(100) DEFAULT NULL,
    `experience_years` INT DEFAULT 0,
    `group_size` INT DEFAULT 1,
    `price_range` VARCHAR(50) DEFAULT NULL,
    `languages` TEXT DEFAULT NULL,
    `areas_of_expertise` TEXT DEFAULT NULL,
    `status` ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    `resume_url` VARCHAR(500) DEFAULT NULL,
    `cover_letter` VARCHAR(1000) DEFAULT NULL,
    `application_date` DATE DEFAULT CURRENT_TIMESTAMP,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`guide_id`) REFERENCES `tour_guides`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
