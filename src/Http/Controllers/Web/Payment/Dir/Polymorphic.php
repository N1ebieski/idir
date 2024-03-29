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

namespace N1ebieski\IDir\Http\Controllers\Web\Payment\Dir;

use Illuminate\Http\RedirectResponse;
use N1ebieski\IDir\Models\Payment\Dir\Payment;
use N1ebieski\IDir\Loads\Web\Payment\Dir\ShowLoad;
use N1ebieski\IDir\Loads\Web\Payment\Dir\CompleteLoad;
use N1ebieski\IDir\Http\Requests\Web\Payment\Interfaces\CompleteRequestInterface;
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
     *
     * @param CompleteRequestInterface $request
     * @param CompleteLoad $load
     * @param TransferClientInterface $client
     * @param string|null $driver
     * @return RedirectResponse
     */
    public function complete(
        CompleteRequestInterface $request,
        CompleteLoad $load,
        TransferClientInterface $client,
        string $driver = null
    ): RedirectResponse;
}
