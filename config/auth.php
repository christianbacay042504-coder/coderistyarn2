<?php
// Authentication Helper Functions
// Created: January 30, 2026

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
    $stmt = $conn->prepare("SELECT id, first_name, last_name, email, password, user_type, status, preferences_set FROM users WHERE email = ?");
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
    
    // Check if user has set preferences
    $preferencesSet = $user['preferences_set'] ?? 0;
    
    return [
        'success' => true, 
        'message' => 'Login successful',
        'user_type' => $user['user_type'],
        'preferences_set' => $preferencesSet
    ];
}

// Register new user
function registerUser($firstName, $lastName, $email, $password) {
    $conn = getDatabaseConnection();
    if (!$conn) {
        return ['success' => false, 'message' => 'Database connection failed'];
    }
    
    // Check if email already exists
    $checkStmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $checkStmt->bind_param("s", $email);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();
    
    if ($checkResult->num_rows > 0) {
        $checkStmt->close();
        closeDatabaseConnection($conn);
        return ['success' => false, 'message' => 'Email already exists'];
    }
    $checkStmt->close();
    
    // Hash password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    
    // Insert new user
    $stmt = $conn->prepare("INSERT INTO users (first_name, last_name, email, password, user_type) VALUES (?, ?, ?, ?, 'user')");
    $stmt->bind_param("ssss", $firstName, $lastName, $email, $hashedPassword);
    
    if ($stmt->execute()) {
        $userId = $stmt->insert_id;
        $stmt->close();
        closeDatabaseConnection($conn);
        
        return ['success' => true, 'message' => 'Registration successful', 'user_id' => $userId];
    } else {
        $stmt->close();
        closeDatabaseConnection($conn);
        return ['success' => false, 'message' => 'Registration failed'];
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

// Save user preferences
function saveUserPreferences($userId, $categories) {
    $conn = getDatabaseConnection();
    if (!$conn) {
        return ['success' => false, 'message' => 'Database connection failed'];
    }
    
    try {
        // Start transaction
        $conn->begin_transaction();
        
        // Clear existing preferences
        $deleteStmt = $conn->prepare("DELETE FROM user_preferences WHERE user_id = ?");
        $deleteStmt->bind_param("i", $userId);
        $deleteStmt->execute();
        $deleteStmt->close();
        
        // Insert new preferences
        $insertStmt = $conn->prepare("INSERT INTO user_preferences (user_id, category) VALUES (?, ?)");
        foreach ($categories as $category) {
            $insertStmt->bind_param("is", $userId, $category);
            $insertStmt->execute();
        }
        $insertStmt->close();
        
        // Mark preferences as set
        $updateStmt = $conn->prepare("UPDATE users SET preferences_set = 1 WHERE id = ?");
        $updateStmt->bind_param("i", $userId);
        $updateStmt->execute();
        $updateStmt->close();
        
        // Commit transaction
        $conn->commit();
        closeDatabaseConnection($conn);
        
        return ['success' => true, 'message' => 'Preferences saved successfully'];
        
    } catch (Exception $e) {
        $conn->rollback();
        closeDatabaseConnection($conn);
        return ['success' => false, 'message' => 'Error saving preferences: ' . $e->getMessage()];
    }
}

// Get user preferences
function getUserPreferences($userId) {
    $conn = getDatabaseConnection();
    if (!$conn) {
        return [];
    }
    
    $stmt = $conn->prepare("SELECT category FROM user_preferences WHERE user_id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $preferences = [];
    while ($row = $result->fetch_assoc()) {
        $preferences[] = $row['category'];
    }
    
    $stmt->close();
    closeDatabaseConnection($conn);
    
    return $preferences;
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
?>
