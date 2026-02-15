<?php
require_once __DIR__ . '/config/database.php';

echo "<h2>Check Specialization Data in Database</h2>";

try {
    $conn = getDatabaseConnection();
    if (!$conn) {
        throw new Exception("Database connection failed");
    }
    
    // Get the latest registration
    $sql = "SELECT id, specialization, created_at FROM registration_tour_guides ORDER BY id DESC LIMIT 5";
    $result = $conn->query($sql);
    
    if ($result && $result->num_rows > 0) {
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr><th>ID</th><th>Specialization (Raw)</th><th>Specialization (Decoded)</th><th>Created At</th></tr>";
        
        while ($row = $result->fetch_assoc()) {
            $specializationRaw = $row['specialization'];
            $specializationDecoded = '';
            
            if (!empty($specializationRaw)) {
                $decoded = json_decode($specializationRaw, true);
                if (is_array($decoded)) {
                    $specializationDecoded = implode(', ', $decoded);
                } else {
                    $specializationDecoded = 'Invalid JSON: ' . $specializationRaw;
                }
            } else {
                $specializationDecoded = 'Empty';
            }
            
            echo "<tr>";
            echo "<td>" . $row['id'] . "</td>";
            echo "<td>" . htmlspecialchars($specializationRaw) . "</td>";
            echo "<td>" . htmlspecialchars($specializationDecoded) . "</td>";
            echo "<td>" . $row['created_at'] . "</td>";
            echo "</tr>";
        }
        
        echo "</table>";
    } else {
        echo "<p>No records found in registration_tour_guides table.</p>";
    }
    
    $conn->close();
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
}
?>
