// shared/js/contact_report.js
// This JavaScript file manages the contact report page in the admin panel. It handles loading messages based on their status (new, read, archived),
// displaying message details when a row is clicked, and providing buttons to mark messages as read or archived. 
// The script uses AJAX to fetch message data from the server and update message statuses without needing to reload the page.

console.log("CONTACT REPORT JS LOADED");

(function () {
    'use strict';

    $(function () {

        const $tableBody = $('#contact_report_table tbody');
        const $statusText = $('#contact_report_status_text');
        const $detail = $('#contact_report_detail');
        const $btnMarkRead = $('#btn_mark_read');
        const $btnArchive = $('#btn_archive');

        let currentStatus = 'new';
        let selectedId = null;
        let selectedRowData = null;

        function buildUrl(path) {
            return window.APP_CONFIG.base + path;
        }

        function renderDetail(row) {
            if (!row) {
                $detail.html('<p class="text-muted mb-0">Select a message from the table to view details.</p>');
                $btnMarkRead.prop('disabled', true);
                $btnArchive.prop('disabled', true);
                return;
            }

            const html = `
                <dl class="row mb-0">
                    <dt class="col-sm-4">ID</dt>
                    <dd class="col-sm-8">${row.id}</dd>

                    <dt class="col-sm-4">Created</dt>
                    <dd class="col-sm-8">${row.created_at}</dd>

                    <dt class="col-sm-4">Name</dt>
                    <dd class="col-sm-8">${row.contact_name}</dd>

                    <dt class="col-sm-4">Email</dt>
                    <dd class="col-sm-8">${row.email}</dd>

                    <dt class="col-sm-4">Number</dt>
                    <dd class="col-sm-8">${row.contact_number}</dd>

                    <dt class="col-sm-4">IP Address</dt>
                    <dd class="col-sm-8">${row.ip_address}</dd>

                    <dt class="col-sm-4">Message</dt>
                    <dd class="col-sm-8"><pre class="contact-report-message">${row.contact_message}</pre></dd>
                </dl>
            `;
            $detail.html(html);

            $btnMarkRead.prop('disabled', row.read_msg == 1);
            $btnArchive.prop('disabled', row.archived_msg == 1);
        }

        function loadMessages(status) {
            currentStatus = status;
            selectedId = null;
            selectedRowData = null;
            renderDetail(null);

            let label = 'Showing new messages';
            if (status === 'read') label = 'Showing read messages';
            if (status === 'archived') label = 'Showing archived messages';
            if (status === 'all') label = 'Showing all messages';

            $statusText.text(label);

            $.ajax({
                url: buildUrl('/shared/ajax/contact_report_list.php'),
                method: 'GET',
                data: { status },
                dataType: 'json'
            })
                .done(function (res) {
                    if (res.status !== 'success') {
                        $tableBody.html('<tr><td colspan="7" class="text-center text-danger">Unable to load messages.</td></tr>');
                        return;
                    }

                    const rows = res.data;

                    if (!rows.length) {
                        $tableBody.html('<tr><td colspan="7" class="text-center text-muted">No messages found.</td></tr>');
                        return;
                    }

                    $tableBody.empty();

                    rows.forEach(row => {
                        const tr = $('<tr></tr>')
                            .attr('data-id', row.id)
                            .data('row', row);

                        tr.append(`<td>${row.id}</td>`);
                        tr.append(`<td>${row.created_at}</td>`);
                        tr.append(`<td>${row.contact_name}</td>`);
                        tr.append(`<td>${row.email}</td>`);
                      
                        $tableBody.append(tr);
                    });
                })
                .fail(function () {
                    $tableBody.html('<tr><td colspan="7" class="text-center text-danger">Server error loading messages.</td></tr>');
                });
        }

        $tableBody.on('click', 'tr', function () {
            const row = $(this).data('row');

            $tableBody.find('tr').removeClass('table-active');
            $(this).addClass('table-active');

            selectedId = row.id;
            selectedRowData = row;
            renderDetail(row);
        });

        $('.filter-btn').on('click', function () {
            loadMessages($(this).data('status'));
        });

        function updateMessage(action) {
            if (!selectedId) return;

            $.ajax({
                url: buildUrl('/shared/ajax/contact_report_update.php'),
                method: 'POST',
                data: { id: selectedId, action },
                dataType: 'json'
            })
                .done(function (res) {
                    if (res.status === 'success') {
                        loadMessages(currentStatus);
                    } else {
                        alert(res.message);
                    }
                })
                .fail(function () {
                    alert('Server error updating message.');
                });
        }

        $('#btn_mark_read').on('click', () => updateMessage('mark_read'));
        $('#btn_archive').on('click', () => updateMessage('archive'));

        loadMessages('new');
    });

})();