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
                        <div class="profile-avatar">U</div>
                        <span class="material-icons-outlined">expand_more</span>
                    </button>
                    <div class="dropdown-menu" id="profileMenu">
                        <div class="profile-info">
                            <div class="profile-avatar large">U</div>
                            <div class="profile-details">
                                <h3>User Name</h3>
                                <p>user@example.com</p>
                            </div>
                        </div>
                        <div class="dropdown-divider"></div>
                        <a href="javascript:void(0)" class="dropdown-item">
                            <span class="material-icons-outlined">account_circle</span>
                            <span>My Account</span>
                        </a>
                        <a href="javascript:void(0)" class="dropdown-item">
                            <span class="material-icons-outlined">history</span>
                            <span>Booking History</span>
                        </a>
                        <a href="javascript:void(0)" class="dropdown-item">
                            <span class="material-icons-outlined">favorite_border</span>
                            <span>Saved Tours</span>
                        </a>
                        <div class="dropdown-divider"></div>
                        <a href="javascript:void(0)" class="dropdown-item">
                            <span class="material-icons-outlined">settings</span>
                            <span>Settings</span>
                        </a>
                        <a href="javascript:void(0)" class="dropdown-item">
                            <span class="material-icons-outlined">help_outline</span>
                            <span>Help & Support</span>
                        </a>
                        <a href="javascript:void(0)" class="dropdown-item">
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
                    <div class="weekday" id="currentWeekday">Today</div>
                    <div class="month-year" id="currentDate">December 2024</div>
                </div>
                <div class="weather-info">
                    <span class="material-icons-outlined"></span>
                    <span class="temperature">28°C</span>
                    <span class="weather-label">Sunny</span>
                </div>
            </div>

            <!-- Filters -->
            <div class="travelry-filters">
                <div class="filter-row">
                    <div class="filter-group">
                        <label>Category</label>
                        <select class="filter-select" id="categoryFilter">
                            <option value="all">All Categories</option>
                            <option value="nature">Nature & Waterfalls</option>
                            <option value="farm">Farms & Eco-Tourism</option>
                            <option value="park">Parks & Recreation</option>
                            <option value="religious">Religious Sites</option>
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
                        <label>Duration</label>
                        <select class="filter-select" id="durationFilter">
                            <option value="all">All Durations</option>
                            <option value="1-2">1-2 Hours</option>
                            <option value="2-4">2-4 Hours</option>
                            <option value="4+">4+ Hours</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Tourist Spots Grid -->
            <div class="travelry-grid" id="spotsGrid">
                <!-- Nature Spots -->
                <!-- Mt. Balagbag -->
                <div class="travelry-card" data-category="nature" data-activity="moderate" data-duration="4-6">
                    <div class="card-image">
                        <img src="https://images.unsplash.com/photo-1551632811-561732d1e306?q=80&w=2070&auto=format&fit=crop" alt="Mt. Balagbag">
                        <div class="card-badge">
                            <span class="material-icons-outlined">landscape</span>
                            Mountain
                        </div>
                    </div>
                    <div class="card-content">
                        <div class="card-date">
                            <span class="material-icons-outlined">schedule</span>
                            4-6 hours tour
                        </div>
                        <h3 class="card-title">Mt. Balagbag</h3>
                        <span class="card-category">Mountain Hiking</span>
                        <div class="card-stats">
                            <div class="stat-item">
                                <span class="stat-label">Temperature</span>
                                <span class="stat-value">+21°C</span>
                            </div>
                            <div class="stat-item">
                                <span class="stat-label">Elevation</span>
                                <span class="stat-value">777 MASL</span>
                            </div>
                            <div class="stat-item">
                                <span class="stat-label">Difficulty</span>
                                <span class="stat-value">Moderate</span>
                            </div>
                        </div>
                        <div class="card-buttons">
                            <button class="card-button" onclick="window.location.href='/coderistyarn2/tourist-detail/mt-balagbag.php'">
                                View Details
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Kaytitinga Falls -->
                <div class="travelry-card" data-category="nature" data-activity="moderate" data-duration="3-5">
                    <div class="card-image">
                        <img src="https://images.unsplash.com/photo-1433086173841-718858a6022c?q=80&w=1887&auto=format&fit=crop" alt="Kaytitinga Falls">
                        <div class="card-badge">
                            <span class="material-icons-outlined">waterfall</span>
                            Waterfall
                        </div>
                    </div>
                    <div class="card-content">
                        <div class="card-date">
                            <span class="material-icons-outlined">schedule</span>
                            3-5 hours tour
                        </div>
                        <h3 class="card-title">Kaytitinga Falls</h3>
                        <span class="card-category">Nature Trek</span>
                        <div class="card-stats">
                            <div class="stat-item">
                                <span class="stat-label">Temperature</span>
                                <span class="stat-value">+23°C</span>
                            </div>
                            <div class="stat-item">
                                <span class="stat-label">Elevation</span>
                                <span class="stat-value">350 MASL</span>
                            </div>
                            <div class="stat-item">
                                <span class="stat-label">Difficulty</span>
                                <span class="stat-value">Moderate</span>
                            </div>
                        </div>
                        <div class="card-buttons">
                            <button class="card-button" onclick="window.location.href='/coderistyarn2/tourist-detail/kaytitinga-falls.php'">
                                View Details
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Tungtong Falls -->
                <div class="travelry-card" data-category="nature" data-activity="moderate" data-duration="3-4">
                    <div class="card-image">
                        <img src="https://images.unsplash.com/photo-1509316785289-025f5b846b35?q=80&w=2076&auto=format&fit=crop" alt="Tungtong Falls">
                        <div class="card-badge">
                            <span class="material-icons-outlined">waterfall</span>
                            Waterfall
                        </div>
                    </div>
                    <div class="card-content">
                        <div class="card-date">
                            <span class="material-icons-outlined">schedule</span>
                            3-4 hours tour
                        </div>
                        <h3 class="card-title">Tungtong Falls</h3>
                        <span class="card-category">Hidden Gem</span>
                        <div class="card-stats">
                            <div class="stat-item">
                                <span class="stat-label">Temperature</span>
                                <span class="stat-value">+22°C</span>
                            </div>
                            <div class="stat-item">
                                <span class="stat-label">Elevation</span>
                                <span class="stat-value">300 MASL</span>
                            </div>
                            <div class="stat-item">
                                <span class="stat-label">Difficulty</span>
                                <span class="stat-value">Moderate</span>
                            </div>
                        </div>
                        <div class="card-buttons">
                            <button class="card-button" onclick="window.location.href='/coderistyarn2/tourist-detail/tungtong.php'">
                                View Details
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Burong Falls -->
                <div class="travelry-card" data-category="nature" data-activity="difficult" data-duration="5-7">
                    <div class="card-image">
                        <img src="https://images.unsplash.com/photo-1511884642898-4c92249e20b6?q=80&w=2070&auto=format&fit=crop" alt="Burong Falls">
                        <div class="card-badge">
                            <span class="material-icons-outlined">waterfall</span>
                            Waterfall
                        </div>
                    </div>
                    <div class="card-content">
                        <div class="card-date">
                            <span class="material-icons-outlined">schedule</span>
                            5-7 hours tour
                        </div>
                        <h3 class="card-title">Burong Falls</h3>
                        <span class="card-category">Adventure Trek</span>
                        <div class="card-stats">
                            <div class="stat-item">
                                <span class="stat-label">Temperature</span>
                                <span class="stat-value">+20°C</span>
                            </div>
                            <div class="stat-item">
                                <span class="stat-label">Elevation</span>
                                <span class="stat-value">400 MASL</span>
                            </div>
                            <div class="stat-item">
                                <span class="stat-label">Difficulty</span>
                                <span class="stat-value">Difficult</span>
                            </div>
                        </div>
                        <div class="card-buttons">
                            <button class="card-button" onclick="window.location.href='/coderistyarn2/tourist-detail/burong-falls.php'">
                                View Details
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Otso-Otso Falls -->
                <div class="travelry-card" data-category="nature" data-activity="moderate" data-duration="4-5">
                    <div class="card-image">
                        <img src="https://images.unsplash.com/photo-1530041539828-114de669390e?q=80&w=2070&auto=format&fit=crop" alt="Otso-Otso Falls">
                        <div class="card-badge">
                            <span class="material-icons-outlined">waterfall</span>
                            Waterfall
                        </div>
                    </div>
                    <div class="card-content">
                        <div class="card-date">
                            <span class="material-icons-outlined">schedule</span>
                            4-5 hours tour
                        </div>
                        <h3 class="card-title">Otso-Otso Falls</h3>
                        <span class="card-category">Nature Adventure</span>
                        <div class="card-stats">
                            <div class="stat-item">
                                <span class="stat-label">Temperature</span>
                                <span class="stat-value">+22°C</span>
                            </div>
                            <div class="stat-item">
                                <span class="stat-label">Elevation</span>
                                <span class="stat-value">320 MASL</span>
                            </div>
                            <div class="stat-item">
                                <span class="stat-label">Difficulty</span>
                                <span class="stat-value">Moderate</span>
                            </div>
                        </div>
                        <div class="card-buttons">
                            <button class="card-button" onclick="window.location.href='/coderistyarn2/tourist-detail/otso-otso-falls.php'">
                                View Details
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Farm Spots -->
                <!-- Paradise Hill Farm -->
                <div class="travelry-card" data-category="farm" data-activity="easy" data-duration="3-4">
                    <div class="card-image">
                        <img src="https://images.unsplash.com/photo-1464454709131-ffd692591ee5?q=80&w=2070&auto=format&fit=crop" alt="Paradise Hill Farm">
                        <div class="card-badge">
                            <span class="material-icons-outlined">agriculture</span>
                            Farm
                        </div>
                    </div>
                    <div class="card-content">
                        <div class="card-date">
                            <span class="material-icons-outlined">schedule</span>
                            3-4 hours tour
                        </div>
                        <h3 class="card-title">Paradise Hill Farm</h3>
                        <span class="card-category">Eco-Tourism</span>
                        <div class="card-stats">
                            <div class="stat-item">
                                <span class="stat-label">Temperature</span>
                                <span class="stat-value">+26°C</span>
                            </div>
                            <div class="stat-item">
                                <span class="stat-label">Elevation</span>
                                <span class="stat-value">150 MASL</span>
                            </div>
                            <div class="stat-item">
                                <span class="stat-label">Difficulty</span>
                                <span class="stat-value">Easy</span>
                            </div>
                        </div>
                        <div class="card-buttons">
                            <button class="card-button" onclick="window.location.href='/coderistyarn2/tourist-detail/paradise-hill-farm.php'">
                                View Details
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Abes Farm -->
                <div class="travelry-card" data-category="farm" data-activity="easy" data-duration="2-3">
                    <div class="card-image">
                        <img src="https://images.unsplash.com/photo-1500382017468-9049fed747ef?q=80&w=2052&auto=format&fit=crop" alt="Abes Farm">
                        <div class="card-badge">
                            <span class="material-icons-outlined">agriculture</span>
                            Farm
                        </div>
                    </div>
                    <div class="card-content">
                        <div class="card-date">
                            <span class="material-icons-outlined">schedule</span>
                            2-3 hours tour
                        </div>
                        <h3 class="card-title">Abes Farm</h3>
                        <span class="card-category">Organic Farming</span>
                        <div class="card-stats">
                            <div class="stat-item">
                                <span class="stat-label">Temperature</span>
                                <span class="stat-value">+26°C</span>
                            </div>
                            <div class="stat-item">
                                <span class="stat-label">Elevation</span>
                                <span class="stat-value">120 MASL</span>
                            </div>
                            <div class="stat-item">
                                <span class="stat-label">Difficulty</span>
                                <span class="stat-value">Easy</span>
                            </div>
                        </div>
                        <div class="card-buttons">
                            <button class="card-button" onclick="window.location.href='/coderistyarn2/tourist-detail/abes-farm.php'">
                                View Details
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Parks & Recreation -->
                <!-- The Rising Heart -->
                <div class="travelry-card" data-category="park" data-activity="easy" data-duration="1-2">
                    <div class="card-image">
                        <img src="https://images.unsplash.com/photo-1519003722824-194d4455a60e?q=80&w=2070&auto=format&fit=crop" alt="The Rising Heart">
                        <div class="card-badge">
                            <span class="material-icons-outlined">park</span>
                            Park
                        </div>
                    </div>
                    <div class="card-content">
                        <div class="card-date">
                            <span class="material-icons-outlined">schedule</span>
                            1-2 hours tour
                        </div>
                        <h3 class="card-title">The Rising Heart</h3>
                        <span class="card-category">Monument & Park</span>
                        <div class="card-stats">
                            <div class="stat-item">
                                <span class="stat-label">Temperature</span>
                                <span class="stat-value">+28°C</span>
                            </div>
                            <div class="stat-item">
                                <span class="stat-label">Elevation</span>
                                <span class="stat-value">50 MASL</span>
                            </div>
                            <div class="stat-item">
                                <span class="stat-label">Difficulty</span>
                                <span class="stat-value">Easy</span>
                            </div>
                        </div>
                        <div class="card-buttons">
                            <button class="card-button" onclick="window.location.href='/coderistyarn2/tourist-detail/the-rising-heart.php'">
                                View Details
                            </button>
                        </div>
                    </div>
                </div>

                <!-- City Oval and People's Park -->
                <div class="travelry-card" data-category="park" data-activity="easy" data-duration="2-3">
                    <div class="card-image">
                        <img src="https://images.unsplash.com/photo-1578662996442-48f60103fc96?q=80&w=2070&auto=format&fit=crop" alt="City Oval and People's Park">
                        <div class="card-badge">
                            <span class="material-icons-outlined">sports</span>
                            Sports
                        </div>
                    </div>
                    <div class="card-content">
                        <div class="card-date">
                            <span class="material-icons-outlined">schedule</span>
                            2-3 hours tour
                        </div>
                        <h3 class="card-title">City Oval & People's Park</h3>
                        <span class="card-category">Recreation Area</span>
                        <div class="card-stats">
                            <div class="stat-item">
                                <span class="stat-label">Temperature</span>
                                <span class="stat-value">+28°C</span>
                            </div>
                            <div class="stat-item">
                                <span class="stat-label">Elevation</span>
                                <span class="stat-value">50 MASL</span>
                            </div>
                            <div class="stat-item">
                                <span class="stat-label">Difficulty</span>
                                <span class="stat-value">Easy</span>
                            </div>
                        </div>
                        <div class="card-buttons">
                            <button class="card-button" onclick="window.location.href='/coderistyarn2/tourist-detail/city-ovals-peoples-park.php'">
                                View Details
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Religious Sites -->
                <!-- Grotto of Our Lady of Lourdes -->
                <div class="travelry-card" data-category="religious" data-activity="easy" data-duration="2-3">
                    <div class="card-image">
                        <img src="https://images.unsplash.com/photo-1548013146-72479768bbaa?q=80&w=2070&auto=format&fit=crop" alt="Grotto Complex">
                        <div class="card-badge">
                            <span class="material-icons-outlined">church</span>
                            Religious
                        </div>
                    </div>
                    <div class="card-content">
                        <div class="card-date">
                            <span class="material-icons-outlined">schedule</span>
                            2-3 hours tour
                        </div>
                        <h3 class="card-title">Grotto of Our Lady of Lourdes</h3>
                        <span class="card-category">Spiritual Sanctuary</span>
                        <div class="card-stats">
                            <div class="stat-item">
                                <span class="stat-label">Temperature</span>
                                <span class="stat-value">+26°C</span>
                            </div>
                            <div class="stat-item">
                                <span class="stat-label">Elevation</span>
                                <span class="stat-value">150 MASL</span>
                            </div>
                            <div class="stat-item">
                                <span class="stat-label">Difficulty</span>
                                <span class="stat-value">Easy</span>
                            </div>
                        </div>
                        <div class="card-buttons">
                            <button class="card-button" onclick="window.location.href='/coderistyarn2/tourist-detail/grotto-of-our-lady-of-lourdes.php'">
                                View Details
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Padre Pio Mountain -->
                <div class="travelry-card" data-category="religious" data-activity="easy" data-duration="2-3">
                    <div class="card-image">
                        <img src="https://images.unsplash.com/photo-1519810755548-39cd217da494?q=80&w=1888&auto=format&fit=crop" alt="Padre Pio Mountain">
                        <div class="card-badge">
                            <span class="material-icons-outlined">church</span>
                            Religious
                        </div>
                    </div>
                    <div class="card-content">
                        <div class="card-date">
                            <span class="material-icons-outlined">schedule</span>
                            2-3 hours tour
                        </div>
                        <h3 class="card-title">Padre Pio Mountain of Healing</h3>
                        <span class="card-category">Pilgrimage Site</span>
                        <div class="card-stats">
                            <div class="stat-item">
                                <span class="stat-label">Temperature</span>
                                <span class="stat-value">+24°C</span>
                            </div>
                            <div class="stat-item">
                                <span class="stat-label">Elevation</span>
                                <span class="stat-value">200 MASL</span>
                            </div>
                            <div class="stat-item">
                                <span class="stat-label">Difficulty</span>
                                <span class="stat-value">Easy</span>
                            </div>
                        </div>
                        <div class="card-buttons">
                            <button class="card-button" onclick="window.location.href='/coderistyarn2/tourist-detail/padre-pio.php'">
                                View Details
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script src="script.js"></script>
</body>
</html>