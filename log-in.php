<?php
require_once __DIR__ . '/config/auth.php';
require_once __DIR__ . '/config/smtp.php';
require_once __DIR__ . '/vendor/autoload.php';

function readEnvValue(string $key): string
{
    if (function_exists('apache_getenv')) {
        $apacheVal = apache_getenv($key, true);
        if (is_string($apacheVal) && $apacheVal !== '') {
            return $apacheVal;
        }
    }

    $val = getenv($key);
    if (is_string($val) && $val !== '') {
        return $val;
    }
    if (isset($_SERVER[$key]) && is_string($_SERVER[$key]) && $_SERVER[$key] !== '') {
        return (string) $_SERVER[$key];
    }
    if (isset($_ENV[$key]) && is_string($_ENV[$key]) && $_ENV[$key] !== '') {
        return (string) $_ENV[$key];
    }
    return '';
}

function sendLoginOtpEmail(string $toEmail, string $code): array
{
    $host = readEnvValue('SMTP_HOST');
    $username = readEnvValue('SMTP_USERNAME');
    $password = readEnvValue('SMTP_PASSWORD');
    $port = (int) (readEnvValue('SMTP_PORT') ?: 587);
    $secure = readEnvValue('SMTP_SECURE') ?: 'tls';
    $fromEmail = readEnvValue('SMTP_FROM_EMAIL') ?: $username;
    $fromName = readEnvValue('SMTP_FROM_NAME') ?: 'SJDM Tours';

    if (!$host || !$username || !$password || !$fromEmail) {
        $missing = [];
        if (!$host) $missing[] = 'SMTP_HOST';
        if (!$username) $missing[] = 'SMTP_USERNAME';
        if (!$password) $missing[] = 'SMTP_PASSWORD';
        if (!$fromEmail) $missing[] = 'SMTP_FROM_EMAIL';

        $msg = 'SMTP is not configured on this server';
        if (!empty($missing)) {
            $msg .= ' (missing: ' . implode(', ', $missing) . ')';
        }

        return ['success' => false, 'message' => $msg];
    }

    $mail = new PHPMailer\PHPMailer\PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host = $host;
        $mail->SMTPAuth = true;
        $mail->Username = $username;
        $mail->Password = $password;
        $mail->Port = $port;

        if ($secure) {
            $mail->SMTPSecure = $secure;
        }

        $mail->setFrom($fromEmail, $fromName);
        $mail->addAddress($toEmail);

        $mail->isHTML(true);
        $mail->Subject = 'Your SJDM Tours verification code';
        
        $mail->Body = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>SJDM Tours - Verification Code</title>
            <style>
                * {
                    margin: 0;
                    padding: 0;
                    box-sizing: border-box;
                }
                
                body {
                    font-family: "Inter", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
                    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                    padding: 20px;
                    color: #2d3748;
                }
                
                .email-wrapper {
                    max-width: 650px;
                    margin: 0 auto;
                    background: transparent;
                }
                
                .email-container {
                    background: rgba(255, 255, 255, 0.95);
                    backdrop-filter: blur(10px);
                    border-radius: 24px;
                    overflow: hidden;
                    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1), 0 0 0 1px rgba(255, 255, 255, 0.2);
                    border: 1px solid rgba(255, 255, 255, 0.3);
                }
                
                .header {
                    background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 50%, #d946ef 100%);
                    padding: 50px 40px;
                    text-align: center;
                    position: relative;
                    overflow: hidden;
                }
                
                .header::before {
                    content: "";
                    position: absolute;
                    top: -50%;
                    left: -50%;
                    width: 200%;
                    height: 200%;
                    background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
                    animation: float 6s ease-in-out infinite;
                }
                
                @keyframes float {
                    0%, 100% { transform: translateY(0px) rotate(0deg); }
                    50% { transform: translateY(-20px) rotate(180deg); }
                }
                
                .header-content {
                    position: relative;
                    z-index: 1;
                }
                
                .logo-icon {
                    font-size: 48px;
                    margin-bottom: 15px;
                    filter: drop-shadow(0 4px 8px rgba(0,0,0,0.2));
                }
                
                .header h1 {
                    color: white;
                    margin: 0;
                    font-size: 32px;
                    font-weight: 700;
                    letter-spacing: -0.5px;
                    text-shadow: 0 2px 4px rgba(0,0,0,0.1);
                }
                
                .tagline {
                    color: rgba(255, 255, 255, 0.9);
                    font-size: 16px;
                    margin-top: 8px;
                    font-weight: 400;
                }
                
                .content {
                    padding: 50px 40px;
                    background: white;
                }
                
                .welcome-section {
                    text-align: center;
                    margin-bottom: 40px;
                }
                
                .welcome-section h2 {
                    font-size: 28px;
                    font-weight: 700;
                    color: #1a202c;
                    margin-bottom: 12px;
                    background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
                    -webkit-background-clip: text;
                    -webkit-text-fill-color: transparent;
                    background-clip: text;
                }
                
                .welcome-section p {
                    font-size: 17px;
                    color: #4a5568;
                    line-height: 1.7;
                    max-width: 480px;
                    margin: 0 auto;
                }
                
                .verification-container {
                    background: linear-gradient(135deg, #f0f9ff 0%, #e0e7ff 100%);
                    border-radius: 20px;
                    padding: 40px;
                    margin: 40px 0;
                    position: relative;
                    border: 2px solid transparent;
                    background-clip: padding-box;
                    box-shadow: 0 10px 30px rgba(99, 102, 241, 0.1);
                }
                
                .verification-container::before {
                    content: "";
                    position: absolute;
                    top: 0;
                    left: 0;
                    right: 0;
                    bottom: 0;
                    border-radius: 20px;
                    padding: 2px;
                    background: linear-gradient(135deg, #6366f1, #8b5cf6, #d946ef);
                    -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
                    -webkit-mask-composite: xor;
                    mask-composite: exclude;
                    z-index: -1;
                }
                
                .code-title {
                    text-align: center;
                    font-size: 14px;
                    font-weight: 600;
                    color: #6366f1;
                    text-transform: uppercase;
                    letter-spacing: 2px;
                    margin-bottom: 20px;
                }
                
                .verification-code {
                    background: white;
                    color: #1a202c;
                    font-size: 42px;
                    font-weight: 800;
                    letter-spacing: 12px;
                    padding: 25px 40px;
                    border-radius: 16px;
                    display: inline-block;
                    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.08);
                    font-family: "SF Mono", "Monaco", "Inconsolata", "Roboto Mono", monospace;
                    position: relative;
                    border: 1px solid rgba(99, 102, 241, 0.1);
                    text-align: center;
                    width: 100%;
                    box-sizing: border-box;
                }
                
                .security-badge {
                    background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
                    border-radius: 12px;
                    padding: 20px;
                    margin: 30px 0;
                    display: flex;
                    align-items: center;
                    gap: 15px;
                    border: 1px solid rgba(251, 191, 36, 0.2);
                }
                
                .security-icon {
                    font-size: 24px;
                    flex-shrink: 0;
                }
                
                .security-text {
                    flex: 1;
                }
                
                .security-text h4 {
                    color: #92400e;
                    font-size: 16px;
                    font-weight: 600;
                    margin-bottom: 4px;
                }
                
                .security-text p {
                    color: #78350f;
                    font-size: 14px;
                    line-height: 1.5;
                    margin: 0;
                }
                
                .instructions-card {
                    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
                    border-radius: 16px;
                    padding: 30px;
                    margin: 30px 0;
                    border-left: 4px solid #6366f1;
                }
                
                .instructions-card h3 {
                    color: #1e293b;
                    font-size: 18px;
                    font-weight: 600;
                    margin-bottom: 16px;
                    display: flex;
                    align-items: center;
                    gap: 8px;
                }
                
                .step-list {
                    list-style: none;
                    padding: 0;
                }
                
                .step-list li {
                    display: flex;
                    align-items: flex-start;
                    margin-bottom: 12px;
                    font-size: 15px;
                    color: #475569;
                    line-height: 1.6;
                }
                
                .step-number {
                    background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
                    color: white;
                    width: 24px;
                    height: 24px;
                    border-radius: 50%;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    font-size: 12px;
                    font-weight: 600;
                    margin-right: 12px;
                    flex-shrink: 0;
                    margin-top: 2px;
                }
                
                .footer {
                    text-align: center;
                    padding: 30px 40px;
                    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
                    border-top: 1px solid rgba(0, 0, 0, 0.05);
                }
                
                .footer-brand {
                    font-size: 18px;
                    font-weight: 700;
                    color: #1e293b;
                    margin-bottom: 8px;
                }
                
                .footer-tagline {
                    color: #64748b;
                    font-size: 14px;
                    margin-bottom: 16px;
                }
                
                .footer-note {
                    color: #94a3b8;
                    font-size: 12px;
                    line-height: 1.5;
                }
                
                .help-text {
                    background: rgba(99, 102, 241, 0.05);
                    border-radius: 12px;
                    padding: 20px;
                    margin: 30px 0;
                    text-align: center;
                    border: 1px solid rgba(99, 102, 241, 0.1);
                }
                
                .help-text p {
                    color: #475569;
                    font-size: 14px;
                    line-height: 1.6;
                    margin: 0;
                }
                
                @media (max-width: 600px) {
                    body {
                        padding: 10px;
                    }
                    
                    .header, .content {
                        padding: 30px 25px;
                    }
                    
                    .verification-container {
                        padding: 30px 25px;
                    }
                    
                    .verification-code {
                        font-size: 32px;
                        letter-spacing: 8px;
                        padding: 20px 25px;
                    }
                }
            </style>
        </head>
        <body>
            <div class="email-wrapper">
                <div class="email-container">
                    <div class="header">
                        <div class="header-content">
                            <div class="logo-icon">🏔️</div>
                            <h1>SJDM Tours</h1>
                            <p class="tagline">Discover the Balcony of Metropolis</p>
                        </div>
                    </div>
                    
                    <div class="content">
                        <div class="welcome-section">
                            <h2>Verify Your Identity</h2>
                            <p>We need to confirm it\'s really you. Use the secure verification code below to complete your sign-in process.</p>
                        </div>
                        
                        <div class="verification-container">
                            <div class="code-title">🔐 Your Secure Verification Code</div>
                            <div class="verification-code">' . htmlspecialchars($code) . '</div>
                        </div>
                        
                        <div class="security-badge">
                            <div class="security-icon">⏰</div>
                            <div class="security-text">
                                <h4>Time-Sensitive Security</h4>
                                <p>This code expires in <strong>10 minutes</strong> for your protection. Never share this code with anyone.</p>
                            </div>
                        </div>
                        
                        <div class="instructions-card">
                            <h3>📋 Quick Steps to Verify</h3>
                            <ul class="step-list">
                                <li>
                                    <span class="step-number">1</span>
                                    <span>Return to the SJDM Tours login page</span>
                                </li>
                                <li>
                                    <span class="step-number">2</span>
                                    <span>Enter the 6-digit verification code shown above</span>
                                </li>
                                <li>
                                    <span class="step-number">3</span>
                                    <span>Click "Verify" to access your account</span>
                                </li>
                            </ul>
                        </div>
                        
                        <div class="help-text">
                            <p>❓ Didn\'t request this code? Please ignore this email or contact our support team if you have concerns.</p>
                        </div>
                    </div>
                    
                    <div class="footer">
                        <div class="footer-brand">SJDM Tours</div>
                        <div class="footer-tagline">Your Gateway to San Jose del Monte Adventures</div>
                        <div class="footer-note">
                            This is an automated security message. Please do not reply to this email.<br>
                            &copy; 2024 SJDM Tours. All rights reserved.
                        </div>
                    </div>
                </div>
            </div>
        </body>
        </html>';
        
        $mail->AltBody = 'Your SJDM Tours verification code is: ' . $code . ' (expires in 10 minutes).';

        $mail->send();
        return ['success' => true];
    } catch (Throwable $e) {
        return ['success' => false, 'message' => 'Failed to send verification email'];
    }
}

