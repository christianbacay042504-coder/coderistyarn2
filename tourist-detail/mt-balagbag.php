<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mt. Balagbag - San Jose del Monte Tourism</title>
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
            padding: 0 10px;
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

        .info-grid  {
        display: grid;
        grid-template-columns: 1fr;
        gap: 15px;
        margin: 20px 0;
    }

    .info-item {
        display: flex;
        align-items: center;
        gap: 20px;
        padding: 20px;
        background: var(--secondary);
        border-radius: var(--radius-md);
        transition: transform 0.3s ease;
    }

    .info-item:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }

    .info-item .material-icons-outlined {
        color: var(--primary);
        font-size: 24px;
    }

    .info-text {
        flex: 1;
    }

    .info-label {
        display: block;
        font-size: 0.85rem;
        color: var(--text-light);
        margin-bottom: 3px;
    }

    .info-value {
        display: block;
        font-size: 1.05rem;
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

        .trail-info {
            background: #f3e5f5;
            border-left: 4px solid #9c27b0;
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
                    <h1 class="page-title">Mt. Balagbag</h1>
                </div>
            </div>
        </div>
    </div>

    <!-- Hero Section -->
    <section class="spot-hero">
        <div class="hero-content">
            <h1 class="hero-title">Mt. Balagbag</h1>
            <p class="hero-subtitle">The "Mt. Pulag of Bulacan" - Experience Breathtaking Panoramas</p>
        </div>
    </section>

    <!-- Main Content -->
    <main class="container">
        <div class="spot-content">
            <div class="spot-details">
                <!-- Left Column: Content -->
                <div>
                    <h2 class="section-title">About Mt. Balagbag</h2>
                    <p class="description">
                        Mt. Balagbag, standing at 777 meters above sea level, is one of the most popular hiking 
                        destinations near Metro Manila. Known as the "Mt. Pulag of Bulacan," it offers stunning 
                        360-degree panoramic views of Metro Manila, Laguna de Bay, and the surrounding mountain ranges.
                    </p>
                    
                    <!-- Trail Information -->
                    <div class="trail-info">
                        <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 10px;">
                            <span class="material-icons-outlined" style="color: #9c27b0;">trail_length</span>
                            <h4 style="margin: 0; color: #7b1fa2; font-size: 1.1rem;">Trail Information</h4>
                        </div>
                        <p style="margin: 0; color: #4a148c; font-size: 0.95rem;">
                            Perfect for beginner to intermediate hikers. Multiple trail options available with 
                            well-established paths and gradual ascents. Ideal for sunrise viewing.
                        </p>
                    </div>

                    <h3 class="section-subtitle">Why Hike Mt. Balagbag?</h3>
                    <ul class="features-list">
                        <li><strong>Accessibility:</strong> Just 1.5 hours from Metro Manila, perfect for day hikes</li>
                        <li><strong>Beginner-Friendly:</strong> Gradual ascent with well-established, marked trails</li>
                        <li><strong>Panoramic Views:</strong> See four provinces from the summit on clear days</li>
                        <li><strong>Sea of Clouds:</strong> High probability of witnessing beautiful morning cloud formations</li>
                        <li><strong>Community Support:</strong> Well-organized with local guides and registration system</li>
                        <li><strong>Year-Round Hiking:</strong> Accessible throughout the year with varying seasonal beauty</li>
                    </ul>

                    <h3 class="section-subtitle">Trail Options</h3>
                    <ul class="features-list">
                        <li><strong>Standard Trail:</strong> 3-4 hours ascent, 2-3 hours descent via main route</li>
                        <li><strong>Scenic Trail:</strong> Passes through grasslands and pine-like tree areas</li>
                        <li><strong>Adventure Trail:</strong> Includes river crossings and steeper, more challenging sections</li>
                        <li><strong>Sunrise Hike:</strong> Start at 3 AM to reach summit for sunrise viewing</li>
                        <li><strong>Day Hike:</strong> Complete circuit in 6-8 hours including breaks</li>
                    </ul>

                    <!-- Photo Gallery -->
                    <h3 class="section-subtitle">Gallery</h3>
                    <div class="photo-gallery">
                        <div class="gallery-item">
                            <img src="https://images.unsplash.com/photo-1551632811-561732d1e306?q=80&w=2070&auto=format&fit=crop" alt="Mountain Summit">
                        </div>
                        <div class="gallery-item">
                            <img src="https://images.unsplash.com/photo-1506905925346-21bda4d32df4?q=80&w=2070&auto=format&fit=crop" alt="Sea of Clouds">
                        </div>
                        <div class="gallery-item">
                            <img src="https://images.unsplash.com/photo-1464822759023-fed622ff2c3b?q=80&w=2070&auto=format&fit=crop" alt="Hiking Trail">
                        </div>
                    </div>
                </div>

                <!-- Right Column: Information Card -->
                <div class="spot-info-card">
                    <h3 class="section-subtitle" style="margin-top: 0;">Hiking Information</h3>
                    
                    <div class="info-grid">
                        <div class="info-item">
                            <span class="material-icons-outlined">schedule</span>
                            <div class="info-text">
                                <span class="info-label">Hike Duration</span>
                                <span class="info-value">4-6 Hours</span>
                            </div>
                        </div>
                        <div class="info-item">
                            <span class="material-icons-outlined">payments</span>
                            <div class="info-text">
                                <span class="info-label">Barangay Entrance/Registration</span>
                                <span class="info-value">~₱25 per person</span>
                            </div>
                        </div>
                        <div class="info-item">
                            <span class="material-icons-outlined">payments</span>
                            <div class="info-text">
                                <span class="info-label">Peak/Private Access Fee</span>
                                <span class="info-value">~₱50 - ₱100+ per person</span>
                            </div>
                        </div>
                        <div class="info-item">
                            <span class="material-icons-outlined">groups</span>
                            <div class="info-text">
                                <span class="info-label">Mandatory Tour Guide</span>
                                <span class="info-value">~₱350 per group (5-7 pax)</span>
                            </div>
                        </div>
                        <div class="info-item">
                            <span class="material-icons-outlined">hotel</span>
                            <div class="info-text">
                                <span class="info-label">Overnight Fee</span>
                                <span class="info-value">~₱750 per group</span>
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
                                <span class="info-value">777 MASL</span>
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

                    <h3 class="section-subtitle">Optimal Hiking Schedule</h3>
                    <div class="info-item">
                        <span class="material-icons-outlined">brightness_low</span>
                        <div class="info-text">
                            <span class="info-label">Sunrise Hike</span>
                            <span class="info-value">Start: 3:00 AM</span>
                        </div>
                    </div>

                    <div class="info-item">
                        <span class="material-icons-outlined">brightness_high</span>
                        <div class="info-text">
                            <span class="info-label">Day Hike</span>
                            <span class="info-value">Start: 7:00 AM</span>
                        </div>
                    </div>
                    
                    <div class="info-item">
                        <span class="material-icons-outlined">calendar_today</span>
                        <div class="info-text">
                            <span class="info-label">Best Season</span>
                            <span class="info-value">November to March</span>
                        </div>
                    </div>

                    <h3 class="section-subtitle">Essential Gear & Supplies</h3>
                    <ul style="margin: 15px 0; padding-left: 20px; color: var(--text-light); font-size: 0.95rem;">
                        <li>Minimum 2L water supply</li>
                        <li>Trail food and energy snacks</li>
                        <li>Rain jacket or poncho</li>
                        <li>Headlamp with extra batteries (for sunrise hike)</li>
                        <li>Basic first aid kit</li>
                        <li>Extra cash (₱500-1000 for emergencies)</li>
                        <li>Proper hiking shoes</li>
                        <li>Sun protection (hat, sunscreen)</li>
                    </ul>

                    <h3 class="section-subtitle">Mt. Balagbag Fee Structure</h3>
                    <div style="background: #fff3e0; padding: 15px; border-radius: 8px; margin: 15px 0;">
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <span class="material-icons-outlined" style="color: #ff9800;">payments</span>
                            <span style="font-weight: 600; color: #ef6c00;">Complete Fee Breakdown</span>
                        </div>
                        <p style="margin: 10px 0 0; font-size: 0.9rem; color: #5d4037;">
                            • Barangay Entrance/Registration: ~₱25 per person<br>
                            • Peak/Private Access Fee: ~₱50 - ₱100+ per person<br>
                            • Mandatory Tour Guide: ~₱350 per group (5-7 pax)<br>
                            • Overnight Fee: ~₱750 per group (if camping)
                        </p>
                    </div>

                    <h3 class="section-subtitle">Guide Requirements</h3>
                    <div style="background: #fff3e0; padding: 15px; border-radius: 8px; margin: 15px 0;">
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <span class="material-icons-outlined" style="color: #ff9800;">groups</span>
                            <span style="font-weight: 600; color: #ef6c00;">Guide Recommended</span>
                        </div>
                        <p style="margin: 10px 0 0; font-size: 0.9rem; color: #5d4037;">
                            Guide required for groups of 5+ hikers. Recommended for first-time visitors 
                            to ensure safety and optimal route selection.
                        </p>
                    </div>

                    <button class="btn-primary" onclick="location.href='/coderistyarn2/sjdm-user/index.php#booking'" style="width: 100%; margin-top: 25px; color: var(--secondary); background-color: var(--primary);">
                        <span class="material-icons-outlined">hiking</span>
                        Book Mountain Guide
                    </button>
                </div>
            </div>

            <!-- Call to Action Section -->
            <div class="cta-section">
                <h2 class="cta-title">Ready to Conquer Mt. Balagbag?</h2>
                <p class="cta-text">
                    Experience the most spectacular sunrise views near Manila with our certified mountain guides. 
                    Create unforgettable memories atop the "Mt. Pulag of Bulacan."
                </p>
                <div class="cta-buttons">
                    <button class="btn-primary" onclick="location.href='/coderistyarn2/sjdm-user/index.php#booking'">
                        Book Sunrise Hike
                    </button>
                    <button class="btn-secondary" onclick="location.href='/coderistyarn2/sjdm-user/index.php#guides'">
                        Find Mountain Guide
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