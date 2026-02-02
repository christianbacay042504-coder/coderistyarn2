<?php
require_once __DIR__ . '/../config/auth.php';
requireAdmin();

// Get database connection
$conn = getDatabaseConnection();

// Test if tour_guides table exists and has data
if ($conn) {
    echo "<h1>Database Test</h1>";
    
    // Check if table exists
    $tableCheck = $conn->query("SHOW TABLES LIKE 'tour_guides'");
    if ($tableCheck->num_rows > 0) {
        echo "<p>✅ tour_guides table exists</p>";
        
        // Check if there's data
        $countQuery = $conn->query("SELECT COUNT(*) as count FROM tour_guides");
        $count = $countQuery->fetch_assoc()['count'];
        echo "<p>Tour guides count: $count</p>";
        
        // Show sample data
        if ($count > 0) {
            echo "<h3>Sample Tour Guide Data:</h3>";
            $sampleQuery = $conn->query("SELECT id, name, email, specialty FROM tour_guides LIMIT 3");
            while ($row = $sampleQuery->fetch_assoc()) {
                echo "<p>ID: {$row['id']}, Name: {$row['name']}, Email: {$row['email']}, Specialty: {$row['specialty']}</p>";
            }
        } else {
            echo "<p>❌ No tour guides found in database</p>";
        }
        
        // Test AJAX endpoint
        if (isset($_GET['test_ajax'])) {
            $guideId = 1;
            $stmt = $conn->prepare("SELECT * FROM tour_guides WHERE id = ?");
            $stmt->bind_param("i", $guideId);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                $guide = $result->fetch_assoc();
                echo "<h3>AJAX Test Result:</h3>";
                echo "<pre>" . json_encode(['success' => true, 'data' => $guide], JSON_PRETTY_PRINT) . "</pre>";
            } else {
                echo "<p>❌ No guide found with ID 1</p>";
            }
        }
        
    } else {
        echo "<p>❌ tour_guides table doesn't exist</p>";
    }
    
    closeDatabaseConnection($conn);
} else {
    echo "<p>❌ Database connection failed</p>";
}
?>

<?php if (!isset($_GET['test_ajax'])): ?>
<p><a href="?test_ajax=1">Test AJAX Endpoint</a></p>
<?php endif; ?>
