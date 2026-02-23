<?php
// Simple test to verify logo data URI is working
$logoPath = __DIR__ . '/lgo.png';
$logoData = file_get_contents($logoPath);

if ($logoData === false) {
    die('Logo file not found');
}

$logoBase64 = base64_encode($logoData);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Logo Test</title>
</head>
<body>
    <h1>Logo Test Page</h1>
    <p>Testing logo data URI...</p>
    <img src="data:image/png;base64,<?php echo $logoBase64; ?>" alt="Test Logo" style="border: 2px solid red; padding: 10px;">
    <p>If you can see the logo above, the data URI method is working!</p>
</body>
</html>
