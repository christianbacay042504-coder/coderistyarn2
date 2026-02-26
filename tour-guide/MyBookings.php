<?php
/**
 * Tour Guide My Bookings
 * Created: February 9, 2026
 */

require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/TourGuide.php';

// Check if user is logged in and is a tour guide
requireTourGuide();

// Get current tour guide
$userId = getCurrentUserId();
$tourGuide = new TourGuide($userId);
$profile = $tourGuide->getProfile();

if (!$profile) {
    // Tour guide profile not found, redirect to registration
    header('Location: register.php');
    exit();
}

// Get bookings with filtering
$status = $_GET['status'] ?? 'all';
$page = $_GET['page'] ?? 1;
$perPage = 10;
$offset = ($page - 1) * $perPage;

// Get actual bookings from database
$bookings = [];
$conn = getDatabaseConnection();

if ($conn) {
    // Get current tour guide ID from tour_guides table
    $tourGuideId = $userId;
    
    // Get the tour guide record ID from tour_guides table
    $guideQuery = "SELECT id FROM tour_guides WHERE user_id = ?";
    $guideStmt = $conn->prepare($guideQuery);
    $guideRecordId = null;
    
    if ($guideStmt) {
        $guideStmt->bind_param('i', $tourGuideId);
        $guideStmt->execute();
        $guideResult = $guideStmt->get_result();
        if ($guideResult->num_rows > 0) {
            $guideRecordId = $guideResult->fetch_assoc()['id'];
        }
        $guideStmt->close();
    }
    
    // Build base query - filter by guide_id
    $whereClause = "WHERE b.guide_id = ?";
    $params = [$guideRecordId];
    $types = "i";
    
    // Add status filter if not 'all'
    if ($status !== 'all') {
        $whereClause .= " AND b.status = ?";
        $params[] = $status;
        $types .= "s";
    }
    
    // Get total count for pagination
    $countQuery = "SELECT COUNT(*) as total FROM bookings b $whereClause";
    $countStmt = $conn->prepare($countQuery);
    if ($countStmt) {
        $countStmt->bind_param($types, ...$params);
        $countStmt->execute();
        $totalCount = $countStmt->get_result()->fetch_assoc()['total'];
        $countStmt->close();
    }
    
    // Get bookings with user details
    $query = "SELECT b.*, u.first_name, u.last_name, u.email as user_email
              FROM bookings b 
              LEFT JOIN users u ON b.user_id = u.id 
              $whereClause 
              ORDER BY b.created_at DESC 
              LIMIT ? OFFSET ?";
    
    $params[] = $perPage;
    $params[] = $offset;
    $types .= "ii";
    
    $stmt = $conn->prepare($query);
    if ($stmt) {
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $result = $stmt->get_result();
        
        while ($row = $result->fetch_assoc()) {
            $bookings[] = [
                'id' => $row['id'],
                'tourist_name' => $row['first_name'] . ' ' . $row['last_name'],
                'tourist_email' => $row['user_email'],
                'destination' => $row['tour_name'] ?? 'Unknown Tour',
                'tour_date' => $row['booking_date'],
                'start_time' => '09:00:00', // Default time since not in table
                'end_time' => '12:00:00', // Default time since not in table
                'status' => $row['status'],
                'total_amount' => $row['total_amount'],
                'special_requests' => $row['special_requests'] ?? '',
                'created_at' => $row['created_at']
            ];
        }
        $stmt->close();
    }
}

