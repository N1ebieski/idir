$(document).on(
    'click.n1ebieski/idir/web/scripts/ajax/contact@send',
    '.sendContact, .send-contact',
    function (e) {
        e.preventDefault();

        let $element = $(this);

        let $form = $element.closest('.modal-content').find('form');
        $form.btn = $form.find('.btn');
        $form.input = $form.find('.form-control, .custom-control-input');

        $.ajax({
            url: $form.data('route'),
            method: 'post',
            data: $form.serialize(),
            dataType: 'json',
            beforeSend: function () {
                $element.getLoader('show');
                $('.invalid-feedback').remove();
                $form.input.removeClass('is-valid');
                $form.input.removeClass('is-invalid');
            },
            complete: function () {
                $element.getLoader('hide');
                $form.find('.captcha').recaptcha();
                $form.find('.captcha').captcha();
                $form.input.addClass('is-valid');
            },
            success: function (response) {
                $('.modal').modal('hide');

                $('body').addToast(response.success);
            },
            error: function (response) {
                if (response.responseJSON.errors) {
                    $.each(response.responseJSON.errors, function (key, value) {
                        $form.find('[name="' + $.escapeSelector(key) + '"]').addClass('is-invalid');
                        $form.find('[name="' + $.escapeSelector(key) + '"]').closest('.form-group').append($.getError(key, value));
                    });

                    return;
                }

                if (response.responseJSON.message) {
                    $form.prepend($.getAlert('danger', response.responseJSON.message));
                }            
            }
        });
    }
);
