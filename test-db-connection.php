<?php
// Simple database connection test
echo "<h2>Database Connection Test</h2>";

// Test basic mysqli connection
$host = 'localhost:3306';
$user = 'root';
$pass = '';
$dbname = 'sjdm_tours';

echo "<p>Testing connection to: $host as $user</p>";

try {
    // Test connection without database first
    $conn = new mysqli($host, $user, $pass);
    
    if ($conn->connect_error) {
        echo "<p style='color: red;'>Connection failed: " . $conn->connect_error . "</p>";
    } else {
        echo "<p style='color: green;'>✓ Connected to MySQL server</p>";
        
        // Check if database exists
        $result = $conn->query("SHOW DATABASES LIKE '$dbname'");
        if ($result->num_rows > 0) {
            echo "<p style='color: green;'>✓ Database '$dbname' exists</p>";
            
            // Try to select the database
            if ($conn->select_db($dbname)) {
                echo "<p style='color: green;'>✓ Successfully selected database</p>";
                
                // Test a simple query
                $tables = $conn->query("SHOW TABLES");
                echo "<p>Found " . $tables->num_rows . " tables in database:</p>";
                echo "<ul>";
                while ($row = $tables->fetch_row()) {
                    echo "<li>" . $row[0] . "</li>";
                }
                echo "</ul>";
                
                // Test tour_guides table specifically
                $guides = $conn->query("SELECT COUNT(*) as count FROM tour_guides WHERE status = 'active'");
                if ($guides) {
                    $count = $guides->fetch_assoc()['count'];
                    echo "<p style='color: green;'>✓ Found $count active tour guides</p>";
                } else {
                    echo "<p style='color: orange;'>Could not query tour_guides table: " . $conn->error . "</p>";
                }
                
            } else {
                echo "<p style='color: red;'>✗ Could not select database: " . $conn->error . "</p>";
            }
        } else {
            echo "<p style='color: red;'>✗ Database '$dbname' does not exist</p>";
            echo "<p>Available databases:</p><ul>";
            $dbs = $conn->query("SHOW DATABASES");
            while ($row = $dbs->fetch_row()) {
                echo "<li>" . $row[0] . "</li>";
            }
            echo "</ul>";
        }
        
        $conn->close();
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>Exception: " . $e->getMessage() . "</p>";
}

// Test the actual database function
echo "<h3>Testing getDatabaseConnection() function:</h3>";
require_once 'config/database.php';
$conn = getDatabaseConnection();
if ($conn) {
    echo "<p style='color: green;'>✓ getDatabaseConnection() works</p>";
    closeDatabaseConnection($conn);
} else {
    echo "<p style='color: red;'>✗ getDatabaseConnection() failed</p>";
}
?>
