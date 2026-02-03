<?php
// Hotels Management Module
// This file handles hotel management operations with separated connections and functions

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

// Hotel Management Functions
function addHotel($conn, $data) {
    try {
        $stmt = $conn->prepare("INSERT INTO hotels (name, description, category, location, address, contact_info, website, email, phone, price_range, rating, review_count, amenities, services, image_url, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'active')");
        $stmt->bind_param("sssssssssssssss", 
            $data['name'], $data['description'], $data['category'], $data['location'], 
            $data['address'], $data['contact_info'], $data['website'], $data['email'], 
            $data['phone'], $data['price_range'], $data['rating'], $data['review_count'], 
            $data['amenities'], $data['services'], $data['image_url']
        );
        
        if ($stmt->execute()) {
            return ['success' => true, 'message' => 'Hotel added successfully'];
        } else {
            return ['success' => false, 'message' => 'Failed to add hotel'];
        }
    } catch (Exception $e) {
        return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
    }
}

function editHotel($conn, $data) {
    try {
        $stmt = $conn->prepare("UPDATE hotels SET name = ?, description = ?, category = ?, location = ?, address = ?, contact_info = ?, website = ?, email = ?, phone = ?, price_range = ?, rating = ?, review_count = ?, amenities = ?, services = ?, image_url = ?, status = ? WHERE id = ?");
        $stmt->bind_param("sssssssssssssssi", 
            $data['name'], $data['description'], $data['category'], $data['location'], 
            $data['address'], $data['contact_info'], $data['website'], $data['email'], 
            $data['phone'], $data['price_range'], $data['rating'], $data['review_count'], 
            $data['amenities'], $data['services'], $data['image_url'], $data['status'], 
            $data['hotel_id']
        );
        
        if ($stmt->execute()) {
            return ['success' => true, 'message' => 'Hotel updated successfully'];
        } else {
            return ['success' => false, 'message' => 'Failed to update hotel'];
        }
    } catch (Exception $e) {
        return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
    }
}

function deleteHotel($conn, $hotelId) {
    try {
        $stmt = $conn->prepare("DELETE FROM hotels WHERE id = ?");
        $stmt->bind_param("i", $hotelId);
        
        if ($stmt->execute()) {
            return ['success' => true, 'message' => 'Hotel deleted successfully'];
        } else {
            return ['success' => false, 'message' => 'Failed to delete hotel'];
        }
    } catch (Exception $e) {
        return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
    }
}

function getHotel($conn, $hotelId) {
    try {
        $stmt = $conn->prepare("SELECT * FROM hotels WHERE id = ?");
        $stmt->bind_param("i", $hotelId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            return ['success' => true, 'data' => $result->fetch_assoc()];
        } else {
            return ['success' => false, 'message' => 'Hotel not found'];
        }
    } catch (Exception $e) {
        return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
    }
}

