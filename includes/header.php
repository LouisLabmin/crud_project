<?php

//includes/header.php
// This file is included at the top of every page. It sets up the HTML head, loads configuration,
// and renders the navigation bar. It also handles session management and provides a base URL for assets
declare(strict_types=1);

// ---------------------------------------------------------
// DEBUG MARKERS (optional)
// ---------------------------------------------------------
echo "<!-- HEADER START -->";
echo "<!-- PHP START -->";

// ---------------------------------------------------------
// Load bootstrap (absolute path, rewrite-proof)
// ---------------------------------------------------------
require_once __DIR__ . '/../configuration/bootstrap.php';

// ---------------------------------------------------------
// Start session safely
// ---------------------------------------------------------
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ---------------------------------------------------------
// Prevent undefined variable warnings
// ---------------------------------------------------------
$page_scripts = $page_scripts ?? [];
$redirectToMenu = $redirectToMenu ?? false;

// ---------------------------------------------------------
// Theme values (safe defaults)
// ---------------------------------------------------------
$primary = $config['theme_primary'] ?? '#0d6efd';
$text    = $config['theme_text']    ?? '#212529';
$bg      = $config['theme_bg']      ?? '#ffffff';

// ---------------------------------------------------------
// Base URL handling (bulletproof)
// ---------------------------------------------------------
$base = defined('APP_BASE') ? rtrim((string)APP_BASE, '/') : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($config['siteName'] ?? 'My Portfolio') ?></title>

    <!-- Core CSS -->
    <link rel="stylesheet" href="<?= $base ?>/vendor/bootstrap/css/bootstrap.min.css">

    <?php if (!empty($_SESSION['admin']['logged_in'])): ?>
        <link rel="stylesheet" href="<?= $base ?>/vendor/summernote/css/summernote.min.css">
    <?php endif; ?>

    <link rel="stylesheet" href="<?= $base ?>/css/stylesheet.php">

    <!-- Core JS -->
    <script src="<?= $base ?>/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <!-- Provide APP_BASE to JavaScript -->
    <script>
        window.APP_CONFIG = {
            base: "<?= $base ?>"
        };
    </script>

    <!-- Page-specific scripts -->
    <?php foreach ($page_scripts as $script): ?>
        <script src="<?= $base . $script ?>"></script>
    <?php endforeach; ?>
</head>

<body style="background-color: <?= $bg ?>; color: <?= $text ?>;">

<?php if (!empty($_SESSION['admin']['logged_in'])): ?>
    <div class="text-center py-2" style="background:#222; color:#fff;">
        <strong>ADMIN MODE ACTIVE</strong>
    </div>
<?php endif; ?>

<?php
// ---------------------------------------------------------
// PORTFOLIO MENU OVERRIDE
// If a page sets $redirectToMenu = true, load the portfolio menu
// and skip the normal navbar entirely.
// ---------------------------------------------------------
if ($redirectToMenu === true) {
    include __DIR__ . '/portfolio_menu.php';
    echo '<div class="container my-4">';
    return; // Stop header from rendering the normal navbar
}
?>

<nav class="navbar navbar-expand-lg" style="background-color: <?= $primary ?>;">
    <div class="container d-flex align-items-center">

        <a href="<?= $base ?>/index.php" class="me-3">
            <img src="<?= $base ?>/images/photo.jpg"
                 alt="Profile Photo"
                 style="height:50px; width:50px; object-fit:cover; border-radius:50%;">
        </a>

        <a class="navbar-brand fw-bold text-white" href="<?= $base ?>/index.php">
            <?= htmlspecialchars($config['siteName'] ?? 'My Portfolio') ?>
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                data-bs-target="#mainNavbar">
            <span class="navbar-toggler-icon" style="filter: invert(1);"></span>
        </button>

        <div class="collapse navbar-collapse" id="mainNavbar">
            <ul class="navbar-nav ms-auto mb-2 mb-lg-0">

                <li class="nav-item">
                    <a class="nav-link text-white fw-bold" href="<?= $base ?>/index.php">Home</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link text-white fw-bold" href="<?= $base ?>/public/about-me.php">About Me</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link text-white fw-bold" href="<?= $base ?>/public/contact.php">Contact</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link text-white fw-bold" href="<?= $base ?>/public/portfolio.php">Portfolio</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link text-white fw-bold" href="<?= $base ?>/public/projects.php">Projects</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link text-white fw-bold" href="<?= $base ?>/public/services.php">Professional Services</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link text-white fw-bold" href="<?= $base ?>/shared/register_check.php">Admin Scripts</a>
                </li>

             <?php if (empty($_SESSION['admin']['logged_in'])): ?>
                    <li class="nav-item">
                        <a class="nav-link text-white fw-bold" href="<?= $base ?>/secure/login.php">Admin</a>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link text-white fw-bold" href="<?= $base ?>/admin/index.php">Admin</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white fw-bold" href="<?= $base ?>/secure/logout.php">Logout</a>
                    </li>
                <?php endif; ?> 
                
            </ul>

            <a href="<?= $base ?>/index.php" class="ms-3">
                <img src="<?= $base ?>/images/logo.jpg"
                     alt="Logo"
                     style="height:50px; width:auto;">
            </a>

        </div>
    </div>
</nav>

<div class="container my-4">