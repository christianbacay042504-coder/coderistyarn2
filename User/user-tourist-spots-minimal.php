<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "Starting minimal tourist spots page...<br>";

// Only include database.php (skip auth.php for now)
require_once '../config/database.php';

echo "Database.php included<br>";

// OpenWeatherMap API configuration
$apiKey = '6c21a0d2aaf514cb8d21d56814312b19';
$weatherUrl = "https://api.openweathermap.org/data/2.5/weather?q=San%20Jose%20Del%20Monte,Bulacan&appid={$apiKey}&units=metric";

$weatherData = null;
$weatherError = null;
$currentTemp = '28';
$weatherLabel = 'Sunny';

echo "Weather API setup complete<br>";

// Get current date and weekday
$currentWeekday = date('l');
$currentDate = date('F Y');

echo "Date variables set<br>";

// Check if user is logged in (simplified)
$isLoggedIn = isset($_SESSION['user_id']);

echo "Login check complete<br>";

// Get current user data (simplified)
$conn = getDatabaseConnection();
$currentUser = [];
if ($conn && $isLoggedIn) {
    echo "Getting user data...<br>";
    $stmt = $conn->prepare("SELECT first_name, last_name, email FROM users WHERE id = ?");
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $currentUser = [
            'name' => $user['first_name'] . ' ' . $user['last_name'],
            'email' => $user['email']
        ];
        echo "User data retrieved<br>";
    }
    closeDatabaseConnection($conn);
} else {
    echo "Skipping user data (not logged in or no connection)<br>";
}

echo "Starting HTML output...<br>";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tourist Spots - San Jose del Monte Bulacan</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: white; }
        .debug { background: #f0f0f0; padding: 10px; margin: 10px 0; border: 1px solid #ccc; }
        .spot { border: 1px solid #ddd; margin: 10px; padding: 10px; }
    </style>
</head>
<body>
    <div class="debug">
        <strong>DEBUG:</strong> HTML rendering started at <?php echo date('Y-m-d H:i:s'); ?>
    </div>
    
    <h1>Tourist Spots - Minimal Version</h1>
    
    <div class="debug">
        <strong>Weather:</strong> <?php echo $currentTemp; ?>°C - <?php echo $weatherLabel; ?>
    </div>
    
    <h2>Tourist Spots from Database</h2>
    
    <?php
    echo "Fetching tourist spots...<br>";
    $conn = getDatabaseConnection();
    if ($conn) {
        echo "Database connected<br>";
        
        $query = "SELECT * FROM tourist_spots WHERE status = 'active' ORDER BY name LIMIT 5";
        $result = $conn->query($query);
        
        if ($result && $result->num_rows > 0) {
            echo "Found " . $result->num_rows . " tourist spots<br>";
            while ($spot = $result->fetch_assoc()) {
                echo '<div class="spot">';
                echo '<h3>' . htmlspecialchars($spot['name']) . '</h3>';
                echo '<p>Category: ' . htmlspecialchars($spot['category']) . '</p>';
                echo '<p>Description: ' . htmlspecialchars(substr($spot['description'], 0, 200)) . '...</p>';
                echo '</div>';
            }
        } else {
            echo "No tourist spots found<br>";
        }
        
        closeDatabaseConnection($conn);
    } else {
        echo "Database connection failed<br>";
    }
    ?>
    
    <div class="debug">
        <strong>DEBUG:</strong> Page completed successfully
    </div>
</body>
</html>
