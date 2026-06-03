<?php
// reports/site_visit_report.php
// AJAX-driven analytics dashboard

declare(strict_types=1);

require_once __DIR__ . '/../configuration/bootstrap.php';
include_once __DIR__ . '/../includes/header.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Site Analytics</title>

    <!-- ✅ jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <!-- ✅ Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- ✅ Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- ✅ APP CONFIG -->
    <script>
        window.APP_CONFIG = {
            base: "<?= APP_BASE ?>"
        };
    </script>
</head>

<body class="bg-light">

<div class="container-fluid py-4 px-4">

    <!-- ============================================================
         HEADER
         ============================================================ -->

    <div class="mb-4 text-center">
        <h2 class="mb-1">📊 Site Analytics</h2>
        <small class="text-muted">Real-time visitor insights</small>
    </div>

    <!-- ============================================================
         SUMMARY CARDS (CENTERED ✅)
         ============================================================ -->

    <div class="row g-4 mb-4 justify-content-center">

        <div class="col-lg-3 col-md-4 col-sm-6">
            <div class="card shadow-sm text-center p-3 h-100 mx-auto">
                <small class="text-muted">Total Visits</small>
                <h3 id="total_visits" class="fw-bold">0</h3>
            </div>
        </div>

        <div class="col-lg-3 col-md-4 col-sm-6">
            <div class="card shadow-sm text-center p-3 h-100 mx-auto">
                <small class="text-muted">Unique Visitors</small>
                <h3 id="unique_visits" class="fw-bold">0</h3>
            </div>
        </div>

        <div class="col-lg-3 col-md-4 col-sm-6">
            <div class="card shadow-sm text-center p-3 h-100 mx-auto">
                <small class="text-muted">Countries</small>
                <h3 id="country_count" class="fw-bold">0</h3>
            </div>
        </div>

    </div>

    <!-- ============================================================
         CHARTS
         ============================================================ -->

    <div class="row g-4 mb-4">

        <div class="col-lg-6">
            <div class="card shadow-sm h-100 p-3">
                <h6 class="mb-3">🌍 Visitors by Country</h6>
                <canvas id="countryChart"></canvas>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card shadow-sm h-100 p-3">
                <h6 class="mb-3">📱 Device Types</h6>
                <canvas id="deviceChart"></canvas>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card shadow-sm h-100 p-3">
                <h6 class="mb-3">💻 Operating Systems</h6>
                <canvas id="osChart"></canvas>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card shadow-sm h-100 p-3">
                <h6 class="mb-3">🌐 Browsers</h6>
                <canvas id="browserChart"></canvas>
            </div>
        </div>

    </div>

    <!-- ============================================================
         TABLE
         ============================================================ -->

    <div class="card shadow-sm">

        <div class="card-header d-flex justify-content-between align-items-center">
            <strong>Visit Log</strong>

            <input
                id="search"
                class="form-control form-control-sm w-auto"
                placeholder="Search..."
                style="min-width: 250px;"
            >
        </div>

        <div class="table-responsive" style="max-height:600px;">

            <table class="table table-sm table-hover mb-0">

                <thead class="table-light sticky-top">
                    <tr>
                        <th>Date</th>
                        <th>IP</th>
                        <th>Country</th>
                        <th>Device</th>
                        <th>OS</th>
                        <th>Browser</th>
                    </tr>
                </thead>

                <tbody id="visitTable">
                    <tr>
                        <td colspan="6" class="text-center text-muted">
                            Loading data...
                        </td>
                    </tr>
                </tbody>

            </table>

        </div>

    </div>

</div>

<!-- ✅ AJAX JS -->
<script src="<?= APP_BASE ?>/shared/js/site_visit_report.js"></script>


</body>
</html>

<?php
include_once __DIR__ . '/../includes/footer.php';
?>