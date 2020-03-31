<?php

use N1ebieski\IDir\Models\Payment\Payment;

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
    'redirect' => 'Za chwilę zostaniesz przekierowany na stronę płatności operatora :provider. Jeśli przekierowanie nie nastąpi automatycznie, proszę kliknąć przycisk poniżej',
    'status' => [
        'label' => 'Status',
        Payment::FINISHED => 'zrealizowana',
        Payment::UNFINISHED => 'oczekujący na realizację',
        Payment::PENDING => 'oczekujący na płatność'
    ],
];
