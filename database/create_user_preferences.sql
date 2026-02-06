-- Create user preferences table for tourist spot categories
CREATE TABLE IF NOT EXISTS user_preferences (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    category VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_category (user_id, category)
);

-- Add column to users table to track if preferences have been set
ALTER TABLE users ADD COLUMN preferences_set TINYINT(1) DEFAULT 0;

-- Insert tourist spot categories based on the tourist-spots.php file
INSERT IGNORE INTO available_categories (name, display_name, icon) VALUES
('nature', 'Nature & Waterfalls', 'forest'),
('farm', 'Farms & Eco-Tourism', 'agriculture'),
('park', 'Parks & Recreation', 'park'),
('adventure', 'Adventure & Activities', 'hiking'),
('cultural', 'Cultural & Historical', 'museum'),
('religious', 'Religious Sites', 'church'),
('entertainment', 'Entertainment & Leisure', 'sports_esports'),
('food', 'Food & Dining', 'restaurant'),
('shopping', 'Shopping & Markets', 'shopping_cart'),
('wellness', 'Wellness & Relaxation', 'spa'),
('education', 'Educational & Learning', 'school'),
('family', 'Family-Friendly', 'family_restroom'),
('photography', 'Photography Spots', 'photo_camera'),
('wildlife', 'Wildlife & Nature', 'pets'),
('outdoor', 'Outdoor Activities', 'terrain');
