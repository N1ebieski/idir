<?php

use N1ebieski\IDir\ValueObjects\Price\Type;

return [
    'success' => [
        'store' => 'Price has been added.',
        'update' => 'Price has been updated.',
        'destroy' => 'Price was successfully removed.'
    ],
    'route' => [
        'index' => 'Prices',
        'edit' => 'Edit price',
        'create' => 'Add price',
        'show' => 'Price: :price'
    ],
    'days' => 'Days',
    'price' => 'Price',
    'price_from' => 'paid from :price PLN / :days :limit',
    'unlimited' => 'unlimited',
    'group' => 'Group',
    'code_sms' => 'SMS code',
    'code_transfer' => 'ID',
    'token' => 'Token',
    'number' => 'Number',
    'codes' => 'Manual codes',
    'sync_codes' => 'Sync codes',
    'payment' => [
        'label' => 'Payment method',
        Type::TRANSFER => 'Transfer online',
        Type::CODE_SMS => 'SMS code',
        Type::CODE_TRANSFER => 'Transfer code',
        Type::PAYPAL_EXPRESS => 'PayPal'
    ]
];
