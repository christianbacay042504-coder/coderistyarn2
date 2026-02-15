<?php
// Bookings Management Module
// This file handles booking management operations with separated connections and functions

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/auth.php';

// Database connection functions
function getAdminConnection()
{
    return getDatabaseConnection();
}

function initAdminAuth()
{
    requireAdmin();
    return getCurrentUser();
}

function closeAdminConnection($conn)
{
    closeDatabaseConnection($conn);
}

// Booking Management Functions
function addBooking($conn, $data)
{
    try {
        $stmt = $conn->prepare("INSERT INTO bookings (user_id, tour_name, booking_date, number_of_people, total_amount, status, created_at) VALUES (?, ?, ?, ?, ?, 'pending', NOW())");
        $stmt->bind_param("issid", $data['user_id'], $data['tour_name'], $data['booking_date'], $data['number_of_people'], $data['total_amount']);

        if ($stmt->execute()) {
            return ['success' => true, 'message' => 'Booking added successfully'];
        } else {
            return ['success' => false, 'message' => 'Failed to add booking'];
        }
    } catch (Exception $e) {
        return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
    }
}

function editBookingRecord($conn, $data)
{
    try {
        $stmt = $conn->prepare("UPDATE bookings SET tour_name = ?, booking_date = ?, number_of_people = ?, total_amount = ?, status = ? WHERE id = ?");
        $stmt->bind_param(
            "ssidsi",
            $data['tour_name'],
            $data['booking_date'],
            $data['number_of_people'],
            $data['total_amount'],
            $data['status'],
            $data['booking_id']
        );

        if ($stmt->execute()) {
            return ['success' => true, 'message' => 'Booking updated successfully'];
        }

        return ['success' => false, 'message' => 'Failed to update booking'];
    } catch (Exception $e) {
        return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
    }
}

function updateBookingStatus($conn, $data)
{
    try {
        $stmt = $conn->prepare("UPDATE bookings SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $data['status'], $data['booking_id']);

        if ($stmt->execute()) {
            return ['success' => true, 'message' => 'Booking status updated successfully'];
        } else {
            return ['success' => false, 'message' => 'Failed to update booking status'];
        }
    } catch (Exception $e) {
        return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
    }
}

function deleteBooking($conn, $bookingId)
{
    try {
        $stmt = $conn->prepare("DELETE FROM bookings WHERE id = ?");
        $stmt->bind_param("i", $bookingId);

        if ($stmt->execute()) {
            return ['success' => true, 'message' => 'Booking deleted successfully'];
        } else {
            return ['success' => false, 'message' => 'Failed to delete booking'];
        }
    } catch (Exception $e) {
        return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
    }
}

function getBooking($conn, $bookingId)
{
    try {
        $stmt = $conn->prepare("SELECT b.*, CONCAT(u.first_name, ' ', u.last_name) as user_name, u.email as user_email FROM bookings b JOIN users u ON b.user_id = u.id WHERE b.id = ?");
        $stmt->bind_param("i", $bookingId);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $bookingData = $result->fetch_assoc();
            return ['success' => true, 'data' => $bookingData];
        } else {
            return ['success' => false, 'message' => 'Booking not found'];
        }
    } catch (Exception $e) {
        return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
    }
}

