<?php
require_once __DIR__ . '/../config/auth.php';

// Check if user is admin
requireAdmin();

$currentUser = getCurrentUser();

// Get database connection for dashboard stats only
$conn = getDatabaseConnection();

// Pagination variables
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$limit = 15;
$offset = ($page - 1) * $limit;
$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';

// Get analytics data
$totalUsers = 0;
$totalBookings = 0;
$activeUsers = 0;
$todayLogins = 0;
$totalGuides = 0;
$totalDestinations = 0;
$totalHotels = 0;
$pendingBookings = 0;
$monthlyRevenue = 0;

if ($conn) {
    // Fetch dashboard settings
    $settings = [];
    $result = $conn->query("SELECT setting_key, setting_value FROM dashboard_settings");
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $settings[$row['setting_key']] = $row['setting_value'];
        }
    }
    
    // Default settings if not found
    $pageTitle = $settings['page_title'] ?? 'Dashboard Overview';
    $pageSubtitle = $settings['page_subtitle'] ?? 'System statistics and analytics';
    $logoText = $settings['admin_logo_text'] ?? 'SJDM ADMIN';

    // Fetch admin specific info
    $adminInfo = ['role_title' => 'Administrator', 'admin_mark' => 'A'];
    $stmt = $conn->prepare("SELECT admin_mark, role_title FROM admins WHERE user_id = ?");
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
    $result = $conn->query("SELECT * FROM admin_menu WHERE is_active = 1 ORDER BY display_order ASC");
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $menuItems[] = $row;
        }
    }

    // Fetch dashboard widgets
    $widgets = [];
    $result = $conn->query("SELECT * FROM dashboard_widgets WHERE is_active = 1 ORDER BY display_order ASC");
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $widgets[] = $row;
        }
    }

    // Total users
    $result = $conn->query("SELECT COUNT(*) as total FROM users WHERE user_type = 'user'");
    if ($result) {
        $totalUsers = $result->fetch_assoc()['total'];
    }
    
    // Active users
    $result = $conn->query("SELECT COUNT(*) as total FROM users WHERE user_type = 'user' AND status = 'active'");
    if ($result) {
        $activeUsers = $result->fetch_assoc()['total'];
    }
    
    // Total bookings
    $result = $conn->query("SELECT COUNT(*) as total FROM bookings");
    if ($result) {
        $totalBookings = $result->fetch_assoc()['total'];
    }
    
    // Pending bookings
    $result = $conn->query("SELECT COUNT(*) as total FROM bookings WHERE status = 'pending'");
    if ($result) {
        $pendingBookings = $result->fetch_assoc()['total'];
    }
    
    // Today's logins
    $result = $conn->query("SELECT COUNT(DISTINCT user_id) as total FROM login_activity WHERE DATE(login_time) = CURDATE() AND status = 'success'");
    if ($result) {
        $todayLogins = $result->fetch_assoc()['total'];
    }
    
    // Total tour guides
    $result = $conn->query("SELECT COUNT(*) as total FROM tour_guides WHERE status = 'active'");
    if ($result) {
        $totalGuides = $result->fetch_assoc()['total'];
    }
    
    // Total destinations
    $result = $conn->query("SELECT COUNT(*) as total FROM tourist_spots WHERE status = 'active'");
    if ($result) {
        $totalDestinations = $result->fetch_assoc()['total'];
    }
    
    // Total hotels
    $result = $conn->query("SELECT COUNT(*) as total FROM hotels WHERE status = 'active'");
    if ($result) {
        $totalHotels = $result->fetch_assoc()['total'];
    }
    
    // Monthly revenue
    $result = $conn->query("SELECT COALESCE(SUM(total_amount), 0) as total FROM bookings WHERE status = 'confirmed' AND MONTH(created_at) = MONTH(CURDATE()) AND YEAR(created_at) = YEAR(CURDATE())");
    if ($result) {
        $monthlyRevenue = $result->fetch_assoc()['total'];
    }

    // Map query keys to values for widgets
    $queryValues = [
        'totalUsers' => $totalUsers,
        'activeUsers' => $activeUsers,
        'totalBookings' => $totalBookings,
        'pendingBookings' => $pendingBookings,
        'todayLogins' => $todayLogins,
        'totalGuides' => $totalGuides,
        'totalDestinations' => $totalDestinations,
        'totalHotels' => $totalHotels,
        'monthlyRevenue' => '₱' . number_format($monthlyRevenue, 2)
    ];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?> | SJDM Tours</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet">
    <link rel="stylesheet" href="admin-styles.css">
