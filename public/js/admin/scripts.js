jQuery(document).ready(function() {
    $(document).trigger('readyAndAjax');
});

jQuery(document).ajaxComplete(function() {
    $(document).trigger('readyAndAjax');
});

jQuery(document).on('click', 'button.storeBanUser', function(e) {
    e.preventDefault();

    let $form = $(this).closest('form');
    $form.btn = $form.find('.btn');
    $form.input = $form.find('.form-control, .custom-control-input');
    let $modal = {};
    $modal.body = $form.closest('.modal-body');

    jQuery.ajax({
        url: $form.attr('data-route'),
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        method: 'post',
        data: $form.serialize(),
        dataType: 'json',
        beforeSend: function() {
            $form.btn.prop('disabled', true);
            $modal.body.append($.getLoader('spinner-border'));
            $('.invalid-feedback').remove();
            $form.input.removeClass('is-valid');
            $form.input.removeClass('is-invalid');
        },
        complete: function() {
            $form.btn.prop('disabled', false);
            $modal.body.find('div.loader-absolute').remove();
            $form.input.addClass('is-valid');
        },
        success: function(response) {
            $modal.body.html($.getAlert(response.success, 'success'));
        },
        error: function(response) {
            var errors = response.responseJSON;

            $.each(errors.errors, function( key, value ) {
                $form.find('[id="'+key+'"]').addClass('is-invalid');
                $form.find('[id="'+key+'"]').closest('.form-group')
                                            .append($.getError(key, value));
            });
        }
    });
});

jQuery(document).on('click', 'a.destroyCategory', function(e) {
    e.preventDefault();

    let $element = $(this);
    let $row = $('#row'+$element.attr('data-id'));

    jQuery.ajax({
        url: $element.attr('data-route'),
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        method: 'delete',
        beforeSend: function() {
            $row.find('.responsive-btn-group').addClass('disabled');
            $row.append($.getLoader('spinner-border'));
        },
        complete: function() {
            $row.find('div.loader-absolute').remove();
        },
        success: function(response) {
            $row.fadeOut('slow');
            $.each(response.descendants, function(key, value) {
                let $rowDescendant = $('#row'+value);
                if ($rowDescendant.length) {
                    $rowDescendant.fadeOut('slow');
                }
            });
        }
    });
});

function categorySelect()
{
    return $('#categoryOptions .form-group').map(function() {
       return '#' + $(this).attr('id');
    }).get();
}

jQuery(document).on('click', '#searchCategory .btn', function(e) {
    e.preventDefault();

    let $searchCategory = $('#searchCategory');
    $searchCategory.url = $searchCategory.attr('data-route');
    $searchCategory.btn = $searchCategory.find('.btn');
    $searchCategory.input = $searchCategory.find('input');

    $.ajax({
        url: $searchCategory.url+'?name='+$searchCategory.input.val(),
        method: 'get',
        dataType: 'json',
        beforeSend: function() {
            $searchCategory.btn.prop('disabled', true);
            $('#searchCategoryOptions').empty();
            $searchCategory.append($.getLoader('spinner-border'));
            $('.invalid-feedback').remove();
            $searchCategory.input.removeClass('is-valid');
            $searchCategory.input.removeClass('is-invalid');
        },
        complete: function() {
            $searchCategory.btn.prop('disabled', false);
            $searchCategory.find('div.loader-absolute').remove();
        },
        success: function(response) {
            let $response = $(response.view).find(categorySelect().join(',')).remove().end();

            $searchCategory.find('#searchCategoryOptions').html($.sanitize($response.html()));
        },
        error: function(response) {
            var errors = response.responseJSON;

            $.each(errors.errors, function( key, value ) {
                $searchCategory.input.addClass('is-invalid');
                $searchCategory.input.parent().after($.getError(key, value));
            });
        }
    });
});

jQuery(document).on('change', '.categoryOption', function() {

    let $searchCategory = $('#searchCategory');
    $searchCategory.max = $searchCategory.attr('data-max');
    let $input = $(this).closest('.form-group');

    if ($(this).prop('checked') == true) {
        $input.appendTo('#categoryOptions');
    } else {
        $input.remove();
    }

    if ($.isInteger($searchCategory.max)) {
        if ($searchCategory.is(':visible') && categorySelect().length >= $searchCategory.max) {
            $searchCategory.fadeOut();
        }

        if (!$searchCategory.is(':visible') && categorySelect().length < $searchCategory.max) {
            $searchCategory.fadeIn();
        }
    }
});

jQuery(document).on('readyAndAjax', function() {
    $('#searchCategory input').keypress(function(e) {
        if (e.which == 13) {
            $('#searchCategory .btn').trigger('click');
            return false;
        }
    });
});

