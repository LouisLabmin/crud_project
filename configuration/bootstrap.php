<?php
/**
 * configuration/bootstrap.php
 * Central application bootstrap.
 */

declare(strict_types=1);

// -------------------------------------------------------
// DEVELOPMENT ERROR REPORTING
// -------------------------------------------------------

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

// -------------------------------------------------------
// 1. Load DB Credentials
// -------------------------------------------------------

$creds = require __DIR__ . '/../secure/db_config.php';

$host    = $creds['host'];
$dbname  = $creds['dbname'];
$db_user = $creds['user'];
$db_pass = $creds['pass'];

// -------------------------------------------------------
// 2. Database Connection Configuration
// -------------------------------------------------------

$dsn = sprintf(
    'mysql:host=%s;port=3306;dbname=%s;charset=utf8mb4;connect_timeout=30',
    $host,
    $dbname
);

$pdo_options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
    PDO::ATTR_TIMEOUT            => 30,
];

// -------------------------------------------------------
// 3. Start Session
// -------------------------------------------------------

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// -------------------------------------------------------
// 4. Create Database Connection
// -------------------------------------------------------

$maxRetries   = 5;
$retryDelay   = 3;
$pdo          = null;
$lastException = null;

for ($attempt = 1; $attempt <= $maxRetries; $attempt++) {

    try {

        $startTime = microtime(true);

        $pdo = new PDO(
            $dsn,
            $db_user,
            $db_pass,
            $pdo_options
        );

        // Verify connection
        $pdo->query('SELECT 1');

        error_log(
            sprintf(
                '[DB] Connected successfully in %.2f seconds (attempt %d)',
                microtime(true) - $startTime,
                $attempt
            )
        );

        break;

    } catch (PDOException $e) {

        $lastException = $e;

        error_log(
            sprintf(
                '[DB] Connection failed (attempt %d/%d): %s',
                $attempt,
                $maxRetries,
                $e->getMessage()
            )
        );

        if ($attempt < $maxRetries) {
            sleep($retryDelay);
        }
    }
}

// -------------------------------------------------------
// 5. Connection Failure Handling
// -------------------------------------------------------

if (!$pdo) {

    error_log(
        '[DB] All connection attempts failed. Last error: ' .
        ($lastException
            ? $lastException->getMessage()
            : 'Unknown error')
    );

    http_response_code(503);

    die(
        'Database service temporarily unavailable. Please try again later.'
    );
}

// -------------------------------------------------------
// 6. Application Configuration
// -------------------------------------------------------

date_default_timezone_set('Africa/Johannesburg');

// -------------------------------------------------------
// 7. Application Constants
// -------------------------------------------------------

define('APP_ENV', 'development');

define('APP_DEBUG', true);

define('APP_NAME', 'My Portfolio');

define(
    'APP_BASE',
    'http://10.0.0.121/crud_project'
);

define(
    'APP_TZ',
    'Africa/Johannesburg'
);

// -------------------------------------------------------
// 8. Global Configuration Array
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
// 9. Convenience Variables (Optional)
// -------------------------------------------------------

$baseUrl   = APP_BASE;
$siteName  = APP_NAME;
$debugMode = APP_DEBUG;
$appEnv    = APP_ENV;
$timezone  = APP_TZ;

// -------------------------------------------------------
// End Bootstrap
// -------------------------------------------------------