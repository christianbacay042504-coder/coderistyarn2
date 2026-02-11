<?php
// Update admin credentials in database
require_once __DIR__ . '/config/database.php';

try {
    $conn = getDatabaseConnection();
    
    if (!$conn) {
        die("Database connection failed");
    }
    
    // Update admin email and password
    $stmt = $conn->prepare("UPDATE users SET email = ?, password = ? WHERE user_type = 'admin'");
    $newEmail = 'jeanmarcaguilar829@gmail.com';
    $newPassword = '$2y$10$VzQT8RqQw8Pb95baXQ6aLeMh.nQImQT2vMmc/lwKa/Zj4zGhtk1Ru'; // admin2024
    
    $stmt->bind_param("ss", $newEmail, $newPassword);
    
    if ($stmt->execute()) {
        echo "✅ Admin credentials updated successfully!\n";
        echo "📧 Email: jeanmarcaguilar829@gmail.com\n";
        echo "🔑 Password: admin2024\n";
        echo "📊 Rows affected: " . $stmt->affected_rows . "\n";
        
        // Verify the update
        $checkStmt = $conn->prepare("SELECT email, user_type FROM users WHERE user_type = 'admin'");
        $checkStmt->execute();
        $result = $checkStmt->get_result();
        $admin = $result->fetch_assoc();
        
        echo "\n🔍 Verification:\n";
        echo "Current admin email: " . $admin['email'] . "\n";
        echo "User type: " . $admin['user_type'] . "\n";
        
    } else {
        echo "❌ Failed to update admin credentials\n";
        echo "Error: " . $stmt->error . "\n";
    }
    
    $stmt->close();
    closeDatabaseConnection($conn);
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
?>
