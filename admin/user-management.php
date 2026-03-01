<?php
// User Management Module
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/auth.php';

function getAdminConnection() { return getDatabaseConnection(); }
function initAdminAuth() { requireAdmin(); return getCurrentUser(); }
function closeAdminConnection($conn) { closeDatabaseConnection($conn); }

function addUser($conn, $data) {
    try {
        $stmt = $conn->prepare("INSERT INTO users (first_name, last_name, email, password, user_type, status, created_at) VALUES (?, ?, ?, ?, 'user', 'active', NOW())");
        $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);
        $stmt->bind_param("ssss", $data['first_name'], $data['last_name'], $data['email'], $hashedPassword);
        return $stmt->execute() ? ['success' => true, 'message' => 'User added successfully'] : ['success' => false, 'message' => 'Failed to add user'];
    } catch (Exception $e) { return ['success' => false, 'message' => 'Error: ' . $e->getMessage()]; }
}

function editUser($conn, $data) {
    try {
        if (!empty($data['password'])) {
            $stmt = $conn->prepare("UPDATE users SET first_name = ?, last_name = ?, email = ?, status = ?, password = ? WHERE id = ?");
            $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);
            $stmt->bind_param("sssssi", $data['first_name'], $data['last_name'], $data['email'], $data['status'], $hashedPassword, $data['user_id']);
        } else {
            $stmt = $conn->prepare("UPDATE users SET first_name = ?, last_name = ?, email = ?, status = ? WHERE id = ?");
            $stmt->bind_param("ssssi", $data['first_name'], $data['last_name'], $data['email'], $data['status'], $data['user_id']);
        }
        return $stmt->execute() ? ['success' => true, 'message' => 'User updated successfully'] : ['success' => false, 'message' => 'Failed to update user'];
    } catch (Exception $e) { return ['success' => false, 'message' => 'Error: ' . $e->getMessage()]; }
}

function deleteUser($conn, $userId, $justification = '') {
    try {
        // Get user data before deletion for email notification
        $userStmt = $conn->prepare("SELECT * FROM users WHERE id = ? AND user_type = 'user'");
        $userStmt->bind_param("i", $userId);
        $userStmt->execute();
        $userData = $userStmt->get_result()->fetch_assoc();
        $userStmt->close();
        
        // Log the deletion with justification (non-fatal if table doesn't exist)
        try {
            if (session_status() === PHP_SESSION_NONE) session_start();
            $stmt = $conn->prepare("INSERT INTO deletion_log (item_type, item_id, justification, deleted_by, deleted_at) VALUES (?, ?, ?, ?, NOW())");
            if ($stmt) {
                $itemType = 'user';
                $deletedBy = $_SESSION['user_id'] ?? 0;
                $stmt->bind_param("sisi", $itemType, $userId, $justification, $deletedBy);
                $stmt->execute();
            }
        } catch (Exception $logEx) {
            // Log table may not exist; continue with deletion
        }
        
        // Send email notification to the user
        $emailSent = false;
        $emailMessage = '';
        if ($userData && !empty($userData['email'])) {
            $emailResult = sendUserDeletionEmail($userData['email'], $userData, $justification);
            $emailSent = $emailResult['success'];
            $emailMessage = $emailResult['message'];
        }
        
        // Proceed with deletion
        $stmt = $conn->prepare("DELETE FROM users WHERE id = ? AND user_type = 'user'");
        $stmt->bind_param("i", $userId);
        $deleteSuccess = $stmt->execute();
        
        if ($deleteSuccess) {
            $message = 'User deleted successfully with justification logged';
            if ($emailSent) {
                $message .= ' and notification email sent';
            } else if (!empty($emailMessage)) {
                $message .= ' (Email notification failed: ' . $emailMessage . ')';
            }
            return ['success' => true, 'message' => $message];
        } else {
            return ['success' => false, 'message' => 'Failed to delete user'];
        }
        
    } catch (Exception $e) { 
        return ['success' => false, 'message' => 'Error: ' . $e->getMessage()]; 
    }
}

function getUser($conn, $userId) {
    try {
        $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            $activityStmt = $conn->prepare("SELECT login_time, ip_address, status FROM login_activity WHERE user_id = ? ORDER BY login_time DESC LIMIT 10");
            $activityStmt->bind_param("i", $userId);
            $activityStmt->execute();
            $activityResult = $activityStmt->get_result();
            $user['activity'] = [];
            while ($row = $activityResult->fetch_assoc()) { $user['activity'][] = $row; }
            return ['success' => true, 'data' => $user];
        } else { return ['success' => false, 'message' => 'User not found']; }
    } catch (Exception $e) { return ['success' => false, 'message' => 'Error: ' . $e->getMessage()]; }
}

