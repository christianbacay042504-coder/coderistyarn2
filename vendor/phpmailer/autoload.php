<?php
// Simple autoloader for PHPMailer
spl_autoload_register(function ($class) {
    // Handle PHPMailer classes
    if (strpos($class, 'PHPMailer') === 0) {
        $file = __DIR__ . '/phpmailer/' . str_replace('\\', '/', $class) . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
        
        // Try without PHPMailer prefix
        $relative_class = str_replace('PHPMailer\\PHPMailer\\', '', $class);
        $file = __DIR__ . '/phpmailer/' . str_replace('\\', '/', $relative_class) . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});
