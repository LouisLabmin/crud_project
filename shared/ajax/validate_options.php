<?php
// shared/ajax/validate_options.php
// This script validates the JSON structure of the options file. It checks for syntax errors and ensures that the file exists. 
//  This is used to prevent issues when loading options in the admin panel or on the frontend.

$p = 'shared/options.json';
if (!file_exists($p)) {
    echo "MISSING\n";
    exit(1);
}
$s = file_get_contents($p);
json_decode($s, true);
echo json_last_error_msg() . PHP_EOL;
