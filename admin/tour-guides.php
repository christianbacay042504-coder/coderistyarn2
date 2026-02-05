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
        $stmt = $conn->prepare("INSERT INTO tour_guides (name, specialty, category, description, bio, areas_of_expertise, rating, review_count, price_range, price_min, price_max, languages, contact_number, email, schedules, experience_years, group_size, verified, total_tours, photo_url, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'active')");
        $verified = isset($data['verified']) ? 1 : 0;
        $stmt->bind_param(
            "sssssssssssssssiisis",
            $data['name'],
            $data['specialty'],
            $data['category'],
            $data['description'],
            $data['bio'],
            $data['areas_of_expertise'],
            $data['rating'],
            $data['review_count'],
            $data['price_range'],
            $data['price_min'],
            $data['price_max'],
            $data['languages'],
            $data['contact_number'],
            $data['email'],
            $data['schedules'],
            $data['experience_years'],
            $data['group_size'],
            $verified,
            $data['total_tours'],
            $data['photo_url']
        );

        if ($stmt->execute()) {
            return ['success' => true, 'message' => 'Tour guide added successfully'];
        } else {
            return ['success' => false, 'message' => 'Failed to add tour guide'];
        }
    } catch (Exception $e) {
        return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
    }
}

function editTourGuide($conn, $data)
{
    try {
        $stmt = $conn->prepare("UPDATE tour_guides SET name = ?, specialty = ?, category = ?, description = ?, bio = ?, areas_of_expertise = ?, rating = ?, review_count = ?, price_range = ?, price_min = ?, price_max = ?, languages = ?, contact_number = ?, email = ?, schedules = ?, experience_years = ?, group_size = ?, verified = ?, total_tours = ?, photo_url = ?, status = ? WHERE id = ?");
        $verified = isset($data['verified']) ? 1 : 0;
        $stmt->bind_param(
            "sssssssssssssssiisisi",
            $data['name'],
            $data['specialty'],
            $data['category'],
            $data['description'],
            $data['bio'],
            $data['areas_of_expertise'],
            $data['rating'],
            $data['review_count'],
            $data['price_range'],
            $data['price_min'],
            $data['price_max'],
            $data['languages'],
            $data['contact_number'],
            $data['email'],
            $data['schedules'],
            $data['experience_years'],
            $data['group_size'],
            $verified,
            $data['total_tours'],
            $data['photo_url'],
            $data['status'],
            $data['guide_id']
        );

        if ($stmt->execute()) {
            return ['success' => true, 'message' => 'Tour guide updated successfully'];
        } else {
            return ['success' => false, 'message' => 'Failed to update tour guide'];
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
    <style>
        .guide-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: var(--bg-light);
            padding: 20px;
            border-radius: var(--radius-md);
            border-left: 4px solid var(--primary);
        }

        .stat-card h3 {
            margin: 0 0 10px 0;
            font-size: 2rem;
            color: var(--primary);
        }

        .stat-card p {
            margin: 0;
            color: var(--text-secondary);
        }

        .search-bar {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
        }

        .search-bar input {
            flex: 1;
            padding: 10px;
            border: 1px solid var(--border-color);
            border-radius: var(--radius-sm);
        }

        .guide-table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: var(--radius-md);
            overflow: hidden;
            box-shadow: var(--shadow-sm);
        }

        .guide-table th,
        .guide-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid var(--border-color);
        }

        .guide-table th {
            background: var(--bg-light);
            font-weight: 600;
        }

        .guide-table tr:hover {
            background: var(--bg-light);
        }

        .status-badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 0.8rem;
            font-weight: 500;
        }

        .status-active {
            background: #d1fae5;
            color: #065f46;
        }

        .status-inactive {
            background: #fee2e2;
            color: #991b1b;
        }

        .verified-badge {
            background: #dbeafe;
            color: #1e40af;
        }

        .unverified-badge {
            background: #fef3c7;
            color: #92400e;
        }

        .action-buttons {
            display: flex;
            gap: 5px;
        }

        .btn-icon {
            padding: 6px;
            border: none;
            background: transparent;
            cursor: pointer;
            border-radius: 4px;
            transition: background 0.2s;
        }

        .btn-icon:hover {
            background: var(--bg-light);
        }

        .pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
            margin-top: 20px;
        }

        .pagination button {
            padding: 8px 12px;
            border: 1px solid var(--border-color);
            background: white;
            cursor: pointer;
            border-radius: 4px;
        }

        .pagination button:hover {
            background: var(--bg-light);
        }

        .pagination button.active {
            background: var(--primary);
            color: white;
            border-color: var(--primary);
        }

        .guide-photo {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
        }

        .rating {
            color: #f59e0b;
        }
    </style>
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
                    
                    <div class="profile-dropdown">
                        <div class="profile-dropdown-toggle">
                            <div class="avatar">
                                <span><?php echo strtoupper(substr($currentUser['first_name'], 0, 1)); ?></span>
                                <div class="admin-mark-badge"><?php echo $adminInfo['admin_mark'] ?? 'A'; ?></div>
                            </div>
                            <div class="user-info">
                                <p class="user-name"><?php echo htmlspecialchars($currentUser['first_name'] . ' ' . $currentUser['last_name']); ?></p>
                                <p class="user-role"><?php echo $adminInfo['role_title']; ?></p>
                            </div>
                            <span class="material-icons-outlined dropdown-arrow">expand_more</span>
                        </div>
                    </div>
                </div>
            </header>

            <div class="content-area">
                <!-- Guide Statistics -->
                <div class="guide-stats">
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
                    <table class="guide-table">
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
                                            <button class="btn-icon" onclick="editGuide(<?php echo $guide['id']; ?>)"
                                                title="Edit">
                                                <span class="material-icons-outlined">edit</span>
                                            </button>
                                            <button class="btn-icon"
                                                onclick="toggleVerification(<?php echo $guide['id']; ?>, <?php echo $guide['verified']; ?>)"
                                                title="Toggle Verification">
                                                <span
                                                    class="material-icons-outlined"><?php echo $guide['verified'] ? 'verified' : 'gpp_maybe'; ?></span>
                                            </button>
                                            <button class="btn-icon" onclick="deleteGuide(<?php echo $guide['id']; ?>)"
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

    <script src="admin-script.js"></script>
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

        function editGuide(guideId) {
            // Implement edit guide modal
            console.log('Edit guide:', guideId);
        }

        function toggleVerification(guideId, currentStatus) {
            const newStatus = !currentStatus;
            if (confirm(`Are you sure you want to ${newStatus ? 'verify' : 'unverify'} this guide?`)) {
                fetch('', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `action=toggle_verification&guide_id=${guideId}&verified=${newStatus ? 1 : 0}`
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

        function deleteGuide(guideId) {
            if (confirm('Are you sure you want to delete this tour guide? This action cannot be undone.')) {
                fetch('', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `action=delete_guide&guide_id=${guideId}`
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

        function showAddGuideModal() {
            // Implement add guide modal
            console.log('Show add guide modal');
        }

        // Search on Enter key
        document.getElementById('searchInput').addEventListener('keypress', function (e) {
            if (e.key === 'Enter') {
                searchGuides();
            }
        });
    </script>
</body>

</html>