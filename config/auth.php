<?php

// Authentication Helper Functions

// Created: January 30, 2026

use PHPMailer\PHPMailer\PHPMailer;

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../config/database.php';






// Check if user is logged in



function isLoggedIn() {



    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);



}







// Check if user is admin



function isAdmin() {



    return isLoggedIn() && isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'admin';



}







// Check if user is tour guide



function isTourGuide() {



    return isLoggedIn() && isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'tour_guide';



}







// Get current user ID



function getCurrentUserId() {



    return isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;



}







// Get current user data



function getCurrentUser() {



    if (!isLoggedIn()) {



        return null;



    }



    



    $conn = getDatabaseConnection();



    if (!$conn) {



        return null;



    }



    



    $userId = getCurrentUserId();



    $stmt = $conn->prepare("SELECT id, first_name, last_name, email, user_type, status FROM users WHERE id = ?");



    $stmt->bind_param("i", $userId);



    $stmt->execute();



    $result = $stmt->get_result();



    $user = $result->fetch_assoc();



    



    $stmt->close();



    closeDatabaseConnection($conn);



    



    return $user;



}







// Login user



function loginUser($email, $password) {



    $conn = getDatabaseConnection();



    if (!$conn) {



        error_log("Database connection failed in loginUser");



        return ['success' => false, 'message' => 'Database connection failed'];



    }



    



    // Get user by email



    $stmt = $conn->prepare("SELECT id, first_name, last_name, email, password, user_type, status FROM users WHERE email = ?");



    if (!$stmt) {



        error_log("Prepare statement failed: " . $conn->error);



        closeDatabaseConnection($conn);



        return ['success' => false, 'message' => 'Database query failed'];



    }



    



    $stmt->bind_param("s", $email);



    if (!$stmt->execute()) {



        error_log("Execute statement failed: " . $stmt->error);



        $stmt->close();



        closeDatabaseConnection($conn);



        return ['success' => false, 'message' => 'Database query failed'];



    }



    



    $result = $stmt->get_result();



    



    if ($result->num_rows === 0) {



        $stmt->close();



        closeDatabaseConnection($conn);



        return ['success' => false, 'message' => 'Invalid email or password'];



    }



    



    $user = $result->fetch_assoc();



    $stmt->close();



    



    // Check if account is active



    if ($user['status'] !== 'active') {



        closeDatabaseConnection($conn);



        return ['success' => false, 'message' => 'Account is not active'];



    }



    



    // Verify password



    if (!password_verify($password, $user['password'])) {



        // Log failed attempt



        logLoginActivity($conn, $user['id'], 'failed');



        closeDatabaseConnection($conn);



        return ['success' => false, 'message' => 'Invalid email or password'];



    }



    



    // Update last login



    $updateStmt = $conn->prepare("UPDATE users SET last_login = NOW() WHERE id = ?");



    if ($updateStmt) {



        $updateStmt->bind_param("i", $user['id']);



        $updateStmt->execute();



        $updateStmt->close();



    }



    



    // Log successful login



    logLoginActivity($conn, $user['id'], 'success');
    
    closeDatabaseConnection($conn);
    
    // Return user data for OTP verification - DO NOT set session variables yet
    return [
        'success' => true, 
        'message' => 'Password verified - OTP required',
        'user_type' => $user['user_type'],
        'user_id' => $user['id'],
        'user_data' => [
            'id' => $user['id'],
            'first_name' => $user['first_name'],
            'last_name' => $user['last_name'],
            'email' => $user['email'],
            'user_type' => $user['user_type']
        ]
    ];




}







// Register new user



function registerUser($firstName, $lastName, $email, $password, $userType = 'user') {



    $conn = getDatabaseConnection();



    if (!$conn) {



        return ['success' => false, 'message' => 'Database connection failed'];



    }



    



    // Check if email already exists



    $checkStmt = $conn->prepare("SELECT id FROM users WHERE email = ?");



    $checkStmt->bind_param("s", $email);



    $checkStmt->execute();



    $existingUser = $checkStmt->get_result();



    $checkStmt->close();



    



    if ($existingUser->num_rows > 0) {



        return ['success' => false, 'message' => 'Email already exists'];



    }



    



    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);



    



    // Insert new user



    $stmt = $conn->prepare("INSERT INTO users (first_name, last_name, email, password, user_type) VALUES (?, ?, ?, ?, ?)");



    $stmt->bind_param("sssss", $firstName, $lastName, $email, $hashedPassword, $userType);



    



    if ($stmt->execute()) {



        $userId = $stmt->insert_id;



        $stmt->close();



        closeDatabaseConnection($conn);



        



        // Auto-login user after registration



        $_SESSION['user_id'] = $userId;



        $_SESSION['first_name'] = $firstName;



        $_SESSION['last_name'] = $lastName;



        $_SESSION['email'] = $email;



        $_SESSION['user_type'] = $userType;



        



        return ['success' => true, 'message' => 'Registration successful', 'user_id' => $userId];



    } else {



        $stmt->close();



        closeDatabaseConnection($conn);



        return ['success' => false, 'message' => 'Registration failed'];



    }



}







// Save user preferences



