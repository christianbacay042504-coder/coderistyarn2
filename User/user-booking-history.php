<?php
// Include database connection and authentication
require_once '../config/database.php';
require_once '../config/auth.php';

// OpenWeatherMap API configuration
$apiKey = '6c21a0d2aaf514cb8d21d56814312b19';
$weatherUrl = "https://api.openweathermap.org/data/2.5/weather?q=San%20Jose%20Del%20Monte,Bulacan&appid={$apiKey}&units=metric";

$weatherData = null;
$weatherError = null;
$currentTemp = '28';
$weatherLabel = 'Sunny';

// Fetch weather data
try {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $weatherUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    $weatherResponse = curl_exec($ch);
    
    if (curl_errno($ch)) {
        $weatherError = 'Weather API connection error';
    } else {
        $weatherData = json_decode($weatherResponse, true);
        if ($weatherData && isset($weatherData['main']) && isset($weatherData['weather'][0])) {
            $currentTemp = round($weatherData['main']['temp']);
            $weatherLabel = ucfirst($weatherData['weather'][0]['description']);
        } else {
            $weatherError = 'Weather data unavailable';
        }
    }
    curl_close($ch);
} catch (Exception $e) {
    $weatherError = 'Weather service unavailable';
}

// Get current date and weekday
$currentWeekday = date('l');
$currentDate = date('F Y');

$currentUser = [
    'name' => '',
    'email' => ''
];

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../log-in/log-in.php');
    exit();
}

// Get current user data
$conn = getDatabaseConnection();
if ($conn) {
    $stmt = $conn->prepare("SELECT first_name, last_name, email FROM users WHERE id = ?");
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $currentUser = [
            'name' => $user['first_name'] . ' ' . $user['last_name'],
            'email' => $user['email']
        ];
    }
}

// Handle cancel booking (DB)
$cancelResponse = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'cancel_booking') {
    header('Content-Type: application/json');

    $bookingId = intval($_POST['booking_id'] ?? 0);
    if ($bookingId < 1) {
        echo json_encode(['success' => false, 'message' => 'Invalid booking id']);
        closeDatabaseConnection($conn);
        exit;
    }

    try {
        $updateStmt = $conn->prepare("UPDATE bookings SET status = 'cancelled' WHERE id = ? AND user_id = ? AND status = 'pending'");
        $updateStmt->bind_param('ii', $bookingId, $_SESSION['user_id']);
        $updateStmt->execute();

        if ($updateStmt->affected_rows > 0) {
            echo json_encode(['success' => true, 'message' => 'Booking cancelled successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Unable to cancel booking']);
        }
        $updateStmt->close();
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Error cancelling booking']);
    }

    closeDatabaseConnection($conn);
    exit;
}

// Handle modify booking request (DB)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'modify_booking') {
    header('Content-Type: application/json');

    $bookingId = intval($_POST['booking_id'] ?? 0);
    $newDate = $_POST['new_date'] ?? '';
    $newGuests = intval($_POST['new_guests'] ?? 0);
    $guideRating = intval($_POST['guide_rating'] ?? 0);
    $guideReview = $_POST['guide_review'] ?? '';

    // Validate inputs
    if ($bookingId < 1) {
        echo json_encode(['success' => false, 'message' => 'Invalid booking ID']);
        closeDatabaseConnection($conn);
        exit;
    }

    if (empty($newDate) || $newGuests < 1 || $newGuests > 30) {
        echo json_encode(['success' => false, 'message' => 'Invalid date or guest count']);
        closeDatabaseConnection($conn);
        exit;
    }

    // Require rating for booking modification
    if ($guideRating < 1 || $guideRating > 5) {
        echo json_encode(['success' => false, 'message' => 'Please provide a rating for the tour guide (1-5 stars)']);
        closeDatabaseConnection($conn);
        exit;
    }

    try {
        // Check if booking exists and belongs to user (more flexible status check)
        $checkStmt = $conn->prepare("SELECT id, status FROM bookings WHERE id = ? AND user_id = ?");
        $checkStmt->bind_param('ii', $bookingId, $_SESSION['user_id']);
        $checkStmt->execute();
        $checkResult = $checkStmt->get_result();
        
        if ($checkResult->num_rows === 0) {
            echo json_encode(['success' => false, 'message' => 'Booking not found or does not belong to your account']);
            $checkStmt->close();
            closeDatabaseConnection($conn);
            exit;
        }
        
        $bookingData = $checkResult->fetch_assoc();
        $currentStatus = $bookingData['status'];
        $checkStmt->close();
        
        // Only allow modification for confirmed bookings (or add other statuses as needed)
        $allowedStatuses = ['confirmed', 'pending'];
        if (!in_array($currentStatus, $allowedStatuses)) {
            echo json_encode(['success' => false, 'message' => 'Booking cannot be modified. Current status: ' . $currentStatus]);
            closeDatabaseConnection($conn);
            exit;
        }

        // Calculate new total
        $guideFee = 2500;
        $entranceFee = $newGuests * 100;
        $platformFee = 100;
        $newTotal = $guideFee + $entranceFee + $platformFee;

        // Update booking (removed status requirement)
        $updateStmt = $conn->prepare("UPDATE bookings SET booking_date = ?, number_of_people = ?, total_amount = ? WHERE id = ? AND user_id = ?");
        $updateStmt->bind_param('sidii', $newDate, $newGuests, $newTotal, $bookingId, $_SESSION['user_id']);
        $updateStmt->execute();

        // Check if update was successful or if data was the same
        if ($updateStmt->affected_rows > 0) {
            // Data was actually changed
            if ($guideRating > 0) {
                try {
                    $reviewStmt = $conn->prepare("INSERT INTO guide_reviews (booking_id, user_id, guide_id, rating, review_text, created_at) VALUES (?, ?, (SELECT guide_id FROM bookings WHERE id = ?), ?, ?, NOW())");
                    if ($reviewStmt) {
                        $reviewStmt->bind_param('iiisi', $bookingId, $_SESSION['user_id'], $bookingId, $guideRating, $guideReview);
                        $reviewStmt->execute();
                        $reviewStmt->close();
                    }
                } catch (Exception $reviewEx) {
                    // Log error but don't fail the main booking update
                    error_log("Review save error: " . $reviewEx->getMessage());
                }
            }
            
            echo json_encode(['success' => true, 'message' => 'Booking modification request submitted successfully']);
        } else {
            // Check if the booking exists with the same data (no changes needed)
            $currentDataStmt = $conn->prepare("SELECT booking_date, number_of_people, total_amount FROM bookings WHERE id = ? AND user_id = ?");
            $currentDataStmt->bind_param('ii', $bookingId, $_SESSION['user_id']);
            $currentDataStmt->execute();
            $currentResult = $currentDataStmt->get_result();
            
            if ($currentResult->num_rows > 0) {
                $currentData = $currentResult->fetch_assoc();
                $currentDataStmt->close();
                
                // Check if the data is actually the same
                if ($currentData['booking_date'] === $newDate && 
                    $currentData['number_of_people'] == $newGuests && 
                    $currentData['total_amount'] == $newTotal) {
                    
                    // Save review even if no booking changes
                    if ($guideRating > 0) {
                        try {
                            $reviewStmt = $conn->prepare("INSERT INTO guide_reviews (booking_id, user_id, guide_id, rating, review_text, created_at) VALUES (?, ?, (SELECT guide_id FROM bookings WHERE id = ?), ?, ?, NOW())");
                            if ($reviewStmt) {
                                $reviewStmt->bind_param('iiisi', $bookingId, $_SESSION['user_id'], $bookingId, $guideRating, $guideReview);
                                $reviewStmt->execute();
                                $reviewStmt->close();
                            }
                        } catch (Exception $reviewEx) {
                            error_log("Review save error: " . $reviewEx->getMessage());
                        }
                    }
                    
                    echo json_encode(['success' => true, 'message' => 'No changes needed - booking already has the requested details']);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Unable to update booking. Please try again.']);
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'Booking not found']);
            }
        }
        $updateStmt->close();
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Error modifying booking: ' . $e->getMessage()]);
    }

    closeDatabaseConnection($conn);
    exit;
}

