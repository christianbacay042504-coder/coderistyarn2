<?php
// Database import script
echo "Importing SJDM Tours database...\n";

// Read SQL file
$sqlFile = 'sjdm_tours.sql';
$sql = file_get_contents($sqlFile);

if (!$sql) {
    die("Error: Could not read SQL file\n");
}

// Connect to MySQL without database
$host = 'localhost';
$user = 'root';
$pass = ''; // Empty password for XAMPP

try {
    $conn = new mysqli($host, $user, $pass);
    
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error . "\n");
    }
    
    // Create database if not exists
    $conn->query("CREATE DATABASE IF NOT EXISTS sjdm_tours");
    $conn->query("USE sjdm_tours");
    
    // Execute SQL commands
    if ($conn->multi_query($sql)) {
        echo "Database imported successfully!\n";
        
        // Check if tour guide accounts exist
        $result = $conn->query("SELECT COUNT(*) as count FROM users WHERE user_type = 'tour_guide'");
        if ($result) {
            $row = $result->fetch_assoc();
            echo "Tour guide accounts found: " . $row['count'] . "\n";
        }
        
    } else {
        echo "Error importing database: " . $conn->error . "\n";
    }
    
    $conn->close();
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
