<?php
// ============================================================
// File: get_streets_data.php
// Description:
// AJAX endpoint for address lookup + optional save.
//
// FEATURES:
// - GET: Search addresses via OpenStreetMap (Nominatim)
// - Country filtering support
// - Normalize OSM response into structured format
// - POST: Save selected address to database
// - Duplicate prevention
// - Clean JSON responses
//
// Phase: R&D - Live lookup + controlled save
// ============================================================

declare(strict_types=1);

// ✅ Load full app (config + DB connection)
require_once __DIR__ . '/../../configuration/bootstrap.php';

// ✅ Always return JSON
header('Content-Type: application/json');

try {

    // ============================================================
    // ✅ HANDLE SAVE (POST)
    // ============================================================
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        $action = $_POST['action'] ?? '';

        if ($action === 'save') {

            $row = json_decode($_POST['data'] ?? '', true);

            if (!$row || !is_array($row)) {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Invalid data payload'
                ]);
                exit;
            }

            // ✅ Basic validation
            if (empty($row['str_name']) || empty($row['city'])) {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Missing required address fields'
                ]);
                exit;
            }

            // ----------------------------------------------------
            // DUPLICATE CHECK
            // ----------------------------------------------------
            $stmt = $pdo->prepare("
                SELECT id FROM str_address
                WHERE str_name = :str_name
                  AND city     = :city
                LIMIT 1
            ");

            $stmt->execute([
                ':str_name' => $row['str_name'],
                ':city'     => $row['city']
            ]);

            if (!$stmt->fetch()) {

                // ------------------------------------------------
                // INSERT DATA
                // ------------------------------------------------
                $insert = $pdo->prepare("
                    INSERT INTO str_address
                    (
                        str_number,
                        str_name,
                        suburb,
                        city,
                        province,
                        country,
                        latitude,
                        longitude,
                        osm_place_id
                    )
                    VALUES
                    (
                        :str_number,
                        :str_name,
                        :suburb,
                        :city,
                        :province,
                        :country,
                        :latitude,
                        :longitude,
                        :osm_place_id
                    )
                ");

                $insert->execute([
                    ':str_number'   => $row['str_number'] ?? '',
                    ':str_name'     => $row['str_name'],
                    ':suburb'       => $row['suburb'] ?? '',
                    ':city'         => $row['city'],
                    ':province'     => $row['province'] ?? '',
                    ':country'      => $row['country'] ?? '',
                    ':latitude'     => $row['latitude'] ?? null,
                    ':longitude'    => $row['longitude'] ?? null,
                    ':osm_place_id' => $row['osm_place_id'] ?? null
                ]);
            }

            echo json_encode([
                'status' => 'success',
                'message' => 'Address saved'
            ]);
            exit;
        }
    }

    // ============================================================
    // ✅ HANDLE SEARCH (GET)
    // ============================================================

    $q = trim($_GET['q'] ?? '');
    $country = strtolower(trim($_GET['country'] ?? 'za'));

    if ($q === '' || strlen($q) < 2) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Invalid search query'
        ]);
        exit;
    }

    // ============================================================
    // ✅ BUILD OSM REQUEST
    // ============================================================

    $query = urlencode($q);

    $url = "https://nominatim.openstreetmap.org/search"
         . "?q={$query}"
         . "&countrycodes={$country}"
         . "&format=json"
         . "&addressdetails=1"
         . "&limit=10";

    // ✅ Required by OSM policy
    $options = [
        "http" => [
            "header" => "User-Agent: GeoLookupApp/1.0 (admin@yourdomain.com)\r\n"
        ]
    ];

    $context = stream_context_create($options);

    // ============================================================
    // ✅ CALL OSM
    // ============================================================

    $response = @file_get_contents($url, false, $context);

    if ($response === false) {
        throw new Exception('OSM API request failed');
    }

    $osmData = json_decode($response, true);

    if (!is_array($osmData)) {
        throw new Exception('Invalid OSM response');
    }

    // ============================================================
    // ✅ NORMALIZE RESULTS
    // ============================================================

    $results = [];

    foreach ($osmData as $item) {

        $addr = $item['address'] ?? [];

        $results[] = [
            'osm_place_id' => $item['place_id'] ?? null,
            'display_name' => $item['display_name'] ?? '',

            'str_number' => $addr['house_number'] ?? '',
            'str_name'   => $addr['road'] ?? $addr['pedestrian'] ?? '',
            'suburb'     => $addr['suburb'] ?? $addr['neighbourhood'] ?? '',
            'city'       => $addr['city'] ?? $addr['town'] ?? $addr['village'] ?? '',
            'province'   => $addr['state'] ?? '',
            'country'    => $addr['country'] ?? '',

            'latitude'   => $item['lat'] ?? null,
            'longitude'  => $item['lon'] ?? null
        ];
    }

    // ============================================================
    // ✅ RETURN RESPONSE
    // ============================================================

    echo json_encode([
        'status' => 'success',
        'source' => 'osm',
        'count'  => count($results),
        'data'   => $results
    ]);

} catch (Throwable $e) {

    error_log('Street lookup error: ' . $e->getMessage());

    echo json_encode([
        'status' => 'error',
        'message' => 'Server error'
    ]);
}