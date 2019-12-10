jQuery(document).ready(function() {
    $(document).trigger('readyAndAjax');
});

jQuery(document).ajaxComplete(function() {
    $(document).trigger('readyAndAjax');
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
            $searchCategory.find('.invalid-feedback').remove();
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

    if ($.isNumeric($searchCategory.max)) {
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

function ajaxCreateComment($element) {
    let $comment = $element.closest('[id^=comment]');
    $.ajax({
        url: $element.attr('data-route'),
        method: 'get',
        beforeSend: function() {
            $element.prop('disabled', true);
            $comment.append($.getLoader('spinner-border', 'loader'));
        },
        complete: function() {
            $element.prop('disabled', false);
            $comment.find('div.loader').remove();
            $comment.find('.captcha').recaptcha();
        },
        success: function(response) {
            $comment.children('div').append($.sanitize(response.view));
        },
        error: function(response) {
            if (response.responseJSON.message) {
                $comment.children('div').prepend($.getAlert(response.responseJSON.message, 'danger'));
            }
        }
    });
}

jQuery(document).on('click', 'a.createComment', function(e) {
    e.preventDefault();

    let $form = $(this).closest('[id^=comment]').find('form#createComment');

    if ($form.length > 0) {
        $form.fadeToggle();
    } else {
        ajaxCreateComment($(this));
    }
});

jQuery(document).on('click', 'a.editComment', function(e) {
    e.preventDefault();

    let $element = $(this);
    let $comment = $element.closest('[id^=comment]');

    $.ajax({
        url: $element.attr('data-route'),
        method: 'get',
        beforeSend: function() {
            $comment.children('div').hide();
            $comment.append($.getLoader('spinner-border', 'loader'));
        },
        complete: function() {
            $comment.find('div.loader').remove();
        },
        success: function(response) {
            $comment.append($.sanitize(response.view));
        },
        error: function(response) {
            $comment.children('div').show();
            if (response.responseJSON.message) {
                $comment.children('div').prepend($.getAlert(response.responseJSON.message, 'danger'));
            }
        }
    });
});

jQuery(document).on('click', 'button.editCommentCancel', function(e) {
    e.preventDefault();

    let $comment = $(this).closest('[id^=comment]');

    $comment.children('div').show();
    $comment.find('form#editComment').remove();
});

(function($) {
    let ajaxFilterComment = function($form, href) {
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
                $('div#comment').find('.captcha').recaptcha();
            },
            success: function(response) {
                $('#filterContent').html($.sanitize($(response).find('#filterContent').html()));
                document.title = document.title.replace(/:\s(\d+)/, ': 1');
                history.replaceState(null, null, href);
            },
        });
    };

    jQuery(document).on('change', '#filterCommentOrderBy', function(e) {
        e.preventDefault();

        let $form = $('#filter');
        $form.href = $form.attr('data-route')+'?'+$form.serialize();

        ajaxFilterComment($form, $form.href);
    });
})(jQuery);

jQuery(document).on('click', 'a.rateComment', function(e) {
    e.preventDefault();

    let $element = $(this);
    let $ratingComment = $element.closest('[id^=comment]').find('span.rating');

    $.ajax({
        url: $element.attr('data-route'),
        method: 'get',
        beforeSend: function() {
        },
        complete: function() {
            $ratingComment.addClass('font-weight-bold');
        },
        success: function(response) {
            $ratingComment.text(response.sum_rating);
        }
    });
});

