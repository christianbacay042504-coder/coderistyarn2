<?php
/**
 * Create Tour Guide Registration Table
 * This script creates the registration_tour_guide table and related tables
 * Created: February 16, 2026
 */

require_once __DIR__ . '/config/database.php';

echo "Creating Tour Guide Registration Tables...\n";

$conn = getDatabaseConnection();
if (!$conn) {
    die("Database connection failed!\n");
}

try {
    // Read SQL file
    $sqlFile = __DIR__ . '/database/create_registration_tour_guide_table.sql';
    if (!file_exists($sqlFile)) {
        die("SQL file not found: $sqlFile\n");
    }
    
    $sql = file_get_contents($sqlFile);
    
    // Split SQL into individual statements
    $statements = array_filter(array_map('trim', explode(';', $sql)));
    
    foreach ($statements as $statement) {
        if (!empty($statement)) {
            echo "Executing: " . substr($statement, 0, 50) . "...\n";
            
            if (!$conn->query($statement)) {
                throw new Exception("SQL Error: " . $conn->error . "\nStatement: " . $statement);
            }
        }
    }
    
    echo "\n✅ Tables created successfully!\n";
    echo "Tables created:\n";
    echo "- registration_tour_guide\n";
    echo "- tour_guide_languages\n";
    
    // Show table structure
    echo "\nTable structure for registration_tour_guide:\n";
    $result = $conn->query("DESCRIBE registration_tour_guide");
    while ($row = $result->fetch_assoc()) {
        echo "- " . $row['Field'] . " (" . $row['Type'] . ")\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
} finally {
    closeDatabaseConnection($conn);
}

echo "\nDone!\n";
?>
