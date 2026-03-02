<?php
require_once 'config/database.php';

$conn = getDatabaseConnection();
if ($conn) {
    echo "=== TOUR_GUIDES TABLE STRUCTURE ===\n";
    $result = $conn->query('DESCRIBE tour_guides');
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            echo $row['Field'] . ' - ' . $row['Type'] . PHP_EOL;
        }
    }
    
    echo "\n=== SAMPLE DATA ===\n";
    $result = $conn->query('SELECT * FROM tour_guides LIMIT 3');
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            echo "ID: " . $row['id'] . "\n";
            foreach ($row as $key => $value) {
                if ($key !== 'id') {
                    echo "  $key: $value\n";
                }
            }
            echo "---\n";
        }
    }
    
    $conn->close();
} else {
    echo "Database connection failed\n";
}
?>