function saveUserPreferences($userId, $preferredTourType, $difficultyLevel, $groupSizePreference, $emailNotifications, $newsletterSubscription) {



    $conn = getDatabaseConnection();



    if (!$conn) {



        return ['success' => false, 'message' => 'Database connection failed'];



    }



    



    try {



        // Create user_preferences table if it doesn't exist



        $createTableSQL = "



            CREATE TABLE IF NOT EXISTS user_preferences (



                id INT AUTO_INCREMENT PRIMARY KEY,



                user_id INT NOT NULL,



                preferred_tour_type VARCHAR(50) DEFAULT NULL,



                difficulty_level VARCHAR(20) DEFAULT NULL,



                group_size_preference VARCHAR(20) DEFAULT NULL,



                email_notifications TINYINT(1) DEFAULT 0,



                newsletter_subscription TINYINT(1) DEFAULT 0,



                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,



                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,



                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,



                UNIQUE KEY unique_user_preference (user_id)



            )



        ";



        



        if (!$conn->query($createTableSQL)) {



            error_log("Error creating user_preferences table: " . $conn->error);



            closeDatabaseConnection($conn);



            return ['success' => false, 'message' => 'Failed to create preferences table'];



        }



        



        // Insert or update user preferences



        $stmt = $conn->prepare("



            INSERT INTO user_preferences 



            (user_id, preferred_tour_type, difficulty_level, group_size_preference, email_notifications, newsletter_subscription) 



            VALUES (?, ?, ?, ?, ?, ?)



            ON DUPLICATE KEY UPDATE



            preferred_tour_type = VALUES(preferred_tour_type),



            difficulty_level = VALUES(difficulty_level),



            group_size_preference = VALUES(group_size_preference),



            email_notifications = VALUES(email_notifications),



            newsletter_subscription = VALUES(newsletter_subscription),



            updated_at = CURRENT_TIMESTAMP



        ");



        



        $stmt->bind_param("isssii", $userId, $preferredTourType, $difficultyLevel, $groupSizePreference, $emailNotifications, $newsletterSubscription);



        



        if ($stmt->execute()) {



            $stmt->close();



            closeDatabaseConnection($conn);



            return ['success' => true, 'message' => 'User preferences saved successfully'];



        } else {



            $stmt->close();



            closeDatabaseConnection($conn);



            return ['success' => false, 'message' => 'Failed to save user preferences'];



        }



        



    } catch (Exception $e) {



        error_log("Error saving user preferences: " . $e->getMessage());



        closeDatabaseConnection($conn);



        return ['success' => false, 'message' => 'An error occurred while saving preferences'];



    }



}







// Get user preferences



function getUserPreferences($userId) {



    $conn = getDatabaseConnection();



    if (!$conn) {



        return null;



    }



    



    try {



        $stmt = $conn->prepare("



            SELECT preferred_tour_type, difficulty_level, group_size_preference, 



                   email_notifications, newsletter_subscription 



            FROM user_preferences 



            WHERE user_id = ?



        ");



        



        $stmt->bind_param("i", $userId);



        $stmt->execute();



        $result = $stmt->get_result();



        



        if ($result->num_rows > 0) {



            $preferences = $result->fetch_assoc();



            $stmt->close();



            closeDatabaseConnection($conn);



            return $preferences;



        }



        



        $stmt->close();



        closeDatabaseConnection($conn);



        return null;



        



    } catch (Exception $e) {



        error_log("Error getting user preferences: " . $e->getMessage());



        closeDatabaseConnection($conn);



        return null;



    }



}







// Log login activity



function logLoginActivity($conn, $userId, $status) {



    try {



        $ipAddress = $_SERVER['REMOTE_ADDR'] ?? 'Unknown';



        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';



        



        $stmt = $conn->prepare("INSERT INTO login_activity (user_id, ip_address, user_agent, status) VALUES (?, ?, ?, ?)");



        if ($stmt) {



            $stmt->bind_param("isss", $userId, $ipAddress, $userAgent, $status);



            $stmt->execute();



            $stmt->close();



        }



    } catch (Exception $e) {



        // Don't fail login if logging fails



        error_log("Failed to log login activity: " . $e->getMessage());



    }



}







// Logout user



function logoutUser() {



    try {



        // Log the logout activity if user is logged in



        if (isLoggedIn()) {



            $currentUser = getCurrentUser();



            if ($currentUser) {



                $conn = getDatabaseConnection();



                if ($conn) {



                    // Log logout activity



                    $ipAddress = $_SERVER['REMOTE_ADDR'] ?? 'Unknown';



                    $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';



                    



                    $stmt = $conn->prepare("INSERT INTO login_activity (user_id, ip_address, user_agent, status) VALUES (?, ?, ?, 'logout')");



                    if ($stmt) {



                        $stmt->bind_param("iss", $currentUser['id'], $ipAddress, $userAgent);



                        $stmt->execute();



                        $stmt->close();



                    }



                    closeDatabaseConnection($conn);



                }



            }



        }



        



        // Clear and destroy session



        session_unset();



        session_destroy();



        



        return true;



    } catch (Exception $e) {



        error_log("Logout error: " . $e->getMessage());



        return false;



    }



}







// Redirect if not logged in



function requireLogin() {



    if (!isLoggedIn()) {



        header('Location: ../log-in.php');



        exit();



    }



}







// Redirect if not admin



function requireAdmin() {
    if (!isAdmin()) {
        header('Location: ../log-in.php');
        exit();
    }
}



// Redirect if not tour guide



function requireTourGuide() {



    if (!isTourGuide()) {



        header('Location: ../log-in.php');



        exit();



    }



}







// Register tour guide



function registerTourGuide($firstName, $lastName, $email, $password, $personName, $specialization, $experienceYears, $languages, $hourlyRate, $contactNumber, $bio) {



    $conn = getDatabaseConnection();



    if (!$conn) {



        return ['success' => false, 'message' => 'Database connection failed'];



    }



    



    try {



        $conn->begin_transaction();



        



        // Check if email already exists



        $checkStmt = $conn->prepare("SELECT id FROM users WHERE email = ?");



        $checkStmt->bind_param("s", $email);



        $checkStmt->execute();



        $checkResult = $checkStmt->get_result();



        



        if ($checkResult->num_rows > 0) {



            $checkStmt->close();



            $conn->rollback();



            closeDatabaseConnection($conn);



            return ['success' => false, 'message' => 'Email already exists'];



        }



        $checkStmt->close();



        



        // Hash password



        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);



        



        // Insert new user with tour_guide type



        $userStmt = $conn->prepare("INSERT INTO users (first_name, last_name, email, password, user_type) VALUES (?, ?, ?, ?, 'tour_guide')");



        $userStmt->bind_param("ssss", $firstName, $lastName, $email, $hashedPassword);



        



        if (!$userStmt->execute()) {



            $userStmt->close();



            $conn->rollback();



            closeDatabaseConnection($conn);



            return ['success' => false, 'message' => 'Failed to create user account'];



        }



        



        $userId = $userStmt->insert_id;



        $userStmt->close();



        



        // Insert tour guide details



        $guideStmt = $conn->prepare("INSERT INTO tour_guides (user_id, person_name, specialization, experience_years, languages, hourly_rate, contact_number, bio) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");



        $guideStmt->bind_param("issisids", $userId, $personName, $specialization, $experienceYears, $languages, $hourlyRate, $contactNumber, $bio);



        



        if (!$guideStmt->execute()) {



            $guideStmt->close();



            $conn->rollback();



            closeDatabaseConnection($conn);



            return ['success' => false, 'message' => 'Failed to create tour guide profile'];



        }



        



        $guideStmt->close();



        $conn->commit();



        closeDatabaseConnection($conn);



        



        return ['success' => true, 'message' => 'Tour guide registration successful', 'user_id' => $userId];



        



    } catch (Exception $e) {



        $conn->rollback();



        closeDatabaseConnection($conn);



        return ['success' => false, 'message' => 'Registration failed: ' . $e->getMessage()];



    }



}







// Get available categories



function getAvailableCategories() {



    $conn = getDatabaseConnection();



    if (!$conn) {



        return [];



    }



    



    $stmt = $conn->prepare("SELECT name, display_name, icon FROM available_categories WHERE status = 'active' ORDER BY display_name");



    $stmt->execute();



    $result = $stmt->get_result();



    



    $categories = [];



    while ($row = $result->fetch_assoc()) {



        $categories[] = $row;



    }



    



    $stmt->close();



    closeDatabaseConnection($conn);



    



    return $categories;



}



// Generate 6-digit OTP code
function generateOtpCode() {
    return str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
}

// Send OTP email for login verification
function sendLoginOtpEmail($toEmail, $code) {
    // Check if PHPMailer is available
    $phpMailerPath = __DIR__ . '/../PHPMailer-6.9.1/src/PHPMailer.php';
    $exceptionPath = __DIR__ . '/../PHPMailer-6.9.1/src/Exception.php';
    $smtpPath = __DIR__ . '/../PHPMailer-6.9.1/src/SMTP.php';
    
    if (!file_exists($phpMailerPath) || !file_exists($exceptionPath) || !file_exists($smtpPath)) {
        error_log("PHPMailer files not found at: $phpMailerPath");
        return [
            'success' => false,
            'message' => 'Email service not available'
        ];
    }
    
    try {
        require_once $phpMailerPath;
        require_once $exceptionPath;
        require_once $smtpPath;
        
        if (!class_exists('PHPMailer\PHPMailer\PHPMailer')) {
            error_log("PHPMailer class not found after including files");
            return [
                'success' => false,
                'message' => 'Email service not available'
            ];
        }
        
        $mail = new PHPMailer(true);
        
        // SMTP configuration - Use centralized SMTP config
        require_once __DIR__ . '/smtp.php';
        
        $mail->isSMTP();
        $mail->Host = getenv('SMTP_HOST') ?: 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'jeanmarcaguilar829@gmail.com';
        $mail->Password = 'rqulwmjxdtzmxfli';
        $mail->Port = getenv('SMTP_PORT') ?: 587;
        $mail->SMTPSecure = getenv('SMTP_SECURE') === 'tls' ? PHPMailer::ENCRYPTION_STARTTLS : PHPMailer::ENCRYPTION_SMTPS;
        
        // Email content
        $mail->setFrom(getenv('SMTP_FROM_EMAIL') ?: 'jeanmarcaguilar829@gmail.com', getenv('SMTP_FROM_NAME') ?: 'SJDM Tours');
        $mail->addAddress($toEmail);
        $mail->isHTML(true);
        $mail->Subject = 'Your SJDM Tours Login Verification Code';
        
        // HTML email template
        $mail->Body = '
        <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; background-color: #f8f9fa;">
            <div style="background-color: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
                <div style="text-align: center; margin-bottom: 30px;">
                    <h1 style="color: #2c3e50; margin: 0;">SJDM Tours</h1>
                    <p style="color: #7f8c8d; margin: 5px 0 0 0;">Discover the Balcony of Metropolis</p>
                </div>
                
                <div style="background-color: #e8f4fd; padding: 20px; border-radius: 8px; margin: 20px 0; text-align: center;">
                    <h2 style="color: #2c3e50; margin: 0 0 10px 0;">Verification Code</h2>
                    <div style="font-size: 32px; font-weight: bold; color: #3498db; letter-spacing: 5px; margin: 20px 0;">
                        ' . $code . '
                    </div>
                    <p style="color: #7f8c8d; margin: 0;">This code will expire in 10 minutes</p>
                </div>
                
                <div style="margin: 30px 0;">
                    <h3 style="color: #2c3e50; margin: 0 0 10px 0;">How to use this code:</h3>
                    <ol style="color: #5a6c7d; line-height: 1.6;">
                        <li>Return to the SJDM Tours login page</li>
                        <li>Enter this 6-digit code in the verification modal</li>
                        <li>Click "Verify Code" to complete your login</li>
                    </ol>
                </div>
                
                <div style="background-color: #fff3cd; border-left: 4px solid #ffc107; padding: 15px; margin: 20px 0;">
                    <p style="color: #856404; margin: 0;">
                        <strong>Security Notice:</strong> Never share this code with anyone. SJDM Tours staff will never ask for your verification code.
                    </p>
                </div>
                
                <div style="text-align: center; margin-top: 30px; padding-top: 20px; border-top: 1px solid #e9ecef;">
                    <p style="color: #7f8c8d; font-size: 14px; margin: 0;">
                        If you didn\'t request this code, please ignore this email or contact our support team.
                    </p>
                    <p style="color: #7f8c8d; font-size: 14px; margin: 10px 0 0 0;">
                        © 2024 SJDM Tours. All rights reserved.
                    </p>
                </div>
            </div>
        </div>';
        
        // Plain text version
        $mail->AltBody = "Your SJDM Tours Login Verification Code: $code\n\nThis code will expire in 10 minutes.\n\nIf you didn't request this code, please ignore this email.";
        
        $mail->send();
        
        return [
            'success' => true,
            'message' => 'Verification code sent successfully'
        ];
        
    } catch (Exception $e) {
        error_log("Email sending failed: " . $e->getMessage());
        return [
            'success' => false,
            'message' => 'Failed to send verification code: ' . $e->getMessage()
        ];
    }
}

// Send booking confirmation email with e-receipt
function sendBookingConfirmationEmail($toEmail, $bookingData) {
    // Check if PHPMailer is available
    $phpMailerPath = __DIR__ . '/../PHPMailer-6.9.1/src/PHPMailer.php';
    $exceptionPath = __DIR__ . '/../PHPMailer-6.9.1/src/Exception.php';
    $smtpPath = __DIR__ . '/../PHPMailer-6.9.1/src/SMTP.php';
    
    if (!file_exists($phpMailerPath) || !file_exists($exceptionPath) || !file_exists($smtpPath)) {
        error_log("PHPMailer files not found at: $phpMailerPath");
        return [
            'success' => false,
            'message' => 'Email service not available'
        ];
    }
    
    try {
        require_once $phpMailerPath;
        require_once $exceptionPath;
        require_once $smtpPath;
        
        if (!class_exists('PHPMailer\PHPMailer\PHPMailer')) {
            error_log("PHPMailer class not found after including files");
            return [
                'success' => false,
                'message' => 'Email service not available'
            ];
        }
        
        $mail = new PHPMailer(true);
        
        // SMTP configuration
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'sjdmbulacanlgu@gmail.com';
        $mail->Password = 'hdzszfxiqekenxlb';
        $mail->Port = 587;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        
        // Email content
        $mail->setFrom('sjdmbulacanlgu@gmail.com', 'SJDM Tours');
        $mail->addAddress($toEmail);
        $mail->isHTML(true);
        $mail->Subject = 'Booking Confirmation - SJDM Tours - ' . $bookingData['booking_reference'];
        
        // Format date for display
        $formattedDate = date('F j, Y', strtotime($bookingData['tour_date']));
        
        // HTML email template
        $mail->Body = '
        <div style="font-family: Arial, sans-serif; max-width: 700px; margin: 0 auto; padding: 20px; background-color: #f8f9fa;">
            <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 40px 30px; border-radius: 15px 15px 0 0; text-align: center; color: white;">
                <h1 style="margin: 0; font-size: 36px; font-weight: bold;">🎉 Booking Confirmed!</h1>
                <p style="margin: 10px 0 0 0; font-size: 18px; opacity: 0.9;">Your SJDM Tours adventure is all set</p>
            </div>
            
            <div style="background-color: white; padding: 40px 30px; border-radius: 0 0 15px 15px; box-shadow: 0 4px 20px rgba(0,0,0,0.1);">
                
                <div style="background-color: #d4edda; border-left: 5px solid #28a745; padding: 20px; margin: 0 0 30px 0; border-radius: 5px;">
                    <h2 style="color: #155724; margin: 0 0 10px 0; font-size: 24px;">✅ Booking Successful</h2>
                    <p style="color: #155724; margin: 0; font-size: 16px;">Thank you for choosing SJDM Tours! Your booking has been confirmed and we\'re excited to host your adventure.</p>
                </div>
                
                <div style="background-color: #f8f9fa; padding: 25px; border-radius: 10px; margin: 0 0 30px 0; border: 2px solid #e9ecef;">
                    <h3 style="color: #2c3e50; margin: 0 0 20px 0; font-size: 20px; text-align: center;">📋 E-RECEIPT</h3>
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 20px;">
                        <div>
                            <p style="margin: 0 0 5px 0; color: #7f8c8d; font-size: 12px; text-transform: uppercase;">Booking Reference</p>
                            <p style="margin: 0; color: #2c3e50; font-weight: bold; font-size: 16px;">' . $bookingData['booking_reference'] . '</p>
                        </div>
                        <div>
                            <p style="margin: 0 0 5px 0; color: #7f8c8d; font-size: 12px; text-transform: uppercase;">Status</p>
                            <p style="margin: 0; color: #28a745; font-weight: bold; font-size: 16px;">Pending</p>
                        </div>
                    </div>
                    
                    <div style="border-top: 1px solid #dee2e6; border-bottom: 1px solid #dee2e6; padding: 20px 0; margin: 20px 0;">
                        <h4 style="color: #2c3e50; margin: 0 0 15px 0; font-size: 18px;">📍 Tour Details</h4>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                            <div>
                                <p style="margin: 0 0 5px 0; color: #7f8c8d; font-size: 12px; text-transform: uppercase;">Destination</p>
                                <p style="margin: 0; color: #2c3e50; font-weight: 600;">' . htmlspecialchars($bookingData['destination']) . '</p>
                            </div>
                            <div>
                                <p style="margin: 0 0 5px 0; color: #7f8c8d; font-size: 12px; text-transform: uppercase;">Tour Date</p>
                                <p style="margin: 0; color: #2c3e50; font-weight: 600;">' . $formattedDate . '</p>
                            </div>
                            <div>
                                <p style="margin: 0 0 5px 0; color: #7f8c8d; font-size: 12px; text-transform: uppercase;">Number of Guests</p>
                                <p style="margin: 0; color: #2c3e50; font-weight: 600;">' . $bookingData['guests'] . ' ' . ($bookingData['guests'] == 1 ? 'Person' : 'People') . '</p>
                            </div>
                            <div>
                                <p style="margin: 0 0 5px 0; color: #7f8c8d; font-size: 12px; text-transform: uppercase;">Contact Number</p>
                                <p style="margin: 0; color: #2c3e50; font-weight: 600;">' . htmlspecialchars($bookingData['contact_number']) . '</p>
                            </div>
                        </div>';
        
        // Add special requests if any
        if (!empty($bookingData['special_requests'])) {
            $mail->Body .= '
                        <div style="margin-top: 20px;">
                            <p style="margin: 0 0 5px 0; color: #7f8c8d; font-size: 12px; text-transform: uppercase;">Special Requests</p>
                            <p style="margin: 0; color: #2c3e50; font-style: italic;">' . htmlspecialchars($bookingData['special_requests']) . '</p>
                        </div>';
        }
        
        $mail->Body .= '
                    </div>
                    
                    <div style="background-color: #fff3cd; padding: 20px; border-radius: 8px; margin: 20px 0;">
                        <h4 style="color: #856404; margin: 0 0 15px 0; font-size: 18px;">💰 Payment Breakdown</h4>
                        <div style="display: grid; gap: 10px;">
                            <div style="display: flex; justify-content: space-between; align-items: center;">
                                <span style="color: #5a6c7d;">Tour Guide Fee</span>
                                <span style="color: #2c3e50; font-weight: 600;">₱' . number_format($bookingData['guide_fee'], 2) . '</span>
                            </div>
                            <div style="display: flex; justify-content: space-between; align-items: center;">
                                <span style="color: #5a6c7d;">Entrance Fee (' . $bookingData['guests'] . ' guests)</span>
                                <span style="color: #2c3e50; font-weight: 600;">₱' . number_format($bookingData['entrance_fee'], 2) . '</span>
                            </div>
                            <div style="display: flex; justify-content: space-between; align-items: center;">
                                <span style="color: #5a6c7d;">Service Fee</span>
                                <span style="color: #2c3e50; font-weight: 600;">₱' . number_format($bookingData['service_fee'], 2) . '</span>
                            </div>
                            <div style="border-top: 2px solid #ffc107; padding-top: 10px; margin-top: 10px;">
                                <div style="display: flex; justify-content: space-between; align-items: center;">
                                    <span style="color: #2c3e50; font-weight: bold; font-size: 18px;">TOTAL AMOUNT</span>
                                    <span style="color: #28a745; font-weight: bold; font-size: 20px;">₱' . number_format($bookingData['total_amount'], 2) . '</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div style="background-color: #e3f2fd; padding: 20px; border-radius: 8px; margin: 30px 0;">
                    <h4 style="color: #1976d2; margin: 0 0 15px 0; font-size: 18px;">📝 Important Information</h4>
                    <ul style="color: #5a6c7d; line-height: 1.8; margin: 0; padding-left: 20px;">
                        <li>Please arrive at the meeting point 15 minutes before your scheduled tour</li>
                        <li>Bring a valid ID for verification</li>
                        <li>Wear comfortable clothing and appropriate footwear</li>
                        <li>Don\'t forget to bring water and sun protection</li>
                        <li>Our tour guide will contact you via phone for final coordination</li>
                    </ul>
                </div>
                
                <div style="text-align: center; margin-top: 40px; padding-top: 30px; border-top: 1px solid #e9ecef;">
                    <div style="margin-bottom: 20px;">
                        <a href="#" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 15px 30px; text-decoration: none; border-radius: 25px; font-weight: bold; display: inline-block;">View My Bookings</a>
                    </div>
                    <p style="color: #7f8c8d; font-size: 14px; margin: 0;">
                        If you have any questions, feel free to contact us at:<br>
                        📧 christianbacay042504@gmail.com | 📱 ' . htmlspecialchars($bookingData['contact_number']) . '
                    </p>
                    <p style="color: #7f8c8d; font-size: 14px; margin: 20px 0 0 0;">
                        © 2024 SJDM Tours. All rights reserved.<br>
                        Discover the Balcony of Metropolis 🏔️
                    </p>
                </div>
            </div>
        </div>';
        
        // Plain text version
        $mail->AltBody = "BOOKING CONFIRMATION - SJDM Tours\n\n" .
            "Booking Reference: " . $bookingData['booking_reference'] . "\n" .
            "Status: Confirmed\n\n" .
            "Tour Details:\n" .
            "Destination: " . $bookingData['destination'] . "\n" .
            "Date: " . $formattedDate . "\n" .
            "Guests: " . $bookingData['guests'] . "\n" .
            "Contact: " . $bookingData['contact_number'] . "\n\n" .
            "Payment Breakdown:\n" .
            "Tour Guide Fee: ₱" . number_format($bookingData['guide_fee'], 2) . "\n" .
            "Entrance Fee: ₱" . number_format($bookingData['entrance_fee'], 2) . "\n" .
            "Service Fee: ₱" . number_format($bookingData['service_fee'], 2) . "\n" .
            "Total: ₱" . number_format($bookingData['total_amount'], 2) . "\n\n" .
            "Thank you for choosing SJDM Tours!\n" .
            "For questions, contact: christianbacay042504@gmail.com\n";
        
        $mail->send();
        
        return [
            'success' => true,
            'message' => 'Booking confirmation email sent successfully'
        ];
        
    } catch (Exception $e) {
        error_log("Email sending failed: " . $e->getMessage());
        return [
            'success' => false,
            'message' => 'Email sending failed: ' . $e->getMessage()
        ];
    }
}

// Send booking status update email (approved/confirmed)
function sendBookingStatusUpdateEmail($toEmail, $bookingData, $status) {
    // Check if PHPMailer is available
    $phpMailerPath = __DIR__ . '/../PHPMailer-6.9.1/src/PHPMailer.php';
    $exceptionPath = __DIR__ . '/../PHPMailer-6.9.1/src/Exception.php';
    $smtpPath = __DIR__ . '/../PHPMailer-6.9.1/src/SMTP.php';
    
    if (!file_exists($phpMailerPath) || !file_exists($exceptionPath) || !file_exists($smtpPath)) {
        error_log("PHPMailer files not found at: $phpMailerPath");
        return [
            'success' => false,
            'message' => 'Email service not available'
        ];
    }
    
    try {
        require_once $phpMailerPath;
        require_once $exceptionPath;
        require_once $smtpPath;
        
        if (!class_exists('PHPMailer\PHPMailer\PHPMailer')) {
            error_log("PHPMailer class not found after including files");
            return [
                'success' => false,
                'message' => 'Email service not available'
            ];
        }
        
        $mail = new PHPMailer(true);
        
        // SMTP configuration - Use centralized SMTP config
        require_once __DIR__ . '/smtp.php';
        
        $mail->isSMTP();
        $mail->Host = getenv('SMTP_HOST') ?: 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'jeanmarcaguilar829@gmail.com';
        $mail->Password = 'rqulwmjxdtzmxfli';
        $mail->Port = getenv('SMTP_PORT') ?: 587;
        $mail->SMTPSecure = getenv('SMTP_SECURE') === 'tls' ? PHPMailer::ENCRYPTION_STARTTLS : PHPMailer::ENCRYPTION_SMTPS;
        
        // Email content
        $mail->setFrom(getenv('SMTP_FROM_EMAIL') ?: 'jeanmarcaguilar829@gmail.com', getenv('SMTP_FROM_NAME') ?: 'SJDM Tours');
        $mail->addAddress($toEmail);
        $mail->isHTML(true);
        
        $formattedDate = date('F j, Y', strtotime($bookingData['booking_date']));
        $bookingId = str_pad($bookingData['id'], 6, '0', STR_PAD_LEFT);
        
        if ($status === 'confirmed') {
            $mail->Subject = 'Booking Approved - SJDM Tours - #' . $bookingId;
            
            $mail->Body = '
            <div style="font-family: Arial, sans-serif; max-width: 700px; margin: 0 auto; padding: 20px; background-color: #f8f9fa;">
                <div style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%); padding: 40px 30px; border-radius: 15px 15px 0 0; text-align: center; color: white;">
                    <h1 style="margin: 0; font-size: 36px; font-weight: bold;">🎉 Booking Approved!</h1>
                    <p style="margin: 10px 0 0 0; font-size: 18px; opacity: 0.9;">Your tour has been confirmed</p>
                </div>
                
                <div style="background-color: white; padding: 40px 30px; border-radius: 0 0 15px 15px; box-shadow: 0 4px 20px rgba(0,0,0,0.1);">
                    
                    <div style="background-color: #d4edda; border-left: 5px solid #28a745; padding: 20px; margin: 0 0 30px 0; border-radius: 5px;">
                        <h2 style="color: #155724; margin: 0 0 10px 0; font-size: 24px;">✅ Good News!</h2>
                        <p style="color: #155724; margin: 0; font-size: 16px;">Your booking has been approved and confirmed. Get ready for an amazing adventure with SJDM Tours!</p>
                    </div>
                    
                    <div style="background-color: #f8f9fa; padding: 25px; border-radius: 10px; margin: 0 0 30px 0; border: 2px solid #e9ecef;">
                        <h3 style="color: #2c3e50; margin: 0 0 20px 0; font-size: 20px; text-align: center;">📋 UPDATED E-RECEIPT</h3>
                        
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 20px;">
                            <div>
                                <p style="margin: 0 0 5px 0; color: #7f8c8d; font-size: 12px; text-transform: uppercase;">Booking ID</p>
                                <p style="margin: 0; color: #2c3e50; font-weight: bold; font-size: 16px;">#' . $bookingId . '</p>
                            </div>
                            <div>
                                <p style="margin: 0 0 5px 0; color: #7f8c8d; font-size: 12px; text-transform: uppercase;">Status</p>
                                <p style="margin: 0; color: #28a745; font-weight: bold; font-size: 16px;">✅ Confirmed</p>
                            </div>
                        </div>
                        
                        <div style="border-top: 1px solid #dee2e6; border-bottom: 1px solid #dee2e6; padding: 20px 0; margin: 20px 0;">
                            <h4 style="color: #2c3e50; margin: 0 0 15px 0; font-size: 18px;">📍 Tour Details</h4>
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                                <div>
                                    <p style="margin: 0 0 5px 0; color: #7f8c8d; font-size: 12px; text-transform: uppercase;">Tour Name</p>
                                    <p style="margin: 0; color: #2c3e50; font-weight: 600;">' . htmlspecialchars($bookingData['tour_name']) . '</p>
                                </div>
                                <div>
                                    <p style="margin: 0 0 5px 0; color: #7f8c8d; font-size: 12px; text-transform: uppercase;">Tour Date</p>
                                    <p style="margin: 0; color: #2c3e50; font-weight: 600;">' . $formattedDate . '</p>
                                </div>
                                <div>
                                    <p style="margin: 0 0 5px 0; color: #7f8c8d; font-size: 12px; text-transform: uppercase;">Number of People</p>
                                    <p style="margin: 0; color: #2c3e50; font-weight: 600;">' . $bookingData['number_of_people'] . '</p>
                                </div>
                                <div>
                                    <p style="margin: 0 0 5px 0; color: #7f8c8d; font-size: 12px; text-transform: uppercase;">Total Amount</p>
                                    <p style="margin: 0; color: #28a745; font-weight: bold; font-size: 16px;">₱' . number_format($bookingData['total_amount'], 2) . '</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div style="background-color: #e3f2fd; padding: 20px; border-radius: 8px; margin: 30px 0;">
                        <h4 style="color: #1976d2; margin: 0 0 15px 0; font-size: 18px;">📝 What\'s Next?</h4>
                        <ul style="color: #5a6c7d; line-height: 1.8; margin: 0; padding-left: 20px;">
                            <li>Please arrive at the meeting point 15 minutes before your scheduled tour</li>
                            <li>Bring a valid ID for verification</li>
                            <li>Wear comfortable clothing and appropriate footwear</li>
                            <li>Our tour guide will contact you via phone for final coordination</li>
                            <li>Payment can be made on the day of the tour</li>
                        </ul>
                    </div>
                    
                    <div style="text-align: center; margin-top: 40px; padding-top: 30px; border-top: 1px solid #e9ecef;">
                        <p style="color: #7f8c8d; font-size: 14px; margin: 0;">
                            If you have any questions, feel free to contact us at:<br>
                            📧 ' . readEnvValue('SMTP_FROM_EMAIL') . ' | 📱 Contact: 0912-345-6789
                        </p>
                        <p style="color: #7f8c8d; font-size: 14px; margin: 20px 0 0 0;">
                            © 2024 SJDM Tours. All rights reserved.<br>
                            Discover the Balcony of Metropolis 🏔️
                        </p>
                    </div>
                </div>
            </div>';
            
            $mail->AltBody = "BOOKING APPROVED - SJDM Tours\n\n" .
                "Booking ID: #" . $bookingId . "\n" .
                "Status: Confirmed\n\n" .
                "Tour Details:\n" .
                "Tour Name: " . $bookingData['tour_name'] . "\n" .
                "Date: " . $formattedDate . "\n" .
                "People: " . $bookingData['number_of_people'] . "\n" .
                "Total Amount: ₱" . number_format($bookingData['total_amount'], 2) . "\n\n" .
                "Your booking has been approved! Get ready for your tour.\n" .
                "For questions, contact: " . readEnvValue('SMTP_FROM_EMAIL') . "\n";
        }
        
        $mail->send();
        
        return [
            'success' => true,
            'message' => 'Booking status update email sent successfully'
        ];
        
    } catch (Exception $e) {
        error_log("Email sending failed: " . $e->getMessage());
        return [
            'success' => false,
            'message' => 'Email sending failed: ' . $e->getMessage()
        ];
    }
}

