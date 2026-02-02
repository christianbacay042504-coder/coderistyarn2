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
            <!-- HOME PAGE -->
            <div class="hero">
                <h1>Welcome to San Jose del Monte, Bulacan</h1>
                <p>The Balcony of Metropolis - Where Nature Meets Progress</p>
                <button class="btn-hero" onclick="window.location.href='user-guides.php'">Find Your Guide</button>
            </div>

            <h2 class="section-title">Featured Destinations</h2>
            <div class="destinations-grid">
                <div class="destination-card">
                    <div class="destination-img">
                        <img src="https://via.placeholder.com/400x300/2c5f2d/ffffff?text=Mt.+Balagbag" alt="Mt. Balagbag">
                    </div>
                    <div class="destination-content">
                        <h3>Mt. Balagbag</h3>
                        <p>Known as the "Mt. Pulag of Bulacan," this 777-meter peak offers stunning views of Metro Manila and surrounding mountains. Perfect for beginner hikers!</p>
                    </div>
                </div>
                <div class="destination-card">
                    <div class="destination-img">
                        <img src="https://via.placeholder.com/400x300/2c5f2d/ffffff?text=Kaytitinga+Falls" alt="Kaytitinga Falls">
                    </div>
                    <div class="destination-content">
                        <h3>Kaytitinga Falls</h3>
                        <p>A hidden gem with three-level cascading falls nestled in the forest. One hour trek through pristine nature awaits adventure seekers.</p>
                    </div>
                </div>
                <div class="destination-card">
                    <div class="destination-img">
                        <img src="https://via.placeholder.com/400x300/2c5f2d/ffffff?text=Grotto+Lourdes" alt="Grotto of Our Lady of Lourdes">
                    </div>
                    <div class="destination-content">
                        <h3>Grotto of Our Lady of Lourdes</h3>
                        <p>A spiritual sanctuary replica of the French basilica. Beautiful compound with meditation areas and breathtaking views from the second floor.</p>
                    </div>
                </div>
                <div class="destination-card">
                    <div class="destination-img">
                        <img src="https://via.placeholder.com/400x300/2c5f2d/ffffff?text=Padre+Pio" alt="Padre Pio Mountain of Healing">
                    </div>
                    <div class="destination-content">
                        <h3>Padre Pio Mountain of Healing</h3>
                        <p>Features a giant statue of St. Padre Pio on the hill. Open 24/7 for prayer, meditation, and peaceful reflection with panoramic city views.</p>
                    </div>
                </div>
            </div>

            <h2 class="section-title">Why Visit San Jose del Monte?</h2>
            <div class="stats-grid">
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
            </div>
        </div>
    </main>

    <script src="script.js"></script>
</body>
</html>
