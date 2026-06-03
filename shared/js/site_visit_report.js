// shared/js/site_visit_report.js

$(function () {

    console.log("Site report JS loaded ✅");

    const data = window.SITE_REPORT;

    if (!data) {
        console.error("No SITE_REPORT data found");
        return;
    }

    // SEARCH
    $('#search').on('keyup', function () {
        const value = $(this).val().toLowerCase();

        $('#visitTable tr').filter(function () {
            $(this).toggle($(this).text().toLowerCase().includes(value));
        });
    });

    // CHARTS
    new Chart(document.getElementById('countryChart'), {
        type: 'pie',
        data: {
            labels: data.countryLabels,
            datasets: [{ data: data.countryValues }]
        }
    });

    new Chart(document.getElementById('deviceChart'), {
        type: 'doughnut',
        data: {
            labels: data.deviceLabels,
            datasets: [{ data: data.deviceValues }]
        }
    });

    new Chart(document.getElementById('osChart'), {
        type: 'bar',
        data: {
            labels: data.osLabels,
            datasets: [{ data: data.osValues }]
        }
    });

    new Chart(document.getElementById('browserChart'), {
        type: 'bar',
        data: {
            labels: data.browserLabels,
            datasets: [{ data: data.browserValues }]
        }
    });

});