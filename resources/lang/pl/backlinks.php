<?php

/**
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

return [
    'not_found' => 'Nie znaleziono linka zwrotnego',
    'mail' => [
        'not_found' => [
            'info' => ':attempt próba z rzędu nie wykazała prawidłowo wstawionego linka zwrotnego na Twojej stronie. W związku z tym, do czasu przywrócenia linka zwrotnego Twój wpis w naszym katalogu zostanie tymczasowo deaktywowany. Gdy przywrócisz link zwrotny lub zmienisz grupę wpisu bez tego obowiązku, wpis zostanie z powrotem aktywowany.',
            'backlink' => 'Poniżej kod linka zwrotnego, który wybrałeś w formularzu dodawania wpisu:',
            'edit_dir' => 'Przypominamy również o możliwości zmiany grupy bez konieczności obowiązkowego linka zwrotnego. Edycji swojego wpisu dokonasz klikając w przycisk poniżej:'
        ],
        'forbidden' => [
            'title' => 'Bot sprawdzający link zwrotny nie ma dostępu do strony',
            'info' => 'Informujemy, że bot sprawdzający link zwrotny Państwa wpisu :dir_link znajdującego się na: <a href=":dir_page">:dir_page</a> nie uzyskał dostępu do strony pod adresem :dir_url.',
            'result' => 'Przyczyną problemu jest konfiguracja Państwa serwera, która blokuje żądania pochodzące z naszej strony. Skutkiem może być w przyszłości dezaktywacja wpisu w katalogu z powodu nieodnalezienia linka zwrotnego.',
            'solve' => 'Mogą Państwo temu zapobiec zwracając się do admina hostingu z prośbą o odblokowanie dostępu dla bota o parametrach:'
        ]
    ],
    'delay' => 'Odłóż',
    'confirm' => [
        'delay' => 'Czy na pewno chcesz odłożyć następne sprawdzenie i z powrotem aktywować wpis?'
    ],
    'delay_for' => [
        'label' => 'Wybierz ilość dni opóźnienia',
        'custom' => 'Inna wartość'
    ]
];
