<?php
// Create Admin User Script
require_once __DIR__ . '/config/database.php';

echo "<h2>Creating Admin User</h2>";

try {
    $conn = getDatabaseConnection();
    if (!$conn) {
        throw new Exception("Database connection failed");
    }
    
    echo "<p style='color: green;'>✅ Database connected successfully</p>";
    
    // Check if admin user already exists
    $checkStmt = $conn->prepare("SELECT id FROM users WHERE email = 'adminlgu@gmail.com'");
    $checkStmt->execute();
    $result = $checkStmt->get_result();
    
    if ($result->num_rows > 0) {
        echo "<p style='color: orange;'>⚠️ Admin user already exists!</p>";
        $checkStmt->close();
        closeDatabaseConnection($conn);
        exit;
    }
    
    // Create admin user
    $firstName = 'Admin';
    $lastName = 'SJDM';
    $email = 'adminlgu@gmail.com';
    $password = 'admin123';
    $userType = 'admin';
    $status = 'active';
    
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    
    $stmt = $conn->prepare("INSERT INTO users (first_name, last_name, email, password, user_type, status) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $firstName, $lastName, $email, $hashedPassword, $userType, $status);
    
    if ($stmt->execute()) {
        echo "<p style='color: green;'>✅ Admin user created successfully!</p>";
        echo "<ul>";
        echo "<li>Email: adminlgu@gmail.com</li>";
        echo "<li>Password: admin123</li>";
        echo "<li>User Type: admin</li>";
        echo "</ul>";
    } else {
        throw new Exception("Failed to create admin user: " . $stmt->error);
    }
    
    $stmt->close();
    closeDatabaseConnection($conn);
    
    echo "<hr>";
    echo "<p><strong>Next Steps:</strong></p>";
    echo "<ul>";
    echo "<li><a href='/coderistyarn2/log-in/log-in.php'>Go to Login Page</a></li>";
    echo "<li><a href='/coderistyarn2/admin/dashboard.php'>Go to Admin Dashboard</a></li>";
    echo "</ul>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
}
?>
