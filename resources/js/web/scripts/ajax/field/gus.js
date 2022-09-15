/*
 * NOTICE OF LICENSE
 * 
 * This source file is licenced under the Software License Agreement
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://intelekt.net.pl/pages/regulamin
 * 
 * With the purchase or the installation of the software in your application
 * you accept the licence agreement.
 * 
 * @author    Mariusz Wysokiński <kontakt@intelekt.net.pl>
 * @copyright Since 2019 INTELEKT - Usługi Komputerowe Mariusz Wysokiński
 * @license   https://intelekt.net.pl/pages/regulamin
 */

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
                $searchGus.btn.loader('show');
                $searchGus.find('.invalid-feedback').remove();
                $searchGus.input.removeClass('is-valid');
                $searchGus.input.removeClass('is-invalid');
            },
            complete: function () {
                $searchGus.btn.loader('hide');
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
                    $searchGus.input.parent().addError({
                        id: key,
                        message: value
                    });
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
