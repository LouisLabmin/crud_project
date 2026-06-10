//shared/js/streets_lookup.js

$(function () {

    const BASE = window.APP_CONFIG.base;
    let timer;

    // ============================================
    // SEARCH
    // ============================================
    $('#searchBox').on('keyup', function () {

        const query = $(this).val().trim();
        const country = $('#countrySelect').val();

        clearTimeout(timer);

        if (query.length < 2) {
            showMessage('Keep typing...');
            return;
        }

        timer = setTimeout(() => {
            fetchResults(query, country);
        }, 400);
    });

    function fetchResults(query, country) {

        showLoading();

        $.get(BASE + '/shared/ajax/get_streets_data.php', {
            q: query,
            country: country
        }, function (res) {

            if (!res || res.status !== 'success') {
                showError('Failed to load data');
                return;
            }

            renderResults(res.data);

        }, 'json')
        .fail(() => showError('Server error'));
    }

    // ============================================
    // RENDER
    // ============================================
    function renderResults(rows) {

        const $r = $('#results').empty();

        if (!rows.length) {
            showMessage('No results found');
            return;
        }

        rows.forEach(row => {

            const json = encodeURIComponent(JSON.stringify(row));

            $r.append(`
                <div class="border rounded p-2 mb-2">

                    <strong>${safe(row.str_number)} ${safe(row.str_name)}</strong><br>
                    <small>${safe(row.suburb)}, ${safe(row.city)}</small><br>
                    <small>${safe(row.province)}, ${safe(row.country)}</small>

                    <div class="text-end mt-2">
                        <button class="btn btn-sm btn-primary save-btn"
                            data-row="${json}">
                            Save
                        </button>
                    </div>

                </div>
            `);
        });
    }

    // ============================================
    // SAVE (NO REQUERY ✅)
    // ============================================
    $(document).on('click', '.save-btn', function () {

        const row = JSON.parse(decodeURIComponent($(this).data('row')));

        if (!confirm('Save this address?')) return;

        $.post(BASE + '/shared/ajax/get_streets_data.php', {
            action: 'save',
            data: JSON.stringify(row)
        }, function (res) {

            if (res.status === 'success') {
                alert('Saved ✅');
            } else {
                alert('Save failed');
            }

        }, 'json');
    });

    // ============================================
    // HELPERS
    // ============================================
    function showLoading() {
        $('#results').html('<div class="text-center text-muted">Loading...</div>');
    }

    function showMessage(msg) {
        $('#results').html(`<div class="text-center text-muted">${msg}</div>`);
    }

    function showError(msg) {
        $('#results').html(`<div class="text-danger text-center">${msg}</div>`);
    }

    function safe(v) {
        return (v ?? '').toString()
            .replace(/&/g,"&amp;")
            .replace(/</g,"&lt;")
            .replace(/>/g,"&gt;");
    }
});