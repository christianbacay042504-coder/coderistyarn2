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
            <h2 class="section-title">Meet Our Local Expert Tour Guides</h2>
            
            <!-- Filter Section -->
            <div class="filter-section">
                <div class="filter-container">
                    <div class="filter-buttons">
                        <button class="filter-btn active" data-filter="all">All Guides</button>
                        <button class="filter-btn" data-filter="mountain">Mountain Hiking</button>
                        <button class="filter-btn" data-filter="nature">Nature & Waterfall</button>
                        <button class="filter-btn" data-filter="religious">Religious Tours</button>
                        <button class="filter-btn" data-filter="adventure">Adventure</button>
                        <button class="filter-btn" data-filter="food">Food Tours</button>
                        <button class="filter-btn" data-filter="city">City Tours</button>
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
                <!-- Guide 1 -->
                <div class="guide-card" data-guide-id="1" data-category="mountain">
                    <div class="guide-photo">
                        <img src="https://via.placeholder.com/400x300/2c5f2d/ffffff?text=Marc+Santos" alt="Guide Marc Santos">
                        <button class="favorite-btn" data-guide-id="1">
                            <span class="material-icons-outlined">favorite_border</span>
                        </button>
                        <div class="verified-badge">
                            <span class="material-icons-outlined">verified</span>
                            Verified Guide
                        </div>
                    </div>
                    <div class="guide-info">
                        <h3 class="guide-name">Marc Santos</h3>
                        <span class="guide-specialty">Mountain & Adventure Guide</span>
                        <p class="guide-description">Local mountaineer with 10+ years experience guiding hikers to Mt. Balagbag. Knows every trail like the back of his hand.</p>
                        <div class="guide-meta">
                            <div class="meta-item">
                                <span class="material-icons-outlined">location_on</span>
                                San Jose del Monte Native
                            </div>
                            <div class="meta-item">
                                <span class="material-icons-outlined">schedule</span>
                                8+ years experience
                            </div>
                            <div class="meta-item">
                                <span class="material-icons-outlined">translate</span>
                                English, Tagalog
                            </div>
                        </div>
                        <div class="rating-display">
                            <span class="material-icons-outlined">star</span>
                            <span class="material-icons-outlined">star</span>
                            <span class="material-icons-outlined">star</span>
                            <span class="material-icons-outlined">star</span>
                            <span class="material-icons-outlined">star_half</span>
                            <span class="rating-value">4.7</span>
                            <span class="review-count">(128 reviews)</span>
                        </div>
                        <div class="guide-footer">
                            <div class="price-tag">₱2,500 / day</div>
                            <button class="btn-view-profile" onclick="viewGuideProfile(1)">View Profile</button>
                        </div>
                    </div>
                </div>

                <!-- Guide 2 -->
                <div class="guide-card" data-guide-id="2" data-category="nature">
                    <div class="guide-photo">
                        <img src="https://via.placeholder.com/400x300/2c5f2d/ffffff?text=Anna+Reyes" alt="Guide Anna Reyes">
                        <button class="favorite-btn" data-guide-id="2">
                            <span class="material-icons-outlined">favorite_border</span>
                        </button>
                        <div class="verified-badge">
                            <span class="material-icons-outlined">verified</span>
                            Verified Guide
                        </div>
                    </div>
                    <div class="guide-info">
                        <h3 class="guide-name">Anna Reyes</h3>
                        <span class="guide-specialty">Cultural & Food Tour Specialist</span>
                        <p class="guide-description">Food enthusiast and history buff. Expert in local cuisine and cultural heritage of SJDM. Knows all the best food spots!</p>
                        <div class="guide-meta">
                            <div class="meta-item">
                                <span class="material-icons-outlined">location_on</span>
                                Sto. Cristo Resident
                            </div>
                            <div class="meta-item">
                                <span class="material-icons-outlined">schedule</span>
                                5+ years experience
                            </div>
                            <div class="meta-item">
                                <span class="material-icons-outlined">translate</span>
                                English, Tagalog, Spanish
                            </div>
                        </div>
                        <div class="rating-display">
                            <span class="material-icons-outlined">star</span>
                            <span class="material-icons-outlined">star</span>
                            <span class="material-icons-outlined">star</span>
                            <span class="material-icons-outlined">star</span>
                            <span class="material-icons-outlined">star</span>
                            <span class="rating-value">5.0</span>
                            <span class="review-count">(94 reviews)</span>
                        </div>
                        <div class="guide-footer">
                            <div class="price-tag">₱2,000 / day</div>
                            <button class="btn-view-profile" onclick="viewGuideProfile(2)">View Profile</button>
                        </div>
                    </div>
                </div>

                <!-- Guide 3 -->
                <div class="guide-card" data-guide-id="3" data-category="nature">
                    <div class="guide-photo">
                        <img src="https://via.placeholder.com/400x300/2c5f2d/ffffff?text=Juan+Dela+Cruz" alt="Guide Juan Dela Cruz">
                        <button class="favorite-btn" data-guide-id="3">
                            <span class="material-icons-outlined">favorite_border</span>
                        </button>
                        <div class="verified-badge">
                            <span class="material-icons-outlined">verified</span>
                            Verified Guide
                        </div>
                    </div>
                    <div class="guide-info">
                        <h3 class="guide-name">Juan Dela Cruz</h3>
                        <span class="guide-specialty">Waterfall & Nature Expert</span>
                        <p class="guide-description">Adventure guide specializing in waterfall treks. Certified first aider and photography enthusiast.</p>
                        <div class="guide-meta">
                            <div class="meta-item">
                                <span class="material-icons-outlined">location_on</span>
                                Kaypian Resident
                            </div>
                            <div class="meta-item">
                                <span class="material-icons-outlined">schedule</span>
                                6+ years experience
                            </div>
                            <div class="meta-item">
                                <span class="material-icons-outlined">translate</span>
                                English, Tagalog
                            </div>
                        </div>
                        <div class="rating-display">
                            <span class="material-icons-outlined">star</span>
                            <span class="material-icons-outlined">star</span>
                            <span class="material-icons-outlined">star</span>
                            <span class="material-icons-outlined">star</span>
                            <span class="material-icons-outlined">star</span>
                            <span class="rating-value">4.9</span>
                            <span class="review-count">(156 reviews)</span>
                        </div>
                        <div class="guide-footer">
                            <div class="price-tag">₱2,300 / day</div>
                            <button class="btn-view-profile" onclick="viewGuideProfile(3)">View Profile</button>
                        </div>
                    </div>
                </div>

                <!-- Guide 4 -->
                <div class="guide-card" data-guide-id="4" data-category="religious">
                    <div class="guide-photo">
                        <img src="https://via.placeholder.com/400x300/2c5f2d/ffffff?text=Maria+Gonzales" alt="Guide Maria Gonzales">
                        <button class="favorite-btn" data-guide-id="4">
                            <span class="material-icons-outlined">favorite_border</span>
                        </button>
                        <div class="verified-badge">
                            <span class="material-icons-outlined">verified</span>
                            Verified Guide
                        </div>
                    </div>
                    <div class="guide-info">
                        <h3 class="guide-name">Maria Gonzales</h3>
                        <span class="guide-specialty">Family & Religious Tours</span>
                        <p class="guide-description">Specializes in family-friendly tours and religious pilgrimages. Patient and knowledgeable about local history.</p>
                        <div class="guide-meta">
                            <div class="meta-item">
                                <span class="material-icons-outlined">location_on</span>
                                Graceville Resident
                            </div>
                            <div class="meta-item">
                                <span class="material-icons-outlined">schedule</span>
                                7+ years experience
                            </div>
                            <div class="meta-item">
                                <span class="material-icons-outlined">translate</span>
                                English, Tagalog, Ilocano
                            </div>
                        </div>
                        <div class="rating-display">
                            <span class="material-icons-outlined">star</span>
                            <span class="material-icons-outlined">star</span>
                            <span class="material-icons-outlined">star</span>
                            <span class="material-icons-outlined">star</span>
                            <span class="material-icons-outlined">star_half</span>
                            <span class="rating-value">4.8</span>
                            <span class="review-count">(203 reviews)</span>
                        </div>
                        <div class="guide-footer">
                            <div class="price-tag">₱1,800 / day</div>
                            <button class="btn-view-profile" onclick="viewGuideProfile(4)">View Profile</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script src="script.js"></script>
</body>
</html>