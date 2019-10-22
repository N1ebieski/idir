<?php

namespace N1ebieski\IDir\Listeners;

/**
 * [CheckoutDir description]
 */
class CheckoutDir
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event) : void
    {
        if ($event->dir->status === 1) {
            $event->dir->loadCheckoutPayments();

            $event->dir->payments->each(function($payment) use ($event) {
                if ($payment->price->group_id === $event->dir->group_id) {
                    $event->dir->getService()->updatePrivileged(['days' => $payment->price->days]);
                    $payment->getService()->updateStatus(['status' => 1]);

                    return false;
                }
            });
        }
    }
}
