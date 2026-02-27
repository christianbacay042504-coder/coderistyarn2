<?php
require_once __DIR__ . '/config/database.php';

echo "Checking and creating user_comments table...\n";

$conn = getDatabaseConnection();
if (!$conn) {
    die("Database connection failed\n");
}

// Check if table exists
$result = $conn->query("SHOW TABLES LIKE 'user_comments'");
if ($result->num_rows == 0) {
    echo "Table user_comments does not exist. Creating...\n";
    
    // Create the table
    $sql = "CREATE TABLE `user_comments` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `user_id` int(11) NOT NULL,
        `comment_text` text NOT NULL,
        `comment_type` enum('preset', 'custom', 'both') NOT NULL DEFAULT 'custom',
        `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        UNIQUE KEY `unique_user_comment` (`user_id`),
        FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    
    if ($conn->query($sql)) {
        echo "Table user_comments created successfully!\n";
    } else {
        echo "Error creating table: " . $conn->error . "\n";
    }
} else {
    echo "Table user_comments already exists.\n";
}

closeDatabaseConnection($conn);
echo "Done!\n";
?>
