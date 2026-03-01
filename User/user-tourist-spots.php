<?php
// Include database connection first
require_once '../config/database.php';

// Start session manually before including auth
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include auth functions (but wrap to avoid PHPMailer issues)
try {
    $phpmailerPath = '../PHPMailer-6.9.1/src/PHPMailer.php';
    if (!file_exists($phpmailerPath)) {
        if (!class_exists('PHPMailer\PHPMailer\PHPMailer')) {
            class PHPMailer {
                public function __construct() {}
            }
        }
    }
    require_once '../config/auth.php';
} catch (Exception $e) {
    error_log("Auth.php loading failed: " . $e->getMessage());
    if (!function_exists('isLoggedIn')) {
        function isLoggedIn() {
            return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
        }
    }
    if (!function_exists('getCurrentUserId')) {
        function getCurrentUserId() {
            return isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
        }
    }
}

// OpenWeatherMap API configuration
$apiKey = '6c21a0d2aaf514cb8d21d56814312b19';
$weatherUrl = "https://api.openweathermap.org/data/2.5/weather?q=San%20Jose%20Del%20Monte,Bulacan&appid={$apiKey}&units=metric";

$weatherData = null;
$weatherError = null;
$currentTemp = '28';
$weatherLabel = 'Sunny';

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

$currentWeekday = date('l');
$currentDate = date('F Y');
$isLoggedIn = isset($_SESSION['user_id']);

