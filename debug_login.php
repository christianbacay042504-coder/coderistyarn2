<?php
require_once __DIR__ . '/config/auth.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Login Debug Test</h2>";

// Test login with sample data
$email = "test@example.com"; // Change this to a real email
$password = "password";      // Change this to a real password

echo "<p>Testing login with email: $email</p>";

$result = loginUser($email, $password);

echo "<h3>Result:</h3>";
echo "<pre>";
print_r($result);
echo "</pre>";

// Check if database connection works
echo "<h3>Database Test:</h3>";
$conn = getDatabaseConnection();
if ($conn) {
    echo "<p style='color: green;'>✓ Database connection successful</p>";
    
    // Check user exists
    $stmt = $conn->prepare("SELECT id, email, password, user_type, status FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        echo "<p style='color: green;'>✓ User found</p>";
        echo "<pre>";
        echo "ID: " . $user['id'] . "\n";
        echo "Email: " . $user['email'] . "\n";
        echo "User Type: " . $user['user_type'] . "\n";
        echo "Status: " . $user['status'] . "\n";
        echo "Password Hash: " . substr($user['password'], 0, 20) . "...\n";
        echo "</pre>";
        
        // Test password verification
        if (password_verify($password, $user['password'])) {
            echo "<p style='color: green;'>✓ Password verification successful</p>";
        } else {
            echo "<p style='color: red;'>✗ Password verification failed</p>";
        }
    } else {
        echo "<p style='color: red;'>✗ User not found</p>";
    }
    
    $stmt->close();
    closeDatabaseConnection($conn);
} else {
    echo "<p style='color: red;'>✗ Database connection failed</p>";
}

echo "<h3>All Users in Database:</h3>";
$conn = getDatabaseConnection();
if ($conn) {
    $result = $conn->query("SELECT id, email, user_type, status FROM users");
    echo "<table border='1'>";
    echo "<tr><th>ID</th><th>Email</th><th>Type</th><th>Status</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['id'] . "</td>";
        echo "<td>" . $row['email'] . "</td>";
        echo "<td>" . $row['user_type'] . "</td>";
        echo "<td>" . $row['status'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    closeDatabaseConnection($conn);
}
?>
