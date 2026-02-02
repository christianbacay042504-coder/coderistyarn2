<?php
// Test Database Connection and Admin User
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/config/database.php';

echo "<h2>Database Connection Test</h2>";

// Test connection
$conn = getDatabaseConnection();

if (!$conn) {
    echo "<p style='color: red;'>❌ Database connection FAILED!</p>";
    echo "<p>Please check your database credentials in config/database.php</p>";
    exit;
}

echo "<p style='color: green;'>✅ Database connection successful!</p>";

// Check if tables exist
$tables = ['users', 'bookings', 'login_activity', 'saved_tours'];
echo "<h3>Checking Tables:</h3>";

foreach ($tables as $table) {
    $result = $conn->query("SHOW TABLES LIKE '$table'");
    if ($result && $result->num_rows > 0) {
        echo "<p style='color: green;'>✅ Table '$table' exists</p>";
    } else {
        echo "<p style='color: red;'>❌ Table '$table' NOT found</p>";
    }
}

// Check admin user
echo "<h3>Checking Admin User:</h3>";
$result = $conn->query("SELECT id, email, user_type, status FROM users WHERE email = 'adminlgu@gmail.com'");

if ($result && $result->num_rows > 0) {
    $admin = $result->fetch_assoc();
    echo "<p style='color: green;'>✅ Admin user found!</p>";
    echo "<ul>";
    echo "<li>ID: " . $admin['id'] . "</li>";
    echo "<li>Email: " . $admin['email'] . "</li>";
    echo "<li>Type: " . $admin['user_type'] . "</li>";
    echo "<li>Status: " . $admin['status'] . "</li>";
    echo "</ul>";
    
    // Test password verification
    $passwordQuery = $conn->query("SELECT password FROM users WHERE email = 'adminlgu@gmail.com'");
    $passRow = $passwordQuery->fetch_assoc();
    $testPassword = 'admin123';
    
    if (password_verify($testPassword, $passRow['password'])) {
        echo "<p style='color: green;'>✅ Password verification successful! Password 'admin123' is correct.</p>";
    } else {
        echo "<p style='color: orange;'>⚠️ Password verification failed. Need to update password hash.</p>";
        echo "<p>Run this query in phpMyAdmin:</p>";
        $newHash = password_hash($testPassword, PASSWORD_DEFAULT);
        echo "<pre>UPDATE users SET password = '$newHash' WHERE email = 'adminlgu@gmail.com';</pre>";
    }
} else {
    echo "<p style='color: red;'>❌ Admin user NOT found!</p>";
    echo "<p>Run this query in phpMyAdmin:</p>";
    $hash = password_hash('admin123', PASSWORD_DEFAULT);
    echo "<pre>INSERT INTO users (first_name, last_name, email, password, user_type, status) 
VALUES ('Admin', 'SJDM', 'adminlgu@gmail.com', '$hash', 'admin', 'active');</pre>";
}

// Count all users
$result = $conn->query("SELECT COUNT(*) as total FROM users");
$count = $result->fetch_assoc()['total'];
echo "<h3>Total Users: $count</h3>";

closeDatabaseConnection($conn);

echo "<hr>";
echo "<p><strong>Next Steps:</strong></p>";
echo "<ul>";
echo "<li><a href='/coderistyarn2/log-in/log-in.php'>Go to Login Page</a></li>";
echo "<li><a href='/coderistyarn2/admin/dashboard.php'>Go to Admin Dashboard</a></li>";
echo "</ul>";
?>
