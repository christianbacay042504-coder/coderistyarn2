<?php
require_once 'config/database.php';

echo "Checking database connection...\n";
$conn = getDatabaseConnection();

if ($conn) {
    echo "Database connected successfully\n";
    
    // Check if tourist_spots table exists
    $result = $conn->query("SHOW TABLES LIKE 'tourist_spots'");
    if ($result && $result->num_rows > 0) {
        echo "tourist_spots table exists\n";
        
        // Count tourist spots
        $result = $conn->query("SELECT COUNT(*) as count FROM tourist_spots");
        if ($result) {
            $row = $result->fetch_assoc();
            echo "Tourist spots count: " . $row['count'] . "\n";
            
            // Show first few spots
            $result = $conn->query("SELECT id, name, category, status FROM tourist_spots LIMIT 5");
            if ($result && $result->num_rows > 0) {
                echo "First few tourist spots:\n";
                while ($row = $result->fetch_assoc()) {
                    echo "ID: " . $row['id'] . ", Name: " . $row['name'] . ", Category: " . $row['category'] . ", Status: " . $row['status'] . "\n";
                }
            }
        } else {
            echo "Error querying tourist_spots: " . $conn->error . "\n";
        }
    } else {
        echo "tourist_spots table does not exist\n";
        
        // Show all tables
        $result = $conn->query("SHOW TABLES");
        if ($result) {
            echo "Available tables:\n";
            while ($row = $result->fetch_row()) {
                echo "- " . $row[0] . "\n";
            }
        }
    }
    
    closeDatabaseConnection($conn);
} else {
    echo "Database connection failed\n";
}
?>
