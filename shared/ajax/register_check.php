<?php
// shared/register_check.php

declare(strict_types=1);

/* -------------------------------------------------------
   ERROR REPORTING (DEV)
------------------------------------------------------- */
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

/* -------------------------------------------------------
   LOAD CORE
------------------------------------------------------- */
require_once __DIR__ . '/../configuration/bootstrap.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/* -------------------------------------------------------
   CSRF TOKEN
------------------------------------------------------- */
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

/* -------------------------------------------------------
   INIT
------------------------------------------------------- */
$error = "";
$email = "";
$existing_user = false;

/* -------------------------------------------------------
   HANDLE POST
------------------------------------------------------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // CSRF CHECK
    if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'] ?? '')) {
        die('Invalid CSRF token');
    }

    $email = trim($_POST['email'] ?? '');

    if ($email === '') {
        $error = "Please enter your email address.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email address.";
    } else {
        try {

            if (!isset($pdo)) {
                throw new Exception("Database connection not available.");
            }

            $stmt = $pdo->prepare(
                "SELECT id, email FROM public_users WHERE email = :email LIMIT 1"
            );

            if ($stmt === false) {
                throw new Exception("Failed to prepare query.");
            }

            $stmt->execute([':email' => $email]);

            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                // Secure session storage
                $_SESSION['public_user'] = [
                    'id'        => $user['id'],
                    'email'     => $user['email'],
                    'logged_in' => true
                ];

                $existing_user = true;

            } else {
                // Small delay to reduce enumeration attacks
                usleep(500000);

                header("Location: register.php?email=" . urlencode($email));
                exit;
            }

        } catch (Throwable $e) {
            error_log($e->getMessage());
            $error = "Something went wrong. Please try again later.";
        }
    }
}

/* -------------------------------------------------------
   LINKS
------------------------------------------------------- */
$base_path   = defined('APP_BASE') ? APP_BASE : '';
$scripts_url = $base_path . "/public/index.php";
$index_url   = $base_path . "/index.php";

include_once __DIR__ . '/../includes/header.php';
?>

<main class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">

            <?php if ($existing_user): ?>
                <div class="card shadow border-0 p-4 text-center">
                    <div class="mb-3">
                        <i class="bi bi-check-circle-fill text-success" style="font-size: 3rem;"></i>
                    </div>

                    <h2 class="fw-bold">Welcome Back</h2>

                    <p class="text-muted">
                        The email <strong><?= htmlspecialchars($email) ?></strong> is recognized.
                    </p>

                    <hr>

                    <div class="d-grid gap-2">
                        <a href="<?= htmlspecialchars($scripts_url) ?>" class="btn btn-primary btn-lg fw-bold">
                            Continue to Scripts
                        </a>

                        <a href="<?= htmlspecialchars($index_url) ?>" class="btn btn-link text-decoration-none">
                            Return to Home
                        </a>
                    </div>
                </div>

            <?php else: ?>
                <div class="card shadow border-0">
                    <div class="card-header bg-primary text-white py-3">
                        <h4 class="mb-0">Access Scripts</h4>
                    </div>

                    <div class="card-body p-4">
                        <p class="text-muted">
                            Enter your registered email to access the tool library.
                        </p>

                        <?php if ($error): ?>
                            <div class="alert alert-danger border-0 shadow-sm">
                                <strong>Error:</strong> <?= htmlspecialchars($error) ?>
                            </div>
                        <?php endif; ?>

                        <form method="POST" action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>" novalidate>

                            <!-- CSRF -->
                            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

                            <div class="mb-4">
                                <label class="form-label fw-bold">Email Address</label>
                                <input
                                    type="email"
                                    name="email"
                                    class="form-control form-control-lg"
                                    placeholder="name@example.com"
                                    value="<?= htmlspecialchars($email) ?>"
                                    required
                                >
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary btn-lg fw-bold">
                                    Check Registration
                                </button>
                            </div>
                        </form>

                        <div class="mt-3 text-center">
                            <small class="text-muted">
                                Not registered? You’ll be redirected to create an account.
                            </small>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

        </div>
    </div>
</main>

<?php include_once __DIR__ . '/../includes/footer.php'; ?>