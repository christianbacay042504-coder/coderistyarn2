<?php
// Check session and authentication status
session_start();

echo "<h2>Session and Authentication Check</h2>";

echo "<h3>Session Status:</h3>";
echo "<p>Session ID: " . session_id() . "</p>";
echo "<p>Session Status: " . (session_status() === PHP_SESSION_ACTIVE ? 'Active' : 'Inactive') . "</p>";

echo "<h3>Session Data:</h3>";
echo "<pre>" . print_r($_SESSION, true) . "</pre>";

// Check if auth functions exist
if (file_exists('config/auth.php')) {
    require_once 'config/auth.php';
    
    echo "<h3>Authentication Functions:</h3>";
    echo "<p>isLoggedIn(): " . (isLoggedIn() ? 'Yes' : 'No') . "</p>";
    echo "<p>User ID in session: " . (isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 'Not set') . "</p>";
    
    if (isLoggedIn()) {
        echo "<p style='color: green;'>✅ User is logged in</p>";
        
        // Try to get user data
        if (file_exists('config/database.php')) {
            require_once 'config/database.php';
            $conn = getDatabaseConnection();
            
            if ($conn) {
                $stmt = $conn->prepare("SELECT first_name, last_name, email FROM users WHERE id = ?");
                $stmt->bind_param("i", $_SESSION['user_id']);
                $stmt->execute();
                $result = $stmt->get_result();
                
                if ($result->num_rows > 0) {
                    $user = $result->fetch_assoc();
                    echo "<p style='color: green;'>✅ User data found: " . htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) . "</p>";
                } else {
                    echo "<p style='color: red;'>❌ User data not found in database</p>";
                }
                
                closeDatabaseConnection($conn);
            } else {
                echo "<p style='color: red;'>❌ Database connection failed</p>";
            }
        }
    } else {
        echo "<p style='color: red;'>❌ User is NOT logged in</p>";
    }
} else {
    echo "<p style='color: red;'>❌ auth.php file not found</p>";
}

echo "<h3>Test Login:</h3>";
echo "<p>Testing if we can set a session manually...</p>";

// Test setting a session manually
$_SESSION['test_user_id'] = 1;
$_SESSION['test_login'] = true;

echo "<p>✅ Test session data set</p>";
echo "<p>New session data:</p>";
echo "<pre>" . print_r($_SESSION, true) . "</pre>";

echo "<hr>";
echo "<h3>Next Steps:</h3>";
echo "<ol>";
echo "<li>If user is NOT logged in, the booking will fail</li>";
echo "<li>If user IS logged in but user_id is not in database, booking will fail</li>";
echo "<li>Check if you're actually logged in when trying to book</li>";
echo "<li>Try logging in first, then attempt a booking</li>";
echo "</ol>";
?>
