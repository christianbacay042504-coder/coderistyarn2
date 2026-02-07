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
            --primary: #2c5f2d;
            --primary-light: #4a8c4a;
            --secondary: #f5f5f5;
            --accent: #ff9800;
            --text-dark: #333333;
            --text-light: #666666;
            --border: #e0e0e0;
            --shadow: 0 4px 12px rgba(0,0,0,0.08);
            --success: #4caf50;
            --danger: #f44336;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            background: #fafafa;
            color: var(--text-dark);
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .page-header {
            background: white;
            padding: 20px 0;
            border-bottom: 1px solid var(--border);
        }

        .header-content {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .back-navigation {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .btn-back {
            background: transparent;
            border: none;
            color: var(--primary);
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            padding: 10px 20px;
            border-radius: 8px;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .btn-back:hover {
            background: rgba(44, 95, 45, 0.1);
        }

        .page-title {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--primary);
            margin: 0;
        }

        .spot-hero {
            height: 400px;
            background: linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.5)), 
                       url('https://images.unsplash.com/photo-1551632811-561732d1e306?q=80&w=2070&auto=format&fit=crop');
            background-size: cover;
            background-position: center;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            color: white;
            margin: 30px auto 40px;
            border-radius: 50px;
            max-width: 1500px;
            width: calc(100% - 40px);
        }

        .hero-content {
            max-width: 800px;
            padding: 0 20px;
        }

        .hero-title {
            font-size: 3rem;
            font-weight: 700;
            margin-bottom: 15px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }

        .hero-subtitle {
            font-size: 1.2rem;
            opacity: 0.9;
            margin-bottom: 20px;
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
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 16px;
            padding-bottom: 15px;
            border-bottom: 2px solid var(--border);
        }

        .section-subtitle {
            font-size: 1.3rem;
            font-weight: 600;
            color: var(--text-dark);
            margin: 30px 0 15px;
        }

        .description {
            font-size: 1.05rem;
            line-height: 1.8;
            color: var(--text-light);
            margin-bottom: 25px;
        }

        .features-list {
            list-style: none;
            padding: 0;
            margin: 25px 0;
        }

        .features-list li {
            padding: 12px 0;
            padding-left: 35px;
            position: relative;
            font-size: 1rem;
            line-height: 1.6;
            color: var(--text-light);
        }

        .features-list li:before {
            content: '✓';
            position: absolute;
            left: 0;
            color: var(--primary);
            font-weight: bold;
            font-size: 1.2rem;
        }

        .spot-info-card {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: var(--shadow);
            border: 1px solid var(--border);
            position: sticky;
            top: 20px;
        }

        .info-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 20px;
            margin: 25px 0;
        }

        .info-item {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 15px;
            background: var(--secondary);
            border-radius: 10px;
            transition: transform 0.3s ease;
        }

        .info-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }

        .info-item .material-icons-outlined {
            color: var(--primary);
            font-size: 26px;
        }

        .info-text {
            flex: 1;
        }

        .info-label {
            display: block;
            font-size: 0.9rem;
            color: var(--text-light);
            margin-bottom: 4px;
        }

        .info-value {
            display: block;
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--text-dark);
        }

        .photo-gallery {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 25px;
            margin: 40px 0;
        }

        .gallery-item {
            border-radius: 12px;
            overflow: hidden;
            box-shadow: var(--shadow);
            transition: transform 0.3s ease;
            height: 220px;
        }

        .gallery-item:hover {
            transform: translateY(-5px);
        }

        .gallery-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }

        .gallery-item:hover img {
            transform: scale(1.05);
        }

        .cta-section {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
            color: white;
            padding: 50px 40px;
            border-radius: 15px;
            text-align: center;
            margin: 50px 0;
            box-shadow: var(--shadow);
        }

        .cta-title {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 20px;
        }

        .cta-text {
            font-size: 1.1rem;
            opacity: 0.9;
            max-width: 700px;
            margin: 0 auto 30px;
            line-height: 1.6;
        }

        .cta-buttons {
            display: flex;
            gap: 20px;
            justify-content: center;
            flex-wrap: wrap;
        }

        .btn-primary {
            background: white;
            color: var(--primary);
            border: none;
            padding: 16px 32px;
            font-size: 1.1rem;
            font-weight: 600;
            border-radius: 10px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 10px;
            transition: all 0.3s ease;
            min-width: 180px;
            justify-content: center;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0,0,0,0.15);
        }

        .btn-secondary {
            background: transparent;
            color: white;
            border: 2px solid white;
            padding: 14px 32px;
            font-size: 1.1rem;
            font-weight: 600;
            border-radius: 10px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 10px;
            transition: all 0.3s ease;
            min-width: 180px;
            justify-content: center;
        }

        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.1);
            transform: translateY(-2px);
        }

        .footer {
            background: white;
            padding: 30px 0;
            text-align: center;
            border-top: 1px solid var(--border);
            margin-top: 60px;
        }

        .footer-text {
            color: var(--text-light);
            font-size: 0.9rem;
            margin: 5px 0;
        }

        .safety-notice {
            background: #fff8e1;
            border-left: 4px solid #ff9800;
            padding: 20px;
            border-radius: 8px;
            margin: 25px 0;
        }

        .pool-info {
            background: #e3f2fd;
            border-left: 4px solid #2196f3;
            padding: 20px;
            border-radius: 8px;
            margin: 25px 0;
        }

        /* Enhanced Book Button Styles */
        .booking-btn {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
            color: white;
            border: none;
            padding: 16px 32px;
            font-size: 1.1rem;
            font-weight: 700;
            border-radius: 12px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            transition: all 0.3s ease;
            min-width: 200px;
            width: 100%;
            margin: 20px 0;
            text-transform: uppercase;
            letter-spacing: 1px;
            box-shadow: 0 6px 20px rgba(44, 95, 45, 0.3);
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
            box-shadow: 0 10px 25px rgba(44, 95, 45, 0.4);
            background: linear-gradient(135deg, var(--primary-light) 0%, #3a7c3a 100%);
        }

        .booking-btn:hover::before {
            left: 100%;
        }

        .booking-btn .material-icons-outlined {
            font-size: 24px;
            transition: transform 0.3s ease;
        }

        .booking-btn:hover .material-icons-outlined {
            transform: translateX(5px);
        }

        .booking-btn:active {
            transform: translateY(-1px);
            box-shadow: 0 5px 15px rgba(44, 95, 45, 0.3);
        }

        .glow-effect {
            animation: glowPulse 2s infinite alternate;
        }

        /* Google Map Styles */
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

        /* Modal Container */
        .modal-content {
            background: white;
            border-radius: 16px;
            max-width: 650px;
            width: 100%;
            max-height: 85vh;
            overflow: hidden;
            animation: slideUp 0.4s cubic-bezier(0.16, 1, 0.3, 1);
            box-shadow: 0 16px 32px rgba(0, 0, 0, 0.12);
            display: flex;
            flex-direction: column;
        }

        /* Modal Header */
        .modal-header {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
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
            padding: 16px 28px;
            font-size: 0.95rem;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 10px;
            transition: all 0.3s ease;
            border-radius: 12px 12px 0 0;
            letter-spacing: 0.5px;
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
            color: var(--primary);
        }

        .tab-btn .material-icons-outlined {
            font-size: 20px;
        }

        .modal-close {
            position: absolute;
            top: 24px;
            right: 24px;
            background: rgba(255, 255, 255, 0.2);
            border: none;
            color: white;
            width: 44px;
            height: 44px;
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
            font-size: 24px;
        }

        /* Modal Body */
        .modal-body {
            padding: 24px;
            overflow-y: auto;
            flex: 1;
        }

        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
            animation: fadeInContent 0.3s ease;
        }

        /* Guide Profile Section */
        .guide-profile-section {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-radius: 20px;
            padding: 24px;
            margin-bottom: 24px;
            border: 1px solid var(--border);
        }

        .guide-profile-header {
            display: flex;
            align-items: center;
            gap: 20px;
            margin-bottom: 20px;
            padding-bottom: 20px;
            border-bottom: 2px solid var(--border);
        }

        .guide-avatar {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            border: 4px solid var(--primary);
            object-fit: cover;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
        }

        .guide-profile-info {
            flex: 1;
        }

        .guide-name {
            font-size: 1.75rem;
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 6px;
        }

        .guide-specialty {
            color: var(--text-light);
            font-size: 1rem;
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .guide-specialty .material-icons-outlined {
            font-size: 18px;
            color: var(--accent);
        }

        .guide-rating {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .stars {
            display: flex;
            gap: 2px;
        }

        .star {
            color: #ffc107;
            font-size: 20px;
        }

        .rating-score {
            font-weight: 600;
            color: var(--text-dark);
            margin-left: 4px;
        }

        .rating-count {
            color: var(--text-light);
            font-size: 0.9rem;
        }

        /* Guide Details Grid */
        .guide-details-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 12px;
        }

        .guide-detail-card {
            background: white;
            padding: 16px;
            border-radius: 12px;
            border: 1px solid var(--border);
            display: flex;
            align-items: center;
            gap: 12px;
            transition: all 0.3s ease;
        }

        .guide-detail-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.08);
        }

        .detail-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            flex-shrink: 0;
        }

        .detail-icon .material-icons-outlined {
            font-size: 24px;
        }

        .detail-content {
            flex: 1;
        }

        .detail-label {
            font-size: 0.85rem;
            color: var(--text-light);
            margin-bottom: 4px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .detail-value {
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--text-dark);
        }

        /* Booking Information Section */
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
            border: 2px solid var(--border);
            border-radius: 16px;
            padding: 20px;
            transition: all 0.3s ease;
        }

        .booking-info-card:hover {
            border-color: var(--primary);
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
            color: var(--text-dark);
            line-height: 1.4;
        }

        /* Calendar Section */
        .calendar-section {
            background: var(--secondary);
            border-radius: 16px;
            padding: 20px;
            margin-bottom: 24px;
        }

        .calendar-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 16px;
            padding-bottom: 12px;
            border-bottom: 2px solid var(--border);
        }

        .calendar-title {
            font-size: 1.2rem;
            font-weight: 700;
            color: var(--primary);
            margin: 0;
        }

        .calendar-nav {
            display: flex;
            gap: 8px;
        }

        .calendar-nav button {
            background: var(--primary);
            color: white;
            border: none;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }

        .calendar-nav button:hover {
            background: var(--primary-light);
            transform: scale(1.1);
        }

        .calendar-nav button:active {
            transform: scale(0.95);
        }

        .calendar-grid {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 8px;
            margin-bottom: 16px;
        }

        .calendar-day-header {
            text-align: center;
            font-weight: 600;
            color: var(--text-light);
            padding: 8px;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .calendar-day {
            aspect-ratio: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.3s ease;
            font-weight: 600;
            border: 2px solid transparent;
            font-size: 0.95rem;
            position: relative;
        }

        .calendar-day.available {
            background: white;
            color: var(--primary);
            border-color: #c8e6c9;
        }

        .calendar-day.available:hover {
            background: #e8f5e9;
            border-color: var(--success);
            transform: scale(1.05);
            box-shadow: 0 4px 12px rgba(76, 175, 80, 0.2);
        }

        .calendar-day.unavailable {
            background: #ffebee;
            color: #c62828;
            cursor: not-allowed;
            opacity: 0.5;
        }

        .calendar-day.selected {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
            color: white;
            transform: scale(1.1);
            box-shadow: 0 6px 16px rgba(44, 95, 45, 0.4);
            border-color: var(--primary);
        }

        .calendar-day.today::after {
            content: '';
            position: absolute;
            bottom: 4px;
            left: 50%;
            transform: translateX(-50%);
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: var(--accent);
        }

        /* Calendar Legend */
        .calendar-legend {
            display: flex;
            gap: 20px;
            justify-content: center;
            padding: 16px;
            background: white;
            border-radius: 12px;
            flex-wrap: wrap;
        }

        .legend-item {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.9rem;
            color: var(--text-dark);
        }

        .legend-color {
            width: 24px;
            height: 24px;
            border-radius: 6px;
            border: 2px solid var(--border);
        }

        .legend-color.available {
            background: white;
            border-color: #c8e6c9;
        }

        .legend-color.unavailable {
            background: #ffebee;
            border-color: #ffcdd2;
        }

        .legend-color.selected {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
            border-color: var(--primary);
        }

        /* Booking Actions */
        .booking-actions {
            display: flex;
            gap: 16px;
            padding-top: 20px;
            border-top: 2px solid var(--border);
        }

        .btn-book-selected {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
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
            color: var(--text-dark);
            border: 2px solid var(--border);
            padding: 18px 32px;
            font-size: 1.05rem;
            font-weight: 600;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.3s ease;
            min-width: 140px;
        }

        .btn-cancel-modal:hover {
            background: var(--secondary);
            border-color: var(--text-light);
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
                max-height: 98vh;
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
            }

            .calendar-grid {
                gap: 6px;
            }

            .calendar-day {
                font-size: 0.85rem;
                border-radius: 8px;
            }

            .calendar-day-header {
                font-size: 0.75rem;
                padding: 8px;
            }

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

                    <!-- Photo Gallery -->
                    <h3 class="section-subtitle">Gallery</h3>
                    <div class="photo-gallery">
                        <div class="gallery-item">
                            <img src="https://images.unsplash.com/photo-1551632811-561732d1e306?q=80&w=2070&auto=format&fit=crop" alt="Waterfall Cascade">
                        </div>
                        <div class="gallery-item">
                            <img src="https://images.unsplash.com/photo-1433086173841-718858a6022c?q=80&w=1887&auto=format&fit=crop" alt="Natural Pool">
                        </div>
                        <div class="gallery-item">
                            <img src="https://images.unsplash.com/photo-1509316785289-025f5b846b35?q=80&w=2076&auto=format&fit=crop" alt="Forest Trail">
                        </div>
                    </div>
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

                    <button class="booking-btn glow-effect" onclick="openBookingModal()">
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
                    <button class="btn-primary" onclick="openBookingModal()">
                        <span class="material-icons-outlined">calendar_month</span>
                        Book Complete Tour
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
            <p class="footer-text">© 2024 San Jose del Monte Tourism Office. All rights reserved.</p>
            <p class="footer-text">Promoting sustainable tourism and community development.</p>
        </div>
    </footer>

    <!-- Tour Guide Booking Modal -->
    <div id="bookingModal" class="modal">
        <div class="modal-content">
            <!-- Modal Header with Tabs -->
            <div class="modal-header">
                <div class="modal-tabs">
                    <button class="tab-btn active" onclick="switchTab('guide')">
                        <span class="material-icons-outlined">person</span>
                        <span>Guide Profileeee</span>
                    </button>
                    <button class="tab-btn" onclick="switchTab('booking')">
                        <span class="material-icons-outlined">info</span>
                        <span>Tour Info</span>
                    </button>
                </div>
                <button class="modal-close" onclick="closeBookingModal()">
                    <span class="material-icons-outlined">close</span>
                </button>
            </div>

            <!-- Modal Body -->
            <div class="modal-body">
                <!-- Guide Profile Tab Content -->
                <div id="guideTabContent" class="tab-content active">
                    <!-- Guide Profile Section -->
                    <div class="guide-profile-section">
                        <div class="guide-profile-header">
                            <img src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?q=80&w=200&auto=format&fit=crop" alt="Tour Guide" class="guide-avatar">
                            <div class="guide-profile-info">
                                <h3 class="guide-name">Carlos Mendoza</h3>
                                <p class="guide-specialty">
                                    <span class="material-icons-outlined">hiking</span>
                                    Adventure Tours
                                </p>
                                <div class="guide-rating">
                                    <div class="stars">
                                        <span class="star">★</span>
                                        <span class="star">★</span>
                                        <span class="star">★</span>
                                        <span class="star">★</span>
                                        <span class="star">★</span>
                                    </div>
                                    <span class="rating-score">4.8</span>
                                    <span class="rating-count">(89 reviews)</span>
                                </div>
                            </div>
                        </div>

                        <div class="guide-details-grid">
                            <div class="guide-detail-card">
                                <div class="detail-icon">
                                    <span class="material-icons-outlined">wc</span>
                                </div>
                                <div class="detail-content">
                                    <div class="detail-label">Gender</div>
                                    <div class="detail-value">Male</div>
                                </div>
                            </div>
                            <div class="guide-detail-card">
                                <div class="detail-icon">
                                    <span class="material-icons-outlined">language</span>
                                </div>
                                <div class="detail-content">
                                    <div class="detail-label">Languages</div>
                                    <div class="detail-value">English, Filipino</div>
                                </div>
                            </div>
                            <div class="guide-detail-card">
                                <div class="detail-icon">
                                    <span class="material-icons-outlined">verified</span>
                                </div>
                                <div class="detail-content">
                                    <div class="detail-label">Experience</div>
                                    <div class="detail-value">8+ Years</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Booking Information Section -->
                    <div class="booking-info-section">
                        <h3 class="section-title">
                            <span class="material-icons-outlined">event</span>
                            Tour Details
                        </h3>
                        <div class="booking-info-grid">
                            <div class="booking-info-card">
                                <div class="booking-info-header">
                                    <span class="material-icons-outlined">schedule</span>
                                </div>
                                <div class="booking-info-title">Tour Duration</div>
                                <div class="booking-info-value">4-6 Hours</div>
                            </div>
                            <div class="booking-info-card">
                                <div class="booking-info-header">
                                    <span class="material-icons-outlined">payments</span>
                                </div>
                                <div class="booking-info-title">Guide Fee</div>
                                <div class="booking-info-value">₱350 per group</div>
                            </div>
                            <div class="booking-info-card">
                                <div class="booking-info-header">
                                    <span class="material-icons-outlined">terrain</span>
                                </div>
                                <div class="booking-info-title">Difficulty Level</div>
                                <div class="booking-info-value">Moderate to Hard</div>
                            </div>
                            <div class="booking-info-card">
                                <div class="booking-info-header">
                                    <span class="material-icons-outlined">height</span>
                                </div>
                                <div class="booking-info-title">Total Elevation</div>
                                <div class="booking-info-value">300 meters</div>
                            </div>
                        </div>
                    </div>

                    <!-- Calendar Section -->
                    <div class="calendar-section">
                        <div class="calendar-header">
                            <h3 class="calendar-title">Select Your Date</h3>
                            <div class="calendar-nav">
                                <button onclick="changeMonth(-1)" title="Previous Month">
                                    <span class="material-icons-outlined">chevron_left</span>
                                </button>
                                <button onclick="changeMonth(1)" title="Next Month">
                                    <span class="material-icons-outlined">chevron_right</span>
                                </button>
                            </div>
                        </div>
                        <div id="calendarGrid" class="calendar-grid">
                            <!-- Calendar will be generated by JavaScript -->
                        </div>
                        <div class="calendar-legend">
                            <div class="legend-item">
                                <div class="legend-color available"></div>
                                <span>Available</span>
                            </div>
                            <div class="legend-item">
                                <div class="legend-color unavailable"></div>
                                <span>Unavailable</span>
                            </div>
                            <div class="legend-item">
                                <div class="legend-color selected"></div>
                                <span>Selected</span>
                            </div>
                        </div>
                    </div>

                    <!-- Booking Actions -->
                    <div class="booking-actions">
                        <button class="btn-book-selected" id="bookSelectedBtn" onclick="bookSelectedDate()" disabled>
                            <span class="material-icons-outlined">calendar_today</span>
                            Select a Date to Continue
                        </button>
                        <button class="btn-cancel-modal" onclick="closeBookingModal()">
                            Cancel
                        </button>
                    </div>
                </div>

                <!-- Tour Info Tab Content -->
                <div id="bookingTabContent" class="tab-content">
                    <h3 class="section-title">
                        <span class="material-icons-outlined">info</span>
                        Tour Information & Policies
                    </h3>

                    <!-- What's Included Section -->
                    <div class="info-box" style="background: #e8f5e9; border-left: 4px solid #4caf50; padding: 24px; border-radius: 12px; margin-bottom: 24px;">
                        <h4 style="color: #2e7d32; margin: 0 0 16px 0; font-size: 1.2rem; display: flex; align-items: center; gap: 10px;">
                            <span class="material-icons-outlined">check_circle</span>
                            What's Included
                        </h4>
                        <ul style="margin: 0; padding-left: 20px; color: #1b5e20; line-height: 2;">
                            <li>Professional certified tour guide for entire duration</li>
                            <li>Safety briefing and trail orientation</li>
                            <li>Access to all eight waterfalls in the circuit</li>
                            <li>Emergency first aid kit and communication device</li>
                            <li>Trail navigation and route guidance</li>
                            <li>Local insights and waterfall information</li>
                        </ul>
                    </div>

                    <!-- What to Bring Section -->
                    <div class="info-box" style="background: #fff3e0; border-left: 4px solid #ff9800; padding: 24px; border-radius: 12px; margin-bottom: 24px;">
                        <h4 style="color: #e65100; margin: 0 0 16px 0; font-size: 1.2rem; display: flex; align-items: center; gap: 10px;">
                            <span class="material-icons-outlined">backpack</span>
                            What to Bring
                        </h4>
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px;">
                            <div>
                                <strong style="color: #e65100; display: block; margin-bottom: 8px;">Essential Items:</strong>
                                <ul style="margin: 0; padding-left: 20px; color: #5d4037; line-height: 1.8;">
                                    <li>Waterproof hiking shoes</li>
                                    <li>Swimwear & towel</li>
                                    <li>2L water minimum</li>
                                    <li>Energy snacks & lunch</li>
                                </ul>
                            </div>
                            <div>
                                <strong style="color: #e65100; display: block; margin-bottom: 8px;">Recommended:</strong>
                                <ul style="margin: 0; padding-left: 20px; color: #5d4037; line-height: 1.8;">
                                    <li>Waterproof backpack</li>
                                    <li>Dry bag for electronics</li>
                                    <li>Sun protection (hat, sunscreen)</li>
                                    <li>Insect repellent</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Safety Requirements Section -->
                    <div class="info-box" style="background: #ffebee; border-left: 4px solid #f44336; padding: 24px; border-radius: 12px; margin-bottom: 24px;">
                        <h4 style="color: #c62828; margin: 0 0 16px 0; font-size: 1.2rem; display: flex; align-items: center; gap: 10px;">
                            <span class="material-icons-outlined">warning</span>
                            Safety Requirements & Restrictions
                        </h4>
                        <ul style="margin: 0; padding-left: 20px; color: #5d4037; line-height: 2;">
                            <li><strong>Minimum Age:</strong> 12 years old (with parental supervision)</li>
                            <li><strong>Fitness Level:</strong> Moderate to good physical condition required</li>
                            <li><strong>Health Conditions:</strong> Not recommended for pregnant women, individuals with heart conditions, or recent injuries</li>
                            <li><strong>Swimming Ability:</strong> Basic swimming skills recommended (life vest available upon request)</li>
                            <li><strong>Weather Conditions:</strong> Tours may be cancelled during heavy rain or flood warnings</li>
                            <li><strong>Group Size:</strong> Minimum 4 persons, maximum 5 persons per guide</li>
                        </ul>
                    </div>

                    <!-- Booking Terms Section -->
                    <div class="info-box" style="background: #e3f2fd; border-left: 4px solid #2196f3; padding: 24px; border-radius: 12px; margin-bottom: 24px;">
                        <h4 style="color: #1565c0; margin: 0 0 16px 0; font-size: 1.2rem; display: flex; align-items: center; gap: 10px;">
                            <span class="material-icons-outlined">description</span>
                            Booking Terms & Conditions
                        </h4>
                        <div style="color: #0d47a1; line-height: 1.8;">
                            <p style="margin: 0 0 12px 0;"><strong>Payment:</strong> Full payment of ₱350 guide fee required upon booking confirmation. Cash payment to guide on tour day.</p>
                            <p style="margin: 0 0 12px 0;"><strong>Confirmation:</strong> Booking must be confirmed at least 24 hours in advance.</p>
                            <p style="margin: 0 0 12px 0;"><strong>Meeting Point:</strong> Tourism Office parking area, 15 minutes before tour start time (7:00 AM).</p>
                            <p style="margin: 0 0 12px 0;"><strong>Late Arrival:</strong> Tours depart on time. Late arrivals may result in shortened tour or cancellation without refund.</p>
                            <p style="margin: 0;"><strong>Additional Fees:</strong> Environmental fee (₱30/person) and parking fee (₱50/vehicle) paid separately on-site.</p>
                        </div>
                    </div>

                    <!-- Cancellation Policy Section -->
                    <div class="info-box" style="background: #f3e5f5; border-left: 4px solid #9c27b0; padding: 24px; border-radius: 12px; margin-bottom: 24px;">
                        <h4 style="color: #6a1b9a; margin: 0 0 16px 0; font-size: 1.2rem; display: flex; align-items: center; gap: 10px;">
                            <span class="material-icons-outlined">event_busy</span>
                            Cancellation & Rescheduling Policy
                        </h4>
                        <div style="color: #4a148c; line-height: 1.8;">
                            <p style="margin: 0 0 12px 0;"><strong>Free Cancellation:</strong> Cancel up to 48 hours before tour for full refund.</p>
                            <p style="margin: 0 0 12px 0;"><strong>24-48 Hours:</strong> 50% refund if cancelled 24-48 hours before tour.</p>
                            <p style="margin: 0 0 12px 0;"><strong>Less than 24 Hours:</strong> No refund for cancellations within 24 hours.</p>
                            <p style="margin: 0 0 12px 0;"><strong>Weather Cancellation:</strong> Full refund or free rescheduling if tour is cancelled due to weather by guide/tourism office.</p>
                            <p style="margin: 0 0 12px 0;"><strong>Rescheduling:</strong> Free rescheduling available once up to 48 hours before tour.</p>
                            <p style="margin: 0;"><strong>No-Show:</strong> No refund for no-shows without prior notice.</p>
                        </div>
                    </div>

                    <!-- Important Notes Section -->
                    <div class="info-box" style="background: #fce4ec; border-left: 4px solid #e91e63; padding: 24px; border-radius: 12px; margin-bottom: 24px;">
                        <h4 style="color: #880e4f; margin: 0 0 16px 0; font-size: 1.2rem; display: flex; align-items: center; gap: 10px;">
                            <span class="material-icons-outlined">priority_high</span>
                            Important Notes
                        </h4>
                        <ul style="margin: 0; padding-left: 20px; color: #880e4f; line-height: 2;">
                            <li>This is part of the <strong>Trio Falls Tour</strong> (Burong, Otso-Otso, and Kaytitinga Falls)</li>
                            <li>The complete circuit to all 8 waterfalls takes 4-6 hours depending on fitness level</li>
                            <li>Trail involves river crossings, rock scrambling, and steep sections</li>
                            <li>Swimming is only allowed at designated waterfalls (1, 3, and 7)</li>
                            <li>Guide's instructions must be followed at all times for safety</li>
                            <li>Littering is strictly prohibited - carry all trash out</li>
                            <li>Photography is allowed but drones require special permission</li>
                        </ul>
                    </div>

                    <!-- Contact Information Section -->
                    <div class="info-box" style="background: linear-gradient(135deg, #f5f5f5 0%, #e0e0e0 100%); border: 2px solid var(--border); padding: 24px; border-radius: 12px;">
                        <h4 style="color: var(--primary); margin: 0 0 16px 0; font-size: 1.2rem; display: flex; align-items: center; gap: 10px;">
                            <span class="material-icons-outlined">contact_support</span>
                            Questions or Concerns?
                        </h4>
                        <p style="margin: 0 0 12px 0; color: var(--text-dark); line-height: 1.8;">
                            For inquiries about this tour or to make special arrangements, please contact:
                        </p>
                        <div style="display: flex; flex-direction: column; gap: 12px; color: var(--text-dark);">
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <span class="material-icons-outlined" style="color: var(--primary);">phone</span>
                                <span><strong>Tourism Office:</strong> (044) 123-4567</span>
                            </div>
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <span class="material-icons-outlined" style="color: var(--primary);">email</span>
                                <span><strong>Email:</strong> tourism@sjdm.gov.ph</span>
                            </div>
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <span class="material-icons-outlined" style="color: var(--primary);">schedule</span>
                                <span><strong>Office Hours:</strong> Mon-Fri, 8:00 AM - 5:00 PM</span>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div style="display: flex; gap: 16px; margin-top: 32px; padding-top: 24px; border-top: 2px solid var(--border);">
                        <button class="btn-book-selected" onclick="switchTab('guide')" style="background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);">
                            <span class="material-icons-outlined">arrow_back</span>
                            Back to Booking
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Modal and Calendar JavaScript
        let currentMonth = new Date().getMonth();
        let currentYear = new Date().getFullYear();
        let selectedDate = null;

        function openBookingModal() {
            document.getElementById('bookingModal').classList.add('show');
            document.body.style.overflow = 'hidden';
            generateCalendar();
        }

        function closeBookingModal() {
            document.getElementById('bookingModal').classList.remove('show');
            document.body.style.overflow = 'auto';
            selectedDate = null;
            updateBookButton();
        }

        function switchTab(tabName) {
            // Update tab buttons
            document.querySelectorAll('.tab-btn').forEach(btn => {
                btn.classList.remove('active');
            });
            event.target.closest('.tab-btn').classList.add('active');

            // Update tab content
            document.querySelectorAll('.tab-content').forEach(content => {
                content.classList.remove('active');
            });
            
            if (tabName === 'guide') {
                document.getElementById('guideTabContent').classList.add('active');
            } else if (tabName === 'booking') {
                document.getElementById('bookingTabContent').classList.add('active');
            }
        }

        function generateCalendar() {
            const firstDay = new Date(currentYear, currentMonth, 1).getDay();
            const daysInMonth = new Date(currentYear, currentMonth + 1, 0).getDate();
            const today = new Date();
            
            const monthNames = ['January', 'February', 'March', 'April', 'May', 'June',
                               'July', 'August', 'September', 'October', 'November', 'December'];
            
            document.querySelector('.calendar-title').textContent = `${monthNames[currentMonth]} ${currentYear}`;
            
            const calendarGrid = document.getElementById('calendarGrid');
            calendarGrid.innerHTML = '';
            
            // Day headers
            const dayHeaders = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
            dayHeaders.forEach(day => {
                const dayHeader = document.createElement('div');
                dayHeader.className = 'calendar-day-header';
                dayHeader.textContent = day;
                calendarGrid.appendChild(dayHeader);
            });
            
            // Empty cells for days before month starts
            for (let i = 0; i < firstDay; i++) {
                const emptyDay = document.createElement('div');
                calendarGrid.appendChild(emptyDay);
            }
            
            // Days of the month
            for (let day = 1; day <= daysInMonth; day++) {
                const dayElement = document.createElement('div');
                dayElement.className = 'calendar-day';
                dayElement.textContent = day;
                
                const currentDate = new Date(currentYear, currentMonth, day);
                const isToday = currentDate.toDateString() === today.toDateString();
                const isPast = currentDate < today && !isToday;
                
                // All future dates and today are available
                const isAvailable = !isPast;
                
                if (isToday) {
                    dayElement.classList.add('today');
                }
                
                if (isPast) {
                    dayElement.classList.add('unavailable');
                } else {
                    dayElement.classList.add('available');
                    dayElement.onclick = () => selectDate(currentYear, currentMonth, day);
                }
                
                calendarGrid.appendChild(dayElement);
            }
        }

        function selectDate(year, month, day) {
            // Remove previous selection
            document.querySelectorAll('.calendar-day.selected').forEach(el => {
                el.classList.remove('selected');
            });
            
            // Add selection to clicked date
            const days = document.querySelectorAll('.calendar-day.available');
            days.forEach(dayEl => {
                if (dayEl.textContent == day && !dayEl.classList.contains('selected')) {
                    dayEl.classList.add('selected');
                }
            });
            
            selectedDate = new Date(year, month, day);
            updateBookButton();
        }

        function updateBookButton() {
            const bookBtn = document.getElementById('bookSelectedBtn');
            if (selectedDate) {
                const options = { weekday: 'short', month: 'short', day: 'numeric', year: 'numeric' };
                const formattedDate = selectedDate.toLocaleDateString('en-US', options);
                bookBtn.innerHTML = `<span class="material-icons-outlined">check_circle</span>Book for ${formattedDate}`;
                bookBtn.disabled = false;
            } else {
                bookBtn.innerHTML = '<span class="material-icons-outlined">calendar_today</span>Select a Date to Continue';
                bookBtn.disabled = true;
            }
        }

        function changeMonth(direction) {
            const today = new Date();
            currentMonth += direction;
            
            if (currentMonth < 0) {
                currentMonth = 11;
                currentYear--;
            } else if (currentMonth > 11) {
                currentMonth = 0;
                currentYear++;
            }
            
            // Prevent going to past months
            if (currentYear < today.getFullYear() || 
                (currentYear === today.getFullYear() && currentMonth < today.getMonth())) {
                currentMonth = today.getMonth();
                currentYear = today.getFullYear();
            }
            
            generateCalendar();
        }

        function bookSelectedDate() {
            if (selectedDate) {
                // Redirect to booking page with destination, date, and Carlos Mendoza (ID 1) as guide
                const dateStr = selectedDate.toISOString().split('T')[0]; // Format as YYYY-MM-DD
                const bookingUrl = `/coderistyarn2/sjdm-user/book.php?destination=Otso-Otso Falls&date=${dateStr}&guide=1`;
                window.location.href = bookingUrl;
            }
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('bookingModal');
            if (event.target === modal) {
                closeBookingModal();
            }
        }

        // Close modal with Escape key
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                closeBookingModal();
            }
        });

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