<?php
// Include database connection and authentication
require_once '../config/database.php';
require_once '../config/auth.php';

// Get current user data (optional for testing)
$conn = getDatabaseConnection();
$tourGuides = []; // Initialize tour guides array
$currentUser = []; // Initialize current user array

if ($conn) {
    // Temporarily set a mock user for testing
    $currentUser = [
        'name' => 'Test User',
        'email' => 'test@example.com'
    ];
    
    // Fetch tour guides from database
    if ($conn) {
        $guidesStmt = $conn->prepare("SELECT * FROM tour_guides WHERE status = 'active' ORDER BY name ASC");
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
                            <option value="experience">Most Experience</option>
                            <option value="price-low">Price: Low to High</option>
                            <option value="price-high">Price: High to Low</option>
                        </select>
                    </div>
                </div>
            </div>

            <div id="guidesList" class="guides-grid">
                <div class="guide-card" data-guide-id="1" data-category="general">
                    <div class="guide-info">
                        <h3 class="guide-name">Alex Rodriguez</h3>
                        <span class="guide-specialty">Cultural & Heritage Tours</span>
                        <p class="guide-description">Passionate about sharing the rich history and vibrant culture of San Jose del Monte. Let me take you on a journey through our city's most treasured landmarks and hidden gems.</p>
                        <div class="guide-meta">
                            <div class="meta-item">
                                <span class="material-icons-outlined">schedule</span>
                                8+ years experience
                            </div>
                            <div class="meta-item">
                                <span class="material-icons-outlined">translate</span>
                                English, Tagalog
                            </div>
                            <div class="meta-item">
                                <span class="material-icons-outlined">groups</span>
                                Up to 15 guests
                            </div>
                        </div>
                        <div class="guide-footer">
                            <button class="btn-view-profile" onclick="openGuideModal(1)">View Profile</button>
                        </div>
                    </div>
                </div>

                <div class="guide-card" data-guide-id="2" data-category="mountain">
                    <div class="guide-info">
                        <h3 class="guide-name">Maria Santos</h3>
                        <span class="guide-specialty">Mountain Hiking Adventures</span>
                        <p class="guide-description">Experienced mountaineer specializing in guided treks through Bulacan's scenic mountain trails. Safety-focused with extensive knowledge of local flora and fauna.</p>
                        <div class="guide-meta">
                            <div class="meta-item">
                                <span class="material-icons-outlined">schedule</span>
                                6+ years experience
                            </div>
                            <div class="meta-item">
                                <span class="material-icons-outlined">translate</span>
                                English, Tagalog, Basic Japanese
                            </div>
                            <div class="meta-item">
                                <span class="material-icons-outlined">groups</span>
                                Up to 8 guests
                            </div>
                        </div>
                        <div class="guide-footer">
                            <button class="btn-view-profile" onclick="openGuideModal(2)">View Profile</button>
                        </div>
                    </div>
                </div>

                <div class="guide-card" data-guide-id="3" data-category="waterfall">
                    <div class="guide-info">
                        <h3 class="guide-name">Carlos Mendoza</h3>
                        <span class="guide-specialty">Waterfall Tours & Swimming</span>
                        <p class="guide-description">Local expert on San Jose del Monte's hidden waterfalls. Certified lifeguard with deep knowledge of the best swimming spots and seasonal conditions.</p>
                        <div class="guide-meta">
                            <div class="meta-item">
                                <span class="material-icons-outlined">schedule</span>
                                5+ years experience
                            </div>
                            <div class="meta-item">
                                <span class="material-icons-outlined">translate</span>
                                English, Tagalog
                            </div>
                            <div class="meta-item">
                                <span class="material-icons-outlined">groups</span>
                                Up to 12 guests
                            </div>
                        </div>
                        <div class="guide-footer">
                            <button class="btn-view-profile" onclick="openGuideModal(3)">View Profile</button>
                        </div>
                    </div>
                </div>

                <div class="guide-card" data-guide-id="4" data-category="city">
                    <div class="guide-info">
                        <h3 class="guide-name">Elena Reyes</h3>
                        <span class="guide-specialty">Urban City Tours</span>
                        <p class="guide-description">City explorer with insider knowledge of San Jose del Monte's urban landscape. Specializes in architecture, local markets, and contemporary culture.</p>
                        <div class="guide-meta">
                            <div class="meta-item">
                                <span class="material-icons-outlined">schedule</span>
                                4+ years experience
                            </div>
                            <div class="meta-item">
                                <span class="material-icons-outlined">translate</span>
                                English, Tagalog, Mandarin
                            </div>
                            <div class="meta-item">
                                <span class="material-icons-outlined">groups</span>
                                Up to 20 guests
                            </div>
                        </div>
                        <div class="guide-footer">
                            <button class="btn-view-profile" onclick="openGuideModal(4)">View Profile</button>
                        </div>
                    </div>
                </div>

                <div class="guide-card" data-guide-id="5" data-category="farm">
                    <div class="guide-info">
                        <h3 class="guide-name">Roberto Cruz</h3>
                        <span class="guide-specialty">Farm & Eco-Tourism</span>
                        <p class="guide-description">Agricultural specialist offering authentic farm experiences. Learn about sustainable farming practices and enjoy fresh farm-to-table experiences.</p>
                        <div class="guide-meta">
                            <div class="meta-item">
                                <span class="material-icons-outlined">schedule</span>
                                10+ years experience
                            </div>
                            <div class="meta-item">
                                <span class="material-icons-outlined">translate</span>
                                English, Tagalog
                            </div>
                            <div class="meta-item">
                                <span class="material-icons-outlined">groups</span>
                                Up to 25 guests
                            </div>
                        </div>
                        <div class="guide-footer">
                            <button class="btn-view-profile" onclick="openGuideModal(5)">View Profile</button>
                        </div>
                    </div>
                </div>

                <div class="guide-card" data-guide-id="6" data-category="historical">
                    <div class="guide-info">
                        <h3 class="guide-name">Isabella Fernandez</h3>
                        <span class="guide-specialty">Historical Tours & Storytelling</span>
                        <p class="guide-description">History buff and storyteller bringing San Jose del Monte's past to life. Expert on colonial history, revolutionary sites, and local legends.</p>
                        <div class="guide-meta">
                            <div class="meta-item">
                                <span class="material-icons-outlined">schedule</span>
                                7+ years experience
                            </div>
                            <div class="meta-item">
                                <span class="material-icons-outlined">translate</span>
                                English, Tagalog, Spanish
                            </div>
                            <div class="meta-item">
                                <span class="material-icons-outlined">groups</span>
                                Up to 18 guests
                            </div>
                        </div>
                        <div class="guide-footer">
                            <button class="btn-view-profile" onclick="openGuideModal(6)">View Profile</button>
                        </div>
                    </div>
                </div>

                <!-- Guide Profile Modal -->
                <div class="modal-overlay" id="modal-guide-1">
                    <div class="modal-content guide-profile-modal">
                        <div class="modal-header">
                            <div class="modal-title">
                                <span class="material-icons-outlined modal-icon">person</span>
                                <h2>Guide Profile</h2>
                            </div>
                            <button class="close-modal" onclick="closeModal('modal-guide-1')">
                                <span class="material-icons-outlined">close</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="guide-profile-content">
                                <div class="guide-profile-header">
                                    <div class="guide-profile-info">
                                        <div class="guide-name-section">
                                            <h3>Alex Rodriguez</h3>
                                            <div class="verified-ribbon">
                                                <span class="material-icons-outlined">verified_user</span>
                                                <span>Trusted Professional</span>
                                            </div>
                                        </div>
                                        <p class="guide-specialty">Cultural & Heritage Tours</p>
                                        <div class="guide-category-badge">
                                            <span class="material-icons-outlined">category</span>
                                            General Tours
                                        </div>
                                    </div>
                                </div>

                                <div class="guide-description-section">
                                    <h4><span class="material-icons-outlined">info</span> About</h4>
                                    <p>Passionate about sharing the rich history and vibrant culture of San Jose del Monte. Let me take you on a journey through our city's most treasured landmarks and hidden gems. I specialize in creating immersive cultural experiences that connect visitors with the authentic spirit of our community.</p>
                                </div>

                                <div class="guide-details-grid">
                                    <div class="detail-item">
                                        <span class="material-icons-outlined">wc</span>
                                        <div>
                                            <strong>Gender</strong>
                                            <p>Male</p>
                                        </div>
                                    </div>
                                    <div class="detail-item">
                                        <span class="material-icons-outlined">translate</span>
                                        <div>
                                            <strong>Languages</strong>
                                            <p>English, Tagalog</p>
                                        </div>
                                    </div>
                                    <div class="detail-item">
                                        <span class="material-icons-outlined">groups</span>
                                        <div>
                                            <strong>Group Size</strong>
                                            <p>Up to 15 guests</p>
                                        </div>
                                    </div>
                                    <div class="detail-item">
                                        <span class="material-icons-outlined">place</span>
                                        <div>
                                            <strong>Location</strong>
                                            <p>San Jose del Monte, Bulacan</p>
                                        </div>
                                    </div>
                                    <div class="detail-item">
                                        <span class="material-icons-outlined">email</span>
                                        <div>
                                            <strong>Email</strong>
                                            <p>alex@sjdmtours.com</p>
                                        </div>
                                    </div>
                                    <div class="detail-item">
                                        <span class="material-icons-outlined">phone</span>
                                        <div>
                                            <strong>Phone</strong>
                                            <p>+63 912 345 6789</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="guide-booking-section">
                                    <h4><span class="material-icons-outlined">calendar_today</span> Booking Information</h4>
                                    <p>To book this guide and get detailed tour information, please click the button below.</p>
                                    <div class="booking-actions">
                                        <button class="btn-primary" onclick="bookGuide(1)">
                                            <span class="material-icons-outlined">calendar_today</span>
                                            Book This Guide
                                        </button>
                                        <button class="btn-secondary" onclick="closeModal('modal-guide-1')">
                                            Close
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Maria Santos Modal -->
                <div class="modal-overlay" id="modal-guide-2">
                    <div class="modal-content guide-profile-modal">
                        <div class="modal-header">
                            <div class="modal-title">
                                <span class="material-icons-outlined modal-icon">person</span>
                                <h2>Guide Profile</h2>
                            </div>
                            <button class="close-modal" onclick="closeModal('modal-guide-2')">
                                <span class="material-icons-outlined">close</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="guide-profile-content">
                                <div class="guide-profile-header">
                                    <div class="guide-profile-info">
                                        <div class="guide-name-section">
                                            <h3>Maria Santos</h3>
                                            <div class="verified-ribbon">
                                                <span class="material-icons-outlined">verified_user</span>
                                                <span>Trusted Professional</span>
                                            </div>
                                        </div>
                                        <p class="guide-specialty">Mountain Hiking Adventures</p>
                                        <div class="guide-category-badge">
                                            <span class="material-icons-outlined">category</span>
                                            Mountain Hiking
                                        </div>
                                    </div>
                                </div>

                                <div class="guide-description-section">
                                    <h4><span class="material-icons-outlined">info</span> About</h4>
                                    <p>Experienced mountaineer specializing in guided treks through Bulacan's scenic mountain trails. Safety-focused with extensive knowledge of local flora and fauna. I'll ensure you have an unforgettable adventure while staying safe and learning about our mountain ecosystem.</p>
                                </div>

                                <div class="guide-details-grid">
                                    <div class="detail-item">
                                        <span class="material-icons-outlined">wc</span>
                                        <div>
                                            <strong>Gender</strong>
                                            <p>Female</p>
                                        </div>
                                    </div>
                                    <div class="detail-item">
                                        <span class="material-icons-outlined">translate</span>
                                        <div>
                                            <strong>Languages</strong>
                                            <p>English, Tagalog, Basic Japanese</p>
                                        </div>
                                    </div>
                                    <div class="detail-item">
                                        <span class="material-icons-outlined">groups</span>
                                        <div>
                                            <strong>Group Size</strong>
                                            <p>Up to 8 guests</p>
                                        </div>
                                    </div>
                                    <div class="detail-item">
                                        <span class="material-icons-outlined">place</span>
                                        <div>
                                            <strong>Location</strong>
                                            <p>San Jose del Monte, Bulacan</p>
                                        </div>
                                    </div>
                                    <div class="detail-item">
                                        <span class="material-icons-outlined">email</span>
                                        <div>
                                            <strong>Email</strong>
                                            <p>maria@sjdmtours.com</p>
                                        </div>
                                    </div>
                                    <div class="detail-item">
                                        <span class="material-icons-outlined">phone</span>
                                        <div>
                                            <strong>Phone</strong>
                                            <p>+63 923 456 7890</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="guide-booking-section">
                                    <h4><span class="material-icons-outlined">calendar_today</span> Booking Information</h4>
                                    <p>To book this guide and get detailed tour information, please click the button below.</p>
                                    <div class="booking-actions">
                                        <button class="btn-primary" onclick="bookGuide(2)">
                                            <span class="material-icons-outlined">calendar_today</span>
                                            Book This Guide
                                        </button>
                                        <button class="btn-secondary" onclick="closeModal('modal-guide-2')">
                                            Close
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Carlos Mendoza Modal -->
                <div class="modal-overlay" id="modal-guide-3">
                    <div class="modal-content guide-profile-modal">
                        <div class="modal-header">
                            <div class="modal-title">
                                <span class="material-icons-outlined modal-icon">person</span>
                                <h2>Guide Profile</h2>
                            </div>
                            <button class="close-modal" onclick="closeModal('modal-guide-3')">
                                <span class="material-icons-outlined">close</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="guide-profile-content">
                                <div class="guide-profile-header">
                                    <div class="guide-profile-info">
                                        <div class="guide-name-section">
                                            <h3>Carlos Mendoza</h3>
                                            <div class="verified-ribbon">
                                                <span class="material-icons-outlined">verified_user</span>
                                                <span>Trusted Professional</span>
                                            </div>
                                        </div>
                                        <p class="guide-specialty">Waterfall Tours & Swimming</p>
                                        <div class="guide-category-badge">
                                            <span class="material-icons-outlined">category</span>
                                            Waterfall Tours
                                        </div>
                                    </div>
                                </div>

                                <div class="guide-description-section">
                                    <h4><span class="material-icons-outlined">info</span> About</h4>
                                    <p>Local expert on San Jose del Monte's hidden waterfalls. Certified lifeguard with deep knowledge of the best swimming spots and seasonal conditions. Let me guide you to the most beautiful waterfalls while ensuring your safety and comfort.</p>
                                </div>

                                <div class="guide-details-grid">
                                    <div class="detail-item">
                                        <span class="material-icons-outlined">wc</span>
                                        <div>
                                            <strong>Gender</strong>
                                            <p>Male</p>
                                        </div>
                                    </div>
                                    <div class="detail-item">
                                        <span class="material-icons-outlined">translate</span>
                                        <div>
                                            <strong>Languages</strong>
                                            <p>English, Tagalog</p>
                                        </div>
                                    </div>
                                    <div class="detail-item">
                                        <span class="material-icons-outlined">groups</span>
                                        <div>
                                            <strong>Group Size</strong>
                                            <p>Up to 12 guests</p>
                                        </div>
                                    </div>
                                    <div class="detail-item">
                                        <span class="material-icons-outlined">place</span>
                                        <div>
                                            <strong>Location</strong>
                                            <p>San Jose del Monte, Bulacan</p>
                                        </div>
                                    </div>
                                    <div class="detail-item">
                                        <span class="material-icons-outlined">email</span>
                                        <div>
                                            <strong>Email</strong>
                                            <p>carlos@sjdmtours.com</p>
                                        </div>
                                    </div>
                                    <div class="detail-item">
                                        <span class="material-icons-outlined">phone</span>
                                        <div>
                                            <strong>Phone</strong>
                                            <p>+63 934 567 8901</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="guide-booking-section">
                                    <h4><span class="material-icons-outlined">calendar_today</span> Booking Information</h4>
                                    <p>To book this guide and get detailed tour information, please click the button below.</p>
                                    <div class="booking-actions">
                                        <button class="btn-primary" onclick="bookGuide(3)">
                                            <span class="material-icons-outlined">calendar_today</span>
                                            Book This Guide
                                        </button>
                                        <button class="btn-secondary" onclick="closeModal('modal-guide-3')">
                                            Close
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Elena Reyes Modal -->
                <div class="modal-overlay" id="modal-guide-4">
                    <div class="modal-content guide-profile-modal">
                        <div class="modal-header">
                            <div class="modal-title">
                                <span class="material-icons-outlined modal-icon">person</span>
                                <h2>Guide Profile</h2>
                            </div>
                            <button class="close-modal" onclick="closeModal('modal-guide-4')">
                                <span class="material-icons-outlined">close</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="guide-profile-content">
                                <div class="guide-profile-header">
                                    <div class="guide-profile-info">
                                        <div class="guide-name-section">
                                            <h3>Elena Reyes</h3>
                                            <div class="verified-ribbon">
                                                <span class="material-icons-outlined">verified_user</span>
                                                <span>Trusted Professional</span>
                                            </div>
                                        </div>
                                        <p class="guide-specialty">Urban City Tours</p>
                                        <div class="guide-category-badge">
                                            <span class="material-icons-outlined">category</span>
                                            City Tours
                                        </div>
                                    </div>
                                </div>

                                <div class="guide-description-section">
                                    <h4><span class="material-icons-outlined">info</span> About</h4>
                                    <p>City explorer with insider knowledge of San Jose del Monte's urban landscape. Specializes in architecture, local markets, and contemporary culture. Let me show you the modern face of our city while sharing stories about its development and hidden urban gems.</p>
                                </div>

                                <div class="guide-details-grid">
                                    <div class="detail-item">
                                        <span class="material-icons-outlined">wc</span>
                                        <div>
                                            <strong>Gender</strong>
                                            <p>Female</p>
                                        </div>
                                    </div>
                                    <div class="detail-item">
                                        <span class="material-icons-outlined">translate</span>
                                        <div>
                                            <strong>Languages</strong>
                                            <p>English, Tagalog, Mandarin</p>
                                        </div>
                                    </div>
                                    <div class="detail-item">
                                        <span class="material-icons-outlined">groups</span>
                                        <div>
                                            <strong>Group Size</strong>
                                            <p>Up to 20 guests</p>
                                        </div>
                                    </div>
                                    <div class="detail-item">
                                        <span class="material-icons-outlined">place</span>
                                        <div>
                                            <strong>Location</strong>
                                            <p>San Jose del Monte, Bulacan</p>
                                        </div>
                                    </div>
                                    <div class="detail-item">
                                        <span class="material-icons-outlined">email</span>
                                        <div>
                                            <strong>Email</strong>
                                            <p>elena@sjdmtours.com</p>
                                        </div>
                                    </div>
                                    <div class="detail-item">
                                        <span class="material-icons-outlined">phone</span>
                                        <div>
                                            <strong>Phone</strong>
                                            <p>+63 945 678 9012</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="guide-booking-section">
                                    <h4><span class="material-icons-outlined">calendar_today</span> Booking Information</h4>
                                    <p>To book this guide and get detailed tour information, please click the button below.</p>
                                    <div class="booking-actions">
                                        <button class="btn-primary" onclick="bookGuide(4)">
                                            <span class="material-icons-outlined">calendar_today</span>
                                            Book This Guide
                                        </button>
                                        <button class="btn-secondary" onclick="closeModal('modal-guide-4')">
                                            Close
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Roberto Cruz Modal -->
                <div class="modal-overlay" id="modal-guide-5">
                    <div class="modal-content guide-profile-modal">
                        <div class="modal-header">
                            <div class="modal-title">
                                <span class="material-icons-outlined modal-icon">person</span>
                                <h2>Guide Profile</h2>
                            </div>
                            <button class="close-modal" onclick="closeModal('modal-guide-5')">
                                <span class="material-icons-outlined">close</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="guide-profile-content">
                                <div class="guide-profile-header">
                                    <div class="guide-profile-info">
                                        <div class="guide-name-section">
                                            <h3>Roberto Cruz</h3>
                                            <div class="verified-ribbon">
                                                <span class="material-icons-outlined">verified_user</span>
                                                <span>Trusted Professional</span>
                                            </div>
                                        </div>
                                        <p class="guide-specialty">Farm & Eco-Tourism</p>
                                        <div class="guide-category-badge">
                                            <span class="material-icons-outlined">category</span>
                                            Farm & Eco-Tourism
                                        </div>
                                    </div>
                                </div>

                                <div class="guide-description-section">
                                    <h4><span class="material-icons-outlined">info</span> About</h4>
                                    <p>Agricultural specialist offering authentic farm experiences. Learn about sustainable farming practices and enjoy fresh farm-to-table experiences. I'll introduce you to the agricultural heritage of San Jose del Monte and show you how modern farming meets traditional wisdom.</p>
                                </div>

                                <div class="guide-details-grid">
                                    <div class="detail-item">
                                        <span class="material-icons-outlined">wc</span>
                                        <div>
                                            <strong>Gender</strong>
                                            <p>Male</p>
                                        </div>
                                    </div>
                                    <div class="detail-item">
                                        <span class="material-icons-outlined">translate</span>
                                        <div>
                                            <strong>Languages</strong>
                                            <p>English, Tagalog</p>
                                        </div>
                                    </div>
                                    <div class="detail-item">
                                        <span class="material-icons-outlined">groups</span>
                                        <div>
                                            <strong>Group Size</strong>
                                            <p>Up to 25 guests</p>
                                        </div>
                                    </div>
                                    <div class="detail-item">
                                        <span class="material-icons-outlined">place</span>
                                        <div>
                                            <strong>Location</strong>
                                            <p>San Jose del Monte, Bulacan</p>
                                        </div>
                                    </div>
                                    <div class="detail-item">
                                        <span class="material-icons-outlined">email</span>
                                        <div>
                                            <strong>Email</strong>
                                            <p>roberto@sjdmtours.com</p>
                                        </div>
                                    </div>
                                    <div class="detail-item">
                                        <span class="material-icons-outlined">phone</span>
                                        <div>
                                            <strong>Phone</strong>
                                            <p>+63 956 789 0123</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="guide-booking-section">
                                    <h4><span class="material-icons-outlined">calendar_today</span> Booking Information</h4>
                                    <p>To book this guide and get detailed tour information, please click the button below.</p>
                                    <div class="booking-actions">
                                        <button class="btn-primary" onclick="bookGuide(5)">
                                            <span class="material-icons-outlined">calendar_today</span>
                                            Book This Guide
                                        </button>
                                        <button class="btn-secondary" onclick="closeModal('modal-guide-5')">
                                            Close
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Isabella Fernandez Modal -->
                <div class="modal-overlay" id="modal-guide-6">
                    <div class="modal-content guide-profile-modal">
                        <div class="modal-header">
                            <div class="modal-title">
                                <span class="material-icons-outlined modal-icon">person</span>
                                <h2>Guide Profile</h2>
                            </div>
                            <button class="close-modal" onclick="closeModal('modal-guide-6')">
                                <span class="material-icons-outlined">close</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="guide-profile-content">
                                <div class="guide-profile-header">
                                    <div class="guide-profile-info">
                                        <div class="guide-name-section">
                                            <h3>Isabella Fernandez</h3>
                                            <div class="verified-ribbon">
                                                <span class="material-icons-outlined">verified_user</span>
                                                <span>Trusted Professional</span>
                                            </div>
                                        </div>
                                        <p class="guide-specialty">Historical Tours & Storytelling</p>
                                        <div class="guide-category-badge">
                                            <span class="material-icons-outlined">category</span>
                                            Historical Tours
                                        </div>
                                    </div>
                                </div>

                                <div class="guide-description-section">
                                    <h4><span class="material-icons-outlined">info</span> About</h4>
                                    <p>History buff and storyteller bringing San Jose del Monte's past to life. Expert on colonial history, revolutionary sites, and local legends. Let me transport you through time as I share captivating stories about our city's rich historical heritage.</p>
                                </div>

                                <div class="guide-details-grid">
                                    <div class="detail-item">
                                        <span class="material-icons-outlined">wc</span>
                                        <div>
                                            <strong>Gender</strong>
                                            <p>Female</p>
                                        </div>
                                    </div>
                                    <div class="detail-item">
                                        <span class="material-icons-outlined">translate</span>
                                        <div>
                                            <strong>Languages</strong>
                                            <p>English, Tagalog, Spanish</p>
                                        </div>
                                    </div>
                                    <div class="detail-item">
                                        <span class="material-icons-outlined">groups</span>
                                        <div>
                                            <strong>Group Size</strong>
                                            <p>Up to 18 guests</p>
                                        </div>
                                    </div>
                                    <div class="detail-item">
                                        <span class="material-icons-outlined">place</span>
                                        <div>
                                            <strong>Location</strong>
                                            <p>San Jose del Monte, Bulacan</p>
                                        </div>
                                    </div>
                                    <div class="detail-item">
                                        <span class="material-icons-outlined">email</span>
                                        <div>
                                            <strong>Email</strong>
                                            <p>isabella@sjdmtours.com</p>
                                        </div>
                                    </div>
                                    <div class="detail-item">
                                        <span class="material-icons-outlined">phone</span>
                                        <div>
                                            <strong>Phone</strong>
                                            <p>+63 967 890 1234</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="guide-booking-section">
                                    <h4><span class="material-icons-outlined">calendar_today</span> Booking Information</h4>
                                    <p>To book this guide and get detailed tour information, please click the button below.</p>
                                    <div class="booking-actions">
                                        <button class="btn-primary" onclick="bookGuide(6)">
                                            <span class="material-icons-outlined">calendar_today</span>
                                            Book This Guide
                                        </button>
                                        <button class="btn-secondary" onclick="closeModal('modal-guide-6')">
                                            Close
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
          </main>

    <!-- Booking History Modal -->
    <div class="modal-overlay" id="bookingHistoryModal">
        <div class="modal-content booking-modal">
            <div class="modal-header">
                <div class="modal-title">
                    <span class="material-icons-outlined modal-icon">history</span>
                    <h2>Booking History</h2>
                </div>
                <button class="close-modal" onclick="closeModal('bookingHistoryModal')">
                    <span class="material-icons-outlined">close</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Filter Tabs -->
                <div class="booking-filter-tabs">
                    <button class="filter-tab active" data-filter="all">
                        <span class="material-icons-outlined">list_alt</span>
                        <span>All Bookings</span>
                    </button>
                    <button class="filter-tab" data-filter="pending">
                        <span class="material-icons-outlined">schedule</span>
                        <span>Pending</span>
                    </button>
                    <button class="filter-tab" data-filter="confirmed">
                        <span class="material-icons-outlined">check_circle</span>
                        <span>Confirmed</span>
                    </button>
                    <button class="filter-tab" data-filter="completed">
                        <span class="material-icons-outlined">verified</span>
                        <span>Completed</span>
                    </button>
                    <button class="filter-tab" data-filter="cancelled">
                        <span class="material-icons-outlined">cancel</span>
                        <span>Cancelled</span>
                    </button>
                </div>

                <!-- Bookings List -->
                <div id="modalBookingsList" class="bookings-container"></div>
            </div>
        </div>
    </div>

    <script src="script.js"></script>
    <script>
        // Pass current user data to JavaScript
        <?php if (isset($currentUser)): ?>
        const currentUser = <?php echo json_encode($currentUser); ?>;
        localStorage.setItem('currentUser', JSON.stringify(currentUser));
        <?php endif; ?>

        // Modal functionality
        function openModal(modalId) {
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.classList.add('show');
                document.body.style.overflow = 'hidden';
                
                // Load content based on modal type
                if (modalId === 'bookingHistoryModal') {
                    loadBookingHistory();
                }
            }
        }

        function closeModal(modalId) {
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.classList.remove('show');
                document.body.style.overflow = 'auto';
            }
        }

        function openGuideModal(guideId) {
            const modal = document.getElementById('modal-guide-' + guideId);
            if (modal) {
                modal.classList.add('show');
                document.body.style.overflow = 'hidden';
            }
        }

        // Check for guide parameter in URL and open modal
        document.addEventListener('DOMContentLoaded', function() {
            const urlParams = new URLSearchParams(window.location.search);
            const guideId = urlParams.get('guide');
            
            if (guideId) {
                // Open the guide modal after a short delay
                setTimeout(() => {
                    openGuideModal(guideId);
                }, 500);
            }
        });

        function loadBookingHistory() {
            const container = document.getElementById('modalBookingsList');
            if (!container) return;
            
            const userBookings = JSON.parse(localStorage.getItem('userBookings')) || [];
            
            if (userBookings.length === 0) {
                container.innerHTML = `
                    <div class="empty-state-card">
                        <div class="empty-state-icon">
                            <span class="material-icons-outlined">event_busy</span>
                        </div>
                        <h3 class="empty-state-title">No bookings found</h3>
                        <p class="empty-state-text">Start your adventure by booking your first tour with our experienced guides.</p>
                        <button class="btn-primary-action" onclick="closeModal('bookingHistoryModal'); window.location.href='book.php'">
                            <span class="material-icons-outlined">explore</span>
                            <span>Book Now</span>
                        </button>
                    </div>
                `;
                return;
            }
            
            container.innerHTML = userBookings.reverse().map(booking => `
                <div class="booking-card" data-status="${booking.status}">
                    <div class="booking-card-header">
                        <div class="booking-primary-info">
                            <div class="booking-icon">
                                <span class="material-icons-outlined">tour</span>
                            </div>
                            <div class="booking-title-section">
                                <h3 class="booking-title">${booking.guideName}</h3>
                                <p class="booking-destination">
                                    <span class="material-icons-outlined">place</span>
                                    ${booking.destination}
                                </p>
                            </div>
                        </div>
                        <span class="status-badge status-${booking.status}">
                            ${getStatusIcon(booking.status)}
                            <span>${booking.status.toUpperCase()}</span>
                        </span>
                    </div>
                    
                    <div class="booking-card-divider"></div>
                    
                    <div class="booking-details-grid">
                        <div class="detail-item">
                            <div class="detail-icon">
                                <span class="material-icons-outlined">event</span>
                            </div>
                            <div class="detail-content">
                                <div class="detail-label">Check-in Date</div>
                                <div class="detail-value">${formatDate(booking.checkIn)}</div>
                            </div>
                        </div>
                        
                        <div class="detail-item">
                            <div class="detail-icon">
                                <span class="material-icons-outlined">people</span>
                            </div>
                            <div class="detail-content">
                                <div class="detail-label">Number of Guests</div>
                                <div class="detail-value">${booking.guests} Guest${booking.guests > 1 ? 's' : ''}</div>
                            </div>
                        </div>
                        
                        <div class="detail-item highlight">
                            <div class="detail-icon">
                                <span class="material-icons-outlined">payments</span>
                            </div>
                            <div class="detail-content">
                                <div class="detail-label">Total Amount</div>
                                <div class="detail-value price">₱${booking.totalAmount ? booking.totalAmount.toLocaleString() : '2,600'}</div>
                            </div>
                        </div>
                    </div>
                </div>
            `).join('');
        }

        function getStatusIcon(status) {
            const icons = {
                'pending': '<span class="material-icons-outlined">schedule</span>',
                'confirmed': '<span class="material-icons-outlined">check_circle</span>',
                'completed': '<span class="material-icons-outlined">verified</span>',
                'cancelled': '<span class="material-icons-outlined">cancel</span>'
            };
            return icons[status] || '<span class="material-icons-outlined">info</span>';
        }

        function formatDate(dateString) {
            const date = new Date(dateString);
            const options = { weekday: 'short', month: 'short', day: 'numeric', year: 'numeric' };
            return date.toLocaleDateString('en-US', options);
        }

        // Event listeners for dropdown menu items
        document.addEventListener('DOMContentLoaded', function() {
            // Booking History link
            const bookingHistoryLink = document.getElementById('userBookingHistoryLink');
            if (bookingHistoryLink) {
                bookingHistoryLink.addEventListener('click', function(e) {
                    e.preventDefault();
                    openModal('bookingHistoryModal');
                });
            }

            // Close modals when clicking outside
            document.addEventListener('click', function(e) {
                if (e.target.classList.contains('modal-overlay')) {
                    e.target.classList.remove('show');
                    document.body.style.overflow = 'auto';
                }
            });

            // Filter tabs for booking history
            const filterTabs = document.querySelectorAll('.filter-tab');
            filterTabs.forEach(tab => {
                tab.addEventListener('click', function() {
                    filterTabs.forEach(t => t.classList.remove('active'));
                    this.classList.add('active');
                    // Filter functionality can be added here
                });
            });
        });
    </script>
</body>
</html>