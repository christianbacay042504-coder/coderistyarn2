<?php
require_once __DIR__ . '/../config/auth.php';

// Logout the user
logoutUser();

// Redirect to login page
header('Location:log-in.php');
exit();
?>