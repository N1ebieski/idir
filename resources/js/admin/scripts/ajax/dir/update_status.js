jQuery(document).on('click', '.statusDir', function (e) {
    e.preventDefault();

    let $element = $(this);
    let $row = $('#row'+$element.attr('data-id'));

    jQuery.ajax({
        url: $element.attr('data-route'),
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        method: 'patch',
        data: {
            status: $element.attr('data-status'),
            reason: $element.closest('.popover-body').find('#reason').val()
        },
        beforeSend: function () {
            $row.find('.responsive-btn-group').addClass('disabled');
            $row.append($.getLoader('spinner-border'));
        },
        complete: function () {
            $row.find('div.loader-absolute').remove();
        },
        success: function (response) {
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
