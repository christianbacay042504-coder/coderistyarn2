<?php
// Include database connection and authentication
require_once '../config/database.php';
require_once '../config/auth.php';

// OpenWeatherMap API configuration
$apiKey = '6c21a0d2aaf514cb8d21d56814312b19';
$weatherUrl = "https://api.openweathermap.org/data/2.5/weather?q=San%20Jose%20Del%20Monte,Bulacan&appid={$apiKey}&units=metric";

$weatherData = null;
$weatherError = null;
$currentTemp = '28';
$weatherLabel = 'Sunny';

// Fetch weather data
try {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $weatherUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    $weatherResponse = curl_exec($ch);
    
    if (curl_errno($ch)) {
        $weatherError = 'Weather API connection error';
    } else {
        $weatherData = json_decode($weatherResponse, true);
        if ($weatherData && isset($weatherData['main']) && isset($weatherData['weather'][0])) {
            $currentTemp = round($weatherData['main']['temp']);
            $weatherLabel = ucfirst($weatherData['weather'][0]['description']);
        } else {
            $weatherError = 'Weather data unavailable';
        }
    }
    curl_close($ch);
} catch (Exception $e) {
    $weatherError = 'Weather service unavailable';
}

// Get current date and weekday
$currentWeekday = date('l'); // Full weekday name
$currentDate = date('F Y'); // Month Year format

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
    <title>Tourist Spots - San Jose del Monte Bulacan</title>
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
            <a class="nav-item active" href="javascript:void(0)">
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
            <h1>Tourist Spots</h1>
            <div class="search-bar">
                <span class="material-icons-outlined">search</span>
                <input type="text" placeholder="Search destinations...">
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
            <h2 class="section-title">San Jose del Monte Tourist Spots</h2>
            
            <!-- Calendar Header -->
            <div class="calendar-header">
                <div class="date-display">
                    <div class="weekday" id="currentWeekday"><?php echo htmlspecialchars($currentWeekday); ?></div>
                    <div class="month-year" id="currentDate"><?php echo htmlspecialchars($currentDate); ?></div>
                </div>
                <div class="weather-info">
                    <span class="material-icons-outlined"><?php echo $weatherLabel === 'Clear' ? 'wb_sunny' : ($weatherLabel === 'Clouds' ? 'cloud' : 'wb_cloudy'); ?></span>
                    <span class="temperature"><?php echo $currentTemp; ?>°C</span>
                    <span class="weather-label"><?php echo htmlspecialchars($weatherLabel); ?></span>
                </div>
            </div>

            <!-- Filters -->
            <div class="travelry-filters">
                <div class="filter-row">
                    <div class="filter-group">
                        <label>Category</label>
                        <select class="filter-select" id="categoryFilter">
                            <option value="all">All Categories</option>
                            <?php
                            // Fetch unique categories from database
                            $conn = getDatabaseConnection();
                            if ($conn) {
                                $query = "SELECT DISTINCT category FROM tourist_spots WHERE status = 'active'";
                                $result = $conn->query($query);
                                if ($result && $result->num_rows > 0) {
                                    while ($category = $result->fetch_assoc()) {
                                        echo '<option value="' . $category['category'] . '">' . ucfirst($category['category']) . '</option>';
                                    }
                                }
                                closeDatabaseConnection($conn);
                            }
                            ?>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label>Activity Level</label>
                        <select class="filter-select" id="activityFilter">
                            <option value="all">All Levels</option>
                            <option value="easy">Easy</option>
                            <option value="moderate">Moderate</option>
                            <option value="difficult">Difficult</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        
                    </div>
                </div>
            </div>

            <!-- Tourist Spots Grid -->
            <div class="travelry-grid" id="spotsGrid">
                <?php
                // Fetch tourist spots from database
                $conn = getDatabaseConnection();
                if ($conn) {
                    $query = "SELECT * FROM tourist_spots WHERE status = 'active' ORDER BY name";
                    $result = $conn->query($query);
                    
                    if ($result && $result->num_rows > 0) {
                        while ($spot = $result->fetch_assoc()) {
                            // Map database categories to display categories
                            $categoryMap = [
                                'nature' => 'Nature & Waterfalls',
                                'farm' => 'Farms & Eco-Tourism', 
                                'park' => 'Parks & Recreation',
                                'religious' => 'Religious Sites',
                                'urban' => 'Urban Landmarks',
                                'historical' => 'Historical Sites'
                            ];
                            
                            // Map database categories to badge icons
                            $iconMap = [
                                'nature' => 'landscape',
                                'farm' => 'agriculture',
                                'park' => 'park',
                                'religious' => 'church',
                                'urban' => 'location_city',
                                'historical' => 'account_balance'
                            ];
                            
                            // Map database categories to badge labels
                            $badgeMap = [
                                'nature' => 'Nature',
                                'farm' => 'Farm',
                                'park' => 'Park',
                                'religious' => 'Religious',
                                'urban' => 'Urban',
                                'historical' => 'Historical'
                            ];
                            
                            $category = $spot['category'];
                            $displayCategory = $categoryMap[$category] ?? $category;
                            $icon = $iconMap[$category] ?? 'place';
                            $badge = $badgeMap[$category] ?? $category;
                            
                            // Generate star rating HTML
                            $rating = floatval($spot['rating']);
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
                            
                            // Determine activity level based on difficulty
                            $activityLevel = $spot['difficulty_level'];
                            
                            // Get duration for filtering
                            $duration = $spot['duration'] ?? '2-3 hours';
                            
                            echo '<div class="travelry-card" data-category="' . $category . '" data-activity="' . $activityLevel . '" data-duration="' . $duration . '">';
                            echo '<div class="card-image">';
                            echo '<img src="' . htmlspecialchars($spot['image_url']) . '" alt="' . htmlspecialchars($spot['name']) . '">';
                            echo '<div class="card-badge">';
                            echo '<span class="material-icons-outlined">' . $icon . '</span>';
                            echo $badge;
                            echo '</div>';
                            echo '</div>';
                            echo '<div class="card-content">';
                            echo '<div class="card-weather">';
                            echo '<span class="material-icons-outlined">' . ($weatherLabel === 'Clear' ? 'wb_sunny' : ($weatherLabel === 'Clouds' ? 'cloud' : 'wb_cloudy')) . '</span>';
                            echo '<span class="weather-temp">' . $currentTemp . '°C</span>';
                            echo '<span class="weather-desc">' . htmlspecialchars($weatherLabel) . '</span>';
                            echo '</div>';
                            echo '<h3 class="card-title">' . htmlspecialchars($spot['name']) . '</h3>';
                            echo '<span class="card-category">' . htmlspecialchars($displayCategory) . '</span>';
                            echo '<div class="card-stats">';
                            echo '<div class="stat-item">';
                            echo '<span class="stat-label">Rating</span>';
                            echo '<div style="display: flex; align-items: center; gap: 4px;">';
                            echo $starsHtml;
                            echo '<span style="font-size: 12px; color: #666;">(' . $spot['review_count'] . ')</span>';
                            echo '</div>';
                            echo '</div>';
                            echo '<div class="stat-item">';
                            echo '<span class="stat-label">Difficulty</span>';
                            echo '<span class="stat-value">' . ucfirst($spot['difficulty_level']) . '</span>';
                            echo '</div>';
                            echo '<div class="stat-item">';
                            echo '<span class="stat-label">Entrance</span>';
                            echo '<span class="stat-value">' . htmlspecialchars($spot['entrance_fee'] ?? 'Free') . '</span>';
                            echo '</div>';
                            echo '</div>';
                            echo '<div class="card-buttons">';
                            echo '<button class="card-button" onclick="showTouristSpotModal(';
                            echo "'" . addslashes($spot['name']) . "', ";
                            echo "'" . addslashes($displayCategory) . "', ";
                            echo "'" . addslashes($spot['image_url']) . "', ";
                            echo "'" . $icon . "', ";
                            echo "'" . $badge . "', ";
                            echo "'" . htmlspecialchars($spot['entrance_fee'] ?? 'Free') . "', ";
                            echo "'" . $currentTemp . "°C', ";
                            echo "'200 MASL', ";
                            echo "'" . ucfirst($spot['difficulty_level']) . "', ";
                            echo "'" . number_format($spot['rating'], 1) . "', ";
                            echo "'" . $spot['review_count'] . "'";
                            echo ')">';
                            echo 'View Details';
                            echo '</button>';
                            echo '</div>';
                            echo '</div>';
                            echo '</div>';
                        }
                    } else {
                        echo '<div class="no-results" style="grid-column: 1 / -1; text-align: center; padding: 60px 20px;">';
                        echo '<span class="material-icons-outlined" style="font-size: 48px; color: #9ca3af;">place</span>';
                        echo '<h3 style="color: #6b7280; margin-top: 16px;">No tourist spots found</h3>';
                        echo '<p style="color: #9ca3af;">Please check back later for available destinations.</p>';
                        echo '</div>';
                    }
                    closeDatabaseConnection($conn);
                } else {
                    echo '<div class="error-message" style="grid-column: 1 / -1; text-align: center; padding: 60px 20px;">';
                    echo '<span class="material-icons-outlined" style="font-size: 48px; color: #ef4444;">error</span>';
                    echo '<h3 style="color: #ef4444; margin-top: 16px;">Database Connection Error</h3>';
                    echo '<p style="color: #6b7280;">Unable to load tourist spots. Please try again later.</p>';
                    echo '</div>';
                }
                ?>
            </div>
        </div>
    </main>

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

    <script src="script.js"></script>
    <style>
        /* Card Weather Styling */
        .card-weather {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 12px;
            padding: 8px 12px;
            background: linear-gradient(135deg, rgba(74, 124, 78, 0.1), rgba(44, 95, 45, 0.05));
            border-radius: 12px;
            border: 1px solid rgba(74, 124, 78, 0.2);
            font-size: 0.85rem;
        }
        
        .card-weather .material-icons-outlined {
            color: #4a7c4e;
            font-size: 18px;
        }
        
        .weather-temp {
            font-weight: 600;
            color: #2c5f2d;
            font-size: 0.9rem;
        }
        
        .weather-desc {
            color: #666;
            font-size: 0.8rem;
            text-transform: capitalize;
        }
        
        /* Responsive adjustments */
        @media (max-width: 768px) {
            .card-weather {
                padding: 6px 10px;
                gap: 6px;
                font-size: 0.8rem;
            }
            
            .card-weather .material-icons-outlined {
                font-size: 16px;
            }
            
            .weather-temp {
                font-size: 0.85rem;
            }
            
            .weather-desc {
                font-size: 0.75rem;
            }
        }
    </style>
    <script>
        // Tourist Spot Modal Functions - Global Scope
        function showTouristSpotModal(name, category, image, icon, type, temp, elevation, difficulty, duration) {
            console.log('Modal function called with:', { name, category, type });
            const modal = document.getElementById('touristSpotModal');
            
            if (!modal) {
                console.error('Modal element not found!');
                return;
            }
            
            // Update modal content
            document.getElementById('modalSpotName').textContent = name;
            document.getElementById('modalSpotCategory').textContent = category;
            document.getElementById('modalSpotTitle').textContent = name;
            document.getElementById('modalSpotImage').src = image;
            document.getElementById('modalSpotImage').alt = name;
            document.getElementById('modalSpotType').textContent = type;
            document.getElementById('modalSpotTemp').textContent = temp;
            document.getElementById('modalSpotElevation').textContent = elevation;
            document.getElementById('modalSpotDifficulty').textContent = difficulty;
            
            // Update badge icon
            const badgeIcon = document.querySelector('#modalSpotBadge .material-icons-outlined');
            badgeIcon.textContent = icon;
            
            // Generate dynamic description based on spot type
            let description = '';
            let features = [];
            
            if (type === 'Mountain') {
                description = `Experience the breathtaking beauty of ${name}, one of San Jose del Monte's most majestic mountain peaks. This stunning destination offers panoramic views, challenging trails, and an unforgettable adventure for nature enthusiasts and hikers alike.`;
                features = [
                    'Challenging hiking trails with varying difficulty levels',
                    'Spectacular panoramic views of Bulacan province',
                    'Rich biodiversity and unique flora and fauna',
                    'Perfect for sunrise and sunset photography',
                    'Camping spots available for overnight stays'
                ];
            } else if (type === 'Waterfall') {
                description = `Discover the natural wonder of ${name}, a hidden gem nestled in the lush landscapes of San Jose del Monte. This pristine waterfall offers a refreshing escape with its crystal-clear waters and serene surroundings.`;
                features = [
                    'Crystal-clear waters perfect for swimming',
                    'Natural pools for relaxation',
                    'Lush tropical surroundings',
                    'Ideal for nature photography',
                    'Accessible hiking trails with scenic views'
                ];
            } else if (type === 'Farm') {
                description = `Experience sustainable agriculture and rural life at ${name}, a charming eco-tourism destination in San Jose del Monte. This working farm offers hands-on experiences and educational opportunities for visitors of all ages.`;
                features = [
                    'Organic farming practices and sustainable agriculture',
                    'Fresh produce sampling and farm-to-table experiences',
                    'Educational tours about farming techniques',
                    'Interactive activities for children and families',
                    'Scenic rural landscapes and peaceful environment'
                ];
            } else if (type === 'Park') {
                description = `Enjoy recreational activities and natural beauty at ${name}, a well-maintained public space in San Jose del Monte. This park offers facilities for sports, relaxation, and family gatherings in a clean, safe environment.`;
                features = [
                    'Well-maintained sports facilities and equipment',
                    'Children\'s playground and family-friendly areas',
                    'Jogging paths and walking trails',
                    'Picnic areas with benches and tables',
                    'Regular community events and activities'
                ];
            } else if (type === 'Religious') {
                description = `Find spiritual solace and architectural beauty at ${name}, a sacred destination in San Jose del Monte. This religious site offers a peaceful atmosphere for prayer, reflection, and cultural appreciation.`;
                features = [
                    'Beautiful religious architecture and artwork',
                    'Peaceful atmosphere for prayer and meditation',
                    'Cultural and historical significance',
                    'Well-maintained grounds and gardens',
                    'Regular religious services and community events'
                ];
            } else if (type === 'Sports') {
                description = `Get active and enjoy sports facilities at ${name}, a premier recreational destination in San Jose del Monte. This venue offers various sports activities and programs for fitness enthusiasts and athletes.`;
                features = [
                    'Modern sports facilities and equipment',
                    'Professional coaching and training programs',
                    'Various sports courts and playing fields',
                    'Fitness programs and group activities',
                    'Regular tournaments and community sports events'
                ];
            }
            
            document.getElementById('modalSpotDescription').textContent = description;
            
            // Update features list
            const featuresList = document.getElementById('modalSpotFeatures');
            const featureIcons = ['🌟', '📸', '👨‍👩‍👧‍👦', '🍽️', '🏞️', '🥾', '⛰️', '🌿', '🏛️', '⚽'];
            featuresList.innerHTML = features.map((feature, index) => `
                <div class="feature-item">
                    <span class="feature-icon">${featureIcons[index % featureIcons.length]}</span>
                    <span>${feature}</span>
                </div>
            `).join('');
            
            // Show modal
            modal.style.display = 'block';
            document.body.style.overflow = 'hidden';
        }
        
        function closeTouristSpotModal() {
            const modal = document.getElementById('touristSpotModal');
            modal.style.display = 'none';
            document.body.style.overflow = 'auto';
        }
        
        function viewAllDetails() {
            // Get current spot name from modal
            const spotName = document.getElementById('modalSpotName').textContent;
            console.log('Spot name from modal:', JSON.stringify(spotName)); // Debug log
            closeTouristSpotModal();
            
            // Create mapping for spot names to detail pages
            const spotPages = {
                'Mt. Balagbag': '../tourist-detail/mt-balagbag.php',
                'Mt. Balagbag Mountain': '../tourist-detail/mt-balagbag.php',
                'Mount Balagbag': '../tourist-detail/mt-balagbag.php',
                'Abes Farm': '../tourist-detail/abes-farm.php',
                'Abes Farm Resort': '../tourist-detail/abes-farm.php',
                'Burong Falls': '../tourist-detail/burong-falls.php',
                'Burong Falls San Jose del Monte': '../tourist-detail/burong-falls.php',
                'City Oval & People\'s Park': '../tourist-detail/city-ovals-peoples-park.php',
                'Kaytitinga Falls': '../tourist-detail/kaytitinga-falls.php',
                'Kaytitinga Falls San Jose del Monte': '../tourist-detail/kaytitinga-falls.php',
                'Otso Otso Falls': '../tourist-detail/otso-otso-falls.php',
                'Otso-Otso Falls': '../tourist-detail/otso-otso-falls.php',
                'Our Lady of Lourdes': '../tourist-detail/our-lady-of-lourdes.php',
                'Our Lady of Lourdes Parish': '../tourist-detail/our-lady-of-lourdes.php',
                'Padre Pio': '../tourist-detail/padre-pio.php',
                'Padre Pio Shrine': '../tourist-detail/padre-pio.php',
                'Paradise Hill Farm': '../tourist-detail/paradise-hill-farm.php',
                'Paradise Hill Farm Resort': '../tourist-detail/paradise-hill-farm.php',
                'The Rising Heart': '../tourist-detail/the-rising-heart.php',
                'The Rising Heart Farm': '../tourist-detail/the-rising-heart.php',
                'Tungtong Falls': '../tourist-detail/tungtong.php',
                'Tungtong Falls San Jose del Monte': '../tourist-detail/tungtong.php'
            };
            
            console.log('Available spot pages:', Object.keys(spotPages));
            console.log('Looking for spot name:', JSON.stringify(spotName));
            console.log('Direct match exists:', spotPages.hasOwnProperty(spotName));
            const detailPage = spotPages[spotName] || '../tourist-detail/city-ovals-peoples-park.php';
            console.log('Final detail page:', detailPage);
            
            // Redirect to the detail page
            window.location.href = detailPage;
        }
        
        function saveThisSpot() {
            const spotName = document.getElementById('modalSpotName').textContent;
            const saveBtn = document.querySelector('.modal-save-btn');
            
            // Toggle saved state
            if (saveBtn.classList.contains('saved')) {
                saveBtn.classList.remove('saved');
                saveBtn.innerHTML = '<span class="material-icons-outlined">favorite_border</span> Save to Favorites';
                showNotification('Removed from favorites', 'info');
            } else {
                saveBtn.classList.add('saved');
                saveBtn.innerHTML = '<span class="material-icons-outlined">favorite</span> Saved to Favorites';
                showNotification('Added to favorites!', 'success');
            }
        }
        
        function showNotification(message, type = 'info') {
            // Remove any existing notifications
            const existingNotification = document.querySelector('.notification-banner');
            if (existingNotification) {
                existingNotification.remove();
            }

            // Create notification banner
            const notification = document.createElement('div');
            notification.className = `notification-banner ${type}`;
            
            // Icon mapping for different types
            const icons = {
                success: 'check_circle',
                error: 'error',
                warning: 'warning',
                info: 'info'
            };
            
            notification.innerHTML = `
                <span class="material-icons-outlined notification-icon">${icons[type] || 'info'}</span>
                <span class="notification-message">${message}</span>
                <button class="notification-close" onclick="this.parentElement.remove()">
                    <span class="material-icons-outlined">close</span>
                </button>
            `;
            
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
                    if (notification.parentElement) {
                        document.body.removeChild(notification);
                    }
                }, 400);
            }, 3000);
        }
        
        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('touristSpotModal');
            if (event.target === modal) {
                closeTouristSpotModal();
            }
        }
        
        // Close modal with Escape key
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                closeTouristSpotModal();
            }
        });
    </script>

    <script>
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

    <style>
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
            max-width: 980px;
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
            align-items: stretch;
            justify-content: flex-start;
        }
        
        /* Enhanced Modal Styles */
        .modal-header {
    display: flex;
    flex-direction: column;
    gap: 12px;
    padding: 20px 60px 20px 24px; /* Extra right padding for close button */
    background: linear-gradient(135deg, #4a8c4a 0%, #2c5f2d 100%);
    position: relative;
}

.category-badge {
    display: inline-flex;
    align-items: center;
    padding: 6px 16px;
    background: rgba(255, 255, 255, 0.25);
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
    letter-spacing: 0.5px;
    text-transform: uppercase;
    color: white;
    width: fit-content;
    backdrop-filter: blur(10px);
}

.modal-title {
    font-size: 1.75rem;
    font-weight: 700;
    color: white;
    margin: 0;
    line-height: 1.3;
}

.close-btn {
    position: absolute;
    top: 16px;
    right: 16px;
    background: rgba(255, 255, 255, 0.2);
    border: none;
    width: 32px;
    height: 32px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    color: white;
    font-size: 1.25rem;
}
        
        .modal-header-content {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            padding: 40px 40px 32px 40px;
            background: linear-gradient(135deg, #4a7c4e, #2c5f2d);
            min-height: 120px;
        }
        
        .modal-title-section {
            flex: 1;
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
            max-height: calc(90vh - 120px);
            overflow-y: auto;
            width: 100%;
            align-items: stretch;
        }
        
        .modal-hero-section {
            position: relative;
            width: 100%;
            padding-top: 56.25%; /* 16:9 aspect ratio */
            overflow: hidden;
            background: #f0f0f0;
        }
        
        .modal-image-container {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .modal-image-container img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center;
            display: block;
        }
        
        .modal-image-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(to bottom, 
                rgba(0, 0, 0, 0.1) 0%, 
                rgba(0, 0, 0, 0.4) 70%,
                rgba(0, 0, 0, 0.6) 100%);
            pointer-events: none;
        }
        
        .modal-badge {
            position: absolute;
            top: 24px;
            left: 24px;
            background: rgba(255, 255, 255, 0.95);
            color: #2c5f2d;
            padding: 10px 18px;
            border-radius: 25px;
            display: flex;
            align-items: center;
            gap: 8px;
            font-weight: 600;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
            backdrop-filter: blur(10px);
        }
        
        .modal-content-section {
            padding: 40px 36px;
            display: flex;
            flex-direction: column;
            gap: 40px;
            align-items: center;
            justify-content: flex-start;
            width: 100%;
            max-width: 100%;
        }
        
        .modal-info-header {
            text-align: center;
            padding: 0 20px;
            width: 100%;
            max-width: 800px;
            margin: 0 auto;
        }
        
        .modal-info-header h3 {
            font-size: 2.4em;
            font-weight: 700;
            color: #1a1a1a;
            margin: 0 0 20px 0;
            line-height: 1.1;
            background: linear-gradient(135deg, #1a1a1a, #333);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            text-align: center;
        }
        
        .modal-info-header p {
            font-size: 1.2em;
            line-height: 1.8;
            color: #555;
            margin: 0;
            max-width: 650px;
            margin: 0 auto;
            font-weight: 400;
            text-align: center;
        }
        
        .modal-stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 24px;
            margin: 0 -8px;
            width: 100%;
            max-width: 100%;
        }
        
        .modal-stat-card {
            background: linear-gradient(145deg, #ffffff, #f8f9fa);
            border: 1px solid rgba(44, 95, 45, 0.08);
            border-radius: 20px;
            padding: 28px 24px;
            display: flex;
            align-items: center;
            gap: 20px;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 
                0 4px 20px rgba(0, 0, 0, 0.06),
                0 2px 8px rgba(0, 0, 0, 0.04);
            position: relative;
            overflow: hidden;
        }
        
        .modal-stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, #4a7c4e, #2c5f2d);
            transform: scaleX(0);
            transition: transform 0.4s ease;
        }
        
        .modal-stat-card:hover {
            transform: translateY(-6px);
            box-shadow: 
                0 12px 40px rgba(44, 95, 45, 0.15),
                0 6px 20px rgba(0, 0, 0, 0.08);
            border-color: rgba(44, 95, 45, 0.2);
        }
        
        .modal-stat-card:hover::before {
            transform: scaleX(1);
        }
        
        .stat-icon {
            width: 64px;
            height: 64px;
            background: linear-gradient(135deg, #4a7c4e, #2c5f2d);
            color: white;
            border-radius: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            flex-shrink: 0;
            box-shadow: 
                0 8px 24px rgba(44, 95, 45, 0.3),
                0 4px 12px rgba(44, 95, 45, 0.2);
            transition: all 0.3s ease;
        }
        
        .modal-stat-card:hover .stat-icon {
            transform: scale(1.05);
            box-shadow: 
                0 12px 32px rgba(44, 95, 45, 0.4),
                0 6px 16px rgba(44, 95, 45, 0.3);
        }
        
        .stat-info {
            flex: 1;
        }
        
        .stat-label {
            font-size: 0.9em;
            color: #666;
            margin-bottom: 4px;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .stat-value {
            font-size: 1.3em;
            font-weight: 700;
            color: #1a1a1a;
        }
        
        .modal-features-section {
            background: linear-gradient(135deg, rgba(44, 95, 45, 0.03), rgba(44, 95, 45, 0.01));
            padding: 32px;
            border-radius: 20px;
            border: 1px solid rgba(44, 95, 45, 0.08);
            width: 100%;
            max-width: 100%;
            align-self: center;
        }
        
        .section-header {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            margin-bottom: 24px;
            text-align: center;
        }
        
        .section-header h4 {
            font-size: 1.5em;
            font-weight: 600;
            color: #1a1a1a;
            margin: 0;
            text-align: center;
        }
        
        .section-icon {
            width: 36px;
            height: 36px;
            background: linear-gradient(135deg, #ffd700, #ffb300);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            box-shadow: 0 4px 12px rgba(255, 183, 0, 0.3);
            flex-shrink: 0;
        }
        
        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 16px;
            width: 100%;
            justify-items: stretch;
        }
        
        .feature-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 16px;
            background: white;
            border-radius: 12px;
            border: 1px solid #e9ecef;
            transition: all 0.3s ease;
            width: 100%;
            justify-content: flex-start;
        }
        
        .feature-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            border-color: #2c5f2d;
        }
        
        .feature-icon {
            font-size: 20px;
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #f8f9fa, #ffffff);
            border-radius: 8px;
            flex-shrink: 0;
        }
        
        .feature-item span:last-child {
            font-size: 0.95em;
            color: #444;
            font-weight: 500;
            flex: 1;
        }
        
        .modal-actions-section {
            padding-top: 8px;
            width: 100%;
            max-width: 600px;
            align-self: center;
        }
        
        .action-buttons {
            display: flex;
            gap: 16px;
            width: 100%;
            justify-content: center;
        }
        
        .modal-book-btn,
        .modal-save-btn {
            flex: 1;
            padding: 18px 28px;
            border: none;
            border-radius: 14px;
            font-size: 1.05em;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .modal-book-btn {
            background: linear-gradient(135deg, #4a7c4e, #2c5f2d);
            color: white;
            box-shadow: 0 6px 20px rgba(44, 95, 45, 0.3);
        }
        
        .modal-book-btn:hover {
            background: linear-gradient(135deg, #3d6341, #244d26);
            transform: translateY(-3px);
            box-shadow: 0 8px 28px rgba(44, 95, 45, 0.4);
        }
        
        .modal-save-btn {
            background: linear-gradient(135deg, #f8f9fa, #ffffff);
            color: #444;
            border: 2px solid #e9ecef;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }
        
        .modal-save-btn:hover {
            background: linear-gradient(135deg, #fff3e0, #ffe0b2);
            border-color: #ff9800;
            color: #e65100;
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(255, 152, 0, 0.2);
        }
        
        .modal-save-btn.saved {
            background: linear-gradient(135deg, #ff9800, #f57c00);
            color: white;
            border-color: #ff9800;
            box-shadow: 0 6px 20px rgba(255, 152, 0, 0.3);
        }
        
        .modal-save-btn.saved:hover {
            background: linear-gradient(135deg, #f57c00, #e65100);
            box-shadow: 0 8px 28px rgba(255, 152, 0, 0.4);
        }
        
        @media (max-width: 768px) {
            .modal-content {
                margin: 20px;
                width: calc(100% - 40px);
                max-height: calc(100vh - 40px);
            }
            
            .modal-header-content {
                padding: 24px 24px 20px 24px;
            }
            
            .modal-header h2 {
                font-size: 1.6em;
            }
            
            .modal-body {
                flex-direction: column;
                max-height: calc(100vh - 100px);
            }
            
            .modal-hero-section {
                padding-top: 60%; /* Adjusted for mobile */
            }
            
            .modal-badge {
                top: 16px;
                left: 16px;
                padding: 8px 14px;
                font-size: 0.85em;
            }
            
            .modal-content-section {
                padding: 24px;
                gap: 24px;
            }
            
            .modal-info-header h3 {
                font-size: 1.8em;
            }
            
            .modal-info-header p {
                font-size: 1.05em;
            }
            
            .modal-stats-grid {
                grid-template-columns: 1fr;
                gap: 16px;
            }
            
            .modal-stat-card {
                padding: 20px;
            }
            
            .stat-icon {
                width: 48px;
                height: 48px;
                font-size: 22px;
            }
            
            .modal-features-section {
                padding: 24px;
            }
            
            .features-grid {
                grid-template-columns: 1fr;
                gap: 12px;
            }
            
            .feature-item {
                padding: 14px;
            }
            
            .action-buttons {
                flex-direction: column;
                gap: 12px;
            }
            
            .modal-book-btn,
            .modal-save-btn {
                padding: 16px 24px;
                font-size: 1em;
            }
        }
        
        @media (max-width: 480px) {
            .modal-content {
                margin: 10px;
                width: calc(100% - 20px);
                max-height: calc(100vh - 20px);
            }
            
            .modal-header-content {
                padding: 20px 20px 16px 20px;
            }
            
            .modal-header h2 {
                font-size: 1.4em;
            }
            
            .modal-hero-section {
                padding-top: 65%; /* Further adjusted for small screens */
            }
            
            .modal-badge {
                top: 12px;
                left: 12px;
                padding: 6px 12px;
                font-size: 0.8em;
            }
            
            .modal-content-section {
                padding: 20px;
                gap: 20px;
            }
            
            .modal-info-header h3 {
                font-size: 1.6em;
            }
            
            .modal-info-header p {
                font-size: 1em;
            }
            
            .modal-stat-card {
                padding: 16px;
            }
            
            .stat-icon {
                width: 44px;
                height: 44px;
                font-size: 20px;
            }
            
            .modal-features-section {
                padding: 20px;
            }
            
            .section-header h4 {
                font-size: 1.3em;
            }
            
            .section-icon {
                width: 32px;
                height: 32px;
                font-size: 16px;
            }
            
            .feature-item {
                padding: 12px;
            }
            
            .feature-icon {
                width: 28px;
                height: 28px;
                font-size: 18px;
            }
            
            .modal-actions-section {
                padding-top: 0;
            }
        }
    </style>

    <!-- Tourist Spot Detail Modal -->
    <div id="touristSpotModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <div class="modal-header-content">
                    <div class="modal-title-section">
                        <div class="modal-category" id="modalSpotCategory">Category</div>
                        <h2 id="modalSpotName">Tourist Spot Name</h2>
                    </div>
                    <button class="modal-close" onclick="closeTouristSpotModal()">
                        <span class="material-icons-outlined">close</span>
                    </button>
                </div>
            </div>
            
            <div class="modal-body">
                <div class="modal-hero-section">
                    <div class="modal-image-container">
                        <img id="modalSpotImage" src="" alt="">
                        <div class="modal-image-overlay">
                            <div class="modal-badge" id="modalSpotBadge">
                                <span class="material-icons-outlined">place</span>
                                <span id="modalSpotType">Type</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="modal-content-section">
                    <div class="modal-info-header">
                        <h3 id="modalSpotTitle">Spot Title</h3>
                        <p id="modalSpotDescription">Description of the tourist spot...</p>
                    </div>
                    
                    <div class="modal-stats-grid">
                        <div class="modal-stat-card">
                            <div class="stat-icon">
                                <span class="material-icons-outlined">thermostat</span>
                            </div>
                            <div class="stat-info">
                                <div class="stat-label">Temperature</div>
                                <div class="stat-value" id="modalSpotTemp">+24°C</div>
                            </div>
                        </div>
                        
                        <div class="modal-stat-card">
                            <div class="stat-icon">
                                <span class="material-icons-outlined">terrain</span>
                            </div>
                            <div class="stat-info">
                                <div class="stat-label">Elevation</div>
                                <div class="stat-value" id="modalSpotElevation">200 MASL</div>
                            </div>
                        </div>
                        
                        <div class="modal-stat-card">
                            <div class="stat-icon">
                                <span class="material-icons-outlined">signal_cellular_alt</span>
                            </div>
                            <div class="stat-info">
                                <div class="stat-label">Difficulty</div>
                                <div class="stat-value" id="modalSpotDifficulty">Moderate</div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-features-section">
                        <div class="section-header">
                            <h4>What to Expect</h4>
                            <div class="section-icon">
                                <span class="material-icons-outlined">stars</span>
                            </div>
                        </div>
                        <div class="features-grid" id="modalSpotFeatures">
                            <div class="feature-item">
                                <span class="feature-icon">🌟</span>
                                <span>Beautiful natural scenery</span>
                            </div>
                            <div class="feature-item">
                                <span class="feature-icon">📸</span>
                                <span>Perfect for photography</span>
                            </div>
                            <div class="feature-item">
                                <span class="feature-icon">👨‍👩‍👧‍👦</span>
                                <span>Family-friendly environment</span>
                            </div>
                            <div class="feature-item">
                                <span class="feature-icon">🍽️</span>
                                <span>Local food options nearby</span>
                            </div>
                        </div>
                    </div>

                    <div class="modal-actions-section">
                        <div class="action-buttons">
                            <button class="btn-primary modal-book-btn" onclick="viewAllDetails()">
                                <span class="material-icons-outlined">visibility</span>
                                <span>View All</span>
                            </button>
                            <button class="btn-secondary modal-save-btn" onclick="saveThisSpot()">
                                <span class="material-icons-outlined">favorite_border</span>
                                <span>Save to Favorites</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>