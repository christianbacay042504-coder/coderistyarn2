<?php
require_once '../config/database.php';

echo "<h2>Tour Guide Debug</h2>";

try {
    $conn = getDatabaseConnection();
    if ($conn) {
        echo "✅ Database connection successful<br>";
        
        // Fetch available tour guides (only verified and active)
        $tour_guides = [];
        $guidesStmt = $conn->prepare("SELECT id, name, specialty, category, verified, experience_years, languages, group_size, description FROM tour_guides WHERE verified = 1 AND status = 'active' ORDER BY name ASC");
        
        if ($guidesStmt) {
            echo "✅ Query prepared successfully<br>";
            $guidesStmt->execute();
            $guidesResult = $guidesStmt->get_result();
            
            echo "📊 Query executed, rows found: " . $guidesResult->num_rows . "<br>";
            
            if ($guidesResult->num_rows > 0) {
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
                    echo "✅ Found guide: " . htmlspecialchars($guide['name']) . " (ID: " . $guide['id'] . ")<br>";
                }
                echo "📋 Total tour guides array: " . count($tour_guides) . "<br>";
                echo "📋 Array contents: <pre>" . print_r($tour_guides, true) . "</pre>";
            } else {
                echo "❌ No guides found in query result<br>";
            }
            
            $guidesStmt->close();
        } else {
            echo "❌ Failed to prepare query: " . $conn->error . "<br>";
        }
        
        $conn->close();
    } else {
        echo "❌ Database connection failed<br>";
    }
} catch (Exception $e) {
    echo "❌ Exception: " . $e->getMessage() . "<br>";
}

// Test the actual condition used in book.php
echo "<h3>Testing Empty Condition</h3>";
echo "Empty check: " . (empty($tour_guides) ? 'EMPTY' : 'NOT EMPTY') . "<br>";
echo "Count check: " . count($tour_guides) . "<br>";

?>
