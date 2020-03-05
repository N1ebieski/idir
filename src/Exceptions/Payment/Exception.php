<?php

namespace N1ebieski\IDir\Exceptions\Payment;

use N1ebieski\IDir\Exceptions\CustomException;
use N1ebieski\IDir\Models\Payment\Payment;

class Exception extends CustomException
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
