<?php

namespace N1ebieski\IDir\Http\Clients\Payment\PayPal\Express;

use N1ebieski\IDir\Http\Clients\Payment\PayPal\PayPalListener;
use Illuminate\Contracts\Container\Container as App;
use Illuminate\Contracts\Config\Repository as Config;
use Illuminate\Contracts\Container\BindingResolutionException;
use N1ebieski\IDir\Http\Clients\Payment\PayPal\Express\Requests\CompleteRequest;
use N1ebieski\IDir\Http\Clients\Payment\PayPal\Express\Requests\PurchaseRequest;
use N1ebieski\IDir\Http\Clients\Payment\PayPal\Express\Requests\AuthorizeRequest;
use N1ebieski\IDir\Http\Clients\Payment\PayPal\Express\Responses\CompleteResponse;
use N1ebieski\IDir\Http\Clients\Payment\PayPal\Express\Responses\PurchaseResponse;
use N1ebieski\IDir\Http\Clients\Payment\PayPal\Express\Responses\AuthorizeResponse;
use N1ebieski\IDir\Http\Clients\Payment\Interfaces\Transfer\TransferClientInterface;
use N1ebieski\IDir\Http\Clients\Payment\Interfaces\Transfer\Responses\CompleteResponseInterface;
use N1ebieski\IDir\Http\Clients\Payment\Interfaces\Transfer\Responses\PurchaseResponseInterface;
use N1ebieski\IDir\Http\Clients\Payment\Interfaces\Transfer\Responses\AuthorizeResponseInterface;

/**
 *
 * @author Mariusz Wysokiński <kontakt@intelekt.net.pl>
 */
class ExpressClient implements TransferClientInterface
{
    /**
     * Undocumented variable
     *
     * @var App
     */
    protected $app;

    /**
     *
     * @var Config
     */
    protected $config;

    /**
     * Undocumented variable
     *
     * @var PayPalListener
     */
    protected $listener;

    /**
     *
     * @param App $app
     * @param Config $config
     * @return void
     */
    public function __construct(
        App $app,
        Config $config,
        PayPalListener $listener
    ) {
        $this->app = $app;
        $this->config = $config;

        $this->listener = $listener;
    }

    /**
     *
     * @return array
     */
    protected function getDefaultParameters(): array
    {
        return [
            'username' => $this->config->get('services.paypal.paypal_express.username'),
            'password' => $this->config->get('services.paypal.paypal_express.password'),
            'signature' => $this->config->get('services.paypal.paypal_express.signature'),
            'sandbox' => $this->config->get('services.paypal.paypal_express.sandbox'),
            'currency' => $this->config->get('services.paypal.paypal_express.currency'),
            'lang' => $this->config->get('services.paypal.paypal_express.lang')
        ];
    }

    /**
     *
     * @param array $parameters
     * @return PurchaseResponseInterface
     * @throws BindingResolutionException
     */
    public function purchase(array $parameters): PurchaseResponseInterface
    {
        /**
         * @var PurchaseRequest
         */
        $request = $this->app->make(PurchaseRequest::class, [
            'parameters' => array_merge($this->getDefaultParameters(), $parameters)
        ]);

        /**
         * @var PurchaseResponse
         */
        $response = $this->app->make(PurchaseResponse::class, [
            'response' => $request->makeRequest()
        ]);

        return $response;
    }

    /**
     *
     * @param array $parameters
     * @param array $recievedParameters
     * @return CompleteResponseInterface
     * @throws BindingResolutionException
     */
    public function complete(array $parameters, array $recievedParameters): CompleteResponseInterface
    {
       /**
         * @var CompleteRequest
         */
        $request = $this->app->make(CompleteRequest::class, [
            'parameters' => array_merge($this->getDefaultParameters(), $recievedParameters, $parameters)
        ]);

        /**
         * @var CompleteResponse
         */
        $response = $this->app->make(CompleteResponse::class, [
            'response' => $request->makeRequest()
        ]);

        return $response;
    }

    /**
     *
     * @param array $parameters
     * @param array $recievedParameters
     * @return AuthorizeResponseInterface
     * @throws BindingResolutionException
     */
    public function authorize(array $parameters, array $recievedParameters): AuthorizeResponseInterface
    {
        /**
         * @var AuthorizeRequest
         */
        $request = $this->app->make(AuthorizeRequest::class, [
            'parameters' => array_merge($recievedParameters, $parameters)
        ]);

        $request->makeRequest();

        /**
         * @var AuthorizeResponse
         */
        $response = $this->app->make(AuthorizeResponse::class);

        return $response;
    }
}
