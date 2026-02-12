<?php
// Test script to verify tour guide verification functionality
// This script checks if verified tour guides appear in the booking dropdown

require_once 'config/database.php';

echo "<h2>Tour Guide Verification Test</h2>";

try {
    $conn = getDatabaseConnection();
    
    if ($conn) {
        echo "<p>✅ Database connection successful</p>";
        
        // Get all tour guides
        $allGuidesStmt = $conn->prepare("SELECT id, name, verified, status FROM tour_guides ORDER BY name ASC");
        $allGuidesStmt->execute();
        $allGuidesResult = $allGuidesStmt->get_result();
        
        echo "<h3>All Tour Guides:</h3>";
        echo "<table border='1' style='border-collapse: collapse; margin: 20px 0;'>";
        echo "<tr><th>ID</th><th>Name</th><th>Status</th><th>Verified</th><th>Will Appear in Booking</th></tr>";
        
        while ($guide = $allGuidesResult->fetch_assoc()) {
            $willAppear = ($guide['verified'] == 1 && $guide['status'] == 'active') ? '✅ Yes' : '❌ No';
            $verifiedStatus = $guide['verified'] == 1 ? '✅ Verified' : '❌ Unverified';
            $statusText = $guide['status'] ?? 'unknown';
            
            echo "<tr>";
            echo "<td>{$guide['id']}</td>";
            echo "<td>" . htmlspecialchars($guide['name']) . "</td>";
            echo "<td>" . htmlspecialchars($statusText) . "</td>";
            echo "<td>$verifiedStatus</td>";
            echo "<td>$willAppear</td>";
            echo "</tr>";
        }
        echo "</table>";
        
        // Get only verified and active guides (what appears in booking)
        $verifiedGuidesStmt = $conn->prepare("SELECT id, name, specialty, category FROM tour_guides WHERE verified = 1 AND status = 'active' ORDER BY name ASC");
        $verifiedGuidesStmt->execute();
        $verifiedGuidesResult = $verifiedGuidesStmt->get_result();
        
        echo "<h3>Verified & Active Guides (Will appear in booking dropdown):</h3>";
        if ($verifiedGuidesResult->num_rows > 0) {
            echo "<ul>";
            while ($guide = $verifiedGuidesResult->fetch_assoc()) {
                echo "<li><strong>ID {$guide['id']}:</strong> " . htmlspecialchars($guide['name']) . " - " . htmlspecialchars($guide['specialty'] ?? 'No specialty') . "</li>";
            }
            echo "</ul>";
        } else {
            echo "<p>❌ No verified and active tour guides found. Users will see 'No tour guides available' in the booking form.</p>";
        }
        
        $allGuidesStmt->close();
        $verifiedGuidesStmt->close();
        closeDatabaseConnection($conn);
        
    } else {
        echo "<p>❌ Database connection failed</p>";
    }
    
} catch (Exception $e) {
    echo "<p>❌ Error: " . htmlspecialchars($e->getMessage()) . "</p>";
}

echo "<hr>";
echo "<h3>How the System Works:</h3>";
echo "<ol>";
echo "<li>Admin adds tour guides in <strong>admin/tour-guides.php</strong></li>";
echo "<li>Admin can verify/unverify guides using the verification button (✓/✗)</li>";
echo "<li>Only guides with <code>verified = 1</code> AND <code>status = 'active'</code> appear in the booking dropdown</li>";
echo "<li>The booking form in <strong>sjdm-user/book.php</strong> automatically fetches verified guides</li>";
echo "</ol>";

echo "<h3>Test the Flow:</h3>";
echo "<p>1. <a href='admin/tour-guides.php' target='_blank'>Open Admin Tour Guides</a> to verify/unverify guides</p>";
echo "<p>2. <a href='sjdm-user/book.php' target='_blank'>Open Booking Form</a> to see the dropdown</p>";
?>
