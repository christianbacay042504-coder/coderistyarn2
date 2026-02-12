<?php
// Include necessary files
require_once __DIR__ . '/config/auth.php';

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'login') {
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        
        try {
            // Validate input
            if (empty($email) || empty($password)) {
                echo json_encode(['success' => false, 'message' => 'Please enter both email and password']);
                exit();
            }
            
            // Attempt login
            $result = loginUser($email, $password);
            echo json_encode($result);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
        }
        exit();
    }
}

// Redirect if already logged in (but not if just logged out)
if (isLoggedIn() && !isset($_SESSION['logout_message'])) {
    if (isAdmin()) {
        header('Location: admin/dashboard.php');
    } elseif (isTourGuide()) {
        header('Location: tour-guide/dashboard.php');
    } else {
        header('Location: sjdm-user/index.php');
    }
    exit();
}

// Check for logout messages
$logoutMessage = '';
$logoutStatus = '';
if (isset($_SESSION['logout_message'])) {
    $logoutMessage = $_SESSION['logout_message'];
    $logoutStatus = $_SESSION['logout_status'] ?? 'success';
    
    // Clear the session messages after displaying
    unset($_SESSION['logout_message']);
    unset($_SESSION['logout_status']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | SJDM Tours</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700;900&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet">
    <link rel="stylesheet" href="log-in/styles.css">
    <script
      src="https://unpkg.com/@lottiefiles/dotlottie-wc@0.8.11/dist/dotlottie-wc.js"
      type="module"
    ></script>
</head>
<body>
    <div class="login-container">
        <!-- Left Panel - Branding & Info -->
        <div class="login-left-panel">
            <div class="login-brand">
                <div class="brand-logo">
                    <span class="material-icons-outlined">landscape</span>
                </div>
                <h1>SJDM TOURS</h1>
                <p class="brand-tagline">Discover the Balcony of Metropolis</p>
            </div>
            
            <div class="login-hero">
                <div class="hero-image">
                    <dotlottie-wc
                      src="https://lottie.host/104c69ad-6fe4-4dbf-926e-426ff5362ca5/EfPh5p1jDj.lottie"
                      style="width: 300px;height: 300px; display: block; margin: 0 auto;"
                      autoplay
                      loop
                    ></dotlottie-wc>
                </div>
                <h2>Welcome Back</h2>
                <p>Sign in to continue your journey and explore the beauty of San Jose del Monte.</p>
                
                <div class="features-list">
                    <div class="feature-item">
                        <span class="material-icons-outlined">verified_user</span>
                        <span>Secure & Trusted Platform</span>
                    </div>
                    <div class="feature-item">
                        <span class="material-icons-outlined">tour</span>
                        <span>Access 20+ Tour Guides</span>
                    </div>
                    <div class="feature-item">
                        <span class="material-icons-outlined">calendar_today</span>
                        <span>Easy Booking Management</span>
                    </div>
                </div>
            </div>
            
            <div class="login-footer">
                <p> 2024 SJDM Tours. All rights reserved.</p>
                <p>Discover the beauty of San Jose del Monte, Bulacan</p>
            </div>
        </div>
        
        <!-- Right Panel - Login Form -->
        <div class="login-right-panel">
            <div class="login-wrapper">
                <!-- Back to Home -->
                <a href="index.php" class="back-home">
                    <span class="material-icons-outlined">arrow_back</span>
                    Back to Home
                </a>
                
                <!-- Login Form -->
                <div class="login-form-container">
                    <div class="form-header">
                        <h2>Login to Your Account</h2>
                        <p>Welcome back! Please enter your credentials to continue.</p>
                    </div>
                    
                    <div id="alertMessage" class="alert" style="display: none;"></div>
                    
                    <?php if (!empty($logoutMessage)): ?>
                    <div id="logoutMessage" class="alert alert-<?php echo htmlspecialchars($logoutStatus); ?>" style="display: block;">
                        <?php echo htmlspecialchars($logoutMessage); ?>
                    </div>
                    <?php endif; ?>
                    
                    <form id="loginForm" class="auth-form">
                        <div class="form-group">
                            <label for="email">
                                <span class="material-icons-outlined">person</span>
                                Email Address
                            </label>
                            <input type="email" id="email" name="email" placeholder="Enter your email address" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="password">
                                <span class="material-icons-outlined">lock</span>
                                Password
                            </label>
                            <div class="password-input">
                                <input type="password" id="password" name="password" placeholder="Enter your password" required>
                                <button type="button" class="toggle-password" id="togglePassword">
                                    <span class="material-icons-outlined">visibility</span>
                                </button>
                            </div>
                        </div>
                        
                        <div class="form-options">
                            <label class="remember-me">
                                <input type="checkbox" id="remember">
                                <span>Remember me</span>
                            </label>
                            <a href="#" class="forgot-password">Forgot Password?</a>
                        </div>
                        
                        <button type="submit" class="login-btn" id="loginBtn">
                            <span>Login to Account</span>
                            <span class="material-icons-outlined">arrow_forward</span>
                        </button>
                        
                        <div class="social-login">
                            <p>Or continue with</p>
                            <div class="social-buttons">
                                <button type="button" class="social-btn google">
                                    <span class="material-icons-outlined">mail</span>
                                    Google
                                </button>
                                <button type="button" class="social-btn facebook">
                                    <span class="material-icons-outlined">facebook</span>
                                    Facebook
                                </button>
                            </div>
                        </div>
                        
                        <div class="register-link">
                            <p>Don't have an account? <a href="log-in/register-user.php">Register Now</a></p>
                        </div>
                    </form>
                </div>
                
                <!-- Terms Section -->
                <div class="terms-section">
                    <p>By logging in, you agree to our <a href="#">Terms of Service</a> and <a href="#">Privacy Policy</a></p>
                </div>
                
                <!-- Support Section -->
                <div class="support-section">
                    <div class="support-icon">
                        <span class="material-icons-outlined">support_agent</span>
                    </div>
                    <div class="support-info">
                        <p>Need assistance? Contact our support team</p>
                        <a href="mailto:support@sjdmtours.ph">support@sjdmtours.ph</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="log-in/log-in.js"></script>
</body>
</html>