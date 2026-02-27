<?php
/**
 * EXTREME AVAILABILITY SCRIPT - ALL MONTHS, ALL DATES, ALL YEAR!
 * Complete calendar coverage for maximum availability
 */

require_once __DIR__ . '/config/database.php';

echo "🔥 CREATING EXTREME AVAILABILITY - ALL MONTHS, ALL DATES! 🔥\n";

try {
    $conn = getDatabaseConnection();
    if (!$conn) {
        throw new Exception('Database connection failed');
    }

    // Get all tour guides
    $guidesQuery = "SELECT id, user_id FROM tour_guides";
    $guidesResult = $conn->query($guidesQuery);
    
    if (!$guidesResult) {
        throw new Exception('Failed to get tour guides: ' . $conn->error);
    }
    
    $tourGuides = $guidesResult->fetch_all(MYSQLI_ASSOC);
    echo "Found " . count($tourGuides) . " tour guides\n";
    
    $totalCreated = 0;
    $currentDate = date('Y-m-d');
    
    // Define time slots - MORE SLOTS FOR EXTREME AVAILABILITY
    $timeSlots = [
        ['06:00:00', '09:00:00'],  // Early morning
        ['09:00:00', '12:00:00'],  // Morning
        ['12:00:00', '15:00:00'],  // Afternoon
        ['15:00:00', '18:00:00'],  // Late afternoon
        ['18:00:00', '21:00:00'],  // Evening
        ['21:00:00', '23:59:59']   // Night
    ];
    
    foreach ($tourGuides as $guide) {
        $guideId = $guide['id'];
        $userId = $guide['user_id'];
        
        echo "🚀 Processing guide ID: $guideId (User ID: $userId)\n";
        
        // Create availability for NEXT 365 DAYS (1 FULL YEAR!)
        for ($daysAhead = 1; $daysAhead <= 365; $daysAhead++) {
            $targetDate = date('Y-m-d', strtotime($currentDate . " +$daysAhead days"));
            
            // Create ALL time slots for each date (NO RESTRICTIONS!)
            foreach ($timeSlots as $slot) {
                $startTime = $slot[0];
                $endTime = $slot[1];
                
                // Check if availability already exists
                $checkQuery = "
                    SELECT id FROM tour_guide_availability 
                    WHERE tour_guide_id = ? AND available_date = ? AND start_time = ? AND end_time = ?
                ";
                $checkStmt = $conn->prepare($checkQuery);
                $checkStmt->bind_param("isss", $guideId, $targetDate, $startTime, $endTime);
                $checkStmt->execute();
                
                if ($checkStmt->get_result()->num_rows === 0) {
                    // Insert new availability
                    $insertQuery = "
                        INSERT INTO tour_guide_availability 
                        (tour_guide_id, available_date, start_time, end_time, status) 
                        VALUES (?, ?, ?, ?, 'available')
                    ";
                    $insertStmt = $conn->prepare($insertQuery);
                    $insertStmt->bind_param("isss", $guideId, $targetDate, $startTime, $endTime);
                    
                    $result = $insertStmt->execute();
                    
                    if ($result) {
                        $totalCreated++;
                        if ($totalCreated % 100 == 0) {
                            echo "  📅 Created $totalCreated records so far...\n";
                        }
                    } else {
                        echo "  ❌ Error creating availability for $targetDate $startTime-$endTime: " . $insertStmt->error . "\n";
                    }
                    
                    $insertStmt->close();
                }
                
                $checkStmt->close();
            }
        }
        
        // Also create PAST 365 DAYS (complete year backwards)
        for ($daysBack = 1; $daysBack <= 365; $daysBack++) {
            $targetDate = date('Y-m-d', strtotime($currentDate . " -$daysBack days"));
            
            // Create ALL time slots for each past date
            foreach ($timeSlots as $slot) {
                $startTime = $slot[0];
                $endTime = $slot[1];
                
                // Check if availability already exists
                $checkQuery = "
                    SELECT id FROM tour_guide_availability 
                    WHERE tour_guide_id = ? AND available_date = ? AND start_time = ? AND end_time = ?
                ";
                $checkStmt = $conn->prepare($checkQuery);
                $checkStmt->bind_param("isss", $guideId, $targetDate, $startTime, $endTime);
                $checkStmt->execute();
                
                if ($checkStmt->get_result()->num_rows === 0) {
                    // Insert new availability
                    $insertQuery = "
                        INSERT INTO tour_guide_availability 
                        (tour_guide_id, available_date, start_time, end_time, status) 
                        VALUES (?, ?, ?, ?, 'available')
                    ";
                    $insertStmt = $conn->prepare($insertQuery);
                    $insertStmt->bind_param("isss", $guideId, $targetDate, $startTime, $endTime);
                    
                    $result = $insertStmt->execute();
                    
                    if ($result) {
                        $totalCreated++;
                        if ($totalCreated % 100 == 0) {
                            echo "  📅 Created $totalCreated records so far...\n";
                        }
                    } else {
                        echo "  ❌ Error creating PAST availability for $targetDate $startTime-$endTime: " . $insertStmt->error . "\n";
                    }
                    
                    $insertStmt->close();
                }
                
                $checkStmt->close();
            }
        }
    }
    
    echo "\n🎉 EXTREME AVAILABILITY CREATION COMPLETE! 🎉\n";
    echo "==========================================\n";
    echo "🔥 Total new availability records created: $totalCreated\n";
    
    // Set ALL dates to available except today
    $updateQuery = "
        UPDATE tour_guide_availability 
        SET status = 'available'
        WHERE available_date != CURDATE()
    ";
    
    $stmt = $conn->prepare($updateQuery);
    $stmt->execute();
    $updatedRows = $stmt->affected_rows;
    echo "🔥 Updated $updatedRows records to AVAILABLE status\n";
    
    // Set today to unavailable
    $todayQuery = "
        UPDATE tour_guide_availability 
        SET status = 'unavailable'
        WHERE available_date = CURDATE()
    ";
    
    $stmt = $conn->prepare($todayQuery);
    $stmt->execute();
    $todayRows = $stmt->affected_rows;
    echo "🚫 Set $todayRows records to UNAVAILABLE for today\n";
    
    // Show final status
    $summaryQuery = "
        SELECT 
            COUNT(*) as total_records,
            SUM(CASE WHEN status = 'available' THEN 1 ELSE 0 END) as available,
            SUM(CASE WHEN status = 'booked' THEN 1 ELSE 0 END) as booked,
            SUM(CASE WHEN status = 'unavailable' THEN 1 ELSE 0 END) as unavailable,
            COUNT(DISTINCT tour_guide_id) as guides_with_availability,
            MIN(available_date) as earliest_date,
            MAX(available_date) as latest_date
        FROM tour_guide_availability
    ";
    
    $result = $conn->query($summaryQuery);
    if ($result) {
        $summary = $result->fetch_assoc();
        echo "\n📊 FINAL EXTREME AVAILABILITY STATUS:\n";
        echo "====================================\n";
        echo "📅 Total records: {$summary['total_records']}\n";
        echo "✅ Available: {$summary['available']}\n";
        echo "📝 Booked: {$summary['booked']}\n";
        echo "🚫 Unavailable: {$summary['unavailable']}\n";
        echo "👥 Guides with availability: {$summary['guides_with_availability']}\n";
        echo "📆 Date range: {$summary['earliest_date']} to {$summary['latest_date']}\n";
        echo "⏰ Time slots per day: 6 (6am-11:59pm)\n";
    }
    
    echo "\n🔥🔥🔥 EXTREME MODE ACTIVATED! 🔥🔥🔥\n";
    echo "📅 ALL MONTHS COVERED!\n";
    echo "📆 ALL DATES COVERED!\n";
    echo "⏰ ALL TIME SLOTS COVERED!\n";
    echo "🚫 ONLY TODAY IS BLOCKED!\n";
    echo "🎉 CALENDAR IS 1000% READY! 🎉\n";
    
    $stmt->close();
    closeDatabaseConnection($conn);
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    if (isset($conn)) {
        closeDatabaseConnection($conn);
    }
    exit(1);
}
?>
