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

namespace N1ebieski\IDir\Listeners\Dir;

use N1ebieski\IDir\Models\Payment\Dir\Payment;
use N1ebieski\IDir\Events\Interfaces\Dir\DirEventInterface;

class Checkout
{
    /**
     * Undocumented variable
     *
     * @var DirEventInterface
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
            // @phpstan-ignore-next-line
            if ($payment->order?->group_id === $event->dir->group_id) {
                $event->dir->makeService()->updatePrivileged($payment->order->days);
                $payment->makeService()->finished();
            }
        });
    }
}
