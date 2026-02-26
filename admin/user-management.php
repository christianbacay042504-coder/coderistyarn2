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



    try 



    {



        $stmt = $conn->prepare("DELETE FROM users WHERE id = ? AND user_type = 'user'");



        $stmt->bind_param("i", $userId);







        if ($stmt->execute()) 



        {



            return ['success' => true, 'message' => 'User deleted successfully'];



        } else 



        {



            return ['success' => false, 'message' => 'Failed to delete user'];



        }



    } catch (Exception $e) 



    {



        return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];



    }



}







function getUser($conn, $userId)



{



    try 



    {



        $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");



        $stmt->bind_param("i", $userId);







        $stmt->execute();







        $result = $stmt->get_result();







        if ($result->num_rows > 0) 



        {



            $user = $result->fetch_assoc();







            // Get user activity







            $activityStmt = $conn->prepare("SELECT login_time, ip_address, status FROM login_activity WHERE user_id = ? ORDER BY login_time DESC LIMIT 10");







            $activityStmt->bind_param("i", $userId);







            $activityStmt->execute();







            $activityResult = $activityStmt->get_result();







            $user['activity'] = [];







            while ($row = $activityResult->fetch_assoc()) 



            {



                $user['activity'][] = $row;







            }







            return ['success' => true, 'data' => $user];







        } else 



        {



            return ['success' => false, 'message' => 'User not found'];







        }



    } catch (Exception $e) 



    {



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








function getUsersList($conn, $page = 1, $limit = 15, $search = '', $userType = '')
{
    $offset = ($page - 1) * $limit;
    $search = $conn->real_escape_string($search);
    $userType = $conn->real_escape_string($userType);

    // Get users with pagination
    $usersQuery = "SELECT u.*, a.admin_mark, tg.name as guide_name,
                   (SELECT COUNT(*) FROM bookings WHERE user_id = u.id) as total_bookings,
                   (SELECT SUM(total_amount) FROM bookings WHERE user_id = u.id AND status = 'completed') as total_spent
                   FROM users u 
                   LEFT JOIN admin_users a ON u.id = a.user_id
                   LEFT JOIN tour_guides tg ON u.id = tg.user_id WHERE 1=1";

    if ($search) {
        $usersQuery .= " AND (u.first_name LIKE '%$search%' OR u.last_name LIKE '%$search%' OR u.email LIKE '%$search%')";
    }
    
    if ($userType) {
        $usersQuery .= " AND u.user_type = '$userType'";
    }






    $usersQuery .= " ORDER BY u.created_at DESC LIMIT $limit OFFSET $offset";



    $usersResult = $conn->query($usersQuery);







    // Get total count for pagination



    $countQuery = "SELECT COUNT(*) as total FROM users WHERE 1=1";
    
    if ($search) {
        $countQuery .= " AND (first_name LIKE '%$search%' OR last_name LIKE '%$search%' OR email LIKE '%$search%')";
    }
    
    if ($userType) {
        $countQuery .= " AND user_type = '$userType'";
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
$userType = isset($_GET['user_type']) ? $conn->real_escape_string($_GET['user_type']) : '';







// Get users data



$usersData = getUsersList($conn, $page, $limit, $search, $userType);



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
    <link rel="stylesheet" href="ultra-modern-filters.css">



    <!-- Load admin scripts early to ensure AdminDashboard class is available -->
    <script src="admin-script.js"></script>
    <script src="admin-profile-dropdown.js"></script>


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



                            <a href="javascript:void(0)" class="dropdown-item" id="adminSignoutLink" onclick="openSignOutModal()">



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
                    <div class="search-filters">
                        <div class="filter-buttons">
                            <button class="filter-btn <?php echo $userType === '' ? 'active' : ''; ?>" onclick="filterByType('')">
                                <span class="material-icons-outlined">people</span>
                                All Users
                            </button>
                            <button class="filter-btn <?php echo $userType === 'user' ? 'active' : ''; ?>" onclick="filterByType('user')">
                                <span class="material-icons-outlined">person</span>
                                Regular Users
                            </button>
                            <button class="filter-btn <?php echo $userType === 'tour_guide' ? 'active' : ''; ?>" onclick="filterByType('tour_guide')">
                                <span class="material-icons-outlined">tour</span>
                                Tour Guides
                            </button>
                            <button class="filter-btn <?php echo $userType === 'admin' ? 'active' : ''; ?>" onclick="filterByType('admin')">
                                <span class="material-icons-outlined">admin_panel_settings</span>
                                Admins
                            </button>
                        </div>
                    </div>
                    
                    <!-- Inline script to ensure filterByType is available -->
                    <script>
                        function filterByType(userType) {
                            const currentUrl = new URL(window.location);
                            if (userType) {
                                currentUrl.searchParams.set('user_type', userType);
                            } else {
                                currentUrl.searchParams.delete('user_type');
                            }
                            window.location.href = currentUrl.toString();
                        }
                    </script>
                    
                    <div class="search-inputs">
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



                                    <td>
                                        <?php 
                                        if ($user['user_type'] == 'tour_guide' && !empty($user['guide_name'])):
                                            echo htmlspecialchars($user['guide_name']);
                                        else:
                                            echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']);
                                        endif;
                                        ?>
                                        <?php if ($user['user_type'] == 'admin'): ?>
                                            <span class="badge" style="background: var(--primary-light); color: var(--primary); padding: 2px 8px; border-radius: 4px; font-size: 11px; margin-left: 5px;">ADMIN</span>
                                        <?php elseif ($user['user_type'] == 'tour_guide'): ?>
                                            <span class="badge" style="background: #f0f9ff; color: #0369a1; padding: 2px 8px; border-radius: 4px; font-size: 11px; margin-left: 5px;">TOUR GUIDE</span>
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

                                            <button class="btn-icon" onclick="showDeleteUserModal(<?php echo $user['id']; ?>, '<?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?>')"
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

                            <button class="pagination-btn" onclick="goToPage(<?php echo $pagination['current_page'] - 1; ?>)">

                                <span class="material-icons-outlined">chevron_left</span>

                                Previous

                            </button>

                        <?php endif; ?>

                        

                        <div class="pagination-numbers">

                            <?php 

                            $currentPage = $pagination['current_page'];

                            $totalPages = $pagination['total_pages'];

                            $startPage = max(1, $currentPage - 2);

                            $endPage = min($totalPages, $currentPage + 2);

                            

                            // Show first page if not in range

                            if ($startPage > 1): ?>

                                <button class="pagination-number" onclick="goToPage(1)">1</button>

                                <?php if ($startPage > 2): ?>

                                    <span class="pagination-ellipsis">...</span>

                                <?php endif; ?>

                            <?php endif; ?>



                            <?php for ($i = $startPage; $i <= $endPage; $i++): ?>

                                <button class="pagination-number <?php echo $i == $currentPage ? 'active' : ''; ?>" onclick="goToPage(<?php echo $i; ?>)">

                                    <?php echo $i; ?>

                                </button>

                            <?php endfor; ?>



                            <?php if ($endPage < $totalPages): ?>

                                <span class="pagination-ellipsis">...</span>

                                <?php endif; ?>

                            

                            <?php if ($totalPages > $endPage): ?>

                                <button class="pagination-number" onclick="goToPage(<?php echo $totalPages; ?>)">

                                    <?php echo $totalPages; ?>

                                </button>

                            <?php endif; ?>

                        </div>

                        

                        <?php if ($pagination['current_page'] < $pagination['total_pages']): ?>

                            <button class="pagination-btn pagination-next" onclick="goToPage(<?php echo $pagination['current_page'] + 1; ?>)">

                                Next

                                <span class="material-icons-outlined">chevron_right</span>

                            </button>

                        <?php endif; ?>

                    </div>

                <?php endif; ?>



            </div>



        </main>



    </div>



    <!-- View User Modal -->

    <div id="viewUserModal" class="modal">

        <div class="modal-content">

            <div class="modal-header">

                <h2>User Details</h2>

                <button class="modal-close" onclick="closeViewUserModal()">

                    <span class="material-icons-outlined">close</span>

                </button>

            </div>

            <div class="modal-body">

                <div class="user-details">

                    <div class="user-avatar">

                        <span class="material-icons-outlined">person</span>

                    </div>

                    <div class="user-info">

                        <h3 id="viewUserName">Loading...</h3>

                        <p id="viewUserEmail">Loading...</p>

                        <div class="user-status">

                            <span id="viewUserStatus" class="status-badge">Loading...</span>

                        </div>

                    </div>

                </div>

                

                <div class="details-grid">

                    <div class="detail-item">

                        <label>User ID:</label>

                        <span id="viewUserId">-</span>

                    </div>

                    <div class="detail-item">

                        <label>First Name:</label>

                        <span id="viewUserFirstName">-</span>

                    </div>

                    <div class="detail-item">

                        <label>Last Name:</label>

                        <span id="viewUserLastName">-</span>

                    </div>

                    <div class="detail-item">

                        <label>Email:</label>

                        <span id="viewUserEmailDetail">-</span>

                    </div>

                    <div class="detail-item">

                        <label>Status:</label>

                        <span id="viewUserStatusDetail">-</span>

                    </div>

                    <div class="detail-item">

                        <label>User Type:</label>

                        <span id="viewUserType">-</span>

                    </div>

                    <div class="detail-item">

                        <label>Total Bookings:</label>

                        <span id="viewUserBookings">-</span>

                    </div>

                    <div class="detail-item">

                        <label>Total Spent:</label>

                        <span id="viewUserSpent">-</span>

                    </div>

                    <div class="detail-item">

                        <label>Joined Date:</label>

                        <span id="viewUserJoined">-</span>

                    </div>

                    <div class="detail-item">

                        <label>Last Login:</label>

                        <span id="viewUserLastLogin">-</span>

                    </div>

                </div>

            </div>

            <div class="modal-footer">

                <button type="button" class="btn-secondary" onclick="closeViewUserModal()">Close</button>

                <button type="button" class="btn-primary" onclick="editUserFromView()">Edit User</button>

            </div>

        </div>

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





    <!-- Edit User Modal -->

    <div id="editUserModal" class="modal">

        <div class="modal-content">

            <div class="modal-header">

                <h2>Edit User</h2>

                <button class="modal-close" onclick="closeEditUserModal()">

                    <span class="material-icons-outlined">close</span>

                </button>

            </div>

            <form id="editUserForm" action="" method="POST">

                <input type="hidden" name="action" value="edit_user">

                <input type="hidden" name="user_id" id="editUserId">

                <div class="modal-body">

                    <div class="form-row">

                        <div class="form-group">

                            <label for="editFirstName">First Name *</label>

                            <input type="text" id="editFirstName" name="first_name" required>

                        </div>

                        <div class="form-group">

                            <label for="editLastName">Last Name *</label>

                            <input type="text" id="editLastName" name="last_name" required>

                        </div>

                    </div>

                    <div class="form-group">

                        <label for="editEmail">Email Address *</label>

                        <input type="email" id="editEmail" name="email" required>

                    </div>

                    <div class="form-group">

                        <label for="editStatus">Status</label>

                        <select id="editStatus" name="status">

                            <option value="active">Active</option>

                            <option value="inactive">Inactive</option>

                            <option value="suspended">Suspended</option>

                        </select>

                    </div>

                    <div class="form-group">

                        <label for="editPassword">New Password (leave blank to keep current)</label>

                        <input type="password" id="editPassword" name="password">

                    </div>

                </div>

                <div class="modal-footer">

                    <button type="button" class="btn-secondary" onclick="closeEditUserModal()">Cancel</button>

                    <button type="submit" class="btn-primary">Update User</button>

                </div>

            </form>

        </div>

    </div>





    <!-- Delete User Confirmation Modal -->
    <div id="deleteUserModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Delete User</h2>
                <button class="modal-close" onclick="closeDeleteUserModal()">
                    <span class="material-icons-outlined">close</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="delete-content">
                    <div class="delete-icon">
                        <span class="material-icons-outlined" style="color: #dc3545; font-size: 48px;">warning</span>
                    </div>
                    <div class="delete-message">
                        <h3>Are you sure you want to delete this user?</h3>
                        <p><strong id="deleteUserName"></strong></p>
                        <p>This action cannot be undone. All user data will be permanently removed.</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-secondary" onclick="closeDeleteUserModal()">Cancel</button>
                <button type="button" class="btn-danger" onclick="confirmDeleteUser()">Delete User</button>
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




                        <option value="suspended">Suspended</option>

                    </select>

                </div>

                <div class="form-group">

                    <label for="editPassword">New Password (leave blank to keep current)</label>

                    <input type="password" id="editPassword" name="password">

                </div>

            </div>

            <div class="modal-footer">

                <button type="button" class="btn-secondary" onclick="closeEditUserModal()">Cancel</button>

                <button type="submit" class="btn-primary">Update User</button>

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
    // User management functions following destinations.php pattern
    let currentViewUserId = null;

    function viewUser(userId) {
        console.log('viewUser called with userId:', userId);
        fetch('user-management.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `action=get_user&user_id=${userId}`
        })
        .then(response => {
            console.log('Response status:', response.status);
            return response.json();
        })
        .then(data => {
            console.log('Response data:', data);
            if (!data.success) {
                alert(data.message || 'Failed to load user details.');
                return;
            }

            currentViewUserId = data.data.id;

            document.getElementById('viewUserName').textContent = `${data.data.first_name} ${data.data.last_name}` || '';
            document.getElementById('viewUserEmail').textContent = data.data.email || '';
            document.getElementById('viewUserStatus').textContent = data.data.status ? data.data.status.charAt(0).toUpperCase() + data.data.status.slice(1) : '';
            document.getElementById('viewUserStatus').className = `status-badge status-${data.data.status || 'inactive'}`;
            document.getElementById('viewUserId').textContent = data.data.id || '';
            document.getElementById('viewUserFirstName').textContent = data.data.first_name || '';
            document.getElementById('viewUserLastName').textContent = data.data.last_name || '';
            document.getElementById('viewUserEmailDetail').textContent = data.data.email || '';
            document.getElementById('viewUserStatusDetail').textContent = data.data.status ? data.data.status.charAt(0).toUpperCase() + data.data.status.slice(1) : '';
            document.getElementById('viewUserStatusDetail').className = `status-badge status-${data.data.status || 'inactive'}`;
            document.getElementById('viewUserType').textContent = data.data.user_type || 'user';
            document.getElementById('viewUserBookings').textContent = data.data.total_bookings || 0;
            document.getElementById('viewUserSpent').textContent = `₱${(data.data.total_spent || 0).toFixed(2)}`;
            document.getElementById('viewUserJoined').textContent = data.data.created_at ? new Date(data.data.created_at).toLocaleDateString() : '-';
            document.getElementById('viewUserLastLogin').textContent = data.data.last_login ? new Date(data.data.last_login).toLocaleString() : '-';

            const modal = document.getElementById('viewUserModal');
            console.log('Modal element:', modal);
            if (modal) {
                modal.style.display = 'block';
                modal.classList.add('show');
                document.body.style.overflow = 'hidden';
                console.log('Modal should be visible now');
            } else {
                console.error('Modal not found!');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error loading user details.');
        });
    }

    function editUser(userId) {
        console.log('editUser called with userId:', userId);
        fetch('user-management.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `action=get_user&user_id=${userId}`
        })
        .then(response => {
            console.log('Edit Response status:', response.status);
            return response.json();
        })
        .then(data => {
            console.log('Edit Response data:', data);
            if (!data.success) {
                alert(data.message || 'Failed to load user data.');
                return;
            }

            // Populate edit form
            document.getElementById('editUserId').value = data.data.id;
            document.getElementById('editFirstName').value = data.data.first_name || '';
            document.getElementById('editLastName').value = data.data.last_name || '';
            document.getElementById('editEmail').value = data.data.email || '';
            document.getElementById('editStatus').value = data.data.status || 'active';

            const modal = document.getElementById('editUserModal');
            console.log('Edit Modal element:', modal);
            if (modal) {
                modal.style.display = 'block';
                modal.classList.add('show');
                document.body.style.overflow = 'hidden';
                console.log('Edit Modal should be visible now');
            } else {
                console.error('Edit Modal not found!');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error loading user data.');
        });
    }

    // Delete User Modal Functions
    let deleteUserId = null;

    function showDeleteUserModal(userId, userName) {
        deleteUserId = userId;
        document.getElementById('deleteUserName').textContent = userName;
        const modal = document.getElementById('deleteUserModal');
        if (modal) {
            modal.style.display = 'block';
            modal.classList.add('show');
            document.body.style.overflow = 'hidden';
        }
    }

    function closeDeleteUserModal() {
        const modal = document.getElementById('deleteUserModal');
        if (modal) {
            modal.style.display = 'none';
            modal.classList.remove('show');
            document.body.style.overflow = 'auto';
            deleteUserId = null;
        }
    }

    function confirmDeleteUser() {
        if (!deleteUserId) return;
        
        fetch('user-management.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `action=delete_user&user_id=${deleteUserId}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                closeDeleteUserModal();
                alert(data.message);
                location.reload();
            } else {
                alert(data.message || 'Error deleting user');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error deleting user');
        });
    }

    function deleteUser(userId) {
        // This function is kept for backward compatibility but now uses modal
        // Get user name from the table row
        const row = document.querySelector(`tr:has([onclick*="deleteUser(${userId})"]`);
        let userName = 'this user';
        if (row) {
            const nameCell = row.cells[1]; // Name is in second column
            if (nameCell) {
                userName = nameCell.textContent.trim();
            }
        }
        showDeleteUserModal(userId, userName);
    }

    // Close Delete User modal on overlay click or Escape
    window.addEventListener('click', function(event) {
        const modal = document.getElementById('deleteUserModal');
        if (modal && event.target === modal) {
            closeDeleteUserModal();
        }
    });

    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            const modal = document.getElementById('deleteUserModal');
            if (modal && modal.classList.contains('show')) {
                closeDeleteUserModal();
            }
        }
    });

    function editUserFromView() {
        if (!currentViewUserId) return;
        closeViewUserModal();
        setTimeout(() => editUser(currentViewUserId), 150);
    }

    function closeViewUserModal() {
        const modal = document.getElementById('viewUserModal');
        if (modal) {
            modal.style.display = 'none';
            modal.classList.remove('show');
            document.body.style.overflow = 'auto';
            currentViewUserId = null;
        }
    }

    function closeEditUserModal() {
        const modal = document.getElementById('editUserModal');
        if (modal) {
            modal.style.display = 'none';
            modal.classList.remove('show');
            document.body.style.overflow = 'auto';
            const form = document.getElementById('editUserForm');
            if (form) {
                form.reset();
            }
        }
    }

    function closeAddUserModal() {
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

    function openSignOutModal() {
        const modal = document.getElementById('signOutModal');
        if (modal) {
            modal.classList.add('show');
            document.body.style.overflow = 'hidden'; // Prevent background scrolling
        }
    }

    function closeSignOutModal() {
        const modal = document.getElementById('signOutModal');
        if (modal) {
            modal.classList.remove('show');
            document.body.style.overflow = 'auto';
        }
    }

    function confirmSignOut() {
        window.location.href = 'logout.php';
    }
</script>

<script>
    // Global functions for user management - must be accessible from HTML onclick handlers
    function filterByType(userType) {
        const currentUrl = new URL(window.location);
        if (userType) {
            currentUrl.searchParams.set('user_type', userType);
        } else {
            currentUrl.searchParams.delete('user_type');
        }
        window.location.href = currentUrl.toString();
    }
    
    function viewUserSafe(userId) {
        // Wait for admin to be initialized with better retry logic
        if (typeof window.admin !== 'undefined') {
            window.admin.viewUser(userId);
        } else {
            // Retry with increasing delays
            let retryCount = 0;
            const maxRetries = 5;
            const retryDelay = 100;
            
            function tryViewUser() {
                if (typeof window.admin !== 'undefined') {
                    window.admin.viewUser(userId);
                } else if (retryCount < maxRetries) {
                    retryCount++;
                    setTimeout(tryViewUser, retryDelay * retryCount);
                } else {
                    console.log('Admin still not initialized after retries for view');
                    // Fallback: show basic user info
                    alert(`View user functionality not available. User ID: ${userId}`);
                }
            }
            
            tryViewUser();
        }
    }

    function editUserSafe(userId) {
        // Wait for admin to be initialized with better retry logic
        if (typeof window.admin !== 'undefined') {
            window.admin.editUserModal(userId);
        } else {
            // Retry with increasing delays
            let retryCount = 0;
            const maxRetries = 5;
            const retryDelay = 100;
            
            function tryEditUser() {
                if (typeof window.admin !== 'undefined') {
                    window.admin.editUserModal(userId);
                } else if (retryCount < maxRetries) {
                    retryCount++;
                    setTimeout(tryEditUser, retryDelay * retryCount);
                } else {
                    console.log('Admin still not initialized after retries for edit');
                    // Fallback: redirect with edit parameter
                    window.location.href = `?edit_user=${userId}`;
                }
            }
            
            tryEditUser();
        }
    }

    function deleteUserSafe(userId) {
        // Wait for admin to be initialized with better retry logic
        if (typeof window.admin !== 'undefined') {
            window.admin.deleteUser(userId);
        } else {
            // Retry with increasing delays
            let retryCount = 0;
            const maxRetries = 5;
            const retryDelay = 100;
            
            function tryDeleteUser() {
                if (typeof window.admin !== 'undefined') {
                    window.admin.deleteUser(userId);
                } else if (retryCount < maxRetries) {
                    retryCount++;
                    setTimeout(tryDeleteUser, retryDelay * retryCount);
                } else {
                    console.log('Admin still not initialized after retries for delete');
                    // Fallback: direct delete
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
                            })
                            .catch(error => {
                                console.error('Delete error:', error);
                                alert('Error deleting user');
                            });
                    }
                }
            }
            
            tryDeleteUser();
        }
        
        function initializeAdmin() {
            try {
                if (typeof AdminDashboard !== 'undefined') {
                    admin = new AdminDashboard();
                    window.admin = admin;
                    console.log('Admin dashboard initialized successfully');
                } else {
                    console.error('AdminDashboard class not found');
                }
            } catch (error) {
                console.error('Error initializing admin dashboard:', error);
            }
        }
        
        // Try to initialize immediately
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initializeAdmin);
        } else {
            // DOM is already loaded
            initializeAdmin();
        }

        // Additional utility functions
        function searchUsers() {
            const searchValue = document.getElementById('searchInput').value;
            const currentUrl = new URL(window.location);
            if (searchValue) {
                currentUrl.searchParams.set('search', searchValue);
            } else {
                currentUrl.searchParams.delete('search');
            }
            window.location.href = currentUrl.toString();
        }

        function clearSearch() {
            document.getElementById('searchInput').value = '';
            const currentUrl = new URL(window.location);
            currentUrl.searchParams.delete('search');
            window.location.href = currentUrl.toString();
        }

        function filterByType(userType) {
            const currentUrl = new URL(window.location);
            if (userType) {
                currentUrl.searchParams.set('user_type', userType);
            } else {
                currentUrl.searchParams.delete('user_type');
            }
            window.location.href = currentUrl.toString();
        }

        function goToPage(page) {
            const currentUrl = new URL(window.location);
            currentUrl.searchParams.set('page', page);
            window.location.href = currentUrl.toString();
        }

        function toggleSelectAll() {
            const selectAll = document.getElementById('selectAll');
            const checkboxes = document.querySelectorAll('.user-checkbox');
            checkboxes.forEach(checkbox => {
                checkbox.checked = selectAll.checked;
            });
        }

        function showAddUserModal() {
            const modal = document.getElementById('addUserModal');
            if (modal) {
                modal.style.display = 'block';
                modal.classList.add('show');
                document.body.style.overflow = 'hidden';
            } else {
                // Retry with increasing delays
                let retryCount = 0;
                const maxRetries = 5;
                const retryDelay = 100;
                
                function tryViewUser() {
                    if (typeof window.admin !== 'undefined') {
                        window.admin.viewUser(userId);
                    } else if (retryCount < maxRetries) {
                        retryCount++;
                        setTimeout(tryViewUser, retryDelay * retryCount);
                    } else {
                        console.log('Admin still not initialized after retries for view');
                        // Fallback: show basic user info
                        alert(`View user functionality not available. User ID: ${userId}`);
                    }
                }
                
                tryViewUser();
            }
        }

        function editUserSafe(userId) {
            // Wait for admin to be initialized with better retry logic
            if (typeof window.admin !== 'undefined') {
                window.admin.editUserModal(userId);
            } else {
                // Retry with increasing delays
                let retryCount = 0;
                const maxRetries = 5;
                const retryDelay = 100;
                
                function tryEditUser() {
                    if (typeof window.admin !== 'undefined') {
                        window.admin.editUserModal(userId);
                    } else if (retryCount < maxRetries) {
                        retryCount++;
                        setTimeout(tryEditUser, retryDelay * retryCount);
                    } else {
                        console.log('Admin still not initialized after retries for edit');
                        // Fallback: redirect with edit parameter
                        window.location.href = `?edit_user=${userId}`;
                    }
                }
                
                tryEditUser();
            }
        }

        function deleteUserSafe(userId) {
            // Wait for admin to be initialized with better retry logic
            if (typeof window.admin !== 'undefined') {
                window.admin.deleteUser(userId);
            } else {
                // Retry with increasing delays
                let retryCount = 0;
                const maxRetries = 5;
                const retryDelay = 100;
                
                function tryDeleteUser() {
                    if (typeof window.admin !== 'undefined') {
                        window.admin.deleteUser(userId);
                    } else if (retryCount < maxRetries) {
                        retryCount++;
                        setTimeout(tryDeleteUser, retryDelay * retryCount);
                    } else {
                        console.log('Admin still not initialized after retries for delete');
                        // Fallback: direct delete
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
                                })
                                .catch(error => {
                                    console.error('Delete error:', error);
                                    alert('Error deleting user');
                                });
                        }
                    }
                }
                
                tryDeleteUser();
            }
            
            function initializeAdmin() {
                try {
                    if (typeof AdminDashboard !== 'undefined') {
                        admin = new AdminDashboard();
                        window.admin = admin;
                        console.log('Admin dashboard initialized successfully');
                    } else {
                        console.error('AdminDashboard class not found');
                    }
                } catch (error) {
                    console.error('Error initializing admin dashboard:', error);
                }
            }
            
            // Try to initialize immediately
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', initializeAdmin);
            } else {
                // DOM is already loaded
                initializeAdmin();
            }

            // Additional utility functions
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

            function showAddUserModal() {
                const modal = document.getElementById('addUserModal');
                if (modal) {
                    modal.style.display = 'block';
                    modal.classList.add('show');
                    document.body.style.overflow = 'hidden';
                } else {
                    console.error('Modal not found!');
                }
            }

            function filterByType(userType) {
                const currentUrl = new URL(window.location);
                if (userType) {
                    currentUrl.searchParams.set('user_type', userType);
                } else {
                    currentUrl.searchParams.delete('user_type');
                }
                window.location.href = currentUrl.toString();
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



<style>

    /* View User Modal Styles */

    .user-details {

        display: flex;

        align-items: center;

        gap: 20px;

        margin-bottom: 24px;

        padding: 20px;

        background: var(--gray-50);

        border-radius: 12px;

        border: 1px solid var(--border);

    }



    .user-avatar {

        width: 60px;

        height: 60px;

        border-radius: 50%;

        background: linear-gradient(135deg, var(--primary) 0%, #1a3d1a 100%);

        color: white;

        display: flex;

        align-items: center;

        justify-content: center;

        font-size: 24px;

        font-weight: 700;

        flex-shrink: 0;

    }



    .user-info h3 {

        margin: 0 0 8px 0;

        font-size: 18px;

        font-weight: 600;

        color: var(--text-primary);

    }



    .user-info p {

        margin: 0 0 8px 0;

        font-size: 14px;

        color: var(--text-secondary);

    }



    .user-status {

        display: flex;

        align-items: center;

        gap: 8px;

    }



    .details-grid {

        display: grid;

        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));

        gap: 16px;

    }



    .detail-item {

        display: flex;

        justify-content: space-between;

        align-items: center;

        padding: 12px 16px;

        background: var(--gray-50);

        border-radius: 8px;

        border: 1px solid var(--border);

    }



    .detail-item label {

        font-weight: 600;

        color: var(--text-secondary);

        font-size: 13px;

        text-transform: uppercase;

        letter-spacing: 0.5px;

    }



    .detail-item span {

        font-weight: 500;

        color: var(--text-primary);

        font-size: 14px;

    }



    /* Responsive Design for View User Modal */

    @media (max-width: 768px) {

        .user-details {

            flex-direction: column;

            text-align: center;

            gap: 16px;

        }



        .details-grid {

            grid-template-columns: 1fr;

        }



        .detail-item {

            flex-direction: column;

            align-items: flex-start;

            gap: 8px;

        }

    }

