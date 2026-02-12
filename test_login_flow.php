<?php
// Test the login flow directly
session_start();

// Simulate login data
$_POST['action'] = 'login';
$_POST['email'] = 'christianbacay042504@gmail.com';
$_POST['password'] = 'user123';

echo "Testing login flow...\n\n";

// Include the login file
require_once __DIR__ . '/config/auth.php';
require_once __DIR__ . '/config/smtp.php';
require_once __DIR__ . '/log-in.php';

echo "Login file included\n";
?>
