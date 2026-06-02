<?php
// configuration/bootstrap.php
// Central application bootstrap.

declare(strict_types=1);


// -------------------------------------------------------
// 1. Load DB credentials from secure config
// -------------------------------------------------------
$creds = require __DIR__ . '/../secure/db_config.php';

$host    = $creds['host'];
$dbname  = $creds['dbname'];
$db_user = $creds['user'];
$db_pass = $creds['pass'];

$dsn = "mysql:host={$host};dbname={$dbname};charset=utf8mb4";

$pdo_options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

// -------------------------------------------------------
// 2. Start session
// -------------------------------------------------------
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// -------------------------------------------------------
// 3. HUMAN CHECK — inclusion-based (only run on specific pages)
// -------------------------------------------------------
$uriPath = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH);
$normalizedUri = '/' . ltrim($uriPath, '/');

// Only these pages require Human Check
$includedPrefixes = [
    '/public/contact.php',
];

$shouldRunHumanCheck = false;

foreach ($includedPrefixes as $prefix) {
    if (str_starts_with($normalizedUri, $prefix)) {
        $shouldRunHumanCheck = true;
        break;
    }
}

$cooldownSeconds = 5;
$today = date('Y-m-d');

if (
    $shouldRunHumanCheck &&
    (!isset($_SESSION['human_verified']) || $_SESSION['human_verified'] !== $today)
) {
    $errorMsg = '';

    if (isset($_SESSION['human_cooldown_until']) && time() < $_SESSION['human_cooldown_until']) {
        $remaining = $_SESSION['human_cooldown_until'] - time();
        $errorMsg = "Too many failed attempts. Please wait {$remaining} seconds.";
    }

    if (
        empty($errorMsg) &&
        $_SERVER['REQUEST_METHOD'] === 'POST' &&
        isset($_POST['human_check'], $_POST['a'], $_POST['b'])
    ) {
        $answer   = (int)$_POST['human_check'];
        $a        = (int)$_POST['a'];
        $b        = (int)$_POST['b'];
        $expected = $a + $b;

        if (!empty($_POST['website'])) {
            $errorMsg = "Bot detected.";
            $_SESSION['human_cooldown_until'] = time() + $cooldownSeconds;

        } elseif ($answer === $expected) {
            $_SESSION['human_verified'] = $today;
            unset($_SESSION['human_cooldown_until']);
            header("Location: " . $uriPath);
            exit;

        } else {
            $errorMsg = "Incorrect answer. Please try again.";
            $_SESSION['human_cooldown_until'] = time() + $cooldownSeconds;
        }
    }

    $a = rand(1, 9);
    $b = rand(1, 9);

    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <title>Human Verification</title>
        <link rel="stylesheet" href="/vendor/bootstrap/css/bootstrap.min.css">
    </head>
    <body class="bg-light d-flex justify-content-center align-items-center" style="height:100vh;">
        <div class="card shadow p-4" style="max-width:400px; width:100%;">
            <h4 class="mb-3 text-center">Human Verification</h4>
            <p class="text-muted text-center">Please solve this simple challenge to continue.</p>
            <?php if (!empty($errorMsg)): ?>
                <div class="alert alert-danger text-center">
                    <?= htmlspecialchars($errorMsg, ENT_QUOTES, 'UTF-8'); ?>
                </div>
            <?php endif; ?>
            <form method="post">
                <input type="text" name="website" style="display:none">

                <input type="hidden" name="a" value="<?= $a; ?>">
                <input type="hidden" name="b" value="<?= $b; ?>">

                <div class="mb-3">
                    <label class="form-label fw-semibold">
                        What is <?= $a; ?> + <?= $b; ?>?
                    </label>
                    <input type="number" name="human_check" class="form-control" required>
                </div>

                <button class="btn btn-primary w-100">Verify</button>
            </form>
        </div>
    </body>
    </html>
    <?php
    exit;
}

// -------------------------------------------------------
// 4. Create base PDO connection
// -------------------------------------------------------
try {
    $pdo = new PDO($dsn, $db_user, $db_pass, $pdo_options);
} catch (PDOException $e) {
    die('Database connection failed.');
}

// -------------------------------------------------------
// 5. Load settings (single-row schema)
// -------------------------------------------------------
try {
    $stmt = $pdo->query("SELECT * FROM site_settings LIMIT 1");
    $settings = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$settings) {
        die('Settings table is empty.');
    }
} catch (Throwable $e) {
    die('Failed to load application settings.');
}

// -------------------------------------------------------
// 6. ADMIN SESSION TIMEOUT CHECK
// -------------------------------------------------------
if (!empty($_SESSION['admin']['logged_in'])) {
    $logoutPeriod = (int)($settings['logout_period'] ?? 0);

    if ($logoutPeriod > 0) {
        $maxAge    = $logoutPeriod * 60;
        $loginTime = $_SESSION['admin']['login_time'] ?? 0;

        if ($loginTime > 0 && (time() - $loginTime) > $maxAge) {
            unset($_SESSION['admin']);
            session_destroy();
            header('Location: /secure/logout.php?expired=1');
            exit;
        }
    }
}

// -------------------------------------------------------
// 7. Debug mode
// -------------------------------------------------------
$debugEnabled = isset($settings['base_debug']) && (int)$settings['base_debug'] === 1;

if ($debugEnabled) {
    ini_set('display_errors', '1');
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', '0');
    error_reporting(0);
}

// -------------------------------------------------------
// 8. Timezone
// -------------------------------------------------------
if (!empty($settings['base_tz'])) {
    date_default_timezone_set($settings['base_tz']);
} else {
    date_default_timezone_set('UTC');
}

// -------------------------------------------------------
// 9. APP_BASE (prod/dev switch)
// -------------------------------------------------------
$baseUrl = ((int)$settings['base_dev'] === 1)
    ? ($settings['base_url_dev'] ?? '')
    : ($settings['base_url_prod'] ?? '');

define('APP_BASE', rtrim($baseUrl, '/'));

// -------------------------------------------------------
// 10. APP_ENV
// -------------------------------------------------------
define('APP_ENV', $debugEnabled ? 'development' : 'production');

// -------------------------------------------------------
// 11. APP_NAME
// -------------------------------------------------------
define('APP_NAME', $settings['site_name'] ?? 'My Portfolio');
