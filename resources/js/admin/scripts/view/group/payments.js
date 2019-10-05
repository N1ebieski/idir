jQuery(document).on('change', 'div[id^=prices] div.price:last-child input[type="checkbox"]', function() {
    if ($(this).prop('checked') === true) {
        let $price = $(this).closest('div.price').clone();
        $price.id = parseInt($(this).attr('id').match(/\d+/), 10) + 1;

        $price.find('[id^=price], [for^=price], [name^=prices]').each(function() {
            if ($(this).attr('id')) {
                $(this).attr('id', $(this).attr('id').replace(/(\d+)/, $price.id));
            }
            if ($(this).attr('for')) {
                $(this).attr('for', $(this).attr('for').replace(/(\d+)/, $price.id));
            }
            if ($(this).attr('name')) {
                $(this).attr('name', $(this).attr('name').replace(/(\d+)/, $price.id));
            }
        });

        $(this).closest('div[id^=prices]').append($.sanitize('<div class="price">' + $price.html() + '</div>'));
    }
});

jQuery(document).on('change', 'div[id^=prices] div.price:not(:first-child) input[type="checkbox"]', function() {
    if ($(this).prop('checked') === false) {
        $(this).closest('div.price').remove();
    }
});
