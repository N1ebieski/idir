<?php

namespace N1ebieski\IDir\Http\Clients\Intelekt;

use Psr\Http\Message\ResponseInterface;
use N1ebieski\IDir\Http\Clients\Client as BaseClient;

class Client extends BaseClient
{
    /**
     * Undocumented variable
     *
     * @var string
     */
    protected $host = 'https://intelekt.net.pl';

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
            throw new \N1ebieski\ICore\Exceptions\Intelekt\Post\TransferException(
                $e->getMessage(),
                $e->getCode()
            );
        }

        return $response;
    }
}
