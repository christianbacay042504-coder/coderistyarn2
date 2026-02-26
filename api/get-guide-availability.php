<?php
// API endpoint to get tour guide availability
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

// Include database configuration
require_once __DIR__ . '/../config/database.php';

// Get parameters
$guideId = $_GET['guide_id'] ?? '';
$year = $_GET['year'] ?? date('Y');
$month = $_GET['month'] ?? date('n');

// Validate parameters
if (empty($guideId) || !is_numeric($guideId)) {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid guide ID'
    ]);
    exit;
}

if (!is_numeric($year) || !is_numeric($month) || $month < 1 || $month > 12) {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid year or month'
    ]);
    exit;
}

try {
    $conn = getDatabaseConnection();
    if (!$conn) {
        throw new Exception('Database connection failed');
    }

    // Get availability slots for the specified month and year
    $query = "SELECT 
                DATE(available_date) as date,
                start_time,
                end_time,
                status,
                id as availability_id
              FROM tour_guide_availability 
              WHERE tour_guide_id = ? 
                AND YEAR(available_date) = ? 
                AND MONTH(available_date) = ?
              ORDER BY available_date, start_time";
    
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        throw new Exception('Query preparation failed');
    }
    
    $stmt->bind_param('iii', $guideId, $year, $month);
    $stmt->execute();
    $result = $stmt->get_result();
    
    // Group availability by date and calculate day-level status
    $dateGroups = [];
    while ($row = $result->fetch_assoc()) {
        $date = $row['date'];
        
        if (!isset($dateGroups[$date])) {
            $dateGroups[$date] = [
                'date' => $date,
                'slots' => [],
                'total_slots' => 0,
                'available_slots' => 0,
                'booked_slots' => 0,
                'unavailable_slots' => 0
            ];
        }
        
        $dateGroups[$date]['slots'][] = [
            'availability_id' => $row['availability_id'],
            'start_time' => $row['start_time'],
            'end_time' => $row['end_time'],
            'status' => $row['status']
        ];
        
        $dateGroups[$date]['total_slots']++;
        
        switch ($row['status']) {
            case 'available':
                $dateGroups[$date]['available_slots']++;
                break;
            case 'booked':
                $dateGroups[$date]['booked_slots']++;
                break;
            case 'unavailable':
                $dateGroups[$date]['unavailable_slots']++;
                break;
        }
    }
    
    // Convert to final availability array with day-level status
    $availability = [];
    foreach ($dateGroups as $dateData) {
        $dayStatus = 'available';
        $message = 'Available';
        
        if ($dateData['booked_slots'] > 0 && $dateData['available_slots'] === 0) {
            $dayStatus = 'unavailable';
            $message = 'Fully booked';
        } elseif ($dateData['booked_slots'] > 0 || $dateData['available_slots'] < $dateData['total_slots']) {
            $dayStatus = 'limited';
            $remainingSlots = $dateData['available_slots'];
            $message = $remainingSlots > 0 ? "Only $remainingSlots slot" . ($remainingSlots > 1 ? 's' : '') . ' left' : 'Limited availability';
        }
        
        $availability[] = [
            'date' => $dateData['date'],
            'status' => $dayStatus,
            'message' => $message,
            'slots' => $dateData['slots'],
            'total_slots' => $dateData['total_slots'],
            'available_slots' => $dateData['available_slots']
        ];
    }
    
    echo json_encode([
        'success' => true,
        'availability' => $availability,
        'guide_id' => $guideId,
        'year' => $year,
        'month' => $month
    ]);
    
    $stmt->close();
    closeDatabaseConnection($conn);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error fetching availability: ' . $e->getMessage()
    ]);
}
?>
