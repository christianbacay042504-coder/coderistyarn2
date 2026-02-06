<?php
// Test script to verify AUTO_INCREMENT fix
require_once 'config/database.php';

echo "<h2>AUTO_INCREMENT Fix Verification</h2>";

// Test adding a new destination (tourist spot)
echo "<h3>Testing: Add New Tourist Spot</h3>";

try {
    $sql = "INSERT INTO tourist_spots (name, description, category, location, address) VALUES (?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    
    $name = "Test Destination " . date('Y-m-d H:i:s');
    $description = "This is a test destination to verify AUTO_INCREMENT works";
    $category = "nature";
    $location = "Test Location";
    $address = "Test Address";
    
    $stmt->execute([$name, $description, $category, $location, $address]);
    
    $last_id = $pdo->lastInsertId();
    echo "<p style='color: green;'>✅ Success! New destination added with ID: <strong>$last_id</strong></p>";
    
    // Verify the record was inserted correctly
    $check_sql = "SELECT id, name FROM tourist_spots WHERE id = ?";
    $check_stmt = $pdo->prepare($check_sql);
    $check_stmt->execute([$last_id]);
    $result = $check_stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($result) {
        echo "<p>✅ Verification successful: Found record with ID {$result['id']} and name '{$result['name']}'</p>";
    }
    
} catch (PDOException $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
}

// Test adding a new booking
echo "<h3>Testing: Add New Booking</h3>";

try {
    $sql = "INSERT INTO bookings (user_id, tour_name, destination, booking_date, number_of_people, total_amount) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    
    $user_id = 1; // Assuming user with ID 1 exists
    $tour_name = "Test Tour " . date('Y-m-d H:i:s');
    $destination = "Test Destination";
    $booking_date = date('Y-m-d');
    $number_of_people = 2;
    $total_amount = 1500.00;
    
    $stmt->execute([$user_id, $tour_name, $destination, $booking_date, $number_of_people, $total_amount]);
    
    $last_id = $pdo->lastInsertId();
    echo "<p style='color: green;'>✅ Success! New booking added with ID: <strong>$last_id</strong></p>";
    
} catch (PDOException $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
}

// Display current AUTO_INCREMENT values
echo "<h3>Current AUTO_INCREMENT Values</h3>";

$tables = ['users', 'login_activity', 'tourist_spots', 'tour_guides', 'hotels', 'bookings', 'saved_tours'];

foreach ($tables as $table) {
    try {
        $sql = "SELECT AUTO_INCREMENT FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$table]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($result) {
            echo "<p><strong>$table:</strong> {$result['AUTO_INCREMENT']}</p>";
        }
    } catch (PDOException $e) {
        echo "<p style='color: red;'>Error getting AUTO_INCREMENT for $table: " . $e->getMessage() . "</p>";
    }
}

echo "<h3>Test Complete</h3>";
echo "<p><a href='javascript:history.back()'>Go Back</a></p>";
?>
