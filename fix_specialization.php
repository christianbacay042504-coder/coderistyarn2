<?php
require_once __DIR__ . '/config/database.php';

echo "<h2>Fix Specialization Column</h2>";

try {
    $conn = getDatabaseConnection();
    if (!$conn) {
        throw new Exception("Database connection failed");
    }
    
    echo "<h3>Step 1: Check current structure</h3>";
    $result = $conn->query("DESCRIBE registration_tour_guides");
    while ($row = $result->fetch_assoc()) {
        if ($row['Field'] === 'specialization') {
            echo "<p><strong>Current specialization column:</strong><br>";
            echo "Type: " . $row['Type'] . "<br>";
            echo "Null: " . $row['Null'] . "<br>";
            echo "Default: " . $row['Default'] . "</p>";
        }
    }
    
    echo "<h3>Step 2: Update column structure</h3>";
    $sql = "ALTER TABLE registration_tour_guides MODIFY COLUMN specialization TEXT DEFAULT NULL COMMENT 'JSON format: [\"mountain\",\"waterfall\",\"cultural\",\"adventure\",\"photography\"]'";
    
    if ($conn->query($sql)) {
        echo "<p style='color: green;'>✓ Column updated successfully</p>";
    } else {
        echo "<p style='color: red;'>✗ Error updating column: " . $conn->error . "</p>";
    }
    
    echo "<h3>Step 3: Fix existing data</h3>";
    $sql = "UPDATE registration_tour_guides SET specialization = '[\"mountain\"]' WHERE specialization = '0' OR specialization IS NULL OR specialization = ''";
    
    if ($conn->query($sql)) {
        $affected = $conn->affected_rows;
        echo "<p style='color: green;'>✓ Fixed $affected records with invalid specialization data</p>";
    } else {
        echo "<p style='color: red;'>✗ Error fixing data: " . $conn->error . "</p>";
    }
    
    echo "<h3>Step 4: Verify the fix</h3>";
    $result = $conn->query("SELECT id, specialization FROM registration_tour_guides ORDER BY id DESC LIMIT 5");
    
    if ($result && $result->num_rows > 0) {
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr><th>ID</th><th>Specialization</th><th>Decoded</th></tr>";
        
        while ($row = $result->fetch_assoc()) {
            $raw = $row['specialization'];
            $decoded = json_decode($raw, true);
            $decodedStr = is_array($decoded) ? implode(', ', $decoded) : 'Invalid JSON';
            
            echo "<tr>";
            echo "<td>" . $row['id'] . "</td>";
            echo "<td>" . htmlspecialchars($raw) . "</td>";
            echo "<td>" . htmlspecialchars($decodedStr) . "</td>";
            echo "</tr>";
        }
        
        echo "</table>";
    }
    
    echo "<h3>Step 5: Test insertion</h3>";
    $testValue = json_encode(['adventure']);
    $sql = "INSERT INTO registration_tour_guides (status, last_name, first_name, primary_phone, email, specialization) VALUES ('Test', 'Test', 'User', '123456789', 'test@example.com', ?)";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $testValue);
    
    if ($stmt->execute()) {
        $newId = $conn->insert_id;
        echo "<p style='color: green;'>✓ Test insertion successful (ID: $newId)</p>";
        
        // Clean up test record
        $conn->query("DELETE FROM registration_tour_guides WHERE id = $newId");
        echo "<p style='color: blue;'>♻ Test record cleaned up</p>";
    } else {
        echo "<p style='color: red;'>✗ Test insertion failed: " . $stmt->error . "</p>";
    }
    
    $stmt->close();
    $conn->close();
    
    echo "<h3>✅ Specialization column fix completed!</h3>";
    echo "<p><a href='check_table_structure.php'>Check table structure</a> | <a href='../log-in/register-guide.php'>Test registration form</a></p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
}
?>
