jQuery(document).on('click', '#searchGus .btn', function(e) {
    e.preventDefault();

    let $searchGus = $('#searchGus');
    $searchGus.form = $searchGus.closest('form');
    $searchGus.url = $searchGus.attr('data-route');
    $searchGus.btn = $searchGus.find('.btn');
    $searchGus.input = $searchGus.find('input');

    $.ajax({
        url: $searchGus.url,
        data: {
            type: $searchGus.find('select#type').val(),
            number: $searchGus.find('input#number').val()
        },
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        method: 'post',
        dataType: 'json',        
        beforeSend: function() {
            $searchGus.btn.prop('disabled', true);
            $searchGus.append($.getLoader('spinner-border'));
            $searchGus.find('.invalid-feedback').remove();
            $searchGus.input.removeClass('is-valid');
            $searchGus.input.removeClass('is-invalid');
        },
        complete: function() {
            $searchGus.btn.prop('disabled', false);
            $searchGus.find('div.loader-absolute').remove();
        },
        success: function(response) {
            $.each(response.data, function( key, value ) {
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
        error: function(response) {
            var errors = response.responseJSON;

            $.each(errors.errors, function( key, value ) {
                $searchGus.input.addClass('is-invalid');
                $searchGus.input.parent().after($.getError(key, value));
            });
        }
    });
});

jQuery(document).on('readyAndAjax', function() {
    $('#searchGus input').keypress(function(e) {
        if (e.which == 13) {
            $('#searchGus .btn').trigger('click');
            return false;
        }
    });
});