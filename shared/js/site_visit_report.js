// site_visit_report.js
// Handles loading dashboard data, charts, table, pagination, sorting, search

console.log("site_visit_report.js LOADED ✅");

$(function () {

    const BASE = window.APP_CONFIG.base;

    let page = 1;
    let total = 0;
    const limit = 10;

    let sortCol = 'date_visited';
    let sortDir = 'desc';
    let search = '';

    let charts = {}; // store Chart.js instances

    // ============================================================
    // LOAD DATA
    // ============================================================
    function load() {

        showLoading();

        $.get(BASE + '/shared/ajax/site_visit.php', {
            page,
            limit,
            sort_col: sortCol,
            sort_dir: sortDir,
            search
        }, function (res) {

            console.log("API RESPONSE:", res);

            if (!res || res.status !== 'success') {
                showError("Failed to load data");
                return;
            }

            total = res.pagination?.total || 0;

            // ----------------------------------
            // SUMMARY
            // ----------------------------------
            $('#total_visits').text(res.summary?.total ?? 0);
            $('#unique_visits').text(res.summary?.unique ?? 0);
            $('#country_count').text(res.summary?.countries ?? 0);

            // ----------------------------------
            // TABLE
            // ----------------------------------
            renderTable(res.visits || []);

            // ----------------------------------
            // PAGINATION + RANGE
            // ----------------------------------
            renderPagination();
            renderRange();

            // ✅ CRITICAL FIX (YOU WERE MISSING THIS)
            renderCharts(res.charts || {});

        }, 'json')
        .fail(function () {
            showError("Server error");
        });
    }

    // ============================================================
    // TABLE
    // ============================================================
    function renderTable(rows) {

        const $t = $('#visitTable');
        $t.empty();

        if (!rows.length) {
            $t.html(`<tr><td colspan="6" class="text-center text-muted">No data found</td></tr>`);
            return;
        }

        rows.forEach(r => {
            $t.append(`
                <tr>
                    <td>${safe(r.date_visited)}</td>
                    <td>${safe(r.visitor_ip)}</td>
                    <td>${safe(r.country)}</td>
                    <td>${safe(r.device_type)}</td>
                    <td>${safe(r.operating_system)}</td>
                    <td>${safe(r.browser)}</td>
                </tr>
            `);
        });
    }

    // ============================================================
    // PAGINATION
    // ============================================================
    function renderPagination() {

        const pages = Math.ceil(total / limit);
        const $p = $('#pagination');
        $p.empty();

        if (pages <= 1) return;

        $p.append(`
            <button class="btn btn-sm btn-outline-primary me-1"
                ${page === 1 ? 'disabled' : ''} id="prev">Prev</button>
        `);

        for (let i = Math.max(1, page - 2); i <= Math.min(pages, page + 2); i++) {
            $p.append(`
                <button class="btn btn-sm ${i === page ? 'btn-primary' : 'btn-outline-primary'} mx-1 pageBtn"
                    data-p="${i}">
                    ${i}
                </button>
            `);
        }

        $p.append(`
            <button class="btn btn-sm btn-outline-primary ms-1"
                ${page === pages ? 'disabled' : ''} id="next">Next</button>
        `);

        // EVENTS
        $('#prev').off().click(() => { page--; load(); });
        $('#next').off().click(() => { page++; load(); });

        $('.pageBtn').off().click(function () {
            page = $(this).data('p');
            load();
        });
    }

    // ============================================================
    // RANGE
    // ============================================================
    function renderRange() {

        if (total === 0) {
            $('#range').text('');
            return;
        }

        const start = (page - 1) * limit + 1;
        const end = Math.min(page * limit, total);

        $('#range').text(`Showing ${start}–${end} of ${total}`);
    }

    // ============================================================
    // CHARTS ✅ (FULL FIX)
    // ============================================================
    function renderCharts(data) {

        console.log("CHART DATA:", data);

        if (!data) return;

        buildChart('countryChart', 'pie', data.country);
        buildChart('deviceChart', 'doughnut', data.device);
        buildChart('osChart', 'bar', data.os);
        buildChart('browserChart', 'bar', data.browser);
    }

    function buildChart(id, type, obj = {}) {

        const labels = Object.keys(obj);
        const values = Object.values(obj);

        const ctx = document.getElementById(id);

        if (!ctx) return;

        // ✅ destroy old chart
        if (charts[id]) {
            charts[id].destroy();
        }

        charts[id] = new Chart(ctx, {
            type: type,
            data: {
                labels: labels,
                datasets: [{
                    data: values
                }]
            },
            options: {
                maintainAspectRatio: false
            }
        });
    }

    // ============================================================
    // SORTING
    // ============================================================
    $('.sortable').on('click', function () {

        const col = $(this).data('col');
        if (!col) return;

        sortDir = (sortCol === col && sortDir === 'asc') ? 'desc' : 'asc';
        sortCol = col;
        page = 1;

        load();
    });

    // ============================================================
    // SEARCH (DEBOUNCED)
    // ============================================================
    let timer;

    $('#search').on('keyup', function () {

        clearTimeout(timer);

        timer = setTimeout(() => {
            search = $(this).val();
            page = 1;
            load();
        }, 300);
    });

    // ============================================================
    // UTILITIES
    // ============================================================

    function showLoading() {
        $('#visitTable').html(`
            <tr><td colspan="6" class="text-center text-muted">Loading...</td></tr>
        `);
    }

    function showError(msg) {
        $('#visitTable').html(`
            <tr><td colspan="6" class="text-center text-danger">${msg}</td></tr>
        `);
    }

    function safe(val) {
        return (val ?? '').toString()
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;");
    }

    // ============================================================
    // INIT
    // ============================================================
    load();

});