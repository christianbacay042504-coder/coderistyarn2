<?php
require_once __DIR__ . '/../config/database.php';

/**
 * Automatically assigns a tour guide based on the destination
 * Uses category matching between tourist spots and tour guides
 * 
 * @param string $destination The name of the destination/tourist spot
 * @return int|null The ID of the assigned tour guide, or null if no guide available
 */
function assignTourGuideByDestination($destination) {
    if (empty($destination)) {
        return null;
    }
    
    $conn = getDatabaseConnection();
    if (!$conn) {
        return null;
    }
    
    try {
        // First, get the category of the destination from tourist_spots table
        $spotQuery = "SELECT category FROM tourist_spots WHERE name LIKE ? AND status = 'active' LIMIT 1";
        $spotStmt = $conn->prepare($spotQuery);
        if (!$spotStmt) {
            return null;
        }
        
        $spotName = '%' . $destination . '%';
        $spotStmt->bind_param('s', $spotName);
        $spotStmt->execute();
        $spotResult = $spotStmt->get_result();
        
        if ($spotResult->num_rows === 0) {
            $spotStmt->close();
            closeDatabaseConnection($conn);
            return null;
        }
        
        $spot = $spotResult->fetch_assoc();
        $category = $spot['category'];
        $spotStmt->close();
        
        // Map tourist spot categories to tour guide categories
        $categoryMapping = [
            'nature' => 'mountain',
            'historical' => 'historical', 
            'religious' => 'historical',
            'farm' => 'farm',
            'park' => 'city',
            'urban' => 'city'
        ];
        
        $guideCategory = $categoryMapping[$category] ?? 'general';
        
        // Find the best available tour guide for this category
        // Prioritize: 1) Verified guides, 2) Highest rating, 3) Most experience
        $guideQuery = "SELECT id FROM tour_guides 
                      WHERE category = ? AND status = 'active' 
                      ORDER BY verified DESC, rating DESC, experience_years DESC 
                      LIMIT 1";
        
        $guideStmt = $conn->prepare($guideQuery);
        if (!$guideStmt) {
            closeDatabaseConnection($conn);
            return null;
        }
        
        $guideStmt->bind_param('s', $guideCategory);
        $guideStmt->execute();
        $guideResult = $guideStmt->get_result();
        
        if ($guideResult->num_rows === 0) {
            // If no specific category guide found, try general category
            $guideQuery = "SELECT id FROM tour_guides 
                          WHERE category = 'general' AND status = 'active' 
                          ORDER BY verified DESC, rating DESC, experience_years DESC 
                          LIMIT 1";
            
            $guideResult = $conn->query($guideQuery);
            
            if (!$guideResult || $guideResult->num_rows === 0) {
                closeDatabaseConnection($conn);
                return null;
            }
        }
        
        $guide = $guideResult->fetch_assoc();
        $guideId = $guide['id'];
        
        if (isset($guideStmt)) {
            $guideStmt->close();
        }
        
        closeDatabaseConnection($conn);
        return $guideId;
        
    } catch (Exception $e) {
        error_log("Error assigning tour guide: " . $e->getMessage());
        closeDatabaseConnection($conn);
        return null;
    }
}

/**
 * Gets tour guide information by ID
 * 
 * @param int $guideId The tour guide ID
 * @return array|null Tour guide information or null if not found
 */
function getTourGuideInfo($guideId) {
    if (empty($guideId)) {
        return null;
    }
    
    $conn = getDatabaseConnection();
    if (!$conn) {
        return null;
    }
    
    try {
        $query = "SELECT id, name, specialty, category, rating, contact_number, email 
                 FROM tour_guides 
                 WHERE id = ? AND status = 'active'";
        
        $stmt = $conn->prepare($query);
        if (!$stmt) {
            closeDatabaseConnection($conn);
            return null;
        }
        
        $stmt->bind_param('i', $guideId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            $stmt->close();
            closeDatabaseConnection($conn);
            return null;
        }
        
        $guide = $result->fetch_assoc();
        $stmt->close();
        closeDatabaseConnection($conn);
        
        return $guide;
        
    } catch (Exception $e) {
        error_log("Error getting tour guide info: " . $e->getMessage());
        closeDatabaseConnection($conn);
        return null;
    }
}

/**
 * Gets available tour guides for a specific destination
 * 
 * @param string $destination The destination name
 * @return array List of available tour guides
 */
function getAvailableGuidesForDestination($destination) {
    if (empty($destination)) {
        return [];
    }
    
    $conn = getDatabaseConnection();
    if (!$conn) {
        return [];
    }
    
    try {
        // Get destination category
        $spotQuery = "SELECT category FROM tourist_spots WHERE name LIKE ? AND status = 'active' LIMIT 1";
        $spotStmt = $conn->prepare($spotQuery);
        if (!$spotStmt) {
            return [];
        }
        
        $spotName = '%' . $destination . '%';
        $spotStmt->bind_param('s', $spotName);
        $spotStmt->execute();
        $spotResult = $spotStmt->get_result();
        
        if ($spotResult->num_rows === 0) {
            $spotStmt->close();
            closeDatabaseConnection($conn);
            return [];
        }
        
        $spot = $spotResult->fetch_assoc();
        $category = $spot['category'];
        $spotStmt->close();
        
        // Map categories
        $categoryMapping = [
            'nature' => 'mountain',
            'historical' => 'historical', 
            'religious' => 'historical',
            'farm' => 'farm',
            'park' => 'city',
            'urban' => 'city'
        ];
        
        $guideCategory = $categoryMapping[$category] ?? 'general';
        
        // Get all available guides for this category
        $guideQuery = "SELECT id, name, specialty, rating, experience_years, verified 
                      FROM tour_guides 
                      WHERE category = ? AND status = 'active' 
                      ORDER BY verified DESC, rating DESC, experience_years DESC";
        
        $guideStmt = $conn->prepare($guideQuery);
        if (!$guideStmt) {
            closeDatabaseConnection($conn);
            return [];
        }
        
        $guideStmt->bind_param('s', $guideCategory);
        $guideStmt->execute();
        $guideResult = $guideStmt->get_result();
        
        $guides = [];
        while ($guide = $guideResult->fetch_assoc()) {
            $guides[] = $guide;
        }
        
        $guideStmt->close();
        closeDatabaseConnection($conn);
        
        return $guides;
        
    } catch (Exception $e) {
        error_log("Error getting available guides: " . $e->getMessage());
        closeDatabaseConnection($conn);
        return [];
    }
}
?>