// Send booking rejection email with reason
function sendBookingRejectionEmail($toEmail, $bookingData, $rejectionReason) {
    // Check if PHPMailer is available
    $phpMailerPath = __DIR__ . '/../PHPMailer-6.9.1/src/PHPMailer.php';
    $exceptionPath = __DIR__ . '/../PHPMailer-6.9.1/src/Exception.php';
    $smtpPath = __DIR__ . '/../PHPMailer-6.9.1/src/SMTP.php';
    
    if (!file_exists($phpMailerPath) || !file_exists($exceptionPath) || !file_exists($smtpPath)) {
        error_log("PHPMailer files not found at: $phpMailerPath");
        return [
            'success' => false,
            'message' => 'Email service not available'
        ];
    }
    
    try {
        require_once $phpMailerPath;
        require_once $exceptionPath;
        require_once $smtpPath;
        
        if (!class_exists('PHPMailer\PHPMailer\PHPMailer')) {
            error_log("PHPMailer class not found after including files");
            return [
                'success' => false,
                'message' => 'Email service not available'
            ];
        }
        
        $mail = new PHPMailer(true);
        
        // SMTP configuration - Use centralized SMTP config
        require_once __DIR__ . '/smtp.php';
        
        $mail->isSMTP();
        $mail->Host = getenv('SMTP_HOST') ?: 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'jeanmarcaguilar829@gmail.com';
        $mail->Password = 'rqulwmjxdtzmxfli';
        $mail->Port = getenv('SMTP_PORT') ?: 587;
        $mail->SMTPSecure = getenv('SMTP_SECURE') === 'tls' ? PHPMailer::ENCRYPTION_STARTTLS : PHPMailer::ENCRYPTION_SMTPS;
        
        // Email content
        $mail->setFrom(getenv('SMTP_FROM_EMAIL') ?: 'jeanmarcaguilar829@gmail.com', getenv('SMTP_FROM_NAME') ?: 'SJDM Tours');
        $mail->addAddress($toEmail);
        $mail->isHTML(true);
        
        $formattedDate = date('F j, Y', strtotime($bookingData['booking_date']));
        $bookingId = str_pad($bookingData['id'], 6, '0', STR_PAD_LEFT);
        
        $mail->Subject = 'Booking Update - SJDM Tours - #' . $bookingId;
        
        $mail->Body = '
        <div style="font-family: Arial, sans-serif; max-width: 700px; margin: 0 auto; padding: 20px; background-color: #f8f9fa;">
            <div style="background: linear-gradient(135deg, #dc3545 0%, #c82333 100%); padding: 40px 30px; border-radius: 15px 15px 0 0; text-align: center; color: white;">
                <h1 style="margin: 0; font-size: 36px; font-weight: bold;">📋 Booking Update</h1>
                <p style="margin: 10px 0 0 0; font-size: 18px; opacity: 0.9;">Information about your booking</p>
            </div>
            
            <div style="background-color: white; padding: 40px 30px; border-radius: 0 0 15px 15px; box-shadow: 0 4px 20px rgba(0,0,0,0.1);">
                
                <div style="background-color: #f8d7da; border-left: 5px solid #dc3545; padding: 20px; margin: 0 0 30px 0; border-radius: 5px;">
                    <h2 style="color: #721c24; margin: 0 0 10px 0; font-size: 24px;">❌ Booking Cancelled</h2>
                    <p style="color: #721c24; margin: 0; font-size: 16px;">We regret to inform you that your booking has been cancelled. Please see the reason below.</p>
                </div>
                
                <div style="background-color: #f8f9fa; padding: 25px; border-radius: 10px; margin: 0 0 30px 0; border: 2px solid #e9ecef;">
                    <h3 style="color: #2c3e50; margin: 0 0 20px 0; font-size: 20px; text-align: center;">📋 BOOKING DETAILS</h3>
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 20px;">
                        <div>
                            <p style="margin: 0 0 5px 0; color: #7f8c8d; font-size: 12px; text-transform: uppercase;">Booking ID</p>
                            <p style="margin: 0; color: #2c3e50; font-weight: bold; font-size: 16px;">#' . $bookingId . '</p>
                        </div>
                        <div>
                            <p style="margin: 0 0 5px 0; color: #7f8c8d; font-size: 12px; text-transform: uppercase;">Status</p>
                            <p style="margin: 0; color: #dc3545; font-weight: bold; font-size: 16px;">❌ Cancelled</p>
                        </div>
                    </div>
                    
                    <div style="border-top: 1px solid #dee2e6; border-bottom: 1px solid #dee2e6; padding: 20px 0; margin: 20px 0;">
                        <h4 style="color: #2c3e50; margin: 0 0 15px 0; font-size: 18px;">📍 Tour Details</h4>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                            <div>
                                <p style="margin: 0 0 5px 0; color: #7f8c8d; font-size: 12px; text-transform: uppercase;">Tour Name</p>
                                <p style="margin: 0; color: #2c3e50; font-weight: 600;">' . htmlspecialchars($bookingData['tour_name']) . '</p>
                            </div>
                            <div>
                                <p style="margin: 0 0 5px 0; color: #7f8c8d; font-size: 12px; text-transform: uppercase;">Tour Date</p>
                                <p style="margin: 0; color: #2c3e50; font-weight: 600;">' . $formattedDate . '</p>
                            </div>
                            <div>
                                <p style="margin: 0 0 5px 0; color: #7f8c8d; font-size: 12px; text-transform: uppercase;">Number of People</p>
                                <p style="margin: 0; color: #2c3e50; font-weight: 600;">' . $bookingData['number_of_people'] . '</p>
                            </div>
                            <div>
                                <p style="margin: 0 0 5px 0; color: #7f8c8d; font-size: 12px; text-transform: uppercase;">Total Amount</p>
                                <p style="margin: 0; color: #6c757d; font-weight: bold; font-size: 16px;">₱' . number_format($bookingData['total_amount'], 2) . '</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div style="background-color: #fff3cd; border-left: 5px solid #ffc107; padding: 20px; border-radius: 8px; margin: 30px 0;">
                    <h4 style="color: #856404; margin: 0 0 15px 0; font-size: 18px;">📝 Reason for Cancellation</h4>
                    <p style="color: #856404; margin: 0; font-size: 16px; line-height: 1.6; font-style: italic;">' . htmlspecialchars($rejectionReason) . '</p>
                </div>
                
                <div style="background-color: #e3f2fd; padding: 20px; border-radius: 8px; margin: 30px 0;">
                    <h4 style="color: #1976d2; margin: 0 0 15px 0; font-size: 18px;">💡 What You Can Do</h4>
                    <ul style="color: #5a6c7d; line-height: 1.8; margin: 0; padding-left: 20px;">
                        <li>You can book a new tour for a different date</li>
                        <li>Contact our support team for assistance with rebooking</li>
                        <li>Check our website for available tour schedules</li>
                        <li>We apologize for any inconvenience caused</li>
                    </ul>
                </div>
                
                <div style="text-align: center; margin-top: 40px; padding-top: 30px; border-top: 1px solid #e9ecef;">
                    <div style="margin-bottom: 20px;">
                        <a href="#" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 15px 30px; text-decoration: none; border-radius: 25px; font-weight: bold; display: inline-block;">Book New Tour</a>
                    </div>
                    <p style="color: #7f8c8d; font-size: 14px; margin: 0;">
                        If you have any questions or need assistance, feel free to contact us at:<br>
                        📧 ' . readEnvValue('SMTP_FROM_EMAIL') . ' | 📱 0912-345-6789
                    </p>
                    <p style="color: #7f8c8d; font-size: 14px; margin: 20px 0 0 0;">
                        © 2024 SJDM Tours. All rights reserved.<br>
                        Discover the Balcony of Metropolis 🏔️
                    </p>
                </div>
            </div>
        </div>';
        
        $mail->AltBody = "BOOKING CANCELLED - SJDM Tours\n\n" .
            "Booking ID: #" . $bookingId . "\n" .
            "Status: Cancelled\n\n" .
            "Tour Details:\n" .
            "Tour Name: " . $bookingData['tour_name'] . "\n" .
            "Date: " . $formattedDate . "\n" .
            "People: " . $bookingData['number_of_people'] . "\n\n" .
            "Reason for Cancellation:\n" . $rejectionReason . "\n\n" .
            "We apologize for the inconvenience. You can book a new tour anytime.\n" .
            "For assistance, contact: " . readEnvValue('SMTP_FROM_EMAIL') . "\n";
        
        $mail->send();
        
        return [
            'success' => true,
            'message' => 'Booking rejection email sent successfully'
        ];
        
    } catch (Exception $e) {
        error_log("Email sending failed: " . $e->getMessage());
        return [
            'success' => false,
            'message' => 'Email sending failed: ' . $e->getMessage()
        ];
    }
}

