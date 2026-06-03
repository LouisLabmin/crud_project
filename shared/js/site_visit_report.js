// shared/js/site_visit_report.js
// Handles site visit report: load data, build charts, filter table.

$(function () {

    const BASE = window.APP_CONFIG.base;
    let charts = {};

    // ============================================================
    // LOAD DASHBOARD DATA
    // ============================================================
    function loadDashboard() {

        $.get(BASE + '/shared/ajax/site_visit.php', function (res) {

            if (!res || res.status !== 'success') {
                console.error('Dashboard load failed', res);
                alert('Failed to load data');
                return;
            }

            // ----------------------------------
            // SUMMARY
            // ----------------------------------
            $('#total_visits').text(res.summary.total || 0);
            $('#unique_visits').text(res.summary.unique || 0);
            $('#country_count').text(res.summary.countries || 0);

            // ----------------------------------
            // BUILD UI
            // ----------------------------------
            buildCharts(res.charts || {});
            buildTable(res.visits || []);

        }, 'json')
        .fail(function (xhr, status, error) {
            console.error('AJAX error:', status, error);
            alert('Server error loading dashboard');
        });
    }

    // ============================================================
    // BUILD TABLE
    // ============================================================
    function buildTable(rows) {

        const $table = $('#visitTable');
        $table.empty();

        if (!rows.length) {
            $table.html(`
                <tr>
                    <td colspan="6" class="text-center text-muted">
                        No data available
                    </td>
                </tr>
            `);
            return;
        }

        rows.forEach(v => {

            $table.append(`
                <tr>
                    <td>${safe(v.date_visited)}</td>
                    <td>${safe(v.visitor_ip)}</td>
                    <td>${safe(v.country)}</td>
                    <td>${safe(v.device_type)}</td>
                    <td>${safe(v.operating_system)}</td>
                    <td>${safe(v.browser)}</td>
                </tr>
            `);
        });
    }

    // ============================================================
    // BUILD CHARTS
    // ============================================================
    function buildCharts(data) {

        function makeChart(id, type, obj = {}) {

            const labels = Object.keys(obj);
            const values = Object.values(obj);

            // Destroy existing chart (important!)
            if (charts[id]) {
                charts[id].destroy();
            }

            charts[id] = new Chart(document.getElementById(id), {
                type: type,
                data: {
                    labels: labels,
                    datasets: [{
                        data: values
                    }]
                }
            });
        }

        makeChart('countryChart', 'pie', data.country);
        makeChart('deviceChart', 'doughnut', data.device);
        makeChart('osChart', 'bar', data.os);
        makeChart('browserChart', 'bar', data.browser);
    }

    // ============================================================
    // SEARCH FILTER
    // ============================================================
    $('#search').on('keyup', function () {

        const value = $(this).val().toLowerCase();

        $('#visitTable tr').filter(function () {
            $(this).toggle($(this).text().toLowerCase().includes(value));
        });
    });

    // ============================================================
    // SIMPLE XSS PROTECTION
    // ============================================================
    function safe(str) {
        return (str || '')
            .toString()
            .replace(/[&<>"']/g, function (m) {
                return ({
                    '&': '&amp;',
                    '<': '&lt;',
                    '>': '&gt;',
                    '"': '&quot;',
                    "'": '&#039;'
                })[m];
            });
    }

    // ============================================================
    // INIT
    // ============================================================
    loadDashboard();

    // OPTIONAL: AUTO REFRESH
    // setInterval(loadDashboard, 15000);

});