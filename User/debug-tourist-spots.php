<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include database connection and authentication
require_once '../config/database.php';
require_once '../config/auth.php';

// Simple test content
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Debug Tourist Spots</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background: white;
            color: black;
        }
        .debug-info {
            background: #f0f0f0;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
        }
        .tourist-spot {
            background: #f9f9f9;
            padding: 15px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <h1>Debug Tourist Spots Page</h1>
    
    <div class="debug-info">
        <h2>Debug Information</h2>
        <p>PHP Version: <?php echo PHP_VERSION; ?></p>
        <p>Database Connection: <?php echo (getDatabaseConnection() ? 'Success' : 'Failed'); ?></p>
        <p>Current Time: <?php echo date('Y-m-d H:i:s'); ?></p>
    </div>
    
    <h2>Tourist Spots from Database</h2>
    
    <?php
    $conn = getDatabaseConnection();
    if ($conn) {
        echo '<div class="debug-info"><p>Database connected successfully</p></div>';
        
        $query = "SELECT ts.*, 
                     GROUP_CONCAT(DISTINCT CONCAT(tg.id, ':', tg.name, ':', tg.specialty, ':', tg.rating, ':', tg.verified) ORDER BY tg.rating DESC SEPARATOR '|') as guides_info
                     FROM tourist_spots ts 
                     LEFT JOIN guide_destinations gd ON ts.id = gd.destination_id 
                     LEFT JOIN tour_guides tg ON gd.guide_id = tg.id AND tg.status = 'active'
                     WHERE ts.status = 'active' 
                     GROUP BY ts.id 
                     ORDER BY ts.name";
        
        $result = $conn->query($query);
        
        if ($result && $result->num_rows > 0) {
            echo '<div class="debug-info"><p>Found ' . $result->num_rows . ' tourist spots</p></div>';
            
            while ($spot = $result->fetch_assoc()) {
                echo '<div class="tourist-spot">';
                echo '<h3>' . htmlspecialchars($spot['name']) . '</h3>';
                echo '<p><strong>Category:</strong> ' . htmlspecialchars($spot['category']) . '</p>';
                echo '<p><strong>Description:</strong> ' . htmlspecialchars($spot['description']) . '</p>';
                echo '<p><strong>Rating:</strong> ' . $spot['rating'] . '</p>';
                echo '<p><strong>Entrance Fee:</strong> ' . htmlspecialchars($spot['entrance_fee']) . '</p>';
                echo '<p><strong>Difficulty:</strong> ' . htmlspecialchars($spot['difficulty_level']) . '</p>';
                echo '</div>';
            }
        } else {
            echo '<div class="debug-info"><p>No tourist spots found or query failed: ' . $conn->error . '</p></div>';
        }
        
        closeDatabaseConnection($conn);
    } else {
        echo '<div class="debug-info"><p>Database connection failed</p></div>';
    }
    ?>
    
    <div class="debug-info">
        <h2>Test Complete</h2>
        <p>If you can see this content and the tourist spots above, then the database connection and queries are working.</p>
        <p>The issue with the original page might be CSS-related.</p>
    </div>
</body>
</html>
