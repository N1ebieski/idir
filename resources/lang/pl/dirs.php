<?php

use N1ebieski\IDir\Models\Dir;

return [
    'route' => [
        'index' => 'Katalog',
        'search' => 'Wyszukiwanie: :search',
        'create' => [
            'index' => 'Dodaj wpis',
            '1' => 'Wybierz typ wpisu',
            '2' => 'Wypełnij formularz',
            '3' => 'Podsumowanie'
        ],
        'edit' => [
            'index' => 'Edytuj wpis',
            'renew' => 'Przedłuż ważność',
            '1' => 'Wybierz typ wpisu',
            '2' => 'Wypełnij formularz',
            '3' => 'Podsumowanie'
        ],
        'step' => 'Krok :step'
    ],
    'success' => [
        'store' => [
            Dir::INACTIVE => 'Wpis został dodany i oczekuje na akceptację przez moderatora.',
            Dir::ACTIVE => 'Wpis został dodany i jest aktywny.'
        ],
        'update' => [
            Dir::INACTIVE => 'Wpis został zaktualizowany i oczekuje na akceptację przez moderatora.',
            Dir::ACTIVE => 'Wpis został zaktualizowany i jest aktywny.'
        ],
        'update_status' => [
            Dir::ACTIVE => 'Wpis został aktywowany',
            Dir::INCORRECT_INACTIVE => 'Wpis został zgłoszony do poprawy'
        ],
        'update_renew' => [
            Dir::INACTIVE => 'Dziękujemy. Czas ważności wpisu zostanie przedłużony w momencie akceptacji wpisu przez moderację.',
            Dir::ACTIVE => 'Dziękujemy. Czas ważności wpisu został przedłużony.'
        ],
        'destroy' => 'Wpis został usunięty',
        'destroy_global' => 'Pomyślnie usunięto :affected wpisów'
    ],
    'choose_group' => 'Wybierz grupę',
    'change_group' => 'Zmień grupę',
    'renew_group' => 'Przedłuż ważność',
    'categories' => 'Kategorie',
    'tags' => [
        'label' => 'Tagi',
        'tooltip' => 'Min 3 znaki, max :max_chars znaków, max :max_tags tagów',
        'placeholder' => 'Dodaj tag'
    ],
    'choose_payment_type' => 'Wybierz typ płatności',
    'payment' => [
        'transfer' => [
            'label' => 'Przelew online',
            'info' => 'Płatności internetowe przelewem realizuje <a href=":provider_url" target="_blank" rel="noopener" title=":provider_name">:provider_name</a>. Dokumenty dotyczące systemu płatności dostępne są na stronie <a href=":provider_docs_url" target="_blank" rel="noopener" title=":provider_name dokumenty">:provider_name dokumenty</a>. Regulamin usługi dostępny jest na stronie <a href=":provider_rules_url" target="_blank" rel="noopener" title=":provider_name regulamin">:provider_name regulamin</a>. Zgłoszenie strony do katalogu równoznaczne jest z akceptacją <a href=":rules_url" target="_blank" rel="noopener" title="Regulamin">regulaminu</a>.',
        ],
        'code_sms' => [
            'label' => 'Kod automatyczny SMS',
            'info' => 'Aby otrzymać kod dostępu - wyślij SMS o treści <b><span id="code_sms">:code_sms</span></b> na numer <b><span id="number">:number</span></b>. Koszt SMSa to <b><span id="price">:price</span></b> PLN. Usługa SMS dostępna jest dla wszystkich operatorów sieci komórkowych w Polsce. Płatności SMS w serwisie obsługuje <a href=":provider_url" target="_blank" rel="noopener" title=":provider_name">:provider_name</a>. Dokumenty dotyczące systemu płatności dostępne są na stronie <a href=":provider_docs_url" target="_blank" rel="noopener" title=":provider_name dokumenty">:provider_name dokumenty</a>. Regulamin usługi dostępny jest na stronie <a href=":provider_rules_url" target="_blank" rel="noopener" title=":provider_name regulamin">:provider_name regulamin</a>. Zgłoszenie strony do katalogu równoznaczne jest z akceptacją <a href=":rules_url" target="_blank" rel="noopener" title="Regulamin">regulaminu</a>.'
        ],
        'code_transfer' => [
            'label' => 'Kod automatyczny przelewem',
            'info' => 'Aby otrzymać kod dostępu - dokonaj płatności przelewem na stronie zakupu kodów <a id="code_transfer" href=":code_transfer_url" target="blank" title=":provider_name"><b>:provider_name</b></a>. Koszt to <b><span id="price">:price</span></b> PLN. Dokumenty dotyczące systemu płatności dostępne są na stronie <a href=":provider_docs_url" target="_blank" rel="noopener" title=":provider_name dokumenty">:provider_name dokumenty</a>. Regulamin usługi dostępny jest na stronie <a href=":provider_rules_url" target="_blank" rel="noopener" title=":provider_name regulamin">:provider_name regulamin</a>. Zgłoszenie strony do katalogu równoznaczne jest z akceptacją <a href=":rules_url" target="_blank" rel="noopener" title="Regulamin">regulaminu</a>.'
        ],
        'paypal_express' => [
            'label' => 'PayPal',
            'info' => 'Płatności internetowe realizuje <a href=":provider_url" target="_blank" rel="noopener" title=":provider_name">:provider_name</a>. Dokumenty dotyczące systemu płatności dostępne są na stronie <a href=":provider_docs_url" target="_blank" rel="noopener" title=":provider_name dokumenty">:provider_name dokumenty</a>. Regulamin usługi dostępny jest na stronie <a href=":provider_rules_url" target="_blank" rel="noopener" title=":provider_name regulamin">:provider_name regulamin</a>. Zgłoszenie strony do katalogu równoznaczne jest z akceptacją <a href=":rules_url" target="_blank" rel="noopener" title="Regulamin">regulaminu</a>.',
        ]
    ],
    'price' => ':price :currency / :days :limit',
    'rules' => 'Regulamin',
    'code' => 'Wpisz kod',
    'choose_backlink' => 'Wybierz link zwrotny',
    'backlink_url' => 'Adres z linkiem',
    'group' => 'Grupa',
    'group_limit' => 'Limit wyczerpany (max: :dirs, dzienny: :dirs_today)',
    'unlimited' => 'nielimitowany',
    'status' => [
        'label' => 'Status',
        Dir::ACTIVE => 'aktywny',
        Dir::INACTIVE => 'oczekujący na moderację',
        Dir::PAYMENT_INACTIVE => 'oczekujący na płatność',
        Dir::BACKLINK_INACTIVE => 'oczekujący na backlink',
        Dir::STATUS_INACTIVE => 'oczekujący na status 200',
        Dir::INCORRECT_INACTIVE => 'oczekujący na poprawę'
    ],
    'privileged_to' => 'Data wygaśnięcia',
    'reason' => [
        'label' => 'Powód odrzucenia',
        'custom' => 'Inny powód'
    ],
    'mail' => [
        'delete' => [
            'info' => 'Niestety przykro nam, ale Twój wpis :dir_link został usunięty z naszego katalogu.'
        ],
        'activation' => [
            'info' => 'Gratulujemy, Twój wpis :dir_link został poprawnie dodany do naszego katalogu i znajduje się na stronie: <a href=":dir_page">:dir_page</a>. Zapraszamy do kolejnych wpisów!'
        ],
        'incorrect' => [
            'info' => 'Niestety przykro nam, ale Twój wpis :dir_link nie jest zgodny z naszym regulaminem i wymaga poprawy treści. Do tego czasu pozostanie nieaktywny.',
            'edit_dir' => 'Edycji swojego wpisu dokonasz klikając w przycisk poniżej:'
        ],
        'reminder' => [
            'title' => 'Przypomnienie o wygasającym wpisie',
            'info' => 'Przypominamy, że kończy się czas ważności Twojego wpisu :dir_link znajdującego się na stronie: <a href=":dir_page">:dir_page</a> w grupie :group.',
            'alt' => 'Po upływie czasu ważności :days, wpis zostanie przeniesiony do niższej grupy :alt_group.',
            'deactivation' => 'Po upływie czasu ważności :days, wpis zostanie deaktywowany ze statusem "oczekujący na płatność".',
            'renew_dir' => 'Możesz temu zapobiec przedłużając czas ważności wpisu w aktualnej grupie. Przedłużenia swojego wpisu dokonasz klikając w przycisk poniżej:'
        ],
        'completed' => [
            'title' => 'Koniec czasu ważności',
            'info' => 'Informujemy, że zakończył się czas ważności Twojego wpisu :dir_link w grupie :group.',
            'alt' => 'Tym samym wpis został przeniesiony do niższej grupy :alt_group.',
            'deactivation' => 'Tym samym wpis został deaktywowany ze statusem "oczekujący na płatność".',
            'edit_dir' => 'Możesz odnowić wpis lub zmienić grupę. Edycji swojego wpisu dokonasz klikając w przycisk poniżej:'
        ]
    ],
    'link_dir_page' => 'Podlinkuj swój wpis by przyśpieszyć indeksację',
    'premium_dir' => 'Wyróżnij swój wpis na stronie',
    'content' => 'Opis',
    'author' => 'Autor',
    'reload_thumbnail' => 'Odśwież',
    'check_content' => 'Sprawdź unikalność opisu',
    'rating' => 'Ocena',
    'url' => 'Adres strony',
    'related' => 'Podobne wpisy',
    'latest' => 'Najnowsze wpisy',
    'title' => 'Tytuł',
    'email' => [
        'label' => 'Adres e-mail',
        'tooltip' => 'Na ten adres zostanie założone konto użytkownika. Jesli masz już konto - zaloguj się.'
    ],
    'notes' => 'Uwagi moderatora',
    'more' => 'pokaż więcej &raquo',
    'correct' => 'Poprawa',
    'confirm' => [
        'correct' => 'Czy na pewno chcesz zgłosić wpis do poprawy?'
    ]
];
