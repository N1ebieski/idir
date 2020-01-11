<?php

return [
    'page' => [
        'show' => 'Przejdź do płatności',
        'show_logs' => 'Logi płatności'
    ],
    'success' => [
        'complete' => 'Dziękujemy za płatność. Usługa zostanie aktywowana w momencie
        otrzymania potwierdzenia od operatora płatności.'
    ],
    'error' => [
        'complete' => 'Odnotowano błąd związany z tą płatnością u operatora płatności.'
    ],
    'redirect' => 'Za chwilę zostaniesz przekierowany na stronę płatności operatora :provider.
    Jeśli przekierowanie nie nastąpi automatycznie, proszę kliknąć przycisk poniżej',
    'desc' => [
        'dir' => ':title. Grupa: :group. Okres: :days :limit.'
    ],
    'status' => [
        'index' => 'Status',
        '1' => 'zrealizowana',
        '0' => 'oczekujący na realizację',
        '2' => 'oczekujący na płatność'
    ],
];
