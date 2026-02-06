<?php
// Database Setup Script for User Preferences
// This script will create the necessary tables for user preferences

require_once __DIR__ . '/config/database.php';

echo "Setting up user preferences database...\n";

try {
    $conn = getDatabaseConnection();
    
    if (!$conn) {
        die("Database connection failed!\n");
    }
    
    echo "Connected to database successfully.\n";
    
    // Read and execute SQL from file
    $sqlFile = __DIR__ . '/database/create_user_preferences.sql';
    if (file_exists($sqlFile)) {
        $sql = file_get_contents($sqlFile);
        
        // Split SQL statements by semicolon
        $statements = array_filter(array_map('trim', explode(';', $sql)));
        
        foreach ($statements as $statement) {
            if (!empty($statement)) {
                echo "Executing: " . substr($statement, 0, 50) . "...\n";
                if ($conn->query($statement)) {
                    echo "✓ Success\n";
                } else {
                    echo "✗ Error: " . $conn->error . "\n";
                }
            }
        }
        
        echo "\nDatabase setup completed!\n";
        
        // Verify tables were created
        echo "\nVerifying tables...\n";
        $tables = ['user_preferences', 'available_categories'];
        foreach ($tables as $table) {
            $result = $conn->query("SHOW TABLES LIKE '$table'");
            if ($result->num_rows > 0) {
                echo "✓ Table '$table' exists\n";
            } else {
                echo "✗ Table '$table' not found\n";
            }
        }
        
        // Check if preferences_set column was added to users table
        $result = $conn->query("SHOW COLUMNS FROM users LIKE 'preferences_set'");
        if ($result->num_rows > 0) {
            echo "✓ Column 'preferences_set' exists in users table\n";
        } else {
            echo "✗ Column 'preferences_set' not found in users table\n";
        }
        
        // Count categories inserted
        $result = $conn->query("SELECT COUNT(*) as count FROM available_categories");
        $count = $result->fetch_assoc()['count'];
        echo "✓ $count categories inserted into available_categories\n";
        
    } else {
        echo "SQL file not found: $sqlFile\n";
    }
    
    closeDatabaseConnection($conn);
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

echo "\nSetup script finished.\n";
?>
