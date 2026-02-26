<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "PHP is working!";
echo "<br>";
echo "Current time: " . date('Y-m-d H:i:s');

// Test database connection
require_once '../config/database.php';
$conn = getDatabaseConnection();
if ($conn) {
    echo "<br>Database: Connected";
    closeDatabaseConnection($conn);
} else {
    echo "<br>Database: Failed";
}
?>
