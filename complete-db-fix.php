<?php
// Complete Database Fix Script
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "🔧 Running Complete Database Fix...\n\n";

// Connect to MySQL server (without specifying database first)
$adminConn = new mysqli('localhost', 'root', '');
if ($adminConn->connect_error) {
    die("❌ Cannot connect to MySQL server: " . $adminConn->connect_error . "\n");
}
echo "✅ Connected to MySQL server\n";

// Create database if it doesn't exist
if ($adminConn->query("CREATE DATABASE IF NOT EXISTS sjdm_tours")) {
    echo "✅ Database 'sjdm_tours' verified/created\n";
} else {
    echo "❌ Failed to create database: " . $adminConn->error . "\n";
}

$adminConn->close();

// Connect to sjdm_tours database
$conn = new mysqli('localhost', 'root', '', 'sjdm_tours');
if ($conn->connect_error) {
    die("❌ Cannot connect to sjdm_tours database: " . $conn->connect_error . "\n");
}
echo "✅ Connected to sjdm_tours database\n";

// Create all required tables
$tables = [
    'users' => "CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        first_name VARCHAR(50) NOT NULL,
        last_name VARCHAR(50) NOT NULL,
        email VARCHAR(100) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        user_type ENUM('user', 'admin') DEFAULT 'user',
        status ENUM('active', 'inactive', 'suspended') DEFAULT 'active',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        last_login TIMESTAMP NULL,
        INDEX idx_email (email),
        INDEX idx_user_type (user_type)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
    
    'bookings' => "CREATE TABLE IF NOT EXISTS bookings (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        tour_name VARCHAR(200) NOT NULL,
        booking_date DATE NOT NULL,
        number_of_people INT NOT NULL,
        total_amount DECIMAL(10, 2) NOT NULL,
        status ENUM('pending', 'confirmed', 'cancelled', 'completed') DEFAULT 'pending',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        INDEX idx_user_id (user_id),
        INDEX idx_status (status),
        INDEX idx_booking_date (booking_date)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
    
    'login_activity' => "CREATE TABLE IF NOT EXISTS login_activity (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        login_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        ip_address VARCHAR(45),
        user_agent VARCHAR(255),
        status ENUM('success', 'failed') DEFAULT 'success',
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        INDEX idx_user_id (user_id),
        INDEX idx_login_time (login_time)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
    
    'saved_tours' => "CREATE TABLE IF NOT EXISTS saved_tours (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        tour_name VARCHAR(200) NOT NULL,
        tour_description TEXT,
        saved_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        INDEX idx_user_id (user_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
];

foreach ($tables as $tableName => $createQuery) {
    if ($conn->query($createQuery)) {
        echo "✅ Table '$tableName' verified/created\n";
    } else {
        echo "❌ Failed to create table '$tableName': " . $conn->error . "\n";
    }
}

// Ensure admin user exists with correct password
$email = 'adminlgu@gmail.com';
$firstName = 'Admin';
$lastName = 'SJDM';
$userType = 'admin';
$status = 'active';

// Check if user exists
$stmt = $conn->prepare("SELECT id, password FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // User exists, update with correct password
    $user = $result->fetch_assoc();
    $newHash = password_hash('admin123', PASSWORD_DEFAULT);
    
    $updateStmt = $conn->prepare("UPDATE users SET first_name = ?, last_name = ?, password = ?, user_type = ?, status = ? WHERE email = ?");
    $updateStmt->bind_param("ssssss", $firstName, $lastName, $newHash, $userType, $status, $email);
    
    if ($updateStmt->execute()) {
        echo "✅ Admin user updated successfully\n";
    } else {
        echo "❌ Failed to update admin user: " . $updateStmt->error . "\n";
    }
    $updateStmt->close();
} else {
    // User doesn't exist, create new
    $newHash = password_hash('admin123', PASSWORD_DEFAULT);
    
    $insertStmt = $conn->prepare("INSERT INTO users (first_name, last_name, email, password, user_type, status) VALUES (?, ?, ?, ?, ?, ?)");
    $insertStmt->bind_param("ssssss", $firstName, $lastName, $email, $newHash, $userType, $status);
    
    if ($insertStmt->execute()) {
        echo "✅ Admin user created successfully\n";
    } else {
        echo "❌ Failed to create admin user: " . $insertStmt->error . "\n";
    }
    $insertStmt->close();
}
$stmt->close();

// Test login functionality
function testLogin($conn, $email, $password) {
    $stmt = $conn->prepare("SELECT id, first_name, last_name, email, password, user_type, status FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        $stmt->close();
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

$loginTest = testLogin($conn, 'adminlgu@gmail.com', 'admin123');
if ($loginTest['success']) {
    echo "✅ Login test PASSED\n";
    echo "User: " . $loginTest['user']['first_name'] . " " . $loginTest['user']['last_name'] . " (" . $loginTest['user']['user_type'] . ")\n";
} else {
    echo "❌ Login test FAILED: " . $loginTest['message'] . "\n";
}

$conn->close();

echo "\n🎉 COMPLETE DATABASE FIX FINISHED\n";
echo "✅ You can now login at: http://localhost/coderistyarn2/log-in/log-in.php\n";
echo "📧 Email: adminlgu@gmail.com\n";
echo "🔑 Password: admin123\n";
?>