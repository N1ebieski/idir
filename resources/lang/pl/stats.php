<?php

use N1ebieski\IDir\Models\Dir;

return [
    'dir' => [
        'label' => 'Wpisy',
        'status' => [
            Dir::ACTIVE => 'aktywne',
            Dir::INACTIVE => 'oczekujące'
        ]
    ]
];
