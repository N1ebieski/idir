<?php

namespace N1ebieski\IDir\Http\Clients\Dir;

use N1ebieski\ICore\Http\Clients\Client;
use Psr\Http\Message\ResponseInterface;

class CheckBacklinkClient extends Client
{
    /**
     * Undocumented variable
     *
     * @var string
     */
    protected $method = 'GET';

    /**
     * Undocumented variable
     *
     * @var string
     */
    protected $uri;

    /**
     * Undocumented variable
     *
     * @var string
     */
    protected $host;

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

    /**
     * Undocumented function
     *
     * @return ResponseInterface
     */
    protected function makeResponse(): ResponseInterface
    {
        try {
            $response = parent::makeResponse();
        } catch (\GuzzleHttp\Exception\GuzzleException $e) {
            throw new \N1ebieski\IDir\Exceptions\Dir\TransferException(
                $e->getMessage(),
                $e->getCode()
            );
        }

        return $response;
    }
}
