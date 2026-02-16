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
            width: 70px;
            height: 70px;
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 0;
            position: relative;
            transition: all 0.3s ease;
            flex-shrink: 0;
        }

        .stat-icon .material-icons-outlined {
            font-size: 32px;
            color: white;
            z-index: 2;
            position: relative;
        }

        .stat-content {
            flex: 1;
            z-index: 2;
            position: relative;
        }

        .stat-content h3 {
            font-size: 2.2rem;
            font-weight: 800;
            margin: 0 0 4px 0;
            line-height: 1.1;
            transition: all 0.3s ease;
        }

        .stat-content p {
            font-size: 0.9rem;
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
                    <div class="stat-card">
                        <h3><?php echo $stats['totalUsers']; ?></h3>
                        <p>Total Users</p>
                        <small class="trend-up">
                            <span class="material-icons-outlined">trending_up</span>
                            12% growth
                        </small>
                    </div>
                    <div class="stat-card">
                        <h3><?php echo $stats['totalBookings']; ?></h3>
                        <p>Total Bookings</p>
                        <small class="trend-up">
                            <span class="material-icons-outlined">trending_up</span>
                            8% increase
                        </small>
                    </div>
                    <div class="stat-card stat-card-warning">
                        <div class="stat-icon">
                            <span class="material-icons-outlined">payments</span>
                        </div>
                        <div class="stat-content">
                            <h3>₱<?php echo number_format($bookingStats['total_revenue'], 2); ?></h3>
                            <p>Total Revenue</p>
                            <div class="stat-meta">All-time earnings</div>
                            <div class="stat-trend positive">
                                <span class="material-icons-outlined">trending_up</span>
                                <span>15% growth</span>
                            </div>
                        </div>
                    </div>
                    <div class="stat-card">
                        <h3><?php echo $stats['totalDestinations']; ?></h3>
                        <p>Destinations</p>
                        <small class="trend-up">
                            <span class="material-icons-outlined">trending_up</span>
                            3 new this month
                        </small>
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
                    tension: 0.4
                }, {
                    label: 'Bookings',
                    data: <?php echo json_encode(array_column($monthlyRevenue, 'bookings')); ?>,
                    borderColor: '#10b981',
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    tension: 0.4,
                    yAxisID: 'y1'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        type: 'linear',
                        display: true,
                        position: 'left',
                    },
                    y1: {
                        type: 'linear',
                        display: true,
                        position: 'right',
                        grid: {
                            drawOnChartArea: false,
                        },
                    }
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
                    backgroundColor: ['#f59e0b', '#3b82f6', '#10b981', '#ef4444']
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
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
                    backgroundColor: '#8b5cf6'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
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