<?php
// User Management Module
// This file handles user management operations with separated connections and functions

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

// User Management Functions
function addUser($conn, $data)
{
    try {
        $stmt = $conn->prepare("INSERT INTO users (first_name, last_name, email, password, user_type, status, created_at) VALUES (?, ?, ?, ?, 'user', 'active', NOW())");
        $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);
        $stmt->bind_param("ssss", $data['first_name'], $data['last_name'], $data['email'], $hashedPassword);

        if ($stmt->execute()) {
            return ['success' => true, 'message' => 'User added successfully'];
        } else {
            return ['success' => false, 'message' => 'Failed to add user'];
        }
    } catch (Exception $e) {
        return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
    }
}

function editUser($conn, $data)
{
    try {
        if (!empty($data['password'])) {
            $stmt = $conn->prepare("UPDATE users SET first_name = ?, last_name = ?, email = ?, status = ?, password = ? WHERE id = ?");
            $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);
            $stmt->bind_param("sssssi", $data['first_name'], $data['last_name'], $data['email'], $data['status'], $hashedPassword, $data['user_id']);
        } else {
            $stmt = $conn->prepare("UPDATE users SET first_name = ?, last_name = ?, email = ?, status = ? WHERE id = ?");
            $stmt->bind_param("ssssi", $data['first_name'], $data['last_name'], $data['email'], $data['status'], $data['user_id']);
        }

        if ($stmt->execute()) {
            return ['success' => true, 'message' => 'User updated successfully'];
        } else {
            return ['success' => false, 'message' => 'Failed to update user'];
        }
    } catch (Exception $e) {
        return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
    }
}

function deleteUser($conn, $userId)
{
    try {
        $stmt = $conn->prepare("DELETE FROM users WHERE id = ? AND user_type = 'user'");
        $stmt->bind_param("i", $userId);

        if ($stmt->execute()) {
            return ['success' => true, 'message' => 'User deleted successfully'];
        } else {
            return ['success' => false, 'message' => 'Failed to delete user'];
        }
    } catch (Exception $e) {
        return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
    }
}

function getUser($conn, $userId)
{
    try {
        $stmt = $conn->prepare("SELECT id, first_name, last_name, email, phone, status, created_at, last_login FROM users WHERE id = ? AND user_type = 'user'");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();

            // Get user activity
            $activityStmt = $conn->prepare("SELECT login_time, ip_address, status FROM login_activity WHERE user_id = ? ORDER BY login_time DESC LIMIT 10");
            $activityStmt->bind_param("i", $userId);
            $activityStmt->execute();
            $activityResult = $activityStmt->get_result();
            $user['activity'] = [];
            while ($row = $activityResult->fetch_assoc()) {
                $user['activity'][] = $row;
            }

            return ['success' => true, 'data' => $user];
        } else {
            return ['success' => false, 'message' => 'User not found'];
        }
    } catch (Exception $e) {
        return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
    }
}

function bulkUpdateStatus($conn, $data)
{
    try {
        $userIds = json_decode($data['user_ids']);
        $status = $data['status'];

        $placeholders = str_repeat('?,', count($userIds) - 1) . '?';
        $types = str_repeat('i', count($userIds));

        $stmt = $conn->prepare("UPDATE users SET status = ? WHERE id IN ($placeholders)");
        $stmt->bind_param("s" . $types, $status, ...$userIds);

        if ($stmt->execute()) {
            return ['success' => true, 'message' => count($userIds) . ' user(s) updated successfully'];
        } else {
            return ['success' => false, 'message' => 'Failed to update users'];
        }
    } catch (Exception $e) {
        return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
    }
}

function bulkDeleteUsers($conn, $data)
{
    try {
        $userIds = json_decode($data['user_ids']);

        $placeholders = str_repeat('?,', count($userIds) - 1) . '?';
        $types = str_repeat('i', count($userIds));

        $stmt = $conn->prepare("DELETE FROM users WHERE id IN ($placeholders) AND user_type = 'user'");
        $stmt->bind_param($types, ...$userIds);

        if ($stmt->execute()) {
            return ['success' => true, 'message' => count($userIds) . ' user(s) deleted successfully'];
        } else {
            return ['success' => false, 'message' => 'Failed to delete users'];
        }
    } catch (Exception $e) {
        return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
    }
}

function resetPassword($conn, $data)
{
    try {
        $userId = $data['user_id'];
        $newPassword = $data['new_password'] ?? bin2hex(random_bytes(8));
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

        $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
        $stmt->bind_param("si", $hashedPassword, $userId);

        if ($stmt->execute()) {
            return ['success' => true, 'message' => 'Password reset successfully', 'data' => ['password' => $newPassword]];
        } else {
            return ['success' => false, 'message' => 'Failed to reset password'];
        }
    } catch (Exception $e) {
        return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
    }
}

