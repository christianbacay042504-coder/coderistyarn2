<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Travel Tips - San Jose del Monte Bulacan</title>
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
            <a class="nav-item active" href="javascript:void(0)">
                <span class="material-icons-outlined">tips_and_updates</span>
                <span>Travel Tips</span>
            </a>
        </nav>
    </aside>

    <!-- MAIN CONTENT -->
    <main class="main-content">
        <header class="main-header">
            <h1>Travel Tips</h1>
            <div class="search-bar">
                <span class="material-icons-outlined">search</span>
                <input type="text" placeholder="Search travel tips...">
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
            <h2 class="section-title">Travel Tips for SJDM Visitors</h2>
            <div class="info-cards">
                <div class="info-card">
                    <h3>🚗 Getting to SJDM</h3>
                    <ul>
                        <li>30-45 minutes from Metro Manila</li>
                        <li>Via NLEX - Bocaue Exit</li>
                        <li>Buses from Cubao to Bulacan</li>
                        <li>Private car recommended for tours</li>
                        <li>Ride-sharing apps available</li>
                    </ul>
                </div>
                <div class="info-card">
                    <h3>⛰️ For Mountain Hikers</h3>
                    <ul>
                        <li>Start early (5-6 AM recommended)</li>
                        <li>Bring at least 2L water per person</li>
                        <li>Wear proper hiking shoes</li>
                        <li>Apply sunscreen and insect repellent</li>
                        <li>Hire local guides for safety</li>
                    </ul>
                </div>
                <div class="info-card">
                    <h3>💧 Visiting Waterfalls</h3>
                    <ul>
                        <li>Wear water shoes or trekking sandals</li>
                        <li>Trails can be muddy and slippery</li>
                        <li>Bring plastic bags for electronics</li>
                        <li>Swimming allowed in designated areas</li>
                        <li>Follow Leave No Trace principles</li>
                    </ul>
                </div>
                <div class="info-card">
                    <h3>💰 Budget Planning</h3>
                    <ul>
                        <li>Tour guide fees: ₱1,500-3,500/day</li>
                        <li>Entrance fees: ₱50-200 per site</li>
                        <li>Meals: ₱150-300 per person</li>
                        <li>Transportation: ₱500-1,000</li>
                        <li>Total budget: ₱2,500-5,000/person</li>
                    </ul>
                </div>
                <div class="info-card">
                    <h3>🌤️ Best Time to Visit</h3>
                    <ul>
                        <li>November to February - cool weather</li>
                        <li>March to May - summer, hot but clear</li>
                        <li>Avoid July-September rainy season</li>
                        <li>Weekdays less crowded</li>
                        <li>Early morning for mountain hikes</li>
                    </ul>
                </div>
                <div class="info-card">
                    <h3>🎒 What to Bring</h3>
                    <ul>
                        <li>Comfortable hiking attire</li>
                        <li>Extra clothes & towel</li>
                        <li>Sunscreen & insect repellent</li>
                        <li>First aid kit & personal meds</li>
                        <li>Reusable water bottle & snacks</li>
                    </ul>
                </div>
                <div class="info-card">
                    <h3>📱 Local Tips</h3>
                    <ul>
                        <li>Mobile signal available in most areas</li>
                        <li>ATMs available in malls & town centers</li>
                        <li>Bring cash for entrance fees</li>
                        <li>Respect local communities</li>
                        <li>Ask permission before taking photos</li>
                    </ul>
                </div>
                <div class="info-card">
                    <h3>⚠️ Safety Reminders</h3>
                    <ul>
                        <li>Always book with licensed guides</li>
                        <li>Check weather before hiking</li>
                        <li>Stay on marked trails</li>
                        <li>Don't swim during heavy rain</li>
                        <li>Emergency hotline: 911</li>
                    </ul>
                </div>
            </div>
        </div>
    </main>

    <script src="script.js"></script>
</body>
</html>