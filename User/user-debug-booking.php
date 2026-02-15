<?php
// Debug version of book.php to check tour guide loading

// Debug version - bypass session for testing
// session_start();

// Check if user is logged in - bypassed for testing
if (!isset($_SESSION['user_id'])) {
    echo "<p>⚠️ Session bypassed for testing purposes</p>";
}

require_once '../config/database.php';

echo "<h2>Debug: Tour Guide Loading</h2>";

$user_id = $_SESSION['user_id'] ?? 'test_user';
echo "<p>✅ User ID: $user_id is logged in</p>";

try {
    $conn = getDatabaseConnection();
    
    if ($conn) {
        echo "<p>✅ Database connection successful</p>";
        
        // Test the exact same query as book.php
        $guidesStmt = $conn->prepare("SELECT id, name, specialty, category FROM tour_guides WHERE verified = 1 AND status = 'active' ORDER BY name ASC");
        
        if ($guidesStmt) {
            echo "<p>✅ Query prepared successfully</p>";
            
            $guidesStmt->execute();
            echo "<p>✅ Query executed successfully</p>";
            
            $guidesResult = $guidesStmt->get_result();
            $count = $guidesResult->num_rows;
            echo "<p>📊 Query returned $count rows</p>";
            
            if ($count > 0) {
                echo "<h3>Found Tour Guides:</h3>";
                echo "<ul>";
                while ($guide = $guidesResult->fetch_assoc()) {
                    echo "<li><strong>ID {$guide['id']}:</strong> " . htmlspecialchars($guide['name']) . " - " . htmlspecialchars($guide['specialty'] ?? 'No specialty') . "</li>";
                }
                echo "</ul>";
            } else {
                echo "<p>❌ No tour guides found with verified=1 AND status='active'</p>";
                
                // Let's check what's actually in the table
                echo "<h3>Checking all tour guides:</h3>";
                $allStmt = $conn->prepare("SELECT id, name, verified, status FROM tour_guides ORDER BY id");
                $allStmt->execute();
                $allResult = $allStmt->get_result();
                
                echo "<table border='1' style='border-collapse: collapse;'>";
                echo "<tr><th>ID</th><th>Name</th><th>Verified</th><th>Status</th><th>Meets Criteria</th></tr>";
                while ($guide = $allResult->fetch_assoc()) {
                    $meetsCriteria = ($guide['verified'] == 1 && $guide['status'] == 'active') ? '✅ Yes' : '❌ No';
                    echo "<tr>";
                    echo "<td>{$guide['id']}</td>";
                    echo "<td>" . htmlspecialchars($guide['name']) . "</td>";
                    echo "<td>{$guide['verified']}</td>";
                    echo "<td>" . htmlspecialchars($guide['status']) . "</td>";
                    echo "<td>$meetsCriteria</td>";
                    echo "</tr>";
                }
                echo "</table>";
            }
            
            $guidesStmt->close();
        } else {
            echo "<p>❌ Failed to prepare query</p>";
        }
        
        closeDatabaseConnection($conn);
        
    } else {
        echo "<p>❌ Database connection failed</p>";
    }
    
} catch (Exception $e) {
    echo "<p>❌ Error: " . htmlspecialchars($e->getMessage()) . "</p>";
}

echo "<hr>";
echo "<p><a href='book.php'>Go to actual booking page</a></p>";
?>
