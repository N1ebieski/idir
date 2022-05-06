<?php

namespace N1ebieski\IDir\Http\Clients\Payment\PayPal\Express\Requests;

use N1ebieski\ICore\Http\Clients\Request;
use Omnipay\PayPal\ExpressGateway as PayPalGateway;
use Omnipay\Common\Message\RedirectResponseInterface as OmniPayResponse;

class PurchaseRequest extends Request
{
    /**
     * Undocumented variable
     *
     * @var array
     */
    protected $parameters;

    /**
     * Undocumented variable
     *
     * @var PayPalGateway
     */
    protected $gateway;

    /**
     * Undocumented function
     *
     * @param array $parameters
     * @param PayPalGateway $gateway
     */
    public function __construct(array $parameters, PayPalGateway $gateway)
    {
        $this->gateway = $gateway;

        $this->parameters = $parameters;

        $this->setParameters($parameters);

        $this->gateway->setUsername($this->username)
            ->setPassword($this->password)
            ->setSignature($this->signature)
            ->setTestMode($this->sandbox)
            ->setCurrency($this->currency);
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
        $this->parameters['description'] = $desc;

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
        $this->parameters['transactionId'] = $uuid;

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
        $this->parameters['redirect'] = $redirect;

        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $desc
     * @return self
     */
    public function setCancelUrl(string $cancelUrl)
    {
        $this->parameters['cancelUrl'] = $cancelUrl . '?uuid=' . $this->get('transactionId') . '&status=err&redirect=' . $this->get('redirect');

        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $desc
     * @return self
     */
    public function setReturnUrl(string $returnUrl)
    {
        $this->parameters['returnUrl'] = $returnUrl . '?uuid=' . $this->get('transactionId') . '&status=ok&redirect=' . $this->get('redirect');

        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $desc
     * @return self
     */
    public function setNotifyUrl(string $notifyUrl)
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
            'amount' => $this->amount,
            'description' => $this->description,
            'transactionId' => $this->transactionId,
            'cancelUrl' => $this->cancelUrl,
            'returnUrl' => $this->returnUrl,
            'notifyUrl' => $this->notifyUrl
        ])
        ->setLocaleCode($this->lang)
        ->send();
    }
}
