<?php

namespace N1ebieski\IDir\Utils\Payment\Factories;

use Illuminate\Http\Response as HttpResponse;
use Illuminate\Contracts\Container\Container as App;
use N1ebieski\IDir\Utils\Payment\Interfaces\TransferUtilStrategy;

class TransferUtilFactory
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
     * Undocumented function
     *
     * @param string $driver
     * @return TransferUtilStrategy
     */
    public function makeTransferUtil(string $driver): TransferUtilStrategy
    {
        switch ($driver) {
            case 'paypal':
                return $this->app->make(\N1ebieski\IDir\Utils\Payment\PayPal\PayPalExpressAdapter::class);

            case 'cashbill':
                return $this->app->make(\N1ebieski\IDir\Utils\Payment\Cashbill\TransferUtil::class);
        }

        throw new \N1ebieski\IDir\Exceptions\Payment\DriverNotFoundException(
            "Driver {$driver} not found",
            HttpResponse::HTTP_FORBIDDEN
        );
    }
}