jQuery(document).on('click', 'button.statusCategory', function(e) {
    e.preventDefault();

    let $element = $(this);
    let $row = $element.closest('[id^=row]');
    $row.btnGroup = $row.find('.responsive-btn-group');
    $row.btn0 = $row.find('button[data-status="0"]');
    $row.btn1 = $row.find('button[data-status="1"]');

    jQuery.ajax({
        url: $element.attr('data-route'),
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        method: 'patch',
        data: {
            status: $element.attr('data-status'),
        },
        beforeSend: function() {
            $row.btnGroup.addClass('disabled');
            $row.append($.getLoader('spinner-border'));
        },
        complete: function() {
            $row.find('div.loader-absolute').remove();
        },
        success: function(response) {

            if (response.status == 1) {
                $row.btnGroup.removeClass('disabled');
                $row.btn1.prop('disabled', true);
                $row.btn0.attr('disabled', false);
                $row.addClass('alert-success');
                setTimeout(function() {
                    $row.removeClassStartingWith('alert-');
                }, 5000);
                $.each(response.ancestors, function(key, value) {
                    let $rowAncestor = $('#row'+value);
                    if ($rowAncestor.length) {
                        $rowAncestor.find('button[data-status="1"]').prop('disabled', true);
                        $rowAncestor.find('button[data-status="0"]').attr('disabled', false);
                        $rowAncestor.addClass('alert-success');
                        setTimeout(function() {
                            $rowAncestor.removeClassStartingWith('alert-');
                        }, 5000);
                    }
                });
            }

            if (response.status == 0) {
                $row.btnGroup.removeClass('disabled');
                $row.btn0.prop('disabled', true);
                $row.btn1.attr('disabled', false);
                $row.addClass('alert-warning');
                setTimeout(function() {
                    $row.removeClassStartingWith('alert-');
                }, 5000);
                $.each(response.descendants, function(key, value) {
                    let $rowDescendant = $('#row'+value);
                    if ($rowDescendant.length) {
                        $rowDescendant.find('button[data-status="0"]').prop('disabled', true);
                        $rowDescendant.find('button[data-status="1"]').attr('disabled', false);
                        $rowDescendant.addClass('alert-warning');
                        setTimeout(function() {
                            $rowDescendant.removeClassStartingWith('alert-');
                        }, 5000);
                    }
                });
            }
        }
    });
});

jQuery(document).on('click', 'a.destroyComment', function(e) {
    e.preventDefault();

    let $element = $(this);
    let $row = $('#row'+$element.attr('data-id'));

    jQuery.ajax({
        url: $element.attr('data-route'),
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        method: 'delete',
        beforeSend: function() {
            $row.find('.responsive-btn-group').addClass('disabled');
            $row.append($.getLoader('spinner-border'));
        },
        complete: function() {
            $row.find('div.loader-absolute').remove();
        },
        success: function(response) {
            $row.fadeOut('slow');
            $.each(response.descendants, function(key, value) {
                let $rowDescendant = $('#row'+value);
                if ($rowDescendant.length) {
                    $rowDescendant.fadeOut('slow');
                }
            });
        }
    });
});

jQuery(document).on('click', 'button.storeComment', function(e) {
    e.preventDefault();

    let $form = $(this).closest('form');
    $form.btn = $form.find('.btn');
    $form.input = $form.find('.form-control');
    let $modal = {};
    $modal.body = $form.closest('.modal-body');

    jQuery.ajax({
        url: $form.attr('data-route'),
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        method: 'post',
        data: $form.serialize(),
        dataType: 'json',
        beforeSend: function() {
            $form.btn.prop('disabled', true);
            $modal.body.append($.getLoader('spinner-border'));
            $('.invalid-feedback').remove();
            $form.input.removeClass('is-valid');
            $form.input.removeClass('is-invalid');
        },
        complete: function() {
            $form.btn.prop('disabled', false);
            $modal.body.find('div.loader-absolute').remove();
            $form.input.addClass('is-valid');
        },
        success: function(response) {
            let $row = $('#row'+$form.attr('data-id'));
            $row.after($.sanitize(response.view));

            let $rowNext = $row.next();
            $rowNext.addClass('alert-primary font-italic');
            setTimeout(function() {
                $rowNext.removeClassStartingWith('alert-');
            }, 5000);
            $('.modal').modal('hide');
        },
        error: function(response) {
            var errors = response.responseJSON;

            $.each(errors.errors, function( key, value ) {
                $form.find('#'+key).addClass('is-invalid');
                $form.find('#'+key).after($.getError(key, value));
            });
        }
    });
});

