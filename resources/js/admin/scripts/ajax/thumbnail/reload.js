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
    'click.n1ebieski/idir/admin/scripts/ajax/thumbnail@reload',
    'a.reloadThumbnail, a.reload-thumbnail',
    function (e) {
        e.preventDefault();

        let $element = $(this);
        $element.thumbnail = $element.parent().children('.thumbnail');
        $element.thumbnail.img = $element.thumbnail.children('img');

        $.ajax({
            url: $element.data('route'),
            method: 'patch',
            beforeSend: function () {
                $element.prop('disabled', true);
                $element.thumbnail.addLoader();
            },
            complete: function () {
                $element.prop('disabled', false);
                $element.thumbnail.find('.loader-absolute').remove();
            },
            success: function (response) {
                $element.thumbnail.img.attr('src', response.thumbnail_url + '&reload=' + Math.random());
            }
        });
    }
);
