<?php
// Fix tourist_spots table and re-import destinations
require_once __DIR__ . '/../config/database.php';

function fixTouristSpotsTable() {
    $conn = getDatabaseConnection();
    if (!$conn) {
        echo "Database connection failed!\n";
        return false;
    }
    
    echo "Fixing tourist_spots table structure...\n";
    
    // Disable foreign key checks
    $conn->query("SET FOREIGN_KEY_CHECKS = 0");
    
    // Drop and recreate the table with proper AUTO_INCREMENT
    $dropTable = "DROP TABLE IF EXISTS tourist_spots";
    if ($conn->query($dropTable)) {
        echo "  - Dropped existing tourist_spots table\n";
    }
    
    $createTable = "CREATE TABLE tourist_spots (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(200) NOT NULL,
        description TEXT,
        category ENUM('nature','historical','religious','farm','park','urban','waterfalls','mountains','agri-tourism','religious sites','parks & recreation','tourist spot') DEFAULT 'tourist spot',
        location VARCHAR(200),
        address TEXT,
        operating_hours VARCHAR(100),
        entrance_fee VARCHAR(100),
        difficulty_level ENUM('easy','moderate','difficult') DEFAULT 'easy',
        duration VARCHAR(100),
        best_time_to_visit VARCHAR(100),
        activities TEXT,
        amenities TEXT,
        contact_info VARCHAR(200),
        website VARCHAR(200),
        image_url VARCHAR(500),
        latitude DECIMAL(10,8),
        longitude DECIMAL(11,8),
        rating DECIMAL(3,2) DEFAULT 0.00,
        review_count INT DEFAULT 0,
        status ENUM('active','inactive') DEFAULT 'active',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        INDEX idx_category (category),
        INDEX idx_status (status),
        INDEX idx_rating (rating)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    
    if ($conn->query($createTable)) {
        echo "  - Created tourist_spots table with AUTO_INCREMENT\n";
        
        // Re-enable foreign key checks
        $conn->query("SET FOREIGN_KEY_CHECKS = 1");
        
        return true;
    } else {
        echo "  - Error creating table: " . $conn->error . "\n";
        
        // Re-enable foreign key checks even on error
        $conn->query("SET FOREIGN_KEY_CHECKS = 1");
        
        return false;
    }
    
    closeDatabaseConnection($conn);
}

