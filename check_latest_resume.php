<?php
require_once 'config/database.php';

$conn = getDatabaseConnection();
$result = $conn->query('SELECT resume FROM tour_guides WHERE resume IS NOT NULL ORDER BY id DESC LIMIT 1');
echo 'Latest resume URL in database:' . PHP_EOL;
if ($row = $result->fetch_assoc()) {
    echo '- ' . $row['resume'] . PHP_EOL;
} else {
    echo 'No resume found';
}
$conn->close();
?>
