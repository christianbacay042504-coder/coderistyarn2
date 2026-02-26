<?php
// Simple test to check if PHP is working
header('Content-Type: application/json');
echo json_encode([
    'success' => true,
    'message' => 'PHP test successful',
    'timestamp' => date('Y-m-d H:i:s')
]);
?>
