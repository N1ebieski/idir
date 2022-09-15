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

$(document).on('ready.n1ebieski/idir/web/scripts/plugins/cssmap-poland@init', function () {
    $("#map-poland").CSSMap({
        "size": 430,
        "tooltips": "floating-top-center",
        "responsive": "auto",
        "tapOnce": true,
        onLoad: function () {
            $("#map-poland").find('a.active-region').parent().addClass('active-region');
        }
    }).children().show();
});
