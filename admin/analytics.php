<?php
// Analytics Module
// This file handles analytics and reporting with separated connections and functions

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
    $stats['totalUsers'] = $result ? $result->fetch_assoc()['total'] : 0;

    // Active users
    $result = $conn->query("SELECT COUNT(*) as total FROM users WHERE user_type = 'user' AND status = 'active'");
    $stats['activeUsers'] = $result ? $result->fetch_assoc()['total'] : 0;

    // Total bookings
    $result = $conn->query("SELECT COUNT(*) as total FROM bookings");
    $stats['totalBookings'] = $result ? $result->fetch_assoc()['total'] : 0;

    // Today's logins
    $result = $conn->query("SELECT COUNT(DISTINCT user_id) as total FROM login_activity WHERE DATE(login_time) = CURDATE() AND status = 'success'");
    $stats['todayLogins'] = $result ? $result->fetch_assoc()['total'] : 0;

    // Total guides
    $result = $conn->query("SELECT COUNT(*) as total FROM tour_guides");
    $stats['totalGuides'] = $result ? $result->fetch_assoc()['total'] : 0;

    // Total destinations
    $result = $conn->query("SELECT COUNT(*) as total FROM tourist_spots");
    $stats['totalDestinations'] = $result ? $result->fetch_assoc()['total'] : 0;

    return $stats;
}

function getBookingStats($conn)
{
    $stats = [];

    // Total bookings
    $result = $conn->query("SELECT COUNT(*) as total FROM bookings");
    $stats['total'] = $result ? $result->fetch_assoc()['total'] : 0;

    // Bookings by status
    $result = $conn->query("SELECT status, COUNT(*) as count FROM bookings GROUP BY status");
    $stats['by_status'] = [];
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $stats['by_status'][$row['status']] = $row['count'];
        }
    }

    // Today's bookings
    $result = $conn->query("SELECT COUNT(*) as total FROM bookings WHERE DATE(created_at) = CURDATE()");
    $stats['today'] = $result ? $result->fetch_assoc()['total'] : 0;

    // This month's bookings
    $result = $conn->query("SELECT COUNT(*) as total FROM bookings WHERE MONTH(created_at) = MONTH(CURDATE()) AND YEAR(created_at) = YEAR(CURDATE())");
    $stats['this_month'] = $result ? $result->fetch_assoc()['total'] : 0;

    // Total revenue
    $result = $conn->query("SELECT SUM(total_amount) as total FROM bookings WHERE status = 'confirmed'");
    $stats['total_revenue'] = $result ? ($result->fetch_assoc()['total'] ?? 0) : 0;

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

// Fetch analytics settings
$anSettings = [];
$result = $conn->query("SELECT setting_key, setting_value FROM analytics_settings");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $anSettings[$row['setting_key']] = $row['setting_value'];
    }
}

// Common settings
$logoText = $dbSettings['admin_logo_text'] ?? 'SJDM ADMIN';
$moduleTitle = $anSettings['module_title'] ?? 'Analytics Dashboard';
$moduleSubtitle = $anSettings['module_subtitle'] ?? 'System performance and insights';
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

// Get statistics
$stats = getAdminStats($conn);
$bookingStats = getBookingStats($conn);

// Get additional analytics data
$analytics = [];

// Monthly revenue (last 6 months)
$monthlyRevenue = [];
for ($i = 5; $i >= 0; $i--) {
    $month = date('Y-m', strtotime("-$i months"));
    $result = $conn->query("SELECT SUM(total_amount) as revenue, COUNT(*) as bookings FROM bookings WHERE DATE_FORMAT(created_at, '%Y-%m') = '$month' AND status = 'confirmed'");
    $data = $result ? $result->fetch_assoc() : ['revenue' => 0, 'bookings' => 0];
    $monthlyRevenue[] = [
        'month' => date('M Y', strtotime($month)),
        'revenue' => $data['revenue'] ?? 0,
        'bookings' => $data['bookings'] ?? 0
    ];
}

