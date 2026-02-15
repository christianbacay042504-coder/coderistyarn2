<?php
session_start();
// Include database connection
require_once '../config/database.php';

// Get current user data (optional - for logged in users)
$currentUser = [];
$conn = getDatabaseConnection();
if ($conn && isset($_SESSION['user_id'])) {
    $stmt = $conn->prepare("SELECT first_name, last_name, email FROM users WHERE id = ?");
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $currentUser = [
            'name' => $user['first_name'] . ' ' . $user['last_name'],
            'email' => $user['email']
        ];
    }
    closeDatabaseConnection($conn);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Local Culture - San Jose del Monte Bulacan</title>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="user-styles.css">
    <style>
        /* Full-width layout styles */
        .main-content.full-width {
            margin-left: 0;
            max-width: 100%;
        }

        .main-content.full-width .main-header {
            padding: 30px 40px;
            background: white;
            border-bottom: 1px solid var(--border);
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 40px;
        }

        .header-left {
            display: flex;
            align-items: center;
            gap: 30px;
        }

        .header-right {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .header-nav {
            display: flex;
            align-items: center;
            gap: 8px;
            background: var(--gray-50);
            padding: 4px;
            border-radius: 12px;
        }

        .nav-link {
            display: flex;
            align-items: center;
            gap: 6px;
            padding: 8px 16px;
            text-decoration: none;
            color: var(--text-secondary);
            font-weight: 500;
            font-size: 14px;
            border-radius: 8px;
            transition: all 0.2s ease;
        }

        .nav-link:hover {
            background: white;
            color: var(--primary);
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .nav-link.active {
            background: var(--primary);
            color: white;
        }

        .nav-link .material-icons-outlined {
            font-size: 18px;
        }

        .btn-signin {
            background: #2563eb;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 500;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .btn-signin:hover {
            background: #1d4ed8;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
        }

        /* ===== USER PROFILE DROPDOWN ===== */
        .user-profile-dropdown {
            position: relative;
            display: inline-block;
            z-index: 1000;
        }

        .profile-trigger {
            display: flex;
            align-items: center;
            gap: 8px;
            background: none;
            border: 1px solid rgba(251, 255, 253, 1);
            cursor: pointer;
            color: #333;
            font-weight: 600;
            padding: 6px 12px;
            border-radius: 20px;
            transition: background 0.2s;
            box-shadow: 5px 10px 10px rgba(0, 0, 0, 0.1);
        }

        .profile-trigger:hover {
            background: #f0f0f0;
        }

        .profile-avatar,
        .profile-avatar-large {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: #2c5f2d;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 14px;
            flex-shrink: 0;
        }

        .profile-avatar-large {
            width: 56px;
            height: 56px;
            font-size: 20px;
            margin: 0 auto 12px;
        }

        .profile-name {
            display: none;
        }

        .dropdown-menu {
            position: absolute;
            top: 100%;
            right: 0;
            margin-top: 8px;
            width: 240px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            overflow: hidden;
            z-index: 1001;
            opacity: 0;
            visibility: hidden;
            transform: translateY(-10px);
            transition: all 0.2s ease;
        }

        .dropdown-menu.show {
            opacity: 1 !important;
            visibility: visible !important;
            transform: translateY(0) !important;
        }

        .dropdown-header {
            padding: 16px;
            background: #f9f9f9;
            text-align: center;
            border-bottom: 1px solid #eee;
        }

        .dropdown-header h4 {
            margin: 8px 0 4px;
            font-size: 16px;
            color: #333;
        }

        .dropdown-header p {
            font-size: 13px;
            color: #777;
            margin: 0;
        }

        .dropdown-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 16px;
            text-decoration: none;
            color: #444;
            transition: background 0.2s;
        }

        .dropdown-item:hover {
            background: #f5f5f5;
        }

        .dropdown-item .material-icons-outlined {
            font-size: 20px;
            color: #555;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .profile-name {
                display: inline-block;
                font-size: 14px;
            }

            .dropdown-menu {
                width: 280px;
            }
        }

        .main-content.full-width .content-area {
            padding: 40px;
            max-width: 1400px;
            margin: 0 auto;
        }

        @media (max-width: 768px) {
            .main-content.full-width .main-header {
                padding: 20px;
                flex-direction: column;
                gap: 20px;
                align-items: stretch;
            }
            
            .header-left {
                flex-direction: column;
                gap: 15px;
                align-items: stretch;
            }
            
            .header-right {
                justify-content: center;
            }
            
            .header-nav {
                flex-wrap: wrap;
                justify-content: center;
                gap: 4px;
                padding: 6px;
            }
            
            .nav-link {
                padding: 6px 12px;
                font-size: 12px;
                gap: 4px;
            }
            
            .nav-link .material-icons-outlined {
                font-size: 16px;
            }
            
            .main-content.full-width .content-area {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <!-- MAIN CONTENT -->
    <main class="main-content full-width">
        <header class="main-header">
            <div class="header-left">
                <h1>Local Culture</h1>
                
            </div>
            <div class="header-right">
                <nav class="header-nav">
                    
                </nav>
            </div>
            <div class="header-right">
                <nav class="header-nav">
                    <a href="user-index.php" class="nav-link">
                        <span class="material-icons-outlined">dashboard</span>
                        <span>Dashboard</span>
                    </a>
                    <a href="user-guides-page.php" class="nav-link">
                        <span class="material-icons-outlined">people</span>
                        <span>Tour Guides</span>
                    </a>
                    <a href="user-book.php" class="nav-link">
                        <span class="material-icons-outlined">event</span>
                        <span>Book Now</span>
                    </a>
                    <a href="user-booking-history.php" class="nav-link">
                        <span class="material-icons-outlined">history</span>
                        <span>Booking History</span>
                    </a>
                    <a href="user-tourist-spots.php" class="nav-link">
                        <span class="material-icons-outlined">place</span>
                        <span>Tourist Spots</span>
                    </a>
                    <a href="user-local-culture.php" class="nav-link">
                        <span class="material-icons-outlined">theater_comedy</span>
                        <span>Local Culture</span>
                    </a>
                    <a href="user-travel-tips.php" class="nav-link">
                        <span class="material-icons-outlined">tips_and_updates</span>
                        <span>Travel Tips</span>
                    </a>
                </nav>
                <div class="header-actions">
                    <?php if (isset($currentUser) && !empty($currentUser)): ?>
                        <!-- Profile Dropdown for Logged In Users -->
                        <div class="user-profile-dropdown">
                            <button class="profile-trigger">
                                <div class="profile-avatar"><?php echo isset($currentUser['name']) ? strtoupper(substr($currentUser['name'], 0, 1)) : 'U'; ?></div>
                                <span class="profile-name"><?php echo htmlspecialchars($currentUser['name']); ?></span>
                                <span class="material-icons-outlined">arrow_drop_down</span>
                            </button>
                            <div class="dropdown-menu">
                                <div class="dropdown-header">
                                    <div class="profile-avatar-large"><?php echo isset($currentUser['name']) ? strtoupper(substr($currentUser['name'], 0, 1)) : 'U'; ?></div>
                                    <h4><?php echo htmlspecialchars($currentUser['name']); ?></h4>
                                    <p><?php echo htmlspecialchars($currentUser['email']); ?></p>
                                </div>
                                <a href="#" class="dropdown-item">
                                    <span class="material-icons-outlined">person</span>
                                    My Profile
                                </a>
                                <a href="user-booking-history.php" class="dropdown-item">
                                    <span class="material-icons-outlined">event</span>
                                    My Bookings
                                </a>
                                <a href="#" class="dropdown-item">
                                    <span class="material-icons-outlined">settings</span>
                                    Settings
                                </a>
                                <a href="user-logout.php" class="dropdown-item">
                                    <span class="material-icons-outlined">logout</span>
                                    Logout
                                </a>
                            </div>
                        </div>
                    <?php else: ?>
                        <!-- Sign In Button for Guests -->
                        <button class="btn-signin" onclick="window.location.href='../log-in.php'">Sign in/register</button>
                    <?php endif; ?>
                </div>
            </div>
        </header>

        <div class="content-area">
            <h2 class="section-title">SJDM Local Culture & Heritage</h2>
            <div class="info-cards">
                <?php
                // Fetch local culture data from database
                $conn = getDatabaseConnection();
                if ($conn) {
                    $query = "SELECT * FROM local_culture WHERE is_active = 'yes' ORDER BY display_order";
                    $result = $conn->query($query);
                    
                    if ($result && $result->num_rows > 0) {
                        while ($culture = $result->fetch_assoc()) {
                            echo '<div class="info-card">';
                            echo '<h3>' . htmlspecialchars($culture['icon']) . ' ' . htmlspecialchars($culture['title']) . '</h3>';
                            
                            // Convert description from newlines to list items
                            $descriptionLines = explode("\n", $culture['description']);
                            echo '<ul>';
                            foreach ($descriptionLines as $line) {
                                $trimmedLine = trim($line);
                                if (!empty($trimmedLine)) {
                                    echo '<li>' . htmlspecialchars($trimmedLine) . '</li>';
                                }
                            }
                            echo '</ul>';
                            echo '</div>';
                        }
                    } else {
                        echo '<div class="no-results" style="grid-column: 1 / -1; text-align: center; padding: 60px 20px;">';
                        echo '<span class="material-icons-outlined" style="font-size: 48px; color: #9ca3af;">theater_comedy</span>';
                        echo '<h3 style="color: #6b7280; margin-top: 16px;">No cultural information found</h3>';
                        echo '<p style="color: #9ca3af;">Please check back later for cultural content.</p>';
                        echo '</div>';
                    }
                    closeDatabaseConnection($conn);
                } else {
                    echo '<div class="error-message" style="grid-column: 1 / -1; text-align: center; padding: 60px 20px;">';
                    echo '<span class="material-icons-outlined" style="font-size: 48px; color: #ef4444;">error</span>';
                    echo '<h3 style="color: #ef4444; margin-top: 16px;">Database Connection Error</h3>';
                    echo '<p style="color: #6b7280;">Unable to load cultural information. Please try again later.</p>';
                    echo '</div>';
                }
                ?>
            </div>
        </main>

        <script src="script.js"></script>
        <script>
            // ========== USER PROFILE DROPDOWN ==========
            function initUserProfileDropdown() {
                const profileDropdown = document.querySelector('.user-profile-dropdown');
                const profileTrigger = document.querySelector('.profile-trigger');
                const dropdownMenu = document.querySelector('.dropdown-menu');
                const logoutLink = document.querySelector('[href="../log-in/logout.php"]');

                if (!profileDropdown || !profileTrigger || !dropdownMenu) {
                    console.log('Profile dropdown elements not found');
                    return;
                }

                // Toggle dropdown on click
                profileTrigger.addEventListener('click', function (e) {
                    e.preventDefault();
                    e.stopPropagation();
                    dropdownMenu.classList.toggle('show');
                });

                // Close dropdown when clicking outside
                document.addEventListener('click', function (e) {
                    if (!profileDropdown.contains(e.target)) {
                        dropdownMenu.classList.remove('show');
                    }
                });

                // Handle logout with confirmation
                if (logoutLink) {
                    logoutLink.addEventListener('click', function (e) {
                        e.preventDefault();
                        e.stopPropagation();
                        if (confirm('Are you sure you want to logout?')) {
                            window.location.href = '../log-in/logout.php';
                        }
                    });
                }
            }

            // Initialize on page load
            document.addEventListener('DOMContentLoaded', function () {
                initUserProfileDropdown();
            });
            
            // Pass current user data to JavaScript
            <?php if (isset($currentUser)): ?>
            const currentUser = <?php echo json_encode($currentUser); ?>;
            localStorage.setItem('currentUser', JSON.stringify(currentUser));
            <?php endif; ?>
        </script>
    </body>
</html>