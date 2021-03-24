<?php

namespace N1ebieski\IDir\Utils\Payment\PayPal;

use Mdb\PayPal\Ipn\Event\MessageInvalidEvent;
use Omnipay\PayPal\ExpressGateway as PayPalGateway;
use Omnipay\PayPal\Message\ExpressAuthorizeResponse;
use Illuminate\Contracts\Config\Repository as Config;
use Mdb\PayPal\Ipn\Event\MessageVerificationFailureEvent;
use N1ebieski\IDir\Utils\Payment\Interfaces\TransferUtilStrategy;
use Mdb\PayPal\Ipn\ListenerBuilder\Guzzle\ArrayListenerBuilder as PayPalListener;

class PayPalExpressAdapter implements TransferUtilStrategy
{
    /**
     * Undocumented variable
     *
     * @var PayPalGateway
     */
    protected $gateway;

    /**
     * Undocumented variable
     *
     * @var PayPalListener
     */
    protected $listener;

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
    protected $redirect;

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

    /**
     * Undocumented function
     *
     * @param PayPalGateway $gateway
     * @param PayPalListener $listener
     * @param Config $config
     */
    public function __construct(
        PayPalGateway $gateway,
        PayPalListener $listener,
        Config $config
    ) {
        $this->gateway = $gateway;
        $this->listener = $listener;
        $this->config = $config;

        $this->gateway->setUsername($config->get('services.paypal.paypal_express.username'))
            ->setPassword($config->get('services.paypal.paypal_express.password'))
            ->setSignature($config->get('services.paypal.paypal_express.signature'))
            ->setTestMode($config->get('services.paypal.paypal_express.sandbox'))
            ->setCurrency($config->get('services.paypal.paypal_express.currency'));
    }

    /**
     * Undocumented function
     *
     * @param string $amount
     * @return self
     */
    public function setAmount(string $amount)
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $desc
     * @return self
     */
    public function setDesc(string $desc)
    {
        $this->description = $desc;

        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $desc
     * @return self
     */
    public function setUuid(string $uuid)
    {
        $this->transactionId = $uuid;

        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $desc
     * @return self
     */
    public function setRedirect(string $redirect)
    {
        $this->redirect = $redirect;

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
        $this->cancelUrl = $cancelUrl . '?uuid=' . $this->transactionId . '&status=err&redirect=' . $this->redirect;

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
        $this->returnUrl = $returnUrl . '?uuid=' . $this->transactionId . '&status=ok&redirect=' . $this->redirect;

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
        $this->notifyUrl = $notifyUrl;

        return $this;
    }
   
    /**
     * [isStatus description]
     * @param  string $status [description]
     * @return bool           [description]
     */
    public function isStatus(string $status) : bool
    {
        return $status === 'ok';
    }
    
    /**
     * Undocumented function
     *
     * @return void
     */
    public function purchase() : void
    {
        $this->response = $this->gateway->purchase($this->all())
            ->setLocaleCode($this->config->get('services.paypal.paypal_express.lang'))
            ->send();
    }

    /**
     * Undocumented function
     *
     * @param array $attributes
     * @return void
     */
    public function complete(array $attributes) : void
    {
        if (!$this->isStatus($attributes['status'])) {
            return;
        }

        $this->response = $this->gateway->completePurchase([
                'amount' => $this->amount,
                'notifyUrl' => $this->notifyUrl,
                'transactionId' => $attributes['uuid'],
                'PayerID' => $attributes['PayerID']
            ])
            ->send();
    }

    /**
     * [authorize description]
     * @param  array  $attributes [description]
     * @return void               [description]
     */
    public function authorize(array $attributes) : void
    {
        $this->listener->setData($attributes);

        if ((bool)$this->gateway->getTestMode() === true) {
            $this->listener->useSandbox();
        }

        $listener = $this->listener->build();

        $listener->onInvalid(function (MessageInvalidEvent $event) {
            throw new \N1ebieski\IDir\Exceptions\Payment\PayPal\Express\InvalidException(
                $event->getMessage(),
                403
            );
        });
         
        $listener->onVerificationFailure(function (MessageVerificationFailureEvent $event) {
            throw new \N1ebieski\IDir\Exceptions\Payment\PayPal\VerificationFailureException(
                $event->getError(),
                403
            );
        });
         
        $listener->listen();
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
