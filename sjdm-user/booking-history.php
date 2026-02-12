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

$currentUser = [
    'name' => '',
    'email' => ''
];

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
}

// Handle cancel booking (DB)
$cancelResponse = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'cancel_booking') {
    header('Content-Type: application/json');

    $bookingId = intval($_POST['booking_id'] ?? 0);
    if ($bookingId < 1) {
        echo json_encode(['success' => false, 'message' => 'Invalid booking id']);
        closeDatabaseConnection($conn);
        exit;
    }

    try {
        $updateStmt = $conn->prepare("UPDATE bookings SET status = 'cancelled' WHERE id = ? AND user_id = ? AND status = 'pending'");
        $updateStmt->bind_param('ii', $bookingId, $_SESSION['user_id']);
        $updateStmt->execute();

        if ($updateStmt->affected_rows > 0) {
            echo json_encode(['success' => true, 'message' => 'Booking cancelled successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Unable to cancel booking']);
        }
        $updateStmt->close();
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Error cancelling booking']);
    }

    closeDatabaseConnection($conn);
    exit;
}

// Fetch bookings for this user
$userBookings = [];
if ($conn) {
    $bookingsSql = "SELECT b.id, b.booking_reference, b.tour_name, b.destination, b.booking_date, b.number_of_people, b.total_amount, b.status, tg.name AS guide_name
        FROM bookings b
        LEFT JOIN tour_guides tg ON b.guide_id = tg.id
        WHERE b.user_id = ?
        ORDER BY b.created_at DESC";

    $bookingsStmt = $conn->prepare($bookingsSql);
    if (!$bookingsStmt) {
        $bookingsSql = "SELECT b.id, b.booking_reference, b.tour_name, b.destination, b.booking_date, b.number_of_people, b.total_amount, b.status, tg.name AS guide_name
            FROM bookings b
            LEFT JOIN tour_guides tg ON b.guide_id = tg.id
            WHERE b.user_id = ?
            ORDER BY b.id DESC";
        $bookingsStmt = $conn->prepare($bookingsSql);
    }

    if ($bookingsStmt) {
        $bookingsStmt->bind_param('i', $_SESSION['user_id']);
        $bookingsStmt->execute();
        $bookingsResult = $bookingsStmt->get_result();
        while ($row = $bookingsResult->fetch_assoc()) {
            $userBookings[] = $row;
        }
        $bookingsStmt->close();
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
    
    <!-- Modal Styles -->
    <style>
        .modal-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.85);
            backdrop-filter: blur(8px);
            z-index: 9999;
            animation: fadeIn 0.3s ease;
        }

        .modal-overlay.show {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        .modal-content {
            background: white;
            border-radius: 24px;
            width: 90%;
            max-width: 600px;
            max-height: 90vh;
            overflow: hidden;
            box-shadow: 
                0 32px 64px rgba(0, 0, 0, 0.25),
                0 16px 32px rgba(0, 0, 0, 0.15),
                0 8px 16px rgba(0, 0, 0, 0.1);
            position: relative;
            border: 1px solid rgba(255, 255, 255, 0.2);
            animation: slideIn 0.3s ease;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-50px) scale(0.9);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 24px 32px 24px 32px;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: white;
            position: relative;
        }

        .modal-header h2 {
            margin: 0;
            font-size: 1.5rem;
            font-weight: 700;
            color: white;
        }

        .close-modal {
            background: rgba(255, 255, 255, 0.2);
            border: none;
            border-radius: 12px;
            width: 36px;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            color: white;
            font-size: 1.2rem;
            transition: all 0.3s ease;
        }

        .close-modal:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: scale(1.1);
        }

        .close-modal .material-icons-outlined {
            font-size: 20px;
        }

        .modal-body {
            padding: 32px;
            max-height: calc(90vh - 120px);
            overflow-y: auto;
        }

        .booking-details-content {
            display: grid;
            gap: 20px;
        }

        .booking-detail-row {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .booking-detail-row div {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .detail-item .material-icons-outlined {
            color: var(--primary);
            font-size: 20px;
        }

        .detail-item strong {
            color: var(--text-primary);
            font-weight: 600;
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
        }

        .status-badge.status-pending {
            background: linear-gradient(135deg, var(--warning) 0%, #d97706 100%);
            color: white;
        }

        .status-badge.status-confirmed {
            background: linear-gradient(135deg, var(--success) 0%, #059669 100%);
            color: white;
        }

        .status-badge.status-completed {
            background: linear-gradient(135deg, var(--info) 0%, #2563eb 100%);
            color: white;
        }

        .status-badge.status-cancelled {
            background: linear-gradient(135deg, var(--danger) 0%, #dc2626 100%);
            color: white;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .modal-content {
                width: 95%;
                margin: 20px;
            }
            
            .modal-header {
                padding: 20px 24px 20px 24px;
            }
            
            .modal-body {
                padding: 24px;
            }
            
            .booking-details-content {
                gap: 16px;
            }
        }
    </style>
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

    <!-- Modal Container -->
    <div id="modalContainer"></div>

    <script>
        let currentFilter = 'all';
        const userBookings = <?php echo json_encode($userBookings); ?>;
        const currentUserId = <?php echo (int)$_SESSION['user_id']; ?>;
        function showNotification(message, type) {
            console.log('Notification:', type, message);
            if (type === 'error') {
                alert(message);
            }
        }
        
        window.addEventListener('DOMContentLoaded', function() {
            console.log('Booking History currentUserId:', currentUserId);
            console.log('Booking History DB bookings:', userBookings);
            displayUserBookings();
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
            
            container.innerHTML = filteredBookings.map(booking => `
                <div class="booking-card" data-status="${booking.status}">
                    <div class="booking-card-header">
                        <div class="booking-primary-info">
                            <div class="booking-icon">
                                <span class="material-icons-outlined">tour</span>
                            </div>
                            <div class="booking-title-section">
                                <h3 class="booking-title">${booking.guide_name || 'Tour Guide'}</h3>
                                <p class="booking-destination">
                                    <span class="material-icons-outlined">place</span>
                                    ${booking.destination || booking.tour_name}
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
                                <div class="detail-label">Tour Date</div>
                                <div class="detail-value">${formatDate(booking.booking_date)}</div>
                            </div>
                        </div>
                        
                        <div class="detail-item">
                            <div class="detail-icon">
                                <span class="material-icons-outlined">people</span>
                            </div>
                            <div class="detail-content">
                                <div class="detail-label">Number of Guests</div>
                                <div class="detail-value">${booking.number_of_people} Guest${booking.number_of_people > 1 ? 's' : ''}</div>
                            </div>
                        </div>
                        
                        <div class="detail-item">
                            <div class="detail-icon">
                                <span class="material-icons-outlined">confirmation_number</span>
                            </div>
                            <div class="detail-content">
                                <div class="detail-label">Booking Reference</div>
                                <div class="detail-value">${booking.booking_reference || ('#' + booking.id)}</div>
                            </div>
                        </div>
                        
                        <div class="detail-item highlight">
                            <div class="detail-icon">
                                <span class="material-icons-outlined">payments</span>
                            </div>
                            <div class="detail-content">
                                <div class="detail-label">Total Amount</div>
                                <div class="detail-value price">₱${Number(booking.total_amount).toLocaleString()}</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="booking-card-divider"></div>
                    
                    <div class="booking-actions-row">
                        ${booking.status === 'pending' ? `
                            <button class="btn-action btn-cancel" onclick="cancelBooking(${booking.id})">
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
                        <button class="btn-action btn-view" onclick="viewBookingDetails(${booking.id})">
                            <span class="material-icons-outlined">visibility</span>
                            <span>View Details</span>
                        </button>
                        <button class="btn-action btn-download" onclick="downloadBooking(${booking.id})">
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

        function cancelBooking(bookingId) {
            if (!confirm('Are you sure you want to cancel this booking? This action cannot be undone.')) return;

            fetch('', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: `action=cancel_booking&booking_id=${bookingId}`
            })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        showNotification(data.message, 'info');
                        const booking = userBookings.find(b => String(b.id) === String(bookingId));
                        if (booking) booking.status = 'cancelled';
                        displayUserBookings();
                    } else {
                        showNotification(data.message, 'error');
                    }
                })
                .catch(() => {
                    showNotification('Unable to cancel booking', 'error');
                });
        }

        function modifyBooking(bookingNumber) {
            showNotification('Modify booking feature coming soon!', 'info');
        }

        function viewBookingDetails(bookingId) {
            const booking = userBookings.find(b => String(b.id) === String(bookingId));
            
            if (!booking) return;
            
            const content = `
                <div class="booking-details-modal">
                    <p><strong>Tour Guide:</strong> ${booking.guide_name || 'Tour Guide'}</p>
                    <p><strong>Destination:</strong> ${booking.destination || booking.tour_name}</p>
                    <p><strong>Tour Date:</strong> ${formatDate(booking.booking_date)}</p>
                    <p><strong>Guests:</strong> ${booking.number_of_people}</p>
                    <p><strong>Ref #:</strong> ${booking.booking_reference || ('#' + booking.id)}</p>
                    <p><strong>Status:</strong> ${booking.status.toUpperCase()}</p>
                    <p><strong>Total:</strong> ₱${Number(booking.total_amount).toLocaleString()}</p>
                </div>
            `;
            
            if (typeof createModal === 'function') {
                createModal('bookingDetailsModal', 'Booking Details', content, 'description');
            } else {
                console.log(booking);
                alert('Booking details in console');
            }
        }

        function downloadBooking(bookingId) {
            showNotification('Download feature coming soon!', 'info');
        }

        // Modal creation function
        function createModal(modalId, title, content, modalClass = '') {
            const modal = document.createElement('div');
            modal.className = 'modal-overlay';
            modal.innerHTML = `
                <div class="modal-content ${modalClass}">
                    <div class="modal-header">
                        <h2>${title}</h2>
                        <button class="close-modal" onclick="this.closest('.modal-overlay').remove()">
                            <span class="material-icons-outlined">close</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        ${content}
                    </div>
                </div>
            `;
            document.body.appendChild(modal);
            setTimeout(() => modal.classList.add('show'), 10);
            
            // Close modal when clicking outside
            modal.addEventListener('click', function(e) {
                if (e.target === modal) {
                    modal.remove();
                }
            });
        }

        // Listen for modal requests from parent window
        window.addEventListener('message', function(event) {
            if (event.data.type === 'showBookingModal') {
                createModal('bookingDetailsModal', 'Booking Details', event.data.content, 'booking-details-modal');
            }
        });

        // Check if there's a modal request in URL parameters
        const urlParams = new URLSearchParams(window.location.search);
        const showBookingId = urlParams.get('show');
        const bookingId = urlParams.get('booking');
        
        if (showBookingId && bookingId) {
            const booking = userBookings.find(b => String(b.id) === bookingId);
            if (booking) {
                const content = `
                    <div class="booking-details-content">
                        <div class="booking-detail-row">
                            <div class="detail-item">
                                <span class="material-icons-outlined">tour</span>
                                <strong>Tour Guide:</strong> ${booking.guide_name || 'Tour Guide'}
                            </div>
                        </div>
                        <div class="booking-detail-row">
                            <div class="detail-item">
                                <span class="material-icons-outlined">place</span>
                                <strong>Destination:</strong> ${booking.destination || booking.tour_name}
                            </div>
                        </div>
                        <div class="booking-detail-row">
                            <div class="detail-item">
                                <span class="material-icons-outlined">event</span>
                                <strong>Tour Date:</strong> ${formatDate(booking.booking_date)}
                            </div>
                        </div>
                        <div class="booking-detail-row">
                            <div class="detail-item">
                                <span class="material-icons-outlined">people</span>
                                <strong>Guests:</strong> ${booking.number_of_people}
                            </div>
                        </div>
                        <div class="booking-detail-row">
                            <div class="detail-item">
                                <span class="material-icons-outlined">confirmation_number</span>
                                <strong>Reference:</strong> ${booking.booking_reference || ('#' + booking.id)}
                            </div>
                        </div>
                        <div class="booking-detail-row">
                            <div class="detail-item">
                                <span class="material-icons-outlined">payments</span>
                                <strong>Total Amount:</strong> ₱${Number(booking.total_amount).toLocaleString()}
                            </div>
                        </div>
                        <div class="booking-detail-row">
                            <div class="detail-item">
                                <span class="material-icons-outlined">info</span>
                                <strong>Status:</strong> 
                                <span class="status-badge status-${booking.status.toLowerCase()}">
                                    ${getStatusIcon(booking.status)}
                                    <span>${booking.status.toUpperCase()}</span>
                                </span>
                            </div>
                        </div>
                    </div>
                `;
                
                createModal('bookingDetailsModal', 'Booking Details', content, 'booking-details-modal');
            }
        }
    </script>
</body>
</html>