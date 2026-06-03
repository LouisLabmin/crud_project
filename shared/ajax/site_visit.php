<?php
// shared/ajax/site_visit.php
// Returns analytics data as JSON

declare(strict_types=1);

require_once __DIR__ . '/../../configuration/bootstrap.php';

header('Content-Type: application/json');

try {

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
    $totalVisits      = count($visits);
    $uniqueVisitors   = count(array_unique(array_column($visits, 'visitor_ip')));

    // -------------------------------------------------------
    // AGGREGATION
    // -------------------------------------------------------
    $countryCounts = [];
    $deviceCounts  = [];
    $osCounts      = [];
    $browserCounts = [];

    foreach ($visits as $v) {

        $country = $v['country'] ?? 'Unknown';
        $device  = $v['device_type'] ?? 'Unknown';
        $os      = $v['operating_system'] ?? 'Unknown';

        $countryCounts[$country] = ($countryCounts[$country] ?? 0) + 1;
        $deviceCounts[$device]   = ($deviceCounts[$device] ?? 0) + 1;
        $osCounts[$os]           = ($osCounts[$os] ?? 0) + 1;

        // -------------------------------------------------------
        // SAFE BROWSER DETECTION ✅ (FIXED!)
        // -------------------------------------------------------
        $ua = strtolower($v['browser'] ?? '');

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

        // ✅ SAFE INCREMENT (CRITICAL FIX)
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
        'visits' => $visits
    ]);

} catch (Throwable $e) {

    // ✅ LOG THE ERROR
    error_log('site_visit API error: ' . $e->getMessage());

    // ✅ RETURN CLEAN JSON ERROR
    echo json_encode([
        'status' => 'error',
        'message' => 'Server error'
    ]);
}