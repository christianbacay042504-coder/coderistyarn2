<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Savano Hotel - San Jose del Monte, Bulacan</title>
  <style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
    
    :root {
        --primary-green: #2c5f2d;
        --secondary-green: #1e4220;
        --accent-green: #10b981;
        --light-green: #97bc62;
        --pure-white: #ffffff;
        --light-bg: #f8fcf9;
    }
    
    body {
        background-color: var(--light-bg);
        color: #333;
        line-height: 1.6;
        display: flex;
        flex-direction: column;
        min-height: 100vh;
    }
    
    .container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 20px;
        flex: 1;
    }
    
    /* BACK BUTTON STYLES */
    .back-button-container {
        margin-bottom: 20px;
    }
    
    .btn-back {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: var(--secondary-green);
        color: var(--pure-white);
        padding: 12px 20px;
        border-radius: 8px;
        text-decoration: none;
        font-weight: 500;
        transition: 0.3s;
        border: none;
        cursor: pointer;
        font-size: 16px;
    }
    
    .btn-back:hover {
        background: var(--primary-green);
        transform: translateX(-5px);
    }
    
    .btn-back i {
        font-size: 18px;
    }
    
    header {
        background: linear-gradient(135deg, var(--secondary-green), var(--primary-green));
        color: var(--pure-white);
        padding: 40px 20px;
        text-align: center;
        border-radius: 15px;
        margin-bottom: 30px;
    }
    
    h1 {
        font-size: 2.8rem;
        margin-bottom: 10px;
    }
    
    .tagline {
        font-size: 1.2rem;
        opacity: 0.9;
    }
    
    .hotel-info {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 30px;
        margin-bottom: 40px;
    }
    
    .main-image {
        width: 100%;
        height: 400px;
        object-fit: cover;
        border-radius: 15px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    }
    
    .details-box {
        background: var(--pure-white);
        padding: 25px;
        border-radius: 15px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.05);
    }
    
    .details-box h2 {
        color: var(--primary-green);
        margin-bottom: 20px;
        border-bottom: 2px solid var(--light-green);
        padding-bottom: 10px;
    }
    
    .detail-item {
        margin-bottom: 15px;
        display: flex;
        align-items: flex-start;
    }
    
    .detail-item i {
        color: var(--accent-green);
        margin-right: 10px;
        margin-top: 3px;
    }
    
    .amenities {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 20px;
        margin-top: 30px;
    }
    
    .amenity-card {
        background: var(--pure-white);
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        text-align: center;
        transition: all 0.3s;
        border: 1px solid transparent;
    }
    
    .amenity-card:hover {
        transform: translateY(-5px);
        border: 1px solid var(--light-green);
    }
    
    .amenity-card i {
        font-size: 2rem;
        color: var(--primary-green);
        margin-bottom: 10px;
    }
    
    /* FOOTER STYLES */
    footer {
        background: var(--secondary-green);
        color: var(--pure-white);
        padding: 40px 20px;
        margin-top: 50px;
        border-top-left-radius: 15px;
        border-top-right-radius: 15px;
    }
    
    .footer-container {
        max-width: 1200px;
        margin: 0 auto;
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 30px;
    }
    
    .footer-section h3 {
        color: var(--pure-white);
        margin-bottom: 20px;
        font-size: 1.3rem;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .footer-section h3 i {
        font-size: 1.2rem;
    }
    
    .contact-info {
        list-style: none;
        padding: 0;
    }
    
    .contact-info li {
        margin-bottom: 15px;
        display: flex;
        align-items: flex-start;
        gap: 10px;
    }
    
    .contact-info li i {
        color: var(--accent-green);
        margin-top: 3px;
    }
    
    .social-links {
        display: flex;
        gap: 15px;
        margin-top: 20px;
    }
    
    .social-links a {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 40px;
        height: 40px;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 50%;
        color: var(--pure-white);
        text-decoration: none;
        transition: 0.3s;
    }
    
    .social-links a:hover {
        background: var(--accent-green);
        transform: translateY(-3px);
    }
    
    .footer-bottom {
        text-align: center;
        margin-top: 30px;
        padding-top: 20px;
        border-top: 1px solid rgba(255, 255, 255, 0.1);
        font-size: 0.9rem;
        opacity: 0.8;
    }
    
    .btn {
        display: inline-block;
        background: var(--accent-green);
        color: var(--pure-white);
        padding: 12px 25px;
        border-radius: 8px;
        text-decoration: none;
        font-weight: bold;
        margin-top: 15px;
        transition: 0.3s;
        border: none;
        cursor: pointer;
    }
    
    .btn:hover {
        background: var(--primary-green);
    }
    
    /* GOOGLE MAPS STYLES */
    .map-container {
        margin: 40px 0;
    }
    
    .map-container h2 {
        color: var(--primary-green);
        margin-bottom: 20px;
        text-align: center;
        font-size: 1.8rem;
    }
    
    .map-wrapper {
        background: var(--pure-white);
        padding: 25px;
        border-radius: 15px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        margin-bottom: 20px;
    }
    
    #map {
        width: 100%;
        height: 450px;
        border-radius: 10px;
        border: 1px solid #ddd;
    }
    
    .location-details {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
        margin-top: 20px;
    }
    
    .location-info {
        background: rgba(151, 188, 98, 0.1);
        padding: 20px;
        border-radius: 10px;
    }
    
    .location-info h3 {
        color: var(--primary-green);
        margin-bottom: 15px;
    }
    
    .location-info ul {
        list-style: none;
        padding-left: 0;
    }
    
    .location-info li {
        padding: 8px 0;
        border-bottom: 1px solid rgba(151, 188, 98, 0.3);
    }
    
    .location-info li:last-child {
        border-bottom: none;
    }
    
    .location-info i {
        color: var(--primary-green);
        margin-right: 10px;
        width: 20px;
    }
    
    .map-actions {
        display: flex;
        gap: 15px;
        margin-top: 20px;
        flex-wrap: wrap;
    }
    
    .map-btn {
        padding: 10px 20px;
        background: var(--primary-green);
        color: var(--pure-white);
        border: none;
        border-radius: 8px;
        cursor: pointer;
        font-weight: 500;
        transition: 0.3s;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    
    .map-btn:hover {
        background: var(--secondary-green);
    }
    
    .map-btn.get-directions {
        background: var(--accent-green);
    }
    
    .map-btn.get-directions:hover {
        background: var(--primary-green);
    }
    
    /* PACKAGES CARDS STYLES */
    .packages-section {
        margin: 40px 0;
    }
    
    .packages-section h2 {
        color: var(--primary-green);
        margin-bottom: 30px;
        text-align: center;
        font-size: 2rem;
    }
    
    .packages-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 25px;
        margin-top: 20px;
    }
    
    .package-card {
        background: var(--pure-white);
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        transition: all 0.3s ease;
        border: 1px solid #eaeaea;
    }
    
    .package-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 15px 30px rgba(0,0,0,0.15);
    }
    
    .package-header {
        background: linear-gradient(135deg, var(--secondary-green), var(--primary-green));
        color: var(--pure-white);
        padding: 25px;
        text-align: center;
    }
    
    .package-header h3 {
        font-size: 1.5rem;
        margin-bottom: 10px;
    }
    
    .package-price {
        font-size: 2.2rem;
        font-weight: bold;
        margin: 15px 0;
    }
    
    .package-price span {
        font-size: 1rem;
        font-weight: normal;
        opacity: 0.9;
    }
    
    .package-badge {
        display: inline-block;
        background: var(--accent-green);
        color: var(--pure-white);
        padding: 5px 15px;
        border-radius: 20px;
        font-size: 0.9rem;
        font-weight: bold;
    }
    
    .package-content {
        padding: 25px;
    }
    
    .package-features {
        list-style: none;
        margin: 20px 0;
    }
    
    .package-features li {
        padding: 10px 0;
        border-bottom: 1px solid rgba(151, 188, 98, 0.2);
        display: flex;
        align-items: center;
    }
    
    .package-features li:last-child {
        border-bottom: none;
    }
    
    .package-features li i {
        color: var(--accent-green);
        margin-right: 10px;
        font-size: 1.1rem;
    }
    
    .package-card.popular {
        border: 2px solid var(--accent-green);
        position: relative;
    }
    
    .popular-badge {
        position: absolute;
        top: 15px;
        right: 15px;
        background: var(--accent-green);
        color: var(--pure-white);
        padding: 5px 15px;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: bold;
    }
    
    .package-actions {
        padding: 0 25px 25px;
        text-align: center;
    }
    
    @media (max-width: 768px) {
        .hotel-info {
            grid-template-columns: 1fr;
        }
        
        h1 {
            font-size: 2rem;
        }
        
        .packages-grid {
            grid-template-columns: 1fr;
        }
        
        .location-details {
            grid-template-columns: 1fr;
        }
        
        #map {
            height: 300px;
        }
        
        .main-image {
            height: 250px;
        }
        
        .footer-container {
            grid-template-columns: 1fr;
        }
    }
    
    /* Additional green theme elements */
    h2[style*="color: #1e3c72"] {
        color: var(--primary-green) !important;
    }
