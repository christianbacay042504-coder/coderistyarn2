<?php
// Include database connection and authentication
require_once '../config/database.php';
require_once '../config/auth.php';

// Get current user data (optional - for logged in users)
$conn = getDatabaseConnection();
$tourGuides = [];
$currentUser = ['name' => 'Guest', 'email' => ''];

// Check if user is logged in
$isLoggedIn = isset($_SESSION['user_id']);
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
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tour Guides - San Jose del Monte Bulacan</title>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,500;0,700;1,400;1,500&family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;1,9..40,300&family=Cormorant+Garamond:ital,wght@0,300;0,400;0,600;1,300;1,400&display=swap" rel="stylesheet">
    
    <style>
        :root {
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
        
        /* HEADER */
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
        
        /* Profile Dropdown */
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
        
        /* HERO SECTION */
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
                url('https://images.unsplash.com/photo-1544735716-392fe2489ffa?q=80&w=2070&auto=format&fit=crop') center/cover no-repeat;
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
        
        /* CONTENT WRAPPER */
        .page-inner {
            max-width: 1360px;
            margin: 0 auto;
            padding: 48px 48px 80px;
        }
        
        /* Preferences banner */
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
        
        /* FILTER BAR */
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
        
        .guides-section-title {
            font-family: 'Playfair Display', serif;
            font-size: 1.6rem;
            font-weight: 500;
            color: var(--pg-forest);
            margin-bottom: 28px;
            letter-spacing: -0.01em;
        }
        .guides-section-title span {
            font-family: 'DM Sans', sans-serif;
            font-size: 0.78rem;
            font-weight: 400;
            color: #999;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            display: block;
            margin-bottom: 6px;
        }
        
        /* CARD GRID */
        .guides-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(340px, 1fr));
            gap: 28px;
        }
        
        .guide-card {
            background: var(--pg-cream);
            border-radius: 20px;
            overflow: hidden;
            border: 1px solid var(--pg-sand);
            transition: transform 0.28s cubic-bezier(0.22,1,0.36,1), box-shadow 0.28s;
            cursor: pointer;
            display: flex;
            flex-direction: column;
        }
        .guide-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 24px 60px rgba(30,58,31,0.13), 0 4px 16px rgba(0,0,0,0.06);
        }
        
        .guide-header {
            position: relative;
            height: 180px;
            background: linear-gradient(135deg, var(--pg-sage) 0%, var(--pg-forest) 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }
        .guide-header::before {
            content: '';
            position: absolute;
            inset: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><circle cx="50" cy="50" r="40" fill="none" stroke="rgba(255,255,255,0.1)" stroke-width="0.5"/></svg>');
            background-size: 60px 60px;
            opacity: 0.3;
        }
        .guide-avatar-large {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background: var(--pg-gold);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Cormorant Garamond', serif;
            font-size: 42px;
            font-weight: 600;
            position: relative;
            z-index: 1;
            border: 4px solid rgba(255,255,255,0.3);
            box-shadow: 0 8px 32px rgba(0,0,0,0.2);
        }
        .verified-badge {
            position: absolute;
            top: 16px;
            right: 16px;
            background: rgba(255,255,255,0.95);
            color: var(--pg-forest);
            padding: 5px 12px;
            border-radius: 100px;
            font-size: 11px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 4px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.15);
            z-index: 2;
        }
        .verified-badge .material-icons-outlined {
            font-size: 14px;
            color: var(--pg-sage);
        }
        
        .guide-body {
            padding: 24px 22px 22px;
            display: flex;
            flex-direction: column;
            gap: 12px;
            flex: 1;
        }
        
        .guide-name {
            font-family: 'Playfair Display', serif;
            font-size: 1.4rem;
            font-weight: 500;
            color: var(--pg-forest);
            line-height: 1.25;
            letter-spacing: -0.01em;
            margin-bottom: 4px;
        }
        
        .guide-specialty {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: var(--pg-mist);
            color: var(--pg-sage);
            padding: 5px 12px;
            border-radius: 100px;
            font-size: 11px;
            font-weight: 500;
            letter-spacing: 0.07em;
            text-transform: uppercase;
            margin-bottom: 8px;
            width: fit-content;
        }
        .guide-specialty .material-icons-outlined {
            font-size: 14px;
        }
        
        .guide-description {
            font-size: 13px;
            line-height: 1.6;
            color: #666;
            margin-bottom: 8px;
        }
        
        .guide-stats {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 8px;
            margin-bottom: 12px;
        }
        .guide-stat-item {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 10px 12px;
            background: var(--pg-mist);
            border-radius: 12px;
            border: 1px solid var(--pg-sand);
        }
        .guide-stat-item .material-icons-outlined {
            font-size: 16px;
            color: var(--pg-sage);
        }
        .guide-stat-label {
            font-size: 9.5px;
            font-weight: 500;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: #999;
        }
        .guide-stat-value {
            font-family: 'Playfair Display', serif;
            font-size: 0.9rem;
            font-weight: 500;
            color: var(--pg-ink);
        }
        
        .guide-features {
            margin-bottom: 12px;
        }
        .features-label {
            font-size: 9.5px;
            font-weight: 600;
            color: #bbb;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            margin-bottom: 7px;
        }
        .features-list {
            display: flex;
            flex-wrap: wrap;
            gap: 5px;
        }
        .feature-item {
            display: inline-flex;
            align-items: center;
            padding: 4px 11px;
            background: var(--pg-mist);
            color: var(--pg-ink);
            border-radius: 100px;
            font-size: 11px;
            font-weight: 400;
            border: 1px solid var(--pg-sand);
        }
        
        .guide-actions {
            margin-top: auto;
            padding-top: 12px;
        }
        .btn-view-profile {
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
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        .btn-view-profile:hover {
            background: var(--pg-sage);
            transform: translateY(-1px);
        }
        .btn-view-profile .material-icons-outlined {
            font-size: 16px;
        }
        
        /* ══════════════════════════════════════════════
        REDESIGNED MODAL - AESTHETIC & SMOOTH
        ══════════════════════════════════════════════ */

        /* Fix: use visibility+opacity for smooth transitions (not display:none) */
        .modal-overlay {
            position: fixed;
            inset: 0;
            z-index: 9999;
            background: rgba(15,13,10,0.75);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            opacity: 0;
            visibility: hidden;
            pointer-events: none;
            transition: opacity 0.35s cubic-bezier(0.4,0,0.2,1), visibility 0.35s;
        }
        .modal-overlay.show {
            opacity: 1;
            visibility: visible;
            pointer-events: all;
        }

        .modal-content.guide-profile-modal {
            background: var(--pg-cream);
            border-radius: 28px;
            width: 100%;
            max-width: 720px;
            max-height: 92vh;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            box-shadow:
                0 0 0 1px rgba(30,58,31,0.06),
                0 8px 32px rgba(0,0,0,0.12),
                0 32px 80px rgba(0,0,0,0.22),
                0 64px 120px rgba(0,0,0,0.1);
            transform: scale(0.93) translateY(24px);
            transition: transform 0.45s cubic-bezier(0.22,1,0.36,1), opacity 0.35s cubic-bezier(0.4,0,0.2,1);
            opacity: 0;
        }
        .modal-overlay.show .modal-content.guide-profile-modal {
            transform: scale(1) translateY(0);
            opacity: 1;
        }

        /* Close button — floats top-right inside modal body */
        .modal-close-btn {
            position: absolute;
            top: 16px;
            right: 16px;
            width: 34px;
            height: 34px;
            background: var(--pg-sand);
            border: none;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: background 0.2s, transform 0.22s cubic-bezier(0.22,1,0.36,1);
            z-index: 10;
            color: #555;
        }
        .modal-close-btn:hover {
            background: var(--pg-warm);
            transform: rotate(90deg);
            color: #222;
        }
        .modal-close-btn .material-icons-outlined {
            font-size: 18px;
            color: inherit;
        }

        /* Modal Body */
        .modal-body {
            overflow-y: auto;
            flex: 1;
            padding: 44px 44px 44px;
            position: relative;
            scrollbar-width: thin;
            scrollbar-color: var(--m-sand) transparent;
            background: var(--pg-cream);
        }
        .modal-body::-webkit-scrollbar { width: 5px; }
        .modal-body::-webkit-scrollbar-track { background: transparent; }
        .modal-body::-webkit-scrollbar-thumb {
            background: var(--m-sand);
            border-radius: 10px;
        }
        .modal-body::-webkit-scrollbar-thumb:hover { background: var(--pg-warm); }

        /* Guide Name Section */
        .guide-profile-header {
            text-align: center;
            margin-bottom: 36px;
            padding-bottom: 32px;
            border-bottom: 1px solid var(--pg-sand);
        }
        .guide-name-section h3 {
            font-family: 'Playfair Display', serif;
            font-size: 2.1rem;
            font-weight: 600;
            color: var(--m-ink);
            margin: 0 0 10px 0;
            letter-spacing: -0.025em;
            line-height: 1.15;
        }
        .verified-ribbon {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            background: linear-gradient(135deg, #2a5e2c 0%, var(--pg-forest) 100%);
            color: rgba(255,255,255,0.95);
            padding: 6px 16px;
            border-radius: 100px;
            font-size: 10.5px;
            font-weight: 600;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            margin-bottom: 12px;
            box-shadow: 0 4px 18px rgba(30,58,31,0.28), 0 1px 4px rgba(0,0,0,0.12);
        }
        .verified-ribbon .material-icons-outlined {
            font-size: 14px;
        }

        /* Quick stats row under name */
        .modal-quick-stats {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0;
            margin-top: 20px;
        }
        .modal-quick-stat {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 3px;
            padding: 12px 28px;
            flex: 1;
            max-width: 140px;
        }
        .modal-quick-stat + .modal-quick-stat {
            border-left: 1px solid var(--pg-sand);
        }
        .modal-quick-stat-value {
            font-family: 'Playfair Display', serif;
            font-size: 1.4rem;
            font-weight: 600;
            color: var(--pg-forest);
            line-height: 1;
        }
        .modal-quick-stat-label {
            font-size: 10px;
            font-weight: 500;
            color: #999;
            text-transform: uppercase;
            letter-spacing: 0.1em;
        }

        .guide-specialty-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: rgba(74,124,78,0.1);
            color: var(--m-sage);
            padding: 7px 16px;
            border-radius: 100px;
            font-size: 11.5px;
            font-weight: 500;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            border: 1px solid rgba(74,124,78,0.2);
        }
        .guide-specialty-badge .material-icons-outlined {
            font-size: 14px;
        }

        /* Section Dividers */
        .section-divider {
            display: none; /* replaced by guide-profile-header border */
        }

        /* Section Headings */
        .guide-description-section,
        .guide-details-section {
            margin-bottom: 28px;
        }
        .guide-booking-section {
            margin-bottom: 0;
        }
        .section-heading {
            display: flex;
            align-items: center;
            gap: 8px;
            font-family: 'DM Sans', sans-serif;
            font-size: 10px;
            font-weight: 700;
            color: var(--pg-sage);
            letter-spacing: 0.14em;
            text-transform: uppercase;
            margin-bottom: 14px;
        }
        .section-heading .material-icons-outlined {
            font-size: 16px;
            color: var(--pg-sage);
        }
        .section-heading::after {
            content: '';
            flex: 1;
            height: 1px;
            background: var(--pg-sand);
            margin-left: 4px;
        }

        /* Description */
        .guide-description-section p {
            font-size: 14.5px;
            line-height: 1.9;
            color: #5a5a52;
            margin: 0;
            padding: 20px 24px;
            background: #fff;
            border-radius: 16px;
            border: 1px solid var(--pg-sand);
            font-style: italic;
            position: relative;
        }
        .guide-description-section p::before {
            content: '"';
            position: absolute;
            top: -2px;
            left: 18px;
            font-family: 'Cormorant Garamond', serif;
            font-size: 52px;
            color: var(--pg-mint);
            line-height: 1;
            pointer-events: none;
        }

        /* Details Grid */
        .guide-details-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
        }
        .detail-item {
            display: flex;
            flex-direction: column;
            gap: 8px;
            padding: 16px;
            background: #fff;
            border-radius: 16px;
            border: 1px solid var(--pg-sand);
            transition: box-shadow 0.22s, border-color 0.22s, transform 0.22s;
        }
        .detail-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 28px rgba(30,58,31,0.08);
            border-color: var(--pg-mint);
        }
        .detail-item .material-icons-outlined {
            font-size: 20px;
            color: var(--m-sage);
            opacity: 0.8;
        }
        .detail-item strong {
            display: block;
            margin-bottom: 2px;
            color: #aaa;
            font-size: 9.5px;
            font-weight: 700;
            letter-spacing: 0.1em;
            text-transform: uppercase;
        }
        .detail-item p {
            margin: 0;
            color: var(--m-ink);
            font-size: 13px;
            font-weight: 500;
            line-height: 1.4;
        }

        /* Booking Section */
        .guide-booking-section {
            background: linear-gradient(145deg, #1b3d1c 0%, #2c6030 60%, #1e3a1f 100%);
            padding: 28px 32px;
            border-radius: 20px;
            color: white;
            margin-top: 24px;
            position: relative;
            overflow: hidden;
            border: 1px solid rgba(181,212,184,0.12);
        }
        .guide-booking-section::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background:
                radial-gradient(ellipse 80% 60% at 90% 0%, rgba(201,168,92,0.1) 0%, transparent 60%),
                radial-gradient(ellipse 60% 80% at 10% 100%, rgba(181,212,184,0.08) 0%, transparent 60%);
            pointer-events: none;
        }
        .guide-booking-section h4 {
            display: flex;
            align-items: center;
            gap: 8px;
            font-family: 'Playfair Display', serif;
            font-size: 1.15rem;
            font-weight: 500;
            letter-spacing: -0.01em;
            margin: 0 0 8px 0;
            position: relative;
            z-index: 1;
            color: rgba(255,255,255,0.97);
        }
        .guide-booking-section h4 .material-icons-outlined {
            font-size: 20px;
            color: var(--m-mint);
        }
        .guide-booking-section p {
            margin: 0 0 22px 0;
            font-size: 13.5px;
            line-height: 1.7;
            opacity: 0.78;
            position: relative;
            z-index: 1;
        }
        .booking-actions {
            display: flex;
            gap: 12px;
            position: relative;
            z-index: 1;
        }
        .btn-primary {
            flex: 1;
            padding: 14px 24px;
            background: var(--pg-gold);
            color: #1a1208;
            border: none;
            border-radius: 14px;
            font-family: 'DM Sans', sans-serif;
            font-size: 13.5px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.28s cubic-bezier(0.22,1,0.36,1);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            letter-spacing: 0.01em;
            box-shadow: 0 4px 18px rgba(201,168,92,0.35), 0 1px 4px rgba(0,0,0,0.15);
        }
        .btn-primary:hover {
            background: #d4b560;
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(201,168,92,0.45), 0 2px 8px rgba(0,0,0,0.2);
        }
        .btn-primary:active { transform: translateY(0); }
        .btn-primary .material-icons-outlined { font-size: 17px; }
        .btn-secondary {
            padding: 14px 24px;
            background: rgba(255,255,255,0.1);
            color: rgba(255,255,255,0.82);
            border: 1.5px solid rgba(255,255,255,0.22);
            border-radius: 14px;
            font-family: 'DM Sans', sans-serif;
            font-size: 13.5px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.28s cubic-bezier(0.22,1,0.36,1);
            letter-spacing: 0.01em;
            white-space: nowrap;
        }
        .btn-secondary:hover {
            background: rgba(255,255,255,0.18);
            border-color: rgba(255,255,255,0.36);
            color: #fff;
        }
        
        /* Responsive */
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
            .guides-grid { grid-template-columns: 1fr; gap: 20px; }
            .guide-details-grid { grid-template-columns: repeat(2, 1fr); }
            .modal-body { padding: 40px 22px 32px; }
            .guide-name-section h3 { font-size: 1.75rem; }
            .modal-quick-stat { padding: 10px 16px; }
            .modal-quick-stat-value { font-size: 1.2rem; }
        }
        @media (max-width: 480px) {
            .hero-section h1 { font-size: 1.8rem; }
            .guide-header { height: 150px; }
            .guide-avatar-large { width: 80px; height: 80px; font-size: 36px; }
            .guide-details-grid { grid-template-columns: 1fr 1fr; }
            .modal-body { padding: 36px 18px 28px; }
            .guide-booking-section { padding: 22px 20px; }
        }
    </style>
