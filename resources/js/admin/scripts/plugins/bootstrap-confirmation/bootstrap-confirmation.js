jQuery(document).on('readyAndAjax', function() {
    $('[data-toggle^="dir-confirmation"]').confirmation({
        rootSelector: '[data-toggle^="dir-confirmation"]',
        copyAttributes: 'href data-route data-id data-delays data-delays-label data-delays-custom data-reasons data-reasons-label data-reasons-custom',
        singleton: true,
        popout: false,
        onConfirm: function () {
            if ($(this).hasClass('submit')) {
                $(this).parents('form:first').submit();
            }
        }
    });
});
