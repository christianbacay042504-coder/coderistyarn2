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
    
    // Fetch featured tourist spots from database
    $featuredSpots = [];
    $spotsQuery = "SELECT name, description, image_url, rating, review_count, category FROM tourist_spots WHERE status = 'active' ORDER BY rating DESC, review_count DESC LIMIT 4";
    $spotsResult = $conn->query($spotsQuery);
    if ($spotsResult) {
        while ($spot = $spotsResult->fetch_assoc()) {
            $featuredSpots[] = $spot;
        }
    }
    
    // Fetch homepage content from database
    $homepageContent = [];
    $contentQuery = "SELECT content_type, content_key, content_value, display_order FROM homepage_content WHERE status = 'active' ORDER BY display_order";
    $contentResult = $conn->query($contentQuery);
    if ($contentResult) {
        while ($content = $contentResult->fetch_assoc()) {
            $homepageContent[$content['content_type']][$content['content_key']] = $content['content_value'];
        }
    }
    
    closeDatabaseConnection($conn);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>San Jose del Monte Bulacan - Tour Guide & Tourism</title>
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
            <a class="nav-item active" href="index.php">
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
            <h1 id="pageTitle">Home</h1>
            <div class="search-bar">
                <span class="material-icons-outlined">search</span>
                <input type="text" placeholder="Search destinations or guides...">
            </div>
            <div class="header-actions">
                <button class="icon-button">
                    <span class="material-icons-outlined">notifications_none</span>
                    <span class="notification-badge" style="display: none;">0</span>
                </button>
                
                <!-- User Profile Dropdown -->
                <div class="profile-dropdown">
                    <button class="profile-button" id="userProfileButton">
                        <div class="profile-avatar"><?php echo isset($currentUser['name']) ? substr($currentUser['name'], 0, 1) : 'U'; ?></div>
                        <span class="material-icons-outlined">expand_more</span>
                    </button>
                    <div class="dropdown-menu" id="userProfileMenu">
                        <div class="profile-info">
                            <div class="profile-avatar large"><?php echo isset($currentUser['name']) ? substr($currentUser['name'], 0, 1) : 'U'; ?></div>
                            <div class="profile-details">
                                <h3 class="user-name"><?php echo isset($currentUser['name']) ? htmlspecialchars($currentUser['name']) : 'User'; ?></h3>
                                <p class="user-email"><?php echo isset($currentUser['email']) ? htmlspecialchars($currentUser['email']) : 'user@sjdmtours.com'; ?></p>
                            </div>
                        </div>
                        <div class="dropdown-divider"></div>
                        <a href="javascript:void(0)" class="dropdown-item" id="userAccountLink">
                            <span class="material-icons-outlined">account_circle</span>
                            <span>My Account</span>
                        </a>
                        <a href="javascript:void(0)" class="dropdown-item" id="userBookingHistoryLink">
                            <span class="material-icons-outlined">history</span>
                            <span>Booking History</span>
                        </a>
                        <a href="javascript:void(0)" class="dropdown-item" id="userSavedToursLink">
                            <span class="material-icons-outlined">favorite</span>
                            <span>Saved Tours</span>
                        </a>
                        <div class="dropdown-divider"></div>
                        <a href="javascript:void(0)" class="dropdown-item" id="userSettingsLink">
                            <span class="material-icons-outlined">settings</span>
                            <span>Settings</span>
                        </a>
                        <a href="javascript:void(0)" class="dropdown-item" id="userHelpLink">
                            <span class="material-icons-outlined">help_outline</span>
                            <span>Help & Support</span>
                        </a>
                        <div class="dropdown-divider"></div>
                        <a href="logout.php" class="dropdown-item">
                            <span class="material-icons-outlined">logout</span>
                            <span>Sign Out</span>
                        </a>
                    </div>
                </div>
            </div>
        </header>

        <div class="content-area">
            <div class="hero">
                <h1><?php echo htmlspecialchars($homepageContent['hero_title']['main_title'] ?? 'Welcome to San Jose del Monte, Bulacan'); ?></h1>
                <p><?php echo htmlspecialchars($homepageContent['hero_subtitle']['main_subtitle'] ?? 'The Balcony of Metropolis - Where Nature Meets Progress'); ?></p>
                <button class="btn-hero" onclick="window.location.href='user-guides.php'">
                    <?php echo htmlspecialchars($homepageContent['hero_button_text']['main_button'] ?? 'Find Your Guide'); ?>
                </button>
            </div>

            <h2 class="section-title"><?php echo htmlspecialchars($homepageContent['section_title']['featured_destinations'] ?? 'Featured Destinations'); ?></h2>
            <div class="destinations-grid">
                <?php if (!empty($featuredSpots)): ?>
                    <?php foreach ($featuredSpots as $spot): ?>
                        <div class="destination-card">
                            <div class="destination-img">
                                <img src="<?php echo htmlspecialchars($spot['image_url'] ?? 'https://via.placeholder.com/400x300/2c5f2d/ffffff?text=' . urlencode($spot['name'])); ?>" alt="<?php echo htmlspecialchars($spot['name']); ?>">
                            </div>
                            <div class="destination-content">
                                <h3><?php echo htmlspecialchars($spot['name']); ?></h3>
                                <p><?php echo htmlspecialchars($spot['description']); ?></p>
                                <div class="destination-meta">
                                    <span class="rating">
                                        <span class="material-icons-outlined">star</span>
                                        <?php echo number_format($spot['rating'], 1); ?>
                                    </span>
                                    <span class="category"><?php echo ucfirst(htmlspecialchars($spot['category'])); ?></span>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="no-destinations">
                        <p>No featured destinations available at the moment.</p>
                    </div>
                <?php endif; ?>
            </div>

            <h2 class="section-title"><?php echo htmlspecialchars($homepageContent['section_title']['why_visit'] ?? 'Why Visit San Jose del Monte?'); ?></h2>
            <div class="stats-grid">
                <?php if (!empty($homepageContent['stat_title'])): ?>
                    <?php foreach ($homepageContent['stat_title'] as $key => $title): ?>
                        <div class="stat-card">
                            <h3><?php echo htmlspecialchars($homepageContent['stat_value'][$key] ?? '0'); ?></h3>
                            <p><?php echo htmlspecialchars($title); ?></p>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <!-- Fallback stats if database is empty -->
                    <div class="stat-card">
                        <h3>10+</h3>
                        <p>Natural Attractions</p>
                    </div>
                    <div class="stat-card">
                        <h3>30 min</h3>
                        <p>From Metro Manila</p>
                    </div>
                    <div class="stat-card">
                        <h3>Year-round</h3>
                        <p>Perfect Climate</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <script src="script.js"></script>
</body>
</html>
