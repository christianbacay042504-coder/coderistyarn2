<?php
require_once 'config/database.php';

echo "<h1>Database Check - Bookings Table</h1>";

$conn = getDatabaseConnection();
if ($conn) {
    echo "<p style='color: green;'>✓ Connected to database: " . DB_NAME . "</p>";
    
    // Check total bookings
    $result = $conn->query("SELECT COUNT(*) as total FROM bookings");
    $row = $result->fetch_assoc();
    echo "<h2>Total Bookings: " . $row['total'] . "</h2>";
    
    // Show recent bookings
    $result = $conn->query("SELECT * FROM bookings ORDER BY created_at DESC LIMIT 10");
    
    if ($result->num_rows > 0) {
        echo "<h3>Recent Bookings:</h3>";
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr style='background: #f0f0f0;'>";
        echo "<th>ID</th><th>Reference</th><th>User ID</th><th>Guide ID</th>";
        echo "<th>Tour Name</th><th>Destination</th><th>Guests</th>";
        echo "<th>Amount</th><th>Status</th><th>Created</th>";
        echo "</tr>";
        
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $row['id'] . "</td>";
            echo "<td>" . $row['booking_reference'] . "</td>";
            echo "<td>" . $row['user_id'] . "</td>";
            echo "<td>" . $row['guide_id'] . "</td>";
            echo "<td>" . $row['tour_name'] . "</td>";
            echo "<td>" . $row['destination'] . "</td>";
            echo "<td>" . $row['number_of_people'] . "</td>";
            echo "<td>₱" . number_format($row['total_amount'], 2) . "</td>";
            echo "<td>" . $row['status'] . "</td>";
            echo "<td>" . $row['created_at'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p style='color: orange;'>⚠ No bookings found in the database.</p>";
    }
    
    // Test inserting a sample booking
    echo "<h3>Testing Database Insert:</h3>";
    $testQuery = "INSERT INTO bookings (user_id, guide_id, tour_name, destination, check_in_date, check_out_date, number_of_people, contact_number, email, total_amount, status, booking_reference) VALUES (4, 1, 'Test Tour', 'Test Destination', CURDATE(), DATE_ADD(CURDATE(), INTERVAL 1 DAY), 2, '09123456789', 'test@example.com', 2800.00, 'pending', 'TEST-" . time() . "')";
    
    if ($conn->query($testQuery)) {
        echo "<p style='color: green;'>✓ Successfully inserted test booking</p>";
        $newId = $conn->insert_id;
        echo "<p>New booking ID: $newId</p>";
    } else {
        echo "<p style='color: red;'>✗ Failed to insert test booking: " . $conn->error . "</p>";
    }
    
    $conn->close();
} else {
    echo "<p style='color: red;'>✗ Failed to connect to database</p>";
}
?>
