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
                'resources/views/vendor/idir/web/partials/nav.blade.php'
            ],
            'actions' => [
                [
                    'type' => 'replace',
                    'search' => '/<a[^<]*?route\(\'logout\'\)[\s\S]*?<\/a>/',
                    'to' => <<<EOD
<form 
                            class="d-inline" 
                            method="POST" 
                            action="{{ route('logout') }}"
                        >
                            @csrf

                            <button type="submit" class="btn btn-link dropdown-item">
                                {{ trans('icore::auth.route.logout') }}
                            </button>
                        </form>
EOD
                ]
            ]
        ],
        [
            'paths' => [
                'resources/lang/vendor/idir'
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
                    'type' => 'replace',
                    'search' => '/Payment::PENDING/',
                    'to' => 'Status::PENDING'
                ],
                [
                    'type' => 'replace',
                    'search' => '/Payment::FINISHED/',
                    'to' => 'Status::FINISHED'
                ],
                [
                    'type' => 'replace',
                    'search' => '/Payment::UNFINISHED/',
                    'to' => 'Status::UNFINISHED'
                ],
                [
                    'type' => 'replace',
                    'search' => '/Field::OPTIONAL/',
                    'to' => 'Required::INACTIVE'
                ],
                [
                    'type' => 'replace',
                    'search' => '/Field::REQUIRED/',
                    'to' => 'Required::ACTIVE'
                ],
                [
                    'type' => 'replace',
                    'search' => '/Field::INVISIBLE/',
                    'to' => 'Visible::INACTIVE'
                ],
                [
                    'type' => 'replace',
                    'search' => '/Field::VISIBLE/',
                    'to' => 'Visible::ACTIVE'
                ],
                [
                    'type' => 'replace',
                    'search' => '/Group::INVISIBLE/',
                    'to' => 'Visible::INACTIVE'
                ],
                [
                    'type' => 'replace',
                    'search' => '/Group::VISIBLE/',
                    'to' => 'Visible::ACTIVE'
                ],
                [
                    'type' => 'replace',
                    'search' => '/Group::PAYMENT/',
                    'to' => 'Payment::ACTIVE'
                ],
                [
                    'type' => 'replace',
                    'search' => '/Group::WITHOUT_PAYMENT/',
                    'to' => 'Payment::INACTIVE'
                ],
                [
                    'type' => 'replace',
                    'search' => '/Group::APPLY_ACTIVE/',
                    'to' => 'ApplyStatus::ACTIVE'
                ],
                [
                    'type' => 'replace',
                    'search' => '/Group::APPLY_INACTIVE/',
                    'to' => 'ApplyStatus::INACTIVE'
                ],
                [
                    'type' => 'replace',
                    'search' => '/Group::OBLIGATORY_URL/',
                    'to' => 'Url::ACTIVE'
                ],
                [
                    'type' => 'replace',
                    'search' => '/Group::WITHOUT_URL/',
                    'to' => 'Url::INACTIVE'
                ],
                [
                    'type' => 'replace',
                    'search' => '/Group::OPTIONAL_URL/',
                    'to' => 'Url::OPTIONAL'
                ],
                [
                    'type' => 'replace',
                    'search' => '/Group::OBLIGATORY_BACKLINK/',
                    'to' => 'Backlink::ACTIVE'
                ],
                [
                    'type' => 'replace',
                    'search' => '/Group::WITHOUT_BACKLINK/',
                    'to' => 'Backlink::INACTIVE'
                ],
                [
                    'type' => 'replace',
                    'search' => '/Group::OPTIONAL_BACKLINK/',
                    'to' => 'Backlink::OPTIONAL'
                ],
                [
                    'type' => 'afterFirst',
                    'search' => '/use\s*N1ebieski\\\IDir\\\Models\\\Dir;/',
                    'to' => 'use N1ebieski\IDir\ValueObjects\Dir\Status;'
                ],
                [
                    'type' => 'afterFirst',
                    'search' => '/use\s*N1ebieski\\\IDir\\\Models\\\Payment\\\Payment;/',
                    'to' => 'use N1ebieski\IDir\ValueObjects\Payment\Status;'
                ],
                [
                    'type' => 'afterFirst',
                    'search' => '/use\s*N1ebieski\\\IDir\\\Models\\\Field\\\Field;/',
                    'to' => 'use N1ebieski\IDir\ValueObjects\Field\Required;'
                ],
                [
                    'type' => 'afterFirst',
                    'search' => '/use\s*N1ebieski\\\IDir\\\Models\\\Field\\\Field;/',
                    'to' => 'use N1ebieski\IDir\ValueObjects\Field\Visible;'
                ],
                [
                    'type' => 'afterFirst',
                    'search' => '/use\s*N1ebieski\\\IDir\\\Models\\\Group;/',
                    'to' => 'use N1ebieski\IDir\ValueObjects\Group\Visible;'
                ],
                [
                    'type' => 'afterFirst',
                    'search' => '/use\s*N1ebieski\\\IDir\\\Models\\\Group;/',
                    'to' => 'use N1ebieski\IDir\ValueObjects\Group\Payment;'
                ],
                [
                    'type' => 'afterFirst',
                    'search' => '/use\s*N1ebieski\\\IDir\\\Models\\\Group;/',
                    'to' => 'use N1ebieski\IDir\ValueObjects\Group\ApplyStatus;'
                ],
                [
                    'type' => 'afterFirst',
                    'search' => '/use\s*N1ebieski\\\IDir\\\Models\\\Group;/',
                    'to' => 'use N1ebieski\IDir\ValueObjects\Group\Url;'
                ],
                [
                    'type' => 'afterFirst',
                    'search' => '/use\s*N1ebieski\\\IDir\\\Models\\\Group;/',
                    'to' => 'use N1ebieski\IDir\ValueObjects\Group\Backlink;'
                ],
                [
                    'type' => 'removeFirst',
                    'search' => '/use\s*N1ebieski\\\IDir\\\Models\\\Dir;\\n*/'
                ],
                [
                    'type' => 'removeFirst',
                    'search' => '/use\s*N1ebieski\\\IDir\\\Models\\\Payment\\\Payment;\\n*/'
                ],
                [
                    'type' => 'removeFirst',
                    'search' => '/use\s*N1ebieski\\\IDir\\\Models\\\Field\\\Field;\\n*/'
                ],
                [
                    'type' => 'removeFirst',
                    'search' => '/use\s*N1ebieski\\\IDir\\\Models\\\Group;\\n*/'
                ]
            ]
        ],
        [
            'paths' => [
                'resources/views/vendor/idir/web'
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
                    'search' => '/\$dir->url\s*\!={2,3}\s*null/',
                    'to' => '$dir->isUrl()'
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
                    'search' => '/\$group->url\s*={2,3}\s*\$group::OBLIGATORY_URL/',
                    'to' => '$group->url->isActive()'
                ],
                [
                    'type' => 'replace',
                    'search' => '/\$group->url\s*>\s*0/',
                    'to' => '!$group->url->isInactive()'
                ],
                [
                    'type' => 'replace',
                    'search' => '/\$group->backlink\s*={2,3}\s*\$group::OBLIGATORY_BACKLINK/',
                    'to' => '$group->backlink->isActive()'
                ],
                [
                    'type' => 'replace',
                    'search' => '/\$group->backlink\s*>\s*0/',
                    'to' => '!$group->backlink->isInactive()'
                ],
                [
                    'type' => 'replace',
                    'search' => '/\$field->isRequired\(\)/',
                    'to' => '$field->options->required->isActive()'
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
                'resources/views/vendor/idir/mails'
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
