<?php
// main/index.php
// This is the contact form page. It uses Bootstrap for styling, submits via AJAX to shared/ajax/contact_submit.php,
// and logs visitors using shared/ajax/log_visit.php with a live visitor counter.

declare(strict_types=1);

require_once __DIR__ . '/../configuration/bootstrap.php';
include_once __DIR__ . '/../includes/header.php';

// -------------------------------------------------------
// CSRF TOKEN
// -------------------------------------------------------
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Form</title>

    <!-- ============================================================
         LIBRARIES
         ============================================================ -->

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <!-- Bootstrap -->
    <link 
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" 
        rel="stylesheet"
    >
    <script 
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
    ></script>

    <!-- ============================================================
         CUSTOM STYLES
         ============================================================ -->

    <link href="<?= APP_BASE ?>/css/stylesheet.php" rel="stylesheet">

    <!-- ============================================================
         APP CONFIG (for JS)
         ============================================================ -->

    <script>
        window.APP_CONFIG = {
            base: "<?= APP_BASE ?>"
        };
    </script>

</head>

<body class="bg-light">

<div class="container py-5">

    <!-- ============================================================
         CONTACT CARD
         ============================================================ -->

    <div class="card shadow-sm mx-auto" style="max-width: 600px;">
        <div class="card-body">

            <h3 class="mb-4 text-center">Contact Form</h3>

            <form id="contact_form" autocomplete="off">

                <!-- CSRF -->
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

                <!-- Name -->
                <div class="mb-3">
                    <input 
                        type="text" 
                        id="contact_name" 
                        class="form-control" 
                        placeholder="Name (optional)"
                    >
                </div>

                <!-- Email -->
                <div class="mb-3">
                    <input 
                        type="email" 
                        id="contact_email" 
                        class="form-control" 
                        placeholder="Email (optional)"
                    >
                </div>

                <!-- Number -->
                <div class="mb-3">
                    <input 
                        type="text" 
                        id="contact_number" 
                        class="form-control" 
                        placeholder="+27821234567"
                    >
                </div>

                <!-- Message -->
                <div class="mb-1">
                    <textarea 
                        id="contact_message" 
                        class="form-control" 
                        rows="4" 
                        maxlength="2000" 
                        required
                    ></textarea>
                </div>

                <!-- Character Counter -->
                <div id="char_count" class="text-end small text-muted mb-3">
                    2000 characters remaining
                </div>

                <!-- Honeypot -->
                <input type="text" id="website" style="display:none">

                <!-- Submit -->
                <button type="submit" class="btn btn-primary w-100">
                    Send Message
                </button>

                <!-- Status -->
                <div id="contact_status" class="mt-3"></div>

            </form>

        </div>
    </div>

    <!-- ============================================================
         VISITOR COUNTER
         ============================================================ -->

    <div class="text-center mt-3">
        You are visitor <strong id="visitor_count">0</strong>
    </div>

</div>

<!-- ============================================================
     SCRIPTS (LOAD LAST FOR PERFORMANCE)
     ============================================================ -->

<script src="<?= APP_BASE ?>/shared/js/contact_form.js"></script>
<script src="<?= APP_BASE ?>/shared/js/visitors.js"></script>


</body>
</html>
<?php
include_once __DIR__ . '/../includes/footer.php';
?>