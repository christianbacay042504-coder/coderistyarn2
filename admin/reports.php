<?php
// Reports Module
// This file handles reporting functionality with separated connections and functions

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

// Report generation functions
function generateBookingsReport($conn, $startDate, $endDate, $format)
{
    $query = "SELECT b.*, CONCAT(u.first_name, ' ', u.last_name) as user_name 
              FROM bookings b 
              JOIN users u ON b.user_id = u.id 
              WHERE 1=1";

    if ($startDate) {
        $query .= " AND DATE(b.created_at) >= '$startDate'";
    }
    if ($endDate) {
        $query .= " AND DATE(b.created_at) <= '$endDate'";
    }

    $query .= " ORDER BY b.created_at DESC";

    $result = $conn->query($query);
    $bookings = [];
    while ($row = $result->fetch_assoc()) {
        $bookings[] = $row;
    }

    if ($format === 'csv') {
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="bookings_report.csv"');

        $output = fopen('php://output', 'w');
        fputcsv($output, ['ID', 'User', 'Tour Name', 'Date', 'People', 'Amount', 'Status', 'Created']);

        foreach ($bookings as $booking) {
            fputcsv($output, [
                $booking['id'],
                $booking['user_name'],
                $booking['tour_name'],
                $booking['booking_date'],
                $booking['number_of_people'],
                $booking['total_amount'],
                $booking['status'],
                $booking['created_at']
            ]);
        }

        fclose($output);
    } else {
        include 'reports/bookings_report.php';
    }
}

function generateUsersReport($conn, $startDate, $endDate, $format)
{
    $query = "SELECT u.*, 
               (SELECT COUNT(*) FROM bookings WHERE user_id = u.id) as total_bookings,
               (SELECT SUM(total_amount) FROM bookings WHERE user_id = u.id AND status = 'completed') as total_spent
               FROM users u 
               WHERE u.user_type = 'user'";

    if ($startDate) {
        $query .= " AND DATE(u.created_at) >= '$startDate'";
    }
    if ($endDate) {
        $query .= " AND DATE(u.created_at) <= '$endDate'";
    }

    $query .= " ORDER BY u.created_at DESC";

    $result = $conn->query($query);
    $users = [];
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }

    if ($format === 'csv') {
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="users_report.csv"');

        $output = fopen('php://output', 'w');
        fputcsv($output, ['ID', 'Name', 'Email', 'Status', 'Total Bookings', 'Total Spent', 'Joined']);

        foreach ($users as $user) {
            fputcsv($output, [
                $user['id'],
                $user['first_name'] . ' ' . $user['last_name'],
                $user['email'],
                $user['status'],
                $user['total_bookings'],
                $user['total_spent'] ?? 0,
                $user['created_at']
            ]);
        }

        fclose($output);
    } else {
        include 'reports/users_report.php';
    }
}

function generateRevenueReport($conn, $startDate, $endDate, $format)
{
    $query = "SELECT DATE(created_at) as date, 
               COUNT(*) as bookings, 
               SUM(total_amount) as revenue 
               FROM bookings 
               WHERE status = 'completed'";

    if ($startDate) {
        $query .= " AND DATE(created_at) >= '$startDate'";
    }
    if ($endDate) {
        $query .= " AND DATE(created_at) <= '$endDate'";
    }

    $query .= " GROUP BY DATE(created_at) ORDER BY date DESC";

    $result = $conn->query($query);
    $revenue = [];
    while ($row = $result->fetch_assoc()) {
        $revenue[] = $row;
    }

    if ($format === 'csv') {
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="revenue_report.csv"');

        $output = fopen('php://output', 'w');
        fputcsv($output, ['Date', 'Bookings', 'Revenue']);

        foreach ($revenue as $row) {
            fputcsv($output, [$row['date'], $row['bookings'], $row['revenue']]);
        }

        fclose($output);
    } else {
        include 'reports/revenue_report.php';
    }
}

