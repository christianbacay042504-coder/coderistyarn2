<?php
// Include database connection and authentication
require_once '../config/database.php';
require_once '../config/auth.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../log-in/log-in.php');
    exit();
}

// Get current user data
$conn = getDatabaseConnection();
if ($conn) {
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
        }
    </style>
</head>
<body class="travel-tips-page">
    <!-- SIDEBAR -->
    <aside class="sidebar">
        <div class="sidebar-logo">
            <h1>SJDM Tours</h1>
            <p>Explore San Jose del Monte</p>
        </div>

        <nav class="sidebar-nav">
            <a class="nav-item" href="index.php">
                <span class="material-icons-outlined">home</span>
                <span>Home</span>
            </a>
            <a class="nav-item" href="user-guides.php">
                <span class="material-icons-outlined">people</span>
                <span>Tour Guides</span>
            </a>
            <a class="nav-item" href="book.php">
                <span class="material-icons-outlined">event</span>
                <span>Book Now</span>
            </a>
            <a class="nav-item" href="tourist-spots.php">
                <span class="material-icons-outlined">place</span>
                <span>Tourist Spots</span>
            </a>
            <a class="nav-item" href="local-culture.php">
                <span class="material-icons-outlined">theater_comedy</span>
                <span>Local Culture</span>
            </a>
            <a class="nav-item active" href="javascript:void(0)">
                <span class="material-icons-outlined">tips_and_updates</span>
                <span>Travel Tips</span>
            </a>
        </nav>
    </aside>

    <!-- MAIN CONTENT -->
    <main class="main-content">
        <header class="main-header">
            <h1>Travel Tips</h1>
            <div class="search-bar">
                <span class="material-icons-outlined">search</span>
                <input type="text" placeholder="Search travel tips, hotels, or restaurants...">
            </div>
            <div class="header-actions">
                <button class="icon-button">
                    <span class="material-icons-outlined">notifications_none</span>
                    <span class="notification-badge" style="display: none;">0</span>
                </button>
                <div class="profile-dropdown">
                    <button class="profile-button" id="profileButton">
                        <div class="profile-avatar"><?php echo isset($currentUser) ? substr($currentUser['name'], 0, 1) : 'U'; ?></div>
                        <span class="material-icons-outlined">expand_more</span>
                    </button>
                    <div class="dropdown-menu" id="profileMenu">
                        <div class="profile-info">
                            <div class="profile-avatar large"><?php echo isset($currentUser) ? substr($currentUser['name'], 0, 1) : 'U'; ?></div>
                            <div class="profile-details">
                                <h3><?php echo isset($currentUser) ? htmlspecialchars($currentUser['name']) : 'User Name'; ?></h3>
                                <p><?php echo isset($currentUser) ? htmlspecialchars($currentUser['email']) : 'user@example.com'; ?></p>
                            </div>
                        </div>
                        <div class="dropdown-divider"></div>
                        <a href="javascript:void(0)" class="dropdown-item" id="myAccountLink">
                            <span class="material-icons-outlined">account_circle</span>
                            <span>My Account</span>
                        </a>
                        <a href="javascript:void(0)" class="dropdown-item" id="bookingHistoryLink">
                            <span class="material-icons-outlined">history</span>
                            <span>Booking History</span>
                        </a>
                        <a href="javascript:void(0)" class="dropdown-item" id="savedToursLink">
                            <span class="material-icons-outlined">favorite_border</span>
                            <span>Saved Tours</span>
                        </a>
                        <div class="dropdown-divider"></div>
                        <a href="javascript:void(0)" class="dropdown-item" id="settingsLink">
                            <span class="material-icons-outlined">settings</span>
                            <span>Settings</span>
                        </a>
                        <a href="javascript:void(0)" class="dropdown-item" id="helpSupportLink">
                            <span class="material-icons-outlined">help_outline</span>
                            <span>Help & Support</span>
                        </a>
                        <a href="../logout.php" class="dropdown-item" id="signoutLink">
                            <span class="material-icons-outlined">logout</span>
                            <span>Sign Out</span>
                        </a>
                    </div>
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
        // ========================================
        // TRAVEL TIPS PAGE FUNCTIONALITY
        // ========================================

        // Pass current user data to JavaScript
        <?php if (isset($currentUser)): ?>
        const currentUser = <?php echo json_encode($currentUser); ?>;
        localStorage.setItem('currentUser', JSON.stringify(currentUser));
        <?php endif; ?>

        // Function to get nearby accommodations and restaurants for a tourist spot
        function getNearbySuggestions(spotName) {
            const suggestions = {
                'City Oval (People\'s Park)': {
                    'hotels': ['Hotel Sogo', 'Hotel Turista'],
                    'restaurants': ['Escobar\'s', 'Roadside Dampa'],
                    'malls': ['SM City SJDM', 'Starmall SJDM'],
                    'area': 'SJDM Center',
                    'transport_tip': 'Very accessible via jeepney and tricycle. Parking available.'
                },
                'Our Lady of Lourdes Parish / Padre Pio Parish': {
                    'hotels': ['Hotel Sogo', 'Staycation Amaia'],
                    'restaurants': ['Max\'s SM SJDM', 'Escobar\'s'],
                    'malls': ['SM City SJDM (nearby)'],
                    'area': 'Tungkong Mangga',
                    'transport_tip': 'Near major highways. Easy access from city center.'
                },
                'The Rising Heart Monument': {
                    'hotels': ['Local lodges in Paradise 3 area'],
                    'restaurants': ['Los Arcos De Hermano (close resort)', 'Escobar\'s (short drive)'],
                    'malls': [],
                    'area': 'Paradise 3 area',
                    'transport_tip': 'Best visited by private vehicle. Photo spot along highway.'
                },
                'Abes Farm / Paradise Hill Farm': {
                    'hotels': ['Los Arcos', 'Pacific Waves Resort'],
                    'restaurants': ['Farm-to-table restaurants in resort areas'],
                    'malls': [],
                    'area': 'Paradise / Rural SJDM',
                    'transport_tip': 'Requires private transportation. Rural roads may be narrow.'
                },
                'Waterfalls (Burong, Kaytitinga, Otso-Otso, Tungtong Falls)': {
                    'hotels': ['Hotel Sogo', 'Central SJDM accommodations'],
                    'restaurants': ['Escobar\'s', 'Local carinderias'],
                    'malls': [],
                    'area': 'Barangays San Isidro / Sto. Cristo',
                    'transport_tip': 'Requires local guides and transportation. Start early in the morning.'
                }
            };
            
            return suggestions[spotName] || {
                'hotels': ['Hotel Sogo (central location)', 'Local lodges'],
                'restaurants': ['Escobar\'s (central)', 'Local eateries'],
                'malls': ['SM City SJDM', 'Starmall SJDM'],
                'area': 'Central SJDM',
                'transport_tip': 'Check local transportation options'
            };
        }

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

        // Profile dropdown functionality
        document.addEventListener('DOMContentLoaded', function() {
            console.log('DOM Content Loaded');
            
            // Initialize travel tips page
            addTravelTipsAnimations();
            initTravelTipsPage();
            
            const profileButton = document.getElementById('profileButton');
            const profileMenu = document.getElementById('profileMenu');
            
            console.log('Profile Button:', profileButton);
            console.log('Profile Menu:', profileMenu);
            
            if (profileButton) {
                console.log('Profile button found, adding click listener');
                profileButton.addEventListener('click', function(e) {
                    console.log('Profile button clicked!');
                    e.preventDefault();
                    e.stopPropagation();
                    
                    if (profileMenu) {
                        console.log('Toggling menu. Current classes:', profileMenu.className);
                        profileMenu.classList.toggle('active');
                        console.log('Menu after toggle. Classes:', profileMenu.className);
                    }
                });
            } else {
                console.error('Profile button not found!');
            }
            
            // Close dropdown when clicking outside
            document.addEventListener('click', function(e) {
                if (profileButton && profileMenu && 
                    !profileButton.contains(e.target) && 
                    !profileMenu.contains(e.target)) {
                    profileMenu.classList.remove('active');
                }
            });
            
            // Add event listeners for all profile menu items
            const myAccountLink = document.getElementById('myAccountLink');
            const bookingHistoryLink = document.getElementById('bookingHistoryLink');
            const savedToursLink = document.getElementById('savedToursLink');
            const settingsLink = document.getElementById('settingsLink');
            const helpSupportLink = document.getElementById('helpSupportLink');
            const signoutLink = document.getElementById('signoutLink');
            
            if (myAccountLink) {
                myAccountLink.addEventListener('click', function(e) {
                    e.preventDefault();
                    profileMenu.classList.remove('active');
                    showMyAccountModal();
                });
            }
            
            if (bookingHistoryLink) {
                bookingHistoryLink.addEventListener('click', function(e) {
                    e.preventDefault();
                    profileMenu.classList.remove('active');
                    showBookingHistoryModal();
                });
            }
            
            if (savedToursLink) {
                savedToursLink.addEventListener('click', function(e) {
                    e.preventDefault();
                    profileMenu.classList.remove('active');
                    showSavedToursModal();
                });
            }
            
            if (settingsLink) {
                settingsLink.addEventListener('click', function(e) {
                    e.preventDefault();
                    profileMenu.classList.remove('active');
                    showSettingsModal();
                });
            }
            
            if (helpSupportLink) {
                helpSupportLink.addEventListener('click', function(e) {
                    e.preventDefault();
                    profileMenu.classList.remove('active');
                    showHelpSupportModal();
                });
            }
            
            if (signoutLink) {
                signoutLink.addEventListener('click', function(e) {
                    e.preventDefault();
                    profileMenu.classList.remove('active');
                    handleLogout();
                });
            }
        });
    </script>
</body>
</html>