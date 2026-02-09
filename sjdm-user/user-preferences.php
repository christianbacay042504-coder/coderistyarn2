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
        header('Location: index.php');
        exit();
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    
    try {
        $selectedCategories = $_POST['categories'] ?? [];
        
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

// Fetch available categories (tourist spot categories)
$categories = [
    [
        'name' => 'nature',
        'display_name' => 'Nature & Waterfalls',
        'icon' => 'forest',
        'description' => 'Beautiful waterfalls, forests, and natural landscapes'
    ],
    [
        'name' => 'farm',
        'display_name' => 'Farms & Eco-Tourism',
        'icon' => 'agriculture',
        'description' => 'Organic farms, gardens, and agricultural experiences'
    ],
    [
        'name' => 'park',
        'display_name' => 'Parks & Recreation',
        'icon' => 'park',
        'description' => 'Public parks, gardens, and recreational areas'
    ],
    [
        'name' => 'adventure',
        'display_name' => 'Adventure & Activities',
        'icon' => 'hiking',
        'description' => 'Thrilling adventures and outdoor activities'
    ],
    [
        'name' => 'cultural',
        'display_name' => 'Cultural & Historical',
        'icon' => 'museum',
        'description' => 'Museums, historical sites, and cultural landmarks'
    ],
    [
        'name' => 'religious',
        'display_name' => 'Religious Sites',
        'icon' => 'church',
        'description' => 'Churches, temples, and spiritual destinations'
    ],
    [
        'name' => 'entertainment',
        'display_name' => 'Entertainment & Leisure',
        'icon' => 'sports_esports',
        'description' => 'Entertainment venues and leisure activities'
    ],
    [
        'name' => 'food',
        'display_name' => 'Food & Dining',
        'icon' => 'restaurant',
        'description' => 'Local cuisine, restaurants, and dining experiences'
    ],
    [
        'name' => 'shopping',
        'display_name' => 'Shopping & Markets',
        'icon' => 'shopping_cart',
        'description' => 'Local markets, shopping centers, and souvenirs'
    ],
    [
        'name' => 'wellness',
        'display_name' => 'Wellness & Relaxation',
        'icon' => 'spa',
        'description' => 'Spas, wellness centers, and relaxation spots'
    ],
    [
        'name' => 'education',
        'display_name' => 'Educational & Learning',
        'icon' => 'school',
        'description' => 'Educational centers and learning experiences'
    ],
    [
        'name' => 'family',
        'display_name' => 'Family-Friendly',
        'icon' => 'family_restroom',
        'description' => 'Activities and spots perfect for families'
    ],
    [
        'name' => 'photography',
        'display_name' => 'Photography Spots',
        'icon' => 'photo_camera',
        'description' => 'Scenic locations perfect for photography'
    ],
    [
        'name' => 'wildlife',
        'display_name' => 'Wildlife & Nature',
        'icon' => 'pets',
        'description' => 'Wildlife sanctuaries and nature reserves'
    ],
    [
        'name' => 'outdoor',
        'display_name' => 'Outdoor Activities',
        'icon' => 'terrain',
        'description' => 'Outdoor sports and recreational activities'
    ]
];
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
            min-height: 190px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
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
            <h1>What interests you most in San Jose del Monte?</h1>
            <p>Choose up to 8 categories to personalize your experience</p>
            <div class="selection-count">
                <span id="selectedCount">0</span> of 8 selected
            </div>
        </div>

        <div id="alertMessage" class="alert"></div>

        <form id="preferenceForm">
            <div class="categories-grid" id="categoriesGrid">
                <?php foreach ($categories as $category): ?>
                    <div class="category-item" data-category="<?php echo htmlspecialchars($category['name']); ?>">
                        <span class="material-icons-outlined"><?php echo htmlspecialchars($category['icon']); ?></span>
                        <div class="category-name"><?php echo htmlspecialchars($category['display_name']); ?></div>
                        <div class="category-description"><?php echo htmlspecialchars($category['description']); ?></div>
                        <span class="remove-icon">✕</span>
                    </div>
                <?php endforeach; ?>
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

            let selectedCategories = new Set();

            // Category selection logic
            categoryItems.forEach(item => {
                item.addEventListener('click', function() {
                    const category = this.dataset.category;
                    
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
                    }
                    
                    updateSelectionCount();
                    updateContinueButton();
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

            // Form submission
            preferenceForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                if (selectedCategories.size === 0) {
                    showAlert('Please select at least one category', 'error');
                    return;
                }

                // Show loading state
                preferenceForm.style.display = 'none';
                loading.style.display = 'block';

                // Prepare form data
                const formData = new FormData();
                selectedCategories.forEach(category => {
                    formData.append('categories[]', category);
                });

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
                            window.location.href = 'index.php';
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
        });
    </script>
</body>
</html>
