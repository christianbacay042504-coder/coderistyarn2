<?php
// Create database script
$servername = "localhost";
$username = "root";
$password = "";

// Create connection without database
$conn = new mysqli($servername, $username, $password);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create database
$sql = "CREATE DATABASE IF NOT EXISTS sjdm_tours";
if ($conn->query($sql) === TRUE) {
    echo "Database created successfully\n";
} else {
    echo "Error creating database: " . $conn->error . "\n";
}

$conn->close();

// Now connect to the database and create tables
$conn = new mysqli($servername, $username, $password, "sjdm_tours");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create tables
$tables = [
    "CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        first_name VARCHAR(50) NOT NULL,
        last_name VARCHAR(50) NOT NULL,
        email VARCHAR(100) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL,
        user_type ENUM('user', 'admin', 'guide') DEFAULT 'user',
        status ENUM('active', 'inactive') DEFAULT 'active',
        address TEXT,
        contact_number VARCHAR(20),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )",

    "CREATE TABLE IF NOT EXISTS tour_guides (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        specialty VARCHAR(100),
        category VARCHAR(50),
        description TEXT,
        contact_number VARCHAR(20),
        email VARCHAR(100),
        rating DECIMAL(3,2) DEFAULT 0.00,
        review_count INT DEFAULT 0,
        verified TINYINT(1) DEFAULT 0,
        status ENUM('active', 'inactive') DEFAULT 'active',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )",

    "CREATE TABLE IF NOT EXISTS bookings (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        guide_id INT NOT NULL,
        destination VARCHAR(255) NOT NULL,
        date DATE NOT NULL,
        guests INT NOT NULL,
        full_name VARCHAR(100) NOT NULL,
        email VARCHAR(100) NOT NULL,
        contact VARCHAR(20) NOT NULL,
        address TEXT,
        nationality VARCHAR(50),
        emergency_name VARCHAR(100),
        emergency_contact VARCHAR(20),
        special_requests TEXT,
        payment_method VARCHAR(50) DEFAULT 'cash',
        terms_agreed TINYINT(1) DEFAULT 1,
        cancellation_acknowledged TINYINT(1) DEFAULT 1,
        booking_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        status ENUM('pending', 'confirmed', 'cancelled', 'completed') DEFAULT 'pending',
        FOREIGN KEY (user_id) REFERENCES users(id),
        FOREIGN KEY (guide_id) REFERENCES tour_guides(id)
    )"
];

foreach ($tables as $table) {
    if ($conn->query($table) === TRUE) {
        echo "Table created successfully\n";
    } else {
        echo "Error creating table: " . $conn->error . "\n";
    }
}

$conn->close();
echo "Database setup complete\n";
?>
