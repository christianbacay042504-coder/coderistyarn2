<?php
// Include database connection and authentication
require_once '../config/database.php';
require_once '../config/auth.php';

// OpenWeatherMap API configuration
$apiKey = '6c21a0d2aaf514cb8d21d56814312b19';
$weatherUrl = "https://api.openweathermap.org/data/2.5/weather?q=San%20Jose%20Del%20Monte,Bulacan&appid={$apiKey}&units=metric";
$weatherData = null;
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
        }
    }
    curl_close($ch);
} catch (Exception $e) {
    $weatherError = 'Weather service unavailable';
}

// Get current date and weekday
$currentWeekday = date('l');
$currentDate = date('F Y');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../log-in/log-in.php');
    exit();
}

// Get current user data
$conn = getDatabaseConnection();
$currentUser = ['name' => '', 'email' => ''];

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
    $stmt->close();
}

// Handle cancel booking (DB)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'cancel_booking') {
    header('Content-Type: application/json');
    $bookingId = intval($_POST['booking_id'] ?? 0);
    if ($bookingId < 1) {
        echo json_encode(['success' => false, 'message' => 'Invalid booking id']);
        if ($conn) closeDatabaseConnection($conn);
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
    if ($conn) closeDatabaseConnection($conn);
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

    if ($bookingId < 1 || empty($newDate) || $newGuests < 1 || $newGuests > 30) {
        echo json_encode(['success' => false, 'message' => 'Invalid inputs']);
        if ($conn) closeDatabaseConnection($conn);
        exit;
    }

    try {
        $guideFee = 2500;
        $entranceFee = $newGuests * 100;
        $platformFee = 100;
        $newTotal = $guideFee + $entranceFee + $platformFee;

        $updateStmt = $conn->prepare("UPDATE bookings SET booking_date = ?, number_of_people = ?, total_amount = ? WHERE id = ? AND user_id = ?");
        $updateStmt->bind_param('sidii', $newDate, $newGuests, $newTotal, $bookingId, $_SESSION['user_id']);
        $updateStmt->execute();

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

        echo json_encode(['success' => true, 'message' => 'Booking modification submitted successfully']);
        $updateStmt->close();
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }
    if ($conn) closeDatabaseConnection($conn);
    exit;
}

