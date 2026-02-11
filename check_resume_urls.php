<?php
require_once 'config/database.php';

$conn = getDatabaseConnection();
$result = $conn->query('SELECT resume FROM tour_guides WHERE resume IS NOT NULL AND resume != "" LIMIT 3');
echo 'Resume URLs in database:' . PHP_EOL;
while ($row = $result->fetch_assoc()) {
    echo '- ' . $row['resume'] . PHP_EOL;
}
$conn->close();
?>
