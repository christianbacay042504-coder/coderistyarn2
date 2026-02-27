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
        
        // Save user comment/feedback (optional - doesn't fail if table doesn't exist)
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
            
            // Try to save comment (optional - doesn't fail if table doesn't exist)
            try {
                $commentStmt = $conn->prepare("INSERT INTO user_comments (user_id, comment_text, comment_type, created_at) VALUES (?, ?, ?, NOW()) ON DUPLICATE KEY UPDATE comment_text = VALUES(comment_text), comment_type = VALUES(comment_type), updated_at = NOW()");
                if ($commentStmt) {
                    $commentStmt->bind_param("iss", $_SESSION['user_id'], $commentText, $commentType);
                    $commentStmt->execute();
                    $commentStmt->close();
                }
            } catch (Exception $e) {
                // Comment saving failed, but continue with preferences
                error_log("Comment saving failed: " . $e->getMessage());
            }
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
        'total_reviews' => 601
    ],
    [
        'name' => 'farm',
        'display_name' => 'Farms & Eco-Tourism',
        'icon' => 'agriculture',
        'description' => 'Abes Farm (4.7⭐) - Organic farming experiences and educational tours perfect for families',
        'ai_tags' => ['organic', 'educational', 'family-friendly', 'sustainable'],
        'ai_recommendation_score' => 92,
        'ai_benefits' => ['Organic farming education', 'Animal interactions', 'Fresh produce experience', 'Family bonding'],
        'actual_spots' => ['Abes Farm', 'Paradise Hill Farm'],
        'avg_rating' => 4.6,
        'total_reviews' => 245
    ],
    [
        'name' => 'park',
        'display_name' => 'Parks & Recreation',
        'icon' => 'park',
        'description' => 'City Oval People\'s Park (4.3⭐) - Central park for jogging, picnics, and family activities',
        'ai_tags' => ['recreation', 'family', 'exercise', 'community'],
        'ai_recommendation_score' => 85,
        'ai_benefits' => ['Jogging paths', 'Playground facilities', 'Sports courts', 'Family gatherings'],
        'actual_spots' => ['City Oval (People\'s Park)'],
        'avg_rating' => 4.3,
        'total_reviews' => 98
    ],
    [
        'name' => 'adventure',
        'display_name' => 'Adventure & Activities',
        'icon' => 'hiking',
        'description' => 'Challenging Mt. Balagbag hikes and waterfall adventures for thrill seekers',
        'ai_tags' => ['challenging', 'outdoor', 'fitness', 'thrilling'],
        'ai_recommendation_score' => 94,
        'ai_benefits' => ['Mt. Balagbag challenge', 'Waterfall hiking', 'Sierra Madre views', 'Adventure photography'],
        'actual_spots' => ['Mt. Balagbag', 'Kaytitinga Falls', 'Otso-Otso Falls'],
        'avg_rating' => 4.7,
        'total_reviews' => 601
    ],
    [
        'name' => 'religious',
        'display_name' => 'Religious Sites',
        'icon' => 'church',
        'description' => 'Our Lady of Lourdes Parish (4.4⭐) and Padre Pio Parish - Historic churches for spiritual reflection',
        'ai_tags' => ['spiritual', 'historical', 'peaceful', 'architectural'],
        'ai_recommendation_score' => 88,
        'ai_benefits' => ['Spiritual reflection', 'Historical architecture', 'Peaceful prayer', 'Cultural heritage'],
        'actual_spots' => ['Our Lady of Lourdes Parish', 'Padre Pio Parish'],
        'avg_rating' => 4.5,
        'total_reviews' => 246
    ],
    [
        'name' => 'family',
        'display_name' => 'Family-Friendly',
        'icon' => 'family_restroom',
        'description' => 'Abes Farm, City Oval, and religious sites - Perfect destinations for family bonding and activities',
        'ai_tags' => ['all-ages', 'educational', 'bonding', 'kid-friendly'],
        'ai_recommendation_score' => 96,
        'ai_benefits' => ['Educational farm tours', 'Park playgrounds', 'Safe religious sites', 'Family picnics'],
        'actual_spots' => ['Abes Farm', 'City Oval (People\'s Park)', 'Our Lady of Lourdes Parish'],
        'avg_rating' => 4.5,
        'total_reviews' => 388
    ],
    [
        'name' => 'photography',
        'display_name' => 'Photography Spots',
        'icon' => 'photo_camera',
        'description' => 'Mt. Balagbag sunrises, waterfalls, Rising Heart Monument - SJDM\'s most photogenic locations',
        'ai_tags' => ['scenic', 'instagrammable', 'nature', 'landmarks'],
        'ai_recommendation_score' => 91,
        'ai_benefits' => ['Sunrise mountain shots', 'Waterfall photography', 'Urban landmark photos', 'Nature landscapes'],
        'actual_spots' => ['Mt. Balagbag', 'Kaytitinga Falls', 'The Rising Heart Monument'],
        'avg_rating' => 4.6,
        'total_reviews' => 637
    ]
];

