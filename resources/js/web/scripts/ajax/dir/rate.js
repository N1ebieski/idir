$(document).on('ready.n1ebieski/idir/web/scripts/ajax/dir@rate', function () {
    $('[id^="star-rating"]').on('rating:change', function (event, value) {
        event.preventDefault();

        let $element = $(this);

        if (value === $element.data('user-value')) {
            $element.rating("update", $element.attr('value'));

            return;
        }

        $.ajax({
            url: $element.data('route') + '?rating=' + value,
            method: 'get',
            success: function (response) {
                $element.rating("update", response.sum_rating)
                        .rating("refresh", {
                            showClear: true
                        })
                        .attr('value', response.sum_rating)
                        .data('user-value', value);
            }
        });    
    });

    $('[id^="star-rating"]').on('rating:clear', function (event) {
        event.preventDefault();

        let $element = $(this);

        $.ajax({
            url: $element.data('route') + '?rating=' + $element.data('user-value'),
            method: 'get',
            success: function (response) {
                $element.rating("update", response.sum_rating)
                        .rating("refresh", {
                            showClear: false
                        })
                        .data('user-value', '');
            }
        }); 
    });
});
