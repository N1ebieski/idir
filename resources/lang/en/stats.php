<?php

use N1ebieski\IDir\Models\Dir;

return [
    'dir' => [
        'label' => 'Entries',
        'status' => [
            Dir::ACTIVE => 'active',
            Dir::INACTIVE => 'pending'
        ]
    ]
];
