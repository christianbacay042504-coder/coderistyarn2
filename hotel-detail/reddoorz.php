<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RedDoorz Hotel - San Jose del Monte, Bulacan</title>
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
            <h1><i class="fas fa-door-closed"></i> RedDoorz Hotel</h1>
            <p class="tagline">Affordable Comfort in San Jose del Monte, Bulacan</p>
        </header>

        <div class="hotel-info">
            <div>
                <img src="https://images.unsplash.com/photo-1566665797739-1674de7a421a?q=80&w=2074&auto=format&fit=crop" alt="RedDoorz Hotel" class="main-image">
            </div>
            <div class="details-box">
                <h2>Hotel Overview</h2>
                <div class="detail-item">
                    <i class="fas fa-map-marker-alt"></i>
                    <div>
                        <strong>Location:</strong><br>
                        City Proper, San Jose del Monte, Bulacan
                    </div>
                </div>
                <button class="btn" onclick="showBookingModal()">
                    <i class="fas fa-calendar-check"></i> Book Online Now
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
                            <li><i class="fas fa-map-pin"></i> <strong>Full Address:</strong> City Proper, San Jose del Monte, Bulacan 3023</li>
                            <li><i class="fas fa-compass"></i> <strong>Coordinates:</strong> 14.8150° N, 121.0480° E</li>
                            <li><i class="fas fa-building"></i> <strong>Hotel Type:</strong> Budget-friendly chain hotel</li>
                            <li><i class="fas fa-star"></i> <strong>Rating:</strong> RedDoorz Standard Certified</li>
                        </ul>
                    </div>
                    
                    <div class="location-info">
                        <h3><i class="fas fa-directions"></i> How to Get Here</h3>
                        <ul>
                            <li><i class="fas fa-car"></i> <strong>From NLEX:</strong> Exit at Bocaue, take Quirino Highway to City Proper</li>
                            <li><i class="fas fa-bus"></i> <strong>Public Transport:</strong> Buses and jeepneys from Manila to SJDM Terminal</li>
                            <li><i class="fas fa-clock"></i> <strong>Travel Time:</strong> 35 minutes from Quezon City</li>
                            <li><i class="fas fa-store"></i> <strong>Convenience:</strong> Walking distance to shops and restaurants</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- REDDOORZ BENEFITS SECTION -->
        <div class="packages-section">
            <h2><i class="fas fa-award"></i> RedDoorz Benefits</h2>
            <div class="benefits-grid">
                
                <div class="benefit-card">
                    <i class="fas fa-wifi" style="font-size: 2rem; color: #e63946; margin-bottom: 10px;"></i>
                    <h3>Free WiFi</h3>
                    <p>High-speed internet access</p>
                </div>

                <div class="benefit-card">
                    <i class="fas fa-shield-alt" style="font-size: 2rem; color: #e63946; margin-bottom: 10px;"></i>
                    <h3>24/7 Security</h3>
                    <p>CCTV and security personnel</p>
                </div>

                <div class="benefit-card">
                    <i class="fas fa-snowflake" style="font-size: 2rem; color: #e63946; margin-bottom: 10px;"></i>
                    <h3>Air Conditioning</h3>
                    <p>Individual room control</p>
                </div>

                <div class="benefit-card">
                    <i class="fas fa-tv" style="font-size: 2rem; color: #e63946; margin-bottom: 10px;"></i>
                    <h3>Cable TV</h3>
                    <p>Entertainment channels</p>
                </div>

            </div>
        </div>

        <!-- ROOMS SECTION -->
        <div class="rooms-section">
            <h2><i class="fas fa-bed"></i> Room Types & Rates</h2>
            <div class="rooms-grid">
                
                <!-- Standard Room Card -->
                <div class="room-card popular">
                    <div class="popular-badge">BESTSELLER</div>
                    <div class="room-header">
                        <h3>Standard Room</h3>
                        <div class="room-badge">Single/Double</div>
                        <div class="room-price">₱1,200<span>/night</span></div>
                    </div>
                    <div class="room-content">
                        <ul class="room-features">
                            <li><i class="fas fa-check-circle"></i> 20 sqm comfortable room</li>
                            <li><i class="fas fa-check-circle"></i> Choice of single or double bed</li>
                            <li><i class="fas fa-check-circle"></i> Air conditioning</li>
                            <li><i class="fas fa-check-circle"></i> Free WiFi</li>
                            <li><i class="fas fa-check-circle"></i> Cable TV</li>
                            <li><i class="fas fa-check-circle"></i> Ensuite bathroom</li>
                        </ul>
                    </div>
                    <div class="room-actions">
                        <button class="btn" onclick="selectRoom('Standard Room')">
                            <i class="fas fa-book"></i> Book This Room
                        </button>
                    </div>
                </div>

                <!-- Family Room Card -->
                <div class="room-card">
                    <div class="room-header">
                        <h3>Family Room</h3>
                        <div class="room-badge">Up to 4 Persons</div>
                        <div class="room-price">₱1,800<span>/night</span></div>
                    </div>
                    <div class="room-content">
                        <ul class="room-features">
                            <li><i class="fas fa-check-circle"></i> 25 sqm family room</li>
                            <li><i class="fas fa-check-circle"></i> Two double beds</li>
                            <li><i class="fas fa-check-circle"></i> Perfect for families</li>
                            <li><i class="fas fa-check-circle"></i> Air conditioning</li>
                            <li><i class="fas fa-check-circle"></i> Free WiFi</li>
                            <li><i class="fas fa-check-circle"></i> Cable TV with kids channels</li>
                        </ul>
                    </div>
                    <div class="room-actions">
                        <button class="btn" onclick="selectRoom('Family Room')">
                            <i class="fas fa-users"></i> Book This Room
                        </button>
                    </div>
                </div>

                <!-- Executive Room Card -->
                <div class="room-card">
                    <div class="room-header">
                        <h3>Executive Room</h3>
                        <div class="room-badge">Business Travelers</div>
                        <div class="room-price">₱1,500<span>/night</span></div>
                    </div>
                    <div class="room-content">
                        <ul class="room-features">
                            <li><i class="fas fa-check-circle"></i> 22 sqm executive room</li>
                            <li><i class="fas fa-check-circle"></i> Queen-size bed</li>
                            <li><i class="fas fa-check-circle"></i> Work desk with lamp</li>
                            <li><i class="fas fa-check-circle"></i> Complimentary coffee/tea</li>
                            <li><i class="fas fa-check-circle"></i> Enhanced WiFi speed</li>
                            <li><i class="fas fa-check-circle"></i> Premium bathroom amenities</li>
                        </ul>
                    </div>
                    <div class="room-actions">
                        <button class="btn" onclick="selectRoom('Executive Room')">
                            <i class="fas fa-briefcase"></i> Book This Room
                        </button>
                    </div>
                </div>

            </div>
        </div>

        <!-- NEARBY ATTRACTIONS -->
        <div class="packages-section">
            <h2><i class="fas fa-map-pin"></i> Nearby Attractions</h2>
            <div class="nearby-attractions">
                
                <div class="attraction-card">
                    <i class="fas fa-shopping-cart" style="font-size: 2rem; color: #e63946; margin-bottom: 15px;"></i>
                    <h3>SM City SJDM</h3>
                    <p>5-minute drive<br>Shopping mall</p>
                </div>

                <div class="attraction-card">
                    <i class="fas fa-church" style="font-size: 2rem; color: #e63946; margin-bottom: 15px;"></i>
                    <h3>Divine Mercy Shrine</h3>
                    <p>10-minute walk<br>Religious site</p>
                </div>

                <div class="attraction-card">
                    <i class="fas fa-utensils" style="font-size: 2rem; color: #e63946; margin-bottom: 15px;"></i>
                    <h3>Food Street</h3>
                    <p>Walking distance<br>Local restaurants</p>
                </div>

                <div class="attraction-card">
                    <i class="fas fa-bus" style="font-size: 2rem; color: #e63946; margin-bottom: 15px;"></i>
                    <h3>Transport Terminal</h3>
                    <p>5-minute walk<br>Public transport hub</p>
                </div>

            </div>
        </div>

        <div class="details-box">
            <h2>About RedDoorz Hotel</h2>
            <p>RedDoorz Hotel in San Jose del Monte offers affordable, clean, and comfortable accommodation with standardized quality across all RedDoorz properties. As part of Southeast Asia's leading hotel chain, we provide budget-friendly rooms without compromising on essential amenities. Perfect for budget-conscious travelers, business visitors, and families looking for reliable accommodation.</p>
        </div>

        <h2 style="color: #e63946; margin: 30px 0 20px 0;">Hotel Features</h2>
        <div class="hotel-features">
            <div class="feature-card">
                <i class="fas fa-concierge-bell"></i>
                <h3>24-Hour Reception</h3>
                <p>Round-the-clock front desk service</p>
            </div>
            <div class="feature-card">
                <i class="fas fa-parking"></i>
                <h3>Parking Available</h3>
                <p>Secure parking for guests</p>
            </div>
            <div class="feature-card">
                <i class="fas fa-luggage-cart"></i>
                <h3>Luggage Storage</h3>
                <p>Safe storage for belongings</p>
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
                            (044) 791-2222 / 0917-777-8888
                        </div>
                    </li>
                    <li>
                        <i class="fas fa-envelope"></i>
                        <div>
                            <strong>Email:</strong><br>
                            sjdm@reddoorz.com
                        </div>
                    </li>
                    <li>
                        <i class="fas fa-clock"></i>
                        <div>
                            <strong>Check-in/Check-out:</strong><br>
                            2:00 PM | 12:00 NN
                        </div>
                    </li>
                </ul>
            </div>
            
            <div class="footer-section">
                <h3><i class="fas fa-share-alt"></i> Connect With Us</h3>
                <div class="social-links">
                    <a href="https://facebook.com/RedDoorzSJDM" target="_blank" title="Facebook">
                        <i class="fab fa-facebook-f"></i>
                    </a>
                    <a href="mailto:sjdm@reddoorz.com" title="Email">
                        <i class="fas fa-envelope"></i>
                    </a>
                    <a href="tel:+0447912222" title="Call">
                        <i class="fas fa-phone"></i>
                    </a>
                    <a href="https://maps.google.com/?q=14.8150,121.0480" target="_blank" title="Location">
                        <i class="fas fa-map-marker-alt"></i>
                    </a>
                </div>
                <button class="btn" onclick="showBookingModal()" style="margin-top: 20px;">
                    <i class="fas fa-calendar-check"></i> Book Online Now
                </button>
            </div>
            
            <div class="footer-section">
                <h3><i class="fas fa-door-closed"></i> Hotel Details</h3>
                <ul class="contact-info">
                    <li>
                        <i class="fas fa-map-marker-alt"></i>
                        <div>
                            <strong>Location:</strong><br>
                            City Proper, SJDM, Bulacan
                        </div>
                    </li>
                    <li>
                        <i class="fas fa-door-closed"></i>
                        <div>
                            <strong>Rooms:</strong><br>
                            40 rooms (Standard, Family, Executive)
                        </div>
                    </li>
                    <li>
                        <i class="fas fa-wifi"></i>
                        <div>
                            <strong>Amenities:</strong><br>
                            WiFi, AC, Cable TV, 24/7 Reception
                        </div>
                    </li>
                </ul>
            </div>
        </div>
        
        <div class="footer-bottom">
            <p>&copy; 2024 RedDoorz Hotel. All rights reserved. | Affordable Comfort in San Jose del Monte, Bulacan</p>
            <p style="margin-top: 10px; font-size: 0.8rem;">RedDoorz is a registered trademark of RedDoorz International Pte Ltd</p>
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
        
        // Initialize Google Map for RedDoorz Hotel
        function initMap() {
            // Exact coordinates for RedDoorz Hotel
            const hotelLocation = { 
                lat: 14.8150, 
                lng: 121.0480 
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
                title: "RedDoorz Hotel",
                icon: {
                    path: google.maps.SymbolPath.CIRCLE,
                    scale: 10,
                    fillColor: "#ff6b6b",
                    fillOpacity: 1,
                    strokeColor: "#FFFFFF",
                    strokeWeight: 2
                },
                animation: google.maps.Animation.DROP
            });
            
            infoWindow = new google.maps.InfoWindow({
                content: `
                    <div style="padding: 10px; max-width: 250px;">
                        <h3 style="color: #e63946; margin: 0 0 10px 0; font-size: 16px;">🏨 RedDoorz Hotel</h3>
                        <p style="margin: 5px 0; font-size: 14px;"><strong>💰 Budget-Friendly Accommodation</strong></p>
                        <p style="margin: 5px 0; font-size: 13px;"><strong>📍 Address:</strong> City Proper, SJDM</p>
                        <p style="margin: 5px 0; font-size: 13px;"><strong>📞 Contact:</strong> (044) 791-2222</p>
                        <div style="margin-top: 10px;">
                            <button onclick="getDirections()" style="background: #e63946; color: white; border: none; padding: 8px 15px; border-radius: 5px; cursor: pointer; font-size: 12px;">
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
            
            // Add nearby commercial establishments
            const nearbyEstablishments = [
                {
                    name: "SM City SJDM",
                    position: { lat: 14.8160, lng: 121.0460 },
                    icon: "http://maps.google.com/mapfiles/ms/icons/red-dot.png"
                },
                {
                    name: "Divine Mercy Shrine",
                    position: { lat: 14.8140, lng: 121.0500 },
                    icon: "http://maps.google.com/mapfiles/ms/icons/yellow-dot.png"
                }
            ];
            
            nearbyEstablishments.forEach(establishment => {
                new google.maps.Marker({
                    position: establishment.position,
                    map: map,
                    title: establishment.name,
                    icon: establishment.icon
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
                map.setCenter({ lat: 14.8150, lng: 121.0480 });
            }
        }
        
        function getDirections() {
            const destination = "14.8150,121.0480";
            const url = `https://www.google.com/maps/dir/?api=1&destination=${destination}&destination_place_id=RedDoorz+Hotel+San+Jose+del+Monte+Bulacan`;
            window.open(url, '_blank');
        }
        
        // Room selection function
        function selectRoom(roomType) {
            const message = `You selected: ${roomType}\n\nTo book this room:\n1. Call us at (044) 791-2222\n2. Or book online via RedDoorz app/website\n3. Mention room code: RDZ-${roomType.toUpperCase().replace(/ /g, '-')}\n4. We'll confirm your booking immediately`;
            alert(message);
        }

        // Booking modal function
        function showBookingModal() {
            alert('Book directly through:\n\n1. RedDoorz Mobile App (Available on iOS & Android)\n2. Website: www.reddoorz.com\n3. Call: (044) 791-2222\n\nBest rates guaranteed when booking directly!');
        }
        
        // Add keyboard shortcut for back button (Esc key)
        document.addEventListener('DOMContentLoaded', function() {
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    goBackToHotels();
                }
            });
            
            // Benefit card interactions
            const benefits = document.querySelectorAll('.benefit-card');
            benefits.forEach(benefit => {
                benefit.addEventListener('click', function() {
                    const benefitName = this.querySelector('h3').textContent;
                    alert(`RedDoorz Benefit: ${benefitName}\n\nAll our standard benefits are included in every room booking at no extra cost.`);
                });
            });
        });
        
        // Handle Google Maps API errors
        window.gm_authFailure = function() {
            document.getElementById("map").innerHTML = `
                <div style="height: 100%; display: flex; flex-direction: column; justify-content: center; align-items: center; text-align: center; padding: 20px;">
                    <i class="fas fa-door-closed" style="font-size: 48px; color: #666; margin-bottom: 15px;"></i>
                    <h3 style="color: #333; margin-bottom: 10px;">Map Unavailable</h3>
                    <p style="color: #666; margin-bottom: 15px;">Google Maps cannot be loaded at the moment.</p>
                    <p style="color: #e63946;"><strong>📍 RedDoorz Hotel Location:</strong></p>
                    <p style="color: #333;">City Proper, San Jose del Monte, Bulacan</p>
                    <p style="color: #333;">Coordinates: 14.8150° N, 121.0480° E</p>
                </div>
            `;
        };
    </script>
</body>
</html>