// Fetch bookings for this user
$userBookings = [];
if ($conn) {
    $bookingsSql = "SELECT b.id, b.booking_reference, b.tour_name, b.destination, b.booking_date, b.number_of_people, b.total_amount, b.status, tg.name AS guide_name
        FROM bookings b
        LEFT JOIN tour_guides tg ON b.guide_id = tg.id
        WHERE b.user_id = ?
        ORDER BY b.created_at DESC";

    $bookingsStmt = $conn->prepare($bookingsSql);
    if (!$bookingsStmt) {
        $bookingsSql = "SELECT b.id, b.booking_reference, b.tour_name, b.destination, b.booking_date, b.number_of_people, b.total_amount, b.status, tg.name AS guide_name
            FROM bookings b
            LEFT JOIN tour_guides tg ON b.guide_id = tg.id
            WHERE b.user_id = ?
            ORDER BY b.id DESC";
        $bookingsStmt = $conn->prepare($bookingsSql);
    }

    if ($bookingsStmt) {
        $bookingsStmt->bind_param('i', $_SESSION['user_id']);
        $bookingsStmt->execute();
        $bookingsResult = $bookingsStmt->get_result();
        while ($row = $bookingsResult->fetch_assoc()) {
            $userBookings[] = $row;
        }
        $bookingsStmt->close();
    }
    closeDatabaseConnection($conn);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking History - SJDM Tours</title>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="user-styles.css">
    
    <!-- Full-width layout and header styles -->
    <style>
        /* Full-width layout styles */
        .main-content.full-width {
            margin-left: 0;
            max-width: 100%;
        }

        .main-content.full-width .main-header {
            padding: 30px 40px;
            background: white;
            border-bottom: 1px solid var(--border);
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 40px;
        }

        .header-left {
            display: flex;
            align-items: center;
            gap: 30px;
        }

        .header-right {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .header-nav {
            display: flex;
            align-items: center;
            gap: 8px;
            background: var(--gray-50);
            padding: 4px;
            border-radius: 12px;
        }

        .nav-link {
            display: flex;
            align-items: center;
            gap: 6px;
            padding: 8px 16px;
            text-decoration: none;
            color: var(--text-secondary);
            font-weight: 500;
            font-size: 14px;
            border-radius: 8px;
            transition: all 0.2s ease;
        }

        .nav-link:hover {
            background: white;
            color: var(--primary);
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .nav-link.active {
            background: var(--primary);
            color: white;
        }

        .nav-link .material-icons-outlined {
            font-size: 18px;
        }

        /* ===== USER PROFILE DROPDOWN ===== */
        .user-profile-dropdown {
            position: relative;
            display: inline-block;
            z-index: 1000;
        }

        .profile-trigger {
            display: flex;
            align-items: center;
            gap: 8px;
            background: none;
            border: 1px solid rgba(251, 255, 253, 1);
            cursor: pointer;
            color: #333;
            font-weight: 600;
            padding: 6px 12px;
            border-radius: 20px;
            transition: background 0.2s;
            box-shadow: 5px 10px 10px rgba(0, 0, 0, 0.1);
        }

        .profile-trigger:hover {
            background: #f0f0f0;
        }

        .profile-avatar,
        .profile-avatar-large {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: #2c5f2d;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 14px;
            flex-shrink: 0;
        }

        .profile-avatar-large {
            width: 56px;
            height: 56px;
            font-size: 20px;
            margin: 0 auto 12px;
        }

        .profile-name {
            display: none;
        }

        .dropdown-menu {
            position: absolute;
            top: 100%;
            right: 0;
            margin-top: 8px;
            width: 240px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            overflow: hidden;
            z-index: 1001;
            opacity: 0;
            visibility: hidden;
            transform: translateY(-10px);
            transition: all 0.2s ease;
        }

        .dropdown-menu.show {
            opacity: 1 !important;
            visibility: visible !important;
            transform: translateY(0) !important;
        }

        .dropdown-header {
            padding: 16px;
            background: #f9f9f9;
            text-align: center;
            border-bottom: 1px solid #eee;
        }

        .dropdown-header h4 {
            margin: 8px 0 4px;
            font-size: 16px;
            color: #333;
        }

        .dropdown-header p {
            font-size: 13px;
            color: #777;
            margin: 0;
        }

        .dropdown-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 16px;
            text-decoration: none;
            color: #444;
            transition: background 0.2s;
        }

        .dropdown-item:hover {
            background: #f5f5f5;
        }

        .dropdown-item .material-icons-outlined {
            font-size: 20px;
            color: #555;
        }

        .dropdown-divider {
            height: 1px;
            background: #eee;
            margin: 4px 0;
        }

        .main-content.full-width .content-area {
            padding: 40px;
            max-width: 1400px;
            margin: 0 auto;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .mobile-menu-toggle {
                display: flex;
            }

            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.active {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
            }

            .main-content.full-width .main-header {
                padding: 20px;
                flex-direction: column;
                gap: 20px;
                align-items: stretch;
            }
            
            .header-left {
                flex-direction: column;
                gap: 15px;
                align-items: stretch;
            }
            
            .header-right {
                justify-content: center;
            }
            
            .header-nav {
                flex-wrap: wrap;
                justify-content: center;
                gap: 4px;
                padding: 6px;
            }
            
            .nav-link {
                padding: 6px 12px;
                font-size: 12px;
                gap: 4px;
            }
            
            .nav-link .material-icons-outlined {
                font-size: 16px;
            }

            .profile-name {
                display: inline-block;
                font-size: 14px;
            }

            .dropdown-menu {
                width: 280px;
            }
        }
    </style>
    
    <!-- Modal Styles -->
    <style>
        .modal-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.85);
            backdrop-filter: blur(8px);
            z-index: 9999;
            animation: fadeIn 0.3s ease;
        }

        .modal-overlay.show {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        .modal-content {
            background: white;
            border-radius: 24px;
            width: 90%;
            max-width: 600px;
            max-height: 90vh;
            overflow: hidden;
            box-shadow: 
                0 32px 64px rgba(0, 0, 0, 0.25),
                0 16px 32px rgba(0, 0, 0, 0.15),
                0 8px 16px rgba(0, 0, 0, 0.1);
            position: relative;
            border: 1px solid rgba(255, 255, 255, 0.2);
            animation: slideIn 0.3s ease;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-50px) scale(0.9);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 24px 32px 24px 32px;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: white;
            position: relative;
        }

        .modal-header h2 {
            margin: 0;
            font-size: 1.5rem;
            font-weight: 700;
            color: white;
        }

        .close-modal {
            background: rgba(255, 255, 255, 0.2);
            border: none;
            border-radius: 12px;
            width: 36px;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            color: white;
            font-size: 1.2rem;
            transition: all 0.3s ease;
        }

        .close-modal:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: scale(1.1);
        }

        .close-modal .material-icons-outlined {
            font-size: 20px;
        }

        .modal-body {
            padding: 32px;
            max-height: calc(90vh - 120px);
            overflow-y: auto;
        }

        .booking-details-content {
            display: grid;
            gap: 20px;
        }

        .booking-detail-row {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .booking-detail-row div {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .detail-item .material-icons-outlined {
            color: var(--primary);
            font-size: 20px;
        }

        .detail-item strong {
            color: var(--text-primary);
            font-weight: 600;
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
        }

        .status-badge.status-pending {
            background: linear-gradient(135deg, var(--warning) 0%, #d97706 100%);
            color: white;
        }

        .status-badge.status-confirmed {
            background: linear-gradient(135deg, var(--success) 0%, #059669 100%);
            color: white;
        }

        .status-badge.status-completed {
            background: linear-gradient(135deg, var(--info) 0%, #2563eb 100%);
            color: white;
        }

        .status-badge.status-cancelled {
            background: linear-gradient(135deg, var(--danger) 0%, #dc2626 100%);
            color: white;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .modal-content {
                width: 95%;
                margin: 20px;
            }
            
            .modal-header {
                padding: 20px 24px 20px 24px;
            }
            
            .modal-body {
                padding: 24px;
            }
        }

        /* Logout Modal Styles */
        .logout-message {
            text-align: center;
            margin-bottom: 20px;
        }

        .logout-icon {
            width: 48px;
            height: 48px;
            background: var(--danger);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 16px;
        }

        .logout-message h3 {
            margin: 16px 0 8px;
            color: var(--text-primary);
        }

        .logout-message p {
            color: var(--text-secondary);
            margin-bottom: 24px;
        }

        /* Override logout modal specific button styles */
        .logout-modal .btn-cancel,
        .logout-modal .btn-confirm-logout {
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
            white-space: nowrap;
        }

        .logout-modal .btn-cancel {
            background: var(--gray-100);
            color: var(--text-secondary);
        }

        .logout-modal .btn-cancel:hover {
            background: var(--gray-200);
        }

        .logout-modal .btn-confirm-logout {
            background: var(--danger);
            color: white;
        }

        .logout-modal .btn-confirm-logout:hover {
            background: #dc2626;
        }

        .modify-booking-modal .booking-summary h4 {
            margin: 0 0 16px 0;
            color: var(--text-primary);
            font-weight: 600;
        }

        .modify-booking-modal .summary-grid {
            display: grid;
            gap: 12px;
        }

        .modify-booking-modal .summary-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px 0;
        }

        .modify-booking-modal .summary-label {
            color: var(--text-secondary);
            font-weight: 500;
        }

        .modify-booking-modal .summary-value {
            color: var(--text-primary);
            font-weight: 600;
        }

        .modify-booking-modal .modify-form {
            display: grid;
            gap: 20px;
        }

        .modify-booking-modal .form-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .modify-booking-modal .form-group label {
            font-weight: 600;
            color: var(--text-primary);
        }

        .modify-booking-modal .form-group input,
        .modify-booking-modal .form-group textarea {
            padding: 12px 16px;
            border: 2px solid var(--border);
            border-radius: 8px;
            font-size: 14px;
            transition: border-color 0.2s;
        }

        .modify-booking-modal .form-group input:focus,
        .modify-booking-modal .form-group textarea:focus {
            outline: none;
            border-color: var(--primary);
        }

        .modify-booking-modal .price-estimate {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: white;
            padding: 20px;
            border-radius: 12px;
            margin-top: 20px;
        }

        .modify-booking-modal .estimate-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px 0;
        }

        .modify-booking-modal .estimate-divider {
            height: 1px;
            background: rgba(255, 255, 255, 0.3);
            margin: 12px 0;
        }

        .modify-booking-modal .estimate-item.total {
            font-weight: 700;
            font-size: 18px;
        }

        /* Guide Review Section in Modify Booking Modal */
        .modify-booking-modal .guide-review-section {
            background: var(--gray-50);
            padding: 20px;
            border-radius: 12px;
            border: 1px solid var(--border);
        }

        .modify-booking-modal .rating-section {
            margin-bottom: 16px;
        }

        .modify-booking-modal .rating-label {
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 12px;
            display: block;
        }

        .modify-booking-modal #modifyRatingStars {
            display: flex;
            gap: 8px;
            margin-bottom: 8px;
        }

        .modify-booking-modal #modifyRatingStars .star-btn {
            background: none;
            border: none;
            cursor: pointer;
            padding: 8px;
            border-radius: 8px;
            transition: all 0.2s;
        }

        .modify-booking-modal #modifyRatingStars .star-btn:hover {
            background: var(--gray-100);
        }

        .modify-booking-modal #modifyRatingStars .star-btn .material-icons-outlined {
            font-size: 28px;
            color: var(--gray-300);
            transition: color 0.2s;
        }

        .modify-booking-modal #modifyRatingStars .star-btn.active .material-icons-outlined {
            color: #ffc107;
        }

        .modify-booking-modal .review-text-section textarea {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid var(--border);
            border-radius: 8px;
            font-size: 14px;
            transition: border-color 0.2s;
            resize: vertical;
            min-height: 80px;
        }

        .modify-booking-modal .review-text-section textarea:focus {
            outline: none;
            border-color: var(--primary);
        }

        .review-modal .booking-info-review h4 {
            margin: 0 0 16px 0;
            color: var(--text-primary);
            font-weight: 600;
        }

        .review-modal .review-booking-details {
            display: grid;
            gap: 8px;
        }

        .review-modal .review-booking-details .detail-item {
            display: flex;
            align-items: center;
            gap: 8px;
            color: var(--text-secondary);
        }

        .review-modal .review-booking-details .material-icons-outlined {
            color: var(--primary);
            font-size: 18px;
        }

        .review-modal .review-form-content {
            display: grid;
            gap: 20px;
        }

        .review-modal .form-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .review-modal .form-group label {
            font-weight: 600;
            color: var(--text-primary);
        }

        .review-modal .form-group input,
        .review-modal .form-group textarea {
            padding: 12px 16px;
            border: 2px solid var(--border);
            border-radius: 8px;
            font-size: 14px;
            transition: border-color 0.2s;
        }

        .review-modal .form-group input:focus,
        .review-modal .form-group textarea:focus {
            outline: none;
            border-color: var(--primary);
        }

        .review-modal .rating-stars {
            display: flex;
            gap: 8px;
        }

        .review-modal .star-btn {
            background: none;
            border: none;
            cursor: pointer;
            padding: 8px;
            border-radius: 8px;
            transition: all 0.2s;
        }

        .review-modal .star-btn:hover {
            background: var(--gray-100);
        }

        .review-modal .star-btn .material-icons-outlined {
            font-size: 32px;
            color: var(--gray-300);
            transition: color 0.2s;
        }

        .review-modal .star-btn.active .material-icons-outlined {
            color: #ffc107;
        }

        .review-modal .recommend-options {
            display: flex;
            gap: 16px;
        }

        .review-modal .radio-label {
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            padding: 12px 16px;
            border: 2px solid var(--border);
            border-radius: 8px;
            transition: all 0.2s;
        }

        .review-modal .radio-label:hover {
            border-color: var(--primary);
        }

        .review-modal .radio-label input[type="radio"] {
            display: none;
        }

        .review-modal .radio-check {
            width: 20px;
            height: 20px;
            border: 2px solid var(--border);
            border-radius: 50%;
            position: relative;
            transition: all 0.2s;
        }

        .review-modal .radio-label input[type="radio"]:checked + .radio-check {
            border-color: var(--primary);
        }

        .review-modal .radio-label input[type="radio"]:checked + .radio-check::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 8px;
            height: 8px;
            background: var(--primary);
            border-radius: 50%;
        }

        .review-modal .radio-label input[type="radio"]:checked ~ span {
            color: var(--primary);
            font-weight: 600;
        }

        /* Modal Actions Styles */
        .modal-actions {
            display: flex;
            gap: 12px;
            justify-content: flex-end;
            margin-top: 16px;
            width: 100%;
        }

        .btn-cancel,
        .btn-confirm {
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
            white-space: nowrap;
        }

        .btn-cancel {
            background: var(--gray-100);
            color: var(--text-secondary);
        }

        .btn-cancel:hover {
            background: var(--gray-200);
        }

        .btn-confirm {
            background: var(--primary);
            color: white;
        }

        .btn-confirm:hover {
            background: var(--primary-dark);
        }

        /* Specific styles for different modal types */
        .logout-modal .modal-actions {
            justify-content: center;
            margin-top: 24px;
            padding-top: 20px;
            border-top: 1px solid var(--border);
        }

        .modify-booking-modal .modal-actions,
        .review-modal .modal-actions {
            justify-content: flex-end;
            margin-top: 16px;
            padding-top: 0;
            border-top: none;
        }
    </style>
