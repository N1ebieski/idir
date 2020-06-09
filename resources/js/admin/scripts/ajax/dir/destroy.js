jQuery(document).on('click', 'a.destroyDir', function (e) {
    e.preventDefault();

    let $element = $(this);
    let $row = $('#row'+$element.attr('data-id'));

    jQuery.ajax({
        url: $element.attr('data-route'),
        data: {
            reason: $element.closest('.popover-body').find('#reason').val(),
        },
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        method: 'delete',
        beforeSend: function () {
            $row.find('.responsive-btn-group').addClass('disabled');
            $row.append($.getLoader('spinner-border'));
        },
        complete: function () {
            $row.find('div.loader-absolute').remove();
        },
        success: function () {
            $row.fadeOut('slow');
        }
    });
});
