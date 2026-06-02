<?php
// shared/ajax/log_visit.php
// Logs visitor information (IP, device, OS, country) into the site_visits table.

declare(strict_types=1);

require_once __DIR__ . '/../../configuration/bootstrap.php';
require_once __DIR__ . '/../../vendor/autoload.php';

header('Content-Type: application/json');

try {
    $visitor_ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    $userAgent  = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';

    // Device detection
    $deviceType = 'Desktop';
    if (preg_match('/mobile|android|iphone/i', $userAgent)) {
        $deviceType = 'Mobile';
    }

    // OS detection
    $os = 'Unknown';
    if (preg_match('/windows/i', $userAgent)) $os = 'Windows';

    // GeoIP (safe)
    $country = 'Unknown';
    $dbPath = __DIR__ . '/../geoip/GeoLite2-Country/GeoLite2-Country.mmdb';

    if (file_exists($dbPath)) {
        try {
            $reader = new \GeoIp2\Database\Reader($dbPath);
            $record = $reader->country($visitor_ip);
            $country = $record->country->name ?? 'Unknown';
        } catch (Throwable $e) {
            error_log("GeoIP error: " . $e->getMessage());
        }
    }

    // Insert
    $stmt = $pdo->prepare("
        INSERT INTO site_visits
        (browser, date_visited, visitor_ip, country, device_type, operating_system)
        VALUES (?, NOW(), ?, ?, ?, ?)
    ");

    $stmt->execute([
        $userAgent,
        $visitor_ip,
        $country,
        $deviceType,
        $os
    ]);

    echo json_encode(['status' => 'success']);

} catch (Throwable $e) {
    error_log("log_visit error: " . $e->getMessage());

    echo json_encode([
        'status' => 'error'
    ]);
}