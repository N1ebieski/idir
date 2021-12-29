<?php

namespace N1ebieski\IDir\Http\Clients\Payment\Cashbill;

use Psr\Http\Message\ResponseInterface;
use N1ebieski\IDir\Http\Clients\Client as BaseClient;

class Client extends BaseClient
{
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
            throw new \N1ebieski\IDir\Exceptions\Payment\Cashbill\Exception(
                $e->getMessage(),
                $e->getCode()
            );
        }

        return $response;
    }
}
