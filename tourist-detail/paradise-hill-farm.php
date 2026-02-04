<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paradise Hill Farm - San Jose del Monte Tourism</title>
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
                       url('https://images.unsplash.com/photo-1500382017468-9049fed747ef?q=80&w=2052&auto=format&fit=crop');
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

        .farm-features {
            background: #e8f5e9;
            border-left: 4px solid #4caf50;
            padding: 20px;
            border-radius: 8px;
            margin: 25px 0;
        }

        .workshop-info {
            background: #fff3e0;
            border-left: 4px solid #ff9800;
            padding: 20px;
            border-radius: 8px;
            margin: 25px 0;
        }

        .crop-cycle {
            background: #f3e5f5;
            border-left: 4px solid #9c27b0;
            padding: 20px;
            border-radius: 8px;
            margin: 25px 0;
        }

        .activities-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin: 30px 0;
        }

        .activity-card {
            background: white;
            padding: 25px;
            border-radius: 12px;
            text-align: center;
            box-shadow: var(--shadow);
            border: 1px solid var(--border);
            transition: transform 0.3s ease;
        }

        .activity-card:hover {
            transform: translateY(-5px);
        }

        .activity-card .material-icons-outlined {
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
            
            .activities-grid {
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
            
            .activities-grid {
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
                    <h1 class="page-title">Paradise Hill Farm</h1>
                </div>
            </div>
        </div>
    </div>

    <!-- Hero Section -->
    <section class="spot-hero">
        <div class="hero-content">
            <h1 class="hero-title">Paradise Hill Farm</h1>
            <p class="hero-subtitle">Sustainable Agriculture Meets Recreational Farming Experience</p>
        </div>
    </section>

    <!-- Main Content -->
    <main class="container">
        <div class="spot-content">
            <div class="spot-details">
                <!-- Left Column: Content -->
                <div>
                    <h2 class="section-title">About Paradise Hill Farm</h2>
                    <p class="description">
                        Paradise Hill Farm is a 25-hectare integrated sustainable farm that combines modern agricultural 
                        practices with eco-tourism experiences. Established in 2010, this farm utilizes organic farming 
                        methods, renewable energy, and water conservation techniques to create a model for sustainable 
                        agriculture in San Jose del Monte.
                    </p>
                    
                    <!-- Farm Features -->
                    <div class="farm-features">
                        <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 10px;">
                            <span class="material-icons-outlined" style="color: #4caf50;">eco</span>
                            <h4 style="margin: 0; color: #2e7d32; font-size: 1.1rem;">Sustainable Farming Practices</h4>
                        </div>
                        <p style="margin: 0; color: #1b5e20; font-size: 0.95rem;">
                            Paradise Hill Farm implements permaculture design, organic pest management, 
                            rainwater harvesting, and solar energy systems. All produce is grown without 
                            synthetic chemicals or pesticides.
                        </p>
                    </div>

                    <!-- Workshop Information -->
                    <div class="workshop-info">
                        <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 10px;">
                            <span class="material-icons-outlined" style="color: #ff9800;">school</span>
                            <h4 style="margin: 0; color: #ef6c00; font-size: 1.1rem;">Educational Workshops</h4>
                        </div>
                        <p style="margin: 0; color: #e65100; font-size: 0.95rem;">
                            The farm offers regular workshops on organic gardening, composting, 
                            herbal medicine, and sustainable living. Perfect for families, schools, 
                            and community groups.
                        </p>
                    </div>

                    <h3 class="section-subtitle">Farm Activities & Experiences</h3>
                    
                    <!-- Activities Grid -->
                    <div class="activities-grid">
                        <div class="activity-card">
                            <span class="material-icons-outlined">agriculture</span>
                            <h4 style="margin: 10px 0 5px; color: var(--text-dark);">Organic Farming Tour</h4>
                            <p style="color: var(--text-light); font-size: 0.9rem; margin: 0;">Guided tour of organic vegetable production</p>
                        </div>
                        <div class="activity-card">
                            <span class="material-icons-outlined">local_florist</span>
                            <h4 style="margin: 10px 0 5px; color: var(--text-dark);">Fruit Picking</h4>
                            <p style="color: var(--text-light); font-size: 0.9rem; margin: 0;">Seasonal fruit harvesting experience</p>
                        </div>
                        <div class="activity-card">
                            <span class="material-icons-outlined">pets</span>
                            <h4 style="margin: 10px 0 5px; color: var(--text-dark);">Animal Feeding</h4>
                            <p style="color: var(--text-light); font-size: 0.9rem; margin: 0;">Interaction with farm animals</p>
                        </div>
                        <div class="activity-card">
                            <span class="material-icons-outlined">restaurant</span>
                            <h4 style="margin: 10px 0 5px; color: var(--text-dark);">Farm-to-Table Dining</h4>
                            <p style="color: var(--text-light); font-size: 0.9rem; margin: 0;">Meals prepared with fresh farm produce</p>
                        </div>
                    </div>

                    <!-- Crop Cycle Information -->
                    <div class="crop-cycle">
                        <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 10px;">
                            <span class="material-icons-outlined" style="color: #9c27b0;">calendar_month</span>
                            <h4 style="margin: 0; color: #7b1fa2; font-size: 1.1rem;">Seasonal Crop Availability</h4>
                        </div>
                        <p style="margin: 0; color: #4a148c; font-size: 0.95rem;">
                            January-March: Strawberries, Lettuce, Herbs | April-June: Tomatoes, Eggplants, Peppers | 
                            July-September: Squash, Beans, Root Crops | October-December: Leafy Greens, Citrus Fruits
                        </p>
                    </div>

                    <h3 class="section-subtitle">Educational Programs</h3>
                    <ul class="features-list">
                        <li><strong>School Field Trips:</strong> Customized educational tours for elementary to college students</li>
                        <li><strong>Weekend Workshops:</strong> Hands-on sessions on composting, seedling preparation, and organic pest control</li>
                        <li><strong>Corporate Team Building:</strong> Farming challenges and sustainable living activities</li>
                        <li><strong>Family Farming Day:</strong> Weekend programs for families to experience farm life together</li>
                        <li><strong>Agricultural Training:</strong> Certificate courses for aspiring farmers and agriculture students</li>
                        <li><strong>Senior Citizen Programs:</strong> Gentle gardening activities and farm therapy sessions</li>
                    </ul>

                    <!-- Photo Gallery -->
                    <h3 class="section-subtitle">Gallery</h3>
                    <div class="photo-gallery">
                        <div class="gallery-item">
                            <img src="https://images.unsplash.com/photo-1500382017468-9049fed747ef?q=80&w=2052&auto=format&fit=crop" alt="Farm Overview">
                        </div>
                        <div class="gallery-item">
                            <img src="https://images.unsplash.com/photo-1625246333195-78d9c38ad449?q=80&w=2070&auto=format&fit=crop" alt="Organic Vegetables">
                        </div>
                        <div class="gallery-item">
                            <img src="https://images.unsplash.com/photo-1464454709131-ffd692591ee5?q=80&w=2070&auto=format&fit=crop" alt="Farm Activities">
                        </div>
                    </div>
                </div>

                <!-- Right Column: Information Card -->
                <div class="spot-info-card">
                    <h3 class="section-subtitle" style="margin-top: 0;">Visit Information</h3>
                    
                    <div class="info-grid">
                        <div class="info-item">
                            <span class="material-icons-outlined">schedule</span>
                            <div class="info-text">
                                <span class="info-label">Operating Hours</span>
                                <span class="info-value">8:00 AM - 5:00 PM</span>
                            </div>
                        </div>
                        <div class="info-item">
                            <span class="material-icons-outlined">payments</span>
                            <div class="info-text">
                                <span class="info-label">Entrance Fee</span>
                                <span class="info-value">Free</span>
                            </div>
                        </div>
                        <div class="info-item">
                            <span class="material-icons-outlined">terrain</span>
                            <div class="info-text">
                                <span class="info-label">Terrain</span>
                                <span class="info-value">Gently Sloping Hills</span>
                            </div>
                        </div>
                        <div class="info-item">
                            <span class="material-icons-outlined">groups</span>
                            <div class="info-text">
                                <span class="info-label">Recommended For</span>
                                <span class="info-value">All Ages</span>
                            </div>
                        </div>
                    </div>

                    <h3 class="section-subtitle">Tour Guide Information</h3>
                    <div style="background: #e8f5e9; padding: 15px; border-radius: 8px; margin: 15px 0;">
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <span class="material-icons-outlined" style="color: #4caf50;">check_circle</span>
                            <span style="font-weight: 600; color: #2e7d32;">No Tour Guide Required</span>
                        </div>
                        <p style="margin: 10px 0 0; font-size: 0.9rem; color: #1b5e20;">
                            This destination is open for self-guided exploration. Feel free to visit at your own pace.
                        </p>
                    </div>
                    <div class="info-item">
                        <span class="material-icons-outlined">tour</span>
                        <div class="info-text">
                            <span class="info-label">Basic Tour</span>
                            <span class="info-value">₱200 (2 hours)</span>
                        </div>
                    </div>
                    <div class="info-item">
                        <span class="material-icons-outlined">restaurant</span>
                        <div class="info-text">
                            <span class="info-label">Tour with Lunch</span>
                            <span class="info-value">₱350 (3 hours)</span>
                        </div>
                    </div>
                    <div class="info-item">
                        <span class="material-icons-outlined">workshop</span>
                        <div class="info-text">
                            <span class="info-label">Workshop Package</span>
                            <span class="info-value">₱500 (4 hours)</span>
                        </div>
                    </div>

                    <h3 class="section-subtitle">What's Included</h3>
                    <ul style="margin: 15px 0; padding-left: 20px; color: var(--text-light); font-size: 0.95rem;">
                        <li>Guided farm tour</li>
                        <li>Educational materials</li>
                        <li>Fruit tasting session</li>
                        <li>Animal feeding experience</li>
                        <li>Basic farming tools (for workshops)</li>
                        <li>Certificate of participation</li>
                        <li>Farm-produced souvenir</li>
                    </ul>

                    <h3 class="section-subtitle">What to Bring</h3>
                    <ul style="margin: 15px 0; padding-left: 20px; color: var(--text-light); font-size: 0.95rem;">
                        <li>Comfortable walking shoes</li>
                        <li>Sun protection (hat, sunscreen)</li>
                        <li>Water bottle (refill stations available)</li>
                        <li>Change of clothes (optional)</li>
                        <li>Camera for photography</li>
                        <li>Extra cash for farm products</li>
                    </ul>

                    <button class="booking-btn glow-effect" onclick="location.href='/coderistyarn2/sjdm-user/index.php#booking'">
                        <span class="material-icons-outlined">agriculture</span>
                        BOOK FARM EXPERIENCE
                    </button>
                </div>
            </div>

            <!-- Call to Action Section -->
            <div class="cta-section">
                <h2 class="cta-title">Experience Sustainable Farming at Paradise Hill</h2>
                <p class="cta-text">
                    Discover the beauty of organic agriculture and learn sustainable farming practices. 
                    Perfect for families, students, and anyone interested in eco-friendly living.
                </p>
                <div class="cta-buttons">
                    <button class="btn-primary" onclick="location.href='/coderistyarn2/sjdm-user/index.php#booking'">
                        Book Farm Tour
                    </button>
                    <button class="btn-secondary" onclick="location.href='/coderistyarn2/sjdm-user/index.php#guides'">
                        Find Agricultural Guide
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