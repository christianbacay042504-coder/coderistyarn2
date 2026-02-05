<?php
require_once 'config/database.php';

echo "Testing database connection...\n";

$conn = getDatabaseConnection();
if ($conn) {
    echo "Database connection: SUCCESS\n";
    
    // Test if users table exists
    $result = $conn->query("SHOW TABLES LIKE 'users'");
    if ($result->num_rows > 0) {
        echo "Users table: EXISTS\n";
        
        // Check if there are any users
        $count = $conn->query("SELECT COUNT(*) as count FROM users");
        $row = $count->fetch_assoc();
        echo "Number of users: " . $row['count'] . "\n";
    } else {
        echo "Users table: NOT FOUND\n";
    }
    
    closeDatabaseConnection($conn);
} else {
    echo "Database connection: FAILED\n";
    echo "Check your database configuration in config/database.php\n";
}
?>
