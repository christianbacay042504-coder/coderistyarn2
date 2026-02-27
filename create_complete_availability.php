<?php
/**
 * Script to create availability for ALL dates (including weekends) for all tour guides
 * This will ensure complete availability coverage
 */

require_once __DIR__ . '/config/database.php';

echo "Creating availability for ALL dates (including weekends)...\n";

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
    
    // Define time slots
    $timeSlots = [
        ['08:00:00', '12:00:00'],
        ['13:00:00', '17:00:00'],
        ['18:00:00', '20:00:00']
    ];
    
    foreach ($tourGuides as $guide) {
        $guideId = $guide['id'];
        $userId = $guide['user_id'];
        
        echo "Processing guide ID: $guideId (User ID: $userId)\n";
        
        // Create availability for the next 60 days (including weekends)
        for ($daysAhead = 1; $daysAhead <= 60; $daysAhead++) {
            $targetDate = date('Y-m-d', strtotime($currentDate . " +$daysAhead days"));
            
            // Create time slots for each date (NO WEEKEND FILTER - ALL DAYS INCLUDED)
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
                        echo "  Created availability for $targetDate $startTime-$endTime\n";
                    } else {
                        echo "  Error creating availability for $targetDate $startTime-$endTime: " . $insertStmt->error . "\n";
                    }
                    
                    $insertStmt->close();
                }
                
                $checkStmt->close();
            }
        }
        
        // Also create some past dates availability (last 30 days)
        for ($daysBack = 1; $daysBack <= 30; $daysBack++) {
            $targetDate = date('Y-m-d', strtotime($currentDate . " -$daysBack days"));
            
            // Create time slots for each past date
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
                        echo "  Created PAST availability for $targetDate $startTime-$endTime\n";
                    } else {
                        echo "  Error creating PAST availability for $targetDate $startTime-$endTime: " . $insertStmt->error . "\n";
                    }
                    
                    $insertStmt->close();
                }
                
                $checkStmt->close();
            }
        }
    }
    
    echo "\nComplete Availability Creation Summary:\n";
    echo "=====================================\n";
    echo "Total new availability records created: $totalCreated\n";
    
    // Show current availability status
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
        echo "\nComplete Availability Status:\n";
        echo "Total records: {$summary['total_records']}\n";
        echo "Available: {$summary['available']}\n";
        echo "Booked: {$summary['booked']}\n";
        echo "Unavailable: {$summary['unavailable']}\n";
        echo "Guides with availability: {$summary['guides_with_availability']}\n";
        echo "Date range: {$summary['earliest_date']} to {$summary['latest_date']}\n";
    }
    
    echo "\nCOMPLETE availability created successfully!\n";
    echo "ALL dates (including weekends and past dates) are now available!\n";
    
    closeDatabaseConnection($conn);
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    if (isset($conn)) {
        closeDatabaseConnection($conn);
    }
    exit(1);
}
?>
