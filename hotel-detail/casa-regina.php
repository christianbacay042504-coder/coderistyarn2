<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Casa Regina Resorts - San Jose del Monte, Bulacan</title>
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
        --star-color: #ffc107;
        --star-empty: #e0e0e0;
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
    
    /* RATING SYSTEM STYLES */
    .rating-section {
        background: var(--pure-white);
        padding: 30px;
        border-radius: 15px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        margin: 40px 0;
    }
    
    .rating-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
        flex-wrap: wrap;
        gap: 20px;
    }
    
    .overall-rating {
        text-align: center;
        padding: 20px;
        background: rgba(151, 188, 98, 0.1);
        border-radius: 10px;
        min-width: 200px;
    }
    
    .rating-score {
        font-size: 3.5rem;
        font-weight: bold;
        color: var(--primary-green);
        line-height: 1;
    }
    
    .rating-stars {
        display: flex;
        gap: 3px;
        margin: 10px 0;
        justify-content: center;
    }
    
    .rating-star {
        color: var(--star-empty);
        font-size: 1.5rem;
        cursor: pointer;
        transition: color 0.2s;
    }
    
    .rating-star.active {
        color: var(--star-color);
    }
    
    .rating-star.half {
        position: relative;
        overflow: hidden;
    }
    
    .rating-star.half:before {
        content: "★";
        position: absolute;
        left: 0;
        width: 50%;
        overflow: hidden;
        color: var(--star-color);
    }
    
    .rating-count {
        color: #666;
        font-size: 0.9rem;
    }
    
    .rating-breakdown {
        flex: 1;
        max-width: 500px;
    }
    
    .rating-bar {
        display: flex;
        align-items: center;
        margin-bottom: 10px;
    }
    
    .rating-label {
        width: 100px;
        font-size: 0.9rem;
        color: #555;
    }
    
    .rating-bar-container {
        flex: 1;
        height: 8px;
        background: #eee;
        border-radius: 4px;
        overflow: hidden;
        margin: 0 10px;
    }
    
    .rating-bar-fill {
        height: 100%;
        background: var(--accent-green);
        border-radius: 4px;
    }
    
    .rating-percentage {
        width: 40px;
        text-align: right;
        font-size: 0.9rem;
        color: #555;
    }
    
    .user-reviews {
        margin-top: 40px;
    }
    
    .review-card {
        background: #f9f9f9;
        padding: 20px;
        border-radius: 10px;
        margin-bottom: 20px;
        border-left: 4px solid var(--accent-green);
    }
    
    .review-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 10px;
        flex-wrap: wrap;
    }
    
    .reviewer-name {
        font-weight: bold;
        color: var(--primary-green);
    }
    
    .review-date {
        color: #888;
        font-size: 0.9rem;
    }
    
    .add-review-btn {
        background: var(--accent-green);
        color: var(--pure-white);
        border: none;
        padding: 12px 25px;
        border-radius: 8px;
        font-weight: bold;
        cursor: pointer;
        transition: 0.3s;
        display: flex;
        align-items: center;
        gap: 8px;
        margin: 20px auto;
    }
    
    .add-review-btn:hover {
        background: var(--primary-green);
    }
    
    /* MODAL STYLES */
    .modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.5);
        z-index: 1000;
        justify-content: center;
        align-items: center;
    }
    
    .modal-content {
        background: var(--pure-white);
        padding: 30px;
        border-radius: 15px;
        width: 90%;
        max-width: 500px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.2);
    }
    
    .modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }
    
    .modal-title {
        color: var(--primary-green);
        font-size: 1.5rem;
    }
    
    .close-modal {
        background: none;
        border: none;
        font-size: 1.5rem;
        cursor: pointer;
        color: #888;
    }
    
    .rating-input {
        text-align: center;
        margin: 20px 0;
    }
    
    .rating-input-title {
        margin-bottom: 10px;
        color: #555;
    }
    
    .rating-input-stars {
        display: flex;
        justify-content: center;
        gap: 10px;
        margin: 20px 0;
    }
    
    .input-star {
        font-size: 2.5rem;
        color: var(--star-empty);
        cursor: pointer;
        transition: color 0.2s;
    }
    
    .input-star.active {
        color: var(--star-color);
    }
    
    .form-group {
        margin-bottom: 20px;
    }
    
    .form-group label {
        display: block;
        margin-bottom: 8px;
        color: #555;
        font-weight: 500;
    }
    
    .form-group input,
    .form-group textarea {
        width: 100%;
        padding: 12px;
        border: 1px solid #ddd;
        border-radius: 8px;
        font-size: 1rem;
        transition: border 0.3s;
    }
    
    .form-group input:focus,
    .form-group textarea:focus {
        outline: none;
        border-color: var(--accent-green);
    }
    
    .submit-review-btn {
        background: var(--accent-green);
        color: var(--pure-white);
        border: none;
        padding: 12px 30px;
        border-radius: 8px;
        font-weight: bold;
        cursor: pointer;
        transition: 0.3s;
        width: 100%;
        font-size: 1rem;
    }
    
    .submit-review-btn:hover {
        background: var(--primary-green);
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
        
        .rating-header {
            flex-direction: column;
            text-align: center;
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
            <h1><i class="fas fa-hotel"></i> Casa Regina Resorts</h1>
            <p class="tagline">Premier Event Resort & Hotel in San Jose del Monte, Bulacan</p>
        </header>

        <div class="hotel-info">
            <div>
                <img src="https://images.unsplash.com/photo-1542314831-068cd1dbfeeb?q=80&w=2070&auto=format&fit=crop" alt="Casa Regina Resort" class="main-image">
            </div>
            <div class="details-box">
                <h2>Resort Overview</h2>
                <div class="detail-item">
                    <i class="fas fa-map-marker-alt"></i>
                    <div>
                        <strong>Location:</strong><br>
                        Tungkong Mangga, San Jose del Monte, Bulacan
                    </div>
                </div>
                <div class="detail-item">
                    <i class="fas fa-star"></i>
                    <div>
                        <strong>Category:</strong><br>
                        Premium Event Resort & Hotel
                    </div>
                </div>
                <div class="detail-item">
                    <i class="fas fa-phone"></i>
                    <div>
                        <strong>Contact:</strong><br>
                        (044) 760-1234 / 0917-123-4567
                    </div>
                </div>
                <button class="btn" onclick="showBookingModal()">
                    <i class="fas fa-calendar-check"></i> Book Now
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
                            <li><i class="fas fa-map-pin"></i> <strong>Full Address:</strong> Lot 2, Block 3, Tungkong Mangga Road, San Jose del Monte, Bulacan 3023</li>
                            <li><i class="fas fa-compass"></i> <strong>Coordinates:</strong> 14.8136° N, 121.0453° E</li>
                            <li><i class="fas fa-road"></i> <strong>Nearest Landmark:</strong> Near San Jose del Monte City Hall</li>
                            <li><i class="fas fa-church"></i> <strong>Nearby:</strong> St. Joseph the Worker Parish</li>
                        </ul>
                    </div>
                    
                    <div class="location-info">
                        <h3><i class="fas fa-directions"></i> How to Get Here</h3>
                        <ul>
                            <li><i class="fas fa-car"></i> <strong>From NLEX:</strong> Exit at Bocaue, head toward Tungkong Mangga via Quirino Highway</li>
                            <li><i class="fas fa-bus"></i> <strong>Public Transport:</strong> Take buses bound for Tungkong Mangga from Cubao or Monumento</li>
                            <li><i class="fas fa-clock"></i> <strong>Travel Time:</strong> 45-60 minutes from Metro Manila</li>
                            <li><i class="fas fa-parking"></i> <strong>Parking:</strong> Available for 100+ vehicles</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- PACKAGES SECTION -->
        <div class="packages-section">
            <h2><i class="fas fa-gift"></i> Resort Packages</h2>
            <div class="packages-grid">
                
                <!-- Wedding Package Card -->
                <div class="package-card popular">
                    <div class="popular-badge">MOST POPULAR</div>
                    <div class="package-header">
                        <h3>Royal Wedding Package</h3>
                        <div class="package-badge">Perfect for 150-200 Guests</div>
                        <div class="package-price">₱188,888<span>/package</span></div>
                    </div>
                    <div class="package-content">
                        <ul class="package-features">
                            <li><i class="fas fa-check-circle"></i> Grand Ballroom for 8 hours</li>
                            <li><i class="fas fa-check-circle"></i> Catering for 150 persons</li>
                            <li><i class="fas fa-check-circle"></i> Bridal & Groom suites</li>
                            <li><i class="fas fa-check-circle"></i> Professional sound system</li>
                            <li><i class="fas fa-check-circle"></i> Floral decorations</li>
                            <li><i class="fas fa-check-circle"></i> Wedding coordinator</li>
                        </ul>
                    </div>
                    <div class="package-actions">
                        <button class="btn" onclick="selectPackage('Royal Wedding Package')">
                            <i class="fas fa-ring"></i> Select Package
                        </button>
                    </div>
                </div>

                <!-- Corporate Package Card -->
                <div class="package-card">
                    <div class="package-header">
                        <h3>Corporate Event Package</h3>
                        <div class="package-badge">Ideal for 50-100 Delegates</div>
                        <div class="package-price">₱85,000<span>/day</span></div>
                    </div>
                    <div class="package-content">
                        <ul class="package-features">
                            <li><i class="fas fa-check-circle"></i> Seminar room with LCD projector</li>
                            <li><i class="fas fa-check-circle"></i> 2 coffee breaks + lunch buffet</li>
                            <li><i class="fas fa-check-circle"></i> Sound system & microphone</li>
                            <li><i class="fas fa-check-circle"></i> Whiteboard & flipchart</li>
                            <li><i class="fas fa-check-circle"></i> WiFi internet access</li>
                            <li><i class="fas fa-check-circle"></i> Team building facilities</li>
                        </ul>
                    </div>
                    <div class="package-actions">
                        <button class="btn" onclick="selectPackage('Corporate Event Package')">
                            <i class="fas fa-briefcase"></i> Select Package
                        </button>
                    </div>
                </div>

                <!-- Family Package Card -->
                <div class="package-card">
                    <div class="package-header">
                        <h3>Family Getaway Package</h3>
                        <div class="package-badge">Perfect for 4-6 Persons</div>
                        <div class="package-price">₱12,500<span>/night</span></div>
                    </div>
                    <div class="package-content">
                        <ul class="package-features">
                            <li><i class="fas fa-check-circle"></i> Family suite for 2 nights</li>
                            <li><i class="fas fa-check-circle"></i> Breakfast for 4 persons</li>
                            <li><i class="fas fa-check-circle"></i> Pool access all day</li>
                            <li><i class="fas fa-check-circle"></i> BBQ pit & grill</li>
                            <li><i class="fas fa-check-circle"></i> Free use of karaoke</li>
                            <li><i class="fas fa-check-circle"></i> Children's playground access</li>
                        </ul>
                    </div>
                    <div class="package-actions">
                        <button class="btn" onclick="selectPackage('Family Getaway Package')">
                            <i class="fas fa-users"></i> Select Package
                        </button>
                    </div>
                </div>

            </div>
        </div>

        <div class="details-box">
            <h2>About Casa Regina Resorts</h2>
            <p>Casa Regina Resorts is one of the most prestigious event venues in San Jose del Monte, Bulacan. Known for its elegant architecture, spacious gardens, and comprehensive facilities, it has become the go-to destination for weddings, corporate events, family reunions, and special celebrations in Northern Bulacan.</p>
        </div>

        <h2 style="color: var(--primary-green); margin: 30px 0 20px 0;">Resort Features & Amenities</h2>
        <div class="amenities">
            <div class="amenity-card">
                <i class="fas fa-glass-cheers"></i>
                <h3>Grand Ballroom</h3>
                <p>Capacity for 500 guests, state-of-the-art sound and lighting system</p>
            </div>
            <div class="amenity-card">
                <i class="fas fa-swimming-pool"></i>
                <h3>Swimming Pools</h3>
                <p>Adult pool, kiddie pool, and jacuzzi with waterfall features</p>
            </div>
            <div class="amenity-card">
                <i class="fas fa-utensils"></i>
                <h3>In-house Catering</h3>
                <p>Professional catering team serving Filipino and international cuisine</p>
            </div>
            <div class="amenity-card">
                <i class="fas fa-bed"></i>
                <h3>Accommodation</h3>
                <p>25 air-conditioned rooms ranging from standard to suite rooms</p>
            </div>
        </div>

        <!-- RATING SECTION - MOVED TO BEFORE FOOTER -->
        <div class="rating-section">
            <div class="rating-header">
                <div class="overall-rating">
                    <div class="rating-score" id="overallRating">4.8</div>
                    <div class="rating-stars" id="overallStars">
                        <span class="rating-star active">★</span>
                        <span class="rating-star active">★</span>
                        <span class="rating-star active">★</span>
                        <span class="rating-star active">★</span>
                        <span class="rating-star half">★</span>
                    </div>
                    <div class="rating-count" id="totalReviews">Based on 247 reviews</div>
                </div>
                
                <div class="rating-breakdown">
                    <div class="rating-bar">
                        <div class="rating-label">Excellent</div>
                        <div class="rating-bar-container">
                            <div class="rating-bar-fill" style="width: 85%"></div>
                        </div>
                        <div class="rating-percentage">85%</div>
                    </div>
                    <div class="rating-bar">
                        <div class="rating-label">Very Good</div>
                        <div class="rating-bar-container">
                            <div class="rating-bar-fill" style="width: 12%"></div>
                        </div>
                        <div class="rating-percentage">12%</div>
                    </div>
                    <div class="rating-bar">
                        <div class="rating-label">Average</div>
                        <div class="rating-bar-container">
                            <div class="rating-bar-fill" style="width: 2%"></div>
                        </div>
                        <div class="rating-percentage">2%</div>
                    </div>
                    <div class="rating-bar">
                        <div class="rating-label">Poor</div>
                        <div class="rating-bar-container">
                            <div class="rating-bar-fill" style="width: 1%"></div>
                        </div>
                        <div class="rating-percentage">1%</div>
                    </div>
                    <div class="rating-bar">
                        <div class="rating-label">Terrible</div>
                        <div class="rating-bar-container">
                            <div class="rating-bar-fill" style="width: 0%"></div>
                        </div>
                        <div class="rating-percentage">0%</div>
                    </div>
                </div>
            </div>
            
            <div class="user-reviews">
                <h3 style="color: var(--primary-green); margin-bottom: 20px;">Recent Reviews</h3>
                
                <div class="review-card">
                    <div class="review-header">
                        <div class="reviewer-name">Maria Santos</div>
                        <div class="review-date">March 15, 2024</div>
                    </div>
                    <div class="rating-stars">
                        <span class="rating-star active">★</span>
                        <span class="rating-star active">★</span>
                        <span class="rating-star active">★</span>
                        <span class="rating-star active">★</span>
                        <span class="rating-star active">★</span>
                    </div>
                    <p>"Absolutely perfect venue for our wedding! The staff was incredibly professional and the facilities were stunning. Everything went smoothly from start to finish."</p>
                </div>
                
                <div class="review-card">
                    <div class="review-header">
                        <div class="reviewer-name">John Dela Cruz</div>
                        <div class="review-date">February 28, 2024</div>
                    </div>
                    <div class="rating-stars">
                        <span class="rating-star active">★</span>
                        <span class="rating-star active">★</span>
                        <span class="rating-star active">★</span>
                        <span class="rating-star active">★</span>
                        <span class="rating-star">★</span>
                    </div>
                    <p>"Great corporate retreat venue. The seminar rooms were well-equipped and the food was excellent. The pool area was a nice bonus after meetings."</p>
                </div>
                
                <div class="review-card">
                    <div class="review-header">
                        <div class="reviewer-name">Sophia Reyes</div>
                        <div class="review-date">February 10, 2024</div>
                    </div>
                    <div class="rating-stars">
                        <span class="rating-star active">★</span>
                        <span class="rating-star active">★</span>
                        <span class="rating-star active">★</span>
                        <span class="rating-star active">★</span>
                        <span class="rating-star half">★</span>
                    </div>
                    <p>"Beautiful resort with amazing amenities. Family had a wonderful time. The kids loved the playground and pools. Rooms were clean and comfortable."</p>
                </div>
                
                <button class="add-review-btn" onclick="openReviewModal()">
                    <i class="fas fa-pen"></i> Write a Review
                </button>
            </div>
        </div>

        <!-- REVIEW MODAL -->
        <div class="modal" id="reviewModal">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title">Rate Your Experience</h3>
                    <button class="close-modal" onclick="closeReviewModal()">×</button>
                </div>
                <div class="rating-input">
                    <div class="rating-input-title">How would you rate your experience?</div>
                    <div class="rating-input-stars" id="inputStars">
                        <span class="input-star" data-rating="1">★</span>
                        <span class="input-star" data-rating="2">★</span>
                        <span class="input-star" data-rating="3">★</span>
                        <span class="input-star" data-rating="4">★</span>
                        <span class="input-star" data-rating="5">★</span>
                    </div>
                    <div id="ratingText">Select your rating</div>
                </div>
                <form id="reviewForm">
                    <div class="form-group">
                        <label for="reviewerName">Your Name</label>
                        <input type="text" id="reviewerName" required>
                    </div>
                    <div class="form-group">
                        <label for="reviewTitle">Review Title</label>
                        <input type="text" id="reviewTitle" required>
                    </div>
                    <div class="form-group">
                        <label for="reviewText">Your Review</label>
                        <textarea id="reviewText" rows="4" required></textarea>
                    </div>
                    <button type="submit" class="submit-review-btn">Submit Review</button>
                </form>
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
                            (044) 760-1234 / 0917-123-4567
                        </div>
                    </li>
                    <li>
                        <i class="fas fa-envelope"></i>
                        <div>
                            <strong>Email:</strong><br>
                            reservations@casareginaresorts.com
                        </div>
                    </li>
                    <li>
                        <i class="fas fa-clock"></i>
                        <div>
                            <strong>Check-in/Check-out:</strong><br>
                            2:00 PM | 12:00 PM
                        </div>
                    </li>
                </ul>
            </div>
            
            <div class="footer-section">
                <h3><i class="fas fa-share-alt"></i> Connect With Us</h3>
                <div class="social-links">
                    <a href="https://facebook.com/CasaReginaResortsSJDM" target="_blank" title="Facebook">
                        <i class="fab fa-facebook-f"></i>
                    </a>
                    <a href="mailto:reservations@casareginaresorts.com" title="Email">
                        <i class="fas fa-envelope"></i>
                    </a>
                    <a href="tel:+0447601234" title="Call">
                        <i class="fas fa-phone"></i>
                    </a>
                    <a href="https://maps.google.com/?q=14.8136,121.0453" target="_blank" title="Location">
                        <i class="fas fa-map-marker-alt"></i>
                    </a>
                </div>
                <button class="btn" onclick="showBookingModal()" style="margin-top: 20px;">
                    <i class="fas fa-calendar-check"></i> Book Now
                </button>
            </div>
            
            <div class="footer-section">
                <h3><i class="fas fa-info-circle"></i> Quick Links</h3>
                <ul class="contact-info">
                    <li>
                        <i class="fas fa-map-pin"></i>
                        <div>
                            <strong>Address:</strong><br>
                            Tungkong Mangga, San Jose del Monte, Bulacan
                        </div>
                    </li>
                    <li>
                        <i class="fas fa-car"></i>
                        <div>
                            <strong>Parking:</strong><br>
                            Available for 100+ vehicles
                        </div>
                    </li>
                    <li>
                        <i class="fas fa-users"></i>
                        <div>
                            <strong>Capacity:</strong><br>
                            Up to 500 guests for events
                        </div>
                    </li>
                </ul>
            </div>
        </div>
        
        <div class="footer-bottom">
            <p>&copy; 2024 Casa Regina Resorts. All rights reserved. | Premium Event Resort & Hotel in San Jose del Monte, Bulacan</p>
        </div>
    </footer>

    <!-- Google Maps API -->
    <!-- Replace with your actual Google Maps API key -->
    <script src="https://maps.googleapis.com/maps/api/js?key=YOUR_GOOGLE_MAPS_API_KEY&callback=initMap" async defer></script>
    
    <script>
        let map;
        let marker;
        let infoWindow;
        let userRating = 0;
        
        // Function to go back to Hotels section
        function goBackToHotels() {
            if (window.history.length > 1) {
                window.history.back();
            } else {
                window.location.href = 'index.html#hotels';
            }
        }
        
        // Rating System Functions
        function openReviewModal() {
            document.getElementById('reviewModal').style.display = 'flex';
            userRating = 0;
            updateInputStars();
            document.getElementById('ratingText').textContent = 'Select your rating';
        }
        
        function closeReviewModal() {
            document.getElementById('reviewModal').style.display = 'none';
            document.getElementById('reviewForm').reset();
        }
        
        function updateInputStars() {
            const stars = document.querySelectorAll('.input-star');
            const ratingText = document.getElementById('ratingText');
            const texts = [
                'Select your rating',
                'Poor - Not satisfied',
                'Fair - Could be better',
                'Good - Satisfied',
                'Very Good - Enjoyed it',
                'Excellent - Outstanding experience'
            ];
            
            stars.forEach((star, index) => {
                if (index < userRating) {
                    star.classList.add('active');
                } else {
                    star.classList.remove('active');
                }
            });
            
            ratingText.textContent = texts[userRating];
        }
        
        function setRating(rating) {
            userRating = rating;
            updateInputStars();
        }
        
        function submitReview(event) {
            event.preventDefault();
            
            const name = document.getElementById('reviewerName').value;
            const title = document.getElementById('reviewTitle').value;
            const text = document.getElementById('reviewText').value;
            
            if (userRating === 0) {
                alert('Please select a rating before submitting.');
                return;
            }
            
            // In a real application, you would send this data to a server
            // For now, we'll just show a success message
            alert(`Thank you for your review, ${name}! Your ${userRating}-star rating has been submitted.`);
            
            // Close modal and reset form
            closeReviewModal();
            
            // Update overall rating (this would normally come from server)
            updateOverallRating();
        }
        
        function updateOverallRating() {
            // In a real app, this would fetch updated data from server
            // For demo purposes, we'll simulate a small increase
            const currentRating = parseFloat(document.getElementById('overallRating').textContent);
            const newRating = Math.min(5.0, currentRating + 0.01).toFixed(1);
            document.getElementById('overallRating').textContent = newRating;
            
            const totalReviews = document.getElementById('totalReviews');
            const match = totalReviews.textContent.match(/\d+/);
            if (match) {
                const currentCount = parseInt(match[0]);
                totalReviews.textContent = `Based on ${currentCount + 1} reviews`;
            }
        }
        
        // Initialize Google Map for Casa Regina Resorts
        function initMap() {
            const casaReginaLocation = { lat: 14.8136, lng: 121.0453 };
            
            map = new google.maps.Map(document.getElementById("map"), {
                zoom: 16,
                center: casaReginaLocation,
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
                position: casaReginaLocation,
                map: map,
                title: "Casa Regina Resorts",
                icon: {
                    path: google.maps.SymbolPath.CIRCLE,
                    scale: 10,
                    fillColor: "#FF6B35",
                    fillOpacity: 1,
                    strokeColor: "#FFFFFF",
                    strokeWeight: 2
                },
                animation: google.maps.Animation.DROP
            });
            
            infoWindow = new google.maps.InfoWindow({
                content: `
                    <div style="padding: 10px; max-width: 250px;">
                        <h3 style="color: var(--primary-green); margin: 0 0 10px 0; font-size: 16px;">📍 Casa Regina Resorts</h3>
                        <p style="margin: 5px 0; font-size: 14px;"><strong>🏨 Premium Event Resort & Hotel</strong></p>
                        <p style="margin: 5px 0; font-size: 13px;"><strong>📍 Address:</strong> Tungkong Mangga, SJDM, Bulacan</p>
                        <p style="margin: 5px 0; font-size: 13px;"><strong>📞 Contact:</strong> (044) 760-1234</p>
                        <div style="margin-top: 10px;">
                            <button onclick="getDirections()" style="background: var(--primary-green); color: white; border: none; padding: 8px 15px; border-radius: 5px; cursor: pointer; font-size: 12px;">
                                <i class="fas fa-directions"></i> Get Directions
                            </button>
                        </div>
                    </div>
                `
            });
            
            marker.addListener("click", () => {
                infoWindow.open(map, marker);
            });
            
            infoWindow.open(map, marker);
            
            const nearbyPlaces = [
                {
                    name: "San Jose del Monte City Hall",
                    position: { lat: 14.8150, lng: 121.0470 },
                    icon: "http://maps.google.com/mapfiles/ms/icons/blue-dot.png"
                },
                {
                    name: "St. Joseph the Worker Parish",
                    position: { lat: 14.8140, lng: 121.0430 },
                    icon: "http://maps.google.com/mapfiles/ms/icons/green-dot.png"
                }
            ];
            
            nearbyPlaces.forEach(place => {
                new google.maps.Marker({
                    position: place.position,
                    map: map,
                    title: place.name,
                    icon: place.icon
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
                map.setCenter({ lat: 14.8136, lng: 121.0453 });
            }
        }
        
        function getDirections() {
            const destination = "14.8136,121.0453";
            const url = `https://www.google.com/maps/dir/?api=1&destination=${destination}&destination_place_id=Casa+Regina+Resorts+San+Jose+del+Monte+Bulacan`;
            window.open(url, '_blank');
        }
        
        // Package selection function
        function selectPackage(packageName) {
            const message = `You selected: ${packageName}\n\nTo proceed with booking:\n1. Call us at (044) 760-1234\n2. Mention package code: ${packageName.toUpperCase().replace(/ /g, '-')}\n3. Our coordinator will assist you with customization and payment.`;
            alert(message);
        }

        // Booking modal function
        function showBookingModal() {
            alert('For direct bookings or custom packages, please call us at (044) 760-1234 or email reservations@casareginaresorts.com');
        }

        // Initialize rating system
        document.addEventListener('DOMContentLoaded', function() {
            // Add event listeners to input stars
            const inputStars = document.querySelectorAll('.input-star');
            inputStars.forEach(star => {
                star.addEventListener('click', function() {
                    const rating = parseInt(this.getAttribute('data-rating'));
                    setRating(rating);
                });
                
                star.addEventListener('mouseover', function() {
                    const rating = parseInt(this.getAttribute('data-rating'));
                    const stars = document.querySelectorAll('.input-star');
                    
                    stars.forEach((s, index) => {
                        if (index < rating) {
                            s.style.color = '#ffc107';
                        } else {
                            s.style.color = 'var(--star-empty)';
                        }
                    });
                });
                
                star.addEventListener('mouseout', function() {
                    updateInputStars();
                });
            });
            
            // Add form submit handler
            document.getElementById('reviewForm').addEventListener('submit', submitReview);
            
            // Close modal when clicking outside
            document.getElementById('reviewModal').addEventListener('click', function(e) {
                if (e.target === this) {
                    closeReviewModal();
                }
            });
            
            // Add keyboard shortcut for back button (Esc key)
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    if (document.getElementById('reviewModal').style.display === 'flex') {
                        closeReviewModal();
                    } else {
                        goBackToHotels();
                    }
                }
            });
            
            // Interactive package cards
            const packageCards = document.querySelectorAll('.package-card');
            packageCards.forEach(card => {
                card.addEventListener('click', function(e) {
                    if (!e.target.classList.contains('btn')) {
                        this.style.boxShadow = '0 5px 15px rgba(30, 60, 114, 0.2)';
                        setTimeout(() => {
                            this.style.boxShadow = '0 10px 20px rgba(0,0,0,0.1)';
                        }, 300);
                    }
                });
            });
        });
        
        // Handle Google Maps API errors
        window.gm_authFailure = function() {
            document.getElementById("map").innerHTML = `
                <div style="height: 100%; display: flex; flex-direction: column; justify-content: center; align-items: center; text-align: center; padding: 20px;">
                    <i class="fas fa-map-marked-alt" style="font-size: 48px; color: #666; margin-bottom: 15px;"></i>
                    <h3 style="color: #333; margin-bottom: 10px;">Map Unavailable</h3>
                    <p style="color: #666; margin-bottom: 15px;">Google Maps cannot be loaded at the moment.</p>
                    <p style="color: var(--primary-green);"><strong>📍 Casa Regina Resorts Location:</strong></p>
                    <p style="color: #333;">Tungkong Mangga, San Jose del Monte, Bulacan</p>
                    <p style="color: #333;">Coordinates: 14.8136° N, 121.0453° E</p>
                </div>
            `;
        };
    </script>
</body>
</html>