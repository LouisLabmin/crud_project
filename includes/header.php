<?php
// includes/header.php

declare(strict_types=1);

require_once __DIR__ . '/../configuration/bootstrap.php';

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?= $pageTitle ?? 'My CRUD App' ?></title>

    <!-- ============================================================
         CSS
         ============================================================ -->

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?= APP_BASE ?>/assets/css/style.css" rel="stylesheet">

    <!-- ============================================================
         JS (ESSENTIAL)
         ============================================================ -->

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- ============================================================
         GLOBAL CONFIG
         ============================================================ -->

    <script>
        window.APP_CONFIG = {
            base: "<?= APP_BASE ?>"
        };
    </script>

</head>

<body class="bg-light">

<!-- ============================================================
     NAVBAR ✅ (NOW GLOBAL)
     ============================================================ -->

<nav class="navbar navbar-expand-lg shadow-sm mb-4" style="background-color:#3498db;">
    <div class="container-fluid">

        <!-- Brand -->
        <a class="navbar-brand text-white fw-bold" href="<?= APP_BASE ?>/index.php">
            My CRUD App
        </a>

        <!-- Mobile toggle -->
        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Links -->
        <div class="collapse navbar-collapse" id="mainNav">
            <ul class="navbar-nav ms-auto">

                <li class="nav-item">
                    <a class="nav-link text-white" title="Home Page" href="<?= APP_BASE ?>/index.php">
                        Home Page
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link text-white" title="Contact Form" href="<?= APP_BASE ?>/main/index.php">
                        Contact Form
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link text-white" title="Site Visit Report" href="<?= APP_BASE ?>/reports/site_visit_report.php">
                        Site Visit Report
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link text-white" title="Analytics Report" href="<?= APP_BASE ?>/shared/contact_form_report.php">
                        Analytics Report
                    </a>
                </li>

            </ul>
        </div>

    </div>
</nav>

<!-- ============================================================
     OPTIONAL HERO / WELCOME SECTION
     ============================================================ -->

<div class="container py-4 text-center">

    <h3 class="mb-3">Welcome to My CRUD App</h3>

    <p class="text-muted">
        This is a simple CRUD (Create, Read, Update, Delete) application
        built with PHP, Bootstrap, and AJAX-powered dashboards.
    </p>

</div>