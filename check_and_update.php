<?php
// Check existing users and update admin
require_once __DIR__ . '/config/database.php';

try {
    $conn = getDatabaseConnection();
    
    if (!$conn) {
        die("Database connection failed");
    }
    
    // Check all users
    echo "📋 All users in database:\n";
    $result = $conn->query("SELECT id, first_name, last_name, email, user_type FROM users ORDER BY id");
    while ($row = $result->fetch_assoc()) {
        echo "ID: {$row['id']} | Name: {$row['first_name']} {$row['last_name']} | Email: {$row['email']} | Type: {$row['user_type']}\n";
    }
    
    echo "\n🔍 Looking for admin users...\n";
    
    // Find admin users
    $adminResult = $conn->query("SELECT id, email FROM users WHERE user_type = 'admin'");
    $admins = [];
    while ($row = $adminResult->fetch_assoc()) {
        $admins[] = $row;
        echo "Found admin - ID: {$row['id']} | Email: {$row['email']}\n";
    }
    
    if (!empty($admins)) {
        // Update the first admin found
        $adminId = $admins[0]['id'];
        echo "\n🔄 Updating admin ID: $adminId\n";
        
        $stmt = $conn->prepare("UPDATE users SET email = ?, password = ? WHERE id = ?");
        $newEmail = 'jeanmarcaguilar829@gmail.com';
        $newPassword = '$2y$10$VzQT8RqQw8Pb95baXQ6aLeMh.nQImQT2vMmc/lwKa/Zj4zGhtk1Ru'; // admin2024
        
        $stmt->bind_param("ssi", $newEmail, $newPassword, $adminId);
        
        if ($stmt->execute()) {
            echo "✅ Admin credentials updated successfully!\n";
            echo "📧 New Email: jeanmarcaguilar829@gmail.com\n";
            echo "🔑 New Password: admin2024\n";
            echo "📊 Rows affected: " . $stmt->affected_rows . "\n";
        } else {
            echo "❌ Failed to update: " . $stmt->error . "\n";
        }
        $stmt->close();
    } else {
        echo "❌ No admin users found in database!\n";
    }
    
    closeDatabaseConnection($conn);
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
?>
