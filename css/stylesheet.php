<?php
// css/stylesheet.php
// Generates global CSS styles using dynamic theme values from configuration.

declare(strict_types=1);

require_once __DIR__ . '/../configuration/bootstrap.php';

header("Content-Type: text/css");

// Fallbacks (safe defaults)
$primary = $config['theme_primary'] ?? '#0d6efd';
$text    = $config['theme_text'] ?? '#212529';
$bg      = $config['theme_bg'] ?? '#ffffff';
?>
/* ============================================================
   GLOBAL
   ============================================================ */

* {
    font-family: "Segoe UI", "Segoe UI Variable", Tahoma, Geneva, Verdana, sans-serif;
    font-size: 18px;
}

body {
    background-color: <?= $bg ?>;
    color: <?= $text ?>;
    line-height: 1.5;
}

/* Container sizing */
.container {
    max-width: 1200px;
}

/* Smooth UI feel */
a,
button,
.btn,
.nav-link {
    transition: 0.2s ease-in-out;
}

/* ============================================================
   BUTTONS
   ============================================================ */

.btn-primary {
    background-color: <?= $primary ?>;
    border-color: <?= $primary ?>;
}

.btn-primary:hover {
    background-color: <?= $primary ?>cc; /* slight transparency */
}

/* ============================================================
   FORM ELEMENTS
   ============================================================ */

input,
textarea {
    font-family: inherit;
    font-size: inherit;
}

/* Character counter */
#char_count {
    font-size: 14px;
    color: <?= $text ?>;
    opacity: 0.7;
    text-align: right;
}

/* ============================================================
   STATUS FEEDBACK
   ============================================================ */

.text-danger {
    color: #dc3545 !important;
}

.text-success {
    color: #198754 !important;
}