function getUsersList($conn, $page = 1, $limit = 15, $search = '')
{
    $offset = ($page - 1) * $limit;
    $search = $conn->real_escape_string($search);

    // Get users with pagination
    $usersQuery = "SELECT u.*, a.admin_mark,
                   (SELECT COUNT(*) FROM bookings WHERE user_id = u.id) as total_bookings,
                   (SELECT SUM(total_amount) FROM bookings WHERE user_id = u.id AND status = 'completed') as total_spent
                   FROM users u 
                   LEFT JOIN admin_users a ON u.id = a.user_id WHERE 1=1";

    if ($search) {
        $usersQuery .= " AND (u.first_name LIKE '%$search%' OR u.last_name LIKE '%$search%' OR u.email LIKE '%$search%')";
    }

    $usersQuery .= " ORDER BY u.created_at DESC LIMIT $limit OFFSET $offset";
    $usersResult = $conn->query($usersQuery);

    // Get total count for pagination
    $countQuery = "SELECT COUNT(*) as total FROM users WHERE 1=1";
    if ($search) {
        $countQuery .= " AND (first_name LIKE '%$search%' OR last_name LIKE '%$search%' OR email LIKE '%$search%')";
    }
    $countResult = $conn->query($countQuery);
    $totalCount = $countResult->fetch_assoc()['total'];
    $totalPages = ceil($totalCount / $limit);

    $users = [];
    while ($row = $usersResult->fetch_assoc()) {
        $users[] = $row;
    }

    return [
        'users' => $users,
        'pagination' => [
            'current_page' => $page,
            'total_pages' => $totalPages,
            'total_count' => $totalCount,
            'limit' => $limit
        ]
    ];
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

// Fetch user management settings
$umSettings = [];
$result = $conn->query("SELECT setting_key, setting_value FROM user_management_settings");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $umSettings[$row['setting_key']] = $row['setting_value'];
    }
}

// Common settings
$logoText = $dbSettings['admin_logo_text'] ?? 'SJDM ADMIN';
$moduleTitle = $umSettings['module_title'] ?? 'User Management';
$moduleSubtitle = $umSettings['module_subtitle'] ?? 'Manage system users';
$adminMark = $umSettings['admin_mark_label'] ?? 'A';

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
        case 'add_user':
            $response = addUser($conn, $_POST);
            echo json_encode($response);
            exit;
        case 'edit_user':
            $response = editUser($conn, $_POST);
            echo json_encode($response);
            exit;
        case 'delete_user':
            $response = deleteUser($conn, $_POST['user_id']);
            echo json_encode($response);
            exit;
        case 'get_user':
            $response = getUser($conn, $_POST['user_id']);
            echo json_encode($response);
            exit;
        case 'bulk_update_status':
            $response = bulkUpdateStatus($conn, $_POST);
            echo json_encode($response);
            exit;
        case 'bulk_delete_users':
            $response = bulkDeleteUsers($conn, $_POST);
            echo json_encode($response);
            exit;
        case 'reset_password':
            $response = resetPassword($conn, $_POST);
            echo json_encode($response);
            exit;
    }
}

// Pagination variables
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$limit = intval($umSettings['default_user_limit'] ?? 15);
$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';

// Get users data
$usersData = getUsersList($conn, $page, $limit, $search);
$users = $usersData['users'];
$pagination = $usersData['pagination'];

// Get statistics
$stats = getAdminStats($conn);

