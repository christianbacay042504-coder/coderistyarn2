<?php
require_once __DIR__ . '/config/database.php';

// Handle registration form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    
    try {
        // Get database connection
        $conn = getDatabaseConnection();
        if (!$conn) {
            throw new Exception("Database connection failed");
        }
        
        // 1. Sanitize and prepare Personal Information
        $lastName = trim($_POST['lastName'] ?? '');
        $firstName = trim($_POST['firstName'] ?? '');
        $middleInitial = trim($_POST['middleInitial'] ?? '');
        $preferredName = trim($_POST['preferredName'] ?? '');
        $dateOfBirth = $_POST['dateOfBirth'] ?? null;
        $gender = $_POST['gender'] ?? null;
        $homeAddress = trim($_POST['homeAddress'] ?? '');
        $primaryPhone = trim($_POST['primaryPhone'] ?? '');
        $secondaryPhone = trim($_POST['secondaryPhone'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $emergencyContactName = trim($_POST['emergencyContactName'] ?? '');
        $emergencyContactRelationship = trim($_POST['emergencyContactRelationship'] ?? '');
        $emergencyContactPhone = trim($_POST['emergencyContactPhone'] ?? '');
        
        // 2. Professional Qualifications
        $dotAccreditation = trim($_POST['dotAccreditation'] ?? '');
        $accreditationExpiry = $_POST['accreditationExpiry'] ?? null;
        $yearsExperience = intval($_POST['yearsExperience'] ?? 0);
        $firstAidCertified = $_POST['firstAidCertified'] ?? null;
        $firstAidExpiry = $_POST['firstAidExpiry'] ?? null;
        $baseLocation = trim($_POST['baseLocation'] ?? '');
        $employmentType = $_POST['employmentType'] ?? null;
        $hasVehicle = $_POST['hasVehicle'] ?? null;
        
        // 3. Handle Languages (JSON format)
        $languages = [];
        if (isset($_POST['languages']) && isset($_POST['languageProficiency'])) {
            $languageData = $_POST['languages'];
            $proficiencyData = $_POST['languageProficiency'];
            for ($i = 0; $i < count($languageData); $i++) {
                if (!empty($languageData[$i]) && !empty($proficiencyData[$i])) {
                    $languages[] = [
                        'language' => $languageData[$i],
                        'proficiency' => $proficiencyData[$i]
                    ];
                }
            }
        }
        $languagesJson = json_encode($languages);
        
        // 4. Handle Specialization (FIX: Ensures it's a JSON string, not a zero)
        $specializations = [];
        if (isset($_POST['specialization']) && is_array($_POST['specialization'])) {
            $specializations = $_POST['specialization'];
        }
        $specializationsJson = json_encode(array_values($specializations)); 
        
        // 5. Handle File Uploads
        $uploadDir = __DIR__ . '/uploads/tour_guide_documents/';
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        $documentPaths = [
            'resume_path' => handleFileUpload('resume', $uploadDir, ['pdf', 'doc', 'docx'], 5 * 1024 * 1024),
            'dot_id_path' => handleFileUpload('dotId', $uploadDir, ['pdf', 'jpg', 'jpeg', 'png'], 5 * 1024 * 1024),
            'government_id_path' => handleFileUpload('governmentId', $uploadDir, ['pdf', 'jpg', 'jpeg', 'png'], 5 * 1024 * 1024),
            'nbi_clearance_path' => handleFileUpload('nbiClearance', $uploadDir, ['pdf', 'jpg', 'jpeg', 'png'], 5 * 1024 * 1024),
            'first_aid_certificate_path' => handleFileUpload('firstAidCertificate', $uploadDir, ['pdf', 'jpg', 'jpeg', 'png'], 5 * 1024 * 1024),
            'id_photo_path' => handleFileUpload('idPhoto', $uploadDir, ['jpg', 'jpeg', 'png'], 2 * 1024 * 1024)
        ];
        
        // 6. Prepare SQL statement (30 columns in table, 1 hardcoded 'Pending', 29 placeholders)
        $sql = "INSERT INTO registration_tour_guides (
            status, last_name, first_name, middle_initial, preferred_name, 
            date_of_birth, gender, home_address, primary_phone, secondary_phone, 
            email, emergency_contact_name, emergency_contact_relationship, emergency_contact_phone,
            dot_accreditation_number, accreditation_expiry_date, languages_spoken, specialization,
            years_of_experience, first_aid_certified, first_aid_expiry_date,
            base_location, employment_type, has_vehicle,
            resume_path, dot_id_path, government_id_path, nbi_clearance_path,
            first_aid_certificate_path, id_photo_path
        ) VALUES (
            'Pending', ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
        )";
        
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("Database prepare error: " . $conn->error);
        }
        
        // 7. Bind Parameters
        // Sequence: 16 strings, 1 string (specialization), 1 integer (years), then the rest are strings.
        // Format: "ssssssssssssssssissssssssssss" (Total 29 characters)
        $stmt->bind_param(
            "ssssssssssssssssissssssssssss", 
            $lastName, $firstName, $middleInitial, $preferredName,
            $dateOfBirth, $gender, $homeAddress, $primaryPhone, $secondaryPhone,
            $email, $emergencyContactName, $emergencyContactRelationship, $emergencyContactPhone,
            $dotAccreditation, $accreditationExpiry, $languagesJson, $specializationsJson,
            $yearsExperience, $firstAidCertified, $firstAidExpiry,
            $baseLocation, $employmentType, $hasVehicle,
            $documentPaths['resume_path'], $documentPaths['dot_id_path'], 
            $documentPaths['government_id_path'], $documentPaths['nbi_clearance_path'],
            $documentPaths['first_aid_certificate_path'], $documentPaths['id_photo_path']
        );
        
        // 8. Execute
        if ($stmt->execute()) {
            echo json_encode([
                'success' => true, 
                'message' => 'Application submitted successfully!',
                'application_id' => $conn->insert_id
            ]);
        } else {
            throw new Exception("Execution failed: " . $stmt->error);
        }
        
        $stmt->close();
        $conn->close();
        
    } catch (Exception $e) {
        error_log("Tour Guide Registration Error: " . $e->getMessage());
        echo json_encode([
            'success' => false, 
            'message' => 'Error: ' . $e->getMessage()
        ]);
    }
    exit();
}

/**
 * Helper function for file uploads
 */
function handleFileUpload($fieldName, $uploadDir, $allowedExtensions, $maxSize) {
    if (!isset($_FILES[$fieldName]) || $_FILES[$fieldName]['error'] !== UPLOAD_ERR_OK) {
        return null;
    }
    
    $file = $_FILES[$fieldName];
    if ($file['size'] > $maxSize) {
        throw new Exception("File $fieldName is too large.");
    }
    
    $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($extension, $allowedExtensions)) {
        throw new Exception("Invalid file type for $fieldName.");
    }
    
    $filename = uniqid() . '_' . $fieldName . '_' . time() . '.' . $extension;
    $filePath = $uploadDir . $filename;
    
    if (!move_uploaded_file($file['tmp_name'], $filePath)) {
        throw new Exception("Failed to move uploaded file: $fieldName");
    }
    
    return 'uploads/tour_guide_documents/' . $filename;
}
?>