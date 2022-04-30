<?php

namespace N1ebieski\IDir\Repositories;

use N1ebieski\IDir\Models\Payment\Payment;
use N1ebieski\IDir\ValueObjects\Payment\Status;

class PaymentRepo
{
    /**
     * [private description]
     * @var Payment
     */
    protected $payment;

    /**
     * [__construct description]
     * @param Payment $payment [description]
     */
    public function __construct(Payment $payment)
    {
        $this->payment = $payment;
    }

    /**
     * [firstPendingById description]
     * @param  string    $uuid [description]
     * @return Payment|null     [description]
     */
    public function firstByUuid(string $uuid): ?Payment
    {
        return $this->payment->where('uuid', $uuid)
            ->poliType()
            ->with(['morph', 'orderMorph'])
            ->first();
    }

    /**
     * [firstPendingById description]
     * @param  string    $uuid [description]
     * @return Payment|null     [description]
     */
    public function firstPendingByUuid(string $uuid): ?Payment
    {
        return $this->payment->where('uuid', $uuid)
            ->pending()
            ->poliType()
            ->with(['morph', 'orderMorph'])
            ->first();
    }

    /**
     * [completed description]
     * @return bool [description]
     */
    public function finished(): bool
    {
        return $this->payment->update(['status' => Status::FINISHED]);
    }

    /**
     * [paid description]
     * @return bool [description]
     */
    public function paid(): bool
    {
        return $this->payment->update(['status' => Status::UNFINISHED]);
    }
}
