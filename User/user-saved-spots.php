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
$currentWeekday = date('l');
$currentDate = date('F Y');

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
    <title>Saved Tourist Spots - SJDM Tours</title>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="user-styles.css">
    
    <!-- Full-width layout and header styles -->
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

        .dropdown-divider {
            height: 1px;
            background: #eee;
            margin: 4px 0;
        }

        .main-content.full-width .content-area {
            padding: 40px;
            max-width: 1400px;
            margin: 0 auto;
        }

        /* Saved Spots Grid */
        .spots-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 30px;
            margin-top: 30px;
        }

        .spot-card {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.08);
            border: 1px solid rgba(44, 95, 45, 0.1);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            cursor: pointer;
            position: relative;
        }

        .spot-card:hover {
            transform: translateY(-8px) scale(1.02);
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.12);
            border-color: rgba(44, 95, 45, 0.2);
        }

        .spot-card-image {
            width: 100%;
            height: 220px;
            object-fit: cover;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #6c757d;
            font-size: 48px;
        }

        .spot-card-content {
            padding: 25px;
        }

        .spot-card-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 15px;
        }

        .spot-card-title {
            font-size: 1.4rem;
            font-weight: 700;
            color: var(--text-primary);
            margin: 0;
            line-height: 1.3;
        }

        .spot-card-category {
            background: var(--primary);
            color: white;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .spot-card-description {
            color: var(--text-secondary);
            line-height: 1.6;
            margin-bottom: 20px;
            font-size: 0.95rem;
        }

        .spot-card-features {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-bottom: 20px;
        }

        .feature-tag {
            background: rgba(44, 95, 45, 0.1);
            color: var(--primary);
            padding: 6px 12px;
            border-radius: 15px;
            font-size: 0.8rem;
            font-weight: 500;
            border: 1px solid rgba(44, 95, 45, 0.2);
        }

        .spot-card-actions {
            display: flex;
            gap: 12px;
            align-items: center;
        }

        .btn-remove {
            background: var(--danger);
            color: white;
            border: none;
            padding: 10px 16px;
            border-radius: 8px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .btn-remove:hover {
            background: #dc2626;
            transform: translateY(-1px);
        }

        .btn-view {
            background: var(--primary);
            color: white;
            border: none;
            padding: 10px 16px;
            border-radius: 8px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s ease;
            text-decoration: none;
            flex: 1;
        }

        .btn-view:hover {
            background: var(--primary-dark);
            transform: translateY(-1px);
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 80px 40px;
            background: white;
            border-radius: 20px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.08);
        }

        .empty-icon {
            font-size: 64px;
            color: var(--text-secondary);
            margin-bottom: 20px;
        }

        .empty-state h3 {
            font-size: 1.8rem;
            color: var(--text-primary);
            margin-bottom: 12px;
        }

        .empty-state p {
            color: var(--text-secondary);
            line-height: 1.6;
            margin-bottom: 30px;
        }

        /* Responsive Design */
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
            
            .spots-grid {
                grid-template-columns: 1fr;
                gap: 20px;
            }
            
            .spot-card {
                border-radius: 16px;
            }
            
            .spot-card-image {
                height: 180px;
            }
            
            .spot-card-content {
                padding: 20px;
            }
            
            .spot-card-title {
                font-size: 1.2rem;
            }
            
            .spot-card-actions {
                flex-direction: column;
                gap: 10px;
            }
        }
    </style>