function getBookingsList($conn, $page = 1, $limit = 15, $search = '', $status = '')
{
    $offset = ($page - 1) * $limit;
    
    // Get bookings with pagination using prepared statements
    $bookingsQuery = "SELECT b.*, CONCAT(u.first_name, ' ', u.last_name) as user_name, u.email as user_email
                      FROM bookings b 
                      JOIN users u ON b.user_id = u.id 
                      WHERE 1=1";
    
    $params = [];
    $types = '';
    
    if ($search) {
        $bookingsQuery .= " AND (b.tour_name LIKE ? OR u.first_name LIKE ? OR u.last_name LIKE ?)";
        $searchParam = "%{$search}%";
        $params[] = $searchParam;
        $params[] = $searchParam;
        $params[] = $searchParam;
        $types .= 'sss';
    }
    
    if ($status) {
        $bookingsQuery .= " AND b.status = ?";
        $params[] = $status;
        $types .= 's';
    }
    
    $bookingsQuery .= " ORDER BY b.created_at DESC LIMIT ? OFFSET ?";
    $params[] = $limit;
    $params[] = $offset;
    $types .= 'ii';
    
    $stmt = $conn->prepare($bookingsQuery);
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $bookingsResult = $stmt->get_result();
    
    // Get total count for pagination using prepared statements
    $countQuery = "SELECT COUNT(*) as total FROM bookings b JOIN users u ON b.user_id = u.id WHERE 1=1";
    $countParams = [];
    $countTypes = '';
    
    if ($search) {
        $countQuery .= " AND (b.tour_name LIKE ? OR u.first_name LIKE ? OR u.last_name LIKE ?)";
        $countParams[] = $searchParam;
        $countParams[] = $searchParam;
        $countParams[] = $searchParam;
        $countTypes .= 'sss';
    }
    
    if ($status) {
        $countQuery .= " AND b.status = ?";
        $countParams[] = $status;
        $countTypes .= 's';
    }
    
    $countStmt = $conn->prepare($countQuery);
    if (!empty($countParams)) {
        $countStmt->bind_param($countTypes, ...$countParams);
    }
    $countStmt->execute();
    $countResult = $countStmt->get_result();
    $totalCount = $countResult->fetch_assoc()['total'];
    $totalPages = ceil($totalCount / $limit);
    
    $bookings = [];
    while ($row = $bookingsResult->fetch_assoc()) {
        $bookings[] = $row;
    }
    
    $stmt->close();
    $countStmt->close();
    
    return [
        'bookings' => $bookings,
        'pagination' => [
            'current_page' => $page,
            'total_pages' => $totalPages,
            'total_count' => $totalCount,
            'limit' => $limit
        ]
    ];
}

function getBookingStats($conn)
{
    $stats = [];

    // Total bookings
    $result = $conn->query("SELECT COUNT(*) as total FROM bookings");
    $stats['total'] = $result->fetch_assoc()['total'];

    // Bookings by status
    $result = $conn->query("SELECT status, COUNT(*) as count FROM bookings GROUP BY status");
    $stats['by_status'] = [];
    while ($row = $result->fetch_assoc()) {
        $stats['by_status'][$row['status']] = $row['count'];
    }

    // Today's bookings
    $result = $conn->query("SELECT COUNT(*) as total FROM bookings WHERE DATE(created_at) = CURDATE()");
    $stats['today'] = $result->fetch_assoc()['total'];

    // This month's bookings
    $result = $conn->query("SELECT COUNT(*) as total FROM bookings WHERE MONTH(created_at) = MONTH(CURDATE()) AND YEAR(created_at) = YEAR(CURDATE())");
    $stats['this_month'] = $result->fetch_assoc()['total'];

    // Total revenue
    $result = $conn->query("SELECT SUM(total_amount) as total FROM bookings WHERE status = 'confirmed'");
    $stats['total_revenue'] = $result ? ($result->fetch_assoc()['total'] ?? 0) : 0;

    return $stats;
}

function getAdminStats($conn)
{
    $stats = [];

    // Total users
    $result = $conn->query("SELECT COUNT(*) as total FROM users WHERE user_type = 'user'");
    $stats['totalUsers'] = $result->fetch_assoc()['total'];

    // Active users
    $result = $conn->query("SELECT COUNT(*) as total FROM users WHERE user_type = 'user' AND status = 'active'");
    $stats['activeUsers'] = $result->fetch_assoc()['total'];

    // Total bookings
    $result = $conn->query("SELECT COUNT(*) as total FROM bookings");
    $stats['totalBookings'] = $result->fetch_assoc()['total'];

    // Today's logins
    $result = $conn->query("SELECT COUNT(DISTINCT user_id) as total FROM login_activity WHERE DATE(login_time) = CURDATE() AND status = 'success'");
    $stats['todayLogins'] = $result->fetch_assoc()['total'];

    // Total guides
    $result = $conn->query("SELECT COUNT(*) as total FROM tour_guides");
    $stats['totalGuides'] = $result->fetch_assoc()['total'];

    // Total destinations
    $result = $conn->query("SELECT COUNT(*) as total FROM tourist_spots");
    $stats['totalDestinations'] = $result->fetch_assoc()['total'];

    return $stats;
}

// Initialize admin authentication
$currentUser = initAdminAuth();

// Get database connection
$conn = getAdminConnection();

// Fetch dashboard settings
$dbSettings = [];
$result = $conn->query("SELECT setting_key, setting_value FROM admin_dashboard_settings");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $dbSettings[$row['setting_key']] = $row['setting_value'];
    }
}

