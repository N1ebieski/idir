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
use Omnipay\Common\Message\RedirectResponseInterface as OmniPayResponse;

class PurchaseRequest extends Request
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
     * Undocumented function
     *
     * @param string $desc
     * @return self
     */
    protected function setDesc(string $desc): self
    {
        $this->parameters['description'] = $desc;

        return $this;
    }

    /**
     *
     * @param string $uuid
     * @return self
     */
    protected function setUuid(string $uuid): self
    {
        $this->parameters['transactionId'] = $uuid;

        return $this;
    }

    /**
     *
     * @param string $redirect
     * @return self
     */
    protected function setRedirect(string $redirect): self
    {
        $this->parameters['redirect'] = $redirect;

        return $this;
    }

    /**
     *
     * @param string $cancelUrl
     * @return self
     */
    public function setCancelUrl(string $cancelUrl): self
    {
        $this->parameters['cancelUrl'] = $cancelUrl . '?uuid=' . $this->get('transactionId') . '&status=err&redirect=' . $this->get('redirect');

        return $this;
    }

    /**
     *
     * @param string $returnUrl
     * @return self
     */
    public function setReturnUrl(string $returnUrl): self
    {
        $this->parameters['returnUrl'] = $returnUrl . '?uuid=' . $this->get('transactionId') . '&status=ok&redirect=' . $this->get('redirect');

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
        return $this->gateway->purchase([
            'amount' => $this->get('amount'),
            'description' => $this->get('description'),
            'transactionId' => $this->get('transactionId'),
            'cancelUrl' => $this->get('cancelUrl'),
            'returnUrl' => $this->get('returnUrl'),
            'notifyUrl' => $this->get('notifyUrl')
        ])
        ->setLocaleCode($this->get('lang'))
        ->send();
    }
}
