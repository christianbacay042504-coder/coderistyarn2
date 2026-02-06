<?php
// Debug script to check user preferences setup
require_once __DIR__ . '/config/database.php';

echo "=== User Preferences Debug ===\n\n";

try {
    $conn = getDatabaseConnection();
    
    if (!$conn) {
        die("❌ Database connection failed!\n");
    }
    
    echo "✅ Database connected\n\n";
    
    // Check if user_preferences table exists
    $result = $conn->query("SHOW TABLES LIKE 'user_preferences'");
    if ($result->num_rows > 0) {
        echo "✅ user_preferences table exists\n";
    } else {
        echo "❌ user_preferences table MISSING\n";
    }
    
    // Check if available_categories table exists
    $result = $conn->query("SHOW TABLES LIKE 'available_categories'");
    if ($result->num_rows > 0) {
        echo "✅ available_categories table exists\n";
    } else {
        echo "❌ available_categories table MISSING\n";
    }
    
    // Check if preferences_set column exists in users table
    $result = $conn->query("SHOW COLUMNS FROM users LIKE 'preferences_set'");
    if ($result->num_rows > 0) {
        echo "✅ preferences_set column exists in users table\n";
    } else {
        echo "❌ preferences_set column MISSING in users table\n";
    }
    
    // Check a sample user to see preferences_set value
    $result = $conn->query("SELECT id, email, preferences_set FROM users LIMIT 5");
    if ($result->num_rows > 0) {
        echo "\n📋 Sample users:\n";
        while ($row = $result->fetch_assoc()) {
            echo "ID: {$row['id']}, Email: {$row['email']}, preferences_set: " . ($row['preferences_set'] ?? 'NULL') . "\n";
        }
    } else {
        echo "\n❌ No users found in database\n";
    }
    
    // Count categories
    $result = $conn->query("SELECT COUNT(*) as count FROM available_categories");
    $count = $result->fetch_assoc()['count'];
    echo "\n📊 Categories in available_categories: $count\n";
    
    // Show categories
    $result = $conn->query("SELECT name, display_name FROM available_categories ORDER BY display_name");
    if ($result->num_rows > 0) {
        echo "\n📝 Available categories:\n";
        while ($row = $result->fetch_assoc()) {
            echo "- {$row['display_name']} ({$row['name']})\n";
        }
    }
    
    closeDatabaseConnection($conn);
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

echo "\n=== End Debug ===\n";
?>
