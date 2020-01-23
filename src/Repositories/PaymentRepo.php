<?php

namespace N1ebieski\IDir\Repositories;

use Illuminate\Database\Eloquent\Collection;
use N1ebieski\IDir\Models\Payment\Payment;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * [PaymentRepo description]
 */
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
    public function firstPendingByUuid(string $uuid) : ?Payment
    {
        return $this->payment->where('uuid', $uuid)
            ->pending()
            ->poliType()
            ->with(['morph', 'price_morph'])
            ->first();
    }

    /**
     * [completed description]
     * @return bool [description]
     */
    public function completed() : bool
    {
        return $this->payment->update(['status' => 1]);
    }

    /**
     * [paid description]
     * @return bool [description]
     */
    public function paid() : bool
    {
        return $this->payment->update(['status' => 0]);
    }
}
