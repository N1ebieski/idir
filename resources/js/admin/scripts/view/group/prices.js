jQuery(document).on('change', 'div[id^=prices] div.price:last-child input[type="checkbox"]', function() {
    if ($(this).prop('checked') === true) {
        let $price = $(this).closest('div.price').clone();
        $price.id = parseInt($(this).attr('id').match(/\d+/), 10) + 1;

        $price.find('[id^=price], [for^=price], [name^=prices]').each(function(index, element) {
            $.each(['id', 'for', 'name'], function(key, value) {
                if ($(element).attr(value)) {
                    $(element).attr(value, $(element).attr(value).replace(/(\d+)/, $price.id));
                }
            });
        });

        $(this).closest('div[id^=prices]').append($.sanitize('<div class="price">' + $price.html() + '</div>'));
    }
});

jQuery(document).on('change', 'div[id^=prices] div.price:not(:first-child) input[type="checkbox"]', function() {
    if ($(this).prop('checked') === false) {
        $(this).closest('div.price').remove();
    }
});
