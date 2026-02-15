<?php
// Include database connection and authentication
require_once '../config/database.php';
require_once '../config/auth.php';

// Set content type to JSON
header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'User not logged in'
    ]);
    exit;
}

try {
    // Get current user ID
    $userId = $_SESSION['user_id'];
    
    // Connect to database
    $conn = getDatabaseConnection();
    
    if (!$conn) {
        echo json_encode([
            'success' => false,
            'message' => 'Database connection failed'
        ]);
        exit;
    }
    
    // Fetch booking history with tourist spot and guide information
    $query = "SELECT 
                b.id,
                b.destination_id,
                b.guide_id,
                b.booking_date,
                b.guests,
                b.total_price,
                b.status,
                b.created_at,
                b.notes,
                ts.name as destination_name,
                ts.category as destination_category,
                tg.name as guide_name,
                tg.specialty as guide_specialty,
                tg.rating as guide_rating,
                tg.verified as guide_verified
              FROM bookings b
              LEFT JOIN tourist_spots ts ON b.destination_id = ts.id
              LEFT JOIN tour_guides tg ON b.guide_id = tg.id
              WHERE b.user_id = ? 
              ORDER BY b.booking_date DESC, b.created_at DESC
              LIMIT 20";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $bookings = [];
    
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            // Format booking date
            $bookingDate = date('M j, Y', strtotime($row['booking_date']));
            
            // Format duration (you can customize this based on your data)
            $duration = 'Full day'; // Default value, you can modify based on actual data
            
            $booking = [
                'id' => $row['id'],
                'destination' => $row['destination_name'],
                'destination_name' => $row['destination_name'],
                'destination_category' => $row['destination_category'],
                'booking_date' => $bookingDate,
                'guests' => $row['guests'],
                'duration' => $duration,
                'total_price' => number_format($row['total_price'], 2),
                'status' => ucfirst($row['status']),
                'guide_name' => $row['guide_name'] ? htmlspecialchars($row['guide_name']) : 'Guide Assigned',
                'guide_specialty' => $row['guide_specialty'],
                'guide_rating' => $row['guide_rating'],
                'guide_verified' => $row['guide_verified'],
                'notes' => $row['notes'] ? htmlspecialchars($row['notes']) : '',
                'created_at' => date('M j, Y g:i A', strtotime($row['created_at']))
            ];
            
            $bookings[] = $booking;
        }
    }
    
    closeDatabaseConnection($conn);
    
    echo json_encode([
        'success' => true,
        'bookings' => $bookings,
        'count' => count($bookings)
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error fetching booking history: ' . $e->getMessage()
    ]);
}
?>
