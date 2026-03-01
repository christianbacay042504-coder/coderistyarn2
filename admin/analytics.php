<?php
// Analytics Module - Updated with Destinations Design
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/auth.php';

// Database connection functions
function getAdminConnection() { return getDatabaseConnection(); }
function initAdminAuth() { requireAdmin(); return getCurrentUser(); }
function closeAdminConnection($conn) { closeDatabaseConnection($conn); }

function getAdminStats($conn) {
    $stats = [];
    $result = $conn->query("SELECT COUNT(*) as total FROM users WHERE user_type = 'user'");
    $stats['totalUsers'] = $result ? $result->fetch_assoc()['total'] : 0;
    $result = $conn->query("SELECT COUNT(*) as total FROM users WHERE user_type = 'user' AND status = 'active'");
    $stats['activeUsers'] = $result ? $result->fetch_assoc()['total'] : 0;
    $result = $conn->query("SELECT COUNT(*) as total FROM bookings");
    $stats['totalBookings'] = $result ? $result->fetch_assoc()['total'] : 0;
    $result = $conn->query("SELECT COUNT(DISTINCT user_id) as total FROM login_activity WHERE DATE(login_time) = CURDATE() AND status = 'success'");
    $stats['todayLogins'] = $result ? $result->fetch_assoc()['total'] : 0;
    $result = $conn->query("SELECT COUNT(*) as total FROM tour_guides");
    $stats['totalGuides'] = $result ? $result->fetch_assoc()['total'] : 0;
    $result = $conn->query("SELECT COUNT(*) as total FROM tourist_spots");
    $stats['totalDestinations'] = $result ? $result->fetch_assoc()['total'] : 0;
    return $stats;
}

function getBookingStats($conn) {
    $stats = [];
    $result = $conn->query("SELECT COUNT(*) as total FROM bookings");
    $stats['total'] = $result ? $result->fetch_assoc()['total'] : 0;
    $result = $conn->query("SELECT status, COUNT(*) as count FROM bookings GROUP BY status");
    $stats['by_status'] = [];
    if ($result) { while ($row = $result->fetch_assoc()) { $stats['by_status'][$row['status']] = $row['count']; } }
    $result = $conn->query("SELECT COUNT(*) as total FROM bookings WHERE DATE(created_at) = CURDATE()");
    $stats['today'] = $result ? $result->fetch_assoc()['total'] : 0;
    $result = $conn->query("SELECT COUNT(*) as total FROM bookings WHERE MONTH(created_at) = MONTH(CURDATE()) AND YEAR(created_at) = YEAR(CURDATE())");
    $stats['this_month'] = $result ? $result->fetch_assoc()['total'] : 0;
    $result = $conn->query("SELECT SUM(total_amount) as total FROM bookings WHERE status = 'confirmed'");
    $stats['total_revenue'] = $result ? ($result->fetch_assoc()['total'] ?? 0) : 0;
    return $stats;
}

// Initialize
$currentUser = initAdminAuth();
$conn = getAdminConnection();

$dbSettings = [];
$result = $conn->query("SELECT setting_key, setting_value FROM admin_dashboard_settings");
if ($result) { while ($row = $result->fetch_assoc()) { $dbSettings[$row['setting_key']] = $row['setting_value']; } }

$anSettings = [];
$result = $conn->query("SELECT setting_key, setting_value FROM analytics_settings");
if ($result) { while ($row = $result->fetch_assoc()) { $anSettings[$row['setting_key']] = $row['setting_value']; } }

$logoText = $dbSettings['admin_logo_text'] ?? 'SJDM ADMIN';
$moduleTitle = $anSettings['module_title'] ?? 'Analytics Dashboard';
$moduleSubtitle = $anSettings['module_subtitle'] ?? 'System performance and insights';
$adminMark = $dbSettings['admin_mark_label'] ?? 'A';

$adminInfo = ['role_title' => 'Administrator', 'admin_mark' => 'A'];
$stmt = $conn->prepare("SELECT admin_mark, role_title FROM admin_users WHERE user_id = ?");
$userId = $currentUser['id'];
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
if ($row = $result->fetch_assoc()) { $adminInfo = $row; }
$stmt->close();

$menuItems = [];
$result = $conn->query("SELECT * FROM admin_menu_items WHERE is_active = 1 ORDER BY display_order ASC");
if ($result) { while ($row = $result->fetch_assoc()) { $menuItems[] = $row; } }

