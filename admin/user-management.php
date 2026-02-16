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


// Extract admin info variables
$adminMark = $adminInfo['admin_mark'] ?? 'A';
$roleTitle = $adminInfo['role_title'] ?? 'Administrator';

// Extract admin info variables
$adminMark = $adminInfo['admin_mark'] ?? 'A';
$roleTitle = $adminInfo['role_title'] ?? 'Administrator';


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

                        <h3><?php echo $stats['totalUsers']; ?></h3>

                        <p>Total Users</p>

                    </div>

                    <div class="stat-card">

                        <h3><?php echo $stats['activeUsers']; ?></h3>

                        <p>Active Users</p>

                    </div>

                    <div class="stat-card">

                        <h3><?php echo $stats['todayLogins']; ?></h3>

                        <p>Today's Logins</p>

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

                                
                                <?php if ($endPage < $pagination['total_pages']): ?>
                                    <span class="pagination-ellipsis">...</span>
                                <?php endif; ?>
                                
                                <?php if ($pagination['total_pages'] > $endPage + 1): ?>
                                    <button class="pagination-number" onclick="goToPage(<?php echo $pagination['total_pages']; ?>)">
                                        <?php echo $pagination['total_pages']; ?>
                                    </button>
                                <?php endif; ?>
                            </div>
                                
                                <?php if ($endPage < $pagination['total_pages']): ?>
                                    <span class="pagination-ellipsis">...</span>
                                <?php endif; ?>
                                
                                <?php if ($pagination['total_pages'] > $endPage + 1): ?>
                                    <button class="pagination-number" onclick="goToPage(<?php echo $pagination['total_pages']; ?>)">
                                        <?php echo $pagination['total_pages']; ?>
                                    </button>
                                <?php endif; ?>
                            </div>


                        <?php if ($pagination['current_page'] < $pagination['total_pages']): ?>

                            <button onclick="goToPage(<?php echo $pagination['current_page'] + 1; ?>)">Next</button>

                        <?php endif; ?>

                        </div>
                        </div>
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


    // Open Account modal when clicking My Account in dropdown - USE DYNAMIC MODAL ONLY
    document.addEventListener('DOMContentLoaded', function() {
        const accountLink = document.getElementById('adminAccountLink');
        if (accountLink) {
            accountLink.addEventListener('click', function(e) {
                e.preventDefault();
                // Close dropdown
                const menu = document.getElementById('adminProfileMenu');
                if (menu) menu.classList.remove('show');
                // Use dynamic modal only
                if (typeof showAdminAccountModal === 'function') {
                    showAdminAccountModal();
                }
            });
        }
    });

    function closeAccountModal() {
        // Close only the dynamic modal
        const dynamicModal = document.getElementById('adminAccountModal');
        
        if (dynamicModal) {
            dynamicModal.remove();
        }
        
        document.body.style.overflow = 'auto';
    }

    function editAccount() {
        alert('Edit profile functionality coming soon!');
    }

    // Open Settings modal when clicking Settings in dropdown - USE DYNAMIC MODAL ONLY
    document.addEventListener('DOMContentLoaded', function() {
        const settingsLink = document.getElementById('adminSettingsLink');
        if (settingsLink) {
            settingsLink.addEventListener('click', function(e) {
                e.preventDefault();
                // Close dropdown
                const menu = document.getElementById('adminProfileMenu');
                if (menu) menu.classList.remove('show');
                // Use dynamic modal only
                if (typeof showAdminSettingsModal === 'function') {
                    showAdminSettingsModal();
                }
            });
        }
    });

    function closeSettingsModal() {
        // Close only the dynamic modal
        const dynamicModal = document.getElementById('adminSettingsModal');
        
        if (dynamicModal) {
            dynamicModal.remove();
        }
        
        document.body.style.overflow = 'auto';
    }

    function saveSettings() {
        alert('Settings saved successfully! (Functionality coming soon)');
        closeSettingsModal();
    }

    // Open Help modal when clicking Help & Support in dropdown - USE DYNAMIC MODAL ONLY
    document.addEventListener('DOMContentLoaded', function() {
        const helpLink = document.getElementById('adminHelpLink');
        if (helpLink) {
            helpLink.addEventListener('click', function(e) {
                e.preventDefault();
                // Close dropdown
                const menu = document.getElementById('adminProfileMenu');
                if (menu) menu.classList.remove('show');
                // Use dynamic modal only
                if (typeof showAdminHelpModal === 'function') {
                    showAdminHelpModal();
                }
            });
        }
    });

    function closeHelpModal() {
        // Close only the dynamic modal
        const dynamicModal = document.getElementById('adminHelpModal');
        
        if (dynamicModal) {
            dynamicModal.remove();
        }
        
        document.body.style.overflow = 'auto';
    }

    function toggleFAQ(element) {
        const answer = element.nextElementSibling;
        const icon = element.querySelector('.material-icons-outlined');
        
        if (answer.style.display === 'block') {
            answer.style.display = 'none';
            icon.style.transform = 'rotate(0deg)';
        } else {
            answer.style.display = 'block';
            icon.style.transform = 'rotate(180deg)';
        }
    }

    function openLiveChat() {
        alert('Live chat functionality coming soon! For now, please contact support@sjdmtours.com');
    }

    // Close Help modal on overlay click or Escape
    window.addEventListener('click', function(event) {
        const modal = document.getElementById('helpModal');
        if (modal && event.target === modal) {
            closeHelpModal();
        }
    });

    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            const modal = document.getElementById('helpModal');
            if (modal && modal.classList.contains('show')) {
                closeHelpModal();
            }
        }
    });

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
                }
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

    // Open Account modal when clicking My Account in dropdown - USE DYNAMIC MODAL ONLY
    document.addEventListener('DOMContentLoaded', function() {
        const accountLink = document.getElementById('adminAccountLink');
        if (accountLink) {
            accountLink.addEventListener('click', function(e) {
                e.preventDefault();
                // Close dropdown
                const menu = document.getElementById('adminProfileMenu');
                if (menu) menu.classList.remove('show');
                // Use dynamic modal only
                if (typeof showAdminAccountModal === 'function') {
                    showAdminAccountModal();
                }
            });
        }
    });

    function closeAccountModal() {
        // Close only the dynamic modal
        const dynamicModal = document.getElementById('adminAccountModal');
        
        if (dynamicModal) {
            dynamicModal.remove();
        }
        
        document.body.style.overflow = 'auto';
    }

    function editAccount() {
        alert('Edit profile functionality coming soon!');
    }

    // Open Settings modal when clicking Settings in dropdown - USE DYNAMIC MODAL ONLY
    document.addEventListener('DOMContentLoaded', function() {
        const settingsLink = document.getElementById('adminSettingsLink');
        if (settingsLink) {
            settingsLink.addEventListener('click', function(e) {
                e.preventDefault();
                // Close dropdown
                const menu = document.getElementById('adminProfileMenu');
                if (menu) menu.classList.remove('show');
                // Use dynamic modal only
                if (typeof showAdminSettingsModal === 'function') {
                    showAdminSettingsModal();
                }
            });
        }
    });

    function closeSettingsModal() {
        // Close only the dynamic modal
        const dynamicModal = document.getElementById('adminSettingsModal');
        
        if (dynamicModal) {
            dynamicModal.remove();
        }
        
        document.body.style.overflow = 'auto';
    }

    function saveSettings() {
        alert('Settings saved successfully! (Functionality coming soon)');
        closeSettingsModal();
    }

    // Open Help modal when clicking Help & Support in dropdown - USE DYNAMIC MODAL ONLY
    document.addEventListener('DOMContentLoaded', function() {
        const helpLink = document.getElementById('adminHelpLink');
        if (helpLink) {
            helpLink.addEventListener('click', function(e) {
                e.preventDefault();
                // Close dropdown
                const menu = document.getElementById('adminProfileMenu');
                if (menu) menu.classList.remove('show');
                // Use dynamic modal only
                if (typeof showAdminHelpModal === 'function') {
                    showAdminHelpModal();
                }
            });
        }
    });

    function closeHelpModal() {
        // Close only the dynamic modal
        const dynamicModal = document.getElementById('adminHelpModal');
        
        if (dynamicModal) {
            dynamicModal.remove();
        }
        
        document.body.style.overflow = 'auto';
    }

    function toggleFAQ(element) {
        const answer = element.nextElementSibling;
        const icon = element.querySelector('.material-icons-outlined');
        
        if (answer.style.display === 'block') {
            answer.style.display = 'none';
            icon.style.transform = 'rotate(0deg)';
        } else {
            answer.style.display = 'block';
            icon.style.transform = 'rotate(180deg)';
        }
    }

    function openLiveChat() {
        alert('Live chat functionality coming soon! For now, please contact support@sjdmtours.com');
    }

    // Close Help modal on overlay click or Escape
    window.addEventListener('click', function(event) {
        const modal = document.getElementById('helpModal');
        if (modal && event.target === modal) {
            closeHelpModal();
        }
    });

    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            const modal = document.getElementById('helpModal');
            if (modal && modal.classList.contains('show')) {
                closeHelpModal();
            }
        }
    });

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
                }
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


    <style>
        /* Clean & Modern Pagination Styles */
        .pagination-wrapper {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 24px 20px;
            margin-top: 32px;
            background: white;
            border-radius: 12px;
            border: 1px solid var(--border);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
        }

        .pagination-info {
            display: flex;
            align-items: center;
        }

        .pagination-text {
            font-size: 14px;
            color: var(--text-secondary);
            font-weight: 500;
            margin-right: 16px;
        }

        .pagination-controls {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .pagination-btn {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 10px 16px;
            border: 1px solid var(--border);
            border-radius: 8px;
            background: white;
            color: var(--text-primary);
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s ease;
            font-size: 14px;
            min-width: 100px;
        }

        .pagination-btn:hover {
            background: var(--primary);
            color: white;
            border-color: var(--primary);
            transform: translateY(-1px);
            box-shadow: 0 2px 8px rgba(44, 95, 45, 0.12);
        }

        .pagination-btn-prev {
            border-radius: 8px 0 0 8px;
        }

        .pagination-btn-next {
            border-radius: 0 8px 8px 8px;
        }

        .pagination-numbers {
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .pagination-number {
            min-width: 40px;
            height: 40px;
            border: 1px solid var(--border);
            border-radius: 8px;
            background: white;
            color: var(--text-primary);
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s ease;
            font-size: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .pagination-number:hover {
            background: var(--gray-50);
            border-color: var(--primary);
            transform: translateY(-1px);
        }

        .pagination-number.active {
            background: var(--primary);
            color: white;
            border-color: var(--primary);
            font-weight: 600;
            box-shadow: 0 2px 6px rgba(44, 95, 45, 0.15);
        }

        .pagination-ellipsis {
            color: var(--text-secondary);
            font-weight: 500;
            padding: 0 8px;
            user-select: none;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .pagination-wrapper {
                flex-direction: column;
                gap: 20px;
                padding: 20px;
            }

            .pagination-controls {
                flex-wrap: wrap;
                justify-content: center;
                gap: 8px;
            }

            .pagination-numbers {
                gap: 6px;
            }

            .pagination-number {
                min-width: 36px;
                height: 36px;
                font-size: 13px;
            }
        }

        @media (max-width: 480px) {
            .pagination-btn {
                padding: 8px 12px;
                font-size: 13px;
                min-width: 80px;
            }

            .pagination-number {
                min-width: 32px;
                height: 32px;
                font-size: 12px;
            }
        }

        /* Modal Centering and Account Modal Styles */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 999999;
            overflow: auto;
        }

        .modal.show {
            display: flex !important;
            align-items: center;
            justify-content: center;
        }

        .modal-content {
            background: white;
            border-radius: 12px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            max-width: 500px;
            width: 90%;
            max-height: 90vh;
            overflow-y: auto;
            margin: 0;
            position: relative;
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 24px;
            border-bottom: 1px solid var(--border);
        }

        .modal-header h2 {
            margin: 0;
            font-size: 20px;
            font-weight: 600;
            color: var(--text-primary);
        }

        .modal-close {
            background: none;
            border: none;
            font-size: 24px;
            cursor: pointer;
            color: var(--text-secondary);
            padding: 4px;
            border-radius: 8px;
            transition: all 0.2s ease;
        }

        .modal-close:hover {
            background: var(--gray-50);
            color: var(--text-primary);
        }

        .modal-body {
            padding: 24px;
        }

        .modal-footer {
            display: flex;
            justify-content: flex-end;
            gap: 12px;
            padding: 24px;
            border-top: 1px solid var(--border);
        }

        /* Account Modal Specific Styles */
        .account-info {
            display: flex;
            align-items: center;
            gap: 20px;
            margin-bottom: 32px;
        }

        .account-avatar {
            flex-shrink: 0;
        }

        .avatar-circle {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary) 0%, #1a3d1a 100%);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 32px;
            font-weight: 700;
            box-shadow: 0 8px 24px rgba(44, 95, 45, 0.2);
        }

        .account-details h3 {
            margin: 0 0 8px 0;
            font-size: 20px;
            font-weight: 600;
            color: var(--text-primary);
        }

        .account-details p {
            margin: 0 0 8px 0;
            font-size: 14px;
            color: var(--text-secondary);
        }

        .account-role {
            display: inline-block;
            padding: 4px 12px;
            background: var(--primary-light);
            color: var(--primary);
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .account-stats {
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .stat-item {
            display: flex;
            align-items: center;
            gap: 16px;
            padding: 16px;
            background: var(--gray-50);
            border-radius: 12px;
            border: 1px solid var(--border);
        }

        .stat-item .material-icons-outlined {
            font-size: 24px;
            color: var(--primary);
        }

        .stat-info {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .stat-label {
            font-size: 12px;
            font-weight: 600;
            color: var(--text-secondary);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .stat-value {
            font-size: 14px;
            font-weight: 600;
            color: var(--text-primary);
        }

        /* Responsive Design for Account Modal */
        @media (max-width: 768px) {
            .modal-content {
                width: 95%;
                margin: 20px;
            }

            .account-info {
                flex-direction: column;
                text-align: center;
                gap: 16px;
            }

            .account-stats {
                gap: 12px;
            }

            .stat-item {
                padding: 12px;
                gap: 12px;
            }
        }

        /* Settings Modal Styles */
        .settings-sections {
            display: flex;
            flex-direction: column;
            gap: 32px;
        }

        .settings-section {
            background: white;
            border-radius: 12px;
            border: 1px solid var(--border);
            padding: 24px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
        }

        .section-title {
            display: flex;
            align-items: center;
            gap: 12px;
            margin: 0 0 20px 0;
            font-size: 18px;
            font-weight: 600;
            color: var(--text-primary);
        }

            .stat-value {
                font-size: 14px;
                font-weight: 600;
                color: var(--text-primary);
            }

            /* Responsive Design for Account Modal */
            @media (max-width: 768px) {
                .modal-content {
                    width: 95%;
                    margin: 20px;
                }

                .account-info {
                    flex-direction: column;
                    text-align: center;
                    gap: 16px;
                }

                .account-stats {
                    gap: 12px;
                }
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 8px;
        }

        .settings-input,
        .settings-select {
            width: 100%;
            padding: 12px 16px;
            border: 1px solid var(--border);
            border-radius: 8px;
            font-size: 14px;
            color: var(--text-primary);
            background: white;
            transition: all 0.2s ease;
        }

        .settings-input:focus,
        .settings-select:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(44, 95, 45, 0.1);
        }

        .settings-input[readonly] {
            background: var(--gray-50);
            color: var(--text-secondary);
            cursor: not-allowed;
        }

        .settings-checkbox {
            display: flex;
            align-items: center;
            gap: 12px;
            cursor: pointer;
            font-size: 14px;
            color: var(--text-primary);
            font-weight: 500;
            position: relative;
        }

        .settings-checkbox input[type="checkbox"] {
            width: 20px;
            height: 20px;
            border: 2px solid var(--border);
            border-radius: 4px;
            background: white;
            cursor: pointer;
            position: relative;
            transition: all 0.2s ease;
        }

        .settings-checkbox input[type="checkbox"]:checked {
            background: var(--primary);
            border-color: var(--primary);
        }

        .settings-checkbox input[type="checkbox"]:checked::after {
            content: '✓';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            color: white;
            font-size: 12px;
            font-weight: bold;
        }

        .checkmark {
            display: none;
        }

        /* Settings Modal Responsive Design */
        @media (max-width: 768px) {
            .settings-sections {
                gap: 24px;
            }

            .settings-section {
                padding: 20px;
            }

            .section-title {
                font-size: 16px;
            }

            .settings-item {
                margin-bottom: 16px;
            }
        }

        /* Sign Out Modal Styles */
        .signout-content {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 24px;
            padding: 20px 0;
            text-align: center;
        }

        .signout-icon {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: linear-gradient(135deg, #dc3545 0%, #b91c1c 100%);
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

    <style>
        /* Clean & Modern Pagination Styles */
        .pagination-wrapper {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 24px 20px;
            margin-top: 32px;
            background: white;
            border-radius: 12px;
            border: 1px solid var(--border);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
        }

        .pagination-info {
            display: flex;
            align-items: center;
        }

        .pagination-text {
            font-size: 14px;
            color: var(--text-secondary);
            font-weight: 500;
            margin-right: 16px;
        }

        .pagination-controls {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .pagination-btn {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 10px 16px;
            border: 1px solid var(--border);
            border-radius: 8px;
            background: white;
            color: var(--text-primary);
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s ease;
            font-size: 14px;
            min-width: 100px;
        }

        .pagination-btn:hover {
            background: var(--primary);
            color: white;
            border-color: var(--primary);
            transform: translateY(-1px);
            box-shadow: 0 2px 8px rgba(44, 95, 45, 0.12);
        }

        .pagination-btn-prev {
            border-radius: 8px 0 0 8px;
        }

        .pagination-btn-next {
            border-radius: 0 8px 8px 8px;
        }

        .pagination-numbers {
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .pagination-number {
            min-width: 40px;
            height: 40px;
            border: 1px solid var(--border);
            border-radius: 8px;
            background: white;
            color: var(--text-primary);
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s ease;
            font-size: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .pagination-number:hover {
            background: var(--gray-50);
            border-color: var(--primary);
            transform: translateY(-1px);
        }

        .pagination-number.active {
            background: var(--primary);
            color: white;
            border-color: var(--primary);
            font-weight: 600;
            box-shadow: 0 2px 6px rgba(44, 95, 45, 0.15);
        }

        .pagination-ellipsis {
            color: var(--text-secondary);
            font-weight: 500;
            padding: 0 8px;
            user-select: none;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .pagination-wrapper {
                flex-direction: column;
                gap: 20px;
                padding: 20px;
            }

            .pagination-controls {
                flex-wrap: wrap;
                justify-content: center;
                gap: 8px;
            }

            .pagination-numbers {
                gap: 6px;
            }

            .pagination-number {
                min-width: 36px;
                height: 36px;
                font-size: 13px;
            }
        }

        @media (max-width: 480px) {
            .pagination-btn {
                padding: 8px 12px;
                font-size: 13px;
                min-width: 80px;
            }

            .pagination-number {
                min-width: 32px;
                height: 32px;
                font-size: 12px;
            }
        }

        /* Modal Centering and Account Modal Styles */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 999999;
            overflow: auto;
        }

        .modal.show {
            display: flex !important;
            align-items: center;
            justify-content: center;
        }

        .modal-content {
            background: white;
            border-radius: 12px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            max-width: 500px;
            width: 90%;
            max-height: 90vh;
            overflow-y: auto;
            margin: 0;
            position: relative;
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 24px;
            border-bottom: 1px solid var(--border);
        }

        .modal-header h2 {
            margin: 0;
            font-size: 20px;
            font-weight: 600;
            color: var(--text-primary);
        }

        .modal-close {
            background: none;
            border: none;
            font-size: 24px;
            cursor: pointer;
            color: var(--text-secondary);
            padding: 4px;
            border-radius: 8px;
            transition: all 0.2s ease;
        }

        .modal-close:hover {
            background: var(--gray-50);
            color: var(--text-primary);
        }

        .modal-body {
            padding: 24px;
        }

        .modal-footer {
            display: flex;
            justify-content: flex-end;
            gap: 12px;
            padding: 24px;
            border-top: 1px solid var(--border);
        }

        /* Account Modal Specific Styles */
        .account-info {
            display: flex;
            align-items: center;
            gap: 20px;
            margin-bottom: 32px;
        }

        .account-avatar {
            flex-shrink: 0;
        }

        .avatar-circle {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary) 0%, #1a3d1a 100%);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 32px;
            font-weight: 700;
            box-shadow: 0 8px 24px rgba(44, 95, 45, 0.2);
        }

        .account-details h3 {
            margin: 0 0 8px 0;
            font-size: 20px;
            font-weight: 600;
            color: var(--text-primary);
        }

        .account-details p {
            margin: 0 0 8px 0;
            font-size: 14px;
            color: var(--text-secondary);
        }

        .account-role {
            display: inline-block;
            padding: 4px 12px;
            background: var(--primary-light);
            color: var(--primary);
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .account-stats {
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .stat-item {
            display: flex;
            align-items: center;
            gap: 16px;
            padding: 16px;
            background: var(--gray-50);
            border-radius: 12px;
            border: 1px solid var(--border);
        }

        .stat-item .material-icons-outlined {
            font-size: 24px;
            color: var(--primary);
        }

        .stat-info {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .stat-label {
            font-size: 12px;
            font-weight: 600;
            color: var(--text-secondary);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .stat-value {
            font-size: 14px;
            font-weight: 600;
            color: var(--text-primary);
        }

        /* Responsive Design for Account Modal */
        @media (max-width: 768px) {
            .modal-content {
                width: 95%;
                margin: 20px;
            }

            .account-info {
                flex-direction: column;
                text-align: center;
                gap: 16px;
            }

            .account-stats {
                gap: 12px;
            }

            .stat-item {
                padding: 12px;
                gap: 12px;
            }
        }

        /* Settings Modal Styles */
        .settings-sections {
            display: flex;
            flex-direction: column;
            gap: 32px;
        }

        .settings-section {
            background: white;
            border-radius: 12px;
            border: 1px solid var(--border);
            padding: 24px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
        }

        .section-title {
            display: flex;
            align-items: center;
            gap: 12px;
            margin: 0 0 20px 0;
            font-size: 18px;
            font-weight: 600;
            color: var(--text-primary);
        }

            .stat-value {
                font-size: 14px;
                font-weight: 600;
                color: var(--text-primary);
            }

            /* Responsive Design for Account Modal */
            @media (max-width: 768px) {
                .modal-content {
                    width: 95%;
                    margin: 20px;
                }

                .account-info {
                    flex-direction: column;
                    text-align: center;
                    gap: 16px;
                }

                .account-stats {
                    gap: 12px;
                }
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 8px;
        }

        .settings-input,
        .settings-select {
            width: 100%;
            padding: 12px 16px;
            border: 1px solid var(--border);
            border-radius: 8px;
            font-size: 14px;
            color: var(--text-primary);
            background: white;
            transition: all 0.2s ease;
        }

        .settings-input:focus,
        .settings-select:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(44, 95, 45, 0.1);
        }

        .settings-input[readonly] {
            background: var(--gray-50);
            color: var(--text-secondary);
            cursor: not-allowed;
        }

        .settings-checkbox {
            display: flex;
            align-items: center;
            gap: 12px;
            cursor: pointer;
            font-size: 14px;
            color: var(--text-primary);
            font-weight: 500;
            position: relative;
        }

        .settings-checkbox input[type="checkbox"] {
            width: 20px;
            height: 20px;
            border: 2px solid var(--border);
            border-radius: 4px;
            background: white;
            cursor: pointer;
            position: relative;
            transition: all 0.2s ease;
        }

        .settings-checkbox input[type="checkbox"]:checked {
            background: var(--primary);
            border-color: var(--primary);
        }

        .settings-checkbox input[type="checkbox"]:checked::after {
            content: '✓';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            color: white;
            font-size: 12px;
            font-weight: bold;
        }

        .checkmark {
            display: none;
        }

        /* Settings Modal Responsive Design */
        @media (max-width: 768px) {
            .settings-sections {
                gap: 24px;
            }

            .settings-section {
                padding: 20px;
            }

            .section-title {
                font-size: 16px;
            }

            .settings-item {
                margin-bottom: 16px;
            }
        }

        /* Sign Out Modal Styles */
        .signout-content {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 24px;
            padding: 20px 0;
            text-align: center;
        }

        .signout-icon {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: linear-gradient(135deg, #dc3545 0%, #b91c1c 100%);
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
<?php closeAdminConnection($conn); ?>

</body>



</html>