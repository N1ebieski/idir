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
    'click.n1ebieski/idir/web/scripts/ajax/dir@generateContent',
    '#generate-content .btn',
    function (e) {
        e.preventDefault();

        let $generateContent = $('#generate-content');
        $generateContent.form = $generateContent.closest('form');
        $generateContent.url = $generateContent.data('route');
        $generateContent.btn = $generateContent.find('.btn');
        $generateContent.input = $generateContent.find('input');

        $.ajax({
            url: $generateContent.url,
            data: {
                url: $generateContent.form.find('input#url').val(),
                title: $generateContent.form.find('input#title').val()
            },
            method: 'post',
            dataType: 'json',
            beforeSend: function () {
                $generateContent.btn.loader('show');

                ['title', 'url'].forEach(function (value) {
                    $generateContent.form.find('#' + value).parent().find('.invalid-feedback').remove();

                    $generateContent.form.find('#' + value).removeClass('is-valid');
                    $generateContent.form.find('#' + value).removeClass('is-invalid');
                });
            },
            complete: function () {
                $generateContent.btn.loader('hide');
            },
            success: function (response) {
                if ('content' in response.data) {
                    let $contentHtml = $generateContent.form.find('textarea[name="content_html"]');

                    if ($contentHtml.hasClass('trumbowyg-textarea')) {
                        $contentHtml.trumbowyg('html', response.data.content);
                    } else {
                        $contentHtml.val(response.data.content);
                    }

                    $contentHtml.trigger('keyup');
                }

                if ('tags' in response.data) {
                    let $tags = $generateContent.form.find('input[name="tags"]');

                    $tags.importTags(response.data.tags);
                }

                if ('categories' in response.data) {
                    let $categories = $generateContent.form.find('select[name="categories[]"]');

                    const optgroup = $('<optgroup>', { label: $categories.data('optgroup-label') });

                    response.data.categories.forEach(function (category) {
                        let dataContent = category.name;

                        if (category.ancestors.length > 0) {
                            dataContent = `<small class="p-0 m-0">${category.ancestors.map(ancestor => ancestor.name).join(' &raquo; ')} &raquo; </small>` + dataContent;
                        }

                        const option = $('<option>', {
                            value: category.id,
                            text: category.name,
                            "data-content": dataContent,
                            selected: true
                        });

                        optgroup.append(option);
                    });

                    $categories.html(optgroup);

                    $categories.trigger('change').data('AjaxBootstrapSelect').list.cache = {};
                    $categories.attr('data-loaded', false);
                }
            },
            error: function (response) {
                if (response.responseJSON.errors) {
                    if ('title' in response.responseJSON.errors) {
                        $generateContent.form.find('#title').addClass('is-invalid');
                        $generateContent.form.find('#title').parent().addError({
                            id: 'title',
                            message: response.responseJSON.errors.title
                        });
                    }

                    if ('url' in response.responseJSON.errors) {
                        $generateContent.input.addClass('is-invalid');
                        $generateContent.input.parent().addError({
                            id: 'url',
                            message: response.responseJSON.errors.url
                        });
                    }

                    return;
                }

                if (response.responseJSON.message) {
                    $('body').addToast({
                        title: response.responseJSON.message,
                        type: 'danger'
                    });
                }
            }
        });
    }
);

$(document).on('readyAndAjax.n1ebieski/idir/web/scripts/ajax/dir@generateContentEnter', function () {
    $('#generate-content input').on('keypress', function (e) {
        if (e.which == 13) {
            $('#generate-content .btn').trigger('click');

            return false;
        }
    });
});
