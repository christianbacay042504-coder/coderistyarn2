<?php
// Test the login AJAX endpoint
session_start();

// Simulate POST data
$_POST['action'] = 'login';
$_POST['email'] = 'christianbacay042504@gmail.com';
$_POST['password'] = 'user123';

// Set server request method
$_SERVER['REQUEST_METHOD'] = 'POST';

echo "Testing login AJAX endpoint...\n\n";

// Capture output
ob_start();

// Include only the PHP logic (not the HTML)
require_once __DIR__ . '/config/auth.php';
require_once __DIR__ . '/config/smtp.php';

// Read and execute the login logic
$login_code = file_get_contents(__DIR__ . '/log-in.php');
// Extract only the PHP part (before the HTML)
$php_part = substr($login_code, 0, strpos($login_code, '?>'));
$php_part .= '?>';

eval($php_part);

$output = ob_get_clean();

echo "Response: " . $output . "\n";
?>
