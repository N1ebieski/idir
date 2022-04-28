<?php

use N1ebieski\IDir\ValueObjects\Dir\Status;

return [
    'dir' => [
        'label' => 'Wpisy',
        'status' => [
            Status::ACTIVE => 'aktywne',
            Status::INACTIVE => 'oczekujące'
        ]
    ]
];
