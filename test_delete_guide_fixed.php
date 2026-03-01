<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/auth.php';

$conn = getDatabaseConnection();

echo "Testing enhanced deleteTourGuide functionality...\n";

// Test the enhanced deleteTourGuide function
$testGuideId = 1; // Use a test guide ID
$testJustification = "Test deletion with automatic user account deletion";

echo "Deleting tour guide ID: $testGuideId\n";
echo "Justification: $testJustification\n\n";

$result = deleteTourGuide($conn, $testGuideId, $testJustification);

echo "Result:\n";
echo "- Success: " . ($result['success'] ? 'YES' : 'NO') . "\n";
echo "- Message: " . $result['message'] . "\n";

closeDatabaseConnection($conn);
?>
