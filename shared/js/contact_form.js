// shared/js/contact_form.js
// Handles form validation and AJAX submission

$(function () {

    const maxLength = 2000;

    const $message = $('#contact_message');
    const $counter = $('#char_count');

    function updateCounter() {
        const length = $message.val().length;
        const remaining = maxLength - length;

        $counter.text(remaining + ' characters remaining');

        if (remaining < 0) {
            $counter.css('color', 'red');
        } else if (remaining < 100) {
            $counter.css('color', 'orange');
        } else {
            $counter.css('color', '#6c757d');
        }
    }

    $message.on('input', updateCounter);
    updateCounter();

    $('#contact_form').on('submit', function (e) {
        e.preventDefault();

        const data = {
            contact_name: $('#contact_name').val().trim(),
            contact_email: $('#contact_email').val().trim(),
            contact_number: $('#contact_number').val().trim(),
            contact_message: $message.val().trim(),
            csrf_token: $('input[name="csrf_token"]').val()
        };

        const $status = $('#contact_status');
        $status.removeClass().empty();

        if ($('#website').val()) return;

        // ✅ UPDATED VALIDATION
        if (data.contact_message.length < 10) {
            $status.addClass('text-danger').text('Message must be at least 10 characters');
            return;
        }

        if (data.contact_message.length > maxLength) {
            $status.addClass('text-danger').text('Message too long');
            return;
        }

        if (data.contact_email && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(data.contact_email)) {
            $status.addClass('text-danger').text('Invalid email');
            return;
        }

        $status.text('Sending...');

        $.ajax({
            url: window.APP_CONFIG.base + '/shared/ajax/contact_submit.php',
            method: 'POST',
            data: data,
            dataType: 'json'
        })
        .done(function (res) {
            if (res.status === 'success') {
                $('#contact_form')[0].reset();
                updateCounter();
                $status.removeClass().addClass('text-success').text('Message sent ✅');
            } else {
                $status.addClass('text-danger').text(res.message || 'Error');
            }
        })
        .fail(function () {
            $status.addClass('text-danger').text('Server error');
        });
    });

});