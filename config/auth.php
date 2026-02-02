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
        return ['success' => false, 'message' => 'Database connection failed'];
    }
    
    // Get user by email
    $stmt = $conn->prepare("SELECT id, first_name, last_name, email, password, user_type, status FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
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
    $updateStmt->bind_param("i", $user['id']);
    $updateStmt->execute();
    $updateStmt->close();
    
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
    $ipAddress = $_SERVER['REMOTE_ADDR'] ?? 'Unknown';
    $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';
    
    $stmt = $conn->prepare("INSERT INTO login_activity (user_id, ip_address, user_agent, status) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isss", $userId, $ipAddress, $userAgent, $status);
    $stmt->execute();
    $stmt->close();
}

// Logout user
function logoutUser() {
    session_unset();
    session_destroy();
}

// Redirect if not logged in
function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: /coderistyarn2/log-in/log-in.php');
        exit();
    }
}

// Redirect if not admin
function requireAdmin() {
    if (!isAdmin()) {
        header('Location: /coderistyarn2/log-in/log-in.php');
        exit();
    }
}
?>
