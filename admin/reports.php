<?php
// Reports System - Comprehensive Transaction Reporting
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/auth.php';

// Database connection functions
function getReportsConnection() { return getDatabaseConnection(); }
function initReportsAuth() { requireAdmin(); return getCurrentUser(); }
function closeReportsConnection($conn) { closeDatabaseConnection($conn); }

// Report Functions
function getBookingReports($conn, $filters = []) {
    $where = [];
    $params = [];
    $types = '';
    
    // Date range filter
    if (!empty($filters['start_date'])) {
        $where[] = "DATE(b.created_at) >= ?";
        $params[] = $filters['start_date'];
        $types .= 's';
    }
    if (!empty($filters['end_date'])) {
        $where[] = "DATE(b.created_at) <= ?";
        $params[] = $filters['end_date'];
        $types .= 's';
    }
    
    // Status filter
    if (!empty($filters['status'])) {
        $where[] = "b.status = ?";
        $params[] = $filters['status'];
        $types .= 's';
    }
    
    // Tour category filter (if applicable)
    if (!empty($filters['category'])) {
        $where[] = "ts.category = ?";
        $params[] = $filters['category'];
        $types .= 's';
    }
    
    $whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';
    
    $query = "
        SELECT 
            b.id,
            b.tour_name,
            b.booking_date,
            b.number_of_people,
            b.total_amount,
            b.status,
            b.created_at,
            CONCAT(u.first_name, ' ', u.last_name) as customer_name,
            u.email as customer_email,
            ts.category as tour_category
        FROM bookings b
        LEFT JOIN users u ON b.user_id = u.id
        LEFT JOIN tourist_spots ts ON CONVERT(ts.name USING utf8mb4) = CONVERT(b.tour_name USING utf8mb4)
        $whereClause
        ORDER BY b.created_at DESC
    ";
    
    $stmt = $conn->prepare($query);
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    return $stmt->get_result();
}

