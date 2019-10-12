<?php

namespace N1ebieski\IDir\Services;

use N1ebieski\ICore\Services\Serviceable;
use Illuminate\Database\Eloquent\Model;
use N1ebieski\IDir\Models\Payment\Payment;

/**
 * [PaymentService description]
 */
class PaymentService implements Serviceable
{
    /**
     * Model
     * @var Payment
     */
    private $payment;

    /**
     * [__construct description]
     * @param Payment       $payment   [description]
     */
    public function __construct(Payment $payment)
    {
        $this->payment = $payment;
    }

    /**
     * [makeStatus description]
     * @param  string|null  $payment_type  [description]
     * @return int [description]
     */
    protected function makeStatus(string $payment_type = null) : int
    {
        if ($payment_type === 'transfer') {
            return 2;
        }

        return 0;
    }

    /**
     * [create description]
     * @param  array $attributes [description]
     * @return Model             [description]
     */
    public function create(array $attributes) : Model
    {
        $this->payment->status = $this->makeStatus($attributes['payment_type'] ?? null);
        $this->payment->morph()->associate($this->payment->getMorph());
        $this->payment->price_morph()->associate($this->payment->getPriceMorph());
        $this->payment->save();

        return $this->payment;
    }

    /**
     * [update description]
     * @param  array $attributes [description]
     * @return bool              [description]
     */
    public function update(array $attributes) : bool
    {
    }

    /**
     * Update Status attribute the specified Payment in storage.
     *
     * @param  array $attributes [description]
     * @return bool              [description]
     */
    public function updateStatus(array $attributes) : bool
    {
        return $this->payment->update(['status' => $attributes['status']]);
    }

    /**
     * [delete description]
     * @return bool [description]
     */
    public function delete() : bool
    {

    }

    /**
     * [deleteGlobal description]
     * @param  array $ids [description]
     * @return int        [description]
     */
    public function deleteGlobal(array $ids) : int
    {
    }
}
