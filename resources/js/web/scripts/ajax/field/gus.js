$(document).on(
    'click.n1ebieski/idir/web/scripts/ajax/field@gus',
    '#searchGus .btn, #search-gus .btn',
    function (e) {
        e.preventDefault();

        let $searchGus = $('#searchGus, #search-gus');
        $searchGus.form = $searchGus.closest('form');
        $searchGus.url = $searchGus.data('route');
        $searchGus.btn = $searchGus.find('.btn');
        $searchGus.input = $searchGus.find('input');

        $.ajax({
            url: $searchGus.url,
            data: {
                type: $searchGus.find('select#type').val(),
                number: $searchGus.find('input#number').val()
            },
            method: 'post',
            dataType: 'json',        
            beforeSend: function () {
                $searchGus.btn.getLoader('show');
                $searchGus.find('.invalid-feedback').remove();
                $searchGus.input.removeClass('is-valid');
                $searchGus.input.removeClass('is-invalid');
            },
            complete: function () {
                $searchGus.btn.getLoader('hide');
            },
            success: function (response) {
                $.each(response.data, function (key, value) {
                    let $element = $searchGus.form.find('#' + $.escapeSelector(key));

                    if (typeof $element !== 'undefined') {
                        $element.val($.sanitize(value));

                        if ($element.children('#map-select').length) {
                            $('#remove-marker').trigger('click');
                            $('#add-marker').trigger('click', [null, $.sanitize(value)]);
                        }
                    }
                });
            },
            error: function (response) {
                $.each(response.responseJSON.errors, function (key, value) {
                    $searchGus.input.addClass('is-invalid');
                    $searchGus.input.parent().after($.getError(key, value));
                });
            }
        });
    }
);

$(document).on('readyAndAjax.n1ebieski/idir/web/scripts/ajax/field@enter', function () {
    $('#searchGus input, #search-gus input').on('keypress', function (e) {
        if (e.which == 13) {
            $('#searchGus .btn, #search-gus .btn').trigger('click');

            return false;
        }
    });
});
