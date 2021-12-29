<?php

namespace N1ebieski\IDir\Http\Clients\Payment\Cashbill\Codes;

use GuzzleHttp\ClientInterface;
use Psr\Http\Message\ResponseInterface;
use Illuminate\Contracts\Config\Repository as Config;
use N1ebieski\IDir\Http\Clients\Payment\Cashbill\Client;

class CheckTransferClient extends Client
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
    protected $uri = '/form/backcode_check.php';

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

        $url = $config->get('services.cashbill.code_transfer.check_url');

        if (!empty($url)) {
            $this->setUrl($url);
        }
    }

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
