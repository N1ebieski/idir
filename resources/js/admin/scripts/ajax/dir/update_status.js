jQuery(document).on('click', '.statusDir, .status-dir', function (e) {
    e.preventDefault();

    let $element = $(this);

    let $row = $('#row' + $element.data('id'));

    jQuery.ajax({
        url: $element.data('route'),
        method: 'patch',
        data: {
            status: $element.data('status'),
            reason: $element.closest('.popover-body').find('#reason').val()
        },
        beforeSend: function () {
            $row.find('.responsive-btn-group').addClass('disabled');
            $row.find('[data-status="' + $element.data('status') + '"]').getLoader('show');
        },
        success: function (response) {
            $row.find('[data-status="' + $element.data('status') + '"]').getLoader('hide');
            
            $row.html($.sanitize($(response.view).html()));

            if (response.status == 1) {
                $row.addClass('alert-success');
                setTimeout(function () {
                    $row.removeClassStartingWith('alert-');
                }, 5000);
            }

            if ($.inArray(response.status, [0, 5]) > -1) {
                $row.addClass('alert-warning');
                setTimeout(function () {
                    $row.removeClassStartingWith('alert-');
                }, 5000);
            }
        }
    });
});
