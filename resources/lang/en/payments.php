<?php

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
