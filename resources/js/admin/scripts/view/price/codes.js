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
    'change.n1ebieski/idir/admin/scripts/view/price@readonlyCodes',
    'div[id^=nav-code] input[id*="sync"]',
    function () {
        let $textarea = $(this).closest('.input-group').find('textarea[id*="codes"]');

        if ($(this).prop('checked') === true) {
            $textarea.prop('readonly', false);
        } else {
            $textarea.prop('readonly', true);
        }
    }
);
