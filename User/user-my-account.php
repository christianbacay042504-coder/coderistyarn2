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
    <title>My Account - SJDM Tours</title>
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
            <h1>My Account</h1>
            <div class="search-bar">
                <span class="material-icons-outlined">search</span>
                <input type="text" placeholder="Search account settings...">
            </div>
            <div class="header-actions">
                
            </div>
        </header>

        <div class="content-area my-account-page">
            <!-- Page Header -->
            <div class="account-page-header">
                <div class="account-header-content">
                    <div class="account-avatar-section">
                        <div class="account-avatar-large">
                            <?php echo isset($currentUser) ? substr($currentUser['name'], 0, 1) : 'U'; ?>
                        </div>
                        <button class="btn-change-photo">
                            <span class="material-icons-outlined">camera_alt</span>
                            <span>Change Photo</span>
                        </button>
                    </div>
                    <div class="account-header-info">
                        <h2 class="account-username"><?php echo isset($currentUser) ? htmlspecialchars($currentUser['name']) : 'User Name'; ?></h2>
                        <p class="account-useremail"><?php echo isset($currentUser) ? htmlspecialchars($currentUser['email']) : 'user@example.com'; ?></p>
                        <div class="account-badges">
                            <span class="badge badge-verified">
                                <span class="material-icons-outlined">verified</span>
                                Verified Account
                            </span>
                            <span class="badge badge-member">
                                <span class="material-icons-outlined">stars</span>
                                Premium Member
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Account Stats -->
            <div class="account-stats-grid">
                <div class="stat-card">
                    <div class="stat-icon">
                        <span class="material-icons-outlined">event</span>
                    </div>
                    <div class="stat-content">
                        <div class="stat-value">12</div>
                        <div class="stat-label">Total Bookings</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">
                        <span class="material-icons-outlined">favorite</span>
                    </div>
                    <div class="stat-content">
                        <div class="stat-value">8</div>
                        <div class="stat-label">Saved Tours</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">
                        <span class="material-icons-outlined">star</span>
                    </div>
                    <div class="stat-content">
                        <div class="stat-value">4.8</div>
                        <div class="stat-label">Average Rating</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">
                        <span class="material-icons-outlined">calendar_today</span>
                    </div>
                    <div class="stat-content">
                        <div class="stat-value"><?php echo htmlspecialchars($currentWeekday); ?></div>
                        <div class="stat-label"><?php echo htmlspecialchars($currentDate); ?></div>
                    </div>
                </div>
            </div>

            <!-- Account Content -->
            <div class="account-content-layout">
                <!-- Profile Information Card -->
                <div class="account-section-card">
                    <div class="section-card-header">
                        <div class="section-card-title">
                            <span class="material-icons-outlined">person</span>
                            <h3>Profile Information</h3>
                        </div>
                        <p class="section-card-subtitle">Update your personal details and contact information</p>
                    </div>
                    <div class="section-card-body">
                        <form id="accountForm">
                            <div class="form-row">
                                <div class="form-field">
                                    <label for="accountFirstName">
                                        <span class="material-icons-outlined">badge</span>
                                        First Name
                                    </label>
                                    <input type="text" id="accountFirstName" placeholder="Enter your first name" value="<?php echo isset($currentUser) ? htmlspecialchars(explode(' ', $currentUser['name'])[0]) : 'User'; ?>">
                                </div>
                                <div class="form-field">
                                    <label for="accountLastName">
                                        <span class="material-icons-outlined">badge</span>
                                        Last Name
                                    </label>
                                    <input type="text" id="accountLastName" placeholder="Enter your last name" value="<?php echo isset($currentUser) && count(explode(' ', $currentUser['name'])) > 1 ? htmlspecialchars(explode(' ', $currentUser['name'])[1]) : 'Name'; ?>">
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-field">
                                    <label for="accountEmail">
                                        <span class="material-icons-outlined">email</span>
                                        Email Address
                                    </label>
                                    <input type="email" id="accountEmail" placeholder="Enter your email" value="<?php echo isset($currentUser) ? htmlspecialchars($currentUser['email']) : 'user@example.com'; ?>">
                                </div>
                                <div class="form-field">
                                    <label for="accountPhone">
                                        <span class="material-icons-outlined">phone</span>
                                        Phone Number
                                    </label>
                                    <input type="tel" id="accountPhone" placeholder="+63 XXX XXX XXXX" value="+63 912 345 6789">
                                </div>
                            </div>

                            <div class="form-field">
                                <label for="accountAddress">
                                    <span class="material-icons-outlined">location_on</span>
                                    Address
                                </label>
                                <textarea id="accountAddress" rows="3" placeholder="Enter your complete address">Quezon City, National Capital Region, PH</textarea>
                            </div>

                            <div class="form-field">
                                <label for="accountBio">
                                    <span class="material-icons-outlined">description</span>
                                    Bio
                                </label>
                                <textarea id="accountBio" rows="4" placeholder="Tell us about yourself...">Travel enthusiast exploring the beauty of San Jose del Monte and beyond.</textarea>
                            </div>

                            <div class="form-actions">
                                <button type="button" class="btn-secondary" onclick="resetForm()">
                                    <span class="material-icons-outlined">refresh</span>
                                    Reset
                                </button>
                                <button type="button" class="btn-primary" onclick="saveAccountInfo()">
                                    <span class="material-icons-outlined">save</span>
                                    Save Changes
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Security Settings Card -->
                <div class="account-section-card">
                    <div class="section-card-header">
                        <div class="section-card-title">
                            <span class="material-icons-outlined">security</span>
                            <h3>Security Settings</h3>
                        </div>
                        <p class="section-card-subtitle">Manage your password and security preferences</p>
                    </div>
                    <div class="section-card-body">
                        <form id="passwordForm">
                            <div class="form-field">
                                <label for="currentPassword">
                                    <span class="material-icons-outlined">lock</span>
                                    Current Password
                                </label>
                                <input type="password" id="currentPassword" placeholder="Enter your current password">
                            </div>

                            <div class="form-field">
                                <label for="newPassword">
                                    <span class="material-icons-outlined">lock_open</span>
                                    New Password
                                </label>
                                <input type="password" id="newPassword" placeholder="Enter new password (min. 6 characters)">
                                <div class="password-strength" id="passwordStrength"></div>
                            </div>

                            <div class="form-field">
                                <label for="confirmPassword">
                                    <span class="material-icons-outlined">lock_open</span>
                                    Confirm New Password
                                </label>
                                <input type="password" id="confirmPassword" placeholder="Re-enter new password">
                            </div>

                            <div class="security-info-box">
                                <span class="material-icons-outlined">info</span>
                                <div>
                                    <strong>Password Requirements:</strong>
                                    <ul>
                                        <li>At least 6 characters long</li>
                                        <li>Mix of letters and numbers recommended</li>
                                        <li>Avoid common passwords</li>
                                    </ul>
                                </div>
                            </div>

                            <div class="form-actions">
                                <button type="button" class="btn-primary" onclick="changePassword()">
                                    <span class="material-icons-outlined">vpn_key</span>
                                    Update Password
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Preferences Card -->
                <div class="account-section-card">
                    <div class="section-card-header">
                        <div class="section-card-title">
                            <span class="material-icons-outlined">tune</span>
                            <h3>Preferences</h3>
                        </div>
                        <p class="section-card-subtitle">Customize your experience</p>
                    </div>
                    <div class="section-card-body">
                        <div class="preference-item">
                            <div class="preference-info">
                                <span class="material-icons-outlined">notifications_active</span>
                                <div>
                                    <h4>Email Notifications</h4>
                                    <p>Receive updates about bookings and promotions</p>
                                </div>
                            </div>
                            <label class="toggle-switch">
                                <input type="checkbox" checked>
                                <span class="toggle-slider"></span>
                            </label>
                        </div>

                        <div class="preference-item">
                            <div class="preference-info">
                                <span class="material-icons-outlined">sms</span>
                                <div>
                                    <h4>SMS Notifications</h4>
                                    <p>Get text messages for important updates</p>
                                </div>
                            </div>
                            <label class="toggle-switch">
                                <input type="checkbox">
                                <span class="toggle-slider"></span>
                            </label>
                        </div>

                        <div class="preference-item">
                            <div class="preference-info">
                                <span class="material-icons-outlined">campaign</span>
                                <div>
                                    <h4>Marketing Communications</h4>
                                    <p>Receive special offers and travel tips</p>
                                </div>
                            </div>
                            <label class="toggle-switch">
                                <input type="checkbox" checked>
                                <span class="toggle-slider"></span>
                            </label>
                        </div>

                        <div class="preference-item">
                            <div class="preference-info">
                                <span class="material-icons-outlined">dark_mode</span>
                                <div>
                                    <h4>Dark Mode</h4>
                                    <p>Use dark theme for better visibility at night</p>
                                </div>
                            </div>
                            <label class="toggle-switch">
                                <input type="checkbox">
                                <span class="toggle-slider"></span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Danger Zone Card -->
                <div class="account-section-card danger-card">
                    <div class="section-card-header">
                        <div class="section-card-title">
                            <span class="material-icons-outlined">warning</span>
                            <h3>Danger Zone</h3>
                        </div>
                        <p class="section-card-subtitle">Irreversible actions - proceed with caution</p>
                    </div>
                    <div class="section-card-body">
                        <div class="danger-actions">
                            <div class="danger-action-item">
                                <div>
                                    <h4>Deactivate Account</h4>
                                    <p>Temporarily disable your account</p>
                                </div>
                                <button class="btn-danger-outline" onclick="deactivateAccount()">
                                    Deactivate
                                </button>
                            </div>
                            <div class="danger-action-item">
                                <div>
                                    <h4>Delete Account</h4>
                                    <p>Permanently delete your account and all data</p>
                                </div>
                                <button class="btn-danger" onclick="deleteAccount()">
                                    Delete Account
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script src="script.js"></script>
    <script>
        // Load account data
        window.addEventListener('DOMContentLoaded', function() {
            loadAccountData();
            // updateUserInterface is defined in script.js to handle UI updates
            if (typeof updateUserInterface === 'function') {
                updateUserInterface();
            }
            initPasswordStrength();
        });

        function loadAccountData() {
            const user = JSON.parse(localStorage.getItem('currentUser'));
            if (user) {
                const nameParts = (user.name || '').split(' ');
                document.getElementById('accountFirstName').value = nameParts[0] || '';
                document.getElementById('accountLastName').value = nameParts.slice(1).join(' ') || '';
                document.getElementById('accountEmail').value = user.email || '';
                document.getElementById('accountPhone').value = user.phone || '+63 912 345 6789';
                document.getElementById('accountAddress').value = user.address || 'Quezon City, National Capital Region, PH';
                document.getElementById('accountBio').value = user.bio || 'Travel enthusiast exploring the beauty of San Jose del Monte and beyond.';
            }
        }

        function saveAccountInfo() {
            let user = JSON.parse(localStorage.getItem('currentUser')) || {};
            
            const firstName = document.getElementById('accountFirstName').value.trim();
            const lastName = document.getElementById('accountLastName').value.trim();
            
            user.name = `${firstName} ${lastName}`.trim();
            user.email = document.getElementById('accountEmail').value;
            user.phone = document.getElementById('accountPhone').value;
            user.address = document.getElementById('accountAddress').value;
            user.bio = document.getElementById('accountBio').value;
            
            localStorage.setItem('currentUser', JSON.stringify(user));
            showNotification('Account information updated successfully!', 'success');
            
            // Update profile display using centralized function
            if (typeof updateUserInterface === 'function') {
                updateUserInterface();
            }
            
            // Update header info locally
            document.querySelector('.account-username').textContent = user.name;
            document.querySelector('.account-useremail').textContent = user.email;
        }

        function resetForm() {
            if (confirm('Are you sure you want to reset all changes?')) {
                loadAccountData();
                showNotification('Form reset to saved values', 'info');
            }
        }

        function initPasswordStrength() {
            const newPasswordInput = document.getElementById('newPassword');
            const strengthDiv = document.getElementById('passwordStrength');
            
            if (!newPasswordInput || !strengthDiv) return;

            newPasswordInput.addEventListener('input', function() {
                const password = this.value;
                let strength = 0;
                
                if (password.length >= 6) strength++;
                if (password.length >= 10) strength++;
                if (/[a-z]/.test(password) && /[A-Z]/.test(password)) strength++;
                if (/\d/.test(password)) strength++;
                if (/[^a-zA-Z0-9]/.test(password)) strength++;
                
                const strengthText = ['Very Weak', 'Weak', 'Fair', 'Good', 'Strong'];
                const strengthClass = ['very-weak', 'weak', 'fair', 'good', 'strong'];
                
                if (password.length > 0) {
                    strengthDiv.innerHTML = `
                        <div class="strength-bar ${strengthClass[strength - 1] || 'very-weak'}">
                            <div class="strength-fill" style="width: ${strength * 20}%"></div>
                        </div>
                        <div class="strength-text">${strengthText[strength - 1] || 'Very Weak'}</div>
                    `;
                } else {
                    strengthDiv.innerHTML = '';
                }
            });
        }

        function changePassword() {
            const current = document.getElementById('currentPassword').value;
            const newPass = document.getElementById('newPassword').value;
            const confirmVal = document.getElementById('confirmPassword').value;

            if (!current || !newPass || !confirmVal) {
                showNotification('Please fill all password fields', 'error');
                return;
            }

            if (newPass !== confirmVal) {
                showNotification('New passwords do not match', 'error');
                return;
            }

            if (newPass.length < 6) {
                showNotification('Password must be at least 6 characters', 'error');
                return;
            }

            // In real app, this would verify current password with server
            showNotification('Password updated successfully!', 'success');
            
            // Clear password fields
            document.getElementById('currentPassword').value = '';
            document.getElementById('newPassword').value = '';
            document.getElementById('confirmPassword').value = '';
            document.getElementById('passwordStrength').innerHTML = '';
        }

        function deactivateAccount() {
            if (confirm('Are you sure you want to deactivate your account? You can reactivate it later by logging in.')) {
                showNotification('Account deactivation feature coming soon', 'info');
            }
        }

        function deleteAccount() {
            const confirmation = prompt('This action cannot be undone. Type "DELETE" to confirm:');
            if (confirmation === 'DELETE') {
                if (confirm('Are you absolutely sure? All your data will be permanently deleted.')) {
                    showNotification('Account deletion feature coming soon', 'info');
                }
            }
        }
    </script>
</body>
</html>