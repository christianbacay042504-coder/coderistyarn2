<?php
/**
 * Tour Guide Registration Function
 * Handles tour guide registration form submissions
 * Created: February 16, 2026
 */

require_once __DIR__ . '/../config/database.php';

/**
 * Save tour guide registration data
 * @param array $formData - Form data from $_POST
 * @param array $fileData - File data from $_FILES
 * @return array - Result with success status and message
 */
function saveTourGuideRegistration($formData, $fileData) {
    $conn = getDatabaseConnection();
    if (!$conn) {
        return ['success' => false, 'message' => 'Database connection failed'];
    }
    
    try {
        // Start transaction
        $conn->begin_transaction();
        
        // Handle file uploads
        $uploadedFiles = handleFileUploads($fileData);
        if (isset($uploadedFiles['error'])) {
            throw new Exception($uploadedFiles['error']);
        }
        
        // Insert main registration data
        $sql = "INSERT INTO registration_tour_guide (
            last_name, first_name, middle_initial, preferred_name, date_of_birth, gender,
            home_address, primary_phone, secondary_phone, email,
            emergency_contact_name, emergency_contact_relationship, emergency_contact_phone,
            dot_accreditation, accreditation_expiry, specialization, years_experience,
            first_aid_certified, first_aid_expiry, base_location, employment_type, has_vehicle,
            resume_file, dot_id_file, government_id_file, nbi_clearance_file,
            first_aid_certificate_file, id_photo_file
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("Prepare statement failed: " . $conn->error);
        }
        
        // Bind parameters
        $stmt->bind_param(
            "ssssssssssssssssssssssssssss",
            $formData['lastName'],
            $formData['firstName'],
            $formData['middleInitial'],
            $formData['preferredName'],
            $formData['dateOfBirth'],
            $formData['gender'],
            $formData['homeAddress'],
            $formData['primaryPhone'],
            $formData['secondaryPhone'],
            $formData['email'],
            $formData['emergencyContactName'],
            $formData['emergencyContactRelationship'],
            $formData['emergencyContactPhone'],
            $formData['dotAccreditation'],
            $formData['accreditationExpiry'],
            $formData['specialization'],
            $formData['yearsExperience'],
            $formData['firstAidCertified'],
            $formData['firstAidExpiry'],
            $formData['baseLocation'],
            $formData['employmentType'],
            $formData['hasVehicle'],
            $uploadedFiles['resume'],
            $uploadedFiles['dotId'],
            $uploadedFiles['governmentId'],
            $uploadedFiles['nbiClearance'],
            $uploadedFiles['firstAidCertificate'],
            $uploadedFiles['idPhoto']
        );
        
        if (!$stmt->execute()) {
            throw new Exception("Execute failed: " . $stmt->error);
        }
        
        // Get the registration ID
        $registrationId = $conn->insert_id;
        
        // Save languages
        if (isset($formData['languages']) && is_array($formData['languages'])) {
            saveLanguages($conn, $registrationId, $formData['languages'], $formData['languageProficiency']);
        }
        
        // Commit transaction
        $conn->commit();
        
        return [
            'success' => true, 
            'message' => 'Registration submitted successfully! Your application will be reviewed by the admin team.',
            'registration_id' => $registrationId
        ];
        
    } catch (Exception $e) {
        // Rollback transaction
        $conn->rollback();
        error_log("Tour guide registration error: " . $e->getMessage());
        return ['success' => false, 'message' => 'Registration failed: ' . $e->getMessage()];
    } finally {
        closeDatabaseConnection($conn);
    }
}

/**
 * Handle file uploads
 * @param array $files - $_FILES data
 * @return array - Uploaded file paths or error
 */
function handleFileUploads($files) {
    $uploadDir = __DIR__ . '/../uploads/tour_guide_documents/';
    $uploadedFiles = [];
    
    // Create upload directory if it doesn't exist
    if (!file_exists($uploadDir)) {
        if (!mkdir($uploadDir, 0755, true)) {
            return ['error' => 'Failed to create upload directory'];
        }
    }
    
    $allowedTypes = [
        'resume' => ['pdf', 'doc', 'docx'],
        'dotId' => ['pdf', 'jpg', 'jpeg', 'png'],
        'governmentId' => ['pdf', 'jpg', 'jpeg', 'png'],
        'nbiClearance' => ['pdf', 'jpg', 'jpeg', 'png'],
        'firstAidCertificate' => ['pdf', 'jpg', 'jpeg', 'png'],
        'idPhoto' => ['jpg', 'jpeg', 'png']
    ];
    
    $maxSizes = [
        'resume' => 5 * 1024 * 1024, // 5MB
        'dotId' => 5 * 1024 * 1024,  // 5MB
        'governmentId' => 5 * 1024 * 1024, // 5MB
        'nbiClearance' => 5 * 1024 * 1024, // 5MB
        'firstAidCertificate' => 5 * 1024 * 1024, // 5MB
        'idPhoto' => 2 * 1024 * 1024 // 2MB
    ];
    
    foreach ($allowedTypes as $field => $types) {
        if (isset($files[$field]) && $files[$field]['error'] === UPLOAD_ERR_OK) {
            $file = $files[$field];
            
            // Check file size
            if ($file['size'] > $maxSizes[$field]) {
                return ['error' => ucfirst($field) . ' file size exceeds limit'];
            }
            
            // Check file type
            $fileExt = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            if (!in_array($fileExt, $types)) {
                return ['error' => 'Invalid file type for ' . $field];
            }
            
            // Generate unique filename
            $fileName = uniqid() . '_' . $field . '.' . $fileExt;
            $filePath = $uploadDir . $fileName;
            
            // Move uploaded file
            if (!move_uploaded_file($file['tmp_name'], $filePath)) {
                return ['error' => 'Failed to upload ' . $field];
            }
            
            $uploadedFiles[$field] = 'tour_guide_documents/' . $fileName;
        } else {
            $uploadedFiles[$field] = null;
        }
    }
    
    return $uploadedFiles;
}

