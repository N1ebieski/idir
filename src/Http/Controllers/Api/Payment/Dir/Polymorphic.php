<?php

namespace N1ebieski\IDir\Http\Controllers\Api\Payment\Dir;

use N1ebieski\IDir\Loads\Api\Payment\Dir\VerifyLoad;
use N1ebieski\IDir\Utils\Payment\Interfaces\TransferUtilStrategy;
use N1ebieski\IDir\Http\Requests\Api\Payment\Interfaces\VerifyRequestStrategy;

interface Polymorphic
{
    /**
     * Undocumented function
     *
     * @param string $driver
     * @param VerifyRequestStrategy $request
     * @param VerifyLoad $load
     * @param TransferUtilStrategy $transferUtil
     * @return string
     */
    public function verify(
        string $driver = null,
        VerifyRequestStrategy $request,
        VerifyLoad $load,
        TransferUtilStrategy $transferUtil
    ): string;
}
