<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Padre Pio Shrine - San Jose del Monte Tourism</title>
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
                       url('https://images.unsplash.com/photo-1542766788-a2f588f447ee?q=80&w=2067&auto=format&fit=crop');
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

        .healing-info {
            background: #fce4ec;
            border-left: 4px solid #e91e63;
            padding: 20px;
            border-radius: 8px;
            margin: 25px 0;
        }

        .confession-info {
            background: #e8f5e9;
            border-left: 4px solid #4caf50;
            padding: 20px;
            border-radius: 8px;
            margin: 25px 0;
        }

        .stigmata-exhibit {
            background: #fff3e0;
            border-left: 4px solid #ff9800;
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
                    <h1 class="page-title">Padre Pio Shrine</h1>
                </div>
            </div>
        </div>
    </div>

    <!-- Hero Section -->
    <section class="spot-hero">
        <div class="hero-content">
            <h1 class="hero-title">Padre Pio Shrine</h1>
            <p class="hero-subtitle">A Sanctuary of Healing and Spiritual Renewal</p>
        </div>
    </section>

    <!-- Main Content -->
    <main class="container">
        <div class="spot-content">
            <div class="spot-details">
                <!-- Left Column: Content -->
                <div>
                    <h2 class="section-title">About Padre Pio Shrine</h2>
                    <p class="description">
                        The Padre Pio Shrine in San Jose del Monte is a major Catholic pilgrimage site dedicated to 
                        Saint Padre Pio of Pietrelcina, known for his stigmata and miraculous healings. Established in 1998, 
                        this spiritual complex has become one of the most visited religious sites in Bulacan, attracting 
                        devotees seeking spiritual guidance, healing, and miracles.
                    </p>
                    
                    <!-- Healing Information -->
                    <div class="healing-info">
                        <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 10px;">
                            <span class="material-icons-outlined" style="color: #e91e63;">healing</span>
                            <h4 style="margin: 0; color: #c2185b; font-size: 1.1rem;">Healing Ministry</h4>
                        </div>
                        <p style="margin: 0; color: #880e4f; font-size: 0.95rem;">
                            The shrine is renowned for healing Masses and prayer services. Many devotees report 
                            physical and spiritual healings through the intercession of Saint Padre Pio.
                        </p>
                    </div>

                    <!-- Confession Information -->
                    <div class="confession-info">
                        <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 10px;">
                            <span class="material-icons-outlined" style="color: #4caf50;">account_balance</span>
                            <h4 style="margin: 0; color: #2e7d32; font-size: 1.1rem;">Confession Schedule</h4>
                        </div>
                        <p style="margin: 0; color: #1b5e20; font-size: 0.95rem;">
                            Daily Confessions: 8:00 AM - 11:00 AM, 2:00 PM - 5:00 PM, 6:00 PM - 8:00 PM
                            Special confessions available upon request for groups and retreats.
                        </p>
                    </div>

                    <!-- Stigmata Exhibit -->
                    <div class="stigmata-exhibit">
                        <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 10px;">
                            <span class="material-icons-outlined" style="color: #ff9800;">museum</span>
                            <h4 style="margin: 0; color: #ef6c00; font-size: 1.1rem;">Stigmata Relic Exhibit</h4>
                        </div>
                        <p style="margin: 0; color: #e65100; font-size: 0.95rem;">
                            The shrine houses authenticated relics of Saint Padre Pio, including a piece of his 
                            blood-stained glove and other personal items. Open for veneration daily.
                        </p>
                    </div>

                    <h3 class="section-subtitle">Shrine Features & Facilities</h3>
                    <ul class="features-list">
                        <li><strong>Main Chapel:</strong> 800-seat capacity with Italian-inspired architecture</li>
                        <li><strong>Relic Museum:</strong> Exhibits authentic relics and personal items of Saint Padre Pio</li>
                        <li><strong>Prayer Garden:</strong> Serene outdoor space with life-size statues and Stations of the Cross</li>
                        <li><strong>Healing Water Fountain:</strong> Blessed water source for anointment and prayer</li>
                        <li><strong>Confession Complex:</strong> 12 confessionals with priests available throughout the day</li>
                        <li><strong>Retreat Center:</strong> Accommodates up to 100 overnight guests for spiritual retreats</li>
                        <li><strong>Bookstore & Gift Shop:</strong> Religious items, books, and devotional materials</li>
                        <li><strong>Pilgrim Rest Area:</strong> Comfortable seating and facilities for elderly and disabled visitors</li>
                    </ul>

                    <h3 class="section-subtitle">Annual Celebrations & Events</h3>
                    <ul class="features-list">
                        <li><strong>September 23:</strong> Feast Day of Saint Padre Pio - 24-hour vigil and healing Mass</li>
                        <li><strong>Every 23rd of the Month:</strong> Monthly novena and healing service</li>
                        <li><strong>Lenten Season:</strong> Special Way of the Cross and penitential services</li>
                        <li><strong>Christmas Novena:</strong> Nine-day Mass and prayer services leading to Christmas</li>
                        <li><strong>Healing Crusades:</strong> Quarterly healing Masses with guest priests</li>
                        <li><strong>Youth Retreats:</strong> Monthly spiritual formation for young devotees</li>
                    </ul>

                    <!-- Photo Gallery -->
                    <h3 class="section-subtitle">Gallery</h3>
                    <div class="photo-gallery">
                        <div class="gallery-item">
                            <img src="https://images.unsplash.com/photo-1542766788-a2f588f447ee?q=80&w=2067&auto=format&fit=crop" alt="Main Chapel">
                        </div>
                        <div class="gallery-item">
                            <img src="https://images.unsplash.com/photo-1517101724602-c257fe568157?q=80&w=2069&auto=format&fit=crop" alt="Prayer Garden">
                        </div>
                        <div class="gallery-item">
                            <img src="https://images.unsplash.com/photo-1568605114967-8130f3a36994?q=80&w=2070&auto=format&fit=crop" alt="Relic Exhibit">
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
                                <span class="info-label">Opening Hours</span>
                                <span class="info-value">6:00 AM - 9:00 PM</span>
                            </div>
                        </div>
                        <div class="info-item">
                            <span class="material-icons-outlined">payments</span>
                            <div class="info-text">
                                <span class="info-label">Entrance Fee</span>
                                <span class="info-value">Free (Donations Welcome)</span>
                            </div>
                        </div>
                        <div class="info-item">
                            <span class="material-icons-outlined">wheelchair_pickup</span>
                            <div class="info-text">
                                <span class="info-label">Accessibility</span>
                                <span class="info-value">Fully Accessible</span>
                            </div>
                        </div>
                        <div class="info-item">
                            <span class="material-icons-outlined">groups</span>
                            <div class="info-text">
                                <span class="info-label">Daily Visitors</span>
                                <span class="info-value">500-2,000+</span>
                            </div>
                        </div>
                    </div>

                    <h3 class="section-subtitle">Mass Schedules</h3>
                    <div class="info-item">
                        <span class="material-icons-outlined">church</span>
                        <div class="info-text">
                            <span class="info-label">Weekday Masses</span>
                            <span class="info-value">6:00 AM, 12:00 PM, 6:00 PM</span>
                        </div>
                    </div>
                    <div class="info-item">
                        <span class="material-icons-outlined">event</span>
                        <div class="info-text">
                            <span class="info-label">Sunday Masses</span>
                            <span class="info-value">6:00 AM, 8:00 AM, 10:00 AM, 4:00 PM, 6:00 PM</span>
                        </div>
                    </div>
                    <div class="info-item">
                        <span class="material-icons-outlined">healing</span>
                        <div class="info-text">
                            <span class="info-label">Healing Mass</span>
                            <span class="info-value">Every Thursday 6:00 PM</span>
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
                        <li>Silence must be observed in all prayer areas</li>
                        <li>Proper religious attire required (no shorts, sleeveless, or revealing clothing)</li>
                        <li>No photography or videography during Mass and prayer services</li>
                        <li>Mobile phones must be switched to silent mode</li>
                        <li>Food and drinks prohibited inside the chapel</li>
                        <li>Respectful behavior expected at all times</li>
                        <li>Follow instructions from shrine volunteers and staff</li>
                    </ul>

                    <h3 class="section-subtitle">Contact Information</h3>
                    <div class="info-item">
                        <span class="material-icons-outlined">location_on</span>
                        <div class="info-text">
                            <span class="info-label">Address</span>
                            <span class="info-value">Padre Pio Road, San Jose del Monte, Bulacan</span>
                        </div>
                    </div>
                    <div class="info-item">
                        <span class="material-icons-outlined">phone</span>
                        <div class="info-text">
                            <span class="info-label">Office Number</span>
                            <span class="info-value">+63 917 890 1234</span>
                        </div>
                    </div>
                    <div class="info-item">
                        <span class="material-icons-outlined">email</span>
                        <div class="info-text">
                            <span class="info-label">Email</span>
                            <span class="info-value">info@padrepiobulacan.org</span>
                        </div>
                    </div>

                    <button class="booking-btn glow-effect" onclick="location.href='/coderistyarn2/sjdm-user/index.php#guides'">
                        <span class="material-icons-outlined">tour</span>
                        BOOK SPIRITUAL TOUR
                    </button>
                </div>
            </div>

            <!-- Call to Action Section -->
            <div class="cta-section">
                <h2 class="cta-title">Experience Spiritual Healing at Padre Pio Shrine</h2>
                <p class="cta-text">
                    Join thousands of devotees in prayer and reflection at this sacred sanctuary. 
                    Our spiritual guides can help you make the most of your pilgrimage experience, 
                    whether you're seeking healing, confession, or spiritual guidance.
                </p>
                <div class="cta-buttons">
                    <button class="btn-primary" onclick="location.href='/coderistyarn2/sjdm-user/index.php#guides'">
                        Book Pilgrimage Guide
                    </button>
                    <button class="btn-secondary" onclick="location.href='/coderistyarn2/sjdm-user/index.php#events'">
                        View Healing Events
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