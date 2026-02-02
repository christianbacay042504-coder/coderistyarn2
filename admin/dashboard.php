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
$totalGuides = 6;
$totalDestinations = 8;

if ($conn) {
    // Total users
    $result = $conn->query("SELECT COUNT(*) as total FROM users WHERE user_type = 'user'");
    $totalUsers = $result->fetch_assoc()['total'];
    
    // Active users
    $result = $conn->query("SELECT COUNT(*) as total FROM users WHERE user_type = 'user' AND status = 'active'");
    $activeUsers = $result->fetch_assoc()['total'];
    
    // Total bookings
    $result = $conn->query("SELECT COUNT(*) as total FROM bookings");
    $totalBookings = $result->fetch_assoc()['total'];
    
    // Today's logins
    $result = $conn->query("SELECT COUNT(DISTINCT user_id) as total FROM login_activity WHERE DATE(login_time) = CURDATE() AND status = 'success'");
    $todayLogins = $result->fetch_assoc()['total'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | SJDM Tours</title>
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
                    <span class="material-icons-outlined">admin_panel_settings</span>
                    <span>SJDM ADMIN</span>
                </div>
            </div>
            
            <nav class="sidebar-nav">
                <a href="dashboard.php" class="nav-item active">
                    <span class="material-icons-outlined">dashboard</span>
                    <span>Dashboard</span>
                </a>
                <a href="user-management.php" class="nav-item">
                    <span class="material-icons-outlined">people</span>
                    <span>User Management</span>
                    <?php if ($totalUsers > 0): ?>
                        <span class="badge"><?php echo $totalUsers; ?></span>
                    <?php endif; ?>
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
                <a href="analytics.php" class="nav-item">
                    <span class="material-icons-outlined">analytics</span>
                    <span>Analytics</span>
                </a>
                <a href="reports.php" class="nav-item">
                    <span class="material-icons-outlined">description</span>
                    <span>Reports</span>
                </a>
                <a href="settings.php" class="nav-item">
                    <span class="material-icons-outlined">settings</span>
                    <span>Settings</span>
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
            <!-- Top Bar -->
            <header class="top-bar">
                <div class="page-title">
                    <h1 id="pageTitle">Dashboard Overview</h1>
                    <p id="pageSubtitle">System statistics and analytics</p>
                </div>
                
                <div class="top-bar-actions">
                    <button class="icon-btn" title="Notifications">
                        <span class="material-icons-outlined">notifications</span>
                        <span class="badge"><?php echo $totalBookings > 0 ? $totalBookings : ''; ?></span>
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
            
            <!-- Dashboard Overview -->
            <div class="content-area">
                <!-- Stats Cards -->
                <div class="stats-grid">
                    <div class="stat-card" data-stat="totalUsers">
                        <div class="stat-icon blue">
                            <span class="material-icons-outlined">people</span>
                        </div>
                        <div class="stat-details">
                            <h3><?php echo $totalUsers; ?></h3>
                            <p>Total Users</p>
                            <div class="stat-meta">Registered users in system</div>
                            <div class="stat-trend positive">
                                <span class="material-icons-outlined">trending_up</span>
                                <span>12% growth</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="stat-card" data-stat="activeUsers">
                        <div class="stat-icon green">
                            <span class="material-icons-outlined">check_circle</span>
                        </div>
                        <div class="stat-details">
                            <h3><?php echo $activeUsers; ?></h3>
                            <p>Active Users</p>
                            <div class="stat-meta">Currently active accounts</div>
                            <div class="stat-trend positive">
                                <span class="material-icons-outlined">trending_up</span>
                                <span>8% increase</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="stat-card" data-stat="totalBookings">
                        <div class="stat-icon orange">
                            <span class="material-icons-outlined">event</span>
                        </div>
                        <div class="stat-details">
                            <h3><?php echo $totalBookings; ?></h3>
                            <p>Total Bookings</p>
                            <div class="stat-meta">All-time bookings</div>
                            <div class="stat-trend positive">
                                <span class="material-icons-outlined">trending_up</span>
                                <span>15% increase</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="stat-card" data-stat="todayLogins">
                        <div class="stat-icon purple">
                            <span class="material-icons-outlined">login</span>
                        </div>
                        <div class="stat-details">
                            <h3><?php echo $todayLogins; ?></h3>
                            <p>Today's Logins</p>
                            <div class="stat-meta">Successful login attempts</div>
                            <div class="stat-trend positive">
                                <span class="material-icons-outlined">trending_up</span>
                                <span>5% increase</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="stat-card" data-stat="totalGuides">
                        <div class="stat-icon teal">
                            <span class="material-icons-outlined">tour</span>
                        </div>
                        <div class="stat-details">
                            <h3><?php echo $totalGuides; ?></h3>
                            <p>Tour Guides</p>
                            <div class="stat-meta">Available tour guides</div>
                            <div class="stat-trend positive">
                                <span class="material-icons-outlined">trending_up</span>
                                <span>3% increase</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="stat-card" data-stat="totalDestinations">
                        <div class="stat-icon pink">
                            <span class="material-icons-outlined">landscape</span>
                        </div>
                        <div class="stat-details">
                            <h3><?php echo $totalDestinations; ?></h3>
                            <p>Destinations</p>
                            <div class="stat-meta">Tourist spots available</div>
                            <div class="stat-trend positive">
                                <span class="material-icons-outlined">trending_up</span>
                                <span>10% increase</span>
                            </div>
                        </div>
                    </div>
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