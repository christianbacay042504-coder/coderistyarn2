<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Help & Support - SJDM Tours</title>
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
            <h1 id="pageTitle">Help & Support</h1>
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

                <h2 class="section-title">Help & Support</h2>
                <div class="info-cards">
                    <div class="info-card">
                        <h3>📞 Contact Information</h3>
                        <ul>
                            <li>Customer Support: +63 917 123 4567</li>
                            <li>Email: support@sjdmtours.ph</li>
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
                        <div style="padding: 16px 0;">
                            <div class="form-group">
                                <label>Your Name</label>
                                <input type="text" id="supportName" placeholder="Enter your name" style="width: 100%; padding: 12px; border: 1px solid var(--border); border-radius: 8px; font-family: inherit; margin-bottom: 12px;">
                            </div>
                            <div class="form-group">
                                <label>Email Address</label>
                                <input type="email" id="supportEmail" placeholder="your@email.com" style="width: 100%; padding: 12px; border: 1px solid var(--border); border-radius: 8px; font-family: inherit; margin-bottom: 12px;">
                            </div>
                            <div class="form-group">
                                <label>Message</label>
                                <textarea id="supportMessage" rows="4" placeholder="Type your question or concern here..." style="width: 100%; padding: 12px; border: 1px solid var(--border); border-radius: 8px; font-family: inherit; resize: vertical;"></textarea>
                            </div>
                            <button class="btn-hero" onclick="sendSupportMessage()" style="width: 100%; margin-top: 12px;">
                                <span class="material-icons-outlined">send</span>
                                Send Message
                            </button>
                        </div>
                    </div>

                    <div class="info-card">
                        <h3>🗺️ Quick Links</h3>
                        <ul>
                            <li><a href="index.php" style="color: var(--primary); text-decoration: none;">Browse Tour Guides</a></li>
                            <li><a href="index.php#spots" style="color: var(--primary); text-decoration: none;">Tourist Spots</a></li>
                            <li><a href="index.php#culture" style="color: var(--primary); text-decoration: none;">Local Culture</a></li>
                            <li><a href="index.php#tips" style="color: var(--primary); text-decoration: none;">Travel Tips</a></li>
                            <li><a href="booking-history.html" style="color: var(--primary); text-decoration: none;">My Bookings</a></li>
                        </ul>
                    </div>

                    <div class="info-card">
                        <h3>📱 Follow Us</h3>
                        <ul>
                            <li>Facebook: @SJDMTours</li>
                            <li>Instagram: @sjdm_tours</li>
                            <li>Twitter: @sjdmtours</li>
                            <li>TikTok: @sjdmtours</li>
                            <li>YouTube: SJDM Tours Official</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script src="script.js"></script>
    <script src="profile-dropdown.js"></script>
    <script>
        window.addEventListener('DOMContentLoaded', function() {
            initProfileDropdown();
            updateProfileUI();
            loadUserContactInfo();
        });

        function loadUserContactInfo() {
            const user = JSON.parse(localStorage.getItem('currentUser'));
            if (user) {
                document.getElementById('supportName').value = user.name || '';
                document.getElementById('supportEmail').value = user.email || '';
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
            
            // Clear form
            document.getElementById('supportMessage').value = '';
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