<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Saved Tours - SJDM Tours</title>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
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
            <a class="nav-item" href="index.php#guides">
                <span class="material-icons-outlined">people</span>
                <span>Tour Guides</span>
            </a>
            <a class="nav-item" href="index.php#booking">
                <span class="material-icons-outlined">event</span>
                <span>Book Now</span>
            </a>
            <a class="nav-item" href="index.php#spots">
                <span class="material-icons-outlined">place</span>
                <span>Tourist Spots</span>
            </a>
        </nav>
    </aside>

    <!-- MAIN CONTENT -->
    <main class="main-content">
        <header class="main-header">
            <h1 id="pageTitle">Saved Tours</h1>
            <div class="header-actions">
                <button class="icon-button">
                    <span class="material-icons-outlined">notifications_none</span>
                </button>
                <div class="profile-dropdown">
                    <button class="profile-button" id="profileButton">
                        <div class="profile-avatar">U</div>
                        <span class="material-icons-outlined">expand_more</span>
                    </button>
                    <div class="dropdown-menu" id="profileMenu">
                        <div class="profile-info">
                            <div class="profile-avatar large">U</div>
                            <div class="profile-details">
                                <h3>User Name</h3>
                                <p>user@example.com</p>
                            </div>
                        </div>
                        <div class="dropdown-divider"></div>
                        <a href="my-account.html" class="dropdown-item">
                            <span class="material-icons-outlined">account_circle</span>
                            <span>My Account</span>
                        </a>
                        <a href="booking-history.html" class="dropdown-item">
                            <span class="material-icons-outlined">history</span>
                            <span>Booking History</span>
                        </a>
                        <a href="saved-tours.html" class="dropdown-item">
                            <span class="material-icons-outlined">favorite_border</span>
                            <span>Saved Tours</span>
                        </a>
                        <div class="dropdown-divider"></div>
                        <a href="settings.html" class="dropdown-item">
                            <span class="material-icons-outlined">settings</span>
                            <span>Settings</span>
                        </a>
                        <a href="help-support.html" class="dropdown-item">
                            <span class="material-icons-outlined">help_outline</span>
                            <span>Help & Support</span>
                        </a>
                        <a href="/coderistyarn/landingpage/landingpage.php" class="dropdown-item" onclick="handleSignOut(event)">
                            <span class="material-icons-outlined">logout</span>
                            <span>Sign Out</span>
                        </a>
                    </div>
                </div>
            </div>
        </header>

        <div class="content-area">
            <div class="page active">
                <button class="btn-back" onclick="window.location.href='index.php'">
                    <span class="material-icons-outlined">arrow_back</span>
                    Back to Home
                </button>

                <h2 class="section-title">Saved Tours</h2>
                <div id="savedToursList" class="guides-grid"></div>
            </div>
        </div>
    </main>

    <script>
        // Tour Guide Data (same as in script.js)
        const guides = [
            {
                id: 1,
                name: "Rico Mendoza",
                photo: "👨‍🏫",
                specialty: "Mt. Balagbag Hiking Expert",
                category: "mountain",
                description: "Certified mountain guide with 10 years of experience leading Mt. Balagbag expeditions. Safety-first approach with extensive knowledge of local trails.",
                areas: "Mt. Balagbag, Tuntong Falls, Mountain trails",
                rating: 5.0,
                reviewCount: 127,
                priceRange: "₱2,000 - ₱3,500 per day",
                languages: "English, Tagalog",
                experience: "10 years",
                verified: true
            },
            {
                id: 2,
                name: "Anna Marie Santos",
                photo: "👩‍💼",
                specialty: "Nature & Waterfall Tours",
                category: "nature",
                description: "Expert nature guide specializing in Kaytitinga Falls and forest eco-tours. Passionate about sustainable tourism and local ecology.",
                areas: "Kaytitinga Falls, Forest trails, Eco-tourism sites",
                rating: 4.9,
                reviewCount: 89,
                priceRange: "₱2,500 - ₱4,000 per day",
                languages: "English, Tagalog",
                experience: "7 years",
                verified: true
            },
            {
                id: 3,
                name: "Father Jose Reyes",
                photo: "🙏",
                specialty: "Religious & Pilgrimage Tours",
                category: "religious",
                description: "Former parish coordinator offering spiritual tours to Grotto of Our Lady of Lourdes and Padre Pio shrine with historical insights.",
                areas: "Grotto of Our Lady of Lourdes, Padre Pio Mountain, Churches",
                rating: 4.8,
                reviewCount: 156,
                priceRange: "₱1,500 - ₱2,500 per day",
                languages: "English, Tagalog, Spanish",
                experience: "15 years",
                verified: true
            },
            {
                id: 4,
                name: "Michael Cruz",
                photo: "🚵‍♂️",
                specialty: "Adventure & Extreme Sports",
                category: "adventure",
                description: "Adrenaline enthusiast offering adventure packages including hiking, rappelling, and team building activities at Paradise Adventure Camp.",
                areas: "Paradise Adventure Camp, Mt. Balagbag, Extreme trails",
                rating: 4.9,
                reviewCount: 94,
                priceRange: "₱3,000 - ₱5,000 per day",
                languages: "English, Tagalog",
                experience: "8 years",
                verified: true
            },
            {
                id: 5,
                name: "Linda Bautista",
                photo: "👩‍🌾",
                specialty: "Farm & Food Tours",
                category: "food",
                description: "Local farmer and culinary tour guide showcasing SJDM's agricultural heritage, orchid farms, and authentic Bulacan cuisine.",
                areas: "Orchid Garden, Pineapple Farms, Local restaurants, Markets",
                rating: 4.8,
                reviewCount: 72,
                priceRange: "₱2,000 - ₱3,500 per day",
                languages: "English, Tagalog",
                experience: "6 years",
                verified: true
            },
            {
                id: 6,
                name: "Carlos Villanueva",
                photo: "🏙️",
                specialty: "City & Cultural Tours",
                category: "city",
                description: "Urban guide and local historian showcasing SJDM's transformation from rural town to modern city while preserving cultural heritage.",
                areas: "City proper, Malls, Historical sites, Urban attractions",
                rating: 4.7,
                reviewCount: 68,
                priceRange: "₱1,800 - ₱3,000 per day",
                languages: "English, Tagalog",
                experience: "5 years",
                verified: true
            }
        ];
    </script>
    <script src="script.js"></script>
    <script src="profile-dropdown.js"></script>
    <script>
        window.addEventListener('DOMContentLoaded', function() {
            displayFavorites();
            initProfileDropdown();
            updateProfileUI();
        });

        function displayFavorites() {
            const favorites = JSON.parse(localStorage.getItem('favorites')) || [];
            const container = document.getElementById('savedToursList');
            
            if (favorites.length === 0) {
                container.innerHTML = `
                    <div class="empty-state">
                        <span class="material-icons-outlined">favorite_border</span>
                        <h3>No saved tours yet</h3>
                        <p>Start adding your favorite tour guides!</p>
                        <button class="btn-hero" onclick="window.location.href='index.php#guides'">Browse Tour Guides</button>
                    </div>
                `;
                return;
            }
            
            const favoriteGuides = guides.filter(g => favorites.includes(g.id));
            container.innerHTML = favoriteGuides.map(g => createGuideCard(g)).join('');
        }

        function createGuideCard(g) {
            const isFav = isFavorite(g.id);
            return `
                <div class="guide-card" onclick="window.location.href='index.php#profile-${g.id}'">
                    <div class="guide-photo">
                        ${g.photo}
                        <button class="favorite-btn ${isFav ? 'active' : ''}" onclick="event.stopPropagation(); toggleFavorite(${g.id})">
                            <span class="material-icons-outlined">${isFav ? 'favorite' : 'favorite_border'}</span>
                        </button>
                        ${g.verified ? '<span class="verified-badge"><span class="material-icons-outlined">verified</span></span>' : ''}
                    </div>
                    <div class="guide-info">
                        <div class="guide-name">${g.name}</div>
                        <span class="guide-specialty">${g.specialty}</span>
                        <p class="guide-description">${g.description}</p>
                        <div class="guide-meta">
                            <div class="meta-item">
                                <span class="material-icons-outlined">place</span>
                                <span>${g.areas.split(',')[0]}</span>
                            </div>
                            <div class="meta-item">
                                <span class="material-icons-outlined">language</span>
                                <span>${g.languages}</span>
                            </div>
                            <div class="meta-item">
                                <span class="material-icons-outlined">work</span>
                                <span>${g.experience}</span>
                            </div>
                            <div class="rating-display">
                                <span class="material-icons-outlined">star</span>
                                <span class="rating-value">${g.rating.toFixed(1)}</span>
                                <span class="review-count">(${g.reviewCount})</span>
                            </div>
                        </div>
                        <div class="guide-footer">
                            <span class="price-tag">${g.priceRange}</span>
                            <button class="btn-view-profile">View Profile</button>
                        </div>
                    </div>
                </div>
            `;
        }

        function isFavorite(guideId) {
            const favorites = JSON.parse(localStorage.getItem('favorites')) || [];
            return favorites.includes(guideId);
        }

        function toggleFavorite(guideId) {
            const favorites = JSON.parse(localStorage.getItem('favorites')) || [];
            const index = favorites.indexOf(guideId);
            
            if (index > -1) {
                favorites.splice(index, 1);
                showNotification('Removed from favorites', 'info');
            } else {
                favorites.push(guideId);
                showNotification('Added to favorites', 'success');
            }
            
            localStorage.setItem('favorites', JSON.stringify(favorites));
            displayFavorites();
        }

        function handleSignOut(e) {
            e.preventDefault();
            if (confirm('Are you sure you want to sign out?')) {
                localStorage.removeItem('currentUser');
                showNotification('Signed out successfully', 'info');
                setTimeout(() => {
                    window.location.href = '/coderistyarn/landingpage/landingpage.php';
                }, 1000);
            }
        }
    </script>
</body>
</html>