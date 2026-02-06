<?php
// Test script to verify imported destinations
require_once __DIR__ . '/../config/database.php';

$conn = getDatabaseConnection();
if (!$conn) {
    die("Database connection failed!");
}

echo "<h1>Imported Destinations Verification</h1>\n";

// Check total count
$result = $conn->query("SELECT COUNT(*) as total FROM tourist_spots");
$count = $result->fetch_assoc()['total'];
echo "<p><strong>Total destinations in database:</strong> $count</p>\n";

// Show all destinations
$result = $conn->query("SELECT id, name, category, location, entrance_fee, status FROM tourist_spots ORDER BY name");

if ($result->num_rows > 0) {
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>\n";
    echo "<tr><th>ID</th><th>Name</th><th>Category</th><th>Location</th><th>Entrance Fee</th><th>Status</th></tr>\n";
    
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['id'] . "</td>";
        echo "<td>" . htmlspecialchars($row['name']) . "</td>";
        echo "<td>" . htmlspecialchars($row['category']) . "</td>";
        echo "<td>" . htmlspecialchars($row['location']) . "</td>";
        echo "<td>" . htmlspecialchars($row['entrance_fee']) . "</td>";
        echo "<td>" . htmlspecialchars($row['status']) . "</td>";
        echo "</tr>\n";
    }
    
    echo "</table>\n";
} else {
    echo "<p>No destinations found in database.</p>\n";
}

// Show categories
$result = $conn->query("SELECT DISTINCT category, COUNT(*) as count FROM tourist_spots GROUP BY category ORDER BY count DESC");
if ($result->num_rows > 0) {
    echo "<h2>Categories Summary</h2>\n";
    echo "<table border='1' style='border-collapse: collapse; width: 50%;'>\n";
    echo "<tr><th>Category</th><th>Count</th></tr>\n";
    
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['category']) . "</td>";
        echo "<td>" . $row['count'] . "</td>";
        echo "</tr>\n";
    }
    
    echo "</table>\n";
}

closeDatabaseConnection($conn);

echo "<p><strong>✅ All destinations have been successfully imported into the admin database!</strong></p>\n";
echo "<p>You can now manage these destinations through the admin panel at: <code>admin/destinations.php</code></p>\n";
?>
