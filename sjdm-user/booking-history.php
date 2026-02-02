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
            <h1 id="pageTitle">Booking History</h1>
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

                <h2 class="section-title">My Bookings</h2>
                <div id="bookingsList"></div>
            </div>
        </div>
    </main>

    <script src="script.js"></script>
    <script src="profile-dropdown.js"></script>
    <script>
        window.addEventListener('DOMContentLoaded', function() {
            displayUserBookings();
            initProfileDropdown();
            updateProfileUI();
        });

        function displayUserBookings() {
            const container = document.getElementById('bookingsList');
            if (!container) return;
            
            const userBookings = JSON.parse(localStorage.getItem('userBookings')) || [];
            
            if (userBookings.length === 0) {
                container.innerHTML = `
                    <div class="empty-state">
                        <span class="material-icons-outlined">event_busy</span>
                        <h3>No bookings yet</h3>
                        <p>Start exploring and book your first tour!</p>
                        <button class="btn-hero" onclick="window.location.href='index.php#guides'">Browse Tour Guides</button>
                    </div>
                `;
                return;
            }
            
            container.innerHTML = userBookings.reverse().map(booking => `
                <div class="booking-card">
                    <div class="booking-header">
                        <div>
                            <h3>${booking.guideName}</h3>
                            <p class="booking-destination">${booking.destination}</p>
                        </div>
                        <span class="status-badge status-${booking.status}">${booking.status.toUpperCase()}</span>
                    </div>
                    <div class="booking-details">
                        <div class="detail-item">
                            <span class="material-icons-outlined">calendar_today</span>
                            <span>${formatDate(booking.checkIn)} - ${formatDate(booking.checkOut)}</span>
                        </div>
                        <div class="detail-item">
                            <span class="material-icons-outlined">people</span>
                            <span>${booking.guests} guest${booking.guests > 1 ? 's' : ''}</span>
                        </div>
                        <div class="detail-item">
                            <span class="material-icons-outlined">confirmation_number</span>
                            <span>${booking.bookingNumber}</span>
                        </div>
                        <div class="detail-item">
                            <span class="material-icons-outlined">payments</span>
                            <span>₱${booking.totalAmount ? booking.totalAmount.toLocaleString() : '2,600'}</span>
                        </div>
                    </div>
                    <div class="booking-actions">
                        ${booking.status === 'pending' ? `
                            <button class="btn-cancel" onclick="cancelBooking('${booking.bookingNumber}')">
                                <span class="material-icons-outlined">cancel</span>
                                Cancel Booking
                            </button>
                        ` : ''}
                        ${booking.status === 'completed' ? `
                            <button class="btn-review" onclick="window.location.href='index.php#guides'">
                                <span class="material-icons-outlined">rate_review</span>
                                Leave a Review
                            </button>
                        ` : ''}
                        <button class="btn-view" onclick="viewBookingDetails('${booking.bookingNumber}')">
                            <span class="material-icons-outlined">visibility</span>
                            View Details
                        </button>
                    </div>
                </div>
            `).join('');
        }

        function formatDate(dateString) {
            const date = new Date(dateString);
            const options = { month: 'short', day: 'numeric', year: 'numeric' };
            return date.toLocaleDateString('en-US', options);
        }

        function cancelBooking(bookingNumber) {
            if (!confirm('Are you sure you want to cancel this booking?')) return;
            
            const bookings = JSON.parse(localStorage.getItem('userBookings')) || [];
            const index = bookings.findIndex(b => b.bookingNumber === bookingNumber);
            
            if (index > -1) {
                bookings[index].status = 'cancelled';
                localStorage.setItem('userBookings', JSON.stringify(bookings));
                showNotification('Booking cancelled successfully', 'info');
                displayUserBookings();
            }
        }

        function viewBookingDetails(bookingNumber) {
            const bookings = JSON.parse(localStorage.getItem('userBookings')) || [];
            const booking = bookings.find(b => b.bookingNumber === bookingNumber);
            
            if (!booking) return;
            
            alert(`Booking Details:\n\nGuide: ${booking.guideName}\nDestination: ${booking.destination}\nDates: ${formatDate(booking.checkIn)} - ${formatDate(booking.checkOut)}\nGuests: ${booking.guests}\nStatus: ${booking.status.toUpperCase()}`);
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