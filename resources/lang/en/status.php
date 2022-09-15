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

return [
    'delay' => 'Delay',
    'confirm' => [
        'delay' => 'Are you sure you want to delay the next check and reactivate the entry?'
    ],
    'delay_for' => [
        'label' => 'Select number of days delay',
        'custom' => 'Another value'
    ],
    'mail' => [
        'forbidden' => [
            'title' => 'The status checking bot does not have access to the website',
            'info' => 'We inform you that the bot checking the status of the entry :dir_link located on: <a href=":dir_page">:dir_page</a> did not access the website at :dir_url.',
            'result' => 'The cause of the problem is the configuration of your server, which blocks requests from our site. In a future this may result deactivation of an entry in the directory due to an incorrect status.',
            'solve' => 'You can prevent this by asking the hosting admin to unlock access for a bot with parameters:'
        ]
    ]
];
