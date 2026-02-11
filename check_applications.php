<?php
require_once 'config/database.php';

$conn = getDatabaseConnection();
$result = $conn->query('SELECT id, name, resume FROM tour_guides WHERE resume IS NOT NULL ORDER BY id DESC LIMIT 3');
echo 'Recent tour guide applications with resumes:' . PHP_EOL;
while ($row = $result->fetch_assoc()) {
    echo 'ID: ' . $row['id'] . ', Name: ' . $row['name'] . ', Resume: ' . $row['resume'] . PHP_EOL;
}
$conn->close();
?>
