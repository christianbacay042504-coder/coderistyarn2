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
    <title>Saved Tours - SJDM Tours</title>
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

            .profile-name {
                display: inline-block;
                font-size: 14px;
            }

            .dropdown-menu {
                width: 280px;
            }
        }
    </style>
    
    <!-- Logout Modal Styles -->
    <style>
        .modal-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.85);
            backdrop-filter: blur(8px);
            z-index: 9999;
            animation: fadeIn 0.3s ease;
        }

        .modal-overlay.show {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        .modal-content {
            background: white;
            border-radius: 24px;
            width: 90%;
            max-width: 600px;
            max-height: 90vh;
            overflow: hidden;
            box-shadow: 
                0 32px 64px rgba(0, 0, 0, 0.25),
                0 16px 32px rgba(0, 0, 0, 0.15),
                0 8px 16px rgba(0, 0, 0, 0.1);
            position: relative;
            border: 1px solid rgba(255, 255, 255, 0.2);
            animation: slideIn 0.3s ease;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-50px) scale(0.9);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 24px 32px 24px 32px;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: white;
            position: relative;
        }

        .modal-header h2 {
            margin: 0;
            font-size: 1.5rem;
            font-weight: 700;
            color: white;
        }

        .close-modal {
            background: rgba(255, 255, 255, 0.2);
            border: none;
            border-radius: 12px;
            width: 36px;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            color: white;
            font-size: 1.2rem;
            transition: all 0.3s ease;
        }

        .close-modal:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: scale(1.1);
        }

        .close-modal .material-icons-outlined {
            font-size: 20px;
        }

        .modal-body {
            padding: 32px;
            max-height: calc(90vh - 120px);
            overflow-y: auto;
        }

        /* Logout Modal Styles */
        .logout-message {
            text-align: center;
            margin-bottom: 20px;
        }

        .logout-icon {
            width: 48px;
            height: 48px;
            background: var(--danger);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 16px;
        }

        .logout-message h3 {
            margin: 16px 0 8px;
            color: var(--text-primary);
        }

        .logout-message p {
            color: var(--text-secondary);
            margin-bottom: 24px;
        }

        .modal-actions {
            display: flex;
            gap: 12px;
            justify-content: center;
        }

        .btn-cancel,
        .btn-confirm-logout {
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .btn-cancel {
            background: var(--gray-100);
            color: var(--text-secondary);
        }

        .btn-cancel:hover {
            background: var(--gray-200);
        }

        .btn-confirm-logout {
            background: var(--danger);
            color: white;
        }

        .btn-confirm-logout:hover {
            background: #dc2626;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .modal-content {
                width: 95%;
                margin: 20px;
            }
            
            .modal-header {
                padding: 20px 24px 20px 24px;
            }
            
            .modal-body {
                padding: 24px;
            }
        }
    </style>
