<?php
// Bookings Management Module - Updated with Destinations Design
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/auth.php';

// Database connection functions
function getAdminConnection() { return getDatabaseConnection(); }
function initAdminAuth() { requireAdmin(); return getCurrentUser(); }
function closeAdminConnection($conn) { closeDatabaseConnection($conn); }

// Booking Management Functions
function addBooking($conn, $data) {
    try {
        $stmt = $conn->prepare("INSERT INTO bookings (user_id, tour_name, booking_date, number_of_people, total_amount, status, created_at) VALUES (?, ?, ?, ?, ?, 'pending', NOW())");
        $stmt->bind_param("issid", $data['user_id'], $data['tour_name'], $data['booking_date'], $data['number_of_people'], $data['total_amount']);
        return $stmt->execute() ? ['success' => true, 'message' => 'Booking added successfully'] : ['success' => false, 'message' => 'Failed to add booking'];
    } catch (Exception $e) { return ['success' => false, 'message' => 'Error: ' . $e->getMessage()]; }
}

function editBookingRecord($conn, $data) {
    try {
        $stmt = $conn->prepare("UPDATE bookings SET tour_name = ?, booking_date = ?, number_of_people = ?, total_amount = ?, status = ? WHERE id = ?");
        $stmt->bind_param("ssidsi", $data['tour_name'], $data['booking_date'], $data['number_of_people'], $data['total_amount'], $data['status'], $data['booking_id']);
        return $stmt->execute() ? ['success' => true, 'message' => 'Booking updated successfully'] : ['success' => false, 'message' => 'Failed to update booking'];
    } catch (Exception $e) { return ['success' => false, 'message' => 'Error: ' . $e->getMessage()]; }
}

function updateBookingStatus($conn, $data) {
    try {
        $currentBookingStmt = $conn->prepare("SELECT b.*, u.email as user_email FROM bookings b JOIN users u ON b.user_id = u.id WHERE b.id = ?");
        $currentBookingStmt->bind_param("i", $data['booking_id']);
        $currentBookingStmt->execute();
        $currentBookingResult = $currentBookingStmt->get_result();
        $currentBooking = $currentBookingResult->fetch_assoc();
        $currentBookingStmt->close();

        if (!$currentBooking) return ['success' => false, 'message' => 'Booking not found'];

        if ($data['status'] === 'cancelled' && isset($data['rejection_notes'])) {
            $stmt = $conn->prepare("UPDATE bookings SET status = ?, rejection_notes = ? WHERE id = ?");
            $stmt->bind_param("ssi", $data['status'], $data['rejection_notes'], $data['booking_id']);
        } else {
            $stmt = $conn->prepare("UPDATE bookings SET status = ? WHERE id = ?");
            $stmt->bind_param("si", $data['status'], $data['booking_id']);
        }

        if ($stmt->execute()) {
            $stmt->close();
            $emailSent = false; $emailMessage = '';
            if ($data['status'] === 'confirmed' && $currentBooking['status'] !== 'confirmed') {
                $emailResult = sendBookingStatusUpdateEmail($currentBooking['user_email'], $currentBooking, 'confirmed');
                $emailSent = $emailResult['success']; $emailMessage = $emailResult['message'];
            } elseif ($data['status'] === 'cancelled' && $currentBooking['status'] !== 'cancelled') {
                $rejectionReason = $data['rejection_notes'] ?? 'No specific reason provided';
                $emailResult = sendBookingRejectionEmail($currentBooking['user_email'], $currentBooking, $rejectionReason);
                $emailSent = $emailResult['success']; $emailMessage = $emailResult['message'];
            }
            $responseMessage = 'Booking status updated successfully';
            if ($emailSent) $responseMessage .= ' (Email notification sent)';
            elseif (!empty($emailMessage)) $responseMessage .= ' (Email notification failed: ' . $emailMessage . ')';
            return ['success' => true, 'message' => $responseMessage];
        } else {
            $stmt->close();
            return ['success' => false, 'message' => 'Failed to update booking status'];
        }
    } catch (Exception $e) { return ['success' => false, 'message' => 'Error: ' . $e->getMessage()]; }
}

function deleteBooking($conn, $bookingId, $justification = '') {
    try {
        // Log the deletion with justification
        $stmt = $conn->prepare("INSERT INTO deletion_log (item_type, item_id, justification, deleted_by, deleted_at) VALUES (?, ?, ?, ?, NOW())");
        $itemType = 'booking';
        $deletedBy = $_SESSION['user_id'] ?? 0;
        $stmt->bind_param("sisi", $itemType, $bookingId, $justification, $deletedBy);
        $stmt->execute();
        $stmt->close();
        
        // Proceed with deletion
        $stmt = $conn->prepare("DELETE FROM bookings WHERE id = ?");
        $stmt->bind_param("i", $bookingId);
        return $stmt->execute() ? ['success' => true, 'message' => 'Booking deleted successfully with justification logged'] : ['success' => false, 'message' => 'Failed to delete booking'];
    } catch (Exception $e) { return ['success' => false, 'message' => 'Error: ' . $e->getMessage()]; }
}

