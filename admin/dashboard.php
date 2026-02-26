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
    <link rel="icon" type="image/png" href="../lgo.png">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet">

    <link rel="stylesheet" href="admin-styles.css">

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

                <a href="dashboard.php" class="nav-item active">

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

                            <a href="javascript:void(0)" class="dropdown-item" id="adminSignoutLink" onclick="openSignOutModal()">

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
                    
                    <div class="stat-card stat-card-warning" data-stat="monthlyRevenue">
                        <div class="stat-icon">
                            <span class="material-icons-outlined">payments</span>
                        </div>
                        <div class="stat-content">
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
                                <div class="progress-fill" style="width: 80%"></div>
                            </div>
                            <span class="progress-text">80% of target</span>
                        </div>
                    </div>
                </div>

                

                <!-- Recent Activity -->

                <div class="card">

                    <div class="card-header">

                        <h2><span class="material-icons-outlined">history</span> Recent Activity</h2>

                        <div class="export-dropdown">

                            <button class="btn-secondary" id="exportToggleBtn">

                                <span class="material-icons-outlined">download</span>

                                Export

                                <span class="material-icons-outlined">expand_more</span>

                            </button>

                            <div class="export-menu" id="exportMenu" style="display: none;">

                                <a href="export.php?type=login_activity" download>

                                    <span class="material-icons-outlined">login</span>

                                    Login Activity

                                </a>

                                <a href="export.php?type=admin_activity" download>

                                    <span class="material-icons-outlined">admin_panel_settings</span>

                                    Admin Activity

                                </a>

                                <a href="export.php?type=user_registrations" download>

                                    <span class="material-icons-outlined">person_add</span>

                                    User Registrations

                                </a>

                                <a href="export.php?type=bookings" download>

                                    <span class="material-icons-outlined">event</span>

                                    Bookings

                                </a>

                            </div>

                        </div>

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


    <!-- Sign Out Confirmation Modal -->
    <div id="signOutModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Sign Out</h2>
                <button class="modal-close" onclick="closeSignOutModal()">
                    <span class="material-icons-outlined">close</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="signout-content">
                    <div class="signout-icon">
                        <span class="material-icons-outlined">logout</span>
                    </div>
                    <div class="signout-message">
                        <h3>Are you sure you want to sign out?</h3>
                        <p>You will be logged out of the admin panel and redirected to the login page.</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-secondary" onclick="closeSignOutModal()">Cancel</button>
                <button type="button" class="btn-primary" onclick="confirmSignOut()">Sign Out</button>
            </div>
        </div>
    </div>

    <script src="admin-script.js"></script>

    <script src="admin-profile-dropdown.js"></script>

    <script>
        // Sign Out Modal Functions
        function openSignOutModal() {
            const modal = document.getElementById('signOutModal');
            if (modal) {
                modal.style.display = 'block';
                modal.classList.add('show');
                document.body.style.overflow = 'hidden'; // Prevent background scrolling
            }
        }

        function closeSignOutModal() {
            const modal = document.getElementById('signOutModal');
            if (modal) {
                modal.style.display = 'none';
                modal.classList.remove('show');
                document.body.style.overflow = 'auto'; // Restore background scrolling
            }
        }

        function confirmSignOut() {
            // Redirect to logout page
            window.location.href = 'logout.php';
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('signOutModal');
            if (event.target == modal) {
                closeSignOutModal();
            }
        }

        // Close modal with Escape key
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                closeSignOutModal();
            }
        });
    </script>

    <style>
        /* Enhanced Stats Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 24px;
            margin-bottom: 32px;
        }

        .stat-card {
            background: white;
            border-radius: 24px;
            padding: 28px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
            border: 1px solid rgba(0, 0, 0, 0.04);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            gap: 20px;
            min-height: 180px;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.05), rgba(118, 75, 162, 0.05));
            opacity: 0;
            transition: opacity 0.3s ease;
            border-radius: 24px;
        }

        .stat-card:hover {
            transform: translateY(-12px) scale(1.02);
            box-shadow: 0 25px 80px rgba(0, 0, 0, 0.15);
            border-color: rgba(102, 126, 234, 0.2);
        }

        .stat-card:hover::before {
            opacity: 1;
        }

        .stat-icon {
            width: 56px;
            height: 56px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            transition: all 0.3s ease;
            flex-shrink: 0;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
        }

        .stat-icon .material-icons-outlined {
            font-size: 24px;
            color: white;
            z-index: 2;
            position: relative;
        }

        .stat-icon.blue { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
        .stat-icon.green { background: linear-gradient(135deg, #10b981 0%, #059669 100%); }
        .stat-icon.orange { background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); }
        .stat-icon.purple { background: linear-gradient(135deg, #8b5cf6 0%, #6366f1 100%); }
        .stat-icon.teal { background: linear-gradient(135deg, #14b8a6 0%, #0ea5e9 100%); }
        .stat-icon.pink { background: linear-gradient(135deg, #ec4899 0%, #be185d 100%); }
        .stat-icon.red { background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); }

        .stat-details {
            flex: 1;
            z-index: 2;
            position: relative;
        }

        .stat-details h3 {
            font-size: 2.5rem;
            font-weight: 800;
            margin: 0 0 8px 0;
            line-height: 1.1;
            transition: all 0.3s ease;
            color: #1a202c;
        }

        .stat-details p {
            font-size: 0.95rem;
            color: var(--text-secondary);
            margin: 0;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            opacity: 0.8;
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
            color: #4a5568;
            margin: 4px 0 0;
            font-weight: 500;
        }

        .stat-trend {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.85rem;
            font-weight: 700;
            margin-top: 12px;
            padding: 8px 12px;
            background: rgba(102, 126, 234, 0.1);
            border-radius: 20px;
            width: fit-content;
        }

        .stat-trend.positive {
            color: #10b981;
            background: rgba(16, 185, 129, 0.1);
        }

        .stat-trend.negative {
            color: #ef4444;
            background: rgba(239, 68, 68, 0.1);
        }

        .stat-trend .material-icons-outlined {
            font-size: 16px;
        }

        .stat-progress {
            margin-top: auto;
            padding-top: 16px;
        }

        .progress-bar {
            width: 100%;
            height: 6px;
            background: rgba(0, 0, 0, 0.08);
            border-radius: 3px;
            overflow: hidden;
            position: relative;
        }

        .progress-fill {
            height: 100%;
            border-radius: 3px;
            transition: width 0.8s ease;
            position: relative;
        }

        .progress-fill::after {
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

        .progress-fill.blue { background: linear-gradient(90deg, #667eea, #764ba2); }
        .progress-fill.green { background: linear-gradient(90deg, #10b981, #059669); }
        .progress-fill.orange { background: linear-gradient(90deg, #f59e0b, #d97706); }
        .progress-fill.purple { background: linear-gradient(90deg, #8b5cf6, #6366f1); }
        .progress-fill.teal { background: linear-gradient(90deg, #14b8a6, #0ea5e9); }
        .progress-fill.pink { background: linear-gradient(90deg, #ec4899, #be185d); }
        .progress-fill.red { background: linear-gradient(90deg, #ef4444, #dc2626); }

        .progress-text {
            font-size: 0.75rem;
            color: #4a5568;
            align-items: center;
            gap: 24px;
            padding: 24px 0;
            text-align: center;
        }

        .signout-icon {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: var(--red);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 8px 24px rgba(220, 53, 69, 0.3);
        }

        .signout-icon .material-icons-outlined {
            font-size: 40px;
        }

        .signout-message h3 {
            margin: 0 0 8px 0;
            font-size: 20px;
            font-weight: 600;
            color: var(--text-primary);
        }

        .signout-message p {
            margin: 0;
            font-size: 14px;
            color: var(--text-secondary);
            line-height: 1.5;
        }

        /* Sign Out Modal Responsive Design */
        @media (max-width: 768px) {
            .signout-content {
                gap: 20px;
                padding: 16px 0;
            }

            .signout-icon {
                width: 60px;
                height: 60px;
            }

            .signout-icon .material-icons-outlined {
                font-size: 30px;
            }

            .signout-message h3 {
                font-size: 18px;
            }

            .signout-message p {
                font-size: 13px;
            }
        }
    </style>
    <style>
        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(5px);
            animation: fadeIn 0.3s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        .modal-content {
            background-color: white;
            margin: 10% auto;
            padding: 0;
            border-radius: 16px;
            width: 90%;
            max-width: 500px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            animation: slideIn 0.3s ease;
            overflow: hidden;
        }

        @keyframes slideIn {
            from {
                transform: translateY(-50px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .modal-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 24px 32px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-header h2 {
            margin: 0;
            font-size: 1.5rem;
            font-weight: 600;
        }

        .modal-close {
            background: rgba(255, 255, 255, 0.2);
            border: none;
            color: white;
            font-size: 24px;
            cursor: pointer;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }

        .modal-close:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: scale(1.1);
        }

        .modal-body {
            padding: 32px;
        }

        .modal-footer {
            padding: 24px 32px;
            border-top: 1px solid #e5e7eb;
            display: flex;
            gap: 12px;
            justify-content: flex-end;
        }

        .btn-secondary {
            background: #f3f4f6;
            color: #374151;
            border: 1px solid #d1d5db;
            padding: 12px 24px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-secondary:hover {
            background: #e5e7eb;
        }

        .btn-primary {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
        }
    </style>
    <style>
        /* Sign Out Modal Styles */
        .signout-content {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 24px;
            padding: 24px 0;
            text-align: center;
        }

        .signout-icon {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: var(--red);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 8px 24px rgba(220, 53, 69, 0.3);
        }

        .signout-icon .material-icons-outlined {
            font-size: 40px;
        }

        .signout-message h3 {
            margin: 0 0 8px 0;
            font-size: 20px;
            font-weight: 600;
            color: var(--text-primary);
        }

        .signout-message p {
            margin: 0;
            font-size: 14px;
            color: var(--text-secondary);
            line-height: 1.5;
        }

        /* Sign Out Modal Responsive Design */
        @media (max-width: 768px) {
            .signout-content {
                gap: 20px;
                padding: 16px 0;
            }

            .signout-icon {
                width: 60px;
                height: 60px;
            }

            .signout-icon .material-icons-outlined {
                font-size: 30px;
            }

            .signout-message h3 {
                font-size: 18px;
            }

            .signout-message p {
                font-size: 13px;
            }
        }
    </style>
</body>

</html>

<?php

// Close database connection

if ($conn) {

    closeDatabaseConnection($conn);

}

?>