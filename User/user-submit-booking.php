<?php
session_start();

header('Content-Type: application/json');

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$userId = intval($_SESSION['user_id']);

$destination = trim($_POST['destination'] ?? '');
$guideId = intval($_POST['guide_id'] ?? 0);
$tourDate = trim($_POST['date'] ?? '');
$guests = intval($_POST['guests'] ?? 0);
$contactNumber = trim($_POST['contact'] ?? '');
$email = trim($_POST['email'] ?? '');
$specialRequests = trim($_POST['special_requests'] ?? '');

if ($destination === '' || $tourDate === '' || $guests < 1) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Missing required booking fields']);
    exit;
}

// Validate and format the date
$dateObj = DateTime::createFromFormat('Y-m-d', $tourDate);
if (!$dateObj) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid date format. Please use YYYY-MM-DD format.']);
    exit;
}
$formattedDate = $dateObj->format('Y-m-d');

$bookingReference = 'SJDM-' . substr((string)time(), -8);
$tourName = $destination;

$guideFee = 2500;
$entranceFee = 100 * $guests;
$serviceFee = 300;
$totalAmount = $guideFee + $entranceFee + $serviceFee;

$conn = getDatabaseConnection();
if (!$conn) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit;
}

// Validate guide_id exists in tour_guides table (if provided)
if ($guideId > 0) {
    $checkGuideSql = "SELECT id FROM tour_guides WHERE id = ? AND status = 'active'";
    $checkStmt = $conn->prepare($checkGuideSql);
    if ($checkStmt) {
        $checkStmt->bind_param('i', $guideId);
        $checkStmt->execute();
        $checkResult = $checkStmt->get_result();
        if ($checkResult->num_rows === 0) {
            // Guide doesn't exist, set to NULL instead of failing
            $guideId = null;
            error_log("Invalid guide_id $guideId provided, setting to NULL for booking reference: $bookingReference");
        }
        $checkStmt->close();
    } else {
        // If query fails, set guide to NULL to allow booking to proceed
        $guideId = null;
        error_log("Guide validation query failed, setting guide_id to NULL for booking reference: $bookingReference");
    }
}

try {
    // Modify SQL based on whether guide_id is provided
    if ($guideId === null) {
        $sql = "INSERT INTO bookings (user_id, guide_id, tour_name, destination, booking_date, number_of_people, contact_number, email, special_requests, total_amount, booking_reference, status) VALUES (?, NULL, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending')";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            throw new Exception('Prepare failed: ' . $conn->error);
        }
        $stmt->bind_param(
            'isssssssds',
            $userId,
            $tourName,
            $destination,
            $formattedDate,
            $guests,
            $contactNumber,
            $email,
            $specialRequests,
            $totalAmount,
            $bookingReference
        );
    } else {
        $sql = "INSERT INTO bookings (user_id, guide_id, tour_name, destination, booking_date, number_of_people, contact_number, email, special_requests, total_amount, booking_reference, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending')";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            throw new Exception('Prepare failed: ' . $conn->error);
        }
        $stmt->bind_param(
            'iisssisssds',
            $userId,
            $guideId,
            $tourName,
            $destination,
            $formattedDate,
            $guests,
            $contactNumber,
            $email,
            $specialRequests,
            $totalAmount,
            $bookingReference
        );
    }

    if (!$stmt->execute()) {
        throw new Exception('Execute failed: ' . $stmt->error);
    }

    $bookingId = $stmt->insert_id;
    $stmt->close();

    // Prepare booking data for email
    $bookingData = [
        'booking_reference' => $bookingReference,
        'destination' => $destination,
        'tour_date' => $formattedDate,
        'guests' => $guests,
        'contact_number' => $contactNumber,
        'special_requests' => $specialRequests,
        'guide_fee' => $guideFee,
        'entrance_fee' => $entranceFee,
        'service_fee' => $serviceFee,
        'total_amount' => $totalAmount
    ];

    // Send booking confirmation email
    $emailResult = sendBookingConfirmationEmail($email, $bookingData);
    
    if (!$emailResult['success']) {
        // Log the email error but don't fail the booking
        error_log("Booking confirmation email failed: " . $emailResult['message']);
    }

    echo json_encode([
        'success' => true,
        'message' => 'Booking submitted successfully',
        'booking_id' => $bookingId,
        'booking_reference' => $bookingReference,
        'email_sent' => $emailResult['success']
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
} finally {
    closeDatabaseConnection($conn);
}
