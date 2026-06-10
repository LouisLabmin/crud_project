<?php
// ============================================================
// Page: Streets Lookup
// Description:
// Address lookup UI using OSM with country filtering.
// main/streets_lookup.php - UI page with search box and results
// ============================================================

declare(strict_types=1);

$pageTitle = "Street Lookup";

require_once __DIR__ . '/../configuration/bootstrap.php';
include_once __DIR__ . '/../includes/header.php';
?>

<div class="container py-4" style="max-width:900px;">

    <div class="text-center mb-4">
        <h2>📍 Address Lookup</h2>
        <p class="text-muted">Search for an address (country limited).</p>
    </div>

    <!-- COUNTRY -->
    <div class="card shadow-sm p-3 mb-3">
        <label class="form-label">Country</label>
        <select id="countrySelect" class="form-control">
            <option value="za" selected>South Africa</option>
            <option value="us">United States</option>
            <option value="gb">United Kingdom</option>
            <option value="au">Australia</option>
        </select>
    </div>

    <!-- SEARCH -->
    <div class="card shadow-sm p-3 mb-3">
        <input id="searchBox" class="form-control form-control-lg"
               placeholder="Search address..." autocomplete="off">
    </div>

    <!-- RESULTS -->
    <div class="card shadow-sm">
        <div class="card-header"><strong>Results</strong></div>

        <div id="results" class="p-3" style="max-height:500px; overflow-y:auto;">
            <div class="text-muted text-center py-3">
                🔍 Start typing...
            </div>
        </div>
    </div>

</div>

<script src="<?= APP_BASE ?>/shared/js/streets_lookup.js"></script>

<?php include_once __DIR__ . '/../includes/footer.php'; ?>