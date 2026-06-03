// shared/js/contact_report.js
// Handles contact report functionality: load messages, view details, update status.

console.log("CONTACT REPORT JS LOADED ✅");

(function () {
    'use strict';

    $(function () {

        // ============================================================
        // CONFIG
        // ============================================================

        const BASE = window.APP_CONFIG.base;

        const $tableBody = $('#contact_table tbody');
        const $detail    = $('#detail_box');
        const $btnRead   = $('#btn_read');
        const $btnArch   = $('#btn_archive');

        let selectedId = null;
        let currentStatus = 'new';

        // ============================================================
        // LOAD MESSAGES
        // ============================================================

        function loadMessages(status = 'new') {

            currentStatus = status;
            selectedId = null;

            clearDetail();

            $tableBody.html(`
                <tr>
                    <td colspan="3" class="text-center text-muted">Loading...</td>
                </tr>
            `);

            $.get(BASE + '/shared/ajax/contact_report_list.php', { status }, function (res) {

                if (res.status !== 'success') {
                    showTableError("Failed to load messages");
                    return;
                }

                if (!res.data || !res.data.length) {
                    showTableError("No messages found", true);
                    return;
                }

                $tableBody.empty();

                res.data.forEach(row => {

                    const isUnread = row.read_msg == 0;

                    const tr = $(`
                        <tr class="${isUnread ? 'table-warning' : ''}" data-id="${row.id}">
                            <td>${row.id}</td>
                            <td>${row.contact_name || ''}</td>
                            <td>${row.email || ''}</td>
                        </tr>
                    `);

                    tr.data('row', row);
                    $tableBody.append(tr);
                });

            }, 'json')
            .fail(function () {
                showTableError("Server error");
            });
        }

        // ============================================================
        // TABLE ERROR / EMPTY
        // ============================================================

        function showTableError(message, muted = false) {
            $tableBody.html(`
                <tr>
                    <td colspan="3" class="text-center ${muted ? 'text-muted' : 'text-danger'}">
                        ${message}
                    </td>
                </tr>
            `);
        }

        // ============================================================
        // CLEAR DETAIL
        // ============================================================

        function clearDetail() {
            $detail.html(`
                <p class="text-muted mb-0">
                    Select a message from the table to view details
                </p>
            `);

            $btnRead.prop('disabled', true);
            $btnArch.prop('disabled', true);
        }

        // ============================================================
        // SHOW DETAILS
        // ============================================================

        function showDetail(row) {

            $detail.html(`
                <div class="mb-3">
                    <strong>Name</strong>
                    <div class="border rounded p-2 bg-light">${row.contact_name || '-'}</div>
                </div>

                <div class="mb-3">
                    <strong>Email</strong>
                    <div class="border rounded p-2 bg-light">${row.email || '-'}</div>
                </div>

                <div class="mb-3">
                    <strong>Contact Number</strong>
                    <div class="border rounded p-2 bg-light">${row.contact_number || '-'}</div>
                </div>

                <div class="mb-3">
                    <strong>Message</strong>
                    <div class="border rounded p-2 bg-light" style="max-height:200px; overflow:auto;">
                        ${row.contact_message || ''}
                    </div>
                </div>
            `);

            $btnRead.prop('disabled', row.read_msg == 1);
            $btnArch.prop('disabled', row.archived_msg == 1);
        }

        // ============================================================
        // ROW CLICK
        // ============================================================

        $tableBody.on('click', 'tr', function () {

            const row = $(this).data('row');
            if (!row) return;

            selectedId = row.id;

            $('#contact_table tr').removeClass('table-active');
            $(this).addClass('table-active');

            showDetail(row);
        });

        // ============================================================
        // FILTER BUTTONS
        // ============================================================

        $('.filter-btn').on('click', function () {
            loadMessages($(this).data('status'));
        });

        // ============================================================
        // UPDATE MESSAGE
        // ============================================================

        function updateMessage(action) {

            if (!selectedId) return;

            $.post(BASE + '/shared/ajax/contact_report_update.php', {
                id: selectedId,
                action: action
            }, function (res) {

                if (res.status === 'success') {
                    loadMessages(currentStatus);
                    clearDetail();
                } else {
                    alert(res.message || 'Update failed');
                }

            }, 'json')
            .fail(function () {
                alert('Server error updating message');
            });
        }

        // Buttons
        $btnRead.on('click', () => updateMessage('mark_read'));
        $btnArch.on('click', () => updateMessage('archive'));

        // ============================================================
        // INIT
        // ============================================================

        loadMessages();

    });

})();