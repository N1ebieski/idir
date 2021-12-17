$(document).on(
    'change.n1ebieski/idir/admin/scripts/view/dir@toggleCodeSms',
    'select#payment_code_sms',
    function () {
        let $select = JSON.parse($(this).find('option:selected').attr('data'));

        $('div#nav-code_sms p span#number').text($select.number);
        $('div#nav-code_sms p span#code_sms').text($select.code);
        $('div#nav-code_sms p span#price').text($select.price);
        $('div#nav-code_sms div#qr_image').html($.sanitize($select.qr_as_image));   
    }
);

$(document).on(
    'change.n1ebieski/idir/admin/scripts/view/dir@toggleCodeTransfer',
    'select#payment_code_transfer',
    function () {
        let $select = JSON.parse($(this).find('option:selected').attr('data'));

        $('div#nav-code_transfer p a#code_transfer').attr('href', function () {
            return $(this).attr('href').replace(/=(.*)/, '=' + $select.code).trim();
        });
        $('div#nav-code_transfer p span#price').text($select.price);
    }
);

$(document).on(
    'change.n1ebieski/idir/admin/scripts/view/dir@toggleBacklink',
    'select#backlink',
    function () {
        let $select = JSON.parse($(this).find('option:selected').attr('data'));
        let link_as_html = '<a href="' + $select.url + '" title="' + $select.name + '">';

        if ($select.img_url_from_storage !== null) {
            link_as_html += '<img src="' + $select.img_url_from_storage + '" alt="' + $select.name + '">';
        } else {
            link_as_html += $select.name;
        }

        link_as_html += '</a>';

        $('#backlink_code').val($.sanitize(link_as_html));
        $('#backlink_preview').html($.sanitize(link_as_html));
    }
);
