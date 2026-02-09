<?php
require_once 'config/database.php';

$conn = getDatabaseConnection();

echo "=== Password Verification Test ===\n";

// Get the stored hash for Juan Santos
$stmt = $conn->prepare("SELECT password FROM users WHERE email = 'juan.santos@tourguide.com'");
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if ($user) {
    echo "Stored hash: " . $user['password'] . "\n";
    
    // Test password verification
    $testPassword = 'password123';
    if (password_verify($testPassword, $user['password'])) {
        echo "✅ Password verification: SUCCESS\n";
    } else {
        echo "❌ Password verification: FAILED\n";
        
        // Test with our known hash
        $knownHash = '$2y$10$3DQxAUR/H3QdYWRerZKzMuqCvXoRwVpQDFuiE8SU6BvydNnM5CiBS';
        if (password_verify($testPassword, $knownHash)) {
            echo "✅ Known hash verification: SUCCESS\n";
        } else {
            echo "❌ Known hash verification: FAILED\n";
        }
    }
} else {
    echo "User not found\n";
}

closeDatabaseConnection($conn);
?>
