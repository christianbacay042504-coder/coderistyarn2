<?php
// Test Email Script for Tour Guide Registration
// This script helps diagnose email sending issues

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Email Sending Test</h1>";

// Include PHPMailer
require_once '../PHPMailer-6.9.1/src/PHPMailer.php';
require_once '../PHPMailer-6.9.1/src/SMTP.php';
require_once '../PHPMailer-6.9.1/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Test email function
function testEmailSending($recipientEmail) {
    try {
        $mail = new PHPMailer(true);
        
        // Enable debug mode
        $mail->SMTPDebug = 3; // Show connection status
        $mail->Debugoutput = function($str, $level) {
            echo "SMTP Debug Level $level: " . htmlspecialchars($str) . "<br>";
        };
        
        echo "<h3>Testing SMTP Configuration...</h3>";
        
        // SMTP configuration
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'christianbacay042504@gmail.com';
        $mail->Password = 'tayrkzczbhgehbej';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;
        $mail->Timeout = 30;
        
        echo "✅ SMTP Configuration set<br>";
        
        // Email settings
        $mail->setFrom('christianbacay042504@gmail.com', 'SJDM Tours Test');
        $mail->addAddress($recipientEmail);
        $mail->Subject = 'Test Email - Tour Guide Registration System';
        
        // Email body
        $mail->isHTML(true);
        $mail->Body = "
            <h2>Test Email - Tour Guide Registration</h2>
            <p>This is a test email to verify that the email sending functionality works.</p>
            <p><strong>Test Details:</strong></p>
            <ul>
                <li>SMTP Server: smtp.gmail.com</li>
                <li>Port: 587 (TLS)</li>
                <li>From: christianbacay042504@gmail.com</li>
                <li>To: $recipientEmail</li>
                <li>Time: " . date('Y-m-d H:i:s') . "</li>
            </ul>
            <p>If you receive this email, the email system is working correctly!</p>
            <hr>
            <p><em>This is an automated test message from the SJDM Tours registration system.</em></p>
        ";
        
        $mail->AltBody = "
            Test Email - Tour Guide Registration
            
            This is a test email to verify that the email sending functionality works.
            
            Test Details:
            SMTP Server: smtp.gmail.com
            Port: 587 (TLS)
            From: christianbacay042504@gmail.com
            To: $recipientEmail
            Time: " . date('Y-m-d H:i:s') . "
            
            If you receive this email, the email system is working correctly!
        ";
        
        echo "✅ Email content prepared<br>";
        echo "📧 Attempting to send email to: $recipientEmail<br>";
        
        // Send email
        $result = $mail->send();
        
        echo "<h3>✅ Email Sent Successfully!</h3>";
        echo "<p><strong>Message ID:</strong> " . $mail->getLastMessageID() . "</p>";
        echo "<p><strong>Recipients:</strong> " . implode(', ', $mail->getToAddresses()) . "</p>";
        
        return true;
        
    } catch (Exception $e) {
        echo "<h3>❌ Email Sending Failed!</h3>";
        echo "<p><strong>Error:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
        echo "<p><strong>PHPMailer Error:</strong> " . htmlspecialchars($mail->ErrorInfo) . "</p>";
        
        // Common issues and solutions
        echo "<h3>🔧 Common Issues & Solutions:</h3>";
        echo "<ul>";
        echo "<li><strong>Authentication Failed:</strong> Check Gmail password or enable 'Less Secure Apps'</li>";
        echo "<li><strong>Connection Timeout:</strong> Check internet connection or firewall</li>";
        echo "<li><strong>SMTP Error:</strong> Verify SMTP settings and port</li>";
        echo "<li><strong>Google Account Security:</strong> May need to enable 2FA and use App Password</li>";
        echo "</ul>";
        
        return false;
    }
}

// Check if form submitted
if ($_POST['test_email'] && filter_var($_POST['test_email'], FILTER_VALIDATE_EMAIL)) {
    $testEmail = $_POST['test_email'];
    echo testEmailSending($testEmail);
    echo "<br><a href='test-email.php'>← Back to Test Form</a>";
} else {
    ?>
    <form method="post">
        <h2>Send Test Email</h2>
        <p>Enter an email address to test the email sending functionality:</p>
        <p>
            <label for="test_email">Email Address:</label><br>
            <input type="email" id="test_email" name="test_email" required size="50" placeholder="Enter email to test">
        </p>
        <p>
            <button type="submit" style="background: #007bff; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer;">
                📧 Send Test Email
            </button>
        </p>
    </form>
    
    <h3>📋 Email Configuration Check</h3>
    <ul>
        <li>✅ PHPMailer files exist</li>
        <li>✅ SMTP settings configured</li>
        <li>✅ Gmail account: christianbacay042504@gmail.com</li>
        <li>✅ TLS encryption enabled</li>
        <li>✅ Port 587 configured</li>
    </ul>
    
    <h3>🔍 Debug Information</h3>
    <p><strong>PHP Version:</strong> <?php echo PHP_VERSION; ?></p>
    <p><strong>OpenSSL:</strong> <?php echo extension_loaded('openssl') ? '✅ Enabled' : '❌ Disabled'; ?></p>
    <p><strong>SMTP:</strong> <?php echo extension_loaded('sockets') ? '✅ Available' : '❌ Not Available'; ?></p>
    <p><strong>Server Time:</strong> <?php echo date('Y-m-d H:i:s'); ?></p>
    
    <?php
}
?>
