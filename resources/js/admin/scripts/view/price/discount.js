jQuery(document).on('change', '#discount_price', function () {
    let discount_price = parseFloat($(this).val());
    let price = parseFloat($(this).closest('form').find('#price').val());

    if ($.isNumeric(discount_price) && $.isNumeric(price) && discount_price < price) {
        $(this).closest('form').find('#discount').val(
            Math.floor((price - discount_price) / price * 100)
        );
    }
});

jQuery(document).on('change', '#discount', function () {
    let discount = parseFloat($(this).val());
    let price = parseFloat($(this).closest('form').find('#price').val());

    if ($.isNumeric(discount) && discount > 0 && discount < 100) {
        $(this).closest('form').find('#discount_price').val(
            (price * (100 - discount) / 100).toFixed(2)
        );
    }
});

jQuery(document).on('change', '#price', function () {
    let price = parseFloat($(this).val());
    let discount_price = parseFloat($(this).closest('form').find('#discount_price').val());
    let discount = parseFloat($(this).closest('form').find('#discount').val());

    if ($.isNumeric(price) && ($.isNumeric(discount_price) || $.isNumeric(discount))) {
        $(this).closest('form').find('#discount_price').trigger('change');
    }
});
