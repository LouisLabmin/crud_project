<?php
// shared/ajax/get_visit_count.php
// Returns total number of visits as JSON

declare(strict_types=1);

require_once __DIR__ . '/../../configuration/bootstrap.php';

header('Content-Type: application/json');

try {
    $stmt = $pdo->query("SELECT COUNT(*) AS total FROM site_visits");
    $count = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

    echo json_encode([
        'status' => 'success',
        'total'  => (int)$count
    ]);

} catch (Throwable $e) {
    error_log("get_visit_count error: " . $e->getMessage());

    echo json_encode([
        'status' => 'error',
        'total'  => 0
    ]);
}