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
        'step' => 'Krok :step'
    ],
    'success' => [
        'store' => [
            'status_0' => 'Wpis został dodany i oczekuje na moderację.',
            'status_1' => 'Wpis został dodany.'
        ]
    ],
    'choose_group' => 'Wybierz grupę',
    'categories' => 'Kategorie',
    'tags' => 'Tagi',
    'tags_tooltip' => 'Min 3 znaki, max 30 znaków, max :max_tags tagów',
    'tags_placeholder' => 'Dodaj tag',
    'choose_payment_type' => 'Wybierz typ płatności',
    'payment_transfer' => 'Przelew online',
    'payment_transfer_info' => 'Płatności internetowe przelewem realizuje <a href=":provider_url" target="_blank">:provider_name</a>.
    Dokumenty dotyczące systemu płatności dostępne są na stronie <a href=":provider_docs_url" target="_blank">:provider_name dokumenty</a>.
    Regulamin usługi dostępny jest na stronie <a href=":provider_rules_url" target="_blank">:provider_name regulamin</a>.
    Zgłoszenie strony do katalogu równoznaczne jest z akceptacją <a href=":rules_url" target="_blank">regulaminu</a>.',
    'payment_auto_sms' => 'Automatyczny kod SMS',
    'price' => ':price PLN / :days :limit'
];
