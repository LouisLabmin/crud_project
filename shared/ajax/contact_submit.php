<?php
// shared/ajax/contact_submit.php
// This endpoint processes contact form submissions: validates input, checks CSRF, and inserts messages into the database.

declare(strict_types=1);

require_once __DIR__ . '/../../configuration/bootstrap.php';

header('Content-Type: application/json');

// Only POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error']);
    exit;
}

// CSRF
if (empty($_POST['csrf_token']) || $_POST['csrf_token'] !== ($_SESSION['csrf_token'] ?? '')) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
    exit;
}

// Honeypot
if (!empty($_POST['website'] ?? '')) {
    exit;
}

// Inputs
$name    = substr(trim($_POST['contact_name'] ?? ''), 0, 100);
$email   = substr(trim($_POST['contact_email'] ?? ''), 0, 120);
$number  = substr(trim($_POST['contact_number'] ?? ''), 0, 20);
$message = trim($_POST['contact_message'] ?? '');

// ✅ UPDATED VALIDATION
if (strlen($message) < 10) {
    echo json_encode(['status' => 'error', 'message' => 'Message must be at least 10 characters']);
    exit;
}

if (strlen($message) > 2000) {
    echo json_encode(['status' => 'error', 'message' => 'Message too long']);
    exit;
}

if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid email']);
    exit;
}

// Insert
$stmt = $pdo->prepare("
    INSERT INTO contact_form 
    (contact_name, email, contact_number, contact_message, created_at)
    VALUES (?, ?, ?, ?, NOW())
");

$stmt->execute([$name, $email, $number, $message]);

echo json_encode(['status' => 'success']);