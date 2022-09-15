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

$(document).on('readyAndAjax.n1ebieski/idir/admin/scripts/plugins/trumbowyg@init', function () {
    if (!$('.trumbowyg-box').length) {
        let $trumbowyg = $('#content_html_dir_trumbowyg');

        $trumbowyg.trumbowyg({
            lang: $trumbowyg.data('lang'),
            fixedBtnPane: $trumbowyg.data('fixed-btn-pane') || true,
            fixedFullWidth: $trumbowyg.data('fixed-full-width') || false,              
            svgPath: false,
            hideButtonTexts: true,
            tagsToRemove: ['script'],
            autogrow: true,
            btnsDef: {},
            btns: [
                ['viewHTML'],
                ['historyUndo', 'historyRedo'],
                // ['undo', 'redo'], // Only supported in Blink browsers
                ['formatting'],
                ['foreColor', 'backColor'],
                ['strong', 'em', 'del'],
                // ['superscript', 'subscript'],
                // ['link'],
                // ['insertImage'],
                // ['justifyLeft', 'justifyCenter', 'justifyRight', 'justifyFull'],
                ['unorderedList', 'orderedList'],
                // ['horizontalRule'],
                ['removeformat'],
                // ['more'],
                ['fullscreen']
            ]
        });

        $trumbowyg.on('tbwopenfullscreen', function () {
            $('.trumbowyg-fullscreen .trumbowyg-editor').css({
                'cssText': `height: calc(100% - ${$('.trumbowyg-button-pane').height()}px) !important`
            });
        });        
    }
});
