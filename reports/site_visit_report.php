<?php
// site_visit_report.php - Displays analytics dashboard

$pageTitle = "Analytics";
include_once __DIR__ . '/../includes/header.php';
?>

<div class="container-fluid py-4 px-4">

    <!-- ===================== SUMMARY ===================== -->

    <div class="row g-4 justify-content-center mb-4">

        <div class="col-lg-3 text-center">
            <div class="card shadow-sm p-3">
                <small>Total</small>
                <h3 id="total_visits">0</h3>
            </div>
        </div>

        <div class="col-lg-3 text-center">
            <div class="card shadow-sm p-3">
                <small>Unique</small>
                <h3 id="unique_visits">0</h3>
            </div>
        </div>

        <div class="col-lg-3 text-center">
            <div class="card shadow-sm p-3">
                <small>Countries</small>
                <h3 id="country_count">0</h3>
            </div>
        </div>

    </div>

    <!-- ===================== CHARTS ===================== -->

    <div class="row g-4">

        <div class="col-lg-6">
            <div class="card shadow-sm h-100 p-3">
                <h6>🌍 Visitors by Country</h6>
                <div class="chart-container">
                    <canvas id="countryChart"></canvas>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card shadow-sm h-100 p-3">
                <h6>📱 Device Types</h6>
                <div class="chart-container">
                    <canvas id="deviceChart"></canvas>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card shadow-sm h-100 p-3">
                <h6>💻 Operating Systems</h6>
                <div class="chart-container">
                    <canvas id="osChart"></canvas>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card shadow-sm h-100 p-3">
                <h6>🌐 Browsers</h6>
                <div class="chart-container">
                    <canvas id="browserChart"></canvas>
                </div>
            </div>
        </div>

    </div>

    <!-- ===================== TABLE ===================== -->

    <div class="card shadow-sm mt-4">

        <div class="card-header">
            <input id="search" class="form-control form-control-sm" placeholder="Search...">
        </div>

        <div class="table-responsive">

            <table class="table table-sm table-hover mb-0">

                <thead class="table-light">
                    <tr>
                        <th class="sortable" data-col="date_visited">Date</th>
                        <th class="sortable" data-col="visitor_ip">IP</th>
                        <th class="sortable" data-col="country">Country</th>
                        <th>Device</th>
                        <th>OS</th>
                        <th>Browser</th>
                    </tr>
                </thead>

                <tbody id="visitTable">
                    <tr>
                        <td colspan="6" class="text-center text-muted">Loading data...</td>
                    </tr>
                </tbody>

            </table>

        </div>

        <div class="card-footer d-flex justify-content-between align-items-center">
            <small id="range" class="text-muted"></small>
            <div id="pagination"></div>
        </div>

    </div>

</div>

<script src="<?= APP_BASE ?>/shared/js/site_visit_report.js"></script>

<?php include_once __DIR__ . '/../includes/footer.php'; ?>