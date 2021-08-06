jQuery(document).on('readyAndAjax', function () {
    $('.thumbnail').popover({
        trigger: 'hover',
        boundary: 'window',
        html: true,
        content: function() {
            return $.sanitize($(this).html());
        },
        placement: 'auto'
    }).on('inserted.bs.popover', function () {
        $('[id^="popover"]').addClass('thumbnail');
    });
});