jQuery(document).on('click', 'button.censoreComment', function(e) {
    e.preventDefault();

    let $element = $(this);
    let $row = $element.closest('[id^=row]');

    jQuery.ajax({
        url: $element.attr('data-route'),
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        method: 'patch',
        data: {
            censored: $element.attr('data-censored'),
        },
        beforeSend: function() {
            $row.find('.btn').prop('disabled', true);
            $row.append($.getLoader('spinner-border'));
        },
        complete: function() {
            $row.find('div.loader-absolute').remove();
        },
        success: function(response) {
            $row.html($.sanitize($(response.view).html()));

            if (response.censored == 1) {
                $row.addClass('alert-warning');
                setTimeout(function() {
                    $row.removeClassStartingWith('alert-');
                }, 5000);
            }

            if (response.censored == 0) {
                $row.addClass('alert-success');
                setTimeout(function() {
                    $row.removeClassStartingWith('alert-');
                }, 5000);
            }
        }
    });
});

jQuery(document).on('click', 'button.statusComment', function(e) {
    e.preventDefault();

    let $element = $(this);
    let $row = $element.closest('[id^=row]');
    $row.btnGroup = $row.find('.responsive-btn-group');
    $row.btn0 = $row.find('button[data-status="0"]');
    $row.btn1 = $row.find('button[data-status="1"]');

    jQuery.ajax({
        url: $element.attr('data-route'),
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        method: 'patch',
        data: {
            status: $element.attr('data-status'),
        },
        beforeSend: function() {
            $row.btnGroup.addClass('disabled');
            $row.append($.getLoader('spinner-border'));
        },
        complete: function() {
            $row.find('div.loader-absolute').remove();
        },
        success: function(response) {

            if (response.status == 1) {
                $row.btnGroup.removeClass('disabled');
                $row.btn1.attr('disabled', true);
                $row.btn0.attr('disabled', false);
                $row.find('button.answer').attr('disabled', false);
                $row.addClass('alert-success');
                setTimeout(function() {
                    $row.removeClassStartingWith('alert-');
                }, 5000);
                $.each(response.ancestors, function(key, value) {
                    let $rowAncestor = $('#row'+value);
                    if ($rowAncestor.length) {
                        $rowAncestor.find('button[data-status="1"]').attr('disabled', true);
                        $rowAncestor.find('button[data-status="0"]').attr('disabled', false);
                        $rowAncestor.find('button.answer').attr('disabled', false);
                        $rowAncestor.addClass('alert-success');
                        setTimeout(function() {
                            $rowAncestor.removeClassStartingWith('alert-');
                        }, 5000);
                    }
                });
            }

            if (response.status == 0) {
                $row.btnGroup.removeClass('disabled');
                $row.btn0.attr('disabled', true);
                $row.btn1.attr('disabled', false);
                $row.find('button.answer').attr('disabled', true);
                $row.addClass('alert-warning');
                setTimeout(function() {
                    $row.removeClassStartingWith('alert-');
                }, 5000);
                $.each(response.descendants, function(key, value) {
                    let $rowDescendant = $('#row'+value);
                    if ($rowDescendant.length) {
                        $rowDescendant.find('button[data-status="0"]').attr('disabled', true);
                        $rowDescendant.find('button[data-status="1"]').attr('disabled', false);
                        $rowDescendant.find('button.answer').attr('disabled', true);
                        $rowDescendant.addClass('alert-warning');
                        setTimeout(function() {
                            $rowDescendant.removeClassStartingWith('alert-');
                        }, 5000);
                    }
                });
            }
        }
    });
});

jQuery(document).on('click', '.create', function(e) {
    e.preventDefault();

    let $element = $(this);
    let $modal = {
        body: $($element.attr('data-target')).find('.modal-body'),
        content: $($element.attr('data-target')).find('.modal-content')
    };

    $modal.body.empty();

    jQuery.ajax({
        url: $element.attr('data-route'),
        method: 'get',
        beforeSend: function() {
            $modal.body.append($.getLoader('spinner-grow'));
        },
        complete: function() {
            $modal.content.find('div.loader-absolute').remove();
        },
        success: function(response) {
            $modal.body.html($.sanitize(response.view));
        }
    });
});

jQuery(document).on('click', '.destroy', function(e) {
    e.preventDefault();

    let $element = $(this);
    let $row = $('#row'+$element.attr('data-id'));

    jQuery.ajax({
        url: $element.attr('data-route'),
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        method: 'delete',
        beforeSend: function() {
            $row.find('.responsive-btn-group').addClass('disabled');
            $row.append($.getLoader('spinner-border'));
        },
        complete: function() {
            $row.find('div.loader-absolute').remove();
        },
        success: function(response) {
            $row.fadeOut('slow');
        }
    });
});

jQuery(document).on('click', '.edit', function(e) {
    e.preventDefault();

    let $element = $(this);
    let $modal = {
        body: $($element.attr('data-target')).find('.modal-body'),
        content: $($element.attr('data-target')).find('.modal-content')
    };

    $modal.body.empty();

    jQuery.ajax({
        url: $element.attr('data-route'),
        method: 'get',
        beforeSend: function() {
            $modal.body.append($.getLoader('spinner-grow'));
        },
        complete: function() {
            $modal.content.find('div.loader-absolute').remove();
        },
        success: function(response) {
            $modal.body.html($.sanitize(response.view));
        }
    });
});

