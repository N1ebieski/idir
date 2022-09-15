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

use N1ebieski\IDir\ValueObjects\Payment\Status;

return [
    'dir' => [
        'desc' => ':title. Group: :group. Period: :days :limit.'
    ],
    'route' => [
        'show' => 'Proceed to payment',
        'show_logs' => 'Payment logs'
    ],
    'success' => [
        'complete' => 'Thank you for the payment. The service will be activated upon receipt of confirmation from the payment operator.'
    ],
    'error' => [
        'complete' => 'An error occurred in the payment operator'
    ],
    'status' => [
        'label' => 'Status',
        Status::FINISHED => 'finished',
        Status::UNFINISHED => 'in progress',
        Status::PENDING => 'pending'
    ],
];
