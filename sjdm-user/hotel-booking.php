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
                            <option value="resort">Resorts</option>
                            <option value="hotel">Hotels</option>
                            <option value="farm">Farm Stays</option>
                            <option value="budget">Budget Hotels</option>
                            <option value="event">Event Venues</option>
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
                <!-- Nature Area Hotels -->
                <!-- Pacific Waves Resort -->
                <div class="travelry-card" data-category="resort" data-nearby="nature" data-price="mid">
                    <div class="card-image">
                        <img src="https://images.unsplash.com/photo-1566073771259-6a8506099945?q=80&w=2070&auto=format&fit=crop" alt="Pacific Waves Resort">
                        <div class="card-badge">
                            <span class="material-icons-outlined">pool</span>
                            Resort
                        </div>
                        <div class="distance-badge">
                            <span class="material-icons-outlined">location_on</span>
                            20 min to Mt. Balagbag
                        </div>
                    </div>
                    <div class="card-content">
                        <div class="card-date">
                            <span class="material-icons-outlined">schedule</span>
                            Check-in: 2:00 PM • Check-out: 12:00 PM
                        </div>
                        <h3 class="card-title">Pacific Waves Resort</h3>
                        <span class="card-category">Best for Nature Tours</span>
                        <p class="card-description">Closest resort to eastern SJDM nature trails. Perfect base for Mt. Balagbag hikers and waterfall explorers.</p>
                        <div class="card-stats">
                            <div class="stat-item">
                                <span class="stat-label">Distance</span>
                                <span class="stat-value">2.5 km</span>
                            </div>
                            <div class="stat-item">
                                <span class="stat-label">Price</span>
                                <span class="stat-value">₱1,800/night</span>
                            </div>
                            <div class="stat-item">
                                <span class="stat-label">Rating</span>
                                <span class="stat-value">4.2/5</span>
                            </div>
                        </div>
                        <div class="card-features">
                            <span class="feature-tag">Swimming Pool</span>
                            <span class="feature-tag">Free Parking</span>
                            <span class="feature-tag">Group Rooms</span>
                        </div>
                        <button class="card-button" onclick="showBookingModal('Pacific Waves Resort')">
                            Book Now
                        </button>
                    </div>
                </div>
            
                <!-- Grotto Vista Resort -->
                <div class="travelry-card" data-category="resort" data-nearby="nature" data-price="premium">
                    <div class="card-image">
                        <img src="https://images.unsplash.com/photo-1584132915807-fd1f5fbc078f?q=80&w=2070&auto=format&fit=crop" alt="Grotto Vista Resort">
                        <div class="card-badge">
                            <span class="material-icons-outlined">house</span>
                            Large Resort
                        </div>
                        <div class="distance-badge">
                            <span class="material-icons-outlined">location_on</span>
                            25 min to waterfalls
                        </div>
                    </div>
                    <div class="card-content">
                        <div class="card-date">
                            <span class="material-icons-outlined">schedule</span>
                            Check-in: 3:00 PM • Check-out: 11:00 AM
                        </div>
                        <h3 class="card-title">Grotto Vista Resort</h3>
                        <span class="card-category">Family-Friendly Resort</span>
                        <p class="card-description">Large resort with excellent facilities. Ideal for families and groups exploring eastern SJDM nature spots.</p>
                        <div class="card-stats">
                            <div class="stat-item">
                                <span class="stat-label">Distance</span>
                                <span class="stat-value">3 km</span>
                            </div>
                            <div class="stat-item">
                                <span class="stat-label">Price</span>
                                <span class="stat-value">₱3,500/night</span>
                            </div>
                            <div class="stat-item">
                                <span class="stat-label">Rating</span>
                                <span class="stat-value">4.5/5</span>
                            </div>
                        </div>
                        <div class="card-features">
                            <span class="feature-tag">Multiple Pools</span>
                            <span class="feature-tag">Event Spaces</span>
                            <span class="feature-tag">Restaurant</span>
                        </div>
                        <button class="card-button" onclick="showBookingModal('Grotto Vista Resort')">
                            Book Now
                        </button>
                    </div>
                </div>

                <!-- Los Arcos De Hermano -->
                <div class="travelry-card" data-category="resort" data-nearby="nature" data-price="mid">
                    <div class="card-image">
                        <img src="https://images.unsplash.com/photo-1520250497591-112f2f40a3f4?q=80&w=2070&auto=format&fit=crop" alt="Los Arcos De Hermano">
                        <div class="card-badge">
                            <span class="material-icons-outlined">groups</span>
                            Group Resort
                        </div>
                        <div class="distance-badge">
                            <span class="material-icons-outlined">location_on</span>
                            30 min to trailheads
                        </div>
                    </div>
                    <div class="card-content">
                        <div class="card-date">
                            <span class="material-icons-outlined">schedule</span>
                            Check-in: 2:00 PM • Check-out: 12:00 PM
                        </div>
                        <h3 class="card-title">Los Arcos De Hermano</h3>
                        <span class="card-category">Group Accommodation</span>
                        <p class="card-description">Resort with easy road access, perfect for hiking groups and outdoor enthusiasts. Spacious grounds.</p>
                        <div class="card-stats">
                            <div class="stat-item">
                                <span class="stat-label">Capacity</span>
                                <span class="stat-value">50+ guests</span>
                            </div>
                            <div class="stat-item">
                                <span class="stat-label">Price</span>
                                <span class="stat-value">₱2,200/night</span>
                            </div>
                            <div class="stat-item">
                                <span class="stat-label">Rating</span>
                                <span class="stat-value">4.3/5</span>
                            </div>
                        </div>
                        <div class="card-features">
                            <span class="feature-tag">Large Grounds</span>
                            <span class="feature-tag">Team Building</span>
                            <span class="feature-tag">BBQ Areas</span>
                        </div>
                        <button class="card-button" onclick="showBookingModal('Los Arcos De Hermano')">
                            Book Now
                        </button>
                    </div>
                </div>

                <!-- Farm Area Hotels -->
                <!-- The Hillside Farm Resort -->
                <div class="travelry-card" data-category="farm" data-nearby="farm" data-price="mid">
                    <div class="card-image">
                        <img src="https://images.unsplash.com/photo-1600566752355-35792bedcfea?q=80&w=2070&auto=format&fit=crop" alt="The Hillside Farm Resort">
                        <div class="card-badge">
                            <span class="material-icons-outlined">agriculture</span>
                            Farm Stay
                        </div>
                        <div class="distance-badge">
                            <span class="material-icons-outlined">location_on</span>
                            15 min to Paradise Hill Farm
                        </div>
                    </div>
                    <div class="card-content">
                        <div class="card-date">
                            <span class="material-icons-outlined">schedule</span>
                            Check-in: 2:00 PM • Check-out: 12:00 PM
                        </div>
                        <h3 class="card-title">The Hillside Farm Resort</h3>
                        <span class="card-category">Authentic Farm Experience</span>
                        <p class="card-description">Immerse yourself in countryside living. Perfect for visitors exploring SJDM's farm attractions.</p>
                        <div class="card-stats">
                            <div class="stat-item">
                                <span class="stat-label">Farm Size</span>
                                <span class="stat-value">5 hectares</span>
                            </div>
                            <div class="stat-item">
                                <span class="stat-label">Price</span>
                                <span class="stat-value">₱2,500/night</span>
                            </div>
                            <div class="stat-item">
                                <span class="stat-label">Rating</span>
                                <span class="stat-value">4.6/5</span>
                            </div>
                        </div>
                        <div class="card-features">
                            <span class="feature-tag">Farm Animals</span>
                            <span class="feature-tag">Organic Garden</span>
                            <span class="feature-tag">Country Views</span>
                        </div>
                        <button class="card-button" onclick="showBookingModal('The Hillside Farm Resort')">
                            Book Now
                        </button>
                    </div>
                </div>
                    
                <!-- Tierra Fontana 12 Waves Resort -->
                <div class="travelry-card" data-category="resort" data-nearby="farm" data-price="premium">
                    <div class="card-image">
                        <img src="https://images.unsplash.com/photo-1540541338287-41700207dee6?q=80&w=2070&auto=format&fit=crop" alt="Tierra Fontana Resort">
                        <div class="card-badge">
                            <span class="material-icons-outlined">waves</span>
                            Water Park Resort
                        </div>
                        <div class="distance-badge">
                            <span class="material-icons-outlined">location_on</span>
                            On Paradise Farm Street
                        </div>
                    </div>
                    <div class="card-content">
                        <div class="card-date">
                            <span class="material-icons-outlined">schedule</span>
                            Check-in: 3:00 PM • Check-out: 11:00 AM
                        </div>
                        <h3 class="card-title">Tierra Fontana 12 Waves</h3>
                        <span class="card-category">Farm Area Luxury</span>
                        <p class="card-description">Resort with water park facilities located in the farming district. Perfect for families visiting farm attractions.</p>
                        <div class="card-stats">
                            <div class="stat-item">
                                <span class="stat-label">Waves</span>
                                <span class="stat-value">12 pools</span>
                            </div>
                            <div class="stat-item">
                                <span class="stat-label">Price</span>
                                <span class="stat-value">₱4,000/night</span>
                            </div>
                            <div class="stat-item">
                                <span class="stat-label">Rating</span>
                                <span class="stat-value">4.7/5</span>
                            </div>
                        </div>
                        <div class="card-features">
                            <span class="feature-tag">Water Slides</span>
                            <span class="feature-tag">Cottages</span>
                            <span class="feature-tag">Food Court</span>
                        </div>
                        <button class="card-button" onclick="showBookingModal('Tierra Fontana 12 Waves')">
                            Book Now
                        </button>
                    </div>
                </div>

                <!-- City Area Hotels -->
                <!-- Hotel Savano -->
                <div class="travelry-card" data-category="hotel" data-nearby="park" data-price="mid">
                    <div class="card-image">
                        <img src="https://images.unsplash.com/photo-1566665797739-1674de7a421a?q=80&w=2070&auto=format&fit=crop" alt="Hotel Savano">
                        <div class="card-badge">
                            <span class="material-icons-outlined">business</span>
                            City Hotel
                        </div>
                        <div class="distance-badge">
                            <span class="material-icons-outlined">location_on</span>
                            5 min to City Oval
                        </div>
                    </div>
                    <div class="card-content">
                        <div class="card-date">
                            <span class="material-icons-outlined">schedule</span>
                            Check-in: 2:00 PM • Check-out: 12:00 PM
                        </div>
                        <h3 class="card-title">Hotel Savano</h3>
                        <span class="card-category">Central City Location</span>
                        <p class="card-description">Modern hotel in the heart of SJDM. Walking distance to People's Park, City Oval, and The Rising Heart.</p>
                        <div class="card-stats">
                            <div class="stat-item">
                                <span class="stat-label">Location</span>
                                <span class="stat-value">City Center</span>
                            </div>
                            <div class="stat-item">
                                <span class="stat-label">Price</span>
                                <span class="stat-value">₱2,800/night</span>
                            </div>
                            <div class="stat-item">
                                <span class="stat-label">Rating</span>
                                <span class="stat-value">4.4/5</span>
                            </div>
                        </div>
                        <div class="card-features">
                            <span class="feature-tag">WiFi</span>
                            <span class="feature-tag">AC Rooms</span>
                            <span class="feature-tag">Restaurant</span>
                        </div>
                        <button class="card-button" onclick="showBookingModal('Hotel Savano')">
                            Book Now
                        </button>
                    </div>
                </div>

                <!-- Reddoorz @ GaDi Hotel -->
                <div class="travelry-card" data-category="budget" data-nearby="park" data-price="budget">
                    <div class="card-image">
                        <img src="https://images.unsplash.com/photo-1611892440504-42a792e24d32?q=80&w=2070&auto=format&fit=crop" alt="Reddoorz Hotel">
                        <div class="card-badge">
                            <span class="material-icons-outlined">savings</span>
                            Budget Hotel
                        </div>
                        <div class="distance-badge">
                            <span class="material-icons-outlined">location_on</span>
                            10 min to city attractions
                        </div>
                    </div>
                    <div class="card-content">
                        <div class="card-date">
                            <span class="material-icons-outlined">schedule</span>
                            Check-in: 2:00 PM • Check-out: 12:00 PM
                        </div>
                        <h3 class="card-title">Reddoorz @ GaDi Hotel</h3>
                        <span class="card-category">Affordable City Stay</span>
                        <p class="card-description">Clean, comfortable budget accommodation near city attractions. Great value for money.</p>
                        <div class="card-stats">
                            <div class="stat-item">
                                <span class="stat-label">Best For</span>
                                <span class="stat-value">Solo Travelers</span>
                            </div>
                            <div class="stat-item">
                                <span class="stat-label">Price</span>
                                <span class="stat-value">₱1,200/night</span>
                            </div>
                            <div class="stat-item">
                                <span class="stat-label">Rating</span>
                                <span class="stat-value">4.1/5</span>
                            </div>
                        </div>
                        <div class="card-features">
                            <span class="feature-tag">24/7 Front Desk</span>
                            <span class="feature-tag">Security</span>
                            <span class="feature-tag">Basic Amenities</span>
                        </div>
                        <button class="card-button" onclick="showBookingModal('Reddoorz @ GaDi Hotel')">
                            Book Now
                        </button>
                    </div>
                </div>

                <!-- Religious Area Hotels -->
                <!-- Hotel Sogo San Jose Del Monte -->
                <div class="travelry-card" data-category="budget" data-nearby="religious" data-price="budget">
                    <div class="card-image">
                        <img src="https://images.unsplash.com/photo-1590490360182-c33d57733427?q=80&w=2074&auto=format&fit=crop" alt="Hotel Sogo">
                        <div class="card-badge">
                            <span class="material-icons-outlined">night_shelter</span>
                            Budget Chain
                        </div>
                        <div class="distance-badge">
                            <span class="material-icons-outlined">location_on</span>
                            15 min to Grotto
                        </div>
                    </div>
                    <div class="card-content">
                        <div class="card-date">
                            <span class="material-icons-outlined">schedule</span>
                            Check-in: 2:00 PM • Check-out: 12:00 PM
                        </div>
                        <h3 class="card-title">Hotel Sogo SJDM</h3>
                        <span class="card-category">Convenient Location</span>
                        <p class="card-description">Reliable budget hotel chain with easy access to religious sites and city amenities.</p>
                        <div class="card-stats">
                            <div class="stat-item">
                                <span class="stat-label">Accessibility</span>
                                <span class="stat-value">Easy Transport</span>
                            </div>
                            <div class="stat-item">
                                <span class="stat-label">Price</span>
                                <span class="stat-value">₱1,500/night</span>
                            </div>
                            <div class="stat-item">
                                <span class="stat-label">Rating</span>
                                <span class="stat-value">4.0/5</span>
                            </div>
                        </div>
                        <div class="card-features">
                            <span class="feature-tag">24/7 Service</span>
                            <span class="feature-tag">Parking</span>
                            <span class="feature-tag">Clean Rooms</span>
                        </div>
                        <button class="card-button" onclick="showBookingModal('Hotel Sogo SJDM')">
                            Book Now
                        </button>
                    </div>
                </div>

                <!-- Hotel Turista San Jose -->
                <div class="travelry-card" data-category="hotel" data-nearby="religious" data-price="mid">
                    <div class="card-image">
                        <img src="https://images.unsplash.com/photo-1584132915807-fd1f5fbc078f?q=80&w=2070&auto=format&fit=crop" alt="Hotel Turista">
                        <div class="card-badge">
                            <span class="material-icons-outlined">local_hotel</span>
                            Mid-Range Hotel
                        </div>
                        <div class="distance-badge">
                            <span class="material-icons-outlined">location_on</span>
                            20 min to Padre Pio
                        </div>
                    </div>
                    <div class="card-content">
                        <div class="card-date">
                            <span class="material-icons-outlined">schedule</span>
                            Check-in: 2:00 PM • Check-out: 12:00 PM
                        </div>
                        <h3 class="card-title">Hotel Turista San Jose</h3>
                        <span class="card-category">Pilgrim Accommodation</span>
                        <p class="card-description">Comfortable hotel catering to religious tourists visiting SJDM's pilgrimage sites.</p>
                        <div class="card-stats">
                            <div class="stat-item">
                                <span class="stat-label">Room Types</span>
                                <span class="stat-value">Family Rooms</span>
                            </div>
                            <div class="stat-item">
                                <span class="stat-label">Price</span>
                                <span class="stat-value">₱2,200/night</span>
                            </div>
                            <div class="stat-item">
                                <span class="stat-label">Rating</span>
                                <span class="stat-value">4.2/5</span>
                            </div>
                        </div>
                        <div class="card-features">
                            <span class="feature-tag">Family-Friendly</span>
                            <span class="feature-tag">Tour Assistance</span>
                            <span class="feature-tag">Breakfast</span>
                        </div>
                        <button class="card-button" onclick="showBookingModal('Hotel Turista San Jose')">
                            Book Now
                        </button>
                    </div>
                </div>

                <!-- Other Notable Stays -->
                <!-- La Cecilia Resort -->
                <div class="travelry-card" data-category="resort" data-nearby="nature" data-price="mid">
                    <div class="card-image">
                        <img src="https://images.unsplash.com/photo-1611892440504-42a792e24d32?q=80&w=2070&auto=format&fit=crop" alt="La Cecilia Resort">
                        <div class="card-badge">
                            <span class="material-icons-outlined">spa</span>
                            Relaxation Resort
                        </div>
                    </div>
                    <div class="card-content">
                        <div class="card-date">
                            <span class="material-icons-outlined">schedule</span>
                            Check-in: 2:00 PM • Check-out: 12:00 PM
                        </div>
                        <h3 class="card-title">La Cecilia Resort</h3>
                        <span class="card-category">Tranquil Escape</span>
                        <p class="card-description">Smaller resort with relaxed vibe, perfect for unwinding after a day of exploring.</p>
                        <div class="card-stats">
                            <div class="stat-item">
                                <span class="stat-label">Atmosphere</span>
                                <span class="stat-value">Peaceful</span>
                            </div>
                            <div class="stat-item">
                                <span class="stat-label">Price</span>
                                <span class="stat-value">₱2,800/night</span>
                            </div>
                            <div class="stat-item">
                                <span class="stat-label">Rating</span>
                                <span class="stat-value">4.3/5</span>
                            </div>
                        </div>
                        <div class="card-features">
                            <span class="feature-tag">Garden</span>
                            <span class="feature-tag">Quiet Area</span>
                            <span class="feature-tag">Personalized Service</span>
                        </div>
                        <button class="card-button" onclick="showBookingModal('La Cecilia Resort')">
                            Book Now
                        </button>
                    </div>
                </div>

                <!-- Casa Regina Resorts -->
                <div class="travelry-card" data-category="event" data-nearby="park" data-price="premium">
                    <div class="card-image">
                        <img src="https://images.unsplash.com/photo-1542314831-068cd1dbfeeb?q=80&w=2070&auto=format&fit=crop" alt="Casa Regina Resort">
                        <div class="card-badge">
                            <span class="material-icons-outlined">celebration</span>
                            Event Resort
                        </div>
                    </div>
                    <div class="card-content">
                        <div class="card-date">
                            <span class="material-icons-outlined">schedule</span>
                            Check-in: 3:00 PM • Check-out: 11:00 AM
                        </div>
                        <h3 class="card-title">Casa Regina Resorts</h3>
                        <span class="card-category">Events & Celebrations</span>
                        <p class="card-description">Resort-style stay with event spaces, perfect for weddings, reunions, and group celebrations.</p>
                        <div class="card-stats">
                            <div class="stat-item">
                                <span class="stat-label">Event Capacity</span>
                                <span class="stat-value">200+ guests</span>
                            </div>
                            <div class="stat-item">
                                <span class="stat-label">Price</span>
                                <span class="stat-value">₱5,000/night</span>
                            </div>
                            <div class="stat-item">
                                <span class="stat-label">Rating</span>
                                <span class="stat-value">4.8/5</span>
                            </div>
                        </div>
                        <div class="card-features">
                            <span class="feature-tag">Event Halls</span>
                            <span class="feature-tag">Catering</span>
                            <span class="feature-tag">Photo Spots</span>
                        </div>
                        <button class="card-button" onclick="showBookingModal('Casa Regina Resorts')">
                            Book Now
                        </button>
                    </div>
                </div>
            </div>

            <!-- Hotel Booking Tips -->
            <div class="info-section" style="background: white; padding: 30px; border-radius: 20px; margin-top: 40px;">
                <h3>💡 Hotel Booking Tips for SJDM Visitors</h3>
                <div class="info-cards" style="margin-top: 20px;">
                    <div class="info-card">
                        <h4>📍 Location Strategy</h4>
                        <ul>
                            <li><strong>Nature Tours:</strong> Stay in eastern SJDM resorts (Pacific Waves, Grotto Vista)</li>
                            <li><strong>Farm Tours:</strong> Choose farm stays or resorts on Paradise Farm Street</li>
                            <li><strong>City Tours:</strong> Central hotels like Hotel Savano or budget options</li>
                            <li><strong>Religious Tours:</strong> Hotels with easy transport to Grotto & Padre Pio</li>
                        </ul>
                    </div>
                    <div class="info-card">
                        <h4>💰 Price Ranges</h4>
                        <ul>
                            <li><strong>Budget:</strong> ₱800 - ₱1,500/night (Reddoorz, Hotel Sogo)</li>
                            <li><strong>Mid-Range:</strong> ₱1,500 - ₱3,000/night (Most resorts & hotels)</li>
                            <li><strong>Premium:</strong> ₱3,000+/night (Larger resorts, event venues)</li>
                        </ul>
                    </div>
                    <div class="info-card">
                        <h4>📞 Booking Advice</h4>
                        <ul>
                            <li>Book 1-2 weeks ahead for weekend stays</li>
                            <li>Confirm transportation arrangements</li>
                            <li>Check resort/hotel policies on group sizes</li>
                            <li>Ask about tour guide coordination services</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script src="script.js"></script>
</body>
</html>