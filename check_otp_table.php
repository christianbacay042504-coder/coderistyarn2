<?php
require_once __DIR__ . '/config/database.php';

$conn = getDatabaseConnection();
if (!$conn) {
    echo "Database connection failed\n";
    exit;
}

// Check if otp_codes table exists
$result = $conn->query("SHOW TABLES LIKE 'otp_codes'");
if ($result && $result->num_rows > 0) {
    echo "✅ otp_codes table exists\n";
} else {
    echo "❌ otp_codes table missing - creating it...\n";
    
    // Create otp_codes table
    $createSQL = "
    CREATE TABLE otp_codes (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        email VARCHAR(255) NOT NULL,
        otp_code VARCHAR(6) NOT NULL,
        is_used TINYINT(1) DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        expires_at TIMESTAMP NOT NULL,
        used_at TIMESTAMP NULL,
        INDEX idx_email_code (email, otp_code),
        INDEX idx_expires (expires_at),
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    
    if ($conn->query($createSQL)) {
        echo "✅ otp_codes table created successfully\n";
    } else {
        echo "❌ Failed to create otp_codes table: " . $conn->error . "\n";
    }
}

closeDatabaseConnection($conn);
?>
