<?php
// Quick database fix
$connection = new mysqli('localhost', 'root', '', 'sjdm_tours');

// Regenerate password hash
$newHash = password_hash('admin123', PASSWORD_DEFAULT);

// Update the admin user
$stmt = $connection->prepare("UPDATE users SET password = ? WHERE email = 'adminlgu@gmail.com'");
$stmt->bind_param("s", $newHash);

if ($stmt->execute()) {
    echo "✅ Password fixed successfully!\n";
    echo "New hash: " . $newHash . "\n";
} else {
    echo "❌ Failed to update password\n";
}

$stmt->close();
$connection->close();

// Test login
$connection2 = new mysqli('localhost', 'root', '', 'sjdm_tours');
$stmt2 = $connection2->prepare("SELECT password FROM users WHERE email = 'adminlgu@gmail.com'");
$stmt2->execute();
$result = $stmt2->get_result();
$user = $result->fetch_assoc();

if (password_verify('admin123', $user['password'])) {
    echo "✅ Login test: SUCCESS\n";
} else {
    echo "❌ Login test: FAILED\n";
}

$stmt2->close();
$connection2->close();
?>