<?php

return [
    'success' => [
        'store' => 'Grupa została dodana.',
        'update' => 'Grupa została edytowana.',
        'destroy' => 'Pomyślnie usunięto grupę.'
    ],
    'page' => [
        'index' => 'Grupy',
        'type' => [
            'dir' => 'Katalog',
        ],
        'edit' => 'Edycja grupy',
        'create' => 'Dodaj grupę',
        'edit_position' => 'Edycja pozycji',
        'show' => 'Grupa: :group'
    ],
    'name' => 'Nazwa',
    'border' => 'Klasa ramki',
    'border_tooltip' => 'Klasa ramki. Służy do wyróżnienia wpisu na liście.',
    'border_placeholder' => 'przykład bootstrap 4: border-primary',
    'max_cats' => 'Maksymalna ilość kategorii',
    'max_cats_tooltip' => 'Maksymalna wartość kategorii do której można dodać wpis.',
    'desc' => 'Opis grupy',
    'visible' => 'Widoczność',
    'visible_tooltip' => 'Publiczna - widoczna dla wszystkich. Prywatna - widoczna dla ról z uprawnieniem admina.',
    'visible_0' => 'prywatna',
    'visible_1' => 'publiczna',
    'backlink' => 'Link zwrotny',
    'backlink_0' => 'brak',
    'backlink_1' => 'nieobowiązkowy',
    'backlink_2' => 'obowiązkowy',
    'url' => 'Adres strony',
    'url_0' => 'brak',
    'url_1' => 'nieobowiązkowy',
    'url_2' => 'obowiązkowy',
    'apply_status' => 'Status po dodaniu wpisu',
    'apply_status_0' => 'oczekujący na moderację',
    'apply_status_1' => 'natychmiast aktywny',
    'days' => 'Dni',
    'price' => 'Cena',
    'max_models' => 'Maksymalna ilość wpisów w grupie',
    'max_models_daily' => 'Dzienna maksymalna ilość wpisów w grupie',
    'empty' => 'Brak dostępnych grup',
    'payment' => [
        'index' => 'Płatność',
        'transfer' => 'Płatności przelewem',
        'code_sms' => 'Płatności przez kody SMS',
        'code_transfer' => 'Płatności przez kody przelewem'
    ],
    'payment_0' => 'darmowy',
    'payment_1' => 'płatny',
    'price_from' => 'płatny już od :price PLN / :days :limit',
    'unlimited' => 'nieograniczony czasowo',
    'alt' => [
        'index' => 'Alternatywna grupa',
        'tooltip' => 'Grupa do której spadnie wpis w przypadku braku przedłużenia okresu czasowego.',
        'null' => 'Brak (po upływie czasu nastąpi deaktywacja ze statusem "oczekujący na płatność")'
    ],
    'code_sms' => 'Kod SMS',
    'code_transfer' => 'Kod ID',
    'number' => 'Numer',
    'codes' => 'Kody manualne',
    'sync_codes' => 'Synchronizuj kody'
];