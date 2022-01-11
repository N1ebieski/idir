$(document).on(
    'click.n1ebieski/idir/admin/scripts/ajax/dir@delay',
    '.delayDir, .delay-dir',
    function (e) {
        e.preventDefault();

        let $element = $(this);

        let $row = $('#row' + $element.data('id'));

        $.ajax({
            url: $element.data('route'),
            method: 'patch',
            data: {
                delay: $element.closest('.popover-body').find('#delay').val()
            },
            beforeSend: function () {
                $row.find('.responsive-btn-group').addClass('disabled');
                $row.find('[data-btn-ok-class*="delayDir"], [data-btn-ok-class*="delay-dir"]').loader('show');
            },
            complete: function () {
                $row.find('.responsive-btn-group').removeClass('disabled');            
                $row.find('[data-btn-ok-class*="delayDir"], [data-btn-ok-class*="delay-dir"]').loader('hide');
            },
            success: function (response) {
                $row.html($.sanitize($(response.view).html()));

                $row.addClass('alert-success');
                setTimeout(function () {
                    $row.removeClassStartingWith('alert-');
                }, 5000);
            }
        });
    }
);
