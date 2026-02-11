<?php
require_once 'config/database.php';

echo "Testing database connection...\n";
$conn = getDatabaseConnection();

if ($conn) {
    echo "✅ Database connected successfully\n";
    closeDatabaseConnection($conn);
} else {
    echo "❌ Database connection failed\n";
}

echo "Testing PHPMailer...\n";
require_once 'vendor/autoload.php';
if (class_exists('PHPMailer\PHPMailer\PHPMailer')) {
    echo "✅ PHPMailer loaded successfully\n";
} else {
    echo "❌ PHPMailer not found\n";
}
?>
