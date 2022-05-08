<?php

namespace N1ebieski\IDir\Exceptions\Payment;

use Illuminate\Support\Facades\Log;
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
     * @return boolean
     */
    public function report()
    {
        if ($this->payment instanceof Payment) {
            $this->payment->makeService()->updateLogs(['logs' => $this->getMessage() . "\r\n"]);
        }
    }
}
