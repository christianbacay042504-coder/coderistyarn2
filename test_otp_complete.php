<?php
// Test OTP generation and storage
require_once __DIR__ . '/config/auth.php';

echo "<h2>OTP System Test</h2>";

// Test OTP generation
$otpCode = generateOtpCode();
echo "<p>Generated OTP: <strong>$otpCode</strong></p>";

// Test OTP storage
$email = 'adminlgu@gmail.com';
$userId = 30; // Admin user ID

$storeResult = storeOtpCode($userId, $email, $otpCode, 'login');

if ($storeResult['success']) {
    echo "<p style='color: green;'>✅ OTP stored successfully</p>";
    
    // Test OTP verification
    $verifyResult = verifyOtpCode($email, $otpCode, 'login');
    
    if ($verifyResult['success']) {
        echo "<p style='color: green;'>✅ OTP verification successful</p>";
    } else {
        echo "<p style='color: red;'>❌ OTP verification failed: " . $verifyResult['message'] . "</p>";
    }
} else {
    echo "<p style='color: red;'>❌ OTP storage failed: " . $storeResult['message'] . "</p>";
}

// Test email sending
echo "<hr><h3>Email Test:</h3>";
$emailResult = sendLoginOtpEmail($email, $otpCode);

if ($emailResult['success']) {
    echo "<p style='color: green;'>✅ Email sent successfully</p>";
} else {
    echo "<p style='color: orange;'>⚠️ Email failed: " . $emailResult['message'] . "</p>";
    echo "<p><small>Note: Check your Gmail spam folder or configure SMTP settings properly</small></p>";
}

echo "<hr>";
echo "<h3>How OTP Works:</h3>";
echo "<ol>";
echo "<li>User enters email and password</li>";
echo "<li>System generates 6-digit OTP code</li>";
echo "<li>OTP is stored in database with 10-minute expiry</li>";
echo "<li>OTP is sent to user's email</li>";
echo "<li>User enters OTP in verification modal</li>";
echo "<li>System verifies OTP and completes login</li>";
echo "</ol>";
?>
