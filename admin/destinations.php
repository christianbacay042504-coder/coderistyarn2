<?php
// Destinations Management Module
// This file handles tourist spot management operations with separated connections and functions

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/auth.php';

// Database connection functions
function getAdminConnection() {
    return getDatabaseConnection();
}

function initAdminAuth() {
    requireAdmin();
    return getCurrentUser();
}

function closeAdminConnection($conn) {
    closeDatabaseConnection($conn);
}

// Tourist Spots Management Functions
function addTouristSpot($conn, $data) {
    try {
        $stmt = $conn->prepare("INSERT INTO tourist_spots (name, description, category, location, address, operating_hours, entrance_fee, difficulty_level, duration, best_time_to_visit, activities, amenities, contact_info, website, image_url, rating, review_count, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'active')");
        $stmt->bind_param("ssssssssssssssssi", 
            $data['name'], $data['description'], $data['category'], $data['location'], 
            $data['address'], $data['operating_hours'], $data['entrance_fee'], 
            $data['difficulty_level'], $data['duration'], $data['best_time_to_visit'], 
            $data['activities'], $data['amenities'], $data['contact_info'], 
            $data['website'], $data['image_url'], $data['rating'], $data['review_count']
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

function editTouristSpot($conn, $data) {
    try {
        $stmt = $conn->prepare("UPDATE tourist_spots SET name = ?, description = ?, category = ?, location = ?, address = ?, operating_hours = ?, entrance_fee = ?, difficulty_level = ?, duration = ?, best_time_to_visit = ?, activities = ?, amenities = ?, contact_info = ?, website = ?, image_url = ?, rating = ?, review_count = ?, status = ? WHERE id = ?");
        $stmt->bind_param("ssssssssssssssssisi", 
            $data['name'], $data['description'], $data['category'], $data['location'], 
            $data['address'], $data['operating_hours'], $data['entrance_fee'], 
            $data['difficulty_level'], $data['duration'], $data['best_time_to_visit'], 
            $data['activities'], $data['amenities'], $data['contact_info'], 
            $data['website'], $data['image_url'], $data['rating'], $data['review_count'], 
            $data['status'], $data['spot_id']
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

function deleteTouristSpot($conn, $spotId) {
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

function getTouristSpot($conn, $spotId) {
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

function getTouristSpotsList($conn, $page = 1, $limit = 15, $search = '') {
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
    $spotsQuery = "SELECT ts.*, 
                   (SELECT COUNT(*) FROM bookings WHERE spot_id = ts.id) as total_bookings
                   FROM tourist_spots ts WHERE 1=1";
    
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

function getAdminStats($conn) {
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

// Pagination variables
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$limit = 15;
$search = isset($_GET['search']) ? ($conn ? $conn->real_escape_string($_GET['search']) : '') : '';

// Get destinations data
$spotsData = getTouristSpotsList($conn, $page, $limit, $search);
$spots = $spotsData['spots'];
$pagination = $spotsData['pagination'];

// Get statistics
$stats = getAdminStats($conn);

// Close connection
closeAdminConnection($conn);

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
        .destination-stats {
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
        .destination-table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: var(--radius-md);
            overflow: hidden;
            box-shadow: var(--shadow-sm);
        }
        .destination-table th,
        .destination-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid var(--border-color);
        }
        .destination-table th {
            background: var(--bg-light);
            font-weight: 600;
        }
        .destination-table tr:hover {
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
        .destination-image {
            width: 60px;
            height: 40px;
            border-radius: var(--radius-sm);
            object-fit: cover;
        }
        .rating {
            color: #f59e0b;
        }
        .difficulty-badge {
            padding: 2px 6px;
            border-radius: 8px;
            font-size: 0.7rem;
            font-weight: 500;
        }
        .difficulty-easy {
            background: #d1fae5;
            color: #065f46;
        }
        .difficulty-moderate {
            background: #fef3c7;
            color: #92400e;
        }
        .difficulty-challenging {
            background: #fee2e2;
            color: #991b1b;
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <div class="logo">
                    <span class="material-icons-outlined">admin_panel_settings</span>
                    <span>SJDM ADMIN</span>
                </div>
            </div>
            
            <nav class="sidebar-nav">
                <a href="dashboard.php" class="nav-item">
                    <span class="material-icons-outlined">dashboard</span>
                    <span>Dashboard</span>
                </a>
                <a href="user-management.php" class="nav-item">
                    <span class="material-icons-outlined">people</span>
                    <span>User Management</span>
                </a>
                <a href="tour-guides.php" class="nav-item">
                    <span class="material-icons-outlined">tour</span>
                    <span>Tour Guides</span>
                </a>
                <a href="destinations.php" class="nav-item active">
                    <span class="material-icons-outlined">place</span>
                    <span>Destinations</span>
                    <?php if ($stats['totalDestinations'] > 0): ?>
                        <span class="badge"><?php echo $stats['totalDestinations']; ?></span>
                    <?php endif; ?>
                </a>
                <a href="hotels.php" class="nav-item">
                    <span class="material-icons-outlined">hotel</span>
                    <span>Hotels</span>
                </a>
                <a href="bookings.php" class="nav-item">
                    <span class="material-icons-outlined">event</span>
                    <span>Bookings</span>
                </a>
                <a href="analytics.php" class="nav-item">
                    <span class="material-icons-outlined">analytics</span>
                    <span>Analytics</span>
                </a>
                <a href="reports.php" class="nav-item">
                    <span class="material-icons-outlined">description</span>
                    <span>Reports</span>
                </a>
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
                    <h1>Destinations Management</h1>
                    <p>Manage tourist spots and destinations</p>
                </div>
                
                <div class="top-bar-actions">
                    <button class="btn-primary" onclick="showAddDestinationModal()">
                        <span class="material-icons-outlined">add</span>
                        Add Destination
                    </button>
                    
                    <div class="user-profile">
                        <div class="avatar">
                            <span><?php echo strtoupper(substr($currentUser['first_name'], 0, 1)); ?></span>
                        </div>
                        <div class="user-info">
                            <p class="user-name"><?php echo htmlspecialchars($currentUser['first_name'] . ' ' . $currentUser['last_name']); ?></p>
                            <p class="user-role">Administrator</p>
                        </div>
                    </div>
                </div>
            </header>
            
            <div class="content-area">
                <!-- Destination Statistics -->
                <div class="destination-stats">
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
                    <input type="text" id="searchInput" placeholder="Search destinations by name, category, or location..." value="<?php echo htmlspecialchars($search); ?>">
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
                    <table class="destination-table">
                        <thead>
                            <tr>
                                <th>Image</th>
                                <th>Name</th>
                                <th>Category</th>
                                <th>Location</th>
                                <th>Difficulty</th>
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
                                        <img src="<?php echo htmlspecialchars($spot['image_url']); ?>" alt="<?php echo htmlspecialchars($spot['name']); ?>" class="destination-image">
                                    <?php else: ?>
                                        <div class="destination-image" style="background: var(--bg-light); display: flex; align-items: center; justify-content: center;">
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
                                    <span class="difficulty-badge difficulty-<?php echo strtolower($spot['difficulty_level']); ?>">
                                        <?php echo ucfirst($spot['difficulty_level']); ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="rating">
                                        <?php echo number_format($spot['rating'], 1); ?> ⭐
                                        <small>(<?php echo $spot['review_count']; ?> reviews)</small>
                                    </div>
                                </td>
                                <td>₱<?php echo number_format($spot['entrance_fee'], 2); ?></td>
                                <td>
                                    <span class="status-badge status-<?php echo $spot['status']; ?>">
                                        <?php echo ucfirst($spot['status']); ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <button class="btn-icon" onclick="viewDestination(<?php echo $spot['id']; ?>)" title="View">
                                            <span class="material-icons-outlined">visibility</span>
                                        </button>
                                        <button class="btn-icon" onclick="editDestination(<?php echo $spot['id']; ?>)" title="Edit">
                                            <span class="material-icons-outlined">edit</span>
                                        </button>
                                        <button class="btn-icon" onclick="deleteDestination(<?php echo $spot['id']; ?>)" title="Delete">
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
            // Implement view destination modal
            console.log('View destination:', spotId);
        }
        
        function editDestination(spotId) {
            // Implement edit destination modal
            console.log('Edit destination:', spotId);
        }
        
        function deleteDestination(spotId) {
            if (confirm('Are you sure you want to delete this destination? This action cannot be undone.')) {
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
        
        function showAddDestinationModal() {
            // Implement add destination modal
            console.log('Show add destination modal');
        }
        
        // Search on Enter key
        document.getElementById('searchInput').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                searchDestinations();
            }
        });
    </script>
</body>
</html>
