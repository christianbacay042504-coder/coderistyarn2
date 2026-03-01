<?php
// Helper functions for tourist detail pages
require_once __DIR__ . '/../config/database.php';

/**
 * Get assigned guide for a specific tourist spot
 * @param string $spotName The name of the tourist spot
 * @return array|null The assigned guide data or null if no guide assigned
 */
function getAssignedGuideForSpot($spotName) {
    $assignedGuide = null;
    try {
        $conn = getDatabaseConnection();
        
        // Get the tourist spot ID and assigned guide
        $spotQuery = "SELECT id, assigned_guide FROM tourist_spots WHERE name = ? LIMIT 1";
        $stmt = $conn->prepare($spotQuery);
        $stmt->bind_param("s", $spotName);
        $stmt->execute();
        $spotResult = $stmt->get_result();
        
        if ($spotResult && $spot = $spotResult->fetch_assoc()) {
            if ($spot['assigned_guide']) {
                // Get guide details
                $guideQuery = "SELECT tg.*, u.email 
                              FROM tour_guides tg 
                              LEFT JOIN users u ON tg.user_id = u.id 
                              WHERE tg.id = ? LIMIT 1";
                $guideStmt = $conn->prepare($guideQuery);
                $guideStmt->bind_param("i", $spot['assigned_guide']);
                $guideStmt->execute();
                $guideResult = $guideStmt->get_result();
                
                if ($guideResult) {
                    $assignedGuide = $guideResult->fetch_assoc();
                }
                $guideStmt->close();
            }
        }
        $stmt->close();
        
        closeDatabaseConnection($conn);
    } catch (Exception $e) {
        // If there's an error, just return null
        $assignedGuide = null;
    }
    
    return $assignedGuide;
}

/**
 * Format guide specialty for display
 * @param string $specialty The guide's specialty
 * @return string Formatted specialty
 */
function formatGuideSpecialty($specialty) {
    return htmlspecialchars($specialty ?: 'Tour Guide');
}

/**
 * Format guide rating with stars
 * @param float $rating The guide's rating
 * @param int $reviewCount Number of reviews
 * @return string Formatted rating with stars
 */
function formatGuideRating($rating, $reviewCount) {
    $rating = floatval($rating);
    $reviewCount = intval($reviewCount);
    $stars = str_repeat('⭐', min(5, max(1, round($rating))));
    return "{$stars} {$rating} ({$reviewCount} reviews)";
}

/**
 * Initialize assigned guide data for a tourist spot
 * @param string $spotName The name of the tourist spot
 * @return array|null The assigned guide data
 */
function initializeAssignedGuide($spotName) {
    static $guideCache = [];
    
    // Use caching to avoid multiple database calls for the same spot
    if (!isset($guideCache[$spotName])) {
        $guideCache[$spotName] = getAssignedGuideForSpot($spotName);
    }
    
    return $guideCache[$spotName];
}
?>
