<?php

namespace N1ebieski\IDir\Http\Clients\Payment\Cashbill\Codes;

use N1ebieski\IDir\Http\Clients\Payment\Cashbill\Client;

class SMSClient extends Client
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
     * Temporary fix for users who use the old pattern url
     *
     * @param string $url
     * @return static
     */
    protected function setUrl(string $url)
    {
        $this->url = $url;

        if (strpos($url, '{token}') === false) {
            $this->url .= '{token}';
        }

        if (strpos($url, '{code}') === false) {
            $this->url .= '/{code}';
        }

        return $this;
    }
}
