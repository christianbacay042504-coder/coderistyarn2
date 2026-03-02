<?php
/**
 * Admin Reports Page - Comprehensive Transaction Reports with Filtering
 * Created: March 3, 2026
 * Enhanced for complete transaction analysis
 */

require_once __DIR__ . '/../config/auth.php';

// Check if user is admin
requireAdmin();

$currentUser = getCurrentUser();

// Get database connection
$conn = getDatabaseConnection();

// Initialize variables for filtering
$startDate = $_GET['start_date'] ?? date('Y-m-d', strtotime('-30 days'));
$endDate = $_GET['end_date'] ?? date('Y-m-d');
$transactionType = $_GET['transaction_type'] ?? 'all';
$status = $_GET['status'] ?? 'all';
$userId = $_GET['user_id'] ?? '';
$guideId = $_GET['guide_id'] ?? '';
$destinationId = $_GET['destination_id'] ?? '';
$paymentMethod = $_GET['payment_method'] ?? 'all';
$amountMin = $_GET['amount_min'] ?? '';
$amountMax = $_GET['amount_max'] ?? '';

// Build WHERE clause dynamically
$whereConditions = [];
$params = [];
$types = '';

// Base date filter for all transactions
if ($startDate && $endDate) {
    $whereConditions[] = "DATE(b.created_at) BETWEEN ? AND ?";
    $params[] = $startDate;
    $params[] = $endDate;
    $types .= 'ss';
} elseif ($startDate) {
    $whereConditions[] = "DATE(b.created_at) >= ?";
    $params[] = $startDate;
    $types .= 's';
} elseif ($endDate) {
    $whereConditions[] = "DATE(b.created_at) <= ?";
    $params[] = $endDate;
    $types .= 's';
}

// Transaction type filter
if ($transactionType !== 'all') {
    switch ($transactionType) {
        case 'bookings':
            $whereConditions[] = "b.id IS NOT NULL";
            break;
        case 'payments':
            $whereConditions[] = "b.payment_method != 'pay_later'";
            break;
        case 'refunds':
            $whereConditions[] = "b.status = 'cancelled'";
            break;
    }
}

// Status filter
if ($status !== 'all') {
    $whereConditions[] = "b.status = ?";
    $params[] = $status;
    $types .= 's';
}

// User filter
if (!empty($userId)) {
    $whereConditions[] = "b.user_id = ?";
    $params[] = $userId;
    $types .= 'i';
}

// Guide filter
if (!empty($guideId)) {
    $whereConditions[] = "b.guide_id = ?";
    $params[] = $guideId;
    $types .= 'i';
}

// Destination filter (text-based search)
if (!empty($destinationId)) {
    $whereConditions[] = "b.destination = ?";
    $params[] = $destinationId;
    $types .= 's';
}

// Payment method filter
if ($paymentMethod !== 'all') {
    $whereConditions[] = "b.payment_method = ?";
    $params[] = $paymentMethod;
    $types .= 's';
}

// Amount range filter
if (!empty($amountMin)) {
    $whereConditions[] = "b.total_amount >= ?";
    $params[] = $amountMin;
    $types .= 'd';
}

if (!empty($amountMax)) {
    $whereConditions[] = "b.total_amount <= ?";
    $params[] = $amountMax;
    $types .= 'd';
}

// Build the complete query
$whereClause = !empty($whereConditions) ? 'WHERE ' . implode(' AND ', $whereConditions) : '';

// Main query for transactions
$query = "
    SELECT 
        b.id,
        b.booking_reference,
        b.user_id,
        b.guide_id,
        b.destination,
        b.tour_name,
        b.booking_date,
        b.total_amount,
        b.payment_method,
        b.status,
        b.created_at,
        u.first_name as user_first_name,
        u.last_name as user_last_name,
        u.email as user_email,
        tg.name as guide_name,
        CASE 
            WHEN b.status = 'completed' THEN 'Completed Booking'
            WHEN b.status = 'confirmed' THEN 'Confirmed Booking'
            WHEN b.status = 'pending' THEN 'Pending Booking'
            WHEN b.status = 'cancelled' THEN 'Cancelled Booking'
            ELSE 'Other'
        END as transaction_category,
        CASE 
            WHEN b.payment_method = 'pay_later' THEN 'Payment Pending'
            WHEN b.payment_method != 'pay_later' THEN 'Payment Received'
            ELSE 'Other'
        END as payment_status_desc
    FROM bookings b
    LEFT JOIN users u ON b.user_id = u.id
    LEFT JOIN tour_guides tg ON b.guide_id = tg.id
    $whereClause
    ORDER BY b.created_at DESC
