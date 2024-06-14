<?php

/**
 * NOTICE OF LICENSE
 *
 * This source file is licenced under the Software License Agreement
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://intelekt.net.pl/pages/regulamin
 *
 * With the purchase or the installation of the software in your application
 * you accept the licence agreement.
 *
 * @author    Mariusz Wysokiński <kontakt@intelekt.net.pl>
 * @copyright Since 2019 INTELEKT - Usługi Komputerowe Mariusz Wysokiński
 * @license   https://intelekt.net.pl/pages/regulamin
 */

use N1ebieski\IDir\ValueObjects\Price\Type;

return [

    'version' => \N1ebieski\IDir\Providers\IDirServiceProvider::VERSION,

    'license_key' => env('IDIR_LICENSE_KEY'),

    'layout' => env('IDIR_LAYOUT', 'idir'),

    'routes' => [
        'web' => [
            'prefix' => env('IDIR_ROUTES_WEB_PREFIX', null),
            'enabled' => true
        ],
        'admin' => [
            'prefix' => env('IDIR_ROUTES_ADMIN_PREFIX', 'admin'),
            'enabled' => true
        ],
        'api' => [
            'prefix' => env('IDIR_ROUTES_API_PREFIX', 'api'),
            'enabled' => true
        ]
    ],

    'dir' => [
        'max_tags' => (int)env('IDIR_DIR_MAX_TAGS', 10),
        'max_title' => (int)env('IDIR_DIR_MAX_TITLE', 100),
        'min_content' => (int)env('IDIR_DIR_MIN_CONTENT', 255),
        'max_content' => (int)env('IDIR_DIR_MAX_CONTENT', 1000),
        'short_content' => (int)env('IDIR_DIR_SHORT_CONTENT', 500),
        'https_only' => (bool)env('IDIR_DIR_HTTPS_ONLY', false),

        'title_normalizer' => null,

        'backlink' => [
            'check_hours' => (int)env('IDIR_DIR_BACKLINK_CHECK_HOURS', 24),
            'max_attempts' => (int)env('IDIR_DIR_BACKLINK_MAX_ATTEMPTS', 3),
            'delays' => [30, 60, 180, 365]
        ],

        'status' => [
            'check_days' => (int)env('IDIR_DIR_STATUS_CHECK_DAYS', 30),
            'max_attempts' => (int)env('IDIR_DIR_STATUS_MAX_ATTEMPTS', 2),
            'delays' => [30, 60, 180, 365],
            'parked_domains' => [
                'aftermarket.pl'
            ]
        ],

        'reminder' => [
            'left_days' => (int)env('IDIR_DIR_REMINDER_LEFT_DAYS', 7)
        ],

        'reasons' => [
            'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
            'Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.',
            'Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.',
            'Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.'
        ],

        'thumbnail' => [
            'url' => env('IDIR_DIR_THUMBNAIL_URL'),
            'reload_url' => env('IDIR_DIR_THUMBNAIL_RELOAD_URL'),
            'key' => env('IDIR_DIR_THUMBNAIL_KEY'), // 32 characters string
            'cache' => [
                'url' => env('IDIR_DIR_THUMBNAIL_CACHE_URL'),
                'days' => (int)env('IDIR_DIR_THUMBNAIL_CACHE_DAYS', 365)
            ],
            'api' => [
                'reload_url' => env('IDIR_DIR_THUMBNAIL_API_RELOAD_URL')
            ]
        ],

        'notification' => [
            'dirs' => (int)env('IDIR_DIR_NOTIFICATION_DIRS'),
            'hours' => (int)env('IDIR_DIR_NOTIFICATION_HOURS')
        ]

    ],

    'field' => [
        'gus' => [
            'name' => null,
            'street' => null,
            'propertyNumber' => null,
            'apartmentNumber' => null,
            'zipCode' => null,
            'city' => null,
            'regions' => null,
            'district' => null,
            'community' => null,
            'nip' => null,
            'regon' => null,
            'map' => null
        ]
    ],

    'home' => [
        'max' => (int)env('IDIR_HOME_MAX', 10),
        'max_privileged' => (int)env('IDIR_HOME_MAX_PRIVILEGED', 5)
    ],

    'price' => [
        'discount' => (bool)env('IDIR_PRICE_DISCOUNT', true)
    ],

    'payment' => [

        Type::TRANSFER => [
            'driver' => 'cashbill'
        ],

        Type::CODE_SMS => [
            'driver' => 'cashbill'
        ],

        Type::CODE_TRANSFER => [
            'driver' => 'cashbill'
        ],

        Type::PAYPAL_EXPRESS => [
            'driver' => 'paypal'
        ],

        'cashbill' => [
            'name' => 'CashBill',
            'url' => 'https://www.cashbill.pl',
            'rules_url' => 'https://www.cashbill.pl/download/regulaminy/Regulamin_Platnosci.pdf',
            'docs_url' => 'https://www.cashbill.pl/pobierz/dokumenty/'
        ],

        'paypal' => [
            'name' => 'PayPal',
            'url' => 'https://www.paypal.com',
            'rules_url' => 'https://www.paypal.com/pl/webapps/mpp/ua/useragreement-full?locale.x=pl_PL',
            'docs_url' => 'https://www.paypal.com/pl/webapps/mpp/ua/legalhub-full?locale.x=pl_PL'
        ]
    ],

    'import' => [
        'php_path' => 'php',
        'job_limit' => 1000
    ]
];
