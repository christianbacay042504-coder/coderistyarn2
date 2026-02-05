<?php
// Include database connection and authentication
require_once '../config/database.php';
require_once '../config/auth.php';

// Temporarily bypass login for testing - remove this in production
// if (!isset($_SESSION['user_id'])) {
//     header('Location: ../log-in/log-in.php');
//     exit();
// }

// Get current user data (optional for testing)
$conn = getDatabaseConnection();
if ($conn) {
    // Temporarily set a mock user for testing
    $currentUser = [
        'name' => 'Test User',
        'email' => 'test@example.com'
    ];
    
    // Fetch tour guides from database
    $tourGuides = [];
    if ($conn) {
        $guidesStmt = $conn->prepare("SELECT * FROM tour_guides WHERE status = 'active' ORDER BY rating DESC, review_count DESC");
        if ($guidesStmt) {
            $guidesStmt->execute();
            $guidesResult = $guidesStmt->get_result();
            if ($guidesResult->num_rows > 0) {
                while ($guide = $guidesResult->fetch_assoc()) {
                    $tourGuides[] = $guide;
                }
            }
        } else {
            echo "<!-- Error preparing statement -->";
        }
    }
    
    closeDatabaseConnection($conn);
} else {
    echo "<!-- Database connection failed -->";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tour Guides - San Jose del Monte Bulacan</title>
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
            <a class="nav-item active" href="javascript:void(0)">
                <span class="material-icons-outlined">people</span>
                <span>Tour Guides</span>
            </a>
            <a class="nav-item" href="book.php">
                <span class="material-icons-outlined">event</span>
                <span>Book Now</span>
            </a>
            <a class="nav-item" href="hotel-booking.php">
                <span class="material-icons-outlined">hotel</span>
                <span>Hotels</span>
            </a>
            <a class="nav-item" href="local-culture.php">
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
            <h1>Tour Guides</h1>
            <div class="search-bar">
                <span class="material-icons-outlined">search</span>
                <input type="text" placeholder="Search guides..." id="guideSearch">
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
                        <a href="javascript:void(0)" class="dropdown-item" id="signoutLink">
                            <span class="material-icons-outlined">logout</span>
                            <span>Sign Out</span>
                        </a>
                    </div>
                </div>
            </div>
        </header>

        <div class="content-area">
            <h2 class="section-title">Meet Our Local Expert Tour Guides</h2>
            
            <!-- Filter Section -->
            <div class="filter-section">
                <div class="filter-container">
                    <div class="filter-buttons">
                        <button class="filter-btn active" data-filter="all">All Guides</button>
                        <button class="filter-btn" data-filter="mountain">Mountain Hiking</button>
                        <button class="filter-btn" data-filter="waterfall">Waterfall Tours</button>
                        <button class="filter-btn" data-filter="city">City Tours</button>
                        <button class="filter-btn" data-filter="farm">Farm & Eco-Tourism</button>
                        <button class="filter-btn" data-filter="historical">Historical Tours</button>
                        <button class="filter-btn" data-filter="general">General Tours</button>
                    </div>
                    <div class="sort-section">
                        <label>Sort by:</label>
                        <select id="sortGuides">
                            <option value="rating">Highest Rating</option>
                            <option value="experience">Most Experience</option>
                            <option value="price-low">Price: Low to High</option>
                            <option value="price-high">Price: High to Low</option>
                            <option value="reviews">Most Reviews</option>
                        </select>
                    </div>
                </div>
            </div>

            <div id="guidesList" class="guides-grid">
                <!-- Debug Info -->
                <?php 
                echo "<!-- Debug: Database connection: " . ($conn ? "Connected" : "Not connected") . " -->";
                echo "<!-- Debug: Tour guides count: " . count($tourGuides) . " -->";
                if (!empty($tourGuides)) {
                    echo "<!-- Debug: First guide: " . $tourGuides[0]['name'] . " -->";
                    echo "<!-- Debug: First guide data: " . print_r($tourGuides[0], true) . " -->";
                }
                ?>
                
                <?php if (!empty($tourGuides)): ?>
                    <?php foreach ($tourGuides as $guide): ?>
                        <div class="guide-card" data-guide-id="<?php echo $guide['id']; ?>" data-category="<?php echo $guide['category']; ?>">
                            <div class="guide-photo">
                                <img src="<?php echo !empty($guide['photo_url']) ? htmlspecialchars($guide['photo_url']) : 'https://via.placeholder.com/400x300/2c5f2d/ffffff?text=' . urlencode($guide['name']); ?>" alt="Guide <?php echo htmlspecialchars($guide['name']); ?>">
                                <button class="favorite-btn" data-guide-id="<?php echo $guide['id']; ?>">
                                    <span class="material-icons-outlined">favorite_border</span>
                                </button>
                                <?php if ($guide['verified']): ?>
                                    <div class="verified-badge">
                                        <span class="material-icons-outlined">verified</span>
                                        Verified Guide
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="guide-info">
                                <h3 class="guide-name"><?php echo htmlspecialchars($guide['name']); ?></h3>
                                <span class="guide-specialty"><?php echo htmlspecialchars($guide['specialty']); ?></span>
                                <p class="guide-description"><?php echo htmlspecialchars($guide['description']); ?></p>
                                <div class="guide-meta">
                                    <div class="meta-item">
                                        <span class="material-icons-outlined">schedule</span>
                                        <?php echo $guide['experience_years']; ?>+ years experience
                                    </div>
                                    <div class="meta-item">
                                        <span class="material-icons-outlined">translate</span>
                                        <?php echo htmlspecialchars($guide['languages']); ?>
                                    </div>
                                    <?php if (!empty($guide['group_size'])): ?>
                                    <div class="meta-item">
                                        <span class="material-icons-outlined">groups</span>
                                        <?php echo htmlspecialchars($guide['group_size']); ?>
                                    </div>
                                    <?php endif; ?>
                                </div>
                                <div class="rating-display">
                                    <?php
                                    $rating = floatval($guide['rating']);
                                    $fullStars = floor($rating);
                                    $hasHalfStar = ($rating - $fullStars) >= 0.5;
                                    
                                    for ($i = 0; $i < $fullStars; $i++) {
                                        echo '<span class="material-icons-outlined">star</span>';
                                    }
                                    if ($hasHalfStar && $fullStars < 5) {
                                        echo '<span class="material-icons-outlined">star_half</span>';
                                        $fullStars++;
                                    }
                                    for ($i = $fullStars; $i < 5; $i++) {
                                        echo '<span class="material-icons-outlined">star_outline</span>';
                                    }
                                    ?>
                                    <span class="rating-value"><?php echo number_format($rating, 1); ?></span>
                                    <span class="review-count">(<?php echo $guide['review_count']; ?> reviews)</span>
                                </div>
                                <div class="guide-footer">
                                    <button class="btn-view-profile" onclick="viewGuideProfile(<?php echo $guide['id']; ?>)">View Profile</button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="no-guides-message">
                        <p>No tour guides available at the moment. Please check back later.</p>
                        <!-- Additional debug info -->
                        <p><small>Debug: Database connection status: <?php echo $conn ? "Connected" : "Not connected"; ?></small></p>
                    </div>
                <?php endif; ?>
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
            console.log('User guides page loaded');
            
            const profileButton = document.getElementById('profileButton');
            const profileMenu = document.getElementById('profileMenu');
            
            if (profileButton) {
                profileButton.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    
                    if (profileMenu) {
                        profileMenu.classList.toggle('active');
                    }
                });
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
            
            if (signoutLink) {
                signoutLink.addEventListener('click', function(e) {
                    e.preventDefault();
                    profileMenu.classList.remove('active');
                    handleLogout();
                });
            }
        });
    </script>
</body>
</html>