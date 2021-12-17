$(document).on(
    'click.n1ebieski/idir/web/scripts/ajax/stat@click',
    'a.clickStat, a.click-stat',
    function (e) {
        e.preventDefault();

        let $element = $(this);

        $.ajax({
            url: $element.data('route'),
            method: 'get'
        });

        window.open($element.attr('href'), '_blank');

        return;
    }
);
