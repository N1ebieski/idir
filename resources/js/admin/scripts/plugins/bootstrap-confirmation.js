jQuery(document).on('readyAndAjax', function() {
    $('[data-toggle=dir-confirmation]').confirmation({
        rootSelector: '[data-toggle=dir-confirmation]',
        copyAttributes: 'href data-route data-id',
        singleton: true,
        popout: false,
        onConfirm: function() {
            if ($(this).hasClass('submit')) {
                $(this).parents('form:first').submit();
            }
        }
    });

    $('[data-toggle=dir-confirmation]').on('inserted.bs.confirmation', function() {
        let $element = $('[id^="confirmation"] .popover-body p.confirmation-content');
        let $select = $($.parseHTML('<div><div class="form-group"><select id="reason" class="form-control"><option value="">' + $('form#selectForm').attr('data-reasons-label') + ':</option></select></div></div>'));

        $.each($.parseJSON($('form#selectForm').attr('data-reasons')), function(key, value) {
            $select.find('select#reason').append('<option value="' + value + '">' + value + '</option>');
        });

        $element.html($.sanitize($select.html())).show();
    });
});