function getBooking($conn, $bookingId) {
    try {
        $stmt = $conn->prepare("SELECT b.*, CONCAT(u.first_name, ' ', u.last_name) as user_name, u.email as user_email FROM bookings b JOIN users u ON b.user_id = u.id WHERE b.id = ?");
        $stmt->bind_param("i", $bookingId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->num_rows > 0 ? ['success' => true, 'data' => $result->fetch_assoc()] : ['success' => false, 'message' => 'Booking not found'];
    } catch (Exception $e) { return ['success' => false, 'message' => 'Error: ' . $e->getMessage()]; }
}

function getBookingsList($conn, $page = 1, $limit = 15, $search = '', $status = '') {
    $offset = ($page - 1) * $limit;
    $bookingsQuery = "SELECT b.*, CONCAT(u.first_name, ' ', u.last_name) as user_name, u.email as user_email FROM bookings b JOIN users u ON b.user_id = u.id WHERE 1=1";
    $params = []; $types = '';
    if ($search) {
        $bookingsQuery .= " AND (b.tour_name LIKE ? OR u.first_name LIKE ? OR u.last_name LIKE ?)";
        $searchParam = "%{$search}%";
        $params[] = $searchParam; $params[] = $searchParam; $params[] = $searchParam; $types .= 'sss';
    }
    if ($status) {
        $bookingsQuery .= " AND b.status = ?";
        $params[] = $status; $types .= 's';
    }
    $bookingsQuery .= " ORDER BY b.created_at DESC LIMIT ? OFFSET ?";
    $params[] = $limit; $params[] = $offset; $types .= 'ii';
    $stmt = $conn->prepare($bookingsQuery);
    if (!empty($params)) $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $bookingsResult = $stmt->get_result();

    $countQuery = "SELECT COUNT(*) as total FROM bookings b JOIN users u ON b.user_id = u.id WHERE 1=1";
    $countParams = []; $countTypes = '';
    if ($search) {
        $countQuery .= " AND (b.tour_name LIKE ? OR u.first_name LIKE ? OR u.last_name LIKE ?)";
        $countParams[] = $searchParam; $countParams[] = $searchParam; $countParams[] = $searchParam; $countTypes .= 'sss';
    }
    if ($status) {
        $countQuery .= " AND b.status = ?";
        $countParams[] = $status; $countTypes .= 's';
    }
    $countStmt = $conn->prepare($countQuery);
    if (!empty($countParams)) $countStmt->bind_param($countTypes, ...$countParams);
    $countStmt->execute();
    $countResult = $countStmt->get_result();
    $totalCount = $countResult->fetch_assoc()['total'];
    $totalPages = ceil($totalCount / $limit);
    $bookings = [];
    while ($row = $bookingsResult->fetch_assoc()) $bookings[] = $row;
    $stmt->close(); $countStmt->close();
    return ['bookings' => $bookings, 'pagination' => ['current_page' => $page, 'total_pages' => $totalPages, 'total_count' => $totalCount, 'limit' => $limit]];
}

function getBookingStats($conn) {
    $stats = [];
    $result = $conn->query("SELECT COUNT(*) as total FROM bookings"); $stats['total'] = $result->fetch_assoc()['total'];
    $result = $conn->query("SELECT status, COUNT(*) as count FROM bookings GROUP BY status");
    $stats['by_status'] = [];
    while ($row = $result->fetch_assoc()) $stats['by_status'][$row['status']] = $row['count'];
    $result = $conn->query("SELECT COUNT(*) as total FROM bookings WHERE DATE(created_at) = CURDATE()"); $stats['today'] = $result->fetch_assoc()['total'];
    $result = $conn->query("SELECT COUNT(*) as total FROM bookings WHERE MONTH(created_at) = MONTH(CURDATE()) AND YEAR(created_at) = YEAR(CURDATE())"); $stats['this_month'] = $result->fetch_assoc()['total'];
    $result = $conn->query("SELECT SUM(total_amount) as total FROM bookings WHERE status = 'confirmed'"); $stats['total_revenue'] = $result ? ($result->fetch_assoc()['total'] ?? 0) : 0;
    return $stats;
}

function getAdminStats($conn) {
    $stats = [];
    $r = $conn->query("SELECT COUNT(*) as total FROM users WHERE user_type='user'"); $stats['totalUsers'] = $r->fetch_assoc()['total'];
    $r = $conn->query("SELECT COUNT(*) as total FROM users WHERE user_type='user' AND status='active'"); $stats['activeUsers'] = $r->fetch_assoc()['total'];
    $r = $conn->query("SELECT COUNT(*) as total FROM bookings"); $stats['totalBookings'] = $r->fetch_assoc()['total'];
    $r = $conn->query("SELECT COUNT(DISTINCT user_id) as total FROM login_activity WHERE DATE(login_time)=CURDATE() AND status='success'"); $stats['todayLogins'] = $r->fetch_assoc()['total'];
    $r = $conn->query("SELECT COUNT(*) as total FROM tour_guides"); $stats['totalGuides'] = $r->fetch_assoc()['total'];
    $r = $conn->query("SELECT COUNT(*) as total FROM tourist_spots"); $stats['totalDestinations'] = $r->fetch_assoc()['total'];
    return $stats;
}

// Initialize
$currentUser = initAdminAuth();
$conn = getAdminConnection();

$dbSettings = [];
$result = $conn->query("SELECT setting_key, setting_value FROM admin_dashboard_settings");
if ($result) while ($row = $result->fetch_assoc()) $dbSettings[$row['setting_key']] = $row['setting_value'];

$bSettings = [];
$result = $conn->query("SELECT setting_key, setting_value FROM booking_settings");
if ($result) while ($row = $result->fetch_assoc()) $bSettings[$row['setting_key']] = $row['setting_value'];

$logoText = $dbSettings['admin_logo_text'] ?? 'SJDM ADMIN';
$moduleTitle = $bSettings['module_title'] ?? 'Bookings Management';
$moduleSubtitle = $bSettings['module_subtitle'] ?? 'Manage tour bookings';
$adminMark = $dbSettings['admin_mark_label'] ?? 'A';

$adminInfo = ['role_title' => 'Administrator', 'admin_mark' => 'A'];
$stmt = $conn->prepare("SELECT admin_mark, role_title FROM admin_users WHERE user_id = ?");
$userId = $currentUser['id'];
$stmt->bind_param("i", $userId);
$stmt->execute();
if ($row = $stmt->get_result()->fetch_assoc()) $adminInfo = $row;
$stmt->close();

$menuItems = [];
$result = $conn->query("SELECT * FROM admin_menu_items WHERE is_active=1 ORDER BY display_order ASC");
if ($result) while ($row = $result->fetch_assoc()) $menuItems[] = $row;

// AJAX
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    $action = $_POST['action'] ?? '';
    switch ($action) {
        case 'add_booking': echo json_encode(addBooking($conn, $_POST)); exit;
        case 'edit_booking': echo json_encode(editBookingRecord($conn, $_POST)); exit;
        case 'update_booking_status': echo json_encode(updateBookingStatus($conn, $_POST)); exit;
        case 'delete_booking': echo json_encode(deleteBooking($conn, $_POST['booking_id'], $_POST['justification'] ?? '')); exit;
        case 'get_booking': echo json_encode(getBooking($conn, $_POST['booking_id'])); exit;
    }
}

