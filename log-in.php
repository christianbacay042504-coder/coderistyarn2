<?php
// Prevent any output before JSON
error_reporting(0);
ini_set('display_errors', 0);

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
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Please enter both email and password']);
                exit();
            }
            
            // Attempt login
            $result = loginUser($email, $password);
            
            if ($result['success']) {
                // Generate OTP code
                $otpCode = generateOtpCode();
                
                // Store OTP in database
                $storeResult = storeOtpCode($result['user_id'] ?? getCurrentUserId(), $email, $otpCode, 'login');
                
                if ($storeResult['success']) {
                    // Send OTP email
                    $emailResult = sendLoginOtpEmail($email, $otpCode);
                    
                    if ($emailResult['success']) {
                        header('Content-Type: application/json');
                        echo json_encode([
                            'success' => true,
                            'verification_required' => true,
                            'message' => 'Please check your email for verification code',
                            'email' => $email
                        ]);
                    } else {
                        // Email failed but login was successful - set session and allow login
                        $_SESSION['user_id'] = $result['user_id'];
                        $_SESSION['first_name'] = $result['user_data']['first_name'];
                        $_SESSION['last_name'] = $result['user_data']['last_name'];
                        $_SESSION['email'] = $result['user_data']['email'];
                        $_SESSION['user_type'] = $result['user_type'];
                        
                        header('Content-Type: application/json');
                        echo json_encode([
                            'success' => true,
                            'verification_required' => false,
                            'message' => 'Login successful! (email notification failed)',
                            'user_type' => $result['user_type'],
                            'email_error' => $emailResult['message']
                        ]);
                    }
                } else {
                    // OTP storage failed - set session and allow login
                    $_SESSION['user_id'] = $result['user_id'];
                    $_SESSION['first_name'] = $result['user_data']['first_name'];
                    $_SESSION['last_name'] = $result['user_data']['last_name'];
                    $_SESSION['email'] = $result['user_data']['email'];
                    $_SESSION['user_type'] = $result['user_type'];
                    
                    header('Content-Type: application/json');
                    echo json_encode([
                        'success' => true,
                        'verification_required' => false,
                        'message' => 'Login successful!',
                        'user_type' => $result['user_type']
                    ]);
                }
            } else {
                header('Content-Type: application/json');
                echo json_encode($result);
            }
        } catch (Exception $e) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
        }
        exit();
    }
    
    // Handle OTP verification
    if ($_POST['action'] === 'verify_otp') {
        $email = $_POST['email'] ?? '';
        $code = $_POST['code'] ?? '';
        
        try {
            if (empty($email) || empty($code)) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Email and verification code are required']);
                exit();
            }
            
            // Verify OTP code
            $verifyResult = verifyOtpCode($email, $code, 'login');
            
            if ($verifyResult['success']) {
                // Get user details and complete login
                $conn = getDatabaseConnection();
                if ($conn) {
                    $stmt = $conn->prepare("SELECT id, first_name, last_name, email, user_type FROM users WHERE id = ?");
                    $stmt->bind_param("i", $verifyResult['user_id']);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    
                    if ($result->num_rows > 0) {
                        $user = $result->fetch_assoc();
                        $stmt->close();
                        closeDatabaseConnection($conn);
                        
                        // Set session variables
                        $_SESSION['user_id'] = $user['id'];
                        $_SESSION['first_name'] = $user['first_name'];
                        $_SESSION['last_name'] = $user['last_name'];
                        $_SESSION['email'] = $user['email'];
                        $_SESSION['user_type'] = $user['user_type'];
                        
                        header('Content-Type: application/json');
                        echo json_encode([
                            'success' => true,
                            'message' => 'Verification successful! Redirecting...',
                            'user_type' => $user['user_type']
                        ]);
                    } else {
                        header('Content-Type: application/json');
                        echo json_encode(['success' => false, 'message' => 'User not found']);
                    }
                } else {
                    header('Content-Type: application/json');
                    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
                }
            } else {
                header('Content-Type: application/json');
                echo json_encode($verifyResult);
            }
        } catch (Exception $e) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Verification error: ' . $e->getMessage()]);
        }
        exit();
    }
    
    // Handle OTP resend
    if ($_POST['action'] === 'resend_otp') {
        $email = $_POST['email'] ?? '';
        
        try {
            if (empty($email)) {
                echo json_encode(['success' => false, 'message' => 'Email is required']);
                exit();
            }
            
            // Get user by email
            $conn = getDatabaseConnection();
            if ($conn) {
                $stmt = $conn->prepare("SELECT id, first_name, last_name, email, user_type FROM users WHERE email = ?");
                $stmt->bind_param("s", $email);
                $stmt->execute();
                $result = $stmt->get_result();
                
                if ($result->num_rows > 0) {
                    $user = $result->fetch_assoc();
                    $stmt->close();
                    
                    // Generate new OTP code
                    $otpCode = generateOtpCode();
                    
                    // Store OTP in database
                    $storeResult = storeOtpCode($user['id'], $email, $otpCode, 'login');
                    
                    if ($storeResult['success']) {
                        // Send OTP email
                        $emailResult = sendLoginOtpEmail($email, $otpCode);
                        
                        if ($emailResult['success']) {
                            echo json_encode([
                                'success' => true,
                                'message' => 'Verification code resent successfully'
                            ]);
                        } else {
                            echo json_encode([
                                'success' => false,
                                'message' => 'Failed to resend verification code: ' . $emailResult['message']
                            ]);
                        }
                    } else {
                        echo json_encode([
                            'success' => false,
                            'message' => 'Failed to generate new verification code'
                        ]);
                    }
                } else {
                    echo json_encode(['success' => false, 'message' => 'Email not found']);
                }
                closeDatabaseConnection($conn);
            } else {
                echo json_encode(['success' => false, 'message' => 'Database connection failed']);
            }
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Resend error: ' . $e->getMessage()]);
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

    <!-- OTP Verification Modal -->
    <div id="verificationModal" class="verification-modal" style="display: none;">
        <div class="modal-backdrop" onclick="closeVerificationModal()"></div>
        <div class="modal-content">
            <div class="modal-header">
                <div class="modal-icon">
                    <span class="material-icons-outlined">security</span>
                </div>
                <h2>Verify Your Identity</h2>
                <p>We've sent a 6-digit verification code to your email address</p>
            </div>
            
            <div class="modal-body">
                <div class="email-display">
                    <span class="material-icons-outlined">email</span>
                    <span id="otpEmail"></span>
                </div>
                
                <div id="otpInputs" class="otp-inputs">
                    <input type="text" class="otp-input" maxlength="1" inputmode="numeric">
                    <input type="text" class="otp-input" maxlength="1" inputmode="numeric">
                    <input type="text" class="otp-input" maxlength="1" inputmode="numeric">
                    <input type="text" class="otp-input" maxlength="1" inputmode="numeric">
                    <input type="text" class="otp-input" maxlength="1" inputmode="numeric">
                    <input type="text" class="otp-input" maxlength="1" inputmode="numeric">
                </div>
                
                <div class="modal-actions">
                    <button type="button" id="otpVerifyBtn" class="btn btn-primary" onclick="submitVerificationCode()">
                        <span class="material-icons-outlined">check_circle</span>
                        Verify Code
                    </button>
                </div>
                
                <div class="modal-footer">
                    <p>Didn't receive the code?</p>
                    <button type="button" id="otpResendBtn" class="btn-link" onclick="resendVerificationCode()">
                        <span class="material-icons-outlined">refresh</span>
                        Resend Code
                    </button>
                </div>
            </div>
            
            <button type="button" class="modal-close" onclick="closeVerificationModal()">
                <span class="material-icons-outlined">close</span>
            </button>
        </div>
    </div>

    <script src="log-in/log-in.js"></script>
</body>
</html>