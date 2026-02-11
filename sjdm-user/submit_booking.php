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

$guideId = isset($_POST['guide_id']) && $_POST['guide_id'] !== '' ? intval($_POST['guide_id']) : null;
$destination = trim($_POST['destination'] ?? '');
$tourDate = trim($_POST['date'] ?? '');
$guests = intval($_POST['guests'] ?? 0);
$contact = trim($_POST['contact'] ?? '');
$email = trim($_POST['email'] ?? '');
$specialRequests = trim($_POST['special_requests'] ?? '');

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

// Validate guide_id (avoid FK constraint failures)
if ($guideId !== null) {
    try {
        $stmt = $conn->prepare('SELECT id FROM tour_guides WHERE id = ? LIMIT 1');
        if ($stmt) {
            $stmt->bind_param('i', $guideId);
            $stmt->execute();
            $res = $stmt->get_result();
            if (!$res || $res->num_rows === 0) {
                $guideId = null;
            }
            $stmt->close();
        } else {
            $guideId = null;
        }
    } catch (Exception $e) {
        $guideId = null;
    }
}

try {
    if ($guideId === null) {
        $sql = "INSERT INTO bookings (user_id, tour_name, destination, booking_date, number_of_people, contact_number, email, special_requests, total_amount, payment_method, status, booking_reference, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'pay_later', 'pending', ?, NOW())";

        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            throw new Exception('Prepare failed: ' . $conn->error);
        }

        $stmt->bind_param(
            'isssisssds',
            $userId,
            $tourName,
            $destination,
            $tourDate,
            $guests,
            $contact,
            $email,
            $specialRequests,
            $totalAmount,
            $bookingReference
        );
    } else {
        $sql = "INSERT INTO bookings (user_id, guide_id, tour_name, destination, booking_date, number_of_people, contact_number, email, special_requests, total_amount, payment_method, status, booking_reference, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pay_later', 'pending', ?, NOW())";

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
            $tourDate,
            $guests,
            $contact,
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
