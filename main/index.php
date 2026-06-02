<?php
// main/index.php
// This is the contact form page. It uses Bootstrap for styling and submits via AJAX to shared/ajax/contact_submit.php.

declare(strict_types=1);

require_once __DIR__ . '/../configuration/bootstrap.php';

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>

<!DOCTYPE html>
<html>


<head>
    <title>Contact Form</title>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <!-- ✅ Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- ✅ Custom CSS -->
    <link href="<?= APP_BASE ?>/css/stylesheet.php" rel="stylesheet">

    <!-- ✅ App Config -->
    <script>
        window.APP_CONFIG = {
            base: "<?= APP_BASE ?>"
        };
    </script>

    <!-- ✅ Contact JS -->
    <script src="<?= APP_BASE ?>/shared/js/contact_form.js"></script>
</head>




<body class="bg-light">

<div class="container py-5">

    <div class="card shadow-sm mx-auto" style="max-width: 600px;">
        <div class="card-body">

            <h3 class="mb-4 text-center">Contact Form</h3>

            <form id="contact_form">

                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

                <div class="mb-3">
                    <input type="text" id="contact_name" class="form-control" placeholder="Name (optional)">
                </div>

                <div class="mb-3">
                    <input type="email" id="contact_email" class="form-control" placeholder="Email (optional)">
                </div>

                <div class="mb-3">
                    <input type="text" id="contact_number" class="form-control" placeholder="+27821234567">
                </div>

                <div class="mb-1">
                    <textarea id="contact_message" class="form-control" rows="4" maxlength="2000" required></textarea>
                </div>

                <div id="char_count" class="text-end small text-muted mb-3">
                    2000 characters remaining
                </div>

                <!-- Honeypot -->
                <input type="text" id="website" style="display:none">

                <button type="submit" class="btn btn-primary w-100">Send Message</button>

                <div id="contact_status" class="mt-3"></div>

            </form>

        </div>
    </div>

</div>

</body>
</html>
``