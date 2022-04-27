<?php

namespace N1ebieski\IDir\Utils\Updater\Schema;

use N1ebieski\ICore\Utils\Updater\Schema\Interfaces\SchemaInterface;

class Schema900 implements SchemaInterface
{
    /**
     * Undocumented variable
     *
     * @var array
     */
    public $pattern = [
        [
            'paths' => [
                'views' => [
                    'vendor/idir/web'
                ]
            ],
            'actions' => [
                [
                    'type' => 'replace',
                    'search' => '/\$pricesByType\(\'transfer\'\)/',
                    'to' => '$pricesByType(Price\Type::TRANSFER)'
                ],
                [
                    'type' => 'replace',
                    'search' => '/\$driverByType\(\'transfer\'\)/',
                    'to' => '$driverByType(Price\Type::TRANSFER)'
                ],
                [
                    'type' => 'replace',
                    'search' => '/\$pricesByType\(\'code_transfer\'\)/',
                    'to' => '$pricesByType(Price\Type::CODE_TRANSFER)'
                ],
                [
                    'type' => 'replace',
                    'search' => '/\$driverByType\(\'code_transfer\'\)/',
                    'to' => '$driverByType(Price\Type::CODE_TRANSFER)'
                ],
                [
                    'type' => 'replace',
                    'search' => '/\$pricesByType\(\'code_sms\'\)/',
                    'to' => '$pricesByType(Price\Type::CODE_SMS)'
                ],
                [
                    'type' => 'replace',
                    'search' => '/\$driverByType\(\'code_sms\'\)/',
                    'to' => '$driverByType(Price\Type::CODE_SMS)'
                ],
                [
                    'type' => 'replace',
                    'search' => '/\$pricesByType\(\'paypal_express\'\)/',
                    'to' => '$pricesByType(Price\Type::PAYPAL_EXPRESS)'
                ],
                [
                    'type' => 'replace',
                    'search' => '/\$driverByType\(\'paypal_express\'\)/',
                    'to' => '$driverByType(Price\Type::PAYPAL_EXPRESS)'
                ],
                [
                    'type' => 'replace',
                    'search' => '/old\(\'payment_type\',\s*\$paymentType\)\s*={2,3}\s*"code_transfer"/',
                    'to' => 'old(\'payment_type\', $paymentType) === Price\Type::CODE_TRANSFER'
                ],
                [
                    'type' => 'replace',
                    'search' => '/old\(\'payment_type\',\s*\$paymentType\)\s*={2,3}\s*"code_sms"/',
                    'to' => 'old(\'payment_type\', $paymentType) === Price\Type::CODE_SMS'
                ],
                [
                    'type' => 'replace',
                    'search' => '/old\(\'payment_type\',\s*\$paymentType\)\s*={2,3}\s*"transfer"/',
                    'to' => 'old(\'payment_type\', $paymentType) === Price\Type::TRANSFER'
                ],
                [
                    'type' => 'replace',
                    'search' => '/old\(\'payment_type\',\s*\$paymentType\)\s*={2,3}\s*"paypal_express"/',
                    'to' => 'old(\'payment_type\', $paymentType) === Price\Type::PAYPAL_EXPRESS'
                ]
            ]
        ],
        [
            'paths' => [
                'views' => [
                    'vendor/idir/web'
                ]
            ],
            'actions' => [
                [
                    'type' => 'replace',
                    'search' => '/\$price::AVAILABLE/',
                    'to' => 'Price\Type::getAvailable()'
                ]
            ]
        ]
    ];
}
