$(document).on(
    'click.n1ebieski/idir/web/scripts/ajax/contact@show',
    '.showContact, .show-contact',
    function (e) {
        e.preventDefault();

        let $element = $(this);

        let $modal = {
            body: $($element.data('target')).find('.modal-body'),
            footer: $($element.data('target')).find('.modal-footer'),
            content: $($element.data('target')).find('.modal-content')
        };

        $modal.body.empty();
        $modal.footer.empty();

        $.ajax({
            url: $element.data('route'),
            method: 'get',
            beforeSend: function() {
                $modal.body.addLoader('spinner-grow');
            },
            complete: function() {
                $modal.content.find('.loader-absolute').remove();
                $modal.content.find('.captcha').recaptcha();
            },
            success: function(response) {
                $modal.content.html($.sanitize($(response.view).find('.modal-content').html()));
            }
        });
    }
);
