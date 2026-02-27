<?php
// Include database connection and authentication
require_once '../config/database.php';
require_once '../config/auth.php';

// Get current user data (optional - for logged in users)
$conn = getDatabaseConnection();
$tourGuides = []; // Initialize tour guides array
$currentUser = ['name' => 'Guest', 'email' => '']; // Initialize current user with defaults

// Check if user is logged in
$isLoggedIn = isset($_SESSION['user_id']);

if ($conn && $isLoggedIn) {
    // Get current user information only if logged in
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
    }
    $stmt->close();
}

// Fetch tour guides from database
if ($conn) {
    $guidesStmt = $conn->prepare("SELECT * FROM tour_guides WHERE status = 'active' AND verified = 1 ORDER BY name ASC");
    if ($guidesStmt) {
        $guidesStmt->execute();
        $guidesResult = $guidesStmt->get_result();
        if ($guidesResult->num_rows > 0) {
            while ($guide = $guidesResult->fetch_assoc()) {
                $tourGuides[] = $guide;
            }
        }
        $guidesStmt->close();
    } else {
        echo "<!-- Error preparing statement -->";
    }
} else {
    echo "<!-- Database connection failed -->";
}
// Don't close connection here - we need it for user preferences later
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tour Guides - San Jose del Monte Bulacan</title>
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
            min-height: 100vh;
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

        /* Enhanced Filter Section */
        .filter-section {
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.9) 0%, rgba(248, 250, 252, 0.8) 100%);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(44, 95, 45, 0.1);
            border-radius: 24px;
            padding: 32px;
            margin-bottom: 48px;
            box-shadow: 
                0 10px 40px rgba(0, 0, 0, 0.08),
                0 2px 10px rgba(0, 0, 0, 0.04);
            position: relative;
            overflow: hidden;
        }

        .filter-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--accent) 0%, var(--secondary) 25%, var(--primary) 50%, var(--secondary) 75%, var(--accent) 100%);
            opacity: 0.8;
        }

        .filter-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 32px;
            flex-wrap: wrap;
        }

        .filter-buttons {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
            align-items: center;
        }

        .filter-btn {
            padding: 12px 24px;
            border: 2px solid rgba(44, 95, 45, 0.2);
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.9) 0%, rgba(248, 250, 252, 0.8) 100%);
            color: var(--text-primary);
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.9rem;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            backdrop-filter: blur(5px);
            position: relative;
            overflow: hidden;
        }

        .filter-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, 
                transparent, 
                rgba(44, 95, 45, 0.1), 
                transparent);
            transition: left 0.6s ease;
        }

        .filter-btn:hover::before {
            left: 100%;
        }

        .filter-btn:hover {
            transform: translateY(-2px);
            box-shadow: 
                0 8px 25px rgba(44, 95, 45, 0.15),
                0 0 0 2px rgba(44, 95, 45, 0.1);
            border-color: var(--primary);
        }

        .filter-btn.active {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: white;
            border-color: var(--primary);
            box-shadow: 
                0 8px 25px rgba(44, 95, 45, 0.3),
                0 0 0 2px rgba(44, 95, 45, 0.2);
            transform: translateY(-1px);
        }

        .sort-section {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .sort-section label {
            font-weight: 600;
            color: var(--text-secondary);
            font-size: 0.9rem;
        }

        .sort-section select {
            padding: 12px 16px;
            border: 2px solid rgba(44, 95, 45, 0.2);
            border-radius: 12px;
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.9) 0%, rgba(248, 250, 252, 0.8) 100%);
            color: var(--text-primary);
            font-weight: 500;
            font-size: 0.9rem;
            cursor: pointer;
            transition: all 0.3s ease;
            backdrop-filter: blur(5px);
        }

        .sort-section select:hover {
            border-color: var(--primary);
            box-shadow: 0 4px 15px rgba(44, 95, 45, 0.1);
        }

        .sort-section select:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(44, 95, 45, 0.1);
        }

        /* Enhanced Guide Cards */
        .guides-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(380px, 1fr));
            gap: 30px;
            margin-bottom: 60px;
        }

        .guide-card {
            background: linear-gradient(135deg, #ffffff 0%, #fafbfc 100%);
            border-radius: 20px;
            overflow: hidden;
            box-shadow:
                0 10px 30px rgba(0, 0, 0, 0.1),
                0 4px 10px rgba(0, 0, 0, 0.06);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            border: 1px solid rgba(44, 95, 45, 0.06);
            min-height: 420px;
        }

        .guide-card::before {
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

        .guide-card:hover::before {
            opacity: 1;
        }

        .guide-card:hover {
            transform: translateY(-8px);
            box-shadow:
                0 20px 50px rgba(44, 95, 45, 0.15),
                0 10px 25px rgba(0, 0, 0, 0.1);
            border-color: rgba(44, 95, 45, 0.15);
        }

        .guide-header {
            position: relative;
            padding: 28px 28px 20px;
            background: linear-gradient(135deg, var(--primary-light) 0%, rgba(44, 95, 45, 0.05) 100%);
            border-bottom: 1px solid rgba(44, 95, 45, 0.1);
        }

        .guide-header::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 32px;
            right: 32px;
            height: 2px;
            background: linear-gradient(90deg, var(--primary) 0%, var(--secondary) 100%);
            opacity: 0.3;
        }

        .guide-info {
            position: relative;
            z-index: 2;
        }

        .guide-name {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 10px;
            letter-spacing: -0.5px;
            line-height: 1.2;
            background: linear-gradient(135deg, var(--text-primary) 0%, var(--primary) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            transition: all 0.3s ease;
        }

        .guide-card:hover .guide-name {
            transform: translateX(4px);
        }

        .guide-specialty {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: white;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
            margin-bottom: 16px;
            box-shadow: 0 4px 12px rgba(44, 95, 45, 0.2);
            transition: all 0.3s ease;
        }

        .guide-specialty:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(44, 95, 45, 0.3);
        }

        .guide-specialty .material-icons-outlined {
            font-size: 16px;
        }

        .guide-description {
            color: var(--text-secondary);
            font-size: 0.95rem;
            line-height: 1.6;
            margin-bottom: 24px;
            font-weight: 400;
        }

        .guide-meta {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
            margin-bottom: 24px;
        }

        .meta-item {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 12px 16px;
            background: linear-gradient(135deg, rgba(44, 95, 45, 0.05) 0%, rgba(44, 95, 45, 0.02) 100%);
            border-radius: 12px;
            font-size: 0.85rem;
            color: var(--text-secondary);
            font-weight: 500;
            border: 1px solid rgba(44, 95, 45, 0.1);
            transition: all 0.3s ease;
        }

        .meta-item:hover {
            background: linear-gradient(135deg, rgba(44, 95, 45, 0.1) 0%, rgba(44, 95, 45, 0.05) 100%);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(44, 95, 45, 0.1);
        }

        .meta-item .material-icons-outlined {
            font-size: 18px;
            color: var(--primary);
        }

        .guide-body {
            padding: 24px 28px;
            background: linear-gradient(135deg, #ffffff 0%, #fafbfc 100%);
            flex-grow: 1;
        }

        .guide-actions {
            display: flex;
            gap: 12px;
            padding: 24px 28px 28px;
            background: linear-gradient(135deg, #ffffff 0%, #fafbfc 100%);
        }

        .btn-view-profile,
        .btn-book-guide {
            flex: 1;
            padding: 14px 18px;
            border: none;
            border-radius: 12px;
            font-weight: 600;
            font-size: 0.9rem;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            position: relative;
            overflow: hidden;
        }

        .btn-view-profile {
            background: linear-gradient(135deg, var(--info) 0%, #2563eb 100%);
            color: white;
            box-shadow: 0 4px 15px rgba(59, 130, 246, 0.3);
        }

        .btn-view-profile:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(59, 130, 246, 0.4);
        }

        .btn-book-guide {
            background: linear-gradient(135deg, var(--success) 0%, #059669 100%);
            color: white;
            box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3);
        }

        .btn-book-guide:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(16, 185, 129, 0.4);
        }

        .btn-view-profile::before,
        .btn-book-guide::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, 
                transparent, 
                rgba(255, 255, 255, 0.2), 
                transparent);
            transition: left 0.6s ease;
        }

        .btn-view-profile:hover::before,
        .btn-book-guide:hover::before {
            left: 100%;
        }

        /* Verified Badge */
        .verified-badge {
            position: absolute;
            top: 24px;
            right: 24px;
            background: linear-gradient(135deg, var(--success) 0%, #059669 100%);
            color: white;
            padding: 8px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 4px;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
            z-index: 3;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }

        .verified-badge .material-icons-outlined {
            font-size: 14px;
        }
        .user-preferences-section {
            background: linear-gradient(135deg, rgba(44, 95, 45, 0.05), rgba(44, 95, 45, 0.02));
            border: 1px solid rgba(44, 95, 45, 0.1);
            border-radius: 20px;
            padding: 32px;
            margin: 48px 0;
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
            background: linear-gradient(135deg, #4a7c4e 0%, #2c5f2d 100%);
            color: white;
            padding: 12px 20px;
            border-radius: 25px;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.2s ease;
            box-shadow: 0 2px 8px rgba(74, 124, 78, 0.2);
            border: 2px solid transparent;
        }

        .preference-tag:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(74, 124, 78, 0.3);
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

        .main-content.full-width .content-area {
            padding: 40px;
            max-width: 1400px;
            margin: 0 auto;
        }

        @media (max-width: 768px) {
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
            
            .main-content.full-width .content-area {
                padding: 20px;
            }

            /* Hero Section Responsive */
            .hero-section {
                padding: 80px 20px 60px;
                min-height: 300px;
            }

            .hero-section h1 {
                font-size: 2.5rem;
                margin-bottom: 20px;
            }

            .hero-section p {
                font-size: 1.2rem;
                margin-bottom: 30px;
            }

            /* Filter Section Responsive */
            .filter-section {
                padding: 24px;
                margin-bottom: 32px;
            }

            .filter-container {
                flex-direction: column;
                gap: 20px;
                align-items: stretch;
            }

            .filter-buttons {
                justify-content: center;
                gap: 8px;
            }

            .filter-btn {
                padding: 10px 16px;
                font-size: 0.8rem;
            }

            .sort-section {
                justify-content: center;
            }

            /* Guide Cards Responsive */
            .guides-grid {
                grid-template-columns: 1fr;
                gap: 24px;
                margin-bottom: 40px;
            }

            .guide-card:hover {
                transform: translateY(-8px);
            }

            .guide-header {
                padding: 24px 24px 20px;
            }

            .guide-name {
                font-size: 1.5rem;
            }

            .guide-body {
                padding: 20px 24px;
            }

            .guide-meta {
                grid-template-columns: 1fr;
                gap: 12px;
            }

            .guide-actions {
                padding: 20px 24px 24px;
                flex-direction: column;
            }

            .btn-view-profile,
            .btn-book-guide {
                padding: 12px 16px;
                font-size: 0.85rem;
            }

            .verified-badge {
                top: 20px;
                right: 20px;
                padding: 6px 10px;
                font-size: 0.7rem;
            }
        }

        @media (max-width: 480px) {
            .hero-section h1 {
                font-size: 2rem;
            }

            .hero-section p {
                font-size: 1.1rem;
            }

            .filter-btn {
                padding: 8px 12px;
                font-size: 0.75rem;
            }

            .guide-header {
                padding: 20px 20px 16px;
            }

            .guide-name {
                font-size: 1.3rem;
            }

            .guide-body {
                padding: 16px 20px;
            }

            .guide-actions {
                padding: 16px 20px 20px;
            }

            .verified-badge {
                top: 16px;
                right: 16px;
                padding: 4px 8px;
                font-size: 0.65rem;
            }
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
            color: #1f2937;
        }

        .close-modal {
            background: none;
            border: none;
            cursor: pointer;
            padding: 8px;
            border-radius: 50%;
            color: #6b7280;
            transition: all 0.2s;
        }

        .close-modal:hover {
            background: #f3f4f6;
            color: #1f2937;
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
            background: #ef4444;
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 16px;
        }

        .logout-message h3 {
            margin: 16px 0 8px;
            color: #1f2937;
        }

        .logout-message p {
            color: #6b7280;
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
            background: #f3f4f6;
            color: #6b7280;
        }

        .btn-cancel:hover {
            background: #e5e7eb;
        }

        .btn-confirm-logout {
            background: #ef4444;
            color: white;
        }

        .btn-confirm-logout:hover {
            background: #dc2626;
        }

        /* Guide Profile Modal Specific Styles */
        .guide-profile-modal {
            max-width: 600px;
            width: 95%;
        }

        .modal-title {
            display: flex;
            align-items: center;
            gap: 12px;
            margin: 0;
        }

        .modal-icon {
            font-size: 24px;
            color: var(--primary);
        }

        .guide-profile-content {
            color: var(--text-primary);
        }

        .guide-profile-header {
            margin-bottom: 24px;
        }

        .guide-profile-info {
            text-align: center;
        }

        .guide-name-section {
            margin-bottom: 12px;
        }

        .guide-name-section h3 {
            margin: 0 0 8px 0;
            font-size: 1.5rem;
            color: var(--text-primary);
        }

        .verified-ribbon {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: linear-gradient(135deg, var(--success) 0%, #059669 100%);
            color: white;
            padding: 6px 12px;
            border-radius: 16px;
            font-size: 0.8rem;
            font-weight: 600;
            margin-top: 8px;
        }

        .verified-ribbon .material-icons-outlined {
            font-size: 14px;
        }

        .guide-category-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: var(--gray-100);
            color: var(--text-secondary);
            padding: 6px 12px;
            border-radius: 12px;
            font-size: 0.85rem;
            font-weight: 500;
            margin-top: 8px;
        }

        .guide-description-section {
            margin-bottom: 24px;
        }

        .guide-description-section h4 {
            display: flex;
            align-items: center;
            gap: 8px;
            margin: 0 0 12px 0;
            color: var(--text-primary);
            font-size: 1.1rem;
        }

        .guide-description-section p {
            margin: 0;
            line-height: 1.6;
            color: var(--text-secondary);
        }

        .guide-details-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 16px;
            margin-bottom: 24px;
        }

        .detail-item {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            padding: 16px;
            background: var(--gray-50);
            border-radius: 12px;
            border: 1px solid var(--border);
        }

        .detail-item .material-icons-outlined {
            font-size: 20px;
            color: var(--primary);
            flex-shrink: 0;
        }

        .detail-item strong {
            display: block;
            margin-bottom: 4px;
            color: var(--text-primary);
            font-size: 0.9rem;
        }

        .detail-item p {
            margin: 0;
            color: var(--text-secondary);
            font-size: 0.85rem;
        }

        .guide-booking-section {
            background: var(--gray-50);
            padding: 20px;
            border-radius: 12px;
            border: 1px solid var(--border);
        }

        .guide-booking-section h4 {
            display: flex;
            align-items: center;
            gap: 8px;
            margin: 0 0 12px 0;
            color: var(--text-primary);
            font-size: 1.1rem;
        }

        .guide-booking-section p {
            margin: 0 0 16px 0;
            color: var(--text-secondary);
            line-height: 1.5;
        }

        .booking-actions {
            display: flex;
            gap: 12px;
            justify-content: center;
        }

        .btn-primary,
        .btn-secondary {
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(44, 95, 45, 0.3);
        }

        .btn-secondary {
            background: var(--gray-200);
            color: var(--text-secondary);
        }

        .btn-secondary:hover {
            background: var(--gray-300);
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
                    <?php if ($isLoggedIn): ?>
                        <div class="user-profile-dropdown">
                            <button class="profile-trigger" onclick="toggleDropdown(this)">
                                <div class="profile-avatar"><?php echo isset($currentUser['name']) ? strtoupper(substr($currentUser['name'], 0, 1)) : 'U'; ?></div>
                                <span class="profile-name"><?php echo htmlspecialchars($currentUser['name']); ?></span>
                                <span class="material-icons-outlined">arrow_drop_down</span>
                            </button>
                            
                            <div class="dropdown-menu">
                                <div class="dropdown-header">
                                    <div class="profile-avatar-large"><?php echo isset($currentUser['name']) ? strtoupper(substr($currentUser['name'], 0, 1)) : 'U'; ?></div>
                                    <h4><?php echo htmlspecialchars($currentUser['name']); ?></h4>
                                    <p><?php echo htmlspecialchars($currentUser['email']); ?></p>
                                </div>
                                <div class="dropdown-divider"></div>
                                <a href="index.php" class="dropdown-item">
                                    <span class="material-icons-outlined">dashboard</span>
                                    <span>Dashboard</span>
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
                    <?php else: ?>
                        <!-- Sign In Button for Guests -->
                        <button class="btn-signin" onclick="window.location.href='../log-in.php'">Sign in/register</button>
                    <?php endif; ?>
                </div>
            </div>
        </header>


        <div class="content-area">
            <!-- Hero Section -->
            <div class="hero-section">
                <h1>Meet Our Local Expert Tour Guides</h1>
                <p>Discover the perfect guide to make your San Jose del Monte adventure unforgettable</p>
            </div>
            
            <!-- Filter Section -->
            <div class="filter-section">
                <div class="filter-container">
                    <div class="filter-buttons">
                        <button class="filter-btn active" data-filter="all">All Guides</button>
                        <button class="filter-btn" data-filter="mountain">Mountain Hiking</button>
                        <button class="filter-btn" data-filter="waterfall">Waterfall Tours</button>
                        <button class="filter-btn" data-filter="city">City Tours</button>
                        <button class="filter-btn" data-filter="farm">Farm & Eco-Tourism</button>
                        <button class="filter-btn" data-filter="historical">Historical Tours</button>
                        <button class="filter-btn" data-filter="general">General Tours</button>
                    </div>
                    <div class="sort-section">
                        <label>Sort by:</label>
                        <select id="sortGuides">
                            <option value="experience">Most Experience</option>
                            <option value="price-low">Price: Low to High</option>
                            <option value="price-high">Price: High to Low</option>
                        </select>
                    </div>
                </div>
            </div>

            <div id="guidesList" class="guides-grid">
                <?php
                if (!empty($tourGuides)) {
                    foreach ($tourGuides as $index => $guide) {
                        $guideId = $guide['id'];
                        $guideName = htmlspecialchars($guide['name']);
                        $guideSpecialty = htmlspecialchars($guide['specialty']);
                        $guideDescription = htmlspecialchars($guide['description'] ?? 'Experienced tour guide ready to show you the best of San Jose del Monte.');
                        $guideExperience = htmlspecialchars($guide['experience'] ?? '5+ years');
                        $guideLanguages = htmlspecialchars($guide['languages'] ?? 'English, Tagalog');
                        $guideGroupSize = htmlspecialchars($guide['max_group_size'] ?? '10 guests');
                        $guideCategory = htmlspecialchars($guide['category'] ?? 'general');
                        $guideVerified = isset($guide['verified']) && $guide['verified'] == '1';
                        
                        // Map category for data attributes
                        $categoryMap = [
                            'mountain' => 'mountain',
                            'waterfall' => 'waterfall', 
                            'city' => 'city',
                            'farm' => 'farm',
                            'historical' => 'historical',
                            'general' => 'general'
                        ];
                        $dataCategory = $categoryMap[$guideCategory] ?? 'general';
                ?>
                <div class="guide-card" data-guide-id="<?php echo $guideId; ?>" data-category="<?php echo $dataCategory; ?>">
                    <div class="guide-header">
                        <?php if ($guideVerified): ?>
                        <div class="verified-badge">
                            <span class="material-icons-outlined">verified_user</span>
                            <span>Verified</span>
                        </div>
                        <?php endif; ?>
                        <div class="guide-info">
                            <h3 class="guide-name"><?php echo $guideName; ?></h3>
                            <span class="guide-specialty">
                                <span class="material-icons-outlined">stars</span>
                                <?php echo $guideSpecialty; ?>
                            </span>
                        </div>
                    </div>
                    <div class="guide-body">
                        <p class="guide-description"><?php echo $guideDescription; ?></p>
                        <div class="guide-meta">
                            <div class="meta-item">
                                <span class="material-icons-outlined">schedule</span>
                                <?php echo $guideExperience; ?>
                            </div>
                            <div class="meta-item">
                                <span class="material-icons-outlined">translate</span>
                                <?php echo $guideLanguages; ?>
                            </div>
                            <div class="meta-item">
                                <span class="material-icons-outlined">groups</span>
                                Up to <?php echo $guideGroupSize; ?>
                            </div>
                            <div class="meta-item">
                                <span class="material-icons-outlined">wc</span>
                                <?php echo ucfirst($guide['gender'] ?? 'Not specified'); ?>
                            </div>
                        </div>
                    </div>
                    <div class="guide-actions">
                        <button class="btn-view-profile" onclick="openGuideModal(<?php echo $guideId; ?>)">
                            <span class="material-icons-outlined">person</span>
                            View Profile
                        </button>
                    </div>
                </div>
                <?php
                    }
                } else {
                    echo '<div class="no-guides-message" style="grid-column: 1 / -1; text-align: center; padding: 60px 20px;">';
                    echo '<span class="material-icons-outlined" style="font-size: 48px; color: #9ca3af;">person_off</span>';
                    echo '<h3 style="color: #6b7280; margin-top: 16px;">No tour guides available</h3>';
                    echo '<p style="color: #9ca3af;">Please check back later for available tour guides.</p>';
                    echo '</div>';
                }
                ?>
            </div>

            <!-- Dynamic Guide Profile Modals -->
            <?php
            if (!empty($tourGuides)) {
                foreach ($tourGuides as $guide) {
                    $guideId = $guide['id'];
                    $guideName = htmlspecialchars($guide['name']);
                    $guideSpecialty = htmlspecialchars($guide['specialty']);
                    $guideDescription = htmlspecialchars($guide['description'] ?? 'Experienced tour guide ready to show you the best of San Jose del Monte.');
                    $guideExperience = htmlspecialchars($guide['experience'] ?? '5+ years');
                    $guideLanguages = htmlspecialchars($guide['languages'] ?? 'English, Tagalog');
                    $guideGroupSize = htmlspecialchars($guide['max_group_size'] ?? '10 guests');
                    $guideCategory = htmlspecialchars($guide['category'] ?? 'general');
                    $guideVerified = isset($guide['verified']) && $guide['verified'] == '1';
                    $guideEmail = htmlspecialchars($guide['email'] ?? 'guide@sjdmtours.com');
                    $guidePhone = htmlspecialchars($guide['phone'] ?? '+63 912 345 6789');
                    $guideGender = htmlspecialchars($guide['gender'] ?? 'Not specified');
            ?>
            <div class="modal-overlay" id="modal-guide-<?php echo $guideId; ?>">
                <div class="modal-content guide-profile-modal">
                    <div class="modal-header">
                        <div class="modal-title">
                            <span class="material-icons-outlined modal-icon">person</span>
                            <h2>Guide Profile</h2>
                        </div>
                        <button class="close-modal" onclick="closeModal('modal-guide-<?php echo $guideId; ?>')">
                            <span class="material-icons-outlined">close</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="guide-profile-content">
                            <div class="guide-profile-header">
                                <div class="guide-profile-info">
                                    <div class="guide-name-section">
                                        <h3><?php echo $guideName; ?></h3>
                                        <?php if ($guideVerified) { ?>
                                        <div class="verified-ribbon">
                                            <span class="material-icons-outlined">verified_user</span>
                                            <span>Trusted Professional</span>
                                        </div>
                                        <?php } ?>
                                    </div>
                                    <p class="guide-specialty"><?php echo $guideSpecialty; ?></p>
                                    <div class="guide-category-badge">
                                        <span class="material-icons-outlined">category</span>
                                        <?php echo ucfirst($guideCategory); ?>
                                    </div>
                                </div>
                            </div>

                            <div class="guide-description-section">
                                <h4><span class="material-icons-outlined">info</span> About</h4>
                                <p><?php echo $guideDescription; ?></p>
                            </div>

                            <div class="guide-details-grid">
                                <div class="detail-item">
                                    <span class="material-icons-outlined">wc</span>
                                    <div>
                                        <strong>Gender</strong>
                                        <p><?php echo $guideGender; ?></p>
                                    </div>
                                </div>
                                <div class="detail-item">
                                    <span class="material-icons-outlined">translate</span>
                                    <div>
                                        <strong>Languages</strong>
                                        <p><?php echo $guideLanguages; ?></p>
                                    </div>
                                </div>
                                <div class="detail-item">
                                    <span class="material-icons-outlined">groups</span>
                                    <div>
                                        <strong>Group Size</strong>
                                        <p>Up to <?php echo $guideGroupSize; ?></p>
                                    </div>
                                </div>
                                <div class="detail-item">
                                    <span class="material-icons-outlined">place</span>
                                    <div>
                                        <strong>Location</strong>
                                        <p>San Jose del Monte, Bulacan</p>
                                    </div>
                                </div>
                                <div class="detail-item">
                                    <span class="material-icons-outlined">email</span>
                                    <div>
                                        <strong>Email</strong>
                                        <p><?php echo $guideEmail; ?></p>
                                    </div>
                                </div>
                                <div class="detail-item">
                                    <span class="material-icons-outlined">phone</span>
                                    <div>
                                        <strong>Phone</strong>
                                        <p><?php echo $guidePhone; ?></p>
                                    </div>
                                </div>
                            </div>

                            <div class="guide-booking-section">
                                <h4><span class="material-icons-outlined">calendar_today</span> Booking Information</h4>
                                <p>To book this guide and get detailed tour information, please click the button below.</p>
                                <div class="booking-actions">
                                    <button class="btn-primary" onclick="bookGuide(<?php echo $guideId; ?>)">
                                        <span class="material-icons-outlined">calendar_today</span>
                                        Book This Guide
                                    </button>
                                    <button class="btn-secondary" onclick="closeModal('modal-guide-<?php echo $guideId; ?>')">
                                        Close
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
                }
            }
            ?>
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
                <!-- Filter Tabs -->
                <div class="booking-filter-tabs">
                    <button class="filter-tab active" data-filter="all">
                        <span class="material-icons-outlined">list_alt</span>
                        <span>All Bookings</span>
                    </button>
                    <button class="filter-tab" data-filter="pending">
                        <span class="material-icons-outlined">schedule</span>
                        <span>Pending</span>
                    </button>
                    <button class="filter-tab" data-filter="confirmed">
                        <span class="material-icons-outlined">check_circle</span>
                        <span>Confirmed</span>
                    </button>
                    <button class="filter-tab" data-filter="completed">
                        <span class="material-icons-outlined">verified</span>
                        <span>Completed</span>
                    </button>
                    <button class="filter-tab" data-filter="cancelled">
                        <span class="material-icons-outlined">cancel</span>
                        <span>Cancelled</span>
                    </button>
                </div>

                <!-- Bookings List -->
                <div id="modalBookingsList" class="bookings-container"></div>
            </div>
        </div>
    </div>

    <script src="script.js"></script>
    <script>
        // Pass current user data to JavaScript
        <?php if (isset($currentUser)): ?>
        const currentUser = <?php echo json_encode($currentUser); ?>;
        localStorage.setItem('currentUser', JSON.stringify(currentUser));
        <?php endif; ?>

        // Modal functionality
        let currentModal = null;

        function openModal(modalId) {
            // Close any existing modal first
            if (currentModal) {
                closeModal(currentModal);
            }
            
            const modal = document.getElementById(modalId);
            if (modal) {
                currentModal = modalId;
                modal.classList.add('show');
                document.body.style.overflow = 'hidden';
                
                // Load content based on modal type
                if (modalId === 'bookingHistoryModal') {
                    loadBookingHistory();
                }
            }
        }

        function closeModal(modalId) {
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.classList.remove('show');
                document.body.style.overflow = 'auto';
                if (currentModal === modalId) {
                    currentModal = null;
                }
            }
        }

        function openGuideModal(guideId) {
            openModal('modal-guide-' + guideId);
        }

        // bookGuide function is handled in script.js

        // Close modal when clicking outside (but not on modal content)
        document.addEventListener('click', function(event) {
            if (event.target.classList.contains('modal-overlay') && 
                !event.target.closest('.modal-content')) {
                const modalId = event.target.id;
                if (modalId) {
                    closeModal(modalId);
                }
            }
        });

        // Close modal with Escape key
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape' && currentModal) {
                closeModal(currentModal);
            }
        });

        // Check for guide parameter in URL and open modal
        document.addEventListener('DOMContentLoaded', function() {
            const urlParams = new URLSearchParams(window.location.search);
            const guideId = urlParams.get('guide');
            
            if (guideId) {
                // Open the guide modal after a short delay
                setTimeout(() => {
                    openGuideModal(guideId);
                }, 500);
            }
        });

        function loadBookingHistory() {
            const container = document.getElementById('modalBookingsList');
            if (!container) return;
            
            const userBookings = JSON.parse(localStorage.getItem('userBookings')) || [];
            
            if (userBookings.length === 0) {
                container.innerHTML = `
                    <div class="empty-state-card">
                        <div class="empty-state-icon">
                            <span class="material-icons-outlined">event_busy</span>
                        </div>
                        <h3 class="empty-state-title">No bookings found</h3>
                        <p class="empty-state-text">Start your adventure by booking your first tour with our experienced guides.</p>
                        <button class="btn-primary-action" onclick="closeModal('bookingHistoryModal'); window.location.href='book.php'">
                            <span class="material-icons-outlined">explore</span>
                            <span>Book Now</span>
                        </button>
                    </div>
                `;
                return;
            }
            
            container.innerHTML = userBookings.reverse().map(booking => `
                <div class="booking-card" data-status="${booking.status}">
                    <div class="booking-card-header">
                        <div class="booking-primary-info">
                            <div class="booking-icon">
                                <span class="material-icons-outlined">tour</span>
                            </div>
                            <div class="booking-title-section">
                                <h3 class="booking-title">${booking.guideName}</h3>
                                <p class="booking-destination">
                                    <span class="material-icons-outlined">place</span>
                                    ${booking.destination}
                                </p>
                            </div>
                        </div>
                        <span class="status-badge status-${booking.status}">
                            ${getStatusIcon(booking.status)}
                            <span>${booking.status.toUpperCase()}</span>
                        </span>
                    </div>
                    
                    <div class="booking-card-divider"></div>
                    
                    <div class="booking-details-grid">
                        <div class="detail-item">
                            <div class="detail-icon">
                                <span class="material-icons-outlined">event</span>
                            </div>
                            <div class="detail-content">
                                <div class="detail-label">Check-in Date</div>
                                <div class="detail-value">${formatDate(booking.checkIn)}</div>
                            </div>
                        </div>
                        
                        <div class="detail-item">
                            <div class="detail-icon">
                                <span class="material-icons-outlined">people</span>
                            </div>
                            <div class="detail-content">
                                <div class="detail-label">Number of Guests</div>
                                <div class="detail-value">${booking.guests} Guest${booking.guests > 1 ? 's' : ''}</div>
                            </div>
                        </div>
                        
                        <div class="detail-item highlight">
                            <div class="detail-icon">
                                <span class="material-icons-outlined">payments</span>
                            </div>
                            <div class="detail-content">
                                <div class="detail-label">Total Amount</div>
                                <div class="detail-value price">₱${booking.totalAmount ? booking.totalAmount.toLocaleString() : '2,600'}</div>
                            </div>
                        </div>
                    </div>
                </div>
            `).join('');
        }

        function getStatusIcon(status) {
            const icons = {
                'pending': '<span class="material-icons-outlined">schedule</span>',
                'confirmed': '<span class="material-icons-outlined">check_circle</span>',
                'completed': '<span class="material-icons-outlined">verified</span>',
                'cancelled': '<span class="material-icons-outlined">cancel</span>'
            };
            return icons[status] || '<span class="material-icons-outlined">info</span>';
        }

        function formatDate(dateString) {
            const date = new Date(dateString);
            const options = { weekday: 'short', month: 'short', day: 'numeric', year: 'numeric' };
            return date.toLocaleDateString('en-US', options);
        }

        // ========== USER PROFILE DROPDOWN ==========
        function initUserProfileDropdown() {
            // More specific selectors to avoid conflicts
            const profileDropdown = document.querySelector('.header-actions .user-profile-dropdown');
            const profileTrigger = document.querySelector('.header-actions .profile-trigger');
            const dropdownMenu = document.querySelector('.header-actions .dropdown-menu');
            const logoutLink = document.querySelector('.header-actions [href="logout.php"]');

            console.log('Dropdown elements found:', {
                profileDropdown: !!profileDropdown,
                profileTrigger: !!profileTrigger,
                dropdownMenu: !!dropdownMenu,
                logoutLink: !!logoutLink
            });

            if (!profileDropdown || !profileTrigger || !dropdownMenu) {
                console.log('Profile dropdown elements not found');
                return;
            }

            // Remove any existing event listeners to prevent duplicates
            const newTrigger = profileTrigger.cloneNode(true);
            profileTrigger.parentNode.replaceChild(newTrigger, profileTrigger);

            // Toggle dropdown on click
            newTrigger.addEventListener('click', function (e) {
                e.preventDefault();
                e.stopPropagation();
                console.log('Dropdown clicked, current classes:', dropdownMenu.className);
                dropdownMenu.classList.toggle('show');
                console.log('Dropdown toggled, new classes:', dropdownMenu.className);
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
                            <h3>Are you sure you want to sign out?</h3>
                            <p>You will need to log in again to access your account.</p>
                        </div>
                        <div class="modal-actions">
                            <button class="btn-cancel" onclick="this.closest('.modal-overlay').remove()">
                                <span class="material-icons-outlined">close</span>
                                Cancel
                            </button>
                            <button class="btn-confirm-logout" onclick="window.location.href='logout.php'">
                                <span class="material-icons-outlined">logout</span>
                                Sign Out
                            </button>
                        </div>
                    </div>
                </div>
            `;
            document.body.appendChild(modal);
            
            // Show modal with animation
            setTimeout(() => modal.classList.add('show'), 10);
        }

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function () {
            initUserProfileDropdown();
        });
    </script>
</body>
</html>
<?php
// Close database connection at the very end
if ($conn) {
    closeDatabaseConnection($conn);
}
?>