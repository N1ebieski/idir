<?php

use N1ebieski\IDir\Models\Group;

return [
    'dir' => [
        'dir' => 'Katalog'
    ],
    'success' => [
        'store' => 'Grupa została dodana.',
        'update' => 'Grupa została zaktualizowana.',
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
    'max_models' => 'Maksymalna ilość wpisów w grupie',
    'max_models_daily' => 'Dzienna maksymalna ilość wpisów w grupie',
    'empty' => 'Brak dostępnych grup',
    'payment' => [
        'label' => 'Płatność',
        Group::WITHOUT_PAYMENT => 'darmowa',
        Group::PAYMENT => 'płatna'
    ],
    'alt' => [
        'label' => 'Alternatywna grupa',
        'tooltip' => 'Grupa do której spadnie wpis w przypadku braku przedłużenia okresu czasowego.',
        'null' => 'Brak (po upływie czasu nastąpi deaktywacja ze statusem "oczekujący na płatność")'
    ]
];
