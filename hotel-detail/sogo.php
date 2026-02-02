<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sogo Hotel - San Jose del Monte, Bulacan</title>
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
            <h1><i class="fas fa-hotel"></i> Sogo Hotel</h1>
            <p class="tagline">Convenient & Affordable Accommodation in San Jose del Monte, Bulacan</p>
        </header>

        <div class="hotel-info">
            <div>
                <img src="https://images.unsplash.com/photo-1611892440504-42a792e24d32?q=80&w=2070&auto=format&fit=crop" alt="Sogo Hotel" class="main-image">
            </div>
            <div class="details-box">
                <h2>Hotel Overview</h2>
                <div class="detail-item">
                    <i class="fas fa-map-marker-alt"></i>
                    <div>
                        <strong>Location:</strong><br>
                        Muzon Road, San Jose del Monte, Bulacan
                    </div>
                </div>
                <button class="btn" onclick="showBookingModal()">
                    <i class="fas fa-clock"></i> Book Now - 24/7
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
                            <li><i class="fas fa-map-pin"></i> <strong>Full Address:</strong> Muzon Road, San Jose del Monte, Bulacan 3023</li>
                            <li><i class="fas fa-compass"></i> <strong>Coordinates:</strong> 14.8125° N, 121.0465° E</li>
                            <li><i class="fas fa-building"></i> <strong>Hotel Type:</strong> 24-hour convenience hotel</li>
                            <li><i class="fas fa-clock"></i> <strong>Operating Hours:</strong> Open 24 hours, 7 days a week</li>
                        </ul>
                    </div>
                    
                    <div class="location-info">
                        <h3><i class="fas fa-directions"></i> How to Get Here</h3>
                        <ul>
                            <li><i class="fas fa-car"></i> <strong>From NLEX:</strong> Exit at Bocaue, take Quirino Highway to Muzon Road</li>
                            <li><i class="fas fa-bus"></i> <strong>Public Transport:</strong> Jeepneys and buses from Manila to SJDM</li>
                            <li><i class="fas fa-clock"></i> <strong>Travel Time:</strong> 35 minutes from Quezon City</li>
                            <li><i class="fas fa-store"></i> <strong>Convenience:</strong> Near commercial areas and transport terminals</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- HOURLY RATES SECTION -->
        <div class="packages-section">
            <h2><i class="fas fa-clock"></i> Hourly & Overnight Rates</h2>
            <div class="hourly-rates">
                
                <div class="rate-card">
                    <i class="fas fa-bed" style="font-size: 2.5rem; color: #cc0000; margin-bottom: 15px;"></i>
                    <h3>3-Hour Stay</h3>
                    <p class="room-price">₱450</p>
                    <p>Short rest period</p>
                </div>

                <div class="rate-card">
                    <i class="fas fa-sun" style="font-size: 2.5rem; color: #cc0000; margin-bottom: 15px;"></i>
                    <h3>6-Hour Day Use</h3>
                    <p class="room-price">₱750</p>
                    <p>Daytime accommodation</p>
                </div>

                <div class="rate-card">
                    <i class="fas fa-moon" style="font-size: 2.5rem; color: #cc0000; margin-bottom: 15px;"></i>
                    <h3>12-Hour Overnight</h3>
                    <p class="room-price">₱1,200</p>
                    <p>Full night's stay</p>
                </div>

                <div class="rate-card">
                    <i class="fas fa-calendar-day" style="font-size: 2.5rem; color: #cc0000; margin-bottom: 15px;"></i>
                    <h3>24-Hour Stay</h3>
                    <p class="room-price">₱1,800</p>
                    <p>Full day accommodation</p>
                </div>

            </div>
        </div>

        <!-- SOGO BENEFITS SECTION -->
        <div class="packages-section">
            <h2><i class="fas fa-award"></i> Sogo Hotel Benefits</h2>
            <div class="benefits-grid">
                
                <div class="benefit-card">
                    <i class="fas fa-door-open" style="font-size: 2rem; color: #cc0000; margin-bottom: 10px;"></i>
                    <h3>24/7 Access</h3>
                    <p>Check-in anytime, day or night</p>
                </div>

                <div class="benefit-card">
                    <i class="fas fa-shield-alt" style="font-size: 2rem; color: #cc0000; margin-bottom: 10px;"></i>
                    <h3>Privacy & Security</h3>
                    <p>Discreet and secure environment</p>
                </div>

                <div class="benefit-card">
                    <i class="fas fa-snowflake" style="font-size: 2rem; color: #cc0000; margin-bottom: 10px;"></i>
                    <h3>Air Conditioning</h3>
                    <p>Individually controlled units</p>
                </div>

                <div class="benefit-card">
                    <i class="fas fa-tv" style="font-size: 2rem; color: #cc0000; margin-bottom: 10px;"></i>
                    <h3>Cable TV</h3>
                    <p>Entertainment channels included</p>
                </div>

            </div>
        </div>

        <!-- ROOMS SECTION -->
        <div class="rooms-section">
            <h2><i class="fas fa-bed"></i> Room Types</h2>
            <div class="rooms-grid">
                
                <!-- Standard Room Card -->
                <div class="room-card popular">
                    <div class="popular-badge">MOST BOOKED</div>
                    <div class="room-header">
                        <h3>Standard Room</h3>
                        <div class="room-badge">Single/Double</div>
                        <div class="room-price">₱1,200<span>/12 hours</span></div>
                    </div>
                    <div class="room-content">
                        <ul class="room-features">
                            <li><i class="fas fa-check-circle"></i> 20 sqm clean room</li>
                            <li><i class="fas fa-check-circle"></i> Queen-size bed</li>
                            <li><i class="fas fa-check-circle"></i> Air conditioning</li>
                            <li><i class="fas fa-check-circle"></i> Cable TV</li>
                            <li><i class="fas fa-check-circle"></i> Private bathroom</li>
                            <li><i class="fas fa-check-circle"></i> Hot and cold shower</li>
                        </ul>
                    </div>
                    <div class="room-actions">
                        <button class="btn" onclick="selectRoom('Standard Room')">
                            <i class="fas fa-book"></i> Book This Room
                        </button>
                    </div>
                </div>

                <!-- Deluxe Room Card -->
                <div class="room-card">
                    <div class="room-header">
                        <h3>Deluxe Room</h3>
                        <div class="room-badge">More Space</div>
                        <div class="room-price">₱1,500<span>/12 hours</span></div>
                    </div>
                    <div class="room-content">
                        <ul class="room-features">
                            <li><i class="fas fa-check-circle"></i> 25 sqm spacious room</li>
                            <li><i class="fas fa-check-circle"></i> King-size bed</li>
                            <li><i class="fas fa-check-circle"></i> Air conditioning</li>
                            <li><i class="fas fa-check-circle"></i> Cable TV with premium channels</li>
                            <li><i class="fas fa-check-circle"></i> Ensuite bathroom</li>
                            <li><i class="fas fa-check-circle"></i> Mini refrigerator</li>
                        </ul>
                    </div>
                    <div class="room-actions">
                        <button class="btn" onclick="selectRoom('Deluxe Room')">
                            <i class="fas fa-star"></i> Book This Room
                        </button>
                    </div>
                </div>

                <!-- VIP Suite Card -->
                <div class="room-card">
                    <div class="room-header">
                        <h3>VIP Suite</h3>
                        <div class="room-badge">Luxury Experience</div>
                        <div class="room-price">₱2,000<span>/12 hours</span></div>
                    </div>
                    <div class="room-content">
                        <ul class="room-features">
                            <li><i class="fas fa-check-circle"></i> 30 sqm suite</li>
                            <li><i class="fas fa-check-circle"></i> King-size bed with premium mattress</li>
                            <li><i class="fas fa-check-circle"></i> Separate sitting area</li>
                            <li><i class="fas fa-check-circle"></i> Jacuzzi bathtub</li>
                            <li><i class="fas fa-check-circle"></i> Mood lighting</li>
                            <li><i class="fas fa-check-circle"></i> Complimentary drinks</li>
                        </ul>
                    </div>
                    <div class="room-actions">
                        <button class="btn" onclick="selectRoom('VIP Suite')">
                            <i class="fas fa-crown"></i> Book This Suite
                        </button>
                    </div>
                </div>

            </div>
        </div>

        <div class="details-box">
            <h2>About Sogo Hotel</h2>
            <p>Sogo Hotel in San Jose del Monte provides convenient, affordable, and private accommodation with flexible hourly rates. As one of the Philippines' most recognizable hotel chains, we offer clean, comfortable rooms with essential amenities for short stays, overnight accommodation, or extended hours. Our 24/7 operation ensures you can check in anytime that suits your schedule.</p>
        </div>

        <h2 style="color: #cc0000; margin: 30px 0 20px 0;">Hotel Features</h2>
        <div class="hotel-features">
            <div class="feature-card">
                <i class="fas fa-concierge-bell"></i>
                <h3>24-Hour Reception</h3>
                <p>Round-the-clock front desk service</p>
            </div>
            <div class="feature-card">
                <i class="fas fa-parking"></i>
                <h3>Secure Parking</h3>
                <p>Protected parking area</p>
            </div>
            <div class="feature-card">
                <i class="fas fa-lock"></i>
                <h3>Privacy Assured</h3>
                <p>Discreet and confidential service</p>
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
                            (044) 791-5555 / 0917-444-3333
                        </div>
                    </li>
                    <li>
                        <i class="fas fa-clock"></i>
                        <div>
                            <strong>Operating Hours:</strong><br>
                            24 hours, 7 days a week
                        </div>
                    </li>
                    <li>
                        <i class="fas fa-credit-card"></i>
                        <div>
                            <strong>Payment Methods:</strong><br>
                            Cash and major credit cards
                        </div>
                    </li>
                </ul>
            </div>
            
            <div class="footer-section">
                <h3><i class="fas fa-share-alt"></i> Connect With Us</h3>
                <div class="social-links">
                    <a href="https://facebook.com/SogoHotelSJDM" target="_blank" title="Facebook">
                        <i class="fab fa-facebook-f"></i>
                    </a>
                    <a href="tel:+0447915555" title="Call">
                        <i class="fas fa-phone"></i>
                    </a>
                    <a href="https://maps.google.com/?q=14.8125,121.0465" target="_blank" title="Location">
                        <i class="fas fa-map-marker-alt"></i>
                    </a>
                    <a href="https://www.sogohotels.com" target="_blank" title="Website">
                        <i class="fas fa-globe"></i>
                    </a>
                </div>
                <button class="btn" onclick="showBookingModal()" style="margin-top: 20px;">
                    <i class="fas fa-clock"></i> Book Now - 24/7
                </button>
            </div>
            
            <div class="footer-section">
                <h3><i class="fas fa-hotel"></i> Hotel Details</h3>
                <ul class="contact-info">
                    <li>
                        <i class="fas fa-map-marker-alt"></i>
                        <div>
                            <strong>Location:</strong><br>
                            Muzon Road, SJDM, Bulacan
                        </div>
                    </li>
                    <li>
                        <i class="fas fa-door-closed"></i>
                        <div>
                            <strong>Rooms:</strong><br>
                            50 rooms (Standard, Deluxe, VIP)
                        </div>
                    </li>
                    <li>
                        <i class="fas fa-clock"></i>
                        <div>
                            <strong>Flexible Rates:</strong><br>
                            Hourly, day use, and overnight
                        </div>
                    </li>
                </ul>
            </div>
        </div>
        
        <div class="footer-bottom">
            <p>&copy; 2024 Sogo Hotel. All rights reserved. | Convenient & Affordable Accommodation in San Jose del Monte, Bulacan</p>
            <p style="margin-top: 10px; font-size: 0.8rem;">Sogo Hotel is a registered trademark of Sogo Hotels Philippines</p>
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
        
        // Initialize Google Map for Sogo Hotel
        function initMap() {
            // Exact coordinates for Sogo Hotel
            const hotelLocation = { 
                lat: 14.8125, 
                lng: 121.0465 
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
                title: "Sogo Hotel",
                icon: {
                    path: google.maps.SymbolPath.CIRCLE,
                    scale: 10,
                    fillColor: "#ff3333",
                    fillOpacity: 1,
                    strokeColor: "#FFFFFF",
                    strokeWeight: 2
                },
                animation: google.maps.Animation.DROP
            });
            
            infoWindow = new google.maps.InfoWindow({
                content: `
                    <div style="padding: 10px; max-width: 250px;">
                        <h3 style="color: #cc0000; margin: 0 0 10px 0; font-size: 16px;">🏨 Sogo Hotel</h3>
                        <p style="margin: 5px 0; font-size: 14px;"><strong>⏰ 24/7 Hotel Service</strong></p>
                        <p style="margin: 5px 0; font-size: 13px;"><strong>📍 Address:</strong> Muzon Road, SJDM</p>
                        <p style="margin: 5px 0; font-size: 13px;"><strong>📞 Contact:</strong> (044) 791-5555</p>
                        <div style="margin-top: 10px;">
                            <button onclick="getDirections()" style="background: #cc0000; color: white; border: none; padding: 8px 15px; border-radius: 5px; cursor: pointer; font-size: 12px;">
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
                    name: "Muzon Commercial Area",
                    position: { lat: 14.8130, lng: 121.0450 },
                    icon: "http://maps.google.com/mapfiles/ms/icons/red-dot.png"
                },
                {
                    name: "Transport Terminal",
                    position: { lat: 14.8110, lng: 121.0480 },
                    icon: "http://maps.google.com/mapfiles/ms/icons/blue-dot.png"
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
                map.setCenter({ lat: 14.8125, lng: 121.0465 });
            }
        }
        
        function getDirections() {
            const destination = "14.8125,121.0465";
            const url = `https://www.google.com/maps/dir/?api=1&destination=${destination}&destination_place_id=Sogo+Hotel+San+Jose+del+Monte+Bulacan`;
            window.open(url, '_blank');
        }
        
        // Room selection function
        function selectRoom(roomType) {
            const message = `You selected: ${roomType}\n\nTo book this room:\n1. Call us at (044) 791-5555\n2. Or walk-in anytime (24/7)\n3. Mention room type: ${roomType}\n4. Choose your preferred duration (3, 6, 12, or 24 hours)`;
            alert(message);
        }

        // Booking modal function
        function showBookingModal() {
            alert('Sogo Hotel - Walk-ins Welcome 24/7!\n\nNo need to book in advance. Simply:\n1. Visit our hotel at Muzon Road, SJDM\n2. Choose your room type\n3. Select your preferred duration\n4. Check-in immediately!\n\nFor inquiries: (044) 791-5555');
        }
        
        // Add keyboard shortcut for back button (Esc key)
        document.addEventListener('DOMContentLoaded', function() {
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    goBackToHotels();
                }
            });
            
            // Rate card interactions
            const rates = document.querySelectorAll('.rate-card');
            rates.forEach(rate => {
                rate.addEventListener('click', function() {
                    const rateName = this.querySelector('h3').textContent;
                    const ratePrice = this.querySelector('.room-price').textContent;
                    const rateDesc = this.querySelector('p:last-child').textContent;
                    alert(`Rate: ${rateName}\nPrice: ${ratePrice}\n\n${rateDesc}\n\nThis rate includes the room and basic amenities. Additional hours can be extended upon request.`);
                });
            });
        });
        
        // Handle Google Maps API errors
        window.gm_authFailure = function() {
            document.getElementById("map").innerHTML = `
                <div style="height: 100%; display: flex; flex-direction: column; justify-content: center; align-items: center; text-align: center; padding: 20px;">
                    <i class="fas fa-hotel" style="font-size: 48px; color: #666; margin-bottom: 15px;"></i>
                    <h3 style="color: #333; margin-bottom: 10px;">Map Unavailable</h3>
                    <p style="color: #666; margin-bottom: 15px;">Google Maps cannot be loaded at the moment.</p>
                    <p style="color: #cc0000;"><strong>📍 Sogo Hotel Location:</strong></p>
                    <p style="color: #333;">Muzon Road, San Jose del Monte, Bulacan</p>
                    <p style="color: #333;">Coordinates: 14.8125° N, 121.0465° E</p>
                </div>
            `;
        };
    </script>
</body>
</html>