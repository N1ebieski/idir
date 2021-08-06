jQuery(document).on('readyAndAjax', function () {
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
