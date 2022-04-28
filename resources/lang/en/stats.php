<?php

use N1ebieski\IDir\ValueObjects\Dir\Status;

return [
    'dir' => [
        'label' => 'Entries',
        'status' => [
            Status::ACTIVE => 'active',
            Status::INACTIVE => 'pending'
        ]
    ]
];
