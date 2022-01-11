$(document).on(
    'click.n1ebieski/idir/admin/scripts/ajax/dir@destroy',
    'a.destroyDir, a.destroy-dir',
    function (e) {
        e.preventDefault();

        let $element = $(this);

        let $row = $('#row' + $element.data('id'));

        $.ajax({
            url: $element.data('route'),
            data: {
                reason: $element.closest('.popover-body').find('#reason').val(),
            },
            method: 'delete',
            beforeSend: function () {
                $row.find('.responsive-btn-group').addClass('disabled');
                $row.find('[data-btn-ok-class*="destroyDir"], [data-btn-ok-class*="destroy-dir"]').loader('show');
            },
            complete: function () {
                $row.find('[data-btn-ok-class*="destroyDir"], [data-btn-ok-class*="destroy-dir"]').loader('hide');
            },
            success: function () {
                $row.fadeOut('slow');
            }
        });
    }
);