$conn = getDatabaseConnection();
$currentUser = [];
$userPreferences = [];
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
        $prefStmt = $conn->prepare("SELECT category FROM user_preferences WHERE user_id = ?");
        $prefStmt->bind_param("i", $_SESSION['user_id']);
        $prefStmt->execute();
        $prefResult = $prefStmt->get_result();
        while ($pref = $prefResult->fetch_assoc()) {
            $userPreferences[] = $pref['category'];
        }
        $prefStmt->close();
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
    <link rel="icon" type="image/png" href="../lgo.png">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,500;0,700;1,400;1,500&family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;1,9..40,300&family=Cormorant+Garamond:ital,wght@0,300;0,400;0,600;1,300;1,400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="user-styles.css">
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
            --text-primary: #1a1a18;
            --text-secondary: #5a5a52;
            --border: #e4e0d8;
            --bg-light: #f5f7fa;
            --white: #ffffff;
            --gray-50: #f9fafb;
            --gray-100: #f3f4f6;
            --gray-200: #e5e7eb;
            --gray-300: #d1d5db;
            --shadow-sm: 0 1px 2px rgba(0,0,0,0.05);
            --shadow-md: 0 4px 6px -1px rgba(0,0,0,0.1), 0 2px 4px -1px rgba(0,0,0,0.06);
            --shadow-lg: 0 10px 15px -3px rgba(0,0,0,0.1), 0 4px 6px -2px rgba(0,0,0,0.05);
            --shadow-xl: 0 20px 25px -5px rgba(0,0,0,0.1), 0 10px 10px -5px rgba(0,0,0,0.04);
            --shadow-2xl: 0 25px 50px -12px rgba(0,0,0,0.25);
            --radius-sm: 0.375rem;
            --radius-md: 0.5rem;
            --radius-lg: 0.75rem;
            --radius-xl: 1rem;
            --radius-2xl: 1.5rem;
            --transition: all 0.3s cubic-bezier(0.4,0,0.2,1);

            /* Page palette */
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
           HEADER
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

        /* hide the <h1>Tourist Spots</h1> title in header */
        .main-content.full-width .main-header h1 {
            display: none;
        }

        .header-left {
            display: flex;
            align-items: center;
            gap: 0;
        }

        .header-right {
            display: flex;
            align-items: center;
            gap: 4px;
        }

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
        .profile-avatar-large { width: 56px; height: 56px; font-size: 20px; margin: 0 auto 12px; border-radius: 50%; background: var(--pg-sage); color: white; display: flex; align-items: center; justify-content: center; font-weight: bold; }
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
            height: 520px;
            display: flex;
            flex-direction: column;
            justify-content: flex-end;
            align-items: flex-start;
            padding: 0 72px 64px;
            overflow: hidden;
            margin-bottom: 0;
            border-radius: 0;
            background:
                linear-gradient(to bottom, rgba(15,25,16,0.25) 0%, rgba(15,25,16,0.1) 40%, rgba(15,25,16,0.75) 100%),
                url('https://images.unsplash.com/photo-1469474968028-56623f02e42e?q=80&w=2070&auto=format&fit=crop') center/cover no-repeat;
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

        /* remove the shimmer after-line */
        .hero-section::after { display: none; }

        .hero-section h1 {
            font-family: 'Playfair Display', serif;
            font-size: 4rem;
            font-weight: 500;
            letter-spacing: -0.02em;
            line-height: 1.05;
            color: #fff;
            margin-bottom: 16px;
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
            margin: 0 0 32px;
            line-height: 1.65;
            text-shadow: 0 1px 8px rgba(0,0,0,0.2);
            animation: heroFadeUp 0.9s 0.12s cubic-bezier(0.22,1,0.36,1) both;
            position: relative; z-index: 2;
        }

        @keyframes heroFadeUp {
            from { opacity:0; transform:translateY(24px); }
            to   { opacity:1; transform:translateY(0); }
        }
        @keyframes fadeInUp { from{opacity:0;transform:translateY(40px)} to{opacity:1;transform:translateY(0)} }

        /* Hero stat pills */
        .hero-pills {
            display: flex; align-items: center; gap: 10px;
            position: relative; z-index: 2;
            animation: heroFadeUp 0.9s 0.22s cubic-bezier(0.22,1,0.36,1) both;
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
            max-width: 1360px;
            margin: 0 auto;
            padding: 48px 48px 80px;
        }

        /* ── Preferences banner ── */
        .ai-preferences-display {
            background: linear-gradient(135deg, rgba(30,58,31,0.06), rgba(74,124,78,0.04)) !important;
            border: 1px solid rgba(30,58,31,0.14) !important;
            border-radius: 16px !important;
            padding: 20px 24px !important;
            margin-bottom: 32px !important;
        }

        /* ══════════════════════════
           FILTER BAR
        ══════════════════════════ */
        .travelry-filters {
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

        .filter-group label {
            display: none;
        }

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

        /* Section title above grid */
        .section-title {
            font-family: 'Playfair Display', serif;
            font-size: 1.6rem;
            font-weight: 500;
            color: var(--pg-forest);
            margin-bottom: 28px;
            letter-spacing: -0.01em;
        }
        .section-title span {
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
           CARD GRID
        ══════════════════════════ */
        .travelry-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 28px;
        }

        /* ── Card ── */
        .travelry-card {
            background: var(--pg-cream);
            border-radius: 20px;
            overflow: hidden;
            border: 1px solid var(--pg-sand);
            transition: transform 0.28s cubic-bezier(0.22,1,0.36,1), box-shadow 0.28s;
            cursor: pointer;
        }
        .travelry-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 24px 60px rgba(30,58,31,0.13), 0 4px 16px rgba(0,0,0,0.06);
        }

        /* Card image */
        .card-image {
            position: relative;
            height: 210px;
            overflow: hidden;
            background: var(--pg-sand);
        }
        .card-image img {
            width: 100%; height: 100%;
            object-fit: cover; object-position: center;
            display: block;
            transition: transform 5s ease;
        }
        .travelry-card:hover .card-image img { transform: scale(1.06); }
        .card-image-placeholder {
            width: 100%; height: 100%;
            display: flex; align-items: center; justify-content: center;
            position: relative;
        }

        /* Card badge (category pill on image) */
        .card-badge {
            position: absolute; top: 14px; left: 14px;
            display: flex; align-items: center; gap: 5px;
            background: rgba(255,255,255,0.15);
            backdrop-filter: blur(10px); -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.25);
            color: #fff;
            font-family: 'DM Sans', sans-serif;
            font-size: 11px; font-weight: 500;
            letter-spacing: 0.08em; text-transform: uppercase;
            padding: 5px 12px 5px 8px;
            border-radius: 100px;
        }
        .card-badge .material-icons-outlined { font-size: 13px; }

        /* Card labels on image bottom */
        .card-labels {
            position: absolute; bottom: 0; left: 0; right: 0;
            background: linear-gradient(to top, rgba(0,0,0,0.65) 0%, transparent 100%);
            padding: 20px 12px 10px;
            display: flex; flex-wrap: wrap; gap: 5px;
            border-radius: 0;
        }
        .interest-label {
            display: flex; align-items: center; gap: 4px;
            padding: 3px 9px; border-radius: 100px;
            font-family: 'DM Sans', sans-serif;
            font-size: 10.5px; font-weight: 500;
            backdrop-filter: blur(4px);
            transition: all 0.2s;
        }
        .interest-label .material-icons-outlined { font-size: 11px; }
        .interest-label.popular { background: rgba(201,168,92,0.88); color: white; }
        .interest-label.family-friendly { background: rgba(16,185,129,0.88); color: white; }
        .interest-label.adventure { background: rgba(59,130,246,0.88); color: white; }
        .interest-label.nature { background: rgba(34,197,94,0.88); color: white; }
        .interest-label.farm { background: rgba(132,204,22,0.88); color: white; }
        .interest-label.park { background: rgba(6,182,212,0.88); color: white; }
        .interest-label.religious { background: rgba(139,92,246,0.88); color: white; }
        .interest-label.urban { background: rgba(245,158,11,0.88); color: white; }
        .interest-label.historical { background: rgba(107,114,128,0.88); color: white; }
        .interest-label.default { background: rgba(74,124,78,0.88); color: white; }

        /* Card content */
        .card-content {
            padding: 20px 22px 22px;
            display: flex;
            flex-direction: column;
            gap: 0;
        }

        /* Weather row */
        .card-weather {
            display: flex;
            align-items: center;
            gap: 6px;
            margin-bottom: 10px;
        }
        .card-weather .material-icons-outlined { color: var(--pg-sage); font-size: 16px; }
        .weather-temp { font-weight: 600; color: var(--pg-forest); font-size: 12.5px; }
        .weather-desc { color: #999; font-size: 11.5px; text-transform: capitalize; }

        /* Title */
        .card-title {
            font-family: 'Playfair Display', serif;
            font-size: 1.25rem;
            font-weight: 500;
            color: var(--pg-forest);
            line-height: 1.25;
            margin-bottom: 4px;
            letter-spacing: -0.01em;
        }

        /* Category tag */
        .card-category {
            display: inline-block;
            font-size: 11px;
            font-weight: 500;
            color: var(--pg-sage);
            letter-spacing: 0.07em;
            text-transform: uppercase;
            margin-bottom: 14px;
        }

        /* Stats row */
        .card-stats {
            display: flex;
            gap: 0;
            margin-bottom: 14px;
            background: var(--pg-mist);
            border-radius: 12px;
            overflow: hidden;
            border: 1px solid var(--pg-sand);
        }
        .stat-item {
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 3px;
            padding: 10px 12px;
            border-right: 1px solid var(--pg-sand);
        }
        .stat-item:last-child { border-right: none; }
        .stat-label {
            font-size: 9.5px;
            font-weight: 500;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: #aaa;
        }
        .stat-value {
            font-family: 'Playfair Display', serif;
            font-size: 0.95rem;
            font-weight: 500;
            color: var(--pg-ink);
        }

        /* Features chips */
        .card-features-text { margin-bottom: 12px; }
        .card-features-text .features-label {
            font-size: 9.5px;
            font-weight: 600;
            color: #bbb;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            margin-bottom: 7px;
        }
        .card-features-text .features-list {
            display: flex;
            flex-wrap: wrap;
            gap: 5px;
        }
        .card-features-text .feature-item {
            display: inline-flex;
            align-items: center;
            padding: 4px 11px;
            background: var(--pg-mist);
            color: var(--pg-ink);
            border-radius: 100px;
            font-size: 11px;
            font-weight: 400;
            border: 1px solid var(--pg-sand);
            white-space: nowrap;
        }
        .card-features-text .feature-item:hover { background: var(--pg-sand); }

        /* Attract / info rows */
        .card-attract {
            margin-bottom: 16px;
            padding: 10px 12px;
            background: var(--pg-mist);
            border-radius: 12px;
            border: 1px solid var(--pg-sand);
            display: flex;
            flex-direction: column;
            gap: 5px;
        }
        .attract-row {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 12px;
            color: #666;
            line-height: 1.3;
        }
        .attract-row .material-icons-outlined { font-size: 13px; color: var(--pg-sage); flex-shrink: 0; }
        .attract-highlight {
            display: inline-block;
            background: rgba(30,58,31,0.08);
            color: var(--pg-forest);
            font-weight: 600;
            font-size: 11px;
            padding: 1px 7px;
            border-radius: 100px;
            margin-left: 2px;
        }

        /* Card CTA button */
        .card-buttons {
            margin-top: auto;
        }
        .card-button {
            width: 100%;
            padding: 12px 20px;
            background: var(--pg-forest);
            color: #fff;
            border: none;
            border-radius: 12px;
            font-family: 'DM Sans', sans-serif;
            font-size: 13px;
            font-weight: 500;
            letter-spacing: 0.03em;
            cursor: pointer;
            transition: background 0.2s, transform 0.15s;
        }
        .card-button:hover {
            background: var(--pg-sage);
            transform: translateY(-1px);
        }

        /* ════════════════════════════════════════
           REDESIGNED MODAL STYLES
        ════════════════════════════════════════ */

        /* Overlay */
        #touristSpotModal {
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
        #touristSpotModal.active {
            display: flex;
            animation: mOverlayIn 0.25s ease;
        }
        @keyframes mOverlayIn { from{opacity:0} to{opacity:1} }

        /* Shell */
        .m-shell {
            background: #fff;
            border-radius: 20px;
            width: 100%;
            max-width: 860px;
            max-height: 92vh;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0,0,0,0.04), 0 12px 40px rgba(0,0,0,0.18), 0 32px 80px rgba(0,0,0,0.12);
            animation: mShellIn 0.32s cubic-bezier(0.22,1,0.36,1);
            position: relative;
        }
        @keyframes mShellIn { from{opacity:0;transform:translateY(28px)} to{opacity:1;transform:translateY(0)} }

        /* Hero image */
        .m-hero {
            position: relative;
            height: 270px;
            overflow: hidden;
            flex-shrink: 0;
            background: #ddd;
            border-radius: 20px 20px 0 0;
        }
        .m-hero img {
            width: 100%; height: 100%;
            object-fit: cover; object-position: center;
            display: block;
            transition: transform 6s ease;
        }
        .m-shell:hover .m-hero img { transform: scale(1.04); }
        .m-hero-overlay {
            position: absolute; inset: 0;
            background: linear-gradient(to bottom, rgba(0,0,0,0.06) 0%, transparent 30%, rgba(10,8,6,0.58) 100%);
            pointer-events: none;
        }
        /* Category pill */
        .m-hero-cat {
            position: absolute; top: 20px; left: 20px;
            display: flex; align-items: center; gap: 6px;
            background: rgba(255,255,255,0.14);
            backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255,255,255,0.26);
            color: #fff;
            font-family: 'DM Sans', sans-serif;
            font-size: 11px; font-weight: 500;
            letter-spacing: 0.1em; text-transform: uppercase;
            padding: 6px 14px 6px 10px;
            border-radius: 100px;
        }
        .m-hero-cat .material-icons-outlined { font-size: 13px; }
        /* Close */
        .m-close {
            position: absolute; top: 16px; right: 16px;
            width: 36px; height: 36px;
            background: rgba(255,255,255,0.12);
            backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255,255,255,0.22);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            cursor: pointer;
            transition: background 0.2s, transform 0.18s;
            color: #fff;
        }
        .m-close:hover { background: rgba(255,255,255,0.26); transform: scale(1.08); }
        .m-close .material-icons-outlined { font-size: 18px; }
        /* Name / location overlay */
        .m-hero-meta {
            position: absolute; bottom: 20px; left: 24px; right: 24px;
        }
        .m-hero-name {
            font-family: 'Cormorant Garamond', serif;
            font-size: 2.3rem; font-weight: 300;
            color: #fff; line-height: 1.1; letter-spacing: -0.01em;
            text-shadow: 0 2px 20px rgba(0,0,0,0.28);
        }
        .m-hero-loc {
            display: flex; align-items: center; gap: 4px;
            color: rgba(255,255,255,0.72);
            font-family: 'DM Sans', sans-serif;
            font-size: 12px; font-weight: 400; margin-top: 6px;
        }
        .m-hero-loc .material-icons-outlined { font-size: 13px; }

        /* Tabs */
        .m-tabs {
            display: flex; align-items: center;
            border-bottom: 1px solid var(--m-sand);
            padding: 0 28px; gap: 0;
            flex-shrink: 0; background: #fff;
        }
        .m-tab {
            font-family: 'DM Sans', sans-serif;
            font-size: 12px; font-weight: 500;
            color: #999; letter-spacing: 0.07em; text-transform: uppercase;
            padding: 15px 18px 13px;
            border: none; background: none; cursor: pointer;
            border-bottom: 2px solid transparent; margin-bottom: -1px;
            transition: color 0.2s, border-color 0.2s;
            display: flex; align-items: center; gap: 6px;
        }
        .m-tab .material-icons-outlined { font-size: 14px; }
        .m-tab:hover { color: var(--m-ink); }
        .m-tab.active { color: var(--m-forest); border-bottom-color: var(--m-forest); }

        /* Scrollable body */
        .m-body {
            overflow-y: auto; flex: 1;
            scrollbar-width: thin; scrollbar-color: var(--m-sand) transparent;
        }
        .m-body::-webkit-scrollbar { width: 4px; }
        .m-body::-webkit-scrollbar-thumb { background: var(--m-sand); border-radius: 4px; }

        /* Tab panels */
        .m-panel { display: none; padding: 28px 28px 40px; }
        .m-panel.active { display: block; }

        /* ── Stats row ── */
        .m-stats {
            display: grid; grid-template-columns: repeat(4, 1fr);
            gap: 1px; background: var(--m-sand);
            border: 1px solid var(--m-sand); border-radius: 14px;
            overflow: hidden; margin-bottom: 24px;
        }
        .m-stat {
            background: var(--m-mist);
            padding: 15px 10px;
            display: flex; flex-direction: column;
            align-items: center; gap: 5px; text-align: center;
        }
        .m-stat .material-icons-outlined { font-size: 17px; color: var(--m-sage); }
        .m-stat-label {
            font-size: 10px; font-weight: 500;
            letter-spacing: 0.08em; text-transform: uppercase; color: #999;
        }
        .m-stat-value {
            font-family: 'Cormorant Garamond', serif;
            font-size: 1.1rem; font-weight: 600;
            color: var(--m-ink); line-height: 1;
        }

        /* Description */
        .m-desc {
            font-size: 14px; line-height: 1.8; color: #555;
            margin-bottom: 24px;
            border-left: 2px solid var(--m-mint);
            padding-left: 16px;
        }

        /* Section heading */
        .m-section-heading {
            font-family: 'DM Sans', sans-serif;
            font-size: 10.5px; font-weight: 600;
            color: var(--m-forest); letter-spacing: 0.14em;
            text-transform: uppercase; margin-bottom: 12px;
            display: flex; align-items: center; gap: 8px;
        }
        .m-section-heading::after {
            content: ''; flex: 1; height: 1px; background: var(--m-sand);
        }

        /* Highlight bar */
        .m-highlight {
            display: flex; align-items: center; gap: 10px;
            padding: 13px 18px;
            background: linear-gradient(135deg, var(--m-forest) 0%, var(--m-sage) 100%);
            border-radius: 14px; margin-bottom: 24px;
        }
        .m-highlight .material-icons-outlined { color: var(--m-mint); font-size: 17px; }
        .m-highlight-text { font-size: 13px; color: rgba(255,255,255,0.85); flex: 1; font-family: 'DM Sans', sans-serif; }
        .m-highlight-badge {
            background: rgba(255,255,255,0.15); color: #fff;
            font-size: 11px; font-weight: 600;
            padding: 4px 10px; border-radius: 100px;
            border: 1px solid rgba(255,255,255,0.22);
            white-space: nowrap; font-family: 'DM Sans', sans-serif;
        }

        /* Expect chips */
        .m-expect-grid {
            display: grid; grid-template-columns: 1fr 1fr;
            gap: 8px; margin-bottom: 24px;
        }
        .m-expect-chip {
            display: flex; align-items: flex-start; gap: 10px;
            padding: 11px 13px;
            background: var(--m-mist); border-radius: 12px;
            font-size: 12.5px; color: var(--m-ink); line-height: 1.45;
            border: 1px solid var(--m-sand);
            font-family: 'DM Sans', sans-serif;
        }
        .m-expect-chip-icon { font-size: 15px; flex-shrink: 0; margin-top: 1px; }

        /* Guides list */
        .m-guides { display: flex; flex-direction: column; gap: 7px; margin-bottom: 24px; }
        .m-guide-row {
            display: flex; align-items: center; gap: 13px;
            padding: 13px 15px;
            border: 1px solid var(--m-sand); border-radius: 14px;
            transition: border-color 0.2s, background 0.2s;
            cursor: pointer;
        }
        .m-guide-row:hover { border-color: var(--m-mint); background: var(--m-mist); }
        .m-guide-avatar {
            width: 37px; height: 37px; border-radius: 50%;
            background: linear-gradient(135deg, var(--m-sage), var(--m-forest));
            display: flex; align-items: center; justify-content: center;
            color: #fff;
            font-family: 'Cormorant Garamond', serif;
            font-size: 16px; font-weight: 600; flex-shrink: 0;
        }
        .m-guide-info { flex: 1; }
        .m-guide-name { font-size: 13px; font-weight: 500; color: var(--m-ink); font-family: 'DM Sans', sans-serif; }
        .m-guide-spec { font-size: 11.5px; color: #999; margin-top: 1px; font-family: 'DM Sans', sans-serif; }
        .m-guide-rating { display: flex; align-items: center; gap: 3px; }
        .m-guide-rating .material-icons-outlined { font-size: 13px; color: var(--m-gold); }
        .m-guide-rating span { font-size: 12px; color: #777; font-family: 'DM Sans', sans-serif; }
        .m-guide-arrow .material-icons-outlined { font-size: 18px; color: #ccc; transition: color 0.2s; }
        .m-guide-row:hover .m-guide-arrow .material-icons-outlined { color: var(--m-sage); }

        /* Action buttons */
        .m-actions { display: flex; gap: 10px; margin-top: 8px; }
        .m-btn-primary {
            flex: 1; display: flex; align-items: center; justify-content: center; gap: 8px;
            padding: 14px 22px;
            background: var(--m-forest); color: #fff;
            border: none; border-radius: 12px;
            font-family: 'DM Sans', sans-serif;
            font-size: 13px; font-weight: 500; letter-spacing: 0.04em;
            cursor: pointer; transition: background 0.2s, transform 0.15s;
        }
        .m-btn-primary:hover { background: var(--m-sage); transform: translateY(-1px); }
        .m-btn-primary .material-icons-outlined { font-size: 16px; }
        .m-btn-ghost {
            display: flex; align-items: center; justify-content: center; gap: 7px;
            padding: 14px 22px;
            background: transparent; color: var(--m-ink);
            border: 1px solid var(--m-sand); border-radius: 12px;
            font-family: 'DM Sans', sans-serif;
            font-size: 13px; font-weight: 500;
            cursor: pointer; transition: border-color 0.2s, background 0.2s, transform 0.15s, color 0.2s;
            white-space: nowrap;
        }
        .m-btn-ghost:hover { border-color: var(--m-warm); background: var(--m-mist); transform: translateY(-1px); }
        .m-btn-ghost .material-icons-outlined { font-size: 16px; transition: color 0.2s; }
        .m-btn-ghost.saved { border-color: #e53e3e; color: #e53e3e; background: #fff5f5; }
        .m-btn-ghost.saved .material-icons-outlined { color: #e53e3e; }

        /* ── Culture Tab ── */
        .m-culture-intro {
            font-family: 'DM Sans', sans-serif;
            font-size: 13.5px; color: #888; margin-bottom: 20px; line-height: 1.65;
        }
        .m-culture-card {
            border: 1px solid var(--m-sand); border-radius: 16px;
            overflow: hidden; margin-bottom: 10px;
            transition: box-shadow 0.2s;
        }
        .m-culture-card:hover { box-shadow: 0 4px 20px rgba(0,0,0,0.07); }
        .m-culture-top {
            display: flex; align-items: center; gap: 14px;
            padding: 17px 20px 12px;
        }
        .m-culture-icon {
            width: 42px; height: 42px; border-radius: 12px;
            background: var(--m-mist); border: 1px solid var(--m-sand);
            display: flex; align-items: center; justify-content: center;
            font-size: 20px; flex-shrink: 0;
        }
        .m-culture-sublabel {
            font-family: 'DM Sans', sans-serif;
            font-size: 10px; font-weight: 500; letter-spacing: 0.1em;
            text-transform: uppercase; color: var(--m-sage); margin-bottom: 3px;
        }
        .m-culture-title {
            font-family: 'Cormorant Garamond', serif;
            font-size: 1.1rem; font-weight: 600; color: var(--m-ink);
        }
        .m-culture-desc {
            font-family: 'DM Sans', sans-serif;
            font-size: 13px; line-height: 1.75; color: #666;
            padding: 0 20px 14px;
        }
        .m-culture-tags {
            display: flex; flex-wrap: wrap; gap: 6px;
            padding: 0 20px 16px;
        }
        .m-culture-tag {
            font-family: 'DM Sans', sans-serif;
            font-size: 11px; font-weight: 500;
            color: var(--m-forest); background: var(--m-mist);
            border: 1px solid var(--m-mint);
            padding: 3px 10px; border-radius: 100px;
        }
        .m-culture-link {
            display: flex; align-items: center; gap: 5px;
            padding: 11px 20px;
            border-top: 1px solid var(--m-sand);
            font-family: 'DM Sans', sans-serif;
            font-size: 12px; font-weight: 500; color: var(--m-sage);
            text-decoration: none; letter-spacing: 0.02em;
            transition: color 0.2s, gap 0.2s;
        }
        .m-culture-link:hover { color: var(--m-forest); gap: 8px; }
        .m-culture-link .material-icons-outlined { font-size: 14px; }

        /* ── Responsive ── */
        @media (max-width: 1024px) {
            .page-inner { padding: 36px 32px 60px; }
            .hero-section { padding: 0 40px 52px; height: 420px; }
            .hero-section h1 { font-size: 3rem; }
        }
        @media (max-width: 768px) {
            .main-content.full-width .main-header { padding: 0 20px; height: auto; min-height: 60px; flex-direction: column; gap: 0; align-items: stretch; }
            .header-left { padding: 12px 0 0; justify-content: space-between; }
            .header-right { padding: 4px 0 8px; justify-content: center; overflow-x: auto; }
            .header-nav { gap: 0; }
            .nav-link { padding: 8px 10px; font-size: 11.5px; height: auto; border-bottom: none; }
            .nav-link span:not(.material-icons-outlined) { display: none; }
            .hero-section { height: 340px; padding: 0 24px 40px; background-attachment: scroll; }
            .hero-section h1 { font-size: 2.2rem; }
            .hero-section p { font-size: 0.95rem; }
            .hero-pills { flex-wrap: wrap; }
            .page-inner { padding: 24px 20px 48px; }
            .travelry-grid { grid-template-columns: 1fr; gap: 20px; }
            /* modal responsive */
            #touristSpotModal { padding: 12px; }
            .m-hero { height: 210px; }
            .m-hero-name { font-size: 1.75rem; }
            .m-stats { grid-template-columns: repeat(2, 1fr); }
            .m-expect-grid { grid-template-columns: 1fr; }
            .m-panel { padding: 20px 18px 36px; }
            .m-tabs { padding: 0 16px; }
            .m-tab { padding: 14px 12px 12px; font-size: 10.5px; }
            .m-actions { flex-direction: column; }
        }
        @media (max-width: 480px) {
            .hero-section h1 { font-size: 1.8rem; }
            .m-hero { height: 180px; }
            .m-hero-name { font-size: 1.5rem; }
        }

        /* ══════════════════════════════════════════════
           WEATHER + CALENDAR (hide originals, keep data)
        ══════════════════════════════════════════════ */
        .weather-widget, .calendar-header { display: none; }
    </style>
</head>
<body>
    <main class="main-content full-width">
        <header class="main-header">
            <div class="header-left">
                <div class="logo" style="display:flex;align-items:center;gap:12px;margin-right:30px;">
                    <img src="../lgo.png" alt="SJDM Tours Logo" style="height:40px;width:40px;object-fit:contain;border-radius:8px;">
                    <span style="font-family:'Inter',sans-serif;font-weight:700;font-size:20px;color:var(--primary);">SJDM TOURS</span>
                </div>
                <h1>Tourist Spots</h1>
            </div>
            <div class="header-right">
                <nav class="header-nav">
                    <a href="user-index.php" class="nav-link"><span class="material-icons-outlined">dashboard</span><span>Dashboard</span></a>
                    <a href="user-guides-page.php" class="nav-link"><span class="material-icons-outlined">people</span><span>Tour Guides</span></a>
                    <a href="user-book.php" class="nav-link"><span class="material-icons-outlined">event</span><span>Book Now</span></a>
                    <a href="user-booking-history.php" class="nav-link"><span class="material-icons-outlined">history</span><span>Booking History</span></a>
                    <a href="user-tourist-spots.php" class="nav-link active"><span class="material-icons-outlined">place</span><span>Tourist Spots</span></a>
                    <a href="user-travel-tips.php" class="nav-link"><span class="material-icons-outlined">tips_and_updates</span><span>Travel Tips</span></a>
                </nav>
                <div class="header-actions">
                    <?php if ($isLoggedIn): ?>
                    <div class="user-profile-dropdown">
                        <button class="profile-trigger">
                            <div class="profile-avatar"><?php echo isset($currentUser['name']) ? strtoupper(substr($currentUser['name'],0,1)) : 'U'; ?></div>
                            <span class="profile-name"><?php echo htmlspecialchars($currentUser['name']); ?></span>
                            <span class="material-icons-outlined">expand_more</span>
                        </button>
                        <div class="dropdown-menu">
                            <div class="dropdown-header">
                                <div class="profile-avatar large"><?php echo isset($currentUser['name']) ? strtoupper(substr($currentUser['name'],0,1)) : 'U'; ?></div>
                                <div class="profile-details">
                                    <h4><?php echo htmlspecialchars($currentUser['name']); ?></h4>
                                    <p><?php echo htmlspecialchars($currentUser['email']); ?></p>
                                </div>
                            </div>
                            <div class="dropdown-divider"></div>
                            <a href="user-index.php" class="dropdown-item"><span class="material-icons-outlined">dashboard</span><span>Dashboard</span></a>
                            <a href="user-booking-history.php" class="dropdown-item"><span class="material-icons-outlined">history</span><span>Booking History</span></a>
                            <a href="user-saved-spots.php" class="dropdown-item"><span class="material-icons-outlined">favorite</span><span>Saved Spots</span></a>
                            <a href="user-saved-tours.php" class="dropdown-item"><span class="material-icons-outlined">favorite</span><span>Saved Tours</span></a>
                            <a href="#" class="dropdown-item" onclick="openPreferencesModal(); return false;"><span class="material-icons-outlined">tune</span><span>Preferences</span></a>
                            <div class="dropdown-divider"></div>
                            <a href="user-logout.php" class="dropdown-item"><span class="material-icons-outlined">logout</span><span>Sign Out</span></a>
                        </div>
                    </div>
                    <?php else: ?>
                    <button class="btn-signin" onclick="window.location.href='../log-in/log-in.php'">Sign in/register</button>
                    <?php endif; ?>
                </div>
            </div>
        </header>

        <div class="content-area">
            <div class="hero-section">
                <h1>Discover SJDM's<br><em>Hidden Gems</em></h1>
                <p>Explore the breathtaking tourist spots and natural wonders of San Jose del Monte, Bulacan</p>
                <div class="hero-pills">
                    <div class="hero-pill">
                        <span class="material-icons-outlined">wb_sunny</span>
                        <?php echo $currentTemp; ?>°C · <?php echo htmlspecialchars($weatherLabel); ?>
                    </div>
                    <div class="hero-pill">
                        <span class="material-icons-outlined">calendar_today</span>
                        <?php echo htmlspecialchars($currentWeekday . ', ' . $currentDate); ?>
                    </div>
                    <div class="hero-pill">
                        <span class="material-icons-outlined">place</span>
                        San Jose del Monte, Bulacan
                    </div>
                </div>
            </div>

            <div class="page-inner">

            <?php if (!empty($userPreferences)): ?>
            <div class="ai-preferences-display" style="background:linear-gradient(135deg,rgba(44,95,45,0.05),rgba(44,95,45,0.02));border:1px solid rgba(44,95,45,0.1);border-radius:12px;padding:20px;margin-bottom:24px;">
                <h3 style="color:#2c5f2d;margin-bottom:12px;display:flex;align-items:center;gap:8px;">
                    <span class="material-icons-outlined" style="color:#2c5f2d;">psychology</span> 🤖 Your Selected Interests
                </h3>
                <div style="display:flex;flex-wrap:wrap;gap:8px;">
                    <?php
                    $categoryMap = ['nature'=>'Nature & Waterfalls','farm'=>'Farms & Eco-Tourism','park'=>'Parks & Recreation','adventure'=>'Adventure & Activities','cultural'=>'Cultural & Historical','religious'=>'Religious Sites','entertainment'=>'Entertainment & Leisure','food'=>'Food & Dining','shopping'=>'Shopping & Markets','wellness'=>'Wellness & Relaxation','education'=>'Educational & Learning','family'=>'Family-Friendly','photography'=>'Photography Spots','wildlife'=>'Wildlife & Nature','outdoor'=>'Outdoor Activities'];
                    $iconMap = ['nature'=>'forest','farm'=>'agriculture','park'=>'park','adventure'=>'hiking','cultural'=>'museum','religious'=>'church','entertainment'=>'sports_esports','food'=>'restaurant','shopping'=>'shopping_cart','wellness'=>'spa','education'=>'school','family'=>'family_restroom','photography'=>'photo_camera','wildlife'=>'pets','outdoor'=>'terrain'];
                    foreach ($userPreferences as $preference): ?>
                        <div style="background:rgba(44,95,45,0.1);color:#2c5f2d;padding:8px 12px;border-radius:20px;font-size:12px;font-weight:600;display:flex;align-items:center;gap:6px;">
                            <span class="material-icons-outlined" style="font-size:14px;"><?php echo $iconMap[$preference] ?? 'category'; ?></span>
                            <?php echo htmlspecialchars($categoryMap[$preference] ?? $preference); ?>
                        </div>
                    <?php endforeach; ?>
                </div>
                <p style="color:#2c5f2d;font-size:14px;margin-top:12px;font-style:italic;">🎯 <strong>AI-Powered Results:</strong> Tourist spots below are filtered based on your selected interests!</p>
            </div>
            <?php endif; ?>

            <div class="travelry-filters">
                <div style="display:flex;align-items:baseline;justify-content:space-between;margin-bottom:20px;flex-wrap:wrap;gap:12px;">
                    <div class="section-title">
                        <span>Destinations</span>
                        Browse All Spots
                    </div>
                </div>
                <div class="filter-row">
                    <div class="filter-group">
                        <label>Category</label>
                        <select class="filter-select" id="categoryFilter">
                            <option value="all">All Categories</option>
                            <?php
                            $conn = getDatabaseConnection();
                            if ($conn) {
                                $result = $conn->query("SELECT DISTINCT category FROM tourist_spots WHERE status = 'active'");
                                if ($result && $result->num_rows > 0) {
                                    while ($cat = $result->fetch_assoc()) {
                                        echo '<option value="' . $cat['category'] . '">' . ucfirst($cat['category']) . '</option>';
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

            <div class="travelry-grid" id="spotsGrid">
                <?php
                $conn = getDatabaseConnection();
                if ($conn) {
                    if (!empty($userPreferences)) {
                        $placeholders = rtrim(str_repeat('?,', count($userPreferences)), ',');
                        $query = "SELECT ts.*, GROUP_CONCAT(DISTINCT CONCAT(tg.id,':',tg.name,':',tg.specialty,':',tg.rating,':',tg.verified) ORDER BY tg.rating DESC SEPARATOR '|') as guides_info FROM tourist_spots ts LEFT JOIN guide_destinations gd ON ts.id=gd.destination_id LEFT JOIN tour_guides tg ON gd.guide_id=tg.id AND tg.status='active' WHERE ts.status='active' AND ts.category IN ($placeholders) GROUP BY ts.id ORDER BY ts.name";
                        $stmt = $conn->prepare($query);
                        $stmt->bind_param(str_repeat('s', count($userPreferences)), ...$userPreferences);
                        $stmt->execute();
                        $result = $stmt->get_result();
                    } else {
                        $query = "SELECT ts.*, GROUP_CONCAT(DISTINCT CONCAT(tg.id,':',tg.name,':',tg.specialty,':',tg.rating,':',tg.verified) ORDER BY tg.rating DESC SEPARATOR '|') as guides_info FROM tourist_spots ts LEFT JOIN guide_destinations gd ON ts.id=gd.destination_id LEFT JOIN tour_guides tg ON gd.guide_id=tg.id AND tg.status='active' WHERE ts.status='active' GROUP BY ts.id ORDER BY ts.name";
                        $result = $conn->query($query);
                    }

                    if ($result && $result->num_rows > 0) {
                        while ($spot = $result->fetch_assoc()) {
                            $categoryMap = ['nature'=>'Nature & Waterfalls','farm'=>'Farms & Eco-Tourism','park'=>'Parks & Recreation','religious'=>'Religious Sites','urban'=>'Urban Landmarks','historical'=>'Historical Sites','waterfalls'=>'Waterfalls','mountains'=>'Mountains & Hiking','agri-tourism'=>'Agri-Tourism','religious sites'=>'Religious Sites','parks & recreation'=>'Parks & Recreation','tourist spot'=>'Tourist Spots'];
                            $iconMap = ['nature'=>'landscape','farm'=>'agriculture','park'=>'park','religious'=>'church','urban'=>'location_city','historical'=>'account_balance','waterfalls'=>'water','mountains'=>'terrain','agri-tourism'=>'agriculture','religious sites'=>'church','parks & recreation'=>'park','tourist spot'=>'place'];
                            $badgeMap = ['nature'=>'Nature','farm'=>'Farm','park'=>'Park','religious'=>'Religious','urban'=>'Urban','historical'=>'Historical','waterfalls'=>'Waterfalls','mountains'=>'Mountain','agri-tourism'=>'Farm','religious sites'=>'Religious','parks & recreation'=>'Park','tourist spot'=>'Tourist'];

                            $category = $spot['category'];
                            $displayCategory = $categoryMap[$category] ?? $category;
                            $icon = $iconMap[$category] ?? 'place';
                            $badge = $badgeMap[$category] ?? $category;

                            $rating = floatval($spot['rating']);
                            $fullStars = floor($rating);
                            $hasHalfStar = ($rating - $fullStars) >= 0.5;
                            $starsHtml = '';
                            for ($i = 0; $i < $fullStars; $i++) $starsHtml .= '<span class="material-icons-outlined" style="color:#ffc107;font-size:16px;">star</span>';
                            if ($hasHalfStar) $starsHtml .= '<span class="material-icons-outlined" style="color:#ffc107;font-size:16px;">star_half</span>';
                            $emptyStars = 5 - $fullStars - ($hasHalfStar ? 1 : 0);
                            for ($i = 0; $i < $emptyStars; $i++) $starsHtml .= '<span class="material-icons-outlined" style="color:#ddd;font-size:16px;">star_outline</span>';

                            $activityLevel = $spot['difficulty_level'];
                            $duration = $spot['duration'] ?? '2-3 hours';

                            $guides = [];
                            if (!empty($spot['guides_info'])) {
                                foreach (explode('|', $spot['guides_info']) as $guide) {
                                    $p = explode(':', $guide);
                                    if (count($p) >= 5) $guides[] = ['id'=>$p[0],'name'=>$p[1],'specialty'=>$p[2],'rating'=>$p[3],'verified'=>$p[4]];
                                }
                            }

                            $imageUrl = !empty($spot['image_url']) ? $spot['image_url'] : '';
                            $imageAlt = htmlspecialchars($spot['name']);

                            if ($category === 'nature' || $category === 'waterfalls' || $category === 'mountains') {
                                $features = ['🌿 Lush forest trails','📸 Scenic photo spots','🦋 Wildlife sightings','💧 Crystal clear waters','🌅 Stunning viewpoints'];
                            } elseif ($category === 'farm' || $category === 'agri-tourism') {
                                $features = ['🌾 Hands-on farm activities','🥬 Fresh produce picking','🐄 Meet farm animals','🍓 Organic food tasting','📚 Learn agri practices'];
                            } elseif ($category === 'park' || $category === 'parks & recreation') {
                                $features = ['⚽ Sports facilities','🎠 Kids playground','🧺 Picnic areas','🚶 Walking trails','🎉 Community events'];
                            } elseif ($category === 'religious' || $category === 'religious sites') {
                                $features = ['⛪ Historic architecture','🕯️ Peaceful atmosphere','🎨 Sacred artwork','📖 Cultural heritage','🌸 Scenic grounds'];
                            } elseif ($category === 'urban') {
                                $features = ['🏙️ City landmarks','🛍️ Local shopping','🍜 Street food scene','🎭 Arts & culture','🚌 Easy to access'];
                            } elseif ($category === 'historical') {
                                $features = ['🏛️ Historic landmarks','🗿 Cultural exhibits','📜 Guided history tours','🎓 Educational stops','📷 Iconic photo ops'];
                            } else {
                                $features = ['🌟 Unique attractions','📸 Great photo spots','👨‍👩‍👧 Family-friendly','🗺️ Easy to explore','✨ Memorable experience'];
                            }

                            if ($category === 'nature' || $category === 'waterfalls' || $category === 'mountains') {
                                $bestTime = '<span>Best visited <span class="attract-highlight">early morning or late afternoon</span></span>';
                            } elseif ($category === 'farm' || $category === 'agri-tourism') {
                                $bestTime = '<span>Best during <span class="attract-highlight">harvest season</span></span>';
                            } elseif ($category === 'religious' || $category === 'religious sites') {
                                $bestTime = '<span>Best during <span class="attract-highlight">early morning mass</span></span>';
                            } elseif ($category === 'park' || $category === 'parks & recreation') {
                                $bestTime = '<span>Best on <span class="attract-highlight">weekday mornings</span></span>';
                            } elseif ($category === 'historical') {
                                $bestTime = '<span>Best visited <span class="attract-highlight">anytime — guided tours available</span></span>';
                            } else {
                                $bestTime = '<span>Best visited <span class="attract-highlight">in the morning</span></span>';
                            }

                            $fee = $spot['entrance_fee'] ?? 'Free';
                            $feeDisplay = strtolower($fee) === 'free'
                                ? '<span>Entrance Fee <span class="attract-highlight">🎉 Free</span></span>'
                                : '<span>Entrance Fee <span class="attract-highlight">🎟️ ' . htmlspecialchars($fee) . '</span></span>';

                            echo '<div class="travelry-card" data-category="' . $category . '" data-activity="' . $activityLevel . '" data-duration="' . $duration . '">';
                            echo '<div class="card-image">';
                            if (!empty($imageUrl)) {
                                echo '<img src="' . htmlspecialchars($imageUrl) . '" alt="' . $imageAlt . '" loading="lazy" onerror="this.onerror=null;this.src=\'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDAwIiBoZWlnaHQ9IjIwMCIgdmlld0JveD0iMCAwIDQwMCAyMDAiIGZpbGw9Im5vbmUiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+CjxyZWN0IHdpZHRoPSI0MDAiIGhlaWdodD0iMjAwIiBmaWxsPSIjRjVGN0YiLz4KPHN2ZyB3aWR0aD0iNjAiIGhlaWdodD0iNjAiIHZpZXdCb3g9IjAgMCA2MCA2MCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4PSIxNzAiIHk9IjcwIj4KPGNpcmNsZSBjeD0iMzAiIGN5PSIzMCIgcj0iMjAiIGZpbGw9IiM5Q0EzQUYiLz4KPHN2Zz4KPHN2Zz4K\';">';
                            } else {
                                $gradients = ['nature'=>'#22c55e,#16a34a','farm'=>'#84cc16,#65a30d','park'=>'#06b6d4,#0891b2','religious'=>'#8b5cf6,#7c3aed','urban'=>'#f59e0b,#d97706','historical'=>'#6b7280,#4b5563'];
                                $grad = $gradients[$category] ?? '#4a7c4e,#2c5f2d';
                                echo '<div class="card-image-placeholder" style="background:linear-gradient(135deg,' . $grad . ');">';
                                echo '<div style="position:absolute;bottom:10px;left:10px;right:10px;text-align:center;"><span class="material-icons-outlined" style="color:white;font-size:24px;">' . $icon . '</span></div>';
                                echo '</div>';
                            }
                            echo '<div class="card-badge"><span class="material-icons-outlined">' . $icon . '</span>' . $badge . '</div>';
                            echo '<div class="card-labels">';
                            if ($spot['rating'] >= 4.5) echo '<div class="interest-label popular"><span class="material-icons-outlined">trending_up</span><span>Popular</span></div>';
                            if ($spot['difficulty_level'] === 'easy') echo '<div class="interest-label family-friendly"><span class="material-icons-outlined">family_restroom</span><span>Family Friendly</span></div>';
                            if ($spot['difficulty_level'] === 'hard' || $spot['difficulty_level'] === 'moderate') echo '<div class="interest-label adventure"><span class="material-icons-outlined">hiking</span><span>Adventure</span></div>';
                            $labelClass = in_array($category, ['nature','waterfalls','mountains']) ? 'nature' : (in_array($category, ['farm','agri-tourism']) ? 'farm' : (in_array($category, ['park','parks & recreation']) ? 'park' : (in_array($category, ['religious','religious sites']) ? 'religious' : ($category === 'urban' ? 'urban' : ($category === 'historical' ? 'historical' : 'default')))));
                            $labelIcon = ['nature'=>'forest','farm'=>'agriculture','park'=>'park','religious'=>'church','urban'=>'location_city','historical'=>'account_balance','default'=>'place'][$labelClass] ?? 'place';
                            $labelText = ucfirst($labelClass === 'default' ? 'Destination' : $labelClass);
                            echo '<div class="interest-label ' . $labelClass . '"><span class="material-icons-outlined">' . $labelIcon . '</span><span>' . $labelText . '</span></div>';
                            echo '</div></div>';

                            echo '<div class="card-content">';
                            $wIcon = $weatherLabel === 'Clear' ? 'wb_sunny' : ($weatherLabel === 'Clouds' ? 'cloud' : 'wb_cloudy');
                            echo '<div class="card-weather"><span class="material-icons-outlined">' . $wIcon . '</span><span class="weather-temp">' . $currentTemp . '°C</span><span class="weather-desc">' . htmlspecialchars($weatherLabel) . '</span></div>';
                            echo '<h3 class="card-title">' . htmlspecialchars($spot['name']) . '</h3>';
                            echo '<span class="card-category">' . htmlspecialchars($displayCategory) . '</span>';
                            echo '<div class="card-stats">';
                            echo '<div class="stat-item"><span class="stat-label">Rating</span><div style="display:flex;align-items:center;gap:4px;">' . $starsHtml . '<span style="font-size:12px;color:#666;">(' . $spot['review_count'] . ')</span></div></div>';
                            echo '<div class="stat-item"><span class="stat-label">Difficulty</span><span class="stat-value">' . ucfirst($spot['difficulty_level']) . '</span></div>';
                            echo '<div class="stat-item"><span class="stat-label">Entrance</span><span class="stat-value">' . htmlspecialchars($spot['entrance_fee'] ?? 'Free') . '</span></div>';
                            echo '</div>';

                            echo '<div class="card-features-text"><div class="features-label">What to Expect</div><div class="features-list">';
                            foreach ($features as $feature) echo '<div class="feature-item">' . $feature . '</div>';
                            echo '</div></div>';

                            echo '<div class="card-attract">';
                            echo '<div class="attract-row"><span class="material-icons-outlined">location_on</span><span>' . htmlspecialchars($spot['location'] ?? 'San Jose del Monte, Bulacan') . '</span></div>';
                            echo '<div class="attract-row"><span class="material-icons-outlined">local_activity</span>' . $feeDisplay . '</div>';
                            echo '<div class="attract-row"><span class="material-icons-outlined">wb_sunny</span>' . $bestTime . '</div>';
                            echo '</div>';

                            echo '<div class="card-buttons">';
                            echo '<button class="card-button" onclick="showTouristSpotModal(\'' .
                                 addslashes($spot['name']) . '\', \'' .
                                 addslashes($displayCategory) . '\', \'' .
                                 addslashes($spot['image_url'] ?? '') . '\', \'' .
                                 $icon . '\', \'' .
                                 $badge . '\', \'' .
                                 $currentTemp . '°C\', \'' .
                                 ($spot['elevation'] ?? '200') . ' MASL\', \'' .
                                 ucfirst($spot['difficulty_level']) . '\', \'' .
                                 ($spot['duration'] ?? '2-3 hours') . '\', \'' .
                                 htmlspecialchars(json_encode($guides), ENT_QUOTES, 'UTF-8') . '\', \'' .
                                 addslashes($category) . '\', \'' .
                                 addslashes($spot['entrance_fee'] ?? 'Free') . '\')">';
                            echo 'View Details</button>';
                            echo '</div>';
                            echo '</div>';
                            echo '</div>';
                        }
                    } else {
                        echo '<div class="no-results" style="grid-column:1/-1;text-align:center;padding:60px 20px;"><span class="material-icons-outlined" style="font-size:48px;color:#9ca3af;">place</span><h3 style="color:#6b7280;margin-top:16px;">No tourist spots found</h3><p style="color:#9ca3af;">Please check back later.</p></div>';
                    }
                    closeDatabaseConnection($conn);
                } else {
                    echo '<div class="error-message" style="grid-column:1/-1;text-align:center;padding:60px 20px;"><span class="material-icons-outlined" style="font-size:48px;color:#ef4444;">error</span><h3 style="color:#ef4444;margin-top:16px;">Database Connection Error</h3></div>';
                }
                ?>
            </div><!-- end spotsGrid -->

            </div><!-- end page-inner -->
        </div><!-- end content-area -->
    </main>

    <!-- ══════════════════════════════════════════════
         REDESIGNED TOURIST SPOT MODAL
    ══════════════════════════════════════════════ -->
    <div id="touristSpotModal">
        <div class="m-shell">

            <!-- Hero image -->
            <div class="m-hero">
                <img id="mHeroImg" src="" alt="">
                <div class="m-hero-overlay"></div>
                <div class="m-hero-cat" id="mHeroCat">
                    <span class="material-icons-outlined" id="mHeroCatIcon">place</span>
                    <span id="mHeroCatText">Category</span>
                </div>
                <button class="m-close" onclick="closeTouristSpotModal()">
                    <span class="material-icons-outlined">close</span>
                </button>
                <div class="m-hero-meta">
                    <div class="m-hero-name" id="mHeroName">Spot Name</div>
                    <div class="m-hero-loc">
                        <span class="material-icons-outlined">location_on</span>
                        San Jose del Monte, Bulacan
                    </div>
                </div>
            </div>

            <!-- Tab bar -->
            <div class="m-tabs">
                <button class="m-tab active" onclick="switchModalTab('overview', this)">
                    <span class="material-icons-outlined">explore</span> Overview
                </button>
                <button class="m-tab" onclick="switchModalTab('culture', this)">
                    <span class="material-icons-outlined">theater_comedy</span> Local Culture
                </button>
            </div>

            <!-- Scrollable body -->
            <div class="m-body">

                <!-- OVERVIEW PANEL -->
                <div class="m-panel active" id="tab-overview">

                    <!-- 4-stat row -->
                    <div class="m-stats">
                        <div class="m-stat">
                            <span class="material-icons-outlined">thermostat</span>
                            <div class="m-stat-label">Temp</div>
                            <div class="m-stat-value" id="mStatTemp">28°C</div>
                        </div>
                        <div class="m-stat">
                            <span class="material-icons-outlined">terrain</span>
                            <div class="m-stat-label">Elevation</div>
                            <div class="m-stat-value" id="mStatElev">200 MASL</div>
                        </div>
                        <div class="m-stat">
                            <span class="material-icons-outlined">signal_cellular_alt</span>
                            <div class="m-stat-label">Difficulty</div>
                            <div class="m-stat-value" id="mStatDiff">Moderate</div>
                        </div>
                        <div class="m-stat">
                            <span class="material-icons-outlined">schedule</span>
                            <div class="m-stat-label">Duration</div>
                            <div class="m-stat-value" id="mStatDur">2–3 hrs</div>
                        </div>
                    </div>

                    <!-- Description -->
                    <p class="m-desc" id="mDesc">Description loading...</p>

                    <!-- Best time highlight -->
                    <div class="m-highlight" id="mHighlight">
                        <span class="material-icons-outlined">wb_sunny</span>
                        <div class="m-highlight-text" id="mHighlightText">Best visited during early morning</div>
                        <div class="m-highlight-badge" id="mHighlightBadge">🌄 Tip</div>
                    </div>

                    <!-- What to Expect -->
                    <div class="m-section-heading">What to Expect</div>
                    <div class="m-expect-grid" id="mExpectGrid"></div>

                    <!-- Tour Guides -->
                    <div id="mGuidesWrap" style="display:none;">
                        <div class="m-section-heading">Available Guides</div>
                        <div class="m-guides" id="mGuidesList"></div>
                    </div>

                    <!-- Entrance fee -->
                    <div class="m-section-heading">Entrance Fee</div>
                    <div style="margin-bottom:20px;font-family:'DM Sans',sans-serif;font-size:14px;color:#444;" id="mFeeRow">Free</div>

                    <!-- Actions -->
                    <div class="m-actions">
                        <button class="m-btn-primary" onclick="viewAllDetails()">
                            <span class="material-icons-outlined">visibility</span>
                            View Full Details
                        </button>
                        <button class="m-btn-ghost" id="mSaveBtn" onclick="saveThisSpot()">
                            <span class="material-icons-outlined">favorite_border</span>
                            Save
                        </button>
                    </div>
                </div>

                <!-- CULTURE PANEL -->
                <div class="m-panel" id="tab-culture">
                    <p class="m-culture-intro">Discover the traditions, cuisine, and living heritage tied to this destination — where the community's story is as rich as the landscape itself.</p>
                    <div id="mCultureCards"></div>
                </div>

            </div><!-- end m-body -->
        </div><!-- end m-shell -->
    </div><!-- end modal -->

    <script>
    /* ── Globals ── */
    let _currentSpotName = '';

    /* ── Tab Switcher ── */
    function switchModalTab(tab, btn) {
        document.querySelectorAll('.m-tab').forEach(t => t.classList.remove('active'));
        document.querySelectorAll('.m-panel').forEach(p => p.classList.remove('active'));
        if (btn) btn.classList.add('active');
        else document.querySelector(`.m-tab[onclick*="${tab}"]`).classList.add('active');
        document.getElementById('tab-' + tab).classList.add('active');
    }

    /* ── Culture Data ── */
    const cultureData = {
        'nature': [
            { icon:'🌿', label:'Community Life', title:'Barangay Living Near Nature', desc:'Communities surrounding SJDM\'s natural sites practice sustainable living closely tied to the land. Residents rely on local forests and waterways, maintaining a simple, nature-connected lifestyle passed down through generations.', tags:['Eco Living','Local Traditions','Forest Communities'] },
            { icon:'🍚', label:'Local Cuisine', title:'Farm-Fresh & River Catches', desc:'Food near natural destinations features freshly harvested produce and river fish. Try sinigang na hito (catfish sour soup) and pinakbet made from vegetables grown in nearby community gardens.', tags:['Sinigang na Hito','Pinakbet','Native Kakanin'] }
        ],
        'waterfalls': [
            { icon:'🌿', label:'Community Life', title:'Barangay Living Near Nature', desc:'Communities surrounding SJDM\'s natural sites practice sustainable living closely tied to the land, maintaining traditions connected to the rivers and forests.', tags:['Eco Living','Local Traditions','Forest Communities'] },
            { icon:'🎵', label:'Music and Dance', title:'Harana & Folk Songs', desc:'Traditional harana (serenade) and folk songs are still practiced in barangays near waterfalls, often performed during community fiestas and courtship celebrations.', tags:['Harana','Folk Songs','Fiesta Music'] }
        ],
        'mountains': [
            { icon:'🌿', label:'Community Life', title:'Highland Barangay Culture', desc:'Mountain barangays in SJDM maintain strong community bonds. Bayanihan — collective labor and mutual aid — is alive and well, especially during planting and harvest seasons.', tags:['Bayanihan','Highland Life','Community Bonds'] },
            { icon:'🎨', label:'Arts and Crafts', title:'Woven Goods & Native Crafts', desc:'Communities near mountain trails produce handwoven baskets, banig (mats), and native accessories using local materials like rattan and bamboo — crafts passed down through families.', tags:['Basket Weaving','Banig','Bamboo Crafts'] }
        ],
        'farm': [
            { icon:'🍚', label:'Local Cuisine', title:'Farm-to-Table Bulacan Flavors', desc:'Farm destinations in SJDM serve authentic Bulacan cuisine made from freshly harvested ingredients. Signature dishes include puto (rice cake), ensaymada, and kare-kare made with produce grown on-site.', tags:['Puto Bulacan','Ensaymada','Kare-Kare','Fresh Produce'] },
            { icon:'🎉', label:'Traditional Festivals', title:'Harvest Festivals & Planting Rituals', desc:'Agricultural communities celebrate the planting and harvest seasons with thanksgiving rituals, folk dances, and communal feasts. The Pista ng Ani (Harvest Festival) brings together neighbors for prayer, music, and shared meals.', tags:['Pista ng Ani','Harvest Ritual','Folk Dance','Thanksgiving'] },
            { icon:'🌾', label:'Community Life', title:'Bayanihan in the Fields', desc:'The bayanihan spirit is most visible on farms — neighbors helping each other plant, harvest, and build. Visiting a farm destination means witnessing genuine Filipino communal cooperation.', tags:['Bayanihan','Farm Life','Community Work'] }
        ],
        'agri-tourism': [
            { icon:'🍚', label:'Local Cuisine', title:'Farm-to-Table Bulacan Flavors', desc:'Agri-tourism spots serve authentic Bulacan dishes made with freshly harvested ingredients — from puto to kare-kare — everything made with produce grown just meters away.', tags:['Puto','Kare-Kare','Fresh Vegetables','Native Sweets'] },
            { icon:'🎨', label:'Arts and Crafts', title:'Native Crafts from Farm Materials', desc:'Visitors can learn traditional crafts made from farm materials — bamboo lanterns, dried flower arrangements, and woven baskets — as part of hands-on farm experience programs.', tags:['Bamboo Crafts','Woven Baskets','Dried Flowers'] }
        ],
        'park': [
            { icon:'🎵', label:'Music and Dance', title:'Parks as Cultural Gathering Spaces', desc:'SJDM\'s public parks regularly host cultural events featuring folk dances like Tinikling and Cariñosa, as well as live performances by local musicians during town fiestas and city celebrations.', tags:['Tinikling','Cariñosa','Live Performances','City Fiestas'] },
            { icon:'🌺', label:'Community Life', title:'Parks as the Heart of the Community', desc:'Parks in SJDM serve as communal hubs where residents gather for morning exercise, Sunday picnics, and community events. They reflect the close-knit, family-oriented culture of Bulakeños.', tags:['Family Gatherings','Community Events','Sunday Traditions'] }
        ],
        'parks & recreation': [
            { icon:'🎵', label:'Music and Dance', title:'Parks as Cultural Gathering Spaces', desc:'SJDM\'s recreation parks regularly host cultural events featuring folk dances and live local music, especially during the city\'s founding anniversary and town fiestas.', tags:['Tinikling','Folk Dance','City Fiesta','Live Music'] },
            { icon:'🌺', label:'Community Life', title:'Communal Recreation Culture', desc:'Public parks reflect the warm, family-centered culture of SJDM — where neighbors become friends over weekend sports, picnics, and community-organized events.', tags:['Family Activities','Community Bonds','Weekend Gatherings'] }
        ],
        'religious': [
            { icon:'🎉', label:'Traditional Festivals', title:'Patron Saint Fiestas', desc:'Religious sites in SJDM come alive during the celebration of patron saint fiestas. Streets are decorated with colorful lanterns, and the community gathers for novena masses, processions, and cultural performances.', tags:['Town Fiesta','Procession','Novena','Patron Saint'] },
            { icon:'🏛️', label:'Historical Heritage', title:'Colonial-Era Religious Architecture', desc:'Many of SJDM\'s churches and chapels date back to the Spanish colonial era. Their baroque-influenced architecture, old altars, and centuries-old santos (religious icons) are living testaments to Filipino faith and heritage.', tags:['Spanish Colonial','Baroque Architecture','Santos','Heritage'] },
            { icon:'🌺', label:'Community Life', title:'Faith as Community Glue', desc:'In SJDM, religion is inseparable from community life. Sunday masses, weekly novenas, and religious processions bring neighbors together, reinforcing the strong Catholic faith that defines Bulakeño identity.', tags:['Sunday Mass','Novena','Catholic Traditions','Barangay Chapel'] }
        ],
        'religious sites': [
            { icon:'🎉', label:'Traditional Festivals', title:'Patron Saint Fiestas', desc:'Religious sites in SJDM come alive during patron saint fiestas — with novenas, grand processions, and street celebrations that draw the entire community together.', tags:['Town Fiesta','Procession','Patron Saint','Novena'] },
            { icon:'🏛️', label:'Historical Heritage', title:'Colonial-Era Sacred Spaces', desc:'Centuries-old churches and shrines carry stories of Filipino faith through Spanish colonization and beyond. Their architecture and religious artifacts are irreplaceable cultural treasures.', tags:['Spanish Colonial','Religious Heritage','Sacred Arts'] }
        ],
        'historical': [
            { icon:'🏛️', label:'Historical Heritage', title:'SJDM\'s Rich Historical Legacy', desc:'San Jose del Monte carries a rich history stretching back to pre-colonial times. Historical sites preserve stories of indigenous communities, Spanish colonial rule, and the Philippine Revolution — all woven into the identity of the city.', tags:['Pre-Colonial','Spanish Era','Philippine Revolution','Heritage Sites'] },
            { icon:'🎨', label:'Arts and Crafts', title:'Heritage Crafts & Traditional Arts', desc:'Local artisans preserve heritage crafts like pabalat (rice paper crafts), wood carving, and traditional parol (star lantern) making — skills passed down through generations.', tags:['Parol Making','Wood Carving','Pabalat','Traditional Arts'] },
            { icon:'🎵', label:'Music and Dance', title:'Kundiman & Patriotic Arts', desc:'Historical sites in SJDM often host cultural performances featuring kundiman (Filipino love songs) and patriotic folk dances that celebrate the city\'s revolutionary heritage and Filipino identity.', tags:['Kundiman','Patriotic Dance','Folk Performances'] }
        ],
        'urban': [
            { icon:'🍚', label:'Local Cuisine', title:'Street Food & Urban Bulacan Flavors', desc:'Urban areas of SJDM are food havens — from isaw and kwek-kwek street food stalls to Bulacan-style lechon and native kakanin sold in local markets.', tags:['Isaw','Kwek-Kwek','Lechon Bulacan','Kakanin','Street Food'] },
            { icon:'🎨', label:'Arts and Crafts', title:'Urban Art & Local Markets', desc:'SJDM\'s urban centers feature local artisans selling handmade goods in community markets — from painted santos to handwoven accessories.', tags:['Local Markets','Handmade Goods','Urban Crafts'] },
            { icon:'🎵', label:'Music and Dance', title:'Live Music & Urban Festivities', desc:'Urban SJDM buzzes with live music during city celebrations. Local bands, brass ensembles, and street performers add energy to public squares and commercial areas.', tags:['Live Bands','City Fiesta','Brass Ensemble','Street Performers'] }
        ]
    };

    function buildCultureCards(rawCategory) {
        const container = document.getElementById('mCultureCards');
        if (!container) return;
        const cards = cultureData[rawCategory] || [
            { icon:'🌺', label:'Community Life', title:'Warm Bulakeño Hospitality', desc:'SJDM communities are known for their warm, welcoming spirit. Visitors are often greeted with homemade food, friendly conversation, and an invitation to join local celebrations — a reflection of genuine Filipino hospitality.', tags:['Hospitality','Filipino Culture','Community Spirit'] },
            { icon:'🍚', label:'Local Cuisine', title:'Authentic Bulacan Flavors', desc:'Bulacan is renowned for its sweets and festive food. Look out for puto, ensaymada, pastillas de leche, and chicharon — beloved delicacies that define Bulakeño culinary identity.', tags:['Puto','Ensaymada','Pastillas','Chicharon'] }
        ];
        container.innerHTML = cards.map(c => `
            <div class="m-culture-card">
                <div class="m-culture-top">
                    <div class="m-culture-icon">${c.icon}</div>
                    <div>
                        <div class="m-culture-sublabel">${c.label}</div>
                        <div class="m-culture-title">${c.title}</div>
                    </div>
                </div>
                <div class="m-culture-desc">${c.desc}</div>
                <div class="m-culture-tags">${c.tags.map(t=>`<span class="m-culture-tag">${t}</span>`).join('')}</div>
            </div>
        `).join('');
    }

    /* ── Main show function ── */
    function showTouristSpotModal(name, category, image, icon, type, temp, elevation, difficulty, duration, guides, rawCategory, fee) {
        _currentSpotName = name;

        // Hero
        const img = document.getElementById('mHeroImg');
        if (img) { img.src = image || ''; img.alt = name; }
        document.getElementById('mHeroName').textContent = name;
        document.getElementById('mHeroCatText').textContent = category;
        document.getElementById('mHeroCatIcon').textContent = icon;

        // Stats
        document.getElementById('mStatTemp').textContent = temp;
        document.getElementById('mStatElev').textContent = elevation + (elevation.includes('MASL') ? '' : ' MASL');
        document.getElementById('mStatDiff').textContent = difficulty;
        document.getElementById('mStatDur').textContent = duration;

        // Entrance fee
        const feeEl = document.getElementById('mFeeRow');
        if (feeEl) {
            const feeStr = fee || 'Free';
            feeEl.innerHTML = feeStr.toLowerCase() === 'free'
                ? '<span style="color:#2c5f2d;font-weight:600;">🎉 Free Entrance</span>'
                : `<span style="font-weight:500;">🎟️ ${feeStr}</span>`;
        }

        // Description & features by type
        let description = '', features = [], highlightText = '', highlightBadge = '';
        const t = type.toLowerCase();

        if (t.includes('mountain') || rawCategory === 'mountains') {
            description = `Experience the breathtaking beauty of ${name}, one of San Jose del Monte's most majestic mountain peaks. Panoramic views, challenging trails, and rich biodiversity make this an unforgettable escape for nature enthusiasts.`;
            features = ['Challenging hiking trails with varying difficulty','Spectacular panoramic views of Bulacan province','Rich biodiversity and native flora & fauna','Perfect for sunrise and sunset photography','Camping spots available for overnight stays','Natural springs along the trail'];
            highlightText = 'Best visited during early morning for cooler trails and clearer skies';
            highlightBadge = '🌄 Sunrise View';
        } else if (t.includes('waterfall') || t.includes('falls') || rawCategory === 'waterfalls') {
            description = `Discover the natural wonder of ${name}, a hidden gem nestled in the lush landscapes of San Jose del Monte. Crystal-clear waters and serene forest surroundings offer a refreshing and memorable escape.`;
            features = ['Crystal-clear waters perfect for swimming','Natural pools for relaxation','Lush tropical surroundings','Ideal for nature photography','Accessible hiking trails with scenic views','Cool and refreshing microclimate'];
            highlightText = 'Visit during or after the rainy season for the most impressive water flow';
            highlightBadge = '💧 Peak Flow';
        } else if (t.includes('farm') || rawCategory === 'farm' || rawCategory === 'agri-tourism') {
            description = `Experience sustainable agriculture and rural life at ${name}, a charming eco-tourism destination in San Jose del Monte. This working farm offers hands-on experiences and educational opportunities for all ages.`;
            features = ['Organic farming practices & sustainable agriculture','Fresh produce sampling and farm-to-table experiences','Educational tours about farming techniques','Interactive activities for children and families','Scenic rural landscapes and peaceful atmosphere','Local artisan goods and native products'];
            highlightText = 'Visit during harvest season to experience the full farm-to-table journey';
            highlightBadge = '🌾 Harvest Season';
        } else if (t.includes('park') || rawCategory === 'park' || rawCategory === 'parks & recreation') {
            description = `Enjoy recreational activities and natural beauty at ${name}, a well-maintained public space in San Jose del Monte. Sports facilities, walking trails, and family-friendly amenities await visitors of all ages.`;
            features = ['Well-maintained sports facilities and equipment','Children\'s playground and family areas','Jogging and walking trails','Picnic areas with benches and tables','Regular community events and activities','Open green spaces for leisure'];
            highlightText = 'Weekday mornings are quietest — ideal for jogging and family picnics';
            highlightBadge = '🌳 Best Timing';
        } else if (t.includes('religious') || rawCategory === 'religious' || rawCategory === 'religious sites') {
            description = `Find spiritual solace and architectural beauty at ${name}, a sacred destination in San Jose del Monte. This revered site offers a peaceful atmosphere for prayer, reflection, and cultural appreciation.`;
            features = ['Beautiful religious architecture and artwork','Peaceful atmosphere for prayer and meditation','Cultural and historical significance','Well-maintained grounds and gardens','Regular religious services and community events','Significant pilgrimage destination'];
            highlightText = 'Early morning visits offer a peaceful, contemplative atmosphere';
            highlightBadge = '🕊️ Peaceful Hours';
        } else if (rawCategory === 'historical') {
            description = `Explore the rich history of ${name}, a significant heritage destination in San Jose del Monte. This historical site preserves stories of the city's past — from pre-colonial times through the Philippine Revolution.`;
            features = ['Historic landmarks and cultural exhibits','Guided history tours available','Educational stops for students and enthusiasts','Iconic photography opportunities','Cultural significance to the region','Well-preserved heritage structures'];
            highlightText = 'Guided tours are available anytime — highly recommended for first-time visitors';
            highlightBadge = '🏛️ Guided Tours';
        } else {
            description = `Explore the unique beauty and attractions of ${name}, a popular destination in San Jose del Monte. Memorable experiences and warm hospitality await all types of travelers.`;
            features = ['Unique local attractions and activities','Beautiful scenery and photo opportunities','Accessible location with various amenities','Cultural and historical significance','Friendly local community','Suitable for all ages'];
            highlightText = 'Morning visits are recommended for the best experience';
            highlightBadge = '✨ Best Visit';
        }

        document.getElementById('mDesc').textContent = description;
        document.getElementById('mHighlightText').textContent = highlightText;
        document.getElementById('mHighlightBadge').textContent = highlightBadge;

        // Expect chips
        const expectGrid = document.getElementById('mExpectGrid');
        const chipIcons = ['🌿','📸','🦋','💧','🌅','🏕️','🌾','🎓','⛪','⚽','🏛️','✨'];
        if (expectGrid) {
            expectGrid.innerHTML = features.map((f, i) => `
                <div class="m-expect-chip">
                    <span class="m-expect-chip-icon">${chipIcons[i % chipIcons.length]}</span>
                    <span>${f}</span>
                </div>`).join('');
        }

        // Guides
        let guidesData = [];
        try {
            let gs = guides;
            if (typeof guides === 'string') {
                gs = guides.replace(/^'|'$/g,'').replace(/\\'/g,"'");
                guidesData = JSON.parse(gs);
            } else if (Array.isArray(guides)) {
                guidesData = guides;
            }
        } catch(e) { guidesData = []; }

        const guidesWrap = document.getElementById('mGuidesWrap');
        const guidesList = document.getElementById('mGuidesList');
        if (guidesData && guidesData.length > 0) {
            guidesList.innerHTML = guidesData.map(g => `
                <div class="m-guide-row" onclick="viewGuideProfile(${g.id})">
                    <div class="m-guide-avatar">${(g.name || 'G').charAt(0).toUpperCase()}</div>
                    <div class="m-guide-info">
                        <div class="m-guide-name">${g.name || 'Guide'}</div>
                        <div class="m-guide-spec">${g.specialty || 'Tour Guide'}</div>
                    </div>
                    <div class="m-guide-rating">
                        <span class="material-icons-outlined">star</span>
                        <span>${parseFloat(g.rating||0).toFixed(1)}</span>
                    </div>
                    <div class="m-guide-arrow"><span class="material-icons-outlined">chevron_right</span></div>
                </div>`).join('');
            guidesWrap.style.display = 'block';
        } else {
            guidesWrap.style.display = 'none';
        }

        // Save button state
        const savedSpots = JSON.parse(localStorage.getItem('savedSpots')) || [];
        const saveBtn = document.getElementById('mSaveBtn');
        if (savedSpots.includes(name)) {
            saveBtn.classList.add('saved');
            saveBtn.innerHTML = '<span class="material-icons-outlined">favorite</span> Saved';
        } else {
            saveBtn.classList.remove('saved');
            saveBtn.innerHTML = '<span class="material-icons-outlined">favorite_border</span> Save';
        }

        // Build culture tab
        buildCultureCards(rawCategory || t);

        // Reset to overview tab
        document.querySelectorAll('.m-tab').forEach(t => t.classList.remove('active'));
        document.querySelectorAll('.m-panel').forEach(p => p.classList.remove('active'));
        document.querySelector('.m-tab:first-child').classList.add('active');
        document.getElementById('tab-overview').classList.add('active');

        // Show modal
        const modal = document.getElementById('touristSpotModal');
        modal.classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    function closeTouristSpotModal() {
        const modal = document.getElementById('touristSpotModal');
        modal.style.animation = 'mOverlayOut 0.2s ease forwards';
        setTimeout(() => {
            modal.classList.remove('active');
            modal.style.animation = '';
            document.body.style.overflow = '';
        }, 200);
    }
    // Add fadeout keyframe
    const _style = document.createElement('style');
    _style.textContent = '@keyframes mOverlayOut { to { opacity:0; } }';
    document.head.appendChild(_style);

    function saveThisSpot() {
        const saveBtn = document.getElementById('mSaveBtn');
        const savedSpots = JSON.parse(localStorage.getItem('savedSpots')) || [];
        if (saveBtn.classList.contains('saved')) {
            const idx = savedSpots.indexOf(_currentSpotName);
            if (idx > -1) savedSpots.splice(idx, 1);
            saveBtn.classList.remove('saved');
            saveBtn.innerHTML = '<span class="material-icons-outlined">favorite_border</span> Save';
            showNotification('Removed from favorites', 'info');
        } else {
            if (!savedSpots.includes(_currentSpotName)) savedSpots.push(_currentSpotName);
            saveBtn.classList.add('saved');
            saveBtn.innerHTML = '<span class="material-icons-outlined">favorite</span> Saved';
            showNotification('Added to favorites!', 'success');
        }
        localStorage.setItem('savedSpots', JSON.stringify(savedSpots));
    }

    function viewAllDetails() {
        closeTouristSpotModal();
        const spotPages = {
            'Mt. Balagbag': '../tourist-detail/mt-balagbag.php',
            'Mount Balagbag': '../tourist-detail/mt-balagbag.php',
            'Abes Farm': '../tourist-detail/abes-farm.php',
            'Abes Farm Resort': '../tourist-detail/abes-farm.php',
            'Burong Falls': '../tourist-detail/burong-falls.php',
            'City Oval & People\'s Park': '../tourist-detail/city-ovals-peoples-park.php',
            'City Oval and People\'s Park': '../tourist-detail/city-ovals-peoples-park.php',
            'Kaytitinga Falls': '../tourist-detail/kaytitinga-falls.php',
            'Otso Otso Falls': '../tourist-detail/otso-otso-falls.php',
            'Otso-Otso Falls': '../tourist-detail/otso-otso-falls.php',
            'Our Lady of Lourdes': '../tourist-detail/our-lady-of-lourdes.php',
            'Our Lady of Lourdes Parish': '../tourist-detail/our-lady-of-lourdes.php',
            'Padre Pio': '../tourist-detail/padre-pio.php',
            'Padre Pio Shrine': '../tourist-detail/padre-pio.php',
            'Paradise Hill Farm': '../tourist-detail/paradise-hill-farm.php',
            'Paradise Hill Farm Resort': '../tourist-detail/paradise-hill-farm.php',
            'The Rising Heart': '../tourist-detail/the-rising-heart.php',
            'The Rising Heart Farm': '../tourist-detail/the-rising-heart.php',
            'Tungtong Falls': '../tourist-detail/tungtong.php'
        };
        setTimeout(() => {
            window.location.href = spotPages[_currentSpotName] || '../tourist-detail/city-ovals-peoples-park.php';
        }, 220);
    }

    function showNotification(message, type = 'info') {
        const existing = document.querySelector('.notification-banner');
        if (existing) existing.remove();
        const n = document.createElement('div');
        n.className = `notification-banner ${type}`;
        const icons = { success:'check_circle', error:'error', warning:'warning', info:'info' };
        n.innerHTML = `<span class="material-icons-outlined notification-icon">${icons[type]||'info'}</span><span class="notification-message">${message}</span><button class="notification-close" onclick="this.parentElement.remove()"><span class="material-icons-outlined">close</span></button>`;
        document.body.appendChild(n);
        setTimeout(() => n.classList.add('show'), 100);
        setTimeout(() => { n.classList.remove('show'); setTimeout(() => { if (n.parentElement) n.remove(); }, 400); }, 3000);
    }

    function viewGuideProfile(guideId) { window.location.href = 'user-guides-page.php?guide=' + guideId; }

    /* Close on backdrop click */
    document.getElementById('touristSpotModal').addEventListener('click', function(e) {
        if (e.target === this) closeTouristSpotModal();
    });
    document.addEventListener('keydown', function(e) { if (e.key === 'Escape') closeTouristSpotModal(); });

    /* ── Filters ── */
    function filterSpots() {
        const category = document.getElementById('categoryFilter').value;
        const activity = document.getElementById('activityFilter').value;
        const duration = document.getElementById('durationFilter').value;
        const cards = document.querySelectorAll('.travelry-card');
        cards.forEach(card => {
            let show = true;
            if (category !== 'all' && category !== card.getAttribute('data-category')) show = false;
            if (activity !== 'all' && activity !== card.getAttribute('data-activity')) show = false;
            if (duration !== 'all') {
                const d = card.getAttribute('data-duration');
                if (duration === '1-2' && !d.includes('1-2')) show = false;
                else if (duration === '2-4' && !d.includes('2-3') && !d.includes('3-4') && !d.includes('2-4') && !d.includes('3-5')) show = false;
                else if (duration === '4+' && !d.includes('4-5') && !d.includes('5-7') && !d.includes('4-6')) show = false;
            }
            card.style.display = show ? 'block' : 'none';
        });
        const visible = Array.from(cards).filter(c => c.style.display !== 'none');
        const noResults = document.querySelector('.no-results-spots');
        if (visible.length === 0 && !noResults) {
            const msg = document.createElement('div');
            msg.className = 'no-results-spots';
            msg.innerHTML = `<div class="empty-state"><span class="material-icons-outlined">search_off</span><h3>No destinations found</h3><p>Try adjusting your filters</p><button class="btn-hero" onclick="resetSpotFilters()">Reset Filters</button></div>`;
            document.getElementById('spotsGrid').appendChild(msg);
        } else if (visible.length > 0 && noResults) {
            noResults.remove();
        }
    }

    function resetSpotFilters() {
        document.getElementById('categoryFilter').value = 'all';
        document.getElementById('activityFilter').value = 'all';
        document.getElementById('durationFilter').value = 'all';
        filterSpots();
    }

    function searchSpots(searchTerm) {
        document.querySelectorAll('.travelry-card').forEach(card => {
            const title = card.querySelector('.card-title')?.textContent.toLowerCase() || '';
            const cat = card.querySelector('.card-category')?.textContent.toLowerCase() || '';
            card.style.display = (title.includes(searchTerm) || cat.includes(searchTerm) || searchTerm === '') ? 'block' : 'none';
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        ['categoryFilter','activityFilter','durationFilter'].forEach(id => {
            const el = document.getElementById(id);
            if (el) el.addEventListener('change', filterSpots);
        });
        const searchInput = document.querySelector('.search-bar input');
        if (searchInput) searchInput.addEventListener('input', e => searchSpots(e.target.value.toLowerCase()));
    });

    /* ── Profile Dropdown ── */
    function initUserProfileDropdown() {
        const profileDropdown = document.querySelector('.user-profile-dropdown');
        const profileTrigger = document.querySelector('.profile-trigger');
        const dropdownMenu = document.querySelector('.dropdown-menu');
        if (!profileDropdown || !profileTrigger || !dropdownMenu) return;
        profileTrigger.addEventListener('click', function(e) {
            e.preventDefault(); e.stopPropagation();
            dropdownMenu.classList.toggle('show');
        });
        document.addEventListener('click', function(e) {
            if (!profileDropdown.contains(e.target)) dropdownMenu.classList.remove('show');
        });
    }

    function confirmLogout() {
        const modal = document.querySelector('.modal-overlay');
        if (modal) modal.remove();
        window.location.href = '../log-in/logout.php';
    }

    document.addEventListener('DOMContentLoaded', function() { initUserProfileDropdown(); });
    </script>

    <?php include __DIR__ . '/../components/preferences-modal.php'; ?>
</body>
</html>