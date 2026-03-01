<?php
require_once __DIR__ . '/../config/database.php';

/**
 * Get assigned tour guide for a destination by name
 * @param string $destinationName The name of the tourist spot
 * @return array|null Tour guide information or null if not assigned
 */
function getAssignedTourGuide($destinationName) {
    $conn = getDatabaseConnection();
    if (!$conn) {
        return null;
    }
    
    try {
        $stmt = $conn->prepare("
            SELECT tg.id, tg.name, tg.specialty, tg.category, tg.rating, tg.review_count, 
                   tg.contact_number, tg.email, tg.photo_url, tg.bio
            FROM tourist_spots ts 
            LEFT JOIN tour_guides tg ON ts.assigned_guide = tg.id 
            WHERE ts.name = ? AND ts.status = 'active' AND (tg.status = 'active' OR tg.status = 'approved' OR tg.status = 'verified')
        ");
        
        $stmt->bind_param("s", $destinationName);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $guide = $result->fetch_assoc();
            closeDatabaseConnection($conn);
            return $guide;
        }
        
        closeDatabaseConnection($conn);
        return null;
        
    } catch (Exception $e) {
        error_log("Error getting assigned tour guide: " . $e->getMessage());
        closeDatabaseConnection($conn);
        return null;
    }
}

/**
 * Get assigned tour guide by destination ID
 * @param int $destinationId The ID of the tourist spot
 * @return array|null Tour guide information or null if not assigned
 */
function getAssignedTourGuideById($destinationId) {
    $conn = getDatabaseConnection();
    if (!$conn) {
        return null;
    }
    
    try {
        $stmt = $conn->prepare("
            SELECT tg.id, tg.name, tg.specialty, tg.category, tg.rating, tg.review_count, 
                   tg.contact_number, tg.email, tg.photo_url, tg.bio
            FROM tourist_spots ts 
            LEFT JOIN tour_guides tg ON ts.assigned_guide = tg.id 
            WHERE ts.id = ? AND ts.status = 'active' AND (tg.status = 'active' OR tg.status = 'approved' OR tg.status = 'verified')
        ");
        
        $stmt->bind_param("i", $destinationId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $guide = $result->fetch_assoc();
            closeDatabaseConnection($conn);
            return $guide;
        }
        
        closeDatabaseConnection($conn);
        return null;
        
    } catch (Exception $e) {
        error_log("Error getting assigned tour guide: " . $e->getMessage());
        closeDatabaseConnection($conn);
        return null;
    }
}

/**
 * Format tour guide rating display
 * @param float $rating The rating value
 * @param int $reviewCount Number of reviews
 * @return string Formatted rating display
 */
function formatGuideRating($rating, $reviewCount) {
    $stars = '';
    $fullStars = floor($rating);
    $hasHalfStar = ($rating - $fullStars) >= 0.5;
    
    for ($i = 0; $i < $fullStars; $i++) {
        $stars .= '⭐';
    }
    if ($hasHalfStar) {
        $stars .= '✨';
    }
    
    return $stars . ' ' . number_format($rating, 1) . ' (' . $reviewCount . ' reviews)';
}

/**
 * Get tour guide specialty display
 * @param string $specialty The specialty value
 * @return string Formatted specialty display
 */
function formatGuideSpecialty($specialty) {
    if (empty($specialty)) {
        return 'General Guide';
    }
    
    // Format specialty to be more readable
    $specialtyMap = [
        'mountain' => 'Mountain Trekking Guide',
        'waterfall' => 'Waterfall & Nature Guide',
        'city' => 'City & Historical Guide',
        'farm' => 'Agricultural & Farm Guide',
        'general' => 'General Tour Guide',
        'historical' => 'Historical & Cultural Guide'
    ];
    
    return $specialtyMap[strtolower($specialty)] ?? ucfirst($specialty) . ' Guide';
}
?>
