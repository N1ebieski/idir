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

namespace N1ebieski\IDir\Exceptions\Payment;

use N1ebieski\IDir\Models\Payment\Payment;
use N1ebieski\IDir\Exceptions\CustomException;

class Exception extends CustomException
{
    /**
     * [protected description]
     * @var Payment
     */
    protected $payment;

    /**
     *
     * @param Payment $payment
     * @return Exception
     */
    public function setPayment(Payment $payment): self
    {
        $this->payment = $payment;

        return $this;
    }

    /**
     * Report the exception.
     *
     * @return void
     */
    public function report()
    {
        if ($this->payment instanceof Payment) {
            $this->payment->makeService()->updateLogs($this->getMessage() . "\r\n");

            return;
        }
    }
}
