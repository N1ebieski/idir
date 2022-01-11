$(document).on(
    'click.n1ebieski/idir/admin/scripts/ajax/thumbnail@reload',
    'a.reloadThumbnail, a.reload-thumbnail',
    function (e) {
        e.preventDefault();

        let $element = $(this);
        $element.thumbnail = $element.parent().children('.thumbnail');
        $element.thumbnail.img = $element.thumbnail.children('img');

        $.ajax({
            url: $element.data('route'),
            method: 'patch',
            beforeSend: function () {
                $element.prop('disabled', true);
                $element.thumbnail.addLoader();
            },
            complete: function () {
                $element.prop('disabled', false);
                $element.thumbnail.find('.loader-absolute').remove();
            },
            success: function (response) {
                $element.thumbnail.img.attr('src', response.thumbnail_url + '&reload=' + Math.random());
            }
        });
    }
);
