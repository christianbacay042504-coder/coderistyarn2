<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Discover San Jose del Monte | Your Gateway to Nature & Adventure</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700;900&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet">
    <link rel="stylesheet" href="landingpage/style.css">
</head>
<body>
    <!-- Navigation -->
    <nav id="navbar">
        <div class="logo">SJDM TOURS</div>
        <div class="nav-links">
            <a href="#home">Home</a>
            <a href="#destinations">Destinations</a>
            <a href="#guides">Tour Guides</a>
            <a href="#about">About</a>
        </div>
        <div class="mobile-menu">
            <span class="material-icons-outlined">menu</span>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero" id="home">
        <div class="hero-bg"></div>
        <div class="hero-overlay"></div>
        <div class="hero-content">
            <div class="hero-subtitle">THE BALCONY OF METROPOLIS</div>
            <h1 class="hero-title">DISCOVER<br>SAN JOSE DEL MONTE</h1>
            <p class="hero-description">Where nature meets adventure. Experience breathtaking mountains, pristine waterfalls, and authentic Filipino culture just 30 minutes from Metro Manila.</p>
            <div class="hero-buttons">
                <button class="btn-primary" onclick="window.location.href='sjdm-user/index.php'">
                    <span id="authButtonText">Explore Now</span>
                    <span class="material-icons-outlined">arrow_forward</span>
                </button>
                <button class="btn-secondary" onclick="document.getElementById('destinations').scrollIntoView({behavior:'smooth'})">
                    Learn More
                </button>
                
            </div>
        </div>
        <div class="scroll-indicator">
            <span>Scroll to explore</span>
            <span class="material-icons-outlined">keyboard_arrow_down</span>
        </div>
    </section>

    <!-- Destinations Section -->
    <section class="destinations" id="destinations">
        <div class="section-header">
            <div class="section-label">TOP ATTRACTIONS</div>
            <h2 class="section-title">Featured Destinations</h2>
            <p class="section-description">Discover the most stunning natural wonders and cultural landmarks that make San Jose del Monte a must-visit destination in Bulacan.</p>
        </div>
        <div class="destinations-grid">
            <div class="destination-card">
                <img src="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 400 450'><defs><linearGradient id='grad1' x1='0%25' y1='0%25' x2='0%25' y2='100%25'><stop offset='0%25' style='stop-color:%232c5f2d;stop-opacity:1'/><stop offset='100%25' style='stop-color:%23356641;stop-opacity:1'/></linearGradient></defs><rect fill='url(%23grad1)' width='400' height='450'/><path fill='%234a7c4e' d='M0 300 L100 250 L200 280 L300 220 L400 260 L400 450 L0 450 Z'/><circle fill='%23fff' opacity='0.1' cx='200' cy='150' r='80'/></svg>" alt="Mt. Balagbag">
                <div class="destination-info">
                    <div class="destination-rank">1ST PLACE</div>
                    <div class="destination-name">Mt. Balagbag</div>
                    <div class="destination-location">777 MASL â€¢ Beginner Friendly</div>
                </div>
            </div>
            <div class="destination-card">
                <img src="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 400 450'><defs><linearGradient id='grad2' x1='0%25' y1='0%25' x2='0%25' y2='100%25'><stop offset='0%25' style='stop-color:%233b82f6;stop-opacity:1'/><stop offset='100%25' style='stop-color:%232563eb;stop-opacity:1'/></linearGradient></defs><rect fill='url(%23grad2)' width='400' height='450'/><rect fill='%23fff' opacity='0.2' x='150' y='200' width='100' height='250'/><rect fill='%23fff' opacity='0.15' x='100' y='250' width='80' height='200'/></svg>" alt="Kaytitinga Falls">
                <div class="destination-info">
                    <div class="destination-rank">2ND PLACE</div>
                    <div class="destination-name">Kaytitinga Falls</div>
                    <div class="destination-location">3-Level Cascading Falls</div>
                </div>
            </div>
            <div class="destination-card">
                <img src="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 400 450'><defs><linearGradient id='grad3' x1='0%25' y1='0%25' x2='0%25' y2='100%25'><stop offset='0%25' style='stop-color:%23f59e0b;stop-opacity:1'/><stop offset='100%25' style='stop-color:%23d97706;stop-opacity:1'/></linearGradient></defs><rect fill='url(%23grad3)' width='400' height='450'/><path fill='%23fff' opacity='0.3' d='M200 100 L220 180 L300 180 L240 230 L260 310 L200 270 L140 310 L160 230 L100 180 L180 180 Z'/></svg>" alt="Grotto">
                <div class="destination-info">
                    <div class="destination-rank">3RD PLACE</div>
                    <div class="destination-name">Our Lady of Lourdes</div>
                    <div class="destination-location">Spiritual Sanctuary</div>
                </div>
            </div>
            <div class="destination-card">
                <img src="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 400 450'><defs><linearGradient id='grad4' x1='0%25' y1='0%25' x2='0%25' y2='100%25'><stop offset='0%25' style='stop-color:%2397bc62;stop-opacity:1'/><stop offset='100%25' style='stop-color:%236b8e4e;stop-opacity:1'/></linearGradient></defs><rect fill='url(%23grad4)' width='400' height='450'/><circle fill='%23fff' opacity='0.2' cx='200' cy='180' r='60'/><rect fill='%23fff' opacity='0.15' x='180' y='240' width='40' height='210'/></svg>" alt="Padre Pio">
                <div class="destination-info">
                    <div class="destination-rank">4TH PLACE</div>
                    <div class="destination-name">Padre Pio Mountain</div>
                    <div class="destination-location">24/7 Prayer Site</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features">
        <div class="section-header">
            <div class="section-label">WHY CHOOSE US</div>
            <h2 class="section-title">Your Perfect Travel Experience</h2>
            <p class="section-description">We provide everything you need for an unforgettable adventure in San Jose del Monte.</p>
        </div>
        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon">
                    <span class="material-icons-outlined">verified_user</span>
                </div>
                <h3 class="feature-title">Certified Guides</h3>
                <p class="feature-description">All our tour guides are licensed, experienced, and passionate about sharing the beauty of SJDM with visitors.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">
                    <span class="material-icons-outlined">schedule</span>
                </div>
                <h3 class="feature-title">Flexible Booking</h3>
                <p class="feature-description">Book your tours with ease and flexibility. Choose your preferred dates, guides, and destinations online.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">
                    <span class="material-icons-outlined">landscape</span>
                </div>
                <h3 class="feature-title">10+ Destinations</h3>
                <p class="feature-description">From mountains to waterfalls, spiritual sites to urban attractions - explore diverse experiences.</p>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section">
        <div class="cta-content">
            <h2 class="cta-title">Ready for Your Adventure?</h2>
            <p class="cta-description">Join hundreds of satisfied travelers who have discovered the beauty of San Jose del Monte. Book your tour today and create memories that last a lifetime.</p>
            <button class="btn-white" onclick="window.location.href='/coderistyarn/log-in/log-in.php'">
                Start Your Journey
                <span class="material-icons-outlined">explore</span>
            </button>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <div class="footer-content">
            <div class="footer-about">
                <h3>SJDM TOURS</h3>
                <p>Your trusted guide to exploring the natural wonders and cultural heritage of San Jose del Monte, Bulacan. We connect travelers with certified local guides for authentic experiences.</p>
                <div class="social-links">
                    <a href="#" class="social-link">
                        <span class="material-icons-outlined">facebook</span>
                    </a>
                    <a href="#" class="social-link">
                        <span class="material-icons-outlined">camera_alt</span>
                    </a>
                    <a href="#" class="social-link">
                        <span class="material-icons-outlined">public</span>
                    </a>
                </div>
            </div>
            <div class="footer-links">
                <h4>Quick Links</h4>
                <ul>
                    <li><a href="index.php">Home</a></li>
                    <li><a href="index.php#guides">Tour Guides</a></li>
                    <li><a href="index.php#spots">Tourist Spots</a></li>
                    <li><a href="index.php#booking">Book Now</a></li>
                </ul>
            </div>
            <div class="footer-links">
                <h4>Resources</h4>
                <ul>
                    <li><a href="index.php#tips">Travel Tips</a></li>
                    <li><a href="index.php#culture">Local Culture</a></li>
                    <li><a href="#">FAQs</a></li>
                    <li><a href="#">Contact Us</a></li>
                </ul>
            </div>
            <div class="footer-links">
                <h4>Contact</h4>
                <ul>
                    <li>San Jose del Monte</li>
                    <li>Bulacan, Philippines</li>
                    <li>info@sjdmtours.ph</li>
                    <li>+63 912 345 6789</li>
                </ul>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; 2024 SJDM Tours. All rights reserved. | Discover the Balcony of Metropolis</p>
        </div>
    </footer>

    <script src="script.js"></script>
   
</body>
</html>