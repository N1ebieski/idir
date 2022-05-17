$(document).on('readyAndAjax.n1ebieski/idir/web/scripts/plugins/bootstrap-star-rating@init', function () {
    $('[id^="star-rating"]').each(function () {
        $(this).addClass('d-none');
        
        $(this).rating({
            theme: 'krajee-svg',
            showCaption: false
        });
    });
});