</head>
<body>
    <div class="admin-container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <div class="logo">
                    <span class="mark-icon"><?php echo $adminInfo['admin_mark'] ?? 'A'; ?></span>
                    <span class="material-icons-outlined">admin_panel_settings</span>
                    <span><?php echo $logoText; ?></span>
                </div>
            </div>
            
            <nav class="sidebar-nav">
                <?php foreach ($menuItems as $item): 
                    $isActive = basename($_SERVER['PHP_SELF']) == $item['link'] ? 'active' : '';
                    $badgeVal = 0;
                    if ($item['badge_query'] && isset($queryValues[$item['badge_query']])) {
                        $badgeVal = $queryValues[$item['badge_query']];
                    }
                ?>
                <a href="<?php echo $item['link']; ?>" class="nav-item <?php echo $isActive; ?>">
                    <span class="material-icons-outlined"><?php echo $item['icon']; ?></span>
                    <span><?php echo $item['title']; ?></span>
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
            <!-- Top Bar -->
            <header class="top-bar">
                <div class="page-title">
                    <h1 id="pageTitle"><?php echo $pageTitle; ?></h1>
                    <p id="pageSubtitle"><?php echo $pageSubtitle; ?></p>
                </div>
                
                <div class="top-bar-actions">
                    <button class="icon-btn" title="Notifications">
                        <span class="material-icons-outlined">notifications</span>
                        <span class="badge"><?php echo $totalBookings > 0 ? $totalBookings : ''; ?></span>
                    </button>
                    
                    <div class="user-profile">
                        <div class="avatar">
                            <span class="admin-mark-badge"><?php echo $adminInfo['admin_mark'] ?? 'A'; ?></span>
                            <span><?php echo strtoupper(substr($currentUser['first_name'], 0, 1)); ?></span>
                        </div>
                        <div class="user-info">
                            <p class="user-name"><?php echo htmlspecialchars($currentUser['first_name'] . ' ' . $currentUser['last_name']); ?></p>
                            <p class="user-role"><?php echo $adminInfo['role_title']; ?></p>
                        </div>
                    </div>
                </div>
            </header>
            <div class="content-area">
                <div class="stats-grid">
                    <?php foreach ($widgets as $widget): 
                        $val = $queryValues[$widget['query_key']] ?? 0;
                    ?>
                    <div class="stat-card" data-stat="<?php echo $widget['query_key']; ?>">
                        <div class="stat-icon <?php echo $widget['color_class']; ?>">
                            <span class="material-icons-outlined"><?php echo $widget['icon']; ?></span>
                        </div>
                        <div class="stat-details">
                            <h3><?php echo $val; ?></h3>
                            <p><?php echo $widget['title']; ?></p>
                            <div class="stat-meta"><?php echo $widget['subtitle']; ?></div>
                            <div class="stat-trend positive">
                                <span class="material-icons-outlined">trending_up</span>
                                <span>Updated</span>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                
                <!-- Recent Activity -->
                <div class="card">
                    <div class="card-header">
                        <h2><span class="material-icons-outlined">history</span> Recent Activity</h2>
                        <button class="btn-secondary" onclick="admin.exportActivity()">
                            <span class="material-icons-outlined">download</span>
                            Export
                        </button>
                    </div>
                    <div class="table-container">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Time</th>
                                    <th>User</th>
                                    <th>Email</th>
                                    <th>Action</th>
                                    <th>IP Address</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody id="recentActivityTable">
                                <?php
                                if ($conn) {
                                    $query = "SELECT u.first_name, u.last_name, u.email, la.login_time, la.ip_address, la.status 
                                              FROM login_activity la 
                                              JOIN users u ON la.user_id = u.id 
                                              ORDER BY la.login_time DESC 
                                              LIMIT 10";
                                    $result = $conn->query($query);
                                    
                                    if ($result && $result->num_rows > 0) {
                                        while ($row = $result->fetch_assoc()) {
                                            $statusClass = $row['status'] === 'success' ? 'success' : 'failed';
                                            echo '<tr>';
                                            echo '<td>' . date('M d, Y H:i', strtotime($row['login_time'])) . '</td>';
                                            echo '<td>' . htmlspecialchars($row['first_name'] . ' ' . $row['last_name']) . '</td>';
                                            echo '<td>' . htmlspecialchars($row['email']) . '</td>';
                                            echo '<td>Login Attempt</td>';
                                            echo '<td>' . ($row['ip_address'] ?? 'N/A') . '</td>';
                                            echo '<td><span class="status-badge ' . $statusClass . '">' . ucfirst($row['status']) . '</span></td>';
                                            echo '</tr>';
                                        }
                                    } else {
                                        echo '<tr>';
                                        echo '<td colspan="6" style="text-align: center; padding: 40px;">';
                                        echo '<span class="material-icons-outlined" style="font-size: 48px; opacity: 0.3;">history</span>';
                                        echo '<p style="margin-top: 16px; color: var(--text-secondary);">No recent activity found</p>';
                                        echo '</td>';
                                        echo '</tr>';
                                    }
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>
    
    <!-- JavaScript -->
    <script src="admin-script.js"></script>
</body>
</html>
<?php
// Close database connection
if ($conn) {
    closeDatabaseConnection($conn);
}
?>