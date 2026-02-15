-- Create registration_tour_guides table for standalone tour guide applications
-- This table is independent and not connected to users or tour_guides tables

CREATE TABLE IF NOT EXISTS `registration_tour_guides` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `status` enum('Pending','Approved','Rejected') NOT NULL DEFAULT 'Pending',
    `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    -- I. Personal Information
    `last_name` varchar(100) NOT NULL,
    `first_name` varchar(100) NOT NULL,
    `middle_initial` varchar(5) DEFAULT NULL,
    `preferred_name` varchar(100) DEFAULT NULL,
    `date_of_birth` date DEFAULT NULL,
    `gender` enum('male','female') DEFAULT NULL,
    `home_address` text DEFAULT NULL,
    `primary_phone` varchar(20) NOT NULL,
    `secondary_phone` varchar(20) DEFAULT NULL,
    `email` varchar(255) NOT NULL,
    `emergency_contact_name` varchar(100) NOT NULL,
    `emergency_contact_relationship` varchar(50) NOT NULL,
    `emergency_contact_phone` varchar(20) NOT NULL,
    
    -- II. Professional Qualifications
    `dot_accreditation_number` varchar(100) NOT NULL,
    `accreditation_expiry_date` date DEFAULT NULL,
    `languages_spoken` text DEFAULT NULL, -- JSON format: [{"language":"english","proficiency":"fluent"},...]
    `specialization` text DEFAULT NULL, -- JSON format: ["mountain","waterfall","cultural","adventure","photography"]
    `years_of_experience` int(3) DEFAULT NULL,
    `first_aid_certified` enum('yes','no') DEFAULT NULL,
    `first_aid_expiry_date` date DEFAULT NULL,
    
    -- III. Logistics & Availability
    `base_location` varchar(255) NOT NULL,
    `employment_type` enum('full-time','part-time','weekends') DEFAULT NULL,
    `has_vehicle` enum('yes','no') DEFAULT NULL,
    
    -- IV. Required Documents (file paths)
    `resume_path` varchar(500) DEFAULT NULL,
    `dot_id_path` varchar(500) DEFAULT NULL,
    `government_id_path` varchar(500) DEFAULT NULL,
    `nbi_clearance_path` varchar(500) DEFAULT NULL,
    `first_aid_certificate_path` varchar(500) DEFAULT NULL,
    `id_photo_path` varchar(500) DEFAULT NULL,
    
    -- Additional fields for admin processing
    `admin_notes` text DEFAULT NULL,
    `processed_by` varchar(100) DEFAULT NULL,
    `processed_date` timestamp NULL DEFAULT NULL,
    
    PRIMARY KEY (`id`),
    UNIQUE KEY `email` (`email`),
    KEY `status` (`status`),
    KEY `created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Add indexes for better performance
CREATE INDEX `idx_registration_status_date` ON `registration_tour_guides` (`status`, `created_at`);
CREATE INDEX `idx_registration_name` ON `registration_tour_guides` (`last_name`, `first_name`);
