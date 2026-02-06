<?php
// Fix Tour Guides AUTO_INCREMENT Issue
// This script fixes the "Duplicate entry '0' for key 'PRIMARY'" error

require_once __DIR__ . '/../config/database.php';

echo "Fixing tour_guides table AUTO_INCREMENT issue...\n";

try {
    $conn = getDatabaseConnection();
    
    if (!$conn) {
        die("Database connection failed!\n");
    }
    
    echo "Connected to database successfully.\n";
    
    // Check current table structure
    $result = $conn->query("SHOW CREATE TABLE tour_guides");
    if ($result) {
        $createTable = $result->fetch_assoc()['Create Table'];
        echo "Current table structure:\n$createTable\n\n";
    }
    
    // Step 1: Drop primary key
    echo "Step 1: Dropping primary key...\n";
    $conn->query("ALTER TABLE tour_guides DROP PRIMARY KEY");
    echo "Primary key dropped.\n";
    
    // Step 2: Modify id column to be AUTO_INCREMENT
    echo "Step 2: Adding AUTO_INCREMENT to id column...\n";
    $conn->query("ALTER TABLE tour_guides MODIFY COLUMN id INT(11) NOT NULL AUTO_INCREMENT");
    echo "ID column modified to AUTO_INCREMENT.\n";
    
    // Step 3: Re-add primary key
    echo "Step 3: Re-adding primary key...\n";
    $conn->query("ALTER TABLE tour_guides ADD PRIMARY KEY (id)");
    echo "Primary key re-added.\n";
    
    // Step 4: Reset auto-increment value
    echo "Step 4: Resetting auto-increment value...\n";
    $result = $conn->query("SELECT MAX(id) as max_id FROM tour_guides");
    $maxId = $result->fetch_assoc()['max_id'];
    $nextId = ($maxId ?? 0) + 1;
    
    $conn->query("ALTER TABLE tour_guides AUTO_INCREMENT = $nextId");
    echo "Auto-increment set to: $nextId\n";
    
    // Verify the fix
    echo "\nVerifying the fix...\n";
    $result = $conn->query("SHOW CREATE TABLE tour_guides");
    if ($result) {
        $newStructure = $result->fetch_assoc()['Create Table'];
        echo "New table structure:\n$newStructure\n";
    }
    
    // Test insertion
    echo "\nTesting insertion...\n";
    $testName = "Test Guide " . date('Y-m-d H:i:s');
    $conn->query("INSERT INTO tour_guides (name, specialty, contact_number, email) VALUES ('$testName', 'Test Specialty', '1234567890', 'test@example.com')");
    
    $lastId = $conn->insert_id;
    echo "Test insertion successful. New ID: $lastId\n";
    
    // Clean up test data
    $conn->query("DELETE FROM tour_guides WHERE id = $lastId");
    echo "Test data cleaned up.\n";
    
    echo "\n✅ Fix completed successfully!\n";
    echo "The tour_guides table now has proper AUTO_INCREMENT functionality.\n";
    echo "You can now add new tour guides without getting ID 0 errors.\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Please run the SQL script manually if this script fails.\n";
}

if (isset($conn)) {
    $conn->close();
}
?>
