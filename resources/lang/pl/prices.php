<?php

use N1ebieski\IDir\ValueObjects\Price\Type;

return [
    'success' => [
        'store' => 'Cena została dodana.',
        'update' => 'Cena została zaktualizowana.',
        'destroy' => 'Pomyślnie usunięto cenę.'
    ],
    'route' => [
        'index' => 'Ceny',
        'edit' => 'Edycja ceny',
        'create' => 'Dodaj cenę',
        'show' => 'Cena: :price'
    ],
    'days' => 'Dni',
    'price' => 'Cena',
    'discount_price' => [
        'label' => 'Cena ze zniżką',
        'tooltip' => 'W przypadku braku zniżki, pozostawić puste',
    ],
    'price_from' => 'od :price :currency <small class="text-muted">/ :days :limit</small>',
    'unlimited' => 'nielimitowany',
    'group' => 'Grupa',
    'code_sms' => 'Kod SMS',
    'code_transfer' => 'Kod ID',
    'token' => 'Token',
    'number' => 'Numer',
    'codes' => 'Kody manualne',
    'sync_codes' => 'Synchronizuj kody',
    'payment' => [
        'label' => 'Metoda płatności',
        Type::TRANSFER => 'Przelew online',
        Type::CODE_SMS => 'Kody SMS',
        Type::CODE_TRANSFER => 'Kody przelewem',
        Type::PAYPAL_EXPRESS => 'PayPal'
    ]
];
