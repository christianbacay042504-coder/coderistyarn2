<?php
// Include database connection and authentication
require_once '../config/database.php';
require_once '../config/auth.php';

// OpenWeatherMap API configuration
$apiKey = '6c21a0d2aaf514cb8d21d56814312b19';
$weatherUrl = "https://api.openweathermap.org/data/2.5/weather?q=San%20Jose%20Del%20Monte,Bulacan&appid={$apiKey}&units=metric";
$weatherData = null;
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
        }
    }
    curl_close($ch);
} catch (Exception $e) {
    $weatherError = 'Weather service unavailable';
}

// Get current date and weekday
$currentWeekday = date('l');
$currentDate = date('F Y');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../log-in/log-in.php');
    exit();
}

// Get current user data
$conn = getDatabaseConnection();
$currentUser = ['name' => '', 'email' => ''];
$userPreferences = [];

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
        
        // Get user preferences
        $tableExists = false;
        $checkTable = $conn->prepare("SHOW TABLES LIKE 'user_preferences'");
        $checkTable->execute();
        $tableExists = $checkTable->get_result()->num_rows > 0;
        $checkTable->close();
        
        if ($tableExists) {
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
    $stmt->close();
    closeDatabaseConnection($conn);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Travel Tips - San Jose del Monte Bulacan</title>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,500;0,700;1,400;1,500&family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;1,9..40,300&family=Cormorant+Garamond:ital,wght@0,300;0,400;0,600;1,300;1,400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="user-styles.css">
    <style>
        :root {
            /* Page palette - matching tourist spots */
            --pg-bg:     #f4f1eb;
            --pg-ink:    #1a1a18;
            --pg-forest: #1e3a1f;
            --pg-sage:   #4a7c4e;
            --pg-mint:   #b5d4b8;
            --pg-cream:  #faf8f3;
            --pg-sand:   #e8e2d6;
            --pg-warm:   #c8b89a;
            --pg-gold:   #c9a85c;
            --pg-mist:   #f0ede6;
            
            /* Modal palette */
            --m-forest:  #1e3a1f;
            --m-sage:    #4a7c4e;
            --m-mint:    #b5d4b8;
            --m-cream:   #f7f4ef;
            --m-sand:    #ede8df;
            --m-warm:    #c8b89a;
            --m-ink:     #1a1814;
            --m-mist:    #f0ede8;
            --m-gold:    #c9a85c;
        }
        
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body {
            font-family: 'DM Sans', system-ui, sans-serif;
            background: var(--pg-bg);
            color: var(--pg-ink);
            line-height: 1.6;
            min-height: 100vh;
        }
        
        /* ══════════════════════════
        HEADER - Matching Tourist Spots
        ══════════════════════════ */
        .main-content.full-width { margin-left: 0; max-width: 100%; display: block; }
        .main-content.full-width .main-header {
            padding: 0 48px;
            background: var(--pg-forest);
            border-bottom: none;
            box-shadow: none;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 32px;
            height: 68px;
            position: sticky;
            top: 0;
            z-index: 900;
        }
        
        .main-content.full-width .main-header h1 { display: none; }
        
        .header-left { display: flex; align-items: center; gap: 0; }
        .header-right { display: flex; align-items: center; gap: 4px; }
        
        /* Logo */
        .header-left .logo {
            display: flex !important;
            align-items: center;
            gap: 10px;
            margin-right: 40px !important;
            text-decoration: none;
        }
        .header-left .logo img {
            height: 32px !important;
            width: 32px !important;
            border-radius: 6px !important;
            filter: brightness(1.1);
        }
        .header-left .logo span {
            font-family: 'Playfair Display', serif !important;
            font-size: 17px !important;
            font-weight: 700 !important;
            color: #fff !important;
            letter-spacing: 0.04em;
        }
        
        /* Nav links */
        .header-nav {
            display: flex;
            align-items: center;
            gap: 0;
            background: none !important;
            padding: 0 !important;
            border-radius: 0 !important;
        }
        .nav-link {
            display: flex;
            align-items: center;
            gap: 5px;
            padding: 8px 14px;
            text-decoration: none;
            color: rgba(255,255,255,0.62) !important;
            font-weight: 400;
            font-size: 13px;
            border-radius: 0 !important;
            transition: color 0.18s;
            border-bottom: 2px solid transparent;
            height: 68px;
            letter-spacing: 0.01em;
        }
        .nav-link:hover {
            background: none !important;
            color: rgba(255,255,255,0.9) !important;
            box-shadow: none !important;
        }
        .nav-link.active {
            background: none !important;
            color: #fff !important;
            border-bottom-color: var(--pg-gold);
        }
        .nav-link .material-icons-outlined {
            font-size: 16px;
            opacity: 0.8;
        }
        
        /* Sign in button */
        .btn-signin {
            background: rgba(255,255,255,0.12);
            color: #fff;
            border: 1px solid rgba(255,255,255,0.2);
            padding: 9px 20px;
            border-radius: 100px;
            font-weight: 500;
            font-size: 13px;
            cursor: pointer;
            transition: all 0.2s;
            letter-spacing: 0.02em;
        }
        .btn-signin:hover {
            background: rgba(255,255,255,0.22);
            transform: none;
            box-shadow: none;
        }
        
        /* ── Profile Dropdown ── */
        .user-profile-dropdown { position: relative; display: inline-block; z-index: 1000; }
        .profile-trigger {
            display: flex; align-items: center; gap: 8px;
            background: rgba(255,255,255,0.1);
            border: 1px solid rgba(255,255,255,0.18);
            cursor: pointer; color: #fff;
            font-weight: 500; font-size: 13px;
            padding: 7px 14px 7px 8px;
            border-radius: 100px;
            transition: background 0.2s;
            box-shadow: none;
        }
        .profile-trigger:hover { background: rgba(255,255,255,0.18); }
        .profile-avatar {
            width: 28px; height: 28px; border-radius: 50%;
            background: var(--pg-gold); color: #fff;
            display: flex; align-items: center; justify-content: center;
            font-weight: 700; font-size: 12px; flex-shrink: 0;
        }
        .profile-avatar-large {
            width: 56px; height: 56px; font-size: 20px; margin: 0 auto 12px;
            border-radius: 50%; background: var(--pg-sage); color: white;
            display: flex; align-items: center; justify-content: center; font-weight: bold;
        }
        .profile-name { display: none; }
        .dropdown-menu {
            position: absolute; top: calc(100% + 10px); right: 0;
            width: 240px; background: white;
            border-radius: 16px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.15), 0 2px 8px rgba(0,0,0,0.08);
            overflow: hidden; z-index: 1001;
            opacity: 0; visibility: hidden;
            transform: translateY(-8px);
            transition: all 0.2s cubic-bezier(0.22,1,0.36,1);
        }
        .dropdown-menu.show { opacity: 1 !important; visibility: visible !important; transform: translateY(0) !important; }
        .dropdown-header { padding: 20px 16px 14px; background: var(--pg-mist); text-align: center; border-bottom: 1px solid var(--pg-sand); }
        .dropdown-header h4 { margin: 8px 0 4px; font-size: 15px; color: var(--pg-ink); font-family: 'Playfair Display', serif; }
        .dropdown-header p { font-size: 12px; color: #999; margin: 0; }
        .dropdown-item { display: flex; align-items: center; gap: 12px; padding: 11px 16px; text-decoration: none; color: #444; transition: background 0.15s; font-size: 13.5px; }
        .dropdown-item:hover { background: var(--pg-mist); }
        .dropdown-item .material-icons-outlined { font-size: 18px; color: var(--pg-sage); }
        .dropdown-divider { height: 1px; background: var(--pg-sand); margin: 4px 0; }
        
        .main-content.full-width .content-area {
            padding: 0;
            max-width: 100%;
            margin: 0 auto;
            display: block;
        }
        
        /* ══════════════════════════
        HERO SECTION
        ══════════════════════════ */
        .hero-section {
            position: relative;
            height: 380px;
            display: flex;
            flex-direction: column;
            justify-content: flex-end;
            align-items: flex-start;
            padding: 0 72px 48px;
            overflow: hidden;
            margin-bottom: 0;
            border-radius: 0;
            background:
                linear-gradient(to bottom, rgba(15,25,16,0.25) 0%, rgba(15,25,16,0.1) 40%, rgba(15,25,16,0.75) 100%),
                url('https://images.unsplash.com/photo-1501785888041-af3ef285b470?q=80&w=2070&auto=format&fit=crop') center/cover no-repeat;
            background-attachment: fixed;
            text-align: left;
            color: white;
        }
        .hero-section::before {
            content: '';
            position: absolute; inset: 0;
            background: linear-gradient(135deg, rgba(30,58,31,0.3) 0%, transparent 60%);
            pointer-events: none;
            z-index: 0;
        }
        .hero-section::after { display: none; }
        .hero-section h1 {
            font-family: 'Playfair Display', serif;
            font-size: 3.5rem;
            font-weight: 500;
            letter-spacing: -0.02em;
            line-height: 1.05;
            color: #fff;
            margin-bottom: 12px;
            background: none;
            -webkit-text-fill-color: #fff;
            text-shadow: 0 2px 24px rgba(0,0,0,0.22);
            animation: heroFadeUp 0.9s cubic-bezier(0.22,1,0.36,1) both;
            position: relative; z-index: 2;
            max-width: 640px;
        }
        .hero-section p {
            font-family: 'DM Sans', sans-serif;
            font-size: 1.05rem;
            font-weight: 300;
            color: rgba(255,255,255,0.8);
            max-width: 480px;
            margin: 0;
            line-height: 1.65;
            text-shadow: 0 1px 8px rgba(0,0,0,0.2);
            animation: heroFadeUp 0.9s 0.12s cubic-bezier(0.22,1,0.36,1) both;
            position: relative; z-index: 2;
        }
        @keyframes heroFadeUp {
            from { opacity:0; transform:translateY(24px); }
            to   { opacity:1; transform:translateY(0); }
        }
        
        /* Hero stat pills */
        .hero-pills {
            display: flex; align-items: center; gap: 10px;
            position: relative; z-index: 2;
            animation: heroFadeUp 0.9s 0.22s cubic-bezier(0.22,1,0.36,1) both;
            margin-top: 24px;
        }
        .hero-pill {
            display: flex; align-items: center; gap: 6px;
            background: rgba(255,255,255,0.12);
            backdrop-filter: blur(10px); -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.2);
            color: rgba(255,255,255,0.9);
            padding: 8px 16px;
            border-radius: 100px;
            font-family: 'DM Sans', sans-serif;
            font-size: 12.5px; font-weight: 500;
            letter-spacing: 0.02em;
        }
        .hero-pill .material-icons-outlined { font-size: 14px; opacity: 0.8; }
        
        /* ══════════════════════════
        CONTENT WRAPPER
        ══════════════════════════ */
        .page-inner {
            max-width: 1200px;
            margin: 0 auto;
            padding: 48px 48px 80px;
        }
        
        /* ══════════════════════════
        INFO CARDS (Weather & Date)
        ══════════════════════════ */
        .info-cards-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }
        
        .info-card {
            background: var(--pg-cream);
            border-radius: 16px;
            padding: 24px;
            border: 1px solid var(--pg-sand);
            display: flex;
            align-items: center;
            gap: 16px;
            transition: all 0.2s;
        }
        .info-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(0,0,0,0.06);
        }
        
        .info-card-icon {
            width: 48px;
            height: 48px;
            background: var(--pg-mist);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .info-card-icon .material-icons-outlined {
            font-size: 24px;
            color: var(--pg-sage);
        }
        
        .info-card-content {
            flex: 1;
        }
        
        .info-label {
            font-size: 11px;
            font-weight: 500;
            color: #999;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            margin-bottom: 4px;
        }
        
        .info-value {
            font-family: 'Playfair Display', serif;
            font-size: 1.3rem;
            font-weight: 500;
            color: var(--pg-forest);
            line-height: 1.2;
        }
        
        .info-secondary {
            font-size: 12px;
            color: #666;
            margin-top: 2px;
        }
        
        /* ══════════════════════════
        USER PREFERENCES
        ══════════════════════════ */
        .user-preferences-section {
            background: linear-gradient(135deg, rgba(30,58,31,0.06), rgba(74,124,78,0.04)) !important;
            border: 1px solid rgba(30,58,31,0.14) !important;
            border-radius: 16px !important;
            padding: 20px 24px !important;
            margin-bottom: 32px !important;
        }
        .section-title {
            font-family: 'Playfair Display', serif;
            font-size: 1.6rem;
            font-weight: 500;
            color: var(--pg-forest);
            margin-bottom: 12px;
            letter-spacing: -0.01em;
        }
        .preferences-display {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }
        .preference-tag {
            background: rgba(44,95,45,0.1);
            color: #2c5f2d;
            padding: 8px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .preference-tag .material-icons-outlined {
            font-size: 14px;
        }
        
        /* ══════════════════════════
        SEARCH & FILTER
        ══════════════════════════ */
        .travel-tips-filters {
            margin-bottom: 40px;
        }
        .filter-row {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }
        .filter-group {
            display: flex;
            flex-direction: column;
            gap: 0;
        }
        .filter-group label { display: none; }
        .filter-select {
            appearance: none;
            -webkit-appearance: none;
            background: var(--pg-cream) url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%234a7c4e' stroke-width='2'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E") no-repeat right 12px center;
            border: 1.5px solid var(--pg-sand);
            border-radius: 100px;
            padding: 10px 36px 10px 18px;
            font-family: 'DM Sans', sans-serif;
            font-size: 13px;
            font-weight: 500;
            color: var(--pg-ink);
            cursor: pointer;
            transition: border-color 0.2s, box-shadow 0.2s;
            min-width: 160px;
            outline: none;
        }
        .filter-select:hover { border-color: var(--pg-sage); }
        .filter-select:focus { border-color: var(--pg-sage); box-shadow: 0 0 0 3px rgba(74,124,78,0.12); }
        
        .search-bar-container {
            position: relative;
            flex: 1;
            max-width: 400px;
        }
        .search-bar-container input {
            width: 100%;
            padding: 10px 18px 10px 42px;
            border: 1.5px solid var(--pg-sand);
            border-radius: 100px;
            font-family: 'DM Sans', sans-serif;
            font-size: 13px;
            color: var(--pg-ink);
            background: var(--pg-cream);
            transition: all 0.2s;
            outline: none;
        }
        .search-bar-container input:focus {
            border-color: var(--pg-sage);
            box-shadow: 0 0 0 3px rgba(74,124,78,0.12);
        }
        .search-bar-container .material-icons-outlined {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--pg-sage);
            font-size: 18px;
        }
        
        /* Section title */
        .tips-section-title {
            font-family: 'Playfair Display', serif;
            font-size: 1.6rem;
            font-weight: 500;
            color: var(--pg-forest);
            margin-bottom: 28px;
            letter-spacing: -0.01em;
        }
        .tips-section-title span {
            font-family: 'DM Sans', sans-serif;
            font-size: 0.78rem;
            font-weight: 400;
            color: #999;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            display: block;
            margin-bottom: 6px;
        }
        
        /* ══════════════════════════
        TIPS CARDS GRID
        ══════════════════════════ */
        .tips-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(340px, 1fr));
            gap: 28px;
            margin-bottom: 48px;
        }
        
        /* Tip Card */
        .tip-card {
            background: var(--pg-cream);
            border-radius: 20px;
            overflow: hidden;
            border: 1px solid var(--pg-sand);
            transition: transform 0.28s cubic-bezier(0.22,1,0.36,1), box-shadow 0.28s;
            cursor: pointer;
        }
        .tip-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 24px 60px rgba(30,58,31,0.13), 0 4px 16px rgba(0,0,0,0.06);
        }
        
        .tip-card-header {
            padding: 24px 22px 16px;
            background: linear-gradient(135deg, var(--pg-cream) 0%, var(--pg-mist) 100%);
            border-bottom: 1px solid var(--pg-sand);
        }
        
        .tip-icon {
            width: 48px;
            height: 48px;
            background: var(--pg-sage);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 16px;
        }
        .tip-icon .material-icons-outlined {
            font-size: 24px;
            color: #fff;
        }
        
        .tip-title {
            font-family: 'Playfair Display', serif;
            font-size: 1.3rem;
            font-weight: 500;
            color: var(--pg-forest);
            line-height: 1.25;
            margin-bottom: 8px;
            letter-spacing: -0.01em;
        }
        
        .tip-category {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            background: var(--pg-mist);
            color: var(--pg-sage);
            padding: 4px 10px;
            border-radius: 100px;
            font-size: 10px;
            font-weight: 500;
            letter-spacing: 0.07em;
            text-transform: uppercase;
        }
        
        .tip-card-body {
            padding: 20px 22px 22px;
        }
        
        .tip-description {
            font-size: 13px;
            line-height: 1.7;
            color: #666;
            margin-bottom: 16px;
        }
        
        .tip-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        
        .tip-list li {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            padding: 10px 0;
            border-bottom: 1px solid var(--pg-sand);
            font-size: 13px;
            color: var(--pg-ink);
        }
        .tip-list li:last-child {
            border-bottom: none;
        }
        .tip-list li .material-icons-outlined {
            font-size: 16px;
            color: var(--pg-sage);
            flex-shrink: 0;
            margin-top: 2px;
        }
        
        /* ══════════════════════════
        ACCOMMODATION & RESTAURANT CARDS
        ══════════════════════════ */
        .accommodation-section {
            margin-bottom: 48px;
        }
        
        .spot-cards {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(340px, 1fr));
            gap: 28px;
        }
        
        .spot-card {
            background: var(--pg-cream);
            border-radius: 20px;
            overflow: hidden;
            border: 1px solid var(--pg-sand);
            transition: transform 0.28s cubic-bezier(0.22,1,0.36,1), box-shadow 0.28s;
        }
        .spot-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 24px 60px rgba(30,58,31,0.13), 0 4px 16px rgba(0,0,0,0.06);
        }
        
        .spot-card-content {
            padding: 24px 22px;
        }
        
        .area-badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            background: var(--pg-mist);
            color: var(--pg-forest);
            padding: 5px 12px;
            border-radius: 100px;
            font-size: 10px;
            font-weight: 600;
            letter-spacing: 0.07em;
            text-transform: uppercase;
            margin-bottom: 12px;
        }
        .area-badge .material-icons-outlined {
            font-size: 12px;
        }
        
        .spot-card h3 {
            font-family: 'Playfair Display', serif;
            font-size: 1.25rem;
            font-weight: 500;
            color: var(--pg-forest);
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            gap: 10px;
            line-height: 1.4;
        }
        .spot-card h3 .material-icons-outlined {
            font-size: 20px;
            color: var(--pg-sage);
            background: var(--pg-mist);
            padding: 6px;
            border-radius: 8px;
        }
        
        .suggestion-category {
            margin-bottom: 16px;
        }
        .suggestion-category:last-child {
            margin-bottom: 12px;
        }
        .suggestion-category h4 {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 10px;
            font-weight: 600;
            color: #999;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            margin-bottom: 10px;
        }
        .suggestion-category h4 .material-icons-outlined {
            font-size: 14px;
            color: var(--pg-sage);
        }
        
        .suggestion-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .suggestion-list li {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 8px 12px;
            background: var(--pg-mist);
            border-radius: 8px;
            font-size: 13px;
            color: var(--pg-ink);
            margin-bottom: 6px;
            transition: all 0.2s;
            border-left: 2px solid var(--pg-sage);
        }
        .suggestion-list li:last-child {
            margin-bottom: 0;
        }
        .suggestion-list li:hover {
            background: var(--pg-sand);
            transform: translateX(2px);
        }
        .suggestion-list li .material-icons-outlined {
            font-size: 14px;
            color: var(--pg-sage);
        }
        
        .transport-tip {
            background: linear-gradient(135deg, rgba(59,130,246,0.08), rgba(37,99,235,0.06));
            padding: 14px 16px;
            border-radius: 12px;
            margin-top: 16px;
            font-size: 13px;
            color: #1e40af;
            border-left: 3px solid #3b82f6;
            display: flex;
            align-items: flex-start;
            gap: 10px;
        }
        .transport-tip .material-icons-outlined {
            font-size: 16px;
            color: #3b82f6;
            flex-shrink: 0;
            margin-top: 2px;
        }
        .transport-tip strong {
            color: #1e3a8a;
            font-weight: 600;
        }
        
        /* ══════════════════════════
        MODAL STYLES
        ══════════════════════════ */
        .modal-overlay {
            display: none;
            position: fixed;
            inset: 0;
            z-index: 9999;
            background: rgba(20,18,14,0.72);
            backdrop-filter: blur(6px);
            -webkit-backdrop-filter: blur(6px);
            align-items: center;
            justify-content: center;
            padding: 24px;
        }
        .modal-overlay.show {
            display: flex;
            animation: mOverlayIn 0.25s ease;
        }
        @keyframes mOverlayIn { from{opacity:0} to{opacity:1} }
        
        .modal-content {
            background: #fff;
            border-radius: 20px;
            width: 100%;
            max-width: 600px;
            max-height: 90vh;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0,0,0,0.04), 0 12px 40px rgba(0,0,0,0.18), 0 32px 80px rgba(0,0,0,0.12);
            animation: mShellIn 0.32s cubic-bezier(0.22,1,0.36,1);
            position: relative;
        }
        @keyframes mShellIn { from{opacity:0;transform:translateY(28px)} to{opacity:1;transform:translateY(0)} }
        
        .modal-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 20px 28px;
            border-bottom: 1px solid var(--m-sand);
            background: var(--m-mist);
        }
        .modal-header h2 {
            font-family: 'Playfair Display', serif;
            font-size: 1.4rem;
            font-weight: 500;
            color: var(--m-ink);
            margin: 0;
        }
        .close-modal {
            width: 36px;
            height: 36px;
            background: rgba(255,255,255,0.8);
            border: 1px solid var(--m-sand);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s;
        }
        .close-modal:hover {
            background: #fff;
            transform: scale(1.08);
        }
        .close-modal .material-icons-outlined {
            font-size: 18px;
            color: var(--m-ink);
        }
        
        .modal-body {
            padding: 28px;
            max-height: calc(90vh - 120px);
            overflow-y: auto;
        }
        .modal-body::-webkit-scrollbar { width: 4px; }
        .modal-body::-webkit-scrollbar-thumb { background: var(--m-sand); border-radius: 4px; }
        
        .modal-actions {
            display: flex;
            gap: 12px;
            justify-content: flex-end;
            margin-top: 24px;
            padding-top: 24px;
            border-top: 1px solid var(--m-sand);
        }
        .btn-cancel, .btn-confirm {
            padding: 12px 24px;
            border: none;
            border-radius: 12px;
            font-family: 'DM Sans', sans-serif;
            font-size: 13px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .btn-cancel {
            background: var(--m-mist);
            color: var(--m-ink);
            border: 1px solid var(--m-sand);
        }
        .btn-cancel:hover {
            background: var(--m-sand);
        }
        .btn-confirm {
            background: var(--m-forest);
            color: #fff;
        }
        .btn-confirm:hover {
            background: var(--m-sage);
            transform: translateY(-1px);
        }
        
        /* Logout Modal */
        .logout-message { text-align: center; margin-bottom: 20px; }
        .logout-icon {
            width: 48px; height: 48px;
            background: #ef4444; color: white;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 16px;
        }
        .logout-message h3 {
            font-family: 'Playfair Display', serif;
            margin: 16px 0 8px;
            color: var(--m-ink);
        }
        .logout-message p { color: #666; margin-bottom: 24px; }
        .logout-modal .modal-actions { justify-content: center; }
        
        /* ══════════════════════════
        RESPONSIVE
        ══════════════════════════ */
        @media (max-width: 1024px) {
            .page-inner { padding: 36px 32px 60px; }
            .hero-section { padding: 0 40px 40px; height: 320px; }
            .hero-section h1 { font-size: 2.8rem; }
        }
        
        @media (max-width: 768px) {
            .main-content.full-width .main-header {
                padding: 0 20px;
                height: auto;
                min-height: 60px;
                flex-direction: column;
                gap: 0;
                align-items: stretch;
            }
            .header-left { padding: 12px 0 0; justify-content: space-between; }
            .header-right { padding: 4px 0 8px; justify-content: center; overflow-x: auto; }
            .header-nav { gap: 0; }
            .nav-link { padding: 8px 10px; font-size: 11.5px; height: auto; border-bottom: none; }
            .nav-link span:not(.material-icons-outlined) { display: none; }
            
            .hero-section { height: 260px; padding: 0 24px 32px; background-attachment: scroll; }
            .hero-section h1 { font-size: 2rem; }
            .hero-section p { font-size: 0.95rem; }
            .hero-pills { flex-wrap: wrap; }
            
            .page-inner { padding: 24px 20px 48px; }
            .tips-grid { grid-template-columns: 1fr; gap: 20px; }
            .spot-cards { grid-template-columns: 1fr; gap: 20px; }
            .filter-row { flex-direction: column; align-items: stretch; }
            .search-bar-container { max-width: 100%; }
            .modal-actions { flex-direction: column; }
            .btn-cancel, .btn-confirm { width: 100%; justify-content: center; }
        }
        
        @media (max-width: 480px) {
            .hero-section h1 { font-size: 1.8rem; }
            .info-cards-row { grid-template-columns: 1fr; }
            .tip-card-header { padding: 20px 18px 14px; }
            .tip-card-body { padding: 18px 18px 20px; }
        }
    </style>
</head>
<body>
    <!-- MAIN CONTENT -->
    <main class="main-content full-width">
        <header class="main-header">
            <div class="header-left">
                <div class="logo" style="display:flex;align-items:center;gap:12px;margin-right:30px;">
                    <img src="../lgo.png" alt="SJDM Tours Logo" style="height:32px;width:32px;object-fit:contain;border-radius:6px;">
                    <span style="font-family:'Playfair Display',serif;font-weight:700;font-size:17px;color:#fff;">SJDM TOURS</span>
                </div>
                <h1>Travel Tips</h1>
            </div>
            <div class="header-right">
                <nav class="header-nav">
                    <a href="user-index.php" class="nav-link"><span class="material-icons-outlined">dashboard</span><span>Dashboard</span></a>
                    <a href="user-guides-page.php" class="nav-link"><span class="material-icons-outlined">people</span><span>Tour Guides</span></a>
                    <a href="user-book.php" class="nav-link"><span class="material-icons-outlined">event</span><span>Book Now</span></a>
                    <a href="user-booking-history.php" class="nav-link"><span class="material-icons-outlined">history</span><span>Booking History</span></a>
                    <a href="user-tourist-spots.php" class="nav-link"><span class="material-icons-outlined">place</span><span>Tourist Spots</span></a>
                                        <a href="user-travel-tips.php" class="nav-link active"><span class="material-icons-outlined">tips_and_updates</span><span>Travel Tips</span></a>
                </nav>
                <div class="header-actions">
                    <div class="user-profile-dropdown">
                        <button class="profile-trigger">
                            <div class="profile-avatar"><?php echo isset($currentUser['name']) ? strtoupper(substr($currentUser['name'],0,1)) : 'U'; ?></div>
                            <span class="profile-name"><?php echo htmlspecialchars($currentUser['name']); ?></span>
                            <span class="material-icons-outlined">expand_more</span>
                        </button>
                        <div class="dropdown-menu">
                            <div class="dropdown-header">
                                <div class="profile-avatar-large"><?php echo isset($currentUser['name']) ? strtoupper(substr($currentUser['name'],0,1)) : 'U'; ?></div>
                                <h4><?php echo htmlspecialchars($currentUser['name']); ?></h4>
                                <p><?php echo htmlspecialchars($currentUser['email']); ?></p>
                            </div>
                            <div class="dropdown-divider"></div>
                            <a href="user-index.php" class="dropdown-item"><span class="material-icons-outlined">dashboard</span><span>Dashboard</span></a>
                            <a href="user-booking-history.php" class="dropdown-item"><span class="material-icons-outlined">history</span><span>Booking History</span></a>
                            <a href="user-saved-tours.php" class="dropdown-item"><span class="material-icons-outlined">favorite</span><span>Saved Tours</span></a>
                            <a href="#" class="dropdown-item" onclick="openPreferencesModal(); return false;"><span class="material-icons-outlined">tune</span><span>Preferences</span></a>
                            <div class="dropdown-divider"></div>
                            <a href="user-logout.php" class="dropdown-item"><span class="material-icons-outlined">logout</span><span>Sign Out</span></a>
                        </div>
                    </div>
                </div>
            </div>
        </header>
        
        <div class="content-area">
            <!-- Hero Section -->
            <div class="hero-section">
                <h1>Travel Tips &<br><em>Recommendations</em></h1>
                <p>Find accommodation, restaurants, and practical advice for your SJDM adventure</p>
                <div class="hero-pills">
                    <div class="hero-pill">
                        <span class="material-icons-outlined">tips_and_updates</span>
                        Expert Tips
                    </div>
                    <div class="hero-pill">
                        <span class="material-icons-outlined">hotel</span>
                        Accommodations
                    </div>
                    <div class="hero-pill">
                        <span class="material-icons-outlined">restaurant</span>
                        Dining Guide
                    </div>
                </div>
            </div>
            
            <div class="page-inner">
                <!-- Info Cards (Weather & Date) -->
                <div class="info-cards-row">
                    <div class="info-card">
                        <div class="info-card-icon">
                            <span class="material-icons-outlined">calendar_today</span>
                        </div>
                        <div class="info-card-content">
                            <div class="info-label">Today's Date</div>
                            <div class="info-value"><?php echo htmlspecialchars($currentWeekday); ?></div>
                            <div class="info-secondary"><?php echo htmlspecialchars($currentDate); ?></div>
                        </div>
                    </div>
                    <div class="info-card">
                        <div class="info-card-icon">
                            <span class="material-icons-outlined"><?php echo $weatherLabel === 'Clear' ? 'wb_sunny' : ($weatherLabel === 'Clouds' ? 'cloud' : 'wb_cloudy'); ?></span>
                        </div>
                        <div class="info-card-content">
                            <div class="info-label">Current Weather</div>
                            <div class="info-value"><?php echo $currentTemp; ?>°C</div>
                            <div class="info-secondary"><?php echo htmlspecialchars($weatherLabel); ?></div>
                        </div>
                    </div>
                </div>
                
                <!-- User Preferences -->
                <?php if (!empty($userPreferences)): ?>
                <div class="user-preferences-section">
                    <h2 class="section-title">🤖 Your Selected Interests</h2>
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
                            <span class="material-icons-outlined"><?php echo $iconMap[$preference] ?? 'category'; ?></span>
                            <?php echo htmlspecialchars($categoryMap[$preference] ?? $preference); ?>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <p style="color:#2c5f2d;font-size:14px;margin-top:12px;font-style:italic;">🎯 <strong>AI-Powered Results:</strong> Travel tips below are filtered based on your selected interests!</p>
                </div>
                <?php endif; ?>
                
                <!-- Search & Filter -->
                <div class="travel-tips-filters">
                    <div style="display:flex;align-items:baseline;justify-content:space-between;margin-bottom:20px;flex-wrap:wrap;gap:12px;">
                        <div class="tips-section-title">
                            <span>Helpful Information</span>
                            Browse All Tips
                        </div>
                    </div>
                    <div class="filter-row">
                        <div class="search-bar-container">
                            <span class="material-icons-outlined">search</span>
                            <input type="text" id="searchTips" placeholder="Search tips..." onkeyup="filterTips()">
                        </div>
                        <div class="filter-group">
                            <label>Category</label>
                            <select class="filter-select" id="categoryFilter" onchange="filterTips()">
                                <option value="all">All Categories</option>
                                <option value="accommodation">Accommodation</option>
                                <option value="transportation">Transportation</option>
                                <option value="dining">Dining</option>
                                <option value="safety">Safety</option>
                                <option value="packing">Packing</option>
                                <option value="general">General</option>
                            </select>
                        </div>
                    </div>
                </div>
                
                <!-- General Travel Tips Section -->
                <h2 class="tips-section-title">
                    <span>Expert Advice</span>
                    General Travel Tips for SJDM
                </h2>
                <div class="tips-grid" id="tipsGrid">
                    <?php
                    // Fetch travel tips data from database
                    $conn = getDatabaseConnection();
                    if ($conn) {
                        $query = "SELECT * FROM travel_tips WHERE is_active = 'yes' ORDER BY display_order";
                        $result = $conn->query($query);
                        if ($result && $result->num_rows > 0) {
                            while ($tip = $result->fetch_assoc()) {
                                $category = htmlspecialchars($tip['category'] ?? 'general');
                                $icon = htmlspecialchars($tip['icon'] ?? 'tips_and_updates');
                                $title = htmlspecialchars($tip['title']);
                                $description = htmlspecialchars($tip['description'] ?? '');
                                
                                // Map icon colors based on category
                                $iconColors = [
                                    'accommodation' => 'var(--pg-sage)',
                                    'transportation' => '#3b82f6',
                                    'dining' => '#f59e0b',
                                    'safety' => '#ef4444',
                                    'packing' => '#8b5cf6',
                                    'general' => 'var(--pg-sage)'
                                ];
                                $iconColor = $iconColors[$category] ?? 'var(--pg-sage)';
                                
                                echo '<div class="tip-card" data-category="' . $category . '">';
                                echo '<div class="tip-card-header">';
                                echo '<div class="tip-icon" style="background:' . $iconColor . '"><span class="material-icons-outlined">' . $icon . '</span></div>';
                                echo '<h3 class="tip-title">' . $title . '</h3>';
                                echo '<span class="tip-category"><span class="material-icons-outlined">category</span>' . ucfirst($category) . '</span>';
                                echo '</div>';
                                echo '<div class="tip-card-body">';
                                
                                // Convert description from newlines to list items
                                $descriptionLines = explode("\n", $tip['description']);
                                if (!empty(array_filter($descriptionLines))) {
                                    echo '<ul class="tip-list">';
                                    foreach ($descriptionLines as $line) {
                                        $trimmedLine = trim($line);
                                        if (!empty($trimmedLine)) {
                                            echo '<li><span class="material-icons-outlined">check_circle</span>' . htmlspecialchars($trimmedLine) . '</li>';
                                        }
                                    }
                                    echo '</ul>';
                                } else {
                                    echo '<p class="tip-description">' . $description . '</p>';
                                }
                                
                                echo '</div></div>';
                            }
                        } else {
                            echo '<div class="no-results" style="grid-column: 1 / -1; text-align: center; padding: 60px 20px;">';
                            echo '<span class="material-icons-outlined" style="font-size: 48px; color: #9ca3af;">tips_and_updates</span>';
                            echo '<h3 style="color: #6b7280; margin-top: 16px; font-family: \'Playfair Display\', serif;">No travel tips found</h3>';
                            echo '<p style="color: #9ca3af;">Please check back later for travel tips.</p>';
                            echo '</div>';
                        }
                        closeDatabaseConnection($conn);
                    } else {
                        echo '<div class="error-message" style="grid-column: 1 / -1; text-align: center; padding: 60px 20px;">';
                        echo '<span class="material-icons-outlined" style="font-size: 48px; color: #ef4444;">error</span>';
                        echo '<h3 style="color: #ef4444; margin-top: 16px; font-family: \'Playfair Display\', serif;">Database Connection Error</h3>';
                        echo '<p style="color: #6b7280;">Unable to load travel tips. Please try again later.</p>';
                        echo '</div>';
                    }
                    ?>
                </div>
                
                <!-- Accommodation & Restaurant Suggestions Section -->
                <div class="accommodation-section">
                    <h2 class="tips-section-title">
                        <span>Where to Stay & Eat</span>
                        Near Tourist Spots
                    </h2>
                    <p style="color:#666;font-size:14px;margin-bottom:28px;">Find the best hotels, restaurants, and accommodations near popular tourist destinations</p>
                    
                    <div class="spot-cards" id="accommodationCards">
                        <div class="spot-card" data-spot="City Oval (People's Park)">
                            <div class="spot-card-content">
                                <div class="area-badge">
                                    <span class="material-icons-outlined">location_on</span>
                                    SJDM Center
                                </div>
                                <h3>
                                    <span class="material-icons-outlined">park</span>
                                    City Oval (People's Park)
                                </h3>
                                <div class="suggestion-category">
                                    <h4><span class="material-icons-outlined">hotel</span> Hotels</h4>
                                    <ul class="suggestion-list">
                                        <li><span class="material-icons-outlined">star</span>Hotel Sogo</li>
                                        <li><span class="material-icons-outlined">star</span>Hotel Turista</li>
                                    </ul>
                                </div>
                                <div class="suggestion-category">
                                    <h4><span class="material-icons-outlined">restaurant</span> Restaurants</h4>
                                    <ul class="suggestion-list">
                                        <li><span class="material-icons-outlined">restaurant</span>Escobar's</li>
                                        <li><span class="material-icons-outlined">restaurant</span>Roadside Dampa</li>
                                    </ul>
                                </div>
                                <div class="transport-tip">
                                    <span class="material-icons-outlined">directions_bus</span>
                                    <div><strong>Transport Tip:</strong> Very accessible via jeepney and tricycle. Parking available.</div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="spot-card" data-spot="Our Lady of Lourdes Parish / Padre Pio Parish">
                            <div class="spot-card-content">
                                <div class="area-badge">
                                    <span class="material-icons-outlined">location_on</span>
                                    Tungkong Mangga
                                </div>
                                <h3>
                                    <span class="material-icons-outlined">church</span>
                                    Our Lady of Lourdes / Padre Pio Parish
                                </h3>
                                <div class="suggestion-category">
                                    <h4><span class="material-icons-outlined">hotel</span> Hotels</h4>
                                    <ul class="suggestion-list">
                                        <li><span class="material-icons-outlined">star</span>Hotel Sogo</li>
                                        <li><span class="material-icons-outlined">star</span>Staycation Amaia</li>
                                    </ul>
                                </div>
                                <div class="suggestion-category">
                                    <h4><span class="material-icons-outlined">restaurant</span> Restaurants</h4>
                                    <ul class="suggestion-list">
                                        <li><span class="material-icons-outlined">restaurant</span>Max's SM SJDM</li>
                                        <li><span class="material-icons-outlined">restaurant</span>Escobar's</li>
                                    </ul>
                                </div>
                                <div class="transport-tip">
                                    <span class="material-icons-outlined">directions_bus</span>
                                    <div><strong>Transport Tip:</strong> Near major highways. Easy access from city center.</div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="spot-card" data-spot="The Rising Heart Monument">
                            <div class="spot-card-content">
                                <div class="area-badge">
                                    <span class="material-icons-outlined">location_on</span>
                                    Paradise 3 Area
                                </div>
                                <h3>
                                    <span class="material-icons-outlined">landscape</span>
                                    The Rising Heart Monument
                                </h3>
                                <div class="suggestion-category">
                                    <h4><span class="material-icons-outlined">hotel</span> Hotels</h4>
                                    <ul class="suggestion-list">
                                        <li><span class="material-icons-outlined">star</span>Local lodges in Paradise 3 area</li>
                                    </ul>
                                </div>
                                <div class="suggestion-category">
                                    <h4><span class="material-icons-outlined">restaurant</span> Restaurants</h4>
                                    <ul class="suggestion-list">
                                        <li><span class="material-icons-outlined">restaurant</span>Los Arcos De Hermano (close resort)</li>
                                        <li><span class="material-icons-outlined">restaurant</span>Escobar's (short drive)</li>
                                    </ul>
                                </div>
                                <div class="transport-tip">
                                    <span class="material-icons-outlined">directions_bus</span>
                                    <div><strong>Transport Tip:</strong> Best visited by private vehicle. Photo spot along highway.</div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="spot-card" data-spot="Abes Farm / Paradise Hill Farm">
                            <div class="spot-card-content">
                                <div class="area-badge">
                                    <span class="material-icons-outlined">location_on</span>
                                    Paradise / Rural SJDM
                                </div>
                                <h3>
                                    <span class="material-icons-outlined">agriculture</span>
                                    Abes Farm / Paradise Hill Farm
                                </h3>
                                <div class="suggestion-category">
                                    <h4><span class="material-icons-outlined">hotel</span> Hotels</h4>
                                    <ul class="suggestion-list">
                                        <li><span class="material-icons-outlined">star</span>Los Arcos</li>
                                        <li><span class="material-icons-outlined">star</span>Pacific Waves Resort</li>
                                    </ul>
                                </div>
                                <div class="suggestion-category">
                                    <h4><span class="material-icons-outlined">restaurant</span> Restaurants</h4>
                                    <ul class="suggestion-list">
                                        <li><span class="material-icons-outlined">restaurant</span>Farm-to-table restaurants in resort areas</li>
                                    </ul>
                                </div>
                                <div class="transport-tip">
                                    <span class="material-icons-outlined">directions_bus</span>
                                    <div><strong>Transport Tip:</strong> Requires private transportation. Rural roads may be narrow.</div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="spot-card" data-spot="Waterfalls">
                            <div class="spot-card-content">
                                <div class="area-badge">
                                    <span class="material-icons-outlined">location_on</span>
                                    Brgy. San Isidro / Sto. Cristo
                                </div>
                                <h3>
                                    <span class="material-icons-outlined">waterfall</span>
                                    Waterfalls (Burong, Kaytitinga, Otso-Otso, Tungtong)
                                </h3>
                                <div class="suggestion-category">
                                    <h4><span class="material-icons-outlined">hotel</span> Hotels</h4>
                                    <ul class="suggestion-list">
                                        <li><span class="material-icons-outlined">star</span>Hotel Sogo</li>
                                        <li><span class="material-icons-outlined">star</span>Central SJDM accommodations</li>
                                    </ul>
                                </div>
                                <div class="suggestion-category">
                                    <h4><span class="material-icons-outlined">restaurant</span> Restaurants</h4>
                                    <ul class="suggestion-list">
                                        <li><span class="material-icons-outlined">restaurant</span>Escobar's</li>
                                        <li><span class="material-icons-outlined">restaurant</span>Local carinderias</li>
                                    </ul>
                                </div>
                                <div class="transport-tip">
                                    <span class="material-icons-outlined">directions_bus</span>
                                    <div><strong>Transport Tip:</strong> Requires local guides and transportation. Start early in the morning.</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
    
    <!-- Modal Container -->
    <div id="modalContainer"></div>
    
    <script src="user-script.js"></script>
    <script>
        // ========== USER PROFILE DROPDOWN ==========
        function initUserProfileDropdown() {
            const profileDropdown = document.querySelector('.user-profile-dropdown');
            const profileTrigger = document.querySelector('.profile-trigger');
            const dropdownMenu = document.querySelector('.dropdown-menu');
            const logoutLink = document.querySelector('[href="user-logout.php"]');
            
            if (!profileDropdown || !profileTrigger || !dropdownMenu) return;
            
            profileTrigger.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                dropdownMenu.classList.toggle('show');
            });
            
            document.addEventListener('click', function(e) {
                if (!profileDropdown.contains(e.target)) {
                    dropdownMenu.classList.remove('show');
                }
            });
            
            if (logoutLink) {
                logoutLink.addEventListener('click', function(e) {
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
                            <button class="btn-confirm" onclick="confirmLogout()">
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
        
        function confirmLogout() {
            const modal = document.querySelector('.modal-overlay');
            if (modal) modal.remove();
            window.location.href = 'user-logout.php';
        }
        
        // Filter tips function
        function filterTips() {
            const searchTerm = document.getElementById('searchTips').value.toLowerCase();
            const categoryFilter = document.getElementById('categoryFilter').value;
            const cards = document.querySelectorAll('.tip-card');
            
            cards.forEach(card => {
                const text = card.textContent.toLowerCase();
                const category = card.getAttribute('data-category');
                
                let show = true;
                
                if (searchTerm && !text.includes(searchTerm)) show = false;
                if (categoryFilter !== 'all' && category !== categoryFilter) show = false;
                
                card.style.display = show ? 'block' : 'none';
            });
        }
        
        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            initUserProfileDropdown();
        });
    </script>
    
    <!-- Preferences Modal -->
    <?php include __DIR__ . '/../components/preferences-modal.php'; ?>
</body>
</html>