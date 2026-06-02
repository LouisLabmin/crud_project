<?php

// shared/ajax/contact_report_list.php
// This script handles AJAX requests to fetch contact form messages based on their status (new, read, archived)
// It queries the database for messages matching the requested status and returns the results in JSON format.

declare(strict_types=1);

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../../configuration/bootstrap.php';
global $pdo;

header('Content-Type: application/json; charset=utf-8');

try {
    $status = strtolower(trim($_GET['status'] ?? 'new'));

    switch ($status) {
        case 'read':
            $where = 'WHERE read_msg = 1 AND archived_msg = 0';
            break;
        case 'archived':
            $where = 'WHERE archived_msg = 1';
            break;
        case 'all':
            $where = '';
            break;
        case 'new':
        default:
            $where = 'WHERE new_msg = 1';
            break;
    }

    $sql = "SELECT *
            FROM contact_form
            $where
            ORDER BY created_at DESC";

    $stmt = $pdo->query($sql);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['status' => 'success', 'data' => $rows]);
    exit;

} catch (Throwable $e) {
    error_log('contact_report_list error: ' . $e->getMessage());
    echo json_encode(['status' => 'error', 'message' => 'Unable to load messages.']);
    exit;
}