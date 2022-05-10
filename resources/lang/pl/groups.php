<?php

use N1ebieski\IDir\ValueObjects\Group\Url;
use N1ebieski\IDir\ValueObjects\Group\Payment;
use N1ebieski\IDir\ValueObjects\Group\Visible;
use N1ebieski\IDir\ValueObjects\Group\Backlink;
use N1ebieski\IDir\ValueObjects\Group\ApplyStatus;

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
        Visible::INACTIVE => 'prywatna',
        Visible::ACTIVE => 'publiczna'
    ],
    'backlink' => [
        'label' => 'Link zwrotny',
        Backlink::INACTIVE => 'brak',
        Backlink::OPTIONAL => 'nieobowiązkowy',
        Backlink::ACTIVE => 'obowiązkowy'
    ],
    'url' => [
        'label' => 'Adres strony',
        Url::INACTIVE => 'brak',
        Url::OPTIONAL => 'nieobowiązkowy',
        Url::ACTIVE => 'obowiązkowy'
    ],
    'apply_status' => [
        'label' => 'Status po dodaniu/edycji wpisu',
        ApplyStatus::INACTIVE => 'oczekujący na moderację',
        ApplyStatus::ACTIVE => 'natychmiast aktywny'
    ],
    'max_models' => 'Maksymalna ilość wpisów w grupie',
    'max_models_daily' => 'Dzienna maksymalna ilość wpisów w grupie',
    'empty' => 'Brak dostępnych grup',
    'payment' => [
        'label' => 'Płatność',
        Payment::INACTIVE => 'darmowa',
        Payment::ACTIVE => 'płatna'
    ],
    'alt' => [
        'label' => 'Alternatywna grupa',
        'tooltip' => 'Grupa do której spadnie wpis w przypadku braku przedłużenia okresu czasowego.',
        'null' => 'Brak (po upływie czasu nastąpi dezaktywacja ze statusem "oczekujący na płatność")'
    ]
];
