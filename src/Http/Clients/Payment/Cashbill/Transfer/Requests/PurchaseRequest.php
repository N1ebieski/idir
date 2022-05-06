<?php

namespace N1ebieski\IDir\Http\Clients\Payment\Cashbill\Transfer\Requests;

use GuzzleHttp\ClientInterface;
use Psr\Http\Message\ResponseInterface;
use N1ebieski\ICore\Http\Clients\Request;
use Illuminate\Contracts\Config\Repository as Config;
use N1ebieski\IDir\Exceptions\Payment\Cashbill\Exception;

class PurchaseRequest extends Request
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
     * @var array
     */
    protected $options = [
        'verify' => false,
        'allow_redirects' => [
            'track_redirects' => true
        ]
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

        $this->setSign();
    }

    /**
     * Undocumented function
     *
     * @param string $amount
     * @return self
     */
    protected function setAmount(string $amount)
    {
        $this->parameters['amount'] = number_format($amount, 2, '.', '');

        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $desc
     * @return self
     */
    protected function setDesc(string $desc)
    {
        $this->parameters['desc'] = $desc;

        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $desc
     * @return self
     */
    protected function setUuid(string $uuid)
    {
        $userdata = isset($this->parameters['userdata']) ?
            json_decode($this->parameters['userdata'], true) : [];

        $this->parameters['userdata'] = json_encode(array_merge($userdata, [
            'uuid' => $uuid
        ]));

        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $desc
     * @return self
     */
    protected function setRedirect(string $redirect)
    {
        $userdata = isset($this->parameters['userdata']) ?
            json_decode($this->parameters['userdata'], true) : [];

        $this->parameters['userdata'] = json_encode(array_merge($userdata, [
            'redirect' => $redirect
        ]));

        return $this;
    }

    /**
     *
     * @return self
     */
    protected function setSign()
    {
        $this->parameters['sign'] = md5($this->service . '|' . $this->amount . '|' . $this->currency . '|'
            . $this->desc . '|' . $this->lang . '|' . $this->userdata . '||||||||||||' . $this->key);

        return $this;
    }

    /**
     *
     * @return ResponseInterface
     * @throws Exception
     */
    public function makeRequest(): ResponseInterface
    {
        try {
            $response = $this->client->request(
                $this->method,
                $this->config->get("services.cashbill.transfer.url"),
                array_merge($this->options, [
                    'form_params' => [
                        'service' => $this->service,
                        'amount' => $this->amount,
                        'currency' => $this->currency,
                        'lang' => $this->lang,
                        'desc' => $this->desc,
                        'userdata' => $this->userdata,
                        'sign' => $this->sign
                    ]
                ])
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
}
