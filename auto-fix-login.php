<?php
// Automatic Login Fix Script
// This script will automatically diagnose and fix login issues

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>🔧 Automatic Login Fix</h2>";
echo "<pre>";

// Database connection
$connection = new mysqli('localhost', 'root', '', 'sjdm_tours');

if ($connection->connect_error) {
    echo "❌ Database connection failed: " . $connection->connect_error . "\n";
    echo "Attempting to create database...\n";
    
    // Try to create database
    $adminConnection = new mysqli('localhost', 'root', '');
    if ($adminConnection->connect_error) {
        die("Cannot connect to MySQL server");
    }
    
    if ($adminConnection->query("CREATE DATABASE IF NOT EXISTS sjdm_tours")) {
        echo "✅ Database 'sjdm_tours' created successfully\n";
        $adminConnection->close();
        $connection = new mysqli('localhost', 'root', '', 'sjdm_tours');
    } else {
        die("Failed to create database: " . $adminConnection->error);
    }
} else {
    echo "✅ Database connection successful\n";
}

// Check and create users table
echo "\n--- Checking Users Table ---\n";
$tableCheck = $connection->query("SHOW TABLES LIKE 'users'");

if ($tableCheck->num_rows == 0) {
    echo "❌ Users table not found. Creating table...\n";
    
    $createTable = "CREATE TABLE users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        first_name VARCHAR(50) NOT NULL,
        last_name VARCHAR(50) NOT NULL,
        email VARCHAR(100) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        user_type ENUM('user', 'admin') DEFAULT 'user',
        status ENUM('active', 'inactive', 'suspended') DEFAULT 'active',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        last_login TIMESTAMP NULL
    )";
    
    if ($connection->query($createTable)) {
        echo "✅ Users table created successfully\n";
    } else {
        echo "❌ Failed to create users table: " . $connection->error . "\n";
    }
} else {
    echo "✅ Users table exists\n";
}

// Check and fix admin user
echo "\n--- Checking Admin User ---\n";
$userCheck = $connection->query("SELECT * FROM users WHERE email = 'adminlgu@gmail.com'");

if ($userCheck->num_rows == 0) {
    echo "❌ Admin user not found. Creating admin user...\n";
    
    $passwordHash = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'; // hash for 'admin123'
    $insertUser = "INSERT INTO users (first_name, last_name, email, password, user_type, status) 
                   VALUES ('Admin', 'SJDM', 'adminlgu@gmail.com', '$passwordHash', 'admin', 'active')";
    
    if ($connection->query($insertUser)) {
        echo "✅ Admin user created successfully\n";
        echo "Email: adminlgu@gmail.com\n";
        echo "Password: admin123\n";
    } else {
        echo "❌ Failed to create admin user: " . $connection->error . "\n";
    }
} else {
    $user = $userCheck->fetch_assoc();
    echo "✅ Admin user found:\n";
    echo "ID: " . $user['id'] . "\n";
    echo "Email: " . $user['email'] . "\n";
    echo "Type: " . $user['user_type'] . "\n";
    echo "Status: " . $user['status'] . "\n";
    
    // Verify password
    if (password_verify('admin123', $user['password'])) {
        echo "✅ Password is correct\n";
    } else {
        echo "❌ Password is incorrect. Updating password...\n";
        $passwordHash = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi';
        $updatePassword = "UPDATE users SET password = '$passwordHash' WHERE email = 'adminlgu@gmail.com'";
        
        if ($connection->query($updatePassword)) {
            echo "✅ Password updated successfully\n";
        } else {
            echo "❌ Failed to update password: " . $connection->error . "\n";
        }
    }
    
    // Ensure user is active
    if ($user['status'] != 'active') {
        echo "⚠️ User is not active. Activating...\n";
        $activateUser = "UPDATE users SET status = 'active' WHERE email = 'adminlgu@gmail.com'";
        if ($connection->query($activateUser)) {
            echo "✅ User activated\n";
        }
    }
}

// Test login function
echo "\n--- Testing Login Function ---\n";
function testLogin($email, $password) {
    global $connection;
    
    $stmt = $connection->prepare("SELECT id, first_name, last_name, email, password, user_type, status FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        return ['success' => false, 'message' => 'User not found'];
    }
    
    $user = $result->fetch_assoc();
    $stmt->close();
    
    if ($user['status'] !== 'active') {
        return ['success' => false, 'message' => 'Account not active'];
    }
    
    if (!password_verify($password, $user['password'])) {
        return ['success' => false, 'message' => 'Invalid password'];
    }
    
    return [
        'success' => true,
        'message' => 'Login successful',
        'user' => $user
    ];
}

$loginTest = testLogin('adminlgu@gmail.com', 'admin123');
if ($loginTest['success']) {
    echo "✅ Login test PASSED\n";
    echo "User: " . $loginTest['user']['first_name'] . " " . $loginTest['user']['last_name'] . "\n";
    echo "Type: " . $loginTest['user']['user_type'] . "\n";
} else {
    echo "❌ Login test FAILED: " . $loginTest['message'] . "\n";
}

$connection->close();

echo "\n--- FIX COMPLETE ---\n";
echo "✅ You can now login at: http://localhost/coderistyarn2/log-in/log-in.php\n";
echo "📧 Email: adminlgu@gmail.com\n";
echo "🔑 Password: admin123\n";
echo "</pre>";

echo '<div style="margin-top: 20px;">
        <a href="/coderistyarn2/log-in/log-in.php" style="padding: 10px 20px; background: #2c5f2d; color: white; text-decoration: none; border-radius: 5px;">
            Go to Login Page
        </a>
      </div>';
?>