</style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="container">
        <!-- BACK BUTTON -->
        <div class="back-button-container">
            <button class="btn-back" onclick="goBackToHotels()">
                <i class="fas fa-arrow-left"></i>
                Back to Hotels
            </button>
        </div>

        <header>
            <h1><i class="fas fa-spa"></i> Savano Hotel</h1>
            <p class="tagline">Nature-Inspired Hotel & Garden Resort in San Jose del Monte, Bulacan</p>
        </header>

        <div class="hotel-info">
            <div>
                <img src="https://images.unsplash.com/photo-1566073771259-6a8506099945?q=80&w=2070&auto=format&fit=crop" alt="Savano Hotel" class="main-image">
            </div>
            <div class="details-box">
                <h2>Hotel Overview</h2>
                <div class="detail-item">
                    <i class="fas fa-map-marker-alt"></i>
                    <div>
                        <strong>Location:</strong><br>
                        Kaypian Road, San Jose del Monte, Bulacan
                    </div>
                </div>
                <button class="btn" onclick="showBookingModal()">
                    <i class="fas fa-leaf"></i> Book Nature Retreat
                </button>
            </div>
        </div>

        <!-- GOOGLE MAPS SECTION -->
        <div class="map-container">
            <h2><i class="fas fa-map-marked-alt"></i> Exact Location</h2>
            <div class="map-wrapper">
                <div id="map"></div>
                
                <div class="map-actions">
                    <button class="map-btn" onclick="showSatelliteView()">
                        <i class="fas fa-satellite"></i> Satellite View
                    </button>
                    <button class="map-btn" onclick="resetMapView()">
                        <i class="fas fa-sync-alt"></i> Reset View
                    </button>
                    <button class="map-btn get-directions" onclick="getDirections()">
                        <i class="fas fa-directions"></i> Get Directions
                    </button>
                </div>
                
                <div class="location-details">
                    <div class="location-info">
                        <h3><i class="fas fa-location-dot"></i> Address Details</h3>
                        <ul>
                            <li><i class="fas fa-map-pin"></i> <strong>Full Address:</strong> Kaypian Road, San Jose del Monte, Bulacan 3023</li>
                            <li><i class="fas fa-compass"></i> <strong>Coordinates:</strong> 14.8020° N, 121.0350° E</li>
                            <li><i class="fas fa-tree"></i> <strong>Hotel Theme:</strong> Nature-inspired garden hotel</li>
                            <li><i class="fas fa-seedling"></i> <strong>Special Feature:</strong> 2-hectare garden property</li>
                        </ul>
                    </div>
                    
                    <div class="location-info">
                        <h3><i class="fas fa-directions"></i> How to Get Here</h3>
                        <ul>
                            <li><i class="fas fa-car"></i> <strong>From NLEX:</strong> Exit at Bocaue, take Kaypian Road</li>
                            <li><i class="fas fa-bus"></i> <strong>Public Transport:</strong> Jeepneys from SJDM Terminal to Kaypian</li>
                            <li><i class="fas fa-clock"></i> <strong>Travel Time:</strong> 30 minutes from Quezon City</li>
                            <li><i class="fas fa-mountain"></i> <strong>Environment:</strong> Serene garden setting</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- GARDEN AREAS SECTION -->
        <div class="packages-section">
            <h2><i class="fas fa-tree"></i> Garden & Nature Areas</h2>
            <div class="garden-areas">
                
                <div class="garden-card">
                    <i class="fas fa-spa" style="font-size: 2rem; color: #2d5016; margin-bottom: 15px;"></i>
                    <h3>Meditation Garden</h3>
                    <p>Peaceful space for relaxation</p>
                </div>

                <div class="garden-card">
                    <i class="fas fa-route" style="font-size: 2rem; color: #2d5016; margin-bottom: 15px;"></i>
                    <h3>Nature Trail</h3>
                    <p>1km walking trail through gardens</p>
                </div>

                <div class="garden-card">
                    <i class="fas fa-fish" style="font-size: 2rem; color: #2d5016; margin-bottom: 15px;"></i>
                    <h3>Koi Pond</h3>
                    <p>Japanese-style koi fish pond</p>
                </div>

                <div class="garden-card">
                    <i class="fas fa-campfire" style="font-size: 2rem; color: #2d5016; margin-bottom: 15px;"></i>
                    <h3>Bonfire Area</h3>
                    <p>Evening bonfire gatherings</p>
                </div>

            </div>
        </div>

        <!-- ROOMS SECTION -->
        <div class="rooms-section">
            <h2><i class="fas fa-bed"></i> Nature-Inspired Rooms</h2>
            <div class="rooms-grid">
                
                <!-- Garden View Room Card -->
                <div class="room-card popular">
                    <div class="popular-badge">NATURE PICK</div>
                    <div class="room-header">
                        <h3>Garden View Room</h3>
                        <div class="room-badge">Direct Garden Access</div>
                        <div class="room-price">₱2,800<span>/night</span></div>
                    </div>
                    <div class="room-content">
                        <ul class="room-features">
                            <li><i class="fas fa-check-circle"></i> 25 sqm room with garden view</li>
                            <li><i class="fas fa-check-circle"></i> Queen-size bed with organic linens</li>
                            <li><i class="fas fa-check-circle"></i> Private balcony facing garden</li>
                            <li><i class="fas fa-check-circle"></i> Air conditioning</li>
                            <li><i class="fas fa-check-circle"></i> Free WiFi</li>
                            <li><i class="fas fa-check-circle"></i> Eco-friendly bathroom amenities</li>
                        </ul>
                    </div>
                    <div class="room-actions">
                        <button class="btn" onclick="selectRoom('Garden View Room')">
                            <i class="fas fa-leaf"></i> Book This Room
                        </button>
                    </div>
                </div>

                <!-- Treehouse Suite Card -->
                <div class="room-card">
                    <div class="room-header">
                        <h3>Treehouse Suite</h3>
                        <div class="room-badge">Unique Experience</div>
                        <div class="room-price">₱3,500<span>/night</span></div>
                    </div>
                    <div class="room-content">
                        <ul class="room-features">
                            <li><i class="fas fa-check-circle"></i> Elevated treehouse accommodation</li>
                            <li><i class="fas fa-check-circle"></i> Panoramic garden views</li>
                            <li><i class="fas fa-check-circle"></i> Private deck among trees</li>
                            <li><i class="fas fa-check-circle"></i> Romantic setting</li>
                            <li><i class="fas fa-check-circle"></i> Complimentary herbal tea</li>
                            <li><i class="fas fa-check-circle"></i> Bird watching binoculars provided</li>
                        </ul>
                    </div>
                    <div class="room-actions">
                        <button class="btn" onclick="selectRoom('Treehouse Suite')">
                            <i class="fas fa-tree"></i> Book This Suite
                        </button>
                    </div>
                </div>

                <!-- Family Garden Villa Card -->
                <div class="room-card">
                    <div class="room-header">
                        <h3>Family Garden Villa</h3>
                        <div class="room-badge">For 4-6 Persons</div>
                        <div class="package-price">₱5,200<span>/night</span></div>
                    </div>
                    <div class="room-content">
                        <ul class="room-features">
                            <li><i class="fas fa-check-circle"></i> 45 sqm two-bedroom villa</li>
                            <li><i class="fas fa-check-circle"></i> Private garden area</li>
                            <li><i class="fas fa-check-circle"></i> Living room with fireplace</li>
                            <li><i class="fas fa-check-circle"></i> Kitchenette with basic utensils</li>
                            <li><i class="fas fa-check-circle"></i> Outdoor dining area</li>
                            <li><i class="fas fa-check-circle"></i> Children's play corner</li>
                        </ul>
                    </div>
                    <div class="room-actions">
                        <button class="btn" onclick="selectRoom('Family Garden Villa')">
                            <i class="fas fa-home"></i> Book This Villa
                        </button>
                    </div>
                </div>

            </div>
        </div>

        <!-- SAVANO FEATURES -->
        <div class="packages-section">
            <h2><i class="fas fa-seedling"></i> Savano Unique Features</h2>
            <div class="savano-features">
                
                <div class="savano-card">
                    <i class="fas fa-recycle" style="font-size: 2rem; color: #2d5016; margin-bottom: 10px;"></i>
                    <h3>Eco-Friendly</h3>
                    <p>Sustainable practices</p>
                </div>

                <div class="savano-card">
                    <i class="fas fa-leaf" style="font-size: 2rem; color: #2d5016; margin-bottom: 10px;"></i>
                    <h3>Organic Garden</h3>
                    <p>Fresh produce on-site</p>
                </div>

                <div class="savano-card">
                    <i class="fas fa-wind" style="font-size: 2rem; color: #2d5016; margin-bottom: 10px;"></i>
                    <h3>Natural Ventilation</h3>
                    <p>Designed for fresh air flow</p>
                </div>

                <div class="savano-card">
                    <i class="fas fa-heart" style="font-size: 2rem; color: #2d5016; margin-bottom: 10px;"></i>
                    <h3>Wellness Focus</h3>
                    <p>Health and relaxation</p>
                </div>

            </div>
        </div>

        <div class="details-box">
            <h2>About Savano Hotel</h2>
            <p>Savano Hotel is a nature-inspired garden hotel that offers a peaceful retreat from city life. Nestled in a 2-hectare property filled with lush gardens, walking trails, and serene spaces, Savano combines modern comfort with natural surroundings. Our philosophy centers on sustainability, wellness, and connecting guests with nature while providing all the amenities of a quality hotel.</p>
        </div>

        <h2 style="color: #2d5016; margin: 30px 0 20px 0;">Hotel Features & Amenities</h2>
        <div class="hotel-features">
            <div class="feature-card">
                <i class="fas fa-utensils"></i>
                <h3>Garden Restaurant</h3>
                <p>Farm-to-table dining experience</p>
            </div>
            <div class="feature-card">
                <i class="fas fa-swimming-pool"></i>
                <h3>Infinity Pool</h3>
                <p>Pool overlooking the gardens</p>
            </div>
            <div class="feature-card">
                <i class="fas fa-spa"></i>
                <h3>Wellness Spa</h3>
                <p>Nature-inspired treatments</p>
            </div>
        </div>

        <!-- ACTIVITIES SECTION -->
        <div class="details-box">
            <h2>Nature Activities</h2>
            <div class="savano-features">
                <div class="savano-card">
                    <i class="fas fa-binoculars"></i>
                    <h3>Bird Watching</h3>
                    <p>Morning bird watching tours</p>
                </div>
                <div class="savano-card">
                    <i class="fas fa-hiking"></i>
                    <h3>Garden Yoga</h3>
                    <p>Daily yoga sessions in garden</p>
                </div>
                <div class="savano-card">
                    <i class="fas fa-campground"></i>
                    <h3>Stargazing</h3>
                    <p>Evening stargazing activities</p>
                </div>
                <div class="savano-card">
                    <i class="fas fa-seedling"></i>
                    <h3>Gardening Workshop</h3>
                    <p>Organic gardening lessons</p>
                </div>
            </div>
        </div>
    </div>

    <!-- FOOTER SECTION -->
    <footer>
        <div class="footer-container">
            <div class="footer-section">
                <h3><i class="fas fa-envelope"></i> Contact & Reservations</h3>
                <ul class="contact-info">
                    <li>
                        <i class="fas fa-phone"></i>
                        <div>
                            <strong>Phone:</strong><br>
                            (044) 791-4444 / 0927-555-6666
                        </div>
                    </li>
                    <li>
                        <i class="fas fa-envelope"></i>
                        <div>
                            <strong>Email:</strong><br>
                            stay@savanohotel.com
                        </div>
                    </li>
                    <li>
                        <i class="fas fa-clock"></i>
                        <div>
                            <strong>Check-in/Check-out:</strong><br>
                            3:00 PM | 12:00 NN
                        </div>
                    </li>
                </ul>
            </div>
            
            <div class="footer-section">
                <h3><i class="fas fa-share-alt"></i> Connect With Us</h3>
                <div class="social-links">
                    <a href="https://facebook.com/SavanoHotelSJDM" target="_blank" title="Facebook">
                        <i class="fab fa-facebook-f"></i>
                    </a>
                    <a href="mailto:stay@savanohotel.com" title="Email">
                        <i class="fas fa-envelope"></i>
                    </a>
                    <a href="tel:+0447914444" title="Call">
                        <i class="fas fa-phone"></i>
                    </a>
                    <a href="https://maps.google.com/?q=14.8020,121.0350" target="_blank" title="Location">
                        <i class="fas fa-map-marker-alt"></i>
                    </a>
                </div>
                <button class="btn" onclick="showBookingModal()" style="margin-top: 20px;">
                    <i class="fas fa-leaf"></i> Book Nature Retreat
                </button>
            </div>
            
            <div class="footer-section">
                <h3><i class="fas fa-tree"></i> Hotel Details</h3>
                <ul class="contact-info">
                    <li>
                        <i class="fas fa-map-marker-alt"></i>
                        <div>
                            <strong>Location:</strong><br>
                            Kaypian Road, SJDM, Bulacan
                        </div>
                    </li>
                    <li>
                        <i class="fas fa-mountain"></i>
                        <div>
                            <strong>Property Size:</strong><br>
                            2 hectares of gardens and trails
                        </div>
                    </li>
                    <li>
                        <i class="fas fa-leaf"></i>
                        <div>
                            <strong>Theme:</strong><br>
                            Nature-inspired sustainable hotel
                        </div>
                    </li>
                </ul>
            </div>
        </div>
        
        <div class="footer-bottom">
            <p>&copy; 2024 Savano Hotel. All rights reserved. | Nature-Inspired Hotel & Garden Resort in San Jose del Monte, Bulacan</p>
            <p style="margin-top: 10px; font-size: 0.8rem;">Committed to sustainable tourism and environmental conservation</p>
        </div>
    </footer>

    <!-- Google Maps API -->
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDHyM2Tq_jy7n6gXeJxQeK5dQegHxQN9X8&callback=initMap" async defer></script>
    
    <script>
        let map;
        let marker;
        let infoWindow;
        
        // Function to go back to Hotels section
        function goBackToHotels() {
            // Try to go back in history first
            if (window.history.length > 1) {
                window.history.back();
            } else {
                // If no history, redirect to main page with hotels section
                window.location.href = 'index.html#hotels';
            }
        }
        
        // Initialize Google Map for Savano Hotel
        function initMap() {
            // Exact coordinates for Savano Hotel
            const hotelLocation = { 
                lat: 14.8020, 
                lng: 121.0350 
            };
            
            map = new google.maps.Map(document.getElementById("map"), {
                zoom: 16,
                center: hotelLocation,
                mapTypeId: 'roadmap',
                styles: [
                    {
                        featureType: "poi",
                        elementType: "labels",
                        stylers: [{ visibility: "off" }]
                    }
                ]
            });
            
            marker = new google.maps.Marker({
                position: hotelLocation,
                map: map,
                title: "Savano Hotel",
                icon: {
                    path: google.maps.SymbolPath.CIRCLE,
                    scale: 10,
                    fillColor: "#4a7c2a",
                    fillOpacity: 1,
                    strokeColor: "#FFFFFF",
                    strokeWeight: 2
                },
                animation: google.maps.Animation.DROP
            });
            
            infoWindow = new google.maps.InfoWindow({
                content: `
                    <div style="padding: 10px; max-width: 250px;">
                        <h3 style="color: #2d5016; margin: 0 0 10px 0; font-size: 16px;">🌿 Savano Hotel</h3>
                        <p style="margin: 5px 0; font-size: 14px;"><strong>🌳 Nature-Inspired Retreat</strong></p>
                        <p style="margin: 5px 0; font-size: 13px;"><strong>📍 Address:</strong> Kaypian Road, SJDM</p>
                        <p style="margin: 5px 0; font-size: 13px;"><strong>📞 Contact:</strong> (044) 791-4444</p>
                        <div style="margin-top: 10px;">
                            <button onclick="getDirections()" style="background: #2d5016; color: white; border: none; padding: 8px 15px; border-radius: 5px; cursor: pointer; font-size: 12px;">
                                <i class="fas fa-directions"></i> Get Directions
                            </button>
                        </div>
                    </div>
                `
            });
            
            marker.addListener("click", () => {
                infoWindow.open(map, marker);
            });
            
            // Open info window by default
            infoWindow.open(map, marker);
            
            // Add nearby natural attractions
            const nearbyAttractions = [
                {
                    name: "Kaypian Nature Park",
                    position: { lat: 14.8030, lng: 121.0330 },
                    icon: "http://maps.google.com/mapfiles/ms/icons/green-dot.png"
                },
                {
                    name: "Bulacan Botanical Gardens",
                    position: { lat: 14.8010, lng: 121.0370 },
                    icon: "http://maps.google.com/mapfiles/ms/icons/green-dot.png"
                }
            ];
            
            nearbyAttractions.forEach(attraction => {
                new google.maps.Marker({
                    position: attraction.position,
                    map: map,
                    title: attraction.name,
                    icon: attraction.icon
                });
            });
        }
        
        // Map view functions
        function showSatelliteView() {
            if (map) map.setMapTypeId(google.maps.MapTypeId.SATELLITE);
        }
        
        function resetMapView() {
            if (map) {
                map.setMapTypeId(google.maps.MapTypeId.ROADMAP);
                map.setZoom(16);
                map.setCenter({ lat: 14.8020, lng: 121.0350 });
            }
        }
        
        function getDirections() {
            const destination = "14.8020,121.0350";
            const url = `https://www.google.com/maps/dir/?api=1&destination=${destination}&destination_place_id=Savano+Hotel+San+Jose+del+Monte+Bulacan`;
            window.open(url, '_blank');
        }
        
        // Room selection function
        function selectRoom(roomType) {
            const message = `You selected: ${roomType}\n\nTo book this room:\n1. Call us at (044) 791-4444\n2. Mention room code: SVN-${roomType.toUpperCase().replace(/ /g, '-')}\n3. Provide your check-in/check-out dates\n4. We'll send you a confirmation within 24 hours`;
            alert(message);
        }

        // Booking modal function
        function showBookingModal() {
            alert('For bookings or inquiries, please call (044) 791-4444 or email stay@savanohotel.com\n\nExperience nature at its finest! We recommend booking our treehouse suites at least 1 month in advance as they are very popular.');
        }
        
        // Add keyboard shortcut for back button (Esc key)
        document.addEventListener('DOMContentLoaded', function() {
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    goBackToHotels();
                }
            });
            
            // Garden card interactions
            const gardens = document.querySelectorAll('.garden-card');
            gardens.forEach(garden => {
                garden.addEventListener('click', function() {
                    const gardenName = this.querySelector('h3').textContent;
                    const gardenDesc = this.querySelector('p').textContent;
                    alert(`Garden Area: ${gardenName}\n\n${gardenDesc}\n\nAll guests have access to our garden areas. Special activities may require advance booking.`);
                });
            });
            
            // Savano feature interactions
            const features = document.querySelectorAll('.savano-card');
            features.forEach(feature => {
                feature.addEventListener('click', function() {
                    const featureName = this.querySelector('h3').textContent;
                    alert(`Savano Feature: ${featureName}\n\nThis is part of our commitment to sustainable tourism and guest wellness.`);
                });
            });
        });
        
        // Handle Google Maps API errors
        window.gm_authFailure = function() {
            document.getElementById("map").innerHTML = `
                <div style="height: 100%; display: flex; flex-direction: column; justify-content: center; align-items: center; text-align: center; padding: 20px;">
                    <i class="fas fa-spa" style="font-size: 48px; color: #666; margin-bottom: 15px;"></i>
                    <h3 style="color: #333; margin-bottom: 10px;">Map Unavailable</h3>
                    <p style="color: #666; margin-bottom: 15px;">Google Maps cannot be loaded at the moment.</p>
                    <p style="color: #2d5016;"><strong>📍 Savano Hotel Location:</strong></p>
                    <p style="color: #333;">Kaypian Road, San Jose del Monte, Bulacan</p>
                    <p style="color: #333;">Coordinates: 14.8020° N, 121.0350° E</p>
                </div>
            `;
        };
    </script>
</body>
</html>