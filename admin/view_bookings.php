<?php
// Admin Bookings Viewer
// This page allows administrators to view all bookings

session_start();

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header('Location: ../log-in/log-in.php');
    exit();
}

require_once '../config/database.php';

// Get database connection
$conn = getDatabaseConnection();

// Handle status updates
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['booking_id']) && isset($_POST['action'])) {
    $bookingId = (int)$_POST['booking_id'];
    $action = $_POST['action'];
    
    $newStatus = '';
    switch ($action) {
        case 'confirm':
            $newStatus = 'confirmed';
            break;
        case 'cancel':
            $newStatus = 'cancelled';
            break;
        case 'complete':
            $newStatus = 'completed';
            break;
    }
    
    if ($newStatus) {
        $updateQuery = "UPDATE bookings SET status = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?";
        $stmt = $conn->prepare($updateQuery);
        $stmt->bind_param("si", $newStatus, $bookingId);
        $stmt->execute();
        $stmt->close();
        
        $message = "Booking status updated to $newStatus";
    }
}

// Get filter parameters
$statusFilter = $_GET['status'] ?? 'all';
$dateFilter = $_GET['date'] ?? '';

// Build query
$whereConditions = [];
if ($statusFilter !== 'all') {
    $whereConditions[] = "b.status = '" . $conn->real_escape_string($statusFilter) . "'";
}
if ($dateFilter) {
    $whereConditions[] = "DATE(b.booking_date) = '" . $conn->real_escape_string($dateFilter) . "'";
}

$whereClause = !empty($whereConditions) ? 'WHERE ' . implode(' AND ', $whereConditions) : '';

// Fetch bookings
$query = "SELECT b.*, u.first_name, u.last_name, u.email as user_email, tg.name as guide_name, tg.contact_number as guide_contact
          FROM bookings b 
          LEFT JOIN users u ON b.user_id = u.id 
          LEFT JOIN tour_guides tg ON b.guide_id = tg.id 
          $whereClause 
          ORDER BY b.created_at DESC";

$result = $conn->query($query);

