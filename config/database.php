<?php
// Database Configuration File
// Created: January 30, 2026

// Database credentials
define('DB_HOST', 'localhost:3306');
define('DB_USER', 'root');  // Change this to your MySQL username
define('DB_PASS', '');      // Change this to your MySQL password
define('DB_NAME', 'smrt_sjdm_tours');

// Create database connection
function getDatabaseConnection() {
    try {
        $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        
        // Check connection
        if ($conn->connect_error) {
            throw new Exception("Connection failed: " . $conn->connect_error);
        }
        
        // Set charset to utf8mb4
        $conn->set_charset("utf8mb4");
        
        return $conn;
    } catch (Exception $e) {
        error_log("Database connection error: " . $e->getMessage());
        return null;
    }
}

// Close database connection
function closeDatabaseConnection($conn) {
    if ($conn) {
        $conn->close();
    }
}

// Test connection function
function testDatabaseConnection() {
    $conn = getDatabaseConnection();
    if ($conn) {
        closeDatabaseConnection($conn);
        return true;
    }
    return false;
}
?>
