$(document).on('readyAndAjax.n1ebieski/idir/admin/scripts/view/popover@init', function () {
    $('.thumbnail').popover({
        trigger: 'hover',
        boundary: 'window',
        html: true,
        content: function() {
            return $.sanitize($(this).html());
        },
        placement: 'auto'
    }).on('inserted.bs.popover', function () {
        let $popover = $('[id^="popover"]');
        $popover.img = $('[id^="popover"]').find('img');

        $popover.addClass('thumbnail');

        // Chrome doesn't see width and height of image during insert
        $popover.img.prop('width', $(this).find('img').prop('naturalWidth'));
        $popover.img.prop('height', $(this).find('img').prop('naturalHeight'));
    });
});
