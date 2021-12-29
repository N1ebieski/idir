<?php

namespace N1ebieski\IDir\Http\Clients\Intelekt;

use N1ebieski\ICore\Http\Clients\Client as BaseClient;

class Client extends BaseClient
{
    /**
     * Undocumented variable
     *
     * @var string
     */
    protected $host = 'https://intelekt.net.pl';

    /**
     * Undocumented variable
     *
     * @var array
     */
    protected $options = [
        'verify' => false,
        'headers' => [
            'Accept' => 'application/json',
        ]
    ];

    /**
     * Undocumented function
     *
     * @param string $url
     * @return static
     */
    protected function setUrl(string $url)
    {
        $this->url = $this->host . $url;

        return $this;
    }
}
