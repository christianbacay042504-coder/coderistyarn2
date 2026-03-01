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

// Fetch featured destinations based on user preferences
$featuredSpots = [];
if ($conn) {
    if (!empty($userPreferences)) {
        $placeholders = rtrim(str_repeat('?,', count($userPreferences)), ',');
        $query = "SELECT * FROM tourist_spots WHERE status = 'active' AND category IN ($placeholders) ORDER BY rating DESC, review_count DESC LIMIT 6";
        $stmt = $conn->prepare($query);
        $stmt->bind_param(str_repeat('s', count($userPreferences)), ...$userPreferences);
        $stmt->execute();
        $result = $stmt->get_result();
    } else {
        $query = "SELECT * FROM tourist_spots WHERE status = 'active' ORDER BY rating DESC, review_count DESC LIMIT 6";
        $result = $conn->query($query);
    }
    if ($result && $result->num_rows > 0) {
        while ($spot = $result->fetch_assoc()) {
            $featuredSpots[] = $spot;
        }
    }
    if (isset($stmt)) $stmt->close();
}

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    if (empty($email) || empty($password)) {
        echo json_encode(['success' => false, 'message' => 'Email and password are required']);
        exit();
    }
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
        $verificationCode = sprintf('%06d', mt_rand(100000, 999999));
        $_SESSION['login_verification_code'] = $verificationCode;
        $_SESSION['login_verification_email'] = $email;
        $_SESSION['login_verification_expires'] = time() + 600;
        echo json_encode(['success' => true, 'verification_required' => true, 'message' => 'Verification code sent to your email']);
        exit();
    }
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
    <link rel="icon" type="image/png" href="../lgo.png">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,500;0,700;1,400;1,500&family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;1,9..40,300&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary: #2c5f2d;
            --primary-light: #e8f5e9;
            --primary-dark: #1e4220;
            --secondary: #97bc62;
            --accent: #ff6b6b;
            --success: #10b981;
            --danger: #ef4444;
            --warning: #f59e0b;
            --text-primary: #1a1a18;
            --text-secondary: #5a5a52;
            --border: #e4e0d8;
            --white: #ffffff;
            --gray-50: #f9fafb;
            --gray-100: #f3f4f6;
            --gray-200: #e5e7eb;
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);

            /* Shared palette */
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
        .main-content { display: block; }
        .main-content.full-width { margin-left: 0; max-width: 100%; }

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

        .header-left .logo {
            display: flex !important;
            align-items: center;
            gap: 10px;
            margin-right: 40px !important;
        }
        .header-left .logo img {
            height: 32px !important; width: 32px !important;
            border-radius: 6px !important;
        }
        .header-left .logo span {
            font-family: 'Playfair Display', serif !important;
            font-size: 17px !important; font-weight: 700 !important;
            color: #fff !important; letter-spacing: 0.04em;
        }

        .header-nav {
            display: flex; align-items: center; gap: 0;
            background: none !important; padding: 0 !important; border-radius: 0 !important;
        }

        .nav-link {
            display: flex; align-items: center; gap: 5px;
            padding: 8px 14px; text-decoration: none;
            color: rgba(255,255,255,0.62) !important;
            font-weight: 400; font-size: 13px;
            border-radius: 0 !important; transition: color 0.18s;
            border-bottom: 2px solid transparent;
            height: 68px; letter-spacing: 0.01em;
        }
        .nav-link:hover { background: none !important; color: rgba(255,255,255,0.9) !important; box-shadow: none !important; }
        .nav-link.active { background: none !important; color: #fff !important; border-bottom-color: var(--pg-gold); }
        .nav-link .material-icons-outlined { font-size: 16px; opacity: 0.8; }

        .btn-signin {
            background: rgba(255,255,255,0.12);
            color: #fff; border: 1px solid rgba(255,255,255,0.2);
            padding: 9px 20px; border-radius: 100px;
            font-weight: 500; font-size: 13px; cursor: pointer;
            transition: all 0.2s; letter-spacing: 0.02em;
        }
        .btn-signin:hover { background: rgba(255,255,255,0.22); transform: none; box-shadow: none; }

        /* Profile dropdown */
        .user-profile-dropdown { position: relative; display: inline-block; z-index: 1000; }
        .profile-trigger {
            display: flex; align-items: center; gap: 8px;
            background: rgba(255,255,255,0.1);
            border: 1px solid rgba(255,255,255,0.18);
            cursor: pointer; color: #fff;
            font-weight: 500; font-size: 13px;
            padding: 7px 14px 7px 8px; border-radius: 100px;
            transition: background 0.2s; box-shadow: none;
        }
        .profile-trigger:hover { background: rgba(255,255,255,0.18); }
        .profile-avatar {
            width: 28px; height: 28px; border-radius: 50%;
            background: var(--pg-gold); color: #fff;
            display: flex; align-items: center; justify-content: center;
            font-weight: 700; font-size: 12px; flex-shrink: 0;
        }
        .profile-avatar-large {
            width: 56px; height: 56px; font-size: 20px;
            margin: 0 auto 12px; border-radius: 50%;
            background: var(--pg-sage); color: white;
            display: flex; align-items: center; justify-content: center; font-weight: bold;
        }
        .profile-name { display: none; }

        .dropdown-menu {
            position: absolute; top: calc(100% + 10px); right: 0;
            width: 240px; background: white; border-radius: 16px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.15), 0 2px 8px rgba(0,0,0,0.08);
            overflow: hidden; z-index: 1001;
            opacity: 0; visibility: hidden; transform: translateY(-8px);
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

        /* content-area reset */
        .main-content.full-width .content-area { padding: 0; max-width: 100%; margin: 0; }

        /* ══════════════════════════
           HERO
        ══════════════════════════ */
        .hero {
            position: relative;
            height: 580px;
            display: flex;
            flex-direction: column;
            justify-content: flex-end;
            align-items: flex-start;
            padding: 0 72px 72px;
            overflow: hidden;
            background:
                linear-gradient(to bottom, rgba(15,25,16,0.2) 0%, rgba(15,25,16,0.08) 40%, rgba(15,25,16,0.78) 100%),
                url('https://images.unsplash.com/photo-1464822759023-fed622ff2c3b?q=80&w=2070&auto=format&fit=crop') center/cover no-repeat;
            background-attachment: fixed;
        }
        .hero::before {
            content: '';
            position: absolute; inset: 0;
            background: linear-gradient(135deg, rgba(30,58,31,0.28) 0%, transparent 55%);
            pointer-events: none; z-index: 0;
        }
        .hero::after { display: none; }

        .hero h1 {
            font-family: 'Playfair Display', serif;
            font-size: 4.2rem; font-weight: 500;
            letter-spacing: -0.02em; line-height: 1.05;
            color: #fff; margin-bottom: 18px;
            background: none; -webkit-text-fill-color: #fff;
            text-shadow: 0 2px 28px rgba(0,0,0,0.22);
            animation: heroFadeUp 0.9s cubic-bezier(0.22,1,0.36,1) both;
            position: relative; z-index: 2; max-width: 680px;
        }

        .hero p {
            font-family: 'DM Sans', sans-serif;
            font-size: 1.1rem; font-weight: 300;
            color: rgba(255,255,255,0.8); max-width: 500px;
            margin: 0 0 36px; line-height: 1.65;
            text-shadow: 0 1px 8px rgba(0,0,0,0.2);
            animation: heroFadeUp 0.9s 0.12s cubic-bezier(0.22,1,0.36,1) both;
            position: relative; z-index: 2;
        }

        @keyframes heroFadeUp {
            from { opacity:0; transform:translateY(24px); }
            to   { opacity:1; transform:translateY(0); }
        }

        .btn-hero {
            display: inline-flex; align-items: center; gap: 10px;
            padding: 15px 32px;
            background: rgba(255,255,255,0.14);
            backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px);
            color: #fff;
            border: 1.5px solid rgba(255,255,255,0.28);
            border-radius: 100px;
            font-family: 'DM Sans', sans-serif;
            font-size: 13.5px; font-weight: 500;
            letter-spacing: 0.08em; text-transform: uppercase;
            cursor: pointer;
            transition: all 0.28s cubic-bezier(0.22,1,0.36,1);
            animation: heroFadeUp 0.9s 0.22s cubic-bezier(0.22,1,0.36,1) both;
            position: relative; z-index: 2;
            text-decoration: none;
        }
        .btn-hero .material-icons-outlined { font-size: 17px; opacity: 0.85; }
        .btn-hero:hover {
            background: rgba(255,255,255,0.24);
            border-color: rgba(255,255,255,0.45);
            transform: translateY(-2px);
            box-shadow: 0 12px 32px rgba(0,0,0,0.18);
        }

        /* ══════════════════════════
           PAGE INNER
        ══════════════════════════ */
        .page-inner {
            max-width: 1360px;
            margin: 0 auto;
            padding: 56px 48px 96px;
        }

        /* ── Section label + title ── */
        .section-eyebrow {
            font-family: 'DM Sans', sans-serif;
            font-size: 10.5px; font-weight: 600;
            color: var(--pg-sage); letter-spacing: 0.16em;
            text-transform: uppercase; margin-bottom: 8px;
        }
        .section-title {
            font-family: 'Playfair Display', serif;
            font-size: 2rem; font-weight: 500;
            color: var(--pg-forest); letter-spacing: -0.01em;
            margin-bottom: 36px;
            background: none;
            -webkit-text-fill-color: var(--pg-forest);
        }
        .section-title::after { display: none; }

        /* ── User preferences banner ── */
        .user-preferences-section {
            background: linear-gradient(135deg, rgba(30,58,31,0.06), rgba(74,124,78,0.04));
            border: 1px solid rgba(30,58,31,0.13);
            border-radius: 18px;
            padding: 28px 32px;
            margin-bottom: 48px;
        }
        .user-preferences-section .section-title { margin-bottom: 20px; font-size: 1.4rem; }

        .preferences-display { display: flex; flex-wrap: wrap; gap: 10px; }
        .preference-tag {
            display: inline-flex; align-items: center; gap: 7px;
            background: var(--pg-forest); color: rgba(255,255,255,0.9);
            padding: 9px 18px; border-radius: 100px;
            font-family: 'DM Sans', sans-serif;
            font-size: 12.5px; font-weight: 500;
            transition: all 0.2s;
        }
        .preference-tag:hover { background: var(--pg-sage); transform: translateY(-1px); }
        .preference-tag .material-icons-outlined { font-size: 15px; opacity: 0.8; }

        /* ══════════════════════════
           DESTINATION CARDS
        ══════════════════════════ */
        .destinations-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 28px;
            margin-bottom: 72px;
        }

        .destination-card {
            background: var(--pg-cream);
            border-radius: 20px;
            overflow: hidden;
            border: 1px solid var(--pg-sand);
            transition: transform 0.28s cubic-bezier(0.22,1,0.36,1), box-shadow 0.28s;
            cursor: pointer;
        }
        .destination-card::before { display: none; }
        .destination-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 24px 60px rgba(30,58,31,0.13), 0 4px 16px rgba(0,0,0,0.06);
        }

        .destination-img {
            width: 100%; height: 220px;
            overflow: hidden; position: relative;
            background: var(--pg-sand);
        }
        .destination-img::after { display: none; }
        .destination-img img {
            width: 100%; height: 100%;
            object-fit: cover; object-position: center;
            display: block;
            transition: transform 5s ease;
            filter: none;
        }
        .destination-card:hover .destination-img img { transform: scale(1.06); }

        /* category pill on image */
        .destination-img-badge {
            position: absolute; top: 14px; left: 14px;
            display: flex; align-items: center; gap: 5px;
            background: rgba(255,255,255,0.14);
            backdrop-filter: blur(10px); -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.25);
            color: #fff;
            font-family: 'DM Sans', sans-serif;
            font-size: 11px; font-weight: 500;
            letter-spacing: 0.08em; text-transform: uppercase;
            padding: 5px 12px 5px 8px; border-radius: 100px;
        }
        .destination-img-badge .material-icons-outlined { font-size: 13px; }

        .destination-content {
            padding: 22px 24px 24px;
            background: var(--pg-cream);
        }
        .destination-content::before { display: none; }

        .destination-content h3 {
            font-family: 'Playfair Display', serif;
            font-size: 1.25rem; font-weight: 500;
            color: var(--pg-forest);
            margin-bottom: 8px; line-height: 1.25;
            letter-spacing: -0.01em;
            background: none; -webkit-text-fill-color: var(--pg-forest);
            transition: none;
        }
        .destination-card:hover .destination-content h3 { transform: none; }

        .destination-content p {
            color: var(--text-secondary);
            font-size: 13px; line-height: 1.7; margin-bottom: 18px;
            font-weight: 400; display: -webkit-box;
            -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;
        }

        .destination-meta {
            display: flex; align-items: center;
            justify-content: space-between;
            margin-top: 16px; padding-top: 16px;
            border-top: 1px solid var(--pg-sand);
        }

        .destination-meta .rating {
            display: flex; align-items: center; gap: 5px;
            color: var(--pg-gold); font-weight: 600; font-size: 13px;
            background: rgba(201,168,92,0.1);
            padding: 6px 12px; border-radius: 100px;
            border: 1px solid rgba(201,168,92,0.2);
            transition: none;
        }
        .destination-meta .rating:hover { transform: none; box-shadow: none; }
        .destination-meta .rating .material-icons-outlined { font-size: 15px; }

        .destination-meta .category {
            background: var(--pg-forest); color: rgba(255,255,255,0.9);
            padding: 6px 14px; border-radius: 100px;
            font-family: 'DM Sans', sans-serif;
            font-size: 11px; font-weight: 500;
            letter-spacing: 0.06em; text-transform: uppercase;
            border: none; box-shadow: none;
            transition: none;
        }
        .destination-meta .category:hover { transform: none; box-shadow: none; }

        /* ══════════════════════════
           STATS GRID
        ══════════════════════════ */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 20px;
            margin-bottom: 0;
        }

        .stat-card {
            background: var(--pg-cream);
            border: 1px solid var(--pg-sand);
            border-radius: 20px;
            padding: 36px 32px;
            display: flex; flex-direction: column;
            align-items: flex-start; gap: 12px;
            transition: transform 0.28s cubic-bezier(0.22,1,0.36,1), box-shadow 0.28s;
            position: relative; overflow: hidden;
            transform-style: flat; perspective: none;
        }
        .stat-card::before { display: none; }
        .stat-card::after { display: none; }
        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 16px 48px rgba(30,58,31,0.1), 0 4px 16px rgba(0,0,0,0.05);
        }

        .stat-card-icon {
            width: 48px; height: 48px; border-radius: 14px;
            background: linear-gradient(135deg, var(--pg-forest), var(--pg-sage));
            display: flex; align-items: center; justify-content: center;
            color: #fff; font-size: 22px;
        }
        .stat-card-icon .material-icons-outlined { font-size: 22px; }

        .stat-card h3 {
            font-family: 'Playfair Display', serif;
            font-size: 2.6rem; font-weight: 500;
            color: var(--pg-forest); line-height: 1;
            background: none; -webkit-text-fill-color: var(--pg-forest);
            text-shadow: none;
            animation: none;
        }

        .stat-card p {
            font-family: 'DM Sans', sans-serif;
            font-size: 12px; font-weight: 500;
            color: #999; letter-spacing: 0.1em;
            text-transform: uppercase;
            text-shadow: none; opacity: 1;
        }

        /* old stat-icon override */
        .stat-card .stat-icon { display: none; }

        /* ══════════════════════════
           MODALS
        ══════════════════════════ */
        .modal-overlay {
            position: fixed; inset: 0;
            background: rgba(20,18,14,0.65);
            backdrop-filter: blur(5px); -webkit-backdrop-filter: blur(5px);
            display: flex; align-items: center; justify-content: center;
            z-index: 9999; opacity: 0; visibility: hidden;
            transition: all 0.25s ease;
        }
        .modal-overlay.show { opacity: 1 !important; visibility: visible !important; }

        .modal-content {
            background: white; border-radius: 20px;
            max-width: 420px; width: 90%; max-height: 85vh;
            overflow-y: auto;
            box-shadow: 0 24px 80px rgba(0,0,0,0.22);
            transform: translateY(20px) scale(0.97);
            transition: all 0.3s cubic-bezier(0.22,1,0.36,1);
        }
        .modal-overlay.show .modal-content { transform: translateY(0) scale(1); }

        /* booking modal override */
        .booking-modal .modal-header {
            background: linear-gradient(135deg, var(--pg-forest) 0%, var(--pg-sage) 100%);
            color: white; padding: 24px 28px;
            display: flex; justify-content: space-between; align-items: center;
            border-radius: 20px 20px 0 0;
        }
        .booking-modal.modal-content { max-width: 820px; }
        .modal-title { display: flex; align-items: center; gap: 12px; }
        .modal-icon { font-size: 26px; }
        .modal-title h2 { margin: 0; font-family: 'Playfair Display', serif; font-size: 1.35rem; font-weight: 500; }

        .modal-header { display: flex; align-items: center; justify-content: space-between; padding: 20px 24px; border-bottom: 1px solid var(--pg-sand); }
        .modal-header h2 { font-family: 'Playfair Display', serif; font-size: 1.3rem; font-weight: 500; color: var(--pg-ink); }

        .close-modal {
            background: rgba(255,255,255,0.15); border: none;
            border-radius: 10px; width: 36px; height: 36px;
            display: flex; align-items: center; justify-content: center;
            cursor: pointer; color: white; transition: background 0.2s;
        }
        .close-modal:hover { background: rgba(255,255,255,0.28); }

        .modal-body { padding: 28px; max-height: calc(85vh - 100px); overflow-y: auto; }

        .logout-message { text-align: center; margin-bottom: 24px; }
        .logout-icon {
            width: 52px; height: 52px; background: var(--danger);
            color: white; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 16px; font-size: 24px;
        }
        .logout-message h3 { margin: 16px 0 8px; color: var(--pg-ink); font-family: 'Playfair Display', serif; }
        .logout-message p { color: #888; margin-bottom: 24px; font-size: 14px; }

        .modal-actions { display: flex; gap: 10px; justify-content: center; }
        .btn-cancel, .btn-confirm-logout {
            padding: 11px 22px; border: none; border-radius: 100px;
            font-weight: 500; cursor: pointer; transition: all 0.2s;
            display: flex; align-items: center; gap: 7px;
            font-family: 'DM Sans', sans-serif; font-size: 13px;
        }
        .btn-cancel { background: var(--pg-mist); color: var(--text-secondary); border: 1px solid var(--pg-sand); }
        .btn-cancel:hover { background: var(--pg-sand); }
        .btn-confirm-logout { background: var(--danger); color: white; }
        .btn-confirm-logout:hover { background: #dc2626; }

        /* booking filters */
        .booking-filters { display: flex; gap: 8px; margin-bottom: 24px; flex-wrap: wrap; }
        .filter-btn {
            padding: 7px 16px; border: 1.5px solid var(--pg-sand);
            background: white; border-radius: 100px; cursor: pointer;
            font-family: 'DM Sans', sans-serif; font-size: 13px; font-weight: 500;
            transition: all 0.2s; color: var(--text-secondary);
        }
        .filter-btn:hover { border-color: var(--pg-sage); color: var(--pg-forest); }
        .filter-btn.active { background: var(--pg-forest); color: white; border-color: var(--pg-forest); }

        .bookings-list { display: flex; flex-direction: column; gap: 14px; }
        .booking-item {
            border: 1px solid var(--pg-sand); border-radius: 14px;
            padding: 20px; background: var(--pg-mist); transition: all 0.2s;
        }
        .booking-item:hover { box-shadow: 0 4px 16px rgba(0,0,0,0.07); transform: translateY(-2px); }
        .booking-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 14px; }
        .booking-info h4 { margin: 0 0 6px; font-family: 'Playfair Display', serif; font-size: 1rem; font-weight: 500; color: var(--pg-ink); }
        .booking-info p { margin: 0; color: #888; display: flex; align-items: center; gap: 5px; font-size: 13px; }

        .status-badge {
            display: inline-flex; align-items: center; gap: 5px;
            padding: 5px 12px; border-radius: 100px;
            font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em;
        }
        .status-badge.status-pending { background: #fff3cd; color: #856404; }
        .status-badge.status-confirmed { background: #d4edda; color: #155724; }
        .status-badge.status-completed { background: #d1ecf1; color: #0c5460; }
        .status-badge.status-cancelled { background: #f8d7da; color: #721c24; }

        .booking-details { display: grid; grid-template-columns: repeat(auto-fit, minmax(140px, 1fr)); gap: 10px; margin-bottom: 14px; }
        .detail-row { display: flex; align-items: center; gap: 7px; font-size: 13px; color: #777; }
        .detail-row .material-icons-outlined { font-size: 15px; color: var(--pg-sage); }

        .booking-actions { display: flex; justify-content: flex-end; }
        .btn-view {
            display: flex; align-items: center; gap: 6px;
            padding: 9px 18px;
            background: var(--pg-forest); color: white;
            border: none; border-radius: 100px; cursor: pointer;
            font-family: 'DM Sans', sans-serif; font-size: 12.5px; font-weight: 500;
            transition: background 0.2s;
        }
        .btn-view:hover { background: var(--pg-sage); }

        .empty-bookings { text-align: center; padding: 56px 20px; color: #888; }
        .empty-icon { font-size: 40px; color: #ccc; margin-bottom: 14px; }
        .empty-bookings h3 { margin: 0 0 10px; font-family: 'Playfair Display', serif; font-size: 1.15rem; color: var(--pg-ink); }
        .empty-bookings p { margin: 0 0 24px; font-size: 13.5px; }
        .btn-primary {
            display: inline-flex; align-items: center; gap: 7px;
            padding: 11px 22px; background: var(--pg-forest); color: white;
            border: none; border-radius: 100px; cursor: pointer;
            font-family: 'DM Sans', sans-serif; font-size: 13px; font-weight: 500;
            text-decoration: none; transition: background 0.2s;
        }
        .btn-primary:hover { background: var(--pg-sage); }

        /* ══════════════════════════
           RESPONSIVE
        ══════════════════════════ */
        @media (max-width: 1024px) {
            .page-inner { padding: 40px 32px 72px; }
            .hero { padding: 0 40px 56px; height: 460px; }
            .hero h1 { font-size: 3.2rem; }
        }
        @media (max-width: 768px) {
            .main-content.full-width .main-header { padding: 0 20px; height: auto; min-height: 60px; flex-direction: column; gap: 0; align-items: stretch; }
            .header-left { padding: 12px 0 0; justify-content: space-between; }
            .header-right { padding: 4px 0 8px; justify-content: center; overflow-x: auto; }
            .nav-link { padding: 8px 10px; font-size: 11.5px; height: auto; border-bottom: none; }
            .nav-link span:not(.material-icons-outlined) { display: none; }
            .hero { height: 380px; padding: 0 24px 44px; background-attachment: scroll; }
            .hero h1 { font-size: 2.4rem; }
            .hero p { font-size: 0.95rem; }
            .page-inner { padding: 28px 20px 56px; }
            .destinations-grid { grid-template-columns: 1fr; gap: 20px; }
            .stats-grid { grid-template-columns: 1fr 1fr; gap: 14px; }
            .stat-card h3 { font-size: 2rem; }
            .booking-header { flex-direction: column; gap: 10px; }
            .booking-details { grid-template-columns: 1fr; }
            .booking-actions { justify-content: center; }
        }
        @media (max-width: 480px) {
            .hero h1 { font-size: 1.9rem; }
            .stats-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <main class="main-content full-width">
        <header class="main-header">
            <div class="header-left">
                <div class="logo" style="display:flex;align-items:center;gap:10px;margin-right:40px;">
                    <img src="../lgo.png" alt="SJDM Tours Logo" style="height:32px;width:32px;object-fit:contain;border-radius:6px;">
                    <span style="font-family:'Playfair Display',serif;font-weight:700;font-size:17px;color:#fff;letter-spacing:0.04em;">SJDM TOURS</span>
                </div>
                <h1 id="pageTitle">Dashboard</h1>
            </div>
            <div class="header-right">
                <nav class="header-nav">
                    <a href="user-index.php" class="nav-link active">
                        <span class="material-icons-outlined">dashboard</span>
                        <span>Dashboard</span>
                    </a>
                    <a href="user-guides-page.php" class="nav-link">
                        <span class="material-icons-outlined">people</span>
                        <span>Tour Guides</span>
                    </a>
                    <a href="user-book.php" class="nav-link">
                        <span class="material-icons-outlined">event</span>
                        <span>Book Now</span>
                    </a>
                    <a href="user-booking-history.php" class="nav-link">
                        <span class="material-icons-outlined">history</span>
                        <span>Booking History</span>
                    </a>
                    <a href="user-tourist-spots.php" class="nav-link">
                        <span class="material-icons-outlined">place</span>
                        <span>Tourist Spots</span>
                    </a>
                    <a href="user-travel-tips.php" class="nav-link">
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
                                <span class="material-icons-outlined">expand_more</span>
                            </button>
                            <div class="dropdown-menu">
                                <div class="dropdown-header">
                                    <div class="profile-avatar-large"><?php echo isset($currentUser['name']) ? strtoupper(substr($currentUser['name'], 0, 1)) : 'U'; ?></div>
                                    <h4><?php echo htmlspecialchars($currentUser['name']); ?></h4>
                                    <p><?php echo htmlspecialchars($currentUser['email']); ?></p>
                                </div>
                                <div class="dropdown-divider"></div>
                                <a href="user-index.php" class="dropdown-item">
                                    <span class="material-icons-outlined">dashboard</span><span>Dashboard</span>
                                </a>
                                <a href="user-booking-history.php" class="dropdown-item">
                                    <span class="material-icons-outlined">history</span><span>Booking History</span>
                                </a>
                                <a href="user-saved-tours.php" class="dropdown-item">
                                    <span class="material-icons-outlined">favorite</span><span>Saved Tours</span>
                                </a>
                                <a href="#" class="dropdown-item" onclick="openPreferencesModal(); return false;">
                                    <span class="material-icons-outlined">tune</span><span>Preferences</span>
                                </a>
                                <div class="dropdown-divider"></div>
                                <a href="user-logout.php" class="dropdown-item">
                                    <span class="material-icons-outlined">logout</span><span>Sign Out</span>
                                </a>
                            </div>
                        </div>
                    <?php else: ?>
                        <button class="btn-signin" onclick="window.location.href='../log-in.php'">Sign in / Register</button>
                    <?php endif; ?>
                </div>
            </div>
        </header>

        <div class="content-area">

            <!-- ── HERO ── -->
            <div class="hero">
                <h1><?php echo htmlspecialchars($homepageContent['hero_title']['main_title'] ?? "Welcome to San Jose del Monte, Bulacan"); ?></h1>
                <p><?php echo htmlspecialchars($homepageContent['hero_subtitle']['main_subtitle'] ?? 'The Balcony of Metropolis — where nature meets progress and every trail tells a story.'); ?></p>
                <a class="btn-hero" href="user-guides-page.php">
                    <span class="material-icons-outlined">explore</span>
                    <?php echo htmlspecialchars($homepageContent['hero_button_text']['main_button'] ?? 'Find Your Guide'); ?>
                </a>
            </div>

            <div class="page-inner">

                <!-- ── User preferences ── -->
                <?php if (!empty($userPreferences)):
                    $categoryMap = ['nature'=>'Nature & Waterfalls','farm'=>'Farms & Eco-Tourism','park'=>'Parks & Recreation','adventure'=>'Adventure & Activities','cultural'=>'Cultural & Historical','religious'=>'Religious Sites','entertainment'=>'Entertainment & Leisure','food'=>'Food & Dining','shopping'=>'Shopping & Markets','wellness'=>'Wellness & Relaxation','education'=>'Educational & Learning','family'=>'Family-Friendly','photography'=>'Photography Spots','wildlife'=>'Wildlife & Nature','outdoor'=>'Outdoor Activities'];
                    $iconMap = ['nature'=>'forest','farm'=>'agriculture','park'=>'park','adventure'=>'hiking','cultural'=>'museum','religious'=>'church','entertainment'=>'sports_esports','food'=>'restaurant','shopping'=>'shopping_cart','wellness'=>'spa','education'=>'school','family'=>'family_restroom','photography'=>'photo_camera','wildlife'=>'pets','outdoor'=>'terrain'];
                ?>
                <div class="user-preferences-section">
                    <p class="section-eyebrow">Personalized for you</p>
                    <div class="section-title">Your Interests</div>
                    <div class="preferences-display">
                        <?php foreach ($userPreferences as $preference): ?>
                            <div class="preference-tag">
                                <span class="material-icons-outlined"><?php echo $iconMap[$preference] ?? 'category'; ?></span>
                                <?php echo htmlspecialchars($categoryMap[$preference] ?? $preference); ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

                <!-- ── Featured destinations ── -->
                <p class="section-eyebrow"><?php echo !empty($userPreferences) ? 'Based on your interests' : 'Top rated'; ?></p>
                <div class="section-title">
                    <?php echo !empty($userPreferences) ? 'Destinations for You' : 'Featured Destinations'; ?>
                </div>

                <div class="destinations-grid">
                    <?php if (!empty($featuredSpots)):
                        $catIconMap = ['nature'=>'landscape','farm'=>'agriculture','park'=>'park','religious'=>'church','urban'=>'location_city','historical'=>'account_balance','waterfalls'=>'water','mountains'=>'terrain','agri-tourism'=>'agriculture'];
                        foreach ($featuredSpots as $spot):
                            $catIcon = $catIconMap[$spot['category']] ?? 'place';
                    ?>
                        <div class="destination-card">
                            <div class="destination-img">
                                <img src="<?php echo htmlspecialchars($spot['image_url'] ?? ''); ?>"
                                     alt="<?php echo htmlspecialchars($spot['name']); ?>"
                                     onerror="this.style.display='none'">
                                <div class="destination-img-badge">
                                    <span class="material-icons-outlined"><?php echo $catIcon; ?></span>
                                    <?php echo ucfirst(htmlspecialchars($spot['category'])); ?>
                                </div>
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
                    <?php endforeach; else: ?>
                        <div style="grid-column:1/-1;text-align:center;padding:60px 20px;color:#999;">
                            <span class="material-icons-outlined" style="font-size:40px;color:#ccc;display:block;margin-bottom:12px;">place</span>
                            <?php echo !empty($userPreferences) ? 'No destinations match your preferences. Try different categories!' : 'No featured destinations available.'; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- ── Stats ── -->
                <p class="section-eyebrow">Why visit</p>
                <div class="section-title">
                    <?php echo htmlspecialchars($homepageContent['section_title']['why_visit'] ?? 'San Jose del Monte, Bulacan'); ?>
                </div>

                <div class="stats-grid">
                    <?php if (!empty($homepageContent['stat_title'])):
                        $statIconMap = ['natural_attractions'=>'forest','distance'=>'location_on','climate'=>'wb_sunny','tourism'=>'tour','default'=>'star'];
                        foreach ($homepageContent['stat_title'] as $key => $title):
                    ?>
                        <div class="stat-card">
                            <div class="stat-card-icon">
                                <span class="material-icons-outlined"><?php echo $statIconMap[$key] ?? 'star'; ?></span>
                            </div>
                            <h3><?php echo htmlspecialchars($homepageContent['stat_value'][$key] ?? '0'); ?></h3>
                            <p><?php echo htmlspecialchars($title); ?></p>
                        </div>
                    <?php endforeach; else: ?>
                        <div class="stat-card">
                            <div class="stat-card-icon"><span class="material-icons-outlined">forest</span></div>
                            <h3>10+</h3>
                            <p>Natural Attractions</p>
                        </div>
                        <div class="stat-card">
                            <div class="stat-card-icon"><span class="material-icons-outlined">location_on</span></div>
                            <h3>30 min</h3>
                            <p>From Metro Manila</p>
                        </div>
                        <div class="stat-card">
                            <div class="stat-card-icon"><span class="material-icons-outlined">wb_sunny</span></div>
                            <h3>Year-round</h3>
                            <p>Perfect Climate</p>
                        </div>
                    <?php endif; ?>
                </div>

            </div><!-- end page-inner -->
        </div><!-- end content-area -->
    </main>

    <!-- Booking History Modal -->
    <div class="modal-overlay" id="bookingHistoryModal">
        <div class="modal-content booking-modal" style="max-width:820px;">
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
                <div id="modalBookingsList" class="bookings-list"></div>
            </div>
        </div>
    </div>

    <script>
        // Profile dropdown toggle
        function toggleDropdown(button) {
            const dropdown = button.nextElementSibling;
            if (dropdown && dropdown.classList.contains('dropdown-menu')) {
                dropdown.classList.toggle('show');
                document.querySelectorAll('.dropdown-menu.show').forEach(menu => {
                    if (menu !== dropdown) menu.classList.remove('show');
                });
            }
        }
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.user-profile-dropdown')) {
                document.querySelectorAll('.dropdown-menu.show').forEach(m => m.classList.remove('show'));
            }
        });

        // Logout confirmation
        function showLogoutConfirmation() {
            const modal = document.createElement('div');
            modal.className = 'modal-overlay';
            modal.innerHTML = `
                <div class="modal-content" style="max-width:380px;">
                    <div class="modal-header" style="border-bottom:1px solid #ede8df;">
                        <h2>Sign Out</h2>
                        <button class="close-modal" style="background:var(--pg-mist);color:#555;border:1px solid var(--pg-sand);" onclick="this.closest('.modal-overlay').remove()">
                            <span class="material-icons-outlined" style="font-size:18px;">close</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="logout-message">
                            <div class="logout-icon"><span class="material-icons-outlined">logout</span></div>
                            <h3>Confirm Sign Out</h3>
                            <p>Are you sure you want to sign out of your account?</p>
                        </div>
                        <div class="modal-actions">
                            <button class="btn-cancel" onclick="this.closest('.modal-overlay').remove()">
                                <span class="material-icons-outlined">close</span> Cancel
                            </button>
                            <button class="btn-confirm-logout" onclick="confirmLogout()">
                                <span class="material-icons-outlined">logout</span> Sign Out
                            </button>
                        </div>
                    </div>
                </div>`;
            document.body.appendChild(modal);
            setTimeout(() => modal.classList.add('show'), 10);
        }

        function confirmLogout() {
            const modal = document.querySelector('.modal-overlay');
            if (modal) modal.remove();
            window.location.href = 'user-logout.php';
        }

        // Booking history modal
        let currentBookingFilter = 'all';
        let userBookings = [];

        function openBookingHistoryModal() {
            const modal = document.getElementById('bookingHistoryModal');
            if (modal) {
                modal.classList.add('show');
                document.body.style.overflow = 'hidden';
                loadBookingHistory();
                initBookingFilters();
            }
        }

        function closeModal(modalId) {
            const modal = document.getElementById(modalId);
            if (modal) { modal.classList.remove('show'); document.body.style.overflow = 'auto'; }
        }

        function loadBookingHistory() {
            fetch('booking-history.php')
                .then(r => r.text())
                .then(html => {
                    const tempDiv = document.createElement('div');
                    tempDiv.innerHTML = html;
                    const scripts = tempDiv.querySelectorAll('script');
                    for (let script of scripts) {
                        if (script.textContent.includes('userBookings =')) {
                            const match = script.textContent.match(/userBookings = (\[.*?\]);/);
                            if (match) { try { userBookings = JSON.parse(match[1]); } catch(e) { userBookings = []; } break; }
                        }
                    }
                    displayModalBookings();
                })
                .catch(() => { userBookings = []; displayModalBookings(); });
        }

        function displayModalBookings() {
            const container = document.getElementById('modalBookingsList');
            if (!container) return;
            let filtered = currentBookingFilter === 'all' ? userBookings : userBookings.filter(b => b.status === currentBookingFilter);
            if (filtered.length === 0) {
                container.innerHTML = `<div class="empty-bookings"><div class="empty-icon"><span class="material-icons-outlined">event_busy</span></div><h3>No ${currentBookingFilter !== 'all' ? currentBookingFilter : ''} bookings</h3><p>${currentBookingFilter === 'all' ? 'Start your adventure by booking your first tour.' : `No ${currentBookingFilter} bookings at the moment.`}</p><a class="btn-primary" href="user-book.php"><span class="material-icons-outlined">explore</span>Book Now</a></div>`;
                return;
            }
            container.innerHTML = filtered.map(b => `
                <div class="booking-item">
                    <div class="booking-header">
                        <div class="booking-info">
                            <h4>${b.guide_name || 'Tour Guide'}</h4>
                            <p><span class="material-icons-outlined">place</span>${b.destination || b.tour_name}</p>
                        </div>
                        <span class="status-badge status-${b.status}">${getStatusIcon(b.status)} ${b.status.toUpperCase()}</span>
                    </div>
                    <div class="booking-details">
                        <div class="detail-row"><span class="material-icons-outlined">event</span><span>${formatDate(b.booking_date)}</span></div>
                        <div class="detail-row"><span class="material-icons-outlined">people</span><span>${b.number_of_people} Guest${b.number_of_people > 1 ? 's' : ''}</span></div>
                        <div class="detail-row"><span class="material-icons-outlined">payments</span><span>₱${Number(b.total_amount).toLocaleString()}</span></div>
                    </div>
                    <div class="booking-actions">
                        <button class="btn-view" onclick="viewBookingDetails(${b.id})">
                            <span class="material-icons-outlined">visibility</span> View Details
                        </button>
                    </div>
                </div>`).join('');
        }

        function initBookingFilters() {
            document.querySelectorAll('.filter-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
                    this.classList.add('active');
                    currentBookingFilter = this.dataset.filter;
                    displayModalBookings();
                });
            });
        }

        function getStatusIcon(status) {
            const icons = { pending:'<span class="material-icons-outlined">schedule</span>', confirmed:'<span class="material-icons-outlined">check_circle</span>', completed:'<span class="material-icons-outlined">verified</span>', cancelled:'<span class="material-icons-outlined">cancel</span>' };
            return icons[status] || '';
        }

        function formatDate(d) {
            return new Date(d).toLocaleDateString('en-US', { month:'short', day:'numeric', year:'numeric' });
        }

        function viewBookingDetails(id) {
            const b = userBookings.find(x => String(x.id) === String(id));
            if (b) alert(`Guide: ${b.guide_name}\nDestination: ${b.destination || b.tour_name}\nDate: ${formatDate(b.booking_date)}\nGuests: ${b.number_of_people}\nTotal: ₱${Number(b.total_amount).toLocaleString()}\nStatus: ${b.status.toUpperCase()}`);
        }

        document.addEventListener('DOMContentLoaded', function() {
            // close dropdown on outside click — already handled above
        });
    </script>

    <?php include __DIR__ . '/../components/preferences-modal.php'; ?>
</body>
</html> 