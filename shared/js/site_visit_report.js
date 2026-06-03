// site_visit_report.js
// Dashboard controller with gradient-style charts

$(function () {

    const BASE = window.APP_CONFIG.base;

    let page = 1;
    let total = 0;
    const limit = 10;

    let sortCol = 'date_visited';
    let sortDir = 'desc';
    let search = '';

    let charts = {};

    // ============================================================
    // LOAD DATA
    // ============================================================
    function load() {

        showLoading();

        $.get(BASE + '/shared/ajax/site_visit.php', {
            page, limit, sort_col: sortCol, sort_dir: sortDir, search
        }, function (res) {

            if (!res || res.status !== 'success') {
                showError("Failed to load data");
                return;
            }

            total = res.pagination?.total || 0;

            $('#total_visits').text(res.summary?.total ?? 0);
            $('#unique_visits').text(res.summary?.unique ?? 0);
            $('#country_count').text(res.summary?.countries ?? 0);

            renderTable(res.visits || []);
            renderPagination();
            renderRange();
            renderCharts(res.charts || {});

        }, 'json')
        .fail(() => showError("Server error"));
    }

    // ============================================================
    // TABLE
    // ============================================================
    function renderTable(rows) {

        const $t = $('#visitTable').empty();

        if (!rows.length) {
            $t.html(`<tr><td colspan="6" class="text-center text-muted">No data</td></tr>`);
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
        const $p = $('#pagination').empty();

        if (pages <= 1) return;

        $p.append(`<button class="btn btn-sm btn-outline-primary" ${page===1?'disabled':''} id="prev">Prev</button>`);

        for (let i=Math.max(1,page-2); i<=Math.min(pages,page+2); i++) {
            $p.append(`<button class="btn btn-sm ${i===page?'btn-primary':'btn-outline-primary'} pageBtn" data-p="${i}">${i}</button>`);
        }

        $p.append(`<button class="btn btn-sm btn-outline-primary" ${page===pages?'disabled':''} id="next">Next</button>`);

        $('#prev').off().click(()=>{ page--; load(); });
        $('#next').off().click(()=>{ page++; load(); });
        $('.pageBtn').off().click(function(){ page=$(this).data('p'); load(); });
    }

    // ============================================================
    // RANGE
    // ============================================================
    function renderRange() {

        if (total === 0) {
            $('#range').text('');
            return;
        }

        const start = (page-1)*limit + 1;
        const end = Math.min(page*limit, total);

        $('#range').text(`Showing ${start}–${end} of ${total}`);
    }

    // ============================================================
    // CHARTS WITH GRADIENT STYLE ✅
    // ============================================================
    function renderCharts(data) {

        buildChart('countryChart', 'pie', data.country);
        buildChart('deviceChart', 'doughnut', data.device);
        buildChart('osChart', 'bar', data.os);
        buildChart('browserChart', 'bar', data.browser);
    }

    function buildChart(id, type, obj = {}) {

        const labels = Object.keys(obj);
        const values = Object.values(obj);

        const canvas = document.getElementById(id);
        if (!canvas) return;

        const ctx = canvas.getContext('2d');

        if (charts[id]) charts[id].destroy();

        const gradients = generateGradients(ctx, values.length);

        charts[id] = new Chart(ctx, {
            type: type,
            data: {
                labels: labels,
                datasets: [{
                    data: values,
                    backgroundColor: gradients,
                    borderColor: '#ffffff',
                    borderWidth: 1
                }]
            },
            options: {
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: (type === 'pie' || type === 'doughnut')
                    }
                },
                scales: type === 'bar' ? {
                    y: { beginAtZero: true }
                } : {}
            }
        });
    }

    // ============================================================
    // GRADIENT GENERATOR ✅ (KEY FEATURE)
    // ============================================================
    function generateGradients(ctx, count) {

        const baseColors = [
            '#3498db', // base brand
            '#5dade2',
            '#85c1e9',
            '#aed6f1',
            '#2ecc71',
            '#e67e22',
            '#9b59b6'
        ];

        const gradients = [];

        for (let i = 0; i < count; i++) {

            const color = baseColors[i % baseColors.length];

            const gradient = ctx.createLinearGradient(0, 0, 0, 300);

            gradient.addColorStop(0, color);
            gradient.addColorStop(1, hexToRGBA(color, 0.2)); // fade

            gradients.push(gradient);
        }

        return gradients;
    }

    // convert HEX → RGBA (for fade effect)
    function hexToRGBA(hex, alpha) {

        const r = parseInt(hex.slice(1,3), 16);
        const g = parseInt(hex.slice(3,5), 16);
        const b = parseInt(hex.slice(5,7), 16);

        return `rgba(${r}, ${g}, ${b}, ${alpha})`;
    }

    // ============================================================
    // SORT
    // ============================================================
    $('.sortable').click(function() {

        const col = $(this).data('col');

        sortDir = (sortCol === col && sortDir === 'asc') ? 'desc' : 'asc';
        sortCol = col;
        page = 1;

        load();
    });

    // ============================================================
    // SEARCH
    // ============================================================
    let timer;

    $('#search').keyup(function() {

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
        $('#visitTable').html(`<tr><td colspan="6" class="text-center text-muted">Loading...</td></tr>`);
    }

    function showError(msg) {
        $('#visitTable').html(`<tr><td colspan="6" class="text-danger text-center">${msg}</td></tr>`);
    }

    function safe(val) {
        return (val ?? '').toString()
            .replace(/&/g,"&amp;")
            .replace(/</g,"&lt;")
            .replace(/>/g,"&gt;");
    }

    // INIT
    load();

});