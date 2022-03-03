<?php

namespace N1ebieski\IDir\Http\Controllers\Api\Payment\Dir;

use Illuminate\Http\JsonResponse;
use N1ebieski\IDir\Models\Payment\Dir\Payment;
use N1ebieski\IDir\Loads\Api\Payment\Dir\ShowLoad;
use N1ebieski\IDir\Loads\Api\Payment\Dir\VerifyLoad;
use N1ebieski\IDir\Utils\Payment\Interfaces\TransferUtilStrategy;
use N1ebieski\IDir\Http\Requests\Api\Payment\Interfaces\VerifyRequestStrategy;

interface Polymorphic
{
    /**
     * Show payment
     *
     * Initialises the payment.
     *
     * @urlParam payment string required The payment UUID.
     *
     * @param Payment $payment
     * @param string $driver
     * @param ShowLoad $load
     * @param TransferUtilStrategy $transferUtil
     * @return JsonResponse
     */
    public function show(
        Payment $payment,
        string $driver = null,
        ShowLoad $load,
        TransferUtilStrategy $transferUtil
    ): JsonResponse;

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
