<?php
// Debug script to test booking insertion
// Include database connection
require_once 'config/database.php';

echo "<h2>Booking Debug Script</h2>";

// Test database connection
$conn = getDatabaseConnection();
if (!$conn) {
    echo "<p style='color: red;'>❌ Database connection failed!</p>";
    exit();
}

echo "<p style='color: green;'>✅ Database connection successful!</p>";

// Check if bookings table exists and has the correct structure
echo "<h3>Checking Bookings Table Structure:</h3>";
$result = $conn->query("DESCRIBE bookings");
if ($result) {
    echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['Field']) . "</td>";
        echo "<td>" . htmlspecialchars($row['Type']) . "</td>";
        echo "<td>" . htmlspecialchars($row['Null']) . "</td>";
        echo "<td>" . htmlspecialchars($row['Key']) . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p style='color: red;'>❌ Could not describe bookings table!</p>";
}

// Test booking insertion with sample data
echo "<h3>Testing Booking Insertion:</h3>";

$testBookingData = [
    'user_id' => 1,
    'guide_id' => 1,
    'tour_name' => 'Test Tour with Test Guide',
    'destination' => 'Test Destination',
    'booking_date' => date('Y-m-d'),
    'check_in_date' => date('Y-m-d', strtotime('+7 days')),
    'check_out_date' => date('Y-m-d', strtotime('+8 days')),
    'number_of_people' => 2,
    'contact_number' => '+639123456789',
    'email' => 'test@example.com',
    'special_requests' => 'Test special request',
    'total_amount' => 2800.00,
    'payment_method' => 'pay_later',
    'booking_reference' => 'TEST-' . date('Y') . '-' . str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT)
];

echo "<h4>Sample Booking Data:</h4>";
echo "<pre>" . print_r($testBookingData, true) . "</pre>";

// Prepare and execute the insert
$stmt = $conn->prepare("
    INSERT INTO bookings (
        user_id, guide_id, tour_name, destination, 
        booking_date, check_in_date, check_out_date,
        number_of_people, contact_number, email, 
        special_requests, total_amount, payment_method, 
        status, booking_reference
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending', ?)
");

if ($stmt) {
    $stmt->bind_param(
        "iissssissdssss",
        $testBookingData['user_id'],
        $testBookingData['guide_id'],
        $testBookingData['tour_name'],
        $testBookingData['destination'],
        $testBookingData['booking_date'],
        $testBookingData['check_in_date'],
        $testBookingData['check_out_date'],
        $testBookingData['number_of_people'],
        $testBookingData['contact_number'],
        $testBookingData['email'],
        $testBookingData['special_requests'],
        $testBookingData['total_amount'],
        $testBookingData['payment_method'],
        $testBookingData['booking_reference']
    );
    
    if ($stmt->execute()) {
        $bookingId = $stmt->insert_id;
        echo "<p style='color: green;'>✅ Test booking inserted successfully! Booking ID: $bookingId</p>";
        
        // Verify the insertion by selecting the record
        $verifyStmt = $conn->prepare("SELECT * FROM bookings WHERE id = ?");
        $verifyStmt->bind_param("i", $bookingId);
        $verifyStmt->execute();
        $result = $verifyStmt->get_result();
        
        if ($result->num_rows > 0) {
            echo "<h4>✅ Verified Inserted Record:</h4>";
            echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
            echo "<tr>";
            while ($field = $result->fetch_field()) {
                echo "<th>" . htmlspecialchars($field->name) . "</th>";
            }
            echo "</tr>";
            
            $row = $result->fetch_assoc();
            echo "<tr>";
            foreach ($row as $key => $value) {
                echo "<td>" . htmlspecialchars($value) . "</td>";
            }
            echo "</tr>";
            echo "</table>";
        } else {
            echo "<p style='color: red;'>❌ Could not verify inserted record!</p>";
        }
    } else {
        echo "<p style='color: red;'>❌ Failed to insert test booking!</p>";
        echo "<p>Error: " . htmlspecialchars($stmt->error) . "</p>";
    }
} else {
    echo "<p style='color: red;'>❌ Failed to prepare statement!</p>";
    echo "<p>Error: " . htmlspecialchars($conn->error) . "</p>";
}

// Check current bookings in database
echo "<h3>Current Bookings in Database:</h3>";
$result = $conn->query("SELECT COUNT(*) as total FROM bookings");
if ($result) {
    $row = $result->fetch_assoc();
    echo "<p>Total bookings: " . $row['total'] . "</p>";
    
    if ($row['total'] > 0) {
        $result = $conn->query("SELECT id, booking_reference, tour_name, status, created_at FROM bookings ORDER BY created_at DESC LIMIT 5");
        if ($result) {
            echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
            echo "<tr><th>ID</th><th>Reference</th><th>Tour Name</th><th>Status</th><th>Created</th></tr>";
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($row['id']) . "</td>";
                echo "<td>" . htmlspecialchars($row['booking_reference']) . "</td>";
                echo "<td>" . htmlspecialchars($row['tour_name']) . "</td>";
                echo "<td>" . htmlspecialchars($row['status']) . "</td>";
                echo "<td>" . htmlspecialchars($row['created_at']) . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        }
    }
}

closeDatabaseConnection($conn);

echo "<hr>";
echo "<h3>Next Steps:</h3>";
echo "<ol>";
echo "<li>Run this debug script to test database insertion</li>";
echo "<li>If test insertion works, the issue is in the book.php file</li>";
echo "<li>If test insertion fails, there's a database structure issue</li>";
echo "<li>Check the browser's developer console for JavaScript errors</li>";
echo "<li>Check PHP error logs for any server-side errors</li>";
echo "</ol>";
?>
