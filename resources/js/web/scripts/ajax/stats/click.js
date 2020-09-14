jQuery(document).on('click', 'a.statsClick', function(e) {
    e.preventDefault();

    let $element = $(this);

    $.ajax({
        url: $element.data('route'),
        method: 'get'
    });

    window.open($element.attr('href'), '_blank');
});
