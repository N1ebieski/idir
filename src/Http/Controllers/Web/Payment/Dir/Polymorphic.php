<?php

namespace N1ebieski\IDir\Http\Controllers\Web\Payment\Dir;

use Illuminate\Http\RedirectResponse;
use N1ebieski\IDir\Loads\Web\Payment\ShowLoad;
use N1ebieski\IDir\Models\Payment\Dir\Payment;
use N1ebieski\IDir\Http\Requests\Web\Payment\Interfaces\CompleteRequestStrategy;
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
     * @param string $driver
     * @param ShowLoad $load
     * @param TransferUtilStrategy $transferUtil
     * @return RedirectResponse
     */
    public function show(
        Payment $payment,
        string $driver = null,
        ShowLoad $load,
        TransferUtilStrategy $transferUtil
    ) : RedirectResponse;

    /**
     * Undocumented function
     *
     * @param string $driver
     * @param CompleteRequestStrategy $request
     * @param TransferUtilStrategy $transferUtil
     * @return RedirectResponse
     */
    public function complete(
        string $driver = null,
        CompleteRequestStrategy $request,
        TransferUtilStrategy $transferUtil
    ) : RedirectResponse;
}
