<?php
// Tour Guide Registrations Management Module
// This file handles tour guide registration applications with separated connections and functions

// Include admin configuration and authentication
require_once '../config/database.php';
require_once '../config/auth.php';

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

// Tour Guide Registration Functions
function addRegistration($conn, $data)
{
    try {
        $stmt = $conn->prepare("INSERT INTO registration_tour_guide (last_name, first_name, gender, email, phone, specialization, experience_years, status, resume_url, cover_letter) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending', ?, ?)");
        $stmt->bind_param("sssisssssss", 
            $data['last_name'], 
            $data['first_name'], 
            $data['gender'], 
            $data['email'], 
            $data['phone'], 
            $data['specialization'], 
            $data['experience_years'], 
            $data['status'], 
            $data['resume_url'], 
            $data['cover_letter']
        );

        if ($stmt->execute()) {
            return ['success' => true, 'message' => 'Registration added successfully'];
        } else {
            return ['success' => false, 'message' => 'Failed to add registration'];
        }
    } catch (Exception $e) {
        return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
    }
}

function updateRegistrationStatus($conn, $registrationId, $status)
{
    try {
        $processedBy = ($_SESSION['first_name'] ?? 'Admin') . ' ' . ($_SESSION['last_name'] ?? 'User');
        $stmt = $conn->prepare("UPDATE registration_tour_guide SET status = ?, review_date = CURRENT_TIMESTAMP, reviewed_by = ? WHERE id = ?");
        $stmt->bind_param("sii", $status, $_SESSION['id'], $registrationId);
        
        if ($stmt->execute()) {
            $action = $status === 'approved' ? 'approved' : ($status === 'rejected' ? 'rejected' : ($status === 'under_review' ? 'marked as under review' : 'updated'));
            return ['success' => true, 'message' => "Registration $action successfully"];
        } else {
            return ['success' => false, 'message' => 'Failed to update registration status'];
        }
    } catch (Exception $e) {
        return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
    }
}

function deleteRegistration($conn, $registrationId)
{
    try {
        $stmt = $conn->prepare("DELETE FROM tour_guide_registrations WHERE id = ?");
        $stmt->bind_param("i", $registrationId);

        if ($stmt->execute()) {
            return ['success' => true, 'message' => 'Registration deleted successfully'];
        } else {
            return ['success' => false, 'message' => 'Failed to delete registration'];
        }
    } catch (Exception $e) {
        return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
    }
}

function getRegistration($conn, $registrationId)
{
    try {
        $stmt = $conn->prepare("SELECT * FROM registration_tour_guide WHERE id = ?");
        $stmt->bind_param("i", $registrationId);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            return ['success' => true, 'data' => $result->fetch_assoc()];
        } else {
            return ['success' => false, 'message' => 'Registration not found'];
        }
    } catch (Exception $e) {
        return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
    }
}

function getRegistrationsList($conn, $page = 1, $limit = 15, $search = '')
{
    $offset = ($page - 1) * $limit;
    $search = $conn->real_escape_string($search);

    // Get registrations with pagination
    $registrationsQuery = "SELECT * FROM registration_tour_guide WHERE 1=1";
    
    if ($search) {
        $registrationsQuery .= " AND (last_name LIKE '%$search%' OR first_name LIKE '%$search%' OR email LIKE '%$search%' OR specialization LIKE '%$search%')";
    }
    
    $registrationsQuery .= " ORDER BY id DESC LIMIT $limit OFFSET $offset";
    $registrationsResult = $conn->query($registrationsQuery);

    // Get total count for pagination
    $countQuery = "SELECT COUNT(*) as total FROM registration_tour_guide WHERE 1=1";
    if ($search) {
        $countQuery .= " AND (last_name LIKE '%$search%' OR first_name LIKE '%$search%' OR email LIKE '%$search%' OR specialization LIKE '%$search%')";
    }
    $countResult = $conn->query($countQuery);

    if ($registrationsResult && $countResult) {
        $totalCount = $countResult->fetch_assoc()['total'];
        $totalPages = ceil($totalCount / $limit);

        $registrations = [];
        while ($row = $registrationsResult->fetch_assoc()) {
            $registrations[] = $row;
        }

        return [
            'registrations' => $registrations,
            'pagination' => [
                'current_page' => $page,
                'total_pages' => $totalPages,
                'total_count' => $totalCount,
                'limit' => $limit
            ]
        ];
    } else {
        return [
            'registrations' => [],
            'pagination' => [
                'current_page' => $page,
                'total_pages' => 0,
                'total_count' => 0,
                'limit' => $limit
            ]
        ];
    }
}

