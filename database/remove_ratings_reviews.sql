-- Remove ratings and reviews from database
-- This script removes rating and review_count columns from all relevant tables

-- Remove from hotels table
ALTER TABLE hotels DROP COLUMN IF EXISTS rating;
ALTER TABLE hotels DROP COLUMN IF EXISTS review_count;

-- Remove from tourist_spots table  
ALTER TABLE tourist_spots DROP COLUMN IF EXISTS rating;
ALTER TABLE tourist_spots DROP COLUMN IF EXISTS review_count;

-- Remove from tour_guides table
ALTER TABLE tour_guides DROP COLUMN IF EXISTS rating;
ALTER TABLE tour_guides DROP COLUMN IF EXISTS review_count;

-- Remove from restaurants table (if exists)
ALTER TABLE restaurants DROP COLUMN IF EXISTS rating;
ALTER TABLE restaurants DROP COLUMN IF EXISTS review_count;

-- Update any references in admin_pages content
UPDATE admin_pages SET content = REPLACE(content, 'Average Rating: 4.4/5 stars', 'High Quality Service') WHERE content LIKE '%Average Rating%';

-- Remove any review-related settings or configurations
DELETE FROM admin_settings WHERE setting_name LIKE '%rating%' OR setting_name LIKE '%review%';

-- Note: This script only removes rating columns, not the entire review functionality
-- since there are no separate review tables found in the database schema
