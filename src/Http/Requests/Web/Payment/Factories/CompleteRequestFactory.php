<?php

namespace N1ebieski\IDir\Http\Requests\Web\Payment\Factories;

use Illuminate\Http\Response as HttpResponse;
use N1ebieski\IDir\Http\Requests\Web\Payment\Interfaces\CompleteRequestStrategy;

class CompleteRequestFactory
{
    /**
     * Undocumented function
     *
     * @param string $driver
     * @return CompleteRequestStrategy
     */
    public function makeRequest(string $driver): CompleteRequestStrategy
    {
        switch ($driver) {
            case 'paypal':
                return new \N1ebieski\IDir\Http\Requests\Web\Payment\PayPal\CompleteRequest();

            case 'cashbill':
                return new \N1ebieski\IDir\Http\Requests\Web\Payment\Cashbill\CompleteRequest();
        }

        throw new \N1ebieski\IDir\Exceptions\Payment\DriverNotFoundException(
            "Driver {$driver} not found",
            HttpResponse::HTTP_FORBIDDEN
        );
    }
}
