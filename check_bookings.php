<?php
require_once 'config/database.php';

echo "<h3>Recent Bookings (Last 10)</h3>";

$conn = getDatabaseConnection();
if ($conn) {
    $result = $conn->query("
        SELECT id, booking_reference, tour_name, destination, status, 
               check_in_date, check_out_date, number_of_people, 
               total_amount, created_at 
        FROM bookings 
        ORDER BY created_at DESC 
        LIMIT 10
    ");
    
    if ($result && $result->num_rows > 0) {
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr style='background: #f0f0f0;'>";
        echo "<th>ID</th><th>Reference</th><th>Tour Name</th><th>Destination</th>";
        echo "<th>Status</th><th>Dates</th><th>Guests</th><th>Amount</th><th>Created</th>";
        echo "</tr>";
        
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row['id']) . "</td>";
            echo "<td>" . htmlspecialchars($row['booking_reference']) . "</td>";
            echo "<td>" . htmlspecialchars($row['tour_name']) . "</td>";
            echo "<td>" . htmlspecialchars($row['destination']) . "</td>";
            echo "<td><span style='color: " . ($row['status'] == 'pending' ? 'orange' : 'green') . ";'>" . htmlspecialchars($row['status']) . "</span></td>";
            echo "<td>" . htmlspecialchars($row['check_in_date']) . " - " . htmlspecialchars($row['check_out_date']) . "</td>";
            echo "<td>" . htmlspecialchars($row['number_of_people']) . "</td>";
            echo "<td>₱" . number_format($row['total_amount'], 2) . "</td>";
            echo "<td>" . htmlspecialchars($row['created_at']) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
        
        echo "<p><strong>Total bookings:</strong> " . $result->num_rows . " shown</p>";
    } else {
        echo "<p style='color: orange;'>No bookings found in the database.</p>";
    }
    
    closeDatabaseConnection($conn);
} else {
    echo "<p style='color: red;'>Database connection failed.</p>";
}
?>
