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
    'click.n1ebieski/idir/admin/scripts/view/dir@checkContent',
    '.checkContent, .check-content',
    function (e) {
        e.preventDefault();

        let sentence = $(this).parent().find('[id^="content"]').text().split(".").filter(n => n);
        let i, j = 0;
        let content = '';

        for (i = 0; i < 50; i++) {
            if (j === 0) {
                j = Math.floor(Math.random() * sentence.length);
            }

            if (typeof sentence[j] !== 'undefined') {
                content += sentence[j].trim() + '. ';
                j++;
            } else {
                content = '';
                j = 0;
            }

            if (content.length > 150) {
                window.open(
                    'http://www.google.pl/search?hl=pl&q=' + encodeURI(content), 
                    'checkContent', 
                    'resizable=yes,status=no,scrollbars=yes,toolbar=no,menubar=no,width=1366,height=768'
                ).focus();

                break;
            }
        }

        return false;
    }
);
