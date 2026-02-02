<?php
// Password Hash Generator
// This script generates a secure password hash for the admin user

// The password you want to hash
$password = 'admin123';

// Generate the hash
$hash = password_hash($password, PASSWORD_DEFAULT);

echo "Password: " . $password . "\n";
echo "Hash: " . $hash . "\n\n";

echo "Copy this hash and use it in the SQL file or UPDATE query:\n";
echo "UPDATE users SET password = '" . $hash . "' WHERE email = 'adminlgu3@.gov';\n";
?>
