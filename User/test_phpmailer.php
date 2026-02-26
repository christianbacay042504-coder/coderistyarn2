<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "Testing PHPMailer...<br>";

// Check if PHPMailer files exist
$phpmailerPath = '../PHPMailer-6.9.1/src/PHPMailer.php';
$exceptionPath = '../PHPMailer-6.9.1/src/Exception.php';
$smtpPath = '../PHPMailer-6.9.1/src/SMTP.php';

echo "PHPMailer.php exists: " . (file_exists($phpmailerPath) ? "YES" : "NO") . "<br>";
echo "Exception.php exists: " . (file_exists($exceptionPath) ? "YES" : "NO") . "<br>";
echo "SMTP.php exists: " . (file_exists($smtpPath) ? "YES" : "NO") . "<br>";

try {
    // Try to include PHPMailer
    if (file_exists($phpmailerPath)) {
        require_once $phpmailerPath;
        echo "PHPMailer.php loaded successfully<br>";
    }
    
    if (file_exists($exceptionPath)) {
        require_once $exceptionPath;
        echo "Exception.php loaded successfully<br>";
    }
    
    if (file_exists($smtpPath)) {
        require_once $smtpPath;
        echo "SMTP.php loaded successfully<br>";
    }
    
    // Check if class exists
    if (class_exists('PHPMailer\PHPMailer\PHPMailer')) {
        echo "PHPMailer class exists<br>";
    } else {
        echo "PHPMailer class does NOT exist<br>";
    }
    
} catch (Exception $e) {
    echo "ERROR loading PHPMailer: " . $e->getMessage() . "<br>";
}
?>