// Fetch bookings for this user
$userBookings = [];
if ($conn) {
    $bookingsSql = "SELECT b.id, b.booking_reference, b.tour_name, b.destination, b.booking_date, b.number_of_people, b.total_amount, b.status, b.guide_id, tg.name AS guide_name
    FROM bookings b
    LEFT JOIN tour_guides tg ON b.guide_id = tg.id
    WHERE b.user_id = ?
    ORDER BY b.created_at DESC";
    $bookingsStmt = $conn->prepare($bookingsSql);
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
    <title>Booking History - San Jose del Monte Bulacan</title>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,500;0,700;1,400;1,500&family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;1,9..40,300&family=Cormorant+Garamond:ital,wght@0,300;0,400;0,600;1,300;1,400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="user-styles.css">
    <style>
        :root {
            /* Page palette - matching tourist spots */
            --pg-bg:     #f4f1eb;
            --pg-ink:    #1a1a18;
            --pg-forest: #1e3a1f;
            --pg-sage:   #4a7c4e;
            --pg-mint:   #b5d4b8;
            --pg-cream:  #faf8f3;
            --pg-sand:   #e8e2d6;
            --pg-warm:   #c8b89a;
            --pg-gold:   #c9a85c;
            --pg-mist:   #f0ede6;
            
            /* Modal palette */
            --m-forest:  #1e3a1f;
            --m-sage:    #4a7c4e;
            --m-mint:    #b5d4b8;
            --m-cream:   #f7f4ef;
            --m-sand:    #ede8df;
            --m-warm:    #c8b89a;
            --m-ink:     #1a1814;
            --m-mist:    #f0ede8;
            --m-gold:    #c9a85c;
        }
        
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body {
            font-family: 'DM Sans', system-ui, sans-serif;
            background: var(--pg-bg);
            color: var(--pg-ink);
            line-height: 1.6;
            min-height: 100vh;
        }
        
        /* ══════════════════════════
        HEADER - Matching Tourist Spots
        ══════════════════════════ */
        .main-content.full-width { margin-left: 0; max-width: 100%; display: block; }
        .main-content.full-width .main-header {
            padding: 0 48px;
            background: var(--pg-forest);
            border-bottom: none;
            box-shadow: none;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 32px;
            height: 68px;
            position: sticky;
            top: 0;
            z-index: 900;
        }
        
        .main-content.full-width .main-header h1 { display: none; }
        
        .header-left { display: flex; align-items: center; gap: 0; }
        .header-right { display: flex; align-items: center; gap: 4px; }
        
        /* Logo */
        .header-left .logo {
            display: flex !important;
            align-items: center;
            gap: 10px;
            margin-right: 40px !important;
            text-decoration: none;
        }
        .header-left .logo img {
            height: 32px !important;
            width: 32px !important;
            border-radius: 6px !important;
            filter: brightness(1.1);
        }
        .header-left .logo span {
            font-family: 'Playfair Display', serif !important;
            font-size: 17px !important;
            font-weight: 700 !important;
            color: #fff !important;
            letter-spacing: 0.04em;
        }
        
        /* Nav links */
        .header-nav {
            display: flex;
            align-items: center;
            gap: 0;
            background: none !important;
            padding: 0 !important;
            border-radius: 0 !important;
        }
        .nav-link {
            display: flex;
            align-items: center;
            gap: 5px;
            padding: 8px 14px;
            text-decoration: none;
            color: rgba(255,255,255,0.62) !important;
            font-weight: 400;
            font-size: 13px;
            border-radius: 0 !important;
            transition: color 0.18s;
            border-bottom: 2px solid transparent;
            height: 68px;
            letter-spacing: 0.01em;
        }
        .nav-link:hover {
            background: none !important;
            color: rgba(255,255,255,0.9) !important;
            box-shadow: none !important;
        }
        .nav-link.active {
            background: none !important;
            color: #fff !important;
            border-bottom-color: var(--pg-gold);
        }
        .nav-link .material-icons-outlined {
            font-size: 16px;
            opacity: 0.8;
        }
        
        /* Sign in button */
        .btn-signin {
            background: rgba(255,255,255,0.12);
            color: #fff;
            border: 1px solid rgba(255,255,255,0.2);
            padding: 9px 20px;
            border-radius: 100px;
            font-weight: 500;
            font-size: 13px;
            cursor: pointer;
            transition: all 0.2s;
            letter-spacing: 0.02em;
        }
        .btn-signin:hover {
            background: rgba(255,255,255,0.22);
            transform: none;
            box-shadow: none;
        }
        
        /* ── Profile Dropdown ── */
        .user-profile-dropdown { position: relative; display: inline-block; z-index: 1000; }
        .profile-trigger {
            display: flex; align-items: center; gap: 8px;
            background: rgba(255,255,255,0.1);
            border: 1px solid rgba(255,255,255,0.18);
            cursor: pointer; color: #fff;
            font-weight: 500; font-size: 13px;
            padding: 7px 14px 7px 8px;
            border-radius: 100px;
            transition: background 0.2s;
            box-shadow: none;
        }
        .profile-trigger:hover { background: rgba(255,255,255,0.18); }
        .profile-avatar {
            width: 28px; height: 28px; border-radius: 50%;
            background: var(--pg-gold); color: #fff;
            display: flex; align-items: center; justify-content: center;
            font-weight: 700; font-size: 12px; flex-shrink: 0;
        }
        .profile-avatar-large {
            width: 56px; height: 56px; font-size: 20px; margin: 0 auto 12px;
            border-radius: 50%; background: var(--pg-sage); color: white;
            display: flex; align-items: center; justify-content: center; font-weight: bold;
        }
        .profile-name { display: none; }
        .dropdown-menu {
            position: absolute; top: calc(100% + 10px); right: 0;
            width: 240px; background: white;
            border-radius: 16px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.15), 0 2px 8px rgba(0,0,0,0.08);
            overflow: hidden; z-index: 1001;
            opacity: 0; visibility: hidden;
            transform: translateY(-8px);
            transition: all 0.2s cubic-bezier(0.22,1,0.36,1);
        }
        .dropdown-menu.show { opacity: 1 !important; visibility: visible !important; transform: translateY(0) !important; }
        .dropdown-header { padding: 20px 16px 14px; background: var(--pg-mist); text-align: center; border-bottom: 1px solid var(--pg-sand); }
        .dropdown-header h4 { margin: 8px 0 4px; font-size: 15px; color: var(--pg-ink); font-family: 'Playfair Display', serif; }
        .dropdown-header p { font-size: 12px; color: #999; margin: 0; }
        .dropdown-item { display: flex; align-items: center; gap: 12px; padding: 11px 16px; text-decoration: none; color: #444; transition: background 0.15s; font-size: 13.5px; }
        .dropdown-item:hover { background: var(--pg-mist); }
        .dropdown-item .material-icons-outlined { font-size: 18px; color: var(--pg-sage); }
        .dropdown-divider { height: 1px; background: var(--pg-sand); margin: 4px 0; }
        
        .main-content.full-width .content-area {
            padding: 0;
            max-width: 100%;
            margin: 0 auto;
            display: block;
        }
        
        /* ══════════════════════════
        HERO SECTION
        ══════════════════════════ */
        .hero-section {
            position: relative;
            height: 380px;
            display: flex;
            flex-direction: column;
            justify-content: flex-end;
            align-items: flex-start;
            padding: 0 72px 48px;
            overflow: hidden;
            margin-bottom: 0;
            border-radius: 0;
            background:
                linear-gradient(to bottom, rgba(15,25,16,0.25) 0%, rgba(15,25,16,0.1) 40%, rgba(15,25,16,0.75) 100%),
                url('https://images.unsplash.com/photo-1469474968028-56623f02e42e?q=80&w=2070&auto=format&fit=crop') center/cover no-repeat;
            background-attachment: fixed;
            text-align: left;
            color: white;
        }
        .hero-section::before {
            content: '';
            position: absolute; inset: 0;
            background: linear-gradient(135deg, rgba(30,58,31,0.3) 0%, transparent 60%);
            pointer-events: none;
            z-index: 0;
        }
        .hero-section::after { display: none; }
        .hero-section h1 {
            font-family: 'Playfair Display', serif;
            font-size: 3.5rem;
            font-weight: 500;
            letter-spacing: -0.02em;
            line-height: 1.05;
            color: #fff;
            margin-bottom: 12px;
            background: none;
            -webkit-text-fill-color: #fff;
            text-shadow: 0 2px 24px rgba(0,0,0,0.22);
            animation: heroFadeUp 0.9s cubic-bezier(0.22,1,0.36,1) both;
            position: relative; z-index: 2;
            max-width: 640px;
        }
        .hero-section p {
            font-family: 'DM Sans', sans-serif;
            font-size: 1.05rem;
            font-weight: 300;
            color: rgba(255,255,255,0.8);
            max-width: 480px;
            margin: 0;
            line-height: 1.65;
            text-shadow: 0 1px 8px rgba(0,0,0,0.2);
            animation: heroFadeUp 0.9s 0.12s cubic-bezier(0.22,1,0.36,1) both;
            position: relative; z-index: 2;
        }
        @keyframes heroFadeUp {
            from { opacity:0; transform:translateY(24px); }
            to   { opacity:1; transform:translateY(0); }
        }
        
        /* Hero stat pills */
        .hero-pills {
            display: flex; align-items: center; gap: 10px;
            position: relative; z-index: 2;
            animation: heroFadeUp 0.9s 0.22s cubic-bezier(0.22,1,0.36,1) both;
            margin-top: 24px;
        }
        .hero-pill {
            display: flex; align-items: center; gap: 6px;
            background: rgba(255,255,255,0.12);
            backdrop-filter: blur(10px); -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.2);
            color: rgba(255,255,255,0.9);
            padding: 8px 16px;
            border-radius: 100px;
            font-family: 'DM Sans', sans-serif;
            font-size: 12.5px; font-weight: 500;
            letter-spacing: 0.02em;
        }
        .hero-pill .material-icons-outlined { font-size: 14px; opacity: 0.8; }
        
        /* ══════════════════════════
        CONTENT WRAPPER
        ══════════════════════════ */
        .page-inner {
            max-width: 1200px;
            margin: 0 auto;
            padding: 48px 48px 80px;
        }
        
        /* ══════════════════════════
        INFO CARDS (Weather & Date)
        ══════════════════════════ */
        .info-cards-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }
        
        .info-card {
            background: var(--pg-cream);
            border-radius: 16px;
            padding: 24px;
            border: 1px solid var(--pg-sand);
            display: flex;
            align-items: center;
            gap: 16px;
            transition: all 0.2s;
        }
        .info-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(0,0,0,0.06);
        }
        
        .info-card-icon {
            width: 48px;
            height: 48px;
            background: var(--pg-mist);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .info-card-icon .material-icons-outlined {
            font-size: 24px;
            color: var(--pg-sage);
        }
        
        .info-card-content {
            flex: 1;
        }
        
        .info-label {
            font-size: 11px;
            font-weight: 500;
            color: #999;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            margin-bottom: 4px;
        }
        
        .info-value {
            font-family: 'Playfair Display', serif;
            font-size: 1.3rem;
            font-weight: 500;
            color: var(--pg-forest);
            line-height: 1.2;
        }
        
        .info-secondary {
            font-size: 12px;
            color: #666;
            margin-top: 2px;
        }
        
        /* ══════════════════════════
        FILTER TABS
        ══════════════════════════ */
        .booking-filter-tabs {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            margin-bottom: 32px;
            padding-bottom: 20px;
            border-bottom: 1px solid var(--pg-sand);
        }
        
        .filter-tab {
            display: flex;
            align-items: center;
            gap: 6px;
            padding: 10px 18px;
            background: var(--pg-mist);
            border: 1px solid var(--pg-sand);
            border-radius: 100px;
            font-family: 'DM Sans', sans-serif;
            font-size: 12.5px;
            font-weight: 500;
            color: var(--pg-ink);
            cursor: pointer;
            transition: all 0.2s;
        }
        .filter-tab:hover {
            background: var(--pg-sand);
            transform: translateY(-1px);
        }
        .filter-tab.active {
            background: var(--pg-forest);
            color: #fff;
            border-color: var(--pg-forest);
        }
        .filter-tab .material-icons-outlined {
            font-size: 16px;
        }
        
        /* ══════════════════════════
        BOOKINGS CONTAINER
        ══════════════════════════ */
        .bookings-container {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }
        
        .booking-card {
            background: var(--pg-cream);
            border-radius: 20px;
            overflow: hidden;
            border: 1px solid var(--pg-sand);
            transition: all 0.28s cubic-bezier(0.22,1,0.36,1);
        }
        .booking-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 16px 48px rgba(30,58,31,0.12), 0 4px 12px rgba(0,0,0,0.04);
        }
        
        .booking-card-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            padding: 24px 28px;
            border-bottom: 1px solid var(--pg-sand);
            background: linear-gradient(135deg, var(--pg-cream) 0%, var(--pg-mist) 100%);
        }
        
        .booking-primary-info {
            display: flex;
            align-items: flex-start;
            gap: 16px;
        }
        
        .booking-icon {
            width: 48px;
            height: 48px;
            background: var(--pg-sage);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .booking-icon .material-icons-outlined {
            font-size: 24px;
            color: #fff;
        }
        
        .booking-title-section h3 {
            font-family: 'Playfair Display', serif;
            font-size: 1.3rem;
            font-weight: 500;
            color: var(--pg-forest);
            margin-bottom: 6px;
            line-height: 1.25;
        }
        
        .booking-destination {
            display: flex;
            align-items: center;
            gap: 4px;
            font-size: 13px;
            color: #666;
        }
        .booking-destination .material-icons-outlined {
            font-size: 14px;
            color: var(--pg-sage);
        }
        
        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 6px 14px;
            border-radius: 100px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        .status-badge.status-pending {
            background: linear-gradient(135deg, var(--pg-warm) 0%, #b89a6a 100%);
            color: #fff;
        }
        .status-badge.status-confirmed {
            background: linear-gradient(135deg, var(--pg-sage) 0%, var(--pg-forest) 100%);
            color: #fff;
        }
        .status-badge.status-completed {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            color: #fff;
        }
        .status-badge.status-cancelled {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            color: #fff;
        }
        .status-badge .material-icons-outlined {
            font-size: 14px;
        }
        
        .booking-card-divider {
            height: 1px;
            background: var(--pg-sand);
            margin: 0 28px;
        }
        
        .booking-details-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 16px;
            padding: 24px 28px;
        }
        
        .detail-item {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            padding: 14px 16px;
            background: var(--pg-mist);
            border-radius: 12px;
            border: 1px solid var(--pg-sand);
        }
        .detail-item.highlight {
            background: linear-gradient(135deg, rgba(201,168,92,0.1) 0%, rgba(200,184,154,0.08) 100%);
            border-color: var(--pg-warm);
        }
        
        .detail-icon {
            width: 36px;
            height: 36px;
            background: var(--pg-sage);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .detail-icon .material-icons-outlined {
            font-size: 18px;
            color: #fff;
        }
        
        .detail-content {
            flex: 1;
        }
        
        .detail-label {
            font-size: 10px;
            font-weight: 500;
            color: #999;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            margin-bottom: 4px;
        }
        
        .detail-value {
            font-family: 'Playfair Display', serif;
            font-size: 0.95rem;
            font-weight: 500;
            color: var(--pg-ink);
        }
        .detail-value.price {
            font-size: 1.1rem;
            color: var(--pg-forest);
        }
        
        .booking-actions-row {
            display: flex;
            gap: 10px;
            padding: 20px 28px 24px;
            flex-wrap: wrap;
            border-top: 1px solid var(--pg-sand);
            background: linear-gradient(135deg, var(--pg-cream) 0%, var(--pg-mist) 100%);
        }
        
        .btn-action {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 10px 16px;
            background: var(--pg-mist);
            border: 1px solid var(--pg-sand);
            border-radius: 10px;
            font-family: 'DM Sans', sans-serif;
            font-size: 12px;
            font-weight: 500;
            color: var(--pg-ink);
            cursor: pointer;
            transition: all 0.2s;
        }
        .btn-action:hover {
            background: var(--pg-sand);
            transform: translateY(-1px);
        }
        .btn-action .material-icons-outlined {
            font-size: 16px;
        }
        .btn-action.btn-cancel {
            background: rgba(239,68,68,0.1);
            border-color: rgba(239,68,68,0.2);
            color: #dc2626;
        }
        .btn-action.btn-cancel:hover {
            background: rgba(239,68,68,0.15);
        }
        .btn-action.btn-review {
            background: rgba(59,130,246,0.1);
            border-color: rgba(59,130,246,0.2);
            color: #2563eb;
        }
        .btn-action.btn-review:hover {
            background: rgba(59,130,246,0.15);
        }
        .btn-action.btn-modify {
            background: rgba(74,124,78,0.1);
            border-color: rgba(74,124,78,0.2);
            color: var(--pg-sage);
        }
        .btn-action.btn-modify:hover {
            background: rgba(74,124,78,0.15);
        }
        
        /* Empty State */
        .empty-state-card {
            background: var(--pg-cream);
            border-radius: 20px;
            padding: 60px 40px;
            text-align: center;
            border: 1px solid var(--pg-sand);
        }
        .empty-state-icon {
            width: 64px;
            height: 64px;
            background: var(--pg-mist);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 24px;
        }
        .empty-state-icon .material-icons-outlined {
            font-size: 32px;
            color: var(--pg-sage);
        }
        .empty-state-title {
            font-family: 'Playfair Display', serif;
            font-size: 1.4rem;
            font-weight: 500;
            color: var(--pg-forest);
            margin-bottom: 12px;
        }
        .empty-state-text {
            font-size: 14px;
            color: #666;
            margin-bottom: 24px;
            max-width: 400px;
            margin-left: auto;
            margin-right: auto;
        }
        .btn-primary-action {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 24px;
            background: var(--pg-forest);
            color: #fff;
            border: none;
            border-radius: 12px;
            font-family: 'DM Sans', sans-serif;
            font-size: 13px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
        }
        .btn-primary-action:hover {
            background: var(--pg-sage);
            transform: translateY(-1px);
        }
        
        /* ══════════════════════════
        MODAL STYLES
        ══════════════════════════ */
        .modal-overlay {
            display: none;
            position: fixed;
            inset: 0;
            z-index: 9999;
            background: rgba(20,18,14,0.72);
            backdrop-filter: blur(6px);
            -webkit-backdrop-filter: blur(6px);
            align-items: center;
            justify-content: center;
            padding: 24px;
        }
        .modal-overlay.show {
            display: flex;
            animation: mOverlayIn 0.25s ease;
        }
        @keyframes mOverlayIn { from{opacity:0} to{opacity:1} }
        
        .modal-content {
            background: #fff;
            border-radius: 20px;
            width: 100%;
            max-width: 600px;
            max-height: 90vh;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0,0,0,0.04), 0 12px 40px rgba(0,0,0,0.18), 0 32px 80px rgba(0,0,0,0.12);
            animation: mShellIn 0.32s cubic-bezier(0.22,1,0.36,1);
            position: relative;
        }
        @keyframes mShellIn { from{opacity:0;transform:translateY(28px)} to{opacity:1;transform:translateY(0)} }
        
        .modal-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 20px 28px;
            border-bottom: 1px solid var(--m-sand);
            background: var(--m-mist);
        }
        .modal-header h2 {
            font-family: 'Playfair Display', serif;
            font-size: 1.4rem;
            font-weight: 500;
            color: var(--m-ink);
            margin: 0;
        }
        .close-modal {
            width: 36px;
            height: 36px;
            background: rgba(255,255,255,0.8);
            border: 1px solid var(--m-sand);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s;
        }
        .close-modal:hover {
            background: #fff;
            transform: scale(1.08);
        }
        .close-modal .material-icons-outlined {
            font-size: 18px;
            color: var(--m-ink);
        }
        
        .modal-body {
            padding: 28px;
            max-height: calc(90vh - 120px);
            overflow-y: auto;
        }
        .modal-body::-webkit-scrollbar { width: 4px; }
        .modal-body::-webkit-scrollbar-thumb { background: var(--m-sand); border-radius: 4px; }
        
        /* Modal Actions */
        .modal-actions {
            display: flex;
            gap: 12px;
            justify-content: flex-end;
            margin-top: 24px;
            padding-top: 24px;
            border-top: 1px solid var(--m-sand);
        }
        .btn-cancel, .btn-confirm {
            padding: 12px 24px;
            border: none;
            border-radius: 12px;
            font-family: 'DM Sans', sans-serif;
            font-size: 13px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .btn-cancel {
            background: var(--m-mist);
            color: var(--m-ink);
            border: 1px solid var(--m-sand);
        }
        .btn-cancel:hover {
            background: var(--m-sand);
        }
        .btn-confirm {
            background: var(--m-forest);
            color: #fff;
        }
        .btn-confirm:hover {
            background: var(--m-sage);
            transform: translateY(-1px);
        }
        
        /* Logout Modal */
        .logout-message { text-align: center; margin-bottom: 20px; }
        .logout-icon {
            width: 48px; height: 48px;
            background: #ef4444; color: white;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 16px;
        }
        .logout-message h3 {
            font-family: 'Playfair Display', serif;
            margin: 16px 0 8px;
            color: var(--m-ink);
        }
        .logout-message p { color: #666; margin-bottom: 24px; }
        .logout-modal .modal-actions { justify-content: center; }
        
        /* Modify Booking Modal */
        .modify-booking-modal .booking-summary {
            background: var(--m-mist);
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 24px;
            border: 1px solid var(--m-sand);
        }
        .modify-booking-modal .booking-summary h4 {
            font-family: 'Playfair Display', serif;
            font-size: 1.1rem;
            color: var(--m-forest);
            margin-bottom: 16px;
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
            border-bottom: 1px solid var(--m-sand);
        }
        .modify-booking-modal .summary-item:last-child { border-bottom: none; }
        .modify-booking-modal .summary-label {
            font-size: 12px;
            color: #666;
            font-weight: 500;
        }
        .modify-booking-modal .summary-value {
            font-family: 'Playfair Display', serif;
            font-size: 13px;
            color: var(--m-ink);
            font-weight: 500;
        }
        .modify-booking-modal .form-group {
            margin-bottom: 20px;
        }
        .modify-booking-modal .form-group label {
            display: block;
            font-size: 13px;
            font-weight: 500;
            color: var(--m-ink);
            margin-bottom: 8px;
        }
        .modify-booking-modal .form-group input,
        .modify-booking-modal .form-group textarea {
            width: 100%;
            padding: 12px 16px;
            border: 1.5px solid var(--m-sand);
            border-radius: 10px;
            font-family: 'DM Sans', sans-serif;
            font-size: 14px;
            color: var(--m-ink);
            background: #fff;
            transition: all 0.2s;
            outline: none;
        }
        .modify-booking-modal .form-group input:focus,
        .modify-booking-modal .form-group textarea:focus {
            border-color: var(--m-sage);
            box-shadow: 0 0 0 3px rgba(74,124,78,0.12);
        }
        .modify-booking-modal .price-estimate {
            background: linear-gradient(135deg, var(--m-forest) 0%, var(--m-sage) 100%);
            color: #fff;
            padding: 20px;
            border-radius: 12px;
            margin-top: 20px;
        }
        .modify-booking-modal .estimate-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px 0;
            font-size: 13px;
        }
        .modify-booking-modal .estimate-item.total {
            padding-top: 12px;
            margin-top: 8px;
            border-top: 1px solid rgba(255,255,255,0.3);
            font-size: 15px;
            font-weight: 600;
        }
        
        /* Rating Stars */
        .rating-stars {
            display: flex;
            gap: 4px;
            margin-bottom: 12px;
        }
        .star-btn {
            background: none;
            border: none;
            cursor: pointer;
            padding: 4px;
            border-radius: 6px;
            transition: all 0.2s;
        }
        .star-btn:hover { background: var(--m-mist); }
        .star-btn .material-icons-outlined {
            font-size: 28px;
            color: var(--m-sand);
            transition: color 0.2s;
        }
        .star-btn.active .material-icons-outlined {
            color: var(--pg-gold);
        }
        
        /* Review Modal */
        .review-modal .booking-info-review {
            background: var(--m-mist);
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 24px;
            border: 1px solid var(--m-sand);
        }
        .review-modal .booking-info-review h4 {
            font-family: 'Playfair Display', serif;
            font-size: 1.1rem;
            color: var(--m-forest);
            margin-bottom: 12px;
        }
        .review-modal .review-booking-details {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }
        .review-modal .review-booking-details .detail-item {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            color: #666;
        }
        .review-modal .review-booking-details .material-icons-outlined {
            font-size: 16px;
            color: var(--m-sage);
        }
        
        /* ══════════════════════════
        RESPONSIVE
        ══════════════════════════ */
        @media (max-width: 1024px) {
            .page-inner { padding: 36px 32px 60px; }
            .hero-section { padding: 0 40px 40px; height: 320px; }
            .hero-section h1 { font-size: 2.8rem; }
        }
        
        @media (max-width: 768px) {
            .main-content.full-width .main-header {
                padding: 0 20px;
                height: auto;
                min-height: 60px;
                flex-direction: column;
                gap: 0;
                align-items: stretch;
            }
            .header-left { padding: 12px 0 0; justify-content: space-between; }
            .header-right { padding: 4px 0 8px; justify-content: center; overflow-x: auto; }
            .header-nav { gap: 0; }
            .nav-link { padding: 8px 10px; font-size: 11.5px; height: auto; border-bottom: none; }
            .nav-link span:not(.material-icons-outlined) { display: none; }
            
            .hero-section { height: 260px; padding: 0 24px 32px; background-attachment: scroll; }
            .hero-section h1 { font-size: 2rem; }
            .hero-section p { font-size: 0.95rem; }
            .hero-pills { flex-wrap: wrap; }
            
            .page-inner { padding: 24px 20px 48px; }
            .booking-card-header { flex-direction: column; gap: 16px; }
            .booking-details-grid { grid-template-columns: 1fr; }
            .booking-actions-row { flex-direction: column; }
            .btn-action { width: 100%; justify-content: center; }
            .modal-actions { flex-direction: column; }
            .btn-cancel, .btn-confirm { width: 100%; justify-content: center; }
        }
        
        @media (max-width: 480px) {
            .hero-section h1 { font-size: 1.8rem; }
            .info-cards-row { grid-template-columns: 1fr; }
            .booking-filter-tabs { justify-content: center; }
            .filter-tab { font-size: 11px; padding: 8px 14px; }
        }
    </style>
</head>
<body>
    <!-- MAIN CONTENT -->
    <main class="main-content full-width">
        <header class="main-header">
            <div class="header-left">
                <div class="logo" style="display:flex;align-items:center;gap:12px;margin-right:30px;">
                    <img src="../lgo.png" alt="SJDM Tours Logo" style="height:32px;width:32px;object-fit:contain;border-radius:6px;">
                    <span style="font-family:'Playfair Display',serif;font-weight:700;font-size:17px;color:#fff;">SJDM TOURS</span>
                </div>
                <h1>Booking History</h1>
            </div>
            <div class="header-right">
                <nav class="header-nav">
                    <a href="user-index.php" class="nav-link"><span class="material-icons-outlined">dashboard</span><span>Dashboard</span></a>
                    <a href="user-guides-page.php" class="nav-link"><span class="material-icons-outlined">people</span><span>Tour Guides</span></a>
                    <a href="user-book.php" class="nav-link"><span class="material-icons-outlined">event</span><span>Book Now</span></a>
                    <a href="user-booking-history.php" class="nav-link active"><span class="material-icons-outlined">history</span><span>Booking History</span></a>
                    <a href="user-tourist-spots.php" class="nav-link"><span class="material-icons-outlined">place</span><span>Tourist Spots</span></a>
                    <a href="user-travel-tips.php" class="nav-link"><span class="material-icons-outlined">tips_and_updates</span><span>Travel Tips</span></a>
                </nav>
                <div class="header-actions">
                    <div class="user-profile-dropdown">
                        <button class="profile-trigger">
                            <div class="profile-avatar"><?php echo isset($currentUser['name']) ? strtoupper(substr($currentUser['name'],0,1)) : 'U'; ?></div>
                            <span class="profile-name"><?php echo htmlspecialchars($currentUser['name']); ?></span>
                            <span class="material-icons-outlined">expand_more</span>
                        </button>
                        <div class="dropdown-menu">
                            <div class="dropdown-header">
                                <div class="profile-avatar-large"><?php echo isset($currentUser['name']) ? strtoupper(substr($currentUser['name'],0,1)) : 'U'; ?></div>
                                <h4><?php echo htmlspecialchars($currentUser['name']); ?></h4>
                                <p><?php echo htmlspecialchars($currentUser['email']); ?></p>
                            </div>
                            <div class="dropdown-divider"></div>
                            <a href="user-index.php" class="dropdown-item"><span class="material-icons-outlined">dashboard</span><span>Dashboard</span></a>
                            <a href="user-booking-history.php" class="dropdown-item"><span class="material-icons-outlined">history</span><span>Booking History</span></a>
                            <a href="user-saved-tours.php" class="dropdown-item"><span class="material-icons-outlined">favorite</span><span>Saved Tours</span></a>
                            <div class="dropdown-divider"></div>
                            <a href="user-logout.php" class="dropdown-item"><span class="material-icons-outlined">logout</span><span>Sign Out</span></a>
                        </div>
                    </div>
                </div>
            </div>
        </header>
        
        <div class="content-area">
            <!-- Hero Section -->
            <div class="hero-section">
                <h1>Your Booking<br><em>History</em></h1>
                <p>View and manage all your tour bookings in one place</p>
                <div class="hero-pills">
                    <div class="hero-pill">
                        <span class="material-icons-outlined">history</span>
                        <?php echo count($userBookings); ?> Bookings
                    </div>
                    <div class="hero-pill">
                        <span class="material-icons-outlined">verified_user</span>
                        All Verified
                    </div>
                </div>
            </div>
            
            <div class="page-inner">
                <!-- Info Cards (Weather & Date) -->
                <div class="info-cards-row">
                    <div class="info-card">
                        <div class="info-card-icon">
                            <span class="material-icons-outlined">calendar_today</span>
                        </div>
                        <div class="info-card-content">
                            <div class="info-label">Today's Date</div>
                            <div class="info-value"><?php echo htmlspecialchars($currentWeekday); ?></div>
                            <div class="info-secondary"><?php echo htmlspecialchars($currentDate); ?></div>
                        </div>
                    </div>
                    <div class="info-card">
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
                
                <!-- Bookings List -->
                <div id="bookingsList" class="bookings-container"></div>
            </div>
        </div>
    </main>
    
    <!-- Modal Container -->
    <div id="modalContainer"></div>
    
    <script src="user-script.js"></script>
    <script>
        // Pass bookings data to JavaScript
        const userBookings = <?php echo json_encode($userBookings); ?>;
        const currentUserId = <?php echo (int)$_SESSION['user_id']; ?>;
        let currentFilter = 'all';
        
        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            displayUserBookings();
            initFilterTabs();
            initUserProfileDropdown();
        });
        
        // ========== USER PROFILE DROPDOWN ==========
        function initUserProfileDropdown() {
            const profileDropdown = document.querySelector('.user-profile-dropdown');
            const profileTrigger = document.querySelector('.profile-trigger');
            const dropdownMenu = document.querySelector('.dropdown-menu');
            const logoutLink = document.querySelector('[href="user-logout.php"]');
            
            if (!profileDropdown || !profileTrigger || !dropdownMenu) return;
            
            profileTrigger.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                dropdownMenu.classList.toggle('show');
            });
            
            document.addEventListener('click', function(e) {
                if (!profileDropdown.contains(e.target)) {
                    dropdownMenu.classList.remove('show');
                }
            });
            
            if (logoutLink) {
                logoutLink.addEventListener('click', function(e) {
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
                            <button class="btn-confirm" onclick="confirmLogout()">
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
        
        function confirmLogout() {
            const modal = document.querySelector('.modal-overlay');
            if (modal) modal.remove();
            window.location.href = 'user-logout.php';
        }
        
        // Filter tabs
        function initFilterTabs() {
            const filterTabs = document.querySelectorAll('.filter-tab');
            filterTabs.forEach(tab => {
                tab.addEventListener('click', function() {
                    filterTabs.forEach(t => t.classList.remove('active'));
                    this.classList.add('active');
                    currentFilter = this.dataset.filter;
                    displayUserBookings();
                });
            });
        }
        
        // Display bookings
        function displayUserBookings() {
            const container = document.getElementById('bookingsList');
            if (!container) return;
            
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
                        <button class="btn-primary-action" onclick="window.location.href='user-book.php'">
                            <span class="material-icons-outlined">explore</span>
                            <span>Book a Tour</span>
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
                                <h3>${booking.guide_name || 'Tour Guide'}</h3>
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
                                <div class="detail-label">Guests</div>
                                <div class="detail-value">${booking.number_of_people} Guest${booking.number_of_people > 1 ? 's' : ''}</div>
                            </div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-icon">
                                <span class="material-icons-outlined">confirmation_number</span>
                            </div>
                            <div class="detail-content">
                                <div class="detail-label">Reference</div>
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
                    <div class="booking-actions-row">
                        ${booking.status === 'pending' ? `
                            <button class="btn-action btn-cancel" onclick="cancelBooking(${booking.id})">
                                <span class="material-icons-outlined">cancel</span>
                                <span>Cancel</span>
                            </button>
                        ` : ''}
                        ${booking.status === 'completed' ? `
                            <button class="btn-action btn-review" onclick="showReviewModal(${booking.id})">
                                <span class="material-icons-outlined">rate_review</span>
                                <span>Write Review</span>
                            </button>
                        ` : ''}
                        ${booking.status === 'confirmed' ? `
                            <button class="btn-action btn-modify" onclick="modifyBooking(${booking.id})">
                                <span class="material-icons-outlined">edit</span>
                                <span>Modify</span>
                            </button>
                        ` : ''}
                        <button class="btn-action btn-view" onclick="viewBookingDetails(${booking.id})">
                            <span class="material-icons-outlined">visibility</span>
                            <span>View Details</span>
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
            if (!confirm('Are you sure you want to cancel this booking?')) return;
            fetch('', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `action=cancel_booking&booking_id=${bookingId}`
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    const booking = userBookings.find(b => String(b.id) === String(bookingId));
                    if (booking) booking.status = 'cancelled';
                    displayUserBookings();
                } else {
                    alert(data.message);
                }
            })
            .catch(() => alert('Unable to cancel booking'));
        }
        
        function modifyBooking(bookingId) {
            const booking = userBookings.find(b => String(b.id) === String(bookingId));
            if (!booking) return;
            
            const content = `
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
                <form id="modifyBookingForm">
                    <input type="hidden" name="booking_id" value="${booking.id}">
                    <div class="form-group">
                        <label>New Tour Date *</label>
                        <input type="date" name="new_date" required min="${new Date().toISOString().split('T')[0]}" value="${booking.booking_date}">
                    </div>
                    <div class="form-group">
                        <label>Number of Guests *</label>
                        <input type="number" name="new_guests" min="1" max="30" required value="${booking.number_of_people}" id="modifyGuests">
                    </div>
                    <div class="form-group">
                        <label>Guide Rating *</label>
                        <div class="rating-stars" id="modifyRatingStars">
                            ${[1,2,3,4,5].map(star => `
                                <button type="button" class="star-btn" data-rating="${star}" onclick="setModifyRating(${star})">
                                    <span class="material-icons-outlined">star_border</span>
                                </button>
                            `).join('')}
                        </div>
                        <input type="hidden" id="modifyRatingValue" name="guide_rating" value="0">
                    </div>
                    <div class="form-group">
                        <label>Review (Optional)</label>
                        <textarea name="guide_review" rows="3" placeholder="Share your experience..."></textarea>
                    </div>
                    <div class="price-estimate">
                        <div class="estimate-item"><span>Guide Fee:</span><span>₱2,500.00</span></div>
                        <div class="estimate-item"><span>Entrance Fees:</span><span id="entranceEstimate">₱${(booking.number_of_people * 100).toLocaleString()}.00</span></div>
                        <div class="estimate-item"><span>Platform Fee:</span><span>₱100.00</span></div>
                        <div class="estimate-item total"><strong>Estimated Total:</strong><strong id="totalEstimate">₱${Number(booking.total_amount).toLocaleString()}.00</strong></div>
                    </div>
                </form>
            `;
            createModal('modifyBookingModal', 'Modify Booking', content, 'modify-booking-modal');
            
            setTimeout(() => {
                const guestsInput = document.getElementById('modifyGuests');
                if (guestsInput) guestsInput.addEventListener('input', updatePriceEstimate);
            }, 100);
        }
        
        function updatePriceEstimate() {
            const guests = parseInt(document.getElementById('modifyGuests').value) || 1;
            const entranceFee = guests * 100;
            const total = 2500 + entranceFee + 100;
            document.getElementById('entranceEstimate').textContent = `₱${entranceFee.toLocaleString()}.00`;
            document.getElementById('totalEstimate').textContent = `₱${total.toLocaleString()}.00`;
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
        
        function showReviewModal(bookingId) {
            const booking = userBookings.find(b => String(b.id) === String(bookingId));
            if (!booking) return;
            
            const content = `
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
                <form id="reviewForm">
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
                        <label>Review Title *</label>
                        <input type="text" name="review_title" required placeholder="Summarize your experience">
                    </div>
                    <div class="form-group">
                        <label>Your Review *</label>
                        <textarea name="review_text" rows="4" required placeholder="Share your experience..."></textarea>
                    </div>
                </form>
            `;
            createModal('reviewModal', 'Write a Review', content, 'review-modal');
        }
        
        function setRating(rating) {
            const stars = document.querySelectorAll('#ratingStars .star-btn');
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
        
        function submitModifyBooking() {
            const form = document.getElementById('modifyBookingForm');
            if (!form) return;
            const rating = document.getElementById('modifyRatingValue').value;
            if (rating < 1 || rating > 5) {
                alert('Please provide a rating (1-5 stars)');
                return;
            }
            const formData = new FormData(form);
            formData.append('action', 'modify_booking');
            fetch('', { method: 'POST', body: formData })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    document.querySelector('.modal-overlay')?.remove();
                    setTimeout(() => location.reload(), 1500);
                } else {
                    alert(data.message);
                }
            })
            .catch(() => alert('Error submitting modification'));
        }
        
        function submitReview() {
            const form = document.getElementById('reviewForm');
            if (!form) return;
            const rating = document.getElementById('ratingValue').value;
            if (rating === '0') {
                alert('Please select a rating');
                return;
            }
            alert('Review submitted successfully!');
            document.querySelector('.modal-overlay')?.remove();
        }
        
        function viewBookingDetails(bookingId) {
            const booking = userBookings.find(b => String(b.id) === String(bookingId));
            if (!booking) return;
            const content = `
                <div style="display:grid;gap:16px;">
                    <div class="detail-item"><div class="detail-icon"><span class="material-icons-outlined">tour</span></div><div><div class="detail-label">Guide</div><div class="detail-value">${booking.guide_name || 'Tour Guide'}</div></div></div>
                    <div class="detail-item"><div class="detail-icon"><span class="material-icons-outlined">place</span></div><div><div class="detail-label">Destination</div><div class="detail-value">${booking.destination || booking.tour_name}</div></div></div>
                    <div class="detail-item"><div class="detail-icon"><span class="material-icons-outlined">event</span></div><div><div class="detail-label">Date</div><div class="detail-value">${formatDate(booking.booking_date)}</div></div></div>
                    <div class="detail-item"><div class="detail-icon"><span class="material-icons-outlined">people</span></div><div><div class="detail-label">Guests</div><div class="detail-value">${booking.number_of_people}</div></div></div>
                    <div class="detail-item"><div class="detail-icon"><span class="material-icons-outlined">confirmation_number</span></div><div><div class="detail-label">Reference</div><div class="detail-value">${booking.booking_reference || ('#' + booking.id)}</div></div></div>
                    <div class="detail-item"><div class="detail-icon"><span class="material-icons-outlined">payments</span></div><div><div class="detail-label">Total</div><div class="detail-value">₱${Number(booking.total_amount).toLocaleString()}</div></div></div>
                    <div class="detail-item"><div class="detail-icon"><span class="material-icons-outlined">info</span></div><div><div class="detail-label">Status</div><div class="detail-value">${booking.status.toUpperCase()}</div></div></div>
                </div>
            `;
            createModal('bookingDetailsModal', 'Booking Details', content);
        }
        
        function createModal(modalId, title, content, modalClass = '') {
            const modal = document.createElement('div');
            modal.className = 'modal-overlay';
            
            let actionButtons = '';
            if (modalClass === 'modify-booking-modal') {
                actionButtons = `
                    <div class="modal-actions">
                        <button class="btn-cancel" onclick="this.closest('.modal-overlay').remove()">Cancel</button>
                        <button class="btn-confirm" onclick="submitModifyBooking()">Submit Request</button>
                    </div>
                `;
            } else if (modalClass === 'review-modal') {
                actionButtons = `
                    <div class="modal-actions">
                        <button class="btn-cancel" onclick="this.closest('.modal-overlay').remove()">Cancel</button>
                        <button class="btn-confirm" onclick="submitReview()">Submit Review</button>
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
            document.body.appendChild(modal);
            setTimeout(() => modal.classList.add('show'), 10);
            
            modal.addEventListener('click', function(e) {
                if (e.target === modal) modal.remove();
            });
        }
        
        // Close modal on Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                document.querySelectorAll('.modal-overlay.show').forEach(modal => modal.remove());
            }
        });
    </script>
    
    <!-- Preferences Modal -->
    <?php include __DIR__ . '/../components/preferences-modal.php'; ?>
</body>
</html>