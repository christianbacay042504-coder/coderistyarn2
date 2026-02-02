<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>The Rising Heart Monument - San Jose del Monte Tourism</title>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/coderistyarn2/sjdm-user/styles.css">
    <style>
        :root {
            --primary: #2c5f2d;
            --primary-light: #4a8c4a;
            --secondary: #f5f5f5;
            --accent: #ff9800;
            --text-dark: #333333;
            --text-light: #666666;
            --border: #e0e0e0;
            --shadow: 0 4px 12px rgba(0,0,0,0.08);
        }
        
        body {
            font-family: 'Inter', sans-serif;
            background: #fafafa;
            color: var(--text-dark);
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .page-header {
            background: white;
            padding: 20px 0;
            border-bottom: 1px solid var(--border);
        }

        .header-content {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .back-navigation {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .btn-back {
            background: transparent;
            border: none;
            color: var(--primary);
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            padding: 10px 20px;
            border-radius: 8px;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .btn-back:hover {
            background: rgba(44, 95, 45, 0.1);
        }

        .page-title {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--primary);
            margin: 0;
        }

        .spot-hero {
            height: 400px;
            background: linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.5)), 
                       url('https://images.unsplash.com/photo-1516542076529-1ea3854896f2?q=80&w=2071&auto=format&fit=crop');
            background-size: cover;
            background-position: center;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            color: white;
            margin: 30px auto 40px;
            border-radius: 50px;
            max-width: 1500px;
            width: calc(100% - 40px);
        }

        .hero-content {
            max-width: 800px;
            padding: 0 20px;
        }

        .hero-title {
            font-size: 3rem;
            font-weight: 700;
            margin-bottom: 15px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }

        .hero-subtitle {
            font-size: 1.2rem;
            opacity: 0.9;
            margin-bottom: 20px;
        }

        .spot-content {
            padding: 0 0 60px;
        }

        .spot-details {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 40px;
            margin-bottom: 60px;
        }

        .section-title {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid var(--border);
        }

        .section-subtitle {
            font-size: 1.3rem;
            font-weight: 600;
            color: var(--text-dark);
            margin: 30px 0 15px;
        }

        .description {
            font-size: 1.05rem;
            line-height: 1.8;
            color: var(--text-light);
            margin-bottom: 25px;
        }

        .features-list {
            list-style: none;
            padding: 0;
            margin: 25px 0;
        }

        .features-list li {
            padding: 12px 0;
            padding-left: 35px;
            position: relative;
            font-size: 1rem;
            line-height: 1.6;
            color: var(--text-light);
        }

        .features-list li:before {
            content: '✓';
            position: absolute;
            left: 0;
            color: var(--primary);
            font-weight: bold;
            font-size: 1.2rem;
        }

        .spot-info-card {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: var(--shadow);
            border: 1px solid var(--border);
            position: sticky;
            top: 20px;
        }

        .info-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 20px;
            margin: 25px 0;
        }

        .info-item {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 15px;
            background: var(--secondary);
            border-radius: 10px;
            transition: transform 0.3s ease;
        }

        .info-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }

        .info-item .material-icons-outlined {
            color: var(--primary);
            font-size: 26px;
        }

        .info-text {
            flex: 1;
        }

        .info-label {
            display: block;
            font-size: 0.9rem;
            color: var(--text-light);
            margin-bottom: 4px;
        }

        .info-value {
            display: block;
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--text-dark);
        }

        .photo-gallery {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 25px;
            margin: 40px 0;
        }

        .gallery-item {
            border-radius: 12px;
            overflow: hidden;
            box-shadow: var(--shadow);
            transition: transform 0.3s ease;
            height: 220px;
        }

        .gallery-item:hover {
            transform: translateY(-5px);
        }

        .gallery-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }

        .gallery-item:hover img {
            transform: scale(1.05);
        }

        .cta-section {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
            color: white;
            padding: 50px 40px;
            border-radius: 15px;
            text-align: center;
            margin: 50px 0;
            box-shadow: var(--shadow);
        }

        .cta-title {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 20px;
        }

        .cta-text {
            font-size: 1.1rem;
            opacity: 0.9;
            max-width: 700px;
            margin: 0 auto 30px;
            line-height: 1.6;
        }

        .cta-buttons {
            display: flex;
            gap: 20px;
            justify-content: center;
            flex-wrap: wrap;
        }

        .btn-primary {
            background: white;
            color: var(--primary);
            border: none;
            padding: 16px 32px;
            font-size: 1.1rem;
            font-weight: 600;
            border-radius: 10px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 10px;
            transition: all 0.3s ease;
            min-width: 180px;
            justify-content: center;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0,0,0,0.15);
        }

        .btn-secondary {
            background: transparent;
            color: white;
            border: 2px solid white;
            padding: 14px 32px;
            font-size: 1.1rem;
            font-weight: 600;
            border-radius: 10px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 10px;
            transition: all 0.3s ease;
            min-width: 180px;
            justify-content: center;
        }

        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.1);
            transform: translateY(-2px);
        }

        .footer {
            background: white;
            padding: 30px 0;
            text-align: center;
            border-top: 1px solid var(--border);
            margin-top: 60px;
        }

        .footer-text {
            color: var(--text-light);
            font-size: 0.9rem;
            margin: 5px 0;
        }

        .monument-info {
            background: #fce4ec;
            border-left: 4px solid #e91e63;
            padding: 20px;
            border-radius: 8px;
            margin: 25px 0;
        }

        .viewing-info {
            background: #e3f2fd;
            border-left: 4px solid #2196f3;
            padding: 20px;
            border-radius: 8px;
            margin: 25px 0;
        }

        .cultural-significance {
            background: #fff3e0;
            border-left: 4px solid #ff9800;
            padding: 20px;
            border-radius: 8px;
            margin: 25px 0;
        }

        .viewing-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin: 30px 0;
        }

        .viewing-card {
            background: white;
            padding: 25px;
            border-radius: 12px;
            text-align: center;
            box-shadow: var(--shadow);
            border: 1px solid var(--border);
            transition: transform 0.3s ease;
        }

        .viewing-card:hover {
            transform: translateY(-5px);
        }

        .viewing-card .material-icons-outlined {
            font-size: 48px;
            color: var(--primary);
            margin-bottom: 15px;
        }

        /* Enhanced Book Button Styles */
        .booking-btn {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
            color: white;
            border: none;
            padding: 16px 32px;
            font-size: 1.1rem;
            font-weight: 700;
            border-radius: 12px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            transition: all 0.3s ease;
            min-width: 200px;
            width: 100%;
            margin: 20px 0;
            text-transform: uppercase;
            letter-spacing: 1px;
            box-shadow: 0 6px 20px rgba(44, 95, 45, 0.3);
            position: relative;
            overflow: hidden;
        }

        .booking-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: 0.5s;
        }

        .booking-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(44, 95, 45, 0.4);
            background: linear-gradient(135deg, var(--primary-light) 0%, #3a7c3a 100%);
        }

        .booking-btn:hover::before {
            left: 100%;
        }

        .booking-btn .material-icons-outlined {
            font-size: 24px;
            transition: transform 0.3s ease;
        }

        .booking-btn:hover .material-icons-outlined {
            transform: translateX(5px);
        }

        .booking-btn:active {
            transform: translateY(-1px);
            box-shadow: 0 5px 15px rgba(44, 95, 45, 0.3);
        }

        .glow-effect {
            animation: glowPulse 2s infinite alternate;
        }

        @keyframes glowPulse {
            from {
                box-shadow: 0 6px 20px rgba(44, 95, 45, 0.3);
            }
            to {
                box-shadow: 0 6px 25px rgba(44, 95, 45, 0.5), 0 0 15px rgba(76, 175, 80, 0.3);
            }
        }

        @media (max-width: 992px) {
            .spot-details {
                grid-template-columns: 1fr;
                gap: 40px;
            }
            
            .spot-info-card {
                position: static;
            }
            
            .hero-title {
                font-size: 2.5rem;
            }
            
            .viewing-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 768px) {
            .hero-title {
                font-size: 2rem;
            }
            
            .hero-subtitle {
                font-size: 1rem;
            }
            
            .photo-gallery {
                grid-template-columns: 1fr;
            }
            
            .viewing-grid {
                grid-template-columns: 1fr;
            }
            
            .cta-buttons {
                flex-direction: column;
                align-items: center;
            }
            
            .btn-primary, .btn-secondary {
                width: 100%;
                max-width: 300px;
            }
            
            .header-content {
                flex-direction: column;
                gap: 15px;
                text-align: center;
            }
        }
    </style>
