<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check syntax of the user-tourist-spots.php file
$file = 'User/user-tourist-spots.php';
$content = file_get_contents($file);

if ($content === false) {
    echo "Could not read file: $file\n";
    exit;
}

// Check for PHP syntax errors
$tmp_file = tempnam(sys_get_temp_dir(), 'php_syntax_check_');
file_put_contents($tmp_file, $content);

$output = [];
$return_var = 0;
exec("php -l \"$tmp_file\" 2>&1", $output, $return_var);

unlink($tmp_file);

if ($return_var === 0) {
    echo "No syntax errors found in $file\n";
} else {
    echo "Syntax errors found in $file:\n";
    foreach ($output as $line) {
        echo $line . "\n";
    }
}
?>
