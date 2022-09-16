<?php

/**
 * NOTICE OF LICENSE
 *
 * This source file is licenced under the Software License Agreement
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://intelekt.net.pl/pages/regulamin
 *
 * With the purchase or the installation of the software in your application
 * you accept the licence agreement.
 *
 * @author    Mariusz Wysokiński <kontakt@intelekt.net.pl>
 * @copyright Since 2019 INTELEKT - Usługi Komputerowe Mariusz Wysokiński
 * @license   https://intelekt.net.pl/pages/regulamin
 */

namespace N1ebieski\IDir\Http\Controllers\Admin\Payment\Dir;

use N1ebieski\IDir\Models\Dir;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use N1ebieski\IDir\Models\Payment\Dir\Payment;
use N1ebieski\IDir\Loads\Admin\Payment\Dir\ShowLoad;
use N1ebieski\IDir\Http\Clients\Payment\Interfaces\Transfer\TransferClientInterface;

interface Polymorphic
{
    /**
     *
     * @param Payment $payment
     * @param ShowLoad $load
     * @param TransferClientInterface $client
     * @param string|null $driver
     * @return RedirectResponse
     */
    public function show(
        Payment $payment,
        ShowLoad $load,
        TransferClientInterface $client,
        string $driver = null
    ): RedirectResponse;

    /**
     * Undocumented function
     *
     * @param Dir $dir
     * @return JsonResponse
     */
    public function showLogs(Dir $dir): JsonResponse;
}
