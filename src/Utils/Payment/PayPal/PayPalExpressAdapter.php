<?php

namespace N1ebieski\IDir\Utils\Payment\PayPal;

use Omnipay\PayPal\ExpressGateway;
use Omnipay\PayPal\Message\ExpressAuthorizeResponse;
use Illuminate\Contracts\Config\Repository as Config;
use N1ebieski\IDir\Utils\Payment\Interfaces\TransferUtilStrategy;
use Omnipay\Common\Message\ResponseInterface;

class PayPalExpressAdapter implements TransferUtilStrategy
{
    /**
     * Undocumented variable
     *
     * @var ExpressGateway
     */
    protected $gateway;

    /**
     * Undocumented variable
     *
     * @var Config
     */
    protected $config;

    /**
     * [protected description]
     * @var ExpressAuthorizeResponse
     */
    protected $response;

    /**
     * [protected description]
     * @var float
     */
    protected $amount;

    /**
     * Undocumented variable
     *
     * @var string
     */
    protected $description;

    /**
     * Undocumented variable
     *
     * @var string
     */
    protected $transactionId;

    /**
     * Undocumented variable
     *
     * @var string
     */
    protected $cancelUrl;

    /**
     * Undocumented variable
     *
     * @var string
     */
    protected $returnUrl;

    /**
     * Undocumented variable
     *
     * @var string
     */
    protected $notifyUrl;

    public function __construct(ExpressGateway $gateway, Config $config)
    {
        $this->gateway = $gateway;
        $this->config = $config;
    }

    /**
     * [setup description]
     * @param  array $attributes [description]
     * @return static              [description]
     */
    public function setup(array $attributes)
    {
        $userdata = json_decode($attributes['userdata'], true);

        $this->amount = $attributes['amount'];
        $this->description = $attributes['desc'];
        $this->transactionId = $userdata['uuid'];
        $this->cancelUrl = $attributes['cancel'];
        $this->returnUrl = $attributes['redirect'];
        $this->notifyUrl = $attributes['verify'];

        return $this;
    }

    /**
     * [isSign description]
     * @param  array $attributes [description]
     * @return bool              [description]
     */
    public function isSign(array $attributes) : bool
    {
        //
    }

    /**
     * [authorize description]
     * @param  array  $attributes [description]
     * @return void               [description]
     */
    public function authorize(array $attributes) : void
    {
        //
    }
    
    /**
     * Undocumented function
     *
     * @return ResponseInterface
     */
    public function makeResponse() : ResponseInterface
    {
        $this->response = $this->gateway
            ->setUsername($this->config->get('services.paypal.paypal_express.username'))
            ->setPassword($this->config->get('services.paypal.paypal_express.password'))
            ->setSignature($this->config->get('services.paypal.paypal_express.signature'))
            ->setTestMode($this->config->get('services.paypal.paypal_express.sandbox'))
            ->setCurrency($this->config->get('services.paypal.paypal_express.currency'))
            ->purchase($this->all())
            ->setLocaleCode($this->config->get('services.paypal.paypal_express.lang'))
            ->send();

        return $this->response;
    }

    /**
     * Undocumented function
     *
     * @return string
     */
    public function getUrlToPayment() : string
    {
        return $this->response->getRedirectUrl();
    }

    /**
     * [all description]
     * @return array [description]
     */
    public function all() : array
    {
        return [
            'amount' => $this->amount,
            'description' => $this->description,
            'transactionId' => $this->transactionId,
            'cancelUrl' => $this->cancelUrl,
            'returnUrl' => $this->returnUrl,
            'notifyUrl' => $this->notifyUrl
        ];
    }
}