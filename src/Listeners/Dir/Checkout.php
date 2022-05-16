<?php

namespace N1ebieski\IDir\Listeners\Dir;

use N1ebieski\IDir\Models\Payment\Dir\Payment;
use N1ebieski\IDir\Events\Interfaces\Dir\DirEventInterface;

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
        return $this->event->dir->status->isActive();
    }

    /**
     * Handle the event.
     *
     * @param  DirEventInterface  $event
     * @return void
     */
    public function handle(DirEventInterface $event): void
    {
        $this->event = $event;

        if (!$this->verify()) {
            return;
        }

        $event->dir->loadCheckoutPayments();

        $event->dir->payments->each(function (Payment $payment) use ($event) {
            if (optional($payment->order)->group_id === $event->dir->group_id) {
                $event->dir->makeService()->updatePrivileged($payment->order->days);
                $payment->makeRepo()->finished();
            }
        });
    }
}