</head>
<body>
    <!-- MAIN CONTENT -->
    <main class="main-content full-width">
        <header class="main-header">
            <div class="header-left">
                <div class="logo" style="display:flex;align-items:center;gap:12px;margin-right:30px;">
                    <img src="../lgo.png" alt="SJDM Tours Logo" style="height:40px;width:40px;object-fit:contain;border-radius:8px;">
                    <span style="font-family:'Playfair Display',serif;font-weight:700;font-size:20px;color:#fff;">SJDM TOURS</span>
                </div>
                <h1>Tour Guides</h1>
            </div>
            <div class="header-right">
                <nav class="header-nav">
                    <a href="user-index.php" class="nav-link"><span class="material-icons-outlined">dashboard</span><span>Dashboard</span></a>
                    <a href="user-guides-page.php" class="nav-link active"><span class="material-icons-outlined">people</span><span>Tour Guides</span></a>
                    <a href="user-book.php" class="nav-link"><span class="material-icons-outlined">event</span><span>Book Now</span></a>
                    <a href="user-booking-history.php" class="nav-link"><span class="material-icons-outlined">history</span><span>Booking History</span></a>
                    <a href="user-tourist-spots.php" class="nav-link"><span class="material-icons-outlined">place</span><span>Tourist Spots</span></a>
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
                                <div class="profile-avatar-large"><?php echo isset($currentUser['name']) ? strtoupper(substr($currentUser['name'],0,1)) : 'U'; ?></div>
                                <h4><?php echo htmlspecialchars($currentUser['name']); ?></h4>
                                <p><?php echo htmlspecialchars($currentUser['email']); ?></p>
                            </div>
                            <div class="dropdown-divider"></div>
                            <a href="user-index.php" class="dropdown-item"><span class="material-icons-outlined">dashboard</span><span>Dashboard</span></a>
                            <a href="user-booking-history.php" class="dropdown-item"><span class="material-icons-outlined">history</span><span>Booking History</span></a>
                            <a href="user-saved-tours.php" class="dropdown-item"><span class="material-icons-outlined">favorite</span><span>Saved Tours</span></a>
                            <div class="dropdown-divider"></div>
                            <a href="user-logout.php" class="dropdown-item"><span class="material-icons-outlined">logout</span><span>Sign Out</span></a>
                        </div>
                    </div>
                    <?php else: ?>
                    <button class="btn-signin" onclick="window.location.href='../log-in.php'">Sign in/register</button>
                    <?php endif; ?>
                </div>
            </div>
        </header>
        
        <div class="content-area">
            <!-- Hero Section -->
            <div class="hero-section">
                <h1>Meet Our Expert<br><em>Local Guides</em></h1>
                <p>Discover San Jose del Monte with experienced, verified tour guides who know every hidden gem</p>
                <div class="hero-pills">
                    <div class="hero-pill">
                        <span class="material-icons-outlined">people</span>
                        <?php echo count($tourGuides); ?> Verified Guides
                    </div>
                    <div class="hero-pill">
                        <span class="material-icons-outlined">star</span>
                        4.8+ Average Rating
                    </div>
                    <div class="hero-pill">
                        <span class="material-icons-outlined">verified_user</span>
                        100% Trusted
                    </div>
                </div>
            </div>
            
            <div class="page-inner">
                <?php if ($isLoggedIn && !empty($currentUser)): ?>
                <div class="user-preferences-section">
                    <h2 class="section-title">Your Tour Preferences</h2>
                    <div class="preferences-display">
                        <?php
                        $userPreferences = [];
                        if ($conn && isset($_SESSION['user_id'])) {
                            $tableExists = false;
                            $checkTable = $conn->prepare("SHOW TABLES LIKE 'user_preferences'");
                            $checkTable->execute();
                            $tableExists = $checkTable->get_result()->num_rows > 0;
                            $checkTable->close();
                            
                            if ($tableExists) {
                                $stmt = $conn->prepare("SELECT category FROM user_preferences WHERE user_id = ?");
                                if ($stmt) {
                                    $stmt->bind_param("i", $_SESSION['user_id']);
                                    $stmt->execute();
                                    $result = $stmt->get_result();
                                    while ($pref = $result->fetch_assoc()) {
                                        $userPreferences[] = $pref['category'];
                                    }
                                    $stmt->close();
                                }
                            }
                        }
                        
                        if (empty($userPreferences)) {
                            $userPreferences = ['nature', 'adventure', 'cultural'];
                        }
                        
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
                </div>
                <?php endif; ?>
                
                <!-- Filter Section -->
                <div class="travelry-filters">
                    <div style="display:flex;align-items:baseline;justify-content:space-between;margin-bottom:20px;flex-wrap:wrap;gap:12px;">
                        <div class="guides-section-title">
                            <span>Professional Guides</span>
                            Browse All Guides
                        </div>
                    </div>
                    <div class="filter-row">
                        <div class="filter-group">
                            <label>Specialty</label>
                            <select class="filter-select" id="specialtyFilter">
                                <option value="all">All Specialties</option>
                                <option value="mountain">Mountain Hiking</option>
                                <option value="waterfall">Waterfall Tours</option>
                                <option value="city">City Tours</option>
                                <option value="farm">Farm & Eco-Tourism</option>
                                <option value="historical">Historical Tours</option>
                                <option value="general">General Tours</option>
                            </select>
                        </div>
                        <div class="filter-group">
                            <label>Language</label>
                            <select class="filter-select" id="languageFilter">
                                <option value="all">All Languages</option>
                                <option value="english">English</option>
                                <option value="tagalog">Tagalog</option>
                                <option value="both">English & Tagalog</option>
                            </select>
                        </div>
                        <div class="filter-group">
                            <label>Sort By</label>
                            <select class="filter-select" id="sortGuides">
                                <option value="name">Name (A-Z)</option>
                                <option value="experience">Most Experience</option>
                                <option value="rating">Highest Rating</option>
                            </select>
                        </div>
                    </div>
                </div>
                
                <!-- Guides Grid -->
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
                            $guideInitial = strtoupper(substr($guide['name'], 0, 1));
                            
                            $features = [];
                            $category = strtolower($guideCategory);
                            if ($category === 'mountain' || strpos(strtolower($guideSpecialty), 'mountain') !== false) {
                                $features = ['🏔️ Mountain expert', '🧭 Trail navigation', '🌄 Scenic spots', '⛑️ Safety trained'];
                            } elseif ($category === 'waterfall' || strpos(strtolower($guideSpecialty), 'waterfall') !== false) {
                                $features = ['💧 Waterfall tours', '🏊 Swimming spots', '🌿 Nature trails', '📷 Photo guides'];
                            } elseif ($category === 'farm' || strpos(strtolower($guideSpecialty), 'farm') !== false) {
                                $features = ['🌾 Farm tours', '🐄 Animal encounters', '🥬 Organic farming', '🍓 Fruit picking'];
                            } elseif ($category === 'historical' || strpos(strtolower($guideSpecialty), 'historical') !== false) {
                                $features = ['🏛️ History expert', '📜 Cultural stories', '🎓 Educational tours', '🏺 Heritage sites'];
                            } else {
                                $features = ['🗺️ Local expert', '🎯 Customized tours', '🌐 Multilingual', '⭐ Top rated'];
                            }
                            
                            echo '<div class="guide-card" data-guide-id="' . $guideId . '" data-category="' . $category . '" onclick="openGuideModal(' . $guideId . ')">';
                            echo '<div class="guide-header">';
                            if ($guideVerified) {
                                echo '<div class="verified-badge"><span class="material-icons-outlined">verified_user</span><span>Verified</span></div>';
                            }
                            echo '<div class="guide-avatar-large">' . $guideInitial . '</div>';
                            echo '</div>';
                            
                            echo '<div class="guide-body">';
                            echo '<h3 class="guide-name">' . $guideName . '</h3>';
                            echo '<span class="guide-specialty"><span class="material-icons-outlined">stars</span>' . $guideSpecialty . '</span>';
                            echo '<p class="guide-description">' . $guideDescription . '</p>';
                            
                            echo '<div class="guide-stats">';
                            echo '<div class="guide-stat-item"><span class="material-icons-outlined">schedule</span><div><div class="guide-stat-label">Experience</div><div class="guide-stat-value">' . $guideExperience . '</div></div></div>';
                            echo '<div class="guide-stat-item"><span class="material-icons-outlined">groups</span><div><div class="guide-stat-label">Group Size</div><div class="guide-stat-value">Up to ' . $guideGroupSize . '</div></div></div>';
                            echo '<div class="guide-stat-item"><span class="material-icons-outlined">translate</span><div><div class="guide-stat-label">Languages</div><div class="guide-stat-value">' . (strpos($guideLanguages, ',') !== false ? 'Bilingual' : 'Monolingual') . '</div></div></div>';
                            echo '<div class="guide-stat-item"><span class="material-icons-outlined">star</span><div><div class="guide-stat-label">Rating</div><div class="guide-stat-value">4.8+</div></div></div>';
                            echo '</div>';
                            
                            echo '<div class="guide-features"><div class="features-label">Expertise</div><div class="features-list">';
                            foreach ($features as $feature) {
                                echo '<div class="feature-item">' . $feature . '</div>';
                            }
                            echo '</div></div>';
                            
                            echo '<div class="guide-actions">';
                            echo '<button class="btn-view-profile" onclick="event.stopPropagation(); openGuideModal(' . $guideId . ')"><span class="material-icons-outlined">person</span>View Profile</button>';
                            echo '</div>';
                            echo '</div></div>';
                        }
                    } else {
                        echo '<div class="no-guides-message" style="grid-column: 1 / -1; text-align: center; padding: 60px 20px;">';
                        echo '<span class="material-icons-outlined" style="font-size: 48px; color: #9ca3af;">person_off</span>';
                        echo '<h3 style="color: #6b7280; margin-top: 16px; font-family: \'Playfair Display\', serif;">No tour guides available</h3>';
                        echo '<p style="color: #9ca3af; font-size: 14px;">Please check back later for available tour guides.</p>';
                        echo '</div>';
                    }
                    ?>
                </div>
            </div>
        </div>
    </main>
    
    <!-- Guide Profile Modals - Redesigned -->
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
            $guideInitial = strtoupper(substr($guide['name'], 0, 1));
            ?>
            <div class="modal-overlay" id="modal-guide-<?php echo $guideId; ?>">
                <div class="modal-content guide-profile-modal">
                    <!-- Modal Body -->
                    <div class="modal-body">
                        <button class="modal-close-btn" onclick="closeModal('modal-guide-<?php echo $guideId; ?>')">
                            <span class="material-icons-outlined">close</span>
                        </button>
                        <!-- Profile Header -->
                        <div class="guide-profile-header">
                            <div class="guide-name-section">
                                <h3><?php echo $guideName; ?></h3>
                                <?php if ($guideVerified) { ?>
                                <div class="verified-ribbon">
                                    <span class="material-icons-outlined">verified_user</span>
                                    <span>Trusted Professional</span>
                                </div>
                                <?php } ?>
                                <div>
                                    <span class="guide-specialty-badge">
                                        <span class="material-icons-outlined">stars</span>
                                        <?php echo $guideSpecialty; ?>
                                    </span>
                                </div>
                            </div>
                            <!-- Quick Stats -->
                            <div class="modal-quick-stats">
                                <div class="modal-quick-stat">
                                    <div class="modal-quick-stat-value"><?php echo $guideExperience; ?></div>
                                    <div class="modal-quick-stat-label">Experience</div>
                                </div>
                                <div class="modal-quick-stat">
                                    <div class="modal-quick-stat-value">4.8★</div>
                                    <div class="modal-quick-stat-label">Rating</div>
                                </div>
                                <div class="modal-quick-stat">
                                    <div class="modal-quick-stat-value"><?php echo $guideGroupSize; ?></div>
                                    <div class="modal-quick-stat-label">Max Group</div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Section Divider -->
                        <div class="section-divider"></div>
                        
                        <!-- About Section -->
                        <div class="guide-description-section">
                            <div class="section-heading">
                                <span class="material-icons-outlined">info</span>
                                About This Guide
                            </div>
                            <p><?php echo $guideDescription; ?></p>
                        </div>
                        
                        <!-- Details Section -->
                        <div class="guide-details-section">
                            <div class="section-heading">
                                <span class="material-icons-outlined">person</span>
                                Guide Information
                            </div>
                            <div class="guide-details-grid">
                                <div class="detail-item">
                                    <span class="material-icons-outlined">schedule</span>
                                    <div>
                                        <strong>Experience</strong>
                                        <p><?php echo $guideExperience; ?></p>
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
                                    <span class="material-icons-outlined">wc</span>
                                    <div>
                                        <strong>Gender</strong>
                                        <p><?php echo $guideGender; ?></p>
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
                        </div>
                        
                        <!-- Booking Section -->
                        <div class="guide-booking-section">
                            <h4>
                                <span class="material-icons-outlined">calendar_today</span>
                                Ready to Explore?
                            </h4>
                            <p>Book this guide now and get detailed tour information for an unforgettable SJDM adventure.</p>
                            <div class="booking-actions">
                                <button class="btn-primary" onclick="bookGuide(<?php echo $guideId; ?>)">
                                    <span class="material-icons-outlined">event</span>
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
    
    <script src="script.js"></script>
    <script>
        // Modal functionality with smooth animations
        function openModal(modalId) {
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.classList.add('show');
                document.body.style.overflow = 'hidden';
            }
        }
        
        function closeModal(modalId) {
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.classList.remove('show');
                document.body.style.overflow = '';
            }
        }
        
        function openGuideModal(guideId) {
            openModal('modal-guide-' + guideId);
        }
        
        // Close modal when clicking outside
        document.addEventListener('click', function(event) {
            if (event.target.classList.contains('modal-overlay')) {
                const modalId = event.target.id;
                if (modalId) {
                    closeModal(modalId);
                }
            }
        });
        
        // Close modal with Escape key
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                document.querySelectorAll('.modal-overlay.show').forEach(modal => {
                    modal.classList.remove('show');
                });
                document.body.style.overflow = '';
            }
        });
        
        // bookGuide function
        function bookGuide(guideId) {
            window.location.href = 'user-book.php?guide=' + guideId;
        }
        
        // Filter functionality
        document.addEventListener('DOMContentLoaded', function() {
            const specialtyFilter = document.getElementById('specialtyFilter');
            const languageFilter = document.getElementById('languageFilter');
            const sortGuides = document.getElementById('sortGuides');
            
            function filterGuides() {
                const specialty = specialtyFilter.value;
                const language = languageFilter.value;
                const cards = document.querySelectorAll('.guide-card');
                
                cards.forEach(card => {
                    let show = true;
                    const category = card.getAttribute('data-category');
                    
                    if (specialty !== 'all' && specialty !== category) show = false;
                    
                    if (language !== 'all') {
                        // Add more specific language filtering logic here
                    }
                    
                    card.style.display = show ? 'block' : 'none';
                });
            }
            
            if (specialtyFilter) specialtyFilter.addEventListener('change', filterGuides);
            if (languageFilter) languageFilter.addEventListener('change', filterGuides);
            if (sortGuides) sortGuides.addEventListener('change', function() {
                // Add sorting logic here
            });
            
            // Profile dropdown
            const profileTrigger = document.querySelector('.profile-trigger');
            const dropdownMenu = document.querySelector('.dropdown-menu');
            
            if (profileTrigger && dropdownMenu) {
                profileTrigger.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    dropdownMenu.classList.toggle('show');
                });
                
                document.addEventListener('click', function(e) {
                    if (!profileTrigger.contains(e.target) && !dropdownMenu.contains(e.target)) {
                        dropdownMenu.classList.remove('show');
                    }
                });
            }
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