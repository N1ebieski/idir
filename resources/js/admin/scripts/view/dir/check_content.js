jQuery(document).on('click', '.checkContent', function(e) {
    e.preventDefault();

    let sentence = $(this).parent().find('[id^="content"]').text().split(".").filter(n => n);
    let i, j = 0;
    let content = '';

    for (i = 0; i < 50; i++) {
        if (j === 0) {
            j = Math.floor(Math.random() * sentence.length);
        }

        if (typeof sentence[j] !== 'undefined') {
            content += sentence[j].trim() + '. ';
            j++;
        } else {
            content = '';
            j = 0;
        }

        if (content.length > 150) {
            window.open(
                'http://www.google.pl/search?hl=pl&q=' + encodeURI(content), 
                'checkContent', 
                'resizable=yes,status=no,scrollbars=yes,width=1366,height=768'
            ).focus();

            break;
        }
    }

    return false;
});