// Fetch booking settings
$bSettings = [];
$result = $conn->query("SELECT setting_key, setting_value FROM booking_settings");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $bSettings[$row['setting_key']] = $row['setting_value'];
    }
}

// Common settings
$logoText = $dbSettings['admin_logo_text'] ?? 'SJDM ADMIN';
$moduleTitle = $bSettings['module_title'] ?? 'Bookings Management';
$moduleSubtitle = $bSettings['module_subtitle'] ?? 'Manage tour bookings';
$adminMark = $dbSettings['admin_mark_label'] ?? 'A';

// Fetch admin specific info
$adminInfo = ['role_title' => 'Administrator', 'admin_mark' => 'A'];
$stmt = $conn->prepare("SELECT admin_mark, role_title FROM admin_users WHERE user_id = ?");
$userId = $currentUser['id'];
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
if ($row = $result->fetch_assoc()) {
    $adminInfo = $row;
}
$stmt->close();

// Fetch sidebar menu
$menuItems = [];
$result = $conn->query("SELECT * FROM admin_menu_items WHERE is_active = 1 ORDER BY display_order ASC");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $menuItems[] = $row;
    }
}

// Handle AJAX requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    switch ($action) {
        case 'add_booking':
            $response = addBooking($conn, $_POST);
            echo json_encode($response);
            exit;
        case 'edit_booking':
            $response = editBookingRecord($conn, $_POST);
            echo json_encode($response);
            exit;
        case 'update_booking_status':
            $response = updateBookingStatus($conn, $_POST);
            echo json_encode($response);
            exit;
        case 'delete_booking':
            $response = deleteBooking($conn, $_POST['booking_id']);
            echo json_encode($response);
            exit;
        case 'get_booking':
            $response = getBooking($conn, $_POST['booking_id']);
            echo json_encode($response);
            exit;
    }
}

// Pagination variables
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$limit = intval($bSettings['default_booking_limit'] ?? 15);
$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
$status = isset($_GET['status']) ? $conn->real_escape_string($_GET['status']) : '';

// Get bookings data
$bookingsData = getBookingsList($conn, $page, $limit, $search, $status);
$bookings = $bookingsData['bookings'];
$pagination = $bookingsData['pagination'];

// Get statistics
$stats = getAdminStats($conn);
$bookingStats = getBookingStats($conn);

// Map query keys to values for menu badges
$queryValues = [
    'totalUsers' => $stats['totalUsers'],
    'totalBookings' => $stats['totalBookings'],
    'totalGuides' => $stats['totalGuides'],
    'totalDestinations' => $stats['totalDestinations']
];

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bookings Management | SJDM Tours Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet">
    <link rel="stylesheet" href="admin-styles.css">
