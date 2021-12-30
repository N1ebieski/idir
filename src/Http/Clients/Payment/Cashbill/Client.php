<?php

namespace N1ebieski\IDir\Http\Clients\Payment\Cashbill;

use Psr\Http\Message\ResponseInterface;
use N1ebieski\ICore\Http\Clients\Client as BaseClient;

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
        } catch (\N1ebieski\ICore\Exceptions\Client\TransferException $e) {
            throw new \N1ebieski\IDir\Exceptions\Payment\Cashbill\Exception(
                $e->getMessage(),
                $e->getCode(),
                $e
            );
        }

        return $response;
    }
}
