<?php

// Start session for user authentication

session_start();



// Check if user is logged in

if (!isset($_SESSION['user_id'])) {

    // Redirect to login page with return URL

    $_SESSION['redirect_after_login'] = 'book.php';

    header('Location: ../log-in/log-in.php');

    exit();

}



// Include database configuration

require_once '../config/database.php';



// Get user information from session

$user_id = $_SESSION['user_id'];

$user_email = $_SESSION['email'] ?? '';

$user_name = ($_SESSION['first_name'] ?? '') . ' ' . ($_SESSION['last_name'] ?? '');

?>

<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Book Now - San Jose del Monte Bulacan</title>

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

            <a class="nav-item active" href="javascript:void(0)">

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

            <a class="nav-item" href="travel-tips.php">

                <span class="material-icons-outlined">tips_and_updates</span>

                <span>Travel Tips</span>

            </a>

        </nav>

    </aside>



    <!-- MAIN CONTENT -->

    <main class="main-content">

        <header class="main-header">

            <h1>Book Now</h1>

            <div class="search-bar">

                <span class="material-icons-outlined">search</span>

                <input type="text" placeholder="Search tours or guides...">

            </div>

            <div class="header-actions">

                <button class="icon-button">

                    <span class="material-icons-outlined">notifications_none</span>

                    <span class="notification-badge" style="display: none;">0</span>

                </button>

                <!-- User Profile Dropdown -->
                <div class="profile-dropdown">
                    <button class="profile-button" id="userProfileButton">
                        <div class="profile-avatar"><?php echo isset($user_name) && $user_name ? substr($user_name, 0, 1) : 'U'; ?></div>
                        <span class="material-icons-outlined">expand_more</span>
                    </button>
                    <div class="dropdown-menu" id="userProfileMenu">
                        <div class="profile-info">
                            <div class="profile-avatar large"><?php echo isset($user_name) && $user_name ? substr($user_name, 0, 1) : 'U'; ?></div>
                            <div class="profile-details">
                                <h3 class="user-name"><?php echo isset($user_name) && $user_name ? htmlspecialchars($user_name) : 'User'; ?></h3>
                                <p class="user-email"><?php echo isset($user_email) ? htmlspecialchars($user_email) : 'user@sjdmtours.com'; ?></p>
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
               

        </header>



        <div class="content-area">

            <h2 class="section-title">Book Your SJDM Tour</h2>

            

            <div class="booking-progress">

                <div class="progress-step active" data-step="1">

                    <div class="step-number">1</div>

                    <div class="step-label">Tour Details</div>

                </div>

                <div class="progress-step" data-step="2">

                    <div class="step-number">2</div>

                    <div class="step-label">Personal Info</div>

                </div>

                <div class="progress-step" data-step="3">

                    <div class="step-number">3</div>

                    <div class="step-label">Review & Pay</div>

                </div>

                <div class="progress-step" data-step="4">

                    <div class="step-number">4</div>

                    <div class="step-label">Confirmation</div>

                </div>

            </div>



            <div id="step-1" class="booking-step active">

                <div class="form-container">

                    <h3>Tour Details</h3>

                    <form id="tourDetailsForm">

                        <div class="form-group">

                            <label>Select Tour Guide *</label>

                            <select id="selectedGuide" required>

                                <option value="">-- Choose a Guide --</option>

                                <option value="1">Rico Mendoza - Mt. Balagbag Hiking Expert</option>

                                <option value="2">Maria Santos - City Tour Specialist</option>

                                <option value="3">Carlos Dela Cruz - Farm and Eco-Tourism Guide</option>

                                <option value="4">Ana Reyes - Waterfall Adventure Guide</option>

                                <option value="5">James Lim - Historical and Cultural Guide</option>

                            </select>

                        </div>

                        <div class="form-group">

                            <label>Preferred Destination *</label>

                            <select id="destination" required>

                                <option value="">-- Select Destination --</option>

                                <option value="Mt. Balagbag">Mt. Balagbag Hiking</option>

                                <option value="Kaytitinga Falls">Kaytitinga Falls Tour</option>

                                <option value="Tungtong Falls">Tungtong Falls Adventure</option>

                                <option value="Burong Falls">Burong Falls Trek</option>

                                <option value="Otso-Otso Falls">Otso-Otso Falls Exploration</option>

                                <option value="Paradise Hill Farm">Paradise Hill Farm Tour</option>

                                <option value="Abes Farm">Abes Farm Experience</option>

                                <option value="The Rising Heart">The Rising Heart Visit</option>

                                <option value="City Oval & People's Park">City Park Tour</option>

                                <option value="Grotto of Our Lady of Lourdes">Religious Tour</option>

                                <option value="Padre Pio Mountain of Healing">Pilgrimage Tour</option>

                            </select>

                        </div>

                        <div class="form-row">

                            <div class="form-group">

                                <label>Check-in Date *</label>

                                <input type="date" id="checkInDate" required>

                            </div>

                            <div class="form-group">

                                <label>Check-out Date *</label>

                                <input type="date" id="checkOutDate" required>

                            </div>

                        </div>

                        <div class="form-group">

                            <label>Number of Guests *</label>

                            <input type="number" id="guestCount" min="1" max="30" value="1" required>

                        </div>

                        <div class="form-actions">

                            <button type="button" class="btn-next" onclick="nextStep(1)">

                                Next: Personal Info

                                <span class="material-icons-outlined">arrow_forward</span>

                            </button>

                        </div>

                    </form>

                </div>

            </div>



            <div id="step-2" class="booking-step">

                <div class="form-container">

                    <h3>Personal Information</h3>

                    <form id="personalInfoForm">

                        <div class="form-group">

                            <label>Full Name *</label>

                            <input type="text" id="fullName" placeholder="Juan Dela Cruz" required>

                        </div>

                        <div class="form-row">

                            <div class="form-group">

                                <label>Email Address *</label>

                                <input type="email" id="email" placeholder="juan@example.com" required>

                            </div>

                            <div class="form-group">

                                <label>Contact Number *</label>

                                <input type="tel" id="contactNumber" placeholder="+63 912 345 6789" required>

                            </div>

                        </div>

                        <div class="form-group">

                            <label>Special Requests (Optional)</label>

                            <textarea id="specialRequests" rows="3" placeholder="Any special requirements or requests..."></textarea>

                        </div>

                        <div class="form-actions">

                            <button type="button" class="btn-prev" onclick="prevStep(2)">

                                <span class="material-icons-outlined">arrow_back</span>

                                Back

                            </button>

                            <button type="button" class="btn-next" onclick="nextStep(2)">

                                Next: Review

                                <span class="material-icons-outlined">arrow_forward</span>

                            </button>

                        </div>

                    </form>

                </div>

            </div>



            <div id="step-3" class="booking-step">

                <div class="form-container">

                    <h3>Review Your Booking</h3>

                    <div class="booking-review-summary">

                        <div class="review-section">

                            <h4>Tour Summary</h4>

                            <div class="review-grid">

                                <div><strong>Guide:</strong> <span id="reviewGuideName">-</span></div>

                                <div><strong>Destination:</strong> <span id="reviewDestination">-</span></div>

                                <div><strong>Check-in:</strong> <span id="reviewCheckIn">-</span></div>

                                <div><strong>Check-out:</strong> <span id="reviewCheckOut">-</span></div>

                                <div><strong>Guests:</strong> <span id="reviewGuests">-</span></div>

                            </div>

                        </div>

                        <div class="price-summary">

                            <h4>Price Summary</h4>

                            <div class="price-row">

                                <span>Tour Guide Fee</span>

                                <span>₱2,500.00</span>

                            </div>

                            <div class="price-row">

                                <span>Entrance Fees</span>

                                <span>₱100.00</span>

                            </div>

                            <div class="price-row">

                                <span>Service Fee</span>

                                <span>₱200.00</span>

                            </div>

                            <div class="price-row total">

                                <span>Total Amount</span>

                                <span>₱2,800.00</span>

                            </div>

                        </div>

                    </div>

                    <div class="payment-methods">

                        <h4>Payment Method</h4>

                        <div class="payment-note">

                            <span class="material-icons-outlined">info</span>

                            <p>This is a booking request. Payment will be arranged with the tour guide after confirmation.</p>

                        </div>

                        <div class="payment-options">

                            <label class="payment-option">

                                <input type="radio" name="paymentMethod" value="pay_later" checked>

                                <span class="material-icons-outlined">schedule</span>

                                <span>Pay Later (Coordinate with guide)</span>

                            </label>

                            <label class="payment-option">

                                <input type="radio" name="paymentMethod" value="gcash">

                                <span class="material-icons-outlined">account_balance_wallet</span>

                                <span>GCash</span>

                            </label>

                            <label class="payment-option">

                                <input type="radio" name="paymentMethod" value="bank_transfer">

                                <span class="material-icons-outlined">account_balance</span>

                                <span>Bank Transfer</span>

                            </label>

                        </div>

                    </div>

                    <div class="form-actions">

                        <button type="button" class="btn-prev" onclick="prevStep(3)">

                            <span class="material-icons-outlined">arrow_back</span>

                            Back

                        </button>

                        <button type="button" class="btn-submit" onclick="submitBooking()">

                            <span class="material-icons-outlined">check_circle</span>

                            Submit Booking Request

                        </button>

                    </div>

                </div>

            </div>



            <div id="step-4" class="booking-step">

                <div class="confirmation-container">

                    <div class="confirmation-header">

                        <div class="confirmation-icon">

                            <span class="material-icons-outlined">check_circle</span>

                        </div>

                        <h2>Booking Request Submitted!</h2>

                        <p>Thank you for choosing San Jose del Monte Tours. We've sent a confirmation email to <strong><span id="confirmationEmail"></span></strong></p>

                    </div>

                    

                    <div class="confirmation-details">

                        <div class="booking-info">

                            <h3>Booking Details</h3>

                            <div style="margin: 16px 0;">

                                <span class="info-label">Booking Number:</span>

                                <span class="info-value" id="bookingNumber">SJDM-2023-12345</span>

                            </div>

                            <div style="margin: 16px 0;">

                                <span class="info-label">Guest Name:</span>

                                <span class="info-value" id="guestName"></span>

                            </div>

                            <div style="margin: 16px 0;">

                                <span class="info-label">Tour Guide:</span>

                                <span class="info-value" id="tourGuideName"></span>

                            </div>

                            <div style="margin: 16px 0;">

                                <span class="info-label">Tour Date:</span>

                                <span class="info-value" id="tourDateRange"></span>

                            </div>

                            <div style="margin: 16px 0;">

                                <span class="info-label">Contact:</span>

                                <span class="info-value" id="guestContact"></span>

                            </div>

                        </div>



                        <div class="reservation-summary">

                            <h3>Reservation Summary</h3>

                            <div style="margin: 16px 0;">

                                <strong><span id="summaryTourName"></span></strong>

                            </div>

                            <div style="margin: 16px 0;">

                                <span id="summaryCheckIn"></span> - <span id="summaryCheckOut"></span><br>

                                <span id="summaryNights"></span>

                            </div>

                            

                            <div class="price-summary">

                                <h4>Price Summary</h4>

                                <div class="price-row">

                                    <span>Tour Guide Fee</span>

                                    <span id="summaryGuideFee">₱2,500.00</span>

                                </div>

                                <div class="price-row">

                                    <span>Entrance Fees</span>

                                    <span id="summaryEntranceFees">₱100.00</span>

                                </div>

                                <div class="price-row">

                                    <span>Service Fee</span>

                                    <span>₱200.00</span>

                                </div>

                                <div class="price-row total">

                                    <span>Total Price</span>

                                    <span id="summaryTotal">₱2,800.00</span>

                                </div>

                            </div>

                        </div>

                    </div>

                    

                    <div style="text-align: center; margin-top: 32px;">

                        <div class="status-info">

                            <span class="material-icons-outlined">info</span>

                            <p>Your tour guide will contact you within 24 hours to confirm availability and arrange payment details.</p>

                        </div>

                        <div style="display: flex; gap: 12px; justify-content: center; margin-top: 24px;">

                            <button class="btn-hero" onclick="window.location.href='index.php'">

                                <span class="material-icons-outlined">home</span>

                                Back to Home

                            </button>

                            <button class="btn-secondary" onclick="window.location.href='user-guides.php'">

                                <span class="material-icons-outlined">people</span>

                                View More Guides

                            </button>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </main>



    <script src="script.js"></script>

</body>

</html>