function getUsersList($conn, $page = 1, $limit = 15, $search = '', $userType = '') {
    $offset = ($page - 1) * $limit;
    $search = $conn->real_escape_string($search);
    $userType = $conn->real_escape_string($userType);
    $usersQuery = "SELECT u.*, a.admin_mark, tg.name as guide_name, (SELECT COUNT(*) FROM bookings WHERE user_id = u.id) as total_bookings FROM users u LEFT JOIN admin_users a ON u.id = a.user_id LEFT JOIN tour_guides tg ON u.id = tg.user_id WHERE 1=1";
    if ($search) { $usersQuery .= " AND (u.first_name LIKE '%$search%' OR u.last_name LIKE '%$search%' OR u.email LIKE '%$search%')"; }
    if ($userType) { $usersQuery .= " AND u.user_type = '$userType'"; }
    $usersQuery .= " ORDER BY u.created_at DESC LIMIT $limit OFFSET $offset";
    $usersResult = $conn->query($usersQuery);
    $countQuery = "SELECT COUNT(*) as total FROM users WHERE 1=1";
    if ($search) { $countQuery .= " AND (first_name LIKE '%$search%' OR last_name LIKE '%$search%' OR email LIKE '%$search%')"; }
    if ($userType) { $countQuery .= " AND user_type = '$userType'"; }
    $countResult = $conn->query($countQuery);
    $totalCount = $countResult->fetch_assoc()['total'];
    $totalPages = ceil($totalCount / $limit);
    $users = [];
    while ($row = $usersResult->fetch_assoc()) { $users[] = $row; }
    return ['users' => $users, 'pagination' => ['current_page' => $page, 'total_pages' => $totalPages, 'total_count' => $totalCount, 'limit' => $limit]];
}

function getAdminStats($conn) {
    $stats = [];
    $result = $conn->query("SELECT COUNT(*) as total FROM users WHERE user_type = 'user'"); $stats['totalUsers'] = $result->fetch_assoc()['total'];
    $result = $conn->query("SELECT COUNT(*) as total FROM users WHERE user_type = 'user' AND status = 'active'"); $stats['activeUsers'] = $result->fetch_assoc()['total'];
    $result = $conn->query("SELECT COUNT(*) as total FROM bookings"); $stats['totalBookings'] = $result->fetch_assoc()['total'];
    $result = $conn->query("SELECT COUNT(DISTINCT user_id) as total FROM login_activity WHERE DATE(login_time) = CURDATE() AND status = 'success'"); $stats['todayLogins'] = $result->fetch_assoc()['total'];
    $result = $conn->query("SELECT COUNT(*) as total FROM tour_guides"); $stats['totalGuides'] = $result->fetch_assoc()['total'];
    $result = $conn->query("SELECT COUNT(*) as total FROM tourist_spots"); $stats['totalDestinations'] = $result->fetch_assoc()['total'];
    return $stats;
}

$currentUser = initAdminAuth();
$conn = getAdminConnection();

$dbSettings = [];
$result = $conn->query("SELECT setting_key, setting_value FROM admin_dashboard_settings");
if ($result) while ($row = $result->fetch_assoc()) $dbSettings[$row['setting_key']] = $row['setting_value'];

$umSettings = [];
$result = $conn->query("SELECT setting_key, setting_value FROM user_management_settings");
if ($result) while ($row = $result->fetch_assoc()) $umSettings[$row['setting_key']] = $row['setting_value'];

$moduleTitle = $umSettings['module_title'] ?? 'User Management';
$moduleSubtitle = $umSettings['module_subtitle'] ?? 'Manage system users';
$adminMark = $dbSettings['admin_mark_label'] ?? 'A';

$adminInfo = ['role_title' => 'Administrator', 'admin_mark' => 'A'];
$stmt = $conn->prepare("SELECT admin_mark, role_title FROM admin_users WHERE user_id = ?");
$userId = $currentUser['id'];
$stmt->bind_param("i", $userId);
$stmt->execute();
if ($row = $stmt->get_result()->fetch_assoc()) $adminInfo = $row;
$stmt->close();

$menuItems = [];
$result = $conn->query("SELECT * FROM admin_menu_items WHERE is_active = 1 ORDER BY display_order ASC");
if ($result) while ($row = $result->fetch_assoc()) $menuItems[] = $row;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    switch ($action) {
        case 'add_user': echo json_encode(addUser($conn, $_POST)); exit;
        case 'edit_user': echo json_encode(editUser($conn, $_POST)); exit;
        case 'delete_user': echo json_encode(deleteUser($conn, $_POST['user_id'], $_POST['justification'] ?? '')); exit;
        case 'get_user': echo json_encode(getUser($conn, $_POST['user_id'])); exit;
    }
}

$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$limit = intval($umSettings['default_user_limit'] ?? 15);
$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
$userType = isset($_GET['user_type']) ? $conn->real_escape_string($_GET['user_type']) : '';

$usersData = getUsersList($conn, $page, $limit, $search, $userType);
$users = $usersData['users'];
$pagination = $usersData['pagination'];
$stats = getAdminStats($conn);