// Store OTP code in database
function storeOtpCode($userId, $email, $code, $type = 'login') {
    $conn = getDatabaseConnection();
    if (!$conn) {
        return ['success' => false, 'message' => 'Database connection failed'];
    }
    
    try {
        // Delete any existing unused OTP codes for this user
        $deleteStmt = $conn->prepare("DELETE FROM otp_codes WHERE user_id = ? AND is_used = 0 AND expires_at > NOW()");
        $deleteStmt->bind_param("i", $userId);
        $deleteStmt->execute();
        $deleteStmt->close();
        
        // Insert new OTP code using database time for consistency
        $stmt = $conn->prepare("INSERT INTO otp_codes (user_id, email, otp_code, expires_at) VALUES (?, ?, ?, DATE_ADD(NOW(), INTERVAL 10 MINUTE))");
        $stmt->bind_param("iss", $userId, $email, $code);
        
        if ($stmt->execute()) {
            $stmt->close();
            closeDatabaseConnection($conn);
            return ['success' => true, 'message' => 'OTP code stored successfully'];
        } else {
            $stmt->close();
            closeDatabaseConnection($conn);
            return ['success' => false, 'message' => 'Failed to store OTP code'];
        }
        
    } catch (Exception $e) {
        closeDatabaseConnection($conn);
        return ['success' => false, 'message' => 'Error storing OTP code: ' . $e->getMessage()];
    }
}

