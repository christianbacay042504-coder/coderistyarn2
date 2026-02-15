<?php
// Start session and destroy it completely
session_start();

// Unset all session variables
$_SESSION = array();

// Destroy the session cookie
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Destroy the session
session_destroy();

// Start a new clean session for logout messages
session_start();

// Set logout message
$_SESSION['logout_message'] = 'You have been successfully signed out.';
$_SESSION['logout_status'] = 'success';

// Redirect to login page
header('Location: ../log-in.php');
exit();
?>