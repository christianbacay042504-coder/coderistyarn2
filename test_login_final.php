<?php
require_once 'config/database.php';
require_once 'config/auth.php';

$conn = getDatabaseConnection();
if (!$conn) {
    die("Database connection failed");
}

echo "=== Tour Guide Login Test ===\n";

// Test tour guide login
$result = loginUser('juan.santos@tourguide.com', 'password123');

echo "Login result:\n";
print_r($result);

echo "\n=== Tour Guide Accounts in Database ===\n";
$stmt = $conn->prepare("SELECT id, first_name, last_name, email, user_type, status FROM users WHERE user_type = 'tour_guide'");
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    echo "ID: {$row['id']}, Name: {$row['first_name']} {$row['last_name']}, Email: {$row['email']}, Type: {$row['user_type']}, Status: {$row['status']}\n";
}

closeDatabaseConnection($conn);
?>
