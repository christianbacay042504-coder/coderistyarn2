<?php
/**
 * Tour Guide Registration Handler
 * Processes the registration form submission from register-guide.php
 * Created: February 16, 2026
 */

// Set response header for JSON
header('Content-Type: application/json');

// Include required files
require_once __DIR__ . '/functions/tour_guide_registration.php';

// Handle POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Validate required fields
        $requiredFields = [
            'lastName', 'firstName', 'dateOfBirth', 'gender', 'homeAddress',
            'primaryPhone', 'email', 'emergencyContactName', 'emergencyContactRelationship',
            'emergencyContactPhone', 'dotAccreditation', 'accreditationExpiry',
            'specialization', 'yearsExperience', 'firstAidCertified',
            'baseLocation', 'employmentType', 'hasVehicle'
        ];
        
        foreach ($requiredFields as $field) {
            if (!isset($_POST[$field]) || empty(trim($_POST[$field]))) {
                echo json_encode([
                    'success' => false,
                    'message' => "Missing required field: " . ucfirst(str_replace('_', ' ', $field))
                ]);
                exit;
            }
        }
        
        // Validate email
        if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
            echo json_encode([
                'success' => false,
                'message' => 'Invalid email address format'
            ]);
            exit;
        }
        
        // Validate date fields
        $dateFields = ['dateOfBirth', 'accreditationExpiry'];
        foreach ($dateFields as $field) {
            $date = DateTime::createFromFormat('Y-m-d', $_POST[$field]);
            if (!$date || $date->format('Y-m-d') !== $_POST[$field]) {
                echo json_encode([
                    'success' => false,
                    'message' => "Invalid date format for " . ucfirst(str_replace('_', ' ', $field))
                ]);
                exit;
            }
        }
        
        // Validate years experience
        if (!is_numeric($_POST['yearsExperience']) || $_POST['yearsExperience'] < 0) {
            echo json_encode([
                'success' => false,
                'message' => 'Years of experience must be a positive number'
            ]);
            exit;
        }
        
        // Validate first aid expiry if certified
        if ($_POST['firstAidCertified'] === 'yes') {
            if (empty($_POST['firstAidExpiry'])) {
                echo json_encode([
                    'success' => false,
                    'message' => 'First Aid expiry date is required when certified'
                ]);
                exit;
            }
            
            $firstAidDate = DateTime::createFromFormat('Y-m-d', $_POST['firstAidExpiry']);
            if (!$firstAidDate || $firstAidDate->format('Y-m-d') !== $_POST['firstAidExpiry']) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Invalid First Aid expiry date format'
                ]);
                exit;
            }
        } else {
            $_POST['firstAidExpiry'] = null;
        }
        
        // Set nullable fields to null if empty
        $nullableFields = ['middleInitial', 'preferredName', 'secondaryPhone'];
        foreach ($nullableFields as $field) {
            if (empty(trim($_POST[$field]))) {
                $_POST[$field] = null;
            }
        }
        
        // Validate languages
        if (!isset($_POST['languages']) || !is_array($_POST['languages']) || empty(array_filter($_POST['languages']))) {
            echo json_encode([
                'success' => false,
                'message' => 'At least one language must be selected'
            ]);
            exit;
        }
        
        // Validate language proficiencies
        if (!isset($_POST['languageProficiency']) || !is_array($_POST['languageProficiency'])) {
            echo json_encode([
                'success' => false,
                'message' => 'Language proficiencies are required'
            ]);
            exit;
        }
        
        // Filter out empty language entries
        $filteredLanguages = [];
        $filteredProficiencies = [];
        
        foreach ($_POST['languages'] as $index => $language) {
            if (!empty($language) && isset($_POST['languageProficiency'][$index]) && !empty($_POST['languageProficiency'][$index])) {
                $filteredLanguages[] = $language;
                $filteredProficiencies[] = $_POST['languageProficiency'][$index];
            }
        }
        
        if (empty($filteredLanguages)) {
            echo json_encode([
                'success' => false,
                'message' => 'At least one complete language entry is required'
            ]);
            exit;
        }
        
        $_POST['languages'] = $filteredLanguages;
        $_POST['languageProficiency'] = $filteredProficiencies;
        
        // Validate required files
        $requiredFiles = ['resume', 'dotId', 'governmentId', 'nbiClearance', 'idPhoto'];
        foreach ($requiredFiles as $file) {
            if (!isset($_FILES[$file]) || $_FILES[$file]['error'] !== UPLOAD_ERR_OK) {
                echo json_encode([
                    'success' => false,
                    'message' => ucfirst(str_replace('_', ' ', $file)) . ' file is required'
                ]);
                exit;
            }
        }
        
        // Validate first aid certificate if certified
        if ($_POST['firstAidCertified'] === 'yes') {
            if (!isset($_FILES['firstAidCertificate']) || $_FILES['firstAidCertificate']['error'] !== UPLOAD_ERR_OK) {
                echo json_encode([
                    'success' => false,
                    'message' => 'First Aid certificate file is required when certified'
                ]);
                exit;
            }
        }
        
        // Save registration
        $result = saveTourGuideRegistration($_POST, $_FILES);
        
        echo json_encode($result);
        
    } catch (Exception $e) {
        error_log("Registration handler error: " . $e->getMessage());
        echo json_encode([
            'success' => false,
            'message' => 'An error occurred during registration. Please try again.'
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method'
    ]);
}
?>
