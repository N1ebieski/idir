jQuery(document).ready(function() {
    $('[aria-controls="collapsePayments"]').change(function() {
        if ($(this).val() == 0) $('#collapsePayments').collapse('hide');
        else $('#collapsePayments').collapse('show');
    });
});