function generateDestinationsReport($conn, $format)
{
    $result = $conn->query("SELECT ts.*, 
                           (SELECT COUNT(*) FROM bookings WHERE tour_name LIKE CONCAT('%', ts.name, '%')) as booking_count 
                           FROM tourist_spots ts 
                           ORDER BY booking_count DESC");

    $destinations = [];
    while ($row = $result->fetch_assoc()) {
        $destinations[] = $row;
    }

    if ($format === 'csv') {
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="destinations_report.csv"');

        $output = fopen('php://output', 'w');
        fputcsv($output, ['ID', 'Name', 'Category', 'Location', 'Rating', 'Bookings', 'Status']);

        foreach ($destinations as $destination) {
            fputcsv($output, [
                $destination['id'],
                $destination['name'],
                $destination['category'],
                $destination['location'],
                $destination['rating'],
                $destination['booking_count'],
                $destination['status']
            ]);
        }

        fclose($output);
    } else {
        include 'reports/destinations_report.php';
    }
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

// Fetch report settings
$rSettings = [];
$result = $conn->query("SELECT setting_key, setting_value FROM report_settings");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $rSettings[$row['setting_key']] = $row['setting_value'];
    }
}

// Common settings
$logoText = $dbSettings['admin_logo_text'] ?? 'SJDM ADMIN';
$moduleTitle = $rSettings['module_title'] ?? 'Reports Management';
$moduleSubtitle = $rSettings['module_subtitle'] ?? 'Generate and download system reports';
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

// Handle report generation
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $reportType = $_POST['report_type'] ?? '';
    $startDate = $_POST['start_date'] ?? '';
    $endDate = $_POST['end_date'] ?? '';
    $format = $_POST['format'] ?? 'html';

    switch ($reportType) {
        case 'bookings':
            generateBookingsReport($conn, $startDate, $endDate, $format);
            exit;
        case 'users':
            generateUsersReport($conn, $startDate, $endDate, $format);
            exit;
        case 'revenue':
            generateRevenueReport($conn, $startDate, $endDate, $format);
            exit;
        case 'destinations':
            generateDestinationsReport($conn, $format);
            exit;
    }
}

// Get statistics
$stats = getAdminStats($conn);

// Map query keys to values for menu badges
$queryValues = [
    'totalUsers' => $stats['totalUsers'],
    'totalBookings' => $stats['totalBookings'],
    'totalGuides' => $stats['totalGuides'],
    'totalDestinations' => $stats['totalDestinations']
];