(function($) {
    function ajaxFilter($form, href) {
        $.ajax({
            url: href,
            method: 'get',
            dataType: 'html',
            beforeSend: function() {
                $('#filterContent').find('.btn').prop('disabled', true);
                $('#filterOrderBy').prop('disabled', true);
                $('#filterPaginate').prop('disabled', true);
                $form.children('div').append($.getLoader('spinner-border'));
                $('#filterModal').modal('hide');
            },
            complete: function() {
                $form.find('div.loader-absolute').remove();
            },
            success: function(response) {
                $('#filterContent').html($.sanitize($(response).find('#filterContent').html()));
                document.title = document.title.replace(/:\s(\d+)/, ': 1');
                history.replaceState(null, null, href);
            },
        });
    }

    jQuery(document).on('change', '#filterOrderBy', function(e) {
        e.preventDefault();

        let $form = $('#filter');
        $form.href = $form.attr('data-route')+'?'+$form.serialize();

        ajaxFilter($form, $form.href);
    });

    jQuery(document).on('click', '#filterFilter', function(e) {
        e.preventDefault();

        let $form = $('#filter');
        $form.href = $form.attr('data-route')+'?'+$form.serialize();

        if (jQuery_2_1_3('#filter').valid()) ajaxFilter($form, $form.href);
    });

    jQuery(document).on('click', '.filterOption', function(e) {
        e.preventDefault();

        let $form = $('#filter');
        $form.href = $form.attr('data-route')+'?'+$form.find('[name!="'+$(this).attr('data-name')+'"]').serialize();

        ajaxFilter($form, $form.href);
    });

    jQuery(document).on('change', '#filterPaginate', function(e) {
        e.preventDefault();

        let $form = $('#filter');
        $form.href =  $form.attr('data-route')+'?'+$form.serialize();

        ajaxFilter($form, $form.href);
    });
})(jQuery);

jQuery(document).on('click', 'a.show, button.show', function(e) {
    e.preventDefault();

    let $element = $(this);
    let $modal = {
            body: $($element.attr('data-target')).find('.modal-body'),
            content: $($element.attr('data-target')).find('.modal-content')
    };

    $modal.body.empty();

    jQuery.ajax({
        url: $element.attr('data-route'),
        method: 'get',
        beforeSend: function() {
            $modal.body.append('<div class="loader-absolute"><div class="spinner-grow"><span class="sr-only">Loading...</span></div></div>');
        },
        complete: function() {
            $modal.content.find('div.loader-absolute').remove();
        },
        success: function(response) {
            $modal.body.html($.sanitize(response.view));
        }
    });
});

jQuery(document).on('click', '.store', function(e) {
    e.preventDefault();

    let $form = $(this).closest('form');
    $form.btn = $form.find('.btn');
    $form.input = $form.find('.form-control');
    let $modal = {
        body: $form.closest('.modal-body')
    };

    jQuery.ajax({
        url: $form.attr('data-route'),
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        method: 'post',
        // data: $form.serialize(),
        data: new FormData($form[0]),
        processData: false,
        contentType: false,
        dataType: 'json',
        beforeSend: function() {
            $form.btn.prop('disabled', true);
            $modal.body.append($.getLoader('spinner-border'));
            $('.invalid-feedback').remove();
            $form.input.removeClass('is-valid');
            $form.input.removeClass('is-invalid');
        },
        complete: function() {
            $form.btn.prop('disabled', false);
            $modal.body.find('div.loader-absolute').remove();
            $form.input.addClass('is-valid');
        },
        success: function(response) {
            $('.modal').modal('hide');
            window.location.reload();
        },
        error: function(response) {
            var errors = response.responseJSON;

            $.each(errors.errors, function( key, value ) {
                $form.find('#'+key).addClass('is-invalid');
                $form.find('#'+key).parent().append($.getError(key, value));
            });
        }
    });
});

