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

namespace N1ebieski\IDir\Repositories\Payment;

use N1ebieski\IDir\Models\Payment\Payment;

class PaymentRepo
{
    /**
     * [__construct description]
     * @param Payment $payment [description]
     */
    public function __construct(protected Payment $payment)
    {
        //
    }

    /**
     * [firstPendingById description]
     * @param  string    $uuid [description]
     * @return Payment|null     [description]
     */
    public function firstByUuid(string $uuid): ?Payment
    {
        return $this->payment->newQuery()
            ->where('uuid', $uuid)
            ->poliType()
            ->with(['morph', 'orderMorph'])
            ->first();
    }

    /**
     * [firstPendingById description]
     * @param  string    $uuid [description]
     * @return Payment|null     [description]
     */
    public function firstPendingByUuid(string $uuid): ?Payment
    {
        return $this->payment->newQuery()
            ->where('uuid', $uuid)
            ->pending()
            ->poliType()
            ->with(['morph', 'orderMorph'])
            ->first();
    }
}
