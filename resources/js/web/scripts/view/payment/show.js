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