";

// Prepare and execute query
$transactions = [];
$totalAmount = 0;
$totalBookings = 0;

if ($conn) {
    $stmt = $conn->prepare($query);
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($row = $result->fetch_assoc()) {
        $transactions[] = $row;
        $totalAmount += $row['total_amount'];
        $totalBookings++;
    }
    $stmt->close();
    
    // Get filter options
    $users = $conn->query("SELECT id, first_name, last_name, email FROM users WHERE user_type = 'user' ORDER BY first_name, last_name")->fetch_all(MYSQLI_ASSOC);
    $guides = $conn->query("SELECT id, name FROM tour_guides ORDER BY name")->fetch_all(MYSQLI_ASSOC);
    $destinations = $conn->query("SELECT id, name FROM tourist_spots WHERE status = 'active' ORDER BY name")->fetch_all(MYSQLI_ASSOC);
}

// Get summary statistics
$summaryQuery = "
    SELECT 
        COUNT(*) as total_transactions,
        SUM(total_amount) as total_revenue,
        COUNT(CASE WHEN status = 'completed' THEN 1 END) as completed_bookings,
        COUNT(CASE WHEN status = 'pending' THEN 1 END) as pending_bookings,
        COUNT(CASE WHEN status = 'cancelled' THEN 1 END) as cancelled_bookings,
        COUNT(CASE WHEN payment_method != 'pay_later' THEN 1 END) as paid_bookings,
        COUNT(CASE WHEN payment_method = 'pay_later' THEN 1 END) as unpaid_bookings
    FROM bookings b
    $whereClause
";

$summary = [];
if ($conn) {
    $stmt = $conn->prepare($summaryQuery);
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $summary = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}

// Admin info
$adminMark = 'A';
$roleTitle = 'Administrator';
$adminId = null;

