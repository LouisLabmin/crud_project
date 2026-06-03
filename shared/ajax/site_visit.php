<?php
// shared/ajax/site_visit.php
// Returns analytics data as JSON

declare(strict_types=1);

require_once __DIR__ . '/../../configuration/bootstrap.php';

header('Content-Type: application/json');

try {

    // -------------------------------------------------------
    // INPUT PARAMETERS (PAGINATION / SORT / SEARCH)
    // -------------------------------------------------------

    $page  = max(1, (int)($_GET['page'] ?? 1));
    $limit = max(1, (int)($_GET['limit'] ?? 10));
    $offset = ($page - 1) * $limit;

    $search = trim($_GET['search'] ?? '');

    $sortCol = $_GET['sort_col'] ?? 'date_visited';
    $sortDir = (strtolower($_GET['sort_dir'] ?? 'desc') === 'asc') ? 'ASC' : 'DESC';

    // ✅ SAFE COLUMN WHITELIST
    $allowedCols = [
        'date_visited',
        'visitor_ip',
        'country',
        'device_type',
        'operating_system',
        'browser'
    ];

    if (!in_array($sortCol, $allowedCols, true)) {
        $sortCol = 'date_visited';
    }

    // -------------------------------------------------------
    // SEARCH CONDITION BUILDER ✅
    // -------------------------------------------------------

    $where = '';
    $params = [];

    if ($search !== '') {
        $where = "
            WHERE visitor_ip LIKE :search
               OR country LIKE :search
               OR device_type LIKE :search
               OR operating_system LIKE :search
               OR browser LIKE :search
        ";

        $params[':search'] = "%$search%";
    }

    // -------------------------------------------------------
    // TOTAL COUNT (MATCHES DATA QUERY ✅)
    // -------------------------------------------------------

    $countStmt = $pdo->prepare("
        SELECT COUNT(*) 
        FROM site_visits
        $where
    ");

    $countStmt->execute($params);
    $totalRows = (int)$countStmt->fetchColumn();

    // -------------------------------------------------------
    // FETCH PAGINATED DATA
    // -------------------------------------------------------

    $stmt = $pdo->prepare("
        SELECT 
            browser,
            visitor_ip,
            country,
            device_type,
            operating_system,
            date_visited
        FROM site_visits
        $where
        ORDER BY $sortCol $sortDir
        LIMIT :limit OFFSET :offset
    ");

    // Bind dynamic params
    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value, PDO::PARAM_STR);
    }

    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

    $stmt->execute();
    $visits = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // -------------------------------------------------------
    // SUMMARY METRICS
    // -------------------------------------------------------

    $totalVisits = $totalRows;

    $uniqueStmt = $pdo->query("
        SELECT COUNT(DISTINCT visitor_ip) 
        FROM site_visits
    ");

    $uniqueVisitors = (int)$uniqueStmt->fetchColumn();

    // -------------------------------------------------------
    // CHART AGGREGATION (OPTIMIZED ✅)
    // -------------------------------------------------------

    // ✅ Countries
    $countryCounts = [];
    $stmt = $pdo->query("SELECT country, COUNT(*) c FROM site_visits GROUP BY country");

    foreach ($stmt as $row) {
        $countryCounts[$row['country'] ?: 'Unknown'] = (int)$row['c'];
    }

    // ✅ Devices
    $deviceCounts = [];
    $stmt = $pdo->query("SELECT device_type, COUNT(*) c FROM site_visits GROUP BY device_type");

    foreach ($stmt as $row) {
        $deviceCounts[$row['device_type'] ?: 'Unknown'] = (int)$row['c'];
    }

    // ✅ OS
    $osCounts = [];
    $stmt = $pdo->query("SELECT operating_system, COUNT(*) c FROM site_visits GROUP BY operating_system");

    foreach ($stmt as $row) {
        $osCounts[$row['operating_system'] ?: 'Unknown'] = (int)$row['c'];
    }

    // ✅ Browser (still parsed safely)
    $browserCounts = [];

    $stmt = $pdo->query("SELECT browser FROM site_visits");

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

        $ua = strtolower($row['browser'] ?? '');

        if (strpos($ua, 'chrome') !== false) {
            $browser = 'Chrome';
        } elseif (strpos($ua, 'firefox') !== false) {
            $browser = 'Firefox';
        } elseif (strpos($ua, 'edge') !== false) {
            $browser = 'Edge';
        } elseif (strpos($ua, 'safari') !== false) {
            $browser = 'Safari';
        } else {
            $browser = 'Other';
        }

        $browserCounts[$browser] = ($browserCounts[$browser] ?? 0) + 1;
    }

    // -------------------------------------------------------
    // RESPONSE
    // -------------------------------------------------------

    echo json_encode([

        'status' => 'success',

        'summary' => [
            'total'     => $totalVisits,
            'unique'    => $uniqueVisitors,
            'countries' => count($countryCounts)
        ],

        'charts' => [
            'country' => $countryCounts,
            'device'  => $deviceCounts,
            'os'      => $osCounts,
            'browser' => $browserCounts
        ],

        'visits' => $visits,

        // ✅ PAGINATION BLOCK
        'pagination' => [
            'page'  => $page,
            'limit' => $limit,
            'total' => $totalRows
        ]

    ]);

} catch (Throwable $e) {

    error_log('site_visit API error: ' . $e->getMessage());

    echo json_encode([
        'status' => 'error',
        'message' => 'Server error'
    ]);
}
