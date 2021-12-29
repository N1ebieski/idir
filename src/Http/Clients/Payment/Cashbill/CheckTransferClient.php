<?php

namespace N1ebieski\IDir\Http\Clients\Payment\Cashbill;

use GuzzleHttp\ClientInterface;
use Illuminate\Contracts\Config\Repository as Config;
use N1ebieski\IDir\Http\Clients\Payment\Cashbill\Client;

class CheckTransferClient extends Client
{
    /**
     * Undocumented variable
     *
     * @var string
     */
    protected $method = 'POST';

    /**
     * Undocumented variable
     *
     * @var string
     */
    protected $uri = '/form/pay.php';

    /**
     * Undocumented variable
     *
     * @var string
     */
    protected $host = 'https://pay.cashbill.pl';

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

    /**
     * Undocumented function
     *
     * @param ClientInterface $client
     * @param Config $config
     */
    public function __construct(ClientInterface $client, Config $config)
    {
        parent::__construct($client);

        $url = $config->get("services.cashbill.transfer.url");

        if (!empty($url)) {
            $this->setUrl($url);
        }
    }
}
