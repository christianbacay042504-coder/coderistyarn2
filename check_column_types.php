<?php
require_once 'config/database.php';

$conn = getDatabaseConnection();

echo "Checking column types...\n\n";

// Check registration_tour_guide.specialization
$result = $conn->query('DESCRIBE registration_tour_guide');
if ($result) {
    while ($row = $result->fetch_assoc()) {
        if ($row['Field'] === 'specialization') {
            echo "registration_tour_guide.specialization: " . $row['Type'] . "\n";
        }
    }
}

// Check tour_guides.category  
$result = $conn->query('DESCRIBE tour_guides');
if ($result) {
    while ($row = $result->fetch_assoc()) {
        if ($row['Field'] === 'category') {
            echo "tour_guides.category: " . $row['Type'] . "\n";
        }
    }
}

// Check sample data from registration_tour_guide
echo "\nSample specialization values:\n";
$result = $conn->query("SELECT id, specialization FROM registration_tour_guide ORDER BY id DESC LIMIT 5");
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "ID: " . $row['id'] . " - specialization: " . $row['specialization'] . "\n";
    }
}

$conn->close();
?>
