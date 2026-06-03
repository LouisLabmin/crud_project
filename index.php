<?php
// index.php
// Main landing page with navigation and welcome section

declare(strict_types=1);

require_once __DIR__ . '/configuration/bootstrap.php';

$pageTitle = "Welcome to My App";
$showWelcome = true;

include_once __DIR__ . '/includes/header.php';
?>


<!-- ============================================================
     MAIN CONTENT
     ============================================================ -->

<div class="container">

    <div class="text-center mb-4">
        <h2 class="mb-2">Welcome 👋</h2>
        <p class="text-muted">
            This application demonstrates a full-stack PHP system including
            contact forms, analytics tracking, and reporting dashboards.
        </p>
    </div>

    <!-- ============================================================
         FEATURE CARDS
         ============================================================ -->

    <div class="row g-4">

        <!-- Contact Form -->
        <div class="col-md-4">
            <div class="card shadow-sm h-100 text-center">

                <div class="card-body">
                    <h5 class="card-title">📩 Contact Form</h5>

                    <p class="card-text text-muted">
                        Submit and manage user messages with validation and security.
                    </p>

                    <a href="<?= APP_BASE ?>/main/index.php" class="btn btn-primary btn-sm">
                        Open
                    </a>
                </div>

            </div>
        </div>

        <!-- Site Visit Report -->
        <div class="col-md-4">
            <div class="card shadow-sm h-100 text-center">

                <div class="card-body">
                    <h5 class="card-title">📊 Site Visit Report</h5>

                    <p class="card-text text-muted">
                        View visitor analytics including browser, device, and location data.
                    </p>

                    <a href="<?= APP_BASE ?>/reports/site_visit_report.php" class="btn btn-success btn-sm">
                        View Report
                    </a>
                </div>

            </div>
        </div>

        <!-- Analytics Report -->
        <div class="col-md-4">
            <div class="card shadow-sm h-100 text-center">

                <div class="card-body">
                    <h5 class="card-title">📈 Contact Analytics</h5>

                    <p class="card-text text-muted">
                        Manage contact messages, mark as read, and review submission history.
                    </p>

                    <a href="<?= APP_BASE ?>/shared/contact_form_report.php" class="btn btn-warning btn-sm">
                        View Analytics
                    </a>
                </div>

            </div>
        </div>

    </div>

</div>

<?php
include_once __DIR__ . '/includes/footer.php';
?>
