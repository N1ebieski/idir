<?php

namespace N1ebieski\IDir\Http\Controllers\Web\Payment\Dir;

use Illuminate\Http\RedirectResponse;
use N1ebieski\IDir\Loads\Web\Payment\ShowLoad;
use N1ebieski\IDir\Models\Payment\Dir\Payment;
use N1ebieski\IDir\Http\Requests\Web\Payment\Interfaces\CompleteRequestStrategy;
use N1ebieski\IDir\Http\Requests\Web\Payment\Interfaces\ShowRequestStrategy;
use N1ebieski\IDir\Http\Requests\Web\Payment\Interfaces\VerifyRequestStrategy;
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
     * @param ShowLoad $load
     * @param ShowRequestStrategy $request
     * @param TransferUtilStrategy $transferUtil
     * @return RedirectResponse
     */
    public function show(
        Payment $payment,
        ShowLoad $load,
        ShowRequestStrategy $request,
        TransferUtilStrategy $transferUtil
    ) : RedirectResponse;

    /**
     * Undocumented function
     *
     * @param CompleteRequestStrategy $request
     * @param TransferUtilStrategy $transferUtil
     * @return RedirectResponse
     */
    public function complete(
        CompleteRequestStrategy $request,
        TransferUtilStrategy $transferUtil
    ) : RedirectResponse;

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