</head>
<body>
    <div class="admin-container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <div class="logo">
                    <div class="mark-icon"><?php echo strtoupper(substr($logoText, 0, 1) ?: 'A'); ?></div>
                    <span><?php echo $logoText; ?></span>
                </div>
            </div>

            <nav class="sidebar-nav">
                <?php foreach ($menuItems as $item):
                    // Skip hotels, settings, and reports menu items
                    if (stripos($item['menu_name'], 'hotels') !== false || stripos($item['menu_url'], 'hotels') !== false || 
                        stripos($item['menu_name'], 'settings') !== false || stripos($item['menu_url'], 'settings') !== false ||
                        stripos($item['menu_name'], 'reports') !== false || stripos($item['menu_url'], 'reports') !== false) {
                        continue;
                    }
                    
                    $isActive = basename($_SERVER['PHP_SELF']) == $item['menu_url'] ? 'active' : '';
                    $badgeVal = 0;
                    if (isset($item['badge_query']) && isset($queryValues[$item['badge_query']])) {
                        $badgeVal = $queryValues[$item['badge_query']];
                    }
                    ?>
                    <a href="<?php echo $item['menu_url']; ?>" class="nav-item <?php echo $isActive; ?>">
                        <span class="material-icons-outlined"><?php echo $item['menu_icon']; ?></span>
                        <span><?php echo $item['menu_name']; ?></span>
                        <?php if ($badgeVal > 0): ?>
                            <span class="badge"><?php echo $badgeVal; ?></span>
                        <?php endif; ?>
                    </a>
                <?php endforeach; ?>
            </nav>

            <div class="sidebar-footer">
                <a href="logout.php" class="logout-btn">
                    <span class="material-icons-outlined">logout</span>
                    <span>Logout</span>
                </a>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <header class="top-bar">
                <div class="page-title">
                    <h1><?php echo $moduleTitle; ?></h1>
                    <p><?php echo $moduleSubtitle; ?></p>
                </div>

                <div class="top-bar-actions">
                    <!-- Admin Profile Dropdown -->
                    <div class="profile-dropdown">
                        <button class="profile-button" id="adminProfileButton">
                            <div class="profile-avatar"><?php echo isset($adminMark) ? substr($adminMark, 0, 1) : 'A'; ?></div>
                            <span class="material-icons-outlined">expand_more</span>
                        </button>
                        <div class="dropdown-menu" id="adminProfileMenu">
                            <div class="profile-info">
                                <div class="profile-avatar large"><?php echo isset($adminMark) ? substr($adminMark, 0, 1) : 'A'; ?></div>
                                <div class="profile-details">
                                    <h3 class="admin-name"><?php echo isset($currentUser['first_name']) ? htmlspecialchars($currentUser['first_name'] . ' ' . $currentUser['last_name']) : 'Administrator'; ?></h3>
                                    <p class="admin-email"><?php echo isset($currentUser['email']) ? htmlspecialchars($currentUser['email']) : 'admin@sjdmtours.com'; ?></p>
                                </div>
                            </div>
                            <div class="dropdown-divider"></div>
                            <a href="javascript:void(0)" class="dropdown-item" id="adminAccountLink">
                                <span class="material-icons-outlined">account_circle</span>
                                <span>My Account</span>
                            </a>
                            <div class="dropdown-divider"></div>
                            <a href="javascript:void(0)" class="dropdown-item" id="adminSettingsLink">
                                <span class="material-icons-outlined">settings</span>
                                <span>Settings</span>
                            </a>
                            <div class="dropdown-divider"></div>
                            <a href="javascript:void(0)" class="dropdown-item" id="adminHelpLink">
                                <span class="material-icons-outlined">help_outline</span>
                                <span>Help & Support</span>
                            </a>
                            <a href="logout.php" class="dropdown-item" id="adminSignoutLink">
                                <span class="material-icons-outlined">logout</span>
                                <span>Sign Out</span>
                            </a>
                        </div>
                    </div>
                </div>
            </header>

            <div class="content-area">
                <!-- Booking Statistics -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <h3><?php echo $bookingStats['total']; ?></h3>
                        <p>Total Bookings</p>
                    </div>
                    <div class="stat-card">
                        <h3><?php echo $bookingStats['today']; ?></h3>
                        <p>Today's Bookings</p>
                    </div>
                    <div class="stat-card">
                        <h3>₱<?php echo number_format($bookingStats['total_revenue'], 2); ?></h3>
                        <p>Total Revenue</p>
                    </div>
                    <div class="stat-card">
                        <h3><?php echo $bookingStats['this_month']; ?></h3>
                        <p>This Month</p>
                    </div>
                </div>

                <!-- Search and Filters -->
                <div class="booking-search-bar">
                    <div class="booking-search-input">
                        <span class="material-icons-outlined">search</span>
                        <input type="text" id="searchInput" placeholder="Search bookings by tour name or user..."
                            value="<?php echo htmlspecialchars($search); ?>">
                    </div>
                    <div class="booking-status-filter">
                        <span class="material-icons-outlined">tune</span>
                        <select id="statusFilter" onchange="filterBookings()">
                            <option value="">All Status</option>
                            <option value="pending" <?php echo $status == 'pending' ? 'selected' : ''; ?>>Pending</option>
                            <option value="confirmed" <?php echo $status == 'confirmed' ? 'selected' : ''; ?>>Confirmed</option>
                            <option value="completed" <?php echo $status == 'completed' ? 'selected' : ''; ?>>Completed</option>
                            <option value="cancelled" <?php echo $status == 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                        </select>
                    </div>
                    <button class="btn-secondary" onclick="searchBookings()">
                        <span class="material-icons-outlined">search</span>
                        Search
                    </button>
                    <button class="btn-secondary" onclick="clearFilters()">
                        <span class="material-icons-outlined">clear</span>
                        Clear
                    </button>
                </div>

                <!-- Bookings Table -->
                <div class="table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Booking ID</th>
                                <th>User</th>
                                <th>Tour Name</th>
                                <th>Date</th>
                                <th>People</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($bookings as $booking): ?>
                                <tr>
                                    <td>#<?php echo str_pad($booking['id'], 6, '0', STR_PAD_LEFT); ?></td>
                                    <td>
                                        <div class="user-info">
                                            <div class="user-avatar">
                                                <?php echo strtoupper(substr($booking['user_name'], 0, 1)); ?>
                                            </div>
                                            <div>
                                                <strong><?php echo htmlspecialchars($booking['user_name']); ?></strong><br>
                                                <small><?php echo htmlspecialchars($booking['user_email']); ?></small>
                                            </div>
                                        </div>
                                    </td>
                                    <td><?php echo htmlspecialchars($booking['tour_name']); ?></td>
                                    <td><?php echo date('M j, Y', strtotime($booking['booking_date'])); ?></td>
                                    <td><?php echo $booking['number_of_people']; ?></td>
                                    <td class="booking-amount">₱<?php echo number_format($booking['total_amount'], 2); ?>
                                    </td>
                                    <td>
                                        <span class="status-badge status-<?php echo $booking['status']; ?>">
                                            <?php echo ucfirst($booking['status']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="action-buttons">
                                            <?php if ($booking['status'] === 'pending'): ?>
                                                <button class="btn-icon" onclick="acceptBooking(<?php echo $booking['id']; ?>)" title="Accept">
                                                    <span class="material-icons-outlined">check</span>
                                                </button>
                                                <button class="btn-icon" onclick="rejectBooking(<?php echo $booking['id']; ?>)" title="Reject">
                                                    <span class="material-icons-outlined">close</span>
                                                </button>
                                            <?php endif; ?>
                                            <button class="btn-icon" onclick="editBooking(<?php echo $booking['id']; ?>)"
                                                title="Edit">
                                                <span class="material-icons-outlined">edit</span>
                                            </button>
                                            <button class="btn-icon" onclick="updateStatus(<?php echo $booking['id']; ?>)"
                                                title="Update Status">
                                                <span class="material-icons-outlined">sync</span>
                                            </button>
                                            <button class="btn-icon" onclick="deleteBooking(<?php echo $booking['id']; ?>)"
                                                title="Delete">
                                                <span class="material-icons-outlined">delete</span>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <?php if ($pagination['total_pages'] > 1): ?>
                    <div class="pagination">
                        <?php if ($pagination['current_page'] > 1): ?>
                            <button onclick="goToPage(<?php echo $pagination['current_page'] - 1; ?>)">Previous</button>
                        <?php endif; ?>

                        <?php for ($i = 1; $i <= $pagination['total_pages']; $i++): ?>
                            <button onclick="goToPage(<?php echo $i; ?>)" <?php echo $i == $pagination['current_page'] ? 'class="active"' : ''; ?>>
                                <?php echo $i; ?>
                            </button>
                        <?php endfor; ?>

                        <?php if ($pagination['current_page'] < $pagination['total_pages']): ?>
                            <button onclick="goToPage(<?php echo $pagination['current_page'] + 1; ?>)">Next</button>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </main>
    </div>

    <!-- Delete Booking Modal -->
    <div id="deleteBookingModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Delete Booking</h2>
                <button class="modal-close" onclick="closeDeleteBookingModal()">
                    <span class="material-icons-outlined">close</span>
                </button>
            </div>
            <form id="deleteBookingForm" onsubmit="handleDeleteBooking(event)">
                <input type="hidden" id="deleteBookingId" name="booking_id">
                <div class="modal-body">
                    <p>Are you sure you want to delete <strong id="deleteBookingLabel"></strong>? This action cannot be undone.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-secondary" onclick="closeDeleteBookingModal()">Cancel</button>
                    <button type="submit" class="btn-primary">Delete</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Update Booking Status Modal -->
    <div id="updateStatusModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Update Booking Status</h2>
                <button class="modal-close" onclick="closeUpdateStatusModal()">
                    <span class="material-icons-outlined">close</span>
                </button>
            </div>
            <form id="updateStatusForm" onsubmit="handleUpdateStatus(event)">
                <input type="hidden" id="updateStatusBookingId" name="booking_id">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="updateStatusSelect">Status *</label>
                        <select id="updateStatusSelect" name="status" required>
                            <option value="pending">Pending</option>
                            <option value="confirmed">Confirmed</option>
                            <option value="completed">Completed</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-secondary" onclick="closeUpdateStatusModal()">Cancel</button>
                    <button type="submit" class="btn-primary">Update Status</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Booking Modal -->
    <div id="editBookingModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Edit Booking</h2>
                <button class="modal-close" onclick="closeEditBookingModal()">
                    <span class="material-icons-outlined">close</span>
                </button>
            </div>
            <form id="editBookingForm" onsubmit="handleEditBooking(event)">
                <input type="hidden" id="editBookingId" name="booking_id">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="editBookingTour">Tour Name *</label>
                        <input type="text" id="editBookingTour" name="tour_name" required>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="editBookingDate">Booking Date *</label>
                            <input type="date" id="editBookingDate" name="booking_date" required>
                        </div>
                        <div class="form-group">
                            <label for="editBookingPeople">Number of People *</label>
                            <input type="number" id="editBookingPeople" name="number_of_people" min="1" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="editBookingAmount">Total Amount *</label>
                            <input type="number" id="editBookingAmount" name="total_amount" min="0" step="0.01" required>
                        </div>
                        <div class="form-group">
                            <label for="editBookingStatus">Status *</label>
                            <select id="editBookingStatus" name="status" required>
                                <option value="pending">Pending</option>
                                <option value="confirmed">Confirmed</option>
                                <option value="completed">Completed</option>
                                <option value="cancelled">Cancelled</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-secondary" onclick="closeEditBookingModal()">Cancel</button>
                    <button type="submit" class="btn-primary">Update Booking</button>
                </div>
            </form>
        </div>
    </div>

    <style>
        .booking-search-bar {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 18px;
            border-radius: 18px;
            background: rgba(255, 255, 255, 0.92);
            border: 1px solid rgba(0, 0, 0, 0.05);
            box-shadow:
                0 10px 30px rgba(0, 0, 0, 0.03),
                0 2px 8px rgba(0, 0, 0, 0.02);
            margin-bottom: 24px;
            backdrop-filter: blur(8px);
        }

        .booking-search-input,
        .booking-status-filter {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 12px 14px;
            border-radius: 16px;
            background: var(--gray-50);
            border: 2px solid transparent;
            transition: all 0.2s ease;
        }

        .booking-search-input {
            flex: 1;
            min-width: 240px;
        }

        .booking-search-input:focus-within,
        .booking-status-filter:focus-within {
            background: white;
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(44, 95, 45, 0.10);
            transform: translateY(-1px);
        }

        .booking-search-input .material-icons-outlined,
        .booking-status-filter .material-icons-outlined {
            font-size: 20px;
            color: var(--text-muted);
        }

        .booking-search-input input {
            width: 100%;
            border: none;
            outline: none;
            background: transparent;
            font-size: 14px;
            font-weight: 500;
            color: var(--text-primary);
        }

        .booking-search-input input::placeholder {
            color: var(--text-muted);
            font-weight: 400;
        }

        .booking-status-filter select {
            border: none;
            outline: none;
            background: transparent;
            font-size: 14px;
            font-weight: 600;
            color: var(--text-primary);
            cursor: pointer;
            padding-right: 4px;
        }

        @media (max-width: 900px) {
            .booking-search-bar {
                flex-direction: column;
                align-items: stretch;
            }

            .booking-search-input,
            .booking-status-filter {
                width: 100%;
            }

            .booking-search-bar .btn-secondary {
                width: 100%;
                justify-content: center;
            }
        }
    </style>

    <script src="admin-script.js"></script>
    <script src="admin-profile-dropdown.js"></script>
    <script>
        function searchBookings() {
            const searchValue = document.getElementById('searchInput').value;
            const statusValue = document.getElementById('statusFilter').value;
            const params = new URLSearchParams();
            if (searchValue) params.append('search', searchValue);
            if (statusValue) params.append('status', statusValue);
            window.location.href = `?${params.toString()}`;
        }

        function filterBookings() {
            searchBookings();
        }

        function clearFilters() {
            document.getElementById('searchInput').value = '';
            document.getElementById('statusFilter').value = '';
            window.location.href = '?';
        }

        function goToPage(page) {
            const searchValue = document.getElementById('searchInput').value;
            const statusValue = document.getElementById('statusFilter').value;
            const params = new URLSearchParams();
            params.append('page', page);
            if (searchValue) params.append('search', searchValue);
            if (statusValue) params.append('status', statusValue);
            window.location.href = `?${params.toString()}`;
        }

        function editBooking(bookingId) {
            fetch('', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `action=get_booking&booking_id=${bookingId}`
            })
            .then(response => response.json())
            .then(result => {
                if (!result.success) {
                    alert(result.message || 'Failed to load booking details.');
                    return;
                }

                const b = result.data;
                document.getElementById('editBookingId').value = b.id || '';
                document.getElementById('editBookingTour').value = b.tour_name || '';
                document.getElementById('editBookingPeople').value = b.number_of_people || 1;
                document.getElementById('editBookingAmount').value = b.total_amount || 0;
                document.getElementById('editBookingStatus').value = b.status || 'pending';

                // Convert to YYYY-MM-DD for <input type="date">
                if (b.booking_date) {
                    const d = new Date(b.booking_date);
                    if (!isNaN(d.getTime())) {
                        const y = d.getFullYear();
                        const m = String(d.getMonth() + 1).padStart(2, '0');
                        const day = String(d.getDate()).padStart(2, '0');
                        document.getElementById('editBookingDate').value = `${y}-${m}-${day}`;
                    } else {
                        document.getElementById('editBookingDate').value = '';
                    }
                }

                const modal = document.getElementById('editBookingModal');
                modal.style.display = 'block';
                modal.classList.add('show');
                document.body.style.overflow = 'hidden';
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Failed to load booking details.');
            });
        }

        function closeEditBookingModal() {
            const modal = document.getElementById('editBookingModal');
            if (modal) {
                modal.style.display = 'none';
                modal.classList.remove('show');
                document.body.style.overflow = 'auto';
            }
        }

        function handleEditBooking(event) {
            event.preventDefault();

            const submitBtn = event.target.querySelector('button[type="submit"]');
            const originalText = submitBtn ? submitBtn.innerHTML : '';
            if (submitBtn) {
                submitBtn.innerHTML = '<span class="material-icons-outlined">hourglass_empty</span> Updating...';
                submitBtn.disabled = true;
            }

            const formData = new FormData(event.target);
            const data = Object.fromEntries(formData.entries());
            data.action = 'edit_booking';

            fetch('', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams(data)
            })
            .then(r => r.json())
            .then(result => {
                if (result.success) {
                    closeEditBookingModal();
                    location.reload();
                } else {
                    alert(result.message || 'Failed to update booking.');
                    if (submitBtn) {
                        submitBtn.innerHTML = originalText;
                        submitBtn.disabled = false;
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Failed to update booking.');
                if (submitBtn) {
                    submitBtn.innerHTML = originalText;
                    submitBtn.disabled = false;
                }
            });
        }

        function updateStatus(bookingId) {
            document.getElementById('updateStatusBookingId').value = bookingId;
            document.getElementById('updateStatusSelect').value = 'pending';

            // Prefill with current status from server (best-effort)
            fetch('', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `action=get_booking&booking_id=${bookingId}`
            })
            .then(r => r.json())
            .then(result => {
                if (result && result.success && result.data && result.data.status) {
                    document.getElementById('updateStatusSelect').value = result.data.status;
                }
            })
            .catch(() => {
                // ignore
            })
            .finally(() => {
                const modal = document.getElementById('updateStatusModal');
                modal.style.display = 'block';
                modal.classList.add('show');
                document.body.style.overflow = 'hidden';
            });
        }

        function closeUpdateStatusModal() {
            const modal = document.getElementById('updateStatusModal');
            if (modal) {
                modal.style.display = 'none';
                modal.classList.remove('show');
                document.body.style.overflow = 'auto';
            }
        }

        function handleUpdateStatus(event) {
            event.preventDefault();

            const submitBtn = event.target.querySelector('button[type="submit"]');
            const originalText = submitBtn ? submitBtn.innerHTML : '';
            if (submitBtn) {
                submitBtn.innerHTML = '<span class="material-icons-outlined">hourglass_empty</span> Updating...';
                submitBtn.disabled = true;
            }

            const formData = new FormData(event.target);
            const bookingId = formData.get('booking_id');
            const status = formData.get('status');

            fetch('', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `action=update_booking_status&booking_id=${encodeURIComponent(bookingId)}&status=${encodeURIComponent(status)}`
            })
            .then(r => r.json())
            .then(result => {
                if (result.success) {
                    closeUpdateStatusModal();
                    location.reload();
                } else {
                    alert(result.message || 'Failed to update booking status.');
                    if (submitBtn) {
                        submitBtn.innerHTML = originalText;
                        submitBtn.disabled = false;
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Failed to update booking status.');
                if (submitBtn) {
                    submitBtn.innerHTML = originalText;
                    submitBtn.disabled = false;
                }
            });
        }

        function acceptBooking(bookingId) {
            fetch('', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `action=update_booking_status&booking_id=${bookingId}&status=confirmed`
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Booking accepted');
                        location.reload();
                    } else {
                        alert(data.message);
                    }
                });
        }

        function rejectBooking(bookingId) {
            if (!confirm('Reject this booking?')) return;
            fetch('', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `action=update_booking_status&booking_id=${bookingId}&status=cancelled`
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Booking rejected');
                        location.reload();
                    } else {
                        alert(data.message);
                    }
                });
        }

        function deleteBooking(bookingId) {
            document.getElementById('deleteBookingId').value = bookingId;
            document.getElementById('deleteBookingLabel').textContent = `Booking #${String(bookingId).padStart(6, '0')}`;

            // Best-effort: add tour name in label
            fetch('', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `action=get_booking&booking_id=${bookingId}`
            })
            .then(r => r.json())
            .then(result => {
                if (result && result.success && result.data && result.data.tour_name) {
                    document.getElementById('deleteBookingLabel').textContent = `Booking #${String(bookingId).padStart(6, '0')} (${result.data.tour_name})`;
                }
            })
            .catch(() => {
                // ignore
            })
            .finally(() => {
                const modal = document.getElementById('deleteBookingModal');
                modal.style.display = 'block';
                modal.classList.add('show');
                document.body.style.overflow = 'hidden';
            });
        }

        function closeDeleteBookingModal() {
            const modal = document.getElementById('deleteBookingModal');
            if (modal) {
                modal.style.display = 'none';
                modal.classList.remove('show');
                document.body.style.overflow = 'auto';
            }
        }

        function handleDeleteBooking(event) {
            event.preventDefault();

            const submitBtn = event.target.querySelector('button[type="submit"]');
            const originalText = submitBtn ? submitBtn.innerHTML : '';
            if (submitBtn) {
                submitBtn.innerHTML = '<span class="material-icons-outlined">hourglass_empty</span> Deleting...';
                submitBtn.disabled = true;
            }

            const formData = new FormData(event.target);
            const bookingId = formData.get('booking_id');

            fetch('', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `action=delete_booking&booking_id=${encodeURIComponent(bookingId)}`
            })
            .then(r => r.json())
            .then(result => {
                if (result.success) {
                    closeDeleteBookingModal();
                    location.reload();
                } else {
                    alert(result.message || 'Failed to delete booking.');
                    if (submitBtn) {
                        submitBtn.innerHTML = originalText;
                        submitBtn.disabled = false;
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Failed to delete booking.');
                if (submitBtn) {
                    submitBtn.innerHTML = originalText;
                    submitBtn.disabled = false;
                }
            });
        }

        function showAddBookingModal() {
            // Implement add booking modal
            console.log('Show add booking modal');
        }

        // Search on Enter key
        document.getElementById('searchInput').addEventListener('keypress', function (e) {
            if (e.key === 'Enter') {
                searchBookings();
            }
        });

        // Close modals when clicking outside
        window.addEventListener('click', function(event) {
            const editModal = document.getElementById('editBookingModal');
            if (event.target === editModal) {
                closeEditBookingModal();
            }

            const statusModal = document.getElementById('updateStatusModal');
            if (event.target === statusModal) {
                closeUpdateStatusModal();
            }

            const deleteModal = document.getElementById('deleteBookingModal');
            if (event.target === deleteModal) {
                closeDeleteBookingModal();
            }
        });

        // Close modals with Escape key
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                const editModal = document.getElementById('editBookingModal');
                if (editModal && editModal.style.display === 'block') {
                    closeEditBookingModal();
                }

                const statusModal = document.getElementById('updateStatusModal');
                if (statusModal && statusModal.style.display === 'block') {
                    closeUpdateStatusModal();
                }

                const deleteModal = document.getElementById('deleteBookingModal');
                if (deleteModal && deleteModal.style.display === 'block') {
                    closeDeleteBookingModal();
                }
            }
        });
    </script>
    <?php closeAdminConnection($conn); ?>
</body>

</html>