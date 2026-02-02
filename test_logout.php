<?php
// Test script to verify logout functionality
require_once 'config/auth.php';

echo "<h1>Logout Functionality Test</h1>";

// Check if user is currently logged in
if (isLoggedIn()) {
    $user = getCurrentUser();
    echo "<p>Current user: " . $user['first_name'] . " " . $user['last_name'] . " (" . $user['email'] . ")</p>";
    echo "<p>User type: " . $user['user_type'] . "</p>";
    echo "<p>Status: Active session</p>";
    
    // Demonstrate logout
    echo "<h2>Logging out...</h2>";
    logoutUser();
    echo "<p>Session destroyed. Redirecting to login in 3 seconds...</p>";
    
    // Redirect after a delay
    echo "<script>
        setTimeout(function() {
            window.location.href = '/coderistyarn2/log-in/log-in.php';
        }, 3000);
    </script>";
} else {
    echo "<p>No active session. Redirecting to login in 2 seconds...</p>";
    echo "<script>
        setTimeout(function() {
            window.location.href = '/coderistyarn2/log-in/log-in.php';
        }, 2000);
    </script>";
}
?>