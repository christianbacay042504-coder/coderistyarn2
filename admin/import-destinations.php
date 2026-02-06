<?php
// Destination Import Script
// Extracts destination data from tourist-detail files and imports into admin database

require_once __DIR__ . '/../config/database.php';

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
        return 'Agri-Tourism';
    }
    
    // Falls/Waterfalls
    if (strpos($filename, 'falls') !== false || strpos($nameLower, 'falls') !== false || strpos($descLower, 'falls') !== false || strpos($descLower, 'waterfall') !== false) {
        return 'Waterfalls';
    }
    
    // Mountains/Hiking
    if (strpos($filename, 'mt') !== false || strpos($nameLower, 'mount') !== false || strpos($descLower, 'mountain') !== false || strpos($descLower, 'hiking') !== false) {
        return 'Mountains & Hiking';
    }
    
    // Religious
    if (strpos($filename, 'lourdes') !== false || strpos($filename, 'padre') !== false || strpos($nameLower, 'church') !== false || strpos($descLower, 'religious') !== false || strpos($descLower, 'shrine') !== false) {
        return 'Religious Sites';
    }
    
    // Parks
    if (strpos($filename, 'park') !== false || strpos($nameLower, 'park') !== false || strpos($descLower, 'park') !== false) {
        return 'Parks & Recreation';
    }
    
    return 'Tourist Spot';
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
    $touristDetailDir = __DIR__ . '/tourist-detail';
    $conn = getDatabaseConnection();
    
    if (!$conn) {
        echo "Database connection failed!\n";
        return;
    }
    
    // Get all PHP files in tourist-detail directory
    $files = glob($touristDetailDir . '/*.php');
    
    $importedCount = 0;
    $skippedCount = 0;
    
    foreach ($files as $file) {
        echo "Processing: " . basename($file) . "\n";
        
        try {
            $data = extractDestinationData($file);
            
            // Check if destination already exists
            $stmt = $conn->prepare("SELECT id FROM tourist_spots WHERE name = ?");
            $stmt->bind_param("s", $data['name']);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                echo "  - Skipped (already exists): " . $data['name'] . "\n";
                $skippedCount++;
                continue;
            }
            
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
                echo "  - Failed to import: " . $data['name'] . "\n";
            }
            
        } catch (Exception $e) {
            echo "  - Error processing " . basename($file) . ": " . $e->getMessage() . "\n";
        }
    }
    
    closeDatabaseConnection($conn);
    
    echo "\nImport Summary:\n";
    echo "Imported: $importedCount destinations\n";
    echo "Skipped: $skippedCount destinations (already exist)\n";
    echo "Total processed: " . ($importedCount + $skippedCount) . "\n";
}

// Run the import
if (php_sapi_name() === 'cli') {
    echo "Starting destination import...\n\n";
    importDestinations();
} else {
    echo "<pre>";
    importDestinations();
    echo "</pre>";
}
?>
