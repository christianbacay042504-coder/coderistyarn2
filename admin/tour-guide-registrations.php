<?php
// Tour Guide Registrations Management Module
// This file handles tour guide registration applications with separated connections and functions

require_once '../config/database.php';
require_once '../config/auth.php';

function getAdminConnection() { return getDatabaseConnection(); }
function initAdminAuth() { requireAdmin(); return getCurrentUser(); }
function closeAdminConnection($conn) { closeDatabaseConnection($conn); }

function addRegistration($conn, $data) {
    try {
        $stmt = $conn->prepare("INSERT INTO registration_tour_guide (last_name, first_name, gender, email, phone, specialization, experience_years, status, resume_url, cover_letter) VALUES (?, ?, ?, ?, ?, ?, ?, 'pending', ?, ?)");
        $stmt->bind_param("sssissss", $data['last_name'], $data['first_name'], $data['gender'], $data['email'], $data['phone'], $data['specialization'], $data['experience_years'], $data['resume_url'], $data['cover_letter']);
        return $stmt->execute() ? ['success' => true, 'message' => 'Registration added successfully'] : ['success' => false, 'message' => 'Failed to add registration'];
    } catch (Exception $e) { return ['success' => false, 'message' => 'Error: ' . $e->getMessage()]; }
}

function updateRegistrationStatus($conn, $registrationId, $status) {
    try {
        $stmt = $conn->prepare("UPDATE registration_tour_guide SET status = ?, review_date = CURRENT_TIMESTAMP, reviewed_by = ? WHERE id = ?");
        $stmt->bind_param("sii", $status, $_SESSION['id'], $registrationId);
        if ($stmt->execute()) {
            $action = $status === 'approved' ? 'approved' : ($status === 'rejected' ? 'rejected' : ($status === 'under_review' ? 'marked as under review' : 'updated'));
            return ['success' => true, 'message' => "Registration $action successfully"];
        }
        return ['success' => false, 'message' => 'Failed to update registration status'];
    } catch (Exception $e) { return ['success' => false, 'message' => 'Error: ' . $e->getMessage()]; }
}

function deleteRegistration($conn, $registrationId, $justification = '') {
    try {
        // Log the deletion with justification
        $stmt = $conn->prepare("INSERT INTO deletion_log (item_type, item_id, justification, deleted_by, deleted_at) VALUES (?, ?, ?, ?, NOW())");
        $itemType = 'tour_guide_registration';
        $deletedBy = $_SESSION['user_id'] ?? 0;
        $stmt->bind_param("sisi", $itemType, $registrationId, $justification, $deletedBy);
        $stmt->execute();
        $stmt->close();
        
        // Proceed with deletion
        $stmt = $conn->prepare("DELETE FROM registration_tour_guide WHERE id = ?");
        $stmt->bind_param("i", $registrationId);
        return $stmt->execute() ? ['success' => true, 'message' => 'Registration deleted successfully with justification logged'] : ['success' => false, 'message' => 'Failed to delete registration'];
    } catch (Exception $e) { return ['success' => false, 'message' => 'Error: ' . $e->getMessage()]; }
}

function getRegistration($conn, $registrationId) {
    try {
        $stmt = $conn->prepare("SELECT * FROM registration_tour_guide WHERE id = ?");
        $stmt->bind_param("i", $registrationId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->num_rows > 0 ? ['success' => true, 'data' => $result->fetch_assoc()] : ['success' => false, 'message' => 'Registration not found'];
    } catch (Exception $e) { return ['success' => false, 'message' => 'Error: ' . $e->getMessage()]; }
}

function getRegistrationsList($conn, $page = 1, $limit = 15, $search = '') {
    $offset = ($page - 1) * $limit;
    $search = $conn->real_escape_string($search);
    $q = "SELECT r.*, u.id as user_id, tg.name as guide_name, tg.specialty as guide_specialty, tg.experience_years as guide_experience, tg.contact_number as guide_contact, tg.bio as guide_bio FROM registration_tour_guide r LEFT JOIN users u ON r.email COLLATE utf8mb4_unicode_ci = u.email COLLATE utf8mb4_unicode_ci LEFT JOIN tour_guides tg ON u.id = tg.user_id WHERE 1=1";
    if ($search) $q .= " AND (last_name LIKE '%$search%' OR first_name LIKE '%$search%' OR email LIKE '%$search%' OR specialization LIKE '%$search%')";
    $q .= " ORDER BY id DESC LIMIT $limit OFFSET $offset";
    $result = $conn->query($q);
    $cq = "SELECT COUNT(*) as total FROM registration_tour_guide WHERE 1=1";
    if ($search) $cq .= " AND (last_name LIKE '%$search%' OR first_name LIKE '%$search%' OR email LIKE '%$search%' OR specialization LIKE '%$search%')";
    $cr = $conn->query($cq);
    $total = $cr ? $cr->fetch_assoc()['total'] : 0;
    $rows = [];
    if ($result) while ($row = $result->fetch_assoc()) $rows[] = $row;
    return ['registrations' => $rows, 'pagination' => ['current_page' => $page, 'total_pages' => ceil($total / $limit), 'total_count' => $total, 'limit' => $limit]];
}

function editRegistrationData($conn, $data) {
    try {
        $id = $data['id'] ?? 0;
        $stmt = $conn->prepare("UPDATE registration_tour_guide SET last_name=?, first_name=?, middle_initial=?, gender=?, email=?, primary_phone=?, specialization=?, years_of_experience=?, status=?, admin_notes=?, processed_date=CURRENT_TIMESTAMP WHERE id=?");
        $stmt->bind_param("ssssssisssi", $data['last_name'], $data['first_name'], $data['middle_initial'], $data['gender'], $data['email'], $data['primary_phone'], $data['specialization'], $data['years_of_experience'], $data['status'], $data['admin_notes'], $id);
        return $stmt->execute() ? ['success' => true, 'message' => 'Registration updated successfully'] : ['success' => false, 'message' => 'Failed to update registration'];
    } catch (Exception $e) { return ['success' => false, 'message' => 'Error: ' . $e->getMessage()]; }
}

function approveTourGuideRegistration($conn, $registrationId) {
    try {
        $conn->begin_transaction();
        $stmt = $conn->prepare("SELECT * FROM registration_tour_guide WHERE id = ?");
        $stmt->bind_param("i", $registrationId);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows === 0) { $conn->rollback(); return ['success' => false, 'message' => 'Registration not found']; }
        $registration = $result->fetch_assoc();
        $check = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $check->bind_param("s", $registration['email']);
        $check->execute();
        if ($check->get_result()->num_rows > 0) { $conn->rollback(); return ['success' => false, 'message' => 'User with this email already exists']; }
        $randomPassword = generateRandomPassword();
        $hashedPassword = password_hash($randomPassword, PASSWORD_DEFAULT);
        $ins = $conn->prepare("INSERT INTO users (first_name, last_name, email, password, user_type, status) VALUES (?, ?, ?, ?, 'tour_guide', 'active')");
        $ins->bind_param("ssss", $registration['first_name'], $registration['last_name'], $registration['email'], $hashedPassword);
        if (!$ins->execute()) { $conn->rollback(); return ['success' => false, 'message' => 'Failed to create user account']; }
        $userId = $conn->insert_id;
        $tg = $conn->prepare("INSERT INTO tour_guides (user_id, name, category, experience_years, contact_number, email) VALUES (?, ?, ?, ?, ?, ?)");
        $name = $registration['first_name'] . ' ' . $registration['last_name'];
        $exp = $registration['years_experience'] ?? 0;
        $tg->bind_param("ississ", $userId, $name, $registration['specialization'], $exp, $registration['primary_phone'], $registration['email']);
        if (!$tg->execute()) { $conn->rollback(); return ['success' => false, 'message' => 'Failed to create tour guide profile']; }
        $upd = $conn->prepare("UPDATE registration_tour_guide SET status='approved', review_date=CURRENT_TIMESTAMP, reviewed_by=? WHERE id=?");
        $upd->bind_param("ii", $_SESSION['id'], $registrationId);
        if (!$upd->execute()) { $conn->rollback(); return ['success' => false, 'message' => 'Failed to update registration status']; }
        $conn->commit();
        $emailSent = sendApprovalEmail($registration['email'], $registration['first_name'], $randomPassword);
        $msg = 'Registration approved! User account created.' . ($emailSent ? ' Approval email sent.' : ' Warning: Email could not be sent.');
        return ['success' => true, 'message' => $msg, 'password' => $randomPassword, 'email' => $registration['email'], 'name' => $name];
    } catch (Exception $e) { $conn->rollback(); return ['success' => false, 'message' => 'Error: ' . $e->getMessage()]; }
}

