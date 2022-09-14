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

namespace N1ebieski\IDir\Http\Requests\Web\Payment\Factories;

use Illuminate\Http\Response as HttpResponse;
use N1ebieski\IDir\Exceptions\Payment\DriverNotFoundException;
use N1ebieski\IDir\Http\Requests\Web\Payment\Interfaces\CompleteRequestInterface;

class CompleteRequestFactory
{
    /**
     *
     * @param string $driver
     * @return CompleteRequestInterface
     * @throws DriverNotFoundException
     */
    public function makeRequest(string $driver): CompleteRequestInterface
    {
        return match ($driver) {
            'paypal' => new \N1ebieski\IDir\Http\Requests\Web\Payment\PayPal\CompleteRequest(),
            'cashbill' => new \N1ebieski\IDir\Http\Requests\Web\Payment\Cashbill\CompleteRequest(),

            default => throw new \N1ebieski\IDir\Exceptions\Payment\DriverNotFoundException(
                "Driver {$driver} not found",
                HttpResponse::HTTP_FORBIDDEN
            )
        };
    }
}
