<?php

use N1ebieski\IDir\Models\Group;

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
        Group::INVISIBLE => 'private',
        Group::VISIBLE => 'public'
    ],
    'backlink' => [
        'label' => 'Backlink',
        Group::WITHOUT_BACKLINK => 'none',
        Group::OPTIONAL_BACKLINK => 'optional',
        Group::OBLIGATORY_BACKLINK => 'required'
    ],
    'url' => [
        'label' => 'URL',
        Group::WITHOUT_URL => 'none',
        Group::OPTIONAL_URL => 'optional',
        Group::OBLIGATORY_URL => 'required'
    ],
    'apply_status' => [
        'label' => 'Status after adding entry',
        Group::APPLY_INACTIVE => 'pending acceptance',
        Group::APPLY_ACTIVE => 'active immediately'
    ],
    'days' => 'Days',
    'price' => 'Price',
    'max_models' => 'Maximum number of entries in the group',
    'max_models_daily' => 'Daily maximum number of entries in the group',
    'empty' => 'No groups available',
    'payment' => [
        'index' => 'Payment',
        'transfer' => 'Transfer online',
        'code_sms' => 'SMS code',
        'code_transfer' => 'Transfer code',
        '0' => 'free',
        '1' => 'paid'
    ],
    'price_from' => 'paid from :price PLN / :days :limit',
    'unlimited' => 'unlimited',
    'alt' => [
        'index' => 'Alternative group',
        'tooltip' => 'After the expiry date, the entry will be moved to a alternative group',
        'null' => 'None (after the expiry date, the entry will be deactivated with the status "waiting for payment")'
    ],
    'code_sms' => 'SMS code',
    'code_transfer' => 'ID',
    'token' => 'Token',
    'number' => 'Number',
    'codes' => 'Manual codes',
    'sync_codes' => 'Sync codes'
];
