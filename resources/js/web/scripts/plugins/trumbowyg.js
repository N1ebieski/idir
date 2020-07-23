jQuery(document).on('readyAndAjax', function() {
    if (!$('.trumbowyg-box').length) {
        let $trumbowyg = $('#content_html_dir_trumbowyg');

        $trumbowyg.trumbowyg({
            lang: $trumbowyg.data('lang'),
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
    }
});
