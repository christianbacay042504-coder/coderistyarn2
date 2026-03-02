-- Add tour_guide_id column to bookings table
-- This will allow automatic assignment of tour guides to destinations

ALTER TABLE bookings 
ADD COLUMN tour_guide_id INT NULL AFTER total_amount,
ADD FOREIGN KEY (tour_guide_id) REFERENCES tour_guides(id) ON DELETE SET NULL;

-- Add index for better performance
ALTER TABLE bookings 
ADD INDEX idx_tour_guide_id (tour_guide_id);