</style>



<style>

    /* Modal Styles */

    .modal {

        display: none;

        position: fixed;

        z-index: 1000;

        left: 0;

        top: 0;

        width: 100%;

        height: 100%;

        background-color: rgba(0, 0, 0, 0.5);

        backdrop-filter: blur(5px);

        animation: fadeIn 0.3s ease;

    }

    @keyframes fadeIn {

        from {

            opacity: 0;

        }

        to {

            opacity: 1;

        }

    }

    .modal-content {

        background-color: white;

        margin: 10% auto;

        padding: 0;

        border-radius: 16px;

        width: 90%;

        max-width: 500px;

        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);

        animation: slideIn 0.3s ease;

        overflow: hidden;

    }

    @keyframes slideIn {

        from {

            transform: translateY(-50px);

            opacity: 0;

        }

        to {

            transform: translateY(0);

            opacity: 1;

        }

    }

    .modal-header {

        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);

        color: white;

        padding: 24px 32px;

        display: flex;

        justify-content: space-between;

        align-items: center;

    }

    .modal-header h2 {

        margin: 0;

        font-size: 1.5rem;

        font-weight: 600;

    }

    .modal-close {

        background: rgba(255, 255, 255, 0.2);

        border: none;

        color: white;

        font-size: 24px;

        cursor: pointer;

        width: 40px;

        height: 40px;

        border-radius: 50%;

        display: flex;

        align-items: center;

        justify-content: center;

        transition: all 0.3s ease;

    }

    .modal-close:hover {

        background: rgba(255, 255, 255, 0.3);

        transform: scale(1.1);

    }

    .modal-body {

        padding: 32px;

    }

    .modal-footer {

        padding: 24px 32px;

        border-top: 1px solid #e5e7eb;

        display: flex;

        gap: 12px;

        justify-content: flex-end;

    }

    .btn-secondary {

        background: #f3f4f6;

        color: #374151;

        border: 1px solid #d1d5db;

        padding: 12px 24px;

        border-radius: 8px;

        cursor: pointer;

        font-weight: 500;

        transition: all 0.3s ease;

    }

    .btn-secondary:hover {

        background: #e5e7eb;

    }

    .btn-primary {

        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);

        color: white;

        border: none;

        padding: 12px 24px;

        border-radius: 8px;

        cursor: pointer;

        font-weight: 500;

        transition: all 0.3s ease;

    }

    .btn-primary:hover {

        background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);

        transform: translateY(-1px);

        box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);

    }

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

        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);

        color: white;

        display: flex;

        align-items: center;

        justify-content: center;

        box-shadow: 0 8px 24px rgba(239, 68, 68, 0.3);

    }

    .signout-icon .material-icons-outlined {

        font-size: 40px;

    }

    .signout-message h3 {

        margin: 0 0 8px 0;

        font-size: 20px;

        font-weight: 600;

        color: #1a202c;

    }

    .signout-message p {

        margin: 0;

        font-size: 14px;

        color: #64748b;

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

</body>

</html>