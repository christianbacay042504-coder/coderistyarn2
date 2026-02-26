<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include database connection and authentication
require_once 'config/database.php';
require_once 'config/auth.php';

echo "Testing tourist spots page...\n";

// Test database connection
$conn = getDatabaseConnection();
if (!$conn) {
    echo "Database connection failed\n";
    exit;
}

echo "Database connected successfully\n";

// Test the main query from the page
$query = "SELECT ts.*, 
             GROUP_CONCAT(DISTINCT CONCAT(tg.id, ':', tg.name, ':', tg.specialty, ':', tg.rating, ':', tg.verified) ORDER BY tg.rating DESC SEPARATOR '|') as guides_info
             FROM tourist_spots ts 
             LEFT JOIN guide_destinations gd ON ts.id = gd.destination_id 
             LEFT JOIN tour_guides tg ON gd.guide_id = tg.id AND tg.status = 'active'
             WHERE ts.status = 'active' 
             GROUP BY ts.id 
             ORDER BY ts.name";

echo "Executing query...\n";
$result = $conn->query($query);

if (!$result) {
    echo "Query failed: " . $conn->error . "\n";
    closeDatabaseConnection($conn);
    exit;
}

if ($result->num_rows > 0) {
    echo "Found " . $result->num_rows . " tourist spots\n";
    while ($spot = $result->fetch_assoc()) {
        echo "- " . $spot['name'] . " (Category: " . $spot['category'] . ")\n";
    }
} else {
    echo "No tourist spots found\n";
}

closeDatabaseConnection($conn);
echo "Test completed successfully\n";
?>