// AI-powered smart suggestions based on actual SJDM tourist spots data
function getAISuggestions() {
    return [
        'Based on actual visitor data, "Nature & Waterfalls" (Mt. Balagbag, Kaytitinga Falls) are highly rated with 4.6-4.8 stars',
        'Visitors who choose "Family-Friendly" also enjoy "Farms & Eco-Tourism" (Abes Farm has 4.7/5 rating)',
        'Adventure seekers combine "Nature & Waterfalls" with "Religious Sites" for diverse day experiences'
    ];
}

// AI personality matching based on actual SJDM tourist spots
function getAIPersonalityInsights($selectedCategories) {
    $insights = [];
    
    if (in_array('nature', $selectedCategories) && in_array('adventure', $selectedCategories)) {
        $insights[] = 'You\'re an adventure seeker! Mt. Balagbag (4.8⭐) and Kaytitinga Falls await your exploration!';
    }
    
    if (in_array('farm', $selectedCategories) && in_array('family', $selectedCategories)) {
        $insights[] = 'Perfect family combo! Abes Farm (4.7⭐) offers educational experiences for all ages!';
    }
    
    if (in_array('religious', $selectedCategories) && in_array('park', $selectedCategories)) {
        $insights[] = 'You appreciate peaceful spaces! Our Lady of Lourdes Parish + City Oval make serene day trips!';
    }
    
    if (in_array('nature', $selectedCategories) && in_array('photography', $selectedCategories)) {
        $insights[] = 'Nature photographer! Mt. Balagbag sunrises and Kaytitinga Falls offer stunning shots!';
    }
    
    return $insights;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Select Your Interests | SJDM Tours</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #e8f5e9 100%);
            min-height: 100vh;
            margin: 0;
            padding: 0;
        }

        .preference-container {
            background: white;
            border-radius: 0;
            box-shadow: none;
            max-width: 100vw;
            width: 100vw;
            height: 100vh;
            padding: 60px;
            text-align: center;
            max-height: 100vh;
            overflow-y: auto;
            border: none;
        }

        .preference-header {
            margin-bottom: 30px;
        }

        .preference-header h1 {
            font-size: 32px;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 12px;
            line-height: 1.2;
        }

        .preference-header p {
            font-size: 16px;
            color: #6b7280;
            margin-bottom: 8px;
            font-weight: 400;
        }

        .selection-count {
            font-size: 14px;
            color: #2c5f2d;
            font-weight: 600;
            margin-bottom: 32px;
            padding: 8px 16px;
            background: rgba(44, 95, 45, 0.08);
            border-radius: 20px;
            display: inline-block;
        }

        .categories-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            gap: 16px;
            margin-bottom: 40px;
            max-height: 500px;
            overflow-y: auto;
            padding: 8px;
        }

        .category-item {
            border: 2px solid #e5e7eb;
            border-radius: 20px;
            padding: 28px 20px;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            background: white;
            text-align: center;
            position: relative;
            min-height: 220px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        .ai-recommendation {
            position: absolute;
            top: -8px;
            right: -8px;
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            border-radius: 50%;
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: 700;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
            z-index: 10;
        }

        .ai-tags {
            display: flex;
            flex-wrap: wrap;
            gap: 4px;
            margin-top: 8px;
            justify-content: center;
        }

        .ai-tag {
            background: rgba(44, 95, 45, 0.1);
            color: #2c5f2d;
            font-size: 10px;
            padding: 2px 8px;
            border-radius: 12px;
            font-weight: 500;
        }

        .category-item.selected .ai-tag {
            background: rgba(255, 255, 255, 0.2);
            color: white;
        }

        .ai-benefits {
            display: none;
            position: absolute;
            bottom: 100%;
            left: 50%;
            transform: translateX(-50%);
            background: white;
            border: 2px solid #2c5f2d;
            border-radius: 12px;
            padding: 12px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
            z-index: 100;
            min-width: 200px;
            margin-bottom: 8px;
        }

        .category-item:hover .ai-benefits {
            display: block;
        }

        .ai-benefits h4 {
            color: #2c5f2d;
            font-size: 12px;
            margin-bottom: 8px;
            font-weight: 600;
        }

        .ai-benefits ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .ai-benefits li {
            font-size: 11px;
            color: #374151;
            margin-bottom: 4px;
            padding-left: 16px;
            position: relative;
        }

        .ai-benefits li:before {
            content: "✓";
            position: absolute;
            left: 0;
            color: #10b981;
            font-weight: 600;
        }

        .ai-suggestions {
            background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
            border: 2px solid #0ea5e9;
            border-radius: 16px;
            padding: 20px;
            margin-bottom: 32px;
            text-align: left;
        }

        .ai-suggestions h3 {
            color: #0c4a6e;
            font-size: 16px;
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .ai-suggestions ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .ai-suggestions li {
            color: #0c4a6e;
            font-size: 14px;
            margin-bottom: 8px;
            padding-left: 20px;
            position: relative;
            line-height: 1.4;
        }

        .ai-suggestions li:before {
            content: "🤖";
            position: absolute;
            left: 0;
        }

        .ai-personality-insights {
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
            border: 2px solid #f59e0b;
            border-radius: 16px;
            padding: 20px;
            margin-bottom: 32px;
            text-align: center;
            display: none;
        }

        .ai-personality-insights h3 {
            color: #92400e;
            font-size: 16px;
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .ai-personality-insights p {
            color: #92400e;
            font-size: 14px;
            margin: 0;
            font-weight: 500;
        }

        .smart-recommendations {
            background: linear-gradient(135deg, #f3e8ff 0%, #e9d5ff 100%);
            border: 2px solid #9333ea;
            border-radius: 16px;
            padding: 16px;
            margin-bottom: 24px;
            text-align: center;
        }

        .smart-recommendations h4 {
            color: #6b21a8;
            font-size: 14px;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
        }

        .smart-recommendations .recommendation-text {
            color: #6b21a8;
            font-size: 13px;
            font-style: italic;
            margin: 0;
        }

        .category-item:hover {
            border-color: #2c5f2d;
            transform: translateY(-2px);
            box-shadow: 0 12px 24px rgba(44, 95, 45, 0.15);
        }

        .category-item.selected {
            background: linear-gradient(135deg, #2c5f2d 0%, #1e4220 100%);
            border-color: #2c5f2d;
            color: white;
            transform: translateY(-1px);
            box-shadow: 0 12px 24px rgba(44, 95, 45, 0.25);
        }

        .category-item .material-icons-outlined {
            font-size: 44px;
            margin-bottom: 16px;
            display: block;
            color: #2c5f2d;
        }

        .category-item.selected .material-icons-outlined {
            color: white;
        }

        .category-name {
            font-size: 16px;
            font-weight: 600;
            line-height: 1.3;
            margin-bottom: 8px;
        }

        .category-description {
            font-size: 12px;
            color: #9ca3af;
            line-height: 1.5;
            flex: 1;
        }

        .category-item.selected .category-description {
            color: rgba(255, 255, 255, 0.9);
        }

        .remove-icon {
            position: absolute;
            top: 10px;
            right: 10px;
            background: rgba(255, 255, 255, 0.95);
            color: #2c5f2d;
            border-radius: 50%;
            width: 26px;
            height: 26px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            font-weight: 600;
            opacity: 0;
            transition: all 0.3s ease;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .category-item.selected .remove-icon {
            opacity: 1;
        }

        .continue-btn {
            background: linear-gradient(135deg, #2c5f2d 0%, #1e4220 100%);
            color: white;
            border: none;
            border-radius: 20px;
            padding: 20px 48px;
            font-size: 18px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            width: 100%;
            max-width: 380px;
            box-shadow: 0 8px 24px rgba(44, 95, 45, 0.25);
        }

        .continue-btn:hover:not(:disabled) {
            background: linear-gradient(135deg, #1e4220 0%, #0f2110 100%);
            transform: translateY(-2px);
            box-shadow: 0 12px 32px rgba(44, 95, 45, 0.35);
        }

        .continue-btn:disabled {
            background: #d1d5db;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }

        .alert {
            padding: 16px;
            border-radius: 12px;
            margin-bottom: 24px;
            display: none;
            font-weight: 500;
        }

        .alert.error {
            background: #fef2f2;
            color: #dc2626;
            border: 1px solid #fecaca;
        }

        .alert.success {
            background: #f0fdf4;
            color: #16a34a;
            border: 1px solid #bbf7d0;
        }

        .loading {
            display: none;
            text-align: center;
            padding: 30px;
        }

        .spinner {
            border: 4px solid #f3f4f6;
            border-top: 4px solid #2c5f2d;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            animation: spin 1s linear infinite;
            margin: 0 auto 20px;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Scrollbar styling */
        .categories-grid::-webkit-scrollbar {
            width: 10px;
        }

        .categories-grid::-webkit-scrollbar-track {
            background: #f9fafb;
            border-radius: 10px;
        }

        .categories-grid::-webkit-scrollbar-thumb {
            background: #d1d5db;
            border-radius: 10px;
        }

        .categories-grid::-webkit-scrollbar-thumb:hover {
            background: #9ca3af;
        }

        /* Responsive design */
        @media (max-width: 768px) {
            .preference-container {
                padding: 30px 20px;
                max-height: 95vh;
            }

            .preference-header h1 {
                font-size: 24px;
            }

            .categories-grid {
                grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
                gap: 15px;
                max-height: 400px;
            }

            .category-item {
                padding: 20px 15px;
                min-height: 160px;
            }

            .category-item .material-icons-outlined {
                font-size: 32px;
            }

            .category-name {
                font-size: 14px;
            }

            .category-description {
                font-size: 11px;
            }

            .continue-btn {
                padding: 16px 32px;
                font-size: 16px;
            }
        }

        @media (max-width: 480px) {
            .categories-grid {
                grid-template-columns: 1fr;
                gap: 12px;
            }

            .category-item {
                min-height: 140px;
                padding: 16px 12px;
            }
        }
    </style>
</head>
<body>
    <div class="preference-container">
        <div class="preference-header">
            <h1>🤖 AI-Powered Personalized Experience</h1>
            <p>Choose up to 8 categories and let our AI curate the perfect San Jose del Monte experience for you</p>
            <div class="selection-count">
                <span id="selectedCount">0</span> of 8 selected
            </div>
        </div>

        <div class="ai-suggestions">
            <h3>🧠 AI Smart Suggestions</h3>
            <ul>
                <?php foreach (getAISuggestions() as $suggestion): ?>
                    <li><?php echo htmlspecialchars($suggestion); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>

        <div class="ai-personality-insights" id="personalityInsights">
            <h3>✨ Your Travel Personality</h3>
            <p id="personalityText"></p>
        </div>

        <div id="alertMessage" class="alert"></div>

        <form id="preferenceForm">
            <div class="categories-grid" id="categoriesGrid">
                <?php foreach ($categories as $category): ?>
                    <div class="category-item" data-category="<?php echo htmlspecialchars($category['name']); ?>" data-score="<?php echo htmlspecialchars($category['ai_recommendation_score']); ?>">
                        <?php if ($category['ai_recommendation_score'] >= 90): ?>
                            <div class="ai-recommendation" title="AI Recommendation Score: <?php echo $category['ai_recommendation_score']; ?>%">
                                <?php echo $category['ai_recommendation_score']; ?>%
                            </div>
                        <?php endif; ?>
                        
                        <div class="ai-benefits">
                            <h4>🎯 Actual SJDM Spots</h4>
                            <ul>
                                <?php foreach ($category['actual_spots'] as $spot): ?>
                                    <li><?php echo htmlspecialchars($spot); ?></li>
                                <?php endforeach; ?>
                            </ul>
                            <div style="margin-top: 8px; padding-top: 8px; border-top: 1px solid #e5e7eb;">
                                <strong>⭐ <?php echo $category['avg_rating']; ?>/5.0</strong> (<?php echo $category['total_reviews']; ?> reviews)
                            </div>
                        </div>
                        
                        <span class="material-icons-outlined"><?php echo htmlspecialchars($category['icon']); ?></span>
                        <div class="category-name"><?php echo htmlspecialchars($category['display_name']); ?></div>
                        <div class="category-description"><?php echo htmlspecialchars($category['description']); ?></div>
                        
                        <div class="ai-tags">
                            <?php foreach (array_slice($category['ai_tags'], 0, 3) as $tag): ?>
                                <span class="ai-tag"><?php echo htmlspecialchars($tag); ?></span>
                            <?php endforeach; ?>
                        </div>
                        
                        <span class="remove-icon">✕</span>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="smart-recommendations" id="smartRecommendations" style="display: none;">
                <h4>🎯 AI Recommendation</h4>
                <p class="recommendation-text" id="recommendationText"></p>
            </div>

            <button type="submit" class="continue-btn" id="continueBtn" disabled>
                Continue to Dashboard
            </button>
        </form>

        <div class="loading" id="loading">
            <div class="spinner"></div>
            <p>Saving your preferences...</p>
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

            let selectedCategories = new Set();
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
                    actual_spots: ['City Oval (People\'s Park)'],
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
                    actual_spots: ['Abes Farm', 'City Oval (People\'s Park)', 'Our Lady of Lourdes Parish'],
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

            // AI-powered smart recommendations based on real SJDM data
            function getSmartRecommendation() {
                const selectedArray = Array.from(selectedCategories);
                
                if (selectedArray.length === 0) return '';
                
                // Find complementary categories based on actual spots
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
                
                return 'Great choices! You\'ll love exploring SJDM\'s premier destinations.';
            }

            // Update personality insights with real SJDM data
            function updatePersonalityInsights() {
                const selectedArray = Array.from(selectedCategories);
                
                if (selectedArray.length < 2) {
                    personalityInsights.style.display = 'none';
                    return;
                }
                
                let personalities = [];
                let topSpots = [];
                selectedArray.forEach(cat => {
                    if (aiInsights[cat]) {
                        personalities.push(aiInsights[cat].personality);
                        if (aiInsights[cat].actual_spots) {
                            topSpots.push(...aiInsights[cat].actual_spots);
                        }
                    }
                });
                
                if (personalities.length > 0) {
                    const uniquePersonalities = [...new Set(personalities)];
                    const uniqueSpots = [...new Set(topSpots)];
                    
                    if (uniquePersonalities.length === 1) {
                        personalityText.textContent = `You're a ${uniquePersonalities[0]}! Top spots: ${uniqueSpots.slice(0, 2).join(', ')}`;
                    } else {
                        personalityText.textContent = `Diverse interests: ${uniquePersonalities.slice(0, 2).join(' & ')}! Must-visit: ${uniqueSpots.slice(0, 2).join(', ')}`;
                    }
                    personalityInsights.style.display = 'block';
                } else {
                    personalityInsights.style.display = 'none';
                }
            }

            // Update smart recommendations
            function updateSmartRecommendations() {
                const recommendation = getSmartRecommendation();
                if (recommendation) {
                    recommendationText.textContent = recommendation;
                    smartRecommendations.style.display = 'block';
                } else {
                    smartRecommendations.style.display = 'none';
                }
            }

            // Category selection logic with AI enhancements
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
                        
                        // AI celebration for high-score selections
                        if (score >= 90) {
                            setTimeout(() => {
                                showAlert(`Excellent choice! "${this.querySelector('.category-name').textContent}" is highly recommended by our AI!`, 'success');
                            }, 300);
                        }
                    }
                    
                    updateSelectionCount();
                    updateContinueButton();
                    updatePersonalityInsights();
                    updateSmartRecommendations();
                });
            });

            function updateSelectionCount() {
                selectedCountSpan.textContent = selectedCategories.size;
            }

            function updateContinueButton() {
                continueBtn.disabled = selectedCategories.size === 0;
            }

            function showAlert(message, type) {
                alertMessage.textContent = message;
                alertMessage.className = 'alert ' + type;
                alertMessage.style.display = 'block';
                
                setTimeout(() => {
                    alertMessage.style.display = 'none';
                }, 5000);
            }

            // Enhanced form submission with AI analytics
            preferenceForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                if (selectedCategories.size === 0) {
                    showAlert('Please select at least one category', 'error');
                    return;
                }

                // Show loading state
                preferenceForm.style.display = 'none';
                loading.style.display = 'block';

                // Prepare form data with AI insights
                const formData = new FormData();
                selectedCategories.forEach(category => {
                    formData.append('categories[]', category);
                });
                
                // Add AI analytics data
                formData.append('ai_insights', JSON.stringify({
                    selected_count: selectedCategories.size,
                    personality_insights: Array.from(selectedCategories).map(cat => aiInsights[cat]?.personality).filter(Boolean),
                    selection_pattern: 'ai_enhanced'
                }));

                // Send AJAX request
                fetch('user-preferences.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showAlert(data.message, 'success');
                        setTimeout(() => {
                            window.location.href = 'user-index.php';
                        }, 1500);
                    } else {
                        showAlert(data.message, 'error');
                        preferenceForm.style.display = 'block';
                        loading.style.display = 'none';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showAlert('An error occurred. Please try again.', 'error');
                    preferenceForm.style.display = 'block';
                    loading.style.display = 'none';
                });
            });

            // Initialize AI features on load
            updatePersonalityInsights();
            updateSmartRecommendations();
        });
    </script>
</body>
</html>
