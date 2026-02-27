<?php
// Preferences Modal Component
// This file contains the reusable preferences modal for all user pages
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../config/database.php';

// Check if user is logged in
if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit();
}

// Get current user preferences
$conn = getDatabaseConnection();
$userPreferences = [];
if ($conn) {
    $stmt = $conn->prepare("SELECT category FROM user_preferences WHERE user_id = ?");
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($pref = $result->fetch_assoc()) {
        $userPreferences[] = $pref['category'];
    }
    $stmt->close();
    closeDatabaseConnection($conn);
}

// Categories data
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
?>

<!-- Preferences Modal -->
<div id="preferencesModal" class="preferences-modal">
    <div class="modal-overlay" onclick="closePreferencesModal()"></div>
    <div class="modal-content">
        <div class="modal-header">
            <h2>🎯 Your Travel Preferences</h2>
            <p>Customize your SJDM experience by selecting your interests</p>
            <button class="close-btn" onclick="closePreferencesModal()">
                <span class="material-icons-outlined">close</span>
            </button>
        </div>
        
        <div class="modal-body">
            <div class="selection-counter">
                <span id="selectedCount"><?php echo count($userPreferences); ?></span> of 8 selected
            </div>
            
            <div class="categories-grid">
                <?php foreach ($categories as $category): ?>
                    <div class="category-item <?php echo in_array($category['name'], $userPreferences) ? 'selected' : ''; ?>" 
                         data-category="<?php echo htmlspecialchars($category['name']); ?>">
                        <div class="category-icon">
                            <span class="material-icons-outlined"><?php echo htmlspecialchars($category['icon']); ?></span>
                        </div>
                        <div class="category-emoji"><?php echo $category['emoji']; ?></div>
                        <div class="category-name"><?php echo htmlspecialchars($category['display_name']); ?></div>
                        <div class="category-description"><?php echo htmlspecialchars($category['description']); ?></div>
                        
                        <div class="category-stats">
                            <div class="rating">
                                <span class="stars">⭐</span>
                                <span class="rating-value"><?php echo $category['avg_rating']; ?></span>
                                <span class="review-count">(<?php echo $category['total_reviews']; ?>)</span>
                            </div>
                        </div>
                        
                        <div class="ai-score" title="AI Recommendation Score">
                            🤖 <?php echo $category['ai_recommendation_score']; ?>%
                        </div>
                        
                        <div class="check-badge">
                            <span class="material-icons-outlined">check</span>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <div id="preferencesAlert" class="alert"></div>
        </div>
        
        <div class="modal-footer">
            <button class="btn-secondary" onclick="closePreferencesModal()">Cancel</button>
            <button class="btn-primary" id="savePreferencesBtn" onclick="savePreferences()">
                Save Preferences
            </button>
        </div>
    </div>
</div>

<style>
/* Preferences Modal Styles */
.preferences-modal {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    z-index: 10000;
    display: none;
    align-items: center;
    justify-content: center;
    animation: fadeIn 0.3s ease;
}

.preferences-modal.show {
    display: flex;
}

.modal-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.5);
    backdrop-filter: blur(4px);
}

.modal-content {
    position: relative;
    background: white;
    border-radius: 20px;
    box-shadow: 0 25px 50px rgba(0, 0, 0, 0.25);
    max-width: 900px;
    width: 90%;
    max-height: 85vh;
    overflow: hidden;
    display: flex;
    flex-direction: column;
    animation: slideUp 0.3s ease;
}

.modal-header {
    padding: 24px 32px 20px;
    border-bottom: 1px solid #e5e7eb;
    position: relative;
}

.modal-header h2 {
    margin: 0 0 8px 0;
    font-size: 24px;
    font-weight: 700;
    color: #1f2937;
}

.modal-header p {
    margin: 0;
    color: #6b7280;
    font-size: 14px;
}

.close-btn {
    position: absolute;
    top: 20px;
    right: 20px;
    background: none;
    border: none;
    width: 36px;
    height: 36px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    color: #6b7280;
    transition: all 0.2s ease;
}

.close-btn:hover {
    background: #f3f4f6;
    color: #374151;
}

.modal-body {
    padding: 24px 32px;
    overflow-y: auto;
    flex: 1;
}

.selection-counter {
    background: linear-gradient(135deg, #2c5f2d 0%, #1e4220 100%);
    color: white;
    padding: 12px 20px;
    border-radius: 25px;
    display: inline-block;
    font-weight: 600;
    margin-bottom: 24px;
    font-size: 14px;
}

.categories-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 16px;
    margin-bottom: 24px;
}

.category-item {
    position: relative;
    background: white;
    border: 2px solid #e5e7eb;
    border-radius: 16px;
    padding: 20px;
    cursor: pointer;
    transition: all 0.3s ease;
    min-height: 200px;
    display: flex;
    flex-direction: column;
}

.category-item:hover {
    border-color: #2c5f2d;
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(44, 95, 45, 0.15);
}

.category-item.selected {
    background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
    border-color: #2c5f2d;
    color: #1e4220;
}