</head>
<body>
    <!-- MAIN CONTENT -->
    <main class="main-content full-width">
        <header class="main-header">
            <div class="header-left">
                <h1 id="pageTitle">Booking History</h1>
                
            </div>
            <div class="header-right">
                <nav class="header-nav">
                    <a href="user-index.php" class="nav-link">
                        <span class="material-icons-outlined">dashboard</span>
                        <span>Dashboard</span>
                    </a>
                    <a href="user-guides-page.php" class="nav-link">
                        <span class="material-icons-outlined">people</span>
                        <span>Tour Guides</span>
                    </a>
                    <a href="user-book.php" class="nav-link">
                        <span class="material-icons-outlined">event</span>
                        <span>Book Now</span>
                    </a>
                    <a href="user-booking-history.php" class="nav-link">
                        <span class="material-icons-outlined">history</span>
                        <span>Booking History</span>
                    </a>
                    <a href="user-tourist-spots.php" class="nav-link">
                        <span class="material-icons-outlined">place</span>
                        <span>Tourist Spots</span>
                    </a>
                    <a href="user-local-culture.php" class="nav-link">
                        <span class="material-icons-outlined">theater_comedy</span>
                        <span>Local Culture</span>
                    </a>
                    <a href="user-travel-tips.php" class="nav-link">
                        <span class="material-icons-outlined">tips_and_updates</span>
                        <span>Travel Tips</span>
                    </a>
                </nav>
                <div class="header-actions">
                    <div class="user-profile-dropdown">
                        <button class="profile-trigger">
                            <div class="profile-avatar"><?php echo isset($currentUser['name']) ? strtoupper(substr($currentUser['name'], 0, 1)) : 'U'; ?></div>
                            <span class="profile-name"><?php echo htmlspecialchars($currentUser['name']); ?></span>
                            <span class="material-icons-outlined">expand_more</span>
                        </button>
                        <div class="dropdown-menu">
                            <div class="dropdown-header">
                                <div class="profile-avatar large"><?php echo isset($currentUser['name']) ? strtoupper(substr($currentUser['name'], 0, 1)) : 'U'; ?></div>
                                <div class="profile-details">
                                    <h4><?php echo htmlspecialchars($currentUser['name']); ?></h4>
                                    <p><?php echo htmlspecialchars($currentUser['email']); ?></p>
                                </div>
                            </div>
                            <div class="dropdown-divider"></div>
                            <a href="user-index.php" class="dropdown-item">
                                <span class="material-icons-outlined">dashboard</span>
                                <span>Dashboard</span>
                            </a>
                            <a href="user-booking-history.php" class="dropdown-item">
                                <span class="material-icons-outlined">history</span>
                                <span>Booking History</span>
                            </a>
                            <a href="user-saved-tours.php" class="dropdown-item">
                                <span class="material-icons-outlined">favorite</span>
                                <span>Saved Tours</span>
                            </a>
                            <div class="dropdown-divider"></div>
                            <a href="user-logout.php" class="dropdown-item">
                                <span class="material-icons-outlined">logout</span>
                                <span>Sign Out</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <div class="content-area booking-history-page">
            <!-- Page Header -->
            <div class="page-header-section">
                <div class="page-header-content">
                    <h2 class="page-title">My Booking History</h2>
                    <p class="page-subtitle">View and manage all your tour bookings in one place</p>
                </div>
                
                <!-- Filter Tabs -->
                <div class="booking-filter-tabs">
                    <button class="filter-tab active" data-filter="all">
                        <span class="material-icons-outlined">list_alt</span>
                        <span>All Bookings</span>
                    </button>
                    <button class="filter-tab" data-filter="pending">
                        <span class="material-icons-outlined">schedule</span>
                        <span>Pending</span>
                    </button>
                    <button class="filter-tab" data-filter="confirmed">
                        <span class="material-icons-outlined">check_circle</span>
                        <span>Confirmed</span>
                    </button>
                    <button class="filter-tab" data-filter="completed">
                        <span class="material-icons-outlined">verified</span>
                        <span>Completed</span>
                    </button>
                    <button class="filter-tab" data-filter="cancelled">
                        <span class="material-icons-outlined">cancel</span>
                        <span>Cancelled</span>
                    </button>
                </div>
            </div>

            <!-- Calendar and Weather Info -->
            <div class="info-cards-row">
                <div class="info-card calendar-card">
                    <div class="info-card-icon">
                        <span class="material-icons-outlined">calendar_today</span>
                    </div>
                    <div class="info-card-content">
                        <div class="info-label">Today's Date</div>
                        <div class="info-value"><?php echo htmlspecialchars($currentWeekday); ?></div>
                        <div class="info-secondary"><?php echo htmlspecialchars($currentDate); ?></div>
                    </div>
                </div>
                
                <div class="info-card weather-card">
                    <div class="info-card-icon">
                        <span class="material-icons-outlined"><?php echo $weatherLabel === 'Clear' ? 'wb_sunny' : ($weatherLabel === 'Clouds' ? 'cloud' : 'wb_cloudy'); ?></span>
                    </div>
                    <div class="info-card-content">
                        <div class="info-label">Current Weather</div>
                        <div class="info-value"><?php echo $currentTemp; ?>°C</div>
                        <div class="info-secondary"><?php echo htmlspecialchars($weatherLabel); ?></div>
                    </div>
                </div>
            </div>

            <!-- Bookings List -->
            <div id="bookingsList" class="bookings-container"></div>
        </div>
    </main>

    <!-- Modal Container -->
    <div id="modalContainer"></div>

    <script>
        let currentFilter = 'all';
        const userBookings = <?php echo json_encode($userBookings); ?>;
        const currentUserId = <?php echo (int)$_SESSION['user_id']; ?>;
        function showNotification(message, type) {
            console.log('Notification:', type, message);
            if (type === 'error') {
                alert(message);
            }
        }
        
        window.addEventListener('DOMContentLoaded', function() {
            console.log('Booking History currentUserId:', currentUserId);
            console.log('Booking History DB bookings:', userBookings);
            displayUserBookings();
            initFilterTabs();
            initSearch();
            initUserProfileDropdown();
        });

        // ========== USER PROFILE DROPDOWN ==========
        function initUserProfileDropdown() {
            const profileDropdown = document.querySelector('.user-profile-dropdown');
            const profileTrigger = document.querySelector('.profile-trigger');
            const dropdownMenu = document.querySelector('.dropdown-menu');
            const logoutLink = document.querySelector('[href="logout.php"]');

            if (!profileDropdown || !profileTrigger || !dropdownMenu) {
                console.log('Profile dropdown elements not found');
                return;
            }

            // Toggle dropdown on click
            profileTrigger.addEventListener('click', function (e) {
                e.preventDefault();
                e.stopPropagation();
                dropdownMenu.classList.toggle('show');
            });

            // Close dropdown when clicking outside
            document.addEventListener('click', function (e) {
                if (!profileDropdown.contains(e.target)) {
                    dropdownMenu.classList.remove('show');
                }
            });

            // Handle logout with confirmation
            if (logoutLink) {
                logoutLink.addEventListener('click', function (e) {
                    e.preventDefault();
                    e.stopPropagation();
                    showLogoutConfirmation();
                });
            }
        }

        // Show logout confirmation modal
        function showLogoutConfirmation() {
            const modal = document.createElement('div');
            modal.className = 'modal-overlay';
            modal.innerHTML = `
                <div class="modal-content logout-modal">
                    <div class="modal-header">
                        <h2>Sign Out</h2>
                        <button class="close-modal" onclick="this.closest('.modal-overlay').remove()">
                            <span class="material-icons-outlined">close</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="logout-message">
                            <div class="logout-icon">
                                <span class="material-icons-outlined">logout</span>
                            </div>
                            <h3>Confirm Sign Out</h3>
                            <p>Are you sure you want to sign out of your account?</p>
                        </div>
                        <div class="modal-actions">
                            <button class="btn-cancel" onclick="document.querySelector('.modal-overlay').remove()">
                                <span class="material-icons-outlined">close</span>
                                Cancel
                            </button>
                            <button class="btn-confirm-logout" onclick="confirmLogout()">
                                <span class="material-icons-outlined">logout</span>
                                Sign Out
                            </button>
                        </div>
                    </div>
                </div>
            `;
            document.body.appendChild(modal);
            setTimeout(() => modal.classList.add('show'), 10);
        }

        // Confirm and execute logout
        function confirmLogout() {
            // Remove modal
            const modal = document.querySelector('.modal-overlay');
            if (modal) {
                modal.remove();
            }

            // Redirect to logout script
            window.location.href = 'logout.php';
        }

        function initFilterTabs() {
            const filterTabs = document.querySelectorAll('.filter-tab');
            if (!filterTabs) return;
            filterTabs.forEach(tab => {
                tab.addEventListener('click', function() {
                    filterTabs.forEach(t => t.classList.remove('active'));
                    this.classList.add('active');
                    currentFilter = this.dataset.filter;
                    displayUserBookings();
                });
            });
        }

        function initSearch() {
            const searchInput = document.getElementById('searchInput');
            if (searchInput) {
                searchInput.addEventListener('input', function(e) {
                    const searchTerm = e.target.value.toLowerCase();
                    filterBookings(searchTerm);
                });
            }
        }

        function filterBookings(searchTerm) {
            const bookingCards = document.querySelectorAll('.booking-card');
            if (!bookingCards) return;
            bookingCards.forEach(card => {
                const text = card.textContent.toLowerCase();
                if (text.includes(searchTerm)) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        }

        function displayUserBookings() {
            const container = document.getElementById('bookingsList');
            if (!container) return;
            
            // Filter bookings based on current filter
            let filteredBookings = userBookings;
            if (currentFilter !== 'all') {
                filteredBookings = userBookings.filter(b => b.status === currentFilter);
            }
            
            if (filteredBookings.length === 0) {
                container.innerHTML = `
                    <div class="empty-state-card">
                        <div class="empty-state-icon">
                            <span class="material-icons-outlined">event_busy</span>
                        </div>
                        <h3 class="empty-state-title">No ${currentFilter !== 'all' ? currentFilter : ''} bookings found</h3>
                        <p class="empty-state-text">
                            ${currentFilter === 'all' 
                                ? 'Start your adventure by booking your first tour with our experienced guides.' 
                                : `You don't have any ${currentFilter} bookings at the moment.`}
                        </p>
                        <button class="btn-primary-action" onclick="window.location.href='user-index.php#guides'">
                            <span class="material-icons-outlined">explore</span>
                            <span>Browse Tour Guides</span>
                        </button>
                    </div>
                `;
                return;
            }
            
            container.innerHTML = filteredBookings.map(booking => `
                <div class="booking-card" data-status="${booking.status}">
                    <div class="booking-card-header">
                        <div class="booking-primary-info">
                            <div class="booking-icon">
                                <span class="material-icons-outlined">tour</span>
                            </div>
                            <div class="booking-title-section">
                                <h3 class="booking-title">${booking.guide_name || 'Tour Guide'}</h3>
                                <p class="booking-destination">
                                    <span class="material-icons-outlined">place</span>
                                    ${booking.destination || booking.tour_name}
                                </p>
                            </div>
                        </div>
                        <span class="status-badge status-${booking.status}">
                            ${getStatusIcon(booking.status)}
                            <span>${booking.status.toUpperCase()}</span>
                        </span>
                    </div>
                    
                    <div class="booking-card-divider"></div>
                    
                    <div class="booking-details-grid">
                        <div class="detail-item">
                            <div class="detail-icon">
                                <span class="material-icons-outlined">event</span>
                            </div>
                            <div class="detail-content">
                                <div class="detail-label">Tour Date</div>
                                <div class="detail-value">${formatDate(booking.booking_date)}</div>
                            </div>
                        </div>
                        
                        <div class="detail-item">
                            <div class="detail-icon">
                                <span class="material-icons-outlined">people</span>
                            </div>
                            <div class="detail-content">
                                <div class="detail-label">Number of Guests</div>
                                <div class="detail-value">${booking.number_of_people} Guest${booking.number_of_people > 1 ? 's' : ''}</div>
                            </div>
                        </div>
                        
                        <div class="detail-item">
                            <div class="detail-icon">
                                <span class="material-icons-outlined">confirmation_number</span>
                            </div>
                            <div class="detail-content">
                                <div class="detail-label">Booking Reference</div>
                                <div class="detail-value">${booking.booking_reference || ('#' + booking.id)}</div>
                            </div>
                        </div>
                        
                        <div class="detail-item highlight">
                            <div class="detail-icon">
                                <span class="material-icons-outlined">payments</span>
                            </div>
                            <div class="detail-content">
                                <div class="detail-label">Total Amount</div>
                                <div class="detail-value price">₱${Number(booking.total_amount).toLocaleString()}</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="booking-card-divider"></div>
                    
                    <div class="booking-actions-row">
                        ${booking.status === 'pending' ? `
                            <button class="btn-action btn-cancel" onclick="cancelBooking(${booking.id})">
                                <span class="material-icons-outlined">cancel</span>
                                <span>Cancel Booking</span>
                            </button>
                        ` : ''}
                        
                        ${booking.status === 'completed' ? `
                            <button class="btn-action btn-review" onclick="showReviewModal(${booking.id})">
                                <span class="material-icons-outlined">rate_review</span>
                                <span>Write a Review</span>
                            </button>
                        ` : ''}
                        ${booking.status === 'confirmed' ? `
                            <button class="btn-action btn-modify" onclick="modifyBooking(${booking.id})">
                                <span class="material-icons-outlined">edit</span>
                                <span>Modify Booking</span>
                            </button>
                        ` : ''}
                        <button class="btn-action btn-view" onclick="viewBookingDetails(${booking.id})">
                            <span class="material-icons-outlined">visibility</span>
                            <span>View Details</span>
                        </button>
                        <button class="btn-action btn-download" onclick="downloadBooking(${booking.id})">
                            <span class="material-icons-outlined">download</span>
                            <span>Download</span>
                        </button>
                    </div>
                </div>
            `).join('');
        }

        function getStatusIcon(status) {
            const icons = {
                'pending': '<span class="material-icons-outlined">schedule</span>',
                'confirmed': '<span class="material-icons-outlined">check_circle</span>',
                'completed': '<span class="material-icons-outlined">verified</span>',
                'cancelled': '<span class="material-icons-outlined">cancel</span>'
            };
            return icons[status] || '<span class="material-icons-outlined">info</span>';
        }

        function formatDate(dateString) {
            const date = new Date(dateString);
            const options = { weekday: 'short', month: 'short', day: 'numeric', year: 'numeric' };
            return date.toLocaleDateString('en-US', options);
        }

        function cancelBooking(bookingId) {
            if (!confirm('Are you sure you want to cancel this booking? This action cannot be undone.')) return;

            fetch('', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: `action=cancel_booking&booking_id=${bookingId}`
            })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        showNotification(data.message, 'info');
                        const booking = userBookings.find(b => String(b.id) === String(bookingId));
                        if (booking) booking.status = 'cancelled';
                        displayUserBookings();
                    } else {
                        showNotification(data.message, 'error');
                    }
                })
                .catch(() => {
                    showNotification('Unable to cancel booking', 'error');
                });
        }

        function modifyBooking(bookingId) {
            const booking = userBookings.find(b => String(b.id) === String(bookingId));
            
            if (!booking) return;
            
            const content = `
                <div class="modify-booking-form">
                    <div class="booking-summary">
                        <h4>Current Booking Details</h4>
                        <div class="summary-grid">
                            <div class="summary-item">
                                <span class="summary-label">Reference:</span>
                                <span class="summary-value">${booking.booking_reference || ('#' + booking.id)}</span>
                            </div>
                            <div class="summary-item">
                                <span class="summary-label">Guide:</span>
                                <span class="summary-value">${booking.guide_name || 'Tour Guide'}</span>
                            </div>
                            <div class="summary-item">
                                <span class="summary-label">Destination:</span>
                                <span class="summary-value">${booking.destination || booking.tour_name}</span>
                            </div>
                        </div>
                    </div>
                    
                    <form id="modifyBookingForm" class="modify-form">
                        <input type="hidden" name="booking_id" value="${booking.id}">
                        <div class="form-group">
                            <label for="modifyDate">New Tour Date *</label>
                            <input type="date" id="modifyDate" name="new_date" required 
                                   min="${new Date().toISOString().split('T')[0]}"
                                   value="${booking.booking_date}">
                        </div>
                        
                        <div class="form-group">
                            <label for="modifyGuests">Number of Guests *</label>
                            <input type="number" id="modifyGuests" name="new_guests" 
                                   min="1" max="30" required value="${booking.number_of_people}">
                        </div>
                        
                        <div class="form-group">
                            <label>Review for the Tour Guide <span style="color: var(--danger);">*</span></label>
                            <div class="guide-review-section">
                                <div class="rating-section">
                                    <label class="rating-label">How was your experience with ${booking.guide_name || 'this guide'}? <span style="color: var(--danger);">*</span></label>
                                    <div class="rating-stars" id="modifyRatingStars">
                                        ${[1,2,3,4,5].map(star => `
                                            <button type="button" class="star-btn" data-rating="${star}" onclick="setModifyRating(${star})">
                                                <span class="material-icons-outlined">star_border</span>
                                            </button>
                                        `).join('')}
                                    </div>
                                    <input type="hidden" id="modifyRatingValue" name="guide_rating" value="0">
                                </div>
                                <div class="review-text-section">
                                    <textarea id="modifyReviewText" name="guide_review" rows="3" 
                                              placeholder="Share your experience with this tour guide (optional)..."></textarea>
                                </div>
                            </div>
                        </div>
                        
                        <div class="price-estimate">
                            <div class="estimate-item">
                                <span>Guide Fee:</span>
                                <span>₱2,500.00</span>
                            </div>
                            <div class="estimate-item">
                                <span>Entrance Fees:</span>
                                <span id="entranceEstimate">₱${Number(booking.number_of_people * 100).toLocaleString()}.00</span>
                            </div>
                            <div class="estimate-item">
                                <span>Platform Fee:</span>
                                <span>₱100.00</span>
                            </div>
                            <div class="estimate-divider"></div>
                            <div class="estimate-item total">
                                <strong>Estimated Total:</strong>
                                <strong id="totalEstimate">₱${Number(booking.total_amount).toLocaleString()}.00</strong>
                            </div>
                        </div>
                    </form>
                </div>
            `;
            
            createModal('modifyBookingModal', 'Modify Booking', content, 'modify-booking-modal');
            
            // Update price estimate when guest count changes
            setTimeout(() => {
                const guestsInput = document.getElementById('modifyGuests');
                if (guestsInput) {
                    guestsInput.addEventListener('input', updatePriceEstimate);
                }
            }, 100);
        }
        
        function updatePriceEstimate() {
            const guests = parseInt(document.getElementById('modifyGuests').value) || 1;
            const entranceFee = guests * 100;
            const total = 2500 + entranceFee + 100;
            
            const entranceEl = document.getElementById('entranceEstimate');
            const totalEl = document.getElementById('totalEstimate');
            
            if (entranceEl) entranceEl.textContent = `₱${entranceFee.toLocaleString()}.00`;
            if (totalEl) totalEl.textContent = `₱${total.toLocaleString()}.00`;
        }
        
        function showReviewModal(bookingId) {
            const booking = userBookings.find(b => String(b.id) === String(bookingId));
            
            if (!booking) return;
            
            const content = `
                <div class="review-form">
                    <div class="booking-info-review">
                        <h4>Tour Experience</h4>
                        <div class="review-booking-details">
                            <div class="detail-item">
                                <span class="material-icons-outlined">tour</span>
                                <span>${booking.guide_name || 'Tour Guide'} - ${booking.destination || booking.tour_name}</span>
                            </div>
                            <div class="detail-item">
                                <span class="material-icons-outlined">event</span>
                                <span>${formatDate(booking.booking_date)}</span>
                            </div>
                        </div>
                    </div>
                    
                    <form id="reviewForm" class="review-form-content">
                        <div class="form-group">
                            <label>Overall Rating *</label>
                            <div class="rating-stars" id="ratingStars">
                                ${[1,2,3,4,5].map(star => `
                                    <button type="button" class="star-btn" data-rating="${star}" onclick="setRating(${star})">
                                        <span class="material-icons-outlined">star</span>
                                    </button>
                                `).join('')}
                            </div>
                            <input type="hidden" id="ratingValue" name="rating" value="0" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="reviewTitle">Review Title *</label>
                            <input type="text" id="reviewTitle" name="review_title" required 
                                   placeholder="Summarize your experience in a few words">
                        </div>
                        
                        <div class="form-group">
                            <label for="reviewText">Your Review *</label>
                            <textarea id="reviewText" name="review_text" rows="4" required 
                                      placeholder="Share your experience with this tour guide... What did you like? What could be improved?"></textarea>
                        </div>
                        
                        <div class="form-group">
                            <label>Would you recommend this guide?</label>
                            <div class="recommend-options">
                                <label class="radio-label">
                                    <input type="radio" name="recommend" value="yes" required>
                                    <span class="radio-check"></span>
                                    <span>Yes, I would recommend</span>
                                </label>
                                <label class="radio-label">
                                    <input type="radio" name="recommend" value="no" required>
                                    <span class="radio-check"></span>
                                    <span>No, I would not recommend</span>
                                </label>
                            </div>
                        </div>
                    </form>
                </div>
            `;
            
            createModal('reviewModal', 'Write a Review', content, 'review-modal');
        }
        
        function setRating(rating) {
            const stars = document.querySelectorAll('.star-btn');
            const ratingInput = document.getElementById('ratingValue');
            
            stars.forEach((star, index) => {
                if (index < rating) {
                    star.classList.add('active');
                    star.querySelector('.material-icons-outlined').textContent = 'star';
                } else {
                    star.classList.remove('active');
                    star.querySelector('.material-icons-outlined').textContent = 'star_border';
                }
            });
            
            ratingInput.value = rating;
        }
        
        function setModifyRating(rating) {
            const stars = document.querySelectorAll('#modifyRatingStars .star-btn');
            const ratingInput = document.getElementById('modifyRatingValue');
            
            stars.forEach((star, index) => {
                if (index < rating) {
                    star.classList.add('active');
                    star.querySelector('.material-icons-outlined').textContent = 'star';
                } else {
                    star.classList.remove('active');
                    star.querySelector('.material-icons-outlined').textContent = 'star_border';
                }
            });
            
            ratingInput.value = rating;
        }
        
        function submitReview() {
            const form = document.getElementById('reviewForm');
            if (!form) return;
            
            const rating = document.getElementById('ratingValue').value;
            if (rating === '0') {
                showNotification('Please select a rating', 'error');
                return;
            }
            
            const formData = new FormData(form);
            formData.append('action', 'submit_review');
            formData.append('booking_id', userBookings.find(b => b.id === parseInt(formData.get('booking_id')))?.id);
            
            fetch('', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    showNotification('Review submitted successfully! Thank you for your feedback.', 'success');
                    document.querySelector('.modal-overlay').remove();
                } else {
                    showNotification(data.message || 'Failed to submit review', 'error');
                }
            })
            .catch(() => {
                showNotification('Error submitting review', 'error');
            });
        }

        function submitModifyBooking() {
            const form = document.getElementById('modifyBookingForm');
            if (!form) return;
            
            // Validate rating is selected
            const rating = document.getElementById('modifyRatingValue').value;
            if (rating < 1 || rating > 5) {
                showNotification('Please provide a rating for the tour guide (1-5 stars)', 'error');
                return;
            }
            
            const formData = new FormData(form);
            formData.append('action', 'modify_booking');
            
            fetch('', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    showNotification(data.message, 'success');
                    document.querySelector('.modal-overlay').remove();
                    // Refresh bookings list
                    setTimeout(() => location.reload(), 1500);
                } else {
                    showNotification(data.message || 'Failed to submit modification request', 'error');
                }
            })
            .catch(() => {
                showNotification('Error submitting modification request', 'error');
            });
        }

        function viewBookingDetails(bookingId) {
            const booking = userBookings.find(b => String(b.id) === String(bookingId));
            
            if (!booking) return;
            
            const content = `
                <div class="booking-details-modal">
                    <p><strong>Tour Guide:</strong> ${booking.guide_name || 'Tour Guide'}</p>
                    <p><strong>Destination:</strong> ${booking.destination || booking.tour_name}</p>
                    <p><strong>Tour Date:</strong> ${formatDate(booking.booking_date)}</p>
                    <p><strong>Guests:</strong> ${booking.number_of_people}</p>
                    <p><strong>Ref #:</strong> ${booking.booking_reference || ('#' + booking.id)}</p>
                    <p><strong>Status:</strong> ${booking.status.toUpperCase()}</p>
                    <p><strong>Total:</strong> ₱${Number(booking.total_amount).toLocaleString()}</p>
                </div>
            `;
            
            if (typeof createModal === 'function') {
                createModal('bookingDetailsModal', 'Booking Details', content, 'description');
            } else {
                console.log(booking);
                alert('Booking details in console');
            }
        }

        function downloadBooking(bookingId) {
            showNotification('Download feature coming soon!', 'info');
        }

        // Modal creation function
        function createModal(modalId, title, content, modalClass = '') {
            const modal = document.createElement('div');
            modal.className = 'modal-overlay';
            
            // For modify booking modal, we need to place buttons inside the price estimate section
            if (modalClass === 'modify-booking-modal') {
                // Find the price estimate section and insert buttons before its closing div
                const modifiedContent = content.replace(
                    /(<\/div>\s*<\/form>\s*<\/div>)/,
                    `</div>
                        <div class="modal-actions">
                            <button class="btn-cancel" onclick="this.closest('.modal-overlay').remove()">
                                <span class="material-icons-outlined">close</span>
                                Cancel
                            </button>
                            <button class="btn-confirm" onclick="submitModifyBooking()">
                                <span class="material-icons-outlined">send</span>
                                Submit Request
                            </button>
                        </div>
                    $1`
                );
                
                modal.innerHTML = `
                    <div class="modal-content ${modalClass}">
                        <div class="modal-header">
                            <h2>${title}</h2>
                            <button class="close-modal" onclick="this.closest('.modal-overlay').remove()">
                                <span class="material-icons-outlined">close</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            ${modifiedContent}
                        </div>
                    </div>
                `;
            } else {
                // Handle other modals normally
                let actionButtons = '';
                if (modalClass === 'review-modal') {
                    actionButtons = `
                        <div class="modal-actions">
                            <button class="btn-cancel" onclick="this.closest('.modal-overlay').remove()">
                                <span class="material-icons-outlined">close</span>
                                Cancel
                            </button>
                            <button class="btn-confirm" onclick="submitReview()">
                                <span class="material-icons-outlined">rate_review</span>
                                Submit Review
                            </button>
                        </div>
                    `;
                }
                
                modal.innerHTML = `
                    <div class="modal-content ${modalClass}">
                        <div class="modal-header">
                            <h2>${title}</h2>
                            <button class="close-modal" onclick="this.closest('.modal-overlay').remove()">
                                <span class="material-icons-outlined">close</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            ${content}
                            ${actionButtons}
                        </div>
                    </div>
                `;
            }
            
            document.body.appendChild(modal);
            setTimeout(() => modal.classList.add('show'), 10);
            
            // Close modal when clicking outside
            modal.addEventListener('click', function(e) {
                if (e.target === modal) {
                    modal.remove();
                }
            });
        }

        // Listen for modal requests from parent window
        window.addEventListener('message', function(event) {
            if (event.data.type === 'showBookingModal') {
                createModal('bookingDetailsModal', 'Booking Details', event.data.content, 'booking-details-modal');
            }
        });

        // Check if there's a modal request in URL parameters
        const urlParams = new URLSearchParams(window.location.search);
        const showBookingId = urlParams.get('show');
        const bookingId = urlParams.get('booking');
        
        if (showBookingId && bookingId) {
            const booking = userBookings.find(b => String(b.id) === bookingId);
            if (booking) {
                const content = `
                    <div class="booking-details-content">
                        <div class="booking-detail-row">
                            <div class="detail-item">
                                <span class="material-icons-outlined">tour</span>
                                <strong>Tour Guide:</strong> ${booking.guide_name || 'Tour Guide'}
                            </div>
                        </div>
                        <div class="booking-detail-row">
                            <div class="detail-item">
                                <span class="material-icons-outlined">place</span>
                                <strong>Destination:</strong> ${booking.destination || booking.tour_name}
                            </div>
                        </div>
                        <div class="booking-detail-row">
                            <div class="detail-item">
                                <span class="material-icons-outlined">event</span>
                                <strong>Tour Date:</strong> ${formatDate(booking.booking_date)}
                            </div>
                        </div>
                        <div class="booking-detail-row">
                            <div class="detail-item">
                                <span class="material-icons-outlined">people</span>
                                <strong>Guests:</strong> ${booking.number_of_people}
                            </div>
                        </div>
                        <div class="booking-detail-row">
                            <div class="detail-item">
                                <span class="material-icons-outlined">confirmation_number</span>
                                <strong>Reference:</strong> ${booking.booking_reference || ('#' + booking.id)}
                            </div>
                        </div>
                        <div class="booking-detail-row">
                            <div class="detail-item">
                                <span class="material-icons-outlined">payments</span>
                                <strong>Total Amount:</strong> ₱${Number(booking.total_amount).toLocaleString()}
                            </div>
                        </div>
                        <div class="booking-detail-row">
                            <div class="detail-item">
                                <span class="material-icons-outlined">info</span>
                                <strong>Status:</strong> 
                                <span class="status-badge status-${booking.status.toLowerCase()}">
                                    ${getStatusIcon(booking.status)}
                                    <span>${booking.status.toUpperCase()}</span>
                                </span>
                            </div>
                        </div>
                    </div>
                `;
                
                createModal('bookingDetailsModal', 'Booking Details', content, 'booking-details-modal');
            }
        }
    </script>
</body>
</html>