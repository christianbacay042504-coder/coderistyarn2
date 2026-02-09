<?php
// Simple database test
require_once 'config/database.php';

$conn = getDatabaseConnection();
if (!$conn) {
    die("Database connection failed");
}

echo "<h2>Database Connection Test</h2>";
echo "Connection status: " . ($conn ? "SUCCESS" : "FAILED") . "<br>";

// Test basic query
$result = $conn->query("SELECT COUNT(*) as total FROM users");
if ($result) {
    $row = $result->fetch_assoc();
    echo "Total users in database: " . $row['total'] . "<br>";
} else {
    echo "Query failed: " . $conn->error . "<br>";
}

// Test tour guide count specifically
$result2 = $conn->query("SELECT COUNT(*) as total FROM users WHERE user_type = 'tour_guide'");
if ($result2) {
    $row2 = $result2->fetch_assoc();
    echo "Total tour guides in database: " . $row2['total'] . "<br>";
} else {
    echo "Tour guide query failed: " . $conn->error . "<br>";
}

closeDatabaseConnection($conn);
?>
