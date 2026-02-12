<?php
session_start();

header('Content-Type: application/json');

require_once __DIR__ . '/../config/database.php';

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
$tourDate = trim($_POST['date'] ?? '');
$guests = intval($_POST['guests'] ?? 0);

if ($destination === '' || $tourDate === '' || $guests < 1) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Missing required booking fields']);
    exit;
}

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

try {
    // Simple INSERT statement matching the actual database structure
    $sql = "INSERT INTO bookings (user_id, tour_name, booking_date, number_of_people, total_amount, status) VALUES (?, ?, ?, ?, ?, 'pending')";

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception('Prepare failed: ' . $conn->error);
    }

    $stmt->bind_param(
        'isidi',
        $userId,
        $tourName,
        $tourDate,
        $guests,
        $totalAmount
    );

    if (!$stmt->execute()) {
        throw new Exception('Execute failed: ' . $stmt->error);
    }

    $bookingId = $stmt->insert_id;
    $stmt->close();

    echo json_encode([
        'success' => true,
        'message' => 'Booking submitted successfully',
        'booking_id' => $bookingId,
        'booking_reference' => $bookingReference
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
} finally {
    closeDatabaseConnection($conn);
}
