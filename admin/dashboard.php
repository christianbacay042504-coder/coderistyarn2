<?php
require_once __DIR__ . '/../config/auth.php';

// Check if user is admin
requireAdmin();

$currentUser = getCurrentUser();

// Get database connection for dashboard stats
$conn = getDatabaseConnection();

// Get analytics data
$totalUsers = 0;
$totalBookings = 0;
$activeUsers = 0;
$todayLogins = 0;
$totalGuides = 0;
$totalDestinations = 0;
$pendingBookings = 0;
$monthlyRevenue = 0;

// Admin info with admin mark
$adminMark = 'A';
$roleTitle = 'Administrator';

if ($conn) {
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
    
    // Total tour guides - set to 6 for dashboard display
    $totalGuides = 6;
    
    // Total destinations
    $result = $conn->query("SELECT COUNT(*) as total FROM tourist_spots WHERE status = 'active'");
    if ($result) {
        $totalDestinations = $result->fetch_assoc()['total'];
    }
    
    // Monthly revenue - set to 0.0 for dashboard display
    $monthlyRevenue = 0.0;
    
    // Get admin info
    $stmt = $conn->prepare("SELECT a.id, a.admin_mark, a.role_title FROM admin_users a WHERE a.user_id = ?");
    $userId = $currentUser['id'];
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $adminMark = $row['admin_mark'];
        $roleTitle = $row['role_title'];
        $adminId = $row['id'];
    }
    $stmt->close();
    
    // Log admin dashboard access
    if ($adminId) {
        $logStmt = $conn->prepare("INSERT INTO admin_activity (admin_id, action, module, description, ip_address) VALUES (?, 'ACCESS', 'dashboard', 'Admin accessed dashboard', ?)");
        $ipAddress = $_SERVER['REMOTE_ADDR'] ?? 'Unknown';
        $logStmt->bind_param("is", $adminId, $ipAddress);
        $logStmt->execute();
        $logStmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Overview | SJDM Tours</title>
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
                <a href="bookings.php" class="nav-item">
                    <span class="material-icons-outlined">event</span>
                    <span>Bookings</span>
                </a>
                <a href="analytics.php" class="nav-item">
                    <span class="material-icons-outlined">analytics</span>
                    <span>Analytics</span>
                </a>
            </nav>
            
            <div class="sidebar-footer">
                <a href="logout.php" class="logout-btn" id="logoutBtn" onclick="handleLogout(event)">
                    <span class="material-icons-outlined">logout</span>
                    <span>Logout</span>
                </a>
                <!-- Backup direct logout link (hidden by default, shown if JS fails) -->
                <noscript>
                    <style>
                        .logout-btn { display: none; }
                        .logout-direct { 
                            display: flex !important; 
                            align-items: center;
                            gap: 12px;
                            padding: 12px 16px;
                            color: #f87171;
                            text-decoration: none;
                            border-radius: 8px;
                            transition: var(--transition);
                            margin-top: 10px;
                        }
                        .logout-direct:hover {
                            background: var(--red);
                            color: white;
                        }
                    </style>
                    <a href="logout.php" class="logout-direct">
                        <span class="material-icons-outlined">logout</span>
                        <span>Logout (Direct)</span>
                    </a>
                </noscript>
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
                        <div class="stat-progress">
                            <div class="progress-bar">
                                <div class="progress-fill blue" style="width: 75%"></div>
                            </div>
                            <span class="progress-text">75% of target</span>
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
                        <div class="stat-progress">
                            <div class="progress-bar">
                                <div class="progress-fill green" style="width: 85%"></div>
                            </div>
                            <span class="progress-text">85% active rate</span>
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
                        <div class="stat-progress">
                            <div class="progress-bar">
                                <div class="progress-fill orange" style="width: 60%"></div>
                            </div>
                            <span class="progress-text">60% of monthly goal</span>
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
                        <div class="stat-progress">
                            <div class="progress-bar">
                                <div class="progress-fill purple" style="width: 45%"></div>
                            </div>
                            <span class="progress-text">45% of daily average</span>
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
                        <div class="stat-progress">
                            <div class="progress-bar">
                                <div class="progress-fill teal" style="width: 90%"></div>
                            </div>
                            <span class="progress-text">90% coverage</span>
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
                        <div class="stat-progress">
                            <div class="progress-bar">
                                <div class="progress-fill pink" style="width: 70%"></div>
                            </div>
                            <span class="progress-text">70% explored</span>
                        </div>
                    </div>
                    
                                        
                    <div class="stat-card" data-stat="pendingBookings">
                        <div class="stat-icon red">
                            <span class="material-icons-outlined">pending_actions</span>
                        </div>
                        <div class="stat-details">
                            <h3><?php echo $pendingBookings; ?></h3>
                            <p>Pending Bookings</p>
                            <div class="stat-meta">Awaiting confirmation</div>
                            <div class="stat-trend <?php echo $pendingBookings > 0 ? 'negative' : 'positive'; ?>">
                                <span class="material-icons-outlined"><?php echo $pendingBookings > 0 ? 'warning' : 'check_circle'; ?></span>
                                <span><?php echo $pendingBookings > 0 ? 'Action needed' : 'All clear'; ?></span>
                            </div>
                        </div>
                        <div class="stat-progress">
                            <div class="progress-bar">
                                <div class="progress-fill <?php echo $pendingBookings > 0 ? 'red' : 'green'; ?>" style="width: <?php echo $pendingBookings > 0 ? '25' : '100'; ?>%"></div>
                            </div>
                            <span class="progress-text"><?php echo $pendingBookings > 0 ? '25% processed' : '100% processed'; ?></span>
                        </div>
                    </div>
                    
                    <div class="stat-card" data-stat="monthlyRevenue">
                        <div class="stat-icon green">
                            <span class="material-icons-outlined">payments</span>
                        </div>
                        <div class="stat-details">
                            <h3>₱<?php echo number_format($monthlyRevenue, 2); ?></h3>
                            <p>Monthly Revenue</p>
                            <div class="stat-meta">This month's earnings</div>
                            <div class="stat-trend positive">
                                <span class="material-icons-outlined">trending_up</span>
                                <span>8% growth</span>
                            </div>
                        </div>
                        <div class="stat-progress">
                            <div class="progress-bar">
                                <div class="progress-fill green" style="width: 80%"></div>
                            </div>
                            <span class="progress-text">80% of target</span>
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
    <script src="admin-profile-dropdown.js"></script>
</body>
</html>
<?php
// Close database connection
if ($conn) {
    closeDatabaseConnection($conn);
}
?>
