jQuery(document).on('click', 'a.reloadThumbnail', function(e) {
    e.preventDefault();

    let $element = $(this);
    $element.thumbnail = $element.parent().children('.thumbnail');
    $element.thumbnail.img = $element.thumbnail.children('img');

    jQuery.ajax({
        url: $element.attr('data-route'),
        method: 'patch',
        beforeSend: function() {
            $element.prop('disabled', true);
            $element.thumbnail.append($.getLoader('spinner-border'));
        },
        complete: function() {
            $element.prop('disabled', false);
            $element.thumbnail.find('div.loader-absolute').remove();
        },
        success: function(response) {
            $element.thumbnail.img.attr('src', response.thumbnail_url + '&reload=' + Math.random());
        }
    });
});
