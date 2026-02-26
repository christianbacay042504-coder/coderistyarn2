<?php
// Debug script for availability issues
require_once __DIR__ . '/config/database.php';

echo "<h2>Availability Debug Information</h2>";

// Check database connection
$conn = getDatabaseConnection();
if (!$conn) {
    echo "<p style='color: red;'>❌ Database connection failed</p>";
    exit;
} else {
    echo "<p style='color: green;'>✅ Database connection successful</p>";
}

// Check if tour_guides table exists
$result = $conn->query("SHOW TABLES LIKE 'tour_guides'");
if ($result->num_rows > 0) {
    echo "<p style='color: green;'>✅ tour_guides table exists</p>";
} else {
    echo "<p style='color: red;'>❌ tour_guides table missing</p>";
}

// Check if tour_guide_availability table exists
$result = $conn->query("SHOW TABLES LIKE 'tour_guide_availability'");
if ($result->num_rows > 0) {
    echo "<p style='color: green;'>✅ tour_guide_availability table exists</p>";
    
    // Show table structure
    echo "<h3>tour_guide_availability Structure:</h3>";
    $result = $conn->query("DESCRIBE tour_guide_availability");
    echo "<table border='1'><tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr><td>{$row['Field']}</td><td>{$row['Type']}</td><td>{$row['Null']}</td><td>{$row['Key']}</td></tr>";
    }
    echo "</table>";
    
    // Show sample data
    echo "<h3>Sample Data in tour_guide_availability:</h3>";
    $result = $conn->query("SELECT * FROM tour_guide_availability LIMIT 5");
    if ($result->num_rows > 0) {
        echo "<table border='1'>";
        // Header
        echo "<tr>";
        foreach ($result->fetch_fields() as $field) {
            echo "<th>{$field->name}</th>";
        }
        echo "</tr>";
        // Data
        $result->data_seek(0); // Reset pointer
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            foreach ($row as $value) {
                echo "<td>" . htmlspecialchars($value) . "</td>";
            }
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No data in tour_guide_availability table</p>";
    }
} else {
    echo "<p style='color: red;'>❌ tour_guide_availability table missing</p>";
}

// Check current user session
session_start();
if (isset($_SESSION['user_id'])) {
    echo "<p style='color: green;'>✅ User logged in with ID: " . $_SESSION['user_id'] . "</p>";
    
    // Check if user has tour guide profile
    $stmt = $conn->prepare("SELECT id, name FROM tour_guides WHERE user_id = ?");
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $guide = $result->fetch_assoc();
        echo "<p style='color: green;'>✅ Tour guide profile found: " . htmlspecialchars($guide['name']) . " (ID: " . $guide['id'] . ")</p>";
    } else {
        echo "<p style='color: orange;'>⚠️ No tour guide profile found for this user</p>";
    }
} else {
    echo "<p style='color: red;'>❌ No user session found</p>";
}

echo "<h3>Test Adding Availability</h3>";
// Test adding availability
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
            echo "<p style='color: green;'>✅ Test availability added successfully</p>";
            echo "<p>Test data: Guide ID: $tourGuideId, Date: $testDate, Time: $testStart - $testEnd</p>";
        } else {
            echo "<p style='color: red;'>❌ Test availability failed: " . $stmt->error . "</p>";
        }
    }
}

closeDatabaseConnection($conn);
?>
