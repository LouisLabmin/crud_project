<?php
/**
 * secure/db_connect.php
 * Standard PDO connection for production mode.
 *
 * Requires db_config.php to define:
 * $dsn, $db_user, $db_pass, $pdo_options
 */

try {
    // Create the PDO connection using variables defined in db_config.php
    $pdo = new PDO($dsn, $db_user, $db_pass, $pdo_options);
} catch (PDOException $e) {
    // Fail gracefully with a clear message
    die("Database connection failed: " . $e->getMessage());
}

