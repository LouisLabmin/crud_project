<?php
/**
 * configuration/bootstrap.php
 * Central application bootstrap.
 */

declare(strict_types=1);

// -------------------------------------------------------
// 1. Development Error Reporting
// -------------------------------------------------------

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

// -------------------------------------------------------
// 2. Load Environment Configuration
// -------------------------------------------------------

$envFile = dirname(__DIR__) . '/.env';

if (!file_exists($envFile)) {
    throw new RuntimeException(
        '.env file not found: ' . $envFile
    );
}

$env = parse_ini_file(
    $envFile,
    false,
    INI_SCANNER_TYPED
);

if ($env === false) {
    throw new RuntimeException(
        'Unable to parse .env file.'
    );
}

// -------------------------------------------------------
// 3. Start Session
// -------------------------------------------------------

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// -------------------------------------------------------
// 4. Database Configuration
// -------------------------------------------------------

$host    = $env['DB_HOST'] ?? '';
$port    = $env['DB_PORT'] ?? 3306;
$dbname  = $env['DB_NAME'] ?? '';
$db_user = $env['DB_USER'] ?? '';
$db_pass = $env['DB_PASS'] ?? '';

$dsn = sprintf(
    'mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4',
    $host,
    $port,
    $dbname
);

$pdo_options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
    PDO::ATTR_TIMEOUT            => 60,
];

// -------------------------------------------------------
// 5. Application Configuration
// -------------------------------------------------------

define(
    'APP_ENV',
    $env['APP_ENV'] ?? 'development'
);

define(
    'APP_DEBUG',
    filter_var(
        $env['APP_DEBUG'] ?? true,
        FILTER_VALIDATE_BOOLEAN
    )
);

define(
    'APP_NAME',
    $env['APP_NAME'] ?? 'My Portfolio'
);

define(
    'APP_BASE',
    $env['APP_BASE'] ?? ''
);

define(
    'APP_TZ',
    $env['APP_TZ'] ?? 'Africa/Johannesburg'
);

// -------------------------------------------------------
// 6. Timezone
// -------------------------------------------------------

date_default_timezone_set(APP_TZ);

// -------------------------------------------------------
// 7. PHP Error Reporting
// -------------------------------------------------------

if (APP_DEBUG) {

    ini_set('display_errors', '1');
    ini_set('display_startup_errors', '1');

    error_reporting(E_ALL);

} else {

    ini_set('display_errors', '0');

    error_reporting(0);
}

// -------------------------------------------------------
// 8. Create Database Connection
// -------------------------------------------------------

$pdo = require __DIR__ . '/../secure/db_connect.php';

// -------------------------------------------------------
// 9. Global Configuration Array
// -------------------------------------------------------

$config = [
    'debug'       => APP_DEBUG,
    'baseUrl'     => APP_BASE,
    'siteName'    => APP_NAME,
    'environment' => APP_ENV,
    'timezone'    => APP_TZ,
];

$GLOBALS['APP_OPTIONS'] = $config;

// -------------------------------------------------------
// 10. Convenience Variables
// -------------------------------------------------------

$baseUrl   = APP_BASE;
$siteName  = APP_NAME;
$debugMode = APP_DEBUG;
$appEnv    = APP_ENV;
$timezone  = APP_TZ;

// -------------------------------------------------------
// End Bootstrap
// -------------------------------------------------------