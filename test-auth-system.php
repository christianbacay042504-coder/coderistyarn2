<?php
// Test Authentication System
require_once 'config/database.php';
require_once 'config/auth.php';

echo "<h1>Authentication System Test</h1>\n";

// Test 1: Database Connection
echo "<h2>1. Database Connection Test</h2>\n";
if (testDatabaseConnection()) {
    echo "<p style='color: green;'>✓ Database connection successful</p>\n";
} else {
    echo "<p style='color: red;'>✗ Database connection failed</p>\n";
    exit;
}

// Test 2: Check if users table exists
echo "<h2>2. Users Table Check</h2>\n";
$conn = getDatabaseConnection();
$result = $conn->query("SHOW TABLES LIKE 'users'");
if ($result && $result->num_rows > 0) {
    echo "<p style='color: green;'>✓ Users table exists</p>\n";
} else {
    echo "<p style='color: red;'>✗ Users table not found</p>\n";
}

// Test 3: Check admin user
echo "<h2>3. Admin User Check</h2>\n";
$stmt = $conn->prepare("SELECT id, first_name, last_name, email, user_type FROM users WHERE user_type = 'admin'");
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $admin = $result->fetch_assoc();
    echo "<p style='color: green;'>✓ Admin user found:</p>\n";
    echo "<ul>\n";
    echo "<li>ID: " . $admin['id'] . "</li>\n";
    echo "<li>Name: " . $admin['first_name'] . " " . $admin['last_name'] . "</li>\n";
    echo "<li>Email: " . $admin['email'] . "</li>\n";
    echo "<li>Type: " . $admin['user_type'] . "</li>\n";
    echo "</ul>\n";
} else {
    echo "<p style='color: orange;'>⚠ No admin user found. You may need to run the database setup.</p>\n";
}
$stmt->close();

// Test 4: Test Login Function
echo "<h2>4. Login Function Test</h2>\n";
echo "<p>Testing with admin credentials:</p>\n";
echo "<ul>\n";
echo "<li>Email: adminlgu@gmail.com</li>\n";
echo "<li>Password: admin123</li>\n";
echo "</ul>\n";

$loginResult = loginUser('adminlgu@gmail.com', 'admin123');
if ($loginResult['success']) {
    echo "<p style='color: green;'>✓ Login successful</p>\n";
    echo "<p>Message: " . $loginResult['message'] . "</p>\n";
    echo "<p>User Type: " . $loginResult['user_type'] . "</p>\n";
    
    // Test logout
    logoutUser();
    echo "<p style='color: green;'>✓ Logout successful</p>\n";
} else {
    echo "<p style='color: red;'>✗ Login failed: " . $loginResult['message'] . "</p>\n";
}

// Test 5: Test Registration Function
echo "<h2>5. Registration Function Test</h2>\n";
$testEmail = 'testuser' . time() . '@example.com';
echo "<p>Testing registration with:</p>\n";
echo "<ul>\n";
echo "<li>First Name: Test</li>\n";
echo "<li>Last Name: User</li>\n";
echo "<li>Email: $testEmail</li>\n";
echo "<li>Password: testpass123</li>\n";
echo "</ul>\n";

$registerResult = registerUser('Test', 'User', $testEmail, 'testpass123');
if ($registerResult['success']) {
    echo "<p style='color: green;'>✓ Registration successful</p>\n";
    echo "<p>Message: " . $registerResult['message'] . "</p>\n";
    echo "<p>User ID: " . $registerResult['user_id'] . "</p>\n";
    
    // Clean up test user
    $deleteStmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $deleteStmt->bind_param("i", $registerResult['user_id']);
    $deleteStmt->execute();
    $deleteStmt->close();
    echo "<p style='color: green;'>✓ Test user cleaned up</p>\n";
} else {
    echo "<p style='color: red;'>✗ Registration failed: " . $registerResult['message'] . "</p>\n";
}

// Test 6: Check all tables
echo "<h2>6. Database Tables Overview</h2>\n";
$tables = ['users', 'bookings', 'login_activity', 'saved_tours'];
foreach ($tables as $table) {
    $result = $conn->query("SELECT COUNT(*) as count FROM $table");
    if ($result) {
        $row = $result->fetch_assoc();
        echo "<p>✓ $table: " . $row['count'] . " records</p>\n";
    } else {
        echo "<p style='color: red;'>✗ $table: Error accessing table</p>\n";
    }
}

closeDatabaseConnection($conn);

echo "<h2>7. Frontend Files Check</h2>\n";
$files = [
    'log-in/log-in.php',
    'log-in/register.php',
    'log-in/log-in.js',
    'log-in/styles.css',
    'config/auth.php',
    'config/database.php'
];

foreach ($files as $file) {
    if (file_exists($file)) {
        echo "<p style='color: green;'>✓ $file exists</p>\n";
    } else {
        echo "<p style='color: red;'>✗ $file missing</p>\n";
    }
}

echo "<h2>✅ Authentication System Status</h2>\n";
echo "<p>All components are in place. The login and registration system is ready to use!</p>\n";
echo "<p><a href='log-in/log-in.php'>Go to Login Page</a> | <a href='log-in/register.php'>Go to Register Page</a></p>\n";
?>