<?php
// Update Preferences Handler
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../config/database.php';

header('Content-Type: application/json');

// Check if user is logged in
if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit();
}

// Handle POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit();
}

// Get JSON data
$jsonData = file_get_contents('php://input');
$data = json_decode($jsonData, true);

if (!$data || !isset($data['categories'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid data format']);
    exit();
}

$selectedCategories = $data['categories'];

// Validate categories
if (empty($selectedCategories)) {
    echo json_encode(['success' => false, 'message' => 'Please select at least one category']);
    exit();
}

if (count($selectedCategories) > 8) {
    echo json_encode(['success' => false, 'message' => 'You can select up to 8 categories only']);
    exit();
}

// Valid categories list
$validCategories = [
    'nature', 'adventure', 'farm', 'park', 'religious', 'family', 'photography'
];

// Validate each category
foreach ($selectedCategories as $category) {
    if (!in_array($category, $validCategories)) {
        echo json_encode(['success' => false, 'message' => 'Invalid category selected']);
        exit();
    }
}

try {
    $conn = getDatabaseConnection();
    if (!$conn) {
        echo json_encode(['success' => false, 'message' => 'Database connection failed']);
        exit();
    }
    
    // Start transaction
    $conn->begin_transaction();
    
    // Clear existing preferences for this user
    $deleteStmt = $conn->prepare("DELETE FROM user_preferences WHERE user_id = ?");
    $deleteStmt->bind_param("i", $_SESSION['user_id']);
    $deleteStmt->execute();
    $deleteStmt->close();
    
    // Insert new preferences
    $insertStmt = $conn->prepare("INSERT INTO user_preferences (user_id, category) VALUES (?, ?)");
    foreach ($selectedCategories as $category) {
        $insertStmt->bind_param("is", $_SESSION['user_id'], $category);
        $insertStmt->execute();
    }
    $insertStmt->close();
    
    // Mark preferences as set for the user (if not already set)
    $updateStmt = $conn->prepare("UPDATE users SET preferences_set = 1 WHERE id = ? AND preferences_set != 1");
    $updateStmt->bind_param("i", $_SESSION['user_id']);
    $updateStmt->execute();
    $updateStmt->close();
    
    // Commit transaction
    $conn->commit();
    closeDatabaseConnection($conn);
    
    echo json_encode(['success' => true, 'message' => 'Preferences updated successfully']);
    
} catch (Exception $e) {
    if (isset($conn)) {
        $conn->rollback();
        closeDatabaseConnection($conn);
    }
    echo json_encode(['success' => false, 'message' => 'Error updating preferences: ' . $e->getMessage()]);
}
?>