// Verify OTP code
function verifyOtpCode($email, $code, $type = 'login') {
    $conn = getDatabaseConnection();
    if (!$conn) {
        return ['success' => false, 'message' => 'Database connection failed'];
    }
    
    try {
        $stmt = $conn->prepare("
            SELECT id, user_id, email, otp_code, expires_at, used_at 
            FROM otp_codes 
            WHERE email = ? AND otp_code = ? AND is_used = 0 AND expires_at > NOW()
            ORDER BY created_at DESC 
            LIMIT 1
        ");
        $stmt->bind_param("ss", $email, $code);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            $stmt->close();
            closeDatabaseConnection($conn);
            return ['success' => false, 'message' => 'Invalid or expired verification code'];
        }
        
        $otpRecord = $result->fetch_assoc();
        $stmt->close();
        
        // Mark OTP as used
        $updateStmt = $conn->prepare("UPDATE otp_codes SET is_used = 1, used_at = NOW() WHERE id = ?");
        $updateStmt->bind_param("i", $otpRecord['id']);
        $updateStmt->execute();
        $updateStmt->close();
        
        closeDatabaseConnection($conn);
        
        return [
            'success' => true, 
            'message' => 'Verification successful',
            'user_id' => $otpRecord['user_id']
        ];
        
    } catch (Exception $e) {
        closeDatabaseConnection($conn);
        return ['success' => false, 'message' => 'Error verifying OTP code: ' . $e->getMessage()];
    }
}