.category-icon {
    width: 48px;
    height: 48px;
    background: linear-gradient(135deg, #2c5f2d 0%, #1e4220 100%);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 24px;
    margin-bottom: 12px;
}

.category-emoji {
    font-size: 32px;
    margin-bottom: 8px;
}

.category-name {
    font-size: 16px;
    font-weight: 600;
    color: #1f2937;
    margin-bottom: 6px;
}

.category-description {
    font-size: 12px;
    color: #6b7280;
    line-height: 1.5;
    flex: 1;
    margin-bottom: 12px;
}

.category-stats {
    margin-top: auto;
}

.rating {
    display: flex;
    align-items: center;
    gap: 4px;
    font-size: 12px;
    color: #6b7280;
}

.rating-value {
    font-weight: 600;
    color: #2c5f2d;
}

.ai-score {
    position: absolute;
    top: 12px;
    right: 12px;
    background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
    color: white;
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 10px;
    font-weight: 600;
}

.check-badge {
    position: absolute;
    top: 12px;
    left: 12px;
    width: 24px;
    height: 24px;
    background: #2c5f2d;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    opacity: 0;
    transform: scale(0);
    transition: all 0.3s ease;
}

.category-item.selected .check-badge {
    opacity: 1;
    transform: scale(1);
}

.check-badge .material-icons-outlined {
    font-size: 14px;
}

.alert {
    padding: 12px 16px;
    border-radius: 8px;
    margin-bottom: 16px;
    display: none;
    font-size: 14px;
    font-weight: 500;
}

.alert.success {
    background: #f0fdf4;
    color: #166534;
    border: 1px solid #bbf7d0;
}

.alert.error {
    background: #fef2f2;
    color: #dc2626;
    border: 1px solid #fecaca;
}

.modal-footer {
    padding: 20px 32px;
    border-top: 1px solid #e5e7eb;
    display: flex;
    gap: 12px;
    justify-content: flex-end;
}

.btn-secondary, .btn-primary {
    padding: 10px 20px;
    border-radius: 8px;
    font-weight: 600;
    font-size: 14px;
    cursor: pointer;
    transition: all 0.2s ease;
    border: none;
}

.btn-secondary {
    background: #f3f4f6;
    color: #374151;
}

.btn-secondary:hover {
    background: #e5e7eb;
}

.btn-primary {
    background: linear-gradient(135deg, #2c5f2d 0%, #1e4220 100%);
    color: white;
}

.btn-primary:hover {
    background: linear-gradient(135deg, #1e4220 0%, #0f2110 100%);
}

.btn-primary:disabled {
    background: #d1d5db;
    cursor: not-allowed;
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

@keyframes slideUp {
    from { 
        opacity: 0;
        transform: translateY(20px);
    }
    to { 
        opacity: 1;
        transform: translateY(0);
    }
}

/* Responsive */
@media (max-width: 768px) {
    .modal-content {
        width: 95%;
        max-height: 90vh;
    }
    
    .modal-header, .modal-body, .modal-footer {
        padding-left: 20px;
        padding-right: 20px;
    }
    
    .categories-grid {
        grid-template-columns: 1fr;
        gap: 12px;
    }
    
    .category-item {
        padding: 16px;
        min-height: 160px;
    }
}
</style>

<script>
// Preferences Modal JavaScript
let selectedCategories = new Set(<?php echo json_encode($userPreferences); ?>);

function openPreferencesModal() {
    const modal = document.getElementById('preferencesModal');
    modal.classList.add('show');
    document.body.style.overflow = 'hidden';
    updateCounter();
}

function closePreferencesModal() {
    const modal = document.getElementById('preferencesModal');
    modal.classList.remove('show');
    document.body.style.overflow = '';
}

function updateCounter() {
    const counter = document.getElementById('selectedCount');
    const saveBtn = document.getElementById('savePreferencesBtn');
    
    counter.textContent = selectedCategories.size;
    
    if (selectedCategories.size === 0) {
        saveBtn.disabled = true;
    } else {
        saveBtn.disabled = false;
    }
}

function savePreferences() {
    if (selectedCategories.size === 0) {
        showAlert('Please select at least one category', 'error');
        return;
    }
    
    if (selectedCategories.size > 8) {
        showAlert('You can select up to 8 categories only', 'error');
        return;
    }
    
    const categories = Array.from(selectedCategories);
    
    fetch('../User/update-preferences.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            categories: categories
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('Preferences saved successfully! 🎉', 'success');
            setTimeout(() => {
                closePreferencesModal();
                // Optionally refresh the page to show updated content
                window.location.reload();
            }, 1500);
        } else {
            showAlert(data.message || 'Error saving preferences', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('Network error. Please try again.', 'error');
    });
}

function showAlert(message, type) {
    const alert = document.getElementById('preferencesAlert');
    alert.textContent = message;
    alert.className = `alert ${type}`;
    alert.style.display = 'block';
    
    setTimeout(() => {
        alert.style.display = 'none';
    }, 3000);
}

// Initialize category selection
document.addEventListener('DOMContentLoaded', function() {
    const categoryItems = document.querySelectorAll('.category-item');
    
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
            
            updateCounter();
        });
    });
});

// Close modal on Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closePreferencesModal();
    }
});
</script>