jQuery(document).on('click', '.update', function(e) {
    e.preventDefault();

    let $form = $(this).closest('form');
    $form.btn = $form.find('.btn');
    $form.input = $form.find('.form-control');
    let $modal = {
        body: $form.closest('.modal-body')
    };

    let data = new FormData($form[0]);
    data.append('_method', 'put');

    jQuery.ajax({
        url: $form.attr('data-route'),
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        method: 'post',
        // data: $form.serialize(),
        data: data,
        processData: false,
        contentType: false,
        dataType: 'json',
        beforeSend: function() {
            $form.btn.prop('disabled', true);
            $modal.body.append($.getLoader('spinner-border'));
            $form.find('.invalid-feedback').remove();
            $form.input.removeClass('is-valid');
            $form.input.removeClass('is-invalid');
        },
        complete: function() {
            $form.btn.prop('disabled', false);
            $modal.body.find('div.loader-absolute').remove();
            $form.input.addClass('is-valid');
        },
        success: function(response) {
            let $row = $('#row'+$form.attr('data-id'));
            $row.html($.sanitize($(response.view).html()));

            $row.addClass('alert-primary');
            setTimeout(function() {
                $row.removeClassStartingWith('alert-');
            }, 5000);
            $('.modal').modal('hide');
        },
        error: function(response) {
            var errors = response.responseJSON;

            $.each(errors.errors, function( key, value ) {
                $form.find('#'+key).addClass('is-invalid');
                $form.find('#'+key).parent().append($.getError(key, value));
            });
        }
    });
});

jQuery(document).on('click', '.status', function(e) {
    e.preventDefault();

    let $element = $(this);
    let $row = $element.closest('[id^=row]');

    jQuery.ajax({
        url: $element.attr('data-route'),
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        method: 'patch',
        data: {
            status: $element.attr('data-status'),
        },
        beforeSend: function() {
            $row.find('.btn').prop('disabled', true);
            $row.append($.getLoader('spinner-border'));
        },
        complete: function() {
            $row.find('div.loader-absolute').remove();
        },
        success: function(response) {
            $row.html($.sanitize($(response.view).html()));

            if (response.status == 1) {
                $row.addClass('alert-success');
                setTimeout(function() {
                    $row.removeClassStartingWith('alert-');
                }, 5000);
            }

            if (response.status == 0) {
                $row.addClass('alert-warning');
                setTimeout(function() {
                    $row.removeClassStartingWith('alert-');
                }, 5000);
            }
        }
    });
});

jQuery(document).on('click', 'a.resetMailing', function(e) {
    e.preventDefault();

    var $element = $(this);
    var $row = $('#row'+$element.attr('data-id'));

    $.ajax({
        url: $element.attr('data-route'),
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        method: 'delete',
        beforeSend: function() {
            $row.find('.btn').prop('disabled', true);
            $row.append($.getLoader('spinner-border'));
        },
        complete: function() {
            $row.find('div.loader-absolute').remove();
        },
        success: function(response) {
            $row.html($.sanitize($(response.view).html()));

            $row.addClass('alert-danger');
            setTimeout(function() {
                $row.removeClassStartingWith('alert-');
            }, 5000);
        }
    });
});

jQuery(document).on('click', 'a.destroyPage', function(e) {
    e.preventDefault();

    let $element = $(this);
    let $row = $('#row'+$element.attr('data-id'));

    jQuery.ajax({
        url: $element.attr('data-route'),
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        method: 'delete',
        beforeSend: function() {
            $row.find('.responsive-btn-group').addClass('disabled');
            $row.append($.getLoader('spinner-border'));
        },
        complete: function() {
            $row.find('div.loader-absolute').remove();
        },
        success: function(response) {
            $row.fadeOut('slow');
            $.each(response.descendants, function(key, value) {
                let $rowDescendant = $('#row'+value);
                if ($rowDescendant.length) {
                    $rowDescendant.fadeOut('slow');
                }
            });
        }
    });
});

jQuery(document).on('click', 'button.updatePositionPage', function(e) {
    e.preventDefault();

    let $element = $(this);
    let $form = $element.closest('form');
    let $modal = {
        body: $element.closest('.modal').find('.modal-body'),
        content: $element.closest('.modal').find('.modal-content')
    };

    $.ajax({
        url: $form.attr('data-route'),
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        method: 'patch',
        data: {
            position: $form.find('#position').val(),
        },
        beforeSend: function() {
            $modal.body.find('.btn').prop('disabled', true);
            $modal.content.append($.getLoader('spinner-border'));
        },
        complete: function() {
            $modal.content.find('div.loader-absolute').remove();
        },
        success: function(response) {
            $('.modal').modal('hide');
            $.each(response.siblings, function(key, value) {
                let $rowSibling = $('#row'+key);
                if ($rowSibling.length) {
                    $rowSibling.find('#position').text(value+1);
                    $rowSibling.addClass('alert-primary');
                    setTimeout(function() {
                        $rowSibling.removeClassStartingWith('alert-');
                    }, 5000);
                }
            });
        }
    });
});

