<?php
require_once 'config/database.php';
require_once 'config/auth.php';

echo "=== Login Debug Test ===\n";

// Test database connection
$conn = getDatabaseConnection();
if ($conn) {
    echo "✓ Database connection: SUCCESS\n";
    
    // Check if users table exists and has data
    $result = $conn->query("SELECT COUNT(*) as count FROM users");
    if ($result) {
        $row = $result->fetch_assoc();
        echo "✓ Users table found with " . $row['count'] . " users\n";
        
        // Show sample users (without passwords)
        $result = $conn->query("SELECT id, first_name, last_name, email, user_type, status FROM users LIMIT 5");
        if ($result && $result->num_rows > 0) {
            echo "\nSample users:\n";
            while ($user = $result->fetch_assoc()) {
                echo "- ID: {$user['id']}, Name: {$user['first_name']} {$user['last_name']}, Email: {$user['email']}, Type: {$user['user_type']}, Status: {$user['status']}\n";
            }
        }
    } else {
        echo "✗ Users table query failed\n";
    }
    
    $conn->close();
} else {
    echo "✗ Database connection: FAILED\n";
    echo "Check your database credentials in config/database.php\n";
}

echo "\n=== Test Login Function ===\n";
// Test login with a sample user if exists
$testResult = loginUser("test@example.com", "password");
echo "Login test result: " . json_encode($testResult) . "\n";

?>
