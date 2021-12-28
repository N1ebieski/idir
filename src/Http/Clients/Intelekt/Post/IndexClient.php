<?php

namespace N1ebieski\IDir\Http\Clients\Intelekt\Post;

use N1ebieski\IDir\Http\Clients\Intelekt\Client;

class IndexClient extends Client
{
    /**
     * Undocumented variable
     *
     * @var string
     */
    protected $method = 'POST';

    /**
     * Undocumented variable
     *
     * @var string
     */
    protected $uri = '/api/posts/index';

    /**
     * Undocumented variable
     *
     * @var array
     */
    protected $options = [
        'verify' => false,
        'headers' => [
            'Accept' => 'application/json',
        ],
        'form_params' => [
            'filter' => [
                'status' => 1,
                'orderby' => 'created_at|desc',
                'search' => 'idir'
            ]
        ]
    ];
}
