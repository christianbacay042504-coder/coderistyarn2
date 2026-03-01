<?php
// Start session for user authentication
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
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

// Initialize user contact and address variables
$user_address = '';
$user_contact = '';

// Get URL parameters from tourist detail page
$preselected_destination = $_GET['destination'] ?? '';
$preselected_date = $_GET['date'] ?? '';
$preselected_guide = $_GET['guide'] ?? '';

// Get tour guides from database
$conn = getDatabaseConnection();
$tourGuides = [];

if ($conn) {
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
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Now - San Jose del Monte Bulacan</title>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,500;0,700;1,400;1,500&family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;1,9..40,300&family=Cormorant+Garamond:ital,wght@0,300;0,400;0,600;1,300;1,400&display=swap" rel="stylesheet">
   
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
            height: 420px;
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
        BOOKING PROGRESS
        ══════════════════════════ */
        .booking-progress {
            display: flex;
            justify-content: space-between;
            margin-bottom: 48px;
            position: relative;
            padding: 0 20px;
        }
        .booking-progress::before {
            content: '';
            position: absolute;
            top: 20px;
            left: 60px;
            right: 60px;
            height: 2px;
            background: var(--pg-sand);
            z-index: 0;
        }
        .progress-step {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 8px;
            position: relative;
            z-index: 1;
            flex: 1;
        }
        .step-number {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--pg-sand);
            color: #999;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 14px;
            transition: all 0.3s;
            border: 2px solid var(--pg-sand);
        }
        .progress-step.active .step-number {
            background: var(--pg-forest);
            color: #fff;
            border-color: var(--pg-forest);
        }
        .progress-step.completed .step-number {
            background: var(--pg-sage);
            color: #fff;
            border-color: var(--pg-sage);
        }
        .step-label {
            font-size: 11px;
            font-weight: 500;
            color: #999;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            text-align: center;
        }
        .progress-step.active .step-label {
            color: var(--pg-forest);
            font-weight: 600;
        }
        
        /* ══════════════════════════
        FORM CONTAINER
        ══════════════════════════ */
        .booking-step {
            display: none;
            animation: fadeIn 0.4s ease;
        }
        .booking-step.active {
            display: block;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .form-container {
            background: var(--pg-cream);
            border-radius: 20px;
            padding: 40px;
            border: 1px solid var(--pg-sand);
            box-shadow: 0 4px 20px rgba(0,0,0,0.04);
        }
        
        .form-container h3 {
            font-family: 'Playfair Display', serif;
            font-size: 1.8rem;
            font-weight: 500;
            color: var(--pg-forest);
            margin-bottom: 32px;
            letter-spacing: -0.01em;
        }
        
        .form-group {
            margin-bottom: 24px;
        }
        
        .form-group label {
            display: block;
            font-size: 13px;
            font-weight: 500;
            color: var(--pg-ink);
            margin-bottom: 8px;
            letter-spacing: 0.02em;
        }
        
        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 14px 18px;
            border: 1.5px solid var(--pg-sand);
            border-radius: 12px;
            font-family: 'DM Sans', sans-serif;
            font-size: 14px;
            color: var(--pg-ink);
            background: #fff;
            transition: all 0.2s;
            outline: none;
        }
        
        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            border-color: var(--pg-sage);
            box-shadow: 0 0 0 3px rgba(74,124,78,0.12);
        }
        
        .form-group textarea {
            resize: vertical;
            min-height: 100px;
        }
        
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        
        .form-note {
            font-size: 12px;
            color: #999;
            margin-top: 6px;
            display: block;
        }
        
        /* ══════════════════════════
        GUEST COUNTER
        ══════════════════════════ */
        .guest-counter-container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
            margin-top: 8px;
        }
        
        .guest-category {
            background: #fff;
            border: 1px solid var(--pg-sand);
            border-radius: 16px;
            padding: 20px;
            transition: all 0.3s;
        }
        .guest-category:hover {
            border-color: var(--pg-sage);
            box-shadow: 0 4px 12px rgba(0,0,0,0.06);
        }
        
        .guest-label {
            display: flex;
            flex-direction: column;
            gap: 8px;
            margin-bottom: 16px;
        }
        
        .guest-emoji {
            font-size: 24px;
        }
        
        .guest-type {
            font-weight: 600;
            color: var(--pg-ink);
            font-size: 14px;
        }
        
        .guest-age {
            font-size: 11px;
            color: #999;
        }
        
        .guest-counter {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .counter-btn {
            width: 36px;
            height: 36px;
            border: 2px solid var(--pg-sage);
            background: #fff;
            color: var(--pg-sage);
            border-radius: 50%;
            font-size: 20px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .counter-btn:hover:not(:disabled) {
            background: var(--pg-sage);
            color: #fff;
            transform: scale(1.05);
        }
        .counter-btn:disabled {
            opacity: 0.3;
            cursor: not-allowed;
        }
        
        .counter-value {
            min-width: 40px;
            text-align: center;
            font-weight: 700;
            font-size: 18px;
            color: var(--pg-forest);
        }
        
        .total-guests {
            grid-column: 1 / -1;
            margin-top: 24px;
            padding: 20px;
            background: var(--pg-mist);
            border-radius: 12px;
            border: 1px solid var(--pg-sand);
        }
        
        .total-label {
            font-weight: 600;
            color: var(--pg-forest);
            font-size: 11px;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            margin-bottom: 8px;
        }
        
        .total-value {
            font-weight: 500;
            color: var(--pg-ink);
            font-size: 14px;
        }
        
        /* ══════════════════════════
        DATE SELECTION
        ══════════════════════════ */
        .date-input-container {
            display: flex;
            gap: 8px;
            align-items: center;
        }
        
        .selected-date-display {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 14px 18px;
            background: #fff;
            border: 1.5px solid var(--pg-sand);
            border-radius: 12px;
            flex: 1;
            cursor: pointer;
            transition: all 0.2s;
            font-size: 14px;
            color: var(--pg-ink);
        }
        .selected-date-display:hover {
            border-color: var(--pg-sage);
            background: var(--pg-mist);
        }
        .selected-date-display .material-icons-outlined {
            color: var(--pg-sage);
            font-size: 20px;
        }
        
        .availability-status {
            margin-top: 12px;
            padding: 12px 16px;
            border-radius: 10px;
            font-size: 13px;
            font-weight: 500;
            display: none;
            align-items: center;
            gap: 8px;
        }
        .availability-status .material-icons-outlined {
            font-size: 18px;
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
        
        /* ══════════════════════════
        GUIDE LINKS
        ══════════════════════════ */
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
            padding: 10px 16px;
            background: var(--pg-mist);
            color: var(--pg-forest);
            text-decoration: none;
            border-radius: 100px;
            font-size: 12px;
            font-weight: 500;
            transition: all 0.2s;
            border: 1px solid var(--pg-sand);
        }
        .view-guides-link:hover {
            background: var(--pg-sage);
            color: #fff;
            border-color: var(--pg-sage);
            transform: translateY(-1px);
        }
        
        .guide-details-btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 10px 16px;
            background: var(--pg-forest);
            color: #fff;
            border: none;
            border-radius: 100px;
            font-size: 12px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
        }
        .guide-details-btn:hover {
            background: var(--pg-sage);
            transform: translateY(-1px);
        }
        
        /* ══════════════════════════
        FORM ACTIONS
        ══════════════════════════ */
        .form-actions {
            display: flex;
            gap: 12px;
            margin-top: 32px;
            padding-top: 32px;
            border-top: 1px solid var(--pg-sand);
        }
        
        .btn-prev,
        .btn-next,
        .btn-confirm {
            padding: 14px 28px;
            border: none;
            border-radius: 12px;
            font-family: 'DM Sans', sans-serif;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .btn-prev {
            background: var(--pg-mist);
            color: var(--pg-ink);
            border: 1px solid var(--pg-sand);
        }
        .btn-prev:hover {
            background: var(--pg-sand);
        }
        
        .btn-next,
        .btn-confirm {
            background: var(--pg-forest);
            color: #fff;
        }
        .btn-next:hover,
        .btn-confirm:hover {
            background: var(--pg-sage);
            transform: translateY(-1px);
        }
        .btn-next:disabled,
        .btn-confirm:disabled {
            opacity: 0.5;
            cursor: not-allowed;
            transform: none;
        }
        
        /* ══════════════════════════
        BOOKING SUMMARY (Step 3)
        ══════════════════════════ */
        .booking-summary {
            background: #fff;
            border-radius: 16px;
            padding: 32px;
            border: 1px solid var(--pg-sand);
            margin-bottom: 32px;
        }
        
        .summary-section {
            margin-bottom: 32px;
        }
        .summary-section:last-child {
            margin-bottom: 0;
        }
        
        .summary-section h4 {
            font-family: 'Playfair Display', serif;
            font-size: 1.3rem;
            font-weight: 500;
            color: var(--pg-forest);
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .summary-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }
        
        .summary-item {
            display: flex;
            flex-direction: column;
            gap: 4px;
            padding: 16px;
            background: var(--pg-mist);
            border-radius: 12px;
            border: 1px solid var(--pg-sand);
        }
        
        .summary-label {
            font-size: 11px;
            font-weight: 500;
            color: #999;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        
        .summary-value {
            font-weight: 500;
            color: var(--pg-ink);
            font-size: 14px;
        }
        
        /* Price Summary */
        .price-summary {
            background: linear-gradient(135deg, var(--pg-mint) 0%, var(--pg-cream) 100%);
            border-radius: 16px;
            padding: 28px;
            color: var(--pg-forest);
        }
        
        .price-summary h4 {
            color: var(--pg-forest);
            margin-bottom: 20px;
            opacity: 0.95;
        }
        
        .price-breakdown {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }
        
        .price-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 0;
            border-bottom: 1px solid rgba(30,58,31,0.15);
            font-size: 14px;
        }
        .price-item:last-child {
            border-bottom: none;
        }
        .price-item.total {
            padding-top: 20px;
            margin-top: 8px;
            border-top: 2px solid rgba(30,58,31,0.3);
            font-size: 16px;
        }
        
        .price-divider {
            height: 1px;
            background: rgba(30,58,31,0.2);
            margin: 8px 0;
        }
        
        /* Payment Section */
        .payment-section {
            background: #fff;
            border-radius: 16px;
            padding: 32px;
            border: 1px solid var(--pg-sand);
            margin-bottom: 32px;
        }
        
        .payment-section h4 {
            font-family: 'Playfair Display', serif;
            font-size: 1.3rem;
            font-weight: 500;
            color: var(--pg-forest);
            margin-bottom: 20px;
        }
        
        .payment-options {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }
        
        .payment-option {
            display: flex;
            align-items: flex-start;
            gap: 16px;
            padding: 20px;
            background: var(--pg-mist);
            border: 2px solid var(--pg-sand);
            border-radius: 14px;
            cursor: pointer;
            transition: all 0.2s;
        }
        .payment-option:hover,
        .payment-option.active {
            border-color: var(--pg-sage);
            background: rgba(74,124,78,0.08);
        }
        
        .payment-radio {
            display: flex;
            align-items: center;
            margin-top: 2px;
        }
        
        .payment-icon {
            width: 44px;
            height: 44px;
            background: var(--pg-sage);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
        }
        
        .payment-info h5 {
            font-weight: 600;
            color: var(--pg-ink);
            margin-bottom: 4px;
            font-size: 14px;
        }
        
        .payment-info p {
            font-size: 13px;
            color: #666;
            margin-bottom: 8px;
        }
        
        .payment-note {
            display: flex;
            align-items: flex-start;
            gap: 8px;
            font-size: 12px;
            color: #999;
        }
        .payment-note .material-icons-outlined {
            font-size: 16px;
            flex-shrink: 0;
        }
        
        .payment-notice {
            margin-top: 24px;
            padding: 20px;
            background: var(--pg-mist);
            border-radius: 12px;
            border: 1px solid var(--pg-sand);
        }
        
        .notice-content {
            display: flex;
            align-items: flex-start;
            gap: 12px;
        }
        .notice-content .material-icons-outlined {
            color: var(--pg-sage);
            font-size: 20px;
            flex-shrink: 0;
            margin-top: 2px;
        }
        .notice-content strong {
            display: block;
            color: var(--pg-forest);
            margin-bottom: 4px;
            font-size: 14px;
        }
        .notice-content p {
            font-size: 13px;
            color: #666;
            margin: 0;
        }
        
        /* Terms Section */
        .terms-section {
            background: #fff;
            border-radius: 16px;
            padding: 28px;
            border: 1px solid var(--pg-sand);
            margin-bottom: 32px;
        }
        
        .terms-agreement {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            margin-bottom: 16px;
        }
        .terms-agreement:last-child {
            margin-bottom: 0;
        }
        
        .checkbox-label {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            cursor: pointer;
            font-size: 14px;
            color: var(--pg-ink);
        }
        .checkbox-label input[type="checkbox"] {
            width: 18px;
            height: 18px;
            margin-top: 2px;
            accent-color: var(--pg-forest);
        }
        .checkbox-label a {
            color: var(--pg-sage);
            text-decoration: none;
            font-weight: 500;
        }
        .checkbox-label a:hover {
            text-decoration: underline;
        }
        
        /* ══════════════════════════
        CONFIRMATION (Step 4)
        ══════════════════════════ */
        .confirmation-container {
            background: #fff;
            border-radius: 20px;
            overflow: hidden;
            border: 1px solid var(--pg-sand);
        }
        
        .confirmation-header {
            padding: 48px 40px;
            text-align: center;
            background: linear-gradient(135deg, var(--pg-forest) 0%, var(--pg-sage) 100%);
            color: #fff;
        }
        
        .confirmation-icon {
            width: 80px;
            height: 80px;
            background: rgba(255,255,255,0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 24px;
        }
        .confirmation-icon .material-icons-outlined {
            font-size: 40px;
        }
        
        .confirmation-header h2 {
            font-family: 'Playfair Display', serif;
            font-size: 2rem;
            font-weight: 500;
            margin-bottom: 12px;
        }
        
        .confirmation-header p {
            font-size: 14px;
            opacity: 0.9;
            margin-bottom: 24px;
        }
        
        .booking-number {
            background: rgba(255,255,255,0.15);
            padding: 16px 24px;
            border-radius: 12px;
            display: inline-block;
            font-family: 'DM Sans', sans-serif;
        }
        .booking-number strong {
            display: block;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            margin-bottom: 4px;
            opacity: 0.8;
        }
        .booking-number span {
            font-size: 18px;
            font-weight: 600;
            letter-spacing: 0.05em;
        }
        
        .confirmation-content {
            padding: 40px;
        }
        
        .confirmation-details {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 24px;
            margin-bottom: 40px;
        }
        
        .detail-card {
            background: var(--pg-mist);
            border-radius: 16px;
            padding: 28px;
            border: 1px solid var(--pg-sand);
        }
        
        .detail-card h3 {
            font-family: 'Playfair Display', serif;
            font-size: 1.2rem;
            font-weight: 500;
            color: var(--pg-forest);
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .detail-card h3 .material-icons-outlined {
            color: var(--pg-sage);
            font-size: 20px;
        }
        
        .detail-grid {
            display: flex;
            flex-direction: column;
            gap: 16px;
        }
        
        .detail-item {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            padding-bottom: 16px;
            border-bottom: 1px solid var(--pg-sand);
        }
        .detail-item:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }
        
        .detail-label {
            font-size: 12px;
            color: #999;
            font-weight: 500;
        }
        
        .detail-value {
            font-size: 14px;
            color: var(--pg-ink);
            font-weight: 500;
            text-align: right;
        }
        
        .confirmation-actions {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 24px;
            margin-bottom: 40px;
        }
        
        .action-card {
            background: var(--pg-mist);
            border-radius: 16px;
            padding: 28px;
            border: 1px solid var(--pg-sand);
        }
        .action-card.important {
            background: linear-gradient(135deg, rgba(201,168,92,0.1) 0%, rgba(200,184,154,0.08) 100%);
            border-color: var(--pg-warm);
        }
        
        .action-card h4 {
            font-family: 'Playfair Display', serif;
            font-size: 1.1rem;
            font-weight: 500;
            color: var(--pg-forest);
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .action-card h4 .material-icons-outlined {
            color: var(--pg-sage);
            font-size: 18px;
        }
        .action-card.important h4 .material-icons-outlined {
            color: var(--pg-warm);
        }
        
        .next-steps {
            display: flex;
            flex-direction: column;
            gap: 16px;
        }
        
        .step-item {
            display: flex;
            gap: 16px;
            align-items: flex-start;
        }
        
        .step-item .step-number {
            width: 32px;
            height: 32px;
            background: var(--pg-sage);
            color: #fff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
            font-weight: 600;
            flex-shrink: 0;
        }
        
        .step-content strong {
            display: block;
            color: var(--pg-ink);
            margin-bottom: 4px;
            font-size: 14px;
        }
        
        .step-content p {
            font-size: 13px;
            color: #666;
            margin: 0;
        }
        
        .important-notes {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }
        
        .important-notes li {
            font-size: 13px;
            color: #666;
            padding-left: 20px;
            position: relative;
        }
        .important-notes li::before {
            content: '•';
            position: absolute;
            left: 0;
            color: var(--pg-warm);
            font-weight: bold;
        }
        
        .confirmation-footer {
            padding: 32px 40px;
            background: var(--pg-mist);
            border-top: 1px solid var(--pg-sand);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 20px;
        }
        
        .support-info {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .support-info .material-icons-outlined {
            color: var(--pg-sage);
            font-size: 24px;
        }
        .support-info strong {
            display: block;
            color: var(--pg-forest);
            font-size: 14px;
        }
        .support-info p {
            font-size: 13px;
            color: #666;
            margin: 0;
        }
        
        .confirmation-cta {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }
        
        .btn-secondary,
        .btn-primary,
        .btn-outline {
            padding: 12px 24px;
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
        
        .btn-secondary {
            background: var(--pg-mist);
            color: var(--pg-ink);
            border: 1px solid var(--pg-sand);
        }
        .btn-secondary:hover {
            background: var(--pg-sand);
        }
        
        .btn-primary {
            background: var(--pg-forest);
            color: #fff;
            border: none;
        }
        .btn-primary:hover {
            background: var(--pg-sage);
            transform: translateY(-1px);
        }
        
        .btn-outline {
            background: transparent;
            color: var(--pg-forest);
            border: 1.5px solid var(--pg-forest);
        }
        .btn-outline:hover {
            background: var(--pg-forest);
            color: #fff;
        }
        
        /* ══════════════════════════
        CALENDAR MODAL
        ══════════════════════════ */
        .calendar-modal {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(20,18,14,0.72);
            backdrop-filter: blur(6px);
            -webkit-backdrop-filter: blur(6px);
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
            background: #fff;
            border-radius: 20px;
            max-width: 600px;
            width: 90%;
            max-height: 80vh;
            overflow-y: auto;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
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
            padding: 20px 28px;
            border-bottom: 1px solid var(--m-sand);
            background: var(--m-mist);
        }
        
        .calendar-header h3 {
            font-family: 'Playfair Display', serif;
            font-size: 1.4rem;
            font-weight: 500;
            color: var(--m-ink);
            margin: 0;
        }
        
        .calendar-close {
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
        .calendar-close:hover {
            background: #fff;
            transform: scale(1.08);
        }
        .calendar-close .material-icons-outlined {
            font-size: 18px;
            color: var(--m-ink);
        }
        
        .calendar-body {
            padding: 28px;
        }
        
        .calendar-grid {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 8px;
            margin-bottom: 20px;
        }
        
        .calendar-navigation {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 20px;
            padding: 12px;
            background: var(--pg-mist);
            border-radius: 12px;
            grid-column: 1 / -1;
        }
        
        .calendar-nav-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            border: none;
            background: var(--pg-forest);
            color: #fff;
            border-radius: 50%;
            cursor: pointer;
            transition: all 0.2s;
        }
        .calendar-nav-btn:hover {
            background: var(--pg-sage);
            transform: scale(1.05);
        }
        
        .calendar-month-year {
            font-family: 'Playfair Display', serif;
            font-size: 1.1rem;
            font-weight: 500;
            color: var(--pg-ink);
            text-align: center;
        }
        
        .calendar-day-header {
            text-align: center;
            font-weight: 600;
            color: #999;
            font-size: 12px;
            padding: 8px;
        }
        
        .calendar-day {
            aspect-ratio: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.2s;
            position: relative;
            font-weight: 500;
            font-size: 13px;
        }
        .calendar-day:hover:not(.past):not(.no-slot) {
            background: var(--pg-mist);
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
            background: var(--pg-forest) !important;
            color: #fff !important;
            box-shadow: 0 4px 12px rgba(30,58,31,0.3);
        }
        .calendar-day.past {
            color: #ccc;
            cursor: not-allowed;
        }
        .calendar-day.no-slot {
            opacity: 0.35;
            cursor: not-allowed;
        }
        
        .calendar-legend {
            display: flex;
            gap: 16px;
            justify-content: center;
            flex-wrap: wrap;
            margin-top: 16px;
            padding: 16px;
            background: var(--pg-mist);
            border-radius: 12px;
        }
        
        .legend-item {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 12px;
            color: var(--pg-ink);
        }
        
        .legend-color {
            width: 16px;
            height: 16px;
            border-radius: 4px;
        }
        .legend-color.available { background: #d4edda; }
        .legend-color.limited { background: #fff3cd; }
        .legend-color.unavailable { background: #f8d7da; }
        
        /* ══════════════════════════
        GUIDE PROFILE MODAL
        ══════════════════════════ */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(20,18,14,0.72);
            backdrop-filter: blur(6px);
            -webkit-backdrop-filter: blur(6px);
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
            background: #fff;
            border-radius: 20px;
            max-width: 600px;
            width: 90%;
            max-height: 80vh;
            overflow-y: auto;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
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
            padding: 20px 28px;
            border-bottom: 1px solid var(--m-sand);
            background: var(--m-mist);
        }
        
        .modal-title {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .modal-title h2 {
            font-family: 'Playfair Display', serif;
            font-size: 1.4rem;
            font-weight: 500;
            color: var(--m-ink);
            margin: 0;
        }
        .modal-icon {
            font-size: 22px;
            color: var(--m-sage);
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
        }
        
        .guide-profile-content {
            color: var(--m-ink);
        }
        
        .guide-profile-header {
            text-align: center;
            margin-bottom: 24px;
            padding-bottom: 24px;
            border-bottom: 1px solid var(--m-sand);
        }
        
        .guide-name-section h3 {
            font-family: 'Playfair Display', serif;
            font-size: 1.8rem;
            font-weight: 500;
            color: var(--m-ink);
            margin: 0 0 12px 0;
        }
        
        .verified-ribbon {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: linear-gradient(135deg, var(--m-sage), var(--m-forest));
            color: #fff;
            padding: 6px 16px;
            border-radius: 100px;
            font-size: 11px;
            font-weight: 600;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            margin-bottom: 12px;
        }
        
        .guide-specialty {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: var(--m-mist);
            color: var(--m-sage);
            padding: 6px 14px;
            border-radius: 100px;
            font-size: 12px;
            font-weight: 500;
            margin-bottom: 12px;
        }
        
        .guide-category-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: var(--m-mist);
            color: var(--m-ink);
            padding: 6px 14px;
            border-radius: 100px;
            font-size: 11px;
            font-weight: 500;
            text-transform: capitalize;
        }
        
        .guide-description-section {
            margin-bottom: 24px;
        }
        
        .guide-description-section h4 {
            display: flex;
            align-items: center;
            gap: 8px;
            font-family: 'DM Sans', sans-serif;
            font-size: 11px;
            font-weight: 600;
            color: var(--m-forest);
            letter-spacing: 0.1em;
            text-transform: uppercase;
            margin-bottom: 12px;
        }
        .guide-description-section h4::after {
            content: '';
            flex: 1;
            height: 1px;
            background: var(--m-sand);
        }
        
        .guide-description-section p {
            font-size: 14px;
            line-height: 1.8;
            color: #555;
            margin: 0;
            padding-left: 16px;
            border-left: 2px solid var(--m-mint);
        }
        
        .guide-details-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
            margin-bottom: 24px;
        }
        
        .detail-item {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            padding: 16px;
            background: var(--m-mist);
            border-radius: 12px;
            border: 1px solid var(--m-sand);
        }
        
        .detail-item .material-icons-outlined {
            font-size: 20px;
            color: var(--m-sage);
            flex-shrink: 0;
        }
        
        .detail-item strong {
            display: block;
            margin-bottom: 4px;
            color: var(--m-ink);
            font-size: 10px;
            font-weight: 600;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }
        
        .detail-item p {
            margin: 0;
            color: #666;
            font-size: 13px;
        }
        
        .guide-booking-section {
            background: linear-gradient(135deg, var(--m-forest) 0%, var(--m-sage) 100%);
            padding: 24px;
            border-radius: 16px;
            color: #fff;
        }
        
        .guide-booking-section h4 {
            display: flex;
            align-items: center;
            gap: 8px;
            font-family: 'DM Sans', sans-serif;
            font-size: 12px;
            font-weight: 600;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            margin: 0 0 12px 0;
            opacity: 0.9;
        }
        
        .guide-booking-section p {
            margin: 0 0 20px 0;
            font-size: 13.5px;
            line-height: 1.6;
            opacity: 0.95;
        }
        
        .booking-actions {
            display: flex;
            gap: 12px;
        }
        
        .btn-primary-modal {
            flex: 1;
            padding: 14px 22px;
            background: #fff;
            color: var(--m-forest);
            border: none;
            border-radius: 12px;
            font-family: 'DM Sans', sans-serif;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        .btn-primary-modal:hover {
            transform: translateY(-1px);
            box-shadow: 0 8px 24px rgba(0,0,0,0.2);
        }
        
        .btn-secondary-modal {
            padding: 14px 22px;
            background: rgba(255,255,255,0.15);
            color: #fff;
            border: 1px solid rgba(255,255,255,0.3);
            border-radius: 12px;
            font-family: 'DM Sans', sans-serif;
            font-size: 13px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
        }
        .btn-secondary-modal:hover {
            background: rgba(255,255,255,0.25);
        }
        
        /* ══════════════════════════
        RESPONSIVE
        ══════════════════════════ */
        @media (max-width: 1024px) {
            .page-inner { padding: 36px 32px 60px; }
            .hero-section { padding: 0 40px 40px; height: 360px; }
            .hero-section h1 { font-size: 2.8rem; }
            .confirmation-details { grid-template-columns: 1fr; }
            .confirmation-actions { grid-template-columns: 1fr; }
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
            
            .hero-section { height: 280px; padding: 0 24px 32px; background-attachment: scroll; }
            .hero-section h1 { font-size: 2rem; }
            .hero-section p { font-size: 0.95rem; }
            .hero-pills { flex-wrap: wrap; }
            
            .page-inner { padding: 24px 20px 48px; }
            .form-container { padding: 28px 24px; }
            .form-row { grid-template-columns: 1fr; }
            .guest-counter-container { grid-template-columns: 1fr; }
            .summary-grid { grid-template-columns: 1fr; }
            .booking-progress { overflow-x: auto; }
            .confirmation-footer { flex-direction: column; text-align: center; }
            .confirmation-cta { justify-content: center; }
        }
        
        @media (max-width: 480px) {
            .hero-section h1 { font-size: 1.8rem; }
            .form-container h3 { font-size: 1.5rem; }
            .guide-details-grid { grid-template-columns: 1fr; }
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
                <h1>Book Now</h1>
            </div>
            <div class="header-right">
                <nav class="header-nav">
                    <a href="user-index.php" class="nav-link"><span class="material-icons-outlined">dashboard</span><span>Dashboard</span></a>
                    <a href="user-guides-page.php" class="nav-link"><span class="material-icons-outlined">people</span><span>Tour Guides</span></a>
                    <a href="user-book.php" class="nav-link active"><span class="material-icons-outlined">event</span><span>Book Now</span></a>
                    <a href="user-booking-history.php" class="nav-link"><span class="material-icons-outlined">history</span><span>Booking History</span></a>
                    <a href="user-tourist-spots.php" class="nav-link"><span class="material-icons-outlined">place</span><span>Tourist Spots</span></a>
                    <a href="user-travel-tips.php" class="nav-link"><span class="material-icons-outlined">tips_and_updates</span><span>Travel Tips</span></a>
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
                <h1>Book Your SJDM Tour</h1>
                <p>Plan your perfect adventure in San Jose del Monte with our expert guides</p>
                <div class="hero-pills">
                    <div class="hero-pill">
                        <span class="material-icons-outlined">verified_user</span>
                        Verified Guides
                    </div>
                    <div class="hero-pill">
                        <span class="material-icons-outlined">security</span>
                        Secure Booking
                    </div>
                    <div class="hero-pill">
                        <span class="material-icons-outlined">support_agent</span>
                        24/7 Support
                    </div>
                </div>
            </div>
            
            <div class="page-inner">
                <!-- Booking Progress -->
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
                
                <!-- Step 1: Tour Details -->
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
                                    <a href="user-guides-page.php" class="view-guides-link">
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
                                    <div class="guest-counter-container">
                                        <div class="guest-category">
                                            <div class="guest-label">
                                                <span class="guest-emoji">👨</span>
                                                <span class="guest-type">Adults</span>
                                                <span class="guest-age">(Ages 18+)</span>
                                            </div>
                                            <div class="guest-counter">
                                                <button type="button" class="counter-btn minus" onclick="updateGuestCount('adults', -1)">−</button>
                                                <span class="counter-value" id="adults-count">1</span>
                                                <button type="button" class="counter-btn plus" onclick="updateGuestCount('adults', 1)">+</button>
                                            </div>
                                        </div>
                                        <div class="guest-category">
                                            <div class="guest-label">
                                                <span class="guest-emoji">👴</span>
                                                <span class="guest-type">Seniors</span>
                                                <span class="guest-age">(Ages 60+)</span>
                                            </div>
                                            <div class="guest-counter">
                                                <button type="button" class="counter-btn minus" onclick="updateGuestCount('seniors', -1)">−</button>
                                                <span class="counter-value" id="seniors-count">0</span>
                                                <button type="button" class="counter-btn plus" onclick="updateGuestCount('seniors', 1)">+</button>
                                            </div>
                                        </div>
                                        <div class="guest-category">
                                            <div class="guest-label">
                                                <span class="guest-emoji">🧑</span>
                                                <span class="guest-type">Teenagers</span>
                                                <span class="guest-age">(Ages 13-17)</span>
                                            </div>
                                            <div class="guest-counter">
                                                <button type="button" class="counter-btn minus" onclick="updateGuestCount('teenagers', -1)">−</button>
                                                <span class="counter-value" id="teenagers-count">0</span>
                                                <button type="button" class="counter-btn plus" onclick="updateGuestCount('teenagers', 1)">+</button>
                                            </div>
                                        </div>
                                        <div class="guest-category">
                                            <div class="guest-label">
                                                <span class="guest-emoji">👶</span>
                                                <span class="guest-type">Toddlers</span>
                                                <span class="guest-age">(Ages 0-12)</span>
                                            </div>
                                            <div class="guest-counter">
                                                <button type="button" class="counter-btn minus" onclick="updateGuestCount('toddlers', -1)">−</button>
                                                <span class="counter-value" id="toddlers-count">0</span>
                                                <button type="button" class="counter-btn plus" onclick="updateGuestCount('toddlers', 1)">+</button>
                                            </div>
                                        </div>
                                        <div class="total-guests">
                                            <div class="total-label">TOTAL GUESTS</div>
                                            <div class="total-value" id="total-guests-display">1 Adult • 1 Traveler</div>
                                        </div>
                                        <input type="hidden" id="guestCount" min="1" max="30" value="1" required>
                                    </div>
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
                
                <!-- Step 2: Personal Information -->
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
                
                <!-- Step 3: Review & Payment -->
                <div id="step-3" class="booking-step">
                    <div class="form-container">
                        <h3>Review Your Booking & Payment</h3>
                        <div class="booking-summary">
                            <div class="summary-section">
                                <h4><span class="material-icons-outlined">tour</span> Tour Details</h4>
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
                                <h4><span class="material-icons-outlined">person</span> Personal Information</h4>
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
                                <h4><span class="material-icons-outlined">payments</span> Price Summary</h4>
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
                                        <span>Platform Fee</span>
                                        <span>₱100.00</span>
                                    </div>
                                    <div class="price-divider"></div>
                                    <div class="price-item total">
                                        <strong>Total Amount</strong>
                                        <strong id="priceTotal">₱2,700.00</strong>
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
                
                <!-- Step 4: Confirmation -->
                <div id="step-4" class="booking-step">
                    <div class="confirmation-container">
                        <div class="confirmation-header">
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
                                            <span class="detail-value" id="detailBookingId">-</span>
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
                                            <span>Platform Fee</span>
                                            <span>₱100.00</span>
                                        </div>
                                        <div class="price-divider"></div>
                                        <div class="price-item total">
                                            <strong>Total Paid</strong>
                                            <strong id="confirmationTotal">₱2,700.00</strong>
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
                                <button class="btn-secondary" onclick="window.location.href='user-index.php'">
                                    <span class="material-icons-outlined">home</span>
                                    Back to Home
                                </button>
                                <button class="btn-primary" onclick="window.location.href='user-booking-history.php'">
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
    
   
    <script>
        // Pass tour guides data to JavaScript
        const tourGuides = <?php echo json_encode($tourGuides); ?>;
        
        // State variables
        let selectedDate = null;
        let dateAvailability = {};
        let guideAvailabilityData = {};
        let currentCalendarMonth = new Date().getMonth();
        let currentCalendarYear = new Date().getFullYear();
        
        // Initialize guide selection
        function initGuideSelection() {
            const guideSelect = document.getElementById('selectedGuide');
            const guideDetailsBtn = document.getElementById('guideDetailsBtn');
            if (!guideSelect) return;
            
            guideSelect.addEventListener('change', function() {
                const selectedValue = this.value;
                if (selectedValue && guideDetailsBtn) {
                    guideDetailsBtn.style.display = 'inline-flex';
                } else if (guideDetailsBtn) {
                    guideDetailsBtn.style.display = 'none';
                }
                updateGuideReview();
                clearSelectedDate();
            });
            
            if (guideSelect.value && guideDetailsBtn) {
                guideDetailsBtn.style.display = 'inline-flex';
            }
        }
        
        function clearSelectedDate() {
            const dateInput = document.getElementById('checkInDate');
            if (dateInput) dateInput.value = '';
            
            const selectedDateText = document.getElementById('selectedDateText');
            if (selectedDateText) selectedDateText.textContent = 'Click to select date';
            
            const availabilityStatus = document.getElementById('availabilityStatus');
            if (availabilityStatus) availabilityStatus.style.display = 'none';
            
            selectedDate = null;
            guideAvailabilityData = {};
        }
        
        function showSelectedGuideDetails() {
            const guideSelect = document.getElementById('selectedGuide');
            const selectedGuideId = guideSelect?.value;
            if (!selectedGuideId) {
                alert('Please select a guide first');
                return;
            }
            
            const selectedGuide = tourGuides.find(guide => guide.id == selectedGuideId);
            if (!selectedGuide) {
                alert('Guide information not found');
                return;
            }
            
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
                            <div class="guide-description-section">
                                <h4><span class="material-icons-outlined">info</span> About</h4>
                                <p>${guide.description || 'Experienced tour guide ready to show you the best of San Jose del Monte.'}</p>
                            </div>
                            <div class="guide-details-grid">
                                <div class="detail-item">
                                    <span class="material-icons-outlined">schedule</span>
                                    <div><strong>Experience</strong><p>${guide.experience || '5+ years'}</p></div>
                                </div>
                                <div class="detail-item">
                                    <span class="material-icons-outlined">translate</span>
                                    <div><strong>Languages</strong><p>${guide.languages || 'English, Tagalog'}</p></div>
                                </div>
                                <div class="detail-item">
                                    <span class="material-icons-outlined">groups</span>
                                    <div><strong>Max Group Size</strong><p>Up to ${guide.max_group_size || '10'} guests</p></div>
                                </div>
                                <div class="detail-item">
                                    <span class="material-icons-outlined">wc</span>
                                    <div><strong>Gender</strong><p>${guide.gender || 'Not specified'}</p></div>
                                </div>
                            </div>
                            <div class="guide-booking-section">
                                <h4><span class="material-icons-outlined">calendar_today</span> Booking Information</h4>
                                <p>This guide is available for your selected tour. Click "Close" to return to booking.</p>
                                <div class="booking-actions">
                                    <button class="btn-secondary-modal" onclick="this.closest('.modal-overlay').remove()">Close</button>
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
        
        // Calendar functions
        function showCalendarAvailability() {
            const modal = document.getElementById('calendarModal');
            if (!modal) return;
            
            const guideId = document.getElementById('selectedGuide')?.value;
            if (!guideId) {
                alert('Please select a tour guide first to check availability.');
                return;
            }
            
            currentCalendarMonth = new Date().getMonth();
            currentCalendarYear = new Date().getFullYear();
            guideAvailabilityData = {};
            
            modal.classList.add('show');
            document.body.style.overflow = 'hidden';
            
            showCalendarLoading();
            fetchGuideAvailability(guideId);
        }
        
        function showCalendarLoading() {
            const grid = document.getElementById('calendarGrid');
            if (grid) {
                grid.innerHTML = `
                    <div style="grid-column:1/-1;text-align:center;padding:60px 20px;color:#4b5563;">
                        <div style="font-size:2rem;margin-bottom:10px;">⏳</div>
                        <p style="font-weight:500;">Loading guide availability…</p>
                    </div>`;
            }
        }
        
        function closeCalendarModal() {
            const modal = document.getElementById('calendarModal');
            if (modal) {
                modal.classList.remove('show');
                document.body.style.overflow = '';
            }
        }
        
        function buildCalendar() {
            const grid = document.getElementById('calendarGrid');
            if (!grid) return;
            
            const todayMid = new Date(); todayMid.setHours(0,0,0,0);
            const firstDay = new Date(currentCalendarYear, currentCalendarMonth, 1);
            const daysInMonth = new Date(currentCalendarYear, currentCalendarMonth + 1, 0).getDate();
            const startWeekday = firstDay.getDay();
            
            grid.innerHTML = '';
            
            // Navigation row
            const nav = document.createElement('div');
            nav.className = 'calendar-navigation';
            nav.innerHTML = `
                <button class="calendar-nav-btn" onclick="calPrevMonth()">
                    <span class="material-icons-outlined">chevron_left</span>
                </button>
                <div class="calendar-month-year">
                    ${firstDay.toLocaleDateString('en-US',{month:'long',year:'numeric'})}
                </div>
                <button class="calendar-nav-btn" onclick="calNextMonth()">
                    <span class="material-icons-outlined">chevron_right</span>
                </button>`;
            grid.appendChild(nav);
            
            // Day headers
            ['Sun','Mon','Tue','Wed','Thu','Fri','Sat'].forEach(d => {
                const h = document.createElement('div');
                h.className = 'calendar-day-header';
                h.textContent = d;
                grid.appendChild(h);
            });
            
            // Empty cells
            for (let i = 0; i < startWeekday; i++) {
                grid.appendChild(document.createElement('div'));
            }
            
            // Day cells
            for (let day = 1; day <= daysInMonth; day++) {
                const cellDate = new Date(currentCalendarYear, currentCalendarMonth, day);
                const ds = toDateString(cellDate);
                const cell = document.createElement('div');
                cell.className = 'calendar-day';
                cell.textContent = day;
                
                if (cellDate < todayMid) {
                    cell.classList.add('past');
                    cell.title = 'Date has passed';
                } else if (guideAvailabilityData[ds]) {
                    const info = guideAvailabilityData[ds];
                    cell.classList.add(info.status);
                    cell.title = info.message;
                    if (info.status !== 'unavailable') {
                        cell.style.cursor = 'pointer';
                        cell.addEventListener('click', () => onDatePicked(cellDate, cell, info));
                    } else {
                        cell.style.cursor = 'not-allowed';
                    }
                    dateAvailability[ds] = info;
                } else {
                    cell.classList.add('no-slot');
                    cell.style.cssText += 'opacity:.35;cursor:not-allowed;';
                    cell.title = 'No availability set by guide for this date';
                }
                
                if (selectedDate && ds === toDateString(selectedDate)) {
                    cell.classList.add('selected');
                }
                
                grid.appendChild(cell);
            }
        }
        
        function calPrevMonth() {
            currentCalendarMonth--;
            if (currentCalendarMonth < 0) { currentCalendarMonth = 11; currentCalendarYear--; }
            const gId = document.getElementById('selectedGuide')?.value;
            showCalendarLoading();
            if (gId) fetchGuideAvailability(gId); else buildCalendar();
        }
        
        function calNextMonth() {
            currentCalendarMonth++;
            if (currentCalendarMonth > 11) { currentCalendarMonth = 0; currentCalendarYear++; }
            const gId = document.getElementById('selectedGuide')?.value;
            showCalendarLoading();
            if (gId) fetchGuideAvailability(gId); else buildCalendar();
        }
        
        function fetchGuideAvailability(guideId) {
            guideAvailabilityData = {};
            
            const calendarDays = document.querySelectorAll('.calendar-day:not(.past)');
            calendarDays.forEach(day => {
                day.style.opacity = '0.5';
                day.style.pointerEvents = 'none';
            });
            
            fetch(`../api/get-guide-availability.php?guide_id=${guideId}&year=${currentCalendarYear}&month=${currentCalendarMonth + 1}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        data.availability.forEach(item => {
                            guideAvailabilityData[item.date] = {
                                status: item.status,
                                message: item.message || getStatusMessage(item.status),
                                slots: item.slots || []
                            };
                        });
                        buildCalendar();
                    } else {
                        buildCalendar();
                    }
                })
                .catch(error => {
                    console.error('Error fetching availability:', error);
                    buildCalendar();
                });
        }
        
        function getStatusMessage(status) {
            switch(status) {
                case 'available': return 'Available';
                case 'limited': return 'Limited slots';
                case 'unavailable': return 'Fully booked';
                default: return 'Unknown';
            }
        }
        
        function onDatePicked(date, cell, info) {
            document.querySelectorAll('.calendar-day.selected').forEach(el => el.classList.remove('selected'));
            cell.classList.add('selected');
            selectedDate = date;
            
            const dateInput = document.getElementById('checkInDate');
            if (dateInput) dateInput.value = toDateString(date);
            
            const txt = document.getElementById('selectedDateText');
            if (txt) txt.textContent = date.toLocaleDateString('en-US',
                { weekday:'long', year:'numeric', month:'long', day:'numeric' });
            
            showAvailabilityBadge(info);
            setTimeout(closeCalendarModal, 400);
        }
        
        function showAvailabilityBadge(info) {
            const badge = document.getElementById('availabilityStatus');
            if (!badge) return;
            
            badge.style.display = 'flex';
            
            const iconMap = { available:'check_circle', limited:'warning', unavailable:'block' };
            badge.className = `availability-status ${info.status}`;
            
            if (info.status === 'available') {
                badge.innerHTML = `<span class="material-icons-outlined">${iconMap.available}</span> ${info.message} — Tour guide available!`;
                enableNextButton();
            } else if (info.status === 'limited') {
                badge.innerHTML = `<span class="material-icons-outlined">${iconMap.limited}</span> ${info.message} — Book soon!`;
                enableNextButton();
            } else {
                badge.innerHTML = `<span class="material-icons-outlined">${iconMap.unavailable}</span> ${info.message} — Please choose another date.`;
                disableNextButton();
            }
        }
        
        // Guest counter functions
        function updateGuestCount(category, change) {
            const countElement = document.getElementById(category + '-count');
            let currentCount = parseInt(countElement.textContent) || 0;
            let newCount = currentCount + change;
            
            if (newCount < 0) newCount = 0;
            if (category === 'adults' && newCount < 1) newCount = 1;
            
            countElement.textContent = newCount;
            
            const minusBtn = countElement.previousElementSibling;
            const plusBtn = countElement.nextElementSibling;
            
            if ((category === 'adults' && newCount <= 1) || (category !== 'adults' && newCount <= 0)) {
                minusBtn.disabled = true;
            } else {
                minusBtn.disabled = false;
            }
            
            const totalGuests = getTotalGuests();
            if (totalGuests >= 30 && change > 0) {
                plusBtn.disabled = true;
                return;
            } else {
                document.querySelectorAll('.counter-btn.plus').forEach(btn => {
                    btn.disabled = false;
                });
            }
            
            updateTotalGuestsDisplay();
        }
        
        function getTotalGuests() {
            const adults = parseInt(document.getElementById('adults-count').textContent) || 0;
            const seniors = parseInt(document.getElementById('seniors-count').textContent) || 0;
            const teenagers = parseInt(document.getElementById('teenagers-count').textContent) || 0;
            const toddlers = parseInt(document.getElementById('toddlers-count').textContent) || 0;
            return adults + seniors + teenagers + toddlers;
        }
        
        function updateTotalGuestsDisplay() {
            const adults = parseInt(document.getElementById('adults-count').textContent) || 0;
            const seniors = parseInt(document.getElementById('seniors-count').textContent) || 0;
            const teenagers = parseInt(document.getElementById('teenagers-count').textContent) || 0;
            const toddlers = parseInt(document.getElementById('toddlers-count').textContent) || 0;
            const total = adults + seniors + teenagers + toddlers;
            
            const totalDisplay = document.getElementById('total-guests-display');
            let displayText = '';
            const parts = [];
            
            if (adults > 0) parts.push(`${adults} Adult${adults > 1 ? 's' : ''}`);
            if (seniors > 0) parts.push(`${seniors} Senior${seniors > 1 ? 's' : ''}`);
            if (teenagers > 0) parts.push(`${teenagers} Teenager${teenagers > 1 ? 's' : ''}`);
            if (toddlers > 0) parts.push(`${toddlers} Toddler${toddlers > 1 ? 's' : ''}`);
            
            if (parts.length > 0) {
                displayText = parts.join(' • ') + ' • ' + total + ' Traveler' + (total > 1 ? 's' : '');
            } else {
                displayText = '0 Travelers';
            }
            
            totalDisplay.textContent = displayText;
            
            const guestCountInput = document.getElementById('guestCount');
            if (guestCountInput) {
                guestCountInput.value = total;
            }
            
            updatePriceCalculation();
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
        
        function toDateString(date) {
            const y = date.getFullYear();
            const m = String(date.getMonth()+1).padStart(2,'0');
            const d = String(date.getDate()).padStart(2,'0');
            return `${y}-${m}-${d}`;
        }
        
        // Step navigation
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
            
            const currentStep = document.querySelector('.booking-step.active');
            const currentStepNumber = parseInt(currentStep.id.split('-')[1]);
            
            if (currentStepNumber < 4) {
                currentStep.classList.remove('active');
                document.getElementById(`step-${currentStepNumber + 1}`).classList.add('active');
                updateProgressBar(currentStepNumber + 1);
                updateReviewSection();
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }
        }
        
        function prevStep() {
            const currentStep = document.querySelector('.booking-step.active');
            const currentStepNumber = parseInt(currentStep.id.split('-')[1]);
            
            if (currentStepNumber > 1) {
                currentStep.classList.remove('active');
                document.getElementById(`step-${currentStepNumber - 1}`).classList.add('active');
                updateProgressBar(currentStepNumber - 1);
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }
        }
        
        function updateProgressBar(step) {
            document.querySelectorAll('.progress-step').forEach(el => {
                el.classList.remove('active', 'completed');
                if (parseInt(el.dataset.step) < step) {
                    el.classList.add('completed');
                } else if (parseInt(el.dataset.step) == step) {
                    el.classList.add('active');
                }
            });
        }
        
        function updateReviewSection() {
            const guideSelect = document.getElementById('selectedGuide');
            const destinationSelect = document.getElementById('destination');
            const dateInput = document.getElementById('checkInDate');
            const guestCountInput = document.getElementById('guestCount');
            
            const reviewGuideName = document.getElementById('reviewGuideName');
            if (guideSelect && reviewGuideName) {
                const selectedOption = guideSelect.options[guideSelect.selectedIndex];
                reviewGuideName.textContent = selectedOption && selectedOption.value ? selectedOption.text : '-';
            }
            
            const reviewDestination = document.getElementById('reviewDestination');
            if (destinationSelect && reviewDestination) {
                const selectedOption = destinationSelect.options[destinationSelect.selectedIndex];
                reviewDestination.textContent = selectedOption && selectedOption.value ? selectedOption.text : '-';
            }
            
            const reviewDate = document.getElementById('reviewDate');
            if (dateInput && reviewDate) {
                if (dateInput.value) {
                    const date = new Date(dateInput.value);
                    const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
                    reviewDate.textContent = date.toLocaleDateString('en-US', options);
                } else {
                    reviewDate.textContent = '-';
                }
            }
            
            const reviewGuests = document.getElementById('reviewGuests');
            if (guestCountInput && reviewGuests) {
                const guests = guestCountInput.value;
                reviewGuests.textContent = guests ? `${guests} ${guests == 1 ? 'guest' : 'guests'}` : '-';
            }
            
            const fullNameInput = document.getElementById('fullName');
            const emailInput = document.getElementById('email');
            const contactInput = document.getElementById('contactNumber');
            
            const reviewFullName = document.getElementById('reviewFullName');
            const reviewEmail = document.getElementById('reviewEmail');
            const reviewContact = document.getElementById('reviewContact');
            
            if (fullNameInput && reviewFullName) reviewFullName.textContent = fullNameInput.value || '-';
            if (emailInput && reviewEmail) reviewEmail.textContent = emailInput.value || '-';
            if (contactInput && reviewContact) reviewContact.textContent = contactInput.value || '-';
            
            updatePriceCalculation();
        }
        
        function updatePriceCalculation() {
            const guestCount = parseInt(document.getElementById('guestCount')?.value || 1);
            const priceGuideFee = 2500;
            const priceEntrancePerPerson = 100;
            const pricePlatformFee = 100;
            
            const entranceFee = priceEntrancePerPerson * guestCount;
            const total = priceGuideFee + entranceFee + pricePlatformFee;
            
            const priceGuestCountEl = document.getElementById('priceGuestCount');
            const priceEntranceEl = document.getElementById('priceEntrance');
            const priceTotalEl = document.getElementById('priceTotal');
            
            if (priceGuestCountEl) priceGuestCountEl.textContent = guestCount;
            if (priceEntranceEl) priceEntranceEl.textContent = `₱${entranceFee.toFixed(2)}`;
            if (priceTotalEl) priceTotalEl.textContent = `₱${total.toFixed(2)}`;
            
            const confirmationGuestCountEl = document.getElementById('confirmationGuestCount');
            const confirmationEntranceEl = document.getElementById('confirmationEntrance');
            const confirmationTotalEl = document.getElementById('confirmationTotal');
            
            if (confirmationGuestCountEl) confirmationGuestCountEl.textContent = guestCount;
            if (confirmationEntranceEl) confirmationEntranceEl.textContent = `₱${entranceFee.toFixed(2)}`;
            if (confirmationTotalEl) confirmationTotalEl.textContent = `₱${total.toFixed(2)}`;
        }
        
        function submitBooking() {
            const guideSelect = document.getElementById('selectedGuide');
            const destinationSelect = document.getElementById('destination');
            const dateInput = document.getElementById('checkInDate');
            const guestCountInput = document.getElementById('guestCount');
            const fullNameInput = document.getElementById('fullName');
            const emailInput = document.getElementById('email');
            const contactInput = document.getElementById('contactNumber');
            const termsAgreement = document.getElementById('termsAgreement');
            const cancellationPolicy = document.getElementById('cancellationPolicy');
            
            if (!destinationSelect.value) { alert('Please select a destination'); return; }
            if (!dateInput.value) { alert('Please select a tour date'); return; }
            if (!guestCountInput.value || guestCountInput.value < 1) { alert('Please enter number of guests'); return; }
            if (!fullNameInput.value.trim()) { alert('Please enter your full name'); return; }
            if (!emailInput.value.trim()) { alert('Please enter your email address'); return; }
            if (!contactInput.value.trim()) { alert('Please enter your contact number'); return; }
            if (!termsAgreement.checked) { alert('Please agree to the Terms & Conditions'); return; }
            if (!cancellationPolicy.checked) { alert('Please acknowledge the cancellation policy'); return; }
            
            const availabilityStatus = document.getElementById('availabilityStatus');
            if (availabilityStatus && availabilityStatus.classList.contains('unavailable')) {
                alert('Please select an available date before proceeding');
                return;
            }
            
            const formData = new FormData();
            formData.append('guide_id', guideSelect.value || '');
            formData.append('destination', destinationSelect.value);
            formData.append('date', dateInput.value);
            formData.append('guests', guestCountInput.value);
            formData.append('contact', contactInput.value);
            formData.append('email', emailInput.value);
            formData.append('special_requests', document.getElementById('specialRequests')?.value || '');
            
            const confirmBtn = document.querySelector('.btn-confirm');
            const originalText = confirmBtn.innerHTML;
            confirmBtn.innerHTML = '<span class="material-icons-outlined">hourglass_empty</span> Processing...';
            confirmBtn.disabled = true;
            
            fetch('user-submit-booking.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    updateConfirmationPage(data);
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
            const confirmationBookingNumber = document.getElementById('confirmationBookingNumber');
            if (confirmationBookingNumber) {
                confirmationBookingNumber.textContent = bookingData.booking_reference;
            }
            
            const detailBookingId = document.getElementById('detailBookingId');
            if (detailBookingId) {
                detailBookingId.textContent = bookingData.booking_reference;
            }
            
            const destinationSelect = document.getElementById('destination');
            const dateInput = document.getElementById('checkInDate');
            const guestCountInput = document.getElementById('guestCount');
            const guideSelect = document.getElementById('selectedGuide');
            const fullNameInput = document.getElementById('fullName');
            const emailInput = document.getElementById('email');
            const contactInput = document.getElementById('contactNumber');
            
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
            
            const detailGuestName = document.getElementById('detailGuestName');
            const detailGuestEmail = document.getElementById('detailGuestEmail');
            const detailGuestContact = document.getElementById('detailGuestContact');
            
            if (detailGuestName && fullNameInput) detailGuestName.textContent = fullNameInput.value;
            if (detailGuestEmail && emailInput) detailGuestEmail.textContent = emailInput.value;
            if (detailGuestContact && contactInput) detailGuestContact.textContent = contactInput.value;
            
            updatePriceCalculation();
        }
        
        function shareBooking() {
            alert('Booking sharing feature coming soon!');
        }
        
        function selectPayment(method) {
            document.querySelectorAll('.payment-option').forEach(opt => {
                opt.classList.remove('active');
                opt.querySelector('input[type="radio"]').checked = false;
            });
            const selected = event.currentTarget;
            selected.classList.add('active');
            selected.querySelector('input[type="radio"]').checked = true;
        }
        
        // Profile dropdown
        function initUserProfileDropdown() {
            const profileDropdown = document.querySelector('.user-profile-dropdown');
            const profileTrigger = document.querySelector('.profile-trigger');
            const dropdownMenu = document.querySelector('.dropdown-menu');
            
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
        }
        
        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            initUserProfileDropdown();
            initGuideSelection();
            updateTotalGuestsDisplay();
        });
    </script>
    
    <!-- Preferences Modal -->
    <?php include __DIR__ . '/../components/preferences-modal.php'; ?>
</body>
</html>
<?php
// Close database connection at the very end
if ($conn) {
    closeDatabaseConnection($conn);
}
?>