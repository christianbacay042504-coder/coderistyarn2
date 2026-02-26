<?php
// Tour Guides Management Module
// This file handles tour guide management operations with separated connections and functions

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

// Tour Guide Management Functions
function addTourGuide($conn, $data)
{
    try {
        // Check which columns exist in the table
        $columns_to_insert = [];
        $values = [];
        $types = '';
        
        // Basic columns that should always exist
        $columns_to_insert[] = 'name';
        $values[] = $data['name'];
        $types .= 's';
        
        $columns_to_insert[] = 'specialty';
        $values[] = $data['specialty'];
        $types .= 's';
        
        $columns_to_insert[] = 'contact_number';
        $values[] = $data['contact_number'];
        $types .= 's';
        
        $columns_to_insert[] = 'email';
        $values[] = $data['email'];
        $types .= 's';
        
        // Optional columns - check if they exist and add them
        $optional_columns = [
            'category' => 's',
            'description' => 's', 
            'bio' => 's',
            'areas_of_expertise' => 's',
            'rating' => 'd',
            'review_count' => 'i',
            'price_range' => 's',
            'price_min' => 'd',
            'price_max' => 'd',
            'languages' => 's',
            'schedules' => 's',
            'experience_years' => 'i',
            'group_size' => 's',
            'verified' => 'i',
            'total_tours' => 'i',
            'photo_url' => 's',
            'status' => 's'
        ];
        
        foreach ($optional_columns as $column => $type) {
            if (columnExists($conn, 'tour_guides', $column)) {
                $columns_to_insert[] = $column;
                
                if ($column === 'verified') {
                    $values[] = isset($data['verified']) ? 1 : 0;
                } elseif ($column === 'status') {
                    $values[] = 'active';
                } else {
                    $value = $data[$column] ?? '';
                    // Convert empty strings to appropriate defaults
                    if ($value === '' && in_array($column, ['rating', 'review_count', 'experience_years', 'total_tours'])) {
                        $value = 0;
                    } elseif ($value === '' && in_array($column, ['price_min', 'price_max'])) {
                        $value = 0.00;
                    }
                    $values[] = $value;
                }
                $types .= $type;
            }
        }
        
        // Build the query
        $columns_str = implode(', ', $columns_to_insert);
        $placeholders = str_repeat('?,', count($values) - 1) . '?';
        
        $sql = "INSERT INTO tour_guides ($columns_str) VALUES ($placeholders)";
        $stmt = $conn->prepare($sql);
        
        // Bind parameters dynamically
        $stmt->bind_param($types, ...$values);
        $result = $stmt->execute();
        
        if ($result) {
            $guideId = $conn->insert_id;
            
            // Handle destination assignments
            if (isset($data['destinations']) && is_array($data['destinations'])) {
                foreach ($data['destinations'] as $destinationId) {
                    $destStmt = $conn->prepare("INSERT INTO guide_destinations (guide_id, destination_id) VALUES (?, ?)");
                    $destStmt->bind_param("ii", $guideId, $destinationId);
                    $destStmt->execute();
                    $destStmt->close();
                }
            }
            
            return ['success' => true, 'message' => 'Tour guide added successfully!'];
        } else {
            return ['success' => false, 'message' => 'Failed to add tour guide: ' . $stmt->error];
        }
        
    } catch (Exception $e) {
        return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
    }
}

// Helper function to check if column exists
function columnExists($conn, $table, $column) {
    $result = $conn->query("SHOW COLUMNS FROM `$table` LIKE '$column'");
    return $result && $result->num_rows > 0;
}

function editTourGuide($conn, $data)
{
    try {
        // Build dynamic UPDATE query based on available columns
        $columns_to_update = [];
        $values = [];
        $types = '';
        
        // Basic columns that should always exist
        $columns_to_update[] = 'name';
        $values[] = $data['name'];
        $types .= 's';
        
        $columns_to_update[] = 'specialty';
        $values[] = $data['specialty'];
        $types .= 's';
        
        $columns_to_update[] = 'contact_number';
        $values[] = $data['contact_number'];
        $types .= 's';
        
        $columns_to_update[] = 'email';
        $values[] = $data['email'];
        $types .= 's';
        
        // Optional columns - check if they exist and add them
        $optional_columns = [
            'category' => 's',
            'description' => 's', 
            'bio' => 's',
            'areas_of_expertise' => 's',
            'rating' => 'd',
            'review_count' => 'i',
            'price_range' => 's',
            'languages' => 's',
            'schedules' => 's',
            'experience_years' => 'i',
            'group_size' => 's',
            'verified' => 'i',
            'total_tours' => 'i',
            'photo_url' => 's',
            'status' => 's'
        ];
        
        foreach ($optional_columns as $column => $type) {
            if (columnExists($conn, 'tour_guides', $column) && isset($data[$column])) {
                $columns_to_update[] = $column;
                
                if ($column === 'verified') {
                    $values[] = isset($data['verified']) ? 1 : 0;
                } elseif ($column === 'status') {
                    $values[] = $data['status'] ?? 'active';
                } else {
                    $value = $data[$column] ?? '';
                    // Convert empty strings to appropriate defaults
                    if ($value === '' && in_array($column, ['rating', 'review_count', 'experience_years', 'total_tours'])) {
                        $value = 0;
                    } elseif ($value === '' && in_array($column, ['group_size'])) {
                        $value = 10;
                    }
                    $values[] = $value;
                }
                $types .= $type;
            }
        }
        
        // Add the guide ID for WHERE clause
        $values[] = $data['guide_id'];
        $types .= 'i';
        
        // Build the UPDATE query
        $set_clauses = [];
        foreach ($columns_to_update as $column) {
            $set_clauses[] = "$column = ?";
        }
        $set_str = implode(', ', $set_clauses);
        
        $sql = "UPDATE tour_guides SET $set_str WHERE id = ?";
        $stmt = $conn->prepare($sql);
        
        // Bind parameters dynamically
        $stmt->bind_param($types, ...$values);
        $result = $stmt->execute();
        
        if ($result) {
            // Update destination assignments
            // First, delete existing assignments
            $deleteStmt = $conn->prepare("DELETE FROM guide_destinations WHERE guide_id = ?");
            $deleteStmt->bind_param("i", $data['guide_id']);
            $deleteStmt->execute();
            $deleteStmt->close();
            
            // Then insert new assignments
            if (isset($data['destinations']) && is_array($data['destinations'])) {
                foreach ($data['destinations'] as $destinationId) {
                    $destStmt = $conn->prepare("INSERT INTO guide_destinations (guide_id, destination_id) VALUES (?, ?)");
                    $destStmt->bind_param("ii", $data['guide_id'], $destinationId);
                    $destStmt->execute();
                    $destStmt->close();
                }
            }
            
            return ['success' => true, 'message' => 'Tour guide updated successfully'];
        } else {
            return ['success' => false, 'message' => 'Failed to update tour guide: ' . $stmt->error];
        }
    } catch (Exception $e) {
        return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
    }
}

