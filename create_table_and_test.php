<?php
require_once __DIR__ . '/config/database.php';

echo "Creating registration_tour_guides table...\n";

// Read and execute the SQL file
$sqlFile = __DIR__ . '/database/create_registration_tour_guides_table.sql';
if (file_exists($sqlFile)) {
    $sql = file_get_contents($sqlFile);
    
    try {
        $conn = getDatabaseConnection();
        if (!$conn) {
            die("Database connection failed\n");
        }
        
        // Split SQL into individual statements
        $statements = array_filter(array_map('trim', explode(';', $sql)));
        
        foreach ($statements as $statement) {
            if (!empty($statement)) {
                echo "Executing: " . substr($statement, 0, 50) . "...\n";
                if (!$conn->query($statement)) {
                    echo "Error: " . $conn->error . "\n";
                } else {
                    echo "Success\n";
                }
            }
        }
        
        $conn->close();
        echo "Table creation completed!\n";
        
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage() . "\n";
    }
} else {
    echo "SQL file not found!\n";
}

// Test the save function
echo "\nTesting save function...\n";

// Simulate form data
$_POST = [
    'lastName' => 'Test',
    'firstName' => 'User',
    'middleInitial' => 'A',
    'preferredName' => 'Test Guide',
    'dateOfBirth' => '1990-01-01',
    'gender' => 'male',
    'homeAddress' => 'Test Address',
    'primaryPhone' => '09123456789',
    'secondaryPhone' => '09123456788',
    'email' => 'test@example.com',
    'emergencyContactName' => 'Emergency Contact',
    'emergencyContactRelationship' => 'Sibling',
    'emergencyContactPhone' => '09123456787',
    'dotAccreditation' => 'DOT123456',
    'accreditationExpiry' => '2025-12-31',
    'languages' => ['english', 'filipino'],
    'languageProficiency' => ['fluent', 'native'],
    'specialization' => ['mountain', 'cultural'],
    'yearsExperience' => '5',
    'firstAidCertified' => 'yes',
    'firstAidExpiry' => '2025-06-30',
    'baseLocation' => 'San Jose del Monte',
    'employmentType' => 'full-time',
    'hasVehicle' => 'yes'
];

// Simulate file uploads
$_FILES = [
    'resume' => [
        'name' => 'test_resume.pdf',
        'type' => 'application/pdf',
        'size' => 1000000,
        'tmp_name' => __DIR__ . '/test_file.txt',
        'error' => UPLOAD_ERR_OK
    ]
];

// Create a test file
file_put_contents(__DIR__ . '/test_file.txt', 'test content');

try {
    include_once __DIR__ . '/save_registration_tour_guide.php';
} catch (Exception $e) {
    echo "Error in save function: " . $e->getMessage() . "\n";
}

// Clean up test file
if (file_exists(__DIR__ . '/test_file.txt')) {
    unlink(__DIR__ . '/test_file.txt');
}

echo "Test completed!\n";
?>
