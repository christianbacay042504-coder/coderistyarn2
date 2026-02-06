<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Otso-Otso Falls - San Jose del Monte Tourism</title>
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
                       url('https://images.unsplash.com/photo-1551632811-561732d1e306?q=80&w=2070&auto=format&fit=crop');
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

        .safety-notice {
            background: #fff8e1;
            border-left: 4px solid #ff9800;
            padding: 20px;
            border-radius: 8px;
            margin: 25px 0;
        }

        .pool-info {
            background: #e3f2fd;
            border-left: 4px solid #2196f3;
            padding: 20px;
            border-radius: 8px;
            margin: 25px 0;
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
                    <h1 class="page-title">Otso-Otso Falls</h1>
                </div>
            </div>
        </div>
    </div>

    <!-- Hero Section -->
    <section class="spot-hero">
        <div class="hero-content">
            <h1 class="hero-title">Otso-Otso Falls</h1>
            <p class="hero-subtitle">The Hidden Gem of Eight Cascading Waterfalls</p>
        </div>
    </section>

    <!-- Main Content -->
    <main class="container">
        <div class="spot-content">
            <div class="spot-details">
                <!-- Left Column: Content -->
                <div>
                    <h2 class="section-title">About Otso-Otso Falls</h2>
                    <p class="description">
                        Named after the Filipino word "otso" meaning eight, Otso-Otso Falls is a magnificent 
                        series of eight interconnected waterfalls located in the pristine wilderness of San Jose del Monte. 
                        This destination is part of a trio falls tour (Burong, Otso-Otso, and Kaytitinga Falls) with a mandatory tour guide 
                        fee of PHP 350 per group (max 5 pax), arranged through the tourism office.
                    </p>
                    
                    <!-- Safety Information -->
                    <div class="safety-notice">
                        <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 10px;">
                            <span class="material-icons-outlined" style="color: #ff9800;">warning</span>
                            <h4 style="margin: 0; color: #d84315; font-size: 1.1rem;">Important Safety Information</h4>
                        </div>
                        <p style="margin: 0; color: #5d4037; font-size: 0.95rem;">
                            The full trail to all eight waterfalls is recommended for experienced hikers only. 
                            Guided tours are mandatory for the complete circuit. Proper hiking equipment is essential.
                        </p>
                    </div>

                    <!-- Pool Information -->
                    <div class="pool-info">
                        <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 10px;">
                            <span class="material-icons-outlined" style="color: #2196f3;">pool</span>
                            <h4 style="margin: 0; color: #1565c0; font-size: 1.1rem;">Swimming Information</h4>
                        </div>
                        <p style="margin: 0; color: #0d47a1; font-size: 0.95rem;">
                            Natural swimming pools are available at waterfalls 1, 3, and 7. Water depths range from 
                            2-8 feet. Swimming is permitted in designated areas only during daylight hours.
                        </p>
                    </div>

                    <h3 class="section-subtitle">Waterfall Characteristics</h3>
                    <ul class="features-list">
                        <li><strong>Waterfall 1 (First Cascade):</strong> 15-foot drop with shallow swimming pool, accessible for beginners</li>
                        <li><strong>Waterfall 3 (Middle Beauty):</strong> 25-foot curtain waterfall with deep natural pool</li>
                        <li><strong>Waterfall 5 (Hidden Jewel):</strong> Requires moderate climbing, features rock formations</li>
                        <li><strong>Waterfall 7 (Grand Cascade):</strong> 40-foot majestic waterfall with the largest swimming area</li>
                        <li><strong>Waterfall 8 (Final Tier):</strong> 20-foot segmented waterfall with panoramic views</li>
                    </ul>

                    <h3 class="section-subtitle">Trail Specifications</h3>
                    <ul class="features-list">
                        <li><strong>Total Trail Length:</strong> 5 kilometers round trip for complete circuit</li>
                        <li><strong>Hike Duration:</strong> 4-6 hours to visit all eight waterfalls</li>
                        <li><strong>Elevation Gain:</strong> 300 meters cumulative across the trail</li>
                        <li><strong>Trail Features:</strong> River crossings, rock scrambling, forest paths</li>
                        <li><strong>Best Viewing Season:</strong> November to May (dry season)</li>
                        <li><strong>Minimum Group Size:</strong> 4 persons for guided tours</li>
                    </ul>

                    <!-- Photo Gallery -->
                    <h3 class="section-subtitle">Gallery</h3>
                    <div class="photo-gallery">
                        <div class="gallery-item">
                            <img src="https://images.unsplash.com/photo-1551632811-561732d1e306?q=80&w=2070&auto=format&fit=crop" alt="Waterfall Cascade">
                        </div>
                        <div class="gallery-item">
                            <img src="https://images.unsplash.com/photo-1433086173841-718858a6022c?q=80&w=1887&auto=format&fit=crop" alt="Natural Pool">
                        </div>
                        <div class="gallery-item">
                            <img src="https://images.unsplash.com/photo-1509316785289-025f5b846b35?q=80&w=2076&auto=format&fit=crop" alt="Forest Trail">
                        </div>
                    </div>
                </div>

                <!-- Right Column: Information Card -->
                <div class="spot-info-card">
                    <h3 class="section-subtitle" style="margin-top: 0;">Adventure Information</h3>
                    
                    <div class="info-grid">
                        <div class="info-item">
                            <span class="material-icons-outlined">schedule</span>
                            <div class="info-text">
                                <span class="info-label">Tour Duration</span>
                                <span class="info-value">4-6 Hours</span>
                            </div>
                        </div>
                        <div class="info-item">
                            <span class="material-icons-outlined">payments</span>
                            <div class="info-text">
                                <span class="info-label">Tour Guide Fee</span>
                                <span class="info-value">₱350 per group (max 5 pax)</span>
                            </div>
                        </div>
                        <div class="info-item">
                            <span class="material-icons-outlined">terrain</span>
                            <div class="info-text">
                                <span class="info-label">Difficulty Level</span>
                                <span class="info-value">Moderate to Hard</span>
                            </div>
                        </div>
                        <div class="info-item">
                            <span class="material-icons-outlined">height</span>
                            <div class="info-text">
                                <span class="info-label">Total Elevation</span>
                                <span class="info-value">300 meters</span>
                            </div>
                        </div>
                    </div>

                    <h3 class="section-subtitle">Operating Hours</h3>
                    <div class="info-item">
                        <span class="material-icons-outlined">access_time</span>
                        <div class="info-text">
                            <span class="info-label">Daily Schedule</span>
                            <span class="info-value">7:00 AM - 4:00 PM</span>
                        </div>
                    </div>

                    <h3 class="section-subtitle">Trio Falls Tour Information</h3>
                    <div style="background: #e8f5e9; padding: 15px; border-radius: 8px; margin: 15px 0;">
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <span class="material-icons-outlined" style="color: #4caf50;">tour</span>
                            <span style="font-weight: 600; color: #2e7d32;">Trio Falls Adventure Package</span>
                        </div>
                        <p style="margin: 10px 0 0; font-size: 0.9rem; color: #1b5e20;">
                            This tour covers three waterfalls: Burong Falls, Otso-Otso Falls, and Kaytitinga Falls. 
                            The PHP 350 guide fee covers the entire group for all three destinations.
                        </p>
                    </div>

                    <h3 class="section-subtitle">Essential Equipment</h3>
                    <ul style="margin: 15px 0; padding-left: 20px; color: var(--text-light); font-size: 0.95rem;">
                        <li>Waterproof hiking shoes</li>
                        <li>Swimwear and towel</li>
                        <li>Waterproof backpack</li>
                        <li>Minimum 2L water supply</li>
                        <li>Trail snacks and lunch</li>
                        <li>Dry bag for electronics</li>
                        <li>First aid kit</li>
                        <li>Emergency whistle</li>
                    </ul>

                    <button class="booking-btn glow-effect" onclick="location.href='/coderistyarn2/sjdm-user/index.php#booking'">
                        <span class="material-icons-outlined">waterfall</span>
                        BOOK WATERFALL ADVENTURE
                    </button>
                </div>
            </div>

            <!-- Call to Action Section -->
            <div class="cta-section">
                <h2 class="cta-title">Experience the Eight Wonders of Otso-Otso Falls</h2>
                <p class="cta-text">
                    Embark on an unforgettable journey through eight magnificent waterfalls. 
                    Our certified adventure guides will lead you through this natural wonderland 
                    while ensuring your safety and comfort.
                </p>
                <div class="cta-buttons">
                    <button class="btn-primary" onclick="location.href='/coderistyarn2/sjdm-user/index.php#booking'">
                        Book Complete Tour
                    </button>
                    <button class="btn-secondary" onclick="location.href='/coderistyarn2/sjdm-user/index.php#guides'">
                        Find Adventure Guide
                    </button>
                <button class="btn-secondary" onclick="location.href='/coderistyarn2/sjdm-user/tourist-spots.php'">
                        <span class="material-icons-outlined">place</span>
                        View All Destinations
                    </button></div>
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