// Close connection
closeAdminConnection($conn);

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports | SJDM Tours Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet">
    <link rel="stylesheet" href="admin-styles.css">
    <style>
        .reports-container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
        }

        .report-card {
            background: white;
            padding: 30px;
            border-radius: var(--radius-md);
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--border-color);
        }

        .report-card h3 {
            margin: 0 0 20px 0;
            color: var(--text-primary);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .report-card .material-icons-outlined {
            color: var(--primary);
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: var(--text-primary);
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 12px;
            border: 1px solid var(--border-color);
            border-radius: var(--radius-sm);
            font-size: 14px;
        }

        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: var(--primary);
        }

        .date-range {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }

        .format-options {
            display: flex;
            gap: 15px;
            margin-top: 10px;
        }

        .format-option {
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .format-option input[type="radio"] {
            margin: 0;
        }

        .btn-generate {
            width: 100%;
            padding: 12px;
            background: var(--primary);
            color: white;
            border: none;
            border-radius: var(--radius-sm);
            font-weight: 500;
            cursor: pointer;
            transition: background 0.2s;
        }

        .btn-generate:hover {
            background: var(--primary-dark);
        }

        .recent-reports {
            grid-column: 1 / -1;
            background: white;
            padding: 30px;
            border-radius: var(--radius-md);
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--border-color);
        }

        .recent-reports h3 {
            margin: 0 0 20px 0;
            color: var(--text-primary);
        }

        .reports-list {
            display: grid;
            gap: 15px;
        }

        .report-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px;
            background: var(--bg-light);
            border-radius: var(--radius-sm);
        }

        .report-info h4 {
            margin: 0 0 5px 0;
            color: var(--text-primary);
        }

        .report-info p {
            margin: 0;
            color: var(--text-secondary);
            font-size: 14px;
        }

        .report-actions {
            display: flex;
            gap: 10px;
        }

        .btn-small {
            padding: 6px 12px;
            font-size: 12px;
            border: 1px solid var(--border-color);
            background: white;
            border-radius: var(--radius-sm);
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-small:hover {
            background: var(--bg-light);
        }

        .btn-download {
            color: var(--primary);
            border-color: var(--primary);
        }

        .btn-download:hover {
            background: var(--primary);
            color: white;
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
                    <button class="btn-secondary" onclick="scheduleReport()">
                        <span class="material-icons-outlined">schedule</span>
                        Schedule Report
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
                <div class="reports-container">
                    <!-- Bookings Report -->
                    <div class="report-card">
                        <h3>
                            <span class="material-icons-outlined">event</span>
                            Bookings Report
                        </h3>
                        <form method="POST">
                            <input type="hidden" name="report_type" value="bookings">

                            <div class="form-group">
                                <label>Date Range</label>
                                <div class="date-range">
                                    <input type="date" name="start_date" placeholder="Start Date">
                                    <input type="date" name="end_date" placeholder="End Date">
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Format</label>
                                <div class="format-options">
                                    <label class="format-option">
                                        <input type="radio" name="format" value="html" checked>
                                        HTML
                                    </label>
                                    <label class="format-option">
                                        <input type="radio" name="format" value="csv">
                                        CSV
                                    </label>
                                    <label class="format-option">
                                        <input type="radio" name="format" value="pdf">
                                        PDF
                                    </label>
                                </div>
                            </div>

                            <button type="submit" class="btn-generate">
                                <span class="material-icons-outlined">download</span>
                                Generate Report
                            </button>
                        </form>
                    </div>

                    <!-- Users Report -->
                    <div class="report-card">
                        <h3>
                            <span class="material-icons-outlined">people</span>
                            Users Report
                        </h3>
                        <form method="POST">
                            <input type="hidden" name="report_type" value="users">

                            <div class="form-group">
                                <label>Registration Date Range</label>
                                <div class="date-range">
                                    <input type="date" name="start_date" placeholder="Start Date">
                                    <input type="date" name="end_date" placeholder="End Date">
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Format</label>
                                <div class="format-options">
                                    <label class="format-option">
                                        <input type="radio" name="format" value="html" checked>
                                        HTML
                                    </label>
                                    <label class="format-option">
                                        <input type="radio" name="format" value="csv">
                                        CSV
                                    </label>
                                    <label class="format-option">
                                        <input type="radio" name="format" value="pdf">
                                        PDF
                                    </label>
                                </div>
                            </div>

                            <button type="submit" class="btn-generate">
                                <span class="material-icons-outlined">download</span>
                                Generate Report
                            </button>
                        </form>
                    </div>

                    <!-- Revenue Report -->
                    <div class="report-card">
                        <h3>
                            <span class="material-icons-outlined">payments</span>
                            Revenue Report
                        </h3>
                        <form method="POST">
                            <input type="hidden" name="report_type" value="revenue">

                            <div class="form-group">
                                <label>Period</label>
                                <div class="date-range">
                                    <input type="date" name="start_date" placeholder="Start Date">
                                    <input type="date" name="end_date" placeholder="End Date">
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Format</label>
                                <div class="format-options">
                                    <label class="format-option">
                                        <input type="radio" name="format" value="html" checked>
                                        HTML
                                    </label>
                                    <label class="format-option">
                                        <input type="radio" name="format" value="csv">
                                        CSV
                                    </label>
                                    <label class="format-option">
                                        <input type="radio" name="format" value="pdf">
                                        PDF
                                    </label>
                                </div>
                            </div>

                            <button type="submit" class="btn-generate">
                                <span class="material-icons-outlined">download</span>
                                Generate Report
                            </button>
                        </form>
                    </div>

                    <!-- Destinations Report -->
                    <div class="report-card">
                        <h3>
                            <span class="material-icons-outlined">place</span>
                            Destinations Report
                        </h3>
                        <form method="POST">
                            <input type="hidden" name="report_type" value="destinations">

                            <div class="form-group">
                                <label>Include Statistics</label>
                                <select name="include_stats">
                                    <option value="all">All Statistics</option>
                                    <option value="bookings">Booking Count Only</option>
                                    <option value="ratings">Ratings Only</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label>Format</label>
                                <div class="format-options">
                                    <label class="format-option">
                                        <input type="radio" name="format" value="html" checked>
                                        HTML
                                    </label>
                                    <label class="format-option">
                                        <input type="radio" name="format" value="csv">
                                        CSV
                                    </label>
                                    <label class="format-option">
                                        <input type="radio" name="format" value="pdf">
                                        PDF
                                    </label>
                                </div>
                            </div>

                            <button type="submit" class="btn-generate">
                                <span class="material-icons-outlined">download</span>
                                Generate Report
                            </button>
                        </form>
                    </div>

                    <!-- Recent Reports -->
                    <div class="recent-reports">
                        <h3>Recent Reports</h3>
                        <div class="reports-list">
                            <div class="report-item">
                                <div class="report-info">
                                    <h4>Monthly Bookings Report</h4>
                                    <p>Generated on <?php echo date('M j, Y H:i'); ?> • 2.5 MB</p>
                                </div>
                                <div class="report-actions">
                                    <button class="btn-small btn-download">
                                        <span class="material-icons-outlined">download</span>
                                        Download
                                    </button>
                                    <button class="btn-small">
                                        <span class="material-icons-outlined">delete</span>
                                        Delete
                                    </button>
                                </div>
                            </div>

                            <div class="report-item">
                                <div class="report-info">
                                    <h4>User Analytics Report</h4>
                                    <p>Generated on <?php echo date('M j, Y', strtotime('-1 day')); ?> • 1.8 MB</p>
                                </div>
                                <div class="report-actions">
                                    <button class="btn-small btn-download">
                                        <span class="material-icons-outlined">download</span>
                                        Download
                                    </button>
                                    <button class="btn-small">
                                        <span class="material-icons-outlined">delete</span>
                                        Delete
                                    </button>
                                </div>
                            </div>

                            <div class="report-item">
                                <div class="report-info">
                                    <h4>Revenue Summary Q1 2026</h4>
                                    <p>Generated on <?php echo date('M j, Y', strtotime('-3 days')); ?> • 3.2 MB</p>
                                </div>
                                <div class="report-actions">
                                    <button class="btn-small btn-download">
                                        <span class="material-icons-outlined">download</span>
                                        Download
                                    </button>
                                    <button class="btn-small">
                                        <span class="material-icons-outlined">delete</span>
                                        Delete
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script src="admin-script.js"></script>
    <script>
        function scheduleReport() {
            // Implement schedule report modal
            console.log('Schedule report functionality');
            alert('Schedule report functionality will be implemented soon.');
        }

        // Set default date ranges
        document.addEventListener('DOMContentLoaded', function () {
            const today = new Date();
            const lastMonth = new Date(today.getFullYear(), today.getMonth() - 1, today.getDate());

            const dateInputs = document.querySelectorAll('input[type="date"]');
            dateInputs.forEach(input => {
                if (input.name === 'start_date') {
                    input.valueAsDate = lastMonth;
                } else if (input.name === 'end_date') {
                    input.valueAsDate = today;
                }
            });
        });
    </script>
    <?php closeAdminConnection($conn); ?>
</body>

</html>