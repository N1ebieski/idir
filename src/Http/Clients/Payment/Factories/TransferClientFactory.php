<?php

namespace N1ebieski\IDir\Http\Clients\Payment\Factories;

use Illuminate\Http\Response as HttpResponse;
use Illuminate\Contracts\Container\Container as App;
use Illuminate\Contracts\Container\BindingResolutionException;
use N1ebieski\IDir\Exceptions\Payment\DriverNotFoundException;
use N1ebieski\IDir\Http\Clients\Payment\Interfaces\Transfer\TransferClientInterface;

class TransferClientFactory
{
    /**
     * Undocumented variable
     *
     * @var App
     */
    protected $app;

    /**
     * Undocumented function
     *
     * @param App $app
     */
    public function __construct(App $app)
    {
        $this->app = $app;
    }

    /**
     *
     * @param string $driver
     * @return TransferClientInterface
     * @throws BindingResolutionException
     * @throws DriverNotFoundException
     */
    public function makeClient(string $driver): TransferClientInterface
    {
        switch ($driver) {
            case 'paypal':
                return $this->app->make(\N1ebieski\IDir\Http\Clients\Payment\PayPal\Express\ExpressClient::class);

            case 'cashbill':
                return $this->app->make(\N1ebieski\IDir\Http\Clients\Payment\Cashbill\transfer\TransferClient::class);
        }

        throw new \N1ebieski\IDir\Exceptions\Payment\DriverNotFoundException(
            "Driver {$driver} not found",
            HttpResponse::HTTP_FORBIDDEN
        );
    }
}
