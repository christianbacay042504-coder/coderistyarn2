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
$totalHotels = 0;
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
    <style>
        /* Enhanced Stats Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 25px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            border-radius: 16px;
            padding: 24px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            border: 1px solid rgba(0, 0, 0, 0.05);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--primary, #3b82f6), var(--primary-light, #60a5fa));
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15);
            border-color: var(--primary, #3b82f6);
        }

        .stat-card:hover::before {
            opacity: 1;
        }

        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
            position: relative;
            transition: all 0.3s ease;
        }

        .stat-icon::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 0;
            height: 0;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.2);
            transition: all 0.4s ease;
        }

        .stat-card:hover .stat-icon {
            transform: scale(1.1);
        }

        .stat-card:hover .stat-icon::after {
            width: 100%;
            height: 100%;
        }

        .stat-icon.blue { background: linear-gradient(135deg, #3b82f6, #2563eb); }
        .stat-icon.green { background: linear-gradient(135deg, #10b981, #059669); }
        .stat-icon.orange { background: linear-gradient(135deg, #f59e0b, #d97706); }
        .stat-icon.purple { background: linear-gradient(135deg, #8b5cf6, #7c3aed); }
        .stat-icon.teal { background: linear-gradient(135deg, #14b8a6, #0d9488); }
        .stat-icon.pink { background: linear-gradient(135deg, #ec4899, #db2777); }
        .stat-icon.yellow { background: linear-gradient(135deg, #eab308, #ca8a04); }
        .stat-icon.red { background: linear-gradient(135deg, #ef4444, #dc2626); }

        .stat-icon .material-icons-outlined {
            font-size: 28px;
            color: white;
            z-index: 1;
            position: relative;
        }

        .stat-details h3 {
            font-size: 2.5rem;
            font-weight: 700;
            margin: 0 0 8px 0;
            color: #1f2937;
            line-height: 1;
        }

        .stat-details p {
            font-size: 0.95rem;
            font-weight: 600;
            color: #6b7280;
            margin: 0 0 12px 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .stat-meta {
            font-size: 0.85rem;
            color: #9ca3af;
            margin-bottom: 16px;
        }

        .stat-trend {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 0.85rem;
            font-weight: 600;
            margin-bottom: 20px;
        }

        .stat-trend.positive { color: #10b981; }
        .stat-trend.negative { color: #ef4444; }

        .stat-trend .material-icons-outlined {
            font-size: 18px;
        }

        /* Progress Bars */
        .stat-progress {
            margin-top: 16px;
        }

        .progress-bar {
            width: 100%;
            height: 8px;
            background: #f3f4f6;
            border-radius: 10px;
            overflow: hidden;
            margin-bottom: 8px;
            position: relative;
        }

        .progress-bar::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
            animation: shimmer 2s infinite;
        }

        @keyframes shimmer {
            0% { transform: translateX(-100%); }
            100% { transform: translateX(100%); }
        }

        .progress-fill {
            height: 100%;
            border-radius: 10px;
            transition: width 1s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }

        .progress-fill::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(90deg, rgba(255, 255, 255, 0.2), transparent);
            animation: progress-shine 3s infinite;
        }

        @keyframes progress-shine {
            0% { transform: translateX(-100%); }
            100% { transform: translateX(100%); }
        }

        .progress-fill.blue { background: linear-gradient(90deg, #3b82f6, #60a5fa); }
        .progress-fill.green { background: linear-gradient(90deg, #10b981, #34d399); }
        .progress-fill.orange { background: linear-gradient(90deg, #f59e0b, #fbbf24); }
        .progress-fill.purple { background: linear-gradient(90deg, #8b5cf6, #a78bfa); }
        .progress-fill.teal { background: linear-gradient(90deg, #14b8a6, #2dd4bf); }
        .progress-fill.pink { background: linear-gradient(90deg, #ec4899, #f472b6); }
        .progress-fill.yellow { background: linear-gradient(90deg, #eab308, #facc15); }
        .progress-fill.red { background: linear-gradient(90deg, #ef4444, #f87171); }

        .progress-text {
            font-size: 0.75rem;
            color: #6b7280;
            font-weight: 500;
        }

        /* Responsive Design */
        @media (max-width: 1200px) {
            .stats-grid {
                grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
                gap: 20px;
            }
        }

        @media (max-width: 768px) {
            .stats-grid {
                grid-template-columns: 1fr;
                gap: 16px;
            }
            
            .stat-card {
                padding: 20px;
            }
            
            .stat-details h3 {
                font-size: 2rem;
            }
        }

        /* Loading Animation */
        .stat-card {
            animation: slideUp 0.6s ease-out;
        }

        .stat-card:nth-child(1) { animation-delay: 0.1s; }
        .stat-card:nth-child(2) { animation-delay: 0.2s; }
        .stat-card:nth-child(3) { animation-delay: 0.3s; }
        .stat-card:nth-child(4) { animation-delay: 0.4s; }
        .stat-card:nth-child(5) { animation-delay: 0.5s; }
        .stat-card:nth-child(6) { animation-delay: 0.6s; }
        .stat-card:nth-child(7) { animation-delay: 0.7s; }
        .stat-card:nth-child(8) { animation-delay: 0.8s; }
        .stat-card:nth-child(9) { animation-delay: 0.9s; }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
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
                            <div class="admin-mark-badge"><?php echo $adminMark; ?></div>
                        </div>
                        <div class="user-info">
                            <p class="user-name"><?php echo htmlspecialchars($currentUser['first_name'] . ' ' . $currentUser['last_name']); ?></p>
                            <p class="user-role"><?php echo $roleTitle; ?></p>
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
                    
                    <div class="stat-card" data-stat="totalHotels">
                        <div class="stat-icon yellow">
                            <span class="material-icons-outlined">hotel</span>
                        </div>
                        <div class="stat-details">
                            <h3><?php echo $totalHotels; ?></h3>
                            <p>Hotels</p>
                            <div class="stat-meta">Available accommodations</div>
                            <div class="stat-trend positive">
                                <span class="material-icons-outlined">trending_up</span>
                                <span>5% increase</span>
                            </div>
                        </div>
                        <div class="stat-progress">
                            <div class="progress-bar">
                                <div class="progress-fill yellow" style="width: 65%"></div>
                            </div>
                            <span class="progress-text">65% occupancy rate</span>
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
</body>
</html>
<?php
// Close database connection
if ($conn) {
    closeDatabaseConnection($conn);
}
?>