$stats = getAdminStats($conn);
$bookingStats = getBookingStats($conn);

// Monthly revenue (last 6 months)
$monthlyRevenue = [];
for ($i = 5; $i >= 0; $i--) {
    $month = date('Y-m', strtotime("-$i months"));
    $result = $conn->query("SELECT SUM(total_amount) as revenue, COUNT(*) as bookings FROM bookings WHERE DATE_FORMAT(created_at, '%Y-%m') = '$month' AND status = 'confirmed'");
    $data = $result ? $result->fetch_assoc() : ['revenue' => 0, 'bookings' => 0];
    $monthlyRevenue[] = ['month' => date('M Y', strtotime($month)), 'revenue' => $data['revenue'] ?? 0, 'bookings' => $data['bookings'] ?? 0];
}

// User registration trends
$userTrends = [];
for ($i = 11; $i >= 0; $i--) {
    $month = date('Y-m', strtotime("-$i months"));
    $result = $conn->query("SELECT COUNT(*) as users FROM users WHERE DATE_FORMAT(created_at, '%Y-%m') = '$month' AND user_type = 'user'");
    $data = $result ? $result->fetch_assoc() : ['users' => 0];
    $userTrends[] = ['month' => date('M Y', strtotime($month)), 'users' => $data['users'] ?? 0];
}

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
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet">
<link rel="stylesheet" href="admin-styles.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<style>
/* ── Compact Stats (From Destinations) ── */
.um-stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(170px, 1fr)); gap: 14px; margin-bottom: 24px; }
.stat-card-compact { background: white; border-radius: 14px; padding: 16px 18px; box-shadow: 0 2px 12px rgba(0,0,0,.07); border: 1px solid rgba(0,0,0,.06); transition: transform .25s, box-shadow .25s; display: flex; flex-direction: column; }
.stat-card-compact:hover { transform: translateY(-3px); box-shadow: 0 8px 24px rgba(0,0,0,.12); }
.stat-card-compact[data-stat="users"]   { border-top: 3px solid #ec4899; background: #fdf5fb; }
.stat-card-compact[data-stat="bookings"]{ border-top: 3px solid #667eea; background: #fafbff; }
.stat-card-compact[data-stat="revenue"] { border-top: 3px solid #f59e0b; background: #fffbeb; }
.stat-card-compact[data-stat="spots"]   { border-top: 3px solid #10b981; background: #f5fdf9; }
.scc-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 10px; }
.scc-label { display: flex; align-items: center; gap: 5px; font-size: .68rem; font-weight: 700; text-transform: uppercase; letter-spacing: .8px; color: #6b7280; }
.scc-label .material-icons-outlined { font-size: 14px; color: #9ca3af; }
.scc-dot { width: 9px; height: 9px; border-radius: 50%; flex-shrink: 0; }
.dot-pink   { background: #ec4899; }
.dot-blue   { background: #667eea; }
.dot-orange { background: #f59e0b; }
.dot-green  { background: #10b981; }
.scc-number { font-size: 2rem; font-weight: 800; color: #111827; line-height: 1; margin-bottom: 10px; }
.scc-trend { display: inline-flex; align-items: center; gap: 3px; font-size: .72rem; font-weight: 700; padding: 3px 8px; border-radius: 20px; width: fit-content; }
.scc-trend.positive { color: #059669; background: rgba(16,185,129,.12); }
.scc-trend.neutral  { color: #6b7280; background: rgba(107,114,128,.1); }
.scc-trend .material-icons-outlined { font-size: 13px; }

/* ── Chart Containers (From Destinations Style) ── */
.chart-container { background: white; border-radius: 16px; padding: 20px 24px; box-shadow: 0 2px 12px rgba(0,0,0,.07); border: 1px solid rgba(0,0,0,.06); transition: transform .25s, box-shadow .25s; }
.chart-container:hover { transform: translateY(-3px); box-shadow: 0 8px 24px rgba(0,0,0,.12); }
.chart-container h3 { margin: 0 0 20px; font-size: 1rem; font-weight: 700; color: #111827; display: flex; align-items: center; gap: 8px; }
.chart-container h3 .material-icons-outlined { font-size: 18px; color: #667eea; }
.chart-wrapper { position: relative; height: 280px; background: linear-gradient(135deg,#f8fafc,#ffffff); border-radius: 12px; padding: 16px; border: 1px solid #e5e7eb; }

/* ── Buttons (From Destinations) ── */
.btn-primary { background: linear-gradient(135deg,#667eea,#764ba2); color: white; border: none; padding: 10px 18px; border-radius: 10px; font-size: .85rem; font-weight: 600; cursor: pointer; transition: all .2s; display: inline-flex; align-items: center; gap: 6px; font-family: inherit; }
.btn-primary:hover { opacity: .9; transform: translateY(-1px); box-shadow: 0 4px 12px rgba(102,126,234,.35); }
.btn-cancel { background: #f3f4f6; color: #374151; border: 1px solid #e5e7eb; padding: 10px 20px; border-radius: 10px; font-size: .875rem; font-weight: 600; cursor: pointer; transition: all .2s; font-family: inherit; }
.btn-cancel:hover { background: #e5e7eb; }

/* ── Toast feedback (From Destinations) ── */
.toast-wrap { position: fixed; bottom: 24px; right: 24px; z-index: 9999; display: flex; flex-direction: column; gap: 10px; }
.toast { display: flex; align-items: center; gap: 10px; padding: 12px 16px; border-radius: 12px; font-size: .875rem; font-weight: 600; min-width: 260px; box-shadow: 0 4px 20px rgba(0,0,0,.12); transform: translateX(120%); opacity: 0; transition: all .3s ease; }
.toast.show { transform: translateX(0); opacity: 1; }
.toast.success { background: #f0fdf4; border: 1px solid #bbf7d0; color: #15803d; }
.toast.error   { background: #fef2f2; border: 1px solid #fecaca; color: #b91c1c; }
.toast .material-icons-outlined { font-size: 20px; }

/* ── Sign Out Modal (From Destinations) ── */
.modal { display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background: rgba(0,0,0,.45); backdrop-filter: blur(4px); }
.modal.show { display: flex !important; align-items: center; justify-content: center; }
.modal-content { background: white; border-radius: 16px; width: 90%; max-width: 440px; max-height: 90vh; overflow-y: auto; box-shadow: 0 20px 60px rgba(0,0,0,.2); animation: slideIn .25s ease; }
.modal-content.small { max-width: 440px; }
@keyframes slideIn { from { transform: translateY(-20px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
.modal-header { display: flex; justify-content: space-between; align-items: center; padding: 20px 24px; border-bottom: 1px solid #f3f4f6; position: sticky; top: 0; background: white; z-index: 1; border-radius: 16px 16px 0 0; }
.modal-header h2 { margin: 0; font-size: 1.1rem; font-weight: 700; color: #111827; }
.modal-close { width: 32px; height: 32px; border: none; background: #f3f4f6; border-radius: 8px; cursor: pointer; display: flex; align-items: center; justify-content: center; color: #6b7280; transition: all .2s; }
.modal-close:hover { background: #e5e7eb; color: #111827; }
.modal-close .material-icons-outlined { font-size: 18px; }
.modal-body { padding: 24px; }
.modal-footer { display: flex; justify-content: flex-end; gap: 10px; padding: 16px 24px; border-top: 1px solid #f3f4f6; position: sticky; bottom: 0; background: white; border-radius: 0 0 16px 16px; }
.signout-content { display: flex; flex-direction: column; align-items: center; gap: 20px; padding: 16px 0; text-align: center; }
.signout-icon { width: 72px; height: 72px; border-radius: 50%; background: linear-gradient(135deg,#ef4444,#dc2626); color: white; display: flex; align-items: center; justify-content: center; box-shadow: 0 8px 24px rgba(239,68,68,.3); }
.signout-icon .material-icons-outlined { font-size: 36px; }
.signout-message h3 { margin: 0 0 6px; font-size: 1.1rem; font-weight: 700; color: #111827; }
.signout-message p  { margin: 0; font-size: .875rem; color: #6b7280; }
.btn-submit { background: linear-gradient(135deg,#667eea,#764ba2); color: white; border: none; padding: 10px 20px; border-radius: 10px; font-size: .875rem; font-weight: 600; cursor: pointer; transition: all .2s; font-family: inherit; }
.btn-submit:hover { opacity: .9; transform: translateY(-1px); }

@media (max-width: 640px) {
    .um-stats-grid { grid-template-columns: repeat(2,1fr); }
    .chart-wrapper { height: 220px; }
}
</style>
</head>
<body>
<div class="admin-container">
<!-- Sidebar -->
<aside class="sidebar">
<div class="sidebar-header">
<div class="logo" style="display:flex;align-items:center;gap:12px;">
<img src="../lgo.png" alt="SJDM Tours Logo" style="height:40px;width:40px;object-fit:contain;border-radius:8px;">
<span>SJDM ADMIN</span>
</div>
</div>
<nav class="sidebar-nav">
<?php foreach ($menuItems as $item):
if (stripos($item['menu_name'],'hotels')!==false||stripos($item['menu_url'],'hotels')!==false||
stripos($item['menu_name'],'settings')!==false||stripos($item['menu_url'],'settings')!==false||
stripos($item['menu_name'],'reports')!==false||stripos($item['menu_url'],'reports')!==false) continue;
$isActive = basename($_SERVER['PHP_SELF'])==$item['menu_url']?'active':'';
$badgeVal = isset($item['badge_query'])&&isset($queryValues[$item['badge_query']])?$queryValues[$item['badge_query']]:0;
?>
<a href="<?php echo $item['menu_url']; ?>" class="nav-item <?php echo $isActive; ?>">
<span class="material-icons-outlined"><?php echo $item['menu_icon']; ?></span>
<span><?php echo $item['menu_name']; ?></span>
<?php if ($badgeVal>0): ?><span class="badge"><?php echo $badgeVal; ?></span><?php endif; ?>
</a>
<?php endforeach; ?>
</nav>
<div class="sidebar-footer">
<a href="javascript:void(0)" class="logout-btn" onclick="openModal('signOutModal')">
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
<button class="btn-primary" onclick="exportAnalytics()">
<span class="material-icons-outlined" style="font-size:18px;">download</span>
Export
</button>
<div class="profile-dropdown">
<button class="profile-button" id="adminProfileButton">
<div class="profile-avatar"><?php echo substr($adminMark,0,1); ?></div>
<span class="material-icons-outlined">expand_more</span>
</button>
<div class="dropdown-menu" id="adminProfileMenu">
<div class="profile-info">
<div class="profile-avatar large"><?php echo substr($adminMark,0,1); ?></div>
<div class="profile-details">
<h3 class="admin-name"><?php echo htmlspecialchars($currentUser['first_name'].' '.$currentUser['last_name']); ?></h3>
<p class="admin-email"><?php echo htmlspecialchars($currentUser['email']); ?></p>
</div>
</div>
<div class="dropdown-divider"></div>
<a href="javascript:void(0)" class="dropdown-item" id="adminAccountLink"><span class="material-icons-outlined">account_circle</span><span>My Account</span></a>
<div class="dropdown-divider"></div>
<a href="javascript:void(0)" class="dropdown-item" id="adminSettingsLink"><span class="material-icons-outlined">settings</span><span>Settings</span></a>
<div class="dropdown-divider"></div>
<a href="javascript:void(0)" class="dropdown-item" id="adminHelpLink"><span class="material-icons-outlined">help_outline</span><span>Help &amp; Support</span></a>
<a href="javascript:void(0)" class="dropdown-item" id="adminSignoutLink" onclick="openModal('signOutModal')"><span class="material-icons-outlined">logout</span><span>Sign Out</span></a>
</div>
</div>
</div>
</header>

<div class="content-area">
<!-- Compact Stats -->
<div class="um-stats-grid">
<div class="stat-card-compact" data-stat="users">
<div class="scc-header">
<div class="scc-label"><span class="material-icons-outlined">groups</span> Total Users</div>
<span class="scc-dot dot-pink"></span>
</div>
<div class="scc-number"><?php echo $stats['totalUsers']; ?></div>
<div class="scc-trend positive"><span class="material-icons-outlined">north_east</span><span>Registered</span></div>
</div>
<div class="stat-card-compact" data-stat="bookings">
<div class="scc-header">
<div class="scc-label"><span class="material-icons-outlined">book</span> Total Bookings</div>
<span class="scc-dot dot-blue"></span>
</div>
<div class="scc-number"><?php echo $stats['totalBookings']; ?></div>
<div class="scc-trend positive"><span class="material-icons-outlined">north_east</span><span>All time</span></div>
</div>
<div class="stat-card-compact" data-stat="revenue">
<div class="scc-header">
<div class="scc-label"><span class="material-icons-outlined">payments</span> Total Revenue</div>
<span class="scc-dot dot-orange"></span>
</div>
<div class="scc-number">₱<?php echo number_format($bookingStats['total_revenue'], 0); ?></div>
<div class="scc-trend positive"><span class="material-icons-outlined">attach_money</span><span>Confirmed</span></div>
</div>
<div class="stat-card-compact" data-stat="spots">
<div class="scc-header">
<div class="scc-label"><span class="material-icons-outlined">tour</span> Destinations</div>
<span class="scc-dot dot-green"></span>
</div>
<div class="scc-number"><?php echo $stats['totalDestinations']; ?></div>
<div class="scc-trend neutral"><span class="material-icons-outlined">apps</span><span>Available</span></div>
</div>
</div>

<!-- Charts Row 1 -->
<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 20px; margin-bottom: 24px;">
<!-- Monthly Revenue Chart -->
<div class="chart-container">
<h3><span class="material-icons-outlined">trending_up</span> Monthly Revenue Trend</h3>
<div class="chart-wrapper">
<canvas id="revenueChart"></canvas>
</div>
</div>
<!-- Booking Status Pie Chart -->
<div class="chart-container">
<h3><span class="material-icons-outlined">pie_chart</span> Booking Status</h3>
<div class="chart-wrapper">
<canvas id="statusChart"></canvas>
</div>
</div>
</div>

<!-- Charts Row 2 -->
<div style="display: grid; grid-template-columns: 1fr; gap: 20px;">
<!-- User Registration Trend -->
<div class="chart-container">
<h3><span class="material-icons-outlined">person_add</span> User Registration Trend</h3>
<div class="chart-wrapper">
<canvas id="userTrendChart"></canvas>
</div>
</div>
</div>
</div>
</main>
</div>

<!-- Toast container -->
<div class="toast-wrap" id="toastWrap"></div>

<!-- SIGN OUT MODAL -->
<div id="signOutModal" class="modal">
<div class="modal-content small">
<div class="modal-header">
<h2>Sign Out</h2>
<button class="modal-close" onclick="closeModal('signOutModal')"><span class="material-icons-outlined">close</span></button>
</div>
<div class="modal-body">
<div class="signout-content">
<div class="signout-icon"><span class="material-icons-outlined">logout</span></div>
<div class="signout-message">
<h3>Are you sure you want to sign out?</h3>
<p>You will be redirected to the login page.</p>
</div>
</div>
</div>
<div class="modal-footer">
<button class="btn-cancel" onclick="closeModal('signOutModal')">Cancel</button>
<button class="btn-submit" onclick="window.location.href='logout.php'">Sign Out</button>
</div>
</div>
</div>

<script src="admin-script.js"></script>
<script src="admin-profile-dropdown.js"></script>
<script>
/* ── Core Helpers (From Destinations) ── */
const $id = id => document.getElementById(id);
function openModal(id)  { const m=$id(id); if(m){m.classList.add('show'); document.body.style.overflow='hidden';} }
function closeModal(id) { const m=$id(id); if(m){m.classList.remove('show'); document.body.style.overflow='';} }
function showToast(msg, type='success') {
    const w = $id('toastWrap');
    const t = document.createElement('div');
    t.className = `toast ${type}`;
    t.innerHTML = `<span class="material-icons-outlined">${type==='success'?'check_circle':'error'}</span>${msg}`;
    w.appendChild(t);
    requestAnimationFrame(() => t.classList.add('show'));
    setTimeout(() => { t.classList.remove('show'); setTimeout(()=>t.remove(), 300); }, 3000);
}

/* ── Monthly Revenue Chart ── */
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
            pointBorderWidth: 2
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
            yAxisID: 'y1'
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        interaction: { mode: 'index', intersect: false },
        plugins: {
            legend: {
                display: true,
                position: 'top',
                labels: { usePointStyle: true, padding: 20, font: { size: 12, weight: '600' }, color: '#64748b' }
            },
            tooltip: {
                backgroundColor: 'rgba(0, 0, 0, 0.9)',
                titleColor: '#ffffff',
                bodyColor: '#ffffff',
                padding: 12,
                cornerRadius: 12,
                callbacks: {
                    label: function(context) {
                        let label = context.dataset.label || '';
                        if (label) label += ': ';
                        if (context.parsed.y !== null) {
                            if (context.datasetIndex === 0) { label += '₱' + context.parsed.y.toLocaleString(); }
                            else { label += context.parsed.y.toLocaleString() + ' bookings'; }
                        }
                        return label;
                    }
                }
            }
        },
        scales: {
            x: { grid: { display: false }, ticks: { color: '#64748b', font: { size: 11, weight: '500' } } },
            y: {
                type: 'linear', display: true, position: 'left',
                grid: { color: 'rgba(0, 0, 0, 0.05)', borderDash: [5, 5] },
                ticks: { color: '#64748b', font: { size: 11, weight: '500' }, callback: function(value) { return '₱' + value.toLocaleString(); } }
            },
            y1: {
                type: 'linear', display: true, position: 'right',
                grid: { drawOnChartArea: false },
                ticks: { color: '#64748b', font: { size: 11, weight: '500' }, callback: function(value) { return value.toLocaleString(); } }
            }
        },
        animation: { duration: 2000, easing: 'easeInOutQuart' }
    }
});

/* ── Booking Status Chart ── */
const statusCtx = document.getElementById('statusChart').getContext('2d');
const statusChart = new Chart(statusCtx, {
    type: 'doughnut',
    data: {
        labels: <?php echo json_encode(array_keys($bookingStats['by_status'])); ?>,
        datasets: [{
            data: <?php echo json_encode(array_values($bookingStats['by_status'])); ?>,
            backgroundColor: ['#f59e0b', '#3b82f6', '#10b981', '#ef4444'],
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
                labels: { usePointStyle: true, padding: 20, font: { size: 12, weight: '600' }, color: '#64748b' }
            },
            tooltip: {
                backgroundColor: 'rgba(0, 0, 0, 0.9)',
                titleColor: '#ffffff',
                bodyColor: '#ffffff',
                padding: 12,
                cornerRadius: 12,
                callbacks: {
                    label: function(context) {
                        let label = context.label || '';
                        if (label) label += ': ';
                        if (context.parsed !== null) { label += context.parsed.toLocaleString() + ' bookings'; }
                        return label;
                    }
                }
            }
        },
        animation: { animateRotate: true, animateScale: true, duration: 2000, easing: 'easeInOutQuart' },
        cutout: '60%'
    }
});

/* ── User Registration Trend Chart ── */
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
                labels: { usePointStyle: true, padding: 20, font: { size: 12, weight: '600' }, color: '#64748b' }
            },
            tooltip: {
                backgroundColor: 'rgba(0, 0, 0, 0.9)',
                titleColor: '#ffffff',
                bodyColor: '#ffffff',
                padding: 12,
                cornerRadius: 12,
                callbacks: {
                    label: function(context) {
                        let label = context.dataset.label || '';
                        if (label) label += ': ';
                        if (context.parsed.y !== null) { label += context.parsed.y.toLocaleString() + ' users'; }
                        return label;
                    }
                }
            }
        },
        scales: {
            x: { grid: { display: false }, ticks: { color: '#64748b', font: { size: 11, weight: '500' } } },
            y: {
                beginAtZero: true,
                grid: { color: 'rgba(0, 0, 0, 0.05)', borderDash: [5, 5] },
                ticks: { color: '#64748b', font: { size: 11, weight: '500' }, callback: function(value) { return value.toLocaleString(); } }
            }
        },
        animation: { duration: 2000, easing: 'easeInOutQuart' }
    }
});

/* ── Export Function ── */
function exportAnalytics() {
    showToast('Export functionality will be implemented soon.', 'success');
}

/* ── Sidebar Logout ── */
document.addEventListener('DOMContentLoaded', function() {
    const logoutBtn = document.querySelector('.logout-btn');
    if (logoutBtn) {
        logoutBtn.addEventListener('click', function(e) {
            e.preventDefault();
            openModal('signOutModal');
        });
    }
});

/* ── Overlay Click / Escape ── */
const allModals = ['signOutModal'];
window.addEventListener('click', e=>{ allModals.forEach(id=>{ const m=$id(id); if(m&&e.target===m) closeModal(id); }); });
document.addEventListener('keydown', e=>{ if(e.key==='Escape') allModals.forEach(id=>closeModal(id)); });
</script>
<?php closeAdminConnection($conn); ?>
</body>
</html>