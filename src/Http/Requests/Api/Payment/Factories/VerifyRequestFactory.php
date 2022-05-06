<?php

namespace N1ebieski\IDir\Http\Requests\Api\Payment\Factories;

use Illuminate\Http\Response as HttpResponse;
use N1ebieski\IDir\Http\Requests\Api\Payment\Interfaces\VerifyRequestInterface;

class VerifyRequestFactory
{
    /**
     * Undocumented function
     *
     * @param string $driver
     * @return VerifyRequestInterface
     */
    public function makeRequest(string $driver): VerifyRequestInterface
    {
        switch ($driver) {
            case 'paypal':
                return new \N1ebieski\IDir\Http\Requests\Api\Payment\PayPal\VerifyRequest();

            case 'cashbill':
                return new \N1ebieski\IDir\Http\Requests\Api\Payment\Cashbill\VerifyRequest();
        }

        throw new \N1ebieski\IDir\Exceptions\Payment\DriverNotFoundException(
            "Driver {$driver} not found",
            HttpResponse::HTTP_FORBIDDEN
        );
    }
}
