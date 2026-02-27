<?php

// Start session for user authentication
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect to login page with return URL
    $_SESSION['redirect_after_login'] = 'book.php';
    header('Location: ../log-in/log-in.php');
    exit();
}

// Include database configuration
require_once '../config/database.php';

// Get user information from session
$user_id = $_SESSION['user_id'];
$user_email = $_SESSION['email'] ?? '';
$user_name = ($_SESSION['first_name'] ?? '') . ' ' . ($_SESSION['last_name'] ?? '');

// Initialize current user array for profile dropdown
$currentUser = [
    'name' => $user_name,
    'email' => $user_email
];

// Initialize user contact and address variables (these columns don't exist in users table)
$user_address = '';
$user_contact = '';

// Get URL parameters from tourist detail page
$preselected_destination = $_GET['destination'] ?? '';
$preselected_date = $_GET['date'] ?? '';
$preselected_guide = $_GET['guide'] ?? '';

// Get user address from database if available
try {
    $conn = getDatabaseConnection();
    $tourGuides = []; // Initialize tour guides array
    
    if ($conn) {
        // Fetch tour guides from database (removed user address query as columns don't exist)
        $guidesStmt = $conn->prepare("SELECT * FROM tour_guides WHERE status = 'active' ORDER BY name ASC");
        if ($guidesStmt) {
            $guidesStmt->execute();
            $guidesResult = $guidesStmt->get_result();
            if ($guidesResult->num_rows > 0) {
                while ($guide = $guidesResult->fetch_assoc()) {
                    $tourGuides[] = $guide;
                }
            }
            $guidesStmt->close();
        }
        
        closeDatabaseConnection($conn);
    }
} catch (Exception $e) {
    // Silently fail - user can fill in manually
    $user_address = '';
    $user_contact = '';
    $tourGuides = [];
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Now - San Jose del Monte Bulacan</title>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
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
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            color: var(--text-primary);
            line-height: 1.6;
            margin: 0;
            padding: 0;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Hero Section */
        .hero-section {
            background: 
                linear-gradient(135deg,
                    rgba(44, 95, 45, 0.95) 0%,
                    rgba(34, 75, 35, 0.9) 25%,
                    rgba(24, 55, 25, 0.85) 50%,
                    rgba(14, 35, 15, 0.8) 100%),
                radial-gradient(circle at 20% 80%, rgba(151, 188, 98, 0.3) 0%, transparent 50%),
                radial-gradient(circle at 80% 20%, rgba(255, 107, 107, 0.2) 0%, transparent 50%),
                url('https://images.unsplash.com/photo-1469474968028-56623f02e42e?q=80&w=2070&auto=format&fit=crop') center/cover;
            background-blend-mode: overlay, normal, normal, overlay;
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            padding: 120px 40px 80px;
            text-align: center;
            color: white;
            position: relative;
            overflow: hidden;
            border-radius: 0 0 40px 40px;
            margin-bottom: 60px;
            min-height: 400px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }

        .hero-section::before {
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

        .hero-section::after {
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

        .hero-section h1 {
            font-size: 3.5rem;
            font-weight: 900;
            margin-bottom: 24px;
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

        .hero-section p {
            font-size: 1.4rem;
            margin-bottom: 40px;
            max-width: 600px;
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

        /* Guide Links Styling */
        .guide-links {
            display: flex;
            gap: 12px;
            margin-top: 8px;
            flex-wrap: wrap;
        }

        .view-guides-link {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 16px;
            background: var(--primary-light);
            color: var(--primary-dark);
            text-decoration: none;
            border-radius: 6px;
            font-size: 0.9rem;
            font-weight: 500;
            transition: all 0.2s ease;
            border: 1px solid rgba(44, 95, 45, 0.2);
        }

        .view-guides-link:hover {
            background: var(--primary);
            color: white;
            transform: translateY(-1px);
            box-shadow: 0 2px 8px rgba(44, 95, 45, 0.2);
        }

        .guide-details-btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 16px;
            background: var(--info);
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 0.9rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .guide-details-btn:hover {
            background: #2563eb;
            transform: translateY(-1px);
            box-shadow: 0 2px 8px rgba(59, 130, 246, 0.2);
        }

        .selected-date-display .material-icons-outlined {
            color: var(--primary);
            font-size: 20px;
        }

        #selectedDateText {
            font-weight: 500;
            color: var(--text-primary);
        }

        /* Calendar Availability Styles */
        .date-input-container {
            display: flex;
            gap: 8px;
            align-items: center;
        }

        .selected-date-display {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 12px 16px;
            background: var(--bg-light);
            border: 2px solid var(--border);
            border-radius: 8px;
            flex: 1;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .selected-date-display:hover {
            border-color: var(--primary);
            background: var(--primary-light);
        }

        .availability-status {
            margin-top: 8px;
            padding: 8px 12px;
            border-radius: 6px;
            font-size: 0.85rem;
            font-weight: 500;
            display: none;
        }

        .availability-status.available {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .availability-status.limited {
            background: #fff3cd;
            color: #856404;
            border: 1px solid #ffeaa7;
        }

        .availability-status.unavailable {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .availability-status.checking {
            background: #e2e3e5;
            color: #383d41;
            border: 1px solid #d6d8db;
        }

        /* Calendar Modal Styles */
        .calendar-modal {
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

        .calendar-modal.show {
            opacity: 1;
            visibility: visible;
        }

        .calendar-content {
            background: white;
            border-radius: 16px;
            max-width: 600px;
            width: 90%;
            max-height: 80vh;
            overflow-y: auto;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            transform: scale(0.9);
            transition: all 0.3s ease;
        }

        .calendar-modal.show .calendar-content {
            transform: scale(1);
        }

        .calendar-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 20px;
            border-bottom: 1px solid #eee;
        }

        .calendar-header h3 {
            margin: 0;
            font-size: 1.25rem;
            color: var(--text-primary);
        }

        .calendar-close {
            background: none;
            border: none;
            cursor: pointer;
            padding: 8px;
            border-radius: 50%;
            color: var(--text-secondary);
            transition: all 0.2s;
        }

        .calendar-close:hover {
            background: var(--bg-light);
            color: var(--text-primary);
        }

        .calendar-body {
            padding: 20px;
        }

        .calendar-grid {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 8px;
            margin-bottom: 20px;
        }

        .calendar-day-header {
            text-align: center;
            font-weight: 600;
            color: var(--text-secondary);
            font-size: 0.85rem;
            padding: 8px;
        }

        .calendar-day {
            aspect-ratio: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.2s ease;
            position: relative;
            font-weight: 500;
        }

        .calendar-day:hover {
            background: var(--bg-light);
            transform: scale(1.05);
        }

        .calendar-day.available {
            background: #d4edda;
            color: #155724;
        }

        .calendar-day.limited {
            background: #fff3cd;
            color: #856404;
        }

        .calendar-day.unavailable {
            background: #f8d7da;
            color: #721c24;
            cursor: not-allowed;
        }

        .calendar-day.selected {
            background: var(--primary);
            color: white;
            box-shadow: 0 4px 12px rgba(44, 95, 45, 0.3);
        }

        .calendar-day.past {
            color: #ccc;
            cursor: not-allowed;
        }

        .calendar-legend {
            display: flex;
            gap: 16px;
            justify-content: center;
            flex-wrap: wrap;
            margin-top: 16px;
            padding: 12px;
            background: var(--bg-light);
            border-radius: 8px;
        }

        .legend-item {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 0.85rem;
        }

        .legend-color {
            width: 16px;
            height: 16px;
            border-radius: 4px;
        }

        .legend-color.available {
            background: #d4edda;
        }

        .legend-color.limited {
            background: #fff3cd;
        }

        .legend-color.unavailable {
            background: #f8d7da;
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
                <h1>Book Now</h1>
                <div class="search-bar">
                    <span class="material-icons-outlined">search</span>
                    <input type="text" placeholder="Search tours or guides...">
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
                    <a href="javascript:void(0)" class="nav-link active">
                        <span class="material-icons-outlined">event</span>
                        <span>Book Now</span>
                    </a>
                    <a href="tourist-spots.php" class="nav-link">
                        <span class="material-icons-outlined">place</span>
                        <span>Tourist Spots</span>
                    </a>
                    <a href="booking-history.php" class="nav-link">
                        <span class="material-icons-outlined">history</span>
                        <span>Booking History</span>
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
                    <div class="user-profile-dropdown">
                        <button class="profile-trigger">
                            <div class="profile-avatar"><?php echo isset($currentUser['name']) ? strtoupper(substr($currentUser['name'], 0, 1)) : 'U'; ?></div>
                            <span class="profile-name"><?php echo htmlspecialchars($currentUser['name']); ?></span>
                            <span class="material-icons-outlined">expand_more</span>
                        </button>
                        <div class="dropdown-menu">
                            <div class="dropdown-header">
                                <div class="profile-avatar large"><?php echo isset($currentUser['name']) ? strtoupper(substr($currentUser['name'], 0, 1)) : 'U'; ?></div>
                                <div class="profile-details">
                                    <h4><?php echo htmlspecialchars($currentUser['name']); ?></h4>
                                    <p><?php echo htmlspecialchars($currentUser['email']); ?></p>
                                </div>
                            </div>
                            <div class="dropdown-divider"></div>
                            <a href="index.php" class="dropdown-item">
                                <span class="material-icons-outlined">dashboard</span>
                                <span>Dashboard</span>
                            </a>
                            <a href="booking-history.php" class="dropdown-item">
                                <span class="material-icons-outlined">history</span>
                                <span>Booking History</span>
                            </a>
                            <a href="saved-tours.php" class="dropdown-item">
                                <span class="material-icons-outlined">favorite</span>
                                <span>Saved Tours</span>
                            </a>
                            <a href="#" class="dropdown-item" onclick="openPreferencesModal(); return false;">
                                <span class="material-icons-outlined">tune</span>
                                <span>Preferences</span>
                            </a>
                            <div class="dropdown-divider"></div>
                            <a href="logout.php" class="dropdown-item">
                                <span class="material-icons-outlined">logout</span>
                                <span>Sign Out</span>
                            </a>
                        </div>
                    </div>
                </div>
            </header>

            <div class="content-area">
                <!-- Hero Section -->
                <div class="hero-section">
                    <h1>Book Your SJDM Tour</h1>
                    <p>Plan your perfect adventure in San Jose del Monte with our expert guides</p>
                </div>
                
                <!-- Enhanced Booking Progress -->
                <div class="booking-progress">
                    <div class="progress-step active" data-step="1">
                        <div class="step-number">1</div>
                        <div class="step-label">Tour Details</div>
                    </div>
                    <div class="progress-step" data-step="2">
                        <div class="step-number">2</div>
                        <div class="step-label">Personal Info</div>
                    </div>
                    <div class="progress-step" data-step="3">
                        <div class="step-number">3</div>
                        <div class="step-label">Review & Pay</div>
                    </div>
                    <div class="progress-step" data-step="4">
                        <div class="step-number">4</div>
                        <div class="step-label">Confirmation</div>
                    </div>
                </div>

            <div id="step-1" class="booking-step active">
                <div class="form-container">
                    <h3>Tour Details</h3>
                    <form id="tourDetailsForm">
                        <div class="form-group">
                            <label>Select Tour Guide *</label>
                            <select id="selectedGuide" required>
                                <option value="">-- Choose a Guide --</option>
                                <?php
                                if (!empty($tourGuides)) {
                                    echo "<!-- DEBUG: Found " . count($tourGuides) . " guides -->";
                                    foreach ($tourGuides as $guide) {
                                        $guideId = $guide['id'];
                                        $guideName = htmlspecialchars($guide['name']);
                                        $guideSpecialty = htmlspecialchars($guide['specialty'] ?? 'General Tours');
                                        $selected = ($preselected_guide == $guideId) ? 'selected' : '';
                                        echo "<option value=\"{$guideId}\" {$selected}>{$guideName} - {$guideSpecialty}</option>";
                                    }
                                } else {
                                    echo "<option value='' disabled>No guides available</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <div class="guide-links">
                                <a href="user-guides.php" class="view-guides-link" target="_blank">
                                    <span class="material-icons-outlined">people</span>
                                    View All Guides & Profiles
                                </a>
                                <button type="button" class="guide-details-btn" onclick="showSelectedGuideDetails()" id="guideDetailsBtn" style="display: none;">
                                    <span class="material-icons-outlined">info</span>
                                    View Selected Guide Details
                                </button>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Preferred Destination *</label>
                            <select id="destination" required>
                                <option value="">-- Select Destination --</option>
                                <?php 
                                $preselected_destination = $_GET['destination'] ?? '';
                                ?>
                                <option value="Mt. Balagbag" <?php echo ($preselected_destination == 'Mt. Balagbag') ? 'selected' : ''; ?>>Mt. Balagbag Hiking</option>
                                <option value="Kaytitinga Falls" <?php echo ($preselected_destination == 'Kaytitinga Falls') ? 'selected' : ''; ?>>Kaytitinga Falls Tour</option>
                                <option value="Tungtong Falls" <?php echo ($preselected_destination == 'Tungtong Falls') ? 'selected' : ''; ?>>Tungtong Falls Adventure</option>
                                <option value="Burong Falls" <?php echo ($preselected_destination == 'Burong Falls') ? 'selected' : ''; ?>>Burong Falls Trek</option>
                                <option value="Otso-Otso Falls" <?php echo ($preselected_destination == 'Otso-Otso Falls') ? 'selected' : ''; ?>>Otso-Otso Falls Exploration</option>
                                <option value="Paradise Hill Farm" <?php echo ($preselected_destination == 'Paradise Hill Farm') ? 'selected' : ''; ?>>Paradise Hill Farm Tour</option>
                                <option value="Abes Farm" <?php echo ($preselected_destination == 'Abes Farm') ? 'selected' : ''; ?>>Abes Farm Experience</option>
                                <option value="The Rising Heart" <?php echo ($preselected_destination == 'The Rising Heart') ? 'selected' : ''; ?>>The Rising Heart Visit</option>
                                <option value="City Oval & People's Park" <?php echo ($preselected_destination == "City Oval & People's Park") ? 'selected' : ''; ?>>City Park Tour</option>
                                <option value="Grotto of Our Lady of Lourdes" <?php echo ($preselected_destination == 'Grotto of Our Lady of Lourdes') ? 'selected' : ''; ?>>Religious Tour</option>
                                <option value="Padre Pio Mountain of Healing" <?php echo ($preselected_destination == 'Padre Pio Mountain of Healing') ? 'selected' : ''; ?>>Pilgrimage Tour</option>
                            </select>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label>Check-in Date *</label>
                                <div class="date-input-container">
                                    <div class="selected-date-display" id="selectedDateDisplay" onclick="showCalendarAvailability()" style="cursor: pointer;">
                                        <span class="material-icons-outlined">event</span>
                                        <span id="selectedDateText">Click to select date</span>
                                    </div>
                                </div>
                                <div id="availabilityStatus" class="availability-status"></div>
                                <input type="hidden" id="checkInDate" name="checkInDate" value="">
                            </div>
                            <div class="form-group">
                                <label>Number of Guests *</label>
                                <input type="number" id="guestCount" min="1" max="30" value="1" required>
                            </div>
                        </div>
                        <div class="form-actions">
                            <button type="button" class="btn-next" onclick="nextStep()">
                                Next: Personal Info
                                <span class="material-icons-outlined">arrow_forward</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div id="step-2" class="booking-step">
                <div class="form-container">
                    <h3>Personal Information</h3>
                    <form id="personalInfoForm">
                        <div class="form-group">
                            <label>Full Name *</label>
                            <input type="text" id="fullName" placeholder="Juan Dela Cruz" value="<?php echo htmlspecialchars($user_name); ?>" required>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label>Email Address *</label>
                                <input type="email" id="email" placeholder="juan@example.com" value="<?php echo htmlspecialchars($user_email); ?>" required>
                            </div>
                            <div class="form-group">
                                <label>Contact Number *</label>
                                <input type="tel" id="contactNumber" placeholder="+63 912 345 6789" value="<?php echo htmlspecialchars($user_contact); ?>" required>
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label>Address</label>
                                <input type="text" id="address" placeholder="Street, Barangay, City" value="<?php echo htmlspecialchars($user_address); ?>">
                            </div>
                            <div class="form-group">
                                <label>Nationality</label>
                                <input type="text" id="nationality" placeholder="Filipino">
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label>Emergency Contact Person</label>
                            <div class="form-row">
                                <div class="form-group">
                                    <input type="text" id="emergencyName" placeholder="Full Name">
                                </div>
                                <div class="form-group">
                                    <input type="tel" id="emergencyContact" placeholder="Contact Number">
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label>Special Requests (Optional)</label>
                            <textarea id="specialRequests" rows="3" placeholder="Any dietary restrictions, accessibility needs, or other special requirements..."></textarea>
                            <small class="form-note">Please let us know if you need any special accommodations</small>
                        </div>
                        
                        <div class="form-group">
                            <label>How did you hear about us?</label>
                            <select id="hearAboutUs">
                                <option value="">-- Select --</option>
                                <option value="social">Social Media</option>
                                <option value="friend">Friend/Family</option>
                                <option value="search">Search Engine</option>
                                <option value="website">Tourism Website</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        
                        <div class="form-actions">
                            <button type="button" class="btn-prev" onclick="prevStep()">
                                <span class="material-icons-outlined">arrow_back</span>
                                Back to Tour Details
                            </button>
                            <button type="button" class="btn-next" onclick="nextStep()">
                                Next: Review & Pay
                                <span class="material-icons-outlined">arrow_forward</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div id="step-3" class="booking-step">
                <div class="form-container">
                    <h3>Review Your Booking & Payment</h3>
                    
                    <div class="booking-summary">
                        <div class="summary-section">
                            <h4>Tour Details</h4>
                            <div class="summary-grid">
                                <div class="summary-item">
                                    <span class="summary-label">Guide:</span>
                                    <span class="summary-value" id="reviewGuideName">-</span>
                                </div>
                                <div class="summary-item">
                                    <span class="summary-label">Destination:</span>
                                    <span class="summary-value" id="reviewDestination">-</span>
                                </div>
                                <div class="summary-item">
                                    <span class="summary-label">Date:</span>
                                    <span class="summary-value" id="reviewDate">-</span>
                                </div>
                                <div class="summary-item">
                                    <span class="summary-label">Guests:</span>
                                    <span class="summary-value" id="reviewGuests">-</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="summary-section">
                            <h4>Personal Information</h4>
                            <div class="summary-grid">
                                <div class="summary-item">
                                    <span class="summary-label">Name:</span>
                                    <span class="summary-value" id="reviewFullName">-</span>
                                </div>
                                <div class="summary-item">
                                    <span class="summary-label">Email:</span>
                                    <span class="summary-value" id="reviewEmail">-</span>
                                </div>
                                <div class="summary-item">
                                    <span class="summary-label">Contact:</span>
                                    <span class="summary-value" id="reviewContact">-</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="price-summary">
                            <h4>Price Summary</h4>
                            <div class="price-breakdown">
                                <div class="price-item">
                                    <span>Guide Fee (per day)</span>
                                    <span id="priceGuideFee">₱2,500.00</span>
                                </div>
                                <div class="price-item">
                                    <span>Entrance Fees (per person × <span id="priceGuestCount">1</span>)</span>
                                    <span id="priceEntrance">₱100.00</span>
                                </div>
                                <div class="price-item">
                                    <span>Tour Service Fee</span>
                                    <span>₱200.00</span>
                                </div>
                                <div class="price-item">
                                    <span>Platform Fee</span>
                                    <span>₱100.00</span>
                                </div>
                                <div class="price-divider"></div>
                                <div class="price-item total">
                                    <strong>Total Amount</strong>
                                    <strong id="priceTotal">₱2,900.00</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="payment-section">
                        <h4>Payment Method</h4>
                        <div class="payment-options">
                            <div class="payment-option active" onclick="selectPayment('cash')">
                                <div class="payment-radio">
                                    <input type="radio" name="paymentMethod" value="cash" checked>
                                    <span class="radio-check"></span>
                                </div>
                                <div class="payment-icon">
                                    <span class="material-icons-outlined">payments</span>
                                </div>
                                <div class="payment-info">
                                    <h5>Pay on Arrival</h5>
                                    <p>Pay in cash upon meeting your guide</p>
                                    <div class="payment-note">
                                        <span class="material-icons-outlined">info</span>
                                        <p>Payment to be arranged directly with your tour guide</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="payment-notice">
                            <div class="notice-content">
                                <span class="material-icons-outlined">schedule</span>
                                <div>
                                    <strong>Payment Information</strong>
                                    <p>Full payment is due upon meeting your tour guide at the designated meeting point. Please prepare the exact amount to ensure a smooth start to your tour experience.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="terms-section">
                        <div class="terms-agreement">
                            <label class="checkbox-label">
                                <input type="checkbox" id="termsAgreement" required>
                                <span>I agree to the <a href="terms.php" target="_blank">Terms & Conditions</a> and <a href="privacy.php" target="_blank">Privacy Policy</a></span>
                            </label>
                        </div>
                        <div class="terms-agreement">
                            <label class="checkbox-label">
                                <input type="checkbox" id="cancellationPolicy" required>
                                <span>I understand the <a href="cancellation.php" target="_blank">cancellation policy</a>: Free cancellation up to 24 hours before tour</span>
                            </label>
                        </div>
                    </div>
                    
                    <div class="form-actions">
                        <button type="button" class="btn-prev" onclick="prevStep()">
                            <span class="material-icons-outlined">arrow_back</span>
                            Back to Personal Info
                        </button>
                        <button type="button" class="btn-debug" onclick="debugReviewSection()" style="background: #ff6b6b; color: white; margin-right: 10px;">
                            <span class="material-icons-outlined">bug_report</span>
                            Debug Review
                        </button>
                        <button type="button" class="btn-confirm" onclick="submitBooking()">
                            <span class="material-icons-outlined">check_circle</span>
                            Confirm Booking
                        </button>
                    </div>
                </div>
            </div>

            <div id="step-4" class="booking-step">
                <div class="confirmation-container">
                    <div class="confirmation-header success">
                        <div class="confirmation-icon">
                            <span class="material-icons-outlined">check_circle</span>
                        </div>
                        <h2>Booking Submitted!</h2>
                        <p>Thank you for booking with SJDM Tours. Your reservation is pending confirmation from the tour guide.</p>
                        <div class="booking-number">
                            <strong>Booking Reference:</strong>
                            <span id="confirmationBookingNumber">SJDM-<?php echo date('Ymd') . rand(1000, 9999); ?></span>
                        </div>
                    </div>
                    
                    <div class="confirmation-content">
                        <div class="confirmation-details">
                            <div class="detail-card">
                                <h3><span class="material-icons-outlined">confirmation_number</span> Booking Details</h3>
                                <div class="detail-grid">
                                    <div class="detail-item">
                                        <span class="detail-label">Booking ID:</span>
                                        <span class="detail-value" id="detailBookingId">SJDM-<?php echo date('Ymd') . rand(1000, 9999); ?></span>
                                <h3><span class="material-icons-outlined">tour</span> Tour Information</h3>
                                <div class="detail-grid">
                                    <div class="detail-item">
                                        <span class="detail-label">Destination:</span>
                                        <span class="detail-value" id="detailDestination">-</span>
                                    </div>
                                    <div class="detail-item">
                                        <span class="detail-label">Tour Date:</span>
                                        <span class="detail-value" id="detailTourDate">-</span>
                                    </div>
                                    <div class="detail-item">
                                        <span class="detail-label">Tour Guide:</span>
                                        <span class="detail-value" id="detailGuide">-</span>
                                    </div>
                                    <div class="detail-item">
                                        <span class="detail-label">Number of Guests:</span>
                                        <span class="detail-value" id="detailGuests">-</span>
                                    </div>
                                    <div class="detail-item">
                                        <span class="detail-label">Meeting Point:</span>
                                        <span class="detail-value">SJDM City Hall Parking Area</span>
                                    </div>
                                    <div class="detail-item">
                                        <span class="detail-label">Meeting Time:</span>
                                        <span class="detail-value">7:00 AM</span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="detail-card">
                                <h3><span class="material-icons-outlined">person</span> Guest Information</h3>
                                <div class="detail-grid">
                                    <div class="detail-item">
                                        <span class="detail-label">Name:</span>
                                        <span class="detail-value" id="detailGuestName">-</span>
                                    </div>
                                    <div class="detail-item">
                                        <span class="detail-label">Email:</span>
                                        <span class="detail-value" id="detailGuestEmail">-</span>
                                    </div>
                                    <div class="detail-item">
                                        <span class="detail-label">Contact Number:</span>
                                        <span class="detail-value" id="detailGuestContact">-</span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="detail-card">
                                <h3><span class="material-icons-outlined">payments</span> Payment Summary</h3>
                                <div class="price-breakdown">
                                    <div class="price-item">
                                        <span>Guide Service Fee</span>
                                        <span id="confirmationGuideFee">₱2,500.00</span>
                                    </div>
                                    <div class="price-item">
                                        <span>Entrance Fees (<span id="confirmationGuestCount">1</span> pax)</span>
                                        <span id="confirmationEntrance">₱100.00</span>
                                    </div>
                                    <div class="price-item">
                                        <span>Service Charges</span>
                                        <span>₱300.00</span>
                                    </div>
                                    <div class="price-divider"></div>
                                    <div class="price-item total">
                                        <strong>Total Paid</strong>
                                        <strong id="confirmationTotal">₱2,900.00</strong>
                                    </div>
                                    <div class="price-item">
                                        <span>Payment Method</span>
                                        <span id="confirmationPaymentMethod">-</span>
                                    </div>
                                    <div class="price-item">
                                        <span>Payment Status</span>
                                        <span class="status-badge paid">Paid</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="confirmation-actions">
                            <div class="action-card">
                                <h4><span class="material-icons-outlined">notifications</span> What's Next?</h4>
                                <div class="next-steps">
                                    <div class="step-item">
                                        <div class="step-number">1</div>
                                        <div class="step-content">
                                            <strong>Check your email</strong>
                                            <p>We've sent a confirmation email with all details</p>
                                        </div>
                                    </div>
                                    <div class="step-item">
                                        <div class="step-number">2</div>
                                        <div class="step-content">
                                            <strong>Tour guide contact</strong>
                                            <p>Your guide will contact you within 24 hours</p>
                                        </div>
                                    </div>
                                    <div class="step-item">
                                        <div class="step-number">3</div>
                                        <div class="step-content">
                                            <strong>Prepare for your tour</strong>
                                            <p>Check the packing list and meeting instructions</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="action-card important">
                                <h4><span class="material-icons-outlined">warning</span> Important Notes</h4>
                                <ul class="important-notes">
                                    <li>Please arrive at the meeting point 15 minutes before scheduled time</li>
                                    <li>Bring valid ID, comfortable clothing, and water bottle</li>
                                    <li>Weather-appropriate gear is recommended</li>
                                    <li>Cancellation must be made at least 24 hours in advance for full refund</li>
                                    <li>In case of emergency, contact: +63 912 345 6789</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    
                    <div class="confirmation-footer">
                        <div class="support-info">
                            <span class="material-icons-outlined">support_agent</span>
                            <div>
                                <strong>Need Help?</strong>
                                <p>Contact our support team: support@sjdmtours.com | +63 912 345 6789</p>
                            </div>
                        </div>
                        
                        <div class="confirmation-cta">
                            <button class="btn-secondary" onclick="window.location.href='index.php'">
                                <span class="material-icons-outlined">home</span>
                                Back to Home
                            </button>
                            <button class="btn-primary" onclick="window.location.href='booking-history.php'">
                                <span class="material-icons-outlined">history</span>
                                View Booking History
                            </button>
                            <button class="btn-outline" onclick="shareBooking()">
                                <span class="material-icons-outlined">share</span>
                                Share Booking
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Calendar Availability Modal -->
    <div id="calendarModal" class="calendar-modal">
        <div class="calendar-content">
            <div class="calendar-header">
                <h3>Check Tour Availability</h3>
                <button class="calendar-close" onclick="closeCalendarModal()">
                    <span class="material-icons-outlined">close</span>
                </button>
            </div>
            <div class="calendar-body">
                <div class="calendar-grid" id="calendarGrid">
                    <!-- Calendar will be generated here -->
                </div>
                <div class="calendar-legend">
                    <div class="legend-item">
                        <div class="legend-color available"></div>
                        <span>Available</span>
                    </div>
                    <div class="legend-item">
                        <div class="legend-color limited"></div>
                        <span>Limited Slots</span>
                    </div>
                    <div class="legend-item">
                        <div class="legend-color unavailable"></div>
                        <span>Unavailable</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="script.js"></script>
    <script>
        // ========== USER PROFILE DROPDOWN ==========
        function initUserProfileDropdown() {
            const profileDropdown = document.querySelector('.user-profile-dropdown');
            const profileTrigger = document.querySelector('.profile-trigger');
            const dropdownMenu = document.querySelector('.dropdown-menu');
            const logoutLink = document.querySelector('[href="../log-in/logout.php"]');

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
            window.location.href = '../log-in/logout.php';
        }

        // ========== GUIDE SELECTION FUNCTIONALITY ==========
        // Store guide data from PHP
        const tourGuides = <?php echo json_encode($tourGuides); ?>;
        
        function initGuideSelection() {
            const guideSelect = document.getElementById('selectedGuide');
            const guideDetailsBtn = document.getElementById('guideDetailsBtn');
            
            if (!guideSelect) return;
            
            // Show/hide guide details button based on selection
            guideSelect.addEventListener('change', function() {
                const selectedValue = this.value;
                if (selectedValue && guideDetailsBtn) {
                    guideDetailsBtn.style.display = 'inline-flex';
                } else if (guideDetailsBtn) {
                    guideDetailsBtn.style.display = 'none';
                }
                
                // Update guide name in review section
                updateGuideReview();
            });
            
            // Initialize guide details button visibility
            if (guideSelect.value && guideDetailsBtn) {
                guideDetailsBtn.style.display = 'inline-flex';
            }
        }
        
        function showSelectedGuideDetails() {
            const guideSelect = document.getElementById('selectedGuide');
            const selectedGuideId = guideSelect.value;
            
            if (!selectedGuideId) {
                alert('Please select a guide first');
                return;
            }
            
            // Find guide data
            const selectedGuide = tourGuides.find(guide => guide.id == selectedGuideId);
            
            if (!selectedGuide) {
                alert('Guide information not found');
                return;
            }
            
            // Create modal with guide details
            showGuideModal(selectedGuide);
        }
        
        function showGuideModal(guide) {
            const modal = document.createElement('div');
            modal.className = 'modal-overlay';
            modal.innerHTML = `
                <div class="modal-content guide-profile-modal">
                    <div class="modal-header">
                        <div class="modal-title">
                            <span class="material-icons-outlined modal-icon">person</span>
                            <h2>Guide Profile</h2>
                        </div>
                        <button class="close-modal" onclick="this.closest('.modal-overlay').remove()">
                            <span class="material-icons-outlined">close</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="guide-profile-content">
                            <div class="guide-profile-header">
                                <div class="guide-profile-info">
                                    <div class="guide-name-section">
                                        <h3>${guide.name}</h3>
                                        ${guide.verified == '1' ? `
                                        <div class="verified-ribbon">
                                            <span class="material-icons-outlined">verified_user</span>
                                            <span>Trusted Professional</span>
                                        </div>` : ''}
                                    </div>
                                    <p class="guide-specialty">${guide.specialty || 'General Tours'}</p>
                                    <div class="guide-category-badge">
                                        <span class="material-icons-outlined">category</span>
                                        ${guide.category || 'general'}
                                    </div>
                                </div>
                            </div>

                            <div class="guide-description-section">
                                <h4><span class="material-icons-outlined">info</span> About</h4>
                                <p>${guide.description || 'Experienced tour guide ready to show you the best of San Jose del Monte.'}</p>
                            </div>

                            <div class="guide-details-grid">
                                <div class="detail-item">
                                    <span class="material-icons-outlined">schedule</span>
                                    <div>
                                        <strong>Experience</strong>
                                        <p>${guide.experience || '5+ years'}</p>
                                    </div>
                                </div>
                                <div class="detail-item">
                                    <span class="material-icons-outlined">translate</span>
                                    <div>
                                        <strong>Languages</strong>
                                        <p>${guide.languages || 'English, Tagalog'}</p>
                                    </div>
                                </div>
                                <div class="detail-item">
                                    <span class="material-icons-outlined">groups</span>
                                    <div>
                                        <strong>Max Group Size</strong>
                                        <p>Up to ${guide.max_group_size || '10'} guests</p>
                                    </div>
                                </div>
                                <div class="detail-item">
                                    <span class="material-icons-outlined">wc</span>
                                    <div>
                                        <strong>Gender</strong>
                                        <p>${guide.gender || 'Not specified'}</p>
                                    </div>
                                </div>
                            </div>

                            <div class="guide-booking-section">
                                <h4><span class="material-icons-outlined">calendar_today</span> Booking Information</h4>
                                <p>This guide is available for your selected tour. Click "Close" to return to booking.</p>
                                <div class="booking-actions">
                                    <button class="btn-secondary" onclick="this.closest('.modal-overlay').remove()">
                                        Close
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            document.body.appendChild(modal);
            setTimeout(() => modal.classList.add('show'), 10);
        }
        
        function updateGuideReview() {
            const guideSelect = document.getElementById('selectedGuide');
            const reviewGuideName = document.getElementById('reviewGuideName');
            
            if (!guideSelect || !reviewGuideName) return;
            
            const selectedOption = guideSelect.options[guideSelect.selectedIndex];
            if (selectedOption && selectedOption.value) {
                reviewGuideName.textContent = selectedOption.text;
            } else {
                reviewGuideName.textContent = '-';
            }
        }

        // ========== CALENDAR AVAILABILITY FUNCTIONALITY ==========
        let selectedDate = null;
        let dateAvailability = {};

        function showCalendarAvailability() {
            const modal = document.getElementById('calendarModal');
            if (!modal) return;
            
            // Generate calendar
            generateCalendar();
            
            // Show modal
            modal.classList.add('show');
            document.body.style.overflow = 'hidden';
        }

        function closeCalendarModal() {
            const modal = document.getElementById('calendarModal');
            if (modal) {
                modal.classList.remove('show');
                document.body.style.overflow = 'auto';
            }
        }

        function generateCalendar() {
            const calendarGrid = document.getElementById('calendarGrid');
            if (!calendarGrid) return;
            
            const today = new Date();
            const currentMonth = today.getMonth();
            const currentYear = today.getFullYear();
            
            // Get first day of month and number of days
            const firstDay = new Date(currentYear, currentMonth, 1);
            const lastDay = new Date(currentYear, currentMonth + 1, 0);
            const daysInMonth = lastDay.getDate();
            const startingDayOfWeek = firstDay.getDay();
            
            // Clear existing calendar
            calendarGrid.innerHTML = '';
            
            // Add day headers
            const dayHeaders = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
            dayHeaders.forEach(day => {
                const dayHeader = document.createElement('div');
                dayHeader.className = 'calendar-day-header';
                dayHeader.textContent = day;
                calendarGrid.appendChild(dayHeader);
            });
            
            // Add empty cells for days before month starts
            for (let i = 0; i < startingDayOfWeek; i++) {
                const emptyDay = document.createElement('div');
                calendarGrid.appendChild(emptyDay);
            }
            
            // Add days of the month
            for (let day = 1; day <= daysInMonth; day++) {
                const dayElement = document.createElement('div');
                dayElement.className = 'calendar-day';
                dayElement.textContent = day;
                
                const currentDate = new Date(currentYear, currentMonth, day);
                const dateString = formatDateForComparison(currentDate);
                
                // Mark past dates
                if (currentDate < today.setHours(0, 0, 0, 0)) {
                    dayElement.classList.add('past');
                } else {
                    // Generate availability status
                    const availability = generateAvailabilityForDate(currentDate);
                    dayElement.classList.add(availability.status);
                    
                    // Add click handler for available/limited dates
                    if (availability.status !== 'unavailable') {
                        dayElement.addEventListener('click', function() {
                            selectDate(currentDate, dayElement);
                        });
                    }
                    
                    // Store availability data
                    dateAvailability[dateString] = availability;
                }
                
                calendarGrid.appendChild(dayElement);
            }
        }

        function generateAvailabilityForDate(date) {
            // Simulate availability logic (in real app, this would check database)
            const dayOfWeek = date.getDay();
            const random = Math.random();
            
            // Weekends are more popular
            if (dayOfWeek === 0 || dayOfWeek === 6) {
                if (random < 0.3) return { status: 'unavailable', message: 'Fully booked' };
                if (random < 0.6) return { status: 'limited', message: 'Only 2 slots left' };
                return { status: 'available', message: 'Available' };
            }
            
            // Weekdays
            if (random < 0.1) return { status: 'unavailable', message: 'Guide not available' };
            if (random < 0.3) return { status: 'limited', message: 'Limited slots' };
            return { status: 'available', message: 'Available' };
        }

        function selectDate(date, element) {
            // Remove previous selection
            document.querySelectorAll('.calendar-day.selected').forEach(el => {
                el.classList.remove('selected');
            });
            
            // Add selection to clicked date
            element.classList.add('selected');
            selectedDate = date;
            
            // Update the hidden date input field
            const dateInput = document.getElementById('checkInDate');
            if (dateInput) {
                dateInput.value = formatDateForInput(date);
            }
            
            // Update the display text
            const selectedDateText = document.getElementById('selectedDateText');
            if (selectedDateText) {
                const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
                selectedDateText.textContent = date.toLocaleDateString('en-US', options);
            }
            
            // Check availability
            checkDateAvailability();
            
            // Close modal after selection
            setTimeout(() => {
                closeCalendarModal();
            }, 500);
        }

        function checkDateAvailability() {
            const dateInput = document.getElementById('checkInDate');
            const availabilityStatus = document.getElementById('availabilityStatus');
            
            if (!dateInput || !availabilityStatus) return;
            
            const selectedDateValue = dateInput.value;
            if (!selectedDateValue) {
                availabilityStatus.style.display = 'none';
                return;
            }
            
            // Show checking status
            availabilityStatus.className = 'availability-status checking';
            availabilityStatus.innerHTML = '<span class="material-icons-outlined">hourglass_empty</span> Checking availability...';
            availabilityStatus.style.display = 'block';
            
            // Simulate availability check
            setTimeout(() => {
                const date = new Date(selectedDateValue);
                const today = new Date();
                today.setHours(0, 0, 0, 0);
                
                if (date < today) {
                    availabilityStatus.className = 'availability-status unavailable';
                    availabilityStatus.innerHTML = '<span class="material-icons-outlined">error</span> This date has passed. Please select a future date.';
                    disableNextButton();
                } else {
                    const availability = generateAvailabilityForDate(date);
                    availabilityStatus.className = `availability-status ${availability.status}`;
                    
                    if (availability.status === 'available') {
                        availabilityStatus.innerHTML = `<span class="material-icons-outlined">check_circle</span> ${availability.message} - Tour guides available!`;
                        enableNextButton();
                    } else if (availability.status === 'limited') {
                        availabilityStatus.innerHTML = `<span class="material-icons-outlined">warning</span> ${availability.message} - Book soon!`;
                        enableNextButton();
                    } else {
                        availabilityStatus.innerHTML = `<span class="material-icons-outlined">block</span> ${availability.message} - Please choose another date.`;
                        disableNextButton();
                    }
                }
            }, 1000);
        }

        function enableNextButton() {
            const nextBtn = document.querySelector('.btn-next');
            if (nextBtn) {
                nextBtn.disabled = false;
                nextBtn.style.opacity = '1';
                nextBtn.style.cursor = 'pointer';
            }
        }

        function disableNextButton() {
            const nextBtn = document.querySelector('.btn-next');
            if (nextBtn) {
                nextBtn.disabled = true;
                nextBtn.style.opacity = '0.5';
                nextBtn.style.cursor = 'not-allowed';
            }
        }

        function formatDateForComparison(date) {
            return date.toISOString().split('T')[0];
        }

        function formatDateForInput(date) {
            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const day = String(date.getDate()).padStart(2, '0');
            return `${year}-${month}-${day}`;
        }

        // Modify nextStep function to check availability first
        function nextStep() {
            const dateInput = document.getElementById('checkInDate');
            const availabilityStatus = document.getElementById('availabilityStatus');
            
            if (!dateInput.value) {
                alert('Please select a check-in date');
                return;
            }
            
            if (availabilityStatus && availabilityStatus.classList.contains('unavailable')) {
                alert('Please select an available date before proceeding');
                return;
            }
            
            // Proceed with normal next step logic
            const currentStep = document.querySelector('.booking-step.active');
            const currentStepNumber = parseInt(currentStep.id.split('-')[1]);
            
            if (currentStepNumber < 4) {
                currentStep.classList.remove('active');
                document.getElementById(`step-${currentStepNumber + 1}`).classList.add('active');
                updateProgressBar(currentStepNumber + 1);
                
                // Only update review section when moving to step 3
                if (currentStepNumber + 1 === 3) {
                    updateReviewSection();
                }
                
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }
        }

        function updateReviewSection() {
            console.log('updateReviewSection called');
            
            // Update Tour Details
            const guideSelect = document.getElementById('selectedGuide');
            const destinationSelect = document.getElementById('destination');
            const dateInput = document.getElementById('checkInDate');
            const guestCountInput = document.getElementById('guestCount');
            
            console.log('Elements found:', {
                guideSelect: !!guideSelect,
                destinationSelect: !!destinationSelect,
                dateInput: !!dateInput,
                guestCountInput: !!guestCountInput
            });
            
            console.log('Form values:', {
                guideValue: guideSelect?.value,
                destinationValue: destinationSelect?.value,
                dateValue: dateInput?.value,
                guestValue: guestCountInput?.value,
                destinationOptions: destinationSelect ? destinationSelect.options.length : 'N/A'
            });
            
            // Update Guide
            const reviewGuideName = document.getElementById('reviewGuideName');
            if (guideSelect && reviewGuideName) {
                const selectedOption = guideSelect.options[guideSelect.selectedIndex];
                console.log('Guide selected:', selectedOption);
                reviewGuideName.textContent = selectedOption && selectedOption.value ? selectedOption.text : '-';
            }
            
            // Update Destination
            const reviewDestination = document.getElementById('reviewDestination');
            if (destinationSelect && reviewDestination) {
                console.log('Destination select element:', destinationSelect);
                console.log('Destination select value:', destinationSelect.value);
                console.log('Destination select selectedIndex:', destinationSelect.selectedIndex);
                const selectedOption = destinationSelect.options[destinationSelect.selectedIndex];
                console.log('Destination selected option:', selectedOption);
                if (selectedOption) {
                    console.log('Destination option text:', selectedOption.text);
                    console.log('Destination option value:', selectedOption.value);
                }
                reviewDestination.textContent = selectedOption && selectedOption.value ? selectedOption.text : '-';
            }
            
            // Update Date
            const reviewDate = document.getElementById('reviewDate');
            if (dateInput && reviewDate) {
                console.log('Date input element:', dateInput);
                console.log('Date input value:', dateInput.value);
                if (dateInput.value) {
                    const date = new Date(dateInput.value);
                    const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
                    console.log('Date selected:', dateInput.value);
                    console.log('Formatted date:', date.toLocaleDateString('en-US', options));
                    reviewDate.textContent = date.toLocaleDateString('en-US', options);
                } else {
                    reviewDate.textContent = '-';
                }
            }
            
            // Update Guests
            const reviewGuests = document.getElementById('reviewGuests');
            if (guestCountInput && reviewGuests) {
                const guests = guestCountInput.value;
                console.log('Guests count:', guests);
                reviewGuests.textContent = guests ? `${guests} ${guests == 1 ? 'guest' : 'guests'}` : '-';
            }
            
            // Update Personal Information
            const fullNameInput = document.getElementById('fullName');
            const emailInput = document.getElementById('email');
            const contactInput = document.getElementById('contactNumber');
            
            console.log('Personal info elements:', {
                fullNameInput: !!fullNameInput,
                emailInput: !!emailInput,
                contactInput: !!contactInput
            });
            
            const reviewFullName = document.getElementById('reviewFullName');
            const reviewEmail = document.getElementById('reviewEmail');
            const reviewContact = document.getElementById('reviewContact');
            
            if (fullNameInput && reviewFullName) {
                console.log('Full name:', fullNameInput.value);
                reviewFullName.textContent = fullNameInput.value || '-';
            }
            
            if (emailInput && reviewEmail) {
                console.log('Email:', emailInput.value);
                reviewEmail.textContent = emailInput.value || '-';
            }
            
            if (contactInput && reviewContact) {
                console.log('Contact:', contactInput.value);
                reviewContact.textContent = contactInput.value || '-';
            }
            
            // Update Price Calculation
            updatePriceCalculation();
        }

        function updatePriceCalculation() {
            const guestCount = parseInt(document.getElementById('guestCount')?.value || 1);
            const priceGuideFee = 2500;
            const priceEntrancePerPerson = 100;
            const priceServiceFee = 200;
            const pricePlatformFee = 100;
            
            const entranceFee = priceEntrancePerPerson * guestCount;
            const total = priceGuideFee + entranceFee + priceServiceFee + pricePlatformFee;
            
            // Update price display elements
            const priceGuestCountEl = document.getElementById('priceGuestCount');
            const priceEntranceEl = document.getElementById('priceEntrance');
            const priceTotalEl = document.getElementById('priceTotal');
            
            if (priceGuestCountEl) priceGuestCountEl.textContent = guestCount;
            if (priceEntranceEl) priceEntranceEl.textContent = `₱${entranceFee.toFixed(2)}`;
            if (priceTotalEl) priceTotalEl.textContent = `₱${total.toFixed(2)}`;
            
            // Update confirmation page prices
            const confirmationGuestCountEl = document.getElementById('confirmationGuestCount');
            const confirmationEntranceEl = document.getElementById('confirmationEntrance');
            const confirmationTotalEl = document.getElementById('confirmationTotal');
            
            if (confirmationGuestCountEl) confirmationGuestCountEl.textContent = guestCount;
            if (confirmationEntranceEl) confirmationEntranceEl.textContent = `₱${entranceFee.toFixed(2)}`;
            if (confirmationTotalEl) confirmationTotalEl.textContent = `₱${total.toFixed(2)}`;
        }

        function updateProgressBar(step) {
            // Remove active class from all steps
            document.querySelectorAll('.progress-step').forEach(el => {
                el.classList.remove('active');
            });
            
            // Add active class to current step
            const currentStepEl = document.querySelector(`.progress-step[data-step="${step}"]`);
            if (currentStepEl) {
                currentStepEl.classList.add('active');
            }
        }

        function submitBooking() {
            // Validate all required fields
            const guideSelect = document.getElementById('selectedGuide');
            const destinationSelect = document.getElementById('destination');
            const dateInput = document.getElementById('checkInDate');
            const guestCountInput = document.getElementById('guestCount');
            const fullNameInput = document.getElementById('fullName');
            const emailInput = document.getElementById('email');
            const contactInput = document.getElementById('contactNumber');
            const termsAgreement = document.getElementById('termsAgreement');
            const cancellationPolicy = document.getElementById('cancellationPolicy');

            // Check required fields
            if (!destinationSelect.value) {
                alert('Please select a destination');
                return;
            }

            if (!dateInput.value) {
                alert('Please select a tour date');
                return;
            }

            if (!guestCountInput.value || guestCountInput.value < 1) {
                alert('Please enter number of guests');
                return;
            }

            if (!fullNameInput.value.trim()) {
                alert('Please enter your full name');
                return;
            }

            if (!emailInput.value.trim()) {
                alert('Please enter your email address');
                return;
            }

            if (!contactInput.value.trim()) {
                alert('Please enter your contact number');
                return;
            }

            if (!termsAgreement.checked) {
                alert('Please agree to the Terms & Conditions');
                return;
            }

            if (!cancellationPolicy.checked) {
                alert('Please acknowledge the cancellation policy');
                return;
            }

            // Check availability status
            const availabilityStatus = document.getElementById('availabilityStatus');
            if (availabilityStatus && availabilityStatus.classList.contains('unavailable')) {
                alert('Please select an available date before proceeding');
                return;
            }

            // Prepare form data
            const formData = new FormData();
            formData.append('guide_id', guideSelect.value || '');
            formData.append('destination', destinationSelect.value);
            formData.append('date', dateInput.value);
            formData.append('guests', guestCountInput.value);
            formData.append('contact', contactInput.value);
            formData.append('email', emailInput.value);
            formData.append('special_requests', document.getElementById('specialRequests')?.value || '');

            // Show loading state
            const confirmBtn = document.querySelector('.btn-confirm');
            const originalText = confirmBtn.innerHTML;
            confirmBtn.innerHTML = '<span class="material-icons-outlined">hourglass_empty</span> Processing...';
            confirmBtn.disabled = true;

            // Submit booking
            fetch('submit_booking.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update confirmation page with booking details
                    updateConfirmationPage(data);
                    
                    // Move to confirmation step
                    const currentStep = document.querySelector('.booking-step.active');
                    currentStep.classList.remove('active');
                    document.getElementById('step-4').classList.add('active');
                    updateProgressBar(4);
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                } else {
                    alert('Booking failed: ' + data.message);
                    confirmBtn.innerHTML = originalText;
                    confirmBtn.disabled = false;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while submitting your booking. Please try again.');
                confirmBtn.innerHTML = originalText;
                confirmBtn.disabled = false;
            });
        }

        function updateConfirmationPage(bookingData) {
            // Update booking reference
            const confirmationBookingNumber = document.getElementById('confirmationBookingNumber');
            if (confirmationBookingNumber) {
                confirmationBookingNumber.textContent = bookingData.booking_reference;
            }

            // Update detail booking ID
            const detailBookingId = document.getElementById('detailBookingId');
            if (detailBookingId) {
                detailBookingId.textContent = bookingData.booking_reference;
            }

            // Update tour details
            const destinationSelect = document.getElementById('destination');
            const dateInput = document.getElementById('checkInDate');
            const guestCountInput = document.getElementById('guestCount');
            const guideSelect = document.getElementById('selectedGuide');
            const fullNameInput = document.getElementById('fullName');
            const emailInput = document.getElementById('email');
            const contactInput = document.getElementById('contactNumber');

            // Tour Information
            const detailDestination = document.getElementById('detailDestination');
            if (detailDestination && destinationSelect) {
                const selectedOption = destinationSelect.options[destinationSelect.selectedIndex];
                detailDestination.textContent = selectedOption ? selectedOption.text : '-';
            }

            const detailTourDate = document.getElementById('detailTourDate');
            if (detailTourDate && dateInput.value) {
                const date = new Date(dateInput.value);
                const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
                detailTourDate.textContent = date.toLocaleDateString('en-US', options);
            }

            const detailGuide = document.getElementById('detailGuide');
            if (detailGuide && guideSelect) {
                const selectedOption = guideSelect.options[guideSelect.selectedIndex];
                detailGuide.textContent = selectedOption && selectedOption.value ? selectedOption.text : 'No guide selected';
            }

            const detailGuests = document.getElementById('detailGuests');
            if (detailGuests && guestCountInput) {
                detailGuests.textContent = guestCountInput.value;
            }

            // Guest Information
            const detailGuestName = document.getElementById('detailGuestName');
            if (detailGuestName && fullNameInput) {
                detailGuestName.textContent = fullNameInput.value;
            }

            const detailGuestEmail = document.getElementById('detailGuestEmail');
            if (detailGuestEmail && emailInput) {
                detailGuestEmail.textContent = emailInput.value;
            }

            const detailGuestContact = document.getElementById('detailGuestContact');
            if (detailGuestContact && contactInput) {
                detailGuestContact.textContent = contactInput.value;
            }

            // Payment Summary (already updated by updatePriceCalculation)
            updatePriceCalculation();
        }

        // Add a debug function to manually test the review section
        function debugReviewSection() {
            console.log('=== DEBUG REVIEW SECTION ===');
            
            // Force update review section
            updateReviewSection();
            
            // Check all form elements manually
            const allElements = {
                'selectedGuide': document.getElementById('selectedGuide'),
                'destination': document.getElementById('destination'),
                'checkInDate': document.getElementById('checkInDate'),
                'guestCount': document.getElementById('guestCount'),
                'fullName': document.getElementById('fullName'),
                'email': document.getElementById('email'),
                'contactNumber': document.getElementById('contactNumber'),
                'reviewGuideName': document.getElementById('reviewGuideName'),
                'reviewDestination': document.getElementById('reviewDestination'),
                'reviewDate': document.getElementById('reviewDate'),
                'reviewGuests': document.getElementById('reviewGuests'),
                'reviewFullName': document.getElementById('reviewFullName'),
                'reviewEmail': document.getElementById('reviewEmail'),
                'reviewContact': document.getElementById('reviewContact')
            };
            
            console.log('All elements status:');
            Object.keys(allElements).forEach(key => {
                const element = allElements[key];
                console.log(`${key}:`, {
                    exists: !!element,
                    value: element ? (element.value || element.textContent) : 'N/A',
                    type: element ? element.tagName : 'N/A'
                });
            });
            
            // Try to manually set values for testing
            console.log('=== MANUALLY SETTING VALUES ===');
            if (allElements.reviewDestination) {
                allElements.reviewDestination.textContent = 'Test Destination';
                console.log('Manually set destination to: Test Destination');
            }
            
            if (allElements.reviewDate) {
                allElements.reviewDate.textContent = 'Test Date';
                console.log('Manually set date to: Test Date');
            }
            
            if (allElements.reviewFullName) {
                allElements.reviewFullName.textContent = 'Test Name';
                console.log('Manually set name to: Test Name');
            }
            
            if (allElements.reviewEmail) {
                allElements.reviewEmail.textContent = 'test@email.com';
                console.log('Manually set email to: test@email.com');
            }
            
            if (allElements.reviewContact) {
                allElements.reviewContact.textContent = 'Test Contact';
                console.log('Manually set contact to: Test Contact');
            }
        }

        // Initialize guest count listeners and profile dropdown when page loads
        document.addEventListener('DOMContentLoaded', function() {
            initUserProfileDropdown();
            initGuideSelection();
            
            // Initialize total guest count display
            updateTotalGuestCount();
            
            // Add event listeners to form fields for real-time review updates
            const guideSelect = document.getElementById('selectedGuide');
            const destinationSelect = document.getElementById('destination');
            const guestCountInput = document.getElementById('guestCount');
            const fullNameInput = document.getElementById('fullName');
            const emailInput = document.getElementById('email');
            const contactInput = document.getElementById('contactNumber');
            
            if (guideSelect) {
                guideSelect.addEventListener('change', () => {
                    if (document.querySelector('.booking-step.active').id === 'step-3') {
                        updateReviewSection();
                    }
                });
            }
            
            if (destinationSelect) {
                destinationSelect.addEventListener('change', () => {
                    if (document.querySelector('.booking-step.active').id === 'step-3') {
                        updateReviewSection();
                    }
                });
            }
            
            if (guestCountInput) {
                guestCountInput.addEventListener('input', () => {
                    if (document.querySelector('.booking-step.active').id === 'step-3') {
                        updateReviewSection();
                    }
                });
            }
            
            if (fullNameInput) {
                fullNameInput.addEventListener('input', () => {
                    if (document.querySelector('.booking-step.active').id === 'step-3') {
                        updateReviewSection();
                    }
                });
            }
            
            if (emailInput) {
                emailInput.addEventListener('input', () => {
                    if (document.querySelector('.booking-step.active').id === 'step-3') {
                        updateReviewSection();
                    }
                });
            }
            
            if (contactInput) {
                contactInput.addEventListener('input', () => {
                    if (document.querySelector('.booking-step.active').id === 'step-3') {
                        updateReviewSection();
                    }
                });
            }
            
            // Update review section when page loads in case user is returning to review step
            setTimeout(() => {
                const currentStep = document.querySelector('.booking-step.active');
                if (currentStep && currentStep.id === 'step-3') {
                    updateReviewSection();
                }
            }, 100);
        });
    </script>
</body>
</html>