<?php
// Include database connection and authentication
require_once '../config/database.php';
require_once '../config/auth.php';

// OpenWeatherMap API configuration
$apiKey = '6c21a0d2aaf514cb8d21d56814312b19';
$weatherUrl = "https://api.openweathermap.org/data/2.5/weather?q=San%20Jose%20Del%20Monte,Bulacan&appid={$apiKey}&units=metric";

$weatherData = null;
$weatherError = null;
$currentTemp = '28';
$weatherLabel = 'Sunny';

// Fetch weather data
try {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $weatherUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    $weatherResponse = curl_exec($ch);
    
    if (curl_errno($ch)) {
        $weatherError = 'Weather API connection error';
    } else {
        $weatherData = json_decode($weatherResponse, true);
        if ($weatherData && isset($weatherData['main']) && isset($weatherData['weather'][0])) {
            $currentTemp = round($weatherData['main']['temp']);
            $weatherLabel = ucfirst($weatherData['weather'][0]['description']);
        } else {
            $weatherError = 'Weather data unavailable';
        }
    }
    curl_close($ch);
} catch (Exception $e) {
    $weatherError = 'Weather service unavailable';
}

// Get current date and weekday
$currentWeekday = date('l'); // Full weekday name
$currentDate = date('F Y'); // Month Year format

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../log-in/log-in.php');
    exit();
}

