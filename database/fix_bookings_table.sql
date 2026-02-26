-- Add missing columns to bookings table
-- Run this in your MySQL database

USE sjdm_tours;

ALTER TABLE bookings 
ADD COLUMN IF NOT EXISTS guide_id INT NULL,
ADD COLUMN IF NOT EXISTS destination VARCHAR(200) NOT NULL DEFAULT '',
ADD COLUMN IF NOT EXISTS contact_number VARCHAR(50) NOT NULL DEFAULT '',
ADD COLUMN IF NOT EXISTS email VARCHAR(100) NOT NULL DEFAULT '',
ADD COLUMN IF NOT EXISTS special_requests TEXT NULL,
ADD COLUMN IF NOT EXISTS booking_reference VARCHAR(50) NOT NULL DEFAULT '';

-- Add foreign key constraint for guide_id if tour_guides table exists
ALTER TABLE bookings 
ADD CONSTRAINT IF NOT EXISTS fk_bookings_guide_id 
FOREIGN KEY (guide_id) REFERENCES tour_guides(id) ON DELETE SET NULL;
