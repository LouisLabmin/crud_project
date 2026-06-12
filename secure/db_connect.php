<?php
/**
 * secure/db_connect.php
 *
 * PDO connection handler with:
 * - Connection retries
 * - Connection timeout support
 * - Connection validation
 * - Development diagnostics
 * - Production-safe failures
 *
 * Required variables:
 *   $dsn
 *   $db_user
 *   $db_pass
 *   $pdo_options
 *
 * Returns:
 *   $pdo
 */

declare(strict_types=1);

// -------------------------------------------------------
// Connection Configuration
// -------------------------------------------------------

$maxRetries = 5;
$retryDelay = 3;

// Respect bootstrap setting if already defined
$pdo_options[PDO::ATTR_TIMEOUT] =
    $pdo_options[PDO::ATTR_TIMEOUT] ?? 60;

// Ensure MySQL connect timeout exists
if (stripos($dsn, 'connect_timeout=') === false) {
    $dsn .= ';connect_timeout=60';
}

// -------------------------------------------------------
// Connection Attempt
// -------------------------------------------------------

$pdo = null;
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

        // Validate connection
        $pdo->query('SELECT 1');

        $elapsedTime = round(
            microtime(true) - $startTime,
            2
        );

        error_log(
            sprintf(
                '[DB] Connected in %.2fs (Attempt %d)',
                $elapsedTime,
                $attempt
            )
        );

        break;

    } catch (PDOException $e) {

        $lastException = $e;

        error_log(
            sprintf(
                '[DB] Connection failed (%d/%d): %s',
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
// Connection Failure Handling
// -------------------------------------------------------

if (!$pdo) {

    $message = $lastException
        ? $lastException->getMessage()
        : 'Unknown database error';

    error_log(
        '[DB] All connection attempts failed: '
        . $message
    );

    http_response_code(503);

    if (
        defined('APP_DEBUG') &&
        APP_DEBUG === true
    ) {
        die(
            'Database connection failed after '
            . $maxRetries
            . ' attempts.<br><br>'
            . htmlspecialchars(
                $message,
                ENT_QUOTES,
                'UTF-8'
            )
        );
    }

    die(
        'Database service temporarily unavailable. '
        . 'Please try again later.'
    );
}

// -------------------------------------------------------
// Connection Available
// -------------------------------------------------------

return $pdo;