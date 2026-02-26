<?php
require_once 'config/database.php';

$conn = getDatabaseConnection();

echo "Checking ALL category ENUM definitions...\n\n";

// Check tour_guides.category
echo "1. tour_guides.category:\n";
$result = $conn->query("SHOW COLUMNS FROM tour_guides LIKE 'category'");
if ($result) {
    $row = $result->fetch_assoc();
    echo "   " . $row['Type'] . "\n";
}

// Check tourist_spots.category  
echo "\n2. tourist_spots.category:\n";
$result = $conn->query("SHOW COLUMNS FROM tourist_spots LIKE 'category'");
if ($result) {
    $row = $result->fetch_assoc();
    echo "   " . $row['Type'] . "\n";
}

// Check registration_tour_guide.specialization
echo "\n3. registration_tour_guide.specialization:\n";
$result = $conn->query("SHOW COLUMNS FROM registration_tour_guide LIKE 'specialization'");
if ($result) {
    $row = $result->fetch_assoc();
    echo "   " . $row['Type'] . "\n";
}

// Test each possible value
echo "\n4. Testing problematic values:\n";
$testValues = ['cultural', 'adventure', 'photography', 'mountain', 'waterfall'];

foreach ($testValues as $value) {
    echo "\nTesting value: '$value'\n";
    
    // Test tour_guides insertion
    $stmt1 = $conn->prepare("INSERT INTO tour_guides (user_id, name, category) VALUES (1, 'Test', ?)");
    $stmt1->bind_param("s", $value);
    if ($stmt1->execute()) {
        echo "   ✅ tour_guides: SUCCESS\n";
        $conn->query("DELETE FROM tour_guides WHERE name = 'Test' AND user_id = 1");
    } else {
        echo "   ❌ tour_guides: " . $stmt1->error . "\n";
    }
    $stmt1->close();
    
    // Test tourist_spots insertion
    $stmt2 = $conn->prepare("INSERT INTO tourist_spots (name, category, location) VALUES ('Test', ?, 'Test Location')");
    $stmt2->bind_param("s", $value);
    if ($stmt2->execute()) {
        echo "   ✅ tourist_spots: SUCCESS\n";
        $conn->query("DELETE FROM tourist_spots WHERE name = 'Test'");
    } else {
        echo "   ❌ tourist_spots: " . $stmt2->error . "\n";
    }
    $stmt2->close();
}

$conn->close();
?>
