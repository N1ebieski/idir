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
        'desc' => ':title. Grupa: :group. Okres: :days :limit.'
    ],
    'route' => [
        'show' => 'Przejdź do płatności',
        'show_logs' => 'Logi płatności'
    ],
    'success' => [
        'complete' => 'Dziękujemy za płatność. Usługa zostanie aktywowana w momencie otrzymania potwierdzenia od operatora płatności.'
    ],
    'error' => [
        'complete' => 'Odnotowano błąd związany z tą płatnością u operatora płatności.'
    ],
    'status' => [
        'label' => 'Status',
        Status::FINISHED => 'zrealizowana',
        Status::UNFINISHED => 'oczekujący na realizację',
        Status::PENDING => 'oczekujący na płatność'
    ],
];
