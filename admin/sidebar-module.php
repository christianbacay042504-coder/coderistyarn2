<?php
// Admin Sidebar Module - Reusable Navigation Component
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/auth.php';

// Get current admin user
$currentUser = getCurrentUser();
$adminInfo = null;

// Get admin info
if ($currentUser && $conn) {
    $stmt = $conn->prepare("SELECT a.id, a.admin_mark, a.role_title FROM admin_users a WHERE a.user_id = ?");
    $userId = $currentUser['id'];
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $adminInfo = $row;
    }
    $stmt->close();
}

// Get current page for active state highlighting
$currentPage = basename($_SERVER['PHP_SELF']);
?>

<!-- Sidebar Module -->
<aside class="sidebar">
    <div class="sidebar-header">
        <div class="logo" style="display: flex; align-items: center; gap: 12px;">
            <img src="../lgo.png" alt="SJDM Tours Logo" style="height: 40px; width: 40px; object-fit: contain; border-radius: 8px;">
            <span>SJDM ADMIN</span>
        </div>
    </div>
    
    <nav class="sidebar-nav">
        <a href="dashboard.php" class="nav-item <?php echo $currentPage === 'dashboard.php' ? 'active' : ''; ?>">
            <span class="material-icons-outlined">dashboard</span>
            <span>Dashboard</span>
        </a>
        
        <a href="user-management.php" class="nav-item <?php echo $currentPage === 'user-management.php' ? 'active' : ''; ?>">
            <span class="material-icons-outlined">people</span>
            <span>User Management</span>
        </a>
        
        <a href="tour-guides.php" class="nav-item <?php echo $currentPage === 'tour-guides.php' ? 'active' : ''; ?>">
            <span class="material-icons-outlined">tour</span>
            <span>Tour Guides</span>
        </a>
        
        <a href="destinations.php" class="nav-item <?php echo $currentPage === 'destinations.php' ? 'active' : ''; ?>">
            <span class="material-icons-outlined">place</span>
            <span>Destinations</span>
        </a>
        
        <a href="bookings.php" class="nav-item <?php echo $currentPage === 'bookings.php' ? 'active' : ''; ?>">
            <span class="material-icons-outlined">event</span>
            <span>Bookings</span>
        </a>
        
        <a href="analytics.php" class="nav-item <?php echo $currentPage === 'analytics.php' ? 'active' : ''; ?>">
            <span class="material-icons-outlined">analytics</span>
            <span>Analytics</span>
        </a>
        
        <a href="reports.php" class="nav-item <?php echo $currentPage === 'reports.php' ? 'active' : ''; ?>">
            <span class="material-icons-outlined">assessment</span>
            <span>Reports</span>
        </a>
        
        <a href="tour-guide-registrations.php" class="nav-item <?php echo $currentPage === 'tour-guide-registrations.php' ? 'active' : ''; ?>">
            <span class="material-icons-outlined">how_to_reg</span>
            <span>Guide Registrations</span>
        </a>
    </nav>
    
    <div class="sidebar-footer">
        <a href="logout.php" class="logout-btn" id="logoutBtn" onclick="handleLogout(event)">
            <span class="material-icons-outlined">logout</span>
            <span>Logout</span>
        </a>
        
        <!-- Admin Profile Info -->
        <?php if ($adminInfo): ?>
        <div class="admin-profile-mini">
            <div class="profile-avatar"><?php echo substr($adminInfo['admin_mark'], 0, 1); ?></div>
            <div class="profile-info">
                <span class="admin-name"><?php echo htmlspecialchars($currentUser['first_name'] . ' ' . $currentUser['last_name']); ?></span>
                <span class="admin-role"><?php echo htmlspecialchars($adminInfo['role_title']); ?></span>
            </div>
        </div>
        <?php endif; ?>
    </div>
</aside>

<style>
/* Sidebar Module Styles */
.sidebar {
    width: 280px;
    background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
    color: white;
    height: 100vh;
    position: fixed;
    left: 0;
    top: 0;
    overflow-y: auto;
    z-index: 1000;
    box-shadow: 4px 0 20px rgba(0, 0, 0, 0.1);
}

.sidebar-header {
    padding: 24px 20px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.sidebar-header .logo {
    font-size: 1.4rem;
    font-weight: 700;
    color: white;
    text-decoration: none;
}

.sidebar-nav {
    padding: 20px 0;
}

.nav-item {
    display: flex;
    align-items: center;
    gap: 16px;
    padding: 14px 24px;
    color: rgba(255, 255, 255, 0.8);
    text-decoration: none;
    transition: all 0.3s ease;
    position: relative;
    font-weight: 500;
}

.nav-item:hover {
    background: rgba(255, 255, 255, 0.1);
    color: white;
    padding-left: 32px;
}

.nav-item.active {
    background: linear-gradient(90deg, rgba(99, 102, 241, 0.2) 0%, transparent 100%);
    color: #818cf8;
    border-left: 4px solid #818cf8;
}

.nav-item.active::before {
    content: '';
    position: absolute;
    left: 0;
    top: 0;
    bottom: 0;
    width: 4px;
    background: #818cf8;
}

.nav-item .material-icons-outlined {
    font-size: 20px;
    min-width: 20px;
}

.sidebar-footer {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    padding: 20px;
    border-top: 1px solid rgba(255, 255, 255, 0.1);
}

.logout-btn {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px 16px;
    color: #f87171;
    text-decoration: none;
    border-radius: 8px;
    transition: all 0.3s ease;
    margin-bottom: 16px;
    font-weight: 500;
}

.logout-btn:hover {
    background: rgba(248, 113, 113, 0.2);
    color: #fca5a5;
}

.admin-profile-mini {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px;
    background: rgba(255, 255, 255, 0.05);
    border-radius: 8px;
}

.profile-avatar {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    background: linear-gradient(135deg, #818cf8 0%, #6366f1 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 14px;
    color: white;
}

.profile-info {
    flex: 1;
}

.admin-name {
    display: block;
    font-size: 14px;
    font-weight: 600;
    color: white;
    line-height: 1.2;
}

.admin-role {
    display: block;
    font-size: 12px;
    color: rgba(255, 255, 255, 0.6);
    line-height: 1.2;
}

/* Mobile Responsive */
@media (max-width: 768px) {
    .sidebar {
        transform: translateX(-100%);
        transition: transform 0.3s ease;
    }
    
    .sidebar.open {
        transform: translateX(0);
    }
}
</style>

<script>
// Sidebar functionality
document.addEventListener('DOMContentLoaded', function() {
    // Handle logout
    const logoutBtn = document.getElementById('logoutBtn');
    if (logoutBtn) {
        logoutBtn.addEventListener('click', function(e) {
            e.preventDefault();
            if (confirm('Are you sure you want to logout?')) {
                window.location.href = 'logout.php';
            }
        });
    }
    
    // Mobile menu toggle
    const mobileMenuToggle = document.querySelector('.mobile-menu-toggle');
    const sidebar = document.querySelector('.sidebar');
    
    if (mobileMenuToggle && sidebar) {
        mobileMenuToggle.addEventListener('click', function() {
            sidebar.classList.toggle('open');
        });
    }
    
    // Close sidebar when clicking outside on mobile
    document.addEventListener('click', function(e) {
        if (window.innerWidth <= 768) {
            if (!sidebar.contains(e.target) && !mobileMenuToggle.contains(e.target)) {
                sidebar.classList.remove('open');
            }
        }
    });
});

function handleLogout(event) {
    event.preventDefault();
    if (confirm('Are you sure you want to logout?')) {
        window.location.href = 'logout.php';
    }
}
</script>
