<?php
require_once __DIR__ . '/config/database.php';

echo "<h2>Check Specialization Column Structure</h2>";

try {
    $conn = getDatabaseConnection();
    if (!$conn) {
        throw new Exception("Database connection failed");
    }
    
    // Check table structure
    $result = $conn->query("DESCRIBE registration_tour_guides");
    
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
    
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['Field'] . "</td>";
        echo "<td>" . $row['Type'] . "</td>";
        echo "<td>" . $row['Null'] . "</td>";
        echo "<td>" . $row['Key'] . "</td>";
        echo "<td>" . $row['Default'] . "</td>";
        echo "<td>" . $row['Extra'] . "</td>";
        echo "</tr>";
        
        // Highlight specialization row
        if ($row['Field'] === 'specialization') {
            echo "<script>document.currentScript.parentElement.lastElementChild.style.backgroundColor='yellow';</script>";
        }
    }
    
    echo "</table>";
    
    // Check if we need to update the column
    echo "<h3>Sample Data Check:</h3>";
    $result = $conn->query("SELECT id, specialization FROM registration_tour_guides ORDER BY id DESC LIMIT 3");
    
    if ($result && $result->num_rows > 0) {
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr><th>ID</th><th>Specialization (Raw)</th><th>JSON Decode Test</th></tr>";
        
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
    
    $conn->close();
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
}
?>
