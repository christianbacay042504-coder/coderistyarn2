<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tierra Fontana - San Jose del Monte, Bulacan</title>
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
            <h1><i class="fas fa-fountain"></i> Tierra Fontana</h1>
            <p class="tagline">Premier Hotel & Events Venue with Fountain Displays in San Jose del Monte, Bulacan</p>
        </header>

        <div class="hotel-info">
            <div>
                <img src="https://images.unsplash.com/photo-1564501049418-3c27787d01e8?q=80&w=2070&auto=format&fit=crop" alt="Tierra Fontana" class="main-image">
            </div>
            <div class="details-box">
                <h2>Hotel Overview</h2>
                <div class="detail-item">
                    <i class="fas fa-map-marker-alt"></i>
                    <div>
                        <strong>Location:</strong><br>
                        Ciudad de Victoria, San Jose del Monte, Bulacan
                    </div>
                </div>
                <button class="btn" onclick="showBookingModal()">
                    <i class="fas fa-calendar-alt"></i> Book Hotel or Event
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
                            <li><i class="fas fa-map-pin"></i> <strong>Full Address:</strong> Ciudad de Victoria, San Jose del Monte, Bulacan 3023</li>
                            <li><i class="fas fa-compass"></i> <strong>Coordinates:</strong> 14.7900° N, 121.0550° E</li>
                            <li><i class="fas fa-building"></i> <strong>Hotel Type:</strong> Premier hotel and events venue</li>
                            <li><i class="fas fa-fountain"></i> <strong>Signature Feature:</strong> Musical fountain displays</li>
                        </ul>
                    </div>
                    
                    <div class="location-info">
                        <h3><i class="fas fa-directions"></i> How to Get Here</h3>
                        <ul>
                            <li><i class="fas fa-car"></i> <strong>From NLEX:</strong> Take Bocaue exit to Ciudad de Victoria complex</li>
                            <li><i class="fas fa-bus"></i> <strong>Public Transport:</strong> Buses from Manila to Bocaue, then tricycle to Ciudad de Victoria</li>
                            <li><i class="fas fa-clock"></i> <strong>Travel Time:</strong> 45 minutes from Metro Manila</li>
                            <li><i class="fas fa-landmark"></i> <strong>Area:</strong> Part of the Ciudad de Victoria complex</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- WATER FEATURES SECTION -->
        <div class="packages-section">
            <h2><i class="fas fa-fountain"></i> Fountain & Water Features</h2>
            <div class="water-features">
                
                <div class="water-card">
                    <i class="fas fa-music" style="font-size: 2.5rem; color: #1a73e8; margin-bottom: 15px;"></i>
                    <h3>Musical Fountain</h3>
                    <p>Evening light and music show</p>
                </div>

                <div class="water-card">
                    <i class="fas fa-swimming-pool" style="font-size: 2.5rem; color: #1a73e8; margin-bottom: 15px;"></i>
                    <h3>Infinity Pool</h3>
                    <p>Scenic pool with fountain views</p>
                </div>

                <div class="water-card">
                    <i class="fas fa-water" style="font-size: 2.5rem; color: #1a73e8; margin-bottom: 15px;"></i>
                    <h3>Reflection Pool</h3>
                    <p>Calm water feature for meditation</p>
                </div>

                <div class="water-card">
                    <i class="fas fa-child" style="font-size: 2.5rem; color: #1a73e8; margin-bottom: 15px;"></i>
                    <h3>Interactive Fountains</h3>
                    <p>Children's play fountain area</p>
                </div>

            </div>
        </div>

        <!-- EVENT SPACES SECTION -->
        <div class="packages-section">
            <h2><i class="fas fa-glass-cheers"></i> Event & Function Spaces</h2>
            <div class="event-spaces">
                
                <div class="event-card">
                    <i class="fas fa-ring" style="font-size: 2rem; color: #1a73e8; margin-bottom: 15px;"></i>
                    <h3>Grand Ballroom</h3>
                    <p>Capacity: 800 guests<br>With fountain view</p>
                </div>

                <div class="event-card">
                    <i class="fas fa-briefcase" style="font-size: 2rem; color: #1a73e8; margin-bottom: 15px;"></i>
                    <h3>Conference Center</h3>
                    <p>Capacity: 500 guests<br>Business events</p>
                </div>

                <div class="event-card">
                    <i class="fas fa-utensils" style="font-size: 2rem; color: #1a73e8; margin-bottom: 15px;"></i>
                    <h3>Garden Pavilion</h3>
                    <p>Capacity: 300 guests<br>Outdoor events</p>
                </div>

                <div class="event-card">
                    <i class="fas fa-camera" style="font-size: 2rem; color: #1a73e8; margin-bottom: 15px;"></i>
                    <h3>Fountain View Terrace</h3>
                    <p>Capacity: 150 guests<br>Intimate gatherings</p>
                </div>

            </div>
        </div>

        <!-- ROOMS SECTION -->
        <div class="rooms-section">
            <h2><i class="fas fa-bed"></i> Luxury Accommodations</h2>
            <div class="rooms-grid">
                
                <!-- Fountain View Room Card -->
                <div class="room-card popular">
                    <div class="popular-badge">SIGNATURE ROOM</div>
                    <div class="room-header">
                        <h3>Fountain View Room</h3>
                        <div class="room-badge">Direct Fountain View</div>
                        <div class="room-price">₱4,500<span>/night</span></div>
                    </div>
                    <div class="room-content">
                        <ul class="room-features">
                            <li><i class="fas fa-check-circle"></i> 30 sqm room with balcony</li>
                            <li><i class="fas fa-check-circle"></i> King-size bed</li>
                            <li><i class="fas fa-check-circle"></i> Direct view of musical fountain</li>
                            <li><i class="fas fa-check-circle"></i> Air conditioning</li>
                            <li><i class="fas fa-check-circle"></i> Free high-speed WiFi</li>
                            <li><i class="fas fa-check-circle"></i> Premium bathroom amenities</li>
                        </ul>
                    </div>
                    <div class="room-actions">
                        <button class="btn" onclick="selectRoom('Fountain View Room')">
                            <i class="fas fa-fountain"></i> Book This Room
                        </button>
                    </div>
                </div>

                <!-- Executive Suite Card -->
                <div class="room-card">
                    <div class="room-header">
                        <h3>Executive Suite</h3>
                        <div class="room-badge">Business Luxury</div>
                        <div class="room-price">₱6,200<span>/night</span></div>
                    </div>
                    <div class="room-content">
                        <ul class="room-features">
                            <li><i class="fas fa-check-circle"></i> 45 sqm suite with living area</li>
                            <li><i class="fas fa-check-circle"></i> King-size bed</li>
                            <li><i class="fas fa-check-circle"></i> Separate work station</li>
                            <li><i class="fas fa-check-circle"></i> Complimentary executive lounge access</li>
                            <li><i class="fas fa-check-circle"></i> Evening turndown service</li>
                            <li><i class="fas fa-check-circle"></i> Jacuzzi bathtub</li>
                        </ul>
                    </div>
                    <div class="room-actions">
                        <button class="btn" onclick="selectRoom('Executive Suite')">
                            <i class="fas fa-briefcase"></i> Book This Suite
                        </button>
                    </div>
                </div>

                <!-- Presidential Suite Card -->
                <div class="room-card">
                    <div class="room-header">
                        <h3>Presidential Suite</h3>
                        <div class="room-badge">Ultimate Luxury</div>
                        <div class="room-price">₱12,000<span>/night</span></div>
                    </div>
                    <div class="room-content">
                        <ul class="room-features">
                            <li><i class="fas fa-check-circle"></i> 80 sqm luxury suite</li>
                            <li><i class="fas fa-check-circle"></i> Two bedrooms</li>
                            <li><i class="fas fa-check-circle"></i> Formal dining area</li>
                            <li><i class="fas fa-check-circle"></i> Private terrace with fountain view</li>
                            <li><i class="fas fa-check-circle"></i> Butler service</li>
                            <li><i class="fas fa-check-circle"></i> Personal sauna and steam room</li>
                        </ul>
                    </div>
                    <div class="room-actions">
                        <button class="btn" onclick="selectRoom('Presidential Suite')">
                            <i class="fas fa-crown"></i> Book This Suite
                        </button>
                    </div>
                </div>

            </div>
        </div>

        <!-- TIERRA FEATURES -->
        <div class="packages-section">
            <h2><i class="fas fa-award"></i> Tierra Fontana Features</h2>
            <div class="tierra-features">
                
                <div class="tierra-card">
                    <i class="fas fa-lightbulb" style="font-size: 2rem; color: #1a73e8; margin-bottom: 10px;"></i>
                    <h3>LED Fountain Show</h3>
                    <p>Nightly musical fountain display</p>
                </div>

                <div class="tierra-card">
                    <i class="fas fa-utensils" style="font-size: 2rem; color: #1a73e8; margin-bottom: 10px;"></i>
                    <h3>Fine Dining</h3>
                    <p>Multiple restaurant options</p>
                </div>

                <div class="tierra-card">
                    <i class="fas fa-spa" style="font-size: 2rem; color: #1a73e8; margin-bottom: 10px;"></i>
                    <h3>Wellness Center</h3>
                    <p>Spa and fitness facilities</p>
                </div>

                <div class="tierra-card">
                    <i class="fas fa-parking" style="font-size: 2rem; color: #1a73e8; margin-bottom: 10px;"></i>
                    <h3>Ample Parking</h3>
                    <p>Secure parking for 500+ vehicles</p>
                </div>

            </div>
        </div>

        <div class="details-box">
            <h2>About Tierra Fontana</h2>
            <p>Tierra Fontana is a premier hotel and events venue located within the prestigious Ciudad de Victoria complex in San Jose del Monte. Known for its spectacular musical fountain displays, this luxury hotel combines modern elegance with world-class amenities. Perfect for weddings, corporate events, and luxury getaways, Tierra Fontana offers an unforgettable experience with its signature fountain shows and exceptional service.</p>
        </div>

        <h2 style="color: #1a73e8; margin: 30px 0 20px 0;">Hotel Features & Amenities</h2>
        <div class="hotel-features">
            <div class="feature-card">
                <i class="fas fa-restaurant"></i>
                <h3>Multiple Restaurants</h3>
                <p>International and local cuisine</p>
            </div>
            <div class="feature-card">
                <i class="fas fa-dumbbell"></i>
                <h3>Fitness Center</h3>
                <p>24-hour gym with modern equipment</p>
            </div>
            <div class="feature-card">
                <i class="fas fa-concierge-bell"></i>
                <h3>Concierge Service</h3>
                <p>Personalized guest services</p>
            </div>
        </div>

        <!-- NEARBY ATTRACTIONS -->
        <div class="details-box">
            <h2>Nearby Attractions in Ciudad de Victoria</h2>
            <div class="tierra-features">
                <div class="tierra-card">
                    <i class="fas fa-basketball-ball"></i>
                    <h3>Philippine Arena</h3>
                    <p>World's largest indoor arena</p>
                </div>
                <div class="tierra-card">
                    <i class="fas fa-shopping-bag"></i>
                    <h3>Mall</h3>
                    <p>Shopping and entertainment</p>
                </div>
                <div class="tierra-card">
                    <i class="fas fa-church"></i>
                    <h3>Iglesia ni Cristo</h3>
                    <p>Religious complex</p>
                </div>
                <div class="tierra-card">
                    <i class="fas fa-gamepad"></i>
                    <h3>Entertainment Zone</h3>
                    <p>Recreation and leisure area</p>
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
                            (044) 791-6666 / 0918-777-8888
                        </div>
                    </li>
                    <li>
                        <i class="fas fa-envelope"></i>
                        <div>
                            <strong>Email:</strong><br>
                            reservations@tierrafontana.com
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
                    <a href="https://facebook.com/TierraFontanaHotel" target="_blank" title="Facebook">
                        <i class="fab fa-facebook-f"></i>
                    </a>
                    <a href="mailto:reservations@tierrafontana.com" title="Email">
                        <i class="fas fa-envelope"></i>
                    </a>
                    <a href="tel:+0447916666" title="Call">
                        <i class="fas fa-phone"></i>
                    </a>
                    <a href="https://maps.google.com/?q=14.7900,121.0550" target="_blank" title="Location">
                        <i class="fas fa-map-marker-alt"></i>
                    </a>
                </div>
                <button class="btn" onclick="showBookingModal()" style="margin-top: 20px;">
                    <i class="fas fa-calendar-alt"></i> Book Hotel or Event
                </button>
            </div>
            
            <div class="footer-section">
                <h3><i class="fas fa-fountain"></i> Hotel Details</h3>
                <ul class="contact-info">
                    <li>
                        <i class="fas fa-map-marker-alt"></i>
                        <div>
                            <strong>Location:</strong><br>
                            Ciudad de Victoria, SJDM, Bulacan
                        </div>
                    </li>
                    <li>
                        <i class="fas fa-users"></i>
                        <div>
                            <strong>Event Capacity:</strong><br>
                            Up to 800 guests in Grand Ballroom
                        </div>
                    </li>
                    <li>
                        <i class="fas fa-music"></i>
                        <div>
                            <strong>Signature:</strong><br>
                            Musical fountain shows nightly
                        </div>
                    </li>
                </ul>
            </div>
        </div>
        
        <div class="footer-bottom">
            <p>&copy; 2024 Tierra Fontana. All rights reserved. | Premier Hotel & Events Venue in San Jose del Monte, Bulacan</p>
            <p style="margin-top: 10px; font-size: 0.8rem;">Part of the Ciudad de Victoria complex - Home of the Philippine Arena</p>
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
        
        // Initialize Google Map for Tierra Fontana
        function initMap() {
            // Exact coordinates for Tierra Fontana in Ciudad de Victoria
            const hotelLocation = { 
                lat: 14.7900, 
                lng: 121.0550 
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
                title: "Tierra Fontana",
                icon: {
                    path: google.maps.SymbolPath.CIRCLE,
                    scale: 10,
                    fillColor: "#4285f4",
                    fillOpacity: 1,
                    strokeColor: "#FFFFFF",
                    strokeWeight: 2
                },
                animation: google.maps.Animation.DROP
            });
            
            infoWindow = new google.maps.InfoWindow({
                content: `
                    <div style="padding: 10px; max-width: 250px;">
                        <h3 style="color: #1a73e8; margin: 0 0 10px 0; font-size: 16px;">⛲ Tierra Fontana</h3>
                        <p style="margin: 5px 0; font-size: 14px;"><strong>🎵 Musical Fountain Hotel</strong></p>
                        <p style="margin: 5px 0; font-size: 13px;"><strong>📍 Address:</strong> Ciudad de Victoria, SJDM</p>
                        <p style="margin: 5px 0; font-size: 13px;"><strong>📞 Contact:</strong> (044) 791-6666</p>
                        <div style="margin-top: 10px;">
                            <button onclick="getDirections()" style="background: #1a73e8; color: white; border: none; padding: 8px 15px; border-radius: 5px; cursor: pointer; font-size: 12px;">
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
            
            // Add nearby Ciudad de Victoria landmarks
            const nearbyLandmarks = [
                {
                    name: "Philippine Arena",
                    position: { lat: 14.7880, lng: 121.0530 },
                    icon: "http://maps.google.com/mapfiles/ms/icons/blue-dot.png"
                },
                {
                    name: "Mall",
                    position: { lat: 14.7920, lng: 121.0570 },
                    icon: "http://maps.google.com/mapfiles/ms/icons/red-dot.png"
                }
            ];
            
            nearbyLandmarks.forEach(landmark => {
                new google.maps.Marker({
                    position: landmark.position,
                    map: map,
                    title: landmark.name,
                    icon: landmark.icon
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
                map.setCenter({ lat: 14.7900, lng: 121.0550 });
            }
        }
        
        function getDirections() {
            const destination = "14.7900,121.0550";
            const url = `https://www.google.com/maps/dir/?api=1&destination=${destination}&destination_place_id=Tierra+Fontana+Ciudad+de+Victoria+San+Jose+del+Monte+Bulacan`;
            window.open(url, '_blank');
        }
        
        // Room selection function
        function selectRoom(roomType) {
            const message = `You selected: ${roomType}\n\nTo book this room:\n1. Call us at (044) 791-6666\n2. Mention room code: TF-${roomType.toUpperCase().replace(/ /g, '-')}\n3. Provide your check-in/check-out dates\n4. We'll send you a confirmation within 24 hours`;
            alert(message);
        }

        // Booking modal function
        function showBookingModal() {
            alert('For hotel bookings or event inquiries, please call (044) 791-6666 or email reservations@tierrafontana.com\n\nMusical fountain shows occur nightly at 7:00 PM and 8:30 PM. Fountain View Rooms offer the best experience!');
        }
        
        // Add keyboard shortcut for back button (Esc key)
        document.addEventListener('DOMContentLoaded', function() {
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    goBackToHotels();
                }
            });
            
            // Water feature interactions
            const waterFeatures = document.querySelectorAll('.water-card');
            waterFeatures.forEach(feature => {
                feature.addEventListener('click', function() {
                    const featureName = this.querySelector('h3').textContent;
                    const featureDesc = this.querySelector('p').textContent;
                    alert(`Water Feature: ${featureName}\n\n${featureDesc}\n\nOur musical fountain show is the highlight of Tierra Fontana, featuring synchronized lights, water, and music.`);
                });
            });
            
            // Event space interactions
            const events = document.querySelectorAll('.event-card');
            events.forEach(eventCard => {
                eventCard.addEventListener('click', function() {
                    const eventName = this.querySelector('h3').textContent;
                    const eventDetails = this.querySelector('p').textContent;
                    alert(`Event Space: ${eventName}\n\n${eventDetails}\n\nPerfect for weddings, corporate events, and special celebrations. Our event coordinators provide full planning services.`);
                });
            });
        });
        
        // Handle Google Maps API errors
        window.gm_authFailure = function() {
            document.getElementById("map").innerHTML = `
                <div style="height: 100%; display: flex; flex-direction: column; justify-content: center; align-items: center; text-align: center; padding: 20px;">
                    <i class="fas fa-fountain" style="font-size: 48px; color: #666; margin-bottom: 15px;"></i>
                    <h3 style="color: #333; margin-bottom: 10px;">Map Unavailable</h3>
                    <p style="color: #666; margin-bottom: 15px;">Google Maps cannot be loaded at the moment.</p>
                    <p style="color: #1a73e8;"><strong>📍 Tierra Fontana Location:</strong></p>
                    <p style="color: #333;">Ciudad de Victoria, San Jose del Monte, Bulacan</p>
                    <p style="color: #333;">Coordinates: 14.7900° N, 121.0550° E</p>
                </div>
            `;
        };
    </script>
</body>
</html>