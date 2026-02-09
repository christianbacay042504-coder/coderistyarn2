<?php
require_once 'config/database.php';

$conn = getDatabaseConnection();

echo "=== Create Fresh Password Hash ===\n";

// Create a fresh hash for password123
$newPassword = 'password123';
$newHash = password_hash($newPassword, PASSWORD_DEFAULT);

echo "New hash for 'password123': " . $newHash . "\n";
echo "Hash length: " . strlen($newHash) . "\n";

// Test the new hash
if (password_verify($newPassword, $newHash)) {
    echo "✅ New hash verification: SUCCESS\n";
} else {
    echo "❌ New hash verification: FAILED\n";
}

// Update both tour guide accounts with fresh hash
$stmt1 = $conn->prepare("UPDATE users SET password = ? WHERE email = 'juan.santos@tourguide.com'");
$stmt1->bind_param("s", $newHash);
if ($stmt1->execute()) {
    echo "✅ Updated Juan Santos with fresh hash\n";
} else {
    echo "❌ Failed to update Juan Santos: " . $stmt1->error . "\n";
}
$stmt1->close();

$stmt2 = $conn->prepare("UPDATE users SET password = ? WHERE email = 'maria.reyes@tourguide.com'");
$stmt2->bind_param("s", $newHash);
if ($stmt2->execute()) {
    echo "✅ Updated Maria Reyes with fresh hash\n";
} else {
    echo "❌ Failed to update Maria Reyes: " . $stmt2->error . "\n";
}
$stmt2->close();

closeDatabaseConnection($conn);
?>
