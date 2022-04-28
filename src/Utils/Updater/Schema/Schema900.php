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
                'lang' => [
                    'vendor/idir'
                ]
            ],
            'actions' => [
                [
                    'type' => 'replace',
                    'search' => '/Dir::ACTIVE/',
                    'to' => 'Status::ACTIVE'
                ],
                [
                    'type' => 'replace',
                    'search' => '/Dir::INACTIVE/',
                    'to' => 'Status::INACTIVE'
                ],
                [
                    'type' => 'replace',
                    'search' => '/Dir::PAYMENT_INACTIVE/',
                    'to' => 'Status::PAYMENT_INACTIVE'
                ],
                [
                    'type' => 'replace',
                    'search' => '/Dir::BACKLINK_INACTIVE/',
                    'to' => 'Status::BACKLINK_INACTIVE'
                ],
                [
                    'type' => 'replace',
                    'search' => '/Dir::STATUS_INACTIVE/',
                    'to' => 'Status::STATUS_INACTIVE'
                ],
                [
                    'type' => 'replace',
                    'search' => '/Dir::INCORRECT_INACTIVE/',
                    'to' => 'Status::INCORRECT_INACTIVE'
                ],
                [
                    'type' => 'afterFirst',
                    'search' => '/use\s*N1ebieski\\\IDir\\\Models\\\Dir;/',
                    'to' => 'use N1ebieski\IDir\ValueObjects\Dir\Status;'
                ],
                [
                    'type' => 'removeFirst',
                    'search' => '/use\s*N1ebieski\\\IDir\\\Models\\\Dir;\\n*/'
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
                ],
                [
                    'type' => 'replace',
                    'search' => '/\$price::AVAILABLE/',
                    'to' => 'Price\Type::getAvailable()'
                ],
                [
                    'type' => 'replace',
                    'search' => '/\$dir->status\s*={2,3}\s*\$dir::ACTIVE/',
                    'to' => '$dir->status->isActive()'
                ],
                [
                    'type' => 'replace',
                    'search' => '/\$dir->isUrl\(\)/',
                    'to' => '$dir->url->isUrl()'
                ],
                [
                    'type' => 'replace',
                    'search' => '/\$dir->url\s*\!={2,3}\s*null/',
                    'to' => '$dir->url->isUrl()'
                ],
                [
                    'type' => 'replace',
                    'search' => '/\$dir->isGroup\(\$group->id\)/',
                    'to' => '$group->id === $dir->group->id'
                ],
                [
                    'type' => 'replace',
                    'search' => '/\$countDirs->firstWhere\(\'status\',\s*1\)/',
                    'to' => '$countDirs->firstWhere(\'status\', Dir\Status::active())'
                ],
                [
                    'type' => 'replace',
                    'search' => '/\$countDirs->firstWhere\(\'status\',\s*0\)/',
                    'to' => '$countDirs->firstWhere(\'status\', Dir\Status::inactive())'
                ],
                [
                    'type' => 'replace',
                    'search' => '/\$dir::ACTIVE/',
                    'to' => 'Dir\Status::ACTIVE'
                ],
                [
                    'type' => 'replace',
                    'search' => '/\$dir::INACTIVE/',
                    'to' => 'Dir\Status::INACTIVE'
                ],
                [
                    'type' => 'replace',
                    'search' => '/\$dir::PAYMENT_INACTIVE/',
                    'to' => 'Dir\Status::PAYMENT_INACTIVE'
                ],
                [
                    'type' => 'replace',
                    'search' => '/\$dir::STATUS_INACTIVE/',
                    'to' => 'Dir\Status::STATUS_INACTIVE'
                ],
                [
                    'type' => 'replace',
                    'search' => '/\$dir::BACKLINK_INACTIVE/',
                    'to' => 'Dir\Status::BACKLINK_INACTIVE'
                ],
                [
                    'type' => 'replace',
                    'search' => '/\$dir::INCORRECT_INACTIVE/',
                    'to' => 'Dir\Status::INCORRECT_INACTIVE'
                ]
            ]
        ],
        [
            'paths' => [
                'views' => [
                    'vendor/idir/mails'
                ]
            ],
            'actions' => [
                [
                    'type' => 'replace',
                    'search' => '/\$dir->isActive\(\)/',
                    'to' => '$dir->status->isActive()'
                ],
                [
                    'type' => 'replace',
                    'search' => '/\$dir::INACTIVE/',
                    'to' => 'Dir\Status::INACTIVE'
                ],
                [
                    'type' => 'replace',
                    'search' => '/\$report::REPORTED/',
                    'to' => 'Report\Reported::ACTIVE'
                ],
                [
                    'type' => 'replace',
                    'search' => '/\$payment::FINISHED/',
                    'to' => 'Payment\Status::FINISHED'
                ]
            ]
        ]
    ];
}
