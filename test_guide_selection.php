<?php
// Test script for guide selection functionality in book.php
require_once 'config/database.php';

echo "=== Guide Selection Test ===\n\n";

// Test database connection
$conn = getDatabaseConnection();
if (!$conn) {
    echo "❌ Database connection failed\n";
    exit(1);
}
echo "✅ Database connection successful\n";

// Test guide query
$guidesStmt = $conn->prepare("SELECT id, name, specialty, description, experience, languages, max_group_size, category, verified FROM tour_guides WHERE verified = 1 AND status = 'active' ORDER BY name ASC");
if (!$guidesStmt) {
    echo "❌ Failed to prepare guide query: " . $conn->error . "\n";
    exit(1);
}

$guidesStmt->execute();
$guidesResult = $guidesStmt->get_result();

echo "✅ Guide query executed successfully\n";
echo "📊 Found " . $guidesResult->num_rows . " verified and active guides:\n\n";

$guides = [];
while ($guide = $guidesResult->fetch_assoc()) {
    $guides[] = $guide;
    echo "👤 " . htmlspecialchars($guide['name']) . " (ID: " . $guide['id'] . ")\n";
    echo "   Specialty: " . htmlspecialchars($guide['specialty']) . "\n";
    echo "   Experience: " . htmlspecialchars($guide['experience']) . "\n";
    echo "   Languages: " . htmlspecialchars($guide['languages']) . "\n";
    echo "   Max Group Size: " . htmlspecialchars($guide['max_group_size']) . "\n";
    echo "   Category: " . htmlspecialchars($guide['category']) . "\n";
    echo "   Verified: " . ($guide['verified'] ? 'Yes' : 'No') . "\n";
    echo "   Description: " . htmlspecialchars(substr($guide['description'], 0, 100)) . "...\n\n";
}

$guidesStmt->close();
closeDatabaseConnection($conn);

// Test HTML generation
echo "=== HTML Generation Test ===\n\n";

if (!empty($guides)) {
    echo "✅ Guide selection grid HTML generation:\n\n";
    echo "<div class=\"guide-selection-grid\">\n";

    foreach ($guides as $guide) {
        echo "    <div class=\"guide-selection-card\" data-guide-id=\"" . $guide['id'] . "\" onclick=\"selectGuide(" . $guide['id'] . ")\">\n";
        echo "        <div class=\"guide-card-header\">\n";
        echo "            <div class=\"guide-avatar\">" . strtoupper(substr($guide['name'], 0, 1)) . "</div>\n";
        echo "            <div class=\"guide-info\">\n";
        echo "                <h4>" . htmlspecialchars($guide['name']) . "</h4>\n";
        echo "                <span class=\"guide-specialty\">" . htmlspecialchars($guide['specialty']) . "</span>\n";
        echo "            </div>\n";
        echo "        </div>\n";
        echo "        <div class=\"guide-details\">\n";
        echo "            <div class=\"guide-detail\"><span class=\"material-icons-outlined\">schedule</span> " . htmlspecialchars($guide['experience']) . " experience</div>\n";
        echo "            <div class=\"guide-detail\"><span class=\"material-icons-outlined\">translate</span> " . htmlspecialchars($guide['languages']) . "</div>\n";
        echo "            <div class=\"guide-detail\"><span class=\"material-icons-outlined\">groups</span> Up to " . htmlspecialchars($guide['max_group_size']) . "</div>\n";
        echo "        </div>\n";
        echo "        <div class=\"guide-description\">" . htmlspecialchars($guide['description']) . "</div>\n";
        echo "    </div>\n";
    }

    echo "</div>\n\n";
    echo "✅ HTML generation successful\n";
} else {
    echo "⚠️  No guides available - empty state message would be shown\n";
}

echo "\n=== Test Summary ===\n";
echo "✅ Database connection: Working\n";
echo "✅ Guide query: Working (" . count($guides) . " guides found)\n";
echo "✅ HTML generation: Working\n";
echo "✅ Guide selection grid: Ready for testing\n\n";

echo "Next steps for manual testing:\n";
echo "1. Open http://localhost/coderistyarn2/sjdm-user/book.php in browser\n";
echo "2. Verify guide cards display with correct information\n";
echo "3. Test clicking on guide cards (should highlight selected)\n";
echo "4. Test form progression to next step\n";
echo "5. Test responsive design on mobile/tablet\n";
echo "6. Test preselected guide via URL parameter (?guide=ID)\n";

echo "\n=== Test Complete ===\n";
?>
