<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Account - SJDM Tours</title>
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
            <h1 id="pageTitle">My Account</h1>
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

                <h2 class="section-title">My Account</h2>
                <div class="account-container">
                    <div class="account-card">
                        <h3>Profile Information</h3>
                        <form id="accountForm">
                            <div class="form-group">
                                <label>Full Name</label>
                                <input type="text" id="accountName" placeholder="Enter your name" value="User Name">
                            </div>
                            <div class="form-group">
                                <label>Email</label>
                                <input type="email" id="accountEmail" placeholder="Enter your email" value="user@example.com">
                            </div>
                            <div class="form-group">
                                <label>Phone Number</label>
                                <input type="tel" id="accountPhone" placeholder="Enter your phone" value="+63 912 345 6789">
                            </div>
                            <div class="form-group">
                                <label>Address</label>
                                <textarea id="accountAddress" rows="3" placeholder="Enter your address">Quezon City, National Capital Region, PH</textarea>
                            </div>
                            <button type="button" class="btn-submit" onclick="saveAccountInfo()">
                                <span class="material-icons-outlined">save</span>
                                Save Changes
                            </button>
                        </form>
                    </div>

                    <div class="account-card" style="margin-top: 24px;">
                        <h3>Change Password</h3>
                        <form id="passwordForm">
                            <div class="form-group">
                                <label>Current Password</label>
                                <input type="password" id="currentPassword" placeholder="Enter current password">
                            </div>
                            <div class="form-group">
                                <label>New Password</label>
                                <input type="password" id="newPassword" placeholder="Enter new password">
                            </div>
                            <div class="form-group">
                                <label>Confirm New Password</label>
                                <input type="password" id="confirmPassword" placeholder="Confirm new password">
                            </div>
                            <button type="button" class="btn-submit" onclick="changePassword()">
                                <span class="material-icons-outlined">lock</span>
                                Update Password
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script src="script.js"></script>
    <script src="profile-dropdown.js"></script>
    <script>
        // Load account data
        window.addEventListener('DOMContentLoaded', function() {
            loadAccountData();
            initProfileDropdown();
            updateProfileUI();
        });

        function loadAccountData() {
            const user = JSON.parse(localStorage.getItem('currentUser'));
            if (user) {
                document.getElementById('accountName').value = user.name || '';
                document.getElementById('accountEmail').value = user.email || '';
                document.getElementById('accountPhone').value = user.phone || '';
                document.getElementById('accountAddress').value = user.address || 'Quezon City, National Capital Region, PH';
            }
        }

        function saveAccountInfo() {
            let user = JSON.parse(localStorage.getItem('currentUser')) || {};
            
            user.name = document.getElementById('accountName').value;
            user.email = document.getElementById('accountEmail').value;
            user.phone = document.getElementById('accountPhone').value;
            user.address = document.getElementById('accountAddress').value;
            
            localStorage.setItem('currentUser', JSON.stringify(user));
            showNotification('Account information updated successfully!', 'success');
            
            // Update profile display
            updateProfileUI();
        }

        function changePassword() {
            const current = document.getElementById('currentPassword').value;
            const newPass = document.getElementById('newPassword').value;
            const confirm = document.getElementById('confirmPassword').value;

            if (!current || !newPass || !confirm) {
                showNotification('Please fill all password fields', 'error');
                return;
            }

            if (newPass !== confirm) {
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