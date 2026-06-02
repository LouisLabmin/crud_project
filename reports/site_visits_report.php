<?php
// reports/site_visits_report.php
require_once __DIR__ . '/../configuration/bootstrap.php';

// ---------------------------------------------
// FETCH RAW DATA
// ---------------------------------------------
$stmt = $pdo->query("
    SELECT browser, visitor_ip, country, device_type, operating_system, date_visited
    FROM site_visits
    ORDER BY date_visited DESC
");
$visits = $stmt->fetchAll(PDO::FETCH_ASSOC);

// ---------------------------------------------
// SUMMARY METRICS
// ---------------------------------------------
$totalVisits = count($visits);
$uniqueIPs = count(array_unique(array_column($visits, 'visitor_ip')));

// Count by country
$countryCounts = [];
$deviceCounts = [];
$osCounts = [];
$browserCounts = [];

foreach ($visits as $v) {
    $countryCounts[$v['country']] = ($countryCounts[$v['country']] ?? 0) + 1;
    $deviceCounts[$v['device_type']] = ($deviceCounts[$v['device_type']] ?? 0) + 1;
    $osCounts[$v['operating_system']] = ($osCounts[$v['operating_system']] ?? 0) + 1;

    // Simplify browser name from user-agent
    $ua = strtolower($v['browser']);
    if (strpos($ua, 'chrome') !== false) $browserCounts['Chrome'] = ($browserCounts['Chrome'] ?? 0) + 1;
    elseif (strpos($ua, 'firefox') !== false) $browserCounts['Firefox'] = ($browserCounts['Firefox'] ?? 0) + 1;
    elseif (strpos($ua, 'safari') !== false) $browserCounts['Safari'] = ($browserCounts['Safari'] ?? 0) + 1;
    elseif (strpos($ua, 'edge') !== false) $browserCounts['Edge'] = ($browserCounts['Edge'] ?? 0) + 1;
    else $browserCounts['Other'] = ($browserCounts['Other'] ?? 0) + 1;
}

// Prepare JSON for charts
$countryLabels = json_encode(array_keys($countryCounts));
$countryValues = json_encode(array_values($countryCounts));

$deviceLabels = json_encode(array_keys($deviceCounts));
$deviceValues = json_encode(array_values($deviceCounts));

$osLabels = json_encode(array_keys($osCounts));
$osValues = json_encode(array_values($osCounts));

$browserLabels = json_encode(array_keys($browserCounts));
$browserValues = json_encode(array_values($browserCounts));
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Site Visit Analytics</title>
    <link rel="stylesheet" href="/vendor/bootstrap/css/bootstrap.min.css">
    <script src="/vendor/jquery/jquery.min.js"></script>
    <script src="/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>

    </style>
</head>

<body class="p-4">

<a href="/reports/index.php" 
   class="btn btn-secondary position-fixed" 
   style="top: 20px; right: 20px; z-index: 999;">
   Close
</a>

<h1 class="mb-4">📊 Site Visit Analytics Dashboard</h1>

<!-- SUMMARY CARDS -->
<div class="row mb-4">
    <div class="col-md-4">
        <div class="card shadow p-3">
            <h4>Total Visits</h4>
            <p class="display-6"><?= $totalVisits ?></p>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card shadow p-3">
            <h4>Unique Visitors</h4>
            <p class="display-6"><?= $uniqueIPs ?></p>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card shadow p-3">
            <h4>Countries</h4>
            <p class="display-6"><?= count($countryCounts) ?></p>
        </div>
    </div>
</div>

<!-- CHARTS -->
<div class="row mb-4">
    <div class="col-md-6">
        <div class="card shadow p-3">
            <h5>Visitors by Country</h5>
            <canvas id="countryChart"></canvas>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card shadow p-3">
            <h5>Device Types</h5>
            <canvas id="deviceChart"></canvas>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-6">
        <div class="card shadow p-3">
            <h5>Operating Systems</h5>
            <canvas id="osChart"></canvas>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card shadow p-3">
            <h5>Browsers</h5>
            <canvas id="browserChart"></canvas>
        </div>
    </div>
</div>

<!-- TABLE -->
<div class="card shadow p-3">
    <h4>All Visits</h4>
    <input type="text" id="search" class="form-control mb-3" placeholder="Search IP, country, OS, device...">

    <div class="table-container">
        <table class="table table-striped table-hover">
            <thead class="table-dark">
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
                    <td><?= $v['date_visited'] ?></td>
                    <td><?= $v['visitor_ip'] ?></td>
                    <td><?= $v['country'] ?></td>
                    <td><?= $v['device_type'] ?></td>
                    <td><?= $v['operating_system'] ?></td>
                    <td><?= htmlspecialchars($v['browser']) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
// -------------------------
// SEARCH FILTER
// -------------------------
$("#search").on("keyup", function() {
    let value = $(this).val().toLowerCase();
    $("#visitTable tr").filter(function() {
        $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
    });
});

// -------------------------
// CHARTS
// -------------------------
new Chart(document.getElementById('countryChart'), {
    type: 'pie',
    data: {
        labels: <?= $countryLabels ?>,
        datasets: [{ data: <?= $countryValues ?>, backgroundColor: ['#007bff','#28a745','#ffc107','#dc3545','#6f42c1','#20c997'] }]
    }
});

new Chart(document.getElementById('deviceChart'), {
    type: 'doughnut',
    data: {
        labels: <?= $deviceLabels ?>,
        datasets: [{ data: <?= $deviceValues ?>, backgroundColor: ['#17a2b8','#ffc107','#6c757d'] }]
    }
});

new Chart(document.getElementById('osChart'), {
    type: 'bar',
    data: {
        labels: <?= $osLabels ?>,
        datasets: [{ data: <?= $osValues ?>, backgroundColor: '#007bff' }]
    }
});

new Chart(document.getElementById('browserChart'), {
    type: 'bar',
    data: {
        labels: <?= $browserLabels ?>,
        datasets: [{ data: <?= $browserValues ?>, backgroundColor: '#28a745' }]
    }
});
</script>

</body>
</html>
