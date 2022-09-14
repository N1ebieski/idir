<?php

/**
 * NOTICE OF LICENSE
 *
 * This source file is licenced under the Software License Agreement
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://intelekt.net.pl/pages/regulamin
 *
 * With the purchase or the installation of the software in your application
 * you accept the licence agreement.
 *
 * @author    Mariusz Wysokiński <kontakt@intelekt.net.pl>
 * @copyright Since 2019 INTELEKT - Usługi Komputerowe Mariusz Wysokiński
 * @license   https://intelekt.net.pl/pages/regulamin
 */

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
     * Undocumented function
     *
     * @param array $parameters
     * @param ClientInterface $client
     * @param Config $config
     */
    public function __construct(
        array $parameters,
        ClientInterface $client,
        protected Config $config
    ) {
        parent::__construct($parameters, $client);

        $this->setSign();
    }

    /**
     * Undocumented function
     *
     * @param string $amount
     * @return self
     */
    protected function setAmount(string $amount): self
    {
        $this->parameters['amount'] = number_format((float)$amount, 2, '.', '');

        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $desc
     * @return self
     */
    protected function setDesc(string $desc): self
    {
        $this->parameters['desc'] = $desc;

        return $this;
    }

    /**
     *
     * @param string $uuid
     * @return PurchaseRequest
     */
    protected function setUuid(string $uuid): self
    {
        $userdata = isset($this->parameters['userdata']) ?
            json_decode($this->parameters['userdata'], true) : [];

        $this->parameters['userdata'] = json_encode(array_merge($userdata, [
            'uuid' => $uuid
        ]));

        return $this;
    }

    /**
     *
     * @param string $redirect
     * @return PurchaseRequest
     */
    protected function setRedirect(string $redirect): self
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
    protected function setSign(): self
    {
        $this->parameters['sign'] = md5($this->get('service') . '|' . $this->get('amount') . '|'
            . $this->get('currency') . '|' . $this->get('desc') . '|' . $this->get('lang') . '|'
            . $this->get('userdata') . '||||||||||||' . $this->get('key'));

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
                        'service' => $this->get('service'),
                        'amount' => $this->get('amount'),
                        'currency' => $this->get('currency'),
                        'lang' => $this->get('lang'),
                        'desc' => $this->get('desc'),
                        'userdata' => $this->get('userdata'),
                        'sign' => $this->get('sign')
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
