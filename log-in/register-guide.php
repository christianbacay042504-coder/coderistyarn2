<?php
require_once __DIR__ . '/../config/auth.php';

// Handle registration form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Forward to the new registration handler
    $_POST['userType'] = 'guide';
    
    // Include the new registration handler
    include_once __DIR__ . '/../save_registration_tour_guide.php';
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
        
        /* Form Section Styling */
        .form-section {
            margin-bottom: 32px;
            padding: 24px;
            background: #f8fafc;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
        }
        
        .section-title {
            font-family: 'Playfair Display', serif;
            font-size: 20px;
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 20px;
            padding-bottom: 8px;
            border-bottom: 2px solid #3b82f6;
        }
        
        /* Checkbox and Radio Group Styling */
        .checkbox-group, .radio-group {
            display: flex;
            flex-wrap: wrap;
            gap: 16px;
            margin-top: 8px;
        }
        
        .checkbox-item, .radio-item {
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            font-family: 'Inter', sans-serif;
            font-size: 14px;
            color: #374151;
            transition: color 0.2s ease;
        }
        
        .checkbox-item:hover, .radio-item:hover {
            color: #3b82f6;
        }
        
        .checkbox-item input[type="checkbox"],
        .radio-item input[type="radio"] {
            width: 18px;
            height: 18px;
            accent-color: #3b82f6;
            cursor: pointer;
        }
        
        /* Languages Container */
        .languages-container {
            margin-top: 8px;
        }
        
        .language-row {
            display: flex;
            gap: 12px;
            margin-bottom: 12px;
            align-items: center;
        }
        
        .language-select, .proficiency-select {
            flex: 1;
            padding: 10px 14px;
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
            background-position: right 10px center;
            background-repeat: no-repeat;
            background-size: 14px;
            padding-right: 35px;
        }
        
        .language-select:hover, .proficiency-select:hover {
            border-color: #3b82f6;
        }
        
        .language-select:focus, .proficiency-select:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }
        
        .add-btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 16px;
            background: #f0f9ff;
            color: #0369a1;
            border: 2px solid #0ea5e9;
            border-radius: 6px;
            font-family: 'Inter', sans-serif;
            font-size: 13px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .add-btn:hover {
            background: #0ea5e9;
            color: white;
            transform: translateY(-1px);
            box-shadow: 0 2px 8px rgba(14, 165, 233, 0.3);
        }
        
        .add-btn .material-icons-outlined {
            font-size: 16px;
        }
        
        /* Document Upload Grid */
        .document-upload-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
            margin-top: 12px;
        }
        
        /* Textarea Styling */
        textarea {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            background: #ffffff;
            font-family: 'Inter', sans-serif;
            font-size: 14px;
            color: #1f2937;
            resize: vertical;
            min-height: 80px;
            transition: all 0.3s ease;
        }
        
        textarea:hover {
            border-color: #3b82f6;
        }
        
        textarea:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }
        
        /* Form Row with 3 columns */
        .form-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 16px;
            margin-bottom: 16px;
        }
        
        .form-row .form-group {
            margin-bottom: 0;
        }
        
        /* Progress Steps Styling */
        .progress-container {
            margin-bottom: 32px;
        }
        
        .progress-steps {
            display: flex;
            justify-content: space-between;
            margin-bottom: 16px;
            position: relative;
        }
        
        .progress-steps::before {
            content: '';
            position: absolute;
            top: 20px;
            left: 0;
            right: 0;
            height: 2px;
            background: #e2e8f0;
            z-index: 1;
        }
        
        .step {
            display: flex;
            flex-direction: column;
            align-items: center;
            position: relative;
            z-index: 2;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .step-number {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #f1f5f9;
            border: 2px solid #cbd5e1;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            color: #64748b;
            margin-bottom: 8px;
            transition: all 0.3s ease;
        }
        
        .step.active .step-number {
            background: #3b82f6;
            border-color: #3b82f6;
            color: white;
            transform: scale(1.1);
        }
        
        .step.completed .step-number {
            background: #10b981;
            border-color: #10b981;
            color: white;
        }
        
        .step-title {
            font-size: 12px;
            font-weight: 500;
            color: #64748b;
            text-align: center;
            transition: color 0.3s ease;
        }
        
        .step.active .step-title {
            color: #3b82f6;
            font-weight: 600;
        }
        
        .step.completed .step-title {
            color: #10b981;
        }
        
        .progress-bar {
            height: 4px;
            background: #e2e8f0;
            border-radius: 2px;
            overflow: hidden;
        }
        
        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #3b82f6 0%, #10b981 100%);
            width: 25%;
            transition: width 0.3s ease;
        }
        
        /* Form Steps Styling */
        .form-step {
            display: none;
            animation: fadeIn 0.3s ease-in-out;
        }
        
        .form-step.active {
            display: block;
        }
        
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .form-step .step-title {
            font-family: 'Playfair Display', serif;
            font-size: 20px;
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 24px;
            padding-bottom: 8px;
            border-bottom: 2px solid #3b82f6;
        }
        
        /* Step Navigation Buttons */
        .step-navigation {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 32px;
            padding-top: 24px;
            border-top: 1px solid #e2e8f0;
        }
        
        .prev-btn, .next-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            font-family: 'Inter', sans-serif;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
        }
        
        .prev-btn {
            background: #f8fafc;
            color: #64748b;
            border: 2px solid #e2e8f0;
        }
        
        .prev-btn:hover {
            background: #e2e8f0;
            color: #374151;
            transform: translateY(-1px);
        }
        
        .next-btn {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            color: white;
            border: 2px solid #3b82f6;
        }
        
        .next-btn:hover {
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
        }
        
        .prev-btn .material-icons-outlined,
        .next-btn .material-icons-outlined {
            font-size: 18px;
        }
        
        .next-btn:disabled {
            background: #cbd5e1;
            border-color: #cbd5e1;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
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
                    
                    <!-- Progress Steps -->
                    <div class="progress-container">
                        <div class="progress-steps">
                            <div class="step active" data-step="1">
                                <div class="step-number">1</div>
                                <div class="step-title">Personal Info</div>
                            </div>
                            <div class="step" data-step="2">
                                <div class="step-number">2</div>
                                <div class="step-title">Professional</div>
                            </div>
                            <div class="step" data-step="3">
                                <div class="step-number">3</div>
                                <div class="step-title">Logistics</div>
                            </div>
                            <div class="step" data-step="4">
                                <div class="step-number">4</div>
                                <div class="step-title">Documents</div>
                            </div>
                        </div>
                        <div class="progress-bar">
                            <div class="progress-fill" id="progressFill"></div>
                        </div>
                    </div>
                    
                    <form id="registerForm" class="auth-form" method="POST" action="" enctype="multipart/form-data">
                        <!-- Step 1: Personal Information -->
                        <div class="form-step active" id="step1">
                            <h3 class="step-title">I. Personal Information</h3>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="lastName">
                                        <span class="material-icons-outlined">person</span>
                                        Last Name
                                    </label>
                                    <input type="text" id="lastName" name="lastName" placeholder="Enter your last name" required>
                                </div>
                                <div class="form-group">
                                    <label for="firstName">
                                        <span class="material-icons-outlined">person</span>
                                        First Name
                                    </label>
                                    <input type="text" id="firstName" name="firstName" placeholder="Enter your first name" required>
                                </div>
                                <div class="form-group">
                                    <label for="middleInitial">
                                        <span class="material-icons-outlined">person</span>
                                        Middle Initial
                                    </label>
                                    <input type="text" id="middleInitial" name="middleInitial" placeholder="Middle Initial" maxlength="2">
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="preferredName">
                                    <span class="material-icons-outlined">badge</span>
                                    Preferred Name/Alias
                                </label>
                                <input type="text" id="preferredName" name="preferredName" placeholder="Name you use during tours">
                            </div>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="dateOfBirth">
                                        <span class="material-icons-outlined">cake</span>
                                        Date of Birth
                                    </label>
                                    <input type="date" id="dateOfBirth" name="dateOfBirth" required>
                                </div>
                                <div class="form-group">
                                    <label for="gender">
                                        <span class="material-icons-outlined">wc</span>
                                        Gender
                                    </label>
                                    <select id="gender" name="gender" required>
                                        <option value="">Select Gender</option>
                                        <option value="male">Male</option>
                                        <option value="female">Female</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="homeAddress">
                                    <span class="material-icons-outlined">home</span>
                                    Complete Home Address
                                </label>
                                <textarea id="homeAddress" name="homeAddress" placeholder="Enter your complete home address" rows="3" required></textarea>
                            </div>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="primaryPhone">
                                        <span class="material-icons-outlined">phone</span>
                                        Primary Mobile Number
                                    </label>
                                    <input type="tel" id="primaryPhone" name="primaryPhone" placeholder="Primary mobile number" required>
                                </div>
                                <div class="form-group">
                                    <label for="secondaryPhone">
                                        <span class="material-icons-outlined">phone</span>
                                        Secondary Mobile Number
                                    </label>
                                    <input type="tel" id="secondaryPhone" name="secondaryPhone" placeholder="Secondary mobile number">
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="email">
                                    <span class="material-icons-outlined">mail</span>
                                    Email Address
                                </label>
                                <input type="email" id="email" name="email" placeholder="Enter your email address" required>
                            </div>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="emergencyContactName">
                                        <span class="material-icons-outlined">contact_emergency</span>
                                        Emergency Contact Name
                                    </label>
                                    <input type="text" id="emergencyContactName" name="emergencyContactName" placeholder="Emergency contact name" required>
                                </div>
                                <div class="form-group">
                                    <label for="emergencyContactRelationship">
                                        <span class="material-icons-outlined">people</span>
                                        Relationship
                                    </label>
                                    <input type="text" id="emergencyContactRelationship" name="emergencyContactRelationship" placeholder="Relationship to you" required>
                                </div>
                                <div class="form-group">
                                    <label for="emergencyContactPhone">
                                        <span class="material-icons-outlined">phone</span>
                                        Emergency Contact Phone
                                    </label>
                                    <input type="tel" id="emergencyContactPhone" name="emergencyContactPhone" placeholder="Emergency contact phone" required>
                                </div>
                            </div>
                            
                            <div class="step-navigation">
                                <button type="button" class="next-btn" onclick="nextStep(1)">
                                    <span>Next Step</span>
                                    <span class="material-icons-outlined">arrow_forward</span>
                                </button>
                            </div>
                        </div>
                        
                        <!-- Step 2: Professional Qualifications -->
                        <div class="form-step" id="step2">
                            <h3 class="step-title">II. Professional Qualifications</h3>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="dotAccreditation">
                                        <span class="material-icons-outlined">verified</span>
                                        DOT Accreditation Number
                                    </label>
                                    <input type="text" id="dotAccreditation" name="dotAccreditation" placeholder="Department of Tourism License Number" required>
                                </div>
                                <div class="form-group">
                                    <label for="accreditationExpiry">
                                        <span class="material-icons-outlined">event</span>
                                        Accreditation Expiry Date
                                    </label>
                                    <input type="date" id="accreditationExpiry" name="accreditationExpiry" required>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="languages">
                                    <span class="material-icons-outlined">language</span>
                                    Languages Spoken
                                </label>
                                <div class="languages-container">
                                    <div class="language-row">
                                        <select name="languages[]" class="language-select">
                                            <option value="">Select Language</option>
                                            <option value="filipino">Filipino</option>
                                            <option value="english">English</option>
                                            <option value="mandarin">Mandarin</option>
                                            <option value="korean">Korean</option>
                                            <option value="japanese">Japanese</option>
                                            <option value="spanish">Spanish</option>
                                            <option value="french">French</option>
                                            <option value="other">Other</option>
                                        </select>
                                        <select name="languageProficiency[]" class="proficiency-select">
                                            <option value="">Proficiency</option>
                                            <option value="native">Native</option>
                                            <option value="fluent">Fluent</option>
                                            <option value="conversational">Conversational</option>
                                        </select>
                                    </div>
                                    <button type="button" id="addLanguageBtn" class="add-btn">
                                        <span class="material-icons-outlined">add</span>
                                        Add Language
                                    </button>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="specialization">
                                    <span class="material-icons-outlined">hiking</span>
                                    Specialization/Expertise
                                </label>
                                <select id="specialization" name="specialization" required>
                                    <option value="">Select your primary specialization</option>
                                    <option value="mountain">Mountain Tours</option>
                                    <option value="waterfall">Waterfall Tours</option>
                                    <option value="cultural">Cultural Tours</option>
                                    <option value="adventure">Adventure Tours</option>
                                    <option value="photography">Photography Tours</option>
                                </select>
                            </div>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="yearsExperience">
                                        <span class="material-icons-outlined">work_history</span>
                                        Years of Experience
                                    </label>
                                    <input type="number" id="yearsExperience" name="yearsExperience" placeholder="Total years active in the industry" min="0" required>
                                </div>
                                <div class="form-group">
                                    <label for="firstAidCertified">
                                        <span class="material-icons-outlined">medical_services</span>
                                        First Aid/CPR Certified
                                    </label>
                                    <select id="firstAidCertified" name="firstAidCertified" required>
                                        <option value="">Select Option</option>
                                        <option value="yes">Yes</option>
                                        <option value="no">No</option>
                                    </select>
                                </div>
                                <div class="form-group" id="firstAidExpiryGroup" style="display: none;">
                                    <label for="firstAidExpiry">
                                        <span class="material-icons-outlined">event</span>
                                        First Aid/CPR Expiry Date
                                    </label>
                                    <input type="date" id="firstAidExpiry" name="firstAidExpiry">
                                </div>
                            </div>
                            
                            <div class="step-navigation">
                                <button type="button" class="prev-btn" onclick="prevStep(2)">
                                    <span class="material-icons-outlined">arrow_back</span>
                                    <span>Previous</span>
                                </button>
                                <button type="button" class="next-btn" onclick="nextStep(2)">
                                    <span>Next Step</span>
                                    <span class="material-icons-outlined">arrow_forward</span>
                                </button>
                            </div>
                        </div>
                        
                        <!-- Step 3: Logistics & Availability -->
                        <div class="form-step" id="step3">
                            <h3 class="step-title">III. Logistics & Availability</h3>
                            
                            <div class="form-group">
                                <label for="baseLocation">
                                    <span class="material-icons-outlined">location_on</span>
                                    Primary Base Location
                                </label>
                                <input type="text" id="baseLocation" name="baseLocation" placeholder="Current city of residence" required>
                            </div>
                            
                            <div class="form-group">
                                <label>Employment Type</label>
                                <div class="radio-group">
                                    <label class="radio-item">
                                        <input type="radio" name="employmentType" value="full-time" required>
                                        <span>Full-time</span>
                                    </label>
                                    <label class="radio-item">
                                        <input type="radio" name="employmentType" value="part-time" required>
                                        <span>Part-time / Freelance / On-call</span>
                                    </label>
                                    <label class="radio-item">
                                        <input type="radio" name="employmentType" value="weekends" required>
                                        <span>Weekends & Holidays only</span>
                                    </label>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label>Do you own a vehicle for guest transport?</label>
                                <div class="radio-group">
                                    <label class="radio-item">
                                        <input type="radio" name="hasVehicle" value="yes" required>
                                        <span>Yes</span>
                                    </label>
                                    <label class="radio-item">
                                        <input type="radio" name="hasVehicle" value="no" required>
                                        <span>No</span>
                                    </label>
                                </div>
                            </div>
                            
                            <div class="step-navigation">
                                <button type="button" class="prev-btn" onclick="prevStep(3)">
                                    <span class="material-icons-outlined">arrow_back</span>
                                    <span>Previous</span>
                                </button>
                                <button type="button" class="next-btn" onclick="nextStep(3)">
                                    <span>Next Step</span>
                                    <span class="material-icons-outlined">arrow_forward</span>
                                </button>
                            </div>
                        </div>
                        
                        <!-- Step 4: Required Document Checklist -->
                        <div class="form-step" id="step4">
                            <h3 class="step-title">IV. Required Document Checklist</h3>
                            
                            <div class="document-upload-grid">
                                <div class="form-group">
                                    <label for="resume">
                                        <span class="material-icons-outlined">upload_file</span>
                                        Updated Resume / CV
                                    </label>
                                    <input type="file" id="resume" name="resume" accept=".pdf,.doc,.docx" class="file-input" required>
                                    <small class="file-hint">Accepted formats: PDF, DOC, DOCX (Max 5MB)</small>
                                </div>
                                
                                <div class="form-group">
                                    <label for="dotId">
                                        <span class="material-icons-outlined">badge</span>
                                        DOT ID (Front and Back)
                                    </label>
                                    <input type="file" id="dotId" name="dotId" accept=".pdf,.jpg,.jpeg,.png" class="file-input" required>
                                    <small class="file-hint">Accepted formats: PDF, JPG, PNG (Max 5MB)</small>
                                </div>
                                
                                <div class="form-group">
                                    <label for="governmentId">
                                        <span class="material-icons-outlined">card_membership</span>
                                        Valid Government-Issued ID
                                    </label>
                                    <input type="file" id="governmentId" name="governmentId" accept=".pdf,.jpg,.jpeg,.png" class="file-input" required>
                                    <small class="file-hint">Passport, Driver's License, UMID, etc. (Max 5MB)</small>
                                </div>
                                
                                <div class="form-group">
                                    <label for="nbiClearance">
                                        <span class="material-icons-outlined">gavel</span>
                                        NBI Clearance
                                    </label>
                                    <input type="file" id="nbiClearance" name="nbiClearance" accept=".pdf,.jpg,.jpeg,.png" class="file-input" required>
                                    <small class="file-hint">Issued within the last 6 months (Max 5MB)</small>
                                </div>
                                
                                <div class="form-group">
                                    <label for="firstAidCertificate">
                                        <span class="material-icons-outlined">medical_services</span>
                                        First Aid & CPR Training Certificate
                                    </label>
                                    <input type="file" id="firstAidCertificate" name="firstAidCertificate" accept=".pdf,.jpg,.jpeg,.png" class="file-input">
                                    <small class="file-hint">Required if certified (Max 5MB)</small>
                                </div>
                                
                                <div class="form-group">
                                    <label for="idPhoto">
                                        <span class="material-icons-outlined">photo_camera</span>
                                        Recent 2x2 ID Photo
                                    </label>
                                    <input type="file" id="idPhoto" name="idPhoto" accept=".jpg,.jpeg,.png" class="file-input" required>
                                    <small class="file-hint">Professional attire, white background (Max 2MB)</small>
                                </div>
                            </div>
                            
                            <div class="step-navigation">
                                <button type="button" class="prev-btn" onclick="prevStep(4)">
                                    <span class="material-icons-outlined">arrow_back</span>
                                    <span>Previous</span>
                                </button>
                                <button type="submit" class="login-btn" id="submitApplicationBtn">
                                    <span>Submit Application</span>
                                    <span class="material-icons-outlined">send</span>
                                </button>
                            </div>
                        </div>
                    </form>
                        
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
        const firstAidCertified = document.getElementById('firstAidCertified');
        const firstAidExpiryGroup = document.getElementById('firstAidExpiryGroup');
        const addLanguageBtn = document.getElementById('addLanguageBtn');
        const languagesContainer = document.querySelector('.languages-container');
        
        let currentStep = 1;
        const totalSteps = 4;
        
        // Show/hide First Aid expiry date based on certification status
        firstAidCertified.addEventListener('change', function() {
            if (this.value === 'yes') {
                firstAidExpiryGroup.style.display = 'block';
                document.getElementById('firstAidExpiry').required = true;
            } else {
                firstAidExpiryGroup.style.display = 'none';
                document.getElementById('firstAidExpiry').required = false;
                document.getElementById('firstAidExpiry').value = '';
            }
        });
        
        // Add language functionality
        let languageRowCount = 1;
        addLanguageBtn.addEventListener('click', function() {
            languageRowCount++;
            const newLanguageRow = document.createElement('div');
            newLanguageRow.className = 'language-row';
            newLanguageRow.innerHTML = `
                <select name="languages[]" class="language-select" required>
                    <option value="">Select Language</option>
                    <option value="filipino">Filipino</option>
                    <option value="english">English</option>
                    <option value="mandarin">Mandarin</option>
                    <option value="korean">Korean</option>
                    <option value="japanese">Japanese</option>
                    <option value="spanish">Spanish</option>
                    <option value="french">French</option>
                    <option value="other">Other</option>
                </select>
                <select name="languageProficiency[]" class="proficiency-select" required>
                    <option value="">Proficiency</option>
                    <option value="native">Native</option>
                    <option value="fluent">Fluent</option>
                    <option value="conversational">Conversational</option>
                </select>
                <button type="button" class="remove-btn" onclick="this.parentElement.remove()">
                    <span class="material-icons-outlined">remove</span>
                </button>
            `;
            languagesContainer.insertBefore(newLanguageRow, addLanguageBtn);
        });
        
        // Step navigation functions
        function updateProgress() {
            const progressFill = document.getElementById('progressFill');
            const progressPercentage = (currentStep / totalSteps) * 100;
            progressFill.style.width = progressPercentage + '%';
            
            // Update step indicators
            document.querySelectorAll('.step').forEach((step, index) => {
                const stepNumber = index + 1;
                step.classList.remove('active', 'completed');
                
                if (stepNumber === currentStep) {
                    step.classList.add('active');
                } else if (stepNumber < currentStep) {
                    step.classList.add('completed');
                }
            });
        }
        
        function showStep(stepNumber) {
            // Hide all steps
            document.querySelectorAll('.form-step').forEach(step => {
                step.classList.remove('active');
            });
            
            // Show current step
            document.getElementById('step' + stepNumber).classList.add('active');
            
            // Update progress
            updateProgress();
            
            // Scroll to top of form
            document.querySelector('.login-form-container').scrollIntoView({ behavior: 'smooth' });
        }
        
        // Make functions globally accessible
        window.nextStep = function(stepNumber) {
            // Validate current step before proceeding
            if (validateStep(stepNumber)) {
                currentStep = stepNumber + 1;
                showStep(currentStep);
            }
        };
        
        window.prevStep = function(stepNumber) {
            currentStep = stepNumber - 1;
            showStep(currentStep);
        };
        
        function validateStep(stepNumber) {
            const currentStepElement = document.getElementById('step' + stepNumber);
            const requiredFields = currentStepElement.querySelectorAll('[required]');
            let isValid = true;
            let firstInvalidField = null;
            
            // Check required fields
            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    isValid = false;
                    if (!firstInvalidField) {
                        firstInvalidField = field;
                    }
                }
            });
            
            // Special validations for specific steps
            if (stepNumber === 2) {
                // Check if specialization is selected
                const specialization = currentStepElement.querySelector('#specialization');
                if (!specialization.value) {
                    showAlert('Please select a specialization', 'error');
                    return false;
                }
                
                // Check if at least one language is selected
                const languages = currentStepElement.querySelectorAll('select[name="languages[]"]');
                let hasLanguage = false;
                languages.forEach(lang => {
                    if (lang.value) {
                        hasLanguage = true;
                    }
                });
                if (!hasLanguage) {
                    showAlert('Please select at least one language', 'error');
                    return false;
                }
            }
            
            if (!isValid) {
                showAlert('Please fill in all required fields', 'error');
                if (firstInvalidField) {
                    firstInvalidField.focus();
                }
                return false;
            }
            
            return true;
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
            
            // Validate final step
            if (!validateStep(4)) {
                return;
            }
            
            // Get all required fields from entire form
            const requiredFields = registerForm.querySelectorAll('[required]');
            let isValid = true;
            let firstInvalidField = null;
            
            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    isValid = false;
                    if (!firstInvalidField) {
                        firstInvalidField = field;
                    }
                }
            });
            
            // Check if specialization is selected
            const specialization = registerForm.querySelector('#specialization');
            if (!specialization.value) {
                isValid = false;
                showAlert('Please select a specialization', 'error');
                return;
            }
            
            // Check if at least one language is selected
            const languages = registerForm.querySelectorAll('select[name="languages[]"]');
            let hasLanguage = false;
            languages.forEach(lang => {
                if (lang.value) {
                    hasLanguage = true;
                }
            });
            if (!hasLanguage) {
                isValid = false;
                showAlert('Please select at least one language', 'error');
                return;
            }
            
            if (!isValid) {
                showAlert('Please fill in all required fields', 'error');
                if (firstInvalidField) {
                    firstInvalidField.focus();
                }
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
            
            // Debug: Log the specialization value
            const specializationValue = formData.get('specialization');
            console.log('Specialization value being sent:', specializationValue);
            console.log('FormData entries:');
            for (let [key, value] of formData.entries()) {
                console.log(key + ':', value);
            }
            
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
        
        // Initialize progress
        updateProgress();
    });
    
    // Add remove button styling
    const style = document.createElement('style');
    style.textContent = `
        .remove-btn {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 8px 12px;
            background: #fef2f2;
            color: #dc2626;
            border: 2px solid #f87171;
            border-radius: 6px;
            font-family: 'Inter', sans-serif;
            font-size: 13px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .remove-btn:hover {
            background: #dc2626;
            color: white;
            transform: translateY(-1px);
            box-shadow: 0 2px 8px rgba(220, 38, 38, 0.3);
        }
        
        .remove-btn .material-icons-outlined {
            font-size: 16px;
        }
    `;
    document.head.appendChild(style);
    </script>
</body>
</html>
