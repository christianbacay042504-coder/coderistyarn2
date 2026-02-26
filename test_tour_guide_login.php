<?php
// Test script to verify tour guide accounts
require_once 'config/database.php';

$conn = getDatabaseConnection();
if (!$conn) {
    die("Database connection failed");
}

echo "<h2>Tour Guide Accounts Test</h2>";

// Check tour guide accounts in users table
echo "<h3>Tour Guide Accounts in users table:</h3>";
$stmt = $conn->prepare("SELECT id, first_name, last_name, email, user_type, status FROM users WHERE user_type = 'tour_guide'");
$stmt->execute();
$result = $stmt->get_result();

echo "<table border='1'>";
echo "<tr><th>ID</th><th>Name</th><th>Email</th><th>Type</th><th>Status</th></tr>";

while ($row = $result->fetch_assoc()) {
    echo "<tr>";
    echo "<td>{$row['id']}</td>";
    echo "<td>{$row['first_name']} {$row['last_name']}</td>";
    echo "<td>{$row['email']}</td>";
    echo "<td>{$row['user_type']}</td>";
    echo "<td>{$row['status']}</td>";
    echo "</tr>";
}
echo "</table>";

$stmt->close();

// Check tour guide profiles table
echo "<h3>Tour Guide Profiles:</h3>";
$stmt2 = $conn->prepare("SELECT tgp.*, u.first_name, u.last_name, u.email FROM tour_guide_profiles tgp JOIN users u ON tgp.user_id = u.id");
$stmt2->execute();
$result2 = $stmt2->get_result();

echo "<table border='1'>";
echo "<tr><th>Profile ID</th><th>User ID</th><th>Name</th><th>Email</th><th>License</th><th>Specialization</th></tr>";

while ($row = $result2->fetch_assoc()) {
    echo "<tr>";
    echo "<td>{$row['id']}</td>";
    echo "<td>{$row['user_id']}</td>";
    echo "<td>{$row['first_name']} {$row['last_name']}</td>";
    echo "<td>{$row['email']}</td>";
    echo "<td>" . (isset($row['person_name']) ? $row['person_name'] : (isset($row['license_number']) ? $row['license_number'] : 'N/A')) . "</td>";
    echo "<td>{$row['specialization']}</td>";
    echo "</tr>";
}
echo "</table>";

$stmt2->close();
closeDatabaseConnection($conn);

// Test login function
echo "<h3>Testing Login Function:</h3>";
$testResult = loginUser('juan.santos@tourguide.com', 'password123');
echo "<pre>";
print_r($testResult);
echo "</pre>";
?>
