<?php

namespace N1ebieski\IDir\Http\Controllers\Api\Payment\Dir;

use N1ebieski\IDir\Models\Payment\Dir\Payment;
use N1ebieski\IDir\Http\Requests\Api\Payment\Interfaces\VerifyRequestStrategy;
use N1ebieski\IDir\Utils\Payment\Interfaces\TransferUtilStrategy;

/**
 * [interface description]
 */
interface Polymorphic
{
    /**
     * Undocumented function
     *
     * @param Payment $payment
     * @param VerifyRequestStrategy $request
     * @param TransferUtilStrategy $transferUtil
     * @return string
     */
    public function verify(
        Payment $payment,
        VerifyRequestStrategy $request,
        TransferUtilStrategy $transferUtil
    ) : string;
}
