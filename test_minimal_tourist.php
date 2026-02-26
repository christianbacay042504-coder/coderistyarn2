<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'config/database.php';
require_once 'config/auth.php';

$conn = getDatabaseConnection();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Test Tourist Spots</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .spot { border: 1px solid #ccc; margin: 10px; padding: 10px; }
    </style>
</head>
<body>
    <h1>Tourist Spots Test</h1>
    
    <?php if ($conn): ?>
        <p>Database connected successfully</p>
        
        <?php
        $query = "SELECT * FROM tourist_spots WHERE status = 'active' ORDER BY name LIMIT 5";
        $result = $conn->query($query);
        
        if ($result && $result->num_rows > 0):
        ?>
            <p>Found <?php echo $result->num_rows; ?> tourist spots:</p>
            <?php while ($spot = $result->fetch_assoc()): ?>
                <div class="spot">
                    <h3><?php echo htmlspecialchars($spot['name']); ?></h3>
                    <p>Category: <?php echo htmlspecialchars($spot['category']); ?></p>
                    <p>Description: <?php echo htmlspecialchars($spot['description']); ?></p>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No tourist spots found or query failed: <?php echo $conn->error; ?></p>
        <?php endif; ?>
        
        <?php closeDatabaseConnection($conn); ?>
    <?php else: ?>
        <p>Database connection failed</p>
    <?php endif; ?>
</body>
</html>
