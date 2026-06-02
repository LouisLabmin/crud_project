<?php
// shared/ajax/contact_report_update.php
declare(strict_types=1);

require_once __DIR__ . '/../../configuration/bootstrap.php';
global $pdo;

header('Content-Type: application/json; charset=utf-8');

try {
    $id     = (int)($_POST['id'] ?? 0);
    $action = $_POST['action'] ?? '';

    if ($id <= 0) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid message ID.']);
        exit;
    }

    if ($action === 'mark_read') {
        $sql = "UPDATE contact_form
                SET new_msg = 0, read_msg = 1
                WHERE id = :id";
    } elseif ($action === 'archive') {
        $sql = "UPDATE contact_form
                SET new_msg = 0, read_msg = 1, archived_msg = 1
                WHERE id = :id";
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Unknown action.']);
        exit;
    }

    $stmt = $pdo->prepare($sql);
    $stmt->execute([':id' => $id]);

    echo json_encode(['status' => 'success']);
    exit;

} catch (Throwable $e) {
    error_log('contact_report_update error: ' . $e->getMessage());
    echo json_encode(['status' => 'error', 'message' => 'Unable to update message.']);
    exit;
}