function getHotelsList($conn, $page = 1, $limit = 15, $search = '') {
    // Check if connection is valid
    if (!$conn) {
        return [
            'hotels' => [],
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
    
    // Get hotels with pagination
    $hotelsQuery = "SELECT h.* FROM hotels h WHERE 1=1";
    
    if ($search) {
        $hotelsQuery .= " AND (h.name LIKE '%$search%' OR h.category LIKE '%$search%' OR h.location LIKE '%$search%')";
    }
    
    $hotelsQuery .= " ORDER BY h.created_at DESC LIMIT $limit OFFSET $offset";
    $hotelsResult = $conn->query($hotelsQuery);
    
    // Get total count for pagination
    $countQuery = "SELECT COUNT(*) as total FROM hotels WHERE 1=1";
    if ($search) {
        $countQuery .= " AND (name LIKE '%$search%' OR category LIKE '%$search%' OR location LIKE '%$search%')";
    }
    $countResult = $conn->query($countQuery);
    
    if ($hotelsResult && $countResult) {
        $totalCount = $countResult->fetch_assoc()['total'];
        $totalPages = ceil($totalCount / $limit);
        
        $hotels = [];
        while ($row = $hotelsResult->fetch_assoc()) {
            $hotels[] = $row;
        }
        
        return [
            'hotels' => $hotels,
            'pagination' => [
                'current_page' => $page,
                'total_pages' => $totalPages,
                'total_count' => $totalCount,
                'limit' => $limit
            ]
        ];
    } else {
        return [
            'hotels' => [],
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
        case 'add_hotel':
            $response = addHotel($conn, $_POST);
            echo json_encode($response);
            exit;
        case 'edit_hotel':
            $response = editHotel($conn, $_POST);
            echo json_encode($response);
            exit;
        case 'delete_hotel':
            $response = deleteHotel($conn, $_POST['hotel_id']);
            echo json_encode($response);
            exit;
        case 'get_hotel':
            $response = getHotel($conn, $_POST['hotel_id']);
            echo json_encode($response);
            exit;
    }
}

// Pagination variables
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$limit = 15;
$search = isset($_GET['search']) ? ($conn ? $conn->real_escape_string($_GET['search']) : '') : '';

// Get hotels data
$hotelsData = getHotelsList($conn, $page, $limit, $search);
$hotels = $hotelsData['hotels'];
$pagination = $hotelsData['pagination'];

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
    <title>Hotels Management | SJDM Tours Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet">
    <link rel="stylesheet" href="admin-styles.css">
    <style>
        .hotel-stats {
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
        .hotel-table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: var(--radius-md);
            overflow: hidden;
            box-shadow: var(--shadow-sm);
        }
        .hotel-table th,
        .hotel-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid var(--border-color);
        }
        .hotel-table th {
            background: var(--bg-light);
            font-weight: 600;
        }
        .hotel-table tr:hover {
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
        .hotel-image {
            width: 60px;
            height: 40px;
            border-radius: var(--radius-sm);
            object-fit: cover;
        }
        .rating {
            color: #f59e0b;
        }
        .price-range {
            font-weight: 600;
            color: var(--primary);
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
                <a href="destinations.php" class="nav-item">
                    <span class="material-icons-outlined">place</span>
                    <span>Destinations</span>
                </a>
                <a href="hotels.php" class="nav-item active">
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
                    <h1>Hotels Management</h1>
                    <p>Manage hotels and accommodations</p>
                </div>
                
                <div class="top-bar-actions">
                    <button class="btn-primary" onclick="showAddHotelModal()">
                        <span class="material-icons-outlined">add</span>
                        Add Hotel
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
                <!-- Hotel Statistics -->
                <div class="hotel-stats">
                    <div class="stat-card">
                        <h3><?php echo count($hotels); ?></h3>
                        <p>Total Hotels</p>
                    </div>
                    <div class="stat-card">
                        <h3><?php echo count(array_filter($hotels, fn($h) => $h['status'] == 'active')); ?></h3>
                        <p>Active Hotels</p>
                    </div>
                    <div class="stat-card">
                        <h3><?php echo count(array_unique(array_column($hotels, 'category'))); ?></h3>
                        <p>Categories</p>
                    </div>
                </div>
                
                <!-- Search and Filters -->
                <div class="search-bar">
                    <input type="text" id="searchInput" placeholder="Search hotels by name, category, or location..." value="<?php echo htmlspecialchars($search); ?>">
                    <button class="btn-secondary" onclick="searchHotels()">
                        <span class="material-icons-outlined">search</span>
                        Search
                    </button>
                    <button class="btn-secondary" onclick="clearSearch()">
                        <span class="material-icons-outlined">clear</span>
                        Clear
                    </button>
                </div>
                
                <!-- Hotels Table -->
                <div class="table-container">
                    <table class="hotel-table">
                        <thead>
                            <tr>
                                <th>Image</th>
                                <th>Name</th>
                                <th>Category</th>
                                <th>Location</th>
                                <th>Price Range</th>
                                <th>Rating</th>
                                <th>Contact</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($hotels as $hotel): ?>
                            <tr>
                                <td>
                                    <?php if ($hotel['image_url']): ?>
                                        <img src="<?php echo htmlspecialchars($hotel['image_url']); ?>" alt="<?php echo htmlspecialchars($hotel['name']); ?>" class="hotel-image">
                                    <?php else: ?>
                                        <div class="hotel-image" style="background: var(--bg-light); display: flex; align-items: center; justify-content: center;">
                                            <span class="material-icons-outlined">hotel</span>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <strong><?php echo htmlspecialchars($hotel['name']); ?></strong><br>
                                    <small><?php echo htmlspecialchars(substr($hotel['description'], 0, 100)); ?>...</small>
                                </td>
                                <td><?php echo htmlspecialchars($hotel['category']); ?></td>
                                <td><?php echo htmlspecialchars($hotel['location']); ?></td>
                                <td>
                                    <span class="price-range"><?php echo htmlspecialchars($hotel['price_range']); ?></span>
                                </td>
                                <td>
                                    <div class="rating">
                                        <?php echo number_format($hotel['rating'], 1); ?> ⭐
                                        <small>(<?php echo $hotel['review_count']; ?> reviews)</small>
                                    </div>
                                </td>
                                <td>
                                    <?php echo htmlspecialchars($hotel['phone']); ?><br>
                                    <small><?php echo htmlspecialchars($hotel['email']); ?></small>
                                </td>
                                <td>
                                    <span class="status-badge status-<?php echo $hotel['status']; ?>">
                                        <?php echo ucfirst($hotel['status']); ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <button class="btn-icon" onclick="viewHotel(<?php echo $hotel['id']; ?>)" title="View">
                                            <span class="material-icons-outlined">visibility</span>
                                        </button>
                                        <button class="btn-icon" onclick="editHotel(<?php echo $hotel['id']; ?>)" title="Edit">
                                            <span class="material-icons-outlined">edit</span>
                                        </button>
                                        <button class="btn-icon" onclick="deleteHotel(<?php echo $hotel['id']; ?>)" title="Delete">
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
        function searchHotels() {
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
        
        function viewHotel(hotelId) {
            // Implement view hotel modal
            console.log('View hotel:', hotelId);
        }
        
        function editHotel(hotelId) {
            // Implement edit hotel modal
            console.log('Edit hotel:', hotelId);
        }
        
        function deleteHotel(hotelId) {
            if (confirm('Are you sure you want to delete this hotel? This action cannot be undone.')) {
                fetch('', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `action=delete_hotel&hotel_id=${hotelId}`
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
        
        function showAddHotelModal() {
            // Implement add hotel modal
            console.log('Show add hotel modal');
        }
        
        // Search on Enter key
        document.getElementById('searchInput').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                searchHotels();
            }
        });
    </script>
</body>
</html>
