<?php
// Include database connection and authentication
require_once '../config/database.php';
require_once '../config/auth.php';

// Check if user is logged in (optional - for personalized content)
$isLoggedIn = isset($_SESSION['user_id']);

// Get current user data and preferences
$currentUser = ['name' => 'Guest', 'email' => ''];
$userPreferences = [];
$conn = getDatabaseConnection();
if ($conn && $isLoggedIn) {
    $stmt = $conn->prepare("SELECT first_name, last_name, email FROM users WHERE id = ?");
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $currentUser = [
            'name' => $user['first_name'] . ' ' . $user['last_name'],
            'email' => $user['email']
        ];
        
        // Get user preferences
        $prefStmt = $conn->prepare("SELECT category FROM user_preferences WHERE user_id = ?");
        $prefStmt->bind_param("i", $_SESSION['user_id']);
        $prefStmt->execute();
        $prefResult = $prefStmt->get_result();
        while ($pref = $prefResult->fetch_assoc()) {
            $userPreferences[] = $pref['category'];
        }
        $prefStmt->close();
    }
}

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if (empty($email) || empty($password)) {
        echo json_encode(['success' => false, 'message' => 'Email and password are required']);
        exit();
    }
    
    // Check if verification is required for this login
    $verificationRequired = false;
    if ($conn) {
        $checkStmt = $conn->prepare("SELECT verification_required FROM users WHERE email = ?");
        $checkStmt->bind_param("s", $email);
        $checkStmt->execute();
        $result = $checkStmt->get_result();
        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            $verificationRequired = $user['verification_required'] == 1;
        }
        $checkStmt->close();
    }
    
    if ($verificationRequired) {
        // Generate and send verification code
        $verificationCode = sprintf('%06d', mt_rand(100000, 999999));
        $_SESSION['login_verification_code'] = $verificationCode;
        $_SESSION['login_verification_email'] = $email;
        $_SESSION['login_verification_expires'] = time() + 600; // 10 minutes
        
        echo json_encode(['success' => true, 'verification_required' => true, 'message' => 'Verification code sent to your email']);
        exit();
    }
    
    // Regular login attempt
    $result = loginUser($email, $password);
    
    echo json_encode($result);
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>San Jose del Monte Bulacan - Tour Guide & Tourism</title>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary: #2c5f2d;
            --primary-light: #e8f5e9;
            --primary-dark: #1e4220;
            --secondary: #97bc62;
            --success: #10b981;
            --danger: #ef4444;
            --warning: #f59e0b;
            --info: #3b82f6;
            --text-primary: #1f2937;
            --text-secondary: #4b5563;
            --border: #e0e0e0;
            --bg-light: #f5f7fa;
            --white: #ffffff;
            --gray-50: #f9fafb;
            --gray-100: #f3f4f6;
            --gray-200: #e5e7eb;
            --gray-300: #d1d5db;
            --shadow-sm: 0 1px 2px rgba(0, 0, 0, 0.05);
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            --shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            --radius-sm: 0.375rem;
            --radius-md: 0.5rem;
            --radius-lg: 0.75rem;
            --radius-xl: 1rem;
            --transition: all 0.2s ease-in-out;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: #ffffff;
            color: var(--text-primary);
            line-height: 1.6;
            margin: 0;
            padding: 0;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Mobile Menu Toggle */
        .mobile-menu-toggle {
            display: none;
            position: fixed;
            top: 20px;
            left: 20px;
            z-index: 1000;
            background: var(--primary);
            color: white;
            border: none;
            border-radius: var(--radius-md);
            padding: 10px;
            cursor: pointer;
            box-shadow: var(--shadow-md);
        }

        /* User Preferences Section */
        .user-preferences-section {
            background: linear-gradient(135deg, rgba(44, 95, 45, 0.05), rgba(44, 95, 45, 0.02));
            border: 1px solid rgba(44, 95, 45, 0.1);
            border-radius: var(--radius-xl);
            padding: 24px;
            margin-bottom: 32px;
        }

        /* ===== USER PROFILE DROPDOWN ===== */
        .user-profile-dropdown {
            position: relative;
            display: inline-block;
            z-index: 1000;
        }

        .profile-trigger {
            display: flex;
            align-items: center;
            gap: 8px;
            background: none;
            border: 1px solid rgba(251, 255, 253, 1);
            cursor: pointer;
            color: #333;
            font-weight: 600;
            padding: 6px 12px;
            border-radius: 20px;
            transition: background 0.2s;
            box-shadow: 5px 10px 10px rgba(0, 0, 0, 0.1);
        }

        .profile-trigger:hover {
            background: #f0f0f0;
        }

        .profile-avatar,
        .profile-avatar-large {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: #2c5f2d;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 14px;
            flex-shrink: 0;
        }

        .profile-avatar-large {
            width: 56px;
            height: 56px;
            font-size: 20px;
            margin: 0 auto 12px;
        }

        .profile-name {
            display: none;
        }

        .dropdown-menu {
            position: absolute;
            top: 100%;
            right: 0;
            margin-top: 8px;
            width: 240px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            overflow: hidden;
            z-index: 1001;
            opacity: 0;
            visibility: hidden;
            transform: translateY(-10px);
            transition: all 0.2s ease;
        }

        .dropdown-menu.show {
            opacity: 1 !important;
            visibility: visible !important;
            transform: translateY(0) !important;
        }

        .dropdown-header {
            padding: 16px;
            background: #f9f9f9;
            text-align: center;
            border-bottom: 1px solid #eee;
        }

        .dropdown-header h4 {
            margin: 8px 0 4px;
            font-size: 16px;
            color: #333;
        }

        .dropdown-header p {
            font-size: 13px;
            color: #777;
            margin: 0;
        }

        .dropdown-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 16px;
            text-decoration: none;
            color: #444;
            transition: background 0.2s;
        }

        .dropdown-item:hover {
            background: #f5f5f5;
        }

        .dropdown-item .material-icons-outlined {
            font-size: 20px;
            color: #555;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .profile-name {
                display: inline-block;
                font-size: 14px;
            }

            .dropdown-menu {
                width: 280px;
            }
        }

        .preferences-display {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            align-items: center;
            justify-content: center;
        }

        .preference-tag {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: white;
            padding: 12px 20px;
            border-radius: 25px;
            font-size: 0.9rem;
            font-weight: 600;
            box-shadow: var(--shadow-md);
            transition: var(--transition);
            border: 2px solid transparent;
        }

        .preference-tag:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
            border-color: rgba(255, 255, 255, 0.2);
        }

        .preference-tag .material-icons-outlined {
            font-size: 18px;
            opacity: 0.9;
        }

        /* Responsive adjustments for preferences */
        @media (max-width: 768px) {
            .user-preferences-section {
                padding: 20px;
                margin-bottom: 24px;
            }

            .preferences-display {
                gap: 10px;
            }

            .preference-tag {
                padding: 10px 16px;
                font-size: 0.85rem;
            }

            .preference-tag .material-icons-outlined {
                font-size: 16px;
            }
        }

        @media (max-width: 480px) {
            .preferences-display {
                justify-content: flex-start;
            }

            .preference-tag {
                padding: 8px 14px;
                font-size: 0.8rem;
            }
        }

        /* Modal Styles */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
        }

        .modal-overlay.show {
            opacity: 1;
            visibility: visible;
        }

        .modal-content {
            background: white;
            border-radius: 16px;
            max-width: 400px;
            width: 90%;
            max-height: 80vh;
            overflow-y: auto;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            transform: scale(0.9);
            transition: all 0.3s ease;
        }

        .modal-overlay.show .modal-content {
            transform: scale(1);
        }

        .modal-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 20px;
            border-bottom: 1px solid #eee;
        }

        .modal-header h2 {
            margin: 0;
            font-size: 1.5rem;
            color: var(--text-primary);
        }

        .close-modal {
            background: none;
            border: none;
            cursor: pointer;
            padding: 8px;
            border-radius: 50%;
            color: var(--text-secondary);
            transition: all 0.2s;
        }

        .close-modal:hover {
            background: var(--bg-light);
            color: var(--text-primary);
        }

        .modal-body {
            padding: 20px;
        }

        .logout-message {
            text-align: center;
            margin-bottom: 20px;
        }

        .logout-icon {
            width: 48px;
            height: 48px;
            background: var(--danger);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 16px;
        }

        .logout-message h3 {
            margin: 16px 0 8px;
            color: var(--text-primary);
        }

        .logout-message p {
            color: var(--text-secondary);
            margin-bottom: 24px;
        }

        .modal-actions {
            display: flex;
            gap: 12px;
            justify-content: center;
        }

        .btn-cancel,
        .btn-confirm-logout {
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .btn-cancel {
            background: var(--gray-100);
            color: var(--text-secondary);
        }

        .btn-cancel:hover {
            background: var(--gray-200);
        }

        .btn-confirm-logout {
            background: var(--danger);
            color: white;
        }

        .btn-confirm-logout:hover {
            background: #dc2626;
        }

        /* Sidebar */
        .sidebar {
            width: 280px;
            background: var(--white);
            padding: 24px;
            display: flex;
            flex-direction: column;
            border-right: 1px solid var(--border);
            height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            overflow-y: auto;
            z-index: 1000;
            transition: transform 0.3s ease;
        }

        .sidebar-logo {
            margin-bottom: 40px;
        }

        .sidebar-logo h1 {
            font-size: 24px;
            font-weight: 700;
            color: var(--primary);
        }

        .sidebar-logo p {
            font-size: 12px;
            color: var(--text-secondary);
            margin-top: 4px;
        }

        .sidebar-nav {
            display: flex;
            flex-direction: column;
            gap: 4px;
            flex: 1;
        }

        .nav-item {
            display: flex;
            align-items: center;
            padding: 12px 16px;
            border-radius: var(--radius-md);
            color: var(--text-secondary);
            text-decoration: none;
            transition: all 0.3s ease;
            cursor: pointer;
            position: relative;
            overflow: hidden;
            font-size: 14px;
        }

        .nav-item:hover {
            background: var(--primary-light);
            color: var(--primary);
            transform: translateX(4px);
            box-shadow: 0 2px 8px rgba(44, 95, 45, 0.15);
        }

        .nav-item.active {
            background: var(--primary);
            color: white;
            transform: translateX(4px);
            box-shadow: 0 4px 12px rgba(44, 95, 45, 0.3);
        }

        .nav-item .material-icons-outlined {
            margin-right: 12px;
            font-size: 20px;
            transition: transform 0.3s ease;
        }

        .nav-item:hover .material-icons-outlined {
            transform: scale(1.1);
        }

        .nav-item.active .material-icons-outlined {
            transform: scale(1.1);
        }

        /* Main Content */
        .main-content {
            flex: 1;
            margin-left: 280px;
            min-height: 100vh;
            background: #ffffff;
        }

        .main-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 32px 40px;
            background: linear-gradient(135deg,
                    rgba(255, 255, 255, 0.98) 0%,
                    rgba(255, 255, 255, 0.95) 25%,
                    rgba(248, 250, 252, 0.92) 50%,
                    rgba(241, 245, 249, 0.88) 100%);
            box-shadow:
                0 20px 60px rgba(0, 0, 0, 0.08),
                0 8px 24px rgba(0, 0, 0, 0.04);
            position: sticky;
            top: 0;
            z-index: 100;
            gap: 24px;
            border-bottom: 3px solid rgba(44, 95, 45, 0.1);
            backdrop-filter: blur(10px);
        }

        .main-header h1 {
            font-size: 2.2rem;
            font-weight: 800;
            color: var(--text-primary);
            text-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            letter-spacing: -1px;
            position: relative;
            z-index: 1;
        }

        .search-bar {
            position: relative;
            flex: 1;
            max-width: 500px;
        }

        .search-bar input {
            width: 100%;
            padding: 16px 20px 16px 52px;
            border: 2px solid rgba(255, 255, 255, 0.2);
            border-radius: 16px;
            font-size: 15px;
            background: linear-gradient(135deg,
                    rgba(255, 255, 255, 0.95) 0%,
                    rgba(255, 255, 255, 0.85) 100%);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow:
                0 8px 32px rgba(0, 0, 0, 0.1),
                inset 0 1px 0 rgba(255, 255, 255, 0.8);
            color: var(--text-primary);
            backdrop-filter: blur(10px);
        }

        .search-bar input:focus {
            outline: none;
            border-color: rgba(255, 255, 255, 0.4);
            box-shadow:
                0 12px 40px rgba(44, 95, 45, 0.2),
                0 0 0 4px rgba(255, 255, 255, 0.1),
                inset 0 1px 0 rgba(255, 255, 255, 0.9);
            transform: translateY(-2px);
        }

        .search-bar input::placeholder {
            color: rgba(44, 95, 45, 0.6);
            font-weight: 500;
        }

        .search-bar .material-icons-outlined {
            position: absolute;
            left: 20px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--primary);
            font-size: 22px;
            z-index: 1;
        }

        .header-actions {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .icon-button {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg,
                    rgba(255, 255, 255, 0.95) 0%,
                    rgba(255, 255, 255, 0.85) 100%);
            border: 2px solid rgba(255, 255, 255, 0.3);
            color: var(--primary);
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            box-shadow:
                0 8px 24px rgba(0, 0, 0, 0.1),
                inset 0 1px 0 rgba(255, 255, 255, 0.8);
        }

        .icon-button:hover {
            background: linear-gradient(135deg,
                    rgba(255, 255, 255, 1) 0%,
                    rgba(248, 250, 252, 1) 100%);
            transform: translateY(-3px) scale(1.05);
            box-shadow:
                0 12px 32px rgba(44, 95, 45, 0.2),
                0 0 0 4px rgba(255, 255, 255, 0.2);
        }

        .notification-badge {
            position: absolute;
            top: -4px;
            right: -4px;
            background: linear-gradient(135deg, var(--danger) 0%, #dc2626 100%);
            color: white;
            font-size: 11px;
            font-weight: 700;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 8px rgba(239, 68, 68, 0.4);
        }

        .hero {
            background: linear-gradient(135deg,
                    rgba(44, 95, 45, 0.95) 0%,
                    rgba(34, 75, 35, 0.9) 25%,
                    rgba(24, 55, 25, 0.85) 50%,
                    rgba(14, 35, 15, 0.8) 100%),
                url('https://images.unsplash.com/photo-1464822759023-fed622ff2c3b?q=80&w=2070&auto=format&fit=crop') center/cover;
            background-blend-mode: overlay;
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            padding: 120px 40px 80px;
            text-align: center;
            color: white;
            position: relative;
            overflow: hidden;
            border-radius: 0 0 30px 30px;
            margin-bottom: 60px;
        }

        .hero h1 {
            font-size: 3.5rem;
            font-weight: 800;
            margin-bottom: 24px;
            text-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
            letter-spacing: -2px;
            background: linear-gradient(135deg, #ffffff 0%, #f0f9ff 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            animation: fadeInUp 1s ease-out;
        }

        .hero p {
            font-size: 1.4rem;
            margin-bottom: 40px;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
            line-height: 1.6;
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
            opacity: 0.95;
            animation: fadeInUp 1s ease-out 0.2s both;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .btn-hero {
            display: inline-flex;
            align-items: center;
            gap: 12px;
            padding: 18px 36px;
            background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
            color: var(--primary);
            border: none;
            border-radius: 50px;
            font-size: 1.1rem;
            font-weight: 700;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
            text-transform: uppercase;
            letter-spacing: 1px;
            position: relative;
            overflow: hidden;
            animation: fadeInUp 1s ease-out 0.4s both;
        }

        .btn-hero:hover {
            transform: translateY(-3px) scale(1.05);
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.3);
            background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
        }

        .btn-hero:active {
            transform: translateY(-1px) scale(1.02);
        }

        .content-area {
            padding: 32px;
            max-width: 1400px;
            margin: 0 auto;
        }

        .section-title {
            font-size: 2.5rem;
            font-weight: 800;
            color: var(--text-primary);
            margin-bottom: 40px;
            text-align: center;
            position: relative;
            padding-bottom: 20px;
            letter-spacing: -1px;
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .section-title::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 4px;
            background: linear-gradient(90deg, var(--primary) 0%, var(--secondary) 100%);
            border-radius: 2px;
        }

        .destinations-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 32px;
            margin-bottom: 60px;
        }

        .destination-card {
            background: linear-gradient(135deg, #ffffff 0%, #fafbfc 100%);
            border-radius: 20px;
            overflow: hidden;
            box-shadow:
                0 10px 30px rgba(0, 0, 0, 0.1),
                0 1px 8px rgba(0, 0, 0, 0.05);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            border: 1px solid rgba(44, 95, 45, 0.08);
        }

        .destination-card:hover {
            transform: translateY(-8px) scale(1.02);
            box-shadow:
                0 20px 60px rgba(44, 95, 45, 0.15),
                0 10px 20px rgba(0, 0, 0, 0.1);
            border-color: rgba(44, 95, 45, 0.2);
        }

        .destination-img {
            width: 100%;
            height: 240px;
            overflow: hidden;
            position: relative;
        }

        .destination-img img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.6s ease;
        }

        .destination-card:hover .destination-img img {
            transform: scale(1.1);
        }

        .destination-content {
            padding: 28px;
            position: relative;
        }

        .destination-content h3 {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 16px;
            letter-spacing: -0.5px;
            line-height: 1.3;
        }

        .destination-content p {
            color: var(--text-secondary);
            font-size: 1rem;
            line-height: 1.6;
            margin-bottom: 16px;
        }

        .destination-meta {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-top: 16px;
            flex-wrap: wrap;
        }

        .destination-meta .rating {
            display: flex;
            align-items: center;
            gap: 4px;
            color: var(--warning);
            font-weight: 600;
            font-size: 0.9rem;
        }

        .destination-meta .rating .material-icons-outlined {
            font-size: 16px;
        }

        .destination-meta .category {
            background: var(--primary-light);
            color: var(--primary-dark);
            padding: 4px 12px;
            border-radius: var(--radius-sm);
            font-size: 0.8rem;
            font-weight: 500;
            text-transform: capitalize;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 24px;
            margin: 60px 0;
        }

        .stat-card {
            background: linear-gradient(135deg, var(--primary) 0%, #4a7c4e 100%);
            color: white;
            padding: 40px 32px;
            border-radius: 20px;
            text-align: center;
            box-shadow:
                0 10px 30px rgba(44, 95, 45, 0.2),
                0 5px 15px rgba(44, 95, 45, 0.1);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow:
                0 15px 40px rgba(44, 95, 45, 0.3),
                0 8px 20px rgba(44, 95, 45, 0.2);
        }

        .stat-card h3 {
            font-size: 3rem;
            font-weight: 800;
            margin-bottom: 12px;
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
        }

        .stat-card p {
            font-size: 1.1rem;
            opacity: 0.95;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        /* Full-width layout styles */
        .main-content.full-width {
            margin-left: 0;
            max-width: 100%;
        }

        .main-content.full-width .main-header {
            padding: 30px 40px;
            background: white;
            border-bottom: 1px solid var(--border);
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 40px;
        }

        .header-left {
            display: flex;
            align-items: center;
            gap: 30px;
        }

        .header-right {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .header-nav {
            display: flex;
            align-items: center;
            gap: 8px;
            background: var(--gray-50);
            padding: 4px;
            border-radius: 12px;
        }

        .nav-link {
            display: flex;
            align-items: center;
            gap: 6px;
            padding: 8px 16px;
            text-decoration: none;
            color: var(--text-secondary);
            font-weight: 500;
            font-size: 14px;
            border-radius: 8px;
            transition: all 0.2s ease;
        }

        .nav-link:hover {
            background: white;
            color: var(--primary);
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .nav-link.active {
            background: var(--primary);
            color: white;
        }

        .nav-link .material-icons-outlined {
            font-size: 18px;
        }

        .btn-signin {
            background: #2563eb;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 500;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .btn-signin:hover {
            background: #1d4ed8;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
        }

        .main-content.full-width .content-area {
            padding: 40px;
            max-width: 1400px;
            margin: 0 auto;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .mobile-menu-toggle {
                display: flex;
            }

            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.active {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
            }

            .main-content.full-width .main-header {
                padding: 20px;
                flex-direction: column;
                gap: 20px;
                align-items: stretch;
            }
            
            .header-left {
                flex-direction: column;
                gap: 15px;
                align-items: stretch;
            }
            
            .header-right {
                justify-content: center;
            }
            
            .header-nav {
                flex-wrap: wrap;
                justify-content: center;
                gap: 4px;
                padding: 6px;
            }
            
            .nav-link {
                padding: 6px 12px;
                font-size: 12px;
                gap: 4px;
            }
            
            .nav-link .material-icons-outlined {
                font-size: 16px;
            }

            .main-header h1 {
                font-size: 1.8rem;
            }

            .search-bar {
                max-width: none;
            }

            .hero {
                padding: 80px 20px 60px;
            }

            .hero h1 {
                font-size: 2.5rem;
            }

            .hero p {
                font-size: 1.2rem;
            }

            .section-title {
                font-size: 2rem;
            }

            .destinations-grid {
                grid-template-columns: 1fr;
                gap: 20px;
            }

            .stats-grid {
                grid-template-columns: 1fr;
                gap: 16px;
            }

            .main-content.full-width .content-area {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <!-- MAIN CONTENT -->
    <main class="main-content full-width">
        <header class="main-header">
            <div class="header-left">
                <h1 id="pageTitle">Dashboard</h1>
                <div class="search-bar">
                    <span class="material-icons-outlined">search</span>
                    <input type="text" placeholder="Search destinations or guides...">
                </div>
            </div>
            <div class="header-right">
                <nav class="header-nav">
                    <a href="../index.php" class="nav-link active">
                        <span class="material-icons-outlined">home</span>
                        <span>Home</span>
                    </a>
                </nav>
            </div>
            <div class="header-right">
                <nav class="header-nav">
                    <a href="index.php" class="nav-link">
                        <span class="material-icons-outlined">dashboard</span>
                        <span>Dashboard</span>
                    </a>
                    <a href="user-guides.php" class="nav-link">
                        <span class="material-icons-outlined">people</span>
                        <span>Tour Guides</span>
                    </a>
                    <a href="book.php" class="nav-link">
                        <span class="material-icons-outlined">event</span>
                        <span>Book Now</span>
                    </a>
                    <a href="booking-history.php" class="nav-link">
                        <span class="material-icons-outlined">history</span>
                        <span>Booking History</span>
                    </a>
                    <a href="tourist-spots.php" class="nav-link">
                        <span class="material-icons-outlined">place</span>
                        <span>Tourist Spots</span>
                    </a>
                    <a href="local-culture.php" class="nav-link">
                        <span class="material-icons-outlined">theater_comedy</span>
                        <span>Local Culture</span>
                    </a>
                    <a href="travel-tips.php" class="nav-link">
                        <span class="material-icons-outlined">tips_and_updates</span>
                        <span>Travel Tips</span>
                    </a>
                </nav>
                <div class="header-actions">
                    <button class="btn-signin" onclick="window.location.href='logout.php'">Sign in/register</button>
                </div>
            </div>
        </header>

        <div class="content-area">
            <div class="hero">
                <h1><?php echo htmlspecialchars($homepageContent['hero_title']['main_title'] ?? 'Welcome to San Jose del Monte, Bulacan'); ?></h1>
                <p><?php echo htmlspecialchars($homepageContent['hero_subtitle']['main_subtitle'] ?? 'The Balcony of Metropolis - Where Nature Meets Progress'); ?></p>
                <button class="btn-hero" onclick="window.location.href='user-guides.php'">
                    <?php echo htmlspecialchars($homepageContent['hero_button_text']['main_button'] ?? 'Find Your Guide'); ?>
                </button>
            </div>

            <?php if (!empty($userPreferences)): ?>
            <div class="user-preferences-section">
                <h2 class="section-title">Your Interests</h2>
                <div class="preferences-display">
                    <?php 
                    $categoryMap = [
                        'nature' => 'Nature & Waterfalls',
                        'farm' => 'Farms & Eco-Tourism', 
                        'park' => 'Parks & Recreation',
                        'adventure' => 'Adventure & Activities',
                        'cultural' => 'Cultural & Historical',
                        'religious' => 'Religious Sites',
                        'entertainment' => 'Entertainment & Leisure',
                        'food' => 'Food & Dining',
                        'shopping' => 'Shopping & Markets',
                        'wellness' => 'Wellness & Relaxation',
                        'education' => 'Educational & Learning',
                        'family' => 'Family-Friendly',
                        'photography' => 'Photography Spots',
                        'wildlife' => 'Wildlife & Nature',
                        'outdoor' => 'Outdoor Activities'
                    ];

                    $iconMap = [
                        'nature' => 'forest',
                        'farm' => 'agriculture',
                        'park' => 'park',
                        'adventure' => 'hiking',
                        'cultural' => 'museum',
                        'religious' => 'church',
                        'entertainment' => 'sports_esports',
                        'food' => 'restaurant',
                        'shopping' => 'shopping_cart',
                        'wellness' => 'spa',
                        'education' => 'school',
                        'family' => 'family_restroom',
                        'photography' => 'photo_camera',
                        'wildlife' => 'pets',
                        'outdoor' => 'terrain'
                    ];
                    
                    foreach ($userPreferences as $preference): ?>
                        <div class="preference-tag">
                            <span class="material-icons-outlined">
                                <?php echo $iconMap[$preference] ?? 'category'; ?>
                            </span>
                            <?php echo htmlspecialchars($categoryMap[$preference] ?? $preference); ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

            <h2 class="section-title">Featured Destinations for You</h2>
            <div class="destinations-grid">
                <?php if (!empty($featuredSpots)): ?>
                    <?php foreach ($featuredSpots as $spot): ?>
                        <div class="destination-card">
                            <div class="destination-img">
                                <img src="<?php echo htmlspecialchars($spot['image_url'] ?? 'https://via.placeholder.com/400x300/2c5f2d/ffffff?text=' . urlencode($spot['name'])); ?>" alt="<?php echo htmlspecialchars($spot['name']); ?>">
                            </div>
                            <div class="destination-content">
                                <h3><?php echo htmlspecialchars($spot['name']); ?></h3>
                                <p><?php echo htmlspecialchars($spot['description']); ?></p>
                                <div class="destination-meta">
                                    <span class="rating">
                                        <span class="material-icons-outlined">star</span>
                                        <?php echo number_format($spot['rating'], 1); ?>
                                    </span>
                                    <span class="category"><?php echo ucfirst(htmlspecialchars($spot['category'])); ?></span>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="no-destinations">
                        <p>No featured destinations available at the moment.</p>
                    </div>
                <?php endif; ?>
            </div>

            <h2 class="section-title"><?php echo htmlspecialchars($homepageContent['section_title']['why_visit'] ?? 'Why Visit San Jose del Monte?'); ?></h2>
            <div class="stats-grid">
                <?php if (!empty($homepageContent['stat_title'])): ?>
                    <?php foreach ($homepageContent['stat_title'] as $key => $title): ?>
                        <div class="stat-card">
                            <h3><?php echo htmlspecialchars($homepageContent['stat_value'][$key] ?? '0'); ?></h3>
                            <p><?php echo htmlspecialchars($title); ?></p>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="stat-card">
                        <h3>10+</h3>
                        <p>Natural Attractions</p>
                    </div>
                    <div class="stat-card">
                        <h3>30 min</h3>
                        <p>From Metro Manila</p>
                    </div>
                    <div class="stat-card">
                        <h3>Year-round</h3>
                        <p>Perfect Climate</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <script>
        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function () {
            // Profile dropdown functionality removed
        });
    </script>
</body>
</html>