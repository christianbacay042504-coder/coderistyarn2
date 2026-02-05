<?php
// Include database connection and authentication
require_once '../config/database.php';
require_once '../config/auth.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../log-in/log-in.php');
    exit();
}

// Get current user data
$conn = getDatabaseConnection();
if ($conn) {
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
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <!-- SIDEBAR -->
    <aside class="sidebar">
        <div class="sidebar-logo">
            <h1>SJDM Tours</h1>
            <p>Explore San Jose del Monte</p>
        </div>

        <nav class="sidebar-nav">
            <a class="nav-item" href="index.php">
                <span class="material-icons-outlined">home</span>
                <span>Home</span>
            </a>
            <a class="nav-item" href="user-guides.php">
                <span class="material-icons-outlined">people</span>
                <span>Tour Guides</span>
            </a>
            <a class="nav-item" href="book.php">
                <span class="material-icons-outlined">event</span>
                <span>Book Now</span>
            </a>
            <a class="nav-item" href="tourist-spots.php">
                <span class="material-icons-outlined">place</span>
                <span>Tourist Spots</span>
            </a>
            <a class="nav-item active" href="javascript:void(0)">
                <span class="material-icons-outlined">theater_comedy</span>
                <span>Local Culture</span>
            </a>
            <a class="nav-item" href="travel-tips.php">
                <span class="material-icons-outlined">tips_and_updates</span>
                <span>Travel Tips</span>
            </a>
        </nav>
    </aside>

    <!-- MAIN CONTENT -->
    <main class="main-content">
        <header class="main-header">
            <h1>Local Culture</h1>
            <div class="search-bar">
                <span class="material-icons-outlined">search</span>
                <input type="text" placeholder="Search cultural information...">
            </div>
            <div class="header-actions">
                <button class="icon-button">
                    <span class="material-icons-outlined">notifications_none</span>
                    <span class="notification-badge" style="display: none;">0</span>
                </button>
                <div class="profile-dropdown">
                    <button class="profile-button" id="profileButton">
                        <div class="profile-avatar"><?php echo isset($currentUser) ? substr($currentUser['name'], 0, 1) : 'U'; ?></div>
                        <span class="material-icons-outlined">expand_more</span>
                    </button>
                    <div class="dropdown-menu" id="profileMenu">
                        <div class="profile-info">
                            <div class="profile-avatar large"><?php echo isset($currentUser) ? substr($currentUser['name'], 0, 1) : 'U'; ?></div>
                            <div class="profile-details">
                                <h3><?php echo isset($currentUser) ? htmlspecialchars($currentUser['name']) : 'User Name'; ?></h3>
                                <p><?php echo isset($currentUser) ? htmlspecialchars($currentUser['email']) : 'user@example.com'; ?></p>
                            </div>
                        </div>
                        <div class="dropdown-divider"></div>
                        <a href="javascript:void(0)" class="dropdown-item" id="myAccountLink">
                            <span class="material-icons-outlined">account_circle</span>
                            <span>My Account</span>
                        </a>
                        <a href="javascript:void(0)" class="dropdown-item" id="bookingHistoryLink">
                            <span class="material-icons-outlined">history</span>
                            <span>Booking History</span>
                        </a>
                        <a href="javascript:void(0)" class="dropdown-item" id="savedToursLink">
                            <span class="material-icons-outlined">favorite_border</span>
                            <span>Saved Tours</span>
                        </a>
                        <div class="dropdown-divider"></div>
                        <a href="javascript:void(0)" class="dropdown-item" id="settingsLink">
                            <span class="material-icons-outlined">settings</span>
                            <span>Settings</span>
                        </a>
                        <a href="javascript:void(0)" class="dropdown-item" id="helpSupportLink">
                            <span class="material-icons-outlined">help_outline</span>
                            <span>Help & Support</span>
                        </a>
                        <a href="../logout.php" class="dropdown-item" id="signoutLink">
                            <span class="material-icons-outlined">logout</span>
                            <span>Sign Out</span>
                        </a>
                    </div>
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
        </div>
    </main>

    <script src="script.js"></script>
    <script>
        // Pass current user data to JavaScript
        <?php if (isset($currentUser)): ?>
        const currentUser = <?php echo json_encode($currentUser); ?>;
        localStorage.setItem('currentUser', JSON.stringify(currentUser));
        <?php endif; ?>
        
        // Profile dropdown functionality
        document.addEventListener('DOMContentLoaded', function() {
            console.log('DOM Content Loaded');
            
            const profileButton = document.getElementById('profileButton');
            const profileMenu = document.getElementById('profileMenu');
            
            console.log('Profile Button:', profileButton);
            console.log('Profile Menu:', profileMenu);
            
            if (profileButton) {
                console.log('Profile button found, adding click listener');
                profileButton.addEventListener('click', function(e) {
                    console.log('Profile button clicked!');
                    e.preventDefault();
                    e.stopPropagation();
                    
                    if (profileMenu) {
                        console.log('Toggling menu. Current classes:', profileMenu.className);
                        profileMenu.classList.toggle('active');
                        console.log('Menu after toggle. Classes:', profileMenu.className);
                    }
                });
            } else {
                console.error('Profile button not found!');
            }
            
            // Close dropdown when clicking outside
            document.addEventListener('click', function(e) {
                if (profileButton && profileMenu && 
                    !profileButton.contains(e.target) && 
                    !profileMenu.contains(e.target)) {
                    profileMenu.classList.remove('active');
                }
            });
            
            // Add event listeners for all profile menu items
            const myAccountLink = document.getElementById('myAccountLink');
            const bookingHistoryLink = document.getElementById('bookingHistoryLink');
            const savedToursLink = document.getElementById('savedToursLink');
            const settingsLink = document.getElementById('settingsLink');
            const helpSupportLink = document.getElementById('helpSupportLink');
            const signoutLink = document.getElementById('signoutLink');
            
            if (myAccountLink) {
                myAccountLink.addEventListener('click', function(e) {
                    e.preventDefault();
                    profileMenu.classList.remove('active');
                    showMyAccountModal();
                });
            }
            
            if (bookingHistoryLink) {
                bookingHistoryLink.addEventListener('click', function(e) {
                    e.preventDefault();
                    profileMenu.classList.remove('active');
                    showBookingHistoryModal();
                });
            }
            
            if (savedToursLink) {
                savedToursLink.addEventListener('click', function(e) {
                    e.preventDefault();
                    profileMenu.classList.remove('active');
                    showSavedToursModal();
                });
            }
            
            if (settingsLink) {
                settingsLink.addEventListener('click', function(e) {
                    e.preventDefault();
                    profileMenu.classList.remove('active');
                    showSettingsModal();
                });
            }
            
            if (helpSupportLink) {
                helpSupportLink.addEventListener('click', function(e) {
                    e.preventDefault();
                    profileMenu.classList.remove('active');
                    showHelpSupportModal();
                });
            }
            
            // Note: Direct logout link used, no JavaScript event listener needed for signoutLink
        });
    </script>
</body>
</html>