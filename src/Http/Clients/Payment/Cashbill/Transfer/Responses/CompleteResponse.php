<?php

namespace N1ebieski\IDir\Http\Clients\Payment\Cashbill\Transfer\Responses;

use N1ebieski\ICore\Http\Clients\Response;
use N1ebieski\IDir\Http\Clients\Payment\Interfaces\Transfer\Responses\CompleteResponseInterface;

class CompleteResponse extends Response implements CompleteResponseInterface
{
    /**
     *
     * @return bool
     */
    public function isSuccessful(): bool
    {
        return $this->status === "ok";
    }

    /**
     * [isService description]
     * @param  string $service [description]
     * @return bool            [description]
     */
    public function isService(string $service): bool
    {
        return $this->service === $service;
    }

    /**
     *
     * @param string $amount
     * @return bool
     */
    public function isAmount(string $amount): bool
    {
        return number_format($this->amount, 2, '.', '') === $amount;
    }

    /**
     *
     * @return bool
     */
    public function isSign(string $key): bool
    {
        return md5($this->service . $this->orderid . $this->amount
            . $this->userdata . $this->status . $key) === $this->sign;
    }
}
