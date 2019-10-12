<?php

return [

    'layout' => 'idir',

    'dir' => [
        'max_tags' => env('IDIR_DIR_MAX_TAGS', 10),
        'min_content' => env('IDIR_DIR_MIN_CONTENT', 255),
        'max_content' => env('IDIR_DIR_MAX_CONTENT', 500)
    ],

    'payment' => [
        'transfer' => [
            'driver' => 'cashbill'
        ],

        'auto_sms' => [
            'driver' => 'cashbill'
        ],

        'cashbill' => [
            'name' => 'CashBill',
            'url' => 'https://www.cashbill.pl',
            'rules_url' => 'https://www.cashbill.pl/download/regulaminy/Regulamin_Platnosci.pdf',
            'docs_url' => 'https://www.cashbill.pl/pobierz/dokumenty/'
        ]
    ]
];
