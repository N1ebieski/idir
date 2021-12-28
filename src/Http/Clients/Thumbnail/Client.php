<?php

namespace N1ebieski\IDir\Http\Clients\Thumbnail;

use Psr\Http\Message\ResponseInterface;
use N1ebieski\IDir\Http\Clients\Client as BaseClient;

class Client extends BaseClient
{
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
            throw new \N1ebieski\IDir\Exceptions\Thumbnail\Exception(
                $e->getMessage(),
                $e->getCode()
            );
        }

        return $response;
    }
}