function editRegistrationData($conn, $data)
{
    try {
        $registrationId = $data['id'] ?? 0;
        $processedBy = ($_SESSION['first_name'] ?? 'Admin') . ' ' . ($_SESSION['last_name'] ?? 'User');
        
        $stmt = $conn->prepare("UPDATE registration_tour_guide SET last_name = ?, first_name = ?, middle_initial = ?, gender = ?, email = ?, primary_phone = ?, specialization = ?, years_of_experience = ?, status = ?, admin_notes = ?, processed_by = ?, processed_date = CURRENT_TIMESTAMP WHERE id = ?");
        
        $stmt->bind_param("ssssssisssii", 
            $data['last_name'], 
            $data['first_name'], 
            $data['middle_initial'], 
            $data['gender'], 
            $data['email'], 
            $data['primary_phone'], 
            $data['specialization'], 
            $data['years_of_experience'], 
            $data['status'], 
            $data['admin_notes'], 
            $processedBy,
            $registrationId
        );

        if ($stmt->execute()) {
            return ['success' => true, 'message' => 'Registration updated successfully'];
        } else {
            return ['success' => false, 'message' => 'Failed to update registration'];
        }
    } catch (Exception $e) {
        return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
    }
}

function getRegistrationStats($conn)
{
    $stats = [];

    // Total registrations
    $result = $conn->query("SELECT COUNT(*) as total FROM registration_tour_guide");
    $stats['totalRegistrations'] = $result->fetch_assoc()['total'];

    // Pending registrations
    $result = $conn->query("SELECT COUNT(*) as total FROM registration_tour_guide WHERE status = 'pending'");
    $stats['pendingRegistrations'] = $result->fetch_assoc()['total'];

    // Approved registrations
    $result = $conn->query("SELECT COUNT(*) as total FROM registration_tour_guide WHERE status = 'approved'");
    $stats['approvedRegistrations'] = $result->fetch_assoc()['total'];

    // Rejected registrations
    $result = $conn->query("SELECT COUNT(*) as total FROM registration_tour_guide WHERE status = 'rejected'");
    $stats['rejectedRegistrations'] = $result->fetch_assoc()['total'];

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

// Fetch tour guide registration settings
$umSettings = [];
// $result = $conn->query("SELECT setting_key, setting_value FROM tour_guide_registrations_settings");
// if ($result) {
//     while ($row = $result->fetch_assoc()) {
//         $umSettings[$row['setting_key']] = $row['setting_value'];
//     }
// }

// Common settings
$logoText = $dbSettings['admin_logo_text'] ?? 'SJDM ADMIN';
$moduleTitle = $umSettings['module_title'] ?? 'Tour Guide Registrations';
$moduleSubtitle = $umSettings['module_subtitle'] ?? 'Manage tour guide registration applications';

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
    // Always output something for debugging
    header('Content-Type: application/json');
    
    // Disable error display for AJAX requests but keep logging
    ini_set('display_errors', 0);
    error_reporting(E_ALL);
    
    try {
        $action = $_POST['action'] ?? '';

        // Debug logging
        error_log("AJAX request received. Action: " . $action);
        error_log("POST data: " . print_r($_POST, true));
        
        // Test response to ensure AJAX handler is working
        if (empty($action)) {
            echo json_encode(['success' => false, 'message' => 'No action provided', 'debug' => 'AJAX handler reached']);
            exit;
        }

        switch ($action) {
            case 'edit_registration':
                error_log("Processing edit_registration action");
                $response = editRegistrationData($conn, $_POST);
                error_log("editRegistrationData response: " . print_r($response, true));
                echo json_encode($response);
                exit;
            case 'add_registration':
                $response = addRegistration($conn, $_POST);
                echo json_encode($response);
                exit;
            case 'update_status':
                $response = updateRegistrationStatus($conn, $_POST['registration_id'], $_POST['status']);
                echo json_encode($response);
                exit;
            case 'delete_registration':
                $response = deleteRegistration($conn, $_POST['registration_id']);
                echo json_encode($response);
                exit;
            case 'get_registration':
                $response = getRegistration($conn, $_POST['registration_id']);
                echo json_encode($response);
                exit;
            default:
                error_log("Unknown action received: " . $action);
                error_log("Available POST keys: " . implode(', ', array_keys($_POST)));
                echo json_encode(['success' => false, 'message' => 'Unknown action: ' . $action]);
                exit;
        }
    } catch (Exception $e) {
        error_log("AJAX request error: " . $e->getMessage());
        error_log("Exception trace: " . $e->getTraceAsString());
        echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
        exit;
    } catch (Error $e) {
        error_log("AJAX fatal error: " . $e->getMessage());
        error_log("Error trace: " . $e->getTraceAsString());
        echo json_encode(['success' => false, 'message' => 'Fatal server error: ' . $e->getMessage()]);
        exit;
    }
}

// Pagination variables
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$limit = intval($umSettings['default_registration_limit'] ?? 15);
$search = isset($_GET['search']) ? ($conn ? $conn->real_escape_string($_GET['search']) : '') : '';

// Get registrations data
$registrationsData = getRegistrationsList($conn, $page, $limit, $search);
$registrations = $registrationsData['registrations'];
$pagination = $registrationsData['pagination'];

// Get statistics
$stats = getRegistrationStats($conn);

