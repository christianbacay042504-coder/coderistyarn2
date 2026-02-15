-- Update specialization column to ensure it can store JSON data properly
-- This script will fix the specialization column issue

-- First, let's see the current structure
DESCRIBE registration_tour_guides;

-- Update the specialization column to ensure it's properly configured
ALTER TABLE registration_tour_guides 
MODIFY COLUMN specialization TEXT DEFAULT NULL 
COMMENT 'JSON format: ["mountain","waterfall","cultural","adventure","photography"]';

-- Test the column with sample data
UPDATE registration_tour_guides 
SET specialization = '["mountain"]' 
WHERE specialization = '0' OR specialization IS NULL OR specialization = '';

-- Verify the update
SELECT id, specialization, 
       JSON_EXTRACT(specialization, '$[0]') as first_specialization
FROM registration_tour_guides 
ORDER BY id DESC LIMIT 5;

-- Show table structure after update
DESCRIBE registration_tour_guides;
