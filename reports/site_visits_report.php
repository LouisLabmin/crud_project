<?php
declare(strict_types=1);

require_once __DIR__ . '/../configuration/bootstrap.php';

// -------------------------------------------------------
// FETCH DATA
// -------------------------------------------------------
$stmt = $pdo->query("
    SELECT browser, visitor_ip, country, device_type, operating_system, date_visited
    FROM site_visits
    ORDER BY date_visited DESC
");

$visits = $stmt->fetchAll(PDO::FETCH_ASSOC);

// -------------------------------------------------------
// METRICS
// -------------------------------------------------------
$totalVisits = count($visits);
$uniqueVisitors = count(array_unique(array_column($visits, 'visitor_ip')));

// -------------------------------------------------------
// AGGREGATE
// -------------------------------------------------------
$countryCounts = [];
$deviceCounts  = [];
$osCounts      = [];
$browserCounts = [];

foreach ($visits as $v) {

    $country = $v['country'] ?: 'Unknown';
    $device  = $v['device_type'] ?: 'Unknown';
    $os      = $v['operating_system'] ?: 'Unknown';

    $countryCounts[$country] = ($countryCounts[$country] ?? 0) + 1;
    $deviceCounts[$device]   = ($deviceCounts[$device] ?? 0) + 1;
    $osCounts[$os]           = ($osCounts[$os] ?? 0) + 1;

    $ua = strtolower($v['browser'] ?? '');

    if (str_contains($ua, 'chrome')) $browserCounts['Chrome'] = ($browserCounts['Chrome'] ?? 0) + 1;
    elseif (str_contains($ua, 'firefox')) $browserCounts['Firefox'] = ($browserCounts['Firefox'] ?? 0) + 1;
    elseif (str_contains($ua, 'edge')) $browserCounts['Edge'] = ($browserCounts['Edge'] ?? 0) + 1;
    elseif (str_contains($ua, 'safari')) $browserCounts['Safari'] = ($browserCounts['Safari'] ?? 0) + 1;
    else $browserCounts['Other'] = ($browserCounts['Other'] ?? 0) + 1;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Site Analytics</title>

    <!-- ✅ jQuery FIXED -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- ✅ Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- ✅ CSS FIXED -->
    <link href="<?= APP_BASE ?>/css/stylesheet.php" rel="stylesheet">

    <!-- ✅ DATA CONFIG -->
    <script>
        window.SITE_REPORT = {
            countryLabels: <?= json_encode(array_keys($countryCounts)) ?>,
            countryValues: <?= json_encode(array_values($countryCounts)) ?>,
            deviceLabels: <?= json_encode(array_keys($deviceCounts)) ?>,
            deviceValues: <?= json_encode(array_values($deviceCounts)) ?>,
            osLabels: <?= json_encode(array_keys($osCounts)) ?>,
            osValues: <?= json_encode(array_values($osCounts)) ?>,
            browserLabels: <?= json_encode(array_keys($browserCounts)) ?>,
            browserValues: <?= json_encode(array_values($browserCounts)) ?>
        };
    </script>
</head>

<body class="bg-light">

<div class="container-fluid py-4 px-4">

    <h2 class="mb-4">📊 Site Analytics</h2>

    <!-- METRICS -->
    <div class="row g-4 mb-4">

        <div class="col-lg-3 col-md-6">
            <div class="card shadow-sm text-center p-3">
                <small>Total Visits</small>
                <h3><?= $totalVisits ?></h3>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="card shadow-sm text-center p-3">
                <small>Unique Visitors</small>
                <h3><?= $uniqueVisitors ?></h3>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="card shadow-sm text-center p-3">
                <small>Countries</small>
                <h3><?= count($countryCounts) ?></h3>
            </div>
        </div>

    </div>

    <!-- CHARTS -->
    <div class="row g-4 mb-4">

        <div class="col-lg-6">
            <div class="card shadow-sm p-3">
                <h6>Countries</h6>
                <canvas id="countryChart"></canvas>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card shadow-sm p-3">
                <h6>Devices</h6>
                <canvas id="deviceChart"></canvas>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card shadow-sm p-3">
                <h6>Operating Systems</h6>
                <canvas id="osChart"></canvas>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card shadow-sm p-3">
                <h6>Browsers</h6>
                <canvas id="browserChart"></canvas>
            </div>
        </div>

    </div>

    <!-- TABLE -->
    <div class="card shadow-sm">

        <div class="card-header">
            <input id="search" class="form-control form-control-sm" placeholder="Search...">
        </div>

        <div class="table-responsive" style="max-height:600px;">
            <table class="table table-sm table-hover mb-0">

                <thead class="table-light">
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
                <?php foreach ($visits as $v): ?>
                    <tr>
                        <td><?= htmlspecialchars($v['date_visited']) ?></td>
                        <td><?= htmlspecialchars($v['visitor_ip']) ?></td>
                        <td><?= htmlspecialchars($v['country']) ?></td>
                        <td><?= htmlspecialchars($v['device_type']) ?></td>
                        <td><?= htmlspecialchars($v['operating_system']) ?></td>
                        <td><?= htmlspecialchars($v['browser']) ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>

            </table>
        </div>

    </div>

</div>

<!-- ✅ JS FIXED -->
<script src="<?= APP_BASE ?>/shared/js/site_visit_report.js"></script>

</body>
</html>