jQuery(document).on('click', 'button.statusPage', function(e) {
    e.preventDefault();

    let $element = $(this);
    let $row = $element.closest('[id^=row]');
    $row.btnGroup = $row.find('.responsive-btn-group');
    $row.btn0 = $row.find('button[data-status="0"]');
    $row.btn1 = $row.find('button[data-status="1"]');

    jQuery.ajax({
        url: $element.attr('data-route'),
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        method: 'patch',
        data: {
            status: $element.attr('data-status'),
        },
        beforeSend: function() {
            $row.btnGroup.addClass('disabled');
            $row.append($.getLoader('spinner-border'));
        },
        complete: function() {
            $row.find('div.loader-absolute').remove();
        },
        success: function(response) {

            if (response.status == 1) {
                $row.btnGroup.removeClass('disabled');
                $row.btn1.prop('disabled', true);
                $row.btn0.attr('disabled', false);
                $row.addClass('alert-success');
                setTimeout(function() {
                    $row.removeClassStartingWith('alert-');
                }, 5000);
                $.each(response.ancestors, function(key, value) {
                    let $rowAncestor = $('#row'+value);
                    if ($rowAncestor.length) {
                        $rowAncestor.find('button[data-status="1"]').prop('disabled', true);
                        $rowAncestor.find('button[data-status="0"]').attr('disabled', false);
                        $rowAncestor.addClass('alert-success');
                        setTimeout(function() {
                            $rowAncestor.removeClassStartingWith('alert-');
                        }, 5000);
                    }
                });
            }

            if (response.status == 0) {
                $row.btnGroup.removeClass('disabled');
                $row.btn0.prop('disabled', true);
                $row.btn1.attr('disabled', false);
                $row.addClass('alert-warning');
                setTimeout(function() {
                    $row.removeClassStartingWith('alert-');
                }, 5000);
                $.each(response.descendants, function(key, value) {
                    let $rowDescendant = $('#row'+value);
                    if ($rowDescendant.length) {
                        $rowDescendant.find('button[data-status="0"]').prop('disabled', true);
                        $rowDescendant.find('button[data-status="1"]').attr('disabled', false);
                        $rowDescendant.addClass('alert-warning');
                        setTimeout(function() {
                            $rowDescendant.removeClassStartingWith('alert-');
                        }, 5000);
                    }
                });
            }
        }
    });
});

jQuery(document).on('click', 'button.clearReport', function(e) {
    e.preventDefault();

    let $element = $(this);
    let $modal = {
        body: $element.closest('.modal').find('.modal-body'),
        content: $element.closest('.modal').find('.modal-content')
    };

    $.ajax({
        url: $element.attr('data-route'),
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        method: 'delete',
        beforeSend: function() {
            $modal.body.find('.btn').prop('disabled', true);
            $modal.body.append($.getLoader('spinner-border'));
        },
        complete: function() {
            $modal.content.find('div.loader-absolute').remove();
        },
        success: function(response) {
            let $row = $('#row'+$element.attr('data-id'));

            $row.html($.sanitize($(response.view).html()));
            $row.addClass('alert-primary');
            setTimeout(function() {
                $row.removeClassStartingWith('alert-');
            }, 5000);
            $('.modal').modal('hide');
        }
    });
});

(function($) {
    $.fn.removeClassStartingWith = function(begin) {
        this.removeClass(function(index, className) {
            return (className.match(new RegExp("\\b" + begin + "\\S+", "g")) || []).join(' ');
        });
    };

    $.sanitize = function(html) {
        let $output = $($.parseHTML('<div>' + html + '</div>', null, false));

        $output.find('*').each(function(index, node) {
            $.each(node.attributes, function() {
                let attrName = this.name;
                let attrValue = this.value;

                if (attrName.indexOf('on') == 0 || attrValue.indexOf('javascript:') == 0) {
                    $(node).removeAttr(attrName);
                }
            });
        });

        return $output.html();
    };

    $.getUrlParameter = function(url, name) {
        return (RegExp(name + '=' + '(.+?)(&|$)').exec(url)||[,null])[1];
    };

    $.getLoader = function(type, loader = 'loader-absolute') {
        return '<div class="'+loader+'"><div class="'+type+'"><span class="sr-only">Loading...</span></div></div>';
    };

    $.getAlert = function(response, type) {
        return $.sanitize('<div class="alert alert-'+type+'" role="alert">'+response+'</div>');
    };

    $.getError = function(key, value) {
        return $.sanitize('<span class="invalid-feedback d-block font-weight-bold" id="error-'+ key+'">'+value+'</span>');
    };
})(jQuery);

jQuery(document).on('readyAndAjax', function() {
    $('[data-toggle=confirmation]').confirmation({
        rootSelector: '[data-toggle=confirmation]',
        copyAttributes: 'data-route data-id',
        singleton: true,
        popout: true,
        onConfirm: function() {
            if ($(this).hasClass('submit')) {
        		$(this).parents('form:first').submit();
            }
        }
    });
});

