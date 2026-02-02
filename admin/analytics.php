<?php
// Analytics Module
// This file handles analytics and reporting with separated connections and functions

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/auth.php';

// Database connection functions
function getAdminConnection() {
    return getDatabaseConnection();
}

function initAdminAuth() {
    requireAdmin();
    return getCurrentUser();
}

function closeAdminConnection($conn) {
    closeDatabaseConnection($conn);
}

function getAdminStats($conn) {
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

function getBookingStats($conn) {
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
    $result = $conn->query("SELECT SUM(total_amount) as total FROM bookings WHERE status = 'completed'");
    $stats['total_revenue'] = $result ? ($result->fetch_assoc()['total'] ?? 0) : 0;
    
    return $stats;
}

// Initialize admin authentication
$currentUser = initAdminAuth();

// Get database connection
$conn = getAdminConnection();

// Get statistics
$stats = getAdminStats($conn);
$bookingStats = getBookingStats($conn);

// Get additional analytics data
$analytics = [];

// Monthly revenue (last 6 months)
$monthlyRevenue = [];
for ($i = 5; $i >= 0; $i--) {
    $month = date('Y-m', strtotime("-$i months"));
    $result = $conn->query("SELECT SUM(total_amount) as revenue, COUNT(*) as bookings FROM bookings WHERE DATE_FORMAT(created_at, '%Y-%m') = '$month' AND status = 'completed'");
    $data = $result ? $result->fetch_assoc() : ['revenue' => 0, 'bookings' => 0];
    $monthlyRevenue[] = [
        'month' => date('M Y', strtotime($month)),
        'revenue' => $data['revenue'] ?? 0,
        'bookings' => $data['bookings'] ?? 0
    ];
}

// Top destinations by bookings
$topDestinations = [];
$result = $conn->query("SELECT ts.name, COUNT(b.id) as booking_count FROM tourist_spots ts LEFT JOIN bookings b ON b.tour_name LIKE CONCAT('%', ts.name, '%') GROUP BY ts.id ORDER BY booking_count DESC LIMIT 10");
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

// Close connection
closeAdminConnection($conn);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Analytics | SJDM Tours Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
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
                    <span class="material-icons-outlined">admin_panel_settings</span>
                    <span>SJDM ADMIN</span>
                </div>
            </div>
            
            <nav class="sidebar-nav">
                <a href="dashboard.php" class="nav-item">
                    <span class="material-icons-outlined">dashboard</span>
                    <span>Dashboard</span>
                </a>
                <a href="user-management.php" class="nav-item">
                    <span class="material-icons-outlined">people</span>
                    <span>User Management</span>
                </a>
                <a href="tour-guides.php" class="nav-item">
                    <span class="material-icons-outlined">tour</span>
                    <span>Tour Guides</span>
                </a>
                <a href="destinations.php" class="nav-item">
                    <span class="material-icons-outlined">place</span>
                    <span>Destinations</span>
                </a>
                <a href="hotels.php" class="nav-item">
                    <span class="material-icons-outlined">hotel</span>
                    <span>Hotels</span>
                </a>
                <a href="bookings.php" class="nav-item">
                    <span class="material-icons-outlined">event</span>
                    <span>Bookings</span>
                </a>
                <a href="analytics.php" class="nav-item active">
                    <span class="material-icons-outlined">analytics</span>
                    <span>Analytics</span>
                </a>
                <a href="reports.php" class="nav-item">
                    <span class="material-icons-outlined">description</span>
                    <span>Reports</span>
                </a>
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
                    <h1>Analytics Dashboard</h1>
                    <p>System performance and insights</p>
                </div>
                
                <div class="top-bar-actions">
                    <button class="btn-secondary" onclick="exportAnalytics()">
                        <span class="material-icons-outlined">download</span>
                        Export Report
                    </button>
                    
                    <div class="user-profile">
                        <div class="avatar">
                            <span><?php echo strtoupper(substr($currentUser['first_name'], 0, 1)); ?></span>
                        </div>
                        <div class="user-info">
                            <p class="user-name"><?php echo htmlspecialchars($currentUser['first_name'] . ' ' . $currentUser['last_name']); ?></p>
                            <p class="user-role">Administrator</p>
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
</body>
</html>
