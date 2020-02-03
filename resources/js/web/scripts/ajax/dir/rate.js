jQuery(document).ready(function() {
    $('[id^="star-rating"]').on('rating:change', function(event, value, caption) {
        event.preventDefault();

        let $element = $(this);

        if (value === $element.attr('data-user-value')) {
            $element.rating("update", $element.attr('value'));

            return;
        }

        $.ajax({
            url: $element.attr('data-route') + '?rating=' + value,
            method: 'get',
            beforeSend: function() {
            },
            complete: function() {
            },
            success: function(response) {
                $element.rating("update", response.sum_rating)
                        .rating("refresh", {
                            showClear: true
                        })
                        .attr('data-user-value', value);
            }
        });    
    });

    $('[id^="star-rating"]').on('rating:clear', function(event, value, caption) {
        event.preventDefault();

        let $element = $(this);

        $.ajax({
            url: $element.attr('data-route') + '?rating=' + $element.attr('data-user-value'),
            method: 'get',
            beforeSend: function() {
            },
            complete: function() {
            },
            success: function(response) {
                $element.rating("update", response.sum_rating)
                        .rating("refresh", {
                            showClear: false
                        })
                        .attr('data-user-value', '');
            }
        }); 
    });
});