function updateGuideVerification($conn, $guideId, $verified)
{
    try {
        $stmt = $conn->prepare("UPDATE tour_guides SET verified = ? WHERE id = ?");
        $stmt->bind_param("ii", $verified, $guideId);
        $result = $stmt->execute();
        
        if ($result) {
            $action = $verified ? 'verified' : 'unverified';
            return ['success' => true, 'message' => "Tour guide $action successfully"];
        } else {
            return ['success' => false, 'message' => 'Failed to update verification status: ' . $stmt->error];
        }
    } catch (Exception $e) {
        return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
    }
}

function deleteTourGuide($conn, $guideId)
{
    try {
        // Delete dependencies first to avoid foreign key constraint failures
        $conn->begin_transaction();

        $stmt = $conn->prepare("DELETE FROM guide_destinations WHERE guide_id = ?");
        $stmt->bind_param("i", $guideId);
        $stmt->execute();
        $stmt->close();

        $stmt = $conn->prepare("DELETE FROM tour_guides WHERE id = ?");
        $stmt->bind_param("i", $guideId);

        if ($stmt->execute()) {
            $stmt->close();
            $conn->commit();
            return ['success' => true, 'message' => 'Tour guide deleted successfully'];
        }

        $error = $stmt->error;
        $stmt->close();
        $conn->rollback();
        return ['success' => false, 'message' => 'Failed to delete tour guide: ' . $error];
    } catch (Exception $e) {
        if ($conn) {
            try {
                $conn->rollback();
            } catch (Exception $rollbackError) {
                // ignore
            }
        }
        return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
    }
}

function getTourGuide($conn, $guideId)
{
    try {
        $stmt = $conn->prepare("SELECT * FROM tour_guides WHERE id = ?");
        $stmt->bind_param("i", $guideId);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            return ['success' => true, 'data' => $result->fetch_assoc()];
        } else {
            return ['success' => false, 'message' => 'Tour guide not found'];
        }
    } catch (Exception $e) {
        return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
    }
}

function getActiveDestinations($conn)
{
    try {
        $stmt = $conn->prepare("SELECT id, name FROM tourist_spots WHERE status = 'active' ORDER BY name ASC");
        $stmt->execute();
        $result = $stmt->get_result();
        
        $destinations = [];
        while ($row = $result->fetch_assoc()) {
            $destinations[] = $row;
        }
        
        return ['success' => true, 'data' => $destinations];
    } catch (Exception $e) {
        return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
    }
}

