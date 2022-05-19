<?php

namespace N1ebieski\IDir\Http\Clients\Dir;

use N1ebieski\ICore\Http\Clients\Client;
use Psr\Http\Message\ResponseInterface;

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
