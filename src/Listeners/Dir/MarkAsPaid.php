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

use N1ebieski\IDir\Events\Interfaces\Payment\Dir\PaymentEventInterface;

class MarkAsPaid
{
    /**
     * Undocumented variable
     *
     * @var PaymentEventInterface
     */
    protected $event;

    /**
     *
     * @return bool
     */
    public function verify(): bool
    {
        return $this->event->payment->status->isUnfinished()
            && $this->event->payment->morph->status->isPaymentInactive();
    }

    /**
     * Handle the event.
     *
     * @param  PaymentEventInterface  $event
     * @return void
     */
    public function handle(PaymentEventInterface $event)
    {
        $this->event = $event;

        if (!$this->verify()) {
            return;
        }

        $event->payment->morph->makeService()->updateStatus(
            $event->payment->morph->group->apply_status->getValue()
        );
    }
}
