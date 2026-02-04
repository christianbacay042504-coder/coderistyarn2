<?php
require_once __DIR__ . '/config/auth.php';

// Logout the user
logoutUser();

// Set success message for the login page
session_start();
$_SESSION['logout_message'] = 'Successfully signed out!';
$_SESSION['logout_status'] = 'success';

// Redirect to login page
header('Location: /coderistyarn2/log-in.php');
exit();
?>