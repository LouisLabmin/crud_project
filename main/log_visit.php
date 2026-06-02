<?php
// main/log_visit.php
// This script is called via AJAX on every page load to log visitor information such as IP address


declare(strict_types=1);

require_once __DIR__ . '/../configuration/bootstrap.php';
require_once __DIR__ . '/../vendor/autoload.php';

use GeoIp2\Database\Reader;

try {
    $visitor_ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    $userAgent  = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';

    // -----------------------------
    // DEVICE TYPE DETECTION
    // -----------------------------
    if (preg_match('/mobile|iphone|android/i', $userAgent)) {
        $deviceType = 'Mobile';
    } elseif (preg_match('/ipad|tablet/i', $userAgent)) {
        $deviceType = 'Tablet';
    } else {
        $deviceType = 'Desktop';
    }

    // -----------------------------
    // OS DETECTION
    // -----------------------------
    if (preg_match('/windows nt/i', $userAgent)) {
        $os = 'Windows';
    } elseif (preg_match('/macintosh|mac os x/i', $userAgent)) {
        $os = 'macOS';
    } elseif (preg_match('/linux/i', $userAgent)) {
        $os = 'Linux';
    } elseif (preg_match('/android/i', $userAgent)) {
        $os = 'Android';
    } elseif (preg_match('/iphone|ipad/i', $userAgent)) {
        $os = 'iOS';
    } else {
        $os = 'Unknown';
    }

    // -----------------------------
    // COUNTRY DETECTION (LOCAL GEOIP)
    // -----------------------------
    $dbPath = __DIR__ . '/geoip/GeoLite2-Country/GeoLite2-Country.mmdb';

    $country = 'Unknown';

    if (file_exists($dbPath)) {
        $reader = new Reader($dbPath);
        try {
            $record = $reader->country($visitor_ip);
            $country = $record->country->name ?? 'Unknown';
        } catch (Exception $e) {
            $country = 'Unknown';
        }
    }

    // -----------------------------
    // INSERT INTO DATABASE
    // -----------------------------
    $stmt = $pdo->prepare("
        INSERT INTO site_visits (browser, date_visited, visitor_ip, country, device_type, operating_system)
        VALUES (?, NOW(), ?, ?, ?, ?)
    ");
    $stmt->execute([$userAgent, $visitor_ip, $country, $deviceType, $os]);

    echo "OK";

} catch (Throwable $e) {
    error_log("VISITOR LOG ERROR: " . $e->getMessage());
    http_response_code(500);
    echo "ERROR";
}
