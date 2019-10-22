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
    private $payment;

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
     * @param  int    $id [description]
     * @return Payment|null     [description]
     */
    public function firstPendingById(int $id) : ?Payment
    {
        return $this->payment->where('id', $id)
            ->pending()
            ->with(['morph', 'price_morph'])
            ->first();
    }

    /**
     * [firstById description]
     * @param  int    $id [description]
     * @return Payment|null     [description]
     */
    public function firstById(int $id) : ?Payment
    {
        return $this->payment->where('id', $id)
            ->with(['morph', 'price_morph'])
            ->first();
    }    
}
