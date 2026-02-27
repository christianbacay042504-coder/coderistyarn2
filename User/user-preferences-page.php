<?php
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../config/database.php';

// Check if user is logged in
if (!isLoggedIn()) {
    header('Location: ../log-in.php');
    exit();
}

// Check if user has already set preferences
$conn = getDatabaseConnection();
if ($conn) {
    $stmt = $conn->prepare("SELECT preferences_set FROM users WHERE id = ?");
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();
    closeDatabaseConnection($conn);
    
    // If preferences are already set, redirect to dashboard
    if ($user && $user['preferences_set'] == 1) {
        header('Location: user-index.php');
        exit();
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    
    try {
        $selectedCategories = $_POST['categories'] ?? [];
        $selectedPreset = $_POST['preset_comment'] ?? '';
        $customComment = $_POST['custom_comment'] ?? '';
        
        if (empty($selectedCategories)) {
            echo json_encode(['success' => false, 'message' => 'Please select at least one category']);
            exit();
        }
        
        if (count($selectedCategories) > 8) {
            echo json_encode(['success' => false, 'message' => 'You can select up to 8 categories only']);
            exit();
        }
        
        $conn = getDatabaseConnection();
        if (!$conn) {
            echo json_encode(['success' => false, 'message' => 'Database connection failed']);
            exit();
        }
        
        // Start transaction
        $conn->begin_transaction();
        
        // Clear existing preferences for this user
        $deleteStmt = $conn->prepare("DELETE FROM user_preferences WHERE user_id = ?");
        $deleteStmt->bind_param("i", $_SESSION['user_id']);
        $deleteStmt->execute();
        $deleteStmt->close();
        
        // Insert new preferences
        $insertStmt = $conn->prepare("INSERT INTO user_preferences (user_id, category) VALUES (?, ?)");
        foreach ($selectedCategories as $category) {
            $insertStmt->bind_param("is", $_SESSION['user_id'], $category);
            $insertStmt->execute();
        }
        $insertStmt->close();
        
        // Save user comment/feedback
        if (!empty($selectedPreset) || !empty($customComment)) {
            $commentText = '';
            $commentType = '';
            
            if (!empty($selectedPreset)) {
                // Map preset keys to readable text
                $presetTexts = [
                    'adventure-seeker' => 'Adventure Seeker: I love thrilling activities, hiking, and exploring off-the-beaten-path destinations',
                    'nature-lover' => 'Nature Enthusiast: I enjoy peaceful natural settings, wildlife, and scenic photography spots',
                    'family-traveler' => 'Family Traveler: I\'m looking for kid-friendly activities and educational experiences for the whole family',
                    'budget-conscious' => 'Budget-Conscious: I prefer affordable options, free attractions, and value-for-money experiences',
                    'luxury-traveler' => 'Luxury Traveler: I enjoy premium experiences, comfort, and exclusive tours with personalized service',
                    'photography-buff' => 'Photography Buff: I\'m always looking for Instagram-worthy spots and perfect photo opportunities'
                ];
                
                $commentText = $presetTexts[$selectedPreset] ?? $selectedPreset;
                $commentType = 'preset';
            }
            
            if (!empty($customComment)) {
                if (!empty($commentText)) {
                    $commentText .= ' | Custom: ' . $customComment;
                    $commentType = 'both';
                } else {
                    $commentText = $customComment;
                    $commentType = 'custom';
                }
            }
            
            // Insert or update user comment
            $commentStmt = $conn->prepare("INSERT INTO user_comments (user_id, comment_text, comment_type, created_at) VALUES (?, ?, ?, NOW()) ON DUPLICATE KEY UPDATE comment_text = VALUES(comment_text), comment_type = VALUES(comment_type), updated_at = NOW()");
            $commentStmt->bind_param("iss", $_SESSION['user_id'], $commentText, $commentType);
            $commentStmt->execute();
            $commentStmt->close();
        }
        
        // Mark preferences as set for the user
        $updateStmt = $conn->prepare("UPDATE users SET preferences_set = 1 WHERE id = ?");
        $updateStmt->bind_param("i", $_SESSION['user_id']);
        $updateStmt->execute();
        $updateStmt->close();
        
        // Commit transaction
        $conn->commit();
        closeDatabaseConnection($conn);
        
        echo json_encode(['success' => true, 'message' => 'Preferences saved successfully']);
        
    } catch (Exception $e) {
        if (isset($conn)) {
            $conn->rollback();
            closeDatabaseConnection($conn);
        }
        echo json_encode(['success' => false, 'message' => 'Error saving preferences: ' . $e->getMessage()]);
    }
    exit();
}

// Fetch available categories (tourist spot categories) with AI intelligence
// Fetch available categories based on actual SJDM tourist spots database
$categories = [
    [
        'name' => 'nature',
        'display_name' => 'Nature & Waterfalls',
        'icon' => 'forest',
        'description' => 'Mt. Balagbag (4.8⭐), Kaytitinga Falls (4.6⭐), Otso-Otso Falls - Premier hiking and waterfall destinations',
        'ai_tags' => ['hiking', 'waterfalls', 'mountains', 'adventure'],
        'ai_recommendation_score' => 98,
        'ai_benefits' => ['Mt. Balagbag summit views', 'Kaytitinga Falls swimming', 'Otso-Otso secluded pools', 'Photography opportunities'],
        'actual_spots' => ['Mt. Balagbag', 'Kaytitinga Falls', 'Otso-Otso Falls'],
        'avg_rating' => 4.7,
        'total_reviews' => 601,
        'emoji' => '🌿'
    ],
    [
        'name' => 'adventure',
        'display_name' => 'Adventure & Activities',
        'icon' => 'hiking',
        'description' => 'Mountain climbing, waterfall trekking, outdoor adventures - Thrilling SJDM experiences',
        'ai_tags' => ['climbing', 'trekking', 'outdoor', 'extreme'],
        'ai_recommendation_score' => 95,
        'ai_benefits' => ['Adrenaline rush activities', 'Mountain climbing challenges', 'Waterfall trekking', 'Adventure photography'],
        'actual_spots' => ['Mt. Balagbag', 'Kaytitinga Falls', 'Otso-Otso Falls'],
        'avg_rating' => 4.7,
        'total_reviews' => 601,
        'emoji' => '⛰️'
    ],
    [
        'name' => 'farm',
        'display_name' => 'Farms & Eco-Tourism',
        'icon' => 'agriculture',
        'description' => 'Abes Farm (4.7⭐), Paradise Hill Farm - Educational farms with organic experiences',
        'ai_tags' => ['organic', 'educational', 'sustainable', 'family'],
        'ai_recommendation_score' => 92,
        'ai_benefits' => ['Organic farming experiences', 'Educational tours', 'Family activities', 'Sustainable tourism'],
        'actual_spots' => ['Abes Farm', 'Paradise Hill Farm'],
        'avg_rating' => 4.6,
        'total_reviews' => 234,
        'emoji' => '🌾'
    ],
    [
        'name' => 'park',
        'display_name' => 'Parks & Recreation',
        'icon' => 'park',
        'description' => 'City Oval (People\'s Park) - Free family recreation with sports facilities',
        'ai_tags' => ['recreation', 'sports', 'family', 'free'],
        'ai_recommendation_score' => 88,
        'ai_benefits' => ['Free family activities', 'Sports facilities', 'Community events', 'Recreation spaces'],
        'actual_spots' => ['City Oval (People\'s Park)'],
        'avg_rating' => 4.3,
        'total_reviews' => 156,
        'emoji' => '🌳'
    ],
    [
        'name' => 'religious',
        'display_name' => 'Religious Sites',
        'icon' => 'church',
        'description' => 'Our Lady of Lourdes Parish, Padre Pio Parish - Historic churches with cultural significance',
        'ai_tags' => ['spiritual', 'historical', 'cultural', 'heritage'],
        'ai_recommendation_score' => 90,
        'ai_benefits' => ['Spiritual reflection', 'Historical architecture', 'Cultural heritage', 'Peaceful environment'],
        'actual_spots' => ['Our Lady of Lourdes Parish', 'Padre Pio Parish'],
        'avg_rating' => 4.5,
        'total_reviews' => 189,
        'emoji' => '⛪'
    ],
    [
        'name' => 'family',
        'display_name' => 'Family-Friendly',
        'icon' => 'family_restroom',
        'description' => 'Abes Farm, City Oval, Religious sites - Safe destinations for family outings',
        'ai_tags' => ['family', 'kids', 'safe', 'educational'],
        'ai_recommendation_score' => 94,
        'ai_benefits' => ['Kid-friendly activities', 'Educational experiences', 'Safe environments', 'Family bonding'],
        'actual_spots' => ['Abes Farm', 'City Oval (People\'s Park)', 'Our Lady of Lourdes Parish'],
        'avg_rating' => 4.5,
        'total_reviews' => 345,
        'emoji' => '👨‍👩‍👧‍👦'
    ],
    [
        'name' => 'photography',
        'display_name' => 'Photography Spots',
        'icon' => 'photo_camera',
        'description' => 'Mt. Balagbag sunrise, waterfalls, The Rising Heart Monument - Scenic photography locations',
        'ai_tags' => ['photography', 'scenic', 'instagrammable', 'views'],
        'ai_recommendation_score' => 91,
        'ai_benefits' => ['Sunrise photography', 'Waterfall shots', 'Monument photography', 'Nature photography'],
        'actual_spots' => ['Mt. Balagbag', 'Kaytitinga Falls', 'The Rising Heart Monument'],
        'avg_rating' => 4.6,
        'total_reviews' => 278,
        'emoji' => '📸'
    ]
];

// AI-powered suggestion functions
function getAISuggestions($selectedCategories) {
    $suggestions = [];
    
    foreach ($selectedCategories as $category) {
        switch($category) {
            case 'nature':
                $suggestions[] = "Perfect choice! Nature lovers rate Mt. Balagbag 4.8⭐ for sunrise photography";
                break;
            case 'adventure':
                $suggestions[] = "Adventure seekers love Kaytitinga Falls - 4.6⭐ for cliff jumping!";
                break;
            case 'farm':
                $suggestions[] = "Great for families! Abes Farm offers 4.7⭐ organic farming experiences";
                break;
            case 'park':
                $suggestions[] = "City Oval is perfect for morning jogs and family picnics - Free entry!";
                break;
            case 'religious':
                $suggestions[] = "Discover 400+ years of history at Our Lady of Lourdes Parish";
                break;
            case 'family':
                $suggestions[] = "Family favorites: Abes Farm (4.7⭐) and City Oval offer kid-friendly activities";
                break;
            case 'photography':
                $suggestions[] = "Photographers love Mt. Balagbag sunrise - award-winning shots!";
                break;
        }
    }
    
    return $suggestions;
}

function getAIPersonalityInsights($selectedCategories) {
    $insights = [];
    $count = count($selectedCategories);
    
    if ($count >= 5) {
        $insights[] = "You're an Explorer! You love diverse experiences and seek adventure";
    } elseif (in_array('nature', $selectedCategories) && in_array('adventure', $selectedCategories)) {
        $insights[] = "You're a Thrill Seeker! Mountains and waterfalls call your name";
    } elseif (in_array('farm', $selectedCategories) && in_array('family', $selectedCategories)) {
        $insights[] = "You're a Nature Lover! You enjoy peaceful, educational experiences";
    } elseif (in_array('religious', $selectedCategories)) {
        $insights[] = "You're a Cultural Explorer! You appreciate history and spirituality";
    } elseif (in_array('photography', $selectedCategories)) {
        $insights[] = "You're an Artist! You capture beauty through your lens";
    } else {
        $insights[] = "You're a Curious Traveler! Ready to discover SJDM's hidden gems";
    }
    
    // Add specific spot recommendations
    if (in_array('nature', $selectedCategories)) {
        $insights[] = "Don't miss: Mt. Balagbag summit at sunrise (4.8⭐)";
    }
    if (in_array('farm', $selectedCategories)) {
        $insights[] = "Must-visit: Abes Farm for organic vegetables and family activities (4.7⭐)";
    }
    
    return $insights;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Travel Preferences | SJDM Tours</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;600;700&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet">
    <style>
        :root {
            --forest:       #1a3a1f;
            --forest-mid:   #2c5f2d;
            --forest-light: #3d7a40;
            --sage:         #97bc62;
            --sage-light:   #c5dba0;
            --cream:        #faf8f3;
            --warm-white:   #f5f2eb;
            --parchment:    #ede8db;
            --gold:         #c9a84c;
            --gold-light:   #e8c97a;
            --rust:         #c0533a;
            --sky:          #6fa3c7;
            --text-dark:    #1a1f1b;
            --text-mid:     #3d4a3f;
            --text-muted:   #7a8c7c;
            --border:       #d8d0c0;
            --shadow-soft:  0 4px 24px rgba(26, 58, 31, 0.08);
            --shadow-med:   0 8px 40px rgba(26, 58, 31, 0.12);
            --shadow-deep:  0 20px 60px rgba(26, 58, 31, 0.18);
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'DM Sans', sans-serif;
            background-color: var(--cream);
            color: var(--text-dark);
            min-height: 100vh;
            overflow-x: hidden;
        }

        /* ── Background canvas ── */
        .bg-canvas {
            position: fixed;
            inset: 0;
            z-index: 0;
            background:
                radial-gradient(ellipse 80% 60% at 10% 20%, rgba(44, 95, 45, 0.06) 0%, transparent 60%),
                radial-gradient(ellipse 60% 80% at 90% 80%, rgba(151, 188, 98, 0.07) 0%, transparent 60%),
                radial-gradient(ellipse 40% 40% at 50% 50%, rgba(201, 168, 76, 0.04) 0%, transparent 70%);
            background-color: var(--cream);
        }

        .bg-grid {
            position: fixed;
            inset: 0;
            z-index: 0;
            background-image:
                linear-gradient(rgba(44, 95, 45, 0.04) 1px, transparent 1px),
                linear-gradient(90deg, rgba(44, 95, 45, 0.04) 1px, transparent 1px);
            background-size: 48px 48px;
        }

        /* ── Floating leaves deco ── */
        .leaf-deco {
            position: fixed;
            opacity: 0.06;
            pointer-events: none;
            z-index: 0;
        }
        .leaf-deco-1 { top: -40px; right: -30px; font-size: 320px; transform: rotate(20deg); }
        .leaf-deco-2 { bottom: -60px; left: -40px; font-size: 280px; transform: rotate(-15deg); }

        /* ── Layout ── */
        .page-wrap {
            position: relative;
            z-index: 1;
            max-width: 1100px;
            margin: 0 auto;
            padding: 56px 24px 80px;
        }

        /* ── Header ── */
        .hero {
            text-align: center;
            margin-bottom: 56px;
            animation: riseIn 0.9s cubic-bezier(0.22, 1, 0.36, 1) both;
        }

        .hero-eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: linear-gradient(135deg, var(--forest-mid), var(--sage));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            font-size: 0.78rem;
            font-weight: 600;
            letter-spacing: 0.18em;
            text-transform: uppercase;
            margin-bottom: 16px;
        }

        .hero-eyebrow::before,
        .hero-eyebrow::after {
            content: '';
            width: 32px;
            height: 1px;
            background: linear-gradient(90deg, transparent, var(--sage));
        }
        .hero-eyebrow::after { background: linear-gradient(90deg, var(--sage), transparent); }

        .hero h1 {
            font-family: 'Playfair Display', Georgia, serif;
            font-size: clamp(2.2rem, 5vw, 3.4rem);
            font-weight: 700;
            color: var(--forest);
            line-height: 1.15;
            margin-bottom: 16px;
            letter-spacing: -0.01em;
        }

        .hero h1 em {
            font-style: italic;
            color: var(--forest-light);
        }

        .hero p {
            font-size: 1.05rem;
            color: var(--text-mid);
            max-width: 520px;
            margin: 0 auto;
            line-height: 1.7;
        }

        /* ── Main card ── */
        .pref-card {
            background: rgba(255, 255, 255, 0.82);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(216, 208, 192, 0.6);
            border-radius: 24px;
            padding: 48px;
            box-shadow: var(--shadow-deep);
            animation: riseIn 0.9s cubic-bezier(0.22, 1, 0.36, 1) 0.15s both;
        }

        /* ── Selection counter strip ── */
        .counter-strip {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: linear-gradient(135deg, var(--forest) 0%, var(--forest-light) 100%);
            border-radius: 14px;
            padding: 18px 28px;
            margin-bottom: 40px;
            color: white;
        }

        .counter-left {
            display: flex;
            align-items: baseline;
            gap: 6px;
        }

        .counter-num {
            font-family: 'Playfair Display', serif;
            font-size: 2.4rem;
            font-weight: 700;
            color: var(--gold-light);
            line-height: 1;
            transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        .counter-num.bump { transform: scale(1.25); }

        .counter-label {
            font-size: 0.9rem;
            opacity: 0.8;
        }

        .counter-right {
            display: flex;
            gap: 6px;
        }

        .pip {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: rgba(255,255,255,0.2);
            transition: background 0.3s ease, transform 0.3s ease;
        }
        .pip.filled {
            background: var(--gold-light);
            transform: scale(1.2);
        }

        /* ── Categories grid ── */
        .categories-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(290px, 1fr));
            gap: 18px;
            margin-bottom: 32px;
        }

        .category-item {
            position: relative;
            background: var(--warm-white);
            border: 1.5px solid var(--border);
            border-radius: 16px;
            padding: 24px;
            cursor: pointer;
            transition: transform 0.25s cubic-bezier(0.34, 1.56, 0.64, 1),
                        box-shadow 0.25s ease,
                        border-color 0.25s ease,
                        background 0.25s ease;
            overflow: hidden;
        }

        .category-item::after {
            content: '';
            position: absolute;
            inset: 0;
            border-radius: 16px;
            background: linear-gradient(135deg, rgba(44, 95, 45, 0.0), rgba(151, 188, 98, 0.06));
            opacity: 0;
            transition: opacity 0.3s ease;
            pointer-events: none;
        }

        .category-item:hover {
            transform: translateY(-5px) scale(1.01);
            box-shadow: var(--shadow-med);
            border-color: var(--sage);
        }

        .category-item:hover::after { opacity: 1; }

        .category-item.selected {
            background: linear-gradient(135deg, #eef5e9 0%, #f5fbef 100%);
            border-color: var(--forest-mid);
            transform: translateY(-3px);
            box-shadow: 0 8px 32px rgba(44, 95, 45, 0.16), inset 0 0 0 1px rgba(44, 95, 45, 0.1);
        }

        /* Selected checkmark */
        .check-badge {
            position: absolute;
            top: 14px;
            right: 14px;
            width: 26px;
            height: 26px;
            border-radius: 50%;
            background: var(--forest-mid);
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transform: scale(0.5) rotate(-30deg);
            transition: opacity 0.3s ease, transform 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        .category-item.selected .check-badge {
            opacity: 1;
            transform: scale(1) rotate(0deg);
        }

        .check-badge svg { width: 14px; height: 14px; stroke: white; stroke-width: 2.5; fill: none; }

        /* AI pick ribbon */
        .ai-pick-ribbon {
            position: absolute;
            top: 14px;
            right: 14px;
            background: linear-gradient(135deg, var(--gold), var(--gold-light));
            color: var(--forest);
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 0.7rem;
            font-weight: 700;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            display: flex;
            align-items: center;
            gap: 4px;
            box-shadow: 0 2px 8px rgba(201, 168, 76, 0.35);
            transition: opacity 0.3s ease;
        }

        .category-item.selected .ai-pick-ribbon { opacity: 0; }

        /* Icon */
        .category-icon {
            width: 52px;
            height: 52px;
            background: linear-gradient(135deg, var(--forest-mid), var(--forest-light));
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 14px;
            color: white;
            font-size: 26px;
            transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1), box-shadow 0.3s ease;
            box-shadow: 0 4px 14px rgba(44, 95, 45, 0.25);
        }

        .category-item:hover .category-icon,
        .category-item.selected .category-icon {
            transform: scale(1.1) rotate(-4deg);
            box-shadow: 0 8px 22px rgba(44, 95, 45, 0.35);
        }

        .category-name {
            font-family: 'Playfair Display', serif;
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--forest);
            margin-bottom: 6px;
        }

        .category-description {
            font-size: 0.82rem;
            color: var(--text-muted);
            line-height: 1.55;
            margin-bottom: 12px;
        }

        /* Tags */
        .ai-tags {
            display: flex;
            flex-wrap: wrap;
            gap: 5px;
            margin-bottom: 12px;
        }

        .ai-tag {
            background: rgba(44, 95, 45, 0.08);
            color: var(--forest-mid);
            border: 1px solid rgba(44, 95, 45, 0.12);
            padding: 3px 9px;
            border-radius: 20px;
            font-size: 0.72rem;
            font-weight: 500;
        }

        /* Benefits */
        .ai-benefits {
            margin-bottom: 14px;
        }

        .ai-benefits ul { list-style: none; padding: 0; }

        .ai-benefits li {
            font-size: 0.78rem;
            color: #4a7c50;
            padding: 2px 0;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .ai-benefits li::before {
            content: '';
            width: 5px;
            height: 5px;
            border-radius: 50%;
            background: var(--sage);
            flex-shrink: 0;
        }

        /* Rating row */
        .rating-row {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.76rem;
            color: var(--text-muted);
            border-top: 1px solid rgba(0,0,0,0.06);
            padding-top: 10px;
            margin-top: 10px;
        }

        .stars {
            color: var(--gold);
            font-size: 0.85rem;
            letter-spacing: -1px;
        }

        .rating-num {
            font-weight: 600;
            color: var(--forest-mid);
        }

        /* Tooltip */
        .ai-tooltip {
            font-size: 0.74rem;
            color: var(--sky);
            cursor: help;
            text-decoration: underline dotted;
            position: relative;
            display: inline-block;
        }

        .ai-tooltip-content {
            visibility: hidden;
            width: 190px;
            background: var(--forest);
            color: white;
            font-size: 0.76rem;
            border-radius: 10px;
            padding: 8px 12px;
            position: absolute;
            bottom: 125%;
            left: 50%;
            transform: translateX(-50%);
            opacity: 0;
            transition: opacity 0.25s ease;
            pointer-events: none;
            z-index: 10;
        }

        .ai-tooltip:hover .ai-tooltip-content {
            visibility: visible;
            opacity: 1;
        }

        /* ── Insight panels ── */
        .insight-panel {
            border-radius: 14px;
            padding: 20px 24px;
            margin-bottom: 18px;
            display: none;
            animation: slideIn 0.45s cubic-bezier(0.22, 1, 0.36, 1) both;
        }

        .insight-panel.show { display: block; }

        .insight-panel h4 {
            font-family: 'Playfair Display', serif;
            font-size: 1rem;
            font-weight: 600;
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        /* AI Suggestions panel */
        #aiSuggestions {
            background: linear-gradient(135deg, rgba(44, 95, 45, 0.06) 0%, rgba(151, 188, 98, 0.06) 100%);
            border: 1px solid rgba(44, 95, 45, 0.18);
        }

        #aiSuggestions h4 { color: var(--forest-mid); }

        .ai-suggestion-list { list-style: none; padding: 0; }

        .ai-suggestion-list li {
            padding: 7px 0;
            border-bottom: 1px solid rgba(44, 95, 45, 0.08);
            display: flex;
            align-items: flex-start;
            gap: 8px;
            font-size: 0.85rem;
            color: var(--text-mid);
        }

        .ai-suggestion-list li:last-child { border-bottom: none; }
        .ai-suggestion-list li::before { content: '💡'; font-size: 0.9rem; flex-shrink: 0; }

        /* Personality panel */
        #personalityInsights {
            background: linear-gradient(135deg, rgba(111, 163, 199, 0.08) 0%, rgba(16, 185, 129, 0.06) 100%);
            border: 1px solid rgba(111, 163, 199, 0.22);
        }

        #personalityInsights h4 { color: var(--sky); }

        .personality-list { list-style: none; padding: 0; }

        .personality-list li {
            padding: 7px 0;
            border-bottom: 1px solid rgba(111, 163, 199, 0.1);
            display: flex;
            align-items: flex-start;
            gap: 8px;
            font-size: 0.85rem;
            color: var(--text-mid);
        }

        .personality-list li:last-child { border-bottom: none; }
        .personality-list li::before { content: '🎯'; font-size: 0.9rem; flex-shrink: 0; }

        /* Smart recommendation */
        .smart-recommendations {
            background: linear-gradient(135deg, rgba(192, 83, 58, 0.07) 0%, rgba(201, 168, 76, 0.07) 100%);
            border: 1px solid rgba(192, 83, 58, 0.18);
            border-radius: 14px;
            padding: 16px 24px;
            margin-bottom: 18px;
            display: none;
        }

        .smart-recommendations h4 {
            font-size: 0.9rem;
            font-weight: 600;
            color: var(--rust);
            margin-bottom: 6px;
        }

        .recommendation-text {
            font-size: 0.85rem;
            color: var(--text-mid);
        }

        /* ── Alert ── */
        .alert {
            padding: 14px 20px;
            border-radius: 12px;
            margin-bottom: 20px;
            font-size: 0.9rem;
            font-weight: 500;
            display: none;
            animation: slideIn 0.3s ease-out;
        }

        .alert.success {
            background: linear-gradient(135deg, #d1fae5, #a7f3d0);
            color: #065f46;
            border: 1px solid rgba(16, 185, 129, 0.3);
        }

        .alert.error {
            background: linear-gradient(135deg, #fee2e2, #fecaca);
            color: #991b1b;
            border: 1px solid rgba(239, 68, 68, 0.3);
        }

        /* ── Form actions ── */
        .form-actions {
            display: flex;
            justify-content: flex-end;
            align-items: center;
            gap: 16px;
            margin-top: 8px;
        }

        .continue-btn {
            background: linear-gradient(135deg, var(--forest) 0%, var(--forest-light) 100%);
            color: white;
            border: none;
            padding: 16px 40px;
            border-radius: 50px;
            font-family: 'DM Sans', sans-serif;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.25s cubic-bezier(0.34, 1.56, 0.64, 1),
                        box-shadow 0.25s ease,
                        opacity 0.2s ease;
            display: flex;
            align-items: center;
            gap: 10px;
            box-shadow: 0 4px 20px rgba(26, 58, 31, 0.3);
            letter-spacing: 0.02em;
        }

        .continue-btn:hover:not(:disabled) {
            transform: translateY(-3px) scale(1.02);
            box-shadow: 0 10px 30px rgba(26, 58, 31, 0.38);
        }

        .continue-btn:disabled {
            opacity: 0.4;
            cursor: not-allowed;
            transform: none;
        }

        .continue-btn .btn-arrow {
            width: 22px;
            height: 22px;
            background: rgba(255,255,255,0.18);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: transform 0.25s ease;
        }

        .continue-btn:hover:not(:disabled) .btn-arrow {
            transform: translateX(3px);
        }

        /* ── Loading ── */
        .loading {
            text-align: center;
            padding: 60px 40px;
            display: none;
        }

        .loading-ring {
            width: 48px;
            height: 48px;
            border: 3px solid var(--parchment);
            border-top: 3px solid var(--forest-mid);
            border-radius: 50%;
            animation: spin 0.9s linear infinite;
            margin: 0 auto 16px;
        }

        .loading p {
            font-size: 0.9rem;
            color: var(--text-muted);
        }

        /* ── Animations ── */
        @keyframes riseIn {
            from { opacity: 0; transform: translateY(28px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        @keyframes slideIn {
            from { opacity: 0; transform: translateX(-16px); }
            to   { opacity: 1; transform: translateX(0); }
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        /* Stagger the cards */
        .category-item:nth-child(1) { animation: riseIn 0.6s 0.25s both; }
        .category-item:nth-child(2) { animation: riseIn 0.6s 0.32s both; }
        .category-item:nth-child(3) { animation: riseIn 0.6s 0.39s both; }
        .category-item:nth-child(4) { animation: riseIn 0.6s 0.46s both; }
        .category-item:nth-child(5) { animation: riseIn 0.6s 0.53s both; }
        .category-item:nth-child(6) { animation: riseIn 0.6s 0.60s both; }
        .category-item:nth-child(7) { animation: riseIn 0.6s 0.67s both; }

        /* ── Comment Box Section ── */
        .comment-section {
            margin-top: 40px;
            padding: 32px;
            background: linear-gradient(135deg, rgba(44, 95, 45, 0.03) 0%, rgba(151, 188, 98, 0.03) 100%);
            border: 1px solid rgba(44, 95, 45, 0.1);
            border-radius: 16px;
        }

        .comment-section h3 {
            font-family: 'Playfair Display', serif;
            font-size: 1.3rem;
            font-weight: 600;
            color: var(--forest);
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .comment-section p {
            color: var(--text-muted);
            font-size: 0.9rem;
            margin-bottom: 24px;
            line-height: 1.6;
        }

        .preset-options {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 12px;
            margin-bottom: 24px;
        }

        .preset-option {
            background: var(--warm-white);
            border: 1.5px solid var(--border);
            border-radius: 12px;
            padding: 16px;
            cursor: pointer;
            transition: all 0.25s ease;
            position: relative;
        }

        .preset-option:hover {
            border-color: var(--sage);
            transform: translateY(-2px);
            box-shadow: 0 4px 16px rgba(44, 95, 45, 0.1);
        }

        .preset-option.selected {
            background: linear-gradient(135deg, #eef5e9 0%, #f5fbef 100%);
            border-color: var(--forest-mid);
            box-shadow: 0 4px 16px rgba(44, 95, 45, 0.15);
        }

        .preset-option .option-icon {
            width: 36px;
            height: 36px;
            background: linear-gradient(135deg, var(--forest-mid), var(--forest-light));
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 18px;
            margin-bottom: 10px;
        }

        .preset-option .option-title {
            font-weight: 600;
            color: var(--forest);
            margin-bottom: 6px;
            font-size: 0.95rem;
        }

        .preset-option .option-desc {
            font-size: 0.82rem;
            color: var(--text-muted);
            line-height: 1.4;
        }

        .custom-comment-section {
            margin-top: 20px;
        }

        .custom-comment-label {
            display: block;
            font-weight: 600;
            color: var(--forest);
            margin-bottom: 8px;
            font-size: 0.95rem;
        }

        .custom-comment-input {
            width: 100%;
            min-height: 100px;
            padding: 14px 16px;
            border: 2px solid var(--border);
            border-radius: 12px;
            font-family: 'DM Sans', sans-serif;
            font-size: 0.9rem;
            color: var(--text-dark);
            background: var(--warm-white);
            resize: vertical;
            transition: all 0.25s ease;
        }

        .custom-comment-input:focus {
            outline: none;
            border-color: var(--sage);
            box-shadow: 0 0 0 3px rgba(151, 188, 98, 0.1);
        }

        .custom-comment-input::placeholder {
            color: var(--text-muted);
            opacity: 0.7;
        }

        .character-counter {
            text-align: right;
            font-size: 0.8rem;
            color: var(--text-muted);
            margin-top: 6px;
        }

        .character-counter.warning {
            color: var(--rust);
        }

        /* Suggestion Chips */
        .suggestion-chips {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-bottom: 16px;
        }

        .suggestion-chip {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 12px;
            background: var(--warm-white);
            border: 1.5px solid var(--border);
            border-radius: 20px;
            cursor: pointer;
            transition: all 0.25s ease;
            font-size: 0.82rem;
            color: var(--text-mid);
            font-weight: 500;
        }

        .suggestion-chip:hover {
            border-color: var(--sage);
            background: linear-gradient(135deg, #f0f7e9 0%, #f5fbef 100%);
            color: var(--forest-mid);
            transform: translateY(-1px);
            box-shadow: 0 2px 8px rgba(44, 95, 45, 0.1);
        }

        .suggestion-chip:active {
            transform: translateY(0);
            box-shadow: 0 1px 4px rgba(44, 95, 45, 0.1);
        }

        .suggestion-chip .material-icons-outlined {
            font-size: 14px;
            color: var(--sage);
        }

        .suggestion-chip:hover .material-icons-outlined {
            color: var(--forest-mid);
        }

        .preset-checkmark {
            position: absolute;
            top: 12px;
            right: 12px;
            width: 24px;
            height: 24px;
            border-radius: 50%;
            background: var(--forest-mid);
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transform: scale(0.5) rotate(-30deg);
            transition: all 0.3s ease;
        }

        .preset-option.selected .preset-checkmark {
            opacity: 1;
            transform: scale(1) rotate(0deg);
        }

        .preset-checkmark svg { 
            width: 12px; 
            height: 12px; 
            stroke: white; 
            stroke-width: 2.5; 
            fill: none; 
        }

        /* ── Responsive ── */
        @media (max-width: 768px) {
            .page-wrap { padding: 32px 16px 60px; }
            .pref-card { padding: 28px 20px; }
            .categories-grid { grid-template-columns: 1fr; gap: 14px; }
            .form-actions { flex-direction: column; }
            .continue-btn { width: 100%; justify-content: center; }
            .counter-strip { flex-direction: column; gap: 14px; align-items: flex-start; }
            .hero h1 { font-size: 2rem; }
            .comment-section { padding: 24px 20px; }
            .preset-options { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>

    <div class="bg-canvas"></div>
    <div class="bg-grid"></div>
    <div class="leaf-deco leaf-deco-1">🌿</div>
    <div class="leaf-deco leaf-deco-2">🍃</div>

    <div class="page-wrap">

        <!-- Hero -->
        <div class="hero">
            <div class="hero-eyebrow">Personalized for You</div>
            <h1>Your <em>Travel</em> Preferences</h1>
            <p>Tell us what you love, and our AI will craft recommendations tailored just for you.</p>
        </div>

        <!-- Main card -->
        <div class="pref-card">

            <div id="alertMessage" class="alert"></div>

            <!-- Counter strip -->
            <div class="counter-strip">
                <div class="counter-left">
                    <span class="counter-num" id="selectedCount">0</span>
                    <span class="counter-label">&nbsp;/ 8 categories selected</span>
                </div>
                <div class="counter-right" id="pipRow">
                    <?php for($i=0; $i<8; $i++): ?>
                        <div class="pip" id="pip-<?php echo $i; ?>"></div>
                    <?php endfor; ?>
                </div>
            </div>

            <!-- Grid -->
            <div class="categories-grid">
                <?php foreach ($categories as $category): ?>
                    <div class="category-item"
                         data-category="<?php echo $category['name']; ?>"
                         data-score="<?php echo $category['ai_recommendation_score']; ?>">

                        <!-- Check badge (shown when selected) -->
                        <div class="check-badge">
                            <svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
                        </div>

                        <!-- AI Pick ribbon (hidden when selected) -->
                        <?php if ($category['ai_recommendation_score'] >= 90): ?>
                            <div class="ai-pick-ribbon">
                                ✦ AI Pick
                            </div>
                        <?php endif; ?>

                        <div class="category-icon">
                            <span class="material-icons-outlined"><?php echo $category['icon']; ?></span>
                        </div>

                        <div class="category-name"><?php echo $category['emoji'] . ' ' . $category['display_name']; ?></div>
                        <div class="category-description"><?php echo $category['description']; ?></div>

                        <div class="ai-tags">
                            <?php foreach ($category['ai_tags'] as $tag): ?>
                                <span class="ai-tag"><?php echo $tag; ?></span>
                            <?php endforeach; ?>
                        </div>

                        <div class="ai-benefits">
                            <ul>
                                <?php foreach ($category['ai_benefits'] as $benefit): ?>
                                    <li><?php echo $benefit; ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>

                        <div class="rating-row">
                            <span class="stars">★★★★★</span>
                            <span class="rating-num"><?php echo $category['avg_rating']; ?></span>
                            <span>(<?php echo $category['total_reviews']; ?> reviews)</span>
                            <span style="margin-left:auto;">
                                <span class="ai-tooltip">
                                    Why recommended?
                                    <span class="ai-tooltip-content">
                                        <?php echo $category['actual_spots'][0]; ?>: <?php echo $category['avg_rating']; ?>⭐ (<?php echo $category['total_reviews']; ?> reviews)
                                    </span>
                                </span>
                            </span>
                        </div>

                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Insight panels -->
            <div class="insight-panel" id="aiSuggestions">
                <h4>
                    <span class="material-icons-outlined" style="font-size:20px;">lightbulb</span>
                    AI Suggestions
                </h4>
                <ul class="ai-suggestion-list" id="suggestionList"></ul>
            </div>

            <div class="insight-panel" id="personalityInsights">
                <h4>
                    <span class="material-icons-outlined" style="font-size:20px;">psychology</span>
                    Your Travel Personality
                </h4>
                <ul class="personality-list" id="personalityList"></ul>
            </div>

            <div class="smart-recommendations" id="smartRecommendations">
                <h4>🎯 AI Recommendation</h4>
                <p class="recommendation-text" id="recommendationText"></p>
            </div>

            <!-- Comment Box Section -->
            <div class="comment-section">
                <h3>
                    <span class="material-icons-outlined" style="font-size:24px;">chat</span>
                    Tell us more about your travel interests
                </h3>
                <p>Help us personalize your experience even better! Choose from these options or share your own preferences.</p>
                
                <!-- Preset Options -->
                <div class="preset-options">
                    <div class="preset-option" data-comment="adventure-seeker">
                        <div class="preset-checkmark">
                            <svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
                        </div>
                        <div class="option-icon">
                            <span class="material-icons-outlined">hiking</span>
                        </div>
                        <div class="option-title">Adventure Seeker</div>
                        <div class="option-desc">I love thrilling activities, hiking, and exploring off-the-beaten-path destinations</div>
                    </div>
                    
                    <div class="preset-option" data-comment="nature-lover">
                        <div class="preset-checkmark">
                            <svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
                        </div>
                        <div class="option-icon">
                            <span class="material-icons-outlined">forest</span>
                        </div>
                        <div class="option-title">Nature Enthusiast</div>
                        <div class="option-desc">I enjoy peaceful natural settings, wildlife, and scenic photography spots</div>
                    </div>
                    
                    <div class="preset-option" data-comment="family-traveler">
                        <div class="preset-checkmark">
                            <svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
                        </div>
                        <div class="option-icon">
                            <span class="material-icons-outlined">family_restroom</span>
                        </div>
                        <div class="option-title">Family Traveler</div>
                        <div class="option-desc">I'm looking for kid-friendly activities and educational experiences for the whole family</div>
                    </div>
                    
                    <div class="preset-option" data-comment="budget-conscious">
                        <div class="preset-checkmark">
                            <svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
                        </div>
                        <div class="option-icon">
                            <span class="material-icons-outlined">savings</span>
                        </div>
                        <div class="option-title">Budget-Conscious</div>
                        <div class="option-desc">I prefer affordable options, free attractions, and value-for-money experiences</div>
                    </div>
                    
                    <div class="preset-option" data-comment="luxury-traveler">
                        <div class="preset-checkmark">
                            <svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
                        </div>
                        <div class="option-icon">
                            <span class="material-icons-outlined">diamond</span>
                        </div>
                        <div class="option-title">Luxury Traveler</div>
                        <div class="option-desc">I enjoy premium experiences, comfort, and exclusive tours with personalized service</div>
                    </div>
                    
                    <div class="preset-option" data-comment="photography-buff">
                        <div class="preset-checkmark">
                            <svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
                        </div>
                        <div class="option-icon">
                            <span class="material-icons-outlined">photo_camera</span>
                        </div>
                        <div class="option-title">Photography Buff</div>
                        <div class="option-desc">I'm always looking for Instagram-worthy spots and perfect photo opportunities</div>
                    </div>
                </div>
                
                <!-- Custom Comment Input -->
                <div class="custom-comment-section">
                    <label for="customComment" class="custom-comment-label">
                        <span class="material-icons-outlined" style="font-size:18px; vertical-align: middle; margin-right: 6px;">edit</span>
                        Or tell us in your own words...
                    </label>
                    
                    <!-- Spot-specific suggestions -->
                    <div class="suggestion-chips">
                        <span class="suggestion-chip" data-text="I want to go swimming at waterfalls">
                            <span class="material-icons-outlined" style="font-size:14px;">water</span>
                            Swimming & Waterfalls
                        </span>
                        <span class="suggestion-chip" data-text="I love mountain hiking and climbing">
                            <span class="material-icons-outlined" style="font-size:14px;">hiking</span>
                            Mountain Hiking
                        </span>
                        <span class="suggestion-chip" data-text="I want to see sunrise views from Mt. Balagbag">
                            <span class="material-icons-outlined" style="font-size:14px;">wb_twilight</span>
                            Sunrise Views
                        </span>
                        <span class="suggestion-chip" data-text="I'm interested in farm tours and organic experiences">
                            <span class="material-icons-outlined" style="font-size:14px;">agriculture</span>
                            Farm Tours
                        </span>
                        <span class="suggestion-chip" data-text="I want free family activities at parks">
                            <span class="material-icons-outlined" style="font-size:14px;">park</span>
                            Free Park Activities
                        </span>
                        <span class="suggestion-chip" data-text="I love taking photos at scenic spots">
                            <span class="material-icons-outlined" style="font-size:14px;">photo_camera</span>
                            Photography Spots
                        </span>
                        <span class="suggestion-chip" data-text="I want to visit historical churches">
                            <span class="material-icons-outlined" style="font-size:14px;">church</span>
                            Historical Churches
                        </span>
                        <span class="suggestion-chip" data-text="I'm looking for adventure activities">
                            <span class="material-icons-outlined" style="font-size:14px;">extreme_sports</span>
                            Adventure Activities
                        </span>
                    </div>
                    
                    <textarea 
                        id="customComment" 
                        class="custom-comment-input" 
                        placeholder="Share your specific interests, travel style, or what you're most excited about exploring in San Jose del Monte... 

For example:
• 'I want to go swimming at Kaytitinga Falls'
• 'I love mountain hiking and sunrise views'
• 'I'm looking for family-friendly farm tours'
• 'I want to take photos at scenic spots'
• 'I'm interested in historical and religious sites'"
                        maxlength="500"
                    ></textarea>
                    <div class="character-counter" id="charCounter">0 / 500 characters</div>
                </div>
            </div>

            <!-- Form -->
            <form id="preferenceForm">
                <div class="form-actions">
                    <button type="submit" class="continue-btn" id="continueBtn" disabled>
                        Continue to Dashboard
                        <span class="btn-arrow">
                            <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                        </span>
                    </button>
                </div>
            </form>
        </div>

        <!-- Loading -->
        <div class="loading" id="loading">
            <div class="loading-ring"></div>
            <p>Saving your preferences…</p>
        </div>

    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const categoryItems = document.querySelectorAll('.category-item');
            const continueBtn = document.getElementById('continueBtn');
            const selectedCountSpan = document.getElementById('selectedCount');
            const preferenceForm = document.getElementById('preferenceForm');
            const alertMessage = document.getElementById('alertMessage');
            const loading = document.getElementById('loading');
            const personalityInsights = document.getElementById('personalityInsights');
            const personalityText = document.getElementById('personalityText');
            const smartRecommendations = document.getElementById('smartRecommendations');
            const recommendationText = document.getElementById('recommendationText');
            const pips = document.querySelectorAll('.pip');
            
            // Comment box elements
            const presetOptions = document.querySelectorAll('.preset-option');
            const customCommentInput = document.getElementById('customComment');
            const charCounter = document.getElementById('charCounter');
            const suggestionChips = document.querySelectorAll('.suggestion-chip');

            let selectedCategories = new Set();
            let selectedPreset = null;
            let aiInsights = {
                'nature': { 
                    complementary: ['adventure', 'photography'], 
                    personality: 'Mountain Explorer',
                    actual_spots: ['Mt. Balagbag', 'Kaytitinga Falls', 'Otso-Otso Falls'],
                    avg_rating: 4.7,
                    highlight: 'Mt. Balagbag - 4.8⭐ with 245 reviews'
                },
                'adventure': { 
                    complementary: ['nature', 'photography'], 
                    personality: 'Thrill Seeker',
                    actual_spots: ['Mt. Balagbag', 'Kaytitinga Falls', 'Otso-Otso Falls'],
                    avg_rating: 4.7,
                    highlight: 'Challenging hikes with Sierra Madre views'
                },
                'farm': { 
                    complementary: ['family', 'park'], 
                    personality: 'Eco-Tourism Lover',
                    actual_spots: ['Abes Farm', 'Paradise Hill Farm'],
                    avg_rating: 4.6,
                    highlight: 'Abes Farm - 4.7⭐ educational experience'
                },
                'park': { 
                    complementary: ['family', 'farm'], 
                    personality: 'Recreation Seeker',
                    actual_spots: ["City Oval (People's Park)"],
                    avg_rating: 4.3,
                    highlight: 'Free family recreation with sports facilities'
                },
                'religious': { 
                    complementary: ['park', 'family'], 
                    personality: 'Spiritual Explorer',
                    actual_spots: ['Our Lady of Lourdes Parish', 'Padre Pio Parish'],
                    avg_rating: 4.5,
                    highlight: 'Historic churches with cultural significance'
                },
                'family': { 
                    complementary: ['farm', 'park'], 
                    personality: 'Family Oriented',
                    actual_spots: ['Abes Farm', "City Oval (People's Park)", 'Our Lady of Lourdes Parish'],
                    avg_rating: 4.5,
                    highlight: '4.7⭐ rated Abes Farm for educational fun'
                },
                'photography': { 
                    complementary: ['nature', 'adventure'], 
                    personality: 'Photography Lover',
                    actual_spots: ['Mt. Balagbag', 'Kaytitinga Falls', 'The Rising Heart Monument'],
                    avg_rating: 4.6,
                    highlight: 'Sunrise shots at Mt. Balagbag'
                }
            };

            function getSmartRecommendation() {
                const selectedArray = Array.from(selectedCategories);
                if (selectedArray.length === 0) return '';
                let recommendations = [];
                selectedArray.forEach(cat => {
                    if (aiInsights[cat] && aiInsights[cat].complementary) {
                        aiInsights[cat].complementary.forEach(comp => {
                            if (!selectedCategories.has(comp)) {
                                recommendations.push({
                                    category: comp,
                                    reason: aiInsights[comp].highlight,
                                    rating: aiInsights[comp].avg_rating
                                });
                            }
                        });
                    }
                });
                if (recommendations.length > 0) {
                    const rec = recommendations[0];
                    return `Based on your selections, try ${rec.category.charAt(0).toUpperCase() + rec.category.slice(1).replace('-', ' ')}! ${rec.reason} (${rec.avg_rating}⭐)`;
                }
                return "Great choices! You'll love exploring SJDM's premier destinations.";
            }

            function updatePersonalityInsights() {
                const selectedArray = Array.from(selectedCategories);
                if (selectedArray.length < 2) {
                    personalityInsights.classList.remove('show');
                    return;
                }
                let personalities = [];
                let topSpots = [];
                selectedArray.forEach(cat => {
                    if (aiInsights[cat]) {
                        personalities.push(aiInsights[cat].personality);
                        if (aiInsights[cat].actual_spots) topSpots.push(...aiInsights[cat].actual_spots);
                    }
                });
                if (personalities.length > 0) {
                    const uniqueP = [...new Set(personalities)];
                    const uniqueS = [...new Set(topSpots)];
                    const list = document.getElementById('personalityList');
                    list.innerHTML = '';
                    const text = uniqueP.length === 1
                        ? `You're a ${uniqueP[0]}! Top spots: ${uniqueS.slice(0, 2).join(', ')}`
                        : `Diverse interests: ${uniqueP.slice(0, 2).join(' & ')}! Must-visit: ${uniqueS.slice(0, 2).join(', ')}`;
                    const li = document.createElement('li');
                    li.textContent = text;
                    list.appendChild(li);
                    personalityInsights.classList.add('show');
                } else {
                    personalityInsights.classList.remove('show');
                }
            }

            function updateSmartRecommendations() {
                const rec = getSmartRecommendation();
                if (rec) {
                    recommendationText.textContent = rec;
                    smartRecommendations.style.display = 'block';
                } else {
                    smartRecommendations.style.display = 'none';
                }
            }

            function updatePips() {
                pips.forEach((pip, i) => {
                    pip.classList.toggle('filled', i < selectedCategories.size);
                });
            }

            function bumpCounter() {
                selectedCountSpan.classList.remove('bump');
                void selectedCountSpan.offsetWidth; // reflow
                selectedCountSpan.classList.add('bump');
                setTimeout(() => selectedCountSpan.classList.remove('bump'), 400);
            }

            categoryItems.forEach(item => {
                item.addEventListener('click', function() {
                    const category = this.dataset.category;
                    const score = parseInt(this.dataset.score) || 0;
                    
                    if (selectedCategories.has(category)) {
                        selectedCategories.delete(category);
                        this.classList.remove('selected');
                    } else {
                        if (selectedCategories.size >= 8) {
                            showAlert('You can select up to 8 categories only', 'error');
                            return;
                        }
                        selectedCategories.add(category);
                        this.classList.add('selected');
                        if (score >= 90) {
                            setTimeout(() => {
                                showAlert(`Excellent choice! "${this.querySelector('.category-name').textContent}" is highly recommended by our AI!`, 'success');
                            }, 300);
                        }
                    }
                    
                    selectedCountSpan.textContent = selectedCategories.size;
                    bumpCounter();
                    updatePips();
                    continueBtn.disabled = selectedCategories.size === 0;
                    updatePersonalityInsights();
                    updateSmartRecommendations();
                });
            });

            // Preset option handlers
            presetOptions.forEach(option => {
                option.addEventListener('click', function() {
                    const preset = this.dataset.comment;
                    
                    // Toggle selection
                    if (selectedPreset === preset) {
                        selectedPreset = null;
                        this.classList.remove('selected');
                    } else {
                        // Deselect all others first
                        presetOptions.forEach(opt => opt.classList.remove('selected'));
                        selectedPreset = preset;
                        this.classList.add('selected');
                        
                        // Show confirmation
                        const title = this.querySelector('.option-title').textContent;
                        showAlert(`Great choice! You selected: ${title}`, 'success');
                    }
                });
            });

            // Character counter for custom comment
            if (customCommentInput && charCounter) {
                customCommentInput.addEventListener('input', function() {
                    const length = this.value.length;
                    const maxLength = 500;
                    
                    charCounter.textContent = `${length} / ${maxLength} characters`;
                    
                    // Add warning class when approaching limit
                    if (length >= maxLength * 0.9) {
                        charCounter.classList.add('warning');
                    } else {
                        charCounter.classList.remove('warning');
                    }
                    
                    // Clear preset selection if user starts typing custom comment
                    if (length > 0 && selectedPreset) {
                        presetOptions.forEach(opt => opt.classList.remove('selected'));
                        selectedPreset = null;
                    }
                });
            }

            // Suggestion chip handlers
            suggestionChips.forEach(chip => {
                chip.addEventListener('click', function() {
                    const suggestionText = this.dataset.text;
                    
                    // Add the suggestion text to the textarea
                    if (customCommentInput) {
                        const currentValue = customCommentInput.value.trim();
                        const newValue = currentValue ? 
                            `${currentValue}. ${suggestionText}` : 
                            suggestionText;
                        
                        customCommentInput.value = newValue;
                        
                        // Trigger input event to update character counter
                        customCommentInput.dispatchEvent(new Event('input'));
                        
                        // Clear preset selection
                        if (selectedPreset) {
                            presetOptions.forEach(opt => opt.classList.remove('selected'));
                            selectedPreset = null;
                        }
                        
                        // Show confirmation
                        showAlert('Suggestion added! You can continue typing or add more suggestions.', 'success');
                        
                        // Focus on the textarea
                        customCommentInput.focus();
                    }
                });
            });

            function showAlert(message, type) {
                alertMessage.textContent = message;
                alertMessage.className = 'alert ' + type;
                alertMessage.style.display = 'block';
                setTimeout(() => { alertMessage.style.display = 'none'; }, 5000);
            }

            preferenceForm.addEventListener('submit', function(e) {
                e.preventDefault();
                if (selectedCategories.size === 0) {
                    showAlert('Please select at least one category', 'error');
                    return;
                }
                preferenceForm.style.display = 'none';
                loading.style.display = 'block';

                const formData = new FormData();
                selectedCategories.forEach(category => { formData.append('categories[]', category); });
                
                // Add comment data
                if (selectedPreset) {
                    formData.append('preset_comment', selectedPreset);
                }
                if (customCommentInput.value.trim()) {
                    formData.append('custom_comment', customCommentInput.value.trim());
                }
                
                formData.append('ai_insights', JSON.stringify({
                    selected_count: selectedCategories.size,
                    personality_insights: Array.from(selectedCategories).map(cat => aiInsights[cat]?.personality).filter(Boolean),
                    selection_pattern: 'ai_enhanced',
                    user_comment_type: selectedPreset ? (customCommentInput.value.trim() ? 'both' : 'preset') : (customCommentInput.value.trim() ? 'custom' : 'none')
                }));

                fetch('user-preferences.php', { method: 'POST', body: formData })
                    .then(r => r.json())
                    .then(data => {
                        if (data.success) {
                            showAlert(data.message, 'success');
                            setTimeout(() => { window.location.href = 'user-index.php'; }, 1500);
                        } else {
                            showAlert(data.message, 'error');
                            preferenceForm.style.display = 'block';
                            loading.style.display = 'none';
                        }
                    })
                    .catch(() => {
                        showAlert('An error occurred. Please try again.', 'error');
                        preferenceForm.style.display = 'block';
                        loading.style.display = 'none';
                    });
            });

            updatePersonalityInsights();
            updateSmartRecommendations();
        });
    </script>
</body>
</html>