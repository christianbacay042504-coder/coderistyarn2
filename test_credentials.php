<?php
// Test login credentials
echo "Testing user credentials...\n\n";

// Database connection
require_once __DIR__ . '/config/auth.php';

$conn = getDatabaseConnection();
if (!$conn) {
    echo "Database connection failed\n";
    exit();
}

// Test user credentials
$testUsers = [
    ['email' => 'christianbacay042504@gmail.com', 'password' => 'user123'],
    ['email' => 'jeanmarcaguilar829@gmail.com', 'password' => 'admin123'],
    ['email' => 'angelhernandez@gmail.com', 'password' => 'user123']
];

foreach ($testUsers as $user) {
    echo "Testing: " . $user['email'] . "\n";
    
    $stmt = $conn->prepare("SELECT id, first_name, last_name, email, password, user_type, status FROM users WHERE email = ?");
    $stmt->bind_param('s', $user['email']);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $userData = $result->fetch_assoc();
        
        echo "  - User found: " . $userData['first_name'] . " " . $userData['last_name'] . "\n";
        echo "  - User type: " . $userData['user_type'] . "\n";
        echo "  - Status: " . $userData['status'] . "\n";
        
        if (password_verify($user['password'], $userData['password'])) {
            echo "  - ✓ Password correct\n";
        } else {
            echo "  - ✗ Password incorrect\n";
        }
    } else {
        echo "  - ✗ User not found\n";
    }
    
    echo "\n";
    $stmt->close();
}

closeDatabaseConnection($conn);
?>
