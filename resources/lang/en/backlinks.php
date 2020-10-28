<?php

return [
    'not_found' => 'Backlink is not found',
    'mail' => [
        'not_found' => [
            'info' => 'The :attempt attempt in a row did not found a correctly inserted backlink on your website. Therefore, until the backlink is restored, your entry in our directory will be temporarily deactivated. When you restore the backlink or change the group without this obligation, the entry will be activated again.',
            'backlink' => 'Below is the backlink code you chose in the entry form:',
            'edit_dir' => 'We would also like to remind you about the possibility of changing the group without the need for a mandatory backlink. You can edit your entry by clicking on the button below:'
        ],
        'forbidden' => [
            'title' => 'The backlink checking bot does not have access to the website',
            'info' => 'We inform you that the bot checking the backlink of the entry :dir_link located on: <a href=":dir_page">:dir_page</a> did not access the website at :dir_url.',
            'result' => 'The cause of the problem is the configuration of your server, which blocks requests from our site. In a future this may result deactivation of an entry in the directory due to a missing backlink.',
            'solve' => 'You can prevent this by asking the hosting admin to unlock access for a bot with parameters:'
        ]
    ],
    'delay' => 'Delay',
    'confirm' => [
        'delay' => 'Are you sure you want to delay the next check and reactivate the entry?'
    ],
    'delay_for' => [
        'label' => 'Select number of days delay',
        'custom' => 'Another value'
    ],
];
