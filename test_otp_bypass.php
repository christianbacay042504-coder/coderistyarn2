<?php
require_once __DIR__ . '/config/auth.php';
require_once __DIR__ . '/config/smtp.php';

// Create a modified version of sendLoginOtpEmail that bypasses actual email sending
function sendLoginOtpEmailTest(string $toEmail, string $code): array
{
    // For testing purposes, we'll just log the code instead of sending email
    error_log("OTP Code for $toEmail: $code");
    
    // In a real implementation, this would send an email
    // For now, we'll just return success to test the modal
    
    return [
        'success' => true,
        'message' => 'Verification code sent to your email (TEST MODE)',
        'debug_code' => $code // Include the code for testing
    ];
}

// Test the function
$result = sendLoginOtpEmailTest('christianbacay042504@gmail.com', '123456');
print_r($result);
?>
