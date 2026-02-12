<?php
session_start();
require_once '../config/database.php';

// Simulate the same logic as book.php but just output the guide selection section
$user_id = $_SESSION['user_id'] ?? null;
$preselected_guide = $_GET['guide'] ?? '';

try {
    $conn = getDatabaseConnection();
    if ($conn) {
        // Fetch available tour guides (only verified and active)
        $tour_guides = [];
        $guidesStmt = $conn->prepare("SELECT id, name, specialty, category, verified, experience_years, languages, group_size, description FROM tour_guides WHERE verified = 1 AND status = 'active' ORDER BY name ASC");
        if ($guidesStmt) {
            $guidesStmt->execute();
            $guidesResult = $guidesStmt->get_result();

            while ($guide = $guidesResult->fetch_assoc()) {
                $tour_guides[] = [
                    'id' => $guide['id'],
                    'name' => $guide['name'],
                    'specialty' => $guide['specialty'],
                    'category' => $guide['category'],
                    'verified' => $guide['verified'],
                    'experience' => $guide['experience_years'] ? $guide['experience_years'] . '+ years' : '5+ years',
                    'languages' => $guide['languages'] ?? 'English, Tagalog',
                    'max_group_size' => $guide['group_size'] ?? '10 guests',
                    'description' => $guide['description'] ?? 'Experienced tour guide ready to show you the best of San Jose del Monte.'
                ];
            }
            $guidesStmt->close();
        }
        closeDatabaseConnection($conn);
    } else {
        $tour_guides = [];
    }
} catch (Exception $e) {
    $tour_guides = [];
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Debug Book Page</title>
    <style>
        .guide-selection-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 20px; }
        .guide-selection-card { border: 2px solid #e5e7eb; border-radius: 16px; padding: 20px; cursor: pointer; }
        .guide-selection-card:hover { border-color: #2c5f2d; }
        .guide-selection-card.selected { border-color: #2c5f2d; background: #f0f9ff; }
        .guide-avatar { width: 40px; height: 40px; border-radius: 50%; background: #2c5f2d; color: white; display: flex; align-items: center; justify-content: center; font-weight: bold; margin-bottom: 10px; }
        .no-guides-message { text-align: center; padding: 40px; color: #666; }
    </style>
</head>
<body>
    <h2>Debug: Guide Selection Section</h2>
    
    <div class="guide-selection-section">
        <label>Select Tour Guide *</label>
        <div class="guide-selection-grid">
            <?php if (!empty($tour_guides)): ?>
                <?php foreach ($tour_guides as $guide): ?>
                    <div class="guide-selection-card <?php echo ($preselected_guide == $guide['id']) ? 'selected' : ''; ?>" data-guide-id="<?php echo $guide['id']; ?>" onclick="selectGuide(<?php echo $guide['id']; ?>)">
                        <div class="guide-card-header">
                            <div class="guide-avatar">
                                <?php echo strtoupper(substr($guide['name'], 0, 1)); ?>
                            </div>
                            <div class="guide-info">
                                <h4><?php echo htmlspecialchars($guide['name']); ?></h4>
                                <span class="guide-specialty"><?php echo htmlspecialchars($guide['specialty']); ?></span>
                            </div>
                        </div>
                        <div class="guide-details">
                            <div class="guide-detail">
                                <span>⏰</span>
                                <?php echo htmlspecialchars($guide['experience']); ?> experience
                            </div>
                            <div class="guide-detail">
                                <span>🌐</span>
                                <?php echo htmlspecialchars($guide['languages']); ?>
                            </div>
                            <div class="guide-detail">
                                <span>👥</span>
                                Up to <?php echo htmlspecialchars($guide['max_group_size']); ?>
                            </div>
                        </div>
                        <div class="guide-description">
                            <?php echo htmlspecialchars($guide['description']); ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="no-guides-message">
                    <span>🚫</span>
                    <h3>No tour guides available</h3>
                    <p>Please check back later for available tour guides.</p>
                </div>
            <?php endif; ?>
        </div>
        <input type="hidden" id="selectedGuide" name="selectedGuide" value="<?php echo htmlspecialchars($preselected_guide); ?>" required>
    </div>
    
    <script>
        function selectGuide(guideId) {
            document.querySelectorAll('.guide-selection-card').forEach(card => {
                card.classList.remove('selected');
            });
            
            const selectedCard = document.querySelector('[data-guide-id="' + guideId + '"]');
            if (selectedCard) {
                selectedCard.classList.add('selected');
            }
            
            document.getElementById('selectedGuide').value = guideId;
            console.log('Selected guide ID:', guideId);
        }
    </script>
</body>
</html>
