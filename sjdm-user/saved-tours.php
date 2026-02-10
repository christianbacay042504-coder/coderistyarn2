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
            <h1>Saved Tours</h1>
            <div class="search-bar">
                <span class="material-icons-outlined">search</span>
                <input type="text" placeholder="Search saved tours...">
            </div>
            <div class="header-actions">
                
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
            // updateUserInterface is defined in script.js to handle UI updates
            if (typeof updateUserInterface === 'function') {
                updateUserInterface();
            }
        });

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