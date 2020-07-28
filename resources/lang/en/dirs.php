<?php

use N1ebieski\IDir\Models\Dir;

return [
    'route' => [
        'index' => 'Directory',
        'search' => 'Search: :search',
        'create' => [
            'index' => 'Add entry',
            '1' => 'Select the type of entry',
            '2' => 'Fill in the form',
            '3' => 'Summary'
        ],
        'edit' => [
            'index' => 'Edit entry',
            'renew' => 'Extend the validity',
            '1' => 'Select the type of entry',
            '2' => 'Fill in the form',
            '3' => 'Summary'
        ],
        'step' => 'Step :step'
    ],
    'success' => [
        'store' => [
            Dir::INACTIVE => 'The entry has been added and awaits the approval of the moderator.',
            Dir::ACTIVE => 'The entry has been added and is active.'
        ],
        'update' => [
            Dir::INACTIVE => 'The entry has been updated and awaits the approval of the moderator.',
            Dir::ACTIVE => 'The entry has been updated and is active.'
        ],
        'update_status' => [
            Dir::ACTIVE => 'The entry has been activated',
            Dir::INCORRECT_INACTIVE => 'The entry has been submitted for correction'
        ],
        'update_renew' => [
            Dir::INACTIVE => 'Thank you. The validity of the entry will be extended when the moderator accepts the entry.',
            Dir::ACTIVE => 'Thank you. The entry has been extended.'
        ],
        'destroy' => 'The entry was deleted',
        'destroy_global' => 'Successfully deleted :affected entries'
    ],
    'choose_group' => 'Select a group',
    'change_group' => 'Change the group',
    'renew_group' => 'Extend the group',
    'categories' => 'Categories',
    'tags' => [
        'label' => 'Tags',
        'tooltip' => 'Min 3 chars, max 30 chars, max :max_tags chars',
        'placeholder' => 'Add tags'
    ],
    'choose_payment_type' => 'Choose your payment type',
    'payment' => [
        'transfer' => [
            'label' => 'Transfer online',
            'info' => 'Payments via transfer online realizes <a href=":provider_url" target="_blank" rel="noopener" title=":provider_name">:provider_name</a>. Documents relating to the payment system are available on the website <a href=":provider_docs_url" target="_blank" rel="noopener" title=":provider_name documents">:provider_name documents</a>. Terms of service of the payment system is available on the website <a href=":provider_rules_url" target="_blank" rel="noopener" title=":provider_name terms of service">:provider_name terms of service</a>. Submitting a entry to directory is tantamount to acceptance of <a href=":rules_url" target="_blank" rel="noopener" title="Terms of service">terms of service</a>.',
        ],
        'code_sms' => [
            'label' => 'Automatic SMS code',
            'info' => 'To receive the access code, send a SMS <b><span id="code_sms">:code_sms</span></b> to <b><span id="number">:number</span></b>. The cost of the SMS is <b><span id="price">:price</span></b> PLN. The SMS service is available to all mobile operators in Poland. SMS payments realizes <a href=":provider_url" target="_blank" rel="noopener" title=":provider_name">:provider_name</a>. Documents relating to the payment system are available on the website <a href=":provider_docs_url" target="_blank" rel="noopener" title=":provider_name documents">:provider_name documents</a>. Terms of service of the payment system is available on the website <a href=":provider_rules_url" target="_blank" rel="noopener" title=":provider_name terms of service">:provider_name terms of service</a>. Submitting a entry to directory is tantamount to acceptance of <a href=":rules_url" target="_blank" rel="noopener" title="Terms of service">rerms of service</a>.'
        ],
        'code_transfer' => [
            'label' => 'Automatic transfer code',
            'info' => 'To receive the access code, please buy the code on the website <a id="code_transfer" href=":code_transfer_url" target="blank" title=":provider_name"><b>:provider_name</b></a>. The cost is <b><span id="price">:price</span></b> PLN. Documents relating to the payment system are available on the website <a href=":provider_docs_url" target="_blank" rel="noopener" title=":provider_name documents">:provider_name documents</a>. Terms of service of the payment system is available on the website <a href=":provider_rules_url" target="_blank" rel="noopener" title=":provider_name terms of service">:provider_name terms of service</a>. Submitting a entry to directory is tantamount to acceptance of <a href=":rules_url" target="_blank" rel="noopener" title="Rerms of service">terms of service</a>.'
        ]
    ],
    'price' => ':price :currency / :days :limit',
    'rules' => 'Terms of service',
    'code' => 'Enter the code',
    'choose_backlink' => 'Choose a backlink',
    'backlink_url' => 'URL to backlink',
    'group' => 'Group',
    'group_limit' => 'Limit depleted (max: :dirs, daily: :dirs_today)',
    'unlimited' => 'unlimited',
    'status' => [
        'label' => 'Status',
        Dir::ACTIVE => 'active',
        Dir::INACTIVE => 'pending acceptance',
        Dir::PAYMENT_INACTIVE => 'waiting for payment',
        Dir::BACKLINK_INACTIVE => 'waiting for backlink',
        Dir::STATUS_INACTIVE => 'pending 200 status',
        Dir::INCORRECT_INACTIVE => 'waiting for correction'
    ],
    'privileged_to' => 'Expires at',
    'reason' => [
        'label' => 'Reason of rejection',
        'custom' => 'Other'
    ],
    'mail' => [
        'delete' => [
            'info' => 'Sorry, but your entry :dir_link has been removed from our directory.'
        ],
        'activation' => [
            'info' => 'Congratulations, your entry :dir_link has been correctly added to our directory and is on the page: <a href=":dir_page">:dir_page</a>. Welcome to another entry!'
        ],
        'incorrect' => [
            'info' => 'Sorry, but your entry :dir_link does not comply with our terms and conditions and needs to be corrected. Until then, it will remain inactive.',
            'edit_dir' => 'You can edit your entry by clicking on the button below:'
        ],
        'reminder' => [
            'title' => 'Reminder of expiring entry',
            'info' => 'We would like to remind you that your entry :dir_link on the page: <a href=":dir_page">:dir_page</a> will expire soon in group :group.',
            'alt' => 'After the expiry date :days, the entry will be moved to a lower group :alt_group.',
            'deactivation' => 'After the expiry date :days, the entry will be deactivated with the status "waiting for payment".',
            'renew_dir' => 'You can prevent this by extending the validity of an entry in the current group. You can extend your entry by clicking on the button below:'
        ],
        'completed' => [
            'title' => 'End of validity period',
            'info' => 'We would like to inform you that the validity period of your entry has ended :dir_link in group :group.',
            'alt' => 'Thus, the entry has been moved to a lower group :alt_group.',
            'deactivation' => 'Thus, the entry was deactivated with the status "waiting for payment".',
            'edit_dir' => 'You can renew the entry or change the group. You can edit your entry by clicking on the button below:'
        ]
    ],
    'link_dir_page' => 'Link your entry to speed up indexation',
    'premium_dir' => 'Highlight your entry',
    'content' => 'Content',
    'author' => 'Author',
    'reload_thumbnail' => 'Reload',
    'check_content' => 'Check uniqueness',
    'rating' => 'Rating',
    'url' => 'URL',
    'related' => 'Related entries',
    'latest' => 'Recent entries',
    'title' => 'Title',
    'notes' => 'Moderator\'s comments',
    'more' => 'show more &raquo',
    'correct' => 'Correct.',
    'confirm' => [
        'correct' => 'Are you sure you want to report entry to correction?'
    ]
];
