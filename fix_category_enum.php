<?php
require_once 'config/database.php';

$conn = getDatabaseConnection();

echo "Fixing tour_guides.category ENUM to match registration_tour_guide.specialization...\n\n";

// Update the category ENUM to include all specialization values
$sql = "ALTER TABLE tour_guides MODIFY COLUMN category ENUM(
    'mountain','city','farm','waterfall','historical','general',
    'cultural','adventure','photography'
) NOT NULL DEFAULT 'general'";

if ($conn->query($sql)) {
    echo "✅ tour_guides.category ENUM updated successfully\n";
} else {
    echo "❌ Error updating ENUM: " . $conn->error . "\n";
}

// Verify the change
$result = $conn->query("DESCRIBE tour_guides");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        if ($row['Field'] === 'category') {
            echo "New category column type: " . $row['Type'] . "\n";
        }
    }
}

$conn->close();
?>
