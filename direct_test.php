<?php
require_once 'config/database.php';

$conn = getDatabaseConnection();

echo "=== Direct Login Test ===\n";

// Direct test of login logic
$email = 'juan.santos@tourguide.com';
$password = 'password123';

// Get user from database
$stmt = $conn->prepare("SELECT id, first_name, last_name, email, password, user_type, status, preferences_set FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    
    echo "Found user: {$user['first_name']} {$user['last_name']}\n";
    echo "Stored hash: " . $user['password'] . "\n";
    
    // Verify password
    if (password_verify($password, $user['password'])) {
        echo "✅ Password verification: SUCCESS\n";
        echo "✅ User type: {$user['user_type']}\n";
        echo "✅ Status: {$user['status']}\n";
    } else {
        echo "❌ Password verification: FAILED\n";
    }
} else {
    echo "❌ User not found\n";
}

$stmt->close();
closeDatabaseConnection($conn);
?>
