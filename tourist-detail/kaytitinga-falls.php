<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kaytitinga Falls - San Jose del Monte Tourism</title>
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
                       url('https://images.unsplash.com/photo-1433086173841-718858a6022c?q=80&w=1887&auto=format&fit=crop');
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

        .swimming-info {
            background: #e3f2fd;
            border-left: 4px solid #2196f3;
            padding: 20px;
            border-radius: 8px;
            margin: 25px 0;
        }

        .difficulty-indicator {
            background: #f5f5f5;
            padding: 20px;
            border-radius: 10px;
            margin: 25px 0;
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
                    <h1 class="page-title">Kaytitinga Falls</h1>
                </div>
            </div>
        </div>
    </div>

    <!-- Hero Section -->
    <section class="spot-hero">
        <div class="hero-content">
            <h1 class="hero-title">Kaytitinga Falls</h1>
            <p class="hero-subtitle">The Three-Tiered Natural Wonder of San Jose del Monte</p>
        </div>
    </section>

    <!-- Main Content -->
    <main class="container">
        <div class="spot-content">
            <div class="spot-details">
                <!-- Left Column: Content -->
                <div>
                    <h2 class="section-title">About Kaytitinga Falls</h2>
                    <p class="description">
                        Kaytitinga Falls is one of the most picturesque waterfalls in San Jose del Monte, 
                        featuring three stunning tiers of cascading water surrounded by lush tropical forest. 
                        This destination is part of a trio falls tour (Burong, Otso-Otso, and Kaytitinga Falls) with a mandatory tour guide 
                        fee of PHP 350 per group (max 5 pax), arranged through the tourism office.
                    </p>
                    
                    <!-- Swimming Information -->
                    <div class="swimming-info">
                        <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 10px;">
                            <span class="material-icons-outlined" style="color: #2196f3;">pool</span>
                            <h4 style="margin: 0; color: #1565c0; font-size: 1.1rem;">Swimming Information</h4>
                        </div>
                        <p style="margin: 0; color: #0d47a1; font-size: 0.95rem;">
                            The natural pool at the base of the falls is perfect for swimming. Water depth ranges from 
                            3-6 feet with clear, cool water. Swimming is permitted during daylight hours only.
                        </p>
                    </div>

                    <h3 class="section-subtitle">Waterfall Tiers & Features</h3>
                    <ul class="features-list">
                        <li><strong>First Tier (Lower Falls):</strong> 15-foot drop into a natural swimming pool, most accessible section</li>
                        <li><strong>Second Tier (Middle Falls):</strong> 25-foot cascade with scenic viewpoint and photo opportunities</li>
                        <li><strong>Third Tier (Upper Falls):</strong> 40-foot dramatic drop accessible via moderate hiking trail</li>
                        <li><strong>Natural Features:</strong> Rock formations, small caves, and abundant tropical vegetation</li>
                        <li><strong>Wildlife:</strong> Various bird species and freshwater aquatic life in the pools</li>
                    </ul>

                    <h3 class="section-subtitle">Trail Information</h3>
                    <ul class="features-list">
                        <li><strong>Hike Duration:</strong> 45-60 minutes one way from trailhead to main falls</li>
                        <li><strong>Trail Length:</strong> 2.5 kilometers through mixed forest terrain</li>
                        <li><strong>Elevation Gain:</strong> 150 meters gradual ascent</li>
                        <li><strong>Trail Condition:</strong> Well-marked and maintained, moderate difficulty</li>
                        <li><strong>River Crossings:</strong> 2 shallow river crossings with stepping stones</li>
                    </ul>

                    <!-- Photo Gallery -->
                    <h3 class="section-subtitle">Gallery</h3>
                    <div class="photo-gallery">
                        <div class="gallery-item">
                            <img src="https://images.unsplash.com/photo-1433086173841-718858a6022c?q=80&w=1887&auto=format&fit=crop" alt="Main Waterfall Cascade">
                        </div>
                        <div class="gallery-item">
                            <img src="https://images.unsplash.com/photo-1509316785289-025f5b846b35?q=80&w=2076&auto=format&fit=crop" alt="Natural Swimming Pool">
                        </div>
                        <div class="gallery-item">
                            <img src="https://images.unsplash.com/photo-1551632811-561732d1e306?q=80&w=2070&auto=format&fit=crop" alt="Forest Trail">
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
                                <span class="info-label">Tour Duration</span>
                                <span class="info-value">3-5 Hours</span>
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
                                <span class="info-value">Moderate</span>
                            </div>
                        </div>
                        <div class="info-item">
                            <span class="material-icons-outlined">height</span>
                            <div class="info-text">
                                <span class="info-label">Elevation</span>
                                <span class="info-value">350 MASL</span>
                            </div>
                        </div>
                    </div>

                    <!-- Difficulty Indicator -->
                    <div class="difficulty-indicator">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                            <span style="font-weight: 600; color: var(--text-dark);">Trail Difficulty:</span>
                            <span style="color: #ff9800; font-weight: 600;">Moderate Level</span>
                        </div>
                        <div style="display: flex; gap: 5px;">
                            <div style="width: 100%; height: 8px; background: #ff9800; border-radius: 4px;"></div>
                            <div style="width: 100%; height: 8px; background: #ff9800; border-radius: 4px;"></div>
                            <div style="width: 100%; height: 8px; background: #ff9800; border-radius: 4px;"></div>
                            <div style="width: 100%; height: 8px; background: #f5f5f5; border-radius: 4px;"></div>
                            <div style="width: 100%; height: 8px; background: #f5f5f5; border-radius: 4px;"></div>
                        </div>
                    </div>

                    <h3 class="section-subtitle">Optimal Visiting Times</h3>
                    <div class="info-item">
                        <span class="material-icons-outlined">access_time</span>
                        <div class="info-text">
                            <span class="info-label">Morning Hours</span>
                            <span class="info-value">7:00 AM - 10:00 AM</span>
                        </div>
                    </div>
                    <div class="info-item">
                        <span class="material-icons-outlined">calendar_today</span>
                        <div class="info-text">
                            <span class="info-label">Best Season</span>
                            <span class="info-value">November to May</span>
                        </div>
                    </div>

                    <h3 class="section-subtitle">Essential Items</h3>
                    <ul style="margin: 15px 0; padding-left: 20px; color: var(--text-light); font-size: 0.95rem;">
                        <li>Swimwear and towel</li>
                        <li>Water shoes or sandals</li>
                        <li>Waterproof bag for valuables</li>
                        <li>Snacks and drinking water</li>
                        <li>Waterproof camera or phone case</li>
                        <li>Sun protection (hat, sunscreen)</li>
                    </ul>

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

                    <button class="btn-primary" onclick="location.href='/coderistyarn2/sjdm-user/index.php#booking'" style="width: 100%; margin-top: 25px; color: var(--secondary); background-color: var(--primary);">
                        <span class="material-icons-outlined">waterfall</span>Book Waterfall Tour
                    </button>
                </div>
            </div>

            <!-- Call to Action Section -->
            <div class="cta-section">
                <h2 class="cta-title">Discover the Beauty of Kaytitinga Falls</h2>
                <p class="cta-text">
                    Experience one of San Jose del Monte's most stunning natural attractions. 
                    Our experienced nature guides will ensure a safe and memorable adventure.
                </p>
                <div class="cta-buttons">
                    <button class="btn-primary" onclick="location.href='/coderistyarn2/sjdm-user/index.php#booking'">
                        Book Tour Now
                    </button>
                    <button class="btn-secondary" onclick="location.href='/coderistyarn2/sjdm-user/index.php#guides'">
                        Find Nature Guide
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