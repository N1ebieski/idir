<?php

namespace N1ebieski\IDir\Http\Clients\Payment\Cashbill\Codes\Transfer\Requests;

use GuzzleHttp\ClientInterface;
use Psr\Http\Message\ResponseInterface;
use N1ebieski\ICore\Http\Clients\Intelekt\Request;
use Illuminate\Contracts\Config\Repository as Config;

class AuthorizeRequest extends Request
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
     * @var array
     */
    protected $options = [
        'verify' => false
    ];

    /**
     * Undocumented variable
     *
     * @var Config
     */
    protected $config;

    /**
     * Undocumented function
     *
     * @param array $parameters
     * @param ClientInterface $client
     * @param Config $config
     */
    public function __construct(
        array $parameters,
        ClientInterface $client,
        Config $config
    ) {
        $this->config = $config;

        parent::__construct($parameters, $client);
    }

    /**
     * Undocumented function
     *
     * @return ResponseInterface
     * @throws \N1ebieski\IDir\Exceptions\Payment\Cashbill\Exception
     */
    public function makeRequest(): ResponseInterface
    {
        try {
            $response = $this->client->request(
                $this->method,
                $this->getCashbillUrl(),
                $this->options
            );
        } catch (\GuzzleHttp\Exception\GuzzleException $e) {
            throw new \N1ebieski\IDir\Exceptions\Payment\Cashbill\Exception(
                $e->getMessage(),
                $e->getCode(),
                $e
            );
        }

        return $response;
    }

    /**
     *
     * @return string
     */
    protected function getCashbillUrl(): string
    {
        return $this->config->get('services.cashbill.code_transfer.check_url')
            . '?' . http_build_query($this->parameters);
    }
}
