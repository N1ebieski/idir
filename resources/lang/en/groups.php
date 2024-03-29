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

use N1ebieski\IDir\ValueObjects\Group\Url;
use N1ebieski\IDir\ValueObjects\Group\Payment;
use N1ebieski\IDir\ValueObjects\Group\Visible;
use N1ebieski\IDir\ValueObjects\Group\Backlink;
use N1ebieski\IDir\ValueObjects\Group\ApplyStatus;

return [
    'dir' => [
        'dir' => 'Directory'
    ],
    'success' => [
        'store' => 'Group has been added.',
        'update' => 'Group has been updated.',
        'destroy' => 'Group was successfully removed.'
    ],
    'route' => [
        'index' => 'Groups',
        'edit' => 'Edit group',
        'create' => 'Add group',
        'edit_position' => 'Edit position',
        'show' => 'Group: :group'
    ],
    'name' => 'Name',
    'border' => [
        'label' => 'Border class',
        'tooltip' => 'Border class. It is used to highlight entry on the list',
        'placeholder' => 'example bootstrap 4: border-primary'
    ],
    'max_cats' => [
        'label' => 'Maximum number of categories',
        'tooltip' => 'The maximum value of the category to which an entry can be added.'
    ],
    'desc' => 'Description',
    'visible' => [
        'label' => 'Visibility',
        'tooltip' => 'Public - visible to everyone. Private - visible for moderators.',
        Visible::INACTIVE => 'private',
        Visible::ACTIVE => 'public'
    ],
    'backlink' => [
        'label' => 'Backlink',
        Backlink::INACTIVE => 'none',
        Backlink::OPTIONAL => 'optional',
        Backlink::ACTIVE => 'required'
    ],
    'url' => [
        'label' => 'URL',
        Url::INACTIVE => 'none',
        Url::OPTIONAL => 'optional',
        Url::ACTIVE => 'required'
    ],
    'apply_status' => [
        'label' => 'Status after adding entry',
        ApplyStatus::INACTIVE => 'pending acceptance',
        ApplyStatus::ACTIVE => 'active immediately'
    ],
    'max_models' => 'Maximum number of entries in the group',
    'max_models_daily' => 'Daily maximum number of entries in the group',
    'empty' => 'No groups available',
    'payment' => [
        'label' => 'Payment',
        Payment::INACTIVE => 'free',
        Payment::ACTIVE => 'paid'
    ],
    'alt' => [
        'label' => 'Alternative group',
        'tooltip' => 'After the expiry date, the entry will be moved to a alternative group',
        'null' => 'None (after the expiry date, the entry will be deactivated with the status "waiting for payment")'
    ]
];
