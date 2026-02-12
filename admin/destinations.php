<?php
// Destinations Management Module
// This file handles tourist spot management operations with separated connections and functions

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/auth.php';

// Database connection functions
function getAdminConnection()
{
    return getDatabaseConnection();
}

function initAdminAuth()
{
    requireAdmin();
    return getCurrentUser();
}

function closeAdminConnection($conn)
{
    closeDatabaseConnection($conn);
}

// Tourist Spots Management Functions
function addTouristSpot($conn, $data)
{
    try {
        $stmt = $conn->prepare("INSERT INTO tourist_spots (name, description, category, location, address, operating_hours, entrance_fee, duration, best_time_to_visit, activities, amenities, contact_info, website, image_url, rating, review_count, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'active')");
        $stmt->bind_param(
            "sssssssssssssssi",
            $data['name'],
            $data['description'],
            $data['category'],
            $data['location'],
            $data['address'],
            $data['operating_hours'],
            $data['entrance_fee'],
            $data['duration'],
            $data['best_time_to_visit'],
            $data['activities'],
            $data['amenities'],
            $data['contact_info'],
            $data['website'],
            $data['image_url'],
            $data['rating'],
            $data['review_count']
        );

        if ($stmt->execute()) {
            return ['success' => true, 'message' => 'Tourist spot added successfully'];
        } else {
            return ['success' => false, 'message' => 'Failed to add tourist spot'];
        }
    } catch (Exception $e) {
        return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
    }
}

function editTouristSpot($conn, $data)
{
    try {
        $stmt = $conn->prepare("UPDATE tourist_spots SET name = ?, description = ?, category = ?, location = ?, address = ?, operating_hours = ?, entrance_fee = ?, duration = ?, best_time_to_visit = ?, activities = ?, amenities = ?, contact_info = ?, website = ?, image_url = ?, rating = ?, review_count = ?, status = ? WHERE id = ?");
        $stmt->bind_param(
            "sssssssssssssssisi",
            $data['name'],
            $data['description'],
            $data['category'],
            $data['location'],
            $data['address'],
            $data['operating_hours'],
            $data['entrance_fee'],
            $data['duration'],
            $data['best_time_to_visit'],
            $data['activities'],
            $data['amenities'],
            $data['contact_info'],
            $data['website'],
            $data['image_url'],
            $data['rating'],
            $data['review_count'],
            $data['status'],
            $data['spot_id']
        );

        if ($stmt->execute()) {
            return ['success' => true, 'message' => 'Tourist spot updated successfully'];
        } else {
            return ['success' => false, 'message' => 'Failed to update tourist spot'];
        }
    } catch (Exception $e) {
        return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
    }
}

function deleteTouristSpot($conn, $spotId)
{
    try {
        $stmt = $conn->prepare("DELETE FROM tourist_spots WHERE id = ?");
        $stmt->bind_param("i", $spotId);

        if ($stmt->execute()) {
            return ['success' => true, 'message' => 'Tourist spot deleted successfully'];
        } else {
            return ['success' => false, 'message' => 'Failed to delete tourist spot'];
        }
    } catch (Exception $e) {
        return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
    }
}

function getTouristSpot($conn, $spotId)
{
    try {
        $stmt = $conn->prepare("SELECT * FROM tourist_spots WHERE id = ?");
        $stmt->bind_param("i", $spotId);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            return ['success' => true, 'data' => $result->fetch_assoc()];
        } else {
            return ['success' => false, 'message' => 'Tourist spot not found'];
        }
    } catch (Exception $e) {
        return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
    }
}

function getTouristSpotsList($conn, $page = 1, $limit = 15, $search = '')
{
    // Check if connection is valid
    if (!$conn) {
        return [
            'spots' => [],
            'pagination' => [
                'current_page' => $page,
                'total_pages' => 0,
                'total_count' => 0,
                'limit' => $limit
            ]
        ];
    }

    $offset = ($page - 1) * $limit;
    $search = $conn->real_escape_string($search);

    // Get tourist spots with pagination
    $spotsQuery = "SELECT ts.* FROM tourist_spots ts WHERE 1=1";

    if ($search) {
        $spotsQuery .= " AND (ts.name LIKE '%$search%' OR ts.category LIKE '%$search%' OR ts.location LIKE '%$search%')";
    }

    $spotsQuery .= " ORDER BY ts.created_at DESC LIMIT $limit OFFSET $offset";
    $spotsResult = $conn->query($spotsQuery);

    // Get total count for pagination
    $countQuery = "SELECT COUNT(*) as total FROM tourist_spots WHERE 1=1";
    if ($search) {
        $countQuery .= " AND (name LIKE '%$search%' OR category LIKE '%$search%' OR location LIKE '%$search%')";
    }
    $countResult = $conn->query($countQuery);

    if ($spotsResult && $countResult) {
        $totalCount = $countResult->fetch_assoc()['total'];
        $totalPages = ceil($totalCount / $limit);

        $spots = [];
        while ($row = $spotsResult->fetch_assoc()) {
            $spots[] = $row;
        }

        return [
            'spots' => $spots,
            'pagination' => [
                'current_page' => $page,
                'total_pages' => $totalPages,
                'total_count' => $totalCount,
                'limit' => $limit
            ]
        ];
    } else {
        return [
            'spots' => [],
            'pagination' => [
                'current_page' => $page,
                'total_pages' => 0,
                'total_count' => 0,
                'limit' => $limit
            ]
        ];
    }
}