if (isset($_GET['debug_smtp']) && $_GET['debug_smtp'] === '1') {
    $remoteAddr = $_SERVER['REMOTE_ADDR'] ?? '';
    if ($remoteAddr !== '127.0.0.1' && $remoteAddr !== '::1') {
        http_response_code(403);
        echo 'Forbidden';
        exit();
    }

    header('Content-Type: application/json');
    echo json_encode([
        'SMTP_HOST' => readEnvValue('SMTP_HOST') !== '' ? 'SET' : 'MISSING',
        'SMTP_PORT' => readEnvValue('SMTP_PORT') !== '' ? 'SET' : 'MISSING',
        'SMTP_SECURE' => readEnvValue('SMTP_SECURE') !== '' ? 'SET' : 'MISSING',
        'SMTP_USERNAME' => readEnvValue('SMTP_USERNAME') !== '' ? 'SET' : 'MISSING',
        'SMTP_PASSWORD' => readEnvValue('SMTP_PASSWORD') !== '' ? 'SET' : 'MISSING',
        'SMTP_FROM_EMAIL' => readEnvValue('SMTP_FROM_EMAIL') !== '' ? 'SET' : 'MISSING',
        'SMTP_FROM_NAME' => readEnvValue('SMTP_FROM_NAME') !== '' ? 'SET' : 'MISSING'
    ]);
    exit();
}

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    
    try {
        $action = $_POST['action'] ?? 'login';

        if ($action === 'verify_code') {
            $code = preg_replace('/\D/', '', $_POST['code'] ?? '');

            if (strlen($code) !== 6) {
                echo json_encode(['success' => false, 'message' => 'Please enter the 6-digit verification code']);
                exit();
            }

            if (!isset($_SESSION['pending_login_user']) || !isset($_SESSION['pending_login_code']) || !isset($_SESSION['pending_login_expires'])) {
                echo json_encode(['success' => false, 'message' => 'No verification request found. Please login again.']);
                exit();
            }

            if (time() > (int) $_SESSION['pending_login_expires']) {
                echo json_encode(['success' => false, 'message' => 'Verification code expired. Please resend the code.']);
                exit();
            }

            if ($code !== (string) $_SESSION['pending_login_code']) {
                echo json_encode(['success' => false, 'message' => 'Invalid verification code']);
                exit();
            }

            $pending = $_SESSION['pending_login_user'];
            $_SESSION['user_id'] = $pending['id'];
            $_SESSION['first_name'] = $pending['first_name'];
            $_SESSION['last_name'] = $pending['last_name'];
            $_SESSION['email'] = $pending['email'];
            $_SESSION['user_type'] = $pending['user_type'];

            unset($_SESSION['pending_login_user'], $_SESSION['pending_login_code'], $_SESSION['pending_login_expires']);

            echo json_encode([
                'success' => true,
                'message' => 'Verification successful',
                'user_type' => $pending['user_type']
            ]);
            exit();
        }

        if ($action === 'resend_code') {
            if (!isset($_SESSION['pending_login_user'])) {
                echo json_encode(['success' => false, 'message' => 'No verification request found. Please login again.']);
                exit();
            }

            $verificationCode = sprintf('%06d', mt_rand(100000, 999999));
            $_SESSION['pending_login_code'] = $verificationCode;
            $_SESSION['pending_login_expires'] = time() + 600;

            $emailTo = (string) ($_SESSION['pending_login_user']['email'] ?? '');
            $sendRes = sendLoginOtpEmail($emailTo, $verificationCode);
            if (!$sendRes['success']) {
                echo json_encode(['success' => false, 'message' => $sendRes['message'] ?? 'Failed to send verification email']);
                exit();
            }

            echo json_encode([
                'success' => true,
                'message' => 'A new verification code has been sent to your email',
                'verification_required' => true,
                'email' => $_SESSION['pending_login_user']['email']
            ]);
            exit();
        }

        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        
        if (empty($email) || empty($password)) {
            echo json_encode(['success' => false, 'message' => 'Email and password are required']);
            exit();
        }

        $conn = getDatabaseConnection();
        if (!$conn) {
            echo json_encode(['success' => false, 'message' => 'Database connection failed']);
            exit();
        }

        $hasVerificationRequiredColumn = false;
        $colRes = $conn->query("SHOW COLUMNS FROM users LIKE 'verification_required'");
        if ($colRes && $colRes->num_rows > 0) {
            $hasVerificationRequiredColumn = true;
        }

        $stmt = $conn->prepare(
            $hasVerificationRequiredColumn
                ? "SELECT id, first_name, last_name, email, password, user_type, status, verification_required FROM users WHERE email = ?"
                : "SELECT id, first_name, last_name, email, password, user_type, status FROM users WHERE email = ?"
        );
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $userRes = $stmt->get_result();

        if ($userRes->num_rows === 0) {
            $stmt->close();
            closeDatabaseConnection($conn);
            echo json_encode(['success' => false, 'message' => 'Invalid email or password']);
            exit();
        }

        $user = $userRes->fetch_assoc();
        $stmt->close();
        closeDatabaseConnection($conn);

        if (($user['status'] ?? '') !== 'active') {
            echo json_encode(['success' => false, 'message' => 'Account is not active']);
            exit();
        }

        if (!password_verify($password, $user['password'])) {
            echo json_encode(['success' => false, 'message' => 'Invalid email or password']);
            exit();
        }

        // Check if verification is required (configurable based on user settings)
        $verificationRequired = false;
        
        // Check if user has verification_required column and value
        if (isset($user['verification_required']) && $user['verification_required'] === 1) {
            $verificationRequired = true;
        }
        
        // Check system-wide verification setting from environment or config
        $systemVerificationRequired = readEnvValue('REQUIRE_EMAIL_VERIFICATION') === 'true';
        if ($systemVerificationRequired) {
            $verificationRequired = true;
        }
        
        if ($verificationRequired) {
            $verificationCode = sprintf('%06d', mt_rand(100000, 999999));

            $_SESSION['pending_login_user'] = [
                'id' => (int) $user['id'],
                'first_name' => $user['first_name'],
                'last_name' => $user['last_name'],
                'email' => $user['email'],
                'user_type' => $user['user_type']
            ];
            $_SESSION['pending_login_code'] = $verificationCode;
            $_SESSION['pending_login_expires'] = time() + 600;

            $sendRes = sendLoginOtpEmail((string) $user['email'], $verificationCode);
            if (!$sendRes['success']) {
                echo json_encode(['success' => false, 'message' => $sendRes['message'] ?? 'Failed to send verification email']);
                exit();
            }

            echo json_encode([
                'success' => true,
                'verification_required' => true,
                'message' => 'Verification code sent to your email',
                'email' => $user['email']
            ]);
            exit();
        }

        $result = loginUser($email, $password);
        echo json_encode($result);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    }
    exit();
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