// User registration trends
$userTrends = [];
for ($i = 11; $i >= 0; $i--) {
    $month = date('Y-m', strtotime("-$i months"));
    $result = $conn->query("SELECT COUNT(*) as users FROM users WHERE DATE_FORMAT(created_at, '%Y-%m') = '$month' AND user_type = 'user'");
    $data = $result ? $result->fetch_assoc() : ['users' => 0];
    $userTrends[] = [
        'month' => date('M Y', strtotime($month)),
        'users' => $data['users'] ?? 0
    ];
}

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
    <title>Analytics | SJDM Tours Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet">
    <link rel="stylesheet" href="admin-styles.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        /* Enhanced Stats Cards */
        .analytics-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            border-radius: 20px;
            padding: 0;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.08);
            border: 1px solid rgba(0, 0, 0, 0.04);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
            display: flex;
            align-items: center;
            gap: 20px;
            min-height: 120px;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            opacity: 0;
            transition: opacity 0.3s ease;
            border-radius: 20px;
        }

        .stat-card:hover {
            transform: translateY(-8px) scale(1.02);
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.12);
        }

        .stat-card:hover::before {
            opacity: 1;
        }

        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 0;
            position: relative;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            flex-shrink: 0;
            box-shadow: 
                0 4px 12px rgba(0, 0, 0, 0.1),
                0 2px 6px rgba(0, 0, 0, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .stat-icon::before {
            content: '';
            position: absolute;
            top: -2px;
            left: -2px;
            right: -2px;
            bottom: -2px;
            border-radius: 24px;
            background: linear-gradient(45deg, transparent, rgba(255, 255, 255, 0.1), transparent);
            z-index: -1;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

            .stat-card-info::before {
                background: linear-gradient(135deg, rgba(59, 130, 246, 0.1), rgba(30, 64, 175, 0.1));
            }

            .stat-meta {
                font-size: 0.8rem;
                color: rgba(255, 255, 255, 0.9);
                margin: 4px 0 0;
                font-weight: 500;
            }

            .stat-trend {
                display: flex;
                align-items: center;
                gap: 6px;
                font-size: 0.8rem;
                font-weight: 600;
                margin-top: 8px;
            }
                0 12px 35px rgba(0, 0, 0, 0.15),
                0 6px 18px rgba(0, 0, 0, 0.08);
        }

        .stat-card:hover .stat-icon .material-icons-outlined {
            transform: scale(1.1) rotate(5deg);
            text-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
        }

        .stat-content {
            flex: 1;
            z-index: 2;
            position: relative;
        }

        .stat-content h3 {
            font-size: 1.8rem;
            font-weight: 700;
            margin: 0 0 4px 0;
            line-height: 1.1;
            transition: all 0.3s ease;
        }

        .stat-content p {
            font-size: 0.8rem;
            color: var(--text-secondary);
            margin: 0;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Stat Card Variants */
        .stat-card-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .stat-card-primary .stat-icon {
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
        }

        .stat-card-primary::before {
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.1), rgba(118, 75, 162, 0.1));
        }

        .stat-card-success {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
        }

        .stat-card-success .stat-icon {
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
        }

        .stat-card-success::before {
            background: linear-gradient(135deg, rgba(16, 185, 129, 0.1), rgba(5, 150, 105, 0.1));
        }

        .stat-card-warning {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            color: white;
        }

        .stat-card-warning .stat-icon {
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
        }

        .stat-card-warning::before {
            background: linear-gradient(135deg, rgba(245, 158, 11, 0.1), rgba(217, 119, 6, 0.1));
        }

        .stat-card-info {
            background: linear-gradient(135deg, #3b82f6 0%, #1e40af 100%);
            color: white;
        }

        .stat-card-info .stat-icon {
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
        }

        .stat-card-info::before {
            background: linear-gradient(135deg, rgba(59, 130, 246, 0.1), rgba(30, 64, 175, 0.1));
        }

        .stat-meta {
            font-size: 0.8rem;
            color: rgba(255, 255, 255, 0.9);
            margin: 4px 0 0;
            font-weight: 500;
        }

        .stat-trend {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 0.8rem;
            font-weight: 600;
            margin-top: 8px;
        }

        .stat-trend.positive {
            color: rgba(255, 255, 255, 0.9);
        }

        .stat-trend.negative {
            color: rgba(255, 255, 255, 0.9);
        }

        .stat-trend .material-icons-outlined {
            font-size: 16px;
        }

        .stat-progress {
            margin-top: 12px;
        }

        .progress-bar {
            width: 100%;
            height: 8px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 4px;
            overflow: hidden;
        }

        .progress-fill {
            height: 100%;
            border-radius: 4px;
            transition: width 0.3s ease;
        }

        .progress-text {
            font-size: 0.75rem;
            color: var(--text-secondary);
            font-weight: 500;
            margin-top: 6px;
        }

        /* Enhanced Chart Containers */
        .chart-container {
            background: white;
            border-radius: 24px;
            padding: 32px;
            box-shadow: 
                0 20px 60px rgba(0, 0, 0, 0.08),
                0 8px 24px rgba(0, 0, 0, 0.04),
                inset 0 1px 0 rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(0, 0, 0, 0.06);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }

        .chart-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--primary), var(--secondary));
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .chart-container:hover {
            transform: translateY(-8px);
            box-shadow: 
                0 30px 80px rgba(0, 0, 0, 0.12),
                0 12px 35px rgba(0, 0, 0, 0.06),
                inset 0 1px 0 rgba(255, 255, 255, 0.2);
        }

        .chart-container:hover::before {
            opacity: 1;
        }

        .chart-container h3 {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--text-primary);
            margin: 0 0 24px 0;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .chart-container h3::before {
            content: '';
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 20px;
        }

        .chart-wrapper {
            position: relative;
            height: 300px;
            background: linear-gradient(135deg, #f8fafc 0%, #ffffff 100%);
            border-radius: 16px;
            padding: 20px;
            border: 1px solid rgba(0, 0, 0, 0.04);
        }

        /* Enhanced Chart.js Customizations */
        .chart-wrapper canvas {
            border-radius: 12px;
        }

        /* Chart Grid Styling */
        .chart-container:hover .chart-wrapper {
            background: linear-gradient(135deg, #f1f5f9 0%, #ffffff 100%);
            border-color: rgba(44, 95, 45, 0.1);
        }

        /* Chart Legend Styling */
        .chart-legend {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-top: 20px;
            flex-wrap: wrap;
        }

        .legend-item {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 8px 16px;
            background: rgba(255, 255, 255, 0.8);
            border-radius: 20px;
            border: 1px solid rgba(0, 0, 0, 0.1);
            font-size: 0.85rem;
            font-weight: 500;
            color: var(--text-secondary);
            transition: all 0.3s ease;
        }

        .legend-item:hover {
            background: rgba(255, 255, 255, 1);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .legend-color {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            border: 2px solid white;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        /* Chart Tooltips */
        .chart-tooltip {
            background: rgba(0, 0, 0, 0.9);
            color: white;
            padding: 12px 16px;
            border-radius: 12px;
            font-size: 0.85rem;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.2);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        /* Chart Loading Animation */
        @keyframes chartPulse {
            0% { opacity: 0.6; }
            50% { opacity: 1; }
            100% { opacity: 0.6; }
        }

        .chart-loading {
            animation: chartPulse 2s ease-in-out infinite;
        }

        /* Chart Grid Lines */
        .chart-grid-line {
            stroke: rgba(0, 0, 0, 0.05);
            stroke-width: 1;
            stroke-dasharray: 5, 5;
        }

        /* Chart Axis Labels */
        .chart-axis-label {
            color: var(--text-secondary);
            font-size: 0.8rem;
            font-weight: 500;
        }

        /* Chart Data Point Animation */
        @keyframes dataPointPulse {
            0% { transform: scale(1); opacity: 0.8; }
            50% { transform: scale(1.2); opacity: 1; }
            100% { transform: scale(1); opacity: 0.8; }
        }

        .chart-data-point {
            animation: dataPointPulse 2s ease-in-out infinite;
        }
    </style>
</head>

<body>
    <div class="admin-container">
        <!-- Sidebar -->
       <aside class="sidebar">

            <div class="sidebar-header">

                <div class="logo" style="display: flex; align-items: center; gap: 12px;">

                    <img src="../lgo.png" alt="SJDM Tours Logo" style="height: 40px; width: 40px; object-fit: contain; border-radius: 8px;">

                    <span>SJDM ADMIN</span>

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
                <!-- Key Metrics -->
                <div class="analytics-grid">
                    <div class="stat-card stat-card-primary">
                        <div class="stat-icon">
                            <span class="material-icons-outlined">groups</span>
                        </div>
                        <div class="stat-content">
                            <h3><?php echo $stats['totalUsers']; ?></h3>
                            <p>Total Users</p>
                            <div class="stat-meta">Registered accounts</div>
                        </div>
                    </div>
                    <div class="stat-card stat-card-success">
                        <div class="stat-icon">
                            <span class="material-icons-outlined">book</span>
                        </div>
                        <div class="stat-content">
                            <h3><?php echo $stats['totalBookings']; ?></h3>
                            <p>Total Bookings</p>
                            <div class="stat-meta">All time bookings</div>
                        </div>
                    </div>
                    <div class="stat-card stat-card-warning">
                        <div class="stat-icon">
                            <span class="material-icons-outlined">payments</span>
                        </div>
                        <div class="stat-content">
                            <h3>₱<?php echo number_format($bookingStats['total_revenue'], 2); ?></h3>
                            <p>Total Revenue</p>
                            <div class="stat-meta">All-time earnings</div>

                        </div>
                    </div>
                    <div class="stat-card stat-card-info">
                        <div class="stat-icon">
                            <span class="material-icons-outlined">tour</span>
                        </div>
                        <div class="stat-content">
                            <h3><?php echo $stats['totalDestinations']; ?></h3>
                            <p>Destinations</p>
                            <div class="stat-meta">Available tours</div>

                        </div>
                    </div>
                </div>

                <!-- Charts Row 1 -->
                <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 20px; margin-bottom: 30px;">
                    <!-- Monthly Revenue Chart -->
                    <div class="chart-container">
                        <h3>Monthly Revenue Trend</h3>
                        <div class="chart-wrapper">
                            <canvas id="revenueChart"></canvas>
                        </div>
                    </div>

                    <!-- Booking Status Pie Chart -->
                    <div class="chart-container">
                        <h3>Booking Status</h3>
                        <div class="chart-wrapper">
                            <canvas id="statusChart"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Charts Row 2 -->
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 30px;">
                    <!-- User Registration Trend -->
                    <div class="chart-container">
                        <h3>User Registration Trend</h3>
                        <div class="chart-wrapper">
                            <canvas id="userTrendChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script src="admin-script.js"></script>
    <script src="admin-profile-dropdown.js"></script>
    <script>
        // Monthly Revenue Chart
        const revenueCtx = document.getElementById('revenueChart').getContext('2d');
        const revenueChart = new Chart(revenueCtx, {
            type: 'line',
            data: {
                labels: <?php echo json_encode(array_column($monthlyRevenue, 'month')); ?>,
                datasets: [{
                    label: 'Revenue (₱)',
                    data: <?php echo json_encode(array_column($monthlyRevenue, 'revenue')); ?>,
                    borderColor: '#3b82f6',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    borderWidth: 3,
                    tension: 0.4,
                    fill: true,
                    pointRadius: 6,
                    pointHoverRadius: 8,
                    pointBackgroundColor: '#3b82f6',
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 2,
                    pointHoverBackgroundColor: '#2563eb',
                    pointHoverBorderColor: '#ffffff',
                    pointHoverBorderWidth: 3
                }, {
                    label: 'Bookings',
                    data: <?php echo json_encode(array_column($monthlyRevenue, 'bookings')); ?>,
                    borderColor: '#10b981',
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    borderWidth: 3,
                    tension: 0.4,
                    fill: true,
                    pointRadius: 6,
                    pointHoverRadius: 8,
                    pointBackgroundColor: '#10b981',
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 2,
                    pointHoverBackgroundColor: '#059669',
                    pointHoverBorderColor: '#ffffff',
                    pointHoverBorderWidth: 3,
                    yAxisID: 'y1'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                        labels: {
                            usePointStyle: true,
                            padding: 20,
                            font: {
                                size: 12,
                                weight: '600'
                            },
                            color: '#64748b'
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.9)',
                        titleColor: '#ffffff',
                        bodyColor: '#ffffff',
                        padding: 12,
                        cornerRadius: 12,
                        displayColors: true,
                        borderColor: 'rgba(255, 255, 255, 0.1)',
                        borderWidth: 1,
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.parsed.y !== null) {
                                    if (context.datasetIndex === 0) {
                                        label += '₱' + context.parsed.y.toLocaleString();
                                    } else {
                                        label += context.parsed.y.toLocaleString() + ' bookings';
                                    }
                                }
                                return label;
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            color: '#64748b',
                            font: {
                                size: 11,
                                weight: '500'
                            }
                        }
                    },
                    y: {
                        type: 'linear',
                        display: true,
                        position: 'left',
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)',
                            borderDash: [5, 5]
                        },
                        ticks: {
                            color: '#64748b',
                            font: {
                                size: 11,
                                weight: '500'
                            },
                            callback: function(value) {
                                return '₱' + value.toLocaleString();
                            }
                        }
                    },
                    y1: {
                        type: 'linear',
                        display: true,
                        position: 'right',
                        grid: {
                            drawOnChartArea: false,
                        },
                        ticks: {
                            color: '#64748b',
                            font: {
                                size: 11,
                                weight: '500'
                            },
                            callback: function(value) {
                                return value.toLocaleString();
                            }
                        }
                    }
                },
                animation: {
                    duration: 2000,
                    easing: 'easeInOutQuart'
                }
            }
        });

        // Booking Status Chart
        const statusCtx = document.getElementById('statusChart').getContext('2d');
        const statusChart = new Chart(statusCtx, {
            type: 'doughnut',
            data: {
                labels: <?php echo json_encode(array_keys($bookingStats['by_status'])); ?>,
                datasets: [{
                    data: <?php echo json_encode(array_values($bookingStats['by_status'])); ?>,
                    backgroundColor: [
                        '#f59e0b',
                        '#3b82f6', 
                        '#10b981',
                        '#ef4444'
                    ],
                    borderWidth: 3,
                    borderColor: '#ffffff',
                    hoverOffset: 8,
                    hoverBorderWidth: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'bottom',
                        labels: {
                            usePointStyle: true,
                            padding: 20,
                            font: {
                                size: 12,
                                weight: '600'
                            },
                            color: '#64748b'
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.9)',
                        titleColor: '#ffffff',
                        bodyColor: '#ffffff',
                        padding: 12,
                        cornerRadius: 12,
                        displayColors: true,
                        borderColor: 'rgba(255, 255, 255, 0.1)',
                        borderWidth: 1,
                        callbacks: {
                            label: function(context) {
                                let label = context.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.parsed !== null) {
                                    label += context.parsed.toLocaleString() + ' bookings';
                                }
                                return label;
                            }
                        }
                    }
                },
                animation: {
                    animateRotate: true,
                    animateScale: true,
                    duration: 2000,
                    easing: 'easeInOutQuart'
                },
                cutout: '60%'
            }
        });

        // User Registration Trend
        const userTrendCtx = document.getElementById('userTrendChart').getContext('2d');
        const userTrendChart = new Chart(userTrendCtx, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode(array_column($userTrends, 'month')); ?>,
                datasets: [{
                    label: 'New Users',
                    data: <?php echo json_encode(array_column($userTrends, 'users')); ?>,
                    backgroundColor: 'rgba(139, 92, 246, 0.8)',
                    borderColor: '#8b5cf6',
                    borderWidth: 2,
                    borderRadius: 8,
                    hoverBackgroundColor: '#2563eb',
                    hoverBorderColor: '#1e40af',
                    hoverBorderWidth: 3
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                        labels: {
                            usePointStyle: true,
                            padding: 20,
                            font: {
                                size: 12,
                                weight: '600'
                            },
                            color: '#64748b'
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.9)',
                        titleColor: '#ffffff',
                        bodyColor: '#ffffff',
                        padding: 12,
                        cornerRadius: 12,
                        displayColors: true,
                        borderColor: 'rgba(255, 255, 255, 0.1)',
                        borderWidth: 1,
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.parsed.y !== null) {
                                    label += context.parsed.y.toLocaleString() + ' users';
                                }
                                return label;
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            color: '#64748b',
                            font: {
                                size: 11,
                                weight: '500'
                            }
                        }
                    },
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)',
                            borderDash: [5, 5]
                        },
                        ticks: {
                            color: '#64748b',
                            font: {
                                size: 11,
                                weight: '500'
                            },
                            callback: function(value) {
                                return value.toLocaleString();
                            }
                        }
                    }
                },
                animation: {
                    duration: 2000,
                    easing: 'easeInOutQuart',
                    onComplete: function() {
                        // Add pulse effect to bars
                        const chart = this.chart;
                        const meta = chart.getDatasetMeta(0);
                        meta.controller = new Chart.controllers.bar.prototype;
                        Object.assign(meta.controller, {
                            draw: function(ease) {
                                const originalDraw = Chart.controllers.bar.prototype.draw;
                                return function(chartInstance) {
                                    const context = chartInstance.ctx;
                                    const meta = chartInstance.getDatasetMeta(0);
                                    
                                    // Draw original bars
                                    originalDraw.apply(this, arguments);
                                    
                                    // Add pulse effect to bars
                                    context.save();
                                    context.globalAlpha = 0.1;
                                    context.fillStyle = '#8b5cf6';
                                    
                                    chartInstance.data.datasets[0].data.forEach((value, index) => {
                                        const bar = chartInstance.getDatasetMeta(0).data[index];
                                        if (bar) {
                                            const x = bar.x;
                                            const y = bar.y;
                                            const width = bar.width;
                                            const height = bar.height;
                                            
                                            // Draw glow effect
                                            context.shadowColor = '#8b5cf6';
                                            context.shadowBlur = 10;
                                            context.shadowOffsetX = 0;
                                            context.shadowOffsetY = 0;
                                            
                                            context.fillRect(x, y, width, height);
                                        }
                                    });
                                    
                                    context.restore();
                                };
                            }
                        });
                    }
                }
            }
        });

        function exportAnalytics() {
            // Implement export functionality
            console.log('Export analytics data');
            alert('Export functionality will be implemented soon.');
        }
    </script>
    <?php closeAdminConnection($conn); ?>
</body>

</html>