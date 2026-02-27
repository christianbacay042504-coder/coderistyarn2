<?php
require_once 'config/database.php';
$conn = getDatabaseConnection();
if ($conn) {
    $query = "SELECT name, category, description, rating, review_count FROM tourist_spots WHERE status = 'active' ORDER BY category, name";
    $result = $conn->query($query);
    if ($result && $result->num_rows > 0) {
        echo "=== ACTUAL TOURIST SPOTS IN YOUR SYSTEM ===\n\n";
        $categories = [];
        while ($spot = $result->fetch_assoc()) {
            $category = $spot['category'];
            if (!isset($categories[$category])) {
                $categories[$category] = [];
            }
            $categories[$category][] = $spot;
            
            echo sprintf("%-30s | %-12s | Rating: %.1f (%d reviews)\n", 
                substr($spot['name'], 0, 30), 
                $category, 
                $spot['rating'], 
                $spot['review_count']
            );
        }
        
        echo "\n=== CATEGORY ANALYSIS ===\n\n";
        foreach ($categories as $category => $spots) {
            $avgRating = array_sum(array_column($spots, 'rating')) / count($spots);
            $totalReviews = array_sum(array_column($spots, 'review_count'));
            echo sprintf("%-15s: %d spots | Avg Rating: %.1f | Total Reviews: %d\n", 
                ucfirst($category), 
                count($spots), 
                $avgRating, 
                $totalReviews
            );
        }
    }
    closeDatabaseConnection($conn);
}
?>
