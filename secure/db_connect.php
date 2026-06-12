<?php
/**
 * secure/db_connect.php
 * Standard PDO connection with timeout and retry support.
 *
 * Requires:
 * $dsn
 * $db_user
 * $db_pass
 * $pdo_options
 */

$maxRetries = 3;
$retryDelay = 2; // seconds

// Ensure timeout is set
$pdo_options[PDO::ATTR_TIMEOUT] = 60;

// Add MySQL connect timeout if not already present
if (strpos($dsn, 'connect_timeout=') === false) {
    $dsn .= ';connect_timeout=60';
}

$pdo = null;
$lastException = null;

for ($attempt = 1; $attempt <= $maxRetries; $attempt++) {

    try {

        $start = microtime(true);

        $pdo = new PDO(
            $dsn,
            $db_user,
            $db_pass,
            $pdo_options
        );

        // Verify connection is actually usable
        $pdo->query('SELECT 1');

        $elapsed = round(microtime(true) - $start, 2);

        // Uncomment for diagnostics
        // error_log("Database connected in {$elapsed}s on attempt {$attempt}");

        break;

    } catch (PDOException $e) {

        $lastException = $e;

        // Uncomment for diagnostics
        error_log(
            sprintf(
                'DB connection attempt %d/%d failed: %s',
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

if (!$pdo) {

    // Development diagnostics
    if (
        defined('APP_ENV') &&
        APP_ENV === 'development'
    ) {
        die(
            'Database connection failed after '
            . $maxRetries
            . ' attempts: '
            . $lastException->getMessage()
        );
    }

    // Production-safe message
    die('Database connection failed.');
}