function generateRandomPassword($length = 12) {
    $chars = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $prefix = 'tourguide';
    $suffix = '';
    for ($i = 0; $i < $length - strlen($prefix); $i++) $suffix .= $chars[rand(0, strlen($chars) - 1)];
    return $prefix . $suffix;
}

function sendApprovalEmail($email, $firstName, $password) {
    try {
        require_once '../PHPMailer-6.9.1/src/PHPMailer.php';
        require_once '../PHPMailer-6.9.1/src/SMTP.php';
        require_once '../PHPMailer-6.9.1/src/Exception.php';
        $mail = new PHPMailer\PHPMailer\PHPMailer(true);
        $mail->isSMTP(); $mail->Host = 'smtp.gmail.com'; $mail->SMTPAuth = true;
        $mail->Username = 'christianbacay042504@gmail.com'; $mail->Password = 'tayrkzczbhgehbej';
        $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS; $mail->Port = 587;
        $mail->Timeout = 30;
        $mail->setFrom('christianbacay042504@gmail.com', 'SJDM Tours');
        $mail->addAddress($email);
        $mail->Subject = 'Your Tour Guide Application Has Been Approved!';
        $mail->isHTML(true);
        $mail->Body = "<p>Dear <strong>$firstName</strong>, your application has been approved!</p><p>Email: $email<br>Password: $password</p><p>Please change your password after first login.</p>";
        $mail->AltBody = "Congratulations $firstName! Your application was approved.\nEmail: $email\nPassword: $password\nPlease change your password after first login.";
        $mail->send();
        return true;
    } catch (Exception $e) { error_log("Email failed: " . $e->getMessage()); return false; }
}

function getRegistrationStats($conn) {
    $stats = [];
    $result = $conn->query("SELECT COUNT(*) as total FROM registration_tour_guide"); $stats['totalRegistrations'] = $result->fetch_assoc()['total'];
    $result = $conn->query("SELECT COUNT(*) as total FROM registration_tour_guide WHERE status='pending'"); $stats['pendingRegistrations'] = $result->fetch_assoc()['total'];
    $result = $conn->query("SELECT COUNT(*) as total FROM registration_tour_guide WHERE status='approved'"); $stats['approvedRegistrations'] = $result->fetch_assoc()['total'];
    $result = $conn->query("SELECT COUNT(*) as total FROM registration_tour_guide WHERE status='rejected'"); $stats['rejectedRegistrations'] = $result->fetch_assoc()['total'];
    return $stats;
}

// ── Init ──────────────────────────────────────────────────────────────────────
$currentUser = initAdminAuth();
$conn = getAdminConnection();

$dbSettings = [];
$result = $conn->query("SELECT setting_key, setting_value FROM admin_dashboard_settings");
if ($result) while ($row = $result->fetch_assoc()) $dbSettings[$row['setting_key']] = $row['setting_value'];

$umSettings = [];
$moduleTitle    = $umSettings['module_title']    ?? 'Tour Guide Registrations';
$moduleSubtitle = $umSettings['module_subtitle'] ?? 'Manage tour guide registration applications';

$adminInfo = ['role_title' => 'Administrator', 'admin_mark' => 'A'];
$stmt = $conn->prepare("SELECT admin_mark, role_title FROM admin_users WHERE user_id = ?");
$userId = $currentUser['id'];
$stmt->bind_param("i", $userId);
$stmt->execute();
if ($row = $stmt->get_result()->fetch_assoc()) $adminInfo = $row;
$stmt->close();
$adminMark = $adminInfo['admin_mark'] ?? 'A';

$menuItems = [];
$result = $conn->query("SELECT * FROM admin_menu_items WHERE is_active = 1 ORDER BY display_order ASC");
if ($result) while ($row = $result->fetch_assoc()) $menuItems[] = $row;

// ── AJAX handler ──────────────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    ini_set('display_errors', 0);
    error_reporting(E_ALL);
    try {
        $action = $_POST['action'] ?? '';
        if (empty($action)) { echo json_encode(['success' => false, 'message' => 'No action provided']); exit; }
        switch ($action) {
            case 'edit_registration':   echo json_encode(editRegistrationData($conn, $_POST)); exit;
            case 'add_registration':    echo json_encode(addRegistration($conn, $_POST)); exit;
            case 'update_status':
                $id = $_POST['registration_id'];
                $status = $_POST['status'];
                echo json_encode($status === 'approved' ? approveTourGuideRegistration($conn, $id) : updateRegistrationStatus($conn, $id, $status));
                exit;
            case 'delete_registration': echo json_encode(deleteRegistration($conn, $_POST['registration_id'], $_POST['justification'] ?? '')); exit;
            case 'get_registration':    echo json_encode(getRegistration($conn, $_POST['registration_id'])); exit;
            default: echo json_encode(['success' => false, 'message' => 'Unknown action: ' . $action]); exit;
        }
    } catch (Exception $e) { echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]); exit; }
      catch (Error $e)     { echo json_encode(['success' => false, 'message' => 'Fatal error: ' . $e->getMessage()]); exit; }
}

// ── Page data ─────────────────────────────────────────────────────────────────
$page   = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$limit  = intval($umSettings['default_registration_limit'] ?? 15);
$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';

$data          = getRegistrationsList($conn, $page, $limit, $search);
$registrations = $data['registrations'];
$pagination    = $data['pagination'];
$stats         = getRegistrationStats($conn);

