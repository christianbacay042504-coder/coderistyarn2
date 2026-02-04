<?php
require_once __DIR__ . '/../config/auth.php';

// Check if user is logged in before logout
if (!isLoggedIn()) {
    // Set error message for non-logged in users
    session_start();
    $_SESSION['logout_message'] = 'No active session found.';
    $_SESSION['logout_status'] = 'error';
} else {
    // Logout the user
    logoutUser();

    // Set success message
    session_start();
    $_SESSION['logout_message'] = 'Successfully signed out!';
    $_SESSION['logout_status'] = 'success';
}

// Redirect to login page
header('Location: /coderistyarn2/log-in.php');
exit();
?>