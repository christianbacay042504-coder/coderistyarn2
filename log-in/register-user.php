<?php
require_once __DIR__ . '/../config/auth.php';

// Handle registration form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    
    $firstName = $_POST['firstName'] ?? '';
    $lastName = $_POST['lastName'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirmPassword'] ?? '';
    $userType = 'user';
    
    // Validation
    if (empty($firstName) || empty($lastName) || empty($email) || empty($password) || empty($confirmPassword)) {
        echo json_encode(['success' => false, 'message' => 'All fields are required']);
        exit();
    }
    
    if ($password !== $confirmPassword) {
        echo json_encode(['success' => false, 'message' => 'Passwords do not match']);
        exit();
    }
    
    if (strlen($password) < 6) {
        echo json_encode(['success' => false, 'message' => 'Password must be at least 6 characters long']);
        exit();
    }
    
    // Generate and send verification code
    $verificationCode = sprintf('%06d', mt_rand(100000, 999999));
    $_SESSION['verification_code'] = $verificationCode;
    $_SESSION['verification_email'] = $email;
    $_SESSION['verification_expires'] = time() + 600; // 10 minutes
    
    $result = registerUser($firstName, $lastName, $email, $password, $userType);
    
    echo json_encode($result);
    exit();
}

// Redirect if already logged in
if (isLoggedIn()) {
    if (isAdmin()) {
        header('Location: /coderistyarn2/admin/dashboard.php');
    } else {
        header('Location: /coderistyarn2/index.php');
    }
    exit();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register as User | SJDM Tours</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700;900&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
    <style>
        .verification-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 10000;
            justify-content: center;
            align-items: center;
        }
        
        .verification-content {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
            text-align: center;
            max-width: 400px;
            width: 90%;
        }
        
        .verification-code {
            font-size: 24px;
            font-weight: bold;
            color: #2c5282;
            margin: 20px 0;
            letter-spacing: 3px;
        }
        
        .verification-timer {
            font-size: 14px;
            color: #6c757d;
            margin: 10px 0;
        }
        
        .resend-btn {
            background: #6c757d;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            margin-top: 10px;
        }
        
        .resend-btn:hover {
            background: #5a6268;
        }
    </style>
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
                <p class="brand-tagline">Join Our Adventure Community</p>
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
                <h2>Start Your Journey</h2>
                <p>Create an account to unlock exclusive tours, personalized recommendations, and easy booking management.</p>
                
                <div class="features-list">
                    <div class="feature-item">
                        <span class="material-icons-outlined">star</span>
                        <span>Exclusive Tour Packages</span>
                    </div>
                    <div class="feature-item">
                        <span class="material-icons-outlined">discount</span>
                        <span>Member-only Discounts</span>
                    </div>
                    <div class="feature-item">
                        <span class="material-icons-outlined">history</span>
                        <span>Booking History Access</span>
                    </div>
                </div>
            </div>
            
            <div class="login-footer">
                <p> 2024 SJDM Tours. All rights reserved.</p>
                <p>Experience the best of San Jose del Monte</p>
            </div>
        </div>
        
        <!-- Right Panel - Register Form -->
        <div class="login-right-panel">
            <div class="login-wrapper">
                <!-- Back to Home -->
                <a href="/coderistyarn2/log-in.php" class="back-home">
                    <span class="material-icons-outlined">arrow_back</span>
                    Back to Home
                </a>
                
                <!-- Register Form -->
                <div class="login-form-container">
                    <div class="form-header">
                        <h2>Create Your Account</h2>
                        <p>Join thousands of travelers exploring San Jose del Monte</p>
                    </div>
                    
                    <div id="alertMessage" class="alert" style="display: none;"></div>
                    
                    <form id="registerForm" class="auth-form" method="POST" action="">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="firstName">
                                    <span class="material-icons-outlined">person</span>
                                    First Name
                                </label>
                                <input type="text" id="firstName" name="firstName" placeholder="Enter your first name" required>
                            </div>
                            <div class="form-group">
                                <label for="lastName">
                                    <span class="material-icons-outlined">person</span>
                                    Last Name
                                </label>
                                <input type="text" id="lastName" name="lastName" placeholder="Enter your last name" required>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="email">
                                <span class="material-icons-outlined">mail</span>
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
                                <input type="password" id="password" name="password" placeholder="Create a strong password" required>
                                <button type="button" class="toggle-password" id="toggleRegisterPassword">
                                    <span class="material-icons-outlined">visibility</span>
                                </button>
                            </div>
                            <div class="password-strength">
                                <div class="strength-meter">
                                    <div class="strength-bar" id="strengthBar"></div>
                                </div>
                                <span class="strength-text" id="strengthText">Password strength</span>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="confirmPassword">
                                <span class="material-icons-outlined">lock</span>
                                Confirm Password
                            </label>
                            <input type="password" id="confirmPassword" name="confirmPassword" placeholder="Confirm your password" required>
                        </div>
                        
                        <button type="submit" class="login-btn" id="createAccountBtn">
                            <span>Create Account</span>
                            <span class="material-icons-outlined">person_add</span>
                        </button>
                        
                        <div class="social-login">
                            <p>Or sign up with</p>
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
                            <p>Already have an account? <a href="../log-in.php">Sign In</a></p>
                            <p>Want to be a tour guide? <a href="register-guide.php">Register as Guide</a></p>
                        </div>
                    </form>
                </div>
                
                <!-- Terms Section -->
                <div class="terms-section">
                    <p>By registering, you agree to our <a href="#">Terms of Service</a>, <a href="#">Privacy Policy</a>, and <a href="#">Cookie Policy</a></p>
                </div>
                
                <!-- Support Section -->
                <div class="support-section">
                    <div class="support-icon">
                        <span class="material-icons-outlined">help</span>
                    </div>
                    <div class="support-info">
                        <p>Need help with registration?</p>
                        <a href="mailto:support@sjdmtours.ph">support@sjdmtours.ph</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    // Register page specific JavaScript
    document.addEventListener('DOMContentLoaded', function() {
        const registerForm = document.getElementById('registerForm');
        const togglePassword = document.getElementById('toggleRegisterPassword');
        const passwordInput = document.getElementById('password');
        const strengthBar = document.getElementById('strengthBar');
        const strengthText = document.getElementById('strengthText');
        const createAccountBtn = document.getElementById('createAccountBtn');
        
        // Toggle Password Visibility
        if (togglePassword) {
            togglePassword.addEventListener('click', function() {
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
                
                const icon = this.querySelector('.material-icons-outlined');
                icon.textContent = type === 'password' ? 'visibility' : 'visibility_off';
            });
        }
        
        // Password Strength Checker
        if (passwordInput && strengthBar && strengthText) {
            passwordInput.addEventListener('input', function() {
                const password = this.value;
                let strength = 0;
                
                if (password.length >= 8) strength += 25;
                if (/[a-z]/.test(password)) strength += 25;
                if (/[A-Z]/.test(password)) strength += 25;
                if (/[0-9!@#$%^&*]/.test(password)) strength += 25;
                
                strengthBar.style.width = `${strength}%`;
                
                if (strength < 50) {
                    strengthBar.style.backgroundColor = '#dc3545';
                    strengthText.textContent = 'Weak password';
                    strengthText.style.color = '#dc3545';
                } else if (strength < 75) {
                    strengthBar.style.backgroundColor = '#ffc107';
                    strengthText.textContent = 'Medium password';
                    strengthText.style.color = '#ffc107';
                } else {
                    strengthBar.style.backgroundColor = '#28a745';
                    strengthText.textContent = 'Strong password';
                    strengthText.style.color = '#28a745';
                }
            });
        }
        
        // Show alert message
        function showAlert(message, type) {
            const alertDiv = document.getElementById('alertMessage');
            alertDiv.textContent = message;
            alertDiv.className = 'alert ' + type;
            alertDiv.style.display = 'block';
            
            setTimeout(() => {
                alertDiv.style.display = 'none';
            }, 5000);
        }
        
        // Form submission
        registerForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const firstName = document.getElementById('firstName').value.trim();
            const lastName = document.getElementById('lastName').value.trim();
            const email = document.getElementById('email').value.trim();
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirmPassword').value;
            
            // Validation
            if (!firstName || !lastName || !email || !password || !confirmPassword) {
                showAlert('Please fill in all required fields', 'error');
                return;
            }
            
            if (password !== confirmPassword) {
                showAlert('Passwords do not match', 'error');
                return;
            }
            
            if (password.length < 6) {
                showAlert('Password must be at least 6 characters long', 'error');
                return;
            }
            
            // Show loading state
            const originalText = createAccountBtn.innerHTML;
            createAccountBtn.innerHTML = `
                <span>Creating Account...</span>
                <span class="material-icons-outlined">hourglass_empty</span>
            `;
            createAccountBtn.disabled = true;
            
            // Create FormData with all form fields
            const formData = new FormData(registerForm);
            formData.append('userType', 'user');
            
            // Send AJAX request
            fetch('register-user.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                createAccountBtn.innerHTML = originalText;
                createAccountBtn.disabled = false;
                
                if (data.success) {
                    showAlert('Account created successfully! Redirecting to preferences...', 'success');
                    setTimeout(() => {
                        window.location.href = '../User/user-preferences-page.php';
                    }, 1500);
                } else {
                    showAlert(data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('An error occurred. Please try again.', 'error');
                createAccountBtn.innerHTML = originalText;
                createAccountBtn.disabled = false;
            });
        });
    });
    </script>
</body>
</html>
