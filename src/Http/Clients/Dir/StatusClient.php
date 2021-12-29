<?php

namespace N1ebieski\IDir\Http\Clients\Dir;

use N1ebieski\ICore\Http\Clients\Client;

class StatusClient extends Client
{
    /**
     * Undocumented variable
     *
     * @var array
     */
    protected $options = [
        'verify' => false,
        'allow_redirects' => [
            'track_redirects' => true
        ],
        'decode_content' => false,
        'http_errors' => true
    ];
}
