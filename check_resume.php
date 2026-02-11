<?php
require_once 'config/database.php';

$conn = getDatabaseConnection();
$result = $conn->query('DESCRIBE tour_guides');

echo "Checking for resume field in tour_guides table:\n";
while ($row = $result->fetch_assoc()) {
    if (strpos($row['Field'], 'resume') !== false) {
        echo "Found resume field: " . $row['Field'] . "\n";
    }
}

$conn->close();
?>
