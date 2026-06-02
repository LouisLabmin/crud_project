<?php
// shared/register.php
declare(strict_types=1);

require_once __DIR__ . '/../configuration/bootstrap.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/* -------------------------------------------------------
   LOAD HELPERS
------------------------------------------------------- */
require_once __DIR__ . '/../includes/security.php';
require_once __DIR__ . '/../includes/rate_limit.php';
require_once __DIR__ . '/../includes/validation.php';
require_once __DIR__ . '/../includes/helpers.php';

/* -------------------------------------------------------
   INITIALIZE CSRF
------------------------------------------------------- */
csrf_init();

/* -------------------------------------------------------
   DATABASE CONNECTION
------------------------------------------------------- */
if (!isset($pdo)) {
    if (isset($GLOBALS['pdo'])) $pdo = $GLOBALS['pdo'];
    elseif (isset($db))        $pdo = $db;
    elseif (isset($GLOBALS['db'])) $pdo = $GLOBALS['db'];
    else die("Database connection not available.");
}

/* -------------------------------------------------------
   PAGE-SPECIFIC JS
------------------------------------------------------- */
$page_scripts = ['/shared/js/public.js?v=' . time()];

/* -------------------------------------------------------
   INPUTS
------------------------------------------------------- */
$email   = trim($_POST['email'] ?? $_GET['email'] ?? '');
$surname = trim($_POST['surname'] ?? '');
$name    = trim($_POST['name'] ?? '');
$region  = trim($_POST['region'] ?? '');

$error = "";

$just_registered = isset($_GET['registered']) && $_GET['registered'] === '1';
$prefill_email   = trim($_GET['email'] ?? '');

/* -------------------------------------------------------
   FORM SUBMISSION
------------------------------------------------------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    /* Honeypot */
    if (!empty($_POST['website'])) {
        die("Invalid submission.");
    }

    /* Rate limiting (5 seconds) */
    if (!rate_limit('last_register', 5)) {
        $error = "Please wait a moment before trying again.";
    }

    /* CSRF */
    if ($error === '' && !csrf_validate($_POST['csrf_token'] ?? '')) {
        $error = 'Invalid form submission.';
    }

    /* Disclaimer */
    $disclaimer_read = (!empty($_POST['disclaimer_read']) && $_POST['disclaimer_read'] === 'on') ? 1 : 0;
    $read_date = $disclaimer_read ? date('Y-m-d H:i:s') : null;

    if ($error === '' && $disclaimer_read !== 1) {
        $error = 'You must read and accept the disclaimer before registering.';
    }

    /* Validation */
    if ($error === '') {

        if (!valid_email($email)) {
            $error = 'Invalid email address.';

        } elseif (!valid_name($surname) || !valid_name($name)) {
            $error = 'Invalid name or surname.';

        } else {
            try {
                /* Check if email exists */
                $stmt = $pdo->prepare("SELECT 1 FROM public_users WHERE email = :email LIMIT 1");
                $stmt->execute([':email' => $email]);
                $exists = $stmt->fetchColumn();

                if ($exists) {
                    header("Location: " . build_app_path("/shared/register.php?registered=1&email=" . urlencode($email)));
                    exit;
                }

                /* Insert new user */
                $stmt = $pdo->prepare(
                    "INSERT INTO public_users 
                     (email, surname, name, ip_address, region, disclaimer_read, read_date, created_at)
                     VALUES 
                     (:email, :surname, :name, :ip_address, :region, :disclaimer_read, :read_date, :created_at)"
                );

                $now = date('Y-m-d H:i:s');

                $ok = $stmt->execute([
                    ':email'           => $email,
                    ':surname'         => $surname,
                    ':name'            => $name,
                    ':ip_address'      => $_SERVER['REMOTE_ADDR'] ?? '',
                    ':region'          => $region,
                    ':disclaimer_read' => $disclaimer_read,
                    ':read_date'       => $read_date,
                    ':created_at'      => $now
                ]);

                if ($ok) {
                    header("Location: " . build_app_path("/shared/register.php?registered=1&email=" . urlencode($email)));
                    exit;
                }

                $error = 'Database insert failed.';

            } catch (Throwable $e) {
                $error = 'Server error. Please try again later.';
            }
        }
    }
}

/* -------------------------------------------------------
   LOAD HEADER
------------------------------------------------------- */
include_once __DIR__ . '/../includes/header.php';
?>

<main class="py-5">
    <h1 class="text-center mb-4">Register</h1>

    <div class="card shadow p-4 mx-auto" style="max-width: 700px;">

        <?php if ($just_registered): ?>
            <div class="alert alert-success">
                Registration successful for <strong><?= htmlspecialchars($prefill_email) ?></strong>.
            </div>

            <a href="<?= build_app_path('public/index.php') ?>" class="btn btn-success w-100 mb-3">
                Continue to Admin Scripts Page
            </a>

            <a href="<?= build_app_path('index.php') ?>" class="btn btn-primary w-100">
                Return to Home Page
            </a>

        <?php else: ?>

        <!-- FORM -->
        <form method="POST" action="<?= build_app_path('/shared/register.php') ?>">

            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

            <input type="text" name="website" style="display:none">

            <?php if ($error): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-semibold">Email Address</label>
                    <input type="email" name="email" class="form-control"
                           value="<?= htmlspecialchars($email) ?>"
                           required autocomplete="email">
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label fw-semibold">Region</label>
                    <input type="text" name="region" class="form-control"
                           value="<?= htmlspecialchars($region) ?>"
                           placeholder="e.g., Gauteng"
                           pattern="[A-Za-z\s\-]{2,50}">
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-semibold">Surname</label>
                    <input type="text" name="surname" class="form-control"
                           value="<?= htmlspecialchars($surname) ?>"
                           maxlength="255">
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label fw-semibold">Name</label>
                    <input type="text" name="name" class="form-control"
                           value="<?= htmlspecialchars($name) ?>"
                           maxlength="255">
                </div>
            </div>

            <div class="form-check mb-3">
                <input class="form-check-input" type="checkbox" id="disclaimer_read"
                       name="disclaimer_read" autocomplete="off" disabled>
                <label class="form-check-label fw-semibold" for="disclaimer_read">
                    I have read the Disclaimer
                </label>
            </div>

            <button type="button"
                    class="btn btn-outline-primary w-100 mb-3"
                    data-bs-toggle="modal"
                    data-bs-target="#disclaimerModal">
                View Disclaimer
            </button>

            <button type="submit" class="btn btn-primary w-100" id="register_btn" disabled>
                Register
            </button>

        </form>

        <?php endif; ?>

    </div>
</main>

<?php include __DIR__ . '/../includes/register_modal.php'; ?>

<?php include_once __DIR__ . '/../includes/footer.php'; ?>