$queryValues = ['totalUsers' => 0, 'totalBookings' => 0, 'totalGuides' => 0];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tour Guide Registrations | SJDM Tours Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet">
    <link rel="stylesheet" href="admin-styles.css">
    <style>
        /* ── Compact Stats ── */
        .um-stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(170px, 1fr)); gap: 14px; margin-bottom: 24px; }
        .stat-card-compact { background: white; border-radius: 14px; padding: 16px 18px; box-shadow: 0 2px 12px rgba(0,0,0,0.07); border: 1px solid rgba(0,0,0,0.06); transition: transform .25s ease, box-shadow .25s ease; display: flex; flex-direction: column; }
        .stat-card-compact:hover { transform: translateY(-3px); box-shadow: 0 8px 24px rgba(0,0,0,0.12); }
        .stat-card-compact[data-stat="total"]    { border-top: 3px solid #667eea; background: #fafbff; }
        .stat-card-compact[data-stat="pending"]  { border-top: 3px solid #f59e0b; background: #fffdf5; }
        .stat-card-compact[data-stat="approved"] { border-top: 3px solid #10b981; background: #f5fdf9; }
        .stat-card-compact[data-stat="rejected"] { border-top: 3px solid #ef4444; background: #fff5f5; }
        .scc-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 10px; }
        .scc-label { display: flex; align-items: center; gap: 5px; font-size: .68rem; font-weight: 700; text-transform: uppercase; letter-spacing: .8px; color: #6b7280; }
        .scc-label .material-icons-outlined { font-size: 14px; color: #9ca3af; }
        .scc-dot { width: 9px; height: 9px; border-radius: 50%; flex-shrink: 0; }
        .dot-blue { background: #667eea; } .dot-yellow { background: #f59e0b; } .dot-green { background: #10b981; } .dot-red { background: #ef4444; }
        .scc-number { font-size: 2rem; font-weight: 800; color: #111827; line-height: 1; margin-bottom: 10px; }
        .scc-trend { display: inline-flex; align-items: center; gap: 3px; font-size: .72rem; font-weight: 700; padding: 3px 8px; border-radius: 20px; width: fit-content; }
        .scc-trend.neutral  { color: #6b7280; background: rgba(107,114,128,.1); }
        .scc-trend.positive { color: #059669; background: rgba(16,185,129,.12); }
        .scc-trend.negative { color: #dc2626; background: rgba(239,68,68,.12); }
        .scc-trend.warning  { color: #d97706; background: rgba(245,158,11,.12); }
        .scc-trend .material-icons-outlined { font-size: 13px; }

        /* ── Filter Tabs ── */
        .filter-tabs { display: flex; gap: 8px; margin-bottom: 20px; background: #f3f4f6; padding: 6px; border-radius: 12px; width: fit-content; }
        .filter-tab { display: flex; align-items: center; gap: 7px; padding: 9px 18px; border: none; border-radius: 8px; background: transparent; color: #6b7280; font-size: .85rem; font-weight: 600; cursor: pointer; transition: all .2s; font-family: inherit; }
        .filter-tab .material-icons-outlined { font-size: 17px; }
        .filter-tab:hover { background: white; color: #374151; box-shadow: 0 1px 4px rgba(0,0,0,.08); }
        .filter-tab.active { background: white; color: #111827; box-shadow: 0 2px 8px rgba(0,0,0,.1); }

        /* ── Search Bar ── */
        .search-bar { display: flex; gap: 10px; margin-bottom: 20px; align-items: center; }
        .search-bar input { flex: 1; padding: 10px 16px; border: 1px solid #e5e7eb; border-radius: 10px; font-size: .9rem; outline: none; font-family: inherit; transition: border-color .2s; }
        .search-bar input:focus { border-color: #667eea; box-shadow: 0 0 0 3px rgba(102,126,234,.1); }

        /* ── Status Badges ── */
        .reg-badge { display: inline-flex; align-items: center; padding: 4px 10px; border-radius: 20px; font-size: .72rem; font-weight: 700; text-transform: capitalize; }
        .reg-badge.pending      { background: rgba(245,158,11,.12); color: #d97706; border: 1px solid rgba(245,158,11,.2); }
        .reg-badge.under_review { background: rgba(102,126,234,.12); color: #4f46e5; border: 1px solid rgba(102,126,234,.2); }
        .reg-badge.approved     { background: rgba(16,185,129,.12); color: #059669; border: 1px solid rgba(16,185,129,.2); }
        .reg-badge.rejected     { background: rgba(239,68,68,.1); color: #dc2626; border: 1px solid rgba(239,68,68,.2); }

        /* ── Action buttons ── */
        .action-buttons { display: flex; gap: 6px; }
        .btn-icon { width: 32px; height: 32px; border: 1px solid #e5e7eb; border-radius: 8px; background: white; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: all .2s; color: #6b7280; }
        .btn-icon:hover       { background: #f3f4f6; color: #111827; border-color: #d1d5db; }
        .btn-icon.approve:hover { background: #f0fdf4; color: #059669; border-color: #bbf7d0; }
        .btn-icon.reject:hover  { background: #fef2f2; color: #dc2626; border-color: #fecaca; }
        .btn-icon.del:hover     { background: #fef2f2; color: #dc2626; border-color: #fecaca; }
        .btn-icon .material-icons-outlined { font-size: 16px; }

        /* ── Specialization pill ── */
        .spec-pill { background: #f3f4f6; padding: 3px 10px; border-radius: 8px; font-size: .8rem; font-weight: 600; color: #374151; }

        /* ── Pagination ── */
        .pagination { display: flex; align-items: center; gap: 8px; margin-top: 24px; justify-content: center; }
        .pagination-btn { display: flex; align-items: center; gap: 5px; padding: 9px 16px; border: 1px solid #e5e7eb; border-radius: 9px; background: white; color: #374151; font-size: .85rem; font-weight: 600; cursor: pointer; transition: all .2s; font-family: inherit; }
        .pagination-btn:hover { background: #667eea; color: white; border-color: #667eea; }
        .pagination-btn .material-icons-outlined { font-size: 18px; }
        .pagination-numbers { display: flex; gap: 6px; }
        .pagination-number { min-width: 38px; height: 38px; border: 1px solid #e5e7eb; border-radius: 8px; background: white; color: #374151; font-size: .85rem; font-weight: 600; cursor: pointer; transition: all .2s; display: flex; align-items: center; justify-content: center; font-family: inherit; }
        .pagination-number:hover { background: #f3f4f6; border-color: #667eea; }
        .pagination-number.active { background: #667eea; color: white; border-color: #667eea; }

        /* ── Modals ── */
        .modal { display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background: rgba(0,0,0,.45); backdrop-filter: blur(4px); }
        .modal.show { display: flex !important; align-items: center; justify-content: center; }
        .modal-content { background: white; border-radius: 16px; width: 90%; max-width: 560px; max-height: 90vh; overflow-y: auto; box-shadow: 0 20px 60px rgba(0,0,0,.2); animation: slideIn .25s ease; }
        .modal-content.wide  { max-width: 720px; }
        .modal-content.small { max-width: 440px; }
        @keyframes slideIn { from { transform: translateY(-20px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
        .modal-header { display: flex; justify-content: space-between; align-items: center; padding: 20px 24px; border-bottom: 1px solid #f3f4f6; }
        .modal-header h2 { margin: 0; font-size: 1.05rem; font-weight: 700; color: #111827; }
        .modal-close { width: 32px; height: 32px; border: none; background: #f3f4f6; border-radius: 8px; cursor: pointer; display: flex; align-items: center; justify-content: center; color: #6b7280; transition: all .2s; }
        .modal-close:hover { background: #e5e7eb; color: #111827; }
        .modal-close .material-icons-outlined { font-size: 18px; }
        .modal-body { padding: 24px; }
        .modal-footer { display: flex; justify-content: flex-end; gap: 10px; padding: 16px 24px; border-top: 1px solid #f3f4f6; }

        /* ── Form in modals ── */
        .form-group { margin-bottom: 16px; }
        .form-group label { display: block; margin-bottom: 6px; font-size: .82rem; font-weight: 600; color: #374151; }
        .form-group select, .form-group textarea, .form-group input[type="text"], .form-group input[type="email"], .form-group input[type="tel"], .form-group input[type="number"] {
            width: 100%; padding: 10px 14px; border: 1px solid #e5e7eb; border-radius: 10px; font-size: .9rem; font-family: inherit; outline: none; transition: border-color .2s; box-sizing: border-box;
        }
        .form-group select:focus, .form-group textarea:focus, .form-group input:focus { border-color: #667eea; box-shadow: 0 0 0 3px rgba(102,126,234,.1); }
        .form-group textarea { height: 90px; resize: vertical; }
        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }

        /* ── Detail sections in view modal ── */
        .detail-section { margin-bottom: 22px; }
        .detail-section h4 { font-size: .75rem; font-weight: 700; text-transform: uppercase; letter-spacing: .8px; color: #667eea; margin: 0 0 10px; padding-bottom: 8px; border-bottom: 2px solid #ede9fe; }
        .detail-row { display: flex; justify-content: space-between; align-items: flex-start; padding: 9px 0; border-bottom: 1px solid #f9fafb; gap: 16px; }
        .detail-row:last-child { border-bottom: none; }
        .detail-row label { font-size: .78rem; font-weight: 600; color: #9ca3af; min-width: 150px; flex-shrink: 0; }
        .detail-row span  { font-size: .875rem; font-weight: 500; color: #111827; text-align: right; }

        /* ── Review actions inside view modal ── */
        .review-actions { display: flex; gap: 10px; justify-content: center; flex-wrap: wrap; }
        .btn-approve { background: #10b981; color: white; border: none; padding: 10px 20px; border-radius: 10px; font-size: .875rem; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 6px; transition: all .2s; font-family: inherit; }
        .btn-approve:hover { background: #059669; transform: translateY(-1px); }
        .btn-reject  { background: #ef4444; color: white; border: none; padding: 10px 20px; border-radius: 10px; font-size: .875rem; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 6px; transition: all .2s; font-family: inherit; }
        .btn-reject:hover  { background: #dc2626; transform: translateY(-1px); }
        .btn-review  { background: #f59e0b; color: white; border: none; padding: 10px 20px; border-radius: 10px; font-size: .875rem; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 6px; transition: all .2s; font-family: inherit; }
        .btn-review:hover  { background: #d97706; transform: translateY(-1px); }

        /* ── Shared action btns ── */
        .btn-submit { background: linear-gradient(135deg,#667eea,#764ba2); color: white; border: none; padding: 10px 20px; border-radius: 10px; font-size: .875rem; font-weight: 600; cursor: pointer; transition: all .2s; font-family: inherit; }
        .btn-submit:hover { opacity: .9; transform: translateY(-1px); }
        .btn-cancel { background: #f3f4f6; color: #374151; border: 1px solid #e5e7eb; padding: 10px 20px; border-radius: 10px; font-size: .875rem; font-weight: 600; cursor: pointer; transition: all .2s; font-family: inherit; }
        .btn-cancel:hover { background: #e5e7eb; }
        .btn-danger-solid { background: #ef4444; color: white; border: none; padding: 10px 20px; border-radius: 10px; font-size: .875rem; font-weight: 600; cursor: pointer; transition: all .2s; font-family: inherit; }
        .btn-danger-solid:hover { background: #dc2626; }

        /* ── Centered modal icons ── */
        .modal-icon { width: 64px; height: 64px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px; }
        .modal-icon .material-icons-outlined { font-size: 32px; color: white; }
        .modal-icon.green  { background: linear-gradient(135deg,#10b981,#059669); }
        .modal-icon.red    { background: linear-gradient(135deg,#ef4444,#dc2626); }
        .modal-icon.yellow { background: linear-gradient(135deg,#f59e0b,#d97706); }
        .modal-centered { text-align: center; padding: 8px 0 16px; }
        .modal-centered p { font-size: 1rem; color: #374151; margin: 0; font-weight: 500; }

        /* ── Credentials box ── */
        .cred-box { background: #f9fafb; border-radius: 12px; border-left: 4px solid #10b981; padding: 20px; margin: 16px 0; }
        .cred-row { display: flex; justify-content: space-between; align-items: center; padding: 10px 12px; background: white; border-radius: 8px; margin-bottom: 10px; }
        .cred-row:last-child { margin-bottom: 0; }
        .cred-label { font-weight: 600; color: #374151; font-size: .875rem; }
        .cred-value { font-family: monospace; font-weight: 700; color: #667eea; font-size: .9rem; }
        .cred-value.pw { color: #ef4444; }

        /* ── Document rows ── */
        .doc-row { display: flex; justify-content: space-between; align-items: center; padding: 11px 0; border-bottom: 1px solid #f3f4f6; }
        .doc-row:last-child { border-bottom: none; }
        .doc-label { display: flex; align-items: center; gap: 8px; font-size: .875rem; font-weight: 600; color: #374151; }
        .doc-label .material-icons-outlined { font-size: 18px; color: #667eea; }

        /* ── Signout modal ── */
        .signout-content { display: flex; flex-direction: column; align-items: center; gap: 20px; padding: 16px 0; text-align: center; }
        .signout-icon { width: 72px; height: 72px; border-radius: 50%; background: linear-gradient(135deg,#ef4444,#dc2626); color: white; display: flex; align-items: center; justify-content: center; box-shadow: 0 8px 24px rgba(239,68,68,.3); }
        .signout-icon .material-icons-outlined { font-size: 36px; }
        .signout-message h3 { margin: 0 0 6px; font-size: 1.1rem; font-weight: 700; color: #111827; }
        .signout-message p  { margin: 0; font-size: .875rem; color: #6b7280; }

        @media (max-width: 640px) { .um-stats-grid { grid-template-columns: repeat(2,1fr); } .form-grid { grid-template-columns: 1fr; } .filter-tabs { width: 100%; } }
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
                if (stripos($item['menu_name'],'hotels')!==false || stripos($item['menu_url'],'hotels')!==false ||
                    stripos($item['menu_name'],'settings')!==false || stripos($item['menu_url'],'settings')!==false ||
                    stripos($item['menu_name'],'reports')!==false || stripos($item['menu_url'],'reports')!==false) continue;
                $isActive = basename($_SERVER['PHP_SELF'])==$item['menu_url'] ? 'active' : '';
                $badgeVal = isset($item['badge_query']) && isset($queryValues[$item['badge_query']]) ? $queryValues[$item['badge_query']] : 0;
            ?>
            <a href="<?php echo $item['menu_url']; ?>" class="nav-item <?php echo $isActive; ?>">
                <span class="material-icons-outlined"><?php echo $item['menu_icon']; ?></span>
                <span><?php echo $item['menu_name']; ?></span>
                <?php if ($badgeVal > 0): ?><span class="badge"><?php echo $badgeVal; ?></span><?php endif; ?>
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
                        <div class="scc-label"><span class="material-icons-outlined">assignment</span> Total</div>
                        <span class="scc-dot dot-blue"></span>
                    </div>
                    <div class="scc-number"><?php echo $stats['totalRegistrations']; ?></div>
                    <div class="scc-trend neutral"><span class="material-icons-outlined">inbox</span><span>All applications</span></div>
                </div>
                <div class="stat-card-compact" data-stat="pending">
                    <div class="scc-header">
                        <div class="scc-label"><span class="material-icons-outlined">schedule</span> Pending</div>
                        <span class="scc-dot dot-yellow"></span>
                    </div>
                    <div class="scc-number"><?php echo $stats['pendingRegistrations']; ?></div>
                    <div class="scc-trend warning"><span class="material-icons-outlined">pending</span><span>Awaiting review</span></div>
                </div>
                <div class="stat-card-compact" data-stat="approved">
                    <div class="scc-header">
                        <div class="scc-label"><span class="material-icons-outlined">check_circle</span> Approved</div>
                        <span class="scc-dot dot-green"></span>
                    </div>
                    <div class="scc-number"><?php echo $stats['approvedRegistrations']; ?></div>
                    <div class="scc-trend positive"><span class="material-icons-outlined">north_east</span><span>Accepted</span></div>
                </div>
                <div class="stat-card-compact" data-stat="rejected">
                    <div class="scc-header">
                        <div class="scc-label"><span class="material-icons-outlined">cancel</span> Rejected</div>
                        <span class="scc-dot dot-red"></span>
                    </div>
                    <div class="scc-number"><?php echo $stats['rejectedRegistrations']; ?></div>
                    <div class="scc-trend negative"><span class="material-icons-outlined">south_east</span><span>Declined</span></div>
                </div>
            </div>

            <!-- Filter Tabs -->
            <div class="filter-tabs">
                <button class="filter-tab" onclick="window.location.href='tour-guides.php'">
                    <span class="material-icons-outlined">tour</span> Tour Guides
                </button>
                <button class="filter-tab active">
                    <span class="material-icons-outlined">how_to_reg</span> Tour Guides Registrations
                </button>
            </div>

            <!-- Search -->
            <div class="search-bar">
                <input type="text" id="searchInput" placeholder="Search by name, email, or specialization..." value="<?php echo htmlspecialchars($search); ?>">
                <button class="btn-cancel" onclick="searchRegistrations()">
                    <span class="material-icons-outlined" style="font-size:16px;vertical-align:middle;">search</span> Search
                </button>
                <button class="btn-cancel" onclick="clearSearch()">
                    <span class="material-icons-outlined" style="font-size:16px;vertical-align:middle;">clear</span> Clear
                </button>
            </div>

            <!-- Table -->
            <div class="table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Full Name (LN, FN, MI)</th>
                            <th>Gender</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Specialization</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($registrations as $reg): ?>
                        <tr>
                            <td>
                                <div style="display:flex;align-items:center;gap:10px;">
                                    <div style="width:34px;height:34px;border-radius:50%;background:linear-gradient(135deg,#667eea,#764ba2);display:flex;align-items:center;justify-content:center;color:white;font-weight:700;font-size:.85rem;flex-shrink:0;">
                                        <?php echo strtoupper(substr($reg['first_name'],0,1)); ?>
                                    </div>
                                    <strong><?php
                                        if (!empty($reg['guide_name'])) echo htmlspecialchars($reg['guide_name']);
                                        else echo htmlspecialchars($reg['last_name'].', '.$reg['first_name'].' '.($reg['middle_initial']??''));
                                    ?></strong>
                                </div>
                            </td>
                            <td style="color:#6b7280;font-size:.875rem;"><?php echo htmlspecialchars($reg['gender']); ?></td>
                            <td style="color:#6b7280;font-size:.875rem;"><?php echo htmlspecialchars($reg['email']); ?></td>
                            <td style="color:#374151;font-size:.875rem;"><?php echo htmlspecialchars($reg['primary_phone']); ?></td>
                            <td><span class="spec-pill"><?php echo htmlspecialchars($reg['guide_specialty']??$reg['specialization']); ?></span></td>
                            <td><span class="reg-badge <?php echo $reg['status']; ?>"><?php echo str_replace('_',' ',$reg['status']); ?></span></td>
                            <td>
                                <div class="action-buttons">
                                    <button class="btn-icon" onclick="viewRegistration(<?php echo $reg['id']; ?>)" title="View">
                                        <span class="material-icons-outlined">visibility</span>
                                    </button>
                                    <button class="btn-icon approve" onclick="doUpdateStatus(<?php echo $reg['id']; ?>,'approved')" title="Approve">
                                        <span class="material-icons-outlined">check</span>
                                    </button>
                                    <button class="btn-icon reject" onclick="doUpdateStatus(<?php echo $reg['id']; ?>,'rejected')" title="Reject">
                                        <span class="material-icons-outlined">close</span>
                                    </button>
                                    <button class="btn-icon del" onclick="deleteRegistration(<?php echo $reg['id']; ?>)" title="Delete">
                                        <span class="material-icons-outlined">delete</span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($registrations)): ?>
                        <tr><td colspan="7" style="text-align:center;padding:48px;color:#9ca3af;">
                            <span class="material-icons-outlined" style="font-size:44px;display:block;margin-bottom:10px;color:#d1d5db;">how_to_reg</span>
                            No registrations found
                        </td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <?php if ($pagination['total_pages'] > 1): ?>
            <div class="pagination">
                <?php if ($pagination['current_page'] > 1): ?>
                <button class="pagination-btn" onclick="goToPage(<?php echo $pagination['current_page']-1; ?>)">
                    <span class="material-icons-outlined">chevron_left</span> Previous
                </button>
                <?php endif; ?>
                <div class="pagination-numbers">
                    <?php for ($i=1; $i<=$pagination['total_pages']; $i++): ?>
                    <button class="pagination-number <?php echo $i==$pagination['current_page']?'active':''; ?>" onclick="goToPage(<?php echo $i; ?>)"><?php echo $i; ?></button>
                    <?php endfor; ?>
                </div>
                <?php if ($pagination['current_page'] < $pagination['total_pages']): ?>
                <button class="pagination-btn" onclick="goToPage(<?php echo $pagination['current_page']+1; ?>)">
                    Next <span class="material-icons-outlined">chevron_right</span>
                </button>
                <?php endif; ?>
            </div>
            <?php endif; ?>

        </div><!-- /content-area -->
    </main>
</div><!-- /admin-container -->

<!-- ═══ VIEW REGISTRATION MODAL ═══ -->
<div id="viewRegistrationModal" class="modal">
    <div class="modal-content wide">
        <div class="modal-header">
            <h2 id="viewModalTitle">Registration Details</h2>
            <button class="modal-close" onclick="closeModal('viewRegistrationModal')"><span class="material-icons-outlined">close</span></button>
        </div>
        <div class="modal-body" style="max-height:65vh;overflow-y:auto;" id="viewModalBody"></div>
        <div class="modal-footer" id="viewModalFooter">
            <button class="btn-cancel" onclick="closeModal('viewRegistrationModal')">Close</button>
            <button class="btn-submit" onclick="switchToEditForm()">Edit</button>
        </div>
    </div>
</div>

<!-- ═══ CONFIRM MODAL ═══ -->
<div id="confirmModal" class="modal">
    <div class="modal-content small">
        <div class="modal-header">
            <h2>Confirm Action</h2>
            <button class="modal-close" onclick="closeModal('confirmModal')"><span class="material-icons-outlined">close</span></button>
        </div>
        <div class="modal-body">
            <div class="modal-centered">
                <div class="modal-icon yellow"><span class="material-icons-outlined">help</span></div>
                <p id="confirmMsg">Are you sure?</p>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn-cancel" onclick="closeModal('confirmModal')">Cancel</button>
            <button class="btn-submit" id="confirmOkBtn">Confirm</button>
        </div>
    </div>
</div>

<!-- ═══ SUCCESS MODAL ═══ -->
<div id="successModal" class="modal">
    <div class="modal-content small">
        <div class="modal-header">
            <h2>Success</h2>
            <button class="modal-close" onclick="closeSuccessModal()"><span class="material-icons-outlined">close</span></button>
        </div>
        <div class="modal-body">
            <div class="modal-centered">
                <div class="modal-icon green"><span class="material-icons-outlined">check</span></div>
                <p id="successMsg"></p>
            </div>
        </div>
        <div class="modal-footer"><button class="btn-submit" onclick="closeSuccessModal()">OK</button></div>
    </div>
</div>

<!-- ═══ ERROR MODAL ═══ -->
<div id="errorModal" class="modal">
    <div class="modal-content small">
        <div class="modal-header">
            <h2>Error</h2>
            <button class="modal-close" onclick="closeModal('errorModal')"><span class="material-icons-outlined">close</span></button>
        </div>
        <div class="modal-body">
            <div class="modal-centered">
                <div class="modal-icon red"><span class="material-icons-outlined">error</span></div>
                <p id="errorMsg"></p>
            </div>
        </div>
        <div class="modal-footer"><button class="btn-danger-solid" onclick="closeModal('errorModal')">OK</button></div>
    </div>
</div>

<!-- ═══ ACCOUNT CREATED MODAL ═══ -->
<div id="accountCreatedModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>✅ Account Created!</h2>
            <button class="modal-close" onclick="closeModal('accountCreatedModal')"><span class="material-icons-outlined">close</span></button>
        </div>
        <div class="modal-body">
            <div style="text-align:center;margin-bottom:16px;">
                <div class="modal-icon green" style="margin-bottom:12px;"><span class="material-icons-outlined">person_add</span></div>
                <h3 id="acName" style="margin:0;color:#111827;font-size:1.1rem;"></h3>
            </div>
            <div class="cred-box">
                <div style="font-size:.78rem;font-weight:700;color:#059669;margin-bottom:12px;text-transform:uppercase;letter-spacing:.6px;">🔐 Account Credentials</div>
                <div class="cred-row">
                    <span class="cred-label">Email</span>
                    <span class="cred-value" id="acEmail"></span>
                </div>
                <div class="cred-row">
                    <span class="cred-label">Temporary Password</span>
                    <div style="display:flex;align-items:center;gap:8px;">
                        <span class="cred-value pw" id="acPassword"></span>
                        <button onclick="copyPassword()" style="background:#667eea;color:white;border:none;padding:4px 10px;border-radius:6px;cursor:pointer;font-size:.75rem;font-family:inherit;">Copy</button>
                    </div>
                </div>
            </div>
            <div style="background:#fffbeb;border:1px solid #fde68a;border-radius:10px;padding:12px 14px;font-size:.82rem;color:#92400e;">
                <strong>📧 Email sent</strong> — An approval email with these credentials has been sent to the tour guide.
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn-cancel" onclick="closeModal('accountCreatedModal')">Close</button>
            <button class="btn-submit" onclick="location.reload()">Refresh List</button>
        </div>
    </div>
</div>

<!-- ═══ DELETE MODAL ═══ -->
<div id="deleteModal" class="modal">
    <div class="modal-content small">
        <div class="modal-header">
            <h2>Delete Registration</h2>
            <button class="modal-close" onclick="closeModal('deleteModal')"><span class="material-icons-outlined">close</span></button>
        </div>
        <div class="modal-body">
            <div class="modal-centered">
                <div class="modal-icon red"><span class="material-icons-outlined">warning</span></div>
                <p>Are you sure you want to delete this registration? <strong>This cannot be undone.</strong></p>
            </div>
            
            <!-- Item Identification Section -->
            <div style="margin-top:20px;padding:16px;background:#f9fafb;border-radius:10px;border:1px solid #e5e7eb;">
                <h4 style="margin:0 0 12px;font-size:.9rem;font-weight:600;color:#374151;display:flex;align-items:center;gap:6px;">
                    <span class="material-icons-outlined" style="font-size:18px;color:#667eea;">person_search</span>
                    Registration to be Deleted
                </h4>
                <div id="deleteRegistrationDetails" style="font-size:.85rem;color:#6b7280;"></div>
            </div>
            
            <!-- Justification Section -->
            <div style="margin-top:16px;">
                <label for="deleteRegistrationJustification" style="display:block;margin-bottom:6px;font-size:.82rem;font-weight:600;color:#374151;">
                    <span class="material-icons-outlined" style="font-size:16px;vertical-align:middle;margin-right:4px;color:#ef4444;">assignment_late</span>
                    Justification for Deletion <span style="color:#ef4444;">*</span>
                </label>
                <textarea id="deleteRegistrationJustification" name="justification" rows="3" placeholder="Please provide a specific reason why this registration must be deleted..." required
                    style="width:100%;padding:10px 14px;border:1px solid #e5e7eb;border-radius:10px;font-size:.875rem;font-family:inherit;outline:none;transition:border-color .2s;box-sizing:border-box;resize:vertical;"></textarea>
                <div style="margin-top:6px;font-size:.75rem;color:#6b7280;">
                    Examples: Duplicate application, incomplete requirements, fraudulent documents, user withdrawal request, etc.
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn-cancel" onclick="closeModal('deleteModal')">Cancel</button>
            <button class="btn-danger-solid" id="deleteConfirmBtn">Delete</button>
        </div>
    </div>
</div>

<!-- ═══ DOCUMENT PREVIEW MODAL ═══ -->
<div id="documentPreviewModal" class="modal">
    <div class="modal-content wide">
        <div class="modal-header">
            <h2 id="docPreviewTitle">Document Preview</h2>
            <button class="modal-close" onclick="closeModal('documentPreviewModal')"><span class="material-icons-outlined">close</span></button>
        </div>
        <div class="modal-body" style="padding:0;max-height:65vh;overflow:auto;" id="docPreviewContent"></div>
        <div class="modal-footer">
            <button class="btn-cancel" onclick="closeModal('documentPreviewModal')">Close</button>
            <a id="docDownloadLink" href="" target="_blank" class="btn-submit" style="text-decoration:none;display:inline-flex;align-items:center;gap:6px;">
                <span class="material-icons-outlined" style="font-size:17px;">download</span> Download
            </a>
        </div>
    </div>
</div>

<!-- ═══ SIGN OUT MODAL ═══ -->
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
/* ── Core helpers ── */
const $id = id => document.getElementById(id);
function escH(s) { const d=document.createElement('div'); d.textContent=s??'—'; return d.innerHTML||'—'; }

function openModal(id)  { const m=$id(id); if(m){m.classList.add('show'); document.body.style.overflow='hidden';} }
function closeModal(id) { const m=$id(id); if(m){m.classList.remove('show'); document.body.style.overflow='';} }

function showSuccess(msg, cb=null) {
    $id('successMsg').textContent = msg;
    window._successCb = cb;
    openModal('successModal');
}
function closeSuccessModal() {
    closeModal('successModal');
    if (window._successCb) { const cb=window._successCb; window._successCb=null; cb(); }
}
function showError(msg) { $id('errorMsg').textContent=msg; openModal('errorModal'); }

function showConfirm(msg, cb) {
    $id('confirmMsg').textContent = msg;
    const btn = $id('confirmOkBtn');
    const nb = btn.cloneNode(true);
    btn.parentNode.replaceChild(nb, btn);
    nb.addEventListener('click', () => { closeModal('confirmModal'); cb(); });
    openModal('confirmModal');
}

/* ── Navigation ── */
function searchRegistrations() {
    const q = $id('searchInput').value;
    const url = new URL(window.location);
    q ? url.searchParams.set('search',q) : url.searchParams.delete('search');
    url.searchParams.delete('page');
    window.location.href = url;
}
function clearSearch() {
    const url = new URL(window.location);
    url.searchParams.delete('search'); url.searchParams.delete('page');
    window.location.href = url;
}
function goToPage(p) {
    const url = new URL(window.location);
    url.searchParams.set('page',p);
    window.location.href = url;
}
$id('searchInput').addEventListener('keypress', e => { if(e.key==='Enter') searchRegistrations(); });

/* ── View Registration ── */
window.currentReg = null;

function viewRegistration(id) {
    fetch('tour-guide-registrations.php', {
        method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'},
        body:`action=get_registration&registration_id=${id}`
    })
    .then(r=>r.json())
    .then(data => {
        if (!data.success) { showError(data.message||'Failed to load'); return; }
        window.currentReg = data.data;
        renderViewModal(data.data);
        $id('viewModalTitle').textContent = 'Registration Details';
        $id('viewModalFooter').innerHTML = `
            <button class="btn-cancel" onclick="closeModal('viewRegistrationModal')">Close</button>
            <button class="btn-submit" onclick="switchToEditForm()">Edit</button>
        `;
        openModal('viewRegistrationModal');
    })
    .catch(e => showError('Error: '+e.message));
}

function renderViewModal(r) {
    const sc = r.status||'pending';
    $id('viewModalBody').innerHTML = `
        <div class="detail-section">
            <h4>Personal Information</h4>
            <div class="detail-row"><label>Full Name</label><span>${escH((r.guide_name)||(r.last_name+', '+r.first_name+' '+(r.middle_initial||'')))}</span></div>
            <div class="detail-row"><label>Preferred Name</label><span>${escH(r.preferred_name)}</span></div>
            <div class="detail-row"><label>Date of Birth</label><span>${r.date_of_birth?new Date(r.date_of_birth).toLocaleDateString():'—'}</span></div>
            <div class="detail-row"><label>Gender</label><span>${escH(r.gender)}</span></div>
            <div class="detail-row"><label>Home Address</label><span>${escH(r.home_address)}</span></div>
        </div>
        <div class="detail-section">
            <h4>Contact Information</h4>
            <div class="detail-row"><label>Email</label><span>${escH(r.email)}</span></div>
            <div class="detail-row"><label>Primary Phone</label><span>${escH(r.primary_phone)}</span></div>
            <div class="detail-row"><label>Secondary Phone</label><span>${escH(r.secondary_phone)}</span></div>
        </div>
        <div class="detail-section">
            <h4>Emergency Contact</h4>
            <div class="detail-row"><label>Contact Name</label><span>${escH(r.emergency_contact_name)}</span></div>
            <div class="detail-row"><label>Relationship</label><span>${escH(r.emergency_contact_relationship)}</span></div>
            <div class="detail-row"><label>Contact Phone</label><span>${escH(r.emergency_contact_phone)}</span></div>
        </div>
        <div class="detail-section">
            <h4>Professional Information</h4>
            <div class="detail-row"><label>DOT Accreditation #</label><span>${escH(r.dot_accreditation)}</span></div>
            <div class="detail-row"><label>Accreditation Expiry</label><span>${r.accreditation_expiry?new Date(r.accreditation_expiry).toLocaleDateString():'—'}</span></div>
            <div class="detail-row"><label>Specialization</label><span>${escH(r.guide_specialty||r.specialization)}</span></div>
            <div class="detail-row"><label>Years of Experience</label><span>${escH(String(r.years_experience||0))} years</span></div>
            <div class="detail-row"><label>First Aid Certified</label><span>${escH(r.first_aid_certified||'No')}</span></div>
            <div class="detail-row"><label>First Aid Expiry</label><span>${r.first_aid_expiry?new Date(r.first_aid_expiry).toLocaleDateString():'—'}</span></div>
            <div class="detail-row"><label>Base Location</label><span>${escH(r.base_location)}</span></div>
            <div class="detail-row"><label>Employment Type</label><span>${escH(r.employment_type)}</span></div>
            <div class="detail-row"><label>Has Vehicle</label><span>${escH(r.has_vehicle||'No')}</span></div>
        </div>
        <div class="detail-section">
            <h4>Application Status</h4>
            <div class="detail-row"><label>Status</label><span><span class="reg-badge ${sc}">${sc.replace('_',' ')}</span></span></div>
            <div class="detail-row"><label>Application Date</label><span>${r.application_date?new Date(r.application_date).toLocaleDateString():'—'}</span></div>
            <div class="detail-row"><label>Review Date</label><span>${r.review_date?new Date(r.review_date).toLocaleDateString():'Not reviewed yet'}</span></div>
            <div class="detail-row"><label>Admin Notes</label><span>${escH(r.admin_notes||'No notes')}</span></div>
        </div>
        <div class="detail-section">
            <h4>Documents</h4>
            ${generateDocumentLinks(r)}
        </div>
        <div style="background:#f9fafb;border-radius:12px;padding:20px;text-align:center;">
            <div style="font-size:.72rem;font-weight:700;text-transform:uppercase;letter-spacing:.6px;color:#6b7280;margin-bottom:14px;">Review Application</div>
            <div class="review-actions">
                <button class="btn-approve" onclick="doUpdateStatus(${r.id},'approved')"><span class="material-icons-outlined">check_circle</span>Approve</button>
                <button class="btn-reject"  onclick="doUpdateStatus(${r.id},'rejected')"><span class="material-icons-outlined">cancel</span>Reject</button>
                <button class="btn-review"  onclick="doUpdateStatus(${r.id},'under_review')"><span class="material-icons-outlined">schedule</span>Under Review</button>
            </div>
        </div>
    `;
}

/* ── Documents ── */
function generateDocumentLinks(r) {
    const docs = [
        {field:'resume_file', label:'Resume/CV'},
        {field:'dot_id_file', label:'DOT ID'},
        {field:'government_id_file', label:'Government ID'},
        {field:'nbi_clearance_file', label:'NBI Clearance'},
        {field:'first_aid_certificate_file', label:'First Aid Certificate'},
        {field:'id_photo_file', label:'ID Photo'}
    ];
    let html = '';
    docs.forEach(doc => {
        if (r[doc.field]) {
            const fp = '/coderistyarn2/uploads/' + r[doc.field];
            const ext = r[doc.field].split('.').pop().toLowerCase();
            const isImg = ['jpg','jpeg','png','gif','bmp','webp'].includes(ext);
            const icon = isImg ? 'image' : (ext==='pdf' ? 'picture_as_pdf' : 'description');
            html += `
                <div class="doc-row">
                    <div class="doc-label">
                        <span class="material-icons-outlined">${icon}</span>${escH(doc.label)}
                        <span style="background:#e5e7eb;padding:2px 7px;border-radius:5px;font-size:.72rem;font-weight:700;color:#374151;">${ext.toUpperCase()}</span>
                    </div>
                    <div style="display:flex;gap:8px;">
                        <button onclick="viewDocument('${fp}','${escH(doc.label)}','${ext}')" class="btn-icon" title="View"><span class="material-icons-outlined">visibility</span></button>
                        <a href="${fp}" target="_blank" class="btn-icon" style="text-decoration:none;" title="Open"><span class="material-icons-outlined">open_in_new</span></a>
                    </div>
                </div>`;
        }
    });
    return html || '<div style="text-align:center;padding:20px;color:#9ca3af;">No documents uploaded</div>';
}

function viewDocument(filePath, title, ext) {
    $id('docPreviewTitle').textContent = title;
    $id('docDownloadLink').href = filePath;
    const isImg = ['jpg','jpeg','png','gif','bmp','webp'].includes(ext);
    if (isImg) {
        $id('docPreviewContent').innerHTML = `<div style="padding:16px;text-align:center;"><img src="${filePath}" style="max-width:100%;max-height:55vh;border-radius:8px;"></div>`;
    } else if (ext==='pdf') {
        $id('docPreviewContent').innerHTML = `<iframe src="${filePath}" style="width:100%;height:55vh;border:none;"></iframe>`;
    } else {
        $id('docPreviewContent').innerHTML = `<div style="padding:32px;text-align:center;color:#6b7280;"><span class="material-icons-outlined" style="font-size:48px;display:block;margin-bottom:12px;color:#d1d5db;">description</span>Preview not available. Please download to view.</div>`;
    }
    openModal('documentPreviewModal');
}

/* ── Edit form ── */
function switchToEditForm() {
    const r = window.currentReg;
    if (!r) return;
    $id('viewModalTitle').textContent = 'Edit Registration';
    $id('viewModalBody').innerHTML = `
        <form id="editRegForm" onsubmit="handleEditReg(event)">
            <input type="hidden" name="action" value="edit_registration">
            <input type="hidden" name="id" value="${r.id}">
            <div class="form-grid">
                <div class="form-group"><label>Last Name</label><input type="text" name="last_name" value="${escH(r.last_name)}" required></div>
                <div class="form-group"><label>First Name</label><input type="text" name="first_name" value="${escH(r.first_name)}" required></div>
                <div class="form-group"><label>Middle Initial</label><input type="text" name="middle_initial" value="${escH(r.middle_initial)}"></div>
                <div class="form-group"><label>Gender</label>
                    <select name="gender">
                        <option value="Male" ${r.gender==='Male'?'selected':''}>Male</option>
                        <option value="Female" ${r.gender==='Female'?'selected':''}>Female</option>
                        <option value="Other" ${r.gender==='Other'?'selected':''}>Other</option>
                    </select>
                </div>
                <div class="form-group"><label>Email</label><input type="email" name="email" value="${escH(r.email)}" required></div>
                <div class="form-group"><label>Primary Phone</label><input type="tel" name="primary_phone" value="${escH(r.primary_phone)}"></div>
                <div class="form-group"><label>Specialization</label><input type="text" name="specialization" value="${escH(r.specialization)}"></div>
                <div class="form-group"><label>Years of Experience</label><input type="number" name="years_of_experience" value="${r.years_experience||0}" min="0" max="50"></div>
                <div class="form-group"><label>Status</label>
                    <select name="status">
                        <option value="pending"      ${r.status==='pending'?'selected':''}>Pending</option>
                        <option value="under_review" ${r.status==='under_review'?'selected':''}>Under Review</option>
                        <option value="approved"     ${r.status==='approved'?'selected':''}>Approved</option>
                        <option value="rejected"     ${r.status==='rejected'?'selected':''}>Rejected</option>
                    </select>
                </div>
                <div class="form-group" style="grid-column:1/-1"><label>Admin Notes</label><textarea name="admin_notes">${escH(r.admin_notes)}</textarea></div>
            </div>
        </form>
    `;
    $id('viewModalFooter').innerHTML = `
        <button class="btn-cancel" onclick="viewRegistration(${r.id})">Back</button>
        <button class="btn-submit" onclick="document.getElementById('editRegForm').requestSubmit()">Save Changes</button>
    `;
}

function handleEditReg(e) {
    e.preventDefault();
    fetch('tour-guide-registrations.php', { method:'POST', body: new FormData(e.target) })
    .then(r=>r.json())
    .then(data => {
        if (data.success) { closeModal('viewRegistrationModal'); showSuccess(data.message||'Updated!', ()=>location.reload()); }
        else showError(data.message||'Update failed');
    })
    .catch(err => showError('Error: '+err.message));
}

/* ── Status updates ── */
function doUpdateStatus(id, status) {
    const labels = {approved:'approve',rejected:'reject',under_review:'mark as under review'};
    showConfirm(`Are you sure you want to ${labels[status]||'update'} this registration?`, () => {
        fetch('tour-guide-registrations.php', {
            method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'},
            body:`action=update_status&registration_id=${id}&status=${status}`
        })
        .then(r=>r.json())
        .then(data => {
            if (data.success) {
                closeModal('viewRegistrationModal');
                if (status==='approved' && data.email && data.password) {
                    $id('acName').textContent = data.name||'Tour Guide';
                    $id('acEmail').textContent = data.email;
                    $id('acPassword').textContent = data.password;
                    window._generatedPw = data.password;
                    openModal('accountCreatedModal');
                } else {
                    showSuccess(data.message||'Status updated!', ()=>location.reload());
                }
            } else showError(data.message||'Error updating status');
        })
        .catch(e=>showError('Error: '+e.message));
    });
}

/* ── Alias for inline onclick on table rows ── */
function updateRegistrationStatus(id, status) { doUpdateStatus(id, status); }

/* ── Delete ── */
function deleteRegistration(id) {
    // Fetch registration details for identification
    fetch('tour-guide-registrations.php', {
        method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'},
        body:`action=get_registration&registration_id=${id}`
    })
    .then(r=>r.json())
    .then(data => {
        if (data && data.success && data.data) {
            const reg = data.data;
            const details = `
                <div style="display:grid;gap:8px;">
                    <div><strong>Name:</strong> ${reg.guide_name || (reg.last_name + ', ' + reg.first_name + ' ' + (reg.middle_initial || ''))}</div>
                    <div><strong>Email:</strong> ${reg.email || 'N/A'}</div>
                    <div><strong>Phone:</strong> ${reg.primary_phone || 'N/A'}</div>
                    <div><strong>Specialization:</strong> ${reg.guide_specialty || reg.specialization || 'N/A'}</div>
                    <div><strong>Status:</strong> ${ucfirst(reg.status || 'N/A')}</div>
                    <div><strong>Application Date:</strong> ${reg.application_date ? new Date(reg.application_date).toLocaleDateString() : 'N/A'}</div>
                </div>
            `;
            document.getElementById('deleteRegistrationDetails').innerHTML = details;
        }
    })
    .catch(error => console.error('Failed to fetch registration details:', error))
    .finally(() => {
        const btn = $id('deleteConfirmBtn');
        const nb = btn.cloneNode(true);
        btn.parentNode.replaceChild(nb, btn);
        
        nb.addEventListener('click', () => {
            // Validate justification
            const justification = document.getElementById('deleteRegistrationJustification').value.trim();
            if (!justification) {
                showError('Please provide a justification for deletion');
                document.getElementById('deleteRegistrationJustification').focus();
                return;
            }
            
            closeModal('deleteModal');
            fetch('tour-guide-registrations.php', {
                method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'},
                body:`action=delete_registration&registration_id=${id}&justification=${encodeURIComponent(justification)}`
            })
            .then(r=>r.json())
            .then(data => {
                if (data.success) showSuccess(data.message||'Deleted with justification logged!', ()=>location.reload());
                else showError(data.message||'Delete failed');
            })
            .catch(e=>showError('Error: '+e.message));
        });
        
        // Clear justification when opening modal
        document.getElementById('deleteRegistrationJustification').value = '';
        openModal('deleteModal');
    });
}

/* ── Copy password ── */
function copyPassword() {
    if (!window._generatedPw) return;
    navigator.clipboard.writeText(window._generatedPw).catch(()=>{});
}

/* ── Close on overlay click / Escape ── */
const allModals = ['viewRegistrationModal','confirmModal','successModal','errorModal','accountCreatedModal','deleteModal','documentPreviewModal','signOutModal'];
window.addEventListener('click', e => { allModals.forEach(id => { const m=$id(id); if(m&&e.target===m) closeModal(id); }); });
document.addEventListener('keydown', e => { if(e.key==='Escape') allModals.forEach(id=>closeModal(id)); });
</script>
</body>
</html>
<?php closeAdminConnection($conn); ?>