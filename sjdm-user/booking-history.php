<?php
// Include database connection and authentication
require_once '../config/database.php';
require_once '../config/auth.php';

// OpenWeatherMap API configuration
$apiKey = '6c21a0d2aaf514cb8d21d56814312b19';
$weatherUrl = "https://api.openweathermap.org/data/2.5/weather?q=San%20Jose%20Del%20Monte,Bulacan&appid={$apiKey}&units=metric";

$weatherData = null;
$weatherError = null;
$currentTemp = '28';
$weatherLabel = 'Sunny';

// Fetch weather data
try {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $weatherUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    $weatherResponse = curl_exec($ch);
    
    if (curl_errno($ch)) {
        $weatherError = 'Weather API connection error';
    } else {
        $weatherData = json_decode($weatherResponse, true);
        if ($weatherData && isset($weatherData['main']) && isset($weatherData['weather'][0])) {
            $currentTemp = round($weatherData['main']['temp']);
            $weatherLabel = ucfirst($weatherData['weather'][0]['description']);
        } else {
            $weatherError = 'Weather data unavailable';
        }
    }
    curl_close($ch);
} catch (Exception $e) {
    $weatherError = 'Weather service unavailable';
}

// Get current date and weekday
$currentWeekday = date('l');
$currentDate = date('F Y');

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
    <title>Booking History - SJDM Tours</title>
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
            <a class="nav-item" href="user-guides.php">
                <span class="material-icons-outlined">people</span>
                <span>Tour Guides</span>
            </a>
            <a class="nav-item" href="book.php">
                <span class="material-icons-outlined">event</span>
                <span>Book Now</span>
            </a>
            <a class="nav-item active" href="booking-history.php">
                <span class="material-icons-outlined">history</span>
                <span>Booking History</span>
            </a>
            <a class="nav-item" href="hotel-booking.php">
                <span class="material-icons-outlined">hotel</span>
                <span>Hotels</span>
            </a>
            <a class="nav-item" href="local-culture.php">
                <span class="material-icons-outlined">theater_comedy</span>
                <span>Local Culture</span>
            </a>
            <a class="nav-item" href="travel-tips.php">
                <span class="material-icons-outlined">tips_and_updates</span>
                <span>Travel Tips</span>
            </a>
        </nav>
    </aside>

    <!-- MAIN CONTENT -->
    <main class="main-content">
        <header class="main-header">
            <h1>Booking History</h1>
            <div class="search-bar">
                <span class="material-icons-outlined">search</span>
                <input type="text" placeholder="Search bookings..." id="searchInput">
            </div>
            <div class="header-actions">
                <button class="icon-button">
                    <span class="material-icons-outlined">notifications_none</span>
                    <span class="notification-badge" style="display: none;">0</span>
                </button>
                
                <!-- User Profile Dropdown -->
                <div class="profile-dropdown">
                    <button class="profile-button" id="userProfileButton">
                        <div class="profile-avatar"><?php echo isset($currentUser['name']) ? substr($currentUser['name'], 0, 1) : 'U'; ?></div>
                        <span class="material-icons-outlined">expand_more</span>
                    </button>
                    <div class="dropdown-menu" id="userProfileMenu">
                        <div class="profile-info">
                            <div class="profile-avatar large"><?php echo isset($currentUser['name']) ? substr($currentUser['name'], 0, 1) : 'U'; ?></div>
                            <div class="profile-details">
                                <h3 class="user-name"><?php echo isset($currentUser['name']) ? htmlspecialchars($currentUser['name']) : 'User'; ?></h3>
                                <p class="user-email"><?php echo isset($currentUser['email']) ? htmlspecialchars($currentUser['email']) : 'user@sjdmtours.com'; ?></p>
                            </div>
                        </div>
                        <div class="dropdown-divider"></div>
                        <a href="javascript:void(0)" class="dropdown-item" id="userAccountLink">
                            <span class="material-icons-outlined">account_circle</span>
                            <span>My Account</span>
                        </a>
                        <a href="javascript:void(0)" class="dropdown-item" id="userBookingHistoryLink">
                            <span class="material-icons-outlined">history</span>
                            <span>Booking History</span>
                        </a>
                        <a href="javascript:void(0)" class="dropdown-item" id="userSavedToursLink">
                            <span class="material-icons-outlined">favorite</span>
                            <span>Saved Tours</span>
                        </a>
                        <div class="dropdown-divider"></div>
                        <a href="javascript:void(0)" class="dropdown-item" id="userSettingsLink">
                            <span class="material-icons-outlined">settings</span>
                            <span>Settings</span>
                        </a>
                        <a href="javascript:void(0)" class="dropdown-item" id="userHelpLink">
                            <span class="material-icons-outlined">help_outline</span>
                            <span>Help & Support</span>
                        </a>
                        <div class="dropdown-divider"></div>
                        <a href="logout.php" class="dropdown-item">
                            <span class="material-icons-outlined">logout</span>
                            <span>Sign Out</span>
                        </a>
                    </div>
                </div>
            </div>
        </header>

        <div class="content-area booking-history-page">
            <!-- Page Header -->
            <div class="page-header-section">
                <div class="page-header-content">
                    <h2 class="page-title">My Booking History</h2>
                    <p class="page-subtitle">View and manage all your tour bookings in one place</p>
                </div>
                
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
            </div>

            <!-- Calendar and Weather Info -->
            <div class="info-cards-row">
                <div class="info-card calendar-card">
                    <div class="info-card-icon">
                        <span class="material-icons-outlined">calendar_today</span>
                    </div>
                    <div class="info-card-content">
                        <div class="info-label">Today's Date</div>
                        <div class="info-value"><?php echo htmlspecialchars($currentWeekday); ?></div>
                        <div class="info-secondary"><?php echo htmlspecialchars($currentDate); ?></div>
                    </div>
                </div>
                
                <div class="info-card weather-card">
                    <div class="info-card-icon">
                        <span class="material-icons-outlined"><?php echo $weatherLabel === 'Clear' ? 'wb_sunny' : ($weatherLabel === 'Clouds' ? 'cloud' : 'wb_cloudy'); ?></span>
                    </div>
                    <div class="info-card-content">
                        <div class="info-label">Current Weather</div>
                        <div class="info-value"><?php echo $currentTemp; ?>°C</div>
                        <div class="info-secondary"><?php echo htmlspecialchars($weatherLabel); ?></div>
                    </div>
                </div>
            </div>

            <!-- Bookings List -->
            <div id="bookingsList" class="bookings-container"></div>
        </div>
    </main>

    <script src="script.js"></script>
    <script>
        let currentFilter = 'all';
        
        window.addEventListener('DOMContentLoaded', function() {
            displayUserBookings();
            // updateUserInterface is defined in script.js to handle UI updates
            if (typeof updateUserInterface === 'function') {
                updateUserInterface();
            }
            initFilterTabs();
            initSearch();
        });

        function initFilterTabs() {
            const filterTabs = document.querySelectorAll('.filter-tab');
            if (!filterTabs) return;
            filterTabs.forEach(tab => {
                tab.addEventListener('click', function() {
                    filterTabs.forEach(t => t.classList.remove('active'));
                    this.classList.add('active');
                    currentFilter = this.dataset.filter;
                    displayUserBookings();
                });
            });
        }

        function initSearch() {
            const searchInput = document.getElementById('searchInput');
            if (searchInput) {
                searchInput.addEventListener('input', function(e) {
                    const searchTerm = e.target.value.toLowerCase();
                    filterBookings(searchTerm);
                });
            }
        }

        function filterBookings(searchTerm) {
            const bookingCards = document.querySelectorAll('.booking-card');
            if (!bookingCards) return;
            bookingCards.forEach(card => {
                const text = card.textContent.toLowerCase();
                if (text.includes(searchTerm)) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        }

        function displayUserBookings() {
            const container = document.getElementById('bookingsList');
            if (!container) return;
            
            const userBookings = JSON.parse(localStorage.getItem('userBookings')) || [];
            
            // Filter bookings based on current filter
            let filteredBookings = userBookings;
            if (currentFilter !== 'all') {
                filteredBookings = userBookings.filter(b => b.status === currentFilter);
            }
            
            if (filteredBookings.length === 0) {
                container.innerHTML = `
                    <div class="empty-state-card">
                        <div class="empty-state-icon">
                            <span class="material-icons-outlined">event_busy</span>
                        </div>
                        <h3 class="empty-state-title">No ${currentFilter !== 'all' ? currentFilter : ''} bookings found</h3>
                        <p class="empty-state-text">
                            ${currentFilter === 'all' 
                                ? 'Start your adventure by booking your first tour with our experienced guides.' 
                                : `You don't have any ${currentFilter} bookings at the moment.`}
                        </p>
                        <button class="btn-primary-action" onclick="window.location.href='index.php#guides'">
                            <span class="material-icons-outlined">explore</span>
                            <span>Browse Tour Guides</span>
                        </button>
                    </div>
                `;
                return;
            }
            
            container.innerHTML = filteredBookings.reverse().map(booking => `
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
                                <span class="material-icons-outlined">event_available</span>
                            </div>
                            <div class="detail-content">
                                <div class="detail-label">Check-out Date</div>
                                <div class="detail-value">${formatDate(booking.checkOut)}</div>
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
                        
                        <div class="detail-item">
                            <div class="detail-icon">
                                <span class="material-icons-outlined">confirmation_number</span>
                            </div>
                            <div class="detail-content">
                                <div class="detail-label">Booking Reference</div>
                                <div class="detail-value">${booking.bookingNumber}</div>
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
                    
                    <div class="booking-card-divider"></div>
                    
                    <div class="booking-actions-row">
                        ${booking.status === 'pending' ? `
                            <button class="btn-action btn-cancel" onclick="cancelBooking('${booking.bookingNumber}')">
                                <span class="material-icons-outlined">cancel</span>
                                <span>Cancel Booking</span>
                            </button>
                        ` : ''}
                        ${booking.status === 'completed' ? `
                            <button class="btn-action btn-review" onclick="window.location.href='index.php#guides'">
                                <span class="material-icons-outlined">rate_review</span>
                                <span>Write a Review</span>
                            </button>
                        ` : ''}
                        ${booking.status === 'confirmed' ? `
                            <button class="btn-action btn-modify" onclick="modifyBooking('${booking.bookingNumber}')">
                                <span class="material-icons-outlined">edit</span>
                                <span>Modify Booking</span>
                            </button>
                        ` : ''}
                        <button class="btn-action btn-view" onclick="viewBookingDetails('${booking.bookingNumber}')">
                            <span class="material-icons-outlined">visibility</span>
                            <span>View Details</span>
                        </button>
                        <button class="btn-action btn-download" onclick="downloadBooking('${booking.bookingNumber}')">
                            <span class="material-icons-outlined">download</span>
                            <span>Download</span>
                        </button>
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

        function cancelBooking(bookingNumber) {
            if (!confirm('Are you sure you want to cancel this booking? This action cannot be undone.')) return;
            
            const bookings = JSON.parse(localStorage.getItem('userBookings')) || [];
            const index = bookings.findIndex(b => b.bookingNumber === bookingNumber);
            
            if (index > -1) {
                bookings[index].status = 'cancelled';
                localStorage.setItem('userBookings', JSON.stringify(bookings));
                showNotification('Booking cancelled successfully', 'info');
                displayUserBookings();
            }
        }

        function modifyBooking(bookingNumber) {
            showNotification('Modify booking feature coming soon!', 'info');
        }

        function viewBookingDetails(bookingNumber) {
            const bookings = JSON.parse(localStorage.getItem('userBookings')) || [];
            const booking = bookings.find(b => b.bookingNumber === bookingNumber);
            
            if (!booking) return;
            
            const content = `
                <div class="booking-details-modal">
                    <p><strong>Tour Guide:</strong> ${booking.guideName}</p>
                    <p><strong>Destination:</strong> ${booking.destination}</p>
                    <p><strong>Check-in:</strong> ${formatDate(booking.checkIn)}</p>
                    <p><strong>Check-out:</strong> ${formatDate(booking.checkOut)}</p>
                    <p><strong>Guests:</strong> ${booking.guests}</p>
                    <p><strong>Ref #:</strong> ${booking.bookingNumber}</p>
                    <p><strong>Status:</strong> ${booking.status.toUpperCase()}</p>
                    <p><strong>Total:</strong> ₱${booking.totalAmount ? booking.totalAmount.toLocaleString() : '2,600'}</p>
                </div>
            `;
            
            if (typeof createModal === 'function') {
                createModal('bookingDetailsModal', 'Booking Details', content, 'description');
            } else {
                console.log(booking);
                alert('Booking details in console');
            }
        }

        function downloadBooking(bookingNumber) {
            showNotification('Download feature coming soon!', 'info');
        }
    </script>
</body>
</html>