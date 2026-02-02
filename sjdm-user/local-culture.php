<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Local Culture - San Jose del Monte Bulacan</title>
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
            <a class="nav-item active" href="javascript:void(0)">
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
            <h1>Local Culture</h1>
            <div class="search-bar">
                <span class="material-icons-outlined">search</span>
                <input type="text" placeholder="Search cultural information...">
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
            <h2 class="section-title">SJDM Local Culture & Heritage</h2>
            <div class="info-cards">
                <div class="info-card">
                    <h3>🎭 City Identity</h3>
                    <ul>
                        <li>"Balcony of Metropolis" nickname</li>
                        <li>Highly Urbanized City since 2001</li>
                        <li>Gateway to Northern Luzon</li>
                        <li>Blend of urban and rural life</li>
                        <li>Growing residential communities</li>
                    </ul>
                </div>
                <div class="info-card">
                    <h3>🌾 Local Industries</h3>
                    <ul>
                        <li>Orchid cultivation</li>
                        <li>Pineapple farming</li>
                        <li>Real estate development</li>
                        <li>Small-scale agriculture</li>
                        <li>Tourism and hospitality</li>
                    </ul>
                </div>
                <div class="info-card">
                    <h3>👥 Indigenous Culture</h3>
                    <ul>
                        <li>Dumagat communities</li>
                        <li>Forest conservation practices</li>
                        <li>Traditional weaving</li>
                        <li>Nature-based livelihood</li>
                        <li>Cultural preservation efforts</li>
                    </ul>
                </div>
                <div class="info-card">
                    <h3>🍲 Local Cuisine</h3>
                    <ul>
                        <li>Bulacan dishes - Ensaymada, Chicharon</li>
                        <li>Fresh seafood specialties</li>
                        <li>Farm-to-table organic produce</li>
                        <li>Local bakeries and delicacies</li>
                        <li>Street food culture</li>
                    </ul>
                </div>
                <div class="info-card">
                    <h3>🎊 Festivals</h3>
                    <ul>
                        <li>City Foundation Day celebrations</li>
                        <li>Barangay fiestas throughout the year</li>
                        <li>Religious processions</li>
                        <li>Community cultural events</li>
                        <li>Modern urban festivals</li>
                    </ul>
                </div>
                <div class="info-card">
                    <h3>🏙️ Community Life</h3>
                    <ul>
                        <li>Mall culture - SM & Starmall</li>
                        <li>Growing residential communities</li>
                        <li>Family-oriented city</li>
                        <li>Active church communities</li>
                        <li>Sports and recreation focus</li>
                    </ul>
                </div>
            </div>
        </div>
    </main>

    <script src="script.js"></script>
</body>
</html>