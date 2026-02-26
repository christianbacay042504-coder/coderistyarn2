<?php
/**
 * Fix database foreign key constraints
 * This script will fix the incorrect foreign key references
 */

require_once __DIR__ . '/../config/database.php';

echo "<h2>Database Foreign Key Fix</h2>";

$conn = getDatabaseConnection();
if (!$conn) {
    echo "<p style='color: red;'>❌ Database connection failed</p>";
    exit;
}

echo "<p style='color: green;'>✅ Database connected</p>";

// Check current foreign key constraints
echo "<h3>Current Foreign Key Constraints:</h3>";
$result = $conn->query("
    SELECT 
        TABLE_NAME,
        COLUMN_NAME,
        CONSTRAINT_NAME,
        REFERENCED_TABLE_NAME,
        REFERENCED_COLUMN_NAME
    FROM 
        INFORMATION_SCHEMA.KEY_COLUMN_USAGE 
    WHERE 
        TABLE_SCHEMA = 'sjdm_tours' 
        AND TABLE_NAME = 'tour_guide_availability'
        AND REFERENCED_TABLE_NAME IS NOT NULL
");

if ($result->num_rows > 0) {
    echo "<table border='1'>";
    echo "<tr><th>Table</th><th>Column</th><th>Constraint</th><th>References</th><th>Ref Column</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>{$row['TABLE_NAME']}</td>";
        echo "<td>{$row['COLUMN_NAME']}</td>";
        echo "<td>{$row['CONSTRAINT_NAME']}</td>";
        echo "<td>{$row['REFERENCED_TABLE_NAME']}</td>";
        echo "<td>{$row['REFERENCED_COLUMN_NAME']}</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p>No foreign key constraints found</p>";
}

// Check if tour_guide_profiles table exists
echo "<h3>Checking Tables:</h3>";
$result = $conn->query("SHOW TABLES LIKE 'tour_guides'");
if ($result->num_rows > 0) {
    echo "<p style='color: green;'>✅ tour_guides table exists</p>";
} else {
    echo "<p style='color: red;'>❌ tour_guides table missing</p>";
}

$result = $conn->query("SHOW TABLES LIKE 'tour_guide_profiles'");
if ($result->num_rows > 0) {
    echo "<p style='color: orange;'>⚠️ tour_guide_profiles table exists (should be removed)</p>";
} else {
    echo "<p style='color: green;'>✅ tour_guide_profiles table does not exist</p>";
}

// Fix the foreign key constraint
echo "<h3>Fixing Foreign Key Constraint:</h3>";

try {
    // Drop the existing foreign key constraint
    $conn->query("ALTER TABLE tour_guide_availability DROP FOREIGN KEY tour_guide_availability_ibfk_1");
    echo "<p style='color: green;'>✅ Dropped old foreign key constraint</p>";
} catch (Exception $e) {
    echo "<p style='color: orange;'>⚠️ Could not drop foreign key (may not exist): " . $e->getMessage() . "</p>";
}

try {
    // Add the correct foreign key constraint
    $conn->query("ALTER TABLE tour_guide_availability ADD FOREIGN KEY (tour_guide_id) REFERENCES tour_guides(id) ON DELETE CASCADE");
    echo "<p style='color: green;'>✅ Added correct foreign key constraint</p>";
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Failed to add foreign key: " . $e->getMessage() . "</p>";
}

// Also fix reviews table if needed
try {
    $conn->query("ALTER TABLE tour_guide_reviews DROP FOREIGN KEY tour_guide_reviews_ibfk_1");
    echo "<p style='color: green;'>✅ Dropped old reviews foreign key constraint</p>";
} catch (Exception $e) {
    echo "<p style='color: orange;'>⚠️ Could not drop reviews foreign key: " . $e->getMessage() . "</p>";
}

try {
    $conn->query("ALTER TABLE tour_guide_reviews ADD FOREIGN KEY (tour_guide_id) REFERENCES tour_guides(id) ON DELETE CASCADE");
    echo "<p style='color: green;'>✅ Added correct reviews foreign key constraint</p>";
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Failed to add reviews foreign key: " . $e->getMessage() . "</p>";
}

echo "<h3>Testing Availability Addition:</h3>";

// Test adding availability
session_start();
if (isset($_SESSION['user_id'])) {
    $stmt = $conn->prepare("SELECT id FROM tour_guides WHERE user_id = ?");
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $tourGuideId = $result->fetch_assoc()['id'];
        
        // Test insert
        $testDate = date('Y-m-d', strtotime('+1 day'));
        $testStart = '09:00:00';
        $testEnd = '12:00:00';
        
        $stmt = $conn->prepare("
            INSERT INTO tour_guide_availability 
            (tour_guide_id, available_date, start_time, end_time, status, created_at) 
            VALUES (?, ?, ?, ?, 'available', NOW())
        ");
        $stmt->bind_param("issss", $tourGuideId, $testDate, $testStart, $testEnd);
        
        if ($stmt->execute()) {
            echo "<p style='color: green;'>✅ Test availability added successfully!</p>";
            echo "<p>Test data: Guide ID: $tourGuideId, Date: $testDate, Time: $testStart - $testEnd</p>";
        } else {
            echo "<p style='color: red;'>❌ Test availability failed: " . $stmt->error . "</p>";
        }
    } else {
        echo "<p style='color: red;'>❌ No tour guide profile found for logged-in user</p>";
    }
} else {
    echo "<p style='color: orange;'>⚠️ No user session - cannot test</p>";
}

closeDatabaseConnection($conn);

echo "<p><strong>Fixed! Try adding availability again in the dashboard.</strong></p>";
?>
