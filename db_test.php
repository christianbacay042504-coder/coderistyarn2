<?php
require_once 'config/database.php';

echo "<h1>Database Connection Test</h1>\n";

$conn = getDatabaseConnection();
if ($conn) {
    echo "<p style='color: green;'>✓ Database connection successful</p>\n";
    
    // Show all databases
    echo "<h2>Available Databases:</h2>\n";
    $result = $conn->query('SHOW DATABASES');
    echo "<ul>\n";
    while ($row = $result->fetch_assoc()) {
        echo "<li>" . $row['Database'] . "</li>\n";
    }
    echo "</ul>\n";
    
    // Check if sjdm_tours database exists
    $result = $conn->query("SHOW DATABASES LIKE 'sjdm_tours'");
    if ($result->num_rows > 0) {
        echo "<p style='color: green;'>✓ Database 'sjdm_tours' exists</p>\n";
        
        // Select the database
        $conn->select_db('sjdm_tours');
        
        // Show tables
        echo "<h2>Tables in sjdm_tours:</h2>\n";
        $result = $conn->query('SHOW TABLES');
        if ($result->num_rows > 0) {
            echo "<ul>\n";
            while ($row = $result->fetch_row()) {
                echo "<li>" . $row[0] . "</li>\n";
            }
            echo "</ul>\n";
            
            // Check users table
            $result = $conn->query("SHOW TABLES LIKE 'users'");
            if ($result->num_rows > 0) {
                echo "<p style='color: green;'>✓ Users table exists</p>\n";
                
                // Count users
                $result = $conn->query('SELECT COUNT(*) as count FROM users');
                $row = $result->fetch_assoc();
                echo "<p>Total users: " . $row['count'] . "</p>\n";
                
                // Show users
                if ($row['count'] > 0) {
                    echo "<h2>Users in database:</h2>\n";
                    $result = $conn->query('SELECT id, first_name, last_name, email, user_type, status FROM users');
                    echo "<table border='1' style='border-collapse: collapse;'>\n";
                    echo "<tr><th>ID</th><th>First Name</th><th>Last Name</th><th>Email</th><th>Type</th><th>Status</th></tr>\n";
                    while ($user = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $user['id'] . "</td>";
                        echo "<td>" . $user['first_name'] . "</td>";
                        echo "<td>" . $user['last_name'] . "</td>";
                        echo "<td>" . $user['email'] . "</td>";
                        echo "<td>" . $user['user_type'] . "</td>";
                        echo "<td>" . $user['status'] . "</td>";
                        echo "</tr>\n";
                    }
                    echo "</table>\n";
                } else {
                    echo "<p style='color: orange;'>⚠ No users found in the database</p>\n";
                }
            } else {
                echo "<p style='color: red;'>✗ Users table does not exist</p>\n";
            }
        } else {
            echo "<p style='color: red;'>✗ No tables found in sjdm_tours database</p>\n";
        }
    } else {
        echo "<p style='color: red;'>✗ Database 'sjdm_tours' does not exist</p>\n";
        echo "<p>You need to create the database and run the setup script.</p>\n";
    }
} else {
    echo "<p style='color: red;'>✗ Database connection failed</p>\n";
    echo "<p>Check your database configuration in config/database.php</p>\n";
}

closeDatabaseConnection($conn);
?>