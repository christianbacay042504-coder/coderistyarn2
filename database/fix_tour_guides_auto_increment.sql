-- Fix tour_guides table AUTO_INCREMENT issue
-- This script fixes the "Duplicate entry '0' for key 'PRIMARY'" error

-- First, drop the primary key constraint
ALTER TABLE tour_guides DROP PRIMARY KEY;

-- Modify the id column to be AUTO_INCREMENT
ALTER TABLE tour_guides MODIFY COLUMN id INT(11) NOT NULL AUTO_INCREMENT;

-- Re-add the primary key constraint
ALTER TABLE tour_guides ADD PRIMARY KEY (id);

-- Reset the auto-increment value to the next available ID
-- This finds the maximum ID and sets the next value
SET @max_id = (SELECT MAX(id) FROM tour_guides);
SET @next_id = IFNULL(@max_id, 0) + 1;
ALTER TABLE tour_guides AUTO_INCREMENT = @next_id;

-- Verify the fix
SHOW CREATE TABLE tour_guides;