function getGuideDestinations($conn, $guideId)
{
    try {
        $stmt = $conn->prepare("SELECT destination_id FROM guide_destinations WHERE guide_id = ?");
        $stmt->bind_param("i", $guideId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $destinations = [];
        while ($row = $result->fetch_assoc()) {
            $destinations[] = $row['destination_id'];
        }
        
        return ['success' => true, 'data' => $destinations];
    } catch (Exception $e) {
        return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
    }
}

function getTourGuidesList($conn, $page = 1, $limit = 15, $search = '')
{
    // Check if connection is valid
    if (!$conn) {
        return [
            'guides' => [],
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

    // Get tour guides with pagination
    $guidesQuery = "SELECT tg.*, u.first_name, u.last_name, u.email, tg.contact_number, tg.specialty, tg.rating, tg.review_count, tg.price_range, tg.languages, tg.areas_of_expertise, tg.experience_years, tg.group_size, tg.total_tours, tg.verified FROM tour_guides tg LEFT JOIN users u ON tg.user_id = u.id WHERE 1=1";

    if ($search) {
        $guidesQuery .= " AND (tg.name LIKE '%$search%' OR tg.specialty LIKE '%$search%' OR tg.email LIKE '%$search%')";
    }

    $guidesQuery .= " ORDER BY tg.created_at DESC LIMIT $limit OFFSET $offset";
    $guidesResult = $conn->query($guidesQuery);

    // Get total count for pagination
    $countQuery = "SELECT COUNT(*) as total FROM tour_guides WHERE 1=1";
    if ($search) {
        $countQuery .= " AND (name LIKE '%$search%' OR specialty LIKE '%$search%' OR email LIKE '%$search%')";
    }
    $countResult = $conn->query($countQuery);

    if ($guidesResult && $countResult) {
        $totalCount = $countResult->fetch_assoc()['total'];
        $totalPages = ceil($totalCount / $limit);

        $guides = [];
        while ($row = $guidesResult->fetch_assoc()) {
            $guides[] = $row;
        }

        return [
            'guides' => $guides,
            'pagination' => [
                'current_page' => $page,
                'total_pages' => $totalPages,
                'total_count' => $totalCount,
                'limit' => $limit
            ]
        ];
    } else {
        return [
            'guides' => [],
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

// Fetch tour guide settings
$tgSettings = [];
$result = $conn->query("SELECT setting_key, setting_value FROM tour_guide_settings");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $tgSettings[$row['setting_key']] = $row['setting_value'];
    }
}

// Common settings
$logoText = $dbSettings['admin_logo_text'] ?? 'SJDM ADMIN';
$moduleTitle = $tgSettings['module_title'] ?? 'Tour Guides Management';
$moduleSubtitle = $tgSettings['module_subtitle'] ?? 'Manage tour guides';
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

// Handle AJAX requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    // Debug logging
    error_log("AJAX request received. Action: " . $action);
    error_log("POST data: " . print_r($_POST, true));

    switch ($action) {
        case 'add_guide':
            $response = addTourGuide($conn, $_POST);
            echo json_encode($response);
            exit;
        case 'edit_guide':
            $response = editTourGuide($conn, $_POST);
            echo json_encode($response);
            exit;
        case 'verify_guide':
        case 'unverify_guide':
            $guideId = $_POST['guide_id'] ?? 0;
            $newStatus = ($action === 'verify_guide') ? 1 : 0;
            $response = updateGuideVerification($conn, $guideId, $newStatus);
            echo json_encode($response);
            exit;
        case 'delete_guide':
            $response = deleteTourGuide($conn, $_POST['guide_id']);
            echo json_encode($response);
            exit;
        case 'get_guide':
            $guideId = $_POST['guide_id'] ?? 0;
            error_log("Getting guide with ID: " . $guideId);
            $response = getTourGuide($conn, $guideId);
            error_log("Response: " . print_r($response, true));
            echo json_encode($response);
            exit;
        default:
            error_log("Unknown action: " . $action);
            echo json_encode(['success' => false, 'message' => 'Unknown action: ' . $action]);
            exit;
    }
}

// Pagination variables
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$limit = intval($tgSettings['default_guide_limit'] ?? 15);
$search = isset($_GET['search']) ? ($conn ? $conn->real_escape_string($_GET['search']) : '') : '';

// Get tour guides data
$guidesData = getTourGuidesList($conn, $page, $limit, $search);
$guides = $guidesData['guides'];
$pagination = $guidesData['pagination'];

// Get statistics
$stats = getAdminStats($conn);

// Map query keys to values for menu badges
$queryValues = [
    'totalUsers' => $stats['totalUsers'],
    'totalBookings' => $stats['totalBookings'],
    'totalGuides' => $stats['totalGuides']
];

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tour Guides Management | SJDM Tours Admin</title>
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

                <div class="logo" style="display: flex; align-items: center; gap: 12px;">

                    <img src="../lgo.png" alt="SJDM Tours Logo" style="height: 40px; width: 40px; object-fit: contain; border-radius: 8px;">

                    <span>SJDM ADMIN</span>

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
                                                        
                    <!-- Test Button -->
                    <button class="btn-secondary" onclick="testModal()">
                        Test Modal
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
                            <a href="javascript:void(0)" class="dropdown-item" id="adminSignoutLink" onclick="openSignOutModal()">
                                <span class="material-icons-outlined">logout</span>
                                <span>Sign Out</span>
                            </a>
                        </div>
                    </div>
                </div>
            </header>

            <div class="content-area">
                <!-- Guide Statistics -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <h3><?php echo $stats['totalGuides']; ?></h3>
                        <p>Total Tour Guides</p>
                    </div>
                    <div class="stat-card">
                        <h3><?php echo count(array_filter($guides, fn($g) => $g['verified'])); ?></h3>
                        <p>Verified Guides</p>
                    </div>
                    <div class="stat-card">
                        <h3><?php echo count(array_filter($guides, fn($g) => $g['status'] == 'active')); ?></h3>
                        <p>Active Guides</p>
                    </div>
                </div>

                <!-- Filter Tabs -->
                <div class="filter-tabs">
                    <button class="filter-tab active" onclick="showTab('tour-guides', this)" data-tab="tour-guides">
                        <span class="material-icons-outlined">tour</span>
                        Tour Guides
                    </button>
                    <button class="filter-tab" onclick="showTab('registrations', this)" data-tab="registrations">
                        <span class="material-icons-outlined">how_to_reg</span>
                        Tour Guide Registrations
                    </button>
                </div>

                <!-- Search and Filters -->
                <div class="search-bar">
                    <input type="text" id="searchInput" placeholder="Search guides by name, specialty, or email..."
                        value="<?php echo htmlspecialchars($search); ?>">
                    <button class="btn-secondary" onclick="searchGuides()">
                        <span class="material-icons-outlined">search</span>
                        Search
                    </button>
                    <button class="btn-secondary" onclick="clearSearch()">
                        <span class="material-icons-outlined">clear</span>
                        Clear
                    </button>
                </div>

                <!-- Guides Table -->
                <div class="table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Photo</th>
                                <th>Full Name</th>
                                <th>Specialty</th>
                                <th>Contact</th>
                                <th>Rating</th>
                                <th>Status</th>
                                <th>Verified</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($guides as $guide): ?>
                                <tr>
                                    <td>
                                        <?php if ($guide['photo_url']): ?>
                                            <img src="<?php echo htmlspecialchars($guide['photo_url']); ?>"
                                                alt="<?php echo htmlspecialchars($guide['name']); ?>" class="guide-photo">
                                        <?php else: ?>
                                            <div class="guide-photo"
                                                style="background: var(--bg-light); display: flex; align-items: center; justify-content: center;">
                                                <span class="material-icons-outlined">person</span>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <strong><?php echo htmlspecialchars($guide['name']); ?></strong><br>
                                        <small><?php echo htmlspecialchars($guide['email']); ?></small>
                                    </td>
                                    <td><?php echo htmlspecialchars($guide['specialty']); ?></td>
                                    <td>
                                        <?php echo htmlspecialchars($guide['contact_number']); ?><br>
                                        <small><?php echo htmlspecialchars($guide['languages']); ?></small>
                                    </td>
                                    <td>
                                        <div class="rating">
                                            <?php echo number_format($guide['rating'], 1); ?> ⭐
                                            <small>(<?php echo $guide['review_count']; ?> reviews)</small>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="status-badge status-<?php echo $guide['status']; ?>">
                                            <?php echo ucfirst($guide['status']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span
                                            class="status-badge <?php echo $guide['verified'] ? 'verified-badge' : 'unverified-badge'; ?>">
                                            <?php echo $guide['verified'] ? 'Verified' : 'Unverified'; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="action-buttons">
                                            <button class="btn-icon" onclick="viewGuide(<?php echo $guide['id']; ?>)"
                                                title="View">
                                                <span class="material-icons-outlined">visibility</span>
                                            </button>
                                            <button class="btn-icon" onclick="toggleVerification(this, <?php echo $guide['id']; ?>, <?php echo $guide['verified']; ?>)"
                                                title="<?php echo $guide['verified'] ? 'Unverify Guide' : 'Verify Guide'; ?>">
                                                <span class="material-icons-outlined"><?php echo $guide['verified'] ? 'verified_user' : 'person_add'; ?></span>
                                            </button>
                                            <button class="btn-icon" onclick="showDeleteGuideModal(<?php echo $guide['id']; ?>)"
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

    <!-- Add Tour Guide Modal -->
    <div id="addGuideModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Add New Tour Guide</h2>
                <button class="modal-close" onclick="closeAddGuideModal()">
                    <span class="material-icons-outlined">close</span>
                </button>
            </div>
            <form id="addGuideForm" onsubmit="handleAddGuide(event)">
                <div class="modal-body">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="guideName">Name *</label>
                            <input type="text" id="guideName" name="name" required>
                        </div>
                        <div class="form-group">
                            <label for="guideEmail">Email *</label>
                            <input type="email" id="guideEmail" name="email" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="guideSpecialty">Specialty *</label>
                            <input type="text" id="guideSpecialty" name="specialty" required>
                        </div>
                        <div class="form-group">
                            <label for="guideCategory">Category *</label>
                            <select id="guideCategory" name="category" required>
                                <option value="">Select Category</option>
                                <option value="Adventure">Adventure</option>
                                <option value="Cultural">Cultural</option>
                                <option value="Nature">Nature</option>
                                <option value="Historical">Historical</option>
                                <option value="Food & Cuisine">Food & Cuisine</option>
                                <option value="Photography">Photography</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="guideDescription">Description</label>
                        <textarea id="guideDescription" name="description" rows="3"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="guideBio">Bio</label>
                        <textarea id="guideBio" name="bio" rows="4"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="guideAreasOfExpertise">Areas of Expertise</label>
                        <input type="text" id="guideAreasOfExpertise" name="areas_of_expertise" placeholder="e.g., Mountain trekking, Local history, Photography">
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="guideContactNumber">Contact Number *</label>
                            <input type="tel" id="guideContactNumber" name="contact_number" required>
                        </div>
                        <div class="form-group">
                            <label for="guideLanguages">Languages</label>
                            <input type="text" id="guideLanguages" name="languages" placeholder="e.g., English, Tagalog, Japanese">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="guideExperience">Experience (Years)</label>
                            <input type="number" id="guideExperience" name="experience_years" min="0" max="50">
                        </div>
                        <div class="form-group">
                            <label for="guideGroupSize">Max Group Size</label>
                            <input type="number" id="guideGroupSize" name="group_size" min="1" max="100">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="guidePriceRange">Price Range</label>
                            <select id="guidePriceRange" name="price_range">
                                <option value="">Select Range</option>
                                <option value="Budget">Budget (₱500-1000)</option>
                                <option value="Mid-range">Mid-range (₱1000-3000)</option>
                                <option value="Premium">Premium (₱3000-5000)</option>
                                <option value="Luxury">Luxury (₱5000+)</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="guidePhotoUrl">Photo URL</label>
                            <input type="url" id="guidePhotoUrl" name="photo_url" placeholder="https://example.com/photo.jpg">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="guideRating">Rating</label>
                            <input type="number" id="guideRating" name="rating" min="0" max="5" step="0.1" value="0">
                        </div>
                        <div class="form-group">
                            <label for="guideReviewCount">Review Count</label>
                            <input type="number" id="guideReviewCount" name="review_count" min="0" value="0">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="guideSchedules">Schedules</label>
                        <textarea id="guideSchedules" name="schedules" rows="2" placeholder="e.g., Monday-Friday: 9AM-5PM, Weekends: 8AM-6PM"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="guideTotalTours">Total Tours Completed</label>
                        <input type="number" id="guideTotalTours" name="total_tours" min="0" value="0">
                    </div>
                    <div class="form-group">
                        <label class="checkbox-label">
                            <input type="checkbox" id="guideVerified" name="verified">
                            <span class="checkmark"></span>
                            Verified Guide
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-secondary" onclick="closeAddGuideModal()">Cancel</button>
                    <button type="submit" class="btn-primary">Add Tour Guide</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Verify/Unverify Tour Guide Modal -->
    <div id="verifyGuideModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 id="verifyGuideModalTitle">Update Verification</h2>
                <button class="modal-close" onclick="closeVerifyGuideModal()">
                    <span class="material-icons-outlined">close</span>
                </button>
            </div>
            <form id="verifyGuideForm" onsubmit="handleVerifyGuide(event)">
                <div class="modal-body">
                    <input type="hidden" id="verifyGuideId" name="guide_id">
                    <input type="hidden" id="verifyGuideAction" name="action">
                    <p id="verifyGuideModalText">Are you sure you want to continue?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-secondary" onclick="closeVerifyGuideModal()">Cancel</button>
                    <button type="submit" class="btn-primary" id="verifyGuideConfirmBtn">Confirm</button>
                </div>
            </form>
        </div>
    </div>

    <!-- View Tour Guide Modal -->
    <div id="viewGuideModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Tour Guide Application Details</h2>
                <button class="modal-close" onclick="closeViewGuideModal()">
                    <span class="material-icons-outlined">close</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="modal-container">
                    <div class="guide-details-container">
                        <!-- Guide Photo and Basic Info -->
                        <div class="guide-header-section">
                            <div class="guide-photo-container">
                                <div id="viewGuidePhoto" class="guide-photo-large">
                                    <span class="material-icons-outlined">person</span>
                                </div>
                            </div>
                            <div class="guide-basic-info">
                                <h3 id="viewGuideName"></h3>
                                <p id="viewGuideEmail" class="guide-email"></p>
                                <div class="guide-badges">
                                    <span id="viewGuideStatus" class="status-badge"></span>
                                </div>
                            </div>
                        </div>

                        <!-- Contact Information -->
                        <div class="info-section">
                            <h4><span class="material-icons-outlined">contact_phone</span> Contact Information</h4>
                            <div class="info-grid">
                                <div class="info-item">
                                    <label>Phone Number:</label>
                                    <span id="viewGuidePhone"></span>
                                </div>
                                <div class="info-item">
                                    <label>Email:</label>
                                    <span id="viewGuideEmailDetail"></span>
                                </div>
                            </div>
                        </div>

                        <!-- Professional Information -->
                        <div class="info-section">
                            <h4><span class="material-icons-outlined">work</span> Professional Information</h4>
                            <div class="info-grid">
                                <div class="info-item">
                                    <label>Specialization:</label>
                                    <span id="viewGuideSpecialty"></span>
                                </div>
                            </div>
                        </div>

                        <!-- Application Information -->
                        <div class="info-section">
                            <h4><span class="material-icons-outlined">description</span> Application Details</h4>
                            <div class="info-grid">
                                <div class="info-item">
                                    <label>Application Date:</label>
                                    <span id="viewGuideCreatedDate"></span>
                                </div>
                                <div class="info-item">
                                    <label>Resume/CV:</label>
                                    <span id="viewGuideResume"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-secondary" onclick="closeViewGuideModal()">Close</button>
            </div>
        </div>
    </div>

    <!-- Delete Tour Guide Modal -->
    <div id="deleteGuideModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Delete Tour Guide</h2>
                <button class="modal-close" onclick="closeDeleteGuideModal()">
                    <span class="material-icons-outlined">close</span>
                </button>
            </div>
            <form id="deleteGuideForm" onsubmit="handleDeleteGuide(event)">
                <div class="modal-body">
                    <input type="hidden" id="deleteGuideId" name="id">
                    <p>Are you sure you want to delete this tour guide?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-secondary" onclick="closeDeleteGuideModal()">Cancel</button>
                    <button type="submit" class="btn-primary">Delete</button>
                </div>
            </form>
        </div>
    </div>

    <script src="admin-script.js"></script>
    <script src="admin-profile-dropdown.js"></script>
    <script>
        function searchGuides() {
            const searchValue = document.getElementById('searchInput').value;
            window.location.href = `?search=${encodeURIComponent(searchValue)}`;
        }

        function clearSearch() {
            document.getElementById('searchInput').value = '';
            window.location.href = '?';
        }

        function showTab(tabName, element) {
            // Remove active class from all tabs
            document.querySelectorAll('.filter-tab').forEach(tab => {
                tab.classList.remove('active');
            });
            
            // Add active class to clicked tab
            element.classList.add('active');
            
            // Handle tab switching
            if (tabName === 'registrations') {
                // Redirect to tour guide registrations page
                window.location.href = 'tour-guide-registrations.php';
            } else {
                // Update URL with tab parameter for other tabs
                const currentUrl = new URL(window.location.href);
                currentUrl.searchParams.delete('tab');
                currentUrl.searchParams.set('tab', tabName);
                window.location.href = currentUrl.toString();
            }
        }

        function searchGuides() {
            const searchValue = document.getElementById('searchInput').value;
            const activeTab = document.querySelector('.filter-tab.active')?.getAttribute('data-tab') || 'all';
            window.location.href = `?search=${encodeURIComponent(searchValue)}&tab=${activeTab}`;
        }

        function goToPage(page) {
            const searchValue = document.getElementById('searchInput').value;
            const url = searchValue ? `?page=${page}&search=${encodeURIComponent(searchValue)}` : `?page=${page}`;
            window.location.href = url;
        }

        function ucfirst(str) {
            return str ? str.charAt(0).toUpperCase() + str.slice(1) : '';
        }

        function formatDate(dateString) {
            const date = new Date(dateString);
            return date.toLocaleDateString('en-US', {
                year: 'numeric',
                month: 'long',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
        }

        function viewGuide(guideId) {
            console.log('Viewing guide with ID:', guideId);
            
            // Fetch guide data and populate view modal
            fetch('tour-guides.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `action=get_guide&guide_id=${guideId}`
            })
            .then(response => {
                console.log('Response status:', response.status);
                console.log('Response headers:', response.headers);
                return response.json();
            })
            .then(data => {
                console.log('Response data:', data);
                if (data.success) {
                    populateViewModal(data.data);
                    const modal = document.getElementById('viewGuideModal');
                    modal.style.display = 'block';
                    modal.classList.add('show');
                    document.body.style.overflow = 'hidden';
                } else {
                    console.error('Server error:', data.message);
                    showErrorAnimation(data.message);
                }
            })
            .catch(error => {
                console.error('Fetch error:', error);
                showErrorAnimation('Error fetching guide data: ' + error.message);
            });
        }

        function populateViewModal(guide) {
            // Basic Info
            const nameElement = document.getElementById('viewGuideName');
            if (nameElement) {
                nameElement.textContent = guide.name || 'N/A';
            }
            
            const emailElement = document.getElementById('viewGuideEmail');
            if (emailElement) {
                emailElement.textContent = guide.email || 'N/A';
            }
            
            const emailDetailElement = document.getElementById('viewGuideEmailDetail');
            if (emailDetailElement) {
                emailDetailElement.textContent = guide.email || 'N/A';
            }
            
            // Status Badges
            const statusElement = document.getElementById('viewGuideStatus');
            if (statusElement) {
                statusElement.textContent = guide.status ? ucfirst(guide.status) : 'Unknown';
                statusElement.className = `status-badge status-${guide.status || 'unknown'}`;
            }
            
            const verifiedElement = document.getElementById('viewGuideVerified');
            if (verifiedElement) {
                verifiedElement.textContent = guide.verified == 1 ? 'Verified' : 'Unverified';
                verifiedElement.className = `status-badge ${guide.verified == 1 ? 'verified-badge' : 'unverified-badge'}`;
            }
            
            // Contact Information
            const phoneElement = document.getElementById('viewGuidePhone');
            if (phoneElement) {
                phoneElement.textContent = guide.contact_number || 'N/A';
            }
            
            // Professional Information
            const specialtyElement = document.getElementById('viewGuideSpecialty');
            if (specialtyElement) {
                specialtyElement.textContent = guide.specialty || 'N/A';
            }
            
            // Description
            const descriptionElement = document.getElementById('viewGuideDescription');
            if (descriptionElement) {
                descriptionElement.textContent = guide.description || 'No description available';
            }
            
            // Application Info
            const createdDateElement = document.getElementById('viewGuideCreatedDate');
            if (createdDateElement) {
                if (!guide.created_at) {
                    createdDateElement.textContent = 'N/A';
                } else if (typeof formatDate === 'function') {
                    createdDateElement.textContent = formatDate(guide.created_at);
                } else {
                    const d = new Date(guide.created_at);
                    createdDateElement.textContent = isNaN(d.getTime()) ? guide.created_at : d.toLocaleString();
                }
            }
            
            const resumeElement = document.getElementById('viewGuideResume');
            if (resumeElement) {
                if (guide.resume) {
                    // Check if it's a Google Drive link
                    if (guide.resume.includes('drive.google.com')) {
                        resumeElement.innerHTML = `<a href="${guide.resume}" target="_blank" class="resume-link">View Resume/CV</a>`;
                    } else {
                        resumeElement.innerHTML = `<a href="${guide.resume}" target="_blank" class="resume-link">View Resume/CV</a>`;
                    }
                } else {
                    resumeElement.textContent = 'No resume uploaded';
                }
            }
            
            // Guide Photo
            const photoElement = document.getElementById('viewGuidePhoto');
            if (photoElement) {
                if (guide.photo_url) {
                    photoElement.innerHTML = `<img src="${guide.photo_url}" alt="${guide.name}" class="guide-photo-img">`;
                } else {
                    photoElement.innerHTML = '<span class="material-icons-outlined">person</span>';
                }
            }
            
            // Store guide ID for potential edit action
            const modal = document.getElementById('viewGuideModal');
            if (modal) {
                modal.dataset.guideId = guide.id;
            }
        }

        function closeViewGuideModal() {
            const modal = document.getElementById('viewGuideModal');
            modal.style.display = 'none';
            modal.classList.remove('show');
            document.body.style.overflow = 'auto';
        }

        function showDeleteGuideModal(guideId) {
            document.getElementById('deleteGuideId').value = guideId;
            const modal = document.getElementById('deleteGuideModal');
            modal.style.display = 'block';
            modal.classList.add('show');
            document.body.style.overflow = 'hidden';
        }

        function closeDeleteGuideModal() {
            const modal = document.getElementById('deleteGuideModal');
            modal.style.display = 'none';
            modal.classList.remove('show');
            document.body.style.overflow = 'auto';
        }

        function showVerifyGuideModal(guideId, currentStatus, triggerBtn) {
            const willVerify = !currentStatus;
            const action = willVerify ? 'verify_guide' : 'unverify_guide';

            document.getElementById('verifyGuideId').value = guideId;
            document.getElementById('verifyGuideAction').value = action;

            const titleEl = document.getElementById('verifyGuideModalTitle');
            const textEl = document.getElementById('verifyGuideModalText');
            const confirmBtn = document.getElementById('verifyGuideConfirmBtn');
            const modal = document.getElementById('verifyGuideModal');

            if (titleEl) titleEl.textContent = willVerify ? 'Verify Tour Guide' : 'Unverify Tour Guide';
            if (textEl) {
                textEl.textContent = willVerify
                    ? 'Are you sure you want to verify this guide? They will appear in the user booking portal.'
                    : 'Are you sure you want to unverify this guide? They will no longer appear in the user booking portal.';
            }
            if (confirmBtn) confirmBtn.textContent = willVerify ? 'Verify' : 'Unverify';

            if (modal) {
                modal.dataset.triggerBtnId = '';
                if (triggerBtn && triggerBtn.id) {
                    modal.dataset.triggerBtnId = triggerBtn.id;
                }
                modal.style.display = 'block';
                modal.classList.add('show');
                document.body.style.overflow = 'hidden';
            }
        }

        function closeVerifyGuideModal() {
            const modal = document.getElementById('verifyGuideModal');
            if (!modal) return;
            modal.style.display = 'none';
            modal.classList.remove('show');
            document.body.style.overflow = 'auto';
        }

        function testModal() {
            console.log('Test button clicked');
            const modal = document.getElementById('addGuideModal');
            console.log('Modal element:', modal);
            if (modal) {
                modal.style.display = 'block';
                modal.classList.add('show');
                document.body.style.overflow = 'hidden';
                console.log('Modal displayed with display: block');
            } else {
                console.error('Modal element not found!');
            }
        }

        function showAddGuideModal() {
            console.log('Opening modal...');
            const modal = document.getElementById('addGuideModal');
            if (modal) {
                modal.style.display = 'block';
                modal.classList.add('show');
                document.body.style.overflow = 'hidden';
                console.log('Modal should now be visible');
            } else {
                console.error('Modal not found!');
            }
        }

        function closeAddGuideModal() {
            console.log('Closing modal...');
            const modal = document.getElementById('addGuideModal');
            if (modal) {
                modal.style.display = 'none';
                modal.classList.remove('show');
                document.body.style.overflow = 'auto';
                const form = document.getElementById('addGuideForm');
                if (form) {
                    form.reset();
                }
                console.log('Modal closed successfully');
            } else {
                console.error('Modal element not found when trying to close!');
            }
        }

        // Enhanced close modal when clicking outside for all modals
        window.onclick = function(event) {
            const addModal = document.getElementById('addGuideModal');
            const viewModal = document.getElementById('viewGuideModal');
            const deleteModal = document.getElementById('deleteGuideModal');
            const verifyModal = document.getElementById('verifyGuideModal');
            
            if (event.target === addModal) {
                closeAddGuideModal();
            } else if (event.target === viewModal) {
                closeViewGuideModal();
            } else if (event.target === deleteModal) {
                closeDeleteGuideModal();
            } else if (event.target === verifyModal) {
                closeVerifyGuideModal();
            }
        }

        // Close modal with Escape key for all modals
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                const addModal = document.getElementById('addGuideModal');
                const viewModal = document.getElementById('viewGuideModal');
                const deleteModal = document.getElementById('deleteGuideModal');
                const verifyModal = document.getElementById('verifyGuideModal');
                
                if (addModal && addModal.style.display === 'block') {
                    closeAddGuideModal();
                } else if (viewModal && viewModal.style.display === 'block') {
                    closeViewGuideModal();
                } else if (deleteModal && deleteModal.style.display === 'block') {
                    closeDeleteGuideModal();
                } else if (verifyModal && verifyModal.style.display === 'block') {
                    closeVerifyGuideModal();
                }
            }
        });

        function handleAddGuide(event) {
            event.preventDefault();
            
            // Show loading state
            const submitBtn = event.target.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<span class="material-icons-outlined">hourglass_empty</span> Adding...';
            submitBtn.disabled = true;
            
            const formData = new FormData(event.target);
            const data = Object.fromEntries(formData.entries());
            
            // Convert checkbox to boolean
            data.verified = formData.has('verified') ? 1 : 0;
            
            // Set default values for empty fields
            data.rating = data.rating || 0;
            data.review_count = data.review_count || 0;
            data.experience_years = data.experience_years || 0;
            data.group_size = data.group_size || 10;
            data.total_tours = data.total_tours || 0;
            
            // Add action for server-side handling
            data.action = 'add_guide';
            
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
                    // Show success animation
                    showSuccessAnimation();
                    
                    // Reset form and close modal after delay
                    setTimeout(() => {
                        closeAddGuideModal();
                        location.reload();
                    }, 2000);
                } else {
                    // Show error animation
                    showErrorAnimation(result.message);
                    
                    // Reset button
                    submitBtn.innerHTML = originalText;
                    submitBtn.disabled = false;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showErrorAnimation('An error occurred while adding the tour guide.');
                
                // Reset button
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            });
        }

        function showSuccessAnimation() {
            // Create success overlay
            const successOverlay = document.createElement('div');
            successOverlay.className = 'success-overlay';
            successOverlay.innerHTML = `
                <div class="success-content">
                    <div class="success-icon">
                        <span class="material-icons-outlined">check_circle</span>
                    </div>
                    <h3>Success!</h3>
                    <p>Tour guide added successfully</p>
                    <div class="success-progress"></div>
                </div>
            `;
            
            document.body.appendChild(successOverlay);
            
            // Animate in
            setTimeout(() => {
                successOverlay.classList.add('show');
            }, 100);
            
            // Remove after animation
            setTimeout(() => {
                if (successOverlay.parentNode) {
                    successOverlay.remove();
                }
            }, 2000);
        }

        function showErrorAnimation(message) {
            // Create error notification
            const errorNotification = document.createElement('div');
            errorNotification.className = 'error-notification';
            errorNotification.innerHTML = `
                <div class="error-content">
                    <div class="error-icon">
                        <span class="material-icons-outlined">error</span>
                    </div>
                    <div class="error-message">
                        <strong>Error</strong>
                        <p>${message}</p>
                    </div>
                    <button class="error-close" onclick="this.parentElement.parentElement.remove()">
                        <span class="material-icons-outlined">close</span>
                    </button>
                </div>
            `;
            
            document.body.appendChild(errorNotification);
            
            // Animate in
            setTimeout(() => {
                errorNotification.classList.add('show');
            }, 100);
            
            // Auto remove after 5 seconds
            setTimeout(() => {
                if (errorNotification.parentNode) {
                    errorNotification.classList.add('hide');
                    setTimeout(() => errorNotification.remove(), 300);
                }
            }, 5000);
        }

        // Search on Enter key
        document.getElementById('searchInput').addEventListener('keypress', function (e) {
            if (e.key === 'Enter') {
                searchGuides();
            }
        });

        function toggleVerification(btn, guideId, currentStatus) {
            if (btn && !btn.id) {
                btn.id = `verifyBtn_${guideId}`;
            }
            showVerifyGuideModal(guideId, !!currentStatus, btn);
        }

        function handleVerifyGuide(event) {
            event.preventDefault();

            const modal = document.getElementById('verifyGuideModal');
            const formData = new FormData(event.target);
            const guideId = formData.get('guide_id');
            const action = formData.get('action');

            const confirmBtn = document.getElementById('verifyGuideConfirmBtn');
            const originalText = confirmBtn ? confirmBtn.textContent : 'Confirm';

            if (confirmBtn) {
                confirmBtn.innerHTML = '<span class="material-icons-outlined">hourglass_empty</span> Processing...';
                confirmBtn.disabled = true;
            }

            fetch('', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `action=${encodeURIComponent(action)}&guide_id=${encodeURIComponent(guideId)}`
            })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    showSuccessAnimation();
                    setTimeout(() => {
                        closeVerifyGuideModal();
                        location.reload();
                    }, 1500);
                } else {
                    showErrorAnimation(result.message);
                    if (confirmBtn) {
                        confirmBtn.textContent = originalText;
                        confirmBtn.disabled = false;
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showErrorAnimation('An error occurred while updating verification status.');
                if (confirmBtn) {
                    confirmBtn.textContent = originalText;
                    confirmBtn.disabled = false;
                }
            });
        }

        function handleDeleteGuide(event) {
            event.preventDefault();
            
            // Show loading state
            const submitBtn = event.target.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<span class="material-icons-outlined">hourglass_empty</span> Deleting...';
            submitBtn.disabled = true;
            
            const formData = new FormData(event.target);
            const data = Object.fromEntries(formData.entries());
            
            // Add action for server-side handling
            data.action = 'delete_guide';
            data.guide_id = data.id;
            
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
                    // Show success animation
                    showSuccessAnimation();
                    
                    // Reset form and close modal after delay
                    setTimeout(() => {
                        closeDeleteGuideModal();
                        location.reload();
                    }, 2000);
                } else {
                    // Show error animation
                    showErrorAnimation(result.message);
                    
                    // Reset button
                    submitBtn.innerHTML = originalText;
                    submitBtn.disabled = false;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showErrorAnimation('An error occurred while deleting the tour guide.');
                
                // Reset button
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            });
        }

        </script>

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

        <script>
            // Open Sign Out modal when clicking Sign Out in dropdown
            document.addEventListener('DOMContentLoaded', function() {
                const signoutLink = document.getElementById('adminSignoutLink');
                if (signoutLink) {
                    signoutLink.addEventListener('click', function(e) {
                        e.preventDefault();
                        // Close dropdown
                        const menu = document.getElementById('adminProfileMenu');
                        if (menu) menu.classList.remove('show');
                        // Open Sign Out modal
                        const modal = document.getElementById('signOutModal');
                        if (modal) {
                            modal.classList.add('show');
                            document.body.style.overflow = 'hidden';
                    });
                }
            });

            function closeSignOutModal() {
                const modal = document.getElementById('signOutModal');
                if (modal) {
                    modal.classList.remove('show');
                    document.body.style.overflow = 'auto';
                }
            }

            function openSignOutModal() {
                const modal = document.getElementById('signOutModal');
                if (modal) {
                    modal.classList.add('show');
                    document.body.style.overflow = 'hidden'; // Prevent background scrolling
                }
            }

            function confirmSignOut() {
                // Redirect to logout page
                window.location.href = 'logout.php';
            }

            // Close Sign Out modal on overlay click or Escape
            window.addEventListener('click', function(event) {
                const modal = document.getElementById('signOutModal');
                if (modal && event.target === modal) {
                    closeSignOutModal();
                }
            });

            document.addEventListener('keydown', function(event) {
                if (event.key === 'Escape') {
                    const modal = document.getElementById('signOutModal');
                    if (modal && modal.classList.contains('show')) {
                        closeSignOutModal();
                    }
                }
            });
        </script>
</body>

</html>