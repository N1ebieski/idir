<?php

return [

    'layout' => 'idir',

    'dir' => [
        'max_tags' => env('IDIR_DIR_MAX_TAGS', 10),
        'min_content' => env('IDIR_DIR_MIN_CONTENT', 255),
        'max_content' => env('IDIR_DIR_MAX_CONTENT', 500),

        'backlink' => [
            'check_hours' => env('IDIR_DIR_BACKLINK_CHECK_HOURS', 24),
            'max_attempts' => env('IDIR_DIR_BACKLINK_MAX_ATTEMPTS', 3),
        ],

        'delete_reasons' => [
            'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
            'Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.',
            'Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.',
            'Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.'
        ]
    ],

    'payment' => [
        'transfer' => [
            'driver' => 'cashbill'
        ],

        'code_sms' => [
            'driver' => 'cashbill'
        ],

        'code_transfer' => [
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
