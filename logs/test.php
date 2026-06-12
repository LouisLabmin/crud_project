<?php

phpinfo();


//error_reporting(E_ALL);
//ini_set('display_errors', 1);

//if (!extension_loaded('imagick')) {
  //  echo "Imagick failed to load<br>";
//}

//print_r(get_loaded_extensions());



/* echo password_hash("Qwedcxzao0)!6", PASSWORD_BCRYPT, ["cost" => 12]);

$pass = "Qwedcxzao0)!6";
$hash = '$2y$12$SSCK27Ry3WRbc591MzN72.rJ0XAOk99P.lfV59eXzVPW04RzGzL1u';

var_dump(password_verify($pass, $hash));  

echo "Password length: " . strlen("Qwedcxzao0)!6") . "\n";
echo "Characters:\n";

for ($i = 0; $i < strlen("Qwedcxzao0)!6"); $i++) {
    echo $i . ": " . ord("Qwedcxzao0)!6"[$i]) . "\n";
}



if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pass = $_POST['password'] ?? '';

    echo "You typed: [$pass]<br>";
    echo "Length: " . strlen($pass) . "<br><br>";

    echo "ASCII breakdown:<br>";
    for ($i = 0; $i < strlen($pass); $i++) {
        echo $i . ": " . ord($pass[$i]) . "<br>";
    }
}
?>

<form method="POST">
    <input type="password" name="password">
    <button>Test</button>
</form> */

/*
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pass = $_POST['pass'] ?? '';
    echo "<pre>";
    echo "Password: [$pass]\n";
    echo "Length: " . strlen($pass) . "\n";
    echo "Hash: " . password_hash($pass, PASSWORD_DEFAULT) . "\n";
    echo "</pre>";
}
?>

<form method="POST">
    <input type="password" name="pass">
    <button>Generate Hash</button>
</form> 
*/


// test_bootstrap.php (place in D:\mywebsite\portfolio_site and open in browser)
//require_once __DIR__ . '/../Configuration/bootstrap.php';

//echo 'BASE VAR: ' . (isset($varWebSite) ? htmlspecialchars($varWebSite) : 'NOT SET') . "<br>\n";
//echo 'SITE TITLE: ' . (isset($site_title) ? htmlspecialchars($site_title) : 'NOT SET') . "<br>\n";
//echo 'CONFIG KEYS: ' . (isset($config) && is_array($config) ? implode(', ', array_keys($config)) : 'NOT SET') . "<br>\n";
// End of test_bootstrap.php    


