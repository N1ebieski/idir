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

$(document).on('readyAndAjax.n1ebieski/idir/admin/scripts/plugins/bootstrap-confirmation/dir@reason', function () {
    $('[data-toggle=dir-confirmation-reason]').on('inserted.bs.confirmation', function () {
        let $element = $('[id^="confirmation"] .popover-body').last();
        $element.p = $element.find('p.confirmation-content');
        $element.btn = $element.find('a.btn.btn-primary');

        let $select = $($.parseHTML('<div><div class="form-group"><select id="reason" class="form-control custom-select"><option value="">' + $element.btn.data('reasons-label') + ':</option></select></div></div>'));
        $select.reason = $select.find('select#reason');

        $.each($element.btn.data('reasons'), function (key, value) {
            $select.reason.append('<option value="' + value + '">' + value + '</option>');
        });

        $select.reason.append('<option value="custom">' + $element.btn.data('reasons-custom') + '...</option>');

        $element.p.html($.sanitize($select.html())).show();
    });
});

$(document).on(
    'change.n1ebieski/idir/admin/scripts/plugins/bootstrap-confirmation/dir@customReason',
    '[id^="confirmation"] .popover-body select#reason',
    function () {
        if ($(this).val() === 'custom') {
            $(this).replaceWith('<input type="text" id="reason" class="form-control">');
        }
    }
);