function getAdminStats($conn)
{
    $stats = [];

    // Total users
    $result = $conn->query("SELECT COUNT(*) as total FROM users WHERE user_type = 'user'");
    $stats['totalUsers'] = $result->fetch_assoc()['total'];

    // Active users
    $result = $conn->query("SELECT COUNT(*) as total FROM users WHERE user_type = 'user' AND status = 'active'");
    $stats['activeUsers'] = $result->fetch_assoc()['total'];

    // Total bookings
    $result = $conn->query("SELECT COUNT(*) as total FROM bookings");
    $stats['totalBookings'] = $result->fetch_assoc()['total'];

    // Today's logins
    $result = $conn->query("SELECT COUNT(DISTINCT user_id) as total FROM login_activity WHERE DATE(login_time) = CURDATE() AND status = 'success'");
    $stats['todayLogins'] = $result->fetch_assoc()['total'];

    // Total guides
    $result = $conn->query("SELECT COUNT(*) as total FROM tour_guides");
    $stats['totalGuides'] = $result->fetch_assoc()['total'];

    // Total destinations
    $result = $conn->query("SELECT COUNT(*) as total FROM tourist_spots");
    $stats['totalDestinations'] = $result->fetch_assoc()['total'];

    return $stats;
}

function getTouristDetailFiles()
{
    $touristDetailDir = __DIR__ . '/../tourist-detail/';
    $files = [];
    
    if (is_dir($touristDetailDir)) {
        $fileList = scandir($touristDetailDir);
        foreach ($fileList as $file) {
            if (pathinfo($file, PATHINFO_EXTENSION) === 'php') {
                $filePath = $touristDetailDir . $file;
                $files[] = [
                    'filename' => $file,
                    'filepath' => $filePath,
                    'data' => extractTouristDataFromFile($filePath)
                ];
            }
        }
    }
    
    return $files;
}

function extractTouristDataFromFile($filePath)
{
    $content = file_get_contents($filePath);
    $data = [];
    
    // Extract title
    if (preg_match('/<title>([^<]+) - San Jose del Monte Tourism<\/title>/', $content, $matches)) {
        $data['name'] = trim($matches[1]);
    }
    
    // Extract hero title
    if (preg_match('/<h1 class="hero-title">([^<]+)<\/h1>/', $content, $matches)) {
        $data['name'] = trim($matches[1]);
    }
    
    // Extract hero subtitle
    if (preg_match('/<p class="hero-subtitle">([^<]+)<\/p>/', $content, $matches)) {
        $data['subtitle'] = trim($matches[1]);
    }
    
    // Extract description
    if (preg_match('/<h2 class="section-title">About [^<]+<\/h2>\s*<p class="description">([^<]+)<\/p>/', $content, $matches)) {
        $data['description'] = trim($matches[1]);
    }
    
    // Extract category based on filename patterns
    $filename = basename($filePath, '.php');
    $data['category'] = categorizeFromFilename($filename);
    
    // Extract location (default to San Jose del Monte)
    $data['location'] = 'San Jose del Monte, Bulacan';
    
    // Extract entrance fee
    $data['entrance_fee'] = extractEntranceFeeFromContent($content);
    
    // Extract activities
    $data['activities'] = extractActivitiesFromContent($content);
    
    // Extract amenities
    $data['amenities'] = extractAmenitiesFromContent($content);
    
    // Extract operating hours
    $data['operating_hours'] = extractOperatingHoursFromContent($content);
    
    // Extract best time to visit
    $data['best_time_to_visit'] = extractBestTimeFromContent($content);
    
    // Set default values
    $data['rating'] = 0.0;
    $data['review_count'] = 0;
    $data['status'] = 'active';
    $data['image_url'] = '';
    $data['contact_info'] = '';
    $data['website'] = '';
    $data['address'] = '';
    
    return $data;
}

