jQuery(document).on('click', 'a.destroyDir, a.destroy-dir', function (e) {
    e.preventDefault();

    let $element = $(this);

    let $row = $('#row' + $element.data('id'));

    jQuery.ajax({
        url: $element.data('route'),
        data: {
            reason: $element.closest('.popover-body').find('#reason').val(),
        },
        method: 'delete',
        beforeSend: function () {
            $row.find('.responsive-btn-group').addClass('disabled');
            $row.find('[data-btn-ok-class*="destroyDir"], [data-btn-ok-class*="destroy-dir"]').getLoader('show');
        },
        complete: function () {
            $row.find('[data-btn-ok-class*="destroyDir"], [data-btn-ok-class*="destroy-dir"]').getLoader('hide');
        },
        success: function () {
            $row.fadeOut('slow');
        }
    });
});
