<?php
// Comprehensive test to diagnose the booking issue
echo "<h1>Comprehensive Booking System Test</h1>";

// Step 1: Check database connection
echo "<h2>Step 1: Database Connection Test</h2>";
try {
    require_once 'config/database.php';
    $conn = getDatabaseConnection();
    
    if ($conn) {
        echo "<p style='color: green;'>✅ Database connection successful</p>";
        
        // Step 2: Check if bookings table exists
        echo "<h2>Step 2: Bookings Table Structure</h2>";
        $result = $conn->query("SHOW TABLES LIKE 'bookings'");
        if ($result->num_rows > 0) {
            echo "<p style='color: green;'>✅ Bookings table exists</p>";
            
            // Check table structure
            $structure = $conn->query("DESCRIBE bookings");
            echo "<h3>Bookings Table Columns:</h3>";
            echo "<table border='1' style='border-collapse: collapse;'>";
            echo "<tr><th>Column</th><th>Type</th><th>Null</th><th>Key</th></tr>";
            while ($row = $structure->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($row['Field']) . "</td>";
                echo "<td>" . htmlspecialchars($row['Type']) . "</td>";
                echo "<td>" . htmlspecialchars($row['Null']) . "</td>";
                echo "<td>" . htmlspecialchars($row['Key']) . "</td>";
                echo "</tr>";
            }
            echo "</table>";
            
            // Step 3: Check current bookings
            echo "<h2>Step 3: Current Bookings in Database</h2>";
            $count = $conn->query("SELECT COUNT(*) as total FROM bookings");
            $total = $count->fetch_assoc();
            echo "<p>Total bookings: " . $total['total'] . "</p>";
            
            if ($total['total'] > 0) {
                $bookings = $conn->query("SELECT id, booking_reference, tour_name, status, created_at FROM bookings ORDER BY created_at DESC LIMIT 5");
                echo "<h4>Recent Bookings:</h4>";
                echo "<table border='1' style='border-collapse: collapse;'>";
                echo "<tr><th>ID</th><th>Reference</th><th>Tour Name</th><th>Status</th><th>Created</th></tr>";
                while ($row = $bookings->fetch_assoc()) {
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
            
            // Step 4: Test direct booking insertion
            echo "<h2>Step 4: Direct Booking Insertion Test</h2>";
            
            // Check if user exists
            $userCheck = $conn->query("SELECT id, first_name, last_name FROM users LIMIT 1");
            if ($userCheck->num_rows > 0) {
                $user = $userCheck->fetch_assoc();
                echo "<p>Using test user: " . htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) . " (ID: " . $user['id'] . ")</p>";
                
                // Check if guide exists
                $guideCheck = $conn->query("SELECT id, name FROM tour_guides LIMIT 1");
                if ($guideCheck->num_rows > 0) {
                    $guide = $guideCheck->fetch_assoc();
                    echo "<p>Using test guide: " . htmlspecialchars($guide['name']) . " (ID: " . $guide['id'] . ")</p>";
                    
                    // Test insertion
                    $testData = [
                        'user_id' => $user['id'],
                        'guide_id' => $guide['id'],
                        'tour_name' => $guide['name'] . ' Test Tour',
                        'destination' => 'Test Destination',
                        'booking_date' => date('Y-m-d'),
                        'check_in_date' => date('Y-m-d', strtotime('+7 days')),
                        'check_out_date' => date('Y-m-d', strtotime('+8 days')),
                        'number_of_people' => 2,
                        'contact_number' => '+639123456789',
                        'email' => 'test@example.com',
                        'special_requests' => 'Test request',
                        'total_amount' => 2800.00,
                        'payment_method' => 'pay_later',
                        'booking_reference' => 'TEST-' . date('Y-m-d-His')
                    ];
                    
                    echo "<h4>Test Booking Data:</h4>";
                    echo "<pre>" . print_r($testData, true) . "</pre>";
                    
                    // Attempt insertion
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
                            $testData['user_id'],
                            $testData['guide_id'],
                            $testData['tour_name'],
                            $testData['destination'],
                            $testData['booking_date'],
                            $testData['check_in_date'],
                            $testData['check_out_date'],
                            $testData['number_of_people'],
                            $testData['contact_number'],
                            $testData['email'],
                            $testData['special_requests'],
                            $testData['total_amount'],
                            $testData['payment_method'],
                            $testData['booking_reference']
                        );
                        
                        if ($stmt->execute()) {
                            $bookingId = $stmt->insert_id;
                            echo "<p style='color: green;'>✅ Test booking inserted successfully! ID: $bookingId</p>";
                            
                            // Verify insertion
                            $verify = $conn->prepare("SELECT * FROM bookings WHERE id = ?");
                            $verify->bind_param("i", $bookingId);
                            $verify->execute();
                            $result = $verify->get_result();
                            
                            if ($result->num_rows > 0) {
                                echo "<p style='color: green;'>✅ Booking verified in database!</p>";
                                $row = $result->fetch_assoc();
                                echo "<h4>Inserted Booking:</h4>";
                                echo "<pre>" . print_r($row, true) . "</pre>";
                            } else {
                                echo "<p style='color: red;'>❌ Could not verify booking!</p>";
                            }
                        } else {
                            echo "<p style='color: red;'>❌ Insert failed: " . $stmt->error . "</p>";
                        }
                    } else {
                        echo "<p style='color: red;'>❌ Prepare failed: " . $conn->error . "</p>";
                    }
                } else {
                    echo "<p style='color: red;'>❌ No tour guides found in database!</p>";
                }
            } else {
                echo "<p style='color: red;'>❌ No users found in database!</p>";
            }
            
        } else {
            echo "<p style='color: red;'>❌ Bookings table does not exist!</p>";
            echo "<p>You need to import the enhanced bookings table structure.</p>";
        }
        
        closeDatabaseConnection($conn);
        
    } else {
        echo "<p style='color: red;'>❌ Database connection failed</p>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<h2>Next Steps:</h2>";
echo "<ol>";
echo "<li>If database connection fails: Check XAMPP MySQL service</li>";
echo "<li>If bookings table doesn't exist: Import the SQL file</li>";
echo "<li>If test insertion works: The issue is in the booking form</li>";
echo "<li>If test insertion fails: There's a database structure issue</li>";
echo "</ol>";

echo "<h2>Quick Fix Options:</h2>";
echo "<p><a href='sjdm_tours_complete.sql'>Download Complete SQL</a> | ";
echo "<a href='import_enhanced_bookings_simple.sql'>Import Enhanced Bookings</a></p>";
?>
