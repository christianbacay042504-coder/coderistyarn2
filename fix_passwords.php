<?php
require_once 'config/database.php';

$conn = getDatabaseConnection();

echo "=== Fix Tour Guide Passwords ===\n";

// Correct password hash for 'password123'
$correctHash = '$2y$10$3DQxAUR/H3QdYWRerZKzMuqCvXoRwVpQDFuiE8SU6BvydNnM5CiBS';

// Update Juan Santos password
$stmt1 = $conn->prepare("UPDATE users SET password = ? WHERE email = 'juan.santos@tourguide.com'");
$stmt1->bind_param("s", $correctHash);
if ($stmt1->execute()) {
    echo "✅ Updated Juan Santos password\n";
} else {
    echo "❌ Failed to update Juan Santos password: " . $stmt1->error . "\n";
}
$stmt1->close();

// Update Maria Reyes password
$stmt2 = $conn->prepare("UPDATE users SET password = ? WHERE email = 'maria.reyes@tourguide.com'");
$stmt2->bind_param("s", $correctHash);
if ($stmt2->execute()) {
    echo "✅ Updated Maria Reyes password\n";
} else {
    echo "❌ Failed to update Maria Reyes password: " . $stmt2->error . "\n";
}
$stmt2->close();

// Test login after fix
echo "\n=== Testing Login After Fix ===\n";
require_once 'config/auth.php';
$result = loginUser('juan.santos@tourguide.com', 'password123');
echo "Login result: " . ($result['success'] ? 'SUCCESS' : 'FAILED') . "\n";
echo "Message: " . $result['message'] . "\n";

closeDatabaseConnection($conn);
?>
