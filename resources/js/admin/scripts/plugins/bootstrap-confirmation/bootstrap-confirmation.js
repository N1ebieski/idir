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

$(document).on('readyAndAjax.n1ebieski/idir/admin/scripts/plugins/bootstrap-confirmation@init', function () {
    $('[data-toggle^="dir-confirmation"]').each(function () {
        let $confirmation = $(this);

        $confirmation.confirmation({
            rootSelector: '[data-toggle^="dir-confirmation"]',
            copyAttributes: 'href data-route data-status data-id data-delays data-delays-label data-delays-custom data-reasons data-reasons-label data-reasons-custom',
            singleton: true,
            popout: false,
            onConfirm: function () {
                if ($confirmation.hasClass('submit')) {
                    $confirmation.parents('form:first').submit();
                }
            }
        });
    });
});
