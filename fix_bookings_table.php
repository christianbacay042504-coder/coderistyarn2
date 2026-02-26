<?php
// Fix bookings table structure
require_once __DIR__ . '/config/database.php';

echo "Fixing bookings table structure...\n";

$conn = getDatabaseConnection();
if (!$conn) {
    die("Database connection failed\n");
}

// SQL to add missing columns
$sqlStatements = [
    "ALTER TABLE bookings ADD COLUMN IF NOT EXISTS guide_id INT NULL",
    "ALTER TABLE bookings ADD COLUMN IF NOT EXISTS destination VARCHAR(200) NOT NULL DEFAULT ''",
    "ALTER TABLE bookings ADD COLUMN IF NOT EXISTS contact_number VARCHAR(50) NOT NULL DEFAULT ''",
    "ALTER TABLE bookings ADD COLUMN IF NOT EXISTS email VARCHAR(100) NOT NULL DEFAULT ''",
    "ALTER TABLE bookings ADD COLUMN IF NOT EXISTS special_requests TEXT NULL",
    "ALTER TABLE bookings ADD COLUMN IF NOT EXISTS booking_reference VARCHAR(50) NOT NULL DEFAULT ''"
];

foreach ($sqlStatements as $sql) {
    echo "Executing: $sql\n";
    if ($conn->query($sql)) {
        echo "✅ Success\n";
    } else {
        echo "❌ Error: " . $conn->error . "\n";
    }
}

closeDatabaseConnection($conn);
echo "Database fix completed!\n";
?>
