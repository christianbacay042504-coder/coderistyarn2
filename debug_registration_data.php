<?php
require_once 'config/database.php';

$conn = getDatabaseConnection();

echo "Debug: Checking registration data and ENUM values\n\n";

// Check current ENUM values
echo "1. Current tour_guides.category ENUM:\n";
$result = $conn->query("SHOW COLUMNS FROM tour_guides LIKE 'category'");
if ($result) {
    $row = $result->fetch_assoc();
    echo "   " . $row['Type'] . "\n\n";
}

echo "2. Current registration_tour_guide.specialization ENUM:\n";
$result = $conn->query("SHOW COLUMNS FROM registration_tour_guide LIKE 'specialization'");
if ($result) {
    $row = $result->fetch_assoc();
    echo "   " . $row['Type'] . "\n\n";
}

// Check recent registration data
echo "3. Recent registration data:\n";
$result = $conn->query("SELECT id, specialization FROM registration_tour_guide ORDER BY id DESC LIMIT 5");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        echo "   ID: " . $row['id'] . " - specialization: '" . $row['specialization'] . "'\n";
    }
}

echo "\n4. Test insertion with current data:\n";
// Get the latest registration
$result = $conn->query("SELECT * FROM registration_tour_guide ORDER BY id DESC LIMIT 1");
if ($result) {
    $registration = $result->fetch_assoc();
    $name = $registration['first_name'] . ' ' . $registration['last_name'];
    $category = $registration['specialization'];
    
    echo "   Name: $name\n";
    echo "   Category (from specialization): '$category'\n";
    
    // Test if this category value is valid
    $testStmt = $conn->prepare("SELECT ? AS test_category");
    $testStmt->bind_param("s", $category);
    if ($testStmt->execute()) {
        echo "   ✅ Category value is valid for insertion\n";
    } else {
        echo "   ❌ Category value would cause error: " . $conn->error . "\n";
    }
}

$conn->close();
?>
