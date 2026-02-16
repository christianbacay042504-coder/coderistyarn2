<?php
/**
 * Test Tour Guide Registration Function
 * Created: February 16, 2026
 */

require_once __DIR__ . '/functions/tour_guide_registration.php';

echo "Testing Tour Guide Registration Function...\n\n";

// Test data
$testData = [
    'lastName' => 'Test',
    'firstName' => 'Guide',
    'middleInitial' => 'A',
    'preferredName' => 'Tour Guide Test',
    'dateOfBirth' => '1990-01-01',
    'gender' => 'male',
    'homeAddress' => '123 Test Street, Test City, Test Province',
    'primaryPhone' => '09123456789',
    'secondaryPhone' => '09987654321',
    'email' => 'testguide' . time() . '@example.com',
    'emergencyContactName' => 'Emergency Contact',
    'emergencyContactRelationship' => 'Spouse',
    'emergencyContactPhone' => '09112223333',
    'dotAccreditation' => 'DOT-' . time(),
    'accreditationExpiry' => '2025-12-31',
    'specialization' => 'mountain',
    'yearsExperience' => '5',
    'firstAidCertified' => 'yes',
    'firstAidExpiry' => '2024-12-31',
    'baseLocation' => 'San Jose del Monte',
    'employmentType' => 'full-time',
    'hasVehicle' => 'yes',
    'languages' => ['english', 'filipino'],
    'languageProficiency' => ['fluent', 'native']
];

// Mock file data (simulating file uploads)
$testFiles = [
    'resume' => [
        'name' => 'test_resume.pdf',
        'type' => 'application/pdf',
        'tmp_name' => 'C:\xampp\tmp\php' . rand(1000, 9999) . '.tmp',
        'error' => UPLOAD_ERR_NO_FILE, // No actual file for testing
        'size' => 0
    ],
    'dotId' => [
        'name' => 'test_dot.jpg',
        'type' => 'image/jpeg',
        'tmp_name' => 'C:\xampp\tmp\php' . rand(1000, 9999) . '.tmp',
        'error' => UPLOAD_ERR_NO_FILE,
        'size' => 0
    ],
    'governmentId' => [
        'name' => 'test_gov_id.jpg',
        'type' => 'image/jpeg',
        'tmp_name' => 'C:\xampp\tmp\php' . rand(1000, 9999) . '.tmp',
        'error' => UPLOAD_ERR_NO_FILE,
        'size' => 0
    ],
    'nbiClearance' => [
        'name' => 'test_nbi.jpg',
        'type' => 'image/jpeg',
        'tmp_name' => 'C:\xampp\tmp\php' . rand(1000, 9999) . '.tmp',
        'error' => UPLOAD_ERR_NO_FILE,
        'size' => 0
    ],
    'firstAidCertificate' => [
        'name' => 'test_first_aid.pdf',
        'type' => 'application/pdf',
        'tmp_name' => 'C:\xampp\tmp\php' . rand(1000, 9999) . '.tmp',
        'error' => UPLOAD_ERR_NO_FILE,
        'size' => 0
    ],
    'idPhoto' => [
        'name' => 'test_id_photo.jpg',
        'type' => 'image/jpeg',
        'tmp_name' => 'C:\xampp\tmp\php' . rand(1000, 9999) . '.tmp',
        'error' => UPLOAD_ERR_NO_FILE,
        'size' => 0
    ]
];

echo "Test Data:\n";
echo "Name: " . $testData['firstName'] . " " . $testData['lastName'] . "\n";
echo "Email: " . $testData['email'] . "\n";
echo "Specialization: " . $testData['specialization'] . "\n";
echo "Languages: " . implode(', ', $testData['languages']) . "\n\n";

// Test the function
echo "Testing saveTourGuideRegistration function...\n";
$result = saveTourGuideRegistration($testData, $testFiles);

if ($result['success']) {
    echo "✅ SUCCESS: " . $result['message'] . "\n";
    echo "Registration ID: " . $result['registration_id'] . "\n\n";
    
    // Test retrieving the registration
    echo "Testing getTourGuideRegistrationById function...\n";
    $registration = getTourGuideRegistrationById($result['registration_id']);
    
    if ($registration) {
        echo "✅ Registration retrieved successfully:\n";
        echo "- Name: " . $registration['first_name'] . " " . $registration['last_name'] . "\n";
        echo "- Email: " . $registration['email'] . "\n";
        echo "- Status: " . $registration['status'] . "\n";
        echo "- Application Date: " . $registration['application_date'] . "\n";
        echo "- Languages: " . count($registration['languages']) . " languages stored\n";
    } else {
        echo "❌ Failed to retrieve registration\n";
    }
    
    // Test updating status
    echo "\nTesting updateRegistrationStatus function...\n";
    $updateResult = updateRegistrationStatus($result['registration_id'], 'under_review', 'Test review notes', 1);
    
    if ($updateResult['success']) {
        echo "✅ Status updated successfully\n";
    } else {
        echo "❌ Status update failed: " . $updateResult['message'] . "\n";
    }
    
} else {
    echo "❌ FAILED: " . $result['message'] . "\n";
}

// Test getting all registrations
echo "\nTesting getTourGuideRegistrations function...\n";
$registrations = getTourGuideRegistrations();
echo "Total registrations found: " . count($registrations) . "\n";

echo "\nTest completed!\n";
?>
