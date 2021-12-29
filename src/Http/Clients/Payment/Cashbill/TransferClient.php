<?php

namespace N1ebieski\IDir\Http\Clients\Payment\Cashbill;

use N1ebieski\IDir\Http\Clients\Payment\Cashbill\Client;

class TransferClient extends Client
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
        ]
    ];
}