</head>
<body>
    <!-- Page Header -->
    <div class="page-header">
        <div class="container">
            <div class="header-content">
                <div class="back-navigation">
                    <button class="btn-back" onclick="window.history.back()">
                        <span class="material-icons-outlined">arrow_back</span>
                        Back
                    </button>
                    <h1 class="page-title">The Rising Heart Monument</h1>
                </div>
            </div>
        </div>
    </div>

    <!-- Hero Section -->
    <section class="spot-hero">
        <div class="hero-content">
            <h1 class="hero-title">The Rising Heart Monument</h1>
            <p class="hero-subtitle">San Jose del Monte's Iconic Symbol of Love and Resilience</p>
        </div>
    </section>

    <!-- Main Content -->
    <main class="container">
        <div class="spot-content">
            <div class="spot-details">
                <!-- Left Column: Content -->
                <div>
                    <h2 class="section-title">About The Rising Heart Monument</h2>
                    <p class="description">
                        Standing majestically at the entrance of San Jose del Monte City, The Rising Heart Monument 
                        is a 15-meter tall steel sculpture that has become the city's most recognizable landmark. 
                        Completed in 2018 to celebrate the city's conversion from municipality to component city, 
                        this monument symbolizes the city's rising prosperity, love for community, and resilient spirit.
                    </p>
                    
                    <!-- Monument Information -->
                    <div class="monument-info">
                        <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 10px;">
                            <span class="material-icons-outlined" style="color: #e91e63;">architecture</span>
                            <h4 style="margin: 0; color: #c2185b; font-size: 1.1rem;">Architectural Marvel</h4>
                        </div>
                        <p style="margin: 0; color: #880e4f; font-size: 0.95rem;">
                            Designed by renowned Filipino sculptor Eduardo Castrillo, the monument features 
                            stainless steel construction with LED lighting system. The heart shape represents 
                            love while the upward tilt symbolizes progress and aspiration.
                        </p>
                    </div>

                    <!-- Viewing Information -->
                    <div class="viewing-info">
                        <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 10px;">
                            <span class="material-icons-outlined" style="color: #2196f3;">nights_stay</span>
                            <h4 style="margin: 0; color: #1565c0; font-size: 1.1rem;">Night Viewing Experience</h4>
                        </div>
                        <p style="margin: 0; color: #0d47a1; font-size: 0.95rem;">
                            The monument is most spectacular at night when its LED lighting system illuminates 
                            the heart with changing colors. Best viewing times are between 6:00 PM and 10:00 PM.
                        </p>
                    </div>

                    <!-- Cultural Significance -->
                    <div class="cultural-significance">
                        <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 10px;">
                            <span class="material-icons-outlined" style="color: #ff9800;">public</span>
                            <h4 style="margin: 0; color: #ef6c00; font-size: 1.1rem;">Cultural Significance</h4>
                        </div>
                        <p style="margin: 0; color: #e65100; font-size: 0.95rem;">
                            The Rising Heart has become a symbol of unity for San Jose del Monte residents. 
                            It represents the city's journey from agricultural roots to modern urbanization 
                            while maintaining community values and environmental consciousness.
                        </p>
                    </div>

                    <h3 class="section-subtitle">Best Viewing Experiences</h3>
                    
                    <!-- Viewing Grid -->
                    <div class="viewing-grid">
                        <div class="viewing-card">
                            <span class="material-icons-outlined">wb_sunny</span>
                            <h4 style="margin: 10px 0 5px; color: var(--text-dark);">Sunrise Viewing</h4>
                            <p style="color: var(--text-light); font-size: 0.9rem; margin: 0;">5:30 AM - 6:30 AM</p>
                        </div>
                        <div class="viewing-card">
                            <span class="material-icons-outlined">light_mode</span>
                            <h4 style="margin: 10px 0 5px; color: var(--text-dark);">Daytime Viewing</h4>
                            <p style="color: var(--text-light); font-size: 0.9rem; margin: 0;">9:00 AM - 4:00 PM</p>
                        </div>
                        <div class="viewing-card">
                            <span class="material-icons-outlined">nights_stay</span>
                            <h4 style="margin: 10px 0 5px; color: var(--text-dark);">Sunset Viewing</h4>
                            <p style="color: var(--text-light); font-size: 0.9rem; margin: 0;">5:30 PM - 6:30 PM</p>
                        </div>
                        <div class="viewing-card">
                            <span class="material-icons-outlined">lightbulb</span>
                            <h4 style="margin: 10px 0 5px; color: var(--text-dark);">Night Illumination</h4>
                            <p style="color: var(--text-light); font-size: 0.9rem; margin: 0;">7:00 PM - 10:00 PM</p>
                        </div>
                    </div>

                    <h3 class="section-subtitle">Monument Features</h3>
                    <ul class="features-list">
                        <li><strong>Height:</strong> 15 meters (49 feet) from ground level</li>
                        <li><strong>Materials:</strong> Stainless steel framework with weather-resistant coating</li>
                        <li><strong>Lighting System:</strong> 5,000+ LED lights with color-changing capability</li>
                        <li><strong>Observation Deck:</strong> Circular platform with seating for 50 visitors</li>
                        <li><strong>Information Center:</strong> Small museum detailing the monument's construction</li>
                        <li><strong>Landscaped Gardens:</strong> Surrounding floral arrangements and walking paths</li>
                        <li><strong>Photo Opportunities:</strong> Designated selfie spots with best angles marked</li>
                        <li><strong>Accessibility:</strong> Ramps and pathways for wheelchair access</li>
                    </ul>

                    <h3 class="section-subtitle">Special Events & Activities</h3>
                    <ul class="features-list">
                        <li><strong>Valentine's Day Celebration:</strong> Annual "Heart Festival" with couples' activities</li>
                        <li><strong>Cityhood Anniversary:</strong> March 15 celebration with cultural performances</li>
                        <li><strong>Christmas Lighting:</strong> Special holiday light displays from November to January</li>
                        <li><strong>Photography Contests:</strong> Monthly competitions for best monument photos</li>
                        <li><strong>Educational Tours:</strong> School field trips with guided historical explanations</li>
                        <li><strong>Evening Concerts:</strong> Weekend musical performances at the observation deck</li>
                        <li><strong>Community Art Exhibits:</strong> Local artists showcase works inspired by the monument</li>
                    </ul>

                    <!-- Photo Gallery -->
                    <h3 class="section-subtitle">Gallery</h3>
                    <div class="photo-gallery">
                        <div class="gallery-item">
                            <img src="https://images.unsplash.com/photo-1516542076529-1ea3854896f2?q=80&w=2071&auto=format&fit=crop" alt="Monument Day View">
                        </div>
                        <div class="gallery-item">
                            <img src="https://images.unsplash.com/photo-1518709268805-4e9042af2176?q=80&w=2068&auto=format&fit=crop" alt="Night Illumination">
                        </div>
                        <div class="gallery-item">
                            <img src="https://images.unsplash.com/photo-1542744095-fcf48d80b0fd?q=80&w=2076&auto=format&fit=crop" alt="Sunset View">
                        </div>
                    </div>
                </div>

                <!-- Right Column: Information Card -->
                <div class="spot-info-card">
                    <h3 class="section-subtitle" style="margin-top: 0;">Visitor Information</h3>
                    
                    <div class="info-grid">
                        <div class="info-item">
                            <span class="material-icons-outlined">schedule</span>
                            <div class="info-text">
                                <span class="info-label">Operating Hours</span>
                                <span class="info-value">24/7 Access</span>
                            </div>
                        </div>
                        <div class="info-item">
                            <span class="material-icons-outlined">payments</span>
                            <div class="info-text">
                                <span class="info-label">Entrance Fee</span>
                                <span class="info-value">Free Admission</span>
                            </div>
                        </div>
                        <div class="info-item">
                            <span class="material-icons-outlined">terrain</span>
                            <div class="info-text">
                                <span class="info-label">Accessibility</span>
                                <span class="info-value">Fully Accessible</span>
                            </div>
                        </div>
                        <div class="info-item">
                            <span class="material-icons-outlined">park</span>
                            <div class="info-text">
                                <span class="info-label">Park Area</span>
                                <span class="info-value">2,500 sqm</span>
                            </div>
                        </div>
                    </div>

                    <h3 class="section-subtitle">Lighting Schedule</h3>
                    <div class="info-item">
                        <span class="material-icons-outlined">light_mode</span>
                        <div class="info-text">
                            <span class="info-label">Regular Lighting</span>
                            <span class="info-value">6:00 PM - 10:00 PM</span>
                        </div>
                    </div>
                    <div class="info-item">
                        <span class="material-icons-outlined">event</span>
                        <div class="info-text">
                            <span class="info-label">Special Events</span>
                            <span class="info-value">6:00 PM - 11:00 PM</span>
                        </div>
                    </div>
                    <div class="info-item">
                        <span class="material-icons-outlined">holiday_village</span>
                        <div class="info-text">
                            <span class="info-label">Holiday Season</span>
                            <span class="info-value">5:30 PM - 12:00 AM</span>
                        </div>
                    </div>

                    <h3 class="section-subtitle">Facilities Available</h3>
                    <ul style="margin: 15px 0; padding-left: 20px; color: var(--text-light); font-size: 0.95rem;">
                        <li>Free parking for 50 vehicles</li>
                        <li>Public restrooms with accessible facilities</li>
                        <li>Information center and small museum</li>
                        <li>Food kiosks and refreshment stands</li>
                        <li>Benches and seating areas throughout</li>
                        <li>Free Wi-Fi in central area</li>
                        <li>First aid station</li>
                        <li>Lost and found service</li>
                    </ul>

                    <h3 class="section-subtitle">Park Rules</h3>
                    <ul style="margin: 15px 0; padding-left: 20px; color: var(--text-light); font-size: 0.95rem;">
                        <li>No climbing on the monument structure</li>
                        <li>Keep the area clean - use designated trash bins</li>
                        <li>No smoking within the monument area</li>
                        <li>Respect other visitors - maintain quiet zones</li>
                        <li>Professional photography requires permit</li>
                        <li>Pets must be leashed and cleaned after</li>
                        <li>Follow security personnel instructions</li>
                    </ul>

                    <h3 class="section-subtitle">Location & Access</h3>
                    <div class="info-item">
                        <span class="material-icons-outlined">location_on</span>
                        <div class="info-text">
                            <span class="info-label">Address</span>
                            <span class="info-value">City Entrance Road, San Jose del Monte, Bulacan</span>
                        </div>
                    </div>
                    <div class="info-item">
                        <span class="material-icons-outlined">directions_bus</span>
                        <div class="info-text">
                            <span class="info-label">Public Transport</span>
                            <span class="info-value">Jeepneys: Routes 1, 3, 5</span>
                        </div>
                    </div>

                    <button class="booking-btn glow-effect" onclick="location.href='/coderistyarn2/sjdm-user/index.php#guides'">
                        <span class="material-icons-outlined">tour</span>
                        BOOK CITY TOUR
                    </button>
                </div>
            </div>

            <!-- Call to Action Section -->
            <div class="cta-section">
                <h2 class="cta-title">Experience San Jose del Monte's Iconic Landmark</h2>
                <p class="cta-text">
                    Visit The Rising Heart Monument and capture the perfect photo of San Jose del Monte's 
                    symbol of love and progress. Our city guides can provide fascinating insights into 
                    the monument's history and significance.
                </p>
                <div class="cta-buttons">
                    <button class="btn-primary" onclick="location.href='/coderistyarn2/sjdm-user/index.php#guides'">
                        Book City Guide
                    </button>
                    <button class="btn-secondary" onclick="location.href='/coderistyarn2/sjdm-user/index.php#events'">
                        View Monument Events
                    </button>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <p class="footer-text">© 2024 San Jose del Monte Tourism Office. All rights reserved.</p>
            <p class="footer-text">Promoting sustainable tourism and community development.</p>
        </div>
    </footer>

    <script>
        // Smooth scroll for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
    </script>
</body>
</html>