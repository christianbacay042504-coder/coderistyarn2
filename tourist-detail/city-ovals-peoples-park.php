<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>City Oval & People's Park - San Jose del Monte Tourism</title>
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
                       url('https://images.unsplash.com/photo-1578662996442-48f60103fc96?q=80&w=2070&auto=format&fit=crop');
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

        .facilities-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin: 30px 0;
        }

        .facility-card {
            background: white;
            padding: 25px;
            border-radius: 12px;
            text-align: center;
            box-shadow: var(--shadow);
            border: 1px solid var(--border);
            transition: transform 0.3s ease;
        }

        .facility-card:hover {
            transform: translateY(-5px);
        }

        .facility-card .material-icons-outlined {
            font-size: 48px;
            color: var(--primary);
            margin-bottom: 15px;
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
            
            .facilities-grid {
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
            
            .facilities-grid {
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
                    <h1 class="page-title">City Oval & People's Park</h1>
                </div>
            </div>
        </div>
    </div>

    <!-- Hero Section -->
    <section class="spot-hero">
        <div class="hero-content">
            <h1 class="hero-title">City Oval & People's Park</h1>
            <p class="hero-subtitle">The Premier Recreational Hub of San Jose del Monte</p>
        </div>
    </section>

    <!-- Main Content -->
    <main class="container">
        <div class="spot-content">
            <div class="spot-details">
                <!-- Left Column: Content -->
                <div>
                    <h2 class="section-title">About City Oval & People's Park</h2>
                    <p class="description">
                        Spanning over 10 hectares in the heart of San Jose del Monte, the City Oval and People's Park 
                        complex serves as the city's premier sports, recreation, and community event facility. This 
                        multi-purpose complex combines world-class athletic facilities with beautiful green spaces.
                    </p>
                    
                    <h3 class="section-subtitle">Facility Highlights</h3>
                    <ul class="features-list">
                        <li><strong>International Standard Track:</strong> 400m rubberized athletic track for professional training</li>
                        <li><strong>Regulation Football Field:</strong> FIFA-standard grass pitch for competitive matches</li>
                        <li><strong>Multi-Court Basketball Complex:</strong> Covered courts for year-round play</li>
                        <li><strong>Children's Adventure Playground:</strong> Modern, safe play equipment for all ages</li>
                        <li><strong>Outdoor Fitness Stations:</strong> Complete circuit training equipment</li>
                        <li><strong>Cultural Amphitheater:</strong> 500-seat open-air performance venue</li>
                        <li><strong>Skating Facilities:</strong> Professional-grade roller skating rink</li>
                        <li><strong>Jogging Paths:</strong> 2km of shaded jogging paths throughout the park</li>
                    </ul>

                    <!-- Facilities Grid -->
                    <h3 class="section-subtitle">Facilities Overview</h3>
                    <div class="facilities-grid">
                        <div class="facility-card">
                            <span class="material-icons-outlined">sports_soccer</span>
                            <h4 style="margin: 10px 0 5px; color: var(--text-dark);">Football Field</h4>
                            <p style="color: var(--text-light); font-size: 0.9rem; margin: 0;">FIFA Standard Grass Pitch</p>
                        </div>
                        <div class="facility-card">
                            <span class="material-icons-outlined">directions_run</span>
                            <h4 style="margin: 10px 0 5px; color: var(--text-dark);">Athletic Track</h4>
                            <p style="color: var(--text-light); font-size: 0.9rem; margin: 0;">400m Professional Track</p>
                        </div>
                        <div class="facility-card">
                            <span class="material-icons-outlined">child_care</span>
                            <h4 style="margin: 10px 0 5px; color: var(--text-dark);">Playground</h4>
                            <p style="color: var(--text-light); font-size: 0.9rem; margin: 0;">Children's Activity Zone</p>
                        </div>
                        <div class="facility-card">
                            <span class="material-icons-outlined">fitness_center</span>
                            <h4 style="margin: 10px 0 5px; color: var(--text-dark);">Outdoor Gym</h4>
                            <p style="color: var(--text-light); font-size: 0.9rem; margin: 0;">Complete Circuit Training</p>
                        </div>
                    </div>

                    <!-- Photo Gallery -->
                    <h3 class="section-subtitle">Gallery</h3>
                    <div class="photo-gallery">
                        <div class="gallery-item">
                            <img src="https://images.unsplash.com/photo-1578662996442-48f60103fc96?q=80&w=2070&auto=format&fit=crop" alt="Athletic Track">
                        </div>
                        <div class="gallery-item">
                            <img src="https://images.unsplash.com/photo-1546519638-68e109498ffc?q=80&w=2090&auto=format&fit=crop" alt="Football Field">
                        </div>
                        <div class="gallery-item">
                            <img src="https://images.unsplash.com/photo-1544919982-b61976a0d7ed?q=80&w=2069&auto=format&fit=crop" alt="Playground">
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
                                <span class="info-value">5:00 AM - 10:00 PM</span>
                            </div>
                        </div>
                        <div class="info-item">
                            <span class="material-icons-outlined">payments</span>
                            <div class="info-text">
                                <span class="info-label">Admission Fee</span>
                                <span class="info-value">Free Entry</span>
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
                            <span class="material-icons-outlined">groups</span>
                            <div class="info-text">
                                <span class="info-label">Capacity</span>
                                <span class="info-value">5,000+ Visitors</span>
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
                    <ul style="margin: 15px 0; padding-left: 20px; color: var(--text-light); font-size: 0.95rem;">
                        <li>No smoking within the premises</li>
                        <li>Proper disposal of waste in designated bins</li>
                        <li>Respect other park users and facility operators</li>
                        <li>Children must be supervised at all times</li>
                        <li>Follow facility-specific operating hours</li>
                        <li>Pets must be leashed and cleaned after</li>
                    </ul>

                    <h3 class="section-subtitle">Location & Access</h3>
                    <div class="info-item">
                        <span class="material-icons-outlined">location_on</span>
                        <div class="info-text">
                            <span class="info-label">Address</span>
                            <span class="info-value">City Center, San Jose del Monte, Bulacan</span>
                        </div>
                    </div>
                    <div class="info-item">
                        <span class="material-icons-outlined">local_parking</span>
                        <div class="info-text">
                            <span class="info-label">Parking</span>
                            <span class="info-value">Ample parking available</span>
                        </div>
                    </div>

                    <button class="btn-secondary" onclick="location.href='/coderistyarn2/sjdm-user/index.php#guides'" style="width: 100%; margin-top: 25px; color: var(--secondary); background-color: var(--primary);">
                        <span class="material-icons-outlined">tour</span>
                        Book City Tour Guide
                    </button>
                </div>
            </div>

            <!-- Call to Action Section -->
            <div class="cta-section">
                <h2 class="cta-title">Experience the Heart of San Jose del Monte</h2>
                <p class="cta-text">
                    Whether you're looking for athletic training, family recreation, or community events, 
                    City Oval & People's Park offers something for everyone.
                </p>
                <div class="cta-buttons">
                    <button class="btn-primary" onclick="location.href='/coderistyarn2/sjdm-user/index.php#guides'">
                        Find City Guide
                    </button>
                    <button class="btn-secondary" onclick="location.href='/coderistyarn2/sjdm-user/index.php#hotels'">
                        Nearby Accommodations
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