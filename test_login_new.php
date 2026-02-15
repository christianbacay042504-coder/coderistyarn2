<?php
// Test login process - new version
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Simulate POST data
$_SERVER['REQUEST_METHOD'] = 'POST';
$_POST['action'] = 'login';
$_POST['email'] = 'adminlgu@gmail.com';
$_POST['password'] = 'admin123';

echo "<h2>Testing Login Process</h2>";

try {
    // Include the login script
    include __DIR__ . '/log-in/log-in.php';
    echo "<p style='color: green;'>✅ Login script executed without errors</p>";
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
    echo "<p>Stack trace: " . $e->getTraceAsString() . "</p>";
} catch (Error $e) {
    echo "<p style='color: red;'>❌ Fatal Error: " . $e->getMessage() . "</p>";
    echo "<p>Stack trace: " . $e->getTraceAsString() . "</p>";
}
?>
