    <?php
// Destinations Management Module
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/auth.php';

function getAdminConnection() { return getDatabaseConnection(); }
function initAdminAuth() { requireAdmin(); return getCurrentUser(); }
function closeAdminConnection($conn) { closeDatabaseConnection($conn); }

function addTouristSpot($conn, $data) {
    try {
        $stmt = $conn->prepare("INSERT INTO tourist_spots (name, description, category, location, address, operating_hours, entrance_fee, duration, best_time_to_visit, activities, amenities, contact_info, website, image_url, rating, review_count, assigned_guide, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'active')");
        $stmt->bind_param("ssssssssssssssdisi", $data['name'], $data['description'], $data['category'], $data['location'], $data['address'], $data['operating_hours'], $data['entrance_fee'], $data['duration'], $data['best_time_to_visit'], $data['activities'], $data['amenities'], $data['contact_info'], $data['website'], $data['image_url'], $data['rating'], $data['review_count'], $data['assigned_guide']);
        return $stmt->execute() ? ['success' => true, 'message' => 'Tourist spot added successfully'] : ['success' => false, 'message' => 'Failed to add tourist spot'];
    } catch (Exception $e) { return ['success' => false, 'message' => 'Error: ' . $e->getMessage()]; }
}

function editTouristSpot($conn, $data) {
    try {
        $stmt = $conn->prepare("UPDATE tourist_spots SET name=?, description=?, category=?, location=?, address=?, operating_hours=?, entrance_fee=?, duration=?, best_time_to_visit=?, activities=?, amenities=?, contact_info=?, website=?, image_url=?, rating=?, review_count=?, assigned_guide=?, status=? WHERE id=?");
        $stmt->bind_param("ssssssssssssssdisisi", $data['name'], $data['description'], $data['category'], $data['location'], $data['address'], $data['operating_hours'], $data['entrance_fee'], $data['duration'], $data['best_time_to_visit'], $data['activities'], $data['amenities'], $data['contact_info'], $data['website'], $data['image_url'], $data['rating'], $data['review_count'], $data['assigned_guide'], $data['status'], $data['spot_id']);
        return $stmt->execute() ? ['success' => true, 'message' => 'Tourist spot updated successfully'] : ['success' => false, 'message' => 'Failed to update tourist spot'];
    } catch (Exception $e) { return ['success' => false, 'message' => 'Error: ' . $e->getMessage()]; }
}

function deleteTouristSpot($conn, $spotId, $justification = '') {
    try {
        // Log the deletion with justification
        $stmt = $conn->prepare("INSERT INTO deletion_log (item_type, item_id, justification, deleted_by, deleted_at) VALUES (?, ?, ?, ?, NOW())");
        $itemType = 'tourist_spot';
        $deletedBy = $_SESSION['user_id'] ?? 0;
        $stmt->bind_param("sisi", $itemType, $spotId, $justification, $deletedBy);
        $stmt->execute();
        
        // Proceed with deletion
        $stmt = $conn->prepare("DELETE FROM tourist_spots WHERE id = ?");
        $stmt->bind_param("i", $spotId);
        return $stmt->execute() ? ['success' => true, 'message' => 'Tourist spot deleted successfully with justification logged'] : ['success' => false, 'message' => 'Failed to delete tourist spot'];
    } catch (Exception $e) { return ['success' => false, 'message' => 'Error: ' . $e->getMessage()]; }
}

