<?php

namespace N1ebieski\IDir\Http\Clients\Thumbnail\Provider;

use Psr\Http\Message\ResponseInterface;
use N1ebieski\ICore\Http\Clients\Client as BaseClient;

class Client extends BaseClient
{
    /**
     * Temporary fix for users who use the old pattern url
     *
     * @param string $url
     * @return static
     */
    protected function setUrl(string $url)
    {
        $this->url = $url;

        if (strpos($url, '{url}') === false) {
            $this->url .= '{url}';
        }

        return $this;
    }

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
            throw new \N1ebieski\IDir\Exceptions\Thumbnail\Exception(
                $e->getMessage(),
                $e->getCode(),
                $e
            );
        }

        return $response;
    }
}
