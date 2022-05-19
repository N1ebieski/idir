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

    /**
     * Undocumented function
     *
     * @return ResponseInterface
     */
    protected function makeResponse(): ResponseInterface
    {
        try {
            $response = parent::makeResponse();
        } catch (\N1ebieski\ICore\Exceptions\Client\TransferException $e) {
            throw new \N1ebieski\IDir\Exceptions\Dir\TransferException(
                $e->getMessage(),
                $e->getCode(),
                $e
            );
        }

        return $response;
    }
}