if ($conn) {
    $stmt = $conn->prepare("SELECT a.id, a.admin_mark, a.role_title FROM admin_users a WHERE a.user_id = ?");
    $userId = $currentUser['id'];
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $adminMark = $row['admin_mark'];
        $roleTitle = $row['role_title'];
        $adminId = $row['id'];
    }
    $stmt->close();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaction Reports | SJDM Tours</title>
    <link rel="icon" type="image/png" href="../lgo.png">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet">
    <link rel="stylesheet" href="admin-styles.css">
    <style>
        .reports-container {
            padding: 24px;
            max-width: 1400px;
            margin: 0 auto;
        }
        
        .reports-header {
            margin-bottom: 32px;
        }
        
        .reports-header h1 {
            font-size: 2rem;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 8px;
        }
        
        .reports-header p {
            color: var(--text-secondary);
            font-size: 1rem;
        }
        
        .filters-section {
            background: white;
            border-radius: 12px;
            padding: 24px;
            margin-bottom: 24px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
            border: 1px solid var(--border);
        }
        
        .filters-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 16px;
            margin-bottom: 20px;
        }
        
        .filter-group {
            display: flex;
            flex-direction: column;
        }
        
        .filter-group label {
            font-size: 0.875rem;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 6px;
        }
        
        .filter-group select,
        .filter-group input {
            padding: 10px 12px;
            border: 1px solid var(--border);
            border-radius: 8px;
            font-size: 0.875rem;
            transition: all 0.2s ease;
        }
        
        .filter-group select:focus,
        .filter-group input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(44, 95, 45, 0.1);
        }
        
        .filters-actions {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }
        
        .btn-apply {
            background: var(--primary);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .btn-apply:hover {
            background: var(--primary-dark);
            transform: translateY(-1px);
        }
        
        .btn-reset {
            background: transparent;
            color: var(--text-secondary);
            border: 1px solid var(--border);
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .btn-reset:hover {
            background: var(--bg-light);
            border-color: var(--text-secondary);
        }
        
        .summary-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 16px;
            margin-bottom: 24px;
        }
        
        .summary-card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
            border: 1px solid var(--border);
            text-align: center;
        }
        
        .summary-card h3 {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 4px;
        }
        
        .summary-card p {
            font-size: 0.875rem;
            color: var(--text-secondary);
            margin: 0;
        }
        
        .transactions-table {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
            border: 1px solid var(--border);
        }
        
        .table-header {
            padding: 20px 24px;
            border-bottom: 1px solid var(--border);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .table-header h2 {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--text-primary);
            margin: 0;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .table-actions {
            display: flex;
            gap: 12px;
        }
        
        .btn-export {
            background: var(--success);
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.875rem;
            cursor: pointer;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        
        .btn-export:hover {
            background: #059669;
            transform: translateY(-1px);
        }
        
        .data-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .data-table th {
            background: var(--bg-light);
            padding: 12px 16px;
            text-align: left;
            font-weight: 600;
            color: var(--text-primary);
            font-size: 0.875rem;
            border-bottom: 1px solid var(--border);
        }
        
        .data-table td {
            padding: 12px 16px;
            border-bottom: 1px solid var(--border);
            font-size: 0.875rem;
            color: var(--text-primary);
        }
        
        .data-table tbody tr:hover {
            background: var(--bg-light);
        }
        
        .status-badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: capitalize;
        }
        
        .status-badge.completed { background: #dcfce7; color: #166534; }
        .status-badge.confirmed { background: #dbeafe; color: #1e40af; }
        .status-badge.pending { background: #fef3c7; color: #92400e; }
        .status-badge.cancelled { background: #fee2e2; color: #991b1b; }
        .status-badge.paid { background: #dcfce7; color: #166534; }
        .status-badge.unpaid { background: #fef3c7; color: #92400e; }
        .status-badge.refunded { background: #e0e7ff; color: #3730a3; }
        
        .amount {
            font-weight: 600;
            color: var(--primary);
        }
        
        .no-results {
            text-align: center;
            padding: 60px 20px;
            color: var(--text-secondary);
        }
        
        .no-results .material-icons-outlined {
            font-size: 48px;
            opacity: 0.3;
            margin-bottom: 16px;
        }
        
        @media (max-width: 768px) {
            .reports-container {
                padding: 16px;
            }
            
            .filters-grid {
                grid-template-columns: 1fr;
            }
            
            .summary-cards {
                grid-template-columns: 1fr;
            }
            
            .table-header {
                flex-direction: column;
                gap: 16px;
                align-items: flex-start;
            }
            
            .data-table {
                font-size: 0.75rem;
            }
            
            .data-table th,
            .data-table td {
                padding: 8px 12px;
            }
        }
    </style>
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
                <a href="bookings.php" class="nav-item">
                    <span class="material-icons-outlined">event</span>
                    <span>Bookings</span>
                </a>
                <a href="analytics.php" class="nav-item">
                    <span class="material-icons-outlined">analytics</span>
                    <span>Analytics</span>
                </a>
                <a href="reports.php" class="nav-item active">
                    <span class="material-icons-outlined">assessment</span>
                    <span>Reports</span>
                </a>
            </nav>
            
            <div class="sidebar-footer">
                <a href="logout.php" class="logout-btn" id="logoutBtn" onclick="handleLogout(event)">
                    <span class="material-icons-outlined">logout</span>
                    <span>Logout</span>
                </a>
            </div>
        </aside>
        
        <!-- Main Content -->
        <main class="main-content">
            <!-- Top Bar -->
            <header class="top-bar">
                <div class="page-title">
                    <h1 id="pageTitle">Transaction Reports</h1>
                    <p id="pageSubtitle">Comprehensive transaction analysis and filtering</p>
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
                            <a href="javascript:void(0)" class="dropdown-item" id="adminSignoutLink" onclick="openSignOutModal()">
                                <span class="material-icons-outlined">logout</span>
                                <span>Sign Out</span>
                            </a>
                        </div>
                    </div>
                </div>
            </header>
            
            <div class="reports-container">
                <!-- Reports Header -->
                <div class="reports-header">
                    <h1>Transaction Reports</h1>
                    <p>Filter and analyze all transactions across the platform</p>
                </div>
                
                <!-- Filters Section -->
                <div class="filters-section">
                    <form method="GET" action="reports.php">
                        <div class="filters-grid">
                            <div class="filter-group">
                                <label for="start_date">Start Date</label>
                                <input type="date" id="start_date" name="start_date" value="<?php echo htmlspecialchars($startDate); ?>">
                            </div>
                            
                            <div class="filter-group">
                                <label for="end_date">End Date</label>
                                <input type="date" id="end_date" name="end_date" value="<?php echo htmlspecialchars($endDate); ?>">
                            </div>
                            
                            <div class="filter-group">
                                <label for="transaction_type">Transaction Type</label>
                                <select id="transaction_type" name="transaction_type">
                                    <option value="all" <?php echo $transactionType === 'all' ? 'selected' : ''; ?>>All Transactions</option>
                                    <option value="bookings" <?php echo $transactionType === 'bookings' ? 'selected' : ''; ?>>All Bookings</option>
                                    <option value="payments" <?php echo $transactionType === 'payments' ? 'selected' : ''; ?>>Paid Bookings</option>
                                    <option value="refunds" <?php echo $transactionType === 'refunds' ? 'selected' : ''; ?>>Refunded Bookings</option>
                                </select>
                            </div>
                            
                            <div class="filter-group">
                                <label for="status">Booking Status</label>
                                <select id="status" name="status">
                                    <option value="all" <?php echo $status === 'all' ? 'selected' : ''; ?>>All Status</option>
                                    <option value="pending" <?php echo $status === 'pending' ? 'selected' : ''; ?>>Pending</option>
                                    <option value="confirmed" <?php echo $status === 'confirmed' ? 'selected' : ''; ?>>Confirmed</option>
                                    <option value="completed" <?php echo $status === 'completed' ? 'selected' : ''; ?>>Completed</option>
                                    <option value="cancelled" <?php echo $status === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                </select>
                            </div>
                            
                            <div class="filter-group">
                                <label for="user_id">User</label>
                                <select id="user_id" name="user_id">
                                    <option value="">All Users</option>
                                    <?php foreach ($users as $user): ?>
                                        <option value="<?php echo $user['id']; ?>" <?php echo $userId == $user['id'] ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name'] . ' (' . $user['email'] . ')'); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="filter-group">
                                <label for="guide_id">Tour Guide</label>
                                <select id="guide_id" name="guide_id">
                                    <option value="">All Guides</option>
                                    <?php foreach ($guides as $guide): ?>
                                        <option value="<?php echo $guide['id']; ?>" <?php echo $guideId == $guide['id'] ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($guide['name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="filter-group">
                                <label for="destination_id">Destination</label>
                                <select id="destination_id" name="destination_id">
                                    <option value="">All Destinations</option>
                                    <?php foreach ($destinations as $destination): ?>
                                        <option value="<?php echo $destination['id']; ?>" <?php echo $destinationId == $destination['id'] ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($destination['name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="filter-group">
                                <label for="payment_method">Payment Method</label>
                                <select id="payment_method" name="payment_method">
                                    <option value="all" <?php echo $paymentMethod === 'all' ? 'selected' : ''; ?>>All Methods</option>
                                    <option value="pay_later" <?php echo $paymentMethod === 'pay_later' ? 'selected' : ''; ?>>Pay Later</option>
                                    <option value="gcash" <?php echo $paymentMethod === 'gcash' ? 'selected' : ''; ?>>GCash</option>
                                    <option value="bank_transfer" <?php echo $paymentMethod === 'bank_transfer' ? 'selected' : ''; ?>>Bank Transfer</option>
                                </select>
                            </div>
                            
                            <div class="filter-group">
                                <label for="amount_min">Min Amount (₱)</label>
                                <input type="number" id="amount_min" name="amount_min" value="<?php echo htmlspecialchars($amountMin); ?>" step="0.01" min="0">
                            </div>
                            
                            <div class="filter-group">
                                <label for="amount_max">Max Amount (₱)</label>
                                <input type="number" id="amount_max" name="amount_max" value="<?php echo htmlspecialchars($amountMax); ?>" step="0.01" min="0">
                            </div>
                        </div>
                        
                        <div class="filters-actions">
                            <button type="submit" class="btn-apply">
                                <span class="material-icons-outlined">filter_list</span>
                                Apply Filters
                            </button>
                            <a href="reports.php" class="btn-reset">
                                <span class="material-icons-outlined">refresh</span>
                                Reset
                            </a>
                        </div>
                    </form>
                </div>
                
                <!-- Summary Cards -->
                <div class="summary-cards">
                    <div class="summary-card">
                        <h3><?php echo $summary['total_transactions'] ?? 0; ?></h3>
                        <p>Total Transactions</p>
                    </div>
                    <div class="summary-card">
                        <h3>₱<?php echo number_format($summary['total_revenue'] ?? 0, 2); ?></h3>
                        <p>Total Revenue</p>
                    </div>
                    <div class="summary-card">
                        <h3><?php echo $summary['completed_bookings'] ?? 0; ?></h3>
                        <p>Completed</p>
                    </div>
                    <div class="summary-card">
                        <h3><?php echo $summary['pending_bookings'] ?? 0; ?></h3>
                        <p>Pending</p>
                    </div>
                    <div class="summary-card">
                        <h3><?php echo $summary['cancelled_bookings'] ?? 0; ?></h3>
                        <p>Cancelled</p>
                    </div>
                    <div class="summary-card">
                        <h3><?php echo $summary['paid_bookings'] ?? 0; ?></h3>
                        <p>Paid</p>
                    </div>
                </div>
                
                <!-- Transactions Table -->
                <div class="transactions-table">
                    <div class="table-header">
                        <h2>
                            <span class="material-icons-outlined">receipt_long</span>
                            Transaction Details
                        </h2>
                        <div class="table-actions">
                            <button class="btn-export" onclick="exportToCSV()">
                                <span class="material-icons-outlined">download</span>
                                Export CSV
                            </button>
                        </div>
                    </div>
                    
                    <div class="table-container">
                        <?php if (!empty($transactions)): ?>
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th>Reference</th>
                                        <th>Date</th>
                                        <th>Customer</th>
                                        <th>Tour</th>
                                        <th>Guide</th>
                                        <th>Tour Date</th>
                                        <th>Amount</th>
                                        <th>Payment</th>
                                        <th>Status</th>
                                        <th>Type</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($transactions as $transaction): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($transaction['booking_reference']); ?></td>
                                            <td><?php echo date('M d, Y H:i', strtotime($transaction['created_at'])); ?></td>
                                            <td>
                                                <?php echo htmlspecialchars($transaction['user_first_name'] . ' ' . $transaction['user_last_name']); ?>
                                                <br><small style="color: var(--text-secondary);"><?php echo htmlspecialchars($transaction['user_email']); ?></small>
                                            </td>
                                            <td><?php echo htmlspecialchars($transaction['tour_name']); ?></td>
                                            <td><?php echo htmlspecialchars($transaction['guide_name'] ?? 'N/A'); ?></td>
                                            <td><?php echo date('M d, Y', strtotime($transaction['booking_date'])); ?></td>
                                            <td class="amount">₱<?php echo number_format($transaction['total_amount'], 2); ?></td>
                                            <td>
                                                <span class="status-badge <?php echo $transaction['payment_method']; ?>">
                                                    <?php echo htmlspecialchars($transaction['payment_status_desc']); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <span class="status-badge <?php echo $transaction['status']; ?>">
                                                    <?php echo ucfirst($transaction['status']); ?>
                                                </span>
                                            </td>
                                            <td><?php echo htmlspecialchars($transaction['transaction_category']); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php else: ?>
                            <div class="no-results">
                                <span class="material-icons-outlined">search_off</span>
                                <h3>No transactions found</h3>
                                <p>Try adjusting your filters to see more results</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </main>
    </div>
    
    <script src="admin-script.js"></script>
    <script src="admin-profile-dropdown.js"></script>
    <script>
        // Export to CSV function
        function exportToCSV() {
            const table = document.querySelector('.data-table');
            if (!table) return;
            
            let csv = [];
            const rows = table.querySelectorAll('tr');
            
            // Get headers
            const headers = [];
            table.querySelectorAll('th').forEach(th => {
                headers.push(th.textContent.trim());
            });
            csv.push(headers.join(','));
            
            // Get data rows
            table.querySelectorAll('tbody tr').forEach(tr => {
                const row = [];
                tr.querySelectorAll('td').forEach(td => {
                    // Clean up the text for CSV
                    let text = td.textContent.trim();
                    // Remove line breaks and extra spaces
                    text = text.replace(/\s+/g, ' ');
                    // Escape quotes and wrap in quotes if contains comma
                    if (text.includes(',') || text.includes('"')) {
                        text = '"' + text.replace(/"/g, '""') + '"';
                    }
                    row.push(text);
                });
                csv.push(row.join(','));
            });
            
            // Create and download CSV file
            const csvContent = csv.join('\n');
            const blob = new Blob([csvContent], { type: 'text/csv' });
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.setAttribute('hidden', '');
            a.setAttribute('href', url);
            a.setAttribute('download', 'transaction_reports_' + new Date().toISOString().split('T')[0] + '.csv');
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
        }
        
        // Auto-submit form on date change (optional)
        document.getElementById('start_date').addEventListener('change', function() {
            if (document.getElementById('end_date').value) {
                this.form.submit();
            }
        });
        
        document.getElementById('end_date').addEventListener('change', function() {
            if (document.getElementById('start_date').value) {
                this.form.submit();
            }
        });
    </script>
</body>
</html>