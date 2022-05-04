<?php

namespace N1ebieski\IDir\Http\Clients\Payment\Cashbill\Codes;

use Psr\Http\Message\ResponseInterface;
use N1ebieski\IDir\Http\Clients\Payment\Cashbill\Client;

class TransferClient extends Client
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
        $contents = explode("\n", trim($response->getBody()->getContents()));

        $this->contents = (object)[
            'status' => $contents[0],
            'timeRemaining' => $contents[1] ?? null
        ];

        return $this;
    }
}
