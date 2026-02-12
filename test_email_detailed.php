<?php
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/log-in.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Test email sending with detailed error reporting
$testEmail = 'christianbacay042504@gmail.com';
$testCode = '123456';

echo "Testing email sending to: $testEmail\n";
echo "Test code: $testCode\n\n";

// Get environment variables
$host = readEnvValue('SMTP_HOST');
$username = readEnvValue('SMTP_USERNAME');
$password = readEnvValue('SMTP_PASSWORD');
$port = (int) (readEnvValue('SMTP_PORT') ?: 587);
$secure = readEnvValue('SMTP_SECURE') ?: 'tls';
$fromEmail = readEnvValue('SMTP_FROM_EMAIL') ?: $username;
$fromName = readEnvValue('SMTP_FROM_NAME') ?: 'SJDM Tours';

echo "SMTP Configuration:\n";
echo "Host: $host\n";
echo "Username: $username\n";
echo "Password: " . (str_repeat('*', strlen($password))) . "\n";
echo "Port: $port\n";
echo "Secure: $secure\n";
echo "From Email: $fromEmail\n";
echo "From Name: $fromName\n\n";

$mail = new PHPMailer(true);

try {
    $mail->SMTPDebug = SMTP::DEBUG_SERVER; // Enable verbose debug output
    $mail->isSMTP();
    $mail->Host = $host;
    $mail->SMTPAuth = true;
    $mail->Username = $username;
    $mail->Password = $password;
    $mail->Port = $port;

    if ($secure) {
        $mail->SMTPSecure = $secure;
    }

    $mail->setFrom($fromEmail, $fromName);
    $mail->addAddress($testEmail);

    $mail->isHTML(true);
    $mail->Subject = 'Test SJDM Tours verification code';
    $mail->Body = 'This is a test email with verification code: ' . $testCode;

    echo "Attempting to send email...\n";
    $mail->send();
    echo "✅ Email sent successfully!\n";
} catch (Exception $e) {
    echo "❌ Email sending failed!\n";
    echo "Error: " . $e->getMessage() . "\n";
    echo "SMTP Error: " . $mail->ErrorInfo . "\n";
} catch (Throwable $e) {
    echo "❌ Fatal error occurred!\n";
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}
?>