// Get statistics
$statsQuery = "SELECT status, COUNT(*) as count FROM bookings GROUP BY status";
$statsResult = $conn->query($statsQuery);
$stats = [];
while ($row = $statsResult->fetch_assoc()) {
    $stats[$row['status']] = $row['count'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Bookings - SJDM Tours Admin</title>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="admin-styles.css">
    <style>
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            text-align: center;
        }
        .stat-number {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 5px;
        }
        .stat-label {
            color: #666;
            font-size: 0.9rem;
        }
        .status-pending { color: #f59e0b; }
        .status-confirmed { color: #10b981; }
        .status-cancelled { color: #ef4444; }
        .status-completed { color: #3b82f6; }
        
        .filters {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            display: flex;
            gap: 15px;
            align-items: center;
            flex-wrap: wrap;
        }
        
        .bookings-table {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #e5e7eb;
        }
        
        th {
            background: #f9fafb;
            font-weight: 600;
            color: #374151;
        }
        
        .action-buttons {
            display: flex;
            gap: 5px;
        }
        
        .btn-small {
            padding: 5px 10px;
            font-size: 0.8rem;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 3px;
        }
        
        .btn-confirm { background: #10b981; color: white; }
        .btn-cancel { background: #ef4444; color: white; }
        .btn-complete { background: #3b82f6; color: white; }
        
        .badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 0.8rem;
            font-weight: 500;
        }
        
        .badge-pending { background: #fef3c7; color: #92400e; }
        .badge-confirmed { background: #d1fae5; color: #065f46; }
        .badge-cancelled { background: #fee2e2; color: #991b1b; }
        .badge-completed { background: #dbeafe; color: #1e40af; }
    </style>
</head>
<body>
    <div class="admin-container">
        <header class="admin-header">
            <h1>View Bookings</h1>
            <div class="header-actions">
                <a href="dashboard.php" class="btn-secondary">
                    <span class="material-icons-outlined">dashboard</span>
                    Dashboard
                </a>
                <a href="logout.php" class="btn-secondary">
                    <span class="material-icons-outlined">logout</span>
                    Logout
                </a>
            </div>
        </header>

        <main class="admin-content">
            <?php if (isset($message)): ?>
                <div class="alert alert-success">
                    <span class="material-icons-outlined">check_circle</span>
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>

            <!-- Statistics -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-number status-pending"><?php echo $stats['pending'] ?? 0; ?></div>
                    <div class="stat-label">Pending</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number status-confirmed"><?php echo $stats['confirmed'] ?? 0; ?></div>
                    <div class="stat-label">Confirmed</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number status-cancelled"><?php echo $stats['cancelled'] ?? 0; ?></div>
                    <div class="stat-label">Cancelled</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number status-completed"><?php echo $stats['completed'] ?? 0; ?></div>
                    <div class="stat-label">Completed</div>
                </div>
            </div>

            <!-- Filters -->
            <div class="filters">
                <div>
                    <label for="statusFilter">Status:</label>
                    <select id="statusFilter" onchange="window.location.href='?status=' + this.value + '&date=<?php echo $dateFilter; ?>'">
                        <option value="all" <?php echo $statusFilter === 'all' ? 'selected' : ''; ?>>All</option>
                        <option value="pending" <?php echo $statusFilter === 'pending' ? 'selected' : ''; ?>>Pending</option>
                        <option value="confirmed" <?php echo $statusFilter === 'confirmed' ? 'selected' : ''; ?>>Confirmed</option>
                        <option value="cancelled" <?php echo $statusFilter === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                        <option value="completed" <?php echo $statusFilter === 'completed' ? 'selected' : ''; ?>>Completed</option>
                    </select>
                </div>
                <div>
                    <label for="dateFilter">Date:</label>
                    <input type="date" id="dateFilter" value="<?php echo $dateFilter; ?>" onchange="window.location.href='?status=<?php echo $statusFilter; ?>&date=' + this.value">
                </div>
                <button onclick="window.location.href='view_bookings.php'" class="btn-secondary">
                    <span class="material-icons-outlined">refresh</span>
                    Reset
                </button>
            </div>

            <!-- Bookings Table -->
            <div class="bookings-table">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Reference</th>
                            <th>Guest</th>
                            <th>Email</th>
                            <th>Guide</th>
                            <th>Destination</th>
                            <th>Dates</th>
                            <th>Guests</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result && $result->num_rows > 0): ?>
                            <?php while ($booking = $result->fetch_assoc()): ?>
                                <tr>
                                    <td>#<?php echo $booking['id']; ?></td>
                                    <td><?php echo $booking['booking_reference']; ?></td>
                                    <td><?php echo $booking['first_name'] . ' ' . $booking['last_name']; ?></td>
                                    <td><?php echo $booking['email']; ?></td>
                                    <td><?php echo $booking['guide_name'] ?: 'Not assigned'; ?></td>
                                    <td><?php echo $booking['destination']; ?></td>
                                    <td><?php echo date('M j, Y', strtotime($booking['check_in_date'])); ?> - <?php echo date('M j, Y', strtotime($booking['check_out_date'])); ?></td>
                                    <td><?php echo $booking['number_of_people']; ?></td>
                                    <td>₱<?php echo number_format($booking['total_amount'], 2); ?></td>
                                    <td>
                                        <span class="badge badge-<?php echo $booking['status']; ?>">
                                            <?php echo ucfirst($booking['status']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="action-buttons">
                                            <?php if ($booking['status'] === 'pending'): ?>
                                                <form method="POST" style="display:inline;">
                                                    <input type="hidden" name="booking_id" value="<?php echo $booking['id']; ?>">
                                                    <input type="hidden" name="action" value="confirm">
                                                    <button type="submit" class="btn-small btn-confirm" onclick="return confirm('Confirm this booking?')">
                                                        <span class="material-icons-outlined">check</span>
                                                        Confirm
                                                    </button>
                                                </form>
                                                <form method="POST" style="display:inline;">
                                                    <input type="hidden" name="booking_id" value="<?php echo $booking['id']; ?>">
                                                    <input type="hidden" name="action" value="cancel">
                                                    <button type="submit" class="btn-small btn-cancel" onclick="return confirm('Cancel this booking?')">
                                                        <span class="material-icons-outlined">close</span>
                                                        Cancel
                                                    </button>
                                                </form>
                                            <?php endif; ?>
                                            
                                            <?php if ($booking['status'] === 'confirmed'): ?>
                                                <form method="POST" style="display:inline;">
                                                    <input type="hidden" name="booking_id" value="<?php echo $booking['id']; ?>">
                                                    <input type="hidden" name="action" value="complete">
                                                    <button type="submit" class="btn-small btn-complete" onclick="return confirm('Mark this booking as completed?')">
                                                        <span class="material-icons-outlined">done_all</span>
                                                        Complete
                                                    </button>
                                                </form>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="11" style="text-align: center; padding: 40px;">
                                    <span class="material-icons-outlined" style="font-size: 48px; color: #9ca3af;">inbox</span>
                                    <p style="color: #6b7280; margin-top: 10px;">No bookings found</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</body>
</html>