// Get current user data
$conn = getDatabaseConnection();
if ($conn) {
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
    closeDatabaseConnection($conn);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tourist Spots - San Jose del Monte Bulacan</title>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
    <style>

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
        /* Modal Animation */
        @keyframes modalFadeIn {
            from {
                opacity: 0;
                transform: translateY(50px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .modal.show .modal-content {
            animation: modalFadeIn 0.3s ease-out;
        }

        .modal-guides-list {
            display: flex;
            flex-direction: column;
            gap: 10px;
            margin-top: 15px;
        }

        .modal-guide-item {
            display: flex;
            align-items: center;
            padding: 12px;
            background: rgba(74, 124, 78, 0.05);
            border-radius: 8px;
            border: 1px solid rgba(74, 124, 78, 0.1);
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .modal-guide-item:hover {
            background: rgba(74, 124, 78, 0.1);
            transform: translateY(-2px);
        }

        .modal-guide-info {
            flex: 1;
        }

        .modal-guide-name {
            font-weight: 600;
            color: #2c5f2d;
            font-size: 14px;
        }

        .modal-guide-specialty {
            font-size: 12px;
            color: #666;
            margin: 2px 0;
        }

        .modal-guide-rating {
            display: flex;
            align-items: center;
            gap: 4px;
            font-size: 12px;
        }

        .verified-badge {
            color: #4caf50 !important;
            font-size: 14px !important;
            margin-left: 4px;
        }
        
        /* Card Weather Styling */
        .card-weather {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 12px;
            padding: 8px 12px;
            background: linear-gradient(135deg, rgba(74, 124, 78, 0.1), rgba(44, 95, 45, 0.05));
            border-radius: 12px;
            border: 1px solid rgba(74, 124, 78, 0.2);
            font-size: 0.85rem;
        }
        
        .card-weather .material-icons-outlined {
            color: #4a7c4e;
            font-size: 18px;
        }
        
        .weather-temp {
            font-weight: 600;
            color: #2c5f2d;
            font-size: 0.9rem;
        }
        
        .weather-desc {
            color: #666;
            font-size: 0.8rem;
            text-transform: capitalize;
        }
        
        /* Card Guides Styling */
        .card-guides {
            margin-top: 12px;
            padding: 12px;
            background: rgba(74, 124, 78, 0.05);
            border-radius: 8px;
            border: 1px solid rgba(74, 124, 78, 0.1);
        }
        
        .guides-label {
            font-size: 0.85rem;
            font-weight: 600;
            color: #2c5f2d;
            display: block;
            margin-bottom: 8px;
        }
        
        .guides-list {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }
        
        .guide-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 8px 12px;
            background: white;
            border-radius: 6px;
            border: 1px solid rgba(74, 124, 78, 0.2);
            cursor: pointer;
            transition: all 0.2s ease;
        }
        
        .guide-item:hover {
            background: rgba(74, 124, 78, 0.1);
            transform: translateY(-1px);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }
        
        .guide-info {
            display: flex;
            flex-direction: column;
            gap: 2px;
        }
        
        .guide-name {
            font-size: 0.9rem;
            font-weight: 500;
            color: #2c5f2d;
        }
        
        .guide-specialty {
            font-size: 0.8rem;
            color: #666;
        }
        
        .guide-rating {
            display: flex;
            align-items: center;
            gap: 4px;
        }
        
        .verified-icon {
            color: #4caf50 !important;
        }
        
        /* Responsive adjustments */
        @media (max-width: 768px) {
            .card-guides {
                padding: 10px;
                margin-top: 10px;
            }
            
            .guide-item {
                padding: 6px 10px;
                flex-direction: column;
                align-items: flex-start;
                gap: 8px;
            }
            
            .guide-info {
                width: 100%;
            }
            
            .guide-rating {
                align-self: flex-end;
            }
        }
        @media (max-width: 768px) {
            .card-weather {
                padding: 6px 10px;
                gap: 6px;
                font-size: 0.8rem;
            }
            
            .card-weather .material-icons-outlined {
                font-size: 16px;
            }
            
            .weather-temp {
                font-size: 0.85rem;
            }
            
            .weather-desc {
                font-size: 0.75rem;
            }
        }
        
        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 9999;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.85);
            backdrop-filter: blur(8px);
        }
        
        .modal-content {
            background: white;
            margin: 20px auto;
            padding: 0;
            border-radius: 24px;
            width: 95%;
            max-width: 980px;
            max-height: 95vh;
            overflow: hidden;
            box-shadow: 
                0 32px 64px rgba(0, 0, 0, 0.25),
                0 16px 32px rgba(0, 0, 0, 0.15),
                0 8px 16px rgba(0, 0, 0, 0.1);
            position: relative;
            border: 1px solid rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
            display: flex;
            flex-direction: column;
            align-items: stretch;
            justify-content: flex-start;
        }
        
        /* Enhanced Modal Styles */
        .modal-header {
            display: flex;
            flex-direction: column;
            gap: 12px;
            padding: 20px 60px 20px 24px; /* Extra right padding for close button */
            background: linear-gradient(135deg, #4a8c4a 0%, #2c5f2d 100%);
            position: relative;
        }

        .category-badge {
            display: inline-flex;
            align-items: center;
            padding: 6px 16px;
            background: rgba(255, 255, 255, 0.25);
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            color: white;
            width: fit-content;
            backdrop-filter: blur(10px);
        }

        .modal-title {
            font-size: 1.75rem;
            font-weight: 700;
            color: white;
            margin: 0;
            line-height: 1.3;
        }

        .close-btn {
            position: absolute;
            top: 16px;
            right: 16px;
            background: rgba(255, 255, 255, 0.2);
            border: none;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            color: white;
            font-size: 1.25rem;
        }
                
        .modal-header-content {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            padding: 40px 40px 32px 40px;
            background: linear-gradient(135deg, #4a7c4e, #2c5f2d);
            min-height: 120px;
        }
        
        .modal-title-section {
            flex: 1;
        }
        
        .modal-category {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 0.85em;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            display: inline-block;
            margin-bottom: 12px;
            backdrop-filter: blur(10px);
        }
        
        .modal-header h2 {
            font-size: 2em;
            font-weight: 700;
            color: white;
            margin: 0;
            line-height: 1.2;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        
        .modal-close {
            background: rgba(255, 255, 255, 0.15);
            border: none;
            border-radius: 12px;
            width: 44px;
            height: 44px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
        }
        
        .modal-close:hover {
            background: rgba(255, 255, 255, 0.25);
            transform: scale(1.05);
        }
        
        .modal-close .material-icons-outlined {
            color: white;
            font-size: 22px;
        }
        
        .modal-body {
            display: flex;
            flex-direction: column;
            max-height: calc(90vh - 120px);
            overflow-y: auto;
            width: 100%;
            align-items: stretch;
        }
        
        .modal-hero-section {
            position: relative;
            width: 100%;
            padding-top: 56.25%; /* 16:9 aspect ratio */
            overflow: hidden;
            background: #f0f0f0;
        }
        
        .modal-image-container {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .modal-image-container img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center;
            display: block;
        }
        
        .modal-image-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(to bottom, 
                rgba(0, 0, 0, 0.1) 0%, 
                rgba(0, 0, 0, 0.4) 70%,
                rgba(0, 0, 0, 0.6) 100%);
            pointer-events: none;
        }
        
        .modal-badge {
            position: absolute;
            top: 24px;
            left: 24px;
            background: rgba(255, 255, 255, 0.95);
            color: #2c5f2d;
            padding: 10px 18px;
            border-radius: 25px;
            display: flex;
            align-items: center;
            gap: 8px;
            font-weight: 600;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
            backdrop-filter: blur(10px);
        }
        
        .modal-content-section {
            padding: 40px 36px;
            display: flex;
            flex-direction: column;
            gap: 40px;
            align-items: center;
            justify-content: flex-start;
            width: 100%;
            max-width: 100%;
        }
        
        .modal-info-header {
            text-align: center;
            padding: 0 20px;
            width: 100%;
            max-width: 800px;
            margin: 0 auto;
        }
        
        .modal-info-header h3 {
            font-size: 2.4em;
            font-weight: 700;
            color: #1a1a1a;
            margin: 0 0 20px 0;
            line-height: 1.1;
            background: linear-gradient(135deg, #1a1a1a, #333);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            text-align: center;
        }
        
        .modal-info-header p {
            font-size: 1.2em;
            line-height: 1.8;
            color: #555;
            margin: 0;
            max-width: 650px;
            margin: 0 auto;
            font-weight: 400;
            text-align: center;
        }
        
        .modal-stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 24px;
            margin: 0 -8px;
            width: 100%;
            max-width: 100%;
        }
        
        .modal-stat-card {
            background: linear-gradient(145deg, #ffffff, #f8f9fa);
            border: 1px solid rgba(44, 95, 45, 0.08);
            border-radius: 20px;
            padding: 28px 24px;
            display: flex;
            align-items: center;
            gap: 20px;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 
                0 4px 20px rgba(0, 0, 0, 0.06),
                0 2px 8px rgba(0, 0, 0, 0.04);
            position: relative;
            overflow: hidden;
        }
        
        .modal-stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, #4a7c4e, #2c5f2d);
            transform: scaleX(0);
            transition: transform 0.4s ease;
        }
        
        .modal-stat-card:hover {
            transform: translateY(-6px);
            box-shadow: 
                0 12px 40px rgba(44, 95, 45, 0.15),
                0 6px 20px rgba(0, 0, 0, 0.08);
            border-color: rgba(44, 95, 45, 0.2);
        }
        
        .modal-stat-card:hover::before {
            transform: scaleX(1);
        }
        
        .stat-icon {
            width: 64px;
            height: 64px;
            background: linear-gradient(135deg, #4a7c4e, #2c5f2d);
            color: white;
            border-radius: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            flex-shrink: 0;
            box-shadow: 
                0 8px 24px rgba(44, 95, 45, 0.3),
                0 4px 12px rgba(44, 95, 45, 0.2);
            transition: all 0.3s ease;
        }
        
        .modal-stat-card:hover .stat-icon {
            transform: scale(1.05);
            box-shadow: 
                0 12px 32px rgba(44, 95, 45, 0.4),
                0 6px 16px rgba(44, 95, 45, 0.3);
        }
        
        .stat-info {
            flex: 1;
        }
        
        .stat-label {
            font-size: 0.9em;
            color: #666;
            margin-bottom: 4px;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .stat-value {
            font-size: 1.3em;
            font-weight: 700;
            color: #1a1a1a;
        }
        
        .modal-features-section {
            background: linear-gradient(135deg, rgba(44, 95, 45, 0.03), rgba(44, 95, 45, 0.01));
            padding: 32px;
            border-radius: 20px;
            border: 1px solid rgba(44, 95, 45, 0.08);
            width: 100%;
            max-width: 100%;
            align-self: center;
        }
        
        .section-header {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            margin-bottom: 24px;
            text-align: center;
        }
        
        .section-header h4 {
            font-size: 1.5em;
            font-weight: 600;
            color: #1a1a1a;
            margin: 0;
            text-align: center;
        }
        
        .section-icon {
            width: 36px;
            height: 36px;
            background: linear-gradient(135deg, #ffd700, #ffb300);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            box-shadow: 0 4px 12px rgba(255, 183, 0, 0.3);
            flex-shrink: 0;
        }
        
        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 16px;
            width: 100%;
            justify-items: stretch;
        }
        
        .feature-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 16px;
            background: white;
            border-radius: 12px;
            border: 1px solid #e9ecef;
            transition: all 0.3s ease;
            width: 100%;
            justify-content: flex-start;
        }
        
        .feature-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            border-color: #2c5f2d;
        }
        
        .feature-icon {
            font-size: 20px;
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #f8f9fa, #ffffff);
            border-radius: 8px;
            flex-shrink: 0;
        }
        
        .feature-item span:last-child {
            font-size: 0.95em;
            color: #444;
            font-weight: 500;
            flex: 1;
        }
        
        .modal-actions-section {
            padding-top: 8px;
            width: 100%;
            max-width: 600px;
            align-self: center;
        }
        
        .action-buttons {
            display: flex;
            gap: 16px;
            width: 100%;
            justify-content: center;
        }
        
        .modal-book-btn,
        .modal-save-btn {
            flex: 1;
            padding: 18px 28px;
            border: none;
            border-radius: 14px;
            font-size: 1.05em;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .modal-book-btn {
            background: linear-gradient(135deg, #4a7c4e, #2c5f2d);
            color: white;
            box-shadow: 0 6px 20px rgba(44, 95, 45, 0.3);
        }
        
        .modal-book-btn:hover {
            background: linear-gradient(135deg, #3d6341, #244d26);
            transform: translateY(-3px);
            box-shadow: 0 8px 28px rgba(44, 95, 45, 0.4);
        }
        
        .modal-save-btn {
            background: linear-gradient(135deg, #f8f9fa, #ffffff);
            color: #444;
            border: 2px solid #e9ecef;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }
        
        .modal-save-btn:hover {
            background: linear-gradient(135deg, #fff3e0, #ffe0b2);
            border-color: #ff9800;
            color: #e65100;
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(255, 152, 0, 0.2);
        }
        
        .modal-save-btn.saved {
            background: linear-gradient(135deg, #ff9800, #f57c00);
            color: white;
            border-color: #ff9800;
            box-shadow: 0 6px 20px rgba(255, 152, 0, 0.3);
        }
        
        .modal-save-btn.saved:hover {
            background: linear-gradient(135deg, #f57c00, #e65100);
            box-shadow: 0 8px 28px rgba(255, 152, 0, 0.4);
        }
        
        @media (max-width: 768px) {
            .modal-content {
                margin: 20px;
                width: calc(100% - 40px);
                max-height: calc(100vh - 40px);
            }
            
            .modal-header-content {
                padding: 24px 24px 20px 24px;
            }
            
            .modal-header h2 {
                font-size: 1.6em;
            }
            
            .modal-body {
                flex-direction: column;
                max-height: calc(100vh - 100px);
            }
            
            .modal-hero-section {
                padding-top: 60%; /* Adjusted for mobile */
            }
            
            .modal-badge {
                top: 16px;
                left: 16px;
                padding: 8px 14px;
                font-size: 0.85em;
            }
            
            .modal-content-section {
                padding: 24px;
                gap: 24px;
            }
            
            .modal-info-header h3 {
                font-size: 1.8em;
            }
            
            .modal-info-header p {
                font-size: 1.05em;
            }
            
            .modal-stats-grid {
                grid-template-columns: 1fr;
                gap: 16px;
            }
            
            .modal-stat-card {
                padding: 20px;
            }
            
            .stat-icon {
                width: 48px;
                height: 48px;
                font-size: 22px;
            }
            
            .modal-features-section {
                padding: 24px;
            }
            
            .features-grid {
                grid-template-columns: 1fr;
                gap: 12px;
            }
            
            .feature-item {
                padding: 14px;
            }
            
            .action-buttons {
                flex-direction: column;
                gap: 12px;
            }
            
            .modal-book-btn,
            .modal-save-btn {
                padding: 16px 24px;
                font-size: 1em;
            }
            
            .modal-guides-section {
                padding: 24px;
            }
            
            .modal-guides-list {
                grid-template-columns: 1fr;
                gap: 12px;
            }
            
            .modal-guide-item {
                padding: 16px;
            }
        }
        
        @media (max-width: 480px) {
            .modal-content {
                margin: 10px;
                width: calc(100% - 20px);
                max-height: calc(100vh - 20px);
            }
            
            .modal-header-content {
                padding: 20px 20px 16px 20px;
            }
            
            .modal-header h2 {
                font-size: 1.4em;
            }
            
            .modal-hero-section {
                padding-top: 65%; /* Further adjusted for small screens */
            }
            
            .modal-badge {
                top: 12px;
                left: 12px;
                padding: 6px 12px;
                font-size: 0.8em;
            }
            
            .modal-content-section {
                padding: 20px;
                gap: 20px;
            }
            
            .modal-info-header h3 {
                font-size: 1.6em;
            }
            
            .modal-info-header p {
                font-size: 1em;
            }
            
            .modal-stat-card {
                padding: 16px;
            }
            
            .stat-icon {
                width: 44px;
                height: 44px;
                font-size: 20px;
            }
            
            .modal-features-section {
                padding: 20px;
            }
            
            .section-header h4 {
                font-size: 1.3em;
            }
            
            .section-icon {
                width: 32px;
                height: 32px;
                font-size: 16px;
            }
            
            .feature-item {
                padding: 12px;
            }
            
            .feature-icon {
                width: 28px;
                height: 28px;
                font-size: 18px;
            }
            
            .modal-actions-section {
                padding-top: 0;
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
            <a class="nav-item" href="book.php">
                <span class="material-icons-outlined">event</span>
                <span>Book Now</span>
            </a>
            <a class="nav-item active" href="javascript:void(0)">
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
            <h1>Tourist Spots</h1>
            <div class="search-bar">
                <span class="material-icons-outlined">search</span>
                <input type="text" placeholder="Search destinations...">
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
            <h2 class="section-title">San Jose del Monte Tourist Spots</h2>
            
            <!-- Calendar Header -->
            <div class="calendar-header">
                <div class="date-display">
                    <div class="weekday" id="currentWeekday"><?php echo htmlspecialchars($currentWeekday); ?></div>
                    <div class="month-year" id="currentDate"><?php echo htmlspecialchars($currentDate); ?></div>
                </div>
                <div class="weather-info">
                    <span class="material-icons-outlined"><?php echo $weatherLabel === 'Clear' ? 'wb_sunny' : ($weatherLabel === 'Clouds' ? 'cloud' : 'wb_cloudy'); ?></span>
                    <span class="temperature"><?php echo $currentTemp; ?>°C</span>
                    <span class="weather-label"><?php echo htmlspecialchars($weatherLabel); ?></span>
                </div>
            </div>

            <!-- Filters -->
            <div class="travelry-filters">
                <div class="filter-row">
                    <div class="filter-group">
                        <label>Category</label>
                        <select class="filter-select" id="categoryFilter">
                            <option value="all">All Categories</option>
                            <?php
                            // Fetch unique categories from database
                            $conn = getDatabaseConnection();
                            if ($conn) {
                                $query = "SELECT DISTINCT category FROM tourist_spots WHERE status = 'active'";
                                $result = $conn->query($query);
                                if ($result && $result->num_rows > 0) {
                                    while ($category = $result->fetch_assoc()) {
                                        echo '<option value="' . $category['category'] . '">' . ucfirst($category['category']) . '</option>';
                                    }
                                }
                                closeDatabaseConnection($conn);
                            }
                            ?>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label>Activity Level</label>
                        <select class="filter-select" id="activityFilter">
                            <option value="all">All Levels</option>
                            <option value="easy">Easy</option>
                            <option value="moderate">Moderate</option>
                            <option value="difficult">Difficult</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label>Duration</label>
                        <select class="filter-select" id="durationFilter">
                            <option value="all">All Durations</option>
                            <option value="1-2">1-2 hours</option>
                            <option value="2-4">2-4 hours</option>
                            <option value="4+">4+ hours</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Tourist Spots Grid -->
            <div class="travelry-grid" id="spotsGrid">
                <?php
                // Fetch tourist spots from database with assigned guides
                $conn = getDatabaseConnection();
                if ($conn) {
                    $query = "SELECT ts.*, 
                             GROUP_CONCAT(DISTINCT CONCAT(tg.id, ':', tg.name, ':', tg.specialty, ':', tg.rating, ':', tg.verified) ORDER BY tg.rating DESC SEPARATOR '|') as guides_info
                             FROM tourist_spots ts 
                             LEFT JOIN guide_destinations gd ON ts.id = gd.destination_id 
                             LEFT JOIN tour_guides tg ON gd.guide_id = tg.id AND tg.status = 'active'
                             WHERE ts.status = 'active' 
                             GROUP BY ts.id 
                             ORDER BY ts.name";
                    $result = $conn->query($query);
                    
                    if ($result && $result->num_rows > 0) {
                        while ($spot = $result->fetch_assoc()) {
                            // Map database categories to display categories
                            $categoryMap = [
                                'nature' => 'Nature & Waterfalls',
                                'farm' => 'Farms & Eco-Tourism', 
                                'park' => 'Parks & Recreation',
                                'religious' => 'Religious Sites',
                                'urban' => 'Urban Landmarks',
                                'historical' => 'Historical Sites',
                                'waterfalls' => 'Waterfalls',
                                'mountains' => 'Mountains & Hiking',
                                'agri-tourism' => 'Agri-Tourism',
                                'religious sites' => 'Religious Sites',
                                'parks & recreation' => 'Parks & Recreation',
                                'tourist spot' => 'Tourist Spots'
                            ];
                            
                            // Map database categories to badge icons
                            $iconMap = [
                                'nature' => 'landscape',
                                'farm' => 'agriculture',
                                'park' => 'park',
                                'religious' => 'church',
                                'urban' => 'location_city',
                                'historical' => 'account_balance',
                                'waterfalls' => 'water',
                                'mountains' => 'terrain',
                                'agri-tourism' => 'agriculture',
                                'religious sites' => 'church',
                                'parks & recreation' => 'park',
                                'tourist spot' => 'place'
                            ];
                            
                            // Map database categories to badge labels
                            $badgeMap = [
                                'nature' => 'Nature',
                                'farm' => 'Farm',
                                'park' => 'Park',
                                'religious' => 'Religious',
                                'urban' => 'Urban',
                                'historical' => 'Historical',
                                'waterfalls' => 'Waterfalls',
                                'mountains' => 'Mountain',
                                'agri-tourism' => 'Farm',
                                'religious sites' => 'Religious',
                                'parks & recreation' => 'Park',
                                'tourist spot' => 'Tourist'
                            ];
                            
                            $category = $spot['category'];
                            $displayCategory = $categoryMap[$category] ?? $category;
                            $icon = $iconMap[$category] ?? 'place';
                            $badge = $badgeMap[$category] ?? $category;
                            
                            // Generate star rating HTML
                            $rating = floatval($spot['rating']);
                            $fullStars = floor($rating);
                            $hasHalfStar = ($rating - $fullStars) >= 0.5;
                            $starsHtml = '';
                            
                            for ($i = 0; $i < $fullStars; $i++) {
                                $starsHtml .= '<span class="material-icons-outlined" style="color: #ffc107; font-size: 16px;">star</span>';
                            }
                            if ($hasHalfStar) {
                                $starsHtml .= '<span class="material-icons-outlined" style="color: #ffc107; font-size: 16px;">star_half</span>';
                            }
                            $emptyStars = 5 - $fullStars - ($hasHalfStar ? 1 : 0);
                            for ($i = 0; $i < $emptyStars; $i++) {
                                $starsHtml .= '<span class="material-icons-outlined" style="color: #ddd; font-size: 16px;">star_outline</span>';
                            }
                            
                            // Determine activity level based on difficulty
                            $activityLevel = $spot['difficulty_level'];
                            
                            // Get duration for filtering
                            $duration = $spot['duration'] ?? '2-3 hours';
                            
                            // Parse guides information
                            $guides = [];
                            if (!empty($spot['guides_info'])) {
                                $guideData = explode('|', $spot['guides_info']);
                                foreach ($guideData as $guide) {
                                    $guideParts = explode(':', $guide);
                                    if (count($guideParts) >= 5) {
                                        $guides[] = [
                                            'id' => $guideParts[0],
                                            'name' => $guideParts[1],
                                            'specialty' => $guideParts[2],
                                            'rating' => $guideParts[3],
                                            'verified' => $guideParts[4]
                                        ];
                                    }
                                }
                            }
                            
                            echo '<div class="travelry-card" data-category="' . $category . '" data-activity="' . $activityLevel . '" data-duration="' . $duration . '">';
                            echo '<div class="card-image">';
                            
                            // Get individual image for each tourist spot
                            $imageUrl = !empty($spot['image_url']) ? $spot['image_url'] : '';
                            $imageAlt = htmlspecialchars($spot['name']);
                            
                            if (!empty($imageUrl)) {
                                echo '<img src="' . htmlspecialchars($imageUrl) . '" alt="' . $imageAlt . '" 
                                     loading="lazy"
                                     onerror="this.onerror=null; this.src=\'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDAwIiBoZWlnaHQ9IjIwMCIgdmlld0JveD0iMCAwIDQwMCAyMDAiIGZpbGw9Im5vbmUiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+CjxyZWN0IHdpZHRoPSI0MDAiIGhlaWdodD0iMjAwIiBmaWxsPSIjRjVGN0YiLz4KPHN2ZyB3aWR0aD0iNjAiIGhlaWdodD0iNjAiIHZpZXdCb3g9IjAgMCA2MCA2MCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4PSIxNzAiIHk9IjcwIj4KPGNpcmNsZSBjeD0iMzAiIGN5PSIzMCIgcj0iMjAiIGZpbGw9IiM5Q0EzQUYiLz4KPHN2Zz4KPHN2Zz4K\'; this.alt=\'' . $imageAlt . ' - Image not available\';">';
                            } else {
                                // Fallback to unique placeholder based on category
                                echo '<div class="card-image-placeholder" style="background: linear-gradient(135deg, ' . 
                                     ($category === 'nature' ? '#22c55e, #16a34a' : 
                                      ($category === 'farm' ? '#84cc16, #65a30d' : 
                                      ($category === 'park' ? '#06b6d4, #0891b2' : 
                                      ($category === 'religious' ? '#8b5cf6, #7c3aed' : 
                                      ($category === 'urban' ? '#f59e0b, #d97706' : 
                                      ($category === 'historical' ? '#6b7280, #4b5563' : '#4a7c4e, #2c5f2d')))))) . ');">';
                                echo '<svg width="60" height="60" viewBox="0 0 60 60" fill="none" xmlns="http://www.w3.org/2000/svg" style="opacity: 0.3;">';
                                echo '<circle cx="30" cy="30" r="20" fill="white"/>';
                                echo '<path d="M30 15L35 25L45 25L37 32L40 42L30 35L20 42L23 32L15 25L25 25L30 15Z" fill="rgba(255,255,255,0.5)"/>';
                                echo '</svg>';
                                echo '<div style="position: absolute; bottom: 10px; left: 10px; right: 10px; text-align: center;">';
                                echo '<span class="material-icons-outlined" style="color: white; font-size: 24px;">' . $icon . '</span>';
                                echo '</div>';
                                echo '</div>';
                            }
                            
                            echo '<div class="card-badge">';
                            echo '<span class="material-icons-outlined">' . $icon . '</span>';
                            echo $badge;
                            echo '</div>';
                            echo '</div>';
                            echo '<div class="card-content">';
                            echo '<div class="card-weather">';
                            echo '<span class="material-icons-outlined">' . ($weatherLabel === 'Clear' ? 'wb_sunny' : ($weatherLabel === 'Clouds' ? 'cloud' : 'wb_cloudy')) . '</span>';
                            echo '<span class="weather-temp">' . $currentTemp . '°C</span>';
                            echo '<span class="weather-desc">' . htmlspecialchars($weatherLabel) . '</span>';
                            echo '</div>';
                            echo '<h3 class="card-title">' . htmlspecialchars($spot['name']) . '</h3>';
                            echo '<span class="card-category">' . htmlspecialchars($displayCategory) . '</span>';
                            echo '<div class="card-stats">';
                            echo '<div class="stat-item">';
                            echo '<span class="stat-label">Rating</span>';
                            echo '<div style="display: flex; align-items: center; gap: 4px;">';
                            echo $starsHtml;
                            echo '<span style="font-size: 12px; color: #666;">(' . $spot['review_count'] . ')</span>';
                            echo '</div>';
                            echo '</div>';
                            echo '<div class="stat-item">';
                            echo '<span class="stat-label">Difficulty</span>';
                            echo '<span class="stat-value">' . ucfirst($spot['difficulty_level']) . '</span>';
                            echo '</div>';
                            echo '<div class="stat-item">';
                            echo '<span class="stat-label">Entrance</span>';
                            echo '<span class="stat-value">' . htmlspecialchars($spot['entrance_fee'] ?? 'Free') . '</span>';
                            echo '</div>';
                            echo '</div>';
                            echo '<div class="card-buttons">';
                            // Fixed the onclick function call
                            echo '<button class="card-button" onclick="showTouristSpotModal(\'' . 
                                 addslashes($spot['name']) . '\', \'' . 
                                 addslashes($displayCategory) . '\', \'' . 
                                 addslashes($spot['image_url']) . '\', \'' . 
                                 $icon . '\', \'' . 
                                 $badge . '\', \'' . 
                                 $currentTemp . '°C\', \'' . 
                                 ($spot['elevation'] ?? '200') . ' MASL\', \'' . 
                                 ucfirst($spot['difficulty_level']) . '\', \'' . 
                                 ($spot['duration'] ?? '2-3 hours') . '\', \'' . 
                                 htmlspecialchars(json_encode($guides), ENT_QUOTES, 'UTF-8') . '\')">';
                            echo 'View Details';
                            echo '</button>';
                            echo '</div>';
                            echo '</div>';
                            echo '</div>';
                        }
                    } else {
                        echo '<div class="no-results" style="grid-column: 1 / -1; text-align: center; padding: 60px 20px;">';
                        echo '<span class="material-icons-outlined" style="font-size: 48px; color: #9ca3af;">place</span>';
                        echo '<h3 style="color: #6b7280; margin-top: 16px;">No tourist spots found</h3>';
                        echo '<p style="color: #9ca3af;">Please check back later for available destinations.</p>';
                        echo '</div>';
                    }
                    closeDatabaseConnection($conn);
                } else {
                    echo '<div class="error-message" style="grid-column: 1 / -1; text-align: center; padding: 60px 20px;">';
                    echo '<span class="material-icons-outlined" style="font-size: 48px; color: #ef4444;">error</span>';
                    echo '<h3 style="color: #ef4444; margin-top: 16px;">Database Connection Error</h3>';
                    echo '<p style="color: #6b7280;">Unable to load tourist spots. Please try again later.</p>';
                    echo '</div>';
                }
                ?>
            </div>
        </div>
    </main>

    <!-- Tourist Spot Detail Modal -->
    <div id="touristSpotModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <div class="modal-header-content">
                    <div class="modal-title-section">
                        <div class="modal-category" id="modalSpotCategory">Category</div>
                        <h2 id="modalSpotName">Tourist Spot Name</h2>
                    </div>
                    <button class="modal-close" onclick="closeTouristSpotModal()">
                        <span class="material-icons-outlined">close</span>
                    </button>
                </div>
            </div>
            
            <div class="modal-body">
                <div class="modal-hero-section">
                    <div class="modal-image-container">
                        <img id="modalSpotImage" src="" alt="">
                        <div class="modal-image-overlay">
                            <div class="modal-badge" id="modalSpotBadge">
                                <span class="material-icons-outlined">place</span>
                                <span id="modalSpotType">Type</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="modal-content-section">
                    <div class="modal-info-header">
                        <h3 id="modalSpotTitle">Spot Title</h3>
                        <p id="modalSpotDescription">Description of the tourist spot...</p>
                    </div>
                    
                    <div class="modal-stats-grid">
                        <div class="modal-stat-card">
                            <div class="stat-icon">
                                <span class="material-icons-outlined">thermostat</span>
                            </div>
                            <div class="stat-info">
                                <div class="stat-label">Temperature</div>
                                <div class="stat-value" id="modalSpotTemp">+24°C</div>
                            </div>
                        </div>
                        
                        <div class="modal-stat-card">
                            <div class="stat-icon">
                                <span class="material-icons-outlined">terrain</span>
                            </div>
                            <div class="stat-info">
                                <div class="stat-label">Elevation</div>
                                <div class="stat-value" id="modalSpotElevation">200 MASL</div>
                            </div>
                        </div>
                        
                        <div class="modal-stat-card">
                            <div class="stat-icon">
                                <span class="material-icons-outlined">signal_cellular_alt</span>
                            </div>
                            <div class="stat-info">
                                <div class="stat-label">Difficulty</div>
                                <div class="stat-value" id="modalSpotDifficulty">Moderate</div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-features-section">
                        <div class="section-header">
                            <h4>What to Expect</h4>
                            <div class="section-icon">
                                <span class="material-icons-outlined">stars</span>
                            </div>
                        </div>
                        <div class="features-grid" id="modalSpotFeatures">
                            <div class="feature-item">
                                <span class="feature-icon">🌟</span>
                                <span>Beautiful natural scenery</span>
                            </div>
                            <div class="feature-item">
                                <span class="feature-icon">📸</span>
                                <span>Perfect for photography</span>
                            </div>
                            <div class="feature-item">
                                <span class="feature-icon">👨‍👩‍👧‍👦</span>
                                <span>Family-friendly environment</span>
                            </div>
                            <div class="feature-item">
                                <span class="feature-icon">🍽️</span>
                                <span>Local food options nearby</span>
                            </div>
                        </div>
                    </div>

                    <div class="modal-guides-section" id="modalSpotGuides" style="display: none;">
                        <!-- Guides will be dynamically inserted here -->
                    </div>

                    <div class="modal-actions-section">
                        <div class="action-buttons">
                            <button class="btn-primary modal-book-btn" onclick="viewAllDetails()">
                                <span class="material-icons-outlined">visibility</span>
                                <span>View All</span>
                            </button>
                            <button class="btn-secondary modal-save-btn" onclick="saveThisSpot()">
                                <span class="material-icons-outlined">favorite_border</span>
                                <span>Save to Favorites</span>
                            </button>
                        </div>
                    </div>
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
        // Tourist Spot Modal Functions
        function showTouristSpotModal(name, category, image, icon, type, temp, elevation, difficulty, duration, guides) {
            console.log('Modal function called with:', { name, category, type });
            
            const modal = document.getElementById('touristSpotModal');
            
            if (!modal) {
                console.error('Modal element not found!');
                alert('Modal element not found. Please refresh the page.');
                return;
            }
            
            // Update basic modal content
            document.getElementById('modalSpotName').textContent = name;
            document.getElementById('modalSpotCategory').textContent = category;
            document.getElementById('modalSpotTitle').textContent = name;
            document.getElementById('modalSpotType').textContent = type;
            document.getElementById('modalSpotTemp').textContent = temp;
            document.getElementById('modalSpotElevation').textContent = elevation;
            document.getElementById('modalSpotDifficulty').textContent = difficulty;
            
            // Update image
            const modalImage = document.getElementById('modalSpotImage');
            if (modalImage) {
                modalImage.src = image;
                modalImage.alt = name;
            }
            
            // Update badge icon
            const badgeIcon = document.querySelector('#modalSpotBadge .material-icons-outlined');
            if (badgeIcon) {
                badgeIcon.textContent = icon;
            }
            
            // Parse guides
            let guidesData = [];
            try {
                // Clean up the guides string if needed
                let guidesStr = guides;
                if (typeof guides === 'string') {
                    // Remove any extra quotes or slashes
                    guidesStr = guides.replace(/^'|'$/g, '');
                    guidesStr = guidesStr.replace(/\\'/g, "'");
                    console.log('Cleaned guides string:', guidesStr);
                    
                    guidesData = JSON.parse(guidesStr);
                } else if (Array.isArray(guides)) {
                    guidesData = guides;
                }
                console.log('Parsed guides data:', guidesData);
            } catch (e) {
                console.error('Error parsing guides data:', e);
                console.log('Raw guides value that failed:', guides);
                guidesData = [];
            }
            
            // Display guides in modal
            const guidesContainer = document.getElementById('modalSpotGuides');
            if (guidesContainer) {
                if (guidesData && guidesData.length > 0) {
                    guidesContainer.innerHTML = `
                        <h4><span class="material-icons-outlined">people</span> Available Tour Guides</h4>
                        <div class="modal-guides-list">
                            ${guidesData.map(guide => `
                                <div class="modal-guide-item" onclick="viewGuideProfile(${guide.id})">
                                    <div class="modal-guide-info">
                                        <div class="modal-guide-name">${guide.name || 'Guide Name'}</div>
                                        <div class="modal-guide-specialty">${guide.specialty || 'Tour Guide'}</div>
                                        <div class="modal-guide-rating">
                                            ${generateStars(guide.rating || 0)}
                                            <span class="rating-number">${parseFloat(guide.rating || 0).toFixed(1)}</span>
                                            ${guide.verified ? '<span class="material-icons-outlined verified-badge">verified</span>' : ''}
                                        </div>
                                    </div>
                                </div>
                            `).join('')}
                        </div>
                    `;
                    guidesContainer.style.display = 'block';
                } else {
                    guidesContainer.style.display = 'none';
                }
            }
            
            // Generate dynamic description based on spot type
            let description = '';
            let features = [];
            
            const spotType = type.toLowerCase();
            
            if (spotType.includes('mountain')) {
                description = `Experience the breathtaking beauty of ${name}, one of San Jose del Monte's most majestic mountain peaks. This stunning destination offers panoramic views, challenging trails, and an unforgettable adventure for nature enthusiasts and hikers alike.`;
                features = [
                    'Challenging hiking trails with varying difficulty levels',
                    'Spectacular panoramic views of Bulacan province',
                    'Rich biodiversity and unique flora and fauna',
                    'Perfect for sunrise and sunset photography',
                    'Camping spots available for overnight stays'
                ];
            } else if (spotType.includes('waterfall')) {
                description = `Discover the natural wonder of ${name}, a hidden gem nestled in the lush landscapes of San Jose del Monte. This pristine waterfall offers a refreshing escape with its crystal-clear waters and serene surroundings.`;
                features = [
                    'Crystal-clear waters perfect for swimming',
                    'Natural pools for relaxation',
                    'Lush tropical surroundings',
                    'Ideal for nature photography',
                    'Accessible hiking trails with scenic views'
                ];
            } else if (spotType.includes('farm')) {
                description = `Experience sustainable agriculture and rural life at ${name}, a charming eco-tourism destination in San Jose del Monte. This working farm offers hands-on experiences and educational opportunities for visitors of all ages.`;
                features = [
                    'Organic farming practices and sustainable agriculture',
                    'Fresh produce sampling and farm-to-table experiences',
                    'Educational tours about farming techniques',
                    'Interactive activities for children and families',
                    'Scenic rural landscapes and peaceful environment'
                ];
            } else if (spotType.includes('park')) {
                description = `Enjoy recreational activities and natural beauty at ${name}, a well-maintained public space in San Jose del Monte. This park offers facilities for sports, relaxation, and family gatherings in a clean, safe environment.`;
                features = [
                    'Well-maintained sports facilities and equipment',
                    'Children\'s playground and family-friendly areas',
                    'Jogging paths and walking trails',
                    'Picnic areas with benches and tables',
                    'Regular community events and activities'
                ];
            } else if (spotType.includes('religious')) {
                description = `Find spiritual solace and architectural beauty at ${name}, a sacred destination in San Jose del Monte. This religious site offers a peaceful atmosphere for prayer, reflection, and cultural appreciation.`;
                features = [
                    'Beautiful religious architecture and artwork',
                    'Peaceful atmosphere for prayer and meditation',
                    'Cultural and historical significance',
                    'Well-maintained grounds and gardens',
                    'Regular religious services and community events'
                ];
            } else {
                description = `Explore the beauty and attractions of ${name}, a popular destination in San Jose del Monte. This spot offers unique experiences and memorable moments for all types of travelers.`;
                features = [
                    'Unique local attractions and activities',
                    'Beautiful scenery and photo opportunities',
                    'Accessible location with various amenities',
                    'Cultural and historical significance',
                    'Friendly local community'
                ];
            }
            
            // Update description
            const descElement = document.getElementById('modalSpotDescription');
            if (descElement) {
                descElement.textContent = description;
            }
            
            // Update features list
            const featuresList = document.getElementById('modalSpotFeatures');
            if (featuresList) {
                const featureIcons = ['🌟', '📸', '👨‍👩‍👧‍👦', '🍽️', '🏞️', '🥾', '⛰️', '🌿', '🏛️', '⚽'];
                featuresList.innerHTML = features.map((feature, index) => `
                    <div class="feature-item">
                        <span class="feature-icon">${featureIcons[index % featureIcons.length]}</span>
                        <span>${feature}</span>
                    </div>
                `).join('');
            }
            
            // Show modal
            modal.style.display = 'block';
            document.body.style.overflow = 'hidden';
            
            // Add animation class for smooth appearance
            setTimeout(() => {
                modal.classList.add('show');
            }, 10);
        }
        
        // Helper function to generate star rating HTML
        function generateStars(rating) {
            const fullStars = Math.floor(rating);
            const hasHalfStar = (rating - fullStars) >= 0.5;
            let starsHtml = '';
            
            for (let i = 0; i < fullStars; i++) {
                starsHtml += '<span class="material-icons-outlined" style="color: #ffc107; font-size: 14px;">star</span>';
            }
            if (hasHalfStar) {
                starsHtml += '<span class="material-icons-outlined" style="color: #ffc107; font-size: 14px;">star_half</span>';
            }
            const emptyStars = 5 - fullStars - (hasHalfStar ? 1 : 0);
            for (let i = 0; i < emptyStars; i++) {
                starsHtml += '<span class="material-icons-outlined" style="color: #ddd; font-size: 14px;">star_outline</span>';
            }
            return starsHtml;
        }
        
        function viewGuideProfile(guideId) {
            console.log('Viewing guide profile:', guideId);
            closeTouristSpotModal();
            // Redirect to user-guides.php with guide ID parameter
            window.location.href = 'user-guides.php?guide=' + guideId;
        }
        
        function closeTouristSpotModal() {
            const modal = document.getElementById('touristSpotModal');
            if (modal) {
                modal.classList.remove('show');
                setTimeout(() => {
                    modal.style.display = 'none';
                    document.body.style.overflow = 'auto';
                }, 300);
            }
        }
        
        function viewAllDetails() {
            // Get current spot name from modal
            const spotName = document.getElementById('modalSpotName').textContent;
            console.log('Spot name from modal:', spotName);
            closeTouristSpotModal();
            
            // Create mapping for spot names to detail pages
            const spotPages = {
                'Mt. Balagbag': '../tourist-detail/mt-balagbag.php',
                'Mt. Balagbag Mountain': '../tourist-detail/mt-balagbag.php',
                'Mount Balagbag': '../tourist-detail/mt-balagbag.php',
                'Abes Farm': '../tourist-detail/abes-farm.php',
                'Abes Farm Resort': '../tourist-detail/abes-farm.php',
                'Burong Falls': '../tourist-detail/burong-falls.php',
                'Burong Falls San Jose del Monte': '../tourist-detail/burong-falls.php',
                'City Oval & People\'s Park': '../tourist-detail/city-ovals-peoples-park.php',
                'City Oval and People\'s Park': '../tourist-detail/city-ovals-peoples-park.php',
                'City Oval Peoples Park': '../tourist-detail/city-ovals-peoples-park.php',
                'Kaytitinga Falls': '../tourist-detail/kaytitinga-falls.php',
                'Kaytitinga Falls San Jose del Monte': '../tourist-detail/kaytitinga-falls.php',
                'Otso Otso Falls': '../tourist-detail/otso-otso-falls.php',
                'Otso-Otso Falls': '../tourist-detail/otso-otso-falls.php',
                'Our Lady of Lourdes': '../tourist-detail/our-lady-of-lourdes.php',
                'Our Lady of Lourdes Parish': '../tourist-detail/our-lady-of-lourdes.php',
                'Lourdes Parish': '../tourist-detail/our-lady-of-lourdes.php',
                'Padre Pio': '../tourist-detail/padre-pio.php',
                'Padre Pio Shrine': '../tourist-detail/padre-pio.php',
                'Paradise Hill Farm': '../tourist-detail/paradise-hill-farm.php',
                'Paradise Hill Farm Resort': '../tourist-detail/paradise-hill-farm.php',
                'The Rising Heart': '../tourist-detail/the-rising-heart.php',
                'The Rising Heart Farm': '../tourist-detail/the-rising-heart.php',
                'Tungtong Falls': '../tourist-detail/tungtong.php',
                'Tungtong Falls San Jose del Monte': '../tourist-detail/tungtong.php'
            };
            
            console.log('Available spot pages:', Object.keys(spotPages));
            console.log('Looking for spot name:', spotName);
            const detailPage = spotPages[spotName] || '../tourist-detail/city-ovals-peoples-park.php';
            console.log('Final detail page:', detailPage);
            
            // Redirect to the detail page
            window.location.href = detailPage;
        }
        
        function saveThisSpot() {
            const spotName = document.getElementById('modalSpotName').textContent;
            const saveBtn = document.querySelector('.modal-save-btn');
            
            // Toggle saved state
            if (saveBtn.classList.contains('saved')) {
                saveBtn.classList.remove('saved');
                saveBtn.innerHTML = '<span class="material-icons-outlined">favorite_border</span> Save to Favorites';
                showNotification('Removed from favorites', 'info');
            } else {
                saveBtn.classList.add('saved');
                saveBtn.innerHTML = '<span class="material-icons-outlined">favorite</span> Saved to Favorites';
                showNotification('Added to favorites!', 'success');
            }
        }
        
        function showNotification(message, type = 'info') {
            // Remove any existing notifications
            const existingNotification = document.querySelector('.notification-banner');
            if (existingNotification) {
                existingNotification.remove();
            }

            // Create notification banner
            const notification = document.createElement('div');
            notification.className = `notification-banner ${type}`;
            
            // Icon mapping for different types
            const icons = {
                success: 'check_circle',
                error: 'error',
                warning: 'warning',
                info: 'info'
            };
            
            notification.innerHTML = `
                <span class="material-icons-outlined notification-icon">${icons[type] || 'info'}</span>
                <span class="notification-message">${message}</span>
                <button class="notification-close" onclick="this.parentElement.remove()">
                    <span class="material-icons-outlined">close</span>
                </button>
            `;
            
            // Add to page
            document.body.appendChild(notification);
            
            // Show notification
            setTimeout(() => {
                notification.classList.add('show');
            }, 100);
            
            // Hide and remove after 3 seconds
            setTimeout(() => {
                notification.classList.remove('show');
                setTimeout(() => {
                    if (notification.parentElement) {
                        document.body.removeChild(notification);
                    }
                }, 400);
            }, 3000);
        }
        
        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('touristSpotModal');
            if (event.target === modal) {
                closeTouristSpotModal();
            }
        }
        
        // Close modal with Escape key
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                closeTouristSpotModal();
            }
        });

        // Filter functionality
        function filterSpots() {
            const category = document.getElementById('categoryFilter').value;
            const activity = document.getElementById('activityFilter').value;
            const duration = document.getElementById('durationFilter').value;

            const cards = document.querySelectorAll('.travelry-card');

            cards.forEach(card => {
                const cardCategory = card.getAttribute('data-category');
                const cardActivity = card.getAttribute('data-activity');
                const cardDuration = card.getAttribute('data-duration');

                let show = true;

                // Filter by category
                if (category !== 'all' && category !== cardCategory) {
                    show = false;
                }

                // Filter by activity level
                if (activity !== 'all' && activity !== cardActivity) {
                    show = false;
                }

                // Filter by duration
                if (duration !== 'all') {
                    if (duration === '1-2' && !cardDuration.includes('1-2')) {
                        show = false;
                    } else if (duration === '2-4') {
                        if (!cardDuration.includes('2-3') && !cardDuration.includes('3-4') && !cardDuration.includes('2-4') && !cardDuration.includes('3-5')) {
                            show = false;
                        }
                    } else if (duration === '4+') {
                        if (!cardDuration.includes('4-5') && !cardDuration.includes('5-7') && !cardDuration.includes('4-6')) {
                            show = false;
                        }
                    }
                }

                card.style.display = show ? 'block' : 'none';
            });

            // Check if any cards are visible
            const visibleCards = Array.from(cards).filter(card => card.style.display !== 'none');
            const noResults = document.querySelector('.no-results-spots');

            if (visibleCards.length === 0) {
                if (!noResults) {
                    const spotsGrid = document.getElementById('spotsGrid');
                    const message = document.createElement('div');
                    message.className = 'no-results-spots';
                    message.innerHTML = `
                        <div class="empty-state">
                            <span class="material-icons-outlined">search_off</span>
                            <h3>No destinations found</h3>
                            <p>Try adjusting your filters to find the perfect tour</p>
                            <button class="btn-hero" onclick="resetSpotFilters()">Reset Filters</button>
                        </div>
                    `;
                    spotsGrid.appendChild(message);
                }
            } else if (noResults) {
                noResults.remove();
            }
        }

        function resetSpotFilters() {
            document.getElementById('categoryFilter').value = 'all';
            document.getElementById('activityFilter').value = 'all';
            document.getElementById('durationFilter').value = 'all';
            filterSpots();
        }

        // Initialize filters
        document.addEventListener('DOMContentLoaded', function() {
            const filters = ['categoryFilter', 'activityFilter', 'durationFilter'];
            filters.forEach(filterId => {
                const filter = document.getElementById(filterId);
                if (filter) {
                    filter.addEventListener('change', filterSpots);
                }
            });

            // Initialize search
            const searchInput = document.querySelector('.search-bar input');
            if (searchInput) {
                searchInput.addEventListener('input', function(e) {
                    const searchTerm = e.target.value.toLowerCase();
                    searchSpots(searchTerm);
                });
            }

            console.log('Tourist spots page initialized');
            console.log('Modal functions available:', {
                showTouristSpotModal: typeof showTouristSpotModal,
                closeTouristSpotModal: typeof closeTouristSpotModal
            });
        });

        function searchSpots(searchTerm) {
            const cards = document.querySelectorAll('.travelry-card');
            
            cards.forEach(card => {
                const cardTitle = card.querySelector('.card-title')?.textContent.toLowerCase() || '';
                const cardCategory = card.querySelector('.card-category')?.textContent.toLowerCase() || '';
                
                if (cardTitle.includes(searchTerm) || 
                    cardCategory.includes(searchTerm) || 
                    searchTerm === '') {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        }
    </script>
</body>
</html>