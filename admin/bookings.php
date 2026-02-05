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
        $stmt = $conn->prepare("SELECT b.*, CONCAT(u.first_name, ' ', u.last_name) as user_name FROM bookings b JOIN users u ON b.user_id = u.id WHERE b.id = ?");
        $stmt->bind_param("i", $bookingId);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            return ['success' => true, 'data' => $result->fetch_assoc()];
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
    $search = $conn->real_escape_string($search);

    // Get bookings with pagination
    $bookingsQuery = "SELECT b.*, CONCAT(u.first_name, ' ', u.last_name) as user_name, u.email as user_email
                      FROM bookings b 
                      JOIN users u ON b.user_id = u.id 
                      WHERE 1=1";

    if ($search) {
        $bookingsQuery .= " AND (b.tour_name LIKE '%$search%' OR u.first_name LIKE '%$search%' OR u.last_name LIKE '%$search%')";
    }

    if ($status) {
        $bookingsQuery .= " AND b.status = '$status'";
    }

    $bookingsQuery .= " ORDER BY b.created_at DESC LIMIT $limit OFFSET $offset";
    $bookingsResult = $conn->query($bookingsQuery);

    // Get total count for pagination
    $countQuery = "SELECT COUNT(*) as total FROM bookings b JOIN users u ON b.user_id = u.id WHERE 1=1";
    if ($search) {
        $countQuery .= " AND (b.tour_name LIKE '%$search%' OR u.first_name LIKE '%$search%' OR u.last_name LIKE '%$search%')";
    }
    if ($status) {
        $countQuery .= " AND b.status = '$status'";
    }
    $countResult = $conn->query($countQuery);
    $totalCount = $countResult->fetch_assoc()['total'];
    $totalPages = ceil($totalCount / $limit);

    $bookings = [];
    while ($row = $bookingsResult->fetch_assoc()) {
        $bookings[] = $row;
    }

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
    $result = $conn->query("SELECT SUM(total_amount) as total FROM bookings WHERE status = 'completed'");
    $stats['total_revenue'] = $result->fetch_assoc()['total'] ?? 0;

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
    <style>
        .booking-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: var(--bg-light);
            padding: 20px;
            border-radius: var(--radius-md);
            border-left: 4px solid var(--primary);
        }

        .stat-card h3 {
            margin: 0 0 10px 0;
            font-size: 2rem;
            color: var(--primary);
        }

        .stat-card p {
            margin: 0;
            color: var(--text-secondary);
        }

        .search-filters {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }

        .search-filters input {
            flex: 1;
            padding: 10px;
            border: 1px solid var(--border-color);
            border-radius: var(--radius-sm);
            min-width: 200px;
        }

        .search-filters select {
            padding: 10px;
            border: 1px solid var(--border-color);
            border-radius: var(--radius-sm);
            background: white;
        }

        .booking-table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: var(--radius-md);
            overflow: hidden;
            box-shadow: var(--shadow-sm);
        }

        .booking-table th,
        .booking-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid var(--border-color);
        }

        .booking-table th {
            background: var(--bg-light);
            font-weight: 600;
        }

        .booking-table tr:hover {
            background: var(--bg-light);
        }

        .status-badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 0.8rem;
            font-weight: 500;
        }

        .status-pending {
            background: #fef3c7;
            color: #92400e;
        }

        .status-confirmed {
            background: #dbeafe;
            color: #1e40af;
        }

        .status-completed {
            background: #d1fae5;
            color: #065f46;
        }

        .status-cancelled {
            background: #fee2e2;
            color: #991b1b;
        }

        .action-buttons {
            display: flex;
            gap: 5px;
        }

        .btn-icon {
            padding: 6px;
            border: none;
            background: transparent;
            cursor: pointer;
            border-radius: 4px;
            transition: background 0.2s;
        }

        .btn-icon:hover {
            background: var(--bg-light);
        }

        .pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
            margin-top: 20px;
        }

        .pagination button {
            padding: 8px 12px;
            border: 1px solid var(--border-color);
            background: white;
            cursor: pointer;
            border-radius: 4px;
        }

        .pagination button:hover {
            background: var(--bg-light);
        }

        .pagination button.active {
            background: var(--primary);
            color: white;
            border-color: var(--primary);
        }

        .booking-amount {
            font-weight: 600;
            color: var(--primary);
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .user-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: var(--bg-light);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            color: var(--text-secondary);
        }
    </style>
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
                    <button class="btn-primary" onclick="showAddBookingModal()">
                        <span class="material-icons-outlined">add</span>
                        Add Booking
                    </button>

                    <div class="profile-dropdown">
                        <div class="profile-dropdown-toggle">
                            <div class="avatar">
                                <span><?php echo strtoupper(substr($currentUser['first_name'], 0, 1)); ?></span>
                                <div class="admin-mark-badge"><?php echo $adminInfo['admin_mark'] ?? 'A'; ?></div>
                            </div>
                            <div class="user-info">
                                <p class="user-name">
                                    <?php echo htmlspecialchars($currentUser['first_name'] . ' ' . $currentUser['last_name']); ?>
                                </p>
                                <p class="user-role"><?php echo $adminInfo['role_title']; ?></p>
                            </div>
                            <span class="material-icons-outlined dropdown-arrow">expand_more</span>
                        </div>
                    </div>
                </div>
            </header>

            <div class="content-area">
                <!-- Booking Statistics -->
                <div class="booking-stats">
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
                <div class="search-filters">
                    <input type="text" id="searchInput" placeholder="Search bookings by tour name or user..."
                        value="<?php echo htmlspecialchars($search); ?>">
                    <select id="statusFilter" onchange="filterBookings()">
                        <option value="">All Status</option>
                        <option value="pending" <?php echo $status == 'pending' ? 'selected' : ''; ?>>Pending</option>
                        <option value="confirmed" <?php echo $status == 'confirmed' ? 'selected' : ''; ?>>Confirmed
                        </option>
                        <option value="completed" <?php echo $status == 'completed' ? 'selected' : ''; ?>>Completed
                        </option>
                        <option value="cancelled" <?php echo $status == 'cancelled' ? 'selected' : ''; ?>>Cancelled
                        </option>
                    </select>
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
                    <table class="booking-table">
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
                                            <button class="btn-icon" onclick="viewBooking(<?php echo $booking['id']; ?>)"
                                                title="View">
                                                <span class="material-icons-outlined">visibility</span>
                                            </button>
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

    <script src="admin-script.js"></script>
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

        function viewBooking(bookingId) {
            // Implement view booking modal
            console.log('View booking:', bookingId);
        }

        function editBooking(bookingId) {
            // Implement edit booking modal
            console.log('Edit booking:', bookingId);
        }

        function updateStatus(bookingId) {
            const newStatus = prompt('Enter new status (pending, confirmed, completed, cancelled):');
            if (newStatus && ['pending', 'confirmed', 'completed', 'cancelled'].includes(newStatus)) {
                fetch('', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `action=update_booking_status&booking_id=${bookingId}&status=${newStatus}`
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert(data.message);
                            location.reload();
                        } else {
                            alert(data.message);
                        }
                    });
            }
        }

        function deleteBooking(bookingId) {
            if (confirm('Are you sure you want to delete this booking? This action cannot be undone.')) {
                fetch('', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `action=delete_booking&booking_id=${bookingId}`
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert(data.message);
                            location.reload();
                        } else {
                            alert(data.message);
                        }
                    });
            }
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
    </script>
    <?php closeAdminConnection($conn); ?>
</body>

</html>