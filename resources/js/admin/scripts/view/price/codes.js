$(document).on(
    'change.n1ebieski/idir/admin/scripts/view/price@readonlyCodes',
    'div[id^=nav-code] input[id*="sync"]',
    function () {
        let $textarea = $(this).closest('.input-group').find('textarea[id*="codes"]');

        if ($(this).prop('checked') === true) {
            $textarea.prop('readonly', false);
        } else {
            $textarea.prop('readonly', true);
        }
    }
);
