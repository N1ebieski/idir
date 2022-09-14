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

namespace N1ebieski\IDir\Http\Clients\Payment\Factories;

use Illuminate\Http\Response as HttpResponse;
use Illuminate\Contracts\Container\Container as App;
use Illuminate\Contracts\Container\BindingResolutionException;
use N1ebieski\IDir\Exceptions\Payment\DriverNotFoundException;
use N1ebieski\IDir\Http\Clients\Payment\Interfaces\Transfer\TransferClientInterface;

class TransferClientFactory
{
    /**
     * Undocumented function
     *
     * @param App $app
     */
    public function __construct(protected App $app)
    {
        //
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
        return match ($driver) {
            'paypal' => $this->app->make(\N1ebieski\IDir\Http\Clients\Payment\PayPal\Express\ExpressClient::class),
            'cashbill' => $this->app->make(\N1ebieski\IDir\Http\Clients\Payment\Cashbill\Transfer\TransferClient::class),

            default => throw new \N1ebieski\IDir\Exceptions\Payment\DriverNotFoundException(
                "Driver {$driver} not found",
                HttpResponse::HTTP_FORBIDDEN
            )
        };
    }
}
