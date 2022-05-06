<?php

namespace N1ebieski\IDir\Http\Clients\Payment\Cashbill\Transfer;

use Illuminate\Contracts\Container\Container as App;
use Illuminate\Contracts\Config\Repository as Config;
use N1ebieski\IDir\Exceptions\Payment\Cashbill\Exception;
use Illuminate\Contracts\Container\BindingResolutionException;
use N1ebieski\IDir\Exceptions\Payment\Cashbill\Transfer\InvalidSignException;
use N1ebieski\IDir\Exceptions\Payment\Cashbill\Transfer\InvalidAmountException;
use N1ebieski\IDir\Exceptions\Payment\Cashbill\Transfer\InvalidStatusException;
use N1ebieski\IDir\Exceptions\Payment\Cashbill\Transfer\InvalidServiceException;
use N1ebieski\IDir\Http\Clients\Payment\Cashbill\Transfer\Requests\PurchaseRequest;
use N1ebieski\IDir\Http\Clients\Payment\Interfaces\Transfer\TransferClientInterface;
use N1ebieski\IDir\Http\Clients\Payment\Cashbill\Transfer\Responses\CompleteResponse;
use N1ebieski\IDir\Http\Clients\Payment\Cashbill\Transfer\Responses\PurchaseResponse;
use N1ebieski\IDir\Http\Clients\Payment\Cashbill\Transfer\Responses\AuthorizeResponse;
use N1ebieski\IDir\Http\Clients\Payment\Interfaces\Transfer\Responses\CompleteResponseInterface;
use N1ebieski\IDir\Http\Clients\Payment\Interfaces\Transfer\Responses\PurchaseResponseInterface;
use N1ebieski\IDir\Http\Clients\Payment\Interfaces\Transfer\Responses\AuthorizeResponseInterface;

class TransferClient implements TransferClientInterface
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
     *
     * @param App $app
     * @param Config $config
     * @return void
     */
    public function __construct(App $app, Config $config)
    {
        $this->app = $app;
        $this->config = $config;
    }

    /**
     *
     * @return array
     */
    protected function getDefaultParameters(): array
    {
        return [
            'service' => $this->config->get("services.cashbill.transfer.service"),
            'key' => $this->config->get("services.cashbill.transfer.key"),
            'currency' => $this->config->get("services.cashbill.transfer.currency"),
            'lang' => $this->config->get("services.cashbill.transfer.lang")
        ];
    }

    /**
     *
     * @param array $parameters
     * @return PurchaseResponseInterface
     * @throws BindingResolutionException
     * @throws Exception
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
     * @throws InvalidSignException
     */
    public function complete(array $parameters, array $recievedParameters): CompleteResponseInterface
    {
        /**
         * @var PurchaseRequest
         */
        $request = $this->app->make(PurchaseRequest::class, [
            'parameters' => array_merge($this->getDefaultParameters(), $parameters)
        ]);

        /**
         * @var CompleteResponse
         */
        $response = $this->app->make(CompleteResponse::class, [
            'parameters' => $recievedParameters
        ]);

        if (!$response->isService($request->get('service'))) {
            throw new \N1ebieski\IDir\Exceptions\Payment\Cashbill\Transfer\InvalidServiceException();
        }

        if (!$response->isAmount($request->get('amount'))) {
            throw new \N1ebieski\IDir\Exceptions\Payment\Cashbill\Transfer\InvalidAmountException();
        }

        if (!$response->isSign($this->config->get('services.cashbill.transfer.key'))) {
            throw new \N1ebieski\IDir\Exceptions\Payment\Cashbill\Transfer\InvalidSignException();
        }

        return $response;
    }

    /**
     *
     * @param array $parameters
     * @param array $recievedParameters
     * @return AuthorizeResponseInterface
     * @throws BindingResolutionException
     * @throws InvalidServiceException
     * @throws InvalidStatusException
     * @throws InvalidAmountException
     * @throws InvalidSignException
     */
    public function authorize(array $parameters, array $recievedParameters): AuthorizeResponseInterface
    {
        /**
         * @var PurchaseRequest
         */
        $request = $this->app->make(PurchaseRequest::class, [
            'parameters' => array_merge($this->getDefaultParameters(), $parameters)
        ]);

        /**
         * @var AuthorizeResponse
         */
        $response = $this->app->make(AuthorizeResponse::class, [
            'parameters' => $recievedParameters
        ]);

        if (!$response->isSuccessful()) {
            throw new \N1ebieski\IDir\Exceptions\Payment\Cashbill\Transfer\InvalidStatusException();
        }

        if (!$response->isService($request->get('service'))) {
            throw new \N1ebieski\IDir\Exceptions\Payment\Cashbill\Transfer\InvalidServiceException();
        }

        if (!$response->isAmount($request->get('amount'))) {
            throw new \N1ebieski\IDir\Exceptions\Payment\Cashbill\Transfer\InvalidAmountException();
        }

        if (!$response->isSign($this->config->get('services.cashbill.transfer.key'))) {
            throw new \N1ebieski\IDir\Exceptions\Payment\Cashbill\Transfer\InvalidSignException();
        }

        return $response;
    }
}
