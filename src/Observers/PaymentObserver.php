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

namespace N1ebieski\IDir\Observers;

use Ramsey\Uuid\Uuid;
use N1ebieski\IDir\Models\Payment\Payment;

class PaymentObserver
{
    /**
     * Handle the post "creating" event.
     *
     * @param  Payment  $payment
     * @return void
     */
    public function creating(Payment $payment)
    {
        $payment->uuid = (string)Uuid::uuid4();
    }

    /**
     * Handle the post "created" event.
     *
     * @param  Payment  $payment
     * @return void
     */
    public function created(Payment $payment)
    {
        //
    }

    /**
     * Handle the post "updated" event.
     *
     * @param  Payment  $payment
     * @return void
     */
    public function updated(Payment $payment)
    {
        //
    }

    /**
     * Handle the post "deleted" event.
     *
     * @param  Payment  $payment
     * @return void
     */
    public function deleted(Payment $payment)
    {
        //
    }

    /**
     * Handle the post "restored" event.
     *
     * @param  Payment  $payment
     * @return void
     */
    public function restored(Payment $payment)
    {
        //
    }

    /**
     * Handle the post "force deleted" event.
     *
     * @param  Payment  $payment
     * @return void
     */
    public function forceDeleted(Payment $payment)
    {
        //
    }
}