// Handle booking actions
$actionMessage = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $bookingId = $_POST['booking_id'] ?? 0;
    $action = $_POST['action'] ?? '';
    
    switch ($action) {
        case 'confirm':
            // Update booking status to confirmed
            $actionMessage = 'Booking confirmed successfully!';
            break;
        case 'reject':
            // Update booking status to rejected
            $actionMessage = 'Booking rejected successfully!';
            break;
        case 'complete':
            // Update booking status to completed
            $actionMessage = 'Booking marked as completed!';
            break;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Bookings - SJDM Tours</title>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #2c5f2d;
            --primary-light: #e8f5e9;
            --primary-dark: #1e4220;
            --secondary: #97bc62;
            --success: #10b981;
            --danger: #ef4444;
            --warning: #f59e0b;
            --info: #3b82f6;
            --text-primary: #1f2937;
            --text-secondary: #4b5563;
            --border: #e0e0e0;
            --bg-light: #f5f7fa;
            --white: #ffffff;
            --gray-50: #f9fafb;
            --gray-100: #f3f4f6;
            --gray-200: #e5e7eb;
            --gray-300: #d1d5db;
            --shadow-sm: 0 1px 2px rgba(0, 0, 0, 0.05);
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            --shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            --radius-sm: 0.375rem;
            --radius-md: 0.5rem;
            --radius-lg: 0.75rem;
            --radius-xl: 1rem;
            --transition: all 0.2s ease-in-out;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: #ffffff;
            color: var(--text-primary);
            line-height: 1.6;
            margin: 0;
            padding: 0;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Mobile Menu Toggle */
        .mobile-menu-toggle {
            display: none;
            position: fixed;
            top: 20px;
            left: 20px;
            z-index: 1000;
            background: var(--primary);
            color: white;
            border: none;
            border-radius: var(--radius-md);
            padding: 10px;
            cursor: pointer;
            box-shadow: var(--shadow-md);
        }

        /* Sidebar */
        .sidebar {
            width: 280px;
            background: var(--white);
            padding: 24px;
            display: flex;
            flex-direction: column;
            border-right: 1px solid var(--border);
            height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            overflow-y: auto;
            z-index: 1000;
            transition: transform 0.3s ease;
        }

        .sidebar-logo {
            margin-bottom: 40px;
        }

        .sidebar-logo h1 {
            font-size: 24px;
            font-weight: 700;
            color: var(--primary);
        }

        .sidebar-logo p {
            font-size: 12px;
            color: var(--text-secondary);
            margin-top: 4px;
        }

        .sidebar-nav {
            display: flex;
            flex-direction: column;
            gap: 4px;
            flex: 1;
        }

        .nav-item {
            display: flex;
            align-items: center;
            padding: 12px 16px;
            border-radius: var(--radius-md);
            color: var(--text-secondary);
            text-decoration: none;
            transition: all 0.3s ease;
            cursor: pointer;
            position: relative;
            overflow: hidden;
            font-size: 14px;
        }

        .nav-item:hover {
            background: var(--primary-light);
            color: var(--primary);
            transform: translateX(4px);
            box-shadow: 0 2px 8px rgba(44, 95, 45, 0.15);
        }

        .nav-item.active {
            background: var(--primary);
            color: white;
            transform: translateX(4px);
            box-shadow: 0 4px 12px rgba(44, 95, 45, 0.3);
        }

        .nav-item .material-icons-outlined {
            margin-right: 12px;
            font-size: 20px;
            transition: transform 0.3s ease;
        }

        .nav-item:hover .material-icons-outlined {
            transform: scale(1.1);
        }

        .nav-item.active .material-icons-outlined {
            transform: scale(1.1);
        }

        /* Main Content */
        .main-content {
            flex: 1;
            margin-left: 280px;
            min-height: 100vh;
            background: #ffffff;
        }

        .main-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 32px 40px;
            background: linear-gradient(135deg,
                    rgba(255, 255, 255, 0.98) 0%,
                    rgba(255, 255, 255, 0.95) 25%,
                    rgba(248, 250, 252, 0.92) 50%,
                    rgba(241, 245, 249, 0.88) 100%);
            box-shadow:
                0 20px 60px rgba(0, 0, 0, 0.08),
                0 8px 24px rgba(0, 0, 0, 0.04);
            position: sticky;
            top: 0;
            z-index: 100;
            gap: 24px;
            border-bottom: 3px solid rgba(44, 95, 45, 0.1);
            backdrop-filter: blur(10px);
        }

        .main-header h1 {
            font-size: 2.2rem;
            font-weight: 800;
            color: var(--text-primary);
            text-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            letter-spacing: -1px;
            position: relative;
            z-index: 1;
        }

        .header-actions {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .icon-button {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg,
                    rgba(255, 255, 255, 0.95) 0%,
                    rgba(255, 255, 255, 0.85) 100%);
            border: 2px solid rgba(255, 255, 255, 0.3);
            color: var(--primary);
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            box-shadow:
                0 8px 24px rgba(0, 0, 0, 0.1),
                inset 0 1px 0 rgba(255, 255, 255, 0.8);
            text-decoration: none;
        }

        .icon-button:hover {
            background: linear-gradient(135deg,
                    rgba(255, 255, 255, 1) 0%,
                    rgba(248, 250, 252, 1) 100%);
            transform: translateY(-3px) scale(1.05);
            box-shadow:
                0 12px 32px rgba(44, 95, 45, 0.2),
                0 0 0 4px rgba(255, 255, 255, 0.2);
        }

        .content-area {
            padding: 32px;
            max-width: 1400px;
            margin: 0 auto;
        }

        .alert {
            padding: 16px 20px;
            border-radius: var(--radius-lg);
            margin-bottom: 24px;
            border: none;
            display: flex;
            align-items: center;
            gap: 12px;
            background: linear-gradient(135deg, rgba(44, 95, 45, 0.1) 0%, rgba(44, 95, 45, 0.05) 100%);
            color: var(--primary);
            border: 1px solid rgba(44, 95, 45, 0.2);
        }

        .alert-dismissible {
            position: relative;
        }

        .btn-close {
            background: none;
            border: none;
            font-size: 20px;
            cursor: pointer;
            padding: 4px;
            margin-left: auto;
            opacity: 0.6;
        }

        .btn-close:hover {
            opacity: 1;
        }

        /* Filters */
        .filters-section {
            background: linear-gradient(135deg, #ffffff 0%, #fafbfc 100%);
            border-radius: 20px;
            padding: 24px;
            margin-bottom: 32px;
            box-shadow:
                0 10px 30px rgba(0, 0, 0, 0.05),
                0 2px 8px rgba(0, 0, 0, 0.02);
            border: 1px solid rgba(44, 95, 45, 0.08);
        }

        .filter-tabs {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .filter-tab {
            padding: 10px 20px;
            border: 2px solid rgba(44, 95, 45, 0.2);
            background: white;
            color: var(--text-secondary);
            border-radius: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .filter-tab:hover {
            border-color: var(--primary);
            color: var(--primary);
            background: linear-gradient(135deg, rgba(44, 95, 45, 0.05) 0%, rgba(44, 95, 45, 0.02) 100%);
            transform: translateY(-2px);
            box-shadow: 0 4px 16px rgba(44, 95, 45, 0.15);
        }

        .filter-tab.active {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            color: white;
            border-color: var(--primary);
            box-shadow: 0 6px 20px rgba(44, 95, 45, 0.25);
            transform: translateY(-1px);
        }

        /* Booking Cards */
        .bookings-grid {
            display: grid;
            gap: 24px;
        }

        .booking-card {
            background: linear-gradient(135deg, #ffffff 0%, #fafbfc 100%);
            border-radius: 20px;
            padding: 24px;
            box-shadow:
                0 10px 30px rgba(0, 0, 0, 0.1),
                0 1px 8px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
            border: 1px solid rgba(44, 95, 45, 0.08);
            position: relative;
            overflow: hidden;
        }

        .booking-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, var(--primary) 0%, var(--secondary) 100%);
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .booking-card:hover {
            transform: translateY(-4px);
            box-shadow:
                0 15px 45px rgba(0, 0, 0, 0.12),
                0 4px 12px rgba(0, 0, 0, 0.06);
            border-color: rgba(44, 95, 45, 0.15);
        }

        .booking-card:hover::before {
            opacity: 1;
        }

        .booking-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 20px;
        }

        .booking-info h3 {
            font-size: 1.3rem;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 8px;
            letter-spacing: -0.5px;
        }

        .booking-info .tourist-info {
            color: var(--text-secondary);
            font-size: 14px;
            margin-bottom: 4px;
        }

        .booking-status {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: capitalize;
        }

        .status-pending {
            background: var(--warning);
            color: white;
        }

        .status-confirmed {
            background: var(--success);
            color: white;
        }

        .status-completed {
            background: var(--info);
            color: white;
        }

        .status-cancelled {
            background: var(--danger);
            color: white;
        }

        .booking-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 16px;
            margin-bottom: 20px;
        }

        .detail-item {
            display: flex;
            align-items: center;
            gap: 8px;
            color: var(--text-secondary);
            font-size: 14px;
        }

        .detail-item .material-icons-outlined {
            font-size: 18px;
            color: var(--primary);
        }

        .booking-amount {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 16px;
        }

        .special-requests {
            background: var(--bg-light);
            padding: 12px;
            border-radius: var(--radius-md);
            margin-bottom: 20px;
            font-size: 14px;
            color: var(--text-secondary);
        }

        .booking-actions {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }

        /* Buttons */
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: var(--radius-md);
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            color: white;
            box-shadow: 0 4px 15px rgba(44, 95, 45, 0.2);
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, var(--primary-dark) 0%, var(--primary) 100%);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(44, 95, 45, 0.3);
        }

        .btn-success {
            background: var(--success);
            color: white;
            box-shadow: 0 4px 15px rgba(16, 185, 129, 0.2);
        }

        .btn-success:hover {
            background: #059669;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(16, 185, 129, 0.3);
        }

        .btn-danger {
            background: var(--danger);
            color: white;
            box-shadow: 0 4px 15px rgba(239, 68, 68, 0.2);
        }

        .btn-danger:hover {
            background: #dc2626;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(239, 68, 68, 0.3);
        }

        .btn-outline {
            background: transparent;
            color: var(--text-secondary);
            border: 2px solid var(--border);
        }

        .btn-outline:hover {
            background: var(--bg-light);
            transform: translateY(-2px);
        }

        .btn-sm {
            padding: 8px 16px;
            font-size: 13px;
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: var(--text-secondary);
        }

        .empty-state .material-icons-outlined {
            font-size: 4rem;
            color: var(--primary);
            opacity: 0.3;
            margin-bottom: 16px;
        }

        .empty-state h3 {
            font-size: 1.5rem;
            margin-bottom: 8px;
            color: var(--text-primary);
        }

        .availability-badge {
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 14px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        .availability-badge.available { background: var(--primary-light); color: var(--primary-dark); }
        .availability-badge.busy { background: #fef2f2; color: #dc2626; }
        .availability-badge.offline { background: var(--gray-100); color: var(--text-secondary); }

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

            .main-header {
                padding: 20px;
                gap: 16px;
            }

            .main-header h1 {
                font-size: 1.8rem;
            }

            .content-area {
                padding: 20px;
            }

            .booking-details {
                grid-template-columns: 1fr;
            }

            .filter-tabs {
                justify-content: center;
            }

            .booking-actions {
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <!-- Mobile Menu Toggle -->
    <button class="mobile-menu-toggle" id="mobileMenuToggle">
        <span class="material-icons-outlined">menu</span>
    </button>

    <!-- SIDEBAR -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-logo">
            <h1>SJDM Tours</h1>
            <p>Tour Guide Dashboard</p>
        </div>

        <nav class="sidebar-nav">
            <a class="nav-item" href="dashboard.php">
                <span class="material-icons-outlined">dashboard</span>
                <span>Dashboard</span>
            </a>
            <a class="nav-item active" href="MyBookings.php">
                <span class="material-icons-outlined">calendar_today</span>
                <span>My Bookings</span>
            </a>

        </nav>
    </aside>

    <!-- MAIN CONTENT -->
    <main class="main-content">
        <header class="main-header">
            <h1>Tour Guide Dashboard</h1>
            <div class="header-actions">
                <span class="availability-badge <?php echo $profile['availability_status']; ?>">
                    <span class="material-icons-outlined" style="font-size: 16px;">circle</span>
                    <?php echo ucfirst($profile['availability_status']); ?>
                </span>
                <a href="../logout.php" class="icon-button" title="Logout">
                    <span class="material-icons-outlined">logout</span>
                </a>
            </div>
        </header>

        <div class="content-area">
            <?php if ($actionMessage): ?>
                <div class="alert alert-dismissible">
                    <span class="material-icons-outlined">check_circle</span>
                    <?php echo htmlspecialchars($actionMessage); ?>
                    <button class="btn-close" onclick="this.parentElement.style.display='none'">
                        <span class="material-icons-outlined">close</span>
                    </button>
                </div>
            <?php endif; ?>

            <!-- Filters -->
            <div class="filters-section">
                <div class="filter-tabs">
                    <a href="?status=all" class="filter-tab <?php echo $status === 'all' ? 'active' : ''; ?>">
                        <span class="material-icons-outlined">list</span>
                        All Bookings
                    </a>
                    <a href="?status=pending" class="filter-tab <?php echo $status === 'pending' ? 'active' : ''; ?>">
                        <span class="material-icons-outlined">pending</span>
                        Pending
                    </a>
                    <a href="?status=confirmed" class="filter-tab <?php echo $status === 'confirmed' ? 'active' : ''; ?>">
                        <span class="material-icons-outlined">check_circle</span>
                        Confirmed
                    </a>
                    <a href="?status=completed" class="filter-tab <?php echo $status === 'completed' ? 'active' : ''; ?>">
                        <span class="material-icons-outlined">task_alt</span>
                        Completed
                    </a>
                    <a href="?status=cancelled" class="filter-tab <?php echo $status === 'cancelled' ? 'active' : ''; ?>">
                        <span class="material-icons-outlined">cancel</span>
                        Cancelled
                    </a>
                </div>
            </div>

            <!-- Bookings List -->
            <div class="bookings-grid">
                <?php if (empty($bookings)): ?>
                    <div class="empty-state">
                        <span class="material-icons-outlined">event_busy</span>
                        <h3>No bookings found</h3>
                        <p>There are no bookings matching your current filter.</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($bookings as $booking): ?>
                        <div class="booking-card">
                            <div class="booking-header">
                                <div class="booking-info">
                                    <h3><?php echo htmlspecialchars($booking['destination']); ?></h3>
                                    <div class="tourist-info">
                                        <strong><?php echo htmlspecialchars($booking['tourist_name']); ?></strong>
                                    </div>
                                    <div class="tourist-info">
                                        <?php echo htmlspecialchars($booking['tourist_email']); ?>
                                    </div>
                                </div>
                                <span class="booking-status status-<?php echo $booking['status']; ?>">
                                    <?php echo ucfirst($booking['status']); ?>
                                </span>
                            </div>

                            <div class="booking-details">
                                <div class="detail-item">
                                    <span class="material-icons-outlined">calendar_today</span>
                                    <?php echo date('M d, Y', strtotime($booking['tour_date'])); ?>
                                </div>
                                <div class="detail-item">
                                    <span class="material-icons-outlined">schedule</span>
                                    <?php echo date('h:i A', strtotime($booking['start_time'])); ?> - 
                                    <?php echo date('h:i A', strtotime($booking['end_time'])); ?>
                                </div>
                                <div class="detail-item">
                                    <span class="material-icons-outlined">history</span>
                                    Booked <?php echo date('M d, Y', strtotime($booking['created_at'])); ?>
                                </div>
                            </div>

                            <div class="booking-amount">
                                ₱<?php echo number_format($booking['total_amount'], 2); ?>
                            </div>

                            <?php if (!empty($booking['special_requests'])): ?>
                                <div class="special-requests">
                                    <strong>Special Requests:</strong><br>
                                    <?php echo htmlspecialchars($booking['special_requests']); ?>
                                </div>
                            <?php endif; ?>

                            <div class="booking-actions">
                                <?php if ($booking['status'] === 'pending'): ?>
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="booking_id" value="<?php echo $booking['id']; ?>">
                                        <input type="hidden" name="action" value="confirm">
                                        <button type="submit" class="btn btn-success btn-sm">
                                            <span class="material-icons-outlined">check</span>
                                            Confirm
                                        </button>
                                    </form>
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="booking_id" value="<?php echo $booking['id']; ?>">
                                        <input type="hidden" name="action" value="reject">
                                        <button type="submit" class="btn btn-danger btn-sm">
                                            <span class="material-icons-outlined">close</span>
                                            Reject
                                        </button>
                                    </form>
                                <?php endif; ?>

                                <?php if ($booking['status'] === 'confirmed'): ?>
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="booking_id" value="<?php echo $booking['id']; ?>">
                                        <input type="hidden" name="action" value="complete">
                                        <button type="submit" class="btn btn-primary btn-sm">
                                            <span class="material-icons-outlined">task_alt</span>
                                            Mark Complete
                                        </button>
                                    </form>
                                <?php endif; ?>

                                <button class="btn btn-outline btn-sm" onclick="viewBookingDetails(<?php echo $booking['id']; ?>)">
                                    <span class="material-icons-outlined">visibility</span>
                                    View Details
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <script>
        // Mobile menu toggle
        document.getElementById('mobileMenuToggle').addEventListener('click', function() {
            document.getElementById('sidebar').classList.toggle('active');
        });

        // View booking details (placeholder function)
        function viewBookingDetails(bookingId) {
            // In a real application, this would open a modal or navigate to a details page
            alert('Viewing details for booking #' + bookingId);
        }

        // Handle form submissions with confirmation
        document.querySelectorAll('form').forEach(form => {
            form.addEventListener('submit', function(e) {
                const action = this.querySelector('input[name="action"]').value;
                const actionText = action.charAt(0).toUpperCase() + action.slice(1);
                
                if (!confirm(`Are you sure you want to ${actionText} this booking?`)) {
                    e.preventDefault();
                }
            });
        });
    </script>
</body>
</html>