// Pagination
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$limit = intval($bSettings['default_booking_limit'] ?? 15);
$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
$status = isset($_GET['status']) ? $conn->real_escape_string($_GET['status']) : '';

$bookingsData = getBookingsList($conn, $page, $limit, $search, $status);
$bookings = $bookingsData['bookings'];
$pagination = $bookingsData['pagination'];
$stats = getAdminStats($conn);
$bookingStats = getBookingStats($conn);

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
<title>Bookings Management | SJDM Tours Admin</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet">
<link rel="stylesheet" href="admin-styles.css">
<style>
/* ── Compact Stats (From Destinations) ── */
.um-stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(170px, 1fr)); gap: 14px; margin-bottom: 24px; }
.stat-card-compact { background: white; border-radius: 14px; padding: 16px 18px; box-shadow: 0 2px 12px rgba(0,0,0,.07); border: 1px solid rgba(0,0,0,.06); transition: transform .25s, box-shadow .25s; display: flex; flex-direction: column; }
.stat-card-compact:hover { transform: translateY(-3px); box-shadow: 0 8px 24px rgba(0,0,0,.12); }
.stat-card-compact[data-stat="total"]   { border-top: 3px solid #667eea; background: #fafbff; }
.stat-card-compact[data-stat="today"]   { border-top: 3px solid #10b981; background: #f5fdf9; }
.stat-card-compact[data-stat="revenue"] { border-top: 3px solid #f59e0b; background: #fffbeb; }
.stat-card-compact[data-stat="month"]   { border-top: 3px solid #ec4899; background: #fdf5fb; }
.scc-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 10px; }
.scc-label { display: flex; align-items: center; gap: 5px; font-size: .68rem; font-weight: 700; text-transform: uppercase; letter-spacing: .8px; color: #6b7280; }
.scc-label .material-icons-outlined { font-size: 14px; color: #9ca3af; }
.scc-dot { width: 9px; height: 9px; border-radius: 50%; flex-shrink: 0; }
.dot-blue  { background: #667eea; }
.dot-green { background: #10b981; }
.dot-orange{ background: #f59e0b; }
.dot-pink  { background: #ec4899; }
.scc-number { font-size: 2rem; font-weight: 800; color: #111827; line-height: 1; margin-bottom: 10px; }
.scc-trend { display: inline-flex; align-items: center; gap: 3px; font-size: .72rem; font-weight: 700; padding: 3px 8px; border-radius: 20px; width: fit-content; }
.scc-trend.positive { color: #059669; background: rgba(16,185,129,.12); }
.scc-trend.neutral  { color: #6b7280; background: rgba(107,114,128,.1); }
.scc-trend .material-icons-outlined { font-size: 13px; }

/* ── Search Bar (From Destinations) ── */
.search-bar { display: flex; gap: 10px; margin-bottom: 16px; align-items: center; }
.search-bar input, .search-bar select { padding: 10px 16px; border: 1px solid #e5e7eb; border-radius: 10px; font-size: .9rem; outline: none; font-family: inherit; transition: border-color .2s; background: white; }
.search-bar input { flex: 1; }
.search-bar input:focus, .search-bar select:focus { border-color: #667eea; box-shadow: 0 0 0 3px rgba(102,126,234,.1); }
.search-bar select { min-width: 150px; cursor: pointer; }

/* ── Action buttons (From Destinations) ── */
.action-buttons { display: flex; gap: 6px; }
.btn-icon { width: 32px; height: 32px; border: 1px solid #e5e7eb; border-radius: 8px; background: white; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: all .2s; color: #6b7280; }
.btn-icon:hover { background: #f3f4f6; color: #111827; border-color: #d1d5db; }
.btn-icon.edit:hover   { background: #eff6ff; color: #2563eb; border-color: #bfdbfe; }
.btn-icon.del:hover    { background: #fef2f2; color: #dc2626; border-color: #fecaca; }
.btn-icon.accept:hover { background: #f0fdf4; color: #15803d; border-color: #bbf7d0; }
.btn-icon.reject:hover { background: #fef2f2; color: #dc2626; border-color: #fecaca; }
.btn-icon .material-icons-outlined { font-size: 16px; }

/* ── Buttons ── */
.btn-primary { background: linear-gradient(135deg,#667eea,#764ba2); color: white; border: none; padding: 10px 18px; border-radius: 10px; font-size: .85rem; font-weight: 600; cursor: pointer; transition: all .2s; display: inline-flex; align-items: center; gap: 6px; font-family: inherit; }
.btn-primary:hover { opacity: .9; transform: translateY(-1px); box-shadow: 0 4px 12px rgba(102,126,234,.35); }
.btn-submit { background: linear-gradient(135deg,#667eea,#764ba2); color: white; border: none; padding: 10px 20px; border-radius: 10px; font-size: .875rem; font-weight: 600; cursor: pointer; transition: all .2s; font-family: inherit; }
.btn-submit:hover { opacity: .9; transform: translateY(-1px); }
.btn-cancel { background: #f3f4f6; color: #374151; border: 1px solid #e5e7eb; padding: 10px 20px; border-radius: 10px; font-size: .875rem; font-weight: 600; cursor: pointer; transition: all .2s; font-family: inherit; }
.btn-cancel:hover { background: #e5e7eb; }
.btn-danger { background: #ef4444; color: white; border: none; padding: 10px 20px; border-radius: 10px; font-size: .875rem; font-weight: 600; cursor: pointer; transition: all .2s; font-family: inherit; }
.btn-danger:hover { background: #dc2626; }

/* ── Pagination ── */
.pagination { display: flex; align-items: center; gap: 8px; margin-top: 24px; justify-content: center; }
.pagination-number { min-width: 38px; height: 38px; border: 1px solid #e5e7eb; border-radius: 8px; background: white; color: #374151; font-size: .85rem; font-weight: 600; cursor: pointer; transition: all .2s; display: flex; align-items: center; justify-content: center; font-family: inherit; }
.pagination-number:hover { background: #f3f4f6; border-color: #667eea; }
.pagination-number.active { background: #667eea; color: white; border-color: #667eea; }

/* ── Modals (From Destinations) ── */
.modal { display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background: rgba(0,0,0,.45); backdrop-filter: blur(4px); }
.modal.show { display: flex !important; align-items: center; justify-content: center; }
.modal-content { background: white; border-radius: 16px; width: 90%; max-width: 680px; max-height: 90vh; overflow-y: auto; box-shadow: 0 20px 60px rgba(0,0,0,.2); animation: slideIn .25s ease; }
.modal-content.small { max-width: 440px; }
.modal-content.wide  { max-width: 780px; }
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
.form-group input, .form-group select, .form-group textarea { width: 100%; padding: 10px 14px; border: 1px solid #e5e7eb; border-radius: 10px; font-size: .875rem; font-family: inherit; outline: none; transition: border-color .2s; box-sizing: border-box; }
.form-group input:focus, .form-group select:focus, .form-group textarea:focus { border-color: #667eea; box-shadow: 0 0 0 3px rgba(102,126,234,.1); }
.form-group textarea { resize: vertical; }

/* ── Status Badges ── */
.status-badge { display: inline-block; padding: 4px 10px; border-radius: 8px; font-size: .75rem; font-weight: 700; text-transform: capitalize; }
.status-badge.status-pending { background: #fffbeb; color: #b45309; border: 1px solid #fcd34d; }
.status-badge.status-confirmed { background: #f0fdf4; color: #15803d; border: 1px solid #bbf7d0; }
.status-badge.status-completed { background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe; }
.status-badge.status-cancelled { background: #fef2f2; color: #b91c1c; border: 1px solid #fecaca; }

/* ── Rejection Modal Specifics ── */
.reject-booking-info { display: flex; align-items: center; gap: 15px; padding: 16px; background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%); border-radius: 12px; margin-bottom: 20px; border: 1px solid #fecaca; }
.reject-icon { width: 48px; height: 48px; background: linear-gradient(135deg,#ef4444,#dc2626); border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0; box-shadow: 0 4px 12px rgba(239,68,68,.3); }
.reject-icon .material-icons-outlined { font-size: 24px; color: white; }
.reject-message h3 { margin: 0 0 4px; color: #991b1b; font-size: 1rem; font-weight: 700; }
.reject-message p { margin: 0; color: #7f1d1d; font-size: .85rem; }
.reason-chips { display: flex; flex-wrap: wrap; gap: 6px; margin-top: 8px; }
.reason-chip { background: #f3f4f6; border: 1px solid #e5e7eb; border-radius: 16px; padding: 6px 12px; font-size: .75rem; font-weight: 600; color: #4b5563; cursor: pointer; transition: all .2s; }
.reason-chip:hover { background: #ef4444; border-color: #dc2626; color: white; }

/* ── Toast feedback ── */
.toast-wrap { position: fixed; bottom: 24px; right: 24px; z-index: 9999; display: flex; flex-direction: column; gap: 10px; }
.toast { display: flex; align-items: center; gap: 10px; padding: 12px 16px; border-radius: 12px; font-size: .875rem; font-weight: 600; min-width: 260px; box-shadow: 0 4px 20px rgba(0,0,0,.12); transform: translateX(120%); opacity: 0; transition: all .3s ease; }
.toast.show { transform: translateX(0); opacity: 1; }
.toast.success { background: #f0fdf4; border: 1px solid #bbf7d0; color: #15803d; }
.toast.error   { background: #fef2f2; border: 1px solid #fecaca; color: #b91c1c; }
.toast .material-icons-outlined { font-size: 20px; }

/* ── Notes Column ── */
.notes-cell { display: flex; align-items: center; gap: 6px; padding: 6px 10px; background: #f9fafb; border-radius: 6px; border: 1px solid #e5e7eb; max-width: 180px; cursor: pointer; }
.notes-cell .material-icons-outlined { font-size: 14px; color: #6b7280; }
.notes-text { font-size: .75rem; color: #374151; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }

@media (max-width: 640px) {
    .um-stats-grid { grid-template-columns: repeat(2,1fr); }
    .form-row { grid-template-columns: 1fr; }
    .search-bar { flex-direction: column; align-items: stretch; }
    .search-bar select { width: 100%; }
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
<!-- Compact Stats -->
<div class="um-stats-grid">
<div class="stat-card-compact" data-stat="total">
<div class="scc-header">
<div class="scc-label"><span class="material-icons-outlined">book</span> Total Bookings</div>
<span class="scc-dot dot-blue"></span>
</div>
<div class="scc-number"><?php echo $bookingStats['total']; ?></div>
<div class="scc-trend neutral"><span class="material-icons-outlined">apps</span><span>All time</span></div>
</div>
<div class="stat-card-compact" data-stat="today">
<div class="scc-header">
<div class="scc-label"><span class="material-icons-outlined">today</span> Today</div>
<span class="scc-dot dot-green"></span>
</div>
<div class="scc-number"><?php echo $bookingStats['today']; ?></div>
<div class="scc-trend positive"><span class="material-icons-outlined">north_east</span><span>New</span></div>
</div>
<div class="stat-card-compact" data-stat="revenue">
<div class="scc-header">
<div class="scc-label"><span class="material-icons-outlined">payments</span> Revenue</div>
<span class="scc-dot dot-orange"></span>
</div>
<div class="scc-number">₱<?php echo number_format($bookingStats['total_revenue'], 0); ?></div>
<div class="scc-trend positive"><span class="material-icons-outlined">attach_money</span><span>Confirmed</span></div>
</div>
<div class="stat-card-compact" data-stat="month">
<div class="scc-header">
<div class="scc-label"><span class="material-icons-outlined">calendar_month</span> This Month</div>
<span class="scc-dot dot-pink"></span>
</div>
<div class="scc-number"><?php echo $bookingStats['this_month']; ?></div>
<div class="scc-trend neutral"><span class="material-icons-outlined">calendar_today</span><span>Current</span></div>
</div>
</div>

<!-- Search & Filter -->
<div class="search-bar">
<input type="text" id="searchInput" placeholder="Search bookings by tour name or user..." value="<?php echo htmlspecialchars($search); ?>">
<select id="statusFilter" onchange="filterBookings()">
<option value="">All Status</option>
<option value="pending" <?php echo $status == 'pending' ? 'selected' : ''; ?>>Pending</option>
<option value="confirmed" <?php echo $status == 'confirmed' ? 'selected' : ''; ?>>Confirmed</option>
<option value="completed" <?php echo $status == 'completed' ? 'selected' : ''; ?>>Completed</option>
<option value="cancelled" <?php echo $status == 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
</select>
<button class="btn-cancel" onclick="searchBookings()">
<span class="material-icons-outlined" style="font-size:16px;vertical-align:middle;">search</span> Search
</button>
<button class="btn-cancel" onclick="clearFilters()">
<span class="material-icons-outlined" style="font-size:16px;vertical-align:middle;">clear</span> Clear
</button>
</div>

<!-- Bookings Table -->
<div class="table-container">
<table class="data-table">
<thead>
<tr>
<th>Booking ID</th>
<th>User</th>
<th>Tour Name</th>
<th>Date</th>
<th>People</th>
<th>Amount</th>
<th>Status</th>
<th>Notes</th>
<th>Actions</th>
</tr>
</thead>
<tbody>
<?php foreach ($bookings as $booking): ?>
<tr>
<td>#<?php echo str_pad($booking['id'], 6, '0', STR_PAD_LEFT); ?></td>
<td>
<div class="user-info">
<div class="user-avatar">
<?php echo strtoupper(substr($booking['user_name'], 0, 1)); ?>
</div>
<div>
<strong><?php echo htmlspecialchars($booking['user_name']); ?></strong><br>
<small><?php echo htmlspecialchars($booking['user_email']); ?></small>
</div>
</div>
</td>
<td><?php echo htmlspecialchars($booking['tour_name']); ?></td>
<td><?php echo date('M j, Y', strtotime($booking['booking_date'])); ?></td>
<td><?php echo $booking['number_of_people']; ?></td>
<td class="booking-amount">₱<?php echo number_format($booking['total_amount'], 2); ?></td>
<td>
<span class="status-badge status-<?php echo $booking['status']; ?>">
<?php echo ucfirst($booking['status']); ?>
</span>
</td>
<td>
<?php if (!empty($booking['rejection_notes'])): ?>
<div class="notes-cell" title="<?php echo htmlspecialchars($booking['rejection_notes']); ?>">
<span class="material-icons-outlined">note</span>
<span class="notes-text"><?php echo htmlspecialchars(substr($booking['rejection_notes'], 0, 30)); ?><?php echo strlen($booking['rejection_notes']) > 30 ? '...' : ''; ?></span>
</div>
<?php else: ?>
<span style="color:#9ca3af;font-style:italic;font-size:.85rem;">—</span>
<?php endif; ?>
</td>
<td>
<div class="action-buttons">
<?php if ($booking['status'] === 'pending'): ?>
<button class="btn-icon accept" onclick="acceptBooking(<?php echo $booking['id']; ?>)" title="Accept">
<span class="material-icons-outlined">check</span>
</button>
<button class="btn-icon reject" onclick="rejectBooking(<?php echo $booking['id']; ?>)" title="Reject">
<span class="material-icons-outlined">close</span>
</button>
<?php endif; ?>
<button class="btn-icon" onclick="updateStatus(<?php echo $booking['id']; ?>)" title="Update Status">
<span class="material-icons-outlined">sync</span>
</button>
<button class="btn-icon del" onclick="deleteBooking(<?php echo $booking['id']; ?>)" title="Delete">
<span class="material-icons-outlined">delete</span>
</button>
</div>
</td>
</tr>
<?php endforeach; ?>
<?php if (empty($bookings)): ?>
<tr><td colspan="9" style="text-align:center;padding:48px;color:#9ca3af;">
<span class="material-icons-outlined" style="font-size:44px;display:block;margin-bottom:10px;color:#d1d5db;">book</span>
No bookings found
</td></tr>
<?php endif; ?>
</tbody>
</table>
</div>

<!-- Pagination -->
<?php if ($pagination['total_pages'] > 1): ?>
<div class="pagination">
<?php if ($pagination['current_page'] > 1): ?>
<button class="pagination-number" onclick="goToPage(<?php echo $pagination['current_page'] - 1; ?>)">
<span class="material-icons-outlined" style="font-size:16px;">chevron_left</span>
</button>
<?php endif; ?>
<?php for ($i = 1; $i <= $pagination['total_pages']; $i++): ?>
<button class="pagination-number <?php echo $i == $pagination['current_page'] ? 'active' : ''; ?>" onclick="goToPage(<?php echo $i; ?>)">
<?php echo $i; ?>
</button>
<?php endfor; ?>
<?php if ($pagination['current_page'] < $pagination['total_pages']): ?>
<button class="pagination-number" onclick="goToPage(<?php echo $pagination['current_page'] + 1; ?>)">
<span class="material-icons-outlined" style="font-size:16px;">chevron_right</span>
</button>
<?php endif; ?>
</div>
<?php endif; ?>
</div>
</main>
</div>

<!-- Toast container -->
<div class="toast-wrap" id="toastWrap"></div>

<!-- DELETE BOOKING MODAL -->
<div id="deleteBookingModal" class="modal">
<div class="modal-content small">
<div class="modal-header">
<h2>Delete Booking</h2>
<button class="modal-close" onclick="closeModal('deleteBookingModal')"><span class="material-icons-outlined">close</span></button>
</div>
<form id="deleteBookingForm" onsubmit="handleDeleteBooking(event)">
<input type="hidden" id="deleteBookingId" name="booking_id">
<div class="modal-body">
<div style="text-align:center;padding:10px 0 20px;">
<div style="width:64px;height:64px;border-radius:50%;background:linear-gradient(135deg,#ef4444,#dc2626);color:white;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;box-shadow:0 8px 24px rgba(239,68,68,.3);">
<span class="material-icons-outlined" style="font-size:32px;">warning</span>
</div>
<p style="font-size:1rem;color:#374151;margin:0;font-weight:500;">Are you sure you want to delete <strong id="deleteBookingLabel"></strong>? <br><small style="color:#9ca3af;">This cannot be undone.</small></p>
</div>
<div style="margin-top:20px;padding:16px;background:#f9fafb;border-radius:10px;border:1px solid #e5e7eb;">
<h4 style="margin:0 0 12px;font-size:.9rem;font-weight:600;color:#374151;display:flex;align-items:center;gap:6px;">
<span class="material-icons-outlined" style="font-size:18px;color:#667eea;">event_note</span>
Booking to be Deleted
</h4>
<div id="deleteBookingDetails" style="font-size:.85rem;color:#6b7280;"></div>
</div>
<div style="margin-top:16px;">
<label for="deleteBookingJustification" style="display:block;margin-bottom:6px;font-size:.82rem;font-weight:600;color:#374151;">
<span class="material-icons-outlined" style="font-size:16px;vertical-align:middle;margin-right:4px;color:#ef4444;">assignment_late</span>
Justification for Deletion <span style="color:#ef4444;">*</span>
</label>
<textarea id="deleteBookingJustification" name="justification" rows="3" placeholder="Please provide a specific reason why this booking must be deleted..." required
style="width:100%;padding:10px 14px;border:1px solid #e5e7eb;border-radius:10px;font-size:.875rem;font-family:inherit;outline:none;transition:border-color .2s;box-sizing:border-box;resize:vertical;"></textarea>
<div style="margin-top:6px;font-size:.75rem;color:#6b7280;">
Examples: Duplicate booking, user cancellation request, fraudulent activity, system error, test booking, etc.
</div>
</div>
</div>
<div class="modal-footer">
<button type="button" class="btn-cancel" onclick="closeModal('deleteBookingModal')">Cancel</button>
<button type="submit" class="btn-danger">Delete</button>
</div>
</form>
</div>
</div>

<!-- UPDATE STATUS MODAL -->
<div id="updateStatusModal" class="modal">
<div class="modal-content">
<div class="modal-header">
<h2>Update Booking Status</h2>
<button class="modal-close" onclick="closeModal('updateStatusModal')"><span class="material-icons-outlined">close</span></button>
</div>
<form id="updateStatusForm" onsubmit="handleUpdateStatus(event)">
<input type="hidden" id="updateStatusBookingId" name="booking_id">
<div class="modal-body">
<div class="form-group">
<label for="updateStatusSelect">Status *</label>
<select id="updateStatusSelect" name="status" required>
<option value="pending">Pending</option>
<option value="confirmed">Confirmed</option>
<option value="completed">Completed</option>
<option value="cancelled">Cancelled</option>
</select>
</div>
</div>
<div class="modal-footer">
<button type="button" class="btn-cancel" onclick="closeModal('updateStatusModal')">Cancel</button>
<button type="submit" class="btn-submit">Update Status</button>
</div>
</form>
</div>
</div>

<!-- REJECT BOOKING MODAL -->
<div id="rejectBookingModal" class="modal">
<div class="modal-content">
<div class="modal-header">
<h2>Reject Booking</h2>
<button class="modal-close" onclick="closeModal('rejectBookingModal')"><span class="material-icons-outlined">close</span></button>
</div>
<form id="rejectBookingForm" onsubmit="handleRejectBooking(event)">
<input type="hidden" id="rejectBookingId" name="booking_id">
<div class="modal-body">
<div class="reject-booking-info">
<div class="reject-icon"><span class="material-icons-outlined">warning</span></div>
<div class="reject-message">
<h3>Are you sure you want to reject this booking?</h3>
<p>Please provide a reason. This will be sent to the customer.</p>
</div>
</div>
<div class="form-group">
<label for="rejectionNotes">Rejection Reason *</label>
<textarea id="rejectionNotes" name="rejection_notes" rows="4" placeholder="Please explain why this booking is being rejected..." required></textarea>
<small style="display:block;margin-top:6px;font-size:.8rem;color:#6b7280;font-style:italic;">Be specific and professional.</small>
</div>
<div class="form-group">
<label style="font-size:.8rem;font-weight:600;color:#374151;">Common reasons:</label>
<div class="reason-chips">
<button type="button" class="reason-chip" onclick="addReason('Fully booked')">Fully booked</button>
<button type="button" class="reason-chip" onclick="addReason('Tour not available')">Tour not available</button>
<button type="button" class="reason-chip" onclick="addReason('Payment issue')">Payment issue</button>
<button type="button" class="reason-chip" onclick="addReason('Schedule conflict')">Schedule conflict</button>
</div>
</div>
</div>
<div class="modal-footer">
<button type="button" class="btn-cancel" onclick="closeModal('rejectBookingModal')">Cancel</button>
<button type="submit" class="btn-danger">Reject Booking</button>
</div>
</form>
</div>
</div>

<!-- EDIT BOOKING MODAL -->
<div id="editBookingModal" class="modal">
<div class="modal-content">
<div class="modal-header">
<h2>Edit Booking</h2>
<button class="modal-close" onclick="closeModal('editBookingModal')"><span class="material-icons-outlined">close</span></button>
</div>
<form id="editBookingForm" onsubmit="handleEditBooking(event)">
<input type="hidden" id="editBookingId" name="booking_id">
<div class="modal-body">
<div class="form-group">
<label for="editBookingTour">Tour Name *</label>
<input type="text" id="editBookingTour" name="tour_name" required>
</div>
<div class="form-row">
<div class="form-group">
<label for="editBookingDate">Booking Date *</label>
<input type="date" id="editBookingDate" name="booking_date" required>
</div>
<div class="form-group">
<label for="editBookingPeople">Number of People *</label>
<input type="number" id="editBookingPeople" name="number_of_people" min="1" required>
</div>
</div>
<div class="form-row">
<div class="form-group">
<label for="editBookingAmount">Total Amount *</label>
<input type="number" id="editBookingAmount" name="total_amount" min="0" step="0.01" required>
</div>
<div class="form-group">
<label for="editBookingStatus">Status *</label>
<select id="editBookingStatus" name="status" required>
<option value="pending">Pending</option>
<option value="confirmed">Confirmed</option>
<option value="completed">Completed</option>
<option value="cancelled">Cancelled</option>
</select>
</div>
</div>
</div>
<div class="modal-footer">
<button type="button" class="btn-cancel" onclick="closeModal('editBookingModal')">Cancel</button>
<button type="submit" class="btn-submit">Update Booking</button>
</div>
</form>
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
<div style="display:flex;flex-direction:column;align-items:center;gap:20px;padding:16px 0;text-align:center;">
<div style="width:72px;height:72px;border-radius:50%;background:linear-gradient(135deg,#ef4444,#dc2626);color:white;display:flex;align-items:center;justify-content:center;box-shadow:0 8px 24px rgba(239,68,68,.3);">
<span class="material-icons-outlined" style="font-size:36px;">logout</span>
</div>
<div>
<h3 style="margin:0 0 6px;font-size:1.1rem;font-weight:700;color:#111827;">Are you sure you want to sign out?</h3>
<p style="margin:0;font-size:.875rem;color:#6b7280;">You will be redirected to the login page.</p>
</div>
</div>
</div>
<div class="modal-footer">
<button type="button" class="btn-cancel" onclick="closeModal('signOutModal')">Cancel</button>
<button type="button" class="btn-submit" onclick="window.location.href='logout.php'">Sign Out</button>
</div>
</div>
</div>

<script src="admin-script.js"></script>
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

/* ── Search & Filter ── */
function searchBookings() {
    const searchValue = $id('searchInput').value;
    const statusValue = $id('statusFilter').value;
    const params = new URLSearchParams();
    if (searchValue) params.append('search', searchValue);
    if (statusValue) params.append('status', statusValue);
    window.location.href = `?${params.toString()}`;
}
function filterBookings() { searchBookings(); }
function clearFilters() {
    $id('searchInput').value = '';
    $id('statusFilter').value = '';
    window.location.href = '?';
}
function goToPage(page) {
    const searchValue = $id('searchInput').value;
    const statusValue = $id('statusFilter').value;
    const params = new URLSearchParams();
    params.append('page', page);
    if (searchValue) params.append('search', searchValue);
    if (statusValue) params.append('status', statusValue);
    window.location.href = `?${params.toString()}`;
}
$id('searchInput').addEventListener('keypress', e=>{ if(e.key==='Enter') searchBookings(); });

/* ── Edit Booking ── */
function editBooking(bookingId) {
    fetch('', { method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'}, body:`action=get_booking&booking_id=${bookingId}` })
    .then(r=>r.json())
    .then(result => {
        if (!result.success) { showToast(result.message||'Failed to load','error'); return; }
        const b = result.data;
        $id('editBookingId').value = b.id||'';
        $id('editBookingTour').value = b.tour_name||'';
        $id('editBookingPeople').value = b.number_of_people||1;
        $id('editBookingAmount').value = b.total_amount||0;
        $id('editBookingStatus').value = b.status||'pending';
        if (b.booking_date) {
            const d = new Date(b.booking_date);
            if (!isNaN(d.getTime())) {
                const y = d.getFullYear();
                const m = String(d.getMonth()+1).padStart(2,'0');
                const day = String(d.getDate()).padStart(2,'0');
                $id('editBookingDate').value = `${y}-${m}-${day}`;
            }
        }
        openModal('editBookingModal');
    })
    .catch(e=>showToast('Error: '+e.message,'error'));
}
function handleEditBooking(event) {
    event.preventDefault();
    const btn = event.target.querySelector('button[type="submit"]');
    const originalText = btn.textContent;
    btn.textContent = 'Updating…'; btn.disabled = true;
    const formData = new FormData(event.target);
    const data = Object.fromEntries(formData.entries());
    data.action = 'edit_booking';
    fetch('', { method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'}, body:new URLSearchParams(data) })
    .then(r=>r.json())
    .then(result => {
        if (result.success) { closeModal('editBookingModal'); showToast(result.message); setTimeout(()=>location.reload(), 900); }
        else { showToast(result.message||'Failed to update','error'); btn.textContent=originalText; btn.disabled=false; }
    })
    .catch(e=>{ showToast('Error: '+e.message,'error'); btn.textContent=originalText; btn.disabled=false; });
}

/* ── Update Status ── */
function updateStatus(bookingId) {
    $id('updateStatusBookingId').value = bookingId;
    $id('updateStatusSelect').value = 'pending';
    fetch('', { method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'}, body:`action=get_booking&booking_id=${bookingId}` })
    .then(r=>r.json())
    .then(result => { if(result&&result.success&&result.data&&result.data.status) $id('updateStatusSelect').value = result.data.status; })
    .catch(()=>{})
    .finally(()=>openModal('updateStatusModal'));
}
function handleUpdateStatus(event) {
    event.preventDefault();
    const btn = event.target.querySelector('button[type="submit"]');
    const originalText = btn.textContent;
    btn.textContent = 'Updating…'; btn.disabled = true;
    const formData = new FormData(event.target);
    const bookingId = formData.get('booking_id');
    const status = formData.get('status');
    fetch('', { method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'}, body:`action=update_booking_status&booking_id=${encodeURIComponent(bookingId)}&status=${encodeURIComponent(status)}` })
    .then(r=>r.json())
    .then(result => {
        if (result.success) { closeModal('updateStatusModal'); showToast(result.message); setTimeout(()=>location.reload(), 900); }
        else { showToast(result.message||'Failed to update','error'); btn.textContent=originalText; btn.disabled=false; }
    })
    .catch(e=>{ showToast('Error: '+e.message,'error'); btn.textContent=originalText; btn.disabled=false; });
}

/* ── Accept/Reject ── */
function acceptBooking(bookingId) {
    fetch('', { method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'}, body:`action=update_booking_status&booking_id=${bookingId}&status=confirmed` })
    .then(r=>r.json())
    .then(data => {
        if (data.success) {
            showToast(data.message.includes('Email') ? 'Booking approved & email sent!' : 'Booking approved!');
            setTimeout(()=>location.reload(), 900);
        } else { showToast(data.message,'error'); }
    });
}
function rejectBooking(bookingId) {
    $id('rejectBookingId').value = bookingId;
    openModal('rejectBookingModal');
}
function addReason(reason) {
    const textarea = $id('rejectionNotes');
    textarea.value = textarea.value.trim() ? textarea.value + ' ' + reason : reason;
    textarea.focus();
}
function handleRejectBooking(event) {
    event.preventDefault();
    const notes = $id('rejectionNotes').value.trim();
    if (!notes) { showToast('Please provide a reason','error'); return; }
    if (notes.length < 10) { showToast('Reason must be at least 10 chars','error'); return; }
    const btn = event.target.querySelector('button[type="submit"]');
    const originalText = btn.textContent;
    btn.textContent = 'Rejecting…'; btn.disabled = true;
    const formData = new FormData(event.target);
    const data = Object.fromEntries(formData.entries());
    data.action = 'update_booking_status';
    data.status = 'cancelled';
    fetch('', { method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'}, body:new URLSearchParams(data) })
    .then(r=>r.json())
    .then(result => {
        if (result.success) { closeModal('rejectBookingModal'); showToast(result.message.includes('Email')?'Booking rejected & email sent!':'Booking rejected!'); setTimeout(()=>location.reload(), 900); }
        else { showToast(result.message||'Failed to reject','error'); btn.textContent=originalText; btn.disabled=false; }
    })
    .catch(e=>{ showToast('Error: '+e.message,'error'); btn.textContent=originalText; btn.disabled=false; });
}

/* ── Delete ── */
function deleteBooking(bookingId) {
    // Fetch booking details for identification
    fetch('', { method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'}, body:`action=get_booking&booking_id=${bookingId}` })
    .then(r=>r.json())
    .then(result => {
        if (result && result.success && result.data) {
            const booking = result.data;
            $id('deleteBookingLabel').textContent = `Booking #${String(bookingId).padStart(6,'0')} (${booking.tour_name})`;
            
            // Display booking details for identification
            const details = `
                <div style="display:grid;gap:8px;">
                    <div><strong>Booking ID:</strong> #${String(bookingId).padStart(6,'0')}</div>
                    <div><strong>Tour:</strong> ${booking.tour_name || 'N/A'}</div>
                    <div><strong>User:</strong> ${booking.user_name || 'N/A'}</div>
                    <div><strong>Email:</strong> ${booking.user_email || 'N/A'}</div>
                    <div><strong>Date:</strong> ${booking.booking_date ? date('M j, Y', strtotime(booking.booking_date)) : 'N/A'}</div>
                    <div><strong>People:</strong> ${booking.number_of_people || 'N/A'}</div>
                    <div><strong>Amount:</strong> ₱${number_format(booking.total_amount || 0, 2)}</div>
                    <div><strong>Status:</strong> ${ucfirst(booking.status || 'N/A')}</div>
                </div>
            `;
            $id('deleteBookingDetails').innerHTML = details;
        }
    })
    .catch(error => console.error('Failed to fetch booking details:', error))
    .finally(() => {
        document.getElementById('deleteBookingId').value = bookingId;
        openModal('deleteBookingModal');
        // Clear justification when opening modal
        document.getElementById('deleteBookingJustification').value = '';
    });
}

function handleDeleteBooking(event) {
    event.preventDefault();
    
    // Validate justification
    const justification = document.getElementById('deleteBookingJustification').value.trim();
    if (!justification) {
        showToast('Please provide a justification for deletion', 'error');
        document.getElementById('deleteBookingJustification').focus();
        return;
    }
    
    const btn = event.target.querySelector('button[type="submit"]');
    const originalText = btn.textContent;
    btn.textContent = 'Deleting…'; btn.disabled = true;
    
    const formData = new FormData(event.target);
    const bookingId = formData.get('booking_id');
    
    // Include justification in the request
    const data = new URLSearchParams(formData);
    data.append('justification', justification);
    
    fetch('', { 
        method:'POST', 
        headers:{'Content-Type':'application/x-www-form-urlencoded'}, 
        body: data.toString()
    })
    .then(r=>r.json())
    .then(result => {
        if (result.success) { 
            closeModal('deleteBookingModal'); 
            showToast(result.message); 
            setTimeout(()=>location.reload(), 900); 
        } else { 
            showToast(result.message||'Failed to delete','error'); 
            btn.textContent=originalText; 
            btn.disabled=false; 
        }
    })
    .catch(e=>{ 
        showToast('Error: '+e.message,'error'); 
        btn.textContent=originalText; 
        btn.disabled=false; 
    });
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
const allModals = ['deleteBookingModal','updateStatusModal','rejectBookingModal','editBookingModal','signOutModal'];
window.addEventListener('click', e=>{ allModals.forEach(id=>{ const m=$id(id); if(m&&e.target===m) closeModal(id); }); });
document.addEventListener('keydown', e=>{ if(e.key==='Escape') allModals.forEach(id=>closeModal(id)); });
</script>
<?php closeAdminConnection($conn); ?>
</body>
</html>