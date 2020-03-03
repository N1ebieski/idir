jQuery(document).on('change', 'form input[id^=delete_img]', function() {
    let $input = $(this).closest('.form-group').find('[type="file"]');
    let $hidden = $(this).closest('.form-group').find('[type="hidden"]');

    if ($(this).prop('checked') === true) {
        $input.prop('disabled', false);
        $hidden.prop('disabled', true);
    } else {
        $input.prop('disabled', true);
        $hidden.prop('disabled', false);
    }
});
