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

namespace N1ebieski\IDir\Http\Clients\Payment\PayPal\Express\Requests;

use N1ebieski\ICore\Http\Clients\Request;
use Omnipay\PayPal\ExpressGateway as PayPalGateway;
use Omnipay\Common\Message\ResponseInterface as OmniPayResponse;

class CompleteRequest extends Request
{
    /**
     * Undocumented function
     *
     * @param array $parameters
     * @param PayPalGateway $gateway
     */
    public function __construct(
        protected array $parameters,
        protected PayPalGateway $gateway
    ) {
        $this->setParameters($parameters);

        $this->gateway->setUsername($this->get('username'))
            ->setPassword($this->get('password'))
            ->setSignature($this->get('signature'))
            ->setTestMode($this->get('sandbox'))
            ->setCurrency($this->get('currency'));
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
     *
     * @param string $uuid
     * @return self
     */
    public function setUuid(string $uuid): self
    {
        $this->parameters['transactionId'] = $uuid;

        return $this;
    }

    /**
     *
     * @param string $notifyUrl
     * @return self
     */
    public function setNotifyUrl(string $notifyUrl): self
    {
        $this->parameters['notifyUrl'] = $notifyUrl;

        return $this;
    }

    /**
     *
     * @return OmniPayResponse
     */
    public function makeRequest(): OmniPayResponse
    {
        return $this->gateway->completePurchase([
            'amount' => $this->get('amount'),
            'notifyUrl' => $this->get('notifyUrl'),
            'transactionId' => $this->get('uuid'),
            'PayerID' => $this->get('PayerID')
        ])
        ->send();
    }
}
