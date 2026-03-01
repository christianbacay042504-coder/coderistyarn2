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
    
    // Get existing bookings for this guide in the specified month/year
    $bookedDates = [];
    $bookingsQuery = "SELECT DISTINCT DATE(booking_date) as booked_date 
                     FROM bookings 
                     WHERE guide_id = ? 
                       AND YEAR(booking_date) = ? 
                       AND MONTH(booking_date) = ?
                       AND status IN ('confirmed', 'pending')";
    
    $bookingsStmt = $conn->prepare($bookingsQuery);
    $bookingsStmt->bind_param('iii', $guideId, $year, $month);
    $bookingsStmt->execute();
    $bookingsResult = $bookingsStmt->get_result();
    
    while ($row = $bookingsResult->fetch_assoc()) {
        $bookedDates[] = $row['booked_date'];
    }
    $bookingsStmt->close();
    
    closeDatabaseConnection($conn);
    
    // Generate all days for the specified month and year
    $availability = [];
    $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);
    
    for ($day = 1; $day <= $daysInMonth; $day++) {
        $date = sprintf('%04d-%02d-%02d', $year, $month, $day);
        
        // Skip past dates and today
        $dateObj = new DateTime($date);
        $today = new DateTime('today');
        if ($dateObj <= $today) {
            continue;
        }
        
        // Check if this date is already booked
        if (in_array($date, $bookedDates)) {
            $availability[] = [
                'date' => $date,
                'status' => 'unavailable',
                'message' => 'Fully booked',
                'slots' => [],
                'total_slots' => 0,
                'available_slots' => 0
            ];
        } else {
            // Date is available
            $availability[] = [
                'date' => $date,
                'status' => 'available',
                'message' => 'Available',
                'slots' => [
                    [
                        'availability_id' => 0,
                        'start_time' => '09:00:00',
                        'end_time' => '17:00:00',
                        'status' => 'available'
                    ]
                ],
                'total_slots' => 1,
                'available_slots' => 1
            ];
        }
    }
    
    echo json_encode([
        'success' => true,
        'availability' => $availability,
        'guide_id' => $guideId,
        'year' => $year,
        'month' => $month
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error fetching availability: ' . $e->getMessage()
    ]);
}
?>