// Function to extract destination data from a PHP file
function extractDestinationData($filePath) {
    $content = file_get_contents($filePath);
    
    // Extract title from <title> tag
    preg_match('/<title>([^<]+) - San Jose del Monte Tourism<\/title>/', $content, $titleMatch);
    $name = isset($titleMatch[1]) ? trim($titleMatch[1]) : '';
    
    // Extract hero title
    preg_match('/<h1 class="hero-title">([^<]+)<\/h1>/', $content, $heroTitleMatch);
    $displayName = isset($heroTitleMatch[1]) ? trim($heroTitleMatch[1]) : $name;
    
    // Extract hero subtitle
    preg_match('/<p class="hero-subtitle">([^<]+)<\/p>/', $content, $heroSubtitleMatch);
    $subtitle = isset($heroSubtitleMatch[1]) ? trim($heroSubtitleMatch[1]) : '';
    
    // Extract description from About section
    preg_match('/<h2 class="section-title">About[^<]*<\/h2>\s*<p class="description">([^<]+)<\/p>/', $content, $descMatch);
    $description = isset($descMatch[1]) ? trim($descMatch[1]) : $subtitle;
    
    // Extract entrance fee
    preg_match('/<span class="info-label">Entrance Fee<\/span>\s*<span class="info-value">([^<]+)<\/span>/', $content, $feeMatch);
    $entranceFee = isset($feeMatch[1]) ? trim($feeMatch[1]) : 'Free';
    
    // Extract tour duration
    preg_match('/<span class="info-label">Tour Duration<\/span>\s*<span class="info-value">([^<]+)<\/span>/', $content, $durationMatch);
    $duration = isset($durationMatch[1]) ? trim($durationMatch[1]) : '2-3 Hours';
    
    // Extract difficulty level
    preg_match('/<span class="info-label">Difficulty Level<\/span>\s*<span class="info-value">([^<]+)<\/span>/', $content, $difficultyMatch);
    $difficultyLevel = isset($difficultyMatch[1]) ? strtolower(trim($difficultyMatch[1])) : 'easy';
    
    // Extract operating hours
    preg_match('/<span class="info-label">Daily Schedule<\/span>\s*<span class="info-value">([^<]+)<\/span>/', $content, $hoursMatch);
    $operatingHours = isset($hoursMatch[1]) ? trim($hoursMatch[1]) : '8:00 AM - 5:00 PM';
    
    // Extract address
    preg_match('/<span class="info-label">Address<\/span>\s*<span class="info-value">([^<]+)<\/span>/', $content, $addressMatch);
    $address = isset($addressMatch[1]) ? trim($addressMatch[1]) : 'San Jose del Monte, Bulacan';
    
    // Extract contact
    preg_match('/<span class="info-label">Contact Number<\/span>\s*<span class="info-value">([^<]+)<\/span>/', $content, $contactMatch);
    $contactInfo = isset($contactMatch[1]) ? trim($contactMatch[1]) : '+63 9XX XXX XXXX';
    
    // Extract first image URL from gallery
    preg_match('/<div class="gallery-item">\s*<img src="([^"]+)"/', $content, $imageMatch);
    $imageUrl = isset($imageMatch[1]) ? trim($imageMatch[1]) : '';
    
    // Determine category based on filename or content
    $category = determineCategory($filePath, $name, $description);
    
    // Determine location
    $location = 'San Jose del Monte, Bulacan';
    
    // Determine activities based on content
    $activities = determineActivities($filePath, $name, $description);
    
    // Determine amenities based on content
    $amenities = determineAmenities($filePath, $name, $description);
    
    return [
        'name' => $displayName,
        'description' => $description,
        'category' => $category,
        'location' => $location,
        'address' => $address,
        'operating_hours' => $operatingHours,
        'entrance_fee' => $entranceFee,
        'difficulty_level' => $difficultyLevel,
        'duration' => $duration,
        'best_time_to_visit' => 'Year-round',
        'activities' => $activities,
        'amenities' => $amenities,
        'contact_info' => $contactInfo,
        'website' => '',
        'image_url' => $imageUrl,
        'rating' => 4.5,
        'review_count' => 0
    ];
}

function determineCategory($filePath, $name, $description) {
    $filename = basename($filePath, '.php');
    $nameLower = strtolower($name);
    $descLower = strtolower($description);
    
    // Farm/Agriculture
    if (strpos($filename, 'farm') !== false || strpos($nameLower, 'farm') !== false || strpos($descLower, 'farm') !== false || strpos($descLower, 'agri') !== false) {
        return 'agri-tourism';
    }
    
    // Falls/Waterfalls
    if (strpos($filename, 'falls') !== false || strpos($nameLower, 'falls') !== false || strpos($descLower, 'falls') !== false || strpos($descLower, 'waterfall') !== false) {
        return 'waterfalls';
    }
    
    // Mountains/Hiking
    if (strpos($filename, 'mt') !== false || strpos($nameLower, 'mount') !== false || strpos($descLower, 'mountain') !== false || strpos($descLower, 'hiking') !== false) {
        return 'mountains';
    }
    
    // Religious
    if (strpos($filename, 'lourdes') !== false || strpos($filename, 'padre') !== false || strpos($nameLower, 'church') !== false || strpos($descLower, 'religious') !== false || strpos($descLower, 'shrine') !== false) {
        return 'religious sites';
    }
    
    // Parks
    if (strpos($filename, 'park') !== false || strpos($nameLower, 'park') !== false || strpos($descLower, 'park') !== false) {
        return 'parks & recreation';
    }
    
    return 'tourist spot';
}