$pendingVerificationEmail = '';
if (isset($_SESSION['pending_login_user']) && is_array($_SESSION['pending_login_user'])) {
    $pendingVerificationEmail = $_SESSION['pending_login_user']['email'] ?? '';
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

    <div id="verificationModal" class="otp-modal<?php echo $pendingVerificationEmail ? ' show' : ''; ?>">
        <div class="otp-modal-backdrop" onclick="closeVerificationModal()"></div>
        <div class="otp-modal-card" role="dialog" aria-modal="true" aria-labelledby="otpTitle">
            <div class="otp-modal-icon">
                <span class="material-icons-outlined">mark_email_unread</span>
            </div>
            <h3 id="otpTitle" class="otp-modal-title">Please check your email</h3>
            <p class="otp-modal-subtitle">We've sent a code to <span id="otpEmail"><?php echo htmlspecialchars($pendingVerificationEmail); ?></span></p>

            <div class="otp-input-row" id="otpInputs">
                <input class="otp-input" type="text" inputmode="numeric" maxlength="1" aria-label="Digit 1">
                <input class="otp-input" type="text" inputmode="numeric" maxlength="1" aria-label="Digit 2">
                <input class="otp-input" type="text" inputmode="numeric" maxlength="1" aria-label="Digit 3">
                <input class="otp-input" type="text" inputmode="numeric" maxlength="1" aria-label="Digit 4">
                <input class="otp-input" type="text" inputmode="numeric" maxlength="1" aria-label="Digit 5">
                <input class="otp-input" type="text" inputmode="numeric" maxlength="1" aria-label="Digit 6">
            </div>

            <button type="button" class="otp-verify-btn" id="otpVerifyBtn" onclick="submitVerificationCode()">Verify</button>

            <p class="otp-resend">
                Didn't receive an email?
                <button type="button" class="otp-resend-btn" id="otpResendBtn" onclick="resendVerificationCode()">Resend</button>
            </p>
        </div>
    </div>

    <script src="log-in/log-in.js"></script>
</body>
</html>