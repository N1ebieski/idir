<?php

use N1ebieski\IDir\Models\Group;

return [
    'dir' => [
        'dir' => 'Katalog'
    ],
    'success' => [
        'store' => 'Grupa została dodana.',
        'update' => 'Grupa została edytowana.',
        'destroy' => 'Pomyślnie usunięto grupę.'
    ],
    'route' => [
        'index' => 'Grupy',
        'edit' => 'Edycja grupy',
        'create' => 'Dodaj grupę',
        'edit_position' => 'Edycja pozycji',
        'show' => 'Grupa: :group'
    ],
    'name' => 'Nazwa',
    'border' => [
        'label' => 'Klasa ramki',
        'tooltip' => 'Klasa ramki. Służy do wyróżnienia wpisu na liście.',
        'placeholder' => 'przykład bootstrap 4: border-primary'
    ],
    'max_cats' => [
        'label' => 'Maksymalna ilość kategorii',
        'tooltip' => 'Maksymalna wartość kategorii do której można dodać wpis.'
    ],
    'desc' => 'Opis grupy',
    'visible' => [
        'label' => 'Widoczność',
        'tooltip' => 'Publiczna - widoczna dla wszystkich. Prywatna - widoczna dla ról z uprawnieniem admina.',
        Group::INVISIBLE => 'prywatna',
        Group::VISIBLE => 'publiczna'
    ],
    'backlink' => [
        'label' => 'Link zwrotny',
        Group::WITHOUT_BACKLINK => 'brak',
        Group::OPTIONAL_BACKLINK => 'nieobowiązkowy',
        Group::OBLIGATORY_BACKLINK => 'obowiązkowy'
    ],
    'url' => [
        'label' => 'Adres strony',
        Group::WITHOUT_URL => 'brak',
        Group::OPTIONAL_URL => 'nieobowiązkowy',
        Group::OBLIGATORY_URL => 'obowiązkowy'
    ],
    'apply_status' => [
        'label' => 'Status po dodaniu wpisu',
        Group::APPLY_INACTIVE => 'oczekujący na moderację',
        Group::APPLY_ACTIVE => 'natychmiast aktywny'
    ],
    'days' => 'Dni',
    'price' => 'Cena',
    'max_models' => 'Maksymalna ilość wpisów w grupie',
    'max_models_daily' => 'Dzienna maksymalna ilość wpisów w grupie',
    'empty' => 'Brak dostępnych grup',
    'payment' => [
        'index' => 'Płatność',
        'transfer' => 'Płatności przelewem',
        'code_sms' => 'Płatności przez kody SMS',
        'code_transfer' => 'Płatności przez kody przelewem',
        '0' => 'darmowy',
        '1' => 'płatny'
    ],
    'price_from' => 'płatny już od :price PLN / :days :limit',
    'unlimited' => 'nieograniczony czasowo',
    'alt' => [
        'index' => 'Alternatywna grupa',
        'tooltip' => 'Grupa do której spadnie wpis w przypadku braku przedłużenia okresu czasowego.',
        'null' => 'Brak (po upływie czasu nastąpi deaktywacja ze statusem "oczekujący na płatność")'
    ],
    'code_sms' => 'Kod SMS',
    'code_transfer' => 'Kod ID',
    'token' => 'Token',
    'number' => 'Numer',
    'codes' => 'Kody manualne',
    'sync_codes' => 'Synchronizuj kody'
];
