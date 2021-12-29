<?php

namespace N1ebieski\IDir\Http\Clients\Dir;

use Psr\Http\Message\ResponseInterface;
use N1ebieski\ICore\Http\Clients\Client;

class BacklinkClient extends Client
{
    /**
     * Undocumented variable
     *
     * @var array
     */
    protected $options = [
        'verify' => false
    ];

    /**
     * Undocumented function
     *
     * @param ResponseInterface $response
     * @return static
     */
    protected function setContentsFromResponse(ResponseInterface $response)
    {
        $this->contents = $response->getBody()->getContents();

        return $this;
    }
}
