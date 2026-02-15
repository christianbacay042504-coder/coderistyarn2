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
    <title>Help & Support - SJDM Tours</title>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="user-styles.css">
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
            <a class="nav-item" href="tourist-spots.php">
                <span class="material-icons-outlined">place</span>
                <span>Tourist Spots</span>
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
            <h1>Help & Support</h1>
            <div class="search-bar">
                <span class="material-icons-outlined">search</span>
                <input type="text" placeholder="Search help topics...">
            </div>
            <div class="header-actions">
                
            </div>
        </header>

        <div class="content-area">
            <h2 class="section-title">Help & Support</h2>
            
            <!-- Calendar Header -->
            <div class="calendar-header">
                <div class="date-display">
                    <div class="weekday" id="currentWeekday"><?php echo htmlspecialchars($currentWeekday); ?></div>
                    <div class="month-year" id="currentDate"><?php echo htmlspecialchars($currentDate); ?></div>
                </div>
                <div class="weather-info">
                    <span class="material-icons-outlined"><?php echo $weatherLabel === 'Clear' ? 'wb_sunny' : ($weatherLabel === 'Clouds' ? 'cloud' : 'wb_cloudy'); ?></span>
                    <span class="temperature"><?php echo $currentTemp; ?>°C</span>
                    <span class="weather-label"><?php echo htmlspecialchars($weatherLabel); ?></span>
                </div>
            </div>

            <div class="info-cards">
                <div class="info-card">
                    <h3>📞 Contact Information</h3>
                    <ul>
                        <li>Customer Support: +63 917 123 4567</li>
                        <li>Email: support@example.com</li>
                        <li>Office Hours: Mon-Fri, 9AM-6PM</li>
                        <li>Emergency Hotline: 911</li>
                        <li>Location: San Jose del Monte, Bulacan</li>
                    </ul>
                </div>
                
                <div class="info-card">
                    <h3>❓ Frequently Asked Questions</h3>
                    <ul>
                        <li>How do I book a tour guide?</li>
                        <li>What payment methods are accepted?</li>
                        <li>Can I cancel my booking?</li>
                        <li>Are the guides licensed and insured?</li>
                        <li>What if it rains on my tour day?</li>
                        <li>Do you offer group discounts?</li>
                    </ul>
                </div>
                
                <div class="info-card">
                    <h3>📝 Booking Policies</h3>
                    <ul>
                        <li>Free cancellation 24 hours before tour</li>
                        <li>Full refund for guide cancellations</li>
                        <li>Rescheduling available (subject to availability)</li>
                        <li>Group discounts for 10+ people</li>
                        <li>Payment arrangements with tour guide</li>
                        <li>48-hour advance booking recommended</li>
                    </ul>
                </div>
                
                <div class="info-card">
                    <h3>🛡️ Safety Guidelines</h3>
                    <ul>
                        <li>All guides are certified and insured</li>
                        <li>First aid kits provided on all tours</li>
                        <li>Weather monitoring for safety</li>
                        <li>Emergency contact system in place</li>
                        <li>Regular guide training and certification</li>
                        <li>24/7 emergency support hotline</li>
                    </ul>
                </div>
                
                <div class="info-card">
                    <h3>💡 Tips for Tourists</h3>
                    <ul>
                        <li>Book guides in advance during peak season</li>
                        <li>Wear appropriate clothing for activities</li>
                        <li>Bring enough water and snacks</li>
                        <li>Follow your guide's instructions</li>
                        <li>Respect local customs and environment</li>
                        <li>Bring sunscreen and insect repellent</li>
                    </ul>
                </div>
                
                <div class="info-card">
                    <h3>📧 Send Us a Message</h3>
                    <div class="support-form">
                        <div class="form-group">
                            <label>Your Name</label>
                            <input type="text" id="supportName" class="form-control" placeholder="Enter your name">
                        </div>
                        <div class="form-group">
                            <label>Email Address</label>
                            <input type="email" id="supportEmail" class="form-control" placeholder="your@email.com">
                        </div>
                        <div class="form-group">
                            <label>Message</label>
                            <textarea id="supportMessage" rows="4" class="form-control" placeholder="Type your question or concern here..."></textarea>
                        </div>
                        <button class="btn-hero" onclick="sendSupportMessage()">
                            <span class="material-icons-outlined">send</span>
                            Send Message
                        </button>
                    </div>
                </div>

                <div class="info-card">
                    <h3>🗺️ Quick Links</h3>
                    <ul>
                        <li><a href="index.php" class="link-primary">Browse Tour Guides</a></li>
                        <li><a href="tourist-spots.php" class="link-primary">Tourist Spots</a></li>
                        <li><a href="local-culture.php" class="link-primary">Local Culture</a></li>
                        <li><a href="travel-tips.php" class="link-primary">Travel Tips</a></li>
                        <li><a href="booking-history.php" class="link-primary">My Bookings</a></li>
                    </ul>
                </div>

                <div class="info-card">
                    <h3>📱 Follow Us</h3>
                    <ul>
                        <li>Facebook: SJDMTours</li>
                        <li>Instagram: sjdm_tours</li>
                        <li>Twitter: sjdmtours</li>
                        <li>TikTok: sjdmtours</li>
                        <li>YouTube: SJDM Tours Official</li>
                    </ul>
                </div>
            </div>
        </div>
    </main>

    <script src="script.js"></script>
    <script>
        window.addEventListener('DOMContentLoaded', function() {
            // updateUserInterface is defined in script.js to handle UI updates
            if (typeof updateUserInterface === 'function') {
                updateUserInterface();
            }
            loadUserContactInfo();
        });

        function loadUserContactInfo() {
            const user = JSON.parse(localStorage.getItem('currentUser'));
            if (user) {
                const supportName = document.getElementById('supportName');
                const supportEmail = document.getElementById('supportEmail');
                if (supportName) supportName.value = user.name || '';
                if (supportEmail) supportEmail.value = user.email || '';
            }
        }

        function sendSupportMessage() {
            const name = document.getElementById('supportName').value.trim();
            const email = document.getElementById('supportEmail').value.trim();
            const message = document.getElementById('supportMessage').value.trim();
            
            if (!name) {
                showNotification('Please enter your name', 'error');
                return;
            }

            if (!email) {
                showNotification('Please enter your email', 'error');
                return;
            }

            if (!message) {
                showNotification('Please enter a message', 'error');
                return;
            }

            // Email validation
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                showNotification('Please enter a valid email address', 'error');
                return;
            }
            
            // Save message to localStorage
            const messages = JSON.parse(localStorage.getItem('supportMessages') || '[]');
            messages.push({
                id: Date.now(),
                name: name,
                email: email,
                message: message,
                timestamp: new Date().toISOString(),
                status: 'sent'
            });
            localStorage.setItem('supportMessages', JSON.stringify(messages));
            
            showNotification('Message sent successfully! We\'ll respond within 24 hours.', 'success');
            
            // Clear message field
            const supportMessage = document.getElementById('supportMessage');
            if (supportMessage) supportMessage.value = '';
        }
    </script>
</body>
</html>