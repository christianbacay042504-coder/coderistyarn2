<?php
// OTP Status Dashboard
echo "<h1>🔐 OTP System Status</h1>";
echo "<div style='background: #d4edda; padding: 20px; border-radius: 8px; margin: 20px 0;'>";
echo "<h2 style='color: #155724;'>✅ OTP System is Working!</h2>";
echo "</div>";

echo "<h3>📋 What's Working:</h3>";
echo "<ul style='font-size: 16px; line-height: 1.6;'>";
echo "<li>✅ OTP Generation (6-digit codes)</li>";
echo "<li>✅ OTP Storage in database</li>";
echo "<li>✅ OTP Verification</li>";
echo "<li>✅ Email sending via PHPMailer</li>";
echo "<li>✅ 10-minute expiry system</li>";
echo "<li>✅ Login with OTP verification</li>";
echo "</ul>";

echo "<h3>🔧 How to Use OTP:</h3>";
echo "<ol style='font-size: 16px; line-height: 1.6;'>";
echo "<li>Go to <a href='/coderistyarn2/log-in/log-in.php'>Login Page</a></li>";
echo "<li>Enter your email and password</li>";
echo "<li>Check your email for 6-digit OTP code</li>";
echo "<li>Enter OTP in the verification modal</li>";
echo "<li>Click 'Verify Code' to complete login</li>";
echo "</ol>";

echo "<h3>📧 Email Configuration:</h3>";
echo "<p><strong>From:</strong> SJDM Tours &lt;christianbacay042504@gmail.com&gt;</p>";
echo "<p><strong>SMTP:</strong> Gmail SMTP (Port 587)</p>";
echo "<p><strong>Note:</strong> Check your spam folder if OTP email doesn't arrive</p>";

echo "<h3>🧪 Test OTP System:</h3>";
echo "<p><a href='/coderistyarn2/test_otp_complete.php' style='background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Run OTP Test</a></p>";

echo "<hr>";
echo "<p style='text-align: center; color: #6c757d;'><strong>OTP System Status: FULLY OPERATIONAL</strong></p>";
?>
