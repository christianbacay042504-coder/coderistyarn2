<?php
// Quick fix script to ensure preferences_set column exists
require_once __DIR__ . '/config/database.php';

echo "=== Fix User Preferences Setup ===\n\n";

try {
    $conn = getDatabaseConnection();
    
    if (!$conn) {
        die("❌ Database connection failed!\n");
    }
    
    echo "✅ Database connected\n\n";
    
    // Check and add preferences_set column if it doesn't exist
    $result = $conn->query("SHOW COLUMNS FROM users LIKE 'preferences_set'");
    if ($result->num_rows === 0) {
        echo "➕ Adding preferences_set column to users table...\n";
        $sql = "ALTER TABLE users ADD COLUMN preferences_set TINYINT(1) DEFAULT 0";
        if ($conn->query($sql)) {
            echo "✅ preferences_set column added successfully\n";
        } else {
            echo "❌ Failed to add preferences_set column: " . $conn->error . "\n";
        }
    } else {
        echo "✅ preferences_set column already exists\n";
    }
    
    // Create user_preferences table if it doesn't exist
    $result = $conn->query("SHOW TABLES LIKE 'user_preferences'");
    if ($result->num_rows === 0) {
        echo "➕ Creating user_preferences table...\n";
        $sql = "CREATE TABLE user_preferences (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            category VARCHAR(100) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
            UNIQUE KEY unique_user_category (user_id, category)
        )";
        if ($conn->query($sql)) {
            echo "✅ user_preferences table created successfully\n";
        } else {
            echo "❌ Failed to create user_preferences table: " . $conn->error . "\n";
        }
    } else {
        echo "✅ user_preferences table already exists\n";
    }
    
    // Create available_categories table if it doesn't exist
    $result = $conn->query("SHOW TABLES LIKE 'available_categories'");
    if ($result->num_rows === 0) {
        echo "➕ Creating available_categories table...\n";
        $sql = "CREATE TABLE available_categories (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) NOT NULL UNIQUE,
            display_name VARCHAR(100) NOT NULL,
            icon VARCHAR(50) DEFAULT 'category',
            status ENUM('active', 'inactive') DEFAULT 'active',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )";
        if ($conn->query($sql)) {
            echo "✅ available_categories table created successfully\n";
        } else {
            echo "❌ Failed to create available_categories table: " . $conn->error . "\n";
        }
    } else {
        echo "✅ available_categories table already exists\n";
    }
    
    // Insert categories if table is empty
    $result = $conn->query("SELECT COUNT(*) as count FROM available_categories");
    $count = $result->fetch_assoc()['count'];
    if ($count == 0) {
        echo "➕ Inserting categories...\n";
        $categories = [
            ['nature', 'Nature & Waterfalls', 'forest'],
            ['farm', 'Farms & Eco-Tourism', 'agriculture'],
            ['park', 'Parks & Recreation', 'park'],
            ['adventure', 'Adventure & Activities', 'hiking'],
            ['cultural', 'Cultural & Historical', 'museum'],
            ['religious', 'Religious Sites', 'church'],
            ['entertainment', 'Entertainment & Leisure', 'sports_esports'],
            ['food', 'Food & Dining', 'restaurant'],
            ['shopping', 'Shopping & Markets', 'shopping_cart'],
            ['wellness', 'Wellness & Relaxation', 'spa'],
            ['education', 'Educational & Learning', 'school'],
            ['family', 'Family-Friendly', 'family_restroom'],
            ['photography', 'Photography Spots', 'photo_camera'],
            ['wildlife', 'Wildlife & Nature', 'pets'],
            ['outdoor', 'Outdoor Activities', 'terrain']
        ];
        
        $stmt = $conn->prepare("INSERT INTO available_categories (name, display_name, icon) VALUES (?, ?, ?)");
        foreach ($categories as $category) {
            $stmt->bind_param("sss", $category[0], $category[1], $category[2]);
            $stmt->execute();
        }
        $stmt->close();
        echo "✅ " . count($categories) . " categories inserted\n";
    } else {
        echo "✅ Categories already exist ($count found)\n";
    }
    
    // Reset all existing users to have preferences_set = 0 (for testing)
    echo "➕ Resetting existing users preferences_set to 0 for testing...\n";
    if ($conn->query("UPDATE users SET preferences_set = 0 WHERE preferences_set IS NULL OR preferences_set = 1")) {
        $affected = $conn->affected_rows;
        echo "✅ Reset $affected users to preferences_set = 0\n";
    } else {
        echo "❌ Failed to reset users: " . $conn->error . "\n";
    }
    
    closeDatabaseConnection($conn);
    
    echo "\n🎉 Setup completed! Try logging in with a new account now.\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

echo "\n=== End Fix ===\n";
?>
