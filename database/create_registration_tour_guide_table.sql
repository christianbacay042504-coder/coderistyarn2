-- Create registration_tour_guide table for tour guide applications
-- Created: February 16, 2026

CREATE TABLE IF NOT EXISTS `registration_tour_guide` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `application_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    
    -- Personal Information
    `last_name` varchar(100) NOT NULL,
    `first_name` varchar(100) NOT NULL,
    `middle_initial` varchar(5) DEFAULT NULL,
    `preferred_name` varchar(100) DEFAULT NULL,
    `date_of_birth` date NOT NULL,
    `gender` enum('male','female') NOT NULL,
    `home_address` text NOT NULL,
    `primary_phone` varchar(20) NOT NULL,
    `secondary_phone` varchar(20) DEFAULT NULL,
    `email` varchar(255) NOT NULL,
    `emergency_contact_name` varchar(100) NOT NULL,
    `emergency_contact_relationship` varchar(50) NOT NULL,
    `emergency_contact_phone` varchar(20) NOT NULL,
    
    -- Professional Qualifications
    `dot_accreditation` varchar(100) NOT NULL,
    `accreditation_expiry` date NOT NULL,
    `specialization` enum('mountain','waterfall','cultural','adventure','photography') NOT NULL,
    `years_experience` int(3) NOT NULL DEFAULT 0,
    `first_aid_certified` enum('yes','no') NOT NULL,
    `first_aid_expiry` date DEFAULT NULL,
    
    -- Logistics & Availability
    `base_location` varchar(255) NOT NULL,
    `employment_type` enum('full-time','part-time','weekends') NOT NULL,
    `has_vehicle` enum('yes','no') NOT NULL,
    
    -- Document File Paths
    `resume_file` varchar(255) DEFAULT NULL,
    `dot_id_file` varchar(255) DEFAULT NULL,
    `government_id_file` varchar(255) DEFAULT NULL,
    `nbi_clearance_file` varchar(255) DEFAULT NULL,
    `first_aid_certificate_file` varchar(255) DEFAULT NULL,
    `id_photo_file` varchar(255) DEFAULT NULL,
    
    -- Application Status
    `status` enum('pending','under_review','approved','rejected') NOT NULL DEFAULT 'pending',
    `admin_notes` text DEFAULT NULL,
    `review_date` datetime DEFAULT NULL,
    `reviewed_by` int(11) DEFAULT NULL,
    
    PRIMARY KEY (`id`),
    UNIQUE KEY `email` (`email`),
    UNIQUE KEY `dot_accreditation` (`dot_accreditation`),
    KEY `status` (`status`),
    KEY `application_date` (`application_date`),
    KEY `specialization` (`specialization`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create separate table for languages (since guide can speak multiple languages)
CREATE TABLE IF NOT EXISTS `tour_guide_languages` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `registration_id` int(11) NOT NULL,
    `language` varchar(50) NOT NULL,
    `proficiency` enum('native','fluent','conversational') NOT NULL,
    PRIMARY KEY (`id`),
    KEY `registration_id` (`registration_id`),
    FOREIGN KEY (`registration_id`) REFERENCES `registration_tour_guide`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
