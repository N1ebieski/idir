jQuery(document).on('click', '.showContact, .show-contact', function (e) {
    e.preventDefault();

    let $element = $(this);

    let $modal = {
        body: $($element.data('target')).find('.modal-body'),
        content: $($element.data('target')).find('.modal-content')
    };

    $modal.body.empty();

    jQuery.ajax({
        url: $element.data('route'),
        method: 'get',
        beforeSend: function() {
            $modal.body.append($.getLoader('spinner-grow'));
        },
        complete: function() {
            $modal.content.find('.loader-absolute').remove();
            $modal.content.find('.captcha').recaptcha();
        },
        success: function(response) {
            $modal.body.html($.sanitize(response.view));
        }
    });
});
