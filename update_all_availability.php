<?php
/**
 * Script to update all tour guide availability to available except current date
 * This will make all dates available for booking except today
 */

require_once __DIR__ . '/config/database.php';

echo "Starting tour guide availability update...\n";

try {
    $conn = getDatabaseConnection();
    if (!$conn) {
        throw new Exception('Database connection failed');
    }

    // Update all availability records to 'available' status except for current date
    $updateQuery = "
        UPDATE tour_guide_availability 
        SET status = 'available'
        WHERE available_date != CURDATE()
    ";
    
    $stmt = $conn->prepare($updateQuery);
    if (!$stmt) {
        throw new Exception('Failed to prepare update query: ' . $conn->error);
    }
    
    $result = $stmt->execute();
    if (!$result) {
        throw new Exception('Failed to execute update query: ' . $stmt->error);
    }
    
    $affectedRows = $stmt->affected_rows;
    echo "Updated $affectedRows availability records to 'available' status\n";
    
    // Set current date availability to 'unavailable' to prevent same-day bookings
    $currentDateQuery = "
        UPDATE tour_guide_availability 
        SET status = 'unavailable'
        WHERE available_date = CURDATE()
    ";
    
    $stmt = $conn->prepare($currentDateQuery);
    if (!$stmt) {
        throw new Exception('Failed to prepare current date query: ' . $conn->error);
    }
    
    $result = $stmt->execute();
    if (!$result) {
        throw new Exception('Failed to execute current date query: ' . $stmt->error);
    }
    
    $currentDateAffected = $stmt->affected_rows;
    echo "Set $currentDateAffected availability records to 'unavailable' for current date\n";
    
    // Display summary of availability status
    $summaryQuery = "
        SELECT 
            status,
            COUNT(*) as count,
            CASE 
                WHEN available_date = CURDATE() THEN 'Today'
                WHEN available_date > CURDATE() THEN 'Future'
                ELSE 'Past'
            END as date_type
        FROM tour_guide_availability 
        GROUP BY status, date_type
        ORDER BY date_type, status
    ";
    
    $result = $conn->query($summaryQuery);
    if ($result) {
        echo "\nAvailability Summary:\n";
        echo "=====================\n";
        while ($row = $result->fetch_assoc()) {
            echo "{$row['date_type']} - {$row['status']}: {$row['count']} records\n";
        }
    }
    
    echo "\nTour guide availability update completed successfully!\n";
    
    $stmt->close();
    closeDatabaseConnection($conn);
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    if (isset($conn)) {
        closeDatabaseConnection($conn);
    }
    exit(1);
}
?>
