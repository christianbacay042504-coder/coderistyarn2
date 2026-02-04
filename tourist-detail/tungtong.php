<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tungtong Falls - San Jose del Monte Tourism</title>
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
                       url('https://images.unsplash.com/photo-1511884642898-4c92249e20b6?q=80&w=2070&auto=format&fit=crop');
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

        .waterfall-info {
            background: #e3f2fd;
            border-left: 4px solid #2196f3;
            padding: 20px;
            border-radius: 8px;
            margin: 25px 0;
        }

        .swimming-info {
            background: #e8f5e9;
            border-left: 4px solid #4caf50;
            padding: 20px;
            border-radius: 8px;
            margin: 25px 0;
        }

        .trail-info {
            background: #fff3e0;
            border-left: 4px solid #ff9800;
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

        .pool-features {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin: 30px 0;
        }

        .pool-feature-card {
            background: white;
            padding: 25px;
            border-radius: 12px;
            text-align: center;
            box-shadow: var(--shadow);
            border: 1px solid var(--border);
            transition: transform 0.3s ease;
        }

        .pool-feature-card:hover {
            transform: translateY(-5px);
        }

        .pool-feature-card .material-icons-outlined {
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
            
            .pool-features {
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
            
            .pool-features {
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
                    <h1 class="page-title">Tungtong Falls</h1>
                </div>
            </div>
        </div>
    </div>

    <!-- Hero Section -->
    <section class="spot-hero">
        <div class="hero-content">
            <h1 class="hero-title">Tungtong Falls</h1>
            <p class="hero-subtitle">The Hidden Canyon Waterfall of San Jose del Monte</p>
        </div>
    </section>

    <!-- Main Content -->
    <main class="container">
        <div class="spot-content">
            <div class="spot-details">
                <!-- Left Column: Content -->
                <div>
                    <h2 class="section-title">About Tungtong Falls</h2>
                    <p class="description">
                        Tungtong Falls is a spectacular 25-meter waterfall located within a dramatic canyon formation 
                        in San Jose del Monte. Named after the local term "tungtong" meaning "to climb or ascend," 
                        this waterfall features a unique rock amphitheater that creates perfect acoustics and 
                        breathtaking visual effects when sunlight hits the cascading water.
                    </p>
                    
                    <!-- Waterfall Information -->
                    <div class="waterfall-info">
                        <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 10px;">
                            <span class="material-icons-outlined" style="color: #2196f3;">waterfall</span>
                            <h4 style="margin: 0; color: #1565c0; font-size: 1.1rem;">Waterfall Characteristics</h4>
                        </div>
                        <p style="margin: 0; color: #0d47a1; font-size: 0.95rem;">
                            The waterfall flows year-round with increased volume during rainy season (June-October). 
                            The canyon walls create a natural amphitheater that amplifies the sound of falling water, 
                            creating a mesmerizing sensory experience.
                        </p>
                    </div>

                    <!-- Swimming Information -->
                    <div class="swimming-info">
                        <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 10px;">
                            <span class="material-icons-outlined" style="color: #4caf50;">pool</span>
                            <h4 style="margin: 0; color: #2e7d32; font-size: 1.1rem;">Swimming & Natural Pools</h4>
                        </div>
                        <p style="margin: 0; color: #1b5e20; font-size: 0.95rem;">
                            The base of the waterfall forms a deep natural pool (4-6 meters depth) perfect for swimming. 
                            Smaller pools along the river offer safe wading areas for children and non-swimmers.
                        </p>
                    </div>

                    <!-- Trail Information -->
                    <div class="trail-info">
                        <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 10px;">
                            <span class="material-icons-outlined" style="color: #ff9800;">hiking</span>
                            <h4 style="margin: 0; color: #ef6c00; font-size: 1.1rem;">Trail Information</h4>
                        </div>
                        <p style="margin: 0; color: #e65100; font-size: 0.95rem;">
                            The trail to Tungtong Falls involves moderate hiking with some rock scrambling. 
                            The path follows the river with several crossing points. Proper footwear is essential.
                        </p>
                    </div>

                    <h3 class="section-subtitle">Natural Pool Features</h3>
                    
                    <!-- Pool Features Grid -->
                    <div class="pool-features">
                        <div class="pool-feature-card">
                            <span class="material-icons-outlined">water</span>
                            <h4 style="margin: 10px 0 5px; color: var(--text-dark);">Main Pool</h4>
                            <p style="color: var(--text-light); font-size: 0.9rem; margin: 0;">Depth: 4-6 meters</p>
                        </div>
                        <div class="pool-feature-card">
                            <span class="material-icons-outlined">hot_tub</span>
                            <h4 style="margin: 10px 0 5px; color: var(--text-dark);">Jacuzzi Pools</h4>
                            <p style="color: var(--text-light); font-size: 0.9rem; margin: 0;">Natural water massage</p>
                        </div>
                        <div class="pool-feature-card">
                            <span class="material-icons-outlined">child_care</span>
                            <h4 style="margin: 10px 0 5px; color: var(--text-dark);">Kids' Pool</h4>
                            <p style="color: var(--text-light); font-size: 0.9rem; margin: 0;">Shallow, safe area</p>
                        </div>
                        <div class="pool-feature-card">
                            <span class="material-icons-outlined">filter_vintage</span>
                            <h4 style="margin: 10px 0 5px; color: var(--text-dark);">Natural Slides</h4>
                            <p style="color: var(--text-light); font-size: 0.9rem; margin: 0;">Rock water slides</p>
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

                    <h3 class="section-subtitle">Geological Features</h3>
                    <ul class="features-list">
                        <li><strong>Canyon Formation:</strong> Limestone walls creating natural amphitheater</li>
                        <li><strong>Rock Formations:</strong> Unique sedimentary rock patterns and fossil deposits</li>
                        <li><strong>Water Flow:</strong> Consistent year-round flow from mountain springs</li>
                        <li><strong>Natural Slides:</strong> Smooth rock surfaces creating natural water slides</li>
                        <li><strong>Pool System:</strong> Series of interconnected pools at different depths</li>
                        <li><strong>Microclimate:</strong> Cooler temperature within the canyon area</li>
                        <li><strong>Ecosystem:</strong> Home to unique aquatic plants and small freshwater fish</li>
                    </ul>

                    <h3 class="section-subtitle">Trail Specifications</h3>
                    <ul class="features-list">
                        <li><strong>Trail Length:</strong> 3 kilometers from parking area to waterfall</li>
                        <li><strong>Hike Duration:</strong> 45-60 minutes one way</li>
                        <li><strong>Elevation Gain:</strong> 120 meters gradual ascent</li>
                        <li><strong>River Crossings:</strong> 3 shallow crossings with stepping stones</li>
                        <li><strong>Trail Markers:</strong> Well-marked with color-coded ribbons and signs</li>
                        <li><strong>Rest Areas:</strong> 3 designated rest stops along the trail</li>
                        <li><strong>Viewpoints:</strong> 2 scenic viewpoints overlooking the canyon</li>
                    </ul>

                    <!-- Photo Gallery -->
                    <h3 class="section-subtitle">Gallery</h3>
                    <div class="photo-gallery">
                        <div class="gallery-item">
                            <img src="https://images.unsplash.com/photo-1511884642898-4c92249e20b6?q=80&w=2070&auto=format&fit=crop" alt="Main Waterfall">
                        </div>
                        <div class="gallery-item">
                            <img src="https://images.unsplash.com/photo-1509316785289-025f5b846b35?q=80&w=2076&auto=format&fit=crop" alt="Natural Pool">
                        </div>
                        <div class="gallery-item">
                            <img src="https://images.unsplash.com/photo-1551632811-561732d1e306?q=80&w=2070&auto=format&fit=crop" alt="Canyon Trail">
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
                                <span class="info-value">3-4 Hours</span>
                            </div>
                        </div>
                        <div class="info-item">
                            <span class="material-icons-outlined">payments</span>
                            <div class="info-text">
                                <span class="info-label">All-In Package</span>
                                <span class="info-value">₱1,300 per person</span>
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
                                <span class="info-label">Waterfall Height</span>
                                <span class="info-value">25 meters</span>
                            </div>
                        </div>
                    </div>

                    <h3 class="section-subtitle">Operating Hours</h3>
                    <div class="info-item">
                        <span class="material-icons-outlined">access_time</span>
                        <div class="info-text">
                            <span class="info-label">Daily Schedule</span>
                            <span class="info-value">7:00 AM - 5:00 PM</span>
                        </div>
                    </div>
                    <div class="info-item">
                        <span class="material-icons-outlined">calendar_today</span>
                        <div class="info-text">
                            <span class="info-label">Best Season</span>
                            <span class="info-value">November to May</span>
                        </div>
                    </div>

                    <h3 class="section-subtitle">Tungtong Falls All-In Package</h3>
                    <div style="background: #e8f5e9; padding: 15px; border-radius: 8px; margin: 15px 0;">
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <span class="material-icons-outlined" style="color: #4caf50;">all_inclusive</span>
                            <span style="font-weight: 600; color: #2e7d32;">Complete Adventure Package</span>
                        </div>
                        <p style="margin: 10px 0 0; font-size: 0.9rem; color: #1b5e20;">
                            The ₱1,300 ALL-IN package includes entrance fees, guide services, 
                            equipment rental, and meals for a complete hassle-free adventure experience.
                        </p>
                    </div>

                    <h3 class="section-subtitle">Essential Items to Bring</h3>
                    <ul style="margin: 15px 0; padding-left: 20px; color: var(--text-light); font-size: 0.95rem;">
                        <li>Swimwear and quick-dry clothing</li>
                        <li>Water shoes or sturdy sandals</li>
                        <li>Waterproof bag for valuables</li>
                        <li>Minimum 1.5L drinking water</li>
                        <li>Waterproof camera or phone case</li>
                        <li>Energy snacks and light lunch</li>
                        <li>Sun protection (hat, sunscreen)</li>
                        <li>Basic first aid kit</li>
                        <li>Extra set of dry clothes</li>
                    </ul>

                    <h3 class="section-subtitle">Safety Guidelines</h3>
                    <ul style="margin: 15px 0; padding-left: 20px; color: var(--text-light); font-size: 0.95rem;">
                        <li>Swim only in designated safe areas</li>
                        <li>Never swim alone - use the buddy system</li>
                        <li>Follow guide instructions for rock scrambling</li>
                        <li>Watch for slippery rocks near the waterfall</li>
                        <li>Stay on marked trails at all times</li>
                        <li>Check weather conditions before visiting</li>
                        <li>Inform someone of your hiking plans</li>
                    </ul>

                    <button class="booking-btn glow-effect" onclick="location.href='/coderistyarn2/sjdm-user/index.php#booking'">
                        <span class="material-icons-outlined">waterfall</span>
                        BOOK CANYON ADVENTURE
                    </button>
                </div>
            </div>

            <!-- Call to Action Section -->
            <div class="cta-section">
                <h2 class="cta-title">Discover the Canyon Beauty of Tungtong Falls</h2>
                <p class="cta-text">
                    Experience the perfect combination of adventure and relaxation at Tungtong Falls. 
                    Our experienced nature guides will ensure you have a safe and memorable visit 
                    to this hidden canyon wonder.
                </p>
                <div class="cta-buttons">
                    <button class="btn-primary" onclick="location.href='/coderistyarn2/sjdm-user/index.php#booking'">
                        Book Waterfall Tour
                    </button>
                    <button class="btn-secondary" onclick="location.href='/coderistyarn2/sjdm-user/index.php#guides'">
                        Find Nature Guide
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