jQuery(document).on('click', 'button.storeComment', function(e) {
    e.preventDefault();

    let $form = $(this).closest('form');
    $form.btn = $form.find('.btn');
    $form.input = $form.find('.form-control');

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
            $form.append($.getLoader('spinner-border'));
            $('.invalid-feedback').remove();
            $form.input.removeClass('is-valid');
            $form.input.removeClass('is-invalid');
        },
        complete: function() {
            $form.btn.prop('disabled', false);
            $form.find('div.loader-absolute').remove();
            $form.input.addClass('is-valid');
            $form.find('.captcha').recaptcha();
            $form.find('.captcha').captcha();
        },
        success: function(response) {
            if (response.view) {
                $form.closest('[id^=comment]').after($.sanitize(response.view));

                let $comment = $form.closest('[id^=comment]').next('div');

                $comment.addClass('alert-primary font-italic border-bottom');
                setTimeout(function() {
                    $comment.removeClassStartingWith('alert-');
                }, 5000);
            }

            if (response.success) {
                $form.before($.getAlert(response.success, 'success'));
            }

            if ($form.find('#parent_id').val() != 0) {
                $form.remove();
            } else {
                $form.find('#content').val('');
            }
        },
        error: function(response) {

            if (response.responseJSON.errors) {
                $.each(response.responseJSON.errors, function(key, value) {
                    $form.find('[name="'+key+'"]').addClass('is-invalid');
                    $form.find('[name="'+key+'"]').closest('.form-group').append($.getError(key, value));
                });
                return;
            }

            if (response.responseJSON.message) {
                $form.prepend($.getAlert(response.responseJSON.message, 'danger'));
            }
        }
    });
});

jQuery(document).on('click', 'a.takeComment', function(e) {
    e.preventDefault();

    let $element = $(this);
    let $depth = $element.closest('[id^=depth]');
    let $div = $element.closest('div');

    $.ajax({
        url: $element.attr('data-route'),
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        method: 'post',
        data: {
            // Pobieramy IDki wcześniejszych komentarzy i podajemy je do backendu,
            // żeby wykluczył je z paginacji
            except: $depth.children('[id^=depth]').map(function(){
                return $(this).attr('data-id');
            }).get(),
            orderby: $element.closest('#filterContent').find('#filterCommentOrderBy').val()
        },
        beforeSend: function() {
            $element.hide();
            $div.append($.getLoader('spinner-border', 'loader'));
        },
        complete: function() {
            $div.find('div.loader').remove();
        },
        success: function(response) {
            $depth.append($.sanitize(response.view));
        }
    });
});

jQuery(document).on('click', 'button.updateComment', function(e) {
    e.preventDefault();

    let $form = $(this).closest('form');
    $form.btn = $form.find('.btn');
    $form.input = $form.find('.form-control');

    jQuery.ajax({
        url: $form.attr('data-route'),
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        method: 'put',
        data: $form.serialize(),
        dataType: 'json',
        beforeSend: function() {
            $form.btn.prop('disabled', true);
            $form.append($.getLoader('spinner-border'));
            $('.invalid-feedback').remove();
            $form.input.removeClass('is-valid');
            $form.input.removeClass('is-invalid');
        },
        complete: function() {
            $form.btn.prop('disabled', false);
            $form.find('div.loader-absolute').remove();
            $form.input.addClass('is-valid');
        },
        success: function(response) {
            let $comment = $form.closest('[id^=comment]');
            $comment.html($.sanitize($(response.view).html()));
            $comment.addClass('alert-primary');
            setTimeout(function() {
                $comment.removeClassStartingWith('alert-');
            }, 5000);
        },
        error: function(response) {
            if (response.responseJSON.errors) {
                $.each(response.responseJSON.errors, function( key, value ) {
                    $form.find('[name="'+key+'"]').addClass('is-invalid');
                    $form.find('[name="'+key+'"]').closest('.form-group').append($.getError(key, value));
                });
                return;
            }

            if (response.responseJSON.message) {
                $form.prepend($.sanitize($.getAlert(response.responseJSON.message, 'danger')));
            }
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
        $form.href = $form.attr('data-route')+'?'+$form.find('[name!='+$(this).attr('data-name')+']').serialize();

        ajaxFilter($form, $form.href);
    });

    jQuery(document).on('change', '#filterPaginate', function(e) {
        e.preventDefault();

        let $form = $('#filter');
        $form.href =  $form.attr('data-route')+'?'+$form.serialize();

        ajaxFilter($form, $form.href);
    });
})(jQuery);

jQuery(document).on('click', '.storeNewsletter', function(e) {
    e.preventDefault();

    let $form = $(this).parents('form');
    $form.btn = $form.find('.btn');
    $form.group = $form.find('.form-group');
    $form.input = $form.find('.form-control');

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
            $form.group.append($.getLoader('spinner-border'));
            $('.invalid-feedback').remove();
            $('.valid-feedback').remove();
            $form.input.removeClass('is-valid');
            $form.input.removeClass('is-invalid');
        },
        complete: function() {
            $form.btn.prop('disabled', false);
            $form.group.find('div.loader-absolute').remove();
            $form.input.addClass('is-valid');
        },
        success: function(response) {
            if (response.success) {
                $form.find('[name="email"]').val('');
                $form.find('[name="email"]').closest('.form-group').append($.getMessage(response.success));
            }
        },
        error: function(response) {
            if (response.responseJSON.errors) {
                $.each(response.responseJSON.errors, function( key, value ) {
                    $form.find('[name="'+key+'"]').addClass('is-invalid');
                    $form.find('[name="'+key+'"]').closest('.form-group').append($.getError(key, value));
                });
            }
        }
    });
});

