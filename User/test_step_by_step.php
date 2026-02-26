<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "Step 1: Starting PHP execution...<br>";

try {
    echo "Step 2: Including database.php...<br>";
    require_once '../config/database.php';
    echo "Step 3: Database.php included successfully<br>";
    
    echo "Step 4: Including auth.php...<br>";
    require_once '../config/auth.php';
    echo "Step 5: Auth.php included successfully<br>";
    
    echo "Step 6: Testing database connection...<br>";
    $conn = getDatabaseConnection();
    if ($conn) {
        echo "Step 7: Database connection successful<br>";
        closeDatabaseConnection($conn);
    } else {
        echo "Step 7: Database connection failed<br>";
    }
    
    echo "Step 8: All tests completed successfully<br>";
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "<br>";
    echo "File: " . $e->getFile() . "<br>";
    echo "Line: " . $e->getLine() . "<br>";
} catch (Error $e) {
    echo "FATAL ERROR: " . $e->getMessage() . "<br>";
    echo "File: " . $e->getFile() . "<br>";
    echo "Line: " . $e->getLine() . "<br>";
}
?>
