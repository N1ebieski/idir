/*
 * NOTICE OF LICENSE
 * 
 * This source file is licenced under the Software License Agreement
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://intelekt.net.pl/pages/regulamin
 * 
 * With the purchase or the installation of the software in your application
 * you accept the licence agreement.
 * 
 * @author    Mariusz Wysokiński <kontakt@intelekt.net.pl>
 * @copyright Since 2019 INTELEKT - Usługi Komputerowe Mariusz Wysokiński
 * @license   https://intelekt.net.pl/pages/regulamin
 */

$(document).on(
    'change.n1ebieski/idir/admin/scripts/view/price@discountPrice',
    '#discount_price',
    function () {
        let discount_price = parseFloat($(this).val());
        let price = parseFloat($(this).closest('form').find('#price').val());

        if ($.isNumeric(discount_price) && $.isNumeric(price) && discount_price < price) {
            $(this).closest('form').find('#discount').val(
                Math.floor((price - discount_price) / price * 100)
            );
        }
    }
);

$(document).on(
    'change.n1ebieski/idir/admin/scripts/view/price@discount',
    '#discount',
    function () {
        let discount = parseFloat($(this).val());
        let price = parseFloat($(this).closest('form').find('#price').val());

        if ($.isNumeric(discount) && discount > 0 && discount < 100) {
            $(this).closest('form').find('#discount_price').val(
                (price * (100 - discount) / 100).toFixed(2)
            );
        }
    }
);

$(document).on(
    'change.n1ebieski/idir/admin/scripts/view/price@price',
    '#price',
    function () {
        let price = parseFloat($(this).val());
        let discount_price = parseFloat($(this).closest('form').find('#discount_price').val());
        let discount = parseFloat($(this).closest('form').find('#discount').val());

        if ($.isNumeric(price) && ($.isNumeric(discount_price) || $.isNumeric(discount))) {
            $(this).closest('form').find('#discount_price').trigger('change');
        }
    }
);
