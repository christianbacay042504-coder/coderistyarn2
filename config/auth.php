<?php

// Authentication Helper Functions

// Created: January 30, 2026

use PHPMailer\PHPMailer\PHPMailer;

session_start();







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



    



    // Set session variables



    $_SESSION['user_id'] = $user['id'];



    $_SESSION['first_name'] = $user['first_name'];



    $_SESSION['last_name'] = $user['last_name'];



    $_SESSION['email'] = $user['email'];



    $_SESSION['user_type'] = $user['user_type'];



    



    return [



        'success' => true, 



        'message' => 'Login successful',



        'user_type' => $user['user_type']



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



function registerTourGuide($firstName, $lastName, $email, $password, $licenseNumber, $specialization, $experienceYears, $languages, $hourlyRate, $contactNumber, $bio) {



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



        $guideStmt = $conn->prepare("INSERT INTO tour_guides (user_id, license_number, specialization, experience_years, languages, hourly_rate, contact_number, bio) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");



        $guideStmt->bind_param("issisids", $userId, $licenseNumber, $specialization, $experienceYears, $languages, $hourlyRate, $contactNumber, $bio);



        



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
    require_once __DIR__ . '/../vendor/autoload.php';
    
    try {
        $mail = new PHPMailer(true);
        
        // SMTP configuration - UPDATE THIS with your new app password
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'christianbacay042504@gmail.com';
        $mail->Password = 'tayrkzczbhgehbej'; // New app password
        $mail->Port = 587;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        
        // Email content
        $mail->setFrom('christianbacay042504@gmail.com', 'SJDM Tours');
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

// Helper function to read environment variables
function readEnvValue($key) {
    $value = getenv($key);
    if ($value === false) {
        // Fallback to putenv values if getenv doesn't work
        $value = $_ENV[$key] ?? $_SERVER[$key] ?? null;
    }
    return $value;
}

?>



