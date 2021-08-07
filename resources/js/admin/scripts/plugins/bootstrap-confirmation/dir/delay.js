jQuery(document).on('readyAndAjax', function () {
    $('[data-toggle=dir-confirmation-delay]').on('inserted.bs.confirmation', function () {
        let $element = $('[id^="confirmation"] .popover-body').last();
        $element.p = $element.find('p.confirmation-content');
        $element.btn = $element.find('a.btn.btn-primary');

        let $select = $($.parseHTML('<div><div class="form-group"><label for="delay">' + $element.btn.data('delays-label') + ':</label><select id="delay" class="form-control custom-select"></select></div></div>'));
        $select.delay = $select.find('select#delay');

        $.each($element.btn.data('delays'), function (key, value) {
            $select.delay.append('<option value="' + value + '">' + value + '</option>');
        });

        $select.delay.append('<option value="custom">' + $element.btn.data('delays-custom') + '...</option>');

        $element.p.html($.sanitize($select.html())).show();
    });
});

$(document).on('change', '[id^="confirmation"] .popover-body select#delay', function () {
    if ($(this).val() === 'custom') {
        $(this).replaceWith('<input type="text" id="delay" class="form-control">');
    }
});