function determineActivities($filePath, $name, $description) {
    $activities = [];
    $descLower = strtolower($description);
    
    if (strpos($descLower, 'hiking') !== false || strpos($descLower, 'trekking') !== false) {
        $activities[] = 'Hiking';
    }
    if (strpos($descLower, 'swimming') !== false || strpos($descLower, 'bathing') !== false) {
        $activities[] = 'Swimming';
    }
    if (strpos($descLower, 'photography') !== false || strpos($descLower, 'photo') !== false) {
        $activities[] = 'Photography';
    }
    if (strpos($descLower, 'picnic') !== false) {
        $activities[] = 'Picnic';
    }
    if (strpos($descLower, 'tour') !== false) {
        $activities[] = 'Guided Tour';
    }
    if (strpos($descLower, 'fishing') !== false) {
        $activities[] = 'Fishing';
    }
    if (strpos($descLower, 'camping') !== false) {
        $activities[] = 'Camping';
    }
    
    return implode(', ', $activities);
}

function determineAmenities($filePath, $name, $description) {
    $amenities = [];
    $descLower = strtolower($description);
    
    if (strpos($descLower, 'parking') !== false) {
        $amenities[] = 'Parking';
    }
    if (strpos($descLower, 'restroom') !== false || strpos($descLower, 'cr') !== false) {
        $amenities[] = 'Restrooms';
    }
    if (strpos($descLower, 'restaurant') !== false || strpos($descLower, 'food') !== false) {
        $amenities[] = 'Food Service';
    }
    if (strpos($descLower, 'shelter') !== false || strpos($descLower, 'shed') !== false) {
        $amenities[] = 'Shed Areas';
    }
    
    return implode(', ', $amenities);
}

// Main import process
function importDestinations() {
    $touristDetailDir = __DIR__ . '/../tourist-detail';
    $conn = getDatabaseConnection();
    
    if (!$conn) {
        echo "Database connection failed!\n";
        return;
    }
    
    // Get all PHP files in tourist-detail directory
    $files = glob($touristDetailDir . '/*.php');
    
    $importedCount = 0;
    
    foreach ($files as $file) {
        echo "Processing: " . basename($file) . "\n";
        
        try {
            $data = extractDestinationData($file);
            
            // Insert new destination
            $stmt = $conn->prepare("INSERT INTO tourist_spots (name, description, category, location, address, operating_hours, entrance_fee, difficulty_level, duration, best_time_to_visit, activities, amenities, contact_info, website, image_url, rating, review_count, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'active')");
            
            $stmt->bind_param(
                "ssssssssssssssssi",
                $data['name'],
                $data['description'],
                $data['category'],
                $data['location'],
                $data['address'],
                $data['operating_hours'],
                $data['entrance_fee'],
                $data['difficulty_level'],
                $data['duration'],
                $data['best_time_to_visit'],
                $data['activities'],
                $data['amenities'],
                $data['contact_info'],
                $data['website'],
                $data['image_url'],
                $data['rating'],
                $data['review_count']
            );
            
            if ($stmt->execute()) {
                echo "  - Imported: " . $data['name'] . "\n";
                $importedCount++;
            } else {
                echo "  - Failed to import: " . $data['name'] . " - " . $stmt->error . "\n";
            }
            
        } catch (Exception $e) {
            echo "  - Error processing " . basename($file) . ": " . $e->getMessage() . "\n";
        }
    }
    
    closeDatabaseConnection($conn);
    
    echo "\nImport Summary:\n";
    echo "Imported: $importedCount destinations\n";
}

// Run the process
if (php_sapi_name() === 'cli') {
    echo "Starting destination import process...\n\n";
    
    if (fixTouristSpotsTable()) {
        echo "\n";
        importDestinations();
    }
} else {
    echo "<pre>";
    if (fixTouristSpotsTable()) {
        echo "\n";
        importDestinations();
    }
    echo "</pre>";
}
?>