function getBookingSummary($conn, $filters = []) {
    $where = [];
    $params = [];
    $types = '';
    
    // Date range filter
    if (!empty($filters['start_date'])) {
        $where[] = "DATE(created_at) >= ?";
        $params[] = $filters['start_date'];
        $types .= 's';
    }
    if (!empty($filters['end_date'])) {
        $where[] = "DATE(created_at) <= ?";
        $params[] = $filters['end_date'];
        $types .= 's';
    }
    
    $whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';
    
    $query = "
        SELECT 
            COUNT(*) as total_bookings,
            SUM(total_amount) as total_revenue,
            SUM(number_of_people) as total_visitors,
            COUNT(CASE WHEN status = 'confirmed' THEN 1 END) as confirmed_bookings,
            COUNT(CASE WHEN status = 'pending' THEN 1 END) as pending_bookings,
            COUNT(CASE WHEN status = 'cancelled' THEN 1 END) as cancelled_bookings,
            COUNT(CASE WHEN status = 'completed' THEN 1 END) as completed_bookings,
            AVG(total_amount) as avg_booking_value
        FROM bookings
        $whereClause
    ";
    
    $stmt = $conn->prepare($query);
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

function getTourGuideReports($conn, $filters = []) {
    $where = [];
    $params = [];
    $types = '';
    
    // Date range filter
    if (!empty($filters['start_date'])) {
        $where[] = "DATE(tg.created_at) >= ?";
        $params[] = $filters['start_date'];
        $types .= 's';
    }
    if (!empty($filters['end_date'])) {
        $where[] = "DATE(tg.created_at) <= ?";
        $params[] = $filters['end_date'];
        $types .= 's';
    }
    
    // Status filter
    if (!empty($filters['guide_status'])) {
        $where[] = "tg.status = ?";
        $params[] = $filters['guide_status'];
        $types .= 's';
    }
    
    $whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';
    
    $query = "
        SELECT 
            tg.id,
            tg.name,
            tg.email,
            tg.specialty,
            tg.status,
            tg.created_at,
            COUNT(b.id) as total_bookings_assigned
        FROM tour_guides tg
        LEFT JOIN bookings b ON b.guide_id = tg.id
        $whereClause
        GROUP BY tg.id
        ORDER BY tg.created_at DESC
    ";
    
    $stmt = $conn->prepare($query);
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    return $stmt->get_result();
}

function getUserActivityReports($conn, $filters = []) {
    $where = [];
    $params = [];
    $types = '';
    
    // Date range filter
    if (!empty($filters['start_date'])) {
        $where[] = "DATE(la.login_time) >= ?";
        $params[] = $filters['start_date'];
        $types .= 's';
    }
    if (!empty($filters['end_date'])) {
        $where[] = "DATE(la.login_time) <= ?";
        $params[] = $filters['end_date'];
        $types .= 's';
    }
    
    $whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';
    
    $query = "
        SELECT 
            la.id,
            CONCAT(u.first_name, ' ', u.last_name) as user_name,
            u.email,
            u.user_type,
            la.login_time,
            la.ip_address,
            la.status as login_status
        FROM login_activity la
        JOIN users u ON la.user_id = u.id
        $whereClause
        ORDER BY la.login_time DESC
    ";
    
    $stmt = $conn->prepare($query);
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    return $stmt->get_result();
}

function getRevenueByCategory($conn, $filters = []) {
    $where = [];
    $params = [];
    $types = '';
    
    // Date range filter
    if (!empty($filters['start_date'])) {
        $where[] = "DATE(b.created_at) >= ?";
        $params[] = $filters['start_date'];
        $types .= 's';
    }
    if (!empty($filters['end_date'])) {
        $where[] = "DATE(b.created_at) <= ?";
        $params[] = $filters['end_date'];
        $types .= 's';
    }
    
    $whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';
    
    $query = "
        SELECT 
            ts.category,
            COUNT(b.id) as booking_count,
            SUM(b.total_amount) as total_revenue,
            SUM(b.number_of_people) as total_visitors,
            AVG(b.total_amount) as avg_booking_value
        FROM bookings b
        LEFT JOIN tourist_spots ts ON CONVERT(ts.name USING utf8mb4) = CONVERT(b.tour_name USING utf8mb4)
        $whereClause
        GROUP BY ts.category
        ORDER BY total_revenue DESC
    ";
    
    $stmt = $conn->prepare($query);
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    return $stmt->get_result();
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $conn = getReportsConnection();
    
    if (isset($_POST['export_report'])) {
        // Export functionality
        $report_type = $_POST['report_type'];
        $filters = [
            'start_date' => $_POST['start_date'] ?? '',
            'end_date' => $_POST['end_date'] ?? '',
            'status' => $_POST['status'] ?? '',
            'category' => $_POST['category'] ?? '',
            'guide_status' => $_POST['guide_status'] ?? ''
        ];
        
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="report_' . date('Y-m-d') . '.csv"');
        
        $output = fopen('php://output', 'w');
        
        switch ($report_type) {
            case 'bookings':
                fputcsv($output, ['ID', 'Tour Name', 'Booking Date', 'People', 'Amount', 'Status', 'Customer', 'Email', 'Category', 'Created At']);
                $result = getBookingReports($conn, $filters);
                while ($row = $result->fetch_assoc()) {
                    fputcsv($output, [
                        $row['id'],
                        $row['tour_name'],
                        $row['booking_date'],
                        $row['number_of_people'],
                        $row['total_amount'],
                        $row['status'],
                        $row['customer_name'],
                        $row['customer_email'],
                        $row['tour_category'],
                        $row['created_at']
                    ]);
                }
                break;
                
            case 'tour_guides':
                fputcsv($output, ['ID', 'Name', 'Email', 'Specialization', 'Status', 'Bookings Assigned', 'Created At']);
                $result = getTourGuideReports($conn, $filters);
                while ($row = $result->fetch_assoc()) {
                    fputcsv($output, [
                        $row['id'],
                        $row['name'],
                        $row['email'],
                        $row['specialty'],
                        $row['status'],
                        $row['total_bookings_assigned'],
                        $row['created_at']
                    ]);
                }
                break;
                
            case 'user_activity':
                fputcsv($output, ['ID', 'User Name', 'Email', 'User Type', 'Login Time', 'IP Address', 'Status']);
                $result = getUserActivityReports($conn, $filters);
                while ($row = $result->fetch_assoc()) {
                    fputcsv($output, [
                        $row['id'],
                        $row['user_name'],
                        $row['email'],
                        $row['user_type'],
                        $row['login_time'],
                        $row['ip_address'],
                        $row['login_status']
                    ]);
                }
                break;
                
            case 'revenue_by_category':
                fputcsv($output, ['Category', 'Booking Count', 'Total Revenue', 'Total Visitors', 'Avg Booking Value']);
                $result = getRevenueByCategory($conn, $filters);
                while ($row = $result->fetch_assoc()) {
                    fputcsv($output, [
                        $row['category'],
                        $row['booking_count'],
                        $row['total_revenue'],
                        $row['total_visitors'],
                        $row['avg_booking_value']
                    ]);
                }
                break;
        }
        
        fclose($output);
        exit();
    }
    
    closeReportsConnection($conn);
}

// Initialize authentication
$user = initReportsAuth();
$conn = getReportsConnection();

// Get current filters
$filters = [
    'start_date' => $_GET['start_date'] ?? date('Y-m-01'),
    'end_date' => $_GET['end_date'] ?? date('Y-m-d'),
    'status' => $_GET['status'] ?? '',
    'category' => $_GET['category'] ?? '',
    'guide_status' => $_GET['guide_status'] ?? ''
];

// Get report data
$bookingReports = getBookingReports($conn, $filters);
$bookingSummary = getBookingSummary($conn, $filters);
$tourGuideReports = getTourGuideReports($conn, $filters);
$userActivityReports = getUserActivityReports($conn, $filters);
$revenueByCategory = getRevenueByCategory($conn, $filters);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports - SJDM Tours Admin</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            color: #333;
        }
        
        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .header {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        }
        
        .header h1 {
            color: #2c3e50;
            margin-bottom: 10px;
            font-size: 2.5em;
        }
        
        .header p {
            color: #7f8c8d;
            font-size: 1.1em;
        }
        
        .filters-section {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        }
        
        .filters-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }
        
        .filter-group {
            display: flex;
            flex-direction: column;
        }
        
        .filter-group label {
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 8px;
        }
        
        .filter-group input,
        .filter-group select {
            padding: 12px;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            font-size: 14px;
            transition: all 0.3s ease;
        }
        
        .filter-group input:focus,
        .filter-group select:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
        
        .filter-buttons {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
        }
        
        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
        }
        
        .btn-success {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
            color: white;
        }
        
        .btn-success:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(17, 153, 142, 0.3);
        }
        
        .summary-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .summary-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 25px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            text-align: center;
            transition: transform 0.3s ease;
        }
        
        .summary-card:hover {
            transform: translateY(-5px);
        }
        
        .summary-card h3 {
            color: #7f8c8d;
            font-size: 0.9em;
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .summary-card .value {
            color: #2c3e50;
            font-size: 2.2em;
            font-weight: 700;
            margin-bottom: 5px;
        }
        
        .summary-card .label {
            color: #95a5a6;
            font-size: 0.9em;
        }
        
        .reports-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(600px, 1fr));
            gap: 30px;
        }
        
        .report-section {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        }
        
        .report-section h2 {
            color: #2c3e50;
            margin-bottom: 20px;
            font-size: 1.8em;
        }
        
        .table-container {
            overflow-x: auto;
            margin-top: 20px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 10px;
            overflow: hidden;
        }
        
        th {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 15px;
            text-align: left;
            font-weight: 600;
        }
        
        td {
            padding: 15px;
            border-bottom: 1px solid #ecf0f1;
        }
        
        tr:hover {
            background: #f8f9fa;
        }
        
        .status-badge {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.85em;
            font-weight: 600;
            text-transform: uppercase;
        }
        
        .status-pending {
            background: #fff3cd;
            color: #856404;
        }
        
        .status-confirmed {
            background: #d4edda;
            color: #155724;
        }
        
        .status-cancelled {
            background: #f8d7da;
            color: #721c24;
        }
        
        .status-completed {
            background: #cce5ff;
            color: #004085;
        }
        
        .status-active {
            background: #d4edda;
            color: #155724;
        }
        
        .status-inactive {
            background: #f8d7da;
            color: #721c24;
        }
        
        .export-form {
            display: inline-block;
            margin-top: 15px;
        }
        
        @media (max-width: 768px) {
            .container {
                padding: 10px;
            }
            
            .header h1 {
                font-size: 2em;
            }
            
            .filters-grid {
                grid-template-columns: 1fr;
            }
            
            .reports-grid {
                grid-template-columns: 1fr;
            }
            
            .summary-cards {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📊 Reports Dashboard</h1>
            <p>Comprehensive transaction and activity reports</p>
        </div>
        
        <div class="filters-section">
            <h2>📅 Filters</h2>
            <form method="GET" class="filters-grid" id="filter-form">
                <div class="filter-group">
                    <label for="start_date">Start Date</label>
                    <input type="date" id="start_date" name="start_date" value="<?php echo htmlspecialchars($filters['start_date']); ?>">
                </div>
                
                <div class="filter-group">
                    <label for="end_date">End Date</label>
                    <input type="date" id="end_date" name="end_date" value="<?php echo htmlspecialchars($filters['end_date']); ?>">
                </div>
                
                <div class="filter-group">
                    <label for="status">Booking Status</label>
                    <select id="status" name="status">
                        <option value="">All Status</option>
                        <option value="pending" <?php echo $filters['status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                        <option value="confirmed" <?php echo $filters['status'] === 'confirmed' ? 'selected' : ''; ?>>Confirmed</option>
                        <option value="cancelled" <?php echo $filters['status'] === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                        <option value="completed" <?php echo $filters['status'] === 'completed' ? 'selected' : ''; ?>>Completed</option>
                    </select>
                </div>
                
                <div class="filter-group">
                    <label for="category">Tour Category</label>
                    <select id="category" name="category">
                        <option value="">All Categories</option>
                        <option value="nature" <?php echo $filters['category'] === 'nature' ? 'selected' : ''; ?>>Nature</option>
                        <option value="historical" <?php echo $filters['category'] === 'historical' ? 'selected' : ''; ?>>Historical</option>
                        <option value="religious" <?php echo $filters['category'] === 'religious' ? 'selected' : ''; ?>>Religious</option>
                        <option value="farm" <?php echo $filters['category'] === 'farm' ? 'selected' : ''; ?>>Farm</option>
                        <option value="park" <?php echo $filters['category'] === 'park' ? 'selected' : ''; ?>>Park</option>
                        <option value="urban" <?php echo $filters['category'] === 'urban' ? 'selected' : ''; ?>>Urban</option>
                    </select>
                </div>
                
                <div class="filter-group">
                    <label for="guide_status">Guide Status</label>
                    <select id="guide_status" name="guide_status">
                        <option value="">All Guides</option>
                        <option value="active" <?php echo $filters['guide_status'] === 'active' ? 'selected' : ''; ?>>Active</option>
                        <option value="inactive" <?php echo $filters['guide_status'] === 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                    </select>
                </div>
            </form>
            
            <div class="filter-buttons">
                <button type="submit" form="filter-form" class="btn btn-primary">
                    🔄 Apply Filters
                </button>
                <form method="GET" style="display: inline;">
                    <button type="submit" class="btn btn-primary">
                        🔄 Reset
                    </button>
                </form>
            </div>
        </div>
        
        <div class="summary-cards">
            <div class="summary-card">
                <h3>Total Bookings</h3>
                <div class="value"><?php echo number_format($bookingSummary['total_bookings'] ?? 0); ?></div>
                <div class="label">Transactions</div>
            </div>
            
            <div class="summary-card">
                <h3>Total Revenue</h3>
                <div class="value">₱<?php echo number_format($bookingSummary['total_revenue'] ?? 0, 2); ?></div>
                <div class="label">Income Generated</div>
            </div>
            
            <div class="summary-card">
                <h3>Total Visitors</h3>
                <div class="value"><?php echo number_format($bookingSummary['total_visitors'] ?? 0); ?></div>
                <div class="label">People Served</div>
            </div>
            
            <div class="summary-card">
                <h3>Avg Booking Value</h3>
                <div class="value">₱<?php echo number_format($bookingSummary['avg_booking_value'] ?? 0, 2); ?></div>
                <div class="label">Per Transaction</div>
            </div>
        </div>
        
        <div class="reports-grid">
            <div class="report-section">
                <h2>📋 Booking Reports</h2>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Tour Name</th>
                                <th>Date</th>
                                <th>People</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Customer</th>
                                <th>Category</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $bookingReports->fetch_assoc()): ?>
                                <tr>
                                    <td>#<?php echo $row['id']; ?></td>
                                    <td><?php echo htmlspecialchars($row['tour_name']); ?></td>
                                    <td><?php echo date('M d, Y', strtotime($row['booking_date'])); ?></td>
                                    <td><?php echo $row['number_of_people']; ?></td>
                                    <td>₱<?php echo number_format($row['total_amount'], 2); ?></td>
                                    <td><span class="status-badge status-<?php echo $row['status']; ?>"><?php echo $row['status']; ?></span></td>
                                    <td><?php echo htmlspecialchars($row['customer_name'] ?? 'N/A'); ?></td>
                                    <td><?php echo htmlspecialchars($row['tour_category'] ?? 'N/A'); ?></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
                
                <form method="POST" class="export-form">
                    <input type="hidden" name="export_report" value="1">
                    <input type="hidden" name="report_type" value="bookings">
                    <input type="hidden" name="start_date" value="<?php echo htmlspecialchars($filters['start_date']); ?>">
                    <input type="hidden" name="end_date" value="<?php echo htmlspecialchars($filters['end_date']); ?>">
                    <input type="hidden" name="status" value="<?php echo htmlspecialchars($filters['status']); ?>">
                    <input type="hidden" name="category" value="<?php echo htmlspecialchars($filters['category']); ?>">
                    <button type="submit" class="btn btn-success">
                        📥 Export Bookings
                    </button>
                </form>
            </div>
            
            <div class="report-section">
                <h2>👥 Tour Guide Reports</h2>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Specialization</th>
                                <th>Status</th>
                                <th>Bookings</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $tourGuideReports->fetch_assoc()): ?>
                                <tr>
                                    <td>#<?php echo $row['id']; ?></td>
                                    <td><?php echo htmlspecialchars($row['first_name'] . ' ' . $row['last_name']); ?></td>
                                    <td><?php echo htmlspecialchars($row['email']); ?></td>
                                    <td><?php echo htmlspecialchars($row['specialization'] ?? 'N/A'); ?></td>
                                    <td><span class="status-badge status-<?php echo $row['status']; ?>"><?php echo $row['status']; ?></span></td>
                                    <td><?php echo $row['total_bookings_assigned']; ?></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
                
                <form method="POST" class="export-form">
                    <input type="hidden" name="export_report" value="1">
                    <input type="hidden" name="report_type" value="tour_guides">
                    <input type="hidden" name="start_date" value="<?php echo htmlspecialchars($filters['start_date']); ?>">
                    <input type="hidden" name="end_date" value="<?php echo htmlspecialchars($filters['end_date']); ?>">
                    <input type="hidden" name="guide_status" value="<?php echo htmlspecialchars($filters['guide_status']); ?>">
                    <button type="submit" class="btn btn-success">
                        📥 Export Tour Guides
                    </button>
                </form>
            </div>
            
            <div class="report-section">
                <h2>💰 Revenue by Category</h2>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Category</th>
                                <th>Bookings</th>
                                <th>Revenue</th>
                                <th>Visitors</th>
                                <th>Avg Value</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $revenueByCategory->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['category'] ?? 'N/A'); ?></td>
                                    <td><?php echo $row['booking_count']; ?></td>
                                    <td>₱<?php echo number_format($row['total_revenue'], 2); ?></td>
                                    <td><?php echo $row['total_visitors']; ?></td>
                                    <td>₱<?php echo number_format($row['avg_booking_value'], 2); ?></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
                
                <form method="POST" class="export-form">
                    <input type="hidden" name="export_report" value="1">
                    <input type="hidden" name="report_type" value="revenue_by_category">
                    <input type="hidden" name="start_date" value="<?php echo htmlspecialchars($filters['start_date']); ?>">
                    <input type="hidden" name="end_date" value="<?php echo htmlspecialchars($filters['end_date']); ?>">
                    <button type="submit" class="btn btn-success">
                        📥 Export Revenue Report
                    </button>
                </form>
            </div>
            
            <div class="report-section">
                <h2>🔐 User Activity Reports</h2>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>User</th>
                                <th>Email</th>
                                <th>Type</th>
                                <th>Login Time</th>
                                <th>IP Address</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $userActivityReports->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['user_name']); ?></td>
                                    <td><?php echo htmlspecialchars($row['email']); ?></td>
                                    <td><?php echo htmlspecialchars($row['user_type']); ?></td>
                                    <td><?php echo date('M d, Y H:i', strtotime($row['login_time'])); ?></td>
                                    <td><?php echo htmlspecialchars($row['ip_address']); ?></td>
                                    <td><span class="status-badge status-<?php echo $row['login_status']; ?>"><?php echo $row['login_status']; ?></span></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
                
                <form method="POST" class="export-form">
                    <input type="hidden" name="export_report" value="1">
                    <input type="hidden" name="report_type" value="user_activity">
                    <input type="hidden" name="start_date" value="<?php echo htmlspecialchars($filters['start_date']); ?>">
                    <input type="hidden" name="end_date" value="<?php echo htmlspecialchars($filters['end_date']); ?>">
                    <button type="submit" class="btn btn-success">
                        📥 Export User Activity
                    </button>
                </form>
            </div>
        </div>
    </div>
    
    <script>
        // Form submission for filters - target the form by ID, not the grid div
        document.getElementById('filter-form').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const params = new URLSearchParams();
            
            for (let [key, value] of formData.entries()) {
                if (value) params.append(key, value);
            }
            
            window.location.href = '?' + params.toString();
        });
        
        // Auto-refresh every 5 minutes silently
        setInterval(() => {
            window.location.reload();
        }, 300000);
    </script>
<?php closeReportsConnection($conn); ?>
</body>
</html> 