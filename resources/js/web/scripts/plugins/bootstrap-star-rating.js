jQuery(document).on('readyAndAjax', function () {
    $('[id^="star-rating"]').rating({
        theme: 'krajee-svg',
        showCaption: false
    });
});
