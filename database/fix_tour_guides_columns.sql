-- Add missing columns to tour_guides table
-- Run this script to fix the "Unknown column 'price_range'" error

ALTER TABLE tour_guides 
ADD COLUMN IF NOT EXISTS price_range VARCHAR(100),
ADD COLUMN IF NOT EXISTS price_min DECIMAL(10, 2),
ADD COLUMN IF NOT EXISTS price_max DECIMAL(10, 2),
ADD COLUMN IF NOT EXISTS areas_of_expertise TEXT,
ADD COLUMN IF NOT EXISTS languages VARCHAR(200),
ADD COLUMN IF NOT EXISTS schedules TEXT,
ADD COLUMN IF NOT EXISTS experience_years INT,
ADD COLUMN IF NOT EXISTS group_size VARCHAR(50),
ADD COLUMN IF NOT EXISTS total_tours INT DEFAULT 0,
ADD COLUMN IF NOT EXISTS photo_url VARCHAR(500),
ADD COLUMN IF NOT EXISTS bio TEXT,
ADD COLUMN IF NOT EXISTS description TEXT,
ADD COLUMN IF NOT EXISTS category ENUM('Adventure', 'Cultural', 'Nature', 'Historical', 'Food & Cuisine', 'Photography') NOT NULL DEFAULT 'general',
ADD COLUMN IF NOT EXISTS verified BOOLEAN DEFAULT FALSE,
ADD COLUMN IF NOT EXISTS status ENUM('active', 'inactive') DEFAULT 'active',
ADD COLUMN IF NOT EXISTS created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
ADD COLUMN IF NOT EXISTS updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;

-- Update existing records to have default values
UPDATE tour_guides SET 
    price_range = 'Mid-range' WHERE price_range IS NULL,
    price_min = 1000 WHERE price_min IS NULL,
    price_max = 3000 WHERE price_max IS NULL,
    experience_years = 0 WHERE experience_years IS NULL,
    group_size = '10' WHERE group_size IS NULL,
    total_tours = 0 WHERE total_tours IS NULL,
    status = 'active' WHERE status IS NULL,
    verified = FALSE WHERE verified IS NULL;
