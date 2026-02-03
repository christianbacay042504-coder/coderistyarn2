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
    <title>Hotels - San Jose del Monte Bulacan</title>
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
            <h1>Hotels</h1>
            <div class="search-bar">
                <span class="material-icons-outlined">search</span>
                <input type="text" placeholder="Search accommodations...">
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
            <h2 class="section-title">Hotels & Resorts in San Jose del Monte</h2>
            
            <!-- Location Header -->
            <div class="calendar-header">
                <div class="date-display">
                    <div class="weekday">Near Your Destination</div>
                    <div class="month-year">Best Accommodations for Your Tour</div>
                </div>
                <div class="weather-info">
                    <span class="material-icons-outlined">hotel</span>
                    <span class="temperature">20+ Options</span>
                    <span class="weather-label">Verified Stays</span>
                </div>
            </div>

            <!-- Hotel Category Filters -->
            <div class="travelry-filters">
                <div class="filter-row">
                    <div class="filter-group">
                        <label>Hotel Category</label>
                        <select class="filter-select" id="hotelTypeFilter">
                            <option value="all">All Accommodations</option>
                            <?php
                            // Fetch unique categories from database
                            $conn = getDatabaseConnection();
                            if ($conn) {
                                $query = "SELECT DISTINCT category FROM hotels WHERE status = 'active'";
                                $result = $conn->query($query);
                                if ($result && $result->num_rows > 0) {
                                    while ($category = $result->fetch_assoc()) {
                                        $displayCategory = ucfirst(str_replace('-', ' ', $category['category']));
                                        echo '<option value="' . $category['category'] . '">' . $displayCategory . '</option>';
                                    }
                                }
                                closeDatabaseConnection($conn);
                            }
                            ?>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label>Near Tourist Spot</label>
                        <select class="filter-select" id="nearbySpotFilter">
                            <option value="all">All Areas</option>
                            <option value="nature">Nature & Waterfalls</option>
                            <option value="farm">Farm Tours</option>
                            <option value="park">City Parks</option>
                            <option value="religious">Religious Sites</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label>Price Range</label>
                        <select class="filter-select" id="priceFilter">
                            <option value="all">All Prices</option>
                            <option value="budget">Budget (₱800 - ₱1,500)</option>
                            <option value="mid">Mid-Range (₱1,500 - ₱3,000)</option>
                            <option value="premium">Premium (₱3,000+)</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Hotels Grid -->
            <div class="travelry-grid" id="hotelsGrid">
                <?php
                // Fetch hotels from database
                $conn = getDatabaseConnection();
                if ($conn) {
                    $query = "SELECT * FROM hotels WHERE status = 'active' ORDER BY name";
                    $result = $conn->query($query);
                    
                    if ($result && $result->num_rows > 0) {
                        while ($hotel = $result->fetch_assoc()) {
                            // Map database categories to display categories
                            $categoryMap = [
                                'luxury' => 'Luxury Hotels',
                                'mid-range' => 'Mid-Range Hotels',
                                'budget' => 'Budget Hotels',
                                'event' => 'Event Venues'
                            ];
                            
                            // Map database categories to badge icons
                            $iconMap = [
                                'luxury' => 'stars',
                                'mid-range' => 'hotel',
                                'budget' => 'savings',
                                'event' => 'event'
                            ];
                            
                            // Map database categories to badge labels
                            $badgeMap = [
                                'luxury' => 'Luxury',
                                'mid-range' => 'Mid-Range',
                                'budget' => 'Budget',
                                'event' => 'Event'
                            ];
                            
                            // Map price ranges to filter values
                            $priceRangeMap = [
                                '₱800 - ₱1,800 per night' => 'budget',
                                '₱1,500 - ₱3,500 per night' => 'mid',
                                '₱3,000 - ₱8,000 per night' => 'premium',
                                '₱5,000 - ₱15,000 per event' => 'premium'
                            ];
                            
                            $category = $hotel['category'];
                            $displayCategory = $categoryMap[$category] ?? $category;
                            $icon = $iconMap[$category] ?? 'hotel';
                            $badge = $badgeMap[$category] ?? $category;
                            $priceFilter = $priceRangeMap[$hotel['price_range']] ?? 'mid';
                            
                            // Generate star rating HTML
                            $rating = floatval($hotel['rating']);
                            $fullStars = floor($rating);
                            $hasHalfStar = ($rating - $fullStars) >= 0.5;
                            $starsHtml = '';
                            
                            for ($i = 0; $i < $fullStars; $i++) {
                                $starsHtml .= '<span class="material-icons-outlined" style="color: #ffc107; font-size: 16px;">star</span>';
                            }
                            if ($hasHalfStar) {
                                $starsHtml .= '<span class="material-icons-outlined" style="color: #ffc107; font-size: 16px;">star_half</span>';
                            }
                            $emptyStars = 5 - $fullStars - ($hasHalfStar ? 1 : 0);
                            for ($i = 0; $i < $emptyStars; $i++) {
                                $starsHtml .= '<span class="material-icons-outlined" style="color: #ddd; font-size: 16px;">star_outline</span>';
                            }
                            
                            // Parse amenities for display
                            $amenitiesArray = [];
                            if (!empty($hotel['amenities'])) {
                                $amenitiesArray = array_map('trim', explode(',', $hotel['amenities']));
                                $amenitiesArray = array_slice($amenitiesArray, 0, 3); // Show only first 3 amenities
                            }
                            
                            echo '<div class="travelry-card" data-category="' . $category . '" data-nearby="all" data-price="' . $priceFilter . '">';
                            echo '<div class="card-image">';
                            echo '<img src="' . htmlspecialchars($hotel['image_url']) . '" alt="' . htmlspecialchars($hotel['name']) . '">';
                            echo '<div class="card-badge">';
                            echo '<span class="material-icons-outlined">' . $icon . '</span>';
                            echo $badge;
                            echo '</div>';
                            echo '<div class="distance-badge">';
                            echo '<span class="material-icons-outlined">location_on</span>';
                            echo htmlspecialchars($hotel['location'] ?? 'San Jose del Monte');
                            echo '</div>';
                            echo '</div>';
                            echo '<div class="card-content">';
                            echo '<div class="card-date">';
                            echo '<span class="material-icons-outlined">schedule</span>';
                            echo 'Check-in: 2:00 PM • Check-out: 12:00 PM';
                            echo '</div>';
                            echo '<h3 class="card-title">' . htmlspecialchars($hotel['name']) . '</h3>';
                            echo '<span class="card-category">' . htmlspecialchars($displayCategory) . '</span>';
                            echo '<p class="card-description">' . htmlspecialchars($hotel['description'] ?? 'Experience comfort and convenience in San Jose del Monte.') . '</p>';
                            echo '<div class="card-stats">';
                            echo '<div class="stat-item">';
                            echo '<span class="stat-label">Rating</span>';
                            echo '<div style="display: flex; align-items: center; gap: 4px;">';
                            echo $starsHtml;
                            echo '<span style="font-size: 12px; color: #666;">(' . $hotel['review_count'] . ')</span>';
                            echo '</div>';
                            echo '</div>';
                            echo '<div class="stat-item">';
                            echo '<span class="stat-label">Price</span>';
                            echo '<span class="stat-value">' . htmlspecialchars($hotel['price_range']) . '</span>';
                            echo '</div>';
                            echo '<div class="stat-item">';
                            echo '<span class="stat-label">Contact</span>';
                            echo '<span class="stat-value">' . htmlspecialchars($hotel['phone'] ?? 'Available') . '</span>';
                            echo '</div>';
                            echo '</div>';
                            echo '<div class="card-features">';
                            foreach ($amenitiesArray as $amenity) {
                                echo '<span class="feature-tag">' . htmlspecialchars($amenity) . '</span>';
                            }
                            echo '</div>';
                            echo '<button class="card-button" onclick="showHotelDetails(';
                            echo "'" . addslashes($hotel['name']) . "', ";
                            echo "'" . addslashes($displayCategory) . "', ";
                            echo "'" . addslashes($hotel['image_url']) . "', ";
                            echo "'" . $icon . "', ";
                            echo "'" . $badge . "', ";
                            echo "'" . addslashes($hotel['price_range']) . "', ";
                            echo "'" . number_format($hotel['rating'], 1) . "', ";
                            echo "'" . $hotel['review_count'] . "', ";
                            echo "'" . addslashes($hotel['description'] ?? '') . "', ";
                            echo "'" . addslashes($hotel['amenities'] ?? '') . "'";
                            echo ')">';
                            echo 'View Details';
                            echo '</button>';
                            echo '</div>';
                            echo '</div>';
                        }
                    } else {
                        echo '<div class="no-results" style="grid-column: 1 / -1; text-align: center; padding: 60px 20px;">';
                        echo '<span class="material-icons-outlined" style="font-size: 48px; color: #9ca3af;">hotel</span>';
                        echo '<h3 style="color: #6b7280; margin-top: 16px;">No hotels found</h3>';
                        echo '<p style="color: #9ca3af;">Please check back later for available accommodations.</p>';
                        echo '</div>';
                    }
                    closeDatabaseConnection($conn);
                } else {
                    echo '<div class="error-message" style="grid-column: 1 / -1; text-align: center; padding: 60px 20px;">';
                    echo '<span class="material-icons-outlined" style="font-size: 48px; color: #ef4444;">error</span>';
                    echo '<h3 style="color: #ef4444; margin-top: 16px;">Database Connection Error</h3>';
                    echo '<p style="color: #6b7280;">Unable to load hotels. Please try again later.</p>';
                    echo '</div>';
                }
                ?>
            </div>

            <!-- Hotel Booking Tips -->
            <div class="info-section" style="background: white; padding: 30px; border-radius: 20px; margin-top: 40px;">
                <h3>💡 Hotel Booking Tips for SJDM Visitors</h3>
                <div class="info-cards" style="margin-top: 20px;">
                    <?php
                    // Fetch real hotel data for tips
                    $conn = getDatabaseConnection();
                    if ($conn) {
                        // Get price ranges for tips
                        $priceQuery = "SELECT DISTINCT price_range, category FROM hotels WHERE status = 'active' ORDER BY price_range";
                        $priceResult = $conn->query($priceQuery);
                        
                        // Get categories for tips
                        $categoryQuery = "SELECT DISTINCT category, COUNT(*) as count FROM hotels WHERE status = 'active' GROUP BY category";
                        $categoryResult = $conn->query($categoryQuery);
                        
                        // Get average rating
                        $ratingQuery = "SELECT AVG(rating) as avg_rating, COUNT(*) as total_hotels FROM hotels WHERE status = 'active'";
                        $ratingResult = $conn->query($ratingQuery);
                        $ratingData = $ratingResult ? $ratingResult->fetch_assoc() : null;
                        
                        echo '<div class="info-card">';
                        echo '<h4>📍 Available Hotel Categories</h4>';
                        echo '<ul>';
                        if ($categoryResult && $categoryResult->num_rows > 0) {
                            while ($category = $categoryResult->fetch_assoc()) {
                                $categoryName = ucfirst(str_replace('-', ' ', $category['category']));
                                echo '<li><strong>' . $categoryName . ':</strong> ' . $category['count'] . ' option' . ($category['count'] > 1 ? 's' : '') . ' available</li>';
                            }
                        }
                        echo '</ul>';
                        echo '</div>';
                        
                        echo '<div class="info-card">';
                        echo '<h4>💰 Available Price Ranges</h4>';
                        echo '<ul>';
                        if ($priceResult && $priceResult->num_rows > 0) {
                            while ($price = $priceResult->fetch_assoc()) {
                                $categoryName = ucfirst(str_replace('-', ' ', $price['category']));
                                echo '<li><strong>' . $categoryName . ':</strong> ' . htmlspecialchars($price['price_range']) . '</li>';
                            }
                        }
                        echo '</ul>';
                        echo '</div>';
                        
                        echo '<div class="info-card">';
                        echo '<h4>� Hotel Statistics</h4>';
                        echo '<ul>';
                        if ($ratingData) {
                            echo '<li><strong>Total Hotels:</strong> ' . $ratingData['total_hotels'] . ' available</li>';
                            echo '<li><strong>Average Rating:</strong> ' . number_format($ratingData['avg_rating'], 1) . '/5 stars</li>';
                        }
                        echo '<li><strong>Check-in Time:</strong> 2:00 PM (Standard)</li>';
                        echo '<li><strong>Check-out Time:</strong> 12:00 PM (Standard)</li>';
                        echo '</ul>';
                        echo '</div>';
                        
                        closeDatabaseConnection($conn);
                    } else {
                        // Fallback if database connection fails
                        echo '<div class="info-card">';
                        echo '<h4>📞 Booking Advice</h4>';
                        echo '<ul>';
                        echo '<li>Book 1-2 weeks ahead for weekend stays</li>';
                        echo '<li>Confirm transportation arrangements</li>';
                        echo '<li>Check hotel policies on group sizes</li>';
                        echo '<li>Ask about tour guide coordination services</li>';
                        echo '</ul>';
                        echo '</div>';
                    }
                    ?>
                </div>
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
            
            if (signoutLink) {
                signoutLink.addEventListener('click', function(e) {
                    e.preventDefault();
                    profileMenu.classList.remove('active');
                    handleLogout();
                });
            }
        });
    </script>

    <!-- Hotel Booking Modal -->
    <div id="hotelBookingModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <div class="modal-header-content">
                    <div class="modal-title-section">
                        <div class="modal-category">Hotel Details</div>
                        <h2 id="modalHotelName">Hotel Name</h2>
                    </div>
                    <button class="modal-close" onclick="closeHotelBookingModal()">
                        <span class="material-icons-outlined">close</span>
                    </button>
                </div>
            </div>
            
            <div class="modal-body">
                <div class="hotel-details-container">
                    
                </div>
            </div>
        </div>
    </div>

    <script>
        // Hotel Details Modal Functions
        function showHotelDetails(hotelName, category, image, icon, badge, priceRange, rating, reviewCount, description, amenities) {
            const modal = document.getElementById('hotelBookingModal');
            const hotelNameElement = document.getElementById('modalHotelName');
            
            if (!modal) {
                console.error('Hotel details modal not found!');
                return;
            }
            
            // Set hotel name
            hotelNameElement.textContent = hotelName;
            
            // Create hotel info object from database parameters
            const hotelInfo = {
                category: category,
                priceRange: priceRange,
                rating: rating,
                reviewCount: reviewCount,
                location: 'San Jose del Monte',
                description: description || 'Experience comfort and convenience in San Jose del Monte.',
                amenities: amenities || 'Basic amenities available'
            };
            
            // Update modal content with hotel details
            updateHotelDetailsContent(hotelInfo);
            
            // Show modal
            modal.style.display = 'block';
            document.body.style.overflow = 'hidden';
        }
        
        function updateHotelDetailsContent(hotelInfo) {
            // Update modal body with hotel details from database
            const modalBody = document.querySelector('.hotel-details-container');
            if (!hotelInfo) {
                modalBody.innerHTML = '<p>Hotel information not available.</p>';
                return;
            }
            
            modalBody.innerHTML = `
                <div class="hotel-overview">
                    <div class="hotel-info-grid">
                        <div class="info-item">
                            <span class="info-label">Category</span>
                            <span class="info-value">${hotelInfo.category}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Price Range</span>
                            <span class="info-value">${hotelInfo.priceRange}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Rating</span>
                            <span class="info-value">${hotelInfo.rating}/5 (${hotelInfo.reviewCount} reviews)</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Location</span>
                            <span class="info-value">${hotelInfo.location}</span>
                        </div>
                    </div>
                </div>
                
                <div class="hotel-description">
                    <h3>About This Hotel</h3>
                    <p>${hotelInfo.description}</p>
                </div>
                
                <div class="hotel-features">
                    <h3>Amenities & Features</h3>
                    <div class="features-list">
                        ${hotelInfo.amenities ? hotelInfo.amenities.split(',').map(feature => `<span class="feature-badge">${feature.trim()}</span>`).join('') : '<span class="feature-badge">Basic Amenities</span>'}
                    </div>
                </div>
                
                <div class="hotel-schedule">
                    <h3>Check-in & Check-out</h3>
                    <div class="schedule-grid">
                        <div class="schedule-item">
                            <span class="schedule-icon">🕐</span>
                            <div>
                                <div class="schedule-label">Check-in</div>
                                <div class="schedule-time">2:00 PM</div>
                            </div>
                        </div>
                        <div class="schedule-item">
                            <span class="schedule-icon">🕑</span>
                            <div>
                                <div class="schedule-label">Check-out</div>
                                <div class="schedule-time">12:00 PM</div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="modal-actions">
                    <button type="button" class="btn-secondary" onclick="closeHotelBookingModal()">
                        <span class="material-icons-outlined">close</span>
                        Close
                    </button>
                    <button type="button" class="btn-primary" onclick="contactHotel()">
                        <span class="material-icons-outlined">visibility</span>
                        View All
                    </button>
                </div>
            `;
        }
        
        function closeHotelBookingModal() {
            const modal = document.getElementById('hotelBookingModal');
            modal.style.display = 'none';
            document.body.style.overflow = 'auto';
        }
        
        function contactHotel() {
            const hotelName = document.getElementById('modalHotelName').textContent;
            closeHotelBookingModal();
            
            // Create mapping for hotel names to detail pages
            const hotelPages = {
                'Pacific Waves Resort': '../hotel-detail/pacific-waves-resort.php',
                'Grotto Vista Resort': '../hotel-detail/grotto-vista-resort.php',
                'Los Arcos De Hermano': '../hotel-detail/los-arcos-de-hermano.php',
                'The Hillside Farm Resort': '../hotel-detail/the-hillside-farm-resort.php',
                'Tierra Fontana 12 Waves': '../hotel-detail/tierra-fontana-12-waves.php',
                'Hotel Savano': '../hotel-detail/hotel-savano.php',
                'Reddoorz @ GaDi Hotel': '../hotel-detail/reddoorz-gadi-hotel.php',
                'Hotel Sogo SJDM': '../hotel-detail/hotel-sogo-sjdm.php',
                'Hotel Turista San Jose': '../hotel-detail/hotel-turista-san-jose.php',
                'La Cecilia Resort': '../hotel-detail/la-cecilia-resort.php',
                'Casa Regina Resorts': '../hotel-detail/casa-regina-resorts.php'
            };
            
            // Get the appropriate page URL or default to a generic page
            const detailPage = hotelPages[hotelName] || '../hotel-detail/hotels.php';
            
            // Redirect to the detail page
            window.location.href = detailPage;
        }
        
        function showNotification(message, type = 'info') {
            // Create notification element
            const notification = document.createElement('div');
            notification.className = `notification notification-${type}`;
            notification.textContent = message;
            
            // Add to page
            document.body.appendChild(notification);
            
            // Show notification
            setTimeout(() => {
                notification.classList.add('show');
            }, 100);
            
            // Hide and remove after 3 seconds
            setTimeout(() => {
                notification.classList.remove('show');
                setTimeout(() => {
                    document.body.removeChild(notification);
                }, 300);
            }, 3000);
        }
        
        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('hotelBookingModal');
            if (event.target === modal) {
                closeHotelBookingModal();
            }
        }
        
        // Close modal with Escape key
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                closeHotelBookingModal();
            }
        });
    </script>

    <style>
        /* Hotel Booking Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 9999;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.85);
            backdrop-filter: blur(8px);
        }
        
        .modal-content {
            background: white;
            margin: 20px auto;
            padding: 0;
            border-radius: 24px;
            width: 95%;
            max-width: 900px;
            max-height: 95vh;
            overflow: hidden;
            box-shadow: 
                0 32px 64px rgba(0, 0, 0, 0.25),
                0 16px 32px rgba(0, 0, 0, 0.15),
                0 8px 16px rgba(0, 0, 0, 0.1);
            position: relative;
            border: 1px solid rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
            display: flex;
            flex-direction: column;
        }
        
        .modal-header {
            padding: 0;
            border-bottom: none;
            background: white;
            border-radius: 20px 20px 0 0;
            overflow: hidden;
        }
        
        .modal-header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 60px 50px 50px 50px;
            background: linear-gradient(135deg, #4a7c4e 0%, #2c5f2d 50%, #1a4d1e 100%);
            min-height: 180px;
            position: relative;
            overflow: hidden;
        }
        
        .modal-header-content::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grid" width="10" height="10" patternUnits="userSpaceOnUse"><path d="M 10 0 L 0 0 0 10" fill="none" stroke="rgba(255,255,255,0.05)" stroke-width="0.5"/></pattern></defs><rect width="100" height="100" fill="url(%23grid)"/></svg>');
            opacity: 0.3;
        }
        
        .modal-title-section {
            flex: 1;
            text-align: center;
            padding-right: 60px;
        }
        
        .modal-category {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 0.85em;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            display: inline-block;
            margin-bottom: 12px;
            backdrop-filter: blur(10px);
        }
        
        .modal-header h2 {
            font-size: 2em;
            font-weight: 700;
            color: white;
            margin: 0;
            line-height: 1.2;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        
        .modal-close {
            background: rgba(255, 255, 255, 0.15);
            border: none;
            border-radius: 12px;
            width: 44px;
            height: 44px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
        }
        
        .modal-close:hover {
            background: rgba(255, 255, 255, 0.25);
            transform: scale(1.05);
        }
        
        .modal-close .material-icons-outlined {
            color: white;
            font-size: 22px;
        }
        
        .modal-body {
            display: flex;
            flex-direction: column;
            max-height: calc(95vh - 180px);
            overflow-y: auto;
            padding: 0;
            background: linear-gradient(135deg, #fafafa 0%, #ffffff 100%);
        }
        
        .modal-body::-webkit-scrollbar {
            width: 8px;
        }
        
        .modal-body::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }
        
        .modal-body::-webkit-scrollbar-thumb {
            background: linear-gradient(135deg, #4a7c4e, #2c5f2d);
            border-radius: 10px;
        }
        
        .modal-body::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(135deg, #3d6341, #244d26);
        }
        
        .booking-form-container {
            width: 100%;
        }
        
        .form-section {
            margin-bottom: 40px;
        }
        
        .form-section h3 {
            font-size: 1.5em;
            font-weight: 600;
            color: #1a1a1a;
            margin: 0 0 24px 0;
            padding-bottom: 12px;
            border-bottom: 2px solid #f0f0f0;
        }
        
        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 24px;
        }
        
        .form-group {
            display: flex;
            flex-direction: column;
        }
        
        .form-group label {
            font-weight: 500;
            color: #333;
            margin-bottom: 8px;
            font-size: 0.95em;
        }
        
        .form-group input,
        .form-group select,
        .form-group textarea {
            padding: 12px 16px;
            border: 2px solid #e0e0e0;
            border-radius: 12px;
            font-size: 1em;
            transition: all 0.3s ease;
            background: #fafafa;
        }
        
        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #4a7c4e;
            background: white;
            box-shadow: 0 0 0 4px rgba(74, 124, 78, 0.1);
        }
        
        .form-group textarea {
            resize: vertical;
            min-height: 80px;
        }
        
        .booking-summary {
            background: linear-gradient(135deg, #f8f9fa, #ffffff);
            border: 1px solid #e0e0e0;
            border-radius: 16px;
            padding: 24px;
            margin-bottom: 32px;
        }
        
        .booking-summary h3 {
            font-size: 1.3em;
            font-weight: 600;
            color: #1a1a1a;
            margin: 0 0 20px 0;
        }
        
        .summary-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 0;
            border-bottom: 1px solid #f0f0f0;
        }
        
        .summary-item:last-child {
            border-bottom: none;
        }
        
        .summary-item span:first-child {
            font-weight: 500;
            color: #666;
        }
        
        .summary-item span:last-child {
            font-weight: 600;
            color: #1a1a1a;
        }
        
        .modal-actions {
            display: flex;
            gap: 16px;
            justify-content: flex-end;
            padding-top: 24px;
            border-top: 2px solid #f0f0f0;
        }
        
        .btn-primary,
        .btn-secondary {
            padding: 14px 28px;
            border: none;
            border-radius: 12px;
            font-size: 1em;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #4a7c4e, #2c5f2d);
            color: white;
            box-shadow: 0 6px 20px rgba(44, 95, 45, 0.3);
        }
        
        .btn-primary:hover {
            background: linear-gradient(135deg, #3d6341, #244d26);
            transform: translateY(-2px);
            box-shadow: 0 8px 28px rgba(44, 95, 45, 0.4);
        }
        
        .btn-secondary {
            background: linear-gradient(135deg, #f8f9fa, #ffffff);
            color: #666;
            border: 2px solid #e0e0e0;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }
        
        .btn-secondary:hover {
            background: linear-gradient(135deg, #fff3e0, #ffe0b2);
            border-color: #ff9800;
            color: #e65100;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(255, 152, 0, 0.2);
        }
        
        .notification {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 16px 24px;
            border-radius: 12px;
            color: white;
            font-weight: 500;
            z-index: 10000;
            opacity: 0;
            transform: translateY(-20px);
            transition: all 0.3s ease;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
        }
        
        .notification.show {
            opacity: 1;
            transform: translateY(0);
        }
        
        .notification-success {
            background: linear-gradient(135deg, #4caf50, #388e3c);
        }
        
        .notification-error {
            background: linear-gradient(135deg, #f44336, #d32f2f);
        }
                max-height: calc(100vh - 20px);
            }
            
            .modal-header-content {
                padding: 24px 24px 20px 24px;
            }
            
            .modal-header h2 {
                font-size: 1.6em;
            }
            
            .modal-body {
                padding: 24px;
            }
            
            .form-grid {
                grid-template-columns: 1fr;
                gap: 16px;
            }
            
            .modal-actions {
                flex-direction: column;
                gap: 12px;
            }
            
            .btn-primary,
            .btn-secondary {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
</body>
</html>