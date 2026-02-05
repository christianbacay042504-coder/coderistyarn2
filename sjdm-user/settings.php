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
    <title>Settings - SJDM Tours</title>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
    <style>
        /* Toggle Switch Styles */
        .settings-group {
            display: flex;
            flex-direction: column;
            gap: 20px;
            margin-top: 20px;
        }

        .setting-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px;
            background: var(--bg-light);
            border-radius: var(--radius-md);
            transition: all 0.2s ease;
        }

        .setting-item:hover {
            background: var(--gray-100);
        }

        .setting-info strong {
            display: block;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 4px;
            font-size: 1em;
        }

        .setting-info p {
            margin: 0;
            color: var(--text-secondary);
            font-size: 0.9em;
        }

        .toggle-switch {
            position: relative;
            display: inline-block;
            width: 52px;
            height: 28px;
            flex-shrink: 0;
        }

        .toggle-switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .toggle-slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #d1d5db;
            transition: .3s;
            border-radius: 28px;
        }

        .toggle-slider:before {
            position: absolute;
            content: "";
            height: 20px;
            width: 20px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            transition: .3s;
            border-radius: 50%;
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }

        .toggle-switch input:checked + .toggle-slider {
            background-color: var(--primary);
        }

        .toggle-switch input:checked + .toggle-slider:before {
            transform: translateX(24px);
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
            <h1>Settings</h1>
            <div class="search-bar">
                <span class="material-icons-outlined">search</span>
                <input type="text" placeholder="Search settings...">
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
            <h2 class="section-title">Settings</h2>
            
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

            <div class="account-container">
                <div class="account-card">
                    <h3>Notification Preferences</h3>
                    <div class="settings-group">
                        <div class="setting-item">
                            <div class="setting-info">
                                <strong>Push Notifications</strong>
                                <p>Receive notifications about your bookings and updates</p>
                                    <p>Receive notifications about your bookings and updates</p>
                                </div>
                                <label class="toggle-switch">
                                    <input type="checkbox" id="notificationToggle" checked>
                                    <span class="toggle-slider"></span>
                                </label>
                            </div>
                            <div class="setting-item">
                                <div class="setting-info">
                                    <strong>Email Updates</strong>
                                    <p>Get tour recommendations and special offers via email</p>
                                </div>
                                <label class="toggle-switch">
                                    <input type="checkbox" id="emailToggle" checked>
                                    <span class="toggle-slider"></span>
                                </label>
                            </div>
                        </div>
                        
                        <h3 style="margin-top: 32px;">Privacy & Security</h3>
                        <div class="settings-group">
                            <div class="setting-item">
                                <div class="setting-info">
                                    <strong>Share Travel History</strong>
                                    <p>Allow guides to see your past bookings</p>
                                </div>
                                <label class="toggle-switch">
                                    <input type="checkbox" id="shareHistoryToggle">
                                    <span class="toggle-slider"></span>
                                </label>
                            </div>
                            <div class="setting-item">
                                <div class="setting-info">
                                    <strong>Public Profile</strong>
                                    <p>Make your reviews and ratings public</p>
                                </div>
                                <label class="toggle-switch">
                                    <input type="checkbox" id="publicProfileToggle">
                                    <span class="toggle-slider"></span>
                                </label>
                            </div>
                        </div>
                        
                        <button class="btn-submit" onclick="saveUserSettings()">
                            <span class="material-icons-outlined">save</span>
                            Save Settings
                        </button>
                    </div>

                    <div class="account-card" style="margin-top: 24px;">
                        <h3>Danger Zone</h3>
                        <div class="settings-group">
                            <div class="setting-item" style="background: #FEE2E2;">
                                <div class="setting-info">
                                    <strong style="color: #991B1B;">Delete Account</strong>
                                    <p style="color: #991B1B;">Permanently delete your account and all data</p>
                                </div>
                                <button class="btn-cancel" onclick="deleteAccount()">Delete</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script src="script.js"></script>
    <script src="profile-dropdown.js"></script>
    <script>
        window.addEventListener('DOMContentLoaded', function() {
            loadUserSettings();
            initProfileDropdown();
            updateProfileUI();
        });

        function loadUserSettings() {
            const user = JSON.parse(localStorage.getItem('currentUser'));
            if (!user || !user.preferences) return;
            
            document.getElementById('notificationToggle').checked = user.preferences.notifications !== false;
            document.getElementById('emailToggle').checked = user.preferences.emailUpdates !== false;
            document.getElementById('shareHistoryToggle').checked = user.preferences.shareHistory || false;
            document.getElementById('publicProfileToggle').checked = user.preferences.publicProfile || false;
        }

        function saveUserSettings() {
            let user = JSON.parse(localStorage.getItem('currentUser')) || {};
            
            if (!user.preferences) user.preferences = {};
            
            user.preferences.notifications = document.getElementById('notificationToggle').checked;
            user.preferences.emailUpdates = document.getElementById('emailToggle').checked;
            user.preferences.shareHistory = document.getElementById('shareHistoryToggle').checked;
            user.preferences.publicProfile = document.getElementById('publicProfileToggle').checked;
            
            localStorage.setItem('currentUser', JSON.stringify(user));
            showNotification('Settings saved successfully!', 'success');
        }

        function deleteAccount() {
            const confirm1 = confirm('Are you sure you want to delete your account? This action cannot be undone.');
            if (!confirm1) return;

            const confirm2 = confirm('This will permanently delete all your data including bookings, reviews, and favorites. Continue?');
            if (!confirm2) return;

            // Clear all user data
            localStorage.removeItem('currentUser');
            localStorage.removeItem('userBookings');
            localStorage.removeItem('favorites');
            localStorage.removeItem('reviews');

            showNotification('Account deleted successfully', 'info');
            setTimeout(() => {
                window.location.href = '/coderistyarn/landingpage/landingpage.php';
            }, 1500);
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