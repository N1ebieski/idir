<?php

namespace N1ebieski\IDir\Http\Controllers\Api\Payment\Dir;

use Illuminate\Http\JsonResponse;
use N1ebieski\IDir\Models\Payment\Dir\Payment;
use N1ebieski\IDir\Loads\Api\Payment\Dir\ShowLoad;
use N1ebieski\IDir\Loads\Api\Payment\Dir\VerifyLoad;
use N1ebieski\IDir\Http\Requests\Api\Payment\Interfaces\VerifyRequestInterface;
use N1ebieski\IDir\Http\Clients\Payment\Interfaces\Transfer\TransferClientInterface;

interface Polymorphic
{
    /**
     * Show payment
     *
     * @param Payment $payment
     * @param string $driver
     * @param ShowLoad $load
     * @param TransferClientInterface $client
     * @return JsonResponse
     */
    public function show(
        Payment $payment,
        string $driver = null,
        ShowLoad $load,
        TransferClientInterface $client
    ): JsonResponse;

    /**
     * Undocumented function
     *
     * @param string $driver
     * @param VerifyRequestInterface $request
     * @param VerifyLoad $load
     * @param TransferClientInterface $client
     * @return string
     */
    public function verify(
        string $driver = null,
        VerifyRequestInterface $request,
        VerifyLoad $load,
        TransferClientInterface $client
    ): string;
}
