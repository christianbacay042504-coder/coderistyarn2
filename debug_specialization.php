<?php
// Debug file to check form submission values
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    echo "<h2>POST Data Received:</h2>";
    echo "<pre>";
    print_r($_POST);
    echo "</pre>";
    
    echo "<h2>Specialization Specific Check:</h2>";
    echo "Specialization value: '" . ($_POST['specialization'] ?? 'NOT SET') . "'<br>";
    echo "Is empty: " . (empty($_POST['specialization']) ? 'YES' : 'NO') . "<br>";
    echo "Trimmed value: '" . trim($_POST['specialization'] ?? '') . "'<br>";
    
    if (isset($_POST['specialization'])) {
        echo "JSON encoded: " . json_encode([$_POST['specialization']]) . "<br>";
    }
} else {
    echo "<h2>Debug Specialization Form Submission</h2>";
    echo "<p>Please submit the registration form to see the debug data.</p>";
}
?>
