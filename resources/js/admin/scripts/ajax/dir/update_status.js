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
    'click.n1ebieski/idir/admin/scripts/ajax/dir@updateStatus',
    '.statusDir, .status-dir',
    function (e) {
        e.preventDefault();

        let $element = $(this);

        let $row = $('#row' + $element.data('id'));

        $.ajax({
            url: $element.data('route'),
            method: 'patch',
            data: {
                status: $element.data('status'),
                reason: $element.closest('.popover-body').find('#reason').val()
            },
            beforeSend: function () {
                $row.find('.responsive-btn-group').addClass('disabled');
                $row.find('[data-status="' + $element.data('status') + '"]').loader('show');
            },
            success: function (response) {
                $row.find('[data-status="' + $element.data('status') + '"]').loader('hide');
                
                $row.html($.sanitize($(response.view).html()));

                if (response.status == 1) {
                    $row.addClass('alert-success');
                    setTimeout(function () {
                        $row.removeClassStartingWith('alert-');
                    }, 5000);
                }

                if ($.inArray(response.status, [0, 5]) > -1) {
                    $row.addClass('alert-warning');
                    setTimeout(function () {
                        $row.removeClassStartingWith('alert-');
                    }, 5000);
                }
            }
        });
    }
);