// Map query keys to values for menu badges
$queryValues = [
    'totalUsers' => $stats['totalUsers'] ?? 0,
    'totalBookings' => $stats['totalBookings'] ?? 0,
    'totalGuides' => $stats['totalGuides'] ?? 0
];

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tour Guide Registrations | SJDM Tours Admin</title>
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
                    <div class="mark-icon"><?php echo strtoupper(substr($logoText, 0, 1)) ?: 'A'; ?></div>
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
                            <a href="javascript:void(0)" class="dropdown-item" id="adminSignoutLink">
                                <span class="material-icons-outlined">logout</span>
                                <span>Sign Out</span>
                            </a>
                        </div>
                    </div>
                </div>
            </header>

            <div class="content-area">
                <!-- Registration Statistics -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <h3><?php echo $stats['totalRegistrations']; ?></h3>
                        <p>Total Registrations</p>
                    </div>
                    <div class="stat-card">
                        <h3><?php echo $stats['pendingRegistrations']; ?></h3>
                        <p>Pending</p>
                    </div>
                    <div class="stat-card">
                        <h3><?php echo $stats['approvedRegistrations']; ?></h3>
                        <p>Approved</p>
                    </div>
                    <div class="stat-card">
                        <h3><?php echo $stats['rejectedRegistrations']; ?></h3>
                        <p>Rejected</p>
                    </div>
                </div>
                <!-- Filter Tabs -->
                <div class="filter-tabs">
                    <button class="filter-tab" onclick="window.location.href='tour-guides.php'" data-tab="tour-guides">
                        <span class="material-icons-outlined">tour</span>
                        Tour Guides
                    </button>
                    <button class="filter-tab active" data-tab="registrations">
                        <span class="material-icons-outlined">how_to_reg</span>
                        Tour Guide Registrations
                    </button>
                </div>

                <!-- Search and Filters -->
                <div class="search-bar">
                    <input type="text" id="searchInput" placeholder="Search registrations by name, email, or specialization..."
                        value="<?php echo htmlspecialchars($search); ?>">
                    <button class="btn-secondary" onclick="searchRegistrations()">
                        <span class="material-icons-outlined">search</span>
                        Search
                    </button>
                    <button class="btn-secondary" onclick="clearSearch()">
                        <span class="material-icons-outlined">clear</span>
                        Clear
                    </button>
                </div>

                <!-- Registrations Table -->
                <div class="table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Full Name (LN,FN,MI)</th>
                                <th>Gender</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Specializaton</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($registrations as $registration): ?>
                                <tr>
                                    <td>
                                        <strong><?php echo htmlspecialchars($registration['last_name'] . ', ' . $registration['first_name'] . ' ' . $registration['middle_initial']); ?></strong>
                                    </td>
                                    <td>
                                        <?php echo htmlspecialchars($registration['gender']); ?>
                                    </td>
                                    <td>
                                        <?php echo htmlspecialchars($registration['email']); ?>
                                    </td>
                                    <td>
                                        <?php echo htmlspecialchars($registration['primary_phone']); ?>
                                    </td>
                                    <td>
                                        <?php echo htmlspecialchars($registration['specialization']); ?>
                                    </td>
                                    <td>
                                        <span class="status-badge status-<?php echo $registration['status']; ?>">
                                            <?php echo ucfirst($registration['status']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="action-buttons">
                                            <button class="btn-icon btn-view" onclick="viewRegistration(<?php echo $registration['id']; ?>)"
                                                title="View">
                                                <span class="material-icons-outlined">visibility</span>
                                            </button>
                                            <button class="btn-icon btn-edit" onclick="editRegistration(<?php echo $registration['id']; ?>)"
                                                title="Edit">
                                                <span class="material-icons-outlined">edit</span>
                                            </button>
                                            <button class="btn-icon btn-delete" onclick="deleteRegistration(<?php echo $registration['id']; ?>)"
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

    <!-- View Registration Modal -->
    <div id="viewRegistrationModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Registration Details</h2>
                <button class="modal-close" onclick="closeViewRegistrationModal()">
                    <span class="material-icons-outlined">close</span>
                </button>
            </div>
            <div class="modal-body" style="max-height: 60vh; overflow-y: auto;">
                <div id="viewRegistrationContent">
                    <!-- Registration details will be loaded here -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-secondary" onclick="closeViewRegistrationModal()">Close</button>
                <button type="button" class="btn-primary" onclick="openEditForm()">Edit Registration</button>
            </div>
        </div>
    </div>

    <!-- Update Status Modal -->
    <div id="updateStatusModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 id="updateStatusModalTitle">Update Status</h2>
                <button class="modal-close" onclick="closeUpdateStatusModal()">
                    <span class="material-icons-outlined">close</span>
                </button>
            </div>
            <form id="updateStatusForm" onsubmit="handleUpdateStatus(event)">
                <div class="modal-body">
                    <input type="hidden" id="updateRegistrationId" name="registration_id">
                    <input type="hidden" id="updateStatusAction" name="status">
                    <p id="updateStatusModalText">Are you sure you want to update the status?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-secondary" onclick="closeUpdateStatusModal()">Cancel</button>
                    <button type="submit" class="btn-primary" id="updateStatusConfirmBtn">Confirm</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Success Modal -->
    <div id="successModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Success</h2>
                <button class="modal-close" onclick="closeSuccessModal()">
                    <span class="material-icons-outlined">close</span>
                </button>
            </div>
            <div class="modal-body">
                <div style="text-align: center; padding: 20px;">
                    <div style="width: 60px; height: 60px; margin: 0 auto 20px; background: #28a745; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                        <span class="material-icons-outlined" style="color: white; font-size: 30px;">check</span>
                    </div>
                    <p id="successMessage" style="font-size: 18px; margin: 0; color: #333;">Operation completed successfully!</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-primary" onclick="closeSuccessModal()">OK</button>
            </div>
        </div>
    </div>

    <script src="admin-script.js"></script>
    <script src="admin-profile-dropdown.js"></script>
    <script>
        function searchRegistrations() {
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

        function viewRegistration(registrationId) {
            console.log('Opening view modal for registration ID:', registrationId);
            
            fetch('tour-guide-registrations.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `action=get_registration&registration_id=${registrationId}`
            })
            .then(response => response.json())
            .then(data => {
                console.log('Registration data received:', data);
                if (data.success) {
                    // Store registration data globally for editing
                    window.currentRegistrationData = data.data;
                    console.log('Stored registration data:', window.currentRegistrationData);
                    populateViewModal(data.data);
                    const modal = document.getElementById('viewRegistrationModal');
                    modal.style.display = 'block';
                    modal.classList.add('show');
                    document.body.style.overflow = 'hidden';
                } else {
                    console.error('Error fetching registration:', data.message);
                    alert(data.message || 'Error fetching registration data');
                }
            })
            .catch(error => {
                console.error('Fetch error:', error);
                alert('Error fetching registration data: ' + error.message);
            });
        }

        function populateViewModal(registration) {
            const content = document.getElementById('viewRegistrationContent');
            if (content) {
                // Check if registration data exists
                if (!registration) {
                    content.innerHTML = '<div style="text-align: center; padding: 20px;">No registration data available</div>';
                    return;
                }
                
                content.innerHTML = `
                    <div style="margin-bottom: 25px; padding: 20px; background: #f8f9fa; border-radius: 12px;">
                        <div style="display: flex; align-items: center; margin-bottom: 20px;">
                            <div style="width: 80px; height: 80px; margin: 0 auto; background: linear-gradient(135deg, var(--primary), var(--primary-dark)); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-size: 24px; font-weight: 700;">
                                ${(registration.first_name || '') + (registration.last_name || '') ? (registration.first_name || '').charAt(0) + (registration.last_name || '').charAt(0) : '?'}
                            </div>
                        </div>
                        
                        <!-- Personal Information -->
                        <h4 style="margin: 20px 0 15px 0; color: var(--primary); border-bottom: 2px solid var(--primary); padding-bottom: 5px;">Personal Information</h4>
                        <div style="display: flex; justify-content: space-between; padding: 15px 0; border-bottom: 1px solid #e9ecef;">
                            <span style="font-weight: 700; color: #495057;">Full Name:</span>
                            <span style="font-weight: 500; color: #212529;">${(registration.last_name || '') + ', ' + (registration.first_name || '') + ' ' + (registration.middle_initial || '')}</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; padding: 15px 0; border-bottom: 1px solid #e9ecef;">
                            <span style="font-weight: 700; color: #495057;">Preferred Name:</span>
                            <span style="font-weight: 500; color: #212529;">${registration.preferred_name || 'Not specified'}</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; padding: 15px 0; border-bottom: 1px solid #e9ecef;">
                            <span style="font-weight: 700; color: #495057;">Date of Birth:</span>
                            <span style="font-weight: 500; color: #212529;">${registration.date_of_birth ? new Date(registration.date_of_birth).toLocaleDateString() : 'Not provided'}</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; padding: 15px 0; border-bottom: 1px solid #e9ecef;">
                            <span style="font-weight: 700; color: #495057;">Gender:</span>
                            <span style="font-weight: 500; color: #212529;">${registration.gender || 'Not specified'}</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; padding: 15px 0; border-bottom: 1px solid #e9ecef;">
                            <span style="font-weight: 700; color: #495057;">Home Address:</span>
                            <span style="font-weight: 500; color: #212529;">${registration.home_address || 'Not provided'}</span>
                        </div>
                        
                        <!-- Contact Information -->
                        <h4 style="margin: 20px 0 15px 0; color: var(--primary); border-bottom: 2px solid var(--primary); padding-bottom: 5px;">Contact Information</h4>
                        <div style="display: flex; justify-content: space-between; padding: 15px 0; border-bottom: 1px solid #e9ecef;">
                            <span style="font-weight: 700; color: #495057;">Email:</span>
                            <span style="font-weight: 500; color: #212529;">${registration.email || 'Not provided'}</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; padding: 15px 0; border-bottom: 1px solid #e9ecef;">
                            <span style="font-weight: 700; color: #495057;">Primary Phone:</span>
                            <span style="font-weight: 500; color: #212529;">${registration.primary_phone || 'Not provided'}</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; padding: 15px 0; border-bottom: 1px solid #e9ecef;">
                            <span style="font-weight: 700; color: #495057;">Secondary Phone:</span>
                            <span style="font-weight: 500; color: #212529;">${registration.secondary_phone || 'Not provided'}</span>
                        </div>
                        
                        <!-- Emergency Contact -->
                        <h4 style="margin: 20px 0 15px 0; color: var(--primary); border-bottom: 2px solid var(--primary); padding-bottom: 5px;">Emergency Contact</h4>
                        <div style="display: flex; justify-content: space-between; padding: 15px 0; border-bottom: 1px solid #e9ecef;">
                            <span style="font-weight: 700; color: #495057;">Contact Name:</span>
                            <span style="font-weight: 500; color: #212529;">${registration.emergency_contact_name || 'Not provided'}</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; padding: 15px 0; border-bottom: 1px solid #e9ecef;">
                            <span style="font-weight: 700; color: #495057;">Relationship:</span>
                            <span style="font-weight: 500; color: #212529;">${registration.emergency_contact_relationship || 'Not provided'}</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; padding: 15px 0; border-bottom: 1px solid #e9ecef;">
                            <span style="font-weight: 700; color: #495057;">Contact Phone:</span>
                            <span style="font-weight: 500; color: #212529;">${registration.emergency_contact_phone || 'Not provided'}</span>
                        </div>
                        
                        <!-- Professional Information -->
                        <h4 style="margin: 20px 0 15px 0; color: var(--primary); border-bottom: 2px solid var(--primary); padding-bottom: 5px;">Professional Information</h4>
                        <div style="display: flex; justify-content: space-between; padding: 15px 0; border-bottom: 1px solid #e9ecef;">
                            <span style="font-weight: 700; color: #495057;">DOT Accreditation #:</span>
                            <span style="font-weight: 500; color: #212529;">${registration.dot_accreditation || 'Not provided'}</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; padding: 15px 0; border-bottom: 1px solid #e9ecef;">
                            <span style="font-weight: 700; color: #495057;">Accreditation Expiry:</span>
                            <span style="font-weight: 500; color: #212529;">${registration.accreditation_expiry ? new Date(registration.accreditation_expiry).toLocaleDateString() : 'Not provided'}</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; padding: 15px 0; border-bottom: 1px solid #e9ecef;">
                            <span style="font-weight: 700; color: #495057;">Specialization:</span>
                            <span style="font-weight: 500; color: #212529;">${registration.specialization || 'Not provided'}</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; padding: 15px 0; border-bottom: 1px solid #e9ecef;">
                            <span style="font-weight: 700; color: #495057;">Years of Experience:</span>
                            <span style="font-weight: 500; color: #212529;">${registration.years_experience || 0} years</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; padding: 15px 0; border-bottom: 1px solid #e9ecef;">
                            <span style="font-weight: 700; color: #495057;">First Aid Certified:</span>
                            <span style="font-weight: 500; color: #212529;">${registration.first_aid_certified || 'No'}</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; padding: 15px 0; border-bottom: 1px solid #e9ecef;">
                            <span style="font-weight: 700; color: #495057;">First Aid Expiry:</span>
                            <span style="font-weight: 500; color: #212529;">${registration.first_aid_expiry ? new Date(registration.first_aid_expiry).toLocaleDateString() : 'Not provided'}</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; padding: 15px 0; border-bottom: 1px solid #e9ecef;">
                            <span style="font-weight: 700; color: #495057;">Base Location:</span>
                            <span style="font-weight: 500; color: #212529;">${registration.base_location || 'Not provided'}</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; padding: 15px 0; border-bottom: 1px solid #e9ecef;">
                            <span style="font-weight: 700; color: #495057;">Employment Type:</span>
                            <span style="font-weight: 500; color: #212529;">${registration.employment_type || 'Not specified'}</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; padding: 15px 0; border-bottom: 1px solid #e9ecef;">
                            <span style="font-weight: 700; color: #495057;">Has Vehicle:</span>
                            <span style="font-weight: 500; color: #212529;">${registration.has_vehicle || 'No'}</span>
                        </div>
                        
                        <!-- Application Status -->
                        <h4 style="margin: 20px 0 15px 0; color: var(--primary); border-bottom: 2px solid var(--primary); padding-bottom: 5px;">Application Status</h4>
                        <div style="display: flex; justify-content: space-between; padding: 15px 0; border-bottom: 1px solid #e9ecef;">
                            <span style="font-weight: 700; color: #495057;">Status:</span>
                            <span class="status-badge status-${registration.status || 'pending'}">${registration.status || 'Pending'}</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; padding: 15px 0; border-bottom: 1px solid #e9ecef;">
                            <span style="font-weight: 700; color: #495057;">Application Date:</span>
                            <span style="font-weight: 500; color: #212529;">${registration.application_date ? new Date(registration.application_date).toLocaleDateString() : 'Not available'}</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; padding: 15px 0; border-bottom: 1px solid #e9ecef;">
                            <span style="font-weight: 700; color: #495057;">Review Date:</span>
                            <span style="font-weight: 500; color: #212529;">${registration.review_date ? new Date(registration.review_date).toLocaleDateString() : 'Not reviewed yet'}</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; padding: 15px 0;">
                            <span style="font-weight: 700; color: #495057;">Admin Notes:</span>
                            <span style="font-weight: 500; color: #212529;">${registration.admin_notes || 'No notes'}</span>
                        </div>
                        
                        <!-- Documents -->
                        <h4 style="margin: 20px 0 15px 0; color: var(--primary); border-bottom: 2px solid var(--primary); padding-bottom: 5px;">Documents</h4>
                        ${registration.resume_file ? `
                            <div style="display: flex; justify-content: space-between; padding: 15px 0; border-bottom: 1px solid #e9ecef;">
                                <span style="font-weight: 700; color: #495057;">Resume/CV:</span>
                                <a href="../uploads/resumes/${registration.resume_file}" target="_blank" style="color: var(--primary); text-decoration: none;">View Resume/CV</a>
                            </div>
                        ` : ''}
                        ${registration.dot_id_file ? `
                            <div style="display: flex; justify-content: space-between; padding: 15px 0; border-bottom: 1px solid #e9ecef;">
                                <span style="font-weight: 700; color: #495057;">DOT ID:</span>
                                <a href="../uploads/tour_guide_documents/${registration.dot_id_file}" target="_blank" style="color: var(--primary); text-decoration: none;">View DOT ID</a>
                            </div>
                        ` : ''}
                        ${registration.government_id_file ? `
                            <div style="display: flex; justify-content: space-between; padding: 15px 0; border-bottom: 1px solid #e9ecef;">
                                <span style="font-weight: 700; color: #495057;">Government ID:</span>
                                <a href="../uploads/tour_guide_documents/${registration.government_id_file}" target="_blank" style="color: var(--primary); text-decoration: none;">View Government ID</a>
                            </div>
                        ` : ''}
                        ${registration.nbi_clearance_file ? `
                            <div style="display: flex; justify-content: space-between; padding: 15px 0; border-bottom: 1px solid #e9ecef;">
                                <span style="font-weight: 700; color: #495057;">NBI Clearance:</span>
                                <a href="../uploads/tour_guide_documents/${registration.nbi_clearance_file}" target="_blank" style="color: var(--primary); text-decoration: none;">View NBI Clearance</a>
                            </div>
                        ` : ''}
                        ${registration.first_aid_certificate_file ? `
                            <div style="display: flex; justify-content: space-between; padding: 15px 0; border-bottom: 1px solid #e9ecef;">
                                <span style="font-weight: 700; color: #495057;">First Aid Certificate:</span>
                                <a href="../uploads/tour_guide_documents/${registration.first_aid_certificate_file}" target="_blank" style="color: var(--primary); text-decoration: none;">View Certificate</a>
                            </div>
                        ` : ''}
                        ${registration.id_photo_file ? `
                            <div style="display: flex; justify-content: space-between; padding: 15px 0;">
                                <span style="font-weight: 700; color: #495057;">ID Photo:</span>
                                <a href="../uploads/tour_guide_documents/${registration.id_photo_file}" target="_blank" style="color: var(--primary); text-decoration: none;">View ID Photo</a>
                            </div>
                        ` : ''}
                        
                        <!-- Action Buttons -->
                        <div style="margin-top: 30px; padding: 20px; background: #f8f9fa; border-radius: 8px; text-align: center;">
                            <h4 style="margin: 0 0 15px 0; color: var(--primary);">Review Application</h4>
                            <div style="display: flex; gap: 10px; justify-content: center;">
                                <button type="button" class="btn-success" onclick="updateRegistrationStatus(${registration.id}, 'approved')" style="background: #28a745; color: white; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer;">
                                    <span class="material-icons-outlined" style="vertical-align: middle; margin-right: 5px;">check_circle</span>
                                    Approve
                                </button>
                                <button type="button" class="btn-danger" onclick="updateRegistrationStatus(${registration.id}, 'rejected')" style="background: #dc3545; color: white; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer;">
                                    <span class="material-icons-outlined" style="vertical-align: middle; margin-right: 5px;">cancel</span>
                                    Reject
                                </button>
                                <button type="button" class="btn-warning" onclick="updateRegistrationStatus(${registration.id}, 'under_review')" style="background: #ffc107; color: #212529; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer;">
                                    <span class="material-icons-outlined" style="vertical-align: middle; margin-right: 5px;">clock</span>
                                    Under Review
                                </button>
                            </div>
                        </div>
                    </div>
                `;
            }
        }

        function updateRegistrationStatus(registrationId, status) {
            if (confirm(`Are you sure you want to ${status} this registration?`)) {
                fetch('tour-guide-registrations.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `action=update_status&registration_id=${registrationId}&status=${status}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showSuccessModal(data.message || `Registration ${status} successfully!`, () => {
                            location.reload();
                        });
                    } else {
                        alert(data.message || 'Error updating registration status');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error updating registration status: ' + error.message);
                });
            }
        }

        function editRegistration() {
            // Add small delay to ensure DOM is ready
            setTimeout(() => {
                const registrationId = document.querySelector('#editRegistrationForm input[name="id"]')?.value;
                console.log('Edit Registration ID found:', registrationId);
                console.log('Available registration data:', window.currentRegistrationData);
                console.log('Form elements:', {
                    editIdInput: document.querySelector('#editRegistrationForm input[name="id"]'),
                    currentIdInput: document.getElementById('currentRegistrationId')
                });
                
                if (!registrationId) {
                    alert('No registration selected for editing');
                    return;
                }
            }, 100); // Small delay to ensure DOM is ready
        }

        function openEditForm() {
            const modal = document.getElementById('viewRegistrationModal');
            const content = document.getElementById('viewRegistrationContent');
            
            // Create edit form directly in the modal
            content.innerHTML = `
                <div style="margin-bottom: 25px; padding: 20px; background: #f8f9fa; border-radius: 12px;">
                    <h3 style="margin-bottom: 20px; color: var(--primary);">Edit Registration</h3>
                    <form id="editRegistrationForm" onsubmit="handleEditRegistration(event)">
                        <input type="hidden" name="action" value="edit_registration">
                        <input type="hidden" name="id" value="${window.currentRegistrationData?.id || ''}">
                        
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px;">
                            <div>
                                <label style="display: block; margin-bottom: 5px; font-weight: 600; color: #495057;">Last Name:</label>
                                <input type="text" name="last_name" value="${window.currentRegistrationData?.last_name || ''}" required style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                            </div>
                            <div>
                                <label style="display: block; margin-bottom: 5px; font-weight: 600; color: #495057;">First Name:</label>
                                <input type="text" name="first_name" value="${window.currentRegistrationData?.first_name || ''}" required style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                            </div>
                            <div>
                                <label style="display: block; margin-bottom: 5px; font-weight: 600; color: #495057;">Middle Initial:</label>
                                <input type="text" name="middle_initial" value="${window.currentRegistrationData?.middle_initial || ''}" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                            </div>
                            <div>
                                <label style="display: block; margin-bottom: 5px; font-weight: 600; color: #495057;">Gender:</label>
                                <select name="gender" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                                    <option value="male" ${window.currentRegistrationData?.gender === 'male' ? 'selected' : ''}>Male</option>
                                    <option value="female" ${window.currentRegistrationData?.gender === 'female' ? 'selected' : ''}>Female</option>
                                    <option value="other" ${window.currentRegistrationData?.gender === 'other' ? 'selected' : ''}>Other</option>
                                </select>
                            </div>
                            <div>
                                <label style="display: block; margin-bottom: 5px; font-weight: 600; color: #495057;">Email:</label>
                                <input type="email" name="email" value="${window.currentRegistrationData?.email || ''}" required style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                            </div>
                            <div>
                                <label style="display: block; margin-bottom: 5px; font-weight: 600; color: #495057;">Primary Phone:</label>
                                <input type="tel" name="primary_phone" value="${window.currentRegistrationData?.primary_phone || ''}" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                            </div>
                            <div>
                                <label style="display: block; margin-bottom: 5px; font-weight: 600; color: #495057;">Specialization:</label>
                                <input type="text" name="specialization" value="${window.currentRegistrationData?.specialization || ''}" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                            </div>
                            <div>
                                <label style="display: block; margin-bottom: 5px; font-weight: 600; color: #495057;">Years of Experience:</label>
                                <input type="number" name="years_of_experience" value="${window.currentRegistrationData?.years_of_experience || 0}" min="0" max="50" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                            </div>
                            <div>
                                <label style="display: block; margin-bottom: 5px; font-weight: 600; color: #495057;">Status:</label>
                                <select name="status" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                                    <option value="Pending" ${window.currentRegistrationData?.status === 'Pending' ? 'selected' : ''}>Pending</option>
                                    <option value="Approved" ${window.currentRegistrationData?.status === 'Approved' ? 'selected' : ''}>Approved</option>
                                    <option value="Rejected" ${window.currentRegistrationData?.status === 'Rejected' ? 'selected' : ''}>Rejected</option>
                                </select>
                            </div>
                            <div>
                                <label style="display: block; margin-bottom: 5px; font-weight: 600; color: #495057;">Admin Notes:</label>
                                <textarea name="admin_notes" rows="3" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">${window.currentRegistrationData?.admin_notes || ''}</textarea>
                            </div>
                        </div>
                        
                        <div style="display: flex; gap: 10px; justify-content: flex-end; margin-top: 20px;">
                            <button type="button" class="btn-secondary" onclick="closeViewRegistrationModal()">Cancel</button>
                            <button type="submit" class="btn-primary">Save Changes</button>
                        </div>
                    </form>
                </div>
            `;
            
            // Update modal header
            const header = modal.querySelector('.modal-header h2');
            if (header) {
                header.textContent = 'Edit Registration';
            }
            
            // Show the modal
            modal.style.display = 'block';
            modal.classList.add('show');
            document.body.style.overflow = 'hidden';
        }

        function closeSuccessModal() {
            const modal = document.getElementById('successModal');
            modal.style.display = 'none';
            modal.classList.remove('show');
            document.body.style.overflow = 'auto';
            
            // Execute callback if exists
            if (window.successModalCallback) {
                const callback = window.successModalCallback;
                window.successModalCallback = null; // Clear callback
                callback();
            }
        }

        function showSuccessModal(message, callback = null) {
            const modal = document.getElementById('successModal');
            const messageEl = document.getElementById('successMessage');
            
            messageEl.textContent = message;
            modal.style.display = 'block';
            modal.classList.add('show');
            document.body.style.overflow = 'hidden';
            
            // Store callback to execute after modal closes
            if (callback) {
                window.successModalCallback = callback;
            }
        }

        function closeViewRegistrationModal() {
            const modal = document.getElementById('viewRegistrationModal');
            modal.style.display = 'none';
            modal.classList.remove('show');
            document.body.style.overflow = 'auto';
        }

        function handleEditRegistration(event) {
            event.preventDefault();
            const form = event.target;
            const formData = new FormData(form);
            
            // Add the action parameter
            formData.append('action', 'edit_registration');
            
            // Debug logging
            console.log('Form data being submitted:');
            for (let [key, value] of formData.entries()) {
                console.log(key + ':', value);
            }
            
            // Convert to URL encoded for debugging
            const urlEncodedData = new URLSearchParams(formData);
            console.log('URL encoded data:', urlEncodedData.toString());
            
            fetch('tour-guide-registrations.php', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                console.log('Response status:', response.status);
                console.log('Response headers:', response.headers);
                return response.text(); // Get raw text first
            })
            .then(text => {
                console.log('Raw response text:', text);
                console.log('Response text length:', text.length);
                
                // Handle empty response
                if (!text || text.trim() === '') {
                    console.error('Empty response received');
                    alert('Error updating registration: Empty server response');
                    return;
                }
                
                try {
                    const data = JSON.parse(text);
                    console.log('Parsed JSON:', data);
                    if (data.success) {
                        closeViewRegistrationModal();
                        showSuccessModal(data.message || 'Registration updated successfully!', () => {
                            location.reload();
                        });
                    } else {
                        alert(data.message || 'Error updating registration');
                    }
                } catch (e) {
                    console.error('JSON parse error:', e);
                    console.error('Response that failed to parse:', text);
                    console.error('Response starts with:', text.substring(0, 100));
                    alert('Error updating registration: Invalid server response - ' + text.substring(0, 50) + '...');
                }
            })
            .catch(error => {
                console.error('Fetch error:', error);
                alert('Error updating registration: ' + error.message);
            });
        }

        function updateStatusModal(registrationId, status) {
            const modal = document.getElementById('updateStatusModal');
            const titleEl = document.getElementById('updateStatusModalTitle');
            const textEl = document.getElementById('updateStatusModalText');
            const confirmBtn = document.getElementById('updateStatusConfirmBtn');
            const idInput = document.getElementById('updateRegistrationId');
            const actionInput = document.getElementById('updateStatusAction');

            if (modal && titleEl && textEl && confirmBtn && idInput && actionInput) {
                const actionText = status === 'approved' ? 'Approve' : (status === 'rejected' ? 'Reject' : 'Update');
                titleEl.textContent = `${actionText} Registration`;
                textEl.textContent = `Are you sure you want to ${actionText.toLowerCase()} this registration?`;
                confirmBtn.textContent = actionText;
                idInput.value = registrationId;
                actionInput.value = status;

                modal.style.display = 'block';
                modal.classList.add('show');
                document.body.style.overflow = 'hidden';
            }
        }

        function closeUpdateStatusModal() {
            const modal = document.getElementById('updateStatusModal');
            modal.style.display = 'none';
            modal.classList.remove('show');
            document.body.style.overflow = 'auto';
        }

        function handleUpdateStatus(event) {
            event.preventDefault();
            const form = event.target;
            const formData = new FormData(form);

            fetch('tour-guide-registrations.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    closeUpdateStatusModal();
                    location.reload();
                } else {
                    alert(data.message || 'Error updating status');
                }
            })
            .catch(error => {
                alert('Error updating status: ' + error.message);
            });
        }

        function deleteRegistration(registrationId) {
            if (confirm('Are you sure you want to delete this registration?')) {
                fetch('tour-guide-registrations.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `action=delete_registration&registration_id=${registrationId}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert(data.message);
                        location.reload();
                    } else {
                        alert(data.message || 'Error deleting registration');
                    }
                })
                .catch(error => {
                    alert('Error deleting registration: ' + error.message);
                });
            }
        }

        function editRegistration(registrationId) {
            // First get the registration data, then open the view modal in edit mode
            fetch('tour-guide-registrations.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `action=get_registration&registration_id=${registrationId}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Store registration data globally
                    window.currentRegistrationData = data.data;
                    populateViewModal(data.data);
                    const modal = document.getElementById('viewRegistrationModal');
                    modal.style.display = 'block';
                    modal.classList.add('show');
                    document.body.style.overflow = 'hidden';
                    
                    // Show edit form after a short delay
                    setTimeout(() => {
                        openEditForm();
                    }, 100);
                } else {
                    alert(data.message || 'Error fetching registration data');
                }
            })
            .catch(error => {
                alert('Error fetching registration data: ' + error.message);
            });
        }
    </script>
</body>
</html>
<?php closeAdminConnection($conn); ?>