function getTouristSpot($conn, $spotId) {
    try {
        $stmt = $conn->prepare("SELECT * FROM tourist_spots WHERE id = ?");
        $stmt->bind_param("i", $spotId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->num_rows > 0 ? ['success' => true, 'data' => $result->fetch_assoc()] : ['success' => false, 'message' => 'Tourist spot not found'];
    } catch (Exception $e) { return ['success' => false, 'message' => 'Error: ' . $e->getMessage()]; }
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

function getCategoryIcon($category) {
    $icons = ['nature'=>'forest','historical'=>'account_balance','religious'=>'church','farm'=>'agriculture','park'=>'park','urban'=>'location_city'];
    return $icons[strtolower($category)] ?? 'place';
}

function getTouristSpotsList($conn, $page = 1, $limit = 15, $search = '') {
    if (!$conn) return ['spots'=>[],'pagination'=>['current_page'=>$page,'total_pages'=>0,'total_count'=>0,'limit'=>$limit]];
    $offset = ($page - 1) * $limit;
    $search = $conn->real_escape_string($search);
    $q = "SELECT ts.*, tg.name as guide_name, tg.specialty as guide_specialty FROM tourist_spots ts LEFT JOIN tour_guides tg ON ts.assigned_guide = tg.id WHERE 1=1";
    if ($search) $q .= " AND (ts.name LIKE '%$search%' OR ts.category LIKE '%$search%' OR ts.location LIKE '%$search%')";
    $q .= " ORDER BY ts.created_at DESC LIMIT $limit OFFSET $offset";
    $result = $conn->query($q);
    $cq = "SELECT COUNT(*) as total FROM tourist_spots WHERE 1=1";
    if ($search) $cq .= " AND (name LIKE '%$search%' OR category LIKE '%$search%' OR location LIKE '%$search%')";
    $cr = $conn->query($cq);
    $total = $cr ? $cr->fetch_assoc()['total'] : 0;
    $rows = [];
    if ($result) while ($row = $result->fetch_assoc()) $rows[] = $row;
    return ['spots'=>$rows,'pagination'=>['current_page'=>$page,'total_pages'=>ceil($total/$limit),'total_count'=>$total,'limit'=>$limit]];
}

function getTouristDetailFiles() {
    $dir = __DIR__ . '/../tourist-detail/';
    $files = [];
    if (is_dir($dir)) {
        foreach (scandir($dir) as $file) {
            if (pathinfo($file, PATHINFO_EXTENSION) === 'php') {
                $fp = $dir . $file;
                $files[] = ['filename'=>$file,'filepath'=>$fp,'data'=>extractTouristDataFromFile($fp)];
            }
        }
    }
    return $files;
}

function extractTouristDataFromFile($filePath) {
    $content = file_get_contents($filePath);
    $data = [];
    if (preg_match('/<h1 class="hero-title">([^<]+)<\/h1>/', $content, $m)) $data['name'] = trim($m[1]);
    elseif (preg_match('/<title>([^<]+) - San Jose del Monte Tourism<\/title>/', $content, $m)) $data['name'] = trim($m[1]);
    if (preg_match('/<p class="hero-subtitle">([^<]+)<\/p>/', $content, $m)) $data['subtitle'] = trim($m[1]);
    if (preg_match('/<h2 class="section-title">About [^<]+<\/h2>\s*<p class="description">([^<]+)<\/p>/', $content, $m)) $data['description'] = trim($m[1]);
    $data['category'] = categorizeFromFilename(basename($filePath, '.php'));
    $data['location'] = 'San Jose del Monte, Bulacan';
    $data['entrance_fee'] = extractEntranceFeeFromContent($content);
    $data['activities'] = extractActivitiesFromContent($content);
    $data['amenities'] = extractAmenitiesFromContent($content);
    $data['operating_hours'] = extractOperatingHoursFromContent($content);
    $data['best_time_to_visit'] = extractBestTimeFromContent($content);
    $data['rating'] = 0.0; $data['review_count'] = 0; $data['status'] = 'active';
    $data['image_url'] = ''; $data['contact_info'] = ''; $data['website'] = ''; $data['address'] = '';
    return $data;
}

function categorizeFromFilename($filename) {
    $map = ['falls'=>'nature','mt'=>'nature','farm'=>'farm','park'=>'park','shrine'=>'religious','grotto'=>'religious','monument'=>'historical'];
    foreach ($map as $kw => $cat) if (stripos($filename, $kw) !== false) return $cat;
    return 'nature';
}

function extractEntranceFeeFromContent($c) {
    if (preg_match('/₱\s*\d+/', $c, $m)) return $m[0];
    if (preg_match('/PHP\s*\d+/', $c, $m)) return $m[0];
    return 'Free';
}

function extractActivitiesFromContent($c) {
    $kws = ['hiking','swimming','photography','picnic','camping','nature tripping','sightseeing'];
    $out = [];
    foreach ($kws as $k) if (stripos($c,$k)!==false) $out[] = ucfirst($k);
    return implode(', ', $out);
}

function extractAmenitiesFromContent($c) {
    $kws = ['parking','restroom','shower','cottage','shed','store','restaurant'];
    $out = [];
    foreach ($kws as $k) if (stripos($c,$k)!==false) $out[] = ucfirst($k);
    return implode(', ', $out);
}

function extractOperatingHoursFromContent($c) {
    if (preg_match('/(\d{1,2}:\d{2}\s*(AM|PM|am|pm)\s*-\s*\d{1,2}:\d{2}\s*(AM|PM|am|pm))/', $c, $m)) return $m[1];
    return '6:00 AM - 6:00 PM';
}

function extractBestTimeFromContent($c) {
    if (stripos($c,'morning')!==false) return 'Morning';
    if (stripos($c,'summer')!==false||stripos($c,'dry season')!==false) return 'Dry season';
    return 'All year round';
}

// Init
$currentUser = initAdminAuth();
$conn = getAdminConnection();

// Get available tour guides for assignment
$availableGuides = [];
$result = $conn->query("SELECT id, name, specialty, category, rating FROM tour_guides WHERE status = 'active' OR status = 'approved' OR status = 'verified' ORDER BY name");
if ($result) while ($row = $result->fetch_assoc()) $availableGuides[] = $row;

$dbSettings = [];
$result = $conn->query("SELECT setting_key, setting_value FROM admin_dashboard_settings");
if ($result) while ($row = $result->fetch_assoc()) $dbSettings[$row['setting_key']] = $row['setting_value'];

$destSettings = [];
$result = $conn->query("SELECT setting_key, setting_value FROM destination_settings");
if ($result) while ($row = $result->fetch_assoc()) $destSettings[$row['setting_key']] = $row['setting_value'];

$moduleTitle    = $destSettings['module_title']    ?? 'Destinations Management';
$moduleSubtitle = $destSettings['module_subtitle'] ?? 'Manage tourist spots';

$adminInfo = ['role_title' => 'Administrator', 'admin_mark' => 'A'];
$stmt = $conn->prepare("SELECT admin_mark, role_title FROM admin_users WHERE user_id = ?");
$userId = $currentUser['id'];
$stmt->bind_param("i", $userId);
$stmt->execute();
if ($row = $stmt->get_result()->fetch_assoc()) $adminInfo = $row;
$stmt->close();
$adminMark = $adminInfo['admin_mark'] ?? 'A';

$menuItems = [];
$result = $conn->query("SELECT * FROM admin_menu_items WHERE is_active=1 ORDER BY display_order ASC");
if ($result) while ($row = $result->fetch_assoc()) $menuItems[] = $row;

$stats = getAdminStats($conn);

    $queryValues = [
        'totalUsers'        => $stats['totalUsers'],
        'totalBookings'     => $stats['totalBookings'],
        'totalGuides'       => $stats['totalGuides'],
        'totalDestinations' => $stats['totalDestinations']
    ];

    $spots = [];
    $result = $conn->query("SELECT ts.*, tg.name as guide_name, tg.specialty as guide_specialty FROM tourist_spots ts LEFT JOIN tour_guides tg ON ts.assigned_guide = tg.id ORDER BY ts.name ASC");
    if ($result) while ($row = $result->fetch_assoc()) $spots[] = $row;

    $categories = array_unique(array_column($spots, 'category'));
    sort($categories);

    $search = isset($_GET['search']) ? $_GET['search'] : '';

    // AJAX
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Start output buffering to catch any unwanted output
        ob_start();
        
        header('Content-Type: application/json');
        ini_set('display_errors', 0);
        $action = $_POST['action'] ?? '';
        
        switch ($action) {
            case 'add_spot':    echo json_encode(addTouristSpot($conn, $_POST)); break;
            case 'edit_spot':   echo json_encode(editTouristSpot($conn, $_POST)); break;
            case 'delete_spot': echo json_encode(deleteTouristSpot($conn, $_POST['spot_id'], $_POST['justification'] ?? '')); break;
            case 'get_spot':    echo json_encode(getTouristSpot($conn, $_POST['spot_id'])); break;
            default:            echo json_encode(['success' => false, 'message' => 'Invalid action']); break;
        }
        
        // Clean any unwanted output and send clean JSON
        $output = ob_get_contents();
        ob_end_clean();
        
        // Remove any whitespace or warnings before JSON
        $cleanOutput = ltrim($output);
        if (strpos($cleanOutput, '{') === 0 || strpos($cleanOutput, '[') === 0) {
            echo $cleanOutput;
        } else {
            // If output is corrupted, send a clean error response
            echo json_encode(['success' => false, 'message' => 'Server error occurred']);
        }
        exit;
    }
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Destinations Management | SJDM Tours Admin</title>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
        <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet">
        <link rel="stylesheet" href="admin-styles.css">
        <style>
            /* ── Compact Stats ── */
            .um-stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(170px, 1fr)); gap: 14px; margin-bottom: 24px; }
            .stat-card-compact { background: white; border-radius: 14px; padding: 16px 18px; box-shadow: 0 2px 12px rgba(0,0,0,.07); border: 1px solid rgba(0,0,0,.06); transition: transform .25s, box-shadow .25s; display: flex; flex-direction: column; }
            .stat-card-compact:hover { transform: translateY(-3px); box-shadow: 0 8px 24px rgba(0,0,0,.12); }
            .stat-card-compact[data-stat="total"]   { border-top: 3px solid #ec4899; background: #fdf5fb; }
            .stat-card-compact[data-stat="active"]  { border-top: 3px solid #10b981; background: #f5fdf9; }
            .stat-card-compact[data-stat="cats"]    { border-top: 3px solid #667eea; background: #fafbff; }
            .scc-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 10px; }
            .scc-label { display: flex; align-items: center; gap: 5px; font-size: .68rem; font-weight: 700; text-transform: uppercase; letter-spacing: .8px; color: #6b7280; }
            .scc-label .material-icons-outlined { font-size: 14px; color: #9ca3af; }
            .scc-dot { width: 9px; height: 9px; border-radius: 50%; flex-shrink: 0; }
            .dot-pink  { background: #ec4899; }
            .dot-green { background: #10b981; }
            .dot-blue  { background: #667eea; }
            .scc-number { font-size: 2rem; font-weight: 800; color: #111827; line-height: 1; margin-bottom: 10px; }
            .scc-trend { display: inline-flex; align-items: center; gap: 3px; font-size: .72rem; font-weight: 700; padding: 3px 8px; border-radius: 20px; width: fit-content; }
            .scc-trend.positive { color: #059669; background: rgba(16,185,129,.12); }
            .scc-trend.neutral  { color: #6b7280; background: rgba(107,114,128,.1); }
            .scc-trend .material-icons-outlined { font-size: 13px; }

            /* ── Search Bar ── */
            .search-bar { display: flex; gap: 10px; margin-bottom: 16px; align-items: center; }
            .search-bar input { flex: 1; padding: 10px 16px; border: 1px solid #e5e7eb; border-radius: 10px; font-size: .9rem; outline: none; font-family: inherit; transition: border-color .2s; }
            .search-bar input:focus { border-color: #667eea; box-shadow: 0 0 0 3px rgba(102,126,234,.1); }

            /* ── Category filter pills ── */
            .category-filters { display: flex; gap: 8px; margin-bottom: 20px; flex-wrap: wrap; }
            .category-btn { display: flex; align-items: center; gap: 6px; padding: 8px 14px; border: 1px solid #e5e7eb; background: white; border-radius: 20px; cursor: pointer; font-size: .82rem; font-weight: 600; color: #6b7280; transition: all .2s; font-family: inherit; }
            .category-btn .material-icons-outlined { font-size: 16px; }
            .category-btn:hover { border-color: #667eea; color: #667eea; background: rgba(102,126,234,.06); }
            .category-btn.active { background: #667eea; border-color: #667eea; color: white; }
            .category-count { background: rgba(0,0,0,.1); padding: 1px 6px; border-radius: 10px; font-size: .72rem; font-weight: 700; }
            .category-btn.active .category-count { background: rgba(255,255,255,.25); }
            tr[data-category].hidden { display: none; }

            /* ── Destination image in table ── */
            .destination-image { width: 52px; height: 42px; border-radius: 10px; object-fit: cover; border: 1px solid #e5e7eb; display: flex; align-items: center; justify-content: center; background: #f3f4f6; color: #9ca3af; }
            .destination-image .material-icons-outlined { font-size: 22px; }

            /* ── Category pill in table ── */
            .cat-pill { background: #f3f4f6; padding: 3px 10px; border-radius: 8px; font-size: .78rem; font-weight: 600; color: #374151; text-transform: capitalize; }

            /* ── Rating ── */
            .rating { font-size: .875rem; font-weight: 600; color: #374151; }
            .rating small { color: #9ca3af; font-size: .75rem; }

            /* ── Action buttons ── */
            .action-buttons { display: flex; gap: 6px; }
            .btn-icon { width: 32px; height: 32px; border: 1px solid #e5e7eb; border-radius: 8px; background: white; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: all .2s; color: #6b7280; }
            .btn-icon:hover { background: #f3f4f6; color: #111827; border-color: #d1d5db; }
            .btn-icon.edit:hover   { background: #eff6ff; color: #2563eb; border-color: #bfdbfe; }
            .btn-icon.del:hover    { background: #fef2f2; color: #dc2626; border-color: #fecaca; }
            .btn-icon .material-icons-outlined { font-size: 16px; }

            /* ── Add Destination button in top bar ── */
            .btn-primary { background: linear-gradient(135deg,#667eea,#764ba2); color: white; border: none; padding: 10px 18px; border-radius: 10px; font-size: .85rem; font-weight: 600; cursor: pointer; transition: all .2s; display: inline-flex; align-items: center; gap: 6px; font-family: inherit; }
            .btn-primary:hover { opacity: .9; transform: translateY(-1px); box-shadow: 0 4px 12px rgba(102,126,234,.35); }

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
            .form-group input, .form-group select, .form-group textarea {
                width: 100%; padding: 10px 14px; border: 1px solid #e5e7eb; border-radius: 10px; font-size: .875rem; font-family: inherit; outline: none; transition: border-color .2s; box-sizing: border-box;
            }
            .form-group input:focus, .form-group select:focus, .form-group textarea:focus { border-color: #667eea; box-shadow: 0 0 0 3px rgba(102,126,234,.1); }
            .form-group textarea { resize: vertical; }

            .btn-submit { background: linear-gradient(135deg,#667eea,#764ba2); color: white; border: none; padding: 10px 20px; border-radius: 10px; font-size: .875rem; font-weight: 600; cursor: pointer; transition: all .2s; font-family: inherit; }
            .btn-submit:hover { opacity: .9; transform: translateY(-1px); }
            .btn-cancel { background: #f3f4f6; color: #374151; border: 1px solid #e5e7eb; padding: 10px 20px; border-radius: 10px; font-size: .875rem; font-weight: 600; cursor: pointer; transition: all .2s; font-family: inherit; }
            .btn-cancel:hover { background: #e5e7eb; }
            .btn-danger-solid { background: #ef4444; color: white; border: none; padding: 10px 20px; border-radius: 10px; font-size: .875rem; font-weight: 600; cursor: pointer; transition: all .2s; font-family: inherit; }
            .btn-danger-solid:hover { background: #dc2626; }

            /* ── View Destination Modal styles ── */
            .dest-view-hero { display: grid; grid-template-columns: 160px 1fr; gap: 16px; align-items: center; padding: 16px; background: linear-gradient(135deg,rgba(236,72,153,.05),rgba(102,126,234,.08)); border: 1px solid rgba(236,72,153,.1); border-radius: 14px; margin-bottom: 20px; }
            .dest-view-image { width: 160px; height: 120px; border-radius: 12px; overflow: hidden; background: #f3f4f6; display: flex; align-items: center; justify-content: center; border: 1px solid #e5e7eb; flex-shrink: 0; }
            .dest-view-image img { width: 100%; height: 100%; object-fit: cover; }
            .dest-view-image .material-icons-outlined { font-size: 40px; color: #9ca3af; }
            .dest-view-title { font-size: 1.3rem; font-weight: 800; margin: 0 0 4px; color: #111827; }
            .dest-view-location { margin: 0 0 10px; color: #6b7280; font-size: .875rem; font-weight: 500; display: flex; align-items: center; gap: 5px; }
            .dest-view-location .material-icons-outlined { font-size: 16px; }
            .dest-view-badges { display: flex; gap: 8px; flex-wrap: wrap; align-items: center; }
            .dest-pill { display: inline-flex; align-items: center; padding: 4px 10px; border-radius: 20px; font-size: .72rem; font-weight: 700; background: rgba(102,126,234,.12); color: #4f46e5; text-transform: capitalize; }

            .dest-view-grid { display: grid; grid-template-columns: 1fr; gap: 14px; }
            .dest-view-section { background: white; border: 1px solid #f3f4f6; border-radius: 14px; padding: 16px; box-shadow: 0 1px 6px rgba(0,0,0,.04); }
            .dest-view-section h4 { margin: 0 0 12px; display: flex; align-items: center; gap: 8px; font-size: .82rem; font-weight: 700; text-transform: uppercase; letter-spacing: .6px; color: #667eea; }
            .dest-view-section h4 .material-icons-outlined { font-size: 17px; }
            .dest-view-text { margin: 0; color: #374151; line-height: 1.65; font-size: .875rem; white-space: pre-wrap; }
            .dest-view-kv { display: grid; grid-template-columns: repeat(2,1fr); gap: 10px; }
            .dest-view-k { display: flex; gap: 10px; padding: 12px; border-radius: 10px; background: #f9fafb; border: 1px solid #f3f4f6; }
            .dest-view-k .material-icons-outlined { color: #667eea; font-size: 18px; flex-shrink: 0; }
            .dest-view-k-label { font-size: .7rem; font-weight: 700; color: #9ca3af; text-transform: uppercase; letter-spacing: .5px; margin-bottom: 2px; }
            .dest-view-k-value { font-size: .875rem; color: #111827; font-weight: 600; }
            .dest-view-contact { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }
            .dest-view-contact-item { display: flex; align-items: center; gap: 10px; padding: 12px; border-radius: 10px; background: #f9fafb; border: 1px solid #f3f4f6; font-size: .875rem; font-weight: 600; color: #374151; overflow: hidden; }
            .dest-view-contact-item .material-icons-outlined { color: #667eea; font-size: 18px; flex-shrink: 0; }
            .dest-view-contact-item a { color: #667eea; text-decoration: none; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
            .dest-view-rating { display: flex; align-items: baseline; gap: 8px; }
            .dest-view-rating-value { font-size: 2rem; font-weight: 900; color: #111827; }
            .dest-view-rating-sub { color: #9ca3af; font-size: .875rem; font-weight: 600; }

            /* ── Delete modal centered ── */
            .modal-centered { text-align: center; padding: 8px 0 16px; }
            .modal-icon { width: 64px; height: 64px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px; }
            .modal-icon .material-icons-outlined { font-size: 32px; color: white; }
            .modal-icon.red { background: linear-gradient(135deg,#ef4444,#dc2626); }
            .modal-centered p { font-size: 1rem; color: #374151; margin: 0; font-weight: 500; }

            /* ── Toast feedback ── */
            .toast-wrap { position: fixed; bottom: 24px; right: 24px; z-index: 9999; display: flex; flex-direction: column; gap: 10px; }
            .toast { display: flex; align-items: center; gap: 10px; padding: 12px 16px; border-radius: 12px; font-size: .875rem; font-weight: 600; min-width: 260px; box-shadow: 0 4px 20px rgba(0,0,0,.12); transform: translateX(120%); opacity: 0; transition: all .3s ease; }
            .toast.show { transform: translateX(0); opacity: 1; }
            .toast.success { background: #f0fdf4; border: 1px solid #bbf7d0; color: #15803d; }
            .toast.error   { background: #fef2f2; border: 1px solid #fecaca; color: #b91c1c; }
            .toast .material-icons-outlined { font-size: 20px; }

            /* ── Sign Out Modal ── */
            .signout-content { display: flex; flex-direction: column; align-items: center; gap: 20px; padding: 16px 0; text-align: center; }
            .signout-icon { width: 72px; height: 72px; border-radius: 50%; background: linear-gradient(135deg,#ef4444,#dc2626); color: white; display: flex; align-items: center; justify-content: center; box-shadow: 0 8px 24px rgba(239,68,68,.3); }
            .signout-icon .material-icons-outlined { font-size: 36px; }
            .signout-message h3 { margin: 0 0 6px; font-size: 1.1rem; font-weight: 700; color: #111827; }
            .signout-message p  { margin: 0; font-size: .875rem; color: #6b7280; }

            @media (max-width: 640px) {
                .um-stats-grid { grid-template-columns: repeat(2,1fr); }
                .form-row { grid-template-columns: 1fr; }
                .dest-view-hero { grid-template-columns: 1fr; }
                .dest-view-kv, .dest-view-contact { grid-template-columns: 1fr; }
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
                    <button class="btn-primary" onclick="openModal('addDestinationModal')">
                        <span class="material-icons-outlined" style="font-size:18px;">add</span>
                        Add Destination
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
                    <div class="stat-card-compact" data-stat="total">
                        <div class="scc-header">
                            <div class="scc-label"><span class="material-icons-outlined">place</span> Total Destinations</div>
                            <span class="scc-dot dot-pink"></span>
                        </div>
                        <div class="scc-number"><?php echo $stats['totalDestinations']; ?></div>
                        <div class="scc-trend positive"><span class="material-icons-outlined">north_east</span><span>All spots</span></div>
                    </div>
                    <div class="stat-card-compact" data-stat="active">
                        <div class="scc-header">
                            <div class="scc-label"><span class="material-icons-outlined">check_circle</span> Active</div>
                            <span class="scc-dot dot-green"></span>
                        </div>
                        <div class="scc-number"><?php echo count(array_filter($spots, fn($s) => $s['status']=='active')); ?></div>
                        <div class="scc-trend positive"><span class="material-icons-outlined">north_east</span><span>Published</span></div>
                    </div>
                    <div class="stat-card-compact" data-stat="cats">
                        <div class="scc-header">
                            <div class="scc-label"><span class="material-icons-outlined">category</span> Categories</div>
                            <span class="scc-dot dot-blue"></span>
                        </div>
                        <div class="scc-number"><?php echo count($categories); ?></div>
                        <div class="scc-trend neutral"><span class="material-icons-outlined">apps</span><span>Types</span></div>
                    </div>
                </div>

                <!-- Search -->
                <div class="search-bar">
                    <input type="text" id="searchInput" placeholder="Search destinations by name, category, or location..." value="<?php echo htmlspecialchars($search); ?>">
                    <button class="btn-cancel" onclick="searchDestinations()">
                        <span class="material-icons-outlined" style="font-size:16px;vertical-align:middle;">search</span> Search
                    </button>
                    <button class="btn-cancel" onclick="clearSearch()">
                        <span class="material-icons-outlined" style="font-size:16px;vertical-align:middle;">clear</span> Clear
                    </button>
                </div>

                <!-- Category Filter Pills -->
                <div class="category-filters">
                    <button class="category-btn active" onclick="showCategory('all')" data-category="all">
                        <span class="material-icons-outlined">apps</span>
                        All
                        <span class="category-count"><?php echo count($spots); ?></span>
                    </button>
                    <?php foreach ($categories as $cat): ?>
                    <button class="category-btn" onclick="showCategory('<?php echo strtolower($cat); ?>')" data-category="<?php echo strtolower($cat); ?>">
                        <span class="material-icons-outlined"><?php echo getCategoryIcon($cat); ?></span>
                        <?php echo ucfirst($cat); ?>
                        <span class="category-count"><?php echo count(array_filter($spots, fn($s) => strtolower($s['category'])===strtolower($cat))); ?></span>
                    </button>
                    <?php endforeach; ?>
                </div>

                <!-- Table -->
                <div class="table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Image</th>
                                <th>Name</th>
                                <th>Category</th>
                                <th>Location</th>
                                <th>Assigned Guide</th>
                                <th>Rating</th>
                                <th>Entrance Fee</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($spots as $spot): ?>
                            <tr data-category="<?php echo strtolower($spot['category']); ?>">
                                <td>
                                    <?php if ($spot['image_url']): ?>
                                        <img src="<?php echo htmlspecialchars($spot['image_url']); ?>" alt="<?php echo htmlspecialchars($spot['name']); ?>" class="destination-image">
                                    <?php else: ?>
                                        <div class="destination-image"><span class="material-icons-outlined">place</span></div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <strong style="color:#111827;"><?php echo htmlspecialchars($spot['name']); ?></strong><br>
                                    <small style="color:#9ca3af;"><?php echo htmlspecialchars(substr($spot['description'],0,80)); ?>...</small>
                                </td>
                                <td><span class="cat-pill"><?php echo htmlspecialchars($spot['category']); ?></span></td>
                                <td style="color:#6b7280;font-size:.875rem;"><?php echo htmlspecialchars($spot['location']); ?></td>
                                <td>
                                    <?php if ($spot['guide_name']): ?>
                                        <div style="display:flex;align-items:center;gap:6px;">
                                            <div style="width:28px;height:28px;border-radius:50%;background:#667eea;color:white;display:flex;align-items:center;justify-content:center;font-size:.7rem;font-weight:700;flex-shrink:0;">
                                                <?php echo strtoupper(substr($spot['guide_name'], 0, 1)); ?>
                                            </div>
                                            <div>
                                                <div style="font-size:.8rem;font-weight:600;color:#111827;"><?php echo htmlspecialchars($spot['guide_name']); ?></div>
                                                <div style="font-size:.7rem;color:#9ca3af;"><?php echo htmlspecialchars($spot['guide_specialty'] ?? 'Guide'); ?></div>
                                            </div>
                                        </div>
                                    <?php else: ?>
                                        <span style="color:#9ca3af;font-size:.8rem;font-style:italic;">Not assigned</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="rating">
                                        <?php echo number_format($spot['rating'],1); ?> ⭐
                                        <small>(<?php echo $spot['review_count']; ?>)</small>
                                    </div>
                                </td>
                                <td style="font-size:.875rem;font-weight:600;color:#374151;"><?php echo $spot['entrance_fee']; ?></td>
                                <td><span class="status-badge status-<?php echo $spot['status']; ?>"><?php echo ucfirst($spot['status']); ?></span></td>
                                <td>
                                    <div class="action-buttons">
                                        <button class="btn-icon" onclick="viewDestination(<?php echo $spot['id']; ?>)" title="View">
                                            <span class="material-icons-outlined">visibility</span>
                                        </button>
                                        <button class="btn-icon edit" onclick="editDestination(<?php echo $spot['id']; ?>)" title="Edit">
                                            <span class="material-icons-outlined">edit</span>
                                        </button>
                                        <button class="btn-icon del" onclick="deleteDestination(<?php echo $spot['id']; ?>)" title="Delete">
                                            <span class="material-icons-outlined">delete</span>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php if (empty($spots)): ?>
                            <tr><td colspan="9" style="text-align:center;padding:48px;color:#9ca3af;">
                                <span class="material-icons-outlined" style="font-size:44px;display:block;margin-bottom:10px;color:#d1d5db;">place</span>
                                No destinations found
                            </td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

            </div><!-- /content-area -->
        </main>
    </div><!-- /admin-container -->

    <!-- Toast container -->
    <div class="toast-wrap" id="toastWrap"></div>

    <!-- ═══ ADD DESTINATION MODAL ═══ -->
    <div id="addDestinationModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Add New Destination</h2>
                <button class="modal-close" onclick="closeModal('addDestinationModal')"><span class="material-icons-outlined">close</span></button>
            </div>
            <form id="addDestinationForm" onsubmit="handleAddDestination(event)">
                <div class="modal-body">
                    <div class="form-row">
                        <div class="form-group"><label>Name *</label><input type="text" name="name" required></div>
                        <div class="form-group"><label>Category *</label>
                            <select name="category" required>
                                <option value="">Select Category</option>
                                <option value="nature">Nature</option>
                                <option value="historical">Historical</option>
                                <option value="religious">Religious</option>
                                <option value="farm">Farm</option>
                                <option value="park">Park</option>
                                <option value="urban">Urban</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group"><label>Description</label><textarea name="description" rows="3"></textarea></div>
                    <div class="form-row">
                        <div class="form-group"><label>Location *</label><input type="text" name="location" required></div>
                        <div class="form-group"><label>Address</label><input type="text" name="address"></div>
                    </div>
                    <div class="form-row">
                        <div class="form-group"><label>Operating Hours</label><input type="text" name="operating_hours" placeholder="e.g., 8:00 AM - 5:00 PM"></div>
                        <div class="form-group"><label>Entrance Fee</label><input type="text" name="entrance_fee" placeholder="e.g., ₱100 or Free"></div>
                    </div>
                    <div class="form-row">
                        <div class="form-group"><label>Duration</label><input type="text" name="duration" placeholder="e.g., 2-3 hours"></div>
                        <div class="form-group"><label>Best Time to Visit</label><input type="text" name="best_time_to_visit" placeholder="e.g., Morning, Dry season"></div>
                    </div>
                    <div class="form-group"><label>Activities</label><textarea name="activities" rows="2" placeholder="e.g., Hiking, Photography, Swimming"></textarea></div>
                    <div class="form-group"><label>Amenities</label><textarea name="amenities" rows="2" placeholder="e.g., Parking, Restrooms, Food stalls"></textarea></div>
                    <div class="form-row">
                        <div class="form-group"><label>Contact Info</label><input type="text" name="contact_info" placeholder="Phone number or email"></div>
                        <div class="form-group"><label>Website</label><input type="url" name="website" placeholder="https://example.com"></div>
                    </div>
                    <div class="form-group"><label>Image URL</label><input type="url" name="image_url" placeholder="https://example.com/image.jpg"></div>
                    <div class="form-row">
                        <div class="form-group"><label>Rating</label><input type="number" name="rating" min="0" max="5" step="0.1" value="0"></div>
                        <div class="form-group"><label>Review Count</label><input type="number" name="review_count" min="0" value="0"></div>
                    </div>
                    <div class="form-group"><label>Assigned Tour Guide</label>
                        <select name="assigned_guide">
                            <option value="">Select a tour guide (optional)</option>
                            <?php foreach ($availableGuides as $guide): ?>
                                <option value="<?php echo $guide['id']; ?>">
                                    <?php echo htmlspecialchars($guide['name']); ?> - <?php echo htmlspecialchars($guide['specialty'] ?? 'General Guide'); ?> (<?php echo number_format($guide['rating'], 1); ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-cancel" onclick="closeModal('addDestinationModal')">Cancel</button>
                    <button type="submit" class="btn-submit" id="addSubmitBtn">Add Destination</button>
                </div>
            </form>
        </div>
    </div>

    <!-- ═══ VIEW DESTINATION MODAL ═══ -->
    <div id="viewDestinationModal" class="modal">
        <div class="modal-content wide">
            <div class="modal-header">
                <h2>Destination Details</h2>
                <button class="modal-close" onclick="closeModal('viewDestinationModal')"><span class="material-icons-outlined">close</span></button>
            </div>
            <div class="modal-body" id="viewDestBody" style="max-height:65vh;overflow-y:auto;"></div>
            <div class="modal-footer">
                <button class="btn-cancel" onclick="closeModal('viewDestinationModal')">Close</button>
                <button class="btn-submit" onclick="editDestinationFromView()">
                    <span class="material-icons-outlined" style="font-size:17px;vertical-align:middle;">edit</span> Edit
                </button>
            </div>
        </div>
    </div>

    <!-- ═══ EDIT DESTINATION MODAL ═══ -->
    <div id="editDestinationModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Edit Destination</h2>
                <button class="modal-close" onclick="closeModal('editDestinationModal')"><span class="material-icons-outlined">close</span></button>
            </div>
            <form id="editDestinationForm" onsubmit="handleEditDestination(event)">
                <input type="hidden" name="action" value="edit_spot">
                <input type="hidden" id="editSpotId" name="spot_id">
                <div class="modal-body">
                    <div class="form-row">
                        <div class="form-group"><label>Name *</label><input type="text" id="editDestName" name="name" required></div>
                        <div class="form-group"><label>Category *</label>
                            <select id="editDestCategory" name="category" required>
                                <option value="">Select Category</option>
                                <option value="nature">Nature</option>
                                <option value="historical">Historical</option>
                                <option value="religious">Religious</option>
                                <option value="farm">Farm</option>
                                <option value="park">Park</option>
                                <option value="urban">Urban</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group"><label>Description</label><textarea id="editDestDescription" name="description" rows="3"></textarea></div>
                    <div class="form-row">
                        <div class="form-group"><label>Location *</label><input type="text" id="editDestLocation" name="location" required></div>
                        <div class="form-group"><label>Address</label><input type="text" id="editDestAddress" name="address"></div>
                    </div>
                    <div class="form-row">
                        <div class="form-group"><label>Operating Hours</label><input type="text" id="editDestOperatingHours" name="operating_hours"></div>
                        <div class="form-group"><label>Entrance Fee</label><input type="text" id="editDestEntranceFee" name="entrance_fee"></div>
                    </div>
                    <div class="form-row">
                        <div class="form-group"><label>Duration</label><input type="text" id="editDestDuration" name="duration"></div>
                        <div class="form-group"><label>Best Time to Visit</label><input type="text" id="editDestBestTime" name="best_time_to_visit"></div>
                    </div>
                    <div class="form-group"><label>Activities</label><textarea id="editDestActivities" name="activities" rows="2"></textarea></div>
                    <div class="form-group"><label>Amenities</label><textarea id="editDestAmenities" name="amenities" rows="2"></textarea></div>
                    <div class="form-row">
                        <div class="form-group"><label>Contact Info</label><input type="text" id="editDestContact" name="contact_info"></div>
                        <div class="form-group"><label>Website</label><input type="url" id="editDestWebsite" name="website"></div>
                    </div>
                    <div class="form-group"><label>Image URL</label><input type="url" id="editDestImageUrl" name="image_url"></div>
                    <div class="form-row">
                        <div class="form-group"><label>Rating</label><input type="number" id="editDestRating" name="rating" min="0" max="5" step="0.1" value="0"></div>
                        <div class="form-group"><label>Review Count</label><input type="number" id="editDestReviewCount" name="review_count" min="0" value="0"></div>
                    </div>
                    <div class="form-group"><label>Assigned Tour Guide</label>
                        <select id="editDestAssignedGuide" name="assigned_guide">
                            <option value="">Select a tour guide (optional)</option>
                            <?php foreach ($availableGuides as $guide): ?>
                                <option value="<?php echo $guide['id']; ?>">
                                    <?php echo htmlspecialchars($guide['name']); ?> - <?php echo htmlspecialchars($guide['specialty'] ?? 'General Guide'); ?> (<?php echo number_format($guide['rating'], 1); ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group"><label>Status</label>
                        <select id="editDestStatus" name="status">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-cancel" onclick="closeModal('editDestinationModal')">Cancel</button>
                    <button type="submit" class="btn-submit" id="editSubmitBtn">Update Destination</button>
                </div>
            </form>
        </div>
    </div>

    <!-- ═══ DELETE MODAL ═══ -->
    <div id="deleteDestinationModal" class="modal">
        <div class="modal-content small">
            <div class="modal-header">
                <h2>Delete Destination</h2>
                <button class="modal-close" onclick="closeModal('deleteDestinationModal')"><span class="material-icons-outlined">close</span></button>
            </div>
            <div class="modal-body">
                <div class="modal-centered">
                    <div class="modal-icon red"><span class="material-icons-outlined">warning</span></div>
                    <p>Are you sure you want to delete <strong id="deleteSpotName"></strong>? <br><small style="color:#9ca3af;">This cannot be undone.</small></p>
                </div>
                
                <!-- Item Identification Section -->
                <div style="margin-top:20px;padding:16px;background:#f9fafb;border-radius:10px;border:1px solid #e5e7eb;">
                    <h4 style="margin:0 0 12px;font-size:.9rem;font-weight:600;color:#374151;display:flex;align-items:center;gap:6px;">
                        <span class="material-icons-outlined" style="font-size:18px;color:#667eea;">place</span>
                        Destination to be Deleted
                    </h4>
                    <div id="deleteSpotDetails" style="font-size:.85rem;color:#6b7280;"></div>
                </div>
                
                <!-- Justification Section -->
                <div style="margin-top:16px;">
                    <label for="deleteSpotJustification" style="display:block;margin-bottom:6px;font-size:.82rem;font-weight:600;color:#374151;">
                        <span class="material-icons-outlined" style="font-size:16px;vertical-align:middle;margin-right:4px;color:#ef4444;">assignment_late</span>
                        Justification for Deletion <span style="color:#ef4444;">*</span>
                    </label>
                    <textarea id="deleteSpotJustification" name="justification" rows="3" placeholder="Please provide a specific reason why this destination must be deleted..." required
                        style="width:100%;padding:10px 14px;border:1px solid #e5e7eb;border-radius:10px;font-size:.875rem;font-family:inherit;outline:none;transition:border-color .2s;box-sizing:border-box;resize:vertical;"></textarea>
                    <div style="margin-top:6px;font-size:.75rem;color:#6b7280;">
                        Examples: Permanently closed, duplicate listing, inaccurate information, safety concerns, etc.
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn-cancel" onclick="closeModal('deleteDestinationModal')">Cancel</button>
                <button class="btn-danger-solid" id="deleteConfirmBtn">Delete</button>
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
    /* ── Core ── */
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

    /* ── Category filter ── */
    function showCategory(cat) {
        document.querySelectorAll('.category-btn').forEach(b=>b.classList.remove('active'));
        document.querySelector(`[data-category="${cat}"]`).classList.add('active');
        document.querySelectorAll('tr[data-category]').forEach(r => {
            r.classList.toggle('hidden', cat!=='all' && r.dataset.category!==cat);
        });
    }

    /* ── Search ── */
    function searchDestinations() {
        const q = $id('searchInput').value;
        const url = new URL(window.location);
        q ? url.searchParams.set('search',q) : url.searchParams.delete('search');
        window.location.href = url;
    }
    function clearSearch() {
        const url = new URL(window.location);
        url.searchParams.delete('search');
        window.location.href = url;
    }
    $id('searchInput').addEventListener('keypress', e=>{ if(e.key==='Enter') searchDestinations(); });

    /* ── View ── */
    let currentViewSpotId = null;

    function viewDestination(spotId) {
        fetch('destinations.php', { method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'}, body:`action=get_spot&spot_id=${spotId}` })
        .then(r=>r.json())
        .then(data => {
            if (!data.success) { showToast(data.message||'Failed to load', 'error'); return; }
            currentViewSpotId = data.data.id;
            const d = data.data;
            const imgHtml = d.image_url
                ? `<img src="${escH(d.image_url)}" alt="${escH(d.name)}">`
                : `<span class="material-icons-outlined">place</span>`;
            const sc = d.status||'inactive';
            $id('viewDestBody').innerHTML = `
                <div class="dest-view-hero">
                    <div class="dest-view-image">${imgHtml}</div>
                    <div>
                        <h3 class="dest-view-title">${escH(d.name)}</h3>
                        <p class="dest-view-location"><span class="material-icons-outlined">place</span>${escH(d.location)}</p>
                        <div class="dest-view-badges">
                            <span class="status-badge status-${sc}">${sc.charAt(0).toUpperCase()+sc.slice(1)}</span>
                            <span class="dest-pill">${escH(d.category)}</span>
                        </div>
                    </div>
                </div>
                <div class="dest-view-grid">
                    <div class="dest-view-section">
                        <h4><span class="material-icons-outlined">info</span> Overview</h4>
                        <div class="dest-view-kv">
                            <div class="dest-view-k"><span class="material-icons-outlined">schedule</span><div><div class="dest-view-k-label">Operating Hours</div><div class="dest-view-k-value">${escH(d.operating_hours||'N/A')}</div></div></div>
                            <div class="dest-view-k"><span class="material-icons-outlined">payments</span><div><div class="dest-view-k-label">Entrance Fee</div><div class="dest-view-k-value">${escH(d.entrance_fee||'N/A')}</div></div></div>
                            <div class="dest-view-k"><span class="material-icons-outlined">timelapse</span><div><div class="dest-view-k-label">Duration</div><div class="dest-view-k-value">${escH(d.duration||'N/A')}</div></div></div>
                            <div class="dest-view-k"><span class="material-icons-outlined">wb_sunny</span><div><div class="dest-view-k-label">Best Time</div><div class="dest-view-k-value">${escH(d.best_time_to_visit||'N/A')}</div></div></div>
                        </div>
                    </div>
                    <div class="dest-view-section"><h4><span class="material-icons-outlined">description</span> Description</h4><p class="dest-view-text">${escH(d.description||'No description available.')}</p></div>
                    <div class="dest-view-section"><h4><span class="material-icons-outlined">local_activity</span> Activities</h4><p class="dest-view-text">${escH(d.activities||'N/A')}</p></div>
                    <div class="dest-view-section"><h4><span class="material-icons-outlined">room_service</span> Amenities</h4><p class="dest-view-text">${escH(d.amenities||'N/A')}</p></div>
                    <div class="dest-view-section">
                        <h4><span class="material-icons-outlined">contact_phone</span> Contact</h4>
                        <div class="dest-view-contact">
                            <div class="dest-view-contact-item"><span class="material-icons-outlined">call</span>${escH(d.contact_info||'N/A')}</div>
                            <div class="dest-view-contact-item"><span class="material-icons-outlined">public</span>${d.website?`<a href="${escH(d.website)}" target="_blank" rel="noopener">${escH(d.website)}</a>`:'N/A'}</div>
                        </div>
                    </div>
                    <div class="dest-view-section">
                        <h4><span class="material-icons-outlined">star</span> Rating</h4>
                        <div class="dest-view-rating">
                            <div class="dest-view-rating-value">${parseFloat(d.rating||0).toFixed(1)} ⭐</div>
                            <div class="dest-view-rating-sub">(${parseInt(d.review_count||0)} reviews)</div>
                        </div>
                    </div>
                </div>
            `;
            openModal('viewDestinationModal');
        })
        .catch(e=>showToast('Error: '+e.message,'error'));
    }

    function editDestinationFromView() {
        if (!currentViewSpotId) return;
        closeModal('viewDestinationModal');
        setTimeout(() => editDestination(currentViewSpotId), 150);
    }

    /* ── Edit ── */
    function editDestination(spotId) {
        fetch('destinations.php', { method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'}, body:`action=get_spot&spot_id=${spotId}` })
        .then(r=>r.json())
        .then(data => {
            if (!data.success) { showToast(data.message||'Failed to load','error'); return; }
            const d = data.data;
            $id('editSpotId').value             = d.id;
            $id('editDestName').value           = d.name||'';
            $id('editDestCategory').value       = d.category||'';
            $id('editDestDescription').value    = d.description||'';
            $id('editDestLocation').value       = d.location||'';
            $id('editDestAddress').value        = d.address||'';
            $id('editDestOperatingHours').value = d.operating_hours||'';
            $id('editDestEntranceFee').value    = d.entrance_fee||'';
            $id('editDestDuration').value       = d.duration||'';
            $id('editDestBestTime').value       = d.best_time_to_visit||'';
            $id('editDestActivities').value     = d.activities||'';
            $id('editDestAmenities').value      = d.amenities||'';
            $id('editDestContact').value        = d.contact_info||'';
            $id('editDestWebsite').value        = d.website||'';
            $id('editDestImageUrl').value       = d.image_url||'';
            $id('editDestRating').value         = d.rating||0;
            $id('editDestReviewCount').value    = d.review_count||0;
            $id('editDestAssignedGuide').value   = d.assigned_guide||'';
            $id('editDestStatus').value         = d.status||'active';
            openModal('editDestinationModal');
        })
        .catch(e=>showToast('Error: '+e.message,'error'));
    }

    function handleEditDestination(event) {
        event.preventDefault();
        const btn = $id('editSubmitBtn');
        btn.textContent = 'Updating…'; btn.disabled = true;
        const formData = new FormData(event.target);
        const data = Object.fromEntries(formData.entries());
        data.action = 'edit_spot';
        fetch('', { method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'}, body:new URLSearchParams(data) })
        .then(r=>r.json())
        .then(result => {
            if (result.success) {
                closeModal('editDestinationModal');
                showToast(result.message||'Destination updated successfully!');
                setTimeout(()=>location.reload(), 900);
            } else { showToast(result.message||'Failed to update','error'); btn.textContent='Update Destination'; btn.disabled=false; }
        })
        .catch(e=>{ showToast('Error: '+e.message,'error'); btn.textContent='Update Destination'; btn.disabled=false; });
    }

    /* ── Add ── */
    function handleAddDestination(event) {
        event.preventDefault();
        const btn = $id('addSubmitBtn');
        btn.textContent = 'Adding…'; btn.disabled = true;
        const formData = new FormData(event.target);
        const data = Object.fromEntries(formData.entries());
        data.action = 'add_spot';
        fetch('', { method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'}, body:new URLSearchParams(data) })
        .then(r=>r.json())
        .then(result => {
            if (result.success) {
                closeModal('addDestinationModal');
                event.target.reset();
                showToast(result.message||'Destination added successfully!');
                setTimeout(()=>location.reload(), 900);
            } else { showToast(result.message||'Failed to add','error'); btn.textContent='Add Destination'; btn.disabled=false; }
        })
        .catch(e=>{ showToast('Error: '+e.message,'error'); btn.textContent='Add Destination'; btn.disabled=false; });
    }

    /* ── Delete ── */
    function deleteDestination(spotId) {
        fetch('', { method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'}, body:`action=get_spot&spot_id=${spotId}` })
        .then(r=>r.json())
        .then(data => {
            const name = data&&data.success ? (data.data.name||'this destination') : 'this destination';
            $id('deleteSpotName').textContent = name;
            
            // Display destination details for identification
            if (data && data.success) {
                const spot = data.data;
                const details = `
                    <div style="display:grid;gap:8px;">
                        <div><strong>Name:</strong> ${escH(spot.name)}</div>
                        <div><strong>Category:</strong> ${escH(spot.category)}</div>
                        <div><strong>Location:</strong> ${escH(spot.location)}</div>
                        <div><strong>Entrance Fee:</strong> ${escH(spot.entrance_fee || 'N/A')}</div>
                        <div><strong>Status:</strong> ${escH(spot.status || 'active')}</div>
                        <div><strong>Rating:</strong> ${parseFloat(spot.rating || 0).toFixed(1)} ⭐ (${spot.review_count || 0} reviews)</div>
                    </div>
                `;
                $id('deleteSpotDetails').innerHTML = details;
            }
            
            const btn = $id('deleteConfirmBtn');
            const nb = btn.cloneNode(true);
            btn.parentNode.replaceChild(nb, btn);
            nb.addEventListener('click', () => {
                const justification = $id('deleteSpotJustification').value.trim();
                if (!justification) {
                    showToast('Please provide a justification for deletion', 'error');
                    $id('deleteSpotJustification').focus();
                    return;
                }
                
                nb.textContent = 'Deleting…'; nb.disabled = true;
                fetch('', { method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'}, body:`action=delete_spot&spot_id=${spotId}&justification=${encodeURIComponent(justification)}` })
                .then(r=>r.json())
                .then(result => {
                    if (result.success) {
                        closeModal('deleteDestinationModal');
                        showToast(result.message||'Destination deleted with justification logged!');
                        setTimeout(()=>location.reload(), 900);
                    } else { showToast(result.message||'Failed to delete','error'); nb.textContent='Delete'; nb.disabled=false; }
                })
                .catch(e=>{ showToast('Error: '+e.message,'error'); nb.textContent='Delete'; nb.disabled=false; });
            });
            
            // Clear justification when opening modal
            $id('deleteSpotJustification').value = '';
            openModal('deleteDestinationModal');
        })
        .catch(()=>openModal('deleteDestinationModal'));
    }

    /* ── Sidebar logout → sign-out modal (matches original behaviour) ── */
    document.addEventListener('DOMContentLoaded', function() {
        const logoutBtn = document.querySelector('.logout-btn');
        if (logoutBtn) {
            logoutBtn.addEventListener('click', function(e) {
                e.preventDefault();
                openModal('signOutModal');
            });
        }
    });

    /* ── Overlay click / Escape ── */
    const allModals = ['addDestinationModal','viewDestinationModal','editDestinationModal','deleteDestinationModal','signOutModal'];
    window.addEventListener('click', e=>{ allModals.forEach(id=>{ const m=$id(id); if(m&&e.target===m) closeModal(id); }); });
    document.addEventListener('keydown', e=>{ if(e.key==='Escape') allModals.forEach(id=>closeModal(id)); });
    </script>
    </body>
    </html>
    <?php closeAdminConnection($conn); ?>