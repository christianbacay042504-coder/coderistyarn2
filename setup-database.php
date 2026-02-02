<?php
// Database Setup Script
require_once 'config/database.php';

echo "<h1>SJDM Tours Database Setup</h1>\n";

// Check if database exists and create if not
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create database
$sql = "CREATE DATABASE IF NOT EXISTS " . DB_NAME;
if ($conn->query($sql) === TRUE) {
    echo "<p style='color: green;'>✓ Database '" . DB_NAME . "' created or already exists</p>\n";
} else {
    echo "<p style='color: red;'>✗ Error creating database: " . $conn->error . "</p>\n";
    exit;
}

$conn->close();

// Connect to the specific database
$conn = getDatabaseConnection();
if (!$conn) {
    die("Failed to connect to database");
}

// Create tables
echo "<h2>Creating Tables...</h2>\n";

// Users table
$usersTable = "CREATE TABLE IF NOT EXISTS users (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

if ($conn->query($usersTable) === TRUE) {
    echo "<p style='color: green;'>✓ Users table created</p>\n";
} else {
    echo "<p style='color: red;'>✗ Error creating users table: " . $conn->error . "</p>\n";
}

// Bookings table
$bookingsTable = "CREATE TABLE IF NOT EXISTS bookings (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

if ($conn->query($bookingsTable) === TRUE) {
    echo "<p style='color: green;'>✓ Bookings table created</p>\n";
} else {
    echo "<p style='color: red;'>✗ Error creating bookings table: " . $conn->error . "</p>\n";
}

// Login activity table
$loginActivityTable = "CREATE TABLE IF NOT EXISTS login_activity (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    login_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ip_address VARCHAR(45),
    user_agent VARCHAR(255),
    status ENUM('success', 'failed') DEFAULT 'success',
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_login_time (login_time)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

if ($conn->query($loginActivityTable) === TRUE) {
    echo "<p style='color: green;'>✓ Login activity table created</p>\n";
} else {
    echo "<p style='color: red;'>✗ Error creating login_activity table: " . $conn->error . "</p>\n";
}

// Saved tours table
$savedToursTable = "CREATE TABLE IF NOT EXISTS saved_tours (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    tour_name VARCHAR(200) NOT NULL,
    tour_description TEXT,
    saved_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

if ($conn->query($savedToursTable) === TRUE) {
    echo "<p style='color: green;'>✓ Saved tours table created</p>\n";
} else {
    echo "<p style='color: red;'>✗ Error creating saved_tours table: " . $conn->error . "</p>\n";
}

// Insert default admin user
echo "<h2>Creating Default Admin User...</h2>\n";
$checkAdmin = $conn->prepare("SELECT id FROM users WHERE email = 'adminlgu@gmail.com'");
$checkAdmin->execute();
$result = $checkAdmin->get_result();

if ($result->num_rows == 0) {
    $hashedPassword = password_hash('admin123', PASSWORD_DEFAULT);
    $insertAdmin = $conn->prepare("INSERT INTO users (first_name, last_name, email, password, user_type, status) VALUES (?, ?, ?, ?, ?, ?)");
    $firstName = 'Admin';
    $lastName = 'SJDM';
    $email = 'adminlgu@gmail.com';
    $userType = 'admin';
    $status = 'active';
    
    $insertAdmin->bind_param("ssssss", $firstName, $lastName, $email, $hashedPassword, $userType, $status);
    
    if ($insertAdmin->execute()) {
        echo "<p style='color: green;'>✓ Default admin user created</p>\n";
        echo "<p><strong>Login Credentials:</strong></p>\n";
        echo "<ul>\n";
        echo "<li>Email: adminlgu@gmail.com</li>\n";
        echo "<li>Password: admin123</li>\n";
        echo "</ul>\n";
    } else {
        echo "<p style='color: red;'>✗ Error creating admin user: " . $insertAdmin->error . "</p>\n";
    }
    $insertAdmin->close();
} else {
    echo "<p style='color: orange;'>⚠ Admin user already exists</p>\n";
}
$checkAdmin->close();

closeDatabaseConnection($conn);

echo "<h2>✅ Database Setup Complete!</h2>\n";
echo "<p>Your database login and registration system is ready to use.</p>\n";
echo "<p><a href='log-in/log-in.php'>Go to Login Page</a> | <a href='log-in/register.php'>Go to Register Page</a></p>\n";
echo "<p><a href='test-auth-system.php'>Run Authentication Tests</a></p>\n";
?>