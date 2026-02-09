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
// Get URL parameters from tourist detail page
$preselected_destination = $_GET['destination'] ?? '';
$preselected_date = $_GET['date'] ?? '';
$preselected_guide = $_GET['guide'] ?? '';

// Get user address from database if available
try {
    $conn = getDatabaseConnection();
    if ($conn) {
        $stmt = $conn->prepare("SELECT address, contact_number FROM users WHERE user_id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $user_info = $stmt->get_result()->fetch_assoc();
        $user_address = $user_info['address'] ?? '';
        $user_contact = $user_info['contact_number'] ?? '';
        $stmt->close();
        closeDatabaseConnection($conn);
    } else {
        $user_address = '';
        $user_contact = '';
    }
} catch (Exception $e) {
    // Silently fail - user can fill in manually
    $user_address = '';
    $user_contact = '';
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

            .main-header {
                padding: 20px;
                gap: 16px;
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
        }
    </style>
</head>
<body>
    <!-- SIDEBAR -->
    <aside class="sidebar">
        <div class="sidebar-logo">
            <h1>SJDM Tours</h1>
            <p>Explore San Jose del Monte</p>
        </div>

        <nav class="sidebar-nav">
            <a class="nav-item" href="index.php">
                <span class="material-icons-outlined">home</span>
                <span>Home</span>
            </a>
            <a class="nav-item" href="user-guides.php">
                <span class="material-icons-outlined">people</span>
                <span>Tour Guides</span>
            </a>
            <a class="nav-item active" href="javascript:void(0)">
                <span class="material-icons-outlined">event</span>
                <span>Book Now</span>
            </a>
            <a class="nav-item" href="tourist-spots.php">
                <span class="material-icons-outlined">place</span>
                <span>Tourist Spots</span>
            </a>
            <a class="nav-item" href="local-culture.php">
                <span class="material-icons-outlined">theater_comedy</span>
                <span>Local Culture</span>
            </a>
            <a class="nav-item" href="travel-tips.php">
                <span class="material-icons-outlined">tips_and_updates</span>
                <span>Travel Tips</span>
            </a>
        </nav>
    </aside>

    <!-- MAIN CONTENT -->
    <main class="main-content">
        <header class="main-header">
            <h1>Book Now</h1>
            <div class="search-bar">
                <span class="material-icons-outlined">search</span>
                <input type="text" placeholder="Search tours or guides...">
            </div>
            <div class="header-actions">
                <button class="icon-button">
                    <span class="material-icons-outlined">notifications_none</span>
                    <span class="notification-badge" style="display: none;">0</span>
                </button>
                <!-- User Profile Dropdown -->
                <div class="user-profile-dropdown">
                    <button class="profile-trigger">
                        <div class="profile-avatar">
                            <?php echo substr(htmlspecialchars($currentUser['name'] ?? 'U'), 0, 1); ?>
                        </div>
                        <span class="profile-name"><?php echo htmlspecialchars(explode(' ', $currentUser['name'] ?? 'User')[0]); ?></span>
                        <span class="material-icons-outlined">expand_more</span>
                    </button>
                    <div class="dropdown-menu">
                        <div class="dropdown-header">
                            <div class="profile-avatar-large">
                                <?php echo substr(htmlspecialchars($currentUser['name'] ?? 'US'), 0, 2); ?>
                            </div>
                            <h4><?php echo htmlspecialchars($currentUser['name'] ?? 'User'); ?></h4>
                            <p><?php echo htmlspecialchars($currentUser['email'] ?? ''); ?></p>
                        </div>
                        <a href="profile.php" class="dropdown-item">
                            <span class="material-icons-outlined">account_circle</span>
                            <span>My Profile</span>
                        </a>
                        <a href="logout.php" class="dropdown-item">
                            <span class="material-icons-outlined">logout</span>
                            <span>Log Out</span>
                        </a>
                    </div>
                </div>
            </div>
        </header>

        <div class="content-area">
            <h2 class="section-title">Book Your SJDM Tour</h2>
            
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
                                <option value="1" <?php echo ($preselected_guide == '1') ? 'selected' : ''; ?>>Carlos Mendoza - Adventure Tours</option>
                                <option value="2" <?php echo ($preselected_guide == '2') ? 'selected' : ''; ?>>Maria Santos - Cultural Tours</option>
                                <option value="3" <?php echo ($preselected_guide == '3') ? 'selected' : ''; ?>>Roberto Reyes - Nature & Photography</option>
                                <option value="4" <?php echo ($preselected_guide == '4') ? 'selected' : ''; ?>>Ana Cruz - Historical Tours</option>
                                <option value="5" <?php echo ($preselected_guide == '5') ? 'selected' : ''; ?>>David Lee - Food & Culinary Tours</option>
                                <option value="8" <?php echo ($preselected_guide == '8') ? 'selected' : ''; ?>>Ricardo Fernandez - Waterfall Tours</option>
                                <option value="9" <?php echo ($preselected_guide == '9') ? 'selected' : ''; ?>>Sofia Martinez - Religious Sites</option>
                                <option value="10" <?php echo ($preselected_guide == '10') ? 'selected' : ''; ?>>Marco Alvarez - Mountain Trekking</option>
                                <option value="11" <?php echo ($preselected_guide == '11') ? 'selected' : ''; ?>>Elena Rodriguez - City Landmarks</option>
                            </select>
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
                                <input type="date" id="checkInDate" value="<?php echo htmlspecialchars($preselected_date); ?>" required>
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
                        <h2>Booking Confirmed!</h2>
                        <p>Thank you for booking with SJDM Tours. Your reservation has been confirmed.</p>
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
                                    </div>
                                    <div class="detail-item">
                                        <span class="detail-label">Booking Date:</span>
                                        <span class="detail-value" id="detailBookingDate"><?php echo date('F d, Y'); ?></span>
                                    </div>
                                    <div class="detail-item">
                                        <span class="detail-label">Status:</span>
                                        <span class="status-badge confirmed">Confirmed</span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="detail-card">
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

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function () {
            initUserProfileDropdown();
        });
    </script>
</body>
</html>