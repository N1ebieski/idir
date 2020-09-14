jQuery(document).on('click', 'button.sendContact', function (e) {
    e.preventDefault();

    let $form = $(this).closest('form');
    $form.btn = $form.find('.btn');
    $form.input = $form.find('.form-control, .custom-control-input');
    let $modal = {
        body: $form.closest('.modal-body')
    };

    $.ajax({
        url: $form.attr('data-route'),
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        method: 'post',
        data: $form.serialize(),
        dataType: 'json',
        beforeSend: function () {
            $form.btn.prop('disabled', true);
            $modal.body.append($.getLoader('spinner-border'));
            $('.invalid-feedback').remove();
            $form.input.removeClass('is-valid');
            $form.input.removeClass('is-invalid');
        },
        complete: function () {
            $form.btn.prop('disabled', false);
            $modal.body.find('div.loader-absolute').remove();
            $form.find('.captcha').recaptcha();
            $form.find('.captcha').captcha();
            $form.input.addClass('is-valid');
        },
        success: function (response) {
            $modal.body.html($.getAlert(response.success, 'success'));
        },
        error: function (response) {
            if (response.responseJSON.errors) {
                $.each(response.responseJSON.errors, function (key, value) {
                    $form.find('[name="'+key+'"]').addClass('is-invalid');
                    $form.find('[name="'+key+'"]').closest('.form-group').append($.getError(key, value));
                });
                return;
            }

            if (response.responseJSON.message) {
                $form.prepend($.getAlert(response.responseJSON.message, 'danger'));
            }            
        }
    });
});