function categorizeFromFilename($filename)
{
    $categories = [
        'falls' => 'nature',
        'mt' => 'nature',
        'farm' => 'farm',
        'park' => 'park',
        'shrine' => 'religious',
        'grotto' => 'religious',
        'monument' => 'historical'
    ];
    
    foreach ($categories as $keyword => $category) {
        if (stripos($filename, $keyword) !== false) {
            return $category;
        }
    }
    
    return 'nature'; // default
}

function extractEntranceFeeFromContent($content)
{
    if (preg_match('/₱\s*\d+/', $content, $matches)) {
        return $matches[0];
    } elseif (preg_match('/PHP\s*\d+/', $content, $matches)) {
        return $matches[0];
    }
    return 'Free';
}

function extractActivitiesFromContent($content)
{
    $activities = [];
    
    // Look for activity keywords
    $activityKeywords = ['hiking', 'swimming', 'photography', 'picnic', 'camping', 'nature tripping', 'sightseeing'];
    
    foreach ($activityKeywords as $activity) {
        if (stripos($content, $activity) !== false) {
            $activities[] = ucfirst($activity);
        }
    }
    
    return implode(', ', $activities);
}

function extractAmenitiesFromContent($content)
{
    $amenities = [];
    
    // Look for amenity keywords
    $amenityKeywords = ['parking', 'restroom', 'shower', 'cottage', 'shed', 'store', 'restaurant'];
    
    foreach ($amenityKeywords as $amenity) {
        if (stripos($content, $amenity) !== false) {
            $amenities[] = ucfirst($amenity);
        }
    }
    
    return implode(', ', $amenities);
}

function extractOperatingHoursFromContent($content)
{
    if (preg_match('/(\d{1,2}:\d{2}\s*(AM|PM|am|pm)\s*-\s*\d{1,2}:\d{2}\s*(AM|PM|am|pm))/', $content, $matches)) {
        return $matches[1];
    }
    return '6:00 AM - 6:00 PM'; // default
}

function extractBestTimeFromContent($content)
{
    if (stripos($content, 'morning') !== false) {
        return 'Morning';
    } elseif (stripos($content, 'summer') !== false || stripos($content, 'dry season') !== false) {
        return 'Dry season';
    }
    return 'All year round'; // default
}

// Initialize admin authentication
$currentUser = initAdminAuth();

// Get database connection
$conn = getAdminConnection();

// Fetch dashboard settings
$dbSettings = [];
$result = $conn->query("SELECT setting_key, setting_value FROM admin_dashboard_settings");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $dbSettings[$row['setting_key']] = $row['setting_value'];
    }
}

// Fetch destination settings
$destSettings = [];
$result = $conn->query("SELECT setting_key, setting_value FROM destination_settings");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $destSettings[$row['setting_key']] = $row['setting_value'];
    }
}

// Common settings
$logoText = $dbSettings['admin_logo_text'] ?? 'SJDM ADMIN';
$moduleTitle = $destSettings['module_title'] ?? 'Destinations Management';
$moduleSubtitle = $destSettings['module_subtitle'] ?? 'Manage tourist spots';
$adminMark = $dbSettings['admin_mark_label'] ?? 'A';

// Fetch admin specific info
$adminInfo = ['role_title' => 'Administrator', 'admin_mark' => 'A'];
$stmt = $conn->prepare("SELECT admin_mark, role_title FROM admin_users WHERE user_id = ?");
$userId = $currentUser['id'];
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
if ($row = $result->fetch_assoc()) {
    $adminInfo = $row;
}
$stmt->close();

// Fetch sidebar menu
$menuItems = [];
$result = $conn->query("SELECT * FROM admin_menu_items WHERE is_active = 1 ORDER BY display_order ASC");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $menuItems[] = $row;
    }
}

// Get statistics
$stats = getAdminStats($conn);

// Map query keys to values for menu badges
$queryValues = [
    'totalUsers' => $stats['totalUsers'],
    'totalBookings' => $stats['totalBookings'],
    'totalGuides' => $stats['totalGuides'],
    'totalDestinations' => $stats['totalDestinations']
];

// Fetch destinations
$spots = [];
$result = $conn->query("SELECT * FROM tourist_spots ORDER BY name ASC");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $spots[] = $row;
    }
}

// Create pagination data for display
$pagination = [
    'current_page' => 1,
    'total_pages' => 1,
    'total_count' => count($spots),
    'limit' => 15
];

// Initialize search variable
$search = isset($_GET['search']) ? $_GET['search'] : '';

