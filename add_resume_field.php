<?php
require_once 'config/database.php';

$conn = getDatabaseConnection();
$conn->query('ALTER TABLE tour_guides ADD COLUMN resume VARCHAR(500) NULL');
echo 'Resume field added successfully';
$conn->close();
?>
