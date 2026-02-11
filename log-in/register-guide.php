<?php
require_once __DIR__ . '/../config/auth.php';

// Handle registration form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    
    $firstName = $_POST['firstName'] ?? '';
    $lastName = $_POST['lastName'] ?? '';
    $email = $_POST['email'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $specialization = $_POST['specialization'] ?? '';
    $userType = 'guide';
    
    // Validation
    if (empty($firstName) || empty($lastName) || empty($email) || empty($phone) || empty($specialization)) {
        echo json_encode(['success' => false, 'message' => 'All fields are required']);
        exit();
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'message' => 'Invalid email format']);
        exit();
    }
    
    // Handle file upload
    $resumePath = '';
    if (isset($_FILES['resume']) && $_FILES['resume']['error'] === UPLOAD_ERR_OK) {
        $resume = $_FILES['resume'];
        
        // Check file size (5MB limit)
        if ($resume['size'] > 5 * 1024 * 1024) {
            echo json_encode(['success' => false, 'message' => 'Resume file size must be less than 5MB']);
            exit();
        }
        
        // Check file type
        $allowedTypes = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
        if (!in_array($resume['type'], $allowedTypes)) {
            echo json_encode(['success' => false, 'message' => 'Resume must be PDF, DOC, or DOCX format']);
            exit();
        }
        
        // Create upload directory if it doesn't exist
        $uploadDir = __DIR__ . '/../uploads/resumes/';
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        // Generate unique filename
        $filename = uniqid() . '_' . basename($resume['name']);
        $resumePath = $uploadDir . $filename;
        
        // Create web-accessible URL
        $resumeUrl = 'http://localhost/coderistyarn2/uploads/resumes/' . $filename;
        
        if (!move_uploaded_file($resume['tmp_name'], $resumePath)) {
            echo json_encode(['success' => false, 'message' => 'Failed to upload resume']);
            exit();
        }
    }
    
    $result = registerUser($firstName, $lastName, $email, '', $userType, [
        'phone' => $phone,
        'specialization' => $specialization,
        'resume_path' => $resumePath
    ]);
    
    if ($result['success']) {
        // Also save to tour_guides table for admin management
        $guideResult = saveTourGuideApplication($firstName, $lastName, $email, $phone, $specialization, $resumePath);
        if (!$guideResult['success']) {
            error_log("Failed to save tour guide application: " . $guideResult['message']);
        }
    }
    
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
    <title>Register as Tour Guide | SJDM Tours</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700;900&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
    <style>
        /* Specialization Dropdown Styling */
        .form-group select {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            background: #ffffff;
            font-family: 'Inter', sans-serif;
            font-size: 14px;
            color: #1f2937;
            cursor: pointer;
            transition: all 0.3s ease;
            appearance: none;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e");
            background-position: right 12px center;
            background-repeat: no-repeat;
            background-size: 16px;
            padding-right: 40px;
        }
        
        .form-group select:hover {
            border-color: #3b82f6;
        }
        
        .form-group select:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }
        
        .form-group select option {
            padding: 12px 16px;
            background: #ffffff;
            color: #1f2937;
            font-family: 'Inter', sans-serif;
            font-size: 14px;
        }
        
        .form-group select option:hover {
            background: #f8fafc;
        }
        
        /* Fill Up Button Styling */
        .fill-up-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            width: 100%;
            padding: 12px 20px;
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-family: 'Inter', sans-serif;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            border: none;
            justify-content: center;
            margin-top: 8px;
        }
        
        .fill-up-btn:hover {
            background: linear-gradient(135deg, #059669 0%, #047857 100%);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
        }
        
        .fill-up-btn:active {
            transform: translateY(0);
            box-shadow: 0 2px 6px rgba(16, 185, 129, 0.2);
        }
        
        .fill-up-btn .material-icons-outlined {
            font-size: 18px;
        }
        
        /* File Input Styling */
        .file-input {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            background: #ffffff;
            font-family: 'Inter', sans-serif;
            font-size: 14px;
            color: #1f2937;
            transition: all 0.3s ease;
        }
        
        .file-input:hover {
            border-color: #3b82f6;
        }
        
        .file-input:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }
        
        .file-hint {
            display: block;
            margin-top: 8px;
            font-size: 12px;
            color: #64748b;
            line-height: 1.4;
        }
    </style>
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
                    <span class="material-icons-outlined">tour</span>
                </div>
                <h1>SJDM TOURS</h1>
                <p class="brand-tagline">Join Our Guide Community</p>
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
                <h2>Share Your Expertise</h2>
                <p>Become a certified tour guide and help travelers discover the beauty of San Jose del Monte.</p>
                
                <div class="features-list">
                    <div class="feature-item">
                        <span class="material-icons-outlined">verified</span>
                        <span>Professional Recognition</span>
                    </div>
                    <div class="feature-item">
                        <span class="material-icons-outlined">payments</span>
                        <span>Competitive Earnings</span>
                    </div>
                    <div class="feature-item">
                        <span class="material-icons-outlined">schedule</span>
                        <span>Flexible Schedule</span>
                    </div>
                </div>
            </div>
            
            <div class="login-footer">
                <p>© 2024 SJDM Tours. All rights reserved.</p>
                <p>Join our team of professional guides</p>
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
                        <h2>Apply as Tour Guide</h2>
                        <p>Join our team of professional tour guides</p>
                    </div>
                    
                    <div id="alertMessage" class="alert" style="display: none;"></div>
                    
                    <form id="registerForm" class="auth-form" method="POST" action="" enctype="multipart/form-data">
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
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="email">
                                    <span class="material-icons-outlined">mail</span>
                                    Email Address
                                </label>
                                <input type="email" id="email" name="email" placeholder="Enter your email address" required>
                            </div>
                            <div class="form-group">
                                <label for="phone">
                                    <span class="material-icons-outlined">phone</span>
                                    Phone Number
                                </label>
                                <input type="tel" id="phone" name="phone" placeholder="Enter your phone number" required>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="specialization">
                                <span class="material-icons-outlined">hiking</span>
                                Specialization
                            </label>
                            <select id="specialization" name="specialization" required>
                                <option value="">Select your specialization</option>
                                <option value="mountain">Mountain Tours</option>
                                <option value="waterfall">Waterfall Tours</option>
                                <option value="cultural">Cultural Tours</option>
                                <option value="adventure">Adventure Tours</option>
                                <option value="photography">Photography Tours</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="resume">
                                <span class="material-icons-outlined">upload_file</span>
                                Upload Resume/CV
                            </label>
                            <input type="file" id="resume" name="resume" accept=".pdf,.doc,.docx" class="file-input" required>
                            <small class="file-hint">Accepted formats: PDF, DOC, DOCX (Max 5MB)</small>
                        </div>
                        
                        <div class="form-group">
                            <a href="https://forms.gle/iNcnX87Bu72MKHTq5" target="_blank" class="fill-up-btn">
                                <span class="material-icons-outlined">description</span>
                                Fill Up Application
                            </a>
                        </div>
                        
                        <button type="submit" class="login-btn" id="submitApplicationBtn">
                            <span>Submit Application</span>
                            <span class="material-icons-outlined">send</span>
                        </button>
                        
                        <div class="register-link">
                            <p>Already have an account? <a href="../log-in.php">Sign In</a></p>
                            <p>Want to book tours? <a href="register-user.php">Register as User</a></p>
                        </div>
                    </form>
                </div>
                
                <!-- Terms Section -->
                <div class="terms-section">
                    <p>By applying, you agree to our <a href="#">Terms of Service</a>, <a href="#">Privacy Policy</a>, and <a href="#">Guide Agreement</a></p>
                </div>
                
                <!-- Support Section -->
                <div class="support-section">
                    <div class="support-icon">
                        <span class="material-icons-outlined">help</span>
                    </div>
                    <div class="support-info">
                        <p>Need help with your application?</p>
                        <a href="mailto:guides@sjdmtours.ph">guides@sjdmtours.ph</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    // Tour Guide registration JavaScript
    document.addEventListener('DOMContentLoaded', function() {
        const registerForm = document.getElementById('registerForm');
        const submitApplicationBtn = document.getElementById('submitApplicationBtn');
        
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
            const phone = document.getElementById('phone').value.trim();
            const specialization = document.getElementById('specialization').value;
            const resume = document.getElementById('resume').files[0];
            
            // Validation
            if (!firstName || !lastName || !email || !phone || !specialization) {
                showAlert('Please fill in all required fields', 'error');
                return;
            }
            
            if (!resume) {
                showAlert('Please upload your resume/CV', 'error');
                return;
            }
            
            // Check file size (5MB limit)
            if (resume.size > 5 * 1024 * 1024) {
                showAlert('Resume file size must be less than 5MB', 'error');
                return;
            }
            
            // Check file type
            const allowedTypes = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
            if (!allowedTypes.includes(resume.type)) {
                showAlert('Resume must be PDF, DOC, or DOCX format', 'error');
                return;
            }
            
            // Show loading state
            const originalText = submitApplicationBtn.innerHTML;
            submitApplicationBtn.innerHTML = `
                <span>Submitting Application...</span>
                <span class="material-icons-outlined">hourglass_empty</span>
            `;
            submitApplicationBtn.disabled = true;
            
            // Create FormData with all form fields
            const formData = new FormData(registerForm);
            formData.append('userType', 'guide');
            
            // Send AJAX request
            fetch('register-guide.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                submitApplicationBtn.innerHTML = originalText;
                submitApplicationBtn.disabled = false;
                
                if (data.success) {
                    showAlert('Application submitted successfully! Your application will be reviewed by the admin team. Redirecting...', 'success');
                    setTimeout(() => {
                        window.location.href = '../log-in.php';
                    }, 2000);
                } else {
                    showAlert(data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('An error occurred. Please try again.', 'error');
                submitApplicationBtn.innerHTML = originalText;
                submitApplicationBtn.disabled = false;
            });
        });
    });
    </script>
</body>
</html>
