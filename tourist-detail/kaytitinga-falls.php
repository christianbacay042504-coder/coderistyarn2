<?php
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../functions/tourist-detail-helpers.php';

// Get assigned guide for this tourist spot
$assignedGuide = initializeAssignedGuide('Kaytitinga Falls');

if (isset($_GET['action']) && $_GET['action'] === 'book') {
    $destination = $_GET['destination'] ?? 'Kaytitinga Falls';
    if (!isLoggedIn()) { header('Location: ../log-in.php'); exit(); }
    header('Location: ../User/user-book.php?destination=' . urlencode($destination)); exit();
}
?>
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
        body { font-family: 'Inter', sans-serif; background: #fafafa; color: var(--text-dark); margin: 0; padding: 0; }
        .container { max-width: 1200px; margin: 0 auto; padding: 0 20px; }
        .page-header { background: white; padding: 20px 0; border-bottom: 1px solid var(--border); }
        .header-content { display: flex; align-items: center; justify-content: space-between; }
        .back-navigation { display: flex; align-items: center; gap: 20px; }
        .btn-back { background: transparent; border: none; color: var(--primary); font-size: 1rem; font-weight: 600; cursor: pointer; padding: 10px 20px; border-radius: 8px; transition: all 0.3s ease; display: flex; align-items: center; gap: 8px; }
        .btn-back:hover { background: rgba(44,95,45,0.1); }
        .page-title { font-size: 1.8rem; font-weight: 700; color: var(--primary); margin: 0; }
        .spot-hero { height: 400px; background: linear-gradient(rgba(0,0,0,0.55), rgba(0,0,0,0.35)), url('https://images.unsplash.com/photo-1501854140801-50d01698950b?q=80&w=2070&auto=format&fit=crop'); background-size: cover; background-position: center; display: flex; align-items: center; justify-content: center; text-align: center; color: white; margin: 30px auto 40px; border-radius: 50px; max-width: 1500px; width: calc(100% - 40px); }
        .hero-content { max-width: 800px; padding: 0 20px; }
        .hero-title { font-size: 3rem; font-weight: 700; margin-bottom: 15px; text-shadow: 2px 2px 4px rgba(0,0,0,0.3); }
        .hero-subtitle { font-size: 1.2rem; opacity: 0.9; margin-bottom: 20px; }
        .spot-content { padding: 0 0 60px; }
        .spot-details { display: grid; grid-template-columns: 2fr 1fr; gap: 40px; margin-bottom: 60px; }
        .section-title { font-size: 1.8rem; font-weight: 700; color: var(--primary); margin-bottom: 20px; padding-bottom: 15px; border-bottom: 2px solid var(--border); }
        .section-subtitle { font-size: 1.3rem; font-weight: 600; color: var(--text-dark); margin: 30px 0 15px; }
        .description { font-size: 1.05rem; line-height: 1.8; color: var(--text-light); margin-bottom: 25px; }
        .features-list { list-style: none; padding: 0; margin: 25px 0; }
        .features-list li { padding: 12px 0 12px 35px; position: relative; font-size: 1rem; line-height: 1.6; color: var(--text-light); }
        .features-list li:before { content: '✓'; position: absolute; left: 0; color: var(--primary); font-weight: bold; font-size: 1.2rem; }
        .spot-info-card { background: white; border-radius: 15px; padding: 30px; box-shadow: var(--shadow); border: 1px solid var(--border); position: sticky; top: 20px; }
        .info-grid { display: grid; grid-template-columns: 1fr; gap: 20px; margin: 25px 0; }
        .info-item { display: flex; align-items: center; gap: 15px; padding: 15px; background: var(--secondary); border-radius: 10px; transition: transform 0.3s ease; }
        .info-item:hover { transform: translateY(-2px); box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
        .info-item .material-icons-outlined { color: var(--primary); font-size: 26px; }
        .info-text { flex: 1; }
        .info-label { display: block; font-size: 0.9rem; color: var(--text-light); margin-bottom: 4px; }
        .info-value { display: block; font-size: 1.1rem; font-weight: 600; color: var(--text-dark); }
        .photo-gallery { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 25px; margin: 40px 0; }
        .gallery-item { border-radius: 12px; overflow: hidden; box-shadow: var(--shadow); transition: transform 0.3s ease; height: 220px; }
        .gallery-item:hover { transform: translateY(-5px); }
        .gallery-item img { width: 100%; height: 100%; object-fit: cover; transition: transform 0.5s ease; }
        .gallery-item:hover img { transform: scale(1.05); }
        .cta-section { background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%); color: white; padding: 50px 40px; border-radius: 15px; text-align: center; margin: 50px 0; box-shadow: var(--shadow); }
        .cta-title { font-size: 2rem; font-weight: 700; margin-bottom: 20px; }
        .cta-text { font-size: 1.1rem; opacity: 0.9; max-width: 700px; margin: 0 auto 30px; line-height: 1.6; }
        .cta-buttons { display: flex; gap: 20px; justify-content: center; flex-wrap: wrap; }
        .btn-primary { background: white; color: var(--primary); border: none; padding: 16px 32px; font-size: 1.1rem; font-weight: 600; border-radius: 10px; cursor: pointer; display: flex; align-items: center; gap: 10px; transition: all 0.3s ease; min-width: 180px; justify-content: center; }
        .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(0,0,0,0.15); }
        .btn-secondary { background: transparent; color: white; border: 2px solid white; padding: 14px 32px; font-size: 1.1rem; font-weight: 600; border-radius: 10px; cursor: pointer; display: flex; align-items: center; gap: 10px; transition: all 0.3s ease; min-width: 180px; justify-content: center; }
        .btn-secondary:hover { background: rgba(255,255,255,0.1); transform: translateY(-2px); }
        .footer { background: white; padding: 30px 0; text-align: center; border-top: 1px solid var(--border); margin-top: 60px; }
        .footer-text { color: var(--text-light); font-size: 0.9rem; margin: 5px 0; }
        .tour-guide-section { margin: 30px 0; padding: 25px; background: linear-gradient(135deg, rgba(44,95,45,0.05), rgba(76,140,76,0.1)); border-radius: 15px; border: 1px solid rgba(44,95,45,0.2); }
        .tour-guide-header { display: flex; align-items: center; gap: 15px; margin-bottom: 20px; }
        .tour-guide-avatar { width: 60px; height: 60px; border-radius: 50%; background: var(--primary); color: white; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; font-weight: 700; flex-shrink: 0; border: 3px solid white; box-shadow: 0 4px 12px rgba(44,95,45,0.3); }
        .tour-guide-avatar img { width: 100%; height: 100%; border-radius: 50%; object-fit: cover; }
        .tour-guide-info h4 { margin: 0 0 5px; font-size: 1.2rem; font-weight: 700; color: var(--primary); }
        .tour-guide-specialty { font-size: 0.9rem; color: var(--text-light); margin-bottom: 5px; }
        .tour-guide-rating { font-size: 0.85rem; color: #ff9800; font-weight: 600; }
        .tour-guide-details { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-top: 20px; }
        .tour-guide-contact-item { display: flex; align-items: center; gap: 10px; padding: 12px; background: white; border-radius: 10px; font-size: 0.9rem; color: var(--text-dark); border: 1px solid rgba(44,95,45,0.1); }
        .tour-guide-contact-item .material-icons-outlined { color: var(--primary); font-size: 20px; flex-shrink: 0; }
        .tour-guide-bio { margin-top: 20px; padding: 15px; background: white; border-radius: 10px; font-size: 0.9rem; line-height: 1.6; color: var(--text-light); border: 1px solid rgba(44,95,45,0.1); }
        .no-guide-assigned { text-align: center; padding: 20px; color: var(--text-light); font-style: italic; background: rgba(255,255,255,0.7); border-radius: 10px; border: 1px solid var(--border); }
        @media (max-width: 992px) { .spot-details { grid-template-columns: 1fr; } .spot-info-card { position: static; } .hero-title { font-size: 2.5rem; } }
        @media (max-width: 768px) { .hero-title { font-size: 2rem; } .photo-gallery { grid-template-columns: 1fr; } .cta-buttons { flex-direction: column; align-items: center; } .btn-primary, .btn-secondary { width: 100%; max-width: 300px; } .header-content { flex-direction: column; gap: 15px; text-align: center; } }
    </style>
</head>
<body>
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

    <section class="spot-hero">
        <div class="hero-content">
            <h1 class="hero-title">Kaytitinga Falls</h1>
            <p class="hero-subtitle">SJDM's Hidden Cascading Gem</p>
        </div>
    </section>

    <main class="container">
        <div class="spot-content">
            <div class="spot-details">
                <!-- Left Column -->
                <div>
                    <h2 class="section-title">About Kaytitinga Falls</h2>
                    <p class="description">
                        Kaytitinga Falls is one of the most beautiful natural attractions in San Jose del Monte, 
                        Bulacan. Nestled within lush tropical forest, this stunning waterfall features multiple 
                        cascading tiers of crystal-clear water that flow into natural pools perfect for swimming.
                    </p>
                    <p class="description">
                        The trail leading to Kaytitinga Falls winds through scenic forest paths, offering 
                        trekkers an opportunity to spot local wildlife and enjoy the rich biodiversity of the area. 
                        The falls are especially impressive during and after the rainy season when water flow is at its peak.
                    </p>

                    <h3 class="section-subtitle">Falls Highlights</h3>
                    <ul class="features-list">
                        <li><strong>Multi-tier Cascade:</strong> Several stunning waterfall tiers to explore</li>
                        <li><strong>Natural Swimming Pools:</strong> Crystal-clear pools at the base ideal for swimming</li>
                        <li><strong>Scenic Forest Trail:</strong> Beautiful 1–2 hour trek through native forest</li>
                        <li><strong>Wildlife Sightings:</strong> Rich bird life and native flora along the way</li>
                        <li><strong>Photography Paradise:</strong> Dramatic cascades and jungle backdrop</li>
                        <li><strong>Cool Microclimate:</strong> Refreshingly cool atmosphere even on hot days</li>
                    </ul>

                    <h3 class="section-subtitle">Gallery</h3>
                    <div class="photo-gallery">
                        <div class="gallery-item">
                            <img src="https://images.unsplash.com/photo-1501854140801-50d01698950b?q=80&w=2070&auto=format&fit=crop" alt="Waterfall">
                        </div>
                        <div class="gallery-item">
                            <img src="https://images.unsplash.com/photo-1432405972618-c60b0225b8f9?q=80&w=2070&auto=format&fit=crop" alt="Forest Trail">
                        </div>
                        <div class="gallery-item">
                            <img src="https://images.unsplash.com/photo-1546182990-dffeafbe841d?q=80&w=2070&auto=format&fit=crop" alt="Natural Pool">
                        </div>
                    </div>
                </div>

                <!-- Right Column: Info Card -->
                <div class="spot-info-card">
                    <h3 class="section-subtitle" style="margin-top: 0;">Visit Information</h3>

                    <div class="info-grid">
                        <div class="info-item">
                            <span class="material-icons-outlined">schedule</span>
                            <div class="info-text">
                                <span class="info-label">Opening Hours</span>
                                <span class="info-value">6:00 AM - 5:00 PM</span>
                            </div>
                        </div>
                        <div class="info-item">
                            <span class="material-icons-outlined">payments</span>
                            <div class="info-text">
                                <span class="info-label">Entrance Fee</span>
                                <span class="info-value">₱30 per person</span>
                            </div>
                        </div>
                        <div class="info-item">
                            <span class="material-icons-outlined">terrain</span>
                            <div class="info-text">
                                <span class="info-label">Difficulty</span>
                                <span class="info-value">Easy to Moderate</span>
                            </div>
                        </div>
                        <div class="info-item">
                            <span class="material-icons-outlined">groups</span>
                            <div class="info-text">
                                <span class="info-label">Best For</span>
                                <span class="info-value">Families, Nature Lovers</span>
                            </div>
                        </div>
                    </div>

                    <h3 class="section-subtitle">What to Bring</h3>
                    <ul style="margin: 15px 0; padding-left: 20px; color: var(--text-light); font-size: 0.95rem;">
                        <li>Water shoes or sandals</li>
                        <li>Change of clothes for swimming</li>
                        <li>Waterproof bag for valuables</li>
                        <li>Insect repellent</li>
                        <li>Enough water and snacks</li>
                        <li>Waterproof camera</li>
                    </ul>

                    <!-- Tour Guide Section -->
                    <div class="tour-guide-section">
                        <h3 class="section-subtitle" style="margin-top: 0; color: var(--primary);">
                            <span class="material-icons-outlined" style="vertical-align: middle; margin-right: 8px;">person</span>
                            Assigned Tour Guide
                        </h3>

                        <?php if ($assignedGuide): ?>
                            <div class="tour-guide-header">
                                <div class="tour-guide-avatar">
                                    <?php if ($assignedGuide['photo_url']): ?>
                                        <img src="<?php echo htmlspecialchars($assignedGuide['photo_url']); ?>" alt="<?php echo htmlspecialchars($assignedGuide['name']); ?>">
                                    <?php else: ?>
                                        <?php echo strtoupper(substr($assignedGuide['name'], 0, 1)); ?>
                                    <?php endif; ?>
                                </div>
                                <div class="tour-guide-info">
                                    <h4><?php echo htmlspecialchars($assignedGuide['name']); ?></h4>
                                    <div class="tour-guide-specialty"><?php echo formatGuideSpecialty($assignedGuide['specialty']); ?></div>
                                    <div class="tour-guide-rating">
                                        <?php echo formatGuideRating($assignedGuide['rating'], $assignedGuide['review_count']); ?>
                                    </div>
                                </div>
                            </div>

                            <?php if ($assignedGuide['bio']): ?>
                                <div class="tour-guide-bio">
                                    <strong>About your guide:</strong> <?php echo htmlspecialchars($assignedGuide['bio']); ?>
                                </div>
                            <?php endif; ?>

                            <div class="tour-guide-details">
                                <?php if ($assignedGuide['contact_number']): ?>
                                <div class="tour-guide-contact-item">
                                    <span class="material-icons-outlined">phone</span>
                                    <span><?php echo htmlspecialchars($assignedGuide['contact_number']); ?></span>
                                </div>
                                <?php endif; ?>
                                <?php if ($assignedGuide['email']): ?>
                                <div class="tour-guide-contact-item">
                                    <span class="material-icons-outlined">email</span>
                                    <span><?php echo htmlspecialchars($assignedGuide['email']); ?></span>
                                </div>
                                <?php endif; ?>
                            </div>
                        <?php else: ?>
                            <div class="no-guide-assigned">
                                <span class="material-icons-outlined" style="font-size: 2rem; display: block; margin-bottom: 10px;">person_off</span>
                                No tour guide assigned yet for this destination.<br>
                                <small>Contact us for guided tour arrangements.</small>
                            </div>
                        <?php endif; ?>
                    </div>

                    <button class="btn-primary" onclick="checkAuth('Kaytitinga Falls'<?php if ($assignedGuide): ?>, '<?php echo $assignedGuide['id']; ?>'<?php endif; ?>)" style="width: 100%; margin-top: 25px;">
                        <span class="material-icons-outlined">water</span>
                        Book a Visit
                    </button>
                </div>
            </div>

            <div class="cta-section">
                <h2 class="cta-title">Discover Kaytitinga Falls!</h2>
                <p class="cta-text">
                    Escape to the cool, refreshing waters of Kaytitinga Falls. Whether you're looking to swim, 
                    trek, or simply enjoy nature's beauty, this hidden gem awaits you.
                </p>
                <div class="cta-buttons">
                    <button class="btn-primary" onclick="checkAuth('Kaytitinga Falls'<?php if ($assignedGuide): ?>, '<?php echo $assignedGuide['id']; ?>'<?php endif; ?>)">Book a Visit</button>
                    <button class="btn-secondary" onclick="location.href='/coderistyarn2/User/user-guides-page.php'">Book a Tour Guide</button>
                    <button class="btn-secondary" onclick="location.href='/coderistyarn2/User/user-tourist-spots.php'">
                        <span class="material-icons-outlined">place</span>
                        View All Destinations
                    </button>
                </div>
            </div>
        </div>
    </main>

    <footer class="footer">
        <div class="container">
            <p class="footer-text"> 2024 San Jose del Monte Tourism Office. All rights reserved.</p>
            <p class="footer-text">Promoting sustainable tourism and community development.</p>
        </div>
    </footer>

    <script>
        function checkAuth(destination, guideId = null) {
            const isLoggedIn = <?php echo isLoggedIn() ? 'true' : 'false'; ?>;
            if (!isLoggedIn) { window.location.href = '../log-in.php'; return; }
            let url = '../User/user-book.php?destination=' + encodeURIComponent(destination);
            if (guideId) {
                url += '&guide=' + encodeURIComponent(guideId);
            }
            window.location.href = url;
        }
    </script>
</body>
</html>