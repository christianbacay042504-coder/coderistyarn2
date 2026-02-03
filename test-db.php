<?php
require_once 'config/database.php';

echo "Testing database connection...\n";

$conn = getDatabaseConnection();
if ($conn) {
    echo "Database connection: SUCCESS\n";
    
    // Check tour guides table
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM tour_guides WHERE status = 'active'");
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    echo "Active tour guides count: " . $row['count'] . "\n";
    
    // Get first guide details
    $stmt = $conn->prepare("SELECT * FROM tour_guides WHERE status = 'active' LIMIT 1");
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $guide = $result->fetch_assoc();
        echo "First guide: " . $guide['name'] . "\n";
        echo "Category: " . $guide['category'] . "\n";
        echo "Rating: " . $guide['rating'] . "\n";
    }
    
    closeDatabaseConnection($conn);
} else {
    echo "Database connection: FAILED\n";
}
?>
