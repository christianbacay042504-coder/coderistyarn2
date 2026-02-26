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

// Fetch featured destinations from admin database
$featuredSpots = [];
if ($conn) {
    $query = "SELECT * FROM tourist_spots WHERE status = 'active' ORDER BY rating DESC, review_count DESC LIMIT 6";
    $result = $conn->query($query);
    if ($result && $result->num_rows > 0) {
        while ($spot = $result->fetch_assoc()) {
            $featuredSpots[] = $spot;
        }
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
<?php
// Get logo file content and convert to base64
$logoPath = __DIR__ . '/../lgo.png';
$logoData = file_get_contents($logoPath);
$logoBase64 = base64_encode($logoData);
$logoMime = 'image/png';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>San Jose del Monte Bulacan - Tour Guide & Tourism</title>
    <link rel="icon" type="image/png" href="data:<?php echo $logoMime; ?>;base64,<?php echo $logoBase64; ?>">
    <link rel="shortcut icon" type="image/png" href="data:<?php echo $logoMime; ?>;base64,<?php echo $logoBase64; ?>">
    <link rel="apple-touch-icon" href="data:<?php echo $logoMime; ?>;base64,<?php echo $logoBase64; ?>">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary: #2c5f2d;
            --primary-light: #e8f5e9;
            --primary-dark: #1e4220;
            --secondary: #97bc62;
            --accent: #ff6b6b;
            --accent-light: #ffe0e0;
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
            --shadow-2xl: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            --radius-sm: 0.375rem;
            --radius-md: 0.5rem;
            --radius-lg: 0.75rem;
            --radius-xl: 1rem;
            --radius-2xl: 1.5rem;
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
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

        .btn-signin {
            background: #2563eb;
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .btn-signin:hover {
            background: #1d4ed8;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
        }

        .hero {
            background: 
                linear-gradient(135deg,
                    rgba(44, 95, 45, 0.9) 0%,
                    rgba(34, 75, 35, 0.85) 25%,
                    rgba(24, 55, 25, 0.8) 50%,
                    rgba(14, 35, 15, 0.75) 100%),
                radial-gradient(circle at 20% 80%, rgba(151, 188, 98, 0.3) 0%, transparent 50%),
                radial-gradient(circle at 80% 20%, rgba(255, 107, 107, 0.2) 0%, transparent 50%),
                url('https://images.unsplash.com/photo-1464822759023-fed622ff2c3b?q=80&w=2070&auto=format&fit=crop') center/cover;
            background-blend-mode: overlay, normal, normal, overlay;
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            padding: 140px 40px 100px;
            text-align: center;
            color: white;
            position: relative;
            overflow: hidden;
            border-radius: 0 0 40px 40px;
            margin-bottom: 80px;
            min-height: 600px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }

        .hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: 
                radial-gradient(circle at 30% 70%, rgba(255, 255, 255, 0.1) 0%, transparent 40%),
                radial-gradient(circle at 70% 30%, rgba(255, 255, 255, 0.08) 0%, transparent 40%);
            pointer-events: none;
        }

        .hero::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, 
                var(--accent) 0%, 
                var(--secondary) 25%, 
                var(--primary) 50%, 
                var(--secondary) 75%, 
                var(--accent) 100%);
            animation: shimmer 3s ease-in-out infinite;
        }

        @keyframes shimmer {
            0%, 100% { opacity: 0.8; transform: translateX(-10px); }
            50% { opacity: 1; transform: translateX(10px); }
        }

        .hero h1 {
            font-size: 4rem;
            font-weight: 900;
            margin-bottom: 32px;
            text-shadow: 0 6px 30px rgba(0, 0, 0, 0.4);
            letter-spacing: -2px;
            background: linear-gradient(135deg, 
                #ffffff 0%, 
                #f0f9ff 30%, 
                #e0f2fe 60%, 
                #ffffff 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            animation: fadeInUp 1s ease-out, glow 3s ease-in-out infinite alternate;
            position: relative;
            z-index: 2;
        }

        @keyframes glow {
            from { filter: drop-shadow(0 0 20px rgba(255, 255, 255, 0.3)); }
            to { filter: drop-shadow(0 0 30px rgba(255, 255, 255, 0.5)); }
        }

        .hero p {
            font-size: 1.5rem;
            margin-bottom: 48px;
            max-width: 700px;
            margin-left: auto;
            margin-right: auto;
            line-height: 1.7;
            text-shadow: 0 3px 15px rgba(0, 0, 0, 0.3);
            opacity: 0.95;
            animation: fadeInUp 1s ease-out 0.2s both;
            position: relative;
            z-index: 2;
            font-weight: 400;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(40px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .btn-hero {
            display: inline-flex;
            align-items: center;
            gap: 16px;
            padding: 20px 40px;
            background: linear-gradient(135deg, 
                rgba(255, 255, 255, 0.95) 0%, 
                rgba(248, 250, 252, 0.9) 50%, 
                rgba(240, 249, 255, 0.85) 100%);
            color: var(--primary);
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-radius: 60px;
            font-size: 1.2rem;
            font-weight: 700;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 
                0 10px 40px rgba(0, 0, 0, 0.2),
                0 0 0 1px rgba(255, 255, 255, 0.1),
                inset 0 1px 0 rgba(255, 255, 255, 0.5);
            text-transform: uppercase;
            letter-spacing: 1.5px;
            position: relative;
            overflow: hidden;
            animation: fadeInUp 1s ease-out 0.4s both;
            backdrop-filter: blur(10px);
            z-index: 2;
        }

        .btn-hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, 
                transparent, 
                rgba(255, 255, 255, 0.4), 
                transparent);
            transition: left 0.6s ease;
        }

        .btn-hero:hover::before {
            left: 100%;
        }

        .btn-hero:hover {
            transform: translateY(-4px) scale(1.05);
            box-shadow: 
                0 20px 60px rgba(0, 0, 0, 0.3),
                0 0 0 2px rgba(255, 255, 255, 0.2),
                inset 0 1px 0 rgba(255, 255, 255, 0.7);
            background: linear-gradient(135deg, 
                rgba(255, 255, 255, 1) 0%, 
                rgba(240, 249, 255, 0.95) 50%, 
                rgba(224, 242, 254, 0.9) 100%);
        }

        .btn-hero:active {
            transform: translateY(-2px) scale(1.02);
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
            border-radius: 24px;
            overflow: hidden;
            box-shadow:
                0 10px 40px rgba(0, 0, 0, 0.08),
                0 2px 10px rgba(0, 0, 0, 0.04);
            transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            border: 1px solid rgba(44, 95, 45, 0.06);
            transform-style: preserve-3d;
            perspective: 1000px;
        }

        .destination-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--accent) 0%, var(--secondary) 50%, var(--primary) 100%);
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .destination-card:hover::before {
            opacity: 1;
        }

        .destination-card:hover {
            transform: translateY(-12px) rotateX(2deg) rotateY(-2deg);
            box-shadow:
                0 25px 80px rgba(44, 95, 45, 0.15),
                0 15px 30px rgba(0, 0, 0, 0.1),
                0 0 0 1px rgba(44, 95, 45, 0.1);
            border-color: rgba(44, 95, 45, 0.15);
        }

        .destination-img {
            width: 100%;
            height: 280px;
            overflow: hidden;
            position: relative;
            background: linear-gradient(135deg, var(--primary-light) 0%, var(--primary) 100%);
        }

        .destination-img::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(180deg, 
                transparent 0%, 
                transparent 70%, 
                rgba(0, 0, 0, 0.1) 100%);
            pointer-events: none;
        }

        .destination-img img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: all 0.8s cubic-bezier(0.4, 0, 0.2, 1);
            filter: brightness(0.95);
        }

        .destination-card:hover .destination-img img {
            transform: scale(1.15) rotate(1deg);
            filter: brightness(1.05);
        }

        .destination-content {
            padding: 32px;
            position: relative;
            background: linear-gradient(135deg, #ffffff 0%, #fafbfc 100%);
        }

        .destination-content::before {
            content: '';
            position: absolute;
            top: -20px;
            left: 50%;
            transform: translateX(-50%);
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            border-radius: 50%;
            opacity: 0;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(44, 95, 45, 0.3);
        }

        .destination-card:hover .destination-content::before {
            opacity: 1;
            transform: translateX(-50%) scale(1.1);
        }

        .destination-content h3 {
            font-size: 1.6rem;
            font-weight: 800;
            color: var(--text-primary);
            margin-bottom: 20px;
            letter-spacing: -0.5px;
            line-height: 1.3;
            background: linear-gradient(135deg, var(--text-primary) 0%, var(--primary) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            transition: all 0.3s ease;
        }

        .destination-card:hover .destination-content h3 {
            transform: translateX(4px);
        }

        .destination-content p {
            color: var(--text-secondary);
            font-size: 1rem;
            line-height: 1.7;
            margin-bottom: 24px;
            font-weight: 400;
        }

        .destination-meta {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid rgba(44, 95, 45, 0.08);
        }

        .destination-meta .rating {
            display: flex;
            align-items: center;
            gap: 6px;
            color: var(--warning);
            font-weight: 700;
            font-size: 0.95rem;
            background: linear-gradient(135deg, rgba(245, 158, 11, 0.1) 0%, rgba(245, 158, 11, 0.05) 100%);
            padding: 8px 16px;
            border-radius: 20px;
            border: 1px solid rgba(245, 158, 11, 0.2);
            transition: all 0.3s ease;
        }

        .destination-meta .rating:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(245, 158, 11, 0.2);
        }

        .destination-meta .rating .material-icons-outlined {
            font-size: 18px;
            filter: drop-shadow(0 1px 2px rgba(245, 158, 11, 0.3));
        }

        .destination-meta .category {
            background: linear-gradient(135deg, var(--primary-light) 0%, var(--primary) 100%);
            color: white;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
            text-transform: capitalize;
            border: 1px solid rgba(44, 95, 45, 0.2);
            box-shadow: 0 2px 8px rgba(44, 95, 45, 0.2);
            transition: all 0.3s ease;
        }

        .destination-meta .category:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(44, 95, 45, 0.3);
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
            padding: 48px 40px;
            border-radius: 24px;
            text-align: center;
            box-shadow:
                0 20px 60px rgba(44, 95, 45, 0.25),
                0 8px 25px rgba(44, 95, 45, 0.15);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
            transform-style: preserve-3d;
            perspective: 1000px;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: 
                radial-gradient(circle at 20% 80%, rgba(255, 255, 255, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 80% 20%, rgba(255, 255, 255, 0.08) 0%, transparent 50%);
            pointer-events: none;
        }

        .stat-card::after {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: linear-gradient(45deg, 
                transparent 30%, 
                rgba(255, 255, 255, 0.1) 50%, 
                transparent 70%);
            transform: rotate(45deg);
            transition: all 0.6s ease;
            opacity: 0;
        }

        .stat-card:hover::after {
            animation: shine 0.6s ease-in-out;
        }

        @keyframes shine {
            0% { transform: rotate(45deg) translateX(-100%); opacity: 0; }
            50% { opacity: 1; }
            100% { transform: rotate(45deg) translateX(100%); opacity: 0; }
        }

        .stat-card:hover {
            transform: translateY(-8px) rotateX(2deg) rotateY(-2deg);
            box-shadow:
                0 30px 80px rgba(44, 95, 45, 0.35),
                0 15px 40px rgba(44, 95, 45, 0.2),
                0 0 0 2px rgba(255, 255, 255, 0.1);
        }

        .stat-card h3 {
            font-size: 3.5rem;
            font-weight: 900;
            margin-bottom: 16px;
            text-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
            background: linear-gradient(135deg, #ffffff 0%, #f0f9ff 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            position: relative;
            z-index: 2;
            animation: countUp 2s ease-out;
        }

        @keyframes countUp {
            from { 
                opacity: 0; 
                transform: translateY(20px) scale(0.8); 
            }
            to { 
                opacity: 1; 
                transform: translateY(0) scale(1); 
            }
        }

        .stat-card p {
            font-size: 1.2rem;
            opacity: 0.95;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
            position: relative;
            z-index: 2;
        }

        .stat-card .stat-icon {
            position: absolute;
            top: 20px;
            right: 20px;
            font-size: 3rem;
            opacity: 0.2;
            transform: rotate(-15deg);
            transition: all 0.3s ease;
        }

        .stat-card:hover .stat-icon {
            opacity: 0.3;
            transform: rotate(0deg) scale(1.1);
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

        .dropdown-divider {
            height: 1px;
            background: #eee;
            margin: 4px 0;
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

        /* ===== BOOKING HISTORY MODAL STYLES ===== */
        .modal-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.6);
            backdrop-filter: blur(4px);
            z-index: 10000;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .modal-overlay.show {
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 1;
        }

        .modal-content {
            background: white;
            border-radius: 16px;
            width: 90%;
            max-width: 800px;
            max-height: 90vh;
            overflow: hidden;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
            transform: translateY(20px);
            transition: transform 0.3s ease;
        }

        .modal-overlay.show .modal-content {
            transform: translateY(0);
        }

        .booking-modal .modal-header {
            background: linear-gradient(135deg, #2c5f2d 0%, #1a3d1a 100%);
            color: white;
            padding: 24px 32px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-title {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .modal-icon {
            font-size: 28px;
        }

        .modal-title h2 {
            margin: 0;
            font-size: 1.5rem;
            font-weight: 600;
        }

        .close-modal {
            background: rgba(255, 255, 255, 0.2);
            border: none;
            border-radius: 8px;
            width: 36px;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            color: white;
            transition: background 0.2s;
        }

        .close-modal:hover {
            background: rgba(255, 255, 255, 0.3);
        }

        .modal-body {
            padding: 32px;
            max-height: calc(90vh - 120px);
            overflow-y: auto;
        }

        .booking-filters {
            display: flex;
            gap: 8px;
            margin-bottom: 24px;
            flex-wrap: wrap;
        }

        .filter-btn {
            padding: 8px 16px;
            border: 2px solid #e0e0e0;
            background: white;
            border-radius: 20px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.2s;
        }

        .filter-btn:hover {
            border-color: #2c5f2d;
            background: #f8f9f8;
        }

        .filter-btn.active {
            background: #2c5f2d;
            color: white;
            border-color: #2c5f2d;
        }

        .bookings-list {
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .booking-item {
            border: 1px solid #e0e0e0;
            border-radius: 12px;
            padding: 20px;
            background: white;
            transition: all 0.2s;
        }

        .booking-item:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            transform: translateY(-2px);
        }

        .booking-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 16px;
        }

        .booking-info h4 {
            margin: 0 0 8px 0;
            font-size: 1.1rem;
            font-weight: 600;
            color: #333;
        }

        .booking-info p {
            margin: 0;
            color: #666;
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 14px;
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .status-badge.status-pending {
            background: #fff3cd;
            color: #856404;
            border: 1px solid #ffeaa7;
        }

        .status-badge.status-confirmed {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .status-badge.status-completed {
            background: #d1ecf1;
            color: #0c5460;
            border: 1px solid #bee5eb;
        }

        .status-badge.status-cancelled {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .booking-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 12px;
            margin-bottom: 16px;
        }

        .detail-row {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
            color: #666;
        }

        .detail-row .material-icons-outlined {
            font-size: 16px;
            color: #2c5f2d;
        }

        .booking-actions {
            display: flex;
            justify-content: flex-end;
        }

        .btn-view {
            display: flex;
            align-items: center;
            gap: 6px;
            padding: 8px 16px;
            background: #2c5f2d;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 500;
            transition: background 0.2s;
        }

        .btn-view:hover {
            background: #1a3d1a;
        }

        .empty-bookings {
            text-align: center;
            padding: 60px 20px;
            color: #666;
        }

        .empty-icon {
            font-size: 48px;
            color: #ccc;
            margin-bottom: 16px;
        }

        .empty-bookings h3 {
            margin: 0 0 12px 0;
            font-size: 1.2rem;
            color: #333;
        }

        .empty-bookings p {
            margin: 0 0 24px 0;
            font-size: 14px;
        }

        .btn-primary {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 24px;
            background: #2c5f2d;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 500;
            text-decoration: none;
            transition: background 0.2s;
        }

        .btn-primary:hover {
            background: #1a3d1a;
        }

        /* Responsive styles for modal */
        @media (max-width: 768px) {
            .modal-content {
                width: 95%;
                margin: 20px;
            }

            .modal-header {
                padding: 20px 24px;
            }

            .modal-body {
                padding: 24px;
            }

            .booking-filters {
                justify-content: center;
            }

            .booking-header {
                flex-direction: column;
                gap: 12px;
                align-items: flex-start;
            }

            .booking-details {
                grid-template-columns: 1fr;
            }

            .booking-actions {
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <!-- MAIN CONTENT -->
    <main class="main-content full-width">
        <header class="main-header">
            <div class="header-left">
                <div class="logo" style="display: flex; align-items: center; gap: 12px; margin-right: 30px;">
                    <img src="data:<?php echo $logoMime; ?>;base64,<?php echo $logoBase64; ?>" alt="SJDM Tours Logo" style="height: 40px; width: 40px; object-fit: contain; border-radius: 8px;">
                    <span style="font-family: 'Inter', sans-serif; font-weight: 700; font-size: 20px; color: var(--primary);">SJDM TOURS</span>
                </div>
                <h1 id="pageTitle">Dashboard</h1>
                <div class="search-bar">
                    <span class="material-icons-outlined">search</span>
                    <input type="text" placeholder="Search destinations or guides...">
                </div>
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
                    <!-- Sign In Button for Guests -->
                    <button class="btn-signin" onclick="window.location.href='../log-in.php'">Sign in/register</button>
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
                            <span class="stat-icon material-icons-outlined">
                                <?php 
                                $iconMap = [
                                    'natural_attractions' => 'forest',
                                    'distance' => 'location_on', 
                                    'climate' => 'wb_sunny',
                                    'tourism' => 'tour',
                                    'default' => 'star'
                                ];
                                echo $iconMap[$key] ?? 'star';
                                ?>
                            </span>
                            <h3><?php echo htmlspecialchars($homepageContent['stat_value'][$key] ?? '0'); ?></h3>
                            <p><?php echo htmlspecialchars($title); ?></p>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="stat-card">
                        <span class="stat-icon material-icons-outlined">forest</span>
                        <h3>10+</h3>
                        <p>Natural Attractions</p>
                    </div>
                    <div class="stat-card">
                        <span class="stat-icon material-icons-outlined">location_on</span>
                        <h3>30 min</h3>
                        <p>From Metro Manila</p>
                    </div>
                    <div class="stat-card">
                        <span class="stat-icon material-icons-outlined">wb_sunny</span>
                        <h3>Year-round</h3>
                        <p>Perfect Climate</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <!-- Booking History Modal -->
    <div class="modal-overlay" id="bookingHistoryModal">
        <div class="modal-content booking-modal">
            <div class="modal-header">
                <div class="modal-title">
                    <span class="material-icons-outlined modal-icon">history</span>
                    <h2>Booking History</h2>
                </div>
                <button class="close-modal" onclick="closeModal('bookingHistoryModal')">
                    <span class="material-icons-outlined">close</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="booking-filters">
                    <button class="filter-btn active" data-filter="all">All</button>
                    <button class="filter-btn" data-filter="pending">Pending</button>
                    <button class="filter-btn" data-filter="confirmed">Confirmed</button>
                    <button class="filter-btn" data-filter="completed">Completed</button>
                    <button class="filter-btn" data-filter="cancelled">Cancelled</button>
                </div>
                <div id="modalBookingsList" class="bookings-list">
                    <!-- Bookings will be loaded here -->
                </div>
            </div>
        </div>
    </div>

    <script>
        // ========== USER PROFILE DROPDOWN ==========
        function initUserProfileDropdown() {
            const profileDropdown = document.querySelector('.user-profile-dropdown');
            const profileTrigger = document.querySelector('.profile-trigger');
            const dropdownMenu = document.querySelector('.dropdown-menu');
            const logoutLink = document.querySelector('[href="logout.php"]');

            if (!profileDropdown || !profileTrigger || !dropdownMenu) {
                console.log('Profile dropdown elements not found');
                return;
            }

            // Toggle dropdown on click
            profileTrigger.addEventListener('click', function (e) {
                e.preventDefault();
                e.stopPropagation();
                dropdownMenu.classList.toggle('show');
            });

            // Close dropdown when clicking outside
            document.addEventListener('click', function (e) {
                if (!profileDropdown.contains(e.target)) {
                    dropdownMenu.classList.remove('show');
                }
            });

            // Handle logout with confirmation
            if (logoutLink) {
                logoutLink.addEventListener('click', function (e) {
                    e.preventDefault();
                    e.stopPropagation();
                    showLogoutConfirmation();
                });
            }
        }

        // Toggle dropdown function for inline onclick
        function toggleDropdown(button) {
            const dropdown = button.nextElementSibling;
            if (dropdown && dropdown.classList.contains('dropdown-menu')) {
                dropdown.classList.toggle('show');
                
                // Close other dropdowns
                document.querySelectorAll('.dropdown-menu.show').forEach(menu => {
                    if (menu !== dropdown) {
                        menu.classList.remove('show');
                    }
                });
            }
        }

        // Show logout confirmation modal
        function showLogoutConfirmation() {
            const modal = document.createElement('div');
            modal.className = 'modal-overlay';
            modal.innerHTML = `
                <div class="modal-content logout-modal">
                    <div class="modal-header">
                        <h2>Sign Out</h2>
                        <button class="close-modal" onclick="this.closest('.modal-overlay').remove()">
                            <span class="material-icons-outlined">close</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="logout-message">
                            <div class="logout-icon">
                                <span class="material-icons-outlined">logout</span>
                            </div>
                            <h3>Confirm Sign Out</h3>
                            <p>Are you sure you want to sign out of your account?</p>
                        </div>
                        <div class="modal-actions">
                            <button class="btn-cancel" onclick="document.querySelector('.modal-overlay').remove()">
                                <span class="material-icons-outlined">close</span>
                                Cancel
                            </button>
                            <button class="btn-confirm-logout" onclick="confirmLogout()">
                                <span class="material-icons-outlined">logout</span>
                                Sign Out
                            </button>
                        </div>
                    </div>
                </div>
            `;
            document.body.appendChild(modal);
            setTimeout(() => modal.classList.add('show'), 10);
        }

        // Confirm and execute logout
        function confirmLogout() {
            // Remove modal
            const modal = document.querySelector('.modal-overlay');
            if (modal) {
                modal.remove();
            }

            // Redirect to logout script
            window.location.href = 'logout.php';
        }

        // ========== BOOKING HISTORY MODAL ==========
        let currentBookingFilter = 'all';
        let userBookings = [];

        // Open booking history modal
        function openBookingHistoryModal() {
            const modal = document.getElementById('bookingHistoryModal');
            if (modal) {
                modal.classList.add('show');
                document.body.style.overflow = 'hidden';
                loadBookingHistory();
                initBookingFilters();
            }
        }

        // Close modal
        function closeModal(modalId) {
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.classList.remove('show');
                document.body.style.overflow = 'auto';
            }
        }

        // Load booking history
        function loadBookingHistory() {
            // Fetch bookings from server
            fetch('booking-history.php')
                .then(response => response.text())
                .then(html => {
                    // Extract booking data from the page
                    const tempDiv = document.createElement('div');
                    tempDiv.innerHTML = html;
                    
                    // Look for userBookings variable in the script
                    const scripts = tempDiv.querySelectorAll('script');
                    for (let script of scripts) {
                        if (script.textContent.includes('userBookings =')) {
                            const match = script.textContent.match(/userBookings = (\[.*?\]);/);
                            if (match) {
                                try {
                                    userBookings = JSON.parse(match[1]);
                                } catch (e) {
                                    console.error('Error parsing bookings:', e);
                                    userBookings = [];
                                }
                                break;
                            }
                        }
                    }
                    
                    displayModalBookings();
                })
                .catch(error => {
                    console.error('Error loading bookings:', error);
                    userBookings = [];
                    displayModalBookings();
                });
        }

        // Display bookings in modal
        function displayModalBookings() {
            const container = document.getElementById('modalBookingsList');
            if (!container) return;
            
            // Filter bookings
            let filteredBookings = userBookings;
            if (currentBookingFilter !== 'all') {
                filteredBookings = userBookings.filter(b => b.status === currentBookingFilter);
            }
            
            if (filteredBookings.length === 0) {
                container.innerHTML = `
                    <div class="empty-bookings">
                        <div class="empty-icon">
                            <span class="material-icons-outlined">event_busy</span>
                        </div>
                        <h3>No ${currentBookingFilter !== 'all' ? currentBookingFilter : ''} bookings found</h3>
                        <p>${currentBookingFilter === 'all' 
                            ? 'Start your adventure by booking your first tour with our experienced guides.' 
                            : `You don't have any ${currentBookingFilter} bookings at the moment.`}</p>
                        <button class="btn-primary" onclick="closeModal('bookingHistoryModal'); window.location.href='book.php'">
                            <span class="material-icons-outlined">explore</span>
                            Book Now
                        </button>
                    </div>
                `;
                return;
            }
            
            container.innerHTML = filteredBookings.map(booking => `
                <div class="booking-item" data-status="${booking.status}">
                    <div class="booking-header">
                        <div class="booking-info">
                            <h4>${booking.guide_name || 'Tour Guide'}</h4>
                            <p><span class="material-icons-outlined">place</span> ${booking.destination || booking.tour_name}</p>
                        </div>
                        <span class="status-badge status-${booking.status}">
                            ${getBookingStatusIcon(booking.status)}
                            ${booking.status.toUpperCase()}
                        </span>
                    </div>
                    <div class="booking-details">
                        <div class="detail-row">
                            <span class="material-icons-outlined">event</span>
                            <span>${formatBookingDate(booking.booking_date)}</span>
                        </div>
                        <div class="detail-row">
                            <span class="material-icons-outlined">people</span>
                            <span>${booking.number_of_people} Guest${booking.number_of_people > 1 ? 's' : ''}</span>
                        </div>
                        <div class="detail-row">
                            <span class="material-icons-outlined">payments</span>
                            <span>₱${Number(booking.total_amount).toLocaleString()}</span>
                        </div>
                    </div>
                    <div class="booking-actions">
                        <button class="btn-view" onclick="viewBookingDetails(${booking.id})">
                            <span class="material-icons-outlined">visibility</span>
                            View Details
                        </button>
                    </div>
                </div>
            `).join('');
        }

        // Initialize booking filters
        function initBookingFilters() {
            const filterBtns = document.querySelectorAll('.filter-btn');
            filterBtns.forEach(btn => {
                btn.addEventListener('click', function() {
                    filterBtns.forEach(b => b.classList.remove('active'));
                    this.classList.add('active');
                    currentBookingFilter = this.dataset.filter;
                    displayModalBookings();
                });
            });
        }

        // Get booking status icon
        function getBookingStatusIcon(status) {
            const icons = {
                'pending': '<span class="material-icons-outlined">schedule</span>',
                'confirmed': '<span class="material-icons-outlined">check_circle</span>',
                'completed': '<span class="material-icons-outlined">verified</span>',
                'cancelled': '<span class="material-icons-outlined">cancel</span>'
            };
            return icons[status] || '<span class="material-icons-outlined">info</span>';
        }

        // Format booking date
        function formatBookingDate(dateString) {
            const date = new Date(dateString);
            const options = { month: 'short', day: 'numeric', year: 'numeric' };
            return date.toLocaleDateString('en-US', options);
        }

        // View booking details (placeholder)
        function viewBookingDetails(bookingId) {
            const booking = userBookings.find(b => String(b.id) === String(bookingId));
            if (booking) {
                alert(`Booking Details:\n\nGuide: ${booking.guide_name || 'Tour Guide'}\nDestination: ${booking.destination || booking.tour_name}\nDate: ${formatBookingDate(booking.booking_date)}\nGuests: ${booking.number_of_people}\nTotal: ₱${Number(booking.total_amount).toLocaleString()}\nStatus: ${booking.status.toUpperCase()}`);
            }
        }

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function () {
            initUserProfileDropdown();
        });
    </script>
</body>
</html>