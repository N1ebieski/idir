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

namespace N1ebieski\IDir\Listeners\Payment;

use Illuminate\Http\Request;
use N1ebieski\IDir\Events\Interfaces\Payment\Dir\PaymentEventInterface;

class CreateLogs
{
    /**
     * Create the event listener.
     *
     * @param  Request  $request  [description]
     * @return void
     */
    public function __construct(protected Request $request)
    {
        //
    }

    /**
     *
     * @return bool
     */
    public function verify(): bool
    {
        return $this->request->has('logs');
    }

    /**
     * Handle the event.
     *
     * @param  PaymentEventInterface  $event
     * @return void
     */
    public function handle(PaymentEventInterface $event)
    {
        if (!$this->verify()) {
            return;
        }

        $logs = "";

        foreach (array_map('strval', $this->request->input('logs')) as $key => $value) {
            $logs .= $key . ': ' . $value . "\n";
        }

        $event->payment->makeService()->updateLogs($logs);
    }
}