$queryValues = ['totalUsers' => $stats['totalUsers'], 'totalBookings' => $stats['totalBookings']];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>User Management | SJDM Tours Admin</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet">
<link rel="stylesheet" href="admin-styles.css">
<style>
/* ── Compact Stats Grid ── */
.um-stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 14px; margin-bottom: 24px; }
.stat-card-compact { background: white; border-radius: 14px; padding: 16px 18px; box-shadow: 0 2px 12px rgba(0,0,0,.07); border: 1px solid rgba(0,0,0,.06); transition: transform .25s, box-shadow .25s; display: flex; flex-direction: column; }
.stat-card-compact:hover { transform: translateY(-3px); box-shadow: 0 8px 24px rgba(0,0,0,.12); }
.stat-card-compact[data-stat="totalUsers"]  { border-top: 3px solid #667eea; background: #fafbff; }
.stat-card-compact[data-stat="activeUsers"] { border-top: 3px solid #10b981; background: #f5fdf9; }
.stat-card-compact[data-stat="todayLogins"] { border-top: 3px solid #f59e0b; background: #fffdf5; }
.scc-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 10px; }
.scc-label { display: flex; align-items: center; gap: 5px; font-size: .68rem; font-weight: 700; text-transform: uppercase; letter-spacing: .8px; color: #6b7280; }
.scc-label .material-icons-outlined { font-size: 14px; color: #9ca3af; }
.scc-dot { width: 9px; height: 9px; border-radius: 50%; flex-shrink: 0; }
.dot-blue   { background: #667eea; }
.dot-green  { background: #10b981; }
.dot-yellow { background: #f59e0b; }
.scc-number { font-size: 2rem; font-weight: 800; color: #111827; line-height: 1; margin-bottom: 10px; }
.scc-trend { display: inline-flex; align-items: center; gap: 3px; font-size: .72rem; font-weight: 700; padding: 3px 8px; border-radius: 20px; width: fit-content; }
.scc-trend.positive { color: #059669; background: rgba(16,185,129,.12); }
.scc-trend .material-icons-outlined { font-size: 13px; }

/* ── Search Bar ── */
.search-bar { display: flex; gap: 10px; margin-bottom: 20px; align-items: center; }
.search-bar input { flex: 1; padding: 10px 16px; border: 1px solid #e5e7eb; border-radius: 10px; font-size: .9rem; outline: none; font-family: inherit; transition: border-color .2s; }
.search-bar input:focus { border-color: #667eea; box-shadow: 0 0 0 3px rgba(102,126,234,.1); }

/* ── Filter Buttons ── */
.filter-buttons { display: flex; gap: 8px; margin-bottom: 16px; flex-wrap: wrap; }
.filter-btn { display: flex; align-items: center; gap: 6px; padding: 8px 14px; border: 1px solid #e5e7eb; background: white; border-radius: 20px; cursor: pointer; font-size: .82rem; font-weight: 600; color: #6b7280; transition: all .2s; font-family: inherit; }
.filter-btn .material-icons-outlined { font-size: 16px; }
.filter-btn:hover { border-color: #667eea; color: #667eea; background: rgba(102,126,234,.06); }
.filter-btn.active { background: #667eea; border-color: #667eea; color: white; }

/* ── Action buttons (From Destinations) ── */
.action-buttons { display: flex; gap: 6px; }
.btn-icon { width: 32px; height: 32px; border: 1px solid #e5e7eb; border-radius: 8px; background: white; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: all .2s; color: #6b7280; }
.btn-icon:hover { background: #f3f4f6; color: #111827; border-color: #d1d5db; }
.btn-icon.edit:hover   { background: #eff6ff; color: #2563eb; border-color: #bfdbfe; }
.btn-icon.del:hover    { background: #fef2f2; color: #dc2626; border-color: #fecaca; }
.btn-icon .material-icons-outlined { font-size: 16px; }

/* ── Buttons ── */
.btn-primary { background: linear-gradient(135deg,#667eea,#764ba2); color: white; border: none; padding: 10px 18px; border-radius: 10px; font-size: .85rem; font-weight: 600; cursor: pointer; transition: all .2s; display: inline-flex; align-items: center; gap: 6px; font-family: inherit; }
.btn-primary:hover { opacity: .9; transform: translateY(-1px); box-shadow: 0 4px 12px rgba(102,126,234,.35); }
.btn-secondary { background: #f3f4f6; color: #374151; border: 1px solid #e5e7eb; padding: 10px 20px; border-radius: 10px; font-size: .875rem; font-weight: 600; cursor: pointer; transition: all .2s; font-family: inherit; }
.btn-secondary:hover { background: #e5e7eb; }
.btn-danger { background: #ef4444; color: white; border: none; padding: 10px 20px; border-radius: 10px; font-size: .875rem; font-weight: 600; cursor: pointer; transition: all .2s; font-family: inherit; }
.btn-danger:hover { background: #dc2626; }

/* ── Pagination ── */
.pagination { display: flex; align-items: center; gap: 8px; margin-top: 24px; justify-content: center; }
.pagination-btn { display: flex; align-items: center; gap: 5px; padding: 9px 16px; border: 1px solid #e5e7eb; background: white; border-radius: 9px; color: #374151; font-size: .85rem; font-weight: 600; cursor: pointer; transition: all .2s; font-family: inherit; }
.pagination-btn:hover { background: #667eea; color: white; border-color: #667eea; }
.pagination-btn .material-icons-outlined { font-size: 18px; }
.pagination-numbers { display: flex; gap: 6px; }
.pagination-number { min-width: 38px; height: 38px; border: 1px solid #e5e7eb; border-radius: 8px; background: white; color: #374151; font-size: .85rem; font-weight: 600; cursor: pointer; transition: all .2s; display: flex; align-items: center; justify-content: center; font-family: inherit; }
.pagination-number:hover { background: #f3f4f6; border-color: #667eea; }
.pagination-number.active { background: #667eea; color: white; border-color: #667eea; }
.pagination-ellipsis { color: #9ca3af; font-weight: 500; padding: 0 8px; }

/* ── Status Badges ── */
.status-badge { display: inline-block; padding: 4px 10px; border-radius: 8px; font-size: .75rem; font-weight: 700; text-transform: capitalize; }
.status-badge.status-active    { background: #f0fdf4; color: #15803d; border: 1px solid #bbf7d0; }
.status-badge.status-inactive  { background: #f3f4f6; color: #6b7280; border: 1px solid #e5e7eb; }
.status-badge.status-suspended { background: #fef2f2; color: #dc2626; border: 1px solid #fecaca; }

/* ── Modals (From Destinations) ── */
.modal { display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background: rgba(0,0,0,.45); backdrop-filter: blur(4px); }
.modal.show { display: flex !important; align-items: center; justify-content: center; }
.modal-content { background: white; border-radius: 16px; width: 90%; max-width: 500px; max-height: 90vh; overflow-y: auto; box-shadow: 0 20px 60px rgba(0,0,0,.2); animation: slideIn .25s ease; }
.modal-content.small { max-width: 440px; }
@keyframes slideIn { from { transform: translateY(-20px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
.modal-header { display: flex; justify-content: space-between; align-items: center; padding: 20px 24px; border-bottom: 1px solid #f3f4f6; position: sticky; top: 0; background: white; z-index: 1; border-radius: 16px 16px 0 0; }
.modal-header h2 { margin: 0; font-size: 1.1rem; font-weight: 700; color: #111827; }
.modal-close { width: 32px; height: 32px; border: none; background: #f3f4f6; border-radius: 8px; cursor: pointer; display: flex; align-items: center; justify-content: center; color: #6b7280; transition: all .2s; }
.modal-close:hover { background: #e5e7eb; color: #111827; }
.modal-close .material-icons-outlined { font-size: 18px; }
.modal-body { padding: 24px; }
.modal-footer { display: flex; justify-content: flex-end; gap: 10px; padding: 16px 24px; border-top: 1px solid #f3f4f6; position: sticky; bottom: 0; background: white; border-radius: 0 0 16px 16px; }

/* ── Form in modals ── */
.form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
.form-group { margin-bottom: 16px; }
.form-group label { display: block; margin-bottom: 6px; font-size: .82rem; font-weight: 600; color: #374151; }
.form-group input, .form-group select { width: 100%; padding: 10px 14px; border: 1px solid #e5e7eb; border-radius: 10px; font-size: .875rem; font-family: inherit; outline: none; transition: border-color .2s; box-sizing: border-box; }
.form-group input:focus, .form-group select:focus { border-color: #667eea; box-shadow: 0 0 0 3px rgba(102,126,234,.1); }

/* ── Delete modal centered ── */
.delete-content { display: flex; align-items: center; gap: 16px; padding: 8px 0 16px; }
.delete-icon { width: 64px; height: 64px; border-radius: 50%; background: linear-gradient(135deg,#ef4444,#dc2626); display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.delete-icon .material-icons-outlined { font-size: 32px; color: white; }
.delete-message h3 { margin: 0 0 8px; font-size: 1rem; font-weight: 700; color: #111827; }
.delete-message p { margin: 0; font-size: .875rem; color: #6b7280; }

/* ── Sign Out Modal ── */
.signout-content { display: flex; flex-direction: column; align-items: center; gap: 20px; padding: 16px 0; text-align: center; }
.signout-icon { width: 72px; height: 72px; border-radius: 50%; background: linear-gradient(135deg,#ef4444,#dc2626); color: white; display: flex; align-items: center; justify-content: center; box-shadow: 0 8px 24px rgba(239,68,68,.3); }
.signout-icon .material-icons-outlined { font-size: 36px; }
.signout-message h3 { margin: 0 0 6px; font-size: 1.1rem; font-weight: 700; color: #111827; }
.signout-message p { margin: 0; font-size: .875rem; color: #6b7280; }

/* ── View User Modal ── */
.user-details { display: flex; align-items: center; gap: 20px; margin-bottom: 24px; padding: 20px; background: #f9fafb; border-radius: 12px; border: 1px solid #e5e7eb; }
.user-avatar { width: 60px; height: 60px; border-radius: 50%; background: linear-gradient(135deg,#667eea,#764ba2); color: white; display: flex; align-items: center; justify-content: center; font-size: 24px; font-weight: 700; flex-shrink: 0; }
.user-info h3 { margin: 0 0 8px; font-size: 18px; font-weight: 600; color: #111827; }
.user-info p { margin: 0 0 8px; font-size: 14px; color: #6b7280; }
.details-grid { display: grid; grid-template-columns: repeat(2,1fr); gap: 16px; }
.detail-item { display: flex; justify-content: space-between; align-items: center; padding: 12px 16px; background: #f9fafb; border-radius: 8px; border: 1px solid #e5e7eb; }
.detail-item label { font-weight: 600; color: #9ca3af; font-size: 13px; text-transform: uppercase; letter-spacing: .5px; }
.detail-item span { font-weight: 500; color: #111827; font-size: 14px; }

@media (max-width: 640px) {
    .um-stats-grid { grid-template-columns: repeat(2,1fr); }
    .form-row { grid-template-columns: 1fr; }
    .details-grid { grid-template-columns: 1fr; }
    .search-bar { flex-direction: column; align-items: stretch; }
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
<!-- User Statistics -->
<div class="um-stats-grid">
<div class="stat-card-compact" data-stat="totalUsers">
<div class="scc-header">
<div class="scc-label"><span class="material-icons-outlined">people</span> Total Users</div>
<span class="scc-dot dot-blue"></span>
</div>
<div class="scc-number"><?php echo $stats['totalUsers']; ?></div>
<div class="scc-trend positive"><span class="material-icons-outlined">north_east</span><span>Registered</span></div>
</div>
<div class="stat-card-compact" data-stat="activeUsers">
<div class="scc-header">
<div class="scc-label"><span class="material-icons-outlined">check_circle</span> Active Users</div>
<span class="scc-dot dot-green"></span>
</div>
<div class="scc-number"><?php echo $stats['activeUsers']; ?></div>
<div class="scc-trend positive"><span class="material-icons-outlined">north_east</span><span>Active</span></div>
</div>
<div class="stat-card-compact" data-stat="todayLogins">
<div class="scc-header">
<div class="scc-label"><span class="material-icons-outlined">login</span> Today's Logins</div>
<span class="scc-dot dot-yellow"></span>
</div>
<div class="scc-number"><?php echo $stats['todayLogins']; ?></div>
<div class="scc-trend positive"><span class="material-icons-outlined">north_east</span><span>Today</span></div>
</div>
</div>

<!-- Filter Buttons -->
<div class="filter-buttons">
<button class="filter-btn <?php echo $userType === '' ? 'active' : ''; ?>" onclick="filterByType('')">
<span class="material-icons-outlined">people</span> All Users
</button>
<button class="filter-btn <?php echo $userType === 'user' ? 'active' : ''; ?>" onclick="filterByType('user')">
<span class="material-icons-outlined">person</span> Regular Users
</button>
<button class="filter-btn <?php echo $userType === 'tour_guide' ? 'active' : ''; ?>" onclick="filterByType('tour_guide')">
<span class="material-icons-outlined">tour</span> Tour Guides
</button>
<button class="filter-btn <?php echo $userType === 'admin' ? 'active' : ''; ?>" onclick="filterByType('admin')">
<span class="material-icons-outlined">admin_panel_settings</span> Admins
</button>
</div>

<!-- Search -->
<div class="search-bar">
<input type="text" id="searchInput" placeholder="Search users by name or email..." value="<?php echo htmlspecialchars($search); ?>">
<button class="btn-secondary" onclick="searchUsers()">
<span class="material-icons-outlined" style="font-size:16px;vertical-align:middle;">search</span> Search
</button>
<button class="btn-secondary" onclick="clearSearch()">
<span class="material-icons-outlined" style="font-size:16px;vertical-align:middle;">clear</span> Clear
</button>
</div>

<!-- Users Table -->
<div class="table-container">
<table class="data-table">
<thead>
<tr>
<th>Name</th>
<th>Email</th>
<th>Status</th>
<th>Total Bookings</th>
<th>Joined</th>
<th>Actions</th>
</tr>
</thead>
<tbody>
<?php foreach ($users as $user): ?>
<tr>
<td>
<?php
if ($user['user_type'] == 'tour_guide' && !empty($user['guide_name'])):
echo htmlspecialchars($user['guide_name']);
else:
echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']);
endif;
?>
<?php if ($user['user_type'] == 'admin'): ?>
<span class="badge" style="background:#eff6ff;color:#2563eb;padding:2px 8px;border-radius:4px;font-size:11px;margin-left:5px;">ADMIN</span>
<?php elseif ($user['user_type'] == 'tour_guide'): ?>
<span class="badge" style="background:#f0f9ff;color:#0369a1;padding:2px 8px;border-radius:4px;font-size:11px;margin-left:5px;">TOUR GUIDE</span>
<?php endif; ?>
</td>
<td><?php echo htmlspecialchars($user['email']); ?></td>
<td><span class="status-badge status-<?php echo $user['status']; ?>"><?php echo ucfirst($user['status']); ?></span></td>
<td><?php echo $user['total_bookings']; ?></td>
<td><?php echo date('M j, Y', strtotime($user['created_at'])); ?></td>
<td>
<div class="action-buttons">
<button class="btn-icon" onclick="viewUser(<?php echo $user['id']; ?>)" title="View">
<span class="material-icons-outlined">visibility</span>
</button>
<button class="btn-icon edit" onclick="editUser(<?php echo $user['id']; ?>)" title="Edit">
<span class="material-icons-outlined">edit</span>
</button>
<button class="btn-icon del" onclick="showDeleteUserModal(<?php echo $user['id']; ?>, '<?php echo htmlspecialchars($user['first_name'].' '.$user['last_name']); ?>')" title="Delete">
<span class="material-icons-outlined">delete</span>
</button>
</div>
</td>
</tr>
<?php endforeach; ?>
</tbody>
</table>
</div>

<!-- Pagination -->
<?php if ($pagination['total_pages'] > 1): ?>
<div class="pagination">
<?php if ($pagination['current_page'] > 1): ?>
<button class="pagination-btn" onclick="goToPage(<?php echo $pagination['current_page'] - 1; ?>)">
<span class="material-icons-outlined">chevron_left</span> Previous
</button>
<?php endif; ?>
<div class="pagination-numbers">
<?php for ($i = 1; $i <= $pagination['total_pages']; $i++): ?>
<button class="pagination-number <?php echo $i == $pagination['current_page'] ? 'active' : ''; ?>" onclick="goToPage(<?php echo $i; ?>)"><?php echo $i; ?></button>
<?php endfor; ?>
</div>
<?php if ($pagination['current_page'] < $pagination['total_pages']): ?>
<button class="pagination-btn" onclick="goToPage(<?php echo $pagination['current_page'] + 1; ?>)">
Next <span class="material-icons-outlined">chevron_right</span>
</button>
<?php endif; ?>
</div>
<?php endif; ?>
</div>
</main>
</div>

<!-- Toast container -->
<div class="toast-wrap" id="toastWrap"></div>

<!-- VIEW USER MODAL -->
<div id="viewUserModal" class="modal">
<div class="modal-content">
<div class="modal-header">
<h2>User Details</h2>
<button class="modal-close" onclick="closeModal('viewUserModal')"><span class="material-icons-outlined">close</span></button>
</div>
<div class="modal-body" id="viewUserBody"></div>
<div class="modal-footer">
<button class="btn-secondary" onclick="closeModal('viewUserModal')">Close</button>
<button class="btn-primary" onclick="editUserFromView()">Edit User</button>
</div>
</div>
</div>

<!-- EDIT USER MODAL -->
<div id="editUserModal" class="modal">
<div class="modal-content">
<div class="modal-header">
<h2>Edit User</h2>
<button class="modal-close" onclick="closeModal('editUserModal')"><span class="material-icons-outlined">close</span></button>
</div>
<form id="editUserForm" onsubmit="handleEditUser(event)">
<input type="hidden" name="action" value="edit_user">
<input type="hidden" id="editUserId" name="user_id">
<div class="modal-body">
<div class="form-row">
<div class="form-group"><label>First Name *</label><input type="text" id="editFirstName" name="first_name" required></div>
<div class="form-group"><label>Last Name *</label><input type="text" id="editLastName" name="last_name" required></div>
</div>
<div class="form-group"><label>Email *</label><input type="email" id="editEmail" name="email" required></div>
<div class="form-group"><label>Status</label>
<select id="editStatus" name="status">
<option value="active">Active</option>
<option value="inactive">Inactive</option>
<option value="suspended">Suspended</option>
</select>
</div>
<div class="form-group"><label>New Password (leave blank to keep current)</label><input type="password" id="editPassword" name="password"></div>
</div>
<div class="modal-footer">
<button type="button" class="btn-secondary" onclick="closeModal('editUserModal')">Cancel</button>
<button type="submit" class="btn-primary">Update User</button>
</div>
</form>
</div>
</div>

<!-- DELETE USER MODAL -->
<div id="deleteUserModal" class="modal">
<div class="modal-content small">
<div class="modal-header">
<h2>Delete User</h2>
<button class="modal-close" onclick="closeModal('deleteUserModal')"><span class="material-icons-outlined">close</span></button>
</div>
<div class="modal-body">
<div class="delete-content">
<div class="delete-icon"><span class="material-icons-outlined">warning</span></div>
<div class="delete-message">
<h3>Are you sure you want to delete this user?</h3>
<p><strong id="deleteUserName"></strong></p>
<p style="font-size:.8rem;color:#9ca3af;margin-top:6px;">This action cannot be undone.</p>
</div>
</div>

<!-- Item Identification Section -->
<div style="margin-top:20px;padding:16px;background:#f9fafb;border-radius:10px;border:1px solid #e5e7eb;">
<h4 style="margin:0 0 12px;font-size:.9rem;font-weight:600;color:#374151;display:flex;align-items:center;gap:6px;">
<span class="material-icons-outlined" style="font-size:18px;color:#667eea;">person_search</span>
Item to be Deleted
</h4>
<div id="deleteUserDetails" style="font-size:.85rem;color:#6b7280;"></div>
</div>

<!-- Justification Section -->
<div style="margin-top:16px;">
<label for="deleteJustification" style="display:block;margin-bottom:6px;font-size:.82rem;font-weight:600;color:#374151;">
<span class="material-icons-outlined" style="font-size:16px;vertical-align:middle;margin-right:4px;color:#ef4444;">assignment_late</span>
Justification for Deletion <span style="color:#ef4444;">*</span>
</label>
<textarea id="deleteJustification" name="justification" rows="3" placeholder="Please provide a specific reason why this user must be deleted..." required
style="width:100%;padding:10px 14px;border:1px solid #e5e7eb;border-radius:10px;font-size:.875rem;font-family:inherit;outline:none;transition:border-color .2s;box-sizing:border-box;resize:vertical;"></textarea>
<div style="margin-top:6px;font-size:.75rem;color:#6b7280;">
Examples: Duplicate account, fraudulent activity, user request, inactive for 2+ years, etc.
</div>
</div>
</div>
<div class="modal-footer">
<button class="btn-secondary" onclick="closeModal('deleteUserModal')">Cancel</button>
<button class="btn-danger" id="deleteConfirmBtn">Delete User</button>
</div>
</div>
</div>

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
<button class="btn-secondary" onclick="closeModal('signOutModal')">Cancel</button>
<button class="btn-primary" onclick="window.location.href='logout.php'">Sign Out</button>
</div>
</div>
</div>


<script src="admin-profile-dropdown.js"></script>
<script>
/* ── Core Helpers (From Destinations) ── */
const $id = id => document.getElementById(id);
function escH(s) { const d=document.createElement('div'); d.textContent=s??'—'; return d.innerHTML||'—'; }
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

/* ── Filter & Search ── */
function filterByType(userType) {
    const url = new URL(window.location);
    userType ? url.searchParams.set('user_type', userType) : url.searchParams.delete('user_type');
    window.location.href = url;
}
function searchUsers() {
    const q = $id('searchInput').value;
    const url = new URL(window.location);
    q ? url.searchParams.set('search', q) : url.searchParams.delete('search');
    window.location.href = url;
}
function clearSearch() {
    const url = new URL(window.location);
    url.searchParams.delete('search');
    window.location.href = url;
}
$id('searchInput').addEventListener('keypress', e=>{ if(e.key==='Enter') searchUsers(); });
function goToPage(page) {
    const q = $id('searchInput').value;
    const t = new URL(window.location).searchParams.get('user_type');
    const url = new URL(window.location);
    url.searchParams.set('page', page);
    q ? url.searchParams.set('search', q) : url.searchParams.delete('search');
    t ? url.searchParams.set('user_type', t) : url.searchParams.delete('user_type');
    window.location.href = url;
}

/* ── View User ── */
let currentViewUserId = null;
function viewUser(userId) {
    fetch('', { method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'}, body:`action=get_user&user_id=${userId}` })
    .then(r=>r.json())
    .then(data => {
        if (!data.success) { showToast(data.message||'Failed to load', 'error'); return; }
        currentViewUserId = data.data.id;
        const u = data.data;
        $id('viewUserBody').innerHTML = `
            <div class="user-details">
                <div class="user-avatar">${(u.first_name?.[0]||'U').toUpperCase()}</div>
                <div class="user-info">
                    <h3>${escH(u.first_name+' '+u.last_name)}</h3>
                    <p>${escH(u.email)}</p>
                    <span class="status-badge status-${u.status||'inactive'}">${escH(u.status||'inactive')}</span>
                </div>
            </div>
            <div class="details-grid">
                <div class="detail-item"><label>User ID</label><span>#${u.id}</span></div>
                <div class="detail-item"><label>First Name</label><span>${escH(u.first_name)}</span></div>
                <div class="detail-item"><label>Last Name</label><span>${escH(u.last_name)}</span></div>
                <div class="detail-item"><label>Email</label><span>${escH(u.email)}</span></div>
                <div class="detail-item"><label>Status</label><span class="status-badge status-${u.status||'inactive'}">${escH(u.status||'inactive')}</span></div>
                <div class="detail-item"><label>User Type</label><span>${escH(u.user_type||'user')}</span></div>
                <div class="detail-item"><label>Total Bookings</label><span>${u.total_bookings||0}</span></div>
                <div class="detail-item"><label>Joined</label><span>${u.created_at ? new Date(u.created_at).toLocaleDateString() : '—'}</span></div>
            </div>
        `;
        openModal('viewUserModal');
    })
    .catch(e=>showToast('Error: '+e.message,'error'));
}

function editUserFromView() {
    if (!currentViewUserId) return;
    closeModal('viewUserModal');
    setTimeout(() => editUser(currentViewUserId), 150);
}

/* ── Edit User ── */
function editUser(userId) {
    fetch('', { method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'}, body:`action=get_user&user_id=${userId}` })
    .then(r=>r.json())
    .then(data => {
        if (!data.success) { showToast(data.message||'Failed to load','error'); return; }
        const u = data.data;
        $id('editUserId').value = u.id||'';
        $id('editFirstName').value = u.first_name||'';
        $id('editLastName').value = u.last_name||'';
        $id('editEmail').value = u.email||'';
        $id('editStatus').value = u.status||'active';
        openModal('editUserModal');
    })
    .catch(e=>showToast('Error: '+e.message,'error'));
}

function handleEditUser(event) {
    event.preventDefault();
    const btn = event.target.querySelector('button[type="submit"]');
    btn.textContent = 'Updating…'; btn.disabled = true;
    const formData = new FormData(event.target);
    const data = Object.fromEntries(formData.entries());
    data.action = 'edit_user';
    fetch('', { method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'}, body:new URLSearchParams(data) })
    .then(r=>r.json())
    .then(result => {
        if (result.success) {
            closeModal('editUserModal');
            showToast(result.message||'User updated successfully!');
            setTimeout(()=>location.reload(), 900);
        } else { showToast(result.message||'Failed to update','error'); btn.textContent='Update User'; btn.disabled=false; }
    })
    .catch(e=>{ showToast('Error: '+e.message,'error'); btn.textContent='Update User'; btn.disabled=false; });
}

/* ── Delete User ── */
let deleteUserId = null;
function showDeleteUserModal(userId, userName) {
    deleteUserId = userId;
    $id('deleteUserName').textContent = userName;
    
    // Fetch user details for identification
    fetch('', { method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'}, body:`action=get_user&user_id=${userId}` })
    .then(r=>r.json())
    .then(data => {
        if (data.success) {
            const user = data.data;
            const details = `
                <div style="display:grid;gap:8px;">
                    <div><strong>Name:</strong> ${escH(user.first_name + ' ' + user.last_name)}</div>
                    <div><strong>Email:</strong> ${escH(user.email)}</div>
                    <div><strong>User Type:</strong> ${escH(user.user_type)}</div>
                    <div><strong>Status:</strong> ${escH(user.status)}</div>
                    <div><strong>Member Since:</strong> ${escH(new Date(user.created_at).toLocaleDateString())}</div>
                    <div><strong>Total Bookings:</strong> ${user.total_bookings || 0}</div>
                </div>
            `;
            $id('deleteUserDetails').innerHTML = details;
        }
    })
    .catch(e => console.error('Failed to fetch user details:', e));
    
    const btn = $id('deleteConfirmBtn');
    // Remove any previous onclick to avoid stacking handlers
    btn.replaceWith(btn.cloneNode(true));
    const freshBtn = $id('deleteConfirmBtn');
    freshBtn.textContent = 'Delete User';
    freshBtn.disabled = false;
    freshBtn.onclick = function() {
        const justification = $id('deleteJustification').value.trim();
        if (!justification) {
            showToast('Please provide a justification for deletion', 'error');
            $id('deleteJustification').focus();
            return;
        }
        
        const nb = freshBtn; nb.textContent='Deleting...'; nb.disabled=true;
        fetch('', { method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'}, body:`action=delete_user&user_id=${deleteUserId}&justification=${encodeURIComponent(justification)}` })
        .then(r=>r.json())
        .then(result => {
            if (result.success) {
                closeModal('deleteUserModal');
                showToast(result.message||'User deleted with justification logged!');
                setTimeout(()=>location.reload(), 900);
            } else { showToast(result.message||'Failed to delete','error'); nb.textContent='Delete User'; nb.disabled=false; }
        })
        .catch(e=>{ showToast('Error: '+e.message,'error'); nb.textContent='Delete User'; nb.disabled=false; });
    };
    
    // Clear justification when opening modal
    $id('deleteJustification').value = '';
    openModal('deleteUserModal');
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
const allModals = ['viewUserModal','editUserModal','deleteUserModal','signOutModal'];
window.addEventListener('click', e=>{ allModals.forEach(id=>{ const m=$id(id); if(m&&e.target===m) closeModal(id); }); });
document.addEventListener('keydown', e=>{ if(e.key==='Escape') allModals.forEach(id=>closeModal(id)); });
</script>
</body>
</html>
<?php closeAdminConnection($conn); ?>