<?php
require_once 'config/database.php';

$conn = getDatabaseConnection();

echo "Checking current database ENUM definitions...\n\n";

// Check tour_guides.category
echo "tour_guides.category column definition:\n";
$result = $conn->query("SHOW COLUMNS FROM tour_guides LIKE 'category'");
if ($result) {
    $row = $result->fetch_assoc();
    echo "Type: " . $row['Type'] . "\n";
    echo "Null: " . $row['Null'] . "\n";
    echo "Default: " . $row['Default'] . "\n\n";
}

// Check registration_tour_guide.specialization
echo "registration_tour_guide.specialization column definition:\n";
$result = $conn->query("SHOW COLUMNS FROM registration_tour_guide LIKE 'specialization'");
if ($result) {
    $row = $result->fetch_assoc();
    echo "Type: " . $row['Type'] . "\n";
    echo "Null: " . $row['Null'] . "\n";
    echo "Default: " . $row['Default'] . "\n\n";
}

// Test a sample insertion
echo "Testing sample insertion...\n";
$testCategory = 'cultural';
$testName = 'Test Guide';
$testUserId = 999;

$stmt = $conn->prepare("INSERT INTO tour_guides (user_id, name, category) VALUES (?, ?, ?)");
$stmt->bind_param("iss", $testUserId, $testName, $testCategory);

if ($stmt->execute()) {
    echo "✅ Successfully inserted test record with category: '$testCategory'\n";
    // Clean up test record
    $conn->query("DELETE FROM tour_guides WHERE name = 'Test Guide' AND user_id = 999");
} else {
    echo "❌ Failed to insert: " . $stmt->error . "\n";
}

$conn->close();
?>
