<?php
// index.php
//echo "<pre>";
//print_r($_SERVER);
//echo "</pre>";
//die(); 

require_once __DIR__ . '/configuration/bootstrap.php';

$pageTitle = "Welcome";
include_once __DIR__ . '/includes/header.php';

?>

<!-- HERO SLIDESHOW -->
<div id="homeCarousel" class="carousel slide carousel-fade" data-bs-ride="carousel">

    <div class="carousel-indicators">
        <button type="button" data-bs-target="#homeCarousel" data-bs-slide-to="0" class="active"></button>
        <button type="button" data-bs-target="#homeCarousel" data-bs-slide-to="1"></button>
        <button type="button" data-bs-target="#homeCarousel" data-bs-slide-to="2"></button>
        <button type="button" data-bs-target="#homeCarousel" data-bs-slide-to="3"></button>
    </div>

    <div class="carousel-inner">

        <div class="carousel-item active">
            <img src="<?= APP_BASE ?>/images/slide_dashboards.jpg" class="d-block w-100 home-slide-img" alt="Dashboards">
            <div class="carousel-caption d-none d-md-block">
                <h2>Basic Dashboards</h2>
                <p>Clear, simple, and effective reporting visuals.</p>
            </div>
        </div>

        <div class="carousel-item">
            <img src="<?= APP_BASE ?>/images/slide_excel.jpg" class="d-block w-100 home-slide-img" alt="Excel Reporting">
            <div class="carousel-caption d-none d-md-block">
                <h2>Excel Reporting</h2>
                <p>Data cleanup, formatting, and structured reporting.</p>
            </div>
        </div>

        <div class="carousel-item">
            <img src="<?= APP_BASE ?>/images/slide_powershell.jpg" class="d-block w-100 home-slide-img" alt="PowerShell">
            <div class="carousel-caption d-none d-md-block">
                <h2>PowerShell & Scripting</h2>
                <p>Automation, batch tasks, and command-line solutions.</p>
            </div>
        </div>

        <div class="carousel-item">
            <img src="<?= APP_BASE ?>/images/slide_services.jpg" class="d-block w-100 home-slide-img" alt="Professional Services">
            <div class="carousel-caption d-none d-md-block">
                <h2>Professional Services</h2>
                <p>Administrative, technical, and community support.</p>
            </div>
        </div>

    </div>

    <button class="carousel-control-prev" type="button" data-bs-target="#homeCarousel" data-bs-slide="prev">
        <span class="carousel-control-prev-icon"></span>
    </button>

    <button class="carousel-control-next" type="button" data-bs-target="#homeCarousel" data-bs-slide="next">
        <span class="carousel-control-next-icon"></span>
    </button>

</div>

