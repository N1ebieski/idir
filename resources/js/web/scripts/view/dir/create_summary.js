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