jQuery(document).on('click', 'a.createReport', function(e) {
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
            $modal.body.html($.getLoader('spinner-grow'));
        },
        complete: function() {
            $modal.content.find('div.loader-absolute').remove();
        },
        success: function(response) {
            $modal.body.html($.sanitize(response.view));
        }
    });
});

jQuery(document).on('click', 'button.storeReport', function(e) {
    e.preventDefault();

    let $form = $(this).closest('form');
    $form.btn = $form.find('.btn');
    $form.input = $form.find('.form-control');
    let $modal = {
        body: $form.closest('.modal-body')
    };

    $.ajax({
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
            let errors = response.responseJSON;

            $.each(errors.errors, function(key, value) {
                $form.find('#'+key).addClass('is-invalid');
                $form.find('#'+key).after($.getError(key, value));
            });
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

    /**
     * Plugin refreshujący recaptche v2. Potrzebne w przypadku pobrania formularza przez ajax
     */
    $.fn.recaptcha = function() {
        if (this.hasClass('g-recaptcha')) {
            var widgetId;
            // Przypadek, gdy nowy token generowany jest w momencie pobrania formularza
            // przez ajax. Wówczas trzeba go na nowo zrenderować pod nowym widgetId
            if (!this.html().length) {
                widgetId = grecaptcha.render(this[0], {
                    'sitekey' : this.attr('data-sitekey')
                });
            }
            // W przeciwnym razie (tzn. jeśli token jest prawidłowo wygenerowany) pobieramy
            // jego widgetId
            else {
                widgetId = parseInt(this.find('textarea[name="g-recaptcha-response"]').attr('id').match(/\d+$/), 10);
            }

            // Resetowanie tokena. Konieczne w przypadku gdy formularz został wypełniony
            // błędnie, ajax zwrócił errory, bez nowego formularza. W takim przypadku
            // recaptcha nie rozpozna już wcześniejszego rozwiązania, trzeba zresetować i
            // dać użytkownikowi możliwość ponownego przesłania formularza
            if (Number.isInteger(widgetId)) grecaptcha.reset(widgetId);
            else grecaptcha.reset();
        }
    };

    $.fn.captcha = function() {
        if (this.hasClass('logic_captcha')) {
            this.find('input[name="captcha"]').val('');
            this.find('.reload_captcha_base64').trigger('click');
        }
    };

    $.getLoader = function(type, loader = 'loader-absolute') {
        return '<div class="'+loader+'"><div class="'+type+'"><span class="sr-only">Loading...</span></div></div>';
    };

    $.getAlert = function(response, type) {
        return $.sanitize('<div class="alert alert-'+type+' alert-time" role="alert">'+response+'</div>');
    };

    $.getError = function(key, value) {
        return $.sanitize('<span class="invalid-feedback d-block font-weight-bold" id="error-'+ key+'">'+value+'</span>');
    };

    $.getMessage = function(response) {
        return $.sanitize('<span class="valid-feedback d-block font-weight-bold">'+response+'</span>');
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
    $('.tagsinput').tagsInput({
        placeholder: $('.tagsinput').attr('placeholder'),
        minChars: 3,
        maxChars: 30,
        limit: $('.tagsinput').attr('data-max'),
        validationPattern: new RegExp('^(?:^[a-zA-ZąćęłńóśźżĄĆĘŁŃÓŚŹŻ0-9\u00E0-\u00FC ]+$)$'),
        unique: true,
    });
});

(function($) {
    let typeahead = function() {
        let $input = $("#typeahead");
        let $form = $input.closest('form');

        let engine = new Bloodhound({
            remote: {
                url: $input.attr('data-route')+'?search=%QUERY%',
                wildcard: '%QUERY%'
            },
            datumTokenizer: Bloodhound.tokenizers.whitespace('search'),
            queryTokenizer: Bloodhound.tokenizers.whitespace
        });

        $input.typeahead({
            hint: true,
            highlight: true,
            minLength: 3
        }, {
            source: engine.ttAdapter(),
            display: function(data) {
                return $($.parseHTML(data.name)).text();
            },
            templates: {
                suggestion: function(data) {
                    let name = $($.parseHTML(data.name)).text();
                    let href = $form.attr('action')+'?source='+$form.find('select[name="source"]').val()+'&search='+name;

                    return $.sanitize('<a href="'+href+'" class="list-group-item py-2 text-truncate">'+name+'</a>');
                }
            }
        });
    };

    jQuery(document).ready(function() {
        $.when( typeahead() ).then(function() {
            $("input.tt-input").css('background-color', '');
        });
    });
})(jQuery);

jQuery(document).on('readyAndAjax', function() {
    $(".alert-time").delay(20000).fadeOut();
});

jQuery(document).on('readyAndAjax', function() {
  $('[data-toggle="tooltip"]').tooltip();
});

jQuery(document).on('readyAndAjax', function() {
    $(".custom-file-input").on("change", function() {
        let fileName = $(this).val().split("\\").pop();
        $(this).siblings(".custom-file-label").addClass("selected").html(fileName);
    });
});

jQuery(document).ready(function() {
    let c, currentScrollTop = 0;
    let $navbar = $('.navbar');

    $(window).scroll(function () {
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
});

jQuery(document).on('click', ".modal-backdrop, #navbarToggle", function(e) {
    e.preventDefault();

    if ($('.modal-backdrop').length) {
        $('.navbar-collapse').collapse('hide');
        $('.modal-backdrop').fadeOut('slow', function() {
            $(this).remove();
        });
        $('body').removeClass('modal-open');
    } else {
        $('.navbar-collapse').collapse('show');
        $('<div class="modal-backdrop show z-900"></div>').appendTo('body').hide().fadeIn();
        $('body').addClass('modal-open');
    }
});

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
    $('html, body').stop().animate({
        scrollTop: (0)
    }, 1000, 'easeInOutExpo');
    event.preventDefault();
});

$(document).on('click', '.search-toggler', function(e) {
    e.preventDefault();

    if (window.innerWidth >= 768) {
        $('#pagesToggle').fadeToggle(0);
    } else {
        $('#navbarLogo').fadeToggle(0);
        $('#navbarToggle').fadeToggle(0);
    }
    $('#searchForm').fadeToggle(0);
    $('.search-toggler').find('i').toggleClass("fa-search fa-times");
});

$(document).ready(function() {
    let $form = $('form#searchForm');
    $form.btn = $form.find('button');

    $form.find('input[name="search"]').keyup(function(e) {
        if ($(this).val().trim().length >= 3) {
            $form.btn.prop('disabled', false);
        } else {
            $form.btn.prop('disabled', true);
        }
    });
});

jQuery(document).on('click', 'div#themeToggle button', function(e) {
    e.preventDefault();

    let $element = $(this);

    if ($element.hasClass('btn-light')) {
        $('link[href*="web-dark.css"]').attr('href', function() {
            return $(this).attr('href').replace('web-dark.css', 'web.css');
        });
        $.cookie("themeToggle", 'light', { path: '/' });
    }

    if ($element.hasClass('btn-dark')) {
        $('link[href*="web.css"]').attr('href', function() {
            return $(this).attr('href').replace('web.css', 'web-dark.css');
        });
        $.cookie("themeToggle", 'dark', { path: '/' });
    }

    $element.prop('disabled', true);
    $element.siblings('button').prop('disabled', false);
});

jQuery(document).on('readyAndAjax', function() {
    if (!$('.trumbowyg-box').length) {
        $('#content_html_dir_trumbowyg').trumbowyg({
            lang: 'pl',
            svgPath: false,
            hideButtonTexts: true,
            tagsToRemove: ['script'],
            autogrow: true,
            btnsDef: {},
            btns: [
                ['viewHTML'],
                ['historyUndo', 'historyRedo'],
                // ['undo', 'redo'], // Only supported in Blink browsers
                ['formatting'],
                ['foreColor', 'backColor'],
                ['strong', 'em', 'del'],
                // ['superscript', 'subscript'],
                // ['link'],
                // ['insertImage'],
                // ['justifyLeft', 'justifyCenter', 'justifyRight', 'justifyFull'],
                ['unorderedList', 'orderedList'],
                // ['horizontalRule'],
                ['removeformat'],
                // ['more'],
                ['fullscreen']
            ]
        });
    }
});

jQuery(document).on('change', 'form input[id^=delete_img]', function() {
    $input = $(this).closest('.form-group').find('[type="file"]');
    $hidden = $(this).closest('.form-group').find('[type="hidden"]');

    if ($(this).prop('checked') === true) {
        $input.prop('disabled', false);
        $hidden.prop('disabled', true);
    } else {
        $input.prop('disabled', true);
        $hidden.prop('disabled', false);
    }
});

jQuery(document).on('change', 'select#payment_code_sms', function() {
    let $select = $.parseJSON($(this).find('option:selected').attr('data'));

    $('div#nav-code_sms p span#number').text($select.number);
    $('div#nav-code_sms p span#code_sms').text($select.code);
    $('div#nav-code_sms p span#price').text($select.price);
});

jQuery(document).on('change', 'select#payment_code_transfer', function() {
    let $select = $.parseJSON($(this).find('option:selected').attr('data'));

    $('div#nav-code_transfer p a#code_transfer').attr('href', function() {
        return $(this).attr('href').replace(/=(.*)/, '=' + $select.code).trim();
    });
    $('div#nav-code_transfer p span#price').text($select.price);
});

jQuery(document).on('change', 'select#backlink', function() {
    let $select = $.parseJSON($(this).find('option:selected').attr('data'));
    let link_as_html = '<a href="' + $select.url + '" title="' + $select.name + '">';

    if ($select.img_url_from_storage !== null) {
        link_as_html += '<img src="' + $select.img_url_from_storage + '" alt="' + $select.name + '">';
    } else {
        link_as_html += $select.name;
    }

    link_as_html += '</a>';

    $('#backlink_code').val($.sanitize(link_as_html));
});

jQuery(document).ready(function() {
    if ($('form#transfer_redirect').length) {
        let counter = 5;
        let interval = setInterval(function() {
            counter--;
            $('form#transfer_redirect button #counter').text(counter);
            if (counter === 0) {
                $('form#transfer_redirect').submit();
                clearInterval(interval);
            }
        }, 1000);
    }
});
