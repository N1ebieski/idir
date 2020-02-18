<?php

namespace N1ebieski\IDir\Listeners\Dir;

/**
 * [Checkout description]
 */
class Checkout
{
    /**
     * Undocumented variable
     *
     * @var object
     */
    protected object $event;

    /**
     *
     * @return bool
     */
    public function verify() : bool
    {
        return $this->event->dir->isActive();
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event) : void
    {
        $this->event = $event;

        if (!$this->verify()) {
            return;
        }

        $event->dir->loadCheckoutPayments();

        $event->dir->payments->each(function ($payment) use ($event) {
            if (optional($payment->price)->group_id === $event->dir->group_id) {
                $event->dir->makeService()->updatePrivileged(['days' => $payment->price->days]);
                $payment->makeRepo()->completed();
            }
        });
    }
}