/**
 * Save languages for tour guide
 * @param mysqli $conn - Database connection
 * @param int $registrationId - Registration ID
 * @param array $languages - Languages array
 * @param array $proficiencies - Proficiencies array
 */
function saveLanguages($conn, $registrationId, $languages, $proficiencies) {
    $sql = "INSERT INTO tour_guide_languages (registration_id, language, proficiency) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    
    if (!$stmt) {
        throw new Exception("Prepare language statement failed: " . $conn->error);
    }
    
    foreach ($languages as $index => $language) {
        if (!empty($language) && isset($proficiencies[$index])) {
            $proficiency = $proficiencies[$index];
            
            $stmt->bind_param("iss", $registrationId, $language, $proficiency);
            if (!$stmt->execute()) {
                throw new Exception("Execute language failed: " . $stmt->error);
            }
        }
    }
}

/**
 * Get all tour guide registrations
 * @param string $status - Filter by status (optional)
 * @return array - Registrations data
 */
function getTourGuideRegistrations($status = null) {
    $conn = getDatabaseConnection();
    if (!$conn) {
        return [];
    }
    
    try {
        $sql = "SELECT * FROM registration_tour_guide";
        $params = [];
        $types = "";
        
        if ($status) {
            $sql .= " WHERE status = ?";
            $params[] = $status;
            $types = "s";
        }
        
        $sql .= " ORDER BY application_date DESC";
        
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $conn->error);
        }
        
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        
        if (!$stmt->execute()) {
            throw new Exception("Execute failed: " . $stmt->error);
        }
        
        $result = $stmt->get_result();
        $registrations = [];
        
        while ($row = $result->fetch_assoc()) {
            $registrations[] = $row;
        }
        
        return $registrations;
        
    } catch (Exception $e) {
        error_log("Get registrations error: " . $e->getMessage());
        return [];
    } finally {
        closeDatabaseConnection($conn);
    }
}

/**
 * Get tour guide registration by ID
 * @param int $id - Registration ID
 * @return array|null - Registration data or null
 */
function getTourGuideRegistrationById($id) {
    $conn = getDatabaseConnection();
    if (!$conn) {
        return null;
    }
    
    try {
        $sql = "SELECT * FROM registration_tour_guide WHERE id = ?";
        $stmt = $conn->prepare($sql);
        
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $conn->error);
        }
        
        $stmt->bind_param("i", $id);
        
        if (!$stmt->execute()) {
            throw new Exception("Execute failed: " . $stmt->error);
        }
        
        $result = $stmt->get_result();
        $registration = $result->fetch_assoc();
        
        // Get languages
        if ($registration) {
            $sql = "SELECT language, proficiency FROM tour_guide_languages WHERE registration_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $langResult = $stmt->get_result();
            
            $languages = [];
            while ($row = $langResult->fetch_assoc()) {
                $languages[] = $row;
            }
            $registration['languages'] = $languages;
        }
        
        return $registration;
        
    } catch (Exception $e) {
        error_log("Get registration error: " . $e->getMessage());
        return null;
    } finally {
        closeDatabaseConnection($conn);
    }
}

/**
 * Update registration status
 * @param int $id - Registration ID
 * @param string $status - New status
 * @param string $notes - Admin notes (optional)
 * @param int $reviewedBy - Admin ID who reviewed (optional)
 * @return array - Result
 */
function updateRegistrationStatus($id, $status, $notes = null, $reviewedBy = null) {
    $conn = getDatabaseConnection();
    if (!$conn) {
        return ['success' => false, 'message' => 'Database connection failed'];
    }
    
    try {
        $sql = "UPDATE registration_tour_guide SET status = ?, review_date = NOW(), admin_notes = ?, reviewed_by = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $conn->error);
        }
        
        $stmt->bind_param("ssii", $status, $notes, $reviewedBy, $id);
        
        if (!$stmt->execute()) {
            throw new Exception("Execute failed: " . $stmt->error);
        }
        
        return ['success' => true, 'message' => 'Status updated successfully'];
        
    } catch (Exception $e) {
        error_log("Update status error: " . $e->getMessage());
        return ['success' => false, 'message' => 'Update failed: ' . $e->getMessage()];
    } finally {
        closeDatabaseConnection($conn);
    }
}
?>
