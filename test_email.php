<?php
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/log-in.php';

use PHPMailer\PHPMailer\PHPMailer;

// Test email sending
$testEmail = 'christianbacay042504@gmail.com';
$testCode = '123456';

echo "Testing email sending to: $testEmail\n";
echo "Test code: $testCode\n\n";

$result = sendLoginOtpEmail($testEmail, $testCode);

if ($result['success']) {
    echo "✅ Email sent successfully!\n";
    echo "Message: " . $result['message'] . "\n";
} else {
    echo "❌ Email sending failed!\n";
    echo "Error: " . $result['message'] . "\n";
    
    // Check environment variables
    echo "\n--- Environment Variables Check ---\n";
    $envVars = ['SMTP_HOST', 'SMTP_USERNAME', 'SMTP_PASSWORD', 'SMTP_PORT', 'SMTP_SECURE', 'SMTP_FROM_EMAIL'];
    foreach ($envVars as $var) {
        $value = readEnvValue($var);
        echo "$var: " . ($value ? '✓ Set' : '✗ Not set') . "\n";
    }
}
?>
