<?php

return [

    'layout' => 'idir',

    'dir' => [
        'max_tags' => env('IDIR_DIR_MAX_TAGS', 10),
        'min_content' => env('IDIR_DIR_MIN_CONTENT', 255),
        'max_content' => env('IDIR_DIR_MAX_CONTENT', 500)
    ],
];
