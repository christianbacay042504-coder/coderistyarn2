<?php
/**
 * Tour Guide Dashboard
 * Created: February 9, 2026
 */

require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/TourGuide.php';

// Check if user is logged in and is a tour guide
requireTourGuide();

// Get current tour guide
$userId = getCurrentUserId();
$tourGuide = new TourGuide($userId);
$profile = $tourGuide->getProfile();

if (!$profile) {
    // Tour guide profile not found, redirect to registration
    header('Location: register.php');
    exit();
}

// Get recent bookings, reviews, and availability
$reviews = $tourGuide->getReviews();
$availability = $tourGuide->getAvailability();

// Handle profile update
$updateMessage = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $updateData = [
        'license_number' => trim($_POST['license_number'] ?? ''),
        'specialization' => trim($_POST['specialization'] ?? ''),
        'experience_years' => intval($_POST['experience_years'] ?? 0),
        'languages' => trim($_POST['languages'] ?? ''),
        'hourly_rate' => floatval($_POST['hourly_rate'] ?? 0),
        'contact_number' => trim($_POST['contact_number'] ?? ''),
        'bio' => trim($_POST['bio'] ?? '')
    ];
    
    $result = $tourGuide->updateProfile($updateData);
    $updateMessage = $result['message'];
    
    if ($result['success']) {
        $profile = $tourGuide->getProfile(); // Refresh profile data
    }
}

