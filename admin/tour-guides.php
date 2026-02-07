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

function deleteTourGuide($conn, $guideId)
{
    try {
        $stmt = $conn->prepare("DELETE FROM tour_guides WHERE id = ?");
        $stmt->bind_param("i", $guideId);

        if ($stmt->execute()) {
            return ['success' => true, 'message' => 'Tour guide deleted successfully'];
        } else {
            return ['success' => false, 'message' => 'Failed to delete tour guide'];
        }
    } catch (Exception $e) {
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
    $guidesQuery = "SELECT tg.* FROM tour_guides tg WHERE 1=1";

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

    switch ($action) {
        case 'add_guide':
            $response = addTourGuide($conn, $_POST);
            echo json_encode($response);
            exit;
        case 'edit_guide':
            $response = editTourGuide($conn, $_POST);
            echo json_encode($response);
            exit;
        case 'delete_guide':
            $response = deleteTourGuide($conn, $_POST['guide_id']);
            echo json_encode($response);
            exit;
        case 'get_guide':
            $response = getTourGuide($conn, $_POST['guide_id']);
            echo json_encode($response);
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
                <div class="logo">
                    <div class="mark-icon"><?php echo strtoupper(substr($logoText, 0, 1) ?: 'A'); ?></div>
                    <span><?php echo $logoText; ?></span>
                </div>
            </div>
            
            <nav class="sidebar-nav">
                <?php foreach ($menuItems as $item):
                    // Skip hotels and settings menu items
                    if (stripos($item['menu_name'], 'hotels') !== false || stripos($item['menu_url'], 'hotels') !== false ||
                        stripos($item['menu_name'], 'settings') !== false || stripos($item['menu_url'], 'settings') !== false) {
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
                    <button class="btn-primary" onclick="showAddGuideModal()">
                        <span class="material-icons-outlined">add</span>
                        Add Tour Guide
                    </button>
                    
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
                            <a href="logout.php" class="dropdown-item" id="adminSignoutLink">
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
                                <th>Name</th>
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
                                            <button class="btn-icon" onclick="showEditGuideModal(<?php echo $guide['id']; ?>)"
                                                title="Edit">
                                                <span class="material-icons-outlined">edit</span>
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

    <!-- Edit Tour Guide Modal -->
    <div id="editGuideModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Edit Tour Guide</h2>
                <button class="modal-close" onclick="closeEditGuideModal()">
                    <span class="material-icons-outlined">close</span>
                </button>
            </div>
            <form id="editGuideForm" onsubmit="handleEditGuide(event)">
                <div class="modal-body">
                    <input type="hidden" id="editGuideId" name="id">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="editGuideName">Name *</label>
                            <input type="text" id="editGuideName" name="name" required>
                        </div>
                        <div class="form-group">
                            <label for="editGuideEmail">Email *</label>
                            <input type="email" id="editGuideEmail" name="email" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="editGuideSpecialty">Specialty *</label>
                            <input type="text" id="editGuideSpecialty" name="specialty" required>
                        </div>
                        <div class="form-group">
                            <label for="editGuideCategory">Category *</label>
                            <select id="editGuideCategory" name="category" required>
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
                        <label for="editGuideDescription">Description</label>
                        <textarea id="editGuideDescription" name="description" rows="3"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="editGuideBio">Bio</label>
                        <textarea id="editGuideBio" name="bio" rows="4"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="editGuideAreasOfExpertise">Areas of Expertise</label>
                        <input type="text" id="editGuideAreasOfExpertise" name="areas_of_expertise" placeholder="e.g., Mountain trekking, Local history, Photography">
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="editGuideContactNumber">Contact Number *</label>
                            <input type="tel" id="editGuideContactNumber" name="contact_number" required>
                        </div>
                        <div class="form-group">
                            <label for="editGuideLanguages">Languages</label>
                            <input type="text" id="editGuideLanguages" name="languages" placeholder="e.g., English, Tagalog, Japanese">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="editGuideExperience">Experience (Years)</label>
                            <input type="number" id="editGuideExperience" name="experience_years" min="0" max="50">
                        </div>
                        <div class="form-group">
                            <label for="editGuideGroupSize">Max Group Size</label>
                            <input type="number" id="editGuideGroupSize" name="group_size" min="1" max="100">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="editGuidePriceRange">Price Range</label>
                            <select id="editGuidePriceRange" name="price_range">
                                <option value="">Select Range</option>
                                <option value="Budget">Budget (₱500-1000)</option>
                                <option value="Mid-range">Mid-range (₱1000-3000)</option>
                                <option value="Premium">Premium (₱3000-5000)</option>
                                <option value="Luxury">Luxury (₱5000+)</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="editGuidePhotoUrl">Photo URL</label>
                            <input type="url" id="editGuidePhotoUrl" name="photo_url" placeholder="https://example.com/photo.jpg">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="editGuideRating">Rating</label>
                            <input type="number" id="editGuideRating" name="rating" min="0" max="5" step="0.1" value="0">
                        </div>
                        <div class="form-group">
                            <label for="editGuideReviewCount">Review Count</label>
                            <input type="number" id="editGuideReviewCount" name="review_count" min="0" value="0">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="editGuideSchedules">Schedules</label>
                        <textarea id="editGuideSchedules" name="schedules" rows="2" placeholder="e.g., Monday-Friday: 9AM-5PM, Weekends: 8AM-6PM"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="editGuideTotalTours">Total Tours Completed</label>
                        <input type="number" id="editGuideTotalTours" name="total_tours" min="0" value="0">
                    </div>
                    <div class="form-group">
                        <label class="checkbox-label">
                            <input type="checkbox" id="editGuideVerified" name="verified">
                            <span class="checkmark"></span>
                            Verified Guide
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-secondary" onclick="closeEditGuideModal()">Cancel</button>
                    <button type="submit" class="btn-primary">Save Changes</button>
                </div>
            </form>
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

        function goToPage(page) {
            const searchValue = document.getElementById('searchInput').value;
            const url = searchValue ? `?page=${page}&search=${encodeURIComponent(searchValue)}` : `?page=${page}`;
            window.location.href = url;
        }

        function viewGuide(guideId) {
            // Implement view guide modal
            console.log('View guide:', guideId);
        }

        function showEditGuideModal(guideId) {
            // Fetch guide data and populate edit modal
            fetch('', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `action=get_guide&guide_id=${guideId}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    populateEditModal(data.data);
                    const modal = document.getElementById('editGuideModal');
                    modal.style.display = 'block';
                    modal.classList.add('show');
                    document.body.style.overflow = 'hidden';
                } else {
                    alert(data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error fetching guide data');
            });
        }

        function populateEditModal(guide) {
            document.getElementById('editGuideId').value = guide.id;
            document.getElementById('editGuideName').value = guide.name;
            document.getElementById('editGuideEmail').value = guide.email;
            document.getElementById('editGuideSpecialty').value = guide.specialty;
            document.getElementById('editGuideCategory').value = guide.category || '';
            document.getElementById('editGuideDescription').value = guide.description || '';
            document.getElementById('editGuideBio').value = guide.bio || '';
            document.getElementById('editGuideAreasOfExpertise').value = guide.areas_of_expertise || '';
            document.getElementById('editGuideContactNumber').value = guide.contact_number;
            document.getElementById('editGuideLanguages').value = guide.languages || '';
            document.getElementById('editGuideExperience').value = guide.experience_years || 0;
            document.getElementById('editGuideGroupSize').value = guide.group_size || 10;
            document.getElementById('editGuidePriceRange').value = guide.price_range || '';
            document.getElementById('editGuidePhotoUrl').value = guide.photo_url || '';
            document.getElementById('editGuideRating').value = guide.rating || 0;
            document.getElementById('editGuideReviewCount').value = guide.review_count || 0;
            document.getElementById('editGuideSchedules').value = guide.schedules || '';
            document.getElementById('editGuideTotalTours').value = guide.total_tours || 0;
            document.getElementById('editGuideVerified').checked = guide.verified == 1;
        }

        function closeEditGuideModal() {
            const modal = document.getElementById('editGuideModal');
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

        // Enhanced close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('addGuideModal');
            if (event.target === modal) {
                closeAddGuideModal();
            }
        }

        // Close modal with Escape key
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                const modal = document.getElementById('addGuideModal');
                if (modal && modal.style.display === 'block') {
                    closeAddGuideModal();
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

        function handleEditGuide(event) {
            event.preventDefault();
            
            // Show loading state
            const submitBtn = event.target.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<span class="material-icons-outlined">hourglass_empty</span> Saving...';
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
            
            // Add action and guide ID for server-side handling
            data.action = 'edit_guide';
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
                        closeEditGuideModal();
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
                showErrorAnimation('An error occurred while updating the tour guide.');
                
                // Reset button
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
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

        // Enhanced close modal when clicking outside for all modals
        window.onclick = function(event) {
            const addModal = document.getElementById('addGuideModal');
            const editModal = document.getElementById('editGuideModal');
            const deleteModal = document.getElementById('deleteGuideModal');
            
            if (event.target === addModal) {
                closeAddGuideModal();
            } else if (event.target === editModal) {
                closeEditGuideModal();
            } else if (event.target === deleteModal) {
                closeDeleteGuideModal();
            }
        }

        // Close modal with Escape key for all modals
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                const addModal = document.getElementById('addGuideModal');
                const editModal = document.getElementById('editGuideModal');
                const deleteModal = document.getElementById('deleteGuideModal');
                
                if (addModal && addModal.style.display === 'block') {
                    closeAddGuideModal();
                } else if (editModal && editModal.style.display === 'block') {
                    closeEditGuideModal();
                } else if (deleteModal && deleteModal.style.display === 'block') {
                    closeDeleteGuideModal();
                }
            }
        });
    </script>
</body>

</html>