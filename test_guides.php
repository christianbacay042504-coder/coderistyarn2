<?php
// Test script to check if verified tour guides are fetched correctly
require_once 'config/database.php';

try {
    $conn = getDatabaseConnection();
    if ($conn) {
        // Fetch available tour guides (only verified and active)
        $tour_guides = [];
        $guidesStmt = $conn->prepare("SELECT id, name, specialty, category FROM tour_guides WHERE verified = 1 AND status = 'active' ORDER BY name ASC");
        if ($guidesStmt) {
            $guidesStmt->execute();
            $guidesResult = $guidesStmt->get_result();

            echo "Verified and active tour guides found: " . $guidesResult->num_rows . "\n";

            while ($guide = $guidesResult->fetch_assoc()) {
                $tour_guides[] = [
                    'id' => $guide['id'],
                    'name' => $guide['name'],
                    'specialty' => $guide['specialty'],
                    'category' => $guide['category']
                ];
                echo "Guide: " . $guide['name'] . " (ID: " . $guide['id'] . ")\n";
            }
            $guidesStmt->close();
        } else {
            echo "Failed to prepare query\n";
        }

        closeDatabaseConnection($conn);
    } else {
        echo "Database connection failed\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