// Send user deletion notification email
function sendUserDeletionEmail($toEmail, $userData, $justification) {
    // Check if PHPMailer is available
    $phpMailerPath = __DIR__ . '/../PHPMailer-6.9.1/src/PHPMailer.php';
    $exceptionPath = __DIR__ . '/../PHPMailer-6.9.1/src/Exception.php';
    $smtpPath = __DIR__ . '/../PHPMailer-6.9.1/src/SMTP.php';
    
    if (!file_exists($phpMailerPath) || !file_exists($exceptionPath) || !file_exists($smtpPath)) {
        error_log("PHPMailer files not found at: $phpMailerPath");
        return [
            'success' => false,
            'message' => 'Email service not available'
        ];
    }
    
    try {
        require_once $phpMailerPath;
        require_once $exceptionPath;
        require_once $smtpPath;
        
        if (!class_exists('PHPMailer\PHPMailer\PHPMailer')) {
            error_log("PHPMailer class not found after including files");
            return [
                'success' => false,
                'message' => 'Email service not available'
            ];
        }
        
        $mail = new PHPMailer(true);
        
        // SMTP configuration - Use centralized SMTP config
        require_once __DIR__ . '/smtp.php';
        
        $mail->isSMTP();
        $mail->Host = getenv('SMTP_HOST') ?: 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'jeanmarcaguilar829@gmail.com';
        $mail->Password = 'rqulwmjxdtzmxfli';
        $mail->Port = getenv('SMTP_PORT') ?: 587;
        $mail->SMTPSecure = getenv('SMTP_SECURE') === 'tls' ? PHPMailer::ENCRYPTION_STARTTLS : PHPMailer::ENCRYPTION_SMTPS;
        
        // Email content
        $mail->setFrom(getenv('SMTP_FROM_EMAIL') ?: 'jeanmarcaguilar829@gmail.com', getenv('SMTP_FROM_NAME') ?: 'SJDM Tours');
        $mail->addAddress($toEmail);
        $mail->isHTML(true);
        
        $mail->Subject = 'Account Deletion Notice - SJDM Tours';
        
        $mail->Body = "
            <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; background-color: #f8f9fa;'>
                <div style='background: linear-gradient(135deg, #dc2626, #ef4444); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0;'>
                    <h1 style='margin: 0; font-size: 28px;'>⚠️ Account Deletion Notice</h1>
                    <p style='margin: 10px 0 0; font-size: 16px; opacity: 0.9;'>Your SJDM Tours account has been permanently deleted</p>
                </div>
                
                <div style='background: white; padding: 30px; border-radius: 0 0 10px 10px;'>
                    <h2 style='color: #dc2626; margin-top: 0;'>Account Information</h2>
                    <div style='background: #fef2f2; padding: 15px; border-radius: 8px; margin-bottom: 20px;'>
                        <p><strong>Name:</strong> " . htmlspecialchars($userData['first_name'] . ' ' . $userData['last_name']) . "</p>
                        <p><strong>Email:</strong> " . htmlspecialchars($userData['email']) . "</p>
                        <p><strong>User Type:</strong> " . ucfirst($userData['user_type']) . "</p>
                        <p><strong>Status:</strong> <span style='color: #dc2626; font-weight: bold;'>DELETED</span></p>
                    </div>
                    
                    <h2 style='color: #dc2626;'>Deletion Details</h2>
                    <div style='background: #fef2f2; padding: 15px; border-radius: 8px; margin-bottom: 20px;'>
                        <p><strong>Date of Deletion:</strong> " . date('F j, Y, g:i A') . "</p>
                        <p><strong>Reason for Deletion:</strong></p>
                        <div style='background: white; padding: 10px; border-left: 4px solid #dc2626; margin-top: 5px;'>
                            " . nl2br(htmlspecialchars($justification)) . "
                        </div>
                    </div>
                    
                    <div style='background: #fffbeb; border: 1px solid #fde68a; border-radius: 8px; padding: 20px; margin-bottom: 20px;'>
                        <h3 style='color: #92400e; margin-top: 0;'>⚠️ Important Information</h3>
                        <ul style='color: #78716b; line-height: 1.6;'>
                            <li>Your account and all associated data have been permanently deleted</li>
                            <li>You will no longer be able to access SJDM Tours services</li>
                            <li>All your booking history and preferences have been removed</li>
                            <li>This action cannot be undone</li>
                        </ul>
                    </div>
                    
                    <div style='text-align: center; padding: 20px 0;'>
                        <p style='color: #6b7280; font-size: 14px;'>If you believe this was done in error, please contact our support team immediately.</p>
                        <p style='color: #6b7280; font-size: 14px;'>Email: <a href='mailto:support@sjdm-tours.com'>support@sjdm-tours.com</a></p>
                    </div>
                </div>
                
                <div style='background: #374151; color: white; text-align: center; padding: 20px; border-radius: 0 0 10px 10px; font-size: 12px;'>
                    <p>© " . date('Y') . " SJDM Tours. All rights reserved.</p>
                </div>
            </div>
        ";
        
        $mail->AltBody = "
            ACCOUNT DELETION NOTICE - SJDM Tours
            
            Your SJDM Tours account has been permanently deleted.
            
            Account Information:
            Name: " . $userData['first_name'] . ' ' . $userData['last_name'] . "
            Email: " . $userData['email'] . "
            User Type: " . ucfirst($userData['user_type']) . "
            Status: DELETED
            
            Deletion Details:
            Date: " . date('F j, Y, g:i A') . "
            Reason: " . $justification . "
            
            Important Information:
            - Your account and all associated data have been permanently deleted
            - You will no longer be able to access SJDM Tours services
            - All your booking history and preferences have been removed
            - This action cannot be undone
            
            If you believe this was done in error, please contact our support team immediately.
            Email: support@sjdm-tours.com
            
            © " . date('Y') . " SJDM Tours. All rights reserved.
        ";
        
        $mail->send();
        
        return [
            'success' => true,
            'message' => 'User deletion notification email sent successfully'
        ];
        
    } catch (Exception $e) {
        error_log("Error sending user deletion email: " . $e->getMessage());
        return [
            'success' => false,
            'message' => 'Failed to send deletion notification email: ' . $e->getMessage()
        ];
    }
}

// Helper function to read environment variables
function readEnvValue($key) {
    $value = getenv($key);
    if ($value === false) {
        // Fallback to putenv values if getenv doesn't work
        $value = $_ENV[$key] ?? $_SERVER[$key] ?? null;
    }
    return $value;
}



