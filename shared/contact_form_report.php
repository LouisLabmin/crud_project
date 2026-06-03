<?php
// shared/contact_form_report.php
// Displays contact messages in a clean admin report interface with table + detail panel.

declare(strict_types=1);

require_once __DIR__ . '/../configuration/bootstrap.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Report</title>

    <!-- ============================================================
         LIBRARIES
         ============================================================ -->

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- ============================================================
         CUSTOM CSS
         ============================================================ -->

    <link href="<?= APP_BASE ?>/css/stylesheet.php" rel="stylesheet">

    <!-- ============================================================
         JS CONFIG
         ============================================================ -->

    <script>
        window.APP_CONFIG = {
            base: "<?= APP_BASE ?>"
        };
    </script>
</head>

<body class="bg-light">

<div class="container py-4">

    <!-- ============================================================
         PAGE HEADER
         ============================================================ -->

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="mb-0">Contact Report</h3>

        <a href="<?= APP_BASE ?>/main/index.php" class="btn btn-outline-danger btn-sm">
            Close
        </a>
    </div>

    <!-- ============================================================
         FILTERS
         ============================================================ -->

    <div class="mb-3">
        <div class="btn-group" role="group">
            <button class="btn btn-outline-primary filter-btn" data-status="new">New</button>
            <button class="btn btn-outline-secondary filter-btn" data-status="read">Read</button>
            <button class="btn btn-outline-warning filter-btn" data-status="archived">Archived</button>
            <button class="btn btn-outline-dark filter-btn" data-status="all">All</button>
        </div>
    </div>

    <!-- ============================================================
         MAIN GRID
         ============================================================ -->

    <div class="row g-3">

        <!-- ============================================================
             LEFT: MESSAGE LIST
             ============================================================ -->

        <div class="col-md-6">
            <div class="card shadow-sm h-100">

                <div class="card-header">
                    Message List
                </div>

                <div class="card-body p-0">

                    <table class="table table-hover table-sm mb-0" id="contact_table">

                        <thead class="table-light">
                            <tr>
                                <th style="width:70px;">ID</th>
                                <th>Name</th>
                                <th>Email</th>
                            </tr>
                        </thead>

                        <tbody>
                            <tr>
                                <td colspan="3" class="text-center text-muted">
                                    Loading messages...
                                </td>
                            </tr>
                        </tbody>

                    </table>

                </div>

            </div>
        </div>

        <!-- ============================================================
             RIGHT: MESSAGE DETAIL
             ============================================================ -->

        <div class="col-md-6">
            <div class="card shadow-sm h-100 d-flex flex-column">

                <div class="card-header">
                    Message Details
                </div>

                <div class="card-body" id="detail_box">

                    <p class="text-muted mb-0">
                        Select a message from the table to view details
                    </p>

                </div>

                <div class="card-footer d-flex gap-2">
                    <button class="btn btn-success btn-sm" id="btn_read" disabled>
                        Mark Read
                    </button>
                    <button class="btn btn-warning btn-sm" id="btn_archive" disabled>
                        Archive
                    </button>
                </div>

            </div>
        </div>

    </div>

</div>

<!-- ============================================================
     SCRIPTS
     ============================================================ -->

<script src="<?= APP_BASE ?>/shared/js/contact_report.js"></script>

</body>
</html>