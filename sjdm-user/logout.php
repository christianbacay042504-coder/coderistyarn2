<?php
require_once __DIR__ . '/../config/auth.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Validate if user is actually logged in before logout
if (!isLoggedIn()) {
    $_SESSION['logout_message'] = 'You were not logged in.';
    $_SESSION['logout_status'] = 'info';
    header('Location: ../log-in.php');
    exit();
}

// Get user info for logging before logout
$currentUser = getCurrentUser();
$userName = $currentUser ? $currentUser['first_name'] . ' ' . $currentUser['last_name'] : 'Unknown User';

// Logout the user
$logoutResult = logoutUser();

// Set success message for the login page
if ($logoutResult) {
    $_SESSION['logout_message'] = "Goodbye, $userName! You have been successfully signed out.";
    $_SESSION['logout_status'] = 'success';
} else {
    $_SESSION['logout_message'] = 'There was an issue signing out. Please try again.';
    $_SESSION['logout_status'] = 'warning';
}

// Redirect to login page
header('Location: ../log-in.php');
exit();
?>