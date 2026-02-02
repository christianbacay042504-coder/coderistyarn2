<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>La Cecilia Resort - San Jose del Monte, Bulacan</title>
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
            <h1><i class="fas fa-umbrella-beach"></i> La Cecilia Resort</h1>
            <p class="tagline">Relaxing Water Resort Experience in San Jose del Monte, Bulacan</p>
        </header>

        <div class="resort-info">
            <div>
                <img src="https://images.unsplash.com/photo-1519046904884-53103b34b206?q=80&w=2070&auto=format&fit=crop" alt="La Cecilia Resort" class="main-image">
            </div>
            <div class="details-box">
                <h2>Resort Overview</h2>
                <div class="detail-item">
                    <i class="fas fa-map-marker-alt"></i>
                    <div>
                        <strong>Location:</strong><br>
                        Balite Area, Barangay Tungkong Mangga, San Jose del Monte, Bulacan
                    </div>
                </div>
                <button class="btn" onclick="showBookingModal()">
                    <i class="fas fa-swimming-pool"></i> Book Resort Stay
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
                            <li><i class="fas fa-map-pin"></i> <strong>Full Address:</strong> Balite Area, Barangay Tungkong Mangga, San Jose del Monte, Bulacan 3023</li>
                            <li><i class="fas fa-compass"></i> <strong>Coordinates:</strong> 14.8065° N, 121.0382° E</li>
                            <li><i class="fas fa-water"></i> <strong>Resort Size:</strong> 2-hectare water resort complex</li>
                            <li><i class="fas fa-swimmer"></i> <strong>Special Feature:</strong> 5 different swimming pools</li>
                        </ul>
                    </div>
                    
                    <div class="location-info">
                        <h3><i class="fas fa-directions"></i> How to Get Here</h3>
                        <ul>
                            <li><i class="fas fa-car"></i> <strong>From NLEX:</strong> Exit at Bocaue, take Tungkong Mangga Road to Balite Area</li>
                            <li><i class="fas fa-bus"></i> <strong>Public Transport:</strong> Tricycles from Tungkong Mangga junction to resort</li>
                            <li><i class="fas fa-clock"></i> <strong>Travel Time:</strong> 25 minutes from Quezon City</li>
                            <li><i class="fas fa-tree"></i> <strong>Environment:</strong> Semi-rural setting with natural springs</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- POOL TYPES SECTION -->
        <div class="packages-section">
            <h2><i class="fas fa-swimming-pool"></i> Swimming Pool Areas</h2>
            <div class="pool-types">
                
                <div class="pool-card">
                    <i class="fas fa-swimmer" style="font-size: 2.5rem; color: #00695c; margin-bottom: 15px;"></i>
                    <h3>Main Pool</h3>
                    <p>Olympic-sized swimming pool for all ages</p>
                </div>

                <div class="pool-card">
                    <i class="fas fa-child" style="font-size: 2.5rem; color: #00695c; margin-bottom: 15px;"></i>
                    <h3>Kiddie Pool</h3>
                    <p>Shallow pool with water slides and playground</p>
                </div>

                <div class="pool-card">
                    <i class="fas fa-hot-tub" style="font-size: 2.5rem; color: #00695c; margin-bottom: 15px;"></i>
                    <h3>Jacuzzi Pool</h3>
                    <p>Therapeutic warm water with hydrotherapy jets</p>
                </div>

                <div class="pool-card">
                    <i class="fas fa-water" style="font-size: 2.5rem; color: #00695c; margin-bottom: 15px;"></i>
                    <h3>Infinity Pool</h3>
                    <p>Scenic pool overlooking the mountains</p>
                </div>

            </div>
        </div>

        <!-- PACKAGES SECTION -->
        <div class="packages-section">
            <h2><i class="fas fa-gift"></i> Resort Stay Packages</h2>
            <div class="packages-grid">
                
                <!-- Day Tour Package Card -->
                <div class="package-card popular">
                    <div class="popular-badge">MOST BOOKED</div>
                    <div class="package-header">
                        <h3>Day Tour Package</h3>
                        <div class="package-badge">8AM - 5PM Access</div>
                        <div class="package-price">₱450<span>/person</span></div>
                    </div>
                    <div class="package-content">
                        <ul class="package-features">
                            <li><i class="fas fa-check-circle"></i> Access to all swimming pools</li>
                            <li><i class="fas fa-check-circle"></i> Use of picnic tables</li>
                            <li><i class="fas fa-check-circle"></i> Free parking</li>
                            <li><i class="fas fa-check-circle"></i> Locker rental included</li>
                            <li><i class="fas fa-check-circle"></i> Shower facilities</li>
                            <li><i class="fas fa-check-circle"></i> Kiddie playground access</li>
                        </ul>
                    </div>
                    <div class="package-actions">
                        <button class="btn" onclick="selectPackage('Day Tour Package')">
                            <i class="fas fa-calendar-day"></i> Book Day Tour
                        </button>
                    </div>
                </div>

                <!-- Overnight Package Card -->
                <div class="package-card">
                    <div class="package-header">
                        <h3>Overnight Package</h3>
                        <div class="package-badge">24-Hour Stay</div>
                        <div class="package-price">₱3,500<span>/room</span></div>
                    </div>
                    <div class="package-content">
                        <ul class="package-features">
                            <li><i class="fas fa-check-circle"></i> Air-conditioned room</li>
                            <li><i class="fas fa-check-circle"></i> Access to all facilities</li>
                            <li><i class="fas fa-check-circle"></i> Breakfast for two</li>
                            <li><i class="fas fa-check-circle"></i> 24-hour pool access</li>
                            <li><i class="fas fa-check-circle"></i> Complimentary drinks</li>
                            <li><i class="fas fa-check-circle"></i> Bonfire night activity</li>
                        </ul>
                    </div>
                    <div class="package-actions">
                        <button class="btn" onclick="selectPackage('Overnight Package')">
                            <i class="fas fa-moon"></i> Book Overnight
                        </button>
                    </div>
                </div>

                <!-- Family Package Card -->
                <div class="package-card">
                    <div class="package-header">
                        <h3>Family Package</h3>
                        <div class="package-badge">For 4-6 Persons</div>
                        <div class="package-price">₱8,500<span>/2D1N</span></div>
                    </div>
                    <div class="package-content">
                        <ul class="package-features">
                            <li><i class="fas fa-check-circle"></i> Family villa accommodation</li>
                            <li><i class="fas fa-check-circle"></i> All-day pool access</li>
                            <li><i class="fas fa-check-circle"></i> 3 meals for family</li>
                            <li><i class="fas fa-check-circle"></i> Free use of grill</li>
                            <li><i class="fas fa-check-circle"></i> Water sports activities</li>
                            <li><i class="fas fa-check-circle"></i> Evening entertainment</li>
                        </ul>
                    </div>
                    <div class="package-actions">
                        <button class="btn" onclick="selectPackage('Family Package')">
                            <i class="fas fa-users"></i> Book Family Package
                        </button>
                    </div>
                </div>

            </div>
        </div>

        <div class="details-box">
            <h2>About La Cecilia Resort</h2>
            <p>La Cecilia Resort is a premier water resort destination in San Jose del Monte, offering five different swimming pools suitable for all ages. Established in 2010, the resort features Olympic-sized pools, therapeutic jacuzzi, kiddie pools with water slides, and a scenic infinity pool. Perfect for family outings, team building, and weekend getaways.</p>
        </div>

        <h2 style="color: #00695c; margin: 30px 0 20px 0;">Resort Features & Amenities</h2>
        <div class="resort-features">
            <div class="feature-card">
                <i class="fas fa-utensils"></i>
                <h3>Restaurant & Grill</h3>
                <p>On-site restaurant serving Filipino favorites</p>
            </div>
            <div class="feature-card">
                <i class="fas fa-campground"></i>
                <h3>Picnic Areas</h3>
                <p>Grassy picnic spots with tables and benches</p>
            </div>
            <div class="feature-card">
                <i class="fas fa-music"></i>
                <h3>Function Halls</h3>
                <p>Event spaces for parties and gatherings</p>
            </div>
        </div>

        <div class="details-box">
            <h2>Resort Facilities</h2>
            <div class="resort-facilities">
                <div class="facility-card">
                    <h4><i class="fas fa-parking"></i> Ample Parking</h4>
                    <p>Secure parking for 100+ vehicles</p>
                </div>
                <div class="facility-card">
                    <h4><i class="fas fa-lock"></i> Locker Rooms</h4>
                    <p>Secure lockers for valuables</p>
                </div>
                <div class="facility-card">
                    <h4><i class="fas fa-shower"></i> Shower Areas</h4>
                    <p>Clean shower and changing rooms</p>
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
                            (044) 791-8888 / 0918-765-4321
                        </div>
                    </li>
                    <li>
                        <i class="fas fa-envelope"></i>
                        <div>
                            <strong>Email:</strong><br>
                            info@laceciliaresort.com
                        </div>
                    </li>
                    <li>
                        <i class="fas fa-clock"></i>
                        <div>
                            <strong>Operating Hours:</strong><br>
                            8:00 AM - 10:00 PM Daily
                        </div>
                    </li>
                </ul>
            </div>
            
            <div class="footer-section">
                <h3><i class="fas fa-share-alt"></i> Connect With Us</h3>
                <div class="social-links">
                    <a href="https://facebook.com/LaCeciliaResortSJDM" target="_blank" title="Facebook">
                        <i class="fab fa-facebook-f"></i>
                    </a>
                    <a href="mailto:info@laceciliaresort.com" title="Email">
                        <i class="fas fa-envelope"></i>
                    </a>
                    <a href="tel:+0447918888" title="Call">
                        <i class="fas fa-phone"></i>
                    </a>
                    <a href="https://maps.google.com/?q=14.8065,121.0382" target="_blank" title="Location">
                        <i class="fas fa-map-marker-alt"></i>
                    </a>
                </div>
                <button class="btn" onclick="showBookingModal()" style="margin-top: 20px;">
                    <i class="fas fa-swimming-pool"></i> Book Resort Stay
                </button>
            </div>
            
            <div class="footer-section">
                <h3><i class="fas fa-water"></i> Resort Details</h3>
                <ul class="contact-info">
                    <li>
                        <i class="fas fa-map-marker-alt"></i>
                        <div>
                            <strong>Location:</strong><br>
                            Balite Area, Tungkong Mangga, SJDM, Bulacan
                        </div>
                    </li>
                    <li>
                        <i class="fas fa-ruler-combined"></i>
                        <div>
                            <strong>Resort Size:</strong><br>
                            2 hectares with 5 swimming pools
                        </div>
                    </li>
                    <li>
                        <i class="fas fa-users"></i>
                        <div>
                            <strong>Capacity:</strong><br>
                            Can accommodate 500+ guests
                        </div>
                    </li>
                </ul>
            </div>
        </div>
        
        <div class="footer-bottom">
            <p>&copy; 2024 La Cecilia Resort. All rights reserved. | Premier Water Resort Experience in San Jose del Monte, Bulacan</p>
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
        
        // Initialize Google Map for La Cecilia Resort
        function initMap() {
            // Exact coordinates for La Cecilia Resort
            const resortLocation = { 
                lat: 14.8065, 
                lng: 121.0382 
            };
            
            map = new google.maps.Map(document.getElementById("map"), {
                zoom: 16,
                center: resortLocation,
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
                position: resortLocation,
                map: map,
                title: "La Cecilia Resort",
                icon: {
                    path: google.maps.SymbolPath.CIRCLE,
                    scale: 10,
                    fillColor: "#26a69a",
                    fillOpacity: 1,
                    strokeColor: "#FFFFFF",
                    strokeWeight: 2
                },
                animation: google.maps.Animation.DROP
            });
            
            infoWindow = new google.maps.InfoWindow({
                content: `
                    <div style="padding: 10px; max-width: 250px;">
                        <h3 style="color: #00695c; margin: 0 0 10px 0; font-size: 16px;">🏊‍♀️ La Cecilia Resort</h3>
                        <p style="margin: 5px 0; font-size: 14px;"><strong>💦 5 Swimming Pools</strong></p>
                        <p style="margin: 5px 0; font-size: 13px;"><strong>📍 Address:</strong> Tungkong Mangga, SJDM</p>
                        <p style="margin: 5px 0; font-size: 13px;"><strong>📞 Contact:</strong> (044) 791-8888</p>
                        <div style="margin-top: 10px;">
                            <button onclick="getDirections()" style="background: #00695c; color: white; border: none; padding: 8px 15px; border-radius: 5px; cursor: pointer; font-size: 12px;">
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
            
            // Add nearby water resorts
            const nearbyResorts = [
                {
                    name: "Aqua Planet",
                    position: { lat: 14.8080, lng: 121.0360 },
                    icon: "http://maps.google.com/mapfiles/ms/icons/blue-dot.png"
                },
                {
                    name: "Tungkong Mangga Springs",
                    position: { lat: 14.8050, lng: 121.0400 },
                    icon: "http://maps.google.com/mapfiles/ms/icons/blue-dot.png"
                }
            ];
            
            nearbyResorts.forEach(resort => {
                new google.maps.Marker({
                    position: resort.position,
                    map: map,
                    title: resort.name,
                    icon: resort.icon
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
                map.setCenter({ lat: 14.8065, lng: 121.0382 });
            }
        }
        
        function getDirections() {
            const destination = "14.8065,121.0382";
            const url = `https://www.google.com/maps/dir/?api=1&destination=${destination}&destination_place_id=La+Cecilia+Resort+San+Jose+del+Monte+Bulacan`;
            window.open(url, '_blank');
        }
        
        // Package selection function
        function selectPackage(packageName) {
            const message = `You selected: ${packageName}\n\nTo book this package:\n1. Call us at (044) 791-8888\n2. Mention package code: LCR-${packageName.toUpperCase().replace(/ /g, '-')}\n3. Provide your preferred dates and number of persons\n4. We'll send you a confirmation within 24 hours`;
            alert(message);
        }

        // Booking modal function
        function showBookingModal() {
            alert('For bookings or inquiries, please call (044) 791-8888 or email info@laceciliaresort.com\n\nWeekend slots fill up quickly! We recommend booking at least 2 weeks in advance for Saturday/Sunday visits.');
        }
        
        // Add keyboard shortcut for back button (Esc key)
        document.addEventListener('DOMContentLoaded', function() {
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    goBackToHotels();
                }
            });
            
            // Pool card interactions
            const pools = document.querySelectorAll('.pool-card');
            pools.forEach(pool => {
                pool.addEventListener('click', function() {
                    const poolName = this.querySelector('h3').textContent;
                    const poolDesc = this.querySelector('p').textContent;
                    alert(`Pool: ${poolName}\n\nDescription: ${poolDesc}\n\nAll our pools are regularly maintained and chlorinated for your safety and enjoyment.`);
                });
            });
        });
        
        // Handle Google Maps API errors
        window.gm_authFailure = function() {
            document.getElementById("map").innerHTML = `
                <div style="height: 100%; display: flex; flex-direction: column; justify-content: center; align-items: center; text-align: center; padding: 20px;">
                    <i class="fas fa-swimming-pool" style="font-size: 48px; color: #666; margin-bottom: 15px;"></i>
                    <h3 style="color: #333; margin-bottom: 10px;">Map Unavailable</h3>
                    <p style="color: #666; margin-bottom: 15px;">Google Maps cannot be loaded at the moment.</p>
                    <p style="color: #00695c;"><strong>📍 La Cecilia Resort Location:</strong></p>
                    <p style="color: #333;">Balite Area, Tungkong Mangga, San Jose del Monte, Bulacan</p>
                    <p style="color: #333;">Coordinates: 14.8065° N, 121.0382° E</p>
                </div>
            `;
        };
    </script>
</body>
</html>