</head>
<body>
    <!-- MAIN CONTENT -->
    <main class="main-content full-width">
        <header class="main-header">
            <div class="header-left">
                <h1 id="pageTitle">Saved Tours</h1>
               
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
                            <a href="user-saved-tours.php" class="dropdown-item">
                                <span class="material-icons-outlined">favorite</span>
                                <span>Saved Tours</span>
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
            <h2 class="section-title">Saved Tours</h2>
            
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

            <div id="savedToursList" class="guides-grid"></div>
        </div>
    </main>

    <script>
        // Tour Guide Data (same as in script.js)
        const guides = [
            {
                id: 1,
                name: "Rico Mendoza",
                photo: "👨‍🏫",
                specialty: "Mt. Balagbag Hiking Expert",
                category: "mountain",
                description: "Certified mountain guide with 10 years of experience leading Mt. Balagbag expeditions. Safety-first approach with extensive knowledge of local trails.",
                areas: "Mt. Balagbag, Tuntong Falls, Mountain trails",
                rating: 5.0,
                reviewCount: 127,
                priceRange: "₱2,000 - ₱3,500 per day",
                languages: "English, Tagalog",
                experience: "10 years",
                verified: true
            },
            {
                id: 2,
                name: "Anna Marie Santos",
                photo: "👩‍💼",
                specialty: "Nature & Waterfall Tours",
                category: "nature",
                description: "Expert nature guide specializing in Kaytitinga Falls and forest eco-tours. Passionate about sustainable tourism and local ecology.",
                areas: "Kaytitinga Falls, Forest trails, Eco-tourism sites",
                rating: 4.9,
                reviewCount: 89,
                priceRange: "₱2,500 - ₱4,000 per day",
                languages: "English, Tagalog",
                experience: "7 years",
                verified: true
            },
            {
                id: 3,
                name: "Father Jose Reyes",
                photo: "🙏",
                specialty: "Religious & Pilgrimage Tours",
                category: "religious",
                description: "Former parish coordinator offering spiritual tours to Grotto of Our Lady of Lourdes and Padre Pio shrine with historical insights.",
                areas: "Grotto of Our Lady of Lourdes, Padre Pio Mountain, Churches",
                rating: 4.8,
                reviewCount: 156,
                priceRange: "₱1,500 - ₱2,500 per day",
                languages: "English, Tagalog, Spanish",
                experience: "15 years",
                verified: true
            },
            {
                id: 4,
                name: "Michael Cruz",
                photo: "🚵‍♂️",
                specialty: "Adventure & Extreme Sports",
                category: "adventure",
                description: "Adrenaline enthusiast offering adventure packages including hiking, rappelling, and team building activities at Paradise Adventure Camp.",
                areas: "Paradise Adventure Camp, Mt. Balagbag, Extreme trails",
                rating: 4.9,
                reviewCount: 94,
                priceRange: "₱3,000 - ₱5,000 per day",
                languages: "English, Tagalog",
                experience: "8 years",
                verified: true
            },
            {
                id: 5,
                name: "Linda Bautista",
                photo: "👩‍🌾",
                specialty: "Farm & Food Tours",
                category: "food",
                description: "Local farmer and culinary tour guide showcasing SJDM's agricultural heritage, orchid farms, and authentic Bulacan cuisine.",
                areas: "Orchid Garden, Pineapple Farms, Local restaurants, Markets",
                rating: 4.8,
                reviewCount: 72,
                priceRange: "₱2,000 - ₱3,500 per day",
                languages: "English, Tagalog",
                experience: "6 years",
                verified: true
            },
            {
                id: 6,
                name: "Carlos Villanueva",
                photo: "🏙️",
                specialty: "City & Cultural Tours",
                category: "city",
                description: "Urban guide and local historian showcasing SJDM's transformation from rural town to modern city while preserving cultural heritage.",
                areas: "City proper, Malls, Historical sites, Urban attractions",
                rating: 4.7,
                reviewCount: 68,
                priceRange: "₱1,800 - ₱3,000 per day",
                languages: "English, Tagalog",
                experience: "5 years",
                verified: true
            }
        ];
    </script>
    <script src="script.js"></script>
    <script>
        window.addEventListener('DOMContentLoaded', function() {
            displayFavorites();
            initUserProfileDropdown();
            // updateUserInterface is defined in script.js to handle UI updates
            if (typeof updateUserInterface === 'function') {
                updateUserInterface();
            }
        });

        // ========== USER PROFILE DROPDOWN ==========
        function initUserProfileDropdown() {
            const profileDropdown = document.querySelector('.user-profile-dropdown');
            const profileTrigger = document.querySelector('.profile-trigger');
            const dropdownMenu = document.querySelector('.dropdown-menu');
            const logoutLink = document.querySelector('[href="logout.php"]');

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

        // Show logout confirmation modal
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

        // Confirm and execute logout
        function confirmLogout() {
            // Remove modal
            const modal = document.querySelector('.modal-overlay');
            if (modal) {
                modal.remove();
            }

            // Redirect to logout script
            window.location.href = 'logout.php';
        }

        function displayFavorites() {
            const favorites = JSON.parse(localStorage.getItem('favorites')) || [];
            const container = document.getElementById('savedToursList');
            
            if (favorites.length === 0) {
                container.innerHTML = `
                    <div class="modal-empty-state">
                        <div class="empty-icon">
                            <span class="material-icons-outlined">favorite_border</span>
                        </div>
                        <h3>No Saved Tours Yet</h3>
                        <p>Save your favorite tour guides and destinations to quickly access them later. Start exploring and click the heart icon!</p>
                        <div class="centered-actions">
                            <button class="btn-hero" onclick="window.location.href='user-guides.php'">
                                <span class="material-icons-outlined">explore</span>
                                Browse Tour Guides
                            </button>
                        </div>
                    </div>
                `;
                return;
            }
            
            const favoriteGuides = guides.filter(g => favorites.includes(g.id));
            container.innerHTML = favoriteGuides.map(g => createGuideCard(g)).join('');
        }

        function createGuideCard(g) {
            const isFav = isFavorite(g.id);
            return `
                <div class="guide-card" onclick="window.location.href='index.php#profile-${g.id}'">
                    <div class="guide-photo">
                        ${g.photo}
                        <button class="favorite-btn ${isFav ? 'active' : ''}" onclick="event.stopPropagation(); toggleFavorite(${g.id})">
                            <span class="material-icons-outlined">${isFav ? 'favorite' : 'favorite_border'}</span>
                        </button>
                        ${g.verified ? '<span class="verified-badge"><span class="material-icons-outlined">verified</span></span>' : ''}
                    </div>
                    <div class="guide-info">
                        <div class="guide-name">${g.name}</div>
                        <span class="guide-specialty">${g.specialty}</span>
                        <p class="guide-description">${g.description}</p>
                        <div class="guide-meta">
                            <div class="meta-item">
                                <span class="material-icons-outlined">place</span>
                                <span>${g.areas.split(',')[0]}</span>
                            </div>
                            <div class="meta-item">
                                <span class="material-icons-outlined">language</span>
                                <span>${g.languages}</span>
                            </div>
                            <div class="meta-item">
                                <span class="material-icons-outlined">work</span>
                                <span>${g.experience}</span>
                            </div>
                            <div class="rating-display">
                                <span class="material-icons-outlined">star</span>
                                <span class="rating-value">${g.rating.toFixed(1)}</span>
                                <span class="review-count">(${g.reviewCount})</span>
                            </div>
                        </div>
                        <div class="guide-footer">
                            <span class="price-tag">${g.priceRange}</span>
                            <button class="btn-view-profile">View Profile</button>
                        </div>
                    </div>
                </div>
            `;
        }

        function isFavorite(guideId) {
            const favorites = JSON.parse(localStorage.getItem('favorites')) || [];
            return favorites.includes(guideId);
        }

        function toggleFavorite(guideId) {
            const favorites = JSON.parse(localStorage.getItem('favorites')) || [];
            const index = favorites.indexOf(guideId);
            
            if (index > -1) {
                favorites.splice(index, 1);
                showNotification('Removed from favorites', 'info');
            } else {
                favorites.push(guideId);
                showNotification('Added to favorites', 'success');
            }
            
            localStorage.setItem('favorites', JSON.stringify(favorites));
            displayFavorites();
        }
    </script>
</body>
</html>