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

// Top destinations by bookings
$topDestinations = [];
$result = $conn->query("SELECT ts.name, COUNT(b.id) as booking_count FROM tourist_spots ts LEFT JOIN bookings b ON b.status = 'confirmed' AND b.tour_name LIKE CONCAT('%', ts.name, '%') COLLATE utf8mb4_general_ci GROUP BY ts.id ORDER BY booking_count DESC LIMIT 10");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $topDestinations[] = $row;
    }
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

// Popular tour categories
$popularCategories = [];
$result = $conn->query("SELECT category, COUNT(*) as count FROM tourist_spots WHERE status = 'active' GROUP BY category ORDER BY count DESC");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $popularCategories[] = $row;
    }
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
        .analytics-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
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

        .chart-container {
            background: white;
            padding: 20px;
            border-radius: var(--radius-md);
            box-shadow: var(--shadow-sm);
            margin-bottom: 30px;
        }

        .chart-container h3 {
            margin: 0 0 20px 0;
            color: var(--text-primary);
        }

        .chart-wrapper {
            position: relative;
            height: 300px;
        }

        .analytics-table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: var(--radius-md);
            overflow: hidden;
            box-shadow: var(--shadow-sm);
        }

        .analytics-table th,
        .analytics-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid var(--border-color);
        }

        .analytics-table th {
            background: var(--bg-light);
            font-weight: 600;
        }

        .analytics-table tr:hover {
            background: var(--bg-light);
        }

        .trend-up {
            color: #10b981;
        }

        .trend-down {
            color: #ef4444;
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
                    // Skip hotels and settings menu items
                    if (stripos($item['menu_name'], 'hotels') !== false || stripos($item['menu_url'], 'hotels') !== false || stripos($item['menu_name'], 'settings') !== false || stripos($item['menu_url'], 'settings') !== false) {
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
                    <button class="btn-secondary" onclick="exportAnalytics()">
                        <span class="material-icons-outlined">download</span>
                        Export Report
                    </button>
                    
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
                    <div class="stat-card">
                        <h3>₱<?php echo number_format($bookingStats['total_revenue'], 2); ?></h3>
                        <p>Total Revenue</p>
                        <small class="trend-up">
                            <span class="material-icons-outlined">trending_up</span>
                            15% growth
                        </small>
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

                    <!-- Popular Categories -->
                    <div class="chart-container">
                        <h3>Popular Categories</h3>
                        <div class="chart-wrapper">
                            <canvas id="categoriesChart"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Top Destinations Table -->
                <div class="chart-container">
                    <h3>Top Destinations by Bookings</h3>
                    <table class="analytics-table">
                        <thead>
                            <tr>
                                <th>Destination</th>
                                <th>Bookings</th>
                                <th>Trend</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($topDestinations as $destination): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($destination['name']); ?></td>
                                    <td><?php echo $destination['booking_count']; ?></td>
                                    <td>
                                        <span class="trend-up">
                                            <span class="material-icons-outlined">trending_up</span>
                                            +<?php echo rand(5, 25); ?>%
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
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

        // Popular Categories
        const categoriesCtx = document.getElementById('categoriesChart').getContext('2d');
        const categoriesChart = new Chart(categoriesCtx, {
            type: 'horizontalBar',
            data: {
                labels: <?php echo json_encode(array_column($popularCategories, 'category')); ?>,
                datasets: [{
                    label: 'Destinations',
                    data: <?php echo json_encode(array_column($popularCategories, 'count')); ?>,
                    backgroundColor: '#f97316'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                indexAxis: 'y'
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