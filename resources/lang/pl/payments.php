<?php

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