//$('ul.pagination').hide();
jQuery(document).on('readyAndAjax', function() {
    $('#infinite-scroll').jscroll({
        debug: true,
        autoTrigger: false,
        loadingHtml: $.getLoader('spinner-border', 'loader'),
        loadingFunction: function() {
            $('#is-pagination').first().remove();
        },
        padding: 0,
        nextSelector: 'a#is-next:last',
        contentSelector: '#infinite-scroll',
        pagingSelector: '.pagination',
        callback: function(nextHref) {
            let href = nextHref.split(' ')[0];
            let page = $.getUrlParameter(href, 'page');
            let title = $('a#is-next:last').attr('title').replace(/(\d+)/, '').trim();

            if ($.isNumeric(page)) {
                let regex = new RegExp(title+"\\s(\\d+)");
                document.title = document.title.replace(regex, title+': '+page);
            }

            history.replaceState(null, null, href);
        }
    });
});

jQuery(document).ready(function() {
    $('form#createPost .datepicker, form#editFullPost .datepicker').pickadate({
        clear: '',
        formatSubmit: 'yyyy-m-dd',
        hiddenName: true
    });
    $('form#createMailing .datepicker, form#editMailing .datepicker').pickadate({
        clear: '',
        formatSubmit: 'yyyy-m-dd',
        hiddenName: true,
        min: new Date(),
    });
    $('.timepicker').pickatime({
        clear: '',
        format: 'H:i',
        formatSubmit: 'HH:i',
        hiddenName: true
    });
});

jQuery(document).ready(function() {
    $('.tagsinput').tagsInput({
        placeholder: $('.tagsinput').attr('placeholder'),
        minChars: 3,
        maxChars: 30,
        limit: $('.tagsinput').attr('data-max'),
        validationPattern: new RegExp('^(?:^[a-zA-ZąćęłńóśźżĄĆĘŁŃÓŚŹŻ0-9\u00E0-\u00FC ]+$)$'),
        unique: true,
    });
});

jQuery(document).on('readyAndAjax', function() {
    if (!$('.trumbowyg-box').length) {
        $('#content_html_trumbowyg').trumbowyg({
            lang: 'pl',
            svgPath: false,
            hideButtonTexts: true,
            tagsToRemove: ['script'],
            autogrow: true,
            btnsDef: {
                more: {
                    fn: function() {
                        $('#content_html_trumbowyg').trumbowyg('execCmd', {
                        	cmd: 'insertHtml',
                        	param: '<p>[more]</p>',
                        	forceCss: false,
                        });
                    },
                    title: 'Button "show more"',
                    ico: 'more'
                }
            },
            btns: [
                ['viewHTML'],
                ['historyUndo', 'historyRedo'],
                // ['undo', 'redo'], // Only supported in Blink browsers
                ['formatting'],
                ['foreColor', 'backColor'],
                ['strong', 'em', 'del'],
                ['superscript', 'subscript'],
                ['link'],
                ['insertImage'],
                ['justifyLeft', 'justifyCenter', 'justifyRight', 'justifyFull'],
                ['unorderedList', 'orderedList'],
                ['horizontalRule'],
                ['removeformat'],
                ['more'],
                ['fullscreen']
            ]
        });
    }
});

// // jQuery(document).ready(function() {
// //     var button = $('#buttonFilters');
// //
// //     $('#collapseFilters').on('show.bs.collapse', function() {
// //         $(button).html($(button).attr('data-name-up') + ' <i class="fas fa-angle-up"></i>');
// //     });
// //     $('#collapseFilters').on('hide.bs.collapse', function() {
// //         $(button).html($(button).attr('data-name-down') + ' <i class="fas fa-angle-down"></i>');
// //     });
// // });
//
// (function($) {
//     "use strict"; // Start of use strict
//
//     // // Prevent the content wrapper from scrolling when the fixed side navigation hovered over
//     // $('body.fixed-nav .sidebar').on('mousewheel DOMMouseScroll wheel', function(e) {
//     //     if ($(window).width() > 768) {
//     //         var e0 = e.originalEvent,
//     //             delta = e0.wheelDelta || -e0.detail;
//     //         this.scrollTop += (delta < 0 ? 1 : -1) * 30;
//     //         e.preventDefault();
//     //     }
//     // });
//
// })(jQuery); // End of use strict

jQuery(document).on('readyAndAjax', function() {
    $(".alert-time").delay(20000).fadeOut();
});

jQuery(document).on('readyAndAjax', function() {
  $('[data-toggle="tooltip"]').tooltip();
});

jQuery(document).ready(function() {
    $('[aria-controls="collapsePublishedAt"]').change(function() {
        if ($(this).val() == 0) $('#collapsePublishedAt').collapse('hide');
        else $('#collapsePublishedAt').collapse('show');
    });
    $('[aria-controls="collapseActivationAt"]').change(function() {
        if ($(this).val() == 2) $('#collapseActivationAt').collapse('show');
        else $('#collapseActivationAt').collapse('hide');
    });
});

