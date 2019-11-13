<?php

namespace N1ebieski\IDir\Exceptions\Cashbill;

use N1ebieski\IDir\Exceptions\Custom;
use N1ebieski\IDir\Models\Payment\Payment;

class Exception extends Custom
{
    /**
     * [protected description]
     * @var Payment
     */
    protected $payment;

    /**
     * [setPayment description]
     * @param Payment $payment [description]
     */
    public function setPayment(Payment $payment)
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
            $this->payment->makeService()->updateLogs(['logs' => $this->getMessage() . "\r\n"]);
        }
    }
}
