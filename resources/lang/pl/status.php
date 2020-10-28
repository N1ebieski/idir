<?php

return [
    'delay' => 'Odłóż',
    'confirm' => [
        'delay' => 'Czy na pewno chcesz odłożyć następne sprawdzenie i z powrotem aktywować wpis?'
    ],
    'delay_for' => [
        'label' => 'Wybierz ilość dni opóźnienia',
        'custom' => 'Inna wartość'
    ],
    'mail' => [
        'forbidden' => [
            'title' => 'Bot sprawdzający status nie ma dostępu do strony',
            'info' => 'Informujemy, że bot sprawdzający status Państwa wpisu :dir_link znajdującego się na: <a href=":dir_page">:dir_page</a> nie uzyskał dostępu do strony pod adresem :dir_url.',
            'result' => 'Przyczyną problemu jest konfiguracja Państwa serwera, która blokuje żądania pochodzące z naszej strony. Skutkiem może być w przyszłości dezaktywacja wpisu w katalogu z powodu nieprawidłowego statusu.',
            'solve' => 'Możesz temu zapobiec zwracając się do admina hostingu z prośbą o odblokowanie dostępu dla bota o parametrach:'
        ]
    ]
];
