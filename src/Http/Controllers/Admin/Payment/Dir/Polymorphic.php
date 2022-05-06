<?php

namespace N1ebieski\IDir\Http\Controllers\Admin\Payment\Dir;

use N1ebieski\IDir\Models\Dir;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use N1ebieski\IDir\Models\Payment\Dir\Payment;
use N1ebieski\IDir\Loads\Admin\Payment\ShowLoad;
use N1ebieski\IDir\Http\Clients\Payment\Interfaces\Transfer\TransferClientInterface;

interface Polymorphic
{
    /**
     * Undocumented function
     *
     * @param Payment $payment
     * @param string $driver
     * @param ShowLoad $load
     * @param TransferClientInterface $client
     * @return RedirectResponse
     */
    public function show(
        Payment $payment,
        string $driver = null,
        ShowLoad $load,
        TransferClientInterface $client
    ): RedirectResponse;

    /**
     * Undocumented function
     *
     * @param Dir $dir
     * @return JsonResponse
     */
    public function showLogs(Dir $dir): JsonResponse;
}
