<?php
// Process Booking Form
// This file handles the booking submission and stores it in the database

require_once '../config/database.php';

// Start session for user authentication
session_start();

// Set response header
header('Content-Type: application/json');

// Initialize response array
$response = [
    'success' => false,
    'message' => '',
    'booking_id' => null,
    'booking_reference' => null
];

try {
    // Check if user is logged in
    if (!isset($_SESSION['user_id'])) {
        throw new Exception('You must be logged in to make a booking.');
    }

    // Get POST data
    $postData = json_decode(file_get_contents('php://input'), true);
    
    if (!$postData) {
        throw new Exception('Invalid booking data received.');
    }

    // Validate required fields
    $requiredFields = ['guide_id', 'destination', 'check_in_date', 'check_out_date', 'guest_count', 'full_name', 'email', 'contact_number'];
    foreach ($requiredFields as $field) {
        if (empty($postData[$field])) {
            throw new Exception("Missing required field: $field");
        }
    }

    // Get database connection
    $conn = getDatabaseConnection();
    if (!$conn) {
        throw new Exception('Database connection failed.');
    }

    // Get user ID from session
    $userId = $_SESSION['user_id'];
    
    // Sanitize and prepare data
    $guideId = (int)$postData['guide_id'];
    $destination = $conn->real_escape_string($postData['destination']);
    $checkInDate = $conn->real_escape_string($postData['check_in_date']);
    $checkOutDate = $conn->real_escape_string($postData['check_out_date']);
    $guestCount = (int)$postData['guest_count'];
    $fullName = $conn->real_escape_string($postData['full_name']);
    $email = $conn->real_escape_string($postData['email']);
    $contactNumber = $conn->real_escape_string($postData['contact_number']);
    $specialRequests = isset($postData['special_requests']) ? $conn->real_escape_string($postData['special_requests']) : null;
    $paymentMethod = isset($postData['payment_method']) ? $conn->real_escape_string($postData['payment_method']) : 'pay_later';
    
    // Calculate total amount (base pricing)
    $guideFee = 2500.00;
    $entranceFees = 100.00 * $guestCount;
    $serviceFee = 200.00;
    $totalAmount = $guideFee + $entranceFees + $serviceFee;
    
    // Generate booking reference
    $bookingReference = 'SJDM-' . date('Y') . '-' . strtoupper(uniqid());
    
    // Get tour guide name for the tour_name field
    $guideQuery = "SELECT name FROM tour_guides WHERE id = $guideId";
    $guideResult = $conn->query($guideQuery);
    $tourName = 'SJDM Tour';
    if ($guideResult && $guideResult->num_rows > 0) {
        $guideData = $guideResult->fetch_assoc();
        $tourName = $guideData['name'] . ' - ' . $destination;
    }
    
    // Insert booking into database
    $insertQuery = "INSERT INTO bookings (
        user_id, 
        guide_id, 
        tour_name, 
        destination, 
        booking_date, 
        check_in_date, 
        check_out_date, 
        number_of_people, 
        contact_number, 
        email, 
        special_requests, 
        total_amount, 
        payment_method, 
        status, 
        booking_reference
    ) VALUES (
        ?, ?, ?, ?, CURDATE(), ?, ?, ?, ?, ?, ?, ?, ?, 'pending', ?
    )";
    
    $stmt = $conn->prepare($insertQuery);
    if (!$stmt) {
        throw new Exception('Failed to prepare booking statement.');
    }
    
    $stmt->bind_param(
        "iisssisssdsss",
        $userId,
        $guideId,
        $tourName,
        $destination,
        $checkInDate,
        $checkOutDate,
        $guestCount,
        $contactNumber,
        $email,
        $specialRequests,
        $totalAmount,
        $paymentMethod,
        $bookingReference
    );
    
    if (!$stmt->execute()) {
        throw new Exception('Failed to save booking: ' . $stmt->error);
    }
    
    $bookingId = $conn->insert_id;
    
    // Close statement and connection
    $stmt->close();
    $conn->close();
    
    // Set success response
    $response['success'] = true;
    $response['message'] = 'Booking submitted successfully! You will receive a confirmation email shortly.';
    $response['booking_id'] = $bookingId;
    $response['booking_reference'] = $bookingReference;
    
    // Log the booking for debugging
    error_log("New booking created: ID $bookingId, Reference: $bookingReference, User: $userId");
    
} catch (Exception $e) {
    // Log error
    error_log("Booking processing error: " . $e->getMessage());
    
    // Set error response
    $response['message'] = $e->getMessage();
    
    // Close connection if it exists
    if (isset($conn) && $conn) {
        $conn->close();
    }
}

// Send JSON response
echo json_encode($response);
?>
