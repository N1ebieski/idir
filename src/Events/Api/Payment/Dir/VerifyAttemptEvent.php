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

namespace N1ebieski\IDir\Events\Api\Payment\Dir;

use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use N1ebieski\IDir\Models\Payment\Dir\Payment;
use Illuminate\Broadcasting\InteractsWithSockets;
use N1ebieski\IDir\Events\Interfaces\Payment\Dir\PaymentEventInterface;

class VerifyAttemptEvent implements PaymentEventInterface
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @param Payment         $payment    [description]
     * @return void
     */
    public function __construct(public Payment $payment)
    {
        //
    }
}
