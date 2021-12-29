<?php

namespace N1ebieski\IDir\Http\Clients\Payment\Cashbill\Codes;

use GuzzleHttp\ClientInterface;
use Illuminate\Contracts\Config\Repository as Config;
use N1ebieski\IDir\Http\Clients\Payment\Cashbill\Client;

class CheckSMSClient extends Client
{
    /**
     * Undocumented variable
     *
     * @var string
     */
    protected $method = 'GET';

    /**
     * Undocumented variable
     *
     * @var string
     */
    protected $uri = '/code/{token}/{code}';

    /**
     * Undocumented variable
     *
     * @var string
     */
    protected $host = 'https://sms.cashbill.pl';

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
     * @param ClientInterface $client
     * @param Config $config
     */
    public function __construct(ClientInterface $client, Config $config)
    {
        parent::__construct($client);

        $url = $config->get('services.cashbill.code_sms.check_url');

        if (!empty($url)) {
            $this->setUrl($this->url($url));
        }
    }

    /**
     * Temporary fix for users who use the old pattern url
     *
     * @param string $url
     * @return string
     */
    protected function url(string $url): string
    {
        if (strpos($url, '{token}') === false) {
            $url .= '{token}';
        }

        if (strpos($url, '{code}') === false) {
            $url .= '/{code}';
        }

        return $url;
    }
}
