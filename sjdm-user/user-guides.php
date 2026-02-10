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
$tourGuides = []; // Initialize tour guides array
$currentUser = []; // Initialize current user array

if ($conn) {
    // Get current user information
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
    $stmt->close();
    
    // Fetch tour guides from database
    $guidesStmt = $conn->prepare("SELECT * FROM tour_guides WHERE status = 'active' ORDER BY name ASC");
    if ($guidesStmt) {
        $guidesStmt->execute();
        $guidesResult = $guidesStmt->get_result();
        if ($guidesResult->num_rows > 0) {
            while ($guide = $guidesResult->fetch_assoc()) {
                $tourGuides[] = $guide;
            }
        }
        $guidesStmt->close();
    } else {
        echo "<!-- Error preparing statement -->";
    }
    
    closeDatabaseConnection($conn);
} else {
    echo "<!-- Database connection failed -->";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tour Guides - San Jose del Monte Bulacan</title>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
    <style>
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
<body>
    <!-- MAIN CONTENT -->
    <main class="main-content full-width">
        <header class="main-header">
            <div class="header-left">
                <h1>Tour Guides</h1>
                <div class="search-bar">
                    <span class="material-icons-outlined">search</span>
                    <input type="text" placeholder="Search guides..." id="guideSearch">
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
                    <a href="javascript:void(0)" class="nav-link active">
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
                    <a href="travel-tips.php" class="nav-link">
                        <span class="material-icons-outlined">tips_and_updates</span>
                        <span>Travel Tips</span>
                    </a>
                </nav>
                <div class="header-actions">
                    <button class="btn-signin" onclick="window.location.href='../log-in/log-in.php'">Sign in/register</button>
                </div>
            </div>
        </header>

        <div class="content-area">
            <h2 class="section-title">Meet Our Local Expert Tour Guides</h2>
            
            <!-- Filter Section -->
            <div class="filter-section">
                <div class="filter-container">
                    <div class="filter-buttons">
                        <button class="filter-btn active" data-filter="all">All Guides</button>
                        <button class="filter-btn" data-filter="mountain">Mountain Hiking</button>
                        <button class="filter-btn" data-filter="waterfall">Waterfall Tours</button>
                        <button class="filter-btn" data-filter="city">City Tours</button>
                        <button class="filter-btn" data-filter="farm">Farm & Eco-Tourism</button>
                        <button class="filter-btn" data-filter="historical">Historical Tours</button>
                        <button class="filter-btn" data-filter="general">General Tours</button>
                    </div>
                    <div class="sort-section">
                        <label>Sort by:</label>
                        <select id="sortGuides">
                            <option value="experience">Most Experience</option>
                            <option value="price-low">Price: Low to High</option>
                            <option value="price-high">Price: High to Low</option>
                        </select>
                    </div>
                </div>
            </div>

            <div id="guidesList" class="guides-grid">
                <?php
                if (!empty($tourGuides)) {
                    foreach ($tourGuides as $index => $guide) {
                        $guideId = $guide['id'];
                        $guideName = htmlspecialchars($guide['name']);
                        $guideSpecialty = htmlspecialchars($guide['specialty']);
                        $guideDescription = htmlspecialchars($guide['description'] ?? 'Experienced tour guide ready to show you the best of San Jose del Monte.');
                        $guideExperience = htmlspecialchars($guide['experience'] ?? '5+ years');
                        $guideLanguages = htmlspecialchars($guide['languages'] ?? 'English, Tagalog');
                        $guideGroupSize = htmlspecialchars($guide['max_group_size'] ?? '10 guests');
                        $guideCategory = htmlspecialchars($guide['category'] ?? 'general');
                        $guideVerified = isset($guide['verified']) && $guide['verified'] == '1';
                        
                        // Map category for data attributes
                        $categoryMap = [
                            'mountain' => 'mountain',
                            'waterfall' => 'waterfall', 
                            'city' => 'city',
                            'farm' => 'farm',
                            'historical' => 'historical',
                            'general' => 'general'
                        ];
                        $dataCategory = $categoryMap[$guideCategory] ?? 'general';
                ?>
                <div class="guide-card" data-guide-id="<?php echo $guideId; ?>" data-category="<?php echo $dataCategory; ?>">
                    <div class="guide-info">
                        <h3 class="guide-name"><?php echo $guideName; ?></h3>
                        <span class="guide-specialty"><?php echo $guideSpecialty; ?></span>
                        <p class="guide-description"><?php echo $guideDescription; ?></p>
                        <div class="guide-meta">
                            <div class="meta-item">
                                <span class="material-icons-outlined">schedule</span>
                                <?php echo $guideExperience; ?> experience
                            </div>
                            <div class="meta-item">
                                <span class="material-icons-outlined">translate</span>
                                <?php echo $guideLanguages; ?>
                            </div>
                            <div class="meta-item">
                                <span class="material-icons-outlined">groups</span>
                                Up to <?php echo $guideGroupSize; ?>
                            </div>
                        </div>
                        <div class="guide-footer">
                            <button class="btn-view-profile" onclick="openGuideModal(<?php echo $guideId; ?>)">View Profile</button>
                        </div>
                    </div>
                </div>
                <?php
                    }
                } else {
                    echo '<div class="no-guides-message" style="grid-column: 1 / -1; text-align: center; padding: 60px 20px;">';
                    echo '<span class="material-icons-outlined" style="font-size: 48px; color: #9ca3af;">person_off</span>';
                    echo '<h3 style="color: #6b7280; margin-top: 16px;">No tour guides available</h3>';
                    echo '<p style="color: #9ca3af;">Please check back later for available tour guides.</p>';
                    echo '</div>';
                }
                ?>
            </div>

            <!-- Dynamic Guide Profile Modals -->
            <?php
            if (!empty($tourGuides)) {
                foreach ($tourGuides as $guide) {
                    $guideId = $guide['id'];
                    $guideName = htmlspecialchars($guide['name']);
                    $guideSpecialty = htmlspecialchars($guide['specialty']);
                    $guideDescription = htmlspecialchars($guide['description'] ?? 'Experienced tour guide ready to show you the best of San Jose del Monte.');
                    $guideExperience = htmlspecialchars($guide['experience'] ?? '5+ years');
                    $guideLanguages = htmlspecialchars($guide['languages'] ?? 'English, Tagalog');
                    $guideGroupSize = htmlspecialchars($guide['max_group_size'] ?? '10 guests');
                    $guideCategory = htmlspecialchars($guide['category'] ?? 'general');
                    $guideVerified = isset($guide['verified']) && $guide['verified'] == '1';
                    $guideEmail = htmlspecialchars($guide['email'] ?? 'guide@sjdmtours.com');
                    $guidePhone = htmlspecialchars($guide['phone'] ?? '+63 912 345 6789');
                    $guideGender = htmlspecialchars($guide['gender'] ?? 'Not specified');
            ?>
            <div class="modal-overlay" id="modal-guide-<?php echo $guideId; ?>">
                <div class="modal-content guide-profile-modal">
                    <div class="modal-header">
                        <div class="modal-title">
                            <span class="material-icons-outlined modal-icon">person</span>
                            <h2>Guide Profile</h2>
                        </div>
                        <button class="close-modal" onclick="closeModal('modal-guide-<?php echo $guideId; ?>')">
                            <span class="material-icons-outlined">close</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="guide-profile-content">
                            <div class="guide-profile-header">
                                <div class="guide-profile-info">
                                    <div class="guide-name-section">
                                        <h3><?php echo $guideName; ?></h3>
                                        <?php if ($guideVerified) { ?>
                                        <div class="verified-ribbon">
                                            <span class="material-icons-outlined">verified_user</span>
                                            <span>Trusted Professional</span>
                                        </div>
                                        <?php } ?>
                                    </div>
                                    <p class="guide-specialty"><?php echo $guideSpecialty; ?></p>
                                    <div class="guide-category-badge">
                                        <span class="material-icons-outlined">category</span>
                                        <?php echo ucfirst($guideCategory); ?>
                                    </div>
                                </div>
                            </div>

                            <div class="guide-description-section">
                                <h4><span class="material-icons-outlined">info</span> About</h4>
                                <p><?php echo $guideDescription; ?></p>
                            </div>

                            <div class="guide-details-grid">
                                <div class="detail-item">
                                    <span class="material-icons-outlined">wc</span>
                                    <div>
                                        <strong>Gender</strong>
                                        <p><?php echo $guideGender; ?></p>
                                    </div>
                                </div>
                                <div class="detail-item">
                                    <span class="material-icons-outlined">translate</span>
                                    <div>
                                        <strong>Languages</strong>
                                        <p><?php echo $guideLanguages; ?></p>
                                    </div>
                                </div>
                                <div class="detail-item">
                                    <span class="material-icons-outlined">groups</span>
                                    <div>
                                        <strong>Group Size</strong>
                                        <p>Up to <?php echo $guideGroupSize; ?></p>
                                    </div>
                                </div>
                                <div class="detail-item">
                                    <span class="material-icons-outlined">place</span>
                                    <div>
                                        <strong>Location</strong>
                                        <p>San Jose del Monte, Bulacan</p>
                                    </div>
                                </div>
                                <div class="detail-item">
                                    <span class="material-icons-outlined">email</span>
                                    <div>
                                        <strong>Email</strong>
                                        <p><?php echo $guideEmail; ?></p>
                                    </div>
                                </div>
                                <div class="detail-item">
                                    <span class="material-icons-outlined">phone</span>
                                    <div>
                                        <strong>Phone</strong>
                                        <p><?php echo $guidePhone; ?></p>
                                    </div>
                                </div>
                            </div>

                            <div class="guide-booking-section">
                                <h4><span class="material-icons-outlined">calendar_today</span> Booking Information</h4>
                                <p>To book this guide and get detailed tour information, please click the button below.</p>
                                <div class="booking-actions">
                                    <button class="btn-primary" onclick="bookGuide(<?php echo $guideId; ?>)">
                                        <span class="material-icons-outlined">calendar_today</span>
                                        Book This Guide
                                    </button>
                                    <button class="btn-secondary" onclick="closeModal('modal-guide-<?php echo $guideId; ?>')">
                                        Close
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
                }
            }
            ?>
        </main>

    <!-- Booking History Modal -->
    <div class="modal-overlay" id="bookingHistoryModal">
        <div class="modal-content booking-modal">
            <div class="modal-header">
                <div class="modal-title">
                    <span class="material-icons-outlined modal-icon">history</span>
                    <h2>Booking History</h2>
                </div>
                <button class="close-modal" onclick="closeModal('bookingHistoryModal')">
                    <span class="material-icons-outlined">close</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Filter Tabs -->
                <div class="booking-filter-tabs">
                    <button class="filter-tab active" data-filter="all">
                        <span class="material-icons-outlined">list_alt</span>
                        <span>All Bookings</span>
                    </button>
                    <button class="filter-tab" data-filter="pending">
                        <span class="material-icons-outlined">schedule</span>
                        <span>Pending</span>
                    </button>
                    <button class="filter-tab" data-filter="confirmed">
                        <span class="material-icons-outlined">check_circle</span>
                        <span>Confirmed</span>
                    </button>
                    <button class="filter-tab" data-filter="completed">
                        <span class="material-icons-outlined">verified</span>
                        <span>Completed</span>
                    </button>
                    <button class="filter-tab" data-filter="cancelled">
                        <span class="material-icons-outlined">cancel</span>
                        <span>Cancelled</span>
                    </button>
                </div>

                <!-- Bookings List -->
                <div id="modalBookingsList" class="bookings-container"></div>
            </div>
        </div>
    </div>

    <script src="script.js"></script>
    <script>
        // Pass current user data to JavaScript
        <?php if (isset($currentUser)): ?>
        const currentUser = <?php echo json_encode($currentUser); ?>;
        localStorage.setItem('currentUser', JSON.stringify(currentUser));
        <?php endif; ?>

        // Modal functionality
        function openModal(modalId) {
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.classList.add('show');
                document.body.style.overflow = 'hidden';
                
                // Load content based on modal type
                if (modalId === 'bookingHistoryModal') {
                    loadBookingHistory();
                }
            }
        }

        function closeModal(modalId) {
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.classList.remove('show');
                document.body.style.overflow = 'auto';
            }
        }

        function openGuideModal(guideId) {
            const modal = document.getElementById('modal-guide-' + guideId);
            if (modal) {
                modal.classList.add('show');
                document.body.style.overflow = 'hidden';
            }
        }

        // Check for guide parameter in URL and open modal
        document.addEventListener('DOMContentLoaded', function() {
            const urlParams = new URLSearchParams(window.location.search);
            const guideId = urlParams.get('guide');
            
            if (guideId) {
                // Open the guide modal after a short delay
                setTimeout(() => {
                    openGuideModal(guideId);
                }, 500);
            }
        });

        function loadBookingHistory() {
            const container = document.getElementById('modalBookingsList');
            if (!container) return;
            
            const userBookings = JSON.parse(localStorage.getItem('userBookings')) || [];
            
            if (userBookings.length === 0) {
                container.innerHTML = `
                    <div class="empty-state-card">
                        <div class="empty-state-icon">
                            <span class="material-icons-outlined">event_busy</span>
                        </div>
                        <h3 class="empty-state-title">No bookings found</h3>
                        <p class="empty-state-text">Start your adventure by booking your first tour with our experienced guides.</p>
                        <button class="btn-primary-action" onclick="closeModal('bookingHistoryModal'); window.location.href='book.php'">
                            <span class="material-icons-outlined">explore</span>
                            <span>Book Now</span>
                        </button>
                    </div>
                `;
                return;
            }
            
            container.innerHTML = userBookings.reverse().map(booking => `
                <div class="booking-card" data-status="${booking.status}">
                    <div class="booking-card-header">
                        <div class="booking-primary-info">
                            <div class="booking-icon">
                                <span class="material-icons-outlined">tour</span>
                            </div>
                            <div class="booking-title-section">
                                <h3 class="booking-title">${booking.guideName}</h3>
                                <p class="booking-destination">
                                    <span class="material-icons-outlined">place</span>
                                    ${booking.destination}
                                </p>
                            </div>
                        </div>
                        <span class="status-badge status-${booking.status}">
                            ${getStatusIcon(booking.status)}
                            <span>${booking.status.toUpperCase()}</span>
                        </span>
                    </div>
                    
                    <div class="booking-card-divider"></div>
                    
                    <div class="booking-details-grid">
                        <div class="detail-item">
                            <div class="detail-icon">
                                <span class="material-icons-outlined">event</span>
                            </div>
                            <div class="detail-content">
                                <div class="detail-label">Check-in Date</div>
                                <div class="detail-value">${formatDate(booking.checkIn)}</div>
                            </div>
                        </div>
                        
                        <div class="detail-item">
                            <div class="detail-icon">
                                <span class="material-icons-outlined">people</span>
                            </div>
                            <div class="detail-content">
                                <div class="detail-label">Number of Guests</div>
                                <div class="detail-value">${booking.guests} Guest${booking.guests > 1 ? 's' : ''}</div>
                            </div>
                        </div>
                        
                        <div class="detail-item highlight">
                            <div class="detail-icon">
                                <span class="material-icons-outlined">payments</span>
                            </div>
                            <div class="detail-content">
                                <div class="detail-label">Total Amount</div>
                                <div class="detail-value price">₱${booking.totalAmount ? booking.totalAmount.toLocaleString() : '2,600'}</div>
                            </div>
                        </div>
                    </div>
                </div>
            `).join('');
        }

        function getStatusIcon(status) {
            const icons = {
                'pending': '<span class="material-icons-outlined">schedule</span>',
                'confirmed': '<span class="material-icons-outlined">check_circle</span>',
                'completed': '<span class="material-icons-outlined">verified</span>',
                'cancelled': '<span class="material-icons-outlined">cancel</span>'
            };
            return icons[status] || '<span class="material-icons-outlined">info</span>';
        }

        function formatDate(dateString) {
            const date = new Date(dateString);
            const options = { weekday: 'short', month: 'short', day: 'numeric', year: 'numeric' };
            return date.toLocaleDateString('en-US', options);
        }

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function () {
            // Profile dropdown functionality removed
        });
    </script>
</body>
</html>