// Map query keys to values for menu badges
$queryValues = [
    'totalUsers' => $stats['totalUsers'],
    'totalBookings' => $stats['totalBookings']
];

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management | SJDM Tours Admin</title>
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
                    <div class="mark-icon"><?php echo $adminMark; ?></div>
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
                    <button class="btn-primary" onclick="showAddUserModal()">
                        <span class="material-icons-outlined">add</span>
                        Add User
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
                <!-- User Statistics -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon blue">
                            <span class="material-icons-outlined">people</span>
                        </div>
                        <div class="stat-details">
                            <h3><?php echo $stats['totalUsers']; ?></h3>
                            <p>Total Users</p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon green">
                            <span class="material-icons-outlined">person_check</span>
                        </div>
                        <div class="stat-details">
                            <h3><?php echo $stats['activeUsers']; ?></h3>
                            <p>Active Users</p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon orange">
                            <span class="material-icons-outlined">login</span>
                        </div>
                        <div class="stat-details">
                            <h3><?php echo $stats['todayLogins']; ?></h3>
                            <p>Today's Logins</p>
                        </div>
                    </div>
                </div>

                <!-- Search and Filters -->
                <div class="search-bar">
                    <input type="text" id="searchInput" placeholder="Search users by name or email..."
                        value="<?php echo htmlspecialchars($search); ?>">
                    <button class="btn-secondary" onclick="searchUsers()">
                        <span class="material-icons-outlined">search</span>
                        Search
                    </button>
                    <button class="btn-secondary" onclick="clearSearch()">
                        <span class="material-icons-outlined">clear</span>
                        Clear
                    </button>
                </div>

                <!-- Users Table -->
                <div class="table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>
                                    <input type="checkbox" id="selectAll" onchange="toggleSelectAll()">
                                </th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Status</th>
                                <th>Total Bookings</th>
                                <th>Total Spent</th>
                                <th>Joined</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $user): ?>
                                <tr>
                                    <td>
                                        <input type="checkbox" class="user-checkbox" value="<?php echo $user['id']; ?>">
                                    </td>
                                    <td><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?>
                                        <?php if ($user['user_type'] == 'admin'): ?>
                                            <span class="badge" style="background: var(--primary-light); color: var(--primary); padding: 2px 8px; border-radius: 4px; font-size: 11px; margin-left: 5px;">ADMIN</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                                    <td>
                                        <span class="status-badge status-<?php echo $user['status']; ?>">
                                            <?php echo ucfirst($user['status']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo $user['total_bookings']; ?></td>
                                    <td>₱<?php echo number_format($user['total_spent'] ?? 0, 2); ?></td>
                                    <td><?php echo date('M j, Y', strtotime($user['created_at'])); ?></td>
                                    <td>
                                        <div class="action-buttons">
                                            <button class="btn-icon" onclick="viewUser(<?php echo $user['id']; ?>)"
                                                title="View">
                                                <span class="material-icons-outlined">visibility</span>
                                            </button>
                                            <button class="btn-icon" onclick="editUser(<?php echo $user['id']; ?>)"
                                                title="Edit">
                                                <span class="material-icons-outlined">edit</span>
                                            </button>
                                            <button class="btn-icon" onclick="deleteUser(<?php echo $user['id']; ?>)"
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

    <!-- Add User Modal -->
    <div id="addUserModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Add New User</h2>
                <button class="modal-close" onclick="closeAddUserModal()">
                    <span class="material-icons-outlined">close</span>
                </button>
            </div>
            <form id="addUserForm" action="" method="POST">
                <input type="hidden" name="action" value="add_user">
                <div class="modal-body">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="firstName">First Name *</label>
                            <input type="text" id="firstName" name="first_name" required>
                        </div>
                        <div class="form-group">
                            <label for="lastName">Last Name *</label>
                            <input type="text" id="lastName" name="last_name" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="email">Email Address *</label>
                        <input type="email" id="email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label for="password">Password *</label>
                        <input type="password" id="password" name="password" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-secondary" onclick="closeAddUserModal()">Cancel</button>
                    <button type="submit" class="btn-primary">Add User</button>
                </div>
            </form>
        </div>
    </div>

    <script src="admin-script.js"></script>
    <script src="admin-profile-dropdown.js"></script>
    <script>
        // Initialize Admin Dashboard
        let adminDashboard;
        
        document.addEventListener('DOMContentLoaded', function() {
            adminDashboard = new AdminDashboard();
        });
    
    function searchUsers() {
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

    function toggleSelectAll() {
        const selectAll = document.getElementById('selectAll');
        const checkboxes = document.querySelectorAll('.user-checkbox');
        checkboxes.forEach(checkbox => {
            checkbox.checked = selectAll.checked;
        });
    }

    function viewUser(userId) {
        if (adminDashboard) {
            adminDashboard.viewUser(userId);
        } else {
            console.log('View user:', userId);
        }
    }

    function editUser(userId) {
        if (adminDashboard) {
            adminDashboard.editUserModal(userId);
        } else {
            console.log('Edit user:', userId);
        }
    }

    function deleteUser(userId) {
        if (adminDashboard) {
            adminDashboard.deleteUser(userId);
        } else {
            // Fallback for cases where adminDashboard is not initialized
            if (confirm('Are you sure you want to delete this user?')) {
                fetch('', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `action=delete_user&user_id=${userId}`
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

    function showAddUserModal() {
        if (adminDashboard) {
            adminDashboard.showModal('addUserModal');
        } else {
            // Fallback modal display
            const modal = document.getElementById('addUserModal');
            if (modal) {
                modal.style.display = 'block';
                modal.classList.add('show');
                document.body.style.overflow = 'hidden';
            } else {
                console.error('Modal not found!');
            }
        }
    }

    function closeAddUserModal() {
        if (adminDashboard) {
            adminDashboard.closeModal('addUserModal');
        } else {
            // Fallback modal close
            const modal = document.getElementById('addUserModal');
            if (modal) {
                modal.style.display = 'none';
                modal.classList.remove('show');
                document.body.style.overflow = 'auto';
                const form = document.getElementById('addUserForm');
                if (form) {
                    form.reset();
                }
            }
        }
    }

    // Enhanced close modal when clicking outside
    window.onclick = function(event) {
        const modal = document.getElementById('addUserModal');
        if (event.target === modal) {
            closeAddUserModal();
        }
    }

    // Close modal with Escape key
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            const modal = document.getElementById('addUserModal');
            if (modal && modal.style.display === 'block') {
                closeAddUserModal();
            }
        }
    });

    // Search on Enter key
    document.getElementById('searchInput').addEventListener('keypress', function (e) {
        if (e.key === 'Enter') {
            searchUsers();
        }
    });
</script>
<?php closeAdminConnection($conn); ?>
</body>

</html>