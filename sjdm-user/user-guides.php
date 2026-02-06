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
                        <div class="guide-card" 
                             data-guide-id="<?php echo $guide['id']; ?>" 
                             data-category="<?php echo $guide['category']; ?>"
                             data-email="<?php echo isset($guide['email']) ? htmlspecialchars($guide['email']) : ''; ?>"
                             data-phone="<?php echo isset($guide['contact_number']) ? htmlspecialchars($guide['contact_number']) : ''; ?>"
                             data-location="<?php echo isset($guide['location']) ? htmlspecialchars($guide['location']) : (isset($guide['address']) ? htmlspecialchars($guide['address']) : ''); ?>"
                             data-bio="<?php echo isset($guide['bio']) ? htmlspecialchars($guide['bio']) : ''; ?>">
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
                                    <button class="btn-view-profile" onclick="document.getElementById('modal-guide-<?php echo $guide['id']; ?>').classList.add('show')">View Profile</button>
                                </div>
                            </div>
                        </div>

                        <!-- Guide Profile Modal (Server-Side Rendered) -->
                        <div class="modal-overlay" id="modal-guide-<?php echo $guide['id']; ?>">
                            <div class="modal-content guide-profile-modal">
                                <div class="modal-header">
                                    <div class="modal-title">
                                        <span class="material-icons-outlined modal-icon">person</span>
                                        <h2>Guide Profile</h2>
                                    </div>
                                    <button class="close-modal" onclick="this.closest('.modal-overlay').classList.remove('show')">
                                        <span class="material-icons-outlined">close</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <div class="guide-profile-content">
                                        <div class="guide-profile-header">
                                            <div class="guide-profile-photo">
                                                <img src="<?php echo !empty($guide['photo_url']) ? htmlspecialchars($guide['photo_url']) : 'https://via.placeholder.com/400x300/2c5f2d/ffffff?text=' . urlencode($guide['name']); ?>" alt="Guide <?php echo htmlspecialchars($guide['name']); ?>">
                                                <?php if ($guide['verified']): ?>
                                                    <div class="verified-badge">
                                                        <span class="material-icons-outlined">verified</span>
                                                        Verified Guide
                                                    </div>
                                                    <div class="verified-glow"></div>
                                                <?php endif; ?>
                                            </div>
                                            <div class="guide-profile-info">
                                                <div class="guide-name-section">
                                                    <h3><?php echo htmlspecialchars($guide['name']); ?></h3>
                                                    <?php if ($guide['verified']): ?>
                                                        <div class="verified-ribbon">
                                                            <span class="material-icons-outlined">verified_user</span>
                                                            <span>Trusted Professional</span>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                                <p class="guide-specialty"><?php echo htmlspecialchars($guide['specialty']); ?></p>
                                                <div class="guide-rating">
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
                                                <div class="guide-category-badge">
                                                    <span class="material-icons-outlined">category</span>
                                                    <?php 
                                                        $categories = [
                                                            'mountain' => 'Mountain Hiking',
                                                            'city' => 'City Tours',
                                                            'farm' => 'Farm & Eco-Tourism',
                                                            'waterfall' => 'Waterfall Tours',
                                                            'historical' => 'Historical Tours',
                                                            'general' => 'General Tours'
                                                        ];
                                                        echo array_key_exists($guide['category'], $categories) ? $categories[$guide['category']] : htmlspecialchars($guide['category']); 
                                                    ?>
                                                </div>
                                            </div>
                                        </div>

                                        <?php if ($guide['verified']): ?>
                                        <div class="verification-section">
                                            <div class="verification-header">
                                                <span class="material-icons-outlined">security</span>
                                                <h4>Verification Details</h4>
                                            </div>
                                            <div class="verification-grid">
                                                <div class="verification-item">
                                                    <span class="material-icons-outlined">check_circle</span>
                                                    <span>Identity Verified</span>
                                                </div>
                                                <div class="verification-item">
                                                    <span class="material-icons-outlined">workspace_premium</span>
                                                    <span>Professional Certified</span>
                                                </div>
                                                <div class="verification-item">
                                                    <span class="material-icons-outlined">reviews</span>
                                                    <span>Background Checked</span>
                                                </div>
                                                <div class="verification-item">
                                                    <span class="material-icons-outlined">handshake</span>
                                                    <span>Trusted by Community</span>
                                                </div>
                                            </div>
                                        </div>
                                        <?php endif; ?>

                                        <div class="guide-description-section">
                                            <h4><span class="material-icons-outlined">info</span> About</h4>
                                            <p><?php echo isset($guide['bio']) ? htmlspecialchars($guide['bio']) : htmlspecialchars($guide['description']); ?></p>
                                        </div>

                                        <div class="guide-details-grid">
                                            <div class="detail-item">
                                                <span class="material-icons-outlined">wc</span>
                                                <div>
                                                    <strong>Gender</strong>
                                                    <p><?php echo ($guide['id'] % 2 == 0) ? 'Male' : 'Female'; ?></p>
                                                </div>
                                            </div>
                                            <div class="detail-item">
                                                <span class="material-icons-outlined">translate</span>
                                                <div>
                                                    <strong>Languages</strong>
                                                    <p><?php echo !empty($guide['languages']) ? htmlspecialchars($guide['languages']) : 'Tagalog, English'; ?></p>
                                                </div>
                                            </div>
                                            
                                            <?php if (!empty($guide['price_range']) || (!empty($guide['price_min']) && !empty($guide['price_max']))): ?>
                                            <div class="detail-item">
                                                <span class="material-icons-outlined">payments</span>
                                                <div>
                                                    <strong>Pricing</strong>
                                                    <p><?php 
                                                        if (!empty($guide['price_range'])) {
                                                            echo htmlspecialchars($guide['price_range']);
                                                        } else {
                                                            echo '₱' . number_format($guide['price_min']) . ' - ₱' . number_format($guide['price_max']);
                                                        }
                                                    ?></p>
                                                </div>
                                            </div>
                                            <?php endif; ?>

                                            <?php if (!empty($guide['schedules'])): ?>
                                            <div class="detail-item">
                                                <span class="material-icons-outlined">event_available</span>
                                                <div>
                                                    <strong>Availability</strong>
                                                    <p><?php echo htmlspecialchars($guide['schedules']); ?></p>
                                                </div>
                                            </div>
                                            <?php endif; ?>

                                            <?php if (!empty($guide['areas_of_expertise'])): ?>
                                            <div class="detail-item">
                                                <span class="material-icons-outlined">travel_explore</span>
                                                <div>
                                                    <strong>Expertise</strong>
                                                    <p><?php echo htmlspecialchars($guide['areas_of_expertise']); ?></p>
                                                </div>
                                            </div>
                                            <?php endif; ?>

                                            <?php if (!empty($guide['group_size'])): ?>
                                            <div class="detail-item">
                                                <span class="material-icons-outlined">groups</span>
                                                <div>
                                                    <strong>Group Size</strong>
                                                    <p><?php echo htmlspecialchars($guide['group_size']); ?></p>
                                                </div>
                                            </div>
                                            <?php endif; ?>
                                            
                                            <?php if (isset($guide['location']) || isset($guide['address'])): ?>
                                            <div class="detail-item">
                                                <span class="material-icons-outlined">place</span>
                                                <div>
                                                    <strong>Location</strong>
                                                    <p><?php echo isset($guide['location']) ? htmlspecialchars($guide['location']) : htmlspecialchars($guide['address']); ?></p>
                                                </div>
                                            </div>
                                            <?php endif; ?>

                                            <?php if (isset($guide['email'])): ?>
                                            <div class="detail-item">
                                                <span class="material-icons-outlined">email</span>
                                                <div>
                                                    <strong>Email</strong>
                                                    <p><?php echo htmlspecialchars($guide['email']); ?></p>
                                                </div>
                                            </div>
                                            <?php endif; ?>

                                            <?php if (isset($guide['contact_number'])): ?>
                                            <div class="detail-item">
                                                <span class="material-icons-outlined">phone</span>
                                                <div>
                                                    <strong>Phone</strong>
                                                    <p><?php echo htmlspecialchars($guide['contact_number']); ?></p>
                                                </div>
                                            </div>
                                            <?php endif; ?>
                                        </div>

                                        <div class="guide-booking-section">
                                            <h4><span class="material-icons-outlined">calendar_today</span> Booking Information</h4>
                                            <p>To book this guide and get detailed pricing information, please click the button below.</p>
                                            <div class="booking-actions">
                                                <button class="btn-primary" onclick="bookGuide(<?php echo $guide['id']; ?>)">
                                                    <span class="material-icons-outlined">calendar_today</span>
                                                    Book This Guide
                                                </button>
                                                <button class="btn-secondary" onclick="this.closest('.modal-overlay').classList.remove('show')">
                                                    Close
                                                </button>
                                            </div>
                                        </div>
                                    </div>
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