// Handle availability status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_availability'])) {
    $status = $_POST['availability_status'] ?? 'available';
    $result = $tourGuide->updateAvailabilityStatus($status);
    $updateMessage = $result['message'];
    
    if ($result['success']) {
        $profile = $tourGuide->getProfile(); // Refresh profile data
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tour Guide Dashboard - SJDM Tours</title>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #2c5f2d;
            --primary-light: #e8f5e9;
            --primary-dark: #1e4220;
            --secondary: #97bc62;
            --success: #10b981;
            --danger: #ef4444;
            --warning: #f59e0b;
            --info: #3b82f6;
            --text-primary: #1f2937;
            --text-secondary: #4b5563;
            --border: #e0e0e0;
            --bg-light: #f5f7fa;
            --white: #ffffff;
            --gray-50: #f9fafb;
            --gray-100: #f3f4f6;
            --gray-200: #e5e7eb;
            --gray-300: #d1d5db;
            --shadow-sm: 0 1px 2px rgba(0, 0, 0, 0.05);
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            --shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            --radius-sm: 0.375rem;
            --radius-md: 0.5rem;
            --radius-lg: 0.75rem;
            --radius-xl: 1rem;
            --transition: all 0.2s ease-in-out;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: #ffffff;
            color: var(--text-primary);
            line-height: 1.6;
            margin: 0;
            padding: 0;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Mobile Menu Toggle */
        .mobile-menu-toggle {
            display: none;
            position: fixed;
            top: 20px;
            left: 20px;
            z-index: 1000;
            background: var(--primary);
            color: white;
            border: none;
            border-radius: var(--radius-md);
            padding: 10px;
            cursor: pointer;
            box-shadow: var(--shadow-md);
        }

        /* Sidebar */
        .sidebar {
            width: 280px;
            background: var(--white);
            padding: 24px;
            display: flex;
            flex-direction: column;
            border-right: 1px solid var(--border);
            height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            overflow-y: auto;
            z-index: 1000;
            transition: transform 0.3s ease;
        }

        .sidebar-logo {
            margin-bottom: 40px;
        }

        .sidebar-logo h1 {
            font-size: 24px;
            font-weight: 700;
            color: var(--primary);
        }

        .sidebar-logo p {
            font-size: 12px;
            color: var(--text-secondary);
            margin-top: 4px;
        }

        .sidebar-nav {
            display: flex;
            flex-direction: column;
            gap: 4px;
            flex: 1;
        }

        .nav-item {
            display: flex;
            align-items: center;
            padding: 12px 16px;
            border-radius: var(--radius-md);
            color: var(--text-secondary);
            text-decoration: none;
            transition: all 0.3s ease;
            cursor: pointer;
            position: relative;
            overflow: hidden;
            font-size: 14px;
        }

        .nav-item:hover {
            background: var(--primary-light);
            color: var(--primary);
            transform: translateX(4px);
            box-shadow: 0 2px 8px rgba(44, 95, 45, 0.15);
        }

        .nav-item.active {
            background: var(--primary);
            color: white;
            transform: translateX(4px);
            box-shadow: 0 4px 12px rgba(44, 95, 45, 0.3);
        }

        .nav-item .material-icons-outlined {
            margin-right: 12px;
            font-size: 20px;
            transition: transform 0.3s ease;
        }

        .nav-item:hover .material-icons-outlined {
            transform: scale(1.1);
        }

        .nav-item.active .material-icons-outlined {
            transform: scale(1.1);
        }

        /* Main Content */
        .main-content {
            flex: 1;
            margin-left: 280px;
            min-height: 100vh;
            background: #ffffff;
        }

        .main-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 32px 40px;
            background: linear-gradient(135deg,
                    rgba(255, 255, 255, 0.98) 0%,
                    rgba(255, 255, 255, 0.95) 25%,
                    rgba(248, 250, 252, 0.92) 50%,
                    rgba(241, 245, 249, 0.88) 100%);
            box-shadow:
                0 20px 60px rgba(0, 0, 0, 0.08),
                0 8px 24px rgba(0, 0, 0, 0.04);
            position: sticky;
            top: 0;
            z-index: 100;
            gap: 24px;
            border-bottom: 3px solid rgba(44, 95, 45, 0.1);
            backdrop-filter: blur(10px);
        }

        .main-header h1 {
            font-size: 2.2rem;
            font-weight: 800;
            color: var(--text-primary);
            text-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            letter-spacing: -1px;
            position: relative;
            z-index: 1;
        }

        .header-actions {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .icon-button {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg,
                    rgba(255, 255, 255, 0.95) 0%,
                    rgba(255, 255, 255, 0.85) 100%);
            border: 2px solid rgba(255, 255, 255, 0.3);
            color: var(--primary);
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            box-shadow:
                0 8px 24px rgba(0, 0, 0, 0.1),
                inset 0 1px 0 rgba(255, 255, 255, 0.8);
            text-decoration: none;
        }

        .icon-button:hover {
            background: linear-gradient(135deg,
                    rgba(255, 255, 255, 1) 0%,
                    rgba(248, 250, 252, 1) 100%);
            transform: translateY(-3px) scale(1.05);
            box-shadow:
                0 12px 32px rgba(44, 95, 45, 0.2),
                0 0 0 4px rgba(255, 255, 255, 0.2);
        }

        .content-area {
            padding: 32px;
            max-width: 1400px;
            margin: 0 auto;
        }

        .alert {
            padding: 16px 20px;
            border-radius: var(--radius-lg);
            margin-bottom: 24px;
            border: none;
            display: flex;
            align-items: center;
            gap: 12px;
            background: linear-gradient(135deg, rgba(44, 95, 45, 0.1) 0%, rgba(44, 95, 45, 0.05) 100%);
            color: var(--primary);
            border: 1px solid rgba(44, 95, 45, 0.2);
        }

        .alert-dismissible {
            position: relative;
        }

        .btn-close {
            background: none;
            border: none;
            font-size: 20px;
            cursor: pointer;
            padding: 4px;
            margin-left: auto;
            opacity: 0.6;
        }

        .btn-close:hover {
            opacity: 1;
        }
        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 24px;
            margin: 32px 0;
        }

        .stat-card {
            background: linear-gradient(135deg, var(--primary) 0%, #4a7c4e 100%);
            color: white;
            padding: 40px 32px;
            border-radius: 20px;
            text-align: center;
            box-shadow:
                0 10px 30px rgba(44, 95, 45, 0.2),
                0 5px 15px rgba(44, 95, 45, 0.1);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow:
                0 15px 40px rgba(44, 95, 45, 0.3),
                0 8px 20px rgba(44, 95, 45, 0.2);
        }

        .stat-card h3 {
            font-size: 3rem;
            font-weight: 800;
            margin-bottom: 12px;
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
        }

        .stat-card p {
            font-size: 1.1rem;
            opacity: 0.95;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .stat-card .material-icons-outlined {
            font-size: 3rem;
            margin-bottom: 16px;
            opacity: 0.8;
        }
        /* Profile Card */
        .profile-card {
            background: linear-gradient(135deg, #ffffff 0%, #fafbfc 100%);
            border-radius: 20px;
            padding: 32px;
            box-shadow:
                0 10px 30px rgba(0, 0, 0, 0.1),
                0 1px 8px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
            border: 1px solid rgba(44, 95, 45, 0.08);
            margin-bottom: 24px;
        }

        .profile-card:hover {
            box-shadow:
                0 15px 45px rgba(0, 0, 0, 0.12),
                0 4px 12px rgba(0, 0, 0, 0.06);
            border-color: rgba(44, 95, 45, 0.15);
        }

        .profile-card h4 {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 24px;
            letter-spacing: -0.5px;
        }

        .profile-card h5 {
            font-size: 1.2rem;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 16px;
        }

        .availability-badge {
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 14px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        .availability-badge.available { background: var(--primary-light); color: var(--primary-dark); }
        .availability-badge.busy { background: #fef2f2; color: #dc2626; }
        .availability-badge.offline { background: var(--gray-100); color: var(--text-secondary); }
        /* Review Cards */
        .review-card {
            background: linear-gradient(135deg, #ffffff 0%, #fafbfc 100%);
            border-radius: 16px;
            padding: 20px;
            margin-bottom: 16px;
            border-left: 4px solid var(--primary);
            box-shadow:
                0 4px 12px rgba(0, 0, 0, 0.05),
                0 1px 4px rgba(0, 0, 0, 0.02);
            transition: all 0.3s ease;
        }

        .review-card:hover {
            transform: translateY(-2px);
            box-shadow:
                0 8px 24px rgba(0, 0, 0, 0.08),
                0 2px 8px rgba(0, 0, 0, 0.04);
        }

        .review-card h6 {
            font-size: 1rem;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 8px;
        }

        .rating-stars {
            color: var(--warning);
            display: flex;
            gap: 2px;
            margin-bottom: 8px;
        }

        .rating-stars .material-icons-outlined {
            font-size: 16px;
        }

        .review-card p {
            color: var(--text-secondary);
            font-size: 14px;
            line-height: 1.5;
            margin: 0;
        }

        /* Quick Actions */
        .d-grid {
            display: grid;
            gap: 12px;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .mobile-menu-toggle {
                display: flex;
            }

            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.active {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
            }

            .main-header {
                padding: 20px;
                gap: 16px;
            }

            .main-header h1 {
                font-size: 1.8rem;
            }

            .content-area {
                padding: 20px;
            }

            .stats-grid {
                grid-template-columns: 1fr;
                gap: 16px;
            }

            .nav-tabs {
                flex-wrap: wrap;
            }

            .nav-tabs .nav-link {
                font-size: 13px;
                padding: 10px 16px;
            }
        }
        /* Tabs */
        .tabs-container {
            margin-bottom: 32px;
        }

        .nav-tabs {
            display: flex;
            gap: 8px;
            border-bottom: 2px solid var(--border);
            margin-bottom: 0;
        }

        .nav-tabs .nav-link {
            color: var(--text-secondary);
            border: none;
            background: transparent;
            padding: 12px 20px;
            border-radius: var(--radius-md) var(--radius-md) 0 0;
            font-weight: 500;
            transition: all 0.3s ease;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .nav-tabs .nav-link:hover {
            background: var(--bg-light);
            color: var(--primary);
        }

        .nav-tabs .nav-link.active {
            background: var(--primary);
            color: white;
            box-shadow: 0 -2px 8px rgba(44, 95, 45, 0.1);
        }

        .tab-content {
            background: transparent;
        }
        /* Forms */
        .form-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: var(--text-primary);
            font-size: 14px;
        }

        .form-control {
            width: 100%;
            padding: 12px 16px;
            border: 1px solid var(--border);
            border-radius: var(--radius-md);
            font-size: 14px;
            background: var(--white);
            color: var(--text-primary);
            transition: all 0.2s ease;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(44, 95, 45, 0.1);
        }

        .form-select {
            width: 100%;
            padding: 12px 16px;
            border: 1px solid var(--border);
            border-radius: var(--radius-md);
            font-size: 14px;
            background: var(--bg-light);
            color: var(--text-primary);
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .form-select:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(44, 95, 45, 0.1);
        }

        /* Buttons */
        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: var(--radius-md);
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            color: white;
            box-shadow: 0 4px 15px rgba(44, 95, 45, 0.2);
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, var(--primary-dark) 0%, var(--primary) 100%);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(44, 95, 45, 0.3);
        }

        .btn-outline-primary {
            background: transparent;
            color: var(--primary);
            border: 2px solid var(--primary);
        }

        .btn-outline-primary:hover {
            background: var(--primary);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(44, 95, 45, 0.2);
        }

        .btn-outline-info {
            background: transparent;
            color: var(--info);
            border: 2px solid var(--info);
        }

        .btn-outline-info:hover {
            background: var(--info);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(59, 130, 246, 0.2);
        }

        .btn-outline-success {
            background: transparent;
            color: var(--success);
            border: 2px solid var(--success);
        }

        .btn-outline-success:hover {
            background: var(--success);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(16, 185, 129, 0.2);
        }

        .btn-sm {
            padding: 8px 16px;
            font-size: 13px;
        }

        /* Tables */
        .table-responsive {
            background: var(--white);
            border-radius: var(--radius-lg);
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin: 0;
        }

        .table th {
            background: var(--bg-light);
            padding: 16px;
            text-align: left;
            font-weight: 600;
            color: var(--text-primary);
            border-bottom: 1px solid var(--border);
        }

        .table td {
            padding: 16px;
            border-bottom: 1px solid var(--border);
            color: var(--text-primary);
        }

        .table tbody tr:hover {
            background: var(--bg-light);
        }

        .badge {
            padding: 4px 12px;
            border-radius: var(--radius-sm);
            font-size: 12px;
            font-weight: 600;
            text-transform: capitalize;
        }

        .bg-success { background: var(--success) !important; color: white; }
        .bg-danger { background: var(--danger) !important; color: white; }
        .bg-secondary { background: var(--text-secondary) !important; color: white; }
    </style>
</head>
<body>
    <!-- Mobile Menu Toggle -->
    <button class="mobile-menu-toggle" id="mobileMenuToggle">
        <span class="material-icons-outlined">menu</span>
    </button>

    <!-- SIDEBAR -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-logo">
            <h1>SJDM Tours</h1>
            <p>Tour Guide Dashboard</p>
        </div>

        <nav class="sidebar-nav">
            <a class="nav-item active" href="dashboard.php">
                <span class="material-icons-outlined">dashboard</span>
                <span>Dashboard</span>
            </a>
            <a class="nav-item" href="register.php">
                <span class="material-icons-outlined">person_add</span>
                <span>Complete Profile</span>
            </a>
            <a class="nav-item" href="MyBookings.php">
                <span class="material-icons-outlined">calendar_today</span>
                <span>My Bookings</span>
            </a>
            <a class="nav-item" href="#">
                <span class="material-icons-outlined">message</span>
                <span>Messages</span>
            </a>
            <a class="nav-item" href="#">
                <span class="material-icons-outlined">analytics</span>
                <span>Analytics</span>
            </a>
            <a class="nav-item" href="#">
                <span class="material-icons-outlined">settings</span>
                <span>Settings</span>
            </a>
        </nav>
    </aside>

    <!-- MAIN CONTENT -->
    <main class="main-content">
        <header class="main-header">
            <h1>Tour Guide Dashboard</h1>
            <div class="header-actions">
                <span class="availability-badge <?php echo $profile['availability_status']; ?>">
                    <span class="material-icons-outlined" style="font-size: 16px;">circle</span>
                    <?php echo ucfirst($profile['availability_status']); ?>
                </span>
                <a href="../logout.php" class="icon-button" title="Logout">
                    <span class="material-icons-outlined">logout</span>
                </a>
            </div>
        </header>

        <div class="content-area">
            <?php if ($updateMessage): ?>
                <div class="alert alert-dismissible">
                    <span class="material-icons-outlined">info</span>
                    <?php echo htmlspecialchars($updateMessage); ?>
                    <button class="btn-close" onclick="this.parentElement.style.display='none'">
                        <span class="material-icons-outlined">close</span>
                    </button>
                </div>
            <?php endif; ?>

            <!-- Statistics Cards -->
            <div class="stats-grid">
                <div class="stat-card">
                    <span class="material-icons-outlined">star</span>
                    <h3><?php echo number_format($profile['rating'], 1); ?></h3>
                    <p>Average Rating</p>
                </div>
                <div class="stat-card">
                    <span class="material-icons-outlined">route</span>
                    <h3><?php echo $profile['total_tours']; ?></h3>
                    <p>Total Tours</p>
                </div>
                <div class="stat-card">
                    <span class="material-icons-outlined">work_history</span>
                    <h3><?php echo $profile['experience_years']; ?></h3>
                    <p>Years Experience</p>
                </div>
                <div class="stat-card">
                    <span class="material-icons-outlined">review</span>
                    <h3><?php echo count($reviews); ?></h3>
                    <p>Reviews</p>
                </div>
            </div>

            <!-- Main Content -->
            <div style="display: grid; grid-template-columns: 1fr 320px; gap: 32px;">
                <div>
                    <!-- Tabs -->
                    <div class="tabs-container">
                        <div class="nav-tabs">
                            <button class="nav-link active" id="profile-tab" onclick="showTab('profile')">
                                <span class="material-icons-outlined">person</span>
                                Profile
                            </button>
                            <button class="nav-link" id="availability-tab" onclick="showTab('availability')">
                                <span class="material-icons-outlined">calendar_month</span>
                                Availability
                            </button>
                            <button class="nav-link" id="reviews-tab" onclick="showTab('reviews')">
                                <span class="material-icons-outlined">star</span>
                                Reviews
                            </button>
                        </div>
                    </div>

                    <div class="tab-content">
                        <!-- Profile Tab -->
                        <div id="profile" class="tab-pane active">
                            <div class="profile-card">
                                <h4>Edit Profile</h4>
                                <form method="POST" action="">
                                    <input type="hidden" name="update_profile" value="1">
                                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                                        <div>
                                            <label class="form-label">License Number</label>
                                            <input type="text" class="form-control" name="license_number" 
                                                   value="<?php echo htmlspecialchars($profile['license_number']); ?>">
                                        </div>
                                        <div>
                                            <label class="form-label">Contact Number</label>
                                            <input type="tel" class="form-control" name="contact_number" 
                                                   value="<?php echo htmlspecialchars($profile['contact_number']); ?>">
                                        </div>
                                    </div>
                                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-top: 16px;">
                                        <div>
                                            <label class="form-label">Years of Experience</label>
                                            <input type="number" class="form-control" name="experience_years" 
                                                   value="<?php echo $profile['experience_years']; ?>" min="0">
                                        </div>
                                        <div>
                                            <label class="form-label">Hourly Rate (₱)</label>
                                            <input type="number" class="form-control" name="hourly_rate" 
                                                   value="<?php echo $profile['hourly_rate']; ?>" min="0" step="0.01">
                                        </div>
                                    </div>
                                    <div style="margin-top: 16px;">
                                        <label class="form-label">Specialization</label>
                                        <textarea class="form-control" name="specialization" rows="2"><?php echo htmlspecialchars($profile['specialization']); ?></textarea>
                                    </div>
                                    <div style="margin-top: 16px;">
                                        <label class="form-label">Languages</label>
                                        <input type="text" class="form-control" name="languages" 
                                               value="<?php echo htmlspecialchars($profile['languages']); ?>">
                                    </div>
                                    <div style="margin-top: 16px;">
                                        <label class="form-label">Bio</label>
                                        <textarea class="form-control" name="bio" rows="4"><?php echo htmlspecialchars($profile['bio']); ?></textarea>
                                    </div>
                                    <button type="submit" class="btn btn-primary" style="margin-top: 24px;">
                                        <span class="material-icons-outlined">save</span>
                                        Update Profile
                                    </button>
                                </form>
                            </div>
                        </div>

                        <!-- Availability Tab -->
                        <div id="availability" class="tab-pane" style="display: none;">
                            <div class="profile-card">
                                <h4>Manage Availability</h4>
                                
                                <!-- Quick Status Update -->
                                <div style="margin-bottom: 32px;">
                                    <h5>Quick Status Update</h5>
                                    <form method="POST" action="" style="display: flex; gap: 12px; align-items: center;">
                                        <input type="hidden" name="update_availability" value="1">
                                        <select name="availability_status" class="form-select" style="width: auto;">
                                            <option value="available" <?php echo $profile['availability_status'] === 'available' ? 'selected' : ''; ?>>Available</option>
                                            <option value="busy" <?php echo $profile['availability_status'] === 'busy' ? 'selected' : ''; ?>>Busy</option>
                                            <option value="offline" <?php echo $profile['availability_status'] === 'offline' ? 'selected' : ''; ?>>Offline</option>
                                        </select>
                                        <button type="submit" class="btn btn-primary btn-sm">Update Status</button>
                                    </form>
                                </div>

                                <!-- Schedule Management -->
                                <div>
                                    <h5 style="margin-bottom: 16px;">Your Schedule</h5>
                                    <?php if (empty($availability)): ?>
                                        <p style="color: var(--text-secondary);">No availability scheduled yet.</p>
                                    <?php else: ?>
                                        <div class="table-responsive">
                                            <table class="table">
                                                <thead>
                                                    <tr>
                                                        <th>Date</th>
                                                        <th>Start Time</th>
                                                        <th>End Time</th>
                                                        <th>Status</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($availability as $slot): ?>
                                                        <tr>
                                                            <td><?php echo date('M d, Y', strtotime($slot['available_date'])); ?></td>
                                                            <td><?php echo date('h:i A', strtotime($slot['start_time'])); ?></td>
                                                            <td><?php echo date('h:i A', strtotime($slot['end_time'])); ?></td>
                                                            <td>
                                                                <span class="badge bg-<?php 
                                                                    echo $slot['status'] === 'available' ? 'success' : 
                                                                         ($slot['status'] === 'booked' ? 'danger' : 'secondary'); 
                                                                ?>">
                                                                    <?php echo ucfirst($slot['status']); ?>
                                                                </span>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <!-- Reviews Tab -->
                        <div id="reviews" class="tab-pane" style="display: none;">
                            <div class="profile-card">
                                <h4>Customer Reviews</h4>
                                <?php if (empty($reviews)): ?>
                                    <p style="color: var(--text-secondary);">No reviews yet. Start giving amazing tours to get reviews!</p>
                                <?php else: ?>
                                    <?php foreach ($reviews as $review): ?>
                                        <div class="review-card">
                                            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 8px;">
                                                <div>
                                                    <h6><?php echo htmlspecialchars($review['first_name'] . ' ' . $review['last_name']); ?></h6>
                                                    <div class="rating-stars">
                                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                                            <span class="material-icons-outlined"><?php echo $i <= $review['rating'] ? 'star' : 'star_border'; ?></span>
                                                        <?php endfor; ?>
                                                    </div>
                                                </div>
                                                <small style="color: var(--text-secondary);">
                                                    <?php echo date('M d, Y', strtotime($review['created_at'])); ?>
                                                </small>
                                            </div>
                                            <p><?php echo htmlspecialchars($review['review']); ?></p>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div>
                    <div class="profile-card" style="margin-bottom: 24px;">
                        <h5>Quick Actions</h5>
                        <div class="d-grid">
                            <a href="#" class="btn btn-outline-primary">
                                <span class="material-icons-outlined">calendar_add</span>
                                Add Availability
                            </a>
                            <a href="#" class="btn btn-outline-info">
                                <span class="material-icons-outlined">trending_up</span>
                                View Statistics
                            </a>
                            <a href="#" class="btn btn-outline-success">
                                <span class="material-icons-outlined">mail</span>
                                Messages
                            </a>
                        </div>
                    </div>

                    <div class="profile-card">
                        <h5>Profile Summary</h5>
                        <div style="margin-bottom: 16px;">
                            <strong>License:</strong> <?php echo htmlspecialchars($profile['license_number']); ?>
                        </div>
                        <div style="margin-bottom: 16px;">
                            <strong>Specialization:</strong><br>
                            <small style="color: var(--text-secondary);"><?php echo htmlspecialchars($profile['specialization']); ?></small>
                        </div>
                        <div style="margin-bottom: 16px;">
                            <strong>Languages:</strong><br>
                            <small style="color: var(--text-secondary);"><?php echo htmlspecialchars($profile['languages']); ?></small>
                        </div>
                        <div>
                            <strong>Hourly Rate:</strong> ₱<?php echo number_format($profile['hourly_rate'], 2); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script>
        // Mobile menu toggle
        document.getElementById('mobileMenuToggle').addEventListener('click', function() {
            document.getElementById('sidebar').classList.toggle('active');
        });

        // Tab functionality
        function showTab(tabName) {
            // Hide all tabs
            document.querySelectorAll('.tab-pane').forEach(tab => {
                tab.style.display = 'none';
            });
            
            // Remove active class from all nav links
            document.querySelectorAll('.nav-link').forEach(link => {
                link.classList.remove('active');
            });
            
            // Show selected tab
            document.getElementById(tabName).style.display = 'block';
            
            // Add active class to clicked nav link
            document.getElementById(tabName + '-tab').classList.add('active');
        }

        // Initialize first tab
        document.addEventListener('DOMContentLoaded', function() {
            showTab('profile');
        });
    </script>
</body>
</html>
