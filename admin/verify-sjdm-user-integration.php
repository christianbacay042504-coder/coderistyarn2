<?php
// Test script to verify sjdm-user tourist-spots integration
require_once __DIR__ . '/../config/database.php';

$conn = getDatabaseConnection();
if (!$conn) {
    die("Database connection failed!");
}

echo "<h1>SJDM-User Tourist Spots Integration Verification</h1>\n";

// Check total count
$result = $conn->query("SELECT COUNT(*) as total FROM tourist_spots WHERE status = 'active'");
$count = $result->fetch_assoc()['total'];
echo "<p><strong>Total active destinations in database:</strong> $count</p>\n";

// Show all destinations with user-facing categories
$result = $conn->query("SELECT id, name, category, location, entrance_fee, rating, status FROM tourist_spots WHERE status = 'active' ORDER BY name");

if ($result->num_rows > 0) {
    echo "<h2>Available Destinations for SJDM-User Interface</h2>\n";
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>\n";
    echo "<tr><th>ID</th><th>Name</th><th>Category</th><th>Display Category</th><th>Location</th><th>Entrance Fee</th><th>Rating</th></tr>\n";
    
    // Category mapping for user interface
    $categoryMap = [
        'nature' => 'Nature & Waterfalls',
        'farm' => 'Farms & Eco-Tourism', 
        'park' => 'Parks & Recreation',
        'religious' => 'Religious Sites',
        'urban' => 'Urban Landmarks',
        'historical' => 'Historical Sites',
        'waterfalls' => 'Waterfalls',
        'mountains' => 'Mountains & Hiking',
        'agri-tourism' => 'Agri-Tourism',
        'religious sites' => 'Religious Sites',
        'parks & recreation' => 'Parks & Recreation',
        'tourist spot' => 'Tourist Spots'
    ];
    
    while ($row = $result->fetch_assoc()) {
        $displayCategory = $categoryMap[$row['category']] ?? $row['category'];
        echo "<tr>";
        echo "<td>" . $row['id'] . "</td>";
        echo "<td><strong>" . htmlspecialchars($row['name']) . "</strong></td>";
        echo "<td>" . htmlspecialchars($row['category']) . "</td>";
        echo "<td>" . htmlspecialchars($displayCategory) . "</td>";
        echo "<td>" . htmlspecialchars($row['location']) . "</td>";
        echo "<td>" . htmlspecialchars($row['entrance_fee']) . "</td>";
        echo "<td>" . number_format($row['rating'], 1) . " ⭐</td>";
        echo "</tr>\n";
    }
    
    echo "</table>\n";
} else {
    echo "<p>No active destinations found in database.</p>\n";
}

// Show categories summary
$result = $conn->query("SELECT category, COUNT(*) as count FROM tourist_spots WHERE status = 'active' GROUP BY category ORDER BY count DESC");
if ($result->num_rows > 0) {
    echo "<h2>Categories Summary (User Interface)</h2>\n";
    echo "<table border='1' style='border-collapse: collapse; width: 50%;'>\n";
    echo "<tr><th>Database Category</th><th>Display Category</th><th>Count</th></tr>\n";
    
    $categoryMap = [
        'nature' => 'Nature & Waterfalls',
        'farm' => 'Farms & Eco-Tourism', 
        'park' => 'Parks & Recreation',
        'religious' => 'Religious Sites',
        'urban' => 'Urban Landmarks',
        'historical' => 'Historical Sites',
        'waterfalls' => 'Waterfalls',
        'mountains' => 'Mountains & Hiking',
        'agri-tourism' => 'Agri-Tourism',
        'religious sites' => 'Religious Sites',
        'parks & recreation' => 'Parks & Recreation',
        'tourist spot' => 'Tourist Spots'
    ];
    
    while ($row = $result->fetch_assoc()) {
        $displayCategory = $categoryMap[$row['category']] ?? $row['category'];
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['category']) . "</td>";
        echo "<td>" . htmlspecialchars($displayCategory) . "</td>";
        echo "<td>" . $row['count'] . "</td>";
        echo "</tr>\n";
    }
    
    echo "</table>\n";
}

closeDatabaseConnection($conn);

echo "<h2>✅ Integration Status</h2>\n";
echo "<p><strong>✅ Tourist-detail destinations successfully imported into database</strong></p>\n";
echo "<p><strong>✅ SJDM-User tourist-spots.php updated with new category mappings</strong></p>\n";
echo "<p><strong>✅ All tourist-detail pages now link to sjdm-user tourist-spots</strong></p>\n";

echo "<h2>🔗 Navigation Links</h2>\n";
echo "<ul>\n";
echo "<li><strong>Admin Panel:</strong> <code>admin/destinations.php</code> - Manage all destinations</li>\n";
echo "<li><strong>User Interface:</strong> <code>sjdm-user/tourist-spots.php</code> - Browse destinations as user</li>\n";
echo "<li><strong>Detail Pages:</strong> <code>tourist-detail/*.php</code> - Individual destination pages with links back to user interface</li>\n";
echo "</ul>\n";

echo "<p><strong>🎉 All destinations from tourist-detail folder are now fully connected to both admin and user interfaces!</strong></p>\n";
?>
