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

$(document).on('readyAndAjax.n1ebieski/idir/admin/scripts/view/popover@init', function () {
    $('.thumbnail').popover({
        trigger: 'hover',
        boundary: 'window',
        html: true,
        content: function () {
            return $.sanitize($(this).html());
        },
        placement: 'auto'
    }).on('inserted.bs.popover', function () {
        let $popover = $('[id^="popover"]');
        $popover.img = $('[id^="popover"]').find('img');

        $popover.addClass('thumbnail');

        // Chrome doesn't see width and height of image during insert
        $popover.img.prop('width', $(this).find('img').prop('naturalWidth'));
        $popover.img.prop('height', $(this).find('img').prop('naturalHeight'));
    });
});
