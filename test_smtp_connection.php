<?php
// Test with different SMTP settings
echo "Testing alternative SMTP configuration...\n\n";

// Try with explicit SSL context options
$context = stream_context_create([
    'ssl' => [
        'verify_peer' => false,
        'verify_peer_name' => false,
        'allow_self_signed' => true
    ]
]);

// Test basic connection to Gmail SMTP
$smtp_host = 'tls://smtp.gmail.com';
$smtp_port = 587;

echo "Attempting to connect to $smtp_host:$smtp_port...\n";

$socket = @stream_socket_client(
    $smtp_host . ':' . $smtp_port,
    $errno,
    $errstr,
    30,
    STREAM_CLIENT_CONNECT,
    $context
);

if ($socket) {
    echo "✅ Successfully connected to Gmail SMTP\n";
    fclose($socket);
} else {
    echo "❌ Failed to connect to Gmail SMTP\n";
    echo "Error: $errstr ($errno)\n";
}

// Check if OpenSSL extension is loaded
echo "\nOpenSSL Extension: " . (extension_loaded('openssl') ? '✅ Loaded' : '❌ Not loaded') . "\n";

// Check PHP version
echo "PHP Version: " . PHP_VERSION . "\n";

// Check if we can reach Gmail's servers
echo "\nTesting network connectivity to gmail.com...\n";
$ping = @fsockopen('smtp.gmail.com', 587, $errno, $errstr, 5);
if ($ping) {
    echo "✅ Can reach smtp.gmail.com:587\n";
    fclose($ping);
} else {
    echo "❌ Cannot reach smtp.gmail.com:587 - $errstr ($errno)\n";
}
?>
