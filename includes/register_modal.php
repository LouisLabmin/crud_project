<?php
// includes/register_modal.php
// ⭐ This file is included inside shared/register.php
// It contains ONLY the HTML for the disclaimer modal.
?>

<!-- DISCLAIMER MODAL -->
<div class="modal fade" id="disclaimerModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title">Website Disclaimer</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body" id="disclaimer_modal_body"
           style="max-height: 400px; overflow-y: scroll; overflow-x: hidden; padding-right: 15px;">
        <?php include __DIR__ . '/../public/disclaimer.php'; ?>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
            I Agree and Accept the Disclaimer
        </button>
      </div>

    </div>
  </div>
</div>