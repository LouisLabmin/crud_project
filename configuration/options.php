<?php
// configuration/options.php
declare(strict_types=1);

/**
 * Loads site configuration from the database (site_settings table)
 * Replaces the old options.json system entirely.
 */

// Load DB connection
require_once __DIR__ . '/../secure/db_config.php';
require_once __DIR__ . '/../secure/db_connect.php';

// Fetch latest settings row
$stmt = $pdo->query("SELECT * FROM site_settings ORDER BY id DESC LIMIT 1");
$row = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$row) {
    die("Critical error: site_settings table is empty.");
}

/* -------------------------------------------------------
   DETERMINE ACTIVE BASE URL
------------------------------------------------------- */

// Choose dev or prod base URL
$rawBase = ($row['base_dev'] == 1)
    ? ($row['base_url_dev'] ?? '')
    : ($row['base_url_prod'] ?? '');

// Trim slashes from DB value
$rawBase = trim($rawBase, '/');

// Normalize:
// - If empty → site is at domain root → APP_BASE = ''
// - If not empty → ensure it starts with a single slash
$baseUrl = ($rawBase === '') ? '' : '/' . $rawBase;

/* -------------------------------------------------------
   DEFINE CONSTANTS
------------------------------------------------------- */
define('APP_BASE',  $baseUrl);
define('APP_DEBUG', (bool)$row['base_debug']);
define('APP_NAME',  $row['site_name']);
define('APP_ENV',   ($row['base_dev'] == 1 ? 'development' : 'production'));
define('APP_TZ',    $row['base_tz']);

/* -------------------------------------------------------
   PROVIDE $config ARRAY FOR BOOTSTRAP
------------------------------------------------------- */
$config = [
    'debug'       => APP_DEBUG,
    'baseUrl'     => APP_BASE,
    'siteName'    => APP_NAME,
    'environment' => APP_ENV,
    'timezone'    => APP_TZ
];

// Make available globally if needed
$GLOBALS['APP_OPTIONS'] = $config;
