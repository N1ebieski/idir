jQuery(document).on('readyAndAjax', function() {
    if (!$('.trumbowyg-box').length) {
        $('#content_html_dir_trumbowyg').trumbowyg({
            lang: 'pl',
            svgPath: false,
            hideButtonTexts: true,
            tagsToRemove: ['script'],
            autogrow: true,
            btnsDef: {},
            btns: [
                ['viewHTML'],
                ['undo', 'redo'], // Only supported in Blink browsers
                ['formatting'],
                ['foreColor', 'backColor'],
                ['strong', 'em', 'del'],
                ['superscript', 'subscript'],
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
