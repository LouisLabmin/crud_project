<?php
// shared/contact_form_report.php
// This is the main page for viewing contact form messages in the admin panel. 
// It includes a table to display messages based on their status (new, read, archived) and a detail panel to view message content.


declare(strict_types=1);

require_once __DIR__ . '/../configuration/bootstrap.php';

$page_title   = 'Contact Form Report';
$page_scripts = ['/shared/js/contact_report.js'];
$page_styles  = ['/css/stylesheet.php'];

include_once __DIR__ . '/../includes/header.php';
?>

<main class="container py-4">
    <h1 class="mb-4">Contact Form Report</h1>

<!-- Filters -->
<div class="mb-3 d-flex flex-wrap gap-2">
    <button class="btn btn-outline-primary btn-sm filter-btn" type="button" title="New Contacts" data-status="new">New</button>
    <button class="btn btn-outline-secondary btn-sm filter-btn" type="button" title="Read Messages" data-status="read">Read</button>
    <button class="btn btn-outline-warning btn-sm filter-btn" type="button" title="Archived Messages" data-status="archived">Archived</button>
    <button class="btn btn-outline-dark btn-sm filter-btn" type="button" title="All Messages" data-status="all">All</button>

    <!-- Close Button (styled as a button, but works as a link) -->
    <a href="/reports/index.php" class="btn btn-outline-danger btn-sm" title="Close Report">Close</a>
</div>


    <div class="row">
        <!-- Grid -->
        <div class="col-md-7 mb-3">
            <div class="card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>Messages</span>
                    <small class="text-muted" id="contact_report_status_text">Showing new messages</small>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-sm mb-0" id="contact_report_table">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Created</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Filled by JS -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detail panel -->
        <div class="col-md-5 mb-3">
            <div class="card shadow-sm h-100">
                <div class="card-header">
                    Message Details
                </div>
                <div class="card-body" id="contact_report_detail">
                    <p class="text-muted mb-0">Select a message from the table to view details.</p>
                </div>
                <div class="card-footer d-flex gap-2">
                    <button class="btn btn-success btn-sm" id="btn_mark_read" disabled>Mark As Read</button>
                    <button class="btn btn-warning btn-sm" id="btn_archive" disabled>Archive</button>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include_once __DIR__ . '/../includes/footer.php'; ?>
