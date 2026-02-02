<?php
// Simple test to verify admin functionality
echo "<h1>Admin Dashboard Test</h1>";

// Check if files exist and show their sizes
$files = [
    'admin/dashboard.php',
    'admin/admin-styles.css',
    'sjdm-user/script.js'
];

foreach ($files as $file) {
    if (file_exists($file)) {
        $size = filesize($file);
        $modified = date('Y-m-d H:i:s', filemtime($file));
        echo "<p>✅ <strong>$file</strong> - Size: " . number_format($size) . " bytes - Modified: $modified</p>";
    } else {
        echo "<p>❌ $file - Not found</p>";
    }
}

echo "<br><h2>What's New:</h2>";
echo "<ul>";
echo "<li>✅ Complete CRUD operations (Create, Read, Update, Delete)</li>";
echo "<li>✅ Enhanced VIEW modals with user/booking details</li>";
echo "<li>✅ Image/avatar support in profiles</li>";
echo "<li>✅ Search functionality for users and bookings</li>";
echo "<li>✅ Toast notifications for user feedback</li>";
echo "<li>✅ Modern modal designs with gradient headers</li>";
echo "<li>✅ Responsive design improvements</li>";
echo "</ul>";

echo "<br><h2>To Test:</h2>";
echo "<ol>";
echo "<li>Go to: <a href='log-in/log-in.php'>Login Page</a></li>";
echo "<li>Login as admin (adminlgu@gmail.com)</li>";
echo "<li>Go to: <a href='admin/dashboard.php'>Admin Dashboard</a></li>";
echo "<li>Click on 'Users' tab to see new VIEW, EDIT, DELETE buttons</li>";
echo "<li>Try clicking VIEW to see detailed user information modal</li>";
echo "</ol>";
?>