</head>
<body>
    <!-- MAIN CONTENT -->
    <main class="main-content full-width">
        <header class="main-header">
            <div class="header-left">
                <h1 id="pageTitle">Saved Tourist Spots</h1>
               
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
                    <a href="user-tourist-spots.php" class="nav-link active">
                        <span class="material-icons-outlined">place</span>
                        <span>Tourist Spots</span>
                    </a>
                    <a href="user-saved-tours.php" class="nav-link">
                        <span class="material-icons-outlined">favorite</span>
                        <span>Saved Tours</span>
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
                    <div class="user-profile-dropdown">
                        <button class="profile-trigger">
                            <div class="profile-avatar"><?php echo isset($currentUser['name']) ? strtoupper(substr($currentUser['name'], 0, 1)) : 'U'; ?></div>
                            <span class="profile-name"><?php echo htmlspecialchars($currentUser['name']); ?></span>
                            <span class="material-icons-outlined">expand_more</span>
                        </button>
                        <div class="dropdown-menu">
                            <div class="dropdown-header">
                                <div class="profile-avatar large"><?php echo isset($currentUser['name']) ? strtoupper(substr($currentUser['name'], 0, 1)) : 'U'; ?></div>
                                <div class="profile-details">
                                    <h4><?php echo htmlspecialchars($currentUser['name']); ?></h4>
                                    <p><?php echo htmlspecialchars($currentUser['email']); ?></p>
                                </div>
                            </div>
                            <div class="dropdown-divider"></div>
                            <a href="user-index.php" class="dropdown-item">
                                <span class="material-icons-outlined">dashboard</span>
                                <span>Dashboard</span>
                            </a>
                            <a href="user-booking-history.php" class="dropdown-item">
                                <span class="material-icons-outlined">history</span>
                                <span>Booking History</span>
                            </a>
                            <a href="user-saved-spots.php" class="dropdown-item">
                                <span class="material-icons-outlined">favorite</span>
                                <span>Saved Spots</span>
                            </a>
                            <a href="user-saved-tours.php" class="dropdown-item">
                                <span class="material-icons-outlined">people</span>
                                <span>Saved Guides</span>
                            </a>
                            <a href="#" class="dropdown-item" onclick="openPreferencesModal(); return false;">
                                <span class="material-icons-outlined">tune</span>
                                <span>Preferences</span>
                            </a>
                            <div class="dropdown-divider"></div>
                            <a href="user-logout.php" class="dropdown-item">
                                <span class="material-icons-outlined">logout</span>
                                <span>Sign Out</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <div class="content-area">
            <h2 class="section-title">Your Saved Tourist Spots</h2>
            
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

            <div id="savedSpotsList" class="spots-grid"></div>
        </div>
    </main>

    <script>
        // Tourist Spots Data
        const touristSpots = [
            {
                name: 'Mt. Balagbag',
                category: 'Mountain',
                type: 'mountain',
                image: '🏔️',
                description: 'Experience breathtaking beauty of Mt. Balagbag, one of San Jose del Monte\'s most majestic mountain peaks.',
                features: ['Challenging trails', 'Panoramic views', 'Rich biodiversity']
            },
            {
                name: 'Burong Falls',
                category: 'Waterfall',
                type: 'waterfall',
                image: '💧',
                description: 'Discover the natural wonder of Burong Falls, a hidden gem nestled in lush landscapes.',
                features: ['Crystal-clear waters', 'Natural pools', 'Lush surroundings']
            },
            {
                name: 'City Oval & People\'s Park',
                category: 'Park',
                type: 'park',
                image: '🏞️',
                description: 'Enjoy recreational activities at City Oval & People\'s Park in San Jose del Monte.',
                features: ['Sports facilities', 'Playground', 'Jogging paths']
            },
            {
                name: 'Our Lady of Lourdes',
                category: 'Religious',
                type: 'religious',
                image: '⛪',
                description: 'Find spiritual solace and architectural beauty at Our Lady of Lourdes.',
                features: ['Beautiful architecture', 'Peaceful atmosphere', 'Cultural significance']
            },
            {
                name: 'Abes Farm',
                category: 'Farm',
                type: 'farm',
                image: '🌾',
                description: 'Experience sustainable agriculture and rural life at Abes Farm.',
                features: ['Organic farming', 'Fresh produce', 'Educational tours']
            }
        ];

        // Detail page mapping
        const spotDetailPages = {
            'Mt. Balagbag': '../tourist-detail/mt-balagbag.php',
            'Burong Falls': '../tourist-detail/burong-falls.php',
            'City Oval & People\'s Park': '../tourist-detail/city-ovals-peoples-park.php',
            'Our Lady of Lourdes': '../tourist-detail/our-lady-of-lourdes.php',
            'Abes Farm': '../tourist-detail/abes-farm.php'
        };

        window.addEventListener('DOMContentLoaded', function() {
            displaySavedSpots();
            initUserProfileDropdown();
        });

        function displaySavedSpots() {
            const savedSpots = JSON.parse(localStorage.getItem('savedSpots')) || [];
            const container = document.getElementById('savedSpotsList');
            
            if (savedSpots.length === 0) {
                container.innerHTML = `
                    <div class="empty-state">
                        <div class="empty-icon">
                            <span class="material-icons-outlined">favorite_border</span>
                        </div>
                        <h3>No Saved Tourist Spots Yet</h3>
                        <p>Start exploring and click the heart icon on tourist spots to save them here for quick access!</p>
                        <div class="centered-actions">
                            <button class="btn-hero" onclick="window.location.href='user-tourist-spots.php'">
                                <span class="material-icons-outlined">explore</span>
                                Explore Tourist Spots
                            </button>
                        </div>
                    </div>
                `;
                return;
            }
            
            const savedSpotData = touristSpots.filter(spot => savedSpots.includes(spot.name));
            container.innerHTML = savedSpotData.map(spot => createSpotCard(spot)).join('');
        }

        function createSpotCard(spot) {
            const detailPage = spotDetailPages[spot.name] || '#';
            return `
                <div class="spot-card" onclick="window.location.href='${detailPage}'">
                    <div class="spot-card-image">
                        <span style="font-size: 48px;">${spot.image}</span>
                    </div>
                    <div class="spot-card-content">
                        <div class="spot-card-header">
                            <h3 class="spot-card-title">${spot.name}</h3>
                            <span class="spot-card-category">${spot.category}</span>
                        </div>
                        <p class="spot-card-description">${spot.description}</p>
                        <div class="spot-card-features">
                            ${spot.features.map(feature => `<span class="feature-tag">${feature}</span>`).join('')}
                        </div>
                        <div class="spot-card-actions">
                            <button class="btn-remove" onclick="event.stopPropagation(); removeSavedSpot('${spot.name}')">
                                <span class="material-icons-outlined">favorite</span>
                                Remove
                            </button>
                            <a href="${detailPage}" class="btn-view" onclick="event.stopPropagation()">
                                View Details
                            </a>
                        </div>
                    </div>
                </div>
            `;
        }

        function removeSavedSpot(spotName) {
            const savedSpots = JSON.parse(localStorage.getItem('savedSpots')) || [];
            const index = savedSpots.indexOf(spotName);
            
            if (index > -1) {
                savedSpots.splice(index, 1);
                localStorage.setItem('savedSpots', JSON.stringify(savedSpots));
                displaySavedSpots();
                showNotification('Removed from saved spots', 'info');
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

        // User Profile Dropdown
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
                    showLogoutConfirmation();
                });
            }
        }

        function showLogoutConfirmation() {
            const modal = document.createElement('div');
            modal.className = 'modal-overlay';
            modal.innerHTML = `
                <div class="modal-content logout-modal">
                    <div class="modal-header">
                        <h2>Sign Out</h2>
                        <button class="close-modal" onclick="this.closest('.modal-overlay').remove()">
                            <span class="material-icons-outlined">close</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="logout-message">
                            <div class="logout-icon">
                                <span class="material-icons-outlined">logout</span>
                            </div>
                            <h3>Confirm Sign Out</h3>
                            <p>Are you sure you want to sign out of your account?</p>
                        </div>
                        <div class="modal-actions">
                            <button class="btn-cancel" onclick="document.querySelector('.modal-overlay').remove()">
                                <span class="material-icons-outlined">close</span>
                                Cancel
                            </button>
                            <button class="btn-confirm-logout" onclick="confirmLogout()">
                                <span class="material-icons-outlined">logout</span>
                                Sign Out
                            </button>
                        </div>
                    </div>
                </div>
            `;
            document.body.appendChild(modal);
            setTimeout(() => modal.classList.add('show'), 10);
        }

        function confirmLogout() {
            const modal = document.querySelector('.modal-overlay');
            if (modal) {
                modal.remove();
            }
            window.location.href = '../log-in/logout.php';
        }
    </script>

    <!-- Preferences Modal -->
    <?php include __DIR__ . '/../components/preferences-modal.php'; ?>
</body>
</html>
