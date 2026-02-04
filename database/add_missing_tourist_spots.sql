-- Add Missing Tourist Spots
-- This script adds Padre Pio Parish and Paradise Hill Farm to the tourist_spots table

USE sjdm_tours;

-- Insert Padre Pio Parish
INSERT INTO tourist_spots (name, description, category, location, address, operating_hours, entrance_fee, difficulty_level, duration, best_time_to_visit, activities, amenities, contact_info, image_url, rating, review_count) VALUES
('Padre Pio Parish', 'Religious shrine featuring a giant statue of St. Padre Pio on the hill. Open 24/7 for prayer, meditation, and peaceful reflection with panoramic city views.', 'religious', 'Tungkong Mangga', 'Tungkong Mangga, San Jose del Monte City', '24/7', 'Free', 'easy', '1-2 hours', 'Any time', 'Prayer, Meditation, Photography, Sightseeing', 'Parking, Prayer areas, Viewing decks', 'Parish Office', 'https://images.unsplash.com/photo-1544919982-b61976a0d7ed?q=80&w=2069&auto=format&fit=crop', 4.6, 112);

-- Insert Paradise Hill Farm
INSERT INTO tourist_spots (name, description, category, location, address, operating_hours, entrance_fee, difficulty_level, duration, best_time_to_visit, activities, amenities, contact_info, image_url, rating, review_count) VALUES
('Paradise Hill Farm', 'Scenic farm resort offering beautiful landscapes, organic farming experiences, and recreational activities. Perfect for family outings and nature lovers.', 'farm', 'Paradise 3', 'Paradise 3 area, San Jose del Monte City', '8:00 AM - 6:00 PM', '₱200 per person', 'easy', '3-4 hours', 'Weekends', 'Farm tours, Photography, Nature walks, Recreational activities', 'Restaurant, Rest areas, Parking, Photo spots', 'Farm Management', 'https://images.unsplash.com/photo-1500382017468-9049fed747ef?q=80&w=2070&auto=format&fit=crop', 4.5, 89);

-- Update existing tourist spots to add "No Tour Guide Required" flag
-- First, add the column if it doesn't exist
ALTER TABLE tourist_spots 
ADD COLUMN no_guide_required BOOLEAN DEFAULT FALSE;

-- Set no_guide_required = TRUE for the specified tourist spots
UPDATE tourist_spots SET no_guide_required = TRUE WHERE name IN (
    'Abes Farm',
    'City Oval (People\'s Park)', 
    'Our Lady of Lourdes Parish',
    'Padre Pio Parish',
    'Paradise Hill Farm',
    'The Rising Heart Monument'
);
