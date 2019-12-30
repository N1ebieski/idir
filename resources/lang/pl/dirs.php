<?php

return [
    'page' => [
        'index' => 'Katalog',
        'create' => [
            'index' => 'Dodaj wpis',
            'group' => 'Wybierz typ wpisu',
            'form' => 'Wypełnij formularz',
            'summary' => 'Podsumowanie'
        ],
        'edit' => [
            'index' => 'Edytuj wpis',
            'renew' => 'Przedłuż ważność',
            'group' => 'Wybierz typ wpisu',
            'form' => 'Wypełnij formularz',
            'summary' => 'Podsumowanie'
        ],
        'step' => 'Krok :step'
    ],
    'success' => [
        'store' => [
            'status_0' => 'Wpis został dodany i oczekuje na akceptację przez moderatora.',
            'status_1' => 'Wpis został dodany i jest aktywny.'
        ],
        'update' => [
            'status_0' => 'Wpis został edytowany i oczekuje na akceptację przez moderatora.',
            'status_1' => 'Wpis został edytowany i jest aktywny.'
        ],
        'update_status' => [
            'status_1' => 'Wpis został aktywowany'
        ],
        'update_renew' => [
            'status_0' => 'Dziękujemy. Czas ważności wpisu zostanie przedłużony w momencie akceptacji wpisu przed moderację.',
            'status_1' => 'Dziękujemy. Czas ważności wpisu został przedłużony.'
        ],
        'destroy' => 'Wpis został usunięty',
        'destroy_global' => 'Pomyślnie usunięto :affected wpisów'
    ],
    'choose_group' => 'Wybierz grupę',
    'change_group' => 'Zmień grupę',
    'renew_group' => 'Przedłuż ważność',
    'categories' => 'Kategorie',
    'tags' => 'Tagi',
    'tags_tooltip' => 'Min 3 znaki, max 30 znaków, max :max_tags tagów',
    'tags_placeholder' => 'Dodaj tag',
    'choose_payment_type' => 'Wybierz typ płatności',
    'payment' => [
        'transfer' => 'Przelew online',
        'transfer_info' => 'Płatności internetowe przelewem realizuje <a href=":provider_url" target="_blank">:provider_name</a>.
        Dokumenty dotyczące systemu płatności dostępne są na stronie <a href=":provider_docs_url" target="_blank">:provider_name dokumenty</a>.
        Regulamin usługi dostępny jest na stronie <a href=":provider_rules_url" target="_blank">:provider_name regulamin</a>.
        Zgłoszenie strony do katalogu równoznaczne jest z akceptacją <a href=":rules_url" target="_blank">regulaminu</a>.',
        'code_sms' => 'Kod automatyczny SMS',
        'code_sms_info' => 'Aby otrzymać kod dostępu - wyślij SMS na numer <b><span id="number">:number</span></b> o treści
        <b><span id="code_sms">:code_sms</span></b>. Koszt SMSa to <b><span id="price">:price</span></b> PLN. Usługa SMS dostępna jest dla wszystkich operatorów
        sieci komórkowych w Polsce. Płatności SMS w serwisie obsługuje <a href=":provider_url" target="_blank">:provider_name</a>.
        Dokumenty dotyczące systemu płatności dostępne są na stronie <a href=":provider_docs_url" target="_blank">:provider_name dokumenty</a>.
        Regulamin usługi dostępny jest na stronie <a href=":provider_rules_url" target="_blank">:provider_name regulamin</a>.
        Zgłoszenie strony do katalogu równoznaczne jest z akceptacją <a href=":rules_url" target="_blank">regulaminu</a>.',
        'code_transfer' => 'Kod automatyczny przelewem',
        'code_transfer_info' => 'Aby otrzymać kod dostępu - dokonaj płatności przelewem na stronie zakupu kodów
        <a id="code_transfer" href=":code_transfer_url" target="blank"><b>:provider_name</b></a>. Koszt <b><span id="price">:price</span></b> PLN.
        Dokumenty dotyczące systemu płatności dostępne są na stronie <a href=":provider_docs_url" target="_blank">:provider_name dokumenty</a>.
        Regulamin usługi dostępny jest na stronie <a href=":provider_rules_url" target="_blank">:provider_name regulamin</a>.
        Zgłoszenie strony do katalogu równoznaczne jest z akceptacją <a href=":rules_url" target="_blank">regulaminu</a>.'
    ],
    'price' => ':price PLN / :days :limit',
    'rules' => 'Regulamin',
    'code' => 'Wpisz kod',
    'choose_backlink' => 'Wybierz link zwrotny',
    'backlink_url' => 'Adres z linkiem',
    'group' => 'Grupa',
    'group_limit' => 'Limit wyczerpany (max: :dirs, dzienny: :dirs_today)',
    'unlimited' => 'nieograniczony',
    'status' => [
        'index' => 'Status',
        '1' => 'aktywny',
        '0' => 'oczekujący na moderację',
        '2' => 'oczekujący na płatość',
        '3' => 'oczekujący na backlink',
    ],
    'privileged_to' => 'Data wygaśnięcia',
    'delete_reason' => 'Powód usunięcia',
    'mail' => [
        'delete' => [
            'info' => 'Niestety przykro nam, ale Twój wpis :dir_link został usunięty z naszego katalogu.'
        ],
        'activation' => [
            'info' => 'Gratulujemy, Twój wpis :dir_link został poprawnie dodany do naszego katalogu
            i znajduje się na stronie: :dir_page. Zapraszamy do kolejnych wpisów!'
        ]
    ],
    'link_dir_page' => 'Podlinkuj swój wpis by przyśpieszyć indeksację',
    'content' => 'Opis',
    'author' => 'Autor'
];
