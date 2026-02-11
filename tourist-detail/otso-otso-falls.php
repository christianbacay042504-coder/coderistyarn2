<?php
require_once __DIR__ . '/../config/auth.php';

// Function to handle booking button clicks
function handleBookingClick($destination) {
    if (!isLoggedIn()) {
        // Redirect to login page if not logged in
        header('Location: ../log-in.php');
        exit();
    }
    // If logged in, redirect to booking page
    header('Location: ../sjdm-user/book.php?destination=' . urlencode($destination));
    exit();
}

// Handle booking requests
if (isset($_GET['action']) && $_GET['action'] === 'book') {
    $destination = $_GET['destination'] ?? 'Otso-Otso Falls';
    handleBookingClick($destination);
}
?>
<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Otso-Otso Falls - San Jose del Monte Tourism</title>

    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet">

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="/coderistyarn2/sjdm-user/styles.css">

    <script async defer src="https://www.google.com/maps/place/Ocho-Ocho+Falls/@14.8140625,121.157722,17z/data=!4m6!3m5!1s0x3397a40df191d887:0x42d0e8bfe4450243!8m2!3d14.8140625!4d121.1602969!16s%2Fg%2F11c5sf9yr0!5m1!1e4?entry=ttu&g_ep=EgoyMDI2MDIwNC4wIKXMDSoASAFQAw%3D%3D"></script>

    <style>

        :root {
            --trip-primary: #0084ff;
            --trip-primary-dark: #0066cc;
            --trip-primary-light: #e6f3ff;
            --trip-secondary: #ff6b35;
            --trip-success: #28a745;
            --trip-warning: #ffc107;
            --trip-danger: #dc3545;
            --trip-dark: #2c3e50;
            --trip-gray: #6c757d;
            --trip-light-gray: #f8f9fa;
            --trip-border: #dee2e6;
            --trip-shadow: 0 2px 8px rgba(0,0,0,0.1);
            --trip-shadow-hover: 0 8px 24px rgba(0,0,0,0.15);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background: var(--trip-light-gray);
            color: var(--trip-dark);
            margin: 0;
            padding: 0;
            line-height: 1.6;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .page-header {
            background: white;
            padding: 0;
            border-bottom: 1px solid var(--trip-border);
            box-shadow: var(--trip-shadow);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .header-content {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 12px 20px;
            max-width: 1200px;
            margin: 0 auto;
        }

        .back-navigation {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .btn-back {
            background: transparent;
            border: 1px solid var(--trip-border);
            color: var(--trip-dark);
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            padding: 8px 16px;
            border-radius: 6px;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .btn-back:hover {
            background: var(--trip-light-gray);
            border-color: var(--trip-primary);
            color: var(--trip-primary);
        }

        .page-title {
            font-size: 20px;
            font-weight: 600;
            color: var(--trip-dark);
            margin: 0;
        }

        .spot-hero {
            height: 500px;
            background: linear-gradient(rgba(0,0,0,0.3), rgba(0,0,0,0.5)), 
                       url('https://images.unsplash.com/photo-1551632811-561732d1e306?q=80&w=2070&auto=format&fit=crop');
            background-size: cover;
            background-position: center;
            display: flex;
            align-items: flex-end;
            justify-content: center;
            color: white;
            margin: 0;
            border-radius: 0;
            max-width: 100%;
            width: 100%;
            position: relative;
        }

        .spot-hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(to bottom, transparent 0%, rgba(0,0,0,0.7) 100%);
            z-index: 1;
        }

        .hero-content {
            max-width: 1200px;
            padding: 40px;
            position: relative;
            z-index: 2;
            width: 100%;
            text-align: left;
        }

        .hero-title {
            font-size: 48px;
            font-weight: 700;
            margin-bottom: 12px;
            text-shadow: 0 2px 4px rgba(0,0,0,0.5);
            line-height: 1.2;
        }

        .hero-subtitle {
            font-size: 18px;
            opacity: 0.9;
            margin-bottom: 24px;
            font-weight: 400;
        }

        .spot-content {

            padding: 0 0 60px;

        }



        .spot-details {

            display: grid;

            grid-template-columns: 2fr 1fr;

            gap: 40px;

            margin-bottom: 60px;

        }



        .section-title {
            font-size: 28px;
            font-weight: 700;
            color: var(--trip-dark);
            margin-bottom: 24px;
            padding-bottom: 16px;
            border-bottom: 2px solid var(--trip-border);
            position: relative;
        }

        .section-title::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 60px;
            height: 2px;
            background: var(--trip-primary);
        }

        .section-subtitle {
            font-size: 20px;
            font-weight: 600;
            color: var(--trip-dark);
            margin: 32px 0 16px;
        }

        .description {
            font-size: 16px;
            line-height: 1.7;
            color: var(--trip-gray);
            margin-bottom: 24px;
        }

        .features-list {
            list-style: none;
            padding: 0;
            margin: 24px 0;
        }

        .features-list li {
            padding: 16px 0;
            padding-left: 36px;
            position: relative;
            font-size: 15px;
            line-height: 1.6;
            color: var(--trip-gray);
            border-bottom: 1px solid var(--trip-border);
        }

        .features-list li:last-child {
            border-bottom: none;
        }

        .features-list li:before {
            content: '✓';
            position: absolute;
            left: 0;
            color: var(--trip-primary);
            font-weight: 600;
            font-size: 18px;
        }

        .spot-info-card {
            background: white;
            border-radius: 12px;
            padding: 32px;
            box-shadow: var(--trip-shadow);
            border: 1px solid var(--trip-border);
            position: sticky;
            top: 80px;
        }

        .info-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 16px;
            margin: 24px 0;
        }

        .info-item {
            display: flex;
            align-items: center;
            gap: 16px;
            padding: 16px;
            background: var(--trip-light-gray);
            border-radius: 8px;
            transition: all 0.2s ease;
            border: 1px solid transparent;
        }

        .info-item:hover {
            transform: translateY(-2px);
            box-shadow: var(--trip-shadow-hover);
            border-color: var(--trip-primary);
        }

        .info-item .material-icons-outlined {
            color: var(--trip-primary);
            font-size: 24px;
        }

        .info-text {
            flex: 1;
        }

        .info-label {
            display: block;
            font-size: 13px;
            color: var(--trip-gray);
            margin-bottom: 4px;
            font-weight: 500;
        }

        .info-value {
            display: block;
            font-size: 16px;
            font-weight: 600;
            color: var(--trip-dark);
        }

        .photo-gallery {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin: 40px 0;
        }

        .gallery-item {
            border-radius: 12px;
            overflow: hidden;
            box-shadow: var(--trip-shadow);
            transition: all 0.3s ease;
            height: 240px;
            position: relative;
            cursor: pointer;
        }

        .gallery-item:hover {
            transform: translateY(-8px);
            box-shadow: var(--trip-shadow-hover);
        }

        .gallery-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }

        .gallery-item:hover img {
            transform: scale(1.08);
        }

        .cta-section {
            background: linear-gradient(135deg, var(--trip-primary) 0%, var(--trip-primary-dark) 100%);
            color: white;
            padding: 60px 40px;
            border-radius: 16px;
            text-align: center;
            margin: 60px 0;
            box-shadow: var(--trip-shadow-hover);
            position: relative;
            overflow: hidden;
        }

        .cta-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.1), transparent);
            animation: shimmer 3s infinite;
        }

        @keyframes shimmer {
            0% { left: -100%; }
            100% { left: 100%; }
        }

        .cta-title {
            font-size: 32px;
            font-weight: 700;
            margin-bottom: 16px;
            position: relative;
            z-index: 1;
        }

        .cta-text {
            font-size: 18px;
            opacity: 0.95;
            max-width: 600px;
            margin: 0 auto 32px;
            line-height: 1.6;
            position: relative;
            z-index: 1;
        }

        .cta-buttons {
            display: flex;
            gap: 16px;
            justify-content: center;
            flex-wrap: wrap;
            position: relative;
            z-index: 1;
        }

        .btn-primary {
            background: white;
            color: var(--trip-primary);
            border: none;
            padding: 16px 32px;
            font-size: 16px;
            font-weight: 600;
            border-radius: 8px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
            min-width: 160px;
            justify-content: center;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(0,0,0,0.2);
            background: var(--trip-light-gray);
        }

        .btn-secondary {
            background: transparent;
            color: white;
            border: 2px solid white;
            padding: 14px 28px;
            font-size: 16px;
            font-weight: 600;
            border-radius: 8px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
            min-width: 160px;
            justify-content: center;
        }

        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.15);
            transform: translateY(-2px);
            border-color: rgba(255, 255, 255, 0.8);
        }

        .footer {
            background: white;
            padding: 40px 0;
            text-align: center;
            border-top: 1px solid var(--trip-border);
            margin-top: 80px;
        }

        .footer-text {
            color: var(--trip-gray);
            font-size: 14px;
            margin: 8px 0;
        }

        .safety-notice {
            background: #fff3cd;
            border-left: 4px solid var(--trip-warning);
            padding: 20px;
            border-radius: 8px;
            margin: 24px 0;
            border: 1px solid #ffeaa7;
        }

        .pool-info {
            background: #d1ecf1;
            border-left: 4px solid var(--trip-primary);
            padding: 20px;
            border-radius: 8px;
            margin: 24px 0;
            border: 1px solid #bee5eb;
        }

        .booking-btn {
            background: linear-gradient(135deg, var(--trip-primary) 0%, var(--trip-primary-dark) 100%);
            color: white;
            border: none;
            padding: 18px 32px;
            font-size: 16px;
            font-weight: 700;
            border-radius: 8px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            transition: all 0.3s ease;
            min-width: 100%;
            width: 100%;
            margin: 24px 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            box-shadow: 0 4px 16px rgba(0, 132, 255, 0.3);
            position: relative;
            overflow: hidden;
        }

        .booking-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: 0.5s;
        }

        .booking-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 24px rgba(0, 132, 255, 0.4);
            background: linear-gradient(135deg, var(--trip-primary-dark) 0%, #0052a3 100%);
        }

        .booking-btn:hover::before {
            left: 100%;
        }

        .booking-btn .material-icons-outlined {
            font-size: 20px;
            transition: transform 0.3s ease;
        }

        .booking-btn:hover .material-icons-outlined {
            transform: translateX(4px);
        }

        .booking-btn:active {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0, 132, 255, 0.3);
        }

        .glow-effect {
            animation: glowPulse 2s infinite alternate;
        }

        @keyframes glowPulse {
            from {
                box-shadow: 0 4px 16px rgba(0, 132, 255, 0.3);
            }
            to {
                box-shadow: 0 4px 20px rgba(0, 132, 255, 0.5), 0 0 20px rgba(0, 132, 255, 0.2);
            }
        }

        .map-container {
            margin: 20px 0;
            border-radius: 12px;

            overflow: hidden;

            box-shadow: 0 4px 12px rgba(0,0,0,0.1);

        }



        #google-map {

            width: 100%;

            height: 300px;

            border-radius: 12px;

        }



        .map-info {

            background: #e8f5e9;

            padding: 15px;

            border-radius: 8px;

            margin: 15px 0;

            border-left: 4px solid #4caf50;

        }



        .map-info h4 {

            margin: 0 0 10px 0;

            color: #2e7d32;

            font-size: 1rem;

            display: flex;

            align-items: center;

            gap: 8px;

        }



        .map-info p {

            margin: 0;

            font-size: 0.9rem;

            color: #1b5e20;

        }



        @keyframes glowPulse {

            from {

                box-shadow: 0 6px 20px rgba(44, 95, 45, 0.3);

            }

            to {

                box-shadow: 0 6px 25px rgba(44, 95, 45, 0.5), 0 0 15px rgba(76, 175, 80, 0.3);

            }

            .booking-btn:hover::before {
                left: 100%;
            }

            .booking-btn:active {
                transform: translateY(-1px);
                box-shadow: 0 4px 12px rgba(0, 132, 255, 0.3);
            }

            .glow-effect {
                animation: glowPulse 2s infinite alternate;
            }

            @keyframes glowPulse {
                from {
                    box-shadow: 0 4px 16px rgba(0, 132, 255, 0.3);
                }
                to {
                    box-shadow: 0 4px 20px rgba(0, 132, 255, 0.5), 0 0 20px rgba(0, 132, 255, 0.2);
                }
            }

            .map-container {
                margin: 20px 0;
                border-radius: 12px;

                overflow: hidden;

                box-shadow: 0 4px 12px rgba(0,0,0,0.1);

            }



            #google-map {

                width: 100%;

                height: 300px;

                border-radius: 12px;

            }



            .map-info {

                background: #e8f5e9;

                padding: 15px;

                border-radius: 8px;

                margin: 15px 0;

                border-left: 4px solid #4caf50;

            }



            .map-info h4 {

                margin: 0 0 10px 0;

                color: #2e7d32;

                font-size: 1rem;

                display: flex;

                align-items: center;

                gap: 8px;

            }



            .map-info p {

                margin: 0;

                font-size: 0.9rem;

                color: #1b5e20;

            }



            @keyframes glowPulse {

                from {

                    box-shadow: 0 6px 20px rgba(44, 95, 45, 0.3);

                }

                to {

                    box-shadow: 0 6px 25px rgba(44, 95, 45, 0.5), 0 0 15px rgba(76, 175, 80, 0.3);

                }

            }



            /* =================================

               MODERNIZED MODAL STYLES

               ================================= */

            

            /* Modal Overlay */

            .modal {
                display: none;
                position: fixed;
                z-index: 1000;
                left: 0;
                top: 0;
                width: 100%;
                height: 100%;
                background-color: rgba(0, 0, 0, 0.6);
                backdrop-filter: blur(8px);
                animation: fadeIn 0.3s ease;
                overflow-y: auto;
                padding: 20px;
            }

            .modal.show {
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .modal-content {
                background: white;
                border-radius: 16px;
                max-width: 600px;
                width: 100%;
                max-height: 85vh;
                overflow: hidden;
                animation: slideUp 0.4s cubic-bezier(0.16, 1, 0.3, 1);
                box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
                display: flex;
                flex-direction: column;
            }

            .modal-header {
                background: linear-gradient(135deg, var(--trip-primary) 0%, var(--trip-primary-dark) 100%);
                color: white;
                padding: 0;
                position: relative;
                flex-shrink: 0;
            }

            .modal-tabs {
                display: flex;
                gap: 0;
                padding: 24px 24px 0;
            }

            .tab-btn {
                background: transparent;
                border: none;
                color: rgba(255, 255, 255, 0.7);
                padding: 16px 24px;
                font-size: 14px;
                font-weight: 600;
                cursor: pointer;
                display: flex;
                align-items: center;
                gap: 8px;
                transition: all 0.3s ease;
                border-radius: 12px 12px 0 0;
                letter-spacing: 0.3px;
                position: relative;
                flex: 1;
                justify-content: center;
            }

            .tab-btn:hover {
                color: white;
                background: rgba(255, 255, 255, 0.1);
            }

            .tab-btn.active {
                background: white;
                color: var(--trip-primary);
                box-shadow: 0 -2px 8px rgba(0, 0, 0, 0.1);
            }

            .tab-btn .material-icons-outlined {
                font-size: 18px;
            }

            .modal-close {
                position: absolute;
                top: 20px;
                right: 20px;
                background: rgba(255, 255, 255, 0.2);
                border: none;
                color: white;
                width: 40px;
                height: 40px;
                border-radius: 50%;
                cursor: pointer;
                display: flex;
                align-items: center;
                justify-content: center;
                transition: all 0.3s ease;
                z-index: 10;
            }

            .modal-close:hover {
                background: rgba(255, 255, 255, 0.3);
                transform: rotate(90deg);
            }

            .modal-close .material-icons-outlined {
                font-size: 20px;
            }

            .modal-body {
                padding: 24px;
                overflow-y: auto;
                flex: 1;
            }

            .tab-content {
                display: none;
            }


        .booking-info-section {

            margin-bottom: 24px;

        }



        .booking-info-grid {

            display: grid;

            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));

            gap: 16px;

        }



        .booking-info-card {

            background: white;

            border: 2px solid var(--trip-border);

            border-radius: 16px;

            padding: 20px;

            transition: all 0.3s ease;

        }



        .booking-info-card:hover {

            border-color: var(--trip-primary);

            transform: translateY(-4px);

            box-shadow: 0 12px 24px rgba(44, 95, 45, 0.15);

        }



        .booking-info-header {

            display: flex;

            align-items: center;

            gap: 12px;

            margin-bottom: 10px;

            color: var(--primary);

        }



        .booking-info-header .material-icons-outlined {

            font-size: 28px;

        }



        .booking-info-title {

            font-size: 0.9rem;

            font-weight: 600;

            color: var(--text-light);

            text-transform: uppercase;

            letter-spacing: 0.5px;

        }



        .booking-info-value {

            font-size: 1.25rem;

            font-weight: 700;

            color: var(--trip-dark);

            line-height: 1.4;

        }


        /* Booking Actions */

        .booking-actions {

            display: flex;

            gap: 16px;

            padding-top: 20px;

            border-top: 2px solid var(--trip-border);

        }



        .btn-book-selected {

            background: linear-gradient(135deg, var(--trip-primary) 0%, var(--trip-primary-light) 100%);

            color: white;

            border: none;

            padding: 18px 32px;

            font-size: 1.05rem;

            font-weight: 700;

            border-radius: 12px;

            cursor: pointer;

            flex: 1;

            transition: all 0.3s ease;

            display: flex;

            align-items: center;

            justify-content: center;

            gap: 12px;

            box-shadow: 0 4px 12px rgba(44, 95, 45, 0.3);

            text-transform: uppercase;

            letter-spacing: 0.5px;

        }



        .btn-book-selected:hover:not(:disabled) {

            transform: translateY(-2px);

            box-shadow: 0 8px 20px rgba(44, 95, 45, 0.4);

        }



        .btn-book-selected:active:not(:disabled) {

            transform: translateY(0);

        }



        .btn-book-selected:disabled {

            opacity: 0.5;

            cursor: not-allowed;

            background: linear-gradient(135deg, #9e9e9e 0%, #757575 100%);

        }



        .btn-book-selected .material-icons-outlined {

            font-size: 22px;

        }



        .btn-cancel-modal {

            background: white;

            color: var(--trip-dark);

            border: 2px solid var(--trip-border);

            padding: 18px 32px;

            font-size: 1.05rem;

            font-weight: 600;

            border-radius: 12px;

            cursor: pointer;

            transition: all 0.3s ease;

            min-width: 140px;

        }



        .btn-cancel-modal:hover {

            background: var(--trip-secondary);

            border-color: var(--trip-gray);

        }



        /* Animations */

        @keyframes fadeIn {

            from { opacity: 0; }

            to { opacity: 1; }

        }



        @keyframes slideUp {

            from {

                transform: translateY(60px);

                opacity: 0;

            }

            to {

                transform: translateY(0);

                opacity: 1;

            }

        }



        @keyframes fadeInContent {

            from { 

                opacity: 0;

                transform: translateY(10px);

            }

            to { 

                opacity: 1;

                transform: translateY(0);

            }

        }



        /* Responsive Design */

        @media (max-width: 992px) {

            .spot-details {

                grid-template-columns: 1fr;

                gap: 40px;

            }

            

            .spot-info-card {

                position: static;

            }

            

            .hero-title {

                font-size: 2.5rem;

            }

        }



        @media (max-width: 768px) {

            .modal {

                padding: 10px;

            }



            .modal-content {

                max-height: 93vh;

                border-radius: 16px;

            }



            .modal-body {

                padding: 24px;

            }



            .modal-tabs {

                padding: 16px 16px 0;

            }



            .tab-btn {

                padding: 12px 16px;

                font-size: 0.85rem;

                flex-direction: column;

                gap: 4px;

            }



            .tab-btn .material-icons-outlined {

                font-size: 18px;

            }



            .guide-profile-section {

                padding: 24px;

            }



            .guide-profile-header {

                flex-direction: column;

                text-align: center;

            }



            .guide-avatar {

                width: 100px;

                height: 100px;

            }



            .guide-name {

                font-size: 1.5rem;

            }



            .guide-details-grid {

                grid-template-columns: 1fr;

            }



            .booking-info-grid {

                grid-template-columns: 1fr;

            .booking-actions {

                flex-direction: column;

            }



            .btn-cancel-modal {

                min-width: 100%;

            }



            .hero-title {

                font-size: 2rem;

            }

            

            .hero-subtitle {

                font-size: 1rem;

            }

            

            .photo-gallery {

                grid-template-columns: 1fr;

            }

            

            .cta-buttons {

                flex-direction: column;

                align-items: center;

            }

            

            .btn-primary, .btn-secondary {

                width: 100%;

                max-width: 300px;

            }

            

            .header-content {

                flex-direction: column;

                gap: 15px;

                text-align: center;

            }

        }

    </style>

</head>

<body>

    <!-- Page Header -->

    <div class="page-header">

        <div class="container">

            <div class="header-content">

                <div class="back-navigation">

                    <button class="btn-back" onclick="window.history.back()">

                        <span class="material-icons-outlined">arrow_back</span>

                        Back

                    </button>

                    <h1 class="page-title">Otso-Otso Falls</h1>

                </div>

            </div>

        </div>

    </div>



    <!-- Hero Section -->

    <section class="spot-hero">

        <div class="hero-content">

            <h1 class="hero-title">Otso-Otso Falls</h1>

            <p class="hero-subtitle">The Hidden Gem of Eight Cascading Waterfalls</p>

        </div>

    </section>



    <!-- Main Content -->

    <main class="container">

        <div class="spot-content">

            <div class="spot-details">

                <!-- Left Column: Content -->

                <div>

                    <h2 class="section-title">About Otso-Otso Falls</h2>

                    <p class="description">

                        Named after the Filipino word "otso" meaning eight, Otso-Otso Falls is a magnificent 

                        series of eight interconnected waterfalls located in the pristine wilderness of San Jose del Monte. 

                        This destination is part of a trio falls tour (Burong, Otso-Otso, and Kaytitinga Falls) with a mandatory tour guide 

                        fee of PHP 350 per group (max 5 pax), arranged through the tourism office.

                    </p>

                    

                    <!-- Safety Information -->

                    <div class="safety-notice">

                        <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 10px;">

                            <span class="material-icons-outlined" style="color: #ff9800;">warning</span>

                            <h4 style="margin: 0; color: #d84315; font-size: 1.1rem;">Important Safety Information</h4>

                        </div>

                        <p style="margin: 0; color: #5d4037; font-size: 0.95rem;">

                            The full trail to all eight waterfalls is recommended for experienced hikers only. 

                            Guided tours are mandatory for the complete circuit. Proper hiking equipment is essential.

                        </p>

                    </div>



                    <!-- Pool Information -->

                    <div class="pool-info">

                        <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 10px;">

                            <span class="material-icons-outlined" style="color: #2196f3;">pool</span>

                            <h4 style="margin: 0; color: #1565c0; font-size: 1.1rem;">Swimming Information</h4>

                        </div>

                        <p style="margin: 0; color: #0d47a1; font-size: 0.95rem;">

                            Natural swimming pools are available at waterfalls 1, 3, and 7. Water depths range from 

                            2-8 feet. Swimming is permitted in designated areas only during daylight hours.

                        </p>

                    </div>



                    <h3 class="section-subtitle">Waterfall Characteristics</h3>

                    <ul class="features-list">

                        <li><strong>Waterfall 1 (First Cascade):</strong> 15-foot drop with shallow swimming pool, accessible for beginners</li>

                        <li><strong>Waterfall 3 (Middle Beauty):</strong> 25-foot curtain waterfall with deep natural pool</li>

                        <li><strong>Waterfall 5 (Hidden Jewel):</strong> Requires moderate climbing, features rock formations</li>

                        <li><strong>Waterfall 7 (Grand Cascade):</strong> 40-foot majestic waterfall with the largest swimming area</li>

                        <li><strong>Waterfall 8 (Final Tier):</strong> 20-foot segmented waterfall with panoramic views</li>

                    </ul>



                    <h3 class="section-subtitle">Trail Specifications</h3>

                    <ul class="features-list">

                        <li><strong>Total Trail Length:</strong> 5 kilometers round trip for complete circuit</li>

                        <li><strong>Hike Duration:</strong> 4-6 hours to visit all eight waterfalls</li>

                        <li><strong>Elevation Gain:</strong> 300 meters cumulative across the trail</li>

                        <li><strong>Trail Features:</strong> River crossings, rock scrambling, forest paths</li>

                        <li><strong>Best Viewing Season:</strong> November to May (dry season)</li>

                        <li><strong>Minimum Group Size:</strong> 4 persons for guided tours</li>

                    </ul>



                </div>



                <!-- Right Column: Information Card -->

                <div class="spot-info-card">

                    <h3 class="section-subtitle" style="margin-top: 0;">Adventure Information</h3>

                    

                    <div class="info-grid">

                        <div class="info-item">

                            <span class="material-icons-outlined">schedule</span>

                            <div class="info-text">

                                <span class="info-label">Tour Duration</span>

                                <span class="info-value">4-6 Hours</span>

                            </div>

                        </div>

                        <div class="info-item">

                            <span class="material-icons-outlined">payments</span>

                            <div class="info-text">

                                <span class="info-label">Tour Guide Fee</span>

                                <span class="info-value">₱350 per group (max 5 pax)</span>

                            </div>

                        </div>

                        <div class="info-item">

                            <span class="material-icons-outlined">terrain</span>

                            <div class="info-text">

                                <span class="info-label">Difficulty Level</span>

                                <span class="info-value">Moderate to Hard</span>

                            </div>

                        </div>

                        <div class="info-item">

                            <span class="material-icons-outlined">height</span>

                            <div class="info-text">

                                <span class="info-label">Total Elevation</span>

                                <span class="info-value">300 meters</span>

                            </div>

                        </div>

                    </div>



                    <h3 class="section-subtitle">Operating Hours</h3>

                    <div class="info-item">

                        <span class="material-icons-outlined">access_time</span>

                        <div class="info-text">

                            <span class="info-label">Daily Schedule</span>

                            <span class="info-value">7:00 AM - 4:00 PM</span>

                        </div>

                    </div>



                    <h3 class="section-subtitle">Trio Falls Tour Information</h3>

                    <div style="background: #e8f5e9; padding: 15px; border-radius: 8px; margin: 15px 0;">

                        <div style="display: flex; align-items: center; gap: 10px;">

                            <span class="material-icons-outlined" style="color: #4caf50;">tour</span>

                            <span style="font-weight: 600; color: #2e7d32;">Trio Falls Adventure Package</span>

                        </div>

                        <p style="margin: 10px 0 0; font-size: 0.9rem; color: #1b5e20;">

                            This tour covers three waterfalls: Burong Falls, Otso-Otso Falls, and Kaytitinga Falls. 

                            The PHP 350 guide fee covers the entire group for all three destinations.

                        </p>

                    </div>



                    <h3 class="section-subtitle">Location</h3>

                    <div class="map-container">

                        <div id="google-map"></div>

                    </div>

                    <div class="map-info">

                        <h4>

                            <span class="material-icons-outlined" style="font-size: 1.2rem;">location_on</span>

                            Getting to Otso-Otso Falls

                        </h4>

                        <p>

                            Located in the upland area of San Jose del Monte, Bulacan. 

                            Accessible via private vehicle or hired transport from the city proper. 

                            Parking is available at the jump-off point.

                        </p>

                    </div>



                    <h3 class="section-subtitle">Essential Equipment</h3>

                    <ul style="margin: 15px 0; padding-left: 20px; color: var(--text-light); font-size: 0.95rem;">

                        <li>Waterproof hiking shoes</li>

                        <li>Swimwear and towel</li>

                        <li>Waterproof backpack</li>

                        <li>Minimum 2L water supply</li>

                        <li>Trail snacks and lunch</li>

                        <li>Dry bag for electronics</li>

                        <li>First aid kit</li>

                        <li>Emergency whistle</li>

                    </ul>



                    <button class="booking-btn glow-effect" onclick="checkAuth('Otso-Otso Falls')">

                        <span class="material-icons-outlined">event_available</span>

                        Book a Guide

                    </button>

                </div>

            </div>



            <!-- Call to Action Section -->

            <div class="cta-section">

                <h2 class="cta-title">Experience the Eight Wonders of Otso-Otso Falls</h2>

                <p class="cta-text">

                    Embark on an unforgettable journey through eight magnificent waterfalls. 

                    Our certified adventure guides will lead you through this natural wonderland 

                    while ensuring your safety and comfort.

                </p>

                <div class="cta-buttons">

                    <button class="btn-primary" onclick="checkAuth('Otso-Otso Falls')">

                        <span class="material-icons-outlined">event_available</span>

                        Book Waterfall Tour

                    </button>

                    <button class="btn-secondary" onclick="location.href='/coderistyarn2/sjdm-user/index.php#guides'">

                        <span class="material-icons-outlined">person_search</span>

                        Find Adventure Guide

                    </button>

                    <button class="btn-secondary" onclick="location.href='/coderistyarn2/sjdm-user/tourist-spots.php'">

                        <span class="material-icons-outlined">place</span>

                        View All Destinations

                    </button>

                </div>

            </div>

        </div>

    </main>



    <!-- Footer -->

    <footer class="footer">

        <div class="container">

            <p class="footer-text"> 2024 San Jose del Monte Tourism Office. All rights reserved.</p>

            <p class="footer-text">Promoting sustainable tourism and community development.</p>

        </div>

    </footer>


    <script>
        // Function to check authentication before booking
        function checkAuth(destination) {
            <?php if (isLoggedIn()): ?>
                // User is logged in, redirect to booking
                window.location.href = '/coderistyarn2/sjdm-user/book.php?destination=' + encodeURIComponent(destination);
            <?php else: ?>
                // User not logged in, redirect to login
                window.location.href = '../log-in.php';
            <?php endif; ?>
        }
        
        // Initialize Google Map

        function initMap() {

            // Otso-Otso Falls approximate coordinates (San Jose del Monte, Bulacan)

            const otsoOtsoFalls = { lat: 14.8304, lng: 121.0451 };

            

            const map = new google.maps.Map(document.getElementById('google-map'), {

                zoom: 14,

                center: otsoOtsoFalls,

                styles: [

                    {

                        featureType: 'landscape',

                        elementType: 'geometry',

                        stylers: [{ color: '#f5f5f5' }]

                    },

                    {

                        featureType: 'water',

                        elementType: 'geometry',

                        stylers: [{ color: '#c9e6ff' }]

                    },

                    {

                        featureType: 'poi.park',

                        elementType: 'geometry',

                        stylers: [{ color: '#e8f5e9' }]

                    }

                ]

            });



            const marker = new google.maps.Marker({

                position: otsoOtsoFalls,

                map: map,

                title: 'Otso-Otso Falls',

                animation: google.maps.Animation.DROP

            });



            const infoWindow = new google.maps.InfoWindow({

                content: `

                    <div style="padding: 10px; font-family: 'Inter', sans-serif;">

                        <h3 style="margin: 0 0 10px 0; color: #2c5f2d; font-size: 1.1rem;">Otso-Otso Falls</h3>

                        <p style="margin: 0; color: #666; font-size: 0.9rem;">

                            Eight cascading waterfalls in San Jose del Monte, Bulacan<br>

                            <strong>Tour Hours:</strong> 7:00 AM - 4:00 PM<br>

                            <strong>Guide Fee:</strong> ₱350 per group (max 5 pax)

                        </p>

                    </div>

                `

            });



            marker.addListener('click', () => {

                infoWindow.open(map, marker);

            });

        }



        // Smooth scroll for anchor links

        document.querySelectorAll('a[href^="#"]').forEach(anchor => {

            anchor.addEventListener('click', function (e) {

                e.preventDefault();

                const target = document.querySelector(this.getAttribute('href'));

                if (target) {

                    target.scrollIntoView({

                        behavior: 'smooth',

                        block: 'start'

                    });

                }

            });

        });

    </script>

</body>

</html>