jQuery(document).on('readyAndAjax', function() {
    $(".custom-file-input").on("change", function() {
        let fileName = $(this).val().split("\\").pop();
        $(this).siblings(".custom-file-label").addClass("selected").html(fileName);
    });
});

jQuery(document).on('click', '#selectAll', function() {
    $('#selectForm .select').prop('checked', $(this).prop('checked')).trigger('change');
});

jQuery(document).on('change', '#selectForm .select', function() {
    if ($('#selectForm .select:checked').length > 0) {
        $('.select-action').fadeIn();
    }
    else {
        $('.select-action').fadeOut();
    }
});

(function($) {
    let c, currentScrollTop = 0;
    let $navbar = $('.navbar');

    $(window).scroll(function() {
        var a = $(window).scrollTop();
        var b = $navbar.height()+10;

        currentScrollTop = a;

        if (c < currentScrollTop && c > b) {
            $navbar.fadeOut();
        } else {
            $navbar.fadeIn();
        }
        c = currentScrollTop;
   });
})(jQuery);

// Scroll to top button appear
$(document).on('scroll', function() {
    var scrollDistance = $(this).scrollTop();
    if (scrollDistance > 100) {
        $('.scroll-to-top').fadeIn();
    } else {
        $('.scroll-to-top').fadeOut();
    }
});

// Smooth scrolling using jQuery easing
$(document).on('click', 'a.scroll-to-top', function(event) {
    var $anchor = $(this);
    $('html, body').stop().animate({
        scrollTop: (0)
    }, 1000, 'easeInOutExpo');
    event.preventDefault();
});

$(document).on('click', ".modal-backdrop, #sidebarToggle", function(e) {
    e.preventDefault();

    // For larger resolutions, the sidebar is always visible (toggled or not)
    if (window.innerWidth >= 768) {
        $(".sidebar").toggleClass("toggled");
        if ($("ul.sidebar").hasClass("toggled")) {
            $.cookie("sidebarToggle", 1, { path: '/admin' });
        } else {
            $.cookie("sidebarToggle", 0, { path: '/admin' });
        }
    }
    // For smaller resolutions, the sidebar is collapse with body backdrop
    else {
        $(".sidebar").removeClass("toggled");
        if ($('.modal-backdrop').length) {
            $('.modal-backdrop').fadeOut('slow', function() {
                $(this).remove();
            });
            $(".sidebar").removeClass("show");
            $('body').removeClass('modal-open');
        } else {
            $('<div class="modal-backdrop show z-900"></div>').appendTo('body').hide().fadeIn();
            $(".sidebar").addClass("show");
            $('body').addClass('modal-open');
        }
    }
});

jQuery(document).on('click', 'div#themeToggle button', function(e) {
    e.preventDefault();

    let $element = $(this);

    if ($element.hasClass('btn-light')) {
        $('link[href*="admin-dark.css"]').attr('href', function() {
            return $(this).attr('href').replace('admin-dark.css', 'admin.css');
        });
        $.cookie("themeToggle", 'light', { path: '/' });
    }

    if ($element.hasClass('btn-dark')) {
        $('link[href*="admin.css"]').attr('href', function() {
            return $(this).attr('href').replace('admin.css', 'admin-dark.css');
        });
        $.cookie("themeToggle", 'dark', { path: '/' });
    }

    $element.prop('disabled', true);
    $element.siblings('button').prop('disabled', false);
});

jQuery(document).ready(function() {
    $('[aria-controls="collapsePayments"]').change(function() {
        if ($(this).val() == 0) $('#collapsePayments').collapse('hide');
        else $('#collapsePayments').collapse('show');
    });
});

jQuery(document).on('change', 'div[id^=prices] div.price:last-child input[name*="select"]', function() {
    if ($(this).prop('checked') === true) {
        let $price = $(this).closest('div.price').clone();
        $price.id = parseInt($(this).attr('id').match(/\d+/), 10) + 1;

        $price.find('[id^=price], [for^=price], [name^=prices]').each(function(index, element) {
            $.each(['id', 'for', 'name'], function(key, value) {
                if ($(element).attr(value)) {
                    $(element).attr(value, $(element).attr(value).replace(/(\d+)/, $price.id));
                }
            });
        });

        $(this).closest('div[id^=prices]').append($.sanitize('<div class="price">' + $price.html() + '</div>'));
    }
});

jQuery(document).on('change', 'div[id^=prices] div.price:not(:first-child) input[name*="select"]', function() {
    if ($(this).prop('checked') === false) {
        $(this).closest('div.price').remove();
    }
});

jQuery(document).on('change', 'div[id^=prices] input[name*="sync"]', function() {
    let $price = {
        textarea: $(this).closest('div.price').find('textarea[name*="codes"]')
    };

    if ($(this).prop('checked') === true) {
        $price.textarea.prop('readonly', false);
    } else {
        $price.textarea.prop('readonly', true);
    }
});
