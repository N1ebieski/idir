<?php

namespace N1ebieski\IDir\Listeners\Dir;

class Checkout
{
    /**
     * Undocumented variable
     *
     * @var object
     */
    protected $event;

    /**
     *
     * @return bool
     */
    public function verify(): bool
    {
        return $this->event->dir->isActive();
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event): void
    {
        $this->event = $event;

        if (!$this->verify()) {
            return;
        }

        $event->dir->loadCheckoutPayments();

        $event->dir->payments->each(function ($payment) use ($event) {
            if (optional($payment->order)->group_id === $event->dir->group_id) {
                $event->dir->makeService()->updatePrivileged(['days' => $payment->order->days]);
                $payment->makeRepo()->finished();
            }
        });
    }
}
