jQuery(document).on('click', '.delayDirStatus', function (e) {
    e.preventDefault();

    let $element = $(this);
    let $row = $('#row' + $element.attr('data-id'));

    jQuery.ajax({
        url: $element.attr('data-route'),
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        method: 'patch',
        data: {
            delay: $element.closest('.popover-body').find('#delay').val()
        },
        beforeSend: function () {
            $row.find('.responsive-btn-group').addClass('disabled');
            $row.append($.getLoader('spinner-border'));
        },
        complete: function () {
            $row.find('.responsive-btn-group').removeClass('disabled');            
            $row.find('div.loader-absolute').remove();
        },
        success: function (response) {
            $row.find('[data-btn-ok-class*="delayDirStatus"]').prop('disabled', true);

            $row.addClass('alert-success');
            setTimeout(function () {
                $row.removeClassStartingWith('alert-');
            }, 5000);
        }
    });
});
