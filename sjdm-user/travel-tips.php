<?php
// Include database connection
require_once '../config/database.php';

// Get current user data (optional - for logged in users)
$currentUser = [];
$conn = getDatabaseConnection();
if ($conn && isset($_SESSION['user_id'])) {
    $stmt = $conn->prepare("SELECT first_name, last_name, email FROM users WHERE id = ?");
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $currentUser = [
            'name' => $user['first_name'] . ' ' . $user['last_name'],
            'email' => $user['email']
        ];
    }
    closeDatabaseConnection($conn);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Travel Tips - San Jose del Monte Bulacan</title>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
    <style>
        
 /* ===== USER PROFILE DROPDOWN ===== */
        .user-profile-dropdown {
            position: relative;
            display: inline-block;
            z-index: 1000;
        }

        .profile-trigger {
            display: flex;
            align-items: center;
            gap: 8px;
            background: none;
            border: 1px solid rgba(251, 255, 253, 1);
            cursor: pointer;
            color: #333;
            font-weight: 600;
            padding: 6px 12px;
            border-radius: 20px;
            transition: background 0.2s;
            box-shadow: 5px 10px 10px rgba(0, 0, 0, 0.1);
        }

        .profile-trigger:hover {
            background: #f0f0f0;
        }

        .profile-avatar,
        .profile-avatar-large {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: #2c5f2d;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 14px;
            flex-shrink: 0;
        }

        .profile-avatar-large {
            width: 56px;
            height: 56px;
            font-size: 20px;
            margin: 0 auto 12px;
        }

        .profile-name {
            display: none;
        }

        .dropdown-menu {
            position: absolute;
            top: 100%;
            right: 0;
            margin-top: 8px;
            width: 240px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            overflow: hidden;
            z-index: 1001;
            opacity: 0;
            visibility: hidden;
            transform: translateY(-10px);
            transition: all 0.2s ease;
        }

        .dropdown-menu.show {
            opacity: 1 !important;
            visibility: visible !important;
            transform: translateY(0) !important;
        }

        .dropdown-header {
            padding: 16px;
            background: #f9f9f9;
            text-align: center;
            border-bottom: 1px solid #eee;
        }

        .dropdown-header h4 {
            margin: 8px 0 4px;
            font-size: 16px;
            color: #333;
        }

        .dropdown-header p {
            font-size: 13px;
            color: #777;
            margin: 0;
        }

        .dropdown-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 16px;
            text-decoration: none;
            color: #444;
            transition: background 0.2s;
        }

        .dropdown-item:hover {
            background: #f5f5f5;
        }

        .dropdown-item .material-icons-outlined {
            font-size: 20px;
            color: #555;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .profile-name {
                display: inline-block;
                font-size: 14px;
            }

            .dropdown-menu {
                width: 280px;
            }
        }
        /* Additional styles for accommodation suggestions */
        .accommodation-section {
            margin: 40px 0;
            padding: 30px;
            background: #ffffff; box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            border-radius: 20px;
            color: var(--text-primary);
        }

        .accommodation-section h2 {
            color: var(--text-primary);
            margin-bottom: 10px;
            font-size: 28px;
        }

        .accommodation-section .section-subtitle {
            color: var(--text-secondary);
            margin-bottom: 30px;
            font-size: 16px;
        }

        .spot-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }

        .spot-card {
            background: #ffffff; box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            
            border-radius: 15px;
            padding: 25px;
            border: 1px solid var(--border);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .spot-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.2);
        }

        .spot-card h3 {
            color: var(--text-primary);
            margin-bottom: 15px;
            font-size: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
            line-height: 1.3;
        }

        .spot-card h3 .material-icons-outlined {
            font-size: 24px;
            flex-shrink: 0;
            margin-top: 2px;
        }

        .area-badge {
            display: inline-block;
            background: var(--primary-light);
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            margin-bottom: 15px;
            color: var(--primary);
        }

        .suggestion-category {
            margin-bottom: 15px;
        }

        .suggestion-category h4 {
            color: var(--text-primary);
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .suggestion-list {
            list-style: none;
            padding-left: 0;
        }

        .suggestion-list li {
            color: var(--text-secondary);
            margin-bottom: 5px;
            padding-left: 25px;
            position: relative;
        }

        .suggestion-list li:before {
            content: "✓";
            position: absolute;
            left: 0;
            color: #4ade80;
        }

        .transport-tip {
            background: var(--primary-light);
            padding: 12px 15px;
            border-radius: 10px;
            margin-top: 15px;
            font-size: 14px;
            color: var(--text-primary);
            border-left: 4px solid var(--primary);
        }

        .general-tips-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin: 40px 0;
        }

        .general-tip-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            transition: transform 0.3s ease;
        }

        .general-tip-card:hover {
            transform: translateY(-5px);
        }

        .general-tip-card h3 {
            color: #25631cff;
            margin-bottom: 15px;
            font-size: 18px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .general-tip-card ul {
            list-style: none;
            padding-left: 0;
        }

        .general-tip-card ul li {
            margin-bottom: 8px;
            padding-left: 25px;
            position: relative;
            color: #6b7280;
        }

        .general-tip-card ul li:before {
            content: "•";
            position: absolute;
            left: 10px;
            color: #3bc528ff;
        }

        .page-intro {
            background: #ffffffff;
            padding: 40px;
            border-radius: 20px;
            margin-bottom: 40px;
            text-align: center;
            border: 1px solid var(--border);
        }

        .page-intro h1 {
            color: var(--primary);
            margin-bottom: 15px;
        }

        .page-intro p {
            color: var(--text-secondary);
            font-size: 18px;
            max-width: 800px;
            margin: 0 auto;
        }

        .search-bar input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(44, 95, 45, 0.1);
        }

        /* Full-width layout styles */
        .main-content.full-width {
            margin-left: 0;
            max-width: 100%;
        }

        .main-content.full-width .main-header {
            padding: 30px 40px;
            background: white;
            border-bottom: 1px solid var(--border);
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 40px;
        }

        .header-left {
            display: flex;
            align-items: center;
            gap: 30px;
        }

        .header-right {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .header-nav {
            display: flex;
            align-items: center;
            gap: 8px;
            background: var(--gray-50);
            padding: 4px;
            border-radius: 12px;
        }

        .nav-link {
            display: flex;
            align-items: center;
            gap: 6px;
            padding: 8px 16px;
            text-decoration: none;
            color: var(--text-secondary);
            font-weight: 500;
            font-size: 14px;
            border-radius: 8px;
            transition: all 0.2s ease;
        }

        .nav-link:hover {
            background: white;
            color: var(--primary);
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .nav-link.active {
            background: var(--primary);
            color: white;
        }

        .nav-link .material-icons-outlined {
            font-size: 18px;
        }

        .btn-signin {
            background: #2563eb;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 500;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .btn-signin:hover {
            background: #1d4ed8;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
        }

        .main-content.full-width .content-area {
            padding: 40px;
            max-width: 1400px;
            margin: 0 auto;
        }

        @media (max-width: 768px) {
            .spot-cards {
                grid-template-columns: 1fr;
            }
            
            .general-tips-grid {
                grid-template-columns: 1fr;
            }
            
            .accommodation-section {
                padding: 20px;
            }
            
            .page-intro {
                padding: 30px 20px;
            }
            
            .spot-card h3 {
                font-size: 18px;
            }
            
            .suggestion-category h4 {
                font-size: 12px;
            }
            
            .main-content.full-width .main-header {
                padding: 20px;
                flex-direction: column;
                gap: 20px;
                align-items: stretch;
            }
            
            .header-left {
                flex-direction: column;
                gap: 15px;
                align-items: stretch;
            }
            
            .header-right {
                justify-content: center;
            }
            
            .header-nav {
                flex-wrap: wrap;
                justify-content: center;
                gap: 4px;
                padding: 6px;
            }
            
            .nav-link {
                padding: 6px 12px;
                font-size: 12px;
                gap: 4px;
            }
            
            .nav-link .material-icons-outlined {
                font-size: 16px;
            }
            
            .main-content.full-width .content-area {
                padding: 20px;
            }
        }
    </style>
</head>
<body class="travel-tips-page">
    <!-- MAIN CONTENT -->
    <main class="main-content full-width">
        <header class="main-header">
            <div class="header-left">
                <h1>Travel Tips</h1>
                <div class="search-bar">
                    <span class="material-icons-outlined">search</span>
                    <input type="text" placeholder="Search travel tips...">
                </div>
            </div>
            <div class="header-right">
                <nav class="header-nav">
                    <a href="../index.php" class="nav-link active">
                        <span class="material-icons-outlined">home</span>
                        <span>Home</span>
                    </a>
                </nav>
            </div>
            <div class="header-right">
                <nav class="header-nav">
                    <a href="index.php" class="nav-link">
                        <span class="material-icons-outlined">dashboard</span>
                        <span>Dashboard</span>
                    </a>
                    <a href="user-guides.php" class="nav-link">
                        <span class="material-icons-outlined">people</span>
                        <span>Tour Guides</span>
                    </a>
                    <a href="book.php" class="nav-link">
                        <span class="material-icons-outlined">event</span>
                        <span>Book Now</span>
                    </a>
                    <a href="tourist-spots.php" class="nav-link">
                        <span class="material-icons-outlined">place</span>
                        <span>Tourist Spots</span>
                    </a>
                    <a href="local-culture.php" class="nav-link">
                        <span class="material-icons-outlined">theater_comedy</span>
                        <span>Local Culture</span>
                    </a>
                    <a href="javascript:void(0)" class="nav-link active">
                        <span class="material-icons-outlined">tips_and_updates</span>
                        <span>Travel Tips</span>
                    </a>
                </nav>
                <div class="header-actions">
                    <button class="btn-signin" onclick="window.location.href='../log-in.php'">Sign in/register</button>
                </div>
            </div>
        </header>

        <div class="content-area">
            <!-- Page Intro -->
            <div class="page-intro">
                <h1>Travel Tips & Recommendations</h1>
                <p>Find accommodation, restaurants, and practical advice for your SJDM adventure</p>
            </div>

            <!-- Accommodation & Restaurant Suggestions Section -->
            <section class="accommodation-section">
                <h2>Where to Stay & Eat Near Tourist Spots</h2>
                <p class="section-subtitle">Find the best hotels, restaurants, and accommodations near popular tourist destinations</p>
                
                <div class="spot-cards" id="accommodationCards">
                    <!-- Cards will be populated by JavaScript -->
                    <div class="spot-card" data-spot="City Oval (People's Park)">
                        <div class="area-badge">SJDM Center</div>
                        <h3><span class="material-icons-outlined">park</span> City Oval (People's Park)</h3>
                        
                        <div class="suggestion-category">
                            <h4><span class="material-icons-outlined">hotel</span> Hotels</h4>
                            <ul class="suggestion-list">
                                <li>Hotel Sogo</li>
                                <li>Hotel Turista</li>
                            </ul>
                        </div>
                        
                        <div class="suggestion-category">
                            <h4><span class="material-icons-outlined">restaurant</span> Restaurants</h4>
                            <ul class="suggestion-list">
                                <li>Escobar's</li>
                                <li>Roadside Dampa</li>
                            </ul>
                        </div>
                        
                        <div class="transport-tip">
                            <strong>Transport Tip:</strong> Very accessible via jeepney and tricycle. Parking available.
                        </div>
                    </div>
                    
                    <div class="spot-card" data-spot="Our Lady of Lourdes Parish / Padre Pio Parish">
                        <div class="area-badge">Tungkong Mangga</div>
                        <h3><span class="material-icons-outlined">church</span> Our Lady of Lourdes Parish / Padre Pio Parish</h3>
                        
                        <div class="suggestion-category">
                            <h4><span class="material-icons-outlined">hotel</span> Hotels</h4>
                            <ul class="suggestion-list">
                                <li>Hotel Sogo</li>
                                <li>Staycation Amaia</li>
                            </ul>
                        </div>
                        
                        <div class="suggestion-category">
                            <h4><span class="material-icons-outlined">restaurant</span> Restaurants</h4>
                            <ul class="suggestion-list">
                                <li>Max's SM SJDM</li>
                                <li>Escobar's</li>
                            </ul>
                        </div>
                        
                        <div class="transport-tip">
                            <strong>Transport Tip:</strong> Near major highways. Easy access from city center.
                        </div>
                    </div>
                    
                    <div class="spot-card" data-spot="The Rising Heart Monument">
                        <div class="area-badge">Paradise 3 area</div>
                        <h3><span class="material-icons-outlined">landscape</span> The Rising Heart Monument</h3>
                        
                        <div class="suggestion-category">
                            <h4><span class="material-icons-outlined">hotel</span> Hotels</h4>
                            <ul class="suggestion-list">
                                <li>Local lodges in Paradise 3 area</li>
                            </ul>
                        </div>
                        
                        <div class="suggestion-category">
                            <h4><span class="material-icons-outlined">restaurant</span> Restaurants</h4>
                            <ul class="suggestion-list">
                                <li>Los Arcos De Hermano (close resort)</li>
                                <li>Escobar's (short drive)</li>
                            </ul>
                        </div>
                        
                        <div class="transport-tip">
                            <strong>Transport Tip:</strong> Best visited by private vehicle. Photo spot along highway.
                        </div>
                    </div>
                    
                    <div class="spot-card" data-spot="Abes Farm / Paradise Hill Farm">
                        <div class="area-badge">Paradise / Rural SJDM</div>
                        <h3><span class="material-icons-outlined">agriculture</span> Abes Farm / Paradise Hill Farm</h3>
                        
                        <div class="suggestion-category">
                            <h4><span class="material-icons-outlined">hotel</span> Hotels</h4>
                            <ul class="suggestion-list">
                                <li>Los Arcos</li>
                                <li>Pacific Waves Resort</li>
                            </ul>
                        </div>
                        
                        <div class="suggestion-category">
                            <h4><span class="material-icons-outlined">restaurant</span> Restaurants</h4>
                            <ul class="suggestion-list">
                                <li>Farm-to-table restaurants in resort areas</li>
                            </ul>
                        </div>
                        
                        <div class="transport-tip">
                            <strong>Transport Tip:</strong> Requires private transportation. Rural roads may be narrow.
                        </div>
                    </div>
                    
                    <div class="spot-card" data-spot="Waterfalls">
                        <div class="area-badge">Barangays San Isidro / Sto. Cristo</div>
                        <h3><span class="material-icons-outlined">waterfall</span> Waterfalls (Burong, Kaytitinga, Otso-Otso, Tungtong Falls)</h3>
                        
                        <div class="suggestion-category">
                            <h4><span class="material-icons-outlined">hotel</span> Hotels</h4>
                            <ul class="suggestion-list">
                                <li>Hotel Sogo</li>
                                <li>Central SJDM accommodations</li>
                            </ul>
                        </div>
                        
                        <div class="suggestion-category">
                            <h4><span class="material-icons-outlined">restaurant</span> Restaurants</h4>
                            <ul class="suggestion-list">
                                <li>Escobar's</li>
                                <li>Local carinderias</li>
                            </ul>
                        </div>
                        
                        <div class="transport-tip">
                            <strong>Transport Tip:</strong> Requires local guides and transportation. Start early in the morning.
                        </div>
                    </div>
                </div>
            </section>

            <!-- General Travel Tips Section -->
            <h2 class="section-title">General Travel Tips for SJDM</h2>
            <div class="general-tips-grid">
                <?php
                // Fetch travel tips data from database
                $conn = getDatabaseConnection();
                if ($conn) {
                    $query = "SELECT * FROM travel_tips WHERE is_active = 'yes' ORDER BY display_order";
                    $result = $conn->query($query);
                    
                    if ($result && $result->num_rows > 0) {
                        while ($tip = $result->fetch_assoc()) {
                            echo '<div class="general-tip-card" data-category="' . htmlspecialchars($tip['category']) . '">';
                            echo '<h3><span class="material-icons-outlined">' . htmlspecialchars($tip['icon']) . '</span> ' . htmlspecialchars($tip['title']) . '</h3>';
                            
                            // Convert description from newlines to list items
                            $descriptionLines = explode("\n", $tip['description']);
                            echo '<ul>';
                            foreach ($descriptionLines as $line) {
                                $trimmedLine = trim($line);
                                if (!empty($trimmedLine)) {
                                    echo '<li>' . htmlspecialchars($trimmedLine) . '</li>';
                                }
                            }
                            echo '</ul>';
                            echo '</div>';
                        }
                    } else {
                        echo '<div class="no-results" style="grid-column: 1 / -1; text-align: center; padding: 60px 20px;">';
                        echo '<span class="material-icons-outlined" style="font-size: 48px; color: #9ca3af;">tips_and_updates</span>';
                        echo '<h3 style="color: #6b7280; margin-top: 16px;">No travel tips found</h3>';
                        echo '<p style="color: #9ca3af;">Please check back later for travel tips.</p>';
                        echo '</div>';
                    }
                    closeDatabaseConnection($conn);
                } else {
                    echo '<div class="error-message" style="grid-column: 1 / -1; text-align: center; padding: 60px 20px;">';
                    echo '<span class="material-icons-outlined" style="font-size: 48px; color: #ef4444;">error</span>';
                    echo '<h3 style="color: #ef4444; margin-top: 16px;">Database Connection Error</h3>';
                    echo '<p style="color: #6b7280;">Unable to load travel tips. Please try again later.</p>';
                    echo '</div>';
                }
                ?>
            </div>
        </div>
    </main>

    <script src="script.js"></script>
    <script>
        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function () {
            // Profile dropdown functionality removed
        });
        
        // TRAVEL TIPS PAGE FUNCTIONALITY
        // ========================================

        // Pass current user data to JavaScript
        <?php if (isset($currentUser)): ?>
        const currentUser = <?php echo json_encode($currentUser); ?>;
        localStorage.setItem('currentUser', JSON.stringify(currentUser));
        <?php endif; ?>

        // Initialize travel tips search functionality
        function initTravelTipsSearch() {
            const searchInput = document.querySelector('.search-bar input');
            if (searchInput) {
                searchInput.addEventListener('input', function(e) {
                    const searchTerm = e.target.value.toLowerCase();
                    filterTravelTips(searchTerm);
                });
            }
        }

        // Filter travel tips based on search
        function filterTravelTips(searchTerm) {
            const cards = document.querySelectorAll('.general-tip-card, .spot-card');
            
            cards.forEach(card => {
                const text = card.textContent.toLowerCase();
                if (text.includes(searchTerm) || searchTerm === '') {
                    card.style.display = 'block';
                    card.style.animation = 'fadeInUp 0.5s ease';
                } else {
                    card.style.display = 'none';
                }
            });
        }

        // Initialize travel tips page
        function initTravelTipsPage() {
            initTravelTipsSearch();
            console.log('Travel tips page initialized');
        }

        // Add fadeInUp animation for cards
        function addTravelTipsAnimations() {
            const style = document.createElement('style');
            style.textContent = `
                @keyframes fadeInUp {
                    from {
                        opacity: 0;
                        transform: translateY(20px);
                    }
                    to {
                        opacity: 1;
                        transform: translateY(0);
                    }
                }
                
                .spot-card,
                .general-tip-card {
                    animation: fadeInUp 0.5s ease;
                }
            `;
            document.head.appendChild(style);
        }

    </script>
</body>
</html>