// Handle AJAX requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    switch ($action) {
        case 'add_spot':
            $response = addTouristSpot($conn, $_POST);
            echo json_encode($response);
            exit;
        case 'edit_spot':
            $response = editTouristSpot($conn, $_POST);
            echo json_encode($response);
            exit;
        case 'delete_spot':
            $response = deleteTouristSpot($conn, $_POST['spot_id']);
            echo json_encode($response);
            exit;
        case 'get_spot':
            $response = getTouristSpot($conn, $_POST['spot_id']);
            echo json_encode($response);
            exit;
    }
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Destinations Management | SJDM Tours Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet">
    <link rel="stylesheet" href="admin-styles.css">
</head>
<body>
    <div class="admin-container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <div class="logo">
                    <div class="mark-icon"><?php echo strtoupper(substr($logoText, 0, 1) ?: 'A'); ?></div>
                    <span><?php echo $logoText; ?></span>
                </div>
            </div>

            <nav class="sidebar-nav">
                <?php foreach ($menuItems as $item):
                    // Skip hotels, settings, and reports menu items
                    if (stripos($item['menu_name'], 'hotels') !== false || stripos($item['menu_url'], 'hotels') !== false || 
                        stripos($item['menu_name'], 'settings') !== false || stripos($item['menu_url'], 'settings') !== false ||
                        stripos($item['menu_name'], 'reports') !== false || stripos($item['menu_url'], 'reports') !== false) {
                        continue;
                    }
                    
                    $isActive = basename($_SERVER['PHP_SELF']) == $item['menu_url'] ? 'active' : '';
                    $badgeVal = 0;
                    if (isset($item['badge_query']) && isset($queryValues[$item['badge_query']])) {
                        $badgeVal = $queryValues[$item['badge_query']];
                    }
                    ?>
                    <a href="<?php echo $item['menu_url']; ?>" class="nav-item <?php echo $isActive; ?>">
                        <span class="material-icons-outlined"><?php echo $item['menu_icon']; ?></span>
                        <span><?php echo $item['menu_name']; ?></span>
                        <?php if ($badgeVal > 0): ?>
                            <span class="badge"><?php echo $badgeVal; ?></span>
                        <?php endif; ?>
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
                    <button class="btn-primary" onclick="showAddDestinationModal()">
                        <span class="material-icons-outlined">add</span>
                        Add Destination
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
                <!-- Destination Statistics -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <h3><?php echo $stats['totalDestinations']; ?></h3>
                        <p>Total Destinations</p>
                    </div>
                    <div class="stat-card">
                        <h3><?php echo count(array_filter($spots, fn($s) => $s['status'] == 'active')); ?></h3>
                        <p>Active Destinations</p>
                    </div>
                    <div class="stat-card">
                        <h3><?php echo count(array_unique(array_column($spots, 'category'))); ?></h3>
                        <p>Categories</p>
                    </div>
                </div>

                <!-- Search and Filters -->
                <div class="search-bar">
                    <input type="text" id="searchInput"
                        placeholder="Search destinations by name, category, or location..."
                        value="<?php echo htmlspecialchars($search); ?>">
                    <button class="btn-secondary" onclick="searchDestinations()">
                        <span class="material-icons-outlined">search</span>
                        Search
                    </button>
                    <button class="btn-secondary" onclick="clearSearch()">
                        <span class="material-icons-outlined">clear</span>
                        Clear
                    </button>
                </div>

                <!-- Destinations Table -->
                <div class="table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Image</th>
                                <th>Name</th>
                                <th>Category</th>
                                <th>Location</th>
                                <th>Rating</th>
                                <th>Entrance Fee</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($spots as $spot): ?>
                                <tr>
                                    <td>
                                        <?php if ($spot['image_url']): ?>
                                            <img src="<?php echo htmlspecialchars($spot['image_url']); ?>"
                                                alt="<?php echo htmlspecialchars($spot['name']); ?>" class="destination-image">
                                        <?php else: ?>
                                            <div class="destination-image"
                                                style="background: var(--bg-light); display: flex; align-items: center; justify-content: center;">
                                                <span class="material-icons-outlined">place</span>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <strong><?php echo htmlspecialchars($spot['name']); ?></strong><br>
                                        <small><?php echo htmlspecialchars(substr($spot['description'], 0, 100)); ?>...</small>
                                    </td>
                                    <td><?php echo htmlspecialchars($spot['category']); ?></td>
                                    <td><?php echo htmlspecialchars($spot['location']); ?></td>
                                    <td>
                                        <div class="rating">
                                            <?php echo number_format($spot['rating'], 1); ?> 
                                            <small>(<?php echo $spot['review_count']; ?> reviews)</small>
                                        </div>
                                    </td>
                                    <td><?php echo $spot['entrance_fee']; ?></td>
                                    <td>
                                        <span class="status-badge status-<?php echo $spot['status']; ?>">
                                            <?php echo ucfirst($spot['status']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="action-buttons">
                                            <button class="btn-icon" onclick="viewDestination(<?php echo $spot['id']; ?>)"
                                                title="View">
                                                <span class="material-icons-outlined">visibility</span>
                                            </button>
                                            <button class="btn-icon" onclick="editDestination(<?php echo $spot['id']; ?>)"
                                                title="Edit">
                                                <span class="material-icons-outlined">edit</span>
                                            </button>
                                            <button class="btn-icon" onclick="deleteDestination(<?php echo $spot['id']; ?>)"
                                                title="Delete">
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
                            <button onclick="goToPage(<?php echo $pagination['current_page'] - 1; ?>)">Previous</button>
                        <?php endif; ?>

                        <?php for ($i = 1; $i <= $pagination['total_pages']; $i++): ?>
                            <button onclick="goToPage(<?php echo $i; ?>)" <?php echo $i == $pagination['current_page'] ? 'class="active"' : ''; ?>>
                                <?php echo $i; ?>
                            </button>
                        <?php endfor; ?>

                        <?php if ($pagination['current_page'] < $pagination['total_pages']): ?>
                            <button onclick="goToPage(<?php echo $pagination['current_page'] + 1; ?>)">Next</button>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </main>
    </div>

    <!-- Add Destination Modal -->
    <div id="addDestinationModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Add New Destination</h2>
                <button class="modal-close" onclick="closeAddDestinationModal()">
                    <span class="material-icons-outlined">close</span>
                </button>
            </div>
            <form id="addDestinationForm" onsubmit="handleAddDestination(event)">
                <div class="modal-body">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="destName">Name *</label>
                            <input type="text" id="destName" name="name" required>
                        </div>
                        <div class="form-group">
                            <label for="destCategory">Category *</label>
                            <select id="destCategory" name="category" required>
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
                    <div class="form-group">
                        <label for="destDescription">Description</label>
                        <textarea id="destDescription" name="description" rows="3"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="destLocation">Location *</label>
                        <input type="text" id="destLocation" name="location" required>
                    </div>
                    <div class="form-group">
                        <label for="destAddress">Address</label>
                        <input type="text" id="destAddress" name="address">
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="destOperatingHours">Operating Hours</label>
                            <input type="text" id="destOperatingHours" name="operating_hours" placeholder="e.g., 8:00 AM - 5:00 PM">
                        </div>
                        <div class="form-group">
                            <label for="destEntranceFee">Entrance Fee</label>
                            <input type="text" id="destEntranceFee" name="entrance_fee" placeholder="e.g., ₱100">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="destDuration">Duration</label>
                            <input type="text" id="destDuration" name="duration" placeholder="e.g., 2-3 hours">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="destBestTime">Best Time to Visit</label>
                        <input type="text" id="destBestTime" name="best_time_to_visit" placeholder="e.g., Morning, Dry season">
                    </div>
                    <div class="form-group">
                        <label for="destActivities">Activities</label>
                        <textarea id="destActivities" name="activities" rows="2" placeholder="e.g., Hiking, Photography, Swimming"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="destAmenities">Amenities</label>
                        <textarea id="destAmenities" name="amenities" rows="2" placeholder="e.g., Parking, Restrooms, Food stalls"></textarea>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="destContact">Contact Info</label>
                            <input type="text" id="destContact" name="contact_info" placeholder="Phone number or email">
                        </div>
                        <div class="form-group">
                            <label for="destWebsite">Website</label>
                            <input type="url" id="destWebsite" name="website" placeholder="https://example.com">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="destImageUrl">Image URL</label>
                        <input type="url" id="destImageUrl" name="image_url" placeholder="https://example.com/image.jpg">
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="destRating">Rating</label>
                            <input type="number" id="destRating" name="rating" min="0" max="5" step="0.1" value="0">
                        </div>
                        <div class="form-group">
                            <label for="destReviewCount">Review Count</label>
                            <input type="number" id="destReviewCount" name="review_count" min="0" value="0">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-secondary" onclick="closeAddDestinationModal()">Cancel</button>
                    <button type="submit" class="btn-primary">Add Destination</button>
                </div>
            </form>
        </div>
    </div>

    <!-- View Destination Modal -->
    <div id="viewDestinationModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>View Destination Details</h2>
                <button class="modal-close" onclick="closeViewDestinationModal()">
                    <span class="material-icons-outlined">close</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="destination-details">
                    <div class="detail-row">
                        <div class="detail-label">Name:</div>
                        <div class="detail-value" id="viewDestName"></div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">Category:</div>
                        <div class="detail-value" id="viewDestCategory"></div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">Location:</div>
                        <div class="detail-value" id="viewDestLocation"></div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">Description:</div>
                        <div class="detail-value" id="viewDestDescription"></div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">Operating Hours:</div>
                        <div class="detail-value" id="viewDestOperatingHours"></div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">Entrance Fee:</div>
                        <div class="detail-value" id="viewDestEntranceFee"></div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">Duration:</div>
                        <div class="detail-value" id="viewDestDuration"></div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">Best Time to Visit:</div>
                        <div class="detail-value" id="viewDestBestTime"></div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">Activities:</div>
                        <div class="detail-value" id="viewDestActivities"></div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">Amenities:</div>
                        <div class="detail-value" id="viewDestAmenities"></div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">Contact Info:</div>
                        <div class="detail-value" id="viewDestContact"></div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">Website:</div>
                        <div class="detail-value" id="viewDestWebsite"></div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">Rating:</div>
                        <div class="detail-value" id="viewDestRating"></div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">Status:</div>
                        <div class="detail-value" id="viewDestStatus"></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-secondary" onclick="closeViewDestinationModal()">Close</button>
            </div>
        </div>
    </div>

    <!-- Edit Destination Modal -->
    <div id="editDestinationModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Edit Destination</h2>
                <button class="modal-close" onclick="closeEditDestinationModal()">
                    <span class="material-icons-outlined">close</span>
                </button>
            </div>
            <form id="editDestinationForm" onsubmit="handleEditDestination(event)">
                <input type="hidden" name="action" value="edit_spot">
                <input type="hidden" id="editSpotId" name="spot_id">
                <div class="modal-body">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="editDestName">Name *</label>
                            <input type="text" id="editDestName" name="name" required>
                        </div>
                        <div class="form-group">
                            <label for="editDestCategory">Category *</label>
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
                    <div class="form-group">
                        <label for="editDestDescription">Description</label>
                        <textarea id="editDestDescription" name="description" rows="3"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="editDestLocation">Location *</label>
                        <input type="text" id="editDestLocation" name="location" required>
                    </div>
                    <div class="form-group">
                        <label for="editDestAddress">Address</label>
                        <input type="text" id="editDestAddress" name="address">
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="editDestOperatingHours">Operating Hours</label>
                            <input type="text" id="editDestOperatingHours" name="operating_hours" placeholder="e.g., 8:00 AM - 5:00 PM">
                        </div>
                        <div class="form-group">
                            <label for="editDestEntranceFee">Entrance Fee</label>
                            <input type="text" id="editDestEntranceFee" name="entrance_fee" placeholder="e.g., ₱100">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="editDestDuration">Duration</label>
                            <input type="text" id="editDestDuration" name="duration" placeholder="e.g., 2-3 hours">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="editDestBestTime">Best Time to Visit</label>
                        <input type="text" id="editDestBestTime" name="best_time_to_visit" placeholder="e.g., Morning, Dry season">
                    </div>
                    <div class="form-group">
                        <label for="editDestActivities">Activities</label>
                        <textarea id="editDestActivities" name="activities" rows="2" placeholder="e.g., Hiking, Photography, Swimming"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="editDestAmenities">Amenities</label>
                        <textarea id="editDestAmenities" name="amenities" rows="2" placeholder="e.g., Parking, Restrooms, Food stalls"></textarea>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="editDestContact">Contact Info</label>
                            <input type="text" id="editDestContact" name="contact_info" placeholder="Phone number or email">
                        </div>
                        <div class="form-group">
                            <label for="editDestWebsite">Website</label>
                            <input type="url" id="editDestWebsite" name="website" placeholder="https://example.com">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="editDestImageUrl">Image URL</label>
                        <input type="url" id="editDestImageUrl" name="image_url" placeholder="https://example.com/image.jpg">
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="editDestRating">Rating</label>
                            <input type="number" id="editDestRating" name="rating" min="0" max="5" step="0.1" value="0">
                        </div>
                        <div class="form-group">
                            <label for="editDestReviewCount">Review Count</label>
                            <input type="number" id="editDestReviewCount" name="review_count" min="0" value="0">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="editDestStatus">Status</label>
                        <select id="editDestStatus" name="status">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-secondary" onclick="closeEditDestinationModal()">Cancel</button>
                    <button type="submit" class="btn-primary">Update Destination</button>
                </div>
            </form>
        </div>
    </div>

    <script src="admin-script.js"></script>
    <script src="admin-profile-dropdown.js"></script>
    <style>
        .destination-details {
            display: grid;
            grid-template-columns: 150px 1fr;
            gap: 12px;
            padding: 20px;
        }
        
        .detail-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px 0;
            border-bottom: 1px solid var(--border);
        }
        
        .detail-label {
            font-weight: 600;
            color: var(--text-secondary);
            font-size: 14px;
        }
        
        .detail-value {
            color: var(--text-primary);
            font-size: 14px;
            word-wrap: break-word;
        }
        
        .file-source-indicator {
            background: #e3f2fd;
            color: #1976d2;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: 600;
            margin-left: 8px;
            display: inline-block;
        }
    </style>
    <script>
        // Initialize Admin Dashboard
        let adminDashboard;
        
        document.addEventListener('DOMContentLoaded', function() {
            adminDashboard = new AdminDashboard();
        });
        
        function searchDestinations() {
            const searchValue = document.getElementById('searchInput').value;
            window.location.href = `?search=${encodeURIComponent(searchValue)}`;
        }

        function clearSearch() {
            document.getElementById('searchInput').value = '';
            window.location.href = '?';
        }

        function goToPage(page) {
            const searchValue = document.getElementById('searchInput').value;
            const url = searchValue ? `?page=${page}&search=${encodeURIComponent(searchValue)}` : `?page=${page}`;
            window.location.href = url;
        }

        function viewDestination(spotId) {
            if (adminDashboard) {
                adminDashboard.viewSpot(spotId);
            } else {
                // Fallback: fetch destination data and show modal
                fetch('', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `action=get_spot&spot_id=${spotId}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Populate view modal with data
                        document.getElementById('viewDestName').textContent = data.data.name || '';
                        document.getElementById('viewDestCategory').textContent = data.data.category || '';
                        document.getElementById('viewDestLocation').textContent = data.data.location || '';
                        document.getElementById('viewDestDescription').textContent = data.data.description || '';
                        document.getElementById('viewDestOperatingHours').textContent = data.data.operating_hours || '';
                        document.getElementById('viewDestEntranceFee').textContent = data.data.entrance_fee || '';
                        document.getElementById('viewDestDuration').textContent = data.data.duration || '';
                        document.getElementById('viewDestBestTime').textContent = data.data.best_time_to_visit || '';
                        document.getElementById('viewDestActivities').textContent = data.data.activities || '';
                        document.getElementById('viewDestAmenities').textContent = data.data.amenities || '';
                        document.getElementById('viewDestContact').textContent = data.data.contact_info || '';
                        document.getElementById('viewDestWebsite').textContent = data.data.website || '';
                        document.getElementById('viewDestRating').textContent = data.data.rating ? parseFloat(data.data.rating).toFixed(1) : '0.0';
                        document.getElementById('viewDestStatus').textContent = data.data.status || '';
                        
                        // Show modal
                        const modal = document.getElementById('viewDestinationModal');
                        if (modal) {
                            modal.style.display = 'block';
                            modal.classList.add('show');
                            document.body.style.overflow = 'hidden';
                        } else {
                            console.error('Modal not found!');
                            alert('Error: View modal not found.');
                        }
                    } else {
                        console.error('Server error:', data.message);
                        alert('Error loading destination details: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error loading destination details.');
                });
            }
        }

        function editDestination(spotId) {
            if (adminDashboard) {
                adminDashboard.editSpot(spotId);
            } else {
                // Fallback: fetch destination data and show modal
                fetch('', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `action=get_spot&spot_id=${spotId}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Populate edit modal with data
                        document.getElementById('editSpotId').value = data.data.id;
                        document.getElementById('editDestName').value = data.data.name || '';
                        document.getElementById('editDestCategory').value = data.data.category || '';
                        document.getElementById('editDestDescription').value = data.data.description || '';
                        document.getElementById('editDestLocation').value = data.data.location || '';
                        document.getElementById('editDestAddress').value = data.data.address || '';
                        document.getElementById('editDestOperatingHours').value = data.data.operating_hours || '';
                        document.getElementById('editDestEntranceFee').value = data.data.entrance_fee || '';
                        document.getElementById('editDestDuration').value = data.data.duration || '';
                        document.getElementById('editDestBestTime').value = data.data.best_time_to_visit || '';
                        document.getElementById('editDestActivities').value = data.data.activities || '';
                        document.getElementById('editDestAmenities').value = data.data.amenities || '';
                        document.getElementById('editDestContact').value = data.data.contact_info || '';
                        document.getElementById('editDestWebsite').value = data.data.website || '';
                        document.getElementById('editDestImageUrl').value = data.data.image_url || '';
                        document.getElementById('editDestRating').value = data.data.rating || '';
                        document.getElementById('editDestReviewCount').value = data.data.review_count || '';
                        document.getElementById('editDestStatus').value = data.data.status || '';
                        
                        // Show modal
                        const modal = document.getElementById('editDestinationModal');
                        if (modal) {
                            modal.style.display = 'block';
                            modal.classList.add('show');
                            document.body.style.overflow = 'hidden';
                        } else {
                            console.error('Edit modal not found!');
                            alert('Error: Edit modal not found.');
                        }
                    } else {
                        console.error('Server error:', data.message);
                        alert('Error loading destination data: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error loading destination data.');
                });
            }
        }

        function closeViewDestinationModal() {
            if (adminDashboard) {
                adminDashboard.closeModal('viewDestinationModal');
            } else {
                // Fallback modal close
                const modal = document.getElementById('viewDestinationModal');
                if (modal) {
                    modal.style.display = 'none';
                    modal.classList.remove('show');
                    document.body.style.overflow = 'auto';
                }
            }
        }

        function closeEditDestinationModal() {
            if (adminDashboard) {
                adminDashboard.closeModal('editDestinationModal');
            } else {
                // Fallback modal close
                const modal = document.getElementById('editDestinationModal');
                if (modal) {
                    modal.style.display = 'none';
                    modal.classList.remove('show');
                    document.body.style.overflow = 'auto';
                }
            }
        }

        function handleEditDestination(event) {
            event.preventDefault();
            
            if (adminDashboard) {
                // Use AdminDashboard's form submission
                const form = document.getElementById('editDestinationForm');
                const formData = new FormData(form);
                
                // Submit using AdminDashboard
                adminDashboard.submitEditSpot(formData);
            } else {
                // Fallback submission
                const formData = new FormData(event.target);
                const data = Object.fromEntries(formData.entries());
                data.action = 'edit_spot';
                
                // Send data to server
                fetch('', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: new URLSearchParams(data)
                })
                .then(response => response.json())
                .then(result => {
                    if (result.success) {
                        alert(result.message);
                        closeEditDestinationModal();
                        location.reload();
                    } else {
                        alert(result.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while updating destination.');
                });
            }
        }

        function deleteDestination(spotId) {
            if (adminDashboard) {
                adminDashboard.deleteSpot(spotId);
            } else {
                // Fallback for cases where adminDashboard is not initialized
                if (confirm('Are you sure you want to delete this destination?')) {
                    fetch('', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: `action=delete_spot&spot_id=${spotId}`
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert(data.message);
                            location.reload();
                        } else {
                            alert(data.message);
                        }
                    });
                }
            }
        }

        function showAddDestinationModal() {
            if (adminDashboard) {
                adminDashboard.showModal('addDestinationModal');
            } else {
                // Fallback modal display
                const modal = document.getElementById('addDestinationModal');
                if (modal) {
                    modal.style.display = 'block';
                    modal.classList.add('show');
                    document.body.style.overflow = 'hidden';
                } else {
                    console.error('Modal not found!');
                }
            }
        }

        function closeAddDestinationModal() {
            if (adminDashboard) {
                adminDashboard.closeModal('addDestinationModal');
            } else {
                // Fallback modal close
                const modal = document.getElementById('addDestinationModal');
                if (modal) {
                    modal.style.display = 'none';
                    modal.classList.remove('show');
                    document.body.style.overflow = 'auto';
                    const form = document.getElementById('addDestinationForm');
                    if (form) {
                        form.reset();
                    }
                }
            }
        }

        function handleAddDestination(event) {
            event.preventDefault();
            
            if (adminDashboard) {
                // Use AdminDashboard's form submission
                const form = document.getElementById('addDestinationForm');
                const formData = new FormData(form);
                
                // Add action
                formData.append('action', 'add_spot');
                
                // Submit using AdminDashboard
                adminDashboard.submitAddSpot(formData);
            } else {
                // Fallback submission
                const formData = new FormData(event.target);
                const data = Object.fromEntries(formData.entries());
                data.action = 'add_spot';
                
                // Send data to server
                fetch('', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: new URLSearchParams(data)
                })
                .then(response => response.json())
                .then(result => {
                    if (result.success) {
                        alert(result.message);
                        closeAddDestinationModal();
                        location.reload();
                    } else {
                        alert(result.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while adding destination.');
                });
            }
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('addDestinationModal');
            if (event.target === modal) {
                closeAddDestinationModal();
            }
        }

        // Close modal with Escape key
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                const modal = document.getElementById('addDestinationModal');
                if (modal && modal.style.display === 'block') {
                    closeAddDestinationModal();
                }
            }
        });

        // Search on Enter key
        document.getElementById('searchInput').addEventListener('keypress', function (e) {
            if (e.key === 'Enter') {
                searchDestinations();
            }
        });
    </script>
<?php closeAdminConnection($conn); ?>
</body>

</html>