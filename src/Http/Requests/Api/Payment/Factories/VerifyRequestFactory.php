<?php

namespace N1ebieski\IDir\Http\Requests\Api\Payment\Factories;

use Illuminate\Http\Response as HttpResponse;
use N1ebieski\IDir\Http\Requests\Api\Payment\Interfaces\VerifyRequestStrategy;

class VerifyRequestFactory
{
    /**
     * Undocumented function
     *
     * @param string $driver
     * @return VerifyRequestStrategy
     */
    public function makeRequest(string $driver): VerifyRequestStrategy
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
